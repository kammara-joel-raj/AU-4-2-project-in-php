CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
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
    available_sizes VARCHAR(100) NOT NULL DEFAULT 'S,M,L,XL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_products_catalog (is_active, category, price, rating)
);

CREATE TABLE IF NOT EXISTS orders (
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
    INDEX idx_orders_status (status, payment_status),
    INDEX idx_orders_gateway (gateway_order_id)
);

CREATE TABLE IF NOT EXISTS order_items (
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
);

CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    size VARCHAR(20) NOT NULL DEFAULT 'M',
    quantity INT NOT NULL DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_cart_line (user_id, product_id, size),
    INDEX idx_cart_user (user_id),
    INDEX idx_cart_product (product_id)
);

CREATE TABLE IF NOT EXISTS wishlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_wishlist_line (user_id, product_id),
    INDEX idx_wishlist_user (user_id),
    INDEX idx_wishlist_product (product_id)
);

CREATE TABLE IF NOT EXISTS webhook_events (
    event_id VARCHAR(100) PRIMARY KEY,
    event_type VARCHAR(100) NOT NULL,
    processed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products
    (name, category, brand, price, rating, reviews, description, long_description, tag, image_bg_color, image, stock_qty, available_sizes)
VALUES
    ('Senate House ''26 Hoodie', 'apparel', 'AU Heritage', 1899.00, 4.8, 124, 'Navy Blue // Heavyweight Cotton', 'Official University merchandise. Crafted from 400GSM heavyweight cotton fleece. Features high-density embroidery.', 'BESTSELLER', '#222', 'uploads/products/hoodie.avif', 35, 'S,M,L,XL'),
    ('Heritage Graphic Tee', 'apparel', 'AU Basics', 799.00, 4.5, 89, 'Vintage Wash // Archival', 'A tribute to the 1926 founding year. Features screen-printed archival photograph.', '', '#f4f4f4', 'uploads/products/T-shirt.webp', 40, 'S,M,L,XL'),
    ('The Vice Chancellor Jacket', 'premium', 'AU Premium', 4999.00, 5.0, 12, 'Leather Sleeves // Exclusive', 'The pinnacle of the collection. Wool body with genuine leather sleeves.', 'EXCLUSIVE', '#002147', 'uploads/products/jacket.webp', 12, 'M,L,XL'),
    ('Admin Block Tote', 'accessories', 'Eco-AU', 499.00, 4.2, 45, 'Heavy Canvas // Eco-Friendly', 'Durable canvas tote bag suitable for laptops and textbooks.', 'NEW', '#eee', 'uploads/products/adminBlock.jpg', 60, 'ONE SIZE')
ON DUPLICATE KEY UPDATE
    price = VALUES(price),
    description = VALUES(description),
    long_description = VALUES(long_description),
    image = VALUES(image),
    stock_qty = VALUES(stock_qty),
    available_sizes = VALUES(available_sizes),
    is_active = 1;
