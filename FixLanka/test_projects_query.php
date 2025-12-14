<?php
// Direct test of the getAcceptedProjects query
session_start();

// Set up test session (replace with your actual user_id)
$_SESSION['user_id'] = 2; // Company user
$_SESSION['user_role'] = 'company';

require_once '../config/database.php';

echo "<h1>Testing Get Accepted Projects Query</h1>";

echo "<h2>Session Info:</h2>";
echo "User ID: " . $_SESSION['user_id'] . "<br>";
echo "User Role: " . $_SESSION['user_role'] . "<br>";

$companyId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.project_id,
            p.title as project_title,
            p.description as project_description,
            p.project_type,
            p.location,
            p.budget as quoted_price,
            p.start_date as proposed_start_date,
            p.end_date as proposed_end_date,
            p.status,
            p.company_id,
            p.customer_id,
            u.f_name,
            u.l_name,
            u.email as customer_email
        FROM Project p
        INNER JOIN User u ON p.customer_id = u.user_id
        WHERE p.company_id = ?
        AND p.status IN ('planned', 'active')
        AND NOT EXISTS (
            SELECT 1 FROM Contract c
            WHERE c.project_id = p.project_id
        )
        ORDER BY p.start_date DESC
    ");
    
    $stmt->execute([$companyId]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Query Results:</h2>";
    echo "Found " . count($projects) . " projects<br><br>";
    
    if (count($projects) > 0) {
        echo "<pre>";
        print_r($projects);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>No projects found!</p>";
        
        // Let's check what's in the Project table
        echo "<h3>All Projects in Database:</h3>";
        $allStmt = $pdo->query("SELECT project_id, title, status, company_id, customer_id FROM Project");
        $allProjects = $allStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($allProjects);
        echo "</pre>";
        
        // Check if any contracts exist
        echo "<h3>All Contracts in Database:</h3>";
        $contractStmt = $pdo->query("SELECT contract_id, project_id FROM Contract");
        $contracts = $contractStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($contracts);
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
