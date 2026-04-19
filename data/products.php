<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$products = fetch_products_paginated($pdo, [
    'category' => 'all',
    'sort' => 'latest',
    'page' => 1,
    'per_page' => 100,
])['items'];

function getProductById($id, $pdo)
{
    return fetch_product_by_id($pdo, $id, true);
}
