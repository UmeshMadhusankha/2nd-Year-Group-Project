<?php

class SystemNotificationService
{
    private PDO $pdo;
    private ?string $resolvedTable = null;
    private ?array $columnCache = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function tableName(): string
    {
        if ($this->resolvedTable !== null) {
            return $this->resolvedTable;
        }

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
            $stmt = $this->pdo->prepare(
                'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?'
            );
            $stmt->execute([$this->tableName()]);
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $column) {
                if (is_string($column) && $column !== '') {
                    $this->columnCache[strtolower($column)] = true;
                }
            }
        } catch (Throwable $e) {
            $this->columnCache = [];
        }
    }

    private function hasColumn(string $column): bool
    {
        $this->ensureColumnCache();
        return (bool)($this->columnCache[strtolower($column)] ?? false);
    }

    private function ensureRecipientColumns(): void
    {
        try {
            $toAdd = [];
            if (!$this->hasColumn('recipient_id')) {
                $toAdd[] = 'ADD COLUMN recipient_id INT(11) DEFAULT NULL';
            }
            if (!$this->hasColumn('is_read')) {
                $toAdd[] = 'ADD COLUMN is_read TINYINT(1) DEFAULT 0';
            }

            if (!empty($toAdd)) {
                $this->pdo->exec('ALTER TABLE `'.$this->tableName().'` ' . implode(', ', $toAdd));
                $this->columnCache = null;
                $this->ensureColumnCache();
            }
        } catch (Throwable $e) {
            // Keep backward compatible if schema migration is not allowed.
        }
    }

    public function notify(string $title, string $message, string $recipientType = 'all', ?int $recipientId = null, array $creator = []): bool
    {
        try {
            $this->ensureRecipientColumns();

            $recipientType = strtolower(trim($recipientType));
            if (!in_array($recipientType, ['all', 'user', 'company', 'repairer'], true)) {
                $recipientType = 'all';
            }

            $columns = ['title', 'message', 'recipient_type', 'status'];
            $values = ['?', '?', '?', '?'];
            $params = [$title, $message, $recipientType, 'sent'];

            if ($this->hasColumn('recipient_id')) {
                $columns[] = 'recipient_id';
                $values[] = '?';
                $params[] = $recipientId;
            }
            if ($this->hasColumn('is_read')) {
                $columns[] = 'is_read';
                $values[] = '?';
                $params[] = 0;
            }
            if ($this->hasColumn('created_by_id')) {
                $columns[] = 'created_by_id';
                $values[] = '?';
                $params[] = isset($creator['id']) ? (int)$creator['id'] : null;
            }
            if ($this->hasColumn('created_by_role')) {
                $columns[] = 'created_by_role';
                $values[] = '?';
                $params[] = isset($creator['role']) ? (string)$creator['role'] : null;
            }
            if ($this->hasColumn('created_by_name')) {
                $columns[] = 'created_by_name';
                $values[] = '?';
                $params[] = isset($creator['name']) ? (string)$creator['name'] : null;
            }

            $sql = "INSERT INTO `{$this->tableName()}` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Throwable $e) {
            error_log('[SystemNotificationService] notify failed: ' . $e->getMessage());
            return false;
        }
    }
}
