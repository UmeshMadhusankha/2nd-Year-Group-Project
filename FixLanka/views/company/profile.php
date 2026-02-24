<?php
// Start session and check authentication
require_once '../../config/session.php';
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/settings.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/reviews.css">
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
                    <button class="tab-btn" data-tab="password-tab">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
                    </button>
                    <button class="tab-btn" data-tab="password-tab">
                        <i class="fas fa-lock"></i>
                        <span>Change Password</span>
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
                                    <label for="businessType">Business Type</label>
                                    <select id="businessType">
                                        <option value="repair">Repair & Maintenance</option>
                                        <option value="construction">Construction</option>
                                        <option value="electrical">Electrical Services</option>
                                        <option value="plumbing">Plumbing Services</option>
                                        <option value="general">General Contractor</option>
                                    </select>
                                </div>
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

                    <!-- Change Password Tab -->
                    <div class="tab-content" id="password-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Change Password</h2>
                            <p class="section-description">Ensure your account stays secure by using a strong password</p>

                            <div class="form-group">
                                <label for="currentPassword">Current Password *</label>
                                <div class="password-input">
                                    <input type="password" id="currentPassword" placeholder="Enter current password">
                                    <button class="toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="newPassword">New Password *</label>
                                <div class="password-input">
                                    <input type="password" id="newPassword" placeholder="Enter new password">
                                    <button class="toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <span class="strength-text">Password strength: <span id="strengthLevel">-</span></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password *</label>
                                <div class="password-input">
                                    <input type="password" id="confirmPassword" placeholder="Confirm new password">
                                    <button class="toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="password-requirements">
                                <h4>Password Requirements:</h4>
                                <ul>
                                    <li id="req-length"><i class="fas fa-times-circle"></i> At least 8 characters</li>
                                    <li id="req-uppercase"><i class="fas fa-times-circle"></i> One uppercase letter</li>
                                    <li id="req-lowercase"><i class="fas fa-times-circle"></i> One lowercase letter</li>
                                    <li id="req-number"><i class="fas fa-times-circle"></i> One number</li>
                                    <li id="req-special"><i class="fas fa-times-circle"></i> One special character</li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelPasswordBtn">Cancel</button>
                            <button class="btn-primary" id="savePasswordBtn">
                                <i class="fas fa-lock"></i> Change Password
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

        // Password Toggle
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.previousElementSibling;
                const icon = btn.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password Strength Checker
        const newPasswordInput = document.getElementById('newPassword');
        newPasswordInput?.addEventListener('input', (e) => {
            const password = e.target.value;
            let strength = 0;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            // Update requirements checklist
            Object.keys(requirements).forEach(req => {
                const elem = document.getElementById(`req-${req}`);
                if (elem) {
                    if (requirements[req]) {
                        elem.classList.add('met');
                        elem.querySelector('i').classList.remove('fa-times-circle');
                        elem.querySelector('i').classList.add('fa-check-circle');
                        strength++;
                    } else {
                        elem.classList.remove('met');
                        elem.querySelector('i').classList.remove('fa-check-circle');
                        elem.querySelector('i').classList.add('fa-times-circle');
                    }
                }
            });

            // Update strength bar
            const strengthFill = document.querySelector('.strength-fill');
            const strengthLevel = document.getElementById('strengthLevel');
            
            if (password.length === 0) {
                strengthFill.style.width = '0%';
                strengthFill.className = 'strength-fill';
                strengthLevel.textContent = '-';
            } else if (strength <= 2) {
                strengthFill.style.width = '33%';
                strengthFill.className = 'strength-fill weak';
                strengthLevel.textContent = 'Weak';
            } else if (strength <= 4) {
                strengthFill.style.width = '66%';
                strengthFill.className = 'strength-fill medium';
                strengthLevel.textContent = 'Medium';
            } else {
                strengthFill.style.width = '100%';
                strengthFill.className = 'strength-fill strong';
                strengthLevel.textContent = 'Strong';
            }
        });

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
            
            // Change Password
            document.getElementById('savePasswordBtn').addEventListener('click', changePassword);
            
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
                    document.getElementById('businessType').value = data.business_type ? data.business_type.split(',')[0] : ''; // Simple select for now
                    document.getElementById('registrationNumber').value = data.registration_no || '';
                    document.getElementById('taxId').value = data.tax_id || '';
                    document.getElementById('companyDescription').value = data.description || '';
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

        async function saveCompanyProfile() {
            const data = {
                action: 'update_profile',
                name: document.getElementById('companyName').value,
                business_type: document.getElementById('businessType').value,
                registration_no: document.getElementById('registrationNumber').value,
                tax_id: document.getElementById('taxId').value,
                description: document.getElementById('companyDescription').value,
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

        async function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                alert('New passwords do not match');
                return;
            }

            try {
                 const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'change_password',
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });
                const result = await response.json();
                alert(result.message);
                if(result.success) {
                     document.getElementById('currentPassword').value = '';
                     document.getElementById('newPassword').value = '';
                     document.getElementById('confirmPassword').value = '';
                }
            } catch (error) {
                 console.error('Error changing password:', error);
                 alert('Failed to change password');
            }
        }

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
