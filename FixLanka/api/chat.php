<?php
/**
 * Chat API Endpoint — Phase 6
 * 
 * Actions:
 *   GET  ?action=get_messages&contract_id=X&since_id=0
 *   POST ?action=send          { contract_id, message }
 *   POST ?action=mark_read     { contract_id }
 *   GET  ?action=unread_count  &contract_id=X
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ChatModel.php';

// --- Auth check ---
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$chatModel = new ChatModel($pdo);

// Parse action - handle JSON body for POST requests
$action = $_GET['action'] ?? '';
if (empty($action) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if ($json && isset($json['action'])) {
        $action = $json['action'];
        // Store parsed JSON for later use
        $_CHAT_INPUT = $json;
    } else {
        $action = $_POST['action'] ?? '';
    }
} else {
    $_CHAT_INPUT = [];
}

switch ($action) {

    // ─────────────────────────────────────
    // GET MESSAGES (polling)
    // ─────────────────────────────────────
    case 'get_messages':
        $contractId = (int)($_GET['contract_id'] ?? 0);
        $sinceId    = (int)($_GET['since_id'] ?? 0);

        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Missing contract_id']);
            exit;
        }

        // Verify user is participant
        $role = $chatModel->getUserRole($contractId, $userId);
        if (!$role) {
            // Fallback: if user_role is 'company', trust session
            if ($userRole === 'company') {
                $role = 'company';
            } else {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }
        }

        // Check chat is active
        if (!$chatModel->isChatActive($contractId)) {
            echo json_encode([
                'success' => true,
                'messages' => [],
                'chat_active' => false,
                'message' => 'Chat is not yet active for this contract'
            ]);
            exit;
        }

        $messages = $chatModel->getMessages($contractId, $sinceId);

        // Auto-mark as read
        $chatModel->markRead($contractId, $userId, $role);

        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'chat_active' => true,
            'user_role' => $role
        ]);
        break;

    // ─────────────────────────────────────
    // SEND MESSAGE
    // ─────────────────────────────────────
    case 'send':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'POST required']);
            exit;
        }

        // Use already-parsed JSON input or fall back to $_POST
        $input = !empty($_CHAT_INPUT) ? $_CHAT_INPUT : $_POST;

        $contractId = (int)($input['contract_id'] ?? 0);
        $messageText = trim($input['message'] ?? '');

        if (!$contractId || $messageText === '') {
            echo json_encode(['success' => false, 'message' => 'Missing contract_id or message']);
            exit;
        }

        // Verify participation
        $role = $chatModel->getUserRole($contractId, $userId);
        if (!$role) {
            if ($userRole === 'company') {
                $role = 'company';
            } else {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                exit;
            }
        }

        // Check chat active
        if (!$chatModel->isChatActive($contractId)) {
            echo json_encode(['success' => false, 'message' => 'Chat is not active for this contract']);
            exit;
        }

        // Send
        $chatId = $chatModel->sendMessage($contractId, $userId, $role, $messageText);

        if ($chatId) {
            echo json_encode(['success' => true, 'chat_id' => $chatId]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to send message']);
        }
        break;

    // ─────────────────────────────────────
    // MARK READ
    // ─────────────────────────────────────
    case 'mark_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'POST required']);
            exit;
        }

        $input = !empty($_CHAT_INPUT) ? $_CHAT_INPUT : $_POST;

        $contractId = (int)($input['contract_id'] ?? 0);
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Missing contract_id']);
            exit;
        }

        $role = $chatModel->getUserRole($contractId, $userId);
        if (!$role && $userRole === 'company') $role = 'company';

        if ($role) {
            $chatModel->markRead($contractId, $userId, $role);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
        }
        break;

    // ─────────────────────────────────────
    // UNREAD COUNT
    // ─────────────────────────────────────
    case 'unread_count':
        $contractId = (int)($_GET['contract_id'] ?? 0);
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Missing contract_id']);
            exit;
        }

        $role = $chatModel->getUserRole($contractId, $userId);
        if (!$role && $userRole === 'company') $role = 'company';

        if ($role) {
            $count = $chatModel->getUnreadCount($contractId, $role);
            echo json_encode(['success' => true, 'unread' => $count]);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Access denied']);
        }
        break;

    // ─────────────────────────────────────
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        break;
}
