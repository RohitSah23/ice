<?php
require 'config/database.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Process Order
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();

        $user_id = $_SESSION['user_id'];
        $total = 0;
        $items_to_insert = [];

        // 1. Calculate Total & Prepare Items
        $ids = implode(',', array_keys($_SESSION['cart']));
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $price = $p['price'];
            $total += $price * $qty;
            
            $items_to_insert[] = [
                'pid' => $p['id'],
                'qty' => $qty,
                'price' => $price
            ];
        }

        // 2. Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // 3. Insert Order Items
        $insert_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items_to_insert as $item) {
            $insert_item->execute([$order_id, $item['pid'], $item['qty'], $item['price']]);
        }

        $pdo->commit();
        
        // Clear Cart
        unset($_SESSION['cart']);
        
        // Redirect to Invoice
        header("Location: invoice.php?order_id=" . $order_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Order Failed: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
</head>
<body>
    <form id="checkoutForm" method="POST"></form>
    <script>document.getElementById('checkoutForm').submit();</script>
</body>
</html>
