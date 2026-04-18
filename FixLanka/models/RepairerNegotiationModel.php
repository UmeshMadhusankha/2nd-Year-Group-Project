<?php

class RepairerNegotiationModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function listReceived(int $repairerId, int $limit = 100): array
    {
        return $this->listByStatusesForRepairer($repairerId, ['pending', 'countered'], $limit);
    }

    public function listAccepted(int $repairerId, int $limit = 100): array
    {
        return $this->listByStatusesForRepairer($repairerId, ['accepted'], $limit);
    }

    private function listByStatusesForRepairer(int $repairerId, array $statuses, int $limit): array
    {
        $safeLimit = max(1, min(200, $limit));
        $statusPlaceholders = [];
        $params = [':repairer_id' => $repairerId];
        foreach (array_values($statuses) as $index => $status) {
            $key = ':status_' . $index;
            $statusPlaceholders[] = $key;
            $params[$key] = $status;
        }

        $sql = "
            SELECT
                qn.*,
                COALESCE(
                    NULLIF(TRIM(CONCAT(COALESCE(u.f_name, ''), ' ', COALESCE(u.l_name, ''))), ''),
                    NULLIF(c.name, ''),
                    NULLIF(TRIM(CONCAT(COALESCE(r.f_name, ''), ' ', COALESCE(r.l_name, ''))), ''),
                    CONCAT(UCASE(LEFT(qn.sender_role, 1)), SUBSTRING(qn.sender_role, 2))
                ) AS sender_name,
                COALESCE(rq.quoteAmount, cq.total_amount, qn.listed_price) AS current_quote_amount,
                COALESCE(rq.status, cq.status, 'pending') AS current_quote_status,
                (
                    SELECT cn.proposed_price
                    FROM quote_negotiation cn
                    WHERE cn.quote_id = qn.quote_id
                      AND cn.quote_source = qn.quote_source
                      AND cn.request_id = qn.request_id
                      AND cn.request_type = qn.request_type
                      AND cn.sender_role = 'repairer'
                      AND cn.receiver_role = 'user'
                    ORDER BY cn.negotiation_id DESC
                    LIMIT 1
                ) AS latest_counter_price,
                COALESCE(jr.title, djr.title, qn.job_title) AS effective_job_title,
                COALESCE(jr.description, djr.description, qn.job_description) AS effective_job_description
            FROM quote_negotiation qn
            LEFT JOIN user u ON qn.sender_role = 'user' AND qn.sender_id = u.user_id
            LEFT JOIN company c ON qn.sender_role = 'company' AND qn.sender_id = c.company_id
            LEFT JOIN repairer r ON qn.sender_role = 'repairer' AND qn.sender_id = r.repairer_id
            LEFT JOIN repairerquote rq ON qn.quote_source = 'repairer' AND qn.quote_id = rq.quote_id
            LEFT JOIN companyquotation cq ON qn.quote_source = 'company' AND qn.quote_id = cq.quotation_id
            LEFT JOIN jobrequest jr ON qn.request_type = 'regular' AND qn.request_id = jr.request_id
            LEFT JOIN directjobrequest djr ON qn.request_type = 'direct' AND qn.request_id = djr.request_id
            WHERE qn.receiver_role = 'repairer'
              AND qn.receiver_id = :repairer_id
              AND qn.status IN (" . implode(', ', $statusPlaceholders) . ")
            ORDER BY qn.updated_at DESC, qn.negotiation_id DESC
            LIMIT {$safeLimit}
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function acceptReceived(int $negotiationId, int $repairerId): bool
    {
        $this->pdo->beginTransaction();
        try {
            $selectStmt = $this->pdo->prepare(
                "SELECT *
                 FROM quote_negotiation
                 WHERE negotiation_id = :negotiation_id
                   AND receiver_role = 'repairer'
                   AND receiver_id = :repairer_id
                   AND status IN ('pending', 'countered')
                 LIMIT 1
                 FOR UPDATE"
            );
            $selectStmt->execute([
                ':negotiation_id' => $negotiationId,
                ':repairer_id' => $repairerId,
            ]);
            $row = $selectStmt->fetch(PDO::FETCH_ASSOC) ?: null;
            if (!$row) {
                $this->pdo->rollBack();
                return false;
            }

            $newPrice = (float) ($row['proposed_price'] ?? 0);
            if ($newPrice <= 0) {
                throw new RuntimeException('Invalid proposed price in negotiation');
            }

            $quoteSource = strtolower((string) ($row['quote_source'] ?? ''));
            $quoteId = (int) ($row['quote_id'] ?? 0);
            if ($quoteId <= 0) {
                throw new RuntimeException('Invalid quote id in negotiation');
            }

            if ($quoteSource === 'repairer') {
                $quoteStmt = $this->pdo->prepare(
                    "UPDATE repairerquote
                     SET quoteAmount = :quote_amount,
                         status = 'accepted'
                     WHERE quote_id = :quote_id"
                );
                $quoteStmt->execute([
                    ':quote_amount' => $newPrice,
                    ':quote_id' => $quoteId,
                ]);
            } elseif ($quoteSource === 'company') {
                $quoteStmt = $this->pdo->prepare(
                    "UPDATE companyquotation
                     SET total_amount = :total_amount,
                         status = 'accepted'
                     WHERE quotation_id = :quote_id"
                );
                $quoteStmt->execute([
                    ':total_amount' => $newPrice,
                    ':quote_id' => $quoteId,
                ]);
            } else {
                throw new RuntimeException('Unsupported quote source');
            }

            $requestId = (int) ($row['request_id'] ?? 0);
            $requestType = strtolower((string) ($row['request_type'] ?? 'regular')) === 'direct' ? 'direct' : 'regular';
            if ($requestId <= 0) {
                throw new RuntimeException('Invalid request id in negotiation');
            }

            $targetRequestStatus = $this->resolveWorkflowActiveStatus($requestType);
            if ($requestType === 'direct') {
                $requestStmt = $this->pdo->prepare(
                    'UPDATE directjobrequest
                     SET status = :status
                     WHERE request_id = :request_id'
                );
            } else {
                $requestStmt = $this->pdo->prepare(
                    'UPDATE jobrequest
                     SET status = :status
                     WHERE request_id = :request_id'
                );
            }
            $requestStmt->execute([
                ':status' => $targetRequestStatus,
                ':request_id' => $requestId,
            ]);

            $negotiationStmt = $this->pdo->prepare(
                "UPDATE quote_negotiation
                 SET status = 'accepted', updated_at = CURRENT_TIMESTAMP
                 WHERE negotiation_id = :negotiation_id"
            );
            $negotiationStmt->execute([':negotiation_id' => $negotiationId]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    private function resolveWorkflowActiveStatus(string $requestType): string
    {
        $table = strtolower($requestType) === 'direct' ? 'directjobrequest' : 'jobrequest';
        $stmt = $this->pdo->prepare(
            "SELECT COLUMN_TYPE
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = 'status'
             LIMIT 1"
        );
        $stmt->execute([':table_name' => $table]);
        $columnType = strtolower((string) ($stmt->fetchColumn() ?: ''));

        if ($columnType !== '' && strpos($columnType, 'active') !== false) {
            return 'active';
        }

        return 'in_progress';
    }

    public function counterReceived(int $negotiationId, int $repairerId, float $counterPrice, ?string $message): ?array
    {
        if ($counterPrice <= 0) {
            throw new InvalidArgumentException('Counter amount must be greater than zero');
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                "SELECT *
                 FROM quote_negotiation
                 WHERE negotiation_id = :negotiation_id
                   AND receiver_role = 'repairer'
                   AND receiver_id = :repairer_id
                   AND status IN ('pending', 'countered')
                 LIMIT 1
                 FOR UPDATE"
            );
            $stmt->execute([
                ':negotiation_id' => $negotiationId,
                ':repairer_id' => $repairerId,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

            if (!$row) {
                $this->pdo->rollBack();
                return null;
            }

            $updateStmt = $this->pdo->prepare(
                "UPDATE quote_negotiation
                 SET status = 'countered', updated_at = CURRENT_TIMESTAMP
                 WHERE negotiation_id = :negotiation_id"
            );
            $updateStmt->execute([':negotiation_id' => $negotiationId]);

            $insertStmt = $this->pdo->prepare(
                'INSERT INTO quote_negotiation
                    (request_id, request_type, quote_id, quote_source, job_title, job_description, listed_price, proposed_price, message,
                     sender_id, sender_role, receiver_id, receiver_role, status)
                 VALUES
                    (:request_id, :request_type, :quote_id, :quote_source, :job_title, :job_description, :listed_price, :proposed_price, :message,
                     :sender_id, :sender_role, :receiver_id, :receiver_role, :status)'
            );

            $insertStmt->execute([
                ':request_id' => (int) $row['request_id'],
                ':request_type' => (string) $row['request_type'],
                ':quote_id' => (int) $row['quote_id'],
                ':quote_source' => (string) $row['quote_source'],
                ':job_title' => (string) ($row['job_title'] ?? ''),
                ':job_description' => (string) ($row['job_description'] ?? ''),
                ':listed_price' => (float) ($row['proposed_price'] ?? 0),
                ':proposed_price' => $counterPrice,
                ':message' => $message,
                ':sender_id' => $repairerId,
                ':sender_role' => 'repairer',
                ':receiver_id' => (int) $row['sender_id'],
                ':receiver_role' => (string) $row['sender_role'],
                ':status' => 'countered',
            ]);

            $newId = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();

            return [
                'counter_negotiation_id' => $newId,
                'original_negotiation_id' => $negotiationId,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function rejectReceived(int $negotiationId, int $repairerId): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE quote_negotiation
             SET status = 'rejected',
                 updated_at = CURRENT_TIMESTAMP
             WHERE negotiation_id = :negotiation_id
               AND receiver_role = 'repairer'
               AND receiver_id = :repairer_id
               AND status IN ('pending', 'countered')"
        );
        $stmt->execute([
            ':negotiation_id' => $negotiationId,
            ':repairer_id' => $repairerId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
