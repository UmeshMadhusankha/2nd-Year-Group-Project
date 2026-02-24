<?php
/**
 * API: repairer-jobs.php
 * Handles a repairer's own customer jobs (accepted repairerquotes → jobrequest).
 *
 * Actions (GET):
 *   ?action=list&repairer_id=X               – all jobs for this repairer
 *   ?action=stats&repairer_id=X              – header-stat counts
 *
 * Actions (POST):
 *   action=update-status  body: {quote_id, status}  – update jobrequest status
 */

require_once __DIR__ . '/../config/database.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// $pdo is created by config/database.php (global variable)
if (!isset($pdo)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($method === 'POST' ? (json_decode(file_get_contents('php://input'), true)['action'] ?? '') : '');

if ($method === 'GET') {
    switch ($action) {
        case 'list':
            getJobList($pdo);
            break;
        case 'stats':
            getStats($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $postAction = $data['action'] ?? $_POST['action'] ?? '';

    switch ($postAction) {
        case 'update-status':
            updateJobStatus($pdo, $data);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

/* ─── GET: list ─────────────────────────────────────────────────────────── */
function getJobList($pdo) {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    if ($repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'repairer_id required']);
        return;
    }

    $statusFilter = $_GET['status'] ?? '';   // all | active | completed | paid | cancelled
    $search       = trim($_GET['search'] ?? '');

    try {
        $sql = "
            SELECT
                rq.quote_id,
                rq.request_id,
                rq.quoteAmount,
                rq.estimatedDays,
                rq.dateSubmitted,
                rq.status         AS quote_status,

                jr.title          AS job_title,
                jr.description    AS job_description,
                jr.status         AS job_status,
                jr.district,
                jr.address,
                jr.urgency,
                jr.finish_date,
                jr.dateCreated    AS job_posted_date,
                jr.category_id,

                c.name            AS category_name,

                u.user_id         AS customer_id,
                u.f_name          AS customer_first_name,
                u.l_name          AS customer_last_name,
                u.email           AS customer_email,

                p.payment_id,
                p.status          AS payment_status,
                p.paymentDate,
                p.amount          AS payment_amount
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            LEFT JOIN category c    ON jr.category_id  = c.category_id
            LEFT JOIN user u        ON jr.user_id       = u.user_id
            LEFT JOIN payment p     ON p.job_request_id = jr.request_id
                                    AND p.status = 'completed'
            WHERE rq.repairer_id = ?
              AND rq.status = 'accepted'
        ";
        $params = [$repairerId];

        // Status filter maps UI tabs → DB values
        if ($statusFilter && $statusFilter !== 'all') {
            switch ($statusFilter) {
                case 'active':
                    $sql .= " AND jr.status IN ('accepted','in_progress')";
                    break;
                case 'completed':
                    $sql .= " AND jr.status = 'completed' AND p.payment_id IS NULL";
                    break;
                case 'paid':
                    $sql .= " AND jr.status = 'completed' AND p.payment_id IS NOT NULL";
                    break;
                case 'cancelled':
                    $sql .= " AND jr.status = 'cancelled'";
                    break;
            }
        }

        if ($search !== '') {
            $sql .= " AND (jr.title LIKE ? OR jr.district LIKE ? OR u.f_name LIKE ? OR u.l_name LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }

        $sql .= " ORDER BY rq.dateSubmitted DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Derive a single unified status for the UI
        foreach ($jobs as &$job) {
            $job['ui_status'] = deriveUiStatus($job);
        }
        unset($job);

        echo json_encode(['success' => true, 'jobs' => $jobs, 'total' => count($jobs)]);
    } catch (PDOException $e) {
        error_log("repairer-jobs list error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch jobs']);
    }
}

/* ─── GET: stats ────────────────────────────────────────────────────────── */
function getStats($pdo) {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    if ($repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'repairer_id required']);
        return;
    }

    try {
        $sql = "
            SELECT
                jr.status AS job_status,
                p.status  AS payment_status
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            LEFT JOIN payment p     ON p.job_request_id = jr.request_id
                                    AND p.status = 'completed'
            WHERE rq.repairer_id = ?
              AND rq.status = 'accepted'
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$repairerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $active    = 0;
        $completed = 0;
        $paid      = 0;
        $cancelled = 0;

        foreach ($rows as $row) {
            $ui = deriveUiStatus($row);
            switch ($ui) {
                case 'active':    $active++;    break;
                case 'completed': $completed++; break;
                case 'paid':      $paid++;      break;
                case 'cancelled': $cancelled++; break;
            }
        }

        echo json_encode([
            'success'   => true,
            'active'    => $active,
            'completed' => $completed,
            'paid'      => $paid,
            'cancelled' => $cancelled,
            'total'     => $active + $completed + $paid + $cancelled,
        ]);
    } catch (PDOException $e) {
        error_log("repairer-jobs stats error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch stats']);
    }
}

/* ─── POST: update-status ───────────────────────────────────────────────── */
function updateJobStatus($pdo, $data) {
    $requestId = intval($data['request_id'] ?? 0);
    $newStatus = $data['status'] ?? '';
    $repairerId = intval($data['repairer_id'] ?? 0);

    $allowed = ['in_progress', 'completed', 'cancelled'];
    if ($requestId <= 0 || !in_array($newStatus, $allowed)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request_id or status']);
        return;
    }

    try {
        // Verify ownership
        $check = $pdo->prepare(
            "SELECT rq.quote_id FROM repairerquote rq
             WHERE rq.request_id = ? AND rq.repairer_id = ? AND rq.status = 'accepted'
             LIMIT 1"
        );
        $check->execute([$requestId, $repairerId]);
        if (!$check->fetch()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorised or quote not found']);
            return;
        }

        $stmt = $pdo->prepare("UPDATE jobrequest SET status = ? WHERE request_id = ?");
        $stmt->execute([$newStatus, $requestId]);

        echo json_encode(['success' => true, 'message' => 'Status updated']);
    } catch (PDOException $e) {
        error_log("repairer-jobs update-status error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

/* ─── Helper ─────────────────────────────────────────────────────────────── */
function deriveUiStatus($row) {
    $jobStatus     = $row['job_status'] ?? '';
    $paymentStatus = $row['payment_status'] ?? null;

    if ($jobStatus === 'cancelled') return 'cancelled';
    if ($jobStatus === 'completed' && $paymentStatus === 'completed') return 'paid';
    if ($jobStatus === 'completed') return 'completed';
    return 'active';
}
