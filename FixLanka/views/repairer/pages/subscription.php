<?php
// Page configuration
$currentPage = 'subscription';
$pageTitle = 'Subscription Plans';
$pageSubtitle = 'Manage your subscription and unlock premium features';
$searchPlaceholder = 'Search requests, repairers, projects...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Plans - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/upgrade.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content-wrapper">
            <div class="main-content">
                <div class="content-wrapper">
                    <!-- Current Plan Section -->
                    <section class="current-plan-section">
                        <div class="current-plan-header">
                            <h3 class="section-title">
                                <i class="fas fa-user"></i>
                                Current Plan
                            </h3>
                        </div>
                        
                        <div class="current-plan-card">
                            <div class="plan-info">
                                <div class="plan-name">Basic Plan</div>
                                <div class="plan-price">Free</div>
                                <div class="plan-description">Perfect for getting started</div>
                            </div>
                            <div class="plan-usage">
                                <div class="usage-item">
                                    <span class="usage-label">Jobs Applied:</span>
                                    <span class="usage-value">8 / 10</span>
                                </div>
                                <div class="usage-item">
                                    <span class="usage-label">Profile Views:</span>
                                    <span class="usage-value">45 this month</span>
                                </div>
                                <div class="usage-item">
                                    <span class="usage-label">Premium Features:</span>
                                    <span class="usage-value">Not Available</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Upgrade Plans Section -->
                    <section class="upgrade-plans-section">
                        <div class="upgrade-header">
                            <h3 class="section-title">
                                <i class="fas fa-crown"></i>
                                Available Subscription Plans
                            </h3>
                            <p class="section-description">Choose the perfect plan to accelerate your repair business growth</p>
                        </div>

                        <div class="plans-grid">
                            <!-- Pro Plan -->
                            <div class="plan-card pro-plan">
                                <div class="plan-badge">Most Popular</div>
                                <div class="plan-header">
                                    <div class="plan-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h4 class="plan-title">Pro Plan</h4>
                                    <div class="plan-pricing">
                                        <span class="plan-price monthly-price">LKR 2,500</span>
                                        <span class="plan-price annual-price">LKR 2,000</span>
                                        <span class="plan-period">/month</span>
                                    </div>
                                    <p class="plan-subtitle">Perfect for active repairers</p>
                                </div>
                                
                                <div class="plan-features">
                                    <ul class="features-list">
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Unlimited job applications</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Priority in search results</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Advanced analytics dashboard</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Customer contact information</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Pro badge on profile</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>24/7 priority support</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Reduced platform fees (12%)</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <button class="btn btn-primary plan-btn" onclick="selectPlan('pro')">
                                    <i class="fas fa-crown"></i>
                                    Upgrade to Pro
                                </button>
                            </div>

                            <!-- Business Plan -->
                            <div class="plan-card business-plan">
                                <div class="plan-header">
                                    <div class="plan-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <h4 class="plan-title">Business Plan</h4>
                                    <div class="plan-pricing">
                                        <span class="plan-price monthly-price">LKR 4,500</span>
                                        <span class="plan-price annual-price">LKR 3,600</span>
                                        <span class="plan-period">/month</span>
                                    </div>
                                    <p class="plan-subtitle">For established repair businesses</p>
                                </div>
                                
                                <div class="plan-features">
                                    <ul class="features-list">
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Everything in Pro Plan</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Team member accounts (up to 5)</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Advanced scheduling tools</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Custom business profile</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Invoice generation & tracking</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Dedicated account manager</span>
                                        </li>
                                        <li class="feature-item">
                                            <i class="fas fa-check"></i>
                                            <span>Lowest platform fees (8%)</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <button class="btn btn-outline plan-btn" onclick="selectPlan('business')">
                                    <i class="fas fa-building"></i>
                                    Upgrade to Business
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Features Comparison Section -->
                    <section class="features-comparison-section">
                        <div class="comparison-header">
                            <h3 class="section-title">
                                <i class="fas fa-balance-scale"></i>
                                Feature Comparison
                            </h3>
                            <p class="section-description">Compare all plans to find what works best for you</p>
                        </div>
                        
                        <div class="comparison-table-wrapper">
                            <table class="comparison-table">
                                <thead>
                                    <tr>
                                        <th class="feature-col">Features</th>
                                        <th class="plan-col">Basic</th>
                                        <th class="plan-col pro-col">Pro</th>
                                        <th class="plan-col">Business</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="feature-name">Job Applications per Month</td>
                                        <td class="plan-value">10</td>
                                        <td class="plan-value">Unlimited</td>
                                        <td class="plan-value">Unlimited</td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Platform Fees</td>
                                        <td class="plan-value">15%</td>
                                        <td class="plan-value">12%</td>
                                        <td class="plan-value">8%</td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Priority in Search</td>
                                        <td class="plan-value"><i class="fas fa-times text-error"></i></td>
                                        <td class="plan-value"><i class="fas fa-check text-success"></i></td>
                                        <td class="plan-value"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Customer Contact Info</td>
                                        <td class="plan-value"><i class="fas fa-times text-error"></i></td>
                                        <td class="plan-value"><i class="fas fa-check text-success"></i></td>
                                        <td class="plan-value"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Analytics Dashboard</td>
                                        <td class="plan-value">Basic</td>
                                        <td class="plan-value">Advanced</td>
                                        <td class="plan-value">Premium</td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Team Members</td>
                                        <td class="plan-value">1</td>
                                        <td class="plan-value">1</td>
                                        <td class="plan-value">5</td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Support Level</td>
                                        <td class="plan-value">Standard</td>
                                        <td class="plan-value">Priority</td>
                                        <td class="plan-value">Dedicated</td>
                                    </tr>
                                    <tr>
                                        <td class="feature-name">Invoice Tools</td>
                                        <td class="plan-value"><i class="fas fa-times text-error"></i></td>
                                        <td class="plan-value"><i class="fas fa-times text-error"></i></td>
                                        <td class="plan-value"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- FAQ Section -->
                    <section class="faq-section">
                        <div class="faq-header">
                            <h3 class="section-title">
                                <i class="fas fa-question-circle"></i>
                                Frequently Asked Questions
                            </h3>
                        </div>
                        
                        <div class="faq-list">
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span>Can I cancel my subscription anytime?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, you can cancel your subscription at any time. Your Pro features will remain active until the end of your current billing period, and then your account will automatically downgrade to the Basic plan.</p>
                                </div>
                            </div>
                            
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span>What payment methods do you accept?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>We accept all major credit cards (Visa, MasterCard, American Express), debit cards, and bank transfers. All payments are processed securely through our encrypted payment gateway.</p>
                                </div>
                            </div>
                            
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span>Do you offer refunds?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>We offer a 30-day money-back guarantee for all new subscriptions. If you're not satisfied with your Pro plan within the first 30 days, contact our support team for a full refund.</p>
                                </div>
                            </div>
                            
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span>Can I upgrade or downgrade my plan?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Yes, you can change your plan at any time. Upgrades take effect immediately, while downgrades will take effect at the end of your current billing cycle. The price difference will be prorated accordingly.</p>
                                </div>
                            </div>
                            
                            <div class="faq-item">
                                <div class="faq-question" onclick="toggleFaq(this)">
                                    <span>How does the annual billing work?</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    <p>Annual billing gives you a 20% discount compared to monthly billing. You'll be charged upfront for the entire year, and your Pro features will be active for 12 months. You can still cancel anytime, but refunds are prorated based on unused months.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Upgrade Confirmation Modal -->
    <div class="modal-overlay" id="upgrade-modal-overlay">
        <div class="upgrade-modal" id="upgrade-modal">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Your Upgrade</h3>
                <button class="modal-close" id="close-upgrade-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="upgrade-summary">
                    <!-- Plan Header -->
                    <div class="modal-plan-header">
                        <div class="plan-icon-large">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h4 class="modal-plan-title" id="modal-plan-name">Pro Plan</h4>
                            <p class="modal-plan-subtitle">Premium subscription</p>
                        </div>
                    </div>

                    <!-- Billing Options -->
                    <div class="billing-options-section">
                        <h5 class="section-label">
                            <i class="fas fa-calendar-alt"></i>
                            Select Billing Period
                        </h5>
                        <div class="billing-options-grid">
                            <div class="billing-option-card active" data-billing="monthly">
                                <div class="option-radio">
                                    <input type="radio" name="billing-period" value="monthly" checked>
                                    <span class="radio-checkmark"></span>
                                </div>
                                <div class="option-details">
                                    <div class="option-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Monthly Billing</div>
                                        <div class="option-price" id="monthly-option-price">LKR 2,500/month</div>
                                    </div>
                                </div>
                            </div>
                            <div class="billing-option-card" data-billing="annual">
                                <div class="option-badge">
                                    <i class="fas fa-tag"></i>
                                    Save 20%
                                </div>
                                <div class="option-radio">
                                    <input type="radio" name="billing-period" value="annual">
                                    <span class="radio-checkmark"></span>
                                </div>
                                <div class="option-details">
                                    <div class="option-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="option-text">
                                        <div class="option-title">Annual Billing</div>
                                        <div class="option-price" id="annual-option-price">LKR 2,000/month</div>
                                        <div class="option-info" id="annual-yearly-total">Billed LKR 24,000/year</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary-section">
                        <h5 class="section-label">
                            <i class="fas fa-file-invoice"></i>
                            Order Summary
                        </h5>
                        <div class="summary-box">
                            <div class="summary-item">
                                <span class="item-label">Plan Selected</span>
                                <span class="item-value" id="summary-plan-name">Pro Plan</span>
                            </div>
                            <div class="summary-item">
                                <span class="item-label">Billing Cycle</span>
                                <span class="item-value" id="summary-billing-cycle">Monthly</span>
                            </div>
                            <div class="summary-item">
                                <span class="item-label">Price per Month</span>
                                <span class="item-value" id="summary-price-per-month">LKR 2,500</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-item summary-total">
                                <span class="item-label">
                                    <i class="fas fa-credit-card"></i>
                                    Total Due Now
                                </span>
                                <span class="item-value total-amount" id="summary-total-amount">LKR 2,500</span>
                            </div>
                        </div>
                    </div>

                    <!-- Included Features -->
                    <div class="features-section">
                        <h5 class="section-label">
                            <i class="fas fa-check-circle"></i>
                            What's Included
                        </h5>
                        <div class="features-list-grid" id="modal-features-list">
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>Unlimited job applications</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>Priority in search results</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>Advanced analytics</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check"></i>
                                <span>24/7 priority support</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancel-upgrade">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button class="btn btn-primary" id="confirm-upgrade">
                    <i class="fas fa-credit-card"></i>
                    Proceed to Payment
                </button>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/upgrade.js"></script>
</body>
</html>

