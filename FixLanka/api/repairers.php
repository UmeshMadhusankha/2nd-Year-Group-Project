<?php
require_once __DIR__ . '/../controllers/RepairerController.php';

$controller = new RepairerController();
$action = $_GET['action'] ?? $_POST['action'] ?? 'getDetails';

switch ($action) {
    case 'getDetails':
        $controller->getDetails();
        break;
    case 'updateProfile':
        $controller->updateProfile();
        break;
    case 'changePassword':
        $controller->changePassword();
        break;
    case 'uploadPhoto':
        $controller->uploadPhoto();
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
