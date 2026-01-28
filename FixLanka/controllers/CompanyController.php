<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CompanyModel.php';

class CompanyController {
    private $companyModel;

    public function __construct() {
        global $pdo;
        $this->companyModel = new Company($pdo);
    }

    /**
     * Get single company details (JSON)
     * GET params: id
     */
    public function getDetails() {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        try {
            $companyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (!$companyId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Company ID is required'
                ]);
                exit;
            }

            $company = $this->companyModel->getById($companyId);
            if (!$company) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Company not found'
                ]);
                exit;
            }

            // No company reviews table in schema; keep consistent shape
            $company['reviewCount'] = 0;
            $company['reviews'] = [];

            echo json_encode([
                'success' => true,
                'data' => $company
            ]);
        } catch (Exception $e) {
            error_log('Error in CompanyController::getDetails: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch company details'
            ]);
        }
        exit;
    }
}
