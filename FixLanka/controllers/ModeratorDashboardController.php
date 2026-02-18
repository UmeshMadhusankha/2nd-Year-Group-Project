<?php
/**
 * ModeratorDashboardController.php
 * Handles Moderator Dashboard logic and data flow
 */
require_once __DIR__ . '/../models/ModeratorDashboardModel.php';

class ModeratorDashboardController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->model = new ModeratorDashboardModel($this->pdo);
    }

    /**
     * Get all dashboard data
     */
    public function getDashboardData()
    {
        $stats = $this->model->getStatistics();
        $recentActivity = $this->model->getRecentActivity();
        $activityOverview = $this->model->getActivityOverview();
        $systemStatus = $this->model->getSystemStatus();
        $quickActions = $this->model->getQuickActionCounts();

        return [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'activityOverview' => $activityOverview,
            'systemStatus' => $systemStatus,
            'quickActions' => $quickActions
        ];
    }

    /**
     * Format dashboard stats for view
     */
    public function getFormattedStats()
    {
        $data = $this->getDashboardData();
        
        return [
            'totalUsers' => $data['stats']['total_users'],
            'activeAds' => $data['stats']['active_ads'],
            'pendingReviews' => $data['stats']['pending_reviews'],
            'totalRevenue' => $data['stats']['total_revenue'],
            'newUsersToday' => $data['stats']['new_users_today'],
            'adsApprovedToday' => $data['stats']['ads_approved_today'],
            'reportsToday' => $data['stats']['reports_today'],
            'systemAlerts' => $data['stats']['system_alerts']
        ];
    }

    /**
     * Calculate time ago from timestamp
     */
    public function getTimeAgo($datetime)
    {
        if (empty($datetime)) return 'Unknown';
        
        try {
            $now = new DateTime();
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $totalSeconds = ($diff->days * 86400) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s;

            if ($totalSeconds < 1) {
                return 'Just now';
            }
            
            if ($totalSeconds < 60) {
                return $diff->s . ' second' . ($diff->s > 1 ? 's' : '') . ' ago';
            }

            if ($diff->i > 0 && $diff->h == 0 && $diff->d == 0) {
                return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
            }

            if ($diff->h > 0 && $diff->d == 0) {
                return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
            }

            if ($diff->d > 0 && $diff->d < 7) {
                return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
            }

            if ($diff->d >= 7 && $diff->d < 30) {
                $weeks = floor($diff->d / 7);
                return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
            }

            if ($diff->m > 0 && $diff->y == 0) {
                return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
            }

            if ($diff->y > 0) {
                return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
            }

            return 'Just now';
        } catch (Exception $e) {
            error_log("Error calculating time ago: " . $e->getMessage());
            return 'Unknown';
        }
    }
}