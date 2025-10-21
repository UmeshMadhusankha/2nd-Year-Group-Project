<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\provider.php
require_once __DIR__ . '/../../config/session.php';

// Get provider ID from URL (you'll implement this later)
$providerId = $_GET['id'] ?? 1;

// TODO: Fetch provider data from database using Provider model
// For now, using placeholder data
$providerName = "Sarah Johnson";
$providerTitle = "Professional House Cleaner";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($providerName); ?> - <?php echo htmlspecialchars($providerTitle); ?> | Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/provider.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Sticky Header Section -->
    <header class="provider-header" id="providerHeader">
        <div class="provider-header-container">
            <div class="provider-header-info">
                <h1 class="provider-name"><?php echo htmlspecialchars($providerName); ?></h1>
                <div class="provider-rating">
                    <div class="stars">
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                        <i class="fas fa-star star"></i>
                    </div>
                    <span class="rating-text">4.9 (247 reviews)</span>
                </div>
            </div>
            <div class="provider-header-actions">
                <button class="message-btn" id="messageBtn">
                    <i class="fas fa-envelope"></i>
                    Message
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="profile-container">
            <!-- Left Sidebar - Profile Card -->
            <aside class="profile-sidebar">
                <div class="profile-card">
                    <div class="profile-avatar-section">
                        <img src="https://via.placeholder.com/120" alt="<?php echo htmlspecialchars($providerName); ?>" class="profile-avatar-large">
                        <h2 class="profile-title"><?php echo htmlspecialchars($providerTitle); ?></h2>
                    </div>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-phone contact-icon"></i>
                            <div class="contact-info">
                                <span class="contact-label">Phone</span>
                                <span class="contact-value">+94 77 123 4567</span>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-envelope contact-icon"></i>
                            <div class="contact-info">
                                <span class="contact-label">Email</span>
                                <span class="contact-value">sarah.johnson@email.com</span>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt contact-icon"></i>
                            <div class="contact-info">
                                <span class="contact-label">Location</span>
                                <span class="contact-value">Colombo 03, Sri Lanka</span>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <i class="fas fa-clock contact-icon"></i>
                            <div class="contact-info">
                                <span class="contact-label">Availability</span>
                                <span class="contact-value availability-status">Available Today</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Right Column - Main Content -->
            <div class="profile-main">
                <!-- About Me Section -->
                <section class="about-section">
                    <div class="section-card">
                        <h3 class="section-title">About Me</h3>
                        <p class="about-text">
                            I'm Sarah, a dedicated professional house cleaner with over 8 years of experience in providing top-quality cleaning services across Colombo. I take pride in transforming homes into spotless, comfortable spaces for families to enjoy.
                        </p>
                        <p class="about-text">
                            My approach combines attention to detail with eco-friendly cleaning products to ensure your home is not only clean but also safe for your family and pets. I'm fully insured, background-checked, and committed to exceeding your expectations with every visit.
                        </p>
                    </div>
                </section>

                <!-- Services Offered Section -->
                <section class="services-section">
                    <div class="section-card">
                        <h3 class="section-title">Services Offered</h3>
                        <div class="services-grid">
                            <span class="service-tag">Regular Cleaning</span>
                            <span class="service-tag">Deep Cleaning</span>
                            <span class="service-tag">Move-in/Move-out</span>
                            <span class="service-tag">Kitchen Deep Clean</span>
                            <span class="service-tag">Bathroom Sanitization</span>
                            <span class="service-tag">Window Cleaning</span>
                            <span class="service-tag">Carpet Cleaning</span>
                            <span class="service-tag">Post-renovation Cleanup</span>
                        </div>
                    </div>
                </section>

                <!-- Recent Reviews Section -->
                <section class="reviews-section">
                    <div class="section-card">
                        <h3 class="section-title">Recent Reviews</h3>
                        <div class="reviews-container" id="reviewsContainer">
                            <!-- Reviews will be loaded dynamically -->
                        </div>
                        
                        <div class="reviews-toggle">
                            <button class="toggle-reviews-btn" id="toggleReviewsBtn">
                                <span class="toggle-text">Show More Reviews</span>
                                <i class="fas fa-chevron-down toggle-icon"></i>
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <p>&copy; 2025 Fix Lanka. All rights reserved.</p>
        </div>
    </footer>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/provider.js"></script>
</body>
</html>
