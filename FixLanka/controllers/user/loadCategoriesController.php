<?php
require_once __DIR__ . '/../../models/user/loadCategoriesModel.php';

class LoadCategoriesController {
    private LoadCategoriesModel $model;

    public function __construct() {
        $this->model = new LoadCategoriesModel();
    }

    public function handle(): void {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        try {
            $categories = $this->model->getAllCategories();

            echo json_encode([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (Throwable $e) {
            error_log('loadCategories controller error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load categories',
            ]);
        }

        exit;
    }
}
