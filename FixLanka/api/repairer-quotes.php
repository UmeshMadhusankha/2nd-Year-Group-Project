<?php
/**
 * Repairer Quotes API
 * Handles CRUD operations for repairer quotations
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../config/database.php';
require_once '../models/RepairerQuoteModel.php';

// Initialize model
$quoteModel = new RepairerQuote($pdo);

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
 * Handle GET requests - Retrieve quotes
 */
function handleGet() {
    global $quoteModel;
    
    // Get query parameters
    $repairer_id = isset($_GET['repairer_id']) ? intval($_GET['repairer_id']) : null;
    $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
    $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    try {
        // Build filters array
        $filters = [];
        
        if ($quote_id) {
            $filters['quote_id'] = $quote_id;
        }
        
        if ($repairer_id) {
            $filters['repairer_id'] = $repairer_id;
        }
        
        if ($request_id) {
            $filters['request_id'] = $request_id;
        }
        
        if ($status) {
            $filters['status'] = $status;
        }
        
        // Get quotes using model
        $quotes = $quoteModel->getAll($filters);
        
        // Return response
        echo json_encode([
            'success' => true,
            'data' => $quotes,
            'count' => count($quotes)
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error retrieving quotes: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle POST requests - Create new quote
 */
function handlePost() {
    global $quoteModel;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['request_id']) || !isset($input['repairer_id']) || 
        !isset($input['quoteAmount']) || !isset($input['estimatedDays']) || 
        !isset($input['validUntil'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing required fields: request_id, repairer_id, quoteAmount, estimatedDays, validUntil'
        ]);
        return;
    }
    
    // Validate quote amount
    if (floatval($input['quoteAmount']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Quote amount must be greater than 0'
        ]);
        return;
    }
    
    // Validate estimated days
    if (intval($input['estimatedDays']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Estimated days must be greater than 0'
        ]);
        return;
    }
    
    // Check if repairer already has a quote for this job
    if ($quoteModel->hasQuoteForJob($input['request_id'], $input['repairer_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'You have already submitted a quote for this job'
        ]);
        return;
    }
    
    try {
        // Create the quote
        $quote_id = $quoteModel->create($input);
        
        if ($quote_id) {
            // Retrieve the created quote
            $quote = $quoteModel->getById($quote_id);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Quote submitted successfully',
                'data' => $quote
            ]);
        } else {
            throw new Exception('Failed to create quote');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create quote: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle PUT requests - Update existing quote
 */
function handlePut() {
    global $quoteModel;
    
    // Get JSON input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    
    // Debug logging
    
    // Validate quote_id and repairer_id
    if (!isset($input['quote_id']) || !isset($input['repairer_id'])) {
        error_log("Missing parameters - quote_id: " . (isset($input['quote_id']) ? 'present' : 'missing') . 
                  ", repairer_id: " . (isset($input['repairer_id']) ? 'present' : 'missing'));
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quote_id or repairer_id',
            'received' => $input,
            'debug' => [
                'has_quote_id' => isset($input['quote_id']),
                'has_repairer_id' => isset($input['repairer_id']),
                'quote_id_value' => $input['quote_id'] ?? null,
                'repairer_id_value' => $input['repairer_id'] ?? null
            ]
        ]);
        return;
    }
    
    $quote_id = intval($input['quote_id']);
    $repairer_id = intval($input['repairer_id']);
    
    // Validate quote amount if provided
    if (isset($input['quoteAmount']) && floatval($input['quoteAmount']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Quote amount must be greater than 0'
        ]);
        return;
    }
    
    // Validate estimated days if provided
    if (isset($input['estimatedDays']) && intval($input['estimatedDays']) <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Estimated days must be greater than 0'
        ]);
        return;
    }
    
    try {
        // Update the quote
        $success = $quoteModel->update($quote_id, $repairer_id, $input);
        
        if ($success) {
            // Retrieve updated quote
            $quote = $quoteModel->getById($quote_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Quote updated successfully',
                'data' => $quote
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update quote. Quote may not exist, not belong to you, or not be in pending status.'
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update quote: ' . $e->getMessage()
        ]);
    }
}

/**
 * Handle DELETE requests - Delete quote
 */
function handleDelete() {
    global $quoteModel;
    
    // Get quote_id and repairer_id from query parameters
    $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : null;
    $repairer_id = isset($_GET['repairer_id']) ? intval($_GET['repairer_id']) : null;
    
    // Debug logging
    error_log("DELETE Request - quote_id: " . ($quote_id ?? 'null') . ", repairer_id: " . ($repairer_id ?? 'null'));
    
    if (!$quote_id || !$repairer_id) {
        error_log("DELETE Failed - Missing parameters. quote_id: " . ($quote_id ? 'present' : 'missing') . 
                  ", repairer_id: " . ($repairer_id ? 'present' : 'missing'));
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quote_id or repairer_id parameter',
            'debug' => [
                'quote_id' => $quote_id,
                'repairer_id' => $repairer_id,
                'get_params' => $_GET
            ]
        ]);
        return;
    }
    
    try {
        // Delete the quote
        $success = $quoteModel->delete($quote_id, $repairer_id);
        
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Quote deleted successfully'
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete quote. Quote may not exist, not belong to you, or not be in pending status.'
            ]);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete quote: ' . $e->getMessage()
        ]);
    }
}
