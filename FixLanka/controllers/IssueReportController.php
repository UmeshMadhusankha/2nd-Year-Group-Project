<?php
/**
 * IssueReportController.php
 * Business Logic for Issues & Reports
 * MVC Architecture - Controller Layer (All Validations Here)
 * 
 * @version 2.1 - PRODUCTION READY (Fixed Delete Confirmation)
 * @safety Enforces strict status transitions, uses transactions
 */

require_once __DIR__ . '/../models/IssueReportModel.php';

class IssueReportController
{
    private $model;
    
    // =====================================================
    // STRICT STATUS TRANSITION RULES
    // =====================================================
    private $validTransitions = [
        'pending' => ['investigating', 'escalated'],
        'investigating' => ['resolved', 'escalated'],
        'escalated' => ['resolved'],
        'resolved' => [], // FINAL STATE - Cannot change
        'closed' => []    // FINAL STATE - Cannot change
    ];

    public function __construct($pdo)
    {
        $this->model = new IssueReportModel($pdo);
    }

    // =====================================================
    // REQUEST HANDLER
    // =====================================================

    /**
     * Handle POST requests
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'update_issue':
                $this->updateIssue();
                break;
            case 'delete_issue':
                $this->deleteIssue();
                break;
            default:
                $this->redirectWithMessage('Invalid action', 'error');
        }
    }

    // =====================================================
    // UPDATE ISSUE WITH STRICT VALIDATION
    // =====================================================

    /**
     * Update issue with full validation and notifications
     */
    private function updateIssue()
    {
        // 1. VALIDATE INPUT
        if (!isset($_POST['issue_id']) || !isset($_POST['status']) || !isset($_POST['priority'])) {
            $this->redirectWithMessage('Missing required fields', 'error');
            return;
        }

        $issueId = (int)$_POST['issue_id'];
        $newStatus = trim($_POST['status']);
        $newPriority = trim($_POST['priority']);
        $adminNotes = trim($_POST['admin_notes'] ?? '');
        $internalNotes = trim($_POST['admin_internal_notes'] ?? '');

        // 2. VALIDATE PRIORITY
        $validPriorities = ['low', 'medium', 'high'];
        if (!in_array($newPriority, $validPriorities)) {
            $this->redirectWithMessage('Invalid priority level', 'error');
            return;
        }

        // 3. VALIDATE STATUS
        $validStatuses = ['pending', 'investigating', 'resolved', 'escalated', 'closed'];
        if (!in_array($newStatus, $validStatuses)) {
            $this->redirectWithMessage('Invalid status value', 'error');
            return;
        }

        try {
            // 4. GET CURRENT ISSUE
            $currentIssue = $this->model->getIssueById($issueId);
            if (!$currentIssue) {
                $this->redirectWithMessage('Issue not found', 'error');
                return;
            }

            $oldStatus = $currentIssue['status'];
            $oldPriority = $currentIssue['priority'];

            // 5. CHECK STATUS TRANSITION VALIDITY
            if ($oldStatus !== $newStatus) {
                if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                    $allowed = $this->getAllowedTransitionsText($oldStatus);
                    $this->redirectWithMessage(
                        "Invalid status transition: '{$oldStatus}' to '{$newStatus}'. Allowed: {$allowed}",
                        'error'
                    );
                    return;
                }
            }

            // 6. BEGIN TRANSACTION (SAFETY)
            $this->model->beginTransaction();

            try {
                // 7. UPDATE ISSUE
                $this->model->updateIssue($issueId, $newStatus, $newPriority, $adminNotes, $internalNotes);

                // 8. LOG STATUS HISTORY (if status changed)
                if ($oldStatus !== $newStatus) {
                    $changeNotes = !empty($adminNotes) ? $adminNotes : 'Status updated by admin';
                    $this->model->logStatusHistory(
                        $issueId,
                        $oldStatus,
                        $newStatus,
                        'admin', // TODO: Get from session
                        $changeNotes
                    );

                    // 9. SEND NOTIFICATIONS
                    $this->sendStatusChangeNotifications($issueId, $currentIssue, $oldStatus, $newStatus);
                }

                // 10. COMMIT TRANSACTION
                $this->model->commit();

                // 11. SUCCESS MESSAGE
                $statusMsg = ($oldStatus !== $newStatus) ? " Status: {$oldStatus} → {$newStatus}." : "";
                $priorityMsg = ($oldPriority !== $newPriority) ? " Priority: {$oldPriority} → {$newPriority}." : "";
                $this->redirectWithMessage("Issue updated successfully!{$statusMsg}{$priorityMsg}", 'success');

            } catch (Exception $e) {
                // ROLLBACK ON ERROR (PREVENT XAMPP CRASH)
                $this->model->rollback();
                error_log("Transaction Error - updateIssue: " . $e->getMessage());
                $this->redirectWithMessage('Database error. Changes rolled back for safety.', 'error');
            }

        } catch (Exception $e) {
            error_log("Controller Error - updateIssue: " . $e->getMessage());
            $this->redirectWithMessage('System error occurred', 'error');
        }
    }

    // =====================================================
    // DELETE ISSUE WITH CONFIRMATION
    // =====================================================

    /**
     * Delete issue with transaction safety
     * ✅ FIXED: Proper confirmation check
     */
    private function deleteIssue()
    {
        // Check for issue_id
        if (!isset($_POST['issue_id'])) {
            $this->redirectWithMessage('Issue ID is required', 'error');
            return;
        }

        // Check for confirmation (value must be "yes")
        if (!isset($_POST['confirm_delete']) || $_POST['confirm_delete'] !== 'yes') {
            $this->redirectWithMessage('Delete confirmation required', 'error');
            return;
        }

        $issueId = (int)$_POST['issue_id'];

        try {
            // Check if issue exists
            $issue = $this->model->getIssueById($issueId);
            if (!$issue) {
                $this->redirectWithMessage('Issue not found', 'error');
                return;
            }

            // BEGIN TRANSACTION
            $this->model->beginTransaction();

            try {
                // Delete issue (CASCADE will delete history and notifications)
                $this->model->deleteIssue($issueId);

                // COMMIT
                $this->model->commit();

                $this->redirectWithMessage('Issue permanently deleted', 'success');

            } catch (Exception $e) {
                $this->model->rollback();
                error_log("Transaction Error - deleteIssue: " . $e->getMessage());
                $this->redirectWithMessage('Failed to delete issue. Changes rolled back.', 'error');
            }

        } catch (Exception $e) {
            error_log("Controller Error - deleteIssue: " . $e->getMessage());
            $this->redirectWithMessage('System error occurred', 'error');
        }
    }

    // =====================================================
    // STATUS TRANSITION VALIDATION
    // =====================================================

    /**
     * Check if status transition is valid
     */
    private function isValidStatusTransition($oldStatus, $newStatus)
    {
        // Same status is always valid
        if ($oldStatus === $newStatus) {
            return true;
        }

        // Check if transition is allowed
        if (isset($this->validTransitions[$oldStatus])) {
            return in_array($newStatus, $this->validTransitions[$oldStatus]);
        }

        return false;
    }

    /**
     * Get allowed transitions as text
     */
    private function getAllowedTransitionsText($status)
    {
        if (isset($this->validTransitions[$status]) && !empty($this->validTransitions[$status])) {
            return implode(', ', $this->validTransitions[$status]);
        }
        return 'None (Final state)';
    }

    /**
     * Get valid next statuses for UI dropdown
     */
    public function getValidNextStatuses($currentStatus)
    {
        $allowed = $this->validTransitions[$currentStatus] ?? [];
        // Always allow keeping current status
        return array_merge([$currentStatus], $allowed);
    }

    // =====================================================
    // NOTIFICATION SYSTEM
    // =====================================================

    /**
     * Send notifications to reporter and reported party
     */
    private function sendStatusChangeNotifications($issueId, $issue, $oldStatus, $newStatus)
    {
        $statusChange = ucfirst($oldStatus) . ' → ' . ucfirst($newStatus);

        // Notification to REPORTER
        $reporterMessage = "Your issue report (ID: #{$issueId}) status changed: {$statusChange}";
        $this->model->createNotification(
            $issueId,
            'reporter',
            $issue['reportedBy_id'],
            $reporterMessage,
            $statusChange
        );

        // Notification to REPORTED PARTY (if applicable)
        if (!empty($issue['target_id'])) {
            $reportedMessage = "An issue reported against you (ID: #{$issueId}) status changed: {$statusChange}";
            $this->model->createNotification(
                $issueId,
                'reporter', // Using 'reporter' as it's in your ENUM
                $issue['target_id'],
                $reportedMessage,
                $statusChange
            );
        }
    }

    // =====================================================
    // DATA RETRIEVAL METHODS
    // =====================================================

    public function getIssues()
    {
        $statusFilter = $_GET['status'] ?? '';
        $priorityFilter = $_GET['priority'] ?? '';
        $search = $_GET['search'] ?? '';

        return $this->model->getAllIssues($statusFilter, $priorityFilter, $search);
    }

    public function getStatistics()
    {
        return $this->model->getStatistics();
    }

    public function getStatusHistory($issueId)
    {
        return $this->model->getStatusHistory($issueId);
    }

    // =====================================================
    // MESSAGE SYSTEM
    // =====================================================

    public function getMessage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['issue_message'])) {
            $message = $_SESSION['issue_message'];
            $type = $_SESSION['issue_message_type'] ?? 'info';
            unset($_SESSION['issue_message']);
            unset($_SESSION['issue_message_type']);
            return ['message' => $message, 'type' => $type];
        }
        return null;
    }

    private function redirectWithMessage($message, $type)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['issue_message'] = $message;
        $_SESSION['issue_message_type'] = $type;
        header('Location: /2nd-Year-Group-Project/FixLanka/views/admin/issues.php');
        exit();
    }
}