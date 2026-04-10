<?php
require_once __DIR__ . '/../../models/user/loadLandingProvidersModel.php';

class LoadLandingProvidersController {
    private LoadLandingProvidersModel $model;

    public function __construct() {
        $this->model = new LoadLandingProvidersModel();
    }

    public function handle(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        try {
            $providerType = strtolower(trim((string)($_GET['provider_type'] ?? 'all')));
            if (!in_array($providerType, ['all', 'individual', 'company'], true)) {
                $providerType = 'all';
            }

            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

            if ($limit <= 0) {
                $limit = 12;
            }
            if ($limit > 50) {
                $limit = 50;
            }
            if ($offset < 0) {
                $offset = 0;
            }

            $filters = [
                'category_id' => isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : null,
                'min_rating' => isset($_GET['rating']) && $_GET['rating'] !== '' ? (float)$_GET['rating'] : null,
                'district' => isset($_GET['location']) && $_GET['location'] !== '' ? trim((string)$_GET['location']) : null,
            ];

            $result = $this->model->loadLandingProviders($providerType, $filters, $limit, $offset);

            echo json_encode([
                'success' => true,
                'data' => $result['data'],
                'repairers' => $result['repairers'],
                'companies' => $result['companies'],
                'pagination' => $result['pagination'],
            ]);
        } catch (Throwable $e) {
            error_log('loadLandingProviders controller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load landing providers',
            ]);
        }

        exit;
    }
}
