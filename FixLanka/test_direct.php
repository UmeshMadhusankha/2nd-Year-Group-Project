<?php
// Direct test of CompanyModel - No HTML output before this
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/CompanyModel.php';

// Set proper JSON header
header('Content-Type: application/json');

$model = new Company($pdo);

// Test 1: getFeatured
$featured = $model->getFeatured(10, 0);

// Test 2: getAll
$all = $model->getAll([], 10, 0);

// Test 3: getCount
$count = $model->getCount([]);

// Return results
echo json_encode([
    'featured_count' => count($featured),
    'featured_data' => $featured,
    'all_count' => count($all),
    'all_data' => $all,
    'total_count' => $count
], JSON_PRETTY_PRINT);
