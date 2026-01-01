<?php
require 'config/database.php';

// Fetch Products
$stmt = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cart Item Count
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
    <title>Ice Cream Parlour</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
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

    <!-- Modern Hero Header -->
    <header class="hero-modern" id="home">
        <!-- Floating Background Shapes -->
        <div class="hero-shape shape-1"></div>
        <div class="hero-shape shape-2"></div>

        <div class="container">
            <div class="row align-items-center">
                <!-- Text Content -->
                <div class="col-lg-6 hero-content">
                    <span class="hero-badge"><i class="fas fa-award me-2"></i>#1 Ice Cream in Town</span>
                    <h1 class="hero-title">Scoops of <br>Pure <span style="color: #ff7675;">Happiness</span></h1>
                    <p class="hero-text">Experience the magic of handcrafted flavors made from the finest ingredients. Sweeten your day with a scoop of joy.</p>
                    <div class="d-flex gap-3">
                        <a href="#menu" class="btn btn-primary rounded-pill px-5 py-3 shadow-lg fw-bold">Order Now <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="#menu" class="btn btn-white bg-white text-dark rounded-pill px-5 py-3 shadow-sm fw-bold border">View Menu</a>
                    </div>
                </div>
                
                <!-- Hero Image -->
                <div class="col-lg-6 d-none d-lg-block text-center hero-img-container">
                    <img src="assets/img/hero.jpg" alt="Delicious Ice Cream" class="hero-main-img">
                </div>
            </div>
        </div>
    </header>

    <!-- Menu Section -->
    <div class="container mb-5" id="menu">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h6 class="text-primary text-uppercase fw-bold ls-1">Our Menu</h6>
                <h2 class="fw-bold">Popular Flavors</h2>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach($products as $p): ?>
            <div class="col-md-3 col-sm-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="price-badge">₹<?php echo number_format($p['price'], 2); ?></div>
                    <!-- Link Wrapper -->
                    <a href="product_details.php?id=<?php echo $p['id']; ?>" class="text-decoration-none text-dark">
                        <?php if(!empty($p['image']) && $p['image'] != 'default_icecream.png' && file_exists($p['image'])): ?>
                             <div class="card-img-top p-0">
                                <img src="<?php echo $p['image']; ?>" class="w-100 h-100 object-fit-cover" alt="<?php echo htmlspecialchars($p['name']); ?>">
                             </div>
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-4">
                                <i class="fas fa-ice-cream text-warning fa-3x"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($p['name']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($p['description']); ?></p>
                        </div>
                    </a>
                    
                    <div class="card-footer bg-white border-0 pb-3 ps-3 pe-3">
                         <form action="cart.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="quantity-selector">
                                    <button type="button" class="quantity-btn" onclick="updateQty(this, -1)">-</button>
                                    <input type="number" name="quantity" value="1" min="1" max="10" class="form-control quantity-input" readonly>
                                    <button type="button" class="quantity-btn" onclick="updateQty(this, 1)">+</button>
                                </div>
                                <button type="submit" class="btn btn-dark flex-grow-1 btn-sm rounded-pill py-2 fw-medium shadow-sm">
                                    <i class="fas fa-shopping-basket me-2"></i>Add
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 text-muted border-top bg-white">
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> Scoops & Smiles. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQty(btn, change) {
            const input = btn.closest('.input-group').querySelector('input');
            let val = parseInt(input.value) + change;
            if (val >= 1 && val <= 10) {
                input.value = val;
            }
        }
    </script>
</body>
</html>
