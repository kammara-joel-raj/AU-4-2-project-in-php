<?php
require_once 'includes/bootstrap.php';

$pageTitle = 'AU // CART';
$cart = cart_items_with_products($pdo);

include 'includes/header.php';
?>
<style>
    .cart-layout { max-width: 1200px; margin: 4rem auto; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 4rem; }
    .cart-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .cart-table th { text-align: left; padding: 1rem; border-bottom: 2px solid #000; font-family: var(--font-tech); }
    .cart-table td { padding: 1.25rem 1rem; border-bottom: 1px solid #ccc; vertical-align: middle; }
    .summary-box { background: var(--off-white); padding: 2rem; border: var(--border-thick); }
    .remove-link { color: #a40000; font-size: 0.8rem; text-decoration: underline; cursor: pointer; background: none; border: none; font-family: var(--font-tech); }
    .qty-input { width: 80px; padding: 10px; border: 1px solid #ccc; font-family: var(--font-tech); }
    @media(max-width: 768px) { .cart-layout { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container" style="padding: 4rem 20px 0;">
    <h1 class="display-text" style="font-size: 3rem;">YOUR SELECTION</h1>
</div>

<div class="cart-layout">
    <div>
        <?php if (count($cart['items']) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>PRODUCT</th>
                        <th>PRICE</th>
                        <th>SIZE</th>
                        <th>QTY</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart['items'] as $item): ?>
                        <tr>
                            <td style="display: flex; gap: 1rem; align-items: center;">
                                <div style="width: 72px; height: 72px; background: <?= h($item['product']['image_bg_color']) ?>; border-radius: 4px; overflow: hidden; display:flex; align-items:center; justify-content:center;">
                                    <img src="<?= h($item['product']['image']) ?>" alt="<?= h($item['product']['name']) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                                <div>
                                    <strong><?= h($item['product']['name']) ?></strong><br>
                                    <small style="color: #666; font-family: var(--font-tech);"><?= h($item['product']['brand']) ?></small><br>
                                    <form method="POST" action="cart_action.php" style="display:inline;">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                        <input type="hidden" name="size" value="<?= h($item['size']) ?>">
                                        <input type="hidden" name="redirect" value="cart.php">
                                        <button type="submit" class="remove-link">REMOVE</button>
                                    </form>
                                </div>
                            </td>
                            <td>&#8377;<?= format_money($item['product']['price']) ?></td>
                            <td><?= h($item['size']) ?></td>
                            <td>
                                <form method="POST" action="cart_action.php" style="display: flex; gap: 8px; align-items: center;">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                    <input type="hidden" name="size" value="<?= h($item['size']) ?>">
                                    <input type="hidden" name="redirect" value="cart.php">
                                    <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="0" max="<?= (int) $item['product']['stock_qty'] ?>" class="qty-input">
                                    <button type="submit" class="btn" style="padding: 10px 14px;">UPDATE</button>
                                </form>
                            </td>
                            <td>&#8377;<?= format_money($item['line_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 2rem;">
                <form method="POST" action="cart_action.php" style="display:inline;">
                    <?= csrf_input() ?>
                    <input type="hidden" name="action" value="clear">
                    <input type="hidden" name="redirect" value="cart.php">
                    <button type="submit" style="color: #666; text-decoration: underline; background: none; border: none; font-family: var(--font-tech);">CLEAR CART</button>
                </form>
            </div>
        <?php else: ?>
            <p style="font-family: var(--font-tech); padding: 2rem;">YOUR CART IS EMPTY. <a href="shop.php" style="text-decoration: underline;">GO TO SUPPLY</a></p>
        <?php endif; ?>
    </div>

    <div class="summary-box">
        <h3 style="margin-bottom: 1.5rem;">ORDER SUMMARY</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Subtotal</span><span>&#8377;<?= format_money($cart['subtotal']) ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Shipping</span><span>Free</span>
        </div>
        <hr style="margin: 1rem 0; border: 1px solid #ccc;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-weight: bold; font-size: 1.2rem;">
            <span>Total</span><span>&#8377;<?= format_money($cart['total']) ?></span>
        </div>

        <?php if (is_logged_in() && count($cart['items']) > 0): ?>
            <a href="checkout.php" class="btn" style="display: block; width: 100%; background: var(--au-blue); color: var(--au-gold); text-align: center; text-decoration: none;">SECURE CHECKOUT</a>
        <?php elseif (count($cart['items']) > 0): ?>
            <a href="login.php" class="btn" style="display: block; width: 100%; text-align: center; text-decoration: none;">LOGIN TO CHECKOUT</a>
        <?php else: ?>
            <button class="btn" style="width: 100%; opacity: 0.5;" disabled>CART EMPTY</button>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
