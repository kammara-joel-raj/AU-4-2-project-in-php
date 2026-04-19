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

if (!razorpay_enabled()) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Razorpay is not configured yet. Add your Razorpay keys to continue.',
    ]);
    exit;
}

$result = create_pending_checkout_order($pdo, $_POST);

if (!$result['success']) {
    http_response_code(422);
    echo json_encode($result);
    exit;
}

$order = $result['order'];
$gatewayOrder = $result['gateway_order'];
$user = current_user_record($pdo);

echo json_encode([
    'success' => true,
    'local_order_id' => (int) $order['id'],
    'order_number' => order_number($order['id']),
    'gateway_order_id' => $gatewayOrder['id'],
    'amount' => (int) $gatewayOrder['amount'],
    'currency' => APP_CURRENCY,
    'key_id' => RAZORPAY_KEY_ID,
    'store_name' => APP_NAME,
    'prefill' => [
        'name' => $order['shipping_name'],
        'email' => $user['email'] ?? '',
        'contact' => $order['phone'],
    ],
]);
