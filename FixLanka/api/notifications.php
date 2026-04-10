<?php
// API endpoint for notification operations
// Routes requests to NotificationController

error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output, only log them
ini_set('log_errors', 1);

error_log("[API] Notification API called");
error_log("[API] REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("[API] REQUEST_URI: " . $_SERVER['REQUEST_URI']);

session_start();

require_once __DIR__ . '/../controllers/NotificationController.php';

header('Content-Type: application/json');

// Support JSON request bodies (frontend uses application/json)
$rawBody = file_get_contents('php://input');
$jsonBody = [];
if (!empty($rawBody)) {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $jsonBody = $decoded;
        // Merge into $_POST so controller code that expects $_POST still works
        $_POST = array_merge($_POST, $jsonBody);
    }
}

// Get the action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log("[API] Action: " . $action);

try {
    $controller = new NotificationController();
} catch (Exception $e) {
    error_log("[API ERROR] Failed to create controller: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}

switch ($action) {
    case 'getAll':
        $controller->getAllNotifications();
        break;
    
    case 'getRecent':
    case 'list': // Alias for getRecent used by frontend
        $controller->getRecentNotifications();
        break;
    
    case 'getStats':
        $controller->getStats();
        break;

    case 'count':
        $controller->getNotificationCount();
        break;

    case 'mark_all_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markAllRead();
        break;

    case 'mark_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markRead();
        break;
    
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->addNotification();
        break;
    
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->updateNotification();
        break;
    
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->deleteNotification();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
