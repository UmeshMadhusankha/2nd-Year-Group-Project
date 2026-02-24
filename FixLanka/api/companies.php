<?php
require_once __DIR__ . '/../controllers/CompanyController.php';

$controller = new CompanyController();
$action = $_GET['action'] ?? 'getDetails';

switch ($action) {
    case 'getDetails':
        $controller->getDetails();
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
