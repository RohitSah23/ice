<?php
require 'config/database.php';

// Handle Add/Update/Remove
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $pid = $_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if ($action == 'add') {
        $qty = (int)$_POST['quantity'];
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid] += $qty;
        } else {
            $_SESSION['cart'][$pid] = $qty;
        }
    } 
    elseif ($action == 'update') {
        $qty = (int)$_POST['quantity'];
        if ($qty > 0) {
            $_SESSION['cart'][$pid] = $qty;
        } else {
            unset($_SESSION['cart'][$pid]);
        }
    } 
    elseif ($action == 'remove') {
        unset($_SESSION['cart'][$pid]);
    }

    // Redirect back to avoid resubmission
    header("Location: cart.php");
    exit;
}

// Fetch Cart Details
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Check if ids is not empty string (it shouldn't be due to !empty check but good practice)
    if ($ids) {
        $stmt = $pdo->query("SELECT * FROM products WHERE id IN ($ids)");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $subtotal = $p['price'] * $qty;
            $total_price += $subtotal;
            
            $p['qty'] = $qty;
            $p['subtotal'] = $subtotal;
            $cart_items[] = $p;
        }
    }
}
// Calculate Cart Count for Navbar
$menu_cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $c_qty) {
        $menu_cart_count += $c_qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart - Ice Cream Parlour</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-ice-cream me-2"></i>Scoops & Smiles</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                    <!-- Cart In Navbar -->
                    <li class="nav-item me-3">
                        <a href="cart.php" class="nav-link position-relative">
                            <i class="fas fa-shopping-basket fa-lg"></i>
                            <?php if($menu_cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $menu_cart_count; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item me-3">
                            <span class="nav-link text-dark fw-medium">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </li>
                        <?php if($_SESSION['role'] === 'admin'): ?>
                             <li class="nav-item"><a class="nav-link" href="admin/index.php">Dashboard</a></li>
                        <?php else: ?>
                             <li class="nav-item"><a class="nav-link" href="my_orders.php">My Orders</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="btn btn-outline-danger btn-sm ms-2" href="auth/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php">Login</a></li>
                        <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="auth/signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div style="margin-top: 80px;"></div>

    <div class="container my-5">
        <h2 class="mb-4 fw-bold">Shopping Cart 🛒</h2>

        <?php if (!empty($cart_items)): ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="table-glass">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </td>
                                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form action="cart.php" method="POST" class="d-flex" style="width: 140px;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            
                                            <div class="quantity-selector">
                                                <button class="quantity-btn" type="submit" name="quantity" value="<?php echo $item['qty'] - 1; ?>">-</button>
                                                <input type="text" class="form-control quantity-input" value="<?php echo $item['qty']; ?>" readonly>
                                                <button class="quantity-btn" type="submit" name="quantity" value="<?php echo $item['qty'] + 1; ?>">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="fw-bold">₹<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <form action="cart.php" method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-link text-danger p-0">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4 mt-4 mt-lg-0">
                    <div class="card p-4">
                        <h5 class="fw-bold mb-3">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>₹<?php echo number_format($total_price, 2); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-4 text-muted">
                            <small>Tax (Included)</small>
                            <small>₹0.00</small>
                        </div>
                        <div class="d-flex justify-content-between mb-4 border-top pt-3">
                            <span class="fw-bold h5">Total</span>
                            <span class="fw-bold h5 text-primary">₹<?php echo number_format($total_price, 2); ?></span>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <form action="checkout.php" method="POST">
                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm">
                                    Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="auth/login.php" class="btn btn-dark w-100 py-2 rounded-pill">Login to Checkout</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3 text-muted display-1"><i class="fas fa-shopping-basket"></i></div>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Looks like you haven't added any sweets yet.</p>
                <a href="index.php" class="btn btn-primary mt-3 rounded-pill px-4">Browse Menu</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted border-top bg-white mt-auto">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> Scoops & Smiles. All rights reserved.</small>
        </div>
    </footer>
</body>
</html>
