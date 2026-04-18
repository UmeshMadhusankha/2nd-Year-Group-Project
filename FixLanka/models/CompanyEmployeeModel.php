<?php

require_once __DIR__ . '/SystemNotificationService.php';
/**
 * CompanyEmployee Model
 * 
 * Handles all database operations for company employees
 * 
 * @package FixLanka\Models
 * @version 1.0.0
 */

class CompanyEmployeeModel {
    private $db;
    private SystemNotificationService $notifier;
    
    public function __construct($database) {
        $this->db = $database;
        $this->notifier = new SystemNotificationService($database);
    }

    private function getCompanyName(int $companyId): string
    {
        try {
            $stmt = $this->db->prepare('SELECT name FROM company WHERE company_id = ? LIMIT 1');
            $stmt->execute([$companyId]);
            $name = $stmt->fetchColumn();
            return $name ? (string)$name : 'Company'; 
        } catch (Throwable $e) {
            return 'Company';
        }
    }

    private function safeRollback(): void
    {
        try {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
        } catch (Throwable $e) {
            error_log('Rollback skipped: ' . $e->getMessage());
        }
    }
    
    /**
     * Transform employee data to clean format
     * Converts NULL values to appropriate defaults for display
     */
    private function transformEmployee($employee) {
        if (!$employee) {
            return null;
        }
        
        // Handle NULL values for display
        $employee['email'] = ($employee['email'] === null || $employee['email'] === 'NULL') ? null : $employee['email'];
        $employee['phone'] = ($employee['phone'] === null || $employee['phone'] === 'NULL') ? null : $employee['phone'];
        $employee['certification_details'] = ($employee['certification_details'] === null || $employee['certification_details'] === 'NULL') ? null : $employee['certification_details'];
        $employee['profile_photo'] = ($employee['profile_photo'] === null || $employee['profile_photo'] === 'NULL' || $employee['profile_photo'] === '0') ? null : $employee['profile_photo'];
        
        // Generate avatar initials from first and last name
        if (!$employee['profile_photo']) {
            $firstInitial = substr($employee['first_name'], 0, 1);
            $lastInitial = substr($employee['last_name'], 0, 1);
            $employee['avatar'] = strtoupper($firstInitial . $lastInitial);
        } else {
            $employee['avatar'] = $employee['profile_photo'];
        }
        
        // Ensure numeric values are proper types
        $employee['hourly_rate'] = (float) $employee['hourly_rate'];
        $employee['rating'] = (float) $employee['rating'];
        $employee['experience_years'] = (int) $employee['experience_years'];

        if (array_key_exists('success_rate_pct', $employee)) {
            $employee['success_rate_pct'] = ($employee['success_rate_pct'] === null) ? null : (int)$employee['success_rate_pct'];
        }
        if (array_key_exists('assignments_completed_count', $employee)) {
            $employee['assignments_completed_count'] = (int)($employee['assignments_completed_count'] ?? 0);
        }
        if (array_key_exists('assignments_finished_count', $employee)) {
            $employee['assignments_finished_count'] = (int)($employee['assignments_finished_count'] ?? 0);
        }
        
        return $employee;
    }
    
