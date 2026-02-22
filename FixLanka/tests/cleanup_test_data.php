<?php
/**
 * Cleanup Script: Remove ALL test data from every table
 * 
 * Run: http://localhost/2nd-Year-Group-Project/FixLanka/tests/cleanup_test_data.php
 */

require_once __DIR__ . '/../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FixLanka — Test Data Cleanup</h1>";
echo "<pre>";

global $pdo;

$totalDeleted = 0;

function cleanup($pdo, $table, $query, $params = []) {
    global $totalDeleted;
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->rowCount();
        $totalDeleted += $count;
        if ($count > 0) {
            echo "<span style='color:orange'>[DELETED]</span> $table: $count row(s)\n";
        } else {
            echo "<span style='color:gray'>[CLEAN]</span> $table: no test data found\n";
        }
    } catch (PDOException $e) {
        echo "<span style='color:red'>[ERROR]</span> $table: " . $e->getMessage() . "\n";
    }
}

echo "\n<b>Step 1: Finding test user/company IDs...</b>\n";

// Find all test user IDs
$stmt = $pdo->prepare("SELECT user_id, email FROM user WHERE email LIKE '%@test.com' OR email LIKE '%test%@example.com' OR f_name = 'TestPhase' OR f_name = 'Test' OR email LIKE 'test_%'");
$stmt->execute();
$testUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$testUserIds = array_column($testUsers, 'user_id');

echo "Found " . count($testUsers) . " test user(s):\n";
foreach ($testUsers as $u) {
    echo "  - ID {$u['user_id']}: {$u['email']}\n";
}

// Find all test company IDs
$stmt = $pdo->prepare("SELECT company_id, email, name FROM company WHERE email LIKE '%@test.com' OR email LIKE '%test%@example.com' OR name LIKE 'PhaseTestCo%' OR name LIKE 'Test%Co%' OR email LIKE 'test_%'");
$stmt->execute();
$testCompanies = $stmt->fetchAll(PDO::FETCH_ASSOC);
$testCompanyIds = array_column($testCompanies, 'company_id');

echo "Found " . count($testCompanies) . " test company/companies:\n";
foreach ($testCompanies as $c) {
    echo "  - ID {$c['company_id']}: {$c['name']} ({$c['email']})\n";
}

if (empty($testUserIds) && empty($testCompanyIds)) {
    echo "\n<span style='color:green; font-weight:bold'>✅ No test data found. Database is clean!</span>\n";
    echo "</pre>";
    exit;
}

// Find test contracts (linked to test users or companies)
$testContractIds = [];
$testProjectIds = [];
$testQuotationIds = [];
$testRequestIds = [];

if (!empty($testUserIds) || !empty($testCompanyIds)) {
    $userPlaceholders = !empty($testUserIds) ? implode(',', array_fill(0, count($testUserIds), '?')) : '0';
    $companyPlaceholders = !empty($testCompanyIds) ? implode(',', array_fill(0, count($testCompanyIds), '?')) : '0';
    $allIds = array_merge($testUserIds, $testCompanyIds);
    
    // Contracts
    $stmt = $pdo->prepare("SELECT contract_id, project_id, quotation_id, job_request_id FROM contract WHERE customer_id IN ($userPlaceholders) OR company_id IN ($companyPlaceholders)");
    $stmt->execute($allIds);
    $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $testContractIds = array_column($contracts, 'contract_id');
    $testProjectIds = array_filter(array_column($contracts, 'project_id'));
    $testQuotationIds = array_filter(array_column($contracts, 'quotation_id'));
    $testRequestIds = array_filter(array_column($contracts, 'job_request_id'));
    
    // Also find quotations not yet linked to contracts
    $stmt = $pdo->prepare("SELECT quotation_id, request_id FROM companyquotation WHERE user_id IN ($userPlaceholders) OR company_id IN ($companyPlaceholders)");
    $stmt->execute($allIds);
    $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $testQuotationIds = array_values(array_unique(array_merge($testQuotationIds, array_column($quotes, 'quotation_id'))));
    $testRequestIds = array_values(array_unique(array_merge($testRequestIds, array_filter(array_column($quotes, 'request_id')))));
    
    // Also find job requests from test users
    $stmt = $pdo->prepare("SELECT request_id FROM jobrequest WHERE user_id IN ($userPlaceholders)");
    $stmt->execute($testUserIds);
    $requests = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $testRequestIds = array_values(array_unique(array_merge($testRequestIds, $requests)));
}

echo "\nFound " . count($testContractIds) . " test contract(s)\n";
echo "Found " . count($testProjectIds) . " test project(s)\n";
echo "Found " . count($testQuotationIds) . " test quotation(s)\n";
echo "Found " . count($testRequestIds) . " test job request(s)\n";

echo "\n<b>Step 2: Deleting test data (child tables first)...</b>\n\n";

