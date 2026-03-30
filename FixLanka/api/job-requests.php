<?php
/**
 * Job Requests API for Repairers
 * Handles fetching available job requests that repairers can bid on
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../config/database.php';
require_once '../models/JobRequestModel.php';

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle preflight requests
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow GET requests
if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

/**
 * Get available job requests for repairers
 */
function getAvailableJobs() {
    global $pdo;
    
    // Check if PDO connection exists
    if (!isset($pdo)) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database connection not established'
        ]);
        return;
    }
    
    try {
        // Get query parameters
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $district = isset($_GET['district']) ? $_GET['district'] : null;
        $urgency = isset($_GET['urgency']) ? $_GET['urgency'] : null;
        $service_provider_type = isset($_GET['service_provider_type']) ? $_GET['service_provider_type'] : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        $request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : null;
        
        // Base query - get pending job requests with related data
        // IMPORTANT: Filter out expired PUBLIC requests (finish_date < today)
        // This keeps the marketplace clean - companies don't need to see expired public opportunities
        $sql = "SELECT 
                    jr.request_id,
                    jr.user_id,
                    jr.category_id,
                    jr.title,
                    jr.description,
                    jr.status,
                    l.district,
                    l.address,
                    jr.service_provider_type,
                    jr.urgency,
                    jr.finish_date,
                    jr.dateCreated,
                    jr.photos,
                    c.name as category_name,
                    u.f_name as customer_first_name,
                    u.l_name as customer_last_name,
                    TIMESTAMPDIFF(HOUR, jr.dateCreated, NOW()) as hours_ago,
                    CASE 
                        WHEN jr.finish_date < CURDATE() THEN 1 
                        ELSE 0 
                    END as is_expired
                FROM JobRequest jr
                LEFT JOIN location l ON jr.location_id = l.location_id
                LEFT JOIN Category c ON jr.category_id = c.category_id
                LEFT JOIN User u ON jr.user_id = u.user_id
                WHERE jr.status = 'pending'
                AND jr.finish_date >= CURDATE()";
        
        $params = [];
        
        // Apply filters
        if ($request_id) {
            $sql .= " AND jr.request_id = ?";
            $params[] = $request_id;
        }
        
        if ($category) {
            $sql .= " AND c.name = ?";
            $params[] = $category;
        }
        
        if ($district) {
            $sql .= " AND l.district = ?";
            $params[] = $district;
        }
        
        if ($urgency) {
            $sql .= " AND jr.urgency = ?";
            $params[] = $urgency;
        }
        
        if ($service_provider_type) {
            // Service provider type can be 'individual', 'company', or 'both'
            if ($service_provider_type === 'individual') {
                $sql .= " AND (jr.service_provider_type = 'individual' OR jr.service_provider_type = 'both')";
            } elseif ($service_provider_type === 'company') {
                $sql .= " AND (jr.service_provider_type = 'company' OR jr.service_provider_type = 'both')";
            }
        }
        
        // Apply sorting
        switch ($sort) {
            case 'oldest':
                $sql .= " ORDER BY jr.dateCreated ASC";
                break;
            case 'urgency':
                $sql .= " ORDER BY FIELD(jr.urgency, 'urgent', 'medium'), jr.dateCreated DESC";
                break;
            case 'deadline':
                $sql .= " ORDER BY jr.finish_date ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY jr.dateCreated DESC";
                break;
        }
        
        // Execute query
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the response data
        foreach ($jobs as &$job) {
            // Format time ago
            $hoursAgo = $job['hours_ago'];
            if ($hoursAgo < 1) {
                $job['posted_ago'] = 'Just now';
            } elseif ($hoursAgo < 24) {
                $job['posted_ago'] = $hoursAgo . ' hour' . ($hoursAgo != 1 ? 's' : '') . ' ago';
            } else {
                $daysAgo = floor($hoursAgo / 24);
                $job['posted_ago'] = $daysAgo . ' day' . ($daysAgo != 1 ? 's' : '') . ' ago';
            }
            
            // Parse photos if exists
            if ($job['photos']) {
                $job['photos'] = explode(',', $job['photos']);
            } else {
                $job['photos'] = [];
            }
            
            // Format customer name
            $job['customer_name'] = trim($job['customer_first_name'] . ' ' . $job['customer_last_name']);
            
            // Remove individual name fields to clean up response
            unset($job['customer_first_name']);
            unset($job['customer_last_name']);
            unset($job['hours_ago']);
        }
        
        // Return response
        echo json_encode([
            'success' => true,
            'data' => $jobs,
            'count' => count($jobs)
        ]);
        
    } catch (PDOException $e) {
        error_log("Job Requests API Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage(),
            'details' => $e->getTraceAsString()
        ]);
    } catch (Exception $e) {
        error_log("Job Requests API General Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Server error: ' . $e->getMessage()
        ]);
    }
}

// Execute the function
getAvailableJobs();
?>
