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

$basePath = '';
$currentPath = 'users';
$message = '';

// Get mock data
$usersData = $mockUsers;

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
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/users.css">

            <?php renderPageHeader($basePath, 'User Management', 'Manage service providers, companies, and moderators'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div class="users-header">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">User Management</h2>
                            <p class="text-muted-foreground">Manage service providers, companies, and moderators</p>
                        </div>
                        <button onclick="openAddUserModal()" class="users-add-btn">
                            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                            Add User
                        </button>
                    </div>

                    <div id="messageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded"></div>

                    <div class="users-table-container">
                        <div class="users-table-header">
                            <h3 class="text-lg font-medium text-card-foreground">All Users</h3>
                            <p class="text-sm text-muted-foreground">View and manage all registered users in the system</p>

                            <div class="users-search-container">
                                <div class="users-search-input">
                                    <i data-lucide="search" class="users-search-icon"></i>
                                    <input
                                        type="text"
                                        id="searchInput"
                                        placeholder="Search users..."
                                        onkeyup="searchUsers()">
                                </div>
                                <select id="roleFilter" onchange="loadUsers()" class="form-select">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="user">User</option>
                                    <option value="company">Company</option>
                                    <option value="repairer">Repairer</option>
                                </select>
                                <select id="statusFilter" onchange="loadUsers()" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Join Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Loading users...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer" class="flex justify-center mt-4"></div>
                    </div>
                </div>
            </main>

            <div id="userModal" class="users-modal">
                <div class="users-modal-content">
                    <div class="users-modal-header">
                        <h3 id="modalTitle" class="text-lg font-medium text-card-foreground">Add New User</h3>
                        <!-- Modal-specific message container -->
                        <div id="modalMessageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-3"></div>
                        <form id="userForm" class="users-modal-form">
                            <input type="hidden" id="userId" name="user_id">

                            <div class="users-form-group">
                                <label class="users-form-label">Name *</label>
                                <input type="text" id="userName" name="name" required>
                            </div>

                            <div class="users-form-group">
                                <label class="users-form-label">Email *</label>
                                <input type="email" id="userEmail" name="email" required>
                            </div>

                            <div class="users-form-group" id="passwordGroup">
                                <label class="users-form-label">Password *</label>
                                <input type="password" id="userPassword" name="password" minlength="6">
                                <small class="text-muted-foreground">Minimum 6 characters</small>
                            </div>

                            <div class="users-form-group">
                                <label class="users-form-label">Phone</label>
                                <input type="tel" id="userPhone" name="phone">
                            </div>

                            <div class="users-form-group">
                                <label class="users-form-label">Company</label>
                                <input type="text" id="userCompany" name="company">
                            </div>

                            <div class="users-form-group">
                                <label class="users-form-label">Role *</label>
                                <select id="userRole" name="role" required>
                                    <option value="user">User</option>
                                    <option value="company">Company</option>
                                    <option value="repairer">Repairer</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>

                            <div class="users-form-group">
                                <label class="users-form-label">Status *</label>
                                <select id="userStatus" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="banned">Banned</option>
                                </select>
                            </div>

                            <div class="users-form-actions">
                                <button type="button" onclick="closeModal('userModal')" class="users-cancel-btn">
                                    Cancel
                                </button>
                                <button type="button" id="deleteAccountBtn" onclick="confirmDeleteFromModal()" class="users-delete-btn" style="display: none;">
                                    <i data-lucide="trash-2" class="mr-2 h-4 w-4"></i>
                                    Delete Account
                                </button>
                                <button type="submit" class="users-save-btn" id="saveButton">
                                    <span id="saveButtonText">Save User</span>
                                    <span id="saveButtonLoader" style="display: none;">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="deleteModal" class="users-modal">
                <div class="users-modal-content" style="max-width: 400px;">
                    <div class="users-modal-header">
                        <h3 class="text-lg font-medium text-card-foreground">Confirm Delete</h3>
                        <p class="text-sm text-muted-foreground mb-4">Are you sure you want to delete this user? This action cannot be undone.</p>
                        <div class="users-form-actions">
                            <button type="button" onclick="closeModal('deleteModal')" class="users-cancel-btn">
                                Cancel
                            </button>
                            <button type="button" onclick="confirmDelete()" class="users-save-btn" style="background-color: #dc2626;">
                                Delete User
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                lucide.createIcons();

                // Mock data embedded from PHP
                const mockUsersData = <?= json_encode($usersData) ?>;
                
                let currentPage = 1;
                let deleteUserId = null;
                let allUsers = [];

                // Initialize users from mock data
                function initializeUsers() {
                    allUsers = mockUsersData.map(user => ({
                        id: user.id,
                        name: user.name,
                        email: user.email,
                        phone: user.phone || null,
                        role: user.role,
                        status: user.status,
                        created_at: user.joinDate || user.created_at || new Date().toISOString()
                    }));
                }

                // Load users on page load
                document.addEventListener('DOMContentLoaded', () => {
                    initializeUsers();
                    loadUsers();
                });

                function loadUsers(page = 1) {
                    currentPage = page;
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const role = document.getElementById('roleFilter').value;
                    const status = document.getElementById('statusFilter').value;

                    // Filter users
                    let filtered = allUsers.filter(user => {
                        if (search && !user.name.toLowerCase().includes(search) && 
                            !user.email.toLowerCase().includes(search)) return false;
                        if (role && user.role !== role) return false;
                        if (status && user.status !== status) return false;
                        return true;
                    });

                    // Pagination
                    const limit = 20;
                    const total = filtered.length;
                    const pages = Math.ceil(total / limit) || 1;
                    const offset = (page - 1) * limit;
                    const data = filtered.slice(offset, offset + limit);

                    renderUsers(data);
                    renderPagination({ page, pages, total });
                }

                function renderUsers(users) {
                    const tbody = document.getElementById('usersTableBody');

                    if (users.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No users found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = users.map(user => {
                        const roleClass = {
                            'admin': 'users-role-moderator',
                            'moderator': 'users-role-moderator',
                            'company': 'users-role-company',
                            'repairer': 'users-role-provider',
                            'user': 'users-role-provider'
                        } [user.role] || 'users-role-provider';

                        const statusVariant = {
                            'active': 'default',
                            'pending': 'secondary',
                            'suspended': 'destructive',
                            'banned': 'destructive'
                        } [user.status] || 'outline';

                        return `
                <tr>
                    <td class="text-card-foreground font-medium">${escapeHtml(user.name)}</td>
                    <td class="text-muted-foreground">${escapeHtml(user.email)}</td>
                    <td class="text-muted-foreground">${escapeHtml(user.phone || '-')}</td>
                    <td>
                        <span class="users-role-badge ${roleClass}">${capitalizeFirst(user.role)}</span>
                    </td>
                    <td>
                        <span class="badge badge-${statusVariant}">${capitalizeFirst(user.status)}</span>
                    </td>
                    <td class="text-muted-foreground">${formatDate(user.created_at)}</td>
                    <td>
                        <div class="flex space-x-2">
                            <button onclick="editUser(${user.id})" class="users-edit-btn" title="Edit">
                                <i data-lucide="edit" class="h-4 w-4"></i>
                            </button>
                            <button onclick="deleteUser(${user.id})" class="users-edit-btn" style="color: #dc2626;" title="Delete">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
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

                    // Previous button
                    if (pagination.page > 1) {
                        html += `<button onclick="loadUsers(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
                    }

                    // Page numbers
                    for (let i = 1; i <= pagination.pages; i++) {
                        if (i === pagination.page) {
                            html += `<button class="btn btn-primary">${i}</button>`;
                        } else if (i === 1 || i === pagination.pages || Math.abs(i - pagination.page) <= 2) {
                            html += `<button onclick="loadUsers(${i})" class="btn btn-secondary">${i}</button>`;
                        } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                            html += `<span class="px-2">...</span>`;
                        }
                    }

                    // Next button
                    if (pagination.page < pagination.pages) {
                        html += `<button onclick="loadUsers(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
                    }

                    html += '</div>';
                    container.innerHTML = html;
                }

                function searchUsers() {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        loadUsers(1);
                    }, 500);
                }

                function openAddUserModal() {
                    document.getElementById('modalTitle').textContent = 'Add New User';
                    document.getElementById('userForm').reset();
                    document.getElementById('userId').value = '';
                    document.getElementById('passwordGroup').style.display = 'block';
                    document.getElementById('userPassword').required = true;
                    document.getElementById('deleteAccountBtn').style.display = 'none';
                    openModal('userModal');
                }

                function editUser(userId) {
                    const user = allUsers.find(u => u.id === userId);
                    
                    if (!user) {
                        showMessage('User not found', 'error');
                        return;
                    }

                    document.getElementById('modalTitle').textContent = 'Edit User';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('userName').value = user.name;
                    document.getElementById('userEmail').value = user.email;
                    document.getElementById('userPhone').value = user.phone || '';
                    document.getElementById('userCompany').value = user.company || '';
                    document.getElementById('userRole').value = user.role;
                    document.getElementById('userStatus').value = user.status;
                    document.getElementById('passwordGroup').style.display = 'none';
                    document.getElementById('userPassword').required = false;
                    document.getElementById('deleteAccountBtn').style.display = 'inline-flex';
                    openModal('userModal');
                }

                function confirmDeleteFromModal() {
                    const userId = document.getElementById('userId').value;
                    if (!userId) {
                        showMessage('No user selected', 'error');
                        return;
                    }
                    
                    // Close user modal and open delete confirmation modal
                    closeModal('userModal');
                    window.deleteUserId = userId;
                    openModal('deleteModal');
                }

                document.getElementById('userForm').addEventListener('submit', (e) => {
                    e.preventDefault();

                    const userId = document.getElementById('userId').value;
                    const formData = new FormData(e.target);
                    const data = Object.fromEntries(formData.entries());

                    const saveButton = document.getElementById('saveButton');
                    const saveButtonText = document.getElementById('saveButtonText');
                    const saveButtonLoader = document.getElementById('saveButtonLoader');

                    saveButton.disabled = true;
                    saveButtonText.style.display = 'none';
                    saveButtonLoader.style.display = 'inline';

                    // Update or add user in local data
                    if (userId) {
                        // Update existing user
                        const userIndex = allUsers.findIndex(u => u.id === parseInt(userId));
                        if (userIndex !== -1) {
                            allUsers[userIndex] = {
                                ...allUsers[userIndex],
                                name: data.name,
                                email: data.email,
                                phone: data.phone,
                                company: data.company,
                                role: data.role,
                                status: data.status
                            };
                        }
                    } else {
                        // Add new user
                        const newUser = {
                            id: Math.max(...allUsers.map(u => u.id)) + 1,
                            name: data.name,
                            email: data.email,
                            phone: data.phone,
                            company: data.company,
                            role: data.role,
                            status: data.status,
                            created_at: new Date().toISOString()
                        };
                        allUsers.push(newUser);
                    }

                    // Simulate async operation
                    setTimeout(() => {
                        saveButton.disabled = false;
                        saveButtonText.style.display = 'inline';
                        saveButtonLoader.style.display = 'none';
                        closeModal('userModal');
                        loadUsers(currentPage);
                        showMessage(userId ? 'User updated successfully' : 'User added successfully', 'success');
                    }, 300);
                });

                function deleteUser(userId) {
                    deleteUserId = userId;
                    openModal('deleteModal');
                }

                function confirmDelete() {
                    if (!deleteUserId) return;

                    // Remove from local data
                    const userIndex = allUsers.findIndex(u => u.id === deleteUserId);
                    if (userIndex !== -1) {
                        allUsers.splice(userIndex, 1);
                    }

                    showMessage('User deleted successfully', 'success');
                    closeModal('deleteModal');
                    loadUsers(currentPage);
                    deleteUserId = null;
                }

                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                }

                function showMessage(message, type = 'success') {
                    // Check if user modal is open and use modal message container if so
                    const userModal = document.getElementById('userModal');
                    const modalMessageContainer = document.getElementById('modalMessageContainer');
                    const mainMessageContainer = document.getElementById('messageContainer');
                    let container;

                    if (userModal && userModal.classList.contains('show')) {
                        container = modalMessageContainer;
                    } else {
                        container = mainMessageContainer;
                    }

                    container.textContent = message;
                    container.className = type === 'success' ?
                        'bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-3' :
                        'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-3';
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

                function formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
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

