<?php
/**
 * API: repairer-jobs.php
 * Handles a repairer's own customer jobs (accepted repairerquotes → jobrequest).
 *
 * Actions (GET):
 *   ?action=list&repairer_id=X               – all jobs for this repairer
 *   ?action=stats&repairer_id=X              – header-stat counts
 *   ?action=invoice&request_id=X             – invoice data for a job
 *
 * Actions (POST):
 *   action=update-status  body: {quote_id, status}  – update jobrequest status
 *   action=send-reminder  body: {request_id}        – notify customer
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
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
        case 'invoice':
            requireRole('repairer');
            getInvoice($pdo);
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
        case 'send-reminder':
            requireRole('repairer');
            sendReminder($pdo, $data);
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
    requireRole('repairer');
    $repairerId = resolveRepairerId();
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
                rq.materialsIncluded,
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
                p.paymentType,
                p.status          AS payment_status,
                p.paymentDate,
                                p.amount          AS payment_amount,

                                jc.collaboration_id,
                                jc.current_phase,
                                jc.user_completed_at,
                                jc.provider_completed_at,
                                jc.user_payment_confirmed_at,
                                jc.provider_payment_confirmed_at
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            LEFT JOIN category c    ON jr.category_id  = c.category_id
            LEFT JOIN user u        ON jr.user_id       = u.user_id
            LEFT JOIN payment p     ON p.job_request_id = jr.request_id
                                    AND p.status = 'completed'
                        LEFT JOIN job_collaboration jc
                                                                     ON jc.request_id = jr.request_id
                                                                    AND jc.request_type = 'regular'
                                                                    AND jc.provider_id = rq.repairer_id
                                                                    AND jc.provider_role = 'repairer'
            WHERE rq.repairer_id = ?
                            AND rq.status IN ('accepted', 'completed')
        ";
        $params = [$repairerId];

        // Status filter maps UI tabs → DB values
        if ($statusFilter && $statusFilter !== 'all') {
            switch ($statusFilter) {
                case 'active':
                    $sql .= " AND jr.status IN ('accepted','in_progress')";
                    break;
                case 'completed':
                    $sql .= " AND jr.status = 'completed'";
                    break;
                case 'paid':
                    $sql .= " AND jr.status = 'completed' AND p.payment_id IS NOT NULL AND jc.collaboration_id IS NULL";
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
    requireRole('repairer');
    $repairerId = resolveRepairerId();
    if ($repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'repairer_id required']);
        return;
    }

    try {
        $sql = "
            SELECT
                jr.status AS job_status,
                                p.status  AS payment_status,
                                jc.user_completed_at,
                                jc.provider_completed_at,
                                jc.user_payment_confirmed_at,
                                jc.provider_payment_confirmed_at
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            LEFT JOIN payment p     ON p.job_request_id = jr.request_id
                                    AND p.status = 'completed'
                        LEFT JOIN job_collaboration jc
                                                                     ON jc.request_id = jr.request_id
                                                                    AND jc.request_type = 'regular'
                                                                    AND jc.provider_id = rq.repairer_id
                                                                    AND jc.provider_role = 'repairer'
            WHERE rq.repairer_id = ?
                            AND rq.status IN ('accepted', 'completed')
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

    $allowed = ['in_progress', 'cancelled'];
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

/* ─── GET: invoice ─────────────────────────────────────────────────────── */
function getInvoice($pdo) {
    $requestId = intval($_GET['request_id'] ?? 0);
    $repairerId = resolveRepairerId();

    if ($requestId <= 0 || $repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'request_id required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                rq.quote_id,
                rq.quoteAmount,
                rq.dateSubmitted,
                rq.estimatedDays,
                jr.request_id,
                jr.title AS job_title,
                jr.description AS job_description,
                jr.district,
                jr.address,
                u.user_id,
                u.f_name AS customer_first_name,
                u.l_name AS customer_last_name,
                u.email AS customer_email,
                p.payment_id,
                p.paymentDate,
                p.paymentType,
                p.status AS payment_status,
                p.amount AS payment_amount
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            INNER JOIN user u ON jr.user_id = u.user_id
            LEFT JOIN payment p ON p.job_request_id = jr.request_id
            WHERE rq.repairer_id = ?
              AND rq.status = 'accepted'
              AND jr.request_id = ?
            LIMIT 1
        ");
        $stmt->execute([$repairerId, $requestId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Invoice not found']);
            return;
        }

        $amount = (float) ($row['payment_amount'] ?? $row['quoteAmount'] ?? 0);
        $invoiceNumber = 'INV-JR-' . $row['request_id'] . '-' . ($row['payment_id'] ?? $row['quote_id']);

        echo json_encode([
            'success' => true,
            'invoice' => [
                'invoice_number' => $invoiceNumber,
                'issued_date' => $row['paymentDate'] ?: $row['dateSubmitted'],
                'status' => $row['payment_status'] === 'completed' ? 'paid' : 'pending',
                'job_title' => $row['job_title'],
                'job_description' => $row['job_description'],
                'district' => $row['district'],
                'address' => $row['address'],
                'customer_name' => trim(($row['customer_first_name'] ?? '') . ' ' . ($row['customer_last_name'] ?? '')),
                'customer_email' => $row['customer_email'],
                'amount' => $amount,
                'platform_fee' => round($amount * 0.1, 2),
                'total' => round($amount - ($amount * 0.1), 2)
            ]
        ]);
    } catch (PDOException $e) {
        error_log('repairer-jobs invoice error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch invoice']);
    }
}

