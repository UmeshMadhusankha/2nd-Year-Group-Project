<?php
/**
 * Company Quotation Model
 * 
 * Handles all CRUD operations for company quotations submitted to job requests.
 * This model interacts with the CompanyQuotation table in the database.
 * 
 * @package FixLanka\Models
 * @version 1.0.0
 */

class CompanyQuotation
{
    /**
     * PDO database connection instance
     * @var PDO
     */
    private $pdo;

    /**
     * Status constants for quotations
     */
    private const STATUS_PENDING = 'pending';
    private const STATUS_ACCEPTED = 'accepted';
    private const STATUS_REJECTED = 'rejected';
    private const STATUS_SUCCESSFUL = 'successful';

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
     * Create a new company quotation
     * 
     * Inserts a new quotation record into the database. All cost values must be provided.
     * The status defaults to 'pending' if not specified.
     * 
     * @param array $data Quotation data with the following keys:
     *                    - request_id (int): ID of the job request
     *                    - user_id (int): ID of the company submitting the quotation
     *                    - title (string): Quotation title
     *                    - description (string|null): Optional detailed description
     *                    - labor_cost (float): Cost for labor
     *                    - material_cost (float): Cost for materials
     *                    - transport_cost (float): Optional transport cost
     *                    - other_charges (float): Optional other charges
     *                    - total_amount (float): Total quotation amount
     *                    - start_date (date): Estimated start date
     *                    - completion_date (date): Estimated completion date
     *                    - estimated_duration (int): Duration in days
     *                    - payment_terms (string|null): Optional payment terms
     *                    - warranty_period (string|null): Optional warranty period
     *                    - additional_terms (string|null): Optional additional terms
     * 
     * @return int The ID of the newly created quotation
     * @throws Exception If database insertion fails
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO companyquotation (
                request_id, user_id, title, description,
                labor_cost, material_cost, transport_cost, other_charges,
                total_amount, start_date, completion_date, estimated_duration,
                payment_terms, warranty_period, additional_terms, labor_unit_label, material_unit_label, status
            ) VALUES (
                :request_id, :user_id, :title, :description,
                :labor_cost, :material_cost, :transport_cost, :other_charges,
                :total_amount, :start_date, :completion_date, :estimated_duration,
                :payment_terms, :warranty_period, :additional_terms, :labor_unit_label, :material_unit_label, :status
            )";

            $stmt = $this->pdo->prepare($sql);

            $status = $data['status'] ?? self::STATUS_PENDING;

