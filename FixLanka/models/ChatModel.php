<?php
/**
 * ChatModel - Phase 6 Chat System
 * Handles all database operations for the contract chat feature.
 */
class ChatModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get messages for a contract, optionally since a given message ID.
     * @param int $contractId
     * @param int $sinceId  Only return messages with chat_id > this value (for polling)
     * @param int $limit    Max messages to return
     * @return array
     */
    public function getMessages($contractId, $sinceId = 0, $limit = 50) {
        $sqlWithMessageType = "SELECT 
                    cc.chat_id,
                    cc.contract_id,
                    cc.sender_id,
                    cc.sender_type,
                    cc.message_type,
                    cc.message,
                    cc.created_at,
                    cc.is_read,
                    cc.read_at,
                    cc.attachment_url,
                    CASE 
                        WHEN cc.sender_type = 'company' THEN COALESCE(co.name, 'Company')
                        ELSE CONCAT(COALESCE(u.f_name, ''), ' ', COALESCE(u.l_name, ''))
                    END AS sender_name
                FROM contract_chats cc
                LEFT JOIN contract ct ON cc.contract_id = ct.contract_id
                LEFT JOIN company co ON ct.company_id = co.company_id
                LEFT JOIN user u ON cc.sender_type = 'customer' AND cc.sender_id = u.user_id
                WHERE cc.contract_id = :contract_id
                  AND cc.chat_id > :since_id
                ORDER BY cc.created_at ASC
                LIMIT :lim";

        $sqlFallback = "SELECT 
                    cc.chat_id,
                    cc.contract_id,
                    cc.sender_id,
                    cc.sender_type,
                    cc.attachment_type AS message_type,
                    cc.message,
                    cc.created_at,
                    cc.is_read,
                    cc.read_at,
                    cc.attachment_url,
                    CASE 
                        WHEN cc.sender_type = 'company' THEN COALESCE(co.name, 'Company')
                        ELSE CONCAT(COALESCE(u.f_name, ''), ' ', COALESCE(u.l_name, ''))
                    END AS sender_name
                FROM contract_chats cc
                LEFT JOIN contract ct ON cc.contract_id = ct.contract_id
                LEFT JOIN company co ON ct.company_id = co.company_id
                LEFT JOIN user u ON cc.sender_type = 'customer' AND cc.sender_id = u.user_id
                WHERE cc.contract_id = :contract_id
                  AND cc.chat_id > :since_id
                ORDER BY cc.created_at ASC
                LIMIT :lim";

        try {
            $stmt = $this->conn->prepare($sqlWithMessageType);
            $stmt->bindValue(':contract_id', (int)$contractId, PDO::PARAM_INT);
            $stmt->bindValue(':since_id', (int)$sinceId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Backward compatible schema (no message_type column)
            $stmt = $this->conn->prepare($sqlFallback);
            $stmt->bindValue(':contract_id', (int)$contractId, PDO::PARAM_INT);
            $stmt->bindValue(':since_id', (int)$sinceId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Send a text message.
     * @param int    $contractId
     * @param int    $senderId
     * @param string $senderType  'customer' or 'company'
     * @param string $text
     * @return int|false  The new chat_id or false on failure
     */
    public function sendMessage($contractId, $senderId, $senderType, $text) {
        try {
            $sql = "INSERT INTO contract_chats 
                        (contract_id, sender_id, sender_type, message_type, message, created_at)
                    VALUES 
                        (:contract_id, :sender_id, :sender_type, 'text', :message, NOW())";

            $stmt = $this->conn->prepare($sql);
            $ok = $stmt->execute([
                ':contract_id' => (int)$contractId,
                ':sender_id'   => (int)$senderId,
                ':sender_type' => $senderType,
                ':message'     => $text
            ]);

            return $ok ? (int)$this->conn->lastInsertId() : false;
        } catch (Exception $e) {
            // Backward compatible schema (no message_type column)
            $sql = "INSERT INTO contract_chats 
                        (contract_id, sender_id, sender_type, message, created_at)
                    VALUES 
                        (:contract_id, :sender_id, :sender_type, :message, NOW())";

            $stmt = $this->conn->prepare($sql);
            $ok = $stmt->execute([
                ':contract_id' => (int)$contractId,
                ':sender_id'   => (int)$senderId,
                ':sender_type' => $senderType,
                ':message'     => $text
            ]);

            return $ok ? (int)$this->conn->lastInsertId() : false;
        }
    }

    /**
     * Mark all messages in a contract as read for a given user.
     * Only marks messages NOT sent by this user.
     * @param int    $contractId
     * @param int    $userId
     * @param string $userType  'customer' or 'company'
     */
    public function markRead($contractId, $userId, $userType) {
        // Mark messages from the OTHER party as read
        $otherType = ($userType === 'company') ? 'customer' : 'company';

        $sql = "UPDATE contract_chats 
                SET is_read = 1, read_at = NOW() 
                WHERE contract_id = :contract_id 
                  AND sender_type = :other_type
                  AND is_read = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':contract_id' => (int)$contractId,
            ':other_type'  => $otherType
        ]);
    }

    /**
     * Get the number of unread messages for a user in a contract.
     * @param int    $contractId
     * @param string $userType  'customer' or 'company'
     * @return int
     */
    public function getUnreadCount($contractId, $userType) {
        $otherType = ($userType === 'company') ? 'customer' : 'company';

        $sql = "SELECT COUNT(*) AS cnt 
                FROM contract_chats 
                WHERE contract_id = :contract_id 
                  AND sender_type = :other_type
                  AND is_read = 0";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':contract_id' => (int)$contractId,
            ':other_type'  => $otherType
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Check if chat is active for a contract.
     * @param int $contractId
     * @return bool
     */
    public function isChatActive($contractId) {
        $sql = "SELECT chat_active FROM contract WHERE contract_id = :cid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cid' => (int)$contractId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return !empty($row['chat_active']);
    }

    /**
     * Verify that a user belongs to a contract (either as customer or company).
     * Returns the user's role in the contract, or false if not a participant.
     * @param int $contractId
     * @param int $userId
     * @return string|false  'customer' or 'company' or false
     */
    public function getUserRole($contractId, $userId) {
        // Check if user is the company that owns this contract
        $sql = "SELECT 
                    c.contract_id,
                    c.company_id,
                    c.customer_id
                FROM contract c
                WHERE c.contract_id = :cid";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':cid' => (int)$contractId]);
        $contract = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$contract) return false;

        // Check company_id first, then user_id (company_id may be null in older contracts)
        if (!empty($contract['company_id']) && (int)$contract['company_id'] === (int)$userId) {
            return 'company';
        }

        // Check if user_id matches (customer)
        if (!empty($contract['customer_id']) && (int)$contract['customer_id'] === (int)$userId) {
            return 'customer';
        }

        // Fallback: check via project -> jobrequest -> user
        $sql2 = "SELECT jr.user_id AS customer_id
                 FROM contract c
                 JOIN project p ON c.project_id = p.project_id
                 JOIN jobrequest jr ON p.request_id = jr.request_id
                 WHERE c.contract_id = :cid";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute([':cid' => (int)$contractId]);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($row && (int)$row['customer_id'] === (int)$userId) {
            return 'customer';
        }

        // Also check if user has company role in session and owns contract via session
        // This is a loose check for companies whose company_id might differ from user_id
        return false;
    }
}
