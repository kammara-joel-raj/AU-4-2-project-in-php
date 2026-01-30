<?php
require_once 'includes/db.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($action == 'add' && $id > 0) {
    // If item exists, increase quantity, else add it
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
    // Redirect back to previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

if ($action == 'remove' && $id > 0) {
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}

if ($action == 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}
?>