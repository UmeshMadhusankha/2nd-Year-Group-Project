<?php
/**
 * Freelancers API
 * Handles listing available freelancers that a company can hire/assign jobs to.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    global $pdo;
    $db = $pdo;

    $company_id = isset($_GET['company_id']) ? intval($_GET['company_id']) : 0;
    
    if (!$company_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Company ID is required']);
        exit();
    }

    // A freelancer is an active repairer not currently fully employed (or simply anyone available to contract)
    // We will list all Repairers. You can add logic to filter by those in `company_employees` later.
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $specialty = isset($_GET['specialty']) ? $_GET['specialty'] : '';
    
    $sql = "SELECT 
                r.repairer_id,
                r.f_name as first_name,
                r.l_name as last_name,
                u.email,
                u.contact_no as phone,
                c.name as specialty,
                r.experience_years,
                r.hourly_rate,
                r.average_rating as rating,
                u.profile_pic as profile_photo,
                
                -- Check if they are already hired by this company
                CASE WHEN ce.employee_id IS NOT NULL THEN 1 ELSE 0 END as is_hired,
                ce.status as employment_status
                
            FROM Repairer r
            JOIN User u ON r.user_id = u.user_id
            LEFT JOIN Category c ON r.category_id = c.category_id
            LEFT JOIN company_employees ce ON r.repairer_id = ce.repairer_id AND ce.company_id = :company_id
            WHERE u.status = 'active'";
            
    $params = [':company_id' => $company_id];

    if ($search) {
        $sql .= " AND (r.f_name LIKE :search OR r.l_name LIKE :search OR c.name LIKE :search)";
        $params[':search'] = "%{$search}%";
    }

    if ($specialty) {
        $sql .= " AND c.name = :specialty";
        $params[':specialty'] = $specialty;
    }
    
    $sql .= " ORDER BY is_hired DESC, r.average_rating DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $freelancers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'freelancers' => $freelancers,
        'count' => count($freelancers)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
