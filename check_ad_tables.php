<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fix_lanka', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "--- advertisement Table ---\n";
    $stmt = $pdo->query("DESCRIBE advertisement");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\n--- billinghistory Table ---\n";
    $stmt = $pdo->query("DESCRIBE billinghistory");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

    echo "\n--- Sample advertisements ---\n";
    $stmt = $pdo->query("SELECT * FROM advertisement LIMIT 5");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
