<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\terms-of-service.php
require_once __DIR__ . '/../../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/terms-of-service.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="terms-container">
        <!-- Header with Home Button -->
        <div class="terms-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-file-contract"></i>
                    Terms of Service
                </h1>
                <p class="page-subtitle">Last updated: October 22, 2025</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- Terms Content -->
        <div class="terms-content">
            <div class="terms-section">
                <h2 class="section-title">1. Acceptance of Terms</h2>
                <p>By accessing and using Fix Lanka's platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. If you do not agree with any part of these terms, please do not use our services.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">2. User Accounts</h2>
                <p>To access certain features of Fix Lanka, you must create an account. You are responsible for:</p>
                <ul>
                    <li>Maintaining the confidentiality of your account credentials</li>
                    <li>All activities that occur under your account</li>
                    <li>Providing accurate and current information</li>
                    <li>Notifying us immediately of any unauthorized access</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="section-title">3. Service Provider Responsibilities</h2>
                <p>Service providers using Fix Lanka agree to:</p>
                <ul>
                    <li>Provide accurate information about their services and qualifications</li>
                    <li>Maintain professional conduct with all customers</li>
                    <li>Complete agreed-upon work in a timely and professional manner</li>
                    <li>Comply with all applicable local laws and regulations</li>
                    <li>Maintain necessary licenses and insurance as required by law</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="section-title">4. Customer Responsibilities</h2>
                <p>Customers using Fix Lanka agree to:</p>
                <ul>
                    <li>Provide accurate job descriptions and requirements</li>
                    <li>Treat service providers with respect and professionalism</li>
                    <li>Provide safe access to work areas as needed</li>
                    <li>Pay agreed-upon rates for completed services</li>
                    <li>Provide honest and fair reviews of services received</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="section-title">5. Platform Usage</h2>
                <p>Users of Fix Lanka must not:</p>
                <ul>
                    <li>Post false, misleading, or fraudulent information</li>
                    <li>Harass, threaten, or abuse other users</li>
                    <li>Violate any applicable laws or regulations</li>
                    <li>Attempt to circumvent platform fees or policies</li>
                    <li>Use the platform for any illegal or unauthorized purpose</li>
                    <li>Interfere with the proper functioning of the platform</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="section-title">6. Payment and Fees</h2>
                <p>Fix Lanka may charge service fees for using the platform. All fees are non-refundable unless otherwise stated. Payment terms are agreed upon between customers and service providers directly.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">7. Limitation of Liability</h2>
                <p>Fix Lanka acts as a platform connecting customers with service providers. We do not employ service providers and are not responsible for the quality, safety, or legality of services provided. Users agree to hold Fix Lanka harmless from any disputes arising from service arrangements.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">8. Dispute Resolution</h2>
                <p>In the event of disputes between users, Fix Lanka may provide mediation services but is not obligated to do so. Users are encouraged to resolve disputes amicably and may seek legal remedies as appropriate.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">9. Privacy and Data Protection</h2>
                <p>Your privacy is important to us. We collect and use personal information in accordance with applicable data protection laws. By using Fix Lanka, you consent to our collection and use of personal information as described in our Privacy Policy.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">10. Modifications to Terms</h2>
                <p>Fix Lanka reserves the right to modify these Terms of Service at any time. Users will be notified of significant changes via email or platform notifications. Continued use of the platform after changes constitutes acceptance of the modified terms.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">11. Termination</h2>
                <p>Fix Lanka reserves the right to suspend or terminate accounts that violate these Terms of Service or engage in fraudulent, abusive, or illegal activities. Users may also close their accounts at any time.</p>
            </div>

            <div class="terms-section">
                <h2 class="section-title">12. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us:</p>
                <div class="contact-box">
                    <p><i class="fas fa-envelope"></i> Email: support@fixlanka.lk</p>
                    <p><i class="fas fa-phone"></i> Phone: +94 11 234 5678</p>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="terms-footer">
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/help-center.php" class="footer-link">
                <i class="fas fa-question-circle"></i> Help Center
            </a>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php" class="footer-link">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/terms-of-service.js"></script>
</body>
</html>
