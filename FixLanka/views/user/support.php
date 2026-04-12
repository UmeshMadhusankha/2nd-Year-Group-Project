<?php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/support.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="support-container">
        <!-- Header with Home Button -->
        <div class="support-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-headset"></i> Support Center
                </h1>
                <p class="page-subtitle">We're here to help you</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- Quick Links Section -->
        <div class="quick-links-section">
            <h2 class="section-title">Quick Access</h2>
            <div class="quick-links-grid">
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/help-center.php" class="quick-link-card">
                    <div class="link-icon help">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3>Help Center</h3>
                    <p>Browse frequently asked questions and find answers</p>
                </a>

                <a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php" class="quick-link-card">
                    <div class="link-icon contact">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Contact Us</h3>
                    <p>Get in touch with our support team directly</p>
                </a>

                <a href="/2nd-Year-Group-Project/FixLanka/views/user/terms-of-service.php" class="quick-link-card">
                    <div class="link-icon terms">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>Terms of Service</h3>
                    <p>Read our terms and conditions for using Fix Lanka</p>
                </a>
            </div>
        </div>

        <!-- Contact Methods Section -->
        <div class="contact-methods-section">
            <h2 class="section-title">Get in Touch</h2>
            <div class="contact-grid">
                <div class="contact-method">
                    <div class="method-icon email">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Support</h3>
                    <p class="method-value">support@fixlanka.lk</p>
                    <p class="method-description">Response within 24 hours</p>
                </div>

                <div class="contact-method">
                    <div class="method-icon phone">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Phone Support</h3>
                    <p class="method-value">+94 11 234 5678</p>
                    <p class="method-description">Mon - Fri, 9:00 AM - 6:00 PM</p>
                </div>

                <div class="contact-method">
                    <div class="method-icon location">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Office Location</h3>
                    <p class="method-value">Colombo, Sri Lanka</p>
                    <p class="method-description">Visit us for in-person support</p>
                </div>
            </div>
        </div>

        <!-- Support Topics Section -->
        <div class="support-topics-section">
            <h2 class="section-title">Popular Topics</h2>
            <div class="topics-grid">
                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h4>Account Management</h4>
                    <ul class="topic-list">
                        <li>Creating an account</li>
                        <li>Profile settings</li>
                        <li>Password recovery</li>
                        <li>Account verification</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h4>Job Requests</h4>
                    <ul class="topic-list">
                        <li>Posting a job</li>
                        <li>Editing requests</li>
                        <li>Cancellation policy</li>
                        <li>Request tracking</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h4>Payments & Billing</h4>
                    <ul class="topic-list">
                        <li>Payment methods</li>
                        <li>Pricing information</li>
                        <li>Refund policy</li>
                        <li>Invoice queries</li>
                    </ul>
                </div>

                <div class="topic-card">
                    <div class="topic-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Safety & Security</h4>
                    <ul class="topic-list">
                        <li>Verified providers</li>
                        <li>Data protection</li>
                        <li>Reporting issues</li>
                        <li>Trust & safety</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Emergency Support Banner -->
        <div class="emergency-banner">
            <div class="emergency-content">
                <div class="emergency-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="emergency-text">
                    <h3>Need Urgent Help?</h3>
                    <p>For emergency support or urgent issues, please call our hotline immediately</p>
                </div>
                <a href="tel:+94112345678" class="emergency-button">
                    <i class="fas fa-phone"></i> Call Now
                </a>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/support.js"></script>
</body>
</html>
