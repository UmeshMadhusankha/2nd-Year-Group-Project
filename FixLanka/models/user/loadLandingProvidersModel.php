<?php
require_once __DIR__ . '/../../config/database.php';

class LoadLandingProvidersModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        if (!($pdo instanceof PDO)) {
            $pdo = getDatabaseConnection();
        }
        $this->pdo = $pdo;
    }

    public function loadLandingProviders(string $providerType, array $filters, int $limit, int $offset): array {
        $repairers = [];
        $companies = [];

        if ($providerType === 'all' || $providerType === 'individual') {
            $repairers = $this->getRepairers($filters, $limit, $offset);
        }

        if ($providerType === 'all' || $providerType === 'company') {
            $companies = $this->getCompanies($filters, $limit, $offset);
        }

        if ($providerType === 'individual') {
            $data = $repairers;
            $total = $this->countRepairers($filters);
        } elseif ($providerType === 'company') {
            $data = $companies;
            $total = $this->countCompanies($filters);
        } else {
            $data = array_merge($repairers, $companies);
            usort($data, static fn($a, $b) => ($b['ratings'] ?? 0) <=> ($a['ratings'] ?? 0));
            $data = array_slice($data, 0, $limit);
            $total = $this->countRepairers($filters) + $this->countCompanies($filters);
        }

        return [
            'data' => $data,
            'repairers' => $repairers,
            'companies' => $companies,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'hasMore' => ($offset + $limit) < $total,
            ],
        ];
    }

    private function getRepairers(array $filters, int $limit, int $offset): array {
        $sql = "
            SELECT
                r.*,
                CONCAT(r.f_name, ' ', r.l_name) AS full_name,
                c.name AS category_name,
                COALESCE(r.ratings, 0) AS ratings,
                'individual' AS provider_type
            FROM Repairer r
            LEFT JOIN Category c ON c.category_id = r.category_id
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND r.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['min_rating'])) {
            $sql .= ' AND r.ratings >= ?';
            $params[] = (float)$filters['min_rating'];
        }

        if (!empty($filters['district'])) {
            $sql .= ' AND r.districts LIKE ?';
            $params[] = '%' . $filters['district'] . '%';
        }

        $sql .= ' ORDER BY r.ratings DESC, r.completedJobsCount DESC, r.repairer_id DESC LIMIT ? OFFSET ?';

        $stmt = $this->pdo->prepare($sql);

        $index = 1;
        foreach ($params as $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index++, $value, $type);
        }
        $stmt->bindValue($index++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($index, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCompanies(array $filters, int $limit, int $offset): array {
        $sql = "
            SELECT
                c.*,
                COALESCE(c.rating, 0) AS ratings,
                'company' AS provider_type
            FROM Company c
            WHERE 1=1
        ";

        $params = [];

        if (!empty($filters['min_rating'])) {
            $sql .= ' AND c.rating >= ?';
            $params[] = (float)$filters['min_rating'];
        }

        if (!empty($filters['district'])) {
            $sql .= ' AND (c.districts LIKE ? OR c.address LIKE ?)';
            $params[] = '%' . $filters['district'] . '%';
            $params[] = '%' . $filters['district'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $categoryName = $this->getCategoryName((int)$filters['category_id']);
            if ($categoryName !== null && $categoryName !== '') {
                $sql .= ' AND c.business_type LIKE ?';
                $params[] = '%' . $categoryName . '%';
            }
        }

        $sql .= ' ORDER BY c.rating DESC, c.company_id DESC LIMIT ? OFFSET ?';

        $stmt = $this->pdo->prepare($sql);

        $index = 1;
        foreach ($params as $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index++, $value, $type);
        }
        $stmt->bindValue($index++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($index, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function countRepairers(array $filters): int {
        $sql = 'SELECT COUNT(*) AS total FROM Repairer r WHERE 1=1';
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= ' AND r.category_id = ?';
            $params[] = (int)$filters['category_id'];
        }

        if (!empty($filters['min_rating'])) {
            $sql .= ' AND r.ratings >= ?';
            $params[] = (float)$filters['min_rating'];
        }

        if (!empty($filters['district'])) {
            $sql .= ' AND r.districts LIKE ?';
            $params[] = '%' . $filters['district'] . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    private function countCompanies(array $filters): int {
        $sql = 'SELECT COUNT(*) AS total FROM Company c WHERE 1=1';
        $params = [];

        if (!empty($filters['min_rating'])) {
            $sql .= ' AND c.rating >= ?';
            $params[] = (float)$filters['min_rating'];
        }

        if (!empty($filters['district'])) {
            $sql .= ' AND (c.districts LIKE ? OR c.address LIKE ?)';
            $params[] = '%' . $filters['district'] . '%';
            $params[] = '%' . $filters['district'] . '%';
        }

        if (!empty($filters['category_id'])) {
            $categoryName = $this->getCategoryName((int)$filters['category_id']);
            if ($categoryName !== null && $categoryName !== '') {
                $sql .= ' AND c.business_type LIKE ?';
                $params[] = '%' . $categoryName . '%';
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    private function getCategoryName(int $categoryId): ?string {
        if ($categoryId <= 0) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT name FROM Category WHERE category_id = ? LIMIT 1');
        $stmt->execute([$categoryId]);
        $name = $stmt->fetchColumn();

        return $name !== false ? (string)$name : null;
    }
}
