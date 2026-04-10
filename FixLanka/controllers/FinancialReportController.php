<?php
/**
 * FinancialReportController.php
 * Minimal controller for moderator financial reports.
 * Pure PHP + MySQLi (matches FixLanka/views/moderator/finance.php expectations).
 */

class FinancialReportController
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function formatCurrency($amount): string
    {
        $val = is_numeric($amount) ? (float)$amount : 0.0;
        return 'LKR ' . number_format($val, 2);
    }

    public function getRevenueGrowth(): float
    {
        $latest = $this->getLatestFinancialReportRow();
        if ($latest) {
            $latestMonth = (string)($latest['month'] ?? '');
            $latestRevenue = (float)($latest['revenue'] ?? 0);

            $prev = $this->getFinancialReportRowBefore($latestMonth);
            $prevRevenue = $prev ? (float)($prev['revenue'] ?? 0) : 0.0;

            if ($prevRevenue <= 0) {
                return $latestRevenue > 0 ? 100.0 : 0.0;
            }

            return round((($latestRevenue - $prevRevenue) / $prevRevenue) * 100, 2);
        }

        // Fallback: compute from payments
        $current = $this->getPaymentRevenueForMonth(date('Y-m-01'));
        $prevMonth = date('Y-m-01', strtotime('first day of last month'));
        $previous = $this->getPaymentRevenueForMonth($prevMonth);

        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    public function getFinancialData(): array
    {
        $latest = $this->getLatestFinancialReportRow();

        $monthlyRevenue = 0.0;
        $pendingWithdrawals = 0.0;

        if ($latest) {
            $monthlyRevenue = (float)($latest['revenue'] ?? 0);
            $pendingWithdrawals = (float)($latest['pending_withdrawals'] ?? 0);
        } else {
            // Fallback to payment table for current month
            $monthlyRevenue = $this->getPaymentRevenueForMonth(date('Y-m-01'));
        }

        // No dedicated commission table exists; estimate as a simple percentage of revenue.
        $totalCommissions = round($monthlyRevenue * 0.10, 2);

        $activeSubscriptions = $this->countActiveSubscriptions();
        $revenueBreakdown = $this->getRevenueBreakdownForCurrentMonth($monthlyRevenue);
        $commissionStructure = $this->getCommissionStructureSnapshot();
        $recentTransactions = $this->getRecentTransactions();

        return [
            'monthlyRevenue' => $monthlyRevenue,
            'pendingWithdrawals' => $pendingWithdrawals,
            'totalCommissions' => $totalCommissions,
            'activeSubscriptions' => $activeSubscriptions,
            'revenueBreakdown' => $revenueBreakdown,
            'commissionStructure' => $commissionStructure,
            'recentTransactions' => $recentTransactions,
        ];
    }

    private function tableExists(string $tableName): bool
    {
        $tableName = $this->conn->real_escape_string($tableName);
        $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '{$tableName}' LIMIT 1";
        $res = $this->conn->query($sql);
        if (!$res) {
            return false;
        }
        $row = $res->fetch_row();
        return !empty($row);
    }

    private function getLatestFinancialReportRow(): ?array
    {
        if (!$this->tableExists('financialreport')) {
            return null;
        }

        $sql = "SELECT month, revenue, pending_withdrawals FROM financialreport ORDER BY month DESC LIMIT 1";
        $res = $this->conn->query($sql);
        if (!$res) {
            return null;
        }
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    private function getFinancialReportRowBefore(string $monthDate): ?array
    {
        if (!$this->tableExists('financialreport')) {
            return null;
        }

        $stmt = $this->conn->prepare("SELECT month, revenue, pending_withdrawals FROM financialreport WHERE month < ? ORDER BY month DESC LIMIT 1");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param('s', $monthDate);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        return $row ?: null;
    }

    private function getPaymentRevenueForMonth(string $monthStart): float
    {
        if (!$this->tableExists('payment')) {
            return 0.0;
        }

        $start = $monthStart;
        $end = date('Y-m-t', strtotime($monthStart));

        $stmt = $this->conn->prepare(
            "SELECT COALESCE(SUM(amount), 0) AS total FROM payment WHERE DATE(paymentDate) BETWEEN ? AND ? AND status IN ('completed')"
        );
        if (!$stmt) {
            return 0.0;
        }
        $stmt->bind_param('ss', $start, $end);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ? (float)($row['total'] ?? 0) : 0.0;
    }

    private function countActiveSubscriptions(): int
    {
        if ($this->tableExists('promotion')) {
            $sql = "SELECT COUNT(*) AS cnt FROM promotion WHERE status = 'active' AND end_date >= CURDATE()";
            $res = $this->conn->query($sql);
            $row = $res ? $res->fetch_assoc() : null;
            return (int)($row['cnt'] ?? 0);
        }

        return 0;
    }

    private function getRevenueBreakdownForCurrentMonth(float $monthlyRevenue): array
    {
        // Prefer breaking down payment revenue by paymentType if available.
        if ($this->tableExists('payment')) {
            $start = date('Y-m-01');
            $end = date('Y-m-t');

            $stmt = $this->conn->prepare(
                "SELECT paymentType AS source, COALESCE(SUM(amount), 0) AS amount
                 FROM payment
                 WHERE DATE(paymentDate) BETWEEN ? AND ? AND status IN ('completed')
                 GROUP BY paymentType"
            );
            if ($stmt) {
                $stmt->bind_param('ss', $start, $end);
                $stmt->execute();
                $res = $stmt->get_result();

                $rows = [];
                $total = 0.0;
                while ($res && ($r = $res->fetch_assoc())) {
                    $amount = (float)($r['amount'] ?? 0);
                    $rows[] = ['source' => (string)($r['source'] ?? 'Unknown'), 'amount' => $amount];
                    $total += $amount;
                }
                $stmt->close();

                if ($total <= 0) {
                    $total = $monthlyRevenue;
                }

                return array_map(function ($r) use ($total) {
                    $amount = (float)($r['amount'] ?? 0);
                    $pct = $total > 0 ? (int)round(($amount / $total) * 100) : 0;
                    return [
                        'source' => (string)$r['source'],
                        'amount' => $amount,
                        'percentage' => $pct,
                    ];
                }, $rows);
            }
        }

        // Fallback: single bucket
        return [[
            'source' => 'Platform Revenue',
            'amount' => $monthlyRevenue,
            'percentage' => 100,
        ]];
    }

    private function getCommissionStructureSnapshot(): array
    {
        // There is no normalized commission table in this repo; return a simple snapshot
        // so the page can render meaningful, non-empty data.
        $repairers = $this->countTableRows('repairer');
        $companies = $this->countTableRows('company');
        $ads = $this->countTableRows('advertisement');

        return [
            ['service' => 'Repair Services', 'volume' => $repairers, 'rate' => 10],
            ['service' => 'Company Services', 'volume' => $companies, 'rate' => 8],
            ['service' => 'Advertisements', 'volume' => $ads, 'rate' => 0],
        ];
    }

    private function countTableRows(string $tableName): int
    {
        if (!$this->tableExists($tableName)) {
            return 0;
        }
        $sql = "SELECT COUNT(*) AS cnt FROM {$tableName}";
        $res = $this->conn->query($sql);
        $row = $res ? $res->fetch_assoc() : null;
        return (int)($row['cnt'] ?? 0);
    }

    private function getRecentTransactions(): array
    {
        $out = [];

        if ($this->tableExists('payment')) {
            $sql = "
                SELECT payment_id AS id, amount, paymentDate AS dt, status
                FROM payment
                ORDER BY paymentDate DESC
                LIMIT 25
            ";
            $res = $this->conn->query($sql);
            while ($res && ($row = $res->fetch_assoc())) {
                $status = strtolower((string)($row['status'] ?? 'pending'));
                $statusLabel = match ($status) {
                    'completed' => 'Completed',
                    'failed' => 'Failed',
                    default => 'Pending',
                };

                $out[] = [
                    'id' => (int)($row['id'] ?? 0),
                    'type' => 'Payment',
                    'amount' => (float)($row['amount'] ?? 0),
                    'date' => date('Y-m-d', strtotime((string)($row['dt'] ?? 'now'))),
                    'status' => $statusLabel,
                ];
            }
        }

        return $out;
    }
}
