<?php

class QuoteNegotiationModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function normalizeRequestType(?string $requestType): string
    {
        return strtolower(trim((string) $requestType)) === 'direct' ? 'direct' : 'regular';
    }

    private function normalizeSource(string $source): string
    {
        $normalized = strtolower(trim($source));
        if (!in_array($normalized, ['repairer', 'company'], true)) {
            throw new InvalidArgumentException('Invalid quote source');
        }
        return $normalized;
    }

    private function resolveWorkflowActiveStatus(string $requestType): string
    {
        $table = $this->normalizeRequestType($requestType) === 'direct' ? 'directjobrequest' : 'jobrequest';
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

    private function applyAcceptedQuoteEffects(array $row): void
    {
        $quoteSource = $this->normalizeSource((string) ($row['quote_source'] ?? ''));
        $quoteId = (int) ($row['quote_id'] ?? 0);
        $requestId = (int) ($row['request_id'] ?? 0);
        $requestType = $this->normalizeRequestType((string) ($row['request_type'] ?? 'regular'));
        $newPrice = (float) ($row['proposed_price'] ?? 0);

        if ($quoteId <= 0 || $requestId <= 0 || $newPrice <= 0) {
            throw new RuntimeException('Invalid negotiation data for acceptance');
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
        } else {
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
        }

        $nextStatus = $this->resolveWorkflowActiveStatus($requestType);
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
            ':status' => $nextStatus,
            ':request_id' => $requestId,
        ]);
    }

    private function hasOpenUserNegotiation(int $userId, int $quoteId, string $source, string $requestType): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM quote_negotiation
             WHERE quote_id = :quote_id
               AND quote_source = :quote_source
               AND request_type = :request_type
               AND sender_id = :sender_id
               AND sender_role = :sender_role
               AND status IN (\'pending\', \'countered\')'
        );
        $stmt->execute([
            ':quote_id' => $quoteId,
            ':quote_source' => $this->normalizeSource($source),
            ':request_type' => $this->normalizeRequestType($requestType),
            ':sender_id' => $userId,
            ':sender_role' => 'user',
        ]);

        return ((int) $stmt->fetchColumn()) > 0;
    }

    private function quoteContextForUser(int $userId, int $quoteId, string $source, string $requestType): ?array
    {
        $normalizedSource = $this->normalizeSource($source);
        $normalizedRequestType = $this->normalizeRequestType($requestType);
        $requestTable = $normalizedRequestType === 'direct' ? 'directjobrequest' : 'jobrequest';

        if ($normalizedSource === 'repairer') {
            $sql = "
                SELECT
                    rq.quote_id AS quote_id,
                    rq.request_id AS request_id,
                    rq.quoteAmount AS listed_price,
                    rq.status AS quote_status,
                    req.title AS job_title,
                    req.description AS job_description,
                    rq.repairer_id AS provider_id,
                    'repairer' AS provider_role
                FROM repairerquote rq
                INNER JOIN {$requestTable} req ON req.request_id = rq.request_id
                WHERE rq.quote_id = :quote_id
                  AND req.user_id = :user_id
                LIMIT 1
            ";
        } else {
            $sql = "
                SELECT
                    cq.quotation_id AS quote_id,
                    cq.request_id AS request_id,
                    cq.total_amount AS listed_price,
                    cq.status AS quote_status,
                    req.title AS job_title,
                    req.description AS job_description,
                    cq.company_id AS provider_id,
                    'company' AS provider_role
                FROM companyquotation cq
                INNER JOIN {$requestTable} req ON req.request_id = cq.request_id
                WHERE cq.quotation_id = :quote_id
                  AND req.user_id = :user_id
                LIMIT 1
            ";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':quote_id' => $quoteId,
            ':user_id' => $userId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$row) {
            return null;
        }

        $row['request_type'] = $normalizedRequestType;
        $row['quote_source'] = $normalizedSource;
        return $row;
    }

    public function createByUser(int $userId, int $quoteId, string $source, string $requestType, float $proposedPrice, ?string $message): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('Invalid user id');
        }
        if ($quoteId <= 0) {
            throw new InvalidArgumentException('Invalid quote id');
        }
        if ($proposedPrice <= 0) {
            throw new InvalidArgumentException('Negotiation price must be greater than zero');
        }

        $context = $this->quoteContextForUser($userId, $quoteId, $source, $requestType);
        if (!$context) {
            throw new RuntimeException('Quote not found for this user');
        }

        $quoteStatus = strtolower((string) ($context['quote_status'] ?? ''));
        if ($quoteStatus !== 'pending') {
            throw new RuntimeException('Negotiation can be sent only while quote is pending');
        }

        if ($this->hasOpenUserNegotiation($userId, $quoteId, $source, $requestType)) {
            throw new RuntimeException('You already have a pending negotiation for this quote');
        }

        $providerId = (int) ($context['provider_id'] ?? 0);
        $providerRole = strtolower((string) ($context['provider_role'] ?? ''));
        if ($providerId <= 0 || !in_array($providerRole, ['repairer', 'company'], true)) {
            throw new RuntimeException('Provider details are missing for this quote');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO quote_negotiation
                (request_id, request_type, quote_id, quote_source, job_title, job_description, listed_price, proposed_price, message,
                 sender_id, sender_role, receiver_id, receiver_role, status)
             VALUES
                (:request_id, :request_type, :quote_id, :quote_source, :job_title, :job_description, :listed_price, :proposed_price, :message,
                 :sender_id, :sender_role, :receiver_id, :receiver_role, :status)'
        );

        $stmt->execute([
            ':request_id' => (int) ($context['request_id'] ?? 0),
            ':request_type' => (string) ($context['request_type'] ?? 'regular'),
            ':quote_id' => (int) ($context['quote_id'] ?? 0),
            ':quote_source' => (string) ($context['quote_source'] ?? 'repairer'),
            ':job_title' => (string) ($context['job_title'] ?? ''),
            ':job_description' => (string) ($context['job_description'] ?? ''),
            ':listed_price' => (float) ($context['listed_price'] ?? 0),
            ':proposed_price' => $proposedPrice,
            ':message' => $message,
            ':sender_id' => $userId,
            ':sender_role' => 'user',
            ':receiver_id' => $providerId,
            ':receiver_role' => $providerRole,
            ':status' => 'pending',
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->getByIdForUser($id, $userId) ?? [];
    }

    public function getByIdForUser(int $negotiationId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM quote_negotiation
             WHERE negotiation_id = :negotiation_id
                             AND (sender_id = :sender_user_id OR receiver_id = :receiver_user_id)
             LIMIT 1'
        );
        $stmt->execute([
            ':negotiation_id' => $negotiationId,
                        ':sender_user_id' => $userId,
                        ':receiver_user_id' => $userId,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function listByQuoteForUser(int $userId, int $quoteId, string $source, string $requestType, int $limit = 20): array
    {
        $normalizedSource = $this->normalizeSource($source);
        $normalizedRequestType = $this->normalizeRequestType($requestType);
        $safeLimit = max(1, min(100, $limit));

        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM quote_negotiation
             WHERE quote_id = :quote_id
               AND quote_source = :quote_source
               AND request_type = :request_type
                             AND (sender_id = :sender_user_id OR receiver_id = :receiver_user_id)
             ORDER BY negotiation_id DESC
             LIMIT {$safeLimit}"
        );
        $stmt->execute([
            ':quote_id' => $quoteId,
            ':quote_source' => $normalizedSource,
            ':request_type' => $normalizedRequestType,
                        ':sender_user_id' => $userId,
                        ':receiver_user_id' => $userId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function respondToReceivedByUser(int $userId, int $negotiationId, string $decision): bool
    {
        $normalizedDecision = strtolower(trim($decision));
        if (!in_array($normalizedDecision, ['accept', 'reject'], true)) {
            throw new InvalidArgumentException('Invalid decision');
        }

        $this->pdo->beginTransaction();
        try {
            $selectStmt = $this->pdo->prepare(
                "SELECT *
                 FROM quote_negotiation
                 WHERE negotiation_id = :negotiation_id
                   AND receiver_role = 'user'
                   AND receiver_id = :user_id
                   AND status IN ('pending', 'countered')
                 LIMIT 1
                 FOR UPDATE"
            );
            $selectStmt->execute([
                ':negotiation_id' => $negotiationId,
                ':user_id' => $userId,
            ]);

            $row = $selectStmt->fetch(PDO::FETCH_ASSOC) ?: null;
            if (!$row) {
                $this->pdo->rollBack();
                return false;
            }

            if ($normalizedDecision === 'accept') {
                $this->applyAcceptedQuoteEffects($row);
                $targetStatus = 'accepted';
            } else {
                $targetStatus = 'rejected';
            }

            $updateStmt = $this->pdo->prepare(
                'UPDATE quote_negotiation
                 SET status = :status,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE negotiation_id = :negotiation_id'
            );
            $updateStmt->execute([
                ':status' => $targetStatus,
                ':negotiation_id' => $negotiationId,
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
