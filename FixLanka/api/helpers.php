<?php
/**
 * API Helper Functions
 * Common utility functions used across multiple API endpoints
 * Reduces code duplication and improves maintainability
 */

/**
 * Get company ID from user session
 * Handles multiple lookup strategies:
 * 1. Direct match by user_id in company table
 * 2. Email matching via user table
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID from session
 * @return array|null Company data (company_id, email, name) or null
 */
function getCompanyByUserId($pdo, $userId) {
    // Strategy 1: Direct user_id match
    $stmt = $pdo->prepare("SELECT company_id, email, name FROM company WHERE company_id = :user_id LIMIT 1");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($company) {
        return $company;
    }
    
    // Strategy 2: Email matching via user table
    $userStmt = $pdo->prepare("SELECT email FROM user WHERE user_id = :user_id LIMIT 1");
    $userStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $userStmt->execute();
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userData && $userData['email']) {
        $companyStmt = $pdo->prepare("SELECT company_id, email, name FROM company WHERE email = :email LIMIT 1");
        $companyStmt->bindParam(':email', $userData['email'], PDO::PARAM_STR);
        $companyStmt->execute();
        return $companyStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    return null;
}

/**
 * Send JSON response with proper headers
 * Standardizes API response format
 * 
 * @param array $data Response data
 * @param int $statusCode HTTP status code (default: 200)
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 * Standardizes error responses across all APIs
 * 
 * @param string $message Error message
 * @param int $statusCode HTTP status code (default: 400)
 */
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse([
        'success' => false,
        'error' => $message
    ], $statusCode);
}

/**
 * Send success response
 * Standardizes success responses across all APIs
 * 
 * @param mixed $data Response data
 * @param string $message Optional success message
 */
function sendSuccessResponse($data, $message = null) {
    $response = ['success' => true, 'data' => $data];
    if ($message) {
        $response['message'] = $message;
    }
    sendJsonResponse($response);
}

/**
 * Validate required fields in input data
 * Returns array of missing fields or empty array if all present
 * 
 * @param array $data Input data to validate
 * @param array $requiredFields List of required field names
 * @return array Missing field names
 */
function validateRequiredFields($data, $requiredFields) {
    $missing = [];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Check if user is authenticated
 * Returns true if session has user_id, false otherwise
 * 
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require authentication or send 401 error
 * Call this at the start of protected API endpoints
 */
function requireAuth() {
    if (!isAuthenticated()) {
        sendErrorResponse('Authentication required', 401);
    }
}
