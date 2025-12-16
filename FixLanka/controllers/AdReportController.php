<?php
/**
 * AdReportController.php
 * Handles business logic for ad reports
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
     * Handle POST requests (form submissions)
     */
    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'update_report') {
                return $this->updateReport();
            }
            $_SESSION['error_message'] = 'Invalid action';
            return false;
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            return false;
        }
    }

    /**
     * Update report status, priority, and notes
     */
    private function updateReport()
    {
        if (empty($_POST['report_id'])) {
            $_SESSION['error_message'] = 'Report ID is required';
            return false;
        }

        $report_id = intval($_POST['report_id']);
        $data = [
            'status' => $_POST['status'] ?? 'pending',
            'priority' => $_POST['priority'] ?? 'medium',
            'moderator_notes' => $_POST['moderator_notes'] ?? ''
        ];

        if ($this->model->updateReport($report_id, $data)) {
            $_SESSION['success_message'] = '✅ Report updated successfully!';
            return true;
        } else {
            $_SESSION['error_message'] = 'Failed to update report';
            return false;
        }
    }

    /**
     * Get data for view
     */
    public function getViewData()
    {
        $filters = [
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? '',
            'issue_type' => $_GET['type'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $reports = $this->model->getAllReports($filters);

        // Format reports for frontend
        $formattedReports = array_map(function($report) {
            return [
                'id' => $report['report_id'],
                'ad_id' => $report['ad_id'],
                'ad_title' => $report['ad_title'] ?? 'Unknown Advertisement',
                'company_name' => $report['company_name'] ?? 'Unknown Company',
                'user_name' => $report['reporter_name'],
                'user_email' => $report['reporter_email'],
                'issue_type' => $report['issue_type'],
                'description' => $report['description'],
                'evidence' => $report['evidence'],
                'priority' => $report['priority'],
                'status' => $report['status'],
                'moderator_notes' => $report['moderator_notes'],
                'created_date' => $report['created_at'],
                'category' => 'advertisement'
            ];
        }, $reports);

        return [
            'reports' => $formattedReports
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