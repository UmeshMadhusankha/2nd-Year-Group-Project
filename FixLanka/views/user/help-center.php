<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\help-center.php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/help-center.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="help-container">
        <!-- Header with Home Button -->
        <div class="help-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-question-circle"></i>
                    Help Center
                </h1>
                <p class="page-subtitle">Find answers to frequently asked questions</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2 class="section-title">Frequently Asked Questions</h2>
            
            <div class="faq-list">
                <!-- FAQ Item 1 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span class="question-text">How do I post a job request on Fix Lanka?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Posting a job is simple! First, make sure you're logged into your account. Then, click on the "Post a Job" button on the landing page or navigate to the job posting section. Fill in the required details including the service category, description, location, preferred provider type, and urgency level. You can also upload photos if needed. Once submitted, your job request will be visible to service providers in your area.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span class="question-text">How can I find trusted service providers?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Fix Lanka features only verified and trusted service providers. You can browse through our featured providers on the landing page, or use the search filters to find professionals by service type, rating, and location. Each provider's profile displays their ratings, reviews, and service area to help you make an informed decision.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span class="question-text">Can I edit or cancel my job request?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! You can edit or cancel your job request as long as it's still in "Pending" status. Simply go to "My Jobs" from the navigation menu, find your job request, and use the Edit or Delete buttons. Once a job is in progress or completed, it becomes read-only and cannot be modified or cancelled.</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item">
                    <button class="faq-question">
                        <span class="question-text">What types of services are available on Fix Lanka?</span>
                        <i class="fas fa-chevron-down faq-icon"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Fix Lanka offers a wide range of services including Plumbing, Electrical work, HVAC services, Cleaning, Carpentry, Painting, and many more. Our platform connects you with both individual professionals and registered companies specializing in various home and business services across Sri Lanka.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links Section -->
        <div class="quick-links-section">
            <h3 class="quick-links-title">Still need help?</h3>
            <div class="quick-links-grid">
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php" class="quick-link-card">
                    <i class="fas fa-envelope"></i>
                    <h4>Contact Us</h4>
                    <p>Send us a message</p>
                </a>
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php" class="quick-link-card">
                    <i class="fas fa-file-contract"></i>
                    <h4>Terms of Service</h4>
                    <p>Read our policies</p>
                </a>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/help-center.js"></script>
</body>
</html>
