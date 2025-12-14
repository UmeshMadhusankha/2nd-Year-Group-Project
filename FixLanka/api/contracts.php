<?php
// API endpoint for contract operations
// Routes requests to ContractController

error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

error_log("[API] Contract API called");
error_log("[API] REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
error_log("[API] REQUEST_URI: " . $_SERVER['REQUEST_URI']);

session_start();

require_once __DIR__ . '/../controllers/ContractController.php';

header('Content-Type: application/json');

// Get the action from request
$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log("[API] Action: " . $action);

try {
    $controller = new ContractController();
} catch (Exception $e) {
    error_log("[API ERROR] Failed to create controller: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}

switch ($action) {
    case 'list':
    case 'getAll':
        $controller->getAllContracts();
        break;
    
    case 'get':
        $controller->getContract();
        break;
    
    case 'stats':
        $controller->getStats();
        break;
    
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->createContract();
        break;
    
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->updateContract();
        break;
    
    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        $controller->deleteContract();
        break;
    
    case 'filterByStatus':
        $controller->filterByStatus();
        break;
    
    case 'getAcceptedProjects':
        $controller->getAcceptedProjects();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}
