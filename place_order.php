<?php
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Please login first']));
}

$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    try {
        $pdo->beginTransaction();

        // 1. Insert Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'], 
            $data['total'], 
            $data['address'], 
            $data['method']
        ]);
        $order_id = $pdo->lastInsertId();

        // 2. Insert Items
        foreach ($_SESSION['cart'] as $pid => $qty) {
            // Fetch price (In real app, fetch from DB again to prevent tampering)
            $p_stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $p_stmt->execute([$pid]);
            $price = $p_stmt->fetchColumn();

            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $item_stmt->execute([$order_id, $pid, $qty, $price]);
        }

        // 3. Clear Cart
        unset($_SESSION['cart']);
        
        $pdo->commit();
        echo json_encode(['status' => 'success', 'order_id' => 'AU-2026-' . $order_id]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>