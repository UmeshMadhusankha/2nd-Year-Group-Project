<?php
/**
 * Reviews Export API
 * 
 * Handles preview and download requests for review reports.
 */

// Basic error handling for API
ini_set('display_errors', '0');
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/Feedback.php';

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'preview';
$rating = $_GET['rating'] ?? 'all';
$period = $_GET['period'] ?? 'all';
$format = $_GET['format'] ?? 'csv';

// Initialize model
$feedbackModel = new Feedback();

// Prepare filters
$filters = [
    'rating' => $rating,
    'period' => $period
];

// Fetch data
$reviews = $feedbackModel->getCompanyReviews($companyId, $filters);

if ($action === 'preview') {
    $totalRecords = count($reviews);
    $avgRating = 0;
    if ($totalRecords > 0) {
        $avgRating = array_sum(array_column($reviews, 'rating')) / $totalRecords;
    }

    echo json_encode([
        'success' => true,
        'summary' => [
            'total_records' => $totalRecords,
            'avg_rating' => round($avgRating, 1)
        ]
    ]);
    exit;
}

if ($action === 'download') {
    if ($format === 'csv') {
        $filename = "Reviews_Report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['Customer Name', 'Project Title', 'Rating', 'Comments', 'Date']);

        foreach ($reviews as $review) {
            fputcsv($output, [
                $review['f_name'] . ' ' . $review['l_name'],
                $review['project_title'],
                $review['rating'],
                $review['comments'],
                $review['review_date']
            ]);
        }
        fclose($output);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action or format']);
