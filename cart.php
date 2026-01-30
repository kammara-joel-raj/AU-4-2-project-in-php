<?php 
$pageTitle = "AU // CART";
include 'includes/header.php'; 
require_once 'includes/db.php';
require_once 'data/products.php'; // Reuse helper function

$cartItems = [];
$total = 0;

if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id => $qty) {
        $product = getProductById($id, $pdo);
        if ($product) {
            $product['qty'] = $qty;
            $product['line_total'] = $product['price'] * $qty;
            $total += $product['line_total'];
            $cartItems[] = $product;
        }
    }
}
?>
<style>
    .cart-layout { max-width: 1200px; margin: 4rem auto; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 4rem; }
    .cart-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .cart-table th { text-align: left; padding: 1rem; border-bottom: 2px solid #000; font-family: var(--font-tech); }
    .cart-table td { padding: 1.5rem 1rem; border-bottom: 1px solid #ccc; vertical-align: middle; }
    .summary-box { background: var(--off-white); padding: 2rem; border: var(--border-thick); }
    .remove-link { color: red; font-size: 0.8rem; text-decoration: underline; cursor: pointer; }
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
        <?php if(count($cartItems) > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>PRODUCT</th>
                    <th>PRICE</th>
                    <th>QTY</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cartItems as $item): ?>
                <tr>
                    <td style="display: flex; gap: 1rem; align-items: center;">
                        <div style="width: 60px; height: 60px; background: <?php echo $item['image_bg_color']; ?>;"></div>
                        <div>
                            <strong><?php echo $item['name']; ?></strong><br>
                            <a href="cart_action.php?action=remove&id=<?php echo $item['id']; ?>" class="remove-link">REMOVE</a>
                        </div>
                    </td>
                    <td>₹<?php echo number_format($item['price']); ?></td>
                    <td><?php echo $item['qty']; ?></td>
                    <td>₹<?php echo number_format($item['line_total']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 2rem;">
            <a href="cart_action.php?action=clear" style="color: #666; text-decoration: underline;">CLEAR CART</a>
        </div>
        <?php else: ?>
            <p style="font-family: var(--font-tech); padding: 2rem;">YOUR CART IS EMPTY. <a href="shop.php" style="text-decoration: underline;">GO TO SUPPLY</a></p>
        <?php endif; ?>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h3 style="margin-bottom: 1.5rem;">ORDER SUMMARY</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Subtotal</span><span>₹<?php echo number_format($total); ?></span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Shipping</span><span>Free</span>
        </div>
        <hr style="margin: 1rem 0; border: 1px solid #ccc;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-weight: bold; font-size: 1.2rem;">
            <span>Total</span><span>₹<?php echo number_format($total); ?></span>
        </div>
        
        <?php if(isset($_SESSION['user_id']) && count($cartItems) > 0): ?>
            <a href="checkout.php"><button class="btn" style="width: 100%; background: var(--au-blue); color: var(--au-gold);">SECURE CHECKOUT</button></a>
        <?php elseif(count($cartItems) > 0): ?>
            <a href="login.php"><button class="btn" style="width: 100%;">LOGIN TO CHECKOUT</button></a>
        <?php else: ?>
            <button class="btn" style="width: 100%; opacity: 0.5;" disabled>CART EMPTY</button>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>