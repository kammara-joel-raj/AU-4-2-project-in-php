<?php
require_once 'includes/bootstrap.php';

$pageTitle = 'AU // SHOP ARCHIVES';

$current_category = $_GET['category'] ?? 'all';
$max_price = isset($_GET['max_price']) ? (int) $_GET['max_price'] : 5000;
$min_rating = isset($_GET['min_rating']) ? (float) $_GET['min_rating'] : 0;
$sort = $_GET['sort'] ?? 'latest';
$page = max(1, (int) ($_GET['page'] ?? 1));

$catalog = fetch_products_paginated($pdo, [
    'category' => $current_category,
    'max_price' => $max_price,
    'min_rating' => $min_rating,
    'sort' => $sort,
    'page' => $page,
    'per_page' => SHOP_PRODUCTS_PER_PAGE,
]);

$wishlistIds = is_logged_in() ? wishlist_ids($pdo) : [];

function shop_query(array $overrides = [])
{
    $params = array_merge($_GET, $overrides);
    unset($params['page']);
    if (isset($overrides['page'])) {
        $params['page'] = $overrides['page'];
    }

    return http_build_query($params);
}

include 'includes/header.php';
?>

<style>
    .shop-layout { display: grid; grid-template-columns: 280px 1fr; gap: 2rem; padding: 3rem 4rem; min-height: 80vh; background: var(--paper-white); }
    .sidebar { border-right: var(--border-thick); padding-right: 2rem; }
    .product-grid-shop { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 2rem; align-content: start; }
    .product-card-shop { border: 1px solid #ccc; padding: 1rem; background: #fff; transition: 0.3s; display: flex; flex-direction: column; position: relative; }
    .product-card-shop:hover { border-color: var(--au-blue); box-shadow: 6px 6px 0px var(--au-blue); transform: translateY(-3px); }
    .product-img { width: 100%; height: 250px; object-fit: contain; background: var(--off-white); margin-bottom: 1rem; }
    .filter-section { margin-bottom: 2.5rem; }
    .filter-section h4 { font-family: var(--font-varsity); margin-bottom: 1rem; font-size: 1.2rem; color: #222; }
    .cat-list { list-style: none; padding: 0; }
    .cat-list li { margin-bottom: 0.5rem; }
    .cat-list a { text-decoration: none; color: #555; font-family: var(--font-tech); display: block; padding: 8px 12px; border: 1px solid transparent; transition: 0.2s; }
    .cat-list a:hover, .cat-list a.active { background: var(--au-blue); color: var(--paper-white); border-color: var(--au-blue); }
    .range-slider { width: 100%; accent-color: var(--au-blue); cursor: pointer; }
    .price-labels { display: flex; justify-content: space-between; font-family: var(--font-tech); font-size: 0.85rem; margin-top: 10px; color: #555; }
    .sort-select { width: 100%; padding: 10px; border: 1px solid #ccc; font-family: var(--font-tech); background: #fff; }
    .wishlist-btn { background: transparent; border: 1px solid #ccc; padding: 8px; font-family: var(--font-tech); cursor: pointer; }
    .wishlist-btn.active { border-color: var(--au-blue); color: var(--au-blue); }
    .pagination { display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-top: 2rem; font-family: var(--font-tech); }
    .pagination a, .pagination span { padding: 10px 14px; border: 1px solid #ccc; text-decoration: none; color: inherit; }
    .pagination .current { background: var(--au-blue); color: #fff; border-color: var(--au-blue); }
    @media (max-width: 768px) {
        .shop-layout { grid-template-columns: 1fr; padding: 2rem; }
        .sidebar { border-right: none; border-bottom: var(--border-thick); padding-right: 0; padding-bottom: 2rem; }
    }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="shop-layout">
    <aside class="sidebar">
        <div class="filter-section">
            <h4>CATEGORIES</h4>
            <ul class="cat-list">
                <li><a href="?<?= shop_query(['category' => 'all', 'page' => 1]) ?>" class="<?= $current_category === 'all' ? 'active' : '' ?>">All Items</a></li>
                <li><a href="?<?= shop_query(['category' => 'apparel', 'page' => 1]) ?>" class="<?= $current_category === 'apparel' ? 'active' : '' ?>">Apparel</a></li>
                <li><a href="?<?= shop_query(['category' => 'premium', 'page' => 1]) ?>" class="<?= $current_category === 'premium' ? 'active' : '' ?>">Premium</a></li>
                <li><a href="?<?= shop_query(['category' => 'accessories', 'page' => 1]) ?>" class="<?= $current_category === 'accessories' ? 'active' : '' ?>">Accessories</a></li>
            </ul>
        </div>

        <form action="shop.php" method="GET" id="filterForm">
            <input type="hidden" name="category" value="<?= h($current_category) ?>">

            <div class="filter-section">
                <h4>PRICE RANGE</h4>
                <input type="range" name="max_price" id="priceSlider" class="range-slider" min="100" max="10000" step="100" value="<?= $max_price ?>">
                <div class="price-labels">
                    <span>&#8377;100</span>
                    <span style="font-weight: bold; color: var(--au-blue);">Max: &#8377;<span id="priceDisplay"><?= $max_price ?></span></span>
                </div>
            </div>

            <div class="filter-section">
                <h4>RATING</h4>
                <label style="font-family: var(--font-tech); font-size: 0.9rem; display: flex; align-items: center; gap: 8px; cursor: pointer; color: #555;">
                    <input type="checkbox" name="min_rating" value="4.0" <?= $min_rating == 4.0 ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
                    <span style="color: #FFD700; letter-spacing: 2px;">★★★★☆</span> &amp; up
                </label>
            </div>

            <div class="filter-section">
                <h4>SORT</h4>
                <select name="sort" class="sort-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Latest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Rating</option>
                </select>
            </div>
        </form>
    </aside>

    <main>
        <?php if (empty($catalog['items'])): ?>
            <div style="padding: 4rem; text-align: center; border: 2px dashed #ccc; background: var(--off-white);">
                <p style="font-family: var(--font-tech); color: #666; font-size: 1.2rem;">// NO ASSETS FOUND MATCHING YOUR FILTERS //</p>
                <a href="shop.php" class="btn" style="margin-top: 1.5rem; display: inline-block;">RESET ARCHIVES</a>
            </div>
        <?php else: ?>
            <div class="product-grid-shop">
                <?php foreach ($catalog['items'] as $item): ?>
                    <?php
                    $defaultSize = product_sizes($item)[0] ?? 'M';
                    $buttonStyle = 'flex: 1; border: 1px solid var(--au-blue);';
                    if ((int) $item['stock_qty'] <= 0) {
                        $buttonStyle .= ' opacity: 0.5;';
                    }
                    ?>
                    <div class="product-card-shop">
                        <?php if (!empty($item['tag'])): ?>
                            <span class="badge" style="position: absolute; top: 10px; right: 10px; background: var(--au-blue); color: #fff; padding: 4px 8px; font-family: var(--font-tech); font-size: 0.7rem; z-index: 2;"><?= h($item['tag']) ?></span>
                        <?php endif; ?>

                        <?php if ((int) $item['stock_qty'] <= 0): ?>
                            <span class="badge" style="left: 10px; right: auto; background: #111;">OUT OF STOCK</span>
                        <?php endif; ?>

                        <a href="product.php?id=<?= (int) $item['id'] ?>" style="text-decoration: none; color: inherit; flex-grow: 1;">
                            <img src="<?= h($item['image']) ?>" alt="<?= h($item['name']) ?>" class="product-img">

                            <h3 style="font-family: var(--font-varsity); font-size: 1.1rem; margin-bottom: 5px;"><?= h($item['name']) ?></h3>

                            <div style="color: #FFD700; font-size: 0.8rem; margin-bottom: 10px; letter-spacing: 2px;">
                                <?php
                                $stars = round((float) $item['rating']);
                                echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                                ?>
                                <span style="color: #888; letter-spacing: normal;">(<?= (int) $item['reviews'] ?>)</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; font-family: var(--font-tech); margin-bottom: 15px; gap: 1rem;">
                                <span style="color: #666; font-size: 0.85rem;"><?= h($item['brand']) ?></span>
                                <span style="font-weight: bold; color: var(--au-blue); font-size: 1.1rem;">&#8377;<?= format_money($item['price']) ?></span>
                            </div>
                        </a>

                        <div style="display: flex; gap: 10px; margin-top: 12px;">
                            <button
                                class="btn"
                                style="<?= $buttonStyle ?>"
                                onclick="addToCart(<?= (int) $item['id'] ?>, '<?= h($defaultSize) ?>', this)"
                                <?= (int) $item['stock_qty'] <= 0 ? 'disabled' : '' ?>
                            >
                                ADD TO CART
                            </button>

                            <?php if (is_logged_in()): ?>
                                <form method="POST" action="wishlist_action.php" style="margin: 0;">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="action" value="<?= in_array((int) $item['id'], $wishlistIds, true) ? 'remove' : 'add' ?>">
                                    <button type="submit" class="wishlist-btn <?= in_array((int) $item['id'], $wishlistIds, true) ? 'active' : '' ?>">
                                        <?= in_array((int) $item['id'], $wishlistIds, true) ? 'SAVED' : 'SAVE' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($catalog['pages'] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $catalog['pages']; $i++): ?>
                        <?php if ($i === $catalog['page']): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?<?= shop_query(['page' => $i]) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('priceSlider');
        const display = document.getElementById('priceDisplay');
        const form = document.getElementById('filterForm');

        slider.addEventListener('input', function() {
            display.textContent = this.value;
        });

        slider.addEventListener('change', function() {
            form.submit();
        });
    });

    function addToCart(productId, size, btnElement) {
        if (btnElement.disabled) {
            return;
        }

        btnElement.disabled = true;
        auPost('cart_action.php', {
            action: 'add',
            product_id: productId,
            size: size,
            quantity: 1
        }).then(data => {
            const originalText = btnElement.innerText;
            btnElement.innerText = 'ADDED [OK]';
            btnElement.style.background = 'var(--au-gold)';
            btnElement.style.color = 'var(--au-blue)';

            document.querySelectorAll('a[href="cart.php"]').forEach(el => {
                if (el.innerText.includes('Cart')) {
                    el.innerText = `Cart (${data.count})`;
                }
            });

            setTimeout(() => {
                btnElement.innerText = originalText;
                btnElement.style.background = 'transparent';
                btnElement.style.color = 'inherit';
                btnElement.disabled = false;
            }, 1600);
        }).catch(err => {
            alert(err.message || 'Unable to add the item to cart.');
            btnElement.disabled = false;
        });
    }
</script>

<?php include 'includes/footer.php'; ?>
