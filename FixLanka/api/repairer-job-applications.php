<?php
/**
 * Repairer Job Applications API (Repairer-side)
 * Handles job applications submitted by repairers for company job postings.
 * Table: repairerapplication (app_id, repairer_id, posting_id, date_applied, app_status)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

global $pdo;
$db = $pdo;

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'my-list':
            // GET: list all applications for a repairer
            $repairerId = intval($_GET['repairer_id'] ?? 0);
            if (!$repairerId) throw new Exception('repairer_id is required');

            $stmt = $db->prepare("
                SELECT
                    ra.app_id,
                    ra.posting_id,
                    ra.date_applied,
                    ra.app_status,
                    cjp.title,
                    cjp.category,
                    cjp.employment_type,
                    cjp.location,
                    cjp.min_budget,
                    cjp.max_budget,
                    cjp.status AS posting_status,
                    cjp.application_deadline,
                    c.name AS company_name
                FROM repairerapplication ra
                JOIN companyjobpost cjp ON ra.posting_id = cjp.posting_id
                JOIN company c ON cjp.company_id = c.company_id
                WHERE ra.repairer_id = :repairer_id
                ORDER BY ra.date_applied DESC
            ");
            $stmt->execute([':repairer_id' => $repairerId]);
            $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'applications' => $applications]);
            break;

        case 'submit':
            // POST: submit a new application
            if ($method !== 'POST') throw new Exception('POST required');
            $data = json_decode(file_get_contents('php://input'), true);

            $repairerId = intval($data['repairer_id'] ?? 0);
            $postingId  = intval($data['posting_id'] ?? 0);
            if (!$repairerId || !$postingId) throw new Exception('repairer_id and posting_id are required');

            // Check for duplicate
            $dup = $db->prepare("SELECT app_id FROM repairerapplication WHERE repairer_id = :r AND posting_id = :p");
            $dup->execute([':r' => $repairerId, ':p' => $postingId]);
            if ($dup->fetch()) {
                echo json_encode(['success' => false, 'error' => 'You have already applied for this job']);
                break;
            }

            $stmt = $db->prepare("
                INSERT INTO repairerapplication (repairer_id, posting_id, app_status)
                VALUES (:repairer_id, :posting_id, 'pending')
            ");
            $stmt->execute([':repairer_id' => $repairerId, ':posting_id' => $postingId]);
            $appId = $db->lastInsertId();

            echo json_encode(['success' => true, 'app_id' => $appId, 'message' => 'Application submitted successfully']);
            break;

        case 'withdraw':
            // DELETE (or POST): withdraw a pending application
            if ($method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $appId     = intval($data['app_id'] ?? 0);
                $repairerId = intval($data['repairer_id'] ?? 0);
            } else {
                $appId      = intval($_GET['app_id'] ?? 0);
                $repairerId = intval($_GET['repairer_id'] ?? 0);
            }

            if (!$appId || !$repairerId) throw new Exception('app_id and repairer_id are required');

            // Only allow withdrawal of own pending applications
            $stmt = $db->prepare("DELETE FROM repairerapplication WHERE app_id = :app_id AND repairer_id = :repairer_id AND app_status = 'pending'");
            $stmt->execute([':app_id' => $appId, ':repairer_id' => $repairerId]);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Application withdrawn']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Application not found or cannot be withdrawn']);
            }
            break;

        case 'check':
            // GET: check if repairer has already applied for a posting
            $repairerId = intval($_GET['repairer_id'] ?? 0);
            $postingId  = intval($_GET['posting_id'] ?? 0);
            if (!$repairerId || !$postingId) throw new Exception('repairer_id and posting_id are required');

            $stmt = $db->prepare("SELECT app_id, app_status FROM repairerapplication WHERE repairer_id = :r AND posting_id = :p");
            $stmt->execute([':r' => $repairerId, ':p' => $postingId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'applied' => (bool)$row, 'application' => $row ?: null]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
