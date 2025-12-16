<?php
/**
 * Financial Report Model
 * Auto-calculates all stats from FinancialReportTransaction table
 * NO manual data entry - everything is calculated from transactions
 */
class FinancialReportModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    /**
     * Get current report_id for the month
     * @return int|null Report ID or null if not found
     */
    private function getCurrentReportId() {
        $currentMonth = date('Y-m-01');
        $query = "SELECT report_id FROM FinancialReport WHERE month = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $currentMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['report_id'] : null;
    }

    /**
     * Calculate Monthly Revenue from ALL completed transactions (exclude withdrawals)
     * Revenue = SUM(Payment + Commission + Subscription + Advertisement)
     */
    public function getMonthlyRevenue() {
        $reportId = $this->getCurrentReportId();
        if (!$reportId) return 0;

        $query = "SELECT COALESCE(SUM(amount), 0) as total_revenue 
                  FROM FinancialReportTransaction 
                  WHERE report_id = ? 
                  AND transaction_type IN ('Payment', 'Commission', 'Subscription', 'Advertisement')
                  AND status = 'Completed'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['total_revenue'] : 0;
    }

    /**
     * Calculate Pending Withdrawals from transactions with status='Pending'
     */
    public function getPendingWithdrawals() {
        $reportId = $this->getCurrentReportId();
        if (!$reportId) return 0;

        $query = "SELECT COALESCE(SUM(amount), 0) as total_pending 
                  FROM FinancialReportTransaction 
                  WHERE report_id = ? 
                  AND transaction_type = 'Withdrawal'
                  AND status = 'Pending'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['total_pending'] : 0;
    }

    /**
     * Calculate Total Commissions from completed commission transactions
     */
    public function getTotalCommissions() {
        $reportId = $this->getCurrentReportId();
        if (!$reportId) return 0;

        $query = "SELECT COALESCE(SUM(amount), 0) as total_commission 
                  FROM FinancialReportTransaction 
                  WHERE report_id = ? 
                  AND transaction_type = 'Commission'
                  AND status = 'Completed'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['total_commission'] : 0;
    }

    /**
     * Get Active Subscriptions count (still read from FinancialReport table)
     * This is a count, not calculated from transactions
     */
    public function getActiveSubscriptions() {
        $currentMonth = date('Y-m-01');
        $query = "SELECT COALESCE(active_subscriptions, 0) as active_count 
                  FROM FinancialReport 
                  WHERE month = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $currentMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row ? $row['active_count'] : 0;
    }

    /**
     * Calculate Revenue Breakdown from transaction types
     * Returns array with amounts and percentages
     */
    public function getRevenueBreakdown() {
        $reportId = $this->getCurrentReportId();
        if (!$reportId) {
            return array(
                array('source' => 'Service Commissions', 'amount' => 0, 'percentage' => 0),
                array('source' => 'Advertisement Revenue', 'amount' => 0, 'percentage' => 0),
                array('source' => 'Premium Subscriptions', 'amount' => 0, 'percentage' => 0),
                array('source' => 'Other Sources', 'amount' => 0, 'percentage' => 0)
            );
        }

        // Get amounts by transaction type
        $query = "SELECT 
                    COALESCE(SUM(CASE WHEN transaction_type = 'Commission' THEN amount ELSE 0 END), 0) as service_revenue,
                    COALESCE(SUM(CASE WHEN transaction_type = 'Advertisement' THEN amount ELSE 0 END), 0) as ad_revenue,
                    COALESCE(SUM(CASE WHEN transaction_type = 'Subscription' THEN amount ELSE 0 END), 0) as subscription_revenue,
                    COALESCE(SUM(CASE WHEN transaction_type = 'Payment' THEN amount ELSE 0 END), 0) as other_revenue,
                    COALESCE(SUM(CASE WHEN transaction_type IN ('Payment', 'Commission', 'Subscription', 'Advertisement') THEN amount ELSE 0 END), 0) as total_revenue
                  FROM FinancialReportTransaction 
                  WHERE report_id = ? 
                  AND status = 'Completed'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            $totalRevenue = $row['total_revenue'];
            return array(
                array(
                    'source' => 'Service Commissions', 
                    'amount' => $row['service_revenue'], 
                    'percentage' => $totalRevenue > 0 ? round(($row['service_revenue'] / $totalRevenue) * 100) : 0
                ),
                array(
                    'source' => 'Advertisement Revenue', 
                    'amount' => $row['ad_revenue'], 
                    'percentage' => $totalRevenue > 0 ? round(($row['ad_revenue'] / $totalRevenue) * 100) : 0
                ),
                array(
                    'source' => 'Premium Subscriptions', 
                    'amount' => $row['subscription_revenue'], 
                    'percentage' => $totalRevenue > 0 ? round(($row['subscription_revenue'] / $totalRevenue) * 100) : 0
                ),
                array(
                    'source' => 'Other Sources', 
                    'amount' => $row['other_revenue'], 
                    'percentage' => $totalRevenue > 0 ? round(($row['other_revenue'] / $totalRevenue) * 100) : 0
                )
            );
        }
        
        return array(
            array('source' => 'Service Commissions', 'amount' => 0, 'percentage' => 0),
            array('source' => 'Advertisement Revenue', 'amount' => 0, 'percentage' => 0),
            array('source' => 'Premium Subscriptions', 'amount' => 0, 'percentage' => 0),
            array('source' => 'Other Sources', 'amount' => 0, 'percentage' => 0)
        );
    }

    /**
     * Get Commission Structure (unchanged - reads from Category/Repairer)
     */
    public function getCommissionStructure() {
        $query = "SELECT c.name as service, COUNT(DISTINCT r.repairer_id) as volume 
                  FROM Category c 
                  LEFT JOIN Repairer r ON c.category_id = r.category_id 
                  GROUP BY c.category_id, c.name 
                  ORDER BY volume DESC 
                  LIMIT 5";
        $result = $this->connection->query($query);
        $structure = array();
        while ($row = $result->fetch_assoc()) {
            $structure[] = array(
                'service' => $row['service'], 
                'rate' => 15, 
                'volume' => $row['volume']
            );
        }
        return $structure;
    }

    /**
     * Get Recent Transactions (unchanged)
     */
    public function getRecentTransactions() {
        $reportId = $this->getCurrentReportId();
        if (!$reportId) return array();
        
        $query = "SELECT 
                    transaction_ref as id,
                    transaction_type as type,
                    amount,
                    DATE_FORMAT(transaction_date, '%Y-%m-%d') as date,
                    status
                  FROM FinancialReportTransaction
                  WHERE report_id = ?
                  ORDER BY transaction_date DESC
                  LIMIT 10";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $transactions = array();
        while ($row = $result->fetch_assoc()) {
            $transactions[] = array(
                'id' => $row['id'],
                'type' => $row['type'],
                'amount' => floatval($row['amount']),
                'date' => $row['date'],
                'status' => $row['status']
            );
        }
        
        return $transactions;
    }

    /**
     * Calculate Revenue Growth (compares current vs previous month)
     */
    public function calculateRevenueGrowth() {
        // Current month revenue
        $currentRevenue = $this->getMonthlyRevenue();
        
        // Previous month revenue
        $lastMonth = date('Y-m-01', strtotime('-1 month'));
        $query = "SELECT report_id FROM FinancialReport WHERE month = ? LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("s", $lastMonth);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) return 0;
        
        $lastReportId = $row['report_id'];
        $query = "SELECT COALESCE(SUM(amount), 0) as total_revenue 
                  FROM FinancialReportTransaction 
                  WHERE report_id = ? 
                  AND transaction_type IN ('Payment', 'Commission', 'Subscription', 'Advertisement')
                  AND status = 'Completed'";
        
        $stmt = $this->connection->prepare($query);
        $stmt->bind_param("i", $lastReportId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $lastRevenue = $row ? $row['total_revenue'] : 0;
        
        if ($lastRevenue > 0) {
            return round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1);
        }
        return 0;
    }
}
?>