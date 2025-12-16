<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include required files
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/IssueReportController.php';

// Initialize controller
global $pdo;
$controller = new IssueReportController($pdo);

// Handle POST requests
$controller->handlePostRequest();

// Get data from database
$issues = $controller->getIssues();
$stats = $controller->getStatistics();
$messageData = $controller->getMessage();

$basePath = '../..';
$currentPath = 'issues';
$pageTitle = 'Issues & Reports - FixLanka Admin';
$pageDescription = 'System-wide issue management and oversight';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/issues.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-background text-foreground">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Issues & Reports', 'System-wide issue management and oversight'); ?>

            <main style="margin-top: 2rem;" class="dashboard-content">
                
                <!-- Success/Error Message -->
                <?php if ($messageData): ?>
                <div class="alert alert-<?php echo $messageData['type']; ?>" style="margin-bottom: 2rem; padding: 1rem; border-radius: 8px; background: <?php echo $messageData['type'] === 'success' ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo $messageData['type'] === 'success' ? '#065f46' : '#991b1b'; ?>; border: 1px solid <?php echo $messageData['type'] === 'success' ? '#10b981' : '#ef4444'; ?>;">
                    <?php echo htmlspecialchars($messageData['message']); ?>
                </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Issues & Reports</h2>
                        <p class="text-muted-foreground">Manage and resolve user-reported issues and complaints</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="issues-stats-grid">
                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Total Issues</p>
                                    <p class="text-2xl font-bold mt-2"><?php echo $stats['total']; ?></p>
                                </div>
                                <i data-lucide="message-square" class="h-8 w-8 text-blue-600"></i>
                            </div>
                        </div>

                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Pending</p>
                                    <p class="text-2xl font-bold mt-2"><?php echo $stats['pending']; ?></p>
                                </div>
                                <i data-lucide="clock" class="h-8 w-8 text-yellow-600"></i>
                            </div>
                        </div>

                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">In Progress</p>
                                    <p class="text-2xl font-bold mt-2"><?php echo $stats['investigating']; ?></p>
                                </div>
                                <i data-lucide="alert-triangle" class="h-8 w-8 text-orange-600"></i>
                            </div>
                        </div>

                        <div class="bg-card rounded-lg border p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Resolved</p>
                                    <p class="text-2xl font-bold mt-2"><?php echo $stats['resolved']; ?></p>
                                </div>
                                <i data-lucide="check-circle" class="h-8 w-8 text-green-600"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="issues-table-container">
                        <div class="issues-table-header">
                            <h3 class="text-lg font-semibold">All Issues</h3>
                            
                            <form method="GET" class="flex gap-4">
                                <input 
                                    type="text" 
                                    name="search" 
                                    placeholder="Search issues..." 
                                    value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                    class="px-4 py-2 border rounded-lg"
                                >
                                
                                <select name="status" class="px-4 py-2 border rounded-lg">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo ($_GET['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="investigating" <?php echo ($_GET['status'] ?? '') === 'investigating' ? 'selected' : ''; ?>>Investigating</option>
                                    <option value="resolved" <?php echo ($_GET['status'] ?? '') === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    <option value="escalated" <?php echo ($_GET['status'] ?? '') === 'escalated' ? 'selected' : ''; ?>>Escalated</option>
                                </select>

                                <select name="priority" class="px-4 py-2 border rounded-lg">
                                    <option value="">All Priorities</option>
                                    <option value="low" <?php echo ($_GET['priority'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                                    <option value="medium" <?php echo ($_GET['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                    <option value="high" <?php echo ($_GET['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                                </select>

                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Filter
                                </button>
                            </form>
                        </div>

                        <!-- Issues Table -->
                        <?php if (count($issues) === 0): ?>
                            <div class="p-8 text-center">
                                <i data-lucide="inbox" class="h-12 w-12 mx-auto text-muted-foreground"></i>
                                <p class="mt-4 text-muted-foreground">No issues found</p>
                            </div>
                        <?php else: ?>
                            <table class="issues-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>USER</th>
                                        <th>ISSUE DESCRIPTION</th>
                                        <th>PRIORITY</th>
                                        <th>STATUS</th>
                                        <th>DATE</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($issues as $issue): ?>
                                    <tr>
                                        <td class="text-card-foreground font-medium">#<?php echo $issue['issue_id']; ?></td>
                                        <td class="text-muted-foreground"><?php echo htmlspecialchars($issue['reporter_name'] ?? 'Unknown'); ?></td>
                                        <td class="text-muted-foreground issues-description"><?php echo htmlspecialchars(substr($issue['description'], 0, 80)) . '...'; ?></td>
                                        <td>
                                            <?php
                                            $priorityColors = [
                                                'low' => 'badge-secondary',
                                                'medium' => 'badge-outline',
                                                'high' => 'badge-destructive'
                                            ];
                                            $priorityClass = $priorityColors[$issue['priority']] ?? 'badge-outline';
                                            ?>
                                            <span class="badge <?php echo $priorityClass; ?>"><?php echo ucfirst($issue['priority']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'pending' => 'badge-outline',
                                                'investigating' => 'badge-secondary',
                                                'resolved' => 'badge-default',
                                                'escalated' => 'badge-destructive'
                                            ];
                                            $statusClass = $statusColors[$issue['status']] ?? 'badge-outline';
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($issue['status']); ?></span>
                                        </td>
                                        <td class="text-muted-foreground"><?php echo date('M d, Y', strtotime($issue['created_at'])); ?></td>
                                        <td>
                                            <button onclick="openManageModal(<?php echo $issue['issue_id']; ?>)" class="issues-resolve-btn">
                                                Manage
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- Manage Issue Modal -->
            <div id="manageModal" class="issues-modal" style="display: none;">
                <div class="issues-modal-content">
                    <div class="issues-modal-header">
                        <h3 class="text-lg font-semibold">Manage Issue</h3>
                        <p class="text-muted-foreground text-sm">Update issue status and priority</p>
                        <button onclick="closeManageModal()" class="modal-close-btn">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>

                    <div id="issueDetailsContent" class="p-6 border-b">
                        <!-- Issue details will be loaded here by JavaScript -->
                    </div>

                    <form method="POST" action="" class="issues-modal-form">
                        <input type="hidden" name="action" value="update_issue">
                        <input type="hidden" name="issue_id" id="modalIssueId">

                        <div class="grid grid-cols-2 gap-4">
                            <div class="issues-form-group">
                                <label>Status</label>
                                <select name="status" id="modalStatus" required>
                                    <option value="pending">Pending</option>
                                    <option value="investigating">Investigating</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="escalated">Escalated</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div class="issues-form-group">
                                <label>Priority</label>
                                <select name="priority" id="modalPriority" required>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>

                        <div class="issues-form-actions">
                            <button type="submit" class="btn btn-primary">
                                Update Issue
                            </button>
                            <button type="button" onclick="deleteIssueAction()" class="btn btn-danger">
                                Delete Issue
                            </button>
                            <button type="button" onclick="deleteUserAction()" class="btn btn-danger">
                                Delete User Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hidden forms for delete actions -->
            <form id="deleteIssueForm" method="POST" style="display: none;">
                <input type="hidden" name="action" value="delete_issue">
                <input type="hidden" name="issue_id" id="deleteIssueId">
            </form>

            <form id="deleteUserForm" method="POST" style="display: none;">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="user_id" id="deleteUserId">
            </form>

            <script>
                // Store issues data in JavaScript
                const issuesData = <?php echo json_encode($issues); ?>;

                function openManageModal(issueId) {
                    const issue = issuesData.find(i => i.issue_id == issueId);
                    if (!issue) return;

                    // Populate modal
                    document.getElementById('modalIssueId').value = issueId;
                    document.getElementById('modalStatus').value = issue.status;
                    document.getElementById('modalPriority').value = issue.priority;
                    document.getElementById('deleteIssueId').value = issueId;
                    document.getElementById('deleteUserId').value = issue.reportedBy_id;

                    // Show issue details
                    const detailsHTML = `
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Reporter</label>
                                <p class="text-foreground">${issue.reporter_name || 'Unknown'}</p>
                                <p class="text-sm text-muted-foreground">${issue.reporter_email || ''}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Type</label>
                                <p class="text-foreground">${issue.target_type}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-muted-foreground">Description</label>
                                <p class="text-foreground">${issue.description}</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('issueDetailsContent').innerHTML = detailsHTML;

                    // Show modal
                    document.getElementById('manageModal').style.display = 'flex';
                    lucide.createIcons();
                }

                function closeManageModal() {
                    document.getElementById('manageModal').style.display = 'none';
                }

                function deleteIssueAction() {
                    if (confirm('Are you sure you want to delete this issue? This action cannot be undone.')) {
                        document.getElementById('deleteIssueForm').submit();
                    }
                }

                function deleteUserAction() {
                    if (confirm('Are you sure you want to delete this user account? This will permanently delete their account and all related data.')) {
                        document.getElementById('deleteUserForm').submit();
                    }
                }

                // Close modal when clicking outside
                window.onclick = function(event) {
                    const modal = document.getElementById('manageModal');
                    if (event.target == modal) {
                        closeManageModal();
                    }
                }

                // Initialize Lucide icons
                lucide.createIcons();
            </script>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>

</html>