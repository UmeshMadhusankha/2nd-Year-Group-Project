<?php
/**
 * Payment Gateway API
 * Phase 4 - Feature 2: Payment processing with Stripe/PayPal
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/payment.php';
require_once __DIR__ . '/../controllers/PaymentController.php';

session_start();

// Use global database connection
global $conn;
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$controller = new PaymentController($conn);

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id && $action !== 'get_config') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

switch ($action) {
    case 'create_intent':
        $result = $controller->createPaymentIntent(
            $_POST['contract_id'],
            $_POST['amount'],
            $_POST['description'] ?? ''
        );
        echo json_encode($result);
        break;
    
    case 'confirm_payment':
        $result = $controller->processPayment(
            $_POST['payment_id'],
            json_decode($_POST['payment_method'] ?? '{}', true)
        );
        echo json_encode($result);
        break;
    
    case 'release_escrow':
        $result = $controller->releaseFromEscrow(
            $_POST['contract_id'],
            $_POST['amount'],
            $_POST['reason'] ?? ''
        );
        echo json_encode($result);
        break;
    
    case 'get_config':
        echo json_encode([
            'success' => true,
            'stripe_public_key' => getStripePublicKey(),
            'currency' => CURRENCY_CODE
        ]);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
