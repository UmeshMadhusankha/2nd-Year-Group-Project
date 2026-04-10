<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/UserQuotesModel.php';

class UserQuotesController {
    private UserQuotesModel $model;

    public function __construct() {
        global $pdo;
        $this->model = new UserQuotesModel($pdo);
    }

    private function jsonHeader(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
    }

    private function requireUser(): array {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }
        $user = getUserData();
        if (($user['role'] ?? 'user') !== 'user') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
        return $user;
    }

    public function list(): void {
        $this->jsonHeader();
        $user = $this->requireUser();

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? (string)$_GET['status'] : null;
        $requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : null;

        if ($limit <= 0) $limit = 20;
        if ($limit > 50) $limit = 50;
        if ($offset < 0) $offset = 0;

        $quotes = $this->model->getUserQuotes((int)$user['id'], $limit, $offset, $status, $requestId);
        $pendingCount = $this->model->getUserPendingCount((int)$user['id']);

        echo json_encode([
            'success' => true,
            'quotes' => $quotes,
            'pending_count' => (int)$pendingCount,
            'limit' => $limit,
            'offset' => $offset,
        ]);
        exit;
    }

    public function summary(): void {
        $this->jsonHeader();
        $user = $this->requireUser();

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 3;
        if ($limit <= 0) $limit = 3;
        if ($limit > 10) $limit = 10;

        $quotes = $this->model->getUserQuotes((int)$user['id'], $limit, 0, null);
        $pendingCount = $this->model->getUserPendingCount((int)$user['id']);

        echo json_encode([
            'success' => true,
            'quotes' => $quotes,
            'pending_count' => (int)$pendingCount,
        ]);
        exit;
    }

    public function respond(): void {
        $this->jsonHeader();
        $user = $this->requireUser();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $source = isset($input['source']) ? (string)$input['source'] : '';
        $quoteId = isset($input['quote_id']) ? (int)$input['quote_id'] : 0;
        $decision = isset($input['decision']) ? (string)$input['decision'] : '';

        if (!in_array($source, ['repairer', 'company'], true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid source']);
            exit;
        }
        if ($quoteId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid quote_id']);
            exit;
        }
        if (!in_array($decision, ['accepted', 'rejected'], true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid decision']);
            exit;
        }

        $ok = $this->model->respondToQuote((int)$user['id'], $source, $quoteId, $decision);
        if (!$ok) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Could not update quote (maybe not pending or not yours)']);
            exit;
        }

        echo json_encode(['success' => true]);
        exit;
    }
}
