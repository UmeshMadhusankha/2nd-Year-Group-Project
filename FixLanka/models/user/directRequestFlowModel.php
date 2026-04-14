<?php
require_once __DIR__ . '/../../config/database.php';

class DirectRequestFlowModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllowedCategoryIdsForProvider(string $providerType, int $providerId): array {
        if ($providerId <= 0) {
            return [];
        }

        if ($providerType === 'company') {
            return $this->getCompanyCategoryIds($providerId);
        }

        return $this->getRepairerCategoryIds($providerId);
    }

    public function getPendingListedJobs(int $userId, string $providerType): array {
        $sql = "
            SELECT
                jr.request_id,
                jr.title,
                jr.category_id,
                c.name AS category_name,
                l.district,
                jr.finish_date
            FROM jobrequest jr
            LEFT JOIN category c ON c.category_id = jr.category_id
            LEFT JOIN location l ON l.location_id = jr.location_id
            WHERE jr.user_id = :user_id
              AND LOWER(jr.status) IN ('pending', 'open')
              AND jr.finish_date >= CURDATE()
        ";

        if ($providerType === 'company') {
            $sql .= " AND LOWER(jr.service_provider_type) IN ('company', 'both')";
        } else {
            $sql .= " AND LOWER(jr.service_provider_type) IN ('individual', 'both')";
        }

        $sql .= " ORDER BY jr.dateCreated DESC, jr.request_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function createDirectJobRequest(int $userId, array $payload): int {
        $stmt = $this->pdo->prepare("\n            INSERT INTO directjobrequest\n                (user_id, category_id, provider_id, provider_type, title, description, district, address, finish_date, photos)\n            VALUES\n                (:user_id, :category_id, :provider_id, :provider_type, :title, :description, :district, :address, :finish_date, :photos)\n        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':category_id' => (int)$payload['category_id'],
            ':provider_id' => (int)$payload['provider_id'],
            ':provider_type' => $payload['provider_type'],
            ':title' => $payload['title'],
            ':description' => $payload['description'],
            ':district' => $payload['district'],
            ':address' => $payload['address'],
            ':finish_date' => $payload['finish_date'],
            ':photos' => $payload['photos'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function createDirectRequestQuote(int $userId, int $requestId, int $providerId, string $providerType): int {
        $requestStmt = $this->pdo->prepare("\n            SELECT request_id, user_id, category_id\n            FROM jobrequest\n            WHERE request_id = :request_id\n            LIMIT 1\n        ");
        $requestStmt->execute([':request_id' => $requestId]);
        $job = $requestStmt->fetch(PDO::FETCH_ASSOC);

        if (!$job || (int)$job['user_id'] !== $userId) {
            throw new RuntimeException('Selected job was not found for the current user.');
        }

        $allowedCategoryIds = $this->getAllowedCategoryIdsForProvider($providerType, $providerId);
        $jobCategoryId = (int)($job['category_id'] ?? 0);

        if ($jobCategoryId <= 0 || !in_array($jobCategoryId, $allowedCategoryIds, true)) {
            throw new RuntimeException('This service provider does not provide the selected job type.');
        }

        $existsStmt = $this->pdo->prepare("\n            SELECT id\n            FROM directrequestquotes\n            WHERE user_id = :user_id\n              AND request_id = :request_id\n              AND provider_id = :provider_id\n              AND provider_type = :provider_type\n            LIMIT 1\n        ");
        $existsStmt->execute([
            ':user_id' => $userId,
            ':request_id' => $requestId,
            ':provider_id' => $providerId,
            ':provider_type' => $providerType,
        ]);

        $existingId = $existsStmt->fetchColumn();
        if ($existingId !== false) {
            return (int)$existingId;
        }

        $insertStmt = $this->pdo->prepare("\n            INSERT INTO directrequestquotes\n                (user_id, request_id, provider_id, provider_type)\n            VALUES\n                (:user_id, :request_id, :provider_id, :provider_type)\n        ");
        $insertStmt->execute([
            ':user_id' => $userId,
            ':request_id' => $requestId,
            ':provider_id' => $providerId,
            ':provider_type' => $providerType,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    private function getRepairerCategoryIds(int $repairerId): array {
        $stmt = $this->pdo->prepare("SELECT category_id FROM repairer WHERE repairer_id = :repairer_id LIMIT 1");
        $stmt->execute([':repairer_id' => $repairerId]);
        $raw = $stmt->fetchColumn();

        if ($raw === false || $raw === null) {
            return [];
        }

        $ids = array_filter(array_map('intval', array_map('trim', explode(',', (string)$raw))), static function ($id) {
            return $id > 0;
        });

        return array_values(array_unique($ids));
    }

    private function getCompanyCategoryIds(int $companyId): array {
        $stmt = $this->pdo->prepare("SELECT business_type FROM company WHERE company_id = :company_id LIMIT 1");
        $stmt->execute([':company_id' => $companyId]);
        $businessType = (string)($stmt->fetchColumn() ?: '');

        if (trim($businessType) === '') {
            return [];
        }

        $names = array_values(array_filter(array_map('trim', explode(',', $businessType)), static function ($value) {
            return $value !== '';
        }));

        if (!$names) {
            return [];
        }

        $categoryIds = [];
        $categoryStmt = $this->pdo->prepare('SELECT category_id FROM category WHERE LOWER(name) = LOWER(:name) LIMIT 1');

        foreach ($names as $name) {
            $categoryStmt->execute([':name' => $name]);
            $id = (int)$categoryStmt->fetchColumn();
            if ($id > 0) {
                $categoryIds[] = $id;
            }
        }

        return array_values(array_unique($categoryIds));
    }
}
