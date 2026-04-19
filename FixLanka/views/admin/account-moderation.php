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

$dropdownSourceAccounts = !empty($allAccountsForDropdown)
    ? $allAccountsForDropdown
    : array_map(static function ($account) {
        return [
            'id' => $account['account_id'] ?? null,
            'type' => $account['account_type'] ?? 'User',
            'name' => $account['name'] ?? 'Unknown',
            'email' => $account['email'] ?? ''
        ];
    }, $accounts);

if (empty($dropdownSourceAccounts)) {
    try {
        require_once __DIR__ . '/../../config/database.php';

        $userRows = [];
        $repairerRows = [];
        $companyRows = [];

        try {
            $stmt = $pdo->query("SELECT user_id AS id, CONCAT(COALESCE(f_name,''), ' ', COALESCE(l_name,'')) AS name, COALESCE(email,'') AS email FROM User ORDER BY user_id DESC");
            $userRows = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
        } catch (Throwable $ignored) {
            $userRows = [];
        }

        try {
            $stmt = $pdo->query("SELECT repairer_id AS id, CONCAT(COALESCE(f_name,''), ' ', COALESCE(l_name,'')) AS name, COALESCE(email,'') AS email FROM Repairer ORDER BY repairer_id DESC");
            $repairerRows = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
        } catch (Throwable $ignored) {
            $repairerRows = [];
        }

        try {
            $stmt = $pdo->query("SELECT company_id AS id, COALESCE(name,'') AS name, COALESCE(email,'') AS email FROM Company ORDER BY company_id DESC");
            $companyRows = $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
        } catch (Throwable $ignored) {
            $companyRows = [];
        }

        foreach ($userRows as $row) {
            $dropdownSourceAccounts[] = [
                'id' => (int)($row['id'] ?? 0),
                'type' => 'User',
                'name' => trim((string)($row['name'] ?? '')) ?: 'User #' . (int)($row['id'] ?? 0),
                'email' => (string)($row['email'] ?? '')
            ];
        }
        foreach ($repairerRows as $row) {
            $dropdownSourceAccounts[] = [
                'id' => (int)($row['id'] ?? 0),
                'type' => 'Repairer',
                'name' => trim((string)($row['name'] ?? '')) ?: 'Repairer #' . (int)($row['id'] ?? 0),
                'email' => (string)($row['email'] ?? '')
            ];
        }
        foreach ($companyRows as $row) {
            $dropdownSourceAccounts[] = [
                'id' => (int)($row['id'] ?? 0),
                'type' => 'Company',
                'name' => trim((string)($row['name'] ?? '')) ?: 'Company #' . (int)($row['id'] ?? 0),
                'email' => (string)($row['email'] ?? '')
            ];
        }
    } catch (Throwable $ignored) {
    }
}

$dropdownAccountGroups = [
    'User' => [],
    'Repairer' => [],
    'Company' => [],
    'Other' => []
];

foreach ($dropdownSourceAccounts as $acc) {
    $accTypeRaw = trim((string)($acc['type'] ?? ''));
    if ($accTypeRaw === '') {
        $accTypeRaw = 'User';
    }

    if (strcasecmp($accTypeRaw, 'Moderator') === 0) {
        continue;
    }

    $normalizedType = ucfirst(strtolower($accTypeRaw));
    if (!isset($dropdownAccountGroups[$normalizedType])) {
        $normalizedType = 'Other';
    }

    $dropdownAccountGroups[$normalizedType][] = [
        'id' => $acc['id'] ?? '',
        'type' => $accTypeRaw,
        'name' => $acc['name'] ?? 'Unknown',
        'email' => $acc['email'] ?? ''
    ];
}

