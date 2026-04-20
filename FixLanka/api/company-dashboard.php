<?php
require_once '../config/session.php';
require_once '../config/database.php';
require_once 'helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? null) !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$companyId = $_SESSION['company_id'] ?? null;

if (!$companyId && $userId > 0) {
    $companyData = getCompanyByUserId($pdo, $userId);
    if ($companyData && isset($companyData['company_id'])) {
        $companyId = (int)$companyData['company_id'];
    }
}

if (!$companyId) {
    $companyId = $userId;
}

$chartPeriod = $_GET['chart_period'] ?? '7d';
$allowedPeriods = ['7d', '30d', '3m'];
if (!in_array($chartPeriod, $allowedPeriods, true)) {
    $chartPeriod = '7d';
}

function formatDateLabel($dateStr) {
    $ts = strtotime($dateStr);
    return $ts ? date('M j', $ts) : $dateStr;
}

$debug = ($_GET['debug'] ?? '') === '1';
$sectionErrors = [];

function logDashboardSectionError(string $section, Throwable $e): void {
    global $sectionErrors;

    $sectionErrors[] = ['section' => $section];

    $line = '[' . date('Y-m-d H:i:s') . '] company-dashboard ' . $section . ': ' . $e->getMessage() . "\n";
    error_log($line);
    @file_put_contents(__DIR__ . '/sql_error.txt', $line, FILE_APPEND);
}

