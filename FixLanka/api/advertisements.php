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
// Prevent caching because statuses like "active" are time-dependent.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Get request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Route to appropriate handler based on method
if ($requestMethod === 'POST') {
    // If JSON body includes an action, treat it as a status/action request.
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $payload = json_decode(file_get_contents('php://input'), true);
        if (is_array($payload) && isset($payload['action'])) {
            handleAdvertisementAction($payload);
        }
    }

    // Multipart/form-data update (edit) flow.
    if (isset($_POST['action']) && strtolower(trim((string)$_POST['action'])) === 'update') {
        handleUpdateAdvertisement();
    }

    handleCreateAdvertisement();
} elseif ($requestMethod === 'DELETE') {
    handleDeleteAdvertisement();
} else {
    // Default GET request - list advertisements
    handleListAdvertisements();
}

function normalizeDateToYmd(?string $value): ?string {
    $value = trim((string)$value);
    if ($value === '') {
        return null;
    }

    // Accept both 'YYYY-MM-DD' and 'YYYY-MM-DDTHH:MM' (datetime-local)
    $value = str_replace('T', ' ', $value);
    $value = substr($value, 0, 19);

    try {
        $dt = new DateTime($value);
        return $dt->format('Y-m-d');
    } catch (Exception $e) {
        return null;
    }
}

function computeDurationDaysInclusive(string $startYmd, string $endYmd): int {
    $startTs = strtotime($startYmd . ' 00:00:00');
    $endTs = strtotime($endYmd . ' 00:00:00');
    if ($startTs === false || $endTs === false || $endTs < $startTs) {
        return 0;
    }

    $diffDays = (int)floor(($endTs - $startTs) / 86400);
    return $diffDays + 1;
}

function getBaseRateByAdType(string $type): int {
    $type = strtolower(trim($type));
    $rates = [
        'banner' => 500,
        'featured' => 800,
        'sponsored' => 1000,
    ];

    return (int)($rates[$type] ?? 500);
}

function getMinDurationByAdType(string $type): int {
    $type = strtolower(trim($type));
    $mins = [
        'banner' => 3,
        'featured' => 7,
        'sponsored' => 14,
    ];

    return (int)($mins[$type] ?? 3);
}

function getMaxDurationByAdType(string $type): int {
    $type = strtolower(trim($type));
    $maxes = [
        'banner' => 30,
        'featured' => 60,
        'sponsored' => 90,
    ];

    return (int)($maxes[$type] ?? 30);
}

function columnExists(PDO $pdo, string $tableName, string $columnName): bool {
    $stmt = $pdo->prepare(
        'SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1'
    );
    $stmt->execute([':t' => $tableName, ':c' => $columnName]);
    return (bool)$stmt->fetchColumn();
}

function isValidHttpUrl(string $url): bool {
    $url = trim($url);
    if ($url === '') {
        return false;
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = parse_url($url, PHP_URL_SCHEME);
    return $scheme === 'http' || $scheme === 'https';
}

function getAuthenticatedCompanyId(PDO $pdo): ?int {
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        return null;
    }

    $companyId = $_SESSION['company_id'] ?? null;
    if ($companyId) {
        return (int)$companyId;
    }

    $companyData = getCompanyByUserId($pdo, (int)$userId);
    if (!$companyData) {
        return null;
    }

    return (int)$companyData['company_id'];
}

/**
 * Handle POST request - Update ad status/actions (pause/resume/archive/etc.)
 */
