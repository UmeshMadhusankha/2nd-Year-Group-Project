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
                                        <span class="rating-number">4.4</span>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span class="rating-count">Based on 5 reviews</span>
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
                                    <h3 class="stat-number">5</h3>
                                    <p class="stat-label">Total Reviews</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-thumbs-up"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">80%</h3>
                                    <p class="stat-label">Positive Reviews</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-reply"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">40%</h3>
                                    <p class="stat-label">Response Rate</p>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <h3 class="stat-number">1.5</h3>
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
                            <span class="section-subtitle">5 reviews total</span>
                        </div>

                        <div class="reviews-list">
                            <!-- Review Item 1 -->
                            <div class="review-item" data-rating="5" data-response="responded">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">Sarah Fernando</h4>
                                            <span class="review-job">Kitchen Sink Repair</span>
                                        </div>
                                    </div>
                                    <div class="review-meta">
                                        <div class="star-rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <span class="rating-number">5.0</span>
                                        </div>
                                        <span class="review-date">2 days ago</span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <p class="review-text">
                                        "Excellent service! John arrived on time and fixed my kitchen sink perfectly. 
                                        Very professional and cleaned up after the work. The pricing was fair and transparent. 
                                        I would definitely recommend him to others and will use his services again."
                                    </p>
                                </div>

                                <div class="review-actions">
                                    <button class="btn btn-primary respond-btn" onclick="openRespondModal(1, 'Sarah Fernando')">
                                        <i class="fas fa-reply"></i>
                                        Respond
                                    </button>
                                </div>
                            </div>

                            <!-- Review Item 2 -->
                            <div class="review-item" data-rating="4" data-response="pending">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">Priya Wickramasinghe</h4>
                                            <span class="review-job">Air Conditioning Repair</span>
                                        </div>
                                    </div>
                                    <div class="review-meta">
                                        <div class="star-rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span class="rating-number">4.0</span>
                                        </div>
                                        <span class="review-date">3 days ago</span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <p class="review-text">
                                        "Good service overall. The AC is working well now. John was knowledgeable and explained 
                                        the issue clearly. Only minor complaint is that he arrived about 15 minutes late, 
                                        but he called ahead to inform me. Would use again."
                                    </p>
                                </div>

                                <div class="review-actions">
                                    <button class="btn btn-primary respond-btn" onclick="openRespondModal(2, 'Priya Wickramasinghe')">
                                        <i class="fas fa-reply"></i>
                                        Respond
                                    </button>
                                </div>
                            </div>

                            <!-- Review Item 3 -->
                            <div class="review-item" data-rating="5" data-response="responded">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">Nimal Perera</h4>
                                            <span class="review-job">Washing Machine Repair</span>
                                        </div>
                                    </div>
                                    <div class="review-meta">
                                        <div class="star-rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <span class="rating-number">5.0</span>
                                        </div>
                                        <span class="review-date">1 week ago</span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <p class="review-text">
                                        "Outstanding work! My washing machine was making terrible noises and not draining properly. 
                                        John diagnosed the problem quickly and had it fixed within an hour. Very reasonable price and 
                                        gave me maintenance tips. Highly recommended!"
                                    </p>
                                </div>

                                <div class="review-actions">
                                    <button class="btn btn-primary respond-btn" onclick="openRespondModal(3, 'Nimal Perera')">
                                        <i class="fas fa-reply"></i>
                                        Respond
                                    </button>
                                </div>
                            </div>

                            <!-- Review Item 4 -->
                            <div class="review-item" data-rating="3" data-response="pending">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">Kamala Silva</h4>
                                            <span class="review-job">Bathroom Plumbing Fix</span>
                                        </div>
                                    </div>
                                    <div class="review-meta">
                                        <div class="star-rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span class="rating-number">3.0</span>
                                        </div>
                                        <span class="review-date">1 week ago</span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <p class="review-text">
                                        "The plumbing issue was fixed, but it took longer than expected. John had to come back 
                                        the next day for additional parts. The final result is good, but the communication 
                                        could have been better about the timeline."
                                    </p>
                                </div>

                                <div class="review-actions">
                                    <button class="btn btn-primary respond-btn" onclick="openRespondModal(4, 'Kamala Silva')">
                                        <i class="fas fa-reply"></i>
                                        Respond
                                    </button>
                                </div>
                            </div>

                            <!-- Review Item 5 -->
                            <div class="review-item" data-rating="5" data-response="pending">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4 class="reviewer-name">Ruwan Jayawardana</h4>
                                            <span class="review-job">Electrical Outlet Installation</span>
                                        </div>
                                    </div>
                                    <div class="review-meta">
                                        <div class="star-rating">
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <span class="rating-number">5.0</span>
                                        </div>
                                        <span class="review-date">2 weeks ago</span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <p class="review-text">
                                        "Perfect electrical work! John installed new outlets in my home office quickly and safely. 
                                        He explained everything he was doing and made sure I was satisfied with the placement. 
                                        Great attention to detail and very professional manner."
                                    </p>
                                </div>

                                <div class="review-actions">
                                    <button class="btn btn-primary respond-btn" onclick="openRespondModal(5, 'Ruwan Jayawardana')">
                                        <i class="fas fa-reply"></i>
                                        Respond
                                    </button>
                                </div>
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/reviews.js"></script>
</body>
</html>

