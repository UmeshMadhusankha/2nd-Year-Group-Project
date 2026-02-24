<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerModel.php';

class RepairerController {
    private $repairerModel;

    public function __construct() {
        global $pdo;
        $this->repairerModel = new Repairer($pdo);
    }

    /**
     * Get single repairer details (JSON)
     * GET params: id
     */
    public function getDetails() {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json');

        try {
            $repairerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if (!$repairerId) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Repairer ID is required'
                ]);
                exit;
            }

            $repairer = $this->repairerModel->getById($repairerId);
            if (!$repairer) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Repairer not found'
                ]);
                exit;
            }

            // Reviews are stored for repairers in `review`
            $summary = $this->repairerModel->getReviewSummary($repairerId);
            $repairer['reviewCount'] = $summary['count'];
            $repairer['ratings'] = $summary['average'];
            $repairer['reviews'] = $this->repairerModel->getRecentReviews($repairerId, 3);

            echo json_encode([
                'success' => true,
                'data' => $repairer
            ]);
        } catch (Exception $e) {
            error_log('Error in RepairerController::getDetails: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch repairer details'
            ]);
        }
        exit;
    }
}
