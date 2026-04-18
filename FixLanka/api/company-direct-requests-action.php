<?php
/**
 * Company Direct Requests Action API
 *
 * Allows a company to accept or reject a direct request from a customer.
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

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data) {
    // try to get from $_POST
    $data = $_POST;
}

$requestId = isset($data['request_id']) ? (int)$data['request_id'] : 0;
$action = isset($data['action']) ? trim($data['action']) : '';

if ($requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID']);
    exit;
}

if (!in_array($action, ['accept', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    // Verify the request belongs to this company
    $stmt = $pdo->prepare("
        SELECT status
        FROM directjobrequest
        WHERE request_id = ? AND provider_type = 'company' AND provider_id = ?
    ");
    $stmt->execute([$requestId, $companyId]);
    $requestRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$requestRow) {
        echo json_encode(['success' => false, 'message' => 'Request not found or unauthorized']);
        exit;
    }

    if ($requestRow['status'] !== 'pending') {
         echo json_encode(['success' => false, 'message' => 'Request is no longer pending']);
         exit;
    }

    // Update status
    $newStatus = ($action === 'accept') ? 'accepted' : 'rejected';

    $updateStmt = $pdo->prepare("
        UPDATE directjobrequest
        SET status = ?
        WHERE request_id = ?
    ");
    $updateStmt->execute([$newStatus, $requestId]);

    echo json_encode([
        'success' => true, 
        'message' => 'Direct request successfully ' . $newStatus,
        'status' => $newStatus
    ]);
    exit;

} catch (PDOException $e) {
    error_log('company-direct-requests-action error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
