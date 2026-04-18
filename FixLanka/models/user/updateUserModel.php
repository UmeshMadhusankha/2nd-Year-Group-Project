<?php
require_once __DIR__ . '/../../config/database.php';

class UpdateUserModel {
    private PDO $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function updateUser(int $userId, array $data): bool {
        if ($userId <= 0) {
            return false;
        }

        $firstName = trim((string)($data['first_name'] ?? ''));
        $lastName = trim((string)($data['last_name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $address = trim((string)($data['address'] ?? ''));
        $district = trim((string)($data['district'] ?? ''));

        if ($firstName === '' || $lastName === '' || $email === '' || $address === '') {
            return false;
        }

        $stmt = $this->pdo->prepare('
            UPDATE User
            SET f_name = :f_name,
                l_name = :l_name,
                email = :email,
                address = :address,
                district = :district
            WHERE user_id = :user_id
        ');

        return $stmt->execute([
            ':f_name' => $firstName,
            ':l_name' => $lastName,
            ':email' => $email,
            ':address' => $address,
            ':district' => $district,
            ':user_id' => $userId,
        ]);
    }

    public function getUserById(int $userId): ?array {
        if ($userId <= 0) {
            return null;
        }

        $stmt = $this->pdo->prepare('SELECT user_id, f_name, l_name, email, profilePicture, address, district FROM User WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

}
