<?php
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: application/json');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/SupportTicketAdmin.php';

requireRole(['admin', 'moderator']);

$user = getUserData() ?: [];
$userId = (int)($user['id'] ?? 0);
$userRole = (string)($user['role'] ?? 'admin');

if ($userId <= 0) {
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

try {
    $model = new SupportTicketAdmin($pdo);
    $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

    if ($method === 'GET') {
        $ticketId = (int)($_GET['ticket_id'] ?? 0);
        if ($ticketId > 0) {
            $ticket = $model->getTicketById($ticketId);
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

        $filters = [
            'status' => $_GET['status'] ?? '',
            'user_type' => $_GET['user_type'] ?? '',
            'category' => $_GET['category'] ?? ''
        ];

        $tickets = $model->getTickets($filters);
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
        $status = trim((string)($_POST['status'] ?? ($payload['status'] ?? '')));
        $allowedStatuses = ['open', 'in-progress', 'pending', 'resolved', 'closed'];

        if ($ticketId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ticket ID is required']);
            exit;
        }

        $ticket = $model->getTicketById($ticketId);
        if (!$ticket) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Ticket not found']);
            exit;
        }

        if ($status !== '') {
            if (!in_array($status, $allowedStatuses, true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid status value']);
                exit;
            }
            $model->updateStatus($ticketId, $status);
        }

        if ($message !== '') {
            $model->addResponse($ticketId, $userRole, $userId, $message);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated'
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (Throwable $e) {
    error_log('Support ticket admin error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to process support ticket request']);
}
