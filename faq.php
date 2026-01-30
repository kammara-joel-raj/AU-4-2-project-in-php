<?php 
$pageTitle = "AU // SUPPORT HUB";
include 'includes/header.php'; 
?>
<style>
    .faq-item { border-bottom: 1px solid #ccc; }
    .faq-question { padding: 1.5rem; cursor: pointer; font-family: var(--font-tech); display: flex; justify-content: space-between; font-weight: bold; }
    .faq-question:hover { background: #f4f4f4; }
    .faq-answer { display: none; padding: 0 1.5rem 1.5rem; line-height: 1.6; color: #555; }
    .faq-item.active .faq-answer { display: block; }
    .faq-item.active .faq-question { background: var(--au-blue); color: #fff; }
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container" style="padding: 4rem 20px; max-width: 800px; margin: 0 auto;">
    <h1 class="display-text" style="font-size: 3rem; margin-bottom: 3rem;">SUPPORT_HUB</h1>

    <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">01. HOW DO I FIND MY SIZE? <span>+</span></div>
        <div class="faq-answer">
            We use standard sizing. However, our "Street" collection (Hoodies/Tees) features an oversized fit. 
            For a regular fit, we recommend sizing down. Use the "Virtual Fit Lab" on product pages for AI recommendations.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">02. PAYMENT METHODS? <span>+</span></div>
        <div class="faq-answer">
            We accept all major Credit/Debit Cards, UPI (Google Pay, PhonePe), and Net Banking via our secure gateway.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">03. RETURNS & CANCELLATIONS? <span>+</span></div>
        <div class="faq-answer">
            Returns are accepted within 7 days of delivery if tags are intact. Cancellations can be made within 24 hours of placing the order.
            Go to "My Account" to initiate a request.
        </div>
    </div>

    <div class="faq-item">
        <div class="faq-question" onclick="toggleFaq(this)">04. WHERE IS MY ORDER? <span>+</span></div>
        <div class="faq-answer">
            Orders typically ship within 48 hours. You will receive a tracking link via email/SMS once dispatched.
            Campus pickup orders are available at the Senate House within 24 hours.
        </div>
    </div>
</div>

<script>
    function toggleFaq(el) {
        el.parentElement.classList.toggle('active');
    }
</script>

<?php include 'includes/footer.php'; ?>