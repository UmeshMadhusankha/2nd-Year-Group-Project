<?php
// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');
$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - FixLanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/notifications-page.css?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../assets/css/company/notifications-page.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <main class="main-content notifications-main">
            <?php include 'topbar.php'; ?>

            <div class="notifications-container">
                <header class="page-header">
                    <div class="header-content">
                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Notifications</span>
                        </div>

                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-bell"></i> Notifications</h1>
                                <p class="subtitle">All your company notifications in one place</p>
                            </div>
                            <div class="header-actions">
                                <button class="action-btn secondary" id="markAllReadBtn" type="button">
                                    <i class="fas fa-check"></i>
                                    Mark all as read
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <section class="notifications-panel">
                    <div class="notification-list" id="notificationsList">
                        <div class="loading-state">
                            <i class="fas fa-circle-notch fa-spin"></i> Loading notifications...
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        window.NOTIFICATIONS_PAGE_USER_ID = <?php echo json_encode($userData['id'] ?? 0); ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/notifications-page.js?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../assets/javascript/company/notifications-page.js')); ?>"></script>
</body>

</html>
