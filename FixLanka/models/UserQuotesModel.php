<?php

class UserQuotesModel {
    private PDO $pdo;
    private ?bool $companyQuotationHasCompanyId = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
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

    public function getUserQuotes(int $userId, int $limit = 20, int $offset = 0, ?string $status = null, ?int $requestId = null): array {
        $limit = max(1, min(50, (int)$limit));
        $offset = max(0, (int)$offset);

        $params = [
            ':user_id_repairer' => $userId,
            ':user_id_company'  => $userId,
        ];
        $statusSqlRepairer = '';
        $statusSqlCompany  = '';
        if ($status !== null) {
            $params[':status_repairer'] = $status;
            $params[':status_company']  = $status;
            $statusSqlRepairer = ' AND rq.status = :status_repairer ';
            $statusSqlCompany  = ' AND cq.status = :status_company ';
        }

        $requestSqlRepairer = '';
        $requestSqlCompany  = '';
        if ($requestId !== null) {
            $params[':request_id_repairer'] = $requestId;
            $params[':request_id_company']  = $requestId;
            $requestSqlRepairer = ' AND rq.request_id = :request_id_repairer ';
            $requestSqlCompany  = ' AND cq.request_id = :request_id_company ';
        }

        $hasCompanyId          = $this->companyQuotationHasCompanyId();
        $companyJoinSql        = $hasCompanyId ? "LEFT JOIN company c ON cq.company_id = c.company_id" : "";
        $companyProviderNameSql = $hasCompanyId ? "COALESCE(c.name, 'Company')" : "'Company'";

        $sql = "
            SELECT * FROM (
                SELECT
                    'repairer' AS source,
                    rq.quote_id AS quote_id,
                    rq.request_id AS request_id,
                    jr.title AS job_title,
                    rq.quoteAmount AS amount,
                    rq.status AS status,
                    rq.dateSubmitted AS created_at,
                    rq.repairer_id AS provider_id,
                    CONCAT(r.f_name, ' ', r.l_name) AS provider_name,
                    'Individual' AS provider_type,
                    r.profilePicture AS provider_avatar,
                    r.ratings AS provider_rating,
                    rq.estimatedDays AS estimated_days,
                    rq.warrantyPeriod AS warranty_period,
                    rq.materialsIncluded AS materials_included,
                    rq.message AS message,
                    rq.validUntil AS valid_until,
                    -- Company specific fields padded with NULL
                    NULL AS labor_cost,
                    NULL AS material_cost,
                    NULL AS transport_cost,
                    NULL AS other_charges,
                    NULL AS budget_type,
                    NULL AS payment_terms,
                    NULL AS payment_method,
                    NULL AS pricing_type,
                    NULL AS hourly_rate,
                    NULL AS work_schedule_type
                FROM repairerquote rq
                INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                INNER JOIN repairer r ON rq.repairer_id = r.repairer_id
                WHERE jr.user_id = :user_id_repairer
                $statusSqlRepairer
                $requestSqlRepairer

                UNION ALL

                SELECT
                    'company' AS source,
                    cq.quotation_id AS quote_id,
                    cq.request_id AS request_id,
                    jr.title AS job_title,
                    cq.total_amount AS amount,
                    cq.status AS status,
                    cq.created_at AS created_at,
                    NULL AS provider_id,
                    $companyProviderNameSql AS provider_name,
                    'Company' AS provider_type,
                    NULL AS provider_avatar,
                    NULL AS provider_rating,
                    cq.estimated_duration AS estimated_days,
                    cq.warranty_period AS warranty_period,
                    1 AS materials_included, -- Companies are expected to list material cost, assumed included if quote provided
                    cq.description AS message,
                    NULL AS valid_until, -- companyquotation doesn't have an expiration date yet
                    -- Company specific fields
                    cq.labor_cost AS labor_cost,
                    cq.material_cost AS material_cost,
                    cq.transport_cost AS transport_cost,
                    cq.other_charges AS other_charges,
                    cq.budget_type AS budget_type,
                    cq.payment_terms AS payment_terms,
                    cq.payment_method AS payment_method,
                    cq.pricing_type AS pricing_type,
                    cq.hourly_rate AS hourly_rate,
                    cq.work_schedule_type AS work_schedule_type
                FROM companyquotation cq
                INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                $companyJoinSql
                WHERE cq.user_id = :user_id_company
                $statusSqlCompany
                $requestSqlCompany
            ) q
            ORDER BY q.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    WHERE cq.user_id = :user_id_company AND cq.status = 'pending'
                ) AS pending_count
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id_repairer', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id_company', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function respondToQuote(int $userId, string $source, int $quoteId, string $decision): bool {
        if (!in_array($decision, ['accepted', 'rejected'], true)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            // First, find the request_id to ensure ownership and for subsequent updates
            $requestId = null;
            if ($source === 'repairer') {
                $checkSql = "SELECT rq.request_id FROM repairerquote rq INNER JOIN jobrequest jr ON rq.request_id = jr.request_id WHERE rq.quote_id = :quote_id AND jr.user_id = :user_id";
            } else {
                $checkSql = "SELECT cq.request_id FROM companyquotation cq INNER JOIN jobrequest jr ON cq.request_id = jr.request_id WHERE cq.quotation_id = :quote_id AND cq.user_id = :user_id";
            }
            
            $stmt = $this->pdo->prepare($checkSql);
            $stmt->execute([':quote_id' => $quoteId, ':user_id' => $userId]);
            $requestId = $stmt->fetchColumn();

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

            // If the decision is 'accepted', we need to reject competing quotes and update the job request
            if ($decision === 'accepted') {
                // Reject other pending company quotations for this request
                $rejectCompanySql = "UPDATE companyquotation SET status = 'rejected' WHERE request_id = :request_id AND status = 'pending' AND quotation_id != :exclude_id";
                // Reject other pending repairer quotes for this request
                $rejectRepairerSql = "UPDATE repairerquote SET status = 'rejected' WHERE request_id = :request_id AND status = 'pending' AND quote_id != :exclude_id_rep";

                if ($source === 'company') {
                    $stmtCompany = $this->pdo->prepare($rejectCompanySql);
                    $stmtCompany->execute([':request_id' => $requestId, ':exclude_id' => $quoteId]);
                    
                    $stmtRepairer = $this->pdo->prepare("UPDATE repairerquote SET status = 'rejected' WHERE request_id = :request_id AND status = 'pending'");
                    $stmtRepairer->execute([':request_id' => $requestId]);
                } else {
                    $stmtCompany = $this->pdo->prepare("UPDATE companyquotation SET status = 'rejected' WHERE request_id = :request_id AND status = 'pending'");
                    $stmtCompany->execute([':request_id' => $requestId]);
                    
                    $stmtRepairer = $this->pdo->prepare($rejectRepairerSql);
                    $stmtRepairer->execute([':request_id' => $requestId, ':exclude_id_rep' => $quoteId]);
                }

                // Update job request status to accepted
                $updateJobSql = "UPDATE jobrequest SET status = 'accepted' WHERE request_id = :request_id";
                $stmtJob = $this->pdo->prepare($updateJobSql);
                $stmtJob->execute([':request_id' => $requestId]);
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
