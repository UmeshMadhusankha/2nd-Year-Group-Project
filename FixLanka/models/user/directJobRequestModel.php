<?php
require_once __DIR__ . '/../../config/database.php';

class DirectJobRequestModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllByUser(int $userId): array {
        if ($userId <= 0) {
            return [];
        }

        $stmt = $this->pdo->prepare('
            SELECT
                dr.request_id,
                dr.user_id,
                dr.category_id,
                dr.provider_id,
                dr.provider_type,
                dr.title,
                dr.description,
                dr.status,
                dr.district,
                dr.address,
                dr.finish_date,
                dr.date_created,
                dr.photos,
                c.name AS category_name,
                CASE
                    WHEN LOWER(dr.provider_type) = "company" THEN COALESCE(co.name, "Company")
                    ELSE CONCAT(COALESCE(r.f_name, ""), " ", COALESCE(r.l_name, ""))
                END AS provider_name
            FROM directjobrequest dr
            LEFT JOIN category c ON c.category_id = dr.category_id
            LEFT JOIN company co ON LOWER(dr.provider_type) = "company" AND co.company_id = dr.provider_id
            LEFT JOIN repairer r ON LOWER(dr.provider_type) = "individual" AND r.repairer_id = dr.provider_id
            WHERE dr.user_id = ?
            ORDER BY dr.date_created DESC, dr.request_id DESC
        ');

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}