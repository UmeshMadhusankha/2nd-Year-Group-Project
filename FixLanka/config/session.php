<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\config\session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getUserData() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? 'User',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'user'
        ];
    }
    return null;
}

function requireRole($allowedRoles) {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please login to access this page';
        header('Location: /2nd-Year-Group-Project/FixLanka/login');
        exit;
    }
    
    $currentRole = $_SESSION['user_role'] ?? 'user';
    
    // Convert single role to array for consistent checking
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    
    if (!in_array($currentRole, $allowedRoles)) {
        $_SESSION['error'] = 'You do not have permission to access this page';
        header('Location: /2nd-Year-Group-Project/FixLanka/');
        exit;
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}
?>