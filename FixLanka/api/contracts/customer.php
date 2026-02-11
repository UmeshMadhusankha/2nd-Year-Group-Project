<?php
/**
 * Customer Contract API
 * Fetches contracts for the logged-in customer (user role)
 * 
 * Actions:
 *   GET list    - Get all contracts for the customer
 *   GET get     - Get single contract with full details + milestones
 *   POST respond - Accept or reject a contract (future)
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Authentication: must be logged in as a user
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'user') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        getCustomerContracts($pdo, $userId);
        break;

    case 'get':
        getContractDetail($pdo, $userId);
        break;

    case 'respond':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        respondToContract($pdo, $userId);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

// ============================================
// LIST ALL CONTRACTS FOR CUSTOMER
// ============================================
function getCustomerContracts($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.contract_id, c.contract_number, c.project_title, c.project_location,
                c.total_budget, c.payment_method, c.start_date, c.end_date, c.contract_date,
                c.status, c.progress_percentage, c.milestone_plan,
                c.sent_to_customer, c.customer_response,
                comp.name as company_name, comp.email as company_email,
                comp.contact_no as company_phone,
                (SELECT COUNT(*) FROM contract_milestone cm WHERE cm.contract_id = c.contract_id) as total_milestones,
                (SELECT COUNT(*) FROM contract_milestone cm WHERE cm.contract_id = c.contract_id AND cm.status IN ('approved','paid')) as completed_milestones
            FROM contract c
            INNER JOIN company comp ON c.company_id = comp.company_id
            WHERE c.customer_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt->execute([$userId]);
        $contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $contracts,
            'count' => count($contracts)
        ]);

    } catch (Exception $e) {
        error_log("[CustomerContract] List error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}

// ============================================
// GET FULL CONTRACT DETAIL + MILESTONES
// ============================================
function getContractDetail($pdo, $userId) {
    try {
        $contractId = $_GET['id'] ?? null;
        if (!$contractId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }

        // Get contract with company info
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                comp.name as company_name, comp.email as company_email,
                comp.contact_no as company_phone, comp.address as company_address,
                comp.registration_no as company_registration, comp.business_type as company_type,
                u.f_name as customer_fname, u.l_name as customer_lname,
                u.email as customer_email, u.phone as customer_phone,
                u.address as customer_address
            FROM contract c
            INNER JOIN company comp ON c.company_id = comp.company_id
            INNER JOIN user u ON c.customer_id = u.user_id
            WHERE c.contract_id = ? AND c.customer_id = ?
        ");
        $stmt->execute([$contractId, $userId]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Contract not found']);
            return;
        }

        // Get milestones
        $stmt = $pdo->prepare("
            SELECT * FROM contract_milestone 
            WHERE contract_id = ? 
            ORDER BY milestone_number ASC
        ");
        $stmt->execute([$contractId]);
        $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contract['milestones'] = $milestones;

        // Parse terms_conditions JSON
        if (!empty($contract['terms_conditions'])) {
            $contract['terms_parsed'] = json_decode($contract['terms_conditions'], true);
        }

        echo json_encode([
            'success' => true,
            'data' => $contract
        ]);

    } catch (Exception $e) {
        error_log("[CustomerContract] Detail error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}

// ============================================
// RESPOND TO CONTRACT (Accept / Reject)
// ============================================
function respondToContract($pdo, $userId) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $contractId = $data['contract_id'] ?? null;
        $response = $data['response'] ?? null; // 'accepted' or 'rejected'

        if (!$contractId || !in_array($response, ['accepted', 'rejected'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Contract ID and valid response required']);
            return;
        }

        // Verify ownership
        $stmt = $pdo->prepare("SELECT contract_id, status FROM contract WHERE contract_id = ? AND customer_id = ?");
        $stmt->execute([$contractId, $userId]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Contract not found']);
            return;
        }

        $newStatus = ($response === 'accepted') ? 'active' : 'terminated';

        $stmt = $pdo->prepare("
            UPDATE contract 
            SET customer_response = ?, customer_response_at = NOW(), 
                status = ?, signed_at = IF(? = 'accepted', NOW(), signed_at)
            WHERE contract_id = ? AND customer_id = ?
        ");
        $stmt->execute([$response, $newStatus, $response, $contractId, $userId]);

        echo json_encode([
            'success' => true,
            'message' => 'Response recorded successfully'
        ]);

    } catch (Exception $e) {
        error_log("[CustomerContract] Respond error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}
