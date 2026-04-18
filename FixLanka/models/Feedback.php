<?php
require_once __DIR__ . '/../config/database.php';

class Feedback {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!$pdo) {
             require_once __DIR__ . '/../config/database.php';
             global $pdo; // Re-fetch global after require
        }
        $this->pdo = $pdo;
    }

    /**
     * Get all reviews for a specific company with optional filters
     */
    public function getCompanyReviews($companyId, $filters = []) {
        try {
            $query = "
                SELECT 
                    f.feedback_id,
                    f.rating,
                    f.comments,
                    f.date as review_date,
                    p.project_id,
                    p.title as project_title,
                    u.user_id,
                    u.f_name,
                    u.l_name,
                    u.profile_picture,
                    'project' as category -- Default category in this context
                FROM Feedback f
                JOIN Project p ON f.project_id = p.project_id
                JOIN User u ON f.given_by = u.user_id
                WHERE p.company_id = :company_id
            ";
            
            $params = [':company_id' => $companyId];

            // Filter by rating
            if (!empty($filters['rating']) && $filters['rating'] !== 'all') {
                $query .= " AND f.rating = :rating";
                $params[':rating'] = (int)$filters['rating'];
            }

            // Filter by date range (period)
            if (!empty($filters['period'])) {
                if ($filters['period'] === 'month') {
                    $query .= " AND f.date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                } elseif ($filters['period'] === 'quarter') {
                    $query .= " AND f.date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
                } elseif ($filters['period'] === 'year') {
                    $query .= " AND f.date >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                }
            }

            $query .= " ORDER BY f.date DESC";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching company reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get review statistics for a company
     */
    public function getCompanyReviewStats($companyId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(f.feedback_id) as total_reviews,
                    AVG(f.rating) as average_rating,
                    COUNT(CASE WHEN f.rating = 5 THEN 1 END) as star_5,
                    COUNT(CASE WHEN f.rating = 4 THEN 1 END) as star_4,
                    COUNT(CASE WHEN f.rating = 3 THEN 1 END) as star_3,
                    COUNT(CASE WHEN f.rating = 2 THEN 1 END) as star_2,
                    COUNT(CASE WHEN f.rating = 1 THEN 1 END) as star_1
                FROM Feedback f
                JOIN Project p ON f.project_id = p.project_id
                WHERE p.company_id = ?
            ");
            $stmt->execute([$companyId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching review stats: " . $e->getMessage());
            return [
                'total_reviews' => 0,
                'average_rating' => 0,
                'star_5' => 0, 'star_4' => 0, 'star_3' => 0, 'star_2' => 0, 'star_1' => 0
            ];
        }
    }
}
