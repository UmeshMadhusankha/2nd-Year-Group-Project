<?php

require_once __DIR__ . '/SystemNotificationService.php';
require_once __DIR__ . '/JobCollaborationModel.php';

class UserQuotesModel {
    private PDO $pdo;
    private ?bool $companyQuotationHasCompanyId = null;
    private SystemNotificationService $notifier;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->notifier = new SystemNotificationService($pdo);
    }

    private function notifyQuoteDecision(string $source, int $quoteId, string $decision, int $requestId): void
    {
        try {
            if ($source === 'company') {
                $stmt = $this->pdo->prepare("SELECT company_id, title FROM companyquotation WHERE quotation_id = ? LIMIT 1");
                $stmt->execute([$quoteId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['company_id'])) {
                    $this->notifier->notify(
                        $decision === 'accepted' ? 'Quotation accepted' : 'Quotation rejected',
                        ($decision === 'accepted' ? 'Your quotation' : 'Your quotation') . " for request #{$requestId} was {$decision}.",
                        'company',
                        (int)$row['company_id'],
                        ['role' => 'user', 'name' => 'Customer']
                    );
                }
                return;
            }

            $stmt = $this->pdo->prepare("SELECT repairer_id FROM repairerquote WHERE quote_id = ? LIMIT 1");
            $stmt->execute([$quoteId]);
            $repairerId = (int)$stmt->fetchColumn();
            if ($repairerId > 0) {
                $this->notifier->notify(
                    $decision === 'accepted' ? 'Quotation accepted' : 'Quotation rejected',
                    "Your quotation for request #{$requestId} was {$decision}.",
                    'repairer',
                    $repairerId,
                    ['role' => 'user', 'name' => 'Customer']
                );
            }
        } catch (Throwable $e) {
            error_log('notifyQuoteDecision failed: ' . $e->getMessage());
        }
    }

    private function notifyCompetingQuotesRejected(int $requestId, string $winnerSource, int $winnerQuoteId): void
    {
        try {
            if ($winnerSource === 'company') {
                $stmtR = $this->pdo->prepare("SELECT repairer_id FROM repairerquote WHERE request_id = ? AND status = 'rejected'");
                $stmtR->execute([$requestId]);
                foreach ($stmtR->fetchAll(PDO::FETCH_COLUMN) as $repairerId) {
                    $repairerId = (int)$repairerId;
                    if ($repairerId > 0) {
                        $this->notifier->notify('Quotation rejected', "Your quotation for request #{$requestId} was rejected.", 'repairer', $repairerId, ['role' => 'user', 'name' => 'Customer']);
                    }
                }

                $stmtC = $this->pdo->prepare("SELECT company_id FROM companyquotation WHERE request_id = ? AND status = 'rejected' AND quotation_id <> ?");
                $stmtC->execute([$requestId, $winnerQuoteId]);
                foreach ($stmtC->fetchAll(PDO::FETCH_COLUMN) as $companyId) {
                    $companyId = (int)$companyId;
                    if ($companyId > 0) {
                        $this->notifier->notify('Quotation rejected', "Your quotation for request #{$requestId} was rejected.", 'company', $companyId, ['role' => 'user', 'name' => 'Customer']);
                    }
                }
                return;
            }

            $stmtC = $this->pdo->prepare("SELECT company_id FROM companyquotation WHERE request_id = ? AND status = 'rejected'");
            $stmtC->execute([$requestId]);
            foreach ($stmtC->fetchAll(PDO::FETCH_COLUMN) as $companyId) {
                $companyId = (int)$companyId;
                if ($companyId > 0) {
                    $this->notifier->notify('Quotation rejected', "Your quotation for request #{$requestId} was rejected.", 'company', $companyId, ['role' => 'user', 'name' => 'Customer']);
                }
            }

            $stmtR = $this->pdo->prepare("SELECT repairer_id FROM repairerquote WHERE request_id = ? AND status = 'rejected' AND quote_id <> ?");
            $stmtR->execute([$requestId, $winnerQuoteId]);
            foreach ($stmtR->fetchAll(PDO::FETCH_COLUMN) as $repairerId) {
                $repairerId = (int)$repairerId;
                if ($repairerId > 0) {
                    $this->notifier->notify('Quotation rejected', "Your quotation for request #{$requestId} was rejected.", 'repairer', $repairerId, ['role' => 'user', 'name' => 'Customer']);
                }
            }
        } catch (Throwable $e) {
            error_log('notifyCompetingQuotesRejected failed: ' . $e->getMessage());
        }
    }

    private function companyQuotationHasCompanyId(): bool {
        if ($this->companyQuotationHasCompanyId !== null) {
            return $this->companyQuotationHasCompanyId;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'companyquotation' AND COLUMN_NAME = 'company_id'"
            );
            $stmt->execute();
            $this->companyQuotationHasCompanyId = ((int)$stmt->fetchColumn()) > 0;
        } catch (Throwable $e) {
            $this->companyQuotationHasCompanyId = false;
        }

        return $this->companyQuotationHasCompanyId;
    }

    public function getUserQuotes(int $userId, int $limit = 20, int $offset = 0, ?string $status = null, ?int $requestId = null, ?string $requestType = null): array {
        $limit = max(1, min(50, (int)$limit));
        $offset = max(0, (int)$offset);
        $statusNormalized = $status !== null ? strtolower(trim($status)) : null;
        $filterCompletedByRequestStatus = $statusNormalized === 'completed';

        $requestType = $requestType !== null ? strtolower(trim($requestType)) : null;
        $includeRegular = $requestType === null || $requestType === '' || $requestType === 'regular';
        $includeDirect = $requestType === null || $requestType === '' || $requestType === 'direct';

        $params = [];

        $statusSqlRepairerRegular = '';
        $statusSqlCompanyRegular = '';
        $statusSqlRepairerDirect = '';
        $statusSqlCompanyDirect = '';
        $requestStatusSqlRepairerRegular = '';
        $requestStatusSqlCompanyRegular = '';
        $requestStatusSqlRepairerDirect = '';
        $requestStatusSqlCompanyDirect = '';
        $acceptedScopeSqlRepairerRegular = '';
        $acceptedScopeSqlCompanyRegular = '';
        $acceptedScopeSqlRepairerDirect = '';
        $acceptedScopeSqlCompanyDirect = '';

        $requestSqlRepairerRegular = '';
        $requestSqlCompanyRegular = '';
        $requestSqlRepairerDirect = '';
        $requestSqlCompanyDirect = '';

        if ($includeRegular) {
            $params[':user_id_repairer_regular'] = $userId;
            $params[':user_id_company_regular'] = $userId;

            if ($status !== null) {
                if ($filterCompletedByRequestStatus) {
                    $requestStatusSqlRepairerRegular = " AND jr.status = 'completed' AND rq.status IN ('accepted','completed','successful') ";
                    $requestStatusSqlCompanyRegular = " AND jr.status = 'completed' AND cq.status IN ('accepted','successful') ";
                } else {
                    $params[':status_repairer_regular'] = $statusNormalized;
                    $params[':status_company_regular'] = $statusNormalized;
                    $statusSqlRepairerRegular = ' AND rq.status = :status_repairer_regular ';
                    $statusSqlCompanyRegular = ' AND cq.status = :status_company_regular ';
                    if ($statusNormalized === 'accepted') {
                        $acceptedScopeSqlRepairerRegular = " AND jr.status <> 'completed' ";
                        $acceptedScopeSqlCompanyRegular = " AND jr.status <> 'completed' ";
                    }
                }
            }

            if ($requestId !== null) {
                $params[':request_id_repairer_regular'] = $requestId;
                $params[':request_id_company_regular'] = $requestId;
                $requestSqlRepairerRegular = ' AND rq.request_id = :request_id_repairer_regular ';
                $requestSqlCompanyRegular = ' AND cq.request_id = :request_id_company_regular ';
            }
        }

        if ($includeDirect) {
            $params[':user_id_repairer_direct'] = $userId;
            $params[':user_id_company_direct'] = $userId;

            if ($status !== null) {
                if ($filterCompletedByRequestStatus) {
                    $requestStatusSqlRepairerDirect = " AND djr.status = 'completed' AND rq.status IN ('accepted','completed','successful') ";
                    $requestStatusSqlCompanyDirect = " AND djr.status = 'completed' AND cq.status IN ('accepted','successful') ";
                } else {
                    $params[':status_repairer_direct'] = $statusNormalized;
                    $params[':status_company_direct'] = $statusNormalized;
                    $statusSqlRepairerDirect = ' AND rq.status = :status_repairer_direct ';
                    $statusSqlCompanyDirect = ' AND cq.status = :status_company_direct ';
                    if ($statusNormalized === 'accepted') {
                        $acceptedScopeSqlRepairerDirect = " AND djr.status <> 'completed' ";
                        $acceptedScopeSqlCompanyDirect = " AND djr.status <> 'completed' ";
                    }
                }
            }

            if ($requestId !== null) {
                $params[':request_id_repairer_direct'] = $requestId;
                $params[':request_id_company_direct'] = $requestId;
                $requestSqlRepairerDirect = ' AND rq.request_id = :request_id_repairer_direct ';
                $requestSqlCompanyDirect = ' AND cq.request_id = :request_id_company_direct ';
            }
        }

        if (!$includeRegular && !$includeDirect) {
            return [];
        }

        $hasCompanyId          = $this->companyQuotationHasCompanyId();
        $companyJoinSql        = $hasCompanyId ? "LEFT JOIN company comp ON cq.company_id = comp.company_id" : "";
        $companyProviderNameSql = $hasCompanyId ? "COALESCE(comp.name, 'Company')" : "'Company'";
        $companyProviderIdSql   = $hasCompanyId ? "cq.company_id" : "0";

        $parts = [];

        if ($includeRegular) {
            $parts[] = "
                SELECT
                    'repairer' AS source,
                    'regular' AS request_type,
                    rq.quote_id AS quote_id,
                    rq.request_id AS request_id,
                    jr.title AS job_title,
                    rq.quoteAmount AS amount,
                    rq.status AS status,
                    rq.dateSubmitted AS created_at,
                    jr.dateCreated AS job_posted_at,
                    jr.status AS job_status,
                    jr.service_provider_type AS job_provider_preference,
                    c.name AS category_name,
                    rq.repairer_id AS provider_id,
                    CONCAT(r.f_name, ' ', r.l_name) AS provider_name,
                    'Individual' AS provider_type,
                    r.profilePicture AS provider_avatar,
                    r.ratings AS provider_rating,
                    rq.estimatedDays AS estimated_days,
                    rq.warrantyPeriod AS warranty_period,
                    rq.materialsIncluded AS materials_included,
                    rq.message AS message,
                    rq.message AS quote_message,
                    rq.validUntil AS valid_until,
                    NULL AS company_start_date,
                    NULL AS company_completion_date,
                    NULL AS labor_cost,
                    NULL AS material_cost,
                    NULL AS transport_cost,
                    NULL AS other_charges,
                    NULL AS budget_type,
                    NULL AS payment_terms,
                    NULL AS payment_method,
                    NULL AS pricing_type,
                    NULL AS hourly_rate,
                    NULL AS work_schedule_type,
                    NULL AS labor_unit_label,
                    NULL AS material_unit_label
                FROM repairerquote rq
                INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                LEFT JOIN category c ON c.category_id = jr.category_id
                INNER JOIN repairer r ON rq.repairer_id = r.repairer_id
                WHERE jr.user_id = :user_id_repairer_regular
                $statusSqlRepairerRegular
                $requestStatusSqlRepairerRegular
                $acceptedScopeSqlRepairerRegular
                $requestSqlRepairerRegular
            ";

            $parts[] = "
                SELECT
                    'company' AS source,
                    'regular' AS request_type,
                    cq.quotation_id AS quote_id,
                    cq.request_id AS request_id,
                    jr.title AS job_title,
                    cq.total_amount AS amount,
                    cq.status AS status,
                    cq.created_at AS created_at,
                    jr.dateCreated AS job_posted_at,
                    jr.status AS job_status,
                    jr.service_provider_type AS job_provider_preference,
                    c.name AS category_name,
                    $companyProviderIdSql AS provider_id,
                    $companyProviderNameSql AS provider_name,
                    'Company' AS provider_type,
                    NULL AS provider_avatar,
                    NULL AS provider_rating,
                    cq.estimated_duration AS estimated_days,
                    cq.warranty_period AS warranty_period,
                    1 AS materials_included,
                    cq.description AS message,
                    cq.description AS quote_message,
                    NULL AS valid_until,
                    cq.start_date AS company_start_date,
                    cq.completion_date AS company_completion_date,
                    cq.labor_cost AS labor_cost,
                    cq.material_cost AS material_cost,
                    cq.transport_cost AS transport_cost,
                    cq.other_charges AS other_charges,
                    cq.budget_type AS budget_type,
                    cq.payment_terms AS payment_terms,
                    cq.payment_method AS payment_method,
                    cq.pricing_type AS pricing_type,
                    cq.hourly_rate AS hourly_rate,
                    cq.work_schedule_type AS work_schedule_type,
                    cq.labor_unit_label AS labor_unit_label,
                    cq.material_unit_label AS material_unit_label
                FROM companyquotation cq
                INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                LEFT JOIN category c ON c.category_id = jr.category_id
                $companyJoinSql
                WHERE jr.user_id = :user_id_company_regular
                $statusSqlCompanyRegular
                $requestStatusSqlCompanyRegular
                $acceptedScopeSqlCompanyRegular
                $requestSqlCompanyRegular
            ";
        }

        if ($includeDirect) {
            $parts[] = "
                SELECT
                    'repairer' AS source,
                    'direct' AS request_type,
                    rq.quote_id AS quote_id,
                    rq.request_id AS request_id,
                    djr.title AS job_title,
                    rq.quoteAmount AS amount,
                    rq.status AS status,
                    rq.dateSubmitted AS created_at,
                    djr.date_created AS job_posted_at,
                    djr.status AS job_status,
                    djr.provider_type AS job_provider_preference,
                    c.name AS category_name,
                    rq.repairer_id AS provider_id,
                    CONCAT(r.f_name, ' ', r.l_name) AS provider_name,
                    'Individual' AS provider_type,
                    r.profilePicture AS provider_avatar,
                    r.ratings AS provider_rating,
                    rq.estimatedDays AS estimated_days,
                    rq.warrantyPeriod AS warranty_period,
                    rq.materialsIncluded AS materials_included,
                    rq.message AS message,
                    rq.message AS quote_message,
                    rq.validUntil AS valid_until,
                    NULL AS company_start_date,
                    NULL AS company_completion_date,
                    NULL AS labor_cost,
                    NULL AS material_cost,
                    NULL AS transport_cost,
                    NULL AS other_charges,
                    NULL AS budget_type,
                    NULL AS payment_terms,
                    NULL AS payment_method,
                    NULL AS pricing_type,
                    NULL AS hourly_rate,
                    NULL AS work_schedule_type,
                    NULL AS labor_unit_label,
                    NULL AS material_unit_label
                FROM repairerquote rq
                INNER JOIN directjobrequest djr ON rq.request_id = djr.request_id
                LEFT JOIN category c ON c.category_id = djr.category_id
                INNER JOIN repairer r ON rq.repairer_id = r.repairer_id
                WHERE djr.user_id = :user_id_repairer_direct
                $statusSqlRepairerDirect
                $requestStatusSqlRepairerDirect
                $acceptedScopeSqlRepairerDirect
                $requestSqlRepairerDirect
            ";

            $parts[] = "
                SELECT
                    'company' AS source,
                    'direct' AS request_type,
                    cq.quotation_id AS quote_id,
                    cq.request_id AS request_id,
                    djr.title AS job_title,
                    cq.total_amount AS amount,
                    cq.status AS status,
                    cq.created_at AS created_at,
                    djr.date_created AS job_posted_at,
                    djr.status AS job_status,
                    djr.provider_type AS job_provider_preference,
                    c.name AS category_name,
                    $companyProviderIdSql AS provider_id,
                    $companyProviderNameSql AS provider_name,
                    'Company' AS provider_type,
                    NULL AS provider_avatar,
                    NULL AS provider_rating,
                    cq.estimated_duration AS estimated_days,
                    cq.warranty_period AS warranty_period,
                    1 AS materials_included,
                    cq.description AS message,
                    cq.description AS quote_message,
                    NULL AS valid_until,
                    cq.start_date AS company_start_date,
                    cq.completion_date AS company_completion_date,
                    cq.labor_cost AS labor_cost,
                    cq.material_cost AS material_cost,
                    cq.transport_cost AS transport_cost,
                    cq.other_charges AS other_charges,
                    cq.budget_type AS budget_type,
                    cq.payment_terms AS payment_terms,
                    cq.payment_method AS payment_method,
                    cq.pricing_type AS pricing_type,
                    cq.hourly_rate AS hourly_rate,
                    cq.work_schedule_type AS work_schedule_type,
                    cq.labor_unit_label AS labor_unit_label,
                    cq.material_unit_label AS material_unit_label
                FROM companyquotation cq
                INNER JOIN directjobrequest djr ON cq.request_id = djr.request_id
                LEFT JOIN category c ON c.category_id = djr.category_id
                $companyJoinSql
                WHERE djr.user_id = :user_id_company_direct
                $statusSqlCompanyDirect
                $requestStatusSqlCompanyDirect
                $acceptedScopeSqlCompanyDirect
                $requestSqlCompanyDirect
            ";
        }

        $sql = "
            SELECT * FROM (
                " . implode("\nUNION ALL\n", $parts) . "
            ) q
            ORDER BY q.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getUserQuotes: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserPendingCount(int $userId): int {
        $sql = "
            SELECT
                (
                    SELECT COUNT(*)
                    FROM repairerquote rq
                    INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                    WHERE jr.user_id = :user_id_repairer AND rq.status = 'pending'
                )
                +
                (
                    SELECT COUNT(*)
                    FROM companyquotation cq
                    INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                    WHERE jr.user_id = :user_id_company AND cq.status = 'pending'
                )
                +
                (
                    SELECT COUNT(*)
                    FROM repairerquote rq
                    INNER JOIN directjobrequest djr ON rq.request_id = djr.request_id
                    WHERE djr.user_id = :user_id_repairer_direct AND rq.status = 'pending'
                )
                +
                (
                    SELECT COUNT(*)
                    FROM companyquotation cq
                    INNER JOIN directjobrequest djr ON cq.request_id = djr.request_id
                    WHERE djr.user_id = :user_id_company_direct AND cq.status = 'pending'
                ) AS pending_count
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id_repairer', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id_company', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id_repairer_direct', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id_company_direct', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function respondToQuote(int $userId, string $source, int $quoteId, string $decision, ?string $requestType = null): bool {
        if (!in_array($decision, ['accepted', 'rejected'], true)) {
            return false;
        }

        $normalizedRequestType = $requestType !== null ? strtolower(trim($requestType)) : null;
        $allowRegular = $normalizedRequestType === null || $normalizedRequestType === '' || $normalizedRequestType === 'regular';
        $allowDirect = $normalizedRequestType === null || $normalizedRequestType === '' || $normalizedRequestType === 'direct';

        try {
            $this->pdo->beginTransaction();

            // First, find the request_id to ensure ownership and for subsequent updates
            $requestId = null;
            $resolvedRequestType = null;

            $checkCandidates = [];
            if ($source === 'repairer') {
                if ($allowRegular) {
                    $checkCandidates[] = [
                        'sql' => "SELECT rq.request_id FROM repairerquote rq INNER JOIN jobrequest jr ON rq.request_id = jr.request_id WHERE rq.quote_id = :quote_id AND jr.user_id = :user_id",
                        'request_type' => 'regular',
                    ];
                }
                if ($allowDirect) {
                    $checkCandidates[] = [
                        'sql' => "SELECT rq.request_id FROM repairerquote rq INNER JOIN directjobrequest djr ON rq.request_id = djr.request_id WHERE rq.quote_id = :quote_id AND djr.user_id = :user_id",
                        'request_type' => 'direct',
                    ];
                }
            } else {
                if ($allowRegular) {
                    $checkCandidates[] = [
                        'sql' => "SELECT cq.request_id FROM companyquotation cq INNER JOIN jobrequest jr ON cq.request_id = jr.request_id WHERE cq.quotation_id = :quote_id AND jr.user_id = :user_id",
                        'request_type' => 'regular',
                    ];
                }
                if ($allowDirect) {
                    $checkCandidates[] = [
                        'sql' => "SELECT cq.request_id FROM companyquotation cq INNER JOIN directjobrequest djr ON cq.request_id = djr.request_id WHERE cq.quotation_id = :quote_id AND djr.user_id = :user_id",
                        'request_type' => 'direct',
                    ];
                }
            }

            foreach ($checkCandidates as $candidate) {
                $stmt = $this->pdo->prepare($candidate['sql']);
                $stmt->execute([':quote_id' => $quoteId, ':user_id' => $userId]);
                $candidateRequestId = $stmt->fetchColumn();
                if ($candidateRequestId) {
                    $requestId = (int)$candidateRequestId;
                    $resolvedRequestType = $candidate['request_type'];
                    break;
                }
            }

            if (!$requestId) {
                $this->pdo->rollBack();
                return false;
            }

            // Update the target quote
            if ($source === 'repairer') {
                $updateSql = "UPDATE repairerquote SET status = :status WHERE quote_id = :quote_id AND status = 'pending'";
            } else {
                $updateSql = "UPDATE companyquotation SET status = :status WHERE quotation_id = :quote_id AND status = 'pending'";
            }
            
            $stmt = $this->pdo->prepare($updateSql);
            $stmt->execute([':status' => $decision, ':quote_id' => $quoteId]);
            
            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                return false; // Quote not found or not pending
            }

            // If accepted, move the owning request to in_progress.
            // If rejected, only update this quote and keep request status unchanged.
            if ($decision === 'accepted') {
                // Update selected request status to in_progress
                $updateJobSql = $resolvedRequestType === 'direct'
                    ? "UPDATE directjobrequest SET status = 'in_progress' WHERE request_id = :request_id AND user_id = :user_id"
                    : "UPDATE jobrequest SET status = 'in_progress' WHERE request_id = :request_id AND user_id = :user_id";
                $stmtJob = $this->pdo->prepare($updateJobSql);
                $stmtJob->execute([':request_id' => $requestId, ':user_id' => $userId]);

                // Initialize one job-scoped collaboration thread for this accepted quote.
                $collaborationModel = new JobCollaborationModel($this->pdo);
                $collaborationModel->createFromAcceptedQuote(
                    $userId,
                    $source,
                    $quoteId,
                    (int)$requestId,
                    (string)$resolvedRequestType
                );

                $this->notifyQuoteDecision($source, $quoteId, 'accepted', (int)$requestId);
            } else {
                $this->notifyQuoteDecision($source, $quoteId, 'rejected', (int)$requestId);
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Error in respondToQuote: " . $e->getMessage());
            return false;
        }
    }
}
