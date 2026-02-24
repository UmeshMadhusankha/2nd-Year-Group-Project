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
require_once __DIR__ . '/../../includes/admin-modarator/mock-data.php';

// // Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("admin", $basePath);
// $user = getCurrentUser();

$basePath = '../..';
$currentPath = 'issues';
$message = '';

// Get mock issues data
$issuesData = getAdReports();

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/issues.css">

            <?php renderPageHeader($basePath, 'Issues & Reports', 'System-wide issue management and oversight'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Issues & Reports</h2>
                        <p class="text-muted-foreground">Manage and resolve user-reported issues and complaints</p>
                    </div>

                    <div id="statsContainer" class="issues-stats-grid">
                        Stats will be loaded dynamically
                    </div>

                    <div class="issues-table-container">
                        <div class="issues-table-header">
                            <h3 class="text-lg font-semibold">All Issues</h3>
                            <p class="text-muted-foreground text-sm">View and manage all reported issues</p>
                            <div class="issues-search-container">
                                <div class="issues-search-input">
                                    <i data-lucide="search" class="issues-search-icon"></i>
                                    <input type="text" id="searchInput" placeholder="Search issues...">
                                </div>
                                <select id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="investigating">Investigating</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="escalated">Escalated</option>
                                </select>
                                <select id="priorityFilter">
                                    <option value="">All Priorities</option>
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>

                        <div id="loadingState" class="p-8 text-center">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                            <p class="mt-2 text-muted-foreground">Loading issues...</p>
                        </div>

                        <div id="errorState" class="p-8 text-center hidden">
                            <i data-lucide="alert-circle" class="h-12 w-12 text-destructive mx-auto mb-2"></i>
                            <p class="text-destructive font-medium">Failed to load issues</p>
                            <p class="text-muted-foreground text-sm mt-1" id="errorMessage"></p>
                            <button onclick="loadIssues()" class="mt-4 btn btn-secondary">Try Again</button>
                        </div>

                        <div id="emptyState" class="p-8 text-center hidden">
                            <i data-lucide="inbox" class="h-12 w-12 text-muted-foreground mx-auto mb-2"></i>
                            <p class="font-medium">No issues found</p>
                            <p class="text-muted-foreground text-sm mt-1">No issues match your current filters</p>
                        </div>

                        <div id="issuesTableWrapper" class="hidden">
                            <div class="overflow-x-auto">
                                <table class="issues-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Issue Description</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="issuesTableBody">
                                        Issues will be loaded here
                                    </tbody>
                                </table>
                            </div>

                            <div id="pagination" class="p-4 border-t flex items-center justify-between">
                                Pagination will be loaded here
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <div id="resolveModal" class="issues-modal">
                <div class="issues-modal-content">
                    <div class="issues-modal-header">
                        <h3 class="text-lg font-semibold">Manage Issue</h3>
                        <p class="text-muted-foreground text-sm">Update issue status and add notes</p>
                        <button onclick="closeResolveModal()" class="modal-close-btn">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>

                    <div id="issueDetailsContent" class="p-6 border-b">
                        Issue details will be loaded here
                    </div>

                    <form id="resolveForm" class="issues-modal-form">
                        <input type="hidden" id="issueId">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="issues-form-group">
                                <label class="issues-form-label">Status</label>
                                <select id="issueStatus" class="issues-form-select">
                                    <option value="pending">Pending</option>
                                    <option value="investigating">Investigating</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="escalated">Escalated</option>
                                </select>
                            </div>

                            <div class="issues-form-group">
                                <label class="issues-form-label">Priority</label>
                                <select id="issuePriority" class="issues-form-select">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <div class="issues-form-group">
                            <label class="issues-form-label">Resolution Notes</label>
                            <textarea id="resolution" rows="4" placeholder="Describe how this issue was resolved or add investigation notes..."
                                class="issues-form-textarea"></textarea>
                        </div>

                        <div id="resolveErrorAlert" class="hidden bg-destructive/10 border border-destructive/20 text-destructive px-4 py-3 rounded mb-4">
                            <p id="resolveErrorMessage"></p>
                        </div>

                        <div class="issues-form-actions">
                            <button type="submit" id="resolveBtn" class="issues-submit-btn">
                                Update Issue
                            </button>
                            <button type="button" onclick="deleteReporterUser()" class="issues-delete-user-btn">
                                <i data-lucide="user-x" class="mr-1 h-4 w-4"></i>
                                Delete User
                            </button>
                            <button type="button" onclick="deleteIssue()" class="issues-delete-btn">
                                Delete Issue
                            </button>
                            <button type="button" onclick="closeResolveModal()" class="issues-cancel-btn">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // Mock data embedded from PHP
                const mockIssuesData = <?= json_encode($issuesData) ?>;
                
                let currentPage = 1;
                let currentIssueId = null;
                let allIssues = [];

                // Initialize issues from mock data
                function initializeIssues() {
                    allIssues = mockIssuesData.map(issue => ({
                        id: issue.id,
                        reporter_name: issue.user_name || issue.user || 'Unknown',
                        reporter_email: issue.user_email || '',
                        type: (issue.issue_type || 'other').toLowerCase().replace(/ /g, '_'),
                        description: issue.description || issue.issue || '',
                        priority: (issue.priority || 'low').toLowerCase(),
                        status: (issue.status || 'pending').toLowerCase().replace(/ /g, '_'),
                        created_at: issue.created_date || issue.date || new Date().toISOString(),
                        evidence: issue.evidence || null,
                        moderator_notes: issue.moderator_notes || null
                    }));
                }

                function loadIssues(page = 1) {
                    currentPage = page;

                    const searchInput = document.getElementById('searchInput');
                    const statusFilter = document.getElementById('statusFilter');
                    const priorityFilter = document.getElementById('priorityFilter');

                    const search = searchInput ? searchInput.value.toLowerCase() : '';
                    const status = statusFilter ? statusFilter.value : '';
                    const priority = priorityFilter ? priorityFilter.value : '';

                    showLoading();

                    // Filter issues
                    let filtered = allIssues.filter(issue => {
                        if (search && !issue.description.toLowerCase().includes(search) && 
                            !issue.reporter_name.toLowerCase().includes(search)) return false;
                        if (status && issue.status !== status) return false;
                        if (priority && issue.priority !== priority) return false;
                        return true;
                    });

                    // Pagination
                    const limit = 20;
                    const total = filtered.length;
                    const pages = Math.ceil(total / limit) || 1;
                    const offset = (page - 1) * limit;
                    const data = filtered.slice(offset, offset + limit);

                    displayIssues(data, { page, pages, total });
                    updateStats(filtered);
                }

                function displayIssues(issues, pagination) {
                    const tableBody = document.getElementById('issuesTableBody');
                    const loadingState = document.getElementById('loadingState');
                    const errorState = document.getElementById('errorState');
                    const emptyState = document.getElementById('emptyState');
                    const issuesTableWrapper = document.getElementById('issuesTableWrapper');

                    loadingState.classList.add('hidden');
                    errorState.classList.add('hidden');
                    emptyState.classList.add('hidden');

                    if (issues.length === 0) {
                        emptyState.classList.remove('hidden');
                        issuesTableWrapper.classList.add('hidden');
                        return;
                    }

                    issuesTableWrapper.classList.remove('hidden');

                    const statusColors = {
                        'investigating': 'secondary',
                        'resolved': 'default',
                        'pending': 'outline',
                        'escalated': 'destructive'
                    };

                    const priorityColors = {
                        'low': 'secondary',
                        'medium': 'outline',
                        'high': 'destructive'
                    };

                    tableBody.innerHTML = issues.map(issue => `
        <tr>
            <td class="text-card-foreground font-medium">#${issue.id}</td>
            <td class="text-muted-foreground">${escapeHtml(issue.reporter_name || 'Unknown')}</td>
            <td class="text-muted-foreground issues-description">${escapeHtml(issue.description)}</td>
            <td>
                <span class="badge badge-${priorityColors[issue.priority]}">${issue.priority}</span>
            </td>
            <td>
                <span class="badge badge-${statusColors[issue.status]}">${issue.status}</span>
            </td>
            <td class="text-muted-foreground">${new Date(issue.created_at).toLocaleDateString()}</td>
            <td>
                <button onclick="resolveIssue(${issue.id})" class="issues-resolve-btn">
                    Manage
                </button>
            </td>
        </tr>
    `).join('');

                    displayPagination(pagination);
                    lucide.createIcons();
                }

                function displayPagination(pagination) {
                    const paginationDiv = document.getElementById('pagination');

                    if (pagination.pages <= 1) {
                        paginationDiv.innerHTML = '';
                        return;
                    }

                    let paginationHTML = `
        <div class="text-sm text-muted-foreground">
            Showing page ${pagination.page} of ${pagination.pages} (${pagination.total} total)
        </div>
        <div class="flex gap-2">
    `;

                    if (pagination.page > 1) {
                        paginationHTML += `<button onclick="loadIssues(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
                    }

                    if (pagination.page < pagination.pages) {
                        paginationHTML += `<button onclick="loadIssues(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
                    }

                    paginationHTML += '</div>';
                    paginationDiv.innerHTML = paginationHTML;
                }

                function updateStats(issues) {
                    const statsContainer = document.getElementById('statsContainer');

                    const total = issues.length;
                    const pending = issues.filter(i => i.status === 'pending').length;
                    const investigating = issues.filter(i => i.status === 'investigating').length;
                    const resolved = issues.filter(i => i.status === 'resolved').length;

                    statsContainer.innerHTML = `
        ${renderStatCard('Total Issues', total, '', 'message-square', 'blue')}
        ${renderStatCard('Pending', pending, '', 'clock', 'yellow')}
        ${renderStatCard('In Progress', investigating, '', 'alert-triangle', 'orange')}
        ${renderStatCard('Resolved', resolved, '', 'check-circle', 'green')}
    `;

                    lucide.createIcons();
                }

                function renderStatCard(title, value, description, icon, color) {
                    return `
        <div class="bg-card rounded-lg border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">${title}</p>
                    <p class="text-2xl font-bold mt-2">${value}</p>
                    ${description ? `<p class="text-xs text-muted-foreground mt-1">${description}</p>` : ''}
                </div>
                <i data-lucide="${icon}" class="h-8 w-8 text-${color}-600"></i>
            </div>
        </div>
    `;
                }

                function resolveIssue(id) {
                    currentIssueId = id;

                    const issue = allIssues.find(i => i.id === id);
                    
                    if (!issue) {
                        alert('Issue not found');
                        return;
                    }

                    const detailsContent = document.getElementById('issueDetailsContent');

                    const typeLabels = {
                        'harassment': 'Harassment',
                        'service_issue': 'Service Issue',
                        'payment_dispute': 'Payment Dispute',
                        'fraud': 'Fraud',
                        'spam': 'Spam',
                        'performance_issue': 'Performance Issue',
                        'billing_problem': 'Billing Problem',
                        'technical_error': 'Technical Error',
                        'content_issue': 'Content Issue',
                        'other': 'Other'
                    };

                    detailsContent.innerHTML = `
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Reporter</label>
                    <p class="text-foreground">${escapeHtml(issue.reporter_name || 'Unknown')}</p>
                    <p class="text-sm text-muted-foreground">${escapeHtml(issue.reporter_email || '')}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Type</label>
                    <p class="text-foreground">${typeLabels[issue.type] || issue.type}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Description</label>
                    <p class="text-foreground">${escapeHtml(issue.description)}</p>
                </div>
                ${issue.evidence ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Evidence</label>
                    <p class="text-foreground">${escapeHtml(issue.evidence)}</p>
                </div>
                ` : ''}
                ${issue.moderator_notes ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Previous Notes</label>
                    <p class="text-foreground">${escapeHtml(issue.moderator_notes)}</p>
                </div>
                ` : ''}
            </div>
        `;

                    // Set form values
                    document.getElementById('issueId').value = issue.id;
                    document.getElementById('issueStatus').value = issue.status;
                    document.getElementById('issuePriority').value = issue.priority;
                    document.getElementById('resolution').value = issue.moderator_notes || '';

                    document.getElementById('resolveModal').classList.add('show');
                    lucide.createIcons();
                }

                function closeResolveModal() {
                    document.getElementById('resolveModal').classList.remove('show');
                    document.getElementById('resolveErrorAlert').classList.add('hidden');
                    currentIssueId = null;
                }

                // Handle form submission
                document.getElementById('resolveForm').addEventListener('submit', (e) => {
                    e.preventDefault();

                    const resolveBtn = document.getElementById('resolveBtn');
                    const errorAlert = document.getElementById('resolveErrorAlert');

                    resolveBtn.disabled = true;
                    resolveBtn.textContent = 'Updating...';
                    errorAlert.classList.add('hidden');

                    const updateData = {
                        status: document.getElementById('issueStatus').value,
                        priority: document.getElementById('issuePriority').value,
                        moderator_notes: document.getElementById('resolution').value
                    };

                    // Update local data
                    const issueIndex = allIssues.findIndex(i => i.id === currentIssueId);
                    if (issueIndex !== -1) {
                        allIssues[issueIndex].status = updateData.status;
                        allIssues[issueIndex].priority = updateData.priority;
                        allIssues[issueIndex].moderator_notes = updateData.moderator_notes;
                    }

                    // Simulate async operation
                    setTimeout(() => {
                        resolveBtn.disabled = false;
                        resolveBtn.textContent = 'Update Issue';
                        closeResolveModal();
                        loadIssues(currentPage);
                    }, 300);
                });

                function deleteIssue() {
                    if (!confirm('Are you sure you want to delete this issue? This action cannot be undone.')) {
                        return;
                    }

                    // Remove from local data
                    const issueIndex = allIssues.findIndex(i => i.id === currentIssueId);
                    if (issueIndex !== -1) {
                        allIssues.splice(issueIndex, 1);
                    }

                    closeResolveModal();
                    loadIssues(currentPage);
                }

                function deleteReporterUser() {
                    const issue = allIssues.find(i => i.id === currentIssueId);
                    
                    if (!issue) {
                        alert('Issue not found');
                        return;
                    }

                    const userName = issue.reporter_name || 'Unknown';
                    const userEmail = issue.reporter_email || '';
                    
                    if (!confirm(`Are you sure you want to delete the user account?\n\nUser: ${userName}\nEmail: ${userEmail}\n\nThis will permanently delete their account and cannot be undone.`)) {
                        return;
                    }

                    // Here you would make an API call to delete the user
                    // For now, just show a success message
                    alert(`User account "${userName}" has been deleted successfully.`);
                    
                    // Also delete the issue since the user is deleted
                    const issueIndex = allIssues.findIndex(i => i.id === currentIssueId);
                    if (issueIndex !== -1) {
                        allIssues.splice(issueIndex, 1);
                    }

                    closeResolveModal();
                    loadIssues(currentPage);
                }

                function showLoading() {
                    document.getElementById('loadingState').classList.remove('hidden');
                    document.getElementById('errorState').classList.add('hidden');
                    document.getElementById('emptyState').classList.add('hidden');
                    document.getElementById('issuesTableWrapper').classList.add('hidden');
                }

                function showError(message) {
                    document.getElementById('loadingState').classList.add('hidden');
                    document.getElementById('errorState').classList.remove('hidden');
                    document.getElementById('errorMessage').textContent = message;
                    document.getElementById('emptyState').classList.add('hidden');
                    document.getElementById('issuesTableWrapper').classList.add('hidden');
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Event listeners
                document.getElementById('searchInput')?.addEventListener('input', debounce(() => loadIssues(1), 500));
                document.getElementById('statusFilter')?.addEventListener('change', () => loadIssues(1));
                document.getElementById('priorityFilter')?.addEventListener('change', () => loadIssues(1));

                function debounce(func, wait) {
                    let timeout;
                    return function executedFunction(...args) {
                        const later = () => {
                            clearTimeout(timeout);
                            func(...args);
                        };
                        clearTimeout(timeout);
                        timeout = setTimeout(later, wait);
                    };
                }

                // Load issues on page load
                initializeIssues();
                loadIssues();
                lucide.createIcons();
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>

