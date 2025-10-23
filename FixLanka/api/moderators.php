<?php
// API endpoint for moderator operations
session_start();

// Uncomment when auth is ready
// require_once __DIR__ . '/../includes/admin-modarator/auth.php';
// requireRole("admin", "");

require_once __DIR__ . '/../controllers/ModeratorController.php';

header('Content-Type: application/json');

// Get the action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$controller = new ModeratorController();

switch ($action) {
    case 'getAll':
        $controller->getAllModerators();
        break;
    
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->addModerator();
        break;
    
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->updateModerator();
        break;
    
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->deleteModerator();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