// Delete from child tables first (foreign key order)
if (!empty($testContractIds)) {
    $cPlaceholders = implode(',', array_fill(0, count($testContractIds), '?'));
    
    cleanup($pdo, 'escrow_transaction', 
        "DELETE FROM escrow_transaction WHERE related_contract_id IN ($cPlaceholders) OR related_milestone_id IN (SELECT milestone_id FROM contract_milestone WHERE contract_id IN ($cPlaceholders))",
        array_merge($testContractIds, $testContractIds));
    
    cleanup($pdo, 'contract_notifications', 
        "DELETE FROM contract_notifications WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_timeline', 
        "DELETE FROM contract_timeline WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_chats', 
        "DELETE FROM contract_chats WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_time_logs', 
        "DELETE FROM contract_time_logs WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_invoices', 
        "DELETE FROM contract_invoices WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_budget_adjustments', 
        "DELETE FROM contract_budget_adjustments WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_milestone', 
        "DELETE FROM contract_milestone WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract_audit_log', 
        "DELETE FROM contract_audit_log WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'escrow_accounts', 
        "DELETE FROM escrow_accounts WHERE contract_id IN ($cPlaceholders)", $testContractIds);
    
    cleanup($pdo, 'contract', 
        "DELETE FROM contract WHERE contract_id IN ($cPlaceholders)", $testContractIds);
}

// Escrow wallets for test users/companies
if (!empty($testUserIds)) {
    $uPlaceholders = implode(',', array_fill(0, count($testUserIds), '?'));
    
    // Delete transactions for test user wallets
    cleanup($pdo, 'escrow_transaction (user wallets)', 
        "DELETE FROM escrow_transaction WHERE wallet_id IN (SELECT wallet_id FROM escrow_wallet WHERE user_id IN ($uPlaceholders))", $testUserIds);
    
    cleanup($pdo, 'escrow_wallet (users)', 
        "DELETE FROM escrow_wallet WHERE user_id IN ($uPlaceholders)", $testUserIds);
}

if (!empty($testCompanyIds)) {
    $coPlaceholders = implode(',', array_fill(0, count($testCompanyIds), '?'));
    
    cleanup($pdo, 'escrow_transaction (company wallets)', 
        "DELETE FROM escrow_transaction WHERE wallet_id IN (SELECT wallet_id FROM escrow_wallet WHERE company_id IN ($coPlaceholders))", $testCompanyIds);
    
    cleanup($pdo, 'escrow_wallet (companies)', 
        "DELETE FROM escrow_wallet WHERE company_id IN ($coPlaceholders)", $testCompanyIds);
}

// Projects — delete by ID and also by test user/company ownership
if (!empty($testProjectIds)) {
    $pPlaceholders = implode(',', array_fill(0, count($testProjectIds), '?'));
    cleanup($pdo, 'project (by ID)', "DELETE FROM project WHERE project_id IN ($pPlaceholders)", array_values($testProjectIds));
}
// Also delete any remaining projects owned by test users/companies
if (!empty($testUserIds)) {
    $uPlaceholders = implode(',', array_fill(0, count($testUserIds), '?'));
    cleanup($pdo, 'project (by customer)', "DELETE FROM project WHERE customer_id IN ($uPlaceholders)", $testUserIds);
}
if (!empty($testCompanyIds)) {
    $coPlaceholders = implode(',', array_fill(0, count($testCompanyIds), '?'));
    cleanup($pdo, 'project (by company)', "DELETE FROM project WHERE company_id IN ($coPlaceholders)", $testCompanyIds);
}

// Quotations
if (!empty($testQuotationIds)) {
    $qPlaceholders = implode(',', array_fill(0, count($testQuotationIds), '?'));
    cleanup($pdo, 'companyquotation', "DELETE FROM companyquotation WHERE quotation_id IN ($qPlaceholders)", $testQuotationIds);
}

// Job Requests
if (!empty($testRequestIds)) {
    $rPlaceholders = implode(',', array_fill(0, count($testRequestIds), '?'));
    cleanup($pdo, 'jobrequest', "DELETE FROM jobrequest WHERE request_id IN ($rPlaceholders)", $testRequestIds);
}

// Test users and companies (last, after all dependent data is gone)
if (!empty($testUserIds)) {
    $uPlaceholders = implode(',', array_fill(0, count($testUserIds), '?'));
    cleanup($pdo, 'user', "DELETE FROM user WHERE user_id IN ($uPlaceholders)", $testUserIds);
}

if (!empty($testCompanyIds)) {
    $coPlaceholders = implode(',', array_fill(0, count($testCompanyIds), '?'));
    cleanup($pdo, 'company', "DELETE FROM company WHERE company_id IN ($coPlaceholders)", $testCompanyIds);
}

echo "\n<b>═══════════════════════════════════════════════════</b>\n";
echo "<b>  CLEANUP COMPLETE</b>\n";
echo "<b>═══════════════════════════════════════════════════</b>\n\n";
echo "Total rows deleted: <b>$totalDeleted</b>\n";

if ($totalDeleted > 0) {
    echo "\n<span style='color:green; font-weight:bold'>✅ All test data removed successfully!</span>\n";
} else {
    echo "\n<span style='color:green; font-weight:bold'>✅ Database was already clean.</span>\n";
}

echo "</pre>";
