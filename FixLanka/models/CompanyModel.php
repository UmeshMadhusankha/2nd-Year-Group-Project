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
}
?>
