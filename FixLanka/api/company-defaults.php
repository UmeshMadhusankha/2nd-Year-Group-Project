<?php
/**
 * Get Company/Vendor Default Settings
 * 
 * Returns default quotation settings for a company including:
 * - Default payment terms
 * - Default warranty period
 * - Standard terms and conditions template
 * - Location for transport calculation
 * - Standard lead time
 * 
 * @param user_id - Company user ID
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

// Authentication check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    // Use logged-in company ID from session
    $userId = $_SESSION['user_id'];

    if (!$userId) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        exit;
    }

    // Get current company profile plus the latest quotation defaults.
        $profileQuery = "SELECT 
                                                c.company_id AS user_id,
                                                c.name AS company_name,
                                                c.address,
                                                c.contact_no,
                                                c.email,
                                                c.districts AS district
                                            FROM company c
                                            WHERE c.company_id = :user_id AND COALESCE(c.is_deleted, 0) = 0
                                            LIMIT 1";

        $stmt = $pdo->prepare($profileQuery);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'user_id' => $userId,
                'company_name' => null,
                'address' => null,
                'contact_no' => null,
                'email' => null,
                'district' => null,
        ];

        $defaultsQuery = "SELECT
                                                payment_terms AS default_payment_terms,
                                                warranty_period AS default_warranty,
                                                additional_terms AS standard_terms_template
                                            FROM companyquotation
                                            WHERE company_id = :user_id
                                            ORDER BY updated_at DESC
                                            LIMIT 1";

        $stmt = $pdo->prepare($defaultsQuery);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $defaults = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $result = array_merge($result, $defaults);
        $result['quotation_validity_days'] = $result['quotation_validity_days'] ?? 30;
        $result['standard_lead_time_days'] = $result['standard_lead_time_days'] ?? 3;

    if ($result) {
        // Set defaults if not in database
        if (!$result['default_payment_terms']) {
            $result['default_payment_terms'] = '50_50'; // 50% upfront, 50% on completion
        }
        if (!$result['default_warranty']) {
            $result['default_warranty'] = '6_months';
        }
        if (!$result['quotation_validity_days']) {
            $result['quotation_validity_days'] = 30;
        }
        if (!$result['standard_lead_time_days']) {
            $result['standard_lead_time_days'] = 3;
        }

        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        // Return defaults even if no company profile exists
        echo json_encode([
            'success' => true,
            'data' => [
                'user_id' => $userId,
                'company_name' => null,
                'district' => null,
                'default_payment_terms' => '50_50',
                'default_warranty' => '6_months',
                'standard_terms_template' => null,
                'quotation_validity_days' => 30,
                'standard_lead_time_days' => 3
            ]
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
