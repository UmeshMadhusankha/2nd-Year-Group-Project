<?php

class JobCollaborationModel
{
    private PDO $pdo;
    private ?array $quoteStatusEnumCache = null;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function normalizeRequestType(?string $requestType): string
    {
        return strtolower(trim((string) $requestType)) === 'direct' ? 'direct' : 'regular';
    }

    private function normalizeActorRole(string $actorRole): string
    {
        $role = strtolower(trim($actorRole));
        if (!in_array($role, ['user', 'repairer', 'company'], true)) {
            throw new InvalidArgumentException('Unsupported actor role');
        }
        return $role;
    }

    private function actorCanAccess(array $row, int $actorId, string $actorRole): bool
    {
        if ($actorRole === 'user') {
            return (int) ($row['user_id'] ?? 0) === $actorId;
        }

        return (int) ($row['provider_id'] ?? 0) === $actorId
            && strtolower((string) ($row['provider_role'] ?? '')) === $actorRole;
    }

    private function addEvent(int $collaborationId, int $actorId, string $actorRole, string $eventType, ?string $message = null, ?float $amount = null, ?array $meta = null): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO job_collaboration_event
                (collaboration_id, actor_id, actor_role, event_type, message, amount, meta_json)
             VALUES
                (:collaboration_id, :actor_id, :actor_role, :event_type, :message, :amount, :meta_json)'
        );

        $stmt->execute([
            ':collaboration_id' => $collaborationId,
            ':actor_id' => $actorId,
            ':actor_role' => $actorRole,
            ':event_type' => $eventType,
            ':message' => $message,
            ':amount' => $amount,
            ':meta_json' => $meta ? json_encode($meta, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    public function createFromAcceptedQuote(int $userId, string $source, int $quoteId, int $requestId, string $requestType): array
    {
        $normalizedSource = strtolower(trim($source));
        if (!in_array($normalizedSource, ['repairer', 'company'], true)) {
            throw new InvalidArgumentException('Invalid quote source');
        }

        $normalizedRequestType = $this->normalizeRequestType($requestType);

        if ($normalizedSource === 'repairer') {
            $stmt = $this->pdo->prepare(
                'SELECT request_id, repairer_id AS provider_id, quoteAmount AS quoted_amount
                 FROM repairerquote
                 WHERE quote_id = :quote_id
                 LIMIT 1'
            );
        } else {
            $stmt = $this->pdo->prepare(
                'SELECT request_id, company_id AS provider_id, total_amount AS quoted_amount
                 FROM companyquotation
                 WHERE quotation_id = :quote_id
                 LIMIT 1'
            );
        }

        $stmt->execute([':quote_id' => $quoteId]);
        $quoteRow = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if (!$quoteRow) {
            throw new RuntimeException('Accepted quote could not be found');
        }

        $providerId = (int) ($quoteRow['provider_id'] ?? 0);
        if ($providerId <= 0) {
            throw new RuntimeException('Accepted quote has no valid provider');
        }

        if ((int) ($quoteRow['request_id'] ?? 0) !== $requestId) {
            throw new RuntimeException('Quote does not match selected request');
        }

        $requestTable = $normalizedRequestType === 'direct' ? 'directjobrequest' : 'jobrequest';
        $ownershipStmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$requestTable} WHERE request_id = :request_id AND user_id = :user_id");
        $ownershipStmt->execute([
            ':request_id' => $requestId,
            ':user_id' => $userId,
        ]);

        if ((int) $ownershipStmt->fetchColumn() <= 0) {
            throw new RuntimeException('You do not own this request');
        }

        $amount = (float) ($quoteRow['quoted_amount'] ?? 0);
        $providerRole = $normalizedSource;

        $insertStmt = $this->pdo->prepare(
            'INSERT INTO job_collaboration
                (request_id, request_type, quote_id, quote_source, user_id, provider_id, provider_role, base_price, agreed_price, current_phase)
             VALUES
                (:request_id, :request_type, :quote_id, :quote_source, :user_id, :provider_id, :provider_role, :base_price, :agreed_price, :current_phase)
             ON DUPLICATE KEY UPDATE
                quote_id = VALUES(quote_id),
                quote_source = VALUES(quote_source),
                provider_id = VALUES(provider_id),
                provider_role = VALUES(provider_role),
                base_price = VALUES(base_price),
                agreed_price = VALUES(agreed_price),
                updated_at = CURRENT_TIMESTAMP'
        );

        $insertStmt->execute([
            ':request_id' => $requestId,
            ':request_type' => $normalizedRequestType,
            ':quote_id' => $quoteId,
            ':quote_source' => $normalizedSource,
            ':user_id' => $userId,
            ':provider_id' => $providerId,
            ':provider_role' => $providerRole,
            ':base_price' => $amount,
            ':agreed_price' => $amount,
            ':current_phase' => 'in_progress',
        ]);

        $collaboration = $this->getByRequestForActor($requestId, $normalizedRequestType, $userId, 'user', false);
        if (!$collaboration) {
            throw new RuntimeException('Failed to initialize collaboration');
        }

        $eventCheckStmt = $this->pdo->prepare('SELECT COUNT(*) FROM job_collaboration_event WHERE collaboration_id = :collaboration_id');
        $eventCheckStmt->execute([':collaboration_id' => (int) $collaboration['collaboration_id']]);

        if ((int) $eventCheckStmt->fetchColumn() === 0) {
            $this->addEvent(
                (int) $collaboration['collaboration_id'],
                $userId,
                'user',
                'system',
                'Collaboration channel created when quotation was accepted.',
                $amount
            );
        }

        return $this->getByIdForActor((int) $collaboration['collaboration_id'], $userId, 'user');
    }

    public function bootstrapFromRequestIfMissing(int $userId, int $requestId, string $requestType): ?array
    {
        $normalizedRequestType = $this->normalizeRequestType($requestType);

        $existing = $this->getByRequestForActor($requestId, $normalizedRequestType, $userId, 'user');
        if ($existing) {
            return $existing;
        }

        $requestTable = $normalizedRequestType === 'direct' ? 'directjobrequest' : 'jobrequest';
        $requestStmt = $this->pdo->prepare("SELECT status FROM {$requestTable} WHERE request_id = :request_id AND user_id = :user_id LIMIT 1");
        $requestStmt->execute([
            ':request_id' => $requestId,
            ':user_id' => $userId,
        ]);
        $status = strtolower((string) $requestStmt->fetchColumn());

        if (!in_array($status, ['in_progress', 'completed'], true)) {
            return null;
        }

        $repairerStmt = $this->pdo->prepare('SELECT quote_id FROM repairerquote WHERE request_id = :request_id AND status IN ("accepted", "completed") ORDER BY quote_id DESC LIMIT 1');
        $repairerStmt->execute([':request_id' => $requestId]);
        $repairerQuoteId = (int) $repairerStmt->fetchColumn();
        if ($repairerQuoteId > 0) {
            return $this->createFromAcceptedQuote($userId, 'repairer', $repairerQuoteId, $requestId, $normalizedRequestType);
        }

        $companyStmt = $this->pdo->prepare('SELECT quotation_id FROM companyquotation WHERE request_id = :request_id AND status IN ("accepted", "successful") ORDER BY quotation_id DESC LIMIT 1');
        $companyStmt->execute([':request_id' => $requestId]);
        $companyQuoteId = (int) $companyStmt->fetchColumn();
        if ($companyQuoteId > 0) {
            return $this->createFromAcceptedQuote($userId, 'company', $companyQuoteId, $requestId, $normalizedRequestType);
        }

        return null;
    }

    public function bootstrapFromRequestIfMissingForProvider(int $providerId, string $providerRole, int $requestId, string $requestType): ?array
    {
        $normalizedProviderRole = $this->normalizeActorRole($providerRole);
        if (!in_array($normalizedProviderRole, ['repairer', 'company'], true)) {
            throw new InvalidArgumentException('Provider role must be repairer or company');
        }

        $normalizedRequestType = $this->normalizeRequestType($requestType);

        $existing = $this->getByRequestForActor($requestId, $normalizedRequestType, $providerId, $normalizedProviderRole);
        if ($existing) {
            return $existing;
        }

        $requestTable = $normalizedRequestType === 'direct' ? 'directjobrequest' : 'jobrequest';
        $requestStmt = $this->pdo->prepare("SELECT user_id, status FROM {$requestTable} WHERE request_id = :request_id LIMIT 1");
        $requestStmt->execute([':request_id' => $requestId]);
        $requestRow = $requestStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$requestRow) {
            return null;
        }

        $status = strtolower((string) ($requestRow['status'] ?? ''));
        if (!in_array($status, ['in_progress', 'completed'], true)) {
            return null;
        }

        $requestOwnerId = (int) ($requestRow['user_id'] ?? 0);
        if ($requestOwnerId <= 0) {
            return null;
        }

        if ($normalizedProviderRole === 'repairer') {
            $quoteStmt = $this->pdo->prepare(
                'SELECT quote_id
                 FROM repairerquote
                 WHERE request_id = :request_id
                   AND repairer_id = :provider_id
                                     AND status IN ("accepted", "completed")
                 ORDER BY quote_id DESC
                 LIMIT 1'
            );
            $quoteStmt->execute([
                ':request_id' => $requestId,
                ':provider_id' => $providerId,
            ]);
            $quoteId = (int) $quoteStmt->fetchColumn();
            if ($quoteId > 0) {
                return $this->createFromAcceptedQuote($requestOwnerId, 'repairer', $quoteId, $requestId, $normalizedRequestType);
            }
        }

        if ($normalizedProviderRole === 'company') {
            $quoteStmt = $this->pdo->prepare(
                'SELECT quotation_id
                 FROM companyquotation
                 WHERE request_id = :request_id
                   AND company_id = :provider_id
                                     AND status IN ("accepted", "successful")
                 ORDER BY quotation_id DESC
                 LIMIT 1'
            );
            $quoteStmt->execute([
                ':request_id' => $requestId,
                ':provider_id' => $providerId,
            ]);
            $quoteId = (int) $quoteStmt->fetchColumn();
            if ($quoteId > 0) {
                return $this->createFromAcceptedQuote($requestOwnerId, 'company', $quoteId, $requestId, $normalizedRequestType);
            }
        }

        return null;
    }

    public function getByRequestForActor(int $requestId, string $requestType, int $actorId, string $actorRole, bool $includeEvents = true): ?array
    {
        $normalizedRequestType = $this->normalizeRequestType($requestType);
        $normalizedActorRole = $this->normalizeActorRole($actorRole);

        $stmt = $this->pdo->prepare(
            'SELECT * FROM job_collaboration
             WHERE request_id = :request_id AND request_type = :request_type
             LIMIT 1'
        );
        $stmt->execute([
            ':request_id' => $requestId,
            ':request_type' => $normalizedRequestType,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$row || !$this->actorCanAccess($row, $actorId, $normalizedActorRole)) {
            return null;
        }

        return $this->enrichCollaborationRow($row, $includeEvents);
    }

    public function getByIdForActor(int $collaborationId, int $actorId, string $actorRole, bool $includeEvents = true): ?array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);

        $stmt = $this->pdo->prepare('SELECT * FROM job_collaboration WHERE collaboration_id = :collaboration_id LIMIT 1');
        $stmt->execute([':collaboration_id' => $collaborationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if (!$row || !$this->actorCanAccess($row, $actorId, $normalizedActorRole)) {
            return null;
        }

        return $this->enrichCollaborationRow($row, $includeEvents);
    }

    private function enrichCollaborationRow(array $row, bool $includeEvents): array
    {
        if ($includeEvents) {
            $eventsStmt = $this->pdo->prepare(
                'SELECT event_id, actor_id, actor_role, event_type, message, amount, meta_json, created_at
                 FROM job_collaboration_event
                 WHERE collaboration_id = :collaboration_id
                 ORDER BY event_id DESC
                 LIMIT 100'
            );
            $eventsStmt->execute([':collaboration_id' => (int) $row['collaboration_id']]);
            $events = $eventsStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            foreach ($events as &$event) {
                if (!empty($event['meta_json'])) {
                    $decoded = json_decode((string) $event['meta_json'], true);
                    $event['meta'] = is_array($decoded) ? $decoded : null;
                } else {
                    $event['meta'] = null;
                }
                unset($event['meta_json']);
            }
            unset($event);

            $row['events'] = $events;
        }

        return $row;
    }

    private function getQuoteStatusEnumOptions(): array
    {
        if ($this->quoteStatusEnumCache !== null) {
            return $this->quoteStatusEnumCache;
        }

        $result = [
            'repairerquote' => ['pending', 'accepted', 'completed', 'rejected', 'expired'],
            'companyquotation' => ['pending', 'accepted', 'rejected', 'successful'],
        ];

        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM repairerquote LIKE 'status'");
            $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if ($row && isset($row['Type'])) {
                preg_match_all("/'([^']+)'/", (string) $row['Type'], $matches);
                if (!empty($matches[1])) {
                    $result['repairerquote'] = array_values(array_unique(array_map('strtolower', $matches[1])));
                }
            }
        } catch (Throwable $e) {
            // Keep defaults if schema inspection fails.
        }

        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM companyquotation LIKE 'status'");
            $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
            if ($row && isset($row['Type'])) {
                preg_match_all("/'([^']+)'/", (string) $row['Type'], $matches);
                if (!empty($matches[1])) {
                    $result['companyquotation'] = array_values(array_unique(array_map('strtolower', $matches[1])));
                }
            }
        } catch (Throwable $e) {
            // Keep defaults if schema inspection fails.
        }

        $this->quoteStatusEnumCache = $result;
        return $this->quoteStatusEnumCache;
    }

    private function syncAcceptedQuoteStatus(array $collaborationRow, bool $isFinalized): void
    {
        $source = strtolower((string) ($collaborationRow['quote_source'] ?? ''));
        $quoteId = (int) ($collaborationRow['quote_id'] ?? 0);
        if ($quoteId <= 0 || !in_array($source, ['repairer', 'company'], true)) {
            return;
        }

        $enumOptions = $this->getQuoteStatusEnumOptions();

        if ($source === 'repairer') {
            $allowed = $enumOptions['repairerquote'] ?? [];
            $next = ($isFinalized && in_array('completed', $allowed, true)) ? 'completed' : 'accepted';

            $stmt = $this->pdo->prepare(
                'UPDATE repairerquote
                 SET status = :status
                 WHERE quote_id = :quote_id'
            );
            $stmt->execute([
                ':status' => $next,
                ':quote_id' => $quoteId,
            ]);
            return;
        }

        $allowed = $enumOptions['companyquotation'] ?? [];
        $next = ($isFinalized && in_array('successful', $allowed, true)) ? 'successful' : 'accepted';

        $stmt = $this->pdo->prepare(
            'UPDATE companyquotation
             SET status = :status
             WHERE quotation_id = :quote_id'
        );
        $stmt->execute([
            ':status' => $next,
            ':quote_id' => $quoteId,
        ]);
    }

    private function syncPhaseAndRequestStatus(int $collaborationId): void
    {
        $stmt = $this->pdo->prepare(
            'SELECT collaboration_id, request_id, request_type, pending_price,
                    user_completed_at, provider_completed_at,
                    user_payment_confirmed_at, provider_payment_confirmed_at
             FROM job_collaboration
             WHERE collaboration_id = :collaboration_id
             LIMIT 1'
        );
        $stmt->execute([':collaboration_id' => $collaborationId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if (!$row) {
            throw new RuntimeException('Collaboration not found while syncing state');
        }

        $hasUserCompleted = !empty($row['user_completed_at']);
        $hasProviderCompleted = !empty($row['provider_completed_at']);
        $hasUserPaid = !empty($row['user_payment_confirmed_at']);
        $hasProviderPaid = !empty($row['provider_payment_confirmed_at']);
        $hasPendingPrice = $row['pending_price'] !== null;
        $isFinalized = $hasUserCompleted && $hasProviderCompleted && $hasUserPaid && $hasProviderPaid;

        if ($hasUserPaid && $hasProviderPaid) {
            $nextPhase = 'review';
        } elseif ($hasUserCompleted && $hasProviderCompleted) {
            $nextPhase = 'payment_verification';
        } elseif ($hasUserCompleted || $hasProviderCompleted) {
            $nextPhase = 'completion_verification';
        } elseif ($hasPendingPrice) {
            $nextPhase = 'negotiation';
        } else {
            $nextPhase = 'in_progress';
        }

        $phaseStmt = $this->pdo->prepare(
            'UPDATE job_collaboration
             SET current_phase = :phase,
                 updated_at = CURRENT_TIMESTAMP
             WHERE collaboration_id = :collaboration_id'
        );
        $phaseStmt->execute([
            ':phase' => $nextPhase,
            ':collaboration_id' => $collaborationId,
        ]);

        if (strtolower((string) ($row['request_type'] ?? 'regular')) === 'regular') {
            $requestId = (int) ($row['request_id'] ?? 0);
            if ($requestId > 0) {
                if ($isFinalized) {
                    $requestStmt = $this->pdo->prepare('UPDATE jobrequest SET status = :status WHERE request_id = :request_id');
                    $requestStmt->execute([
                        ':status' => 'completed',
                        ':request_id' => $requestId,
                    ]);
                } else {
                    $requestStmt = $this->pdo->prepare(
                        "UPDATE jobrequest
                         SET status = :status
                         WHERE request_id = :request_id
                           AND status <> 'cancelled'"
                    );
                    $requestStmt->execute([
                        ':status' => 'in_progress',
                        ':request_id' => $requestId,
                    ]);
                }
            }
        }

        $this->syncAcceptedQuoteStatus($row, $isFinalized);
    }

    public function postNote(int $collaborationId, int $actorId, string $actorRole, string $message): array
    {
        $trimmed = trim($message);
        if ($trimmed === '') {
            throw new InvalidArgumentException('Message cannot be empty');
        }

        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $actorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        $this->addEvent($collaborationId, $actorId, strtolower($actorRole), 'note', $trimmed);
        return $this->getByIdForActor($collaborationId, $actorId, $actorRole);
    }

    public function proposePrice(int $collaborationId, int $actorId, string $actorRole, float $proposedPrice, ?string $message = null): array
    {
        if ($proposedPrice <= 0) {
            throw new InvalidArgumentException('Proposed price must be greater than zero');
        }

        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        $phase = strtolower((string) ($collaboration['current_phase'] ?? ''));
        if (in_array($phase, ['completed', 'cancelled'], true)) {
            throw new RuntimeException('Cannot negotiate on a closed collaboration');
        }

        $stmt = $this->pdo->prepare(
            'UPDATE job_collaboration
             SET pending_price = :pending_price,
                 pending_price_actor_role = :pending_actor_role,
                 pending_price_note = :pending_note,
                 current_phase = :phase,
                 updated_at = CURRENT_TIMESTAMP
             WHERE collaboration_id = :collaboration_id'
        );
        $stmt->execute([
            ':pending_price' => $proposedPrice,
            ':pending_actor_role' => $normalizedActorRole,
            ':pending_note' => $message,
            ':phase' => 'negotiation',
            ':collaboration_id' => $collaborationId,
        ]);

        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'price_proposed', $message, $proposedPrice);
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Phase moved to negotiation');

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function respondToPendingPrice(int $collaborationId, int $actorId, string $actorRole, bool $accept, ?string $message = null): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        $pendingPrice = $collaboration['pending_price'];
        $pendingActorRole = strtolower((string) ($collaboration['pending_price_actor_role'] ?? ''));

        if ($pendingPrice === null) {
            throw new RuntimeException('There is no pending price proposal to respond to');
        }

        if ($pendingActorRole === $normalizedActorRole) {
            throw new RuntimeException('The proposer cannot respond to their own proposal');
        }

        if ($accept) {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET agreed_price = pending_price,
                     pending_price = NULL,
                     pending_price_actor_role = NULL,
                     pending_price_note = NULL,
                     current_phase = :phase,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([
                ':phase' => 'in_progress',
                ':collaboration_id' => $collaborationId,
            ]);

            $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'price_accepted', $message, (float) $pendingPrice);
            $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Phase moved to in progress');
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET pending_price = NULL,
                     pending_price_actor_role = NULL,
                     pending_price_note = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);

            $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'price_rejected', $message, (float) $pendingPrice);
        }

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function markCompleted(int $collaborationId, int $actorId, string $actorRole): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        if ($normalizedActorRole === 'user') {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET user_completed_at = COALESCE(user_completed_at, NOW()),
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET provider_completed_at = COALESCE(provider_completed_at, NOW()),
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        }

        $this->syncPhaseAndRequestStatus($collaborationId);

        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'completed_marked', 'Marked job completion');
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Collaboration state recalculated after completion update');

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function confirmPayment(int $collaborationId, int $actorId, string $actorRole): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        if (empty($collaboration['user_completed_at']) || empty($collaboration['provider_completed_at'])) {
            throw new RuntimeException('Both parties must mark completion before payment confirmation');
        }

        if ($normalizedActorRole === 'user') {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET user_payment_confirmed_at = COALESCE(user_payment_confirmed_at, NOW()),
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET provider_payment_confirmed_at = COALESCE(provider_payment_confirmed_at, NOW()),
                     user_payment_confirmed_at = COALESCE(user_payment_confirmed_at, NOW()),
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        }

        $this->syncPhaseAndRequestStatus($collaborationId);

        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'payment_confirmed', 'Payment marked as completed');
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Collaboration state recalculated after payment update');

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function resetCompleted(int $collaborationId, int $actorId, string $actorRole): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        if ($normalizedActorRole === 'user') {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET user_completed_at = NULL,
                     user_payment_confirmed_at = NULL,
                     provider_payment_confirmed_at = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET provider_completed_at = NULL,
                     user_payment_confirmed_at = NULL,
                     provider_payment_confirmed_at = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        }

        $this->syncPhaseAndRequestStatus($collaborationId);
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'completed_reset', 'Completion mark reset');
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Collaboration state recalculated after completion reset');

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function resetPayment(int $collaborationId, int $actorId, string $actorRole): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        if ($normalizedActorRole === 'user') {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET user_payment_confirmed_at = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        } else {
            $stmt = $this->pdo->prepare(
                'UPDATE job_collaboration
                 SET provider_payment_confirmed_at = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE collaboration_id = :collaboration_id'
            );
            $stmt->execute([':collaboration_id' => $collaborationId]);
        }

        $this->syncPhaseAndRequestStatus($collaborationId);
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'payment_reset', 'Payment confirmation reset');
        $this->addEvent($collaborationId, $actorId, $normalizedActorRole, 'phase_changed', 'Collaboration state recalculated after payment reset');

        return $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole);
    }

    public function submitRating(int $collaborationId, int $actorId, string $actorRole, int $rating, ?string $comment = null): array
    {
        $normalizedActorRole = $this->normalizeActorRole($actorRole);
        if ($normalizedActorRole !== 'user') {
            throw new RuntimeException('Only users can submit ratings in this flow');
        }

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Rating must be between 1 and 5');
        }

        $collaboration = $this->getByIdForActor($collaborationId, $actorId, $normalizedActorRole, false);
        if (!$collaboration) {
            throw new RuntimeException('Collaboration not found');
        }

        if (empty($collaboration['user_payment_confirmed_at']) || empty($collaboration['provider_payment_confirmed_at'])) {
            throw new RuntimeException('Both payment confirmations are required before rating');
        }

        $insertRatingStmt = $this->pdo->prepare(
            'INSERT INTO job_collaboration_rating
                (collaboration_id, reviewer_id, reviewer_role, target_id, target_role, rating, comment)
             VALUES
                (:collaboration_id, :reviewer_id, :reviewer_role, :target_id, :target_role, :rating, :comment)'
        );

        $insertRatingStmt->execute([
            ':collaboration_id' => $collaborationId,
            ':reviewer_id' => $actorId,
            ':reviewer_role' => 'user',
            ':target_id' => (int) $collaboration['provider_id'],
            ':target_role' => (string) $collaboration['provider_role'],
            ':rating' => $rating,
            ':comment' => $comment,
        ]);

        $updatePhaseStmt = $this->pdo->prepare(
            'UPDATE job_collaboration
             SET user_rated_at = COALESCE(user_rated_at, NOW()),
                 current_phase = :phase,
                 updated_at = CURRENT_TIMESTAMP
             WHERE collaboration_id = :collaboration_id'
        );
        $updatePhaseStmt->execute([
            ':phase' => 'completed',
            ':collaboration_id' => $collaborationId,
        ]);

        if (strtolower((string) $collaboration['provider_role']) === 'repairer') {
            $this->mirrorRatingToLegacyReviewTable($collaboration, $rating, $comment);
        }

        $this->addEvent($collaborationId, $actorId, 'user', 'rating_submitted', $comment, (float) $rating);
        $this->addEvent($collaborationId, $actorId, 'user', 'phase_changed', 'Phase moved to completed');

        return $this->getByIdForActor($collaborationId, $actorId, 'user');
    }

    private function mirrorRatingToLegacyReviewTable(array $collaboration, int $rating, ?string $comment): void
    {
        try {
            $jobStmt = $this->pdo->prepare('SELECT job_id FROM job WHERE job_request_id = :request_id ORDER BY job_id DESC LIMIT 1');
            $jobStmt->execute([':request_id' => (int) $collaboration['request_id']]);
            $jobId = (int) $jobStmt->fetchColumn();
            if ($jobId <= 0) {
                return;
            }

            $insertStmt = $this->pdo->prepare(
                'INSERT INTO review (job_id, service_provider_id, rating, comments, date)
                 VALUES (:job_id, :service_provider_id, :rating, :comments, NOW())'
            );
            $insertStmt->execute([
                ':job_id' => $jobId,
                ':service_provider_id' => (int) $collaboration['provider_id'],
                ':rating' => $rating,
                ':comments' => $comment,
            ]);
        } catch (Throwable $e) {
            error_log('mirrorRatingToLegacyReviewTable failed: ' . $e->getMessage());
        }
    }
}
