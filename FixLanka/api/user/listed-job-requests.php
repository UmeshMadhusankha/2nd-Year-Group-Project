<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated'
    ]);
    exit;
}

$user = getUserData();
$providerTypeRaw = strtolower(trim((string)($_GET['provider_type'] ?? '')));

$providerType = null;
if ($providerTypeRaw === 'individual' || $providerTypeRaw === 'repairer') {
    $providerType = 'individual';
} elseif ($providerTypeRaw === 'company') {
    $providerType = 'company';
}

if ($providerType === null) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid provider_type'
    ]);
    exit;
}

try {
    $pdo = getDatabaseConnection();

    $sql = "
        SELECT
            jr.request_id,
            jr.title,
            jr.description,
            jr.district,
            jr.address,
            jr.urgency,
            jr.finish_date,
            jr.dateCreated,
            jr.service_provider_type,
            c.name AS category_name
        FROM JobRequest jr
        LEFT JOIN Category c ON c.category_id = jr.category_id
        WHERE jr.user_id = :user_id
          AND jr.status = 'pending'
          AND jr.service_provider_type = :provider_type
        ORDER BY jr.dateCreated DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', (int)$user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':provider_type', $providerType, PDO::PARAM_STR);
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $rows,
        'count' => count($rows)
    ]);
} catch (Throwable $e) {
    error_log('listed-job-requests API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load pending listed jobs'
    ]);
}

exit;
