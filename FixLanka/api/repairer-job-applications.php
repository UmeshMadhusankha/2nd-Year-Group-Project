<?php
/**
 * Repairer Job Applications API (Repairer-side)
 *
 * Uses Phase-4 Workforce tables:
 * - companyjobpost
 * - repairer_applications
 *
 * Actions:
 *   GET  ?action=my-list&repairer_id=X
 *   GET  ?action=check&repairer_id=X&posting_id=Y
 *   POST ?action=submit   JSON: {repairer_id, posting_id, proposed_rate, cover_letter}
 *   POST ?action=withdraw JSON: {application_id, repairer_id}
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

global $pdo;

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    if ($method === 'GET') {
        switch ($action) {
            case 'my-list':
                listMyApplications($pdo);
                break;
            case 'check':
                checkApplied($pdo);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        switch ($action) {
            case 'submit':
                submitApplication($pdo, $data);
                break;
            case 'withdraw':
                withdrawApplication($pdo, $data);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function listMyApplications(PDO $pdo): void {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    if (!$repairerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'repairer_id is required']);
        return;
    }

    $deadlineSelect = columnExists($pdo, 'companyjobpost', 'application_deadline')
        ? 'jp.application_deadline'
        : 'NULL AS application_deadline';

    $stmt = $pdo->prepare("
        SELECT
            ra.application_id AS app_id,
            ra.job_posting_id AS posting_id,
            ra.applied_date   AS date_applied,
            ra.status         AS app_status,
            jp.title,
            jp.category,
            jp.employment_type,
            jp.location,
            jp.min_budget,
            jp.max_budget,
            jp.status AS posting_status,
            {$deadlineSelect},
            c.name    AS company_name
        FROM repairer_applications ra
        JOIN companyjobpost jp ON ra.job_posting_id = jp.posting_id
        JOIN company c ON jp.company_id = c.company_id
        WHERE ra.repairer_id = :repairer_id
        ORDER BY ra.applied_date DESC
    ");
    $stmt->execute([':repairer_id' => $repairerId]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'applications' => $applications]);
}

function columnExists(PDO $pdo, string $table, string $column): bool {
    try {
        $stmt = $pdo->prepare('SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1');
        $stmt->execute([':t' => $table, ':c' => $column]);
        return (bool)$stmt->fetchColumn();
    } catch (Exception $e) {
        return false;
    }
}

function tableExists(PDO $pdo, string $table): bool {
    try {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM INFORMATION_SCHEMA.TABLES '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t LIMIT 1'
        );
        $stmt->execute([':t' => $table]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function getJobPostingForeignKey(PDO $pdo): ?array {
    try {
        $stmt = $pdo->prepare(
            'SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME '
            . 'FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE '
            . 'WHERE TABLE_SCHEMA = DATABASE() '
            . '  AND TABLE_NAME = :table '
            . '  AND COLUMN_NAME = :column '
            . '  AND REFERENCED_TABLE_NAME IS NOT NULL '
            . 'LIMIT 1'
        );
        $stmt->execute([':table' => 'repairer_applications', ':column' => 'job_posting_id']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function uniqueIndexExists(PDO $pdo, string $table, string $indexName): bool {
    try {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS '
            . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND INDEX_NAME = :i LIMIT 1'
        );
        $stmt->execute([':t' => $table, ':i' => $indexName]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function ensureRepairerApplicationsUniqueIndex(PDO $pdo): void {
    // Only attempt if table exists; ignore failures (permissions, etc.)
    if (!tableExists($pdo, 'repairer_applications')) return;
    $index = 'uq_repairer_job_application';
    if (uniqueIndexExists($pdo, 'repairer_applications', $index)) return;

    try {
        $pdo->exec('ALTER TABLE `repairer_applications` ADD UNIQUE KEY `' . $index . '` (`repairer_id`, `job_posting_id`)');
    } catch (Throwable $e) {
        // ignore
    }
}

function ensureRepairerApplicationsJobPostingFK(PDO $pdo): void {
    // Only attempt if both tables exist.
    if (!tableExists($pdo, 'repairer_applications') || !tableExists($pdo, 'companyjobpost')) {
        return;
    }

    $fk = getJobPostingForeignKey($pdo);
    if ($fk && ($fk['REFERENCED_TABLE_NAME'] ?? null) === 'companyjobpost') {
        return;
    }

    // If FK exists but points to legacy table name, drop it.
    if ($fk && !empty($fk['CONSTRAINT_NAME'])) {
        $constraint = $fk['CONSTRAINT_NAME'];
        try {
            $pdo->exec('ALTER TABLE `repairer_applications` DROP FOREIGN KEY `' . str_replace('`', '``', $constraint) . '`');
        } catch (Throwable $e) {
            // Ignore; may not have privileges.
        }
    }

    // Recreate the FK pointing to `companyjobpost`.
    try {
        $pdo->exec(
            "ALTER TABLE `repairer_applications` "
            . "ADD CONSTRAINT `repairer_applications_job_posting_fk` "
            . "FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE"
        );
    } catch (Throwable $e) {
        // If the named constraint fails (already exists / permissions), try without a name.
        try {
            $pdo->exec(
                "ALTER TABLE `repairer_applications` "
                . "ADD FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE"
            );
        } catch (Throwable $e2) {
            // Ignore; caller will see FK failure and we will surface actionable SQL.
        }
    }
}

function checkApplied(PDO $pdo): void {
    $repairerId = intval($_GET['repairer_id'] ?? 0);
    $postingId  = intval($_GET['posting_id'] ?? 0);
    if (!$repairerId || !$postingId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'repairer_id and posting_id are required']);
        return;
    }

    $stmt = $pdo->prepare("SELECT application_id AS app_id, status AS app_status FROM repairer_applications WHERE repairer_id = :r AND job_posting_id = :p LIMIT 1");
    $stmt->execute([':r' => $repairerId, ':p' => $postingId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'applied' => (bool)$row, 'application' => $row ?: null]);
}

function submitApplication(PDO $pdo, array $data): void {
    $repairerId   = intval($data['repairer_id'] ?? 0);
    $postingId    = intval($data['posting_id'] ?? 0);
    $proposedRate = null;
    if (isset($data['proposed_rate'])) {
        $proposedRate = floatval($data['proposed_rate']);
    } elseif (isset($data['proposedRate'])) {
        $proposedRate = floatval($data['proposedRate']);
    } elseif (isset($data['expected_rate'])) {
        $proposedRate = floatval($data['expected_rate']);
    }

    $coverLetter  = trim($data['cover_letter'] ?? '');

    if (!$repairerId || !$postingId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'repairer_id and posting_id are required']);
        return;
    }

    if ($coverLetter === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'cover_letter is required']);
        return;
    }

    if ($proposedRate === null || $proposedRate <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'proposed_rate is required']);
        return;
    }

    // Ensure posting exists and is open
    $stmt = $pdo->prepare("SELECT posting_id FROM companyjobpost WHERE posting_id = :p AND status = 'open' LIMIT 1");
    $stmt->execute([':p' => $postingId]);
    if (!$stmt->fetchColumn()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Job posting not found or not open']);
        return;
    }

    // Duplicate check
    // Best-effort: enforce at DB-level too (prevents double-submit races)
    ensureRepairerApplicationsUniqueIndex($pdo);

    $dup = $pdo->prepare("SELECT application_id FROM repairer_applications WHERE repairer_id = :r AND job_posting_id = :p LIMIT 1");
    $dup->execute([':r' => $repairerId, ':p' => $postingId]);
    if ($dup->fetchColumn()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'You have already applied for this job']);
        return;
    }

    // Some DBs still have repairer_applications.job_posting_id FK pointing to `company_jobpost`.
    // Try to self-heal it to `companyjobpost` before inserting.
    $fkBefore = getJobPostingForeignKey($pdo);
    if ($fkBefore && ($fkBefore['REFERENCED_TABLE_NAME'] ?? null) !== 'companyjobpost') {
        ensureRepairerApplicationsJobPostingFK($pdo);

        $fkAfter = getJobPostingForeignKey($pdo);
        if ($fkAfter && ($fkAfter['REFERENCED_TABLE_NAME'] ?? null) !== 'companyjobpost') {
            $fkName = $fkAfter['CONSTRAINT_NAME'] ?? null;
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database foreign key for repairer_applications.job_posting_id is pointing to an old jobpost table name. Update it to reference companyjobpost(posting_id).',
                'fk_detected' => $fkAfter,
                'sql_fix' => [
                    $fkName
                        ? ('ALTER TABLE `repairer_applications` DROP FOREIGN KEY `' . $fkName . '`;')
                        : '-- Find the FK name first: SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=\'repairer_applications\' AND COLUMN_NAME=\'job_posting_id\' AND REFERENCED_TABLE_NAME IS NOT NULL;',
                    'ALTER TABLE `repairer_applications` ADD CONSTRAINT `repairer_applications_job_posting_fk` FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE;'
                ],
            ]);
            return;
        }
    }

    try {
        $stmt = $pdo->prepare("\n            INSERT INTO repairer_applications (job_posting_id, repairer_id, cover_letter, expected_rate, status)
            VALUES (:posting_id, :repairer_id, :cover_letter, :expected_rate, 'pending')
        ");
        $stmt->execute([
            ':posting_id' => $postingId,
            ':repairer_id' => $repairerId,
            ':cover_letter' => $coverLetter,
            ':expected_rate' => $proposedRate,
        ]);
    } catch (PDOException $e) {
        // Provide a more actionable error message for FK issues.
        $msg = $e->getMessage();
        if (stripos($msg, 'FOREIGN KEY') !== false || stripos($msg, 'Integrity constraint violation') !== false) {
            $fk = getJobPostingForeignKey($pdo);
            $fkName = $fk['CONSTRAINT_NAME'] ?? null;
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Database foreign key is still pointing to an old jobpost table name. Fix repairer_applications.job_posting_id FK to reference companyjobpost(posting_id).',
                'sql_fix' => [
                    $fkName
                        ? ('ALTER TABLE `repairer_applications` DROP FOREIGN KEY `' . $fkName . '`;')
                        : '-- Find the FK name first: SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=\'repairer_applications\' AND COLUMN_NAME=\'job_posting_id\' AND REFERENCED_TABLE_NAME IS NOT NULL;',
                    'ALTER TABLE `repairer_applications` ADD CONSTRAINT `repairer_applications_job_posting_fk` FOREIGN KEY (`job_posting_id`) REFERENCES `companyjobpost` (`posting_id`) ON DELETE CASCADE;'
                ],
                'fk_detected' => $fk,
                'details' => $msg,
            ]);
            return;
        }

        throw $e;
    }

    $appId = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'app_id' => $appId, 'message' => 'Application submitted successfully']);
}

function withdrawApplication(PDO $pdo, array $data): void {
    $applicationId = intval($data['application_id'] ?? ($data['app_id'] ?? 0));
    $repairerId = intval($data['repairer_id'] ?? 0);

    if (!$applicationId || !$repairerId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'application_id (or app_id) and repairer_id are required']);
        return;
    }

    // Only allow withdrawal of own pending applications
    $stmt = $pdo->prepare("DELETE FROM repairer_applications WHERE application_id = :id AND repairer_id = :r AND status = 'pending'");
    $stmt->execute([':id' => $applicationId, ':r' => $repairerId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Application withdrawn']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Application not found or cannot be withdrawn']);
    }
}
