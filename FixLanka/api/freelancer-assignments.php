<?php
/**
 * Freelancer Assignments API (Job Offers)
 *
 * Table: freelancer_assignments
 * - Company sends offer to a repairer for a project
 * - Repairer can accept/decline
 *
 * Actions:
 *   POST action=assign_job (multipart/form-data)  [company]
 *   GET  action=list_for_company&company_id=X    [company]
 *   GET  action=list_for_repairer&repairer_id=Y  [repairer]
 *   POST action=accept  (JSON) {assignment_id, repairer_id} [repairer]
 *   POST action=decline (JSON) {assignment_id, repairer_id} [repairer]
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    if ($method === 'GET') {
        switch ($action) {
            case 'list_for_company':
                requireRole('company');
                listForCompany($pdo);
                break;
            case 'list_for_repairer':
                requireRole('repairer');
                listForRepairer($pdo);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        exit;
    }

    if ($method === 'POST') {
        switch ($action) {
            case 'assign_job':
                requireRole('company');
                assignJob($pdo);
                break;
            case 'accept':
                requireRole('repairer');
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                acceptOffer($pdo, $data);
                break;
            case 'decline':
                requireRole('repairer');
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
                declineOffer($pdo, $data);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function assignJob(PDO $pdo): void {
    $userData = getUserData();
    $companyId = intval($userData['id'] ?? 0);
    if (!$companyId) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Company not authenticated']);
        return;
    }

    $repairerId = intval($_POST['repairer_id'] ?? 0);
    $projectId  = isset($_POST['project_id']) && $_POST['project_id'] !== '' ? intval($_POST['project_id']) : null;
    $startDate  = $_POST['start_date'] ?? null;
    $deadline   = $_POST['deadline_date'] ?? null;
    $pricing    = $_POST['pricing_model'] ?? 'hourly';
    $rate       = isset($_POST['rate_or_price']) ? floatval($_POST['rate_or_price']) : null;
    $estHours   = isset($_POST['estimated_hours']) && $_POST['estimated_hours'] !== '' ? floatval($_POST['estimated_hours']) : null;
    $notes      = trim($_POST['notes'] ?? '');

    if (!$repairerId || !$startDate || !$deadline || !$pricing || $rate === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
        return;
    }

    if (!in_array($pricing, ['hourly', 'fixed'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid pricing_model']);
        return;
    }

    if ($pricing === 'fixed') {
        $estHours = null;
    }

    // Optional: validate project belongs to this company
    if ($projectId !== null) {
        $stmt = $pdo->prepare('SELECT project_id FROM project WHERE project_id = ? AND company_id = ? LIMIT 1');
        $stmt->execute([$projectId, $companyId]);
        if (!$stmt->fetchColumn()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid project selected']);
            return;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO freelancer_assignments
            (company_id, repairer_id, project_id, pricing_model, rate_or_price, estimated_hours, start_date, deadline_date, notes, status)
        VALUES
            (:company_id, :repairer_id, :project_id, :pricing_model, :rate_or_price, :estimated_hours, :start_date, :deadline_date, :notes, 'offered')
    ");

    $stmt->execute([
        ':company_id' => $companyId,
        ':repairer_id' => $repairerId,
        ':project_id' => $projectId,
        ':pricing_model' => $pricing,
        ':rate_or_price' => $rate,
        ':estimated_hours' => $estHours,
        ':start_date' => $startDate,
        ':deadline_date' => $deadline,
        ':notes' => $notes,
    ]);

    echo json_encode(['success' => true, 'message' => 'Job offer sent successfully', 'assignment_id' => $pdo->lastInsertId()]);
}

function listForCompany(PDO $pdo): void {
    $companyId = intval($_GET['company_id'] ?? 0);
    $userData = getUserData();
    $sessionCompanyId = intval($userData['id'] ?? 0);

    // Enforce session company id if provided
    if ($sessionCompanyId) {
        $companyId = $sessionCompanyId;
    }

    if (!$companyId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'company_id is required']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT
            fa.*, 
            r.f_name AS repairer_first_name,
            r.l_name AS repairer_last_name,
            r.profile_picture AS repairer_profile_picture,
            p.title AS project_title,
            p.status AS project_status
        FROM freelancer_assignments fa
        LEFT JOIN repairer r ON fa.repairer_id = r.repairer_id
        LEFT JOIN project p ON fa.project_id = p.project_id
        WHERE fa.company_id = :company_id
        ORDER BY fa.created_at DESC
    ");
    $stmt->execute([':company_id' => $companyId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'assignments' => $rows, 'count' => count($rows)]);
}

function listForRepairer(PDO $pdo): void {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    $userData = getUserData();
    $sessionRepairerId = intval($userData['id'] ?? 0);

    if ($sessionRepairerId) {
        $repairerId = $sessionRepairerId;
    }

    if (!$repairerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'repairer_id is required']);
        return;
    }

    $stmt = $pdo->prepare("
        SELECT
            fa.*, 
            c.name AS company_name,
            p.title AS project_title,
            p.location AS project_location,
            p.status AS project_status
        FROM freelancer_assignments fa
        JOIN company c ON fa.company_id = c.company_id
        LEFT JOIN project p ON fa.project_id = p.project_id
        WHERE fa.repairer_id = :repairer_id
        ORDER BY fa.created_at DESC
    ");
    $stmt->execute([':repairer_id' => $repairerId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'offers' => $rows, 'count' => count($rows)]);
}

function acceptOffer(PDO $pdo, array $data): void {
    $assignmentId = intval($data['assignment_id'] ?? 0);
    $repairerId   = intval($data['repairer_id'] ?? 0);
    $userData = getUserData();
    $sessionRepairerId = intval($userData['id'] ?? 0);

    if ($sessionRepairerId) {
        $repairerId = $sessionRepairerId;
    }

    if (!$assignmentId || !$repairerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'assignment_id and repairer_id are required']);
        return;
    }

    $pdo->beginTransaction();

    // Ensure offer belongs to repairer and is still offered
    $stmt = $pdo->prepare("SELECT status FROM freelancer_assignments WHERE assignment_id = ? AND repairer_id = ? FOR UPDATE");
    $stmt->execute([$assignmentId, $repairerId]);
    $status = $stmt->fetchColumn();

    if ($status !== 'offered') {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Offer not found or not available']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE freelancer_assignments SET status = 'accepted' WHERE assignment_id = ? AND repairer_id = ?");
    $stmt->execute([$assignmentId, $repairerId]);

    // Mark repairer as busy
    $stmt = $pdo->prepare("UPDATE repairer SET availability = 'busy' WHERE repairer_id = ?");
    $stmt->execute([$repairerId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Offer accepted']);
}

function declineOffer(PDO $pdo, array $data): void {
    $assignmentId = intval($data['assignment_id'] ?? 0);
    $repairerId   = intval($data['repairer_id'] ?? 0);
    $userData = getUserData();
    $sessionRepairerId = intval($userData['id'] ?? 0);

    if ($sessionRepairerId) {
        $repairerId = $sessionRepairerId;
    }

    if (!$assignmentId || !$repairerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'assignment_id and repairer_id are required']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE freelancer_assignments SET status = 'declined' WHERE assignment_id = ? AND repairer_id = ? AND status = 'offered'");
    $stmt->execute([$assignmentId, $repairerId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Offer not found or cannot be declined']);
        return;
    }

    echo json_encode(['success' => true, 'message' => 'Offer declined']);
}
