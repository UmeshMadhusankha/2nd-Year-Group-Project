<?php
/**
 * Advertisement Reports - Moderator
 * ✅ FIXED: Proper statistics display
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AdReportController.php';

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-ad-reports';

// Initialize PDO Connection ✅ FIXED
try {
    // $pdo is provided by config/database.php
    $controller = new AdReportController($pdo);

    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handlePostRequest();
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Get data from database
    $viewData = $controller->getViewData();
    $messages = $controller->getMessages();
    $reportsData = $viewData['reports'];
    $statistics = $viewData['statistics'];
    $message = $messages['message'] ?? '';
    $messageType = $messages['type'] ?? '';
} catch (Exception $e) {
    die("⛔ Database Error: " . htmlspecialchars($e->getMessage()));
}

// Get page title and description
$pageTitle = 'Advertisement Reports - FixLanka';
$pageDescription = 'Review and moderate advertisement-related reports';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/reports.css?v=<?php echo time(); ?>">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Reports', 'Review and moderate advertisement-related reports'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <!-- Success/Error Messages -->
                <?php if ($message): ?>
                    <div class="alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h4 class="stat-card-title">Total Reports</h4>
                                <p class="stat-card-value"><?php echo $statistics['total_reports'] ?? 0; ?></p>
                            </div>
                            <i class="fa-solid fa-flag"></i>
                        </div>
                        <p class="stat-card-description">All advertisement reports</p>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h4 class="stat-card-title">Pending Review</h4>
                                <p class="stat-card-value"><?php echo $statistics['pending'] ?? 0; ?></p>
                            </div>
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <p class="stat-card-description">Awaiting moderation</p>
                    </div>

                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div>
                                <h4 class="stat-card-title">Resolved</h4>
                                <p class="stat-card-value"><?php echo $statistics['resolved'] ?? 0; ?></p>
                            </div>
                            <i class="fa-solid fa-check-circle"></i>
                        </div>
                        <p class="stat-card-description">Successfully handled</p>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="filters-section">
                    <div class="filters-header">
                        <h3><i class="fa-solid fa-filter"></i> Filter Reports</h3>
                        <p>Search and filter reports by status, type, or priority</p>
                    </div>
                    <div class="filters-controls">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search by advertisement title or description...">
                            <i class="fa-solid fa-search search-icon"></i>
                        </div>
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="dismissed">Dismissed</option>
                            <option value="escalated">Escalated</option>
                        </select>
                        <select id="typeFilter" class="filter-select">
                            <option value="">All Types</option>
                            <option value="inappropriate_content">Inappropriate Content</option>
                            <option value="misleading_information">Misleading Information</option>
                            <option value="spam">Spam</option>
                            <option value="privacy_violation">Privacy Violation</option>
                            <option value="copyright_infringement">Copyright</option>
                            <option value="fraud">Fraud</option>
                            <option value="other">Other</option>
                        </select>
                        <select id="priorityFilter" class="filter-select">
                            <option value="">All Priorities</option>
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <!-- Reports Table -->
                <div class="reports-table-container">
                    <!-- Loading State -->
                    <div id="loadingState" class="loading-container hidden">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Loading reports...</p>
                    </div>

                    <!-- Error State -->
                    <div id="errorState" class="error-container hidden">
                        <i class="fa-solid fa-exclamation-triangle error-icon"></i>
                        <h3 class="error-title">Failed to Load Reports</h3>
                        <p class="error-description">Please try again later</p>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="empty-container hidden">
                        <i class="fa-solid fa-inbox empty-icon"></i>
                        <h3 class="empty-title">No Reports Found</h3>
                        <p class="empty-description">Try adjusting your filters</p>
                    </div>

                    <!-- Table -->
                    <table id="reportsTable" class="reports-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Advertisement</th>
                                <th>Reporter</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Ad Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody">
                            <!-- Populated by JavaScript -->
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div id="pagination" class="pagination-container"></div>
                </div>
            </main>

            <!-- Manage Report Modal -->
            <div id="manageReportModal" class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title"><i class="fa-solid fa-shield-halved"></i> Manage Report</h3>
                        <button class="modal-close" onclick="closeManageModal()">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="report-details" id="reportDetails">
                            <!-- Populated by JavaScript -->
                        </div>

                        <form class="modal-form" method="POST">
                            <input type="hidden" name="report_id" id="modalReportId">
                            <input type="hidden" name="ad_id" id="modalAdId">

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Report Status</label>
                                    <select name="status" id="modalStatus" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="investigating">Investigating</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="dismissed">Dismissed</option>
                                        <option value="escalated">Escalated</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" id="modalPriority" class="form-select" required>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Moderator Notes</label>
                                <textarea name="moderator_notes" id="modalNotes" class="form-textarea" rows="4" placeholder="Add your notes about this report..."></textarea>
                            </div>

                            <div class="modal-footer">
                                <div class="form-buttons">
                                    <button type="submit" name="action" value="update_report" class="btn-submit">
                                        <i class="fa-solid fa-save"></i> Update Report
                                    </button>
                                    <button type="button" class="btn-cancel" onclick="closeManageModal()">
                                        Cancel
                                    </button>
                                </div>

                                <div class="form-buttons" id="actionButtons">
                                    <button type="submit" name="action" value="reject_report" class="btn-submit btn-reject" onclick="return confirm('Dismiss this report as invalid?')">
                                        <i class="fa-solid fa-ban"></i> Dismiss Report
                                    </button>
                                    <button type="submit" name="action" value="suspend_ad" class="btn-submit btn-suspend" onclick="return confirm('Suspend this advertisement?')">
                                        <i class="fa-solid fa-pause"></i> Suspend Ad
                                    </button>
                                    <button type="submit" name="action" value="delete_ad" class="btn-submit btn-delete" onclick="return confirm('⚠️ PERMANENTLY DELETE this advertisement? This cannot be undone!')">
                                        <i class="fa-solid fa-trash"></i> Delete Ad
                                    </button>
                                    <button type="submit" name="action" value="escalate" class="btn-submit btn-escalate" onclick="return confirm('Escalate this report to admin?')">
                                        <i class="fa-solid fa-arrow-up"></i> Escalate to Admin
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const mockReportsData = <?= (json_encode($reportsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]') ?>;
        let currentPage = 1;
        let allReports = [];
        let currentReport = null;

        function initializeReports() {
            allReports = mockReportsData.map(report => ({
                id: report.id,
                ad_id: report.ad_id,
                ad_title: report.ad_title,
                reporter_id: report.reporter_id,
                reporter_type: report.reporter_type,
                report_type: report.issue_type,
                description: report.description,
                priority: report.priority,
                status: report.status,
                ad_status: report.ad_status,
                submitted_date: report.created_date,
                company_name: report.company_name || 'Unknown',
                evidence: report.evidence || ''
            }));
        }

        function loadReports(page = 1) {
            currentPage = page;
            const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
            const status = document.getElementById('statusFilter')?.value || '';
            const type = document.getElementById('typeFilter')?.value || '';
            const priority = document.getElementById('priorityFilter')?.value || '';

            showLoading();

            let filtered = allReports.filter(report => {
                const matchesSearch = !search || report.ad_title.toLowerCase().includes(search) || report.description.toLowerCase().includes(search);
                const matchesStatus = !status || report.status === status;
                const matchesType = !type || report.report_type === type;
                const matchesPriority = !priority || report.priority === priority;
                return matchesSearch && matchesStatus && matchesType && matchesPriority;
            });

            const limit = 20;
            const total = filtered.length;
            const pages = Math.ceil(total / limit) || 1;
            const offset = (page - 1) * limit;
            const data = filtered.slice(offset, offset + limit);

            displayReports(data, {page, pages, total});
        }

        function displayReports(reports, pagination) {
            const tableBody = document.getElementById('reportsTableBody');
            const loadingState = document.getElementById('loadingState');
            const errorState = document.getElementById('errorState');
            const emptyState = document.getElementById('emptyState');
            const reportsTable = document.getElementById('reportsTable');

            loadingState.classList.add('hidden');
            errorState.classList.add('hidden');
            emptyState.classList.add('hidden');

            if (reports.length === 0) {
                reportsTable.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            reportsTable.classList.remove('hidden');

            const statusClasses = {
                'pending': 'badge-pending',
                'investigating': 'badge-investigating',
                'resolved': 'badge-resolved',
                'dismissed': 'badge-rejected',
                'escalated': 'badge-escalated'
            };

            const adStatusClasses = {
                'approved': 'badge-approved',
                'active': 'badge-active',
                'inactive': 'badge-inactive',
                'suspended': 'badge-suspended',
                'deleted': 'badge-deleted',
                'pending': 'badge-pending',
                'rejected': 'badge-rejected'
            };

            const priorityClasses = {
                'critical': 'badge-high',
                'high': 'badge-high',
                'medium': 'badge-medium',
                'low': 'badge-low'
            };

            const typeLabels = {
                'inappropriate_content': 'Inappropriate',
                'misleading_information': 'Misleading',
                'spam': 'Spam',
                'privacy_violation': 'Privacy',
                'copyright_infringement': 'Copyright',
                'fraud': 'Fraud',
                'other': 'Other'
            };

            tableBody.innerHTML = reports.map(report => `
                <tr>
                    <td><span class="report-id">#${report.id}</span></td>
                    <td>
                        <div class="reporter-info">
                            <strong class="reporter-name">${escapeHtml(report.ad_title)}</strong>
                            <span class="reporter-type">by ${escapeHtml(report.company_name)}</span>
                        </div>
                    </td>
                    <td>
                        <div class="reporter-info">
                            <span class="reporter-type">${escapeHtml(report.reporter_type)}</span>
                        </div>
                    </td>
                    <td><span class="badge type-badge">${typeLabels[report.report_type] || report.report_type}</span></td>
                    <td><span class="report-description">${escapeHtml(report.description.substring(0, 50))}${report.description.length > 50 ? '...' : ''}</span></td>
                    <td><span class="badge ${priorityClasses[report.priority]}">${report.priority}</span></td>
                    <td><span class="badge ${statusClasses[report.status]}">${report.status}</span></td>
                    <td><span class="badge ${adStatusClasses[report.ad_status]}">${report.ad_status}</span></td>
                    <td>${new Date(report.submitted_date).toLocaleDateString()}</td>
                    <td>
                        <button onclick="manageReport(${report.id})" class="btn-manage">
                            <i class="fa-solid fa-shield-halved"></i> Manage
                        </button>
                    </td>
                </tr>
            `).join('');

            displayPagination(pagination);
        }

        function displayPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            if (pagination.pages <= 1) {
                paginationDiv.style.display = 'none';
                return;
            }
            paginationDiv.style.display = 'flex';
            let html = `<div class="pagination-info">Page ${pagination.page} of ${pagination.pages} (${pagination.total} total)</div><div class="pagination-buttons">`;
            if (pagination.page > 1) html += `<button onclick="loadReports(${pagination.page - 1})" class="btn-pagination">Previous</button>`;
            if (pagination.page < pagination.pages) html += `<button onclick="loadReports(${pagination.page + 1})" class="btn-pagination">Next</button>`;
            html += '</div>';
            paginationDiv.innerHTML = html;
        }

        function manageReport(id) {
            const report = allReports.find(r => r.id === id);
            if (!report) {
                alert('Report not found');
                return;
            }

            currentReport = report;

            const typeLabels = {
                'inappropriate_content': 'Inappropriate Content',
                'misleading_information': 'Misleading Information',
                'spam': 'Spam',
                'privacy_violation': 'Privacy Violation',
                'copyright_infringement': 'Copyright Infringement',
                'fraud': 'Fraud',
                'other': 'Other'
            };

            document.getElementById('reportDetails').innerHTML = `
                <div class="detail-row">
                    <div class="detail-field">
                        <span class="detail-label">Report ID:</span>
                        <span class="detail-value">#${report.id}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Advertisement ID:</span>
                        <span class="detail-value">#${report.ad_id}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <span class="detail-label">Advertisement Title:</span>
                        <span class="detail-value">${escapeHtml(report.ad_title)}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <span class="detail-label">Company:</span>
                        <span class="detail-value">${escapeHtml(report.company_name)}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Reporter Type:</span>
                        <span class="detail-value">${escapeHtml(report.reporter_type)}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field">
                        <span class="detail-label">Report Type:</span>
                        <span class="detail-value">${typeLabels[report.report_type] || report.report_type}</span>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Submitted:</span>
                        <span class="detail-value">${new Date(report.submitted_date).toLocaleString()}</span>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-field" style="flex: 1;">
                        <span class="detail-label">Description:</span>
                        <div class="description-box">${escapeHtml(report.description)}</div>
                    </div>
                </div>
            `;

            document.getElementById('modalReportId').value = report.id;
            document.getElementById('modalAdId').value = report.ad_id;
            document.getElementById('modalStatus').value = report.status;
            document.getElementById('modalPriority').value = report.priority;
            document.getElementById('modalNotes').value = '';

            updateActionButtons(report);

            document.getElementById('manageReportModal').classList.add('show');
        }

        function updateActionButtons(report) {
            const actionButtons = document.getElementById('actionButtons');
            const buttons = actionButtons.querySelectorAll('button');
            
            // Disable all action buttons if report is resolved or dismissed
            if (report.status === 'resolved' || report.status === 'dismissed') {
                buttons.forEach(btn => btn.disabled = true);
                return;
            }
            
            // Enable all buttons for active reports
            buttons.forEach(btn => btn.disabled = false);
            
            // Disable suspend/delete if ad is already suspended or deleted
            if (report.ad_status === 'suspended') {
                actionButtons.querySelector('[value="suspend_ad"]').disabled = true;
            }
            
            if (report.ad_status === 'deleted') {
                actionButtons.querySelector('[value="suspend_ad"]').disabled = true;
                actionButtons.querySelector('[value="delete_ad"]').disabled = true;
            }
        }

        function closeManageModal() {
            document.getElementById('manageReportModal').classList.remove('show');
        }

        document.getElementById('manageReportModal').addEventListener('click', (e) => {
            if (e.target.id === 'manageReportModal') closeManageModal();
        });

        function showLoading() {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('reportsTable').classList.add('hidden');
            document.getElementById('emptyState').classList.add('hidden');
        }

        function escapeHtml(text) {
            const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        document.getElementById('searchInput')?.addEventListener('input', debounce(() => loadReports(1), 500));
        document.getElementById('statusFilter')?.addEventListener('change', () => loadReports(1));
        document.getElementById('typeFilter')?.addEventListener('change', () => loadReports(1));
        document.getElementById('priorityFilter')?.addEventListener('change', () => loadReports(1));

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        initializeReports();
        loadReports();
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>