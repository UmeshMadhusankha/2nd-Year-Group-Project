<?php
/**
 * User CRUD reference controller (learning sandbox)
 *
 * Purpose:
 * - Show how to structure controller methods for User actor features.
 * - Show both page rendering and JSON API action handlers.
 */

require_once __DIR__ . '/../../../../config/session.php';
require_once __DIR__ . '/../../../../config/database.php';
require_once __DIR__ . '/../../models/user/crud_reference_model.php';

class CrudReferenceController
{
    private CrudReferenceModel $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new CrudReferenceModel($pdo);
    }

    /**
     * Send JSON header and clean output buffers so response is valid JSON.
     */
    private function jsonHeader(): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
    }

    /**
     * Enforce login + user role.
     * Returns user session array when valid.
     */
    private function requireUser(): array
    {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $user = getUserData();
        if (($user['role'] ?? '') !== 'user') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden: User role required']);
            exit;
        }

        return $user;
    }

    /**
     * Render the practice page.
     */
    public function index(): void
    {
        require_once __DIR__ . '/../../views/user/crud_reference_view.php';
    }

    /**
     * GET notes for logged-in user.
     */
    public function listNotes(): void
    {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();
            $notes = $this->model->listNotesByUser((int) $user['id']);

            echo json_encode([
                'success' => true,
                'notes' => $notes,
            ]);
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to load notes', 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * POST create note.
     * Demonstrates adding and validating a new form field: priority_level.
     */
    public function createNote(): void
    {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $payload = json_decode(file_get_contents('php://input'), true) ?: [];

            $title = trim((string) ($payload['title'] ?? ''));
            $body = trim((string) ($payload['body'] ?? ''));
            $priorityLevel = strtolower(trim((string) ($payload['priority_level'] ?? 'medium')));

            if ($title === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Title is required']);
                exit;
            }

            if (!in_array($priorityLevel, ['low', 'medium', 'high'], true)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid priority_level']);
                exit;
            }

            $newId = $this->model->createNote((int) $user['id'], $title, $body, $priorityLevel);

            echo json_encode([
                'success' => true,
                'message' => 'Note created',
                'note_id' => $newId,
            ]);
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Create failed', 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * POST update note.
     */
    public function updateNote(): void
    {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $payload = json_decode(file_get_contents('php://input'), true) ?: [];

            $noteId = (int) ($payload['note_id'] ?? 0);
            $title = trim((string) ($payload['title'] ?? ''));
            $body = trim((string) ($payload['body'] ?? ''));
            $priorityLevel = strtolower(trim((string) ($payload['priority_level'] ?? 'medium')));

            if ($noteId <= 0 || $title === '') {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'note_id and title are required']);
                exit;
            }

            if (!in_array($priorityLevel, ['low', 'medium', 'high'], true)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Invalid priority_level']);
                exit;
            }

            $ok = $this->model->updateNote((int) $user['id'], $noteId, $title, $body, $priorityLevel);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Note not found or no changes']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Note updated']);
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Update failed', 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * POST delete note.
     */
    public function deleteNote(): void
    {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }

            $payload = json_decode(file_get_contents('php://input'), true) ?: [];
            $noteId = (int) ($payload['note_id'] ?? 0);

            if ($noteId <= 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'note_id is required']);
                exit;
            }

            $ok = $this->model->deleteNote((int) $user['id'], $noteId);
            if (!$ok) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Note not found']);
                exit;
            }

            echo json_encode(['success' => true, 'message' => 'Note deleted']);
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Delete failed', 'error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * GET business insights from joined tables.
     */
    public function insights(): void
    {
        $this->jsonHeader();
        try {
            $user = $this->requireUser();

            $status = isset($_GET['status']) && $_GET['status'] !== ''
                ? strtolower(trim((string) $_GET['status']))
                : null;

            $rows = $this->model->getJobInsights((int) $user['id'], $status);
            echo json_encode(['success' => true, 'rows' => $rows]);
            exit;
        } catch (Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Insights failed', 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// Tiny router so one file can act as page + API for quick practice.
$controller = new CrudReferenceController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'list_notes':
        $controller->listNotes();
        break;
    case 'create_note':
        $controller->createNote();
        break;
    case 'update_note':
        $controller->updateNote();
        break;
    case 'delete_note':
        $controller->deleteNote();
        break;
    case 'insights':
        $controller->insights();
        break;
    default:
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
