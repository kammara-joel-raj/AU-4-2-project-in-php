<?php
require_once 'includes/bootstrap.php';

$featuredProducts = fetch_featured_products($pdo, 4);
$heroProduct = $featuredProducts[0] ?? null;

$pageTitle = 'AU HERITAGE // HOME';
include 'includes/header.php';
?>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<header class="grid-2" style="min-height: 90vh; border-bottom: var(--border-thick);">
    <div style="padding: 4rem; display: flex; flex-direction: column; justify-content: center; border-right: var(--border-thick);">
        <div>
            <p style="font-family: var(--font-tech); color: #666; margin-bottom: 1rem; letter-spacing: 2px;">SERIES 004 -- "WALTAIR"</p>
            <h1 class="display-text">
                LOCAL<br>PRIDE<br>
                <span class="glitch" data-text="GLOBAL" style="display: inline-block;">GLOBAL</span><br>
                REACH.
            </h1>
            <p style="max-width: 450px; margin-top: 2rem; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.6;">
                Andhra University merchandise, now with persistent carts, smarter product discovery, and a dual-path Smart Fit Lab.
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="shop.php"><button class="btn">View Collection</button></a>
                <a href="tryon.php"><button class="btn" style="background: var(--au-blue); color: var(--au-gold);">Open Fit Lab</button></a>
            </div>
        </div>
    </div>
    <div style="background: url('https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=2070&auto=format&fit=crop') center/cover; position: relative; min-height: 400px;">
        <?php if ($heroProduct): ?>
            <div style="position: absolute; bottom: 2rem; left: 2rem; background: white; padding: 1rem; border: 2px solid black; max-width: 320px;">
                <span style="font-family: var(--font-varsity); color: var(--au-blue); display: block;">FEATURED:</span>
                <span style="font-family: var(--font-tech); display: block; margin-top: 6px;"><?= h($heroProduct['name']) ?></span>
                <span style="font-family: var(--font-tech); color: #666; display: block; margin-top: 8px;">&#8377;<?= format_money($heroProduct['price']) ?></span>
            </div>
        <?php endif; ?>
    </div>
</header>

<section style="padding: 4rem 3%; border-bottom: var(--border-thick);">
    <h2 style="font-size: 2rem; margin-bottom: 2rem; text-align: center;">BROWSE ARCHIVES</h2>
    <div class="grid-2" style="gap: 2rem;">
        <div style="background: #f4f4f4; padding: 2rem; text-align: center; border: 1px solid #ddd; cursor: pointer;" onclick="window.location.href='shop.php?category=apparel'">
            <h3 style="font-size: 1.5rem;">APPAREL</h3>
            <p>Hoodies, Tees, Jackets</p>
        </div>
        <div style="background: #eee; padding: 2rem; text-align: center; border: 1px solid #ddd; cursor: pointer;" onclick="window.location.href='shop.php?category=premium'">
            <h3 style="font-size: 1.5rem;">PREMIUM</h3>
            <p>Statement pieces and limited releases</p>
        </div>
    </div>
</section>

<section style="padding: 6rem 3%;">
    <h2 style="font-size: 3rem; margin-bottom: 3rem; text-align: center;">TRENDING NOW</h2>

    <?php if (empty($featuredProducts)): ?>
        <div style="border: var(--border-thick); padding: 3rem; text-align: center;">
            <p style="font-family: var(--font-tech); color: #666;">No active products are available right now.</p>
        </div>
    <?php else: ?>
        <div class="product-grid" style="border: var(--border-thick);">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?= (int) $product['id'] ?>'" style="cursor: pointer;">
                    <?php if (!empty($product['tag'])): ?>
                        <span class="badge"><?= h($product['tag']) ?></span>
                    <?php endif; ?>

                    <div class="card-img" style="background: <?= h($product['image_bg_color']) ?>;">
                        <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>">
                    </div>
                    <div>
                        <h3 style="font-size: 1.5rem;"><?= h($product['name']) ?></h3>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px; font-family: var(--font-tech); gap: 1rem;">
                            <span><?= h($product['description']) ?></span>
                            <span>&#8377;<?= format_money($product['price']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
