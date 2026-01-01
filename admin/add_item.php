<?php
require '../config/database.php';

// Auth Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    // Handle Image Upload
    $imagePath = 'default_icecream.png'; // Default if no image uploaded
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
                 $imagePath = 'assets/img/' . $uniqueName;
             }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $imagePath]);

    header("Location: index.php");
    exit;
}
?>
