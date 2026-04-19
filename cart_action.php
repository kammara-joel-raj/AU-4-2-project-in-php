<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
}

require_valid_csrf();

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);
$size = $_POST['size'] ?? 'M';
$quantity = (int) ($_POST['quantity'] ?? 1);
$redirect = trim((string) ($_POST['redirect'] ?? ''));

if ($redirect === '' || str_contains($redirect, '://')) {
    $redirect = 'cart.php';
}

switch ($action) {
    case 'add':
        $result = add_item_to_cart($pdo, $productId, $size, $quantity);
        break;
    case 'update':
        $result = update_cart_quantity($pdo, $productId, $size, $quantity);
        break;
    case 'remove':
        $result = remove_item_from_cart($pdo, $productId, $size);
        break;
    case 'clear':
        $result = clear_current_cart($pdo);
        break;
    default:
        $result = ['success' => false, 'message' => 'Unknown cart action.'];
        break;
}

if (wants_json()) {
    header('Content-Type: application/json');
    http_response_code($result['success'] ? 200 : 422);
    echo json_encode($result);
    exit;
}

session_flash($result['success'] ? 'success' : 'error', $result['message']);
safe_redirect($redirect);
