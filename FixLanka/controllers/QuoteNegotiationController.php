<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/QuoteNegotiationModel.php';

class QuoteNegotiationController
{
    private QuoteNegotiationModel $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new QuoteNegotiationModel($pdo);
    }

    private function jsonHeader(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
    }

    private function requireUser(): int
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $user = getUserData();
        if (strtolower((string) ($user['role'] ?? '')) !== 'user') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }

        return (int) ($user['id'] ?? 0);
    }

    private function parseJsonBody(): array
    {
        $raw = file_get_contents('php://input');
        if (!is_string($raw) || trim($raw) === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function create(): void
    {
        $this->jsonHeader();

        try {
            $userId = $this->requireUser();
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $body = $this->parseJsonBody();
            $quoteId = (int) ($body['quote_id'] ?? 0);
            $source = (string) ($body['source'] ?? '');
            $requestType = (string) ($body['request_type'] ?? 'regular');
            $proposedPrice = (float) ($body['proposed_price'] ?? 0);
            $message = isset($body['message']) ? trim((string) $body['message']) : null;
            if ($message === '') {
                $message = null;
            }

            $created = $this->model->createByUser($userId, $quoteId, $source, $requestType, $proposedPrice, $message);

            echo json_encode([
                'success' => true,
                'negotiation' => $created,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('QuoteNegotiationController::create failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }

    public function list(): void
    {
        $this->jsonHeader();

        try {
            $userId = $this->requireUser();

            $quoteId = isset($_GET['quote_id']) ? (int) $_GET['quote_id'] : 0;
            $source = isset($_GET['source']) ? (string) $_GET['source'] : '';
            $requestType = isset($_GET['request_type']) ? (string) $_GET['request_type'] : 'regular';
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;

            if ($quoteId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'quote_id is required']);
                exit;
            }

            $rows = $this->model->listByQuoteForUser($userId, $quoteId, $source, $requestType, $limit);

            echo json_encode([
                'success' => true,
                'items' => $rows,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('QuoteNegotiationController::list failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }

    public function respond(): void
    {
        $this->jsonHeader();

        try {
            $userId = $this->requireUser();
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $body = $this->parseJsonBody();
            $negotiationId = (int) ($body['negotiation_id'] ?? 0);
            $decision = strtolower(trim((string) ($body['decision'] ?? '')));

            if ($negotiationId <= 0) {
                throw new InvalidArgumentException('Invalid negotiation id');
            }
            if (!in_array($decision, ['accept', 'reject'], true)) {
                throw new InvalidArgumentException('Invalid decision');
            }

            $ok = $this->model->respondToReceivedByUser($userId, $negotiationId, $decision);
            if (!$ok) {
                throw new RuntimeException('Negotiation could not be updated');
            }

            echo json_encode([
                'success' => true,
                'message' => $decision === 'accept'
                    ? 'Negotiation accepted successfully'
                    : 'Negotiation rejected successfully',
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('QuoteNegotiationController::respond failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }
}
