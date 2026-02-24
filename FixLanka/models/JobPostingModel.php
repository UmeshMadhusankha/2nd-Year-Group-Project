<?php
/**
 * Job Posting Model
 * Handles all database operations for Company Job Postings
 */

class JobPostingModel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Create a new job posting
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO job_postings (
                company_id, title, category, employment_type,
                description, min_experience, min_budget, max_budget,
                location,
                status
            ) VALUES (
                :company_id, :title, :category, :employment_type,
                :description, :min_experience, :min_budget, :max_budget,
                :location,
                :status
            )";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':company_id' => $data['company_id'],
                ':title' => $data['title'],
                ':category' => $data['category'],
                ':employment_type' => $data['employment_type'],
                ':description' => $data['description'],
                ':min_experience' => $data['min_experience'],
                ':min_budget' => $data['min_budget'],
                ':max_budget' => $data['max_budget'],
                ':location' => $data['location'],
                ':status' => $data['status'] ?? 'draft'
            ]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("JobPostingModel::create error: " . $e->getMessage());
            throw new Exception("Failed to create job posting");
        }
    }
    
    /**
     * Get all job postings for a company
     */
    public function getByCompany($companyId, $status = null) {
        try {
            $sql = "SELECT * FROM job_postings WHERE company_id = :company_id";
            $params = [':company_id' => $companyId];
            
            if ($status) {
                $sql .= " AND status = :status";
                $params[':status'] = $status;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("JobPostingModel::getByCompany error: " . $e->getMessage());
            throw new Exception("Failed to fetch job postings");
        }
    }
    
    /**
     * Get a single job posting by ID
     */
    public function getById($postingId) {
        try {
            $sql = "SELECT * FROM job_postings WHERE posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':posting_id' => $postingId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("JobPostingModel::getById error: " . $e->getMessage());
            throw new Exception("Failed to fetch job posting");
        }
    }
    
    /**
     * Update a job posting
     */
    public function update($postingId, $data) {
        try {
            $sql = "UPDATE job_postings SET 
                title = :title,
                category = :category,
                employment_type = :employment_type,
                description = :description,
                min_experience = :min_experience,
                priority_level = :priority_level,
                min_budget = :min_budget,
                max_budget = :max_budget,
                application_deadline = :application_deadline,
                required_skills = :required_skills,
                location = :location
                WHERE posting_id = :posting_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':posting_id' => $postingId,
                ':title' => $data['title'],
                ':category' => $data['category'],
                ':employment_type' => $data['employment_type'],
                ':description' => $data['description'],
                ':min_experience' => $data['min_experience'],
                ':priority_level' => $data['priority_level'] ?? 'medium',
                ':min_budget' => $data['min_budget'],
                ':max_budget' => $data['max_budget'],
                ':application_deadline' => $data['application_deadline'] ?? null,
                ':required_skills' => $data['required_skills'] ?? null,
                ':location' => $data['location']
            ]);
        } catch (PDOException $e) {
            error_log("JobPostingModel::update error: " . $e->getMessage());
            throw new Exception("Failed to update job posting");
        }
    }
    
    /**
     * Update job posting status
     */
    public function updateStatus($postingId, $status) {
        try {
            $sql = "UPDATE job_postings SET status = :status";
            
            // If closing the job, you could potentially set closed values if they existed in schema
            // if ($status === 'closed' || $status === 'filled') {
            //     $sql .= ", closed_date = NOW()";
            // }
            
            $sql .= " WHERE posting_id = :posting_id";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':posting_id' => $postingId,
                ':status' => $status
            ]);
        } catch (PDOException $e) {
            error_log("JobPostingModel::updateStatus error: " . $e->getMessage());
            throw new Exception("Failed to update job status");
        }
    }
    
    /**
     * Delete a job posting
     */
    public function delete($postingId) {
        try {
            $sql = "DELETE FROM job_postings WHERE posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':posting_id' => $postingId]);
        } catch (PDOException $e) {
            error_log("JobPostingModel::delete error: " . $e->getMessage());
            throw new Exception("Failed to delete job posting");
        }
    }
    
    /**
     * Get job posting statistics for a company
     */
    public function getStatistics($companyId) {
        try {
            $sql = "SELECT 
                COUNT(*) as total_postings,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_postings,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft_postings,
                SUM(CASE WHEN status = 'filled' THEN 1 ELSE 0 END) as filled_postings,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_postings
                FROM job_postings 
                WHERE company_id = :company_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':company_id' => $companyId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("JobPostingModel::getStatistics error: " . $e->getMessage());
            throw new Exception("Failed to fetch statistics");
        }
    }
    
    /**
     * Get application count for a job posting
     */
    public function getApplicationCount($postingId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM repairer_applications WHERE job_posting_id = :posting_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':posting_id' => $postingId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("JobPostingModel::getApplicationCount error: " . $e->getMessage());
            return 0;
        }
    }
}
