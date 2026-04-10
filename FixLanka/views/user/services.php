<?php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/static_content.php';

$servicesContent = fixlanka_static_content_for_page($pdo, 'services');
$whyChooseContent = fixlanka_static_content_for_page($pdo, 'why_choose');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/services.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/static-content.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="services-container">
        <!-- Header with Home Button -->
        <div class="services-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-tools"></i> Our Services
                </h1>
                <p class="page-subtitle">Professional services for all your needs</p>
            </div>
            <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                <i class="fas fa-home"></i> Home
            </a>
        </div>

        <!-- Services Grid -->
        <div class="services-grid" id="services">
            <?php if (!empty($servicesContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$servicesContent['body']); ?>
            <?php endif; ?>
        </div>

        <!-- Why Choose Us Section -->
        <div class="why-choose-section" id="why-choose">
            <h2 class="section-title">Why Choose Fix Lanka?</h2>
            <?php if (!empty($whyChooseContent['body'])): ?>
                <?php echo fixlanka_static_content_body_to_html((string)$whyChooseContent['body']); ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/services.js"></script>
</body>
</html>
