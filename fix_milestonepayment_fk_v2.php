<?php
// Self-contained script to fix the foreign key constraint
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "Connected to database.\n";

    // 1. Drop the old constraint
    echo "Dropping old constraint milestonepayment_ibfk_1...\n";
    try {
        $pdo->exec("ALTER TABLE milestonepayment DROP FOREIGN KEY milestonepayment_ibfk_1");
        echo "Successfully dropped.\n";
    } catch (Exception $e) {
        echo "Notice: Could not drop (might not exist or already dropped). " . $e->getMessage() . "\n";
    }
    
    // 2. Add new constraint pointing to contract_milestone
    echo "Adding new constraint milestonepayment_ibfk_1 pointing to contract_milestone...\n";
    try {
        $pdo->exec("ALTER TABLE milestonepayment 
                    ADD CONSTRAINT milestonepayment_ibfk_1 
                    FOREIGN KEY (milestone_id) 
                    REFERENCES contract_milestone(milestone_id) 
                    ON DELETE CASCADE");
        echo "Successfully added.\n";
    } catch (Exception $e) {
        echo "Error adding constraint: " . $e->getMessage() . "\n";
    }
    
    echo "Check DONE.\n";
} catch (PDOException $e) {
    echo "Final Connection Error: " . $e->getMessage() . "\n";
}
?>
