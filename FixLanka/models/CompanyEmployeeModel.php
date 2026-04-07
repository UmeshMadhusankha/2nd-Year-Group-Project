<?php
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
    
    public function __construct($database) {
        $this->db = $database;
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
        
        return $employee;
    }
    
    /**
     * Get all employees for a company with optional filters
     */
    public function getAll($companyId, $filters = []) {
        try {
                 $query = "SELECT ce.*, r.f_name as first_name, r.l_name as last_name, r.email, r.phoneNumber as phone,
                         c.name as specialty, r.experience_years, r.ratings as rating,
                         r.hourly_rate as applicant_hourly_rate, r.profile_picture as profile_photo
                      FROM company_employees ce
                      LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                      LEFT JOIN category c ON r.category_id = c.category_id
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
                       r.hourly_rate as applicant_hourly_rate, r.profile_picture as profile_photo
                FROM company_employees ce
                LEFT JOIN repairer r ON ce.repairer_id = r.repairer_id
                LEFT JOIN category c ON r.category_id = c.category_id
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
     * Create new employee
     */
    public function create($data) {
        try {
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
    public function delete($employeeId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM company_employees WHERE employee_id = :employee_id");
            $stmt->execute(['employee_id' => $employeeId]);
            
            return [
                'success' => true,
                'message' => 'Employee deleted successfully'
            ];
            
        } catch (PDOException $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
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
}
