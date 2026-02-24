// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\payment.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../models/ContractModel.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$contractId = $_GET['contract_id'] ?? null;
$contractData = [];

if ($contractId) {
    try {
        $contractModel = new ContractModel();
        // Use correct method name getById
        $contract = $contractModel->getById($contractId);
        
        // Verify ownership (optional but recommended)
        // Assuming session verification is handled by requireRole or similar, but for user payment:
        // if ($contract && $contract['customer_id'] != $_SESSION['user_id']) { $contract = null; $error = "Unauthorized access."; }

        if ($contract) {
            $contractData = $contract;
        } else {
             $error = "Contract not found.";
        }
    } catch (Exception $e) {
        $error = "Error loading contract details.";
    }
} else {
    $error = "No contract specified.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="payment-header">
        <div class="header-container">
            <a href="/2nd-Year-Group-Project/FixLanka/" class="logo">
                <span class="logo-text">Fix<span class="logo-highlight">Lanka</span></span>
            </a>
            <div class="secure-badge">
                <i class="fas fa-lock"></i>
                <span>Secure Payment</span>
            </div>
        </div>
    </header>

    <!-- Progress Indicator -->
    <div class="progress-container">
        <div class="progress-steps">
            <div class="step completed">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Cart</span>
            </div>
            <div class="step-line completed"></div>
            <div class="step completed">
                <div class="step-circle">
                    <i class="fas fa-check"></i>
                </div>
                <span class="step-label">Checkout</span>
            </div>
            <div class="step-line active"></div>
            <div class="step active">
                <div class="step-circle">3</div>
                <span class="step-label">Payment</span>
            </div>
        </div>
    </div>

    </div>

    <?php if (!empty($error)): ?>
        <div class="error-container" style="text-align: center; padding: 50px;">
            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 20px;"></i>
            <h2>Error</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/dashboard.php" class="btn-primary" style="display: inline-block; margin-top: 20px; padding: 10px 20px; text-decoration: none;">Return to Dashboard</a>
        </div>
    <?php else: ?>

    <!-- Main Content -->
    <main class="payment-main">
        <div class="payment-container">
            <!-- Left Side - Payment Form -->
            <div class="payment-form-section">
                <div class="form-card">
                    <h2 class="form-title">
                        <i class="fas fa-credit-card"></i>
                        Payment Information
                    </h2>

                    <form id="paymentForm" class="payment-form">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3 class="section-title">Personal Details</h3>
                            
                            <div class="form-group">
                                <label for="fullName" class="form-label">
                                    Full Name <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                    <input 
                                        type="text" 
                                        id="fullName" 
                                        name="fullName"
                                        class="form-input"
                                        placeholder="John Doe"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    Email Address <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email"
                                        class="form-input"
                                        placeholder="john.doe@example.com"
                                        required
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="form-section">
                            <h3 class="section-title">Billing Address</h3>
                            
                            <div class="form-group">
                                <label for="address" class="form-label">
                                    Street Address <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-home input-icon"></i>
                                    <input 
                                        type="text" 
                                        id="address" 
                                        name="address"
                                        class="form-input"
                                        placeholder="123 Main Street"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city" class="form-label">
                                        City <span class="required">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="city" 
                                        name="city"
                                        class="form-input"
                                        placeholder="Colombo"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="state" class="form-label">
                                        State/Province <span class="required">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="state" 
                                        name="state"
                                        class="form-input"
                                        placeholder="Western Province"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="zipCode" class="form-label">
                                        ZIP/Postal Code <span class="required">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="zipCode" 
                                        name="zipCode"
                                        class="form-input"
                                        placeholder="10100"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="country" class="form-label">
                                        Country <span class="required">*</span>
                                    </label>
                                    <select id="country" name="country" class="form-select" required>
                                        <option value="">Select Country</option>
                                        <option value="LK" selected>Sri Lanka</option>
                                        <option value="IN">India</option>
                                        <option value="US">United States</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Card Information -->
                        <div class="form-section">
                            <h3 class="section-title">Card Details</h3>
                            
                            <div class="form-group">
                                <label for="cardNumber" class="form-label">
                                    Card Number <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <i class="fas fa-credit-card input-icon" id="cardIcon"></i>
                                    <input 
                                        type="text" 
                                        id="cardNumber" 
                                        name="cardNumber"
                                        class="form-input"
                                        placeholder="1234 5678 9012 3456"
                                        maxlength="19"
                                        required
                                    >
                                    <div class="card-type" id="cardType"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiryDate" class="form-label">
                                        Expiration Date <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-calendar input-icon"></i>
                                        <input 
                                            type="text" 
                                            id="expiryDate" 
                                            name="expiryDate"
                                            class="form-input"
                                            placeholder="MM/YY"
                                            maxlength="5"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="cvv" class="form-label">
                                        CVV <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input 
                                            type="text" 
                                            id="cvv" 
                                            name="cvv"
                                            class="form-input"
                                            placeholder="123"
                                            maxlength="4"
                                            required
                                        >
                                        <div class="cvv-hint" title="3 or 4 digits on the back of your card">
                                            <i class="fas fa-question-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="saveCard" name="saveCard">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Save card for future payments</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" id="cancelBtn">
                                <i class="fas fa-arrow-left"></i>
                                Cancel
                            </button>
                            <button type="submit" class="btn-pay" id="payBtn">
                                <i class="fas fa-shield-alt"></i>
                                Pay Now
                            </button>
                        </div>
                    </form>

                    <!-- Trusted Payment Methods -->
                    <div class="payment-methods">
                        <p class="methods-title">We Accept</p>
                        <div class="methods-icons">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-amex"></i>
                            <i class="fab fa-cc-paypal"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Order Summary -->
            <div class="order-summary-section">
                <div class="summary-card">
                    <h3 class="summary-title">Order Summary</h3>

                    <div class="order-item">
                        <div class="item-details">
                            <h4 class="item-name"><?php echo htmlspecialchars($contractData['project_title'] ?? 'Service Payment'); ?></h4>
                            <p class="item-description"><?php echo htmlspecialchars($contractData['project_description'] ?? 'Project payment'); ?></p>
                            <p class="item-provider">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($contractData['company_name'] ?? 'Service Provider'); ?>
                            </p>
                        </div>
                        <div class="item-price">
                            <span class="price-amount">LKR <?php echo number_format($contractData['contract_value'] ?? 0, 2); ?></span>
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Promo Code -->
                    <div class="promo-section">
                        <div class="promo-input-group">
                            <input 
                                type="text" 
                                id="promoCode" 
                                class="promo-input"
                                placeholder="Enter promo code"
                            >
                            <button type="button" class="btn-apply" id="applyPromoBtn">
                                Apply
                            </button>
                        </div>
                        <div class="promo-message" id="promoMessage"></div>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
                        <?php 
                            $subtotal = $contractData['contract_value'] ?? 0;
                            $serviceFee = $subtotal * 0.10; // 10% Service Fee
                            $total = $subtotal + $serviceFee;
                        ?>
                        <div class="price-row">
                            <span class="price-label">Subtotal</span>
                            <span class="price-value" id="subtotal">LKR <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="price-row">
                            <span class="price-label">Service Fee (10%)</span>
                            <span class="price-value" id="serviceFee">LKR <?php echo number_format($serviceFee, 2); ?></span>
                        </div>
                        <div class="price-row discount-row" id="discountRow" style="display: none;">
                            <span class="price-label">
                                <i class="fas fa-tag"></i>
                                Discount
                            </span>
                            <span class="price-value discount" id="discount">- LKR 0.00</span>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="price-row total-row">
                            <span class="price-label">Total</span>
                            <span class="price-value total" id="total">LKR <?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>

                    <!-- Security Badge -->
                    <div class="security-info">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Secure Payment</strong>
                            <p>Your payment information is encrypted and secure</p>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="help-card">
                    <i class="fas fa-headset"></i>
                    <h4>Need Help?</h4>
                    <p>Our support team is available 24/7</p>
                    <a href="chat.html" class="help-link">
                        <i class="fas fa-comments"></i>
                        Chat with Support
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="payment-footer">
        <div class="footer-links">
            <a href="#terms">Terms of Service</a>
            <span class="separator">|</span>
            <a href="#privacy">Privacy Policy</a>
            <span class="separator">|</span>
            <a href="#refund">Refund Policy</a>
        </div>
        <p class="footer-text">&copy; 2025 FixLanka. All rights reserved.</p>
    </footer>

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-container success-modal">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="success-title">Payment Successful!</h2>
            <p class="success-message">
                Your payment has been processed successfully. 
                A confirmation email has been sent to your email address.
            </p>
            <div class="transaction-details">
                <p><strong>Transaction ID:</strong> <span id="transactionId">TXN123456789</span></p>
                <p><strong>Amount Paid:</strong> <span id="amountPaid">LKR 5,500</span></p>
            </div>
            <div class="success-actions">
                <button class="btn-primary" id="viewOrderBtn">
                    <i class="fas fa-receipt"></i>
                    View Order
                </button>
                <button class="btn-secondary" id="backToDashboardBtn">
                    <i class="fas fa-home"></i>
                    Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Processing your payment...</p>
        </div>
    </div>

    <!-- Floating Help Button -->
    <a href="chat.html" class="floating-help-btn" title="Need Help?">
        <i class="fas fa-headset"></i>
    </a>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/payment.js"></script>
<?php endif; ?>
</body>
</html>