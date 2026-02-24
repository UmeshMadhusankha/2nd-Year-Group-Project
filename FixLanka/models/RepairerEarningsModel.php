<?php
/**
 * RepairerEarnings Model
 *
 * Handles all earnings, payment history, and financial statistics
 * for individual repairers.
 */
class RepairerEarnings {
    private $pdo;

    /**
     * @param PDO $pdo Active PDO database connection
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get total earnings for a repairer over a given period.
     *
     * Joins repairerquote (status='accepted') -> jobrequest -> payment (status='completed').
     *
     * @param int    $repairerId Repairer's ID
     * @param string $period     One of: 'today', 'week', 'month', 'year', 'all'
     * @return array ['total' => float, 'count' => int]
     */
    public function getTotalEarnings($repairerId, $period = 'all') {
        try {
            $sql = "
                SELECT
                    COALESCE(SUM(p.amount), 0) AS total,
                    COUNT(p.payment_id)         AS count
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
            ";

            $params = [$repairerId];

            switch ($period) {
                case 'today':
                    $sql .= " AND DATE(p.paymentDate) = CURDATE()";
                    break;
                case 'week':
                    $sql .= " AND p.paymentDate >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $sql .= " AND YEAR(p.paymentDate) = YEAR(NOW()) AND MONTH(p.paymentDate) = MONTH(NOW())";
                    break;
                case 'year':
                    $sql .= " AND YEAR(p.paymentDate) = YEAR(NOW())";
                    break;
                // 'all' — no extra filter
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'total' => (float) $row['total'],
                'count' => (int)   $row['count'],
            ];
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getTotalEarnings error: " . $e->getMessage());
            return ['total' => 0.0, 'count' => 0];
        }
    }

