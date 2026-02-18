<?php
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

// Initialize controller
global $pdo;
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

// Get page title and description
$pageTitle = 'Advertisement Reports - FixLanka';
$pageDescription = 'Review and moderate advertisement-related reports';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/reports.css">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Reports', 'Review and moderate advertisement-related reports'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div class="page-header">
                        <h2 class="page-title">Advertisement Reports</h2>
                        <p class="page-description">Review, investigate, and resolve advertisement-related reports with strict moderation rules</p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div style="
                            padding: 1rem 1.5rem;
                            border-radius: 8px;
                            margin-bottom: 1.5rem;
                            background: <?php echo $messageType === 'success' ? '#d1fae5' : '#fee2e2'; ?>;
                            color: <?php echo $messageType === 'success' ? '#059669' : '#dc2626'; ?>;
                            border: 1px solid <?php echo $messageType === 'success' ? '#10b981' : '#ef4444'; ?>;
                            font-weight: 500;
                        ">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Boxes -->
                    <div id="statsContainer" class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Pending Reports</h4>
                                    <div class="stat-value"><?php echo $statistics['pending'] ?? 0; ?></div>
                                    <div class="stat-change">Awaiting review</div>
                                </div>
                                <div class="stat-icon" style="background: #fef3c7;">
                                    <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Under Investigation</h4>
                                    <div class="stat-value"><?php echo $statistics['investigating'] ?? 0; ?></div>
                                    <div class="stat-change">Being reviewed</div>
                                </div>
                                <div class="stat-icon" style="background: #dbeafe;">
                                    <i class="fa-solid fa-magnifying-glass" style="color: #3b82f6;"></i>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>Resolved</h4>
                                    <div class="stat-value"><?php echo $statistics['resolved'] ?? 0; ?></div>
                                    <div class="stat-change">Successfully handled</div>
                                </div>
                                <div class="stat-icon" style="background: #d1fae5;">
                                    <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filters-section">
                        <div class="filters-header">
                            <i class="fa-solid fa-filter"></i>
                            <span>Filter Reports</span>
                        </div>

                        <div class="filters-controls">
                            <input 
                                type="text" 
                                id="searchInput" 
                                placeholder="Search by ad title, reporter, or description..." 
                                class="filter-input"
                            >

                            <select id="statusFilter" class="filter-select">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                                <option value="rejected">Rejected</option>
                                <option value="escalated">Escalated</option>
                            </select>

                            <select id="typeFilter" class="filter-select">
                                <option value="">All Types</option>
                                <option value="inappropriate_content">Inappropriate Content</option>
                                <option value="misleading_information">Misleading Information</option>
                                <option value="spam">Spam</option>
                                <option value="copyright_violation">Copyright Violation</option>
                                <option value="offensive_material">Offensive Material</option>
                                <option value="false_advertising">False Advertising</option>
                                <option value="broken_link">Broken Link</option>
                                <option value="poor_quality">Poor Quality</option>
                                <option value="other">Other</option>
                            </select>

                            <select id="priorityFilter" class="filter-select">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="loadingState" class="loading-container">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Loading reports...</p>
                    </div>

                    <!-- Error State -->
                    <div id="errorState" class="error-container hidden">
                        <svg class="error-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p class="error-title">Failed to load reports</p>
                        <p id="errorMessage" class="error-description"></p>
                        <button onclick="loadReports()" class="btn-manage">Try Again</button>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="empty-container hidden">
                        <svg class="empty-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                        </svg>
                        <p class="empty-title">No reports found</p>
                        <p class="empty-description">No advertisement reports match your current filters</p>
                    </div>

                    <!-- Reports Table -->
                    <div id="reportsTable" class="reports-table-container hidden">
                        <table class="reports-table">
                            <thead>
                                <tr>
                                    <th>Report ID</th>
                                    <th>Ad ID</th>
                                    <th>Advertisement</th>
                                    <th>Company</th>
                                    <th>Issue Type</th>
                                    <th>Reported By</th>
                                    <th>Priority</th>
                                    <th>Report Status</th>
                                    <th>Ad Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportsTableBody"></tbody>
                        </table>
                        <div id="pagination" class="pagination-container"></div>
                    </div>
                </div>
            </main>

            <!-- Manage Report Modal -->
            <div id="manageReportModal" class="modal-overlay">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa-solid fa-clipboard-list"></i>
                            Manage Report
                        </h3>
                        <button onclick="closeManageModal()" class="modal-close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div id="reportDetailsContent" class="modal-body"></div>

                    <div class="modal-footer">
                        <form id="updateReportForm" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="modal-form">
                            <input type="hidden" name="report_id" id="currentReportId">
                            <input type="hidden" name="ad_id" id="currentAdId">
                            <input type="hidden" name="action" id="currentAction" value="update_report">

                            <div class="form-group">
                                <label for="updateStatus">Report Status</label>
                                <select name="status" id="updateStatus" class="form-input" required>
                                    <option value="pending">Pending</option>
                                    <option value="investigating">Investigating</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="dismissed">Dismissed</option>
                                    <option value="escalated">Escalated</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="updatePriority">Priority</label>
                                <select name="priority" id="updatePriority" class="form-input" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="moderatorNotes">Moderator Notes</label>
                                <textarea 
                                    name="moderator_notes" 
                                    id="moderatorNotes" 
                                    rows="4" 
                                    class="form-input"
                                    placeholder="Add your notes about this report..."
                                ></textarea>
                            </div>

                            <div class="modal-actions">
                                <button type="submit" class="btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Update Report
                                </button>

                                <button type="button" onclick="rejectReport()" class="btn-warning" id="btnRejectReport">
                                    <i class="fa-solid fa-circle-xmark"></i>
                                    Dismiss Report
                                </button>

                                <button type="button" onclick="suspendAd()" class="btn-danger" id="btnSuspendAd">
                                    <i class="fa-solid fa-circle-pause"></i>
                                    Suspend Ad
                                </button>

                                <button type="button" onclick="deleteAd()" class="btn-danger" id="btnDeleteAd">
                                    <i class="fa-solid fa-trash"></i>
                                    Delete Ad
                                </button>

                                <button type="button" onclick="escalateReport()" class="btn-secondary" id="btnEscalate">
                                    <i class="fa-solid fa-circle-arrow-up"></i>
                                    Escalate to Admin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const mockReportsData = <?= json_encode($reportsData) ?>;
        let currentPage = 1;
        let allReports = [];
        let currentReport = null;

        function initializeReports() {
            allReports = mockReportsData.map(report => ({
                id: report.id,
                ad_id: report.ad_id,
                ad_title: report.ad_title || 'Unknown Advertisement',
                ad_status: report.ad_status || 'unknown',
                company_name: report.company_name || 'Unknown Company',
                reporter_name: report.user_name || 'Unknown',
                reporter_email: report.user_email || '',
                reporter_type: report.reporter_type || 'user',
                type: report.issue_type,
                description: report.description || '',
                priority: report.priority || 'medium',
                status: report.status || 'pending',
                created_at: report.created_date || new Date().toISOString(),
                moderator_notes: report.moderator_notes || '',
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
                if (search &&
                    !report.ad_title.toLowerCase().includes(search) &&
                    !report.reporter_name.toLowerCase().includes(search) &&
                    !report.description.toLowerCase().includes(search)) return false;
                if (status && report.status !== status) return false;
                if (type && report.type !== type) return false;
                if (priority && report.priority !== priority) return false;
                return true;
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
                emptyState.classList.remove('hidden');
                reportsTable.classList.add('hidden');
                return;
            }

            reportsTable.classList.remove('hidden');

            const statusClasses = {
                pending: 'badge-pending',
                investigating: 'badge-investigating',
                resolved: 'badge-resolved',
                escalated: 'badge-escalated',
                dismissed: 'badge-rejected'
            };

            const adStatusClasses = {
                pending: 'badge-pending',
                approved: 'badge-approved',
                active: 'badge-active',
                inactive: 'badge-inactive',
                suspended: 'badge-suspended',
                rejected: 'badge-rejected',
                deleted: 'badge-deleted'
            };

            const priorityClasses = {
                low: 'badge-low',
                medium: 'badge-medium',
                high: 'badge-high',
                critical: 'badge-high'
            };

            const typeLabels = {
                inappropriate_content: 'Inappropriate Content',
                misleading_information: 'Misleading Information',
                spam: 'Spam',
                copyright_violation: 'Copyright Violation',
                offensive_material: 'Offensive Material',
                false_advertising: 'False Advertising',
                broken_link: 'Broken Link',
                poor_quality: 'Poor Quality',
                other: 'Other'
            };

            tableBody.innerHTML = reports.map(report => `
                <tr>
                    <td class="font-medium">#${report.id}</td>
                    <td class="font-medium">#${report.ad_id}</td>
                    <td>${escapeHtml(report.ad_title)}</td>
                    <td>${escapeHtml(report.company_name)}</td>
                    <td><span class="badge badge-info">${typeLabels[report.type] || report.type}</span></td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <span>${escapeHtml(report.reporter_name)}</span>
                            <span style="font-size: 0.75rem; color: #64748b;">${report.reporter_type}</span>
                        </div>
                    </td>
                    <td><span class="badge ${priorityClasses[report.priority]}">${report.priority.toUpperCase()}</span></td>
                    <td><span class="badge ${statusClasses[report.status]}">${report.status.toUpperCase()}</span></td>
                    <td><span class="badge ${adStatusClasses[report.ad_status]}">${report.ad_status.toUpperCase()}</span></td>
                    <td>${new Date(report.created_at).toLocaleDateString()}</td>
                    <td>
                        <button onclick="manageReport(${report.id})" class="btn-manage">
                            <i class="fa-solid fa-gear"></i>
                            Manage
                        </button>
                    </td>
                </tr>
            `).join('');

            displayPagination(pagination);
        }

        function displayPagination(pagination) {
            const paginationDiv = document.getElementById('pagination');
            if (pagination.pages <= 1) {
                paginationDiv.innerHTML = '';
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
                inappropriate_content: 'Inappropriate Content',
                misleading_information: 'Misleading Information',
                spam: 'Spam',
                copyright_violation: 'Copyright Violation',
                offensive_material: 'Offensive Material',
                false_advertising: 'False Advertising',
                broken_link: 'Broken Link',
                poor_quality: 'Poor Quality',
                other: 'Other'
            };

            document.getElementById('reportDetailsContent').innerHTML = `
                <div class="detail-section">
                    <h4 class="detail-title">Report Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Report ID:</span>
                            <span class="detail-value">#${report.id}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Advertisement ID:</span>
                            <span class="detail-value">#${report.ad_id}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Ad Title:</span>
                            <span class="detail-value">${escapeHtml(report.ad_title)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Company:</span>
                            <span class="detail-value">${escapeHtml(report.company_name)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Issue Type:</span>
                            <span class="detail-value">${typeLabels[report.type] || report.type}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Reporter:</span>
                            <span class="detail-value">${escapeHtml(report.reporter_name)} (${report.reporter_type})</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Current Report Status:</span>
                            <span class="detail-value">${report.status.toUpperCase()}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Current Ad Status:</span>
                            <span class="detail-value">${report.ad_status.toUpperCase()}</span>
                        </div>
                    </div>
                    <div class="detail-item" style="margin-top: 1rem;">
                        <span class="detail-label">Description:</span>
                        <p class="detail-description">${escapeHtml(report.description)}</p>
                    </div>
                    ${report.evidence ? `
                    <div class="detail-item" style="margin-top: 1rem;">
                        <span class="detail-label">Evidence:</span>
                        <p class="detail-description">${escapeHtml(report.evidence)}</p>
                    </div>
                    ` : ''}
                    ${report.moderator_notes ? `
                    <div class="detail-item" style="margin-top: 1rem;">
                        <span class="detail-label">Previous Notes:</span>
                        <p class="detail-description">${escapeHtml(report.moderator_notes)}</p>
                    </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('currentReportId').value = report.id;
            document.getElementById('currentAdId').value = report.ad_id;
            document.getElementById('updateStatus').value = report.status;
            document.getElementById('updatePriority').value = report.priority;
            document.getElementById('moderatorNotes').value = report.moderator_notes || '';

            // Show/hide action buttons based on status
            updateActionButtons(report);

            document.getElementById('manageReportModal').classList.add('show');
        }

        function updateActionButtons(report) {
            const btnRejectReport = document.getElementById('btnRejectReport');
            const btnSuspendAd = document.getElementById('btnSuspendAd');
            const btnDeleteAd = document.getElementById('btnDeleteAd');
            const btnEscalate = document.getElementById('btnEscalate');

            // Hide all buttons initially
            btnRejectReport.style.display = 'none';
            btnSuspendAd.style.display = 'none';
            btnDeleteAd.style.display = 'none';
            btnEscalate.style.display = 'none';

            // Show buttons based on report and ad status
            if (report.status === 'pending' || report.status === 'investigating') {
                btnRejectReport.style.display = 'inline-flex';
                btnEscalate.style.display = 'inline-flex';

                // Can only suspend active/inactive ads
                if (['active', 'inactive'].includes(report.ad_status)) {
                    btnSuspendAd.style.display = 'inline-flex';
                }

                // Can delete suspended or active ads
                if (['active', 'inactive', 'suspended'].includes(report.ad_status)) {
                    btnDeleteAd.style.display = 'inline-flex';
                }
            }

            // Cannot modify resolved or dismissed reports
            if (report.status === 'resolved' || report.status === 'dismissed') {
                document.getElementById('updateStatus').disabled = true;
                document.getElementById('updatePriority').disabled = true;
                document.getElementById('moderatorNotes').disabled = true;
            } else {
                document.getElementById('updateStatus').disabled = false;
                document.getElementById('updatePriority').disabled = false;
                document.getElementById('moderatorNotes').disabled = false;
            }
        }

        function rejectReport() {
            if (!confirm('Are you sure you want to dismiss this report? This action cannot be undone.')) {
                return;
            }
            document.getElementById('currentAction').value = 'reject_report';
            document.getElementById('updateReportForm').submit();
        }

        function suspendAd() {
            if (!confirm('Are you sure you want to SUSPEND this advertisement? The ad will be removed from active listings.')) {
                return;
            }
            document.getElementById('currentAction').value = 'suspend_ad';
            document.getElementById('updateReportForm').submit();
        }

        function deleteAd() {
            if (!confirm('⚠️ WARNING: Are you sure you want to PERMANENTLY DELETE this advertisement? This action cannot be undone!')) {
                return;
            }
            const confirmed = prompt('Type "DELETE" to confirm permanent deletion:');
            if (confirmed !== 'DELETE') {
                alert('Deletion cancelled');
                return;
            }
            document.getElementById('currentAction').value = 'delete_ad';
            document.getElementById('updateReportForm').submit();
        }

        function escalateReport() {
            if (!confirm('Are you sure you want to escalate this report to admin?')) {
                return;
            }
            document.getElementById('currentAction').value = 'escalate';
            document.getElementById('updateReportForm').submit();
        }

        function closeManageModal() {
            document.getElementById('manageReportModal').classList.remove('show');
            currentReport = null;
        }

        document.getElementById('manageReportModal').addEventListener('click', (e) => {
            if (e.target.id === 'manageReportModal') closeManageModal();
        });

        function showLoading() {
            document.getElementById('loadingState').classList.remove('hidden');
            document.getElementById('errorState').classList.add('hidden');
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('reportsTable').classList.add('hidden');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.getElementById('searchInput')?.addEventListener('input', debounce(() => loadReports(1), 500));
        document.getElementById('statusFilter')?.addEventListener('change', () => loadReports(1));
        document.getElementById('typeFilter')?.addEventListener('change', () => loadReports(1));
        document.getElementById('priorityFilter')?.addEventListener('change', () => loadReports(1));

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
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