<?php
/**
 * Repairer Applications API
 * Handles repairer job applications for companies
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

// Include required files
require_once '../config/database.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Use the global $pdo connection from database.php
    global $pdo;
    $db = $pdo;
    
    switch ($action) {
        case 'list':
            getApplications($db);
            break;
            
        case 'details':
            getApplicationDetails($db);
            break;
            
        case 'approve':
            approveApplication($db);
            break;
            
        case 'reject':
            rejectApplication($db);
            break;
            
        case 'stats':
            getApplicationStats($db);
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action parameter'
            ]);
            break;
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}

/**
 * Get list of applications for a company
 */
function getApplications($db) {
    $company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    
    if (!$company_id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Company ID required'
        ]);
        return;
    }
    
    try {
        // This is a placeholder - adjust based on your actual database schema
        $sql = "SELECT 
                    ra.application_id,
                    ra.job_posting_id,
                    ra.repairer_id,
                    ra.status,
                    ra.applied_date,
                    ra.cover_letter,
                    r.first_name,
                    r.last_name,
                    r.email,
                    r.phone,
                    r.specialty,
                    r.experience_years,
                    r.hourly_rate,
                    r.rating,
                    jp.title as job_title
                FROM repairer_applications ra
                LEFT JOIN repairers r ON ra.repairer_id = r.repairer_id
                LEFT JOIN job_postings jp ON ra.job_posting_id = jp.posting_id
                LEFT JOIN job_postings jp2 ON jp2.company_id = ?
                WHERE jp2.company_id = ?";
        
        $params = [$company_id, $company_id];
        
        if ($status !== 'all') {
            $sql .= " AND ra.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY ra.applied_date DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'applications' => $applications,
            'count' => count($applications)
        ]);
        
    } catch (PDOException $e) {
        // If tables don't exist, return empty array
        echo json_encode([
            'success' => true,
            'applications' => [],
            'count' => 0,
            'note' => 'Application tables may not exist yet'
        ]);
    }
}

/**
 * Get application details
 */
function getApplicationDetails($db) {
    $application_id = isset($_GET['application_id']) ? intval($_GET['application_id']) : 0;
    
    if (!$application_id) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Application ID required'
        ]);
        return;
    }
    
    try {
        $sql = "SELECT 
                    ra.*,
                    r.first_name,
                    r.last_name,
                    r.email,
                    r.phone,
                    r.specialty,
                    r.experience_years,
                    r.hourly_rate,
                    r.rating,
                    r.profile_photo,
                    jp.title as job_title,
                    jp.description as job_description
                FROM repairer_applications ra
                LEFT JOIN repairers r ON ra.repairer_id = r.repairer_id
                LEFT JOIN job_postings jp ON ra.job_posting_id = jp.posting_id
                WHERE ra.application_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($application) {
            echo json_encode([
                'success' => true,
                'application' => $application
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Application not found'
            ]);
        }
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage()
        ]);
    }
}

/**
 * Approve application
 */
function approveApplication($db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['application_id']) ? intval($data['application_id']) : 0;
    
    if (!$application_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Application ID required']);
        return;
    }
    
    try {
        $sql = "UPDATE repairer_applications SET status = 'approved' WHERE application_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Application approved successfully'
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to approve application: ' . $e->getMessage()
        ]);
    }
}

/**
 * Reject application
 */
function rejectApplication($db) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $application_id = isset($data['application_id']) ? intval($data['application_id']) : 0;
    $reason = isset($data['reason']) ? $data['reason'] : '';
    
    if (!$application_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Application ID required']);
        return;
    }
    
    try {
        $sql = "UPDATE repairer_applications 
                SET status = 'rejected', rejection_reason = ? 
                WHERE application_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$reason, $application_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Application rejected successfully'
        ]);
        
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to reject application: ' . $e->getMessage()
        ]);
    }
}

/**
 * Get application statistics
 */
function getApplicationStats($db) {
    $company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;
    
    if (!$company_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Company ID required']);
        return;
    }
    
    try {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN ra.status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN ra.status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN ra.status = 'rejected' THEN 1 ELSE 0 END) as rejected
                FROM repairer_applications ra
                LEFT JOIN job_postings jp ON ra.job_posting_id = jp.posting_id
                WHERE jp.company_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$company_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'stats' => $stats
        ]);
        
    } catch (PDOException $e) {
        // Return empty stats if tables don't exist
        echo json_encode([
            'success' => true,
            'stats' => [
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0
            ]
        ]);
    }
}
?>
