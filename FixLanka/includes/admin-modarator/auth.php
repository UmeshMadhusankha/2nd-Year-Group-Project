<?php
// Admin and Moderator Authentication Helper Functions

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user data
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'Admin User',
            'email' => $_SESSION['user_email'] ?? 'admin@fixlanka.com',
            'role' => $_SESSION['user_role'] ?? 'admin',
            'avatar' => $_SESSION['user_avatar'] ?? null
        ];
    }
    return null;
}

/**
 * Require specific role to access page
 */
function requireRole($requiredRole, $redirectPath = '/') {
    $user = getCurrentUser();
    
    if (!$user) {
        // Not logged in - redirect to login
        header('Location: ' . $redirectPath . '/login');
        exit();
    }
    
    // Check if user has required role
    $allowedRoles = is_array($requiredRole) ? $requiredRole : [$requiredRole];
    
    if (!in_array($user['role'], $allowedRoles)) {
        // User doesn't have required role - redirect to unauthorized page
        header('Location: ' . $redirectPath . '/unauthorized');
        exit();
    }
    
    return true;
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Check if user is moderator
 */
function isModerator() {
    return hasRole('moderator');
}

/**
 * Logout user
 */
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
?>