    /**
     * Get an earnings breakdown split between customer jobs and company contracts.
     *
     * - customer_jobs:      repairerquote (accepted) + payment (completed)
     * - company_contracts:  repairerassignment (completed)
     *
     * @param int $repairerId Repairer's ID
     * @return array ['customer_jobs' => float, 'company_contracts' => float, 'total' => float]
     */
    public function getEarningsBreakdown($repairerId) {
        try {
            // Customer jobs via repairerquote → payment
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS customer_jobs
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
            ");
            $stmt->execute([$repairerId]);
            $customerJobs = (float) $stmt->fetchColumn();

            // Company contracts via repairerassignment
            $stmt2 = $this->pdo->prepare("
                SELECT COALESCE(SUM(ra.amount), 0) AS company_contracts
                FROM repairerassignment ra
                WHERE ra.repairer_id = ?
                  AND ra.status      = 'completed'
            ");
            $stmt2->execute([$repairerId]);
            $companyContracts = (float) $stmt2->fetchColumn();

            return [
                'customer_jobs'      => $customerJobs,
                'company_contracts'  => $companyContracts,
                'total'              => $customerJobs + $companyContracts,
            ];
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getEarningsBreakdown error: " . $e->getMessage());
            return ['customer_jobs' => 0.0, 'company_contracts' => 0.0, 'total' => 0.0];
        }
    }

    /**
     * Get paginated payment history for a repairer with optional filters.
     *
     * Joins payment → jobrequest → repairerquote filtered by repairer_id.
     * Supports filters: status, date_from, date_to.
     * Ordered by paymentDate DESC.
     *
     * @param int   $repairerId Repairer's ID
     * @param int   $limit      Max records to return (default 20)
     * @param int   $offset     Pagination offset (default 0)
     * @param array $filters    Optional: ['status'=>string, 'date_from'=>string, 'date_to'=>string]
     * @return array Array of payment records
     */
    public function getPaymentHistory($repairerId, $limit = 20, $offset = 0, $filters = []) {
        try {
            $sql = "
                SELECT
                    p.payment_id,
                    p.job_request_id,
                    p.paymentType,
                    p.amount,
                    p.paymentDate,
                    p.status             AS payment_status,
                    jr.title             AS job_title,
                    jr.description       AS job_description,
                    jr.district,
                    rq.quote_id,
                    rq.quoteAmount,
                    rq.estimatedDays,
                    rq.warrantyPeriod
                FROM payment p
                JOIN jobrequest jr   ON p.job_request_id = jr.request_id
                JOIN repairerquote rq ON rq.request_id   = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
            ";

            $params = [$repairerId];

            if (!empty($filters['status'])) {
                $sql .= " AND p.status = ?";
                $params[] = $filters['status'];
            }

            if (!empty($filters['date_from'])) {
                $sql .= " AND DATE(p.paymentDate) >= ?";
                $params[] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $sql .= " AND DATE(p.paymentDate) <= ?";
                $params[] = $filters['date_to'];
            }

            $sql .= " ORDER BY p.paymentDate DESC LIMIT ? OFFSET ?";
            $params[] = (int) $limit;
            $params[] = (int) $offset;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getPaymentHistory error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all pending payments for a repairer, including how many days they have been pending.
     *
     * @param int $repairerId Repairer's ID
     * @return array Array of pending payment records with a 'days_pending' field
     */
    public function getPendingPayments($repairerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    p.payment_id,
                    p.job_request_id,
                    p.paymentType,
                    p.amount,
                    p.paymentDate,
                    p.status             AS payment_status,
                    DATEDIFF(NOW(), p.paymentDate) AS days_pending,
                    jr.title             AS job_title,
                    jr.description       AS job_description,
                    jr.district,
                    rq.quote_id,
                    rq.quoteAmount,
                    rq.estimatedDays
                FROM payment p
                JOIN jobrequest jr    ON p.job_request_id = jr.request_id
                JOIN repairerquote rq  ON rq.request_id   = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'pending'
                ORDER BY p.paymentDate ASC
            ");
            $stmt->execute([$repairerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getPendingPayments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get summary earnings statistics for a repairer.
     *
     * Calculates:
     * - growth:        percentage change from last month to this month
     * - avg_job_value: average completed payment amount
     * - highest:       highest single completed payment
     * - lowest:        lowest single completed payment
     *
     * @param int $repairerId Repairer's ID
     * @return array ['growth' => float, 'avg_job_value' => float, 'highest' => float, 'lowest' => float]
     */
    public function getEarningsStats($repairerId) {
        try {
            // This month total
            $stmt = $this->pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS this_month
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
                  AND YEAR(p.paymentDate)  = YEAR(NOW())
                  AND MONTH(p.paymentDate) = MONTH(NOW())
            ");
            $stmt->execute([$repairerId]);
            $thisMonth = (float) $stmt->fetchColumn();

            // Last month total
            $stmt2 = $this->pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS last_month
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
                  AND YEAR(p.paymentDate)  = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                  AND MONTH(p.paymentDate) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
            ");
            $stmt2->execute([$repairerId]);
            $lastMonth = (float) $stmt2->fetchColumn();

            // Growth percentage
            if ($lastMonth > 0) {
                $growth = (($thisMonth - $lastMonth) / $lastMonth) * 100;
            } elseif ($thisMonth > 0) {
                $growth = 100.0;
            } else {
                $growth = 0.0;
            }

            // Avg, highest, lowest (all time, completed)
            $stmt3 = $this->pdo->prepare("
                SELECT
                    COALESCE(AVG(p.amount), 0) AS avg_job_value,
                    COALESCE(MAX(p.amount), 0) AS highest,
                    COALESCE(MIN(p.amount), 0) AS lowest
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
            ");
            $stmt3->execute([$repairerId]);
            $aggRow = $stmt3->fetch(PDO::FETCH_ASSOC);

            return [
                'growth'        => round($growth, 2),
                'avg_job_value' => (float) $aggRow['avg_job_value'],
                'highest'       => (float) $aggRow['highest'],
                'lowest'        => (float) $aggRow['lowest'],
            ];
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getEarningsStats error: " . $e->getMessage());
            return ['growth' => 0.0, 'avg_job_value' => 0.0, 'highest' => 0.0, 'lowest' => 0.0];
        }
    }

    /**
     * Get day-by-day earnings totals for a specific month and year.
     *
     * @param int $repairerId Repairer's ID
     * @param int $year       Four-digit year (e.g. 2026)
     * @param int $month      Month number 1–12
     * @return array ['labels' => string[], 'values' => float[]]
     */
    public function getMonthlyEarnings($repairerId, $year, $month) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    DAY(p.paymentDate)        AS day,
                    COALESCE(SUM(p.amount), 0) AS daily_total
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
                  AND p.status       = 'completed'
                  AND YEAR(p.paymentDate)  = ?
                  AND MONTH(p.paymentDate) = ?
                GROUP BY DAY(p.paymentDate)
                ORDER BY DAY(p.paymentDate) ASC
            ");
            $stmt->execute([$repairerId, (int) $year, (int) $month]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build a full-month map so every day is represented
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);
            $dailyMap = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dailyMap[$d] = 0.0;
            }
            foreach ($rows as $row) {
                $dailyMap[(int) $row['day']] = (float) $row['daily_total'];
            }

            $labels = [];
            $values = [];
            foreach ($dailyMap as $day => $total) {
                $labels[] = str_pad($day, 2, '0', STR_PAD_LEFT);
                $values[] = $total;
            }

            return ['labels' => $labels, 'values' => $values];
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getMonthlyEarnings error: " . $e->getMessage());
            return ['labels' => [], 'values' => []];
        }
    }

    /**
     * Count payments for a repairer, optionally filtered by status.
     *
     * @param int         $repairerId Repairer's ID
     * @param string|null $status     Optional payment status filter (e.g. 'completed', 'pending')
     * @return int Total number of matching payments
     */
    public function getPaymentCount($repairerId, $status = null) {
        try {
            $sql = "
                SELECT COUNT(p.payment_id)
                FROM repairerquote rq
                JOIN jobrequest jr ON rq.request_id    = jr.request_id
                JOIN payment p     ON p.job_request_id = jr.request_id
                WHERE rq.repairer_id = ?
                  AND rq.status      = 'accepted'
            ";

            $params = [$repairerId];

            if ($status !== null) {
                $sql .= " AND p.status = ?";
                $params[] = $status;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("RepairerEarnings::getPaymentCount error: " . $e->getMessage());
            return 0;
        }
    }
}
