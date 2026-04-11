<?php
require_once __DIR__ . '/../config/database.php';

require_once __DIR__ . '/SystemNotificationService.php';

class UserQuotesModel {
    private PDO $pdo;
    private ?bool $companyQuotationHasCompanyId = null;
    private ?string $jobRequestActivationStatus = null;

    public function __construct(?PDO $connection = null) {
        if (!($connection instanceof PDO)) {
            global $pdo;
            if ($pdo instanceof PDO) {
                $connection = $pdo;
            } else {
                $connection = getDatabaseConnection();
            }
        }

        $this->pdo = $connection;
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

    private function getJobRequestActivationStatus(): string {
        if ($this->jobRequestActivationStatus !== null) {
            return $this->jobRequestActivationStatus;
        }

        try {
            $stmt = $this->pdo->prepare(
                "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'jobrequest' AND COLUMN_NAME = 'status'"
            );
            $stmt->execute();
            $columnType = strtolower((string)$stmt->fetchColumn());

            if ($columnType !== '' && strpos($columnType, "'active'") !== false) {
                $this->jobRequestActivationStatus = 'active';
            } else {
                $this->jobRequestActivationStatus = 'in_progress';
            }
        } catch (Throwable $e) {
            $this->jobRequestActivationStatus = 'in_progress';
        }

        return $this->jobRequestActivationStatus;
    }

    public function getUserQuotes(int $userId, int $limit = 20, int $offset = 0, ?string $status = null): array {
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
        $companyProviderIdSql = $hasCompanyId ? "cq.company_id" : "NULL";

        $sql = "
            SELECT * FROM (
                SELECT
                    'repairer' AS source,
                    rq.quote_id AS quote_id,
                    rq.request_id AS request_id,
                    jr.title AS job_title,
                    jr.created_at AS job_posted_at,
                    jr.status AS job_status,
                    jr.urgency AS job_urgency,
                    jr.service_provider_type AS job_provider_preference,
                    c.name AS category_name,
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
                    rq.validUntil AS valid_until,
                    rq.materialsIncluded AS materials_included,
                    rq.message AS quote_message,
                    NULL AS labor_cost,
                    NULL AS material_cost,
                    NULL AS transport_cost,
                    NULL AS other_charges,
                    NULL AS company_start_date,
                    NULL AS company_completion_date,
                    NULL AS company_estimated_duration,
                    NULL AS company_payment_terms,
                    NULL AS company_warranty_text,
                    NULL AS company_additional_terms
                FROM repairerquote rq
                INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                LEFT JOIN category c ON jr.category_id = c.category_id
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
                    jr.created_at AS job_posted_at,
                    jr.status AS job_status,
                    jr.urgency AS job_urgency,
                    jr.service_provider_type AS job_provider_preference,
                    cat.name AS category_name,
                    cq.total_amount AS amount,
                    cq.status AS status,
                    cq.created_at AS created_at,
                    $companyProviderIdSql AS provider_id,
                    $companyProviderNameSql AS provider_name,
                    'Company' AS provider_type,
                    NULL AS provider_avatar,
                    NULL AS provider_rating,
                    NULL AS estimated_days,
                    NULL AS warranty_period,
                    NULL AS valid_until,
                    NULL AS materials_included,
                    cq.description AS quote_message,
                    cq.labor_cost AS labor_cost,
                    cq.material_cost AS material_cost,
                    cq.transport_cost AS transport_cost,
                    cq.other_charges AS other_charges,
                    cq.start_date AS company_start_date,
                    cq.completion_date AS company_completion_date,
                    cq.estimated_duration AS company_estimated_duration,
                    cq.payment_terms AS company_payment_terms,
                    cq.warranty_period AS company_warranty_text,
                    cq.additional_terms AS company_additional_terms
                FROM companyquotation cq
                INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                LEFT JOIN category cat ON jr.category_id = cat.category_id
                $companyJoinSql
                WHERE jr.user_id = :user_id_company
                $statusSqlCompany
                $requestSqlCompany
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

        if (!in_array($source, ['repairer', 'company'], true)) {
            return false;
        }

        try {
            $this->pdo->beginTransaction();

            if ($source === 'repairer') {
                $findSql = "
                    SELECT rq.request_id
                    FROM repairerquote rq
                    INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                    WHERE rq.quote_id = :quote_id
                      AND jr.user_id = :user_id
                      AND rq.status = 'pending'
                    LIMIT 1
                ";
                $updateSql = "
                    UPDATE repairerquote rq
                    INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                    SET rq.status = :status
                    WHERE rq.quote_id = :quote_id
                      AND jr.user_id = :user_id
                      AND rq.status = 'pending'
                ";
            } else {
                $findSql = "
                    SELECT cq.request_id
                    FROM companyquotation cq
                    INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                    WHERE cq.quotation_id = :quote_id
                      AND jr.user_id = :user_id
                      AND cq.status = 'pending'
                    LIMIT 1
                ";
                $updateSql = "
                    UPDATE companyquotation cq
                    INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                    SET cq.status = :status
                    WHERE cq.quotation_id = :quote_id
                      AND jr.user_id = :user_id
                      AND cq.status = 'pending'
                ";
            }

            $findStmt = $this->pdo->prepare($findSql);
            $findStmt->bindValue(':quote_id', $quoteId, PDO::PARAM_INT);
            $findStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $findStmt->execute();
            $requestId = (int)$findStmt->fetchColumn();

            if ($requestId <= 0) {
                $this->pdo->rollBack();
                return false;
            }

            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->bindValue(':status', $decision, PDO::PARAM_STR);
            $updateStmt->bindValue(':quote_id', $quoteId, PDO::PARAM_INT);
            $updateStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $updateStmt->execute();

            if ($updateStmt->rowCount() <= 0) {
                $this->pdo->rollBack();
                return false;
            }

            if ($decision === 'accepted') {
                $jobStatus = $this->getJobRequestActivationStatus();
                $jobStmt = $this->pdo->prepare(
                    "UPDATE jobrequest SET status = :job_status WHERE request_id = :request_id AND user_id = :user_id"
                );
                $jobStmt->bindValue(':job_status', $jobStatus, PDO::PARAM_STR);
                $jobStmt->bindValue(':request_id', $requestId, PDO::PARAM_INT);
                $jobStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $jobStmt->execute();
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('Error responding to quote: ' . $e->getMessage());
            return false;
        }
    }
}
