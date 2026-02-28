<?php
/**
 * AdReportController.php - FIXED VERSION
 * ✅ Updated to match actual ad_reports table structure
 */

class AdReportController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/AdReportModel.php';
        $this->model = new AdReportModel($this->pdo);
    }

    /**
     * Handle POST requests with strict validation
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'update_report':
                    return $this->updateReport();
                
                case 'reject_report':
                    return $this->rejectReport();
                
                case 'suspend_ad':
                    return $this->suspendAdvertisement();
                
                case 'delete_ad':
                    return $this->deleteAdvertisement();
                
                case 'escalate':
                    return $this->escalateToAdmin();
                
                default:
                    $_SESSION['error_message'] = 'Invalid action';
                    return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            error_log("Controller error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update report status and notes
     */
    private function updateReport()
    {
        if (empty($_POST['report_id'])) {
            $_SESSION['error_message'] = 'Report ID is required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        
        // Get current report to validate
        $currentReport = $this->model->getReportById($report_id);
        if (!$currentReport) {
            $_SESSION['error_message'] = 'Report not found';
            return false;
        }

        // Check if report is in final state
        if (in_array($currentReport['status'], ['resolved', 'dismissed'])) {
            $_SESSION['error_message'] = '🚫 Cannot modify a ' . $currentReport['status'] . ' report';
            return false;
        }

        $data = [
            'status' => $_POST['status'] ?? 'pending',
            'severity' => $_POST['priority'] ?? 'medium',
            'resolution_notes' => trim($_POST['moderator_notes'] ?? ''),
            'moderator_id' => $_SESSION['moderator_id'] ?? 1
        ];

        // Validate status
        $validStatuses = ['pending', 'investigating', 'resolved', 'dismissed', 'escalated'];
        if (!in_array($data['status'], $validStatuses)) {
            $_SESSION['error_message'] = 'Invalid status';
            return false;
        }

        // Validate severity
        $validSeverities = ['low', 'medium', 'high', 'critical'];
        if (!in_array($data['severity'], $validSeverities)) {
            $_SESSION['error_message'] = 'Invalid severity';
            return false;
        }

        $result = $this->model->updateReport($report_id, $data);
        
        if ($result['success']) {
            $_SESSION['success_message'] = '✅ ' . $result['message'];
            return true;
        } else {
            $_SESSION['error_message'] = '❌ ' . $result['message'];
            return false;
        }
    }

    /**
     * Dismiss the report (not the advertisement)
     */
    private function rejectReport()
    {
        if (empty($_POST['report_id'])) {
            $_SESSION['error_message'] = 'Report ID is required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        $reason = trim($_POST['moderator_notes'] ?? 'Report dismissed after investigation');

        $data = [
            'status' => 'dismissed',
            'severity' => $_POST['priority'] ?? 'medium',
            'resolution_notes' => $reason,
            'moderator_id' => $_SESSION['moderator_id'] ?? 1
        ];

        $result = $this->model->updateReport($report_id, $data);
        
        if ($result['success']) {
            $_SESSION['success_message'] = '✅ Report dismissed successfully';
            return true;
        } else {
            $_SESSION['error_message'] = '❌ ' . $result['message'];
            return false;
        }
    }

    /**
     * Suspend the advertisement
     */
    private function suspendAdvertisement()
    {
        if (empty($_POST['report_id']) || empty($_POST['ad_id'])) {
            $_SESSION['error_message'] = 'Report ID and Ad ID are required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        $ad_id = intval($_POST['ad_id']);
        $reason = trim($_POST['moderator_notes'] ?? 'Suspended due to policy violation');

        // Update advertisement status to suspended
        $adResult = $this->model->updateAdStatus($ad_id, 'suspended', $reason);
        
        if (!$adResult['success']) {
            $_SESSION['error_message'] = '❌ ' . $adResult['message'];
            return false;
        }

        // Update report status to resolved
        $reportData = [
            'status' => 'resolved',
            'severity' => $_POST['priority'] ?? 'medium',
            'resolution_notes' => $reason . ' - Advertisement suspended',
            'moderator_id' => $_SESSION['moderator_id'] ?? 1
        ];

        $reportResult = $this->model->updateReport($report_id, $reportData);
        
        if ($reportResult['success']) {
            $_SESSION['success_message'] = '✅ Advertisement suspended and report resolved';
            return true;
        } else {
            $_SESSION['error_message'] = '❌ ' . $reportResult['message'];
            return false;
        }
    }

    /**
     * Delete the advertisement
     */
    private function deleteAdvertisement()
    {
        if (empty($_POST['report_id']) || empty($_POST['ad_id'])) {
            $_SESSION['error_message'] = 'Report ID and Ad ID are required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        $ad_id = intval($_POST['ad_id']);
        $reason = trim($_POST['moderator_notes'] ?? 'Deleted due to severe policy violation');

        // Check current ad status - can only delete suspended ads
        $currentAdStatus = $this->model->getAdStatus($ad_id);
        
        if (!in_array($currentAdStatus, ['suspended', 'active', 'inactive'])) {
            $_SESSION['error_message'] = "Cannot delete advertisement with status '{$currentAdStatus}'";
            return false;
        }

        // Update advertisement status to deleted
        $adResult = $this->model->updateAdStatus($ad_id, 'deleted', $reason);
        
        if (!$adResult['success']) {
            $_SESSION['error_message'] = '❌ ' . $adResult['message'];
            return false;
        }

        // Update report status to resolved
        $reportData = [
            'status' => 'resolved',
            'severity' => 'high',
            'resolution_notes' => $reason . ' - Advertisement deleted',
            'moderator_id' => $_SESSION['moderator_id'] ?? 1
        ];

        $reportResult = $this->model->updateReport($report_id, $reportData);
        
        if ($reportResult['success']) {
            $_SESSION['success_message'] = '✅ Advertisement deleted and report resolved';
            return true;
        } else {
            $_SESSION['error_message'] = '❌ ' . $reportResult['message'];
            return false;
        }
    }

    /**
     * Escalate to admin
     */
    private function escalateToAdmin()
    {
        if (empty($_POST['report_id'])) {
            $_SESSION['error_message'] = 'Report ID is required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        $reason = trim($_POST['moderator_notes'] ?? 'Escalated to admin for review');

        $data = [
            'status' => 'escalated',
            'severity' => 'high',
            'resolution_notes' => $reason,
            'moderator_id' => $_SESSION['moderator_id'] ?? 1
        ];

        $result = $this->model->updateReport($report_id, $data);
        
        if ($result['success']) {
            $_SESSION['success_message'] = '✅ Report escalated to admin successfully';
            return true;
        } else {
            $_SESSION['error_message'] = '❌ ' . $result['message'];
            return false;
        }
    }

    /**
     * Get formatted data for view
     * ✅ FIXED: Maps new database columns correctly
     */
    public function getViewData()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'severity' => $_GET['priority'] ?? '',
            'report_category' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $reports = $this->model->getAllReports($filters);

        // Format reports for frontend
        $formattedReports = array_map(function($report) {
            return [
                'id' => $report['report_id'],
                'ad_id' => $report['ad_id'],
                'ad_title' => $report['ad_title'] ?? 'Unknown Advertisement',
                'ad_status' => $report['ad_status'] ?? 'unknown',
                'company_name' => $report['company_name'] ?? 'Unknown Company',
                'provider_email' => $report['provider_email'] ?? '',
                'user_name' => 'User #' . $report['reporter_id'], // No name column
                'user_email' => '', // No email column
                'reporter_type' => $report['reporter_type'],
                'issue_type' => $report['report_category'],
                'description' => $report['description'],
                'evidence' => '', // No evidence column
                'priority' => $report['severity'],
                'status' => $report['report_status'],
                'moderator_notes' => $report['resolution_notes'] ?? '',
                'created_date' => $report['created_at'],
                'updated_date' => $report['updated_at'],
                'resolved_date' => $report['resolved_at'],
                'category' => 'advertisement'
            ];
        }, $reports);

        return [
            'reports' => $formattedReports,
            'statistics' => $this->model->getStatistics()
        ];
    }

    /**
     * Get success/error messages
     */
    public function getMessages()
    {
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        return [
            'message' => $success ?: $error,
            'type' => $success ? 'success' : ($error ? 'error' : '')
        ];
    }
}