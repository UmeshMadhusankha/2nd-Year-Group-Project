<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\tests\test_quotation_acceptance.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ContractController.php';
require_once __DIR__ . '/../models/ContractModel.php';

echo "1. Setting up test environment...\n";

// MOCK SESSION
$_SESSION['user_id'] = 9991; // Test Customer
$_SESSION['user_role'] = 'customer';

// Helper to clean up
function cleanup($pdo) {
    echo "Cleaning up test data...\n";
    $pdo->exec("DELETE FROM contract WHERE project_title = 'TEST_ACCEPT_QUOTE'");
    $pdo->exec("DELETE FROM companyquotation WHERE title = 'TEST_ACCEPT_QUOTE'");
    $pdo->exec("DELETE FROM jobrequest WHERE title = 'TEST_ACCEPT_QUOTE'");
}

try {
    cleanup($pdo);

    // 1. Create Job Request
    echo "2. Creating Job Request...\n";
    $stmt = $pdo->prepare("INSERT INTO jobrequest (user_id, category_id, title, description, district, address, urgency, status, created_at) VALUES (?, 1, 'TEST_ACCEPT_QUOTE', 'Test Description', 'Colombo', 'Test Address', 'medium', 'open', NOW())");
    $stmt->execute([$_SESSION['user_id']]);
    $requestId = $pdo->lastInsertId();
    echo "   Job Request ID: $requestId\n";

    // 2. Create Quotation (as Company)
    echo "3. Creating Quotation...\n";
    $companyId = 9992; // Test Company
    $stmt = $pdo->prepare("INSERT INTO companyquotation (request_id, company_id, title, labor_cost, material_cost, total_amount, start_date, completion_date, estimated_duration, status, created_at) VALUES (?, ?, 'TEST_ACCEPT_QUOTE', 1000, 500, 1500, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 7, 'pending', NOW())");
    $stmt->execute([$requestId, $companyId]);
    $quotationId = $pdo->lastInsertId();
    echo "   Quotation ID: $quotationId\n";

    // 3. Test Accept Logic via Controller
    echo "4. Testing Accept Quotation...\n";
    $controller = new ContractController();
    
    // We need to capture output because controller echoes JSON
    ob_start();
    $controller->acceptQuotation($quotationId, $_SESSION['user_id']);
    $output = ob_get_clean();
    
    echo "   API Output: $output\n";
    $result = json_decode($output, true);

    if ($result['success']) {
        echo "   [PASS] Acceptance successful.\n";
        $contractId = $result['contract_id'];
        
        // 4. Verify Contract Exists
        echo "5. Verifying Contract in DB...\n";
        $stmt = $pdo->prepare("SELECT * FROM contract WHERE contract_id = ?");
        $stmt->execute([$contractId]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contract) {
            echo "   [PASS] Contract found. Status: " . $contract['status'] . "\n";
            echo "   Undo Deadline: " . $contract['undo_deadline'] . "\n";
            
            if ($contract['undo_available'] == 1) {
                 echo "   [PASS] Undo available is set to 1.\n";
            } else {
                 echo "   [FAIL] Undo available is NOT 1.\n";
            }

            // Verify Job Request Status
            $stmt = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = ?");
            $stmt->execute([$requestId]);
            $jobStatus = $stmt->fetchColumn();
            echo "   Job Request Status: $jobStatus\n";
            
            if ($jobStatus === 'Assigned') {
                echo "   [PASS] Job Request set to Assigned.\n";
            } else {
                 echo "   [FAIL] Job Request status is $jobStatus (expected Assigned).\n";
            }

        } else {
            echo "   [FAIL] Contract not found in DB.\n";
        }

    } else {
        echo "   [FAIL] Acceptance failed: " . ($result['message'] ?? 'Unknown error') . "\n";
    }

} catch (Exception $e) {
    echo "   [ERROR] Exception: " . $e->getMessage() . "\n";
} finally {
    // optional cleanup
    // cleanup($pdo); 
}
?>
