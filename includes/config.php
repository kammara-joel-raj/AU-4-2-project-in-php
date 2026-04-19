<?php

$localConfigPath = dirname(__DIR__) . '/config.local.php';
if (is_file($localConfigPath)) {
    require_once $localConfigPath;
}

if (!function_exists('app_env')) {
    function app_env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return $value;
    }
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'AU Archives');
}

if (!defined('APP_CURRENCY')) {
    define('APP_CURRENCY', 'INR');
}

if (!defined('APP_STORAGE_PATH')) {
    define('APP_STORAGE_PATH', dirname(__DIR__) . '/storage');
}

if (!defined('APP_SESSION_PATH')) {
    define('APP_SESSION_PATH', APP_STORAGE_PATH . '/sessions');
}

if (!defined('APP_UPLOAD_CLEANUP_LIMIT')) {
    define('APP_UPLOAD_CLEANUP_LIMIT', (int) app_env('UPLOAD_CLEANUP_LIMIT', 30));
}

if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', rtrim(app_env('APP_BASE_URL', 'http://localhost/AU-4-2-project'), '/'));
}

if (!defined('RAZORPAY_KEY_ID')) {
    define('RAZORPAY_KEY_ID', app_env('RAZORPAY_KEY_ID', ''));
}

if (!defined('RAZORPAY_KEY_SECRET')) {
    define('RAZORPAY_KEY_SECRET', app_env('RAZORPAY_KEY_SECRET', ''));
}

if (!defined('RAZORPAY_WEBHOOK_SECRET')) {
    define('RAZORPAY_WEBHOOK_SECRET', app_env('RAZORPAY_WEBHOOK_SECRET', ''));
}

if (!defined('RAZORPAY_API_BASE')) {
    define('RAZORPAY_API_BASE', 'https://api.razorpay.com/v1');
}

if (!defined('AI_PROVIDER')) {
    define('AI_PROVIDER', strtolower((string) app_env('AI_PROVIDER', 'replicate')));
}

if (!defined('AI_API_TOKEN')) {
    define('AI_API_TOKEN', app_env('AI_API_TOKEN', ''));
}

if (!defined('AI_MODEL_VERSION')) {
    define(
        'AI_MODEL_VERSION',
        app_env(
            'AI_MODEL_VERSION',
            'cuuupid/idm-vton:0513734a452173b8173e907e3a59d19a36266e55b48528559432bd21c7d7e985'
        )
    );
}

if (!defined('AI_MAX_IMAGE_BYTES')) {
    define('AI_MAX_IMAGE_BYTES', (int) app_env('AI_MAX_IMAGE_BYTES', 850000));
}

if (!defined('AI_MAX_EDGE')) {
    define('AI_MAX_EDGE', (int) app_env('AI_MAX_EDGE', 1280));
}

if (!defined('SHOP_PRODUCTS_PER_PAGE')) {
    define('SHOP_PRODUCTS_PER_PAGE', (int) app_env('SHOP_PRODUCTS_PER_PAGE', 6));
}

if (!defined('ADMIN_PRODUCTS_PER_PAGE')) {
    define('ADMIN_PRODUCTS_PER_PAGE', (int) app_env('ADMIN_PRODUCTS_PER_PAGE', 10));
}

if (!defined('DEFAULT_PRODUCT_SIZES')) {
    define('DEFAULT_PRODUCT_SIZES', 'S,M,L,XL');
}

if (!defined('DEFAULT_ACCESSORY_SIZES')) {
    define('DEFAULT_ACCESSORY_SIZES', 'ONE SIZE');
}
