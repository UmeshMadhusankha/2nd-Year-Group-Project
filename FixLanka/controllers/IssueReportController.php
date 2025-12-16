<?php
/**
 * IssueReportController.php
 * Handles business logic for Issues & Reports page
 * Pure PHP - No AJAX - Uses POST and redirects
 */

require_once __DIR__ . '/../models/IssueReportModel.php';

class IssueReportController
{
    private $model;
    private $message = '';
    private $messageType = '';

    /**
     * Constructor
     */
    public function __construct($pdo)
    {
        $this->model = new IssueReportModel($pdo);
    }

    /**
     * Handle POST requests (form submissions)
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        // Check which action was submitted
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_issue':
                    $this->updateIssue();
                    break;
                case 'delete_issue':
                    $this->deleteIssue();
                    break;
                case 'delete_user':
                    $this->deleteUser();
                    break;
                default:
                    $this->message = 'Invalid action';
                    $this->messageType = 'error';
            }
        }
    }

    /**
     * Update issue status and priority
     */
    private function updateIssue()
    {
        // Validate input
        if (!isset($_POST['issue_id']) || !isset($_POST['status']) || !isset($_POST['priority'])) {
            $this->redirectWithMessage('Missing required fields', 'error');
            return;
        }

        $issueId = (int)$_POST['issue_id'];
        $status = $_POST['status'];
        $priority = $_POST['priority'];

        // Validate status
        $validStatuses = ['pending', 'investigating', 'resolved', 'escalated', 'closed'];
        if (!in_array($status, $validStatuses)) {
            $this->redirectWithMessage('Invalid status', 'error');
            return;
        }

        // Validate priority
        $validPriorities = ['low', 'medium', 'high'];
        if (!in_array($priority, $validPriorities)) {
            $this->redirectWithMessage('Invalid priority', 'error');
            return;
        }

        // Update in database
        $success = $this->model->updateIssue($issueId, $status, $priority);

        if ($success) {
            $this->redirectWithMessage('Issue updated successfully', 'success');
        } else {
            $this->redirectWithMessage('Failed to update issue', 'error');
        }
    }

    /**
     * Delete an issue
     */
    private function deleteIssue()
    {
        if (!isset($_POST['issue_id'])) {
            $this->redirectWithMessage('Missing issue ID', 'error');
            return;
        }

        $issueId = (int)$_POST['issue_id'];
        $success = $this->model->deleteIssue($issueId);

        if ($success) {
            $this->redirectWithMessage('Issue deleted successfully', 'success');
        } else {
            $this->redirectWithMessage('Failed to delete issue', 'error');
        }
    }

    /**
     * Delete user account
     */
    private function deleteUser()
    {
        if (!isset($_POST['user_id'])) {
            $this->redirectWithMessage('Missing user ID', 'error');
            return;
        }

        $userId = (int)$_POST['user_id'];
        
        // Delete the user (CASCADE will delete related issues)
        $success = $this->model->deleteUser($userId);

        if ($success) {
            $this->redirectWithMessage('User account deleted successfully', 'success');
        } else {
            $this->redirectWithMessage('Failed to delete user account', 'error');
        }
    }

    /**
     * Redirect with message
     */
    private function redirectWithMessage($message, $type)
    {
        session_start();
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
        header('Location: /2nd-Year-Group-Project/FixLanka/views/admin/issues.php');
        exit();
    }

    /**
     * Get all issues with filters
     */
    public function getIssues()
    {
        $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
        $priorityFilter = isset($_GET['priority']) ? $_GET['priority'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        return $this->model->getAllIssues($statusFilter, $priorityFilter, $search);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        return $this->model->getStatistics();
    }

    /**
     * Get message from session
     */
    public function getMessage()
    {
        session_start();
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $type = $_SESSION['message_type'] ?? 'info';
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }
}