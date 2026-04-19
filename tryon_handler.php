<?php
require_once 'includes/bootstrap.php';

header('Content-Type: application/json');
http_response_code(410);

echo json_encode([
    'success' => false,
    'message' => 'The Virtual Fit Lab now runs fully in the browser. Open tryon.php to use the local composer.',
]);
