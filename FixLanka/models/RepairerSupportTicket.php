<?php
class RepairerSupportTicket
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createTicket(int $repairerId, array $data): int
    {
        $ticketNumber = $this->generateTicketNumber();

        $sql = "INSERT INTO support_tickets 
                (ticket_number, user_type, user_id, title, category, priority, status, description, urgency, related_project_id)
                VALUES
                (:ticket_number, 'repairer', :user_id, :title, :category, :priority, :status, :description, :urgency, :related_project_id)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_number' => $ticketNumber,
            ':user_id' => $repairerId,
            ':title' => $data['title'],
            ':category' => $data['category'],
            ':priority' => $data['priority'],
            ':status' => 'open',
            ':description' => $data['description'],
            ':urgency' => $data['urgency'],
            ':related_project_id' => $data['related_project_id']
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function getTicketsForRepairer(int $repairerId): array
    {
        $sql = "SELECT ticket_id, ticket_number, title, status, created_at, updated_at
                FROM support_tickets
                WHERE user_type = 'repairer' AND user_id = :user_id
                ORDER BY updated_at DESC, ticket_id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $repairerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getTicketById(int $repairerId, int $ticketId): ?array
    {
        $sql = "SELECT * FROM support_tickets
                WHERE ticket_id = :ticket_id AND user_type = 'repairer' AND user_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':user_id' => $repairerId
        ]);

        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ticket ?: null;
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

        return (int)$this->pdo->lastInsertId();
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

    public function addAttachment(int $ticketId, array $fileInfo): int
    {
        $sql = "INSERT INTO support_attachments (ticket_id, file_name, file_path, file_type, file_size)
                VALUES (:ticket_id, :file_name, :file_path, :file_type, :file_size)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ticket_id' => $ticketId,
            ':file_name' => $fileInfo['file_name'],
            ':file_path' => $fileInfo['file_path'],
            ':file_type' => $fileInfo['file_type'],
            ':file_size' => $fileInfo['file_size']
        ]);

        return (int)$this->pdo->lastInsertId();
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

    private function generateTicketNumber(): string
    {
        $prefix = 'R-' . date('Ymd');
        $attempts = 0;

        do {
            $attempts++;
            $suffix = str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            $ticketNumber = $prefix . '-' . $suffix;

            $stmt = $this->pdo->prepare("SELECT 1 FROM support_tickets WHERE ticket_number = :ticket_number LIMIT 1");
            $stmt->execute([':ticket_number' => $ticketNumber]);
            $exists = (bool)$stmt->fetchColumn();
        } while ($exists && $attempts < 5);

        return $ticketNumber;
    }
}
