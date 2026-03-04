<?php
/**
 * Advertisement Review View (MODERATOR)
 * ✅ Pure MVC with PDO - No inline CSS
 * Version: 3.0.0
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';

try {
    // $pdo is provided by config/database.php
    require_once __DIR__ . '/../../controllers/AdvertisementController.php';
    $controller = new AdvertisementController($pdo);
    
    if (!$controller->checkTable()) {
        die("⛔ Advertisement table does not exist.");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handlePostRequest();
        header("Location: /2nd-Year-Group-Project/FixLanka/views/moderator/ads.php");
        exit();
    }
    
    $reviewAdId = isset($_GET['review']) ? intval($_GET['review']) : null;
    $advertisement = null;
    if ($reviewAdId) {
        $advertisement = $controller->review($reviewAdId);
    }
    
    $viewData = $controller->getViewData();
    $messages = $controller->getMessages();
    
    $stats = $viewData['stats'];
    $adsData = $viewData['ads'];
    $filters = $viewData['filters'];
    $message = $messages['message'];
    $messageType = $messages['type'];
    
} catch (Exception $e) {
    die("⛔ Error: " . htmlspecialchars($e->getMessage()));
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
                    <?php if ($message): ?>
                    <div class="message-alert <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="grid gap-4 grid-cols-4">
                        <div class="stat-card" data-color="blue">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>TOTAL ADS</h4>
                                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                            </div>
                        </div>

                        <div class="stat-card" data-color="yellow">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>PENDING REVIEW</h4>
                                    <div class="stat-value"><?php echo $stats['pending']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                            </div>
                        </div>

                        <div class="stat-card" data-color="green">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>APPROVED</h4>
                                    <div class="stat-value"><?php echo $stats['approved']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                            </div>
                        </div>

                        <div class="stat-card" data-color="red">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>REJECTED</h4>
                                    <div class="stat-value"><?php echo $stats['rejected']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Table Card -->
                    <div class="bg-card rounded-lg shadow border">
                        <div class="p-6 border-b">
                            <h2 class="table-title">Advertisement Queue</h2>
                            <p class="table-subtitle">Review and manage advertisement submissions</p>
                        </div>

                        <div class="p-6">
                            <form method="GET" id="filterForm" class="space-y-4">
                                <div class="grid gap-4 grid-cols-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Status</label>
                                        <select name="status" class="form-select w-full">
                                            <option value="">All Advertisements</option>
                                            <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo ($filters['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo ($filters['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Type</label>
                                        <select name="type" class="form-select w-full">
                                            <option value="">All Types</option>
                                            <option value="banner" <?php echo ($filters['type'] === 'banner') ? 'selected' : ''; ?>>Banner</option>
                                            <option value="featured" <?php echo ($filters['type'] === 'featured') ? 'selected' : ''; ?>>Featured</option>
                                            <option value="sponsored" <?php echo ($filters['type'] === 'sponsored') ? 'selected' : ''; ?>>Sponsored</option>
                                        </select>
                                    </div>

                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium mb-2">Search</label>
                                        <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                                               placeholder="Search ads..." class="form-input w-full">
                                    </div>

                                    <div class="flex items-end">
                                        <button type="submit" id="filterBtn" class="filter-button">
                                            <i class="fas fa-filter"></i> <span>Filter</span>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Table -->
                            <div class="table-wrapper">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>TITLE</th>
                                            <th>PROVIDER</th>
                                            <th>TYPE</th>
                                            <th>BUDGET</th>
                                            <th>STATUS</th>
                                            <th>SUBMITTED</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($adsData)): ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2rem; color: #6b7280;">
                                                No advertisements found.
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($adsData as $ad): ?>
                                            <tr>
                                                <td class="font-mono">#<?php echo $ad['ad_id']; ?></td>
                                                <td class="font-medium"><?php echo htmlspecialchars($ad['title']); ?></td>
                                                <td><?php echo htmlspecialchars($ad['provider_name'] ?? 'Unknown'); ?></td>
                                                <td><span class="type-badge"><?php echo ucfirst($ad['type']); ?></span></td>
                                                <td>LKR <?php echo number_format($ad['budget'], 2); ?></td>
                                                <td>
                                                    <span class="badge status-<?php echo strtolower($ad['status']); ?>">
                                                        <?php echo ucfirst($ad['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($ad['submission_date'])); ?></td>
                                                <td>
                                                    <a href="?review=<?php echo $ad['ad_id']; ?>" class="btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> Review
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
                            <span class="detail-label">Advertisement ID:</span>
                            <span class="detail-value">#<?php echo $advertisement['ad_id']; ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Title:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['title']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Description:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['description'] ?? 'No description'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Provider:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['provider_name'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Contact Email:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['contact_email'] ?? $advertisement['provider_email'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Contact Phone:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['contact_phone'] ?? $advertisement['provider_phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value"><?php echo ucfirst($advertisement['type']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Budget:</span>
                            <span class="detail-value">LKR <?php echo number_format($advertisement['budget'], 2); ?></span>
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
                            <span class="detail-value"><?php echo date('M d, Y H:i', strtotime($advertisement['submission_date'])); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($advertisement['status'] === 'pending'): ?>
                    <form method="POST" style="margin-top: 20px;">
                        <input type="hidden" name="ad_id" value="<?php echo $advertisement['ad_id']; ?>">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 500;">Review Notes:</label>
                            <textarea name="notes" rows="3" placeholder="Add optional notes..." 
                                      style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;"></textarea>
                        </div>

                        <div class="modal-actions">
                            <button type="submit" name="action" value="approve" class="approve-btn">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="submit" name="action" value="reject" class="reject-btn">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div style="margin-top: 20px; padding: 15px; background: #f3f4f6; border-radius: 6px; color: #6b7280;">
                        <strong>Note:</strong> <?php echo $controller->getStatusExplanation($advertisement['status']); ?>
                    </div>
                    <?php endif; ?>
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