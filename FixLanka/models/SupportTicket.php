<?php
class SupportTicket {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!$pdo) {
             require_once __DIR__ . '/../config/database.php';
             global $pdo;
        }
        $this->pdo = $pdo;
    }

    // Create a new ticket
    public function create($data) {
        $sql = "INSERT INTO SupportTicket (user_id, user_type, title, description, category, priority, urgency, project_id, attachment) 
                VALUES (:user_id, :user_type, :title, :description, :category, :priority, :urgency, :project_id, :attachment)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':user_type' => $data['user_type'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':priority' => $data['priority'],
            ':urgency' => $data['urgency'],
            ':project_id' => !empty($data['project_id']) ? $data['project_id'] : null,
            ':attachment' => !empty($data['attachment']) ? $data['attachment'] : null
        ]);
        
        return $this->pdo->lastInsertId();
    }

    // Get all tickets for a specific user/company
    public function getAllByUser($userId, $userType, $filters = []) {
        $sql = "SELECT * FROM SupportTicket WHERE user_id = :user_id AND user_type = :user_type";
        $params = [
            ':user_id' => $userId,
            ':user_type' => $userType
        ];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $sql .= " AND priority = :priority";
            $params[':priority'] = $filters['priority'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get ticket by ID
    public function getById($ticketId, $userId, $userType) {
        $sql = "SELECT * FROM SupportTicket WHERE ticket_id = :ticket_id AND user_id = :user_id AND user_type = :user_type";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':user_id' => $userId,
            ':user_type' => $userType
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get ticket statistics
    public function getStats($userId, $userType) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed
                FROM SupportTicket 
                WHERE user_id = :user_id AND user_type = :user_type";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':user_type' => $userType
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
