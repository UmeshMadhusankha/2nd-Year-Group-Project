<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/RepairerModel.php';
require_once __DIR__ . '/CompanyModel.php';

class ProviderSearchModel {
    private Repairer $repairerModel;
    private Company $companyModel;

    public function __construct() {
        global $pdo;
        $this->repairerModel = new Repairer($pdo);
        $this->companyModel = new Company($pdo);
    }

    public function featured(string $providerType, int $limit, int $offset): array {
        // offset is ignored by the underlying featured methods currently; keep for API symmetry
        $repairers = [];
        $companies = [];

        if ($providerType === 'all' || $providerType === 'individual') {
            $repairers = $this->repairerModel->getFeatured($limit, 0);
        }

        if ($providerType === 'all' || $providerType === 'company') {
            $companies = $this->companyModel->getFeatured($limit, 0);
        }

        $data = array_merge($repairers, $companies);
        usort($data, fn($a, $b) => ($b['ratings'] ?? 0) <=> ($a['ratings'] ?? 0));
        $data = array_slice($data, 0, $limit);

        $total = $this->totalCount($providerType, []);

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => 0,
                'hasMore' => false,
            ],
        ];
    }

    public function search(string $providerType, array $filters, int $limit, int $offset): array {
        $data = [];

        if ($providerType === 'all' || $providerType === 'individual') {
            $data = array_merge($data, $this->repairerModel->getAll($filters, $limit, $offset));
        }

        if ($providerType === 'all' || $providerType === 'company') {
            $data = array_merge($data, $this->companyModel->getAll($filters, $limit, $offset));
        }

        usort($data, fn($a, $b) => ($b['ratings'] ?? 0) <=> ($a['ratings'] ?? 0));
        $data = array_slice($data, 0, $limit);

        $total = $this->totalCount($providerType, $filters);

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'hasMore' => ($offset + $limit) < $total,
            ],
        ];
    }

    private function totalCount(string $providerType, array $filters): int {
        $total = 0;
        if ($providerType === 'all' || $providerType === 'individual') {
            $total += (int)$this->repairerModel->getCount($filters);
        }
        if ($providerType === 'all' || $providerType === 'company') {
            $total += (int)$this->companyModel->getCount($filters);
        }
        return $total;
    }
}
