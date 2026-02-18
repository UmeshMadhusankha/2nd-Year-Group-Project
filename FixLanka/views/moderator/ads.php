<?php
/**
 * Advertisement Review View
 * ✅ 3-STATUS SYSTEM: pending, approved, rejected
 * ✅ XAMPP CRASH-PROOF
 * Version: 2.0.0
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

// ✅ CRASH PROTECTION
ini_set('memory_limit', '256M');
set_time_limit(30);

// Database connection
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        die("⛔ Database connection failed. Please check if XAMPP MySQL is running.");
    }
    
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    
} catch (Exception $e) {
    die("⛔ Critical error: " . htmlspecialchars($e->getMessage()));
}

// Initialize Controller
require_once __DIR__ . '/../../controllers/AdvertisementController.php';

try {
    $controller = new AdvertisementController($conn);
    
    if (!$controller->checkTable()) {
        die("⛔ Advertisement table does not exist. Please run create_database.sql.");
    }
    
    // Handle POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handlePostRequest();
        header("Location: /2nd-Year-Group-Project/FixLanka/views/moderator/ads.php");
        exit();
    }
    
    // Check if reviewing specific ad
    $reviewAdId = isset($_GET['review']) ? intval($_GET['review']) : null;
    $advertisement = null;
    if ($reviewAdId) {
        $advertisement = $controller->review($reviewAdId);
    }
    
    // Get data
    $viewData = $controller->getViewData();
    $messages = $controller->getMessages();
    
    $stats = $viewData['stats'];
    $adsData = $viewData['ads'];
    $filters = $viewData['filters'];
    $message = $messages['message'];
    $messageType = $messages['type'];
    
} catch (Exception $e) {
    die("⛔ Application error: " . htmlspecialchars($e->getMessage()));
}

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-ads';
$pageTitle = 'Advertisement Review - FixLanka';
$pageDescription = 'Review and manage submitted advertisements';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/ads.css?v=<?php echo time(); ?>">
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Review', 'Review and manage submitted advertisements'); ?>

            <main style="margin-top: 5rem;" class="ads-content">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Advertisement Review</h2>
                        <p class="text-muted-foreground">Review and manage submitted advertisements (3-Status System)</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="<?php echo $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-600' : 'bg-red-50 border-red-200 text-red-600'; ?> border px-4 py-3 rounded">
                            <strong><?php echo $messageType === 'success' ? '✅ Success: ' : '❌ Error: '; ?></strong>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards (3 cards only) -->
                    <div class="grid gap-4 grid-cols-4">
                        <?php
                        renderCard('Total Ads', $stats['total'], 'All submissions', 'monitor', 'blue');
                        renderCard('Pending Review', $stats['pending'], 'Awaiting approval', 'clock', 'yellow');
                        renderCard('Approved', $stats['approved'], 'Ready for scheduling', 'check-circle', 'green');
                        renderCard('Rejected', $stats['rejected'], 'Not approved', 'x-circle', 'red');
                        ?>
                    </div>

                    <div class="bg-card rounded-lg shadow border">
                        <div class="p-6">
                            <form method="GET" action="" id="filterForm" class="space-y-4">
                                <div class="grid gap-4 grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Status</label>
                                        <select name="status" class="form-select w-full">
                                            <option value="">All Advertisements</option>
                                            <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $filters['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo $filters['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Type</label>
                                        <select name="type" class="form-select w-full">
                                            <option value="">All Types</option>
                                            <option value="banner" <?php echo $filters['type'] === 'banner' ? 'selected' : ''; ?>>Banner</option>
                                            <option value="sidebar" <?php echo $filters['type'] === 'sidebar' ? 'selected' : ''; ?>>Sidebar</option>
                                            <option value="popup" <?php echo $filters['type'] === 'popup' ? 'selected' : ''; ?>>Popup</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Search</label>
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                                               placeholder="Search ads..." class="form-input w-full">
                                    </div>
                                    
                                    <div class="flex items-end">
                                        <button type="submit" id="filterBtn" class="filter-button">
                                            <i class="fa-solid fa-filter w-4 h-4"></i>
                                            <span>Filter</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Provider</th>
                                        <th>Type</th>
                                        <th>Budget</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($adsData)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center py-8 text-muted-foreground">
                                                No advertisements found
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($adsData as $ad): ?>
                                            <tr>
                                                <td class="font-mono text-sm">#<?php echo $ad['ad_id']; ?></td>
                                                <td class="font-medium"><?php echo htmlspecialchars($ad['title']); ?></td>
                                                <td><?php echo htmlspecialchars($ad['company_name']); ?></td>
                                                <td>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <?php echo ucfirst($ad['type']); ?>
                                                    </span>
                                                </td>
                                                <td class="font-mono">LKR <?php echo number_format($ad['budget'], 2); ?></td>
                                                <td>
                                                    <span class="badge status-<?php echo strtolower($ad['status']); ?>">
                                                        <?php echo ucfirst($ad['status']); ?>
                                                    </span>
                                                </td>
                                                <td class="text-sm text-muted-foreground">
                                                    <?php echo date('M d, Y', strtotime($ad['submission_date'])); ?>
                                                </td>
                                                <td>
                                                    <a href="?review=<?php echo $ad['ad_id']; ?>" class="btn-primary btn-sm">
                                                        <i class="fa-solid fa-eye w-4 h-4 mr-1"></i>
                                                        Review
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Review Modal -->
            <?php if ($advertisement && is_array($advertisement)): ?>
            <div id="reviewModal" class="modal-bg">
                <div class="modal-box">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" class="close-btn">×</a>
                    <h2 style="margin-bottom: 20px;">Advertisement Review</h2>
                    
                    <div id="modalContent">
                        <div class="detail-row">
                            <span class="detail-label">Ad ID:</span>
                            <span class="detail-value">#<?php echo $advertisement['ad_id']; ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Title:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['title']); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Provider:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['company_name']); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo ucfirst($advertisement['type']); ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Budget:</span>
                            <span class="detail-value font-mono">LKR <?php echo number_format($advertisement['budget'], 2); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="badge status-<?php echo strtolower($advertisement['status']); ?>">
                                    <?php echo ucfirst($advertisement['status']); ?>
                                </span>
                            </span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Submitted:</span>
                            <span class="detail-value"><?php echo date('F d, Y \a\t g:i A', strtotime($advertisement['submission_date'])); ?></span>
                        </div>
                        
                        <div class="detail-row">
                            <span class="detail-label">Description:</span>
                            <span class="detail-value"><?php echo nl2br(htmlspecialchars($advertisement['description'] ?? 'No description provided.')); ?></span>
                        </div>
                    </div>
                    
                    <!-- Status-Based Actions -->
                    <div class="modal-actions">
                        <?php 
                        $currentStatus = strtolower($advertisement['status']);
                        $allowedActions = $controller->getAllowedActions($advertisement);
                        ?>
                        
                        <?php if ($currentStatus === 'pending'): ?>
                            <!-- Pending: Show Approve/Reject buttons -->
                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('✅ Are you sure you want to APPROVE this advertisement?');">
                                <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="approve-btn">
                                    <i class="fa-solid fa-circle-check w-5 h-5"></i>
                                    Approve
                                </button>
                            </form>
                            
                            <form method="POST" action="" style="display: inline;" onsubmit="return confirm('❌ Are you sure you want to REJECT this advertisement? This action is FINAL.');">
                                <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="reject-btn">
                                    <i class="fa-solid fa-circle-xmark w-5 h-5"></i>
                                    Reject
                                </button>
                            </form>
                            
                        <?php elseif ($currentStatus === 'approved'): ?>
                            <!-- Approved: Show "Go to Scheduling" button -->
                            <div style="text-align: center; width: 100%;">
                                <p style="color: #10b981; font-weight: 600; margin-bottom: 15px;">
                                    ✓ This advertisement has been approved and is ready for scheduling.
                                </p>
                                <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php" 
                                   class="approve-btn" style="text-decoration: none;">
                                    <i class="fa-solid fa-calendar w-5 h-5"></i>
                                    Go to Ad Scheduling
                                </a>
                            </div>
                            
                        <?php elseif ($currentStatus === 'rejected'): ?>
                            <!-- Rejected: Show info message -->
                            <div style="text-align: center; width: 100%;">
                                <p style="color: #ef4444; font-weight: 600; margin-bottom: 15px;">
                                    ✗ This advertisement has been rejected and cannot be modified.
                                </p>
                                <p style="color: #6b7280; font-size: 0.875rem;">
                                    Rejected ads are FINAL and cannot be changed.
                                </p>
                            </div>
                            
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('filterBtn');
            btn.classList.add('loading');
            btn.querySelector('span').textContent = 'Filtering...';
        });
    </script>
</body>
</html>
<?php 
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>