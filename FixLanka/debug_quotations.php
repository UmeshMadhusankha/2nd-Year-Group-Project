<?php
// Debug script to check accepted quotations
require_once 'config/database.php';
session_start();

echo "=== Debugging Accepted Quotations ===\n\n";

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    echo "❌ Not logged in!\n";
    exit;
}

$companyId = $_SESSION['user_id'];
echo "✅ Logged in as user_id: $companyId\n";
echo "Role: " . ($_SESSION['user_role'] ?? 'Not set') . "\n\n";

// Check CompanyQuotation table
echo "--- Checking CompanyQuotation table ---\n";
$stmt = $pdo->prepare("SELECT quotation_id, request_id, user_id, title, total_amount, status FROM CompanyQuotation WHERE user_id = ?");
$stmt->execute([$companyId]);
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total quotations for company: " . count($quotations) . "\n\n";

if (count($quotations) > 0) {
    foreach ($quotations as $q) {
        echo "Quotation ID: {$q['quotation_id']}\n";
        echo "  - Title: {$q['title']}\n";
        echo "  - Amount: {$q['total_amount']}\n";
        echo "  - Status: {$q['status']}\n";
        echo "  - Request ID: {$q['request_id']}\n\n";
    }
} else {
    echo "❌ No quotations found for your company!\n\n";
}

// Check accepted quotations
echo "--- Checking ACCEPTED quotations ---\n";
$stmt = $pdo->prepare("SELECT quotation_id, title, status FROM CompanyQuotation WHERE user_id = ? AND status = 'accepted'");
$stmt->execute([$companyId]);
$accepted = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Accepted quotations: " . count($accepted) . "\n\n";

if (count($accepted) > 0) {
    foreach ($accepted as $q) {
        echo "✅ Quotation {$q['quotation_id']}: {$q['title']} - Status: {$q['status']}\n";
    }
} else {
    echo "❌ No ACCEPTED quotations found!\n";
    echo "💡 The quotations must have status='accepted' to appear in contract creation.\n\n";
}

// Test the actual query used in the API
echo "\n--- Testing API Query ---\n";
$stmt = $pdo->prepare("
    SELECT 
        q.quotation_id,
        q.request_id,
        q.total_amount as price_quoted,
        q.start_date as proposed_start_date,
        q.completion_date as proposed_end_date,
        q.description as quote_description,
        q.title as request_title,
        jr.description as request_description,
        jr.district,
        jr.address,
        jr.urgency,
        u.username as customer_name,
        u.email as customer_email,
        u.phone as customer_phone,
        jr.category as category_name
    FROM CompanyQuotation q
    INNER JOIN JobRequest jr ON q.request_id = jr.request_id
    INNER JOIN User u ON jr.customer_id = u.user_id
    WHERE q.user_id = ?
    AND q.status = 'accepted'
    AND NOT EXISTS (
        SELECT 1 FROM Contract c
        INNER JOIN Project p ON c.project_id = p.project_id
        WHERE p.company_id = q.user_id 
        AND p.customer_id = jr.customer_id
        AND p.title = q.title
    )
    ORDER BY q.created_at DESC
");

$stmt->execute([$companyId]);
$apiResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "API Query Results: " . count($apiResults) . " projects\n\n";

if (count($apiResults) > 0) {
    echo "✅ Projects that should appear:\n";
    foreach ($apiResults as $r) {
        echo "  - {$r['request_title']} (LKR {$r['price_quoted']})\n";
        echo "    Customer: {$r['customer_name']}\n";
        echo "    Location: {$r['address']}\n\n";
    }
} else {
    echo "❌ No projects returned by API query!\n\n";
    
    // Check why
    echo "Troubleshooting:\n";
    
    // Check if JobRequest exists
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM JobRequest WHERE request_id IN (SELECT request_id FROM CompanyQuotation WHERE user_id = ?)");
    $stmt->execute([$companyId]);
    $jrCount = $stmt->fetch()['count'];
    echo "1. JobRequests linked to your quotations: $jrCount\n";
    
    // Check if User (customer) exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM User u
        INNER JOIN JobRequest jr ON u.user_id = jr.customer_id
        WHERE jr.request_id IN (SELECT request_id FROM CompanyQuotation WHERE user_id = ?)
    ");
    $stmt->execute([$companyId]);
    $userCount = $stmt->fetch()['count'];
    echo "2. Customers linked to job requests: $userCount\n";
    
    // Check Contract exclusion
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM CompanyQuotation q
        INNER JOIN JobRequest jr ON q.request_id = jr.request_id
        WHERE q.user_id = ?
        AND q.status = 'accepted'
        AND EXISTS (
            SELECT 1 FROM Contract c
            INNER JOIN Project p ON c.project_id = p.project_id
            WHERE p.company_id = q.user_id 
            AND p.customer_id = jr.customer_id
            AND p.title = q.title
        )
    ");
    $stmt->execute([$companyId]);
    $excludedCount = $stmt->fetch()['count'];
    echo "3. Projects excluded (already have contracts): $excludedCount\n";
}

echo "\n=================================\n";
?>
