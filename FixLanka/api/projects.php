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
require_once '../models/PaymentModel.php';

// Initialize project model with database connection
$projectModel = new Project($pdo);
$paymentModel = new PaymentModel($pdo); // Initialize PaymentModel

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

    // Get timeline
    if ($action === 'timeline' && isset($_GET['project_id'])) {
        $projectId = intval($_GET['project_id']);
        $result = $projectModel->getTimeline($projectId);
        echo json_encode($result);
        return;
    }

    // Get phases (timeline table)
    if ($action === 'phases' && isset($_GET['project_id'])) {
        $projectId = intval($_GET['project_id']);
        $result = $projectModel->getContractPhases($projectId);
        echo json_encode($result);
        return;
    }

    // Get financials
    if ($action === 'financials' && isset($_GET['project_id'])) {
        global $paymentModel;
        $projectId = intval($_GET['project_id']);
        $result = $projectModel->getProjectFinancials($projectId);
        
        // Include payment history if contract exists
        if ($result['success'] && $result['data']['has_contract']) {
            $contractId = $result['data']['contract_id'];
            $payments = $paymentModel->getByContractId($contractId);
            $result['data']['payments'] = $payments ?: [];
        }
        
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

    // Check for specific actions in the URL query string
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    // Start Phase
    if ($action === 'start_phase' && isset($_POST['milestone_id'])) {
        $milestoneId = intval($_POST['milestone_id']);
        $result = $projectModel->startPhase($milestoneId);
        echo json_encode($result);
        return;
    }

    // Complete Phase (Submit Proof)
    if ($action === 'complete_phase' && isset($_POST['milestone_id'])) {
        $milestoneId = intval($_POST['milestone_id']);
        $description = $_POST['description'] ?? '';
        $actualQuantity = $_POST['actual_quantity'] ?? null;
        $actualUnitRate = $_POST['actual_unit_rate'] ?? null;
        $files = $_FILES['proof_files'] ?? [];
        
        $result = $projectModel->submitPhaseProof($milestoneId, $description, $files, $actualQuantity, $actualUnitRate);
        echo json_encode($result);
        return;
    }

    // Verify Phase (Approve/Reject)
    if ($action === 'verify_phase' && isset($_POST['milestone_id']) && isset($_POST['action'])) {
        $milestoneId = intval($_POST['milestone_id']);
        $verifyAction = $_POST['action'];
        $feedback = $_POST['feedback'] ?? '';
        
        $result = $projectModel->verifyPhase($milestoneId, $verifyAction, $feedback);
        echo json_encode($result);
        return;
    }

    // Start Project from Accepted Contract
    if ($action === 'start_from_contract') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['contract_id'])) {
            $contractId = intval($input['contract_id']);

            $employeeIds = $input['employee_ids'] ?? [];
            if (!is_array($employeeIds)) {
                $employeeIds = [];
            }
            $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds), fn($v) => $v > 0)));

            $freelancerAssignmentIds = $input['freelancer_assignment_ids'] ?? [];
            if (!is_array($freelancerAssignmentIds)) {
                $freelancerAssignmentIds = [];
            }
            $freelancerAssignmentIds = array_values(array_unique(array_filter(array_map('intval', $freelancerAssignmentIds), fn($v) => $v > 0)));

            $staffRequirements = $input['staff_requirements'] ?? [];
            if (!is_array($staffRequirements)) {
                $staffRequirements = [];
            }

            $result = $projectModel->startFromContract($contractId, $employeeIds, $freelancerAssignmentIds, $staffRequirements);
            echo json_encode($result);
            return;
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing contract_id']);
            return;
        }
    }

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
