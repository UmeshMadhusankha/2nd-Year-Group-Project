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

require_once '../../config/database.php';
require_once '../../config/session.php';

header('Content-Type: application/json');

try {
    // Removed buggy $stmt = $pdo->prepare($query); since $query is not defined yet
    // Get user_id from request
    $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

    if (!$userId) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        exit;
    }

    // Get company details and defaults
    $query = "SELECT 
                u.id as user_id,
                u_loc.district,
                c.company_name,
                c.payment_terms as default_payment_terms,
                c.warranty_period as default_warranty,
                c.standard_terms as standard_terms_template,
                c.quotation_validity_days,
                c.standard_lead_time_days
              FROM users u
              LEFT JOIN location u_loc ON u.location_id = u_loc.location_id
              LEFT JOIN companies c ON u.id = c.user_id
              WHERE u.id = :user_id AND u.user_type = 'company'";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

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
                'district' => null,
                'company_name' => null,
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
