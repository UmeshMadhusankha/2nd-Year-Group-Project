<?php
require_once 'config/database.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("DESCRIBE contract_milestone");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $columns]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
