<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fix_lanka', 'root', '');
    
    // Check company quotations for request 48
    $stmt = $pdo->query('SELECT quotation_id, request_id, company_id, status FROM companyquotation WHERE request_id = 48 ORDER BY quotation_id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($rows) . " company quotations for request #48:\n";
    foreach ($rows as $row) {
        echo '  Quote ID: #' . $row['quotation_id'] . ', Company ID: ' . $row['company_id'] . ', Status: ' . $row['status'] . "\n";
    }
    
    echo "\n\nChecking if there are duplicate quotations in the entire table:\n";
    $dupStmt = $pdo->query('SELECT request_id, COUNT(*) as count FROM companyquotation GROUP BY request_id HAVING count > 1 LIMIT 10');
    $dups = $dupStmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Requests with multiple quotations: " . count($dups) . "\n";
    foreach ($dups as $dup) {
        echo '  Request #' . $dup['request_id'] . ': ' . $dup['count'] . " quotations\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
