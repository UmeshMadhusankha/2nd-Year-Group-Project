<?php
require_once __DIR__ . '/../../config/database.php';

class LoadCurrentUserAddressModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAddressByUserId(int $userId): ?array {
        if ($userId <= 0) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT user_id, address, district FROM User WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return [
            'user_id' => (int)($row['user_id'] ?? 0),
            'address' => (string)($row['address'] ?? ''),
            'district' => (string)($row['district'] ?? ''),
        ];
    }
}
