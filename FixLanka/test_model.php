<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\config\database.php';
require_once 'c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\models\CompanyEmployeeModel.php';

echo "Testing CompanyEmployeeModel for Company ID 5\n";

try {
    $model = new CompanyEmployeeModel($pdo);
    $stats = $model->getStatistics(5);
    
    echo "Stats result:\n";
    print_r($stats);
    
    echo "\nRaw DB Query Test:\n";
    $stmt = $pdo->prepare("SELECT * FROM company_employees WHERE company_id = 5");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
