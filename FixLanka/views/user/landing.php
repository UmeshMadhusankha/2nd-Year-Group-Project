<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Lanka - Your Trusted Service Professionals</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/landing.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
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
            
            <!-- Search Form -->
            <div class="search-form-container">
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
            
            <div class="providers-grid" id="providersGrid">
                <!-- Provider cards will be dynamically loaded here -->
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
                <p class="footer-tagline">Connecting you with trusted local service professionals.</p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="footer-section footer-services">
                <h3 class="footer-title">Services</h3>
                <ul class="footer-links">
                    <li><a href="#plumbing">Plumbing</a></li>
                    <li><a href="#electrical">Electrical</a></li>
                    <li><a href="#hvac">HVAC</a></li>
                    <li><a href="#cleaning">Cleaning</a></li>
                </ul>
            </div>
            
            <div class="footer-section footer-support">
                <h3 class="footer-title">Support</h3>
                <ul class="footer-links">
                    <li><a href="#help">Help Center</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                    <li><a href="#safety">Safety</a></li>
                    <li><a href="#terms">Terms</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-container">
                <p>&copy; 2025 Fix Lanka. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#privacy">Privacy Policy</a>
                    <span class="separator">|</span>
                    <a href="#terms">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/landing.js"></script>
</body>
</html>
