<?php
class JobRequest {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // create model method
    public function create($data) {
        try {
            // 1. Insert Location
            $locStmt = $this->pdo->prepare("INSERT INTO location (address, district) VALUES (?, ?)");
            $locStmt->execute([$data['address'], $data['district']]);
            $locationId = $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare("
                INSERT INTO JobRequest (user_id, category_id, title, description, location_id, service_provider_type, urgency, finish_date, photos, job_area) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['category_id'],
                $data['title'],
                $data['description'],
                $locationId,
                $data['service_provider_type'],
                $data['urgency'],
                $data['finish_date'],
                $data['photos'] ?? null,
                $data['job_area']
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating job request: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getAllByUser($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT jr.*, c.name as category_name, l.address, l.district 
                FROM JobRequest jr
                LEFT JOIN Category c ON jr.category_id = c.category_id
                LEFT JOIN location l ON jr.location_id = l.location_id
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
    
    
    public function getById($requestId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT jr.*, c.name as category_name, l.address, l.district 
                FROM JobRequest jr
                LEFT JOIN Category c ON jr.category_id = c.category_id
                LEFT JOIN location l ON jr.location_id = l.location_id
                WHERE jr.request_id = ?
            ");
            $stmt->execute([$requestId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting job request: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function update($requestId, $data) {
        try {
            $updateFields = [];
            $params = [];
            
            if (isset($data['title'])) {
                $updateFields[] = "title = ?";
                $params[] = $data['title'];
            }
            
            if (isset($data['category_id'])) {
                $updateFields[] = "category_id = ?";
                $params[] = $data['category_id'];
            }
            
            if (isset($data['description'])) {
                $updateFields[] = "description = ?";
                $params[] = $data['description'];
            }
            
            // 1. Handle location updates separately
            if (isset($data['address']) || isset($data['district'])) {
                $locFields = [];
                $locValues = [];
                if (isset($data['address'])) {
                    $locFields[] = "address = ?";
                    $locValues[] = $data['address'];
                    unset($data['address']);
                }
                if (isset($data['district'])) {
                    $locFields[] = "district = ?";
                    $locValues[] = $data['district'];
                    unset($data['district']);
                }
                
                if (!empty($locFields)) {
                    // Get location_id for job request
                    $stmt = $this->pdo->prepare("SELECT location_id FROM JobRequest WHERE request_id = ?");
                    $stmt->execute([$requestId]);
                    $locId = $stmt->fetchColumn();
                    
                    if ($locId) {
                        $locSql = "UPDATE location SET " . implode(', ', $locFields) . " WHERE location_id = ?";
                        $locValues[] = $locId;
                        $locUpdateStmt = $this->pdo->prepare($locSql);
                        $locUpdateStmt->execute($locValues);
                    }
                }
            }
            
            if (isset($data['service_provider_type'])) {
                $updateFields[] = "service_provider_type = ?";
                $params[] = $data['service_provider_type'];
            }
            
            if (isset($data['urgency'])) {
                $updateFields[] = "urgency = ?";
                $params[] = $data['urgency'];
            }
            
            if (isset($data['finish_date'])) {
                $updateFields[] = "finish_date = ?";
                $params[] = $data['finish_date'];
            }
            
            // Only update photo if new one is uploaded
            if (isset($data['photos']) && $data['photos'] !== null) {
                $updateFields[] = "photos = ?";
                $params[] = $data['photos'];
            }
            
            // If no fields to update, return true
            if (empty($updateFields)) {
                return true; 
            }
            
            // Add WHERE clause parameters
            $params[] = $requestId;
            $params[] = $data['user_id'];
            
            $sql = "UPDATE JobRequest SET " . implode(', ', $updateFields) . " WHERE request_id = ? AND user_id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating job request: " . $e->getMessage());
            error_log("SQL: " . ($sql ?? 'N/A'));
            error_log("Data: " . print_r($data, true));
            return false;
        }
    }
    
    
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

    
    public function getAllOpen($filters = []) {
        try {
            $sql = "SELECT jr.*, c.name as category_name, CONCAT(u.f_name, ' ', u.l_name) as user_name, l.address, l.district 
                    FROM JobRequest jr
                    LEFT JOIN Category c ON jr.category_id = c.category_id
                    LEFT JOIN User u ON jr.user_id = u.user_id
                    LEFT JOIN location l ON jr.location_id = l.location_id
                    WHERE jr.status = 'Open'";
            
            $params = [];
            
            // Filter by district if provided
            if (!empty($filters['district'])) {
                $sql .= " AND l.district = ?";
                $params[] = $filters['district'];
            }
            
            // Filter by category if provided
            if (!empty($filters['category_id'])) {
                $sql .= " AND jr.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            $sql .= " ORDER BY jr.dateCreated DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting open job requests: " . $e->getMessage());
            return [];
        }
    }
}
?>