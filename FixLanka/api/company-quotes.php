<?php
/**
 * Company Quotations API
 * 
 * RESTful API endpoint for managing company quotations.
 * Supports CRUD operations: GET, POST, PUT, DELETE
 * 
 * @package FixLanka\API
 * @version 1.0.0
 */

// Set response headers for JSON API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include required dependencies
require_once '../config/database.php';
require_once '../models/CompanyQuotationModel.php';

// Initialize quotation model with database connection
$quotationModel = new CompanyQuotation($pdo);

// Get HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight OPTIONS requests for CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Route request to appropriate handler based on HTTP method
switch ($method) {
    case 'GET':
        // Check for enhanced endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'get_enhanced') {
            handleGetEnhanced();
        } else {
            handleGet();
        }
        break;
    case 'POST':
        // Check for enhanced endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'create_enhanced') {
            handlePostEnhanced();
        } else {
            handlePost();
        }
        break;
    case 'PUT':
        // Check for enhanced endpoint
        if (isset($_GET['action']) && $_GET['action'] === 'update_enhanced') {
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
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

/**
 * Handle GET requests - Retrieve quotations
 * 
 * Supports filtering by:
 * - quotation_id: Get specific quotation
 * - request_id: Get quotations for a job request
 * - user_id: Get all quotations by a company
 * - status: Filter by quotation status
 * 
 * @return void Outputs JSON response
 */
function handleGet()
{
    global $quotationModel;

    // Get query parameters for filtering
    $quotationId = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
    $requestId = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;

    try {
        // Build filters array dynamically
        $filters = [];

        if ($quotationId) {
            $filters['quotation_id'] = $quotationId;
        }

        if ($requestId) {
            $filters['request_id'] = $requestId;
        }

        if ($userId) {
            $filters['user_id'] = $userId;
        }

        if ($status) {
            $filters['status'] = $status;
        }

        
        // Retrieve quotations from database
        $quotations = $quotationModel->getAll($filters);

        error_log("Retrieved " . count($quotations) . " quotations");
        if (count($quotations) > 0) {
            
        }

        // Return successful response
        echo json_encode([
            'success' => true,
            'data' => $quotations,
            'count' => count($quotations)
        ]);

    } catch (Exception $e) {
        error_log("Exception in handleGet: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error retrieving quotations: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests - Create new quotation
 * 
 * Creates a new quotation for a job request. Validates all required fields
 * and business rules before insertion.
 * 
 * Required fields: request_id, user_id, title, labor_cost, material_cost,
 *                  total_amount, start_date, completion_date, estimated_duration
 * 
 * @return void Outputs JSON response with created quotation data
 */
function handlePost()
{
    global $quotationModel;

    // Parse JSON input from request body
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields are present
    if (!validateRequiredFields($input)) {
        return;
    }

    // Validate cost values are positive
    if (!validateCostValues($input)) {
        return;
    }

    // Validate estimated duration is positive
    if (!validateEstimatedDuration($input)) {
        return;
    }

    // Check for duplicate quotations
    if ($quotationModel->hasQuotationForRequest($input['request_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'A pending quotation already exists for this request'
        ]);
        return;
    }

    try {
        // Create the quotation in database
        
        $quotationId = $quotationModel->create($input);

        if ($quotationId) {
            error_log("Quotation created successfully with ID: " . $quotationId);

            // Retrieve the created quotation with all related data
            $quotation = $quotationModel->getById($quotationId);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Quotation submitted successfully',
                'data' => $quotation
            ]);
        } else {
            error_log("Failed to create quotation - create() returned false");
            throw new Exception('Failed to insert quotation into database');
        }

    } catch (Exception $e) {
        error_log("Exception in handlePost: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Validate required fields are present in input
 * 
 * @param array $input Input data to validate
 * @return bool True if all required fields present, false otherwise
 */
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

/**
 * Validate cost values are non-negative
 * 
 * @param array $input Input data to validate
 * @return bool True if cost values are valid, false otherwise
 */
function validateCostValues($input)
{
    $laborCost = floatval($input['labor_cost']);
    $materialCost = floatval($input['material_cost']);
    $totalAmount = floatval($input['total_amount']);

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

/**
 * Validate estimated duration is positive
 * 
 * @param array $input Input data to validate
 * @return bool True if duration is valid, false otherwise
 */
function validateEstimatedDuration($input)
{
    $estimatedDuration = intval($input['estimated_duration']);

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

/**
 * Handle PUT requests - Update existing quotation
 * 
 * Updates an existing quotation. Only quotations with 'pending' status can be updated.
 * Validates all required fields and ensures quotation exists before updating.
 * 
 * @return void Outputs JSON response with updated quotation data
 */
function handlePut()
{
    global $quotationModel;

    // Parse JSON input from request body
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    // Debug logging
    
    // Validate quotation_id is present
    if (!isset($input['quotation_id'])) {
        error_log("Missing quotation_id in update request");
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quotation_id',
            'received' => $input
        ]);
        return;
    }

    $quotationId = intval($input['quotation_id']);

    // Validate required update fields
    $requiredFields = [
        'title', 'labor_cost', 'material_cost', 'total_amount',
        'start_date', 'completion_date', 'estimated_duration'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Missing required field: $field"
            ]);
            return;
        }
    }

    // Validate total amount is positive
    if (floatval($input['total_amount']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Total amount must be greater than zero'
        ]);
        return;
    }

    try {
        // Attempt to update the quotation
        $success = $quotationModel->update($quotationId, $input);

        if ($success) {
            // Retrieve updated quotation
            $quotation = $quotationModel->getById($quotationId);

            echo json_encode([
                'success' => true,
                'message' => 'Quotation updated successfully',
                'data' => $quotation
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update quotation. Quotation may not exist or is not in pending status.'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update quotation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests - Delete quotation
 * 
 * Deletes a quotation from the database. Only quotations with 'pending' status
 * can be deleted to prevent removal of accepted or processed quotations.
 * 
 * @return void Outputs JSON response indicating success or failure
 */
function handleDelete()
{
    global $quotationModel;

    // Get quotation_id from query parameters
    $quotationId = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;

    // Debug logging
    error_log("DELETE Request - quotation_id: " . ($quotationId ?? 'null'));
    
    // Validate quotation_id is present
    if (!$quotationId) {
        error_log("DELETE Failed - Missing quotation_id");
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quotation_id parameter',
            'debug' => [
                'quotation_id' => $quotationId,
                'get_params' => $_GET
            ]
        ]);
        return;
    }

    try {
        // Attempt to delete the quotation
        $success = $quotationModel->delete($quotationId);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Quotation deleted successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete quotation. Quotation may not exist or is not in pending status.'
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
// BUSINESS LOGIC ENHANCEMENT - New Endpoint Handlers (Added for Phase 1)
// These handlers work with enhanced quotation methods
// ============================================================================

/**
 * Handle POST request for creating enhanced quotation
 * Endpoint: POST /api/company-quotes.php?action=create_enhanced
 */
function handlePostEnhanced()
{
    global $quotationModel;

    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid JSON input'
            ]);
            return;
        }

        // Validate required fields
        $required = ['request_id', 'user_id', 'title', 'labor_cost', 'material_cost', 
                     'total_amount', 'start_date', 'completion_date', 'estimated_duration'];
        
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => "Missing required field: $field"
                ]);
                return;
            }
        }

        // Validate business logic fields
        if (isset($input['budget_type']) && !in_array($input['budget_type'], ['fixed', 'flexible'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid budget_type. Must be "fixed" or "flexible"'
            ]);
            return;
        }

        if (isset($input['payment_method']) && !in_array($input['payment_method'], ['milestone', '50-50', '30-70', 'upfront_final', 'time_material'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid payment_method'
            ]);
            return;
        }

        if (isset($input['pricing_type']) && !in_array($input['pricing_type'], ['fixed_price', 'time_based', 'hybrid'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid pricing_type'
            ]);
            return;
        }

        // Validate hourly_rate only when payment_method is time_material
        // (not just pricing_type, as time_based can come from hourly labor without time_material payment)
        if (isset($input['payment_method']) && $input['payment_method'] === 'time_material') {
            if (!isset($input['hourly_rate']) || $input['hourly_rate'] <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Hourly rate is required when using Time & Material payment method'
                ]);
                return;
            }
        }

        // Create enhanced quotation
        $quotationId = $quotationModel->createEnhanced($input);

        if ($quotationId) {
            // Fetch the created quotation
            $quotation = $quotationModel->getEnhancedById($quotationId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Enhanced quotation created successfully',
                'data' => $quotation
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to create enhanced quotation'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT request for updating enhanced quotation
 * Endpoint: PUT /api/company-quotes.php?action=update_enhanced
 */
function handlePutEnhanced()
{
    global $quotationModel;

    try {
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid JSON input'
            ]);
            return;
        }

        // Validate quotation_id
        if (!isset($input['quotation_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing quotation_id'
            ]);
            return;
        }

        // Update enhanced quotation
        $success = $quotationModel->updateEnhanced($input['quotation_id'], $input);

        if ($success) {
            // Fetch the updated quotation
            $quotation = $quotationModel->getEnhancedById($input['quotation_id']);
            
            echo json_encode([
                'success' => true,
                'message' => 'Enhanced quotation updated successfully',
                'data' => $quotation
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update enhanced quotation. Quotation may not exist or is not in pending status.'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle GET request for fetching enhanced quotation
 * Endpoint: GET /api/company-quotes.php?action=get_enhanced&quotation_id=123
 */
function handleGetEnhanced()
{
    global $quotationModel;

    try {
        // Validate quotation_id
        if (!isset($_GET['quotation_id'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Missing quotation_id parameter'
            ]);
            return;
        }

        $quotationId = intval($_GET['quotation_id']);

        // Fetch enhanced quotation
        $quotation = $quotationModel->getEnhancedById($quotationId);

        if ($quotation) {
            echo json_encode([
                'success' => true,
                'data' => $quotation
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Quotation not found'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }
}
