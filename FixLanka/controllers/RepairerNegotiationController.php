<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerNegotiationModel.php';

class RepairerNegotiationController
{
    private RepairerNegotiationModel $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new RepairerNegotiationModel($pdo);
    }

    private function jsonHeader(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
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

    private function requireRepairer(): int
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $user = getUserData();
        if (strtolower((string) ($user['role'] ?? '')) !== 'repairer') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }

        return (int) ($user['id'] ?? 0);
    }

    public function listReceived(): void
    {
        $this->jsonHeader();

        try {
            $repairerId = $this->requireRepairer();
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;

            $rows = $this->model->listReceived($repairerId, $limit);
            echo json_encode([
                'success' => true,
                'items' => $rows,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('RepairerNegotiationController::listReceived failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }

    public function accept(): void
    {
        $this->jsonHeader();

        try {
            $repairerId = $this->requireRepairer();
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $body = $this->parseJsonBody();
            $negotiationId = (int) ($body['negotiation_id'] ?? 0);
            if ($negotiationId <= 0) {
                throw new InvalidArgumentException('Invalid negotiation id');
            }

            $ok = $this->model->acceptReceived($negotiationId, $repairerId);
            if (!$ok) {
                throw new RuntimeException('Negotiation could not be accepted');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Negotiation accepted successfully',
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('RepairerNegotiationController::accept failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }

    public function counter(): void
    {
        $this->jsonHeader();

        try {
            $repairerId = $this->requireRepairer();
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $body = $this->parseJsonBody();
            $negotiationId = (int) ($body['negotiation_id'] ?? 0);
            $counterPrice = (float) ($body['counter_price'] ?? 0);
            $message = isset($body['message']) ? trim((string) $body['message']) : null;
            if ($message === '') {
                $message = null;
            }

            if ($negotiationId <= 0) {
                throw new InvalidArgumentException('Invalid negotiation id');
            }

            $result = $this->model->counterReceived($negotiationId, $repairerId, $counterPrice, $message);
            if (!$result) {
                throw new RuntimeException('Negotiation could not be countered');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Counter proposal sent successfully',
                'data' => $result,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('RepairerNegotiationController::counter failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }

    public function reject(): void
    {
        $this->jsonHeader();

        try {
            $repairerId = $this->requireRepairer();
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $body = $this->parseJsonBody();
            $negotiationId = (int) ($body['negotiation_id'] ?? 0);
            if ($negotiationId <= 0) {
                throw new InvalidArgumentException('Invalid negotiation id');
            }

            $ok = $this->model->rejectReceived($negotiationId, $repairerId);
            if (!$ok) {
                throw new RuntimeException('Negotiation could not be rejected');
            }

            echo json_encode([
                'success' => true,
                'message' => 'Negotiation rejected successfully',
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('RepairerNegotiationController::reject failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
            exit;
        }
    }
}
