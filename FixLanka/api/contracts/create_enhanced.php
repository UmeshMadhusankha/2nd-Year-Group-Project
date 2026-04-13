<?php
// Enhanced Contract Creation API
// - getQuotationData: returns a single accepted quotation with full join data for auto-fill
// - saveDraft: upserts auto-save draft data for a company + quotation

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

// Support JSON POST bodies (frontend uses fetch + application/json)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
	$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
	if (stripos($contentType, 'application/json') !== false) {
		$raw = file_get_contents('php://input');
		$json = json_decode($raw, true);
		if (is_array($json)) {
			$_POST = array_merge($_POST, $json);
		}
	}
}

require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

function api_error(int $status, string $message, array $extra = []): void {
	http_response_code($status);
	echo json_encode(array_merge(['success' => false, 'message' => $message], $extra));
	exit;
}

function require_company_id(): int {
	$role = $_SESSION['user_role'] ?? null;
	$userId = $_SESSION['user_id'] ?? null;
	if ($role !== 'company' || !$userId) {
		api_error(401, 'Unauthorized');
	}
	return (int)$userId;
}

$action = (string)($_POST['action'] ?? $_GET['action'] ?? '');

try {
	global $pdo;
	if (!$pdo) {
		api_error(500, 'Database connection not available');
	}

	switch ($action) {
		case 'getQuotationData': {
			$companyId = require_company_id();
			$quotationId = (int)($_GET['quotation_id'] ?? 0);
			if ($quotationId <= 0) {
				api_error(400, 'Missing quotation_id');
			}

				$stmt = $pdo->prepare("\
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

						r.address AS project_location,
						r.district AS project_district,
						r.address AS request_address,
						r.district AS request_district,
						cat.name AS request_category_name,
					r.title AS request_title,

					u.user_id AS customer_id,
					u.f_name AS customer_fname,
					u.l_name AS customer_lname,
					u.email AS customer_email,
					u.address AS customer_address,
					u.district AS customer_district,

					COALESCE(comp.name, CONCAT(u.f_name, ' ', u.l_name)) AS company_name,
					comp.registration_no AS company_registration,
					COALESCE(c_loc.address, comp.address, uc.address) AS company_address,
					comp.contact_no AS company_phone,
					COALESCE(comp.email, uc.email) AS company_email
				FROM companyquotation q
				INNER JOIN jobrequest r ON q.request_id = r.request_id
					LEFT JOIN category cat ON cat.category_id = r.category_id
				INNER JOIN user u ON r.user_id = u.user_id
				LEFT JOIN user uc ON uc.user_id = COALESCE(q.company_id, q.user_id)
				LEFT JOIN company comp ON comp.company_id = COALESCE(q.company_id, q.user_id)
				LEFT JOIN location c_loc ON comp.location_id = c_loc.location_id
				WHERE q.quotation_id = ?
				  AND (q.company_id = ? OR (q.company_id IS NULL AND q.user_id = ?))
				LIMIT 1
			");
			$stmt->execute([$quotationId, $companyId, $companyId]);
			$data = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!$data) {
				api_error(404, 'Quotation not found');
			}

			// Include any existing draft for this quotation (best-effort)
			$draftStmt = $pdo->prepare("\
				SELECT draft_id, form_data, current_step, updated_at
				FROM contract_draft
				WHERE company_id = ? AND quotation_id = ?
				ORDER BY updated_at DESC
				LIMIT 1
			");
			$draftStmt->execute([$companyId, $quotationId]);
			$draft = $draftStmt->fetch(PDO::FETCH_ASSOC);
			if ($draft) {
				$data['draft'] = json_decode((string)$draft['form_data'], true);
				$data['draft_current_step'] = (int)($draft['current_step'] ?? 1);
				$data['draft_updated_at'] = $draft['updated_at'] ?? null;
			}

			echo json_encode(['success' => true, 'data' => $data]);
			exit;
		}

		case 'saveDraft': {
			if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
				api_error(405, 'Method not allowed');
			}

			$companyId = require_company_id();
			$quotationId = (int)($_POST['quotation_id'] ?? 0);
			$currentStep = (int)($_POST['current_step'] ?? 1);
			$formData = $_POST['form_data'] ?? null;

			if ($quotationId <= 0) {
				api_error(400, 'Missing quotation_id');
			}
			if (!is_array($formData)) {
				api_error(400, 'Missing form_data');
			}
			if ($currentStep < 1) $currentStep = 1;

			// Verify quotation belongs to this company
			$qStmt = $pdo->prepare("\
				SELECT 1
				FROM companyquotation
				WHERE quotation_id = ?
				  AND (company_id = ? OR (company_id IS NULL AND user_id = ?))
				LIMIT 1
			");
			$qStmt->execute([$quotationId, $companyId, $companyId]);
			if (!$qStmt->fetchColumn()) {
				api_error(403, 'Forbidden');
			}

			$json = json_encode($formData, JSON_UNESCAPED_UNICODE);
			if ($json === false) {
				api_error(400, 'Invalid form_data (JSON encode failed)');
			}

			// Upsert by (company_id, quotation_id)
			$existingStmt = $pdo->prepare("\
				SELECT draft_id
				FROM contract_draft
				WHERE company_id = ? AND quotation_id = ?
				LIMIT 1
			");
			$existingStmt->execute([$companyId, $quotationId]);
			$existingId = $existingStmt->fetchColumn();

			if ($existingId) {
				$upd = $pdo->prepare("\
					UPDATE contract_draft
					SET form_data = ?, current_step = ?
					WHERE draft_id = ? AND company_id = ?
				");
				$upd->execute([$json, $currentStep, (int)$existingId, $companyId]);
				echo json_encode(['success' => true, 'draft_id' => (int)$existingId]);
				exit;
			}

			$ins = $pdo->prepare("\
				INSERT INTO contract_draft (company_id, quotation_id, form_data, current_step)
				VALUES (?, ?, ?, ?)
			");
			$ins->execute([$companyId, $quotationId, $json, $currentStep]);
			echo json_encode(['success' => true, 'draft_id' => (int)$pdo->lastInsertId()]);
			exit;
		}

		default:
			api_error(400, 'Unknown action');
	}
} catch (Throwable $e) {
	error_log('[EnhancedContractAPI] ' . $e->getMessage());
	api_error(500, 'Server error');
}

