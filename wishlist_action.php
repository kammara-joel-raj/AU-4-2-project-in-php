<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

require_valid_csrf();

$productId = (int) ($_POST['product_id'] ?? 0);
$action = $_POST['action'] ?? 'add';
$redirect = trim((string) ($_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? 'shop.php')));

if ($redirect === '' || str_contains($redirect, '://')) {
    $redirect = 'shop.php';
}

$result = toggle_wishlist_item($pdo, $productId, $action);

if (wants_json()) {
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

session_flash($result['success'] ? 'success' : 'error', $result['message']);
safe_redirect($redirect);
