<?php
/**
 * API: repairer-company-earnings.php
 * Handles company assignments for repairer earnings.
 *
 * Actions (GET):
 *   ?action=list&repairer_id=X        - list company assignments for repairer
 *   ?action=invoice&assignment_id=X   - invoice data for assignment
 *
 * Actions (POST):
 *   action=send-reminder body: {assignment_id} - notify company
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

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($method === 'POST' ? (json_decode(file_get_contents('php://input'), true)['action'] ?? '') : '');

if ($method === 'GET') {
    switch ($action) {
        case 'list':
            requireRole('repairer');
            listCompanyAssignments($pdo);
            break;
        case 'invoice':
            requireRole('repairer');
            getInvoice($pdo);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $postAction = $data['action'] ?? $_POST['action'] ?? '';

    switch ($postAction) {
        case 'send-reminder':
            requireRole('repairer');
            sendReminder($pdo, $data);
            break;
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);

function listCompanyAssignments(PDO $pdo): void {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    $userData = getUserData();
    $sessionRepairerId = intval($userData['id'] ?? 0);

    if ($sessionRepairerId) {
        $repairerId = $sessionRepairerId;
    }

    if ($repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'repairer_id required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                ra.assignment_id,
                ra.project_id,
                ra.repairer_id,
                ra.role,
                ra.amount,
                ra.assigned_date,
                ra.status,
                p.title AS project_title,
                p.description AS project_description,
                p.location AS project_location,
                p.start_date,
                p.end_date,
                p.status AS project_status,
                c.company_id,
                c.name AS company_name,
                co.contract_id,
                co.payment_method,
                co.pricing_type,
                co.hourly_rate AS contract_hourly_rate,
                co.materials_responsibility,
                logs.total_hours AS hours_worked,
                COALESCE(co.hourly_rate, logs.avg_hourly_rate) AS hourly_rate
            FROM repairerassignment ra
            INNER JOIN project p ON ra.project_id = p.project_id
            INNER JOIN company c ON p.company_id = c.company_id
            LEFT JOIN contract co ON co.project_id = p.project_id
            LEFT JOIN (
                SELECT
                    contract_id,
                    SUM(total_hours) AS total_hours,
                    AVG(hourly_rate) AS avg_hourly_rate
                FROM contract_time_logs
                GROUP BY contract_id
            ) logs ON logs.contract_id = co.contract_id
            WHERE ra.repairer_id = ?
            ORDER BY ra.assigned_date DESC
        ");
        $stmt->execute([$repairerId]);
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($assignments as &$assignment) {
            $assignment['ui_status'] = $assignment['status'] === 'completed' ? 'paid' : 'pending';
        }
        unset($assignment);

        echo json_encode(['success' => true, 'assignments' => $assignments, 'total' => count($assignments)]);
    } catch (PDOException $e) {
        error_log('repairer-company-earnings list error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch company earnings']);
    }
}

function getInvoice(PDO $pdo): void {
    $assignmentId = intval($_GET['assignment_id'] ?? 0);
    $repairerId = resolveRepairerId();

    if ($assignmentId <= 0 || $repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'assignment_id required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                ra.assignment_id,
                ra.amount,
                ra.assigned_date,
                ra.status,
                p.title AS project_title,
                p.description AS project_description,
                p.location AS project_location,
                p.start_date,
                p.end_date,
                p.status AS project_status,
                c.company_id,
                c.name AS company_name,
                c.email AS company_email
            FROM repairerassignment ra
            INNER JOIN project p ON ra.project_id = p.project_id
            INNER JOIN company c ON p.company_id = c.company_id
            WHERE ra.assignment_id = ?
              AND ra.repairer_id = ?
            LIMIT 1
        ");
        $stmt->execute([$assignmentId, $repairerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Invoice not found']);
            return;
        }

        $amount = (float) ($row['amount'] ?? 0);
        $invoiceNumber = 'INV-ASSIGN-' . $row['assignment_id'];

        echo json_encode([
            'success' => true,
            'invoice' => [
                'invoice_number' => $invoiceNumber,
                'issued_date' => $row['assigned_date'] ?: $row['start_date'],
                'status' => $row['status'] === 'completed' ? 'paid' : 'pending',
                'job_title' => $row['project_title'],
                'job_description' => $row['project_description'],
                'district' => $row['project_location'],
                'address' => $row['project_location'],
                'customer_name' => $row['company_name'],
                'customer_email' => $row['company_email'],
                'amount' => $amount,
                'platform_fee' => 0,
                'total' => $amount
            ]
        ]);
    } catch (PDOException $e) {
        error_log('repairer-company-earnings invoice error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch invoice']);
    }
}

function sendReminder(PDO $pdo, array $data): void {
    $assignmentId = intval($data['assignment_id'] ?? 0);
    $repairerId = resolveRepairerId();

    if ($assignmentId <= 0 || $repairerId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'assignment_id required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            SELECT
                ra.status,
                ra.amount,
                p.title AS project_title,
                c.company_id,
                c.name AS company_name
            FROM repairerassignment ra
            INNER JOIN project p ON ra.project_id = p.project_id
            INNER JOIN company c ON p.company_id = c.company_id
            WHERE ra.assignment_id = ?
              AND ra.repairer_id = ?
            LIMIT 1
        ");
        $stmt->execute([$assignmentId, $repairerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Assignment not found']);
            return;
        }

        if ($row['status'] === 'completed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Assignment already completed']);
            return;
        }

        $amount = number_format((float) ($row['amount'] ?? 0), 2);
        $title = 'Payment reminder for ' . ($row['project_title'] ?? 'assignment');
        $message = 'Hi ' . ($row['company_name'] ?? 'Company') . ', your payment of LKR ' . $amount . ' is pending for "' . ($row['project_title'] ?? 'the assignment') . '".';

        $userData = getUserData();
        $senderName = $userData['name'] ?? 'Repairer';

        $insert = $pdo->prepare("
            INSERT INTO notification
                (title, message, recipient_type, status, created_by_id, created_by_role, created_by_name, recipient_id)
            VALUES
                (?, ?, 'company', 'sent', ?, 'repairer', ?, ?)
        ");
        $insert->execute([$title, $message, $repairerId, $senderName, $row['company_id']]);

        echo json_encode(['success' => true, 'message' => 'Reminder sent']);
    } catch (PDOException $e) {
        error_log('repairer-company-earnings reminder error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to send reminder']);
    }
}

function resolveRepairerId(): int {
    $userData = getUserData();
    return intval($userData['id'] ?? 0);
}