$flatDropdownAccounts = [];
foreach ($dropdownAccountGroups as $groupAccounts) {
    foreach ($groupAccounts as $acc) {
        $flatDropdownAccounts[] = [
            'id' => (string)($acc['id'] ?? ''),
            'type' => (string)($acc['type'] ?? ''),
            'name' => (string)($acc['name'] ?? ''),
            'email' => (string)($acc['email'] ?? ''),
            'label' => trim((string)($acc['name'] ?? '')) . ' (' . trim((string)($acc['type'] ?? '')) . ') - ' . trim((string)($acc['email'] ?? ''))
        ];
    }
}

// Get filter values for persistence
$searchRaw = trim((string)($_GET['search'] ?? ''));
$statusRaw = strtoupper(trim((string)($_GET['status'] ?? '')));
$roleRaw = trim((string)($_GET['role'] ?? ''));
$sortRaw = trim((string)($_GET['sort'] ?? 'newest'));
$pageValue = max(1, (int)($_GET['page'] ?? 1));
$limitValue = (int)($_GET['limit'] ?? 10);
if (!in_array($limitValue, [10, 20, 50, 100], true)) {
    $limitValue = 10;
}

$searchValue = htmlspecialchars($searchRaw, ENT_QUOTES, 'UTF-8');
$statusValue = htmlspecialchars($statusRaw, ENT_QUOTES, 'UTF-8');
$roleValue = htmlspecialchars($roleRaw, ENT_QUOTES, 'UTF-8');
$sortValue = htmlspecialchars($sortRaw, ENT_QUOTES, 'UTF-8');

