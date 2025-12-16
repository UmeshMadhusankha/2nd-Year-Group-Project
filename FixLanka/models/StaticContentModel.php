<?php
/**
 * StaticContentModel.php
 * Model for managing static website content
 */

class StaticContentModel {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all static content
     */
    public function getAllContent() {
        $sql = "SELECT * FROM StaticContent ORDER BY content_id ASC";
        $result = $this->db->query($sql);
        
        if (!$result) {
            error_log("Database error: " . $this->db->error);
            return [];
        }
        
        $contents = [];
        while ($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }
        
        return $contents;
    }
    
    /**
     * Get statistics for dashboard
     */
    public function getStatistics() {
        $stats = [
            'total' => 0,
            'published' => 0,
            'drafts' => 0,
            'last_update' => null
        ];
        
        // Total count
        $result = $this->db->query("SELECT COUNT(*) as total FROM StaticContent");
        if ($result) {
            $stats['total'] = $result->fetch_assoc()['total'];
        }
        
        // Published count
        $result = $this->db->query("SELECT COUNT(*) as total FROM StaticContent WHERE status = 'Published'");
        if ($result) {
            $stats['published'] = $result->fetch_assoc()['total'];
        }
        
        // Draft count
        $result = $this->db->query("SELECT COUNT(*) as total FROM StaticContent WHERE status = 'Draft'");
        if ($result) {
            $stats['drafts'] = $result->fetch_assoc()['total'];
        }
        
        // Last update
        $result = $this->db->query("SELECT MAX(last_update) as last_update FROM StaticContent");
        if ($result) {
            $row = $result->fetch_assoc();
            $stats['last_update'] = $row['last_update'];
        }
        
        return $stats;
    }
    
    /**
     * Get content by ID
     */
    public function getContentById($id) {
        $stmt = $this->db->prepare("SELECT * FROM StaticContent WHERE content_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Update content
     */
    public function updateContent($id, $title, $description, $body, $status) {
        $stmt = $this->db->prepare("
            UPDATE StaticContent 
            SET title = ?, description = ?, body = ?, status = ?, last_update = CURRENT_TIMESTAMP 
            WHERE content_id = ?
        ");
        
        $stmt->bind_param("ssssi", $title, $description, $body, $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        
        // Log activity
        if ($success) {
            $this->logActivity('content_updated', $id, $title);
        }
        
        return $success;
    }
    
    /**
     * Publish content
     */
    public function publishContent($id) {
        $stmt = $this->db->prepare("UPDATE StaticContent SET status = 'Published' WHERE content_id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Unpublish content (set to draft)
     */
    public function unpublishContent($id) {
        $stmt = $this->db->prepare("UPDATE StaticContent SET status = 'Draft' WHERE content_id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Set content as draft
     */
    public function setDraft($id) {
        return $this->unpublishContent($id);
    }
    
    /**
     * Log moderator activity
     */
    private function logActivity($action, $target_id, $target_title) {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=fix_lanka;charset=utf8mb4",
                "root",
                "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            $stmt = $pdo->prepare("
                INSERT INTO moderator_activity (moderator_id, activity_type, target_id, target_title, description, created_at)
                VALUES (1, :activity_type, :target_id, :target_title, :description, NOW())
            ");
            
            $description = "Static content '{$target_title}' was updated";
            
            $stmt->execute([
                'activity_type' => 'content_updated',
                'target_id' => $target_id,
                'target_title' => $target_title,
                'description' => $description
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}