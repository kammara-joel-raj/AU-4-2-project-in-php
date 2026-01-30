<?php 
$pageTitle = "AU // SEARCH";
include 'includes/header.php'; 
include 'data/products.php';

$query = isset($_GET['q']) ? strtolower($_GET['q']) : '';
$results = [];

// Simulate Search Logic
foreach($products as $p) {
    if (strpos(strtolower($p['name']), $query) !== false || strpos(strtolower($p['desc']), $query) !== false) {
        $results[] = $p;
    }
}

// "Did you mean?" Logic
$suggestion = "";
if (count($results) == 0 && $query == "hoddie") {
    $suggestion = "hoodie";
}
?>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container" style="padding: 4rem 20px; min-height: 60vh;">
    <h1 style="font-family: var(--font-tech); color: #666;">SEARCH RESULTS FOR: "<?php echo strtoupper($query); ?>"</h1>
    
    <?php if ($suggestion): ?>
        <p style="margin-top: 1rem;">Did you mean: <a href="search.php?q=<?php echo $suggestion; ?>" style="color: var(--au-blue); font-weight: bold;"><?php echo $suggestion; ?></a>?</p>
    <?php endif; ?>

    <?php if (count($results) > 0): ?>
        <p><?php echo count($results); ?> items found.</p>
        <div class="product-grid" style="margin-top: 2rem; border: var(--border-thick);">
            <?php foreach ($results as $product): ?>
                <div class="product-card">
                    <div class="card-img" style="background: <?php echo $product['img_bg']; ?>; color: #fff;">
                        <?php echo $product['img_text']; ?>
                    </div>
                    <div>
                        <h3><?php echo $product['name']; ?></h3>
                        <p>₹<?php echo $product['price']; ?></p>
                        <a href="product.php?id=<?php echo $product['id']; ?>"><button class="btn" style="margin-top: 10px;">VIEW</button></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif (!$suggestion): ?>
        <div style="margin-top: 4rem; text-align: center;">
            <h2 style="font-size: 2rem; opacity: 0.5;">NO ARCHIVES FOUND.</h2>
            <a href="shop.php"><button class="btn" style="margin-top: 1rem;">RETURN TO SUPPLY</button></a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>