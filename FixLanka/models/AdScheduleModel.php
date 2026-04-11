<?php

class AdScheduleModel
{
    private PDO $pdo;

    private const DEFAULT_ROTATION_SETTINGS = [
        'banner_seconds' => 30,
        'featured_seconds' => 60,
        'sponsored_seconds' => 90,
        'banner_capacity' => 5,
        'featured_capacity' => 3,
        'sponsored_capacity' => 8,
    ];

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

    public function isReady(): bool
    {
        return $this->tableExists('advertisement') && $this->tableExists('adschedule');
    }

    public function getSetupIssues(): array
    {
        $issues = [];
        if (!$this->tableExists('advertisement')) {
            $issues[] = 'Missing required table: advertisement';
        }
        if (!$this->tableExists('adschedule')) {
            $issues[] = 'Missing required table: adschedule';
        }
        if (!$this->tableExists('adrotationsettings')) {
            $issues[] = 'Optional table missing: adrotationsettings (defaults will be used)';
        }
        return $issues;
    }

    public function getRotationSettings(): array
    {
        // If settings table doesn't exist yet, fall back to defaults.
        if (!$this->tableExists('adrotationsettings')) {
            return self::DEFAULT_ROTATION_SETTINGS;
        }

        $sql = "
            SELECT
                banner_seconds,
                featured_seconds,
                sponsored_seconds,
                banner_capacity,
                featured_capacity,
                sponsored_capacity
            FROM adrotationsettings
            ORDER BY setting_id DESC
            LIMIT 1
        ";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];
        $out = self::DEFAULT_ROTATION_SETTINGS;
        foreach (array_keys(self::DEFAULT_ROTATION_SETTINGS) as $k) {
            if (isset($row[$k])) {
                $out[$k] = max(0, (int)$row[$k]);
            }
        }
        return $out;
    }

