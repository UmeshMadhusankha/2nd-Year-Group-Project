<?php
// Page configuration
$currentPage = 'welcome';
$pageTitle = 'Welcome to FixLanka';
$pageSubtitle = 'Your trusted repair network dashboard';
$searchPlaceholder = 'Search requests, repairers, projects...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/welcome.css">
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
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <div class="welcome-header">
                        <div class="welcome-text">
                            <h1 class="welcome-title">Welcome back, <span class="repairer-name">John Doe</span>!</h1>
                            <p class="welcome-subtitle">Ready to help more customers today? Here's your current overview.</p>
                        </div>
                        <div class="welcome-actions">
                            <button class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Update Availability
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Quick Stats Cards -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card active-jobs">
                            <div class="stat-icon">
                                <i class="fas fa-hammer"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">5</h3>
                                <p class="stat-label">Active Jobs</p>
                            </div>
                        </div>

                        <div class="stat-card pending-quotes">
                            <div class="stat-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">8</h3>
                                <p class="stat-label">Pending Quotes</p>
                            </div>
                        </div>

                        <div class="stat-card total-earnings">
                            <div class="stat-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-number">LKR 45,200</h3>
                                <p class="stat-label">Total Earnings</p>
                            </div>
                        </div>

                        <div class="stat-card availability-status">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3 class="stat-status available">Available</h3>
                                <p class="stat-label">Current Status</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions Section -->
                <section class="quick-actions-section">
                    <div class="section-header">
                        <h2 class="section-title">Quick Actions</h2>
                        <span class="section-subtitle">Common tasks at your fingertips</span>
                    </div>

                    <div class="quick-actions-grid">
                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-available-jobs" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">Browse Available Jobs</h3>
                                <p class="action-description">Find repair requests from customers</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-my-jobs" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">My Jobs</h3>
                                <p class="action-description">Manage your current repair tasks</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-company-jobs" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">Company Jobs</h3>
                                <p class="action-description">Browse side projects from companies</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-subscription" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">Upgrade to Pro</h3>
                                <p class="action-description">Unlock premium features for your business</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-earnings" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">View Earnings</h3>
                                <p class="action-description">Track your income and payments</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>

                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-profile" class="action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="action-content">
                                <h3 class="action-title">Update Profile</h3>
                                <p class="action-description">Edit your skills and availability</p>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                </section>

                <!-- Recent Activity -->
                <section class="activity-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Activity</h2>
                        <a href="/2nd-Year-Group-Project/FixLanka/repairer-my-jobs" class="view-all-link">View All <i class="fas fa-arrow-right"></i></a>
                    </div>

                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">Job Completed</h4>
                                <p class="activity-description">Plumbing repair at Colombo 07 - Customer paid LKR 2,500</p>
                                <span class="activity-time">2 hours ago</span>
                            </div>
                            <div class="activity-status completed">
                                <span>Completed</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">Quote Submitted</h4>
                                <p class="activity-description">Electrical repair quote for Kandy - LKR 1,800</p>
                                <span class="activity-time">5 hours ago</span>
                            </div>
                            <div class="activity-status pending">
                                <span>Pending</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">New Review Received</h4>
                                <p class="activity-description">5-star review from Nimal Perera - "Excellent service!"</p>
                                <span class="activity-time">1 day ago</span>
                            </div>
                            <div class="activity-status review">
                                <span>5 ★</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">Job Accepted</h4>
                                <p class="activity-description">AC repair at Nugegoda - Starting tomorrow 9:00 AM</p>
                                <span class="activity-time">1 day ago</span>
                            </div>
                            <div class="activity-status accepted">
                                <span>Accepted</span>
                            </div>
                        </div>

                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="activity-content">
                                <h4 class="activity-title">Payment Received</h4>
                                <p class="activity-description">Appliance repair payment - LKR 3,200 credited to account</p>
                                <span class="activity-time">2 days ago</span>
                            </div>
                            <div class="activity-status payment">
                                <span>Paid</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            </main>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/welcome.js"></script>
</body>
</html>