try {
    $activeProjects = 0;
    $pendingRequests = 0;
    $totalEarnings = 0.0;
    $avgRating = 0.0;

    // KPI: Active projects
    try {
                // IMPORTANT: Don't count 'planned' projects unless the customer has accepted/signed the contract.
                // This avoids showing projects as active when the contract is only sent but not accepted.
                $stmt = $pdo->prepare("
                        SELECT COUNT(DISTINCT p.project_id) AS cnt
                        FROM project p
                        WHERE p.company_id = ?
                            AND (
                                p.status IN ('in_progress','on_hold')
                                OR (
                                        p.status = 'planned'
                                        AND EXISTS (
                                                SELECT 1
                                                FROM contract c
                                                WHERE c.project_id = p.project_id
                                                    AND c.company_id = p.company_id
                                                    AND (
                                                        c.customer_response = 'accepted'
                                                        OR c.terms_accepted = 1
                                                        OR c.signed_at IS NOT NULL
                                                        OR c.status IN ('active','in_progress','milestone_pending')
                                                    )
                                        )
                                )
                            )
                ");
                $stmt->execute([$companyId]);
                $activeProjects = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
    } catch (PDOException $e) {
        logDashboardSectionError('kpi_active_projects', $e);
    }

    // KPI: Pending requests (pending quotations)
    try {
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT request_id) AS cnt FROM companyquotation WHERE company_id = ? AND status = 'pending'");
        $stmt->execute([$companyId]);
        $pendingRequests = (int)($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);
    } catch (PDOException $e) {
        logDashboardSectionError('kpi_pending_requests', $e);
    }

    // KPI: Total earnings (sum of completed payments for this company's contracts)
    try {
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(p.amount), 0) AS total
            FROM payment p
            INNER JOIN contract c ON c.job_request_id = p.job_request_id
            WHERE c.company_id = ? AND p.status = 'completed'
        ");
        $stmt->execute([$companyId]);
        $totalEarnings = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    } catch (PDOException $e) {
        logDashboardSectionError('kpi_total_earnings', $e);
    }

    // KPI: Average rating (from company table)
    try {
        $stmt = $pdo->prepare('SELECT rating FROM company WHERE company_id = ? LIMIT 1');
        $stmt->execute([$companyId]);
        $avgRating = (float)($stmt->fetch(PDO::FETCH_ASSOC)['rating'] ?? 0);
    } catch (PDOException $e) {
        logDashboardSectionError('kpi_average_rating', $e);
    }

    $directRequests = [];
    // Direct requests (truly direct to this company)
    try {
        $stmt = $pdo->prepare("
            SELECT
                djr.request_id,
                djr.title,
                djr.description,
                djr.status,
                djr.date_created AS created_at,
                djr.finish_date,
                c.name AS category,
                u.f_name,
                u.l_name,
                u.profilePicture
            FROM directjobrequest djr
            INNER JOIN category c ON c.category_id = djr.category_id
            INNER JOIN user u ON u.user_id = djr.user_id
            WHERE djr.provider_type = 'company'
              AND djr.provider_id = ?
            AND djr.finish_date >= CURDATE()
            ORDER BY djr.date_created DESC
            LIMIT 5
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $status = strtolower((string)($row['status'] ?? 'pending'));
            if (!in_array($status, ['pending', 'accepted', 'completed', 'rejected'], true)) {
                $status = 'pending';
            }

            $directRequests[] = [
                'request_id' => (int)$row['request_id'],
                'customer' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
                'avatar' => $row['profilePicture'] ?: null,
                'category' => $row['category'] ?? 'General',
                'title' => $row['title'] ?? '',
                'description' => $row['description'] ?? '',
                'date' => formatDateLabel($row['created_at'] ?? ''),
                'deadline' => $row['finish_date'] ? formatDateLabel($row['finish_date']) : null,
                'status' => $status,
                'type' => 'direct',
                'budget' => null
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('requests_direct', $e);
        $directRequests = [];
    }

    $publicRequests = [];
    // Public requests (company-type requests where this company has not quoted yet)
    try {
        // Preferred schema: jobrequest.dateCreated
        $stmt = $pdo->prepare("
        SELECT
            jr.request_id,
            jr.title,
            jr.description,
            jr.dateCreated AS created_at,
            jr.finish_date,
            c.name AS category,
            u.f_name,
            u.l_name,
            u.profilePicture
        FROM jobrequest jr
        INNER JOIN category c ON c.category_id = jr.category_id
        INNER JOIN user u ON u.user_id = jr.user_id
                WHERE (jr.service_provider_type = 'company' OR jr.service_provider_type = 'both')
          AND jr.status = 'pending'
                    AND jr.finish_date >= CURDATE()
          AND NOT EXISTS (
              SELECT 1 FROM companyquotation cq
              WHERE cq.request_id = jr.request_id AND cq.company_id = ?
          )
        ORDER BY jr.dateCreated DESC
        LIMIT 5
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $publicRequests[] = [
            'request_id' => (int)$row['request_id'],
            'customer' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
            'avatar' => $row['profilePicture'] ?: null,
            'category' => $row['category'] ?? 'General',
            'title' => $row['title'] ?? '',
            'description' => $row['description'] ?? '',
            'date' => formatDateLabel($row['created_at'] ?? ''),
            'deadline' => $row['finish_date'] ? formatDateLabel($row['finish_date']) : null,
            'status' => 'bidding',
            'type' => 'public'
        ];
    }
    } catch (PDOException $e) {
        // Fallback schema: jobrequest.created_at
        logDashboardSectionError('requests_public', $e);
        try {
            $stmt = $pdo->prepare("
                SELECT
                    jr.request_id,
                    jr.title,
                    jr.description,
                    jr.dateCreated AS created_at,
                    jr.finish_date,
                    c.name AS category,
                    u.f_name,
                    u.l_name,
                    u.profilePicture
                FROM jobrequest jr
                INNER JOIN category c ON c.category_id = jr.category_id
                INNER JOIN user u ON u.user_id = jr.user_id
                                WHERE (jr.service_provider_type = 'company' OR jr.service_provider_type = 'both')
                  AND jr.status = 'pending'
                                    AND jr.finish_date >= CURDATE()
                  AND NOT EXISTS (
                      SELECT 1 FROM companyquotation cq
                      WHERE cq.request_id = jr.request_id AND cq.company_id = ?
                  )
                ORDER BY jr.dateCreated DESC
                LIMIT 5
            ");
            $stmt->execute([$companyId]);
            $publicRequests = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $publicRequests[] = [
                    'request_id' => (int)$row['request_id'],
                    'customer' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
                    'avatar' => $row['profilePicture'] ?: null,
                    'category' => $row['category'] ?? 'General',
                    'title' => $row['title'] ?? '',
                    'description' => $row['description'] ?? '',
                    'date' => formatDateLabel($row['created_at'] ?? ''),
                    'deadline' => $row['finish_date'] ? formatDateLabel($row['finish_date']) : null,
                    'status' => 'bidding',
                    'type' => 'public'
                ];
            }
        } catch (PDOException $e2) {
            logDashboardSectionError('requests_public_fallback', $e2);
            $publicRequests = [];
        }
    }

    $projects = [];
    // Recent projects
    try {
        $stmt = $pdo->prepare("
        SELECT
            p.project_id,
            p.title,
            p.status,
            p.progress,
            p.start_date,
            p.end_date,
            u.f_name,
            u.l_name
        FROM project p
        INNER JOIN user u ON u.user_id = p.customer_id
                WHERE p.company_id = ?
                    AND (
                        p.status <> 'planned'
                        OR EXISTS (
                                SELECT 1
                                FROM contract c
                                WHERE c.project_id = p.project_id
                                    AND c.company_id = p.company_id
                                    AND (
                                        c.customer_response = 'accepted'
                                        OR c.terms_accepted = 1
                                        OR c.signed_at IS NOT NULL
                                        OR c.status IN ('active','in_progress','milestone_pending')
                                    )
                        )
                    )
        ORDER BY p.project_id DESC
        LIMIT 5
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $projects[] = [
            'project_id' => (int)$row['project_id'],
            'title' => $row['title'] ?? '',
            'client' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
            'status' => $row['status'] ?? 'planned',
            'progress' => (int)($row['progress'] ?? 0),
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date']
        ];
    }
    } catch (PDOException $e) {
        logDashboardSectionError('projects_recent', $e);
        $projects = [];
    }

    // Calendar: System events (project start/end + milestones)
    $calendarSystemEvents = [];
    try {
        // Project start/end dates (keep it bounded to avoid heavy payloads)
        $stmt = $pdo->prepare("
            SELECT project_id, title, start_date, end_date
            FROM project
            WHERE company_id = ?
              AND (start_date IS NOT NULL OR end_date IS NOT NULL)
            ORDER BY project_id DESC
            LIMIT 50
        ");
        $stmt->execute([$companyId]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $projectId = (int)($row['project_id'] ?? 0);
            $projectTitle = $row['title'] ?? '';
            $startDate = $row['start_date'] ?? null;
            $endDate = $row['end_date'] ?? null;

            if (!empty($startDate)) {
                $calendarSystemEvents[] = [
                    'id' => 'sys-project-start-' . $projectId,
                    'date' => $startDate,
                    'title' => 'Project Start: ' . $projectTitle,
                    'description' => 'Project start date',
                    'time' => 'All day',
                    'type' => 'system',
                    'is_system' => true,
                    'project_id' => $projectId
                ];
            }

            if (!empty($endDate)) {
                $calendarSystemEvents[] = [
                    'id' => 'sys-project-end-' . $projectId,
                    'date' => $endDate,
                    'title' => 'Project End: ' . $projectTitle,
                    'description' => 'Project end date',
                    'time' => 'All day',
                    'type' => 'system',
                    'is_system' => true,
                    'project_id' => $projectId
                ];
            }
        }
    } catch (PDOException $e) {
        logDashboardSectionError('calendar_projects', $e);
    }

    try {
        // Milestone due dates (new table)
        $stmt = $pdo->prepare("
            SELECT
                p.project_id,
                p.title AS project_title,
                m.milestone_id,
                m.title AS milestone_title,
                m.description AS milestone_description,
                m.due_date,
                m.status
            FROM contract c
            INNER JOIN project p ON p.project_id = c.project_id
            INNER JOIN contract_milestone m ON m.contract_id = c.contract_id
            WHERE c.company_id = ?
              AND m.due_date IS NOT NULL
            ORDER BY m.due_date ASC
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $milestoneId = (int)($row['milestone_id'] ?? 0);
            $projectId = (int)($row['project_id'] ?? 0);
            $milestoneTitle = $row['milestone_title'] ?? ('Milestone #' . $milestoneId);
            $projectTitle = $row['project_title'] ?? '';
            $dueDate = $row['due_date'] ?? null;

            if (empty($dueDate)) {
                continue;
            }

            $calendarSystemEvents[] = [
                'id' => 'sys-milestone-' . $milestoneId,
                'date' => $dueDate,
                'title' => 'Milestone: ' . $milestoneTitle,
                'description' => 'Project: ' . $projectTitle,
                'time' => 'All day',
                'type' => 'system',
                'is_system' => true,
                'project_id' => $projectId,
                'milestone_id' => $milestoneId,
                'status' => $row['status'] ?? null
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('calendar_milestones', $e);
    }

    $contracts = [];
    // Recent contracts
    try {
        $stmt = $pdo->prepare("
        SELECT
            c.contract_id,
            c.contract_number,
            c.project_title,
            c.start_date,
            c.end_date,
            c.status,
            c.total_budget,
            c.created_at,
            u.f_name,
            u.l_name
        FROM contract c
        INNER JOIN user u ON u.user_id = c.customer_id
        WHERE c.company_id = ?
        ORDER BY c.created_at DESC
        LIMIT 5
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $contracts[] = [
            'contract_id' => (int)$row['contract_id'],
            'contract_number' => $row['contract_number'],
            'title' => $row['project_title'] ?: ('Contract #' . $row['contract_id']),
            'customer' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'status' => $row['status'] ?? 'draft',
            'total_budget' => (float)($row['total_budget'] ?? 0)
        ];
    }
    } catch (PDOException $e) {
        logDashboardSectionError('contracts_recent', $e);
        $contracts = [];
    }

    $payments = [];
    // Recent payments (income only; expenses not modeled in payment table)
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM (
                SELECT 
                    mp.payment_id as id,
                    COALESCE(mp.paid_at, mp.payment_date) as date,
                    p.title as project_name,
                    u.f_name,
                    u.l_name,
                    mp.amount,
                    mp.status,
                    cm.title as milestone_description
                FROM milestonepayment mp
                JOIN contract_milestone cm ON mp.milestone_id = cm.milestone_id
                JOIN contract c ON cm.contract_id = c.contract_id
                JOIN project p ON c.project_id = p.project_id
                JOIN user u ON p.customer_id = u.user_id
                WHERE p.company_id = ?
                
                UNION ALL
                
                SELECT 
                    cph.payment_id as id,
                    COALESCE(cph.completed_at, cph.created_at) as date,
                    p.title as project_name,
                    u.f_name,
                    u.l_name,
                    cph.amount,
                    cph.status,
                    CONCAT(REPLACE(cph.payment_type, '_', ' '), ': ', COALESCE(cm.title, 'General payment')) as milestone_description
                FROM contract_payment_history cph
                JOIN contract c ON cph.contract_id = c.contract_id
                JOIN project p ON c.project_id = p.project_id
                JOIN user u ON p.customer_id = u.user_id
                LEFT JOIN contract_milestone cm ON cph.milestone_id = cm.milestone_id
                WHERE p.company_id = ?
                AND cph.status = 'completed'
                AND cph.payment_type IN ('milestone_release', 'upfront_payment', 'bonus')
            ) AS combined_income
            ORDER BY date DESC
            LIMIT 5
        ");
        $stmt->execute([$companyId, $companyId]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $payments[] = [
                'payment_id' => (int)$row['id'],
                'title' => $row['project_name'] ?: 'Job Payment',
                'description' => $row['milestone_description'],
                'subtitle' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
                'amount' => (float)($row['amount'] ?? 0),
                'status' => $row['status'] ?? 'pending',
                'date' => formatDateLabel($row['date'] ?? '')
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('payments_recent', $e);
        $payments = [];
    }

    $workforce = [];
    // Workforce summary
    try {
        $stmt = $pdo->prepare("
            SELECT specialty, total_count, active_count, inactive_count, avg_rating
            FROM staffsummary
            WHERE company_id = ?
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $workforce[] = [
                'specialty' => $row['specialty'],
                'total' => (int)($row['total_count'] ?? 0),
                'active' => (int)($row['active_count'] ?? 0),
                'inactive' => (int)($row['inactive_count'] ?? 0),
                'avg_rating' => (float)($row['avg_rating'] ?? 0)
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('workforce', $e);
        $workforce = [];
    }

    $feedback = [];
    $feedbackStats = ['total_reviews' => 0, 'average_rating' => 0];
    // Customer feedback (latest)
    try {
        $stmt = $pdo->prepare("
            SELECT
                f.feedback_id,
                f.rating,
                f.comments,
                f.date AS review_date,
                u.f_name,
                u.l_name
            FROM feedback f
            INNER JOIN project p ON p.project_id = f.project_id
            INNER JOIN user u ON u.user_id = f.given_by
            WHERE p.company_id = ?
            ORDER BY f.date DESC
            LIMIT 3
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $feedback[] = [
                'feedback_id' => (int)$row['feedback_id'],
                'rating' => (int)($row['rating'] ?? 0),
                'comments' => $row['comments'] ?? '',
                'customer' => trim(($row['f_name'] ?? '') . ' ' . ($row['l_name'] ?? '')),
                'date' => formatDateLabel($row['review_date'] ?? '')
            ];
        }

        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS total_reviews, COALESCE(AVG(f.rating), 0) AS average_rating
            FROM feedback f
            INNER JOIN project p ON p.project_id = f.project_id
            WHERE p.company_id = ?
        ");
        $stmt->execute([$companyId]);
        $feedbackStats = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_reviews' => 0, 'average_rating' => 0];
    } catch (PDOException $e) {
        logDashboardSectionError('feedback', $e);
        $feedback = [];
        $feedbackStats = ['total_reviews' => 0, 'average_rating' => 0];
    }

    $supportTickets = [];
    // Support tickets (latest)
    try {
        $stmt = $pdo->prepare("
            SELECT
                ticket_id,
                ticket_number,
                title,
                priority,
                status,
                created_at
            FROM support_tickets
            WHERE user_type = 'company' AND user_id = ?
            ORDER BY created_at DESC
            LIMIT 3
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $supportTickets[] = [
                'ticket_id' => (int)$row['ticket_id'],
                'ticket_number' => $row['ticket_number'],
                'title' => $row['title'] ?? '',
                'priority' => $row['priority'] ?? 'medium',
                'status' => $row['status'] ?? 'open',
                'date' => formatDateLabel($row['created_at'] ?? '')
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('support_tickets', $e);
        $supportTickets = [];
    }

    $earningsByMonth = [];
    // Earnings by month (last 6 months)
    try {
        $stmt = $pdo->prepare("
            SELECT DATE_FORMAT(p.paymentDate, '%Y-%m') AS ym, COALESCE(SUM(p.amount), 0) AS total
            FROM payment p
            INNER JOIN contract c ON c.job_request_id = p.job_request_id
            WHERE c.company_id = ? AND p.status = 'completed'
              AND p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY ym
            ORDER BY ym ASC
        ");
        $stmt->execute([$companyId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $label = $row['ym'] ? date('M', strtotime($row['ym'] . '-01')) : $row['ym'];
            $earningsByMonth[] = [
                'month' => $label,
                'ym' => $row['ym'],
                'amount' => (float)($row['total'] ?? 0)
            ];
        }
    } catch (PDOException $e) {
        logDashboardSectionError('earnings_by_month', $e);
        $earningsByMonth = [];
    }

    // Income chart (period-dependent)
    $incomeLabels = [];
    $incomeValues = [];
    $incomeSummary = ['total' => 0, 'avg' => 0, 'growth_pct' => null];
    $periodDays = 7;

    if ($chartPeriod === '7d') {
        $periodDays = 7;
        try {
            $stmt = $pdo->prepare("
                SELECT DATE(p.paymentDate) AS day, COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY day
                ORDER BY day ASC
            ");
            $stmt->execute([$companyId]);
            $map = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $map[$row['day']] = (float)$row['total'];
            }

            for ($i = 6; $i >= 0; $i--) {
                $day = date('Y-m-d', strtotime("-$i day"));
                $incomeLabels[] = date('D', strtotime($day));
                $incomeValues[] = $map[$day] ?? 0;
            }

            $currentSum = array_sum($incomeValues);
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
                  AND p.paymentDate < DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            ");
            $stmt->execute([$companyId]);
            $previousSum = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $avgDaily = $periodDays > 0 ? ($currentSum / $periodDays) : 0;
            $growthPct = $previousSum > 0 ? (($currentSum - $previousSum) / $previousSum) * 100 : null;

            $incomeSummary = [
                'total' => $currentSum,
                'avg' => $avgDaily,
                'growth_pct' => $growthPct
            ];
        } catch (PDOException $e) {
            logDashboardSectionError('income_chart_7d', $e);
            $incomeLabels = [];
            $incomeValues = [];
            $incomeSummary = ['total' => 0, 'avg' => 0, 'growth_pct' => null];
        }
    } elseif ($chartPeriod === '30d') {
        // 4 week buckets (28 days) for a clean chart
        $bucketCount = 4;
        $incomeLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];

        try {
            $stmt = $pdo->prepare("
                SELECT FLOOR(DATEDIFF(CURDATE(), DATE(p.paymentDate)) / 7) AS week_ago,
                       COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 27 DAY)
                GROUP BY week_ago
            ");
            $stmt->execute([$companyId]);
            $map = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $idx = (int)($row['week_ago'] ?? 0);
                if ($idx >= 0 && $idx < 4) {
                    $map[3 - $idx] = (float)$row['total'];
                }
            }
            $incomeValues = array_values($map);

            $currentSum = array_sum($incomeValues);
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(CURDATE(), INTERVAL 55 DAY)
                  AND p.paymentDate < DATE_SUB(CURDATE(), INTERVAL 28 DAY)
            ");
            $stmt->execute([$companyId]);
            $previousSum = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $avgDaily = $currentSum / 28;
            $growthPct = $previousSum > 0 ? (($currentSum - $previousSum) / $previousSum) * 100 : null;

            $incomeSummary = [
                'total' => $currentSum,
                'avg' => $avgDaily,
                'growth_pct' => $growthPct
            ];
        } catch (PDOException $e) {
            logDashboardSectionError('income_chart_30d', $e);
            $incomeLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            $incomeValues = [0, 0, 0, 0];
            $incomeSummary = ['total' => 0, 'avg' => 0, 'growth_pct' => null];
        }
    } else {
        // 3 month buckets (current and previous 2 months)
        $incomeLabels = [];
        $incomeValues = [];
        $months = [];
        for ($i = 2; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime("first day of -$i month"));
            $months[] = $ym;
            $incomeLabels[] = date('M', strtotime($ym . '-01'));
        }

        try {
            $stmt = $pdo->prepare("
                SELECT DATE_FORMAT(p.paymentDate, '%Y-%m') AS ym, COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 2 MONTH)
                GROUP BY ym
            ");
            $stmt->execute([$companyId]);
            $map = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $map[$row['ym']] = (float)$row['total'];
            }
            foreach ($months as $ym) {
                $incomeValues[] = $map[$ym] ?? 0;
            }

            $currentSum = array_sum($incomeValues);
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(p.amount), 0) AS total
                FROM payment p
                INNER JOIN contract c ON c.job_request_id = p.job_request_id
                WHERE c.company_id = ? AND p.status = 'completed'
                  AND p.paymentDate >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 5 MONTH)
                  AND p.paymentDate < DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 2 MONTH)
            ");
            $stmt->execute([$companyId]);
            $previousSum = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $avgDaily = $currentSum / 90;
            $growthPct = $previousSum > 0 ? (($currentSum - $previousSum) / $previousSum) * 100 : null;

            $incomeSummary = [
                'total' => $currentSum,
                'avg' => $avgDaily,
                'growth_pct' => $growthPct
            ];
        } catch (PDOException $e) {
            logDashboardSectionError('income_chart_3m', $e);
            $incomeValues = [0, 0, 0];
            $incomeSummary = ['total' => 0, 'avg' => 0, 'growth_pct' => null];
        }
    }

    $response = [
        'success' => true,
        'data' => [
            'kpis' => [
                'active_projects' => $activeProjects,
                'pending_requests' => $pendingRequests,
                'total_earnings' => $totalEarnings,
                'average_rating' => $avgRating
            ],
            'requests' => [
                'direct' => $directRequests,
                'public' => $publicRequests
            ],
            'projects' => $projects,
            'calendar' => [
                'system_events' => $calendarSystemEvents
            ],
            'contracts' => $contracts,
            'payments' => $payments,
            'workforce' => $workforce,
            'feedback' => $feedback,
            'feedbackStats' => [
                'total_reviews' => (int)($feedbackStats['total_reviews'] ?? 0),
                'average_rating' => (float)($feedbackStats['average_rating'] ?? 0)
            ],
            'supportTickets' => $supportTickets,
            'earningsByMonth' => $earningsByMonth,
            'incomeChart' => [
                'period' => $chartPeriod,
                'labels' => $incomeLabels,
                'values' => $incomeValues,
                'summary' => $incomeSummary
            ]
        ]
    ];

    if ($debug && !empty($sectionErrors)) {
        $response['errors'] = $sectionErrors;
    }

    echo json_encode($response);
} catch (Throwable $e) {
    logDashboardSectionError('fatal', $e);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load dashboard data']);
}
