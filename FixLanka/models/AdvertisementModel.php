<?php

class AdvertisementModel
{
	private PDO $pdo;

	private const APPROVED_STATUSES = [
		'approved',
		'scheduled',
		'paused',
		'inactive',
		'suspended',
		'expired',
	];

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	private function tableExists(string $tableName): bool
	{
		$stmt = $this->pdo->prepare(
			'SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1'
		);
		$stmt->execute([':t' => $tableName]);
		return (bool)$stmt->fetchColumn();
	}

	public function isReady(): bool
	{
		return $this->tableExists('advertisement');
	}

	public function getStats(): array
	{
		if (!$this->isReady()) {
			return ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
		}

		$sql = "
			SELECT
				COUNT(*) AS total,
				SUM(status = 'pending') AS pending,
				SUM(LOWER(status) IN ('approved','scheduled','paused','inactive','suspended','expired')) AS approved,
				SUM(status = 'rejected') AS rejected
			FROM advertisement
		";
		$row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

		return [
			'total' => (int)($row['total'] ?? 0),
			'pending' => (int)($row['pending'] ?? 0),
			'approved' => (int)($row['approved'] ?? 0),
			'rejected' => (int)($row['rejected'] ?? 0),
		];
	}

	public function getAdvertisements(array $filters): array
	{
		if (!$this->isReady()) {
			return [];
		}

		$where = [];
		$params = [];

		if (!empty($filters['status'])) {
			$status = strtolower(trim((string)$filters['status']));
			if ($status === 'approved') {
				$where[] = "LOWER(a.status) IN ('approved','scheduled','paused','inactive','suspended','expired')";
			} else {
				$where[] = 'LOWER(a.status) = :status';
				$params[':status'] = $status;
			}
		}

		if (!empty($filters['type'])) {
			$where[] = 'a.type = :type';
			$params[':type'] = $filters['type'];
		}

		if (!empty($filters['search'])) {
			$where[] = "(a.title LIKE :search OR (CASE WHEN a.provider_type='company' THEN c.name ELSE CONCAT(r.f_name,' ',r.l_name) END) LIKE :search)";
			$params[':search'] = '%' . $filters['search'] . '%';
		}

		$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

		$sql = "
			SELECT
				a.ad_id,
				a.title,
				a.type,
				a.budget,
				a.status,
				a.submission_date,
				a.provider_id,
				a.provider_type,
				(CASE
					WHEN a.provider_type = 'company' THEN c.name
					ELSE CONCAT(r.f_name, ' ', r.l_name)
				END) AS provider_name
			FROM advertisement a
			LEFT JOIN company c ON a.provider_type='company' AND c.company_id = a.provider_id
			LEFT JOIN repairer r ON a.provider_type='repairer' AND r.repairer_id = a.provider_id
			{$whereSql}
			ORDER BY
				CASE
					WHEN a.status = 'pending' THEN
						CASE
							WHEN LOWER(a.type) = 'sponsored' THEN 0
							WHEN LOWER(a.type) = 'featured' THEN 1
							WHEN LOWER(a.type) = 'banner' THEN 2
							ELSE 3
						END
					ELSE 4
				END,
				a.submission_date DESC,
				a.ad_id DESC
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getAdvertisementById(int $adId): ?array
	{
		if (!$this->isReady()) {
			return null;
		}

		$sql = "
			SELECT
				a.*,
				(CASE
					WHEN a.provider_type = 'company' THEN c.name
					ELSE CONCAT(r.f_name, ' ', r.l_name)
				END) AS provider_name,
				(CASE
					WHEN a.provider_type = 'company' THEN c.email
					ELSE r.email
				END) AS provider_email,
				(CASE
					WHEN a.provider_type = 'company' THEN c.contact_no
					ELSE r.phoneNumber
				END) AS provider_phone
			FROM advertisement a
			LEFT JOIN company c ON a.provider_type='company' AND c.company_id = a.provider_id
			LEFT JOIN repairer r ON a.provider_type='repairer' AND r.repairer_id = a.provider_id
			WHERE a.ad_id = :ad_id
			LIMIT 1
		";

		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':ad_id' => $adId]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row ?: null;
	}

	public function reviewAdvertisement(int $adId, string $newStatus, ?int $reviewedBy, string $notes = ''): bool
	{
		if (!$this->isReady()) {
			throw new RuntimeException('Advertisement table is missing.');
		}

		if (!in_array($newStatus, ['approved', 'rejected'], true)) {
			throw new InvalidArgumentException('Invalid review status.');
		}

		$sql = "
			UPDATE advertisement
			SET status = :status,
				reviewed_by = :reviewed_by,
				reviewed_at = NOW(),
				moderator_notes = :notes
			WHERE ad_id = :ad_id
			  AND status = 'pending'
		";
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([
			':status' => $newStatus,
			':reviewed_by' => $reviewedBy,
			':notes' => $notes,
			':ad_id' => $adId,
		]);

		return $stmt->rowCount() > 0;
	}
}

