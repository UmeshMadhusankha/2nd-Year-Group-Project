<?php
/**
 * Enhanced Contract Creation API
 * Handles the 8-section legal contract form
 * 
 * Actions:
 *   POST create         - Create a new contract from form data
 *   POST saveDraft      - Auto-save draft progress
 *   GET  getDraft       - Retrieve saved draft
 *   GET  getQuotationData - Get full quotation + related data for auto-fill
 *   GET  checkPreconditions - Verify all preconditions before contract creation
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in as a company user.']);
    exit;
}

$companyId = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'checkPreconditions':
        checkPreconditions($pdo, $companyId);
        break;

    case 'getQuotationData':
        getQuotationData($pdo, $companyId);
        break;

    case 'create':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        createEnhancedContract($pdo, $companyId);
        break;

    case 'saveDraft':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        saveDraft($pdo, $companyId);
        break;

    case 'getDraft':
        getDraft($pdo, $companyId);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action: ' . $action]);
        exit;
}

// ============================================
// CHECK PRECONDITIONS
// ============================================
function checkPreconditions($pdo, $companyId) {
    try {
        $quotationId = $_GET['quotation_id'] ?? null;
        $errors = [];

        // 1. Check company profile exists
        $stmt = $pdo->prepare("SELECT company_id, name, registration_no, address, contact_no, email FROM company WHERE company_id = ?");
        $stmt->execute([$companyId]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$company) {
            $errors[] = 'Company profile not found. Please complete your company profile first.';
        }

        if ($quotationId) {
            // 2. Check quotation exists and is accepted
            $stmt = $pdo->prepare("
                SELECT q.quotation_id, q.status, q.title,
                       r.user_id, u.f_name, u.l_name
                FROM companyquotation q
                INNER JOIN jobrequest r ON q.request_id = r.request_id
                INNER JOIN user u ON r.user_id = u.user_id
                WHERE q.quotation_id = ?
                AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
            ");
            $stmt->execute([$quotationId, $companyId, $companyId]);
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$quotation) {
                $errors[] = 'Quotation not found or not authorized.';
            } elseif ($quotation['status'] !== 'accepted') {
                $errors[] = 'Quotation must be accepted before creating a contract. Current status: ' . $quotation['status'];
            }

            // 3. Check customer profile
            if ($quotation) {
                $stmt = $pdo->prepare("SELECT user_id, f_name, l_name, email, address FROM user WHERE user_id = ?");
                $stmt->execute([$quotation['user_id']]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$customer) {
                    $errors[] = 'Customer profile not found.';
                }
            }

            // 4. Check if contract already exists
            $stmt = $pdo->prepare("SELECT contract_id FROM contract WHERE quotation_id = ?");
            $stmt->execute([$quotationId]);
            if ($stmt->fetch()) {
                $errors[] = 'A contract already exists for this quotation.';
            }
        }

        echo json_encode([
            'success' => count($errors) === 0,
            'errors' => $errors,
            'can_proceed' => count($errors) === 0
        ]);

    } catch (Exception $e) {
        error_log("[EnhancedContract] Precondition check error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error checking preconditions']);
    }
}

// ============================================
// GET FULL QUOTATION DATA FOR AUTO-FILL
// ============================================
function getQuotationData($pdo, $companyId) {
    try {
        $quotationId = $_GET['quotation_id'] ?? null;

        if (!$quotationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
            return;
        }

        // Get quotation with all related data
        $stmt = $pdo->prepare("
            SELECT 
                q.quotation_id,
                q.request_id,
                q.title,
                q.description,
                q.labor_cost,
                q.material_cost,
                q.transport_cost,
                q.other_charges,
                q.total_amount,
                q.start_date,
                q.completion_date,
                q.estimated_duration,
                q.payment_terms,
                q.warranty_period,
                q.additional_terms,
                q.budget_type,
                q.budget_min,
                q.budget_max,
                
                -- NOTE: payment_method, pricing_type, hourly_rate, spending_cap_multiplier
                -- columns exist in companyquotation but are NEVER populated by the 
                -- quotation creation form. They will always be NULL.
                -- The contract form detects payment type from payment_terms free text instead.
                q.payment_method,
                q.pricing_type,
                q.hourly_rate,
                q.spending_cap_multiplier,
                q.status as quotation_status,
                
                -- Job Request data
                r.title as request_title,
                r.description as request_description,
                r.address as request_address,
                r.district as request_district,
                r.urgency,
                r.category_id,
                
                -- Customer data
                u.user_id as customer_id,
                u.f_name as customer_fname,
                u.l_name as customer_lname,
                u.email as customer_email,
                u.address as customer_address,
                u.district as customer_district,
                
                -- Company data
                c.company_id,
                c.name as company_name,
                c.registration_no as company_registration,
                c.address as company_address,
                c.contact_no as company_phone,
                c.email as company_email,
                c.business_type as company_type
                
            FROM companyquotation q
            INNER JOIN jobrequest r ON q.request_id = r.request_id
            INNER JOIN user u ON r.user_id = u.user_id
            INNER JOIN company c ON c.company_id = ?
            WHERE q.quotation_id = ?
            AND q.status = 'accepted'
            AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
        ");

        $stmt->execute([$companyId, $quotationId, $companyId, $companyId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Quotation not found or not accepted']);
            return;
        }

        // Generate project reference
        $data['project_reference'] = 'PRJ-' . date('Y') . '-' . str_pad($data['quotation_id'], 4, '0', STR_PAD_LEFT);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);

    } catch (Exception $e) {
        error_log("[EnhancedContract] getQuotationData error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error loading quotation data']);
    }
}

// ============================================
// CREATE ENHANCED CONTRACT
// ============================================
function createEnhancedContract($pdo, $companyId) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
            return;
        }

        // Validate required fields
        $required = ['quotation_id', 'total_budget', 'start_date', 'end_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => "Field '$field' is required"]);
                return;
            }
        }

        // Validate dates
        $startDate = new DateTime($data['start_date']);
        $endDate = new DateTime($data['end_date']);
        if ($endDate <= $startDate) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'End date must be after start date']);
            return;
        }

        // Validate payment totals ONLY if milestone-based payment is selected
        $paymentMethod = $data['payment_method'] ?? '50_50';
        if ($paymentMethod === 'milestone_based' && !empty($data['milestones'])) {
            $milestoneTotal = array_sum(array_column($data['milestones'], 'amount'));
            $tolerance = abs($milestoneTotal - floatval($data['total_budget']));
            if ($tolerance > 1) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => "Milestone payments (LKR " . number_format($milestoneTotal, 2) . ") must equal contract value (LKR " . number_format($data['total_budget'], 2) . ")"
                ]);
                return;
            }
        }

        $pdo->beginTransaction();

        try {
            // 1. Get quotation data for project creation
            $stmt = $pdo->prepare("
                SELECT q.*, r.user_id as customer_id, r.address, r.district, r.title as request_title
                FROM companyquotation q
                INNER JOIN jobrequest r ON q.request_id = r.request_id
                WHERE q.quotation_id = ?
                AND q.status = 'accepted'
                AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
            ");
            $stmt->execute([$data['quotation_id'], $companyId, $companyId]);
            $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$quotation) {
                throw new Exception('Quotation not found or not authorized');
            }

            // 2. Create project
            $stmt = $pdo->prepare("
                INSERT INTO project (company_id, customer_id, title, description, project_type, location, budget, start_date, end_date, status, progress)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'planned', 0)
            ");
            $stmt->execute([
                $companyId,
                $quotation['customer_id'],
                $data['project_title'] ?? $quotation['title'],
                $data['project_description'] ?? $quotation['description'],
                $data['project_type'] ?? 'Construction',
                $data['project_location'] ?? ($quotation['address'] . ', ' . $quotation['district']),
                $data['total_budget'],
                $data['start_date'],
                $data['end_date']
            ]);
            $projectId = $pdo->lastInsertId();

            // 3. Generate contract number
            $year = date('Y');
            $prefix = "CTR-{$year}-";
            $stmt = $pdo->prepare("SELECT contract_number FROM contract WHERE contract_number LIKE ? ORDER BY contract_number DESC LIMIT 1");
            $stmt->execute([$prefix . '%']);
            $last = $stmt->fetch(PDO::FETCH_ASSOC);
            $newNum = $last ? ((int)substr($last['contract_number'], -4) + 1) : 1;
            $contractNumber = $prefix . str_pad($newNum, 4, '0', STR_PAD_LEFT);

            // 4. Build terms & conditions JSON
            $termsData = [
                'scope' => [
                    'description' => $data['scope_description'] ?? '',
                    'inclusions' => $data['scope_inclusions'] ?? '',
                    'exclusions' => $data['scope_exclusions'] ?? '',
                    'standards' => $data['scope_standards'] ?? '',
                    'materials_responsibility' => $data['materials_responsibility'] ?? 'company'
                ],
                'delays' => [
                    'late_payment_penalty' => $data['late_payment_penalty'] ?? 'Interest of 2% per month on overdue payments',
                    'pause_work_clause' => $data['pause_work_clause'] ?? true,
                    'time_extension_clause' => $data['time_extension_clause'] ?? true
                ],
                'variations' => [
                    'clause_enabled' => $data['variation_clause'] ?? true
                ],
                'communication' => [
                    'channel' => $data['communication_channel'] ?? 'system',
                    'dispute_resolution' => $data['dispute_resolution'] ?? 'Mediation through FixLanka platform before external arbitration'
                ],
                'warranty_period' => $data['warranty_period'] ?? '',
                'additional_terms' => $data['additional_terms'] ?? ''
            ];

            // 5. Determine if contract should be sent to customer immediately
            $sendToCustomer = !empty($data['send_to_customer']);
            $contractStatus = $sendToCustomer ? 'pending_signature' : 'draft';
            $sentFlag = $sendToCustomer ? 1 : 0;

            // 6. Insert contract
            $stmt = $pdo->prepare("
                INSERT INTO contract (
                    contract_number, quotation_id, company_id, customer_id, job_request_id, project_id,
                    project_title, project_reference, project_location, project_description,
                    scope_description, scope_inclusions, scope_exclusions, scope_standards, materials_responsibility,
                    milestone_plan, total_budget, budget_type, budget_min, budget_max,
                    tax_inclusive, payment_method, advance_payment_pct, pricing_type, hourly_rate, spending_cap,
                    late_payment_penalty, pause_work_clause, time_extension_clause,
                    variation_clause, communication_channel, dispute_resolution,
                    start_date, end_date, contract_date,
                    user_signature, company_signature, terms_conditions, status,
                    auto_generated, amount_pending, payment_status, progress_percentage,
                    sent_to_customer, sent_at, locked
                ) VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?,
                    '', '', ?, ?,
                    0, ?, 'pending', 0,
                    ?, IF(? = 1, NOW(), NULL), 0
                )
            ");

            $paymentMethod = $data['payment_method'] ?? '50_50';
            $milestonePlan = ($paymentMethod === 'milestone_based') ? 1 : 0;
            $totalBudget = floatval($data['total_budget']);

            $stmt->execute([
                $contractNumber,
                $data['quotation_id'],
                $companyId,
                $quotation['customer_id'],
                $quotation['request_id'],
                $projectId,
                $data['project_title'] ?? $quotation['title'],
                $data['project_reference'] ?? '',
                $data['project_location'] ?? '',
                $data['project_description'] ?? '',
                $data['scope_description'] ?? '',
                $data['scope_inclusions'] ?? '',
                $data['scope_exclusions'] ?? '',
                $data['scope_standards'] ?? '',
                $data['materials_responsibility'] ?? 'company',
                $milestonePlan,
                $totalBudget,
                $data['budget_type'] ?? 'fixed',
                $data['budget_min'] ?? null,
                $data['budget_max'] ?? null,
                $data['tax_inclusive'] ?? 1,
                $paymentMethod,
                $data['advance_payment_pct'] ?? 0,
                $data['pricing_type'] ?? 'fixed_price',
                $data['hourly_rate'] ?? null,
                $data['spending_cap'] ?? null,
                $data['late_payment_penalty'] ?? '',
                $data['pause_work_clause'] ?? 1,
                $data['time_extension_clause'] ?? 1,
                $data['variation_clause'] ?? 1,
                $data['communication_channel'] ?? 'system',
                $data['dispute_resolution'] ?? '',
                $data['start_date'],
                $data['end_date'],
                date('Y-m-d'),
                json_encode($termsData),
                $contractStatus,
                $totalBudget,
                $sentFlag,
                $sentFlag
            ]);

            $contractId = $pdo->lastInsertId();

            // 7. Insert milestones (always saved for tracking, amounts only for milestone_based)
            if (!empty($data['milestones'])) {
                $msStmt = $pdo->prepare("
                    INSERT INTO contract_milestone (contract_id, milestone_number, title, description, due_date, amount, percentage, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
                ");
                foreach ($data['milestones'] as $i => $ms) {
                    // Only store payment amounts when milestone_based, otherwise 0 (tracking-only)
                    $msAmount = ($paymentMethod === 'milestone_based') ? floatval($ms['amount'] ?? 0) : 0;
                    $msPct = ($paymentMethod === 'milestone_based') ? floatval($ms['percentage'] ?? 0) : 0;

                    $msStmt->execute([
                        $contractId,
                        $i + 1,
                        $ms['title'] ?? 'Milestone ' . ($i + 1),
                        $ms['description'] ?? '',
                        $ms['due_date'],
                        $msAmount,
                        $msPct
                    ]);
                }
            }

            // 8. Update quotation status
            $stmt = $pdo->prepare("UPDATE companyquotation SET status = 'successful' WHERE quotation_id = ?");
            $stmt->execute([$data['quotation_id']]);

            // 9. Delete draft if exists
            $stmt = $pdo->prepare("DELETE FROM contract_draft WHERE company_id = ? AND quotation_id = ?");
            $stmt->execute([$companyId, $data['quotation_id']]);

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Contract created successfully',
                'data' => [
                    'contract_id' => $contractId,
                    'contract_number' => $contractNumber,
                    'project_id' => $projectId
                ]
            ]);

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        error_log("[EnhancedContract] Create error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error creating contract: ' . $e->getMessage()]);
    }
}

// ============================================
// SAVE DRAFT
// ============================================
function saveDraft($pdo, $companyId) {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $quotationId = $data['quotation_id'] ?? null;
        $currentStep = $data['current_step'] ?? 1;
        $formData = $data['form_data'] ?? $data;

        // Check for existing draft
        $stmt = $pdo->prepare("SELECT draft_id FROM contract_draft WHERE company_id = ? AND quotation_id = ?");
        $stmt->execute([$companyId, $quotationId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $pdo->prepare("UPDATE contract_draft SET form_data = ?, current_step = ?, updated_at = NOW() WHERE draft_id = ?");
            $stmt->execute([json_encode($formData), $currentStep, $existing['draft_id']]);
            $draftId = $existing['draft_id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO contract_draft (company_id, quotation_id, form_data, current_step) VALUES (?, ?, ?, ?)");
            $stmt->execute([$companyId, $quotationId, json_encode($formData), $currentStep]);
            $draftId = $pdo->lastInsertId();
        }

        echo json_encode([
            'success' => true,
            'message' => 'Draft saved',
            'draft_id' => $draftId
        ]);

    } catch (Exception $e) {
        error_log("[EnhancedContract] saveDraft error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error saving draft']);
    }
}

// ============================================
// GET DRAFT
// ============================================
function getDraft($pdo, $companyId) {
    try {
        $quotationId = $_GET['quotation_id'] ?? null;

        $stmt = $pdo->prepare("SELECT * FROM contract_draft WHERE company_id = ? AND quotation_id = ? ORDER BY updated_at DESC LIMIT 1");
        $stmt->execute([$companyId, $quotationId]);
        $draft = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($draft) {
            $draft['form_data'] = json_decode($draft['form_data'], true);
        }

        echo json_encode([
            'success' => true,
            'data' => $draft
        ]);

    } catch (Exception $e) {
        error_log("[EnhancedContract] getDraft error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error loading draft']);
    }
}
