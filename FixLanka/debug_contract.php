<?php
$_SERVER['REQUEST_URI'] = '/';
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "--- TABLE: contract_milestone ---\n";
    $stmt = $pdo->query("DESCRIBE contract_milestone");
    print_r($stmt->fetchAll());

    echo "\n--- TABLE: milestone ---\n";
    $stmt = $pdo->query("DESCRIBE milestone");
    print_r($stmt->fetchAll());

    $contractId = 36;
    echo "\n--- CONTRACT #$contractId ---\n";
    $stmt = $pdo->prepare('SELECT start_date FROM contract WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    print_r($stmt->fetch());

    echo "\n--- MILESTONES FOR #$contractId ---\n";
    // Try both tables just in case
    $stmt = $pdo->prepare('SELECT * FROM contract_milestone WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    print_r($stmt->fetchAll());

    $stmt = $pdo->prepare('SELECT * FROM milestone WHERE contract_id = ?');
    $stmt->execute([$contractId]);
    print_r($stmt->fetchAll());

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
