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
require_once '../../includes/db_connection.php';

// Start session
session_start();

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'company') {
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

$results = [];

try {
    // 1. Projects Search
    if ($category === 'all' || $category === 'projects') {
        $stmt = $pdo->prepare("
            SELECT project_id, title, description 
            FROM Project 
            WHERE company_id = ? 
            AND (title LIKE ? OR description LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$companyId, $searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'type' => 'project',
                'title' => $row['title'],
                'subtitle' => 'Project #' . $row['project_id'],
                'url' => 'projects.php?id=' . $row['project_id'],
                'icon' => 'fa-project-diagram'
            ];
        }
    }

    // 2. Repair Requests (Requests)
    if ($category === 'all' || $category === 'requests') {
        // Search in JobRequest where connected to company (via Quotation or Assignment)
        // This is a simplified query; adjust based on actual schema relations
        $stmt = $pdo->prepare("
            SELECT jr.request_id, jr.title, jr.description
            FROM JobRequest jr
            JOIN Quotation q ON jr.request_id = q.request_id
            WHERE q.company_id = ?
            AND (jr.title LIKE ? OR jr.description LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$companyId, $searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'type' => 'request',
                'title' => $row['title'],
                'subtitle' => 'Request #' . $row['request_id'],
                'url' => 'repair-requests.php?id=' . $row['request_id'],
                'icon' => 'fa-tools'
            ];
        }
    }

    // 3. Workforce (Placeholder - assuming table Employee or similar exists)
    if ($category === 'all' || $category === 'workforce') {
        // Implement when workforce table is clear
    }

    // 4. Payments
    if ($category === 'all' || $category === 'payments') {
        $stmt = $pdo->prepare("
            SELECT mp.payment_id, mp.amount, p.title as project_title
            FROM MilestonePayment mp
            JOIN Milestone m ON mp.milestone_id = m.milestone_id
            JOIN Project p ON m.project_id = p.project_id
            WHERE p.company_id = ?
            AND (p.title LIKE ? OR mp.payment_id LIKE ?)
            LIMIT 5
        ");
        $searchTerm = "%$query%";
        $stmt->execute([$companyId, $searchTerm, $searchTerm]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = [
                'type' => 'payment',
                'title' => 'Payment #' . $row['payment_id'],
                'subtitle' => $row['project_title'] . ' - LKR ' . number_format($row['amount'], 2),
                'url' => 'payments.php?id=' . $row['payment_id'],
                'icon' => 'fa-file-invoice-dollar'
            ];
        }
    }

    echo json_encode(['success' => true, 'results' => $results]);

} catch (PDOException $e) {
    error_log("Search Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