if (empty($accounts)) {
    try {
        require_once __DIR__ . '/../../config/database.php';

        $tableHasColumn = static function (PDO $pdo, string $table, string $column): bool {
            try {
                $stmt = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column LIMIT 1");
                $stmt->execute([':table' => $table, ':column' => $column]);
                return (bool)$stmt->fetchColumn();
            } catch (Throwable $e) {
                return false;
            }
        };

        $tableExists = static function (PDO $pdo, string $table): bool {
            try {
                $stmt = $pdo->prepare("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table LIMIT 1");
                $stmt->execute([':table' => $table]);
                return (bool)$stmt->fetchColumn();
            } catch (Throwable $e) {
                return false;
            }
        };

        $fetchAccounts = static function (PDO $pdo, string $table, string $idColumn, string $nameExpr, string $emailColumn, ?string $phoneColumn, string $typeLabel) : array {
            $phoneExpr = ($phoneColumn) ? $phoneColumn : "''";
            
            // Join with centralized status table
            $sql = "SELECT 
                        t.{$idColumn} AS account_id, 
                        TRIM({$nameExpr}) AS name, 
                        " . $pdo->quote($typeLabel) . " AS account_type, 
                        COALESCE(t.{$emailColumn}, '') AS email, 
                        COALESCE(t.{$phoneExpr}, '') AS phone, 
                        COALESCE(s.account_status, 'ACTIVE') AS account_status, 
                        COALESCE(s.moderation_reason, '') AS moderation_reason, 
                        s.suspended_until AS suspended_until, 
                        COALESCE(s.banned_permanent, 0) AS banned_permanent, 
                        COALESCE(s.last_updated, CURRENT_TIMESTAMP) AS updated_at 
                    FROM `{$table}` t
                    LEFT JOIN account_moderation_status s 
                        ON s.account_id = t.{$idColumn} 
                        AND s.account_type = " . $pdo->quote($typeLabel);

            try {
                $stmt = $pdo->query($sql);
                return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
            } catch (Throwable $e) {
                error_log("Error fetching accounts for {$typeLabel}: " . $e->getMessage());
                return [];
            }
        };

        $allAccountsMerged = array_merge(
            $fetchAccounts($pdo, 'user', 'user_id', "CONCAT(COALESCE(f_name,''), ' ', COALESCE(l_name,''))", 'email', 'phone', 'User'),
            $fetchAccounts($pdo, 'repairer', 'repairer_id', "CONCAT(COALESCE(f_name,''), ' ', COALESCE(l_name,''))", 'email', 'phone', 'Repairer'),
            $fetchAccounts($pdo, 'company', 'company_id', "COALESCE(name, '')", 'email', 'contact_no', 'Company')
        );

        $stats = [
            'total' => count($allAccountsMerged),
            'active' => 0,
            'suspended' => 0,
            'banned' => 0,
        ];

        foreach ($allAccountsMerged as $acc) {
            $status = strtoupper((string)($acc['account_status'] ?? 'ACTIVE'));
            if ($status === 'BANNED') {
                $stats['banned']++;
            } elseif ($status === 'SUSPENDED') {
                $stats['suspended']++;
            } else {
                $stats['active']++;
            }
        }

        $filteredAccounts = array_values(array_filter($allAccountsMerged, static function ($acc) use ($searchRaw, $statusRaw, $roleRaw) {
            $normalizedStatus = strtoupper((string)($acc['account_status'] ?? 'ACTIVE'));

            if ($statusRaw !== '') {
                if ($normalizedStatus !== $statusRaw) {
                    return false;
                }
            } else {
                if (!in_array($normalizedStatus, ['SUSPENDED', 'BANNED'], true)) {
                    return false;
                }
            }

            if ($roleRaw !== '' && strtolower((string)($acc['account_type'] ?? '')) !== strtolower($roleRaw)) {
                return false;
            }

            if ($searchRaw !== '') {
                $needle = strtolower($searchRaw);
                $haystack = strtolower((string)($acc['name'] ?? '') . ' ' . (string)($acc['email'] ?? '') . ' ' . (string)($acc['phone'] ?? ''));
                if (strpos($haystack, $needle) === false) {
                    return false;
                }
            }

            return true;
        }));

        usort($filteredAccounts, static function ($a, $b) use ($sortRaw) {
            $aTime = strtotime((string)($a['updated_at'] ?? '1970-01-01')) ?: 0;
            $bTime = strtotime((string)($b['updated_at'] ?? '1970-01-01')) ?: 0;
            if (strtolower($sortRaw) === 'oldest') {
                return $aTime <=> $bTime;
            }
            return $bTime <=> $aTime;
        });

        $totalAccounts = count($filteredAccounts);
        $totalPages = $totalAccounts > 0 ? (int)ceil($totalAccounts / $limitValue) : 0;

        if ($totalPages > 0 && $pageValue > $totalPages) {
            $pageValue = $totalPages;
        }

        $offset = max(0, ($pageValue - 1) * $limitValue);
        $accounts = array_slice($filteredAccounts, $offset, $limitValue);
    } catch (Throwable $e) {
        error_log('Admin account moderation fallback load failed: ' . $e->getMessage());
    }
}

// Enrichment from log is now redundant as it's included in the primary fetchAccounts join

