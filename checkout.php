<?php
require_once 'includes/bootstrap.php';

require_login('login.php');

$cart = cart_items_with_products($pdo);
if (empty($cart['items'])) {
    session_flash('error', 'Your cart is empty.');
    safe_redirect('cart.php');
}

$user = current_user_record($pdo);
$pageTitle = 'AU // CHECKOUT';

include 'includes/header.php';
?>
<style>
    .checkout-container { max-width: 1100px; margin: 4rem auto; padding: 0 20px; }
    .checkout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; }
    .form-section { margin-bottom: 2rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    input, textarea { width: 100%; padding: 12px; border: 1px solid #ccc; font-family: var(--font-street); }
    textarea { min-height: 130px; resize: vertical; }
    .section-title { font-size: 1.5rem; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
    @media(max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="checkout-container">
    <h1 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem;">SECURE CHECKOUT</h1>

    <div class="checkout-grid">
        <div class="left-col">
            <div class="form-section">
                <h3 class="section-title">01. SHIPPING DETAILS</h3>
                <form id="checkoutForm">
                    <div class="form-row">
                        <input type="text" name="shipping_name" placeholder="Full Name" value="<?= h($user['full_name'] ?? '') ?>" required>
                        <input type="text" name="phone" placeholder="Phone Number" value="<?= h($user['phone'] ?? '') ?>" required>
                    </div>
                    <textarea name="shipping_address" placeholder="Full Shipping Address" required></textarea>
                </form>
            </div>

            <div class="form-section" style="border: var(--border-thick); padding: 1.5rem; background: #fff;">
                <h3 class="section-title" style="margin-bottom: 1rem;">02. PAY WITH RAZORPAY</h3>
                <p style="line-height: 1.7; color: #555; margin-bottom: 1rem;">
                    Razorpay Standard Checkout will handle card, UPI, and net banking in one secure flow after your order is created on the server.
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap; font-family: var(--font-tech); color: #666;">
                    <span style="padding: 10px 14px; border: 1px solid #ccc;">UPI</span>
                    <span style="padding: 10px 14px; border: 1px solid #ccc;">CARDS</span>
                    <span style="padding: 10px 14px; border: 1px solid #ccc;">NETBANKING</span>
                </div>
            </div>

            <button id="payButton" class="btn" style="width: 100%; background: var(--au-blue); color: var(--au-gold);">PAY &amp; PLACE ORDER</button>
            <p id="checkoutStatus" style="margin-top: 1rem; font-family: var(--font-tech); color: #666;"></p>
        </div>

        <div class="right-col">
            <div style="background: var(--off-white); padding: 2rem; border: var(--border-thick); position: sticky; top: 100px;">
                <h3>ORDER REVIEW</h3>
                <hr style="margin: 1rem 0;">
                <?php foreach ($cart['items'] as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px; gap: 1rem;">
                        <span><?= h($item['product']['name']) ?> (<?= h($item['size']) ?> x<?= (int) $item['quantity'] ?>)</span>
                        <span>&#8377;<?= format_money($item['line_total']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #666;">
                    <span>Shipping</span><span>Free</span>
                </div>
                <hr style="margin: 1rem 0;">
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem;">
                    <span>TOTAL</span><span>&#8377;<?= format_money($cart['total']) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const payButton = document.getElementById('payButton');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutStatus = document.getElementById('checkoutStatus');

    payButton.addEventListener('click', async function() {
        if (!checkoutForm.reportValidity()) {
            checkoutStatus.textContent = 'Please complete the shipping details before payment.';
            return;
        }

        checkoutStatus.textContent = 'Creating your order...';
        payButton.disabled = true;

        const formData = new FormData(checkoutForm);

        try {
            const payload = await auPost('place_order.php', formData);
            checkoutStatus.textContent = 'Opening Razorpay checkout...';

            const options = {
                key: payload.key_id,
                name: payload.store_name,
                description: 'AU Merchandise Order ' + payload.order_number,
                order_id: payload.gateway_order_id,
                amount: payload.amount,
                currency: payload.currency,
                image: payload.logo || undefined,
                prefill: payload.prefill,
                notes: {
                    local_order_id: payload.local_order_id,
                    order_number: payload.order_number
                },
                theme: {
                    color: '#002147'
                },
                modal: {
                    ondismiss: function() {
                        auPost('payment_status.php', {
                            order_id: payload.local_order_id,
                            payment_state: 'pending'
                        }).finally(() => {
                            checkoutStatus.textContent = 'Payment window closed. Your order remains pending until payment succeeds.';
                            payButton.disabled = false;
                        });
                    }
                },
                handler: async function (response) {
                    checkoutStatus.textContent = 'Verifying payment...';
                    try {
                        const verification = await auPost('payment_verify.php', {
                            order_id: payload.local_order_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_signature: response.razorpay_signature
                        });

                        window.location.href = verification.redirect;
                    } catch (error) {
                        checkoutStatus.textContent = error.message || 'Payment verification failed.';
                        payButton.disabled = false;
                    }
                }
            };

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function () {
                auPost('payment_status.php', {
                    order_id: payload.local_order_id,
                    payment_state: 'failed'
                }).finally(() => {
                    checkoutStatus.textContent = 'Payment failed. You can try again.';
                    payButton.disabled = false;
                });
            });
            rzp.open();
        } catch (error) {
            checkoutStatus.textContent = error.message || 'Unable to start checkout.';
            payButton.disabled = false;
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
