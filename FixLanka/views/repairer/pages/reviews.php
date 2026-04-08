<?php
// Page configuration
$currentPage = 'reviews';
$pageTitle = 'Customer Reviews';
$pageSubtitle = 'Manage your customer feedback and ratings';
$searchPlaceholder = 'Search reviews, customers, ratings...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/reviews.css">
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
                                <h1 class="page-header-title">Customer Reviews</h1>
                                <p class="page-header-subtitle">Manage your customer feedback and build your reputation</p>
                            </div>
                            <div class="page-header-stats">
                                <div class="rating-overview">
                                    <div class="overall-rating">
                                        <span class="rating-number" id="overallRatingNumber">—</span>
                                        <div class="rating-stars" id="overallRatingStars">
                                        </div>
                                        <span class="rating-count" id="reviewCountLabel">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Statistics Section -->
                    <section class="stats-section">
                        <div class="stats-cards">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number" id="totalReviewsCount">—</h3>
                                    <p class="stat-label">Total Reviews</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-thumbs-up"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number" id="positiveReviewsRate">—</h3>
                                    <p class="stat-label">Positive Reviews</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-reply"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number" id="responseRate">—</h3>
                                    <p class="stat-label">Response Rate</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number" id="avgResponseTime">—</h3>
                                    <p class="stat-label">Avg Response Time (hours)</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filters Section -->
                    <section class="filters-section">
                        <div class="filters-container">
                            <div class="filter-group">
                                <label for="rating-filter" class="filter-label">Rating</label>
                                <select id="rating-filter" class="filter-select">
                                    <option value="all">All Ratings</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="response-filter" class="filter-label">Response Status</label>
                                <select id="response-filter" class="filter-select">
                                    <option value="all">All Reviews</option>
                                    <option value="responded">Responded</option>
                                    <option value="pending">Pending Response</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="sort-filter" class="filter-label">Sort By</label>
                                <select id="sort-filter" class="filter-select">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="rating-high">Highest Rating</option>
                                    <option value="rating-low">Lowest Rating</option>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button class="btn btn-outline" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Reviews Section -->
                    <section class="reviews-section">
                        <div class="section-header">
                            <h2 class="section-title">Customer Reviews</h2>
                            <span class="section-subtitle" id="reviewsSubtitle">Loading...</span>
                        </div>

                        <div class="reviews-list" id="reviewsList">
                            <div class="loading-state" style="text-align:center;padding:40px;color:var(--text-secondary)">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p style="margin-top:12px">Loading reviews...</p>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    <!-- Respond Modal -->
    <div class="modal-overlay" id="respondModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Respond to Review</h3>
                <button class="modal-close" onclick="closeRespondModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="customer-info">
                    <strong>Responding to:</strong> <span id="modalCustomerName"></span>
                </div>
                <div class="response-form">
                    <label for="responseText" class="form-label">Your Response</label>
                    <textarea 
                        id="responseText" 
                        class="response-textarea" 
                        placeholder="Write your professional response to this review..."
                        rows="6"
                    ></textarea>
                    <div class="character-count">
                        <span id="charCount">0</span>/500 characters
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeRespondModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button class="btn btn-primary" onclick="sendResponse()">
                    <i class="fas fa-paper-plane"></i>
                    Send Response
                </button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script>
        window.CURRENT_REPAIRER_ID = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/reviews.js"></script>
</body>
</html>

