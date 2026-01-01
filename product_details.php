<?php
require 'config/database.php';

// Get Product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Redirect if not found
if (!$product) {
    header("Location: index.php");
    exit;
}

// Cart Count for Navbar
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Ice Cream Parlour</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top glass-header" style="height: auto;">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-ice-cream me-2"></i>Scoops & Smiles</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    
                     <!-- Cart in Navbar -->
                     <li class="nav-item me-3">
                        <a href="cart.php" class="nav-link position-relative">
                            <i class="fas fa-shopping-basket fa-lg"></i>
                            <?php if($cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo $cart_count; ?>
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

    <!-- Product Details Section -->
    <div class="container" style="margin-top: 100px; margin-bottom: 50px;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="row g-0">
                <div class="col-md-6 bg-white d-flex align-items-center justify-content-center p-0">
                    <?php if(!empty($product['image']) && $product['image'] != 'default_icecream.png' && file_exists($product['image'])): ?>
                        <img src="<?php echo $product['image']; ?>" class="w-100 h-100 object-fit-cover" style="max-height: 500px;" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div class="p-5 text-center w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-ice-cream text-warning" style="font-size: 10rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="card-body p-5 d-flex flex-column h-100 justify-content-center">
                        <h6 class="text-uppercase text-primary fw-bold mb-2">Premium Flavor</h6>
                        <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <h2 class="text-success fw-bold mb-4">₹<?php echo number_format($product['price'], 2); ?></h2>
                        
                        <p class="lead text-muted mb-4">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>

                        <form action="cart.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="d-flex gap-3 align-items-center mb-4">
                                <div class="quantity-selector" style="max-width: 140px; height: 50px;">
                                    <button type="button" class="quantity-btn fs-5 px-3" onclick="updateQty(this, -1)">-</button>
                                    <input type="number" name="quantity" value="1" min="1" max="10" class="form-control quantity-input fs-5" readonly>
                                    <button type="button" class="quantity-btn fs-5 px-3" onclick="updateQty(this, 1)">+</button>
                                </div>
                                <span class="text-muted fw-medium">Servings</span>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-dark btn-lg px-5 rounded-pill shadow w-100">
                                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted border-top bg-white mt-auto">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> Scoops & Smiles. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQty(btn, change) {
            const input = btn.closest('.quantity-selector').querySelector('input');
            let val = parseInt(input.value) + change;
            if (val >= 1 && val <= 10) {
                input.value = val;
            }
        }
    </script>
</body>
</html>
