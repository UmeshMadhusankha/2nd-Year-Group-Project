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
require_once '../../config/session.php';
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/reviews.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/reviews.js"></script>
</body>
</html>
