<?php
require_once __DIR__ . '/../controllers/UserQuotesController.php';

$controller = new UserQuotesController();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        $controller->list();
        break;
    case 'summary':
        $controller->summary();
        break;
    case 'respond':
        $controller->respond();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
