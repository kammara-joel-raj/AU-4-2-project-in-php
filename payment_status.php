<?php
require_once 'includes/bootstrap.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

require_login('login.php');
require_valid_csrf();

$orderId = (int) ($_POST['order_id'] ?? 0);
$paymentState = trim((string) ($_POST['payment_state'] ?? 'pending'));
$order = fetch_order_by_id($pdo, $orderId, current_user_id());

if (!$order) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit;
}

$paymentState = in_array($paymentState, ['pending', 'failed'], true) ? $paymentState : 'pending';
mark_order_payment_state($pdo, $orderId, $paymentState, 'pending_payment');

echo json_encode(['success' => true]);
