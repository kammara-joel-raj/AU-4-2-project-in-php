<?php 
$pageTitle = "AU // CHECKOUT";
include 'includes/header.php'; 
?>
<style>
    .checkout-container { max-width: 1000px; margin: 4rem auto; padding: 0 20px; }
    .checkout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; }
    .form-section { margin-bottom: 3rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    input, select { width: 100%; padding: 12px; border: 1px solid #ccc; font-family: var(--font-street); }
    .section-title { font-size: 1.5rem; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
    
    /* Payment Tabs */
    .payment-methods { display: flex; gap: 10px; margin-bottom: 20px; }
    .pay-tab { padding: 10px 20px; border: 1px solid #ccc; cursor: pointer; font-family: var(--font-tech); }
    .pay-tab.active { background: var(--au-blue); color: var(--au-gold); border-color: var(--au-blue); }
    .payment-content { border: 1px solid #ccc; padding: 20px; display: none; }
    .payment-content.active { display: block; }
    
    @media(max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="checkout-container">
    <h1 class="display-text" style="font-size: 2.5rem; margin-bottom: 2rem;">SECURE CHECKOUT</h1>
    
    <div class="checkout-grid">
        <!-- Forms -->
        <div class="left-col">
            <!-- Step 1: Shipping -->
            <div class="form-section">
                <h3 class="section-title">01. SHIPPING ADDRESS</h3>
                <div class="form-row">
                    <input type="text" placeholder="First Name">
                    <input type="text" placeholder="Last Name">
                </div>
                <input type="text" placeholder="Street Address" style="margin-bottom: 1rem;">
                <div class="form-row">
                    <input type="text" placeholder="City">
                    <input type="text" placeholder="PIN Code">
                </div>
                <input type="text" placeholder="Phone Number">
            </div>

            <!-- Step 2: Payment -->
            <div class="form-section">
                <h3 class="section-title">02. PAYMENT METHOD</h3>
                
                <div class="payment-methods">
                    <div class="pay-tab active" onclick="showPay('card')">CREDIT/DEBIT</div>
                    <div class="pay-tab" onclick="showPay('upi')">UPI / GPAY</div>
                    <div class="pay-tab" onclick="showPay('net')">NET BANKING</div>
                </div>

                <!-- Card Form -->
                <div id="card" class="payment-content active">
                    <input type="text" placeholder="Card Number" style="margin-bottom: 1rem;">
                    <div class="form-row">
                        <input type="text" placeholder="MM/YY">
                        <input type="text" placeholder="CVV">
                    </div>
                </div>

                <!-- UPI Form -->
                <div id="upi" class="payment-content" style="text-align: center;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_Pay_logo.svg" style="height: 30px; margin: 10px;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo-vector.svg/1200px-UPI-Logo-vector.svg.png" style="height: 30px; margin: 10px;">
                    <p>Enter UPI ID:</p>
                    <input type="text" placeholder="username@oksbi" style="margin-bottom: 1rem;">
                </div>

                <!-- Net Banking -->
                <div id="net" class="payment-content">
                    <select>
                        <option>State Bank of India</option>
                        <option>HDFC Bank</option>
                        <option>ICICI Bank</option>
                        <option>Andhra Bank</option>
                    </select>
                </div>

            </div>
            
            <button class="btn" onclick="alert('Order Placed Successfully! Order ID: AU-2026-X')" style="width: 100%; background: var(--au-blue); color: var(--au-gold);">PAY & PLACE ORDER</button>
        </div>

        <!-- Order Review -->
        <div class="right-col">
            <div style="background: var(--off-white); padding: 2rem; border: var(--border-thick); position: sticky; top: 100px;">
                <h3>ORDER REVIEW</h3>
                <hr style="margin: 1rem 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Senate Hoodie (x1)</span><span>₹1,899</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Admin Tote (x1)</span><span>₹499</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; color: #666;">
                    <span>Shipping</span><span>₹100</span>
                </div>
                <hr style="margin: 1rem 0;">
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem;">
                    <span>TOTAL</span><span>₹2,498</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showPay(method) {
        document.querySelectorAll('.payment-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.pay-tab').forEach(el => el.classList.remove('active'));
        document.getElementById(method).classList.add('active');
        // Add active class to clicked tab (logic simplified for brevity)
    }
</script>

<?php include 'includes/footer.php'; ?>