<?php
/**
 * Company Reviews & Feedback Page
 * 
 * This page allows companies to:
 * - View detailed reviews from customers, projects, and workforce
 * - Filter and search reviews
 * - Analyze review trends and ratings
 * 
 * Authentication: Requires logged-in company user
 */

// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');

// Retrieve logged-in user data from session
$userData = getUserData();
$companyId = $userData['id'] ?? null;

// Ensure user is authenticated
if (!$companyId) {
    die('Error: Company not authenticated');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews & Feedback - FixLanka Dashboard</title>
    
    <!-- CSS Files -->
     <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/reviews.css?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../assets/css/company/reviews.css')); ?>">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/payments-export-modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Pass company ID to JavaScript -->
    <script>
        window.CURRENT_COMPANY_ID = <?php echo json_encode($companyId); ?>;
    </script>
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <section class="reviews-page">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-star"></i> Reviews & Feedback</h1>
                                <p class="subtitle">Monitor and manage customer feedback for your company, projects, and workforce</p>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn secondary" id="exportReviewsBtn">
                                        <i class="fas fa-download"></i>
                                        Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Reviews</span>
                        </div>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-value" id="companyRatingStat">-</span>
                                <span class="stat-label">Company Rating</span>
                            </div>
                            <!-- Currently static placeholders until more detailed stats are available -->
                            <div class="stat-item">
                                <span class="stat-value" id="projectScoreStat">-</span>
                                <span class="stat-label">Project Score</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="workforceQualityStat">-</span>
                                <span class="stat-label">Workforce Quality</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="monthlyTrendStat">-</span>
                                <span class="stat-label">Monthly Trend</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Filters and Controls -->
                <div class="reviews-controls">
                    <div class="filter-section">
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="reviewCategory">
                                <option value="all">All Categories</option>
                                <option value="company">Company</option>
                                <option value="project">Project</option>
                                <option value="worker">Worker</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="reviewRating">
                                <option value="all">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="reviewPeriod">
                                <option value="all">All Dates</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                    </div>


                </div>

                <!-- Reviews Grid -->
                <div class="reviews-container" id="reviewsContainer">
                     <!-- Empty State -->
                     <div class="reviews-empty">
                        <i class="fas fa-star fa-3x"></i>
                        <h3>No Reviews Found</h3>
                        <p>You don't have any reviews matching your criteria.</p>
                     </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Scripts -->
    <!-- Export Modal -->
    <div class="export-modal-overlay" id="reviewsExportModal">
        <div class="export-modal-container">
            <div class="export-modal-header">
                <h2><i class="fas fa-file-export"></i> Export Review Report</h2>
                <button class="export-modal-close" onclick="closeReviewsExportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="export-modal-body">
                <div class="export-options-container">
                    <!-- Rating Filter -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-star"></i> Minimum Rating</h4>
                        <div class="export-radio-group">
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewRating" value="all" checked>
                                <span class="radio-indicator"></span>
                                All Ratings
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewRating" value="5">
                                <span class="radio-indicator"></span>
                                5 Stars
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewRating" value="4">
                                <span class="radio-indicator"></span>
                                4+ Stars
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewRating" value="3">
                                <span class="radio-indicator"></span>
                                3+ Stars
                            </label>
                        </div>
                    </div>

                    <!-- Period Filter -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-calendar-alt"></i> Time Period</h4>
                        <div class="export-radio-group">
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewPeriod" value="all" checked>
                                <span class="radio-indicator"></span>
                                All Time
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewPeriod" value="month">
                                <span class="radio-indicator"></span>
                                Last 30 Days
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewPeriod" value="quarter">
                                <span class="radio-indicator"></span>
                                Last 3 Months
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportReviewPeriod" value="year">
                                <span class="radio-indicator"></span>
                                Last Year
                            </label>
                        </div>
                    </div>

                    <!-- Export Format -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-file-alt"></i> Export Format</h4>
                        <div class="export-format-group">
                            <label class="export-format-option">
                                <input type="radio" name="exportReviewFormat" value="csv" checked>
                                <div class="export-format-icon csv">
                                    <i class="fas fa-file-csv"></i>
                                </div>
                                <span class="export-format-name">CSV</span>
                                <span class="export-format-desc">Best for Excel</span>
                            </label>
                            <label class="export-format-option">
                                <input type="radio" name="exportReviewFormat" value="pdf">
                                <div class="export-format-icon pdf">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <span class="export-format-name">PDF</span>
                                <span class="export-format-desc">Print ready</span>
                            </label>
                        </div>
                    </div>

                    <!-- Export Summary -->
                    <div class="export-summary-box" id="reviewsExportSummaryBox">
                        <h4><i class="fas fa-info-circle"></i> Export Summary</h4>
                        <div class="export-summary-stats">
                            <div class="export-summary-stat">
                                <span class="stat-value" id="reviewsExportTotalRecords">--</span>
                                <span class="stat-label">Reviews</span>
                            </div>
                            <div class="export-summary-stat">
                                <span class="stat-value" id="reviewsExportAvgRating">--</span>
                                <span class="stat-label">Avg Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="export-modal-footer">
                <button class="export-btn export-btn-cancel" onclick="closeReviewsExportModal()">Cancel</button>
                <button class="export-btn export-btn-download" onclick="downloadReviewsExport()">
                    <i class="fas fa-download"></i> Export Now
                </button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/reviews-export.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/reviews.js"></script>
</body>
</html>
