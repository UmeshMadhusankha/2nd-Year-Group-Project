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

// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();

$message = '';
$basePath = '';
$currentPath = 'ads';

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

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/ads.css">

            <?php renderPageHeader($basePath, 'Advertisement Review', 'Review and manage submitted advertisements'); ?>

            <main style="margin-top: 5rem;" class="ads-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-foreground">Advertisement Review</h2>
                        <p class="text-muted-foreground">Review and manage submitted advertisements from companies</p>
                    </div>

                    <div id="messageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded"></div>

                    <div class="grid gap-4 grid-cols-4" id="statsContainer">
                        Stats will be loaded dynamically
                    </div>

                    <div class="bg-card rounded-lg shadow border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground">Advertisement Queue</h3>
                            <p class="text-sm text-muted-foreground">Review submitted advertisements and approve or reject them</p>

                            <div class="mt-4 flex items-center space-x-2">
                                <div class="relative flex-1 max-w-sm">
                                    <i data-lucide="search" class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground"></i>
                                    <input type="text" id="searchInput" placeholder="Search ads..." class="form-input pl-8" onkeyup="searchAds()">
                                </div>
                                <select id="statusFilter" class="form-select" onchange="loadAds()">
                                    <option value="">All Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                    <option value="Active">Active</option>
                                </select>
                                <select id="typeFilter" class="form-select" onchange="loadAds()">
                                    <option value="">All Types</option>
                                    <option value="Banner">Banner</option>
                                    <option value="Sponsored">Sponsored</option>
                                    <option value="Featured">Featured</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Budget</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adsTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Loading ads...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer" class="flex justify-center mt-4 p-4"></div>
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

            <script>
                lucide.createIcons();

                // Mock data embedded from PHP
                const mockAdsData = <?= json_encode($adsData) ?>;
                
                let currentPage = 1;
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
                        submittedDate: ad.submittedDate || ad.created_at || new Date().toISOString()
                    }));
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
                    const pending = allAds.filter(a => a.status === 'Pending').length;
                    const approved = allAds.filter(a => a.status === 'Approved').length;
                    const rejected = allAds.filter(a => a.status === 'Rejected').length;

                    document.getElementById('statsContainer').innerHTML = `
                ${renderCard('Total Ads', allAds.length, 'All submissions', 'monitor', 'text-blue-600')}
                ${renderCard('Pending Review', pending, 'Awaiting approval', 'clock', 'text-yellow-600')}
                ${renderCard('Approved', approved, 'Currently active', 'check-circle', 'text-green-600')}
                ${renderCard('Rejected', rejected, 'Not approved', 'x-circle', 'text-red-600')}
            `;
                    lucide.createIcons();
                }

                function renderCard(title, value, description, icon, colorClass) {
                    return `
            <div class="bg-card rounded-lg border p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted-foreground">${title}</p>
                        <p class="text-2xl font-bold text-foreground mt-2">${value}</p>
                        <p class="text-xs text-muted-foreground mt-1">${description}</p>
                    </div>
                    <div class="${colorClass}">
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
                        const statusVariants = {
                            'Pending': 'outline',
                            'Approved': 'default',
                            'Rejected': 'destructive',
                            'Active': 'default',
                            'Expired': 'secondary'
                        };

                        const typeColors = {
                            'Banner': 'bg-fixlanka-primary/10 text-fixlanka-primary',
                            'Sponsored': 'bg-fixlanka-highlight/20 text-fixlanka-primary',
                            'Featured': 'bg-fixlanka-error/10 text-fixlanka-error'
                        };
                        const typeColor = typeColors[ad.type] || 'bg-muted text-muted-foreground';

                        return `
                <tr>
                    <td class="font-medium text-foreground">#${ad.id}</td>
                    <td class="text-muted-foreground">${escapeHtml(ad.company)}</td>
                    <td class="text-muted-foreground max-w-xs truncate">${escapeHtml(ad.title)}</td>
                    <td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${typeColor}">
                            ${ad.type}
                        </span>
                    </td>
                    <td class="text-muted-foreground">${ad.budget ? '$' + parseFloat(ad.budget).toFixed(2) : '-'}</td>
                    <td>
                        <span class="badge badge-${statusVariants[ad.status]}">${ad.status}</span>
                    </td>
                    <td>
                        <button onclick="reviewAd(${ad.id})" class="btn btn-primary">
                            <i data-lucide="eye" class="mr-1 h-3 w-3"></i>
                            Review
                        </button>
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

                    document.getElementById('adDetailsContent').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2">Advertisement Details</h4>
                        <div class="space-y-2 text-sm">
                            <p><strong>Company:</strong> ${escapeHtml(ad.company)}</p>
                            <p><strong>Title:</strong> ${escapeHtml(ad.title)}</p>
                            <p><strong>Type:</strong> ${ad.type}</p>
                            <p><strong>Budget:</strong> ${ad.budget}</p>
                            <p><strong>Duration:</strong> ${ad.duration || 'Not specified'}</p>
                            <p><strong>Status:</strong> ${ad.status}</p>
                            <p><strong>Views:</strong> ${ad.views || 0}</p>
                            <p><strong>Clicks:</strong> ${ad.clicks || 0}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Description</h4>
                        <div class="bg-muted/50 p-4 rounded-lg text-sm">
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

                    const adId = document.getElementById('reviewAdId').value;
                    const status = document.getElementById('reviewStatus').value;
                    const rejectionReason = document.getElementById('rejectionReason').value;

                    // Update local data
                    const adIndex = allAds.findIndex(a => a.id === parseInt(adId));
                    if (adIndex !== -1) {
                        allAds[adIndex].status = status;
                        if (status === 'Rejected' && rejectionReason) {
                            allAds[adIndex].rejection_reason = rejectionReason;
                        }
                    }

                    showMessage('Ad reviewed successfully', 'success');
                    closeModal('reviewModal');
                    loadAds(currentPage);
                    loadStats();
                });

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
                        'bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded' :
                        'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded';
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>

</body>

</html>

