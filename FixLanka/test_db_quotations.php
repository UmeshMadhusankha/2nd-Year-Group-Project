<?php
/**
 * Test file to check CompanyQuotation data directly from database
 */

require_once 'config/database.php';

echo "<h1>Database Quotations Test</h1>";

// Test 1: Check if quotations exist
echo "<h2>Test 1: All Quotations in CompanyQuotation Table</h2>";
try {
    $sql = "SELECT quotation_id, user_id, request_id, title, status, created_at 
            FROM CompanyQuotation 
            ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Total Quotations:</strong> " . count($quotations) . "</p>";
    echo "<pre>" . print_r($quotations, true) . "</pre>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 2: Check quotations for user_id = 1
echo "<h2>Test 2: Quotations for user_id = 1</h2>";
try {
    $sql = "SELECT quotation_id, user_id, request_id, title, status, created_at 
            FROM CompanyQuotation 
            WHERE user_id = 1
            ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Quotations for user_id=1:</strong> " . count($quotations) . "</p>";
    echo "<pre>" . print_r($quotations, true) . "</pre>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 3: Check the full JOIN query (same as model)
echo "<h2>Test 3: Full JOIN Query (As Used in Model)</h2>";
try {
    $sql = "SELECT 
        cq.*,
        jr.title as job_title,
        jr.description as job_description,
        jr.district,
        jr.address,
        jr.finish_date,
        jr.urgency,
        jr.service_provider_type,
        c.name as category_name,
        u.f_name as customer_fname,
        u.l_name as customer_lname,
        u.email as customer_email,
        u.phoneNumber as customer_phone
    FROM CompanyQuotation cq
    INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
    INNER JOIN User u ON jr.user_id = u.user_id
    LEFT JOIN Category c ON jr.category_id = c.category_id
    WHERE cq.user_id = 1";
    
    $stmt = $pdo->query($sql);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Quotations with JOIN for user_id=1:</strong> " . count($quotations) . "</p>";
    echo "<pre>" . print_r($quotations, true) . "</pre>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>SQL State: " . $e->getCode() . "</p>";
}

// Test 4: Check if JobRequests exist
echo "<h2>Test 4: Check JobRequest Table</h2>";
try {
    $sql = "SELECT request_id, user_id, title, status FROM JobRequest LIMIT 5";
    $stmt = $pdo->query($sql);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Sample JobRequests:</strong> " . count($requests) . "</p>";
    echo "<pre>" . print_r($requests, true) . "</pre>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 5: Check foreign key relationships
echo "<h2>Test 5: Check Foreign Key Relationships</h2>";
try {
    $sql = "SELECT 
        cq.quotation_id,
        cq.request_id,
        cq.user_id as company_user_id,
        jr.request_id as jr_request_id,
        jr.user_id as customer_user_id,
        jr.title as job_title
    FROM CompanyQuotation cq
    LEFT JOIN JobRequest jr ON cq.request_id = jr.request_id
    WHERE cq.user_id = 1";
    
    $stmt = $pdo->query($sql);
    $relationships = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Foreign Key Check:</strong> " . count($relationships) . "</p>";
    echo "<pre>" . print_r($relationships, true) . "</pre>";
    
    // Check for orphaned records
    $orphaned = array_filter($relationships, function($row) {
        return $row['jr_request_id'] === null;
    });
    
    if (count($orphaned) > 0) {
        echo "<p style='color: red;'><strong>WARNING: Found " . count($orphaned) . " orphaned quotations (JobRequest doesn't exist)</strong></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test 6: Test the actual model
echo "<h2>Test 6: Test CompanyQuotationModel::getAll()</h2>";
require_once 'models/CompanyQuotationModel.php';
$model = new CompanyQuotation($pdo);

try {
    $quotations = $model->getAll(['user_id' => 1]);
    echo "<p><strong>Model returned:</strong> " . count($quotations) . " quotations</p>";
    echo "<pre>" . print_r($quotations, true) . "</pre>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

?>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    h1 { color: #333; }
    h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 5px; margin-top: 30px; }
    pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>
