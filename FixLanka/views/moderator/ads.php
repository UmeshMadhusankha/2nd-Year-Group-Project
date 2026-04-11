<?php
/**
 * Advertisement Review View (MODERATOR)
 * ✅ Pure MVC with PDO - No inline CSS
 * Version: 3.0.0
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Reduce chances of stale cached HTML in browsers/back-button.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
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
                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium mb-2">Status</label>
                                        <select name="status" class="form-select w-full">
                                            <option value="">All Advertisements</option>
                                            <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo ($filters['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="rejected" <?php echo ($filters['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                        </select>
                                    </div>

                                    <div class="col-span-3">
                                        <label class="block text-sm font-medium mb-2">Type</label>
                                        <select name="type" class="form-select w-full">
                                            <option value="">All Types</option>
                                            <option value="banner" <?php echo ($filters['type'] === 'banner') ? 'selected' : ''; ?>>Banner</option>
                                            <option value="featured" <?php echo ($filters['type'] === 'featured') ? 'selected' : ''; ?>>Featured</option>
                                            <option value="sponsored" <?php echo ($filters['type'] === 'sponsored') ? 'selected' : ''; ?>>Sponsored</option>
                                        </select>
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
            <div id="reviewModal" class="modal-bg" role="presentation">
                <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle" tabindex="-1">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php" class="close-btn" aria-label="Close dialog">×</a>

                    <div class="review-modal-header">
                        <div class="review-modal-title">
                            <h2 id="reviewModalTitle">Advertisement Review</h2>
                            <p class="review-modal-subtitle">
                                ID #<?php echo (int)$advertisement['ad_id']; ?>
                                <span class="review-dot">•</span>
                                Submitted <?php echo date('M d, Y H:i', strtotime($advertisement['submission_date'])); ?>
                            </p>
                        </div>
                        <div class="review-modal-status">
                            <span class="badge status-<?php echo strtolower($advertisement['status']); ?>">
                                <?php echo ucfirst($advertisement['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="review-media">
                        <?php if (!empty($advertisement['image_url'])): ?>
                            <img
                                class="review-media-img"
                                src="<?php echo htmlspecialchars($advertisement['image_url']); ?>"
                                alt="Advertisement media preview"
                                loading="lazy"
                            />
                        <?php else: ?>
                            <div class="review-media-empty">
                                <i class="fa-solid fa-image"></i>
                                <div>
                                    <div class="review-media-empty-title">No media uploaded</div>
                                    <div class="review-media-empty-subtitle">This advertisement has no image.</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="modalContent" class="review-details">
                        <div class="detail-row">
                            <span class="detail-label">Title</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['title']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Description</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['description'] ?? 'No description'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Provider</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['provider_name'] ?? 'Unknown'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Contact Email</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['contact_email'] ?? $advertisement['provider_email'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Contact Phone</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['contact_phone'] ?? $advertisement['provider_phone'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Type</span>
                            <span class="detail-value"><?php echo ucfirst($advertisement['type']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Budget</span>
                            <span class="detail-value">LKR <?php echo number_format((float)$advertisement['budget'], 2); ?></span>
                        </div>
                    </div>
                    
                    <?php if (($advertisement['status'] ?? '') === 'pending'): ?>
                    <form method="POST" class="review-form">
                        <input type="hidden" name="ad_id" value="<?php echo (int)$advertisement['ad_id']; ?>">

                        <div class="review-notes">
                            <label class="review-notes-label" for="reviewNotes">Review notes (required for rejection)</label>
                            <textarea id="reviewNotes" name="notes" rows="3" placeholder="Add notes for the provider..." class="review-notes-input"></textarea>
                            <div id="reviewNotesError" class="review-notes-error" aria-live="polite"></div>
                        </div>

                        <div class="modal-actions">
                            <button type="submit" name="action" value="approve" class="action-btn success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="submit" name="action" value="reject" class="action-btn danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="review-note">
                        <strong>Note:</strong> <?php echo htmlspecialchars($controller->getStatusExplanation($advertisement['status'] ?? '')); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // Auto-apply filters (Status/Type)
        (function () {
            var form = document.getElementById('filterForm');
            if (!form) return;
            var selects = form.querySelectorAll('select[name="status"], select[name="type"]');
            selects.forEach(function (el) {
                el.addEventListener('change', function () {
                    form.submit();
                });
            });
        })();


        // Require notes when rejecting
        (function () {
            var form = document.querySelector('form.review-form');
            if (!form) return;

            var notes = document.getElementById('reviewNotes');
            var errorEl = document.getElementById('reviewNotesError');
            if (!notes || !errorEl) return;

            function clearError() {
                notes.classList.remove('is-invalid');
                errorEl.textContent = '';
                errorEl.style.display = 'none';
            }

            notes.addEventListener('input', function () {
                if (notes.value.trim() !== '') {
                    clearError();
                }
            });

            form.addEventListener('submit', function (e) {
                var submitter = e.submitter || document.activeElement;
                var action = submitter && submitter.getAttribute ? submitter.getAttribute('value') : '';

                if (action !== 'reject') return;

                if (notes.value.trim() === '') {
                    e.preventDefault();
                    notes.classList.add('is-invalid');
                    errorEl.textContent = 'Please add a short reason to reject this advertisement.';
                    errorEl.style.display = 'block';
                    notes.focus();
                }
            });
        })();
    </script>
</body>
</html>