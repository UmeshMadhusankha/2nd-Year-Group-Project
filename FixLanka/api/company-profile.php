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
    // Determine if this is a JSON request or a Form/File request
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $action = '';
    $input = [];

    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
    } else {
        // Fallback to PHP superglobals for multipart/form-data
        $action = $_POST['action'] ?? '';
        $input = $_POST;
    }

    if (empty($action)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input: No action specified']);
        exit;
    }

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

        case 'upload_logo':
            if (!isset($_FILES['logo'])) {
                error_log("Logo upload failed: 'logo' not set in \$_FILES. Keys present: " . implode(', ', array_keys($_FILES)));
                echo json_encode(['success' => false, 'message' => 'Invalid input: No file received']);
                exit;
            }
            if ($_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
                error_log("Logo upload failed: Error code " . $_FILES['logo']['error']);
                echo json_encode(['success' => false, 'message' => 'Invalid input: Upload error code ' . $_FILES['logo']['error']]);
                exit;
            }

            // Server-side validation: Max 5MB
            if ($_FILES['logo']['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Invalid input: File size exceeds 5MB limit']);
                exit;
            }

            // Server-side validation: Mime type
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['logo']['tmp_name']);
            if (strpos($mimeType, 'image/') !== 0) {
                error_log("Invalid logo upload: Mime type $mimeType");
                echo json_encode(['success' => false, 'message' => 'Invalid input: File is not a valid image']);
                exit;
            }

            $uploadDir = __DIR__ . '/../uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $filename = 'company_' . $companyId . '_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $filename;
            $dbPath = '/2nd-Year-Group-Project/FixLanka/uploads/logos/' . $filename;

            error_log("Attempting to upload logo to: $targetPath");

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $targetPath)) {
                $success = $companyModel->updateLogo($companyId, $dbPath);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Logo uploaded successfully', 'logo_path' => $dbPath]);
                } else {
                    error_log("Failed to update logo in database for company $companyId");
                    echo json_encode(['success' => false, 'message' => 'Failed to update logo in database']);
                }
            } else {
                error_log("Failed to move uploaded file from " . $_FILES['logo']['tmp_name'] . " to $targetPath");
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}
?>
