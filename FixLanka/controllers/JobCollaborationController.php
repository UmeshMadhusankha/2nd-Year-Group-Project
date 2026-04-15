<?php

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/JobCollaborationModel.php';

class JobCollaborationController
{
    private JobCollaborationModel $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new JobCollaborationModel($pdo);
    }

    private function jsonHeader(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
    }

    private function requireParticipant(): array
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $user = getUserData();
        $role = strtolower((string) ($user['role'] ?? ''));
        if (!in_array($role, ['user', 'repairer', 'company'], true)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }

        return [
            'id' => (int) ($user['id'] ?? 0),
            'role' => $role,
        ];
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

    public function get(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();

            $requestId = isset($_GET['request_id']) ? (int) $_GET['request_id'] : 0;
            $requestType = isset($_GET['request_type']) ? (string) $_GET['request_type'] : 'regular';

            if ($requestId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'request_id is required']);
                exit;
            }

            $collaboration = $this->model->getByRequestForActor($requestId, $requestType, $actor['id'], $actor['role']);
            if (!$collaboration && $actor['role'] === 'user') {
                $collaboration = $this->model->bootstrapFromRequestIfMissing($actor['id'], $requestId, $requestType);
            }
            if (!$collaboration) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Collaboration not found for this job']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'collaboration' => $collaboration,
            ]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::get failed: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to load collaboration: ' . $e->getMessage()]);
            exit;
        }
    }

    public function postNote(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            $message = (string) ($body['message'] ?? '');

            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }

            $collaboration = $this->model->postNote($collaborationId, $actor['id'], $actor['role'], $message);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::postNote failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function proposePrice(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            $proposedPrice = (float) ($body['proposed_price'] ?? 0);
            $message = isset($body['message']) ? (string) $body['message'] : null;

            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }

            $collaboration = $this->model->proposePrice($collaborationId, $actor['id'], $actor['role'], $proposedPrice, $message);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::proposePrice failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function respondPrice(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            $decision = strtolower(trim((string) ($body['decision'] ?? '')));
            $message = isset($body['message']) ? (string) $body['message'] : null;

            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }
            if (!in_array($decision, ['accept', 'reject'], true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'decision must be accept or reject']);
                exit;
            }

            $collaboration = $this->model->respondToPendingPrice($collaborationId, $actor['id'], $actor['role'], $decision === 'accept', $message);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::respondPrice failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function markCompleted(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }

            $collaboration = $this->model->markCompleted($collaborationId, $actor['id'], $actor['role']);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::markCompleted failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function confirmPayment(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }

            $collaboration = $this->model->confirmPayment($collaborationId, $actor['id'], $actor['role']);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::confirmPayment failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function submitRating(): void
    {
        $this->jsonHeader();

        try {
            $actor = $this->requireParticipant();
            $body = $this->parseJsonBody();

            $collaborationId = (int) ($body['collaboration_id'] ?? 0);
            $rating = (int) ($body['rating'] ?? 0);
            $comment = isset($body['comment']) ? (string) $body['comment'] : null;

            if ($collaborationId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'collaboration_id is required']);
                exit;
            }

            $collaboration = $this->model->submitRating($collaborationId, $actor['id'], $actor['role'], $rating, $comment);
            echo json_encode(['success' => true, 'collaboration' => $collaboration]);
            exit;
        } catch (Throwable $e) {
            error_log('JobCollaborationController::submitRating failed: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}
