<?php
// Start session and check authentication
require_once __DIR__ . '/../../config/session.php';
requireRole(['company']);
$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/settings.css?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../assets/css/company/settings.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <!-- Profile Header -->
            <div class="settings-header">
                <div class="header-left">
                    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                    <p class="subtitle">Manage your personal and company information</p>
                </div>
                <div class="header-right">
                    <button class="btn-save-all" id="saveAllBtn">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="settings-container">
                <!-- Profile Navigation Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="company-tab">
                        <i class="fas fa-building"></i>
                        <span>Company Profile</span>
                    </button>
                    <button class="tab-btn" data-tab="payment-tab">
                        <i class="fas fa-credit-card"></i>
                        <span>Payment & Banking</span>
                    </button>
                    <button class="tab-btn" data-tab="subscription-tab">
                        <i class="fas fa-layer-group"></i>
                        <span>Subscription</span>
                    </button>
                </div>

                <!-- Profile Content Panels -->
                <div class="settings-content">

                    <!-- Company Profile Tab -->
                    <div class="tab-content" id="company-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Company Information</h2>

                            <!-- Company Logo -->
                            <div class="form-group logo-upload">
                                <label>Company Logo</label>
                                <div class="logo-preview">
                                    <img src="/2nd-Year-Group-Project/FixLanka/assets/images/fixlanka.png" alt="Company Logo" id="logoImage">
                                    <div class="logo-actions">
                                        <button class="btn-upload" id="uploadLogoBtn">
                                            <i class="fas fa-camera"></i> Change Logo
                                        </button>
                                        <input type="file" id="companyLogo" accept="image/*" hidden>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Details -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="companyName">Company Name *</label>
                                    <input type="text" id="companyName" value="" required>
                                </div>
                                <div class="form-group">
                                    <label>Service Categories * (Select at least one)</label>
                                    <div class="checkbox-group business-type-grid" id="companyBusinessTypes">
                                        <label><input type="checkbox" class="company-business-type" value="Plumbing"> Plumbing</label>
                                        <label><input type="checkbox" class="company-business-type" value="Electrical"> Electrical</label>
                                        <label><input type="checkbox" class="company-business-type" value="HVAC"> HVAC</label>
                                        <label><input type="checkbox" class="company-business-type" value="Cleaning"> Cleaning</label>
                                        <label><input type="checkbox" class="company-business-type" value="Carpentry"> Carpentry</label>
                                        <label><input type="checkbox" class="company-business-type" value="Painting"> Painting</label>
                                        <label><input type="checkbox" class="company-business-type" value="Appliance Repair"> Appliance Repair</label>
                                        <label><input type="checkbox" class="company-business-type" value="Roofing"> Roofing</label>
                                        <label><input type="checkbox" class="company-business-type" value="Landscaping"> Landscaping</label>
                                        <label><input type="checkbox" class="company-business-type" value="Pest Control"> Pest Control</label>
                                        <label><input type="checkbox" class="company-business-type" value="Home Security"> Home Security</label>
                                        <label><input type="checkbox" class="company-business-type" value="Interior Design"> Interior Design</label>
                                        <label><input type="checkbox" class="company-business-type" value="Flooring"> Flooring</label>
                                        <label><input type="checkbox" class="company-business-type" value="Masonry"> Masonry</label>
                                        <label><input type="checkbox" class="company-business-type" value="Welding"> Welding</label>
                                        <label><input type="checkbox" class="company-business-type" value="Construction"> Construction</label>
                                        <label><input type="checkbox" class="company-business-type" value="Other" id="companyBusinessTypeOther"> Other</label>
                                    </div>
                                    <small class="form-hint">Update what services your company provides.</small>
                                </div>
                            </div>

                            <div class="form-group" id="companyBusinessTypeOtherGroup" style="display:none;">
                                <label for="companyBusinessTypeOtherText">Other Service Category *</label>
                                <input type="text" id="companyBusinessTypeOtherText" placeholder="Type your service category">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="registrationNumber">Business Registration Number</label>
                                    <input type="text" id="registrationNumber" value="">
                                </div>
                                <div class="form-group">
                                    <label for="taxId">Tax ID / VAT Number</label>
                                    <input type="text" id="taxId" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="companyDescription">Company Description</label>
                                <textarea id="companyDescription" rows="4" placeholder="Tell us about your company..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Skills / Services (Tags)</label>
                                <div class="skills-tag-input">
                                    <div class="skill-tags" id="companySkillsTags"></div>
                                    <input type="text" id="companySkillsInput" placeholder="Type a skill and press Enter">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="website">Website</label>
                                    <input type="url" id="website" value="" placeholder="https://yourcompany.com">
                                </div>
                                <div class="form-group">
                                    <label for="establishedYear">Established Year</label>
                                    <input type="number" id="establishedYear" value="" min="1900" max="2025">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="settings-section">
                            <h2 class="section-title">Contact Information</h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Primary Email *</label>
                                    <input type="email" id="email" value="" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" value="" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="alternatePhone">Alternate Phone</label>
                                    <input type="tel" id="alternatePhone" value="">
                                </div>
                                <div class="form-group">
                                    <label for="companyWhatsapp">WhatsApp Business</label>
                                    <input type="tel" id="companyWhatsapp" value="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Business Address *</label>
                                <input type="text" id="address" value="" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" value="">
                                </div>
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <select id="province">
                                        <option value="western">Western</option>
                                        <option value="central">Central</option>
                                        <option value="southern">Southern</option>
                                        <option value="northern">Northern</option>
                                        <option value="eastern">Eastern</option>
                                        <option value="north-western">North Western</option>
                                        <option value="north-central">North Central</option>
                                        <option value="uva">Uva</option>
                                        <option value="sabaragamuwa">Sabaragamuwa</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="postalCode">Postal Code</label>
                                    <input type="text" id="postalCode" value="">
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="settings-section">
                            <h2 class="section-title">Social Media</h2>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="facebook"><i class="fab fa-facebook"></i> Facebook</label>
                                    <input type="url" id="facebook" placeholder="https://facebook.com/yourcompany">
                                </div>
                                <div class="form-group">
                                    <label for="instagram"><i class="fab fa-instagram"></i> Instagram</label>
                                    <input type="url" id="instagram" placeholder="https://instagram.com/yourcompany">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="linkedin"><i class="fab fa-linkedin"></i> LinkedIn</label>
                                    <input type="url" id="linkedin" placeholder="https://linkedin.com/company/yourcompany">
                                </div>
                                <div class="form-group">
                                    <label for="twitter"><i class="fab fa-twitter"></i> Twitter</label>
                                    <input type="url" id="twitter" placeholder="https://twitter.com/yourcompany">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelCompanyBtn">Cancel</button>
                            <button class="btn-primary" id="saveCompanyBtn">
                                <i class="fas fa-save"></i> Save Company Profile
                            </button>
                        </div>
                    </div>

                    <!-- Payment & Banking Tab -->
                    <div class="tab-content" id="payment-tab">
                        <!-- Bank Accounts for Receiving Payments -->
                        <div class="settings-section">
                            <h2 class="section-title">Bank Accounts for Receiving Payments</h2>
                            <p class="section-description">Manage bank accounts where you receive payments from customers</p>

                            <!-- Primary Bank Account -->
                            <!-- Bank Accounts will be loaded here -->

                            <!-- Add New Bank Account Button -->
                            <button class="btn-add-card" id="addBankAccountBtn">
                                <i class="fas fa-plus"></i> Add Bank Account
                            </button>
                        </div>

                        <!-- Bank Account Details Form (Initially Hidden) -->
                        <div class="settings-section" id="bankAccountForm" style="display: none;">
                            <h2 class="section-title">Add Bank Account Details</h2>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bankName">Bank Name *</label>
                                    <select id="bankName" required>
                                        <option value="">Select Bank</option>
                                        <option value="boc">Bank of Ceylon</option>
                                        <option value="peoples">People's Bank</option>
                                        <option value="commercial">Commercial Bank</option>
                                        <option value="sampath">Sampath Bank</option>
                                        <option value="hnb">Hatton National Bank</option>
                                        <option value="ndb">National Development Bank</option>
                                        <option value="seylan">Seylan Bank</option>
                                        <option value="dfcc">DFCC Bank</option>
                                        <option value="nations_trust">Nations Trust Bank</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="branchName">Branch Name *</label>
                                    <input type="text" id="branchName" placeholder="e.g., Colombo Fort" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="accountNumber">Account Number *</label>
                                    <input type="text" id="accountNumber" placeholder="Enter account number" required>
                                </div>
                                <div class="form-group">
                                    <label for="accountHolderName">Account Holder Name *</label>
                                    <input type="text" id="accountHolderName" placeholder="As per bank records" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="accountType">Account Type *</label>
                                    <select id="accountType" required>
                                        <option value="">Select Type</option>
                                        <option value="savings">Savings Account</option>
                                        <option value="current">Current Account</option>
                                        <option value="business">Business Account</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="swiftCode">SWIFT/BIC Code</label>
                                    <input type="text" id="swiftCode" placeholder="For international payments">
                                    <small>Optional - Required only for international transactions</small>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button class="btn-secondary" id="cancelBankAccountBtn">Cancel</button>
                                <button class="btn-primary" id="saveBankAccountBtn">
                                    <i class="fas fa-save"></i> Save Bank Account
                                </button>
                            </div>
                        </div>

                        <!-- Digital Wallets for Receiving Payments -->
                        <div class="settings-section">
                            <h2 class="section-title">Digital Wallets & Payment Gateways</h2>
                            <p class="section-description">Accept payments through digital wallets and online payment methods</p>

                            <!-- Connected Wallet -->
                            <div class="payment-method-card">
                                <div class="card-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="card-info">
                                    <h4>eZ Cash Merchant Account</h4>
                                    <p>Merchant ID: ****2341 • Status: Connected</p>
                                </div>
                                <span class="status-badge enabled">Active</span>
                                <button class="btn-icon" title="Settings">
                                    <i class="fas fa-cog"></i>
                                </button>
                            </div>

                            <!-- Available Wallets to Connect -->
                            <div class="payment-method-card">
                                <div class="card-icon" style="color: #9ca3af;">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="card-info">
                                    <h4>mCash Merchant</h4>
                                    <p>Accept payments via mCash mobile wallet</p>
                                </div>
                                <button class="btn-enable">Connect</button>
                            </div>

                            <div class="payment-method-card">
                                <div class="card-icon" style="color: #9ca3af;">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="card-info">
                                    <h4>PayHere Payment Gateway</h4>
                                    <p>Accept credit/debit cards and online banking</p>
                                </div>
                                <button class="btn-enable">Connect</button>
                            </div>
                        </div>

                        <!-- Payment Methods for Paying Repairers -->
                        <div class="settings-section">
                            <h2 class="section-title">Payout Methods for Repairers</h2>
                            <p class="section-description">Payment methods you use to pay your hired repairers and workforce</p>

                            <!-- Bank Transfer Option -->
                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3><i class="fas fa-university"></i> Bank Transfer</h3>
                                    <p>Direct bank transfers to repairer accounts (1-2 business days)</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="payoutBankTransfer" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <!-- Mobile Money Option -->
                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3><i class="fas fa-mobile-alt"></i> Mobile Money Transfer</h3>
                                    <p>Instant payments via eZ Cash, mCash, or other mobile wallets</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="payoutMobileMoney" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <!-- Cash Payment Option -->
                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3><i class="fas fa-money-bill-wave"></i> Cash Payment</h3>
                                    <p>Pay repairers in cash on-site or at office</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="payoutCash" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <!-- Cheque Payment Option -->
                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3><i class="fas fa-file-invoice"></i> Cheque Payment</h3>
                                    <p>Issue cheques for larger payments (3-5 business days to clear)</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="payoutCheque">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Payout Settings -->
                        <div class="settings-section">
                            <h2 class="section-title">Payout Settings</h2>
                            
                            <div class="form-group">
                                <label for="payoutSchedule">Default Payout Schedule</label>
                                <select id="payoutSchedule">
                                    <option value="immediate">Immediate (After project completion)</option>
                                    <option value="weekly">Weekly (Every Friday)</option>
                                    <option value="biweekly">Bi-weekly (1st & 15th)</option>
                                    <option value="monthly">Monthly (End of month)</option>
                                </select>
                                <small>How often you process payments to repairers</small>
                            </div>

                            <div class="form-group">
                                <label for="minimumPayout">Minimum Payout Amount (LKR)</label>
                                <input type="number" id="minimumPayout" value="1000" min="100">
                                <small>Repairers must earn at least this amount before payout is processed</small>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="payoutTransactionFee">Transaction Fee Handling</label>
                                    <select id="payoutTransactionFee">
                                        <option value="company_bears">Company bears the fee</option>
                                        <option value="repairer_bears">Deduct from repairer payment</option>
                                        <option value="split">Split 50/50</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="payoutNotification">Payout Notifications</label>
                                    <select id="payoutNotification">
                                        <option value="email_sms">Email + SMS</option>
                                        <option value="email">Email only</option>
                                        <option value="sms">SMS only</option>
                                        <option value="none">No notifications</option>
                                    </select>
                                </div>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Auto-approve Payouts</h3>
                                    <p>Automatically process payouts without manual approval</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="autoApprovePayout">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Payment History & Wallet Balance -->
                        <div class="settings-section">
                            <h2 class="section-title">Payment Summary</h2>
                            
                            <div class="billing-info">
                                <div class="info-item">
                                    <span class="label">Available Balance:</span>
                                    <span class="value" style="color: #10b981; font-size: 18px; font-weight: 700;">LKR 125,450.00</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Pending Payouts:</span>
                                    <span class="value" style="color: #f59e0b;">LKR 45,000.00</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Total Paid This Month:</span>
                                    <span class="value">LKR 285,600.00</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Total Received This Month:</span>
                                    <span class="value">LKR 425,800.00</span>
                                </div>
                            </div>

                            <button class="btn-primary" style="margin-top: 16px;">
                                <i class="fas fa-history"></i> View Full Payment History
                            </button>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelPaymentBtn">Cancel</button>
                            <button class="btn-primary" id="savePaymentBtn">
                                <i class="fas fa-save"></i> Save Payment Settings
                            </button>
                        </div>
                    </div>

                    <!-- Subscription Tab -->
                    <div class="tab-content" id="subscription-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Subscription Plan</h2>

                            <div class="current-plan-card">
                                <div class="plan-header">
                                    <div class="plan-info">
                                        <h3>Loading...</h3>
                                        <p>Fetching subscription details...</p>
                                    </div>
                                    <div class="plan-price">
                                        <span class="price">LKR 0</span>
                                        <span class="period">/month</span>
                                    </div>
                                </div>
                                <div class="plan-features">
                                    <!-- Features will be populated dynamically -->
                                </div>
                                <div class="plan-actions">
                                    <button class="btn-secondary" id="subscriptionChangePlanBtn">Change Plan</button>
                                    <button class="action-btn danger" id="subscriptionCancelBtn">Cancel Subscription</button>
                                </div>
                            </div>

                            <div class="billing-info">
                                <div class="info-item">
                                    <span class="label">Billing Period:</span>
                                    <span class="value">Loading...</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Next Billing Date:</span>
                                    <span class="value">Loading...</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Payment Method:</span>
                                    <span class="value">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Payment Methods</h2>

                            <div id="payment-methods-container">
                                <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                                    <i class="fas fa-spinner fa-spin"></i> Loading payment methods...
                                </p>
                            </div>

                            <button class="btn-add-card" id="addCardBtn">
                                <i class="fas fa-plus"></i> Add Payment Method
                            </button>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Billing History</h2>

                            <div class="billing-history">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                                                <i class="fas fa-spinner fa-spin"></i> Loading billing history...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button class="btn-secondary" id="viewAllInvoicesBtn">
                                View All Invoices
                            </button>
                        </div>
                    </div>

                    <!-- Reviews & Feedback Tab -->
                    <div class="tab-content" id="reviews-tab">
                        <!-- Reviews Overview -->
                        <div class="settings-section">
                            <h2 class="section-title">Reviews & Feedback Overview</h2>
                            <p class="section-description">Monitor and manage customer feedback for your company, projects, and workforce</p>

                            <!-- Reviews Summary Cards -->
                            <div class="reviews-summary">
                                <div class="review-card summary-card">
                                    <div class="review-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="review-info">
                                        <h3>Company Rating</h3>
                                        <div class="rating-display">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <span class="rating-score">4.6</span>
                                        </div>
                                        <p class="review-count">124 reviews</p>
                                    </div>
                                </div>

                                <div class="review-card summary-card">
                                    <div class="review-icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div class="review-info">
                                        <h3>Project Satisfaction</h3>
                                        <div class="rating-display">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                            <span class="rating-score">4.2</span>
                                        </div>
                                        <p class="review-count">89 project reviews</p>
                                    </div>
                                </div>

                                <div class="review-card summary-card">
                                    <div class="review-icon">
                                        <i class="fas fa-users-cog"></i>
                                    </div>
                                    <div class="review-info">
                                        <h3>Workforce Quality</h3>
                                        <div class="rating-display">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <span class="rating-score">4.8</span>
                                        </div>
                                        <p class="review-count">156 worker reviews</p>
                                    </div>
                                </div>

                                <div class="review-card summary-card">
                                    <div class="review-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="review-info">
                                        <h3>Monthly Trend</h3>
                                        <div class="rating-trend">
                                            <span class="trend-value positive">+0.3</span>
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                        <p class="review-count">vs last month</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter and Search -->
                        <div class="settings-section">
                            <div class="reviews-filter-bar">
                                <div class="filter-left">
                                    <div class="filter-group">
                                        <label for="reviewCategory">Category:</label>
                                        <select id="reviewCategory">
                                            <option value="all">All Reviews</option>
                                            <option value="company">Company Reviews</option>
                                            <option value="projects">Project Reviews</option>
                                            <option value="workforce">Workforce Reviews</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="reviewRating">Rating:</label>
                                        <select id="reviewRating">
                                            <option value="all">All Ratings</option>
                                            <option value="5">5 Stars</option>
                                            <option value="4">4 Stars</option>
                                            <option value="3">3 Stars</option>
                                            <option value="2">2 Stars</option>
                                            <option value="1">1 Star</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="reviewPeriod">Period:</label>
                                        <select id="reviewPeriod">
                                            <option value="all">All Time</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                            <option value="quarter">This Quarter</option>
                                            <option value="year">This Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-right">
                                    <div class="search-group">
                                        <input type="text" id="reviewSearch" placeholder="Search reviews..." class="search-input">
                                        <button class="search-btn">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Reviews -->
                        <div class="settings-section">
                            <div class="section-header">
                                <h2 class="section-title">Recent Reviews</h2>
                                <button class="btn-secondary" id="exportReviewsBtn">
                                    <i class="fas fa-download"></i> Export Reviews
                                </button>
                            </div>

                            <!-- Review Items -->
                            <div class="reviews-list">
                                <!-- Company Review -->
                                <div class="review-item company-review">
                                    <div class="review-header">
                                        <div class="review-meta">
                                            <div class="reviewer-info">
                                                <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                                <div class="reviewer-details">
                                                    <h4>Sarah Johnson</h4>
                                                    <p class="review-type">Company Review</p>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <span class="rating-number">5.0</span>
                                            </div>
                                        </div>
                                        <div class="review-date">2 days ago</div>
                                    </div>
                                    <div class="review-content">
                                        <p>"Excellent service! FixLanka Solutions exceeded our expectations. Professional team, timely delivery, and high-quality workmanship. Highly recommended for any repair or maintenance needs."</p>
                                    </div>
                                    <div class="review-tags">
                                        <span class="tag">Professional</span>
                                        <span class="tag">Timely</span>
                                        <span class="tag">Quality Work</span>
                                    </div>
                                    <div class="review-actions">
                                        <button class="btn-action" title="Reply">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        <button class="btn-action" title="Share">
                                            <i class="fas fa-share"></i> Share
                                        </button>
                                        <button class="btn-action" title="Report">
                                            <i class="fas fa-flag"></i> Report
                                        </button>
                                    </div>
                                </div>

                                <!-- Project Review -->
                                <div class="review-item project-review">
                                    <div class="review-header">
                                        <div class="review-meta">
                                            <div class="reviewer-info">
                                                <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                                <div class="reviewer-details">
                                                    <h4>Michael Chen</h4>
                                                    <p class="review-type">Project: Kitchen Renovation</p>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                </div>
                                                <span class="rating-number">4.0</span>
                                            </div>
                                        </div>
                                        <div class="review-date">5 days ago</div>
                                    </div>
                                    <div class="review-content">
                                        <p>"Great work on our kitchen renovation. The project was completed on time and within budget. Minor delays due to weather, but the team communicated well throughout the process."</p>
                                    </div>
                                    <div class="review-tags">
                                        <span class="tag">On Time</span>
                                        <span class="tag">Budget-Friendly</span>
                                        <span class="tag">Good Communication</span>
                                    </div>
                                    <div class="review-actions">
                                        <button class="btn-action" title="Reply">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        <button class="btn-action" title="View Project">
                                            <i class="fas fa-eye"></i> View Project
                                        </button>
                                        <button class="btn-action" title="Share">
                                            <i class="fas fa-share"></i> Share
                                        </button>
                                    </div>
                                </div>

                                <!-- Worker Review -->
                                <div class="review-item worker-review">
                                    <div class="review-header">
                                        <div class="review-meta">
                                            <div class="reviewer-info">
                                                <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                                <div class="reviewer-details">
                                                    <h4>Amanda Rodriguez</h4>
                                                    <p class="review-type">Worker: John Silva (Electrician)</p>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                                <span class="rating-number">5.0</span>
                                            </div>
                                        </div>
                                        <div class="review-date">1 week ago</div>
                                    </div>
                                    <div class="review-content">
                                        <p>"John did an amazing job with our electrical installation. Very knowledgeable, professional, and clean work. Explained everything clearly and finished ahead of schedule."</p>
                                    </div>
                                    <div class="review-tags">
                                        <span class="tag">Expert</span>
                                        <span class="tag">Clean Work</span>
                                        <span class="tag">Ahead of Schedule</span>
                                    </div>
                                    <div class="review-actions">
                                        <button class="btn-action" title="Reply">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        <button class="btn-action" title="View Worker Profile">
                                            <i class="fas fa-user"></i> View Worker
                                        </button>
                                        <button class="btn-action" title="Share">
                                            <i class="fas fa-share"></i> Share
                                        </button>
                                    </div>
                                </div>

                                <!-- Critical Review -->
                                <div class="review-item critical-review">
                                    <div class="review-header">
                                        <div class="review-meta">
                                            <div class="reviewer-info">
                                                <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                                <div class="reviewer-details">
                                                    <h4>David Thompson</h4>
                                                    <p class="review-type">Project: Bathroom Repair</p>
                                                </div>
                                            </div>
                                            <div class="review-rating">
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                </div>
                                                <span class="rating-number">2.0</span>
                                            </div>
                                        </div>
                                        <div class="review-date">2 weeks ago</div>
                                    </div>
                                    <div class="review-content">
                                        <p>"Project took longer than expected and there were some communication issues. However, the final result was satisfactory and the team worked to resolve our concerns."</p>
                                    </div>
                                    <div class="review-tags">
                                        <span class="tag negative">Delayed</span>
                                        <span class="tag negative">Communication Issues</span>
                                        <span class="tag">Resolved</span>
                                    </div>
                                    <div class="review-actions">
                                        <button class="btn-action replied" title="Replied">
                                            <i class="fas fa-reply"></i> Replied
                                        </button>
                                        <button class="btn-action" title="Follow Up">
                                            <i class="fas fa-phone"></i> Follow Up
                                        </button>
                                        <button class="btn-action" title="View Project">
                                            <i class="fas fa-eye"></i> View Project
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Load More Button -->
                            <div class="load-more-container">
                                <button class="btn-secondary" id="loadMoreReviews">
                                    <i class="fas fa-plus"></i> Load More Reviews
                                </button>
                            </div>
                        </div>

                        <!-- Analytics and Insights -->
                        <div class="settings-section">
                            <h2 class="section-title">Review Analytics</h2>

                            <div class="analytics-grid">
                                <!-- Rating Distribution -->
                                <div class="analytics-card">
                                    <h3>Rating Distribution</h3>
                                    <div class="rating-bars">
                                        <div class="rating-bar">
                                            <span class="rating-label">5 stars</span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 68%"></div>
                                            </div>
                                            <span class="rating-count">84</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">4 stars</span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 22%"></div>
                                            </div>
                                            <span class="rating-count">27</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">3 stars</span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 6%"></div>
                                            </div>
                                            <span class="rating-count">8</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">2 stars</span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 3%"></div>
                                            </div>
                                            <span class="rating-count">4</span>
                                        </div>
                                        <div class="rating-bar">
                                            <span class="rating-label">1 star</span>
                                            <div class="bar-container">
                                                <div class="bar-fill" style="width: 1%"></div>
                                            </div>
                                            <span class="rating-count">1</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Keywords -->
                                <div class="analytics-card">
                                    <h3>Top Keywords in Reviews</h3>
                                    <div class="keyword-cloud">
                                        <span class="keyword large">Professional</span>
                                        <span class="keyword medium">Quality</span>
                                        <span class="keyword large">Timely</span>
                                        <span class="keyword small">Excellent</span>
                                        <span class="keyword medium">Communication</span>
                                        <span class="keyword small">Clean</span>
                                        <span class="keyword medium">Reliable</span>
                                        <span class="keyword small">Friendly</span>
                                        <span class="keyword large">Skilled</span>
                                        <span class="keyword small">Efficient</span>
                                    </div>
                                </div>

                                <!-- Response Rate -->
                                <div class="analytics-card">
                                    <h3>Response Rate</h3>
                                    <div class="response-stats">
                                        <div class="stat-item">
                                            <div class="stat-value">87%</div>
                                            <div class="stat-label">Reviews Responded To</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">2.4 hrs</div>
                                            <div class="stat-label">Average Response Time</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-value">16</div>
                                            <div class="stat-label">Pending Responses</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Review Management Actions -->
                        <div class="settings-section">
                            <h2 class="section-title">Review Management</h2>

                            <div class="management-actions">
                                <button class="btn-primary" id="requestReviewBtn">
                                    <i class="fas fa-envelope"></i> Request Reviews from Recent Customers
                                </button>
                                <button class="btn-secondary" id="reviewSettingsBtn">
                                    <i class="fas fa-cog"></i> Review Settings & Notifications
                                </button>
                                <button class="btn-secondary" id="reportAnalyticsBtn">
                                    <i class="fas fa-chart-bar"></i> Generate Detailed Report
                                </button>
                            </div>

                            <!-- Review Guidelines -->
                            <div class="review-guidelines">
                                <h4>Review Response Best Practices:</h4>
                                <ul>
                                    <li><i class="fas fa-check"></i> Respond to reviews within 24-48 hours</li>
                                    <li><i class="fas fa-check"></i> Thank customers for positive feedback</li>
                                    <li><i class="fas fa-check"></i> Address concerns professionally in negative reviews</li>
                                    <li><i class="fas fa-check"></i> Keep responses concise and helpful</li>
                                    <li><i class="fas fa-check"></i> Invite customers to contact you directly for further assistance</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelReviewsBtn">Close</button>
                            <button class="btn-primary" id="saveReviewsBtn">
                                <i class="fas fa-save"></i> Save Review Settings
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Add Payment Method Modal (Subscription) -->
    <div class="modal" id="addPaymentModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-credit-card"></i> Add Payment Method</h3>
                <button class="modal-close" onclick="closeAddCardModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addPaymentForm">
                <div class="form-group">
                    <label for="cardType">Card Type <span class="required">*</span></label>
                    <select id="cardType" name="card_type" required>
                        <option value="">Select card type</option>
                        <option value="visa">Visa</option>
                        <option value="mastercard">Mastercard</option>
                        <option value="amex">American Express</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cardNumber">Card Number <span class="required">*</span></label>
                    <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    <small class="form-hint">Only the last 4 digits will be stored</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cardHolder">Cardholder Name <span class="required">*</span></label>
                        <input type="text" id="cardHolder" name="card_holder" placeholder="John Doe" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryMonth">Expiry Month <span class="required">*</span></label>
                        <select id="expiryMonth" name="expiry_month" required>
                            <option value="">MM</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expiryYear">Expiry Year <span class="required">*</span></label>
                        <select id="expiryYear" name="expiry_year" required>
                            <option value="">YYYY</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV <span class="required">*</span></label>
                        <input type="password" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="billingAddress">Billing Address <span class="required">*</span></label>
                    <textarea id="billingAddress" name="billing_address" rows="3" placeholder="Enter billing address" required></textarea>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="makePrimary" name="make_primary">
                        <span>Set as primary payment method</span>
                    </label>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeAddCardModal()">Cancel</button>
                    <button type="submit" class="btn-primary" id="submitPaymentBtn">
                        <i class="fas fa-save"></i> Add Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            loadComponent('sidebar-container', '/2nd-Year-Group-Project/FixLanka/views/company/sidebar.php');
            loadComponent('header-container', '/2nd-Year-Group-Project/FixLanka/views/company/topbar.php');
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    document.getElementById(containerId).innerHTML = html;
                    
                    // Initialize profile dropdown after topbar loads
                    if (containerId === 'header-container') {
                        // Initialize topbar functionality
                        if (typeof initializeTopbar === 'function') {
                            setTimeout(initializeTopbar, 100);
                        }
                        // Initialize profile dropdown
                        if (typeof initProfileDropdown === 'function') {
                            setTimeout(initProfileDropdown, 200);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading component:', error);
                });
        }

        // Tab Switching Functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                btn.classList.add('active');
                const tabId = btn.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Profile Picture Upload - Removed (no longer needed)

        // Company Logo Upload
        document.getElementById('uploadLogoBtn')?.addEventListener('click', () => {
            document.getElementById('companyLogo').click();
        });

        document.getElementById('companyLogo')?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    document.getElementById('logoImage').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Change Password moved to Settings page

        // Mock Save Buttons Removed - Real logic implemented below


        // Reviews & Feedback Tab Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const categoryFilter = document.getElementById('reviewCategory');
            const ratingFilter = document.getElementById('reviewRating');
            const periodFilter = document.getElementById('reviewPeriod');
            const searchInput = document.getElementById('reviewSearch');
            const searchBtn = document.querySelector('.search-btn');

            // Filter event listeners
            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterReviews);
            }
            if (ratingFilter) {
                ratingFilter.addEventListener('change', filterReviews);
            }
            if (periodFilter) {
                periodFilter.addEventListener('change', filterReviews);
            }
            if (searchInput) {
                searchInput.addEventListener('input', filterReviews);
            }
            if (searchBtn) {
                searchBtn.addEventListener('click', filterReviews);
            }

            // Button event listeners
            const requestReviewBtn = document.getElementById('requestReviewBtn');
            const exportReviewsBtn = document.getElementById('exportReviewsBtn');
            const loadMoreReviews = document.getElementById('loadMoreReviews');
            const reviewSettingsBtn = document.getElementById('reviewSettingsBtn');
            const reportAnalyticsBtn = document.getElementById('reportAnalyticsBtn');
            const saveReviewsBtn = document.getElementById('saveReviewsBtn');
            const cancelReviewsBtn = document.getElementById('cancelReviewsBtn');

            if (requestReviewBtn) {
                requestReviewBtn.addEventListener('click', () => {
                    alert('Review request feature will be implemented soon!');
                });
            }
            if (exportReviewsBtn) {
                exportReviewsBtn.addEventListener('click', () => {
                    alert('Export functionality will be implemented soon!');
                });
            }
            if (loadMoreReviews) {
                loadMoreReviews.addEventListener('click', () => {
                    alert('Loading more reviews...');
                });
            }
            if (reviewSettingsBtn) {
                reviewSettingsBtn.addEventListener('click', () => {
                    alert('Review settings panel will be implemented soon!');
                });
            }
            if (reportAnalyticsBtn) {
                reportAnalyticsBtn.addEventListener('click', () => {
                    alert('Analytics report generation will be implemented soon!');
                });
            }
            if (saveReviewsBtn) {
                saveReviewsBtn.addEventListener('click', () => {
                    alert('Review settings saved successfully!');
                });
            }
            if (cancelReviewsBtn) {
                cancelReviewsBtn.addEventListener('click', () => {
                    // Switch back to company tab
                    document.querySelector('.tab-btn[data-tab="company-tab"]')?.click();
                });
            }

            // Review action buttons (using event delegation)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-action')) {
                    const button = e.target.closest('.btn-action');
                    const action = button.textContent.trim();
                    
                    if (action.includes('Reply')) {
                        if (button.classList.contains('replied')) {
                            alert('You have already replied to this review.');
                        } else {
                            alert('Reply functionality will be implemented soon!');
                        }
                    } else if (action.includes('Share')) {
                        alert('Share functionality will be implemented soon!');
                    } else if (action.includes('Report')) {
                        alert('Report functionality will be implemented soon!');
                    } else if (action.includes('View Project')) {
                        alert('View project functionality will be implemented soon!');
                    } else if (action.includes('View Worker')) {
                        alert('View worker profile functionality will be implemented soon!');
                    } else if (action.includes('Follow Up')) {
                        alert('Follow up functionality will be implemented soon!');
                    }
                }
            });

            // Keyword cloud interaction
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('keyword')) {
                    const keyword = e.target.textContent.trim();
                    alert(`Filtering reviews containing: "${keyword}"`);
                    // This would filter reviews containing the clicked keyword
                }
            });
        });

        function filterReviews() {
            // This function will filter reviews based on selected criteria
            console.log('Filtering reviews...');
            
            const category = document.getElementById('reviewCategory')?.value || 'all';
            const rating = document.getElementById('reviewRating')?.value || 'all';
            const period = document.getElementById('reviewPeriod')?.value || 'all';
            const search = document.getElementById('reviewSearch')?.value || '';

            console.log('Filter criteria:', { category, rating, period, search });
            
            // Get all review items
            const reviewItems = document.querySelectorAll('.review-item');
            
            reviewItems.forEach(item => {
                let shouldShow = true;
                
                // Category filter
                if (category !== 'all') {
                    const hasCategory = item.classList.contains(`${category}-review`) || 
                                      (category === 'company' && item.classList.contains('company-review')) ||
                                      (category === 'projects' && item.classList.contains('project-review')) ||
                                      (category === 'workforce' && item.classList.contains('worker-review'));
                    if (!hasCategory) shouldShow = false;
                }
                
                // Rating filter
                if (rating !== 'all' && shouldShow) {
                    const ratingElement = item.querySelector('.rating-number');
                    if (ratingElement) {
                        const itemRating = Math.floor(parseFloat(ratingElement.textContent));
                        if (itemRating != parseInt(rating)) shouldShow = false;
                    }
                }
                
                // Search filter
                if (search && shouldShow) {
                    const reviewText = item.textContent.toLowerCase();
                    if (!reviewText.includes(search.toLowerCase())) shouldShow = false;
                }
                
                // Show or hide item
                item.style.display = shouldShow ? 'block' : 'none';
            });
        }
    </script>
