<?php
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/AdReportModel.php';
require_once 'helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Method not allowed', 405);
}

try {
    requireAuth();
    
    // Check if user is a company
    if (!hasRole('company')) {
        sendErrorResponse('Unauthorized. Only companies can submit ad reports.', 403);
    }

    $companyId = $_SESSION['company_id'] ?? null;
    if (!$companyId) {
        $companyData = getCompanyByUserId($pdo, $_SESSION['user_id']);
        if (!$companyData) {
            sendErrorResponse('Company profile not found', 404);
        }
        $companyId = $companyData['company_id'];
    }

    // Get input data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        sendErrorResponse('Invalid input data', 400);
    }

    $required = ['ad_id', 'issue_type', 'description'];
    $missing = validateRequiredFields($data, $required);
    if (!empty($missing)) {
        sendErrorResponse('Missing required fields: ' . implode(', ', $missing), 400);
    }

    $adId = (int)$data['ad_id'];
    $issueType = trim($data['issue_type']);
    $description = trim($data['description']);

    $model = new AdReportModel($pdo);
    
    // Verify the ad exists
    $adStatus = $model->getAdvertisementStatus($adId);
    if (!$adStatus) {
        sendErrorResponse('Advertisement not found', 404);
    }

    $success = $model->submitReport($adId, $companyId, 'company', $issueType, $description);

    if ($success) {
        sendSuccessResponse(['ad_id' => $adId], 'Report submitted successfully. Our moderators will review it shortly.');
    } else {
        sendErrorResponse('Failed to submit report', 500);
    }

} catch (Exception $e) {
    error_log('Error in ad-report-submit.php: ' . $e->getMessage());
    sendErrorResponse('An internal server error occurred', 500);
}
