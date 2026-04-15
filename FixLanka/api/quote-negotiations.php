<?php

require_once __DIR__ . '/../controllers/QuoteNegotiationController.php';

$controller = new QuoteNegotiationController();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'list':
        $controller->list();
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
