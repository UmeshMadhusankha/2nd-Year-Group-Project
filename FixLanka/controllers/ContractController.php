<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ContractModel.php';
require_once __DIR__ . '/../models/EscrowModel.php';
require_once __DIR__ . '/../models/SystemNotificationService.php';

class ContractController {
    private $model;
    private $pdo;
    private $notifier;

    public function __construct() {
        global $pdo;
        $this->model = new ContractModel($pdo);
        $this->pdo = $pdo;
        $this->notifier = new SystemNotificationService($pdo);
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
     * Get customer/user ID from session.
     */
    private function getCustomerId() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            return null;
        }

        $role = $_SESSION['user_role'];
        if ($role !== 'user' && $role !== 'customer') {
            return null;
        }

        return $_SESSION['user_id'];
    }

    /**
     * Get all contracts for the logged-in customer.
     */
    public function getCustomerContracts() {
        try {
            $customerId = $this->getCustomerId();
            if ($customerId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $contracts = $this->model->getAllForCustomer($customerId);

            $formattedContracts = array_map(function($contract) {
                $actualStatus = $contract['contract_status'] ?? 'draft';

                if (!empty($contract['sent_to_customer']) && empty($contract['terms_accepted'])) {
                    $actualStatus = 'pending_signature';
                } else if (($contract['customer_response'] ?? null) === 'rejected') {
                    $actualStatus = 'terminated';
                } else if (!empty($contract['terms_accepted'])) {
                    $actualStatus = $contract['contract_status'] ?? 'active';
                }

                return [
                    'contract_id' => (int)$contract['contract_id'],
                    'project_id' => $contract['project_id'],
                    'contract_number' => $contract['contract_number'] ?? ('CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT)),
                    'project_title' => $contract['project_title'] ?? 'Untitled Contract',
                    'project_description' => $contract['project_description'] ?? '',
                    'project_location' => $contract['project_location'] ?? '—',
                    'contract_date' => $contract['contract_date'] ?? null,
                    'start_date' => $contract['start_date'] ?? null,
                    'end_date' => $contract['end_date'] ?? null,
                    'total_budget' => $contract['total_budget'] ?? 0,
                    'value' => $contract['total_budget'] ?? 0,
                    'payment_method' => $contract['payment_method'] ?? null,
                    'budget_type' => $contract['budget_type'] ?? null,
                    'progress_percentage' => (int)($contract['progress_percentage'] ?? 0),
                    'status' => $actualStatus,
                    'sent_to_customer' => (bool)($contract['sent_to_customer'] ?? 0),
                    'sent_at' => $contract['sent_at'] ?? null,
                    'customer_response' => $contract['customer_response'] ?? 'pending',
                    'customer_response_at' => $contract['customer_response_at'] ?? null,
                    'terms_accepted' => (bool)($contract['terms_accepted'] ?? 0),
                    'chat_active' => (int)($contract['chat_active'] ?? 0),
                    'unread_messages' => (int)($contract['unread_messages'] ?? 0),
                    'company_id' => $contract['company_id'] ?? null,
                    'company_name' => $contract['company_name'] ?? '—',
                    'labor_cost' => isset($contract['labor_cost']) ? (float)$contract['labor_cost'] : null,
                    'material_cost' => isset($contract['material_cost']) ? (float)$contract['material_cost'] : null,
                    'labor_unit_label' => $contract['labor_unit_label'] ?? null,
                    'material_unit_label' => $contract['material_unit_label'] ?? null,
                    'total_milestones' => (int)($contract['total_milestones'] ?? 0),
                    'completed_milestones' => (int)($contract['completed_milestones'] ?? 0)
                ];
            }, $contracts);

            echo json_encode([
                'success' => true,
                'data' => $formattedContracts,
                'count' => count($formattedContracts)
            ]);
        } catch (Exception $e) {
            error_log("[ContractController] Error in getCustomerContracts: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Get a single contract for the logged-in customer.
     */
    public function getCustomerContract() {
        try {
            $customerId = $this->getCustomerId();
            if ($customerId === null) {
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

            $contract = $this->model->getByIdForCustomer($contractId, $customerId);
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }

            $milestones = $this->model->getMilestones($contractId);
            $undoAvailable = $this->model->checkUndoStatus($contractId);

            // Determine actual status based on business logic
            $actualStatus = $contract['status'] ?? 'draft';
            if (!empty($contract['sent_to_customer']) && empty($contract['terms_accepted'])) {
                $actualStatus = 'pending_signature';
            } else if (($contract['customer_response'] ?? null) === 'rejected') {
                $actualStatus = 'terminated';
            } else if (!empty($contract['terms_accepted'])) {
                $actualStatus = $contract['status'] ?? 'active';
            }

            $response = [
                'contract_id' => (int)$contract['contract_id'],
                'contract_number' => $contract['contract_number'] ?: ('CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT)),
                'status' => $actualStatus,
                'sent_to_customer' => (bool)($contract['sent_to_customer'] ?? 0),
                'sent_at' => $contract['sent_at'] ?? null,
                'customer_response' => $contract['customer_response'] ?? null,
                'customer_response_at' => $contract['customer_response_at'] ?? null,
                'terms_accepted' => (bool)($contract['terms_accepted'] ?? 0),
                'undo_available' => (bool)$undoAvailable,
                'undo_deadline' => $contract['undo_deadline'] ?? null,

                'project_title' => $contract['project_title'] ?? '',
                'project_reference' => $contract['project_reference'] ?? '',
                'project_location' => $contract['project_location'] ?? '',
                'project_description' => $contract['project_description'] ?? '',

                'company_id' => $contract['company_id'] ?? null,
                'company_name' => $contract['company_name'] ?? '',
                'company_registration' => $contract['company_registration_no'] ?? '',
                'company_email' => $contract['company_email'] ?? '',
                'company_phone' => $contract['company_contact'] ?? '',

                'customer_id' => $contract['customer_id'] ?? null,
                'customer_fname' => $contract['customer_fname'] ?? '',
                'customer_lname' => $contract['customer_lname'] ?? '',
                'customer_email' => $contract['customer_email'] ?? '',

                'total_budget' => $contract['total_budget'] ?? 0,
                'budget_type' => $contract['budget_type'] ?? null,
                'payment_method' => $contract['payment_method'] ?? null,
                'amount_paid' => $contract['amount_paid'] ?? 0,
                'amount_pending' => $contract['amount_pending'] ?? 0,
                'start_date' => $contract['start_date'] ?? null,
                'end_date' => $contract['end_date'] ?? null,
                'contract_date' => $contract['contract_date'] ?? null,
                'progress_percentage' => $contract['progress_percentage'] ?? ($contract['progress'] ?? 0),
                'milestone_plan' => (bool)($contract['milestone_plan'] ?? 0),
                'milestones' => $milestones,

                'scope_description' => $contract['scope_description'] ?? '',
                'scope_inclusions' => $contract['scope_inclusions'] ?? '',
                'scope_exclusions' => $contract['scope_exclusions'] ?? '',
                'materials_responsibility' => $contract['materials_responsibility'] ?? '',
                'late_payment_penalty' => $contract['late_payment_penalty'] ?? '',
                'variation_clause' => (bool)($contract['variation_clause'] ?? 0),
                'communication_channel' => $contract['communication_channel'] ?? '',
                'dispute_resolution' => $contract['dispute_resolution'] ?? '',
                'terms_conditions' => $contract['terms_conditions'] ?? ''
            ];

            echo json_encode(['success' => true, 'data' => $response]);
        } catch (Exception $e) {
            error_log("[ContractController] Error in getCustomerContract: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
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
                // Determine actual status based on business logic
                $actualStatus = $contract['contract_status'] ?? 'draft';
                
                // If contract was sent to customer but not accepted, status should be 'sent' or 'pending'
                if ($contract['sent_to_customer'] && !$contract['terms_accepted']) {
                    $actualStatus = 'sent'; // Awaiting customer response
                } 
                // If customer rejected
                else if ($contract['customer_response'] === 'rejected') {
                    $actualStatus = 'terminated';
                }
                // If terms accepted, use the database status (active, in_progress, etc.)
                else if ($contract['terms_accepted']) {
                    $actualStatus = $contract['contract_status'] ?? 'active';
                }
                
                return [
                    'contract_id' => $contract['contract_id'],
                    'project_id' => $contract['project_id'],
                    'quotation_id' => $contract['quotation_id'] ?? null,
                    'contract_number' => $contract['contract_number'] ?? ('CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT)),
                    'title' => $contract['project_title'] ?? 'Untitled Contract',
                    'description' => $contract['project_description'] ?? '',
                    'type' => 'general', // Contract table doesn't have project_type
                    'client_name' => ($contract['customer_fname'] ?? '') . ' ' . ($contract['customer_lname'] ?? ''),
                    'client_email' => $contract['customer_email'] ?? '',
                    'value' => $contract['total_budget'],
                    'start_date' => $contract['start_date'],
                    'end_date' => $contract['end_date'],
                    'contract_date' => $contract['contract_date'],
                    'status' => $actualStatus,
                    'progress' => $contract['progress'] ?? 0,
                    'location' => $contract['location'] ?? 'N/A',
                    'milestone_plan' => (bool)($contract['milestone_plan'] ?? 0),
                    'payment_method' => $contract['payment_method'] ?? 'milestone_based',
                    'budget_type' => $contract['budget_type'] ?? 'fixed',
                    'sent_to_customer' => (bool)($contract['sent_to_customer'] ?? 0),
                    'sent_at' => $contract['sent_at'] ?? null,
                    'customer_response' => $contract['customer_response'] ?? 'pending',
                    'customer_response_at' => $contract['customer_response_at'] ?? null,
                    'terms_accepted' => (bool)($contract['terms_accepted'] ?? 0),
                    'chat_active' => (int)($contract['chat_active'] ?? 0),
                    'unread_messages' => (int)($contract['unread_messages'] ?? 0),
                    'labor_cost' => isset($contract['labor_cost']) ? (float)$contract['labor_cost'] : null,
                    'material_cost' => isset($contract['material_cost']) ? (float)$contract['material_cost'] : null,
                    'transport_cost' => isset($contract['transport_cost']) ? (float)$contract['transport_cost'] : null,
                    'other_charges' => isset($contract['other_charges']) ? (float)$contract['other_charges'] : null,
                    'labor_unit_label' => $contract['labor_unit_label'] ?? null,
                    'material_unit_label' => $contract['material_unit_label'] ?? null
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

            // Check and update undo status
            $this->model->checkUndoStatus($contractId);
            // Refresh contract data after status update
            $contract = $this->model->getById($contractId, $companyId);

            // Determine actual status based on business logic
            $actualStatus = $contract['status'] ?? 'draft';
            
            // If contract was sent to customer but not accepted, status should be 'sent' or 'pending'
            if ($contract['sent_to_customer'] && !$contract['terms_accepted']) {
                $actualStatus = 'sent'; // Awaiting customer response
            } 
            // If customer rejected
            else if ($contract['customer_response'] === 'rejected') {
                $actualStatus = 'terminated';
            }
            // If terms accepted, use the database status (active, in_progress, etc.)
            else if ($contract['terms_accepted']) {
                $actualStatus = $contract['status'] ?? 'active';
            }

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
                'status' => $actualStatus,
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
                'customer_response_at' => $contract['customer_response_at'],
                'terms_accepted' => (bool)($contract['terms_accepted'] ?? 0),
                'undo_available' => (bool)($contract['undo_available'] ?? 0),
                'undo_deadline' => $contract['undo_deadline'] ?? null
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

            if (!is_array($data)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
                return;
            }

            // Enforce platform chat only (no email communication)
            $data['communication_channel'] = 'system';

            // Start transaction so project+contract are consistent
            $this->pdo->beginTransaction();

            try {
                $quotationForUnitPricing = null;

                // If project_id is missing or not a valid project for this company, create a project from the accepted quotation
                $projectId = $data['project_id'] ?? null;
                $projectIdValid = false;
                if (!empty($projectId) && is_numeric($projectId)) {
                    $pStmt = $this->pdo->prepare("SELECT project_id FROM project WHERE project_id = ? AND company_id = ? LIMIT 1");
                    $pStmt->execute([(int)$projectId, $companyId]);
                    $projectIdValid = (bool)$pStmt->fetch(PDO::FETCH_ASSOC);
                }

                if (!$projectIdValid) {
                    $quotationId = $data['quotation_id'] ?? null;
                    if (empty($quotationId) || !is_numeric($quotationId)) {
                        throw new Exception("project_id is invalid; quotation_id is required to create a project");
                    }

                    $qStmt = $this->pdo->prepare("
                        SELECT q.*, 
                               jr.user_id AS customer_id,
                               jr.request_id,
                               jr.title AS request_title,
                               jr.description AS request_description,
                               jr.address,
                                                             jr.district,
                                                             cat.name AS request_category_name
                        FROM companyquotation q
                        INNER JOIN jobrequest jr ON q.request_id = jr.request_id
                                                LEFT JOIN category cat ON jr.category_id = cat.category_id
                        WHERE q.quotation_id = ?
                          AND q.status = 'accepted'
                          AND q.company_id = ?
                        LIMIT 1
                    ");
                    $qStmt->execute([(int)$quotationId, $companyId]);
                    $quotation = $qStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$quotation) {
                        throw new Exception('Quotation not found, not accepted, or not authorized');
                    }

                    $quotationForUnitPricing = $quotation;

                    $resolvedProjectTitle = trim($data['project_title'] ?? '') !== '' ? trim($data['project_title']) : ($quotation['title'] ?? $quotation['request_title'] ?? 'Project');
                    $resolvedProjectDescription = trim($data['project_description'] ?? '') !== '' ? trim($data['project_description']) : ($quotation['description'] ?? $quotation['request_description'] ?? null);
                    $resolvedProjectLocation = trim($data['project_location'] ?? '') !== ''
                        ? trim($data['project_location'])
                        : trim(($quotation['address'] ?? '') . (isset($quotation['district']) ? (', ' . $quotation['district']) : ''));
                    if ($resolvedProjectLocation === '') {
                        $resolvedProjectLocation = $quotation['district'] ?? 'N/A';
                    }
                    $resolvedProjectType = (isset($data['project_type']) && trim($data['project_type']) !== '')
                        ? trim($data['project_type'])
                        : ($quotation['request_category_name'] ?? null);

                    $resolvedTotalBudget = isset($data['total_budget']) && $data['total_budget'] !== '' ? (float)$data['total_budget'] : (float)$quotation['total_amount'];
                    $resolvedStartDate = !empty($data['start_date']) ? $data['start_date'] : ($quotation['start_date'] ?? null);
                    $resolvedEndDate = !empty($data['end_date']) ? $data['end_date'] : ($quotation['completion_date'] ?? null);

                    $projStmt = $this->pdo->prepare("
                        INSERT INTO project (
                            company_id, customer_id, title, description, project_type, location,
                            budget, start_date, end_date, status, progress
                        ) VALUES (
                            ?, ?, ?, ?, ?, ?,
                            ?, ?, ?, 'planned', 0
                        )
                    ");
                    $projStmt->execute([
                        $companyId,
                        $quotation['customer_id'],
                        $resolvedProjectTitle,
                        $resolvedProjectDescription,
                        $resolvedProjectType,
                        $resolvedProjectLocation,
                        $resolvedTotalBudget,
                        $resolvedStartDate,
                        $resolvedEndDate
                    ]);

                    $projectId = (int)$this->pdo->lastInsertId();

                    // Hydrate contract payload from quotation where useful
                    $data['project_id'] = $projectId;
                    $data['customer_id'] = $data['customer_id'] ?? $quotation['customer_id'];
                    $data['job_request_id'] = $data['job_request_id'] ?? $quotation['request_id'];
                    $data['quotation_id'] = $data['quotation_id'] ?? (int)$quotationId;
                    $data['project_title'] = $data['project_title'] ?? $resolvedProjectTitle;
                    $data['project_location'] = $data['project_location'] ?? $resolvedProjectLocation;
                    $data['project_type'] = $data['project_type'] ?? $resolvedProjectType;
                    $data['project_description'] = $data['project_description'] ?? $resolvedProjectDescription;
                    $data['end_date'] = $data['end_date'] ?? $resolvedEndDate;
                }

                // If we didn't load the quotation above (project already existed), load it for unit-pricing rules.
                if ($quotationForUnitPricing === null) {
                    $quotationId = $data['quotation_id'] ?? null;
                    if (!empty($quotationId) && is_numeric($quotationId)) {
                        $qUnitStmt = $this->pdo->prepare("\
                            SELECT *
                            FROM companyquotation
                            WHERE quotation_id = ?
                              AND status IN ('accepted','successful')
                              AND company_id = ?
                            LIMIT 1
                        ");
                        $qUnitStmt->execute([(int)$quotationId, $companyId]);
                        $quotationForUnitPricing = $qUnitStmt->fetch(PDO::FETCH_ASSOC) ?: null;
                    }
                }

                $isUnitBased = false;
                if ($quotationForUnitPricing) {
                    $labUnit = trim((string)($quotationForUnitPricing['labor_unit_label'] ?? ''));
                    $matUnit = trim((string)($quotationForUnitPricing['material_unit_label'] ?? ''));
                    $isUnitBased = ($labUnit !== '' || $matUnit !== '');
                }

                // Unit-priced contracts must be milestone-based with fixed pricing type.
                if ($isUnitBased) {
                    $data['payment_method'] = 'milestone_based';
                    $data['milestone_plan'] = 1;
                    $data['pricing_type'] = 'fixed_price';
                }
            
                // Validate required fields (project_id will exist after the quotation fallback above)
                $requiredFields = ['project_id', 'customer_id', 'total_budget', 'start_date', 'contract_date'];
                foreach ($requiredFields as $field) {
                    if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                        throw new Exception("Field '$field' is required");
                    }
                }

                // Set defaults
                $data['milestone_plan'] = isset($data['milestone_plan']) ? (int)((bool)$data['milestone_plan']) : 1;
                $data['status'] = $data['status'] ?? 'draft';
                $data['user_signature'] = (isset($data['user_signature']) && $data['user_signature'] !== '') ? $data['user_signature'] : 'PENDING';
                $data['company_signature'] = (isset($data['company_signature']) && $data['company_signature'] !== '') ? $data['company_signature'] : 'PENDING';
                $data['company_id'] = $companyId;
                $data['amount_pending'] = (isset($data['amount_pending']) && $data['amount_pending'] !== '') ? (float)$data['amount_pending'] : (float)$data['total_budget'];
                $data['payment_status'] = $data['payment_status'] ?? 'pending';

                // Enforce again after any hydration/defaulting
                $data['communication_channel'] = 'system';

                $contractId = $this->model->create($data);

                if (!$contractId) {
                    throw new Exception('Failed to create contract');
                }

                // Save milestones
                $milestonesToSave = null;
                if ($isUnitBased && $quotationForUnitPricing) {
                    $endDate = $data['end_date'] ?? null;

                    $labUnit = trim((string)($quotationForUnitPricing['labor_unit_label'] ?? ''));
                    $matUnit = trim((string)($quotationForUnitPricing['material_unit_label'] ?? ''));

                    $laborRate = ($quotationForUnitPricing['labor_cost'] ?? null);
                    $laborRate = ($laborRate !== null && $laborRate !== '' && is_numeric($laborRate)) ? (float)$laborRate : null;

                    $materialRate = 0.0;
                    foreach (['material_cost', 'transport_cost', 'other_charges'] as $k) {
                        $v = $quotationForUnitPricing[$k] ?? 0;
                        if ($v !== null && $v !== '' && is_numeric($v)) {
                            $materialRate += (float)$v;
                        }
                    }

                    if ($materialRate <= 0 && isset($quotationForUnitPricing['total_amount']) && is_numeric($quotationForUnitPricing['total_amount'])) {
                        $total = (float)$quotationForUnitPricing['total_amount'];
                        $lab = (float)($laborRate ?? 0);
                        $materialRate = max(0.0, $total - $lab);
                    }

                    $unitMilestones = [];

                    if ($labUnit !== '' && $laborRate !== null) {
                        $unitMilestones[] = [
                            'title' => 'Labour',
                            'description' => 'Company submits actual labour units after completion; customer verifies before payment is finalized.',
                            'due_date' => $endDate,
                            'amount' => '0.00',
                            'percentage' => 0,
                            'unit_label' => $labUnit,
                            'unit_rate' => $laborRate,
                            'estimated_quantity' => null
                        ];
                    }

                    if ($matUnit !== '') {
                        $unitMilestones[] = [
                            'title' => 'Materials',
                            'description' => 'Company submits actual material units and any material price variation; customer verifies before payment is finalized.',
                            'due_date' => $endDate,
                            'amount' => '0.00',
                            'percentage' => 0,
                            'unit_label' => $matUnit,
                            'unit_rate' => $materialRate,
                            'estimated_quantity' => null
                        ];
                    }

                    if (!empty($unitMilestones)) {
                        $milestonesToSave = $unitMilestones;
                    }
                }

                if ($milestonesToSave === null && isset($data['milestones']) && is_array($data['milestones'])) {
                    $milestonesToSave = $data['milestones'];
                }

                if ($milestonesToSave !== null) {
                    $this->model->updateMilestones($contractId, $milestonesToSave);
                }

                // If asked to send to customer immediately
                if (isset($data['send_to_customer']) && $data['send_to_customer']) {
                    $this->model->markAsSent($contractId, $companyId);
                }

                // Update quotation and job request statuses if they exist
                if (isset($data['quotation_id']) && $data['quotation_id']) {
                    $qStmt = $this->pdo->prepare("UPDATE companyquotation SET status = 'successful' WHERE quotation_id = ?");
                    $qStmt->execute([(int)$data['quotation_id']]);
                }
                if (isset($data['job_request_id']) && $data['job_request_id']) {
                    $rStmt = $this->pdo->prepare("UPDATE jobrequest SET status = 'in_progress' WHERE request_id = ?");
                    $rStmt->execute([(int)$data['job_request_id']]);
                }

                $this->pdo->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Contract created successfully',
                    'contract_id' => $contractId
                ]);

            } catch (Exception $inner) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                throw $inner;
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

            // Enforce platform chat only (no email communication)
            if (is_array($data)) {
                $data['communication_channel'] = 'system';
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
     * Cancel contract (delete and re-enable quotation for new contract)
     */
    public function cancelContract() {
        try {
            $companyId = $this->getCompanyId();

            if ($companyId === null) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $contractId = $_POST['contract_id'] ?? $data['contract_id'] ?? null;

            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            $contract = $this->model->getById($contractId, $companyId);
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found or unauthorized']);
                return;
            }

            $result = $this->model->deleteWithMilestones($contractId, $companyId);

            if ($result) {
                if (!empty($contract['quotation_id'])) {
                    $stmt = $this->pdo->prepare("UPDATE companyquotation SET status = 'accepted' WHERE quotation_id = ? AND status IN ('accepted', 'successful')");
                    $stmt->execute([(int)$contract['quotation_id']]);
                }

                echo json_encode([
                    'success' => true,
                    'message' => 'Contract cancelled successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to cancel contract']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in cancelContract: " . $e->getMessage());
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
                $customerId = (int)($contract['customer_id'] ?? 0);
                $companyId = (int)($contract['company_id'] ?? $companyId);
                $projectTitle = (string)($contract['project_title'] ?? ('Contract #' . $contractId));
                if ($customerId > 0) {
                    $this->notifier->notify(
                        'Contract sent for review',
                        "A contract for {$projectTitle} was sent to you for review.",
                        'user',
                        $customerId,
                        ['role' => 'company', 'id' => $companyId, 'name' => 'Company']
                    );
                }

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
                    q.labor_unit_label,
                    q.material_unit_label,
                    q.warranty_period,
                    q.payment_terms,
                    q.additional_terms,
                    r.address as request_address,
                    r.district as request_district,
                    r.address as location,
                    r.district,
                    r.title as request_title,
                    cat.name as category_name,
                    cat.name as request_category_name,
                    u.user_id as customer_id,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    u.address as customer_address,
                    u.district as customer_district,
                    COALESCE(comp.name, CONCAT(u.f_name, ' ', u.l_name)) as company_name,
                    comp.registration_no as company_registration,
                    COALESCE(c_loc.address, comp.address, uc.address) as company_address,
                    comp.contact_no as company_contact,
                    COALESCE(comp.email, uc.email) as company_email
                FROM companyquotation q
                INNER JOIN jobrequest r ON q.request_id = r.request_id
                LEFT JOIN category cat ON r.category_id = cat.category_id
                INNER JOIN user u ON r.user_id = u.user_id
                LEFT JOIN user uc ON uc.user_id = COALESCE(q.company_id, q.user_id)
                LEFT JOIN company comp ON comp.company_id = COALESCE(q.company_id, q.user_id)
                LEFT JOIN location c_loc ON comp.location_id = c_loc.location_id
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
            
            echo json_encode([
                'success' => true,
                'data' => $quotations
            ]);

        } catch (Exception $e) {
            error_log("[ContractController] Error in getAcceptedQuotations: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Customer accepts a quotation
     * - Validates ownership
     * - Updates quotation status
     * - Creates contract
     */
    public function acceptQuotation($quotationId, $customerId) {
        try {
            global $pdo;

            // 1. Validate quotation exists and belongs to a job request owned by the customer
            $stmt = $pdo->prepare("
                SELECT q.*, r.user_id as request_owner_id, r.request_id
                FROM companyquotation q
                JOIN jobrequest r ON q.request_id = r.request_id
                WHERE q.quotation_id = ?
            ");
            $stmt->execute([$quotationId]);
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$quotation) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Quotation not found']);
                return;
            }

            if ($quotation['request_owner_id'] != $customerId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized: You do not own this job request']);
                return;
            }

            if ($quotation['status'] !== 'pending') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation is not in pending status']);
                return;
            }

            // 2. Begin Transaction
            $pdo->beginTransaction();

            // 3. Update Quotation Status to 'accepted'
            $updateStmt = $pdo->prepare("UPDATE companyquotation SET status = 'accepted' WHERE quotation_id = ?");
            $updateStmt->execute([$quotationId]);

            // 4. Reject all other quotations for this request? 
            // Usually valid, but multiple quotes might be accepted for different parts? 
            // For now, let's assume one quote per job.
            // OPTIONAL: Mark others as rejected. logic omitted for flexibility.

            // 5. Create Contract
            $result = $this->model->createFromQuotation($quotationId);

            if ($result['success']) {
                $pdo->commit();
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation accepted and contract created successfully',
                    'contract_id' => $result['contract_id']
                ]);
            } else {
                $pdo->rollBack();
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create contract: ' . ($result['message'] ?? 'Unknown error')]);
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("[ContractController] Error in acceptQuotation: " . $e->getMessage());
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

        $start    = new DateTime($startDate);
        $end      = new DateTime($endDate);
        $duration = $start->diff($end)->days;

        switch ($paymentMethod) {
            case 'full_upfront':
                // Legacy: single payment at project start
                $milestones[] = [
                    'milestone_number'    => 1,
                    'title'               => 'Full Payment',
                    'description'         => 'Complete project payment upfront',
                    'due_date'            => $startDate,
                    'amount'              => $totalAmount,
                    'percentage'          => 100
                ];
                break;

            case 'milestone_based':
                // New model: NO upfront. All 3 stages billed upon completion + customer approval.
                // Amounts are estimates; actual billing uses unit_rate × actual_quantity.
                $midDate = clone $start;
                $midDate->add(new DateInterval('P' . max(1, floor($duration / 2)) . 'D'));

                $milestones[] = [
                    'milestone_number'    => 1,
                    'title'               => 'Stage 1 — Foundation & Setup',
                    'description'         => 'Initial preparation and setup work. Payment triggered after actual units verified.',
                    'due_date'            => $midDate->format('Y-m-d'),
                    'amount'              => $totalAmount * 0.40,
                    'percentage'          => 40
                ];

                $milestones[] = [
                    'milestone_number'    => 2,
                    'title'               => 'Stage 2 — Core Work',
                    'description'         => 'Main body of work. Payment triggered after actual units verified.',
                    'due_date'            => $midDate->format('Y-m-d'),
                    'amount'              => $totalAmount * 0.35,
                    'percentage'          => 35
                ];

                $milestones[] = [
                    'milestone_number'    => 3,
                    'title'               => 'Stage 3 — Completion & Handover',
                    'description'         => 'Final stage, completion and handover. Payment triggered after customer confirmation.',
                    'due_date'            => $endDate,
                    'amount'              => $totalAmount * 0.25,
                    'percentage'          => 25
                ];
                break;

            case 'completion':
                // 100% payment after full project completion
                $milestones[] = [
                    'milestone_number'    => 1,
                    'title'               => 'Final Payment on Completion',
                    'description'         => 'Full project payment after completion and customer verification.',
                    'due_date'            => $endDate,
                    'amount'              => $totalAmount,
                    'percentage'          => 100
                ];
                break;

            case 'time_material':
                // Dynamic: billing happens per verified time/material log
                // Generate 3 review checkpoints for the customer to approve logged work
                $step1 = clone $start;
                $step1->add(new DateInterval('P' . max(1, intval($duration / 3)) . 'D'));
                $step2 = clone $start;
                $step2->add(new DateInterval('P' . max(2, intval($duration * 2 / 3)) . 'D'));

                $milestones[] = [
                    'milestone_number'    => 1,
                    'title'               => 'T&M Review Checkpoint 1',
                    'description'         => 'First billing review: company submits hours/materials used. Amount calculated from actual units.',
                    'due_date'            => $step1->format('Y-m-d'),
                    'amount'              => 0,
                    'percentage'          => 0
                ];
                $milestones[] = [
                    'milestone_number'    => 2,
                    'title'               => 'T&M Review Checkpoint 2',
                    'description'         => 'Second billing review: company submits hours/materials used. Amount calculated from actual units.',
                    'due_date'            => $step2->format('Y-m-d'),
                    'amount'              => 0,
                    'percentage'          => 0
                ];
                $milestones[] = [
                    'milestone_number'    => 3,
                    'title'               => 'T&M Final Billing',
                    'description'         => 'Final billing review on project close-out.',
                    'due_date'            => $endDate,
                    'amount'              => 0,
                    'percentage'          => 0
                ];
                break;

            // Legacy cases (kept for backward compatibility)
            case '50_50':
                $milestones[] = ['milestone_number' => 1, 'title' => 'Initial Payment (50%)', 'description' => 'First half at project start', 'due_date' => $startDate, 'amount' => $totalAmount * 0.50, 'percentage' => 50];
                $milestones[] = ['milestone_number' => 2, 'title' => 'Final Payment (50%)',   'description' => 'Second half upon completion', 'due_date' => $endDate,   'amount' => $totalAmount * 0.50, 'percentage' => 50];
                break;

            case '30_70':
                $milestones[] = ['milestone_number' => 1, 'title' => 'Initial Payment (30%)', 'description' => 'Advance at project start',    'due_date' => $startDate, 'amount' => $totalAmount * 0.30, 'percentage' => 30];
                $milestones[] = ['milestone_number' => 2, 'title' => 'Final Payment (70%)',   'description' => 'Completion payment on delivery', 'due_date' => $endDate,  'amount' => $totalAmount * 0.70, 'percentage' => 70];
                break;

            default:
                // Fallback: single payment at the end
                $milestones[] = [
                    'milestone_number'    => 1,
                    'title'               => 'Payment on Completion',
                    'description'         => 'Full payment after project completion',
                    'due_date'            => $endDate,
                    'amount'              => $totalAmount,
                    'percentage'          => 100
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
            $projectTitle = trim($_POST['project_title'] ?? '');
            $projectLocation = trim($_POST['project_location'] ?? '');
            $projectDescription = trim($_POST['project_description'] ?? '');
            $projectType = trim($_POST['project_type'] ?? '');
            $totalBudget = $_POST['total_budget'] ?? null;
            $budgetType = $_POST['budget_type'] ?? null;
            $budgetMin = $_POST['budget_min'] ?? null;
            $budgetMax = $_POST['budget_max'] ?? null;
            $paymentMethod = $_POST['payment_method'] ?? null;
            $pricingType = $_POST['pricing_type'] ?? null;
            $hourlyRate = $_POST['hourly_rate'] ?? null;
            $spendingCap = $_POST['spending_cap'] ?? null;
            
            if (!$quotationId || !$startDate || !$endDate) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                return;
            }

            global $pdo;
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Get quotation + request details
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

                // Prevent duplicates
                $checkStmt = $pdo->prepare("SELECT contract_id FROM contract WHERE quotation_id = ? LIMIT 1");
                $checkStmt->execute([$quotationId]);
                if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
                    throw new Exception('Contract already exists for this quotation');
                }

                // Get job request details for defaults (title/location/description)
                $reqStmt = $pdo->prepare(" 
                    SELECT jr.title, jr.description, jr.address, jr.district, c.name AS category_name
                    FROM jobrequest jr
                    LEFT JOIN category c ON jr.category_id = c.category_id
                    WHERE jr.request_id = ?
                ");
                $reqStmt->execute([$quotation['request_id']]);
                $request = $reqStmt->fetch(PDO::FETCH_ASSOC) ?: [];

                // Normalize/derive values
                $resolvedProjectTitle = $projectTitle !== '' ? $projectTitle : ($request['title'] ?? $quotation['title'] ?? 'Project');
                $resolvedProjectDescription = $projectDescription !== ''
                    ? $projectDescription
                    : ($request['description'] ?? $quotation['description'] ?? null);
                $resolvedProjectLocation = $projectLocation !== ''
                    ? $projectLocation
                    : trim(($request['address'] ?? '') . (isset($request['district']) ? (', ' . $request['district']) : ''));
                if ($resolvedProjectLocation === '') {
                    $resolvedProjectLocation = $request['district'] ?? 'N/A';
                }
                $resolvedProjectType = $projectType !== '' ? $projectType : ($request['category_name'] ?? null);

                $resolvedTotalBudget = $totalBudget !== null && $totalBudget !== '' ? (float)$totalBudget : (float)$quotation['total_amount'];
                $resolvedBudgetType = $budgetType ?: ($quotation['budget_type'] ?? 'fixed');
                $resolvedBudgetMin = ($budgetMin !== null && $budgetMin !== '')
                    ? (float)$budgetMin
                    : ((isset($quotation['budget_min']) && $quotation['budget_min'] !== '' && $quotation['budget_min'] !== null) ? (float)$quotation['budget_min'] : null);
                $resolvedBudgetMax = ($budgetMax !== null && $budgetMax !== '')
                    ? (float)$budgetMax
                    : ((isset($quotation['budget_max']) && $quotation['budget_max'] !== '' && $quotation['budget_max'] !== null) ? (float)$quotation['budget_max'] : null);
                $resolvedPaymentMethod = $paymentMethod ?: ($quotation['payment_method'] ?? 'milestone_based');
                $resolvedPricingType = $pricingType ?: ($quotation['pricing_type'] ?? 'fixed_price');
                $resolvedHourlyRate = ($hourlyRate !== null && $hourlyRate !== '')
                    ? (float)$hourlyRate
                    : ((isset($quotation['hourly_rate']) && $quotation['hourly_rate'] !== '' && $quotation['hourly_rate'] !== null) ? (float)$quotation['hourly_rate'] : null);

                $resolvedSpendingCap = null;
                if ($resolvedPricingType === 'time_and_material') {
                    if ($spendingCap !== null && $spendingCap !== '') {
                        $resolvedSpendingCap = (float)$spendingCap;
                    } else {
                        $resolvedSpendingCap = $resolvedTotalBudget * 1.10;
                    }
                }

                $milestonePlan = ($resolvedPaymentMethod === 'milestone_based') ? 1 : 0;

                // Create the linked project first (required by contract.project_id FK)
                $projStmt = $pdo->prepare("
                    INSERT INTO project (
                        company_id, customer_id, title, description, project_type, location,
                        budget, start_date, end_date, status, progress
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?, 'planned', 0
                    )
                ");
                $projStmt->execute([
                    $companyId,
                    $quotation['customer_id'],
                    $resolvedProjectTitle,
                    $resolvedProjectDescription,
                    $resolvedProjectType,
                    $resolvedProjectLocation,
                    $resolvedTotalBudget,
                    $startDate,
                    $endDate
                ]);

                $projectId = $pdo->lastInsertId();
                
                // Generate contract number
                $contractNumber = $this->generateContractNumber();
                
                // Create contract
                $stmt = $pdo->prepare("
                    INSERT INTO contract (
                        contract_number, quotation_id, company_id, customer_id, job_request_id, project_id,
                        project_title, project_location, project_description,
                        milestone_plan,
                        total_budget, budget_type, budget_min, budget_max,
                        payment_method, pricing_type, hourly_rate, spending_cap,
                        start_date, end_date, contract_date,
                        user_signature, company_signature,
                        amount_pending, payment_status,
                        status, terms_accepted
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?,
                        ?, ?, ?,
                        ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?,
                        ?, 'pending',
                        'draft', FALSE
                    )
                ");
                
                $stmt->execute([
                    $contractNumber,
                    $quotationId,
                    $companyId,
                    $quotation['customer_id'],
                    $quotation['request_id'],
                    $projectId,
                    $resolvedProjectTitle,
                    $resolvedProjectLocation,
                    $resolvedProjectDescription,
                    $milestonePlan,
                    $resolvedTotalBudget,
                    $resolvedBudgetType,
                    $resolvedBudgetMin,
                    $resolvedBudgetMax,
                    $resolvedPaymentMethod,
                    $resolvedPricingType,
                    $resolvedHourlyRate,
                    $resolvedSpendingCap,
                    $startDate,
                    $endDate,
                    date('Y-m-d'),
                    'PENDING',
                    'PENDING',
                    $resolvedTotalBudget
                ]);
                
                $contractId = $pdo->lastInsertId();
                
                // Generate and insert milestones
                $milestones = $this->generateMilestones(
                    $resolvedTotalBudget,
                    $resolvedPaymentMethod,
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
                    SET status = 'successful' 
                    WHERE quotation_id = ?
                ");
                $stmt->execute([$quotationId]);

                // Update job request status
                $stmt = $pdo->prepare("UPDATE jobrequest SET status = 'in_progress' WHERE request_id = ?");
                $stmt->execute([$quotation['request_id']]);
                
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

    /**
     * Download contract as PDF
     */
    public function downloadContractPDF() {
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

            // Get contract with all details
            $contract = $this->model->getById($contractId, $companyId);
            
            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }

            // Get milestones if exists
            $milestones = [];
            if ($contract['milestone_plan']) {
                try {
                    global $pdo;
                    $stmt = $pdo->prepare("SELECT * FROM Milestones WHERE contract_id = ? ORDER BY milestone_number");
                    $stmt->execute([$contractId]);
                    $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    error_log("[ContractController] Error fetching milestones: " . $e->getMessage());
                }
            }

            // Generate HTML for PDF
            $html = $this->generateContractHTML($contract, $milestones);
            
            // Generate PDF using browser print
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            
            // Log the download
            $this->logAuditEvent($contractId, null, 'contract_downloaded', $companyId, 'company', 'Contract PDF downloaded');

        } catch (Exception $e) {
            error_log("[ContractController] Error in downloadContractPDF: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate HTML for contract PDF (matching modal preview)
     */
    private function generateContractHTML($contract, $milestones = []) {
        $contractNumber = 'CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT);
        $clientName = $contract['customer_fname'] . ' ' . $contract['customer_lname'];
        
        // Format dates
        $formatDate = function($d) {
            if (!$d) return '—';
            $dt = new DateTime($d);
            return $dt->format('F d, Y');
        };

        $formatDateTime = function($d) {
            if (!$d) return '—';
            $dt = new DateTime($d);
            return $dt->format('F d, Y g:i A');
        };

        $customerSignatureMeta = null;
        if (!empty($contract['customer_signature']) && is_string($contract['customer_signature'])) {
            $decoded = json_decode($contract['customer_signature'], true);
            if (is_array($decoded)) {
                $customerSignatureMeta = $decoded;
            }
        }
        
        // Feed a single shared renderer on the frontend so the printable view
        // stays identical to the in-app previews.
        $contractForPreview = $contract;
        $contractForPreview['contract_number'] = $contractNumber;
        $contractForPreview['status'] = $contract['contract_status'] ?? ($contract['status'] ?? 'draft');
        $contractForPreview['milestones'] = $milestones;

        ob_start();
        ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contract <?php echo htmlspecialchars($contractNumber); ?></title>
    <style>
        @media print {
            @page { 
                margin: 1.5cm;
                size: A4;
            }
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background: white;
            padding: 20px;
        }
        .contract-preview {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 40px;
        }
        .preview-header {
            text-align: center;
            border-bottom: 3px solid #0f766e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .preview-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #0f766e;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .preview-ref {
            font-size: 14px;
            color: #64748b;
            font-style: italic;
            margin: 8px 0;
        }
        .preview-date {
            font-size: 14px;
            color: #475569;
            margin: 8px 0;
        }
        .contract-status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 12px;
        }
        .contract-status-badge.draft { background: #fef3c7; color: #92400e; }
        .contract-status-badge.sent { background: #dbeafe; color: #1e40af; }
        .contract-status-badge.active { background: #d1fae5; color: #065f46; }
        .contract-status-badge.in_progress { background: #dbeafe; color: #1e40af; }
        .contract-status-badge.milestone_pending { background: #fed7aa; color: #9a3412; }
        .contract-status-badge.completed { background: #e0e7ff; color: #3730a3; }
        .contract-status-badge.terminated { background: #fee2e2; color: #991b1b; }
        .contract-status-badge.disputed { background: #fecaca; color: #7f1d1d; }
        
        .preview-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .preview-section h4 {
            font-size: 16px;
            font-weight: 700;
            color: #0f766e;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #99f6e4;
        }
        .preview-parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 12px;
        }
        .preview-parties > div {
            padding: 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .preview-parties strong {
            display: block;
            color: #0f766e;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .preview-parties span {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }
        .preview-parties small {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }
        .preview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 12px;
        }
        .preview-grid.cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }
        .preview-grid > div {
            padding: 10px 12px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .preview-grid strong {
            display: block;
            color: #475569;
            font-size: 12px;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .preview-grid span {
            color: #1e293b;
            font-size: 14px;
            font-weight: 500;
        }
        .preview-value {
            color: #059669 !important;
            font-weight: 700 !important;
            font-size: 16px !important;
        }
        .preview-pre {
            white-space: pre-wrap;
            font-family: inherit;
            background: white;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #475569;
            margin-top: 6px;
        }
        .preview-milestones-table {
            width: 100%;
            margin-top: 12px;
            border-collapse: collapse;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .preview-milestones-table thead {
            background: #f1f5f9;
        }
        .preview-milestones-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        .preview-milestones-table td {
            padding: 10px 12px;
            font-size: 13px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .preview-milestones-table tr:last-child td {
            border-bottom: none;
        }
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .action-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }
        .btn-print {
            background: #0f766e;
            color: white;
        }
        .btn-print:hover {
            background: #0d9488;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 118, 110, 0.3);
        }
        .btn-close {
            background: #64748b;
            color: white;
        }
        .btn-close:hover {
            background: #475569;
        }

        /* Minimal helpers used by shared renderer */
        .preview-muted { color: #64748b; font-style: italic; }
        .preview-paragraph { margin-top: 12px; padding: 12px; background: white; border-radius: 6px; border: 1px solid #e2e8f0; color: #475569; }
        .preview-schedule-title { margin-top: 0; margin-bottom: 10px; color: #0f766e; font-weight: 700; }
    </style>
</head>
<body>
    <div id="contractPreviewHost"></div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/shared/contract-preview.js?v=1.1"></script>
    <script>
        (function () {
            const data = <?php echo json_encode($contractForPreview, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
            const host = document.getElementById('contractPreviewHost');
            if (!host || !window.ContractPreview || typeof window.ContractPreview.renderHTML !== 'function') {
                if (host) host.textContent = 'Preview unavailable';
                return;
            }

            const map = { full_upfront: 'Full Upfront', milestone_based: 'Milestone-Based', '50_50': '50/50 Split', '30_70': '30/70 Split', completion: 'On Completion' };
            const method = data && data.payment_method;

            host.innerHTML = window.ContractPreview.renderHTML(data, {
                isMilestoneBased: method === 'milestone_based',
                paymentLabel: map[method] || 'Standard',
                renderMilestoneAction: function () { return '—'; }
            });
        })();
    </script>

    <div class="no-print">
        <button class="action-btn btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M5 1a2 2 0 0 0-2 2v1h10V3a2 2 0 0 0-2-2H5z"/>
                <path d="M0 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1v-2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2H2a2 2 0 0 1-2-2V7zm2.5 1a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 10a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-3z"/>
            </svg>
            Print / Save as PDF
        </button>
        <button class="action-btn btn-close" onclick="window.close()">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
            </svg>
            Close
        </button>
    </div>
</body>
</html>
        <?php
        return ob_get_clean();
    }

    // ========================================
    // PHASE 2: UNDO WINDOW (24-hour cancellation)
    // ========================================

    /**
     * Customer responds to contract (Accept/Decline)
     */
    public function respondToContract() {
        try {
            // Validate session
            if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), ['customer', 'user'], true)) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;

            $contractId = $data['contract_id'] ?? null;
            $response = $data['response'] ?? null;
            $esignConsentRaw = $data['esign_consent'] ?? null;

            if (!$contractId || !$response) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing contract ID or response']);
                return;
            }

            if ($response === 'accepted') {
                $esignConsent = filter_var($esignConsentRaw, FILTER_VALIDATE_BOOLEAN);
                if ($esignConsent !== true) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Electronic signature confirmation is required to accept this contract.']);
                    return;
                }
            }

            $this->pdo->beginTransaction();

            if ($response === 'accepted') {
                // Build e-sign metadata (acceptance confirmation acts as signature)
                $custStmt = $this->pdo->prepare("SELECT f_name, l_name, email FROM user WHERE user_id = ?");
                $custStmt->execute([$_SESSION['user_id']]);
                $cust = $custStmt->fetch(PDO::FETCH_ASSOC) ?: [];

                $signatureName = trim(($cust['f_name'] ?? '') . ' ' . ($cust['l_name'] ?? ''));
                if ($signatureName === '') {
                    $signatureName = 'Customer';
                }

                // Hash core contract fields for tamper-evident audit
                $hashStmt = $this->pdo->prepare("
                    SELECT
                        contract_id, contract_number, quotation_id, company_id, customer_id, job_request_id, project_id,
                        project_title, project_reference, project_location, project_description,
                        scope_description, scope_inclusions, scope_exclusions, scope_standards, materials_responsibility,
                        milestone_plan, total_budget, budget_type, budget_min, budget_max, tax_inclusive, payment_method,
                        advance_payment_pct, pricing_type, hourly_rate, spending_cap,
                        start_date, end_date, contract_date, terms_conditions
                    FROM contract
                    WHERE contract_id = ? AND customer_id = ?
                ");
                $hashStmt->execute([$contractId, $_SESSION['user_id']]);
                $hashRow = $hashStmt->fetch(PDO::FETCH_ASSOC);
                if (!$hashRow) {
                    throw new Exception("Contract not found or not authorized to accept.");
                }

                $contractHash = hash('sha256', json_encode($hashRow, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

                $signatureMeta = [
                    'type' => 'accept_confirmation',
                    'signed_by_user_id' => (int)$_SESSION['user_id'],
                    'signed_by_name' => $signatureName,
                    'signed_by_email' => $cust['email'] ?? null,
                    'signed_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                    'signed_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                    'signed_at' => gmdate('c'),
                    'contract_hash' => $contractHash
                ];

                // Update contract to active
                $stmt = $this->pdo->prepare("
                    UPDATE contract 
                    SET status = 'active', 
                        terms_accepted = 1, 
                        terms_accepted_at = NOW(),
                        customer_response = 'accepted', 
                        customer_response_at = NOW(),
                        user_signature = 'E-SIGNED (ACCEPTED)',
                        customer_signature = ?,
                        signed_at = NOW(),
                        locked = 1
                    WHERE contract_id = ? AND customer_id = ?
                ");
                $stmt->execute([
                    json_encode($signatureMeta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                    $contractId,
                    $_SESSION['user_id']
                ]);

                if ($stmt->rowCount() === 0) {
                    throw new Exception("Contract not found or not authorized to accept.");
                }



                $this->addTimelineEvent($contractId, 'contract_accepted', 'Contract accepted by customer');

                // Audit log entry for e-signing
                $this->logAuditEvent(
                    $contractId,
                    null,
                    'contract_esigned',
                    $_SESSION['user_id'],
                    'customer',
                    json_encode([
                        'message' => 'Customer accepted and electronically signed via acceptance confirmation',
                        'signature' => $signatureMeta
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                );

                $metaStmt = $this->pdo->prepare("SELECT company_id, customer_id, project_title FROM contract WHERE contract_id = ? LIMIT 1");
                $metaStmt->execute([$contractId]);
                $contractMeta = $metaStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                $companyId = (int)($contractMeta['company_id'] ?? 0);
                $customerId = (int)($contractMeta['customer_id'] ?? 0);
                $projectTitle = (string)($contractMeta['project_title'] ?? ('Contract #' . $contractId));

                if ($companyId > 0) {
                    $this->notifier->notify(
                        'Contract accepted',
                        "Customer accepted the contract for {$projectTitle}.",
                        'company',
                        $companyId,
                        ['role' => 'user', 'id' => (int)$_SESSION['user_id'], 'name' => 'Customer']
                    );
                }
                if ($customerId > 0) {
                    $this->notifier->notify(
                        'Contract accepted',
                        "You accepted the contract for {$projectTitle}.",
                        'user',
                        $customerId,
                        ['role' => 'user', 'id' => (int)$_SESSION['user_id'], 'name' => 'Customer']
                    );
                }

            } else if ($response === 'rejected') {
                // Reject contract
                $stmt = $this->pdo->prepare("
                    UPDATE contract 
                    SET status = 'terminated', 
                        customer_response = 'rejected', 
                        customer_response_at = NOW() 
                    WHERE contract_id = ? AND customer_id = ?
                ");
                $stmt->execute([$contractId, $_SESSION['user_id']]);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception("Contract not found or not authorized to reject.");
                }

                $this->addTimelineEvent($contractId, 'contract_rejected', 'Contract rejected by customer');

                $metaStmt = $this->pdo->prepare("SELECT company_id, customer_id, project_title FROM contract WHERE contract_id = ? LIMIT 1");
                $metaStmt->execute([$contractId]);
                $contractMeta = $metaStmt->fetch(PDO::FETCH_ASSOC) ?: [];
                $companyId = (int)($contractMeta['company_id'] ?? 0);
                $customerId = (int)($contractMeta['customer_id'] ?? 0);
                $projectTitle = (string)($contractMeta['project_title'] ?? ('Contract #' . $contractId));

                if ($companyId > 0) {
                    $this->notifier->notify(
                        'Contract rejected',
                        "Customer rejected the contract for {$projectTitle}.",
                        'company',
                        $companyId,
                        ['role' => 'user', 'id' => (int)$_SESSION['user_id'], 'name' => 'Customer']
                    );
                }
                if ($customerId > 0) {
                    $this->notifier->notify(
                        'Contract rejected',
                        "You rejected the contract for {$projectTitle}.",
                        'user',
                        $customerId,
                        ['role' => 'user', 'id' => (int)$_SESSION['user_id'], 'name' => 'Customer']
                    );
                }
            } else {
                throw new Exception("Invalid response value: " . $response);
            }

            $this->pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Contract ' . $response . ' successfully.'
            ]);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("[ContractController] respondToContract error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Atomic Pay & Accept flow
     */
    public function payAndAccept() {
        try {
            if (!isset($_SESSION['user_id']) || !in_array(($_SESSION['user_role'] ?? ''), ['customer', 'user'], true)) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $contractId = $data['contract_id'] ?? null;

            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing contract ID']);
                return;
            }

            $this->pdo->beginTransaction();

            // 1. Get contract and check upfront requirements
            $contract = $this->model->getByIdForCustomer($contractId, $_SESSION['user_id']);
            if (!$contract) {
                throw new Exception("Contract not found.");
            }

            // Get initial milestones (due on or before start_date)
            $stmt = $this->pdo->prepare("SELECT * FROM contract_milestone WHERE contract_id = ? AND due_date <= ? ORDER BY milestone_number ASC");
            $stmt->execute([$contractId, $contract['start_date']]);
            $initialMilestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalUpfront = 0;
            foreach ($initialMilestones as $ms) {
                $totalUpfront += (float)$ms['amount'];
            }

            // 2. Process Payment (Simulated)
            if ($totalUpfront > 0) {
                $escrowModel = new EscrowModel($this->pdo);
                
                // Add funds to user wallet first (Simulate deposit from card)
                $escrowModel->deposit($_SESSION['user_id'], $totalUpfront, 'user', "Initial deposit for Contract #{$contractId}");
                
                // Then hold for each initial milestone
                foreach ($initialMilestones as $ms) {
                    $success = $escrowModel->holdFundsForMilestone($_SESSION['user_id'], $ms['milestone_id'], $ms['amount']);
                    if (!$success) {
                        throw new Exception("Failed to hold escrow funds for Milestone #{$ms['milestone_id']}");
                    }
                }

                // Record in payment history
                $stmt = $this->pdo->prepare("INSERT INTO contract_payment_history (contract_id, amount, payment_type, status, paid_by, notes) VALUES (?, ?, 'escrow_deposit', 'completed', ?, ?)");
                $stmt->execute([$contractId, $totalUpfront, $_SESSION['user_id'], "Upfront payment for project start"]);
            }

            // 3. Accept Contract (Signature logic)
            $this->forceAcceptContract($contractId, $contract);

            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Payment processed and contract accepted.']);

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("[ContractController] Error in payAndAccept: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function forceAcceptContract($contractId, $contract) {
        // Build e-sign metadata
        $custStmt = $this->pdo->prepare("SELECT f_name, l_name, email FROM user WHERE user_id = ?");
        $custStmt->execute([$_SESSION['user_id']]);
        $cust = $custStmt->fetch(PDO::FETCH_ASSOC) ?: [];
        $signatureName = trim(($cust['f_name'] ?? '') . ' ' . ($cust['l_name'] ?? ''));
        
        $signatureMeta = [
            'type' => 'pay_and_accept_confirmation',
            'signed_by_user_id' => (int)$_SESSION['user_id'],
            'signed_by_name' => $signatureName,
            'signed_at' => gmdate('c'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null
        ];

        $stmt = $this->pdo->prepare("
            UPDATE contract 
            SET status = 'active', 
                terms_accepted = 1, 
                terms_accepted_at = NOW(),
                customer_response = 'accepted', 
                customer_response_at = NOW(),
                user_signature = 'E-SIGNED (PAID & ACCEPTED)',
                customer_signature = ?,
                signed_at = NOW(),
                locked = 1,
                escrow_enabled = 1
            WHERE contract_id = ? AND customer_id = ?
        ");
        $stmt->execute([
            json_encode($signatureMeta),
            $contractId,
            $_SESSION['user_id']
        ]);

        $this->addTimelineEvent($contractId, 'contract_accepted_paid', 'Contract accepted with initial payment received into Escrow');
        
        // Notify
        $projectTitle = $contract['project_title'] ?? ('Contract #' . $contractId);
        $companyId = $contract['company_id'];
        $this->notifier->notify(
            'Contract accepted & paid',
            "Customer accepted and paid upfront for {$projectTitle}. You can now start the job.",
            'company',
            $companyId,
            ['role' => 'user', 'id' => (int)$_SESSION['user_id'], 'name' => 'Customer']
        );
    }

    /**
     * Check if contract has active undo window
     */
    public function checkUndoWindow() {
        $contractId = $_POST['contract_id'] ?? $_GET['contract_id'] ?? null;
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    contract_id,
                    undo_deadline,
                    undo_requested,
                    undo_requested_at,
                    status,
                    TIMESTAMPDIFF(SECOND, NOW(), undo_deadline) as seconds_remaining
                FROM contract
                WHERE contract_id = ?
            ");
            $stmt->execute([$contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract) {
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }
            
            $hasActiveWindow = (
                $contract['undo_deadline'] &&
                strtotime($contract['undo_deadline']) > time() &&
                !$contract['undo_requested']
            );
            
            echo json_encode([
                'success' => true,
                'has_active_window' => $hasActiveWindow,
                'undo_deadline' => $contract['undo_deadline'],
                'seconds_remaining' => max(0, (int)$contract['seconds_remaining']),
                'undo_requested' => (bool)$contract['undo_requested'],
                'undo_requested_at' => $contract['undo_requested_at']
            ]);
            
        } catch (PDOException $e) {
            error_log("Undo window check error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Request contract undo/cancellation
     */
    public function requestUndo() {
        $contractId = $_POST['contract_id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("
                SELECT undo_deadline, undo_requested, status
                FROM contract
                WHERE contract_id = ?
            ");
            $stmt->execute([$contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract || strtotime($contract['undo_deadline']) <= time()) {
                throw new Exception('Undo window has expired');
            }
            
            if ($contract['undo_requested']) {
                throw new Exception('Undo already requested');
            }
            
            $stmt = $this->pdo->prepare("
                UPDATE contract
                SET undo_requested = 1,
                    undo_requested_at = NOW(),
                    undo_reason = ?
                WHERE contract_id = ?
            ");
            $stmt->execute([$reason, $contractId]);
            
            $this->addTimelineEvent($contractId, 'undo_requested', 'Contract cancellation requested');
            $this->createNotification($contractId, 'undo_requested', 'Contract cancellation has been requested');
            
            $this->pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Cancellation request submitted successfully'
            ]);
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Undo request error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ========================================
    // PHASE 2: NOTIFICATIONS SYSTEM
    // ========================================

    public function getNotifications() {
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user_role'] ?? null;
        
        if (!$userId || !$userRole) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT notification_id, contract_id, notification_type, title, message, priority, is_read, created_at,
                       TIMESTAMPDIFF(MINUTE, created_at, NOW()) as minutes_ago
                FROM contract_notifications
                WHERE recipient_type = ? AND recipient_id = ?
                ORDER BY created_at DESC LIMIT 50
            ");
            $stmt->execute([$userRole, $userId]);
            $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as unread_count
                FROM contract_notifications
                WHERE recipient_type = ? AND recipient_id = ? AND is_read = 0
            ");
            $stmt->execute([$userRole, $userId]);
            $unreadCount = $stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => (int)$unreadCount
            ]);
            
        } catch (PDOException $e) {
            error_log("Get notifications error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function markNotificationRead() {
        $notificationId = $_POST['notification_id'] ?? null;
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE contract_notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ?");
            $stmt->execute([$notificationId]);
            echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
        } catch (PDOException $e) {
            error_log("Mark notification read error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function markAllNotificationsRead() {
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user_role'] ?? null;
        
        if (!$userId || !$userRole) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("UPDATE contract_notifications SET is_read = 1, read_at = NOW() WHERE recipient_type = ? AND recipient_id = ? AND is_read = 0");
            $stmt->execute([$userRole, $userId]);
            echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
        } catch (PDOException $e) {
            error_log("Mark all notifications read error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    // ========================================
    // PHASE 2: MILESTONE WORKFLOW
    // ========================================

    public function submitMilestone() {
        $milestoneId     = $_POST['milestone_id']          ?? null;
        $comments        = $_POST['completion_description'] ?? '';
        $actualQuantity  = $_POST['actual_quantity']        ?? null;
        $actualUnitRate  = $_POST['actual_unit_rate']       ?? null;
        $proofFiles      = null; // future: handle file uploads as JSON

        if (!$milestoneId) {
            echo json_encode(['success' => false, 'message' => 'Milestone ID required']);
            return;
        }

        try {
            // Verify this milestone belongs to a contract owned by the logged-in company
            $chk = $this->pdo->prepare("
                SELECT cm.contract_id, cm.title as milestone_name, cm.unit_rate, cm.estimated_quantity,
                       c.project_title, c.customer_id
                FROM contract_milestone cm
                JOIN contract c ON cm.contract_id = c.contract_id
                WHERE cm.milestone_id = ? AND c.company_id = ?
            ");
            $companyId = $this->getCompanyId();
            if (!$companyId) {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            $chk->execute([$milestoneId, $companyId]);
            $m = $chk->fetch(PDO::FETCH_ASSOC);

            if (!$m) {
                echo json_encode(['success' => false, 'message' => 'Milestone not found or unauthorized']);
                return;
            }

            $result = $this->model->markMilestoneCompleted(
                $milestoneId,
                $proofFiles,
                $comments,
                ($actualQuantity !== null && $actualQuantity !== '') ? (float)$actualQuantity : null,
                ($actualUnitRate !== null && $actualUnitRate !== '') ? (float)$actualUnitRate : null
            );

            if (!$result) {
                echo json_encode(['success' => false, 'message' => 'Could not submit milestone (check status)']);
                return;
            }

            // Compute billing preview for response
            $billingPreview = null;
            $effectiveRate = (float)($m['unit_rate'] ?? 0);
            if ($actualUnitRate !== null && $actualUnitRate !== '' && (float)$actualUnitRate > 0) {
                $effectiveRate = (float)$actualUnitRate;
            }
            if ($actualQuantity !== null && $effectiveRate > 0) {
                $billingPreview = $effectiveRate * (float)$actualQuantity;
            }

            $this->addTimelineEvent(
                $m['contract_id'],
                'milestone_submitted',
                "Milestone '{$m['milestone_name']}' submitted for verification"
            );
            $this->createNotification(
                $m['contract_id'],
                'milestone_submitted',
                "Milestone '{$m['milestone_name']}' submitted — please verify the actual units and confirm payment.",
                'customer'
            );

            echo json_encode([
                'success'          => true,
                'message'          => 'Milestone submitted for customer verification',
                'billing_preview'  => $billingPreview,
                'actual_quantity'  => $actualQuantity !== null ? (float)$actualQuantity : null,
                'unit_rate'        => $m['unit_rate'] !== null ? (float)$m['unit_rate'] : null,
                'actual_unit_rate' => $actualUnitRate !== null && $actualUnitRate !== '' ? (float)$actualUnitRate : null,
            ]);
        } catch (Exception $e) {
            error_log("Submit milestone error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    public function approveMilestone() {
        $milestoneId = $_POST['milestone_id'] ?? null;

        if (!$milestoneId) {
            echo json_encode(['success' => false, 'message' => 'Milestone ID required']);
            return;
        }

        // Verify customer owns this contract
        $customerId = $this->getCustomerId();
        if (!$customerId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // Check if this is a contract_milestone (new) or legacy milestone
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contract_milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $isNewMilestone = $stmt->fetchColumn() > 0;

            if ($isNewMilestone) {
                // Verify ownership
                $ownStmt = $this->pdo->prepare("
                    SELECT cm.contract_id, cm.title as milestone_name, c.company_id, c.project_title
                    FROM contract_milestone cm
                    JOIN contract c ON cm.contract_id = c.contract_id
                    WHERE cm.milestone_id = ? AND c.customer_id = ?
                ");
                $ownStmt->execute([$milestoneId, $customerId]);
                $m = $ownStmt->fetch(PDO::FETCH_ASSOC);

                if (!$m) {
                    echo json_encode(['success' => false, 'message' => 'Milestone not found or unauthorized']);
                    return;
                }

                $result = $this->model->approveMilestone($milestoneId);

                if ($result) {
                    $billedAmount = is_array($result) ? ($result['billed_amount'] ?? 0) : 0;

                    $this->addTimelineEvent($m['contract_id'], 'milestone_approved', "Milestone '{$m['milestone_name']}' verified and approved by customer");
                    $this->createNotification(
                        $m['contract_id'],
                        'milestone_approved',
                        "Milestone '{$m['milestone_name']}' approved — LKR " . number_format($billedAmount, 2) . " recorded as paid.",
                        'company'
                    );

                    echo json_encode([
                        'success'       => true,
                        'message'       => 'Milestone approved and payment recorded',
                        'billed_amount' => $billedAmount,
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to approve milestone']);
                }
            } else {
                // Fallback legacy path
                $this->pdo->beginTransaction();
                $stmt = $this->pdo->prepare("UPDATE milestone SET customer_approved = 1, customer_approved_at = NOW(), customer_verified = 1, customer_verified_at = NOW() WHERE milestone_id = ?");
                $stmt->execute([$milestoneId]);
                $stmt = $this->pdo->prepare("SELECT contract_id, milestone_name, amount FROM milestone WHERE milestone_id = ?");
                $stmt->execute([$milestoneId]);
                $milestone = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($milestone) {
                    $this->addTimelineEvent($milestone['contract_id'], 'milestone_approved', "Milestone '{$milestone['milestone_name']}' approved");
                }
                $this->pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Milestone approved successfully']);
            }
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Approve milestone error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function rejectMilestone() {
        $milestoneId = $_POST['milestone_id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        
        if (!$milestoneId || !$reason) {
            echo json_encode(['success' => false, 'message' => 'Milestone ID and reason required']);
            return;
        }
        
        // Verify customer owns this contract
        $customerId = $this->getCustomerId();
        if (!$customerId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        try {
            // Check if this is a contract_milestone (new) or legacy milestone
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM contract_milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $isNewMilestone = $stmt->fetchColumn() > 0;

            if ($isNewMilestone) {
                // Verify ownership (customer)
                $ownStmt = $this->pdo->prepare("
                    SELECT cm.contract_id, cm.title as milestone_name, c.company_id
                    FROM contract_milestone cm
                    JOIN contract c ON cm.contract_id = c.contract_id
                    WHERE cm.milestone_id = ? AND c.customer_id = ?
                ");
                $ownStmt->execute([$milestoneId, $customerId]);
                $m = $ownStmt->fetch(PDO::FETCH_ASSOC);

                if (!$m) {
                    echo json_encode(['success' => false, 'message' => 'Milestone not found or unauthorized']);
                    return;
                }

                $result = $this->model->rejectMilestone($milestoneId, $reason);
                if (!$result) {
                    echo json_encode(['success' => false, 'message' => 'Failed to reject milestone']);
                    return;
                }

                $this->addTimelineEvent($m['contract_id'], 'milestone_rejected', "Milestone '{$m['milestone_name']}' rejected: {$reason}");
                $this->createNotification($m['contract_id'], 'milestone_rejected', "Milestone '{$m['milestone_name']}' was rejected", 'company');

                echo json_encode(['success' => true, 'message' => 'Milestone rejected']);
                return;
            }

            // Legacy milestone path (older tables)
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE milestone SET customer_approved = 0, rejection_reason = ?, rejected_at = NOW(), submitted_for_approval = 0 WHERE milestone_id = ?");
            $stmt->execute([$reason, $milestoneId]);

            $stmt = $this->pdo->prepare("SELECT contract_id, milestone_name FROM milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($milestone) {
                $this->addTimelineEvent($milestone['contract_id'], 'milestone_rejected', "Milestone '{$milestone['milestone_name']}' rejected: {$reason}");
                $this->createNotification($milestone['contract_id'], 'milestone_rejected', "Milestone '{$milestone['milestone_name']}' was rejected", 'company');
            }

            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Milestone rejected']);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Reject milestone error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function markWorkStarted() {
        $milestoneId = $_POST['milestone_id'] ?? null;
        
        if (!$milestoneId) {
            echo json_encode(['success' => false, 'message' => 'Milestone ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("UPDATE milestone SET work_started = 1, work_started_at = NOW() WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            
            $stmt = $this->pdo->prepare("SELECT contract_id, milestone_name FROM milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->addTimelineEvent($milestone['contract_id'], 'work_started', "Work started on milestone '{$milestone['milestone_name']}'");
            $this->createNotification($milestone['contract_id'], 'work_started', "Work has started on milestone '{$milestone['milestone_name']}'", 'customer');
            
            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Work marked as started']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Mark work started error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function markWorkCompleted() {
        $milestoneId = $_POST['milestone_id'] ?? null;
        
        if (!$milestoneId) {
            echo json_encode(['success' => false, 'message' => 'Milestone ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("UPDATE milestone SET work_completed = 1, work_completed_at = NOW() WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            
            $stmt = $this->pdo->prepare("SELECT contract_id, milestone_name FROM milestone WHERE milestone_id = ?");
            $stmt->execute([$milestoneId]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->addTimelineEvent($milestone['contract_id'], 'work_completed', "Work completed on milestone '{$milestone['milestone_name']}'");
            $this->createNotification($milestone['contract_id'], 'work_completed', "Work completed on milestone '{$milestone['milestone_name']}'", 'customer');
            
            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Work marked as completed']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Mark work completed error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    // ========================================
    // PHASE 4: MILESTONE PLAN MANAGEMENT
    // ========================================

    /**
     * Submit milestone plan for a contract (Task 1.1)
     * Company creates 2-10 milestones with percentages totaling 100%
     */
    public function submitMilestonePlan($contract_id, $milestones) {
        try {
            // Validation 1: Check contract exists and is milestone-based
            $stmt = $this->pdo->prepare("
                SELECT contract_id, payment_method, start_date, end_date, total_budget, status
                FROM contract 
                WHERE contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found'];
            }
            
            if ($contract['payment_method'] !== 'milestone') {
                return ['success' => false, 'message' => 'This contract does not use milestone-based payment'];
            }
            
            // Validation 2: Minimum 2 milestones required
            if (count($milestones) < 2) {
                return ['success' => false, 'message' => 'Minimum 2 milestones required'];
            }
            
            // Validation 3: Maximum 10 milestones
            if (count($milestones) > 10) {
                return ['success' => false, 'message' => 'Maximum 10 milestones allowed'];
            }
            
            // Validation 4: Percentages must total 100%
            $totalPercentage = 0;
            foreach ($milestones as $milestone) {
                $totalPercentage += floatval($milestone['percentage']);
            }
            
            if (abs($totalPercentage - 100) > 0.01) { // Allow 0.01% tolerance for rounding
                return ['success' => false, 'message' => 'Milestone percentages must total 100% (currently: ' . $totalPercentage . '%)'];
            }
            
            // Validation 5: Timeline must fit within contract duration
            $contractStart = new DateTime($contract['start_date']);
            $contractEnd = new DateTime($contract['end_date']);
            
            foreach ($milestones as $milestone) {
                if (isset($milestone['planned_end_date'])) {
                    $milestoneEnd = new DateTime($milestone['planned_end_date']);
                    if ($milestoneEnd > $contractEnd) {
                        return ['success' => false, 'message' => 'Milestone "' . $milestone['name'] . '" end date exceeds contract end date'];
                    }
                }
            }
            
            // Begin transaction
            $this->pdo->beginTransaction();
            
            // Check if plan already exists
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM contract_milestones 
                WHERE contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing['count'] > 0) {
                // Delete existing plan
                $stmt = $this->pdo->prepare("DELETE FROM contract_milestones WHERE contract_id = ?");
                $stmt->execute([$contract_id]);
            }
            
            // Insert milestones
            $order = 1;
            foreach ($milestones as $milestone) {
                $amount = ($milestone['percentage'] / 100) * $contract['total_budget'];
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO contract_milestones (
                        contract_id,
                        milestone_order,
                        milestone_name,
                        milestone_description,
                        percentage,
                        amount,
                        deliverables,
                        depends_on_milestone,
                        planned_start_date,
                        planned_end_date,
                        status,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_approval', NOW())
                ");
                
                $stmt->execute([
                    $contract_id,
                    $order,
                    $milestone['name'],
                    $milestone['description'] ?? '',
                    $milestone['percentage'],
                    $amount,
                    isset($milestone['deliverables']) ? json_encode($milestone['deliverables']) : null,
                    $milestone['depends_on'] ?? null,
                    $milestone['planned_start_date'] ?? null,
                    $milestone['planned_end_date'] ?? null
                ]);
                
                $order++;
            }
            
            // Update contract status
            $stmt = $this->pdo->prepare("
                UPDATE contract 
                SET status = 'pending_plan_approval',
                    milestone_plan_submitted_at = NOW()
                WHERE contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            
            // Log timeline event
            $this->addTimelineEvent(
                $contract_id, 
                'milestone_plan_submitted', 
                'Milestone plan submitted with ' . count($milestones) . ' milestones for customer approval'
            );
            
            // Notify customer
            $this->createNotification(
                $contract_id,
                'milestone_plan_submitted',
                'A milestone plan has been submitted for your review and approval',
                'customer'
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Milestone plan submitted successfully',
                'milestones_count' => count($milestones)
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Submit milestone plan error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Get milestone plan for a contract (Task 1.2)
     */
    public function getMilestonePlan($contract_id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    milestone_id,
                    milestone_order,
                    milestone_name,
                    milestone_description,
                    percentage,
                    amount,
                    deliverables,
                    depends_on_milestone,
                    planned_start_date,
                    planned_end_date,
                    actual_start_date,
                    actual_end_date,
                    status,
                    completion_proof,
                    customer_approved_at,
                    created_at,
                    updated_at
                FROM contract_milestones
                WHERE contract_id = ?
                ORDER BY milestone_order ASC
            ");
            $stmt->execute([$contract_id]);
            $milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Decode JSON fields
            foreach ($milestones as &$milestone) {
                if ($milestone['deliverables']) {
                    $milestone['deliverables'] = json_decode($milestone['deliverables'], true);
                }
                if ($milestone['completion_proof']) {
                    $milestone['completion_proof'] = json_decode($milestone['completion_proof'], true);
                }
            }
            
            return [
                'success' => true,
                'milestones' => $milestones
            ];
            
        } catch (Exception $e) {
            error_log("Get milestone plan error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    /**
     * Approve milestone plan (Task 1.8 - Customer)
     */
    public function approveMilestonePlan($contract_id, $customer_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Update all milestones to approved
            $stmt = $this->pdo->prepare("
                UPDATE contract_milestones 
                SET status = 'pending',
                    plan_approved_at = NOW(),
                    plan_approved_by = ?
                WHERE contract_id = ?
            ");
            $stmt->execute([$customer_id, $contract_id]);
            
            // Activate first milestone
            $stmt = $this->pdo->prepare("
                UPDATE contract_milestones 
                SET status = 'active',
                    actual_start_date = NOW()
                WHERE contract_id = ?
                ORDER BY milestone_order ASC
                LIMIT 1
            ");
            $stmt->execute([$contract_id]);
            
            // Update contract status
            $stmt = $this->pdo->prepare("
                UPDATE contract 
                SET status = 'active',
                    milestone_plan_approved_at = NOW()
                WHERE contract_id = ?
            ");
            $stmt->execute([$contract_id]);
            
            // Log timeline event
            $this->addTimelineEvent(
                $contract_id,
                'milestone_plan_approved',
                'Milestone plan approved by customer. First milestone is now active.'
            );
            
            // Notify company
            $this->createNotification(
                $contract_id,
                'milestone_plan_approved',
                'Your milestone plan has been approved! You can now start working on the first milestone.',
                'company'
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Milestone plan approved successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Approve milestone plan error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    /**
     * Request changes to milestone plan (Task 1.9 - Customer)
     */
    public function requestMilestonePlanChanges($contract_id, $customer_id, $feedback) {
        try {
            $this->pdo->beginTransaction();
            
            // Update contract status
            $stmt = $this->pdo->prepare("
                UPDATE contract 
                SET status = 'plan_revision_requested',
                    milestone_plan_feedback = ?
                WHERE contract_id = ?
            ");
            $stmt->execute([$feedback, $contract_id]);
            
            // Log timeline event
            $this->addTimelineEvent(
                $contract_id,
                'milestone_plan_revision_requested',
                'Customer requested changes to milestone plan'
            );
            
            // Notify company
            $this->createNotification(
                $contract_id,
                'milestone_plan_revision_requested',
                'Customer has requested changes to the milestone plan. Please review their feedback.',
                'company'
            );
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Revision request submitted successfully'
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Request milestone plan changes error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // ==================== FEATURE 3: UPFRONT PAYMENT WORKFLOW ====================
    
    /**
     * Request upfront payment (company)
     */
    public function requestUpfrontPayment($contract_id, $amount, $payment_type) {
        try {
            $this->pdo->beginTransaction();
            
            // Validate payment type
            if (!in_array($payment_type, ['upfront_50_50', 'upfront_30_70'])) {
                return ['success' => false, 'message' => 'Invalid payment type'];
            }
            
            // Get contract
            $stmt = $this->pdo->prepare("SELECT * FROM contracts WHERE id = ?");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch();
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found'];
            }
            
            // Validate amount matches payment type
            $expected_percentage = ($payment_type === 'upfront_50_50') ? 50 : 30;
            $expected_amount = ($contract['contract_budget'] * $expected_percentage) / 100;
            
            if (abs($amount - $expected_amount) > 1) {
                return ['success' => false, 'message' => 'Amount does not match payment type'];
            }
            
            // Create payment request
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_payments 
                (contract_id, amount, payment_type, payment_method, payment_status, description, created_at)
                VALUES (?, ?, 'upfront', ?, 'pending', ?, NOW())
            ");
            $stmt->execute([
                $contract_id,
                $amount,
                $payment_type,
                "First payment ({$expected_percentage}% upfront)"
            ]);
            
            // Update contract status
            $stmt = $this->pdo->prepare("UPDATE contracts SET contract_status = 'awaiting_upfront_payment' WHERE id = ?");
            $stmt->execute([$contract_id]);
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'payment_requested', "Upfront payment requested: Rs. " . number_format($amount, 2));
            
            // Notify customer
            $this->createNotification(
                $contract_id,
                'customer',
                "Upfront payment of Rs. " . number_format($amount, 2) . " requested"
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Payment request sent'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Request upfront payment error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Submit work start proof (company)
     */
    public function submitWorkStartProof($contract_id, $proof_data) {
        try {
            $this->pdo->beginTransaction();
            
            // Validate required fields
            if (empty($proof_data['photos']) || empty($proof_data['description'])) {
                return ['success' => false, 'message' => 'Photos and description are required'];
            }
            
            // Insert work start proof
            $stmt = $this->pdo->prepare("
                UPDATE contracts 
                SET work_start_proof = ?,
                    work_start_proof_submitted_at = NOW(),
                    contract_status = 'awaiting_work_verification'
                WHERE id = ?
            ");
            $stmt->execute([json_encode($proof_data), $contract_id]);
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'work_proof_submitted', 'Work start proof submitted for verification');
            
            // Notify customer
            $this->createNotification(
                $contract_id,
                'customer',
                'Please verify work start proof to release second payment'
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Work proof submitted'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Submit work proof error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Verify work started (customer)
     */
    public function verifyWorkStarted($contract_id, $customer_id, $approved) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("SELECT * FROM contracts WHERE id = ? AND customer_id = ?");
            $stmt->execute([$contract_id, $customer_id]);
            $contract = $stmt->fetch();
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found'];
            }
            
            if ($approved) {
                // Update contract
                $stmt = $this->pdo->prepare("
                    UPDATE contracts 
                    SET work_start_verified = 1,
                        work_start_verified_at = NOW(),
                        contract_status = 'in_progress'
                    WHERE id = ?
                ");
                $stmt->execute([$contract_id]);
                
                // Release second payment from escrow
                require_once __DIR__ . '/PaymentController.php';
                global $pdo;
                $paymentController = new PaymentController($pdo);
                
                // Calculate second payment amount
                $payment_type = $contract['payment_type'];
                $second_percentage = ($payment_type === 'upfront_50_50') ? 50 : 70;
                $second_amount = ($contract['contract_budget'] * $second_percentage) / 100;
                
                $paymentController->releaseFromEscrow(
                    $contract_id,
                    $second_amount,
                    "Work start verified - releasing second payment"
                );
                
                // Timeline event
                $this->addTimelineEvent($contract_id, 'work_verified', 'Work start verified - second payment released');
                
                // Notify company
                $this->createNotification(
                    $contract_id,
                    'company',
                    'Work start verified! Second payment released: Rs. ' . number_format($second_amount, 2)
                );
            } else {
                // Update contract
                $stmt = $this->pdo->prepare("
                    UPDATE contracts 
                    SET work_start_verified = 0,
                        contract_status = 'work_verification_rejected'
                    WHERE id = ?
                ");
                $stmt->execute([$contract_id]);
                
                // Timeline event
                $this->addTimelineEvent($contract_id, 'work_rejected', 'Work start verification rejected');
                
                // Notify company
                $this->createNotification(
                    $contract_id,
                    'company',
                    'Work start verification rejected - please provide better proof'
                );
            }
            
            $this->pdo->commit();
            return ['success' => true, 'message' => $approved ? 'Work verified' : 'Verification rejected'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Verify work error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // ==================== FEATURE 4: TIME TRACKING ====================
    
    /**
     * Submit time entry (company)
     */
    public function submitTimeEntry($contract_id, $entry_data) {
        try {
            $this->pdo->beginTransaction();
            
            // Validate required fields
            if (empty($entry_data['date']) || empty($entry_data['hours']) || empty($entry_data['description'])) {
                return ['success' => false, 'message' => 'Date, hours, and description are required'];
            }
            
            // Validate contract uses time & material payment
            $stmt = $this->pdo->prepare("SELECT payment_type FROM contracts WHERE id = ?");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch();
            
            if (!$contract || $contract['payment_type'] !== 'time_material') {
                return ['success' => false, 'message' => 'Contract does not use time & material payment'];
            }
            
            // Insert time entry
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_time_logs 
                (contract_id, log_date, hours_worked, hourly_rate, description, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([
                $contract_id,
                $entry_data['date'],
                $entry_data['hours'],
                $entry_data['hourly_rate'] ?? 0,
                $entry_data['description']
            ]);
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'time_logged', "{$entry_data['hours']} hours logged for " . date('Y-m-d', strtotime($entry_data['date'])));
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Time entry submitted'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Submit time entry error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Get time entries (company/customer)
     */
    public function getTimeEntries($contract_id, $status = null) {
        try {
            $query = "SELECT * FROM contract_time_logs WHERE contract_id = ?";
            $params = [$contract_id];
            
            if ($status) {
                $query .= " AND status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY log_date DESC";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get time entries error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Approve/reject time entry (customer)
     */
    public function approveTimeEntry($entry_id, $customer_id, $approved, $feedback = null) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("
                SELECT ctl.*, c.customer_id 
                FROM contract_time_logs ctl
                JOIN contracts c ON ctl.contract_id = c.id
                WHERE ctl.id = ? AND c.customer_id = ?
            ");
            $stmt->execute([$entry_id, $customer_id]);
            $entry = $stmt->fetch();
            
            if (!$entry) {
                return ['success' => false, 'message' => 'Time entry not found'];
            }
            
            // Update entry
            $stmt = $this->pdo->prepare("
                UPDATE contract_time_logs 
                SET status = ?,
                    approval_feedback = ?,
                    approved_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $approved ? 'approved' : 'rejected',
                $feedback,
                $entry_id
            ]);
            
            // Timeline event
            $this->addTimelineEvent(
                $entry['contract_id'],
                $approved ? 'time_approved' : 'time_rejected',
                "Time entry for {$entry['hours_worked']} hours " . ($approved ? 'approved' : 'rejected')
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => $approved ? 'Time entry approved' : 'Time entry rejected'];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Approve time entry error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // ==================== FEATURE 6: QUALITY GUARANTEE ====================
    
    /**
     * Start quality guarantee period after completion
     */
    public function startQualityGuarantee($contract_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Get contract
            $stmt = $this->pdo->prepare("SELECT * FROM contracts WHERE id = ? AND payment_type = 'quality_guarantee'");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch();
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found or not quality guarantee type'];
            }
            
            // Create guarantee period record
            $stmt = $this->pdo->prepare("
                INSERT INTO quality_guarantee_period 
                (contract_id, start_date, end_date, status, created_at)
                VALUES (?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'active', NOW())
            ");
            $stmt->execute([$contract_id]);
            $guarantee_id = $this->pdo->lastInsertId();
            
            // Update contract status
            $stmt = $this->pdo->prepare("UPDATE contracts SET contract_status = 'quality_guarantee' WHERE id = ?");
            $stmt->execute([$contract_id]);
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'guarantee_started', '7-day quality guarantee period started');
            
            // Notify both parties
            $this->createNotification(
                $contract_id,
                'customer',
                'Quality guarantee period started - 7 days to report any issues'
            );
            
            $this->createNotification(
                $contract_id,
                'company',
                'Quality guarantee period started - payment held for 7 days'
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Quality guarantee started', 'guarantee_id' => $guarantee_id];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Start quality guarantee error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Submit fix request (customer)
     */
    public function submitFixRequest($contract_id, $customer_id, $request_data) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("SELECT * FROM contracts WHERE id = ? AND customer_id = ?");
            $stmt->execute([$contract_id, $customer_id]);
            $contract = $stmt->fetch();
            
            if (!$contract) {
                return ['success' => false, 'message' => 'Contract not found'];
            }
            
            // Check guarantee period is active
            $stmt = $this->pdo->prepare("SELECT * FROM quality_guarantee_period WHERE contract_id = ? AND status = 'active'");
            $stmt->execute([$contract_id]);
            $guarantee = $stmt->fetch();
            
            if (!$guarantee) {
                return ['success' => false, 'message' => 'Quality guarantee period not active'];
            }
            
            // Create fix request
            $stmt = $this->pdo->prepare("
                INSERT INTO fix_requests 
                (contract_id, guarantee_period_id, issue_description, photos, priority, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([
                $contract_id,
                $guarantee['id'],
                $request_data['description'],
                json_encode($request_data['photos'] ?? []),
                $request_data['priority'] ?? 'medium'
            ]);
            $request_id = $this->pdo->lastInsertId();
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'fix_requested', 'Customer reported issue: ' . $request_data['description']);
            
            // Notify company
            $this->createNotification(
                $contract_id,
                'company',
                'Fix request submitted: ' . $request_data['description']
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Fix request submitted', 'request_id' => $request_id];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Submit fix request error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Submit fix (company)
     */
    public function submitFix($request_id, $company_id, $fix_data) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify company owns contract
            $stmt = $this->pdo->prepare("
                SELECT fr.*, c.company_id 
                FROM fix_requests fr
                JOIN contracts c ON fr.contract_id = c.id
                WHERE fr.id = ? AND c.company_id = ?
            ");
            $stmt->execute([$request_id, $company_id]);
            $request = $stmt->fetch();
            
            if (!$request) {
                return ['success' => false, 'message' => 'Fix request not found'];
            }
            
            // Update fix request
            $stmt = $this->pdo->prepare("
                UPDATE fix_requests 
                SET status = 'fixed',
                    fix_description = ?,
                    fix_photos = ?,
                    fixed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $fix_data['description'],
                json_encode($fix_data['photos'] ?? []),
                $request_id
            ]);
            
            // Timeline event
            $this->addTimelineEvent($request['contract_id'], 'fix_submitted', 'Company submitted fix for issue');
            
            // Notify customer
            $this->createNotification(
                $request['contract_id'],
                'customer',
                'Fix submitted - please verify the repair'
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Fix submitted'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Submit fix error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Verify fix (customer)
     */
    public function verifyFix($request_id, $customer_id, $approved) {
        try {
            $this->pdo->beginTransaction();
            
            // Verify customer owns contract
            $stmt = $this->pdo->prepare("
                SELECT fr.*, c.customer_id 
                FROM fix_requests fr
                JOIN contracts c ON fr.contract_id = c.id
                WHERE fr.id = ? AND c.customer_id = ?
            ");
            $stmt->execute([$request_id, $customer_id]);
            $request = $stmt->fetch();
            
            if (!$request) {
                return ['success' => false, 'message' => 'Fix request not found'];
            }
            
            // Update fix request
            $stmt = $this->pdo->prepare("
                UPDATE fix_requests 
                SET status = ?,
                    verified_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $approved ? 'verified' : 'rejected',
                $request_id
            ]);
            
            // Timeline event
            $this->addTimelineEvent(
                $request['contract_id'],
                $approved ? 'fix_verified' : 'fix_rejected',
                $approved ? 'Fix verified by customer' : 'Fix rejected by customer'
            );
            
            // Notify company
            $this->createNotification(
                $request['contract_id'],
                'company',
                $approved ? 'Fix verified successfully' : 'Fix rejected - please retry'
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => $approved ? 'Fix verified' : 'Fix rejected'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Verify fix error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    /**
     * Auto-release payment after guarantee period (cron job)
     */
    public function autoReleaseQualityGuarantee($contract_id) {
        try {
            $this->pdo->beginTransaction();
            
            // Get active guarantee period
            $stmt = $this->pdo->prepare("
                SELECT * FROM quality_guarantee_period 
                WHERE contract_id = ? 
                AND status = 'active' 
                AND end_date <= NOW()
            ");
            $stmt->execute([$contract_id]);
            $guarantee = $stmt->fetch();
            
            if (!$guarantee) {
                return ['success' => false, 'message' => 'No active guarantee period ready for release'];
            }
            
            // Check for pending fix requests
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as pending_count 
                FROM fix_requests 
                WHERE guarantee_period_id = ? 
                AND status IN ('pending', 'fixed')
            ");
            $stmt->execute([$guarantee['id']]);
            $pending = $stmt->fetch();
            
            if ($pending['pending_count'] > 0) {
                return ['success' => false, 'message' => 'Pending fix requests exist'];
            }
            
            // Update guarantee status
            $stmt = $this->pdo->prepare("UPDATE quality_guarantee_period SET status = 'completed' WHERE id = ?");
            $stmt->execute([$guarantee['id']]);
            
            // Release payment from escrow
            require_once __DIR__ . '/PaymentController.php';
            global $pdo;
            $paymentController = new PaymentController($pdo);
            
            // Get contract budget
            $stmt = $this->pdo->prepare("SELECT contract_budget FROM contracts WHERE id = ?");
            $stmt->execute([$contract_id]);
            $contract = $stmt->fetch();
            
            $paymentController->releaseFromEscrow(
                $contract_id,
                $contract['contract_budget'],
                "Quality guarantee period completed - no issues reported"
            );
            
            // Update contract status
            $stmt = $this->pdo->prepare("UPDATE contracts SET contract_status = 'completed' WHERE id = ?");
            $stmt->execute([$contract_id]);
            
            // Timeline event
            $this->addTimelineEvent($contract_id, 'guarantee_completed', 'Quality guarantee completed - payment released');
            
            // Notify both parties
            $this->createNotification(
                $contract_id,
                'company',
                'Quality guarantee completed! Payment released: Rs. ' . number_format($contract['contract_budget'], 2)
            );
            
            $this->createNotification(
                $contract_id,
                'customer',
                'Quality guarantee period ended - contract completed'
            );
            
            $this->pdo->commit();
            return ['success' => true, 'message' => 'Payment released'];
            
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Auto-release quality guarantee error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // ========================================
    // PHASE 2: TIMELINE & ESCROW
    // ========================================

    public function getTimeline() {
        $contractId = $_GET['contract_id'] ?? null;
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT event_id, event_type, title, description, actor_type, actor_id, created_at
                FROM contract_timeline
                WHERE contract_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$contractId]);
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'events' => $events]);
        } catch (PDOException $e) {
            error_log("Get timeline error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    private function addTimelineEvent($contractId, $eventType, $description, $title = null) {
        $userId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user_role'] ?? 'system';
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_timeline (contract_id, event_type, title, description, actor_type, actor_id, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$contractId, $eventType, $title ?? $eventType, $description, $userRole, $userId]);
        } catch (PDOException $e) {
            error_log("Add timeline event error: " . $e->getMessage());
        }
    }

    public function getEscrowStatus() {
        $contractId = $_GET['contract_id'] ?? null;
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT ea.*, 
                    (SELECT SUM(amount) FROM escrow_transactions WHERE account_id = ea.account_id AND transaction_type = 'release') as total_released,
                    (SELECT COUNT(*) FROM escrow_release_requests WHERE contract_id = ea.contract_id AND status = 'pending') as pending_releases
                FROM escrow_accounts ea
                WHERE ea.contract_id = ?
            ");
            $stmt->execute([$contractId]);
            $escrow = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'escrow' => $escrow]);
        } catch (PDOException $e) {
            error_log("Get escrow status error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function approveEscrowRelease() {
        $releaseId = $_POST['release_id'] ?? null;
        
        if (!$releaseId) {
            echo json_encode(['success' => false, 'message' => 'Release ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE escrow_release_requests SET status = 'approved', approved_at = NOW() WHERE release_id = ?");
            $stmt->execute([$releaseId]);
            
            $stmt = $this->pdo->prepare("SELECT contract_id, amount FROM escrow_release_requests WHERE release_id = ?");
            $stmt->execute([$releaseId]);
            $release = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->addTimelineEvent($release['contract_id'], 'escrow_released', "Escrow payment of Rs. {$release['amount']} released");
            $this->createNotification($release['contract_id'], 'payment_released', "Payment of Rs. {$release['amount']} has been released", 'company');
            
            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Payment released successfully']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Approve escrow release error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function denyEscrowRelease() {
        $releaseId = $_POST['release_id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        
        if (!$releaseId) {
            echo json_encode(['success' => false, 'message' => 'Release ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE escrow_release_requests SET status = 'denied', denial_reason = ?, denied_at = NOW() WHERE release_id = ?");
            $stmt->execute([$reason, $releaseId]);
            
            $stmt = $this->pdo->prepare("SELECT contract_id FROM escrow_release_requests WHERE release_id = ?");
            $stmt->execute([$releaseId]);
            $release = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->addTimelineEvent($release['contract_id'], 'escrow_denied', "Escrow release denied: {$reason}");
            
            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Release denied']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Deny escrow release error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    private function createEscrowReleaseRequest($contractId, $milestoneId, $amount) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO escrow_release_requests (contract_id, milestone_id, amount, requested_at, status)
                VALUES (?, ?, ?, NOW(), 'pending')
            ");
            $stmt->execute([$contractId, $milestoneId, $amount]);
        } catch (PDOException $e) {
            error_log("Create escrow release request error: " . $e->getMessage());
        }
    }

    // ========================================
    // PHASE 2: INVOICES
    // ========================================

    public function getInvoices() {
        $contractId = $_GET['contract_id'] ?? null;
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM contract_invoices
                WHERE contract_id = ?
                ORDER BY issue_date DESC
            ");
            $stmt->execute([$contractId]);
            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'invoices' => $invoices]);
        } catch (PDOException $e) {
            error_log("Get invoices error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function getInvoiceDetails() {
        $invoiceId = $_GET['invoice_id'] ?? null;
        
        if (!$invoiceId) {
            echo json_encode(['success' => false, 'message' => 'Invoice ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM contract_invoices WHERE invoice_id = ?");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'invoice' => $invoice]);
        } catch (PDOException $e) {
            error_log("Get invoice details error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function markInvoicePaid() {
        $invoiceId = $_POST['invoice_id'] ?? null;
        
        if (!$invoiceId) {
            echo json_encode(['success' => false, 'message' => 'Invoice ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("UPDATE contract_invoices SET payment_status = 'paid', paid_at = NOW() WHERE invoice_id = ?");
            $stmt->execute([$invoiceId]);
            
            $stmt = $this->pdo->prepare("SELECT contract_id, invoice_number, total_amount FROM contract_invoices WHERE invoice_id = ?");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->addTimelineEvent($invoice['contract_id'], 'invoice_paid', "Invoice {$invoice['invoice_number']} paid (Rs. {$invoice['total_amount']})");
            
            $this->pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Invoice marked as paid']);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Mark invoice paid error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function downloadInvoicePDF() {
        $invoiceId = $_GET['invoice_id'] ?? null;
        
        if (!$invoiceId) {
            echo json_encode(['success' => false, 'message' => 'Invoice ID required']);
            return;
        }
        
        try {
            // Get invoice details
            $stmt = $this->pdo->prepare("
                SELECT 
                    ci.*,
                    c.contract_number,
                    c.project_title,
                    c.customer_id,
                    c.company_id,
                    u.f_name as customer_fname,
                    u.l_name as customer_lname,
                    u.email as customer_email,
                    u.address as customer_address,
                    u.district as customer_district,
                    comp.name as company_name,
                    c_loc.address as company_address,
                    comp.contact_no as company_contact,
                    comp.email as company_email,
                    comp.registration_no as company_registration
                FROM contract_invoices ci
                INNER JOIN contract c ON ci.contract_id = c.contract_id
                LEFT JOIN user u ON c.customer_id = u.user_id
                LEFT JOIN company comp ON c.company_id = comp.company_id
                LEFT JOIN location c_loc ON comp.location_id = c_loc.location_id
                WHERE ci.invoice_id = ?
            ");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$invoice) {
                echo json_encode(['success' => false, 'message' => 'Invoice not found']);
                return;
            }
            
            // Generate PDF (simple HTML to PDF)
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $invoice['invoice_number'] . '.pdf"');
            
            // For now, return HTML that can be printed as PDF
            // In production, use a library like TCPDF or mPDF
            ob_start();
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Invoice <?php echo htmlspecialchars($invoice['invoice_number']); ?></title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 40px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .invoice-info { margin-bottom: 20px; }
                    .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    .table th, .table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    .table th { background-color: #f5f5f5; }
                    .total { text-align: right; font-size: 18px; font-weight: bold; margin-top: 20px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>INVOICE</h1>
                    <h2><?php echo htmlspecialchars($invoice['company_name']); ?></h2>
                    <p><?php echo htmlspecialchars($invoice['company_address']); ?></p>
                    <p>Tel: <?php echo htmlspecialchars($invoice['company_contact']); ?> | Email: <?php echo htmlspecialchars($invoice['company_email']); ?></p>
                </div>
                
                <div class="invoice-info">
                    <p><strong>Invoice Number:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                    <p><strong>Invoice Date:</strong> <?php echo date('F d, Y', strtotime($invoice['issue_date'])); ?></p>
                    <p><strong>Due Date:</strong> <?php echo date('F d, Y', strtotime($invoice['due_date'])); ?></p>
                    <p><strong>Contract:</strong> <?php echo htmlspecialchars($invoice['contract_number']); ?></p>
                </div>
                
                <div class="invoice-info">
                    <h3>Bill To:</h3>
                    <p><strong><?php echo htmlspecialchars($invoice['customer_fname'] . ' ' . $invoice['customer_lname']); ?></strong></p>
                    <p><?php echo htmlspecialchars($invoice['customer_address']); ?></p>
                    <p><?php echo htmlspecialchars($invoice['customer_email']); ?></p>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($invoice['description'] ?? $invoice['project_title']); ?></td>
                            <td>LKR <?php echo number_format($invoice['amount'], 2); ?></td>
                        </tr>
                        <?php if ($invoice['tax_amount'] > 0): ?>
                        <tr>
                            <td>Tax</td>
                            <td>LKR <?php echo number_format($invoice['tax_amount'], 2); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total Amount</th>
                            <th>LKR <?php echo number_format($invoice['total_amount'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="total">
                    <p>Amount Due: LKR <?php echo number_format($invoice['total_amount'], 2); ?></p>
                    <p>Status: <?php echo strtoupper($invoice['payment_status']); ?></p>
                </div>
                
                <div style="margin-top: 40px;">
                    <p><small>Thank you for your business!</small></p>
                </div>
            </body>
            </html>
            <?php
            echo ob_get_clean();
            exit;
            
        } catch (PDOException $e) {
            error_log("Download invoice PDF error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    // ========================================
    // PHASE 2: BUDGET ADJUSTMENTS
    // ========================================

    public function getBudgetAdjustments() {
        $contractId = $_GET['contract_id'] ?? null;
        
        if (!$contractId) {
            echo json_encode(['success' => false, 'message' => 'Contract ID required']);
            return;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM budget_adjustments
                WHERE contract_id = ?
                ORDER BY requested_at DESC
            ");
            $stmt->execute([$contractId]);
            $adjustments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'adjustments' => $adjustments]);
        } catch (PDOException $e) {
            error_log("Get budget adjustments error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function submitBudgetAdjustment() {
        $contractId = $_POST['contract_id'] ?? null;
        $requestedAmount = $_POST['requested_amount'] ?? null;
        $reason = $_POST['reason'] ?? '';
        $companyId = $this->getCompanyId();
        
        if (!$contractId || !$requestedAmount) {
            echo json_encode(['success' => false, 'message' => 'Contract ID and requested amount required']);
            return;
        }
        
        if (!$companyId) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Get contract details with full validation
            $stmt = $this->pdo->prepare("
                SELECT 
                    c.contract_id,
                    c.total_budget,
                    c.budget_type,
                    c.budget_min,
                    c.budget_max,
                    c.status,
                    c.terms_accepted,
                    c.company_id
                FROM contract c
                WHERE c.contract_id = ? AND c.company_id = ?
            ");
            $stmt->execute([$contractId, $companyId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$contract) {
                $this->pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Contract not found or access denied']);
                return;
            }
            
            // ✅ VALIDATION 1: Check contract status (must be active or in_progress)
            if (!in_array($contract['status'], ['active', 'in_progress'])) {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Budget adjustments are only allowed for active or in-progress contracts',
                    'current_status' => $contract['status']
                ]);
                return;
            }
            
            // ✅ VALIDATION 2: Check if customer has accepted the contract
            if (!$contract['terms_accepted']) {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Budget adjustments are only allowed after customer accepts the contract'
                ]);
                return;
            }
            
            // ✅ VALIDATION 3: Check if budget is flexible
            if ($contract['budget_type'] !== 'flexible') {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'This contract uses fixed budgeting. Adjustments are not allowed.'
                ]);
                return;
            }
            
            // ✅ VALIDATION 4: Check if requested amount is within allowed range (±10%)
            $minAllowed = $contract['budget_min'] ?: ($contract['total_budget'] * 0.9);
            $maxAllowed = $contract['budget_max'] ?: ($contract['total_budget'] * 1.1);
            
            if ($requestedAmount < $minAllowed || $requestedAmount > $maxAllowed) {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Requested amount is outside the allowed range',
                    'min_allowed' => $minAllowed,
                    'max_allowed' => $maxAllowed,
                    'requested' => $requestedAmount
                ]);
                return;
            }
            
            // ✅ VALIDATION 5: Check if there's already a pending adjustment
            $stmt = $this->pdo->prepare("
                SELECT adjustment_id 
                FROM contract_budget_adjustments 
                WHERE contract_id = ? AND status = 'pending'
            ");
            $stmt->execute([$contractId]);
            if ($stmt->fetch()) {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'There is already a pending budget adjustment request for this contract'
                ]);
                return;
            }
            
            // Calculate adjustment details
            $adjustmentAmount = $requestedAmount - $contract['total_budget'];
            $adjustmentPercentage = ($adjustmentAmount / $contract['total_budget']) * 100;
            $adjustmentType = $adjustmentAmount > 0 ? 'increase' : 'decrease';
            
            // Insert adjustment request
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_budget_adjustments (
                    contract_id,
                    requested_by,
                    requester_id,
                    requester_name,
                    adjustment_type,
                    original_amount,
                    requested_amount,
                    adjustment_amount,
                    adjustment_percentage,
                    reason,
                    status,
                    created_at
                ) VALUES (?, 'company', ?, 
                    (SELECT name FROM company WHERE company_id = ?),
                    ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->execute([
                $contractId,
                $companyId,
                $companyId,
                $adjustmentType,
                $contract['total_budget'],
                $requestedAmount,
                $adjustmentAmount,
                $adjustmentPercentage,
                $reason
            ]);
            
            $adjustmentId = $this->pdo->lastInsertId();
            
            // Add timeline event
            $this->addTimelineEvent(
                $contractId, 
                'budget_adjustment_requested', 
                sprintf(
                    'Budget adjustment requested: LKR %s → LKR %s (%+.1f%%)',
                    number_format($contract['total_budget'], 2),
                    number_format($requestedAmount, 2),
                    $adjustmentPercentage
                ),
                'Budget Adjustment Request'
            );
            
            // Create notification for customer
            $this->createNotification(
                $contractId, 
                'budget_adjustment_request', 
                sprintf(
                    'Company has requested a budget adjustment of %s%.1f%% (LKR %s)',
                    $adjustmentAmount > 0 ? '+' : '',
                    $adjustmentPercentage,
                    number_format(abs($adjustmentAmount), 2)
                ),
                'customer'
            );
            
            $this->pdo->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Budget adjustment request submitted successfully',
                'adjustment_id' => $adjustmentId,
                'adjustment_details' => [
                    'original_amount' => $contract['total_budget'],
                    'requested_amount' => $requestedAmount,
                    'adjustment_amount' => $adjustmentAmount,
                    'adjustment_percentage' => $adjustmentPercentage
                ]
            ]);
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("[ContractController] Submit budget adjustment error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function approveBudgetAdjustment() {
        $adjustmentId = $_POST['adjustment_id'] ?? null;
        
        if (!$adjustmentId) {
            echo json_encode(['success' => false, 'message' => 'Adjustment ID required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Get adjustment details with validation
            $stmt = $this->pdo->prepare("
                SELECT 
                    ba.adjustment_id,
                    ba.contract_id,
                    ba.requested_amount,
                    ba.adjustment_amount,
                    ba.adjustment_percentage,
                    ba.status,
                    c.customer_id,
                    c.total_budget as current_budget,
                    c.status as contract_status
                FROM contract_budget_adjustments ba
                INNER JOIN contract c ON ba.contract_id = c.contract_id
                WHERE ba.adjustment_id = ?
            ");
            $stmt->execute([$adjustmentId]);
            $adjustment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$adjustment) {
                $this->pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Budget adjustment not found']);
                return;
            }
            
            // ✅ VALIDATION 1: Check if adjustment is still pending
            if ($adjustment['status'] !== 'pending') {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'This adjustment has already been ' . $adjustment['status'],
                    'current_status' => $adjustment['status']
                ]);
                return;
            }
            
            // ✅ VALIDATION 2: Verify customer authorization
            // Note: Add session check here if needed
            // $userId = $_SESSION['user_id'] ?? null;
            // if ($userId != $adjustment['customer_id']) { return unauthorized; }
            
            // Update adjustment status
            $stmt = $this->pdo->prepare("
                UPDATE contract_budget_adjustments 
                SET status = 'approved', 
                    approved_by = ?,
                    approved_by_name = (SELECT CONCAT(f_name, ' ', l_name) FROM user WHERE user_id = ?),
                    approved_at = NOW(),
                    updated_at = NOW()
                WHERE adjustment_id = ?
            ");
            $customerId = $_SESSION['user_id'] ?? $adjustment['customer_id'];
            $stmt->execute([$customerId, $customerId, $adjustmentId]);
            
            // ✅ UPDATE CONTRACT BUDGET
            $stmt = $this->pdo->prepare("
                UPDATE contract 
                SET total_budget = ?,
                    updated_at = NOW()
                WHERE contract_id = ?
            ");
            $stmt->execute([$adjustment['requested_amount'], $adjustment['contract_id']]);
            
            // Add timeline event
            $this->addTimelineEvent(
                $adjustment['contract_id'], 
                'budget_adjustment_approved', 
                sprintf(
                    'Budget adjustment approved: LKR %s → LKR %s (%+.1f%%)',
                    number_format($adjustment['current_budget'], 2),
                    number_format($adjustment['requested_amount'], 2),
                    $adjustment['adjustment_percentage']
                ),
                'Budget Adjustment Approved'
            );
            
            // Create notification for company
            $this->createNotification(
                $adjustment['contract_id'], 
                'budget_adjustment_approved', 
                sprintf(
                    'Customer approved budget adjustment. New budget: LKR %s',
                    number_format($adjustment['requested_amount'], 2)
                ),
                'company'
            );
            
            $this->pdo->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Budget adjustment approved successfully',
                'new_budget' => $adjustment['requested_amount'],
                'adjustment_details' => [
                    'previous_budget' => $adjustment['current_budget'],
                    'new_budget' => $adjustment['requested_amount'],
                    'change' => $adjustment['adjustment_amount'],
                    'percentage' => $adjustment['adjustment_percentage']
                ]
            ]);
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("[ContractController] Approve budget adjustment error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function rejectBudgetAdjustment() {
        $adjustmentId = $_POST['adjustment_id'] ?? null;
        $rejectionReason = $_POST['rejection_reason'] ?? '';
        
        if (!$adjustmentId) {
            echo json_encode(['success' => false, 'message' => 'Adjustment ID required']);
            return;
        }
        
        if (empty($rejectionReason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
            return;
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Get adjustment details
            $stmt = $this->pdo->prepare("
                SELECT 
                    ba.adjustment_id,
                    ba.contract_id,
                    ba.requested_amount,
                    ba.adjustment_amount,
                    ba.adjustment_percentage,
                    ba.status,
                    c.total_budget as current_budget
                FROM contract_budget_adjustments ba
                INNER JOIN contract c ON ba.contract_id = c.contract_id
                WHERE ba.adjustment_id = ?
            ");
            $stmt->execute([$adjustmentId]);
            $adjustment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$adjustment) {
                $this->pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Budget adjustment not found']);
                return;
            }
            
            // ✅ VALIDATION: Check if adjustment is still pending
            if ($adjustment['status'] !== 'pending') {
                $this->pdo->rollBack();
                echo json_encode([
                    'success' => false, 
                    'message' => 'This adjustment has already been ' . $adjustment['status']
                ]);
                return;
            }
            
            // Update adjustment status
            $customerId = $_SESSION['user_id'] ?? null;
            $stmt = $this->pdo->prepare("
                UPDATE contract_budget_adjustments 
                SET status = 'rejected', 
                    rejection_reason = ?,
                    approved_by = ?,
                    approved_by_name = (SELECT CONCAT(f_name, ' ', l_name) FROM user WHERE user_id = ?),
                    approved_at = NOW(),
                    updated_at = NOW()
                WHERE adjustment_id = ?
            ");
            $stmt->execute([$rejectionReason, $customerId, $customerId, $adjustmentId]);
            
            // Add timeline event
            $this->addTimelineEvent(
                $adjustment['contract_id'], 
                'budget_adjustment_rejected', 
                sprintf(
                    'Budget adjustment rejected. Reason: %s',
                    $rejectionReason
                ),
                'Budget Adjustment Rejected'
            );
            
            // Create notification for company
            $this->createNotification(
                $adjustment['contract_id'], 
                'budget_adjustment_rejected', 
                sprintf(
                    'Customer rejected budget adjustment request. Reason: %s',
                    $rejectionReason
                ),
                'company'
            );
            
            $this->pdo->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Budget adjustment rejected',
                'rejection_reason' => $rejectionReason
            ]);
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("[ContractController] Reject budget adjustment error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    // ========================================
    // PHASE 2: HELPER METHODS
    // ========================================

    private function createNotification($contractId, $type, $message, $recipientType = null) {
        try {
            // Get contract details
            $stmt = $this->pdo->prepare("SELECT customer_id, company_id FROM contract WHERE contract_id = ?");
            $stmt->execute([$contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Determine recipient
            if ($recipientType === null) {
                $recipientType = ($_SESSION['user_role'] ?? '') === 'company' ? 'customer' : 'company';
            }
            
            $recipientId = $recipientType === 'customer' ? $contract['customer_id'] : $contract['company_id'];
            
            $stmt = $this->pdo->prepare("
                INSERT INTO contract_notifications (contract_id, recipient_type, recipient_id, notification_type, title, message, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$contractId, $recipientType, $recipientId, $type, ucfirst(str_replace('_', ' ', $type)), $message]);
        } catch (PDOException $e) {
            error_log("Create notification error: " . $e->getMessage());
        }
    }
    /**
     * Reject a time entry for a contract.
     * @param int|null $entry_id
     * @param int|null $contract_id
     * @param int|null $user_id
     * @param string|null $reason
     */
    public function rejectTimeEntry($entry_id, $contract_id, $user_id, $reason) {
        // TODO: Implement the logic to reject a time entry.
        // For now, just return a success response for API compatibility.
        echo json_encode([
            'success' => true,
            'message' => 'Time entry rejected (stub implementation).',
            'entry_id' => $entry_id,
            'contract_id' => $contract_id,
            'user_id' => $user_id,
            'reason' => $reason
        ]);
    }

    /**
     * Cancel/Undo a contract
     */
    public function undoContract() {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $contractId = $data['contract_id'] ?? $_POST['contract_id'] ?? null;
            $reason = $data['reason'] ?? $_POST['reason'] ?? 'User requested cancellation';

            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            // Check if user is involved
            global $pdo;
            $stmt = $pdo->prepare("SELECT customer_id, company_id FROM contract WHERE contract_id = ?");
            $stmt->execute([$contractId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$contract) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Contract not found']);
                return;
            }

            if ($contract['customer_id'] != $userId && $contract['company_id'] != $userId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized access to contract']);
                return;
            }

            // Attempt undo
            $result = $this->model->cancelContract($contractId, $reason);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Contract cancelled successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel contract']);
            }

        } catch (Exception $e) {
            error_log("[ContractController] Error in undoContract: " . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ========================================
    // CONTRACT CHANGE REQUESTS (Customer → Company)
    // ========================================

    private const CONTRACT_CHANGE_STRUCTURED_MARKER = "\n__STRUCTURED_JSON__:";

    private function insertContractChatMessage($contractId, $senderType, $senderId, $message, $messageType = 'system') {
        // Support both schemas (with/without message_type column)
        try {
            $stmt = $this->pdo->prepare("INSERT INTO contract_chats (contract_id, sender_type, sender_id, message_type, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([(int)$contractId, $senderType, (int)$senderId, $messageType, $message]);
            return (int)$this->pdo->lastInsertId();
        } catch (Exception $e) {
            // Fallback if message_type column doesn't exist
            $stmt = $this->pdo->prepare("INSERT INTO contract_chats (contract_id, sender_type, sender_id, message, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([(int)$contractId, $senderType, (int)$senderId, $message]);
            return (int)$this->pdo->lastInsertId();
        }
    }

    private function validateStructuredContractChange($contractRow, $proposedChanges) {
        if (!is_array($proposedChanges)) {
            throw new Exception('Invalid proposed_changes');
        }

        // Date range validation
        $start = $proposedChanges['start_date'] ?? ($contractRow['start_date'] ?? null);
        $end = $proposedChanges['end_date'] ?? ($contractRow['end_date'] ?? null);
        if (!$start || !$end) {
            throw new Exception('Start date and end date are required');
        }
        $startDT = new DateTime($start);
        $endDT = new DateTime($end);
        if ($endDT < $startDT) {
            throw new Exception('End date must be after start date');
        }

        // Budget validation
        if (array_key_exists('total_budget', $proposedChanges) && $proposedChanges['total_budget'] !== null && $proposedChanges['total_budget'] !== '') {
            if (!is_numeric($proposedChanges['total_budget']) || (float)$proposedChanges['total_budget'] < 0) {
                throw new Exception('Invalid total budget');
            }
        }

        // Milestone validations (only if present)
        if (isset($proposedChanges['milestones']) && is_array($proposedChanges['milestones'])) {
            $milestones = $proposedChanges['milestones'];
            $count = count($milestones);
            if ($count < 2) {
                throw new Exception('Minimum 2 milestones required');
            }
            if ($count > 10) {
                throw new Exception('Maximum 10 milestones allowed');
            }

            $sumPct = 0.0;
            foreach ($milestones as $m) {
                if (!is_array($m)) {
                    throw new Exception('Invalid milestone');
                }

                $title = trim((string)($m['title'] ?? ''));
                if ($title === '') {
                    throw new Exception('Milestone title is required');
                }

                $due = $m['due_date'] ?? null;
                if (!$due) {
                    throw new Exception('Milestone due date is required');
                }
                $dueDT = new DateTime($due);
                if ($dueDT < $startDT || $dueDT > $endDT) {
                    throw new Exception('Milestone due date must be within the contract date range');
                }

                // Percentage must sum to 100%
                $pct = $m['percentage'] ?? null;
                if ($pct === null || $pct === '') {
                    throw new Exception('Milestone percentage is required');
                }
                if (!is_numeric($pct) || (float)$pct <= 0 || (float)$pct > 100) {
                    throw new Exception('Invalid milestone percentage');
                }
                $sumPct += (float)$pct;

                // Amount (if provided) must be numeric
                if (array_key_exists('amount', $m) && $m['amount'] !== null && $m['amount'] !== '') {
                    if (!is_numeric($m['amount']) || (float)$m['amount'] < 0) {
                        throw new Exception('Invalid milestone amount');
                    }
                }
            }

            if (abs($sumPct - 100.0) > 0.01) {
                throw new Exception('Milestone percentages must total 100%');
            }
        }
    }

    /**
     * List change requests for a contract (participant-only).
     * GET: ?contract_id=
     */
    public function listContractChangeRequests() {
        try {
            $contractId = (int)($_GET['contract_id'] ?? 0);
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing contract_id']);
                return;
            }

            $role = $_SESSION['user_role'] ?? null;
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            if ($role === 'company') {
                $stmt = $this->pdo->prepare("SELECT contract_id FROM contract WHERE contract_id = ? AND company_id = ?");
                $stmt->execute([$contractId, $userId]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Access denied']);
                    return;
                }
            } else if (in_array($role, ['user', 'customer'], true)) {
                $stmt = $this->pdo->prepare("SELECT contract_id FROM contract WHERE contract_id = ? AND customer_id = ?");
                $stmt->execute([$contractId, $userId]);
                if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                    http_response_code(403);
                    echo json_encode(['success' => false, 'message' => 'Access denied']);
                    return;
                }
            } else {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $stmt = $this->pdo->prepare("
                SELECT 
                    change_request_id,
                    contract_id,
                    requested_by_customer_id,
                    request_text,
                    status,
                    responded_by_company_id,
                    response_note,
                    created_at,
                    responded_at
                FROM contract_change_requests
                WHERE contract_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$contractId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Backward-compatible: hide structured payload marker from consumers
            foreach ($rows as &$r) {
                $hasStructured = false;
                $rt = (string)($r['request_text'] ?? '');
                $markerPos = strpos($rt, self::CONTRACT_CHANGE_STRUCTURED_MARKER);
                if ($markerPos !== false) {
                    $hasStructured = true;
                    $r['request_text'] = trim(substr($rt, 0, $markerPos));
                }
                $r['has_structured'] = $hasStructured;
            }
            unset($r);

            echo json_encode(['success' => true, 'data' => $rows]);
        } catch (Exception $e) {
            error_log('[ContractController] listContractChangeRequests error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Customer submits a contract adjustment request.
     * POST JSON: { contract_id, request_text }
     */
    public function requestContractChange() {
        try {
            $role = $_SESSION['user_role'] ?? null;
            if (!in_array($role, ['user', 'customer'], true)) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;

            $contractId = (int)($data['contract_id'] ?? 0);
            $requestText = trim((string)($data['request_text'] ?? ''));

            $proposedChanges = $data['proposed_changes'] ?? null;
            if (is_string($proposedChanges) && $proposedChanges !== '') {
                $decoded = json_decode($proposedChanges, true);
                if (is_array($decoded)) $proposedChanges = $decoded;
            }
            if (!is_array($proposedChanges)) $proposedChanges = null;

            if (!$contractId || ($requestText === '' && $proposedChanges === null)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing contract_id and/or changes']);
                return;
            }

            $userId = (int)($_SESSION['user_id'] ?? 0);

            // Ensure customer owns contract
            $stmt = $this->pdo->prepare("SELECT contract_id, company_id, project_title, start_date, end_date, total_budget, payment_method FROM contract WHERE contract_id = ? AND customer_id = ?");
            $stmt->execute([$contractId, $userId]);
            $contract = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$contract) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if ($proposedChanges !== null) {
                $this->validateStructuredContractChange($contract, $proposedChanges);
            }

            $this->pdo->beginTransaction();

            // Capture original snapshot for diffing/apply-on-accept
            $originalSnapshot = null;
            $proposedJson = null;
            $originalJson = null;
            if ($proposedChanges !== null) {
                $orig = $this->model->getByIdForCustomer($contractId, $userId);
                $origMilestones = $this->model->getMilestones($contractId);
                $originalSnapshot = [
                    'start_date' => $orig['start_date'] ?? null,
                    'end_date' => $orig['end_date'] ?? null,
                    'total_budget' => $orig['total_budget'] ?? null,
                    'payment_method' => $orig['payment_method'] ?? null,
                    'milestones' => array_map(function ($m) {
                        return [
                            'milestone_number' => $m['milestone_number'] ?? null,
                            'title' => $m['title'] ?? ($m['milestone_name'] ?? null),
                            'description' => $m['description'] ?? ($m['milestone_description'] ?? null),
                            'due_date' => $m['due_date'] ?? ($m['planned_end_date'] ?? null),
                            'amount' => $m['payment_amount'] ?? ($m['amount'] ?? null),
                            'percentage' => $m['payment_percentage'] ?? ($m['percentage'] ?? null)
                        ];
                    }, is_array($origMilestones) ? $origMilestones : [])
                ];
                $proposedJson = json_encode($proposedChanges, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                $originalJson = json_encode($originalSnapshot, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            // Insert (supports both schemas; fallback stores structured JSON in request_text)
            $dbRequestText = $requestText;
            if ($proposedChanges !== null && $proposedJson) {
                $dbRequestText = ($dbRequestText !== '' ? $dbRequestText : 'Contract adjustment request')
                    . self::CONTRACT_CHANGE_STRUCTURED_MARKER
                    . $proposedJson;
            }

            try {
                $stmt = $this->pdo->prepare("
                    INSERT INTO contract_change_requests (
                        contract_id,
                        requested_by_customer_id,
                        request_text,
                        proposed_changes_json,
                        original_snapshot_json,
                        status,
                        created_at
                    ) VALUES (?, ?, ?, ?, ?, 'pending', NOW())
                ");
                $stmt->execute([$contractId, $userId, $requestText !== '' ? $requestText : 'Contract adjustment request', $proposedJson, $originalJson]);
            } catch (Exception $e) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO contract_change_requests (contract_id, requested_by_customer_id, request_text, status, created_at)
                    VALUES (?, ?, ?, 'pending', NOW())
                ");
                $stmt->execute([$contractId, $userId, $dbRequestText]);
            }
            $changeRequestId = (int)$this->pdo->lastInsertId();

            // Log into chat as a special marker message (rendered as clickable card)
            $displayRequestText = $requestText !== '' ? $requestText : 'Contract adjustment request';
            $payload = json_encode([
                'change_request_id' => $changeRequestId,
                'request_text' => $displayRequestText,
                'has_structured' => $proposedChanges !== null
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $this->insertContractChatMessage(
                $contractId,
                'customer',
                $userId,
                '__CONTRACT_CHANGE_REQUEST__:' . $payload,
                'system'
            );

            // Notify company
            $companyId = (int)($contract['company_id'] ?? 0);
            $projectTitle = (string)($contract['project_title'] ?? ('Contract #' . $contractId));
            if ($companyId > 0) {
                $this->notifier->notify(
                    'Contract change request',
                    "Customer requested adjustments for {$projectTitle}.",
                    'company',
                    $companyId,
                    ['role' => 'user', 'id' => $userId, 'name' => 'Customer']
                );
            }

            $this->pdo->commit();

            echo json_encode([
                'success' => true,
                'change_request_id' => $changeRequestId,
                'message' => 'Change request submitted'
            ]);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('[ContractController] requestContractChange error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    /**
     * Preview a change request (participant-only), returning original + proposed + diff.
     * GET: ?change_request_id=
     */
    public function getContractChangePreview() {
        try {
            $changeRequestId = (int)($_GET['change_request_id'] ?? 0);
            if (!$changeRequestId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing change_request_id']);
                return;
            }

            $role = $_SESSION['user_role'] ?? null;
            $userId = (int)($_SESSION['user_id'] ?? 0);
            if (!$userId) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            if (!in_array($role, ['company', 'user', 'customer'], true)) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            // Load change request + contract ids (supports both schemas)
            $row = null;
            try {
                $stmt = $this->pdo->prepare("SELECT change_request_id, contract_id, request_text, status, proposed_changes_json, original_snapshot_json, created_at, responded_at, response_note FROM contract_change_requests WHERE change_request_id = ?");
                $stmt->execute([$changeRequestId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $stmt = $this->pdo->prepare("SELECT change_request_id, contract_id, request_text, status, created_at, responded_at, response_note FROM contract_change_requests WHERE change_request_id = ?");
                $stmt->execute([$changeRequestId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$row) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Change request not found']);
                return;
            }

            $contractId = (int)$row['contract_id'];

            // Role-based contract load + access check
            if ($role === 'company') {
                $contract = $this->model->getById($contractId, $userId);
            } else {
                $contract = $this->model->getByIdForCustomer($contractId, $userId);
            }

            if (!$contract) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            $milestones = $this->model->getMilestones($contractId);
            $contract['milestones'] = $milestones;

            // Get structured payload (new columns or marker fallback)
            $requestText = (string)($row['request_text'] ?? '');
            $markerPos = strpos($requestText, self::CONTRACT_CHANGE_STRUCTURED_MARKER);
            $markerJson = null;
            if ($markerPos !== false) {
                $markerJson = trim(substr($requestText, $markerPos + strlen(self::CONTRACT_CHANGE_STRUCTURED_MARKER)));
                $requestText = trim(substr($requestText, 0, $markerPos));
            }

            $proposed = null;
            $original = null;
            if (!empty($row['proposed_changes_json'])) {
                $proposed = json_decode((string)$row['proposed_changes_json'], true);
            } elseif ($markerJson) {
                $proposed = json_decode($markerJson, true);
            }

            if (!empty($row['original_snapshot_json'])) {
                $original = json_decode((string)$row['original_snapshot_json'], true);
            }

            if (!is_array($proposed)) $proposed = null;
            if (!is_array($original)) $original = null;

            $diff = $this->computeContractChangeDiff($original, $proposed);

            echo json_encode([
                'success' => true,
                'data' => [
                    'change_request' => [
                        'change_request_id' => (int)$row['change_request_id'],
                        'contract_id' => $contractId,
                        'request_text' => $requestText,
                        'status' => $row['status'] ?? 'pending',
                        'created_at' => $row['created_at'] ?? null,
                        'responded_at' => $row['responded_at'] ?? null,
                        'response_note' => $row['response_note'] ?? null
                    ],
                    'contract' => $contract,
                    'original_snapshot' => $original,
                    'proposed_changes' => $proposed,
                    'diff' => $diff
                ]
            ]);
        } catch (Exception $e) {
            error_log('[ContractController] getContractChangePreview error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    private function computeContractChangeDiff($originalSnapshot, $proposedChanges) {
        if (!is_array($originalSnapshot) || !is_array($proposedChanges)) {
            return [
                'has_structured' => false,
                'fields' => new stdClass(),
                'milestones' => []
            ];
        }

        $fields = [];
        foreach (['start_date', 'end_date', 'total_budget', 'payment_method'] as $k) {
            if (array_key_exists($k, $proposedChanges)) {
                $a = (string)($originalSnapshot[$k] ?? '');
                $b = (string)($proposedChanges[$k] ?? '');
                if ($a !== $b) $fields[$k] = true;
            }
        }

        $origMs = is_array($originalSnapshot['milestones'] ?? null) ? $originalSnapshot['milestones'] : [];
        $propMs = is_array($proposedChanges['milestones'] ?? null) ? $proposedChanges['milestones'] : [];
        $max = max(count($origMs), count($propMs));
        $milestones = [];
        for ($i = 0; $i < $max; $i++) {
            $o = is_array($origMs[$i] ?? null) ? $origMs[$i] : null;
            $p = is_array($propMs[$i] ?? null) ? $propMs[$i] : null;
            $entry = [
                'index' => $i,
                'exists' => [
                    'original' => $o !== null,
                    'proposed' => $p !== null
                ],
                'changed' => []
            ];

            if ($o === null || $p === null) {
                $entry['changed']['row'] = true;
            } else {
                foreach (['title', 'description', 'due_date', 'amount', 'percentage'] as $mk) {
                    $a = (string)($o[$mk] ?? '');
                    $b = (string)($p[$mk] ?? '');
                    if ($a !== $b) $entry['changed'][$mk] = true;
                }
            }

            if (!empty($entry['changed'])) $milestones[] = $entry;
        }

        return [
            'has_structured' => true,
            'fields' => $fields,
            'milestones' => $milestones
        ];
    }

    /**
     * Company responds to a contract change request (accept/reject).
     * POST JSON: { change_request_id, decision, response_note? }
     */
    public function respondContractChange() {
        try {
            if (($_SESSION['user_role'] ?? null) !== 'company') {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;

            $changeRequestId = (int)($data['change_request_id'] ?? 0);
            $decision = $data['decision'] ?? '';
            $responseNote = trim((string)($data['response_note'] ?? ''));

            if (!$changeRequestId || !in_array($decision, ['accepted', 'rejected'], true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing change_request_id or invalid decision']);
                return;
            }

            $companyId = (int)($_SESSION['user_id'] ?? 0);

            // Verify request belongs to this company via contract (supports both schemas)
            try {
                $stmt = $this->pdo->prepare("
                    SELECT 
                        cr.change_request_id,
                        cr.contract_id,
                        cr.status,
                        cr.request_text,
                        cr.proposed_changes_json,
                        cr.original_snapshot_json,
                        c.company_id,
                        c.customer_id,
                        c.project_title
                    FROM contract_change_requests cr
                    INNER JOIN contract c ON cr.contract_id = c.contract_id
                    WHERE cr.change_request_id = ?
                ");
                $stmt->execute([$changeRequestId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $stmt = $this->pdo->prepare("
                    SELECT 
                        cr.change_request_id,
                        cr.contract_id,
                        cr.status,
                        cr.request_text,
                        c.company_id,
                        c.customer_id,
                        c.project_title
                    FROM contract_change_requests cr
                    INNER JOIN contract c ON cr.contract_id = c.contract_id
                    WHERE cr.change_request_id = ?
                ");
                $stmt->execute([$changeRequestId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if (!$row || (int)$row['company_id'] !== $companyId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                return;
            }

            if (($row['status'] ?? '') !== 'pending') {
                echo json_encode(['success' => false, 'message' => 'This request is already ' . ($row['status'] ?? 'processed')]);
                return;
            }

            $this->pdo->beginTransaction();

            // Apply structured changes before marking accepted
            if ($decision === 'accepted') {
                $proposed = null;
                if (!empty($row['proposed_changes_json'])) {
                    $proposed = json_decode((string)$row['proposed_changes_json'], true);
                }
                if (!is_array($proposed)) {
                    // Fallback: parse from marker stored in request_text (old schema)
                    $rt = (string)($row['request_text'] ?? '');
                    $markerPos = strpos($rt, self::CONTRACT_CHANGE_STRUCTURED_MARKER);
                    if ($markerPos !== false) {
                        $markerJson = trim(substr($rt, $markerPos + strlen(self::CONTRACT_CHANGE_STRUCTURED_MARKER)));
                        $decoded = json_decode($markerJson, true);
                        if (is_array($decoded)) $proposed = $decoded;
                    }
                }

                if (is_array($proposed)) {
                    $this->applyStructuredContractChange((int)$row['contract_id'], $proposed, $companyId);
                }
            }

            $stmt = $this->pdo->prepare("
                UPDATE contract_change_requests
                SET status = ?, responded_by_company_id = ?, response_note = ?, responded_at = NOW()
                WHERE change_request_id = ?
            ");
            $stmt->execute([$decision, $companyId, $responseNote !== '' ? $responseNote : null, $changeRequestId]);

            // Chat log response
            $payload = json_encode([
                'change_request_id' => $changeRequestId,
                'decision' => $decision,
                'response_note' => $responseNote
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $this->insertContractChatMessage(
                (int)$row['contract_id'],
                'company',
                $companyId,
                '__CONTRACT_CHANGE_RESPONSE__:' . $payload,
                'system'
            );

            // Notify customer
            $customerId = (int)($row['customer_id'] ?? 0);
            $projectTitle = (string)($row['project_title'] ?? ('Contract #' . (int)$row['contract_id']));
            if ($customerId > 0) {
                $this->notifier->notify(
                    'Contract change request updated',
                    "Company {$decision} your change request for {$projectTitle}.",
                    'user',
                    $customerId,
                    ['role' => 'company', 'id' => $companyId, 'name' => 'Company']
                );
            }

            $this->pdo->commit();

            echo json_encode(['success' => true, 'message' => 'Change request ' . $decision]);
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('[ContractController] respondContractChange error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        }
    }

    private function applyStructuredContractChange($contractId, $proposed, $companyId) {
        // Only allow a limited set of fields
        $allowedFields = ['start_date', 'end_date', 'total_budget', 'payment_method'];

        // Update contract fields
        $setParts = [];
        $params = [];
        foreach ($allowedFields as $f) {
            if (array_key_exists($f, $proposed) && $proposed[$f] !== null && $proposed[$f] !== '') {
                $setParts[] = "{$f} = ?";
                $params[] = $proposed[$f];
            }
        }

        if (!empty($setParts)) {
            $params[] = $contractId;
            $stmt = $this->pdo->prepare("UPDATE contract SET " . implode(', ', $setParts) . " WHERE contract_id = ?");
            $stmt->execute($params);
        }

        // Update milestones (only if provided)
        if (isset($proposed['milestones']) && is_array($proposed['milestones'])) {
            // Safety: do not rewrite milestones if any are already progressed
            $stmt = $this->pdo->prepare("SELECT status FROM contract_milestone WHERE contract_id = ?");
            $stmt->execute([$contractId]);
            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($existing as $st) {
                if ($st && $st !== 'pending') {
                    throw new Exception('Cannot apply milestone changes once work has started.');
                }
            }

            $stmt = $this->pdo->prepare("DELETE FROM contract_milestone WHERE contract_id = ?");
            $stmt->execute([$contractId]);

            // Try to persist percentage if column exists
            try {
                $ins = $this->pdo->prepare("
                    INSERT INTO contract_milestone (contract_id, milestone_number, title, description, due_date, amount, percentage, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                $withPct = true;
            } catch (Exception $e) {
                $ins = $this->pdo->prepare("
                    INSERT INTO contract_milestone (contract_id, milestone_number, title, description, due_date, amount, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");
                $withPct = false;
            }

            $i = 1;
            foreach ($proposed['milestones'] as $m) {
                if (!is_array($m)) continue;
                $title = trim((string)($m['title'] ?? ''));
                if ($title === '') continue;
                $desc = (string)($m['description'] ?? '');
                $due = $m['due_date'] ?? null;
                $amt = $m['amount'] ?? null;
                $pct = $m['percentage'] ?? null;
                if ($withPct) {
                    $ins->execute([$contractId, $i, $title, $desc, $due, $amt, $pct]);
                } else {
                    $ins->execute([$contractId, $i, $title, $desc, $due, $amt]);
                }
                $i++;
            }
        }

        // Update unit rates (only if provided)
        if (isset($proposed['unit_rate_proposals']) && is_array($proposed['unit_rate_proposals'])) {
            // Safety: do not update unit rates if any milestone is already progressed
            $stmt = $this->pdo->prepare("SELECT milestone_id, status FROM contract_milestone WHERE contract_id = ?");
            $stmt->execute([$contractId]);
            $existingRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($existingRows as $r) {
                $st = $r['status'] ?? null;
                if ($st && $st !== 'pending') {
                    throw new Exception('Cannot apply unit rate changes once work has started.');
                }
            }

            $updateById = $this->pdo->prepare("UPDATE contract_milestone SET unit_rate = ? WHERE contract_id = ? AND milestone_id = ?");
            $updateByNumber = $this->pdo->prepare("UPDATE contract_milestone SET unit_rate = ? WHERE contract_id = ? AND milestone_number = ?");

            foreach ($proposed['unit_rate_proposals'] as $p) {
                if (!is_array($p)) continue;
                $rate = $p['proposed_unit_rate'] ?? null;
                if ($rate === null || $rate === '') continue;
                if (!is_numeric($rate) || (float)$rate < 0) {
                    throw new Exception('Invalid proposed unit rate');
                }

                $milestoneId = isset($p['milestone_id']) && $p['milestone_id'] !== '' ? (int)$p['milestone_id'] : 0;
                $milestoneNumber = isset($p['milestone_number']) && $p['milestone_number'] !== '' ? (int)$p['milestone_number'] : 0;

                if ($milestoneId > 0) {
                    $updateById->execute([(float)$rate, $contractId, $milestoneId]);
                } elseif ($milestoneNumber > 0) {
                    $updateByNumber->execute([(float)$rate, $contractId, $milestoneNumber]);
                }
            }
        }

        // Audit log (best-effort)
        try {
            $this->logAuditEvent(
                $contractId,
                null,
                'contract_change_applied',
                $companyId,
                'company',
                json_encode(['proposed' => $proposed], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        } catch (Exception $e) {
            // ignore
        }
    }
}