<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$db = new mysqli("localhost", "root", "", "fix_lanka");

if ($db->connect_error) {
    die("<h1 style='color:red'>Database Error: " . $db->connect_error . "</h1>");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $id = intval($_POST['content_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $body = trim($_POST['body']);
    $status = $_POST['status'];
    
    $sql = "UPDATE StaticContent SET title = ?, description = ?, body = ?, status = ? WHERE content_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssssi", $title, $description, $body, $status, $id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = 'Content updated successfully!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Update failed!';
        $_SESSION['message_type'] = 'error';
    }
    
    header('Location: static-content-working.php');
    exit;
}

// Get all content
$contents = [];
$result = $db->query("SELECT * FROM StaticContent ORDER BY last_update DESC");
if ($result) {
    $contents = $result->fetch_all(MYSQLI_ASSOC);
}

// Get statistics
$statsResult = $db->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Published' THEN 1 ELSE 0 END) as published,
    SUM(CASE WHEN status = 'Draft' THEN 1 ELSE 0 END) as drafts,
    MAX(last_update) as last_update
    FROM StaticContent");
$stats = $statsResult->fetch_assoc();

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message'], $_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Static Content Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-card h3 { font-size: 14px; color: #666; margin-bottom: 10px; }
        .stat-card .number { font-size: 32px; font-weight: bold; color: #333; }
        .message { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .message.success { background: #d1fae5; color: #065f46; }
        .message.error { background: #fee2e2; color: #991b1b; }
        .content-item { background: #fff; border: 1px solid #dee2e6; padding: 20px; margin-bottom: 15px; border-radius: 8px; }
        .content-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
        .content-title { font-size: 18px; font-weight: 600; color: #333; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .status-badge.published { background: #d1fae5; color: #065f46; }
        .status-badge.draft { background: #fef3c7; color: #92400e; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; padding: 30px; border-radius: 8px; max-width: 600px; width: 90%; }
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: 500; }
        .form-input, .form-textarea, .form-select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .form-textarea { resize: vertical; font-family: Arial, sans-serif; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Static Content Management</h1>
        <p class="subtitle">Edit and manage website content</p>

        <?php if ($message): ?>
        <div class="message <?= $message_type ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Sections</h3>
                <div class="number"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Published</h3>
                <div class="number"><?= $stats['published'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Drafts</h3>
                <div class="number"><?= $stats['drafts'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Last Updated</h3>
                <div class="number" style="font-size: 14px;">
                    <?= $stats['last_update'] ? date('M d, Y', strtotime($stats['last_update'])) : 'Never' ?>
                </div>
            </div>
        </div>

        <h2 style="margin-bottom: 20px;">Content Sections</h2>

        <?php foreach ($contents as $item): ?>
        <div class="content-item">
            <div class="content-header">
                <div>
                    <span class="content-title"><?= htmlspecialchars($item['title']) ?></span>
                    <span class="status-badge <?= strtolower($item['status']) ?>">
                        <?= $item['status'] ?>
                    </span>
                </div>
                <button onclick="edit(<?= $item['content_id'] ?>, '<?= addslashes($item['title']) ?>', '<?= addslashes($item['description'] ?? '') ?>', '<?= addslashes($item['body']) ?>', '<?= $item['status'] ?>')" class="btn btn-primary">
                    Edit
                </button>
            </div>
            <?php if ($item['description']): ?>
            <p style="color: #666; font-size: 14px; margin-bottom: 10px;"><?= htmlspecialchars($item['description']) ?></p>
            <?php endif; ?>
            <p style="color: #666; font-size: 14px;"><?= htmlspecialchars(substr($item['body'], 0, 150)) ?>...</p>
            <p style="color: #999; font-size: 12px; margin-top: 10px;">
                Last updated: <?= date('M d, Y', strtotime($item['last_update'])) ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 20px;">Edit Content</h2>
            <form method="POST">
                <input type="hidden" name="content_id" id="editId">
                <input type="hidden" name="update_content" value="1">
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="editTitle" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" id="editDescription" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Content</label>
                    <textarea name="body" id="editBody" rows="8" class="form-textarea" required></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="editStatus" class="form-select">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function edit(id, title, description, body, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            document.getElementById('editBody').value = body;
            document.getElementById('editStatus').value = status;
            document.getElementById('modal').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('modal').classList.remove('show');
        }
        
        document.getElementById('modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>