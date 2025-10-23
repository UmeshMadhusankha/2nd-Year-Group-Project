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
    global $conn;
    
    // Get query parameters
    $repairer_id = isset($_GET['repairer_id']) ? intval($_GET['repairer_id']) : null;
    $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
    $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    
    // **DUMMY DATA MODE**
    // Return mock quotations for testing
    $mockQuotes = [
        [
            'quote_id' => 1001,
            'request_id' => 101,
            'repairer_id' => 1,
            'quoteAmount' => 3500.00,
            'estimatedDays' => 2,
            'warrantyPeriod' => 6,
            'validUntil' => '2025-10-31',
            'materialsIncluded' => true,
            'message' => 'I can complete this plumbing repair with high-quality materials and guarantee no leaks. I have 10 years of experience in similar repairs.',
            'status' => 'pending',
            'dateSubmitted' => '2025-10-23 14:30:00'
        ],
        [
            'quote_id' => 1002,
            'request_id' => 102,
            'repairer_id' => 1,
            'quoteAmount' => 5200.00,
            'estimatedDays' => 1,
            'warrantyPeriod' => 12,
            'validUntil' => '2025-11-01',
            'materialsIncluded' => true,
            'message' => 'Professional ceiling fan installation including electrical work and testing. All safety standards will be followed.',
            'status' => 'accepted',
            'dateSubmitted' => '2025-10-22 09:15:00'
        ],
        [
            'quote_id' => 1003,
            'request_id' => 103,
            'repairer_id' => 1,
            'quoteAmount' => 8500.00,
            'estimatedDays' => 3,
            'warrantyPeriod' => 3,
            'validUntil' => '2025-10-28',
            'materialsIncluded' => false,
            'message' => 'Washing machine motor replacement. Customer to provide the motor. I will handle installation and testing.',
            'status' => 'rejected',
            'dateSubmitted' => '2025-10-21 16:45:00'
        ],
        [
            'quote_id' => 1004,
            'request_id' => 104,
            'repairer_id' => 1,
            'quoteAmount' => 12000.00,
            'estimatedDays' => 5,
            'warrantyPeriod' => 24,
            'validUntil' => '2025-10-20',
            'materialsIncluded' => true,
            'message' => 'Complete AC servicing including gas refill, filter replacement, and coil cleaning. Premium service package.',
            'status' => 'expired',
            'dateSubmitted' => '2025-10-15 11:20:00'
        ],
        [
            'quote_id' => 1005,
            'request_id' => 105,
            'repairer_id' => 1,
            'quoteAmount' => 4500.00,
            'estimatedDays' => 2,
            'warrantyPeriod' => 6,
            'validUntil' => '2025-11-05',
            'materialsIncluded' => true,
            'message' => 'Cabinet door repair with quality hinges and alignment. Will ensure smooth operation.',
            'status' => 'pending',
            'dateSubmitted' => '2025-10-24 08:00:00'
        ]
    ];
    
    // Filter mock data based on parameters
    $filteredQuotes = array_filter($mockQuotes, function($quote) use ($quote_id, $repairer_id, $request_id, $status) {
        if ($quote_id && $quote['quote_id'] != $quote_id) return false;
        if ($repairer_id && $quote['repairer_id'] != $repairer_id) return false;
        if ($request_id && $quote['request_id'] != $request_id) return false;
        if ($status && $quote['status'] != $status) return false;
        return true;
    });
    
    // Convert to indexed array
    $filteredQuotes = array_values($filteredQuotes);
    
    echo json_encode([
        'success' => true,
        'data' => $filteredQuotes,
        'count' => count($filteredQuotes),
        'mode' => 'dummy'
    ]);
    
    /* UNCOMMENT THIS WHEN READY TO USE REAL DATABASE:
    
    // Build query
    $sql = "SELECT * FROM RepairerQuote WHERE 1=1";
    $params = [];
    $types = "";
    
    if ($quote_id) {
        $sql .= " AND quote_id = ?";
        $params[] = $quote_id;
        $types .= "i";
    }
    
    if ($repairer_id) {
        $sql .= " AND repairer_id = ?";
        $params[] = $repairer_id;
        $types .= "i";
    }
    
    if ($request_id) {
        $sql .= " AND request_id = ?";
        $params[] = $request_id;
        $types .= "i";
    }
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    $sql .= " ORDER BY dateSubmitted DESC";
    
    try {
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conn->query($sql);
        }
        
        $quotes = [];
        while ($row = $result->fetch_assoc()) {
            $quotes[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $quotes,
            'count' => count($quotes)
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
    */
}

