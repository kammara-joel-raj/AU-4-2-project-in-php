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
$gatewayOrderId = trim((string) ($_POST['razorpay_order_id'] ?? ''));
$gatewayPaymentId = trim((string) ($_POST['razorpay_payment_id'] ?? ''));
$gatewaySignature = trim((string) ($_POST['razorpay_signature'] ?? ''));

$order = fetch_order_by_id($pdo, $orderId, current_user_id());
if (!$order) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit;
}

if ($gatewayOrderId === '' || $gatewayPaymentId === '' || $gatewaySignature === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Missing payment verification fields.']);
    exit;
}

if ($gatewayOrderId !== $order['gateway_order_id']) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Gateway order mismatch.']);
    exit;
}

if (!razorpay_verify_payment_signature($order['gateway_order_id'], $gatewayPaymentId, $gatewaySignature)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Payment signature verification failed.']);
    exit;
}

try {
    $payment = razorpay_fetch_payment($gatewayPaymentId);
    if (($payment['status'] ?? '') === 'failed') {
        mark_order_payment_state($pdo, $orderId, 'failed', 'pending_payment');
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Razorpay reported this payment as failed.']);
        exit;
    }
} catch (Throwable $e) {
    // Signature verification is already complete. Continue and let webhooks reconcile if needed.
}

$result = finalize_paid_order($pdo, $orderId, $gatewayOrderId, $gatewayPaymentId, $gatewaySignature);

if (!$result['success']) {
    http_response_code(422);
    echo json_encode($result);
    exit;
}

echo json_encode([
    'success' => true,
    'redirect' => 'profile.php?tab=orders&order=' . (int) $orderId,
]);
