<?php
/**
 * Customer Quotations API
 * 
 * Handles customer interactions with quotations:
 * 1. Fetching quotations for a specific job request
 * 2. Accepting a quotation (which triggers contract creation)
 */

require_once '../config/database.php';
require_once '../controllers/ContractController.php';
require_once '../controllers/CompanyQuotationController.php';

header('Content-Type: application/json');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Router
switch ($action) {
    case 'get_by_request':
        if ($method !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        handleGetQuotations();
        break;

    case 'accept':
        if ($method !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        handleAcceptQuotation();
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleGetQuotations() {
    global $pdo;
    
    $requestId = $_GET['request_id'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$requestId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID is required']);
        return;
    }

    // Verify user owns the job request
    $stmt = $pdo->prepare("SELECT user_id FROM jobrequest WHERE request_id = ?");
    $stmt->execute([$requestId]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Job request not found']);
        return;
    }

    if ($job['user_id'] != $userId) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized to view these quotations']);
        return;
    }

    // Fetch quotations
    // We join with Company table (if it exists) or User table to get provider name
    // Assuming 'company' table or 'user' table for provider details
    $sql = "
        SELECT 
            q.quotation_id,
            q.title,
            q.description,
            q.total_amount,
            q.labor_cost,
            q.material_cost,
            q.transport_cost,
            q.other_charges,
            q.labor_unit_label,
            q.material_unit_label,
            q.estimated_duration,
            q.start_date,
            q.status,
            q.created_at,
            COALESCE(u.company_name, u.f_name || ' ' || u.l_name, 'Service Provider') as provider_name,
            u.user_id as provider_id
        FROM companyquotation q
        LEFT JOIN user u ON (q.company_id = u.user_id OR q.user_id = u.user_id) 
        WHERE q.request_id = ?
        ORDER BY q.created_at DESC
    ";

    // Note: The join condition might need adjustment based on exact schema. 
    // Assuming company_id in quotation refers to user_id of a company type user,
    // or separate company table. Based on previous files, 'company_id' exists in quotation.
    
    // Let's rely on standard fetch.
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$requestId]);
    $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $quotations]);
}

function handleAcceptQuotation() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $quotationId = $input['quotation_id'] ?? null;
    $userId = $_SESSION['user_id'];

    if (!$quotationId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
        return;
    }

    // Initialize controller
    $contractController = new ContractController();
    
    // Call accept logic
    // We pass the IO to the controller or handle simple validation here
    // Better to put business logic in Controller
    
    // Validating ownership happens inside controller for security consistency?
    // Or we do it here. Let's do it in Controller as planned.
    
    // But ContractController::acceptQuotation doesn't exist yet by name, 
    // we need to add it or use a public method.
    // I will add `acceptQuotation` to ContractController.
    
    // We can simulate the controller call.
    // We need to capture output since controller might echo json. 
    // Actually, following the pattern of other APIs:
    $contractController->acceptQuotation($quotationId, $userId);
}
