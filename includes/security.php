<?php

if (!function_exists('session_flash')) {
    function session_flash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('pull_flash')) {
    function pull_flash()
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_input')) {
    function csrf_input()
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token)
    {
        return is_string($token) && hash_equals(csrf_token(), $token);
    }
}

if (!function_exists('require_valid_csrf')) {
    function require_valid_csrf()
    {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!verify_csrf_token($token)) {
            http_response_code(419);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Your session expired. Refresh the page and try again.',
            ]);
            exit;
        }
    }
}

if (!function_exists('wants_json')) {
    function wants_json()
    {
        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');

        return str_contains($accept, 'application/json') || $requestedWith === 'xmlhttprequest';
    }
}

if (!function_exists('safe_redirect')) {
    function safe_redirect($location)
    {
        header('Location: ' . $location);
        exit;
    }
}

