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
// For company users, user_id IS the company_id (see AuthController)
$companyId = $_SESSION['user_id']; 

// Handle GET Request (Fetch Profile)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $profile = $companyModel->getProfile($companyId);
    if ($profile) {
        echo json_encode(['success' => true, 'data' => $profile]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Profile not found']);
    }
    exit;
}

// Handle POST Request (Updates)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $action = $input['action'] ?? '';

    switch ($action) {
        case 'change_password':
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                exit;
            }

            $result = $companyModel->changePassword($companyId, $currentPassword, $newPassword);
            echo json_encode($result);
            break;

        case 'update_profile':
            // Filter input to only pass relevant data
            // This assumes the frontend sends fields matching the allowed list in Model
            // or we filter it here. For now, pass all remaining input.
            unset($input['action']); 
            $success = $companyModel->updateProfile($companyId, $input);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made or update failed']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}
?>
