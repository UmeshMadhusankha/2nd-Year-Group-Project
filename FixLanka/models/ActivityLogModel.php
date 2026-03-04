<?php
/**
 * ActivityLogModel.php
 * Centralized Activity Logging System
 * Version: 1.1.0 - Font Awesome Icons
 */

class ActivityLogModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function logActivity($userId, $userRole, $activityType, $description, $relatedId = null)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO system_activity_logs 
                (user_id, user_role, activity_type, description, related_id)
                VALUES (:user_id, :user_role, :activity_type, :description, :related_id)
            ");

            $stmt->execute([
                'user_id' => $userId,
                'user_role' => $userRole,
                'activity_type' => $activityType,
                'description' => $description,
                'related_id' => $relatedId
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Activity log failed: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentActivities($limit = 10)
    {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    id,
                    user_id,
                    user_role,
                    activity_type,
                    description,
                    related_id,
                    created_at
                FROM system_activity_logs
                ORDER BY created_at DESC
                LIMIT :limit
            ");

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get activities failed: " . $e->getMessage());
            return [];
        }
    }

    public function getActivityIcon($type)
    {
        $iconMap = [
            'ad_approved' => 'check-circle',
            'ad_rejected' => 'times-circle',
            'ad_override' => 'shield-alt',
            'ad_deleted' => 'trash',
            'ad_scheduled' => 'calendar-alt',
            'ad_activated' => 'play-circle',
            'ad_paused' => 'pause-circle',
            'ad_suspended' => 'exclamation-triangle',
            'report_resolved' => 'check-square',
            'moderator_created' => 'user-plus',
            'moderator_updated' => 'user-check',
            'moderator_deleted' => 'user-times',
            'content_updated' => 'edit',
            'system' => 'history'
        ];

        return $iconMap[$type] ?? 'circle';
    }

    public function getActivityColor($type)
    {
        $colorMap = [
            'ad_approved' => 'green',
            'ad_rejected' => 'red',
            'ad_override' => 'purple',
            'ad_deleted' => 'red',
            'ad_scheduled' => 'blue',
            'ad_activated' => 'green',
            'ad_paused' => 'yellow',
            'ad_suspended' => 'red',
            'report_resolved' => 'green',
            'moderator_created' => 'blue',
            'moderator_updated' => 'blue',
            'moderator_deleted' => 'red',
            'content_updated' => 'blue',
            'system' => 'gray'
        ];

        return $colorMap[$type] ?? 'gray';
    }
}