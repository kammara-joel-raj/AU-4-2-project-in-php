<?php 
$pageTitle = "AU // CART";
include 'includes/header.php'; 
?>
<style>
    .cart-layout { max-width: 1200px; margin: 4rem auto; padding: 0 20px; display: grid; grid-template-columns: 2fr 1fr; gap: 4rem; }
    .cart-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    .cart-table th { text-align: left; padding: 1rem; border-bottom: 2px solid #000; font-family: var(--font-tech); }
    .cart-table td { padding: 1.5rem 1rem; border-bottom: 1px solid #ccc; vertical-align: middle; }
    .summary-box { background: var(--off-white); padding: 2rem; border: var(--border-thick); }
    @media(max-width: 768px) { .cart-layout { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container" style="padding: 4rem 20px 0;">
    <h1 class="display-text" style="font-size: 3rem;">YOUR SELECTION</h1>
</div>

<div class="cart-layout">
    <!-- Cart Items -->
    <div>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>PRODUCT</th>
                    <th>PRICE</th>
                    <th>QUANTITY</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="display: flex; gap: 1rem; align-items: center;">
                        <div style="width: 60px; height: 60px; background: #222;"></div>
                        <div>
                            <strong>Senate House Hoodie</strong><br>
                            <span style="font-size: 0.8rem; color: #666;">Size: L</span>
                        </div>
                    </td>
                    <td>₹1,899</td>
                    <td><input type="number" value="1" style="width: 50px; padding: 5px;"></td>
                    <td>₹1,899</td>
                </tr>
                 <tr>
                    <td style="display: flex; gap: 1rem; align-items: center;">
                        <div style="width: 60px; height: 60px; background: #eee;"></div>
                        <div>
                            <strong>Admin Block Tote</strong><br>
                            <span style="font-size: 0.8rem; color: #666;">Size: One Size</span>
                        </div>
                    </td>
                    <td>₹499</td>
                    <td><input type="number" value="1" style="width: 50px; padding: 5px;"></td>
                    <td>₹499</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h3 style="margin-bottom: 1.5rem;">ORDER SUMMARY</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Subtotal</span><span>₹2,398</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
            <span>Shipping</span><span>Calculated at Checkout</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; font-weight: bold; font-size: 1.2rem;">
            <span>Estimated Total</span><span>₹2,398</span>
        </div>
        <a href="checkout.php"><button class="btn" style="width: 100%; background: var(--au-blue); color: var(--au-gold);">SECURE CHECKOUT</button></a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>