<?php
/**
 * Milestone Management API
 * 
 * Actions:
 * - mark_completed (Company)
 * - approve (Customer)
 * - reject (Customer)
 */

require_once '../config/database.php';
require_once '../controllers/ContractController.php';
require_once '../models/ContractModel.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

/*
 * GET: Retrieve milestone proof details for customer review
 */
if ($method === 'GET') {
    if ($action !== 'get') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

    $milestoneId = isset($_GET['milestone_id']) ? (int)$_GET['milestone_id'] : 0;
    if (!$milestoneId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'milestone_id required']);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT
            m.milestone_id,
            m.title,
            m.description,
            m.status,
            m.due_date,
            m.amount,
            m.actual_amount,
            m.proof_of_work,
            m.proof_files,
            m.is_non_paying,
            m.actual_labor_quantity,
            m.actual_material_quantity,
            m.actual_material_unit_rate,
            m.actual_extra_amount,
            m.completed_at,
            m.milestone_number,
            c.customer_id,
            q.labor_unit_label,
            q.material_unit_label,
            q.labor_cost     AS agreed_labor_rate,
            (COALESCE(q.material_cost,0) + COALESCE(q.transport_cost,0) + COALESCE(q.other_charges,0)) AS agreed_material_rate
        FROM contract_milestone m
        JOIN contract c ON c.contract_id = m.contract_id
        LEFT JOIN companyquotation q ON q.quotation_id = c.quotation_id
        WHERE m.milestone_id = ?
        LIMIT 1
    ");
    $stmt->execute([$milestoneId]);
    $milestone = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$milestone) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Milestone not found']);
        exit;
    }

    if ($userId != $milestone['customer_id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Decode proof files JSON
    $proofFiles = [];
    if (!empty($milestone['proof_files'])) {
        $decoded = json_decode($milestone['proof_files'], true);
        if (is_array($decoded)) {
            $proofFiles = $decoded;
        }
    }
    $milestone['proof_files_array'] = $proofFiles;

    echo json_encode(['success' => true, 'data' => $milestone]);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $milestoneId = $input['milestone_id'] ?? null;
    
    if (!$milestoneId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Milestone ID required']);
        exit;
    }

    $contractModel = new ContractModel($pdo);

    // Get milestone details to check ownership
    $stmt = $pdo->prepare("
        SELECT m.*, c.customer_id, c.company_id, c.status as contract_status 
        FROM contract_milestone m
        JOIN contract c ON m.contract_id = c.contract_id
        WHERE m.milestone_id = ?
    ");
    $stmt->execute([$milestoneId]);
    $milestone = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$milestone) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Milestone not found']);
        exit;
    }

    /*
     * ACTION: MARK COMPLETED
     * Role: Company
     */
    if ($action === 'mark_completed') {
        if ($userId != $milestone['company_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized: Only the assigned company can mark milestones as completed.']);
            exit;
        }

        $proof = $input['proof_files'] ?? null;
        $comments = $input['comments'] ?? null;

        if ($contractModel->markMilestoneCompleted($milestoneId, $proof, $comments)) {
            echo json_encode(['success' => true, 'message' => 'Milestone marked as completed.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update milestone.']);
        }
    }

    /*
     * ACTION: APPROVE
     * Role: Customer
     */
    elseif ($action === 'approve') {
        if ($userId != $milestone['customer_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized: Only the customer can approve milestones.']);
            exit;
        }

        if ($contractModel->approveMilestone($milestoneId)) {
            echo json_encode(['success' => true, 'message' => 'Milestone approved.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to approve milestone.']);
        }
    }

    /*
     * ACTION: REJECT
     * Role: Customer
     */
    elseif ($action === 'reject') {
        if ($userId != $milestone['customer_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized: Only the customer can reject milestones.']);
            exit;
        }

        $reason = $input['reason'] ?? 'No reason provided';

        if ($contractModel->rejectMilestone($milestoneId, $reason)) {
            echo json_encode(['success' => true, 'message' => 'Milestone rejected.']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to reject milestone.']);
        }
    }

    else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
