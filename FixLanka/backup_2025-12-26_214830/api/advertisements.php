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
