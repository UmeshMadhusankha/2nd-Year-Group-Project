<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews & Feedback - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../assets/css/company/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/company/topbar.css">
    <link rel="stylesheet" href="../../assets/css/company/settings.css">
    <link rel="stylesheet" href="../../assets/css/company/reviews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <!-- Reviews Header -->
            <div class="settings-header">
                <div class="header-left">
                    <h1><i class="fas fa-star"></i> Reviews & Feedback</h1>
                    <p class="subtitle">Monitor and manage customer feedback for your company, projects, and workforce</p>
                </div>
            </div>

            <!-- Reviews Content -->
            <div class="settings-container">
                <!-- Reviews Overview -->
                <div class="settings-section">
                    <h2 class="section-title">Reviews & Feedback Overview</h2>
                    <p class="section-description">Monitor and manage customer feedback for your company, projects, and workforce</p>

                    <!-- Reviews Summary Cards -->
                    <div class="reviews-summary">
                        <div class="review-card summary-card">
                            <div class="review-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="review-info">
                                <h3>Company Rating</h3>
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="rating-score">4.6</span>
                                </div>
                                <p class="review-count">124 reviews</p>
                            </div>
                        </div>

                        <div class="review-card summary-card">
                            <div class="review-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="review-info">
                                <h3>Project Satisfaction</h3>
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <span class="rating-score">4.2</span>
                                </div>
                                <p class="review-count">89 project reviews</p>
                            </div>
                        </div>

                        <div class="review-card summary-card">
                            <div class="review-icon">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="review-info">
                                <h3>Workforce Quality</h3>
                                <div class="rating-display">
                                    <div class="stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <span class="rating-score">4.8</span>
                                </div>
                                <p class="review-count">156 worker reviews</p>
                            </div>
                        </div>

                        <div class="review-card summary-card">
                            <div class="review-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="review-info">
                                <h3>Monthly Trend</h3>
                                <div class="rating-trend">
                                    <span class="trend-value positive">+0.3</span>
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <p class="review-count">vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="settings-section">
                    <div class="reviews-filter-bar">
                        <div class="filter-left">
                            <div class="filter-group">
                                <label for="reviewCategory">Category:</label>
                                <select id="reviewCategory">
                                    <option value="all">All Reviews</option>
                                    <option value="company">Company Reviews</option>
                                    <option value="projects">Project Reviews</option>
                                    <option value="workforce">Workforce Reviews</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="reviewRating">Rating:</label>
                                <select id="reviewRating">
                                    <option value="all">All Ratings</option>
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label for="reviewPeriod">Period:</label>
                                <select id="reviewPeriod">
                                    <option value="all">All Time</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="filter-right">
                            <div class="search-group">
                                <input type="text" id="reviewSearch" placeholder="Search reviews..." class="search-input">
                                <button class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="settings-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Reviews</h2>
                        <button class="btn-secondary" id="exportReviewsBtn">
                            <i class="fas fa-download"></i> Export Reviews
                        </button>
                    </div>

                    <!-- Review Items -->
                    <div class="reviews-list">
                        <!-- Company Review -->
                        <div class="review-item company-review">
                            <div class="review-header">
                                <div class="review-meta">
                                    <div class="reviewer-info">
                                        <img src="../../assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                        <div class="reviewer-details">
                                            <h4>Sarah Johnson</h4>
                                            <p class="review-type">Company Review</p>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span class="rating-number">5.0</span>
                                    </div>
                                </div>
                                <div class="review-date">2 days ago</div>
                            </div>
                            <div class="review-content">
                                <p>"Excellent service! FixLanka Solutions exceeded our expectations. Professional team, timely delivery, and high-quality workmanship. Highly recommended for any repair or maintenance needs."</p>
                            </div>
                            <div class="review-tags">
                                <span class="tag">Professional</span>
                                <span class="tag">Timely</span>
                                <span class="tag">Quality Work</span>
                            </div>
                            <div class="review-actions">
                                <button class="btn-action" title="Reply">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                            </div>
                            <!-- Reply Section -->
                            <div class="review-reply">
                                <div class="reply-header">
                                    <img src="../../assets/images/fixlanka.png" alt="FixLanka" class="reply-avatar">
                                    <div class="reply-info">
                                        <h5>FixLanka Solutions</h5>
                                        <span class="reply-date">1 day ago</span>
                                    </div>
                                </div>
                                <div class="reply-content">
                                    <p>Thank you so much for your kind words, Sarah! We're thrilled to hear that you had a great experience with our team. Your satisfaction is our top priority, and we look forward to serving you again in the future.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Project Review -->
                        <div class="review-item project-review">
                            <div class="review-header">
                                <div class="review-meta">
                                    <div class="reviewer-info">
                                        <img src="../../assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                        <div class="reviewer-details">
                                            <h4>Michael Chen</h4>
                                            <p class="review-type">Project: Kitchen Renovation</p>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span class="rating-number">4.0</span>
                                    </div>
                                </div>
                                <div class="review-date">5 days ago</div>
                            </div>
                            <div class="review-content">
                                <p>"Great work on our kitchen renovation. The project was completed on time and within budget. Minor delays due to weather, but the team communicated well throughout the process."</p>
                            </div>
                            <div class="review-tags">
                                <span class="tag">On Time</span>
                                <span class="tag">Budget-Friendly</span>
                                <span class="tag">Good Communication</span>
                            </div>
                            <div class="review-actions">
                                <button class="btn-action" title="Reply">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                <button class="btn-action" title="View Project">
                                    <i class="fas fa-eye"></i> View Project
                                </button>
                            </div>
                            <!-- Reply Section -->
                            <div class="review-reply">
                                <div class="reply-header">
                                    <img src="../../assets/images/fixlanka.png" alt="FixLanka" class="reply-avatar">
                                    <div class="reply-info">
                                        <h5>FixLanka Solutions</h5>
                                        <span class="reply-date">4 days ago</span>
                                    </div>
                                </div>
                                <div class="reply-content">
                                    <p>Hi Michael, thank you for your feedback! We appreciate your understanding regarding the weather delays. We're glad we could keep you informed throughout the process and deliver quality results within your budget.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Worker Review -->
                        <div class="review-item worker-review">
                            <div class="review-header">
                                <div class="review-meta">
                                    <div class="reviewer-info">
                                        <img src="../../assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                        <div class="reviewer-details">
                                            <h4>Amanda Rodriguez</h4>
                                            <p class="review-type">Worker: John Silva (Electrician)</p>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span class="rating-number">5.0</span>
                                    </div>
                                </div>
                                <div class="review-date">1 week ago</div>
                            </div>
                            <div class="review-content">
                                <p>"John did an amazing job with our electrical installation. Very knowledgeable, professional, and clean work. Explained everything clearly and finished ahead of schedule."</p>
                            </div>
                            <div class="review-tags">
                                <span class="tag">Expert</span>
                                <span class="tag">Clean Work</span>
                                <span class="tag">Ahead of Schedule</span>
                            </div>
                            <div class="review-actions">
                                <button class="btn-action" title="Reply">
                                    <i class="fas fa-reply"></i> Reply
                                </button>
                                <button class="btn-action" title="View Worker Profile">
                                    <i class="fas fa-user"></i> View Worker
                                </button>
                            </div>
                            <!-- Reply Section -->
                            <div class="review-reply">
                                <div class="reply-header">
                                    <img src="../../assets/images/fixlanka.png" alt="FixLanka" class="reply-avatar">
                                    <div class="reply-info">
                                        <h5>FixLanka Solutions</h5>
                                        <span class="reply-date">6 days ago</span>
                                    </div>
                                </div>
                                <div class="reply-content">
                                    <p>Thank you for the wonderful review, Amanda! We're proud to have John on our team, and we're delighted that his expertise and professionalism exceeded your expectations. We'll be sure to pass along your kind words!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Critical Review -->
                        <div class="review-item critical-review">
                            <div class="review-header">
                                <div class="review-meta">
                                    <div class="reviewer-info">
                                        <img src="../../assets/images/user.png" alt="Customer" class="reviewer-avatar">
                                        <div class="reviewer-details">
                                            <h4>David Thompson</h4>
                                            <p class="review-type">Project: Bathroom Repair</p>
                                        </div>
                                    </div>
                                    <div class="review-rating">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                        <span class="rating-number">2.0</span>
                                    </div>
                                </div>
                                <div class="review-date">2 weeks ago</div>
                            </div>
                            <div class="review-content">
                                <p>"Project took longer than expected and there were some communication issues. However, the final result was satisfactory and the team worked to resolve our concerns."</p>
                            </div>
                            <div class="review-tags">
                                <span class="tag negative">Delayed</span>
                                <span class="tag negative">Communication Issues</span>
                                <span class="tag">Resolved</span>
                            </div>
                            <div class="review-actions">
                                <button class="btn-action replied" title="Replied">
                                    <i class="fas fa-check-circle"></i> Replied
                                </button>
                                <button class="btn-action" title="View Project">
                                    <i class="fas fa-eye"></i> View Project
                                </button>
                            </div>
                            <!-- Reply Section -->
                            <div class="review-reply">
                                <div class="reply-header">
                                    <img src="../../assets/images/fixlanka.png" alt="FixLanka" class="reply-avatar">
                                    <div class="reply-info">
                                        <h5>FixLanka Solutions</h5>
                                        <span class="reply-date">2 weeks ago</span>
                                    </div>
                                </div>
                                <div class="reply-content">
                                    <p>Hi David, thank you for your honest feedback. We sincerely apologize for the delays and communication issues you experienced. We've taken your concerns seriously and have implemented improvements to our project management process. We're glad we could work together to achieve a satisfactory outcome, and we appreciate your patience. If there's anything else we can do, please don't hesitate to reach out.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Load More Button -->
                    <div class="load-more-container">
                        <button class="btn-secondary" id="loadMoreReviews">
                            <i class="fas fa-plus"></i> Load More Reviews
                        </button>
                    </div>
                </div>

                <!-- Analytics and Insights -->
                <div class="settings-section">
                    <h2 class="section-title">Review Analytics</h2>

                    <div class="analytics-grid">
                        <!-- Rating Distribution -->
                        <div class="analytics-card">
                            <h3>Rating Distribution</h3>
                            <div class="rating-bars">
                                <div class="rating-bar">
                                    <span class="rating-label">5 stars</span>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: 68%"></div>
                                    </div>
                                    <span class="rating-count">84</span>
                                </div>
                                <div class="rating-bar">
                                    <span class="rating-label">4 stars</span>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: 22%"></div>
                                    </div>
                                    <span class="rating-count">27</span>
                                </div>
                                <div class="rating-bar">
                                    <span class="rating-label">3 stars</span>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: 6%"></div>
                                    </div>
                                    <span class="rating-count">8</span>
                                </div>
                                <div class="rating-bar">
                                    <span class="rating-label">2 stars</span>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: 3%"></div>
                                    </div>
                                    <span class="rating-count">4</span>
                                </div>
                                <div class="rating-bar">
                                    <span class="rating-label">1 star</span>
                                    <div class="bar-container">
                                        <div class="bar-fill" style="width: 1%"></div>
                                    </div>
                                    <span class="rating-count">1</span>
                                </div>
                            </div>
                        </div>

                        <!-- Top Keywords -->
                        <div class="analytics-card">
                            <h3>Top Keywords in Reviews</h3>
                            <div class="keyword-cloud">
                                <span class="keyword large">Professional</span>
                                <span class="keyword medium">Quality</span>
                                <span class="keyword large">Timely</span>
                                <span class="keyword small">Excellent</span>
                                <span class="keyword medium">Communication</span>
                                <span class="keyword small">Clean</span>
                                <span class="keyword medium">Reliable</span>
                                <span class="keyword small">Friendly</span>
                                <span class="keyword large">Skilled</span>
                                <span class="keyword small">Efficient</span>
                            </div>
                        </div>

                        <!-- Response Rate -->
                        <div class="analytics-card">
                            <h3>Response Rate</h3>
                            <div class="response-stats">
                                <div class="stat-item">
                                    <div class="stat-value">87%</div>
                                    <div class="stat-label">Reviews Responded To</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">2.4 hrs</div>
                                    <div class="stat-label">Average Response Time</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">16</div>
                                    <div class="stat-label">Pending Responses</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Management Actions -->
                <div class="settings-section">
                    <h2 class="section-title">Review Management</h2>

                    <div class="management-actions">
                        <button class="btn-secondary" id="reviewSettingsBtn">
                            <i class="fas fa-cog"></i> Review Settings & Notifications
                        </button>
                        <button class="btn-secondary" id="reportAnalyticsBtn">
                            <i class="fas fa-chart-bar"></i> Generate Detailed Report
                        </button>
                    </div>

                    <!-- Review Guidelines -->
                    <div class="review-guidelines">
                        <h4>Review Response Best Practices:</h4>
                        <ul>
                            <li><i class="fas fa-check"></i> Respond to reviews within 24-48 hours</li>
                            <li><i class="fas fa-check"></i> Thank customers for positive feedback</li>
                            <li><i class="fas fa-check"></i> Address concerns professionally in negative reviews</li>
                            <li><i class="fas fa-check"></i> Keep responses concise and helpful</li>
                            <li><i class="fas fa-check"></i> Invite customers to contact you directly for further assistance</li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../assets/javascript/company/sidebar.js"></script>
    <script>
        // Reviews functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const categoryFilter = document.getElementById('reviewCategory');
            const ratingFilter = document.getElementById('reviewRating');
            const periodFilter = document.getElementById('reviewPeriod');
            const searchInput = document.getElementById('reviewSearch');
            const searchBtn = document.querySelector('.search-btn');

            // Filter event listeners
            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterReviews);
            }
            if (ratingFilter) {
                ratingFilter.addEventListener('change', filterReviews);
            }
            if (periodFilter) {
                periodFilter.addEventListener('change', filterReviews);
            }
            if (searchInput) {
                searchInput.addEventListener('input', filterReviews);
            }
            if (searchBtn) {
                searchBtn.addEventListener('click', filterReviews);
            }

            // Button event listeners
            const exportReviewsBtn = document.getElementById('exportReviewsBtn');
            const loadMoreReviews = document.getElementById('loadMoreReviews');
            const reviewSettingsBtn = document.getElementById('reviewSettingsBtn');
            const reportAnalyticsBtn = document.getElementById('reportAnalyticsBtn');

            if (exportReviewsBtn) {
                exportReviewsBtn.addEventListener('click', () => {
                    alert('Export functionality will be implemented soon!');
                });
            }
            if (loadMoreReviews) {
                loadMoreReviews.addEventListener('click', () => {
                    alert('Loading more reviews...');
                });
            }
            if (reviewSettingsBtn) {
                reviewSettingsBtn.addEventListener('click', () => {
                    alert('Review settings panel will be implemented soon!');
                });
            }
            if (reportAnalyticsBtn) {
                reportAnalyticsBtn.addEventListener('click', () => {
                    alert('Analytics report generation will be implemented soon!');
                });
            }

            // Review action buttons
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-action')) {
                    const button = e.target.closest('.btn-action');
                    const action = button.textContent.trim();
                    
                    if (action.includes('Reply') || action.includes('Replied')) {
                        alert('Reply functionality will be implemented soon!');
                    } else if (action.includes('View Project')) {
                        alert('View project functionality will be implemented soon!');
                    } else if (action.includes('View Worker')) {
                        alert('View worker profile functionality will be implemented soon!');
                    }
                }
            });
        });

        function filterReviews() {
            // This function will filter reviews based on selected criteria
            console.log('Filtering reviews...');
            // Implementation will be added for actual filtering
        }
    </script>
</body>

</html>
