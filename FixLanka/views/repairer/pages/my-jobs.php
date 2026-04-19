<?php
require_once __DIR__ . '/../../../config/session.php';

// Ensure repairer is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /2nd-Year-Group-Project/FixLanka/views/auth/login.php');
    exit;
}

$currentRepairerId = (int)$_SESSION['user_id'];

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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
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
                                    <span class="header-stat-number" id="headerActiveJobs">—</span>
                                    <span class="header-stat-label">Active Jobs</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number" id="headerAwaitingPayment">—</span>
                                    <span class="header-stat-label">Awaiting Payment</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number" id="headerTotalJobs">—</span>
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
                                    <span class="tab-count" id="tabCountAll">—</span>
                                </button>
                                <button class="filter-tab" data-filter="active">
                                    <i class="fas fa-play-circle"></i>
                                    Active
                                    <span class="tab-count" id="tabCountActive">—</span>
                                </button>
                                <button class="filter-tab" data-filter="completed">
                                    <i class="fas fa-clipboard-check"></i>
                                    Completed
                                    <span class="tab-count" id="tabCountCompleted">—</span>
                                </button>
                                <button class="filter-tab" data-filter="paid">
                                    <i class="fas fa-check-circle"></i>
                                    Paid
                                    <span class="tab-count" id="tabCountPaid">—</span>
                                </button>
                                <button class="filter-tab" data-filter="cancelled">
                                    <i class="fas fa-times-circle"></i>
                                    Cancelled
                                    <span class="tab-count" id="tabCountCancelled">—</span>
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
                                    <span class="section-subtitle" id="jobsFoundCount">Loading...</span>
                                </div>

                        <!-- Jobs list populated by my-jobs.js -->
                        <div class="jobs-list" id="jobsList">
                            <div class="loading-state" style="text-align:center;padding:40px;color:var(--text-secondary)">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p style="margin-top:12px">Loading your jobs...</p>
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
                            <h2 id="modal-job-title">—</h2>
                            <span class="job-detail-status" id="modal-job-status">
                                <i class="fas fa-tools"></i>
                                Active
                            </span>
                        </div>
                        <div class="job-detail-amount" id="modal-job-amount">—</div>
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
                                <span class="detail-value" id="modal-customer-name">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value" id="modal-customer-phone">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value" id="modal-customer-email">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location:</span>
                                <span class="detail-value" id="modal-job-location">—</span>
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
                                <span class="detail-value" id="modal-job-started">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estimated Completion:</span>
                                <span class="detail-value" id="modal-job-completion">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Job ID:</span>
                                <span class="detail-value" id="modal-job-id">—</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value" id="modal-job-category">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="job-detail-section">
                        <h4 class="detail-section-title">
                            <i class="fas fa-file-alt"></i>
                            Description
                        </h4>
                        <p class="job-description" id="modal-job-description">—</p>
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
                                <span class="payment-value" id="modal-service-charge">—</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Platform Fee (15%):</span>
                                <span class="payment-value" id="modal-platform-fee">—</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Tax (5%):</span>
                                <span class="payment-value" id="modal-tax">—</span>
                            </div>
                            <div class="payment-divider"></div>
                            <div class="payment-row payment-total">
                                <span class="payment-label">Total Amount:</span>
                                <span class="payment-value" id="modal-total-amount">—</span>
                            </div>
                            <div class="payment-row">
                                <span class="payment-label">Your Earnings:</span>
                                <span class="payment-value payment-earnings" id="modal-earnings">—</span>
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
                            <!-- Timeline populated by my-jobs.js -->
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

    <!-- Review Modal -->
    <div class="modal-overlay" id="reviewModal">
        <div class="modal-container review-modal-container">
            <div class="modal-header">
                <h3 class="modal-title" id="reviewModalTitle">Give a Review</h3>
                <button class="modal-close" onclick="closeReviewModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="review-summary-panel" id="reviewSummaryPanel" style="display:none;">
                    <div class="review-summary-head">
                        <div>
                            <h4 class="review-summary-job" id="reviewJobTitle">—</h4>
                            <p class="review-summary-customer" id="reviewCustomerName">—</p>
                        </div>
                        <div class="review-summary-rating" id="reviewSummaryRating">—</div>
                    </div>
                    <p class="review-summary-comment" id="reviewSummaryComment">—</p>
                    <div class="review-summary-date" id="reviewSummaryDate">—</div>
                </div>

                <div class="review-form-panel" id="reviewFormPanel">
                    <div class="review-field">
                        <label for="reviewRating">Rating</label>
                        <select id="reviewRating">
                            <option value="">Select rating</option>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Bad</option>
                        </select>
                    </div>
                    <div class="review-field">
                        <label for="reviewComments">Comments</label>
                        <textarea id="reviewComments" rows="4" placeholder="Write your review here..."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer review-modal-footer">
                <button class="btn btn-secondary" onclick="closeReviewModal()">
                    <i class="fas fa-times"></i>
                    Close
                </button>
                <button class="btn btn-outline" id="reviewEditButton" onclick="startReviewEdit()" style="display:none;">
                    <i class="fas fa-pen"></i>
                    Edit Review
                </button>
                <button class="btn btn-primary" id="reviewSubmitButton" onclick="submitReview()">
                    <i class="fas fa-paper-plane"></i>
                    Submit Review
                </button>
            </div>
        </div>
    </div>

    <script>
        window.CURRENT_REPAIRER_ID = <?php echo $currentRepairerId; ?>;
        window.BASE_URL = '/2nd-Year-Group-Project/FixLanka/';
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/my-jobs.js"></script>
</body>
</html>

