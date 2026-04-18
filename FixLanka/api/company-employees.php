<?php
/**
 * Company Employees API
 * 
 * RESTful API for managing company employees
 * 
 * @package FixLanka\API
 * @version 1.0.0
 */

// Ensure this endpoint never emits HTML/PHP notices into the response body.
// Frontend fetch handlers expect valid JSON.
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');

ob_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/CompanyEmployeeModel.php';

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Get logged-in company ID
$companyId = $_SESSION['user_id'];

// Initialize model with database connection
$employeeModel = new CompanyEmployeeModel($pdo);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

function respondJson($payload, int $statusCode = 200): void {
    http_response_code($statusCode);

    // Drop any prior output (warnings, whitespace) to keep JSON parseable.
    if (ob_get_length() !== false) {
        ob_clean();
    }

    echo json_encode($payload);
    exit;
}

// Handle different HTTP methods
switch ($method) {
    case 'GET':
        handleGet($employeeModel);
        break;
    case 'POST':
        handlePost($employeeModel);
        break;
    case 'PUT':
        handlePut($employeeModel);
        break;
    case 'DELETE':
        handleDelete($employeeModel);
        break;
    default:
        respondJson(['error' => 'Method not allowed'], 405);
        break;
}

/**
 * Handle GET requests
 */
function handleGet($model) {
    // Get staff availability (staffsummary minus allocations to active projects)
    if (isset($_GET['action']) && $_GET['action'] === 'availability') {
        if (!isset($_GET['company_id'])) {
            respondJson(['error' => 'Company ID is required'], 400);
            return;
        }

        $companyId = (int)$_GET['company_id'];
        $availability = $model->getStaffAvailability($companyId);
        respondJson(['success' => true, 'data' => $availability]);
        return;
    }

    // Get statistics
    if (isset($_GET['action']) && $_GET['action'] === 'stats') {
        if (!isset($_GET['company_id'])) {
            respondJson(['error' => 'Company ID is required'], 400);
            return;
        }

        $filters = [];
        if (isset($_GET['employment_type'])) {
            $filters['employment_type'] = $_GET['employment_type'];
        }

        $stats = $model->getStatistics($_GET['company_id'], $filters);
        respondJson($stats);
        return;
    }
    
    // Get single employee
    if (isset($_GET['employee_id'])) {
        $employee = $model->getById($_GET['employee_id']);
        if ($employee) {
            respondJson($employee);
        } else {
            respondJson(['error' => 'Employee not found'], 404);
        }
        return;
    }
    
    // Get all employees with filters
    if (!isset($_GET['company_id'])) {
        respondJson(['error' => 'Company ID is required'], 400);
        return;
    }
    
    $filters = [
        'specialty' => $_GET['specialty'] ?? null,
        'status' => $_GET['status'] ?? null,
        'search' => $_GET['search'] ?? null,
        'order_by' => $_GET['order_by'] ?? 'created_at',
        'order_dir' => $_GET['order_dir'] ?? 'DESC'
    ];

    if (isset($_GET['employment_type'])) {
        $filters['employment_type'] = $_GET['employment_type'];
    }
    
    $employees = $model->getAll($_GET['company_id'], $filters);
    respondJson($employees);
}

/**
 * Handle POST requests (Create)
 */
function handlePost($model) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        respondJson(['error' => 'Invalid JSON data'], 400);
        return;
    }

    // Offboard a system-hired freelancer (job-post/application recruit)
    if (isset($data['action']) && $data['action'] === 'offboard_freelancer') {
        if (!isset($data['company_id']) || !isset($data['repairer_id'])) {
            respondJson(['success' => false, 'message' => 'Company ID and repairer ID are required'], 400);
            return;
        }

        $reason = trim((string)($data['reason'] ?? ''));
        if ($reason === '') {
            respondJson(['success' => false, 'message' => 'Layoff reason is required'], 400);
            return;
        }

        $result = $model->offboardFreelancerByRepairer((int)$data['company_id'], (int)$data['repairer_id'], $reason);
        if (!empty($result['success'])) {
            respondJson($result, 200);
            return;
        }

        respondJson($result, 400);
        return;
    }

    // Reduce staff counts (staffsummary) by specialty.
    // Used by Workforce "Reduce Staff" drawer where staff are managed as counts.
    if (isset($data['action']) && $data['action'] === 'reduce_staff') {
        if (!isset($data['company_id']) || !isset($data['reductions']) || !is_array($data['reductions'])) {
            respondJson(['success' => false, 'message' => 'Company ID and reductions array required'], 400);
        }

        try {
            $result = $model->reduceStaffSummary($data['company_id'], $data['reductions']);
            respondJson($result);
        } catch (Throwable $e) {
            respondJson(['success' => false, 'message' => 'Failed to reduce staff: ' . $e->getMessage()], 500);
        }
    }
    
    // Bulk add
    if (isset($data['bulk']) && $data['bulk'] === true) {
        if (!isset($data['company_id']) || !isset($data['employees'])) {
            respondJson(['error' => 'Company ID and employees array required'], 400);
            return;
        }

        if (!is_array($data['employees'])) {
            respondJson(['error' => 'Employees must be an array'], 400);
        }

        // Heuristic:
        // - If payload includes repairer_id, treat as roster bulk-add to company_employees.
        // - Else if payload includes specialty, treat as staff-summary bulk-add to staffsummary.
        $first = null;
        foreach ($data['employees'] as $emp) {
            if (is_array($emp)) {
                $first = $emp;
                break;
            }
        }

        try {
            if ($first && array_key_exists('repairer_id', $first)) {
                $result = $model->bulkAdd($data['company_id'], $data['employees']);
                respondJson($result);
            }

            if ($first && array_key_exists('specialty', $first)) {
                $result = $model->bulkAddStaffSummary($data['company_id'], $data['employees']);
                respondJson($result);
            }

            respondJson([
                'success' => false,
                'message' => 'Invalid bulk payload. Provide either repairer_id (roster) or specialty (staff summary).'
            ], 400);
        } catch (Throwable $e) {
            respondJson(['success' => false, 'message' => 'Bulk add failed: ' . $e->getMessage()], 500);
        }
    }
    
    // Validate required fields
    $required = ['company_id', 'repairer_id'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            respondJson(['error' => "Field '{$field}' is required"], 400);
            return;
        }
    }
    
    $result = $model->create($data);
    
    if ($result['success']) {
        respondJson($result, 201);
    } else {
        respondJson($result, 500);
    }
}

/**
 * Handle PUT requests (Update)
 */
function handlePut($model) {
    if (!isset($_GET['employee_id'])) {
        respondJson(['error' => 'Employee ID is required'], 400);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        respondJson(['error' => 'Invalid JSON data'], 400);
        return;
    }
    
    // Update status only
    if (isset($_GET['action']) && $_GET['action'] === 'status') {
        if (!isset($data['status'])) {
            respondJson(['error' => 'Status is required'], 400);
            return;
        }
        
        $result = $model->updateStatus($_GET['employee_id'], $data['status']);
        respondJson($result);
        return;
    }
    
    // Full update
    $required = ['job_title'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            respondJson(['error' => "Field '{$field}' is required"], 400);
            return;
        }
    }
    
    $result = $model->update($_GET['employee_id'], $data);
    respondJson($result);
}

/**
 * Handle DELETE requests
 */
function handleDelete($model) {
    if (!isset($_GET['employee_id'])) {
        respondJson(['error' => 'Employee ID is required'], 400);
        return;
    }
    
    $result = $model->delete($_GET['employee_id']);
    respondJson($result);
}