// Flash messages
$successMessage = $successMessage ?? (isset($_GET['success']) ? trim((string)$_GET['success']) : '');
$errorMessage = $errorMessage ?? (isset($_GET['error']) ? trim((string)$_GET['error']) : '');
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
                            <form method="GET" action="/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php" id="filterForm">
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
                                    <a href="?search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $pageValue - 1; ?>" class="btn-page">
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
                                            <a href="?search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=1" class="page-number">1</a>
                                            <?php if ($startPage > 2): ?>
                                                <span class="page-dots">...</span>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <a href="?search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $i; ?>" class="page-number <?php echo $i === $pageValue ? 'active' : ''; ?>"><?php echo $i; ?></a>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <span class="page-dots">...</span>
                                            <?php endif; ?>
                                            <a href="?search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $totalPages; ?>" class="page-number"><?php echo $totalPages; ?></a>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($pageValue < $totalPages): ?>
                                    <a href="?search=<?php echo urlencode($searchValue); ?>&status=<?php echo urlencode($statusValue); ?>&role=<?php echo urlencode($roleValue); ?>&sort=<?php echo urlencode($sortValue); ?>&limit=<?php echo $limitValue; ?>&page=<?php echo $pageValue + 1; ?>" class="btn-page">
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
                        <form id="banForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/api/account-moderation.php" onsubmit="return confirmBanSuspend(event)">
                            <input type="hidden" name="action" id="formActionType" value="ban">
                            <input type="hidden" name="account_id" id="formAccountId" value="">
                            <input type="hidden" name="account_type" id="formAccountType" value="">

                            <div class="form-group">
                                <label class="form-label">Select Account <span class="required">*</span></label>
                                <select id="banAccountSelect" class="form-input" required onchange="updateFormFieldsFromSelect()" onkeydown="handleAccountSelectTypeahead(event)">
                                    <option value="">-- Select an account --</option>
                                    <?php foreach ($dropdownAccountGroups as $groupLabel => $groupAccounts): ?>
                                        <?php if (!empty($groupAccounts)): ?>
                                            <optgroup label="<?php echo htmlspecialchars($groupLabel); ?>">
                                                <?php foreach ($groupAccounts as $acc): ?>
                                                    <option value="<?php echo htmlspecialchars($acc['id']); ?>"
                                                            data-id="<?php echo htmlspecialchars($acc['id']); ?>"
                                                            data-type="<?php echo htmlspecialchars($acc['type']); ?>"
                                                            data-name="<?php echo htmlspecialchars($acc['name']); ?>"
                                                            data-email="<?php echo htmlspecialchars($acc['email']); ?>">
                                                        <?php echo htmlspecialchars($acc['name'] . ' (' . $acc['type'] . ') - ' . $acc['email']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endif; ?>
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
                        <form id="restoreForm" method="POST" action="/2nd-Year-Group-Project/FixLanka/api/account-moderation.php">
                            <input type="hidden" name="action" value="restore">
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

            <!-- Action Confirmation Modal -->
            <div id="actionConfirmModal" class="modal-overlay" style="display: none;">
                <div class="modal-container modal-small">
                    <div class="modal-header">
                        <h3 class="modal-title">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Confirm Action
                        </h3>
                        <button type="button" class="modal-close" onclick="closeModal('actionConfirmModal')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="warning-box">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <p id="actionConfirmMessage">Please confirm this action.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-secondary" onclick="closeModal('actionConfirmModal')">Cancel</button>
                            <button type="button" class="btn-danger" id="actionConfirmProceedBtn">
                                <i class="fa-solid fa-check"></i>
                                Yes, Continue
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div id="toast" class="toast"></div>

            <script>
                // ================================
                // JAVASCRIPT FUNCTIONALITY
                // ================================
                const banAccountCatalog = <?php echo json_encode($flatDropdownAccounts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                let pendingActionConfirm = null;
                let accountSelectTypeBuffer = '';
                let accountSelectTypeTimer = null;

                // Clear filters function
                function clearFilters() {
                    window.location.href = '/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php';
                }

                // Ban/Suspend Modal Functions
                function openBanModal() {
                    document.getElementById('banForm').reset();
                    document.getElementById('durationSection').style.display = 'none';
                    document.getElementById('customDateSection').style.display = 'none';
                    document.getElementById('formActionType').value = 'ban';
                    document.getElementById('formAccountId').value = '';
                    document.getElementById('formAccountType').value = '';
                    const select = document.getElementById('banAccountSelect');
                    if (select) {
                        select.selectedIndex = 0;
                    }
                    openModal('banModal');
                }

                function updateFormFieldsFromSelect() {
                    const select = document.getElementById('banAccountSelect');
                    const selectedOption = select ? select.options[select.selectedIndex] : null;
                    const hiddenId = document.getElementById('formAccountId');
                    const hiddenType = document.getElementById('formAccountType');

                    if (selectedOption && selectedOption.value) {
                        hiddenId.value = selectedOption.getAttribute('data-id') || selectedOption.value || '';
                        hiddenType.value = selectedOption.getAttribute('data-type') || '';
                    } else {
                        hiddenId.value = '';
                        hiddenType.value = '';
                    }
                }

                function handleAccountSelectTypeahead(event) {
                    const select = document.getElementById('banAccountSelect');
                    if (!select) return;

                    if (event.key.length !== 1 || event.ctrlKey || event.metaKey || event.altKey) {
                        return;
                    }

                    accountSelectTypeBuffer += event.key.toLowerCase();

                    if (accountSelectTypeTimer) {
                        clearTimeout(accountSelectTypeTimer);
                    }

                    accountSelectTypeTimer = setTimeout(() => {
                        accountSelectTypeBuffer = '';
                    }, 700);

                    const options = Array.from(select.options).filter(opt => opt.value);
                    const match = options.find(opt => {
                        const text = (opt.textContent || '').toLowerCase().trim();
                        return text.startsWith(accountSelectTypeBuffer) || text.includes(accountSelectTypeBuffer);
                    });

                    if (match) {
                        select.value = match.value;
                        updateFormFieldsFromSelect();
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
                        document.getElementById('formActionType').value = 'suspend';
                        submitBtn.textContent = 'Suspend Account';
                    } else if (actionType === 'ban') {
                        durationSection.style.display = 'none';
                        document.getElementById('customDateSection').style.display = 'none';
                        banDuration.required = false;
                        banDuration.value = '';
                        document.getElementById('formActionType').value = 'ban';
                        submitBtn.textContent = 'Ban Account';
                    } else {
                        durationSection.style.display = 'none';
                        document.getElementById('customDateSection').style.display = 'none';
                        document.getElementById('formActionType').value = '';
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
                    if (event) {
                        event.preventDefault();
                    }

                    updateFormFieldsFromSelect();

                    const actionType = document.getElementById('banActionType').value;
                    const accountSelect = document.getElementById('banAccountSelect');
                    const accountName = accountSelect && accountSelect.selectedIndex > 0
                        ? (accountSelect.options[accountSelect.selectedIndex].text || '').trim()
                        : '';
                    const selectedId = document.getElementById('formAccountId').value;
                    const selectedType = document.getElementById('formAccountType').value;
                    const reason = document.getElementById('banReason').value;

                    if (!selectedId || !selectedType || !accountName) {
                        showToast('Please select a valid account from the suggestions list.', 'error');
                        return false;
                    }
                    
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

                    openActionConfirmModal(message, function () {
                        const form = document.getElementById('banForm');
                        if (form) {
                            form.submit();
                        }
                    });

                    return false;
                }

                function openActionConfirmModal(message, onConfirm) {
                    const messageEl = document.getElementById('actionConfirmMessage');
                    const proceedBtn = document.getElementById('actionConfirmProceedBtn');

                    if (messageEl) {
                        messageEl.innerHTML = String(message || '').replace(/\n/g, '<br>');
                    }

                    pendingActionConfirm = typeof onConfirm === 'function' ? onConfirm : null;

                    if (proceedBtn) {
                        proceedBtn.onclick = function () {
                            const action = pendingActionConfirm;
                            pendingActionConfirm = null;
                            closeModal('actionConfirmModal');
                            if (typeof action === 'function') {
                                action();
                            }
                        };
                    }

                    openModal('actionConfirmModal');
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
                    const select = document.getElementById('banAccountSelect');
                    const accountId = String(account.account_id ?? '');
                    const accountType = String(account.account_type ?? '');

                    if (select) {
                        const matchedOption = Array.from(select.options).find(opt => {
                            if (!opt.value) return false;
                            return String(opt.getAttribute('data-id') || opt.value) === accountId &&
                                   String(opt.getAttribute('data-type') || '') === accountType;
                        });

                        if (matchedOption) {
                            select.value = matchedOption.value;
                            updateFormFieldsFromSelect();
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