<?php
/**
 * Job Postings API Endpoint
 * Handles CRUD operations for Company Job Postings
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../models/JobPostingModel.php';

// Get database connection
global $pdo;

// Initialize model
$jobPostingModel = new JobPostingModel($pdo);

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGet($jobPostingModel, $action);
            break;
            
        case 'POST':
            handlePost($jobPostingModel, $action);
            break;
            
        case 'PUT':
            handlePut($jobPostingModel, $action);
            break;
            
        case 'DELETE':
            handleDelete($jobPostingModel);
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function isMissingOrBlankField(array $data, string $field): bool {
    if (!array_key_exists($field, $data)) {
        return true;
    }

    $value = $data[$field];
    if ($value === null) {
        return true;
    }

    if (is_string($value)) {
        return trim($value) === '';
    }

    return false;
}

/**
 * Handle GET requests
 */
function handleGet($model, $action) {
    switch ($action) {
        case 'browse':
            // Repairer-side: browse open job postings across companies
            global $pdo;

            // Ensure `companyjobpost` exists (auto-renames legacy `job_postings`/`job_posting` if present)
            if (is_object($model) && method_exists($model, 'ensureTable')) {
                $model->ensureTable();
            }

            $jobTable = (is_object($model) && method_exists($model, 'getTableName'))
                ? $model->getTableName()
                : 'companyjobpost';

            $columnExists = function (string $table, string $column) use ($pdo): bool {
                try {
                    $stmt = $pdo->prepare(
                        'SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS '
                        . 'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1'
                    );
                    $stmt->execute([':t' => $table, ':c' => $column]);
                    return (bool)$stmt->fetchColumn();
                } catch (Throwable $e) {
                    return false;
                }
            };

            $firstExistingColumn = function (string $table, array $candidates) use ($columnExists): ?string {
                foreach ($candidates as $c) {
                    if ($columnExists($table, (string)$c)) {
                        return (string)$c;
                    }
                }
                return null;
            };

            $dateCol = $firstExistingColumn($jobTable, [
                'created_at',
                'createdAt',
                'posted_date',
                'postedDate',
                'posting_date',
                'date_created',
                'dateCreated',
                'created_on',
                'createdOn',
                'updated_at',
                'updatedAt',
            ]);

            $postedDateSelect = $dateCol ? ("jp.`{$dateCol}` AS posted_date") : 'NULL AS posted_date';
            $orderBy = $dateCol ? ("jp.`{$dateCol}` DESC") : 'jp.posting_id DESC';

            $category = $_GET['category'] ?? null;
            $location = $_GET['location'] ?? null;
            $search = $_GET['search'] ?? null;

            $sql = "SELECT 
                        jp.*, 
                        c.name AS company_name,
                        {$postedDateSelect},
                        (SELECT COUNT(*) FROM repairer_applications ra WHERE ra.job_posting_id = jp.posting_id) AS application_count
                    FROM {$jobTable} jp
                    JOIN company c ON jp.company_id = c.company_id
                    WHERE jp.status = 'open'";

            $params = [];

            if (!empty($category) && $category !== 'all') {
                $sql .= " AND jp.category = ?";
                $params[] = $category;
            }

            if (!empty($location) && $location !== 'all') {
                $sql .= " AND jp.location LIKE ?";
                $params[] = "%{$location}%";
            }

            if (!empty($search)) {
                $sql .= " AND (jp.title LIKE ? OR jp.description LIKE ? OR c.name LIKE ?)";
                $like = "%{$search}%";
                $params[] = $like;
                $params[] = $like;
                $params[] = $like;
            }

            $sql .= " ORDER BY {$orderBy}";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $postings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'postings' => $postings
            ]);
            break;

        case 'list':
            // Get all job postings for a company
            $companyId = $_GET['company_id'] ?? null;
            $status = $_GET['status'] ?? null;
            
            if (!$companyId) {
                throw new Exception('Company ID is required');
            }
            
            $postings = $model->getByCompany($companyId, $status);
            
            // Add application counts
            foreach ($postings as &$posting) {
                $posting['application_count'] = $model->getApplicationCount($posting['posting_id']);
            }
            
            echo json_encode([
                'success' => true,
                'postings' => $postings
            ]);
            break;
            
        case 'get':
            // Get a single job posting
            $postingId = $_GET['posting_id'] ?? null;
            
            if (!$postingId) {
                throw new Exception('Posting ID is required');
            }
            
            $posting = $model->getById($postingId);
            
            if (!$posting) {
                throw new Exception('Job posting not found');
            }

            // Add company name and posted_date for repairer UI
            global $pdo;
            $stmt = $pdo->prepare("SELECT name FROM company WHERE company_id = ? LIMIT 1");
            $stmt->execute([$posting['company_id']]);
            $posting['company_name'] = $stmt->fetchColumn() ?: null;
            $posting['posted_date'] = $posting['created_at']
                ?? ($posting['createdAt'] ?? ($posting['posted_date'] ?? ($posting['postedDate'] ?? ($posting['date_created'] ?? ($posting['dateCreated'] ?? null)))));
            
            $posting['application_count'] = $model->getApplicationCount($postingId);
            
            echo json_encode([
                'success' => true,
                'posting' => $posting
            ]);
            break;
            
        case 'applications':
            // Get application count for a specific posting
            $postingId = $_GET['posting_id'] ?? null;
            
            if (!$postingId) {
                throw new Exception('Posting ID is required');
            }
            
            $count = $model->getApplicationCount($postingId);
            
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            break;
            
        case 'stats':
            // Get statistics
            $companyId = $_GET['company_id'] ?? null;
            
            if (!$companyId) {
                throw new Exception('Company ID is required');
            }
            
            $stats = $model->getStatistics($companyId);
            
            echo json_encode([
                'success' => true,
                'statistics' => $stats
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
}

/**
 * Handle POST requests (Create)
 */
function handlePost($model, $action) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    // Validate required fields
    $required = ['company_id', 'title', 'category', 'employment_type', 'description', 
                 'min_experience', 'min_budget', 'max_budget', 'location'];
    
    foreach ($required as $field) {
        if (isMissingOrBlankField($data, $field)) {
            throw new Exception("Field '$field' is required");
        }
    }
    
    // Validate budget range
    if ($data['min_budget'] > $data['max_budget']) {
        throw new Exception('Minimum budget cannot exceed maximum budget');
    }
    
    // Create the job posting
    $postingId = $model->create($data);
    
    if (!$postingId) {
        throw new Exception('Failed to create job posting');
    }
    
    // Fetch the created posting
    $posting = $model->getById($postingId);

    $response = [
        'success' => true,
        'message' => 'Job posting created successfully',
        'posting_id' => $postingId,
        'posting' => $posting
    ];

    // Debug helper: shows what was received vs what was stored.
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        $response['debug'] = [
            'received' => [
                'application_deadline' => $data['application_deadline'] ?? null,
                'applicationDeadline' => $data['applicationDeadline'] ?? null,
                'priority_level' => $data['priority_level'] ?? null,
                'priorityLevel' => $data['priorityLevel'] ?? null,
            ],
            'stored' => [
                'application_deadline' => $posting['application_deadline'] ?? null,
                'applicationDeadline' => $posting['applicationDeadline'] ?? null,
                'priority_level' => $posting['priority_level'] ?? null,
                'priorityLevel' => $posting['priorityLevel'] ?? null,
            ]
        ];
    }
    
    echo json_encode($response);
}

