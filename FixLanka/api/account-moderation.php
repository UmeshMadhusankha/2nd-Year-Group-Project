<?php
ini_set('display_errors', '0');
ini_set('html_errors', '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

requireRole(['admin', 'moderator']);

$redirectUrl = '/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php';

function wantsJsonResponse(): bool
{
    $accept = strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? ''));
    $xhr = strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''));
    return str_contains($accept, 'application/json') || $xhr === 'xmlhttprequest';
}

function respondAndExit(bool $success, string $message, int $statusCode = 200, array $extra = []): void
{
    global $redirectUrl;

    if (wantsJsonResponse()) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $success,
            'message' => $message
        ], $extra));
        exit;
    }

    $queryKey = $success ? 'success' : 'error';
    $separator = str_contains($redirectUrl, '?') ? '&' : '?';
    header('Location: ' . $redirectUrl . $separator . http_build_query([$queryKey => $message]));
    exit;
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    try {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table
               AND COLUMN_NAME = :column
             LIMIT 1"
        );
        $stmt->execute([
            ':table' => $table,
            ':column' => $column
        ]);
        return (bool)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return false;
    }
}

function ensureModerationLogTable(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS account_moderation_log (
            log_id INT AUTO_INCREMENT PRIMARY KEY,
            account_type VARCHAR(30) NOT NULL,
            account_id INT NOT NULL,
            action_type ENUM('suspend','ban','restore') NOT NULL,
            reason TEXT NULL,
            notes TEXT NULL,
            suspended_until DATETIME NULL,
            acted_by_role VARCHAR(30) NULL,
            acted_by_id INT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_account_lookup (account_type, account_id, created_at),
            INDEX idx_action_type (action_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );
}

function insertModerationLog(PDO $pdo, string $accountType, int $accountId, string $action, ?string $reason, ?string $notes, ?string $suspendedUntil): void
{
    ensureModerationLogTable($pdo);

    $actorRole = (string)($_SESSION['user_role'] ?? 'system');
    $actorId = (int)($_SESSION['user_id'] ?? 0);

    $stmt = $pdo->prepare(
        "INSERT INTO account_moderation_log
        (account_type, account_id, action_type, reason, notes, suspended_until, acted_by_role, acted_by_id)
        VALUES (:account_type, :account_id, :action_type, :reason, :notes, :suspended_until, :acted_by_role, :acted_by_id)"
    );

    $stmt->execute([
        ':account_type' => strtolower($accountType),
        ':account_id' => $accountId,
        ':action_type' => $action,
        ':reason' => $reason,
        ':notes' => $notes,
        ':suspended_until' => $suspendedUntil,
        ':acted_by_role' => $actorRole,
        ':acted_by_id' => $actorId > 0 ? $actorId : null,
    ]);
}

if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
    respondAndExit(false, 'Method not allowed', 405);
}

$action = strtolower(trim((string)($_POST['action'] ?? '')));
$accountId = (int)($_POST['account_id'] ?? 0);
$accountTypeRaw = trim((string)($_POST['account_type'] ?? ''));
$reason = trim((string)($_POST['reason'] ?? ''));
$notes = trim((string)($_POST['notes'] ?? ''));

if (!in_array($action, ['suspend', 'ban', 'restore'], true)) {
    respondAndExit(false, 'Invalid action.', 400);
}

if ($accountId <= 0) {
    respondAndExit(false, 'Invalid account selected.', 400);
}

if ($action !== 'restore' && mb_strlen($reason) < 10) {
    respondAndExit(false, 'Reason must be at least 10 characters.', 400);
}

$normalizedType = strtolower($accountTypeRaw);
$table = '';
$idColumn = '';

switch ($normalizedType) {
    case 'user':
        $table = 'user';
        $idColumn = 'user_id';
        break;
    case 'repairer':
        $table = 'repairer';
        $idColumn = 'repairer_id';
        break;
    case 'company':
        $table = 'company';
        $idColumn = 'company_id';
        break;
    default:
        respondAndExit(false, 'Unsupported account type.', 400);
}

