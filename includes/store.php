<?php

require_once __DIR__ . '/config.php';

if (!function_exists('h')) {
    function h($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('format_money')) {
    function format_money($amount)
    {
        return number_format((float) $amount, 2);
    }
}

if (!function_exists('order_number')) {
    function order_number($orderId)
    {
        return 'AU-2026-' . str_pad((string) $orderId, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('default_sizes_for_category')) {
    function default_sizes_for_category($category)
    {
        return strtolower((string) $category) === 'accessories'
            ? explode(',', DEFAULT_ACCESSORY_SIZES)
            : explode(',', DEFAULT_PRODUCT_SIZES);
    }
}

if (!function_exists('product_sizes')) {
    function product_sizes(array $product)
    {
        $raw = trim((string) ($product['available_sizes'] ?? ''));
        if ($raw === '') {
            return array_map('trim', default_sizes_for_category($product['category'] ?? ''));
        }

        $sizes = array_values(array_filter(array_map('trim', explode(',', $raw))));
        if (empty($sizes)) {
            return array_map('trim', default_sizes_for_category($product['category'] ?? ''));
        }

        return $sizes;
    }
}

if (!function_exists('normalize_size')) {
    function normalize_size($size)
    {
        $size = trim((string) $size);
        if ($size === '') {
            return 'M';
        }

        return strtoupper($size);
    }
}

if (!function_exists('normalize_session_cart')) {
    function normalize_session_cart()
    {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
            return $_SESSION['cart'];
        }

        $normalized = [];
        foreach ($_SESSION['cart'] as $key => $line) {
            if (is_array($line) && isset($line['product_id'])) {
                $productId = (int) $line['product_id'];
                $size = normalize_size($line['size'] ?? 'M');
                $quantity = max(1, (int) ($line['quantity'] ?? 1));
            } else {
                $productId = (int) $key;
                $size = 'M';
                $quantity = max(1, (int) $line);
            }

            if ($productId <= 0) {
                continue;
            }

            $normalized[$productId . ':' . $size] = [
                'product_id' => $productId,
                'size' => $size,
                'quantity' => $quantity,
            ];
        }

        $_SESSION['cart'] = $normalized;

        return $_SESSION['cart'];
    }
}

if (!function_exists('guest_cart_lines')) {
    function guest_cart_lines()
    {
        return normalize_session_cart();
    }
}

if (!function_exists('set_guest_cart_lines')) {
    function set_guest_cart_lines(array $lines)
    {
        $_SESSION['cart'] = $lines;
    }
}

if (!function_exists('fetch_product_by_id')) {
    function fetch_product_by_id(PDO $pdo, $productId, $includeInactive = false)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        if (!$includeInactive) {
            $sql .= " AND is_active = 1";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([(int) $productId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

if (!function_exists('validate_product_size')) {
    function validate_product_size(array $product, $size)
    {
        $normalized = normalize_size($size);
        return in_array($normalized, product_sizes($product), true);
    }
}

if (!function_exists('build_product_filters')) {
    function build_product_filters(array $filters)
    {
        $conditions = ['is_active = 1'];
        $params = [];

        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $conditions[] = 'category = ?';
            $params[] = $filters['category'];
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== null) {
            $conditions[] = 'price <= ?';
            $params[] = (float) $filters['max_price'];
        }

        if (isset($filters['min_rating']) && $filters['min_rating'] !== null && $filters['min_rating'] > 0) {
            $conditions[] = 'rating >= ?';
            $params[] = (float) $filters['min_rating'];
        }

        if (!empty($filters['q'])) {
            $conditions[] = '(name LIKE ? OR description LIKE ? OR brand LIKE ?)';
            $like = '%' . $filters['q'] . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        return [$conditions, $params];
    }
}

if (!function_exists('product_sort_clause')) {
    function product_sort_clause($sort)
    {
        return match ($sort) {
            'price_asc' => 'price ASC, id DESC',
            'price_desc' => 'price DESC, id DESC',
            'rating_desc' => 'rating DESC, reviews DESC, id DESC',
            default => 'id DESC',
        };
    }
}

if (!function_exists('fetch_products_paginated')) {
    function fetch_products_paginated(PDO $pdo, array $filters = [])
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, (int) ($filters['per_page'] ?? SHOP_PRODUCTS_PER_PAGE));
        [$conditions, $params] = build_product_filters($filters);
        $where = implode(' AND ', $conditions);

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $where");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $offset = ($page - 1) * $perPage;
        $sort = product_sort_clause($filters['sort'] ?? 'latest');

        $stmt = $pdo->prepare("SELECT * FROM products WHERE $where ORDER BY $sort LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'page' => $page,
            'pages' => max(1, (int) ceil($total / $perPage)),
            'per_page' => $perPage,
        ];
    }
}

if (!function_exists('fetch_featured_products')) {
    function fetch_featured_products(PDO $pdo, $limit = 4)
    {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = 1 ORDER BY rating DESC, reviews DESC, id DESC LIMIT ?");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('search_products')) {
    function search_products(PDO $pdo, $query, $limit = 20)
    {
        $query = trim((string) $query);
        if ($query === '') {
            return [];
        }

        $stmt = $pdo->prepare(
            "SELECT * FROM products
             WHERE is_active = 1 AND (name LIKE ? OR description LIKE ? OR brand LIKE ?)
             ORDER BY rating DESC, reviews DESC, id DESC
             LIMIT ?"
        );
        $like = '%' . $query . '%';
        $stmt->bindValue(1, $like);
        $stmt->bindValue(2, $like);
        $stmt->bindValue(3, $like);
        $stmt->bindValue(4, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('load_user_cart_lines')) {
    function load_user_cart_lines(PDO $pdo, $userId)
    {
        $stmt = $pdo->prepare("SELECT product_id, size, quantity FROM cart_items WHERE user_id = ? ORDER BY id ASC");
        $stmt->execute([(int) $userId]);
        $lines = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $line) {
            $key = (int) $line['product_id'] . ':' . normalize_size($line['size']);
            $lines[$key] = [
                'product_id' => (int) $line['product_id'],
                'size' => normalize_size($line['size']),
                'quantity' => (int) $line['quantity'],
            ];
        }

        return $lines;
    }
}

if (!function_exists('merge_session_cart_into_db')) {
    function merge_session_cart_into_db(PDO $pdo, $userId)
    {
        $lines = guest_cart_lines();
        if (empty($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $stmt = $pdo->prepare(
                "INSERT INTO cart_items (user_id, product_id, size, quantity)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
            );
            $stmt->execute([
                (int) $userId,
                (int) $line['product_id'],
                normalize_size($line['size']),
                (int) $line['quantity'],
            ]);
        }

        $_SESSION['cart'] = [];
    }
}

if (!function_exists('current_cart_lines')) {
    function current_cart_lines(PDO $pdo)
    {
        if (is_logged_in()) {
            return load_user_cart_lines($pdo, current_user_id());
        }

        return guest_cart_lines();
    }
}

if (!function_exists('current_cart_count')) {
    function current_cart_count(PDO $pdo)
    {
        if (is_logged_in()) {
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart_items WHERE user_id = ?");
            $stmt->execute([current_user_id()]);
            return (int) $stmt->fetchColumn();
        }

        $count = 0;
        foreach (guest_cart_lines() as $line) {
            $count += (int) $line['quantity'];
        }

        return $count;
    }
}

if (!function_exists('set_logged_in_cart_line')) {
    function set_logged_in_cart_line(PDO $pdo, $userId, $productId, $size, $quantity)
    {
        if ($quantity <= 0) {
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ? AND size = ?");
            $stmt->execute([(int) $userId, (int) $productId, normalize_size($size)]);
            return;
        }

        $stmt = $pdo->prepare(
            "INSERT INTO cart_items (user_id, product_id, size, quantity)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = VALUES(quantity)"
        );
        $stmt->execute([(int) $userId, (int) $productId, normalize_size($size), (int) $quantity]);
    }
}

if (!function_exists('add_item_to_cart')) {
    function add_item_to_cart(PDO $pdo, $productId, $size, $quantity)
    {
        $product = fetch_product_by_id($pdo, $productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if ((int) $product['stock_qty'] <= 0) {
            return ['success' => false, 'message' => 'This product is currently out of stock.'];
        }

        $size = normalize_size($size);
        if (!validate_product_size($product, $size)) {
            return ['success' => false, 'message' => 'Please choose a valid size.'];
        }

        $quantity = max(1, min((int) $quantity, (int) $product['stock_qty']));

        if (is_logged_in()) {
            $lines = load_user_cart_lines($pdo, current_user_id());
            $key = (int) $productId . ':' . $size;
            $newQuantity = $quantity + (int) ($lines[$key]['quantity'] ?? 0);
            $newQuantity = min($newQuantity, (int) $product['stock_qty']);
            set_logged_in_cart_line($pdo, current_user_id(), $productId, $size, $newQuantity);
        } else {
            $lines = guest_cart_lines();
            $key = (int) $productId . ':' . $size;
            $newQuantity = $quantity + (int) ($lines[$key]['quantity'] ?? 0);
            $newQuantity = min($newQuantity, (int) $product['stock_qty']);
            $lines[$key] = [
                'product_id' => (int) $productId,
                'size' => $size,
                'quantity' => $newQuantity,
            ];
            set_guest_cart_lines($lines);
        }

        return [
            'success' => true,
            'message' => 'Added to cart.',
            'count' => current_cart_count($pdo),
        ];
    }
}

if (!function_exists('update_cart_quantity')) {
    function update_cart_quantity(PDO $pdo, $productId, $size, $quantity)
    {
        $product = fetch_product_by_id($pdo, $productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        $size = normalize_size($size);
        $quantity = (int) $quantity;

        if (is_logged_in()) {
            set_logged_in_cart_line($pdo, current_user_id(), $productId, $size, min(max($quantity, 0), (int) $product['stock_qty']));
        } else {
            $lines = guest_cart_lines();
            $key = (int) $productId . ':' . $size;
            if ($quantity <= 0) {
                unset($lines[$key]);
            } else {
                $lines[$key] = [
                    'product_id' => (int) $productId,
                    'size' => $size,
                    'quantity' => min($quantity, (int) $product['stock_qty']),
                ];
            }
            set_guest_cart_lines($lines);
        }

        return ['success' => true, 'message' => 'Cart updated.', 'count' => current_cart_count($pdo)];
    }
}

if (!function_exists('remove_item_from_cart')) {
    function remove_item_from_cart(PDO $pdo, $productId, $size)
    {
        return update_cart_quantity($pdo, $productId, $size, 0);
    }
}

if (!function_exists('clear_current_cart')) {
    function clear_current_cart(PDO $pdo)
    {
        if (is_logged_in()) {
            $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([current_user_id()]);
        } else {
            $_SESSION['cart'] = [];
        }

        return ['success' => true, 'message' => 'Cart cleared.', 'count' => 0];
    }
}

if (!function_exists('cart_items_with_products')) {
    function cart_items_with_products(PDO $pdo)
    {
        $lines = current_cart_lines($pdo);
        $items = [];
        $subtotal = 0.0;

        foreach ($lines as $line) {
            $product = fetch_product_by_id($pdo, $line['product_id']);
            if (!$product) {
                continue;
            }

            $quantity = min((int) $line['quantity'], (int) $product['stock_qty']);
            if ($quantity <= 0) {
                continue;
            }

            $lineTotal = (float) $product['price'] * $quantity;
            $subtotal += $lineTotal;

            $items[] = [
                'product' => $product,
                'product_id' => (int) $product['id'],
                'size' => normalize_size($line['size']),
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total' => $subtotal,
        ];
    }
}

if (!function_exists('wishlist_ids')) {
    function wishlist_ids(PDO $pdo, $userId = null)
    {
        $userId = $userId ?: current_user_id();
        if (!$userId) {
            return [];
        }

        $stmt = $pdo->prepare("SELECT product_id FROM wishlist_items WHERE user_id = ?");
        $stmt->execute([(int) $userId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}

if (!function_exists('toggle_wishlist_item')) {
    function toggle_wishlist_item(PDO $pdo, $productId, $action)
    {
        require_login('login.php');
        $product = fetch_product_by_id($pdo, $productId);
        if (!$product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if ($action === 'remove') {
            $stmt = $pdo->prepare("DELETE FROM wishlist_items WHERE user_id = ? AND product_id = ?");
            $stmt->execute([current_user_id(), (int) $productId]);
            return ['success' => true, 'message' => 'Removed from wishlist.'];
        }

        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO wishlist_items (user_id, product_id) VALUES (?, ?)"
        );
        $stmt->execute([current_user_id(), (int) $productId]);

        return ['success' => true, 'message' => 'Saved to wishlist.'];
    }
}

if (!function_exists('wishlist_products')) {
    function wishlist_products(PDO $pdo, $userId = null)
    {
        $userId = $userId ?: current_user_id();
        if (!$userId) {
            return [];
        }

        $stmt = $pdo->prepare(
            "SELECT p.* FROM wishlist_items w
             INNER JOIN products p ON p.id = w.product_id
             WHERE w.user_id = ? AND p.is_active = 1
             ORDER BY w.created_at DESC"
        );
        $stmt->execute([(int) $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (!function_exists('fetch_user_orders')) {
    function fetch_user_orders(PDO $pdo, $userId = null)
    {
        $userId = $userId ?: current_user_id();
        if (!$userId) {
            return [];
        }

        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([(int) $userId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as &$order) {
            $itemsStmt = $pdo->prepare(
                "SELECT oi.*, p.name, p.image
                 FROM order_items oi
                 INNER JOIN products p ON p.id = oi.product_id
                 WHERE oi.order_id = ?
                 ORDER BY oi.id ASC"
            );
            $itemsStmt->execute([(int) $order['id']]);
            $order['items'] = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    }
}

if (!function_exists('update_profile_settings')) {
    function update_profile_settings(PDO $pdo, array $input)
    {
        require_login('login.php');

        $userId = current_user_id();
        $fullName = trim((string) ($input['full_name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? ''));

        if ($fullName === '' || $email === '') {
            return ['success' => false, 'message' => 'Name and email are required.'];
        }

        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return ['success' => false, 'message' => 'Account not found.'];
        }

        $emailStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $emailStmt->execute([$email, $userId]);
        if ($emailStmt->fetchColumn()) {
            return ['success' => false, 'message' => 'That email is already in use.'];
        }

        $newPassword = trim((string) ($input['new_password'] ?? ''));
        $currentPassword = trim((string) ($input['current_password'] ?? ''));

        if ($newPassword !== '') {
            if ($currentPassword === '' || !password_verify($currentPassword, $user['password'])) {
                return ['success' => false, 'message' => 'Current password is incorrect.'];
            }
        }

        if ($newPassword !== '') {
            $stmt = $pdo->prepare(
                "UPDATE users SET full_name = ?, email = ?, phone = ?, password = ? WHERE id = ?"
            );
            $stmt->execute([$fullName, $email, $phone !== '' ? $phone : null, password_hash($newPassword, PASSWORD_DEFAULT), $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$fullName, $email, $phone !== '' ? $phone : null, $userId]);
        }

        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_email'] = $email;

        return ['success' => true, 'message' => 'Profile updated successfully.'];
    }
}

if (!function_exists('current_user_record')) {
    function current_user_record(PDO $pdo, $userId = null)
    {
        $userId = $userId ?: current_user_id();
        if (!$userId) {
            return null;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int) $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

if (!function_exists('fetch_order_by_id')) {
    function fetch_order_by_id(PDO $pdo, $orderId, $userId = null)
    {
        $params = [(int) $orderId];
        $sql = "SELECT * FROM orders WHERE id = ?";

        if ($userId !== null) {
            $sql .= " AND user_id = ?";
            $params[] = (int) $userId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

if (!function_exists('fetch_order_by_gateway_order_id')) {
    function fetch_order_by_gateway_order_id(PDO $pdo, $gatewayOrderId)
    {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE gateway_order_id = ?");
        $stmt->execute([(string) $gatewayOrderId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

if (!function_exists('create_pending_checkout_order')) {
    function create_pending_checkout_order(PDO $pdo, array $shipping)
    {
        require_login('login.php');

        $shippingName = trim((string) ($shipping['shipping_name'] ?? ''));
        $phone = trim((string) ($shipping['phone'] ?? ''));
        $address = trim((string) ($shipping['shipping_address'] ?? ''));

        if ($shippingName === '' || $phone === '' || $address === '') {
            return ['success' => false, 'message' => 'Please complete the shipping form before payment.'];
        }

        $cart = cart_items_with_products($pdo);
        if (empty($cart['items'])) {
            return ['success' => false, 'message' => 'Your cart is empty.'];
        }

        foreach ($cart['items'] as $item) {
            if ((int) $item['product']['stock_qty'] < (int) $item['quantity']) {
                return ['success' => false, 'message' => $item['product']['name'] . ' no longer has enough stock.'];
            }
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO orders
                    (user_id, total_amount, shipping_name, shipping_address, phone, payment_method, status, payment_status)
                 VALUES (?, ?, ?, ?, ?, 'razorpay', 'pending_payment', 'pending')"
            );
            $stmt->execute([
                current_user_id(),
                $cart['total'],
                $shippingName,
                $address,
                $phone,
            ]);

            $orderId = (int) $pdo->lastInsertId();
            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, size, price, unit_price)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            foreach ($cart['items'] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['size'],
                    $item['product']['price'],
                    $item['product']['price'],
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            return ['success' => false, 'message' => 'Unable to prepare the order. ' . $e->getMessage()];
        }

        try {
            $gatewayOrder = razorpay_create_gateway_order($orderId, (int) round($cart['total'] * 100), [
                'local_order_id' => (string) $orderId,
                'user_id' => (string) current_user_id(),
            ]);

            $updateStmt = $pdo->prepare("UPDATE orders SET gateway_order_id = ? WHERE id = ?");
            $updateStmt->execute([$gatewayOrder['id'], $orderId]);
        } catch (Throwable $e) {
            $updateStmt = $pdo->prepare("UPDATE orders SET payment_status = 'failed' WHERE id = ?");
            $updateStmt->execute([$orderId]);

            return ['success' => false, 'message' => $e->getMessage()];
        }

        return [
            'success' => true,
            'order' => fetch_order_by_id($pdo, $orderId, current_user_id()),
            'gateway_order' => $gatewayOrder,
            'cart' => $cart,
        ];
    }
}

if (!function_exists('mark_order_payment_state')) {
    function mark_order_payment_state(PDO $pdo, $orderId, $paymentStatus, $orderStatus = null)
    {
        $currentStmt = $pdo->prepare("SELECT payment_status, status FROM orders WHERE id = ?");
        $currentStmt->execute([(int) $orderId]);
        $current = $currentStmt->fetch(PDO::FETCH_ASSOC);

        if (!$current) {
            return;
        }

        // Once an order is paid, ignore late dismiss/failure signals from the client.
        if (($current['payment_status'] ?? '') === 'paid' && $paymentStatus !== 'paid') {
            return;
        }

        $orderStatus = $orderStatus ?: ($paymentStatus === 'paid' ? 'paid' : 'pending_payment');
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([(string) $paymentStatus, (string) $orderStatus, (int) $orderId]);
    }
}

if (!function_exists('finalize_paid_order')) {
    function finalize_paid_order(PDO $pdo, $orderId, $gatewayOrderId, $gatewayPaymentId, $gatewaySignature)
    {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? FOR UPDATE");
            $stmt->execute([(int) $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                throw new RuntimeException('Order not found.');
            }

            if ($order['payment_status'] === 'paid') {
                $pdo->commit();
                return ['success' => true, 'order' => $order];
            }

            $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ? ORDER BY id ASC");
            $itemsStmt->execute([(int) $orderId]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                $stockStmt = $pdo->prepare(
                    "UPDATE products
                     SET stock_qty = stock_qty - ?
                     WHERE id = ? AND stock_qty >= ?"
                );
                $stockStmt->execute([
                    (int) $item['quantity'],
                    (int) $item['product_id'],
                    (int) $item['quantity'],
                ]);

                if ($stockStmt->rowCount() !== 1) {
                    throw new RuntimeException('Stock changed before payment confirmation. Please contact support.');
                }
            }

            $updateStmt = $pdo->prepare(
                "UPDATE orders
                 SET payment_status = 'paid',
                     status = 'paid',
                     gateway_order_id = ?,
                     gateway_payment_id = ?,
                     gateway_signature = ?,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?"
            );
            $updateStmt->execute([
                (string) $gatewayOrderId,
                (string) $gatewayPaymentId,
                (string) $gatewaySignature,
                (int) $orderId,
            ]);

            $clearStmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $clearStmt->execute([(int) $order['user_id']]);

            $pdo->commit();

            return ['success' => true, 'order' => fetch_order_by_id($pdo, $orderId)];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            mark_order_payment_state($pdo, $orderId, 'failed', 'pending_payment');

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

if (!function_exists('record_webhook_event')) {
    function record_webhook_event(PDO $pdo, $eventId, $eventType)
    {
        if ($eventId === '') {
            return true;
        }

        $stmt = $pdo->prepare("INSERT IGNORE INTO webhook_events (event_id, event_type) VALUES (?, ?)");
        $stmt->execute([(string) $eventId, (string) $eventType]);

        return $stmt->rowCount() === 1;
    }
}

if (!function_exists('product_ai_category')) {
    function product_ai_category(array $product)
    {
        $category = strtolower((string) ($product['category'] ?? ''));
        $name = strtolower((string) ($product['name'] ?? ''));

        if ($category === 'accessories') {
            return null;
        }

        if (str_contains($name, 'dress')) {
            return 'dresses';
        }

        if (str_contains($name, 'pant') || str_contains($name, 'jean') || str_contains($name, 'short')) {
            return 'lower_body';
        }

        return 'upper_body';
    }
}
