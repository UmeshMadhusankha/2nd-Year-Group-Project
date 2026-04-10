<?php
require_once '../config/session.php';
require_once '../models/CompanyModel.php';
require_once '../config/database.php';
require_once 'helpers.php';

// Set JSON header
header('Content-Type: application/json');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Initialize Model
$companyModel = new CompanyModel();
$userId = (int)($_SESSION['user_id'] ?? 0);
$companyId = $_SESSION['company_id'] ?? null;

// Fallback: resolve company_id from user_id
if (!$companyId && $userId > 0) {
    $companyData = getCompanyByUserId($pdo, $userId);
    if ($companyData && isset($companyData['company_id'])) {
        $companyId = (int)$companyData['company_id'];
    }
}

// Last fallback for legacy sessions
if (!$companyId) {
    $companyId = $userId;
}

// Handle GET Request (Fetch All Settings Data)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $settings = $companyModel->getSettings($companyId);
        $history = $companyModel->getLoginHistory($companyId);
        $billing = $companyModel->getBillingHistory($companyId);

        echo json_encode([
            'success' => true, 
            'data' => [
                'settings' => $settings,
                'history' => $history,
                'billing' => $billing,
                'current_session' => [
                    'device' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'location' => 'Unknown' // GeoIP would go here
                ]
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle POST Request (Update Settings)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $action = $input['action'] ?? '';

    if ($action === 'update_notifications') {
        $success = $companyModel->updateSettings($companyId, $input['settings']);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save settings']);
        }
    } 
    elseif ($action === 'get_sessions') {
        try {
            // Get all active sessions for the company
            $sessions = $companyModel->getActiveSessions($userId);
            $sessionCount = $companyModel->getSessionCount($userId);
            echo json_encode([
                'success' => true,
                'data' => $sessions,
                'count' => $sessionCount,
                'current_session_id' => session_id()
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to load sessions']);
        }
    }
    elseif ($action === 'revoke_session') {
        // Revoke a specific session
        $sessionId = $input['session_id'] ?? '';
        if (empty($sessionId)) {
            echo json_encode(['success' => false, 'message' => 'Session ID required']);
            exit;
        }

        try {
            $success = $companyModel->revokeSession($userId, $sessionId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Session revoked successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to revoke session or session is current']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to revoke session']);
        }
    }
    elseif ($action === 'revoke_all_sessions') {
        // Revoke all sessions except current
        try {
            $success = $companyModel->revokeAllOtherSessions($userId);
            if ($success) {
                $remainingCount = $companyModel->getSessionCount($userId);
                echo json_encode([
                    'success' => true,
                    'message' => 'All other sessions revoked successfully',
                    'remaining_sessions' => $remainingCount
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to revoke sessions']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to revoke sessions']);
        }
    }
    elseif ($action === 'get_billing_data') {
        // Get subscription, payment methods, and billing history
        $subscription = $companyModel->getSubscription($companyId);
        $paymentMethods = $companyModel->getPaymentMethods($companyId);
        $billingHistory = $companyModel->getBillingHistory($companyId);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'subscription' => $subscription,
                'payment_methods' => $paymentMethods,
                'billing_history' => $billingHistory
            ]
        ]);
    }
    elseif ($action === 'add_payment_method') {
        // Add new payment method
        $data = $input['payment_method'] ?? [];
        
        if (empty($data['card_type']) || empty($data['last_four_digits']) || 
            empty($data['card_holder_name']) || empty($data['expiry_month']) || empty($data['expiry_year'])) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }
        
        $success = $companyModel->addPaymentMethod($companyId, $data);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Payment method added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add payment method']);
        }
    }
    elseif ($action === 'remove_payment_method') {
        // Remove payment method
        $paymentMethodId = $input['payment_method_id'] ?? 0;
        
        if (empty($paymentMethodId)) {
            echo json_encode(['success' => false, 'message' => 'Payment method ID required']);
            exit;
        }
        
        $success = $companyModel->removePaymentMethod($paymentMethodId, $companyId);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Payment method removed successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove payment method']);
        }
    }
    elseif ($action === 'set_primary_payment') {
        // Set primary payment method
        $paymentMethodId = $input['payment_method_id'] ?? 0;
        
        if (empty($paymentMethodId)) {
            echo json_encode(['success' => false, 'message' => 'Payment method ID required']);
            exit;
        }
        
        $success = $companyModel->setPrimaryPaymentMethod($paymentMethodId, $companyId);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Primary payment method updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update primary payment method']);
        }
    }
    elseif ($action === 'change_plan') {
        // Update subscription plan
        $planName = $input['plan_name'] ?? '';
        $billingPeriod = $input['billing_period'] ?? 'monthly';
        
        if (empty($planName) || !in_array($planName, ['free', 'basic', 'professional', 'enterprise'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid plan name']);
            exit;
        }
        
        $success = $companyModel->updateSubscriptionPlan($companyId, $planName, $billingPeriod);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Subscription plan updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update subscription plan']);
        }
    }
    elseif ($action === 'cancel_subscription') {
        // Cancel subscription
        $success = $companyModel->cancelSubscription($companyId);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Subscription cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to cancel subscription']);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit;
}
?>
