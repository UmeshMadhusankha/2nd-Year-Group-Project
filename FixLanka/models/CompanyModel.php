<?php
require_once __DIR__ . '/../config/database.php';

class CompanyModel {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!($pdo instanceof PDO)) {
            $pdo = getDatabaseConnection();
        }
        $this->pdo = $pdo;
    }

    // Get Company Profile by ID
    public function getProfile($companyId) {
        $sql = "SELECT c.*, l.address, l.district 
                FROM Company c 
                LEFT JOIN location l ON c.location_id = l.location_id 
                WHERE c.company_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fetch service areas (districts)
        if ($profile) {
            $areaSql = "SELECT district FROM service_area WHERE owner_id = ? AND owner_type = 'company'";
            $areaStmt = $this->pdo->prepare($areaSql);
            $areaStmt->execute([$companyId]);
            $districts = $areaStmt->fetchAll(PDO::FETCH_COLUMN);
            $profile['districts'] = implode(',', $districts);
        }
        
        return $profile;
    }

    // Update Company Profile
    public function updateProfile($companyId, $data) {
        // Build dynamic query based on data
        $fields = [];
        $values = [];

        // 1. Handle location updates separately
        if (isset($data['address']) || isset($data['district'])) {
            $locFields = [];
            $locValues = [];
            if (isset($data['address'])) {
                $locFields[] = "address = ?";
                $locValues[] = $data['address'];
                unset($data['address']);
            }
            if (isset($data['district'])) {
                $locFields[] = "district = ?";
                $locValues[] = $data['district'];
                unset($data['district']);
            }
            
            if (!empty($locFields)) {
                // Get company location_id
                $stmt = $this->pdo->prepare("SELECT location_id FROM Company WHERE company_id = ?");
                $stmt->execute([$companyId]);
                $locId = $stmt->fetchColumn();
                
                if ($locId) {
                    $locSql = "UPDATE location SET " . implode(', ', $locFields) . " WHERE location_id = ?";
                    $locValues[] = $locId;
                    $locUpdateStmt = $this->pdo->prepare($locSql);
                    $locUpdateStmt->execute($locValues);
                }
            }
        }

        // Allowed fields to update (Company table only)
        $allowedFields = [
            'name', 'contact_no', 'description', 
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
            return true; // Locations might have been updated, so return true
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
        
        // Add quiet hours if provided
        if (isset($data['quietHoursStart'])) {
            $dbData['quiet_hours_start'] = $data['quietHoursStart'];
        }
        if (isset($data['quietHoursEnd'])) {
            $dbData['quiet_hours_end'] = $data['quietHoursEnd'];
        }

        // Insert or Update using ON DUPLICATE KEY UPDATE
        $sql = "INSERT INTO CompanySettings (
            company_id, email_repair_requests, email_project_updates, 
            email_payments, email_team_activity, email_messages, 
            push_desktop, push_mobile, quiet_hours_start, quiet_hours_end
        ) VALUES (
            :company_id, :email_repair_requests, :email_project_updates,
            :email_payments, :email_team_activity, :email_messages,
            :push_desktop, :push_mobile, :quiet_hours_start, :quiet_hours_end
        )
        ON DUPLICATE KEY UPDATE 
            email_repair_requests = VALUES(email_repair_requests),
            email_project_updates = VALUES(email_project_updates),
            email_payments = VALUES(email_payments),
            email_team_activity = VALUES(email_team_activity),
            email_messages = VALUES(email_messages),
            push_desktop = VALUES(push_desktop),
            push_mobile = VALUES(push_mobile),
            quiet_hours_start = VALUES(quiet_hours_start),
            quiet_hours_end = VALUES(quiet_hours_end)";
        
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

    // ==================== SESSION MANAGEMENT ====================
    
    /**
     * Get all active sessions for a user
     */
    public function getActiveSessions($userId) {
        $sql = "SELECT session_id, device_type, browser, os, ip_address, location, 
                       last_activity, is_current, created_at, user_agent
                FROM user_sessions 
                WHERE user_id = ? AND user_role = 'company'
                ORDER BY last_activity DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Revoke a specific session (force logout)
     */
    public function revokeSession($userId, $sessionId) {
        // Can't revoke current session
        $currentSessionId = session_id();
        if ($sessionId === $currentSessionId) {
            return false;
        }
        
        $sql = "DELETE FROM user_sessions 
                WHERE session_id = ? AND user_id = ? AND user_role = 'company'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$sessionId, $userId]);
    }
    
    /**
     * Revoke all sessions except current one
     */
    public function revokeAllOtherSessions($userId) {
        $currentSessionId = session_id();
        $sql = "DELETE FROM user_sessions 
                WHERE user_id = ? AND user_role = 'company' AND session_id != ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$userId, $currentSessionId]);
    }
    
    /**
     * Get session count for a user
     */
    public function getSessionCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM user_sessions 
                WHERE user_id = ? AND user_role = 'company'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    // ==================== BILLING & SUBSCRIPTION ====================
    
    /**
     * Get subscription details for a company
     */
    public function getSubscription($companyId) {
        $sql = "SELECT * FROM company_subscriptions 
                WHERE company_id = ? 
                ORDER BY created_at DESC 
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If no subscription exists, create a free trial
        if (!$subscription) {
            $this->createDefaultSubscription($companyId);
            $stmt->execute([$companyId]);
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return $subscription;
    }
    
    /**
     * Create default free subscription for new company
     */
    private function createDefaultSubscription($companyId) {
        $sql = "INSERT INTO company_subscriptions 
                (company_id, plan_name, plan_price, billing_period, status, start_date, trial_ends_at, next_billing_date)
                VALUES (?, 'free', 0.00, 'monthly', 'trial', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY))";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$companyId]);
    }
    
    /**
     * Update subscription plan
     */
    public function updateSubscriptionPlan($companyId, $planName, $billingPeriod) {
        $prices = [
            'free' => 0,
            'basic' => 2500,
            'professional' => 5000,
            'enterprise' => 10000
        ];
        
        $price = $prices[$planName] ?? 0;
        
        // Update existing subscription
        $sql = "UPDATE company_subscriptions 
                SET plan_name = ?, 
                    plan_price = ?, 
                    billing_period = ?, 
                    status = 'active',
                    updated_at = CURRENT_TIMESTAMP
                WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$planName, $price, $billingPeriod, $companyId]);
    }
    
    /**
     * Cancel subscription
     */
    public function cancelSubscription($companyId) {
        $sql = "UPDATE company_subscriptions 
                SET status = 'cancelled',
                    auto_renew = 0,
                    updated_at = CURRENT_TIMESTAMP
                WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$companyId]);
    }
    
    /**
     * Get all payment methods for a company
     */
    public function getPaymentMethods($companyId) {
        $sql = "SELECT * FROM payment_methods 
                WHERE company_id = ? AND is_active = 1
                ORDER BY is_primary DESC, created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Add new payment method
     */
    public function addPaymentMethod($companyId, $data) {
        // If this is the first payment method, make it primary
        $isFirst = $this->getPaymentMethodCount($companyId) === 0;
        
        $sql = "INSERT INTO payment_methods 
                (company_id, card_type, last_four_digits, card_holder_name, expiry_month, expiry_year, billing_address, is_primary, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $companyId,
            $data['card_type'],
            $data['last_four_digits'],
            $data['card_holder_name'],
            $data['expiry_month'],
            $data['expiry_year'],
            $data['billing_address'] ?? null,
            $isFirst ? 1 : 0
        ]);
    }
    
    /**
     * Remove payment method
     */
    public function removePaymentMethod($paymentMethodId, $companyId) {
        // Check if it's the primary method
        $sql = "SELECT is_primary FROM payment_methods WHERE payment_method_id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$paymentMethodId, $companyId]);
        $method = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$method) {
            return false;
        }
        
        // Soft delete
        $sql = "UPDATE payment_methods SET is_active = 0 WHERE payment_method_id = ? AND company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$paymentMethodId, $companyId]);
        
        // If primary was deleted, set another as primary
        if ($result && $method['is_primary']) {
            $this->assignNewPrimary($companyId);
        }
        
        return $result;
    }
    
    /**
     * Set payment method as primary
     */
    public function setPrimaryPaymentMethod($paymentMethodId, $companyId) {
        // Remove primary from all methods
        $sql = "UPDATE payment_methods SET is_primary = 0 WHERE company_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        
        // Set new primary
        $sql = "UPDATE payment_methods SET is_primary = 1 
                WHERE payment_method_id = ? AND company_id = ? AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$paymentMethodId, $companyId]);
    }
    
    /**
     * Get payment method count
     */
    private function getPaymentMethodCount($companyId) {
        $sql = "SELECT COUNT(*) as count FROM payment_methods WHERE company_id = ? AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$companyId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    /**
     * Assign new primary if current primary is deleted
     */
    private function assignNewPrimary($companyId) {
        $sql = "UPDATE payment_methods 
                SET is_primary = 1 
                WHERE company_id = ? AND is_active = 1
                ORDER BY created_at ASC
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$companyId]);
    }
}

