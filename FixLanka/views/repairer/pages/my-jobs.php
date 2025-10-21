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
    <link rel="stylesheet" href="../../../assets/css/common/global.css">
    <link rel="stylesheet" href="../../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../../assets/css/common/topbar.css">
    <link rel="stylesheet" href="../../../assets/css/common/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/my-jobs.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php include '../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php include '../common/sidebar.php'; ?>
            
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

                    <!-- Jobs Section -->
                    <section class="jobs-section">
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
                    </section>
                </div>
            </main>
        </div>
    </div>

    <script src="../../../assets/javascript/common/common.js"></script>
    <script src="../../../assets/javascript/repairer/my-jobs.js"></script>
</body>
</html>
