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

    public function getUserQuotes(int $userId, int $limit = 20, int $offset = 0, ?string $status = null): array {
        $limit = max(1, min(50, (int)$limit));
        $offset = max(0, (int)$offset);

        // IMPORTANT: PDO MySQL can throw "Invalid parameter number" when the same
        // named placeholder is used multiple times (when emulation is off). Use
        // distinct placeholders for each occurrence.
        $params = [
            ':user_id_repairer' => $userId,
            ':user_id_company' => $userId,
        ];
        $statusSqlRepairer = '';
        $statusSqlCompany = '';
        if ($status !== null) {
            $params[':status_repairer'] = $status;
            $params[':status_company'] = $status;
            $statusSqlRepairer = ' AND rq.status = :status_repairer ';
            $statusSqlCompany = ' AND cq.status = :status_company ';
        }

        $hasCompanyId = $this->companyQuotationHasCompanyId();

        $companyJoinSql = $hasCompanyId ? "LEFT JOIN company c ON cq.company_id = c.company_id" : "";
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
                    r.ratings AS provider_rating
                FROM repairerquote rq
                INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                INNER JOIN repairer r ON rq.repairer_id = r.repairer_id
                WHERE jr.user_id = :user_id_repairer
                $statusSqlRepairer

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
                    NULL AS provider_rating
                FROM companyquotation cq
                INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                $companyJoinSql
                WHERE cq.user_id = :user_id_company
                $statusSqlCompany
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

        if ($source === 'repairer') {
            $sql = "
                UPDATE repairerquote rq
                INNER JOIN jobrequest jr ON rq.request_id = jr.request_id
                SET rq.status = :status
                WHERE rq.quote_id = :quote_id
                  AND jr.user_id = :user_id
                  AND rq.status = 'pending'
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $decision, PDO::PARAM_STR);
            $stmt->bindValue(':quote_id', $quoteId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }

        if ($source === 'company') {
            $sql = "
                UPDATE companyquotation cq
                INNER JOIN jobrequest jr ON cq.request_id = jr.request_id
                SET cq.status = :status
                WHERE cq.quotation_id = :quote_id
                  AND cq.user_id = :user_id
                  AND cq.status = 'pending'
            ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $decision, PDO::PARAM_STR);
            $stmt->bindValue(':quote_id', $quoteId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }

        return false;
    }
}
