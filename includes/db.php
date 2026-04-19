<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/schema.php';

$host = 'localhost';
$db_name = 'lol';
$username = 'root';
$password = 'joel';

ensure_directory(APP_SESSION_PATH);

if (session_status() === PHP_SESSION_NONE) {
    session_name('au_archives');
    session_save_path(APP_SESSION_PATH);
    session_start();
}

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db_name;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    ensure_app_schema($pdo);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
