<?php
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerSupportTicket.php';

requireRole('repairer');

$user = getUserData() ?: [];
$repairerId = (int)($user['id'] ?? 0);

if ($repairerId <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

function parseJsonBody(): array
{
    $rawBody = file_get_contents('php://input');
    if (!$rawBody) {
        return [];
    }

    $decoded = json_decode($rawBody, true);
    return is_array($decoded) ? $decoded : [];
}

function sanitizeFileName(string $name): string
{
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    return trim($name, '._');
}

function storeAttachment(array $file, int $ticketId): ?array
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if ($file['size'] > 10 * 1024 * 1024) {
        throw new InvalidArgumentException('Attachment exceeds 10MB limit');
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    $fileName = sanitizeFileName($file['name'] ?? 'attachment');
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new InvalidArgumentException('Unsupported attachment type');
    }

    $uploadsDir = __DIR__ . '/../uploads/support_tickets';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0775, true);
    }

    $uniqueName = sprintf('%s_%s.%s', $ticketId, bin2hex(random_bytes(4)), $extension);
    $targetPath = $uploadsDir . '/' . $uniqueName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Failed to save attachment');
    }

    return [
        'file_name' => $fileName,
        'file_path' => '/2nd-Year-Group-Project/FixLanka/uploads/support_tickets/' . $uniqueName,
        'file_type' => $file['type'] ?? 'application/octet-stream',
        'file_size' => (int)$file['size']
    ];
}

try {
    $model = new RepairerSupportTicket($pdo);
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    if ($method === 'GET') {
        $ticketId = (int)($_GET['ticket_id'] ?? 0);
        if ($ticketId > 0) {
            $ticket = $model->getTicketById($repairerId, $ticketId);
            if (!$ticket) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ticket not found']);
                exit;
            }

            $responses = $model->getResponses($ticketId);
            $attachments = $model->getAttachments($ticketId);

            echo json_encode([
                'success' => true,
                'ticket' => $ticket,
                'responses' => $responses,
                'attachments' => $attachments
            ]);
            exit;
        }

        $tickets = $model->getTicketsForRepairer($repairerId);
        echo json_encode([
            'success' => true,
            'tickets' => $tickets
        ]);
        exit;
    }

    if ($method === 'POST') {
        $payload = parseJsonBody();
        $ticketId = (int)($_POST['ticket_id'] ?? ($payload['ticket_id'] ?? 0));
        $message = trim((string)($_POST['message'] ?? ($payload['message'] ?? '')));

        if ($ticketId > 0 && $message !== '') {
            $ticket = $model->getTicketById($repairerId, $ticketId);
            if (!$ticket) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Ticket not found']);
                exit;
            }

            $model->addResponse($ticketId, 'user', $repairerId, $message);
            echo json_encode([
                'success' => true,
                'message' => 'Reply sent successfully'
            ]);
            exit;
        }

        $title = trim((string)($_POST['title'] ?? ($payload['title'] ?? '')));
        $description = trim((string)($_POST['description'] ?? ($payload['description'] ?? '')));
        $category = trim((string)($_POST['category'] ?? ($payload['category'] ?? 'other')));
        $priority = trim((string)($_POST['priority'] ?? ($payload['priority'] ?? 'medium')));
        $urgency = trim((string)($_POST['urgency'] ?? ($payload['urgency'] ?? 'soon')));
        $relatedProjectId = (int)($_POST['project_id'] ?? ($payload['project_id'] ?? 0));

        if ($title === '' || $description === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title and description are required']);
            exit;
        }

        $ticketId = $model->createTicket($repairerId, [
            'title' => $title,
            'description' => $description,
            'category' => $category === '' ? 'other' : $category,
            'priority' => $priority === '' ? 'medium' : $priority,
            'urgency' => $urgency === '' ? 'soon' : $urgency,
            'related_project_id' => $relatedProjectId > 0 ? $relatedProjectId : null
        ]);

        if (!empty($_FILES['attachment'])) {
            $fileInfo = storeAttachment($_FILES['attachment'], $ticketId);
            if ($fileInfo) {
                $model->addAttachment($ticketId, $fileInfo);
            }
        }

        echo json_encode([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'ticket_id' => $ticketId
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('Repairer support ticket failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to process support request']);
}
