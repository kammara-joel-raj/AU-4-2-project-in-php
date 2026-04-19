<?php

require_once __DIR__ . '/security.php';

if (!function_exists('current_user_id')) {
    function current_user_id()
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return current_user_id() !== null;
    }
}

if (!function_exists('current_user_role')) {
    function current_user_role()
    {
        return $_SESSION['user_role'] ?? 'guest';
    }
}

if (!function_exists('current_user_name')) {
    function current_user_name()
    {
        return $_SESSION['user_name'] ?? null;
    }
}

if (!function_exists('is_admin_user')) {
    function is_admin_user()
    {
        return current_user_role() === 'admin';
    }
}

if (!function_exists('set_logged_in_user')) {
    function set_logged_in_user(array $user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'customer';
    }
}

if (!function_exists('clear_logged_in_user')) {
    function clear_logged_in_user()
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

if (!function_exists('require_login')) {
    function require_login($redirect = 'login.php')
    {
        if (!is_logged_in()) {
            session_flash('error', 'Please sign in to continue.');
            safe_redirect($redirect);
        }
    }
}

if (!function_exists('require_admin')) {
    function require_admin()
    {
        if (!is_logged_in()) {
            session_flash('error', 'Please sign in to access the admin dashboard.');
            safe_redirect('login.php');
        }

        if (!is_admin_user()) {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}

