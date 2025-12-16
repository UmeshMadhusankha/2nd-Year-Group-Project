<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Direct database connection
$db = new mysqli("localhost", "root", "", "fix_lanka");

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Include the model
require_once __DIR__ . '/../../models/StaticContentModel.php';
$model = new StaticContentModel($db);

// Handle status toggle (Publish/Unpublish)
if (isset($_GET['toggle_status']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $content = $model->getContentById($id);
    
    if ($content) {
        if ($content['status'] === 'Published') {
            $success = $model->unpublishContent($id);
            $_SESSION['message'] = $success ? 'Content unpublished successfully!' : 'Failed to unpublish content';
        } else {
            $success = $model->publishContent($id);
            $_SESSION['message'] = $success ? 'Content published successfully!' : 'Failed to publish content';
        }
        $_SESSION['message_type'] = 'success';
    }
    
    header('Location: /2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php');
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $id = intval($_POST['content_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $body = trim($_POST['body']);
    $status = $_POST['status'];
    
    $success = $model->updateContent($id, $title, $description, $body, $status);
    
    if ($success) {
        $_SESSION['message'] = 'Content updated successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to update content';
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: /2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php');
    exit;
}

// Get data using model
$contents = $model->getAllContent();
$stats = $model->getStatistics();

$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';

$basePath = '../..';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-static-content';
$pageTitle = 'Static Content Management';
$pageDescription = 'Edit and manage website content';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/static-content.css?v=<?php echo time(); ?>">
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
                    <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>">
                        <?= $message_type === 'success' ? '✓' : '✗' ?> <?= htmlspecialchars($message) ?>
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
                                <i data-lucide="file-text" class="h-8 w-8 text-blue-600"></i>
                            </div>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Published</p>
                                    <p class="text-2xl font-bold mt-2"><?= $stats['published'] ?></p>
                                    <p class="text-xs text-muted-foreground mt-1">Live on website</p>
                                </div>
                                <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                            </div>
                        </div>
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Unpublished</p>
                                    <p class="text-2xl font-bold mt-2"><?= $stats['drafts'] ?></p>
                                    <p class="text-xs text-muted-foreground mt-1">Not visible</p>
                                </div>
                                <i data-lucide="eye-off" class="h-8 w-8 text-orange-600"></i>
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
                                <i data-lucide="clock" class="h-8 w-8 text-purple-600"></i>
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
                                <i data-lucide="inbox" class="h-16 w-16 text-muted-foreground mx-auto mb-4"></i>
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
                                            <!-- Publish/Unpublish Toggle Button -->
                                            <?php if ($item['status'] === 'Draft'): ?>
                                            <a href="?toggle_status=1&id=<?= $item['content_id'] ?>" class="btn btn-primary">
                                                <i data-lucide="upload" class="h-4 w-4"></i>
                                                Publish
                                            </a>
                                            <?php else: ?>
                                            <a href="?toggle_status=1&id=<?= $item['content_id'] ?>" class="btn btn-secondary">
                                                <i data-lucide="archive" class="h-4 w-4"></i>
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
                                                <i data-lucide="edit" class="h-4 w-4"></i>
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
                        <i data-lucide="edit" class="h-5 w-5"></i>
                        Edit Content
                    </h3>
                    <button type="button" onclick="closeModal()" class="modal-close">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <form method="POST" action="/2nd-Year-Group-Project/FixLanka/views/moderator/static-content.php">
                    <div class="modal-body">
                        <input type="hidden" name="content_id" id="editContentId">
                        <input type="hidden" name="update_content" value="1">
                        
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
                            <i data-lucide="save" class="h-4 w-4"></i>
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
            
            setTimeout(() => {
                lucide.createIcons();
            }, 100);
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
        
        lucide.createIcons();
    </script>
</body>
</html>