<?php
header('Content-Type: application/json');
require_once '../config/session.php';
require_once '../models/Feedback.php';

// Verify authentication
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? null) !== 'company') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userId = $_SESSION['user_id'] ?? null;
$companyId = $userId;

if (!$companyId) {
    http_response_code(400);
    echo json_encode(['error' => 'Company ID not found']);
    exit();
}

try {
    $feedbackModel = new Feedback();
    
    // Fetch data
    $reviews = $feedbackModel->getCompanyReviews($companyId);
    $stats = $feedbackModel->getCompanyReviewStats($companyId);
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'reviews' => $reviews
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
