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
        try {
            $user = $this->requireUser();

            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            $status = isset($_GET['status']) && $_GET['status'] !== '' ? (string)$_GET['status'] : null;
            $requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : null;
            $requestType = isset($_GET['request_type']) && $_GET['request_type'] !== '' ? (string)$_GET['request_type'] : null;

            if ($limit <= 0) $limit = 20;
            if ($limit > 50) $limit = 50;
            if ($offset < 0) $offset = 0;

            $quotes = $this->model->getUserQuotes((int)$user['id'], $limit, $offset, $status, $requestId, $requestType);
            $pendingCount = $this->model->getUserPendingCount((int)$user['id']);

            echo json_encode([
                'success' => true,
                'quotes' => $quotes,
                'pending_count' => (int)$pendingCount,
                'limit' => $limit,
                'offset' => $offset,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('UserQuotesController::list failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load quotes: ' . $e->getMessage(),
            ]);
            exit;
        }
    }

    public function summary(): void {
        $this->jsonHeader();
        try {
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
        } catch (Throwable $e) {
            error_log('UserQuotesController::summary failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load quote summary: ' . $e->getMessage(),
            ]);
            exit;
        }
    }

    public function respond(): void {
        $this->jsonHeader();
        try {
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
            $requestType = isset($input['request_type']) ? (string)$input['request_type'] : null;

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

            $ok = $this->model->respondToQuote((int)$user['id'], $source, $quoteId, $decision, $requestType);
            if (!$ok) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Could not update quote (maybe not pending or not yours)']);
                exit;
            }

            echo json_encode(['success' => true]);
            exit;
        } catch (Throwable $e) {
            error_log('UserQuotesController::respond failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to process quote action: ' . $e->getMessage(),
            ]);
            exit;
        }
    }

    public function resetToPending(): void {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $source = isset($input['source']) ? (string)$input['source'] : '';
            $quoteId = isset($input['quote_id']) ? (int)$input['quote_id'] : 0;
            $requestType = isset($input['request_type']) ? (string)$input['request_type'] : null;

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

            $ok = $this->model->resetQuoteToPending((int)$user['id'], $source, $quoteId, $requestType);
            if (!$ok) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Could not reset quote status']);
                exit;
            }

            echo json_encode(['success' => true]);
            exit;
        } catch (Throwable $e) {
            error_log('UserQuotesController::resetToPending failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to reset quote: ' . $e->getMessage(),
            ]);
            exit;
        }
    }

    public function completionSummary(): void {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            $source = isset($_GET['source']) ? (string)$_GET['source'] : '';
            $quoteId = isset($_GET['quote_id']) ? (int)$_GET['quote_id'] : 0;
            $requestType = isset($_GET['request_type']) ? (string)$_GET['request_type'] : null;

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

            $summary = $this->model->getCompletedQuoteSummary((int)$user['id'], $source, $quoteId, $requestType);
            if ($summary === null) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Quote summary not found']);
                exit;
            }

            echo json_encode(['success' => true, 'summary' => $summary]);
            exit;
        } catch (Throwable $e) {
            error_log('UserQuotesController::completionSummary failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load completion summary: ' . $e->getMessage(),
            ]);
            exit;
        }
    }
}
