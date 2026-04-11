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
    // We will list all Repairers. This API is tolerant to schema drift (missing columns/tables).
    
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $specialty = isset($_GET['specialty']) ? $_GET['specialty'] : '';
    
    $hasExperienceYears = columnExists($db, 'repairer', 'experience_years');
    $hasHourlyRate = columnExists($db, 'repairer', 'hourly_rate');
    $hasRatings = columnExists($db, 'repairer', 'ratings');
    $hasProfilePicture = columnExists($db, 'repairer', 'profile_picture');
    $experienceSelect = $hasExperienceYears ? 'r.experience_years' : '0 AS experience_years';
    $hourlySelect = $hasHourlyRate ? 'r.hourly_rate' : '0.00 AS hourly_rate';
    $ratingsSelect = $hasRatings ? 'r.ratings AS rating' : '0.00 AS rating';
    $profileSelect = $hasProfilePicture ? 'r.profile_picture AS profile_photo' : 'NULL AS profile_photo';

    $hasIsDeleted = columnExists($db, 'repairer', 'is_deleted');
    $companyEmployeesExists = tableExists($db, 'company_employees');

    $selectEmployment = $companyEmployeesExists
        ? "CASE WHEN ce.employee_id IS NOT NULL THEN 1 ELSE 0 END as is_hired, ce.status as employment_status"
        : "0 as is_hired, NULL as employment_status";

    $joinEmployment = $companyEmployeesExists
        ? "LEFT JOIN company_employees ce ON r.repairer_id = ce.repairer_id AND ce.company_id = :company_id"
        : "";

    $whereActive = $hasIsDeleted ? "WHERE r.is_deleted = 0" : "WHERE 1=1";

    $sql = "SELECT 
                r.repairer_id,
                r.f_name as first_name,
                r.l_name as last_name,
                r.email,
                r.phoneNumber as phone,
                c.name as specialty,
                {$experienceSelect},
                {$hourlySelect},
                {$ratingsSelect},
                {$profileSelect},

                {$selectEmployment}

            FROM repairer r
            LEFT JOIN category c ON r.category_id = c.category_id
            {$joinEmployment}
            {$whereActive}";
            
    $params = [':company_id' => $company_id];

    if ($search) {
        $sql .= " AND (r.f_name LIKE :search OR r.l_name LIKE :search OR c.name LIKE :search OR r.email LIKE :search)";
        $params[':search'] = "%{$search}%";
    }

    if ($specialty) {
        $sql .= " AND c.name = :specialty";
        $params[':specialty'] = $specialty;
    }
    
    $sql .= " ORDER BY is_hired DESC, r.ratings DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $freelancers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compute experience dynamically (initial experience + earned years)
    $experienceCache = [];
    foreach ($freelancers as &$fr) {
        $rid = isset($fr['repairer_id']) ? intval($fr['repairer_id']) : 0;
        if ($rid <= 0) continue;
        if (!array_key_exists($rid, $experienceCache)) {
            $experienceCache[$rid] = computeRepairerExperienceYears($db, $rid);
        }
        $fr['experience_years'] = $experienceCache[$rid];
    }
    unset($fr);

    echo json_encode([
        'success' => true,
        'freelancers' => $freelancers,
        'count' => count($freelancers)
    ]);

} catch (PDOException $e) {
    // If schema is incomplete (missing tables/columns), return empty list instead of hard failing.
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'freelancers' => [],
        'count' => 0,
        'note' => 'Freelancer query failed (schema mismatch): ' . $e->getMessage()
    ]);
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

function tableExists(PDO $db, string $table): bool {
    try {
        $stmt = $db->prepare('SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t LIMIT 1');
        $stmt->execute([':t' => $table]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

function computeRepairerExperienceYears(PDO $db, int $repairerId): int {
    // Change this threshold if needed.
    $jobsPerYearThreshold = 5;

    $hasExpYears = columnExists($db, 'repairer', 'experience_years');
    $hasExpYearsCamel = columnExists($db, 'repairer', 'experienceYears');
    $expCol = $hasExpYears ? 'experience_years' : ($hasExpYearsCamel ? 'experienceYears' : null);
    if (!$expCol) return 0;

    $hasInitial = columnExists($db, 'repairer', 'experience_initial_years');
    $hasInitialCamel = columnExists($db, 'repairer', 'experienceInitialYears');
    $initialCol = $hasInitial ? 'experience_initial_years' : ($hasInitialCamel ? 'experienceInitialYears' : null);

    $hasJoinedAt = columnExists($db, 'repairer', 'joined_at');
    $hasDateJoined = columnExists($db, 'repairer', 'dateJoined');
    $joinedCol = $hasJoinedAt ? 'joined_at' : ($hasDateJoined ? 'dateJoined' : null);

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
        if ($fullYears <= 0) return max($current, $initial);

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
?>
