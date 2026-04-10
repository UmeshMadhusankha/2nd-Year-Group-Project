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
