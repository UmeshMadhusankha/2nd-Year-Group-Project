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
require_once __DIR__ . '/../../includes/admin-modarator/auth.php';
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
$message = $messages['message'] ?? '';
$messageType = $messages['type'] ?? '';

// Get page title and description
$pageTitle = $title ?? 'Advertisement Reports';
$pageDescription = $description ?? 'Review and moderate advertisement-related reports';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

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
                        <p class="page-description">Review, investigate, and resolve advertisement-related reports</p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div style="
                            padding: 1rem 1.5rem;
                            border-radius: 8px;
                            margin-bottom: 1.5rem;
                            border: 2px solid <?php echo $messageType === 'success' ? '#10b981' : '#ef4444'; ?>;
                            background-color: <?php echo $messageType === 'success' ? '#d1fae5' : '#fee2e2'; ?>;
                            color: <?php echo $messageType === 'success' ? '#065f46' : '#991b1b'; ?>;
                            font-weight: 500;
                        ">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Boxes -->
                    <div id="statsContainer" class="stats-grid"></div>

                    <!-- Filters -->
                    <div class="filters-section">
                        <div class="filters-header">
                            <h3>Advertisement Reports</h3>
                            <p>View and manage all advertisement-related reports</p>
                        </div>

                        <div class="filters-controls">
                            <div class="search-box">
                                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <input type="text" id="searchInput" placeholder="Search by ad title, company, or reporter...">
                            </div>
                            <select id="statusFilter" class="filter-select">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                                <option value="escalated">Escalated</option>
                                <option value="reject_report">Rejected</option>
                                <option value="suspend_ad">Suspended</option>
                                <option value="delete_ad">Deleted</option>
                            </select>
                            <select id="typeFilter" class="filter-select">
                                <option value="">All Issue Types</option>
                                <option value="misleading_information">Misleading Information</option>
                                <option value="false_pricing">False Pricing</option>
                                <option value="inappropriate_content">Inappropriate Content</option>
                                <option value="duplicate_listing">Duplicate Listing</option>
                                <option value="spam_content">Spam Content</option>
                                <option value="expired_advertisement">Expired Advertisement</option>
                                <option value="policy_violation">Policy Violation</option>
                                <option value="unverified_claims">Unverified Claims</option>
                                <option value="inappropriate_images">Inappropriate Images</option>
                            </select>
                            <select id="priorityFilter" class="filter-select">
                                <option value="">All Priorities</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
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
                            <path d="M21.5 12H16l-2-3h-4l-2 3H2.5"></path>
                            <path d="M5.5 12v7h13v-7"></path>
                        </svg>
                        <p class="empty-title">No reports found</p>
                        <p class="empty-description">No advertisement reports match your current filters</p>
                    </div>

                    <!-- Reports Table -->
                    <div id="reportsTable" class="reports-table-container hidden">
                        <table class="reports-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ad Title</th>
                                    <th>Company</th>
                                    <th>Reporter</th>
                                    <th>Issue Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
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
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            Manage Advertisement Report
                        </h3>
                        <button onclick="closeManageModal()" class="modal-close">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                    <div id="reportDetailsContent" class="modal-body"></div>

                    <div class="modal-footer">
                        <form id="updateReportForm" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="modal-form">
                            <input type="hidden" name="action" value="update_report">
                            <input type="hidden" id="currentReportId" name="report_id">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="updateStatus" class="form-label">Status</label>
                                    <select id="updateStatus" name="status" class="form-select">
                                        <option value="pending">Pending</option>
                                        <option value="investigating">Investigating</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="escalated">Escalated</option>
                                        <option value="reject_report">Reject Report</option>
                                        <option value="suspend_ad">Suspend Advertisement</option>
                                        <option value="delete_ad">Delete Advertisement</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="updatePriority" class="form-label">Priority</label>
                                    <select id="updatePriority" name="priority" class="form-select">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="moderatorNotes" class="form-label">Moderator Notes</label>
                                <textarea id="moderatorNotes" name="moderator_notes" class="form-textarea" placeholder="Add notes about your investigation or action taken..."></textarea>
                            </div>
                            <div class="form-buttons">
                                <button type="submit" id="updateBtn" class="btn-submit">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                        <polyline points="7 3 7 8 15 8"></polyline>
                                    </svg>
                                    Update Report
                                </button>
                                <button type="button" onclick="closeManageModal()" class="btn-cancel">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                const mockReportsData = <?= json_encode($reportsData) ?>;
                let currentPage = 1;
                let allReports = [];

                function initializeReports() {
                    allReports = mockReportsData.map(report => ({
                        id: report.id,
                        ad_id: report.ad_id || null,
                        ad_title: report.ad_title || 'Unknown Advertisement',
                        company_name: report.company_name || 'Unknown Company',
                        reporter_name: report.user_name || report.user || 'Unknown',
                        reporter_email: report.user_email || '',
                        type: (report.issue_type || 'other').toLowerCase().replace(/ /g, '_'),
                        description: report.description || report.issue || '',
                        priority: (report.priority || 'low').toLowerCase(),
                        status: (report.status || 'pending').toLowerCase().replace(/ /g, '_'),
                        created_at: report.created_date || report.date || new Date().toISOString(),
                        moderator_notes: report.moderator_notes || '',
                        category: report.category || 'advertisement',
                        evidence: report.evidence || ''
                    }));

                    allReports = allReports.filter(r => r.category === 'advertisement');
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
                            !report.company_name.toLowerCase().includes(search) &&
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

                    displayReports(data, {
                        page,
                        pages,
                        total
                    });
                    updateStats(filtered);
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
                        reject_report: 'badge-rejected',
                        suspend_ad: 'badge-suspended',
                        delete_ad: 'badge-deleted'
                    };

                    const statusLabels = {
                        pending: 'PENDING',
                        investigating: 'INVESTIGATING',
                        resolved: 'RESOLVED',
                        escalated: 'ESCALATED',
                        reject_report: 'REJECTED',
                        suspend_ad: 'SUSPENDED',
                        delete_ad: 'DELETED'
                    };

                    const priorityClasses = {
                        low: 'badge-low',
                        medium: 'badge-medium',
                        high: 'badge-high'
                    };

                    const typeLabels = {
                        misleading_information: 'Misleading Information',
                        false_pricing: 'False Pricing',
                        inappropriate_content: 'Inappropriate Content',
                        duplicate_listing: 'Duplicate Listing',
                        spam_content: 'Spam Content',
                        expired_advertisement: 'Expired Advertisement',
                        policy_violation: 'Policy Violation',
                        unverified_claims: 'Unverified Claims',
                        inappropriate_images: 'Inappropriate Images'
                    };

                    tableBody.innerHTML = reports.map(report => `
                        <tr>
                            <td><span class="report-id">#${report.id}</span></td>
                            <td>
                                <div style="font-weight: 500; color: #111827; margin-bottom: 4px;">${escapeHtml(report.ad_title)}</div>
                                <div style="font-size: 0.75rem; color: #6b7280;">Ad ID: ${report.ad_id || 'N/A'}</div>
                            </td>
                            <td style="font-weight: 500; color: #374151;">${escapeHtml(report.company_name)}</td>
                            <td>
                                <div class="reporter-info">
                                    <span class="reporter-name">${escapeHtml(report.reporter_name)}</span>
                                    ${report.reporter_email ? `<span class="reporter-email">${escapeHtml(report.reporter_email)}</span>` : ''}
                                </div>
                            </td>
                            <td style="font-size: 0.813rem;">${typeLabels[report.type] || report.type}</td>
                            <td><span class="badge ${priorityClasses[report.priority]}">${report.priority.toUpperCase()}</span></td>
                            <td><span class="badge ${statusClasses[report.status]}">${statusLabels[report.status] || report.status.replace('_', ' ').toUpperCase()}</span></td>
                            <td>${new Date(report.created_at).toLocaleDateString()}</td>
                            <td>
                                <button onclick="manageReport(${report.id})" class="btn-manage">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path d="M12 1v6m0 6v6"></path>
                                    </svg>
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

                function updateStats(reports) {
                    const pending = reports.filter(r => r.status === 'pending').length;
                    const investigating = reports.filter(r => r.status === 'investigating').length;
                    const escalated = reports.filter(r => r.status === 'escalated').length;
                    const resolved = reports.filter(r => r.status === 'resolved').length;
                    const rejected = reports.filter(r => r.status === 'reject_report').length;
                    const suspended = reports.filter(r => r.status === 'suspend_ad').length;
                    const deleted = reports.filter(r => r.status === 'delete_ad').length;

                    document.getElementById('statsContainer').innerHTML = `
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${pending}</p>
                                    <p class="stat-card-title">Pending</p>
                                    <p class="stat-card-description">Awaiting review</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${investigating}</p>
                                    <p class="stat-card-title">Investigating</p>
                                    <p class="stat-card-description">Under review</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${escalated}</p>
                                    <p class="stat-card-title">Escalated</p>
                                    <p class="stat-card-description">Needs attention</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${resolved}</p>
                                    <p class="stat-card-title">Resolved</p>
                                    <p class="stat-card-description">Completed</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${rejected}</p>
                                    <p class="stat-card-title">Rejected</p>
                                    <p class="stat-card-description">Reports rejected</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${suspended}</p>
                                    <p class="stat-card-title">Suspended</p>
                                    <p class="stat-card-description">Ads suspended</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-card-header">
                                <div>
                                    <p class="stat-card-value">${deleted}</p>
                                    <p class="stat-card-title">Deleted</p>
                                    <p class="stat-card-description">Ads removed</p>
                                </div>
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </div>
                        </div>
                    `;
                }

                function manageReport(id) {
                    const report = allReports.find(r => r.id === id);
                    if (!report) {
                        alert('Report not found');
                        return;
                    }

                    const typeLabels = {
                        misleading_information: 'Misleading Information',
                        false_pricing: 'False Pricing',
                        inappropriate_content: 'Inappropriate Content',
                        duplicate_listing: 'Duplicate Listing',
                        spam_content: 'Spam Content',
                        expired_advertisement: 'Expired Advertisement',
                        policy_violation: 'Policy Violation',
                        unverified_claims: 'Unverified Claims',
                        inappropriate_images: 'Inappropriate Images'
                    };

                    const statusLabels = {
                        pending: 'PENDING',
                        investigating: 'INVESTIGATING',
                        resolved: 'RESOLVED',
                        escalated: 'ESCALATED',
                        reject_report: 'REJECTED',
                        suspend_ad: 'SUSPENDED',
                        delete_ad: 'DELETED'
                    };

                    document.getElementById('reportDetailsContent').innerHTML = `
                        <div class="report-details">
                            <div class="detail-row">
                                <div class="detail-field">
                                    <label class="detail-label">Report ID</label>
                                    <p class="detail-value detail-value-large">#${report.id}</p>
                                </div>
                                <div class="detail-field">
                                    <label class="detail-label">Date Submitted</label>
                                    <p class="detail-value">${new Date(report.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-field">
                                    <label class="detail-label">Advertisement Title</label>
                                    <p class="detail-value">${escapeHtml(report.ad_title)}</p>
                                    <p class="detail-value-secondary">Ad ID: ${report.ad_id}</p>
                                </div>
                                <div class="detail-field">
                                    <label class="detail-label">Company Name</label>
                                    <p class="detail-value">${escapeHtml(report.company_name)}</p>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-field">
                                    <label class="detail-label">Reported By</label>
                                    <p class="detail-value">${escapeHtml(report.reporter_name)}</p>
                                    ${report.reporter_email ? `<p class="detail-value-secondary">${escapeHtml(report.reporter_email)}</p>` : ''}
                                </div>
                                <div class="detail-field">
                                    <label class="detail-label">Issue Type</label>
                                    <p class="detail-value">${typeLabels[report.type] || report.type}</p>
                                </div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-field">
                                    <label class="detail-label">Priority Level</label>
                                    <p class="detail-value"><span class="badge badge-${report.priority}">${report.priority.toUpperCase()}</span></p>
                                </div>
                                <div class="detail-field">
                                    <label class="detail-label">Current Status</label>
                                    <p class="detail-value"><span class="badge badge-${report.status}">${statusLabels[report.status] || report.status.replace('_', ' ').toUpperCase()}</span></p>
                                </div>
                            </div>
                            <div class="detail-field">
                                <label class="detail-label">Problem Description</label>
                                <div class="description-box">
                                    <p class="detail-value">${escapeHtml(report.description)}</p>
                                </div>
                            </div>
                            ${report.evidence ? `
                                <div class="detail-field">
                                    <label class="detail-label">Evidence Provided</label>
                                    <div class="description-box">
                                        <p class="detail-value">${escapeHtml(report.evidence)}</p>
                                    </div>
                                </div>
                            ` : ''}
                            ${report.moderator_notes ? `
                                <div class="detail-field">
                                    <label class="detail-label">Previous Moderator Notes</label>
                                    <div class="description-box">
                                        <p class="detail-value">${escapeHtml(report.moderator_notes)}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;

                    document.getElementById('currentReportId').value = report.id;
                    document.getElementById('updateStatus').value = report.status;
                    document.getElementById('updatePriority').value = report.priority;
                    document.getElementById('moderatorNotes').value = report.moderator_notes || '';
                    document.getElementById('manageReportModal').classList.add('show');
                }

                function closeManageModal() {
                    document.getElementById('manageReportModal').classList.remove('show');
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
                        timeout = setTimeout(() => func(...args), wait);
                    };
                }

                initializeReports();
                loadReports();
            </script>
        </div>
    </div>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>