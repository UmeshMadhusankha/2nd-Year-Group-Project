<?php
// ModeratorController.php - Business logic layer for moderator operations
// Handles validation, password hashing, and coordinates between API and Model

require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../models/ModeratorModel.php';

class ModeratorController
{
    private $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new ModeratorModel($pdo);
    }

    /**
     * Get all moderators
     */
    public function getAllModerators()
    {
        try {
            $moderators = $this->model->getAllModerators();
            $this->jsonResponse(['success' => true, 'data' => $moderators]);
        } catch (PDOException $e) {
            error_log("Error fetching moderators: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch moderators'], 500);
        }
    }

    /**
     * Add new moderator
     */
    public function addModerator()
    {
        // Get and sanitize input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($assigned_section)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        try {
            // Check if username exists
            if ($this->model->usernameExists($username)) {
                $this->jsonResponse(['success' => false, 'message' => 'Username already exists'], 409);
                return;
            }

            // Check if email exists
            if ($this->model->emailExists($email)) {
                $this->jsonResponse(['success' => false, 'message' => 'Email already exists'], 409);
                return;
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create moderator
            $moderator_id = $this->model->createModerator($username, $email, $hashedPassword, $assigned_section);

            // Fetch the newly created moderator
            $moderator = $this->model->getModeratorById($moderator_id);

            $this->jsonResponse(['success' => true, 'message' => 'Moderator added successfully', 'data' => $moderator]);
        } catch (PDOException $e) {
            error_log("Error adding moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add moderator'], 500);
        }
    }

    /**
     * Update moderator
     */
    public function updateModerator()
    {
        // Get and sanitize input
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');

        // Debug logging
        error_log("UPDATE Request - ID: $moderator_id, Email: $email, Section: $assigned_section, Has Password: " . (!empty($password) ? 'YES' : 'NO'));

        // Validation
        if (!$moderator_id || empty($email) || empty($assigned_section)) {
            $this->jsonResponse(['success' => false, 'message' => 'Required fields are missing'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        try {
            // Check if email exists for other moderators
            if ($this->model->emailExists($email, $moderator_id)) {
                $this->jsonResponse(['success' => false, 'message' => 'Email already exists'], 409);
                return;
            }

            // Update with or without password change
            if (!empty($password)) {
                // Validate password length
                if (strlen($password) < 6) {
                    $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
                    return;
                }

                // Hash password and update
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $this->model->updateModeratorWithPassword($moderator_id, $email, $hashedPassword, $assigned_section);
                error_log("Updated moderator WITH password change");
            } else {
                // Update without changing password
                $this->model->updateModerator($moderator_id, $email, $assigned_section);
                error_log("Updated moderator WITHOUT password change");
            }

            // Fetch updated moderator
            $moderator = $this->model->getModeratorById($moderator_id);

            $this->jsonResponse(['success' => true, 'message' => 'Moderator updated successfully', 'data' => $moderator]);
        } catch (PDOException $e) {
            error_log("Error updating moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update moderator'], 500);
        }
    }

    /**
     * Delete moderator
     */
    public function deleteModerator()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);

        // Validation
        if (!$moderator_id) {
            $this->jsonResponse(['success' => false, 'message' => 'Moderator ID is required'], 400);
            return;
        }

        try {
            $rowsAffected = $this->model->deleteModerator($moderator_id);

            if ($rowsAffected > 0) {
                $this->jsonResponse(['success' => true, 'message' => 'Moderator deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Moderator not found'], 404);
            }
        } catch (PDOException $e) {
            error_log("Error deleting moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete moderator'], 500);
        }
    }

    /**
     * Helper function to send JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
