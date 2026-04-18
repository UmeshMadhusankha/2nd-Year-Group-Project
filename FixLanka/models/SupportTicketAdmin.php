<?php
class SupportTicketAdmin
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTickets(array $filters = []): array
    {
        $sql = "SELECT ticket_id, ticket_number, user_type, user_id, title, status, priority, category, created_at, updated_at
                FROM support_tickets
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['user_type'])) {
            $sql .= " AND user_type = :user_type";
            $params[':user_type'] = $filters['user_type'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND category = :category";
            $params[':category'] = $filters['category'];
        }

        $sql .= " ORDER BY updated_at DESC, ticket_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getTicketById(int $ticketId): ?array
    {
        $sql = "SELECT * FROM support_tickets WHERE ticket_id = :ticket_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ticket ?: null;
    }

    public function getResponses(int $ticketId): array
    {
        $sql = "SELECT response_id, responder_type, responder_id, message, created_at
                FROM support_responses
                WHERE ticket_id = :ticket_id
                ORDER BY created_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getAttachments(int $ticketId): array
    {
        $sql = "SELECT attachment_id, file_name, file_path, file_type, file_size, uploaded_at
                FROM support_attachments
                WHERE ticket_id = :ticket_id
                ORDER BY uploaded_at ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function addResponse(int $ticketId, string $responderType, int $responderId, string $message): int
    {
        $sql = "INSERT INTO support_responses (ticket_id, responder_type, responder_id, message)
                VALUES (:ticket_id, :responder_type, :responder_id, :message)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':responder_type' => $responderType,
            ':responder_id' => $responderId,
            ':message' => $message
        ]);

        $this->touchTicket($ticketId);
        return (int)$this->pdo->lastInsertId();
    }

    public function updateStatus(int $ticketId, string $status): void
    {
        $sql = "UPDATE support_tickets SET status = :status WHERE ticket_id = :ticket_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => $status,
            ':ticket_id' => $ticketId
        ]);
    }

    private function touchTicket(int $ticketId): void
    {
        $sql = "UPDATE support_tickets SET updated_at = CURRENT_TIMESTAMP WHERE ticket_id = :ticket_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);
    }
}
