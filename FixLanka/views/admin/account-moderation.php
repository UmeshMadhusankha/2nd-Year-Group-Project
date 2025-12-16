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

$message = '';
$basePath = '';
$currentPath = 'account-moderation';

// Get mock users data
$accountsData = $mockUsers;

$pageTitle = 'Account Moderation';
$pageDescription = 'Manage banned accounts, appeals, and reactivation requests';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/account-moderation.css?v=<?php echo time(); ?>">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Account Moderation', 'Manage banned accounts, appeals, and reactivation requests'); ?>

            <main style="margin-top: 2rem;" class="dashboard-content">
                <div class="space-y-6">
                    <!-- Ban Account Button (Top Right) -->
                    <div class="button-container-right">
                        <button onclick="openBanModal()" class="btn-destructive">
                            <i data-lucide="shield-ban"></i>
                            Ban Account
                        </button>
                    </div>

                    <!-- Stats Cards Row -->
                    <div class="dashboard-stats grid grid-cols-4 gap-4" id="statsContainer">
                        <!-- Stats loaded via JavaScript -->
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="bg-card rounded-lg border">
                        <div class="p-6">
                            <div class="filter-container">
                                <div class="search-container">
                                    <div class="relative">
                                        <i data-lucide="search" class="search-icon"></i>
                                        <input type="text" id="searchInput" placeholder="Search by name, email..." class="form-input pl-10" onkeyup="searchAccounts()">
                                    </div>
                                </div>
                                <select id="statusFilter" class="form-select" onchange="loadAccounts()">
                                    <option value="">All Status</option>
                                    <option value="banned">Banned</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="active">Active</option>
                                </select>
                                <select id="roleFilter" class="form-select" onchange="loadAccounts()">
                                    <option value="">All Roles</option>
                                    <option value="user">User</option>
                                    <option value="company">Company</option>
                                    <option value="repairer">Repairer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Moderation Cases Table Section -->
                    <div class="bg-card rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground">Moderation Cases</h3>
                            <p class="text-sm text-muted-foreground">Review and manage banned/suspended accounts</p>
                        </div>

                        <div class="table-container">
                            <table class="reports-table" id="moderationTable">
                                <thead>
                                    <tr>
                                        <th>User Details</th>
                                        <th>Status</th>
                                        <th>Role</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="accountsTableBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Loading accounts...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer" class="pagination-container"></div>
                    </div>
                </div>
            </main>

            <!-- Ban Modal -->
            <div id="banModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-header">
                        <h3>Ban/Suspend Account</h3>
                        <button type="button" onclick="closeModal('banModal')" class="modal-close">
                            <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="banForm" class="space-y-4">
                            <div class="form-group">
                                <label>User Email *</label>
                                <input type="email" id="banEmail" required placeholder="Enter user email" class="form-input">
                            </div>
                            <div class="form-group">
                                <label>Action *</label>
                                <select id="banStatus" class="form-select">
                                    <option value="suspended">Temporary Suspension</option>
                                    <option value="banned">Permanent Ban</option>
                                </select>
                            </div>
                            <div class="form-actions">
                                <button type="button" onclick="closeModal('banModal')" class="btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn-primary" style="background: #ef4444;">
                                    Apply Action
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Restore Modal -->
            <div id="restoreModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-header">
                        <h3>Restore Account</h3>
                        <button type="button" onclick="closeModal('restoreModal')" class="modal-close">
                            <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-muted-foreground mb-4">Are you sure you want to restore this account to active status?</p>
                        <div class="form-actions">
                            <button type="button" onclick="closeModal('restoreModal')" class="btn-secondary">
                                Cancel
                            </button>
                            <button type="button" onclick="confirmRestore()" class="btn-primary">
                                Restore Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Account Info Modal - NEW -->
            <div id="activeInfoModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-header">
                        <h3>Account Information</h3>
                        <button type="button" onclick="closeModal('activeInfoModal')" class="modal-close">
                            <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="account-info-grid" id="activeAccountInfo">
                            <!-- Account info loaded here -->
                        </div>
                        <div class="form-actions" style="margin-top: 1.5rem;">
                            <button type="button" onclick="closeModal('activeInfoModal')" class="btn-primary">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                window.allAccounts = <?= json_encode($accountsData) ?>;
                
                lucide.createIcons();

                let currentPage = 1;
                let restoreUserId = null;

                document.addEventListener('DOMContentLoaded', () => {
                    loadAccounts();
                    loadStats();
                });

                function loadStats() {
                    const users = window.allAccounts;
                    const totalCases = users.filter(u => ['banned', 'suspended'].includes(u.status)).length;
                    const banned = users.filter(u => u.status === 'banned').length;
                    const suspended = users.filter(u => u.status === 'suspended').length;
                    const active = users.filter(u => u.status === 'active').length;

                    document.getElementById('statsContainer').innerHTML = `
                        <div class="stat-card pending">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3>Total Cases</h3>
                                    <div class="stat-value">${totalCases}</div>
                                    <div class="stat-desc">Active moderation</div>
                                </div>
                                <div class="stat-icon">
                                    <i data-lucide="shield-alert" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card escalated">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3>Banned</h3>
                                    <div class="stat-value">${banned}</div>
                                    <div class="stat-desc">Permanently banned</div>
                                </div>
                                <div class="stat-icon">
                                    <i data-lucide="user-x" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card investigating">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3>Suspended</h3>
                                    <div class="stat-value">${suspended}</div>
                                    <div class="stat-desc">Temporarily suspended</div>
                                </div>
                                <div class="stat-icon">
                                    <i data-lucide="user-minus" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="stat-card resolved">
                            <div class="stat-card-content">
                                <div class="stat-info">
                                    <h3>Active</h3>
                                    <div class="stat-value">${active}</div>
                                    <div class="stat-desc">Currently active</div>
                                </div>
                                <div class="stat-icon">
                                    <i data-lucide="user-check" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                        </div>
                    `;
                    lucide.createIcons();
                }

                function loadAccounts(page = 1) {
                    currentPage = page;
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const status = document.getElementById('statusFilter').value;
                    const role = document.getElementById('roleFilter').value;

                    let filteredAccounts = window.allAccounts.filter(account => {
                        const matchesSearch = !search || 
                            account.name.toLowerCase().includes(search) ||
                            account.email.toLowerCase().includes(search) ||
                            (account.phone && account.phone.includes(search));
                        const matchesStatus = !status || account.status === status;
                        const matchesRole = !role || account.role === role;
                        
                        return matchesSearch && matchesStatus && matchesRole;
                    });

                    const limit = 20;
                    const totalPages = Math.ceil(filteredAccounts.length / limit);
                    const startIndex = (page - 1) * limit;
                    const endIndex = startIndex + limit;
                    const paginatedAccounts = filteredAccounts.slice(startIndex, endIndex);

                    renderAccounts(paginatedAccounts);
                    renderPagination({ page, pages: totalPages, total: filteredAccounts.length, limit });
                }

                function renderAccounts(accounts) {
                    const tbody = document.getElementById('accountsTableBody');

                    if (accounts.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No accounts found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = accounts.map(account => {
                        const statusBadge = account.status === 'banned' ? 'status-escalated' : 
                                          account.status === 'suspended' ? 'status-investigating' : 
                                          'status-resolved';

                        return `
                            <tr>
                                <td>
                                    <div class="reporter-info">
                                        <div class="name">${escapeHtml(account.name)}</div>
                                        <div class="user-id">ID: ${account.id}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge ${statusBadge}">${capitalizeFirst(account.status)}</span>
                                </td>
                                <td>
                                    <span class="badge priority-medium">${capitalizeFirst(account.role)}</span>
                                </td>
                                <td class="text-sm text-muted-foreground">${escapeHtml(account.email)}</td>
                                <td class="text-sm text-muted-foreground">${escapeHtml(account.phone || '-')}</td>
                                <td>
                                    ${['banned', 'suspended'].includes(account.status) ? `
                                        <button onclick="restoreAccount(${account.id})" class="btn-icon" title="Restore Account" style="background: #d1fae5; color: #065f46; padding: 0.5rem 1rem; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.375rem;">
                                            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
                                            Restore
                                        </button>
                                    ` : `
                                        <button onclick="showActiveInfo(${account.id})" class="active-status-link" title="View account details">
                                            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                            Active
                                        </button>
                                    `}
                                </td>
                            </tr>
                        `;
                    }).join('');

                    lucide.createIcons();
                }

                // NEW FUNCTION: Show Active Account Info
                function showActiveInfo(userId) {
                    const user = window.allAccounts.find(u => u.id === userId);
                    
                    if (!user) {
                        alert('User not found');
                        return;
                    }

                    const infoContainer = document.getElementById('activeAccountInfo');
                    infoContainer.innerHTML = `
                        <div class="info-row">
                            <div class="info-label">Account Status</div>
                            <div class="info-value success">
                                <i data-lucide="check-circle"></i>
                                Active & In Good Standing
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">${escapeHtml(user.name)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">${escapeHtml(user.email)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Phone Number</div>
                            <div class="info-value">${escapeHtml(user.phone || 'Not provided')}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Account Type</div>
                            <div class="info-value">${capitalizeFirst(user.role)}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Account ID</div>
                            <div class="info-value">#${user.id}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Last Activity</div>
                            <div class="info-value">Today at 10:45 AM</div>
                        </div>
                    `;

                    const modal = document.getElementById('activeInfoModal');
                    modal.classList.add('show');
                    modal.style.display = 'flex';
                    lucide.createIcons();
                }

                function renderPagination(pagination) {
                    const container = document.getElementById('paginationContainer');

                    if (pagination.pages <= 1) {
                        container.innerHTML = '';
                        return;
                    }

                    let html = '<div class="pagination-info">Page ' + pagination.page + ' of ' + pagination.pages + '</div>';
                    html += '<div class="pagination-buttons">';

                    if (pagination.page > 1) {
                        html += '<button onclick="loadAccounts(' + (pagination.page - 1) + ')">← Previous</button>';
                    }

                    if (pagination.page < pagination.pages) {
                        html += '<button onclick="loadAccounts(' + (pagination.page + 1) + ')">Next →</button>';
                    }

                    html += '</div>';
                    container.innerHTML = html;
                }

                function searchAccounts() {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => loadAccounts(1), 500);
                }

                function openBanModal() {
                    document.getElementById('banForm').reset();
                    const modal = document.getElementById('banModal');
                    modal.classList.add('show');
                    modal.style.display = 'flex';
                    lucide.createIcons();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    const banForm = document.getElementById('banForm');
                    if (banForm) {
                        banForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const email = document.getElementById('banEmail').value;
                            const status = document.getElementById('banStatus').value;

                            const user = window.allAccounts.find(u => u.email.toLowerCase() === email.toLowerCase());

                            if (!user) {
                                alert('User not found with email: ' + email);
                                return;
                            }

                            user.status = status;
                            alert(`Account ${status} successfully!`);
                            closeModal('banModal');
                            loadAccounts(currentPage);
                            loadStats();
                        });
                    }
                });

                function restoreAccount(userId) {
                    restoreUserId = userId;
                    const modal = document.getElementById('restoreModal');
                    modal.classList.add('show');
                    modal.style.display = 'flex';
                    lucide.createIcons();
                }

                function confirmRestore() {
                    if (!restoreUserId) return;

                    const user = window.allAccounts.find(u => u.id === restoreUserId);
                    if (user) {
                        user.status = 'active';
                        alert('Account restored successfully!');
                        closeModal('restoreModal');
                        loadAccounts(currentPage);
                        loadStats();
                        restoreUserId = null;
                    } else {
                        alert('User not found');
                    }
                }

                function closeModal(modalId) {
                    const modal = document.getElementById(modalId);
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                function capitalizeFirst(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1);
                }
            </script>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>