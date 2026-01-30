<?php 
$pageTitle = "AU ARCHIVES // SUPPLY";
include 'includes/header.php'; 
include 'data/products.php';

// Basic PHP Filtering Simulation
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<style>
    .shop-layout { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
    .sidebar { padding: 2rem; border-right: var(--border-thick); background: var(--paper-white); }
    .filter-group { margin-bottom: 2rem; }
    .filter-title { font-family: var(--font-tech); font-weight: bold; margin-bottom: 1rem; display: block; }
    .filter-item { display: block; padding: 5px 0; font-family: var(--font-street); font-size: 0.9rem; cursor: pointer; }
    .filter-item:hover { color: var(--au-blue); text-decoration: underline; }
    
    /* Rating Stars */
    .stars { color: var(--au-gold); letter-spacing: 2px; }
    
    @media (max-width: 768px) { .shop-layout { grid-template-columns: 1fr; } .sidebar { display: none; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="shop-layout">
    <aside class="sidebar">
        <div class="filter-group">
            <span class="filter-title">// CATEGORIES</span>
            <a href="shop.php?category=all" class="filter-item">[All Items]</a>
            <a href="shop.php?category=apparel" class="filter-item">[Apparel]</a>
            <a href="shop.php?category=accessories" class="filter-item">[Accessories]</a>
        </div>
        
        <div class="filter-group">
            <span class="filter-title">// PRICE RANGE</span>
            <input type="range" min="100" max="5000" style="width: 100%;">
            <div style="display: flex; justify-content: space-between; font-family: var(--font-tech); font-size: 0.8rem;">
                <span>₹100</span><span>₹5000</span>
            </div>
        </div>

        <div class="filter-group">
            <span class="filter-title">// RATING</span>
            <label class="filter-item"><input type="checkbox"> ★★★★☆ & Up</label>
            <label class="filter-item"><input type="checkbox"> ★★★☆☆ & Up</label>
        </div>
    </aside>

    <main class="product-grid" style="border-left: none; border-top: none;">
        <?php foreach ($products as $product): ?>
            <!-- Click card to go to PDP -->
            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'" style="cursor: pointer;">
                <?php if($product['tag']): ?>
                    <span class="badge"><?php echo $product['tag']; ?></span>
                <?php endif; ?>
                
                <!-- FIXED: Updated variable names to match SQL database -->
                <div class="card-img" style="background: <?php echo $product['image_bg_color']; ?>; color: <?php echo ($product['image_bg_color'] == '#222' || $product['image_bg_color'] == '#002147') ? 'white' : 'black'; ?>;">
                    <?php echo $product['image_text']; ?>
                </div>
                
                <div>
                    <h3 style="font-size: 1.2rem;"><?php echo $product['name']; ?></h3>
                    
                    <!-- Rating -->
                    <div style="font-size: 0.8rem; margin: 5px 0;">
                        <span class="stars"><?php echo str_repeat("★", round($product['rating'])); ?></span> 
                        <span style="color: #666;">(<?php echo $product['reviews']; ?>)</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech);">
                        <span><?php echo $product['brand']; ?></span>
                        <span style="font-weight: bold;">₹<?php echo $product['price']; ?></span>
                    </div>

                    <!-- ADD TO CART BUTTON -->
                    <a href="cart_action.php?action=add&id=<?php echo $product['id']; ?>" onclick="event.stopPropagation();">
                        <button class="btn" style="width: 100%; margin-top: 15px;">ADD TO CART</button>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
</div>

<?php include 'includes/footer.php'; ?>