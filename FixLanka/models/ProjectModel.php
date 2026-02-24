<?php
/**
 * Project Model
 * 
 * Handles all CRUD operations for company projects.
 * Manages project lifecycle from planning to completion.
 * 
 * @package FixLanka\Models
 * @version 1.0.0
 */

class Project
{
    /**
     * PDO database connection instance
     * @var PDO
     */
    private $pdo;

    /**
     * Status constants for projects
     */
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';

    /**
     * Constructor - Initialize model with database connection
     * 
     * @param PDO $pdo Database connection instance
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new project
     * 
     * @param array $data Project data (company_id, customer_id, title, description, etc.)
     * @return array Success/error response with project_id
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO Project (
                        company_id, customer_id, title, description, 
                        project_type, location, budget, start_date, 
                        end_date, attachment, status, progress
                    ) VALUES (
                        :company_id, :customer_id, :title, :description,
                        :project_type, :location, :budget, :start_date,
                        :end_date, :attachment, :status, :progress
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':company_id' => $data['company_id'],
                ':customer_id' => $data['customer_id'],
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':project_type' => $data['project_type'] ?? null,
                ':location' => $data['location'],
                ':budget' => $data['budget'] ?? null,
                ':start_date' => $data['start_date'] ?? null,
                ':end_date' => $data['end_date'] ?? null,
                ':attachment' => $data['attachment'] ?? null,
                ':status' => $data['status'] ?? self::STATUS_PLANNED,
                ':progress' => $data['progress'] ?? 0
            ]);

            return [
                'success' => true,
                'message' => 'Project created successfully',
                'project_id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all projects with optional filtering
     * 
     * @param array $filters Optional filters (company_id, status, customer_id, etc.)
     * @return array List of projects with customer and company details
     */
    public function getAll($filters = [])
    {
        try {
            $sql = "SELECT 
                        p.*,
                        u.f_name as customer_first_name,
                        u.l_name as customer_last_name,
                        u.email as customer_email,
                        u.address as customer_address,
                        c.name as company_name
                    FROM Project p
                    LEFT JOIN User u ON p.customer_id = u.user_id
                    LEFT JOIN Company c ON p.company_id = c.company_id
                    WHERE 1=1";

            $params = [];

            // Apply filters
            if (!empty($filters['company_id'])) {
                $sql .= " AND p.company_id = :company_id";
                $params[':company_id'] = $filters['company_id'];
            }

            if (!empty($filters['customer_id'])) {
                $sql .= " AND p.customer_id = :customer_id";
                $params[':customer_id'] = $filters['customer_id'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['project_type'])) {
                $sql .= " AND p.project_type = :project_type";
                $params[':project_type'] = $filters['project_type'];
            }

            // Add search capability
            if (!empty($filters['search'])) {
                $sql .= " AND (p.title LIKE :search OR p.description LIKE :search OR p.location LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }

            $sql .= " ORDER BY p.project_id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $projects,
                'count' => count($projects)
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch projects: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get a single project by ID
     * 
     * @param int $projectId Project ID
     * @return array Project details with related information
     */
    public function getById($projectId)
    {
        try {
            $sql = "SELECT 
                        p.*,
                        u.f_name as customer_first_name,
                        u.l_name as customer_last_name,
                        u.email as customer_email,
                        u.phoneNumber as customer_phone,
                        u.address as customer_address,
                        c.name as company_name,
                        c.email as company_email,
                        c.contact_no as company_contact
                    FROM Project p
                    LEFT JOIN User u ON p.customer_id = u.user_id
                    LEFT JOIN Company c ON p.company_id = c.company_id
                    WHERE p.project_id = :project_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':project_id' => $projectId]);

            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($project) {
                return [
                    'success' => true,
                    'data' => $project
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Project not found'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch project: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update an existing project
     * 
     * @param int $projectId Project ID
     * @param array $data Updated project data
     * @return array Success/error response
     */
    public function update($projectId, $data)
    {
        try {
            $sql = "UPDATE Project SET 
                        title = :title,
                        description = :description,
                        project_type = :project_type,
                        location = :location,
                        budget = :budget,
                        final_cost = :final_cost,
                        start_date = :start_date,
                        end_date = :end_date,
                        attachment = :attachment,
                        status = :status,
                        progress = :progress
                    WHERE project_id = :project_id";

            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':project_id' => $projectId,
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':project_type' => $data['project_type'] ?? null,
                ':location' => $data['location'],
                ':budget' => $data['budget'] ?? null,
                ':final_cost' => $data['final_cost'] ?? null,
                ':start_date' => $data['start_date'] ?? null,
                ':end_date' => $data['end_date'] ?? null,
                ':attachment' => $data['attachment'] ?? null,
                ':status' => $data['status'],
                ':progress' => $data['progress']
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Project updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update project'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update project progress percentage
     * 
     * @param int $projectId Project ID
     * @param int $progress Progress percentage (0-100)
     * @return array Success/error response
     */
    public function updateProgress($projectId, $progress)
    {
        try {
            // Ensure progress is within 0-100 range
            $progress = max(0, min(100, intval($progress)));

            $sql = "UPDATE Project SET progress = :progress WHERE project_id = :project_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':project_id' => $projectId,
                ':progress' => $progress
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Progress updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update progress'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to update progress: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update project status
     * 
     * @param int $projectId Project ID
     * @param string $status New status
     * @return array Success/error response
     */
    public function updateStatus($projectId, $status)
    {
        try {
            $validStatuses = [
                self::STATUS_PLANNED,
                self::STATUS_IN_PROGRESS,
                self::STATUS_COMPLETED,
                self::STATUS_CANCELLED,
                self::STATUS_ON_HOLD
            ];

            if (!in_array($status, $validStatuses)) {
                return [
                    'success' => false,
                    'message' => 'Invalid status value'
                ];
            }

            $sql = "UPDATE Project SET status = :status WHERE project_id = :project_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                ':project_id' => $projectId,
                ':status' => $status
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Status updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update status'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a project
     * 
     * @param int $projectId Project ID
     * @return array Success/error response
     */
    public function delete($projectId)
    {
        try {
            $sql = "DELETE FROM Project WHERE project_id = :project_id";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([':project_id' => $projectId]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Project deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete project'
                ];
            }
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get project statistics for a company
     * 
     * @param int $companyId Company ID
     * @return array Statistics including counts by status, average progress, etc.
     */
    public function getStatistics($companyId)
    {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_projects,
                        SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                        SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold,
                        AVG(progress) as average_progress,
                        SUM(budget) as total_budget,
                        SUM(final_cost) as total_final_cost
                    FROM Project
                    WHERE company_id = :company_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':company_id' => $companyId]);

            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ];
        }
    }
}
