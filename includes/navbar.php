<!-- Custom Cursor Elements -->
<div class="cursor-dot"></div>
<div class="cursor-outline"></div>

<!-- Marquee -->
<div class="marquee-container">
    <div class="marquee-content">
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // WORLDWIDE SHIPPING //</span>
        <span>// OFFICIAL MERCHANDISE // ANDHRA UNIVERSITY EST. 1926 // WORLDWIDE SHIPPING //</span>
    </div>
</div>

<nav class="navbar">
    <div class="logo glitch" data-text="AU.ARCHIVES™">AU.ARCHIVES™</div>
    
    <!-- SEARCH BAR ADDITION -->
    <div class="nav-search" style="flex-grow: 1; max-width: 400px; margin: 0 2rem;">
        <form action="search.php" method="GET" style="display: flex;">
            <input type="text" name="q" placeholder="SEARCH ARCHIVES..." style="width: 100%; padding: 8px; border: 1px solid #ccc; font-family: var(--font-tech); background: transparent;">
            <button type="submit" class="btn" style="padding: 8px 15px; border-left: none;">GO</button>
        </form>
    </div>

    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php">Cart (0)</a></li> <!-- Direct link to Cart Page -->
    </ul>
</nav>

<!-- Cart Drawer (Still here for quick view, but Cart Page is main) -->
<div class="cart-overlay" id="cart-overlay"></div>
<div class="cart-drawer" id="cart-drawer">
    <div class="cart-header">
        <h3>// QUICK_CART</h3>
        <button class="close-cart" onclick="toggleCart()">[X]</button>
    </div>
    <div class="cart-items">
        <div class="cart-item">
            <div style="width: 60px; height: 60px; background: #222;"></div>
            <div>
                <p style="font-family: var(--font-tech); font-size: 0.8rem;">AU_HOODIE_NAVY</p>
                <p>₹1,499</p>
            </div>
        </div>
    </div>
    <div class="cart-footer">
        <a href="cart.php"><button class="btn" style="width: 100%;">VIEW FULL CART</button></a>
    </div>
</div>