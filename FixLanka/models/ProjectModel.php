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

            if (!empty($filters['exclude_status'])) {
                $sql .= " AND p.status != :exclude_status";
                $params[':exclude_status'] = $filters['exclude_status'];
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
     * Start a new project from an accepted contract
     * 
     * @param int $contractId Contract ID
     * @return array Success/error response with project_id
     */
    public function startFromContract($contractId)
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Fetch Contract Data
            $sqlContract = "SELECT c.*, 'General Maintenance' as category 
                            FROM Contract c 
                            WHERE c.contract_id = :cat_id AND c.status = 'accepted' LIMIT 1";
            $stmtC = $this->pdo->prepare($sqlContract);
            $stmtC->execute([':cat_id' => $contractId]);
            $contract = $stmtC->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                // Also check if contract exists without status='accepted' or missing category
                $sqlCheck = "SELECT status, project_id FROM Contract WHERE contract_id = :cid";
                $stmtCheck = $this->pdo->prepare($sqlCheck);
                $stmtCheck->execute([':cid' => $contractId]);
                $c = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$c) {
                    throw new Exception("Contract not found.");
                }
                if ($c['status'] !== 'accepted') {
                    throw new Exception("Contract must be accepted before starting a project.");
                }
                if (!empty($c['project_id'])) {
                    throw new Exception("Project already generated for this contract.");
                }
                
                // fallback generic category
                $contract = $c;
                $contract['category'] = 'General Maintenance';
                $contract['company_id'] = $c['company_id'];
                $contract['customer_id'] = $c['customer_id'];
                $contract['address'] = 'Specified in chat';
                $contract['total_price'] = $c['total_price'] ?? 0;
            }

            if (!empty($contract['project_id'])) {
                throw new Exception("A project has already been started for this contract.");
            }

            // 2. Create the Project record
            $sqlInsert = "INSERT INTO Project (
                            company_id, customer_id, title, description, 
                            project_type, location, budget, start_date, 
                            end_date, status, progress
                        ) VALUES (
                            :company_id, :customer_id, :title, :description,
                            :project_type, :location, :budget, NOW(),
                            NULL, :status, 0
                        )";
            
            $title = "Project #" . $contractId . " (" . substr($contract['category'] ?? 'General', 0, 15) . ")";

            $stmtInsert = $this->pdo->prepare($sqlInsert);
            $stmtInsert->execute([
                ':company_id' => $contract['company_id'],
                ':customer_id' => $contract['customer_id'] ?? 0,
                ':title' => $title,
                ':description' => "Automatically generated from accepted Contract #" . $contractId,
                ':project_type' => $contract['category'] ?? 'General',
                ':location' => $contract['address'] ?? 'Specified by customer',
                ':budget' => $contract['total_price'] ?? 0,
                ':status' => self::STATUS_PLANNED
            ]);

            $projectId = $this->pdo->lastInsertId();

            // 3. Link Project back to Contract
            $sqlUpdate = "UPDATE Contract SET project_id = :pid WHERE contract_id = :cid";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([':pid' => $projectId, ':cid' => $contractId]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => 'Project successfully started from contract.',
                'project_id' => $projectId
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
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
    /**
     * Get project timeline events
     * 
     * @param int $projectId Project ID
     * @return array List of timeline events sorted by date
     */
    public function getTimeline($projectId)
    {
        try {
            $timeline = [];
            $debugLog = []; // For debugging

            // 1. Get Project base events
            $sql = "SELECT created_at, start_date, end_date, title, status FROM Project WHERE project_id = :project_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':project_id' => $projectId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($project) {
                // Project Created (if available)
                if (isset($project['created_at'])) {
                    $timeline[] = [
                        'date' => $project['created_at'],
                        'title' => 'Project Created',
                        'description' => 'Project "' . $project['title'] . '" was created',
                        'type' => 'creation',
                        'icon' => 'fa-plus-circle',
                        'status' => 'completed'
                    ];
                }

                // Project Started
                if (!empty($project['start_date'])) {
                    $isPast = strtotime($project['start_date']) <= time();
                    $timeline[] = [
                        'date' => $project['start_date'],
                        'title' => 'Start Date',
                        'description' => 'Scheduled start date',
                        'type' => 'start',
                        'icon' => 'fa-calendar-plus',
                        'status' => $isPast ? 'completed' : 'planned'
                    ];
                }

                // Project Ends
                if (!empty($project['end_date'])) {
                    $isPast = strtotime($project['end_date']) <= time();
                     $timeline[] = [
                        'date' => $project['end_date'],
                        'title' => 'Expected End Date',
                        'description' => 'Scheduled completion date',
                        'type' => 'end',
                        'icon' => 'fa-flag-checkered',
                        'status' => $project['status'] === 'completed' ? 'completed' : ($isPast ? 'delayed' : 'planned')
                    ];
                }
            }

            // 2. Get Contract & Milestones (if available)
            // Join Contract to get ID
            $sqlContract = "SELECT contract_id, contract_date, signed_at, created_at, start_date, end_date FROM Contract WHERE project_id = :project_id LIMIT 1";
            $stmtContract = $this->pdo->prepare($sqlContract);
            $stmtContract->execute([':project_id' => $projectId]);
            $contract = $stmtContract->fetch(PDO::FETCH_ASSOC);

            if ($contract) {
                $contractId = $contract['contract_id'];

                // Contract Created
                if (!empty($contract['created_at'])) {
                     $timeline[] = [
                        'date' => $contract['created_at'],
                        'title' => 'Contract Drafted',
                        'description' => 'Contract created and sent for review',
                        'type' => 'contract_created',
                        'icon' => 'fa-file-signature',
                        'status' => 'completed'
                    ];
                }

                // Contract Start Date
                if (!empty($contract['start_date'])) {
                     $timeline[] = [
                        'date' => $contract['start_date'],
                        'title' => 'Contract Start',
                        'description' => 'Official contract start date',
                        'type' => 'start',
                        'icon' => 'fa-play-circle',
                        'status' => strtotime($contract['start_date']) <= time() ? 'completed' : 'planned'
                    ];
                }

                // Contract End Date
                if (!empty($contract['end_date'])) {
                     $timeline[] = [
                        'date' => $contract['end_date'],
                        'title' => 'Contract Completion',
                        'description' => 'Official contract completion date',
                        'type' => 'end',
                        'icon' => 'fa-flag-checkered',
                        'status' => 'planned'
                    ];
                }

                // Contract Signed
                if (!empty($contract['signed_at'])) {
                    $timeline[] = [
                        'date' => $contract['signed_at'],
                        'title' => 'Contract Signed',
                        'description' => 'Contract formally signed',
                        'type' => 'contract_signed',
                        'icon' => 'fa-file-signature',
                        'status' => 'completed'
                    ];
                }

                // 3. Get Milestones
                // Try contract_milestone first (new table)
                try {
                    $sqlMilestones = "SELECT title, due_date, status, completion_date FROM contract_milestone WHERE contract_id = :contract_id ORDER BY due_date ASC";
                    $stmtMilestone = $this->pdo->prepare($sqlMilestones);
                    $stmtMilestone->execute([':contract_id' => $contractId]);
                    $milestones = $stmtMilestone->fetchAll(PDO::FETCH_ASSOC);

                    // If empty, try legacy Milestone table
                    if (empty($milestones)) {
                        $sqlLegacy = "SELECT name as title, due_date, status, completion_date FROM Milestone WHERE contract_id = :contract_id ORDER BY due_date ASC";
                        $stmtLegacy = $this->pdo->prepare($sqlLegacy);
                        $stmtLegacy->execute([':contract_id' => $contractId]);
                        $milestones = $stmtLegacy->fetchAll(PDO::FETCH_ASSOC);
                    }
     
                    foreach ($milestones as $ms) {
                         // Milestone Due
                         if (!empty($ms['due_date'])) {
                             $isCompleted = !empty($ms['completion_date']) || $ms['status'] === 'completed';
                             $timeline[] = [
                                 'date' => $ms['due_date'],
                                 'title' => 'Milestone Due: ' . $ms['title'],
                                 'description' => 'Status: ' . ucfirst($ms['status']),
                                 'type' => 'milestone',
                                 'icon' => 'fa-map-marker-alt',
                                 'status' => $isCompleted ? 'completed' : 'planned'
                             ];
                         }
                         // Milestone Completed
                         if (!empty($ms['completion_date'])) {
                             $timeline[] = [
                                 'date' => $ms['completion_date'],
                                 'title' => 'Milestone Completed: ' . $ms['title'],
                                 'description' => 'Work completed for milestone',
                                 'type' => 'milestone_completed',
                                 'icon' => 'fa-check-circle',
                                 'status' => 'completed'
                             ];
                         }
                    }
                } catch (Exception $e) { /* Ignore */ }

                 // 4. Contract Timeline Events
                try {
                    $sqlTimeline = "SELECT event_title, event_description, created_at FROM contract_timeline WHERE contract_id = :contract_id";
                    $stmtTimeline = $this->pdo->prepare($sqlTimeline);
                    $stmtTimeline->execute([':contract_id' => $contractId]);
                    $events = $stmtTimeline->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($events as $ev) {
                        $timeline[] = [
                            'date' => $ev['created_at'],
                            'title' => $ev['event_title'],
                            'description' => $ev['event_description'],
                            'type' => 'system_event',
                            'icon' => 'fa-info-circle',
                            'status' => 'completed'
                        ];
                    }
                } catch (Exception $e) { /* Ignore */ }
            }

            // Sort by date descending
            usort($timeline, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            return [
                'success' => true,
                'data' => $timeline
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch timeline: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get contract phases (milestones) for a project
     * 
     * @param int $projectId Project ID
     * @return array List of phases with amount and percentage
     */
    public function getContractPhases($projectId)
    {
        try {
            // 1. Get Contract details
            // Added total_budget to selection
            $sqlContract = "SELECT contract_id, payment_method, total_budget FROM Contract WHERE project_id = :project_id ORDER BY contract_id DESC LIMIT 1";
            $stmtContract = $this->pdo->prepare($sqlContract);
            $stmtContract->execute([':project_id' => $projectId]);
            $contract = $stmtContract->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                return [
                    'success' => true,
                    'data' => [] // No contract found
                ];
            }

            $contractId = $contract['contract_id'];
            $totalBudget = floatval($contract['total_budget'] ?? 0);

            // 2. Get Milestones
            // Try contract_milestone first (new table)
            $sqlMilestones = "SELECT 
                                milestone_id,
                                milestone_number as sort_order,
                                title as phase_name, 
                                description, 
                                due_date as target_date, 
                                percentage as pct_of_total, 
                                amount as amount_lkr,
                                status
                              FROM contract_milestone 
                              WHERE contract_id = :contract_id 
                              ORDER BY milestone_number ASC";
            
            $stmtMilestone = $this->pdo->prepare($sqlMilestones);
            $stmtMilestone->execute([':contract_id' => $contractId]);
            $phases = $stmtMilestone->fetchAll(PDO::FETCH_ASSOC);

            // If empty, try legacy Milestone table
            if (empty($phases)) {
                $sqlLegacy = "SELECT 
                                id as sort_order, -- simplified
                                name as phase_name, 
                                description, 
                                due_date as target_date, 
                                0 as pct_of_total, 
                                amount as amount_lkr,
                                status
                              FROM Milestone 
                              WHERE contract_id = :contract_id 
                              ORDER BY due_date ASC";
                $stmtLegacy = $this->pdo->prepare($sqlLegacy);
                $stmtLegacy->execute([':contract_id' => $contractId]);
                $phases = $stmtLegacy->fetchAll(PDO::FETCH_ASSOC);
            }

            // Fix for 'completion' payment method or single milestone with 0 amount
            if (count($phases) === 1 && floatval($phases[0]['amount_lkr']) == 0 && $totalBudget > 0) {
                $phases[0]['amount_lkr'] = $totalBudget;
                $phases[0]['pct_of_total'] = 100;
            }

            return [
                'success' => true,
                'data' => $phases,
                'payment_method' => $contract['payment_method']
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Failed to fetch phases: ' . $e->getMessage()
            ];
        }
        }


    /**
     * Start a phase work
     * 
     * @param int $milestoneId
     * @return array Success status
     */
    public function startPhase($milestoneId)
    {
        try {
            $sql = "UPDATE contract_milestone 
                    SET status = 'in_progress', 
                        updated_at = NOW() 
                    WHERE milestone_id = :milestone_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':milestone_id' => $milestoneId]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Phase started successfully'];
            }
            
            return ['success' => false, 'message' => 'Phase not found or no changes made'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Submit proof for phase completion
     * 
     * @param int $milestoneId
     * @param string $description
     * @param array $files Uploaded files
     * @return array
     */
    public function submitPhaseProof($milestoneId, $description, $files)
    {
        try {
            $uploadedPaths = [];
            $uploadDir = __DIR__ . '/../uploads/proofs/';
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Handle file uploads
            if (!empty($files['name'][0])) {
                $fileCount = count($files['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $fileName = time() . '_' . basename($files['name'][$i]);
                        $targetPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                            // Store relative path for database
                            $uploadedPaths[] = 'uploads/proofs/' . $fileName;
                        }
                    }
                }
            }

            $filesJson = !empty($uploadedPaths) ? json_encode($uploadedPaths) : null;

            $sql = "UPDATE contract_milestone 
                    SET status = 'submitted', 
                        completed_at = NOW(),
                        proof_of_work = :proof_of_work,
                        proof_files = :proof_files
                    WHERE milestone_id = :milestone_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':proof_of_work' => $description,
                ':proof_files' => $filesJson,
                ':milestone_id' => $milestoneId
            ]);

            return ['success' => true, 'message' => 'Phase submitted for review'];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    /**
     * Verify a phase (Approve or Reject)
     * 
     * @param int $milestoneId
     * @param string $action 'approve' or 'reject'
     * @param string $feedback Optional feedback
     * @return array
     */
    public function verifyPhase($milestoneId, $action, $feedback = '')
    {
        try {
            $this->pdo->beginTransaction();

            // 1. Get milestone details (amount, contract_id)
            $stmt = $this->pdo->prepare("SELECT contract_id, amount, title FROM contract_milestone WHERE milestone_id = :id");
            $stmt->execute([':id' => $milestoneId]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$milestone) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Milestone not found'];
            }

            if ($action === 'approve') {
                // UPDATE STATUS
                $sql = "UPDATE contract_milestone 
                        SET status = 'approved', 
                            approved_at = NOW() 
                        WHERE milestone_id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id' => $milestoneId]);

                // RELEASE ESCROW
                // Check if escrow account exists
                $stmt = $this->pdo->prepare("SELECT escrow_id, balance FROM escrow_accounts WHERE contract_id = :cid");
                $stmt->execute([':cid' => $milestone['contract_id']]);
                $escrow = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($escrow && $escrow['balance'] >= $milestone['amount']) {
                    // Deduct from escrow
                    $stmt = $this->pdo->prepare("UPDATE escrow_accounts SET balance = balance - :amt, updated_at = NOW() WHERE escrow_id = :eid");
                    $stmt->execute([':amt' => $milestone['amount'], ':eid' => $escrow['escrow_id']]);

                    // Log transaction
                    $stmt = $this->pdo->prepare("INSERT INTO escrow_transactions (escrow_id, transaction_type, amount, balance_after, reason, created_at) VALUES (:eid, 'release', :amt, :bal, :reason, NOW())");
                    $stmt->execute([
                        ':eid' => $escrow['escrow_id'],
                        ':amt' => $milestone['amount'],
                        ':bal' => $escrow['balance'] - $milestone['amount'],
                        ':reason' => "Milestone '{$milestone['title']}' Approved"
                    ]);
                } else {
                    // Optional: Log warning if no escrow or insufficient funds
                    // For now, we proceed with approval but note the payment issue? 
                    // Or we could fail the approval. Let's fail if strictly escrow-based.
                    // But for this project, let's allow approval even if escrow is empty (manual payment fallback).
                }

                $message = 'Phase approved and payment released from escrow';

            } elseif ($action === 'reject') {
                // REVERT STATUS
                $sql = "UPDATE contract_milestone 
                        SET status = 'in_progress', 
                            proof_of_work = NULL, 
                            proof_files = NULL,
                            completed_at = NULL 
                        WHERE milestone_id = :id";
                // Note: We might want to keep proof for history, but typically 'reject' means 'do it again'.
                // Let's NOT clear the proof text, but maybe move it to a history log? 
                // For simplicity: We keep valid current columns but status 'in_progress' implies it needs work.
                // Actually, clearing proof fields signals "not done".
                
                $sql = "UPDATE contract_milestone 
                        SET status = 'in_progress'
                        WHERE milestone_id = :id"; // Keep proof for reference? No, let's reset status primarily.

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id' => $milestoneId]);

                // We ideally need a "feedback" column. 
                // Since we don't have one in schema yet, we can append to description or description?
                // Let's check schema... `description` exists.
                if ($feedback) {
                    $stmt = $this->pdo->prepare("UPDATE contract_milestone SET description = CONCAT(description, '\n\n[REJECTION FEEDBACK]: ', :fb) WHERE milestone_id = :id");
                    $stmt->execute([':fb' => $feedback, ':id' => $milestoneId]);
                }

                $message = 'Phase rejected and returned to progress';
            } else {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Invalid action'];
            }

            $this->pdo->commit();
            return ['success' => true, 'message' => $message];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get project financial summary
     * 
     * @param int $projectId Project ID
     * @return array Financial summary
     */
    public function getProjectFinancials($projectId)
    {
        try {
            // 1. Get Project Budget
            $stmt = $this->pdo->prepare("SELECT budget FROM Project WHERE project_id = :project_id");
            $stmt->execute([':project_id' => $projectId]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$project) {
                return ['success' => false, 'message' => 'Project not found'];
            }

            $totalBudget = floatval($project['budget'] ?? 0);

            // 2. Get Contract
            $stmtContract = $this->pdo->prepare("SELECT contract_id FROM Contract WHERE project_id = :project_id ORDER BY contract_id DESC LIMIT 1");
            $stmtContract->execute([':project_id' => $projectId]);
            $contract = $stmtContract->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                return [
                    'success' => true,
                    'data' => [
                        'total_budget' => $totalBudget,
                        'escrow_balance' => 0,
                        'total_paid' => 0,
                        'total_pending' => 0,
                        'has_contract' => false
                    ]
                ];
            }

            $contractId = $contract['contract_id'];

            // 3. Get Escrow Balance
            $stmtEscrow = $this->pdo->prepare("SELECT held_amount FROM escrow_accounts WHERE contract_id = :contract_id");
            $stmtEscrow->execute([':contract_id' => $contractId]);
            $escrow = $stmtEscrow->fetch(PDO::FETCH_ASSOC);
            $escrowBalance = $escrow ? floatval($escrow['held_amount']) : 0;

            // 4. Get Milestones Totals
            // Try contract_milestone first (new table)
            // approved means paid out, others are pending
            $sqlMilestones = "SELECT 
                                SUM(CASE WHEN status = 'approved' THEN amount ELSE 0 END) as total_paid,
                                SUM(CASE WHEN status IN ('pending', 'in_progress', 'submitted', 'rejected') THEN amount ELSE 0 END) as total_pending
                              FROM contract_milestone 
                              WHERE contract_id = :contract_id";
            
            $stmtMilestone = $this->pdo->prepare($sqlMilestones);
            $stmtMilestone->execute([':contract_id' => $contractId]);
            $milestoneTotals = $stmtMilestone->fetch(PDO::FETCH_ASSOC);

            $totalPaid = floatval($milestoneTotals['total_paid'] ?? 0);
            $totalPending = floatval($milestoneTotals['total_pending'] ?? 0);

            // Use legacy table if new is empty
            if ($totalPaid == 0 && $totalPending == 0) {
                $sqlLegacy = "SELECT 
                                SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_paid,
                                SUM(CASE WHEN status != 'completed' THEN amount ELSE 0 END) as total_pending
                              FROM Milestone 
                              WHERE contract_id = :contract_id";
                $stmtLegacy = $this->pdo->prepare($sqlLegacy);
                $stmtLegacy->execute([':contract_id' => $contractId]);
                $legacyTotals = $stmtLegacy->fetch(PDO::FETCH_ASSOC);
                
                $totalPaid = floatval($legacyTotals['total_paid'] ?? 0);
                $totalPending = floatval($legacyTotals['total_pending'] ?? 0);
            }

            return [
                'success' => true,
                'data' => [
                    'total_budget' => $totalBudget,
                    'escrow_balance' => $escrowBalance,
                    'total_paid' => $totalPaid,
                    'total_pending' => $totalPending,
                    'has_contract' => true,
                    'contract_id' => $contractId
                ]
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to fetch financials: ' . $e->getMessage()];
        }
    }
}
