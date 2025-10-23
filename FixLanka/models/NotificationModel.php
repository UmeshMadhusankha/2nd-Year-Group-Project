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
        $stmt = $this->pdo->prepare("
            SELECT notification_id, title, message, send_date, recipient_type, status 
            FROM Notification 
            ORDER BY send_date DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recent notifications with limit
     * @param int $limit Number of notifications to retrieve
     * @return array Array of notification records
     */
    public function getRecentNotifications($limit = 5)
    {
        error_log("[MODEL] getRecentNotifications called with limit: $limit");
        try {
            // Cast limit to integer to avoid SQL syntax error
            $limit = (int)$limit;
            
            $stmt = $this->pdo->prepare("
                SELECT notification_id, title, message, send_date, recipient_type, status 
                FROM Notification 
                ORDER BY send_date DESC 
                LIMIT :limit
            ");
            // Bind limit as integer (PDO::PARAM_INT) to avoid quotes in SQL
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("[MODEL] Fetched " . count($result) . " notifications from database");
            return $result;
        } catch (PDOException $e) {
            error_log("[MODEL ERROR] getRecentNotifications failed: " . $e->getMessage());
            throw $e;
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
            $stmt = $this->pdo->prepare("
                INSERT INTO Notification (title, message, recipient_type, status) 
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
}
