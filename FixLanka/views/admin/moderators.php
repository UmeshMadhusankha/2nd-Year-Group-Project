<?php
/**
 * Moderator Management Page - Pure MVC (NO Handler Files)
 * Forms submit to index.php route
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';

// Include model
require_once __DIR__ . '/../../config/databse.php';
require_once __DIR__ . '/../../models/ModeratorModel.php';

$basePath = '';
$currentPath = 'moderators';
$pageTitle = 'Moderator Management - FixLanka Admin';
$pageDescription = 'Manage system moderators, assign sections, and control access';

// Initialize model
$model = new ModeratorModel($pdo);

// Get data
$allModerators = $model->getAllModerators();
$moderatorStats = $model->getModeratorStats();

// Apply filters
$search = $_GET['search'] ?? '';
$sectionFilter = $_GET['section'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$limit = 20;

// Filter moderators
$filteredModerators = array_filter($allModerators, function($mod) use ($search, $sectionFilter) {
    $matchesSearch = empty($search) || 
                     stripos($mod['username'], $search) !== false || 
                     stripos($mod['email'], $search) !== false;
    $matchesSection = empty($sectionFilter) || $mod['assigned_section'] === $sectionFilter;
    return $matchesSearch && $matchesSection;
});

// Pagination
$totalModerators = count($filteredModerators);
$totalPages = max(1, ceil($totalModerators / $limit));
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $limit;
$moderators = array_slice($filteredModerators, $offset, $limit);

// Get flash messages
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
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
                    
                    <!-- Header with Add Button -->
                    <div class="moderators-header">
                        <div>
                            <h2>System Moderators</h2>
                            <p>Manage moderator accounts and permissions</p>
                        </div>
                        <button onclick="openAddModeratorModal()" class="moderators-add-btn">
                            <i class="fa-solid fa-user-plus"></i>
                            Add Moderator
                        </button>
                    </div>

                    <!-- Success/Error Messages -->
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-circle-check"></i>
                            <?php echo htmlspecialchars($successMessage); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMessage): ?>
                        <div class="alert alert-error">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Moderators Table Card -->
                    <div class="moderators-table-container">
                        <div class="moderators-table-header">
                            <h3>All Moderators</h3>
                            <p>Total: <?php echo count($allModerators); ?> moderators (<?php echo $moderatorStats['active'] ?? 0; ?> active)</p>
                            
                            <!-- Search & Filter -->
                            <form method="GET" action="" class="moderators-search-container">
                                <div class="moderators-search-input">
                                    <i class="fa-solid fa-magnifying-glass moderators-search-icon"></i>
                                    <input 
                                        type="text" 
                                        name="search" 
                                        placeholder="Search by username or email..." 
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <select name="section" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Sections</option>
                                    <option value="Advertisements" <?php echo $sectionFilter === 'Advertisements' ? 'selected' : ''; ?>>Advertisements</option>
                                    <option value="User Reports" <?php echo $sectionFilter === 'User Reports' ? 'selected' : ''; ?>>User Reports</option>
                                    <option value="Content Moderation" <?php echo $sectionFilter === 'Content Moderation' ? 'selected' : ''; ?>>Content Moderation</option>
                                    <option value="Financial Reports" <?php echo $sectionFilter === 'Financial Reports' ? 'selected' : ''; ?>>Financial Reports</option>
                                    <option value="System Monitoring" <?php echo $sectionFilter === 'System Monitoring' ? 'selected' : ''; ?>>System Monitoring</option>
                                </select>
                            </form>
                        </div>

                        <!-- Data Table -->
                        <div class="overflow-x-auto">
                            <table class="moderators-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Assigned Section</th>
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($moderators)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-4" style="color: #9ca3af;">
                                                <i class="fa-solid fa-inbox" style="width: 48px; height: 48px; margin: 0 auto; display: block; margin-bottom: 0.5rem; font-size: 48px;"></i>
                                                No moderators found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($moderators as $mod): ?>
                                            <tr>
                                                <td class="font-medium">#<?php echo $mod['moderator_id']; ?></td>
                                                <td class="text-card-foreground font-medium"><?php echo htmlspecialchars($mod['username']); ?></td>
                                                <td class="text-muted-foreground"><?php echo htmlspecialchars($mod['email']); ?></td>
                                                <td>
                                                    <span class="moderators-section-badge">
                                                        <?php echo htmlspecialchars($mod['assigned_section']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($mod['status'] === 'active'): ?>
                                                        <span class="status-badge status-active">● ACTIVE</span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-inactive">○ INACTIVE</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-muted-foreground">
                                                    <?php echo $mod['last_login'] ? date('M d, Y', strtotime($mod['last_login'])) : 'Never'; ?>
                                                </td>
                                                <td class="text-muted-foreground"><?php echo date('M d, Y', strtotime($mod['created_at'])); ?></td>
                                                <td>
                                                    <div class="flex space-x-2">
                                                        <!-- Edit Button -->
                                                        <button 
                                                            onclick='editModerator(<?php echo json_encode($mod); ?>)' 
                                                            class="moderators-edit-btn"
                                                            title="Edit moderator">
                                                            <i class="fa-solid fa-pen-to-square w-4 h-4"></i>
                                                        </button>
                                                        
                                                        <!-- Toggle Status Button -->
                                                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action" style="display: inline;">
                                                            <input type="hidden" name="action" value="toggle_status">
                                                            <input type="hidden" name="moderator_id" value="<?php echo $mod['moderator_id']; ?>">
                                                            <input type="hidden" name="status" value="<?php echo $mod['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                                            <button 
                                                                type="submit" 
                                                                class="moderators-edit-btn"
                                                                title="<?php echo $mod['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>"
                                                                style="color: <?php echo $mod['status'] === 'active' ? '#f59e0b' : '#10b981'; ?>;"
                                                                onclick="return confirm('Are you sure you want to <?php echo $mod['status'] === 'active' ? 'deactivate' : 'activate'; ?> this moderator?')">
                                                                <i class="fa-solid <?php echo $mod['status'] === 'active' ? 'fa-circle-pause' : 'fa-circle-play'; ?> w-4 h-4"></i>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- Delete Button -->
                                                        <button 
                                                            onclick="deleteModerator(<?php echo $mod['moderator_id']; ?>, '<?php echo htmlspecialchars($mod['username'], ENT_QUOTES); ?>')" 
                                                            class="moderators-edit-btn"
                                                            title="Delete moderator"
                                                            style="color: #dc2626;">
                                                            <i class="fa-solid fa-trash w-4 h-4"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div id="paginationContainer">
                                <div class="flex space-x-2 justify-center">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&section=<?php echo urlencode($sectionFilter); ?>" class="btn btn-secondary">Previous</a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&section=<?php echo urlencode($sectionFilter); ?>" 
                                           class="btn <?php echo $i === $page ? 'btn-primary' : 'btn-secondary'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&section=<?php echo urlencode($sectionFilter); ?>" class="btn btn-secondary">Next</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- ============================================ -->
            <!-- ADD/EDIT MODERATOR MODAL -->
            <!-- ============================================ -->
            <div id="moderatorModal" class="moderators-modal">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3 id="modalTitle">Add New Moderator</h3>
                        
                        <!-- Form submits to MVC route -->
                        <form id="moderatorForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action" class="moderators-modal-form">
                            <input type="hidden" name="action" id="formAction" value="add">
                            <input type="hidden" name="moderator_id" id="moderatorId">

                            <!-- Username -->
                            <div class="moderators-form-group" id="usernameGroup">
                                <label class="moderators-form-label" for="moderatorUsername">
                                    Username <span style="color: red;">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="moderatorUsername" 
                                    name="username" 
                                    placeholder="Enter username"
                                    pattern="[a-zA-Z0-9_]+"
                                    title="Only letters, numbers, and underscores"
                                    maxlength="50"
                                    required>
                                <small>3-50 characters. Only letters, numbers, and underscores.</small>
                            </div>

                            <!-- Email -->
                            <div class="moderators-form-group">
                                <label class="moderators-form-label" for="moderatorEmail">
                                    Email <span style="color: red;">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="moderatorEmail" 
                                    name="email" 
                                    placeholder="moderator@fixlanka.com"
                                    maxlength="100"
                                    required>
                            </div>

                            <!-- Password -->
                            <div class="moderators-form-group" id="passwordGroup">
                                <label class="moderators-form-label" for="moderatorPassword">
                                    Password <span style="color: red;">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="moderatorPassword" 
                                    name="password" 
                                    placeholder="Enter password"
                                    minlength="6"
                                    maxlength="255">
                                <small id="passwordHint">Minimum 6 characters</small>
                            </div>

                            <!-- Assigned Section -->
                            <div class="moderators-form-group">
                                <label class="moderators-form-label" for="moderatorSection">
                                    Assigned Section <span style="color: red;">*</span>
                                </label>
                                <select id="moderatorSection" name="assigned_section" required>
                                    <option value="">-- Select Section --</option>
                                    <option value="Advertisements">Advertisements</option>
                                    <option value="User Reports">User Reports</option>
                                    <option value="Content Moderation">Content Moderation</option>
                                    <option value="Financial Reports">Financial Reports</option>
                                    <option value="System Monitoring">System Monitoring</option>
                                </select>
                            </div>
                            
                            <!-- Modal Actions Inside Form -->
                            <div class="moderators-form-actions">
                                <button type="button" onclick="closeModal('moderatorModal')" class="moderators-cancel-btn">
                                    Cancel
                                </button>
                                <button type="submit" class="moderators-save-btn" id="saveBtn">
                                    Save Moderator
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- DELETE CONFIRMATION MODAL -->
            <!-- ============================================ -->
            <div id="deleteModal" class="moderators-modal">
                <div class="moderators-modal-content" style="max-width: 450px;">
                    <div class="moderators-modal-header">
                        <h3 style="color: #dc2626;">
                            <i class="fa-solid fa-triangle-exclamation w-4 h-4"></i>
                            ⚠️ Confirm Deletion
                        </h3>
                        
                        <!-- Form submits to MVC route -->
                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="moderator_id" id="deleteModeratorId">
                            
                            <p class="text-muted-foreground mb-4">
                                Are you sure you want to <strong>permanently delete</strong> this moderator?
                            </p>
                            <p id="deleteWarningText" style="color: #dc2626; font-weight: 600; margin-bottom: 1.5rem;">
                                ⚠️ This action cannot be undone!
                            </p>
                            
                            <div class="moderators-form-actions">
                                <button type="button" onclick="closeModal('deleteModal')" class="moderators-cancel-btn">
                                    Cancel
                                </button>
                                <button type="submit" class="moderators-save-btn" style="background-color: #dc2626;">
                                    <i class="fa-solid fa-trash w-4 h-4 mr-2"></i>
                                    Delete Permanently
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- JAVASCRIPT -->
            <!-- ============================================ -->
            <script>
                // Auto-hide success/error messages
                setTimeout(function() {
                    const alerts = document.querySelectorAll('.alert');
                    alerts.forEach(alert => {
                        alert.style.opacity = '0';
                        alert.style.transition = 'opacity 0.5s';
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);

                // Open Add Moderator Modal
                function openAddModeratorModal() {
                    document.getElementById('modalTitle').textContent = 'Add New Moderator';
                    document.getElementById('moderatorForm').reset();
                    document.getElementById('moderatorId').value = '';
                    document.getElementById('formAction').value = 'add';
                    document.getElementById('moderatorUsername').disabled = false;
                    document.getElementById('usernameGroup').style.display = 'block';
                    document.getElementById('passwordGroup').style.display = 'block';
                    document.getElementById('moderatorPassword').required = true;
                    document.getElementById('passwordHint').textContent = 'Minimum 6 characters';
                    document.getElementById('saveBtn').textContent = 'Save Moderator';
                    openModal('moderatorModal');
                }

                // Edit Moderator
                function editModerator(moderator) {
                    document.getElementById('modalTitle').textContent = 'Edit Moderator';
                    document.getElementById('moderatorForm').reset();
                    document.getElementById('moderatorId').value = moderator.moderator_id;
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('moderatorEmail').value = moderator.email;
                    document.getElementById('moderatorSection').value = moderator.assigned_section;
                    document.getElementById('moderatorUsername').disabled = true;
                    document.getElementById('usernameGroup').style.display = 'none';
                    document.getElementById('passwordGroup').style.display = 'block';
                    document.getElementById('moderatorPassword').required = false;
                    document.getElementById('passwordHint').textContent = 'Leave blank to keep current password';
                    document.getElementById('saveBtn').textContent = 'Update Moderator';
                    openModal('moderatorModal');
                }

                // Delete Moderator
                function deleteModerator(moderatorId, username) {
                    document.getElementById('deleteModeratorId').value = moderatorId;
                    document.getElementById('deleteWarningText').innerHTML = 
                        '⚠️ This will permanently delete moderator: <strong>' + username + '</strong>!';
                    openModal('deleteModal');
                }

                // Open Modal
                function openModal(modalId) {
                    document.getElementById(modalId).classList.add('show');
                    document.body.style.overflow = 'hidden';
                }

                // Close Modal
                function closeModal(modalId) {
                    document.getElementById(modalId).classList.remove('show');
                    document.body.style.overflow = '';
                }

                // Close modal on clicking overlay
                document.querySelectorAll('.moderators-modal').forEach(modal => {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this.id);
                        }
                    });
                });

                // Close modal on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeModal('moderatorModal');
                        closeModal('deleteModal');
                    }
                });
            </script>
        </div>
    </div>
    
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>