<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\profile.php
require_once __DIR__ . '/../../config/session.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/profile.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title-section">
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-subtitle">Manage your account information and preferences</p>
                </div>
                <div class="page-actions">
                    <button class="btn-primary" id="editProfileBtn">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Profile Grid -->
            <div class="profile-grid">
                <!-- User Info Card -->
                <div class="profile-card user-info-card">
                    <div class="card-content">
                        <div class="profile-avatar-section">
                            <div class="profile-avatar-container">
                                <img src="https://via.placeholder.com/120" alt="Profile" class="profile-avatar" id="profileAvatar">
                                <div class="avatar-overlay" id="avatarOverlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Change Photo</span>
                                </div>
                            </div>
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                            <div class="profile-status">
                                <span class="status-badge verified">
                                    <i class="fas fa-check-circle"></i>
                                    Verified
                                </span>
                            </div>
                        </div>
                        
                        <div class="user-details">
                            <h2 class="user-full-name">John Doe</h2>
                            <p class="user-username">@johndoe</p>
                            
                            <div class="contact-info">
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>john.doe@email.com</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span>+94 77 123 4567</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Colombo, Sri Lanka</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Joined December 2023</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Completion Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i>
                            Account Completion
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="completion-stats">
                            <div class="completion-circle">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring-circle-bg" cx="60" cy="60" r="52"></circle>
                                    <circle class="progress-ring-circle" cx="60" cy="60" r="52" id="progressCircle"></circle>
                                </svg>
                                <div class="completion-percentage">
                                    <span class="percentage-value" id="completionPercentage">85</span>
                                    <span class="percentage-symbol">%</span>
                                </div>
                            </div>
                            
                            <div class="completion-tasks">
                                <div class="task-item completed">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Profile picture uploaded</span>
                                </div>
                                <div class="task-item completed">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Contact information verified</span>
                                </div>
                                <div class="task-item completed">
                                    <i class="fas fa-check-circle"></i>
                                    <span>First job posted</span>
                                </div>
                                <div class="task-item pending">
                                    <i class="far fa-circle"></i>
                                    <span>Payment method added</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="action-buttons">
                            <button class="action-btn" id="manageProfileBtn">
                                <div class="action-icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Manage Profile</span>
                                    <span class="action-subtitle">Update information</span>
                                </div>
                            </button>
                            
                            <button class="action-btn" id="viewQuotesBtn">
                                <div class="action-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">View Quotes</span>
                                    <span class="action-subtitle">3 new quotes</span>
                                </div>
                                <span class="action-badge">3</span>
                            </button>
                            
                            <button class="action-btn" id="manageReviewsBtn">
                                <div class="action-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Manage Reviews</span>
                                    <span class="action-subtitle">View and respond</span>
                                </div>
                            </button>
                            
                            <button class="action-btn" id="paymentHistoryBtn">
                                <div class="action-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Payment History</span>
                                    <span class="action-subtitle">View transactions</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Job Overview Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-briefcase"></i>
                            Job Overview
                        </h3>
                        <button class="view-all-btn" id="viewAllJobsBtn">View All</button>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon total">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">12</span>
                                    <span class="stat-label">Total Jobs</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon active">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">3</span>
                                    <span class="stat-label">Active Jobs</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">8</span>
                                    <span class="stat-label">Completed</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon pending">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number">1</span>
                                    <span class="stat-label">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i>
                            Recent Activity
                        </h3>
                        <button class="view-all-btn" id="viewAllActivityBtn">View All</button>
                    </div>
                    <div class="card-content">
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon job-posted">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">Posted job: <strong>Kitchen Renovation</strong></p>
                                    <span class="activity-time">2 hours ago</span>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon agreement">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">Sent agreement to <strong>Kasun Silva</strong></p>
                                    <span class="activity-time">5 hours ago</span>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon payment">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">Completed payment for <strong>Plumbing</strong></p>
                                    <span class="activity-time">1 day ago</span>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <div class="activity-icon review">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">Received 5-star review</p>
                                    <span class="activity-time">2 days ago</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Summary Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-star"></i>
                            Reviews Summary
                        </h3>
                        <button class="view-all-btn" id="viewAllReviewsBtn">View All</button>
                    </div>
                    <div class="card-content">
                        <div class="reviews-summary">
                            <div class="rating-overview">
                                <div class="average-rating">
                                    <span class="rating-value">4.8</span>
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="rating-count">Based on 23 reviews</span>
                                </div>
                            </div>
                            
                            <div class="rating-breakdown">
                                <div class="rating-row">
                                    <span class="star-label">5★</span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: 78%"></div>
                                    </div>
                                    <span class="rating-number">18</span>
                                </div>
                                <div class="rating-row">
                                    <span class="star-label">4★</span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: 17%"></div>
                                    </div>
                                    <span class="rating-number">4</span>
                                </div>
                                <div class="rating-row">
                                    <span class="star-label">3★</span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: 4%"></div>
                                    </div>
                                    <span class="rating-number">1</span>
                                </div>
                                <div class="rating-row">
                                    <span class="star-label">2★</span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: 0%"></div>
                                    </div>
                                    <span class="rating-number">0</span>
                                </div>
                                <div class="rating-row">
                                    <span class="star-label">1★</span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: 0%"></div>
                                    </div>
                                    <span class="rating-number">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quotes Received Card -->
                <div class="profile-card quotes-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Quotes Received
                        </h3>
                        <span class="quotes-badge" id="quotesBadge" style="display:none;">0 new</span>
                    </div>
                    <div class="card-content">
                        <div class="quotes-list" id="quotesList">
                            <div class="quote-item">
                                <div class="quote-details">
                                    <span class="quote-job">Loading...</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="quotes-footer">
                            <a class="btn-secondary" id="viewAllQuotesBtn" href="/2nd-Year-Group-Project/FixLanka/views/user/quotes_received.php">
                                View All Quotes
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Profile Modal -->
    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Profile</h3>
                <button class="modal-close" id="closeEditModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="editProfileForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editFullName">Full Name <span class="required">*</span></label>
                            <input type="text" id="editFullName" class="form-input" value="John Doe" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="editUsername">Username <span class="required">*</span></label>
                            <input type="text" id="editUsername" class="form-input" value="johndoe" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editEmail">Email <span class="required">*</span></label>
                            <input type="email" id="editEmail" class="form-input" value="john.doe@email.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="editPhone">Phone <span class="required">*</span></label>
                            <input type="tel" id="editPhone" class="form-input" value="+94 77 123 4567" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editLocation">Location <span class="required">*</span></label>
                        <input type="text" id="editLocation" class="form-input" value="Colombo, Sri Lanka" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editBio">Bio</label>
                        <textarea id="editBio" class="form-textarea" rows="4" placeholder="Tell us about yourself..."></textarea>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <i class="fas fa-check-circle toast-icon"></i>
            <span class="toast-message" id="toastMessage">Success!</span>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/profile.js"></script>
</body>
</html>
