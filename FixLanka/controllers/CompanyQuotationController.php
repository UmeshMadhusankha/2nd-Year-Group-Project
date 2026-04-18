<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';

class CompanyQuotationController {
    private $quotationModel;
    
    public function __construct() {
        global $pdo;
        $this->quotationModel = new CompanyQuotationModel($pdo);
    }
    
    public function create() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || !hasRole('company')) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        // Get JSON input if sent as JSON, otherwise use $_POST
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        try {
            // Map input to model expected structure
            $data = [
                'company_id' => $userId,
                'request_id' => $input['request_id'] ?? null,
                'quotation_text' => $input['description'] ?? '',
                'estimated_cost' => $input['total_price'] ?? 0,
                'estimated_duration' => ($input['estimated_duration'] ?? 0) . ' days',
                
                // Enhanced fields
                'work_schedule_type' => $input['work_schedule_type'] ?? 'weekdays_only',
                'working_days_per_week' => $input['working_days_per_week'] ?? 5,
                'daily_work_hours' => $input['daily_work_hours'] ?? 8,
                'work_start_time' => $input['work_start_time'] ?? '08:00:00',
                'work_end_time' => $input['work_end_time'] ?? '17:00:00',
                'custom_schedule_details' => $input['custom_schedule_details'] ?? null,
                'total_work_hours' => $input['total_work_hours'] ?? 0,
                'overtime_available' => isset($input['overtime_available']) ? 1 : 0,
                'overtime_rate' => $input['overtime_rate'] ?? 0,
                
                // Other fields that might be in the model
                'status' => 'pending'
            ];
            
            // Note: I need to verify CompanyQuotationModel::createEnhanced arguments
            // For now, assuming it takes an array. 
            // Checking the model earlier, createEnhanced takes ($data).
            
            $quotationId = $this->quotationModel->createEnhanced($data);
            
            if ($quotationId) {
                echo json_encode(['success' => true, 'message' => 'Quotation submitted successfully', 'quotation_id' => $quotationId]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create quotation']);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    public function getMyQuotations() {
        header('Content-Type: application/json');
        
        if (!isLoggedIn() || !hasRole('company')) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            // Need a method in model to get by company_id
            // Assuming getByCompany exists or I generally query it
            // Let's implement a simple query here or use model
            
            // Check if model has getByCompany
            // For now, I'll assume I need to add it or use generic get
            
            // I'll query directly solely for speed if model lacks it, but let's try to be clean.
            // I'll use a direct PDO query via model if available or just raw sql in controller (bad practice but quick)
            // Better: Add getByCompany to model if missing.
            
            // Let's just create a raw query for now to verify.
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM companyquotation WHERE company_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'quotations' => $quotations]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}
