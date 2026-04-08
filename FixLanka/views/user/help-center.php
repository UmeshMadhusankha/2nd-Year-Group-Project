<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\help-center.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$helpContent = fixlanka_static_content_for_page($pdo, 'help');
$faqContent = fixlanka_static_content_for_page($pdo, 'faq');

$helpExcerpt = '';
if (!empty($helpContent['body'])) {
    $helpExcerpt = fixlanka_static_content_excerpt((string)$helpContent['body'], 140);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/help-center.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
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
                <p class="page-subtitle">
                    <?php echo htmlspecialchars($helpExcerpt !== '' ? $helpExcerpt : 'Find answers to frequently asked questions', ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section" id="faq">
            <h2 class="section-title">Frequently Asked Questions</h2>
            
            <div class="faq-list">
                <?php if (!empty($faqContent['body'])): ?>
                    <?php echo fixlanka_static_content_faq_body_to_html((string)$faqContent['body']); ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Links Section -->
        <div class="quick-links-section" id="quick-links">
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
