<?php

require_once __DIR__ . '/config.php';

if (!function_exists('razorpay_enabled')) {
    function razorpay_enabled()
    {
        return RAZORPAY_KEY_ID !== '' && RAZORPAY_KEY_SECRET !== '';
    }
}

if (!function_exists('razorpay_request')) {
    function razorpay_request($method, $endpoint, array $payload = null)
    {
        if (!razorpay_enabled()) {
            throw new RuntimeException('Razorpay credentials are not configured.');
        }

        $ch = curl_init();
        $url = rtrim(RAZORPAY_API_BASE, '/') . '/' . ltrim($endpoint, '/');
        $headers = ['Content-Type: application/json'];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_USERPWD => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 45,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Razorpay request failed: ' . $error);
        }

        curl_close($ch);
        $decoded = json_decode($response, true);

        if ($status >= 400) {
            $message = $decoded['error']['description'] ?? $decoded['error']['reason'] ?? 'Unknown Razorpay error.';
            throw new RuntimeException($message);
        }

        return $decoded;
    }
}

if (!function_exists('razorpay_create_gateway_order')) {
    function razorpay_create_gateway_order($localOrderId, $amountPaise, array $notes = [])
    {
        return razorpay_request('POST', 'orders', [
            'amount' => (int) $amountPaise,
            'currency' => APP_CURRENCY,
            'receipt' => 'local_order_' . (int) $localOrderId,
            'notes' => $notes,
        ]);
    }
}

if (!function_exists('razorpay_verify_payment_signature')) {
    function razorpay_verify_payment_signature($orderId, $paymentId, $signature)
    {
        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, RAZORPAY_KEY_SECRET);
        return hash_equals($expected, (string) $signature);
    }
}

if (!function_exists('razorpay_fetch_payment')) {
    function razorpay_fetch_payment($paymentId)
    {
        return razorpay_request('GET', 'payments/' . rawurlencode($paymentId));
    }
}

if (!function_exists('razorpay_verify_webhook_signature')) {
    function razorpay_verify_webhook_signature($rawPayload, $signature)
    {
        if (RAZORPAY_WEBHOOK_SECRET === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawPayload, RAZORPAY_WEBHOOK_SECRET);
        return hash_equals($expected, (string) $signature);
    }
}

