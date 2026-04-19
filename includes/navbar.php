<?php
require_once __DIR__ . '/bootstrap.php';

$cart_count = current_cart_count($pdo);
$current_page = basename($_SERVER['PHP_SELF']);
$active_style = 'border-bottom: 2px solid var(--au-blue);';
$flash = pull_flash();
?>

<style>
    .nav-links { gap: 1.25rem !important; flex-wrap: wrap; justify-content: flex-end; }
    .flash-banner { padding: 14px 3%; font-family: var(--font-tech); border-bottom: var(--border-thick); }
    .flash-success { background: rgba(0, 110, 50, 0.12); color: #0b6e32; }
    .flash-error { background: rgba(160, 0, 0, 0.08); color: #8c1111; }
</style>

<div class="cursor-dot"></div>
<div class="cursor-outline"></div>

<?php if ($flash): ?>
    <div class="flash-banner flash-<?= h($flash['type']) ?>">
        <?= h($flash['message']) ?>
    </div>
<?php endif; ?>

<div class="marquee-container">
    <div class="marquee-content">
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // PRODUCT-LEVEL STOCK LIVE //</span>
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // SMART FIT LAB READY //</span>
    </div>
</div>

<nav class="navbar">
    <a href="index.php" style="text-decoration: none; color: inherit;">
        <div class="logo glitch" data-text="ANDHRA UNIVERSITY">ANDHRA UNIVERSITY</div>
    </a>

    <div class="nav-search" style="flex-grow: 1; max-width: 360px; margin: 0 1.5rem;">
        <form action="search.php" method="GET" style="display: flex;">
            <input
                type="text"
                name="q"
                value="<?= h($_GET['q'] ?? '') ?>"
                placeholder="SEARCH ARCHIVES..."
                style="width: 100%; padding: 8px; border: 1px solid #ccc; font-family: var(--font-tech); background: transparent;"
            >
            <button type="submit" class="btn" style="padding: 8px 15px; border-left: none;">GO</button>
        </form>
    </div>

    <ul class="nav-links">
        <li><a href="shop.php" <?= $current_page === 'shop.php' ? 'style="' . $active_style . '"' : '' ?>>Shop</a></li>
        <li><a href="tryon.php" <?= $current_page === 'tryon.php' ? 'style="' . $active_style . '"' : '' ?>>Lab</a></li>
        <li><a href="about.php" <?= $current_page === 'about.php' ? 'style="' . $active_style . '"' : '' ?>>About</a></li>
        <li><a href="locator.php" <?= $current_page === 'locator.php' ? 'style="' . $active_style . '"' : '' ?>>Locator</a></li>
        <li><a href="blog.php" <?= $current_page === 'blog.php' ? 'style="' . $active_style . '"' : '' ?>>Blog</a></li>
        <li><a href="faq.php" <?= $current_page === 'faq.php' ? 'style="' . $active_style . '"' : '' ?>>FAQ</a></li>

        <?php if (is_logged_in()): ?>
            <li><a href="profile.php" <?= $current_page === 'profile.php' ? 'style="' . $active_style . '"' : '' ?>>Profile</a></li>
            <?php if (is_admin_user()): ?>
                <li><a href="admin.php" <?= $current_page === 'admin.php' ? 'style="' . $active_style . '"' : '' ?>>Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php" style="color: #a40000;">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" <?= ($current_page === 'login.php' || $current_page === 'register.php') ? 'style="' . $active_style . '"' : '' ?>>Login</a></li>
        <?php endif; ?>

        <li><a href="cart.php" <?= ($current_page === 'cart.php' || $current_page === 'checkout.php') ? 'style="' . $active_style . '"' : '' ?>>Cart (<?= $cart_count ?>)</a></li>
    </ul>
</nav>

<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-drawer" id="cart-drawer">
    <div class="cart-header">
        <h3>// QUICK_CART</h3>
        <button class="close-cart" onclick="toggleCart()">[X]</button>
    </div>

    <div class="cart-items">
        <?php if ($cart_count > 0): ?>
            <p style="padding: 20px; font-family: var(--font-tech);">YOU HAVE <?= $cart_count ?> ITEM(S) READY FOR CHECKOUT.</p>
            <div style="padding: 0 20px;">
                <a href="cart.php" style="text-decoration: underline; color: var(--au-blue);">VIEW DETAILS</a>
            </div>
        <?php else: ?>
            <p style="padding: 20px; font-family: var(--font-tech); color: #666;">CART IS EMPTY</p>
        <?php endif; ?>
    </div>

    <div class="cart-footer">
        <a href="cart.php"><button class="btn" style="width: 100%;">VIEW FULL CART</button></a>
    </div>
</div>
