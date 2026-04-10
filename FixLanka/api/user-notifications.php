<?php
require_once __DIR__ . '/../controllers/UserNotificationsController.php';

$controller = new UserNotificationsController();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $controller->list();
        break;
    case 'count':
        $controller->count();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
