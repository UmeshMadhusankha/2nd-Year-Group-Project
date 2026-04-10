<?php
// Repairer model (must be UTF-8 encoded)
class Repairer {
    private $pdo;
    private ?bool $hasSkillsColumn = null;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get featured repairers (top-rated, available)
     */
    public function getFeatured($limit = 10, $offset = 0) {
        try {
            $skillsSelect = $this->hasSkills() ? 'r.skills,' : "'' AS skills,";
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
                    $skillsSelect
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
            $skillsSelect = $this->hasSkills() ? 'r.skills,' : "'' AS skills,";
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
                    $skillsSelect
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

            if (!empty($filters['q'])) {
                $q = (string)$filters['q'];
                $like = '%' . $q . '%';
                if ($this->hasSkills()) {
                    $sql .= " AND (CONCAT(r.f_name, ' ', r.l_name) LIKE ? OR r.about LIKE ? OR r.skills LIKE ?)";
                    array_push($params, $like, $like, $like);
                } else {
                    $sql .= " AND (CONCAT(r.f_name, ' ', r.l_name) LIKE ? OR r.about LIKE ?)";
                    array_push($params, $like, $like);
                }
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
            $skillsSelect = $this->hasSkills() ? 'r.skills,' : "'' AS skills,";
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
                    $skillsSelect
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
     * Get finished job outcomes for a repairer from the core `job` table.
     * Success rate is computed from finished outcomes only: completed vs cancelled.
     */
    public function getPlatformJobOutcomeStats(int $repairerId): array {
        try {
            $stmt = $this->pdo->prepare('
                SELECT
                    SUM(CASE WHEN j.status = \'completed\' THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN j.status = \'cancelled\' THEN 1 ELSE 0 END) AS cancelled_count
                FROM job j
                WHERE j.fixer_id = ?
            ');
            $stmt->execute([$repairerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $completed = (int)($row['completed_count'] ?? 0);
            $cancelled = (int)($row['cancelled_count'] ?? 0);

            return [
                'completed' => $completed,
                'cancelled' => $cancelled,
                'total_finished' => $completed + $cancelled,
            ];
        } catch (PDOException $e) {
            error_log('Error getting platform job outcome stats: ' . $e->getMessage());
            return ['completed' => 0, 'cancelled' => 0, 'total_finished' => 0];
        }
    }

    /**
     * Get finished outcomes for a repairer from workforce `freelancer_assignments`.
     * By default this aggregates across all companies; pass $companyId to scope.
     */
    public function getFreelancerAssignmentOutcomeStats(int $repairerId, ?int $companyId = null): array {
        try {
            $sql = '
                SELECT
                    SUM(CASE WHEN fa.status = \'completed\' THEN 1 ELSE 0 END) AS completed_count,
                    SUM(CASE WHEN fa.status = \'cancelled\' THEN 1 ELSE 0 END) AS cancelled_count
                FROM freelancer_assignments fa
                WHERE fa.repairer_id = ?
            ';
            $params = [$repairerId];
            if ($companyId !== null) {
                $sql .= ' AND fa.company_id = ?';
                $params[] = $companyId;
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

            $completed = (int)($row['completed_count'] ?? 0);
            $cancelled = (int)($row['cancelled_count'] ?? 0);

            return [
                'completed' => $completed,
                'cancelled' => $cancelled,
                'total_finished' => $completed + $cancelled,
            ];
        } catch (PDOException $e) {
            error_log('Error getting freelancer assignment outcome stats: ' . $e->getMessage());
            return ['completed' => 0, 'cancelled' => 0, 'total_finished' => 0];
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
            // If DB hasn't been migrated yet, ensure the skills column exists
            // so profile saves don't silently drop the field.
            if (is_array($data) && array_key_exists('skills', $data)) {
                $this->ensureSkillsColumn();
            }
            $lName = isset($nameParts[1]) ? $nameParts[1] : '';

            $fields = [
                'f_name = ?',
                'l_name = ?',
                'email = ?',
                'phoneNumber = ?',
                'category_id = ?',
                'districts = ?',
                'availability = ?',
                'about = ?'
            ];

            $values = [
                $fName,
                $lName,
                $data['email'],
                $data['phone'],
                $data['category_id'] ?: null,
                $data['districts'],
                $data['availability'],
                $data['about'] ?? null,
            ];

            if ($this->hasSkills()) {
                $fields[] = 'skills = ?';
                $values[] = isset($data['skills']) ? (string)$data['skills'] : '';
            }

            $values[] = $repairerId;

            $stmt = $this->pdo->prepare(
                'UPDATE repairer SET ' . implode(', ', $fields) . ' WHERE repairer_id = ?'
            );

            return $stmt->execute($values);
        } catch (PDOException $e) {
            error_log("Error updating repairer profile: " . $e->getMessage());
            return false;
        }
    }

    private function ensureSkillsColumn(): void {
        if ($this->hasSkills()) {
            return;
        }

        try {
            $this->pdo->exec('ALTER TABLE repairer ADD COLUMN skills TEXT NULL');
            $this->hasSkillsColumn = true;
        } catch (Throwable $e) {
            // If we can't alter schema (no privileges), keep behavior tolerant.
            $this->hasSkillsColumn = false;
        }
    }

    private function hasSkills(): bool {
        if ($this->hasSkillsColumn !== null) {
            return $this->hasSkillsColumn;
        }

        try {
            $stmt = $this->pdo->prepare('
                SELECT 1
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = "repairer"
                  AND COLUMN_NAME = "skills"
                LIMIT 1
            ');
            $stmt->execute();
            $this->hasSkillsColumn = (bool)$stmt->fetchColumn();
        } catch (Throwable $e) {
            $this->hasSkillsColumn = false;
        }

        return $this->hasSkillsColumn;
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