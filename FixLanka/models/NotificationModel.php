<?php
// NotificationModel.php - Handles all database operations for Notification table

class NotificationModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all notifications from database
     * @return array Array of notification records
     */
    public function getAllNotifications()
    {
        // Prefer current schema: notification.send_date
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    notification_id,
                    title,
                    message,
                    send_date AS created_at,
                    recipient_type,
                    status
                FROM notification
                ORDER BY send_date DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback to legacy/alternate schemas if needed
            $stmt = $this->pdo->prepare("SELECT * FROM Notification ORDER BY send_date DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
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
        try {
            $limit = (int)$limit;

            // Best-effort for advanced schemas (recipient_id/date/time/is_read)
            try {
                $sql = "
                    SELECT notification_id, title, message, date, time,
                           CONCAT(date, ' ', time) as created_at,
                           recipient_type, status, is_read
                    FROM Notification
                    WHERE
                        (recipient_id = :user_id AND recipient_type = :user_type)
                        OR (recipient_id IS NULL AND recipient_type = :user_type)
                        OR (recipient_type = 'all')
                    ORDER BY date DESC, time DESC
                    LIMIT :limit
                ";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', (string)$user_type, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Fall back to current schema in create_database.sql: notification(send_date, recipient_type)
                $sql = "
                    SELECT
                        notification_id,
                        title,
                        message,
                        send_date AS created_at,
                        recipient_type,
                        status
                    FROM notification
                    WHERE recipient_type = 'all' OR recipient_type = :user_type
                    ORDER BY send_date DESC
                    LIMIT :limit
                ";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_type', (string)$user_type, PDO::PARAM_STR);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("[MODEL ERROR] getRecentNotifications failed: " . $e->getMessage());
            return $this->getRecentNotificationsFallback($limit);
        }
    }

    /**
     * Fallback method if schema is old
     */
    private function getRecentNotificationsFallback($limit) {
        try {
            $stmt = $this->pdo->prepare("SELECT notification_id, title, message, send_date AS created_at, recipient_type, status FROM notification ORDER BY send_date DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $stmt = $this->pdo->prepare("SELECT *, send_date as created_at FROM Notification ORDER BY send_date DESC LIMIT :limit");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Get notification by ID
     * @param int $notification_id
     * @return array|false Notification record or false if not found
     */
    public function getNotificationById($notification_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT notification_id, title, message, send_date, recipient_type, status 
            FROM Notification 
            WHERE notification_id = ?
        ");
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
        $stmt = $this->pdo->prepare("
            SELECT notification_id, title, message, send_date, recipient_type, status 
            FROM Notification 
            WHERE status = ? 
            ORDER BY send_date DESC
        ");
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
        $stmt = $this->pdo->prepare("
            SELECT notification_id, title, message, send_date, recipient_type, status 
            FROM Notification 
            WHERE recipient_type = ? 
            ORDER BY send_date DESC
        ");
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
    public function createNotification($title, $message, $recipient_type, $status = 'sent')
    {
        error_log("[MODEL] createNotification - Title: $title, Recipient: $recipient_type, Status: $status");
        try {
            // Current schema uses send_date timestamp
            $stmt = $this->pdo->prepare("
                INSERT INTO notification (title, message, recipient_type, status)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $message, $recipient_type, $status]);
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
    public function updateNotification($notification_id, $title, $message, $recipient_type)
    {
        $stmt = $this->pdo->prepare("
            UPDATE Notification 
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
        $stmt = $this->pdo->prepare("
            UPDATE Notification 
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
    public function deleteNotification($notification_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Notification WHERE notification_id = ?");
        $stmt->execute([$notification_id]);
        return $stmt->rowCount();
    }

    /**
     * Get notification statistics
     * @return array Statistics about notifications
     */
    public function getNotificationStats()
    {
        $stmt = $this->pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            FROM Notification
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
            // Best-effort advanced schema
            try {
                $sql = "
                    SELECT COUNT(*)
                    FROM Notification
                    WHERE
                        (recipient_id = :user_id AND recipient_type = :user_type)
                        OR (recipient_id IS NULL AND recipient_type = :user_type)
                        OR (recipient_type = 'all')
                ";

                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_id', (int)$user_id, PDO::PARAM_INT);
                $stmt->bindValue(':user_type', (string)$user_type, PDO::PARAM_STR);
                $stmt->execute();
                return (int)$stmt->fetchColumn();
            } catch (PDOException $e) {
                // Current schema: only recipient_type
                $sql = "SELECT COUNT(*) FROM notification WHERE recipient_type = 'all' OR recipient_type = :user_type";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(':user_type', (string)$user_type, PDO::PARAM_STR);
                $stmt->execute();
                return (int)$stmt->fetchColumn();
            }
        } catch (PDOException $e) {
            return 0;
        }
    }
}
