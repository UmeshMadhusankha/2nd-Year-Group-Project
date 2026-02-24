<?php
require_once __DIR__ . '/../controllers/ProviderController.php';

$controller = new ProviderController();
$action = $_GET['action'] ?? 'getFeatured';

switch ($action) {
    case 'getProviders':
        $controller->getProviders();
        break;
    case 'getFeatured':
        $controller->getFeatured();
        break;
    case 'getDetails':
        $controller->getProviderDetails();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        exit;
}
