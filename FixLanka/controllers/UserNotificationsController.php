<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/NotificationModel.php';

class UserNotificationsController {
    private NotificationModel $model;

    public function __construct() {
        global $pdo;
        $this->model = new NotificationModel($pdo);
    }

    public function list(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        $user = getUserData();
        // This endpoint is intended for the user navbar bell.
        // Only return notifications intended for users (plus global broadcasts).
        if (($user['role'] ?? 'user') !== 'user') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8;
        if ($limit <= 0) $limit = 8;
        if ($limit > 20) $limit = 20;

        $notifications = $this->model->getRecentNotifications((int)$user['id'], 'user', $limit);

        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
        ]);
        exit;
    }

    public function count(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

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

        $count = $this->model->getNotificationCount((int)$user['id'], 'user');

        echo json_encode([
            'success' => true,
            'count' => (int)$count,
        ]);
        exit;
    }
}
