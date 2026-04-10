<?php
// NotificationModel.php - Handles all database operations for Notification table

class NotificationModel
{
    private $pdo;
    private ?string $resolvedTable = null;
    private ?array $columnCache = null;
    private bool $creatorColumnsEnsured = false;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function tableName(): string
    {
        if ($this->resolvedTable !== null) {
            return $this->resolvedTable;
        }

        // Resolve table name safely for case-sensitive filesystems (Linux).
        // Common variants seen in this repo: `notification` (schema) and `Notification` (legacy code).
        try {
            $stmt = $this->pdo->prepare(
                "SELECT TABLE_NAME
                 FROM INFORMATION_SCHEMA.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME IN ('notification','Notification')
                 LIMIT 1"
            );
            $stmt->execute();
            $name = $stmt->fetchColumn();
            $this->resolvedTable = $name ? (string)$name : 'notification';
        } catch (Throwable $e) {
            // If INFORMATION_SCHEMA is not accessible, default to schema name.
            $this->resolvedTable = 'notification';
        }

        return $this->resolvedTable;
    }

    private function ensureColumnCache(): void
    {
        if ($this->columnCache !== null) {
            return;
        }

        $this->columnCache = [];
        try {
            $table = $this->tableName();
            $stmt = $this->pdo->prepare(
                'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
            );
            $stmt->execute([$table]);
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($cols as $c) {
                if (is_string($c) && $c !== '') {
                    $this->columnCache[strtolower($c)] = true;
                }
            }
        } catch (Throwable $e) {
            // Leave cache empty; callers will fall back.
        }
    }

    private function hasColumn(string $column): bool
    {
        $this->ensureColumnCache();
        return (bool)($this->columnCache[strtolower($column)] ?? false);
    }

    private function ensureCreatorColumns(): void
    {
        if ($this->creatorColumnsEnsured) {
            return;
        }
        $this->creatorColumnsEnsured = true;

        try {
            $table = $this->tableName();
            $columnsToAdd = [];

            if (!$this->hasColumn('created_by_id')) {
                $columnsToAdd[] = 'ADD COLUMN created_by_id INT(11) DEFAULT NULL';
            }
            if (!$this->hasColumn('created_by_role')) {
                $columnsToAdd[] = "ADD COLUMN created_by_role ENUM('admin','moderator','company','repairer','user') DEFAULT NULL";
            }
            if (!$this->hasColumn('created_by_name')) {
                $columnsToAdd[] = 'ADD COLUMN created_by_name VARCHAR(255) DEFAULT NULL';
            }

            if (!empty($columnsToAdd)) {
                $this->pdo->exec('ALTER TABLE `'.$table.'` ' . implode(', ', $columnsToAdd));
                $this->columnCache = null;
            }
        } catch (Throwable $e) {
            // Keep working even if schema migration is unavailable.
        }
    }

    private function creatorSelect(): string
    {
        $select = '';
        if ($this->hasColumn('created_by_id')) {
            $select .= ', created_by_id';
        }
        if ($this->hasColumn('created_by_role')) {
            $select .= ', created_by_role';
        }
        if ($this->hasColumn('created_by_name')) {
            $select .= ', created_by_name';
        }
        return $select;
    }

    private function normalizeCreator(array $creator = []): array
    {
        return [
            'created_by_id' => isset($creator['id']) ? (int)$creator['id'] : null,
            'created_by_role' => isset($creator['role']) ? (string)$creator['role'] : null,
            'created_by_name' => isset($creator['name']) ? (string)$creator['name'] : null,
        ];
    }

    private function canActorModify(array $existing, array $actor = []): bool
    {
        $actorRole = strtolower((string)($actor['role'] ?? ''));
        if ($actorRole !== 'moderator') {
            return true;
        }

        $createdByRole = strtolower((string)($existing['created_by_role'] ?? ''));
        return $createdByRole !== 'admin';
    }

    private function createdAtSelect(): string
    {
        // Normalize a `created_at` field for consumers.
        if ($this->hasColumn('send_date')) {
            return 'send_date AS created_at';
        }
        if ($this->hasColumn('date') && $this->hasColumn('time')) {
            return "CONCAT(date, ' ', time) AS created_at";
        }
        if ($this->hasColumn('created_at')) {
            return 'created_at';
        }
        return 'NULL AS created_at';
    }

    /**
     * Get all notifications from database
     * @return array Array of notification records
     */
    public function getAllNotifications()
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $createdAt = $this->createdAtSelect();

