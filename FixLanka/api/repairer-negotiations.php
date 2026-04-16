<?php

require_once __DIR__ . '/../controllers/RepairerNegotiationController.php';

$controller = new RepairerNegotiationController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'list':
        $controller->listReceived();
        break;
    case 'accept':
        $controller->accept();
        break;
    case 'counter':
        $controller->counter();
        break;
    case 'reject':
        $controller->reject();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action',
        ]);
        break;
}
