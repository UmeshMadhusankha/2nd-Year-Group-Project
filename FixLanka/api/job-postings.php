<?php
/**
 * Job Postings API Endpoint
 * Handles CRUD operations for Company Job Postings
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/JobPostingModel.php';

// Get database connection
global $pdo;

// Initialize model
$jobPostingModel = new JobPostingModel($pdo);

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGet($jobPostingModel, $action);
            break;
            
        case 'POST':
            handlePost($jobPostingModel, $action);
            break;
            
        case 'PUT':
            handlePut($jobPostingModel, $action);
            break;
            
        case 'DELETE':
            handleDelete($jobPostingModel);
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

/**
 * Handle GET requests
 */
function handleGet($model, $action) {
    switch ($action) {
        case 'list':
            // Get all job postings for a company
            $companyId = $_GET['company_id'] ?? null;
            $status = $_GET['status'] ?? null;
            
            if (!$companyId) {
                throw new Exception('Company ID is required');
            }
            
            $postings = $model->getByCompany($companyId, $status);
            
            // Add application counts
            foreach ($postings as &$posting) {
                $posting['application_count'] = $model->getApplicationCount($posting['posting_id']);
            }
            
            echo json_encode([
                'success' => true,
                'postings' => $postings
            ]);
            break;
            
        case 'get':
            // Get a single job posting
            $postingId = $_GET['posting_id'] ?? null;
            
            if (!$postingId) {
                throw new Exception('Posting ID is required');
            }
            
            $posting = $model->getById($postingId);
            
            if (!$posting) {
                throw new Exception('Job posting not found');
            }
            
            $posting['application_count'] = $model->getApplicationCount($postingId);
            
            echo json_encode([
                'success' => true,
                'posting' => $posting
            ]);
            break;
            
        case 'applications':
            // Get application count for a specific posting
            $postingId = $_GET['posting_id'] ?? null;
            
            if (!$postingId) {
                throw new Exception('Posting ID is required');
            }
            
            $count = $model->getApplicationCount($postingId);
            
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            break;
            
        case 'stats':
            // Get statistics
            $companyId = $_GET['company_id'] ?? null;
            
            if (!$companyId) {
                throw new Exception('Company ID is required');
            }
            
            $stats = $model->getStatistics($companyId);
            
            echo json_encode([
                'success' => true,
                'statistics' => $stats
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
}

/**
 * Handle POST requests (Create)
 */
function handlePost($model, $action) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    // Validate required fields
    $required = ['company_id', 'title', 'category', 'employment_type', 'description', 
                 'min_experience', 'min_budget', 'max_budget', 'location'];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    // Validate budget range
    if ($data['min_budget'] > $data['max_budget']) {
        throw new Exception('Minimum budget cannot exceed maximum budget');
    }
    
    // Create the job posting
    $postingId = $model->create($data);
    
    if (!$postingId) {
        throw new Exception('Failed to create job posting');
    }
    
    // Fetch the created posting
    $posting = $model->getById($postingId);
    
    echo json_encode([
        'success' => true,
        'message' => 'Job posting created successfully',
        'posting_id' => $postingId,
        'posting' => $posting
    ]);
}

/**
 * Handle PUT requests (Update)
 */
function handlePut($model, $action) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    $postingId = $data['posting_id'] ?? null;
    
    if (!$postingId) {
        throw new Exception('Posting ID is required');
    }
    
    switch ($action) {
        case 'status':
            // Update only the status
            $status = $data['status'] ?? null;
            
            if (!$status) {
                throw new Exception('Status is required');
            }
            
            $validStatuses = ['draft', 'open', 'closed', 'filled'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Invalid status value');
            }
            
            $success = $model->updateStatus($postingId, $status);
            
            echo json_encode([
                'success' => $success,
                'message' => 'Job posting status updated successfully'
            ]);
            break;
            
        default:
            // Update full posting
            $required = ['title', 'category', 'employment_type', 'description', 
                         'min_experience', 'min_budget', 'max_budget', 'location'];
            
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }
            
            // Validate budget range
            if ($data['min_budget'] > $data['max_budget']) {
                throw new Exception('Minimum budget cannot exceed maximum budget');
            }
            
            $success = $model->update($postingId, $data);
            
            // Fetch updated posting
            $posting = $model->getById($postingId);
            
            echo json_encode([
                'success' => $success,
                'message' => 'Job posting updated successfully',
                'posting' => $posting
            ]);
            break;
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($model) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postingId = $data['posting_id'] ?? $_GET['posting_id'] ?? null;
    
    if (!$postingId) {
        throw new Exception('Posting ID is required');
    }
    
    $success = $model->delete($postingId);
    
    echo json_encode([
        'success' => $success,
        'message' => 'Job posting deleted successfully'
    ]);
}
