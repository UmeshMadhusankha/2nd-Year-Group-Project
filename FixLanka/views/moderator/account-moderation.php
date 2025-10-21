<?php
require_once __DIR__ . '/../../../views/other/includes/auth.php';
require_once __DIR__ . '/../common/Header.php';
require_once __DIR__ . '/../common/Common.php';

// Require moderator or admin role
requireAnyRole(['moderator', 'admin'], $basePath);
?>

<link rel="stylesheet" href="<?= $basePath ?>/assets/css/admin & moderator/account-moderation.css">
<?php renderPageHeader($basePath,'Account Moderation', 'Manage banned accounts, appeals, and reactivation requests'); ?>

<main style="margin-top: 5rem;" class="dashboard-content">
    <div class="space-y-6">
        <div class="flex items-center justify-between w-full">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-foreground">Account Moderation</h2>
                <p class="text-muted-foreground">Review banned accounts, handle appeals, and manage account reactivation requests</p>
            </div>
            <button onclick="openBanModal()" class="btn btn-destructive">
                <i data-lucide="shield-x" class="mr-2 h-4 w-4"></i>
                Ban Account
            </button>
        </div>

        <div id="messageContainer" style="display: none;" class="alert alert-info"></div>

        <div class="dashboard-stats grid grid-cols-1 grid-cols-4 gap-4" id="statsContainer">
             Stats will be loaded dynamically 
        </div>

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

        <div class="bg-card rounded-lg border">
            <div class="p-6">
                <h3 class="text-lg font-medium text-foreground">Moderation Cases</h3>
                <p class="text-sm text-muted-foreground">Review and manage banned/suspended accounts</p>
            </div>

            <div class="table-container">
                <table class="table" id="moderationTable">
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

            <div id="paginationContainer" class="flex justify-center mt-4 p-4"></div>
        </div>
    </div>
</main>

