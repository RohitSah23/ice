<?php
require '../config/database.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    // Handle Image Update
    $imageSQL = "";
    $params = [$name, $desc, $price];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/img/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $uniqueName = uniqid() . '.' . $fileExt;
        $targetFile = $uploadDir . $uniqueName;
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExt, $allowed)) {
             if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                 $imageSQL = ", image = ?";
                 $params[] = 'assets/img/' . $uniqueName;
             }
        }
    }
    
    $params[] = $id;

    $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ? $imageSQL WHERE id = ?");
    if ($stmt->execute($params)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Failed to update item.";
    }
}

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    die("Item not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Item - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
        <h4 class="mb-4">Edit Flavor 🍦</h4>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Price (₹)</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $item['price']; ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Image</label>
                <?php if(!empty($item['image']) && $item['image'] != 'default_icecream.png'): ?>
                    <div class="mb-2">
                        <img src="../<?php echo $item['image']; ?>" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Leave empty to keep current image</small>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </form>
    </div>

</body>
</html>
