<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/IssueReportController.php';

// ✅ FIXED: Proper PDO initialization
try {
    $pdo = getDatabaseConnection();
    $controller = new IssueReportController($pdo);
    $controller->handlePostRequest();
} catch (Exception $e) {
    die("⛔ Database Error: " . htmlspecialchars($e->getMessage()));
}
$issues      = $controller->getIssues();
$stats       = $controller->getStatistics();
$messageData = $controller->getMessage();

$basePath        = '../..';
$currentPath     = 'issues';
$pageTitle       = 'Issues & Reports - FixLanka Admin';
$pageDescription = 'System-wide issue management and oversight';

function getIssueType($description) {
    $d = strtolower($description);
    if (strpos($d,'payment')!==false||strpos($d,'refund')!==false)       return 'Payment Issue';
    if (strpos($d,'behavior')!==false||strpos($d,'harassment')!==false)  return 'Behavior Issue';
    if (strpos($d,'advertisement')!==false)                               return 'Advertisement Issue';
    if (strpos($d,'quality')!==false||strpos($d,'service')!==false)      return 'Service Quality';
    if (strpos($d,'project')!==false||strpos($d,'timeline')!==false)     return 'Project Issue';
    return 'General Issue';
}
function formatTargetType($t) { return ucfirst($t); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/issues.css?v=<?php echo time(); ?>">
</head>
<body class="bg-background text-foreground">

<input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

<div class="dashboard-container">
    <?php renderAdminSidebar($currentPath, $basePath); ?>

    <div class="dashboard-main">
        <?php renderPageHeader($basePath, 'Issues & Reports', 'System-wide issue management and oversight'); ?>

        <main class="dashboard-content">

            <?php if ($messageData): ?>
            <div class="alert alert-<?php echo $messageData['type']; ?>">
                <div class="alert-content">
                    <i class="fa-solid <?php echo $messageData['type']==='success'?'fa-circle-check':'fa-circle-exclamation'; ?> alert-icon"></i>
                    <span><?php echo htmlspecialchars($messageData['message']); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="alert-close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php endif; ?>

            <div class="space-y-6">

                <!-- Page Title -->
                <div class="page-header">
                    <h2>Issues &amp; Reports</h2>
                    <p>System-wide issue management and oversight</p>
                </div>

                <!-- Stat Cards -->
                <div class="issues-stats-grid">
                    <div class="stat-card stat-card-blue">
                        <div class="stat-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                        <div class="stat-content">
                            <h3>Total Issues</h3>
                            <span class="stat-value"><?php echo $stats['total']; ?></span>
                            <p>All reported issues</p>
                        </div>
                    </div>
                    <div class="stat-card stat-card-yellow">
                        <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                        <div class="stat-content">
                            <h3>Pending</h3>
                            <span class="stat-value"><?php echo $stats['pending']; ?></span>
                            <p>Awaiting review</p>
                        </div>
                    </div>
                    <div class="stat-card stat-card-orange">
                        <div class="stat-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <div class="stat-content">
                            <h3>Investigating</h3>
                            <span class="stat-value"><?php echo $stats['investigating']; ?></span>
                            <p>Under investigation</p>
                        </div>
                    </div>
                    <div class="stat-card stat-card-green">
                        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-content">
                            <h3>Resolved</h3>
                            <span class="stat-value"><?php echo $stats['resolved']; ?></span>
                            <p>Successfully resolved</p>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="issues-table-container">

                    <!-- Header + Filters -->
                    <div class="issues-table-header">
                        <div>
                            <h3>Reported Issues</h3>
                            <p>View and manage all issue reports</p>
                        </div>
                        <div class="issues-filters">
                            <div class="issues-search-box">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="issueSearch" placeholder="Search issues...">
                            </div>
                            <select class="issues-filter-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                                <option value="dismissed">Dismissed</option>
                            </select>
                            <select class="issues-filter-select" id="priorityFilter">
                                <option value="">All Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                            <select class="issues-filter-select" id="targetFilter">
                                <option value="">All Types</option>
                                <option value="user">User</option>
                                <option value="repairer">Repairer</option>
                                <option value="company">Company</option>
                            </select>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="issues-table-wrapper">
                        <table class="issues-table">
                            <thead>
                                <tr>
                                    <th>Issue ID</th>
                                    <th>Reporter</th>
                                    <th>Target</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="issuesTableBody">
                            <?php if (empty($issues)): ?>
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <i class="fa-solid fa-inbox"></i>
                                            <p>No issues found</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($issues as $issue):
                                    $issueType  = getIssueType($issue['description']);
                                    $targetType = formatTargetType($issue['target_type']);
                                ?>
                                <tr>
                                    <td><span class="issue-id">#<?php echo $issue['issue_id']; ?></span></td>
                                    <td>
                                        <div class="reporter-info">
                                            <span class="name"><?php echo htmlspecialchars($issue['reporter_name']); ?></span>
                                            <span class="email"><?php echo htmlspecialchars($issue['reporter_email']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="target-info">
                                            <span class="name"><?php echo htmlspecialchars($issue['target_name']); ?></span>
                                            <span class="type"><?php echo $targetType; ?></span>
                                        </div>
                                    </td>
                                    <td><span class="issues-type-badge"><?php echo $issueType; ?></span></td>
                                    <td>
                                        <p class="issue-description">
                                            <?php echo htmlspecialchars(substr($issue['description'],0,100)).(strlen($issue['description'])>100?'...':''); ?>
                                        </p>
                                    </td>
                                    <td>
                                        <span class="issues-priority-badge priority-<?php echo strtolower($issue['priority']); ?>">
                                            <?php echo ucfirst($issue['priority']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="issues-status-badge status-<?php echo strtolower($issue['status']); ?>">
                                            <?php echo ucfirst($issue['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($issue['created_at'])); ?></td>
                                    <td>
                                        <button onclick="openManageModal(<?php echo $issue['issue_id']; ?>)" class="issues-manage-btn">
                                            <i class="fa-solid fa-gear"></i> Manage
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.issues-table-wrapper -->

                </div>
                <!-- /.issues-table-container -->

            </div>
        </main>

        <!-- MODAL -->
        <div id="manageModal" class="modal-overlay">
            <div class="modal-dialog">
                <div class="modal-header">
                    <h3 class="modal-title"><i class="fa-solid fa-gear"></i> Manage Issue</h3>
                    <button type="button" onclick="closeManageModal()" class="modal-close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_issue">
                        <input type="hidden" name="issue_id" id="modalIssueId">

                        <div class="modal-section">
                            <h4 class="modal-section-title"><i class="fa-solid fa-user"></i> Reporter Information</h4>
                            <div id="reporterInfo" class="info-grid"></div>
                        </div>

                        <div class="modal-section">
                            <h4 class="modal-section-title"><i class="fa-solid fa-clipboard-list"></i> Issue Details</h4>
                            <div id="issueDetails" class="info-grid"></div>
                        </div>

                        <div class="modal-section">
                            <h4 class="modal-section-title"><i class="fa-solid fa-pen-to-square"></i> Update Issue</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="modalStatus" class="form-select" required>
                                        <option value="pending">Pending</option>
                                        <option value="investigating">Investigating</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="dismissed">Dismissed</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" id="modalPriority" class="form-select" required>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Admin Notes (Visible to Reporter)</label>
                                <textarea name="admin_notes" id="modalAdminNotes" class="form-textarea" placeholder="Notes visible to reporter..."></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Internal Notes (Private)</label>
                                <textarea name="internal_notes" id="modalInternalNotes" class="form-textarea" placeholder="Internal notes (admins only)..."></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" onclick="confirmDeleteIssue()" class="btn btn-danger">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                            <div class="modal-footer-right">
                                <button type="button" onclick="closeManageModal()" class="btn btn-secondary">
                                    <i class="fa-solid fa-xmark"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <form id="deleteIssueForm" method="POST" style="display:none;">
            <input type="hidden" name="action" value="delete_issue">
            <input type="hidden" name="issue_id" id="deleteIssueId">
            <input type="hidden" name="confirm_delete" value="yes">
        </form>

    </div><!-- /.dashboard-main -->
</div><!-- /.dashboard-container -->

<script>
const issuesData = <?php echo json_encode($issues); ?>;

function openManageModal(issueId) {
    const issue = issuesData.find(i => i.issue_id == issueId);
    if (!issue) return;
    document.getElementById('modalIssueId').value    = issueId;
    document.getElementById('modalStatus').value     = issue.status;
    document.getElementById('modalPriority').value   = issue.priority;
    document.getElementById('deleteIssueId').value   = issueId;
    document.getElementById('modalAdminNotes').value    = issue.admin_notes || '';
    document.getElementById('modalInternalNotes').value = issue.admin_internal_notes || '';

    document.getElementById('reporterInfo').innerHTML = `
        <div class="info-item">
            <span class="info-label">Name</span>
            <span class="info-value">${esc(issue.reporter_name)}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value">${esc(issue.reporter_email)}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Date Reported</span>
            <span class="info-value">${fmtDate(issue.created_at)}</span>
        </div>`;

    document.getElementById('issueDetails').innerHTML = `
        <div class="info-item info-full">
            <span class="info-label">Target</span>
            <span class="info-value">${esc(issue.target_name)} (${cap(issue.target_type)})</span>
        </div>
        <div class="info-item info-full">
            <span class="info-label">Issue Type</span>
            <span class="info-value">${issueTypeJS(issue.description)}</span>
        </div>
        <div class="info-item info-full">
            <span class="info-label">Description</span>
            <span class="info-value">${esc(issue.description)}</span>
        </div>
        ${issue.evidence ? `<div class="info-item info-full"><span class="info-label">Evidence</span><span class="info-value">${esc(issue.evidence)}</span></div>` : ''}`;

    document.getElementById('manageModal').style.display = 'flex';
}

function closeManageModal() {
    document.getElementById('manageModal').style.display = 'none';
}

function confirmDeleteIssue() {
    if (confirm('Delete this issue permanently?')) {
        document.getElementById('deleteIssueForm').submit();
    }
}

window.onclick = e => { if (e.target === document.getElementById('manageModal')) closeManageModal(); };
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeManageModal(); });

function issueTypeJS(d) {
    d = d.toLowerCase();
    if (d.includes('payment')||d.includes('refund'))     return 'Payment Issue';
    if (d.includes('behavior')||d.includes('harassment')) return 'Behavior Issue';
    if (d.includes('advertisement'))                      return 'Advertisement Issue';
    if (d.includes('quality')||d.includes('service'))    return 'Service Quality';
    if (d.includes('project')||d.includes('timeline'))   return 'Project Issue';
    return 'General Issue';
}
function esc(t) { const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }
function cap(s) { return s.charAt(0).toUpperCase()+s.slice(1); }
function fmtDate(s) { return new Date(s).toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'}); }

// Live filter
['issueSearch','statusFilter','priorityFilter','targetFilter'].forEach(id => {
    document.getElementById(id).addEventListener(id==='issueSearch'?'input':'change', filterRows);
});

function filterRows() {
    const s  = document.getElementById('issueSearch').value.toLowerCase();
    const st = document.getElementById('statusFilter').value.toLowerCase();
    const pr = document.getElementById('priorityFilter').value.toLowerCase();
    const tg = document.getElementById('targetFilter').value.toLowerCase();
    document.querySelectorAll('#issuesTableBody tr').forEach(row => {
        const t = row.textContent.toLowerCase();
        row.style.display = (!s||t.includes(s)) && (!st||t.includes(st)) && (!pr||t.includes(pr)) && (!tg||t.includes(tg)) ? '' : 'none';
    });
}
</script>

<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js"></script>
</body>
</html>