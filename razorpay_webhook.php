<?php
require_once 'includes/bootstrap.php';

$rawPayload = file_get_contents('php://input') ?: '';
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';
$eventId = $_SERVER['HTTP_X_RAZORPAY_EVENT_ID'] ?? '';

if (!razorpay_verify_webhook_signature($rawPayload, $signature)) {
    http_response_code(401);
    echo 'Invalid signature';
    exit;
}

$payload = json_decode($rawPayload, true);
$eventType = $payload['event'] ?? '';

if (!record_webhook_event($pdo, $eventId, $eventType)) {
    http_response_code(200);
    echo 'Duplicate';
    exit;
}

$paymentEntity = $payload['payload']['payment']['entity'] ?? null;
if ($paymentEntity) {
    $gatewayOrderId = $paymentEntity['order_id'] ?? '';
    $order = $gatewayOrderId !== '' ? fetch_order_by_gateway_order_id($pdo, $gatewayOrderId) : null;

    if ($order) {
        if ($eventType === 'payment.captured' || $eventType === 'payment.authorized') {
            finalize_paid_order(
                $pdo,
                $order['id'],
                $gatewayOrderId,
                $paymentEntity['id'] ?? '',
                $order['gateway_signature'] ?? ''
            );
        } elseif ($eventType === 'payment.failed') {
            mark_order_payment_state($pdo, $order['id'], 'failed', 'pending_payment');
        }
    }
}

http_response_code(200);
echo 'OK';
