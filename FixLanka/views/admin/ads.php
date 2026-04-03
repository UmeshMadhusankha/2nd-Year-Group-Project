<?php
/**
 * Advertisement Review View (ADMIN)
 * ✅ Pure MVC with PDO - External CSS Only
 * Version: 3.0.0
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: 0');
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';

try {
    // $pdo is provided by config/database.php
    require_once __DIR__ . '/../../controllers/AdminAdvertisementController.php';
    $controller = new AdminAdvertisementController($pdo);
    
    if (!$controller->checkTables()) {
        die("⛔ Advertisement table does not exist.");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->handlePostRequest();
        header("Location: /2nd-Year-Group-Project/FixLanka/views/admin/ads.php");
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
    $moderators = $viewData['moderators'];
    $message = $messages['message'];
    $messageType = $messages['type'];
    
} catch (Exception $e) {
    die("⛔ Error: " . htmlspecialchars($e->getMessage()));
}

$basePath = '';
$currentPath = 'ads';
$pageTitle = 'Advertisement Review - Admin';
$pageDescription = 'Monitor and manage submitted advertisements';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/ads.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Review', 'Monitor and manage submitted advertisements (Admin Oversight)'); ?>

            <main style="margin-top: 5rem;" class="ads-content">
                <div class="content-wrapper">
                    <?php if ($message): ?>
                    <div class="message-alert <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="grid gap-4 grid-cols-5">
                        <div class="stat-card" data-color="blue">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>TOTAL ADS</h4>
                                    <div class="stat-value"><?php echo $stats['total']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-bullhorn"></i></div>
                            </div>
                        </div>

                        <div class="stat-card" data-color="amber">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>PENDING</h4>
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

                        <div class="stat-card" data-color="purple">
                            <div class="stat-card-inner">
                                <div class="stat-info">
                                    <h4>ACTIVE</h4>
                                    <div class="stat-value"><?php echo $stats['active']; ?></div>
                                </div>
                                <div class="stat-icon"><i class="fas fa-rocket"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter & Table Card -->
                    <div class="bg-card">
                        <div class="p-6 border-b">
                            <h2 class="table-title">Advertisement Queue</h2>
                            <p class="table-subtitle">Review and manage advertisement approvals and lifecycle (Admin Oversight)</p>
                        </div>

                        <div class="p-6">
                            <form method="GET" id="filterForm" class="space-y-4">
                                <div class="grid gap-4 grid-cols-6">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Status</label>
                                        <select name="status" class="form-select w-full">
                                            <option value="">All Status</option>
                                            <option value="pending" <?php echo ($filters['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo ($filters['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                            <option value="scheduled" <?php echo ($filters['status'] === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                            <option value="rejected" <?php echo ($filters['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                            <option value="active" <?php echo ($filters['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="expired" <?php echo ($filters['status'] === 'expired') ? 'selected' : ''; ?>>Expired</option>
                                            <option value="suspended" <?php echo ($filters['status'] === 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Type</label>
                                        <select name="type" class="form-select w-full">
                                            <option value="">All Types</option>
                                            <option value="banner" <?php echo ($filters['type'] === 'banner') ? 'selected' : ''; ?>>Banner</option>
                                            <option value="featured" <?php echo ($filters['type'] === 'featured') ? 'selected' : ''; ?>>Featured</option>
                                            <option value="sponsored" <?php echo ($filters['type'] === 'sponsored') ? 'selected' : ''; ?>>Sponsored</option>
                                        </select>
                                    </div>

                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium mb-2">Moderator</label>
                                        <select name="moderator" class="form-select w-full">
                                            <option value="">All Moderators</option>
                                            <?php foreach ($moderators as $mod): ?>
                                            <option value="<?php echo $mod['moderator_id']; ?>" 
                                                <?php echo ($filters['moderator'] == $mod['moderator_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($mod['name']); ?>
                                            </option>
                                            <?php endforeach; ?>
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
                                            <th>REVIEWED BY</th>
                                            <th>SUBMITTED</th>
                                            <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($adsData)): ?>
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 2rem; color: #6b7280;">
                                                No advertisements found.
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($adsData as $ad): ?>
                                            <?php $displayStatus = $ad['computed_status'] ?? $ad['status']; ?>
                                            <tr>
                                                <td class="font-mono">#<?php echo $ad['ad_id']; ?></td>
                                                <td class="font-medium"><?php echo htmlspecialchars($ad['title']); ?></td>
                                                <td><?php echo htmlspecialchars($ad['provider_name'] ?? 'Unknown'); ?></td>
                                                <td><span class="type-badge"><?php echo ucfirst($ad['type']); ?></span></td>
                                                <td>LKR <?php echo number_format($ad['budget'], 2); ?></td>
                                                <td>
                                                    <span class="badge status-<?php echo strtolower($displayStatus); ?>">
                                                        <?php echo ucfirst($displayStatus); ?>
                                                        <?php if ($ad['is_override']): ?>
                                                        <i class="fas fa-shield-alt" title="Admin Override"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($ad['moderator_name']): ?>
                                                        <div><?php echo htmlspecialchars($ad['moderator_name']); ?></div>
                                                        <div class="reviewer-email"><?php echo htmlspecialchars($ad['moderator_email']); ?></div>
                                                    <?php else: ?>
                                                        <span style="color: #9ca3af;">Not reviewed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($ad['submission_date'])); ?></td>
                                                <td>
                                                    <a href="?review=<?php echo $ad['ad_id']; ?>" class="btn-primary btn-sm">
                                                        <i class="fas fa-eye"></i> View
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

            <!-- Admin Review Modal (match Moderator UI) -->
            <?php if ($advertisement && is_array($advertisement)): ?>
            <div id="reviewModal" class="modal-bg" role="presentation">
                <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle" tabindex="-1">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/admin/ads.php" class="close-btn" aria-label="Close dialog">×</a>

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
                            <?php $displayStatus = $advertisement['computed_status'] ?? $advertisement['status']; ?>
                            <span class="badge status-<?php echo strtolower($displayStatus); ?>">
                                <?php echo ucfirst($displayStatus); ?>
                                <?php if (!empty($advertisement['is_override'])): ?>
                                    <i class="fas fa-shield-alt" title="Admin Override"></i>
                                <?php endif; ?>
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
                        <div class="detail-row">
                            <span class="detail-label">Reviewed By</span>
                            <span class="detail-value">
                                <?php echo !empty($advertisement['moderator_name'])
                                    ? htmlspecialchars($advertisement['moderator_name']) . (!empty($advertisement['moderator_email']) ? ' (' . htmlspecialchars($advertisement['moderator_email']) . ')' : '')
                                    : 'Not yet reviewed'; ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Moderator Notes</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['moderator_notes'] ?? 'No notes'); ?></span>
                        </div>
                        <?php if (!empty($advertisement['is_override'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">Override Reason</span>
                            <span class="detail-value"><?php echo htmlspecialchars($advertisement['override_reason'] ?? 'No reason provided'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php $currentStatus = strtolower($advertisement['computed_status'] ?? $advertisement['status'] ?? ''); ?>

                    <?php if ($currentStatus === 'pending'): ?>
                    <form method="POST" class="review-form">
                        <input type="hidden" name="ad_id" value="<?php echo (int)$advertisement['ad_id']; ?>">
                        <input type="hidden" name="is_override" value="false">

                        <div class="review-notes">
                            <label class="review-notes-label" for="reviewNotes">Admin notes (optional)</label>
                            <textarea id="reviewNotes" name="reason" rows="3" placeholder="Add optional notes..." class="review-notes-input"></textarea>
                        </div>

                        <div class="modal-actions">
                            <button type="submit" name="action" value="approve" class="action-btn success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button type="submit" name="action" value="reject" class="action-btn danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                            <button type="submit" name="action" value="suspend" class="action-btn warning">
                                <i class="fas fa-ban"></i> Suspend
                            </button>
                        </div>
                    </form>

                    <?php elseif ($currentStatus === 'rejected' || $currentStatus === 'suspended'): ?>
                    <div class="review-note">
                        <strong>Override required:</strong> This advertisement is <?php echo htmlspecialchars($currentStatus); ?>. Admin override is necessary to approve.
                    </div>

                    <form method="POST" class="review-form" onsubmit="return confirm('⚠️ ADMIN OVERRIDE: Are you sure you want to override the current <?php echo $currentStatus; ?> status? This action will be logged.');">
                        <input type="hidden" name="ad_id" value="<?php echo (int)$advertisement['ad_id']; ?>">
                        <input type="hidden" name="is_override" value="true">

                        <div class="review-notes">
                            <label class="review-notes-label" for="overrideReason">Override reason (required)</label>
                            <textarea id="overrideReason" name="reason" rows="3" required placeholder="Explain why you are overriding this decision..." class="review-notes-input"></textarea>
                        </div>

                        <div class="modal-actions">
                            <button type="submit" name="action" value="approve" class="action-btn primary">
                                <i class="fas fa-shield-alt"></i> Override & Approve
                            </button>
                        </div>
                    </form>

                    <?php else: ?>
                    <div class="review-note">
                        <strong>Status:</strong> This advertisement is currently <strong><?php echo htmlspecialchars($currentStatus); ?></strong>.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js?v=<?php echo time(); ?>"></script>
    <script>
        // Auto-apply filters (Status/Type/Moderator)
        (function () {
            var form = document.getElementById('filterForm');
            if (!form) return;
            var selects = form.querySelectorAll('select[name="status"], select[name="type"], select[name="moderator"]');
            selects.forEach(function (el) {
                el.addEventListener('change', function () {
                    form.submit();
                });
            });
        })();
    </script>
</body>
</html>