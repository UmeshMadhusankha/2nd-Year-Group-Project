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

$basePath = '';
$currentPath = 'ads';
$message = '';

// Get mock data
$adsData = $mockAds;

$pageTitle = 'Advertisement Review - Admin';
$pageDescription = 'Review and manage advertisement approvals';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/ads.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Review', 'Review and manage advertisement approvals'); ?>

            <main class="admin-ads-content">
                <!-- Success/Error Message -->
                <div id="messageContainer" style="display: none;"></div>

                <!-- Page Header -->
                <div class="page-header">
                    <h2 class="page-title">Advertisement Review</h2>
                    <p class="page-description">Oversee advertisement approvals and manage ad lifecycle</p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card stat-blue">
                        <div class="stat-content">
                            <div class="stat-info">
                                <h3 class="stat-label">Total Advertisements</h3>
                                <div class="stat-value" id="totalAds">0</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="monitor"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-amber">
                        <div class="stat-content">
                            <div class="stat-info">
                                <h3 class="stat-label">Pending Review</h3>
                                <div class="stat-value" id="pendingAds">0</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-emerald">
                        <div class="stat-content">
                            <div class="stat-info">
                                <h3 class="stat-label">Approved</h3>
                                <div class="stat-value" id="approvedAds">0</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-rose">
                        <div class="stat-content">
                            <div class="stat-info">
                                <h3 class="stat-label">Rejected</h3>
                                <div class="stat-value" id="rejectedAds">0</div>
                            </div>
                            <div class="stat-icon">
                                <i data-lucide="x-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertisement Table Container -->
                <div class="table-card">
                    <!-- Table Header with Search & Filters -->
                    <div class="table-header">
                        <div>
                            <h3 class="table-title">Advertisement Queue</h3>
                            <p class="table-subtitle">Review and manage advertisement approvals and lifecycle</p>
                        </div>
                        
                        <div class="filters-container">
                            <div class="search-box">
                                <i data-lucide="search" class="search-icon"></i>
                                <input 
                                    type="text" 
                                    id="searchInput" 
                                    placeholder="Search by company or title..." 
                                    class="search-input"
                                    onkeyup="filterAds()"
                                >
                            </div>

                            <select id="statusFilter" class="filter-select" onchange="filterAds()">
                                <option value="">All Status</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Active">Active</option>
                                <option value="Rejected">Rejected</option>
                            </select>

                            <select id="typeFilter" class="filter-select" onchange="filterAds()">
                                <option value="">All Types</option>
                                <option value="Banner">Banner</option>
                                <option value="Sponsored">Sponsored</option>
                                <option value="Featured">Featured</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-wrapper">
                        <table class="ads-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th style="width: 180px;">Company</th>
                                    <th>Title</th>
                                    <th style="width: 120px;">Type</th>
                                    <th style="width: 140px;">Budget</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 280px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adsTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Loading advertisements...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>

            <!-- View Advertisement Modal -->
            <div id="viewModal" class="modal-overlay" onclick="handleModalBackdropClick(event, 'viewModal')">
                <div class="modal-dialog" onclick="event.stopPropagation()">
                    <div class="modal-header">
                        <h3 class="modal-title">Advertisement Details</h3>
                        <button type="button" onclick="closeModal('viewModal')" class="modal-close" title="Close">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body" id="adDetailsContent">
                        <!-- Content loaded by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div id="approveModal" class="modal-overlay" onclick="handleModalBackdropClick(event, 'approveModal')">
                <div class="modal-dialog modal-sm" onclick="event.stopPropagation()">
                    <div class="modal-header modal-success">
                        <h3 class="modal-title">Approve Advertisement</h3>
                        <button type="button" onclick="closeModal('approveModal')" class="modal-close" title="Close">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-message">Are you sure you want to approve this advertisement?</p>
                        <p class="modal-submessage">The advertisement will be marked as approved and ready for activation.</p>
                        
                        <div class="modal-actions">
                            <button type="button" onclick="closeModal('approveModal')" class="btn btn-secondary">
                                <i data-lucide="x"></i>
                                Cancel
                            </button>
                            <button type="button" onclick="confirmApprove()" class="btn btn-success">
                                <i data-lucide="check"></i>
                                Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div id="rejectModal" class="modal-overlay" onclick="handleModalBackdropClick(event, 'rejectModal')">
                <div class="modal-dialog modal-sm" onclick="event.stopPropagation()">
                    <div class="modal-header modal-danger">
                        <h3 class="modal-title">Reject Advertisement</h3>
                        <button type="button" onclick="closeModal('rejectModal')" class="modal-close" title="Close">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-message">Please provide a reason for rejecting this advertisement:</p>
                        
                        <textarea 
                            id="rejectReason" 
                            class="form-textarea" 
                            rows="4" 
                            placeholder="Enter detailed rejection reason..."
                            required
                        ></textarea>
                        
                        <div class="modal-actions">
                            <button type="button" onclick="closeModal('rejectModal')" class="btn btn-secondary">
                                <i data-lucide="x"></i>
                                Cancel
                            </button>
                            <button type="button" onclick="confirmReject()" class="btn btn-danger">
                                <i data-lucide="ban"></i>
                                Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suspend Modal -->
            <div id="suspendModal" class="modal-overlay" onclick="handleModalBackdropClick(event, 'suspendModal')">
                <div class="modal-dialog modal-sm" onclick="event.stopPropagation()">
                    <div class="modal-header modal-warning">
                        <h3 class="modal-title">Suspend Active Advertisement</h3>
                        <button type="button" onclick="closeModal('suspendModal')" class="modal-close" title="Close">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-message">Please provide a reason for suspending this active advertisement:</p>
                        
                        <textarea 
                            id="suspendReason" 
                            class="form-textarea" 
                            rows="4" 
                            placeholder="Enter suspension reason..."
                            required
                        ></textarea>
                        
                        <div class="modal-actions">
                            <button type="button" onclick="closeModal('suspendModal')" class="btn btn-secondary">
                                <i data-lucide="x"></i>
                                Cancel
                            </button>
                            <button type="button" onclick="confirmSuspend()" class="btn btn-warning">
                                <i data-lucide="pause-circle"></i>
                                Suspend
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Initialize Lucide icons
                lucide.createIcons();

                // Load mock data
                const allAds = <?= json_encode($adsData) ?>;
                let currentAdId = null;

                // Load stats and table on page load
                document.addEventListener('DOMContentLoaded', () => {
                    loadStats();
                    loadAdsTable();
                    setupKeyboardShortcuts();
                });

                // Setup ESC key to close modals
                function setupKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            closeAllModals();
                        }
                    });
                }

                // Close all modals
                function closeAllModals() {
                    ['viewModal', 'approveModal', 'rejectModal', 'suspendModal'].forEach(closeModal);
                }

                // Handle backdrop click
                function handleModalBackdropClick(event, modalId) {
                    if (event.target.classList.contains('modal-overlay')) {
                        closeModal(modalId);
                    }
                }

                // Load statistics
                function loadStats() {
                    const total = allAds.length;
                    const pending = allAds.filter(ad => ad.status === 'Pending').length;
                    const approved = allAds.filter(ad => ad.status === 'Approved').length;
                    const rejected = allAds.filter(ad => ad.status === 'Rejected').length;

                    document.getElementById('totalAds').textContent = total;
                    document.getElementById('pendingAds').textContent = pending;
                    document.getElementById('approvedAds').textContent = approved;
                    document.getElementById('rejectedAds').textContent = rejected;
                }

                // Filter ads based on search and filters
                function filterAds() {
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const statusFilter = document.getElementById('statusFilter').value;
                    const typeFilter = document.getElementById('typeFilter').value;

                    const filtered = allAds.filter(ad => {
                        const matchSearch = !search || 
                            ad.company.toLowerCase().includes(search) || 
                            ad.title.toLowerCase().includes(search);
                        const matchStatus = !statusFilter || ad.status === statusFilter;
                        const matchType = !typeFilter || ad.type === typeFilter;

                        return matchSearch && matchStatus && matchType;
                    });

                    renderAdsTable(filtered);
                }

                // Load and render ads table
                function loadAdsTable() {
                    renderAdsTable(allAds);
                }

                // Render ads table with STATUS-BASED ACTION BUTTONS
                function renderAdsTable(ads) {
                    const tbody = document.getElementById('adsTableBody');

                    if (ads.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No advertisements found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = ads.map(ad => {
                        // Status badge classes
                        const statusClass = {
                            'Pending': 'status-pending',
                            'Approved': 'status-approved',
                            'Active': 'status-active',
                            'Rejected': 'status-rejected'
                        }[ad.status] || 'status-pending';

                        // Type badge classes
                        const typeClass = {
                            'Banner': 'type-banner',
                            'Sponsored': 'type-sponsored',
                            'Featured': 'type-featured'
                        }[ad.type] || 'type-banner';

                        // CRITICAL: Action buttons based on status
                        let actionButtons = '';
                        
                        if (ad.status === 'Pending') {
                            // PENDING: Show View, Approve, Reject
                            actionButtons = `
                                <button onclick="viewAd(${ad.id})" class="action-btn btn-view" title="View Details">
                                    <i data-lucide="eye"></i>
                                    <span>View</span>
                                </button>
                                <button onclick="approveAd(${ad.id})" class="action-btn btn-approve" title="Approve">
                                    <i data-lucide="check"></i>
                                    <span>Approve</span>
                                </button>
                                <button onclick="rejectAd(${ad.id})" class="action-btn btn-reject" title="Reject">
                                    <i data-lucide="x"></i>
                                    <span>Reject</span>
                                </button>
                            `;
                        } else if (ad.status === 'Approved') {
                            // APPROVED: Show View only + no actions text
                            actionButtons = `
                                <button onclick="viewAd(${ad.id})" class="action-btn btn-view" title="View Details">
                                    <i data-lucide="eye"></i>
                                    <span>View</span>
                                </button>
                                <span class="no-actions-text">No actions available</span>
                            `;
                        } else if (ad.status === 'Rejected') {
                            // REJECTED: Show View only + no actions text
                            actionButtons = `
                                <button onclick="viewAd(${ad.id})" class="action-btn btn-view" title="View Details">
                                    <i data-lucide="eye"></i>
                                    <span>View</span>
                                </button>
                                <span class="no-actions-text">No actions available</span>
                            `;
                        } else if (ad.status === 'Active') {
                            // ACTIVE: Show View and Suspend
                            actionButtons = `
                                <button onclick="viewAd(${ad.id})" class="action-btn btn-view" title="View Details">
                                    <i data-lucide="eye"></i>
                                    <span>View</span>
                                </button>
                                <button onclick="suspendAd(${ad.id})" class="action-btn btn-suspend" title="Suspend">
                                    <i data-lucide="pause-circle"></i>
                                    <span>Suspend</span>
                                </button>
                            `;
                        }

                        return `
                            <tr>
                                <td class="text-center cell-id">#${ad.id}</td>
                                <td class="cell-company">${escapeHtml(ad.company)}</td>
                                <td class="cell-title">${escapeHtml(ad.title)}</td>
                                <td class="text-center">
                                    <span class="type-badge ${typeClass}">${ad.type}</span>
                                </td>
                                <td class="text-right cell-budget">LKR ${parseInt(ad.budget || 0).toLocaleString()}</td>
                                <td class="text-center">
                                    <span class="status-badge ${statusClass}">${ad.status}</span>
                                </td>
                                <td class="cell-actions">
                                    <div class="actions-wrapper">
                                        ${actionButtons}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    lucide.createIcons();
                }

                // View advertisement details
                function viewAd(adId) {
                    const ad = allAds.find(a => a.id === adId);
                    if (!ad) return;

                    const content = `
                        <div class="ad-details-grid">
                            <div class="detail-row">
                                <span class="detail-label">Ad ID:</span>
                                <span class="detail-value">#${ad.id}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Company:</span>
                                <span class="detail-value">${escapeHtml(ad.company)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Title:</span>
                                <span class="detail-value">${escapeHtml(ad.title)}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value">
                                    <span class="type-badge type-${ad.type.toLowerCase()}">${ad.type}</span>
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Budget:</span>
                                <span class="detail-value">LKR ${parseInt(ad.budget || 0).toLocaleString()}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">${ad.duration || 'Not specified'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge status-${ad.status.toLowerCase()}">${ad.status}</span>
                                </span>
                            </div>
                            <div class="detail-row detail-full">
                                <span class="detail-label">Description:</span>
                                <span class="detail-value">${ad.description || 'No description provided'}</span>
                            </div>
                            ${ad.rejection_reason ? `
                                <div class="detail-row detail-full rejection-reason">
                                    <span class="detail-label">Rejection Reason:</span>
                                    <span class="detail-value">${escapeHtml(ad.rejection_reason)}</span>
                                </div>
                            ` : ''}
                        </div>
                    `;

                    document.getElementById('adDetailsContent').innerHTML = content;
                    openModal('viewModal');
                }

                // Approve advertisement
                function approveAd(adId) {
                    currentAdId = adId;
                    openModal('approveModal');
                }

                // Confirm approve
                function confirmApprove() {
                    const ad = allAds.find(a => a.id === currentAdId);
                    if (ad) {
                        ad.status = 'Approved';
                        showMessage('Advertisement approved successfully!', 'success');
                        closeModal('approveModal');
                        loadStats();
                        filterAds();
                    }
                }

                // Reject advertisement
                function rejectAd(adId) {
                    currentAdId = adId;
                    document.getElementById('rejectReason').value = '';
                    openModal('rejectModal');
                }

                // Confirm reject
                function confirmReject() {
                    const reason = document.getElementById('rejectReason').value.trim();
                    
                    if (!reason) {
                        showMessage('Please provide a rejection reason', 'error');
                        return;
                    }

                    const ad = allAds.find(a => a.id === currentAdId);
                    if (ad) {
                        ad.status = 'Rejected';
                        ad.rejection_reason = reason;
                        showMessage('Advertisement rejected successfully!', 'success');
                        closeModal('rejectModal');
                        loadStats();
                        filterAds();
                    }
                }

                // Suspend advertisement
                function suspendAd(adId) {
                    currentAdId = adId;
                    document.getElementById('suspendReason').value = '';
                    openModal('suspendModal');
                }

                // Confirm suspend
                function confirmSuspend() {
                    const reason = document.getElementById('suspendReason').value.trim();
                    
                    if (!reason) {
                        showMessage('Please provide a suspension reason', 'error');
                        return;
                    }

                    const ad = allAds.find(a => a.id === currentAdId);
                    if (ad) {
                        ad.status = 'Rejected';
                        ad.rejection_reason = 'Suspended: ' + reason;
                        showMessage('Advertisement suspended successfully!', 'success');
                        closeModal('suspendModal');
                        loadStats();
                        filterAds();
                    }
                }

                // Open modal
                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                    document.body.style.overflow = 'hidden';
                    lucide.createIcons();
                }

                // Close modal
                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                    document.body.style.overflow = 'auto';
                }

                // Show message
                function showMessage(message, type) {
                    const container = document.getElementById('messageContainer');
                    container.textContent = message;
                    container.className = type === 'success' ? 'message-success' : 'message-error';
                    container.style.display = 'block';

                    setTimeout(() => {
                        container.style.display = 'none';
                    }, 4000);
                }

                // Escape HTML
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            </script>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>