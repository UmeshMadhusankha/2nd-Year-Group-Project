<?php
/**
 * Company Quotations API
 * Handles CRUD operations for company quotations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../config/database.php';
require_once '../models/CompanyQuotationModel.php';

// Initialize model
$quotationModel = new CompanyQuotation($pdo);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Route to appropriate handler
switch ($method) {
    case 'GET':
        handleGet();
        break;
    case 'POST':
        handlePost();
        break;
    case 'PUT':
        handlePut();
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
 */
function handleGet() {
    global $quotationModel;
    
    // Get query parameters
    $quotation_id = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
    $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    try {
        // Build filters array
        $filters = [];
        
        if ($quotation_id) {
            $filters['quotation_id'] = $quotation_id;
        }
        
        if ($request_id) {
            $filters['request_id'] = $request_id;
        }
        
        if ($user_id) {
            $filters['user_id'] = $user_id;
        }
        
        if ($status) {
            $filters['status'] = $status;
        }
        
        error_log("GET request with filters: " . print_r($filters, true));
        
        // Get quotations using model
        $quotations = $quotationModel->getAll($filters);
        
        error_log("Retrieved " . count($quotations) . " quotations");
        if (count($quotations) > 0) {
            error_log("Sample quotation: " . print_r($quotations[0], true));
        }
        
        // Return response
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
 */
function handlePost() {
    global $quotationModel;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['request_id', 'user_id', 'title', 'labor_cost', 'material_cost', 
                 'total_amount', 'start_date', 'completion_date', 'estimated_duration'];
    
    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => "Missing required field: $field"
            ]);
            return;
        }
    }
    
    // Validate numeric fields
    if (floatval($input['labor_cost']) < 0 || floatval($input['material_cost']) < 0 || floatval($input['total_amount']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid cost values'
        ]);
        return;
    }
    
    // Validate estimated duration
    if (intval($input['estimated_duration']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Estimated duration must be greater than 0'
        ]);
        return;
    }
    
    // Check if quotation already exists for this request
    if ($quotationModel->hasQuotationForRequest($input['request_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'A pending quotation already exists for this request'
        ]);
        return;
    }
    
    try {
        // Create the quotation
        error_log("Creating quotation with data: " . print_r($input, true));
        $quotation_id = $quotationModel->create($input);
        
        if ($quotation_id) {
            error_log("Quotation created successfully with ID: " . $quotation_id);
            
            // Retrieve the created quotation
            $quotation = $quotationModel->getById($quotation_id);
            error_log("Retrieved quotation: " . print_r($quotation, true));
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Quotation submitted successfully',
                'data' => $quotation
            ]);
        } else {
            error_log("Failed to create quotation - quotationModel->create() returned false");
            throw new Exception('Failed to create quotation');
        }
        
    } catch (Exception $e) {
        error_log("Exception in handlePost: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create quotation: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests - Update existing quotation
 */
function handlePut() {
    global $quotationModel;
    
    // Get JSON input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Debug logging
    error_log("PUT Request Raw Input: " . $rawInput);
    error_log("PUT Request Decoded: " . print_r($input, true));
    
    // Validate quotation_id
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
    
    $quotation_id = intval($input['quotation_id']);
    
    // Validate required update fields
    $required = ['title', 'labor_cost', 'material_cost', 'total_amount', 
                 'start_date', 'completion_date', 'estimated_duration'];
    
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
    
    // Validate cost values
    if (floatval($input['total_amount']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Total amount must be greater than zero'
        ]);
        return;
    }
    
    try {
        // Update the quotation
        $success = $quotationModel->update($quotation_id, $input);
        
        if ($success) {
            // Retrieve updated quotation
            $quotation = $quotationModel->getById($quotation_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Quotation updated successfully',
                'data' => $quotation
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update quotation. Quotation may not exist or not be in pending status.'
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
 */
function handleDelete() {
    global $quotationModel;
    
    // Get quotation_id from query parameters
    $quotation_id = isset($_GET['quotation_id']) ? intval($_GET['quotation_id']) : null;
    
    // Debug logging
    error_log("DELETE Request - quotation_id: " . ($quotation_id ?? 'null'));
    error_log("DELETE Request - Full GET params: " . print_r($_GET, true));
    
    if (!$quotation_id) {
        error_log("DELETE Failed - Missing quotation_id");
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quotation_id parameter',
            'debug' => [
                'quotation_id' => $quotation_id,
                'get_params' => $_GET
            ]
        ]);
        return;
    }
    
    try {
        // Delete the quotation
        $success = $quotationModel->delete($quotation_id);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Quotation deleted successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete quotation. Quotation may not exist or not be in pending status.'
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
