<?php
/**
 * Workforce Export API
 * 
 * Handles preview and download requests for workforce reports.
 */

// Basic error handling for API
ini_set('display_errors', '0');
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/CompanyEmployeeModel.php';

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'preview';
$type = $_GET['type'] ?? 'all'; // all, staff, freelance
$format = $_GET['format'] ?? 'csv';

// Initialize model
$employeeModel = new CompanyEmployeeModel($pdo);

// Prepare filters
$filters = [
    'status' => 'active', // Default to active for reports usually
    'employment_type' => null
];

if ($type === 'staff') {
    $filters['employment_type'] = ['full_time', 'part_time'];
} elseif ($type === 'freelance') {
    $filters['employment_type'] = ['freelance'];
}

// Fetch data
$workforce = $employeeModel->getAll($companyId, $filters);

if ($action === 'preview') {
    $totalRecords = count($workforce);
    $avgRating = 0;
    if ($totalRecords > 0) {
        $avgRating = array_sum(array_column($workforce, 'rating')) / $totalRecords;
    }

    echo json_encode([
        'success' => true,
        'summary' => [
            'total_records' => $totalRecords,
            'avg_rating' => round($avgRating, 1),
            'specialties_count' => count(array_unique(array_column($workforce, 'specialty')))
        ]
    ]);
    exit;
}

if ($action === 'download') {
    if ($format === 'csv') {
        $filename = "Workforce_Report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV Header
        fputcsv($output, ['Name', 'Email', 'Phone', 'Job Title', 'Type', 'Specialty', 'Experience (Yrs)', 'Rating', 'Hired Date', 'Status']);

        foreach ($workforce as $member) {
            fputcsv($output, [
                $member['first_name'] . ' ' . $member['last_name'],
                $member['email'],
                $member['phone'],
                $member['job_title'],
                $member['employment_type'],
                $member['specialty'],
                $member['experience_years'],
                $member['rating'],
                $member['hired_date'],
                $member['status']
            ]);
        }
        fclose($output);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Invalid action or format']);
