<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

$user = getUserData();
if (($user['role'] ?? 'user') !== 'user') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON payload'
    ]);
    exit;
}

$providerTypeRaw = strtolower(trim((string)($input['provider_type'] ?? '')));
$providerType = null;
if ($providerTypeRaw === 'individual' || $providerTypeRaw === 'repairer') {
    $providerType = 'individual';
} elseif ($providerTypeRaw === 'company') {
    $providerType = 'company';
}

$requestId = isset($input['request_id']) ? (int)$input['request_id'] : 0;
$providerId = isset($input['provider_id']) ? (int)$input['provider_id'] : 0;
$userId = (int)($user['id'] ?? 0);

if ($userId <= 0 || $requestId <= 0 || $providerId <= 0 || $providerType === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data'
    ]);
    exit;
}

try {
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('Database connection is not initialized');
    }

    // Ensure the selected request belongs to the authenticated user and is still pending.
    $checkRequestSql = "
        SELECT request_id
        FROM JobRequest
        WHERE request_id = :request_id
          AND user_id = :user_id
          AND status = 'pending'
          AND service_provider_type = :provider_type
        LIMIT 1
    ";

    $checkRequestStmt = $pdo->prepare($checkRequestSql);
    $checkRequestStmt->bindValue(':request_id', $requestId, PDO::PARAM_INT);
    $checkRequestStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $checkRequestStmt->bindValue(':provider_type', $providerType, PDO::PARAM_STR);
    $checkRequestStmt->execute();

    if (!$checkRequestStmt->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Selected job request is not eligible for this provider type'
        ]);
        exit;
    }

    // Avoid duplicate entries for same user/request/provider combination.
    $duplicateSql = "
        SELECT id
        FROM directrequestquotes
        WHERE user_id = :user_id
          AND request_id = :request_id
          AND provider_id = :provider_id
          AND provider_type = :provider_type
        LIMIT 1
    ";

    $duplicateStmt = $pdo->prepare($duplicateSql);
    $duplicateStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $duplicateStmt->bindValue(':request_id', $requestId, PDO::PARAM_INT);
    $duplicateStmt->bindValue(':provider_id', $providerId, PDO::PARAM_INT);
    $duplicateStmt->bindValue(':provider_type', $providerType, PDO::PARAM_STR);
    $duplicateStmt->execute();

    $existing = $duplicateStmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        echo json_encode([
            'success' => true,
            'message' => 'Request already sent for this job and provider',
            'id' => (int)$existing['id'],
            'existing' => true
        ]);
        exit;
    }

    $insertSql = "
        INSERT INTO directrequestquotes (user_id, request_id, provider_id, provider_type)
        VALUES (:user_id, :request_id, :provider_id, :provider_type)
    ";

    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $insertStmt->bindValue(':request_id', $requestId, PDO::PARAM_INT);
    $insertStmt->bindValue(':provider_id', $providerId, PDO::PARAM_INT);
    $insertStmt->bindValue(':provider_type', $providerType, PDO::PARAM_STR);
    $insertStmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Request sent successfully',
        'id' => (int)$pdo->lastInsertId(),
        'existing' => false
    ]);
} catch (Throwable $e) {
    error_log('direct-request-quotes API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save direct request quote'
    ]);
}

exit;
