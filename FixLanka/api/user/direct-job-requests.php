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

$userId = (int)($user['id'] ?? 0);
$categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$providerId = isset($_POST['provider_id']) ? (int)$_POST['provider_id'] : 0;
$providerTypeRaw = strtolower(trim((string)($_POST['provider_type'] ?? '')));
$title = trim((string)($_POST['title'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$district = trim((string)($_POST['district'] ?? ''));
$address = trim((string)($_POST['address'] ?? ''));
$finishDate = trim((string)($_POST['finish_date'] ?? ''));

$providerType = null;
if ($providerTypeRaw === 'individual' || $providerTypeRaw === 'repairer') {
    $providerType = 'individual';
} elseif ($providerTypeRaw === 'company') {
    $providerType = 'company';
}

if ($userId <= 0 || $categoryId <= 0 || $providerId <= 0 || $providerType === null || $title === '' || $description === '' || $district === '' || $address === '' || $finishDate === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $finishDate)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid finish date format'
    ]);
    exit;
}

if ($finishDate < date('Y-m-d')) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Finish date cannot be in the past'
    ]);
    exit;
}

$photoPath = null;
if (isset($_FILES['photos']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/direct_job_photos/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $allowedExtensions, true)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid image type. Allowed: jpg, jpeg, png, gif, webp'
        ]);
        exit;
    }

    $newFileName = uniqid('direct_job_', true) . '.' . $extension;
    $target = $uploadDir . $newFileName;

    if (!move_uploaded_file($_FILES['photos']['tmp_name'], $target)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload image'
        ]);
        exit;
    }

    $photoPath = 'uploads/direct_job_photos/' . $newFileName;
}

try {
    $pdo = getDatabaseConnection();

    // Validate category exists.
    $categoryCheck = $pdo->prepare('SELECT category_id FROM Category WHERE category_id = ? LIMIT 1');
    $categoryCheck->execute([$categoryId]);
    if (!$categoryCheck->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid category'
        ]);
        exit;
    }

    // Validate provider exists according to provider type.
    if ($providerType === 'individual') {
        $providerCheck = $pdo->prepare('SELECT repairer_id FROM Repairer WHERE repairer_id = ? LIMIT 1');
        $providerCheck->execute([$providerId]);
    } else {
        $providerCheck = $pdo->prepare('SELECT company_id FROM Company WHERE company_id = ? LIMIT 1');
        $providerCheck->execute([$providerId]);
    }

    if (!$providerCheck->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid provider'
        ]);
        exit;
    }

    $insertSql = '
        INSERT INTO directjobrequest
            (user_id, category_id, provider_id, provider_type, title, description, status, district, address, finish_date, photos)
        VALUES
            (:user_id, :category_id, :provider_id, :provider_type, :title, :description, :status, :district, :address, :finish_date, :photos)
    ';

    $stmt = $pdo->prepare($insertSql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':provider_id', $providerId, PDO::PARAM_INT);
    $stmt->bindValue(':provider_type', $providerType, PDO::PARAM_STR);
    $stmt->bindValue(':title', $title, PDO::PARAM_STR);
    $stmt->bindValue(':description', $description, PDO::PARAM_STR);
    $stmt->bindValue(':status', 'pending', PDO::PARAM_STR);
    $stmt->bindValue(':district', $district, PDO::PARAM_STR);
    $stmt->bindValue(':address', $address, PDO::PARAM_STR);
    $stmt->bindValue(':finish_date', $finishDate, PDO::PARAM_STR);
    $stmt->bindValue(':photos', $photoPath, $photoPath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Direct job request created successfully',
        'request_id' => (int)$pdo->lastInsertId()
    ]);
} catch (Throwable $e) {
    error_log('direct-job-requests API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create direct job request'
    ]);
}

exit;