    /**
     * Get all employees for a company with optional filters
     */
    public function getAll($companyId, $filters = []) {
        try {
                 $query = "SELECT ce.*, r.f_name as first_name, r.l_name as last_name, r.email, r.phoneNumber as phone,
                         c.name as specialty, r.experience_years, r.ratings as rating,
                         r.hourly_rate as applicant_hourly_rate, r.profile_picture as profile_photo,
                         fas.completed_count AS assignments_completed_count,
                         fas.finished_count AS assignments_finished_count,
                         CASE
                           WHEN fas.finished_count > 0 THEN ROUND((fas.completed_count / fas.finished_count) * 100)
                           ELSE NULL
                         END AS success_rate_pct
                      FROM company_employees ce
                      LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                      LEFT JOIN category c ON r.category_id = c.category_id
                      LEFT JOIN (
                          SELECT
                              company_id,
                              repairer_id,
                              SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
                              SUM(CASE WHEN status IN ('completed','cancelled') THEN 1 ELSE 0 END) AS finished_count
                          FROM freelancer_assignments
                          GROUP BY company_id, repairer_id
                      ) fas ON fas.company_id = ce.company_id AND fas.repairer_id = ce.repairer_id
                      WHERE ce.company_id = :company_id";
            $params = ['company_id' => $companyId];

            // Filter by employment type.
            // By default, exclude freelance contractors from "Company Employees" views.
            $employmentTypes = null;
            if (isset($filters['employment_type']) && $filters['employment_type'] !== null && $filters['employment_type'] !== '') {
                $employmentTypes = $filters['employment_type'];
                if (!is_array($employmentTypes)) {
                    $employmentTypes = array_filter(array_map('trim', explode(',', (string)$employmentTypes)));
                }
            }

            if ($employmentTypes && count($employmentTypes) > 0) {
                $placeholders = [];
                $requested = array_map('strtolower', array_values($employmentTypes));

                foreach (array_values($employmentTypes) as $idx => $etype) {
                    $ph = ":etype_$idx";
                    $placeholders[] = $ph;
                    $params[ltrim($ph, ':')] = $etype;
                }

                // Legacy compatibility: previously, job-posting recruits were inserted with job_title='Freelancer'
                // but could have been saved with employment_type != 'freelance'. When requesting freelance,
                // include those legacy rows.
                $legacyFreelancerClause = in_array('freelance', $requested, true)
                    ? " OR (ce.job_title IS NOT NULL AND LOWER(ce.job_title) = 'freelancer')"
                    : '';

                $query .= " AND (ce.employment_type IN (" . implode(',', $placeholders) . "){$legacyFreelancerClause})";
            } else {
                // Default: permanent staff only (exclude freelance contractors).
                // Also exclude legacy job-posting recruits labeled as job_title='Freelancer'.
                $query .= " AND ce.employment_type IN ('full_time','part_time') AND (ce.job_title IS NULL OR LOWER(ce.job_title) <> 'freelancer')";
            }
            
            // Filter by specialty
            if (!empty($filters['specialty'])) {
                $query .= " AND c.name = :specialty";
                $params['specialty'] = $filters['specialty'];
            }
            
            // Filter by status
            if (!empty($filters['status'])) {
                $query .= " AND ce.status = :status";
                $params['status'] = $filters['status'];
            }
            
            // Search by name
            if (!empty($filters['search'])) {
                $query .= " AND (r.f_name LIKE :search OR r.l_name LIKE :search OR r.email LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }
            
            // Order by
            $orderKey = $filters['order_by'] ?? 'created_at';
            $orderDir = strtoupper($filters['order_dir'] ?? 'DESC');
            if (!in_array($orderDir, ['ASC', 'DESC'], true)) {
                $orderDir = 'DESC';
            }

            $allowedOrder = [
                'created_at' => 'ce.created_at',
                'updated_at' => 'ce.updated_at',
                'hired_date' => 'ce.hired_date',
                'rating' => 'r.ratings',
                'hourly_rate' => 'ce.hourly_rate',
                'first_name' => 'r.f_name',
                'last_name' => 'r.l_name'
            ];
            $orderBy = $allowedOrder[$orderKey] ?? 'ce.created_at';
            $query .= " ORDER BY {$orderBy} {$orderDir}";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Transform each employee
            return array_map([$this, 'transformEmployee'], $employees);
            
        } catch (PDOException $e) {
            error_log("Error fetching employees: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get employee by ID
     */
    public function getById($employeeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ce.*, r.f_name as first_name, r.l_name as last_name, r.email, r.phoneNumber as phone,
                       c.name as specialty, r.experience_years, r.ratings as rating,
                       r.hourly_rate as applicant_hourly_rate, r.profile_picture as profile_photo,
                       fas.completed_count AS assignments_completed_count,
                       fas.finished_count AS assignments_finished_count,
                       CASE
                         WHEN fas.finished_count > 0 THEN ROUND((fas.completed_count / fas.finished_count) * 100)
                         ELSE NULL
                       END AS success_rate_pct
                FROM company_employees ce
                LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
                LEFT JOIN (
                    SELECT
                        company_id,
                        repairer_id,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_count,
                        SUM(CASE WHEN status IN ('completed','cancelled') THEN 1 ELSE 0 END) AS finished_count
                    FROM freelancer_assignments
                    GROUP BY company_id, repairer_id
                ) fas ON fas.company_id = ce.company_id AND fas.repairer_id = ce.repairer_id
                WHERE ce.employee_id = :employee_id
            ");
            $stmt->execute(['employee_id' => $employeeId]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->transformEmployee($employee);
        } catch (PDOException $e) {
            error_log("Error fetching employee: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get employees by specialty
     */
    public function getBySpecialty($companyId, $specialty) {
        try {
            $stmt = $this->db->prepare("
                SELECT ce.*, r.f_name as first_name, r.l_name as last_name, r.email, r.phoneNumber as phone,
                       c.name as specialty, r.experience_years, r.ratings as rating,
                       r.hourly_rate as applicant_hourly_rate, r.profile_picture as profile_photo
                FROM company_employees ce
                LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE ce.company_id = :company_id AND c.name = :specialty
                ORDER BY r.ratings DESC
            ");
            $stmt->execute([
                'company_id' => $companyId,
                'specialty' => $specialty
            ]);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Transform each employee
            return array_map([$this, 'transformEmployee'], $employees);
        } catch (PDOException $e) {
            error_log("Error fetching employees by specialty: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get employee statistics from StaffSummary
     */
    public function getStatistics($companyId, $filters = []) {
        try {
            // Prefer staffsummary if present (used by workforce "bulk add/reduce by numbers" flows).
            // Fallback to aggregating company_employees when summary table is empty.
            $summaryStats = $this->getStaffSummaryStatistics($companyId);
            if ($summaryStats !== null) {
                return $summaryStats;
            }

            // Filter by employment type (default: staff only)
            $employmentTypes = null;
            if (isset($filters['employment_type']) && $filters['employment_type'] !== null && $filters['employment_type'] !== '') {
                $employmentTypes = $filters['employment_type'];
                if (!is_array($employmentTypes)) {
                    $employmentTypes = array_filter(array_map('trim', explode(',', (string)$employmentTypes)));
                }
            }

            $employmentWhere = '';
            $params = ['company_id' => $companyId];
            if ($employmentTypes && count($employmentTypes) > 0) {
                $placeholders = [];
                $requested = array_map('strtolower', array_values($employmentTypes));
                foreach (array_values($employmentTypes) as $idx => $etype) {
                    $ph = ":stype_$idx";
                    $placeholders[] = $ph;
                    $params[ltrim($ph, ':')] = $etype;
                }

                $legacyFreelancerClause = in_array('freelance', $requested, true)
                    ? " OR (ce.job_title IS NOT NULL AND LOWER(ce.job_title) = 'freelancer')"
                    : '';

                $employmentWhere = " AND (ce.employment_type IN (" . implode(',', $placeholders) . "){$legacyFreelancerClause})";
            } else {
                $employmentWhere = " AND ce.employment_type IN ('full_time','part_time') AND (ce.job_title IS NULL OR LOWER(ce.job_title) <> 'freelancer')";
            }

            // Get summary by specialty
            $stmt = $this->db->prepare("
                SELECT 
                    c.name as specialty,
                    COUNT(*) as total_count,
                    SUM(CASE WHEN ce.status = 'active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN ce.status = 'inactive' THEN 1 ELSE 0 END) as inactive_count,
                    AVG(r.ratings) as avg_rating,
                    AVG(ce.hourly_rate) as avg_hourly_rate,
                    MIN(ce.hourly_rate) as min_hourly_rate,
                    MAX(ce.hourly_rate) as max_hourly_rate
                FROM company_employees ce
                LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
                WHERE ce.company_id = :company_id {$employmentWhere}
                GROUP BY c.name
                ORDER BY c.name
            ");
            $stmt->execute($params);
            $specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get overall totals
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_employees,
                    SUM(CASE WHEN ce.status = 'active' THEN 1 ELSE 0 END) as active_employees,
                    SUM(CASE WHEN ce.status = 'inactive' THEN 1 ELSE 0 END) as inactive_employees,
                    SUM(CASE WHEN ce.status = 'suspended' THEN 1 ELSE 0 END) as on_leave_employees,
                    AVG(r.ratings) as avg_rating,
                    AVG(ce.hourly_rate) as avg_hourly_rate
                FROM company_employees ce
                LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                WHERE ce.company_id = :company_id {$employmentWhere}
            ");
            $stmt->execute($params);
            $totals = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'specialties' => $specialties,
                'totals' => $totals
            ];
            
        } catch (PDOException $e) {
            error_log("Error fetching statistics: " . $e->getMessage());
            return [
                'specialties' => [],
                'totals' => [
                    'total_employees' => 0,
                    'active_employees' => 0,
                    'inactive_employees' => 0,
                    'on_leave_employees' => 0,
                    'avg_rating' => 0,
                    'avg_hourly_rate' => 0
                ]
            ];
        }
    }

    /**
     * Get staff statistics from staffsummary table if available.
     * Returns null when no summary rows exist for the company.
     */
    private function getStaffSummaryStatistics($companyId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM staffsummary WHERE company_id = :company_id ORDER BY specialty");
            $stmt->execute(['company_id' => $companyId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows || count($rows) === 0) {
                return null;
            }

            $totals = [
                'total_employees' => 0,
                'active_employees' => 0,
                'inactive_employees' => 0,
                'on_leave_employees' => 0,
                'avg_rating' => 0,
                'avg_hourly_rate' => 0
            ];

            $weightedRatingSum = 0.0;
            $weightedRateSum = 0.0;
            $weightTotal = 0.0;

            foreach ($rows as $r) {
                $tc = (int)($r['total_count'] ?? 0);
                $ac = (int)($r['active_count'] ?? 0);
                $ic = (int)($r['inactive_count'] ?? 0);
                $ar = (float)($r['avg_rating'] ?? 0);
                $ahr = (float)($r['avg_hourly_rate'] ?? 0);

                $totals['total_employees'] += $tc;
                $totals['active_employees'] += $ac;
                $totals['inactive_employees'] += $ic;

                if ($tc > 0) {
                    $weightedRatingSum += $ar * $tc;
                    $weightedRateSum += $ahr * $tc;
                    $weightTotal += $tc;
                }
            }

            if ($weightTotal > 0) {
                $totals['avg_rating'] = $weightedRatingSum / $weightTotal;
                $totals['avg_hourly_rate'] = $weightedRateSum / $weightTotal;
            }

            // Normalize rows to match the API shape used by the frontend.
            $specialties = array_map(function ($r) {
                return [
                    'specialty' => $r['specialty'] ?? '',
                    'total_count' => (int)($r['total_count'] ?? 0),
                    'active_count' => (int)($r['active_count'] ?? 0),
                    'inactive_count' => (int)($r['inactive_count'] ?? 0),
                    'avg_rating' => (float)($r['avg_rating'] ?? 0),
                    'avg_hourly_rate' => (float)($r['avg_hourly_rate'] ?? 0),
                    'min_hourly_rate' => (float)($r['min_hourly_rate'] ?? 0),
                    'max_hourly_rate' => (float)($r['max_hourly_rate'] ?? 0),
                ];
            }, $rows);

            return [
                'specialties' => $specialties,
                'totals' => $totals
            ];
        } catch (PDOException $e) {
            error_log("Error fetching staffsummary statistics: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new employee
     */
    public function create($data) {
        try {
            // Re-hire flow: if the same repairer already exists for this company,
            // reactivate/update instead of failing on unique(company_id, repairer_id).
            $existingStmt = $this->db->prepare("SELECT employee_id, status FROM company_employees WHERE company_id = :company_id AND repairer_id = :repairer_id LIMIT 1");
            $existingStmt->execute([
                'company_id' => $data['company_id'],
                'repairer_id' => $data['repairer_id']
            ]);
            $existing = $existingStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if (($existing['status'] ?? '') === 'active') {
                    return [
                        'success' => false,
                        'message' => 'Employee is already active in this company'
                    ];
                }

                $rehireStmt = $this->db->prepare("
                    UPDATE company_employees SET
                        job_title = :job_title,
                        employment_type = :employment_type,
                        status = 'active',
                        hired_date = :hire_date,
                        hourly_rate = :hourly_rate,
                        notes = :notes
                    WHERE employee_id = :employee_id
                ");

                $rehireStmt->execute([
                    'employee_id' => (int)$existing['employee_id'],
                    'job_title' => $data['job_title'] ?? null,
                    'employment_type' => $data['employment_type'] ?? 'freelance',
                    'hire_date' => $data['hire_date'] ?? date('Y-m-d'),
                    'hourly_rate' => $data['hourly_rate'] ?? null,
                    'notes' => $data['notes'] ?? null
                ]);

                $companyId = (int)$data['company_id'];
                $repairerId = (int)$data['repairer_id'];
                $companyName = $this->getCompanyName($companyId);
                $this->notifier->notify(
                    'You are re-hired',
                    "{$companyName} has re-hired you.",
                    'repairer',
                    $repairerId,
                    ['role' => 'company', 'id' => $companyId, 'name' => $companyName]
                );

                return [
                    'success' => true,
                    'employee_id' => (int)$existing['employee_id'],
                    'message' => 'Employee re-hired successfully'
                ];
            }

            $stmt = $this->db->prepare("
                INSERT INTO company_employees (
                    company_id, repairer_id, job_title, employment_type,
                    status, hired_date, hourly_rate, notes
                ) VALUES (
                    :company_id, :repairer_id, :job_title, :employment_type,
                    :status, :hire_date, :hourly_rate, :notes
                )
            ");
            
            $stmt->execute([
                'company_id' => $data['company_id'],
                'repairer_id' => $data['repairer_id'],
                'job_title' => $data['job_title'] ?? null,
                'employment_type' => $data['employment_type'] ?? 'freelance',
                'status' => $data['status'] ?? 'active',
                'hire_date' => $data['hire_date'] ?? date('Y-m-d'),
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);

            $companyId = (int)$data['company_id'];
            $repairerId = (int)$data['repairer_id'];
            $companyName = $this->getCompanyName($companyId);
            $this->notifier->notify(
                'You are hired',
                "{$companyName} has hired you.",
                'repairer',
                $repairerId,
                ['role' => 'company', 'id' => $companyId, 'name' => $companyName]
            );
            
            return [
                'success' => true,
                'employee_id' => $this->db->lastInsertId(),
                'message' => 'Employee created successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Error creating employee: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update employee
     */
    public function update($employeeId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE company_employees SET
                    job_title = :job_title,
                    employment_type = :employment_type,
                    status = :status,
                    hourly_rate = :hourly_rate,
                    notes = :notes
                WHERE employee_id = :employee_id
            ");
            
            $stmt->execute([
                'employee_id' => $employeeId,
                'job_title' => $data['job_title'] ?? null,
                'employment_type' => $data['employment_type'] ?? 'freelance',
                'status' => $data['status'] ?? 'active',
                'hourly_rate' => $data['hourly_rate'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
            
            return [
                'success' => true,
                'message' => 'Employee updated successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Error updating employee: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update employee: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update employee status only
     */
    public function updateStatus($employeeId, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE company_employees 
                SET status = :status 
                WHERE employee_id = :employee_id
            ");
            
            $stmt->execute([
                'employee_id' => $employeeId,
                'status' => $status
            ]);
            
            return [
                'success' => true,
                'message' => 'Employee status updated successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Error updating employee status: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update employee status: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete employee
     */
    public function delete($employeeId, ?string $reason = null) {
        try {
            $this->db->beginTransaction();

            $reason = trim((string)$reason);

            $lookup = $this->db->prepare("SELECT company_id, repairer_id FROM company_employees WHERE employee_id = :employee_id LIMIT 1");
            $lookup->execute(['employee_id' => $employeeId]);
            $row = $lookup->fetch(PDO::FETCH_ASSOC);

            // Soft offboarding: keep worker history/ratings and mark as inactive.
            $stmt = $this->db->prepare("
                UPDATE company_employees
                SET status = 'inactive'
                WHERE employee_id = :employee_id
            ");
            $stmt->execute(['employee_id' => $employeeId]);

            if ($stmt->rowCount() <= 0) {
                    $this->safeRollback();
                return [
                    'success' => false,
                    'message' => 'Employee not found'
                ];
            }

            if ($reason !== '') {
                $reasonStmt = $this->db->prepare("
                    UPDATE company_employees
                    SET notes = TRIM(CONCAT(COALESCE(notes, ''), CASE WHEN COALESCE(notes, '') = '' THEN '' ELSE '\n' END, 'Offboard reason: ', :reason))
                    WHERE employee_id = :employee_id
                ");
                $reasonStmt->execute([
                    'employee_id' => $employeeId,
                    'reason' => $reason
                ]);
            }

            // Remove active project assignments for offboarded staff if the legacy
            // assignment table exists. This is best-effort only; the layoff itself
            // should not fail when the table is absent.
            try {
                $cleanup = $this->db->prepare("
                    DELETE pea FROM project_employee_assignments pea
                    INNER JOIN project p ON p.project_id = pea.project_id
                    WHERE pea.employee_id = :employee_id
                      AND p.status IN ('planned','in_progress','on_hold')
                ");
                $cleanup->execute(['employee_id' => $employeeId]);
            } catch (PDOException $cleanupError) {
                error_log('Skipping project_employee_assignments cleanup: ' . $cleanupError->getMessage());
            }

            if ($row) {
                $companyId = (int)($row['company_id'] ?? 0);
                $repairerId = (int)($row['repairer_id'] ?? 0);
                if ($companyId > 0 && $repairerId > 0) {
                    $companyName = $this->getCompanyName($companyId);
                    $this->notifier->notify(
                        'Employment ended',
                        $reason !== ''
                            ? "{$companyName} has ended your employment. Reason: {$reason}"
                            : "{$companyName} has ended your employment.",
                        'repairer',
                        $repairerId,
                        ['role' => 'company', 'id' => $companyId, 'name' => $companyName]
                    );
                }
            }

            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'Employee offboarded successfully'
            ];
            
        } catch (PDOException $e) {
                $this->safeRollback();
            error_log("Error deleting employee: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Offboard a freelancer recruited via job postings/applications.
     * This targets only freelance/legacy-freelancer roster rows.
     */
    public function offboardFreelancerByRepairer($companyId, $repairerId, ?string $reason = null) {
        $companyId = (int)$companyId;
        $repairerId = (int)$repairerId;

        if ($companyId <= 0 || $repairerId <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid company or repairer id'
            ];
        }

        try {
                        $stmt = $this->db->prepare("
                                SELECT employee_id
                                FROM company_employees
                                WHERE company_id = :company_id
                                    AND repairer_id = :repairer_id
                                    AND status = 'active'
                                    AND (employment_type = 'freelance' OR (job_title IS NOT NULL AND LOWER(job_title) = 'freelancer'))
                                ORDER BY employee_id DESC
                                LIMIT 1
                        ");
            $stmt->execute([
                'company_id' => $companyId,
                'repairer_id' => $repairerId
            ]);

            $employeeId = (int)($stmt->fetchColumn() ?: 0);

            $appStmt = $this->db->prepare("
                SELECT ra.application_id
                FROM repairer_applications ra
                INNER JOIN companyjobpost jp ON jp.posting_id = ra.job_posting_id
                WHERE jp.company_id = :company_id
                  AND ra.repairer_id = :repairer_id
                  AND ra.status = 'approved'
                ORDER BY ra.application_id DESC
                LIMIT 1
            ");
            $appStmt->execute([
                'company_id' => $companyId,
                'repairer_id' => $repairerId
            ]);
            $applicationId = (int)($appStmt->fetchColumn() ?: 0);

            if ($employeeId <= 0 && $applicationId <= 0) {
                return [
                    'success' => false,
                    'message' => 'No active system-hired freelancer found for this repairer'
                ];
            }

            $result = ['success' => true, 'message' => 'Freelancer offboarded successfully'];

            if ($employeeId > 0) {
                $result = $this->delete($employeeId, $reason);
                if (empty($result['success'])) {
                    return $result;
                }
            }

            if ($applicationId > 0) {
                $rejectStmt = $this->db->prepare("
                    UPDATE repairer_applications ra
                    INNER JOIN companyjobpost jp ON jp.posting_id = ra.job_posting_id
                    SET ra.status = 'rejected',
                        ra.rejection_reason = :reason
                    WHERE jp.company_id = :company_id
                      AND ra.repairer_id = :repairer_id
                      AND ra.status = 'approved'
                ");
                $rejectStmt->execute([
                    'company_id' => $companyId,
                    'repairer_id' => $repairerId,
                    'reason' => $reason !== '' ? $reason : 'Offboarded by company'
                ]);
            }

            return $result;
        } catch (PDOException $e) {
            error_log("Error offboarding freelancer: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to offboard freelancer: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Bulk add employees
     */
    public function bulkAdd($companyId, $employees) {
        try {
            $this->db->beginTransaction();
            
            $successCount = 0;
            $errors = [];
            
            foreach ($employees as $index => $employee) {
                $employee['company_id'] = $companyId;
                $result = $this->create($employee);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errors[] = "Row " . ($index + 1) . ": " . $result['message'];
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'added' => $successCount,
                'total' => count($employees),
                'errors' => $errors,
                'message' => "Successfully added {$successCount} out of " . count($employees) . " employees"
            ];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error bulk adding employees: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add employees: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk add staff counts into staffsummary table.
     * Expected input rows may include: specialty, hourly_rate, status.
     */
    public function bulkAddStaffSummary($companyId, $employees) {
        $companyId = (int)$companyId;
        if ($companyId <= 0) {
            return ['success' => false, 'message' => 'Invalid company_id'];
        }

        if (!is_array($employees) || count($employees) === 0) {
            return ['success' => false, 'message' => 'Employees array is required'];
        }

        try {
            $this->db->beginTransaction();

            // Aggregate by specialty.
            $bySpecialty = [];
            foreach ($employees as $idx => $emp) {
                if (!is_array($emp)) {
                    continue;
                }

                $specialty = trim((string)($emp['specialty'] ?? $emp['skillCategory'] ?? ''));
                if ($specialty === '') {
                    throw new InvalidArgumentException('Missing specialty in row ' . ($idx + 1));
                }

                $status = strtolower(trim((string)($emp['status'] ?? 'active')));
                $hourlyRate = isset($emp['hourly_rate']) ? (float)$emp['hourly_rate'] : 0.0;

                if (!isset($bySpecialty[$specialty])) {
                    $bySpecialty[$specialty] = [
                        'total' => 0,
                        'active' => 0,
                        'inactive' => 0,
                        'sum_rates' => 0.0,
                        'min_rate' => null,
                        'max_rate' => null,
                    ];
                }

                $bySpecialty[$specialty]['total'] += 1;
                if ($status === 'inactive') {
                    $bySpecialty[$specialty]['inactive'] += 1;
                } else {
                    $bySpecialty[$specialty]['active'] += 1;
                }

                if ($hourlyRate > 0) {
                    $bySpecialty[$specialty]['sum_rates'] += $hourlyRate;
                    $bySpecialty[$specialty]['min_rate'] = $bySpecialty[$specialty]['min_rate'] === null
                        ? $hourlyRate
                        : min($bySpecialty[$specialty]['min_rate'], $hourlyRate);
                    $bySpecialty[$specialty]['max_rate'] = $bySpecialty[$specialty]['max_rate'] === null
                        ? $hourlyRate
                        : max($bySpecialty[$specialty]['max_rate'], $hourlyRate);
                }
            }

            $stmtSelect = $this->db->prepare(
                "SELECT * FROM staffsummary WHERE company_id = :company_id AND specialty = :specialty FOR UPDATE"
            );
            $stmtInsert = $this->db->prepare(
                "INSERT INTO staffsummary (
                    company_id, specialty, total_count, active_count, inactive_count,
                    avg_rating, avg_hourly_rate, min_hourly_rate, max_hourly_rate
                ) VALUES (
                    :company_id, :specialty, :total_count, :active_count, :inactive_count,
                    0, :avg_hourly_rate, :min_hourly_rate, :max_hourly_rate
                )"
            );
            $stmtUpdate = $this->db->prepare(
                "UPDATE staffsummary SET
                    total_count = :total_count,
                    active_count = :active_count,
                    inactive_count = :inactive_count,
                    avg_hourly_rate = :avg_hourly_rate,
                    min_hourly_rate = :min_hourly_rate,
                    max_hourly_rate = :max_hourly_rate
                 WHERE company_id = :company_id AND specialty = :specialty"
            );

            $addedTotal = 0;
            foreach ($bySpecialty as $specialty => $agg) {
                $addedTotal += (int)$agg['total'];

                $stmtSelect->execute(['company_id' => $companyId, 'specialty' => $specialty]);
                $existing = $stmtSelect->fetch(PDO::FETCH_ASSOC);

                $addTotal = (int)$agg['total'];
                $addActive = (int)$agg['active'];
                $addInactive = (int)$agg['inactive'];
                $sumRates = (float)$agg['sum_rates'];
                $minRate = $agg['min_rate'] === null ? 0.0 : (float)$agg['min_rate'];
                $maxRate = $agg['max_rate'] === null ? 0.0 : (float)$agg['max_rate'];

                if (!$existing) {
                    $avgRate = $addTotal > 0 ? ($sumRates / max(1, $addTotal)) : 0.0;
                    $stmtInsert->execute([
                        'company_id' => $companyId,
                        'specialty' => $specialty,
                        'total_count' => $addTotal,
                        'active_count' => $addActive,
                        'inactive_count' => $addInactive,
                        'avg_hourly_rate' => $avgRate,
                        'min_hourly_rate' => $minRate,
                        'max_hourly_rate' => $maxRate,
                    ]);
                    continue;
                }

                $oldTotal = (int)($existing['total_count'] ?? 0);
                $oldActive = (int)($existing['active_count'] ?? 0);
                $oldInactive = (int)($existing['inactive_count'] ?? 0);
                $oldAvgRate = (float)($existing['avg_hourly_rate'] ?? 0);
                $oldMinRate = (float)($existing['min_hourly_rate'] ?? 0);
                $oldMaxRate = (float)($existing['max_hourly_rate'] ?? 0);

                $newTotal = $oldTotal + $addTotal;
                $newActive = $oldActive + $addActive;
                $newInactive = $oldInactive + $addInactive;

                // Weighted average by headcount.
                $oldRateSum = $oldAvgRate * $oldTotal;
                $newAvgRate = $newTotal > 0 ? (($oldRateSum + $sumRates) / $newTotal) : 0.0;

                $newMinRate = $oldMinRate;
                if ($newMinRate <= 0 && $minRate > 0) {
                    $newMinRate = $minRate;
                } elseif ($minRate > 0) {
                    $newMinRate = min($newMinRate, $minRate);
                }

                $newMaxRate = $oldMaxRate;
                if ($maxRate > 0) {
                    $newMaxRate = max($newMaxRate, $maxRate);
                }

                $stmtUpdate->execute([
                    'company_id' => $companyId,
                    'specialty' => $specialty,
                    'total_count' => $newTotal,
                    'active_count' => $newActive,
                    'inactive_count' => $newInactive,
                    'avg_hourly_rate' => $newAvgRate,
                    'min_hourly_rate' => $newMinRate,
                    'max_hourly_rate' => $newMaxRate,
                ]);
            }

            $this->db->commit();

            return [
                'success' => true,
                'added' => $addedTotal,
                'total' => count($employees),
                'message' => "Successfully added {$addedTotal} staff members"
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error bulk adding staffsummary: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to add staff: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reduce staff counts in staffsummary.
     * Accepts array entries with either {specialty, quantity} or {skillCategory, reductionQuantity}.
     */
    public function reduceStaffSummary($companyId, $reductions) {
        $companyId = (int)$companyId;
        if ($companyId <= 0) {
            return ['success' => false, 'message' => 'Invalid company_id'];
        }

        if (!is_array($reductions) || count($reductions) === 0) {
            return ['success' => false, 'message' => 'Reductions array is required'];
        }

        try {
            $this->db->beginTransaction();

            $stmtSelect = $this->db->prepare(
                "SELECT * FROM staffsummary WHERE company_id = :company_id AND specialty = :specialty FOR UPDATE"
            );
            $stmtUpdate = $this->db->prepare(
                "UPDATE staffsummary SET
                    total_count = :total_count,
                    active_count = :active_count,
                    inactive_count = :inactive_count,
                    avg_hourly_rate = :avg_hourly_rate,
                    min_hourly_rate = :min_hourly_rate,
                    max_hourly_rate = :max_hourly_rate
                 WHERE company_id = :company_id AND specialty = :specialty"
            );

            $reducedTotal = 0;
            foreach ($reductions as $idx => $r) {
                if (!is_array($r)) {
                    continue;
                }

                $specialty = trim((string)($r['specialty'] ?? $r['skillCategory'] ?? ''));
                $qty = (int)($r['quantity'] ?? $r['reductionQuantity'] ?? 0);

                if ($specialty === '' || $qty <= 0) {
                    throw new InvalidArgumentException('Invalid reduction entry at row ' . ($idx + 1));
                }

                $stmtSelect->execute(['company_id' => $companyId, 'specialty' => $specialty]);
                $existing = $stmtSelect->fetch(PDO::FETCH_ASSOC);
                if (!$existing) {
                    throw new RuntimeException("No staff found for specialty '{$specialty}'");
                }

                $oldTotal = (int)($existing['total_count'] ?? 0);
                $oldActive = (int)($existing['active_count'] ?? 0);
                $oldInactive = (int)($existing['inactive_count'] ?? 0);
                $oldAvgRate = (float)($existing['avg_hourly_rate'] ?? 0);
                $oldMinRate = (float)($existing['min_hourly_rate'] ?? 0);
                $oldMaxRate = (float)($existing['max_hourly_rate'] ?? 0);

                if ($qty > $oldTotal) {
                    throw new RuntimeException("Cannot reduce {$qty}; only {$oldTotal} available in '{$specialty}'");
                }

                $newTotal = $oldTotal - $qty;

                // Reduce active first (best-effort), then inactive.
                $activeReduced = min($oldActive, $qty);
                $remainingToReduce = $qty - $activeReduced;
                $inactiveReduced = min($oldInactive, $remainingToReduce);

                $newActive = $oldActive - $activeReduced;
                $newInactive = $oldInactive - $inactiveReduced;

                // Keep rate stats as-is unless the row becomes empty.
                $newAvgRate = $oldAvgRate;
                $newMinRate = $oldMinRate;
                $newMaxRate = $oldMaxRate;
                if ($newTotal <= 0) {
                    $newAvgRate = 0.0;
                    $newMinRate = 0.0;
                    $newMaxRate = 0.0;
                }

                $stmtUpdate->execute([
                    'company_id' => $companyId,
                    'specialty' => $specialty,
                    'total_count' => $newTotal,
                    'active_count' => $newActive,
                    'inactive_count' => $newInactive,
                    'avg_hourly_rate' => $newAvgRate,
                    'min_hourly_rate' => $newMinRate,
                    'max_hourly_rate' => $newMaxRate,
                ]);

                $reducedTotal += $qty;
            }

            $this->db->commit();
            return [
                'success' => true,
                'reduced' => $reducedTotal,
                'message' => "Successfully reduced {$reducedTotal} staff members"
            ];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error reducing staffsummary: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to reduce staff: ' . $e->getMessage()
            ];
        }
    }
}
