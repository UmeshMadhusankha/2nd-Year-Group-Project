<?php
/**
 * ModeratorDashboardController.php
 * Handles Moderator Dashboard logic and data flow
 */

class ModeratorDashboardController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/ModeratorDashboardModel.php';
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
}