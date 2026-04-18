<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/user/updateUserModel.php';

class UpdateUserController {
    private UpdateUserModel $model;

    public function __construct() {
        $this->model = new UpdateUserModel();
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
        if (($user['role'] ?? 'user') !== 'user') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid payload']);
            exit;
        }

        $fullName = trim((string)($payload['fullName'] ?? ''));
        $email = trim((string)($payload['email'] ?? ''));
        $location = trim((string)($payload['location'] ?? ''));

        if ($fullName === '' || $email === '' || $location === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Full name, email and location are required']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            exit;
        }

        try {
            $updated = $this->model->updateUser((int)$user['id'], [
                'full_name' => $fullName,
                'email' => $email,
                'location' => $location,
            ]);

            if (!$updated) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
                exit;
            }

            $fresh = $this->model->getUserById((int)$user['id']);
            if (!$fresh) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Profile updated but failed to refresh user data']);
                exit;
            }

            $freshFullName = trim(((string)($fresh['f_name'] ?? '')) . ' ' . ((string)($fresh['l_name'] ?? '')));
            $_SESSION['user_name'] = $freshFullName !== '' ? $freshFullName : ($_SESSION['user_name'] ?? 'User');
            $_SESSION['user_email'] = (string)($fresh['email'] ?? ($_SESSION['user_email'] ?? ''));

            $address = trim((string)($fresh['address'] ?? ''));
            $district = trim((string)($fresh['district'] ?? ''));
            $locationValue = trim($address !== '' && $district !== '' ? ($address . ', ' . $district) : ($address ?: $district));

            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => [
                    'fullName' => $freshFullName,
                    'email' => (string)($fresh['email'] ?? ''),
                    'location' => $locationValue,
                    'address' => $address,
                    'district' => $district,
                    'avatar' => (string)($fresh['profilePicture'] ?? ''),
                ]
            ]);
        } catch (Throwable $e) {
            error_log('updateUser controller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
        }

        exit;
    }
}
