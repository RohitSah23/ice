<?php
require 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    
    <nav class="navbar navbar-light bg-white border-bottom mb-5">
        <div class="container">
            <a class="navbar-brand" href="index.php">Back to Menu</a>
        </div>
    </nav>

    <div class="container">
        <h2 class="mb-4">My Orders 🥡</h2>
        
        <?php if(count($orders) > 0): ?>
            <div class="table-responsive glass-panel">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $o): ?>
                        <tr>
                            <td>#<?php echo $o['id']; ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($o['order_date'])); ?></td>
                            <td class="fw-bold">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                            <td><span class="badge bg-success">Completed</span></td>
                            <td>
                                <a href="invoice.php?order_id=<?php echo $o['id']; ?>" class="btn btn-sm btn-outline-primary">View Bill</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">You haven't placed any orders yet.</div>
        <?php endif; ?>
    </div>
    <!-- Footer -->
    <footer class="text-center py-4 text-muted border-top bg-white mt-5">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> Scoops & Smiles. All rights reserved.</small>
        </div>
    </footer>
</body>
</html>
