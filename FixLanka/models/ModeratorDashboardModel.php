<?php
/**
 * ModeratorDashboardModel.php
 * Dashboard Data Provider with Activity Logging Integration
 */

require_once __DIR__ . '/ActivityLogModel.php';

class ModeratorDashboardModel
{
    private $pdo;
    private $activityLog;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->activityLog = new ActivityLogModel($pdo);
    }

    /**
     * Get dashboard statistics
     */
    public function getStatistics()
    {
        $stats = [];

        // Total Users
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM User");
        $stats['total_users'] = $stmt->fetch()['count'] ?? 0;

        // New Users Today
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM User WHERE DATE(created_at) = CURDATE()");
        $stats['new_users_today'] = $stmt->fetch()['count'] ?? 0;

        // Active Ads (approved or active status)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Advertisement WHERE status IN ('approved', 'active')");
        $stats['active_ads'] = $stmt->fetch()['count'] ?? 0;

        // Ads Approved Today (from activity logs)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM system_activity_logs WHERE activity_type = 'ad_approved' AND DATE(created_at) = CURDATE()");
        $stats['ads_approved_today'] = $stmt->fetch()['count'] ?? 0;

        // Pending Reviews (pending ads)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Advertisement WHERE status = 'pending'");
        $stats['pending_reviews'] = $stmt->fetch()['count'] ?? 0;

        // Reports Today
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM IssueReport WHERE DATE(date) = CURDATE()");
        $stats['reports_today'] = $stmt->fetch()['count'] ?? 0;

        // Total Revenue (from Payments this month)
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM Payment WHERE MONTH(paymentDate) = MONTH(CURDATE()) AND YEAR(paymentDate) = YEAR(CURDATE())");
        $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;

        // System Alerts (pending notifications)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Notification WHERE status = 'pending'");
        $stats['system_alerts'] = $stmt->fetch()['count'] ?? 0;

        return $stats;
    }

    /**
     * Get recent activity from system_activity_logs
     */
    public function getRecentActivity($limit = 10)
    {
        $rows = $this->activityLog->getRecentActivities($limit);

        if (empty($rows)) {
            return [
                [
                    'activity_type' => 'system',
                    'description' => 'No recent activity available',
                    'user_role' => 'system',
                    'icon' => $this->activityLog->getActivityIcon('system'),
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }

        // Normalize to keys views expect
        $activities = [];
        foreach ($rows as $r) {
            $activities[] = [
                'activity_type' => $r['activity_type'] ?? 'system',
                'description' => $r['description'] ?? '',
                'user_role' => isset($r['user_role']) ? strtolower($r['user_role']) : 'system',
                'icon' => $this->activityLog->getActivityIcon($r['activity_type'] ?? 'system'),
                'created_at' => $r['created_at'] ?? date('Y-m-d H:i:s')
            ];
        }

        return $activities;
    }

    /**
     * Get activity overview percentages
     */
    public function getActivityOverview()
    {
        $overview = [];

        // Ad Approvals
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM Advertisement");
            $total = $stmt->fetch()['total'] ?? 0;
            $stmt = $this->pdo->query("SELECT COUNT(*) as approved FROM Advertisement WHERE status IN ('approved', 'active')");
            $approved = $stmt->fetch()['approved'] ?? 0;
            $overview['ad_approvals'] = $total > 0 ? round(($approved / $total) * 100) : 0;
        } catch (PDOException $e) {
            error_log("Error getting ad approvals: " . $e->getMessage());
            $overview['ad_approvals'] = 0;
        }

        // Revenue Growth
        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) as this_month FROM Payment WHERE MONTH(paymentDate) = MONTH(CURDATE()) AND YEAR(paymentDate) = YEAR(CURDATE())");
            $thisMonth = $stmt->fetch()['this_month'] ?? 0;
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) as last_month FROM Payment WHERE MONTH(paymentDate) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(paymentDate) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))");
            $lastMonth = $stmt->fetch()['last_month'] ?? 0;
            if ($lastMonth > 0) {
                $overview['revenue_growth'] = round((($thisMonth - $lastMonth) / $lastMonth) * 100);
            } else {
                $overview['revenue_growth'] = $thisMonth > 0 ? 100 : 0;
            }
        } catch (PDOException $e) {
            error_log("Error calculating revenue growth: " . $e->getMessage());
            $overview['revenue_growth'] = 0;
        }

        // System Performance (simple heuristic)
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM Advertisement");
            $adCount = $stmt->fetch()['total'] ?? 0;
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM User");
            $userCount = $stmt->fetch()['total'] ?? 0;
            $performance = min(100, ($adCount + $userCount) > 0 ? round((100 / ($adCount + $userCount)) * 10) + 50 : 96);
            $overview['system_performance'] = max(50, min(100, $performance));
        } catch (PDOException $e) {
            error_log("Error calculating system performance: " . $e->getMessage());
            $overview['system_performance'] = 96;
        }

        return $overview;
    }

    /**
     * Get system status
     */
    public function getSystemStatus()
    {
        $status = [];

        $status['server'] = [
            'status' => 'operational',
            'label' => 'Server Status',
            'value' => 'Operational'
        ];

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Advertisement");
            $adCount = $stmt->fetch()['count'] ?? 0;
            $status['database'] = [
                'status' => 'operational',
                'label' => 'Database',
                'value' => 'Connected',
                'detail' => $adCount . ' ads'
            ];
        } catch (PDOException $e) {
            $status['database'] = [
                'status' => 'error',
                'label' => 'Database',
                'value' => 'Error',
                'detail' => 'Connection failed'
            ];
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM IssueReport WHERE status = 'pending'");
            $reportCount = $stmt->fetch()['count'] ?? 0;
            $status['reports'] = [
                'status' => $reportCount > 5 ? 'warning' : 'operational',
                'label' => 'Reports',
                'value' => $reportCount . ' pending'
            ];
        } catch (PDOException $e) {
            $status['reports'] = [
                'status' => 'error',
                'label' => 'Reports',
                'value' => 'Unknown'
            ];
        }

        return $status;
    }

    /**
     * Get quick action counts
     */
    public function getQuickActionCounts()
    {
        $counts = [];

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Advertisement WHERE status = 'pending'");
            $counts['pending_reviews'] = $stmt->fetch()['count'] ?? 0;
        } catch (PDOException $e) {
            $counts['pending_reviews'] = 0;
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM User WHERE account_status = 'flagged'");
            $counts['moderation_needed'] = $stmt->fetch()['count'] ?? 0;
        } catch (PDOException $e) {
            $counts['moderation_needed'] = 0;
        }

        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM IssueReport WHERE DATE(date) = CURDATE()");
            $counts['reports_today'] = $stmt->fetch()['count'] ?? 0;
        } catch (PDOException $e) {
            $counts['reports_today'] = 0;
        }

        try {
            $stmt = $this->pdo->query("SELECT COALESCE(SUM(amount), 0) as total FROM Payment WHERE MONTH(paymentDate) = MONTH(CURDATE()) AND YEAR(paymentDate) = YEAR(CURDATE())");
            $counts['total_revenue'] = $stmt->fetch()['total'] ?? 0;
        } catch (PDOException $e) {
            $counts['total_revenue'] = 0;
        }

        return $counts;
    }
}