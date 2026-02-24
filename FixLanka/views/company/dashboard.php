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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/dashboard.js"></script>
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
                            <p class="kpi-value">12</p>
                            <span class="kpi-trend positive">
                                <i class="fas fa-arrow-up"></i> +2 this week
                            </span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Pending Requests</h3>
                            <p class="kpi-value">8</p>
                            <span class="kpi-trend negative">
                                <i class="fas fa-arrow-down"></i> -3 from yesterday
                            </span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon green">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Total Earnings</h3>
                            <p class="kpi-value">LKR 450K</p>
                            <span class="kpi-trend positive">
                                <i class="fas fa-arrow-up"></i> +15% this month
                            </span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon yellow">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="kpi-content">
                            <h3>Average Rating</h3>
                            <p class="kpi-value">4.8</p>
                            <span class="kpi-trend positive">
                                <i class="fas fa-arrow-up"></i> +0.2 this month
                            </span>
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
                                <div class="request-tabs">
                                    <button class="tab-button active" data-tab="direct"><i class="fas fa-user-tie"></i>
                                        Direct Requests</button>
                                    <button class="tab-button" data-tab="public"><i class="fas fa-globe"></i> Public
                                        Requests</button>
                                </div>
                                <a href="/2nd-Year-Group-Project/FixLanka/company-repair-requests" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                            </div>
                        </div>

                        <div class="requests-list" id="requestsList">
                            <!-- Request cards will be populated by JavaScript -->
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
                            <div class="event-item">
                                <div class="event-date">Aug 27</div>
                                <div class="event-title">Team Meeting</div>
                            </div>
                            <div class="event-item">
                                <div class="event-date">Aug 30</div>
                                <div class="event-title">Project Deadline</div>
                            </div>
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

                        <div class="projects-list">
                            <div class="project-item">
                                <div class="project-info">
                                    <h4>Air Conditioner Repair – Colombo</h4>
                                    <p><strong>Client:</strong> John Perera</p>
                                    <span class="project-date">Due: Aug 25, 2025</span>
                                </div>
                                <div class="project-details">
                                    <div class="project-status in-progress">In Progress</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 60%;"></div>
                                        </div>
                                        <span class="progress-text">60%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="project-item">
                                <div class="project-info">
                                    <h4>Plumbing Fix – Kandy</h4>
                                    <p><strong>Client:</strong> Saman Silva</p>
                                    <span class="project-date">Completed: Aug 10, 2025</span>
                                </div>
                                <div class="project-details">
                                    <div class="project-status completed">Completed</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 100%;"></div>
                                        </div>
                                        <span class="progress-text">100%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="project-item">
                                <div class="project-info">
                                    <h4>Electrical Wiring – Galle</h4>
                                    <p><strong>Client:</strong> Modern Apartments</p>
                                    <span class="project-date">Starts: Sep 1, 2025</span>
                                </div>
                                <div class="project-details">
                                    <div class="project-status pending">Pending</div>
                                    <div class="project-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 0%;"></div>
                                        </div>
                                        <span class="progress-text">0%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contracts Panel -->
                    <div class="contracts-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-handshake"></i> Contracts</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-contracts" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="contracts-list">
                            <div class="contract-item">
                                <div class="contract-info">
                                    <h4>Office Renovation</h4>
                                    <p>ABC Corporation</p>
                                    <span class="contract-date">Started: Aug 15, 2025</span>
                                </div>
                                <div class="contract-details">
                                    <div class="contract-status active">Active</div>
                                    <div class="contract-value">LKR 250K</div>
                                </div>
                            </div>
                            <div class="contract-item">
                                <div class="contract-info">
                                    <h4>Home Repair</h4>
                                    <p>John Smith</p>
                                    <span class="contract-date">Pending: Aug 20, 2025</span>
                                </div>
                                <div class="contract-details">
                                    <div class="contract-status pending">Pending</div>
                                    <div class="contract-value">LKR 75K</div>
                                </div>
                            </div>
                            <div class="contract-item">
                                <div class="contract-info">
                                    <h4>Plumbing Maintenance</h4>
                                    <p>Green Valley Resort</p>
                                    <span class="contract-date">Started: Aug 18, 2025</span>
                                </div>
                                <div class="contract-details">
                                    <div class="contract-status active">Active</div>
                                    <div class="contract-value">LKR 180K</div>
                                </div>
                            </div>
                            <div class="contract-item">
                                <div class="contract-info">
                                    <h4>Electrical Wiring</h4>
                                    <p>Modern Apartments</p>
                                    <span class="contract-date">Starts: Sep 1, 2025</span>
                                </div>
                                <div class="contract-details">
                                    <div class="contract-status upcoming">Upcoming</div>
                                    <div class="contract-value">LKR 320K</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Panel with Income Overview -->
                    <div class="payments-panel">
                        <!-- Recent Payments Section -->
                        <div class="payments-list-section">
                            <div class="section-header">
                                <h3><i class="fas fa-credit-card"></i> Recent Payments</h3>
                                <a href="/2nd-Year-Group-Project/FixLanka/company-payments" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                            </div>
                            <div class="payments-list">
                                <div class="payment-item">
                                    <div class="payment-info">
                                        <h4>Payment Received</h4>
                                        <p>ABC Corporation</p>
                                    </div>
                                    <div class="payment-amount positive">+LKR 125K</div>
                                    <div class="payment-date">Aug 23</div>
                                </div>
                                <div class="payment-item">
                                    <div class="payment-info">
                                        <h4>Material Cost</h4>
                                        <p>Hardware Store</p>
                                    </div>
                                    <div class="payment-amount negative">-LKR 15K</div>
                                    <div class="payment-date">Aug 22</div>
                                </div>
                                <div class="payment-item">
                                    <div class="payment-info">
                                        <h4>Service Payment</h4>
                                        <p>Johnson Residence</p>
                                    </div>
                                    <div class="payment-amount positive">+LKR 85K</div>
                                    <div class="payment-date">Aug 21</div>
                                </div>
                            </div>
                        </div>

                        <!-- Income Overview Section -->
                        <div class="income-overview-section">
                            <div class="section-header">
                                <h3><i class="fas fa-chart-line"></i> Income Overview</h3>
                                <select class="period-selector">
                                    <option>Last 7 Days</option>
                                    <option>Last 30 Days</option>
                                    <option>Last 3 Months</option>
                                </select>
                            </div>

                            <div class="income-chart-container">
                                <div class="income-chart" id="incomeChart">
                                    <div class="chart-bar" data-value="45K" style="height: 60%;"></div>
                                    <div class="chart-bar" data-value="62K" style="height: 80%;"></div>
                                    <div class="chart-bar" data-value="38K" style="height: 50%;"></div>
                                    <div class="chart-bar" data-value="75K" style="height: 100%;"></div>
                                    <div class="chart-bar" data-value="52K" style="height: 70%;"></div>
                                    <div class="chart-bar" data-value="68K" style="height: 90%;"></div>
                                    <div class="chart-bar" data-value="41K" style="height: 55%;"></div>
                                </div>
                                <div class="chart-labels">
                                    <div class="chart-label">Mon</div>
                                    <div class="chart-label">Tue</div>
                                    <div class="chart-label">Wed</div>
                                    <div class="chart-label">Thu</div>
                                    <div class="chart-label">Fri</div>
                                    <div class="chart-label">Sat</div>
                                    <div class="chart-label">Sun</div>
                                </div>
                            </div>

                            <div class="income-summary">
                                <div class="summary-item">
                                    <div class="summary-value">LKR 381K</div>
                                    <div class="summary-label">Total Income</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-value">LKR 54K</div>
                                    <div class="summary-label">Avg Daily</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-value">+18%</div>
                                    <div class="summary-label">Growth</div>
                                </div>
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
                                <input type="text" placeholder="Search workers..." class="search-input">
                                <i class="fas fa-search search-icon"></i>
                            </div>

                            <div class="workforce-filters">
                                <select class="filter-select" aria-label="Filter by worker category">
                                    <option value="">All Categories</option>
                                    <option value="carpenter">Carpenters</option>
                                    <option value="electrician">Electricians</option>
                                    <option value="plumber">Plumbers</option>
                                    <option value="painter">Painters</option>
                                </select>

                                <select class="filter-select" aria-label="Filter by availability status">
                                    <option value="">All Status</option>
                                    <option value="available">Available</option>
                                    <option value="busy">Busy</option>
                                    <option value="offline">Offline</option>
                                </select>

                                <button class="view-all-btn" type="button" aria-describedby="create-post-help">
                                    <i class="fas fa-plus icon-left" aria-hidden="true"></i>
                                    <span class="btn-text">Create Post</span>
                                </button>
                                <div id="create-post-help" class="sr-only">Create a new job posting for workers</div>
                            </div>
                        </div>

                        <!-- Enhanced Workforce Grid -->
                        <div class="workforce-grid">
                            <div class="workforce-item" data-category="carpenter">
                                <div class="workforce-header">
                                    <div class="workforce-icon">
                                        <i class="fas fa-hammer"></i>
                                    </div>
                                    <div class="workforce-title">
                                        <h4>Carpenters</h4>
                                        <div class="workforce-availability" id="carpenters-count">
                                            <span class="available-count">8</span>
                                            <span class="total-count">/ 12 Total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="workforce-progress">
                                    <div class="progress-label">
                                        <span>Availability Ratio</span>
                                        <span class="progress-percentage">67%</span>
                                    </div>
                                    <div class="progress-bar" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100" aria-label="Carpenter availability ratio">
                                        <div class="progress-fill" style="width: 67%"></div>
                                    </div>
                                </div>

                                <div class="workforce-details">
                                    <div class="detail-item">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <span>4.8 Rating</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        <span>Verified</span>
                                    </div>
                                </div>

                                <div class="availability-badge available">Available</div>

                                <div class="workforce-actions" role="group" aria-label="Carpenter workforce actions">
                                    <button class="action-btn primary" type="button" 
                                            aria-describedby="carpenters-count"
                                            onclick="window.location.href='/2nd-Year-Group-Project/FixLanka/company-workforce#carpenters'">
                                        <i class="fas fa-eye icon-left" aria-hidden="true"></i>
                                        <span class="btn-text">View All</span>
                                    </button>
                                </div>
                            </div>

                            <div class="workforce-item" data-category="electrician">
                                <div class="workforce-header">
                                    <div class="workforce-icon">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <div class="workforce-title">
                                        <h4>Electricians</h4>
                                        <div class="workforce-availability" id="electricians-count">
                                            <span class="available-count">5</span>
                                            <span class="total-count">/ 8 Total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="workforce-progress">
                                    <div class="progress-label">
                                        <span>Availability Ratio</span>
                                        <span class="progress-percentage">63%</span>
                                    </div>
                                    <div class="progress-bar" role="progressbar" aria-valuenow="63" aria-valuemin="0" aria-valuemax="100" aria-label="Electrician availability ratio">
                                        <div class="progress-fill" style="width: 63%"></div>
                                    </div>
                                </div>

                                <div class="workforce-details">
                                    <div class="detail-item">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <span>4.9 Rating</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        <span>Verified</span>
                                    </div>
                                </div>

                                <div class="availability-badge available">Available</div>

                                <div class="workforce-actions" role="group" aria-label="Electrician workforce actions">
                                    <button class="action-btn primary" type="button" 
                                            aria-describedby="electricians-count"
                                            onclick="window.location.href='/2nd-Year-Group-Project/FixLanka/company-workforce#electricians'">
                                        <i class="fas fa-eye icon-left" aria-hidden="true"></i>
                                        <span class="btn-text">View All</span>
                                    </button>
                                </div>
                            </div>

                            <div class="workforce-item" data-category="plumber">
                                <div class="workforce-header">
                                    <div class="workforce-icon">
                                        <i class="fas fa-wrench"></i>
                                    </div>
                                    <div class="workforce-title">
                                        <h4>Plumbers</h4>
                                        <div class="workforce-availability" id="plumbers-count">
                                            <span class="available-count">3</span>
                                            <span class="total-count">/ 6 Total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="workforce-progress">
                                    <div class="progress-label">
                                        <span>Availability Ratio</span>
                                        <span class="progress-percentage">50%</span>
                                    </div>
                                    <div class="progress-bar" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" aria-label="Plumber availability ratio">
                                        <div class="progress-fill" style="width: 50%"></div>
                                    </div>
                                </div>

                                <div class="workforce-details">
                                    <div class="detail-item">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <span>4.7 Rating</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        <span>Verified</span>
                                    </div>
                                </div>

                                <div class="availability-badge limited">Limited</div>

                                <div class="workforce-actions" role="group" aria-label="Plumber workforce actions">
                                    <button class="action-btn primary" type="button" 
                                            aria-describedby="plumbers-count"
                                            onclick="window.location.href='/2nd-Year-Group-Project/FixLanka/company-workforce#plumbers'">
                                        <i class="fas fa-eye icon-left" aria-hidden="true"></i>
                                        <span class="btn-text">View All</span>
                                    </button>
                                </div>
                            </div>

                            <div class="workforce-item" data-category="painter">
                                <div class="workforce-header">
                                    <div class="workforce-icon">
                                        <i class="fas fa-paint-brush"></i>
                                    </div>
                                    <div class="workforce-title">
                                        <h4>Painters</h4>
                                        <div class="workforce-availability" id="painters-count">
                                            <span class="available-count">4</span>
                                            <span class="total-count">/ 7 Total</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="workforce-progress">
                                    <div class="progress-label">
                                        <span>Availability Ratio</span>
                                        <span class="progress-percentage">57%</span>
                                    </div>
                                    <div class="progress-bar" role="progressbar" aria-valuenow="57" aria-valuemin="0" aria-valuemax="100" aria-label="Painter availability ratio">
                                        <div class="progress-fill" style="width: 57%"></div>
                                    </div>
                                </div>

                                <div class="workforce-details">
                                    <div class="detail-item">
                                        <i class="fas fa-star" aria-hidden="true"></i>
                                        <span>4.6 Rating</span>
                                    </div>
                                    <div class="detail-item">
                                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                        <span>Verified</span>
                                    </div>
                                </div>

                                <div class="availability-badge available">Available</div>

                                <div class="workforce-actions" role="group" aria-label="Painter workforce actions">
                                    <button class="action-btn primary" type="button" 
                                            aria-describedby="painters-count"
                                            onclick="window.location.href='/2nd-Year-Group-Project/FixLanka/company-workforce#painters'">
                                        <i class="fas fa-eye icon-left" aria-hidden="true"></i>
                                        <span class="btn-text">View All</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div> <!-- Customer Feedback -->
                    <div class="feedback-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-comments"></i> Customer Feedback</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-reviews" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="feedback-list">
                            <div class="feedback-item">
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                                <p>"Excellent work on kitchen repair!"</p>
                                <span class="feedback-customer">- Sarah Johnson</span>
                            </div>
                            <div class="feedback-item">
                                <div class="feedback-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                                <p>"Good service, quick response time"</p>
                                <span class="feedback-customer">- Mike Wilson</span>
                            </div>
                        </div>
                    </div>

                    <!-- Issues & Support -->
                    <div class="support-panel">
                        <div class="panel-header">
                            <h2><i class="fas fa-life-ring"></i> Issues & Support</h2>
                            <a href="/2nd-Year-Group-Project/FixLanka/company-support" class="view-all-btn"><i class="fas fa-eye"></i> View All</a>
                        </div>
                        <div class="support-tickets">
                            <div class="ticket-item high">
                                <div class="ticket-priority">High</div>
                                <div class="ticket-info">
                                    <h4>Payment Issue</h4>
                                    <p>Customer unable to process payment</p>
                                </div>
                                <div class="ticket-status">Open</div>
                            </div>
                            <div class="ticket-item medium">
                                <div class="ticket-priority">Medium</div>
                                <div class="ticket-info">
                                    <h4>Schedule Conflict</h4>
                                    <p>Worker availability issue</p>
                                </div>
                                <div class="ticket-status">In Progress</div>
                            </div>
                        </div>
                    </div>
                </section>
    </div>
    </main>
    </div>

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
