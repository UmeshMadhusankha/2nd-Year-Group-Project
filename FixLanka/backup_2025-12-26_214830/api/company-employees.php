<?php
/**
 * Company Employees API
 * 
 * RESTful API for managing company employees
 * 
 * @package FixLanka\API
 * @version 1.0.0
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../models/CompanyEmployeeModel.php';

// Initialize model with database connection
$employeeModel = new CompanyEmployeeModel($pdo);

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

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
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

/**
 * Handle GET requests
 */
function handleGet($model) {
    // Get statistics
    if (isset($_GET['action']) && $_GET['action'] === 'stats') {
        if (!isset($_GET['company_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Company ID is required']);
            return;
        }
        
        $stats = $model->getStatistics($_GET['company_id']);
        echo json_encode($stats);
        return;
    }
    
    // Get single employee
    if (isset($_GET['employee_id'])) {
        $employee = $model->getById($_GET['employee_id']);
        if ($employee) {
            echo json_encode($employee);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Employee not found']);
        }
        return;
    }
    
    // Get all employees with filters
    if (!isset($_GET['company_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Company ID is required']);
        return;
    }
    
    $filters = [
        'specialty' => $_GET['specialty'] ?? null,
        'status' => $_GET['status'] ?? null,
        'search' => $_GET['search'] ?? null,
        'order_by' => $_GET['order_by'] ?? 'created_at',
        'order_dir' => $_GET['order_dir'] ?? 'DESC'
    ];
    
    $employees = $model->getAll($_GET['company_id'], $filters);
    echo json_encode($employees);
}

/**
 * Handle POST requests (Create)
 */
function handlePost($model) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Bulk add
    if (isset($data['bulk']) && $data['bulk'] === true) {
        if (!isset($data['company_id']) || !isset($data['employees'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Company ID and employees array required']);
            return;
        }
        
        $result = $model->bulkAdd($data['company_id'], $data['employees']);
        echo json_encode($result);
        return;
    }
    
    // Validate required fields
    $required = ['company_id', 'first_name', 'last_name', 'specialty'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '{$field}' is required"]);
            return;
        }
    }
    
    $result = $model->create($data);
    
    if ($result['success']) {
        http_response_code(201);
    } else {
        http_response_code(500);
    }
    
    echo json_encode($result);
}

/**
 * Handle PUT requests (Update)
 */
function handlePut($model) {
    if (!isset($_GET['employee_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Employee ID is required']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        return;
    }
    
    // Update status only
    if (isset($_GET['action']) && $_GET['action'] === 'status') {
        if (!isset($data['status'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Status is required']);
            return;
        }
        
        $result = $model->updateStatus($_GET['employee_id'], $data['status']);
        echo json_encode($result);
        return;
    }
    
    // Full update
    $required = ['first_name', 'last_name', 'specialty'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '{$field}' is required"]);
            return;
        }
    }
    
    $result = $model->update($_GET['employee_id'], $data);
    echo json_encode($result);
}

/**
 * Handle DELETE requests
 */
function handleDelete($model) {
    if (!isset($_GET['employee_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Employee ID is required']);
        return;
    }
    
    $result = $model->delete($_GET['employee_id']);
    echo json_encode($result);
}
