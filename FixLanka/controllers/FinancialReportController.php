<?php
/**
 * Financial Report Controller
 * Handles financial data display for moderator dashboard
 * Pure MVC - No AJAX, No frameworks
 */

class FinancialReportController {
    private $model;
    private $conn;

    /**
     * Constructor
     * @param mysqli $connection Database connection
     */
    public function __construct($connection) {
        $this->conn = $connection;
        require_once __DIR__ . '/../models/FinancialReportModel.php';
        $this->model = new FinancialReportModel($connection);
    }

    /**
     * Get all financial data for the view
     * @return array All financial data
     */
    public function getFinancialData() {
        // Get all data from model
        $data = [
            'monthlyRevenue' => $this->model->getMonthlyRevenue(),
            'pendingWithdrawals' => $this->model->getPendingWithdrawals(),
            'totalCommissions' => $this->model->getTotalCommissions(),
            'activeSubscriptions' => $this->model->getActiveSubscriptions(),
            'revenueBreakdown' => $this->model->getRevenueBreakdown(),
            'commissionStructure' => $this->model->getCommissionStructure(),
            'recentTransactions' => $this->model->getRecentTransactions()
        ];

        return $data;
    }

    /**
     * Calculate revenue growth percentage
     * @return string Growth percentage with + or - sign
     */
    public function getRevenueGrowth() {
        return $this->model->calculateRevenueGrowth();
    }

    /**
     * Get formatted currency
     * @param float $amount Amount to format
     * @return string Formatted amount
     */
    public function formatCurrency($amount) {
        if ($amount >= 1000000) {
            return 'LKR ' . number_format($amount / 1000000, 1) . 'M';
        } elseif ($amount >= 1000) {
            return 'LKR ' . number_format($amount / 1000) . 'K';
        } else {
            return 'LKR ' . number_format($amount);
        }
    }
}
?>