<?php
/**
 * Financial Reports View - Clean Minimal Design
 * Matches Advertisement Review Page Style
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';

// Database connection
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize Controller
require_once __DIR__ . '/../../controllers/FinancialReportController.php';
$controller = new FinancialReportController($conn);

// Get all financial data
$financialData = $controller->getFinancialData();
$revenueGrowth = $controller->getRevenueGrowth();

// Extract data for easy access
$monthlyRevenue = $financialData['monthlyRevenue'];
$pendingWithdrawals = $financialData['pendingWithdrawals'];
$totalCommissions = $financialData['totalCommissions'];
$activeSubscriptions = $financialData['activeSubscriptions'];
$revenueBreakdown = $financialData['revenueBreakdown'];
$commissionStructure = $financialData['commissionStructure'];
$recentTransactions = $financialData['recentTransactions'];

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-finance';
$pageTitle = 'Financial Reports - FixLanka';
$pageDescription = 'Monitor revenue and financial transactions';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/finance.css?v=<?php echo time(); ?>">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Financial Reports', 'Monitor revenue and financial transactions'); ?>

            <main style="margin-top: 5rem;" class="main-content">
                <div class="space-y-6">
                    <!-- Page Title -->
                    <div>
                        <h2 class="text-3xl">Financial Reports</h2>
                        <p class="text-muted-foreground">Monitor revenue, commissions, and financial transactions</p>
                    </div>

                    <!-- Clean Stat Cards (Like Advertisement Review) -->
                    <div class="grid gap-4 grid-cols-4">
                        <!-- Monthly Revenue Card -->
                        <div class="stat-card" data-color="primary">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Monthly Revenue</h4>
                                    <div class="stat-value"><?php echo $controller->formatCurrency($monthlyRevenue); ?></div>
                                    <div class="stat-change">
                                        <span class="stat-badge">
                                            <?php echo ($revenueGrowth > 0 ? '↑ ' : '↓ '); ?>
                                            <?php echo abs($revenueGrowth); ?>%
                                        </span>
                                        from last month
                                    </div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-arrow-trend-up"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Withdrawals Card -->
                        <div class="stat-card" data-color="warning">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Pending Withdrawals</h4>
                                    <div class="stat-value"><?php echo $controller->formatCurrency($pendingWithdrawals); ?></div>
                                    <div class="stat-change">Awaiting processing</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Total Commissions Card -->
                        <div class="stat-card" data-color="info">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Total Commissions</h4>
                                    <div class="stat-value"><?php echo $controller->formatCurrency($totalCommissions); ?></div>
                                    <div class="stat-change">This month</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Subscriptions Card -->
                        <div class="stat-card" data-color="purple">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Active Subscriptions</h4>
                                    <div class="stat-value"><?php echo number_format($activeSubscriptions); ?></div>
                                    <div class="stat-change">Premium accounts</div>
                                </div>
                                <div class="stat-icon">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue & Commission Section -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Revenue Breakdown -->
                        <div class="revenue-breakdown">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fa-solid fa-chart-bar" style="width: 20px; height: 20px;"></i>
                                    Revenue Breakdown
                                </h3>
                                <p class="section-subtitle">Monthly revenue by source</p>
                            </div>

                            <div class="section-body">
                                <?php foreach ($revenueBreakdown as $item): ?>
                                    <div class="revenue-item">
                                        <div class="revenue-item-header">
                                            <span class="revenue-item-label"><?php echo htmlspecialchars($item['source']); ?></span>
                                            <span class="revenue-item-value">
                                                <?php 
                                                if ($item['amount'] >= 1000) {
                                                    echo 'LKR ' . number_format($item['amount'] / 1000) . 'K';
                                                } else {
                                                    echo 'LKR ' . number_format($item['amount']);
                                                }
                                                ?>
                                                (<?php echo $item['percentage']; ?>%)
                                            </span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $item['percentage']; ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Commission Structure -->
                        <div class="commission-structure">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fa-solid fa-gear" style="width: 20px; height: 20px;"></i>
                                    Commission Structure
                                </h3>
                                <p class="section-subtitle">Current commission rates by service type</p>
                            </div>

                            <div class="section-body">
                                <?php if (empty($commissionStructure)): ?>
                                    <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No commission data available</p>
                                <?php else: ?>
                                    <div class="commission-list">
                                        <?php foreach ($commissionStructure as $item): ?>
                                            <div class="commission-item">
                                                <div class="commission-info">
                                                    <p class="commission-name"><?php echo htmlspecialchars($item['service']); ?></p>
                                                    <p class="commission-volume"><?php echo $item['volume']; ?> active providers</p>
                                                </div>
                                                <span class="commission-rate"><?php echo $item['rate']; ?>%</span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Transactions Table -->
                    <div class="transactions-table">
                        <div class="section-header">
                            <h3 class="section-title">
                                <i class="fa-solid fa-list" style="width: 20px; height: 20px;"></i>
                                Recent Transactions
                            </h3>
                            <p class="section-subtitle">Latest financial activities and transactions</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table">
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
                                    <?php if (empty($recentTransactions)): ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                                No transactions found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $transactionIndex = 0;
                                        foreach ($recentTransactions as $transaction): 
                                        ?>
                                            <tr>
                                                <td class="font-medium">
                                                    <?php echo htmlspecialchars($transaction['id']); ?>
                                                </td>
                                                <td>
                                                    <div class="transaction-type">
                                                        <?php
                                                        $typeIcons = [
                                                            'Commission' => 'fa-arrow-up-right',
                                                            'Ad Payment' => 'fa-arrow-up-right',
                                                            'Payment' => 'fa-arrow-up-right',
                                                            'Withdrawal' => 'fa-arrow-down-right'
                                                        ];
                                                        $typeColors = [
                                                            'Commission' => 'text-fixlanka-primary',
                                                            'Ad Payment' => 'text-fixlanka-primary',
                                                            'Payment' => 'text-fixlanka-primary',
                                                            'Withdrawal' => 'text-fixlanka-error'
                                                        ];
                                                        $icon = $typeIcons[$transaction['type']] ?? 'fa-dollar-sign';
                                                        $color = $typeColors[$transaction['type']] ?? '';
                                                        ?>
                                                        <i class="fa-solid <?php echo $icon; ?> <?php echo $color; ?>"></i>
                                                        <?php echo htmlspecialchars($transaction['type']); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    LKR <?php echo number_format($transaction['amount']); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($transaction['date']); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statusClass = 'badge badge-status';
                                                    if ($transaction['status'] == 'Completed') {
                                                        $statusClass .= ' badge-completed';
                                                    } elseif ($transaction['status'] == 'Pending') {
                                                        $statusClass .= ' badge-pending';
                                                    } else {
                                                        $statusClass .= ' badge-failed';
                                                    }
                                                    ?>
                                                    <span 
                                                        class="<?php echo $statusClass; ?>" 
                                                        onclick="showTransactionDetails(<?php echo $transactionIndex; ?>)"
                                                        title="Click to view details"
                                                    >
                                                        <?php echo htmlspecialchars($transaction['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php 
                                        $transactionIndex++;
                                        endforeach; 
                                        ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Clean Transaction Details Modal -->
            <div id="transactionModal" class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">Transaction Details</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="modalContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-close-modal" onclick="closeModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Transaction data from PHP
        const transactions = <?php echo json_encode($recentTransactions); ?>;

        // Show transaction details
        function showTransactionDetails(index) {
            const transaction = transactions[index];
            if (!transaction) {
                console.error('Transaction not found at index:', index);
                return;
            }

            const statusClass = {
                'Completed': 'status-completed',
                'Pending': 'status-pending',
                'Failed': 'status-failed'
            };

            const content = `
                <div class="detail-row">
                    <span class="detail-label">Transaction ID</span>
                    <span class="detail-value">${transaction.id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Type</span>
                    <span class="detail-value">${transaction.type}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount</span>
                    <span class="detail-value">LKR ${transaction.amount.toLocaleString()}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value">${transaction.date}</span>
                </div>
                <div class="detail-row ${statusClass[transaction.status] || ''}">
                    <span class="detail-label">Status</span>
                    <span class="detail-value">${transaction.status}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">${transaction.type === 'Withdrawal' ? 'Bank Transfer' : 'Platform Credit'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reference Number</span>
                    <span class="detail-value">REF-${transaction.id.replace('TXN-P', '').replace('TXN-A', '')}</span>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('transactionModal').classList.add('show');
        }

        // Close modal
        function closeModal() {
            document.getElementById('transactionModal').classList.remove('show');
        }

        // Close modal on outside click
        document.getElementById('transactionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('transactionModal');
                if (modal.classList.contains('show')) {
                    closeModal();
                }
            }
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>