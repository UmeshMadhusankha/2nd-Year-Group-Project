<?php
// Page configuration
$currentPage = 'my-jobs';
$pageTitle = 'My Jobs';
$pageSubtitle = 'Manage your current and completed repair tasks';
$searchPlaceholder = 'Search jobs, customers, locations...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Jobs - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/my-jobs.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>
            
        </aside>

        <!-- Main Content -->
        <div class="main-content-wrapper">
            <main class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header-section">
                        <div class="page-header">
                            <div class="page-header-text">
                                <h1 class="page-header-title">My Jobs</h1>
                                <p class="page-header-subtitle">Manage your current and completed repair tasks</p>
                            </div>
                            <div class="page-header-stats">
                                <div class="header-stat">
                                    <span class="header-stat-number">3</span>
                                    <span class="header-stat-label">Active Jobs</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number">0</span>
                                    <span class="header-stat-label">Awaiting Payment</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number">6</span>
                                    <span class="header-stat-label">Total Jobs</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filter Tabs Section -->
                    <section class="filter-tabs-section">
                        <div class="filter-tabs-container">
                            <div class="filter-tabs">
                                <button class="filter-tab active" data-filter="all">
                                    <i class="fas fa-list"></i>
                                    All Jobs
                                    <span class="tab-count">6</span>
                                </button>
                                <button class="filter-tab" data-filter="active">
                                    <i class="fas fa-play-circle"></i>
                                    Active
                                    <span class="tab-count">3</span>
                                </button>
                                <button class="filter-tab" data-filter="completed">
                                    <i class="fas fa-clipboard-check"></i>
                                    Completed
                                    <span class="tab-count">0</span>
                                </button>
                                <button class="filter-tab" data-filter="paid">
                                    <i class="fas fa-check-circle"></i>
                                    Paid
                                    <span class="tab-count">2</span>
                                </button>
                                <button class="filter-tab" data-filter="cancelled">
                                    <i class="fas fa-times-circle"></i>
                                    Cancelled
                                    <span class="tab-count">1</span>
                                </button>
                            </div>
                            <div class="filter-actions">
                                <select class="filter-select" id="sort-filter">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="status">By Status</option>
                                    <option value="amount">By Amount</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Jobs Section with Calendar -->
                    <section class="jobs-section">
                        <div class="jobs-container">
                            <!-- Jobs List Section -->
                            <div class="jobs-list-container">
                                <div class="section-header">
                                    <h2 class="section-title">My Jobs</h2>
                                    <span class="section-subtitle">6 jobs found</span>
                                </div>

                        <!-- Jobs List -->
                        <div class="jobs-list">
                            <!-- Job Item 1 - Active -->
                            <div class="job-item active-job" data-status="active">
                                <div class="job-status-badge active">
                                    <i class="fas fa-tools"></i>
                                    Active
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Kitchen Sink Repair</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-user"></i>
                                            <span>Sarah Fernando</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Colombo 07, Western Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Started 2 days ago</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 2,800</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <button class="btn btn-primary" onclick="viewJobDetails(1)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-success" onclick="markJobAsCompleted(this, 1)">
                                        <i class="fas fa-check"></i>
                                        Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" onclick="cancelJob(this, 1)">
                                        <i class="fas fa-times"></i>
                                        Cancel Job
                                    </button>
                                </div>
                            </div>

                            <!-- Job Item 2 - Active -->
                            <div class="job-item active-job" data-status="active">
                                <div class="job-status-badge active">
                                    <i class="fas fa-tools"></i>
                                    Active
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Ceiling Fan Installation</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-building"></i>
                                            <span>Kandy Hardware Store</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Kandy, Central Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Started yesterday</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 4,200</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <button class="btn btn-primary" onclick="viewJobDetails(2)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-success" onclick="markJobAsCompleted(this, 2)">
                                        <i class="fas fa-check"></i>
                                        Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" onclick="cancelJob(this, 2)">
                                        <i class="fas fa-times"></i>
                                        Cancel Job
                                    </button>
                                </div>
                            </div>

                            <!-- Job Item 3 - Active -->
                            <div class="job-item active-job" data-status="active">
                                <div class="job-status-badge active">
                                    <i class="fas fa-tools"></i>
                                    Active
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Air Conditioning Repair</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-user"></i>
                                            <span>Priya Wickramasinghe</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Nugegoda, Western Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Started today</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 3,500</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <button class="btn btn-primary" onclick="viewJobDetails(3)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-success" onclick="markJobAsCompleted(this, 3)">
                                        <i class="fas fa-check"></i>
                                        Mark as Completed
                                    </button>
                                    <button class="btn btn-danger" onclick="cancelJob(this, 3)">
                                        <i class="fas fa-times"></i>
                                        Cancel Job
                                    </button>
                                </div>
                            </div>

                            <!-- Job Item 4 - Paid -->
                            <div class="job-item completed-job" data-status="paid">
                                <div class="job-status-badge paid">
                                    <i class="fas fa-check-circle"></i>
                                    Paid
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Washing Machine Repair</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-user"></i>
                                            <span>Nimal Perera</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Maharagama, Western Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Completed 3 days ago</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 2,500</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <button class="btn btn-primary" onclick="viewJobDetails(4)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Job Item 5 - Paid -->
                            <div class="job-item completed-job" data-status="completed">
                                <div class="job-status-badge paid">
                                    <i class="fas fa-check-circle"></i>
                                    Paid
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Bathroom Plumbing Fix</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-user"></i>
                                            <span>Kamala Silva</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Dehiwala, Western Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Completed 1 week ago</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 1,800</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <button class="btn btn-primary" onclick="viewJobDetails(5)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>

                            <!-- Job Item 6 - Cancelled (Sample) -->
                            <div class="job-item cancelled-job" data-status="cancelled">
                                <div class="job-status-badge cancelled">
                                    <i class="fas fa-times-circle"></i>
                                    Cancelled
                                </div>
                                <div class="job-info">
                                    <div class="job-header">
                                        <h3 class="job-title">Electrical Wiring Repair</h3>
                                    </div>
                                    <div class="job-details">
                                        <div class="job-customer">
                                            <i class="fas fa-user"></i>
                                            <span>Rajith Kumar</span>
                                        </div>
                                        <div class="job-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Moratuwa, Western Province</span>
                                        </div>
                                        <div class="job-date">
                                            <i class="fas fa-calendar"></i>
                                            <span>Cancelled 2 days ago</span>
                                        </div>
                                        <div class="job-amount">
                                            <i class="fas fa-money-bill"></i>
                                            <span class="amount">LKR 3,200</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="job-actions">
                                    <div class="cancellation-info">
                                        <span class="cancelled-label">
                                            <i class="fas fa-times-circle"></i>
                                            Job Cancelled
                                        </span>
                                        <span class="cancel-reason">Reason: Customer requested another repairer</span>
                                    </div>
                                    <button class="btn btn-secondary" onclick="viewJobDetails(6)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End Jobs List -->
                        </div>
                        <!-- End Jobs List Container -->

                        <!-- Calendar Section -->
                            <div class="calendar-container">
                                <div class="calendar-widget">
                                    <div class="calendar-header">
                                        <div class="calendar-title">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Calendar</span>
                                        </div>
                                        <div class="calendar-controls">
                                            <button class="calendar-nav-btn" id="prev-month">
                                                <i class="fas fa-chevron-left"></i>
                                            </button>
                                            <span class="calendar-month-year" id="current-month-year">October 2025</span>
                                            <button class="calendar-nav-btn" id="next-month">
                                                <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="calendar-body">
                                        <div class="calendar-weekdays">
                                            <div class="calendar-weekday">S</div>
                                            <div class="calendar-weekday">M</div>
                                            <div class="calendar-weekday">T</div>
                                            <div class="calendar-weekday">W</div>
                                            <div class="calendar-weekday">T</div>
                                            <div class="calendar-weekday">F</div>
                                            <div class="calendar-weekday">S</div>
                                        </div>
                                        <div class="calendar-days" id="calendar-days">
                                            <!-- Days will be generated by JavaScript -->
                                        </div>
                                    </div>
                                    <div class="calendar-footer">
                                        <div class="upcoming-events">
                                            <h3 class="upcoming-events-title">
                                                <i class="fas fa-clock"></i>
                                                Upcoming Events
                                            </h3>
                                            <div class="upcoming-events-list" id="upcoming-events-list">
                                                <!-- Events will be populated by JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal-overlay" id="jobDetailsModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Job Details</h3>
                <button class="modal-close" onclick="closeJobDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="job-details-content">
                    <!-- Job Header -->
                    <div class="job-details-header">
                        <div class="job-detail-title-section">
                            <h2 id="modal-job-title">Kitchen Sink Repair</h2>
                            <span class="job-detail-status" id="modal-job-status">
                                <i class="fas fa-tools"></i>
                                Active
                            </span>
                        </div>
                        <div class="job-detail-amount" id="modal-job-amount">LKR 2,800</div>
                    </div>

                    <!-- Customer Information -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-user"></i>
                            Customer Information
                        </h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value" id="modal-customer-name">Sarah Fernando</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value" id="modal-customer-phone">+94 77 123 4567</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value" id="modal-customer-email">sarah.fernando@email.com</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location:</span>
                                <span class="detail-value" id="modal-job-location">Colombo 07, Western Province</span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Information -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-info-circle"></i>
                            Job Information
                        </h4>
                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="detail-label">Started:</span>
                                <span class="detail-value" id="modal-job-started">October 21, 2025</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estimated Completion:</span>
                                <span class="detail-value" id="modal-job-completion">October 24, 2025</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Job ID:</span>
                                <span class="detail-value" id="modal-job-id">#JOB-2025-001</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value" id="modal-job-category">Plumbing</span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-file-alt"></i>
                            Description
                        </h4>
                        <p class="job-description" id="modal-job-description">
                            The kitchen sink is leaking from the pipe underneath. Water is dripping constantly and needs immediate repair. The customer mentioned that the issue started 3 days ago and has been getting worse. Please bring necessary tools and replacement parts if needed.
                        </p>
                    </div>

                    <!-- Payment Details -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-money-bill-wave"></i>
                            Payment Details
                        </h4>
                        <div class="payment-breakdown">
                            <div class="payment-row">
                                <span class="payment-label">Service Charge:</span>
                                <span class="payment-value" id="modal-service-charge">LKR 2,500</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Platform Fee (15%):</span>
                                <span class="payment-value" id="modal-platform-fee">LKR 375</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Tax (5%):</span>
                                <span class="payment-value" id="modal-tax">LKR 125</span>
                            </div>
                            <div class="payment-divider"></div>
                            <div class="payment-row payment-total">
                                <span class="payment-label">Total Amount:</span>
                                <span class="payment-value" id="modal-total-amount">LKR 2,800</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Your Earnings:</span>
                                <span class="payment-value payment-earnings" id="modal-earnings">LKR 2,125</span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-history"></i>
                            Status Timeline
                        </h4>
                        <div class="status-timeline" id="modal-timeline">
                            <div class="timeline-item completed">
                                <div class="timeline-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Job Accepted</div>
                                    <div class="timeline-date">October 21, 2025 - 10:30 AM</div>
                                </div>
                            </div>
                            <div class="timeline-item completed">
                                <div class="timeline-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Work Started</div>
                                    <div class="timeline-date">October 21, 2025 - 2:00 PM</div>
                                </div>
                            </div>
                            <div class="timeline-item active">
                                <div class="timeline-icon">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">In Progress</div>
                                    <div class="timeline-date">Current Status</div>
                                </div>
                            </div>
                            <div class="timeline-item pending">
                                <div class="timeline-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-title">Pending Completion</div>
                                    <div class="timeline-date">Est. October 24, 2025</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeJobDetailsModal()">
                    <i class="fas fa-times"></i>
                    Close
                </button>
                <button class="btn btn-primary" id="modal-primary-action">
                    <i class="fas fa-check"></i>
                    Mark as Complete
                </button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/my-jobs.js"></script>
</body>
</html>

