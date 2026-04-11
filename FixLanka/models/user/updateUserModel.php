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

        $fullName = trim((string)($data['full_name'] ?? ''));
        $email = trim((string)($data['email'] ?? ''));
        $location = trim((string)($data['location'] ?? ''));

        if ($fullName === '' || $email === '' || $location === '') {
            return false;
        }

        [$firstName, $lastName] = $this->splitName($fullName);
        [$address, $district] = $this->splitLocation($location);

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

        $stmt = $this->pdo->prepare('SELECT user_id, f_name, l_name, email, profile_picture, address, district FROM user WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    private function splitName(string $fullName): array {
        $parts = preg_split('/\s+/', trim($fullName));
        $parts = array_values(array_filter($parts, static fn($part) => $part !== ''));

        if (count($parts) === 0) {
            return ['User', '-'];
        }

        if (count($parts) === 1) {
            return [$parts[0], '-'];
        }

        $firstName = array_shift($parts);
        $lastName = implode(' ', $parts);

        return [$firstName, $lastName !== '' ? $lastName : '-'];
    }

    private function splitLocation(string $location): array {
        $segments = array_map('trim', explode(',', $location));
        $segments = array_values(array_filter($segments, static fn($segment) => $segment !== ''));

        if (count($segments) === 0) {
            return ['', ''];
        }

        if (count($segments) === 1) {
            return [$segments[0], $segments[0]];
        }

        $district = array_pop($segments);
        $address = implode(', ', $segments);

        if ($address === '') {
            $address = $district;
        }

        return [$address, $district];
    }
}
