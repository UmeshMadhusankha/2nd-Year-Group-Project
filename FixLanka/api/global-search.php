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
require_once __DIR__ . '/helpers.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$companyId = (int)($_SESSION['company_id'] ?? 0);

// Resolve company id robustly (some setups store a user_id in session)
if (!$companyId && $userId > 0) {
    $companyData = getCompanyByUserId($pdo, $userId);
    if ($companyData && isset($companyData['company_id'])) {
        $companyId = (int)$companyData['company_id'];
    }
}

if (!$companyId) {
    // Fallback to user id as company id
    $companyId = $userId;
}

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
            AND (status IS NULL OR status <> 'planned')
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

    // 2. Repair Requests (Scoped to what this company can see)
    if ($category === 'all' || $category === 'requests') {
        error_log("Searching requests for: $query");
        $searchTerm = "%$query%";

        // Visible requests for company search:
        // - Public marketplace requests for companies (pending + not expired) that this company hasn't quoted on yet
        // - Direct requests (approximated as requests this company has already quoted on)
        // NOTE: This prevents leaking unrelated requests across companies.

        try {
            $stmt = $pdo->prepare("
                SELECT
                    jr.request_id,
                    jr.title,
                    jr.description,
                    CASE
                        WHEN EXISTS (
                            SELECT 1 FROM companyquotation cq
                            WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                        ) THEN 'direct'
                        ELSE 'public'
                    END AS request_scope
                FROM jobrequest jr
                WHERE
                    (
                        EXISTS (
                            SELECT 1 FROM companyquotation cq
                            WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                        )
                        OR (
                            (jr.service_provider_type = 'company' OR jr.service_provider_type = 'both')
                            AND jr.status = 'pending'
                            AND jr.finish_date >= CURDATE()
                            AND NOT EXISTS (
                                SELECT 1 FROM companyquotation cq
                                WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                            )
                        )
                    )
                    AND jr.finish_date >= CURDATE()
                    AND (jr.title LIKE ? OR jr.description LIKE ?)
                ORDER BY jr.dateCreated DESC
                LIMIT 5
            ");
            $stmt->execute([$companyId, $companyId, $companyId, $searchTerm, $searchTerm]);
        } catch (PDOException $e) {
            // Fallback for older schema using created_at
            $stmt = $pdo->prepare("
                SELECT
                    jr.request_id,
                    jr.title,
                    jr.description,
                    CASE
                        WHEN EXISTS (
                            SELECT 1 FROM companyquotation cq
                            WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                        ) THEN 'direct'
                        ELSE 'public'
                    END AS request_scope
                FROM jobrequest jr
                WHERE
                    (
                        EXISTS (
                            SELECT 1 FROM companyquotation cq
                            WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                        )
                        OR (
                            (jr.service_provider_type = 'company' OR jr.service_provider_type = 'both')
                            AND jr.status = 'pending'
                            AND jr.finish_date >= CURDATE()
                            AND NOT EXISTS (
                                SELECT 1 FROM companyquotation cq
                                WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                            )
                        )
                    )
                    AND jr.finish_date >= CURDATE()
                    AND (jr.title LIKE ? OR jr.description LIKE ?)
                ORDER BY jr.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$companyId, $companyId, $companyId, $searchTerm, $searchTerm]);
        }

        $requestCount = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requestCount++;
            $results[] = [
                'type' => 'request',
                'title' => $row['title'],
                'subtitle' => ucfirst(($row['request_scope'] ?? 'request')) . ' Request #' . $row['request_id'],
                'url' => 'repair-requests.php?request_id=' . $row['request_id'] . '&action=details',
                'icon' => 'fa-tools'
            ];
        }
        error_log("Found $requestCount requests (scoped)");
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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
}
?>
