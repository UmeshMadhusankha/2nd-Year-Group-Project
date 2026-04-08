<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';

requireRole('admin', '/2nd-Year-Group-Project/FixLanka');

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/admin-audit-logs';

$pageTitle = 'Audit Logs - FixLanka';
$pageDescription = 'Read-only system audit logs (append-only)';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/audit-logs.css?v=<?php echo time(); ?>">
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>

        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Audit Logs', 'Read-only system activity log'); ?>

            <main class="audit-logs-main">
                <div class="audit-logs-card">
                    <div class="audit-logs-card-header">
                        <div>
                            <h2 class="audit-logs-title">System Audit Logs</h2>
                            <p class="audit-logs-subtitle">Entries are append-only and cannot be edited or deleted.</p>
                        </div>
                        <div class="audit-logs-controls">
                            <label class="audit-logs-control">
                                <span class="audit-logs-control-label">Page size</span>
                                <select id="auditLimit" class="audit-logs-select">
                                    <option value="25">25</option>
                                    <option value="50" selected>50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                </select>
                            </label>
                            <button id="auditRefresh" class="audit-logs-button" type="button">Refresh</button>
                        </div>
                    </div>

                    <div id="auditMessage" class="audit-logs-message" style="display:none"></div>

                    <div class="audit-logs-table-wrap">
                        <table class="audit-logs-table" aria-label="Audit logs">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Actor</th>
                                    <th>Action</th>
                                    <th>Entity</th>
                                    <th>Endpoint</th>
                                    <th>IP</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody id="auditTableBody">
                                <tr>
                                    <td colspan="8" class="audit-logs-loading">Loading logs...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="audit-logs-pagination">
                        <button id="auditPrev" class="audit-logs-button" type="button">Prev</button>
                        <div class="audit-logs-pageinfo">
                            <span id="auditPageText">Page 1</span>
                            <span class="audit-logs-page-divider">•</span>
                            <span id="auditTotalText">0 total</span>
                        </div>
                        <button id="auditNext" class="audit-logs-button" type="button">Next</button>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        window.FIXLANKA_AUDIT_LOGS = {
            apiUrl: '/2nd-Year-Group-Project/FixLanka/api/audit-logs.php'
        };
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/audit-logs.js?v=<?php echo time(); ?>"></script>
</body>

</html>
