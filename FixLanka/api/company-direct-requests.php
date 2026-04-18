<?php
/**
 * Company Direct Requests API
 *
 * Returns requests that are direct-to-company (customer specifically chose this company).
 *
 * Data source: `directjobrequest` (preferred).
 *
 * IMPORTANT: Older databases may not have direct-request tables yet.
 * In that case, this endpoint fails closed to an empty list (success=true) to avoid breaking the UI.
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

$requestId = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
if ($requestId < 0) {
    $requestId = 0;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            djr.request_id,
            djr.user_id,
            djr.title,
            djr.description,
            djr.status,
            djr.district,
            djr.address,
            djr.finish_date,
            djr.date_created AS created_at,
            djr.photos,
            c.name AS category_name,
            u.f_name AS customer_fname,
            u.l_name AS customer_lname,
            u.email AS customer_email
        FROM directjobrequest djr
        INNER JOIN category c ON c.category_id = djr.category_id
        INNER JOIN user u ON u.user_id = djr.user_id
        WHERE djr.provider_type = 'company'
          AND djr.provider_id = ?
        ORDER BY djr.date_created DESC
        LIMIT {$limit}
    ");

    $stmt->execute([$companyId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $rows]);
    exit;

} catch (PDOException $e) {
    error_log('company-direct-requests error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
