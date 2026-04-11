<?php
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/IssueReportModel.php';

requireRole('repairer');

$user = getUserData() ?: [];
$repairerId = (int)($user['id'] ?? 0);

if ($repairerId <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

try {
    $model = new IssueReportModel($pdo);
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    if ($method === 'GET') {
        $issues = $model->getIssuesForRepairer($repairerId);
        echo json_encode([
            'success' => true,
            'issues' => $issues,
        ]);
        exit;
    }

    if ($method === 'POST') {
        $rawBody = file_get_contents('php://input');
        $payload = [];
        if (!empty($rawBody)) {
            $decoded = json_decode($rawBody, true);
            if (is_array($decoded)) {
                $payload = $decoded;
            }
        }

        $subject = trim((string)($payload['subject'] ?? $_POST['subject'] ?? ''));
        $message = trim((string)($payload['message'] ?? $_POST['message'] ?? ''));

        if ($subject === '' || $message === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Subject and message are required']);
            exit;
        }

        $reporterEmail = trim((string)($user['email'] ?? ''));
        $reporterName = trim((string)($user['name'] ?? 'Repairer'));
        $issueId = $model->createIssueFromRepairer($repairerId, $subject, $message, $reporterEmail, $reporterName);

        echo json_encode([
            'success' => true,
            'message' => 'Issue report submitted successfully',
            'issue_id' => $issueId
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Issue report submit failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit issue report']);
}
