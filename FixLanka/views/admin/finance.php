<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
require_once __DIR__ . '/../../includes/admin-modarator/mock-data.php';

$basePath = '';
$currentPath = 'finance';
$message = '';

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Financial Overview - FixLanka';
$pageDescription = $description ?? 'Monitor revenue, commissions, and financial transactions';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/finance.css?v=<?php echo time(); ?>">

            <?php renderPageHeader($basePath, 'Financial Overview', 'Monitor revenue, commissions, and financial transactions'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Financial Overview</h2>
                        <p class="text-muted-foreground">Monitor revenue, commissions, and financial transactions</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="finance-stats-grid">
                        <?php
                        $stats = [
                            ['title' => 'Monthly Revenue', 'value' => 'LKR ' . number_format($mockFinancials['monthlyRevenue'] / 1000000, 1) . 'M', 'description' => '+15.2% from last month', 'icon' => 'dollar-sign', 'color' => 'green'],
                            ['title' => 'Pending Withdrawals', 'value' => 'LKR ' . number_format($mockFinancials['pendingWithdrawals'] / 1000) . 'K', 'description' => 'Awaiting processing', 'icon' => 'wallet', 'color' => 'orange'],
                            ['title' => 'Total Commissions', 'value' => 'LKR ' . number_format($mockFinancials['totalCommissions'] / 1000) . 'K', 'description' => 'This month', 'icon' => 'trending-up', 'color' => 'blue'],
                            ['title' => 'Active Subscriptions', 'value' => $mockFinancials['activeSubscriptions'], 'description' => 'Premium accounts', 'icon' => 'credit-card', 'color' => 'purple']
                        ];

                        foreach ($stats as $stat) {
                            echo renderStatCard($stat['title'], $stat['value'], $stat['description'], $stat['icon'], $stat['color']);
                        }
                        ?>
                    </div>

                    <!-- Revenue Breakdown & Commission Structure Grid -->
                    <div class="finance-content-grid">
                        <!-- Revenue Breakdown Card (CLICKABLE) -->
                        <div class="finance-breakdown-card clickable-card" onclick="showRevenueDetailsPopup()">
                            <div class="finance-breakdown-header">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <h3 class="text-lg font-semibold">Revenue Breakdown</h3>
                                        <p class="text-muted-foreground text-sm">Monthly revenue by source</p>
                                    </div>
                                    <i class="fa-solid fa-circle-info h-5 w-5" style="color: #14b8a6;"></i>
                                </div>
                            </div>
                            <div class="finance-breakdown-content">
                                <?php
                                $revenueBreakdown = [
                                    ['source' => 'Service Commissions', 'amount' => 1200000, 'percentage' => 60],
                                    ['source' => 'Advertisement Revenue', 'amount' => 500000, 'percentage' => 25],
                                    ['source' => 'Premium Subscriptions', 'amount' => 200000, 'percentage' => 10],
                                    ['source' => 'Other Sources', 'amount' => 100000, 'percentage' => 5]
                                ];

                                foreach ($revenueBreakdown as $item):
                                ?>
                                    <div class="finance-breakdown-item">
                                        <div class="finance-breakdown-row">
                                            <span class="text-sm font-medium"><?php echo $item['source']; ?></span>
                                            <span class="text-sm text-muted-foreground">LKR <?php echo number_format($item['amount'] / 1000); ?>K (<?php echo $item['percentage']; ?>%)</span>
                                        </div>
                                        <div class="finance-progress-bar">
                                            <div class="finance-progress-fill" style="width: <?php echo $item['percentage']; ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Commission Structure Card (CLICKABLE) -->
                        <div class="finance-breakdown-card clickable-card" onclick="showCommissionDetailsPopup()">
                            <div class="finance-breakdown-header">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <h3 class="text-lg font-semibold">Commission Structure</h3>
                                        <p class="text-muted-foreground text-sm">Current commission rates by service type</p>
                                    </div>
                                    <i class="fa-solid fa-circle-info h-5 w-5" style="color: #14b8a6;"></i>
                                </div>
                            </div>
                            <div class="finance-breakdown-content">
                                <?php
                                $commissionStructure = [
                                    ['service' => 'Plumbing Services', 'rate' => 15, 'volume' => 45],
                                    ['service' => 'Electrical Work', 'rate' => 12, 'volume' => 38],
                                    ['service' => 'Cleaning Services', 'rate' => 10, 'volume' => 52],
                                    ['service' => 'Carpentry', 'rate' => 18, 'volume' => 28],
                                    ['service' => 'General Maintenance', 'rate' => 14, 'volume' => 35]
                                ];

                                foreach ($commissionStructure as $item):
                                ?>
                                    <div class="finance-commission-item">
                                        <div class="finance-commission-info">
                                            <p class="finance-commission-service"><?php echo $item['service']; ?></p>
                                            <p class="finance-commission-volume"><?php echo $item['volume']; ?> active providers</p>
                                        </div>
                                        <?php renderBadge($item['rate'] . '%', 'outline'); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Transactions Table (CLICKABLE ROWS) -->
                    <div class="finance-transactions-table">
                        <div class="finance-transactions-header">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <div>
                                    <h3 class="text-lg font-semibold">Recent Transactions</h3>
                                    <p class="text-muted-foreground text-sm">Latest financial activities and transactions</p>
                                </div>
                                <i class="fa-solid fa-circle-info h-5 w-5" style="color: #14b8a6; cursor: pointer;" onclick="showTransactionHelpPopup()"></i>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="finance-table">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mockFinancials['recentTransactions'] as $transaction): ?>
                                        <?php
                                        // Font Awesome icon mapping for transaction types
                                        $typeIconsFA = [
                                            'Commission' => 'fa-arrow-up-right',
                                            'Ad Payment' => 'fa-arrow-up-right',
                                            'Subscription' => 'fa-arrow-up-right',
                                            'Withdrawal' => 'fa-arrow-down-right'
                                        ];

                                        $typeColors = [
                                            'Commission' => 'text-fixlanka-primary',
                                            'Ad Payment' => 'text-fixlanka-primary',
                                            'Subscription' => 'text-fixlanka-primary',
                                            'Withdrawal' => 'text-fixlanka-error'
                                        ];

                                        $statusColors = [
                                            'Completed' => 'default',
                                            'Pending' => 'secondary',
                                            'Failed' => 'destructive'
                                        ];

                                        $iconFA = $typeIconsFA[$transaction['type']] ?? 'fa-dollar-sign';
                                        $color = $typeColors[$transaction['type']] ?? 'text-muted-foreground';
                                        ?>
                                        <tr class="clickable-row" onclick='showTransactionDetailsPopup(<?php echo json_encode($transaction); ?>)'>
                                            <td class="text-card-foreground font-medium"><?php echo $transaction['id']; ?></td>
                                            <td class="text-muted-foreground">
                                                <div class="finance-transaction-type">
                                                    <i class="fa-solid <?php echo $iconFA; ?> h-4 w-4 <?php echo $color; ?>"></i>
                                                    <?php echo $transaction['type']; ?>
                                                </div>
                                            </td>
                                            <td class="text-muted-foreground">LKR <?php echo number_format($transaction['amount']); ?></td>
                                            <td class="text-muted-foreground"><?php echo $transaction['date']; ?></td>
                                            <td>
                                                <?php renderBadge($transaction['status'], $statusColors[$transaction['status']]); ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sidebar Grid (Payment Methods, Financial Health, Quick Actions) -->
                    <div class="finance-sidebar-grid">
                        <!-- Payment Methods (CLICKABLE) -->
                        <div class="finance-sidebar-card clickable-card" onclick="showPaymentMethodsPopup()">
                            <div class="finance-sidebar-header">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <h3 class="text-lg font-semibold">Payment Methods</h3>
                                        <p class="text-muted-foreground text-sm">Accepted payment options</p>
                                    </div>
                                    <i class="fa-solid fa-circle-info h-5 w-5" style="color: #14b8a6;"></i>
                                </div>
                            </div>
                            <div class="finance-sidebar-content">
                                <?php
                                $paymentMethods = [
                                    ['method' => 'Bank Transfer', 'usage' => 65, 'status' => 'Active'],
                                    ['method' => 'Credit/Debit Cards', 'usage' => 28, 'status' => 'Active'],
                                    ['method' => 'Digital Wallets', 'usage' => 7, 'status' => 'Active']
                                ];

                                foreach ($paymentMethods as $method):
                                ?>
                                    <div class="finance-sidebar-item">
                                        <div class="finance-sidebar-info">
                                            <p class="text-sm font-medium"><?php echo $method['method']; ?></p>
                                            <p class="text-xs text-muted-foreground"><?php echo $method['usage']; ?>% usage</p>
                                        </div>
                                        <?php renderBadge($method['status'], 'outline'); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Financial Health (CLICKABLE) -->
                        <div class="finance-sidebar-card clickable-card" onclick="showFinancialHealthPopup()">
                            <div class="finance-sidebar-header">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <div>
                                        <h3 class="text-lg font-semibold">Financial Health</h3>
                                        <p class="text-muted-foreground text-sm">Key financial indicators</p>
                                    </div>
                                    <i class="fa-solid fa-circle-info h-5 w-5" style="color: #14b8a6;"></i>
                                </div>
                            </div>
                            <div class="finance-sidebar-content">
                                <div class="finance-sidebar-item">
                                    <span class="text-sm">Cash Flow</span>
                                    <?php renderBadge('Positive', 'default'); ?>
                                </div>
                                <div class="finance-sidebar-item">
                                    <span class="text-sm">Payment Processing</span>
                                    <?php renderBadge('Healthy', 'default'); ?>
                                </div>
                                <div class="finance-sidebar-item">
                                    <span class="text-sm">Outstanding Dues</span>
                                    <?php renderBadge('LKR 25K', 'outline'); ?>
                                </div>
                                <div class="finance-sidebar-item">
                                    <span class="text-sm">Monthly Growth</span>
                                    <?php renderBadge('+15.2%', 'default'); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions (WORKING BUTTONS) -->
                        <div class="finance-sidebar-card">
                            <div class="finance-sidebar-header">
                                <h3 class="text-lg font-semibold">Quick Actions</h3>
                                <p class="text-muted-foreground text-sm">Financial management tools</p>
                            </div>
                            <div class="finance-sidebar-content">
                                <div class="finance-quick-actions">
                                    <button class="finance-action-btn" onclick="showProcessWithdrawalsPopup()">
                                        <i class="fa-solid fa-wallet"></i>
                                        Process Withdrawals
                                    </button>
                                    <button class="finance-action-btn" onclick="showGenerateReportPopup()">
                                        <i class="fa-solid fa-file-lines"></i>
                                        Generate Report
                                    </button>
                                    <button class="finance-action-btn" onclick="showUpdateRatesPopup()">
                                        <i class="fa-solid fa-percent"></i>
                                        Update Rates
                                    </button>
                                    <button class="finance-action-btn" onclick="showViewAnalyticsPopup()">
                                        <i class="fa-solid fa-chart-line"></i>
                                        View Analytics
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ========== MODAL POPUPS (All 10 Modals) ========== -->
            
            <!-- 1. Revenue Details Popup -->
            <div id="revenueDetailsModal" class="finance-modal" onclick="handleBackdropClick(event, 'revenueDetailsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-chart-pie"></i>
                            Revenue Breakdown Details
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('revenueDetailsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Service Commissions</span>
                                <span class="info-value">LKR 1,200,000 (60%)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Advertisement Revenue</span>
                                <span class="info-value">LKR 500,000 (25%)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Premium Subscriptions</span>
                                <span class="info-value">LKR 200,000 (10%)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Other Sources</span>
                                <span class="info-value">LKR 100,000 (5%)</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Total Monthly Revenue</span>
                                <span class="info-value">LKR 2,000,000</span>
                            </div>
                        </div>
                        <p class="info-note">Revenue streams are auto-calculated from transaction data. Growth rate: +15.2% from last month.</p>
                    </div>
                </div>
            </div>

            <!-- 2. Commission Details Popup -->
            <div id="commissionDetailsModal" class="finance-modal" onclick="handleBackdropClick(event, 'commissionDetailsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-percent"></i>
                            Commission Structure Details
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('commissionDetailsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Plumbing Services</span>
                                <span class="info-value">15% (45 providers)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Electrical Work</span>
                                <span class="info-value">12% (38 providers)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Cleaning Services</span>
                                <span class="info-value">10% (52 providers)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Carpentry</span>
                                <span class="info-value">18% (28 providers)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">General Maintenance</span>
                                <span class="info-value">14% (35 providers)</span>
                            </div>
                        </div>
                        <p class="info-note">Commission rates are set based on service complexity and market standards. Total active providers: 198</p>
                    </div>
                </div>
            </div>

            <!-- 3. Transaction Details Popup -->
            <div id="transactionDetailsModal" class="finance-modal" onclick="handleBackdropClick(event, 'transactionDetailsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-receipt"></i>
                            Transaction Details
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('transactionDetailsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid" id="transactionDetailsContent">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Transaction Help Popup -->
            <div id="transactionHelpModal" class="finance-modal" onclick="handleBackdropClick(event, 'transactionHelpModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-circle-question"></i>
                            Transaction Types Guide
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('transactionHelpModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Commission</span>
                                <span class="info-value">Platform fee from completed services</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ad Payment</span>
                                <span class="info-value">Revenue from advertisement placements</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Subscription</span>
                                <span class="info-value">Premium account membership fees</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Withdrawal</span>
                                <span class="info-value">Payment processed to service providers</span>
                            </div>
                        </div>
                        <p class="info-note">Click on any transaction row to view detailed information.</p>
                    </div>
                </div>
            </div>

            <!-- 5. Payment Methods Popup -->
            <div id="paymentMethodsModal" class="finance-modal" onclick="handleBackdropClick(event, 'paymentMethodsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-credit-card"></i>
                            Payment Methods Details
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('paymentMethodsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Bank Transfer</span>
                                <span class="info-value">65% usage - Most Popular</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Credit/Debit Cards</span>
                                <span class="info-value">28% usage - Fast Processing</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Digital Wallets</span>
                                <span class="info-value">7% usage - Instant Transfers</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Total Transactions Processed</span>
                                <span class="info-value">1,248 this month</span>
                            </div>
                        </div>
                        <p class="info-note">All payment methods are secure and PCI-DSS compliant. Average processing time: 1-2 business days.</p>
                    </div>
                </div>
            </div>

            <!-- 6. Financial Health Popup -->
            <div id="financialHealthModal" class="finance-modal" onclick="handleBackdropClick(event, 'financialHealthModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-chart-line"></i>
                            Financial Health Report
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('financialHealthModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Cash Flow Status</span>
                                <span class="info-value status-positive">Positive (+LKR 1.2M)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Payment Processing</span>
                                <span class="info-value status-positive">Healthy (98.5% success rate)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Outstanding Dues</span>
                                <span class="info-value">LKR 25,000 (3 pending)</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Monthly Growth</span>
                                <span class="info-value status-positive">+15.2% increase</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Overall Health Score</span>
                                <span class="info-value">Excellent (92/100)</span>
                            </div>
                        </div>
                        <p class="info-note">Financial indicators are calculated from real-time transaction data. Last updated: December 15, 2025</p>
                    </div>
                </div>
            </div>

            <!-- 7. Process Withdrawals Popup -->
            <div id="processWithdrawalsModal" class="finance-modal" onclick="handleBackdropClick(event, 'processWithdrawalsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-wallet"></i>
                            Process Withdrawals
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('processWithdrawalsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Pending Withdrawals</span>
                                <span class="info-value">LKR 235,000</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total Requests</span>
                                <span class="info-value">12 requests</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Processing Time</span>
                                <span class="info-value">1-2 business days</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Available Balance</span>
                                <span class="info-value">LKR 1,850,000</span>
                            </div>
                        </div>
                        <p class="info-note">⚠️ This feature is currently in development. Full withdrawal processing system coming soon!</p>
                        <button class="finance-modal-btn" onclick="closeModalDirectly('processWithdrawalsModal')" type="button">Close</button>
                    </div>
                </div>
            </div>

            <!-- 8. Generate Report Popup -->
            <div id="generateReportModal" class="finance-modal" onclick="handleBackdropClick(event, 'generateReportModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-file-lines"></i>
                            Generate Financial Report
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('generateReportModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Report Type</span>
                                <span class="info-value">Monthly Financial Summary</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Report Period</span>
                                <span class="info-value">December 2025</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Format</span>
                                <span class="info-value">PDF, Excel, CSV</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Estimated Size</span>
                                <span class="info-value">~2.5 MB</span>
                            </div>
                        </div>
                        <p class="info-note">📊 Report generation feature is under development. You'll be able to export comprehensive financial reports soon!</p>
                        <button class="finance-modal-btn" onclick="closeModalDirectly('generateReportModal')" type="button">Close</button>
                    </div>
                </div>
            </div>

            <!-- 9. Update Rates Popup -->
            <div id="updateRatesModal" class="finance-modal" onclick="handleBackdropClick(event, 'updateRatesModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-percent"></i>
                            Update Commission Rates
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('updateRatesModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Current Rate Range</span>
                                <span class="info-value">10% - 18%</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Average Commission</span>
                                <span class="info-value">13.8%</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Updated</span>
                                <span class="info-value">November 15, 2025</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Affected Services</span>
                                <span class="info-value">5 service categories</span>
                            </div>
                        </div>
                        <p class="info-note">⚙️ Commission rate management system is coming soon. This will allow dynamic rate adjustments!</p>
                        <button class="finance-modal-btn" onclick="closeModalDirectly('updateRatesModal')" type="button">Close</button>
                    </div>
                </div>
            </div>

            <!-- 10. View Analytics Popup -->
            <div id="viewAnalyticsModal" class="finance-modal" onclick="handleBackdropClick(event, 'viewAnalyticsModal')">
                <div class="finance-modal-content" onclick="event.stopPropagation()">
                    <div class="finance-modal-header">
                        <h3 class="finance-modal-title">
                            <i class="fa-solid fa-chart-column"></i>
                            Financial Analytics
                        </h3>
                        <button class="finance-modal-close" onclick="closeModalDirectly('viewAnalyticsModal')" type="button">&times;</button>
                    </div>
                    <div class="finance-modal-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Total Revenue (YTD)</span>
                                <span class="info-value">LKR 18.5M</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Monthly Average</span>
                                <span class="info-value">LKR 1.85M</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Best Performing Month</span>
                                <span class="info-value">December 2025</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Growth Trend</span>
                                <span class="info-value status-positive">Upward (+15.2%)</span>
                            </div>
                            <div class="info-item highlight">
                                <span class="info-label">Projection (Next Month)</span>
                                <span class="info-value">LKR 2.3M</span>
                            </div>
                        </div>
                        <p class="info-note">📈 Advanced analytics dashboard with charts and insights is under development!</p>
                        <button class="finance-modal-btn" onclick="closeModalDirectly('viewAnalyticsModal')" type="button">Close</button>
                    </div>
                </div>
            </div>

            <!-- ========== JAVASCRIPT FOR ALL POPUPS ========== -->
            <script>
                // Direct close function - GUARANTEED TO WORK
                function closeModalDirectly(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('active');
                        modal.style.display = 'none';
                    }
                    return false;
                }

                // Handle backdrop click (click outside modal)
                function handleBackdropClick(event, modalId) {
                    if (event.target.classList.contains('finance-modal')) {
                        closeModalDirectly(modalId);
                    }
                }

                // Show modal functions
                function showRevenueDetailsPopup() {
                    const modal = document.getElementById('revenueDetailsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showCommissionDetailsPopup() {
                    const modal = document.getElementById('commissionDetailsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showTransactionDetailsPopup(transaction) {
                    const content = document.getElementById('transactionDetailsContent');
                    content.innerHTML = `
                        <div class="info-item">
                            <span class="info-label">Transaction ID</span>
                            <span class="info-value">${transaction.id}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Type</span>
                            <span class="info-value">${transaction.type}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Amount</span>
                            <span class="info-value">LKR ${transaction.amount.toLocaleString()}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Date</span>
                            <span class="info-value">${transaction.date}</span>
                        </div>
                        <div class="info-item highlight">
                            <span class="info-label">Status</span>
                            <span class="info-value status-${transaction.status.toLowerCase()}">${transaction.status}</span>
                        </div>
                    `;
                    
                    const modal = document.getElementById('transactionDetailsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showTransactionHelpPopup() {
                    const modal = document.getElementById('transactionHelpModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showPaymentMethodsPopup() {
                    const modal = document.getElementById('paymentMethodsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showFinancialHealthPopup() {
                    const modal = document.getElementById('financialHealthModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showProcessWithdrawalsPopup() {
                    const modal = document.getElementById('processWithdrawalsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showGenerateReportPopup() {
                    const modal = document.getElementById('generateReportModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showUpdateRatesPopup() {
                    const modal = document.getElementById('updateRatesModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                function showViewAnalyticsPopup() {
                    const modal = document.getElementById('viewAnalyticsModal');
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('active'), 10);
                }

                // ESC key to close ALL modals
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' || event.key === 'Esc') {
                        const allModalIds = [
                            'revenueDetailsModal',
                            'commissionDetailsModal',
                            'transactionDetailsModal',
                            'transactionHelpModal',
                            'paymentMethodsModal',
                            'financialHealthModal',
                            'processWithdrawalsModal',
                            'generateReportModal',
                            'updateRatesModal',
                            'viewAnalyticsModal'
                        ];
                        
                        allModalIds.forEach(modalId => {
                            closeModalDirectly(modalId);
                        });
                    }
                });
            </script>

        </div>
    </div>
    
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>