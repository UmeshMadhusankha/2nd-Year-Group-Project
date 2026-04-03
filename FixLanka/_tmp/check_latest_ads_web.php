<?php
// Local-only debug helper: view latest advertisements and their stored image_url.
// Safety: deny requests from non-local addresses.
$remote = $_SERVER['REMOTE_ADDR'] ?? '';
$allowed = in_array($remote, ['127.0.0.1', '::1'], true);
if (!$allowed) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo "Forbidden";
    exit;
}

require __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $rows = $pdo->query('SELECT ad_id, provider_type, provider_id, title, submission_date, image_url FROM advertisement ORDER BY ad_id DESC LIMIT 10')
        ->fetchAll(PDO::FETCH_ASSOC);

    $uploadDir = realpath(__DIR__ . '/../uploads/advertisements');

    foreach ($rows as &$r) {
        $url = (string)($r['image_url'] ?? '');
        $r['image_url'] = $url;
        $r['image_file_exists'] = false;

        if ($url !== '' && $uploadDir) {
            $baseName = basename(parse_url($url, PHP_URL_PATH) ?? $url);
            $filePath = $uploadDir . DIRECTORY_SEPARATOR . $baseName;
            $r['image_file_exists'] = is_file($filePath);
        }
    }

    echo json_encode([
        'ok' => true,
        'remote' => $remote,
        'upload_dir' => $uploadDir ?: null,
        'rows' => $rows,
    ], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