            $stmt->execute([
                ':request_id'        => $data['request_id'],
                ':user_id'           => $data['user_id'],
                ':title'             => $data['title'],
                ':description'       => $data['description'] ?? null,
                ':labor_cost'        => $data['labor_cost'],
                ':material_cost'     => $data['material_cost'],
                ':transport_cost'    => $data['transport_cost'] ?? 0.00,
                ':other_charges'     => $data['other_charges'] ?? 0.00,
                ':total_amount'      => $data['total_amount'],
                ':start_date'        => $data['start_date'],
                ':completion_date'   => $data['completion_date'],
                ':estimated_duration'=> $data['estimated_duration'],
                ':payment_terms'     => $data['payment_terms'] ?? null,
                ':warranty_period'   => $data['warranty_period'] ?? null,
                ':additional_terms'  => $data['additional_terms'] ?? null,
                ':labor_unit_label'  => $data['labor_unit_label'] ?? null,
                ':material_unit_label'=> $data['material_unit_label'] ?? null,
                ':status'            => $status
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error creating company quotation: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    
    /**
     * Retrieve quotations with optional filtering
     * 
     * Fetches quotation records with related job request and customer information.
     * Results are joined with JobRequest, User, and Category tables for complete data.
     * 
     * @param array $filters Optional filters:
     *                       - quotation_id (int): Filter by specific quotation ID
     *                       - request_id (int): Filter by job request ID
     *                       - user_id (int): Filter by company user ID
     *                       - status (string): Filter by quotation status
     * 
     * @return array Array of quotation records with related data
     */
    public function getAll($filters = [])
    {
        try {
            $sql = "SELECT 
                cq.*,
                jr.title as job_title,
                jr.description as job_description,
                jr.district,
                jr.address,
                jr.finish_date,
                jr.urgency,
                jr.service_provider_type,
                c.name as category_name,
                u.f_name as customer_fname,
                u.l_name as customer_lname,
                u.email as customer_email,
                u.address as customer_address,
                NULL as customer_phone,
                COALESCE(cmp.name, CONCAT(u.f_name, ' ', u.l_name)) as company_name,
                COALESCE(cmp.address, uc.address) as company_address,
                cmp.registration_no as company_registration_no,
                cmp.contact_no as company_phone,
                ct.contract_id as contract_id,
                ct.sent_to_customer as contract_sent_to_customer,
                ct.sent_at as contract_sent_at,
                ct.status as contract_status,
                ct.customer_response as contract_customer_response
            FROM companyquotation cq
            INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
            INNER JOIN user u ON jr.user_id = u.user_id
            LEFT JOIN user uc ON uc.user_id = COALESCE(cq.company_id, cq.user_id)
            LEFT JOIN company cmp ON cmp.company_id = COALESCE(cq.company_id, cq.user_id)
            LEFT JOIN category c ON jr.category_id = c.category_id
            LEFT JOIN contract ct ON ct.quotation_id = cq.quotation_id
            WHERE 1=1";

            $params = [];

            // Apply filters dynamically
            if (!empty($filters['quotation_id'])) {
                $sql .= " AND cq.quotation_id = :quotation_id";
                $params[':quotation_id'] = $filters['quotation_id'];
            }

            if (!empty($filters['request_id'])) {
                $sql .= " AND cq.request_id = :request_id";
                $params[':request_id'] = $filters['request_id'];
            }

            if (!empty($filters['user_id'])) {
                $sql .= " AND cq.company_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }

            if (!empty($filters['status'])) {
                $sql .= " AND cq.status = :status";
                $params[':status'] = $filters['status'];
            }

            $sql .= " ORDER BY cq.created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error fetching company quotations: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Retrieve a single quotation by ID
     * 
     * Fetches a specific quotation with all related job request and customer information.
     * 
     * @param int $quotationId The ID of the quotation to retrieve
     * @return array|false Quotation data array if found, false if not found or on error
     */
    public function getById($quotationId)
    {
        try {
            $sql = "SELECT 
                cq.*,
                jr.title as job_title,
                jr.description as job_description,
                jr.district,
                jr.address,
                jr.finish_date,
                jr.urgency,
                jr.service_provider_type,
                c.name as category_name,
                u.f_name as customer_fname,
                u.l_name as customer_lname,
                u.email as customer_email,
                ct.contract_id as contract_id,
                ct.sent_to_customer as contract_sent_to_customer,
                ct.sent_at as contract_sent_at,
                ct.status as contract_status,
                ct.customer_response as contract_customer_response
            FROM companyquotation cq
            INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
            INNER JOIN user u ON jr.user_id = u.user_id
            LEFT JOIN category c ON jr.category_id = c.category_id
            LEFT JOIN contract ct ON ct.quotation_id = cq.quotation_id
            WHERE cq.quotation_id = :quotation_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':quotation_id' => $quotationId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error fetching quotation by ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update an existing quotation
     * 
     * Updates quotation details. Only quotations with 'pending' status can be updated.
     * Validates that the quotation exists and is editable before performing the update.
     * 
     * @param int $quotationId The ID of the quotation to update
     * @param array $data Updated quotation data (same structure as create method)
     * @return bool True if update successful, false otherwise
     */
    public function update($quotationId, $data)
    {
        try {
            // Verify quotation exists and is in pending status
            $existing = $this->getById($quotationId);
            if (!$existing || $existing['status'] !== self::STATUS_PENDING) {
                return false;
            }

            $sql = "UPDATE CompanyQuotation SET
                title = :title,
                description = :description,
                labor_cost = :labor_cost,
                material_cost = :material_cost,
                transport_cost = :transport_cost,
                other_charges = :other_charges,
                total_amount = :total_amount,
                start_date = :start_date,
                completion_date = :completion_date,
                estimated_duration = :estimated_duration,
                payment_terms = :payment_terms,
                warranty_period = :warranty_period,
                additional_terms = :additional_terms,
                labor_unit_label = :labor_unit_label,
                material_unit_label = :material_unit_label
            WHERE quotation_id = :quotation_id AND status = :status";

            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute([
                ':quotation_id' => $quotationId,
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':labor_cost' => $data['labor_cost'],
                ':material_cost' => $data['material_cost'],
                ':transport_cost' => $data['transport_cost'] ?? 0.00,
                ':other_charges' => $data['other_charges'] ?? 0.00,
                ':total_amount' => $data['total_amount'],
                ':start_date' => $data['start_date'],
                ':completion_date' => $data['completion_date'],
                ':estimated_duration' => $data['estimated_duration'],
                ':payment_terms' => $data['payment_terms'] ?? null,
                ':warranty_period' => $data['warranty_period'] ?? null,
                ':additional_terms' => $data['additional_terms'] ?? null,
                ':labor_unit_label' => $data['labor_unit_label'] ?? null,
                ':material_unit_label' => $data['material_unit_label'] ?? null,
                ':status' => self::STATUS_PENDING
            ]);

        } catch (PDOException $e) {
            error_log("Error updating quotation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a quotation
     * 
     * Removes a quotation from the database. Only quotations with 'pending' status
     * can be deleted to prevent removal of accepted or processed quotations.
     * 
     * @param int $quotationId The ID of the quotation to delete
     * @return bool True if deletion successful, false if quotation not found or not deletable
     */
    public function delete($quotationId)
    {
        try {
            $sql = "DELETE FROM companyquotation 
                    WHERE quotation_id = :quotation_id 
                    AND status = :status";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':quotation_id' => $quotationId,
                ':status' => self::STATUS_PENDING
            ]);

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("Error deleting quotation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a quotation already exists for a specific job request
     * 
     * Prevents duplicate quotations for the same job request.
     * Only checks for pending quotations.
     * 
     * @param int $requestId The job request ID to check
     * @return bool True if a pending quotation exists, false otherwise
     */
    public function hasQuotationForRequest($requestId)
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM companyquotation 
                    WHERE request_id = :request_id 
                    AND status = :status";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':request_id' => $requestId,
                ':status' => self::STATUS_PENDING
            ]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("Error checking existing quotation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update the status of a quotation
     * 
     * Changes the status of a quotation (e.g., from pending to accepted).
     * Validates that the new status is one of the allowed values.
     * 
     * @param int $quotationId The ID of the quotation to update
     * @param string $status New status (pending, accepted, rejected, successful)
     * @return bool True if status update successful, false otherwise
     */
    public function updateStatus($quotationId, $status)
    {
        try {
            // Validate status value
            $validStatuses = [
                self::STATUS_PENDING,
                self::STATUS_ACCEPTED,
                self::STATUS_REJECTED,
                self::STATUS_SUCCESSFUL
            ];

            if (!in_array($status, $validStatuses)) {
                return false;
            }

            $sql = "UPDATE companyquotation 
                    SET status = :status 
                    WHERE quotation_id = :quotation_id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':quotation_id' => $quotationId,
                ':status' => $status
            ]);

        } catch (PDOException $e) {
            error_log("Error updating quotation status: " . $e->getMessage());
            return false;
        }
    }

    // ========================================================================
    // BUSINESS LOGIC ENHANCEMENT - New Methods (Added for Phase 1)
    // These methods extend functionality without modifying existing features
    // ========================================================================

    /**
     * Create a new enhanced quotation with business logic fields
     * 
     * This method extends the standard create() method by adding support for:
     * - Budget flexibility (fixed or flexible with min/max range)
     * - Advanced payment methods (milestone, 50-50, 30-70, upfront, time & material)
     * - Pricing types (fixed price, time-based, hybrid)
     * - Hourly rates and spending cap multipliers
     * 
     * @param array $data Quotation data including all standard fields PLUS:
     *                    - budget_type (string): 'fixed' or 'flexible'
     *                    - payment_method (string): Payment structure
     *                    - pricing_type (string): Pricing model
     *                    - hourly_rate (float|null): Rate per hour for time-based pricing
     *                    - spending_cap_multiplier (float): Default 1.5
     * @return int|false Quotation ID on success, false on failure
     */
    public function createEnhanced($data)
    {
        try {
            // Fetch the customer's user_id from the job request
            // The $data['user_id'] passed from the frontend is actually the logged-in company's ID!
            $stmtUser = $this->pdo->prepare("SELECT user_id FROM jobrequest WHERE request_id = ?");
            $stmtUser->execute([$data['request_id']]);
            $customerUserId = $stmtUser->fetchColumn();
            
            if (!$customerUserId) {
                error_log("Error creating enhanced quotation: Could not find requested job.");
                return false;
            }
            
            $companyId = $data['user_id']; // This is the company ID passed from the frontend/session

            // Calculate budget range if flexible
            $budgetMin = null;
            $budgetMax = null;
            
            if (isset($data['budget_type']) && $data['budget_type'] === 'flexible') {
                $range = $this->calculateBudgetRange($data['total_amount']);
                $budgetMin = $range['min'];
                $budgetMax = $range['max'];
            }

            $sql = "INSERT INTO companyquotation (
                        request_id, company_id, user_id, title, description,
                        labor_cost, material_cost, transport_cost, other_charges, total_amount,
                        budget_type, budget_min, budget_max,
                        start_date, completion_date, estimated_duration,
                        payment_terms, payment_method, pricing_type, hourly_rate, spending_cap_multiplier,
                        work_schedule_type, working_days_per_week, daily_work_hours,
                        work_start_time, work_end_time, custom_schedule_json,
                        labor_unit_label, material_unit_label,
                        warranty_period, additional_terms, status
                    ) VALUES (
                        :request_id, :company_id, :user_id, :title, :description,
                        :labor_cost, :material_cost, :transport_cost, :other_charges, :total_amount,
                        :budget_type, :budget_min, :budget_max,
                        :start_date, :completion_date, :estimated_duration,
                        :payment_terms, :payment_method, :pricing_type, :hourly_rate, :spending_cap_multiplier,
                        :work_schedule_type, :working_days_per_week, :daily_work_hours,
                        :work_start_time, :work_end_time, :custom_schedule_json,
                        :labor_unit_label, :material_unit_label,
                        :warranty_period, :additional_terms, :status
                    )";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':request_id' => $data['request_id'],
                ':company_id' => $companyId,
                ':user_id' => $customerUserId,
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':labor_cost' => $data['labor_cost'],
                ':material_cost' => $data['material_cost'],
                ':transport_cost' => $data['transport_cost'] ?? 0,
                ':other_charges' => $data['other_charges'] ?? 0,
                ':total_amount' => $data['total_amount'],
                ':budget_type' => $data['budget_type'] ?? 'fixed',
                ':budget_min' => $budgetMin,
                ':budget_max' => $budgetMax,
                ':start_date' => $data['start_date'],
                ':completion_date' => $data['completion_date'],
                ':estimated_duration' => $data['estimated_duration'],
                ':payment_terms' => $data['payment_terms'] ?? null,
                ':payment_method' => match($data['payment_method'] ?? 'milestone') {
                    'upfront_final' => 'full_upfront',
                    'milestone' => 'milestone_based',
                    '50-50' => '50_50',
                    '30-70' => '30_70',
                    'completion' => 'completion',
                    'time_material' => 'time_and_material',
                    default => 'milestone_based'
                },
                ':pricing_type' => match($data['pricing_type'] ?? 'fixed_price') {
                    'fixed_price' => 'fixed_price',
                    'time_based' => 'time_and_material',
                    'hybrid' => 'fixed_price', // hybrid is technically a fixed_price with unit multipliers in this schema
                    default => 'fixed_price'
                },
                ':hourly_rate' => $data['hourly_rate'] ?? null,
                ':spending_cap_multiplier' => $data['spending_cap_multiplier'] ?? 1.5,
                ':work_schedule_type' => $data['work_schedule_type'] ?? 'weekdays_only',
                ':working_days_per_week' => $data['working_days_per_week'] ?? 5,
                ':daily_work_hours' => $data['daily_work_hours'] ?? 8.00,
                ':work_start_time' => $data['work_start_time'] ?? '08:00:00',
                ':work_end_time' => $data['work_end_time'] ?? '17:00:00',
                ':custom_schedule_json' => $data['custom_schedule_json'] ?? $data['custom_schedule_details'] ?? null,
                ':labor_unit_label' => $data['labor_unit_label'] ?? null,
                ':material_unit_label' => $data['material_unit_label'] ?? null,
                ':warranty_period' => $data['warranty_period'] ?? null,
                ':additional_terms' => $data['additional_terms'] ?? null,
                ':status' => $data['status'] ?? self::STATUS_PENDING
            ]);

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            $errorMsg = "Error creating enhanced quotation: " . $e->getMessage();
            error_log($errorMsg);
            file_put_contents(__DIR__ . '/../api/sql_error.txt', $errorMsg . "\n" . print_r($data, true));
            return false;
        }
    }

    /**
     * Calculate budget range for flexible pricing
     * 
     * Calculates minimum (-10%) and maximum (+10%) budget bounds
     * for flexible budget quotations.
     * 
     * @param float $baseAmount The base quotation amount
     * @return array Array with 'min' and 'max' keys
     */
    public function calculateBudgetRange($baseAmount)
    {
        return [
            'min' => round($baseAmount * 0.90, 2),
            'max' => round($baseAmount * 1.10, 2)
        ];
    }

    /**
     * Get enhanced quotation by ID with business logic fields
     * 
     * Retrieves a quotation with all standard and business logic fields,
     * including calculated budget range text for display.
     * 
     * @param int $quotationId The quotation ID
     * @return array|false Quotation data or false if not found
     */
    public function getEnhancedById($quotationId)
    {
        try {
            $sql = "SELECT cq.*, jr.title as job_title, jr.district
                    FROM companyquotation cq
                    INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                    WHERE cq.quotation_id = :quotation_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':quotation_id' => $quotationId]);
            
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($quotation && $quotation['budget_type'] === 'flexible') {
                $quotation['budget_range_text'] = 'LKR ' . number_format($quotation['budget_min'], 2) . 
                                                  ' - LKR ' . number_format($quotation['budget_max'], 2);
            }
            
            return $quotation;

        } catch (PDOException $e) {
            error_log("Error fetching enhanced quotation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update enhanced quotation with business logic fields
     * 
     * Updates an existing quotation with business logic fields.
     * Only allows updates to quotations with 'pending' status.
     * 
     * @param int $quotationId The quotation ID to update
     * @param array $data Updated quotation data
     * @return bool True on success, false on failure
     */
    public function updateEnhanced($quotationId, $data)
    {
        try {
            // First check if quotation exists and is pending
            $current = $this->getById($quotationId);
            if (!$current || $current['status'] !== self::STATUS_PENDING) {
                return false;
            }

            // Calculate budget range if flexible
            $budgetMin = $data['budget_min'] ?? null;
            $budgetMax = $data['budget_max'] ?? null;
            
            if (isset($data['budget_type']) && $data['budget_type'] === 'flexible' && isset($data['total_amount'])) {
                $range = $this->calculateBudgetRange($data['total_amount']);
                $budgetMin = $range['min'];
                $budgetMax = $range['max'];
            }

            $sql = "UPDATE companyquotation 
                    SET title = :title,
                        description = :description,
                        labor_cost = :labor_cost,
                        material_cost = :material_cost,
                        transport_cost = :transport_cost,
                        other_charges = :other_charges,
                        total_amount = :total_amount,
                        budget_type = :budget_type,
                        budget_min = :budget_min,
                        budget_max = :budget_max,
                        start_date = :start_date,
                        completion_date = :completion_date,
                        estimated_duration = :estimated_duration,
                        payment_terms = :payment_terms,
                        payment_method = :payment_method,
                        pricing_type = :pricing_type,
                        hourly_rate = :hourly_rate,
                        spending_cap_multiplier = :spending_cap_multiplier,
                        work_schedule_type = :work_schedule_type,
                        working_days_per_week = :working_days_per_week,
                        daily_work_hours = :daily_work_hours,
                        work_start_time = :work_start_time,
                        work_end_time = :work_end_time,
                        custom_schedule_json = :custom_schedule_json,
                        labor_unit_label = :labor_unit_label,
                        material_unit_label = :material_unit_label,
                        warranty_period = :warranty_period,
                        additional_terms = :additional_terms
                    WHERE quotation_id = :quotation_id";

            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':quotation_id' => $quotationId,
                ':title' => $data['title'],
                ':description' => $data['description'] ?? null,
                ':labor_cost' => $data['labor_cost'],
                ':material_cost' => $data['material_cost'],
                ':transport_cost' => $data['transport_cost'] ?? 0,
                ':other_charges' => $data['other_charges'] ?? 0,
                ':total_amount' => $data['total_amount'],
                ':budget_type' => $data['budget_type'] ?? 'fixed',
                ':budget_min' => $budgetMin,
                ':budget_max' => $budgetMax,
                ':start_date' => $data['start_date'],
                ':completion_date' => $data['completion_date'],
                ':estimated_duration' => $data['estimated_duration'],
                ':payment_terms' => $data['payment_terms'] ?? null,
                ':payment_method' => $data['payment_method'] ?? 'milestone',
                ':pricing_type' => $data['pricing_type'] ?? 'fixed_price',
                ':hourly_rate' => $data['hourly_rate'] ?? null,
                ':spending_cap_multiplier' => $data['spending_cap_multiplier'] ?? 1.5,
                ':work_schedule_type' => $data['work_schedule_type'] ?? 'weekdays_only',
                ':working_days_per_week' => $data['working_days_per_week'] ?? 5,
                ':daily_work_hours' => $data['daily_work_hours'] ?? 8.00,
                ':work_start_time' => $data['work_start_time'] ?? '08:00:00',
                ':work_end_time' => $data['work_end_time'] ?? '17:00:00',
                ':custom_schedule_json' => $data['custom_schedule_json'] ?? $data['custom_schedule_details'] ?? null,
                ':labor_unit_label' => $data['labor_unit_label'] ?? null,
                ':material_unit_label' => $data['material_unit_label'] ?? null,
                ':warranty_period' => $data['warranty_period'] ?? null,
                ':additional_terms' => $data['additional_terms'] ?? null
            ]);

        } catch (PDOException $e) {
            error_log("Error updating enhanced quotation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate total work hours based on schedule
     */
    public function calculateTotalWorkHours($estimatedDuration, $dailyWorkHours, $workingDaysPerWeek) {
        // Calendar days to work days conversion
        $weeksNeeded = ceil($estimatedDuration / 7);
        $totalWorkDays = $weeksNeeded * $workingDaysPerWeek;
        
        // Total hours
        $totalHours = $totalWorkDays * $dailyWorkHours;
        
        return round($totalHours, 2);
    }

    /**
     * Validate work schedule data
     */
    public function validateWorkSchedule($data) {
        $errors = [];
        
        // Validate working days per week
        if (isset($data['working_days_per_week'])) {
            $days = (int)$data['working_days_per_week'];
            if ($days < 1 || $days > 7) {
                $errors[] = 'Working days per week must be between 1 and 7';
            }
        }
        
        // Validate daily work hours
        if (isset($data['daily_work_hours'])) {
            $hours = (float)$data['daily_work_hours'];
            if ($hours <= 0 || $hours > 24) {
                $errors[] = 'Daily work hours must be between 0 and 24';
            }
        }
        
        // Validate time range
        if (isset($data['work_start_time']) && isset($data['work_end_time'])) {
            $start = strtotime($data['work_start_time']);
            $end = strtotime($data['work_end_time']);
            if ($start >= $end) {
                $errors[] = 'Work end time must be after start time';
            }
        }
        
        return empty($errors) ? true : $errors;
    }
}
