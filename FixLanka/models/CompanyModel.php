<?php
require_once __DIR__ . '/../config/database.php';

class CompanyModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Get Company Profile by ID
    public function getProfile($companyId) {
        $sql = "SELECT * FROM Company WHERE company_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update Company Profile
    public function updateProfile($companyId, $data) {
        // Build dynamic query based on data
        $fields = [];
        $values = [];

        // Allowed fields to update
        $allowedFields = [
            'name', 'contact_no', 'address', 'description', 
            'website', 'city', 'province', 'postal_code', 
            'facebook', 'instagram', 'linkedin', 'twitter',
            'alternate_phone', 'whatsapp'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            return false; // Nothing to update
        }

        $sql = "UPDATE Company SET " . implode(', ', $fields) . " WHERE company_id = ?";
        $values[] = $companyId;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    // Change Password
    public function changePassword($companyId, $currentPassword, $newPassword) {
        // 1. Verify current password
        $sql = "SELECT password FROM Company WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $company = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$company) {
            return ['success' => false, 'message' => 'Company account not found'];
        }

        if (!password_verify($currentPassword, $company['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // 2. Hash new password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // 3. Update password
        $updateSql = "UPDATE Company SET password = ? WHERE company_id = ?";
        $updateStmt = $this->pdo->prepare($updateSql);
        
        if ($updateStmt->execute([$newHash, $companyId])) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update password in database'];
        }
    }
    
    // Update Logo
    public function updateLogo($companyId, $logoPath) {
        $sql = "UPDATE Company SET logo = ? WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$logoPath, $companyId]);
    }
    // Get Login/Activity History
    public function getLoginHistory($userId) {
        // Query the ActivityLog table
        // Assuming action_type 'login' or 'failed_login' exists or just showing all activity
        $sql = "SELECT action_type, description, timestamp, ip_address 
                FROM ActivityLog 
                WHERE user_id = ? 
                ORDER BY timestamp DESC 
                LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get Settings (Real)
    public function getSettings($companyId) {
        $sql = "SELECT * FROM CompanySettings WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settings) {
            // Return defaults if no settings exist yet
            return [
                'email_repair_requests' => 1,
                'email_project_updates' => 1,
                'email_payments' => 1,
                'email_team_activity' => 0,
                'email_messages' => 1,
                'push_desktop' => 1,
                'push_mobile' => 0
            ];
        }
        return $settings;
    }

    // Update Settings (Real)
    public function updateSettings($companyId, $data) {
        // Check if settings exist
        $check = $this->getSettings($companyId);
        
        // Map frontend keys to DB columns
        $dbData = [
            'email_repair_requests' => $data['emailRepairRequests'] ? 1 : 0,
            'email_project_updates' => $data['emailProjectUpdates'] ? 1 : 0,
            'email_payments' => $data['emailPayments'] ? 1 : 0,
            'email_team_activity' => $data['emailTeamActivity'] ? 1 : 0,
            'email_messages' => $data['emailMessages'] ? 1 : 0,
            'push_desktop' => $data['pushDesktop'] ? 1 : 0,
            'push_mobile' => $data['pushMobile'] ? 1 : 0,
            'company_id' => $companyId
        ];

        // Insert or Update using ON DUPLICATE KEY UPDATE (or simpler check logic)
        $sql = "INSERT INTO CompanySettings (company_id, email_repair_requests, email_project_updates, email_payments, email_team_activity, email_messages, push_desktop, push_mobile) 
                VALUES (:company_id, :email_repair_requests, :email_project_updates, :email_payments, :email_team_activity, :email_messages, :push_desktop, :push_mobile)
                ON DUPLICATE KEY UPDATE 
                email_repair_requests = VALUES(email_repair_requests),
                email_project_updates = VALUES(email_project_updates),
                email_payments = VALUES(email_payments),
                email_team_activity = VALUES(email_team_activity),
                email_messages = VALUES(email_messages),
                push_desktop = VALUES(push_desktop),
                push_mobile = VALUES(push_mobile)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dbData);
    }

    // Get Billing History (Real)
    public function getBillingHistory($companyId) {
        $sql = "SELECT * FROM BillingHistory WHERE company_id = ? ORDER BY date DESC LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
