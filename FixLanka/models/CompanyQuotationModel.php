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
            $sql = "INSERT INTO CompanyQuotation (
                request_id, user_id, title, description,
                labor_cost, material_cost, transport_cost, other_charges,
                total_amount, start_date, completion_date, estimated_duration,
                payment_terms, warranty_period, additional_terms, status
            ) VALUES (
                :request_id, :user_id, :title, :description,
                :labor_cost, :material_cost, :transport_cost, :other_charges,
                :total_amount, :start_date, :completion_date, :estimated_duration,
                :payment_terms, :warranty_period, :additional_terms, :status
            )";

            $stmt = $this->pdo->prepare($sql);

            // Set default status if not provided
            $status = $data['status'] ?? self::STATUS_PENDING;

            $stmt->execute([
                ':request_id' => $data['request_id'],
                ':user_id' => $data['user_id'],
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
                ':status' => $status
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
                u.email as customer_email
            FROM CompanyQuotation cq
            INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
            INNER JOIN User u ON jr.user_id = u.user_id
            LEFT JOIN Category c ON jr.category_id = c.category_id
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
                $sql .= " AND cq.user_id = :user_id";
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
                u.email as customer_email
            FROM CompanyQuotation cq
            INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
            INNER JOIN User u ON jr.user_id = u.user_id
            LEFT JOIN Category c ON jr.category_id = c.category_id
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
                additional_terms = :additional_terms
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
            // Only allow deletion of pending quotations
            $sql = "DELETE FROM CompanyQuotation 
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
                    FROM CompanyQuotation 
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

            $sql = "UPDATE CompanyQuotation 
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
}
