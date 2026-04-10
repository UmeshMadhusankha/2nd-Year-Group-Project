<?php
// Local-only debug helper: shows current session user/company and ad counts.
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo 'Forbidden';
    exit;
}

require __DIR__ . '/../config/session.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../api/helpers.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['user_role'] ?? null;
$sessionCompanyId = $_SESSION['company_id'] ?? null;

$result = [
    'ok' => true,
    'remote' => $remote,
    'session' => [
        'user_id' => $userId,
        'role' => $role,
        'company_id' => $sessionCompanyId,
    ],
    'resolved_company' => null,
    'ads' => [
        'provider_type_company_count' => 0,
        'provider_type_company_latest' => [],
    ],
];

if (!$userId) {
    $result['ok'] = false;
    $result['error'] = 'Not logged in (no session user_id). Open this after logging in as a company.';
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}

try {
    $companyData = getCompanyByUserId($pdo, (int)$userId);
    $companyId = $sessionCompanyId ?: ($companyData['company_id'] ?? null);

    $result['resolved_company'] = [
        'companyData' => $companyData,
        'effective_company_id' => $companyId,
    ];

    if ($companyId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM advertisement WHERE provider_type='company' AND provider_id = :id");
        $stmt->execute([':id' => (int)$companyId]);
        $result['ads']['provider_type_company_count'] = (int)$stmt->fetchColumn();

        $latest = $pdo->prepare("SELECT ad_id, title, status, submission_date, image_url FROM advertisement WHERE provider_type='company' AND provider_id = :id ORDER BY ad_id DESC LIMIT 5");
        $latest->execute([':id' => (int)$companyId]);
        $result['ads']['provider_type_company_latest'] = $latest->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($result, JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT);
}
