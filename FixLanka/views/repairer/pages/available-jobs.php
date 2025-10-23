<?php
// Page configuration
$currentPage = 'available-jobs';
$pageTitle = 'Available Jobs';
$pageSubtitle = 'Find new repair requests in your area';
$searchPlaceholder = 'Search jobs, customers, locations...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Jobs - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/available-jobs.css">
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
                                <h1 class="page-header-title">Available Jobs</h1>
                                <p class="page-header-subtitle">Browse and apply for repair jobs in your area</p>
                            </div>
                            <div class="page-header-stats">
                                <div class="header-stat">
                                    <span class="header-stat-number" id="new-jobs-count">0</span>
                                    <span class="header-stat-label">New Jobs</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number" id="total-jobs-count">0</span>
                                    <span class="header-stat-label">Total Available</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filters Section -->
                    <section class="filters-section">
                        <div class="filters-container">
                            <div class="filter-group">
                                <label for="category-filter" class="filter-label">Category</label>
                                <select id="category-filter" class="filter-select">
                                    <option value="">All Categories</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="appliance">Appliance Repair</option>
                                    <option value="hvac">HVAC</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="painting">Painting</option>
                                    <option value="general">General Maintenance</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="location-filter" class="filter-label">Location</label>
                                <select id="location-filter" class="filter-select">
                                    <option value="">All Districts</option>
                                    <option value="colombo">Colombo</option>
                                    <option value="gampaha">Gampaha</option>
                                    <option value="kalutara">Kalutara</option>
                                    <option value="kandy">Kandy</option>
                                    <option value="matale">Matale</option>
                                    <option value="nuwara-eliya">Nuwara Eliya</option>
                                    <option value="galle">Galle</option>
                                    <option value="matara">Matara</option>
                                    <option value="hambantota">Hambantota</option>
                                    <option value="jaffna">Jaffna</option>
                                    <option value="kilinochchi">Kilinochchi</option>
                                    <option value="mannar">Mannar</option>
                                    <option value="vavuniya">Vavuniya</option>
                                    <option value="mullaitivu">Mullaitivu</option>
                                    <option value="batticaloa">Batticaloa</option>
                                    <option value="ampara">Ampara</option>
                                    <option value="trincomalee">Trincomalee</option>
                                    <option value="kurunegala">Kurunegala</option>
                                    <option value="puttalam">Puttalam</option>
                                    <option value="anuradhapura">Anuradhapura</option>
                                    <option value="polonnaruwa">Polonnaruwa</option>
                                    <option value="badulla">Badulla</option>
                                    <option value="monaragala">Monaragala</option>
                                    <option value="ratnapura">Ratnapura</option>
                                    <option value="kegalle">Kegalle</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="sort-filter" class="filter-label">Sort by</label>
                                <select id="sort-filter" class="filter-select">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="pay-high">Highest Pay</option>
                                    <option value="pay-low">Lowest Pay</option>
                                    <option value="distance">Distance</option>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button class="btn-filter btn-primary">
                                    <i class="fas fa-filter"></i>
                                    Apply Filters
                                </button>
                                <button class="btn-filter btn-secondary">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Tabs Navigation -->
                    <section class="tabs-section">
                        <div class="tabs-container">
                            <button class="tab-button active" data-tab="available-jobs">
                                <i class="fas fa-briefcase"></i>
                                Available Jobs
                                <span class="tab-badge" id="available-jobs-badge">0</span>
                            </button>
                            <button class="tab-button" data-tab="submitted-quotes">
                                <i class="fas fa-file-invoice"></i>
                                My Quotations
                                <span class="tab-badge" id="quotes-count-badge">0</span>
                            </button>
                        </div>
                    </section>

                    <!-- Tab Content: Available Jobs -->
                    <div class="tab-content active" id="available-jobs-tab">
                        <!-- Jobs Section -->
                        <section class="jobs-section">
                            <div class="section-header">
                                <h2 class="section-title">Available Jobs</h2>
                                <span class="section-subtitle" id="jobs-count">Loading...</span>
                            </div>

                            <div class="jobs-grid" id="jobs-grid-container">
                                <!-- Jobs will be loaded dynamically via JavaScript -->
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading available jobs...</p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- End Available Jobs Tab -->

                    <!-- Tab Content: Submitted Quotations -->
                    <div class="tab-content" id="submitted-quotes-tab">
                        <section class="submitted-quotes-section">
                            <div class="section-header">
                                <h2 class="section-title">
                                    <i class="fas fa-file-invoice"></i>
                                    My Submitted Quotations
                                </h2>
                                <span class="section-subtitle" id="quotes-count">Loading...</span>
                            </div>

                            <div class="quotes-container" id="submitted-quotes-container">
                                <!-- Quotations will be loaded dynamically -->
                                <div class="loading-state">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading your quotations...</p>
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- End Submitted Quotations Tab -->
                </div>
            </main>
        </div>
    </div>

    <!-- Job Details Drawer -->
    <div class="drawer" id="jobDetailsDrawer">
        <div class="drawer-overlay" onclick="closeJobDetails()"></div>
        <div class="drawer-content">
            <div class="drawer-header">
                <h3><i class="fas fa-briefcase"></i> Job Details</h3>
                <button class="drawer-close" onclick="closeJobDetails()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <!-- Job Header -->
                <div class="job-detail-header">
                    <div class="job-detail-category" id="detailCategory">
                        <i class="fas fa-wrench"></i>
                        <span>Plumbing</span>
                    </div>
                    <div class="job-detail-urgency" id="detailUrgency">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>High Priority</span>
                    </div>
                </div>

                <h2 class="job-detail-title" id="detailTitle">Kitchen Sink Repair</h2>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-user"></i> Customer Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Name</span>
                            <span class="detail-value" id="detailCustomerName">Sarah Fernando</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Posted</span>
                            <span class="detail-value" id="detailPosted">2 hours ago</span>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-map-marker-alt"></i> Location</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Full Address</span>
                            <span class="detail-value" id="detailAddress">No. 45, Galle Road, Colombo 07, Western Province</span>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-calendar"></i> Schedule</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Preferred Date & Time</span>
                            <span class="detail-value" id="detailSchedule">Tomorrow, 2:00 PM - 4:00 PM</span>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="detail-section">
                    <h4><i class="fas fa-file-alt"></i> Job Description</h4>
                    <p class="detail-description" id="detailDescription">
                        The kitchen sink is leaking from the pipe connection underneath. Water is dripping constantly and has created a puddle. The sink was installed about 5 years ago. Need urgent repair to prevent water damage to the cabinet.
                    </p>
                </div>

                <!-- Attachments -->
                <div class="detail-section">
                    <h4><i class="fas fa-paperclip"></i> Attachments</h4>
                    <div class="attachments-grid" id="detailAttachments">
                        <div class="attachment-item">
                            <i class="fas fa-image"></i>
                            <span>sink-leak.jpg</span>
                        </div>
                        <div class="attachment-item">
                            <i class="fas fa-image"></i>
                            <span>pipe-close-up.jpg</span>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Additional Information</h4>
                    <div class="detail-list">
                        <div class="detail-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span id="detailInfo1">Customer will provide necessary materials</span>
                        </div>
                        <div class="detail-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span id="detailInfo2">Parking available on premises</span>
                        </div>
                        <div class="detail-list-item">
                            <i class="fas fa-check-circle"></i>
                            <span id="detailInfo3">Customer prefers afternoon appointments</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeJobDetails()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-primary" onclick="submitQuoteFromDetails()">
                    <i class="fas fa-file-invoice-dollar"></i> Submit Quote
                </button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/available-jobs.js"></script>
</body>
</html>

