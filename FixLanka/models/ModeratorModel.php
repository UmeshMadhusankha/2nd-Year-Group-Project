<?php
// ModeratorModel.php - Handles all database operations for Moderator table

class ModeratorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all moderators from database
     * @return array Array of moderator records
     */
    public function getAllModerators()
    {
        $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, assigned_section, created_at FROM Moderator ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get moderator by ID
     * @param int $moderator_id
     * @return array|false Moderator record or false if not found
     */
    public function getModeratorById($moderator_id)
    {
        $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, assigned_section, created_at FROM Moderator WHERE moderator_id = ?");
        $stmt->execute([$moderator_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if username exists
     * @param string $username
     * @return bool True if exists, false otherwise
     */
    public function usernameExists($username)
    {
        $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if email exists
     * @param string $email
     * @param int|null $exclude_id Moderator ID to exclude from check (for updates)
     * @return bool True if exists, false otherwise
     */
    public function emailExists($email, $exclude_id = null)
    {
        if ($exclude_id) {
            $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE email = ? AND moderator_id != ?");
            $stmt->execute([$email, $exclude_id]);
        } else {
            $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->fetch() !== false;
    }

    /**
     * Create new moderator
     * @param string $username
     * @param string $email
     * @param string $hashedPassword
     * @param string $assigned_section
     * @return int Last insert ID
     */
    public function createModerator($username, $email, $hashedPassword, $assigned_section)
    {
        $stmt = $this->pdo->prepare("INSERT INTO Moderator (username, email, password, assigned_section) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $assigned_section]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Update moderator without changing password
     * @param int $moderator_id
     * @param string $email
     * @param string $assigned_section
     * @return bool Success status
     */
    public function updateModerator($moderator_id, $email, $assigned_section)
    {
        $stmt = $this->pdo->prepare("UPDATE Moderator SET email = ?, assigned_section = ? WHERE moderator_id = ?");
        return $stmt->execute([$email, $assigned_section, $moderator_id]);
    }

    /**
     * Update moderator including password
     * @param int $moderator_id
     * @param string $email
     * @param string $hashedPassword
     * @param string $assigned_section
     * @return bool Success status
     */
    public function updateModeratorWithPassword($moderator_id, $email, $hashedPassword, $assigned_section)
    {
        $stmt = $this->pdo->prepare("UPDATE Moderator SET email = ?, password = ?, assigned_section = ? WHERE moderator_id = ?");
        return $stmt->execute([$email, $hashedPassword, $assigned_section, $moderator_id]);
    }

    /**
     * Delete moderator
     * @param int $moderator_id
     * @return int Number of rows affected
     */
    public function deleteModerator($moderator_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM Moderator WHERE moderator_id = ?");
        $stmt->execute([$moderator_id]);
        return $stmt->rowCount();
    }
}
