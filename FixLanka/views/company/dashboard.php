<?php
// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');
$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/repair-requests.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        window.CURRENT_USER_ID = <?php echo intval($userData['id'] ?? 0); ?>;
        window.REPAIR_REQUESTS_WIDGET_CONFIG = {
            enabled: true,
            mode: 'dashboard',
            loadRequests: false,
            loadQuotations: false,
            showCounts: false
        };
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/dashboard.js"></script>
    <script defer src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/repair-requests-db.js"></script>
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
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

            <!-- Combined Content Container -->
            <!-- KPI Cards Row -->
            <section class="kpi-section">
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon blue">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Active Projects</h3>
                            <p class="kpi-value" id="kpiActiveProjects">—</p>
                            <span class="kpi-trend" id="kpiActiveProjectsTrend"></span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Pending Requests</h3>
                            <p class="kpi-value" id="kpiPendingRequests">—</p>
                            <span class="kpi-trend" id="kpiPendingRequestsTrend"></span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon green">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Total Earnings</h3>
                            <p class="kpi-value" id="kpiTotalEarnings">—</p>
                            <span class="kpi-trend" id="kpiTotalEarningsTrend"></span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon yellow">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Average Rating</h3>
                            <p class="kpi-value" id="kpiAverageRating">—</p>
                            <span class="kpi-trend" id="kpiAverageRatingTrend"></span>
                        </div>
                    </div>
                </div>

                <!-- Main Dashboard Grid -->
                <section class="dashboard-grid">
                    <!-- Repair Requests (Main Focus) -->
                    <div class="requests-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-tools"></i> Repair Requests</h2>
                            <div class="panel-header-actions">
                                <a href="/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                            </div>
                        </div>

                        <section class="requests-tabs" style="margin-top: var(--spacing-md);">
                            <nav class="tab-nav">
                                <button class="tab-button active" data-tab="public" type="button">
                                    <i class="fas fa-globe"></i>
                                    Public Requests
                                </button>
                                <button class="tab-button" data-tab="direct" type="button">
                                    <i class="fas fa-inbox"></i>
                                    Direct Requests
                                </button>
                            </nav>
                        </section>

                        <div class="requests-table-container" id="requestsList">
                            <div class="loading-state" style="text-align: center; padding: 2rem;">
                                <i class="fas fa-spinner fa-spin" style="font-size: 1.5rem; opacity: 0.5;"></i>
                                <p style="color: var(--text-secondary); margin-top: 1rem;">Loading requests...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Widget (Right Sidebar) -->
                    <div class="calendar-widget">
                        <div class="calendar-header">
                            <h3><i class="fas fa-calendar-alt"></i> Calendar</h3>
                            <div class="calendar-nav">
                                <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                                <span id="currentMonth">August 2025</span>
                                <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                            </div>
                        </div>

                        <div class="calendar-grid">
                            <div class="calendar-days">
                                <div class="day-header">S</div>
                                <div class="day-header">M</div>
                                <div class="day-header">T</div>
                                <div class="day-header">W</div>
                                <div class="day-header">T</div>
                                <div class="day-header">F</div>
                                <div class="day-header">S</div>
                            </div>
                            <div class="calendar-dates" id="calendarDates">
                                <!-- Calendar dates will be generated by JavaScript -->
                            </div>
                        </div>

                        <div class="upcoming-events">
                            <h4><i class="fas fa-clock"></i> Upcoming Events</h4>
                            <div id="upcomingEventsList"></div>
                        </div>
                    </div>
                    <!-- Project Overview Section -->
                    <div class="projects-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-project-diagram"></i> Project Overview</h2>
                            <div class="project-controls">
                                <select class="project-filter">
                                    <option value="all">All Projects</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="completed">Completed</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <button class="view-all-btn" id="dashboardNewProjectBtn" onclick="showProjectStartOptions()"><i class="fas fa-plus"></i> New Project</button>
                            </div>
                        </div>

                        <div class="projects-list" id="dashboardProjectsList"></div>
                    </div>

                    <!-- Contracts Panel -->
                    <div class="contracts-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-handshake"></i> Contracts</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-contracts" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="contracts-list" id="dashboardContractsList"></div>
                    </div>

                    <div class="payments-panel">
                        <!-- Recent Payments Section -->
                        <div class="payments-list-section">
                            <div class="section-header">
                                <h3><i class="fas fa-credit-card"></i> Recent Payments</h3>
                                <a href="/2nd-Year-Group-Project/FixLanka/company-payments" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                            </div>
                            <div class="table-container">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Project</th>
                                            <th>Detail</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashboardPaymentsList">
                                        <!-- Populated by JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Workforce Overview -->
                    <div class="workforce-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-users-cog"></i> Workforce Overview</h2>
                        </div>

                        <!-- Workforce Controls -->
                        <div class="workforce-controls">
                            <div class="workforce-search">
                                <input type="text" placeholder="Search workers..." class="search-input" id="workforceSearch">
                                <i class="fas fa-search search-icon"></i>
                            </div>

                            <div class="workforce-filters">
                                <select class="filter-select" aria-label="Filter by worker category" id="workforceCategoryFilter">
                                    <option value="">All Categories</option>
                                </select>

                                <select class="filter-select" aria-label="Filter by availability status" id="workforceStatusFilter">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="offline">Offline</option>
                                </select>
                            </div>
                        </div>

                        <!-- Enhanced Workforce Grid -->
                        <div class="workforce-grid" id="workforceGrid"></div>
                    </div> <!-- Customer Feedback -->
                    <div class="feedback-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-comments"></i> Customer Feedback</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-reviews" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="feedback-list" id="dashboardFeedbackList"></div>
                    </div>

                    <!-- Issues & Support -->
                    <div class="support-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-life-ring"></i> Issues & Support</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-support" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="support-tickets" id="dashboardSupportTickets"></div>
                    </div>
                </section>
    </div>
    </main>
    </div>

    <?php include __DIR__ . '/partials/repair-requests-modals.php'; ?>

    <!-- Project Start Options Modal -->
    <div class="project-start-modal" id="projectStartModal">
        <div class="modal-overlay-blur" onclick="closeProjectStartModal()"></div>
        <div class="project-start-content">
            <button class="modal-close-btn" onclick="closeProjectStartModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-header-section">
                <div class="modal-icon-wrapper">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2>Start a New Project</h2>
                <p class="modal-subtitle">Choose the best workflow for your project needs</p>
            </div>

            <div class="project-options-grid">
                <!-- Option 1: Browse Repair Requests -->
                <div class="project-option-card">
                    <div class="option-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Browse Repair Requests</h3>
                    <p>Start from customer repair requests and follow the complete workflow</p>
                    
                    <div class="option-steps">
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Browse available repair requests</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Accept a repair request</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Create and sign contract</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Initialize project</span>
                        </div>
                    </div>

                    <button class="option-action-btn primary" onclick="navigateToRepairRequests()">
                        <i class="fas fa-arrow-right"></i>
                        Go to Repair Requests
                    </button>
                </div>

                <!-- Option 2: From Signed Contract -->
                <div class="project-option-card">
                    <div class="option-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>From Signed Contract</h3>
                    <p>Initialize a project from an already signed contract</p>
                    
                    <div class="option-steps">
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>View signed contracts</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Select a contract</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Verify contract terms</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Start project execution</span>
                        </div>
                    </div>

                    <button class="option-action-btn secondary" onclick="navigateToContracts()">
                        <i class="fas fa-arrow-right"></i>
                        Go to Contracts
                    </button>
                </div>
            </div>

            <div class="modal-footer-note">
                <i class="fas fa-info-circle"></i>
                <p>
                    <strong>Note:</strong> Projects must originate from either customer repair requests or signed contracts to ensure proper documentation and workflow compliance.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Project Start Modal Functions
        function showProjectStartOptions() {
            const modal = document.getElementById('projectStartModal');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeProjectStartModal() {
            const modal = document.getElementById('projectStartModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateToRepairRequests() {
            showNavigationMessage('Redirecting to Repair Requests...', 'info');
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/company-repair-requests';
            }, 500);
        }

        function navigateToContracts() {
            showNavigationMessage('Redirecting to Contracts...', 'info');
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/company-contracts';
            }, 500);
        }

        function showNavigationMessage(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                border-radius: var(--border-radius-lg);
                box-shadow: 0 8px 24px rgba(10, 186, 181, 0.3);
                z-index: 10001;
                font-weight: 600;
                animation: slideInRight 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProjectStartModal();
            }
        });
    </script>
</body>

</html>
