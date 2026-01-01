<?php
require '../config/database.php';

// Auth Check for Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Product Deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

// Handle Product Add/Edit (Simplified)
// In a fuller implementation, this would be a modal or separate page. 
// For now, let's list orders and items.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-ice-cream me-2"></i>Scoops & Smiles <span class="badge bg-primary rounded-pill small ms-2" style="font-size: 0.7rem; vertical-align: middle;">Admin</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <span class="nav-link text-dark fw-medium">Hello, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="../index.php">View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container" style="margin-top: 100px;">
        
        <!-- Mobile/Tablet Tab Navigation -->
        <ul class="nav nav-pills mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill fw-bold px-4" id="flavors-tab" data-bs-toggle="pill" data-bs-target="#flavors" type="button" role="tab">
                    <i class="fas fa-ice-cream me-2"></i>Manage Flavors
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill fw-bold px-4 ms-2" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                    <i class="fas fa-shopping-bag me-2"></i>Recent Orders
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill fw-bold px-4 ms-2" id="users-tab" data-bs-toggle="pill" data-bs-target="#users" type="button" role="tab">
                    <i class="fas fa-users me-2"></i>Users
                </button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">
            
            <!-- Flavors Tab -->
            <div class="tab-pane fade show active" id="flavors" role="tabpanel">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold m-0 text-dark">Flavor Inventory</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fas fa-plus me-1"></i> Add New
                        </button>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="table-responsive table-glass rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3" style="width: 40%;">Name</th>
                                        <th style="width: 20%;">Price</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
                                    while ($row = $stmt->fetch()):
                                    ?>
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($row['image']) && file_exists('../'.$row['image'])): ?>
                                                    <img src="../<?php echo $row['image']; ?>" class="rounded-circle me-3 object-fit-cover shadow-sm" width="50" height="50">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-ice-cream text-warning fa-lg"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <span class="fw-bold d-block text-dark"><?php echo htmlspecialchars($row['name']); ?></span>
                                                    <small class="text-muted d-none d-md-block text-truncate" style="max-width: 250px;"><?php echo htmlspecialchars($row['description']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-5">₹<?php echo $row['price']; ?></span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="edit_item.php?id=<?php echo $row['id']; ?>" class="btn btn-light text-primary me-2 rounded-circle shadow-sm hover-scale" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-light text-danger rounded-circle shadow-sm hover-scale" onclick="return confirm('Delete this item?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Tab -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                 <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold m-0 text-dark">Order History</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="table-responsive table-glass rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">#ID</th>
                                        <th>User Details</th>
                                        <th>Total</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 50");
                                    while ($order = $stmt->fetch()):
                                    ?>
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-light text-dark border rounded-pill px-3 py-2">#<?php echo $order['id']; ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($order['username']); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($order['email']); ?></div>
                                            <div class="small text-muted mt-1"><i class="far fa-clock me-1"></i><?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?></div>
                                        </td>
                                        <td class="fw-bold text-primary fs-5">₹<?php echo $order['total_amount']; ?></td>
                                        <td class="text-end pe-3">
                                            <a href="../invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-4" target="_blank">
                                                View Invoice
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                 <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="fw-bold m-0 text-dark">Registered Users</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="table-responsive table-glass rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">#ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th class="text-end pe-3">Joined</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
                                    while ($u = $stmt->fetch()):
                                    ?>
                                    <tr>
                                        <td class="ps-3"><span class="badge bg-light text-dark border rounded-pill px-3 py-2">#<?php echo $u['id']; ?></span></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($u['username']); ?></div>
                                        </td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <?php if($u['role'] === 'admin'): ?>
                                                <span class="badge bg-primary rounded-pill">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-3 text-muted">
                                           <?php echo isset($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : 'N/A'; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <!-- Add Item Modal -->
    <div class="modal" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Flavor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="add_item.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Item</button>
                    </div>
                </form>
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
</body>
</html>
