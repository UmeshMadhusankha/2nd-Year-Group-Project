<?php
// API endpoint for contract operations
// Routes requests to ContractController

error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

error_log("[API] Contract API called");
error_log("[API] REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("[API] REQUEST_URI: " . $_SERVER['REQUEST_URI']);

session_start();

// Support JSON POST bodies (frontend uses fetch + application/json)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        if (is_array($json)) {
            // Merge into $_POST so downstream controller methods can fall back to it
            $_POST = array_merge($_POST, $json);
        }
    }
}

error_log("[API] Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("[API] Session user_role: " . ($_SESSION['user_role'] ?? 'NOT SET'));

require_once __DIR__ . '/../controllers/ContractController.php';

header('Content-Type: application/json');

// Get the action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log("[API] Action: " . $action);

try {
    $controller = new ContractController();
} catch (Exception $e) {
    error_log("[API ERROR] Failed to create controller: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}

switch ($action) {
    case 'list':
    case 'getAll':
        $role = $_SESSION['user_role'] ?? null;
        if ($role === 'company') {
            $controller->getAllContracts();
        } else {
            $controller->getCustomerContracts();
        }
        break;
    
    case 'get':
        $role = $_SESSION['user_role'] ?? null;
        if ($role === 'company') {
            $controller->getContract();
        } else {
            $controller->getCustomerContract();
        }
        break;
    
    case 'stats':
        $controller->getStats();
        break;
    
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->createContract();
        break;
    
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->updateContract();
        break;
    
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->deleteContract();
        break;

    case 'cancel_contract':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->cancelContract();
        break;
    
    case 'filterByStatus':
        $controller->filterByStatus();
        break;
    
    case 'getAcceptedProjects':
        $controller->getAcceptedProjects();
        break;
    
    // PHASE 2: Quotation-based contract creation
    case 'getAcceptedQuotations':
        $controller->getAcceptedQuotations();
        break;
    
    case 'autoCreateContracts':
        $controller->autoCreateContracts();
        break;
    
    case 'quotationSummary':
        $controller->getQuotationSummary();
        break;
    
    // PHASE 2A: Contract with milestones
    case 'createContractWithMilestones':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->createContractWithMilestones();
        break;
    
    case 'getContractWithMilestones':
        $controller->getContractWithMilestones();
        break;
    
    case 'sendToCustomer':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->sendToCustomer();
        break;

    case 'undo_send_to_customer':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->undoSendToCustomer();
        break;
    
    case 'downloadPDF':
        $controller->downloadContractPDF();
        break;
        
    case 'respond':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->respondToContract();
        break;

    case 'undo_customer_response':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->undoCustomerResponse();
        break;

    case 'pay_and_accept':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->payAndAccept();
        break;
    
    // ========================================
    // PHASE 2: UNDO WINDOW (24-hour cancellation)
    // ========================================
    case 'check_undo_window':
        $controller->checkUndoWindow();
        break;
    
    case 'request_undo':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->requestUndo();
        break;
    
    // ========================================
    // PHASE 2: NOTIFICATIONS SYSTEM
    // ========================================
    case 'get_notifications':
        $controller->getNotifications();
        break;
    
    case 'mark_notification_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markNotificationRead();
        break;
    
    case 'mark_all_notifications_read':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markAllNotificationsRead();
        break;
    
    // ========================================
    // PHASE 2: MILESTONE WORKFLOW
    // ========================================
    case 'submit_milestone':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->submitMilestone();
        break;
    
    case 'approve_milestone':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->approveMilestone();
        break;
    
    case 'reject_milestone':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->rejectMilestone();
        break;
    
    case 'mark_work_started':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markWorkStarted();
        break;
    
    case 'mark_work_completed':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markWorkCompleted();
        break;
    
    // ========================================
    // PHASE 2: CONTRACT TIMELINE
    // ========================================
    case 'get_timeline':
        $controller->getTimeline();
        break;
    
    // ========================================
    // PHASE 2: ESCROW MANAGEMENT
    // ========================================
    case 'get_escrow_status':
        $controller->getEscrowStatus();
        break;
    
    case 'approve_escrow_release':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->approveEscrowRelease();
        break;
    
    case 'deny_escrow_release':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->denyEscrowRelease();
        break;
    
    // ========================================
    // PHASE 2: INVOICE MANAGEMENT
    // ========================================
    case 'get_invoices':
        $controller->getInvoices();
        break;
    
    case 'get_invoice_details':
        $controller->getInvoiceDetails();
        break;
    
    case 'download_invoice_pdf':
        $controller->downloadInvoicePDF();
        break;
    
    case 'mark_invoice_paid':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->markInvoicePaid();
        break;
    
    // ========================================
    // PHASE 2: BUDGET ADJUSTMENT
    // ========================================
    case 'get_budget_adjustments':
        $controller->getBudgetAdjustments();
        break;
    
    case 'submit_budget_adjustment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->submitBudgetAdjustment();
        break;
    
    case 'approve_budget_adjustment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->approveBudgetAdjustment();
        break;
    
    case 'reject_budget_adjustment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->rejectBudgetAdjustment();
        break;
    
    // ========================================
    // PHASE 2: TIME & MATERIAL TRACKING
    // ========================================
    case 'get_time_entries':
        $contract_id = $_GET['contract_id'] ?? null;
        $controller->getTimeEntries($contract_id);
        break;
    
    case 'submit_time_entry':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $contract_id = $_POST['contract_id'] ?? null;
        $data = $_POST;
        $controller->submitTimeEntry($contract_id, $data);
        break;
    
    case 'approve_time_entry':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $entry_id = $_POST['entry_id'] ?? null;
        $contract_id = $_POST['contract_id'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;
        $controller->approveTimeEntry($entry_id, $contract_id, $user_id);
        break;
    
    case 'reject_time_entry':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $entry_id = $_POST['entry_id'] ?? null;
        $contract_id = $_POST['contract_id'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;
        $reason = $_POST['reason'] ?? null;
        $controller->rejectTimeEntry($entry_id, $contract_id, $user_id, $reason);
        break;

    // ========================================
    // CONTRACT CHANGE REQUESTS
    // ========================================
    case 'list_contract_changes':
        $controller->listContractChangeRequests();
        break;

    case 'get_contract_change_preview':
        $controller->getContractChangePreview();
        break;

    case 'request_contract_change':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->requestContractChange();
        break;

    case 'respond_contract_change':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->respondContractChange();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        exit;
}
