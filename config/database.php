<?php
// config.php
$host = 'localhost';
$dbname = 'icecream_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Create DB if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}

// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
