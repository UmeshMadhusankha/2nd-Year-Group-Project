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
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/analytics.css">
            <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
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
                            <i class="fa-solid fa-download mr-2 h-4 w-4"></i>
                            Export Report
                        </button>
                    </div>

                    <div class="analytics-stats-grid">
                        <?php
                        $stats = [
                            ['title' => 'Total Users', 'value' => number_format($mockAnalytics['totalUsers']), 'description' => '+12% from last month', 'icon' => 'fa-users', 'color' => 'blue'],
                            ['title' => 'Active Sessions', 'value' => '1,247', 'description' => 'Currently online', 'icon' => 'fa-chart-line', 'color' => 'green'],
                            ['title' => 'Page Views', 'value' => '45.2K', 'description' => 'This month', 'icon' => 'fa-eye', 'color' => 'purple'],
                            ['title' => 'Conversion Rate', 'value' => '3.2%', 'description' => '+0.5% improvement', 'icon' => 'fa-arrow-trend-up', 'color' => 'emerald']
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
                                <canvas id="userGrowthChart" width="400" height="200"></canvas>
                            </div>
                        </div>

                        <div class="analytics-chart-card">
                            <div class="analytics-chart-header">
                                <h3 class="text-lg font-semibold">Revenue Analytics</h3>
                                <p class="text-muted-foreground text-sm">Monthly revenue and commission breakdown</p>
                            </div>
                            <div class="analytics-chart-content">
                                <canvas id="revenueChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="analytics-metrics-grid">
                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">Top Service Categories</h3>
                                <p class="text-muted-foreground text-sm">Most popular service types</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Plumbing</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-primary" style="width: 85%"></div>
                                    </div>
                                    <span class="text-muted-foreground">850 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Electrical</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-highlight" style="width: 75%"></div>
                                    </div>
                                    <span class="text-muted-foreground">750 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Carpentry</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-accent" style="width: 60%"></div>
                                    </div>
                                    <span class="text-muted-foreground">600 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Painting</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-error" style="width: 45%"></div>
                                    </div>
                                    <span class="text-muted-foreground">450 requests</span>
                                </div>
                            </div>
                        </div>

                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">Top Rated Providers</h3>
                                <p class="text-muted-foreground text-sm">Highest performing service providers</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">John's Plumbing Services</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-primary" style="width: 95%"></div>
                                    </div>
                                    <span class="text-muted-foreground">4.8/5.0 (120 reviews)</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Electric Fix Pro</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-highlight" style="width: 92%"></div>
                                    </div>
                                    <span class="text-muted-foreground">4.6/5.0 (98 reviews)</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Master Carpenters Ltd</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-accent" style="width: 88%"></div>
                                    </div>
                                    <span class="text-muted-foreground">4.4/5.0 (85 reviews)</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Quick House Repairs</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-error" style="width: 85%"></div>
                                    </div>
                                    <span class="text-muted-foreground">4.2/5.0 (72 reviews)</span>
                                </div>
                            </div>
                        </div>

                        <div class="analytics-metrics-card">
                            <div class="analytics-metrics-header">
                                <h3 class="text-lg font-semibold">Popular Districts</h3>
                                <p class="text-muted-foreground text-sm">Most active service areas</p>
                            </div>
                            <div class="analytics-metrics-content">
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Colombo</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-primary" style="width: 90%"></div>
                                    </div>
                                    <span class="text-muted-foreground">1,200 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Gampaha</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-highlight" style="width: 70%"></div>
                                    </div>
                                    <span class="text-muted-foreground">800 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Kandy</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-accent" style="width: 55%"></div>
                                    </div>
                                    <span class="text-muted-foreground">600 requests</span>
                                </div>
                                <div class="analytics-metric-item">
                                    <span class="text-card-foreground">Negombo</span>
                                    <div class="analytics-progress-bar">
                                        <div class="analytics-progress-fill bg-fixlanka-error" style="width: 40%"></div>
                                    </div>
                                    <span class="text-muted-foreground">450 requests</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <script>
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
                                position: 'top',
                                labels: {
                                    usePointStyle: true
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
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
                                position: 'top',
                                labels: {
                                    usePointStyle: true
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'LKR ' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            </script>
        </div>
    </div>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>