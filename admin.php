<?php
require_once 'includes/bootstrap.php';

require_admin();

function admin_safe_sizes($category, $sizes)
{
    $sizes = trim((string) $sizes);
    if ($sizes === '') {
        return strtolower((string) $category) === 'accessories' ? DEFAULT_ACCESSORY_SIZES : DEFAULT_PRODUCT_SIZES;
    }

    $normalized = array_values(array_filter(array_map(static fn($value) => strtoupper(trim($value)), explode(',', $sizes))));
    return implode(',', $normalized);
}

function admin_handle_product_image_upload($currentImage = null)
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return $currentImage;
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        throw new RuntimeException('Invalid image type. Use JPG, PNG, WEBP, or AVIF.');
    }

    $filename = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $relativePath = 'uploads/products/' . $filename;
    $targetPath = __DIR__ . '/' . $relativePath;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        throw new RuntimeException('Failed to upload product image.');
    }

    if ($currentImage && str_starts_with($currentImage, 'uploads/products/prod_')) {
        $oldPath = __DIR__ . '/' . $currentImage;
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return $relativePath;
}

$search = trim((string) ($_GET['q'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? 1));
$editId = (int) ($_GET['edit'] ?? 0);
$editProduct = $editId > 0 ? fetch_product_by_id($pdo, $editId, true) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save_product') {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $current = $productId > 0 ? fetch_product_by_id($pdo, $productId, true) : null;
            $imagePath = admin_handle_product_image_upload($current['image'] ?? 'uploads/products/default.jpg');
            $category = trim((string) ($_POST['category'] ?? 'apparel'));
            $availableSizes = admin_safe_sizes($category, $_POST['available_sizes'] ?? '');

            $payload = [
                trim((string) $_POST['name']),
                $category,
                trim((string) ($_POST['brand'] ?? '')),
                (float) ($_POST['price'] ?? 0),
                (float) ($_POST['rating'] ?? 5),
                (int) ($_POST['reviews'] ?? 0),
                trim((string) ($_POST['origin'] ?? 'India')),
                trim((string) ($_POST['description'] ?? '')),
                trim((string) ($_POST['long_description'] ?? '')),
                trim((string) ($_POST['tag'] ?? '')),
                trim((string) ($_POST['image_bg_color'] ?? '#f4f4f4')),
                $imagePath,
                max(0, (int) ($_POST['stock_qty'] ?? 0)),
                $availableSizes,
                isset($_POST['is_active']) ? 1 : 0,
            ];

            if ($productId > 0 && $current) {
                $stmt = $pdo->prepare(
                    "UPDATE products
                     SET name = ?, category = ?, brand = ?, price = ?, rating = ?, reviews = ?, origin = ?, description = ?,
                         long_description = ?, tag = ?, image_bg_color = ?, image = ?, stock_qty = ?, available_sizes = ?, is_active = ?
                     WHERE id = ?"
                );
                $payload[] = $productId;
                $stmt->execute($payload);
                session_flash('success', 'Product updated successfully.');
            } else {
                $stmt = $pdo->prepare(
                    "INSERT INTO products
                        (name, category, brand, price, rating, reviews, origin, description, long_description, tag, image_bg_color, image, stock_qty, available_sizes, is_active)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute($payload);
                session_flash('success', 'Product created successfully.');
            }
        } elseif ($action === 'delete_product') {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $usageStmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
            $usageStmt->execute([$productId]);
            $hasOrders = (int) $usageStmt->fetchColumn() > 0;

            if ($hasOrders) {
                $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id = ?");
                $stmt->execute([$productId]);
                session_flash('success', 'Product had order history, so it was archived instead of deleted.');
            } else {
                $product = fetch_product_by_id($pdo, $productId, true);
                if ($product && !empty($product['image']) && str_starts_with($product['image'], 'uploads/products/prod_')) {
                    $path = __DIR__ . '/' . $product['image'];
                    if (is_file($path)) {
                        @unlink($path);
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                session_flash('success', 'Product deleted successfully.');
            }
        } elseif ($action === 'update_order_status') {
            $orderId = (int) ($_POST['order_id'] ?? 0);
            $status = $_POST['status'] ?? 'processing';
            $allowed = ['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $allowed, true)) {
                throw new RuntimeException('Invalid order status.');
            }

            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            session_flash('success', 'Order status updated.');
        }
    } catch (Throwable $e) {
        session_flash('error', $e->getMessage());
    }

    safe_redirect('admin.php');
}

$where = '';
$params = [];
if ($search !== '') {
    $where = "WHERE name LIKE ? OR brand LIKE ? OR category LIKE ?";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products $where");
$countStmt->execute($params);
$totalProducts = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($totalProducts / ADMIN_PRODUCTS_PER_PAGE));
$page = min($page, $pages);
$offset = ($page - 1) * ADMIN_PRODUCTS_PER_PAGE;

$productStmt = $pdo->prepare("SELECT * FROM products $where ORDER BY id DESC LIMIT " . ADMIN_PRODUCTS_PER_PAGE . " OFFSET " . $offset);
$productStmt->execute($params);
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$ordersStmt = $pdo->query(
    "SELECT o.*, u.full_name
     FROM orders o
     INNER JOIN users u ON u.id = o.user_id
     ORDER BY o.id DESC
     LIMIT 15"
);
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'AU // ADMIN';
include 'includes/header.php';
?>
<style>
    .admin-container { padding: 3rem 3%; min-height: 80vh; background: #050505; color: #fff; }
    .dashboard-grid { display: grid; grid-template-columns: 380px 1fr; gap: 2rem; }
    .panel { background: #111; border: 1px solid #333; padding: 1.5rem; }
    .panel-title { font-family: var(--font-tech); color: #00f3ff; margin-bottom: 1rem; border-bottom: 1px solid #333; padding-bottom: 10px; }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-family: var(--font-tech); font-size: 0.8rem; color: #888; margin-bottom: 5px; }
    .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; background: #000; border: 1px solid #333; color: #fff; font-family: var(--font-street); }
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.9rem; }
    .admin-table th, .admin-table td { padding: 12px; text-align: left; border-bottom: 1px solid #222; vertical-align: top; }
    .admin-table th { font-family: var(--font-tech); color: #888; text-transform: uppercase; }
    .btn-admin { background: transparent; color: #00f3ff; border: 1px solid #00f3ff; padding: 10px 20px; font-family: var(--font-tech); cursor: pointer; transition: 0.3s; width: 100%; }
    .btn-admin:hover { background: #00f3ff; color: #000; }
    @media (max-width: 980px) { .dashboard-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="admin-container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; gap:1rem; flex-wrap:wrap;">
        <div>
            <h1 style="font-family: var(--font-varsity); font-size: 3rem; color: #fff;">INVENTORY CONTROL</h1>
            <p style="font-family: var(--font-tech); color: #666; margin-top: 8px;">Role-based admin access is live for <?= h(current_user_name() ?? 'Admin') ?>.</p>
        </div>
        <form method="GET" style="display:flex; gap:10px; min-width:280px;">
            <input type="text" name="q" value="<?= h($search) ?>" placeholder="Search products..." style="padding: 10px; border: 1px solid #333; background: #000; color: #fff; font-family: var(--font-tech);">
            <button type="submit" class="btn-admin" style="width:auto;">SEARCH</button>
        </form>
    </div>

    <div class="dashboard-grid">
        <div class="panel">
            <h3 class="panel-title"><?= $editProduct ? '>> EDIT PRODUCT' : '>> ADD PRODUCT' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?= csrf_input() ?>
                <input type="hidden" name="action" value="save_product">
                <input type="hidden" name="product_id" value="<?= (int) ($editProduct['id'] ?? 0) ?>">

                <div class="form-group">
                    <label>ASSET NAME</label>
                    <input type="text" name="name" required value="<?= h($editProduct['name'] ?? '') ?>">
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>CATEGORY</label>
                        <select name="category" required>
                            <?php foreach (['apparel', 'premium', 'accessories'] as $category): ?>
                                <option value="<?= $category ?>" <?= ($editProduct['category'] ?? 'apparel') === $category ? 'selected' : '' ?>><?= strtoupper($category) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>PRICE</label>
                        <input type="number" name="price" step="0.01" required value="<?= h($editProduct['price'] ?? '0') ?>">
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>BRAND</label>
                        <input type="text" name="brand" value="<?= h($editProduct['brand'] ?? '') ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>TAG</label>
                        <input type="text" name="tag" value="<?= h($editProduct['tag'] ?? '') ?>">
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>RATING</label>
                        <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= h($editProduct['rating'] ?? '5') ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>REVIEWS</label>
                        <input type="number" name="reviews" min="0" step="1" value="<?= h($editProduct['reviews'] ?? '0') ?>">
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>STOCK QUANTITY</label>
                        <input type="number" name="stock_qty" min="0" step="1" value="<?= h($editProduct['stock_qty'] ?? '25') ?>">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>AVAILABLE SIZES</label>
                        <input type="text" name="available_sizes" value="<?= h($editProduct['available_sizes'] ?? DEFAULT_PRODUCT_SIZES) ?>" placeholder="S,M,L,XL">
                    </div>
                </div>

                <div class="form-group">
                    <label>ORIGIN</label>
                    <input type="text" name="origin" value="<?= h($editProduct['origin'] ?? 'India') ?>">
                </div>

                <div class="form-group">
                    <label>SHORT DESCRIPTION</label>
                    <input type="text" name="description" required value="<?= h($editProduct['description'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>LONG DESCRIPTION</label>
                    <textarea name="long_description" rows="4" required><?= h($editProduct['long_description'] ?? '') ?></textarea>
                </div>

                <div style="display:flex; gap:10px; align-items:flex-end;">
                    <div class="form-group" style="flex:1;">
                        <label>PRODUCT IMAGE</label>
                        <input type="file" name="image" accept="image/*" style="background: transparent; border: 1px dashed #555;">
                    </div>
                    <div class="form-group" style="width:120px;">
                        <label>BG COLOR</label>
                        <input type="color" name="image_bg_color" value="<?= h($editProduct['image_bg_color'] ?? '#f4f4f4') ?>" style="height: 42px;">
                    </div>
                </div>

                <label style="display:flex; align-items:center; gap:10px; font-family: var(--font-tech); margin:1rem 0;">
                    <input type="checkbox" name="is_active" value="1" <?= !isset($editProduct['is_active']) || (int) $editProduct['is_active'] === 1 ? 'checked' : '' ?>>
                    ACTIVE IN CATALOG
                </label>

                <button type="submit" class="btn-admin"><?= $editProduct ? 'SAVE CHANGES' : 'CREATE PRODUCT' ?></button>
                <?php if ($editProduct): ?>
                    <a href="admin.php" style="display:block; text-align:center; margin-top:12px; color:#888; font-family:var(--font-tech);">CANCEL EDIT</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="panel">
            <h3 class="panel-title">>> PRODUCT DATABASE</h3>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>#<?= (int) $product['id'] ?></td>
                                <td>
                                    <div style="display:flex; gap:10px;">
                                        <div style="width:52px; height:52px; background: <?= h($product['image_bg_color']) ?>; display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                            <img src="<?= h($product['image']) ?>" alt="" style="max-width:100%; max-height:100%; object-fit:contain;">
                                        </div>
                                        <div>
                                            <strong><?= h($product['name']) ?></strong><br>
                                            <span style="font-size:0.8rem; color:#888;"><?= h($product['brand']) ?> / <?= h(strtoupper($product['category'])) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td>&#8377;<?= format_money($product['price']) ?></td>
                                <td><?= (int) $product['stock_qty'] ?></td>
                                <td style="font-family:var(--font-tech); color: <?= (int) $product['is_active'] === 1 ? '#0b6e32' : '#ff1744' ?>;">
                                    <?= (int) $product['is_active'] === 1 ? 'ACTIVE' : 'ARCHIVED' ?>
                                </td>
                                <td>
                                    <a href="admin.php?edit=<?= (int) $product['id'] ?>" style="color:#00f3ff; font-family:var(--font-tech); text-decoration:underline; display:block; margin-bottom:10px;">EDIT</a>
                                    <form method="POST" onsubmit="return confirm('Delete or archive this product?');" style="margin:0;">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" style="color:#ff1744; border:none; background:none; font-family:var(--font-tech); text-decoration:underline; cursor:pointer;">DELETE / ARCHIVE</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pages > 1): ?>
                <div style="display:flex; gap:10px; margin-top:1rem; flex-wrap:wrap; font-family:var(--font-tech);">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span style="padding:8px 12px; border:1px solid #00f3ff; color:#00f3ff;"><?= $i ?></span>
                        <?php else: ?>
                            <a href="admin.php?page=<?= $i ?>&q=<?= urlencode($search) ?>" style="padding:8px 12px; border:1px solid #333; color:#888; text-decoration:none;"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel" style="margin-top:2rem;">
        <h3 class="panel-title">>> ORDER OPERATIONS</h3>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= h(order_number($order['id'])) ?><br><span style="font-size:0.8rem; color:#888;"><?= h($order['created_at']) ?></span></td>
                            <td><?= h($order['full_name']) ?><br><span style="font-size:0.8rem; color:#888;"><?= h($order['shipping_name']) ?></span></td>
                            <td>&#8377;<?= format_money($order['total_amount']) ?></td>
                            <td style="font-family:var(--font-tech);"><?= h(strtoupper($order['payment_status'])) ?></td>
                            <td style="font-family:var(--font-tech);"><?= h(strtoupper($order['status'])) ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:10px; align-items:center;">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="action" value="update_order_status">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <select name="status" style="padding:8px; background:#000; color:#fff; border:1px solid #333; font-family:var(--font-tech);">
                                        <?php foreach (['pending_payment', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'] as $status): ?>
                                            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= strtoupper($status) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn-admin" style="width:auto;">SAVE</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
