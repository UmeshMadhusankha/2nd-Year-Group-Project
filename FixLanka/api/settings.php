<?php
require_once '../config/session.php';
require_once '../models/CompanyModel.php';

// Set JSON header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize Model
$companyModel = new CompanyModel();
$companyId = $_SESSION['user_id']; 

// Handle GET Request (Fetch All Settings Data)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $settings = $companyModel->getSettings($companyId);
        $history = $companyModel->getLoginHistory($companyId);
        $billing = $companyModel->getBillingHistory($companyId);

        echo json_encode([
            'success' => true, 
            'data' => [
                'settings' => $settings,
                'history' => $history,
                'billing' => $billing,
                'current_session' => [
                    'device' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'location' => 'Unknown' // GeoIP would go here
                ]
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle POST Request (Update Settings)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $action = $input['action'] ?? '';

    if ($action === 'update_notifications') {
        $success = $companyModel->updateSettings($companyId, $input['settings']);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>
