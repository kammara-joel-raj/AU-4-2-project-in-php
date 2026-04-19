<?php
require_once 'includes/bootstrap.php';

$pageTitle = 'AU // SEARCH';
$query = trim((string) ($_GET['q'] ?? ''));
$results = search_products($pdo, $query, 30);
$suggestion = count($results) === 0 && strtolower($query) === 'hoddie' ? 'hoodie' : '';

include 'includes/header.php';
?>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container" style="padding: 4rem 20px; min-height: 60vh;">
    <h1 style="font-family: var(--font-tech); color: #666;">SEARCH RESULTS FOR: "<?= h(strtoupper($query)) ?>"</h1>

    <?php if ($suggestion): ?>
        <p style="margin-top: 1rem;">Did you mean: <a href="search.php?q=<?= h($suggestion) ?>" style="color: var(--au-blue); font-weight: bold;"><?= h($suggestion) ?></a>?</p>
    <?php endif; ?>

    <?php if (count($results) > 0): ?>
        <p style="margin-top: 1rem;"><?= count($results) ?> items found.</p>
        <div class="product-grid" style="margin-top: 2rem; border: var(--border-thick);">
            <?php foreach ($results as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?= (int) $product['id'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="card-img" style="background: <?= h($product['image_bg_color']) ?>;">
                            <img src="<?= h($product['image']) ?>" alt="<?= h($product['name']) ?>">
                        </div>
                        <div>
                            <h3><?= h($product['name']) ?></h3>
                            <p style="font-family: var(--font-tech); margin-top: 10px;">&#8377;<?= format_money($product['price']) ?></p>
                            <p style="color: #666; margin-top: 8px;"><?= h($product['description']) ?></p>
                        </div>
                    </a>
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
