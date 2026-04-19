<?php
/**
 * User CRUD reference model (learning sandbox)
 *
 * Shows:
 * - Simple CRUD with prepared statements
 * - New field usage (priority_level)
 * - Join-based business logic query
 */

class CrudReferenceModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * OPTIONAL helper:
     * Create practice table if it does not exist.
     * Keep this manual in viva unless asked to auto-create tables.
     */
    public function createPracticeTableIfMissing(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS user_practice_note (
                note_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(120) NOT NULL,
                body TEXT NULL,
                priority_level ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                INDEX idx_user_practice_note_user (user_id)
            )
        ";
        $this->pdo->exec($sql);
    }

    /**
     * CREATE
     */
    public function createNote(int $userId, string $title, string $body, string $priorityLevel): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_practice_note (user_id, title, body, priority_level) VALUES (:user_id, :title, :body, :priority_level)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $title,
            ':body' => $body,
            ':priority_level' => $priorityLevel,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * READ list
     */
    public function listNotesByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT note_id, user_id, title, body, priority_level, created_at, updated_at
             FROM user_practice_note
             WHERE user_id = :user_id
             ORDER BY note_id DESC'
        );
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * UPDATE
     * Returns true when at least one row changed.
     */
    public function updateNote(int $userId, int $noteId, string $title, string $body, string $priorityLevel): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE user_practice_note
             SET title = :title,
                 body = :body,
                 priority_level = :priority_level,
                 updated_at = NOW()
             WHERE note_id = :note_id AND user_id = :user_id'
        );
        $stmt->execute([
            ':title' => $title,
            ':body' => $body,
            ':priority_level' => $priorityLevel,
            ':note_id' => $noteId,
            ':user_id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * DELETE
     */
    public function deleteNote(int $userId, int $noteId): bool
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM user_practice_note WHERE note_id = :note_id AND user_id = :user_id'
        );
        $stmt->execute([
            ':note_id' => $noteId,
            ':user_id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Business logic example using joins.
     *
     * Joins:
     * - jobrequest jr (base)
     * - accepted repairer quote rq
     * - accepted company quote cq
     * - review rv
     *
     * Output:
     * - one row per job with chosen quote amount and review summary
     */
    public function getJobInsights(int $userId, ?string $status = null): array
    {
        $sql = "
            SELECT
                jr.request_id,
                jr.title AS job_title,
                jr.status AS job_status,
                jr.priority_level,
                jr.created_at,
                COALESCE(rq.amount, cq.amount, 0) AS agreed_amount,
                CASE
                    WHEN rq.quote_id IS NOT NULL THEN 'repairer'
                    WHEN cq.quotation_id IS NOT NULL THEN 'company'
                    ELSE 'none'
                END AS selected_provider_type,
                COALESCE(rv.rating, 0) AS user_rating,
                rv.comment AS user_review
            FROM jobrequest jr
            LEFT JOIN repairerquote rq
                ON rq.request_id = jr.request_id
                AND rq.status IN ('accepted','completed','successful')
            LEFT JOIN companyquotation cq
                ON cq.request_id = jr.request_id
                AND cq.status IN ('accepted','completed','successful')
            LEFT JOIN review rv
                ON rv.request_id = jr.request_id
                AND rv.user_id = jr.user_id
            WHERE jr.user_id = :user_id
        ";

        $params = [':user_id' => $userId];

        if ($status !== null && $status !== '') {
            $sql .= ' AND jr.status = :status ';
            $params[':status'] = $status;
        }

        $sql .= ' ORDER BY jr.request_id DESC LIMIT 100';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