/* ─── POST: send-reminder ─────────────────────────────────────────────── */
function sendReminder($pdo, $data) {
    $requestId = intval($data['request_id'] ?? 0);
    $repairerId = resolveRepairerId();

    if ($requestId <= 0 || $repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'request_id required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                jr.title AS job_title,
                jr.user_id,
                u.f_name AS customer_first_name,
                u.l_name AS customer_last_name,
                p.status AS payment_status,
                rq.quoteAmount
            FROM repairerquote rq
            INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
            INNER JOIN user u ON jr.user_id = u.user_id
            LEFT JOIN payment p ON p.job_request_id = jr.request_id
            WHERE rq.repairer_id = ?
              AND rq.status = 'accepted'
              AND jr.request_id = ?
            LIMIT 1
        ");
        $stmt->execute([$repairerId, $requestId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Job not found']);
            return;
        }

        if ($row['payment_status'] === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Payment already completed']);
            return;
        }

        $customerName = trim(($row['customer_first_name'] ?? '') . ' ' . ($row['customer_last_name'] ?? ''));
        $amount = number_format((float) ($row['quoteAmount'] ?? 0), 2);
        $title = 'Payment reminder for ' . ($row['job_title'] ?? 'job');
        $message = 'Hi ' . ($customerName ?: 'Customer') . ', your payment of LKR ' . $amount . ' is pending for "' . ($row['job_title'] ?? 'your job') . '".';

        $userData = getUserData();
        $senderName = $userData['name'] ?? 'Repairer';

        $insert = $pdo->prepare("
            INSERT INTO notification
                (title, message, recipient_type, status, created_by_id, created_by_role, created_by_name, recipient_id)
            VALUES
                (?, ?, 'user', 'sent', ?, 'repairer', ?, ?)
        ");
        $insert->execute([$title, $message, $repairerId, $senderName, $row['user_id']]);

        echo json_encode(['success' => true, 'message' => 'Reminder sent']);
    } catch (PDOException $e) {
        error_log('repairer-jobs reminder error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send reminder']);
    }
}

function resolveRepairerId(): int {
    $userData = getUserData();
    return intval($userData['id'] ?? 0);
}

/* ─── Helper ─────────────────────────────────────────────────────────────── */
function deriveUiStatus($row) {
    $jobStatus     = $row['job_status'] ?? '';
    $paymentStatus = $row['payment_status'] ?? null;
    $hasUserCompleted = !empty($row['user_completed_at']);
    $hasProviderCompleted = !empty($row['provider_completed_at']);
    $hasUserPaid = !empty($row['user_payment_confirmed_at']);
    $hasProviderPaid = !empty($row['provider_payment_confirmed_at']);
    $hasCollaborationContext = isset($row['collaboration_id'])
        || isset($row['user_completed_at'])
        || isset($row['provider_completed_at'])
        || isset($row['user_payment_confirmed_at'])
        || isset($row['provider_payment_confirmed_at']);

    if ($jobStatus === 'cancelled') return 'cancelled';

    if ($hasCollaborationContext) {
        // For collaboration workflow, keep final jobs in the Completed tab even after
        // payment confirmations so repairers continue to see the full flow there.
        if ($hasProviderCompleted || $jobStatus === 'completed') return 'completed';
        return 'active';
    }

    if ($jobStatus === 'completed' && $paymentStatus === 'completed') return 'paid';
    if ($jobStatus === 'completed') return 'completed';
    return 'active';
}
