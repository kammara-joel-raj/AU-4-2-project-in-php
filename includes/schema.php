<?php

require_once __DIR__ . '/config.php';

if (!function_exists('ensure_directory')) {
    function ensure_directory($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}

if (!function_exists('app_table_exists')) {
    function app_table_exists(PDO $pdo, $table)
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?"
        );
        $stmt->execute([$table]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('app_column_exists')) {
    function app_column_exists(PDO $pdo, $table, $column)
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?"
        );
        $stmt->execute([$table, $column]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('app_index_exists')) {
    function app_index_exists(PDO $pdo, $table, $indexName)
    {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?"
        );
        $stmt->execute([$table, $indexName]);

        return (int) $stmt->fetchColumn() > 0;
    }
}

if (!function_exists('app_exec')) {
    function app_exec(PDO $pdo, $sql)
    {
        $pdo->exec($sql);
    }
}

if (!function_exists('ensure_app_schema')) {
    function ensure_app_schema(PDO $pdo)
    {
        static $schemaReady = false;

        if ($schemaReady) {
            return;
        }

        ensure_directory(APP_STORAGE_PATH);
        ensure_directory(APP_SESSION_PATH);
        ensure_directory(dirname(__DIR__) . '/uploads/generated');
        ensure_directory(dirname(__DIR__) . '/uploads/tryon');
        ensure_directory(dirname(__DIR__) . '/uploads/products');

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'customer',
                phone VARCHAR(20) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                category VARCHAR(50) NOT NULL,
                brand VARCHAR(100) NULL,
                price DECIMAL(10,2) NOT NULL,
                rating DECIMAL(2,1) DEFAULT 5.0,
                reviews INT DEFAULT 0,
                origin VARCHAR(50) DEFAULT 'India',
                description VARCHAR(255) NULL,
                long_description TEXT NULL,
                tag VARCHAR(50) NULL,
                image_bg_color VARCHAR(20) DEFAULT '#222',
                image_text VARCHAR(100) NULL,
                image VARCHAR(255) NULL,
                stock_qty INT NOT NULL DEFAULT 25,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                available_sizes VARCHAR(100) NOT NULL DEFAULT '" . DEFAULT_PRODUCT_SIZES . "',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                shipping_name VARCHAR(100) NULL,
                shipping_address TEXT NULL,
                phone VARCHAR(20) NULL,
                payment_method VARCHAR(50) NOT NULL DEFAULT 'razorpay',
                status VARCHAR(30) NOT NULL DEFAULT 'pending_payment',
                payment_status VARCHAR(30) NOT NULL DEFAULT 'pending',
                gateway_order_id VARCHAR(100) NULL,
                gateway_payment_id VARCHAR(100) NULL,
                gateway_signature VARCHAR(255) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_orders_user (user_id),
                INDEX idx_orders_gateway (gateway_order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL DEFAULT 1,
                size VARCHAR(20) NOT NULL DEFAULT 'M',
                price DECIMAL(10,2) NULL,
                unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_order_items_order (order_id),
                INDEX idx_order_items_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS cart_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                size VARCHAR(20) NOT NULL DEFAULT 'M',
                quantity INT NOT NULL DEFAULT 1,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_cart_line (user_id, product_id, size),
                INDEX idx_cart_user (user_id),
                INDEX idx_cart_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS wishlist_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_wishlist_line (user_id, product_id),
                INDEX idx_wishlist_user (user_id),
                INDEX idx_wishlist_product (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        app_exec(
            $pdo,
            "CREATE TABLE IF NOT EXISTS webhook_events (
                event_id VARCHAR(100) PRIMARY KEY,
                event_type VARCHAR(100) NOT NULL,
                processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $columnMap = [
            'users' => [
                "ALTER TABLE users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'customer'",
                "ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL",
                "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            ],
            'products' => [
                "ALTER TABLE products ADD COLUMN image VARCHAR(255) NULL",
                "ALTER TABLE products ADD COLUMN stock_qty INT NOT NULL DEFAULT 25",
                "ALTER TABLE products ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1",
                "ALTER TABLE products ADD COLUMN available_sizes VARCHAR(100) NOT NULL DEFAULT '" . DEFAULT_PRODUCT_SIZES . "'",
                "ALTER TABLE products ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "ALTER TABLE products ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            ],
            'orders' => [
                "ALTER TABLE orders ADD COLUMN shipping_name VARCHAR(100) NULL",
                "ALTER TABLE orders ADD COLUMN phone VARCHAR(20) NULL",
                "ALTER TABLE orders ADD COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending_payment'",
                "ALTER TABLE orders ADD COLUMN payment_status VARCHAR(30) NOT NULL DEFAULT 'pending'",
                "ALTER TABLE orders ADD COLUMN gateway_order_id VARCHAR(100) NULL",
                "ALTER TABLE orders ADD COLUMN gateway_payment_id VARCHAR(100) NULL",
                "ALTER TABLE orders ADD COLUMN gateway_signature VARCHAR(255) NULL",
                "ALTER TABLE orders ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
                "ALTER TABLE orders ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            ],
            'order_items' => [
                "ALTER TABLE order_items ADD COLUMN size VARCHAR(20) NOT NULL DEFAULT 'M'",
                "ALTER TABLE order_items ADD COLUMN unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00",
                "ALTER TABLE order_items ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
            ],
        ];

        foreach ($columnMap as $table => $alterStatements) {
            foreach ($alterStatements as $statement) {
                if (preg_match('/ADD COLUMN ([a-z_]+)/i', $statement, $matches)) {
                    $column = $matches[1];
                    if (!app_column_exists($pdo, $table, $column)) {
                        app_exec($pdo, $statement);
                    }
                }
            }
        }

        if (!app_index_exists($pdo, 'products', 'idx_products_catalog')) {
            app_exec($pdo, "CREATE INDEX idx_products_catalog ON products (is_active, category, price, rating)");
        }

        if (!app_index_exists($pdo, 'orders', 'idx_orders_status')) {
            app_exec($pdo, "CREATE INDEX idx_orders_status ON orders (status, payment_status)");
        }

        if (!app_index_exists($pdo, 'users', 'idx_users_role')) {
            app_exec($pdo, "CREATE INDEX idx_users_role ON users (role)");
        }

        app_exec(
            $pdo,
            "UPDATE products
             SET available_sizes = CASE
                 WHEN category = 'accessories' THEN '" . DEFAULT_ACCESSORY_SIZES . "'
                 WHEN available_sizes IS NULL OR available_sizes = '' THEN '" . DEFAULT_PRODUCT_SIZES . "'
                 ELSE available_sizes
             END"
        );

        app_exec(
            $pdo,
            "UPDATE products
             SET stock_qty = CASE
                 WHEN stock_qty IS NULL OR stock_qty < 0 THEN 25
                 ELSE stock_qty
             END"
        );

        app_exec($pdo, "UPDATE products SET is_active = 1 WHERE is_active IS NULL");
        app_exec($pdo, "UPDATE users SET role = 'customer' WHERE role IS NULL OR role = ''");
        app_exec($pdo, "UPDATE order_items SET unit_price = COALESCE(NULLIF(unit_price, 0), price, 0)");

        $adminCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
        if ($adminCount === 0) {
            $firstUserId = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1")->fetchColumn();
            if ($firstUserId) {
                $stmt = $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                $stmt->execute([$firstUserId]);
            }
        }

        $schemaReady = true;
    }
}

