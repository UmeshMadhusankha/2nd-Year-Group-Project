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
                    r.f_name as first_name,
                    r.l_name as last_name,
                    u.email,
                    u.contact_no as phone,
                    c.name as specialty,
                    r.experience_years,
                    r.hourly_rate,
                    r.average_rating as rating,
                    jp.title as job_title
                FROM repairer_applications ra
                LEFT JOIN Repairer r ON ra.repairer_id = r.repairer_id
                LEFT JOIN Category c ON r.category_id = c.category_id
                LEFT JOIN User u ON r.user_id = u.user_id
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
                    r.f_name as first_name,
                    r.l_name as last_name,
                    u.email,
                    u.contact_no as phone,
                    c.name as specialty,
                    r.experience_years,
                    r.hourly_rate,
                    r.average_rating as rating,
                    u.profile_pic as profile_photo,
                    jp.title as job_title,
                    jp.description as job_description
                FROM repairer_applications ra
                LEFT JOIN Repairer r ON ra.repairer_id = r.repairer_id
                LEFT JOIN Category c ON r.category_id = c.category_id
                LEFT JOIN User u ON r.user_id = u.user_id
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
        $db->beginTransaction();

        $sql = "UPDATE repairer_applications SET status = 'approved' WHERE application_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);

        // Automated Onboarding Feature
        // 1. Get application details
        $sql = "SELECT jp.company_id, ra.repairer_id, jp.employment_type, ra.expected_rate
                FROM repairer_applications ra
                JOIN job_postings jp ON ra.job_posting_id = jp.posting_id
                WHERE ra.application_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);
        $appDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appDetails) {
            // 2. Insert into company_employees
            $sql = "INSERT INTO company_employees (
                        company_id, repairer_id, job_title, employment_type,
                        status, hired_date, hourly_rate
                    ) VALUES (
                        ?, ?, 'Freelancer', ?,
                        'active', CURDATE(), ?
                    ) ON DUPLICATE KEY UPDATE status = 'active'";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $appDetails['company_id'],
                $appDetails['repairer_id'],
                $appDetails['employment_type'],
                $appDetails['expected_rate']
            ]);
        }

        $db->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Application approved successfully'
        ]);
        
    } catch (PDOException $e) {
        $db->rollBack();
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
