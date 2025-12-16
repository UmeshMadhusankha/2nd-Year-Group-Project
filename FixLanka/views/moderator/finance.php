<?php
/**
 * Financial Reports View
 * Displays financial data using MVC pattern
 * Pure MVC - No inline CSS, proper separation of concerns
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
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Financial Reports</h2>
                        <p class="text-muted-foreground">Monitor revenue, commissions, and financial transactions</p>
                    </div>

                    <!-- KPI Cards -->
                    <div class="grid gap-4 grid-cols-2">
                        <?php
                        $growthText = ($revenueGrowth > 0 ? '+' : '') . $revenueGrowth . '% from last month';
                        renderCard('Monthly Revenue', $controller->formatCurrency($monthlyRevenue), $growthText, 'dollar-sign', 'text-green-600');
                        renderCard('Pending Withdrawals', $controller->formatCurrency($pendingWithdrawals), 'Awaiting processing', 'wallet', 'text-orange-600');
                        renderCard('Total Commissions', $controller->formatCurrency($totalCommissions), 'This month', 'trending-up', 'text-blue-600');
                        renderCard('Active Subscriptions', $activeSubscriptions, 'Premium accounts', 'credit-card', 'text-purple-600');
                        ?>
                    </div>

                    <!-- Revenue & Commission Section -->
                    <div class="grid gap-6 md:grid-cols-2">
                        <!-- Revenue Breakdown -->
                        <div class="revenue-breakdown">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">Revenue Breakdown</h3>
                                <p class="text-sm text-muted-foreground">Monthly revenue by source</p>

                                <div class="mt-6 space-y-4">
                                    <?php foreach ($revenueBreakdown as $item): ?>
                                        <div class="revenue-item">
                                            <div class="revenue-item-header">
                                                <span class="text-sm font-medium text-foreground"><?php echo htmlspecialchars($item['source']); ?></span>
                                                <span class="text-sm text-muted-foreground">
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
                        </div>

                        <!-- Commission Structure -->
                        <div class="commission-structure">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">Commission Structure</h3>
                                <p class="text-sm text-muted-foreground">Current commission rates by service type</p>

                                <div class="mt-6 space-y-4">
                                    <?php if (empty($commissionStructure)): ?>
                                        <p class="text-sm text-muted-foreground">No commission data available</p>
                                    <?php else: ?>
                                        <?php foreach ($commissionStructure as $item): ?>
                                            <div class="commission-item">
                                                <div>
                                                    <p class="text-sm font-medium text-foreground"><?php echo htmlspecialchars($item['service']); ?></p>
                                                    <p class="text-xs text-muted-foreground"><?php echo $item['volume']; ?> active providers</p>
                                                </div>
                                                <?php renderBadge($item['rate'] . '%', 'outline'); ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transactions Table -->
                    <div class="transactions-table">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground">Recent Transactions</h3>
                            <p class="text-sm text-muted-foreground">Latest financial activities and transactions</p>
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
                                            <td colspan="5" class="text-center py-4">No transactions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $transactionIndex = 0;
                                        foreach ($recentTransactions as $transaction): 
                                        ?>
                                            <tr>
                                                <td class="font-medium text-foreground">
                                                    <?php echo htmlspecialchars($transaction['id']); ?>
                                                </td>
                                                <td class="text-muted-foreground">
                                                    <div class="transaction-type">
                                                        <?php
                                                        $typeIcons = [
                                                            'Commission' => 'arrow-up-right',
                                                            'Ad Payment' => 'arrow-up-right',
                                                            'Payment' => 'arrow-up-right',
                                                            'Withdrawal' => 'arrow-down-right'
                                                        ];
                                                        $typeColors = [
                                                            'Commission' => 'text-fixlanka-primary',
                                                            'Ad Payment' => 'text-fixlanka-primary',
                                                            'Payment' => 'text-fixlanka-primary',
                                                            'Withdrawal' => 'text-fixlanka-error'
                                                        ];
                                                        $icon = $typeIcons[$transaction['type']] ?? 'dollar-sign';
                                                        $color = $typeColors[$transaction['type']] ?? 'text-muted-foreground';
                                                        ?>
                                                        <i data-lucide="<?php echo $icon; ?>" class="h-4 w-4 <?php echo $color; ?>"></i>
                                                        <?php echo htmlspecialchars($transaction['type']); ?>
                                                    </div>
                                                </td>
                                                <td class="text-muted-foreground">
                                                    LKR <?php echo number_format($transaction['amount']); ?>
                                                </td>
                                                <td class="text-muted-foreground">
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

            <!-- Transaction Details Modal -->
            <div id="transactionModal" class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">Transaction Details</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i data-lucide="x"></i>
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
        
        console.log('Transactions loaded:', transactions);

        // Show transaction details
        function showTransactionDetails(index) {
            console.log('Opening modal for transaction index:', index);
            
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
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value">${transaction.id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Type:</span>
                    <span class="detail-value">${transaction.type}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">LKR ${transaction.amount.toLocaleString()}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date:</span>
                    <span class="detail-value">${transaction.date}</span>
                </div>
                <div class="detail-row ${statusClass[transaction.status] || ''}">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">${transaction.status}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method:</span>
                    <span class="detail-value">${transaction.type === 'Withdrawal' ? 'Bank Transfer' : 'Platform Credit'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reference Number:</span>
                    <span class="detail-value">REF-${transaction.id.replace('TXN-P', '').replace('TXN-A', '')}</span>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('transactionModal').classList.add('show');
            lucide.createIcons();
            
            console.log('Modal opened successfully');
        }

        // Close modal
        function closeModal() {
            console.log('Closing modal');
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

        // Initialize lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            console.log('Page loaded, icons initialized');
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>