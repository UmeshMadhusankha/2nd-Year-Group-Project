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
require_once '../../includes/admin-modarator/auth.php';
require_once '../../includes/admin-modarator/mock-data.php';

// // Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("admin", $basePath);
// $user = getCurrentUser();

$basePath = '';
$currentPath = 'ads';
$message = '';

// Get mock data
$adsData = $mockAds;

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
            <link rel="stylesheet" href="../../assets/css/admin/ads.css">
            <link rel="stylesheet" href="../../assets/css/admin/modals.css">
            <?php renderPageHeader($basePath, 'Advertisement Review', 'Review and manage submitted advertisements from companies'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Advertisement Review</h2>
                        <p class="text-muted-foreground">Review and manage submitted advertisements from companies</p>
                    </div>

                    <div id="messageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-4"></div>

                    <div class="ads-stats-grid" id="statsContainer">
                        Stats will be loaded dynamically
                    </div>

                    <div class="ads-table-container">
                        <div class="ads-table-header">
                            <h3 class="text-lg font-semibold">Advertisement Queue</h3>
                            <p class="text-muted-foreground text-sm">Review submitted advertisements and approve or reject them</p>
                            <div class="ads-search-container">
                                <div class="ads-search-input">
                                    <i data-lucide="search" class="ads-search-icon"></i>
                                    <input type="text" id="searchInput" placeholder="Search by company or title..." onkeyup="searchAds()">
                                </div>
                                <select id="statusFilter" onchange="loadAds()" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Active">Active</option>
                                    <option value="Expired">Expired</option>
                                </select>
                                <select id="typeFilter" onchange="loadAds()" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="Banner">Banner</option>
                                    <option value="Sponsored">Sponsored</option>
                                    <option value="Featured">Featured</option>
                                </select>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="ads-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th style="width: 200px;">Company</th>
                                        <th style="width: 250px;">Title</th>
                                        <th style="width: 120px;">Type</th>
                                        <th style="width: 120px;">Budget</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Loading ads...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer" class="flex justify-center mt-4"></div>
                    </div>
                </div>
            </main>

            <div id="reviewModal" class="modal-overlay">
                <div class="modal-content">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-foreground mb-4">Review Advertisement</h3>

                        <div class="ad-details" id="adDetailsContent">
                            Content will be populated by JavaScript
                        </div>

                        <form id="reviewForm" class="space-y-4 mt-6">
                            <input type="hidden" id="reviewAdId">

                            <div>
                                <label class="block text-sm font-medium text-foreground">Decision *</label>
                                <select id="reviewStatus" required class="form-select mt-1">
                                    <option value="">Select decision</option>
                                    <option value="Approved">Approve Advertisement</option>
                                    <option value="Rejected">Reject Advertisement</option>
                                    <option value="Active">Set as Active</option>
                                </select>
                            </div>

                            <div id="rejectionReasonGroup" style="display: none;">
                                <label class="block text-sm font-medium text-foreground">Rejection Reason *</label>
                                <textarea id="rejectionReason" rows="3" placeholder="Provide reason for rejection..." class="form-textarea mt-1"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('reviewModal')" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="deleteModal" class="modal-overlay">
                <div class="modal-content" style="max-width: 400px;">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-foreground">Confirm Delete</h3>
                        <p class="text-sm text-muted-foreground mb-4">Are you sure you want to delete this advertisement? This action cannot be undone.</p>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeModal('deleteModal')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="button" onclick="confirmDelete()" class="btn btn-destructive">
                                Delete Ad
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                lucide.createIcons();

                // Mock data embedded from PHP
                const mockAdsData = <?= json_encode($adsData) ?>;
                
                let currentPage = 1;
                let deleteAdId = null;
                let allAds = [];

                // Initialize ads from mock data
                function initializeAds() {
                    allAds = mockAdsData.map(ad => ({
                        id: ad.id,
                        company: ad.company,
                        title: ad.title,
                        type: ad.type,
                        budget: ad.budget,
                        duration: ad.duration,
                        status: ad.status,
                        submittedDate: ad.submittedDate || ad.created_at || new Date().toISOString(),
                        description: ad.description || 'No description available',
                        rejection_reason: ad.rejection_reason || null,
                        impressions: ad.impressions || 0
                    }));
                    
                    console.log('Initialized ads:', allAds);
                }

                document.addEventListener('DOMContentLoaded', () => {
                    initializeAds();
                    loadAds();
                    loadStats();
                });

                // Show/hide rejection reason based on status
                document.getElementById('reviewStatus').addEventListener('change', (e) => {
                    const rejectionGroup = document.getElementById('rejectionReasonGroup');
                    const rejectionReason = document.getElementById('rejectionReason');

                    if (e.target.value === 'Rejected') {
                        rejectionGroup.style.display = 'block';
                        rejectionReason.required = true;
                    } else {
                        rejectionGroup.style.display = 'none';
                        rejectionReason.required = false;
                    }
                });

                function loadStats() {
                    const total = allAds.length;
                    const pending = allAds.filter(a => a.status === 'Pending').length;
                    const approved = allAds.filter(a => a.status === 'Approved').length;
                    const rejected = allAds.filter(a => a.status === 'Rejected').length;

                    document.getElementById('statsContainer').innerHTML = `
                ${renderStatCard('Total Ads', total, '', 'monitor', 'blue')}
                ${renderStatCard('Pending Review', pending, '', 'clock', 'yellow')}
                ${renderStatCard('Approved', approved, '', 'check-circle', 'green')}
                ${renderStatCard('Rejected', rejected, '', 'x-circle', 'red')}
            `;
                    lucide.createIcons();
                }

                function renderStatCard(title, value, subtitle, icon, color) {
                    return `
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">${title}</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${value}</p>
                        ${subtitle ? `<p class="text-xs text-muted-foreground mt-1">${subtitle}</p>` : ''}
                    </div>
                    <div class="text-${color}-600">
                        <i data-lucide="${icon}" class="h-8 w-8"></i>
                    </div>
                </div>
            </div>
        `;
                }

                function loadAds(page = 1) {
                    currentPage = page;
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const status = document.getElementById('statusFilter').value;
                    const type = document.getElementById('typeFilter').value;

                    // Filter ads
                    let filtered = allAds.filter(ad => {
                        if (search && !ad.title.toLowerCase().includes(search) && 
                            !ad.company.toLowerCase().includes(search)) return false;
                        if (status && ad.status !== status) return false;
                        if (type && ad.type !== type) return false;
                        return true;
                    });

                    // Pagination
                    const limit = 20;
                    const total = filtered.length;
                    const pages = Math.ceil(total / limit) || 1;
                    const offset = (page - 1) * limit;
                    const data = filtered.slice(offset, offset + limit);

                    renderAds(data);
                    renderPagination({ page, pages, total });
                }

                function renderAds(ads) {
                    const tbody = document.getElementById('adsTableBody');

                    if (ads.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No ads found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = ads.map(ad => {
                        const statusColors = {
                            'Pending': 'outline',
                            'Approved': 'default',
                            'Rejected': 'destructive',
                            'Active': 'default',
                            'Expired': 'secondary'
                        };

                        const typeClass = {
                            'Banner': 'ads-type-banner',
                            'Sponsored': 'ads-type-sponsored',
                            'Featured': 'ads-type-featured'
                        }[ad.type] || '';

                        return `
                <tr>
                    <td class="text-card-foreground font-medium" style="text-align: center;">#${ad.id}</td>
                    <td class="text-muted-foreground">${escapeHtml(ad.company)}</td>
                    <td class="text-muted-foreground ads-title">${escapeHtml(ad.title)}</td>
                    <td style="text-align: center;">
                        <span class="ads-type-badge ${typeClass}">${ad.type}</span>
                    </td>
                    <td class="text-muted-foreground" style="text-align: right;">LKR ${parseInt(ad.budget || 0).toLocaleString()}</td>
                    <td style="text-align: center;">
                        <span class="badge badge-${statusColors[ad.status]}">${ad.status}</span>
                    </td>
                    <td style="text-align: center;">
                        <div class="flex space-x-2 justify-center">
                            <button onclick="reviewAd(${ad.id})" class="btn btn-sm btn-primary" title="Review">
                                <i data-lucide="eye" class="h-3 w-3"></i>
                            </button>
                            <button onclick="deleteAd(${ad.id})" class="btn btn-sm btn-destructive" title="Delete">
                                <i data-lucide="trash-2" class="h-3 w-3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
                    }).join('');

                    lucide.createIcons();
                }

                function renderPagination(pagination) {
                    const container = document.getElementById('paginationContainer');

                    if (pagination.pages <= 1) {
                        container.innerHTML = '';
                        return;
                    }

                    let html = '<div class="flex space-x-2">';

                    if (pagination.page > 1) {
                        html += `<button onclick="loadAds(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
                    }

                    for (let i = 1; i <= pagination.pages; i++) {
                        if (i === pagination.page) {
                            html += `<button class="btn btn-primary">${i}</button>`;
                        } else if (i === 1 || i === pagination.pages || Math.abs(i - pagination.page) <= 2) {
                            html += `<button onclick="loadAds(${i})" class="btn btn-secondary">${i}</button>`;
                        } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                            html += `<span class="px-2">...</span>`;
                        }
                    }

                    if (pagination.page < pagination.pages) {
                        html += `<button onclick="loadAds(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
                    }

                    html += '</div>';
                    container.innerHTML = html;
                }

                function searchAds() {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        loadAds(1);
                    }, 500);
                }

                function reviewAd(adId) {
                    const ad = allAds.find(a => a.id === adId);
                    
                    if (!ad) {
                        showMessage('Ad not found', 'error');
                        return;
                    }

                    const submittedDate = new Date(ad.submittedDate).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });

                    document.getElementById('adDetailsContent').innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-3">Advertisement Details</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Ad ID:</strong> #${ad.id}</p>
                            <p><strong>Company:</strong> ${escapeHtml(ad.company)}</p>
                            <p><strong>Title:</strong> ${escapeHtml(ad.title)}</p>
                            <p><strong>Type:</strong> <span class="badge badge-default">${ad.type}</span></p>
                            <p><strong>Budget:</strong> LKR ${parseInt(ad.budget || 0).toLocaleString()}</p>
                            <p><strong>Duration:</strong> ${ad.duration || 'Not specified'}</p>
                            <p><strong>Status:</strong> <span class="badge badge-${ad.status === 'Pending' ? 'outline' : ad.status === 'Rejected' ? 'destructive' : 'default'}">${ad.status}</span></p>
                            <p><strong>Submitted Date:</strong> ${submittedDate}</p>
                            <p><strong>Impressions:</strong> ${parseInt(ad.impressions || 0).toLocaleString()}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium mb-3">Description</h4>
                        <div class="bg-muted/50 p-4 rounded-lg text-sm" style="max-height: 150px; overflow-y: auto;">
                            ${ad.description ? escapeHtml(ad.description) : 'No description provided'}
                        </div>
                        ${ad.rejection_reason ? `
                            <div class="mt-4">
                                <h4 class="font-medium mb-2 text-red-600">Previous Rejection Reason</h4>
                                <div class="bg-red-50 p-4 rounded-lg text-sm">
                                    ${escapeHtml(ad.rejection_reason)}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

                    document.getElementById('reviewAdId').value = ad.id;
                    document.getElementById('reviewStatus').value = '';
                    document.getElementById('rejectionReason').value = '';
                    document.getElementById('rejectionReasonGroup').style.display = 'none';

                    openModal('reviewModal');
                }

                document.getElementById('reviewForm').addEventListener('submit', (e) => {
                    e.preventDefault();

                    const adId = parseInt(document.getElementById('reviewAdId').value);
                    const status = document.getElementById('reviewStatus').value;
                    const rejectionReason = document.getElementById('rejectionReason').value;

                    if (!status) {
                        showMessage('Please select a decision', 'error');
                        return;
                    }

                    if (status === 'Rejected' && !rejectionReason.trim()) {
                        showMessage('Please provide a rejection reason', 'error');
                        return;
                    }

                    // Update local data
                    const adIndex = allAds.findIndex(a => a.id === adId);
                    if (adIndex !== -1) {
                        allAds[adIndex].status = status;
                        if (status === 'Rejected' && rejectionReason) {
                            allAds[adIndex].rejection_reason = rejectionReason;
                        } else if (status !== 'Rejected') {
                            allAds[adIndex].rejection_reason = null;
                        }
                        
                        console.log('Updated ad:', allAds[adIndex]);
                    }

                    showMessage(`Advertisement ${status.toLowerCase()} successfully`, 'success');
                    closeModal('reviewModal');
                    loadAds(currentPage);
                    loadStats();
                });

                function deleteAd(adId) {
                    deleteAdId = adId;
                    openModal('deleteModal');
                }

                function confirmDelete() {
                    if (!deleteAdId) return;

                    // Remove from local data
                    const adIndex = allAds.findIndex(a => a.id === deleteAdId);
                    if (adIndex !== -1) {
                        const deletedAd = allAds.splice(adIndex, 1)[0];
                        console.log('Deleted ad:', deletedAd);
                    }

                    showMessage('Advertisement deleted successfully', 'success');
                    closeModal('deleteModal');
                    loadAds(currentPage);
                    loadStats();
                    deleteAdId = null;
                }

                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                }

                function showMessage(message, type = 'success') {
                    const container = document.getElementById('messageContainer');
                    container.textContent = message;
                    container.className = type === 'success' ?
                        'bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-4' :
                        'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-4';
                    container.style.display = 'block';

                    setTimeout(() => {
                        container.style.display = 'none';
                    }, 5000);
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            </script>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="../../assets/javascript/admin-moderator/common.js"></script>
</body>

</html>
