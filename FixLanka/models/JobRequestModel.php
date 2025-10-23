<?php
class JobRequest {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * CREATE - Create new job request
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO JobRequest (user_id, category_id, title, description, district, address, service_provider_type, urgency, finish_date, photos) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['category_id'],
                $data['title'],
                $data['description'],
                $data['district'],
                $data['address'],
                $data['service_provider_type'],
                $data['urgency'],
                $data['finish_date'],
                $data['photos'] ?? null
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating job request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * READ - Get all job requests by user
     */
    public function getAllByUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT jr.*, c.name as category_name 
                FROM JobRequest jr
                LEFT JOIN Category c ON jr.category_id = c.category_id
                WHERE jr.user_id = ?
                ORDER BY jr.dateCreated DESC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting job requests: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * READ - Get single job request by ID
     */
    public function getById($requestId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT jr.*, c.name as category_name 
                FROM JobRequest jr
                LEFT JOIN Category c ON jr.category_id = c.category_id
                WHERE jr.request_id = ?
            ");
            $stmt->execute([$requestId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting job request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * UPDATE - Update job request
     */
    public function update($requestId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE JobRequest 
                SET category_id = ?, description = ?, location = ?, 
                    service_provider_type = ?, urgency = ?, photos = ?
                WHERE request_id = ? AND user_id = ?
            ");
            
            return $stmt->execute([
                $data['category_id'],
                $data['description'],
                $data['location'],
                $data['service_provider_type'],
                $data['urgency'],
                $data['photos'] ?? null,
                $requestId,
                $data['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Error updating job request: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * DELETE - Delete job request
     */
    public function delete($requestId, $userId) {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM JobRequest 
                WHERE request_id = ? AND user_id = ?
            ");
            return $stmt->execute([$requestId, $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting job request: " . $e->getMessage());
            return false;
        }
    }
}
?>