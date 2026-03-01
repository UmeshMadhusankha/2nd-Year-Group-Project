<?php
// Test Script for Work Schedule Features
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CompanyQuotationModel.php';

echo "Starting Work Schedule Test...\n";

try {
    // 1. Setup Test Data
    // We need a valid user and job request. 
    // For simplicity, we'll INSERT dummy ones if they don't exist, or just fail if DB is empty.
    // Better: Create a temporary user and request.
    
    // Create Customer
    $pdo->exec("INSERT INTO User (email, password, f_name, l_name) VALUES ('test_customer@example.com', 'pass', 'Test', 'Customer') ON DUPLICATE KEY UPDATE user_id=LAST_INSERT_ID(user_id)");
    $customerId = $pdo->lastInsertId();
    
    // Create Company
    // Note: detailed fields might be required by DB constraints, check schema
    $pdo->exec("INSERT INTO Company (name, registration_no, email, password, address, contact_no) VALUES ('Test Company', 'REG123', 'test_company@example.com', 'pass', 'Company Address', '1234567890') ON DUPLICATE KEY UPDATE company_id=LAST_INSERT_ID(company_id)");
    $companyId = $pdo->lastInsertId();
    
    // NOTE: CompanyQuotation table has user_id and company_id.
    // Based on schema, user_id FK points to User. company_id FK points to Company.
    // Docblock said user_id is company submitting... but maybe it means the *User* account of the company?
    // But Company table is separate.
    // Maybe user_id in CompanyQuotation refers to the Customer?
    // Let's assume user_id = CustomerId and company_id = CompanyId.
    // Or maybe user_id refers to the company's User ID if they have one?
    // But Company table has email/password, so they ARE the user entity for companies.
    // Let's try setting user_id = customerId (since FK is to User table and customer is a User).
    // And if model requires user_id, we pass customerId.
    // We should also set company_id if the model supports it?
    // The createEnhanced method takes 'user_id' and maps it to :user_id in INSERT.
    // If the DB constraint requires user_id to be a valid User ID, then it MUST be the customer ID (since companies are in Company table).
    
    // So: user_id => $customerId
    
    // Create Job Request (assuming minimal fields)
    $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, 1, 'Test Job', 'Test Desc', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
    // Note: Category ID 1 might not exist. If fails, we might need to insert category. 
    // Let's assume Category 1 exists or handle error.
    try {
        $stmt->execute([$customerId]);
        $requestId = $pdo->lastInsertId();
    } catch (Exception $e) {
        // Try inserting category first
        $pdo->exec("INSERT INTO Category (name) VALUES ('Test Cat')"); // Removed image as it might not be in schema or nullable? Schema check needed but keeping simple.
        $catId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, finish_date, urgency, status) VALUES (?, ?, 'Test Job', 'Test Desc', 'Colombo', 'Address', 'company', NOW(), 'medium', 'pending')");
        $stmt->execute([$customerId, $catId]);
        $requestId = $pdo->lastInsertId();
    }
    
    echo "Test Data Created: CompanyID=$companyId, RequestID=$requestId\n";
    
    // 2. Test Create Enhanced with Schedule
    $model = new CompanyQuotation($pdo);
    $data = [
        'request_id' => $requestId,
        'user_id' => $customerId, // This maps to user_id column (Customer)
        // 'company_id' => $companyId, // The model create method does NOT seem to take company_id in the data array?
        // Wait, look at CompanyQuotationModel::create logic.
        // It inserts :user_id. It does NOT insert company_id in the SQL!
        // This suggests the model might be flawed or written for a different schema version?
        // OR user_id in CompanyQuotation IS the company ID, but that violates FK if Company IDs are different from User IDs?
        // Let's check if User and Company IDs overlap. They are auto-increment.
        // If I pass companyId as user_id, and if companyId=1 and UserId=1 exists, it works but refers to wrong entity.
        // IF the schema says user_id FK references User, then it MUST be a User ID.
        // If the Model uses it as Company ID, then it's a bug in the Project or I misunderstood.
        // BUT, for this test, I just need it to work.
        // Since I'm testing "Work Schedule", passing customerId as user_id is safe for DB constraint.
        'title' => 'Quotation with Schedule',
        'description' => 'Testing schedule fields',
        'labor_cost' => 5000,
        'material_cost' => 2000,
        'total_amount' => 7000,
        'start_date' => date('Y-m-d'),
        'completion_date' => date('Y-m-d', strtotime('+7 days')),
        'estimated_duration' => 7,
        'payment_method' => 'milestone',
        'budget_type' => 'fixed',
        
        // New Fields
        'work_schedule_type' => 'weekdays_only',
        'working_days_per_week' => 5,
        'daily_work_hours' => 8.0,
        'work_start_time' => '09:00:00',
        'work_end_time' => '17:00:00',
        'custom_schedule_details' => 'No work on public holidays',
        'overtime_available' => 1,
        'overtime_rate' => 1000.50
    ];
    
    $quoteId = $model->createEnhanced($data);
    
    if (!$quoteId) {
        throw new Exception("Failed to create quotation");
    }
    
    echo "Quotation Created: ID $quoteId\n";
    
    // 3. Verify Data
    $quote = $model->getEnhancedById($quoteId);
    
    if ($quote['work_schedule_type'] !== 'weekdays_only') throw new Exception("Mismatch: work_schedule_type");
    if ($quote['working_days_per_week'] != 5) throw new Exception("Mismatch: working_days_per_week");
    if ($quote['daily_work_hours'] != 8.0) throw new Exception("Mismatch: daily_work_hours");
    if ($quote['work_start_time'] !== '09:00:00') throw new Exception("Mismatch: work_start_time");
    if ($quote['overtime_available'] != 1) throw new Exception("Mismatch: overtime_available");
    
    echo "Verification Passed: Create\n";
    
    // 4. Test Update Enhanced
    $updateData = $data;
    $updateData['work_schedule_type'] = 'weekends_included';
    $updateData['working_days_per_week'] = 6;
    
    $result = $model->updateEnhanced($quoteId, $updateData);
    
    if (!$result) throw new Exception("Failed to update quotation");
    
    $updatedQuote = $model->getEnhancedById($quoteId);
    if ($updatedQuote['work_schedule_type'] !== 'weekends_included') throw new Exception("Mismatch after update: work_schedule_type");
    if ($updatedQuote['working_days_per_week'] != 6) throw new Exception("Mismatch after update: working_days_per_week");
    
    echo "Verification Passed: Update\n";
    
    // Cleanup
    $model->delete($quoteId);
    echo "Test Cleaned Up\n";
    echo "SUCCESS: All tests passed.\n";

} catch (Exception $e) {
    echo "TEST FAILED: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