/**
 * Handle PUT requests (Update)
 */
function handlePut($model, $action) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid request data');
    }
    
    $postingId = $data['posting_id'] ?? null;
    
    if (!$postingId) {
        throw new Exception('Posting ID is required');
    }
    
    switch ($action) {
        case 'status':
            // Update only the status
            $status = $data['status'] ?? null;
            
            if (!$status) {
                throw new Exception('Status is required');
            }
            
            $validStatuses = ['draft', 'open', 'closed', 'filled'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Invalid status value');
            }
            
            $success = $model->updateStatus($postingId, $status);
            
            echo json_encode([
                'success' => $success,
                'message' => 'Job posting status updated successfully'
            ]);
            break;
            
        default:
            // Update full posting
            $required = ['title', 'category', 'employment_type', 'description', 
                         'min_experience', 'min_budget', 'max_budget', 'location'];
            
            foreach ($required as $field) {
                if (isMissingOrBlankField($data, $field)) {
                    throw new Exception("Field '$field' is required");
                }
            }
            
            // Validate budget range
            if ($data['min_budget'] > $data['max_budget']) {
                throw new Exception('Minimum budget cannot exceed maximum budget');
            }
            
            $success = $model->update($postingId, $data);
            
            // Fetch updated posting
            $posting = $model->getById($postingId);

            $response = [
                'success' => $success,
                'message' => 'Job posting updated successfully',
                'posting' => $posting
            ];

            if (isset($_GET['debug']) && $_GET['debug'] === '1') {
                $response['debug'] = [
                    'received' => [
                        'application_deadline' => $data['application_deadline'] ?? null,
                        'applicationDeadline' => $data['applicationDeadline'] ?? null,
                        'priority_level' => $data['priority_level'] ?? null,
                        'priorityLevel' => $data['priorityLevel'] ?? null,
                    ],
                    'stored' => [
                        'application_deadline' => $posting['application_deadline'] ?? null,
                        'applicationDeadline' => $posting['applicationDeadline'] ?? null,
                        'priority_level' => $posting['priority_level'] ?? null,
                        'priorityLevel' => $posting['priorityLevel'] ?? null,
                    ]
                ];
            }

            echo json_encode($response);
            break;
    }
}

/**
 * Handle DELETE requests
 */
function handleDelete($model) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $postingId = $data['posting_id'] ?? $_GET['posting_id'] ?? null;
    
    if (!$postingId) {
        throw new Exception('Posting ID is required');
    }
    
    $success = $model->delete($postingId);
    
    echo json_encode([
        'success' => $success,
        'message' => 'Job posting deleted successfully'
    ]);
}
