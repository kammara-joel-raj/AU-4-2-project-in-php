<?php
require_once 'includes/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
$item = fetch_product_by_id($pdo, $id);

if (!$item) {
    http_response_code(404);
    exit('Product not found.');
}

$sizes = product_sizes($item);
$wishlistIds = is_logged_in() ? wishlist_ids($pdo) : [];

$pageTitle = 'AU // ' . strtoupper($item['name']);
include 'includes/header.php';
?>
<style>
    .pdp-grid { display: grid; grid-template-columns: 1fr 1fr; min-height: 80vh; }
    .pdp-images { background: <?= h($item['image_bg_color']) ?>; display: flex; align-items: center; justify-content: center; border-right: var(--border-thick); position: relative; }
    .pdp-info { padding: 4rem; display: flex; flex-direction: column; justify-content: center; }
    .origin-label { display: inline-block; border: 1px solid #ccc; padding: 5px 10px; font-family: var(--font-tech); font-size: 0.8rem; margin-top: 2rem; }
    @media(max-width: 768px) { .pdp-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="pdp-grid">
    <div class="pdp-images">
        <img src="<?= h($item['image']) ?>" onerror="this.src='uploads/products/default.jpg';" alt="<?= h($item['name']) ?>">
    </div>

    <div class="pdp-info">
        <p style="font-family: var(--font-tech); color: #666;">// <?= h(strtoupper($item['category'])) ?> // ID: 00<?= (int) $item['id'] ?></p>
        <h1 style="font-size: 3rem; line-height: 1; margin: 1rem 0;"><?= h($item['name']) ?></h1>

        <div style="font-size: 1.2rem; margin-bottom: 2rem;">
            <span class="stars" style="color: var(--au-gold);">
                <?php
                $stars = round((float) $item['rating']);
                echo str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                ?>
            </span>
            <span><?= h($item['rating']) ?> / 5.0</span>
        </div>

        <h2 style="font-size: 2rem; color: var(--au-blue); margin-bottom: 1rem;">&#8377;<?= format_money($item['price']) ?></h2>
        <p style="font-family: var(--font-tech); color: <?= (int) $item['stock_qty'] > 0 ? '#0b6e32' : '#8c1111' ?>; margin-bottom: 1rem;">
            <?= (int) $item['stock_qty'] > 0 ? 'IN STOCK: ' . (int) $item['stock_qty'] . ' UNITS' : 'CURRENTLY OUT OF STOCK' ?>
        </p>

        <p style="line-height: 1.8; margin-bottom: 2rem; max-width: 540px;"><?= h($item['long_description']) ?></p>

        <form method="POST" action="cart_action.php" style="display: flex; flex-direction: column; gap: 1rem;">
            <?= csrf_input() ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
            <input type="hidden" name="redirect" value="product.php?id=<?= (int) $item['id'] ?>">

            <div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
                <select name="size" style="padding: 15px; border: var(--border-thick); font-family: var(--font-tech); min-width: 140px;">
                    <?php foreach ($sizes as $size): ?>
                        <option value="<?= h($size) ?>">SIZE: <?= h($size) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="quantity" value="1" min="1" max="<?= max(1, (int) $item['stock_qty']) ?>" style="padding: 15px; border: var(--border-thick); width: 90px; text-align: center;">
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button class="btn btn-add-cart" type="submit" style="background: var(--au-blue); color: var(--au-gold); min-width: 220px;" <?= (int) $item['stock_qty'] <= 0 ? 'disabled' : '' ?>>
                    ADD TO CART
                </button>
            </div>
        </form>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem;">
            <a href="tryon.php?product_id=<?= (int) $item['id'] ?>" class="btn" style="background: #000; color: #00f3ff; border: 1px solid #00f3ff; min-width: 220px; text-align: center; text-decoration: none;">
                OPEN VIRTUAL FIT LAB
            </a>
            <?php if (is_logged_in()): ?>
                <form method="POST" action="wishlist_action.php" style="margin: 0;">
                    <?= csrf_input() ?>
                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                    <input type="hidden" name="action" value="<?= in_array((int) $item['id'], $wishlistIds, true) ? 'remove' : 'add' ?>">
                    <input type="hidden" name="redirect" value="product.php?id=<?= (int) $item['id'] ?>">
                    <button type="submit" class="btn" style="min-width: 200px;">
                        <?= in_array((int) $item['id'], $wishlistIds, true) ? 'REMOVE FROM WISHLIST' : 'SAVE FOR LATER' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="origin-label">COUNTRY OF ORIGIN: <?= h(strtoupper($item['origin'])) ?></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
