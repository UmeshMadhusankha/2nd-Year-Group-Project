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

$basePath = '';
$currentPath = 'moderators';

// Get page title and description
$pageTitle = 'Moderator Management - FixLanka Admin';
$pageDescription = 'Manage system moderators and their assigned sections';
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
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/moderators.css">

            <?php renderPageHeader($basePath, 'Moderator Management', 'Manage system moderators and their assigned sections'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    <div class="moderators-header">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Moderator Management</h2>
                            <p class="text-muted-foreground">Add, edit, and remove system moderators</p>
                        </div>
                        <button onclick="openAddModeratorModal()" class="moderators-add-btn">
                            <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                            Add Moderator
                        </button>
                    </div>

                    <div id="messageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded"></div>

                    <div class="moderators-table-container">
                        <div class="moderators-table-header">
                            <h3 class="text-lg font-medium text-card-foreground">All Moderators</h3>
                            <p class="text-sm text-muted-foreground">View and manage all registered moderators</p>

                            <div class="moderators-search-container">
                                <div class="moderators-search-input">
                                    <i data-lucide="search" class="moderators-search-icon"></i>
                                    <input
                                        type="text"
                                        id="searchInput"
                                        placeholder="Search moderators..."
                                        onkeyup="searchModerators()">
                                </div>
                                <select id="sectionFilter" onchange="loadModerators()" class="form-select">
                                    <option value="">All Sections</option>
                                    <option value="Advertisement Review">Advertisement Review</option>
                                    <option value="Content Management">Content Management</option>
                                    <option value="User Reports">User Reports</option>
                                    <option value="Financial">Financial</option>
                                    <option value="General">General</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="moderators-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Assigned Section</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="moderatorsTableBody">
                                    <!-- Data will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <div id="paginationContainer" class="flex justify-center mt-4"></div>
                    </div>
                </div>
            </main>

            <!-- Add/Edit Moderator Modal -->
            <div id="moderatorModal" class="moderators-modal">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3 id="modalTitle" class="text-lg font-medium text-card-foreground">Add New Moderator</h3>
                        <div id="modalMessageContainer" style="display: none;" class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-3"></div>
                        <form id="moderatorForm" class="moderators-modal-form">
                            <input type="hidden" id="moderatorId" name="moderator_id">
                            <input type="hidden" id="formAction" name="action" value="add">

                            <div class="moderators-form-group">
                                <label class="moderators-form-label">Username *</label>
                                <input type="text" id="moderatorUsername" name="username" required>
                            </div>

                            <div class="moderators-form-group">
                                <label class="moderators-form-label">Email *</label>
                                <input type="email" id="moderatorEmail" name="email" required>
                            </div>

                            <div class="moderators-form-group" id="passwordGroup">
                                <label class="moderators-form-label">Password *</label>
                                <input type="password" id="moderatorPassword" name="password" minlength="6">
                                <small class="text-muted-foreground">Minimum 6 characters</small>
                            </div>

                            <div class="moderators-form-group">
                                <label class="moderators-form-label">Assigned Section *</label>
                                <select id="moderatorSection" name="assigned_section" required>
                                    <option value="">Select Section</option>
                                    <option value="Advertisement Review">Advertisement Review</option>
                                    <option value="Content Management">Content Management</option>
                                    <option value="User Reports">User Reports</option>
                                    <option value="Financial">Financial</option>
                                    <option value="General">General</option>
                                </select>
                            </div>

                            <div class="moderators-form-actions">
                                <button type="button" onclick="closeModal('moderatorModal')" class="moderators-cancel-btn">
                                    Cancel
                                </button>
                                <button type="submit" class="moderators-save-btn" id="saveButton">
                                    <span id="saveButtonText">Save Moderator</span>
                                    <span id="saveButtonLoader" style="display: none;">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteModal" class="moderators-modal">
                <div class="moderators-modal-content" style="max-width: 400px;">
                    <div class="moderators-modal-header">
                        <h3 class="text-lg font-medium text-card-foreground">Confirm Delete</h3>
                        <p class="text-sm text-muted-foreground mb-4">Are you sure you want to delete this moderator? This action cannot be undone.</p>
                        <form id="deleteForm">
                            <input type="hidden" id="deleteModeratorId" name="moderator_id">
                            <input type="hidden" name="action" value="delete">
                            <div class="moderators-form-actions">
                                <button type="button" onclick="closeModal('deleteModal')" class="moderators-cancel-btn">
                                    Cancel
                                </button>
                                <button type="submit" class="moderators-save-btn" style="background-color: #dc2626;">
                                    Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                lucide.createIcons();

                const API_URL = '/2nd-Year-Group-Project/FixLanka/api/moderators.php';
                
                let currentPage = 1;
                let allModerators = [];

                // Load moderators on page load
                document.addEventListener('DOMContentLoaded', () => {
                    loadModeratorsFromAPI();
                    setupFormSubmission();
                });

                // Fetch moderators from API
                async function loadModeratorsFromAPI() {
                    try {
                        const response = await fetch(`${API_URL}?action=getAll`);
                        const result = await response.json();
                        
                        if (result.success) {
                            allModerators = result.data;
                            loadModerators();
                        } else {
                            showMessage(result.message || 'Failed to load moderators', 'error');
                        }
                    } catch (error) {
                        console.error('Error fetching moderators:', error);
                        showMessage('Failed to load moderators', 'error');
                    }
                }

                // Setup form submission handlers
                function setupFormSubmission() {
                    // Add/Edit form submission
                    document.getElementById('moderatorForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(this);
                        const saveBtn = document.getElementById('saveButton');
                        const saveBtnText = document.getElementById('saveButtonText');
                        const saveBtnLoader = document.getElementById('saveButtonLoader');
                        
                        // Disable button and show loader
                        saveBtn.disabled = true;
                        saveBtnText.style.display = 'none';
                        saveBtnLoader.style.display = 'inline';
                        
                        try {
                            const response = await fetch(API_URL, {
                                method: 'POST',
                                body: formData
                            });
                            
                            const result = await response.json();
                            
                            if (result.success) {
                                showMessage(result.message, 'success');
                                closeModal('moderatorModal');
                                await loadModeratorsFromAPI();
                            } else {
                                showMessage(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Error submitting form:', error);
                            showMessage('Failed to save moderator', 'error');
                        } finally {
                            // Re-enable button and hide loader
                            saveBtn.disabled = false;
                            saveBtnText.style.display = 'inline';
                            saveBtnLoader.style.display = 'none';
                        }
                    });
                    
                    // Delete form submission
                    document.getElementById('deleteForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        const formData = new FormData(this);
                        
                        try {
                            const response = await fetch(API_URL, {
                                method: 'POST',
                                body: formData
                            });
                            
                            const result = await response.json();
                            
                            if (result.success) {
                                showMessage(result.message, 'success');
                                closeModal('deleteModal');
                                await loadModeratorsFromAPI();
                            } else {
                                showMessage(result.message, 'error');
                            }
                        } catch (error) {
                            console.error('Error deleting moderator:', error);
                            showMessage('Failed to delete moderator', 'error');
                        }
                    });
                }

                function loadModerators(page = 1) {
                    currentPage = page;
                    const search = document.getElementById('searchInput').value.toLowerCase();
                    const section = document.getElementById('sectionFilter').value;

                    // Filter moderators
                    let filtered = allModerators.filter(moderator => {
                        if (search && !moderator.username.toLowerCase().includes(search) && 
                            !moderator.email.toLowerCase().includes(search)) return false;
                        if (section && moderator.assigned_section !== section) return false;
                        return true;
                    });

                    // Pagination
                    const limit = 20;
                    const total = filtered.length;
                    const pages = Math.ceil(total / limit) || 1;
                    const offset = (page - 1) * limit;
                    const data = filtered.slice(offset, offset + limit);

                    renderModerators(data);
                    renderPagination({ page, pages, total });
                }

                function renderModerators(moderators) {
                    const tbody = document.getElementById('moderatorsTableBody');

                    if (moderators.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">No moderators found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = moderators.map(moderator => `
                <tr>
                    <td class="text-card-foreground font-medium">${escapeHtml(moderator.username)}</td>
                    <td class="text-muted-foreground">${escapeHtml(moderator.email || '-')}</td>
                    <td>
                        <span class="moderators-section-badge">${escapeHtml(moderator.assigned_section)}</span>
                    </td>
                    <td class="text-muted-foreground">${formatDate(moderator.created_at)}</td>
                    <td>
                        <div class="flex space-x-2">
                            <button onclick="editModerator(${moderator.moderator_id})" class="moderators-edit-btn" title="Edit">
                                <i data-lucide="pencil" class="h-4 w-4"></i>
                            </button>
                            <button onclick="deleteModerator(${moderator.moderator_id})" class="moderators-edit-btn" style="color: #dc2626;" title="Delete">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');

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
                        html += `<button onclick="loadModerators(${pagination.page - 1})" class="btn btn-secondary">Previous</button>`;
                    }

                    for (let i = 1; i <= pagination.pages; i++) {
                        if (i === pagination.page) {
                            html += `<button class="btn btn-primary">${i}</button>`;
                        } else if (i === 1 || i === pagination.pages || Math.abs(i - pagination.page) <= 2) {
                            html += `<button onclick="loadModerators(${i})" class="btn btn-secondary">${i}</button>`;
                        } else if (i === pagination.page - 3 || i === pagination.page + 3) {
                            html += `<span class="px-2">...</span>`;
                        }
                    }

                    if (pagination.page < pagination.pages) {
                        html += `<button onclick="loadModerators(${pagination.page + 1})" class="btn btn-secondary">Next</button>`;
                    }

                    html += '</div>';
                    container.innerHTML = html;
                }

                function searchModerators() {
                    clearTimeout(window.searchTimeout);
                    window.searchTimeout = setTimeout(() => {
                        loadModerators(1);
                    }, 500);
                }

                function openAddModeratorModal() {
                    document.getElementById('modalTitle').textContent = 'Add New Moderator';
                    document.getElementById('moderatorForm').reset();
                    document.getElementById('moderatorId').value = '';
                    document.getElementById('formAction').value = 'add';
                    document.getElementById('moderatorUsername').disabled = false;
                    document.getElementById('passwordGroup').style.display = 'block';
                    document.getElementById('moderatorPassword').required = true;
                    document.querySelector('#passwordGroup small').textContent = 'Minimum 6 characters';
                    openModal('moderatorModal');
                }

                function editModerator(moderatorId) {
                    // Convert to number for comparison (handles string/number mismatch)
                    const moderator = allModerators.find(m => parseInt(m.moderator_id) === parseInt(moderatorId));
                    
                    if (!moderator) {
                        showMessage('Moderator not found', 'error');
                        console.error('Moderator not found. ID:', moderatorId, 'Available moderators:', allModerators);
                        return;
                    }

                    document.getElementById('modalTitle').textContent = 'Edit Moderator';
                    document.getElementById('moderatorId').value = moderator.moderator_id;
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('moderatorUsername').value = moderator.username;
                    document.getElementById('moderatorUsername').disabled = true;
                    document.getElementById('moderatorEmail').value = moderator.email;
                    document.getElementById('moderatorSection').value = moderator.assigned_section;
                    document.getElementById('moderatorPassword').value = '';
                    document.getElementById('moderatorPassword').required = false;
                    document.querySelector('#passwordGroup small').textContent = 'Leave blank to keep current password';
                    openModal('moderatorModal');
                }

                function deleteModerator(moderatorId) {
                    document.getElementById('deleteModeratorId').value = moderatorId;
                    openModal('deleteModal');
                }

                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                    document.body.style.overflow = 'hidden'; // Prevent background scrolling
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                    document.body.style.overflow = ''; // Restore scrolling
                    document.getElementById('modalMessageContainer').style.display = 'none';
                }

                function showMessage(message, type = 'success') {
                    const container = document.getElementById('messageContainer');
                    container.textContent = message;
                    container.className = type === 'success' ?
                        'bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded mb-3' :
                        'bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded mb-3';
                    container.style.display = 'block';

                    // Scroll to message
                    container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                    setTimeout(() => {
                        container.style.display = 'none';
                    }, 5000);
                }

                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
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
