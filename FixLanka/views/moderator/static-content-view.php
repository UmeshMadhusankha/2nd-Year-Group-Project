<?php
/**
 * static-content-view.php
 * PURE VIEW - Only displays data, no business logic
 * Data is passed from controller via variables: $contents, $stats, $message, $message_type
 */

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/../../controllers/StaticContentController.php';
$basePath = '../..';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-static-content';
$pageTitle = 'Static Content Management';
$pageDescription = 'Edit and manage website content';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/static-content.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Static Content Management', 'Edit and manage website content'); ?>
            
            <main class="dashboard-content">
                <div class="space-y-6">
                    <!-- Page Header -->
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Static Content Management</h2>
                        <p class="text-muted-foreground">Edit and manage website content - Changes are saved to database</p>
                    </div>

                    <!-- Success/Error Message -->
                    <?php if ($message): ?>
                    <div id="alertMessage" class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>" style="display: flex;">
                        <i class="fa-solid <?= $message_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                        <span><?= htmlspecialchars($message) ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="grid gap-4 md-grid-cols-4">
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Total Sections</p>
                                    <p class="text-2xl font-bold mt-2"><?= $stats['total'] ?></p>
                                    <p class="text-xs text-muted-foreground mt-1">All content</p>
                                </div>
                                <i class="fa-solid fa-file-lines h-8 w-8 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Published</p>
                                    <p class="text-2xl font-bold mt-2"><?= $stats['published'] ?></p>
                                    <p class="text-xs text-muted-foreground mt-1">Live on website</p>
                                </div>
                                <i class="fa-solid fa-circle-check h-8 w-8 text-green-600"></i>
                            </div>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Unpublished</p>
                                    <p class="text-2xl font-bold mt-2"><?= $stats['drafts'] ?></p>
                                    <p class="text-xs text-muted-foreground mt-1">Not visible</p>
                                </div>
                                <i class="fa-solid fa-eye-slash h-8 w-8 text-orange-600"></i>
                            </div>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Last Updated</p>
                                    <p class="text-lg font-bold mt-2">
                                        <?= $stats['last_update'] ? date('M d, Y', strtotime($stats['last_update'])) : 'Never' ?>
                                    </p>
                                </div>
                                <i class="fa-solid fa-clock h-8 w-8 text-purple-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Content Sections -->
                    <div class="bg-card rounded-lg border" style="margin-bottom: 4rem;">
                        <div class="p-6 border-b">
                            <h3 class="text-lg font-medium text-foreground">Content Sections</h3>
                            <p class="text-sm text-muted-foreground">Manage all static content sections of the website</p>
                        </div>

                        <div class="divide-y divide-border">
                            <?php if (empty($contents)): ?>
                            <div class="p-8 text-center">
                                <i class="fa-solid fa-inbox h-16 w-16 text-muted-foreground mx-auto mb-4"></i>
                                <p class="text-muted-foreground">No content sections found. Please run the SQL script to add sample data.</p>
                            </div>
                            <?php else: ?>
                                <?php foreach ($contents as $item): ?>
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-3">
                                                <h4 class="text-lg font-semibold"><?= htmlspecialchars($item['title']) ?></h4>
                                                <span class="badge <?= $item['status'] === 'Published' ? 'badge-default' : 'badge-secondary' ?>">
                                                    <?= $item['status'] ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($item['description'])): ?>
                                            <p class="text-sm text-muted-foreground mb-3"><?= htmlspecialchars($item['description']) ?></p>
                                            <?php endif; ?>
                                            <p class="text-sm text-muted-foreground mb-3">
                                                <?= htmlspecialchars(mb_substr($item['body'], 0, 150)) ?>...
                                            </p>
                                            <p class="text-xs text-muted-foreground">
                                                Last updated: <?= date('M d, Y H:i', strtotime($item['last_update'])) ?>
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <!-- ✅ Points to CONTROLLER -->
                                            <?php if ($item['status'] === 'Draft'): ?>
                                            <a href="/2nd-Year-Group-Project/FixLanka/controllers/StaticContentController.php?action=toggle_status&id=<?= $item['content_id'] ?>" 
                                               class="btn btn-primary">
                                                <i class="fa-solid fa-upload h-4 w-4"></i>
                                                Publish
                                            </a>
                                            <?php else: ?>
                                            <a href="/2nd-Year-Group-Project/FixLanka/controllers/StaticContentController.php?action=toggle_status&id=<?= $item['content_id'] ?>" 
                                               class="btn btn-secondary">
                                                <i class="fa-solid fa-box-archive h-4 w-4"></i>
                                                Unpublish
                                            </a>
                                            <?php endif; ?>
                                            
                                            <!-- Edit Button -->
                                            <button type="button" 
                                                    onclick='editContent(<?= json_encode([
                                                        "id" => $item["content_id"],
                                                        "title" => $item["title"],
                                                        "description" => $item["description"] ?? "",
                                                        "body" => $item["body"],
                                                        "status" => $item["status"]
                                                    ], JSON_HEX_QUOT | JSON_HEX_APOS) ?>)' 
                                                    class="btn btn-secondary">
                                                <i class="fa-solid fa-pen-to-square h-4 w-4"></i>
                                                Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        <i class="fa-solid fa-pen-to-square h-5 w-5"></i>
                        Edit Content
                    </h3>
                    <button type="button" onclick="closeModal()" class="modal-close">
                        <i class="fa-solid fa-xmark h-5 w-5"></i>
                    </button>
                </div>
                <!-- ✅ Form submits to CONTROLLER -->
                <form method="POST" action="/2nd-Year-Group-Project/FixLanka/controllers/StaticContentController.php">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="content_id" id="editContentId">
                        
                        <div class="form-group">
                            <label class="form-label">Section Title</label>
                            <input type="text" name="title" id="editTitle" class="form-input" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description (Optional)</label>
                            <input type="text" name="description" id="editDescription" class="form-input">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Content</label>
                            <textarea name="body" id="editContent" rows="12" class="form-textarea" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" id="editStatus" class="form-select">
                                <option value="Draft">Draft</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk h-4 w-4"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editContent(data) {
            document.getElementById('editContentId').value = data.id;
            document.getElementById('editTitle').value = data.title;
            document.getElementById('editDescription').value = data.description || '';
            document.getElementById('editContent').value = data.body;
            document.getElementById('editStatus').value = data.status;
            
            const modal = document.getElementById('editModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal() {
            const modal = document.getElementById('editModal');
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            const alertMessage = document.getElementById('alertMessage');
            if (alertMessage) {
                alertMessage.style.opacity = '0';
                alertMessage.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    alertMessage.style.display = 'none';
                }, 300);
            }
        }, 5000);
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>
</html>