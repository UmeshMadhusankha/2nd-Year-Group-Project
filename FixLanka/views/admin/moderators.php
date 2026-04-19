<?php
/**
 * moderators.php - ENHANCED SECURE VIEW
 * ✅ Separate Add, Edit, and Reset Password modals
 * ✅ Password confirmation fields
 * ✅ Client-side validation hints
 * ✅ Improved UI/UX
 * ✅ Responsive design
 * ✅ FIXED: No more inline <style> block - all CSS lives in moderators.css
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../controllers/ModeratorController.php';

$basePath = '';
$currentPath = 'moderators';

// ✅ VIEW ONLY FETCHES DATA - NO POST HANDLING
try {
    $controller = new ModeratorController();
    
    // Get filter parameters
    $search = $_GET['search'] ?? '';
    $sectionFilter = $_GET['section'] ?? '';
    $page = (int)($_GET['page'] ?? 1);
    
    // ✅ ONLY GET DATA FOR DISPLAY (View's job)
    $data = $controller->getModerators($search, $sectionFilter, $page, 20);
    $moderatorStats = $controller->getStatistics();
    
    // Extract data
    $moderators = $data['moderators'];
    $allModerators = $data['allModerators'];
    $totalPages = $data['totalPages'];
    
} catch (Exception $e) {
    error_log("Moderator Page Error: " . $e->getMessage());
    $moderators = [];
    $allModerators = [];
    $moderatorStats = ['total' => 0, 'active' => 0, 'suspended' => 0];
    $totalPages = 1;
}

// Get success/error messages from session
$successMessage = $_SESSION['success_message'] ?? '';
$errorMessage = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Get page title and description
$pageTitle = 'Moderator Management - FixLanka Admin';
$pageDescription = 'Manage system moderators, assign sections, and control access';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/moderators.css">
    <!-- All styles are now in moderators.css - no inline styles needed -->
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
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
                    <?php if (!empty($successMessage)): ?>
                        <div id="successMessage" class="alert alert-success" style="display: flex;">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?php echo htmlspecialchars($successMessage); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMessage)): ?>
                        <div id="errorMessage" class="alert alert-error" style="display: flex;">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span><?php echo htmlspecialchars($errorMessage); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Moderators Table Card -->
                    <div class="moderators-table-container">
                        <div class="moderators-table-header">
                            <h3>All Moderators</h3>
                            <p>Total: <?php echo count($allModerators); ?> moderators (<?php echo $moderatorStats['active'] ?? 0; ?> active)</p>
                            
                            <!-- Search & Filter -->
                            <form method="GET" action="" class="moderators-search-container">
                                <div class="moderators-search-bar">
                                    <input type="text" name="search" placeholder="Search by username or email..." 
                                           value="<?php echo htmlspecialchars($search); ?>" class="moderators-search-input">
                                    <button type="submit" class="moderators-search-btn">
                                        <i class="fa-solid fa-search"></i> Search
                                    </button>
                                </div>
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
                                        <th>Status</th>
                                        <th>Last Login</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($moderators)): ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">
                                                <i class="fa-solid fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                                                No moderators found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($moderators as $mod): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($mod['moderator_id']); ?></td>
                                                <td><strong><?php echo htmlspecialchars($mod['username']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($mod['email']); ?></td>
                                                <td>
                                                    <?php if ($mod['status'] === 'active'): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Suspended</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $mod['last_login'] ? date('M d, Y H:i', strtotime($mod['last_login'])) : 'Never'; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($mod['created_at'])); ?></td>
                                                <td>
                                                    <div class="action-buttons-group">
                                                        
                                                        <!-- Reset Password Button -->
                                                        <button onclick='resetPassword(<?php echo json_encode($mod); ?>)' 
                                                                class="btn-reset-password" title="Reset Password">
                                                            <i class="fa-solid fa-key"></i>
                                                        </button>
                                                        
                                                        <!-- Toggle Status Button -->
                                                        <button onclick="toggleStatus(<?php echo $mod['moderator_id']; ?>, '<?php echo $mod['status'] === 'active' ? 'suspended' : 'active'; ?>')" 
                                                                class="btn-action <?php echo $mod['status'] === 'active' ? 'btn-warning' : 'btn-success'; ?>" 
                                                                title="<?php echo $mod['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fa-solid fa-<?php echo $mod['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
                                                        </button>
                                                        
                                                        <!-- Delete Button -->
                                                        <button onclick="deleteModerator(<?php echo $mod['moderator_id']; ?>, '<?php echo htmlspecialchars($mod['username']); ?>')" 
                                                                class="btn-action btn-delete" title="Delete">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination removed -->
                    </div>
                </div>
            </main>

            <!-- ============================================ -->
            <!-- ADD MODERATOR MODAL -->
            <!-- ============================================ -->
            <div id="addModal" class="moderators-modal">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3>Add New Moderator</h3>
                        <button onclick="closeModal('addModal')" class="close-btn">&times;</button>
                    </div>
                        
                    <form id="addForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action" class="moderators-modal-form">
                        <input type="hidden" name="action" value="create">

                        <!-- Username -->
                        <div class="moderators-form-group">
                            <label for="addUsername" class="moderators-form-label">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="username" id="addUsername" 
                                   class="moderators-form-input" required minlength="3" maxlength="50"
                                   pattern="[a-zA-Z0-9_]+" title="Only letters, numbers, and underscores allowed">
                            <small class="text-muted-foreground">3-50 characters, letters, numbers, and underscores only</small>
                        </div>

                        <!-- Email -->
                        <div class="moderators-form-group">
                            <label for="addEmail" class="moderators-form-label">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="addEmail" 
                                   class="moderators-form-input" required maxlength="100">
                        </div>

                        <!-- Password -->
                        <div class="moderators-form-group">
                            <label for="addPassword" class="moderators-form-label">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password" id="addPassword" 
                                   class="moderators-form-input" required minlength="8" maxlength="100"
                                   oninput="checkPasswordMatch('add')">
                            <div class="password-requirements">
                                <strong>Password Requirements:</strong>
                                <ul>
                                    <li>Minimum 8 characters</li>
                                    <li>At least one uppercase letter</li>
                                    <li>At least one lowercase letter</li>
                                    <li>At least one number</li>
                                    <li>At least one special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="moderators-form-group">
                            <label for="addConfirmPassword" class="moderators-form-label">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="confirm_password" id="addConfirmPassword" 
                                   class="moderators-form-input" required minlength="8" maxlength="100"
                                   oninput="checkPasswordMatch('add')">
                            <div id="addPasswordMatch" class="password-match-indicator"></div>
                        </div>

                        
                        <!-- Modal Actions -->
                        <div class="moderators-form-actions">
                            <button type="button" onclick="closeModal('addModal')" class="moderators-btn-secondary">
                                <i class="fa-solid fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="moderators-btn-primary">
                                <i class="fa-solid fa-user-plus"></i> Create Moderator
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- EDIT MODERATOR MODAL (Email & Section Only) -->

            <!-- ============================================ -->
            <!-- RESET PASSWORD MODAL -->
            <!-- ============================================ -->
            <div id="resetPasswordModal" class="moderators-modal">
                <div class="moderators-modal-content">
                    <div class="moderators-modal-header">
                        <h3>
                            <i class="fa-solid fa-key"></i> Reset Password
                        </h3>
                        <button onclick="closeModal('resetPasswordModal')" class="close-btn">&times;</button>
                    </div>
                        
                    <form id="resetPasswordForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action" class="moderators-modal-form">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="moderator_id" id="resetModeratorId">

                        <!-- Display Username -->
                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Moderator Username</label>
                            <input type="text" id="resetUsername" class="moderators-form-input" disabled>
                        </div>

                        <!-- New Password -->
                        <div class="moderators-form-group">
                            <label for="resetNewPassword" class="moderators-form-label">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="new_password" id="resetNewPassword" 
                                   class="moderators-form-input" required minlength="8" maxlength="100"
                                   oninput="checkPasswordMatch('reset')">
                            <div class="password-requirements">
                                <strong>Password Requirements:</strong>
                                <ul>
                                    <li>Minimum 8 characters</li>
                                    <li>At least one uppercase letter</li>
                                    <li>At least one lowercase letter</li>
                                    <li>At least one number</li>
                                    <li>At least one special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="moderators-form-group">
                            <label for="resetConfirmPassword" class="moderators-form-label">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="confirm_password" id="resetConfirmPassword" 
                                   class="moderators-form-input" required minlength="8" maxlength="100"
                                   oninput="checkPasswordMatch('reset')">
                            <div id="resetPasswordMatch" class="password-match-indicator"></div>
                        </div>
                        
                        <!-- Modal Actions -->
                        <div class="moderators-form-actions">
                            <button type="button" onclick="closeModal('resetPasswordModal')" class="moderators-btn-secondary">
                                <i class="fa-solid fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="moderators-btn-primary">
                                <i class="fa-solid fa-key"></i> Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- DELETE CONFIRMATION MODAL -->
            <!-- ============================================ -->
            <div id="deleteModal" class="moderators-modal">
                <div class="moderators-modal-content" style="max-width: 500px;">
                    <div class="moderators-modal-header">
                        <h3 style="color: #dc2626;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Confirm Deletion
                        </h3>
                        <button onclick="closeModal('deleteModal')" class="close-btn">&times;</button>
                    </div>
                        
                    <form method="POST" action="/2nd-Year-Group-Project/FixLanka/admin-moderators-action" class="moderators-modal-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="moderator_id" id="deleteModeratorId">
                        
                        <p id="deleteWarningText" class="text-muted-foreground mb-4"></p>
                        
                        <div class="moderators-form-group">
                            <label class="moderators-form-label">Delete Type:</label>
                            <select name="delete_type" id="deleteTypeSelect" class="moderators-form-select" onchange="updateDeleteWarning()">
                                <option value="soft">Soft Delete (Deactivate - Recommended)</option>
                                <option value="hard">Hard Delete (Permanent - Dangerous)</option>
                            </select>
                            <small class="text-muted-foreground">
                                Soft delete keeps data intact. Hard delete removes permanently.
                            </small>
                        </div>
                        
                        <div class="moderators-form-actions">
                            <button type="button" onclick="closeModal('deleteModal')" class="moderators-btn-secondary">
                                <i class="fa-solid fa-times"></i> Cancel
                            </button>
                            <button type="submit" class="moderators-btn-danger">
                                <i class="fa-solid fa-trash"></i> Confirm Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- JAVASCRIPT -->
            <script>
                // Auto-hide success/error messages
                setTimeout(function() {
                    const messages = document.querySelectorAll('#successMessage, #errorMessage');
                    messages.forEach(msg => {
                        msg.style.opacity = '0';
                        msg.style.transition = 'opacity 0.3s ease';
                        setTimeout(() => msg.style.display = 'none', 300);
                    });
                }, 5000);

                // Open Add Moderator Modal
                function openAddModeratorModal() {
                    document.getElementById('addForm').reset();
                    document.getElementById('addPasswordMatch').innerHTML = '';
                    openModal('addModal');
                }


                // Reset Password
                function resetPassword(moderator) {
                    document.getElementById('resetPasswordForm').reset();
                    document.getElementById('resetModeratorId').value = moderator.moderator_id;
                    document.getElementById('resetUsername').value = moderator.username;
                    document.getElementById('resetPasswordMatch').innerHTML = '';
                    openModal('resetPasswordModal');
                }

                // Delete Moderator
                function deleteModerator(moderatorId, username) {
                    document.getElementById('deleteModeratorId').value = moderatorId;
                    document.getElementById('deleteTypeSelect').value = 'soft';
                    updateDeleteWarning();
                    document.getElementById('deleteWarningText').innerHTML = 
                        '⚠️ You are about to delete moderator: <strong>' + username + '</strong>. Are you sure?';
                    openModal('deleteModal');
                }

                // Update Delete Warning
                function updateDeleteWarning() {
                    const type = document.getElementById('deleteTypeSelect').value;
                    const warningText = document.getElementById('deleteWarningText');
                    if (type === 'hard') {
                        warningText.style.color = '#dc2626';
                        warningText.style.fontWeight = 'bold';
                    } else {
                        warningText.style.color = '';
                        warningText.style.fontWeight = '';
                    }
                }

                // Toggle Status
                function toggleStatus(moderatorId, newStatus) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/2nd-Year-Group-Project/FixLanka/admin-moderators-action';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'toggle_status';
                    
                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'moderator_id';
                    idInput.value = moderatorId;
                    
                    const statusInput = document.createElement('input');
                    statusInput.type = 'hidden';
                    statusInput.name = 'status';
                    statusInput.value = newStatus;
                    
                    form.appendChild(actionInput);
                    form.appendChild(idInput);
                    form.appendChild(statusInput);
                    document.body.appendChild(form);
                    form.submit();
                }

                // Check Password Match
                function checkPasswordMatch(type) {
                    const password = document.getElementById(type + 'Password')?.value || 
                                    document.getElementById(type + 'NewPassword')?.value;
                    const confirmPassword = document.getElementById(type + 'ConfirmPassword').value;
                    const indicator = document.getElementById(type + 'PasswordMatch');
                    
                    if (!password || !confirmPassword) {
                        indicator.innerHTML = '';
                        return;
                    }
                    
                    if (password === confirmPassword) {
                        indicator.className = 'password-match-indicator match';
                        indicator.innerHTML = '<i class="fa-solid fa-check"></i> Passwords match';
                    } else {
                        indicator.className = 'password-match-indicator no-match';
                        indicator.innerHTML = '<i class="fa-solid fa-xmark"></i> Passwords do not match';
                    }
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
                        closeModal('addModal');
                        closeModal('editModal');
                        closeModal('resetPasswordModal');
                        closeModal('deleteModal');
                    }
                });

                // Add loading state to all submit buttons
                document.querySelectorAll('form').forEach(form => {
                    form.addEventListener('submit', function() {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        if (submitBtn) {
                            const originalHtml = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                            submitBtn.classList.add('loading');
                        }
                    });
                });
            </script>
        </div>
    </div>
    
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>