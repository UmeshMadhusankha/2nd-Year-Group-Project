<?php

/**
 * Lightweight system audit logger.
 *
 * Goals:
 * - Central place to log actions across modules
 * - Safe defaults: redact sensitive fields; avoid breaking requests
 * - Minimal coupling: can be called from APIs/controllers, and can auto-log API requests
 */
class AuditLogger
{
    private static bool $autoLogged = false;
    private static bool $disabledMissingTable = false;

    /**
     * Auto-log API requests (best-effort).
     *
     * This is intentionally conservative to avoid noise:
     * - Only logs requests under /FixLanka/api/
     * - Skips GET requests unless they include an explicit "action" parameter
     */
    public static function autoLogApiRequest(PDO $pdo): void
    {
        if (self::$autoLogged) {
            return;
        }
        self::$autoLogged = true;

        $requestUri = (string)($_SERVER['REQUEST_URI'] ?? '');
        if ($requestUri === '' || strpos($requestUri, '/FixLanka/api/') === false) {
            return;
        }

        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $action = (string)($_POST['action'] ?? $_GET['action'] ?? '');

        if ($method === 'GET' && $action === '') {
            return;
        }

        $endpoint = strtok($requestUri, '?') ?: $requestUri;
        $basename = basename(parse_url($endpoint, PHP_URL_PATH) ?: $endpoint);
        $actionName = $action !== '' ? $action : strtolower($method);

        $actorUserId = $_SESSION['user_id'] ?? null;
        $actorRole = $_SESSION['user_role'] ?? null;

        $details = [
            'query_allow' => self::extractAllowedIds($_GET ?? []),
            'body_allow' => self::extractAllowedIds($_POST ?? []),
            'body_keys' => self::safeKeys($_POST ?? []),
        ];

        self::log(
            $pdo,
            'api.' . $basename . '.' . $actionName,
            $details,
            null,
            null,
            null,
            $actorUserId,
            $actorRole
        );
    }

    /**
     * Log an audit event (best-effort; does not throw).
     *
     * @param PDO $pdo
     * @param string $action
     * @param array|null $details Will be JSON encoded. Sensitive keys are removed.
     * @param string|null $entityType
     * @param string|int|null $entityId
     * @param int|null $statusCode
     * @param int|string|null $actorUserId
     * @param string|null $actorRole
     */
    public static function log(
        PDO $pdo,
        string $action,
        ?array $details = null,
        ?string $entityType = null,
        $entityId = null,
        ?int $statusCode = null,
        $actorUserId = null,
        ?string $actorRole = null
    ): void {
        if (self::$disabledMissingTable) {
            return;
        }
        try {
            $requestId = self::getOrCreateRequestId();

            $httpMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? ''));
            $endpoint = (string)($_SERVER['REQUEST_URI'] ?? '');
            if ($endpoint !== '') {
                $endpoint = strtok($endpoint, '?') ?: $endpoint;
            }

            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;

            $cleanDetails = $details ? self::redactSensitive($details) : null;
            $detailsJson = $cleanDetails ? json_encode($cleanDetails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null;

            $stmt = $pdo->prepare(
                'INSERT INTO system_audit_log (
                    request_id, actor_user_id, actor_role, action, entity_type, entity_id,
                    http_method, endpoint, ip_address, user_agent, status_code, details
                 ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );

            $stmt->execute([
                $requestId,
                self::toNullableInt($actorUserId),
                $actorRole !== null ? (string)$actorRole : null,
                $action,
                $entityType,
                $entityId !== null ? (string)$entityId : null,
                $httpMethod !== '' ? $httpMethod : null,
                $endpoint !== '' ? $endpoint : null,
                $ip,
                $ua,
                $statusCode,
                $detailsJson,
            ]);
        } catch (Throwable $e) {
            $msg = $e->getMessage();
            if (
                stripos($msg, 'system_audit_log') !== false &&
                (stripos($msg, "doesn't exist") !== false || stripos($msg, 'base table or view not found') !== false)
            ) {
                self::$disabledMissingTable = true;
                // Avoid spamming logs; one message is enough.
                error_log('[AuditLogger] system_audit_log table missing; audit logging temporarily disabled until schema is applied.');
                return;
            }
            // Never break the main request because audit logging failed.
            error_log('[AuditLogger] Failed to log audit event: ' . $msg);
        }
    }

    private static function getOrCreateRequestId(): string
    {
        if (isset($GLOBALS['__AUDIT_REQUEST_ID']) && is_string($GLOBALS['__AUDIT_REQUEST_ID']) && $GLOBALS['__AUDIT_REQUEST_ID'] !== '') {
            return $GLOBALS['__AUDIT_REQUEST_ID'];
        }

        $id = self::uuidv4();
        $GLOBALS['__AUDIT_REQUEST_ID'] = $id;
        return $id;
    }

    private static function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        $hex = bin2hex($data);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }

    private static function toNullableInt($v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_int($v)) {
            return $v;
        }
        if (is_string($v) && ctype_digit($v)) {
            return (int)$v;
        }
        if (is_numeric($v)) {
            return (int)$v;
        }
        return null;
    }

    private static function safeKeys(array $arr): array
    {
        $keys = [];
        foreach ($arr as $k => $_) {
            if (is_string($k)) {
                $keys[] = $k;
            }
        }
        return $keys;
    }

    /**
     * Extract commonly-used ID fields from input to make logs useful
     * without storing full request payload.
     */
    private static function extractAllowedIds(array $arr): array
    {
        $allow = [
            'id',
            'user_id',
            'company_id',
            'repairer_id',
            'project_id',
            'contract_id',
            'quotation_id',
            'invoice_id',
            'payment_id',
            'milestone_id',
            'request_id',
            'job_request_id',
        ];

        $out = [];
        foreach ($allow as $k) {
            if (array_key_exists($k, $arr)) {
                $out[$k] = is_scalar($arr[$k]) ? $arr[$k] : null;
            }
        }
        return $out;
    }

    private static function redactSensitive(array $data): array
    {
        $sensitivePattern = '/(password|pass|token|secret|authorization|bearer|otp|cvv|card|pan|pin)/i';

        $out = [];
        foreach ($data as $k => $v) {
            if (is_string($k) && preg_match($sensitivePattern, $k)) {
                continue;
            }

            if (is_array($v)) {
                $out[$k] = self::redactSensitive($v);
            } else {
                // Avoid logging huge blobs
                if (is_string($v) && strlen($v) > 2000) {
                    $out[$k] = substr($v, 0, 2000) . '…';
                } else {
                    $out[$k] = $v;
                }
            }
        }
        return $out;
    }
}