try {
    $moderationUntil = null;
    $statusValue = 'ACTIVE';
    $isPermanent = 0;

    if ($action === 'suspend') {
        $duration = trim((string)($_POST['duration'] ?? ''));
        $customDays = (int)($_POST['custom_days'] ?? 0);
        $days = $duration === 'custom' ? $customDays : (int)$duration;

        if ($days <= 0) {
            respondAndExit(false, 'Please choose a valid suspension duration.', 400);
        }

        $statusValue = 'SUSPENDED';
        $moderationUntil = date('Y-m-d H:i:s', strtotime('+' . $days . ' days'));
        $isPermanent = 0;
    } elseif ($action === 'ban') {
        $statusValue = 'BANNED';
        $isPermanent = 1;
        $moderationUntil = null;
    } else {
        $statusValue = 'ACTIVE';
        $isPermanent = 0;
        $moderationUntil = null;
    }

    // 1. Update/Insert into Centralized Moderation Status Table
    $stmt = $pdo->prepare(
        "INSERT INTO account_moderation_status 
        (account_id, account_type, account_status, banned_permanent, suspended_until, moderation_reason, updated_by)
        VALUES (:id, :type, :status, :perm, :until, :reason, :by)
        ON DUPLICATE KEY UPDATE 
            account_status = VALUES(account_status),
            banned_permanent = VALUES(banned_permanent),
            suspended_until = VALUES(suspended_until),
            moderation_reason = VALUES(moderation_reason),
            updated_by = VALUES(updated_by),
            last_updated = CURRENT_TIMESTAMP"
    );

    $stmt->execute([
        ':id' => $accountId,
        ':type' => ucfirst($normalizedType),
        ':status' => $statusValue,
        ':perm' => $isPermanent,
        ':until' => $moderationUntil,
        ':reason' => $action === 'restore' ? null : $reason,
        ':by' => (string)($_SESSION['username'] ?? 'admin')
    ]);

    // 2. Legacy Support: Update individual tables IF columns exist
    $hasAccountStatus = columnExists($pdo, $table, 'account_status');
    $hasReason = columnExists($pdo, $table, 'moderation_reason');
    $hasSuspendedUntil = columnExists($pdo, $table, 'suspended_until');
    $hasBannedPermanent = columnExists($pdo, $table, 'banned_permanent');

    if ($hasAccountStatus || $hasReason || $hasSuspendedUntil || $hasBannedPermanent) {
        $legacyUpdates = [];
        $legacyParams = [':id' => $accountId];

        if ($hasAccountStatus) {
            $legacyUpdates[] = "account_status = :status";
            $legacyParams[':status'] = $statusValue;
        }
        if ($hasReason) {
            $legacyUpdates[] = "moderation_reason = :reason";
            $legacyParams[':reason'] = ($action === 'restore' ? null : $reason);
        }
        if ($hasSuspendedUntil) {
            $legacyUpdates[] = "suspended_until = :until";
            $legacyParams[':until'] = $moderationUntil;
        }
        if ($hasBannedPermanent) {
            $legacyUpdates[] = "banned_permanent = :perm";
            $legacyParams[':perm'] = $isPermanent;
        }

        if (!empty($legacyUpdates)) {
            $sql = "UPDATE `{$table}` SET " . implode(', ', $legacyUpdates) . " WHERE `{$idColumn}` = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($legacyParams);
        }
    }

    // 3. Insert into Moderation Log
    insertModerationLog(
        $pdo,
        $normalizedType,
        $accountId,
        $action,
        $action === 'restore' ? null : $reason,
        $notes !== '' ? $notes : null,
        $action === 'suspend' ? $moderationUntil : null
    );

    $message = match ($action) {
        'suspend' => 'Account suspended successfully.',
        'ban' => 'Account banned successfully.',
        default => 'Account restored successfully.'
    };

    respondAndExit(true, $message, 200);
} catch (Throwable $e) {
    error_log('Account moderation API error: ' . $e->getMessage());
    respondAndExit(false, 'Failed to apply moderation action: ' . $e->getMessage(), 500);
}
