<?php
/**
 * Global Search API
 * specific for company dashboard
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // JSON response, so hide HTML errors

// Set JSON header
header('Content-Type: application/json');

// Include database connection
require_once __DIR__ . '/../config/databse.php';

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$companyId = $_SESSION['user_id']; // For company users, user_id is the company_id

// Get search parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Validate query
if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'results' => []]);
    exit;
}

// Debug logging
error_log("Search Debug - Query: $query, Category: $category, Company: $companyId");

$results = [];

try {
    // 1. Projects Search (FIXED: lowercase table name)
    if ($category === 'all' || $category === 'projects') {
        error_log("Searching projects for: $query");
        $stmt = $pdo->prepare("
            SELECT project_id, title, description 
            FROM project 
            WHERE company_id = ? 
            AND (title LIKE ? OR description LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$companyId, $searchTerm, $searchTerm]);
        
        $projectCount = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $projectCount++;
            $results[] = [
                'type' => 'project',
                'title' => $row['title'],
                'subtitle' => 'Project #' . $row['project_id'],
                'url' => 'projects.php?id=' . $row['project_id'],
                'icon' => 'fa-project-diagram'
            ];
        }
        error_log("Found $projectCount projects");
    }

    // 2. Repair Requests (FIXED: lowercase table name, search all requests)
    if ($category === 'all' || $category === 'requests') {
        error_log("Searching requests for: $query");
        // Search ALL repair requests (not just those with quotations)
        // Company can see all available requests to potentially bid on
        $stmt = $pdo->prepare("
            SELECT request_id, title, description
            FROM jobrequest
            WHERE (title LIKE ? OR description LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm]);
        
        $requestCount = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requestCount++;
            $results[] = [
                'type' => 'request',
                'title' => $row['title'],
                'subtitle' => 'Request #' . $row['request_id'],
                'url' => 'repair-requests.php?id=' . $row['request_id'],
                'icon' => 'fa-tools'
            ];
        }
        error_log("Found $requestCount requests");
    }

    // 3. Workforce (Placeholder - assuming table Employee or similar exists)
    if ($category === 'all' || $category === 'workforce') {
        // Implement when workforce table is clear
    }

    // 4. Payments (FIXED: lowercase table names + correct JOIN via contract)
    if ($category === 'all' || $category === 'payments') {
        error_log("Searching payments for: $query");
        $stmt = $pdo->prepare("
            SELECT mp.payment_id, mp.amount, p.title as project_title
            FROM milestonepayment mp
            JOIN milestone m ON mp.milestone_id = m.milestone_id
            JOIN contract c ON m.contract_id = c.contract_id
            JOIN project p ON c.project_id = p.project_id
            WHERE p.company_id = ?
            AND (p.title LIKE ? OR mp.payment_id LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$companyId, $searchTerm, $searchTerm]);
        
        $paymentCount = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paymentCount++;
            $results[] = [
                'type' => 'payment',
                'title' => 'Payment #' . $row['payment_id'],
                'subtitle' => $row['project_title'] . ' - LKR ' . number_format($row['amount'], 2),
                'url' => 'payments.php?id=' . $row['payment_id'],
                'icon' => 'fa-file-invoice-dollar'
            ];
        }
        error_log("Found $paymentCount payments");
    }

    error_log("Total results found: " . count($results));
    echo json_encode(['success' => true, 'results' => $results]);

} catch (PDOException $e) {
    error_log("Search Error: " . $e->getMessage());
    // Return more detailed error for debugging
    echo json_encode([
        'success' => false, 
        'message' => 'Database error',
        'error' => $e->getMessage(),
        'query' => $query,
        'category' => $category
    ]);
}
