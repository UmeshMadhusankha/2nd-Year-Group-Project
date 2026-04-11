<?php
$currentPage = 'notifications';
$pageTitle = 'Notifications';
$pageSubtitle = 'Review all updates before taking action';
$searchPlaceholder = 'Search notifications...';
$repairerId = (int)($_SESSION['user_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/notifications.css?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../../assets/css/repairer/notifications.css')); ?>">
</head>
<body>
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

        <main class="main-content-wrapper">
            <div class="main-content repairer-notifications-main">
                <div class="content-wrapper">
                    <section class="page-header">
                        <div class="page-header-content">
                            <div class="page-header-text">
                                <h2 class="page-title"><i class="fas fa-bell"></i> Notifications</h2>
                                <p class="page-description">Read full notification details before reporting an issue.</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn btn-secondary" id="markAllRepairerRead" type="button">
                                    <i class="fas fa-check"></i>
                                    Mark all as read
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="repairer-notifications-section">
                        <div class="repairer-notifications-list" id="repairerNotificationsList">
                            <div class="repairer-notifications-empty">
                                <i class="fas fa-spinner fa-spin"></i>
                                <p>Loading notifications...</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <div class="notification-details-overlay" id="repairerNotificationDetailsOverlay">
        <div class="notification-details-modal">
            <div class="notification-details-header">
                <h3 class="notification-details-title" id="repairerNotificationTitle">Notification</h3>
                <button type="button" class="notification-details-close" id="closeRepairerNotificationDetails" aria-label="Close">&times;</button>
            </div>
            <div class="notification-details-body">
                <div class="notification-details-meta" id="repairerNotificationMeta"></div>
                <div class="notification-details-message" id="repairerNotificationMessage"></div>
            </div>
            <div class="notification-details-footer">
                <button type="button" class="btn btn-secondary" id="closeRepairerNotificationBtn">Close</button>
                <button type="button" class="btn btn-primary" id="reportIssueFromNotificationBtn">
                    <i class="fas fa-life-ring"></i>
                    Report Issue
                </button>
            </div>
        </div>
    </div>

    <script>
        window.NOTIFICATIONS_PAGE_USER_ID = <?php echo $repairerId; ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/notifications.js?v=<?php echo urlencode((string) @filemtime(__DIR__ . '/../../../assets/javascript/repairer/notifications.js')); ?>"></script>
</body>
</html>
