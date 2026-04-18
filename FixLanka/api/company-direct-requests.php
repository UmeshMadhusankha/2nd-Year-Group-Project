<?php
/**
 * Company Direct Requests API
 *
 * Returns requests that are "direct" to the company in the current UI meaning:
 * requests for which this company has already submitted a quotation.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? null) !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$companyId = (int)($_SESSION['company_id'] ?? 0);

if (!$companyId && $userId > 0) {
    $companyData = getCompanyByUserId($pdo, $userId);
    if ($companyData && isset($companyData['company_id'])) {
        $companyId = (int)$companyData['company_id'];
    }
}

if (!$companyId) {
    $companyId = $userId;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
if ($limit < 1) {
    $limit = 50;
}
if ($limit > 200) {
    $limit = 200;
}

try {
    // Preferred schema: jobrequest.dateCreated
    $stmt = $pdo->prepare("
        SELECT
            jr.request_id,
            jr.user_id,
            jr.title,
            jr.description,
            jr.dateCreated AS created_at,
            jr.finish_date,
            c.name AS category_name,
            u.f_name AS customer_fname,
            u.l_name AS customer_lname,
            u.email AS customer_email,
            cq.status AS quote_status,
            cq.total_amount
        FROM (
            SELECT cq1.*
            FROM companyquotation cq1
            INNER JOIN (
                SELECT request_id, MAX(updated_at) AS max_updated
                FROM companyquotation
                WHERE company_id = ?
                GROUP BY request_id
            ) latest
                ON latest.request_id = cq1.request_id AND latest.max_updated = cq1.updated_at
            WHERE cq1.company_id = ?
        ) cq
        INNER JOIN jobrequest jr ON jr.request_id = cq.request_id
        INNER JOIN category c ON c.category_id = jr.category_id
        INNER JOIN user u ON u.user_id = jr.user_id
        WHERE jr.finish_date >= CURDATE()
        ORDER BY jr.dateCreated DESC
        LIMIT {$limit}
    ");

    $stmt->execute([$companyId, $companyId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
} catch (PDOException $e) {
    // Fallback schema: jobrequest.created_at
    try {
        $stmt = $pdo->prepare("
            SELECT
                jr.request_id,
                jr.user_id,
                jr.title,
                jr.description,
                jr.created_at,
                jr.finish_date,
                c.name AS category_name,
                u.f_name AS customer_fname,
                u.l_name AS customer_lname,
                u.email AS customer_email,
                cq.status AS quote_status,
                cq.total_amount
            FROM (
                SELECT cq1.*
                FROM companyquotation cq1
                INNER JOIN (
                    SELECT request_id, MAX(updated_at) AS max_updated
                    FROM companyquotation
                    WHERE company_id = ?
                    GROUP BY request_id
                ) latest
                    ON latest.request_id = cq1.request_id AND latest.max_updated = cq1.updated_at
                WHERE cq1.company_id = ?
            ) cq
            INNER JOIN jobrequest jr ON jr.request_id = cq.request_id
            INNER JOIN category c ON c.category_id = jr.category_id
            INNER JOIN user u ON u.user_id = jr.user_id
            WHERE jr.finish_date >= CURDATE()
            ORDER BY jr.created_at DESC
            LIMIT {$limit}
        ");

        $stmt->execute([$companyId, $companyId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    } catch (PDOException $e2) {
        error_log('company-direct-requests error: ' . $e2->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
        exit;
    }
}