    public function updateRotationSettings(array $settings, ?int $updatedBy): void
    {
        if (!$this->tableExists('adrotationsettings')) {
            throw new RuntimeException('Rotation settings table is missing. Please apply the latest database schema updates.');
        }

        $payload = self::DEFAULT_ROTATION_SETTINGS;
        foreach ($payload as $k => $v) {
            if (array_key_exists($k, $settings)) {
                $payload[$k] = max(0, (int)$settings[$k]);
            }
        }

        // Basic guardrails (seconds should be reasonable)
        foreach (['banner_seconds', 'featured_seconds', 'sponsored_seconds'] as $secKey) {
            $payload[$secKey] = min(600, $payload[$secKey]);
        }

        $sql = "
            INSERT INTO adrotationsettings (
                banner_seconds, featured_seconds, sponsored_seconds,
                banner_capacity, featured_capacity, sponsored_capacity,
                updated_by
            ) VALUES (
                :banner_seconds, :featured_seconds, :sponsored_seconds,
                :banner_capacity, :featured_capacity, :sponsored_capacity,
                :updated_by
            )
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':banner_seconds' => $payload['banner_seconds'],
            ':featured_seconds' => $payload['featured_seconds'],
            ':sponsored_seconds' => $payload['sponsored_seconds'],
            ':banner_capacity' => $payload['banner_capacity'],
            ':featured_capacity' => $payload['featured_capacity'],
            ':sponsored_capacity' => $payload['sponsored_capacity'],
            ':updated_by' => $updatedBy,
        ]);
    }

    public function getAvailableAds(): array
    {
        if (!$this->isReady()) {
            return [];
        }

        $sql = "
            SELECT
                a.ad_id,
                a.title,
                a.type,
                a.status,
                a.start_date,
                a.end_date,
                EXISTS(
                    SELECT 1
                    FROM adschedule s
                    WHERE s.ad_id = a.ad_id
                      AND s.end_date >= CURDATE()
                ) AS is_scheduled
            FROM advertisement a
            WHERE a.status IN ('approved', 'scheduled', 'active')
            ORDER BY a.submission_date DESC
        ";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function getSchedules(array $filters): array
    {
        if (!$this->isReady()) {
            return [];
        }

        $where = [];
        $params = [];

        if (!empty($filters['placement']) && $filters['placement'] !== 'all') {
            $where[] = 'a.type = :placement';
            $params[':placement'] = $filters['placement'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(a.title LIKE :search OR (CASE WHEN a.provider_type='company' THEN c.name ELSE CONCAT(r.f_name,' ',r.l_name) END) LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $sql = "
            SELECT
                s.schedule_id,
                s.ad_id,
                a.title AS ad_title,
                a.type AS placement,
                a.budget AS budget,
                a.start_date AS campaign_start,
                a.end_date AS campaign_end,
                (CASE
                    WHEN a.provider_type = 'company' THEN c.name
                    ELSE CONCAT(r.f_name, ' ', r.l_name)
                END) AS provider_name,
                s.start_date,
                s.end_date,
                TIME_FORMAT(COALESCE(s.start_time, '00:00:00'), '%H:%i') AS start_time,
                TIME_FORMAT(COALESCE(s.end_time, '23:59:00'), '%H:%i') AS end_time,
                (CASE
                    WHEN CURDATE() < s.start_date THEN 'scheduled'
                    WHEN CURDATE() > s.end_date THEN 'completed'
                    WHEN CURTIME() BETWEEN COALESCE(s.start_time, '00:00:00') AND COALESCE(s.end_time, '23:59:59') THEN 'active'
                    ELSE 'scheduled'
                END) AS status
            FROM adschedule s
            INNER JOIN advertisement a ON a.ad_id = s.ad_id
            LEFT JOIN company c ON a.provider_type='company' AND c.company_id = a.provider_id
            LEFT JOIN repairer r ON a.provider_type='repairer' AND r.repairer_id = a.provider_id
            {$whereSql}
            ORDER BY s.start_date DESC, s.schedule_id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $rows = array_values(array_filter($rows, function ($row) use ($filters) {
                return ($row['status'] ?? '') === $filters['status'];
            }));
        }

        return $rows;
    }

    private function getCampaignRangeOrFail(int $adId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ad_id, status, start_date, end_date FROM advertisement WHERE ad_id = :id LIMIT 1"
        );
        $stmt->execute([':id' => $adId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new RuntimeException('Advertisement not found.');
        }

        $campaignStart = $row['start_date'] ?? null;
        $campaignEnd = $row['end_date'] ?? null;
        if (!$campaignStart || !$campaignEnd) {
            throw new RuntimeException('Campaign date range is missing for this advertisement.');
        }

        return [
            'status' => strtolower((string)($row['status'] ?? '')),
            'start_date' => (string)$campaignStart,
            'end_date' => (string)$campaignEnd,
        ];
    }

    private function assertScheduleWithinCampaign(int $adId, string $scheduleStart, string $scheduleEnd): void
    {
        $campaign = $this->getCampaignRangeOrFail($adId);

        if (!in_array($campaign['status'], ['approved', 'active', 'scheduled', 'paused', 'inactive'], true)) {
            // Only allow scheduling for ads that have passed moderation.
            throw new RuntimeException('Only reviewed advertisements can be scheduled.');
        }

        if ($scheduleStart < $campaign['start_date'] || $scheduleEnd > $campaign['end_date']) {
            throw new RuntimeException(
                "Schedule must be within the campaign date range ({$campaign['start_date']} to {$campaign['end_date']})."
            );
        }
    }

    private function setAdvertisementStatusOnSchedule(int $adId): void
    {
        // When a schedule is created/updated:
        // - If campaign ended => expired
        // - Otherwise => scheduled
        // NOTE: "active" is a time-window concept (daily start/end time) and is
        // computed when displaying, not persisted in advertisement.status.
        // Do not override explicit non-time-based states.

        $stmt = $this->pdo->prepare(
            'SELECT status, start_date, end_date FROM advertisement WHERE ad_id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $adId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $currentStatus = strtolower((string)($row['status'] ?? ''));
        if (!in_array($currentStatus, ['approved', 'scheduled', 'active'], true)) {
            return;
        }

        $startDate = (string)($row['start_date'] ?? '');
        $endDate = (string)($row['end_date'] ?? '');
        $today = date('Y-m-d');

        $newStatus = 'scheduled';
        if ($endDate !== '' && $endDate < $today) {
            $newStatus = 'expired';
        }

        $stmt = $this->pdo->prepare(
            'UPDATE advertisement SET status = :st WHERE ad_id = :id AND status IN (\'approved\',\'scheduled\',\'active\')'
        );
        $stmt->execute([':st' => $newStatus, ':id' => $adId]);
    }

    private function setAdvertisementStatusOnUnschedule(int $adId): void
    {
        // If schedule removed:
        // - If campaign already ended, mark expired
        // - Otherwise revert to approved
        // Do not override explicit non-time-based states.

        $stmt = $this->pdo->prepare('SELECT status, end_date FROM advertisement WHERE ad_id = :id LIMIT 1');
        $stmt->execute([':id' => $adId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $currentStatus = strtolower((string)($row['status'] ?? ''));
        if (!in_array($currentStatus, ['scheduled', 'active'], true)) {
            return;
        }

        $endDate = (string)($row['end_date'] ?? '');
        $newStatus = 'approved';
        if ($endDate !== '' && $endDate < date('Y-m-d')) {
            $newStatus = 'expired';
        }

        $stmt = $this->pdo->prepare('UPDATE advertisement SET status = :st WHERE ad_id = :id');
        $stmt->execute([':st' => $newStatus, ':id' => $adId]);
    }

    public function getStats(): array
    {
        if (!$this->isReady()) {
            return ['total' => 0, 'active_schedules' => 0];
        }

        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(
                    CASE
                        WHEN CURDATE() BETWEEN s.start_date AND s.end_date
                         AND CURTIME() BETWEEN COALESCE(s.start_time, '00:00:00') AND COALESCE(s.end_time, '23:59:59')
                        THEN 1
                        ELSE 0
                    END
                ) AS active_schedules
            FROM adschedule s
        ";
        $row = $this->pdo->query($sql)->fetch();

        return [
            'total' => (int)($row['total'] ?? 0),
            'active_schedules' => (int)($row['active_schedules'] ?? 0),
        ];
    }

    public function countStartingToday(): int
    {
        if (!$this->isReady()) {
            return 0;
        }

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM adschedule WHERE start_date = CURDATE()");
        return (int)$stmt->fetchColumn();
    }

    public function getPlacementAnalytics(): array
    {
        if (!$this->isReady()) {
            return [];
        }

        $sql = "
            SELECT
                a.type AS placement,
                COUNT(*) AS total_schedules,
                SUM(
                    CASE
                        WHEN CURDATE() BETWEEN s.start_date AND s.end_date
                         AND CURTIME() BETWEEN COALESCE(s.start_time, '00:00:00') AND COALESCE(s.end_time, '23:59:59')
                        THEN 1
                        ELSE 0
                    END
                ) AS active_count
            FROM adschedule s
            INNER JOIN advertisement a ON a.ad_id = s.ad_id
            GROUP BY a.type
        ";

        $rows = $this->pdo->query($sql)->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $placement = $row['placement'] ?? '';
            if ($placement === '') {
                continue;
            }
            $out[$placement] = [
                'total_schedules' => (int)($row['total_schedules'] ?? 0),
                'active_count' => (int)($row['active_count'] ?? 0),
            ];
        }
        return $out;
    }

    public function createSchedule(int $adId, string $startDate, string $endDate, ?string $startTime, ?string $endTime): void
    {
        if (!$this->isReady()) {
            throw new RuntimeException('Required tables are missing (advertisement/adschedule).');
        }

        // Moderator cannot change date range: schedule always equals the campaign range.
        $campaign = $this->getCampaignRangeOrFail($adId);
        $startDate = $campaign['start_date'];
        $endDate = $campaign['end_date'];

        $this->assertScheduleWithinCampaign($adId, $startDate, $endDate);
		$this->assertPlacementCapacityAvailable($adId, $startDate, $endDate, $startTime, $endTime, null);

        if ($this->hasOverlappingSchedule($adId, $startDate, $endDate, null)) {
            throw new RuntimeException('This advertisement already has an overlapping schedule.');
        }

        $sql = "
            INSERT INTO adschedule (ad_id, start_date, end_date, start_time, end_time)
            VALUES (:ad_id, :start_date, :end_date, :start_time, :end_time)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ad_id' => $adId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
        ]);

        // Reflect scheduled state across the system (admin/company pages read advertisement.status)
        $this->setAdvertisementStatusOnSchedule($adId);
    }

    public function updateSchedule(int $scheduleId, string $startDate, string $endDate, ?string $startTime, ?string $endTime): void
    {
        if (!$this->isReady()) {
            throw new RuntimeException('Required tables are missing (advertisement/adschedule).');
        }

        $stmt = $this->pdo->prepare('SELECT ad_id FROM adschedule WHERE schedule_id = :id');
        $stmt->execute([':id' => $scheduleId]);
        $adId = (int)($stmt->fetchColumn() ?? 0);
        if ($adId <= 0) {
            throw new RuntimeException('Schedule not found.');
        }

        // Moderator cannot change date range: schedule always equals the campaign range.
        $campaign = $this->getCampaignRangeOrFail($adId);
        $startDate = $campaign['start_date'];
        $endDate = $campaign['end_date'];

        $this->assertScheduleWithinCampaign($adId, $startDate, $endDate);
		$this->assertPlacementCapacityAvailable($adId, $startDate, $endDate, $startTime, $endTime, $scheduleId);

        if ($this->hasOverlappingSchedule($adId, $startDate, $endDate, $scheduleId)) {
            throw new RuntimeException('This advertisement already has an overlapping schedule.');
        }

        $sql = "
            UPDATE adschedule
            SET start_date = :start_date,
                end_date = :end_date,
                start_time = :start_time,
                end_time = :end_time
            WHERE schedule_id = :schedule_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':schedule_id' => $scheduleId,
        ]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('No changes were applied.');
        }

        $this->setAdvertisementStatusOnSchedule($adId);
    }

    private function normalizeTimeValue(?string $time, string $fallback): string
    {
        $t = trim((string)$time);
        if ($t === '') {
            return $fallback;
        }
        // Accept HH:MM and HH:MM:SS
        if (preg_match('/^\d{2}:\d{2}$/', $t)) {
            return $t . ':00';
        }
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $t)) {
            return $t;
        }
        return $fallback;
    }

    private function getPlacementByAdId(int $adId): string
    {
        $stmt = $this->pdo->prepare('SELECT type FROM advertisement WHERE ad_id = :id LIMIT 1');
        $stmt->execute([':id' => $adId]);
        $type = strtolower(trim((string)($stmt->fetchColumn() ?? '')));
        return $type;
    }

    private function assertPlacementCapacityAvailable(
        int $adId,
        string $startDate,
        string $endDate,
        ?string $startTime,
        ?string $endTime,
        ?int $excludeScheduleId
    ): void {
        $placement = $this->getPlacementByAdId($adId);
        if (!in_array($placement, ['banner', 'featured', 'sponsored'], true)) {
            return;
        }

        $settings = $this->getRotationSettings();
        $capKey = $placement . '_capacity';
        $capacity = (int)($settings[$capKey] ?? 0);
        // Capacity of 0 means "unlimited".
        if ($capacity <= 0) {
            return;
        }

        $newStartTime = $this->normalizeTimeValue($startTime, '00:00:00');
        $newEndTime = $this->normalizeTimeValue($endTime, '23:59:59');

        if ($newStartTime > $newEndTime) {
            throw new RuntimeException('Start time must be before end time.');
        }

        $sql = "
            SELECT COUNT(*)
            FROM adschedule s
            INNER JOIN advertisement a ON a.ad_id = s.ad_id
            WHERE a.type = :placement
              AND NOT (s.end_date < :start_date OR s.start_date > :end_date)
              AND NOT (
                COALESCE(s.end_time, '23:59:59') < :new_start_time
                OR COALESCE(s.start_time, '00:00:00') > :new_end_time
              )
        ";

        $params = [
            ':placement' => $placement,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':new_start_time' => $newStartTime,
            ':new_end_time' => $newEndTime,
        ];
        if ($excludeScheduleId !== null) {
            $sql .= ' AND s.schedule_id != :exclude_id';
            $params[':exclude_id'] = $excludeScheduleId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $currentCount = (int)$stmt->fetchColumn();
        if ($currentCount >= $capacity) {
            throw new RuntimeException(
                "This time slot is full for {$placement} ads (max {$capacity}). Try a different time window."
            );
        }
    }

    public function deleteSchedule(int $scheduleId): void
    {
        if (!$this->isReady()) {
            throw new RuntimeException('Required tables are missing (advertisement/adschedule).');
        }

        $stmt = $this->pdo->prepare('SELECT ad_id FROM adschedule WHERE schedule_id = :id');
        $stmt->execute([':id' => $scheduleId]);
        $adId = (int)($stmt->fetchColumn() ?? 0);

        $stmt = $this->pdo->prepare('DELETE FROM adschedule WHERE schedule_id = :id');
        $stmt->execute([':id' => $scheduleId]);

        if ($stmt->rowCount() === 0) {
            throw new RuntimeException('Schedule not found or already deleted.');
        }

        if ($adId > 0) {
            $this->setAdvertisementStatusOnUnschedule($adId);
        }
    }

    private function hasOverlappingSchedule(int $adId, string $startDate, string $endDate, ?int $excludeScheduleId): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM adschedule
            WHERE ad_id = :ad_id
              AND NOT (end_date < :start_date OR start_date > :end_date)
        ";

        $params = [
            ':ad_id' => $adId,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
        ];

        if ($excludeScheduleId !== null) {
            $sql .= ' AND schedule_id != :exclude_id';
            $params[':exclude_id'] = $excludeScheduleId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return ((int)$stmt->fetchColumn()) > 0;
    }
}
