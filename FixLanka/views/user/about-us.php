<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$aboutContent = fixlanka_static_content_for_page($pdo, 'about');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/terms-of-service.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="terms-container">
        <div class="terms-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-circle-info"></i>
                    About Us
                </h1>
                <p class="page-subtitle">Learn more about Fix Lanka</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <div class="terms-content">
            <?php if (!empty($aboutContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$aboutContent['body']); ?>
            <?php else: ?>
                <p class="text-muted-foreground">Content not available.</p>
            <?php endif; ?>
        </div>

        <div class="terms-footer">
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/help-center.php" class="footer-link">
                <i class="fas fa-question-circle"></i> Help Center
            </a>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/contact-us.php" class="footer-link">
                <i class="fas fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>
</body>
</html>
