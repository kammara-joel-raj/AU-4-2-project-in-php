<?php
require_once 'includes/bootstrap.php';

require_login('login.php');

$tab = $_GET['tab'] ?? 'orders';
$user = current_user_record($pdo);
$orders = fetch_user_orders($pdo);
$wishlist = wishlist_products($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $result = update_profile_settings($pdo, $_POST);
    session_flash($result['success'] ? 'success' : 'error', $result['message']);
    safe_redirect('profile.php?tab=settings');
}

$pageTitle = 'AU // PROFILE';
include 'includes/header.php';
?>
<style>
    .profile-wrap { max-width: 1200px; margin: 3rem auto; padding: 0 20px 4rem; display: grid; grid-template-columns: 220px 1fr; gap: 2rem; }
    .profile-nav a { display: block; padding: 14px 16px; border: 1px solid #ccc; margin-bottom: 10px; text-decoration: none; font-family: var(--font-tech); color: inherit; }
    .profile-nav a.active { background: var(--au-blue); color: #fff; border-color: var(--au-blue); }
    .panel { border: var(--border-thick); background: #fff; padding: 1.5rem; }
    .order-card { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; }
    .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .settings-grid input { width: 100%; padding: 12px; border: 1px solid #ccc; font-family: var(--font-tech); }
    @media(max-width: 900px) { .profile-wrap { grid-template-columns: 1fr; } .settings-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div style="padding: 3rem 20px 0; max-width: 1200px; margin: 0 auto;">
    <h1 class="display-text" style="font-size: 2.8rem;">PROFILE</h1>
</div>

<div class="profile-wrap">
    <aside class="profile-nav">
        <a href="profile.php?tab=orders" class="<?= $tab === 'orders' ? 'active' : '' ?>">Orders</a>
        <a href="profile.php?tab=wishlist" class="<?= $tab === 'wishlist' ? 'active' : '' ?>">Wishlist</a>
        <a href="profile.php?tab=settings" class="<?= $tab === 'settings' ? 'active' : '' ?>">Settings</a>
    </aside>

    <section>
        <?php if ($tab === 'wishlist'): ?>
            <div class="panel">
                <h2 style="margin-bottom: 1rem;">Saved For Later</h2>
                <?php if (empty($wishlist)): ?>
                    <p style="font-family: var(--font-tech); color: #666;">Your wishlist is empty.</p>
                <?php else: ?>
                    <div class="product-grid" style="border: var(--border-thick);">
                        <?php foreach ($wishlist as $product): ?>
                            <div class="product-card">
                                <a href="product.php?id=<?= (int) $product['id'] ?>" style="text-decoration:none; color:inherit;">
                                    <div class="card-img" style="background: <?= h($product['image_bg_color']) ?>;">
                                        <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>">
                                    </div>
                                    <h3><?= h($product['name']) ?></h3>
                                    <p style="margin-top: 10px; font-family: var(--font-tech);">&#8377;<?= format_money($product['price']) ?></p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif ($tab === 'settings'): ?>
            <div class="panel">
                <h2 style="margin-bottom: 1rem;">Account Settings</h2>
                <form method="POST">
                    <?= csrf_input() ?>
                    <div class="settings-grid">
                        <input type="text" name="full_name" value="<?= h($user['full_name'] ?? '') ?>" placeholder="Full Name" required>
                        <input type="email" name="email" value="<?= h($user['email'] ?? '') ?>" placeholder="Email" required>
                        <input type="text" name="phone" value="<?= h($user['phone'] ?? '') ?>" placeholder="Phone">
                        <input type="password" name="current_password" placeholder="Current Password">
                        <input type="password" name="new_password" placeholder="New Password">
                    </div>
                    <button type="submit" class="btn" style="margin-top: 1rem;">SAVE CHANGES</button>
                </form>
            </div>
        <?php else: ?>
            <div class="panel">
                <h2 style="margin-bottom: 1rem;">Order History</h2>
                <?php if (empty($orders)): ?>
                    <p style="font-family: var(--font-tech); color: #666;">You have not placed any orders yet.</p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card" id="order-<?= (int) $order['id'] ?>">
                            <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                                <div>
                                    <strong><?= h(order_number($order['id'])) ?></strong><br>
                                    <span style="font-family: var(--font-tech); color: #666;"><?= h($order['created_at']) ?></span>
                                </div>
                                <div style="font-family: var(--font-tech); text-align:right;">
                                    <div>Status: <?= h(strtoupper($order['status'])) ?></div>
                                    <div>Payment: <?= h(strtoupper($order['payment_status'])) ?></div>
                                </div>
                            </div>
                            <div style="margin-top: 1rem;">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div style="display:flex; justify-content:space-between; gap:1rem; margin-bottom:8px;">
                                        <span><?= h($item['name']) ?> (<?= h($item['size']) ?> x<?= (int) $item['quantity'] ?>)</span>
                                        <span>&#8377;<?= format_money($item['unit_price'] * $item['quantity']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:1rem; font-weight:bold;">
                                <span>Total</span>
                                <span>&#8377;<?= format_money($order['total_amount']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
