<?php 
$pageTitle = "AU ARCHIVES // SUPPLY";
include 'includes/header.php'; 
include 'data/products.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<style>
    /* ===== LAYOUT ===== */
    .shop-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        min-height: 100vh;
    }

    /* ===== MODERNIZED SIDEBAR FILTERS ===== */
    .sidebar {
        padding: 2rem;
        border-right: var(--border-thick);
        background: #fafafa;
    }
    
    .filter-section {
        margin-bottom: 2rem;
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid #eee;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .filter-title {
        font-family: var(--font-street);
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        text-transform: uppercase;
        color: var(--au-blue);
        border-bottom: 2px solid #f4f4f4;
        padding-bottom: 10px;
    }

    .category-list { display: flex; flex-direction: column; gap: 8px; }

    .category-btn {
        display: block;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        color: #555;
        background: #f9f9f9;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .category-btn:hover, .category-btn.active {
        background: var(--au-blue);
        color: var(--au-gold);
        border-color: var(--au-blue);
        transform: translateX(4px);
    }

    .price-slider-container { padding: 10px 0; }
    
    .custom-range {
        width: 100%; appearance: none; height: 6px; border-radius: 3px; background: #e0e0e0; outline: none; margin-bottom: 10px;
    }
    .custom-range::-webkit-slider-thumb {
        appearance: none; width: 20px; height: 20px; border-radius: 50%; background: var(--au-blue); cursor: pointer; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .price-labels { display: flex; justify-content: space-between; font-family: var(--font-tech); font-size: 0.9rem; font-weight: bold; color: #444; }
    
    .rating-label { display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 8px 0; font-size: 0.95rem; color: #555; }
    .rating-label input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; accent-color: var(--au-blue); }

    .mobile-filter-toggle { 
        display: none; width: 100%; padding: 15px; background: var(--au-blue); color: var(--au-gold); 
        font-weight: bold; text-align: center; font-family: var(--font-tech); border: none; cursor: pointer; 
        text-transform: uppercase; letter-spacing: 1px; 
    }

    .stars { color: var(--au-gold); letter-spacing: 2px; }

    /* ===== PRODUCT GRID ===== */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 300px));
        justify-content: center;
        gap: 15px;
        padding: 20px;
    }

    .product-card {
        background: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        transition: 0.3s;
        border-radius: 10px;
        position: relative;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .card-img {
        width: 100%;
        height: 220px;
        background: #f5f5f5;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .card-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .product-card h3 { font-size: 1.2rem; margin-top: 10px; }
    .badge { position: absolute; top: 25px; right: 25px; background: var(--au-blue); color: #fff; padding: 5px 10px; font-family: var(--font-tech); font-size: 0.7rem; transform: rotate(5deg); z-index: 10;}

    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; }
        .mobile-filter-toggle { display: block; }
        .sidebar { display: none; padding: 1.5rem; }
        .sidebar.active { display: block; background: #fafafa; border-bottom: var(--border-thick); }
        .product-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); padding: 15px; }
    }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Mobile Sidebar Toggle -->
<button class="mobile-filter-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
    <span style="margin-right: 8px;">≡</span> Toggle Filters
</button>

<div class="shop-layout">
    <!-- SIDEBAR -->
    <aside class="sidebar">
        
        <div class="filter-section">
            <span class="filter-title">Categories</span>
            <div class="category-list">
                <a href="shop.php?category=all" class="category-btn <?php echo $category == 'all' ? 'active' : ''; ?>">All Items</a>
                <a href="shop.php?category=apparel" class="category-btn <?php echo $category == 'apparel' ? 'active' : ''; ?>">Apparel</a>
                <a href="shop.php?category=accessories" class="category-btn <?php echo $category == 'accessories' ? 'active' : ''; ?>">Accessories</a>
            </div>
        </div>

        <div class="filter-section">
            <span class="filter-title">Price Range</span>
            <div class="price-slider-container">
                <input type="range" min="100" max="5000" value="5000" class="custom-range" id="priceRange" oninput="document.getElementById('maxPrice').innerText = '₹' + this.value">
                <div class="price-labels">
                    <span>₹100</span>
                    <span id="maxPrice">₹5000</span>
                </div>
            </div>
        </div>

        <div class="filter-section">
            <span class="filter-title">Rating</span>
            <label class="rating-label">
                <input type="checkbox" checked> <span class="stars">★★★★☆</span> & Up
            </label>
        </div>

    </aside>

    <!-- PRODUCTS -->
    <main class="product-grid">

        <?php foreach ($products as $product): ?>
            
            <?php 
                // Filter by category if selected
                if ($category != 'all' && strtolower($product['category']) != strtolower($category)) {
                    continue; 
                } 
            ?>

            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'" style="cursor: pointer;">

                <?php if(!empty($product['tag'])): ?>
                    <span class="badge"><?php echo $product['tag']; ?></span>
                <?php endif; ?>

                <div class="card-img">
                    <img src="<?php echo !empty($product['image']) 
                        ? str_replace('\\', '/', $product['image']) 
                        : 'uploads/products/default.jpg'; ?>" 
                        alt="<?php echo $product['name']; ?>">
                </div>

                <div>
                    <h3 style="font-size: 1.2rem;">
                        <?php echo $product['name']; ?>
                    </h3>

                    <div style="font-size: 0.8rem; margin: 5px 0;">
                        <span class="stars">
                            <?php echo str_repeat("★", round($product['rating'])); ?>
                        </span>
                        <span style="color: #666;">
                            (<?php echo $product['reviews']; ?>)
                        </span>
                    </div>

                    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech);">
                        <span><?php echo $product['brand']; ?></span>
                        <span style="font-weight: bold;">
                            ₹<?php echo $product['price']; ?>
                        </span>
                    </div>

                    <a href="cart_action.php?action=add&id=<?php echo $product['id']; ?>" onclick="event.stopPropagation();">
                        <button class="btn" style="width: 100%; margin-top: 15px;">
                            ADD TO CART
                        </button>
                    </a>
                </div>

            </div>

        <?php endforeach; ?>

    </main>
</div>

<?php include 'includes/footer.php'; ?>