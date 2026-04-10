<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$supportContent = fixlanka_static_content_for_page($pdo, 'support');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/support.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
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

        <?php if (!empty($supportContent['body'])): ?>
            <div id="support-content">
            <?php echo fixlanka_static_content_body_to_html((string)$supportContent['body']); ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/support.js"></script>
</body>
</html>
