<?php
/**
 * Projects API
 * 
 * RESTful API endpoint for managing company projects.
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
require_once '../models/ProjectModel.php';

// Initialize project model with database connection
$projectModel = new Project($pdo);

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
 * Handle GET requests - Retrieve projects
 * 
 * Supports filtering by:
 * - project_id: Get specific project
 * - company_id: Get all projects for a company
 * - customer_id: Get all projects for a customer
 * - status: Filter by project status
 * - action=stats: Get project statistics
 * 
 * @return void Outputs JSON response
 */
function handleGet()
{
    global $projectModel;

    // Check for specific actions
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    // Get statistics
    if ($action === 'stats' && isset($_GET['company_id'])) {
        $companyId = intval($_GET['company_id']);
        $result = $projectModel->getStatistics($companyId);
        echo json_encode($result);
        return;
    }

    // Get single project by ID
    if (isset($_GET['project_id'])) {
        $projectId = intval($_GET['project_id']);
        $result = $projectModel->getById($projectId);
        echo json_encode($result);
        return;
    }

    // Get projects with optional filters
    $filters = [];
    
    if (isset($_GET['company_id'])) {
        $filters['company_id'] = intval($_GET['company_id']);
    }
    
    if (isset($_GET['customer_id'])) {
        $filters['customer_id'] = intval($_GET['customer_id']);
    }
    
    if (isset($_GET['status'])) {
        $filters['status'] = $_GET['status'];
    }
    
    if (isset($_GET['project_type'])) {
        $filters['project_type'] = $_GET['project_type'];
    }
    
    if (isset($_GET['search'])) {
        $filters['search'] = $_GET['search'];
    }

    $result = $projectModel->getAll($filters);
    echo json_encode($result);
}

/**
 * Handle POST requests - Create new project
 * 
 * Required fields:
 * - company_id: Company creating the project
 * - customer_id: Customer for whom project is being done
 * - title: Project title
 * - location: Project location
 * 
 * Optional fields:
 * - description, project_type, budget, start_date, end_date, attachment, status, progress
 * 
 * @return void Outputs JSON response
 */
function handlePost()
{
    global $projectModel;

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $requiredFields = ['company_id', 'customer_id', 'title', 'location'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        ]);
        return;
    }

    // Create the project
    $result = $projectModel->create($input);

    if ($result['success']) {
        http_response_code(201);
    } else {
        http_response_code(500);
    }

    echo json_encode($result);
}

/**
 * Handle PUT requests - Update existing project
 * 
 * Supports two types of updates:
 * 1. Full update: Update all project fields
 * 2. Partial update: Update only progress or status
 * 
 * Query parameters:
 * - project_id: Required - ID of project to update
 * - action: Optional - 'progress' or 'status' for partial updates
 * 
 * @return void Outputs JSON response
 */
function handlePut()
{
    global $projectModel;

    // Get project ID from query parameter
    if (!isset($_GET['project_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Project ID is required'
        ]);
        return;
    }

    $projectId = intval($_GET['project_id']);
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Handle specific actions
    if ($action === 'progress' && isset($input['progress'])) {
        $result = $projectModel->updateProgress($projectId, $input['progress']);
    } elseif ($action === 'status' && isset($input['status'])) {
        $result = $projectModel->updateStatus($projectId, $input['status']);
    } else {
        // Full update - validate required fields
        $requiredFields = ['title', 'location', 'status', 'progress'];
        $missingFields = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($input[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ]);
            return;
        }

        $result = $projectModel->update($projectId, $input);
    }

    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }

    echo json_encode($result);
}

/**
 * Handle DELETE requests - Delete project
 * 
 * Query parameters:
 * - project_id: Required - ID of project to delete
 * 
 * @return void Outputs JSON response
 */
function handleDelete()
{
    global $projectModel;

    // Get project ID from query parameter
    if (!isset($_GET['project_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Project ID is required'
        ]);
        return;
    }

    $projectId = intval($_GET['project_id']);

    // Delete the project
    $result = $projectModel->delete($projectId);

    if ($result['success']) {
        http_response_code(200);
    } else {
        http_response_code(500);
    }

    echo json_encode($result);
}
