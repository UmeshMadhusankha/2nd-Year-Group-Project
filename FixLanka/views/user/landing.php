<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\landing.php
require_once __DIR__ . '/../../config/session.php';

$isLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Lanka - Your Trusted Service Professionals</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/landing.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/repairer-profile-popup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <!-- Hero Banner -->
            <div class="hero-banner">
                <div class="hero-content">
                    <h1 class="hero-title">Find Trusted Service Professionals Near You</h1>
                    <p class="hero-subtitle">Connect with verified local experts for all your home and business needs</p>
                </div>
            </div>
            
            <!-- Search Form with Post Job Button -->
            <div class="search-form-container">
                <?php if ($isLoggedIn): ?>
                    <!-- Quick Actions for Logged In Users -->
                    <div class="quick-actions">
                        <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="post-job-btn">
                            <i class="fas fa-plus-circle"></i>
                            Post a Job
                        </a>
                        <a href="/2nd-Year-Group-Project/FixLanka/job-history" class="view-jobs-btn">
                            <i class="fas fa-list-alt"></i>
                            My Jobs
                        </a>
                    </div>
                <?php endif; ?>
                
                <form class="search-form" id="searchForm">
                    <div class="form-row">
                        <div class="form-group">
                            <select class="form-select" id="serviceSelect" name="service">
                                <option value="">All Services</option>
                                <option value="1">Plumbing</option>
                                <option value="2">Electrical</option>
                                <option value="3">HVAC</option>
                                <option value="4">Cleaning</option>
                                <option value="5">Carpentry</option>
                                <option value="6">Painting</option>
                                <option value="7">Appliance Repair</option>
                                <option value="8">Roofing</option>
                                <option value="9">Landscaping</option>
                                <option value="10">Pest Control</option>
                                <option value="11">Home Security</option>
                                <option value="12">Interior Design</option>
                                <option value="13">Flooring</option>
                                <option value="14">Masonry</option>
                                <option value="15">Welding</option>
                                <option value="16">Glass & Mirror</option>
                                <option value="17">Tile Work</option>
                                <option value="18">Drywall</option>
                                <option value="19">Insulation</option>
                                <option value="20">Window Installation</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <select class="form-select" id="districtSelect" name="district">
                                <option value="">All Districts</option>
                                <option value="Colombo">Colombo</option>
                                <option value="Gampaha">Gampaha</option>
                                <option value="Kalutara">Kalutara</option>
                                <option value="Kandy">Kandy</option>
                                <option value="Matale">Matale</option>
                                <option value="Nuwara Eliya">Nuwara Eliya</option>
                                <option value="Galle">Galle</option>
                                <option value="Matara">Matara</option>
                                <option value="Hambantota">Hambantota</option>
                                <option value="Jaffna">Jaffna</option>
                                <option value="Kilinochchi">Kilinochchi</option>
                                <option value="Mannar">Mannar</option>
                                <option value="Vavuniya">Vavuniya</option>
                                <option value="Mullaitivu">Mullaitivu</option>
                                <option value="Batticaloa">Batticaloa</option>
                                <option value="Ampara">Ampara</option>
                                <option value="Trincomalee">Trincomalee</option>
                                <option value="Kurunegala">Kurunegala</option>
                                <option value="Puttalam">Puttalam</option>
                                <option value="Anuradhapura">Anuradhapura</option>
                                <option value="Polonnaruwa">Polonnaruwa</option>
                                <option value="Badulla">Badulla</option>
                                <option value="Moneragala">Moneragala</option>
                                <option value="Ratnapura">Ratnapura</option>
                                <option value="Kegalle">Kegalle</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <select class="form-select" id="ratingSelect" name="rating">
                                <option value="">All Ratings</option>
                                <option value="4.5">4.5+ Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3.5">3.5+ Stars</option>
                                <option value="3">3+ Stars</option>
                            </select>
                        </div>
                        
                        <div class="form-group form-actions">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-filter"></i>
                                Apply Filters
                            </button>
                            <button type="button" class="clear-btn" id="clearFiltersBtn">
                                <i class="fas fa-times"></i>
                                Clear
                            </button>
                        </div>
                    </div>
                    <div class="active-filters" id="activeFilters" style="display: none;">
                        <span class="filter-label">Active Filters:</span>
                        <div class="filter-tags" id="filterTags"></div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Service Providers Section -->
    <section class="providers-section">
        <div class="providers-container">
            <div class="section-header">
                <h2 class="section-title">Featured Service Providers</h2>
                <p class="section-subtitle">Discover trusted professionals in your area</p>
            </div>
            
            <!-- Provider Type Tabs -->
            <div class="provider-tabs">
                <button class="provider-tab active" data-type="repairers">
                    <i class="fas fa-user-tie"></i>
                    <span>Individual Repairers</span>
                </button>
                <button class="provider-tab" data-type="companies">
                    <i class="fas fa-building"></i>
                    <span>Companies</span>
                </button>
            </div>
            
            <!-- Repairers Grid -->
            <div class="providers-grid active" id="repairersGrid" data-type="repairers">
                <!-- Repairer cards will be dynamically loaded here -->
            </div>
            
            <!-- Companies Grid -->
            <div class="providers-grid" id="companiesGrid" data-type="companies">
                <!-- Company cards will be dynamically loaded here -->
            </div>
            
            <!-- Loading indicator -->
            <div class="loading-indicator" id="loadingIndicator">
                <div class="loading-spinner"></div>
                <p>Loading more providers...</p>
            </div>
            
            <!-- Intersection observer trigger -->
            <div class="scroll-trigger" id="scrollTrigger"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section footer-branding">
                <div class="footer-logo">
                    <span class="logo-text">Fix Lanka</span>
                </div>
                <p class="footer-tagline">Connecting you with trusted local service professionals across Sri Lanka.</p>
                <p class="footer-description">Fix Lanka is your one-stop platform for finding reliable and verified service providers for all your home and business needs. We ensure quality service delivery through our network of skilled professionals.</p>
            </div>
            
            <div class="footer-section footer-about">
                <h3 class="footer-title">About Us</h3>
                <p class="footer-text">Fix Lanka was established to bridge the gap between customers and quality service providers in Sri Lanka. Our mission is to make finding trusted professionals simple, fast, and reliable.</p>
            </div>
            
            <div class="footer-section footer-support">
                <h3 class="footer-title">Support</h3>
                <ul class="footer-links">
                    <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/help-center.php">Help Center</a></li>
                    <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php">Contact Us</a></li>
                    <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php">Terms of Service</a></li>
                </ul>
                <div class="footer-contact-info">
                    <p><i class="fas fa-envelope"></i> support@fixlanka.lk</p>
                    <p><i class="fas fa-phone"></i> +94 11 234 5678</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <p>&copy; 2025 Fix Lanka. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Repairer Profile Popup -->
    <?php include 'repairer-profile-popup.php'; ?>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/landing.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/repairer-profile-popup.js"></script>
</body>
</html>
