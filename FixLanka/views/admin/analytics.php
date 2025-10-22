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

// // Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("admin", $basePath);
// $user = getCurrentUser();

$basePath = '';
$currentPath = 'analytics';
$message = '';

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

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="../../assets/css/admin/analytics.css">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <?php renderPageHeader($basePath, 'Analytics', 'Comprehensive insights and performance metrics'); ?>
            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Analytics Dashboard</h2>
                        <p class="text-muted-foreground">Comprehensive insights and performance metrics for FixLanka platform</p>
                    </div>

                    <div class="analytics-filters">
                        <div class="analytics-filter-group">
                            <label class="analytics-filter-label">Time Period</label>
                            <select>
                                <option>Last 7 days</option>
                                <option>Last 30 days</option>
                                <option>Last 3 months</option>
                                <option>Last year</option>
                            </select>
                        </div>
                        <div class="analytics-filter-group">
                            <label class="analytics-filter-label">User Type</label>
                            <select>
                                <option>All Users</option>
                                <option>Service Providers</option>
                                <option>Companies</option>
                                <option>Customers</option>
                            </select>
                        </div>
                        <button class="analytics-export-btn">
                            <i data-lucide="download" class="mr-2 h-4 w-4"></i>
                            Export Report
                        </button>
                    </div>

                    <div class="analytics-stats-grid">
                        <?php
                        $stats = [
                            ['title' => 'Total Users', 'value' => number_format($mockAnalytics['totalUsers']), 'description' => '+12% from last month', 'icon' => 'users', 'color' => 'blue'],
                            ['title' => 'Active Sessions', 'value' => '1,247', 'description' => 'Currently online', 'icon' => 'activity', 'color' => 'green'],
                            ['title' => 'Page Views', 'value' => '45.2K', 'description' => 'This month', 'icon' => 'eye', 'color' => 'purple'],
                            ['title' => 'Conversion Rate', 'value' => '3.2%', 'description' => '+0.5% improvement', 'icon' => 'trending-up', 'color' => 'emerald']
                        ];

                        foreach ($stats as $stat) {
                            echo renderStatCard($stat['title'], $stat['value'], $stat['description'], $stat['icon'], $stat['color']);
                        }
                        ?>
                    </div>

                    <div class="analytics-charts-grid">
                        <div class="analytics-chart-card">
                            <div class="analytics-chart-header">
                                <h3 class="text-lg font-semibold">User Growth</h3>
                                <p class="text-muted-foreground text-sm">Monthly user registration trends</p>
                            </div>
                            <div class="analytics-chart-content">
                                <!-- Replaced placeholder with actual canvas for chart -->
                                <canvas id="userGrowthChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <div class="analytics-chart-card">
                            <div class="analytics-chart-header">
                                <h3 class="text-lg font-semibold">Revenue Analytics</h3>
                                <p class="text-muted-foreground text-sm">Monthly revenue and commission breakdown</p>
                            </div>
                            <div class="analytics-chart-content">
                                <!-- Replaced placeholder with actual canvas for chart -->
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="analytics-metrics-grid">
                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">User Engagement</h3>
                                <p class="text-muted-foreground text-sm">Key engagement metrics and trends</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Average Session Duration</span>
                                        <span class="analytics-metric-description">Time spent per session</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-positive">4m 32s</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Bounce Rate</span>
                                        <span class="analytics-metric-description">Single page visits</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-negative">23.4%</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Pages per Session</span>
                                        <span class="analytics-metric-description">Average page views</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-positive">3.7</span>
                                </div>
                            </div>
                        </div>

                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">Service Performance</h3>
                                <p class="text-muted-foreground text-sm">Service provider and booking metrics</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Booking Success Rate</span>
                                        <span class="analytics-metric-description">Completed bookings</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-positive">87.3%</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Average Rating</span>
                                        <span class="analytics-metric-description">Service provider ratings</span>
                                    </div>
                                    <span class="analytics-metric-value">4.6/5</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Response Time</span>
                                        <span class="analytics-metric-description">Average provider response</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-positive">2.3h</span>
                                </div>
                            </div>
                        </div>

                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">Platform Health</h3>
                                <p class="text-muted-foreground text-sm">System performance and reliability</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Uptime</span>
                                        <span class="analytics-metric-description">System availability</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-positive">99.9%</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Load Time</span>
                                        <span class="analytics-metric-description">Average page load</span>
                                    </div>
                                    <span class="analytics-metric-value">1.2s</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <div class="analytics-metric-info">
                                        <span class="analytics-metric-label">Error Rate</span>
                                        <span class="analytics-metric-description">System errors</span>
                                    </div>
                                    <span class="analytics-metric-value analytics-trend-negative">0.1%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <script>
                lucide.createIcons();


                // User Growth Chart
                const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
                const userGrowthChart = new Chart(userGrowthCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'New Users',
                            data: [120, 190, 300, 500, 420, 630, 750, 890, 1200, 1100, 1350, 1500],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Total Users',
                            data: [120, 310, 610, 1110, 1530, 2160, 2910, 3800, 5000, 6100, 7450, 8950],
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgb(156, 163, 175)'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(156, 163, 175)'
                                },
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: 'rgb(156, 163, 175)'
                                },
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            }
                        }
                    }
                });

                // Revenue Analytics Chart
                const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                const revenueChart = new Chart(revenueCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Revenue (LKR)',
                            data: [45000, 52000, 48000, 61000, 55000, 67000, 73000, 69000, 78000, 82000, 89000, 95000],
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1
                        }, {
                            label: 'Commission (LKR)',
                            data: [4500, 5200, 4800, 6100, 5500, 6700, 7300, 6900, 7800, 8200, 8900, 9500],
                            backgroundColor: 'rgba(16, 185, 129, 0.8)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgb(156, 163, 175)'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(156, 163, 175)',
                                    callback: function(value) {
                                        return 'LKR ' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: 'rgb(156, 163, 175)'
                                },
                                grid: {
                                    color: 'rgba(156, 163, 175, 0.1)'
                                }
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="../../assets/javascript/admin-moderator/common.js"></script>
</body>

</html>
