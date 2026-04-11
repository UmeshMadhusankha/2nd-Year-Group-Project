<?php
/**
 * Repairer Direct Jobs API
 *
 * Sources:
 * 1) directjobrequest      -> user posts directly to a specific repairer
 * 2) directrequestquotes   -> user forwards an existing jobrequest to a specific repairer
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$repairerId = isset($_GET['repairer_id']) ? (int)$_GET['repairer_id'] : 0;
if ($repairerId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'repairer_id is required']);
    exit;
}

$category = isset($_GET['category']) ? trim((string)$_GET['category']) : '';
$district = isset($_GET['district']) ? trim((string)$_GET['district']) : '';
$sort = isset($_GET['sort']) ? trim((string)$_GET['sort']) : 'newest';

try {
    $sql = "
        SELECT
            d.request_id,
            d.user_id,
            d.category_id,
            d.title,
            d.description,
            d.status,
            d.district,
            d.address,
            'individual' AS service_provider_type,
            'medium' AS urgency,
            d.finish_date,
            d.date_created AS dateCreated,
            d.photos,
            c.name AS category_name,
            u.f_name AS customer_first_name,
            u.l_name AS customer_last_name,
            TIMESTAMPDIFF(HOUR, d.date_created, NOW()) AS hours_ago,
            'directjobrequest' AS source_table
        FROM directjobrequest d
        LEFT JOIN category c ON d.category_id = c.category_id
        LEFT JOIN user u ON d.user_id = u.user_id
        WHERE d.provider_id = ?
          AND LOWER(d.provider_type) IN ('repairer','individual')
          AND LOWER(COALESCE(d.status, 'pending')) = 'pending'
          AND d.finish_date >= CURDATE()

        UNION ALL

        SELECT
            jr.request_id,
            jr.user_id,
            jr.category_id,
            jr.title,
            jr.description,
            jr.status,
            jr.district,
            jr.address,
            jr.service_provider_type,
            jr.urgency,
            jr.finish_date,
            jr.dateCreated,
            jr.photos,
            c.name AS category_name,
            u.f_name AS customer_first_name,
            u.l_name AS customer_last_name,
            TIMESTAMPDIFF(HOUR, jr.dateCreated, NOW()) AS hours_ago,
            'directrequestquotes' AS source_table
        FROM directrequestquotes drq
        INNER JOIN jobrequest jr ON drq.request_id = jr.request_id
        LEFT JOIN category c ON jr.category_id = c.category_id
        LEFT JOIN user u ON jr.user_id = u.user_id
        WHERE drq.provider_id = ?
          AND LOWER(drq.provider_type) IN ('repairer','individual')
          AND jr.status = 'pending'
          AND jr.finish_date >= CURDATE()
    ";

    $params = [$repairerId, $repairerId];

    if ($category !== '') {
        $sql = "SELECT * FROM (" . $sql . ") direct_jobs WHERE category_name = ?";
        $params[] = $category;
    } else {
        $sql = "SELECT * FROM (" . $sql . ") direct_jobs";
    }

    if ($district !== '') {
        $sql .= ($category !== '' ? " AND " : " WHERE ") . "district = ?";
        $params[] = $district;
    }

    switch ($sort) {
        case 'oldest':
            $sql .= " ORDER BY dateCreated ASC";
            break;
        case 'urgency':
            $sql .= " ORDER BY FIELD(urgency, 'urgent', 'medium'), dateCreated DESC";
            break;
        case 'deadline':
            $sql .= " ORDER BY finish_date ASC";
            break;
        case 'newest':
        default:
            $sql .= " ORDER BY dateCreated DESC";
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dedup = [];
    foreach ($rows as $row) {
        $reqId = (int)$row['request_id'];
        if (isset($dedup[$reqId])) {
            continue;
        }

        $hoursAgo = (int)($row['hours_ago'] ?? 0);
        if ($hoursAgo < 1) {
            $row['posted_ago'] = 'Just now';
        } elseif ($hoursAgo < 24) {
            $row['posted_ago'] = $hoursAgo . ' hour' . ($hoursAgo !== 1 ? 's' : '') . ' ago';
        } else {
            $daysAgo = (int)floor($hoursAgo / 24);
            $row['posted_ago'] = $daysAgo . ' day' . ($daysAgo !== 1 ? 's' : '') . ' ago';
        }

        $row['photos'] = !empty($row['photos']) ? explode(',', (string)$row['photos']) : [];
        $row['customer_name'] = trim(((string)($row['customer_first_name'] ?? '')) . ' ' . ((string)($row['customer_last_name'] ?? '')));

        unset($row['customer_first_name'], $row['customer_last_name'], $row['hours_ago']);
        $dedup[$reqId] = $row;
    }

    $data = array_values($dedup);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($data)
    ]);
} catch (Throwable $e) {
    error_log('repairer-direct-jobs API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load direct jobs'
    ]);
}
