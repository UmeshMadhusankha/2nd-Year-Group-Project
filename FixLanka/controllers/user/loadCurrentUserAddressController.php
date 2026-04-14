<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/user/loadCurrentUserAddressModel.php';

class LoadCurrentUserAddressController {
    private LoadCurrentUserAddressModel $model;

    public function __construct() {
        $this->model = new LoadCurrentUserAddressModel();
    }

    public function handle(): void {
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
        $userId = (int)($user['id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid session user']);
            exit;
        }

        try {
            $addressData = $this->model->getAddressByUserId($userId);
            if (!$addressData) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Address not found']);
                exit;
            }

            echo json_encode([
                'success' => true,
                'data' => $addressData,
            ]);
        } catch (Throwable $e) {
            error_log('loadCurrentUserAddress controller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to load address']);
        }

        exit;
    }
}
