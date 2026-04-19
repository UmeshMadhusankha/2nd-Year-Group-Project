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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'moderator'])) {
            require_once __DIR__ . '/../config/session.php';
            requireRole(['admin', 'moderator']);
        }
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
     * Get formatted stats for view
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
     * Format datetime for display
     * Format: "20 Feb 2026, 03:45 PM"
     */
    public function formatDateTime($datetime)
    {
        if (empty($datetime)) {
            return 'Unknown';
        }

        try {
            $date = new DateTime($datetime);
            return $date->format('d M Y, h:i A');
        } catch (Exception $e) {
            error_log("Date format error: " . $e->getMessage());
            return 'Invalid date';
        }
    }
}