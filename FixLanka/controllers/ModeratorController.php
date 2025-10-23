<?php
// ModeratorController.php - Handles all moderator CRUD operations

require_once __DIR__ . '/../config/databse.php';

class ModeratorController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Get all moderators
    public function getAllModerators()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, assigned_section, created_at FROM Moderator ORDER BY created_at DESC");
            $stmt->execute();
            $moderators = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->jsonResponse(['success' => true, 'data' => $moderators]);
        } catch (PDOException $e) {
            error_log("Error fetching moderators: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to fetch moderators'], 500);
        }
    }

    // Add new moderator
    public function addModerator()
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($assigned_section)) {
            $this->jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
            return;
        }

        if (strlen($password) < 6) {
            $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        try {
            // Check if username exists
            $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $this->jsonResponse(['success' => false, 'message' => 'Username already exists'], 409);
                return;
            }

            // Check if email exists
            $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $this->jsonResponse(['success' => false, 'message' => 'Email already exists'], 409);
                return;
            }

            // Hash password and insert
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("INSERT INTO Moderator (username, email, password, assigned_section) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $assigned_section]);

            $moderator_id = $this->pdo->lastInsertId();

            // Fetch the newly created moderator
            $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, assigned_section, created_at FROM Moderator WHERE moderator_id = ?");
            $stmt->execute([$moderator_id]);
            $moderator = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->jsonResponse(['success' => true, 'message' => 'Moderator added successfully', 'data' => $moderator]);
        } catch (PDOException $e) {
            error_log("Error adding moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to add moderator'], 500);
        }
    }

    // Update moderator
    public function updateModerator()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $assigned_section = trim($_POST['assigned_section'] ?? '');

        // Validation
        if (!$moderator_id || empty($email) || empty($assigned_section)) {
            $this->jsonResponse(['success' => false, 'message' => 'Required fields are missing'], 400);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid email format'], 400);
            return;
        }

        try {
            // Check if email exists for other moderators
            $stmt = $this->pdo->prepare("SELECT moderator_id FROM Moderator WHERE email = ? AND moderator_id != ?");
            $stmt->execute([$email, $moderator_id]);
            if ($stmt->fetch()) {
                $this->jsonResponse(['success' => false, 'message' => 'Email already exists'], 409);
                return;
            }

            if (!empty($password)) {
                // Update with new password
                if (strlen($password) < 6) {
                    $this->jsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 400);
                    return;
                }

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare("UPDATE Moderator SET email = ?, password = ?, assigned_section = ? WHERE moderator_id = ?");
                $stmt->execute([$email, $hashedPassword, $assigned_section, $moderator_id]);
            } else {
                // Update without changing password
                $stmt = $this->pdo->prepare("UPDATE Moderator SET email = ?, assigned_section = ? WHERE moderator_id = ?");
                $stmt->execute([$email, $assigned_section, $moderator_id]);
            }

            // Fetch updated moderator
            $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, assigned_section, created_at FROM Moderator WHERE moderator_id = ?");
            $stmt->execute([$moderator_id]);
            $moderator = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->jsonResponse(['success' => true, 'message' => 'Moderator updated successfully', 'data' => $moderator]);
        } catch (PDOException $e) {
            error_log("Error updating moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to update moderator'], 500);
        }
    }

    // Delete moderator
    public function deleteModerator()
    {
        $moderator_id = (int)($_POST['moderator_id'] ?? 0);

        if (!$moderator_id) {
            $this->jsonResponse(['success' => false, 'message' => 'Moderator ID is required'], 400);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("DELETE FROM Moderator WHERE moderator_id = ?");
            $stmt->execute([$moderator_id]);

            if ($stmt->rowCount() > 0) {
                $this->jsonResponse(['success' => true, 'message' => 'Moderator deleted successfully']);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Moderator not found'], 404);
            }
        } catch (PDOException $e) {
            error_log("Error deleting moderator: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Failed to delete moderator'], 500);
        }
    }

    // Helper function to send JSON response
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
