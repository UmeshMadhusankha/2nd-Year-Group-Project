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
    <link rel="stylesheet" href="../../../assets/css/common/global.css">
    <link rel="stylesheet" href="../../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../../assets/css/common/topbar.css">
    <link rel="stylesheet" href="../../../assets/css/common/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/available-jobs.css">
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
                                    <span class="header-stat-number">24</span>
                                    <span class="header-stat-label">New Jobs</span>
                                </div>
                                <div class="header-stat">
                                    <span class="header-stat-number">156</span>
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

                    <!-- Jobs Section -->
                    <section class="jobs-section">
                        <div class="section-header">
                            <h2 class="section-title">Available Jobs</h2>
                            <span class="section-subtitle">6 jobs match your criteria</span>
                        </div>

                        <div class="jobs-grid">
                            <!-- Job Card 1 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge plumbing">
                                        <i class="fas fa-wrench"></i>
                                        Plumbing
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        2 hours ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Kitchen Sink Repair</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-user"></i>
                                        <span>Sarah Fernando</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Colombo</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>No. 45, Galle Road, Colombo 07</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-wrench"></i>
                                        <span>Plumbing</span>
                                    </div>
                                    <div class="job-urgency urgent">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Urgent</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Tomorrow, 2:00 PM - 4:00 PM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(1)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(1)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Job Card 2 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge electrical">
                                        <i class="fas fa-bolt"></i>
                                        Electrical
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        4 hours ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Ceiling Fan Installation</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-building"></i>
                                        <span>ABC Trading Company</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Kandy</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>123, Peradeniya Road, Kandy</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-bolt"></i>
                                        <span>Electrical</span>
                                    </div>
                                    <div class="job-urgency normal">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Normal</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Sept 3, 9:00 AM - 12:00 PM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(2)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(2)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Job Card 3 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge appliance">
                                        <i class="fas fa-tv"></i>
                                        Appliance
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        6 hours ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Washing Machine Repair</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-user"></i>
                                        <span>Nimal Perera</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Colombo</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>78, High Level Road, Nugegoda</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-tv"></i>
                                        <span>Appliance Repair</span>
                                    </div>
                                    <div class="job-urgency urgent">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Urgent</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Sept 4, 3:00 PM - 5:00 PM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(3)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(3)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Job Card 4 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge hvac">
                                        <i class="fas fa-snowflake"></i>
                                        HVAC
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        1 day ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Air Conditioner Service</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-user"></i>
                                        <span>Kamala Silva</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Gampaha</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>56, Yakkala Road, Gampaha</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-snowflake"></i>
                                        <span>HVAC</span>
                                    </div>
                                    <div class="job-urgency normal">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Normal</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Sept 5, 10:00 AM - 1:00 PM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(4)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(4)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Job Card 5 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge carpentry">
                                        <i class="fas fa-hammer"></i>
                                        Carpentry
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        1 day ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Cabinet Door Repair</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-user"></i>
                                        <span>Rajesh Kumar</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Colombo</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>34, Beach Road, Mount Lavinia</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-hammer"></i>
                                        <span>Carpentry</span>
                                    </div>
                                    <div class="job-urgency normal">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Normal</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Sept 6, 8:00 AM - 11:00 AM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(5)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(5)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
                                    </button>
                                </div>
                            </div>

                            <!-- Job Card 6 -->
                            <div class="job-card">
                                <div class="job-header">
                                    <div class="job-category-badge painting">
                                        <i class="fas fa-paint-brush"></i>
                                        Painting
                                    </div>
                                    <div class="job-posted">
                                        <i class="fas fa-clock"></i>
                                        2 days ago
                                    </div>
                                </div>
                                
                                <div class="job-content">
                                    <h3 class="job-title">Room Wall Painting</h3>
                                    <div class="job-customer">
                                        <i class="fas fa-user"></i>
                                        <span>Priya Wickramasinghe</span>
                                    </div>
                                    <div class="job-district">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Kurunegala</span>
                                    </div>
                                    <div class="job-address">
                                        <i class="fas fa-location-dot"></i>
                                        <span>89, Colombo Road, Kurunegala</span>
                                    </div>
                                    <div class="job-type">
                                        <i class="fas fa-paint-brush"></i>
                                        <span>Painting</span>
                                    </div>
                                    <div class="job-urgency urgent">
                                        <i class="fas fa-exclamation-circle"></i>
                                        <span>Urgent</span>
                                    </div>
                                    <div class="job-date">
                                        <i class="fas fa-calendar"></i>
                                        <span>Sept 7-8, 9:00 AM - 5:00 PM</span>
                                    </div>
                                </div>

                                <div class="job-actions">
                                    <button class="btn btn-secondary job-btn" onclick="viewJobDetails(6)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-primary job-btn" onclick="submitQuote(6)">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        Submit Quote
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
    <script src="../../../assets/javascript/repairer/available-jobs.js"></script>
</body>
</html>
