<?php

require_once __DIR__ . '/SystemNotificationService.php';
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
    private SystemNotificationService $notifier;

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
        $this->notifier = new SystemNotificationService($pdo);
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
                        u.district as customer_district,
                        c.name as company_name,
                        ct.contract_id
                    FROM Project p
                    LEFT JOIN User u ON p.customer_id = u.user_id
                    LEFT JOIN Company c ON p.company_id = c.company_id
                    LEFT JOIN Contract ct ON p.project_id = ct.project_id
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
                        u.address as customer_address,
                        u.district as customer_district,
                        c.name as company_name,
                        c.email as company_email,
                        c.contact_no as company_contact,
                        ct.contract_id
                    FROM Project p
                    LEFT JOIN User u ON p.customer_id = u.user_id
                    LEFT JOIN Company c ON p.company_id = c.company_id
                    LEFT JOIN Contract ct ON p.project_id = ct.project_id
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
     * @param int[] $employeeIds Company employee IDs to assign
     * @param int[] $freelancerAssignmentIds Freelancer assignment IDs to attach
     * @return array Success/error response with project_id
     */
    public function startFromContract($contractId, array $employeeIds = [], array $freelancerAssignmentIds = [], array $staffRequirements = [])
    {
        try {
            $this->pdo->beginTransaction();

            $employeeIds = array_values(array_unique(array_filter(array_map('intval', $employeeIds), fn($v) => $v > 0)));
            $freelancerAssignmentIds = array_values(array_unique(array_filter(array_map('intval', $freelancerAssignmentIds), fn($v) => $v > 0)));
            $staffRequirements = $this->normalizeStaffRequirements($staffRequirements);

            // 1. Fetch Contract Data
            // Note: Contract acceptance in this app is tracked via terms_accepted/customer_response (status is often 'active').
            $sqlContract = "SELECT c.*, 'General Maintenance' as category
                            FROM contract c
                            WHERE c.contract_id = :cid
                            LIMIT 1";
            $stmtC = $this->pdo->prepare($sqlContract);
            $stmtC->execute([':cid' => $contractId]);
            $contract = $stmtC->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                throw new Exception('Contract not found.');
            }

            $isAccepted = ((int)($contract['terms_accepted'] ?? 0) === 1) || ((string)($contract['customer_response'] ?? '') === 'accepted');
            if (!$isAccepted) {
                throw new Exception('Contract must be accepted before starting a project.');
            }

            $projectId = null;
            $title = "Project #" . $contractId . " (" . substr($contract['category'] ?? 'General', 0, 15) . ")";

            // If the contract is already linked to a project, do not create a second one.
            // However, if the referenced project row no longer exists (deleted/cleanup),
            // clear the link and allow re-start.
            if (!empty($contract['project_id'])) {
                $existingProjectId = (int)$contract['project_id'];

                $stmtP = $this->pdo->prepare('SELECT project_id, company_id, title FROM Project WHERE project_id = :pid LIMIT 1');
                $stmtP->execute([':pid' => $existingProjectId]);
                $projRow = $stmtP->fetch(PDO::FETCH_ASSOC);

                if (!$projRow) {
                    // Heal stale linkage
                    $stmtClr = $this->pdo->prepare('UPDATE Contract SET project_id = NULL WHERE contract_id = :cid');
                    $stmtClr->execute([':cid' => $contractId]);
                    $contract['project_id'] = null;
                } else {
                    if ((int)($projRow['company_id'] ?? 0) !== (int)($contract['company_id'] ?? 0)) {
                        throw new Exception('Linked project does not belong to this company.');
                    }

                    // Use the already-linked project (often created as a placeholder during contract creation)
                    $projectId = (int)$projRow['project_id'];
                    if (!empty($projRow['title'])) {
                        $title = (string)$projRow['title'];
                    }
                }
            }

            // 2. Create the Project record if none exists yet
            if (empty($projectId)) {
                $sqlInsert = "INSERT INTO Project (
                                company_id, customer_id, title, description, 
                                project_type, location, budget, start_date, 
                                end_date, status, progress
                            ) VALUES (
                                :company_id, :customer_id, :title, :description,
                                :project_type, :location, :budget, CURDATE(),
                                NULL, :status, 0
                            )";
                
                $location = $contract['address'] ?? $contract['project_location'] ?? 'Specified by customer';
                $budget = $contract['total_price'] ?? $contract['total_budget'] ?? 0;

                $stmtInsert = $this->pdo->prepare($sqlInsert);
                $stmtInsert->execute([
                    ':company_id' => $contract['company_id'],
                    ':customer_id' => $contract['customer_id'] ?? 0,
                    ':title' => $title,
                    ':description' => "Automatically generated from accepted Contract #" . $contractId,
                    ':project_type' => $contract['category'] ?? 'General',
                    ':location' => $location,
                    ':budget' => $budget,
                    ':status' => self::STATUS_IN_PROGRESS
                ]);

                $projectId = (int)$this->pdo->lastInsertId();

                // 3. Link Project back to Contract
                $sqlUpdate = "UPDATE Contract SET project_id = :pid WHERE contract_id = :cid";
                $stmtUpdate = $this->pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([':pid' => $projectId, ':cid' => $contractId]);
            } else {
                // Start/update the existing linked project
                $stmtStart = $this->pdo->prepare("
                    UPDATE Project
                    SET status = :status,
                        start_date = CURDATE()
                    WHERE project_id = :pid
                ");
                $stmtStart->execute([
                    ':status' => self::STATUS_IN_PROGRESS,
                    ':pid' => (int)$projectId
                ]);
            }

            // 4. Assign employees (optional)
            if (count($employeeIds) > 0) {
                $this->ensureProjectEmployeeAssignmentsTable();
                $this->assignEmployeesToProject((int)$projectId, (int)$contract['company_id'], $employeeIds);
            }

            // 5. Attach accepted freelancer offers
            if (count($freelancerAssignmentIds) > 0) {
                $this->attachFreelancerAssignmentsToProject((int)$projectId, (int)$contractId, (int)$contract['company_id'], $freelancerAssignmentIds);
            }

            // 6. Save staff-category requirements (optional, from staffsummary mode)
            if (count($staffRequirements) > 0) {
                $this->ensureProjectStaffRequirementsTable();
                $this->validateAndSaveStaffRequirements((int)$projectId, (int)$contract['company_id'], $staffRequirements);
            }

            $this->pdo->commit();

            $customerId = (int)($contract['customer_id'] ?? 0);
            $companyId = (int)($contract['company_id'] ?? 0);
            $titleLabel = $title;
            if ($customerId > 0) {
                $this->notifier->notify(
                    'Project started',
                    "Your project has started: {$titleLabel}.",
                    'user',
                    $customerId,
                    ['role' => 'company', 'id' => $companyId, 'name' => 'Company']
                );
            }
            if ($companyId > 0) {
                $this->notifier->notify(
                    'Project started',
                    "Project started successfully from contract #{$contractId}.",
                    'company',
                    $companyId,
                    ['role' => 'company', 'id' => $companyId, 'name' => 'Company']
                );
            }

            return [
                'success' => true,
                'message' => 'Project successfully started from contract.',
                'project_id' => (int)$projectId,
                'assigned_employees_count' => count($employeeIds),
                'attached_freelancers_count' => count($freelancerAssignmentIds),
                'staff_requirements_count' => count($staffRequirements)
            ];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function normalizeStaffRequirements(array $staffRequirements): array
    {
        $out = [];
        foreach ($staffRequirements as $row) {
            if (!is_array($row)) {
                continue;
            }
            $specialty = trim((string)($row['specialty'] ?? ''));
            if ($specialty === '') {
                continue;
            }
            $required = (int)($row['required_count'] ?? 0);
            if ($required <= 0) {
                continue;
            }

            if (!isset($out[$specialty])) {
                $out[$specialty] = 0;
            }
            $out[$specialty] += $required;
        }

        // Convert to stable list
        $list = [];
        foreach ($out as $specialty => $required) {
            $list[] = ['specialty' => $specialty, 'required_count' => $required];
        }
        return $list;
    }

    private function ensureProjectStaffRequirementsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS project_staff_requirements (
            id INT(11) NOT NULL AUTO_INCREMENT,
            project_id INT(11) NOT NULL,
            company_id INT(11) NOT NULL,
            specialty VARCHAR(100) NOT NULL,
            required_count INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_project_specialty (project_id, specialty),
            KEY idx_company_specialty (company_id, specialty),
            CONSTRAINT fk_psr_project FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE,
            CONSTRAINT fk_psr_company FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function validateAndSaveStaffRequirements(int $projectId, int $companyId, array $staffRequirements): void
    {
        // Validate availability against staffsummary capacity minus allocations to other open projects.
        $stmtCheck = $this->pdo->prepare('SELECT active_count, total_count FROM staffsummary WHERE company_id = ? AND specialty = ? LIMIT 1');
        $stmtAllocated = $this->pdo->prepare(
            "SELECT COALESCE(SUM(psr.required_count),0) AS allocated
             FROM project_staff_requirements psr
             INNER JOIN project p ON p.project_id = psr.project_id
             WHERE psr.company_id = ?
               AND psr.specialty = ?
               AND p.status IN ('planned','in_progress','on_hold')"
        );
        $stmtInsert = $this->pdo->prepare('INSERT INTO project_staff_requirements (project_id, company_id, specialty, required_count) VALUES (?,?,?,?)');

        foreach ($staffRequirements as $r) {
            $specialty = trim((string)($r['specialty'] ?? ''));
            $required = (int)($r['required_count'] ?? 0);
            if ($specialty === '' || $required <= 0) {
                continue;
            }

            $stmtCheck->execute([$companyId, $specialty]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                throw new Exception("Category '{$specialty}' is not available in the system.");
            }

            $active = (int)($row['active_count'] ?? 0);
            $total = (int)($row['total_count'] ?? 0);

            $capacity = $active > 0 ? $active : $total;

            $stmtAllocated->execute([$companyId, $specialty]);
            $allocated = (int)($stmtAllocated->fetchColumn() ?? 0);

            $available = $capacity - $allocated;
            if ($available < 0) {
                $available = 0;
            }

            if ($required > $available) {
                throw new Exception("Only {$available} '{$specialty}' available. Add more from the Workforce page.");
            }

            $stmtInsert->execute([$projectId, $companyId, $specialty, $required]);
        }
    }

    private function ensureProjectEmployeeAssignmentsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS project_employee_assignments (
            id INT(11) NOT NULL AUTO_INCREMENT,
            project_id INT(11) NOT NULL,
            employee_id INT(11) NOT NULL,
            assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_project_employee (project_id, employee_id),
            CONSTRAINT fk_pea_project FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE,
            CONSTRAINT fk_pea_employee FOREIGN KEY (employee_id) REFERENCES company_employees(employee_id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    private function assignEmployeesToProject(int $projectId, int $companyId, array $employeeIds): void
    {
        if (count($employeeIds) === 0) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($employeeIds), '?'));
        $sql = "SELECT employee_id
                FROM company_employees
                WHERE company_id = ?
                  AND status = 'active'
                  AND employment_type IN ('full_time','part_time')
                  AND (job_title IS NULL OR LOWER(job_title) <> 'freelancer')
                  AND employee_id IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_merge([$companyId], $employeeIds));
        $validIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $validIds = array_map('intval', $validIds);

        if (count($validIds) !== count($employeeIds)) {
            throw new Exception('One or more selected employees are invalid or not active staff for this company.');
        }

        $values = [];
        $params = [];
        foreach ($employeeIds as $eid) {
            $values[] = '(?, ?)';
            $params[] = $projectId;
            $params[] = (int)$eid;
        }

        $sqlInsert = 'INSERT IGNORE INTO project_employee_assignments (project_id, employee_id) VALUES ' . implode(',', $values);
        $stmtInsert = $this->pdo->prepare($sqlInsert);
        $stmtInsert->execute($params);
    }

    private function attachFreelancerAssignmentsToProject(int $projectId, int $contractId, int $companyId, array $assignmentIds): void
    {
        if (count($assignmentIds) === 0) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($assignmentIds), '?'));

        // Lock and validate first (avoid attaching already-linked or wrong-company offers)
        $sqlCheck = "SELECT assignment_id
                     FROM freelancer_assignments
                     WHERE company_id = ?
                       AND status = 'accepted'
                       AND project_id IS NULL
                       AND (contract_id IS NULL OR contract_id = ?)
                       AND assignment_id IN ($placeholders)
                     FOR UPDATE";
        $stmt = $this->pdo->prepare($sqlCheck);
        $stmt->execute(array_merge([$companyId, $contractId], $assignmentIds));
        $valid = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $valid = array_map('intval', $valid);

        if (count($valid) !== count($assignmentIds)) {
            throw new Exception('One or more selected freelancer offers are invalid, not accepted, or already linked to another project.');
        }

        // Attach to project (and link contract_id if it was NULL)
        $sqlUpdate = "UPDATE freelancer_assignments
                      SET project_id = ?, contract_id = COALESCE(contract_id, ?)
                      WHERE company_id = ?
                        AND status = 'accepted'
                        AND project_id IS NULL
                        AND (contract_id IS NULL OR contract_id = ?)
                        AND assignment_id IN ($placeholders)";
        $stmtUp = $this->pdo->prepare($sqlUpdate);
        $stmtUp->execute(array_merge([$projectId, $contractId, $companyId, $contractId], $assignmentIds));
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
                // Side effects for completion
                if ($status === self::STATUS_COMPLETED) {
                    // 1. Auto-release company employees
                    try {
                        $this->pdo->prepare("DELETE FROM project_employee_assignments WHERE project_id = :pid")
                                  ->execute([':pid' => $projectId]);
                    } catch (Throwable $e) {
                         // Table might not exist or other error, ignore to allow other side effects
                    }

                    // 2. Mark freelancer assignments as completed
                    try {
                        $this->pdo->prepare("UPDATE freelancer_assignments SET status = 'completed', updated_at = NOW() WHERE project_id = :pid AND status = 'accepted'")
                                  ->execute([':pid' => $projectId]);
                    } catch (Throwable $e) {}

                    // 3. Mark linked quotation and job request as completed
                    try {
                        $stmtContract = $this->pdo->prepare("SELECT quotation_id, job_request_id FROM contract WHERE project_id = :pid ORDER BY contract_id DESC LIMIT 1");
                        $stmtContract->execute([':pid' => $projectId]);
                        $contractInfo = $stmtContract->fetch(PDO::FETCH_ASSOC);

                        if ($contractInfo) {
                            if (!empty($contractInfo['quotation_id'])) {
                                $this->pdo->prepare("UPDATE companyquotation SET status = 'completed' WHERE quotation_id = :qid")
                                          ->execute([':qid' => $contractInfo['quotation_id']]);
                            }
                            if (!empty($contractInfo['job_request_id'])) {
                                $this->pdo->prepare("UPDATE jobrequest SET status = 'completed' WHERE request_id = :rid")
                                          ->execute([':rid' => $contractInfo['job_request_id']]);

                                // Sync collaboration state so user can leave a review
                                try {
                                    if (file_exists(__DIR__ . '/JobCollaborationModel.php')) {
                                        require_once __DIR__ . '/JobCollaborationModel.php';
                                        $collabModel = new JobCollaborationModel($this->pdo);

                                        $stmtProj = $this->pdo->prepare("SELECT company_id FROM Project WHERE project_id = :pid");
                                        $stmtProj->execute([':pid' => $projectId]);
                                        $projData = $stmtProj->fetch(PDO::FETCH_ASSOC);
                                        $companyId = $projData ? (int)$projData['company_id'] : 0;

                                        if ($companyId > 0) {
                                            $collabModel->bootstrapFromRequestIfMissingForProvider($companyId, 'company', (int)$contractInfo['job_request_id'], 'regular');
                                        }
                                    }
                                } catch (Throwable $collabEx) {}
                            }
                        }
                    } catch (Throwable $e) {}
                }

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
            $sqlContract = "SELECT contract_id, payment_method, total_budget, quotation_id, end_date FROM Contract WHERE project_id = :project_id ORDER BY contract_id DESC LIMIT 1";
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

            // Unit pricing is contract-level (from quotation). Units are entered per milestone.
            $quotationId = $contract['quotation_id'] ?? null;
            $unitPricing = [
                'labor_unit_label' => null,
                'material_unit_label' => null,
                'labor_unit_rate' => null,
                'material_unit_rate' => null,
                'is_unit_priced' => false,
            ];

            if ($quotationId !== null && $quotationId !== '' && is_numeric($quotationId)) {
                try {
                    $qStmt = $this->pdo->prepare("SELECT labor_unit_label, material_unit_label, labor_cost, material_cost, transport_cost, other_charges, total_amount FROM companyquotation WHERE quotation_id = :qid LIMIT 1");
                    $qStmt->execute([':qid' => (int)$quotationId]);
                    $q = $qStmt->fetch(PDO::FETCH_ASSOC) ?: null;
                    if ($q) {
                        $labUnit = trim((string)($q['labor_unit_label'] ?? ''));
                        $matUnit = trim((string)($q['material_unit_label'] ?? ''));
                        $unitPricing['labor_unit_label'] = $labUnit !== '' ? $labUnit : null;
                        $unitPricing['material_unit_label'] = $matUnit !== '' ? $matUnit : null;

                        $laborRate = null;
                        if (isset($q['labor_cost']) && $q['labor_cost'] !== null && $q['labor_cost'] !== '' && is_numeric($q['labor_cost'])) {
                            $laborRate = (float)$q['labor_cost'];
                        }
                        $unitPricing['labor_unit_rate'] = $laborRate;

                        $materialRate = 0.0;
                        foreach (['material_cost', 'transport_cost', 'other_charges'] as $k) {
                            $v = $q[$k] ?? 0;
                            if ($v !== null && $v !== '' && is_numeric($v)) {
                                $materialRate += (float)$v;
                            }
                        }
                        if ($materialRate <= 0 && isset($q['total_amount']) && $q['total_amount'] !== null && $q['total_amount'] !== '' && is_numeric($q['total_amount'])) {
                            $materialRate = max(0.0, (float)$q['total_amount'] - (float)($laborRate ?? 0));
                        }
                        $unitPricing['material_unit_rate'] = $materialRate;

                        $unitPricing['is_unit_priced'] = ($labUnit !== '' || $matUnit !== '') && ($laborRate !== null || $materialRate > 0);
                    }
                } catch (Exception $e) {
                    // Ignore quotation fetch errors to avoid breaking timeline.
                }
            }

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
                                                                status,
                                                                unit_label,
                                                                unit_rate,
                                                                actual_unit_rate,
                                                                estimated_quantity,
                                                                actual_quantity,
                                                                actual_amount,
                                                                is_non_paying,
                                                                actual_labor_quantity,
                                                                actual_material_quantity,
                                                                actual_material_unit_rate,
                                                                actual_extra_amount
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

            // Remove legacy auto-generated unit-billing rows ("Labour"/"Materials") from the timeline.
            // Unit quantities are now entered inside each milestone completion form.
            if (!empty($phases) && ($unitPricing['is_unit_priced'] ?? false)) {
                $phases = array_values(array_filter($phases, function ($p) {
                    $title = strtolower(trim((string)($p['phase_name'] ?? '')));
                    if ($title !== 'labour' && $title !== 'materials') {
                        return true;
                    }

                    $desc = strtolower(trim((string)($p['description'] ?? '')));
                    $isAutoDesc = (
                        strpos($desc, 'company submits actual labour units') === 0
                        || strpos($desc, 'company submits actual material units') === 0
                    );

                    $amount = isset($p['amount_lkr']) && is_numeric($p['amount_lkr']) ? (float)$p['amount_lkr'] : null;
                    $unitLabel = trim((string)($p['unit_label'] ?? ''));
                    $unitRate = $p['unit_rate'] ?? null;

                    // Only filter when it matches the known system-generated pattern.
                    if ($isAutoDesc && ($amount === 0.0 || $amount === 0) && $unitLabel !== '' && $unitRate !== null) {
                        return false;
                    }
                    return true;
                }));
            }

            // Attach contract-level unit pricing info to every milestone (used by completion form)
            if (!empty($phases)) {
                foreach ($phases as &$p) {
                    $p['labor_unit_label'] = $unitPricing['labor_unit_label'];
                    $p['material_unit_label'] = $unitPricing['material_unit_label'];
                    $p['labor_unit_rate'] = $unitPricing['labor_unit_rate'];
                    $p['material_unit_rate'] = $unitPricing['material_unit_rate'];
                    $p['is_unit_priced'] = $unitPricing['is_unit_priced'] ? 1 : 0;
                }
                unset($p);
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
     * Recalculate project progress based on approved milestones
     * 
     * @param int $projectId
     * @return array
     */
    public function recalculateProjectProgress(int $projectId): array
    {
        try {
            $phasesResult = $this->getContractPhases($projectId);
            if (!$phasesResult['success']) {
                return $phasesResult;
            }

            $phases = $phasesResult['data'] ?? [];
            if (empty($phases)) {
                return ['success' => true, 'progress' => 0];
            }

            $count = count($phases);
            $totalProgress = 0;
            $allApproved = true;

            // Check if weights are assigned (sum of pct_of_total should be > 0)
            $totalWeight = array_sum(array_column($phases, 'pct_of_total'));
            $useEqualWeights = ($totalWeight < 1); // If total weight is near 0, assume equal weight

            foreach ($phases as $phase) {
                // Consider 'approved' or 'paid' as completed phases
                if (in_array($phase['status'], ['approved', 'paid'])) {
                    if ($useEqualWeights) {
                        $totalProgress += (100 / $count);
                    } else {
                        $totalProgress += (float)($phase['pct_of_total'] ?? 0);
                    }
                } else {
                    $allApproved = false;
                }
            }

            // If all milestones are approved, force progress to 100
            if ($allApproved) {
                $totalProgress = 100;
            } else {
                // Round and cap at 100
                $totalProgress = min(100, (int)round($totalProgress));
            }

            // Update project record progress
            $this->updateProgress($projectId, $totalProgress);

            // If progress is 100%, set status to completed
            if ($totalProgress >= 100) {
                $this->updateStatus($projectId, self::STATUS_COMPLETED);
            }

            return [
                'success' => true,
                'progress' => $totalProgress,
                'is_completed' => ($totalProgress >= 100 || $allApproved)
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Progress recalculation failed: ' . $e->getMessage()];
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
    public function submitPhaseProof(
        $milestoneId,
        $description,
        $files,
        $laborQuantityOrLegacyQty = null,
        $materialQuantity = null,
        $materialUnitRate = null,
        $extraAmount = null,
        $nonPaying = false
    )
    {
        try {
            require_once __DIR__ . '/../includes/undo.php';

            $splitMode = ($materialQuantity !== null || $materialUnitRate !== null || $extraAmount !== null);

            // Rates/labels are derived from quotation for unit-priced contracts.
            // Legacy mode uses contract_milestone.unit_rate + (optional) actual_unit_rate.
            $agreedLaborRate = 0.0;
            $agreedMaterialRate = 0.0;

            if ($splitMode) {
                $qStmt = $this->pdo->prepare(
                    "SELECT c.quotation_id,
                            q.labor_cost, q.material_cost, q.transport_cost, q.other_charges, q.total_amount
                     FROM contract_milestone m
                     JOIN contract c ON c.contract_id = m.contract_id
                     LEFT JOIN companyquotation q ON q.quotation_id = c.quotation_id
                     WHERE m.milestone_id = :id
                     LIMIT 1"
                );
                $qStmt->execute([':id' => $milestoneId]);
                $q = $qStmt->fetch(PDO::FETCH_ASSOC) ?: null;

                if ($q) {
                    if (isset($q['labor_cost']) && $q['labor_cost'] !== null && $q['labor_cost'] !== '' && is_numeric($q['labor_cost'])) {
                        $agreedLaborRate = (float)$q['labor_cost'];
                    }

                    $materialRate = 0.0;
                    foreach (['material_cost', 'transport_cost', 'other_charges'] as $k) {
                        $v = $q[$k] ?? 0;
                        if ($v !== null && $v !== '' && is_numeric($v)) {
                            $materialRate += (float)$v;
                        }
                    }
                    if ($materialRate <= 0 && isset($q['total_amount']) && $q['total_amount'] !== null && $q['total_amount'] !== '' && is_numeric($q['total_amount'])) {
                        $materialRate = max(0.0, (float)$q['total_amount'] - $agreedLaborRate);
                    }
                    $agreedMaterialRate = $materialRate;
                }
            }

            $toNullableFloat = function ($v) {
                if ($v === null) return null;
                if ($v === '') return null;
                if (!is_numeric($v)) return null;
                return (float)$v;
            };

            $laborQty = $toNullableFloat($laborQuantityOrLegacyQty);
            $materialQty = $toNullableFloat($materialQuantity);
            $extra = $toNullableFloat($extraAmount);
            $matRateOverride = $toNullableFloat($materialUnitRate);

            foreach ([['Labour units', $laborQty], ['Material units', $materialQty], ['Extra amount', $extra], ['Material unit rate', $matRateOverride]] as $pair) {
                $label = $pair[0];
                $val = $pair[1];
                if ($val !== null && $val < 0) {
                    return ['success' => false, 'message' => $label . ' must be 0 or greater'];
                }
            }

            $isNonPaying = (bool)$nonPaying;

            // Compute actual_amount
            $actualAmount = null;
            $actualLaborQty = null;
            $actualMaterialQty = null;
            $actualMaterialRate = null;
            $actualExtra = null;

            if ($isNonPaying) {
                $actualAmount = 0.0;
            } else {
                if ($splitMode) {
                    $actualLaborQty = $laborQty;
                    $actualMaterialQty = $materialQty;
                    $actualExtra = $extra;

                    $effectiveMatRate = $agreedMaterialRate;
                    if ($matRateOverride !== null && $matRateOverride > 0) {
                        $effectiveMatRate = $matRateOverride;
                        $actualMaterialRate = $matRateOverride;
                    }

                    $laborPart = ($actualLaborQty !== null) ? ($agreedLaborRate * $actualLaborQty) : 0.0;
                    $materialPart = ($actualMaterialQty !== null) ? ($effectiveMatRate * $actualMaterialQty) : 0.0;
                    $extraPart = ($actualExtra !== null) ? $actualExtra : 0.0;

                    $actualAmount = round($laborPart + $materialPart + $extraPart, 2);
                } else {
                    // Legacy: single-unit billing (actual_quantity × COALESCE(actual_unit_rate, unit_rate))
                    $mStmt = $this->pdo->prepare("SELECT unit_rate FROM contract_milestone WHERE milestone_id = :id LIMIT 1");
                    $mStmt->execute([':id' => $milestoneId]);
                    $milestone = $mStmt->fetch(PDO::FETCH_ASSOC);

                    $agreedRate = $milestone && $milestone['unit_rate'] !== null ? (float)$milestone['unit_rate'] : 0.0;
                    $effectiveRate = $agreedRate;
                    if ($matRateOverride !== null && $matRateOverride > 0) {
                        $effectiveRate = $matRateOverride;
                    }

                    $qty = $laborQty;
                    if ($qty !== null && $qty > 0 && $effectiveRate > 0) {
                        $actualAmount = round($effectiveRate * $qty, 2);
                    }

                    $actualLaborQty = $qty;
                    $actualMaterialRate = ($matRateOverride !== null && $matRateOverride > 0) ? $matRateOverride : null;
                }
            }

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
                        proof_files = :proof_files,
                        is_non_paying = :is_non_paying,
                        actual_labor_quantity = :actual_labor_qty,
                        actual_material_quantity = :actual_material_qty,
                        actual_material_unit_rate = :actual_material_unit_rate,
                        actual_extra_amount = :actual_extra_amount,
                        actual_amount = :actual_amount,
                        -- legacy fields (no longer primary for billing)
                        actual_quantity = :legacy_actual_qty,
                        actual_unit_rate = :legacy_actual_unit_rate
                    WHERE milestone_id = :milestone_id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':proof_of_work' => $description,
                ':proof_files' => $filesJson,
                ':is_non_paying' => $isNonPaying ? 1 : 0,
                ':actual_labor_qty' => $isNonPaying ? null : $actualLaborQty,
                ':actual_material_qty' => $isNonPaying ? null : $actualMaterialQty,
                ':actual_material_unit_rate' => $isNonPaying ? null : $actualMaterialRate,
                ':actual_extra_amount' => $isNonPaying ? null : $actualExtra,
                ':actual_amount' => $actualAmount,
                ':legacy_actual_qty' => ($splitMode || $isNonPaying) ? null : $actualLaborQty,
                ':legacy_actual_unit_rate' => ($splitMode || $isNonPaying) ? null : $actualMaterialRate,
                ':milestone_id' => $milestoneId
            ]);

            // Create short undo window (company can revert submission quickly)
            $undo = undo_create(
                $this->pdo,
                'milestone',
                (int)$milestoneId,
                'submit_proof',
                [
                    'entity' => 'contract_milestone',
                    'note' => 'Undo milestone proof submission'
                ],
                null,
                null,
                null
            );

            return [
                'success' => true,
                'message' => 'Phase submitted for review',
                'undo' => $undo
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Undo a submitted phase (within short grace period).
     */
    public function undoSubmittedPhase(int $milestoneId, int $companyId): array
    {
        require_once __DIR__ . '/../includes/undo.php';

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "SELECT m.milestone_id, m.contract_id, m.status, m.completed_at,
                        m.approved_at, m.reviewed_at,
                        c.company_id
                 FROM contract_milestone m
                 JOIN contract c ON c.contract_id = m.contract_id
                 WHERE m.milestone_id = :id
                 LIMIT 1"
            );
            $stmt->execute([':id' => $milestoneId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Milestone not found'];
            }
            if ((int)$row['company_id'] !== (int)$companyId) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Unauthorized'];
            }
            if (($row['status'] ?? '') !== 'submitted') {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Only submitted milestones can be undone'];
            }
            if (!empty($row['approved_at']) || !empty($row['reviewed_at'])) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Undo is not available after review'];
            }

            // Check active undo window (fallback to completed_at timing)
            $active = undo_get_active($this->pdo, 'milestone', (int)$milestoneId, 'submit_proof');
            $secondsRemaining = $active['seconds_remaining'] ?? null;

            if ($active === null) {
                // Backward-compatible fallback: 30s from completed_at
                if (empty($row['completed_at'])) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Undo window is not available'];
                }
                $diffStmt = $this->pdo->prepare("SELECT TIMESTAMPDIFF(SECOND, completed_at, NOW()) AS s FROM contract_milestone WHERE milestone_id = :id");
                $diffStmt->execute([':id' => $milestoneId]);
                $s = (int)($diffStmt->fetch(PDO::FETCH_ASSOC)['s'] ?? 999999);
                if ($s > undo_window_seconds()) {
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Undo window has expired'];
                }
            }

            // Revert submission back to in_progress and clear proof/billing fields
            $upd = $this->pdo->prepare(
                "UPDATE contract_milestone
                 SET status = 'in_progress',
                     completed_at = NULL,
                     proof_of_work = NULL,
                     proof_files = NULL,
                     is_non_paying = 0,
                     actual_labor_quantity = NULL,
                     actual_material_quantity = NULL,
                     actual_material_unit_rate = NULL,
                     actual_extra_amount = NULL,
                     actual_amount = NULL,
                     actual_quantity = NULL,
                     actual_unit_rate = NULL,
                     updated_at = NOW()
                 WHERE milestone_id = :id AND status = 'submitted'"
            );
            $upd->execute([':id' => $milestoneId]);

            if ($upd->rowCount() === 0) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Nothing to undo'];
            }

            if ($active && !empty($active['undo_id'])) {
                $uid = $_SESSION['user_id'] ?? null;
                $role = $_SESSION['user_role'] ?? null;
                undo_mark_used($this->pdo, (int)$active['undo_id'], $uid ? (int)$uid : null, $role ? (string)$role : null);
            }

            $this->pdo->commit();
            return [
                'success' => true,
                'message' => 'Milestone submission undone',
                'seconds_remaining' => $secondsRemaining
            ];

        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
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

            // 1. Get milestone details and project_id from linked contract
            $stmt = $this->pdo->prepare("
                SELECT m.contract_id, m.title, m.status, COALESCE(m.actual_amount, m.amount) AS billed_amount,
                       c.project_id
                FROM contract_milestone m
                JOIN contract c ON c.contract_id = m.contract_id
                WHERE m.milestone_id = :id
            ");
            $stmt->execute([':id' => $milestoneId]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$milestone) {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Milestone not found'];
            }

            $projectId = (int)($milestone['project_id'] ?? 0);
            $billedAmount = (float)($milestone['billed_amount'] ?? 0);

            if ($action === 'approve') {
                // UPDATE STATUS (idempotent-safe)
                $sql = "UPDATE contract_milestone 
                        SET status = 'approved', 
                            approved_at = NOW() 
                        WHERE milestone_id = :id AND status = 'submitted'";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id' => $milestoneId]);

                if ($stmt->rowCount() === 0) {
                    if (($milestone['status'] ?? '') === 'approved') {
                        $this->pdo->commit();
                        return ['success' => true, 'message' => 'Phase already approved', 'billed_amount' => $billedAmount];
                    }
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Phase not in submitted status'];
                }

                // Update contract totals (unit-based billed amount)
                if ($billedAmount > 0) {
                    $this->pdo->prepare(
                        "UPDATE contract
                         SET amount_paid    = COALESCE(amount_paid, 0) + :billed,
                             amount_pending = GREATEST(0, COALESCE(amount_pending, 0) - :billed2)
                         WHERE contract_id  = :cid"
                    )->execute([':billed' => $billedAmount, ':billed2' => $billedAmount, ':cid' => $milestone['contract_id']]);

                    // Record the payment transaction in milestonepayment
                    $payStmt = $this->pdo->prepare(
                        "INSERT INTO milestonepayment
                            (contract_id, milestone_id, amount, payment_type, description, status, paid_at, created_at)
                         VALUES
                            (:contract_id, :milestone_id, :amount, 'milestone', :description, 'completed', NOW(), NOW())"
                    );
                    $payStmt->execute([
                        ':contract_id'  => $milestone['contract_id'],
                        ':milestone_id' => $milestoneId,
                        ':amount'       => $billedAmount,
                        ':description'  => 'Phase approved: ' . ($milestone['title'] ?? 'Milestone'),
                    ]);
                }

                $message = 'Phase approved';

            } elseif ($action === 'reject') {
                $sql = "UPDATE contract_milestone 
                        SET status = 'rejected',
                            reviewed_at = NOW(),
                            review_comments = :fb
                        WHERE milestone_id = :id AND status = 'submitted'";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':fb' => $feedback ?: null, ':id' => $milestoneId]);

                if ($stmt->rowCount() === 0) {
                    if (($milestone['status'] ?? '') === 'rejected') {
                        $this->pdo->commit();
                        return ['success' => true, 'message' => 'Phase already rejected'];
                    }
                    $this->pdo->rollBack();
                    return ['success' => false, 'message' => 'Phase not in submitted status'];
                }

                $message = 'Phase rejected';
            } else {
                $this->pdo->rollBack();
                return ['success' => false, 'message' => 'Invalid action'];
            }

            // 2. Trigger progress recalculation if approved
            if ($action === 'approve' && $projectId > 0) {
                $this->recalculateProjectProgress($projectId);
            }

            $this->pdo->commit();
            $resp = ['success' => true, 'message' => $message];
            if ($action === 'approve') {
                $resp['billed_amount'] = $billedAmount;
            }
            return $resp;

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

            // 2. Get Contract (join quotation to get unit pricing labels)
            $stmtContract = $this->pdo->prepare("
                SELECT c.contract_id,
                       COALESCE(cq.labor_unit_label, cq_req.labor_unit_label) AS labor_unit_label,
                       COALESCE(cq.material_unit_label, cq_req.material_unit_label) AS material_unit_label,
                       COALESCE(cq.labor_cost, cq_req.labor_cost) AS labor_cost,
                       COALESCE(cq.material_cost, cq_req.material_cost) AS material_cost
                FROM Contract c
                LEFT JOIN companyquotation cq ON c.quotation_id = cq.quotation_id
                LEFT JOIN companyquotation cq_req ON cq_req.quotation_id = (
                    SELECT q2.quotation_id
                    FROM companyquotation q2
                    WHERE q2.request_id = c.job_request_id
                      AND (q2.company_id = c.company_id OR q2.company_id IS NULL)
                      AND q2.status IN ('accepted', 'successful')
                    ORDER BY (q2.status = 'successful') DESC, q2.updated_at DESC
                    LIMIT 1
                )
                WHERE c.project_id = :project_id
                ORDER BY c.contract_id DESC LIMIT 1
            ");
            $stmtContract->execute([':project_id' => $projectId]);
            $contract = $stmtContract->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                return [
                    'success' => true,
                    'data' => [
                        'total_budget' => $totalBudget,
                        'total_paid' => 0,
                        'total_pending' => 0,
                        'has_contract' => false
                    ]
                ];
            }

            $contractId = $contract['contract_id'];

            // 3. Get Milestones Totals
            // Try contract_milestone first (new table)
            // approved means paid out, others are pending
                        $sqlMilestones = "SELECT 
                                                                SUM(CASE WHEN status IN ('approved', 'paid') THEN COALESCE(actual_amount, amount) ELSE 0 END) as total_paid,
                                                                SUM(CASE WHEN status IN ('pending', 'in_progress', 'submitted', 'rejected', 'under_review') THEN COALESCE(actual_amount, amount) ELSE 0 END) as total_pending
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

            // 4. Fetch payment history from milestonepayment
            $stmtPay = $this->pdo->prepare(
                "SELECT mp.*, cm.title as milestone_title
                 FROM milestonepayment mp
                 LEFT JOIN contract_milestone cm ON cm.milestone_id = mp.milestone_id
                 WHERE mp.contract_id = :cid
                 ORDER BY mp.created_at DESC"
            );
            $stmtPay->execute([':cid' => $contractId]);
            $payments = $stmtPay->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'data' => [
                    'total_budget'        => $totalBudget,
                    'total_paid'          => $totalPaid,
                    'total_pending'       => $totalPending,
                    'has_contract'        => true,
                    'contract_id'         => $contractId,
                    'labor_unit_label'    => $contract['labor_unit_label'] ?? null,
                    'material_unit_label' => $contract['material_unit_label'] ?? null,
                    'labor_cost'          => $contract['labor_cost'] ?? null,
                    'material_cost'       => $contract['material_cost'] ?? null,
                    'payments'            => $payments
                ]
            ];

        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to fetch financials: ' . $e->getMessage()];
        }
    }
}
