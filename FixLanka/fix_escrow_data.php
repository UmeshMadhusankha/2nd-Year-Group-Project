<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Select all escrow accounts
    $stmt = $pdo->query("SELECT contract_id FROM escrow_accounts WHERE held_amount = 70000");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($accounts as $acc) {
        $contractId = $acc['contract_id'];
        
        // Find the actual budget for this contract via Project table
        $stmtBudget = $pdo->prepare("SELECT p.budget FROM Project p JOIN Contract c ON p.project_id = c.project_id WHERE c.contract_id = :id");
        $stmtBudget->execute([':id' => $contractId]);
        $project = $stmtBudget->fetch(PDO::FETCH_ASSOC);
        
        if ($project) {
            $realBudget = $project['budget'];
            
            // Update the escrow account to match the real budget
            $stmtUpdate = $pdo->prepare("UPDATE escrow_accounts SET total_amount = :amt, held_amount = :amt WHERE contract_id = :id");
            $stmtUpdate->execute([
                ':amt' => $realBudget,
                ':id' => $contractId
            ]);
            
            echo "Updated Contract #$contractId escrow to match budget: $realBudget\n";
        }
    }
    
    echo "Finished fixing escrow mock data!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
