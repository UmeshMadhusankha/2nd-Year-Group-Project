<?php
/**
 * Advertisement API
 * Handles fetching and managing company advertisements
 * Provides CRUD operations for company advertisement management
 * 
 * @author FixLanka Team
 * @return JSON response with advertisements data
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once 'helpers.php';

header('Content-Type: application/json');

// Get request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route to appropriate handler based on method
if ($requestMethod === 'POST') {
    handleCreateAdvertisement();
} elseif ($requestMethod === 'DELETE') {
    handleDeleteAdvertisement();
} else {
    // Default GET request - list advertisements
    handleListAdvertisements();
}

/**
 * Handle GET request - List all advertisements for company
 */
function handleListAdvertisements() {
    global $pdo;

try {
    // Require authentication - will send 401 if not logged in
    requireAuth();
    
    $userId = $_SESSION['user_id'];
    
    // Get company ID from session or database
    $companyId = $_SESSION['company_id'] ?? null;
    
    // Use helper function to lookup company if not in session
    if (!$companyId) {
        $companyData = getCompanyByUserId($pdo, $userId);
        
        if (!$companyData) {
            // Return empty data gracefully - company profile may not be completed yet
            sendSuccessResponse([
                'data' => [],
                'counts' => [
                    'all' => 0,
                    'active' => 0,
                    'pending' => 0,
                    'scheduled' => 0,
                    'paused' => 0,
                    'expired' => 0
                ],
                'total' => 0,
                'message' => 'No company profile associated with this account'
            ]);
        }
        
        $companyId = $companyData['company_id'];
    }

    // Fetch advertisements from database
    // Uses computed status based on dates and current status
    $query = "SELECT 
                ad_id as id,
                provider_id,
                provider_type,
                title,
                description,
                category,
                image_url,
                type,
                budget,
                start_date,
                end_date,
                view_count as impressions,
                click_count as clicks,
                -- Calculate click-through rate (CTR)
                CASE 
                    WHEN click_count > 0 AND view_count > 0 
                    THEN ROUND((click_count / view_count) * 100, 2)
                    ELSE 0.00
                END as click_through_rate,
                0.00 as budget_spent,
                status,
                submission_date as created_at,
                updated_at,
                -- Compute dynamic status based on dates
                CASE 
                    WHEN start_date > CURDATE() THEN 'scheduled'
                    WHEN end_date < CURDATE() THEN 'expired'
                    WHEN status = 'active' THEN 'active'
                    WHEN status = 'pending' THEN 'pending'
                    WHEN status = 'rejected' THEN 'paused'
                    ELSE 'pending'
                END as computed_status
              FROM advertisement 
              WHERE provider_id = :company_id 
                AND provider_type = 'company'
              ORDER BY submission_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
    $stmt->execute();
    
    $advertisements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate counts for each status type
    $counts = [
        'all' => count($advertisements),
        'active' => 0,
        'pending' => 0,
        'scheduled' => 0,
        'paused' => 0,
        'expired' => 0
    ];

    foreach ($advertisements as $ad) {
        $status = $ad['computed_status'];
        if (isset($counts[$status])) {
            $counts[$status]++;
        }
    }

    // Send successful response with data and counts
    sendSuccessResponse([
        'data' => $advertisements,
        'counts' => $counts,
        'total' => count($advertisements)
    ]);

} catch (PDOException $e) {
    // Database-specific error handling
    error_log("Database error in advertisements API: " . $e->getMessage());
    sendErrorResponse('Database error occurred', 500);
} catch (Exception $e) {
    // General error handling
    error_log("Error in advertisements API: " . $e->getMessage());
    sendErrorResponse('An error occurred', 500);
}
}

/**
 * Handle POST request - Create new advertisement
 */
function handleCreateAdvertisement() {
    global $pdo;
    
    try {
        // Require authentication
        requireAuth();
        
        $userId = $_SESSION['user_id'];
        
        // Get company ID
        $companyId = $_SESSION['company_id'] ?? null;
        if (!$companyId) {
            $companyData = getCompanyByUserId($pdo, $userId);
            if (!$companyData) {
                sendErrorResponse('Company profile not found', 404);
                return;
            }
            $companyId = $companyData['company_id'];
        }
        
        // Get form data
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        $type = $_POST['type'] ?? 'banner';
        $targetUrl = $_POST['target_url'] ?? '';
        $targetAudience = $_POST['target_audience'] ?? 'all';
        $linkText = $_POST['link_text'] ?? '';
        $scheduleType = $_POST['schedule_type'] ?? 'immediate';
        $startDate = $_POST['start_date'] ?? date('Y-m-d H:i:s');
        $endDate = $_POST['end_date'] ?? null;
        $duration = $_POST['duration'] ?? 30;
        $priorityPlacement = isset($_POST['priority_placement']) ? 1 : 0;
        $paymentMethod = $_POST['payment_method'] ?? '';
        
        // Validate required fields
        if (empty($title) || empty($description) || empty($category)) {
            sendErrorResponse('Title, description, and category are required', 400);
            return;
        }
        
        // Handle file upload
        $imageUrl = null;
        if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/advertisements/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['media_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $targetPath)) {
                $imageUrl = '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/' . $fileName;
            }
        }
        
        // Calculate budget based on duration and pricing
        $baseRate = 500;
        $videoRate = ($type === 'video') ? 300 : 0;
        $priorityRate = $priorityPlacement ? 200 : 0;
        $budget = ($baseRate + $videoRate + $priorityRate) * intval($duration);
        
        // If schedule type is scheduled, use provided dates
        if ($scheduleType === 'scheduled' && !empty($endDate)) {
            // Use provided dates
        } else {
            // Calculate end date from duration
            $endDate = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
        }
        
        // Insert into database
        $query = "INSERT INTO advertisement (
                    provider_id, provider_type, title, description, category, 
                    image_url, type, budget, priority, target_url, 
                    start_date, end_date, status, submission_date
                  ) VALUES (
                    :provider_id, 'company', :title, :description, :category,
                    :image_url, :type, :budget, :priority, :target_url,
                    :start_date, :end_date, 'pending', NOW()
                  )";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':provider_id' => $companyId,
            ':title' => $title,
            ':description' => $description,
            ':category' => $category,
            ':image_url' => $imageUrl,
            ':type' => $type,
            ':budget' => $budget,
            ':priority' => $priorityPlacement,
            ':target_url' => $targetUrl,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        
        $adId = $pdo->lastInsertId();
        
        // Send success response
        sendSuccessResponse([
            'message' => 'Advertisement created successfully and submitted for review',
            'ad_id' => $adId,
            'status' => 'pending'
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error creating advertisement: " . $e->getMessage());
        sendErrorResponse('Failed to create advertisement', 500);
    } catch (Exception $e) {
        error_log("Error creating advertisement: " . $e->getMessage());
        sendErrorResponse('An error occurred', 500);
    }
}

/**
 * Handle DELETE request - Delete advertisement
 */
function handleDeleteAdvertisement() {
    global $pdo;
    
    try {
        requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        $adId = $data['ad_id'] ?? null;
        
        if (!$adId) {
            sendErrorResponse('Advertisement ID is required', 400);
            return;
        }
        
        // Delete advertisement
        $query = "DELETE FROM advertisement WHERE ad_id = :ad_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':ad_id' => $adId]);
        
        sendSuccessResponse(['message' => 'Advertisement deleted successfully']);
        
    } catch (Exception $e) {
        error_log("Error deleting advertisement: " . $e->getMessage());
        sendErrorResponse('Failed to delete advertisement', 500);
    }
}
