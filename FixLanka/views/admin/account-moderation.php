<?php
/**
 * Account Moderation Page - PRODUCTION VERSION
 * Manage banned, suspended, and active accounts
 * ✅ Strict MVC: View layer only - NO business logic
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


$basePath = '';
$currentPath = 'account-moderation';
$pageTitle = 'Account Moderation - FixLanka Admin';
$pageDescription = 'Manage banned, suspended, and active accounts across the platform';

// Extract data from Controller (NO fallback dummy data - Controller must provide)
$stats = $stats ?? ['total' => 0, 'active' => 0, 'suspended' => 0, 'banned' => 0];
$accounts = $accounts ?? [];
$totalAccounts = $totalAccounts ?? 0;
$totalPages = $totalPages ?? 0;
$allAccountsForDropdown = $allAccountsForDropdown ?? [];

// Get filter values for persistence
$searchValue = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$statusValue = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$roleValue = isset($_GET['role']) ? htmlspecialchars($_GET['role']) : '';
$sortValue = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'newest';
$pageValue = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limitValue = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

// Flash messages
$successMessage = $successMessage ?? '';
$errorMessage = $errorMessage ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/account-moderation.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Account Moderation', 'Manage banned, suspended, and active accounts across the platform'); ?>

            <main style="margin-top: 5rem;" class="dashboard-content">
                <div class="space-y-6">
                    
                    <!-- Top Action Button -->
                    <div class="action-button-container">
                        <button onclick="openBanModal()" class="btn-action-primary">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>Ban/Suspend Account</span>
                        </button>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <div class="stat-icon">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Total Accounts</p>
                                <h3 class="stat-value"><?php echo $stats['total']; ?></h3>
                                <p class="stat-desc">All registered accounts</p>
                            </div>
                        </div>

                        <div class="stat-card active">
                            <div class="stat-icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Active Accounts</p>
                                <h3 class="stat-value"><?php echo $stats['active']; ?></h3>
                                <p class="stat-desc">Currently active</p>
                            </div>
                        </div>

                        <div class="stat-card suspended">
                            <div class="stat-icon">
                                <i class="fa-solid fa-circle-pause"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Suspended Accounts</p>
                                <h3 class="stat-value"><?php echo $stats['suspended']; ?></h3>
                                <p class="stat-desc">Temporarily suspended</p>
                            </div>
                        </div>

                        <div class="stat-card banned">
                            <div class="stat-icon">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </div>
                            <div class="stat-content">
                                <p class="stat-label">Banned Accounts</p>
                                <h3 class="stat-value"><?php echo $stats['banned']; ?></h3>
                                <p class="stat-desc">Permanently banned</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Table Card -->
                    <div class="table-card">
                        <div class="table-card-header">
                            <div>
                                <h3 class="table-title">Account Management</h3>
                                <p class="table-subtitle">Review and manage all platform accounts (Total: <?php echo $totalAccounts; ?>)</p>
                            </div>
                        </div>

                        <!-- Filter Section with GET Form for Persistence -->
                        <div class="filter-section">
                            <form method="GET" action="/2nd-Year-Group-Project/FixLanka/index.php" id="filterForm">
                                <input type="hidden" name="page" value="accountModeration">
                                <div class="filter-row">
                                    <div class="search-input-wrapper">
                                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                        <input 
                                            type="text" 
                                            name="search" 
                                            id="searchInput" 
                                            placeholder="Search by name, email, or phone..." 
                                            class="search-input"
                                            value="<?php echo $searchValue; ?>">
                                    </div>

                                    <select name="status" id="statusFilter" class="filter-select">
                                        <option value="">All Status</option>
                                        <option value="ACTIVE" <?php echo $statusValue === 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                                        <option value="SUSPENDED" <?php echo $statusValue === 'SUSPENDED' ? 'selected' : ''; ?>>Suspended</option>
                                        <option value="BANNED" <?php echo $statusValue === 'BANNED' ? 'selected' : ''; ?>>Banned</option>
                                    </select>

                                    <select name="role" id="roleFilter" class="filter-select">
                                        <option value="">All Roles</option>
                                        <option value="User" <?php echo $roleValue === 'User' ? 'selected' : ''; ?>>User</option>
                                        <option value="Repairer" <?php echo $roleValue === 'Repairer' ? 'selected' : ''; ?>>Repairer</option>
                                        <option value="Company" <?php echo $roleValue === 'Company' ? 'selected' : ''; ?>>Company</option>
                                    </select>

                                    <select name="sort" id="sortFilter" class="filter-select">
                                        <option value="newest" <?php echo $sortValue === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                                        <option value="oldest" <?php echo $sortValue === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                                    </select>

                                    <select name="limit" id="limitFilter" class="filter-select">
                                        <option value="10" <?php echo $limitValue == 10 ? 'selected' : ''; ?>>10 per page</option>
                                        <option value="20" <?php echo $limitValue == 20 ? 'selected' : ''; ?>>20 per page</option>
                                        <option value="50" <?php echo $limitValue == 50 ? 'selected' : ''; ?>>50 per page</option>
                                        <option value="100" <?php echo $limitValue == 100 ? 'selected' : ''; ?>>100 per page</option>
                                    </select>

                                    <button type="submit" class="btn-filter-apply">
                                        <i class="fa-solid fa-filter"></i>
                                        Apply
                                    </button>

                                    <button type="button" class="btn-filter-clear" onclick="clearFilters()">
                                        <i class="fa-solid fa-xmark"></i>
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Accounts Table -->
                        <div class="table-container">
                            <?php if (empty($accounts)): ?>
                                <!-- Empty State -->
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fa-solid fa-inbox"></i>
                                    </div>
                                    <h3 class="empty-title">No accounts found</h3>
                                    <p class="empty-desc">Try adjusting your filters or search criteria</p>
                                </div>
                            <?php else: ?>
                                <table class="accounts-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Account Details</th>
                                            <th>Status</th>
                                            <th>Role</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Reason</th>
                                            <th>Suspended Until</th>
                                            <th>Last Updated</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($accounts as $account): 
                                            $status = $account['account_status'] ?? 'ACTIVE';
                                            $statusClass = strtolower($status);
                                            $isSuspended = $status === 'SUSPENDED';
                                            $isBanned = $status === 'BANNED';
                                            $isPermanent = isset($account['banned_permanent']) && $account['banned_permanent'] == 1;
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="account-id-badge">#<?php echo htmlspecialchars($account['account_id']); ?></span>
                                            </td>
                                            <td>
                                                <div class="account-details">
                                                    <span class="account-name"><?php echo htmlspecialchars($account['name'] ?? 'N/A'); ?></span>
                                                    <span class="account-type-small"><?php echo htmlspecialchars($account['account_type'] ?? 'N/A'); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $statusClass; ?>">
                                                    <?php echo $status; ?>
                                                    <?php if ($isPermanent): ?>
                                                    <i class="fa-solid fa-lock" title="Permanent"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="role-badge role-<?php echo strtolower($account['account_type'] ?? 'user'); ?>">
                                                    <?php echo htmlspecialchars($account['account_type'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($account['email'] ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($account['phone'] ?? 'N/A'); ?></td>
                                            <td>
                                                <?php if (!empty($account['moderation_reason'])): ?>
                                                    <span class="reason-text" title="<?php echo htmlspecialchars($account['moderation_reason']); ?>">
                                                        <?php echo htmlspecialchars(substr($account['moderation_reason'], 0, 30) . (strlen($account['moderation_reason']) > 30 ? '...' : '')); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($isSuspended && !empty($account['suspended_until'])): ?>
                                                    <span class="date-text"><?php echo date('M d, Y', strtotime($account['suspended_until'])); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="date-text"><?php echo date('M d, Y H:i', strtotime($account['updated_at'] ?? 'now')); ?></span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" onclick='viewAccountDetails(<?php echo json_encode($account); ?>)' title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                    <?php if ($isSuspended && !$isPermanent): ?>
                                                        <button class="btn-action btn-restore" onclick='confirmRestore(<?php echo json_encode($account); ?>)' title="Restore Account">
                                                            <i class="fa-solid fa-rotate-left"></i>
                                                        </button>
                                                    <?php elseif ($isBanned && $isPermanent): ?>
                                                        <button class="btn-action btn-disabled" disabled title="Permanently Banned - Cannot Restore">
                                                            <i class="fa-solid fa-lock"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn-action btn-edit" onclick='openEditModal(<?php echo json_encode($account); ?>)' title="Manage Account">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination (Server-Side) -->
                        <?php if ($totalPages > 1): ?>
                        <div class="pagination-section">
                            <div class="pagination-info">
                                <span>Page <strong><?php echo $pageValue; ?></strong> of <strong><?php echo $totalPages; ?></strong> (Total: <?php echo $totalAccounts; ?> accounts)</span>
                            </div>
                            <div class="pagination-controls">
                                <div class="pagination-buttons">
                                    <?php if ($pageValue > 1): ?>
                                    <a href="?page=accountModeration&search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $pageValue - 1; ?>" class="btn-page">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </a>
                                    <?php else: ?>
                                    <button class="btn-page" disabled>
                                        <i class="fa-solid fa-chevron-left"></i>
                                        Previous
                                    </button>
                                    <?php endif; ?>

                                    <div class="page-numbers">
                                        <?php 
                                        $startPage = max(1, $pageValue - 2);
                                        $endPage = min($totalPages, $pageValue + 2);
                                        
                                        if ($startPage > 1): ?>
                                            <a href="?page=accountModeration&search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=1" class="page-number">1</a>
                                            <?php if ($startPage > 2): ?>
                                                <span class="page-dots">...</span>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <a href="?page=accountModeration&search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $i; ?>" class="page-number <?php echo $i === $pageValue ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <span class="page-dots">...</span>
                                            <?php endif; ?>
                                            <a href="?page=accountModeration&search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $totalPages; ?>" class="page-number"><?php echo $totalPages; ?></a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($pageValue < $totalPages): ?>
                                    <a href="?page=accountModeration&search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $pageValue + 1; ?>" class="btn-page">
                                        Next
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                    <?php else: ?>
                                    <button class="btn-page" disabled>
                                        Next
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- Ban/Suspend Modal -->
            <div id="banModal" class="modal-overlay" style="display: none;">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa-solid fa-shield-halved"></i>
                            Ban/Suspend Account
                        </h3>
                        <button type="button" class="modal-close" onclick="closeModal('banModal')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="banForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration&action=ban" onsubmit="return confirmBanSuspend(event)">
                            <input type="hidden" name="account_id" id="formAccountId" value="">
                            <input type="hidden" name="account_type" id="formAccountType" value="">

                            <div class="form-group">
                                <label class="form-label">Select Account <span class="required">*</span></label>
                                <select id="banAccountSelect" class="form-input" required onchange="updateFormFields()">
                                    <option value="">-- Select an account --</option>
                                    <?php foreach ($allAccountsForDropdown as $acc): ?>
                                        <option value="<?php echo $acc['id']; ?>" 
                                                data-id="<?php echo $acc['id']; ?>" 
                                                data-type="<?php echo $acc['type']; ?>">
                                            <?php echo htmlspecialchars($acc['name']); ?> (<?php echo htmlspecialchars($acc['email']); ?>) - <?php echo $acc['type']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Action Type <span class="required">*</span></label>
                                <select id="banActionType" class="form-input" required onchange="toggleDurationFields()">
                                    <option value="">-- Select action --</option>
                                    <option value="suspend">Suspend (Temporary)</option>
                                    <option value="ban">Ban (Permanent)</option>
                                </select>
                            </div>

                            <div id="durationSection" class="form-group" style="display: none;">
                                <label class="form-label">Suspension Duration <span class="required">*</span></label>
                                <select id="banDuration" name="duration" class="form-input" onchange="toggleCustomDate()">
                                    <option value="">-- Select duration --</option>
                                    <option value="1">1 Day</option>
                                    <option value="3">3 Days</option>
                                    <option value="7">7 Days (1 Week)</option>
                                    <option value="14">14 Days (2 Weeks)</option>
                                    <option value="30">30 Days (1 Month)</option>
                                    <option value="custom">Custom...</option>
                                </select>
                            </div>

                            <div id="customDateSection" class="form-group" style="display: none;">
                                <label class="form-label">Custom Days <span class="required">*</span></label>
                                <input type="number" id="banCustomDays" name="custom_days" class="form-input" min="1" max="365" placeholder="Enter number of days">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Reason <span class="required">*</span></label>
                                <textarea id="banReason" name="reason" class="form-textarea" rows="3" required minlength="10" placeholder="Enter reason for this action (minimum 10 characters)"></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Internal Notes (Optional)</label>
                                <textarea id="banNotes" name="notes" class="form-textarea" rows="3" placeholder="Add internal notes for admin reference (optional)"></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn-secondary" onclick="closeModal('banModal')">Cancel</button>
                                <button type="submit" class="btn-danger" id="submitBanBtn">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span id="submitBanText">Apply Action</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Details Modal -->
            <div id="viewModal" class="modal-overlay" style="display: none;">
                <div class="modal-container">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa-solid fa-user-circle"></i>
                            Account Details
                        </h3>
                        <button type="button" class="modal-close" onclick="closeModal('viewModal')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body" id="accountDetailsContent">
                        <!-- JavaScript will populate this -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-secondary" onclick="closeModal('viewModal')">Close</button>
                    </div>
                </div>
            </div>

            <!-- Restore Confirmation Modal -->
            <div id="restoreModal" class="modal-overlay" style="display: none;">
                <div class="modal-container modal-small">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa-solid fa-rotate-left"></i>
                            Confirm Account Restoration
                        </h3>
                        <button type="button" class="modal-close" onclick="closeModal('restoreModal')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="warning-box">
                            <i class="fa-solid fa-exclamation-triangle"></i>
                            <p>Are you sure you want to restore this account to <strong>ACTIVE</strong> status?</p>
                        </div>
                        <div id="restoreAccountInfo" class="info-box">
                            <!-- JavaScript will populate this -->
                        </div>
                        <form id="restoreForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration&action=restore">
                            <input type="hidden" name="account_id" id="restoreAccountId" value="">
                            <input type="hidden" name="account_type" id="restoreAccountType" value="">
                            <div class="modal-footer">
                                <button type="button" class="btn-secondary" onclick="closeModal('restoreModal')">Cancel</button>
                                <button type="submit" class="btn-success">
                                    <i class="fa-solid fa-check"></i>
                                    Yes, Restore Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div id="toast" class="toast"></div>

            <script>
                // ================================
                // JAVASCRIPT FUNCTIONALITY
                // ================================

                // Clear filters function
                function clearFilters() {
                    window.location.href = '/2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration';
                }

                // Ban/Suspend Modal Functions
                function openBanModal() {
                    document.getElementById('banForm').reset();
                    document.getElementById('durationSection').style.display = 'none';
                    document.getElementById('customDateSection').style.display = 'none';
                    document.getElementById('formAccountId').value = '';
                    document.getElementById('formAccountType').value = '';
                    openModal('banModal');
                }

                function updateFormFields() {
                    const select = document.getElementById('banAccountSelect');
                    const selectedOption = select.options[select.selectedIndex];
                    
                    if (selectedOption && selectedOption.value) {
                        const accountId = selectedOption.getAttribute('data-id');
                        const accountType = selectedOption.getAttribute('data-type');
                        
                        document.getElementById('formAccountId').value = accountId;
                        document.getElementById('formAccountType').value = accountType;
                    } else {
                        document.getElementById('formAccountId').value = '';
                        document.getElementById('formAccountType').value = '';
                    }
                }

                function toggleDurationFields() {
                    const actionType = document.getElementById('banActionType').value;
                    const durationSection = document.getElementById('durationSection');
                    const banDuration = document.getElementById('banDuration');
                    const submitBtn = document.getElementById('submitBanText');
                    const form = document.getElementById('banForm');
                    
                    if (actionType === 'suspend') {
                        durationSection.style.display = 'block';
                        banDuration.required = true;
                        submitBtn.textContent = 'Suspend Account';
                        form.action = '/2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration&action=suspend';
                    } else if (actionType === 'ban') {
                        durationSection.style.display = 'none';
                        document.getElementById('customDateSection').style.display = 'none';
                        banDuration.required = false;
                        banDuration.value = '';
                        submitBtn.textContent = 'Ban Account';
                        form.action = '/2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration&action=ban';
                    } else {
                        durationSection.style.display = 'none';
                        document.getElementById('customDateSection').style.display = 'none';
                        submitBtn.textContent = 'Apply Action';
                    }
                }

                function toggleCustomDate() {
                    const duration = document.getElementById('banDuration').value;
                    const customDateSection = document.getElementById('customDateSection');
                    const customDays = document.getElementById('banCustomDays');
                    
                    if (duration === 'custom') {
                        customDateSection.style.display = 'block';
                        customDays.required = true;
                    } else {
                        customDateSection.style.display = 'none';
                        customDays.required = false;
                        customDays.value = '';
                    }
                }

                function confirmBanSuspend(event) {
                    const actionType = document.getElementById('banActionType').value;
                    const accountSelect = document.getElementById('banAccountSelect');
                    const accountName = accountSelect.options[accountSelect.selectedIndex].text;
                    const reason = document.getElementById('banReason').value;
                    
                    let message = '';
                    if (actionType === 'ban') {
                        message = `Are you sure you want to PERMANENTLY BAN this account?\n\n` +
                                  `Account: ${accountName}\n` +
                                  `Reason: ${reason}\n\n` +
                                  `⚠️ WARNING: This action cannot be undone!`;
                    } else if (actionType === 'suspend') {
                        const duration = document.getElementById('banDuration').value;
                        const customDays = document.getElementById('banCustomDays').value;
                        const days = duration === 'custom' ? customDays : duration;
                        message = `Are you sure you want to SUSPEND this account for ${days} days?\n\n` +
                                  `Account: ${accountName}\n` +
                                  `Reason: ${reason}`;
                    }
                    
                    return confirm(message);
                }

                // View Account Details
                function viewAccountDetails(account) {
                    const status = account.account_status || 'ACTIVE';
                    const statusClass = status.toLowerCase();
                    const isPermanent = account.banned_permanent == 1;
                    
                    const content = document.getElementById('accountDetailsContent');
                    content.innerHTML = `
                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">Account ID:</span>
                                <span class="detail-value">#${escapeHtml(account.account_id)}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${escapeHtml(account.name || 'N/A')}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${escapeHtml(account.email || 'N/A')}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">${escapeHtml(account.phone || 'N/A')}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Account Type:</span>
                                <span class="detail-value">
                                    <span class="role-badge role-${(account.account_type || 'user').toLowerCase()}">
                                        ${account.account_type || 'N/A'}
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge status-${statusClass}">
                                        ${status}
                                        ${isPermanent ? '<i class="fa-solid fa-lock" title="Permanent"></i>' : ''}
                                    </span>
                                </span>
                            </div>
                            ${account.moderation_reason ? `
                            <div class="detail-item full-width">
                                <span class="detail-label">Moderation Reason:</span>
                                <span class="detail-value">${escapeHtml(account.moderation_reason)}</span>
                            </div>
                            ` : ''}
                            ${account.suspended_until ? `
                            <div class="detail-item">
                                <span class="detail-label">Suspended Until:</span>
                                <span class="detail-value">${formatDate(account.suspended_until)}</span>
                            </div>
                            ` : ''}
                            <div class="detail-item">
                                <span class="detail-label">Last Updated:</span>
                                <span class="detail-value">${formatDateTime(account.updated_at)}</span>
                            </div>
                        </div>
                    `;
                    openModal('viewModal');
                }

                // Restore Account
                function confirmRestore(account) {
                    document.getElementById('restoreAccountId').value = account.account_id;
                    document.getElementById('restoreAccountType').value = account.account_type;
                    
                    document.getElementById('restoreAccountInfo').innerHTML = `
                        <p><strong>Account:</strong> ${escapeHtml(account.name)}</p>
                        <p><strong>Email:</strong> ${escapeHtml(account.email)}</p>
                        <p><strong>Type:</strong> ${escapeHtml(account.account_type)}</p>
                        <p><strong>Current Status:</strong> <span class="status-badge status-${(account.account_status || 'active').toLowerCase()}">${account.account_status}</span></p>
                    `;
                    openModal('restoreModal');
                }

                // Open Edit Modal (redirect to ban modal with pre-filled data)
                function openEditModal(account) {
                    openBanModal();
                    // Pre-select the account
                    const select = document.getElementById('banAccountSelect');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].getAttribute('data-id') == account.account_id && 
                            select.options[i].getAttribute('data-type') === account.account_type) {
                            select.selectedIndex = i;
                            updateFormFields();
                            break;
                        }
                    }
                }

                // Modal Functions
                function openModal(modalId) {
                    document.getElementById(modalId).style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                function closeModal(modalId) {
                    document.getElementById(modalId).style.display = 'none';
                    document.body.style.overflow = 'auto';
                }

                // Close modal on overlay click
                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this.id);
                        }
                    });
                });

                // Toast notification
                function showToast(message, type = 'success') {
                    const toast = document.getElementById('toast');
                    toast.textContent = message;
                    toast.className = `toast toast-${type} show`;
                    setTimeout(() => {
                        toast.className = 'toast';
                    }, 5000);
                }

                // Utility functions
                function escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                function formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                }

                function formatDateTime(dateString) {
                    return new Date(dateString).toLocaleDateString('en-US', { 
                        year: 'numeric', month: 'short', day: 'numeric', 
                        hour: '2-digit', minute: '2-digit' 
                    });
                }

                // Show flash messages on page load
                document.addEventListener('DOMContentLoaded', function() {
                    <?php if (!empty($successMessage)): ?>
                        showToast('<?php echo addslashes($successMessage); ?>', 'success');
                    <?php endif; ?>
                    
                    <?php if (!empty($errorMessage)): ?>
                        showToast('<?php echo addslashes($errorMessage); ?>', 'error');
                    <?php endif; ?>
                });
            </script>
        </div>
    </div>
</body>
</html>