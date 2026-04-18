<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/user/directRequestFlowModel.php';

class DirectRequestFlowController {
    private DirectRequestFlowModel $model;

    public function __construct() {
        $this->model = new DirectRequestFlowModel();
    }

    public function handleListedJobRequests(): void {
        $this->prepareJsonResponse();
        $userId = $this->requireUserAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            $this->respondError('Method not allowed', 405);
        }

        $providerType = $this->normalizeProviderType($_GET['provider_type'] ?? 'individual');

        try {
            $jobs = $this->model->getPendingListedJobs($userId, $providerType);
            $this->respondSuccess($jobs);
        } catch (Throwable $e) {
            error_log('listed-job-requests controller error: ' . $e->getMessage());
            $this->respondError('Failed to load listed jobs', 500);
        }
    }

    public function handleDirectJobRequests(): void {
        $this->prepareJsonResponse();
        $userId = $this->requireUserAuth();

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $action = strtolower(trim((string)($_GET['action'] ?? 'create')));

        if ($method === 'GET') {
            if ($action === 'get') {
                $this->handleGetDirectJobRequest($userId);
            }
            if ($action === 'provider-categories') {
                $this->handleProviderCategories();
            }
            $this->respondError('Method not allowed', 405);
        }

        if ($method === 'POST') {
            if ($action === 'update') {
                $this->handleUpdateDirectJobRequest($userId);
            }
            if ($action === 'delete') {
                $this->handleDeleteDirectJobRequest($userId);
            }
            if ($action !== 'create') {
                $this->respondError('Unsupported action', 400);
            }
        } else {
            $this->respondError('Method not allowed', 405);
        }

        $providerId = (int)($_POST['provider_id'] ?? 0);
        $providerType = $this->normalizeProviderType($_POST['provider_type'] ?? 'individual');
        $categoryId = (int)($_POST['category_id'] ?? 0);

        $payload = [
            'provider_id' => $providerId,
            'provider_type' => $providerType,
            'category_id' => $categoryId,
            'title' => trim((string)($_POST['title'] ?? '')),
            'description' => trim((string)($_POST['description'] ?? '')),
            'district' => trim((string)($_POST['district'] ?? '')),
            'address' => trim((string)($_POST['address'] ?? '')),
            'finish_date' => trim((string)($_POST['finish_date'] ?? '')),
            'photos' => null,
        ];

        if ($providerId <= 0 || $categoryId <= 0 || $payload['title'] === '' || $payload['description'] === '' || $payload['district'] === '' || $payload['address'] === '' || $payload['finish_date'] === '') {
            $this->respondError('Missing required fields', 400);
        }

        $allowedCategoryIds = $this->model->getAllowedCategoryIdsForProvider($providerType, $providerId);
        if (!$allowedCategoryIds) {
            $this->respondError('Selected provider does not expose service categories', 400);
        }

        if (!in_array($categoryId, $allowedCategoryIds, true)) {
            $this->respondError('Selected category is not provided by this service provider', 400);
        }

        try {
            $uploadedPhoto = $this->handlePhotoUpload('photos');
            if ($uploadedPhoto !== null) {
                $payload['photos'] = $uploadedPhoto;
            }

            $requestId = $this->model->createDirectJobRequest($userId, $payload);
            if ($requestId <= 0) {
                $this->respondError('Failed to create direct job request', 500);
            }

            $this->respondSuccess([
                'request_id' => $requestId,
                'category_id' => $categoryId,
            ], 'Job request sent successfully.');
        } catch (Throwable $e) {
            error_log('direct-job-requests controller error: ' . $e->getMessage());
            $this->respondError('Failed to create direct job request', 500);
        }
    }

    private function handleGetDirectJobRequest(int $userId): void {
        $requestId = (int)($_GET['request_id'] ?? 0);
        if ($requestId <= 0) {
            $this->respondError('Invalid request_id', 400);
        }

        try {
            $request = $this->model->getDirectJobRequestById($userId, $requestId);
            if (!$request) {
                $this->respondError('Direct request not found', 404);
            }

            $this->respondSuccess($request);
        } catch (Throwable $e) {
            error_log('get direct-job-request controller error: ' . $e->getMessage());
            $this->respondError('Failed to load direct request', 500);
        }
    }

    private function handleUpdateDirectJobRequest(int $userId): void {
        $requestId = (int)($_POST['request_id'] ?? 0);
        $providerId = (int)($_POST['provider_id'] ?? 0);
        $providerType = $this->normalizeProviderType($_POST['provider_type'] ?? 'individual');
        $categoryId = (int)($_POST['category_id'] ?? 0);

        $payload = [
            'category_id' => $categoryId,
            'title' => trim((string)($_POST['title'] ?? '')),
            'description' => trim((string)($_POST['description'] ?? '')),
            'district' => trim((string)($_POST['district'] ?? '')),
            'address' => trim((string)($_POST['address'] ?? '')),
            'finish_date' => trim((string)($_POST['finish_date'] ?? '')),
            'photos' => null,
        ];

        if ($requestId <= 0 || $providerId <= 0 || $categoryId <= 0 || $payload['title'] === '' || $payload['description'] === '' || $payload['district'] === '' || $payload['address'] === '' || $payload['finish_date'] === '') {
            $this->respondError('Missing required fields', 400);
        }

        $allowedCategoryIds = $this->model->getAllowedCategoryIdsForProvider($providerType, $providerId);
        if (!$allowedCategoryIds || !in_array($categoryId, $allowedCategoryIds, true)) {
            $this->respondError('Selected category is not provided by this service provider', 400);
        }

        try {
            $uploadedPhoto = $this->handlePhotoUpload('photos');
            if ($uploadedPhoto !== null) {
                $payload['photos'] = $uploadedPhoto;
            }

            $updated = $this->model->updateDirectJobRequest($userId, $requestId, $payload);
            if (!$updated) {
                $this->respondError('Failed to update direct job request', 400);
            }

            $this->respondSuccess([
                'request_id' => $requestId,
            ], 'Direct job request updated successfully.');
        } catch (Throwable $e) {
            error_log('update direct-job-request controller error: ' . $e->getMessage());
            $this->respondError('Failed to update direct job request', 500);
        }
    }

    private function handleDeleteDirectJobRequest(int $userId): void {
        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            $this->respondError('Invalid request_id', 400);
        }

        try {
            $deleted = $this->model->deleteDirectJobRequest($userId, $requestId);
            if (!$deleted) {
                $this->respondError('Failed to delete direct job request', 400);
            }

            $this->respondSuccess([
                'request_id' => $requestId,
            ], 'Direct job request deleted successfully.');
        } catch (Throwable $e) {
            error_log('delete direct-job-request controller error: ' . $e->getMessage());
            $this->respondError('Failed to delete direct job request', 500);
        }
    }

    private function handleProviderCategories(): void {
        $providerId = (int)($_GET['provider_id'] ?? 0);
        $providerType = $this->normalizeProviderType($_GET['provider_type'] ?? 'individual');

        if ($providerId <= 0) {
            $this->respondError('Invalid provider', 400);
        }

        try {
            $categories = $this->model->getProviderCategories($providerType, $providerId);
            $this->respondSuccess($categories);
        } catch (Throwable $e) {
            error_log('provider categories controller error: ' . $e->getMessage());
            $this->respondError('Failed to load provider categories', 500);
        }
    }

    public function handleDirectRequestQuotes(): void {
        $this->prepareJsonResponse();
        $userId = $this->requireUserAuth();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->respondError('Method not allowed', 405);
        }

        $rawBody = file_get_contents('php://input');
        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            $this->respondError('Invalid JSON payload', 400);
        }

        $requestId = (int)($payload['request_id'] ?? 0);
        $providerId = (int)($payload['provider_id'] ?? 0);
        $providerType = $this->normalizeProviderType($payload['provider_type'] ?? 'individual');

        if ($requestId <= 0 || $providerId <= 0) {
            $this->respondError('Invalid request or provider', 400);
        }

        try {
            $quoteId = $this->model->createDirectRequestQuote($userId, $requestId, $providerId, $providerType);
            $this->respondSuccess([
                'id' => $quoteId,
                'request_id' => $requestId,
            ], 'Listed job request sent successfully.');
        } catch (RuntimeException $e) {
            $this->respondError($e->getMessage(), 400);
        } catch (Throwable $e) {
            error_log('direct-request-quotes controller error: ' . $e->getMessage());
            $this->respondError('Failed to submit listed job request', 500);
        }
    }

    private function handlePhotoUpload(string $fieldName): ?string {
        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return null;
        }

        if ((int)$_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extension = strtolower((string)pathinfo((string)$_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed, true)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../uploads/direct_job_requests/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = uniqid('direct_job_', true) . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (!move_uploaded_file((string)$_FILES[$fieldName]['tmp_name'], $targetPath)) {
            return null;
        }

        return 'uploads/direct_job_requests/' . $fileName;
    }

    private function normalizeProviderType(string $providerType): string {
        $normalized = strtolower(trim($providerType));
        if ($normalized === 'company' || $normalized === 'companies') {
            return 'company';
        }
        return 'individual';
    }

    private function requireUserAuth(): int {
        if (!isLoggedIn()) {
            $this->respondError('Not authenticated', 401);
        }

        $user = getUserData();
        $userId = (int)($user['id'] ?? 0);
        if ($userId <= 0) {
            $this->respondError('Invalid session user', 401);
        }

        return $userId;
    }

    private function prepareJsonResponse(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
    }

    private function respondError(string $message, int $statusCode): void {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'message' => $message,
        ]);
        exit;
    }

    private function respondSuccess(array $data, string $message = ''): void {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
        exit;
    }
}
