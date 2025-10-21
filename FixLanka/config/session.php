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
?>