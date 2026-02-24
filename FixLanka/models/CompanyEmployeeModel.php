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
            $query = "SELECT * FROM CompanyEmployee WHERE company_id = :company_id";
            $params = ['company_id' => $companyId];
            
            // Filter by specialty
            if (!empty($filters['specialty'])) {
                $query .= " AND specialty = :specialty";
                $params['specialty'] = $filters['specialty'];
            }
            
            // Filter by status
            if (!empty($filters['status'])) {
                $query .= " AND status = :status";
                $params['status'] = $filters['status'];
            }
            
            // Search by name
            if (!empty($filters['search'])) {
                $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
                $params['search'] = '%' . $filters['search'] . '%';
            }
            
            // Order by
            $orderBy = $filters['order_by'] ?? 'created_at';
            $orderDir = $filters['order_dir'] ?? 'DESC';
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
            $stmt = $this->db->prepare("SELECT * FROM CompanyEmployee WHERE employee_id = :employee_id");
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
                SELECT * FROM CompanyEmployee 
                WHERE company_id = :company_id AND specialty = :specialty
                ORDER BY rating DESC
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
    public function getStatistics($companyId) {
        try {
            // Get summary by specialty
            $stmt = $this->db->prepare("
                SELECT 
                    specialty,
                    total_count,
                    active_count,
                    inactive_count,
                    avg_rating,
                    avg_hourly_rate,
                    min_hourly_rate,
                    max_hourly_rate
                FROM StaffSummary
                WHERE company_id = :company_id
                ORDER BY specialty
            ");
            $stmt->execute(['company_id' => $companyId]);
            $specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get overall totals
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_employees,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_employees,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_employees,
                    SUM(CASE WHEN status = 'on_leave' THEN 1 ELSE 0 END) as on_leave_employees,
                    AVG(rating) as avg_rating,
                    AVG(hourly_rate) as avg_hourly_rate
                FROM CompanyEmployee
                WHERE company_id = :company_id
            ");
            $stmt->execute(['company_id' => $companyId]);
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
                INSERT INTO CompanyEmployee (
                    company_id, repairer_id, first_name, last_name, email, phone,
                    specialty, hourly_rate, rating, status, hire_date, 
                    experience_years, certification_details, profile_photo
                ) VALUES (
                    :company_id, :repairer_id, :first_name, :last_name, :email, :phone,
                    :specialty, :hourly_rate, :rating, :status, :hire_date,
                    :experience_years, :certification_details, :profile_photo
                )
            ");
            
            $stmt->execute([
                'company_id' => $data['company_id'],
                'repairer_id' => $data['repairer_id'] ?? null,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'specialty' => $data['specialty'],
                'hourly_rate' => $data['hourly_rate'] ?? 0,
                'rating' => $data['rating'] ?? 0,
                'status' => $data['status'] ?? 'active',
                'hire_date' => $data['hire_date'] ?? date('Y-m-d'),
                'experience_years' => $data['experience_years'] ?? 0,
                'certification_details' => $data['certification_details'] ?? null,
                'profile_photo' => $data['profile_photo'] ?? null
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
                UPDATE CompanyEmployee SET
                    first_name = :first_name,
                    last_name = :last_name,
                    email = :email,
                    phone = :phone,
                    specialty = :specialty,
                    hourly_rate = :hourly_rate,
                    rating = :rating,
                    status = :status,
                    experience_years = :experience_years,
                    certification_details = :certification_details,
                    profile_photo = :profile_photo
                WHERE employee_id = :employee_id
            ");
            
            $stmt->execute([
                'employee_id' => $employeeId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'specialty' => $data['specialty'],
                'hourly_rate' => $data['hourly_rate'] ?? 0,
                'rating' => $data['rating'] ?? 0,
                'status' => $data['status'] ?? 'active',
                'experience_years' => $data['experience_years'] ?? 0,
                'certification_details' => $data['certification_details'] ?? null,
                'profile_photo' => $data['profile_photo'] ?? null
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
                UPDATE CompanyEmployee 
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
            $stmt = $this->db->prepare("DELETE FROM CompanyEmployee WHERE employee_id = :employee_id");
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
