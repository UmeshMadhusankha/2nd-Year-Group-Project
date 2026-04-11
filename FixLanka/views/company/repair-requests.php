<?php
/**
 * Company Repair Requests Page
 * 
 * This page allows companies to:
 * - View available job requests from customers
 * - Submit quotations for job requests
 * - Manage submitted quotations (view, edit, delete)
 * - Track quotation status (pending, accepted, rejected, successful)
 * 
 * Authentication: Requires logged-in company user
 * 
 * @package FixLanka\Views\Company
 * @version 1.0.0
 */

// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');

// Retrieve logged-in user data from session
$userData = getUserData();
$userId = $userData['id'] ?? null;

// Ensure user is authenticated
if (!$userId) {
    die('Error: User not authenticated');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Requests - FixLanka</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/repair-requests.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Expose PHP session data to JavaScript -->
    <script>
        window.CURRENT_USER_ID = <?php echo intval($userId); ?>;
    </script>
</head>

<body>
    <!-- Sidebar Toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Container -->
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <!-- Header Container -->
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <div class="header-main">
                        <div class="title-section">
                            <h1>
                                <i class="fas fa-tools"></i>
                                Repair Requests
                            </h1>
                            <p class="subtitle">
                                Manage customer repair requests - view public opportunities and handle direct
                                requests
                            </p>
                            <div class="breadcrumbs">
                                <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                                <span class="separator">/</span>
                                <span class="current">Repair Requests</span>
                            </div>
                        </div>

                        <!-- Header Stats -->
                        <div class="header-stats">
                            <div class="stat-card">
                                <div class="stat-number" id="public-requests-count">0</div>
                                <div class="stat-label">Public Requests</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="direct-requests-count">0</div>
                                <div class="stat-label">Direct Requests</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="pending-response-count">0</div>
                                <div class="stat-label">Pending Response</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="this-month-count">0</div>
                                <div class="stat-label">This Month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Tab Navigation -->
            <section class="requests-tabs">
                <nav class="tab-nav">
                    <button class="tab-button active" data-tab="public-requests">
                        <i class="fas fa-globe"></i>
                        Public Requests
                        <span class="tab-count">24</span>
                    </button>
                    <button class="tab-button" data-tab="direct-requests">
                        <i class="fas fa-inbox"></i>
                        Direct Requests
                        <span class="tab-count" id="direct-requests-tab-count">0</span>
                    </button>
                    <button class="tab-button" data-tab="logs">
                        <i class="fas fa-history"></i>
                        Request Logs
                        <span class="tab-count">156</span>
                    </button>
                </nav>
            </section>

            <!-- Public Requests Tab -->
            <section id="public-requests" class="tab-content active">
                <!-- Controls -->
                <div class="requests-controls">
                    <div class="controls-row">
                        <div class="filters-group">
                            <div class="filter-item">
                                <label class="filter-label">Service Type</label>
                                <select class="filter-select" id="service-filter">
                                    <option value="">All Services</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="hvac">HVAC</option>
                                    <option value="painting">Painting</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Priority</label>
                                <select class="filter-select" id="priority-filter">
                                    <option value="">All Priorities</option>
                                    <option value="high">High Priority</option>
                                    <option value="low">Low Priority</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Location</label>
                                <select class="filter-select" id="location-filter">
                                    <option value="">All Locations</option>
                                    <option value="colombo">Colombo</option>
                                    <option value="kandy">Kandy</option>
                                    <option value="galle">Galle</option>
                                    <option value="negombo">Negombo</option>
                                </select>
                            </div>
                        </div>

                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Requests Grid -->
                <div class="requests-grid">
                    <!-- Job requests will be loaded dynamically from the database -->
                    <div class="loading-state" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; opacity: 0.5;"></i>
                        <p style="color: var(--text-secondary); margin-top: 1rem;">Loading job requests...</p>
                    </div>
                </div>
            </section>

            <!-- Direct Requests Tab -->
            <section id="direct-requests" class="tab-content">
                <!-- Direct Requests Table -->
                <div class="requests-table-container" id="direct-requests-container">
                    <!-- Empty state - shown when no direct requests -->
                    <div class="empty-state-table" id="direct-requests-empty">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-state-title">No Direct Requests Yet</h3>
                        <p class="empty-state-description">
                            Direct requests from customers who specifically choose your company will appear here.
                            <br>
                            These are high-value opportunities because the customer already knows your work!
                        </p>
                        <div class="empty-state-tips">
                            <h4><i class="fas fa-lightbulb"></i> How to get direct requests:</h4>
                            <ul>
                                <li>Provide excellent service to build your reputation</li>
                                <li>Encourage satisfied customers to request you again</li>
                                <li>Complete your company profile to stand out</li>
                                <li>Respond quickly to public requests to build trust</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Table - shown when direct requests exist -->
                    <table class="requests-table" id="direct-requests-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>Request Details</th>
                                <th>Customer</th>
                                <th>Date Received</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="direct-requests-tbody">
                            <!-- Direct requests will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Logs Tab -->
            <section id="logs" class="tab-content">
                <div class="logs-container">
                    <!-- Successful Requests -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon success">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Successful Contracts</h3>
                                <p>Completed projects with positive outcomes</p>
                            </div>
                            <div class="log-count" id="successful-contracts-count">0</div>
                        </div>
                        <div class="log-items" id="successful-contracts-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No completed projects yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon pending">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Pending Quotations</h3>
                                <p>Submitted quotations awaiting customer response</p>
                            </div>
                            <div class="log-count" id="pending-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="pending-quotations-list">
                            <!-- Pending quotations will be dynamically added here -->
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No pending quotations. Submit a quotation to see it here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Accepted Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Accepted Quotations</h3>
                                <p>Quotations accepted by customers</p>
                            </div>
                            <div class="log-count" id="accepted-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="accepted-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No accepted quotations yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Requests -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon rejected">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Rejected Quotations</h3>
                                <p>Quotations declined by customers</p>
                            </div>
                            <div class="log-count" id="rejected-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="rejected-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No rejected quotations yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Draft Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon draft">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Draft Quotations</h3>
                                <p>Saved quotation drafts for future completion</p>
                            </div>
                            <div class="log-count" id="draft-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="draft-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No draft quotations. Save a quotation as draft to see it here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <?php include __DIR__ . '/partials/repair-requests-modals.php'; ?>

    <!-- JavaScript -->
    <script>
        // Pass PHP session data to JavaScript
        window.CURRENT_USER_ID = <?php echo json_encode($userId); ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/repair-requests-db.js"></script>

    <script>
        // Additional scripts if needed


        // ========================================
        // ENHANCED PRICING CALCULATION SYSTEM
        // ========================================

        class QuotationPricingCalculator {
            constructor() {
                this.initializeEventListeners();
            }

            initializeEventListeners() {
                // Labor pricing method listeners
                document.querySelectorAll('input[name="labor_pricing_method"]').forEach(radio => {
                    radio.addEventListener('change', (e) => this.handleLaborMethodChange(e.target.value));
                });

                // Labor calculation inputs
                const laborUnitPrice = document.getElementById('labor-unit-price');
                const laborCost = document.getElementById('labor-cost');
                const transportCost = document.getElementById('transport-cost');
                const otherCost = document.getElementById('other-cost');

                if (laborCost) laborCost.addEventListener('input', () => this.updateTotal());
                if (transportCost) transportCost.addEventListener('input', () => this.updateTotal());
                if (otherCost) otherCost.addEventListener('input', () => this.updateTotal());

                // Material supply checkbox
                const materialSupplyCheckbox = document.getElementById('vendor-supplies-materials');
                if (materialSupplyCheckbox) {
                    materialSupplyCheckbox.addEventListener('change', (e) => this.toggleMaterialPricing(e.target.checked));
                }

                // Material pricing method listeners
                document.querySelectorAll('input[name="material_pricing_method"]').forEach(radio => {
                    radio.addEventListener('change', (e) => this.handleMaterialMethodChange(e.target.value));
                });

                const materialCost = document.getElementById('material-cost');
                if (materialCost) materialCost.addEventListener('input', () => this.updateTotal());
            }

            // ===== LABOR CALCULATION METHODS =====

            handleLaborMethodChange(method) {
                const unitPricingSection = document.getElementById('labor-unit-pricing');
                const laborCostInput = document.getElementById('labor-cost');
                const unitLabel = document.getElementById('labor-unit-label');
                const qtyLabel = document.getElementById('labor-quantity-label');
                const qtyBreakdownLabel = document.getElementById('labor-breakdown-qty-label');

                // Unit-based pricing is now the only option
                if (unitPricingSection) unitPricingSection.style.display = 'block';
                if (laborCostInput) {
                    laborCostInput.removeAttribute('readonly');
                    laborCostInput.classList.remove('form-input-calculated');
                }

                // Update labels based on method
                const labelConfig = {
                    'hourly': {
                        unitLabel: '(per hour)',
                        qtyLabel: 'Rate / Price of One',
                        breakdownLabel: 'Quantity (Hidden)'
                    },
                    'per_sqm': {
                        unitLabel: '(per m²)',
                        qtyLabel: 'Rate / Price of One',
                        breakdownLabel: 'Quantity (Hidden)'
                    },
                    'per_unit': {
                        unitLabel: '(per unit)',
                        qtyLabel: 'Rate / Price of One',
                        breakdownLabel: 'Quantity (Hidden)'
                    }
                };

                const config = labelConfig[method];
                if (config) {
                    if (unitLabel) unitLabel.textContent = config.unitLabel;
                    if (qtyLabel) qtyLabel.textContent = config.qtyLabel;
                    if (qtyBreakdownLabel) qtyBreakdownLabel.textContent = config.breakdownLabel;
                }

                this.calculateLaborCost();
            }

            calculateLaborCost() {
                this.updateTotal();
            }

            // ===== MATERIAL CALCULATION METHODS =====

            toggleMaterialPricing(isSupplied) {
                const pricingSection = document.getElementById('material-pricing-section');
                const notSuppliedMessage = document.getElementById('material-not-supplied-message');
                const materialCostInput = document.getElementById('material-cost');

                if (isSupplied) {
                    if (pricingSection) pricingSection.style.display = 'block';
                    if (notSuppliedMessage) notSuppliedMessage.style.display = 'none';
                    if (materialCostInput) materialCostInput.required = true;
                } else {
                    if (pricingSection) pricingSection.style.display = 'none';
                    if (notSuppliedMessage) notSuppliedMessage.style.display = 'flex';
                    if (materialCostInput) {
                        materialCostInput.value = '0.00';
                        materialCostInput.required = false;
                    }
                    this.updateTotal();
                }
            }

            handleMaterialMethodChange(method) {
                const unitPricingSection = document.getElementById('material-unit-pricing');
                const materialCostInput = document.getElementById('material-cost');
                const unitLabel = document.getElementById('material-unit-label');
                const qtyLabel = document.getElementById('material-quantity-label');
                const qtyBreakdownLabel = document.getElementById('material-breakdown-qty-label');

                // Unit-based pricing is now the only option
                if (unitPricingSection) unitPricingSection.style.display = 'block';
                if (materialCostInput) {
                    materialCostInput.removeAttribute('readonly');
                    materialCostInput.classList.remove('form-input-calculated');
                }

                // Update labels
                const labelConfig = {
                    'per_sqm': {
                        unitLabel: '(per m²)',
                        qtyLabel: 'Rate / Price of One',
                        breakdownLabel: 'Quantity (Hidden)'
                    },
                    'per_unit': {
                        unitLabel: '(per unit)',
                        qtyLabel: 'Rate / Price of One',
                        breakdownLabel: 'Quantity (Hidden)'
                    }
                };

                const config = labelConfig[method];
                if (config) {
                    if (unitLabel) unitLabel.textContent = config.unitLabel;
                    if (qtyLabel) qtyLabel.textContent = config.qtyLabel;
                    if (qtyBreakdownLabel) qtyBreakdownLabel.textContent = config.breakdownLabel;
                }

                this.calculateMaterialCost();
            }

            calculateMaterialCost() {
                this.updateTotal();
            }

            // ===== TOTAL CALCULATION =====

            updateTotal() {
                const laborCost = parseFloat(document.getElementById('labor-cost')?.value) || 0;
                const materialCost = parseFloat(document.getElementById('material-cost')?.value) || 0;
                const transportCost = parseFloat(document.getElementById('transport-cost')?.value) || 0;
                const otherCost = parseFloat(document.getElementById('other-cost')?.value) || 0;

                const subtotal = laborCost + materialCost + transportCost + otherCost;
                const total = subtotal;

                // Update display
                const subtotalDisplay = document.getElementById('subtotal-amount');
                const totalDisplay = document.getElementById('total-amount');
                const totalInput = document.getElementById('total-price');

                if (subtotalDisplay) subtotalDisplay.textContent = `LKR ${subtotal.toFixed(2)}`;
                if (totalDisplay) totalDisplay.textContent = `LKR ${total.toFixed(2)}`;
                if (totalInput) totalInput.value = total.toFixed(2);
            }
        }

        // Initialize pricing calculator when modal is opened
        let pricingCalculator;
        document.addEventListener('DOMContentLoaded', function() {
            pricingCalculator = new QuotationPricingCalculator();
        });

        // ========================================================================
        // BUSINESS LOGIC: Budget Flexibility Functions
        // ========================================================================

        /**
         * Update budget range display when budget type or total changes
         */
        function updateBudgetDisplay() {
            const budgetType = document.querySelector('input[name="budget_type"]:checked')?.value;
            const totalAmount = parseFloat(document.getElementById('total-amount')?.value) || 0;
            const rangeDisplay = document.getElementById('budget-range-display');
            const rangeText = document.getElementById('budget-range-text');

            if (budgetType === 'flexible' && totalAmount > 0) {
                const minBudget = (totalAmount * 0.90).toFixed(2);
                const maxBudget = (totalAmount * 1.10).toFixed(2);
                
                if (rangeText) {
                    rangeText.textContent = `LKR ${parseFloat(minBudget).toLocaleString()} - LKR ${parseFloat(maxBudget).toLocaleString()}`;
                }
                if (rangeDisplay) {
                    rangeDisplay.style.display = 'block';
                }
            } else {
                if (rangeDisplay) {
                    rangeDisplay.style.display = 'none';
                }
            }
        }

        // Update budget display when total changes
        const totalAmountField = document.getElementById('total-amount');
        if (totalAmountField) {
            // Use MutationObserver to watch for value changes
            const observer = new MutationObserver(updateBudgetDisplay);
            observer.observe(totalAmountField, { attributes: true, attributeFilter: ['value'] });
            
            // Also listen to input events
            totalAmountField.addEventListener('input', updateBudgetDisplay);
        }

        // ========================================================================
        // BUSINESS LOGIC: Payment Method Functions
        // ========================================================================

        /**
         * Update payment method information box
         */
        function updatePaymentMethodInfo() {
            const paymentMethod = document.getElementById('payment-method')?.value;
            const infoBox = document.getElementById('payment-method-info');
            const hourlyRateSection = document.getElementById('hourly-rate-section');
            const spendingCapSection = document.getElementById('spending-cap-section');

            if (!paymentMethod || !infoBox) return;

            const paymentInfo = {
                'milestone': {
                    icon: 'fas fa-tasks',
                    title: 'Milestone-Based Payment',
                    description: 'Payment released in stages as project milestones are completed. Provides security for both parties.',
                    color: '#2196F3'
                },
                'completion': {
                    icon: 'fas fa-check-circle',
                    title: '100% on Completion',
                    description: 'Full payment made only after the project is successfully completed and verified.',
                    color: '#4CAF50'
                },
                'time_material': {
                    icon: 'fas fa-clock',
                    title: 'Time & Material',
                    description: 'Pay based on actual hours worked and materials used. Hourly rate applies.',
                    color: '#F44336'
                }
            };

            const info = paymentInfo[paymentMethod];
            if (info) {
                infoBox.innerHTML = `
                    <div class="info-box" style="border-left-color: ${info.color}">
                        <i class="${info.icon}" style="color: ${info.color}"></i>
                        <div>
                            <strong>${info.title}</strong>
                            <p>${info.description}</p>
                        </div>
                    </div>
                `;
                infoBox.style.display = 'block';
            } else {
                infoBox.style.display = 'none';
            }

            // Show/hide hourly rate and spending cap sections
            const hourlyRateInput = document.getElementById('hourly-rate');
            if (paymentMethod === 'time_material') {
                if (hourlyRateSection) hourlyRateSection.style.display = 'block';
                if (spendingCapSection) spendingCapSection.style.display = 'block';
                // Make hourly rate required when Time & Material is selected
                if (hourlyRateInput) hourlyRateInput.required = true;
            } else {
                if (hourlyRateSection) hourlyRateSection.style.display = 'none';
                if (spendingCapSection) spendingCapSection.style.display = 'none';
                // Remove required attribute when not Time & Material
                if (hourlyRateInput) hourlyRateInput.required = false;
            }
        }
    </script>

    <!-- ============================================================ -->
    <!-- BUSINESS LOGIC: Additional Styles -->
    <!-- ============================================================ -->
    <style>
        .info-box {
            display: flex;
            gap: 12px;
            padding: 15px;
            background: #e8f4f8;
            border-left: 4px solid #2196F3;
            border-radius: 6px;
            margin-top: 10px;
        }

        .info-box i {
            color: #2196F3;
            font-size: 1.5rem;
            margin-top: 2px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .info-box p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .radio-group-inline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .radio-card small {
            display: block;
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
    </style>
</body>

</html>






