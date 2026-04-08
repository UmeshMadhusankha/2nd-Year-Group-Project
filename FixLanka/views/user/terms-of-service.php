<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\terms-of-service.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$termsContent = fixlanka_static_content_for_page($pdo, 'terms');
$privacyContent = fixlanka_static_content_for_page($pdo, 'privacy');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/terms-of-service.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
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
            <?php if (!empty($termsContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$termsContent['body']); ?>
            <?php endif; ?>

            <div id="privacy">
                <?php if (!empty($privacyContent['body'])): ?>
                    <?php echo fixlanka_static_content_body_to_html((string)$privacyContent['body']); ?>
                <?php endif; ?>
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
