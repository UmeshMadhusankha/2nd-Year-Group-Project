<?php
header('Content-Type: application/json');
require_once '../config/session.php';
require_once '../models/SupportTicket.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['user_id']; // This is actually the company_id for company users
$ticketModel = new SupportTicket();

try {
    // Handle GET request (Fetch tickets)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $filters = [
            'status' => isset($_GET['status']) ? $_GET['status'] : null,
            'priority' => isset($_GET['priority']) ? $_GET['priority'] : null,
            'category' => isset($_GET['category']) ? $_GET['category'] : null
        ];

        $tickets = $ticketModel->getAllByUser($user_id, 'company', $filters);
        $stats = $ticketModel->getStats($user_id, 'company');

        echo json_encode([
            'success' => true,
            'tickets' => $tickets,
            'stats' => $stats
        ]);
        exit;
    }

    // Handle POST request (Create ticket)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (empty($input['title']) || empty($input['description']) || empty($input['category'])) {
            throw new Exception('Missing required fields');
        }

        $data = [
            'user_id' => $user_id,
            'user_type' => 'company',
            'title' => $input['title'],
            'description' => $input['description'],
            'category' => $input['category'],
            'priority' => $input['priority'] ?? 'medium',
            'urgency' => $input['urgency'] ?? 'soon',
            'project_id' => !empty($input['project']) ? $input['project'] : null,
            // 'attachment' => handleFileUpload() // TODO: Implement file upload handling
        ];

        $ticketId = $ticketModel->create($data);

        echo json_encode([
            'success' => true,
            'message' => 'Support ticket created successfully',
            'ticket_id' => $ticketId
        ]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
