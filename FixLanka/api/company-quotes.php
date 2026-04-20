<?php
/**
 * Company Quotations API
 *
 * JSON API endpoint for managing company quotations.
 * Supports legacy and enhanced quotation flows.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/CompanyQuotationModel.php';
require_once '../includes/undo.php';

$quotationModel = new CompanyQuotation($pdo);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Company-only API: enforce session auth and role.
$sessionUserId = $_SESSION['user_id'] ?? null;
$sessionUserRole = $_SESSION['user_role'] ?? null;
if (!$sessionUserId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Authentication required']);
    exit;
}
if ($sessionUserRole !== 'company') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Company access only']);
    exit;
}

$AUTH_COMPANY_ID = (int)$sessionUserId;
$action = $_GET['action'] ?? null;

switch ($method) {
    case 'GET':
        if ($action === 'get_enhanced') {
            handleGetEnhanced();
        } else {
            handleGet();
        }
        break;

    case 'POST':
        if ($action === 'undo_submit') {
            handleUndoSubmit();
        } else if ($action === 'create_enhanced') {
            handlePostEnhanced();
        } else {
            handlePost();
        }
        break;

    case 'PUT':
        if ($action === 'update_enhanced') {
            handlePutEnhanced();
        } else {
            handlePut();
        }
        break;

    case 'DELETE':
        handleDelete();
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        break;
}

function readJsonBody()
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return null;
    }
    return json_decode($raw, true);
}

function validateRequiredFields($input)
{
    $requiredFields = [
        'request_id', 'user_id', 'title', 'labor_cost', 'material_cost',
        'total_amount', 'start_date', 'completion_date', 'estimated_duration'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Missing required field: $field"
            ]);
            return false;
        }
    }

    return true;
}

function validateCostValues($input)
{
    $laborCost = floatval($input['labor_cost'] ?? 0);
    $materialCost = floatval($input['material_cost'] ?? 0);
    $totalAmount = floatval($input['total_amount'] ?? 0);

    if ($laborCost < 0 || $materialCost < 0 || $totalAmount <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Cost values must be positive. Total amount must be greater than zero.'
        ]);
        return false;
    }

    return true;
}

function validateEstimatedDuration($input)
{
    $estimatedDuration = intval($input['estimated_duration'] ?? 0);
    if ($estimatedDuration <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Estimated duration must be greater than 0 days'
        ]);
        return false;
    }

    return true;
}

function handleGet()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    $quotationId = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
    $requestId = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    $filters = [
        'user_id' => $AUTH_COMPANY_ID
    ];

    if ($quotationId) {
        $filters['quotation_id'] = $quotationId;
    }
    if ($requestId) {
        $filters['request_id'] = $requestId;
    }
    if ($status) {
        $filters['status'] = $status;
    }

    try {
        $quotations = $quotationModel->getAll($filters);

        echo json_encode([
            'success' => true,
            'data' => $quotations,
            'count' => count($quotations)
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error retrieving quotations: ' . $e->getMessage()
        ]);
    }
}

function handlePost()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    $input = readJsonBody();
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        return;
    }

    // Authoritative company identity
    $input['user_id'] = $AUTH_COMPANY_ID;

    if (!validateRequiredFields($input)) return;
    if (!validateCostValues($input)) return;
    if (!validateEstimatedDuration($input)) return;

    if ($quotationModel->hasQuotationForRequest($input['request_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'A pending quotation already exists for this request'
        ]);
        return;
    }

    try {
        $quotationId = $quotationModel->create($input);
        if (!$quotationId) {
            throw new Exception('Failed to insert quotation into database');
        }

        $undo = undo_create(
            $GLOBALS['pdo'],
            'quotation',
            (int)$quotationId,
            'submit',
            ['request_id' => (int)($input['request_id'] ?? 0)],
            (int)$AUTH_COMPANY_ID,
            'company',
            null
        );

        $quotation = $quotationModel->getById($quotationId);
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Quotation submitted successfully',
            'data' => $quotation,
            'undo' => $undo
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

function handlePut()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    $input = readJsonBody();
    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
        return;
    }

    if (!isset($input['quotation_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing quotation_id']);
        return;
    }

    $quotationId = intval($input['quotation_id']);
    $existing = $quotationModel->getById($quotationId);
    if (!$existing) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Quotation not found']);
        return;
    }

    $existingCompanyId = (int)($existing['company_id'] ?? $existing['user_id'] ?? 0);
    if ($existingCompanyId !== $AUTH_COMPANY_ID) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden']);
        return;
    }

    // Validate required update fields
    $requiredFields = [
        'title', 'labor_cost', 'material_cost', 'total_amount',
        'start_date', 'completion_date', 'estimated_duration'
    ];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            return;
        }
    }
    if (floatval($input['total_amount']) <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Total amount must be greater than zero']);
        return;
    }

    try {
        $success = $quotationModel->update($quotationId, $input);
        if (!$success) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update quotation. Quotation may not exist or is not in pending status.'
            ]);
            return;
        }

        $quotation = $quotationModel->getById($quotationId);
        echo json_encode([
            'success' => true,
            'message' => 'Quotation updated successfully',
            'data' => $quotation
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update quotation: ' . $e->getMessage()
        ]);
    }
}

function handleDelete()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    $quotationId = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
    if (!$quotationId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing quotation_id parameter']);
        return;
    }

    try {
        $success = $quotationModel->delete($quotationId, $AUTH_COMPANY_ID);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Quotation deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete quotation. Quotation may not exist, may not be pending, or you do not own it.'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete quotation: ' . $e->getMessage()
        ]);
    }
}

// ============================================================================
// Enhanced endpoint handlers
// ============================================================================

function handlePostEnhanced()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    try {
        $input = readJsonBody();
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
            return;
        }

        // Authoritative company identity
        $input['user_id'] = $AUTH_COMPANY_ID;

        $required = ['request_id', 'user_id', 'title', 'labor_cost', 'material_cost',
            'total_amount', 'start_date', 'completion_date', 'estimated_duration'];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
                return;
            }
        }

        if (isset($input['budget_type']) && !in_array($input['budget_type'], ['fixed', 'flexible'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid budget_type. Must be "fixed" or "flexible"']);
            return;
        }

        if (isset($input['payment_method']) && !in_array($input['payment_method'], ['milestone', '50-50', '30-70', 'upfront_final', 'completion', 'time_material'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid payment_method']);
            return;
        }

        if (isset($input['pricing_type']) && !in_array($input['pricing_type'], ['fixed_price', 'time_based', 'hybrid'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid pricing_type']);
            return;
        }

        if (($input['payment_method'] ?? null) === 'time_material') {
            if (!isset($input['hourly_rate']) || floatval($input['hourly_rate']) <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Hourly rate is required when using Time & Material payment method']);
                return;
            }
        }

        // ===================================
        // DIRECT REQUEST HANDLING
        // ===================================
        $isDirect = (isset($input['is_direct']) && $input['is_direct'] === true);
        if ($isDirect) {
            $GLOBALS['pdo']->beginTransaction();
            try {
                // 1. Convert direct request to job request
                $newJobRequestId = $quotationModel->convertDirectToJobRequest($input['request_id']);
                if (!$newJobRequestId) {
                    throw new Exception("Failed to convert direct request to job request");
                }
                
                // 2. Update input with new job request ID
                $input['request_id'] = $newJobRequestId;
                
                // 3. Set status to accepted for direct requests (per user requirement)
                $input['status'] = 'accepted';
            } catch (Exception $e) {
                $GLOBALS['pdo']->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Conversion failed: ' . $e->getMessage()]);
                return;
            }
        }

        $quotationId = $quotationModel->createEnhanced($input);
        if (!$quotationId) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to create enhanced quotation']);
            return;
        }

        $undo = undo_create(
            $GLOBALS['pdo'],
            'quotation',
            (int)$quotationId,
            'submit',
            ['request_id' => (int)($input['request_id'] ?? 0), 'enhanced' => true],
            (int)$AUTH_COMPANY_ID,
            'company',
            null
        );

        if ($isDirect) {
            $GLOBALS['pdo']->commit();
        }

        $quotation = $quotationModel->getEnhancedById($quotationId, $AUTH_COMPANY_ID);
        echo json_encode([
            'success' => true,
            'message' => 'Enhanced quotation created successfully',
            'data' => $quotation,
            'undo' => $undo
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
}

function handleUndoSubmit()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    $input = readJsonBody();
    if (!is_array($input)) {
        $input = $_POST;
    }

    $quotationId = (int)($input['quotation_id'] ?? 0);
    if ($quotationId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing quotation_id']);
        return;
    }

    // Must be within undo window
    $active = undo_get_active($GLOBALS['pdo'], 'quotation', $quotationId, 'submit');
    if (!$active) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Undo window has expired']);
        return;
    }

    // Block if a contract has been created from this quotation
    $stmt = $GLOBALS['pdo']->prepare("SELECT 1 FROM contract WHERE quotation_id = ? LIMIT 1");
    $stmt->execute([$quotationId]);
    if ($stmt->fetchColumn()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Undo not available: quotation already processed into a contract']);
        return;
    }

    // Delete only if still pending and owned by company
    $ok = $quotationModel->delete($quotationId, $AUTH_COMPANY_ID);
    if (!$ok) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Undo not available: quotation is not pending or not owned']);
        return;
    }

    undo_mark_used($GLOBALS['pdo'], (int)$active['undo_id'], (int)$AUTH_COMPANY_ID, 'company');
    echo json_encode(['success' => true, 'message' => 'Quotation submission undone']);
}

function handlePutEnhanced()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    try {
        $input = readJsonBody();
        if (!is_array($input)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
            return;
        }

        if (!isset($input['quotation_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing quotation_id']);
            return;
        }

        $quotationId = intval($input['quotation_id']);
        $input['user_id'] = $AUTH_COMPANY_ID;

        $success = $quotationModel->updateEnhanced($quotationId, $AUTH_COMPANY_ID, $input);
        if (!$success) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update enhanced quotation. Quotation may not exist, may not be pending, or you do not own it.'
            ]);
            return;
        }

        $quotation = $quotationModel->getEnhancedById($quotationId, $AUTH_COMPANY_ID);
        echo json_encode([
            'success' => true,
            'message' => 'Enhanced quotation updated successfully',
            'data' => $quotation
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
}

function handleGetEnhanced()
{
    global $quotationModel, $AUTH_COMPANY_ID;

    try {
        if (!isset($_GET['quotation_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing quotation_id parameter']);
            return;
        }

        $quotationId = intval($_GET['quotation_id']);
        $quotation = $quotationModel->getEnhancedById($quotationId, $AUTH_COMPANY_ID);

        if ($quotation) {
            echo json_encode(['success' => true, 'data' => $quotation]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Quotation not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
    }
}
