<?php
// Test file to verify database connection and data
require_once __DIR__ . '/config/databse.php';
require_once __DIR__ . '/models/RepairerModel.php';
require_once __DIR__ . '/models/CompanyModel.php';

header('Content-Type: application/json');

try {
    $repairerModel = new Repairer($pdo);
    $companyModel = new Company($pdo);
    
    $repairers = $repairerModel->getFeatured(5, 0);
    $companies = $companyModel->getFeatured(5, 0);
    
    echo json_encode([
        'success' => true,
        'repairers' => $repairers,
        'companies' => $companies,
        'total' => count($repairers) + count($companies)
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>