/**
 * Handle POST requests - Create new quote
 */
function handlePost() {
    global $conn;
    
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
    
    // Extract data
    $request_id = intval($input['request_id']);
    $repairer_id = intval($input['repairer_id']);
    $quoteAmount = floatval($input['quoteAmount']);
    $estimatedDays = intval($input['estimatedDays']);
    $warrantyPeriod = isset($input['warrantyPeriod']) ? intval($input['warrantyPeriod']) : 0;
    $validUntil = $input['validUntil'];
    $materialsIncluded = isset($input['materialsIncluded']) ? (bool)$input['materialsIncluded'] : true;
    $message = isset($input['message']) ? $input['message'] : null;
    $status = isset($input['status']) ? $input['status'] : 'pending';
    
    // Validate status
    $valid_statuses = ['pending', 'accepted', 'rejected', 'expired'];
    if (!in_array($status, $valid_statuses)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid status. Must be one of: ' . implode(', ', $valid_statuses)
        ]);
        return;
    }
    
    // **DUMMY DATA MODE**
    // For now, we'll return success without actual database insertion
    // Simulating a successful quote submission
    
    $dummyQuoteId = rand(1000, 9999);
    $currentTimestamp = date('Y-m-d H:i:s');
    
    // Simulated response data
    $responseData = [
        'quote_id' => $dummyQuoteId,
        'request_id' => $request_id,
        'repairer_id' => $repairer_id,
        'quoteAmount' => $quoteAmount,
        'estimatedDays' => $estimatedDays,
        'warrantyPeriod' => $warrantyPeriod,
        'validUntil' => $validUntil,
        'materialsIncluded' => $materialsIncluded,
        'message' => $message,
        'status' => $status,
        'dateSubmitted' => $currentTimestamp
    ];
    
    // Log the data for debugging (you can check this in PHP error logs)
    error_log("DUMMY QUOTE SUBMISSION: " . json_encode($responseData));
    
    // Return success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Quote submitted successfully (dummy mode)',
        'data' => $responseData
    ]);
    
    /* UNCOMMENT THIS WHEN READY TO USE REAL DATABASE:
    
    try {
        // Prepare SQL statement
        $sql = "INSERT INTO RepairerQuote (request_id, repairer_id, quoteAmount, estimatedDays, 
                warrantyPeriod, validUntil, materialsIncluded, message, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidiisbss", $request_id, $repairer_id, $quoteAmount, $estimatedDays, 
                         $warrantyPeriod, $validUntil, $materialsIncluded, $message, $status);
        
        if ($stmt->execute()) {
            $quote_id = $conn->insert_id;
            
            // Retrieve the created quote
            $result = $conn->query("SELECT * FROM RepairerQuote WHERE quote_id = $quote_id");
            $quote = $result->fetch_assoc();
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Quote submitted successfully',
                'data' => $quote
            ]);
        } else {
            throw new Exception($stmt->error);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create quote: ' . $e->getMessage()
        ]);
    }
    */
}

/**
 * Handle PUT requests - Update existing quote
 */
