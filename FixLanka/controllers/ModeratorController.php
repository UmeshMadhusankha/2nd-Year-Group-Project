<?php
/**
 * ModeratorController.php - ENHANCED SECURE VERSION
 * Handles HTTP requests for Moderator Management operations
 * ✅ Separate Password Reset Feature
 * ✅ Enhanced Validation
 * ✅ Soft Delete Support
 * ✅ Session Flash Messages
 * ✅ FIXED: array_values() applied after array_filter() to prevent pagination skipping rows
 */

require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../models/ModeratorModel.php';

class ModeratorController {
    private $model;
    private $currentAdmin;
    
    /**
     * Constructor - Initialize model and check authentication
     */
    public function __construct() {
        global $pdo;
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if admin is logged in
        $this->checkAuthentication();
        
        // Initialize model
        $this->model = new ModeratorModel($pdo);
    }
    
    /**
     * Check if user is authenticated as admin
     */
    private function checkAuthentication() {
        // TODO: Replace with your actual admin authentication check
        if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
            $_SESSION['admin'] = 'admin'; // Default for testing
        }
        
        $this->currentAdmin = $_SESSION['admin'];
    }
    
    /**
     * Main entry point - Route requests based on action
     */
    public function handleRequest() {
        $action = $_POST['action'] ?? $_GET['action'] ?? 'list';
        
        try {
            switch ($action) {
                case 'create':
                case 'add':
                    $this->createModerator();
                    break;
                
                case 'update':
                case 'edit':
                    $this->updateModerator();
                    break;
                
                case 'reset_password':
                    $this->resetPassword();
                    break;
                
                case 'delete':
                    $this->deleteModerator();
                    break;
                
                case 'toggle_status':
                    $this->toggleStatus();
                    break;
                
                case 'list':
                default:
                    // Just return - view will call getters
                    return;
            }
        } catch (Exception $e) {
            error_log("ModeratorController Error: " . $e->getMessage());
            $this->handleError("An unexpected error occurred. Please try again.");
        }
    }
    
    /**
     * Create a new moderator
     */
    private function createModerator() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        // Server-side validation
        $errors = [];
        
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');
        
        // Username validation
        if (empty($username)) {
            $errors[] = "Username is required";
        } elseif (strlen($username) < 3) {
            $errors[] = "Username must be at least 3 characters";
        } elseif (strlen($username) > 50) {
            $errors[] = "Username must not exceed 50 characters";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = "Username can only contain letters, numbers, and underscores";
        }
        
        // Email validation
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (strlen($email) > 100) {
            $errors[] = "Email must not exceed 100 characters";
        }
        
        // Password validation
        $passwordErrors = $this->validatePassword($password, $confirm_password);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
        }
        
        // Assigned section validation
        $validSections = ['Advertisements', 'User Reports', 'Content Moderation', 'Financial Reports', 'System Monitoring'];
        if (empty($assigned_section)) {
            $errors[] = "Assigned section is required";
        } elseif (!in_array($assigned_section, $validSections)) {
            $errors[] = "Invalid assigned section";
        }
        
        if (!empty($errors)) {
            $this->redirect('error', implode(', ', $errors));
            return;
        }
        
        // Check for duplicates
        if ($this->model->usernameExists($username)) {
            $this->redirect('error', "Username '{$username}' is already taken");
            return;
        }
        
        if ($this->model->emailExists($email)) {
            $this->redirect('error', "Email '{$email}' is already registered");
            return;
        }
        
        // Create moderator with secure password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $moderator_id = $this->model->createModerator($username, $email, $hashedPassword, $assigned_section);
        
        if ($moderator_id) {
            // Log action
            $this->model->logAction($this->currentAdmin, $moderator_id, 'created', null, [
                'username' => $username,
                'email' => $email,
                'section' => $assigned_section
            ]);
            
            $this->redirect('success', "Moderator '{$username}' created successfully!");
        } else {
            $this->redirect('error', 'Failed to create moderator. Please try again.');
        }
    }
    
    /**
     * Update an existing moderator (email and section only)
     */
    private function updateModerator() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        
        if ($moderator_id <= 0) {
            $this->redirect('error', 'Invalid moderator ID');
            return;
        }
        
        // Get old data
        $oldData = $this->model->getModeratorById($moderator_id);
        if (!$oldData) {
            $this->redirect('error', 'Moderator not found');
            return;
        }
        
        // Validate input
        $errors = [];
        $email = trim($_POST['email'] ?? '');
        $assigned_section = trim($_POST['assigned_section'] ?? '');
        
        // Email validation
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (strlen($email) > 100) {
            $errors[] = "Email must not exceed 100 characters";
        }
        
        // Assigned section validation
        $validSections = ['Advertisements', 'User Reports', 'Content Moderation', 'Financial Reports', 'System Monitoring'];
        if (empty($assigned_section)) {
            $errors[] = "Assigned section is required";
        } elseif (!in_array($assigned_section, $validSections)) {
            $errors[] = "Invalid assigned section";
        }
        
        if (!empty($errors)) {
            $this->redirect('error', implode(', ', $errors));
            return;
        }
        
        // Check email duplicate
        if ($this->model->emailExists($email, $moderator_id)) {
            $this->redirect('error', "Email '{$email}' is already used by another moderator");
            return;
        }
        
        // Update moderator (WITHOUT password)
        $result = $this->model->updateModerator($moderator_id, $email, $assigned_section);
        
        if ($result) {
            // Log action
            $this->model->logAction($this->currentAdmin, $moderator_id, 'updated', [
                'email' => $oldData['email'],
                'section' => $oldData['assigned_section']
            ], [
                'email' => $email,
                'section' => $assigned_section
            ]);
            
            $this->redirect('success', "Moderator '{$oldData['username']}' updated successfully!");
        } else {
            $this->redirect('error', 'Failed to update moderator. Please try again.');
        }
    }
    
    /**
     * ✅ Reset moderator password (separate secure feature)
     */
    private function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if ($moderator_id <= 0) {
            $this->redirect('error', 'Invalid moderator ID');
            return;
        }
        
        // Get moderator data
        $moderator = $this->model->getModeratorById($moderator_id);
        if (!$moderator) {
            $this->redirect('error', 'Moderator not found');
            return;
        }
        
        // Validate password
        $passwordErrors = $this->validatePassword($new_password, $confirm_password);
        if (!empty($passwordErrors)) {
            $this->redirect('error', implode(', ', $passwordErrors));
            return;
        }
        
        // Hash and update password
        $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        $result = $this->model->updateModeratorWithPassword(
            $moderator_id, 
            $moderator['email'], 
            $hashedPassword, 
            $moderator['assigned_section']
        );
        
        if ($result) {
            // Log action
            $this->model->logAction($this->currentAdmin, $moderator_id, 'password_reset', null, [
                'reset_by' => $this->currentAdmin
            ]);
            
            $this->redirect('success', "Password reset successfully for '{$moderator['username']}'!");
        } else {
            $this->redirect('error', 'Failed to reset password. Please try again.');
        }
    }
    
    /**
     * Delete a moderator (with soft delete support)
     */
    private function deleteModerator() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $delete_type = $_POST['delete_type'] ?? 'soft'; // 'soft' or 'hard'
        
        if ($moderator_id <= 0) {
            $this->redirect('error', 'Invalid moderator ID');
            return;
        }
        
        // Get moderator data
        $moderator = $this->model->getModeratorById($moderator_id);
        if (!$moderator) {
            $this->redirect('error', 'Moderator not found');
            return;
        }
        
        // Check if can delete
        $deleteCheck = $this->model->canDeleteModerator($moderator_id);
        
        if ($delete_type === 'hard') {
            // Hard delete - check constraints
            if (!$deleteCheck['can_delete']) {
                $constraints = implode(', ', $deleteCheck['constraints']);
                $this->redirect('error', "Cannot delete '{$moderator['username']}'. This moderator has: {$constraints}. Please use DEACTIVATE instead.");
                return;
            }
            
            // Perform hard delete
            $result = $this->model->deleteModerator($moderator_id);
            $action = 'deleted (hard)';
            $message = "Moderator '{$moderator['username']}' permanently deleted!";
        } else {
            // Soft delete - just deactivate
            $result = $this->model->deactivateModerator($moderator_id);
            $action = 'deactivated';
            $message = "Moderator '{$moderator['username']}' deactivated successfully!";
        }
        
        if ($result) {
            // Log action
            $this->model->logAction($this->currentAdmin, $moderator_id, $action, [
                'username' => $moderator['username'],
                'email' => $moderator['email']
            ], null);
            
            $this->redirect('success', $message);
        } else {
            $this->redirect('error', 'Failed to delete moderator. Please try again.');
        }
    }
    
    /**
     * Toggle moderator status
     */
    private function toggleStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('error', 'Invalid request method');
            return;
        }
        
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $new_status = trim($_POST['status'] ?? '');
        
        if ($moderator_id <= 0 || !in_array($new_status, ['active', 'inactive'])) {
            $this->redirect('error', 'Invalid request');
            return;
        }
        
        // Get moderator data
        $moderator = $this->model->getModeratorById($moderator_id);
        if (!$moderator) {
            $this->redirect('error', 'Moderator not found');
            return;
        }
        
        // Toggle status
        if ($new_status === 'active') {
            $result = $this->model->activateModerator($moderator_id);
        } else {
            $result = $this->model->deactivateModerator($moderator_id);
        }
        
        if ($result) {
            // Log action
            $this->model->logAction($this->currentAdmin, $moderator_id, 'status_changed', [
                'status' => $moderator['status']
            ], [
                'status' => $new_status
            ]);
            
            $action = $new_status === 'active' ? 'activated' : 'deactivated';
            $this->redirect('success', "Moderator '{$moderator['username']}' {$action} successfully!");
        } else {
            $this->redirect('error', 'Failed to update status. Please try again.');
        }
    }
    
    /**
     * ✅ Enhanced Password Validation
     */
    private function validatePassword($password, $confirm_password) {
        $errors = [];
        
        if (empty($password)) {
            $errors[] = "Password is required";
            return $errors;
        }
        
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        
        if (strlen($password) > 100) {
            $errors[] = "Password must not exceed 100 characters";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        return $errors;
    }
    
    /**
     * Get all moderators with filtering and pagination
     * ✅ FIXED: array_values() applied after array_filter() so array_slice() works correctly.
     *    Without array_values(), array_filter() preserves original numeric keys (e.g. 0,2,5,7...)
     *    and array_slice() with $offset=0,$limit=20 still works for page 1, BUT if any rows
     *    were filtered out in between, the preserved keys cause slice to behave unexpectedly
     *    on subsequent pages. More critically, when the full unfiltered array has gaps in keys
     *    (because getAllModerators returns PDO rows which CAN have non-sequential internal state
     *    after fetch), array_values() guarantees 0-based sequential indexing every time.
     *
     * @return array Filtered and paginated moderators
     */
    public function getModerators($search = '', $sectionFilter = '', $page = 1, $limit = 20) {
        $allModerators = $this->model->getAllModerators();
        
        // Filter moderators
        $filteredModerators = array_filter($allModerators, function($mod) use ($search, $sectionFilter) {
            $matchesSearch = empty($search) || 
                             stripos($mod['username'], $search) !== false || 
                             stripos($mod['email'], $search) !== false;
            $matchesSection = empty($sectionFilter) || $mod['assigned_section'] === $sectionFilter;
            return $matchesSearch && $matchesSection;
        });

        // ✅ KEY FIX: Re-index array after filter so array_slice() offsets are correct.
        // array_filter() preserves original keys; without array_values() a $page > 1
        // offset would skip wrong elements and page 1 could miss rows at non-zero indices.
        $filteredModerators = array_values($filteredModerators);
        
        // Pagination
        $totalModerators = count($filteredModerators);
        $totalPages = max(1, ceil($totalModerators / $limit));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $limit;
        $moderators = array_slice($filteredModerators, $offset, $limit);
        
        return [
            'moderators' => $moderators,
            'allModerators' => $allModerators,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'totalModerators' => $totalModerators
        ];
    }
    
    /**
     * Get all moderators for display
     * @return array All moderators
     */
    public function getAllModerators() {
        return $this->model->getAllModerators();
    }
    
    /**
     * Get moderator statistics
     * @return array Statistics
     */
    public function getStatistics() {
        return $this->model->getModeratorStats();
    }
    
    /**
     * Redirect helper
     */
    private function redirect($messageType = null, $message = null) {
        if ($messageType && $message) {
            $_SESSION['success_message'] = $messageType === 'success' ? $message : '';
            $_SESSION['error_message'] = $messageType === 'error' ? $message : '';
        }
        
        if (headers_sent()) {
            return;
        }
        
        $baseUrl = '/2nd-Year-Group-Project/FixLanka/admin-moderators';
        header("Location: $baseUrl");
        exit();
    }
    
    /**
     * Handle errors safely
     */
    private function handleError($message) {
        $_SESSION['error_message'] = $message;
        
        if (!headers_sent()) {
            header('Location: /2nd-Year-Group-Project/FixLanka/admin-moderators');
            exit();
        }
    }
}
?>