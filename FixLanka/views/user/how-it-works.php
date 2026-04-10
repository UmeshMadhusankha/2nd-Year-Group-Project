<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$howItWorksContent = fixlanka_static_content_for_page($pdo, 'how_it_works');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/how-it-works.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="how-it-works-container">
        <!-- Header with Home Button -->
        <div class="how-it-works-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-info-circle"></i> How It Works
                </h1>
                <p class="page-subtitle">Simple steps to get professional service</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <div id="how-it-works-content">
            <?php if (!empty($howItWorksContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$howItWorksContent['body']); ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/how-it-works.js"></script>
</body>
</html>
