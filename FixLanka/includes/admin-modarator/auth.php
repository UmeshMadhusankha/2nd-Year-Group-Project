<?php
// Admin and Moderator Authentication Helper Functions

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load centralized session configuration
require_once __DIR__ . '/../../config/session.php';

/**
 * Get current logged-in user data
 * Note: centralized session.php version returns slightly different keys, 
 * but trackUserSession uses $_SESSION directly.
 */
if (!function_exists('getCurrentUser')) {
    function getCurrentUser() {
        if (isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? ($_SESSION['admin_id'] ?? 'Admin User'),
                'email' => $_SESSION['user_email'] ?? 'admin@fixlanka.com',
                'role' => $_SESSION['user_role'] ?? 'admin',
                'avatar' => $_SESSION['user_avatar'] ?? null
            ];
        }
        return null;
    }
}

/**
 * Check if user is admin
 */
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return hasRole('admin');
    }
}

/**
 * Check if user is moderator
 */
if (!function_exists('isModerator')) {
    function isModerator() {
        return hasRole('moderator');
    }
}

/**
 * Logout user
 */
if (!function_exists('logout')) {
    function logout($redirectPath = '/') {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Redirect to login page
        header('Location: ' . $redirectPath . '/login');
        exit();
    }
}
?>