function handleAdvertisementAction(array $payload): void {
    global $pdo;

    try {
        requireAuth();

        $companyId = getAuthenticatedCompanyId($pdo);
        if (!$companyId) {
            sendErrorResponse('Company profile not found', 404);
        }

        $action = strtolower(trim((string)($payload['action'] ?? '')));
        $adId = (int)($payload['ad_id'] ?? 0);
        if ($adId <= 0) {
            sendErrorResponse('Advertisement ID is required', 400);
        }

        $allowedActions = ['pause', 'resume', 'start', 'cancel', 'archive'];
        if (!in_array($action, $allowedActions, true)) {
            sendErrorResponse('Invalid action', 400);
        }

        // Persisted status should not be time-window-based.
        // "active" is computed at read-time from adschedule daily window.
        $newStatus = match ($action) {
            'pause' => 'paused',
            'cancel', 'archive' => 'inactive',
            'resume', 'start' => 'approved',
            default => 'approved',
        };

        if ($action === 'resume' || $action === 'start') {
            // If the ad has a schedule, resuming should restore the scheduled state.
            try {
                $hasScheduleStmt = $pdo->prepare(
                    "SELECT EXISTS(SELECT 1 FROM adschedule WHERE ad_id = :ad_id LIMIT 1)"
                );
                $hasScheduleStmt->execute([':ad_id' => $adId]);
                $hasSchedule = (int)$hasScheduleStmt->fetchColumn() === 1;
                if ($hasSchedule) {
                    $newStatus = 'scheduled';
                }
            } catch (PDOException $e) {
                // If schedule table isn't available, fall back to approved.
                $newStatus = 'approved';
            }
        }

                $stmt = $pdo->prepare(
                        "UPDATE advertisement 
                         SET status = :status 
                         WHERE ad_id = :ad_id 
                             AND provider_id = :provider_id 
                             AND provider_type = 'company'"
                );
        $stmt->execute([
            ':status' => $newStatus,
            ':ad_id' => $adId,
            ':provider_id' => $companyId,
        ]);

        if ($stmt->rowCount() === 0) {
            sendErrorResponse('Advertisement not found', 404);
        }

        sendSuccessResponse(['message' => 'Advertisement updated successfully', 'status' => $newStatus]);
    } catch (Exception $e) {
        error_log('Error updating advertisement status: ' . $e->getMessage());
        sendErrorResponse('Failed to update advertisement', 500);
    }
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
            sendJsonResponse([
                'success' => true,
                'data' => [],
                'counts' => [
                    'all' => 0,
                    'active' => 0,
                    'approved' => 0,
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

    // Best-effort sync of time-based statuses for this company's ads.
    // Persist only the non-dynamic part (expired). "active" is computed based on
    // the daily time window and should not be stored.
    try {
        // Expire after campaign end date.
        $stmt = $pdo->prepare(
            "UPDATE advertisement\n"
            . "SET status = 'expired'\n"
            . "WHERE provider_id = :company_id\n"
            . "  AND provider_type = 'company'\n"
            . "  AND end_date < CURDATE()\n"
            . "  AND status IN ('approved','scheduled','active')"
        );
        $stmt->execute([':company_id' => $companyId]);
    } catch (PDOException $e) {
        // Don't break the API response if optional sync fails.
        error_log('Company ad status sync failed: ' . $e->getMessage());
    }

    // Fetch advertisements from database
    // Uses computed status based on dates and current status
    $query = "SELECT 
                a.ad_id as id,
                a.provider_id,
                a.provider_type,
                a.title,
                a.description,
                a.category_id,
                a.image_url,
                a.type,
                a.budget,
                a.start_date,
                a.end_date,
                a.impressions,
                a.clicks,
                -- Calculate click-through rate (CTR)
                CASE 
                    WHEN a.clicks > 0 AND a.impressions > 0 
                    THEN ROUND((a.clicks / a.impressions) * 100, 2)
                    ELSE 0.00
                END as click_through_rate,
                0.00 as budget_spent,
                a.status,
                a.submission_date as created_at,
                NULL as updated_at,
                -- Compute status based on schedule existence + dates
                CASE 
                    WHEN s.schedule_id IS NOT NULL THEN
                        CASE
                            WHEN a.end_date < CURDATE() THEN 'expired'
                            WHEN a.status IN ('paused','inactive','suspended','rejected') THEN 'paused'
                            WHEN CURDATE() < s.start_date THEN 'scheduled'
                            WHEN CURDATE() > s.end_date THEN 'expired'
                            WHEN CURTIME() BETWEEN COALESCE(s.start_time,'00:00:00') AND COALESCE(s.end_time,'23:59:59') THEN 'active'
                            ELSE 'scheduled'
                        END
                    ELSE
                        CASE
                            WHEN a.end_date < CURDATE() THEN 'expired'
                            WHEN a.status IN ('paused','inactive','suspended','rejected') THEN 'paused'
                            ELSE a.status
                        END
                END as computed_status
              FROM advertisement a
              LEFT JOIN adschedule s
                ON s.schedule_id = (
                    SELECT s2.schedule_id
                    FROM adschedule s2
                    WHERE s2.ad_id = a.ad_id
                    ORDER BY s2.schedule_id DESC
                    LIMIT 1
                )
              WHERE a.provider_id = :company_id 
                AND a.provider_type = 'company'
              ORDER BY a.submission_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':company_id', $companyId, PDO::PARAM_INT);
    $stmt->execute();
    
    $advertisements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate counts for each status type
    $counts = [
        'all' => count($advertisements),
        'active' => 0,
        'approved' => 0,
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

    // Send successful response with data and counts (shape expected by frontend)
    sendJsonResponse([
        'success' => true,
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
        $title = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $type = strtolower(trim((string)($_POST['type'] ?? 'banner')));
        // Field removed from UI; keep DB value stable.
        $targetAudience = 'all';
        $scheduleType = strtolower(trim((string)($_POST['schedule_type'] ?? 'scheduled')));
        $startDate = normalizeDateToYmd($_POST['start_date'] ?? null);
        $endDate = normalizeDateToYmd($_POST['end_date'] ?? null);
        $duration = (int)($_POST['duration'] ?? 0);
        $priorityPlacement = isset($_POST['priority_placement']) ? 1 : 0;
        $targetUrl = trim((string)($_POST['target_url'] ?? ''));
        $linkText = trim((string)($_POST['link_text'] ?? ''));
        if ($type === 'sponsored') {
            $priorityPlacement = 1;
        }
        
        // Validate required fields
        if (empty($title) || empty($description) || empty($category)) {
            sendErrorResponse('Title, description, and category are required', 400);
            return;
        }

        $allowedTypes = ['banner', 'featured', 'sponsored'];
        if (!in_array($type, $allowedTypes, true)) {
            sendErrorResponse('Invalid advertisement type', 400);
            return;
        }

        // Scheduling is always required (review lead-time)
        if (!$startDate || !$endDate) {
            sendErrorResponse('Start date and end date are required', 400);
            return;
        }

        $reviewLeadDays = 2;
        $minStart = date('Y-m-d', strtotime('+' . $reviewLeadDays . ' days'));
        if ($startDate < $minStart) {
            sendErrorResponse('Start date must be at least ' . $reviewLeadDays . ' days from today for review time', 400);
            return;
        }

        $computedDuration = computeDurationDaysInclusive($startDate, $endDate);
        if ($computedDuration <= 0) {
            sendErrorResponse('End date must be the same as or after the start date', 400);
            return;
        }

        $minDuration = getMinDurationByAdType($type);
        if ($computedDuration < $minDuration) {
            sendErrorResponse('This advertisement type requires a minimum duration of ' . $minDuration . ' days', 400);
            return;
        }

        $maxDuration = getMaxDurationByAdType($type);
        if ($computedDuration > $maxDuration) {
            sendErrorResponse('This advertisement type allows a maximum duration of ' . $maxDuration . ' days', 400);
            return;
        }

        $hasTargetUrl = columnExists($pdo, 'advertisement', 'target_url');
        $hasLinkText = columnExists($pdo, 'advertisement', 'link_text');

        if ($hasTargetUrl && $targetUrl !== '' && !isValidHttpUrl($targetUrl)) {
            sendErrorResponse('Please enter a valid Call-to-Action link (must start with http:// or https://)', 400);
            return;
        }

        if ($type === 'sponsored' && $hasTargetUrl && $targetUrl === '') {
            sendErrorResponse('Sponsored advertisements require a Call-to-Action link', 400);
            return;
        }

        $duration = $computedDuration;
        
        // Handle file upload
        $imageUrl = null;
        if (isset($_FILES['media_file'])) {
            $uploadError = (int)($_FILES['media_file']['error'] ?? UPLOAD_ERR_NO_FILE);
            $size = (int)($_FILES['media_file']['size'] ?? 0);

            if ($uploadError !== UPLOAD_ERR_NO_FILE && $uploadError !== UPLOAD_ERR_OK) {
                $messages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the server upload limit.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the form upload limit.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                $msg = $messages[$uploadError] ?? 'File upload failed.';
                sendErrorResponse($msg, 400);
                return;
            }

            if ($uploadError === UPLOAD_ERR_OK) {
                $maxBytes = 5 * 1024 * 1024;
                if ($size > $maxBytes) {
                    sendErrorResponse('Image file is too large. Maximum allowed size is 5MB.', 400);
                    return;
                }

                $tmp = $_FILES['media_file']['tmp_name'] ?? '';
                $mime = $tmp ? @mime_content_type($tmp) : '';
                if ($mime && strpos($mime, 'image/') !== 0) {
                    sendErrorResponse('Invalid file type. Please upload an image.', 400);
                    return;
                }

                $uploadDir = __DIR__ . '/../uploads/advertisements/';
                if (!is_dir($uploadDir)) {
                    if (!@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                        sendErrorResponse('Failed to create upload directory.', 500);
                        return;
                    }
                }

                $fileName = time() . '_' . basename((string)($_FILES['media_file']['name'] ?? 'upload.jpg'));
                $targetPath = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

                if (!move_uploaded_file($tmp, $targetPath)) {
                    sendErrorResponse('Failed to save uploaded image.', 500);
                    return;
                }

                $imageUrl = '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/' . $fileName;
            }
        }

        if (!$imageUrl) {
            sendErrorResponse('Please upload an image for your advertisement', 400);
            return;
        }
        
        // Calculate budget based on duration and pricing (varies by ad type)
        $baseRate = getBaseRateByAdType($type);
        $priorityRate = ($type === 'sponsored') ? 0 : ($priorityPlacement ? 200 : 0);
        $budget = ($baseRate + $priorityRate) * intval($duration);
        
        // Ignore legacy schedule types; all ads are scheduled by dates.
        $scheduleType = 'scheduled';
        
        // Insert into database (conditionally persist CTA fields if present in live schema)

        $columns = [
            'provider_id',
            'provider_type',
            'title',
            'description',
            'category_id',
            'image_url',
            'type',
            'budget',
            'target_audience',
            'start_date',
            'end_date',
            'status',
            'submission_date',
        ];
        $values = [
            ':provider_id',
            "'company'",
            ':title',
            ':description',
            ':category_id',
            ':image_url',
            ':type',
            ':budget',
            ':target_audience',
            ':start_date',
            ':end_date',
            "'pending'",
            'NOW()',
        ];

        if ($hasTargetUrl) {
            $columns[] = 'target_url';
            $values[] = ':target_url';
        }
        if ($hasLinkText) {
            $columns[] = 'link_text';
            $values[] = ':link_text';
        }

        $query = 'INSERT INTO advertisement (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';
        
        $stmt = $pdo->prepare($query);
        $exec = [
            ':provider_id' => $companyId,
            ':title' => $title,
            ':description' => $description,
            ':category_id' => intval($category) ?: 1,
            ':image_url' => $imageUrl,
            ':type' => $type,
            ':budget' => $budget,
            ':target_audience' => $targetAudience,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ];
        if ($hasTargetUrl) {
            $exec[':target_url'] = $targetUrl;
        }
        if ($hasLinkText) {
            $exec[':link_text'] = $linkText;
        }

        $stmt->execute($exec);
        
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
 * Handle POST request - Update an existing advertisement (company-owned)
 * Currently restricted to pending ads to avoid bypassing moderation.
 */
function handleUpdateAdvertisement(): void {
    global $pdo;

    try {
        requireAuth();

        $companyId = getAuthenticatedCompanyId($pdo);
        if (!$companyId) {
            sendErrorResponse('Company profile not found', 404);
            return;
        }

        $adId = (int)($_POST['ad_id'] ?? 0);
        if ($adId <= 0) {
            sendErrorResponse('Advertisement ID is required', 400);
            return;
        }

        $stmt = $pdo->prepare(
            "SELECT ad_id, status, image_url, start_date, end_date FROM advertisement 
             WHERE ad_id = :ad_id AND provider_type = 'company' AND provider_id = :company_id
             LIMIT 1"
        );
        $stmt->execute([':ad_id' => $adId, ':company_id' => $companyId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$existing) {
            sendErrorResponse('Advertisement not found', 404);
            return;
        }

        if (($existing['status'] ?? '') !== 'pending') {
            sendErrorResponse('Only pending advertisements can be edited', 400);
            return;
        }

        $title = trim((string)($_POST['title'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $type = strtolower(trim((string)($_POST['type'] ?? 'banner')));
        // Field removed from UI; keep DB value stable.
        $targetAudience = 'all';
        $scheduleType = strtolower(trim((string)($_POST['schedule_type'] ?? 'scheduled')));
        $startDate = normalizeDateToYmd($_POST['start_date'] ?? null);
        $endDate = normalizeDateToYmd($_POST['end_date'] ?? null);
        $duration = (int)($_POST['duration'] ?? 0);
        $priorityPlacement = isset($_POST['priority_placement']) ? 1 : 0;
        $targetUrl = trim((string)($_POST['target_url'] ?? ''));
        $linkText = trim((string)($_POST['link_text'] ?? ''));
        if ($type === 'sponsored') {
            $priorityPlacement = 1;
        }

        if (empty($title) || empty($description) || empty($category)) {
            sendErrorResponse('Title, description, and category are required', 400);
            return;
        }

        $allowedTypes = ['banner', 'featured', 'sponsored'];
        if (!in_array($type, $allowedTypes, true)) {
            sendErrorResponse('Invalid advertisement type', 400);
            return;
        }

        if (!$startDate || !$endDate) {
            sendErrorResponse('Start date and end date are required', 400);
            return;
        }

        $existingStart = normalizeDateToYmd($existing['start_date'] ?? null);
        $existingEnd = normalizeDateToYmd($existing['end_date'] ?? null);

        $reviewLeadDays = 2;
        $minStart = date('Y-m-d', strtotime('+' . $reviewLeadDays . ' days'));
        if ($startDate < $minStart) {
            // Allow editing older ads that were already created with an earlier start date.
            if (!$existingStart || $existingStart !== $startDate) {
                sendErrorResponse('Start date must be at least ' . $reviewLeadDays . ' days from today for review time', 400);
                return;
            }
        }

        $computedDuration = computeDurationDaysInclusive($startDate, $endDate);
        if ($computedDuration <= 0) {
            sendErrorResponse('End date must be the same as or after the start date', 400);
            return;
        }

        $datesUnchanged = $existingStart && $existingEnd && $existingStart === $startDate && $existingEnd === $endDate;

        $minDuration = getMinDurationByAdType($type);
        if ($computedDuration < $minDuration && !$datesUnchanged) {
            sendErrorResponse('This advertisement type requires a minimum duration of ' . $minDuration . ' days', 400);
            return;
        }

        $maxDuration = getMaxDurationByAdType($type);
        if ($computedDuration > $maxDuration && !$datesUnchanged) {
            sendErrorResponse('This advertisement type allows a maximum duration of ' . $maxDuration . ' days', 400);
            return;
        }

        $hasTargetUrl = columnExists($pdo, 'advertisement', 'target_url');
        if ($hasTargetUrl && $targetUrl !== '' && !isValidHttpUrl($targetUrl)) {
            sendErrorResponse('Please enter a valid Call-to-Action link (must start with http:// or https://)', 400);
            return;
        }
        if ($type === 'sponsored' && $hasTargetUrl && $targetUrl === '') {
            sendErrorResponse('Sponsored advertisements require a Call-to-Action link', 400);
            return;
        }

        $duration = $computedDuration;

        // Handle optional file upload (replace existing image)
        $imageUrl = null;
        if (isset($_FILES['media_file'])) {
            $uploadError = (int)($_FILES['media_file']['error'] ?? UPLOAD_ERR_NO_FILE);
            $size = (int)($_FILES['media_file']['size'] ?? 0);

            if ($uploadError !== UPLOAD_ERR_NO_FILE && $uploadError !== UPLOAD_ERR_OK) {
                $messages = [
                    UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the server upload limit.',
                    UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the form upload limit.',
                    UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder on the server.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
                ];
                $msg = $messages[$uploadError] ?? 'File upload failed.';
                sendErrorResponse($msg, 400);
                return;
            }

            if ($uploadError === UPLOAD_ERR_OK) {
                $maxBytes = 5 * 1024 * 1024;
                if ($size > $maxBytes) {
                    sendErrorResponse('Image file is too large. Maximum allowed size is 5MB.', 400);
                    return;
                }

                $tmp = $_FILES['media_file']['tmp_name'] ?? '';
                $mime = $tmp ? @mime_content_type($tmp) : '';
                if ($mime && strpos($mime, 'image/') !== 0) {
                    sendErrorResponse('Invalid file type. Please upload an image.', 400);
                    return;
                }

                $uploadDir = __DIR__ . '/../uploads/advertisements/';
                if (!is_dir($uploadDir)) {
                    if (!@mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                        sendErrorResponse('Failed to create upload directory.', 500);
                        return;
                    }
                }

                $fileName = time() . '_' . basename((string)($_FILES['media_file']['name'] ?? 'upload.jpg'));
                $targetPath = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

                if (!move_uploaded_file($tmp, $targetPath)) {
                    sendErrorResponse('Failed to save uploaded image.', 500);
                    return;
                }

                $imageUrl = '/2nd-Year-Group-Project/FixLanka/uploads/advertisements/' . $fileName;
            }
        }

        // Budget calculation (keep consistent with create, varies by ad type)
        $baseRate = getBaseRateByAdType($type);
        $priorityRate = ($type === 'sponsored') ? 0 : ($priorityPlacement ? 200 : 0);
        $budget = ($baseRate + $priorityRate) * intval($duration);

        // Ignore legacy schedule types; all ads are scheduled by dates.
        $scheduleType = 'scheduled';

        $finalImageUrl = $imageUrl !== null ? $imageUrl : (string)($existing['image_url'] ?? '');
        if (!$finalImageUrl) {
            sendErrorResponse('Please upload an image for your advertisement', 400);
            return;
        }

        $hasLinkText = columnExists($pdo, 'advertisement', 'link_text');

        $setParts = [
            'title = :title',
            'description = :description',
            'category_id = :category_id',
            'image_url = :image_url',
            'type = :type',
            'budget = :budget',
            'target_audience = :target_audience',
            'start_date = :start_date',
            'end_date = :end_date',
        ];
        if ($hasTargetUrl) {
            $setParts[] = 'target_url = :target_url';
        }
        if ($hasLinkText) {
            $setParts[] = 'link_text = :link_text';
        }

        $update = $pdo->prepare(
            "UPDATE advertisement
             SET " . implode(",\n                 ", $setParts) .
            "
             WHERE ad_id = :ad_id
               AND provider_type = 'company'
               AND provider_id = :company_id
               AND status = 'pending'"
        );

        $exec = [
            ':title' => $title,
            ':description' => $description,
            ':category_id' => intval($category) ?: 1,
            ':image_url' => $finalImageUrl,
            ':type' => $type,
            ':budget' => $budget,
            ':target_audience' => $targetAudience,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':ad_id' => $adId,
            ':company_id' => $companyId,
        ];

        if ($hasTargetUrl) {
            $exec[':target_url'] = $targetUrl;
        }
        if ($hasLinkText) {
            $exec[':link_text'] = $linkText;
        }

        $update->execute($exec);

        sendSuccessResponse([
            'message' => 'Advertisement updated successfully',
            'ad_id' => $adId,
            'status' => 'pending',
            'image_url' => $finalImageUrl,
        ]);
    } catch (PDOException $e) {
        error_log('Database error updating advertisement: ' . $e->getMessage());
        sendErrorResponse('Failed to update advertisement', 500);
    } catch (Exception $e) {
        error_log('Error updating advertisement: ' . $e->getMessage());
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
        
        $companyId = getAuthenticatedCompanyId($pdo);
        if (!$companyId) {
            sendErrorResponse('Company profile not found', 404);
            return;
        }

        // Delete advertisement (only if it belongs to this company)
        $query = "DELETE FROM advertisement WHERE ad_id = :ad_id AND provider_id = :provider_id AND provider_type = 'company'";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':ad_id' => $adId, ':provider_id' => $companyId]);

        if ($stmt->rowCount() === 0) {
            sendErrorResponse('Advertisement not found', 404);
            return;
        }
        
        sendSuccessResponse(['message' => 'Advertisement deleted successfully']);
        
    } catch (Exception $e) {
        error_log("Error deleting advertisement: " . $e->getMessage());
        sendErrorResponse('Failed to delete advertisement', 500);
    }
}
