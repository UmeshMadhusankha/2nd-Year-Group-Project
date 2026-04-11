<?php
/**
 * AdReportController.php
 * Moderator advertisement reports controller (MVC)
 */

require_once __DIR__ . '/../models/AdReportModel.php';

class AdReportController
{
    private AdReportModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new AdReportModel($pdo);
    }

    public function handlePostRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'moderator') {
            $this->setMessage('Unauthorized. Moderator access required.', 'error');
            return;
        }

        $action = $_POST['action'] ?? '';
        $adId = (int)($_POST['ad_id'] ?? 0);
        $reportId = (int)($_POST['report_id'] ?? 0);
        $moderatorNotes = trim((string)($_POST['moderator_notes'] ?? ''));
        $handledBy = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

        try {
            switch ($action) {
                case 'update_report':
                    $this->updateReport();
                    break;
                case 'reject_report':
                    if ($reportId <= 0) {
                        throw new InvalidArgumentException('Report ID is required.');
                    }
                    $changed = $this->model->dismissReport($reportId, $moderatorNotes, $handledBy);
                    $this->setMessage($changed ? 'Report dismissed.' : 'No report updated.', $changed ? 'success' : 'error');
                    break;
                case 'escalate':
                    if ($reportId <= 0) {
                        throw new InvalidArgumentException('Report ID is required.');
                    }
                    $changed = $this->model->escalateReport($reportId, $moderatorNotes, $handledBy);
                    $this->setMessage($changed ? 'Report escalated.' : 'No report updated.', $changed ? 'success' : 'error');
                    break;
                case 'suspend_ad':
                    if ($adId <= 0) {
                        throw new InvalidArgumentException('Advertisement ID is required.');
                    }
                    $changed = $this->model->suspendAdvertisement($adId);
                    if ($changed && $reportId > 0) {
                        $notes = $moderatorNotes !== '' ? $moderatorNotes : 'Advertisement suspended based on report.';
                        $this->model->resolveReport($reportId, $notes, $handledBy);
                    }
                    $this->setMessage($changed ? 'Advertisement suspended.' : 'No advertisement updated.', $changed ? 'success' : 'error');
                    break;
                case 'delete_ad':
                    if ($adId <= 0) {
                        throw new InvalidArgumentException('Advertisement ID is required.');
                    }
                    $deleted = $this->model->deleteAdvertisement($adId);
                    $this->setMessage($deleted ? 'Advertisement deleted successfully.' : 'Advertisement not found.', $deleted ? 'success' : 'error');
                    break;
                default:
                    $this->setMessage('Invalid action.', 'error');
            }
        } catch (Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }
    }

    public function getViewData(): array
    {
        $reports = $this->model->getReports();
        $reports = $this->model->enrichReportsWithAdStatus($reports);

        return [
            'reports' => $reports,
            'statistics' => $this->model->getStatistics($reports),
        ];
    }

    public function getMessages(): array
    {
        $message = $_SESSION['ad_report_message'] ?? '';
        $type = $_SESSION['ad_report_message_type'] ?? '';
        unset($_SESSION['ad_report_message'], $_SESSION['ad_report_message_type']);
        return ['message' => $message, 'type' => $type];
    }

    private function updateReport(): void
    {
        $reportId = (int)($_POST['report_id'] ?? 0);
        $status = trim((string)($_POST['status'] ?? ''));
        $priority = strtolower(trim((string)($_POST['priority'] ?? 'low')));
        $moderatorNotes = trim((string)($_POST['moderator_notes'] ?? ''));
        $handledBy = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

        if ($reportId <= 0) {
            throw new InvalidArgumentException('Report ID is required.');
        }

        $valid = ['pending', 'investigating', 'resolved', 'dismissed', 'escalated'];
        if (!in_array($status, $valid, true)) {
            throw new InvalidArgumentException('Invalid status.');
        }

        $validPriorities = ['low', 'medium', 'high', 'critical'];
        if (!in_array($priority, $validPriorities, true)) {
            throw new InvalidArgumentException('Invalid priority.');
        }

        $changed = $this->model->updateReport($reportId, $status, $priority, $moderatorNotes, $handledBy);
        $this->setMessage($changed ? 'Report updated.' : 'No report updated.', $changed ? 'success' : 'error');
    }

    private function setMessage(string $message, string $type): void
    {
        $_SESSION['ad_report_message'] = $message;
        $_SESSION['ad_report_message_type'] = $type;
    }
}
