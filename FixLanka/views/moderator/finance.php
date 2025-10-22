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
require_once '../../includes/admin-modarator/auth.php';
require_once '../../includes/admin-modarator/mock-data.php';

// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();

$message = '';
$basePath = '';
$currentPath = 'finance';

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="../../assets/css/moderator/finance.css">

            <?php renderPageHeader($basePath, 'Financial Reports', 'Monitor revenue and financial transactions'); ?>

            <main style="margin-top: 5rem;" class="main-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Financial Reports</h2>
                        <p class="text-muted-foreground">Monitor revenue, commissions, and financial transactions</p>
                    </div>

                    <div class="grid gap-4 grid-cols-2">
                        <?php
                        renderCard('Monthly Revenue', 'LKR ' . number_format($mockFinancials['monthlyRevenue'] / 1000000, 1) . 'M', '+15.2% from last month', 'dollar-sign', 'text-green-600');
                        renderCard('Pending Withdrawals', 'LKR ' . number_format($mockFinancials['pendingWithdrawals'] / 1000) . 'K', 'Awaiting processing', 'wallet', 'text-orange-600');
                        renderCard('Total Commissions', 'LKR ' . number_format($mockFinancials['totalCommissions'] / 1000) . 'K', 'This month', 'trending-up', 'text-blue-600');
                        renderCard('Active Subscriptions', $mockFinancials['activeSubscriptions'], 'Premium accounts', 'credit-card', 'text-purple-600');
                        ?>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="revenue-breakdown">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">Revenue Breakdown</h3>
                                <p class="text-sm text-muted-foreground">Monthly revenue by source</p>

                                <div class="mt-6 space-y-4">
                                    <?php
                                    $revenueBreakdown = [
                                        ['source' => 'Service Commissions', 'amount' => 1200000, 'percentage' => 60],
                                        ['source' => 'Advertisement Revenue', 'amount' => 500000, 'percentage' => 25],
                                        ['source' => 'Premium Subscriptions', 'amount' => 200000, 'percentage' => 10],
                                        ['source' => 'Other Sources', 'amount' => 100000, 'percentage' => 5]
                                    ];

                                    foreach ($revenueBreakdown as $item):
                                    ?>
                                        <div class="revenue-item">
                                            <div class="revenue-item-header">
                                                <span class="text-sm font-medium text-foreground"><?php echo $item['source']; ?></span>
                                                <span class="text-sm text-muted-foreground">LKR <?php echo number_format($item['amount'] / 1000); ?>K (<?php echo $item['percentage']; ?>%)</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?php echo $item['percentage']; ?>%"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="commission-structure">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-foreground">Commission Structure</h3>
                                <p class="text-sm text-muted-foreground">Current commission rates by service type</p>

                                <div class="mt-6 space-y-4">
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
                                        <div class="commission-item">
                                            <div>
                                                <p class="text-sm font-medium text-foreground"><?php echo $item['service']; ?></p>
                                                <p class="text-xs text-muted-foreground"><?php echo $item['volume']; ?> active providers</p>
                                            </div>
                                            <?php renderBadge($item['rate'] . '%', 'outline'); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <?php foreach ($mockFinancials['recentTransactions'] as $transaction): ?>
                                        <tr>
                                            <td class="font-medium text-foreground">
                                                <?php echo $transaction['id']; ?>
                                            </td>
                                            <td class="text-muted-foreground">
                                                <div class="transaction-type">
                                                    <?php
                                                    $typeIcons = [
                                                        'Commission' => 'arrow-up-right',
                                                        'Ad Payment' => 'arrow-up-right',
                                                        'Subscription' => 'arrow-up-right',
                                                        'Withdrawal' => 'arrow-down-right'
                                                    ];
                                                    $typeColors = [
                                                        'Commission' => 'text-fixlanka-primary',
                                                        'Ad Payment' => 'text-fixlanka-primary',
                                                        'Subscription' => 'text-fixlanka-primary',
                                                        'Withdrawal' => 'text-fixlanka-error'
                                                    ];
                                                    $icon = $typeIcons[$transaction['type']] ?? 'dollar-sign';
                                                    $color = $typeColors[$transaction['type']] ?? 'text-muted-foreground';
                                                    ?>
                                                    <i data-lucide="<?php echo $icon; ?>" class="h-4 w-4 <?php echo $color; ?>"></i>
                                                    <?php echo $transaction['type']; ?>
                                                </div>
                                            </td>
                                            <td class="text-muted-foreground">
                                                LKR <?php echo number_format($transaction['amount']); ?>
                                            </td>
                                            <td class="text-muted-foreground">
                                                <?php echo $transaction['date']; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusVariants = [
                                                    'Completed' => 'default',
                                                    'Pending' => 'secondary',
                                                    'Failed' => 'destructive'
                                                ];
                                                renderBadge($transaction['status'], $statusVariants[$transaction['status']]);
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <script>
                lucide.createIcons();
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="../../assets/javascript/admin-moderator/common.js"></script>

</body>

</html>
