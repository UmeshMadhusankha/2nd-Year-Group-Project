<?php
require_once __DIR__ . '/../common/Header.php';
require_once __DIR__ . '/../common/Common.php';
require_once __DIR__ . '/../../../views/other/includes/auth.php';

?>
<link rel="stylesheet" href="<?= $basePath ?>/assets/css/admin & moderator/reports.css">

<?php renderPageHeader($basePath,'Reports Management', 'Review and resolve user-submitted reports'); ?>

<main style="margin-top: 5rem;" class="dashboard-content">
    <div class="space-y-6">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Reports Management</h2>
            <p class="text-muted-foreground">Review, investigate, and resolve user-submitted reports</p>
        </div>

        <div id="statsContainer" class="grid gap-4 grid-cols-4">
             Stats will be loaded dynamically 
        </div>

        <div class="bg-card rounded-lg border">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">All Reports</h3>
                <p class="text-muted-foreground text-sm">View and manage all user reports</p>
                
                <div class="mt-4 flex gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground"></i>
                            <input 
                                type="text" 
                                id="searchInput"
                                placeholder="Search reports..." 
                                class="w-full pl-10 pr-4 py-2 border rounded-md"
                            >
                        </div>
                    </div>
                    <select id="statusFilter" class="border rounded-md px-4 py-2">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="investigating">Investigating</option>
                        <option value="resolved">Resolved</option>
                        <option value="escalated">Escalated</option>
                    </select>
                    <select id="typeFilter" class="border rounded-md px-4 py-2">
                        <option value="">All Types</option>
                        <option value="harassment">Harassment</option>
                        <option value="service_issue">Service Issue</option>
                        <option value="payment_dispute">Payment Dispute</option>
                        <option value="fraud">Fraud</option>
                        <option value="spam">Spam</option>
                        <option value="other">Other</option>
                    </select>
                    <select id="priorityFilter" class="border rounded-md px-4 py-2">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div id="loadingState" class="p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                <p class="mt-2 text-muted-foreground">Loading reports...</p>
            </div>

            <div id="errorState" class="p-8 text-center hidden">
                <i data-lucide="alert-circle" class="h-12 w-12 text-destructive mx-auto mb-2"></i>
                <p class="text-destructive font-medium">Failed to load reports</p>
                <p class="text-muted-foreground text-sm mt-1" id="errorMessage"></p>
                <button onclick="loadReports()" class="mt-4 btn btn-secondary">Try Again</button>
            </div>

            <div id="emptyState" class="p-8 text-center hidden">
                <i data-lucide="inbox" class="h-12 w-12 text-muted-foreground mx-auto mb-2"></i>
                <p class="font-medium">No reports found</p>
                <p class="text-muted-foreground text-sm mt-1">No reports match your current filters</p>
            </div>

            <div id="reportsTable" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-muted/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Reporter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-muted-foreground uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody" class="bg-card divide-y divide-border">
                             Reports will be loaded here 
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

<div id="manageReportModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-card rounded-lg max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold">Manage Report</h3>
                <button onclick="closeManageModal()" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
        </div>
        <div id="reportDetailsContent" class="p-6">
             Report details will be loaded here 
        </div>
        <div class="p-6 border-t bg-muted/20">
            <form id="updateReportForm" class="space-y-4">
                <input type="hidden" id="currentReportId">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="updateStatus" class="block text-sm font-medium mb-2">Status</label>
                        <select id="updateStatus" class="w-full border rounded-md px-4 py-2">
                            <option value="pending">Pending</option>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="escalated">Escalated</option>
                        </select>
                    </div>
                    <div>
                        <label for="updatePriority" class="block text-sm font-medium mb-2">Priority</label>
                        <select id="updatePriority" class="w-full border rounded-md px-4 py-2">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="moderatorNotes" class="block text-sm font-medium mb-2">Moderator Notes</label>
                    <textarea 
                        id="moderatorNotes" 
                        rows="4" 
                        placeholder="Add notes about your investigation or resolution..."
                        class="w-full border rounded-md px-4 py-2"
                    ></textarea>
                </div>
                
                <div id="updateErrorAlert" class="hidden bg-destructive/10 border border-destructive/20 text-destructive px-4 py-3 rounded">
                    <p id="updateErrorMessage"></p>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" id="updateBtn" class="btn btn-primary flex-1">
                        <i data-lucide="save" class="mr-2 h-4 w-4"></i>
                        Update Report
                    </button>
                    <button type="button" onclick="closeManageModal()" class="btn btn-secondary">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentFilters = {};

