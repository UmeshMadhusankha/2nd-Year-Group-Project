<?php
// Repairer model (must be UTF-8 encoded)
class Repairer {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get featured repairers (top-rated, available)
     */
    public function getFeatured($limit = 10, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.repairer_id,
                    r.f_name,
                    r.l_name,
                    CONCAT(r.f_name, ' ', r.l_name) as full_name,
                    r.email,
                    r.phoneNumber,
                    r.about,
                    r.profilePicture,
                    r.ratings,
                    r.completedJobsCount,
                    r.districts,
                    r.availability,
                    r.dateJoined,
                    c.name as category_name,
                    'individual' as provider_type
                FROM repairer r
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE r.availability = 'available'
                ORDER BY r.ratings DESC, r.completedJobsCount DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting featured repairers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all repairers with optional filters
     */
    public function getAll($filters = [], $limit = 20, $offset = 0) {
        try {
            $sql = "
                SELECT 
                    r.repairer_id,
                    r.f_name,
                    r.l_name,
                    CONCAT(r.f_name, ' ', r.l_name) as full_name,
                    r.email,
                    r.phoneNumber,
                    r.about,
                    r.profilePicture,
                    r.ratings,
                    r.completedJobsCount,
                    r.districts,
                    r.availability,
                    r.dateJoined,
                    c.name as category_name,
                    'individual' as provider_type
                FROM repairer r
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Apply filters
            if (!empty($filters['category_id'])) {
                $sql .= " AND r.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['min_rating'])) {
                $sql .= " AND r.ratings >= ?";
                $params[] = $filters['min_rating'];
            }
            
            if (!empty($filters['availability'])) {
                $sql .= " AND r.availability = ?";
                $params[] = $filters['availability'];
            }
            
            if (!empty($filters['service_area'])) {
                $sql .= " AND r.districts LIKE ?";
                $params[] = '%' . $filters['service_area'] . '%';
            }
            
            $sql .= " ORDER BY r.ratings DESC, r.completedJobsCount DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all repairers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single repairer by ID
     */
    public function getById($repairerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    r.repairer_id,
                    r.f_name,
                    r.l_name,
                    CONCAT(r.f_name, ' ', r.l_name) as full_name,
                    r.email,
                    r.phoneNumber,
                    r.about,
                    r.profilePicture,
                    r.ratings,
                    r.completedJobsCount,
                    r.districts,
                    r.availability,
                    r.dateJoined,
                    r.category_id,
                    c.name as category_name,
                    'individual' as provider_type
                FROM repairer r
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE r.repairer_id = ?
            ");
            
            $stmt->execute([$repairerId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting repairer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get total count of repairers
     */
    public function getCount($filters = []) {
        try {
            $sql = "SELECT COUNT(*) as total FROM repairer r WHERE 1=1";
            $params = [];
            
            if (!empty($filters['category_id'])) {
                $sql .= " AND r.category_id = ?";
                $params[] = $filters['category_id'];
            }
            
            if (!empty($filters['min_rating'])) {
                $sql .= " AND r.ratings >= ?";
                $params[] = $filters['min_rating'];
            }
            
            if (!empty($filters['availability'])) {
                $sql .= " AND r.availability = ?";
                $params[] = $filters['availability'];
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error getting repairer count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get review summary (average rating + total count) for a repairer.
     */
    public function getReviewSummary($repairerId) {
        try {
            $stmt = $this->pdo->prepare('
                SELECT
                    COALESCE(AVG(r.rating), 0) AS avg_rating,
                    COUNT(*) AS review_count
                FROM review r
                WHERE r.service_provider_id = ?
            ');
            $stmt->execute([$repairerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'average' => isset($row['avg_rating']) ? (float)$row['avg_rating'] : 0.0,
                'count' => isset($row['review_count']) ? (int)$row['review_count'] : 0,
            ];
        } catch (PDOException $e) {
            error_log('Error getting review summary: ' . $e->getMessage());
            return ['average' => 0.0, 'count' => 0];
        }
    }

    /**
     * Update repairer profile details.
     */
    public function updateProfile($repairerId, $data) {
        try {
            // Split full_name into f_name and l_name
            $nameParts = explode(' ', trim($data['full_name']), 2);
            $fName = $nameParts[0];
            $lName = isset($nameParts[1]) ? $nameParts[1] : '';

            $stmt = $this->pdo->prepare("
                UPDATE repairer SET
                    f_name = ?,
                    l_name = ?,
                    email = ?,
                    phoneNumber = ?,
                    category_id = ?,
                    districts = ?,
                    availability = ?,
                    about = ?
                WHERE repairer_id = ?
            ");

            return $stmt->execute([
                $fName,
                $lName,
                $data['email'],
                $data['phone'],
                $data['category_id'] ?: null,
                $data['districts'],
                $data['availability'],
                $data['about'] ?? null,
                $repairerId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating repairer profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update repairer password.
     */
    public function updatePassword($repairerId, $newPasswordHash) {
        try {
            $stmt = $this->pdo->prepare("UPDATE repairer SET password = ? WHERE repairer_id = ?");
            return $stmt->execute([$newPasswordHash, $repairerId]);
        } catch (PDOException $e) {
            error_log("Error updating repairer password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get repairer's current password hash for verification.
     */
    public function getPasswordHash($repairerId) {
        try {
            $stmt = $this->pdo->prepare("SELECT password FROM repairer WHERE repairer_id = ?");
            $stmt->execute([$repairerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['password'] : null;
        } catch (PDOException $e) {
            error_log("Error getting repairer password: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update repairer profile picture path.
     */
    public function updateProfilePicture($repairerId, $picturePath) {
        try {
            $stmt = $this->pdo->prepare("UPDATE repairer SET profilePicture = ? WHERE repairer_id = ?");
            return $stmt->execute([$picturePath, $repairerId]);
        } catch (PDOException $e) {
            error_log("Error updating profile picture: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get most recent reviews for a repairer with reviewer name.
     */
    public function getRecentReviews($repairerId, $limit = 3) {
        try {
            $limit = (int)$limit;
            if ($limit <= 0) {
                $limit = 3;
            }

            $stmt = $this->pdo->prepare('
                SELECT
                    CONCAT(u.f_name, " ", u.l_name) AS author,
                    r.rating AS rating,
                    r.comments AS text,
                    r.date AS date
                FROM review r
                INNER JOIN job j ON j.job_id = r.job_id
                INNER JOIN jobrequest jr ON jr.request_id = j.job_request_id
                INNER JOIN user u ON u.user_id = jr.user_id
                WHERE r.service_provider_id = ?
                ORDER BY r.date DESC
                LIMIT ?
            ');

            $stmt->bindValue(1, (int)$repairerId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting recent reviews: ' . $e->getMessage());
            return [];
        }
    }
}