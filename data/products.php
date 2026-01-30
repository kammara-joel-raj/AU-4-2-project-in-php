<?php
require_once __DIR__ . '/../includes/db.php';

// 1. Fetch all products for Shop Page
try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If DB fails, return empty array to prevent crash
    $products = [];
}

// 2. Helper function for Product Page & Cart
function getProductById($id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>