<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerModel.php';

class RepairerController {
    private $repairerModel;

    public function __construct() {
        global $pdo;
        $this->repairerModel = new Repairer($pdo);
    }

    /**
     * Get single repairer details (JSON)
     * GET params: id
     */
    public function getDetails() {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        try {
            $repairerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (!$repairerId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Repairer ID is required'
                ]);
                exit;
            }

            $repairer = $this->repairerModel->getById($repairerId);
            if (!$repairer) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Repairer not found'
                ]);
                exit;
            }

            // Reviews are stored for repairers in `review`
            $summary = $this->repairerModel->getReviewSummary($repairerId);
            $repairer['reviewCount'] = $summary['count'];
            $repairer['ratings'] = $summary['average'];
            $repairer['reviews'] = $this->repairerModel->getRecentReviews($repairerId, 3);

            echo json_encode([
                'success' => true,
                'data' => $repairer
            ]);
        } catch (Exception $e) {
            error_log('Error in RepairerController::getDetails: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch repairer details'
            ]);
        }
        exit;
    }

    /**
     * Update repairer profile details (JSON)
     * POST body: full_name, email, phone, category_id, districts, availability, about
     */
    public function updateProfile() {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request data']);
                exit;
            }

            $repairerId = isset($input['id']) ? (int)$input['id'] : 0;
            if (!$repairerId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Repairer ID is required']);
                exit;
            }

            // Validate required fields
            $required = ['full_name', 'email', 'phone'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
                    exit;
                }
            }

            // Check for duplicate email (excluding current repairer)
            global $pdo;
            $emailCheck = $pdo->prepare("SELECT repairer_id FROM repairer WHERE email = ? AND repairer_id != ?");
            $emailCheck->execute([$input['email'], $repairerId]);
            if ($emailCheck->fetch()) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Email already in use by another account']);
                exit;
            }

            $data = [
                'full_name'   => $input['full_name'],
                'email'       => $input['email'],
                'phone'       => $input['phone'],
                'category_id' => $input['category_id'] ?? null,
                'districts'   => $input['districts'] ?? '',
                'availability'=> $input['availability'] ?? 'available',
                'about'       => $input['about'] ?? ''
            ];

            $result = $this->repairerModel->updateProfile($repairerId, $data);

            if ($result) {
                // Return updated data
                $updated = $this->repairerModel->getById($repairerId);
                $summary = $this->repairerModel->getReviewSummary($repairerId);
                $updated['reviewCount'] = $summary['count'];
                $updated['ratings'] = $summary['average'];

                // Update session name/email if this is the logged-in user
                if (session_status() === PHP_SESSION_NONE) session_start();
                if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $repairerId) {
                    $_SESSION['user_name'] = $input['full_name'];
                    $_SESSION['user_email'] = $input['email'];
                }

                echo json_encode(['success' => true, 'message' => 'Profile updated successfully', 'data' => $updated]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
            }
        } catch (Exception $e) {
            error_log('Error in RepairerController::updateProfile: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while updating profile']);
        }
        exit;
    }

    /**
     * Change repairer password (JSON)
     * POST body: id, current_password, new_password
     */
    public function changePassword() {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request data']);
                exit;
            }

            $repairerId = isset($input['id']) ? (int)$input['id'] : 0;
            if (!$repairerId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Repairer ID is required']);
                exit;
            }

            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Both current and new passwords are required']);
                exit;
            }

            if (strlen($newPassword) < 8) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters']);
                exit;
            }

            // Verify current password
            $storedHash = $this->repairerModel->getPasswordHash($repairerId);
            if (!$storedHash) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Repairer not found']);
                exit;
            }

            // Support both hashed and plain-text passwords (for legacy data)
            $passwordValid = false;
            if (password_verify($currentPassword, $storedHash)) {
                $passwordValid = true;
            } elseif ($currentPassword === $storedHash) {
                // Plain-text fallback for legacy passwords
                $passwordValid = true;
            }

            if (!$passwordValid) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
                exit;
            }

            // Hash new password and update
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $result = $this->repairerModel->updatePassword($repairerId, $newHash);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to change password']);
            }
        } catch (Exception $e) {
            error_log('Error in RepairerController::changePassword: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while changing password']);
        }
        exit;
    }

    /**
     * Upload repairer profile picture
     * POST: multipart/form-data with 'photo' file and 'id' field
     */
    public function uploadPhoto() {
        while (ob_get_level() > 0) ob_end_clean();
        header('Content-Type: application/json');

        try {
            $repairerId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if (!$repairerId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Repairer ID is required']);
                exit;
            }

            if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No photo uploaded or upload error']);
                exit;
            }

            $file = $_FILES['photo'];

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed']);
                exit;
            }

            // Validate file size (5MB max)
            if ($file['size'] > 5 * 1024 * 1024) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
                exit;
            }

            // Create upload directory if it doesn't exist
            $uploadDir = __DIR__ . '/../uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Delete old profile picture if it exists
            $currentRepairer = $this->repairerModel->getById($repairerId);
            if ($currentRepairer && !empty($currentRepairer['profilePicture'])) {
                $oldPath = __DIR__ . '/../' . $currentRepairer['profilePicture'];
                if (file_exists($oldPath) && strpos($currentRepairer['profilePicture'], 'uploads/') !== false) {
                    unlink($oldPath);
                }
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'repairer_' . $repairerId . '_' . time() . '.' . $extension;
            $filePath = $uploadDir . $filename;
            $dbPath = 'uploads/profile_pictures/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file']);
                exit;
            }

            // Update database
            $result = $this->repairerModel->updateProfilePicture($repairerId, $dbPath);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile photo updated successfully',
                    'data' => [
                        'profilePicture' => $dbPath,
                        'url' => '/2nd-Year-Group-Project/FixLanka/' . $dbPath
                    ]
                ]);
            } else {
                // Clean up file if DB update failed
                if (file_exists($filePath)) unlink($filePath);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update profile picture in database']);
            }
        } catch (Exception $e) {
            error_log('Error in RepairerController::uploadPhoto: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'An error occurred while uploading photo']);
        }
        exit;
    }
}
