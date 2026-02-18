<?php
/**
 * Admin Advertisement Review Page
 * ✅ UI Redesigned to match Moderator page style
 * ✅ UPDATED: Mock data now matches Moderator database data
 * ✅ XAMPP-SAFE (No heavy queries)
 * Version: 2.2.0
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';

$basePath = '';
$currentPath = 'ads';
$message = '';

// ✅ UPDATED MOCK DATA - Matches Moderator's database data exactly
$mockAdsData = [
    [
        'id' => 1,
        'title' => 'Leel Plumbers',
        'company' => 'Unknown',
        'type' => 'Banner',
        'status' => 'Pending',
        'submitted' => '2026-02-10',
        'budget' => 50000,
        'description' => 'Professional plumbing services available 24/7.',
        'reviewed_by' => null,
        'reviewed_date' => null,
        'moderator_email' => null
    ],
    [
        'id' => 2,
        'title' => 'Wall Painters',
        'company' => 'Unknown',
        'type' => 'Banner',
        'status' => 'Pending',
        'submitted' => '2026-02-10',
        'budget' => 50000,
        'description' => 'Expert wall painting services.',
        'reviewed_by' => null,
        'reviewed_date' => null,
        'moderator_email' => null
    ],
    [
        'id' => 3,
        'title' => 'Nimal Constructions',
        'company' => 'Unknown',
        'type' => 'Banner',
        'status' => 'Pending',
        'submitted' => '2026-02-10',
        'budget' => 50000,
        'description' => 'Complete construction services.',
        'reviewed_by' => null,
        'reviewed_date' => null,
        'moderator_email' => null
    ],
    [
        'id' => 4,
        'title' => 'High Quality Plumbing Services',
        'company' => 'Unknown',
        'type' => 'Featured',
        'status' => 'Approved',
        'submitted' => '2026-01-03',
        'budget' => 50000,
        'description' => 'High-quality plumbing solutions.',
        'reviewed_by' => 'Mike Wilson',
        'reviewed_date' => '2026-01-03',
        'moderator_email' => 'mike@fixlanka.lk'
    ],
    [
        'id' => 5,
        'title' => 'Happy Customer Constructions',
        'company' => 'Mike Wilson',
        'type' => 'Banner',
        'status' => 'Approved',
        'submitted' => '2026-01-03',
        'budget' => 50000,
        'description' => 'Construction services with customer satisfaction guarantee.',
        'reviewed_by' => 'Mike Wilson',
        'reviewed_date' => '2026-01-03',
        'moderator_email' => 'mike@fixlanka.lk'
    ],
    [
        'id' => 6,
        'title' => 'Expert Cleaning Services',
        'company' => 'Sarah Brown',
        'type' => 'Sponsored',
        'status' => 'Rejected',
        'submitted' => '2026-01-03',
        'budget' => 28000,
        'description' => 'Professional cleaning services for homes and offices.',
        'reviewed_by' => 'Sarah Brown',
        'reviewed_date' => '2026-01-03',
        'moderator_email' => 'sarah@fixlanka.lk',
        'rejection_reason' => 'Service area not covered.'
    ],
    [
        'id' => 7,
        'title' => 'Nimal Constructions',
        'company' => 'Mike Wilson',
        'type' => 'Sponsored',
        'status' => 'Rejected',
        'submitted' => '2026-01-04',
        'budget' => 65000,
        'description' => 'Complete building construction and renovation.',
        'reviewed_by' => 'Mike Wilson',
        'reviewed_date' => '2026-01-04',
        'moderator_email' => 'mike@fixlanka.lk',
        'rejection_reason' => 'Duplicate submission.'
    ],
    [
        'id' => 8,
        'title' => 'H&Q Constructions',
        'company' => 'Mike Wilson',
        'type' => 'Featured',
        'status' => 'Approved',
        'submitted' => '2026-02-03',
        'budget' => 265000,
        'description' => 'Premium construction services with quality guarantee.',
        'reviewed_by' => 'Mike Wilson',
        'reviewed_date' => '2026-02-03',
        'moderator_email' => 'mike@fixlanka.lk'
    ],
    [
        'id' => 9,
        'title' => 'Good Plumbing',
        'company' => 'Unknown',
        'type' => 'Banner',
        'status' => 'Approved',
        'submitted' => '2026-02-03',
        'budget' => 50000,
        'description' => 'Reliable plumbing services at affordable rates.',
        'reviewed_by' => 'Sarah Brown',
        'reviewed_date' => '2026-02-03',
        'moderator_email' => 'sarah@fixlanka.lk'
    ],
    [
        'id' => 10,
        'title' => 'S&S Korean Constructions',
        'company' => 'Unknown',
        'type' => 'Featured',
        'status' => 'Approved',
        'submitted' => '2026-02-07',
        'budget' => 28000,
        'description' => 'Korean-style construction and design services.',
        'reviewed_by' => 'Mike Wilson',
        'reviewed_date' => '2026-02-07',
        'moderator_email' => 'mike@fixlanka.lk'
    ],
    [
        'id' => 11,
        'title' => 'best quality plumbing',
        'company' => 'Unknown',
        'type' => 'Sponsored',
        'status' => 'Rejected',
        'submitted' => '2026-02-07',
        'budget' => 65000,
        'description' => 'Top quality plumbing repair and installation.',
        'reviewed_by' => 'Sarah Brown',
        'reviewed_date' => '2026-02-07',
        'moderator_email' => 'sarah@fixlanka.lk',
        'rejection_reason' => 'Unprofessional title format.'
    ],
    [
        'id' => 12,
        'title' => 'Quality ABC Constructions',
        'company' => 'Sarah Brown',
        'type' => 'Featured',
        'status' => 'Approved',
        'submitted' => '2026-02-10',
        'budget' => 45000,
        'description' => 'ABC quality construction services for all building types.',
        'reviewed_by' => 'Sarah Brown',
        'reviewed_date' => '2026-02-10',
        'moderator_email' => 'sarah@fixlanka.lk'
    ]
];

// Calculate stats
$totalAds = count($mockAdsData);
$pendingAds = count(array_filter($mockAdsData, fn($ad) => $ad['status'] === 'Pending'));
$approvedAds = count(array_filter($mockAdsData, fn($ad) => $ad['status'] === 'Approved'));
$rejectedAds = count(array_filter($mockAdsData, fn($ad) => $ad['status'] === 'Rejected'));
$activeAds = count(array_filter($mockAdsData, fn($ad) => $ad['status'] === 'Active'));

$pageTitle = 'Advertisement Review - Admin';
$pageDescription = 'Monitor and manage submitted advertisements (Admin Oversight)';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/admin/ads.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderAdminSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Advertisement Review', 'Monitor and manage submitted advertisements'); ?>

            <main class="ads-content">
                <div class="content-wrapper">
                    <!-- Page Title -->
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">Advertisement Review</h2>
                        <p class="text-muted-foreground">Monitor and manage submitted advertisements (Admin Oversight)</p>
                    </div>

                    <?php if ($message): ?>
                        <div class="message-alert">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Statistics Cards (5 Cards) -->
                    <div class="grid gap-4 grid-cols-5">
                        <?php
                        function renderAdminCard($title, $value, $subtitle, $icon, $color) {
                            $iconMap = [
                                'monitor' => 'fa-desktop',
                                'clock' => 'fa-clock',
                                'check-circle' => 'fa-circle-check',
                                'x-circle' => 'fa-circle-xmark',
                                'chart-line' => 'fa-chart-line'
                            ];
                            $iconClass = $iconMap[$icon] ?? 'fa-desktop';
                            ?>
                            <div class="stat-card" data-color="<?php echo $color; ?>">
                                <div class="stat-card-inner">
                                    <div class="stat-info">
                                        <h4><?php echo $title; ?></h4>
                                        <div class="stat-value"><?php echo $value; ?></div>
                                        <p class="stat-change"><?php echo $subtitle; ?></p>
                                    </div>
                                    <div class="stat-icon">
                                        <i class="fa-solid <?php echo $iconClass; ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        
                        renderAdminCard('Total Ads', $totalAds, 'All submissions', 'monitor', 'blue');
                        renderAdminCard('Pending Review', $pendingAds, 'Awaiting approval', 'clock', 'yellow');
                        renderAdminCard('Approved', $approvedAds, 'Ready for scheduling', 'check-circle', 'green');
                        renderAdminCard('Rejected', $rejectedAds, 'Not approved', 'x-circle', 'red');
                        renderAdminCard('Active Ads', $activeAds, 'Currently live', 'chart-line', 'purple');
                        ?>
                    </div>

                    <!-- Main Table Card -->
                    <div class="bg-card rounded-lg shadow border">
                        <!-- Filter Section -->
                        <div class="p-6 border-b">
                            <div class="mb-6">
                                <h3 class="table-title">Advertisement Queue</h3>
                                <p class="table-subtitle">Review and manage advertisement approvals and lifecycle</p>
                            </div>

                            <form method="GET" action="" id="filterForm" class="space-y-4">
                                <div class="grid gap-4 grid-cols-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Search</label>
                                        <input 
                                            type="text" 
                                            id="searchInput" 
                                            placeholder="Search ads..." 
                                            class="form-input w-full"
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Status</label>
                                        <select id="statusFilter" class="form-select w-full">
                                            <option value="">All Status</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                            <option value="Active">Active</option>
                                            <option value="Rejected">Rejected</option>
                                            <option value="Suspended">Suspended</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Type</label>
                                        <select id="typeFilter" class="form-select w-full">
                                            <option value="">All Types</option>
                                            <option value="Banner">Banner</option>
                                            <option value="Sponsored">Sponsored</option>
                                            <option value="Featured">Featured</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Moderator</label>
                                        <select id="moderatorFilter" class="form-select w-full">
                                            <option value="">All Moderators</option>
                                            <option value="Mike Wilson">Mike Wilson</option>
                                            <option value="Sarah Brown">Sarah Brown</option>
                                            <option value="Not Reviewed">Not Reviewed</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium mb-2">Sort By</label>
                                        <select id="sortFilter" class="form-select w-full">
                                            <option value="newest">Newest First</option>
                                            <option value="oldest">Oldest First</option>
                                            <option value="budget-high">Budget: High to Low</option>
                                            <option value="budget-low">Budget: Low to High</option>
                                        </select>
                                    </div>

                                    <div class="flex items-end">
                                        <button type="button" id="clearFilters" class="filter-button">
                                            <i class="fa-solid fa-xmark w-4 h-4"></i>
                                            <span>Clear Filters</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Table Section -->
                        <div class="table-wrapper">
                            <table class="table w-full">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">ID</th>
                                        <th style="width: 200px;">TITLE</th>
                                        <th style="width: 150px;">PROVIDER</th>
                                        <th style="width: 100px;">TYPE</th>
                                        <th style="width: 130px;">BUDGET</th>
                                        <th style="width: 110px;">STATUS</th>
                                        <th style="width: 130px;">SUBMITTED</th>
                                        <th style="width: 200px;">ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody id="adsTableBody">
                                    <?php foreach ($mockAdsData as $ad): ?>
                                    <tr data-ad-id="<?= $ad['id'] ?>">
                                        <td class="font-mono text-sm">#<?= $ad['id'] ?></td>
                                        <td class="font-medium"><?= htmlspecialchars($ad['title']) ?></td>
                                        <td><?= htmlspecialchars($ad['company']) ?></td>
                                        <td>
                                            <span class="type-badge">
                                                <?= $ad['type'] ?>
                                            </span>
                                        </td>
                                        <td class="font-mono">LKR <?= number_format($ad['budget'], 2) ?></td>
                                        <td>
                                            <span class="badge status-<?= strtolower($ad['status']) ?>">
                                                <?= strtoupper($ad['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-sm">
                                            <?= date('M d, Y', strtotime($ad['submitted'])) ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons-group">
                                                <button class="btn-action btn-view" onclick='viewAd(<?= json_encode($ad) ?>)' title="View Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                    <span>View</span>
                                                </button>
                                                <button class="btn-action btn-history" onclick="viewHistory(<?= $ad['id'] ?>)" title="View History">
                                                    <i class="fa-solid fa-history"></i>
                                                    <span>History</span>
                                                </button>
                                                <button class="btn-action btn-override" onclick='openOverride(<?= json_encode($ad) ?>)' title="Admin Override">
                                                    <i class="fa-solid fa-user-shield"></i>
                                                    <span>Override</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <!-- View Advertisement Modal -->
            <div id="viewModal" class="modal-bg" style="display: none;">
                <div class="modal-box">
                    <button class="close-btn" onclick="closeModal('viewModal')">×</button>
                    <h2 style="margin-bottom: 20px;">Advertisement Details</h2>
                    
                    <div class="detail-row">
                        <span class="detail-label">Advertisement ID:</span>
                        <span class="detail-value" id="viewId">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Advertisement Title:</span>
                        <span class="detail-value" id="viewTitle">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Provider/Company:</span>
                        <span class="detail-value" id="viewCompany">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ad Type:</span>
                        <span class="detail-value" id="viewType">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Budget:</span>
                        <span class="detail-value" id="viewBudget">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Submitted Date:</span>
                        <span class="detail-value" id="viewSubmitted">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Current Status:</span>
                        <span class="detail-value" id="viewStatus">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Reviewed By:</span>
                        <span class="detail-value" id="viewReviewedBy">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Reviewed Date:</span>
                        <span class="detail-value" id="viewReviewedDate">-</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Description:</span>
                        <span class="detail-value" id="viewDescription">-</span>
                    </div>
                    <div class="detail-row" id="rejectionReasonRow" style="display: none;">
                        <span class="detail-label" style="color: #ef4444;">Rejection Reason:</span>
                        <span class="detail-value" id="viewRejectionReason" style="color: #ef4444;">-</span>
                    </div>
                </div>
            </div>

            <!-- Review History Modal -->
            <div id="historyModal" class="modal-bg" style="display: none;">
                <div class="modal-box">
                    <button class="close-btn" onclick="closeModal('historyModal')">×</button>
                    <h2 style="margin-bottom: 20px;">Review History</h2>
                    
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background: #3b82f6;"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Advertisement Submitted</div>
                                <div class="timeline-date">Feb 10, 2026 - 10:30 AM</div>
                                <div class="timeline-description">Submitted by provider</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background: #f59e0b;"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Under Review</div>
                                <div class="timeline-date">Feb 10, 2026 - 11:00 AM</div>
                                <div class="timeline-description">Assigned to moderator for review</div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker" style="background: #10b981;"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">Approved</div>
                                <div class="timeline-date">Feb 11, 2026 - 09:15 AM</div>
                                <div class="timeline-description">Reviewed and approved by moderator</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Override Modal -->
            <div id="overrideModal" class="modal-bg" style="display: none;">
                <div class="modal-box">
                    <button class="close-btn" onclick="closeModal('overrideModal')">×</button>
                    <h2 style="margin-bottom: 20px; color: #f59e0b;">
                        <i class="fa-solid fa-user-shield"></i> Admin Override
                    </h2>
                    
                    <div class="override-warning">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <p>Admin override will be recorded in audit logs.</p>
                    </div>

                    <div class="override-current-info">
                        <h4>Current Advertisement:</h4>
                        <p id="overrideAdTitle">-</p>
                        <p class="override-current-status">Current Status: <span id="overrideCurrentStatus">-</span></p>
                    </div>

                    <div class="form-group">
                        <label for="overrideStatus">New Status:</label>
                        <select id="overrideStatus" class="form-select w-full">
                            <option value="">-- Select New Status --</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Suspended">Suspended</option>
                            <option value="Active">Active</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="overrideReason">Reason for Override:</label>
                        <textarea 
                            id="overrideReason" 
                            class="form-textarea w-full" 
                            rows="4" 
                            placeholder="Explain why you are overriding this decision..."
                            required
                        ></textarea>
                    </div>

                    <div class="modal-actions">
                        <button class="btn btn-secondary" onclick="closeModal('overrideModal')">
                            <i class="fa-solid fa-xmark"></i> Cancel
                        </button>
                        <button class="btn btn-warning" onclick="confirmOverride()">
                            <i class="fa-solid fa-check"></i> Confirm Override
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js?v=<?php echo time(); ?>"></script>
    <script>
        let currentAdData = null;

        function viewAd(ad) {
            currentAdData = ad;
            document.getElementById('viewId').textContent = '#' + ad.id;
            document.getElementById('viewTitle').textContent = ad.title;
            document.getElementById('viewCompany').textContent = ad.company;
            document.getElementById('viewType').textContent = ad.type;
            document.getElementById('viewBudget').textContent = 'LKR ' + Number(ad.budget).toLocaleString();
            document.getElementById('viewSubmitted').textContent = new Date(ad.submitted).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('viewStatus').innerHTML = '<span class="badge status-' + ad.status.toLowerCase() + '">' + ad.status.toUpperCase() + '</span>';
            document.getElementById('viewReviewedBy').textContent = ad.reviewed_by || 'Not reviewed yet';
            document.getElementById('viewReviewedDate').textContent = ad.reviewed_date ? new Date(ad.reviewed_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';
            document.getElementById('viewDescription').textContent = ad.description;
            
            if (ad.rejection_reason) {
                document.getElementById('rejectionReasonRow').style.display = 'flex';
                document.getElementById('viewRejectionReason').textContent = ad.rejection_reason;
            } else {
                document.getElementById('rejectionReasonRow').style.display = 'none';
            }
            
            document.getElementById('viewModal').style.display = 'flex';
        }

        function viewHistory(adId) {
            document.getElementById('historyModal').style.display = 'flex';
        }

        function openOverride(ad) {
            currentAdData = ad;
            document.getElementById('overrideAdTitle').textContent = ad.title + ' (' + ad.company + ')';
            document.getElementById('overrideCurrentStatus').textContent = ad.status;
            document.getElementById('overrideStatus').value = '';
            document.getElementById('overrideReason').value = '';
            document.getElementById('overrideModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function confirmOverride() {
            const newStatus = document.getElementById('overrideStatus').value;
            const reason = document.getElementById('overrideReason').value;
            
            if (!newStatus) {
                alert('Please select a new status');
                return;
            }
            
            if (!reason.trim()) {
                alert('Please provide a reason for override');
                return;
            }
            
            alert('Override successful!\n\nAd: ' + currentAdData.title + '\nNew Status: ' + newStatus + '\nReason: ' + reason);
            closeModal('overrideModal');
        }

        // Filter functionality
        document.getElementById('clearFilters').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('typeFilter').value = '';
            document.getElementById('moderatorFilter').value = '';
            document.getElementById('sortFilter').value = 'newest';
        });

        // Close modal on overlay click
        document.querySelectorAll('.modal-bg').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>