        $orderBy = '';
        if ($this->hasColumn('date') && $this->hasColumn('time')) {
            $orderBy = 'ORDER BY date DESC, time DESC';
        } elseif ($this->hasColumn('send_date')) {
            $orderBy = 'ORDER BY send_date DESC';
        } elseif ($this->hasColumn('created_at')) {
            $orderBy = 'ORDER BY created_at DESC';
        } else {
            $orderBy = 'ORDER BY notification_id DESC';
        }

        $select = "notification_id, title, message, recipient_type, status, {$createdAt}" . $this->creatorSelect();
        if ($this->hasColumn('date')) $select .= ', date';
        if ($this->hasColumn('time')) $select .= ', time';
        if ($this->hasColumn('send_date')) $select .= ', send_date';

        $stmt = $this->pdo->prepare("SELECT {$select} FROM `{$table}` {$orderBy}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent notifications with limit, filtered by user
     * @param int $user_id Recipient ID
     * @param string $user_type Recipient Type
     * @param int $limit Number of notifications to retrieve
     * @return array Array of notification records
     */
    public function getRecentNotifications($user_id, $user_type, $limit = 5)
    {
        // error_log("[MODEL] getRecentNotifications called for User: $user_id Type: $user_type Limit: $limit");
        try {
            $this->ensureCreatorColumns();
            $limit = (int)$limit;

            $table = $this->tableName();
            $createdAt = $this->createdAtSelect();

            // Prefer per-recipient logic only if those columns exist.
            $hasRecipientId = $this->hasColumn('recipient_id');
            $hasIsRead = $this->hasColumn('is_read');

            $select = "notification_id, title, message, recipient_type, status, {$createdAt}" . $this->creatorSelect();
            if ($hasIsRead) {
                $select .= ', is_read';
            }
            if ($this->hasColumn('date')) $select .= ', date';
            if ($this->hasColumn('time')) $select .= ', time';
            if ($this->hasColumn('send_date')) $select .= ', send_date';

            // Logic:
            // - If schema supports recipient_id: include per-user + broadcasts
            // - Else: treat notifications as broadcast only (recipient_type + all)
            if ($hasRecipientId) {
                $sql = "
                    SELECT {$select}
                    FROM `{$table}`
                    WHERE
                        (recipient_id = :user_id AND recipient_type = :user_type)
                        OR (recipient_id IS NULL AND recipient_type = :user_type)
                        OR (recipient_type = 'all')
                    ORDER BY " . ($this->hasColumn('date') && $this->hasColumn('time')
                        ? 'date DESC, time DESC'
                        : ($this->hasColumn('send_date') ? 'send_date DESC' : 'notification_id DESC')) . "
                    LIMIT :limit
                ";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $sql = "
                SELECT {$select}
                FROM `{$table}`
                WHERE recipient_type IN ('all', :user_type)
                ORDER BY " . ($this->hasColumn('send_date')
                    ? 'send_date DESC'
                    : ($this->hasColumn('created_at') ? 'created_at DESC' : 'notification_id DESC')) . "
                LIMIT :limit
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("[MODEL ERROR] getRecentNotifications failed: " . $e->getMessage());
            // Fallback for missing columns (if migration hasn't run)
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                 return $this->getRecentNotificationsFallback($limit);
            }
            throw $e;
        }
    }

    /**
     * Fallback method if schema is old
     */
    private function getRecentNotificationsFallback($limit) {
        $table = $this->tableName();
        $stmt = $this->pdo->prepare("SELECT *, send_date as created_at FROM `{$table}` ORDER BY send_date DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get notification by ID
     * @param int $notification_id
     * @return array|false Notification record or false if not found
     */
    public function getNotificationById($notification_id)
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $createdAt = $this->createdAtSelect();
        $select = "notification_id, title, message, recipient_type, status, {$createdAt}" . $this->creatorSelect();
        if ($this->hasColumn('send_date')) $select .= ', send_date';
        if ($this->hasColumn('date')) $select .= ', date';
        if ($this->hasColumn('time')) $select .= ', time';

        $stmt = $this->pdo->prepare("SELECT {$select} FROM `{$table}` WHERE notification_id = ?");
        $stmt->execute([$notification_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get notifications by status
     * @param string $status Status to filter by
     * @return array Array of notification records
     */
    public function getNotificationsByStatus($status)
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $createdAt = $this->createdAtSelect();
        $orderBy = $this->hasColumn('send_date') ? 'send_date DESC' : ($this->hasColumn('date') && $this->hasColumn('time') ? 'date DESC, time DESC' : 'notification_id DESC');
        $select = "notification_id, title, message, recipient_type, status, {$createdAt}" . $this->creatorSelect();
        if ($this->hasColumn('send_date')) $select .= ', send_date';
        if ($this->hasColumn('date')) $select .= ', date';
        if ($this->hasColumn('time')) $select .= ', time';

        $stmt = $this->pdo->prepare("SELECT {$select} FROM `{$table}` WHERE status = ? ORDER BY {$orderBy}");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get notifications by recipient type
     * @param string $recipient_type Recipient type to filter by
     * @return array Array of notification records
     */
    public function getNotificationsByRecipient($recipient_type)
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $createdAt = $this->createdAtSelect();
        $orderBy = $this->hasColumn('send_date') ? 'send_date DESC' : ($this->hasColumn('date') && $this->hasColumn('time') ? 'date DESC, time DESC' : 'notification_id DESC');
        $select = "notification_id, title, message, recipient_type, status, {$createdAt}" . $this->creatorSelect();
        if ($this->hasColumn('send_date')) $select .= ', send_date';
        if ($this->hasColumn('date')) $select .= ', date';
        if ($this->hasColumn('time')) $select .= ', time';

        $stmt = $this->pdo->prepare("SELECT {$select} FROM `{$table}` WHERE recipient_type = ? ORDER BY {$orderBy}");
        $stmt->execute([$recipient_type]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new notification
     * @param string $title
     * @param string $message
     * @param string $recipient_type
     * @param string $status
     * @return int Last insert ID
     */
    public function createNotification($title, $message, $recipient_type, $status = 'sent', array $creator = [])
    {
        error_log("[MODEL] createNotification - Title: $title, Recipient: $recipient_type, Status: $status");
        try {
            $this->ensureCreatorColumns();
            $table = $this->tableName();
            $creatorData = $this->normalizeCreator($creator);

            // Schema variants:
            // - Base schema: send_date timestamp (default CURRENT_TIMESTAMP)
            // - Legacy schema: date + time columns
            if ($this->hasColumn('date') && $this->hasColumn('time')) {
                $columns = ['title', 'message', 'recipient_type', 'status', 'date', 'time'];
                $values = ['?', '?', '?', '?', 'CURDATE()', 'CURTIME()'];
                $params = [$title, $message, $recipient_type, $status];
                if ($this->hasColumn('created_by_id')) { $columns[] = 'created_by_id'; $values[] = '?'; $params[] = $creatorData['created_by_id']; }
                if ($this->hasColumn('created_by_role')) { $columns[] = 'created_by_role'; $values[] = '?'; $params[] = $creatorData['created_by_role']; }
                if ($this->hasColumn('created_by_name')) { $columns[] = 'created_by_name'; $values[] = '?'; $params[] = $creatorData['created_by_name']; }
                $stmt = $this->pdo->prepare("INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")");
                $stmt->execute($params);
            } else {
                // send_date will default; if it exists we don't need to set it.
                $columns = ['title', 'message', 'recipient_type', 'status'];
                $placeholders = ['?', '?', '?', '?'];
                $params = [$title, $message, $recipient_type, $status];
                if ($this->hasColumn('created_by_id')) { $columns[] = 'created_by_id'; $placeholders[] = '?'; $params[] = $creatorData['created_by_id']; }
                if ($this->hasColumn('created_by_role')) { $columns[] = 'created_by_role'; $placeholders[] = '?'; $params[] = $creatorData['created_by_role']; }
                if ($this->hasColumn('created_by_name')) { $columns[] = 'created_by_name'; $placeholders[] = '?'; $params[] = $creatorData['created_by_name']; }
                $stmt = $this->pdo->prepare("INSERT INTO `{$table}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")");
                $stmt->execute($params);
            }

            $newId = $this->pdo->lastInsertId();
            error_log("[MODEL] Notification created successfully with ID: $newId");
            return $newId;
        } catch (PDOException $e) {
            error_log("[MODEL ERROR] createNotification failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update notification
     * @param int $notification_id
     * @param string $title
     * @param string $message
     * @param string $recipient_type
     * @return bool Success status
     */
    public function updateNotification($notification_id, $title, $message, $recipient_type, array $actor = [])
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $existing = $this->getNotificationById($notification_id);
        if (!$existing) {
            return false;
        }
        if (!$this->canActorModify($existing, $actor)) {
            return false;
        }
        $stmt = $this->pdo->prepare("
            UPDATE `{$table}` 
            SET title = ?, message = ?, recipient_type = ? 
            WHERE notification_id = ?
        ");
        $result = $stmt->execute([$title, $message, $recipient_type, $notification_id]);
        
        // Debug logging
        error_log("UPDATE Notification - ID: $notification_id, Title: $title, Recipient: $recipient_type, Rows affected: " . $stmt->rowCount());
        
        return $result;
    }

    /**
     * Update notification status
     * @param int $notification_id
     * @param string $status
     * @return bool Success status
     */
    public function updateNotificationStatus($notification_id, $status)
    {
        $table = $this->tableName();
        $stmt = $this->pdo->prepare("
            UPDATE `{$table}` 
            SET status = ? 
            WHERE notification_id = ?
        ");
        return $stmt->execute([$status, $notification_id]);
    }

    /**
     * Delete notification
     * @param int $notification_id
     * @return int Number of rows affected
     */
    public function deleteNotification($notification_id, array $actor = [])
    {
        $this->ensureCreatorColumns();
        $table = $this->tableName();
        $existing = $this->getNotificationById($notification_id);
        if (!$existing) {
            return 0;
        }
        if (!$this->canActorModify($existing, $actor)) {
            return 0;
        }
        $stmt = $this->pdo->prepare("DELETE FROM `{$table}` WHERE notification_id = ?");
        $stmt->execute([$notification_id]);
        return $stmt->rowCount();
    }

    /**
     * Get notification statistics
     * @return array Statistics about notifications
     */
    public function getNotificationStats()
    {
        $table = $this->tableName();
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM `{$table}`
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Get notification count for specific user and type
     * @param int $user_id
     * @param string $user_type
     * @return int Count of notifications
     */
    public function getNotificationCount($user_id, $user_type)
    {
        try {
            $table = $this->tableName();
            if ($this->hasColumn('recipient_id')) {
                $sql = "
                    SELECT COUNT(*)
                    FROM `{$table}`
                    WHERE
                        (recipient_id = :user_id AND recipient_type = :user_type)
                        OR (recipient_id IS NULL AND recipient_type = :user_type)
                        OR (recipient_type = 'all')
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
                $stmt->execute();
                return (int)$stmt->fetchColumn();
            }

            $sql = "SELECT COUNT(*) FROM `{$table}` WHERE recipient_type IN ('all', :user_type)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
             // Fallback
             if (strpos($e->getMessage(), 'Unknown column') !== false) {
                 return 0; // Or global count
             }
             throw $e;
        }
    }

    /**
     * Get unread notification count for specific user and type
     */
    public function getUnreadNotificationCount($user_id, $user_type)
    {
        try {
            $table = $this->tableName();

            // If schema has is_read, compute properly; otherwise treat as 0.
            if (!$this->hasColumn('is_read')) {
                return 0;
            }

            if ($this->hasColumn('recipient_id')) {
                $sql = "
                    SELECT COUNT(*)
                    FROM `{$table}`
                    WHERE
                        (
                            (recipient_id = :user_id AND recipient_type = :user_type)
                            OR (recipient_id IS NULL AND recipient_type = :user_type)
                            OR (recipient_type = 'all')
                        )
                        AND (is_read = 0 OR is_read IS NULL)
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
                $stmt->execute();
                return (int)$stmt->fetchColumn();
            }

            // Broadcast-only schema with is_read.
            $sql = "
                SELECT COUNT(*)
                FROM `{$table}`
                WHERE recipient_type IN ('all', :user_type)
                  AND (is_read = 0 OR is_read IS NULL)
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
            $stmt->execute();
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                return 0;
            }
            throw $e;
        }
    }

    /**
     * Mark all notifications as read that are visible to this user.
     */
    public function markAllRead($user_id, $user_type)
    {
        try {
            $table = $this->tableName();
            if (!$this->hasColumn('is_read')) {
                return 0;
            }

            if ($this->hasColumn('recipient_id')) {
                $sql = "
                    UPDATE `{$table}`
                    SET is_read = 1
                    WHERE
                        (
                            (recipient_id = :user_id AND recipient_type = :user_type)
                            OR (recipient_id IS NULL AND recipient_type = :user_type)
                            OR (recipient_type = 'all')
                        )
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->rowCount();
            }

            $sql = "UPDATE `{$table}` SET is_read = 1 WHERE recipient_type IN ('all', :user_type)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':user_type', $user_type, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                return 0;
            }
            throw $e;
        }
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead($notification_id)
    {
        try {
            $table = $this->tableName();
            if (!$this->hasColumn('is_read')) {
                return 0;
            }
            $stmt = $this->pdo->prepare("UPDATE `{$table}` SET is_read = 1 WHERE notification_id = ?");
            $stmt->execute([(int)$notification_id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                return 0;
            }
            throw $e;
        }
    }
}
