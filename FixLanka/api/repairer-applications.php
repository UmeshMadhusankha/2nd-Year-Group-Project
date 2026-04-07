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

// Backward-compat: some callers pass only company_id
if ($action === '' && isset($_GET['company_id'])) {
    $action = 'list';
}

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
        $hasExperienceYears = columnExists($db, 'repairer', 'experience_years');
        $hasHourlyRate = columnExists($db, 'repairer', 'hourly_rate');
        $hasRatings = columnExists($db, 'repairer', 'ratings');
        $hasProfilePicture = columnExists($db, 'repairer', 'profile_picture');
        $hasSkills = columnExists($db, 'repairer', 'skills');
        $experienceSelect = $hasExperienceYears ? 'r.experience_years' : '0 AS experience_years';
        $hourlySelect = $hasHourlyRate ? 'r.hourly_rate' : '0.00 AS hourly_rate';
        $ratingsSelect = $hasRatings ? 'r.ratings AS rating' : '0.00 AS rating';
        $profileSelect = $hasProfilePicture ? 'r.profile_picture AS profile_photo' : 'NULL AS profile_photo';
        $skillsSelect = $hasSkills ? 'r.skills AS skills' : "'' AS skills";

        $sql = "SELECT 
                    ra.application_id,
                    ra.job_posting_id,
                    ra.repairer_id,
                    ra.status,
                    ra.applied_date,
                    ra.cover_letter,
                    ra.expected_rate,
                    r.f_name AS first_name,
                    r.l_name AS last_name,
                    r.email,
                    r.phoneNumber AS phone,
                    c.name AS specialty,
                    {$experienceSelect},
                    {$hourlySelect},
                    {$ratingsSelect},
                    {$profileSelect},
                    {$skillsSelect},
                    jp.title AS job_title
                FROM repairer_applications ra
                JOIN companyjobpost jp ON ra.job_posting_id = jp.posting_id
                JOIN repairer r ON ra.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE jp.company_id = ?";

        $params = [$company_id];
        
        if ($status !== 'all') {
            $sql .= " AND ra.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY ra.applied_date DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Compute experience dynamically (initial experience + earned years)
        $experienceCache = [];
        foreach ($applications as &$app) {
            $rid = isset($app['repairer_id']) ? intval($app['repairer_id']) : 0;
            if ($rid <= 0) continue;
            if (!array_key_exists($rid, $experienceCache)) {
                $experienceCache[$rid] = computeRepairerExperienceYears($db, $rid);
            }
            $app['experience_years'] = $experienceCache[$rid];
        }
        unset($app);
        
        echo json_encode([
            'success' => true,
            'applications' => $applications,
            'count' => count($applications)
        ]);
        
    } catch (PDOException $e) {
        // If tables don't exist, return empty array
        $debugMessage = null;
        if (isset($_GET['debug']) && $_GET['debug'] === '1') {
            $debugMessage = $e->getMessage();
        }
        echo json_encode([
            'success' => true,
            'applications' => [],
            'count' => 0,
            'note' => $debugMessage ? ('Application query failed: ' . $debugMessage) : 'Application tables may not exist yet'
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
        $hasExperienceYears = columnExists($db, 'repairer', 'experience_years');
        $hasHourlyRate = columnExists($db, 'repairer', 'hourly_rate');
        $hasRatings = columnExists($db, 'repairer', 'ratings');
        $hasProfilePicture = columnExists($db, 'repairer', 'profile_picture');
        $hasSkills = columnExists($db, 'repairer', 'skills');
        $experienceSelect = $hasExperienceYears ? 'r.experience_years' : '0 AS experience_years';
        $hourlySelect = $hasHourlyRate ? 'r.hourly_rate' : '0.00 AS hourly_rate';
        $ratingsSelect = $hasRatings ? 'r.ratings AS rating' : '0.00 AS rating';
        $profileSelect = $hasProfilePicture ? 'r.profile_picture AS profile_photo' : 'NULL AS profile_photo';
        $skillsSelect = $hasSkills ? 'r.skills AS skills' : "'' AS skills";

        $sql = "SELECT 
                    ra.*,
                    r.f_name AS first_name,
                    r.l_name AS last_name,
                    r.email,
                    r.phoneNumber AS phone,
                    c.name AS specialty,
                    {$experienceSelect},
                    {$hourlySelect},
                    {$ratingsSelect},
                    {$profileSelect},
                    {$skillsSelect},
                    jp.title AS job_title,
                    jp.description AS job_description
                FROM repairer_applications ra
                LEFT JOIN repairer r ON ra.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
                LEFT JOIN companyjobpost jp ON ra.job_posting_id = jp.posting_id
                WHERE ra.application_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($application) {
            $rid = intval($application['repairer_id'] ?? 0);
            if ($rid > 0) {
                $application['experience_years'] = computeRepairerExperienceYears($db, $rid);
            }
            $workHistory = getRepairerWorkHistory($db, (int)$application['repairer_id']);
            echo json_encode([
                'success' => true,
                'application' => $application,
                'work_history' => $workHistory
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
 * Experience rule:
 * - Start with repairer.experience_initial_years (fallback: experience_years)
 * - For each full year since signup, if completed jobs in that year >= threshold,
 *   add +1 year experience.
 */
function computeRepairerExperienceYears($db, $repairerId) {
    $repairerId = intval($repairerId);
    if ($repairerId <= 0) return 0;

    // Change this threshold if needed.
    $jobsPerYearThreshold = 5;

    $hasExpYears = columnExists($db, 'repairer', 'experience_years');
    $hasExpYearsCamel = columnExists($db, 'repairer', 'experienceYears');
    $expCol = $hasExpYears ? 'experience_years' : ($hasExpYearsCamel ? 'experienceYears' : null);

    $hasInitial = columnExists($db, 'repairer', 'experience_initial_years');
    $hasInitialCamel = columnExists($db, 'repairer', 'experienceInitialYears');
    $initialCol = $hasInitial ? 'experience_initial_years' : ($hasInitialCamel ? 'experienceInitialYears' : null);

    $hasJoinedAt = columnExists($db, 'repairer', 'joined_at');
    $hasDateJoined = columnExists($db, 'repairer', 'dateJoined');
    $joinedCol = $hasJoinedAt ? 'joined_at' : ($hasDateJoined ? 'dateJoined' : null);

    if (!$expCol) {
        return 0;
    }

    try {
        $selectInitial = $initialCol ? "$initialCol AS experience_initial_years" : "$expCol AS experience_initial_years";
        $selectJoined = $joinedCol ? "$joinedCol AS joined_at" : "NULL AS joined_at";
        $stmt = $db->prepare("SELECT $selectInitial, $expCol AS experience_years, $selectJoined FROM repairer WHERE repairer_id = ? LIMIT 1");
        $stmt->execute([$repairerId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$r) return 0;

        $initial = intval($r['experience_initial_years'] ?? 0);
        $current = intval($r['experience_years'] ?? 0);
        $joinedAtRaw = $r['joined_at'] ?? null;
        if (!$joinedAtRaw) return max($current, $initial);

        $joinedDate = date('Y-m-d', strtotime($joinedAtRaw));
        if (!$joinedDate) return max($current, $initial);

        $daysSince = intval((strtotime(date('Y-m-d')) - strtotime($joinedDate)) / 86400);
        $fullYears = (int) floor($daysSince / 365);
        if ($fullYears <= 0) {
            if ($current !== $initial) {
                // keep aligned
                $upd = $db->prepare("UPDATE repairer SET $expCol = ? WHERE repairer_id = ?");
                $upd->execute([$initial, $repairerId]);
            }
            return $initial;
        }

        if (!tableExists($db, 'job') || !columnExists($db, 'job', 'completionDate') || !columnExists($db, 'job', 'fixer_id') || !columnExists($db, 'job', 'status')) {
            return max($current, $initial);
        }

        $stmt = $db->prepare(
            "SELECT (TIMESTAMPDIFF(DAY, :joined, completionDate) DIV 365) AS year_index, COUNT(*) AS cnt
             FROM job
             WHERE fixer_id = :rid
               AND status = 'completed'
               AND completionDate IS NOT NULL
               AND completionDate >= :joined
             GROUP BY year_index"
        );
        $stmt->execute([':joined' => $joinedDate, ':rid' => $repairerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $earnedYears = 0;
        foreach ($rows as $row) {
            $idx = intval($row['year_index'] ?? -1);
            $cnt = intval($row['cnt'] ?? 0);
            if ($idx < 0 || $idx >= $fullYears) continue;
            if ($cnt >= $jobsPerYearThreshold) $earnedYears++;
        }

        $computedCandidate = $initial + $earnedYears;
        $computed = max($current, $computedCandidate);

        if ($computed !== $current) {
            $upd = $db->prepare("UPDATE repairer SET $expCol = ? WHERE repairer_id = ?");
            $upd->execute([$computed, $repairerId]);
        }

        return $computed;
    } catch (Exception $e) {
        return 0;
    }
}

function tableExists($db, $table) {
    try {
        $stmt = $db->prepare('SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t LIMIT 1');
        $stmt->execute([':t' => $table]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Returns an array of work history items visible to companies.
 * Sources:
 *  - repairer_work_history (manual portfolio entries)
 *  - completed platform jobs from job + jobrequest
 */
function getRepairerWorkHistory($db, $repairerId) {
    $repairerId = intval($repairerId);
    if ($repairerId <= 0) return [];

    $items = [];

    // Manual/portfolio history
    if (tableExists($db, 'repairer_work_history')) {
        try {
            $sql = "SELECT
                        h.history_id,
                        h.title,
                        h.summary,
                        h.completed_at,
                        h.hours_worked,
                        h.rating,
                        h.attachments_json,
                        h.source,
                        c.name AS category
                    FROM repairer_work_history h
                    LEFT JOIN category c ON h.category_id = c.category_id
                    WHERE h.repairer_id = ?
                    ORDER BY h.completed_at DESC, h.history_id DESC
                    LIMIT 20";

            $stmt = $db->prepare($sql);
            $stmt->execute([$repairerId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {
                $items[] = [
                    'source' => $r['source'] ?: 'manual',
                    'title' => $r['title'],
                    'category' => $r['category'],
                    'completed_at' => $r['completed_at'],
                    'summary' => $r['summary'],
                    'hours_worked' => $r['hours_worked'],
                    'rating' => $r['rating'],
                    'attachments_json' => $r['attachments_json']
                ];
            }
        } catch (Exception $e) {
            // ignore; return whatever we have
        }
    }

    // Platform-completed jobs history
    if (tableExists($db, 'job') && tableExists($db, 'jobrequest')) {
        try {
            $sql = "SELECT
                        j.job_id,
                        jr.title,
                        jr.description,
                        j.completionDate AS completed_at,
                        c.name AS category
                    FROM job j
                    JOIN jobrequest jr ON j.job_request_id = jr.request_id
                    LEFT JOIN category c ON jr.category_id = c.category_id
                    WHERE j.fixer_id = ? AND j.status = 'completed'
                    ORDER BY j.completionDate DESC, j.job_id DESC
                    LIMIT 20";

            $stmt = $db->prepare($sql);
            $stmt->execute([$repairerId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $r) {
                $items[] = [
                    'source' => 'platform',
                    'title' => $r['title'],
                    'category' => $r['category'],
                    'completed_at' => $r['completed_at'],
                    'summary' => $r['description'],
                    'hours_worked' => null,
                    'rating' => null,
                    'attachments_json' => null
                ];
            }
        } catch (Exception $e) {
            // ignore
        }
    }

    // Sort combined list by completed_at desc if present
    usort($items, function ($a, $b) {
        $ad = $a['completed_at'] ? strtotime($a['completed_at']) : 0;
        $bd = $b['completed_at'] ? strtotime($b['completed_at']) : 0;
        return $bd <=> $ad;
    });

    return $items;
}

function columnExists(PDO $db, string $table, string $column): bool {
    try {
        $stmt = $db->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1');
        $stmt->execute([':t' => $table, ':c' => $column]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
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
        $sql = "SELECT jp.company_id, ra.repairer_id, ra.expected_rate
                FROM repairer_applications ra
                JOIN companyjobpost jp ON ra.job_posting_id = jp.posting_id
                WHERE ra.application_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$application_id]);
        $appDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($appDetails) {
            // 2. Insert into company_employees
            // IMPORTANT: Repairers recruited via job postings are treated as freelance contractors
            // and should appear under "Available Freelancers" (not permanent staff).
            $sql = "INSERT INTO company_employees (
                        company_id, repairer_id, job_title, employment_type,
                        status, hired_date, hourly_rate
                    ) VALUES (
                        ?, ?, 'Freelancer', 'freelance',
                        'active', CURDATE(), ?
                    ) ON DUPLICATE KEY UPDATE status = 'active', employment_type = 'freelance'";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $appDetails['company_id'],
                $appDetails['repairer_id'],
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
                LEFT JOIN companyjobpost jp ON ra.job_posting_id = jp.posting_id
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
