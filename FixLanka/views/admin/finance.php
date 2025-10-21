<?php
require_once __DIR__ . '/../../../views/other/includes/mock-data.php';
require_once __DIR__ . '/../common/Header.php';
require_once __DIR__ . '/../common/Common.php';
require_once __DIR__ . '/../../../views/other/includes/auth.php';
?>
<link rel="stylesheet" href="<?= $basePath ?>/assets/css/admin & moderator/finance.css">

<?php renderPageHeader($basePath,'Financial Overview', 'Monitor revenue, commissions, and financial transactions'); ?>

<main style="margin-top: 5rem;" class="dashboard-content">
    <div class="space-y-6">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Financial Overview</h2>
            <p class="text-muted-foreground">Monitor revenue, commissions, and financial transactions</p>
        </div>

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

        <div class="finance-content-grid">
            <div class="finance-breakdown-card">
                <div class="finance-breakdown-header">
                    <h3 class="text-lg font-semibold">Revenue Breakdown</h3>
                    <p class="text-muted-foreground text-sm">Monthly revenue by source</p>
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

            <div class="finance-breakdown-card">
                <div class="finance-breakdown-header">
                    <h3 class="text-lg font-semibold">Commission Structure</h3>
                    <p class="text-muted-foreground text-sm">Current commission rates by service type</p>
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

        <div class="finance-transactions-table">
            <div class="finance-transactions-header">
                <h3 class="text-lg font-semibold">Recent Transactions</h3>
                <p class="text-muted-foreground text-sm">Latest financial activities and transactions</p>
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

                            $statusColors = [
                                'Completed' => 'default',
                                'Pending' => 'secondary',
                                'Failed' => 'destructive'
                            ];

                            $icon = $typeIcons[$transaction['type']] ?? 'dollar-sign';
                            $color = $typeColors[$transaction['type']] ?? 'text-muted-foreground';
                            ?>
                            <tr>
                                <td class="text-card-foreground font-medium"><?php echo $transaction['id']; ?></td>
                                <td class="text-muted-foreground">
                                    <div class="finance-transaction-type">
                                        <i data-lucide="<?php echo $icon; ?>" class="h-4 w-4 <?php echo $color; ?>"></i>
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

        <div class="finance-sidebar-grid">
            <div class="finance-sidebar-card">
                <div class="finance-sidebar-header">
                    <h3 class="text-lg font-semibold">Payment Methods</h3>
                    <p class="text-muted-foreground text-sm">Accepted payment options</p>
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

            <div class="finance-sidebar-card">
                <div class="finance-sidebar-header">
                    <h3 class="text-lg font-semibold">Financial Health</h3>
                    <p class="text-muted-foreground text-sm">Key financial indicators</p>
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

            <div class="finance-sidebar-card">
                <div class="finance-sidebar-header">
                    <h3 class="text-lg font-semibold">Quick Actions</h3>
                    <p class="text-muted-foreground text-sm">Financial management tools</p>
                </div>
                <div class="finance-sidebar-content">
                    <div class="finance-quick-actions">
                        <button class="finance-action-btn">Process Withdrawals</button>
                        <button class="finance-action-btn">Generate Report</button>
                        <button class="finance-action-btn">Update Rates</button>
                        <button class="finance-action-btn">View Analytics</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    lucide.createIcons();
</script>