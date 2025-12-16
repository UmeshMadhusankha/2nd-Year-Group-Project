<?php
/**
 * Advertisement Review View
 * Proper MVC Architecture - View Layer (Display Only)
 * NO INLINE CSS - All styles in external ads.css
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

// Database connection
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize Controller
require_once __DIR__ . '/../../controllers/AdvertisementController.php';
$controller = new AdvertisementController($conn);

// Check if table exists
if (!$controller->checkTable()) {
    die("Advertisement table does not exist. Please run create_database.sql in phpMyAdmin.");
}

// Handle POST requests (Status updates)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handlePostRequest();
    header("Location: /2nd-Year-Group-Project/FixLanka/views/moderator/ads.php");
    exit();
}

// Check if reviewing specific ad (modal view)
$reviewAdId = isset($_GET['review']) ? intval($_GET['review']) : null;
$advertisement = null;
if ($reviewAdId) {
    $advertisement = $controller->review($reviewAdId);
}

// Get data from controller
$viewData = $controller->getViewData();
$messages = $controller->getMessages();

// Extract variables
$stats = $viewData['stats'];
$adsData = $viewData['ads'];
$filters = $viewData['filters'];
$message = $messages['message'];
$messageType = $messages['type'];

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
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
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
                        <p class="text-muted-foreground">Review and manage submitted advertisements</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="<?php echo $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-600' : 'bg-red-50 border-red-200 text-red-600'; ?> border px-4 py-3 rounded">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="grid gap-4 grid-cols-4">
                        <?php
                        renderCard('Total Ads', $stats['total'], 'All submissions', 'monitor', 'blue');
                        renderCard('Pending Review', $stats['pending'], 'Awaiting approval', 'clock', 'yellow');
                        renderCard('Approved', $stats['approved'], 'Currently active', 'check-circle', 'green');
                        renderCard('Rejected', $stats['rejected'], 'Not approved', 'x-circle', 'red');
                        ?>
                    </div>

                    <div class="bg-card rounded-lg shadow border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium">Advertisement Queue</h3>
                            <p class="text-sm text-muted-foreground">Review submitted advertisements</p>

                            <!-- Filter Form -->
                            <form method="GET" action="" id="filterForm" class="mt-4 flex items-center space-x-2">
                                <div class="relative flex-1 max-w-sm">
                                    <i data-lucide="search" class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground"></i>
                                    <input type="text" name="search" placeholder="Search ads..." class="form-input pl-8" value="<?php echo htmlspecialchars($filters['search']); ?>">
                                </div>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $filters['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="rejected" <?php echo $filters['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                    <option value="active" <?php echo $filters['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                </select>
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="banner" <?php echo $filters['type'] === 'banner' ? 'selected' : ''; ?>>Banner</option>
                                    <option value="sponsored" <?php echo $filters['type'] === 'sponsored' ? 'selected' : ''; ?>>Sponsored</option>
                                    <option value="featured" <?php echo $filters['type'] === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                </select>
                                <button type="submit" class="filter-button" id="filterBtn">
                                    <i data-lucide="filter"></i>
                                    <span>Filter</span>
                                </button>
                            </form>
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
                                <tbody>
                                    <?php if (empty($adsData)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No advertisements found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($adsData as $ad): ?>
                                            <tr>
                                                <td class="font-medium">#<?php echo $ad['id']; ?></td>
                                                <td><?php echo htmlspecialchars($ad['company']); ?></td>
                                                <td class="max-w-xs truncate"><?php echo htmlspecialchars($ad['title']); ?></td>
                                                <td>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                        <?php echo $ad['type']; ?>
                                                    </span>
                                                </td>
                                                <td>LKR <?php echo $ad['budget']; ?></td>
                                                <td>
                                                    <?php
                                                    $status_lower = strtolower($ad['status']);
                                                    $status_class = 'badge status-' . $status_lower;
                                                    ?>
                                                    <span class="<?php echo $status_class; ?>"><?php echo $ad['status']; ?></span>
                                                </td>
                                                <td>
                                                    <a href="?review=<?php echo $ad['id']; ?>" class="btn btn-primary btn-sm">
                                                        <i data-lucide="eye" class="mr-1 h-3 w-3"></i>
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
            <?php if ($advertisement): ?>
            <div id="reviewModal" class="modal-bg">
                <div class="modal-box">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" class="close-btn">×</a>
                    <h2 style="margin-bottom: 20px;">Advertisement Review</h2>
                    
                    <div id="modalContent">
                        <div class="detail-row">
                            <div class="detail-label">Ad ID:</div>
                            <div class="detail-value">#<?php echo $advertisement['ad_id']; ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Company:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($advertisement['company_name'] ?? 'N/A'); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Title:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($advertisement['title']); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Type:</div>
                            <div class="detail-value">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?php echo ucfirst($advertisement['type']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Budget:</div>
                            <div class="detail-value">LKR <?php echo number_format($advertisement['budget'], 2); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Current Status:</div>
                            <div class="detail-value">
                                <?php
                                $status_lower = strtolower($advertisement['status']);
                                $status_class = 'badge status-' . $status_lower;
                                ?>
                                <span class="<?php echo $status_class; ?>"><?php echo ucfirst($advertisement['status']); ?></span>
                            </div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Submission Date:</div>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($advertisement['submission_date'])); ?></div>
                        </div>
                        
                        <div class="detail-row">
                            <div class="detail-label">Provider Type:</div>
                            <div class="detail-value"><?php echo ucfirst($advertisement['provider_type']); ?></div>
                        </div>
                    </div>
                    
                    <div class="modal-actions">
                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" style="display: contents;">
                            <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="approve-btn">✓ Approve</button>
                        </form>
                        
                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" style="display: contents;">
                            <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="activate-btn">⚡ Set Active</button>
                        </form>
                        
                        <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" style="display: contents;" onsubmit="return confirm('Are you sure you want to reject this advertisement?');">
                            <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="reject-btn">✗ Reject</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Add visual feedback when Filter button is clicked
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            var btn = document.getElementById('filterBtn');
            btn.classList.add('loading');
            btn.querySelector('span').textContent = 'Filtering...';
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>