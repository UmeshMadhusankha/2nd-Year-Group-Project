<?php
// pages/moderator/notifications/all/index.php

require_once __DIR__ . '/../common/NotificationCard.php';
require_once __DIR__ . '/../common/NotificationFilters.php';
require_once __DIR__ . '/../common/NotificationForm.php';
require_once __DIR__ . '/../common/NotificationStats.php';
require_once __DIR__ . '/../common/Header.php';
require_once __DIR__ . '/../common/mock-data.php';
require_once __DIR__ . '/../common/Modals.php';
require_once __DIR__ . '/../common/Common.php';


$notifications = [];
$stats = [];
$templates = getNotificationTemplates() ?? [];
$isLoading = true;
?>

<?php renderPageHeader($basePath,'All Notifications', 'Complete notification history with advanced filtering and analytics'); ?>
<link rel="stylesheet" href="<?= $basePath ?>/assets/css/admin & moderator/notifications.css">
<link rel="stylesheet" href="<?= $basePath ?>/assets/css/admin & moderator/modals.css">

<div id="loadingOverlay" class="fixed inset-0 bg-background/80 backdrop-blur-sm z-50 flex items-center justify-center" style="display: none;">
    <div class="flex flex-col items-center space-y-4">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-fixlanka-primary"></div>
        <p class="text-foreground font-medium">Loading...</p>
    </div>
</div>

<div id="errorAlert" class="hidden fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-red-500/10 border border-red-500 text-red-500 px-4 py-3 rounded-lg shadow-lg">
        <div class="flex items-start">
            <i data-lucide="alert-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-medium">Error</p>
                <p id="errorMessage" class="text-sm mt-1"></p>
            </div>
            <button onclick="closeError()" class="ml-4 text-red-500 hover:text-red-600">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
</div>

<div id="successAlert" class="hidden fixed top-4 right-4 z-50 max-w-md">
    <div class="bg-green-500/10 border border-green-500 text-green-500 px-4 py-3 rounded-lg shadow-lg">
        <div class="flex items-start">
            <i data-lucide="check-circle" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="font-medium">Success</p>
                <p id="successMessage" class="text-sm mt-1"></p>
            </div>
            <button onclick="closeSuccess()" class="ml-4 text-green-500 hover:text-green-600">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>
</div>

<main style="margin-top: 5rem;" class="notifications-content">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-foreground">All Notifications</h2>
                <p class="text-muted-foreground">Complete notification history with advanced filtering, sorting, and analytics</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?= $basePath ?>/moderator/notifications" class="btn btn-outline">
                    <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i>
                    Back to Dashboard
                </a>
                <button onclick="openVideoModal()" class="btn btn-primary">
                    <i data-lucide="video" class="mr-2 h-4 w-4"></i>
                    Video Notification
                </button>
                <button onclick="exportNotifications()" class="btn btn-secondary">
                    <i data-lucide="download" class="mr-2 h-4 w-4"></i>
                    Export
                </button>
                <button onclick="refreshNotifications()" class="btn btn-secondary" id="refreshBtn">
                    <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                    Refresh
                </button>
            </div>
        </div>


        <div id="statsContainer">
            <div class="grid gap-4 grid-cols-4">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="bg-card rounded-lg border p-6 animate-pulse">
                        <div class="h-4 bg-muted rounded w-1/2 mb-3"></div>
                        <div class="h-8 bg-muted rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-muted rounded w-1/3"></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <?php renderNotificationFilters(true); ?>

        <div class="bg-card rounded-lg border">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" id="selectAll" class="h-4 w-4 text-fixlanka-primary focus:ring-fixlanka-primary border-border rounded" onchange="toggleSelectAll()">
                        <label for="selectAll" class="text-sm font-medium text-foreground">Select All</label>
                        <span id="selectedCount" class="text-sm text-muted-foreground">0 selected</span>
                    </div>
                    <div class="flex items-center space-x-2" id="bulkActions" style="display: none;">
                        <button onclick="bulkDelete()" class="btn btn-sm btn-destructive">
                            <i data-lucide="trash-2" class="mr-1 h-3 w-3"></i>
                            Delete Selected
                        </button>
                        <button onclick="bulkResend()" class="btn btn-sm btn-secondary">
                            <i data-lucide="send" class="mr-1 h-3 w-3"></i>
                            Resend Selected
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-card rounded-lg border">
            <div class="p-6">
                <div class="flex items-center justify-between w-full mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-foreground flex items-center gap-2">
                            <i data-lucide="list" class="h-5 w-5"></i>
                            All Notifications
                            <span class="text-sm text-muted-foreground ml-2" id="notificationCount">(Loading...)</span>
                        </h3>
                        <p class="text-sm text-muted-foreground">Complete notification history with detailed metrics</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="toggleNotificationView('list')" id="listViewBtn" class="btn btn-sm btn-primary">
                            <i data-lucide="list" class="h-4 w-4"></i>
                        </button>
                        <button onclick="toggleNotificationView('grid')" id="gridViewBtn" class="btn btn-sm btn-secondary">
                            <i data-lucide="grid-3x3" class="h-4 w-4"></i>
                        </button>
                        <button onclick="toggleNotificationView('table')" id="tableViewBtn" class="btn btn-sm btn-secondary">
                            <i data-lucide="table" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>

                <div id="notificationsLoader" class="space-y-4">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <div class="animate-pulse">
                            <div class="bg-muted/30 rounded-lg border p-4">
                                <div class="h-4 bg-muted rounded w-3/4 mb-3"></div>
                                <div class="h-3 bg-muted rounded w-1/2 mb-2"></div>
                                <div class="h-3 bg-muted rounded w-1/4"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

                <div id="notificationsList" class="space-y-4" style="display: none;">
                    Will be populated by JavaScript
                </div>

                <div id="notificationsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" style="display: none;">
                    Will be populated by JavaScript
                </div>

                <div id="notificationsTable" class="overflow-x-auto" style="display: none;">
                    Will be populated by JavaScript
                </div>

                <div class="flex items-center justify-between mt-6 pt-4 border-t border-border">
                    <div class="text-sm text-muted-foreground" id="paginationInfo">
                        Loading...
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="btn btn-sm btn-outline" id="prevBtn" onclick="changePage(-1)">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                            Previous
                        </button>
                        <span class="px-3 py-1 text-sm bg-fixlanka-primary text-white rounded" id="currentPage">1</span>
                        <button class="btn btn-sm btn-outline" id="nextBtn" onclick="changePage(1)">
                            Next
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
renderNotificationDetailsModal();
renderEditNotificationModal();
renderVideoNotificationModal();
renderDeleteConfirmModal();
?>
<script>
    window.basePath = "<?php echo $basePath; ?>";
    window.allNotifications = []
</script>
<script src="<?= $basePath ?>/assets/javascript/admin & moderator/modalFunctions.js"></script>
<script src="<?= $basePath ?>/assets/javascript/admin & moderator/notifications.js"></script>