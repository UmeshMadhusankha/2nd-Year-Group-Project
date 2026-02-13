<?php
/**
 * Payment Configuration
 * Phase 4 - Feature 2: Payment Gateway Integration
 * 
 * Contains API keys and settings for payment gateways
 */

// Environment: 'test' or 'live'
define('PAYMENT_ENVIRONMENT', 'test');

// ========================================
// STRIPE CONFIGURATION
// ========================================

// Stripe Test Keys (Replace with your keys)
define('STRIPE_TEST_PUBLIC_KEY', 'pk_test_51234567890abcdef');
define('STRIPE_TEST_SECRET_KEY', 'sk_test_51234567890abcdef');

// Stripe Live Keys (Use in production)
define('STRIPE_LIVE_PUBLIC_KEY', 'pk_live_your_key_here');
define('STRIPE_LIVE_SECRET_KEY', 'sk_live_your_key_here');

// Get active Stripe keys based on environment
function getStripePublicKey() {
    return PAYMENT_ENVIRONMENT === 'live' ? STRIPE_LIVE_PUBLIC_KEY : STRIPE_TEST_PUBLIC_KEY;
}

function getStripeSecretKey() {
    return PAYMENT_ENVIRONMENT === 'live' ? STRIPE_LIVE_SECRET_KEY : STRIPE_TEST_SECRET_KEY;
}

// ========================================
// PAYPAL CONFIGURATION
// ========================================

// PayPal Test Credentials
define('PAYPAL_TEST_CLIENT_ID', 'your_test_client_id');
define('PAYPAL_TEST_SECRET', 'your_test_secret');
define('PAYPAL_TEST_MODE', true);

// PayPal Live Credentials
define('PAYPAL_LIVE_CLIENT_ID', 'your_live_client_id');
define('PAYPAL_LIVE_SECRET', 'your_live_secret');

// Get active PayPal credentials
function getPayPalCredentials() {
    if (PAYMENT_ENVIRONMENT === 'live') {
        return [
            'client_id' => PAYPAL_LIVE_CLIENT_ID,
            'secret' => PAYPAL_LIVE_SECRET,
            'mode' => 'live'
        ];
    } else {
        return [
            'client_id' => PAYPAL_TEST_CLIENT_ID,
            'secret' => PAYPAL_TEST_SECRET,
            'mode' => 'sandbox'
        ];
    }
}

// ========================================
// CURRENCY SETTINGS
// ========================================

define('CURRENCY_CODE', 'LKR');  // Sri Lankan Rupee
define('CURRENCY_SYMBOL', 'Rs.');
define('MIN_PAYMENT_AMOUNT', 100);  // Minimum Rs. 100
define('MAX_PAYMENT_AMOUNT', 10000000);  // Maximum Rs. 10M

// ========================================
// ESCROW SETTINGS
// ========================================

define('ESCROW_HOLD_DAYS', 7);  // Hold payment in escrow for 7 days
define('ESCROW_AUTO_RELEASE', true);  // Auto-release after hold period
define('ESCROW_ACCOUNT_NAME', 'FixLanka Escrow');

// ========================================
// FEE SETTINGS
// ========================================

define('PLATFORM_FEE_PERCENTAGE', 5);  // 5% platform fee
define('PAYMENT_GATEWAY_FEE', 2.9);  // 2.9% + Rs. 30 (typical)
define('PAYMENT_GATEWAY_FIXED_FEE', 30);

// Calculate total fees
function calculatePaymentFees($amount) {
    $platformFee = ($amount * PLATFORM_FEE_PERCENTAGE) / 100;
    $gatewayFee = (($amount * PAYMENT_GATEWAY_FEE) / 100) + PAYMENT_GATEWAY_FIXED_FEE;
    $totalFees = $platformFee + $gatewayFee;
    $netAmount = $amount - $totalFees;
    
    return [
        'gross_amount' => $amount,
        'platform_fee' => round($platformFee, 2),
        'gateway_fee' => round($gatewayFee, 2),
        'total_fees' => round($totalFees, 2),
        'net_amount' => round($netAmount, 2)
    ];
}

// ========================================
// WEBHOOK SETTINGS
// ========================================

define('STRIPE_WEBHOOK_SECRET', 'whsec_your_webhook_secret');
define('PAYPAL_WEBHOOK_ID', 'your_webhook_id');

// ========================================
// PAYMENT METHODS ENABLED
// ========================================

define('ENABLE_CREDIT_CARD', true);
define('ENABLE_DEBIT_CARD', true);
define('ENABLE_BANK_TRANSFER', true);
define('ENABLE_DIGITAL_WALLET', true);

// ========================================
// SECURITY SETTINGS
// ========================================

define('PAYMENT_TIMEOUT_SECONDS', 300);  // 5 minutes
define('MAX_PAYMENT_ATTEMPTS', 3);
define('PAYMENT_LOG_RETENTION_DAYS', 365);  // Keep logs for 1 year

// ========================================
// NOTIFICATION SETTINGS
// ========================================

define('SEND_PAYMENT_CONFIRMATION_EMAIL', true);
define('SEND_PAYMENT_RECEIPT_EMAIL', true);
define('SEND_ESCROW_NOTIFICATIONS', true);

// ========================================
// REFUND SETTINGS
// ========================================

define('ALLOW_REFUNDS', true);
define('REFUND_WINDOW_DAYS', 14);  // Allow refunds within 14 days
define('AUTO_REFUND_ON_DISPUTE', false);  // Manual review required

// ========================================
// TEST CARD NUMBERS (Stripe)
// ========================================

// Use these for testing in test mode:
// Success: 4242 4242 4242 4242
// Decline: 4000 0000 0000 0002
// Insufficient Funds: 4000 0000 0000 9995
// Expired Card: 4000 0000 0000 0069

// ========================================
// UTILITY FUNCTIONS
// ========================================

/**
 * Format amount for display
 */
function formatPaymentAmount($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 2);
}

/**
 * Convert amount to cents (for Stripe)
 */
function convertToCents($amount) {
    return round($amount * 100);
}

/**
 * Convert cents to amount
 */
function convertFromCents($cents) {
    return $cents / 100;
}

/**
 * Validate payment amount
 */
function validatePaymentAmount($amount) {
    if ($amount < MIN_PAYMENT_AMOUNT) {
        return [
            'valid' => false,
            'message' => 'Amount must be at least ' . formatPaymentAmount(MIN_PAYMENT_AMOUNT)
        ];
    }
    
    if ($amount > MAX_PAYMENT_AMOUNT) {
        return [
            'valid' => false,
            'message' => 'Amount exceeds maximum of ' . formatPaymentAmount(MAX_PAYMENT_AMOUNT)
        ];
    }
    
    return ['valid' => true];
}

/**
 * Generate unique payment reference
 */
function generatePaymentReference($prefix = 'PAY') {
    return $prefix . '-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

/**
 * Log payment activity
 */
function logPaymentActivity($userId, $action, $amount, $status, $details = []) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO payment_logs (
                user_id,
                action,
                amount,
                status,
                details,
                ip_address,
                user_agent,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $userId,
            $action,
            $amount,
            $status,
            json_encode($details),
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log("Payment log error: " . $e->getMessage());
        return false;
    }
}

?>
