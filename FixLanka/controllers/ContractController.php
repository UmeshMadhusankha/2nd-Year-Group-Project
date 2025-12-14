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
                    'milestone_plan' => (bool)$contract['milestone_plan']
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
                'contract_number' => 'CNT-' . date('Y', strtotime($contract['contract_date'])) . '-' . str_pad($contract['contract_id'], 3, '0', STR_PAD_LEFT),
                'title' => $contract['project_title'],
                'description' => $contract['project_description'],
                'type' => $contract['project_type'],
                'location' => $contract['location'],
                'client' => [
                    'id' => $contract['customer_id'],
                    'name' => $contract['customer_fname'] . ' ' . $contract['customer_lname'],
                    'email' => $contract['customer_email'],
                    'address' => $contract['customer_address'],
                    'district' => $contract['customer_district']
                ],
                'value' => $contract['total_budget'],
                'start_date' => $contract['start_date'],
                'end_date' => $contract['end_date'],
                'contract_date' => $contract['contract_date'],
                'status' => $contract['status'],
                'progress' => $contract['progress'],
                'milestone_plan' => (bool)$contract['milestone_plan'],
                'milestones' => $milestones,
                'terms_conditions' => $contract['terms_conditions']
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

            $contractId = $_POST['id'] ?? null;
            
            if (!$contractId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Contract ID required']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);

            $result = $this->model->update($contractId, $data, $companyId);

            if ($result) {
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
}
