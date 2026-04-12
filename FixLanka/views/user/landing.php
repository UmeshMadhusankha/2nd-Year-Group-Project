<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\landing.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$isLoggedIn = isLoggedIn();
$aboutContent = fixlanka_static_content_for_page($pdo, 'about');
$landingHeroContent = fixlanka_static_content_for_page($pdo, 'landing_hero');

$heroTitle = 'Find Trusted Service Professionals Near You';
$heroSubtitle = 'Connect with verified local experts for all your home and business needs';
if (!empty($landingHeroContent['body'])) {
    $decoded = json_decode((string)$landingHeroContent['body'], true);
    if (is_array($decoded)) {
        $t = trim((string)($decoded['title'] ?? ''));
        $s = trim((string)($decoded['subtitle'] ?? ''));
        if ($t !== '') {
            $heroTitle = $t;
        }
        if ($s !== '') {
            $heroSubtitle = $s;
        }
    }
}

$aboutExcerpt = '';
if (!empty($aboutContent['body'])) {
    $aboutExcerpt = fixlanka_static_content_excerpt((string)$aboutContent['body'], 220);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Lanka - Your Trusted Service Professionals</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/landing.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/repairer-profile-popup.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/listed-job-request-popup.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/direct-job-request-popup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div class="hero-container">
            <!-- Hero Banner -->
            <div class="hero-banner">
                <div class="hero-content">
                    <h1 class="hero-title"><?php echo htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <p class="hero-subtitle"><?php echo htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?></p>
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
                            <select class="form-select" id="serviceSelect">
                                <option value="">Select Service</option>
                                <option value="plumbing">Plumbing</option>
                                <option value="electrical">Electrical</option>
                                <option value="hvac">HVAC</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="carpentry">Carpentry</option>
                                <option value="painting">Painting</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <select class="form-select" id="ratingSelect">
                                <option value="">All Ratings</option>
                                <option value="5">5 Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3">3+ Stars</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <input type="text" class="form-input" id="locationInput" placeholder="Enter your location">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                                Search
                            </button>
                        </div>
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
                <?php if ($aboutExcerpt !== ''): ?>
                    <p class="footer-text"><?php echo htmlspecialchars($aboutExcerpt, ENT_QUOTES, 'UTF-8'); ?></p>
                    <a class="footer-readmore" href="/2nd-Year-Group-Project/FixLanka/views/user/about-us.php">Read more</a>
                <?php endif; ?>
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

    <!-- Listed Job Request Popup -->
    <?php include 'listed-job-request-popup.php'; ?>

    <!-- Direct New Job Request Popup -->
    <?php include 'direct-job-request-popup.php'; ?>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/landing.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/repairer-profile-popup.js"></script>
</body>
</html>
