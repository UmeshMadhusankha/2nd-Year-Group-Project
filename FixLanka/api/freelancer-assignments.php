<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access', 'errorCode' => 'UNAUTHORIZED']);
    exit;
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'] ?? '';

// Only companies can send job offers for now
if ($userType !== 'company') {
    echo json_encode(['success' => false, 'error' => 'Only companies can assign jobs.', 'errorCode' => 'FORBIDDEN']);
    exit;
}

// Get company ID
$db = new Database();
$conn = $db->getConnection();

$companyQuery = "SELECT company_id FROM company WHERE user_id = ?";
$stmt = $conn->prepare($companyQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Company profile not found.']);
    exit;
}
$companyId = $result->fetch_assoc()['company_id'];
$stmt->close();

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

switch ($action) {
    case 'assign_job':
        assignJob($conn, $companyId);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action.']);
        break;
}

function assignJob($conn, $companyId) {
    // Basic validation
    $repairerId = $_POST['repairer_id'] ?? null;
    $projectId = $_POST['project_id'] ?? null; // For now maps to job_id in frontend UI? 
    // Actually the UI passes "jobSelect" which we will map to project_id or something similar.
    // For now we will allow project_id to be NULL or an ID depending on what's passed.
    if ($projectId === '') $projectId = null;

    $startDate = $_POST['start_date'] ?? null;
    $deadlineDate = $_POST['deadline_date'] ?? null;
    $pricingModel = $_POST['pricing_model'] ?? 'hourly';
    $rateOrPrice = $_POST['rate_or_price'] ?? null;
    $estimatedHours = $_POST['estimated_hours'] ?? null;
    $notes = $_POST['notes'] ?? '';

    if (!$repairerId || !$startDate || !$deadlineDate || !$pricingModel || !$rateOrPrice) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
        return;
    }

    if ($pricingModel === 'fixed') {
        $estimatedHours = null; // No estimated hours for fixed price
    }

    try {
        $conn->begin_transaction();

        $query = "INSERT INTO freelancer_assignments 
                 (company_id, repairer_id, project_id, pricing_model, rate_or_price, estimated_hours, start_date, deadline_date, notes, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'offered')";
                 
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiissddss", 
            $companyId, 
            $repairerId, 
            $projectId, 
            $pricingModel, 
            $rateOrPrice, 
            $estimatedHours, 
            $startDate, 
            $deadlineDate, 
            $notes
        );
        
        $stmt->execute();
        $assignmentId = $conn->insert_id;
        
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Job offer sent successfully',
            'assignment_id' => $assignmentId
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => 'Failed to send job offer: ' . $e->getMessage()]);
    }
}
?>
