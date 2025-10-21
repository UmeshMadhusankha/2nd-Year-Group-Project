<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/Sidebar.php';
require_once __DIR__ . '/Meta.php';
require_once __DIR__ . '/../../../views/other/includes/auth.php';

// Check if user is logged in and get user info
$isLoggedIn = isLoggedIn();
$user = $isLoggedIn ? getCurrentUser() : null;
requireRole("admin", $basePath);
$user = getCurrentUser();

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en" >

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?= $content ?? '' ?>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="<?= $basePath ?>/public/js/common/common.js"></script>
</body>

</html>