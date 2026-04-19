<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerModel.php';

header('Content-Type: application/json');

requireRole('repairer');

$repairerId = (int)($_SESSION['user_id'] ?? 0);
if ($repairerId <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$repairerModel = new Repairer($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $settings = $repairerModel->getSettings($repairerId);
        echo json_encode(['success' => true, 'data' => $settings]);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to load settings']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $action = $input['action'] ?? 'update_settings';
    if ($action !== 'update_settings') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    $incoming = $input['settings'] ?? [];
    if (!is_array($incoming)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid settings payload']);
        exit;
    }

    try {
        $current = $repairerModel->getSettings($repairerId);
        $merged = array_merge($current, $incoming);
        $success = $repairerModel->updateSettings($repairerId, $merged);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
        }
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