async function loadReports(page = 1) {
    currentPage = page;
    
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    
    const params = new URLSearchParams({
        page: page,
        limit: 20
    });
    
    if (searchInput && searchInput.value) params.append('search', searchInput.value);
    if (statusFilter && statusFilter.value) params.append('status', statusFilter.value);
    if (typeFilter && typeFilter.value) params.append('type', typeFilter.value);
    if (priorityFilter && priorityFilter.value) params.append('priority', priorityFilter.value);
    
    currentFilters = Object.fromEntries(params);
    
    showLoading();
    
    try {
        const response = await fetch(`<?= $basePath ?>/api/reports?${params}`);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || 'Failed to load reports');
        }
        
        displayReports(result.data, result.pagination);
        updateStats(result.data);
        
    } catch (error) {
        console.error('[v0] Error loading reports:', error);
        showError(error.message);
    }
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
    
    const statusColors = {
        'pending': 'badge-outline',
        'investigating': 'badge-secondary',
        'resolved': 'badge-default',
        'escalated': 'badge-destructive'
    };
    
    const priorityColors = {
        'low': 'badge-secondary',
        'medium': 'badge-outline',
        'high': 'badge-destructive'
    };
    
    const typeLabels = {
        'harassment': 'Harassment',
        'service_issue': 'Service Issue',
        'payment_dispute': 'Payment Dispute',
        'fraud': 'Fraud',
        'spam': 'Spam',
        'other': 'Other'
    };
    
    tableBody.innerHTML = reports.map(report => `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">#${report.id}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div>
                    <p class="font-medium">${escapeHtml(report.reporter_name || 'Unknown')}</p>
                    <p class="text-xs text-muted-foreground">${escapeHtml(report.reporter_email || '')}</p>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">${typeLabels[report.type] || report.type}</td>
            <td class="px-6 py-4 text-sm max-w-xs truncate">${escapeHtml(report.description)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="badge ${priorityColors[report.priority]}">${report.priority}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <span class="badge ${statusColors[report.status]}">${report.status}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-muted-foreground">
                ${new Date(report.created_at).toLocaleDateString()}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button onclick="manageReport(${report.id})" class="text-primary hover:underline">
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
        paginationHTML += `<button onclick="loadReports(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
    }
    
    if (pagination.page < pagination.pages) {
        paginationHTML += `<button onclick="loadReports(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
    }
    
    paginationHTML += '</div>';
    paginationDiv.innerHTML = paginationHTML;
}

function updateStats(reports) {
    const statsContainer = document.getElementById('statsContainer');
    
    // Calculate stats from current page data (in production, this should come from API)
    const pending = reports.filter(r => r.status === 'pending').length;
    const investigating = reports.filter(r => r.status === 'investigating').length;
    const escalated = reports.filter(r => r.status === 'escalated').length;
    const resolved = reports.filter(r => r.status === 'resolved').length;
    
    statsContainer.innerHTML = `
        ${renderStatCard('Pending', pending, 'Awaiting review', 'clock', 'text-yellow-600')}
        ${renderStatCard('Investigating', investigating, 'Under review', 'search', 'text-blue-600')}
        ${renderStatCard('Escalated', escalated, 'Needs attention', 'alert-triangle', 'text-red-600')}
        ${renderStatCard('Resolved', resolved, 'Completed', 'check-circle', 'text-green-600')}
    `;
    
    lucide.createIcons();
}

function renderStatCard(title, value, description, icon, iconColor) {
    return `
        <div class="bg-card rounded-lg border p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">${title}</p>
                    <p class="text-2xl font-bold mt-2">${value}</p>
                    ${description ? `<p class="text-xs text-muted-foreground mt-1">${description}</p>` : ''}
                </div>
                <i data-lucide="${icon}" class="h-8 w-8 ${iconColor}"></i>
            </div>
        </div>
    `;
}

async function manageReport(id) {
    try {
        const response = await fetch(`<?= $basePath ?>/api/reports/${id}`);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || 'Failed to load report details');
        }
        
        const report = result.data;
        const detailsContent = document.getElementById('reportDetailsContent');
        
        const typeLabels = {
            'harassment': 'Harassment',
            'service_issue': 'Service Issue',
            'payment_dispute': 'Payment Dispute',
            'fraud': 'Fraud',
            'spam': 'Spam',
            'other': 'Other'
        };
        
        detailsContent.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Report ID</label>
                        <p class="text-foreground">#${report.id}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Submitted</label>
                        <p class="text-foreground">${new Date(report.created_at).toLocaleString()}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Reporter</label>
                        <p class="text-foreground">${escapeHtml(report.reporter_name || 'Unknown')}</p>
                        <p class="text-sm text-muted-foreground">${escapeHtml(report.reporter_email || '')}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-muted-foreground">Type</label>
                        <p class="text-foreground">${typeLabels[report.type] || report.type}</p>
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Description</label>
                    <p class="text-foreground">${escapeHtml(report.description)}</p>
                </div>
                ${report.evidence ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Evidence</label>
                    <p class="text-foreground">${escapeHtml(report.evidence)}</p>
                </div>
                ` : ''}
                ${report.reported_entity_type && report.reported_entity_type !== 'other' ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Related To</label>
                    <p class="text-foreground">${report.reported_entity_type} #${report.reported_entity_id || 'N/A'}</p>
                </div>
                ` : ''}
                ${report.moderator_notes ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Previous Notes</label>
                    <p class="text-foreground">${escapeHtml(report.moderator_notes)}</p>
                </div>
                ` : ''}
                ${report.resolved_at ? `
                <div>
                    <label class="text-sm font-medium text-muted-foreground">Resolved</label>
                    <p class="text-foreground">${new Date(report.resolved_at).toLocaleString()}</p>
                    ${report.resolver_name ? `<p class="text-sm text-muted-foreground">By: ${report.resolver_name}</p>` : ''}
                </div>
                ` : ''}
            </div>
        `;
        
        // Set current values in form
        document.getElementById('currentReportId').value = report.id;
        document.getElementById('updateStatus').value = report.status;
        document.getElementById('updatePriority').value = report.priority;
        document.getElementById('moderatorNotes').value = report.moderator_notes || '';
        
        document.getElementById('manageReportModal').classList.remove('hidden');
        document.getElementById('manageReportModal').classList.add('flex');
        lucide.createIcons();
        
    } catch (error) {
        console.error('[v0] Error loading report details:', error);
        alert('Failed to load report details: ' + error.message);
    }
}

function closeManageModal() {
    document.getElementById('manageReportModal').classList.add('hidden');
    document.getElementById('manageReportModal').classList.remove('flex');
    document.getElementById('updateErrorAlert').classList.add('hidden');
}

// Handle update form submission
document.getElementById('updateReportForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const reportId = document.getElementById('currentReportId').value;
    const updateBtn = document.getElementById('updateBtn');
    const errorAlert = document.getElementById('updateErrorAlert');
    const errorMessage = document.getElementById('updateErrorMessage');
    
    // Disable button
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>Updating...';
    errorAlert.classList.add('hidden');
    
    const updateData = {
        status: document.getElementById('updateStatus').value,
        priority: document.getElementById('updatePriority').value,
        moderator_notes: document.getElementById('moderatorNotes').value
    };
    
    try {
        const response = await fetch(`<?= $basePath ?>/api/reports/${reportId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(updateData)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || 'Failed to update report');
        }
        
        // Close modal and reload reports
        closeManageModal();
        loadReports(currentPage);
        
    } catch (error) {
        console.error('[v0] Error updating report:', error);
        errorMessage.textContent = error.message;
        errorAlert.classList.remove('hidden');
        
        // Re-enable button
        updateBtn.disabled = false;
        updateBtn.innerHTML = '<i data-lucide="save" class="mr-2 h-4 w-4"></i>Update Report';
        lucide.createIcons();
    }
});

function showLoading() {
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('errorState').classList.add('hidden');
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('reportsTable').classList.add('hidden');
}

function showError(message) {
    document.getElementById('loadingState').classList.add('hidden');
    document.getElementById('errorState').classList.remove('hidden');
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('emptyState').classList.add('hidden');
    document.getElementById('reportsTable').classList.add('hidden');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners
document.getElementById('searchInput')?.addEventListener('input', debounce(() => loadReports(1), 500));
document.getElementById('statusFilter')?.addEventListener('change', () => loadReports(1));
document.getElementById('typeFilter')?.addEventListener('change', () => loadReports(1));
document.getElementById('priorityFilter')?.addEventListener('change', () => loadReports(1));

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

// Load reports on page load
loadReports();
lucide.createIcons();
</script>
