<?php
require_once 'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\config\session.php';
header('Content-Type: application/json');

echo json_encode([
    'sessionData' => $_SESSION,
    'userData' => getUserData(),
    'companyId' => getUserData()['id'] ?? 'Not found'
]);
?>
