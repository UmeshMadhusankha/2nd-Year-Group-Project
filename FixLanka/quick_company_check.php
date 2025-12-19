<?php
// Quick company check
require_once __DIR__ . '/config/database.php';

echo "<h2>Quick Company Check</h2>";

// Check company count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM Company");
$result = $stmt->fetch();
echo "<p>Companies in database: <strong>" . $result['count'] . "</strong></p>";

// Show companies
if ($result['count'] > 0) {
    echo "<h3>Companies:</h3>";
    $stmt = $pdo->query("SELECT company_id, name, email, rating FROM Company");
    $companies = $stmt->fetchAll();
    
    echo "<ul>";
    foreach ($companies as $c) {
        echo "<li>ID: {$c['company_id']}, Name: {$c['name']}, Rating: {$c['rating']}</li>";
    }
    echo "</ul>";
    
    // Test model
    echo "<h3>Testing CompanyModel:</h3>";
    require_once __DIR__ . '/models/CompanyModel.php';
    $model = new Company($pdo);
    
    $featured = $model->getFeatured(10, 0);
    echo "<p>getFeatured() returned: " . count($featured) . " companies</p>";
    
    $all = $model->getAll([], 10, 0);
    echo "<p>getAll() returned: " . count($all) . " companies</p>";
    
    if (count($all) > 0) {
        echo "<h4>First company from getAll():</h4>";
        echo "<pre>";
        print_r($all[0]);
        echo "</pre>";
    }
} else {
    echo "<p style='color: red;'>NO COMPANIES! Insert them using quick_data_check.sql</p>";
}

// Check repairer count for comparison
$stmt = $pdo->query("SELECT COUNT(*) as count FROM Repairer");
$result = $stmt->fetch();
echo "<p>Repairers in database: <strong>" . $result['count'] . "</strong></p>";
?>
