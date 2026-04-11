<?php
/**
 * Repairer Reviews API
 * Fetches customer reviews and rating summary for an individual repairer.
 * GET /api/repairer-reviews.php?repairer_id=<id>&limit=<n>
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RepairerModel.php';

// Only allow GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$repairerId = isset($_GET['repairer_id']) ? (int)$_GET['repairer_id'] : 0;
$limit      = isset($_GET['limit'])      ? min((int)$_GET['limit'], 100) : 50;

if (!$repairerId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'repairer_id is required']);
    exit;
}

if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection not established']);
    exit;
}

try {
    $repairerModel = new Repairer($pdo);

    // Get review summary (avg, count)
    $summary = $repairerModel->getReviewSummary($repairerId);

    // Get individual reviews
    $reviews = $repairerModel->getRecentReviews($repairerId, $limit);

    // Compute per-star breakdown
    $starBreakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $positiveCount = 0;

    foreach ($reviews as $r) {
        $star = (int)round($r['rating']);
        if (isset($starBreakdown[$star])) {
            $starBreakdown[$star]++;
        }
        if ($r['rating'] >= 4) {
            $positiveCount++;
        }
    }

    $total = $summary['count'];
    $positiveRate = $total > 0 ? round(($positiveCount / $total) * 100) : 0;

    echo json_encode([
        'success'  => true,
        'stats'    => [
            'average_rating'   => round($summary['average'], 1),
            'total_reviews'    => $total,
            'positive_rate'    => $positiveRate,
            'star_breakdown'   => $starBreakdown
        ],
        'reviews'  => $reviews
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>