<div id="banModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Ban/Suspend Account</h3>
            <form id="banForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-foreground">User Email *</label>
                    <input type="email" id="banEmail" required placeholder="Enter user email" class="form-input mt-1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-foreground">Action *</label>
                    <select id="banStatus" class="form-select mt-1">
                        <option value="suspended">Temporary Suspension</option>
                        <option value="banned">Permanent Ban</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('banModal')" class="btn btn-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-destructive">
                        Apply Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="restoreModal" class="modal-overlay">
    <div class="modal-content">
        <div class="p-6">
            <h3 class="text-lg font-medium text-foreground mb-4">Restore Account</h3>
            <p class="text-sm text-muted-foreground mb-4">Are you sure you want to restore this account to active status?</p>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('restoreModal')" class="btn btn-secondary">
                    Cancel
                </button>
                <button type="button" onclick="confirmRestore()" class="btn btn-primary">
                    Restore Account
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    let currentPage = 1;
    let restoreUserId = null;

    document.addEventListener('DOMContentLoaded', () => {
        loadAccounts();
        loadStats();
    });

    async function loadStats() {
        try {
            const response = await fetch(`<?= $basePath ?>/api/accounts?limit=1000`);
            const data = await response.json();

            if (response.ok && data.success) {
                const users = data.data;
                const totalCases = users.filter(u => ['banned', 'suspended'].includes(u.status)).length;
                const banned = users.filter(u => u.status === 'banned').length;
                const suspended = users.filter(u => u.status === 'suspended').length;
                const active = users.filter(u => u.status === 'active').length;

                document.getElementById('statsContainer').innerHTML = `
                    ${renderStatCard('Total Cases', totalCases, 'Active moderation cases', 'shield-alert', 'text-blue-600')}
                    ${renderStatCard('Banned Accounts', banned, 'Permanently banned', 'user-x', 'text-red-600')}
                    ${renderStatCard('Suspended Accounts', suspended, 'Temporarily suspended', 'user-minus', 'text-yellow-600')}
                    ${renderStatCard('Active Accounts', active, 'Currently active', 'user-check', 'text-green-600')}
                `;
                lucide.createIcons();
            }
        } catch (error) {
            console.error('Load stats error:', error);
        }
    }

    function renderStatCard(title, value, description, icon, colorClass) {
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

    async function loadAccounts(page = 1) {
        currentPage = page;
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const role = document.getElementById('roleFilter').value;

        const params = new URLSearchParams({
            page: page,
            limit: 20,
            ...(search && { search }),
            ...(status && { status }),
            ...(role && { role })
        });

        try {
            const response = await fetch(`<?= $basePath ?>/api/accounts?${params}`);
            const data = await response.json();

            if (response.ok && data.success) {
                renderAccounts(data.data);
                renderPagination(data.pagination);
            } else {
                showMessage(data.error || 'Failed to load accounts', 'error');
            }
        } catch (error) {
            console.error('Load accounts error:', error);
            showMessage('An error occurred while loading accounts', 'error');
        }
    }

    function renderAccounts(accounts) {
        const tbody = document.getElementById('accountsTableBody');
        
        if (accounts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">No accounts found</td></tr>';
            return;
        }

        tbody.innerHTML = accounts.map(account => {
            const statusClass = {
                'banned': 'status-badge status-banned',
                'suspended': 'status-badge status-suspended',
                'active': 'status-badge status-active',
                'pending': 'status-badge appeal-pending'
            }[account.status] || 'status-badge appeal-none';

            return `
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="user-avatar">
                                    <i data-lucide="user" class="h-5 w-5 text-fixlanka-primary"></i>
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-foreground">${escapeHtml(account.name)}</div>
                                <div class="text-xs text-muted-foreground">ID: ${account.id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="${statusClass}">${capitalizeFirst(account.status)}</span>
                    </td>
                    <td>
                        <span class="status-badge appeal-none">${capitalizeFirst(account.role)}</span>
                    </td>
                    <td class="text-sm text-muted-foreground">${escapeHtml(account.email)}</td>
                    <td class="text-sm text-muted-foreground">${escapeHtml(account.phone || '-')}</td>
                    <td>
                        <div class="flex flex-col space-y-1">
                            ${['banned', 'suspended'].includes(account.status) ? `
                                <button onclick="restoreAccount(${account.id})" class="btn btn-sm btn-primary w-full">
                                    <i data-lucide="user-check" class="mr-1 h-3 w-3"></i>
                                    Restore
                                </button>
                            ` : ''}
                            <button onclick="viewAccount(${account.id})" class="btn btn-sm btn-secondary w-full">
                                <i data-lucide="eye" class="mr-1 h-3 w-3"></i>
                                Details
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
            html += `<button onclick="loadAccounts(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
        }
        
        for (let i = 1; i <= pagination.pages; i++) {
            if (i === pagination.page) {
                html += `<button class="btn btn-primary">${i}</button>`;
            } else if (i === 1 || i === pagination.pages || Math.abs(i - pagination.page) <= 2) {
                html += `<button onclick="loadAccounts(${i})" class="btn btn-secondary">${i}</button>`;
            } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                html += `<span class="px-2">...</span>`;
            }
        }
        
        if (pagination.page < pagination.pages) {
            html += `<button onclick="loadAccounts(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
        }
        
        html += '</div>';
        container.innerHTML = html;
    }

    function searchAccounts() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            loadAccounts(1);
        }, 500);
    }

    function openBanModal() {
        document.getElementById('banForm').reset();
        openModal('banModal');
    }

    document.getElementById('banForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('banEmail').value;
        const status = document.getElementById('banStatus').value;

        try {
            // First, find the user by email
            const searchResponse = await fetch(`<?= $basePath ?>/api/accounts?search=${encodeURIComponent(email)}&limit=1`);
            const searchData = await searchResponse.json();

            if (!searchResponse.ok || !searchData.success || searchData.data.length === 0) {
                showMessage('User not found with that email', 'error');
                return;
            }

            const user = searchData.data[0];

            // Update user status
            const response = await fetch(`<?= $basePath ?>/api/accounts/${user.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: status })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showMessage(`Account ${status} successfully`, 'success');
                closeModal('banModal');
                loadAccounts(currentPage);
                loadStats();
            } else {
                showMessage(data.error || 'Failed to update account', 'error');
            }
        } catch (error) {
            console.error('Ban account error:', error);
            showMessage('An error occurred', 'error');
        }
    });

    function restoreAccount(userId) {
        restoreUserId = userId;
        openModal('restoreModal');
    }

    async function confirmRestore() {
        if (!restoreUserId) return;

        try {
            const response = await fetch(`<?= $basePath ?>/api/accounts/${restoreUserId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: 'active' })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                showMessage('Account restored successfully', 'success');
                closeModal('restoreModal');
                loadAccounts(currentPage);
                loadStats();
            } else {
                showMessage(data.error || 'Failed to restore account', 'error');
            }
        } catch (error) {
            console.error('Restore account error:', error);
            showMessage('An error occurred', 'error');
        }
    }

    function viewAccount(userId) {
        window.location.href = `<?= $basePath ?>/admin/users?user=${userId}`;
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
        container.className = type === 'success' 
            ? 'alert alert-info'
            : 'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded';
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

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
