<?php
// Calculate total items in cart
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>

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
    <div class="logo glitch" data-text="AU.ARCHIVES™">AU.ARCHIVES™</div>
    
    <div class="nav-search" style="flex-grow: 1; max-width: 400px; margin: 0 2rem;">
        <form action="search.php" method="GET" style="display: flex;">
            <input type="text" name="q" placeholder="SEARCH ARCHIVES..." style="width: 100%; padding: 8px; border: 1px solid #ccc; font-family: var(--font-tech); background: transparent;">
            <button type="submit" class="btn" style="padding: 8px 15px; border-left: none;">GO</button>
        </form>
    </div>

    <ul class="nav-links">
        <li><a href="shop.php">Shop</a></li>
        <li><a href="tryon.php">Lab</a></li>
        
        <!-- DYNAMIC AUTH LINKS -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <li><a href="logout.php" style="color: red;">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>

        <li><a href="cart.php">Cart (<?php echo $cart_count; ?>)</a></li>
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