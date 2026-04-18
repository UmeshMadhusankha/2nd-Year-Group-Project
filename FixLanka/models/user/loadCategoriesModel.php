<?php
require_once __DIR__ . '/../../config/database.php';

class LoadCategoriesModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllCategories(): array {
        $stmt = $this->pdo->query('SELECT category_id, name FROM Category ORDER BY name ASC');
        return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
    }
}