// Provider listing model used by ProviderController (landing page)
class Company {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get featured companies (top-rated)
     */
    public function getFeatured($limit = 10, $offset = 0) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    c.company_id,
                    c.name,
                    c.business_type,
                    c.address,
                    c.email,
                    c.website,
                    c.contact_no,
                    c.districts,
                    c.description,
                    c.rating AS ratings,
                    c.date_of_joined,
                    'company' AS provider_type
                FROM company c
                ORDER BY c.rating DESC, c.company_id DESC
                LIMIT ? OFFSET ?
            ");

            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting featured companies: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all companies with optional filters
     */
    public function getAll($filters = [], $limit = 20, $offset = 0) {
        try {
            $sql = "
                SELECT
                    c.company_id,
                    c.name,
                    c.business_type,
                    c.address,
                    c.email,
                    c.website,
                    c.contact_no,
                    c.districts,
                    c.description,
                    c.rating AS ratings,
                    c.date_of_joined,
                    'company' AS provider_type
                FROM company c
                WHERE 1=1
            ";

            $params = [];

            // Apply filters
            if (!empty($filters['min_rating'])) {
                $sql .= " AND c.rating >= ?";
                $params[] = $filters['min_rating'];
            }

            if (!empty($filters['service_area'])) {
                $sql .= " AND c.districts LIKE ?";
                $params[] = '%' . $filters['service_area'] . '%';
            }

            // Category filtering for companies is based on business_type text
            if (!empty($filters['category_id'])) {
                $categoryName = null;
                try {
                    $catStmt = $this->pdo->prepare('SELECT name FROM category WHERE category_id = ?');
                    $catStmt->execute([$filters['category_id']]);
                    $categoryName = $catStmt->fetchColumn();
                } catch (PDOException $e) {
                    // If category table is unavailable or case differs, skip category filter
                    $categoryName = null;
                }

                if ($categoryName) {
                    $sql .= " AND c.business_type LIKE ?";
                    $params[] = '%' . $categoryName . '%';
                }
            }

            $sql .= " ORDER BY c.rating DESC, c.company_id DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting all companies: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get single company by ID
     */
    public function getById($companyId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT
                    c.company_id,
                    c.name,
                    c.business_type,
                    c.registration_no,
                    c.tax_id,
                    c.address,
                    c.email,
                    c.website,
                    c.contact_no,
                    c.districts,
                    c.description,
                    c.rating AS ratings,
                    c.date_of_joined,
                    'company' AS provider_type
                FROM company c
                WHERE c.company_id = ?
            ");

            $stmt->execute([$companyId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting company: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of companies
     */
    public function getCount($filters = []) {
        try {
            $sql = 'SELECT COUNT(*) as total FROM company c WHERE 1=1';
            $params = [];

            if (!empty($filters['min_rating'])) {
                $sql .= ' AND c.rating >= ?';
                $params[] = $filters['min_rating'];
            }

            if (!empty($filters['service_area'])) {
                $sql .= ' AND c.districts LIKE ?';
                $params[] = '%' . $filters['service_area'] . '%';
            }

            if (!empty($filters['category_id'])) {
                $categoryName = null;
                try {
                    $catStmt = $this->pdo->prepare('SELECT name FROM category WHERE category_id = ?');
                    $catStmt->execute([$filters['category_id']]);
                    $categoryName = $catStmt->fetchColumn();
                } catch (PDOException $e) {
                    $categoryName = null;
                }

                if ($categoryName) {
                    $sql .= ' AND c.business_type LIKE ?';
                    $params[] = '%' . $categoryName . '%';
                }
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log('Error getting company count: ' . $e->getMessage());
            return 0;
        }
    }
}
?>