</body>

</html>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchCompanyProfile();
            fetchBankAccounts();

            // Business type (service categories) - Other toggle
            const otherCb = document.getElementById('companyBusinessTypeOther');
            if (otherCb) {
                otherCb.addEventListener('change', () => {
                    const group = document.getElementById('companyBusinessTypeOtherGroup');
                    if (!group) return;
                    group.style.display = otherCb.checked ? 'block' : 'none';
                    if (!otherCb.checked) {
                        const t = document.getElementById('companyBusinessTypeOtherText');
                        if (t) t.value = '';
                    }
                });
            }
            
            // Tab switching logic
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
                    
                    btn.classList.add('active');
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(tabId).style.display = 'block';
                });
            });

            // Initialize first tab
            document.getElementById('company-tab').style.display = 'block';

            // Save Company Profile
            document.getElementById('saveCompanyBtn').addEventListener('click', saveCompanyProfile);
            
            // Add Bank Account Toggle
            document.getElementById('addBankAccountBtn').addEventListener('click', () => {
               document.getElementById('bankAccountForm').style.display = 'block';
               // Clear form
               document.getElementById('bankName').value = '';
               document.getElementById('branchName').value = '';
               document.getElementById('accountNumber').value = '';
               document.getElementById('accountHolderName').value = '';
               document.getElementById('accountType').value = '';
               document.getElementById('swiftCode').value = '';
               document.getElementById('saveBankAccountBtn').dataset.id = 0; // Reset ID for new
            });
            
            document.getElementById('cancelBankAccountBtn').addEventListener('click', () => {
               document.getElementById('bankAccountForm').style.display = 'none';
            });
            
            document.getElementById('saveBankAccountBtn').addEventListener('click', saveBankAccount);
        });

        async function fetchCompanyProfile() {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php?action=get_profile');
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    document.getElementById('companyName').value = data.name || '';
                    setCompanyBusinessTypesFromData(data.business_type);
                    document.getElementById('registrationNumber').value = data.registration_no || '';
                    document.getElementById('taxId').value = data.tax_id || '';
                    document.getElementById('companyDescription').value = data.description || '';
                    setCompanySkillsFromData(data.skills);
                    document.getElementById('website').value = data.website || '';
                    // document.getElementById('establishedYear').value = data.established_year || ''; // Not in DB yet
                    
                    document.getElementById('email').value = data.email || '';
                    document.getElementById('phone').value = data.contact_no || '';
                    document.getElementById('address').value = data.address || '';
                    
                    // Populate other fields as needed...
                }
            } catch (error) {
                console.error('Error fetching profile:', error);
            }
        }

        function setCompanyBusinessTypesFromData(businessTypeValue) {
            const raw = (businessTypeValue || '').toString();
            const parts = raw
                .split(',')
                .map(s => (s || '').toString().trim())
                .filter(Boolean);

            const knownValues = new Set([
                'Plumbing', 'Electrical', 'HVAC', 'Cleaning', 'Carpentry', 'Painting',
                'Appliance Repair', 'Roofing', 'Landscaping', 'Pest Control', 'Home Security',
                'Interior Design', 'Flooring', 'Masonry', 'Welding', 'Construction'
            ]);

            const checkboxes = Array.from(document.querySelectorAll('.company-business-type'));
            checkboxes.forEach(cb => { cb.checked = false; });

            const unknown = [];
            parts.forEach(val => {
                const match = checkboxes.find(cb => (cb.value || '') === val);
                if (match) {
                    match.checked = true;
                } else if (val && !knownValues.has(val)) {
                    unknown.push(val);
                }
            });

            const otherCb = document.getElementById('companyBusinessTypeOther');
            const otherGroup = document.getElementById('companyBusinessTypeOtherGroup');
            const otherText = document.getElementById('companyBusinessTypeOtherText');
            if (otherCb && otherGroup && otherText) {
                if (unknown.length > 0) {
                    otherCb.checked = true;
                    otherGroup.style.display = 'block';
                    otherText.value = unknown.join(', ');
                } else {
                    otherCb.checked = false;
                    otherGroup.style.display = 'none';
                    otherText.value = '';
                }
            }
        }

        function getCompanyBusinessTypesForSave() {
            const selected = Array.from(document.querySelectorAll('.company-business-type:checked'))
                .map(cb => (cb.value || '').toString().trim())
                .filter(Boolean);

            const otherIdx = selected.indexOf('Other');
            if (otherIdx !== -1) {
                selected.splice(otherIdx, 1);
                const otherText = (document.getElementById('companyBusinessTypeOtherText')?.value || '').toString().trim();
                if (!otherText) {
                    return { ok: false, message: 'Please enter your other service category.' };
                }
                selected.push(otherText);
            }

            if (selected.length === 0) {
                return { ok: false, message: 'Please select at least one service category.' };
            }

            // Store same as signup: comma-separated values (no spaces)
            return { ok: true, value: selected.join(',') };
        }

        let companySkillsTags = [];
        const companySkillsInput = document.getElementById('companySkillsInput');
        const companySkillsTagsEl = document.getElementById('companySkillsTags');

        function normalizeTags(tags) {
            const seen = new Set();
            const out = [];
            (tags || []).forEach(t => {
                const cleaned = (t || '').toString().trim().replace(/\s+/g, ' ');
                if (!cleaned) return;
                const key = cleaned.toLowerCase();
                if (seen.has(key)) return;
                seen.add(key);
                out.push(cleaned);
            });
            return out;
        }

        function escapeHtml(str) {
            return (str || '').toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function setCompanySkillsFromData(skillsValue) {
            if (Array.isArray(skillsValue)) {
                companySkillsTags = normalizeTags(skillsValue);
            } else {
                const raw = (skillsValue || '').toString();
                companySkillsTags = normalizeTags(raw.split(',').map(s => s.trim()).filter(Boolean));
            }
            renderCompanySkillsTags();
        }

        function renderCompanySkillsTags() {
            if (!companySkillsTagsEl) return;
            companySkillsTagsEl.innerHTML = companySkillsTags.map((tag, idx) => {
                return `<span class="skill-tag">${escapeHtml(tag)}<button type="button" class="skill-tag-remove" data-idx="${idx}">×</button></span>`;
            }).join('');
        }

        if (companySkillsInput) {
            companySkillsInput.addEventListener('keydown', (e) => {
                if (e.key !== 'Enter') return;
                e.preventDefault();
                const val = (companySkillsInput.value || '').trim();
                if (!val) return;
                companySkillsTags = normalizeTags(companySkillsTags.concat([val]));
                companySkillsInput.value = '';
                renderCompanySkillsTags();
            });
        }

        if (companySkillsTagsEl) {
            companySkillsTagsEl.addEventListener('click', (e) => {
                const btn = e.target.closest('.skill-tag-remove');
                if (!btn) return;
                const idx = parseInt(btn.getAttribute('data-idx'), 10);
                if (Number.isNaN(idx)) return;
                companySkillsTags.splice(idx, 1);
                renderCompanySkillsTags();
            });
        }

        async function saveCompanyProfile() {
            const businessTypes = getCompanyBusinessTypesForSave();
            if (!businessTypes.ok) {
                alert(businessTypes.message);
                return;
            }
            const data = {
                action: 'update_profile',
                name: document.getElementById('companyName').value,
                business_type: businessTypes.value,
                registration_no: document.getElementById('registrationNumber').value,
                tax_id: document.getElementById('taxId').value,
                description: document.getElementById('companyDescription').value,
                skills: companySkillsTags.join(', '),
                website: document.getElementById('website').value,
                email: document.getElementById('email').value,
                contact_no: document.getElementById('phone').value,
                address: document.getElementById('address').value,
                districts: '' // todo: add district selection
            };

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                alert(result.message);
            } catch (error) {
                console.error('Error saving profile:', error);
                alert('Failed to save profile');
            }
        }

        // ==================== SUBSCRIPTION (PROFILE TAB) ====================
        const SUBSCRIPTION_PLANS = {
            free: {
                name: 'Free',
                price: 0,
                features: ['5 requests/month', '1 team member', 'Email support', 'Basic analytics']
            },
            basic: {
                name: 'Basic',
                price: 2500,
                features: ['50 requests/month', '3 team members', 'Chat support', 'Standard analytics']
            },
            professional: {
                name: 'Professional',
                price: 5000,
                features: ['Unlimited requests', '10 team members', 'Priority support', 'Advanced analytics']
            },
            enterprise: {
                name: 'Enterprise',
                price: 10000,
                features: ['Everything + Custom features', 'Unlimited team', '24/7 support', 'API access']
            }
        };

        function subscriptionNotify(message) {
            alert(message);
        }

        function subscriptionFormatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        async function loadSubscriptionData() {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_billing_data' })
                });

                const result = await response.json();
                if (result.success) {
                    populateSubscriptionData(result.data);
                } else {
                    subscriptionNotify(result.message || 'Failed to load subscription data');
                }
            } catch (error) {
                console.error('Error loading subscription data:', error);
                subscriptionNotify('Error loading subscription data');
            }
        }

        function populateSubscriptionData(data) {
            const root = document.getElementById('subscription-tab');
            if (!root) return;

            if (data.subscription) {
                const sub = data.subscription;
                const plan = SUBSCRIPTION_PLANS[sub.plan_name] || SUBSCRIPTION_PLANS.free;

                const planInfoH3 = root.querySelector('.plan-info h3');
                const planInfoP = root.querySelector('.plan-info p');
                const planPrice = root.querySelector('.plan-price .price');
                const planPeriod = root.querySelector('.plan-price .period');

                if (planInfoH3) planInfoH3.textContent = plan.name + ' Plan';
                if (planInfoP) planInfoP.textContent = 'Perfect for ' + (sub.plan_name === 'enterprise' ? 'large' : sub.plan_name === 'professional' ? 'growing' : 'small') + ' businesses';
                if (planPrice) planPrice.textContent = 'LKR ' + parseFloat(sub.plan_price).toLocaleString();
                if (planPeriod) planPeriod.textContent = '/' + sub.billing_period;

                const featuresContainer = root.querySelector('.plan-features');
                if (featuresContainer) {
                    featuresContainer.innerHTML = plan.features.map(feature => `
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>${feature}</span>
                        </div>
                    `).join('');
                }

                const billingPeriodValue = root.querySelector('.billing-info .info-item:nth-child(1) .value');
                const nextBillingValue = root.querySelector('.billing-info .info-item:nth-child(2) .value');
                if (billingPeriodValue) {
                    billingPeriodValue.textContent = sub.billing_period.charAt(0).toUpperCase() + sub.billing_period.slice(1);
                }
                if (nextBillingValue) {
                    nextBillingValue.textContent = subscriptionFormatDate(sub.next_billing_date);
                }
            }

            displaySubscriptionPaymentMethods(data.payment_methods || []);
            displaySubscriptionBillingHistory(data.billing_history || []);
        }

        function displaySubscriptionPaymentMethods(methods) {
            const root = document.getElementById('subscription-tab');
            if (!root) return;

            const container = root.querySelector('#payment-methods-container');
            if (!container) return;

            if (!methods || methods.length === 0) {
                container.innerHTML = `
                    <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                        <i class="fas fa-credit-card"></i><br>
                        No payment methods added yet
                    </p>
                `;
                return;
            }

            container.innerHTML = '';

            methods.forEach(method => {
                const cardHtml = `
                    <div class="payment-method-card ${method.is_primary ? 'active' : ''}" data-id="${method.payment_method_id}">
                        <div class="card-icon">
                            <i class="fab fa-cc-${method.card_type}"></i>
                        </div>
                        <div class="card-info">
                            <h4>${method.card_type.charAt(0).toUpperCase() + method.card_type.slice(1)} ending in ${method.last_four_digits}</h4>
                            <p>Expires ${method.expiry_month}/${method.expiry_year}</p>
                        </div>
                        ${method.is_primary ?
                            '<div class="card-badge"><span class="badge-primary">Primary</span></div>' :
                            `<button class="btn-set-primary" onclick="setPrimaryPayment(${method.payment_method_id})">Set as Primary</button>`
                        }
                        <button class="btn-icon" title="Remove" onclick="removePaymentMethod(${method.payment_method_id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', cardHtml);
            });

            const primary = methods.find(m => m.is_primary);
            if (primary) {
                const paymentMethodValue = root.querySelector('.billing-info .info-item:nth-child(3) .value');
                if (paymentMethodValue) {
                    paymentMethodValue.textContent = `•••• ${primary.last_four_digits} (${primary.card_type.charAt(0).toUpperCase() + primary.card_type.slice(1)})`;
                }
            }
        }

        function displaySubscriptionBillingHistory(history) {
            const root = document.getElementById('subscription-tab');
            if (!root) return;

            const tbody = root.querySelector('.history-table tbody');
            if (!tbody) return;

            if (!history || history.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                            <i class="fas fa-receipt"></i><br>
                            No billing history available
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = history.map(invoice => `
                <tr>
                    <td>${invoice.invoice_id}</td>
                    <td>${subscriptionFormatDate(invoice.date)}</td>
                    <td>LKR ${parseFloat(invoice.amount).toLocaleString()}</td>
                    <td><span class="status-badge ${invoice.status}">${invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1)}</span></td>
                    <td>
                        <button class="btn-icon" title="Download" onclick="downloadInvoice('${invoice.invoice_id}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-icon" title="View" onclick="viewInvoice('${invoice.invoice_id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        async function setPrimaryPayment(paymentMethodId) {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'set_primary_payment', payment_method_id: paymentMethodId })
                });

                const result = await response.json();
                if (result.success) {
                    subscriptionNotify('Primary payment method updated');
                    loadSubscriptionData();
                } else {
                    subscriptionNotify(result.message || 'Failed to update primary payment method');
                }
            } catch (error) {
                console.error('Error setting primary payment:', error);
                subscriptionNotify('Failed to update primary payment method');
            }
        }

        async function removePaymentMethod(paymentMethodId) {
            if (!confirm('Are you sure you want to remove this payment method?')) return;

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'remove_payment_method', payment_method_id: paymentMethodId })
                });

                const result = await response.json();
                if (result.success) {
                    subscriptionNotify('Payment method removed');
                    loadSubscriptionData();
                } else {
                    subscriptionNotify(result.message || 'Failed to remove payment method');
                }
            } catch (error) {
                console.error('Error removing payment method:', error);
                subscriptionNotify('Failed to remove payment method');
            }
        }

        function downloadInvoice(invoiceId) {
            subscriptionNotify('Invoice download feature coming soon');
            console.log('Download invoice:', invoiceId);
        }

        function viewInvoice(invoiceId) {
            subscriptionNotify('Invoice view feature coming soon');
            console.log('View invoice:', invoiceId);
        }

        function openAddCardModal() {
            const modal = document.getElementById('addPaymentModal');
            const form = document.getElementById('addPaymentForm');
            const yearSelect = document.getElementById('expiryYear');
            const currentYear = new Date().getFullYear();
            if (yearSelect) {
                yearSelect.innerHTML = '<option value="">YYYY</option>';
                for (let i = 0; i < 15; i++) {
                    const year = currentYear + i;
                    yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
                }
            }
            form?.reset();
            modal?.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAddCardModal() {
            const modal = document.getElementById('addPaymentModal');
            modal?.classList.remove('show');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const subscriptionBtn = document.querySelector('[data-tab="subscription-tab"]');
            subscriptionBtn?.addEventListener('click', function () {
                if (!this.dataset.loaded) {
                    loadSubscriptionData();
                    this.dataset.loaded = 'true';
                }
            });

            document.getElementById('subscriptionChangePlanBtn')?.addEventListener('click', () => {
                subscriptionNotify('Plan change feature coming soon');
            });

            document.getElementById('subscriptionCancelBtn')?.addEventListener('click', async () => {
                if (!confirm('Are you sure you want to cancel your subscription? You will lose access to premium features.')) return;
                try {
                    const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'cancel_subscription' })
                    });
                    const result = await response.json();
                    if (result.success) {
                        subscriptionNotify('Subscription cancelled successfully');
                        loadSubscriptionData();
                    } else {
                        subscriptionNotify(result.message || 'Failed to cancel subscription');
                    }
                } catch (error) {
                    console.error('Error cancelling subscription:', error);
                    subscriptionNotify('Failed to cancel subscription');
                }
            });

            document.getElementById('addCardBtn')?.addEventListener('click', openAddCardModal);

            document.getElementById('addPaymentModal')?.addEventListener('click', (e) => {
                if (e.target === e.currentTarget || e.target.classList.contains('modal-backdrop')) {
                    closeAddCardModal();
                }
            });

            const cardNumber = document.getElementById('cardNumber');
            cardNumber?.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s/g, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
            cardNumber?.addEventListener('keypress', (e) => {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
                    e.preventDefault();
                }
            });
            document.getElementById('cvv')?.addEventListener('keypress', (e) => {
                if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
                    e.preventDefault();
                }
            });

            document.getElementById('addPaymentForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = document.getElementById('submitPaymentBtn');
                const cardNumberRaw = (document.getElementById('cardNumber')?.value || '').replace(/\s/g, '');
                const cardType = document.getElementById('cardType')?.value || '';
                const cardHolder = (document.getElementById('cardHolder')?.value || '').trim();
                const expiryMonth = document.getElementById('expiryMonth')?.value || '';
                const expiryYear = document.getElementById('expiryYear')?.value || '';
                const cvv = document.getElementById('cvv')?.value || '';
                const billingAddress = (document.getElementById('billingAddress')?.value || '').trim();
                const makePrimary = !!document.getElementById('makePrimary')?.checked;

                if (!cardType) return subscriptionNotify('Please select a card type');
                if (cardNumberRaw.length < 13 || cardNumberRaw.length > 19) return subscriptionNotify('Please enter a valid card number');
                if (!cardHolder) return subscriptionNotify('Please enter cardholder name');
                if (!expiryMonth || !expiryYear) return subscriptionNotify('Please select expiry date');

                const currentDate = new Date();
                const expiryDate = new Date(parseInt(expiryYear), parseInt(expiryMonth) - 1);
                if (expiryDate < currentDate) return subscriptionNotify('Card has expired');
                if (cvv.length < 3 || cvv.length > 4) return subscriptionNotify('Please enter a valid CVV');
                if (!billingAddress) return subscriptionNotify('Please enter billing address');

                const lastFourDigits = cardNumberRaw.slice(-4);
                submitBtn?.classList.add('loading');
                if (submitBtn) submitBtn.disabled = true;

                try {
                    const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'add_payment_method',
                            payment_method: {
                                card_type: cardType,
                                last_four_digits: lastFourDigits,
                                card_holder_name: cardHolder,
                                expiry_month: expiryMonth,
                                expiry_year: expiryYear,
                                billing_address: billingAddress,
                                is_primary: makePrimary ? 1 : 0
                            }
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        subscriptionNotify('Payment method added successfully');
                        closeAddCardModal();
                        await loadSubscriptionData();
                    } else {
                        subscriptionNotify(data.message || 'Failed to add payment method');
                    }
                } catch (error) {
                    console.error('Error adding payment method:', error);
                    subscriptionNotify('An error occurred. Please try again.');
                } finally {
                    submitBtn?.classList.remove('loading');
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        });

        async function fetchBankAccounts() {
             try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php?action=get_bank_accounts');
                const result = await response.json();
                
                const container = document.querySelector('#payment-tab .settings-section:first-child'); 
                // Clear existing cards but keep title and Add button
                const children = Array.from(container.children);
                children.forEach(child => {
                    if (child.classList.contains('payment-method-card')) {
                        child.remove();
                    }
                });
                
                const addBtn = document.getElementById('addBankAccountBtn');

                if (result.success && result.data.length > 0) {
                    result.data.forEach(acc => {
                        const card = document.createElement('div');
                        card.className = `payment-method-card ${acc.is_primary == 1 ? 'active' : ''}`;
                        card.innerHTML = `
                             <div class="card-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="card-info">
                                    <h4>${acc.bank_name} - ${acc.account_type}</h4>
                                    <p>Account Number: ****${acc.account_number.slice(-4)} • Branch: ${acc.branch_name}</p>
                                </div>
                                ${acc.is_primary == 1 ? '<div class="card-badge"><span class="badge-primary">Primary</span></div>' : ''}
                                <button class="btn-icon" title="Delete" onclick="deleteBankAccount(${acc.bank_id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                        `;
                        container.insertBefore(card, addBtn);
                    });
                }
            } catch (error) {
                console.error('Error fetching bank accounts:', error);
            }
        }
        
        async function saveBankAccount() {
             const data = {
                action: 'save_bank_account',
                bank_id: document.getElementById('saveBankAccountBtn').dataset.id || 0,
                bank_name: document.getElementById('bankName').value,
                branch_name: document.getElementById('branchName').value,
                account_number: document.getElementById('accountNumber').value,
                account_holder_name: document.getElementById('accountHolderName').value,
                account_type: document.getElementById('accountType').value,
                swift_code: document.getElementById('swiftCode').value,
                is_primary: false // Default for now
            };
            
             try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                alert(result.message);
                if (result.success) {
                    document.getElementById('bankAccountForm').style.display = 'none';
                    fetchBankAccounts();
                }
             } catch (error) {
                 console.error('Error saving bank account:', error);
                 alert('Failed to save bank account');
             }
        }

        async function deleteBankAccount(id) {
             if(!confirm('Are you sure?')) return;
             try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete_bank_account', bank_id: id })
                });
                const result = await response.json();
                alert(result.message);
                if (result.success) fetchBankAccounts();
             } catch (error) {
                 console.error('Error deleting bank account:', error);
             }
        }
    </script>
</body>
</html>
