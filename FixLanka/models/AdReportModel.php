<?php

class AdReportModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function tableExists(string $tableName): bool
    {
        // MariaDB/MySQL does not allow binding parameters in SHOW statements.
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1'
        );
        $stmt->execute([':t' => $tableName]);
        return (bool)$stmt->fetchColumn();
    }

    public function getAdvertisementStatus(int $adId): ?string
    {
        if (!$this->tableExists('advertisement')) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT status FROM advertisement WHERE ad_id = :id');
        $stmt->execute([':id' => $adId]);
        $status = $stmt->fetchColumn();
        return $status !== false ? (string)$status : null;
    }

    public function deleteAdvertisement(int $adId): bool
    {
        if (!$this->tableExists('advertisement')) {
            throw new RuntimeException('advertisement table not found.');
        }

        $stmt = $this->pdo->prepare('DELETE FROM advertisement WHERE ad_id = :id');
        $stmt->execute([':id' => $adId]);
        return $stmt->rowCount() > 0;
    }

    public function markAdvertisementExpired(int $adId): bool
    {
        if (!$this->tableExists('advertisement')) {
            throw new RuntimeException('advertisement table not found.');
        }

        $stmt = $this->pdo->prepare("UPDATE advertisement SET status = 'expired' WHERE ad_id = :id");
        $stmt->execute([':id' => $adId]);
        return $stmt->rowCount() > 0;
    }

    public function isReady(): bool
    {
        return $this->tableExists('advertisement') && $this->tableExists('adreport');
    }

    public function getReports(): array
    {
        if ($this->isReady()) {
            $sql = "
                SELECT
                    ar.report_id AS id,
                    ar.ad_id,
                    a.title AS ad_title,
                    ar.reporter_id,
                    ar.reporter_type,
                    ar.issue_type,
                    ar.description,
                    ar.priority,
                    ar.status,
                    a.status AS ad_status,
                    DATE_FORMAT(ar.created_at, '%Y-%m-%d') AS created_date,
                    (CASE
                        WHEN a.provider_type = 'company' THEN c.name
                        ELSE CONCAT(r.f_name, ' ', r.l_name)
                    END) AS company_name,
                    COALESCE(ar.evidence, '') AS evidence
                FROM adreport ar
                INNER JOIN advertisement a ON a.ad_id = ar.ad_id
                LEFT JOIN company c ON a.provider_type='company' AND c.company_id = a.provider_id
                LEFT JOIN repairer r ON a.provider_type='repairer' AND r.repairer_id = a.provider_id
                ORDER BY ar.created_at DESC, ar.report_id DESC
            ";

            $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                $row['status'] = strtolower(trim((string)($row['status'] ?? 'pending')));
                $row['priority'] = strtolower(trim((string)($row['priority'] ?? 'low')));
                $row['issue_type'] = $this->normalizeIssueType((string)($row['issue_type'] ?? 'other'));
                $row['reporter_type'] = strtolower(trim((string)($row['reporter_type'] ?? 'user')));
                $row['ad_status'] = strtolower(trim((string)($row['ad_status'] ?? 'unknown')));
            }
            unset($row);

            return $rows;
        }

        $mockPath = __DIR__ . '/../includes/admin-modarator/mock-data.php';
        if (is_file($mockPath)) {
            require_once $mockPath;

            if (function_exists('getAdReports')) {
                $reports = getAdReports();
                if (!is_array($reports)) {
                    $reports = [];
                }
                $raw = array_values($reports);
                return array_map(function (array $r) {
                    $status = strtolower(trim((string)($r['status'] ?? 'pending')));
                    $statusMap = [
                        'pending' => 'pending',
                        'investigating' => 'investigating',
                        'resolved' => 'resolved',
                        'escalated' => 'escalated',
                        'dismissed' => 'dismissed',
                    ];

                    $priority = strtolower(trim((string)($r['priority'] ?? 'low')));

                    $issueType = $this->normalizeIssueType((string)($r['issue_type'] ?? 'other'));

                    return [
                        'id' => (int)($r['id'] ?? 0),
                        'ad_id' => (int)($r['ad_id'] ?? 0),
                        'ad_title' => (string)($r['ad_title'] ?? ''),
                        'reporter_id' => 0,
                        'reporter_type' => 'user',
                        'issue_type' => $issueType,
                        'description' => (string)($r['description'] ?? ''),
                        'priority' => in_array($priority, ['critical', 'high', 'medium', 'low'], true) ? $priority : 'low',
                        'status' => $statusMap[$status] ?? 'pending',
                        'ad_status' => 'pending',
                        'created_date' => (string)($r['created_date'] ?? date('Y-m-d')),
                        'company_name' => (string)($r['company_name'] ?? 'Unknown'),
                        'evidence' => (string)($r['evidence'] ?? ''),
                    ];
                }, $raw);
            }
        }

        return [];
    }

    public function updateReport(int $reportId, string $status, string $priority, string $moderatorNotes, ?int $handledBy): bool
    {
        if (!$this->tableExists('adreport')) {
            throw new RuntimeException('adreport table not found. Please apply the latest database schema updates.');
        }

        $sql = "
            UPDATE adreport
            SET status = :status,
                priority = :priority,
                moderator_notes = :notes,
                handled_by = :handled_by
            WHERE report_id = :id
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':priority' => $priority,
            ':notes' => $moderatorNotes,
            ':handled_by' => $handledBy,
            ':id' => $reportId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function dismissReport(int $reportId, string $moderatorNotes, ?int $handledBy): bool
    {
        if (!$this->tableExists('adreport')) {
            throw new RuntimeException('adreport table not found. Please apply the latest database schema updates.');
        }

        $sql = "
            UPDATE adreport
            SET status = 'dismissed',
                moderator_notes = :notes,
                handled_by = :handled_by
            WHERE report_id = :id
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':notes' => $moderatorNotes,
            ':handled_by' => $handledBy,
            ':id' => $reportId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function escalateReport(int $reportId, string $moderatorNotes, ?int $handledBy): bool
    {
        if (!$this->tableExists('adreport')) {
            throw new RuntimeException('adreport table not found. Please apply the latest database schema updates.');
        }

        $sql = "
            UPDATE adreport
            SET status = 'escalated',
                moderator_notes = :notes,
                handled_by = :handled_by
            WHERE report_id = :id
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':notes' => $moderatorNotes,
            ':handled_by' => $handledBy,
            ':id' => $reportId,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function enrichReportsWithAdStatus(array $reports): array
    {
        foreach ($reports as &$report) {
            $adId = (int)($report['ad_id'] ?? 0);
            if ($adId <= 0) {
                $report['ad_status'] = 'unknown';
                continue;
            }

            $status = $this->getAdvertisementStatus($adId);
            $report['ad_status'] = $status ?? 'deleted';
        }
        unset($report);

        return $reports;
    }

    public function getStatistics(array $reports): array
    {
        $total = count($reports);
        $pending = 0;
        $resolved = 0;

        foreach ($reports as $r) {
            $status = $r['status'] ?? '';
            if ($status === 'pending') {
                $pending++;
            }
            if ($status === 'resolved') {
                $resolved++;
            }
        }

        return [
            'total_reports' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
        ];
    }

    private function normalizeIssueType(string $issueType): string
    {
        $t = strtolower(trim($issueType));

        $map = [
            'inappropriate content' => 'inappropriate_content',
            'inappropriate images' => 'inappropriate_content',
            'misleading information' => 'misleading_information',
            'false pricing' => 'misleading_information',
            'spam content' => 'spam',
            'spam' => 'spam',
            'privacy violation' => 'privacy_violation',
            'copyright' => 'copyright_infringement',
            'copyright infringement' => 'copyright_infringement',
            'fraud' => 'fraud',
            'policy violation' => 'other',
            'duplicate listing' => 'other',
            'expired advertisement' => 'other',
            'unverified claims' => 'other',
            'other' => 'other',
        ];

        return $map[$t] ?? 'other';
    }
}
