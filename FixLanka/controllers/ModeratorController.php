<?php
/**
 * ModeratorController.php - MVC Controller (Form-Based)
 * Handles ALL Moderator Management Actions
 * Uses SESSION messages and REDIRECTS (NO JSON)
 */

require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../models/ModeratorModel.php';

class ModeratorController
{
    private $model;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->model = new ModeratorModel($pdo);
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Handle ALL incoming POST requests
     */
    public function handleRequest()
    {
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method.';
            $this->redirectBack();
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'add':
                    $this->addModerator();
                    break;
                
                case 'update':
                    $this->updateModerator();
                    break;
                
                case 'delete':
                    $this->deleteModerator();
                    break;
                
                case 'toggle_status':
                    $this->toggleStatus();
                    break;
                
                default:
                    $_SESSION['error_message'] = 'Invalid action specified.';
                    $this->redirectBack();
            }
        } catch (Exception $e) {
            error_log("❌ ModeratorController Error: " . $e->getMessage());
            $_SESSION['error_message'] = 'An unexpected error occurred. Please try again.';
            $this->redirectBack();
        }
    }

    /**
     * Add new moderator
     */
    private function addModerator()
    {
        // Get and sanitize input
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');
        
        // Validation rules
        if (empty($username) || empty($email) || empty($password) || empty($assigned_section)) {
            $_SESSION['error_message'] = '❌ All fields are required.';
            $this->redirectBack();
        }
        
        if (strlen($username) < 3) {
            $_SESSION['error_message'] = '❌ Username must be at least 3 characters.';
            $this->redirectBack();
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $_SESSION['error_message'] = '❌ Username can only contain letters, numbers, and underscores.';
            $this->redirectBack();
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = '❌ Password must be at least 6 characters.';
            $this->redirectBack();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = '❌ Invalid email format.';
            $this->redirectBack();
        }
        
        $validSections = ['Advertisements', 'User Reports', 'Content Moderation', 'Financial Reports', 'System Monitoring'];
        if (!in_array($assigned_section, $validSections)) {
            $_SESSION['error_message'] = '❌ Invalid assigned section.';
            $this->redirectBack();
        }
        
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Check for duplicate username
            if ($this->model->usernameExists($username)) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = "❌ Username '{$username}' is already taken.";
                $this->redirectBack();
            }
            
            // Check for duplicate email
            if ($this->model->emailExists($email)) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = "❌ Email '{$email}' is already registered.";
                $this->redirectBack();
            }
            
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Create moderator
            $moderator_id = $this->model->createModerator($username, $email, $hashedPassword, $assigned_section);
            
            // Log action
            $this->model->logAction(
                $_SESSION['admin_username'] ?? 'admin',
                $moderator_id,
                'created',
                null,
                ['username' => $username, 'email' => $email, 'section' => $assigned_section]
            );
            
            // Commit transaction
            $this->pdo->commit();
            
            $_SESSION['success_message'] = "✅ Moderator '{$username}' created successfully!";
            $this->redirectBack();
            
        } catch (PDOException $e) {
            // Rollback on error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("❌ Error adding moderator: " . $e->getMessage());
            $_SESSION['error_message'] = '❌ Database error occurred. Could not create moderator.';
            $this->redirectBack();
        }
    }

    /**
     * Update moderator
     */
    private function updateModerator()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');
        
        // Validation
        if (!$moderator_id) {
            $_SESSION['error_message'] = '❌ Moderator ID is required.';
            $this->redirectBack();
        }
        
        if (empty($email) || empty($assigned_section)) {
            $_SESSION['error_message'] = '❌ Email and assigned section are required.';
            $this->redirectBack();
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = '❌ Invalid email format.';
            $this->redirectBack();
        }
        
        if (!empty($password) && strlen($password) < 6) {
            $_SESSION['error_message'] = '❌ Password must be at least 6 characters.';
            $this->redirectBack();
        }
        
        $validSections = ['Advertisements', 'User Reports', 'Content Moderation', 'Financial Reports', 'System Monitoring'];
        if (!in_array($assigned_section, $validSections)) {
            $_SESSION['error_message'] = '❌ Invalid assigned section.';
            $this->redirectBack();
        }
        
        try {
            // Start transaction
            $this->pdo->beginTransaction();
            
            // Check if moderator exists
            $oldData = $this->model->getModeratorById($moderator_id);
            if (!$oldData) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = '❌ Moderator not found.';
                $this->redirectBack();
            }
            
            // Check email duplicate
            if ($this->model->emailExists($email, $moderator_id)) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = "❌ Email '{$email}' is already used by another moderator.";
                $this->redirectBack();
            }
            
            // Update with or without password
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $this->model->updateModeratorWithPassword($moderator_id, $email, $hashedPassword, $assigned_section);
            } else {
                $this->model->updateModerator($moderator_id, $email, $assigned_section);
            }
            
            // Log action
            $this->model->logAction(
                $_SESSION['admin_username'] ?? 'admin',
                $moderator_id,
                'updated',
                ['email' => $oldData['email'], 'section' => $oldData['assigned_section']],
                ['email' => $email, 'section' => $assigned_section]
            );
            
            // Commit transaction
            $this->pdo->commit();
            
            $_SESSION['success_message'] = "✅ Moderator '{$oldData['username']}' updated successfully!";
            $this->redirectBack();
            
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("❌ Error updating moderator: " . $e->getMessage());
            $_SESSION['error_message'] = '❌ Database error occurred. Could not update moderator.';
            $this->redirectBack();
        }
    }

    /**
     * Delete moderator (with foreign key constraint checks)
     */
    private function deleteModerator()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        
        if (!$moderator_id) {
            $_SESSION['error_message'] = '❌ Moderator ID is required.';
            $this->redirectBack();
        }
        
        try {
            // Check if moderator exists (NO transaction yet)
            $moderator = $this->model->getModeratorById($moderator_id);
            if (!$moderator) {
                $_SESSION['error_message'] = '❌ Moderator not found.';
                $this->redirectBack();
            }
            
            // Check foreign key constraints
            $deleteCheck = $this->model->canDeleteModerator($moderator_id);
            
            if (!$deleteCheck['can_delete']) {
                $constraints = $deleteCheck['constraints'];
                $constraintList = implode(', ', $constraints);
                $_SESSION['error_message'] = "❌ Cannot delete '{$moderator['username']}'. This moderator has: {$constraintList}. Please DEACTIVATE instead.";
                $this->redirectBack();
            }
            
            // Safe to delete - Start transaction
            $this->pdo->beginTransaction();
            
            try {
                // Perform deletion
                $rowsAffected = $this->model->deleteModerator($moderator_id);
                
                if ($rowsAffected > 0) {
                    // Log deletion
                    $this->model->logAction(
                        $_SESSION['admin_username'] ?? 'admin',
                        $moderator_id,
                        'deleted',
                        ['username' => $moderator['username'], 'email' => $moderator['email']],
                        null
                    );
                    
                    $this->pdo->commit();
                    $_SESSION['success_message'] = "✅ Moderator '{$moderator['username']}' deleted successfully!";
                    $this->redirectBack();
                } else {
                    $this->pdo->rollBack();
                    $_SESSION['error_message'] = '❌ Failed to delete moderator. No rows affected.';
                    $this->redirectBack();
                }
                
            } catch (PDOException $deleteError) {
                $this->pdo->rollBack();
                
                // Check if it's a foreign key constraint error
                if ($deleteError->getCode() == '23000') {
                    $_SESSION['error_message'] = "❌ Cannot delete moderator. They have linked records in the system. Please deactivate instead.";
                } else {
                    $_SESSION['error_message'] = '❌ Database error during deletion: ' . $deleteError->getMessage();
                }
                $this->redirectBack();
            }
            
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("❌ Error in delete handler: " . $e->getMessage());
            $_SESSION['error_message'] = '❌ An error occurred while trying to delete the moderator.';
            $this->redirectBack();
        }
    }

    /**
     * Toggle moderator status (activate/deactivate)
     */
    private function toggleStatus()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $new_status = trim($_POST['status'] ?? '');
        
        // Validation
        if (!$moderator_id || !in_array($new_status, ['active', 'inactive'])) {
            $_SESSION['error_message'] = '❌ Invalid status or moderator ID.';
            $this->redirectBack();
        }
        
        try {
            $this->pdo->beginTransaction();
            
            $moderator = $this->model->getModeratorById($moderator_id);
            if (!$moderator) {
                $this->pdo->rollBack();
                $_SESSION['error_message'] = '❌ Moderator not found.';
                $this->redirectBack();
            }
            
            // Perform status toggle
            if ($new_status === 'active') {
                $this->model->activateModerator($moderator_id);
                $message = "✅ Moderator '{$moderator['username']}' activated successfully!";
            } else {
                $this->model->deactivateModerator($moderator_id);
                $message = "✅ Moderator '{$moderator['username']}' deactivated successfully!";
            }
            
            // Log action
            $this->model->logAction(
                $_SESSION['admin_username'] ?? 'admin',
                $moderator_id,
                'status_changed',
                ['status' => $moderator['status']],
                ['status' => $new_status]
            );
            
            $this->pdo->commit();
            $_SESSION['success_message'] = $message;
            $this->redirectBack();
            
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("❌ Error toggling status: " . $e->getMessage());
            $_SESSION['error_message'] = '❌ Failed to update status.';
            $this->redirectBack();
        }
    }

    /**
     * Redirect back to moderators page
     */
    private function redirectBack()
    {
        header('Location: /2nd-Year-Group-Project/FixLanka/admin-moderators');
        exit;
    }
}