function handlePut() {
    global $conn;
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate quote_id
    if (!isset($input['quote_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quote_id'
        ]);
        return;
    }
    
    $quote_id = intval($input['quote_id']);
    
    // **DUMMY DATA MODE**
    // Simulate successful update with all fields
    $responseData = [
        'quote_id' => $quote_id,
        'quoteAmount' => isset($input['quoteAmount']) ? floatval($input['quoteAmount']) : null,
        'estimatedDays' => isset($input['estimatedDays']) ? intval($input['estimatedDays']) : null,
        'warrantyPeriod' => isset($input['warrantyPeriod']) ? intval($input['warrantyPeriod']) : null,
        'validUntil' => isset($input['validUntil']) ? $input['validUntil'] : null,
        'materialsIncluded' => isset($input['materialsIncluded']) ? (bool)$input['materialsIncluded'] : null,
        'message' => isset($input['message']) ? $input['message'] : null,
        'status' => isset($input['status']) ? $input['status'] : 'pending',
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    error_log("DUMMY QUOTE UPDATE: " . json_encode($responseData));
    
    echo json_encode([
        'success' => true,
        'message' => 'Quote updated successfully (dummy mode)',
        'data' => $responseData
    ]);
    
    /* UNCOMMENT THIS WHEN READY TO USE REAL DATABASE:
    
    try {
        // First, check if quote exists and is in pending status
        $checkSql = "SELECT status FROM RepairerQuote WHERE quote_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $quote_id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Quote not found'
            ]);
            return;
        }
        
        $quote = $result->fetch_assoc();
        if ($quote['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Only pending quotes can be edited'
            ]);
            return;
        }
        
        // Build update query dynamically
        $updates = [];
        $params = [];
        $types = "";
        
        if (isset($input['quoteAmount'])) {
            $updates[] = "quoteAmount = ?";
            $params[] = floatval($input['quoteAmount']);
            $types .= "d";
        }
        
        if (isset($input['estimatedDays'])) {
            $updates[] = "estimatedDays = ?";
            $params[] = intval($input['estimatedDays']);
            $types .= "i";
        }
        
        if (isset($input['warrantyPeriod'])) {
            $updates[] = "warrantyPeriod = ?";
            $params[] = intval($input['warrantyPeriod']);
            $types .= "i";
        }
        
        if (isset($input['validUntil'])) {
            $updates[] = "validUntil = ?";
            $params[] = $input['validUntil'];
            $types .= "s";
        }
        
        if (isset($input['materialsIncluded'])) {
            $updates[] = "materialsIncluded = ?";
            $params[] = (bool)$input['materialsIncluded'];
            $types .= "i";
        }
        
        if (isset($input['message'])) {
            $updates[] = "message = ?";
            $params[] = $input['message'];
            $types .= "s";
        }
        
        if (isset($input['status'])) {
            $valid_statuses = ['pending', 'accepted', 'rejected', 'expired'];
            if (!in_array($input['status'], $valid_statuses)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid status'
                ]);
                return;
            }
            $updates[] = "status = ?";
            $params[] = $input['status'];
            $types .= "s";
        }
        
        if (empty($updates)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'No fields to update'
            ]);
            return;
        }
        
        $sql = "UPDATE RepairerQuote SET " . implode(", ", $updates) . " WHERE quote_id = ?";
        $params[] = $quote_id;
        $types .= "i";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Retrieve updated quote
                $result = $conn->query("SELECT * FROM RepairerQuote WHERE quote_id = $quote_id");
                $quote = $result->fetch_assoc();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quote updated successfully',
                    'data' => $quote
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Quote not found or no changes made'
                ]);
            }
        } else {
            throw new Exception($stmt->error);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update quote: ' . $e->getMessage()
        ]);
    }
    */
}

/**
 * Handle DELETE requests - Delete quote
 */
function handleDelete() {
    global $conn;
    
    // Get quote_id from query parameter
    $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : null;
    
    if (!$quote_id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Missing quote_id parameter'
        ]);
        return;
    }
    
    // **DUMMY DATA MODE**
    // Simulate successful deletion
    error_log("DUMMY QUOTE DELETION: quote_id = $quote_id");
    
    echo json_encode([
        'success' => true,
        'message' => 'Quote deleted successfully (dummy mode)',
        'quote_id' => $quote_id
    ]);
    
    /* UNCOMMENT THIS WHEN READY TO USE REAL DATABASE:
    
    try {
        $sql = "DELETE FROM RepairerQuote WHERE quote_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $quote_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Quote deleted successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Quote not found'
                ]);
            }
        } else {
            throw new Exception($stmt->error);
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to delete quote: ' . $e->getMessage()
        ]);
    }
    */
}
