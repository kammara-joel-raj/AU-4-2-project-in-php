<?php
// Calculate total items in cart
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}

// Get the current page to highlight the active link
$current_page = basename($_SERVER['PHP_SELF']);
$active_style = 'border-bottom: 2px solid var(--au-blue);';
?>

<style>
    /* Reduce gap slightly to fit the new navigation items beautifully */
    .nav-links { gap: 1.5rem !important; }
</style>

<!-- Custom Cursor Elements -->
<div class="cursor-dot"></div>
<div class="cursor-outline"></div>

<div class="marquee-container">
    <div class="marquee-content">
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // WORLDWIDE SHIPPING //</span>
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // WORLDWIDE SHIPPING //</span>
    </div>
</div>

<nav class="navbar">
    <!-- Updated Logo: Clickable and Redirects to Home -->
    <a href="index.php" style="text-decoration: none; color: inherit;">
        <div class="logo glitch" data-text="ANDHRA UNIVERSITY">ANDHRA UNIVERSITY</div>
    </a>
    
    <div class="nav-search" style="flex-grow: 1; max-width: 400px; margin: 0 2rem;">
        <form action="search.php" method="GET" style="display: flex;">
            <input type="text" name="q" placeholder="SEARCH ARCHIVES..." style="width: 100%; padding: 8px; border: 1px solid #ccc; font-family: var(--font-tech); background: transparent;">
            <button type="submit" class="btn" style="padding: 8px 15px; border-left: none;">GO</button>
        </form>
    </div>

    <ul class="nav-links">
        <li><a href="shop.php" <?= ($current_page == 'shop.php') ? 'style="'.$active_style.'"' : '' ?>>Shop</a></li>
        <li><a href="tryon.php" <?= ($current_page == 'tryon.php') ? 'style="'.$active_style.'"' : '' ?>>Lab</a></li>
        <li><a href="about.php" <?= ($current_page == 'about.php') ? 'style="'.$active_style.'"' : '' ?>>About</a></li>
        <li><a href="locator.php" <?= ($current_page == 'locator.php') ? 'style="'.$active_style.'"' : '' ?>>Locator</a></li>
        <li><a href="blog.php" <?= ($current_page == 'blog.php') ? 'style="'.$active_style.'"' : '' ?>>Blog</a></li>
        <li><a href="faq.php" <?= ($current_page == 'faq.php') ? 'style="'.$active_style.'"' : '' ?>>FAQ</a></li>
        
        <!-- DYNAMIC AUTH LINKS -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php" style="color: red;">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" <?= ($current_page == 'login.php' || $current_page == 'register.php') ? 'style="'.$active_style.'"' : '' ?>>Login</a></li>
        <?php endif; ?>

        <li><a href="cart.php" <?= ($current_page == 'cart.php' || $current_page == 'checkout.php') ? 'style="'.$active_style.'"' : '' ?>>Cart (<?php echo $cart_count; ?>)</a></li>
    </ul>
</nav>

<!-- Cart Drawer -->
<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-drawer" id="cart-drawer">
    <div class="cart-header">
        <h3>// QUICK_CART</h3>
        <button class="close-cart" onclick="toggleCart()">[X]</button>
    </div>
    
    <div class="cart-items">
        <?php if($cart_count > 0): ?>
            <p style="padding: 20px; font-family: var(--font-tech);">YOU HAVE <?php echo $cart_count; ?> ITEM(S) IN CART</p>
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