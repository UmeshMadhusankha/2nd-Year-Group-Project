<?php
require_once __DIR__ . '/../models/ProviderSearchModel.php';

class ProviderSearchController {
    private ProviderSearchModel $model;

    public function __construct() {
        $this->model = new ProviderSearchModel();
    }

    public function handle(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        try {
            $mode = strtolower(trim((string)($_GET['mode'] ?? 'featured')));
            if ($mode !== 'featured' && $mode !== 'search') {
                $mode = 'featured';
            }

            $providerType = strtolower(trim((string)($_GET['provider_type'] ?? 'all')));
            if (!in_array($providerType, ['all', 'individual', 'company'], true)) {
                $providerType = 'all';
            }

            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            if ($limit <= 0) $limit = 12;
            if ($limit > 50) $limit = 50;
            if ($offset < 0) $offset = 0;

            $filters = [];

            $category = $_GET['category'] ?? null;
            if ($category !== null && $category !== '') {
                $filters['category_id'] = (int)$category;
            }

            $rating = $_GET['rating'] ?? null;
            if ($rating !== null && $rating !== '') {
                $filters['min_rating'] = (float)$rating;
            }

            $location = $_GET['location'] ?? null;
            if ($location !== null && $location !== '') {
                $filters['service_area'] = (string)$location;
            }

            // If mode=featured but user actually provided filters, treat as search.
            if ($mode === 'featured' && !empty($filters)) {
                $mode = 'search';
            }

            $result = ($mode === 'featured')
                ? $this->model->featured($providerType, $limit, $offset)
                : $this->model->search($providerType, $filters, $limit, $offset);

            echo json_encode([
                'success' => true,
                'mode' => $mode,
                'filters' => $filters,
                'data' => $result['data'],
                'pagination' => $result['pagination'],
            ]);
        } catch (Throwable $e) {
            error_log('ProviderSearchController error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch providers',
            ]);
        }
        exit;
    }
}
