<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ContractModel.php';

class ContractController {
    private $model;

    public function __construct() {
        global $pdo;
        $this->model = new ContractModel($pdo);
    }

    /**
     * Get company ID from session
     */
    private function getCompanyId() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
            return null;
        }
        return $_SESSION['user_id'];
    }

    /**
     * Get all contracts
     */
    public function getAllContracts() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $contracts = $this->model->getAll($companyId);
            
            // Format contracts for frontend
            $formattedContracts = array_map(function($contract) {
                return [
                    'contract_id' => $contract['contract_id'],
                    'project_id' => $contract['project_id'],
                    'contract_number' => 'CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT),
                    'title' => $contract['project_title'],
                    'description' => $contract['project_description'],
                    'type' => $contract['project_type'],
                    'client_name' => $contract['customer_fname'] . ' ' . $contract['customer_lname'],
                    'client_email' => $contract['customer_email'],
                    'value' => $contract['total_budget'],
                    'start_date' => $contract['start_date'],
                    'end_date' => $contract['end_date'],
                    'contract_date' => $contract['contract_date'],
                    'status' => $contract['contract_status'],
                    'progress' => $contract['progress'],
                    'location' => $contract['location'],
                    'milestone_plan' => (bool)$contract['milestone_plan'],
                    'payment_method' => $contract['payment_method'],
                    'sent_to_customer' => (bool)$contract['sent_to_customer'],
                    'sent_at' => $contract['sent_at'],
                    'customer_response' => $contract['customer_response'],
                    'customer_response_at' => $contract['customer_response_at']
                ];
            }, $contracts);

            echo json_encode([
                'success' => true,
                'data' => $formattedContracts,
                'count' => count($formattedContracts)
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getAllContracts: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get single contract by ID
     */
    public function getContract() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $contractId = $_GET['id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            $contract = $this->model->getById($contractId, $companyId);
            
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }

            // Get milestones
            $milestones = $this->model->getMilestones($contractId);

            // Format response
            $response = [
                'contract_id' => $contract['contract_id'],
                'project_id' => $contract['project_id'],
                'contract_number' => $contract['contract_number'] ?: ('CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT)),
                'title' => $contract['project_title'],
                'description' => $contract['project_description'],
                'type' => $contract['project_type'],
                'location' => $contract['location'] ?? $contract['project_location'],
                'project_location' => $contract['project_location'],
                'project_reference' => $contract['project_reference'],
                'client' => [
                    'id' => $contract['customer_id'],
                    'name' => $contract['customer_fname'] . ' ' . $contract['customer_lname'],
                    'email' => $contract['customer_email'],
                    'address' => $contract['customer_address'],
                    'district' => $contract['customer_district']
                ],
                'company' => [
                    'id' => $contract['company_id'],
                    'name' => $contract['company_name'],
                    'registration_no' => $contract['company_registration_no'] ?? '',
                    'address' => $contract['company_address'] ?? '',
                    'contact' => $contract['company_contact'] ?? '',
                    'email' => $contract['company_email'] ?? ''
                ],
                'value' => $contract['total_budget'],
                'budget_type' => $contract['budget_type'],
                'payment_method' => $contract['payment_method'],
                'amount_paid' => $contract['amount_paid'],
                'amount_pending' => $contract['amount_pending'],
                'late_payment_penalty' => $contract['late_payment_penalty'],
                'start_date' => $contract['start_date'],
                'end_date' => $contract['end_date'],
                'contract_date' => $contract['contract_date'],
                'status' => $contract['status'],
                'progress' => $contract['progress'] ?? $contract['progress_percentage'],
                'milestone_plan' => (bool)$contract['milestone_plan'],
                'milestones' => $milestones,
                'scope_description' => $contract['scope_description'],
                'scope_inclusions' => $contract['scope_inclusions'],
                'scope_exclusions' => $contract['scope_exclusions'],
                'materials_responsibility' => $contract['materials_responsibility'],
                'variation_clause' => (bool)$contract['variation_clause'],
                'communication_channel' => $contract['communication_channel'],
                'dispute_resolution' => $contract['dispute_resolution'],
                'terms_conditions' => $contract['terms_conditions'],
                'sent_to_customer' => (bool)$contract['sent_to_customer'],
                'sent_at' => $contract['sent_at'],
                'customer_response' => $contract['customer_response'],
                'customer_response_at' => $contract['customer_response_at']
            ];

            echo json_encode([
                'success' => true,
                'data' => $response
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getContract: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get contract statistics
     */
    public function getStats() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $stats = $this->model->getStats($companyId);

            echo json_encode([
                'success' => true,
                'data' => [
                    'total' => (int)$stats['total_contracts'],
                    'active' => (int)$stats['active_contracts'],
                    'completed' => (int)$stats['completed_contracts'],
                    'draft' => (int)$stats['draft_contracts'],
                    'terminated' => (int)$stats['terminated_contracts'],
                    'total_value' => (float)$stats['total_value'],
                    'avg_progress' => round((float)$stats['avg_progress'], 2)
                ]
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getStats: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Create new contract
     */
    public function createContract() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            $requiredFields = ['project_id', 'total_budget', 'start_date', 'contract_date'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                    return;
                }
            }

            // Set defaults
            $data['milestone_plan'] = $data['milestone_plan'] ?? true;
            $data['status'] = $data['status'] ?? 'draft';
            $data['user_signature'] = $data['user_signature'] ?? '';
            $data['company_signature'] = $data['company_signature'] ?? '';

            $contractId = $this->model->create($data);

            if ($contractId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contract created successfully',
                    'contract_id' => $contractId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create contract']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in createContract: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update contract
     */
    public function updateContract() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $contractId = $_GET['id'] ?? $data['contract_id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            $result = $this->model->update($contractId, $data, $companyId);

            if ($result) {
                // Also update milestones if provided
                if (isset($data['milestones']) && is_array($data['milestones'])) {
                    $this->model->updateMilestones($contractId, $data['milestones']);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Contract updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update contract']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in updateContract: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete contract
     */
    public function deleteContract() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $contractId = $_POST['id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            $result = $this->model->delete($contractId, $companyId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contract deleted successfully'
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found or unauthorized']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in deleteContract: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Send contract to customer (mark as sent within the platform)
     */
    public function sendToCustomer() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $contractId = $data['contract_id'] ?? $_POST['contract_id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            // Verify contract belongs to this company
            $contract = $this->model->getById($contractId, $companyId);
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found or unauthorized']);
                return;
            }

            // Check if already sent
            if ($contract['sent_to_customer']) {
                echo json_encode(['success' => false, 'message' => 'Contract has already been sent to the customer']);
                return;
            }

            // Mark as sent
            $result = $this->model->markAsSent($contractId, $companyId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contract sent to customer successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to send contract']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in sendToCustomer: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Filter contracts by status
     */
    public function filterByStatus() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $status = $_GET['status'] ?? null;
            
            if (!$status) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Status parameter required']);
                return;
            }

            $contracts = $this->model->filterByStatus($status, $companyId);
            
            // Format contracts for frontend
            $formattedContracts = array_map(function($contract) {
                return [
                    'contract_id' => $contract['contract_id'],
                    'project_id' => $contract['project_id'],
                    'contract_number' => 'CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT),
                    'title' => $contract['project_title'],
                    'description' => $contract['project_description'],
                    'type' => $contract['project_type'],
                    'client_name' => $contract['customer_fname'] . ' ' . $contract['customer_lname'],
                    'client_email' => $contract['customer_email'],
                    'value' => $contract['total_budget'],
                    'start_date' => $contract['start_date'],
                    'end_date' => $contract['end_date'],
                    'contract_date' => $contract['contract_date'],
                    'status' => $contract['contract_status'],
                    'progress' => $contract['progress'],
                    'location' => $contract['location'],
                    'milestone_plan' => (bool)$contract['milestone_plan']
                ];
            }, $contracts);

            echo json_encode([
                'success' => true,
                'data' => $formattedContracts,
                'count' => count($formattedContracts)
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in filterByStatus: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get accepted quotations/projects for contract creation
     * Returns projects that can be converted to contracts
     */
    public function getAcceptedProjects() {
        try {
            $companyId = $this->getCompanyId();
            
            error_log("[ContractController] Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
            error_log("[ContractController] Session role: " . ($_SESSION['user_role'] ?? 'NOT SET'));
            error_log("[ContractController] Company ID from session: " . ($companyId ?? 'NULL'));
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized - Not logged in as company']);
                return;
            }

            global $pdo;
            
            // Get projects that don't have contracts yet
            $stmt = $pdo->prepare("
                SELECT 
                    p.project_id,
                    p.title as project_title,
                    p.description as project_description,
                    p.project_type,
                    p.location,
                    p.budget as quoted_price,
                    p.start_date as proposed_start_date,
                    p.end_date as proposed_end_date,
                    p.status,
                    u.f_name,
                    u.l_name,
                    u.email as customer_email
                FROM Project p
                INNER JOIN User u ON p.customer_id = u.user_id
                WHERE p.company_id = ?
                AND p.status IN ('planned', 'active')
                AND NOT EXISTS (
                    SELECT 1 FROM Contract c
                    WHERE c.project_id = p.project_id
                )
                ORDER BY p.start_date DESC
            ");
            
            $stmt->execute([$companyId]);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("[ContractController] SQL executed with company_id: " . $companyId);
            error_log("[ContractController] Found " . count($projects) . " projects for company " . $companyId);
            
            // Format for frontend
            $formattedProjects = array_map(function($project) {
                return [
                    'project_id' => $project['project_id'],
                    'project_title' => $project['project_title'],
                    'project_description' => $project['project_description'] ?? 'No description',
                    'project_type' => $project['project_type'] ?? 'General',
                    'location' => $project['location'] ?? 'Not specified',
                    'quoted_price' => $project['quoted_price'] ?? 0,
                    'proposed_start_date' => $project['proposed_start_date'],
                    'proposed_end_date' => $project['proposed_end_date'],
                    'urgency' => 'medium', // Default since not in Project table
                    'client_name' => trim(($project['f_name'] ?? '') . ' ' . ($project['l_name'] ?? '')),
                    'client_email' => $project['customer_email'] ?? '',
                    'client_phone' => '' // Phone not in User table
                ];
            }, $projects);

            echo json_encode([
                'success' => true,
                'data' => $formattedProjects,
                'count' => count($formattedProjects)
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getAcceptedProjects: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * PHASE 2: Auto-create contracts from accepted quotations
     * This method finds all accepted quotations without contracts and creates them automatically
     */
    public function autoCreateContracts() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Find accepted quotations without contracts for this company
            global $pdo;
            $stmt = $pdo->prepare("
                SELECT q.quotation_id, q.title
                FROM companyquotation q
                LEFT JOIN contract c ON c.quotation_id = q.quotation_id
                WHERE q.status = 'accepted' 
                AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
                AND c.contract_id IS NULL
            ");
            $stmt->execute([$companyId, $companyId]);
            $pendingQuotations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $results = [
                'processed' => 0,
                'succeeded' => 0,
                'failed' => 0,
                'details' => []
            ];

            foreach ($pendingQuotations as $quotation) {
                $results['processed']++;
                
                try {
                    $result = $this->model->createFromQuotation($quotation['quotation_id']);
                    
                    if ($result['success']) {
                        $results['succeeded']++;
                        $results['details'][] = [
                            'quotation_id' => $quotation['quotation_id'],
                            'title' => $quotation['title'],
                            'status' => 'success',
                            'contract_id' => $result['contract_id'],
                            'project_id' => $result['project_id']
                        ];
                        error_log("[ContractController] Auto-created contract {$result['contract_id']} from quotation {$quotation['quotation_id']}");
                    } else {
                        $results['failed']++;
                        $results['details'][] = [
                            'quotation_id' => $quotation['quotation_id'],
                            'title' => $quotation['title'],
                            'status' => 'failed',
                            'error' => $result['message'] ?? 'Unknown error'
                        ];
                    }
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['details'][] = [
                        'quotation_id' => $quotation['quotation_id'],
                        'title' => $quotation['title'],
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                    error_log("[ContractController] Error creating contract from quotation {$quotation['quotation_id']}: " . $e->getMessage());
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $results
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in autoCreateContracts: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * PHASE 2: Get summary of accepted quotations and contract creation status
     */
    public function getQuotationSummary() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            global $pdo;
            
            // Count accepted quotations (check both company_id and user_id for backward compatibility)
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM companyquotation 
                WHERE status = 'accepted' 
                AND (company_id = ? OR (company_id IS NULL AND user_id = ?))
            ");
            $stmt->execute([$companyId, $companyId]);
            $acceptedCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Count quotations with contracts
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT q.quotation_id) as count 
                FROM companyquotation q
                INNER JOIN contract c ON c.quotation_id = q.quotation_id
                WHERE q.status = 'accepted' 
                AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
            ");
            $stmt->execute([$companyId, $companyId]);
            $withContractsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            $pendingCount = $acceptedCount - $withContractsCount;

            echo json_encode([
                'success' => true,
                'data' => [
                    'accepted_quotations' => (int)$acceptedCount,
                    'with_contracts' => (int)$withContractsCount,
                    'pending_creation' => (int)$pendingCount
                ]
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getQuotationSummary: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get accepted quotations for contract creation dropdown
     */
    public function getAcceptedQuotations() {
        try {
            error_log("[ContractController] getAcceptedQuotations called");
            
            $companyId = $this->getCompanyId();
            error_log("[ContractController] Company ID: " . ($companyId ?? 'NULL'));
            
            if ($companyId === null) {
                error_log("[ContractController] Unauthorized - no company ID");
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized - Please log in']);
                return;
            }

            global $pdo;
            
            if (!$pdo) {
                error_log("[ContractController] ERROR: PDO not available");
                throw new Exception("Database connection not available");
            }
            
            error_log("[ContractController] PDO connection OK");
            
            // Get all accepted quotations without contracts
            // Include full details for auto-fill
            $stmt = $pdo->prepare("
                SELECT 
                    q.quotation_id,
                    q.request_id,
                    q.title,
                    q.description,
                    q.total_amount,
                    q.budget_type,
                    q.budget_min,
                    q.budget_max,
                    q.payment_method,
                    q.pricing_type,
                    q.hourly_rate,
                    q.spending_cap_multiplier,
                    q.start_date,
                    q.completion_date,
                    q.estimated_duration,
                    q.labor_cost,
                    q.material_cost,
                    q.transport_cost,
                    q.other_charges,
                    q.warranty_period,
                    q.payment_terms,
                    q.additional_terms,
                    r.address as location,
                    r.district,
                    r.title as request_title,
                    u.user_id as customer_id,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email
                FROM companyquotation q
                INNER JOIN jobrequest r ON q.request_id = r.request_id
                INNER JOIN user u ON r.user_id = u.user_id
                LEFT JOIN contract c ON c.quotation_id = q.quotation_id
                WHERE q.status = 'accepted'
                AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
                AND c.contract_id IS NULL
                ORDER BY q.updated_at DESC
            ");
            
            error_log("[ContractController] Executing query with company_id: " . $companyId);
            $stmt->execute([$companyId, $companyId]);
            error_log("[ContractController] Query executed successfully");
            
            $quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("[ContractController] Found " . count($quotations) . " quotations");

            if (count($quotations) > 0) {
                error_log("[ContractController] First quotation: " . json_encode($quotations[0]));
            }

            echo json_encode([
                'success' => true,
                'data' => $quotations,
                'count' => count($quotations)
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] ERROR in getAcceptedQuotations: " . $e->getMessage());
            error_log("[ContractController] Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * ============================================
     * PHASE 2A: ESSENTIAL CONTRACT FEATURES
     * ============================================
     */

    /**
     * Generate unique contract number
     * Format: CTR-YYYY-XXXX (e.g., CTR-2026-0001)
     */
    private function generateContractNumber() {
        global $pdo;
        
        $year = date('Y');
        $prefix = "CTR-{$year}-";
        
        // Get the highest contract number for this year
        $stmt = $pdo->prepare("
            SELECT contract_number 
            FROM contract 
            WHERE contract_number LIKE ? 
            ORDER BY contract_number DESC 
            LIMIT 1
        ");
        $stmt->execute([$prefix . '%']);
        $lastContract = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($lastContract) {
            // Extract number and increment
            $lastNumber = (int)substr($lastContract['contract_number'], -4);
            $newNumber = $lastNumber + 1;
        } else {
            // First contract of the year
            $newNumber = 1;
        }
        
        // Format: CTR-2026-0001
        $contractNumber = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        
        error_log("[ContractController] Generated contract number: " . $contractNumber);
        
        return $contractNumber;
    }

    /**
     * Generate milestones based on payment method
     */
    private function generateMilestones($totalAmount, $paymentMethod, $startDate, $endDate) {
        $milestones = [];
        
        // Calculate duration in days
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $duration = $start->diff($end)->days;
        
        switch ($paymentMethod) {
            case 'full_upfront':
                // Single milestone at start
                $milestones[] = [
                    'milestone_number' => 1,
                    'title' => 'Full Payment',
                    'description' => 'Complete project payment upfront',
                    'due_date' => $startDate,
                    'amount' => $totalAmount,
                    'percentage' => 100
                ];
                break;
                
            case 'milestone_based':
                // 3 milestones: 30% start, 40% middle, 30% end
                $milestones[] = [
                    'milestone_number' => 1,
                    'title' => 'Initial Payment',
                    'description' => 'Project initiation and setup',
                    'due_date' => $startDate,
                    'amount' => $totalAmount * 0.30,
                    'percentage' => 30
                ];
                
                $midDate = clone $start;
                $midDate->add(new DateInterval('P' . floor($duration / 2) . 'D'));
                
                $milestones[] = [
                    'milestone_number' => 2,
                    'title' => 'Mid-Project Payment',
                    'description' => 'Progress payment for ongoing work',
                    'due_date' => $midDate->format('Y-m-d'),
                    'amount' => $totalAmount * 0.40,
                    'percentage' => 40
                ];
                
                $milestones[] = [
                    'milestone_number' => 3,
                    'title' => 'Final Payment',
                    'description' => 'Project completion and handover',
                    'due_date' => $endDate,
                    'amount' => $totalAmount * 0.30,
                    'percentage' => 30
                ];
                break;
                
            case '50_50':
                // 50% start, 50% end
                $milestones[] = [
                    'milestone_number' => 1,
                    'title' => 'Initial Payment (50%)',
                    'description' => 'First half payment at project start',
                    'due_date' => $startDate,
                    'amount' => $totalAmount * 0.50,
                    'percentage' => 50
                ];
                
                $milestones[] = [
                    'milestone_number' => 2,
                    'title' => 'Final Payment (50%)',
                    'description' => 'Second half payment upon completion',
                    'due_date' => $endDate,
                    'amount' => $totalAmount * 0.50,
                    'percentage' => 50
                ];
                break;
                
            case '30_70':
                // 30% start, 70% end
                $milestones[] = [
                    'milestone_number' => 1,
                    'title' => 'Initial Payment (30%)',
                    'description' => 'Advance payment at project start',
                    'due_date' => $startDate,
                    'amount' => $totalAmount * 0.30,
                    'percentage' => 30
                ];
                
                $milestones[] = [
                    'milestone_number' => 2,
                    'title' => 'Final Payment (70%)',
                    'description' => 'Completion payment upon delivery',
                    'due_date' => $endDate,
                    'amount' => $totalAmount * 0.70,
                    'percentage' => 70
                ];
                break;
                
            case 'completion':
                // Single milestone at end
                $milestones[] = [
                    'milestone_number' => 1,
                    'title' => 'Payment on Completion',
                    'description' => 'Full payment after project completion',
                    'due_date' => $endDate,
                    'amount' => $totalAmount,
                    'percentage' => 100
                ];
                break;
        }
        
        return $milestones;
    }

    /**
     * Create contract with milestones from quotation
     */
    public function createContractWithMilestones() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Get POST data
            $quotationId = $_POST['quotation_id'] ?? null;
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            
            if (!$quotationId || !$startDate || !$endDate) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }

            global $pdo;
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Get quotation details
                $stmt = $pdo->prepare("
                    SELECT q.*, r.user_id as customer_id, r.request_id
                    FROM companyquotation q
                    INNER JOIN jobrequest r ON q.request_id = r.request_id
                    WHERE q.quotation_id = ? 
                    AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
                    AND q.status = 'accepted'
                ");
                $stmt->execute([$quotationId, $companyId, $companyId]);
                $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$quotation) {
                    throw new Exception('Quotation not found or not authorized');
                }
                
                // Generate contract number
                $contractNumber = $this->generateContractNumber();
                
                // Create contract
                $stmt = $pdo->prepare("
                    INSERT INTO contract (
                        contract_number, quotation_id, company_id, customer_id, job_request_id,
                        total_budget, budget_type, budget_min, budget_max,
                        payment_method, pricing_type, hourly_rate, spending_cap,
                        start_date, end_date, status, terms_accepted
                    ) VALUES (
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, 'draft', FALSE
                    )
                ");
                
                $stmt->execute([
                    $contractNumber,
                    $quotationId,
                    $companyId,
                    $quotation['customer_id'],
                    $quotation['request_id'],
                    $quotation['total_amount'],
                    $quotation['budget_type'] ?? 'fixed',
                    $quotation['budget_min'],
                    $quotation['budget_max'],
                    $quotation['payment_method'] ?? 'milestone_based',
                    $quotation['pricing_type'] ?? 'fixed_price',
                    $quotation['hourly_rate'],
                    $quotation['total_amount'] * 1.10, // spending cap
                    $startDate,
                    $endDate
                ]);
                
                $contractId = $pdo->lastInsertId();
                
                // Generate and insert milestones
                $milestones = $this->generateMilestones(
                    $quotation['total_amount'],
                    $quotation['payment_method'] ?? 'milestone_based',
                    $startDate,
                    $endDate
                );
                
                foreach ($milestones as $milestone) {
                    $stmt = $pdo->prepare("
                        INSERT INTO contract_milestone (
                            contract_id, milestone_number, title, description,
                            due_date, amount, status
                        ) VALUES (?, ?, ?, ?, ?, ?, 'pending')
                    ");
                    
                    $stmt->execute([
                        $contractId,
                        $milestone['milestone_number'],
                        $milestone['title'],
                        $milestone['description'],
                        $milestone['due_date'],
                        $milestone['amount']
                    ]);
                }
                
                // Log audit event
                $this->logAuditEvent(
                    $contractId,
                    null,
                    'contract_created',
                    $companyId,
                    'company',
                    json_encode([
                        'quotation_id' => $quotationId,
                        'contract_number' => $contractNumber,
                        'milestone_count' => count($milestones)
                    ])
                );
                
                // Update quotation status
                $stmt = $pdo->prepare("
                    UPDATE companyquotation 
                    SET status = 'contract_created' 
                    WHERE quotation_id = ?
                ");
                $stmt->execute([$quotationId]);
                
                // Commit transaction
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Contract created successfully with milestones',
                    'data' => [
                        'contract_id' => $contractId,
                        'contract_number' => $contractNumber,
                        'milestones' => $milestones
                    ]
                ]);
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("[ContractController] ERROR in createContractWithMilestones: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get contract details with milestones
     */
    public function getContractWithMilestones() {
        try {
            $companyId = $this->getCompanyId();
            
            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $contractId = $_GET['id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            global $pdo;
            
            // Get contract details
            $stmt = $pdo->prepare("
                SELECT c.*, 
                       cu.f_name as customer_fname, cu.l_name as customer_lname, cu.email as customer_email,
                       q.title as quotation_title
                FROM contract c
                LEFT JOIN user cu ON c.customer_id = cu.user_id
                LEFT JOIN companyquotation q ON c.quotation_id = q.quotation_id
                WHERE c.contract_id = ? AND c.company_id = ?
            ");
            $stmt->execute([$contractId, $companyId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }
            
            // Get milestones
            $stmt = $pdo->prepare("
                SELECT * FROM contract_milestone 
                WHERE contract_id = ? 
                ORDER BY milestone_number ASC
            ");
            $stmt->execute([$contractId]);
            $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $contract['milestones'] = $milestones;
            
            echo json_encode([
                'success' => true,
                'data' => $contract
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] ERROR in getContractWithMilestones: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Log audit event
     */
    private function logAuditEvent($contractId, $milestoneId, $action, $performedBy, $userRole, $details) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO contract_audit_log (
                    contract_id, milestone_id, action, performed_by, user_role, details, ip_address, user_agent
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $contractId,
                $milestoneId,
                $action,
                $performedBy,
                $userRole,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
            error_log("[ContractController] Audit log created: {$action} for contract {$contractId}");
            
        } catch (Exception $e) {
            error_log("[ContractController] ERROR logging audit event: " . $e->getMessage());
            // Don't throw - logging failure shouldn't break the main operation
        }
    }
}
