<?php
// Admin-only API to read system audit logs (read-only)

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? null) !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    $limit = (int)($_GET['limit'] ?? 50);
    if ($limit < 1) $limit = 1;
    if ($limit > 200) $limit = 200;

    $page = (int)($_GET['page'] ?? 1);
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    $action = trim((string)($_GET['action'] ?? ''));
    if ($action !== '') {
        $where[] = 'action = ?';
        $params[] = $action;
    }

    $actorRole = trim((string)($_GET['actor_role'] ?? ''));
    if ($actorRole !== '') {
        $where[] = 'actor_role = ?';
        $params[] = $actorRole;
    }

    $actorUserId = trim((string)($_GET['actor_user_id'] ?? ''));
    if ($actorUserId !== '' && ctype_digit($actorUserId)) {
        $where[] = 'actor_user_id = ?';
        $params[] = (int)$actorUserId;
    }

    $entityType = trim((string)($_GET['entity_type'] ?? ''));
    if ($entityType !== '') {
        $where[] = 'entity_type = ?';
        $params[] = $entityType;
    }

    $entityId = trim((string)($_GET['entity_id'] ?? ''));
    if ($entityId !== '') {
        $where[] = 'entity_id = ?';
        $params[] = $entityId;
    }

    $from = trim((string)($_GET['from'] ?? '')); // 'YYYY-MM-DD' or datetime
    if ($from !== '') {
        $where[] = 'occurred_at >= ?';
        $params[] = $from;
    }

    $to = trim((string)($_GET['to'] ?? ''));
    if ($to !== '') {
        $where[] = 'occurred_at <= ?';
        $params[] = $to;
    }

    $whereSql = '';
    if (!empty($where)) {
        $whereSql = 'WHERE ' . implode(' AND ', $where);
    }

    global $pdo;

    $countStmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM system_audit_log {$whereSql}");
    $countStmt->execute($params);
    $total = (int)($countStmt->fetchColumn() ?: 0);

    $sql = "SELECT audit_id, request_id, occurred_at, actor_user_id, actor_role, action,
                   entity_type, entity_id, http_method, endpoint, ip_address, status_code, details
            FROM system_audit_log
            {$whereSql}
            ORDER BY audit_id DESC
            LIMIT {$limit} OFFSET {$offset}";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$r) {
        if (isset($r['details']) && is_string($r['details']) && $r['details'] !== '') {
            $decoded = json_decode($r['details'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $r['details'] = $decoded;
            }
        }
    }

    echo json_encode([
        'success' => true,
        'data' => $rows,
        'meta' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => $limit > 0 ? (int)ceil($total / $limit) : 1,
        ]
    ]);

} catch (Exception $e) {
    $code = $e->getCode();
    $rawMessage = $e->getMessage();

    error_log('[audit-logs] error (' . $code . '): ' . $rawMessage);

    $safeMessage = 'Server error';
    $missingTable = false;
    $lower = strtolower($rawMessage);
    if (
        strpos($lower, 'system_audit_log') !== false &&
        (strpos($lower, 'doesn\'t exist') !== false || strpos($lower, 'base table or view not found') !== false)
    ) {
        $safeMessage = 'Audit log table is missing. Import the latest database schema (create_database.sql) or run the migration.';
        $missingTable = true;
    }

    // Missing table is a setup/migration issue; return 200 so the UI can show guidance
    // without surfacing a scary "500" in the browser console.
    http_response_code($missingTable ? 200 : 500);
    echo json_encode(['success' => false, 'message' => $safeMessage]);
}
