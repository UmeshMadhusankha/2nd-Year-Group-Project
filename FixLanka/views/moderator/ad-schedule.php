<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include components
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once '../../includes/admin-modarator/auth.php';
require_once '../../includes/admin-modarator/mock-data.php';

// Check if user is logged in and get user info
// $isLoggedIn = isLoggedIn();
// $user = $isLoggedIn ? getCurrentUser() : null;
// requireRole("moderator", $basePath);
// $user = getCurrentUser();

// Handle form submission
$message = '';
if ($_POST) {
    if (isset($_POST['action']) && $_POST['action'] === 'schedule_ad') {
        $message = 'Advertisement scheduled successfully!';
    }
}

$scheduledAds = [
    [
        'id' => 1,
        'company' => 'FixItNow Pvt Ltd',
        'title' => 'Professional Plumbing Services',
        'type' => 'Banner',
        'startDate' => '2024-08-01',
        'endDate' => '2024-08-31',
        'timeSlot' => '09:00 - 18:00',
        'placement' => 'Homepage Top',
        'status' => 'Active',
        'priority' => 'high',
        'budget' => 'LKR 50,000',
        'category' => 'Plumbing',
        'createdDate' => '2024-07-25',
        'performance' => ['views' => 1250, 'clicks' => 89, 'ctr' => '7.1%']
    ],
    [
        'id' => 2,
        'company' => 'Lanka Builders',
        'title' => 'Construction & Renovation',
        'type' => 'Sponsored',
        'startDate' => '2024-08-05',
        'endDate' => '2024-09-05',
        'timeSlot' => '08:00 - 20:00',
        'placement' => 'Search Results',
        'status' => 'Scheduled',
        'priority' => 'medium',
        'budget' => 'LKR 75,000',
        'category' => 'Construction',
        'createdDate' => '2024-07-28',
        'performance' => ['views' => 0, 'clicks' => 0, 'ctr' => '0%']
    ],
    [
        'id' => 3,
        'company' => 'ElectroFix Solutions',
        'title' => 'Electrical Repairs & Installation',
        'type' => 'Featured',
        'startDate' => '2024-07-20',
        'endDate' => '2024-08-20',
        'timeSlot' => '10:00 - 22:00',
        'placement' => 'Category Page',
        'status' => 'Active',
        'priority' => 'high',
        'budget' => 'LKR 35,000',
        'category' => 'Electrical',
        'createdDate' => '2024-07-15',
        'performance' => ['views' => 890, 'clicks' => 45, 'ctr' => '5.1%']
    ],
    [
        'id' => 4,
        'company' => 'Clean Pro Services',
        'title' => 'Professional Cleaning Services',
        'type' => 'Banner',
        'startDate' => '2024-08-10',
        'endDate' => '2024-09-10',
        'timeSlot' => '06:00 - 18:00',
        'placement' => 'Sidebar',
        'status' => 'Scheduled',
        'priority' => 'low',
        'budget' => 'LKR 25,000',
        'category' => 'Cleaning',
        'createdDate' => '2024-08-01',
        'performance' => ['views' => 0, 'clicks' => 0, 'ctr' => '0%']
    ],
    [
        'id' => 5,
        'company' => 'Quick Repair Hub',
        'title' => 'Emergency Repair Services',
        'type' => 'Sponsored',
        'startDate' => '2024-07-15',
        'endDate' => '2024-08-15',
        'timeSlot' => '24/7',
        'placement' => 'Homepage Top',
        'status' => 'Expired',
        'priority' => 'high',
        'budget' => 'LKR 60,000',
        'category' => 'Emergency',
        'createdDate' => '2024-07-10',
        'performance' => ['views' => 2100, 'clicks' => 156, 'ctr' => '7.4%']
    ]
];

// Sort by newest first by default
usort($scheduledAds, function ($a, $b) {
    return strtotime($b['createdDate']) - strtotime($a['createdDate']);
});

$message = '';
$basePath = '';
$currentPath = 'ad-schedule';

// Define status and priority variants for styling
$statusVariants = [
    'Active' => 'bg-green-500/10 text-green-500 border-green-500/20',
    'Scheduled' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
    'Expired' => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
    'Paused' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20'
];

$priorityVariants = [
    'high' => 'bg-red-500/10 text-red-500 border-red-500/20',
    'medium' => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
    'low' => 'bg-blue-500/10 text-blue-500 border-blue-500/20'
];

// Get page title and description from variables or use defaults
$pageTitle = $title ?? 'Advanced PHP Router';
$pageDescription = $description ?? 'A Next.js-inspired PHP routing system with advanced features';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, $pageDescription, $basePath ?? ''); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-foreground text-background">
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        <div class="dashboard-main">
            <link rel="stylesheet" href="../../assets/css/moderator/ad-schedule.css">

            <?php renderPageHeader($basePath, 'Ad Scheduling', 'Manage advertisement timing and placement with advanced filtering',); ?>

            <main style="margin-top: 5rem;" class="ad-schedule-content">
                <div class="space-y-6">
                    <div class="page-header-with-action">
                        <div>
                            <h2 class="text-3xl font-bold tracking-tight text-foreground">Ad Scheduling Dashboard</h2>
                            <p class="text-muted-foreground">Manage advertisement timing, placement, and scheduling with powerful filtering tools</p>
                        </div>
                        <button onclick="openModal('scheduleModal')" class="btn btn-primary">
                            <i data-lucide="calendar-plus" class="mr-2 h-4 w-4"></i>
                            Schedule Ad
                        </button>
                    </div>

                    <?php if ($message): ?>
                        <div class="bg-fixlanka-highlight/10 border border-fixlanka-highlight/20 text-fixlanka-primary px-4 py-3 rounded">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-4 grid-cols-4">
                        <?php
                        $activeAds = count(array_filter($scheduledAds, fn($ad) => $ad['status'] === 'Active'));
                        $scheduledCount = count(array_filter($scheduledAds, fn($ad) => $ad['status'] === 'Scheduled'));
                        $expiredCount = count(array_filter($scheduledAds, fn($ad) => $ad['status'] === 'Expired'));
                        $todayScheduled = count(array_filter(
                            $scheduledAds,
                            fn($ad) =>
                            $ad['status'] === 'Scheduled' && $ad['startDate'] === date('Y-m-d')
                        ));

                        renderCard('Total Scheduled', count($scheduledAds), 'All scheduled ads', 'calendar', 'text-blue-600');
                        renderCard('Currently Active', $activeAds, 'Running now', 'play-circle', 'text-green-600');
                        renderCard('Starting Today', $todayScheduled, 'New today', 'clock', 'text-yellow-600');
                        renderCard('Available Slots', '12', 'Open time slots', 'calendar-days', 'text-purple-600');
                        ?>
                    </div>

                    <!-- Added comprehensive filtering and sorting controls for ad scheduling -->
                    <div class="bg-card rounded-lg border">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-foreground mb-4">Filter & Sort Advertisements</h3>
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="sortSelect">Sort By</label>
                                    <select id="sortSelect" class="form-select" onchange="sortAds()">
                                        <option value="newest">Newest First</option>
                                        <option value="oldest">Oldest First</option>
                                        <option value="start_date">Start Date</option>
                                        <option value="end_date">End Date</option>
                                        <option value="priority">Priority</option>
                                        <option value="performance">Performance</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="statusFilter">Status</label>
                                    <select id="statusFilter" class="form-select" onchange="filterAds()">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Scheduled">Scheduled</option>
                                        <option value="Expired">Expired</option>
                                        <option value="Paused">Paused</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="priorityFilter">Priority</label>
                                    <select id="priorityFilter" class="form-select" onchange="filterAds()">
                                        <option value="">All Priorities</option>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="categoryFilter">Category</label>
                                    <select id="categoryFilter" class="form-select" onchange="filterAds()">
                                        <option value="">All Categories</option>
                                        <option value="Plumbing">Plumbing</option>
                                        <option value="Electrical">Electrical</option>
                                        <option value="Construction">Construction</option>
                                        <option value="Cleaning">Cleaning</option>
                                        <option value="Emergency">Emergency</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="placementFilter">Placement</label>
                                    <select id="placementFilter" class="form-select" onchange="filterAds()">
                                        <option value="">All Placements</option>
                                        <option value="Homepage Top">Homepage Top</option>
                                        <option value="Search Results">Search Results</option>
                                        <option value="Category Page">Category Page</option>
                                        <option value="Sidebar">Sidebar</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-foreground mb-2" for="searchInput">Search</label>
                                    <div class="relative">
                                        <i data-lucide="search" class="absolute searchInputIcon text-muted-foreground"></i>
                                        <input type="text" id="searchInput" placeholder="Search ads..." class="form-input pl-10" oninput="searchAds()">
                                    </div>
                                </div>
                            </div>
                            <!-- Added quick filter buttons for common scheduling tasks -->
                            <div class="flex flex-wrap gap-2 mt-4">
                                <button onclick="quickFilter('active_today')" class="btn btn-sm btn-secondary">
                                    <i data-lucide="play-circle" class="mr-1 h-3 w-3"></i>
                                    Active Today
                                </button>
                                <button onclick="quickFilter('starting_soon')" class="btn btn-sm btn-secondary">
                                    <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                    Starting Soon
                                </button>
                                <button onclick="quickFilter('high_performance')" class="btn btn-sm btn-secondary">
                                    <i data-lucide="trending-up" class="mr-1 h-3 w-3"></i>
                                    High Performance
                                </button>
                                <button onclick="quickFilter('expiring_soon')" class="btn btn-sm btn-secondary">
                                    <i data-lucide="alert-triangle" class="mr-1 h-3 w-3"></i>
                                    Expiring Soon
                                </button>
                                <button onclick="clearFilters()" class="btn btn-sm btn-outline">
                                    <i data-lucide="x" class="mr-1 h-3 w-3"></i>
                                    Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="schedule-grid">
                        <div class="scheduled-ads-table">
                            <div class="p-6">
                                <div class="flex items-center justify-between w-full mb-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-foreground">Scheduled Advertisements</h3>
                                        <p class="text-sm text-muted-foreground">View and manage all scheduled advertisements with enhanced details</p>
                                    </div>
                                    <!-- Added view toggle for better data visualization -->
                                    <div class="flex items-center space-x-2">
                                        <button onclick="toggleView('table')" id="tableViewBtn" class="btn btn-sm btn-primary">
                                            <i data-lucide="list" class="h-4 w-4"></i>
                                        </button>
                                        <button onclick="toggleView('cards')" id="cardViewBtn" class="btn btn-sm btn-secondary">
                                            <i data-lucide="grid-3x3" class="h-4 w-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced table view with more comprehensive information -->
                            <div id="tableView" class="overflow-x-auto">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                            </th>
                                            <th>Advertisement Details</th>
                                            <th>Schedule & Status</th>
                                            <th>Performance</th>
                                            <th>Budget & Priority</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adsTableBody">
                                        <?php foreach ($scheduledAds as $ad): ?>
                                            <tr class="ad-row"
                                                data-status="<?php echo $ad['status']; ?>"
                                                data-priority="<?php echo $ad['priority']; ?>"
                                                data-category="<?php echo $ad['category']; ?>"
                                                data-placement="<?php echo $ad['placement']; ?>"
                                                data-start-date="<?php echo $ad['startDate']; ?>"
                                                data-created-date="<?php echo $ad['createdDate']; ?>"
                                                data-performance="<?php echo $ad['performance']['ctr']; ?>">
                                                <td>
                                                    <input type="checkbox" class="ad-checkbox" value="<?php echo $ad['id']; ?>">
                                                </td>
                                                <td>
                                                    <div class="flex items-center space-x-3">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-10 w-10 rounded-lg bg-fixlanka-primary/10 flex items-center justify-center">
                                                                <i data-lucide="calendar" class="h-5 w-5 text-fixlanka-primary"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-foreground">
                                                                <?php echo htmlspecialchars($ad['title']); ?>
                                                            </div>
                                                            <div class="text-xs text-muted-foreground">
                                                                <?php echo htmlspecialchars($ad['company']); ?>
                                                            </div>
                                                            <div class="flex items-center space-x-2 mt-1">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                                                                    <?php echo $ad['type']; ?>
                                                                </span>
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-muted text-muted-foreground">
                                                                    <?php echo $ad['category']; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="space-y-2">
                                                        <?php
                                                        $statusClass = $statusVariants[$ad['status']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                                                        echo '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ' . $statusClass . '">' . $ad['status'] . '</span>';
                                                        ?>
                                                        <div class="text-xs text-muted-foreground">
                                                            <div><?php echo date('M j', strtotime($ad['startDate'])); ?> - <?php echo date('M j', strtotime($ad['endDate'])); ?></div>
                                                            <div><?php echo $ad['timeSlot']; ?></div>
                                                            <div><?php echo $ad['placement']; ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="space-y-1">
                                                        <div class="text-sm font-medium text-foreground">
                                                            <?php echo number_format($ad['performance']['views']); ?> views
                                                        </div>
                                                        <div class="text-xs text-muted-foreground">
                                                            <?php echo $ad['performance']['clicks']; ?> clicks • <?php echo $ad['performance']['ctr']; ?> CTR
                                                        </div>
                                                        <?php if ($ad['status'] === 'Active'): ?>
                                                            <div class="w-full bg-muted rounded-full h-1.5">
                                                                <div class="bg-fixlanka-primary h-1.5 rounded-full" style="width: <?php echo floatval($ad['performance']['ctr']); ?>0%"></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="space-y-2">
                                                        <div class="text-sm font-medium text-foreground"><?php echo $ad['budget']; ?></div>
                                                        <?php
                                                        $priorityClass = $priorityVariants[$ad['priority']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                                                        echo '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ' . $priorityClass . '">' . ucfirst($ad['priority']) . '</span>';
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex space-x-1">
                                                        <?php if ($ad['status'] === 'Active'): ?>
                                                            <button onclick='pauseAd(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-secondary" title="Pause Ad">
                                                                <i data-lucide="pause" class="h-3 w-3"></i>
                                                            </button>
                                                        <?php elseif ($ad['status'] === 'Scheduled'): ?>
                                                            <button onclick='activateAd(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-primary" title="Activate Now">
                                                                <i data-lucide="play" class="h-3 w-3"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button onclick='editSchedule(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-secondary" title="Edit Schedule">
                                                            <i data-lucide="edit" class="h-3 w-3"></i>
                                                        </button>
                                                        <button onclick='viewAdDetails(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-primary" title="View Details">
                                                            <i data-lucide="eye" class="h-3 w-3"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Added card view for better visual management -->
                            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6" style="display: none;">
                                <?php foreach ($scheduledAds as $ad): ?>
                                    <div class="ad-schedule-card bg-muted/30 rounded-lg border p-6 hover:shadow-lg transition-all duration-200"
                                        data-status="<?php echo $ad['status']; ?>"
                                        data-priority="<?php echo $ad['priority']; ?>"
                                        data-category="<?php echo $ad['category']; ?>"
                                        data-placement="<?php echo $ad['placement']; ?>"
                                        data-start-date="<?php echo $ad['startDate']; ?>"
                                        data-created-date="<?php echo $ad['createdDate']; ?>"
                                        data-performance="<?php echo $ad['performance']['ctr']; ?>">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-foreground mb-1"><?php echo htmlspecialchars($ad['title']); ?></h4>
                                                <p class="text-sm text-muted-foreground"><?php echo htmlspecialchars($ad['company']); ?></p>
                                            </div>
                                            <div class="flex flex-col items-end space-y-1">
                                                <?php
                                                $statusClass = $statusVariants[$ad['status']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                                                echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border ' . $statusClass . '">' . $ad['status'] . '</span>';

                                                $priorityClass = $priorityVariants[$ad['priority']] ?? 'bg-gray-500/10 text-gray-500 border-gray-500/20';
                                                echo '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border ' . $priorityClass . '">' . ucfirst($ad['priority']) . '</span>';
                                                ?>
                                            </div>
                                        </div>

                                        <div class="space-y-3 mb-4">
                                            <div class="flex items-center justify-between w-full text-sm">
                                                <span class="text-muted-foreground">Schedule:</span>
                                                <span class="font-medium"><?php echo date('M j', strtotime($ad['startDate'])); ?> - <?php echo date('M j', strtotime($ad['endDate'])); ?></span>
                                            </div>
                                            <div class="flex items-center justify-between w-full text-sm">
                                                <span class="text-muted-foreground">Time:</span>
                                                <span class="font-medium"><?php echo $ad['timeSlot']; ?></span>
                                            </div>
                                            <div class="flex items-center justify-between w-full text-sm">
                                                <span class="text-muted-foreground">Placement:</span>
                                                <span class="font-medium"><?php echo $ad['placement']; ?></span>
                                            </div>
                                            <div class="flex items-center justify-between w-full text-sm">
                                                <span class="text-muted-foreground">Budget:</span>
                                                <span class="font-medium"><?php echo $ad['budget']; ?></span>
                                            </div>
                                            <?php if ($ad['status'] === 'Active'): ?>
                                                <div class="space-y-2">
                                                    <div class="flex items-center justify-between w-full text-sm">
                                                        <span class="text-muted-foreground">Performance:</span>
                                                        <span class="font-medium"><?php echo $ad['performance']['ctr']; ?> CTR</span>
                                                    </div>
                                                    <div class="text-xs text-muted-foreground">
                                                        <?php echo number_format($ad['performance']['views']); ?> views • <?php echo $ad['performance']['clicks']; ?> clicks
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex space-x-2">
                                            <?php if ($ad['status'] === 'Active'): ?>
                                                <button onclick='pauseAd(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="flex-1 btn btn-sm btn-secondary">
                                                    <i data-lucide="pause" class="mr-1 h-3 w-3"></i>
                                                    Pause
                                                </button>
                                            <?php elseif ($ad['status'] === 'Scheduled'): ?>
                                                <button onclick='activateAd(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="flex-1 btn btn-sm btn-primary">
                                                    <i data-lucide="play" class="mr-1 h-3 w-3"></i>
                                                    Activate
                                                </button>
                                            <?php endif; ?>
                                            <button onclick='editSchedule(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-secondary">
                                                <i data-lucide="edit" class="h-3 w-3"></i>
                                            </button>
                                            <button onclick='viewAdDetails(<?php echo htmlspecialchars(json_encode($ad)); ?>)' class="btn btn-sm btn-primary">
                                                <i data-lucide="eye" class="h-3 w-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="sidebar-cards">
                            <div class="calendar-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-foreground">Calendar View</h3>
                                    <p class="text-sm text-muted-foreground">Monthly ad schedule overview</p>

                                    <div class="mt-4">
                                        <div class="text-center mb-4">
                                            <h4 class="text-lg font-semibold">August 2024</h4>
                                        </div>

                                        <div class="calendar-grid">
                                            <div class="calendar-day font-medium text-muted-foreground">Sun</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Mon</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Tue</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Wed</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Thu</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Fri</div>
                                            <div class="calendar-day font-medium text-muted-foreground">Sat</div>

                                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                                <div class="calendar-day <?php echo in_array($i, [1, 5, 10, 20]) ? 'has-event' : ''; ?>">
                                                    <div class="text-sm"><?php echo $i; ?></div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="analytics-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-foreground">Placement Analytics</h3>
                                    <p class="text-sm text-muted-foreground">Ad placement performance</p>

                                    <div class="mt-4 space-y-3">
                                        <?php
                                        $placements = [
                                            ['name' => 'Homepage Top', 'ads' => 3, 'performance' => 85],
                                            ['name' => 'Search Results', 'ads' => 5, 'performance' => 72],
                                            ['name' => 'Category Page', 'ads' => 2, 'performance' => 68],
                                            ['name' => 'Sidebar', 'ads' => 4, 'performance' => 45]
                                        ];

                                        foreach ($placements as $placement):
                                        ?>
                                            <div class="placement-item">
                                                <div class="placement-info">
                                                    <p class="text-sm font-medium"><?php echo $placement['name']; ?></p>
                                                    <p class="text-xs text-muted-foreground"><?php echo $placement['ads']; ?> active ads</p>
                                                </div>
                                                <div class="placement-stats">
                                                    <p class="text-sm font-medium"><?php echo $placement['performance']; ?>%</p>
                                                    <div class="performance-bar">
                                                        <div class="performance-fill" style="width: <?php echo $placement['performance']; ?>%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Schedule Ad Modal -->
            <!-- Updated modal structure to use modal.css classes -->
            <div id="scheduleModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Schedule Advertisement</h3>
                            <button type="button" class="modal-close" onclick="closeModal('scheduleModal')">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>

                        <form method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="schedule_ad">

                                <div class="form-group">
                                    <label class="form-label">Advertisement</label>
                                    <select name="ad_id" required class="form-select">
                                        <option value="">Select advertisement</option>
                                        <?php foreach ($mockAds as $ad): ?>
                                            <option value="<?php echo $ad['id']; ?>"><?php echo htmlspecialchars($ad['title']); ?> - <?php echo htmlspecialchars($ad['company']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" name="start_date" required class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Date</label>
                                        <input type="date" name="end_date" required class="form-input">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Time Slot</label>
                                    <select name="time_slot" class="form-select">
                                        <option value="00:00-23:59">All Day</option>
                                        <option value="08:00-18:00">Business Hours (8 AM - 6 PM)</option>
                                        <option value="09:00-17:00">Office Hours (9 AM - 5 PM)</option>
                                        <option value="18:00-23:00">Evening (6 PM - 11 PM)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Placement</label>
                                    <select name="placement" class="form-select">
                                        <option value="homepage_top">Homepage Top</option>
                                        <option value="search_results">Search Results</option>
                                        <option value="category_page">Category Page</option>
                                        <option value="sidebar">Sidebar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('scheduleModal')" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Schedule Ad
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Ad Details Modal -->
            <!-- Updated modal structure to use modal.css classes -->
            <div id="adDetailsModal" class="modal-overlay">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Advertisement Details</h3>
                            <button type="button" class="modal-close" onclick="closeModal('adDetailsModal')">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="adDetailsContent" class="space-y-6">
                                <!-- Content will be populated by JavaScript -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeModal('adDetailsModal')" class="btn btn-secondary">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Ad Modal -->
            <div id="editAdModal" class="modal-overlay">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title">Edit Advertisement Schedule</h3>
                            <button type="button" class="modal-close" onclick="closeModal('editAdModal')">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>

                        <form id="editAdForm" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="action" value="edit_ad">
                                <input type="hidden" id="edit_ad_id" name="ad_id">

                                <div class="form-group">
                                    <label class="form-label">Advertisement Title</label>
                                    <input type="text" id="edit_title" name="title" readonly class="form-input bg-muted">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Company</label>
                                    <input type="text" id="edit_company" name="company" readonly class="form-input bg-muted">
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" id="edit_start_date" name="start_date" required class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Date</label>
                                        <input type="date" id="edit_end_date" name="end_date" required class="form-input">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Time Slot</label>
                                    <select id="edit_time_slot" name="time_slot" class="form-select">
                                        <option value="00:00-23:59">All Day</option>
                                        <option value="08:00-18:00">Business Hours (8 AM - 6 PM)</option>
                                        <option value="09:00-17:00">Office Hours (9 AM - 5 PM)</option>
                                        <option value="18:00-23:00">Evening (6 PM - 11 PM)</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Placement</label>
                                    <select id="edit_placement" name="placement" class="form-select">
                                        <option value="homepage_top">Homepage Top</option>
                                        <option value="search_results">Search Results</option>
                                        <option value="category_page">Category Page</option>
                                        <option value="sidebar">Sidebar</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Priority</label>
                                    <select id="edit_priority" name="priority" class="form-select">
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <div class="flex items-center gap-3">
                                        <span id="edit_current_status" class="badge"></span>
                                        <span class="text-sm text-muted-foreground">Current status will be updated based on dates</span>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editAdModal')" class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div id="confirmModal" class="modal-overlay">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 class="modal-title" id="confirmModalTitle">Confirm Action</h3>
                            <button type="button" class="modal-close" onclick="closeModal('confirmModal')">
                                <i data-lucide="x" class="h-4 w-4"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center space-y-4">
                                <div id="confirmModalIcon" class="mx-auto w-12 h-12 rounded-full flex items-center justify-center">
                                    <!-- Icon will be inserted here -->
                                </div>
                                <p id="confirmModalMessage" class="text-foreground"></p>
                                <div id="confirmModalStatus" class="bg-muted/50 p-3 rounded-lg">
                                    <!-- Status info will be inserted here -->
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="closeModal('confirmModal')" class="btn btn-secondary">
                                Cancel
                            </button>
                            <button type="button" id="confirmModalAction" class="btn btn-primary">
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                lucide.createIcons();

                let currentView = 'table';
                let pendingAction = null;

                function toggleView(view) {
                    currentView = view;
                    const tableView = document.getElementById('tableView');
                    const cardView = document.getElementById('cardView');
                    const tableBtn = document.getElementById('tableViewBtn');
                    const cardBtn = document.getElementById('cardViewBtn');

                    if (!tableView || !cardView || !tableBtn || !cardBtn) {
                        console.error('[v0] View toggle elements not found');
                        return;
                    }

                    if (view === 'table') {
                        tableView.style.display = 'block';
                        cardView.style.display = 'none';
                        tableBtn.classList.remove('btn-secondary');
                        tableBtn.classList.add('btn-primary');
                        cardBtn.classList.remove('btn-primary');
                        cardBtn.classList.add('btn-secondary');
                    } else {
                        tableView.style.display = 'none';
                        cardView.style.display = 'grid';
                        cardBtn.classList.remove('btn-secondary');
                        cardBtn.classList.add('btn-primary');
                        tableBtn.classList.remove('btn-primary');
                        tableBtn.classList.add('btn-secondary');
                    }

                    lucide.createIcons();
                }

                function sortAds() {
                    const sortSelect = document.getElementById('sortSelect');
                    if (!sortSelect) return;

                    const sortBy = sortSelect.value;
                    const rows = Array.from(document.querySelectorAll('.ad-row'));
                    const cards = Array.from(document.querySelectorAll('.ad-schedule-card'));

                    const sortFunction = (a, b) => {
                        switch (sortBy) {
                            case 'newest':
                                return new Date(b.dataset.createdDate || 0) - new Date(a.dataset.createdDate || 0);
                            case 'oldest':
                                return new Date(a.dataset.createdDate || 0) - new Date(b.dataset.createdDate || 0);
                            case 'start_date':
                                return new Date(a.dataset.startDate || 0) - new Date(b.dataset.startDate || 0);
                            case 'end_date':
                                const aEnd = a.dataset.endDate || a.dataset.startDate || 0;
                                const bEnd = b.dataset.endDate || b.dataset.startDate || 0;
                                return new Date(aEnd) - new Date(bEnd);
                            case 'priority':
                                const priorityOrder = {
                                    'high': 3,
                                    'medium': 2,
                                    'low': 1
                                };
                                return (priorityOrder[b.dataset.priority] || 0) - (priorityOrder[a.dataset.priority] || 0);
                            case 'performance':
                                return parseFloat(b.dataset.performance || 0) - parseFloat(a.dataset.performance || 0);
                            default:
                                return 0;
                        }
                    };

                    rows.sort(sortFunction);
                    cards.sort(sortFunction);

                    const tableBody = document.getElementById('adsTableBody');
                    const cardContainer = document.getElementById('cardView');

                    if (tableBody) rows.forEach(row => tableBody.appendChild(row));
                    if (cardContainer) cards.forEach(card => cardContainer.appendChild(card));
                }

                function filterAds() {
                    const statusFilter = document.getElementById('statusFilter')?.value || '';
                    const priorityFilter = document.getElementById('priorityFilter')?.value || '';
                    const categoryFilter = document.getElementById('categoryFilter')?.value || '';
                    const placementFilter = document.getElementById('placementFilter')?.value || '';

                    document.querySelectorAll('.ad-row, .ad-schedule-card').forEach(element => {
                        const status = element.dataset.status || '';
                        const priority = element.dataset.priority || '';
                        const category = element.dataset.category || '';
                        const placement = element.dataset.placement || '';

                        const matchesStatus = !statusFilter || status === statusFilter;
                        const matchesPriority = !priorityFilter || priority === priorityFilter;
                        const matchesCategory = !categoryFilter || category === categoryFilter;
                        const matchesPlacement = !placementFilter || placement === placementFilter;

                        element.style.display = matchesStatus && matchesPriority && matchesCategory && matchesPlacement ? '' : 'none';
                    });
                }

                function searchAds() {
                    const searchInput = document.getElementById('searchInput');
                    if (!searchInput) return;

                    const searchTerm = searchInput.value.toLowerCase();
                    document.querySelectorAll('.ad-row, .ad-schedule-card').forEach(element => {
                        const text = element.textContent.toLowerCase();
                        element.style.display = text.includes(searchTerm) ? '' : 'none';
                    });
                }

                function quickFilter(type) {
                    const filters = ['statusFilter', 'priorityFilter', 'categoryFilter', 'placementFilter', 'searchInput'];
                    filters.forEach(id => {
                        const element = document.getElementById(id);
                        if (element) element.value = '';
                    });

                    const today = new Date().toISOString().split('T')[0];
                    const nextWeek = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

                    document.querySelectorAll('.ad-row, .ad-schedule-card').forEach(element => {
                        let show = false;
                        switch (type) {
                            case 'active_today':
                                show = element.dataset.status === 'Active';
                                break;
                            case 'starting_soon':
                                show = element.dataset.status === 'Scheduled' && (element.dataset.startDate || '') <= nextWeek;
                                break;
                            case 'high_performance':
                                show = parseFloat(element.dataset.performance || 0) > 5.0;
                                break;
                            case 'expiring_soon':
                                show = element.dataset.status === 'Active' && (element.dataset.endDate || '') <= nextWeek;
                                break;
                        }
                        element.style.display = show ? '' : 'none';
                    });
                }

                function clearFilters() {
                    const filters = ['statusFilter', 'priorityFilter', 'categoryFilter', 'placementFilter', 'searchInput'];
                    filters.forEach(id => {
                        const element = document.getElementById(id);
                        if (element) element.value = '';
                    });

                    const sortSelect = document.getElementById('sortSelect');
                    if (sortSelect) sortSelect.value = 'newest';

                    document.querySelectorAll('.ad-row, .ad-schedule-card').forEach(element => {
                        element.style.display = '';
                    });
                    sortAds();
                }

                function toggleSelectAll() {
                    const selectAll = document.getElementById('selectAll');
                    if (!selectAll) return;

                    document.querySelectorAll('.ad-checkbox').forEach(checkbox => {
                        checkbox.checked = selectAll.checked;
                    });
                }

                function viewAdDetails(adData) {
                    let ad;

                    if (typeof adData === 'string') {
                        try {
                            ad = JSON.parse(adData);
                        } catch (e) {
                            console.error('[v0] Failed to parse ad data:', e);
                            return;
                        }
                    } else {
                        ad = adData;
                    }

                    const content = document.getElementById('adDetailsContent');
                    if (!content) {
                        console.error('[v0] Ad details content element not found');
                        return;
                    }

                    const views = ad.performance?.views || 0;
                    const clicks = ad.performance?.clicks || 0;
                    const ctr = ad.performance?.ctr || '0%';

                    let duration = 'N/A';
                    if (ad.startDate && ad.endDate) {
                        const days = Math.ceil((new Date(ad.endDate) - new Date(ad.startDate)) / (1000 * 60 * 60 * 24));
                        duration = days + ' days';
                    }

                    content.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-foreground mb-2">Advertisement Information</h4>
                        <div class="bg-muted/50 p-4 rounded-lg space-y-2">
                            <p class="text-sm"><strong>Title:</strong> ${ad.title || 'N/A'}</p>
                            <p class="text-sm"><strong>Company:</strong> ${ad.company || 'N/A'}</p>
                            <p class="text-sm"><strong>Type:</strong> ${ad.type || 'N/A'}</p>
                            <p class="text-sm"><strong>Category:</strong> ${ad.category || 'N/A'}</p>
                            <p class="text-sm"><strong>Priority:</strong> ${ad.priority || 'N/A'}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground mb-2">Schedule Details</h4>
                        <div class="bg-muted/50 p-4 rounded-lg space-y-2">
                            <p class="text-sm"><strong>Status:</strong> ${ad.status || 'N/A'}</p>
                            <p class="text-sm"><strong>Start Date:</strong> ${ad.startDate || 'N/A'}</p>
                            <p class="text-sm"><strong>End Date:</strong> ${ad.endDate || 'N/A'}</p>
                            <p class="text-sm"><strong>Time Slot:</strong> ${ad.timeSlot || 'N/A'}</p>
                            <p class="text-sm"><strong>Placement:</strong> ${ad.placement || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-foreground mb-2">Budget & Performance</h4>
                        <div class="bg-muted/50 p-4 rounded-lg space-y-2">
                            <p class="text-sm"><strong>Budget:</strong> ${ad.budget || 'N/A'}</p>
                            <p class="text-sm"><strong>Views:</strong> ${views.toLocaleString()}</p>
                            <p class="text-sm"><strong>Clicks:</strong> ${clicks}</p>
                            <p class="text-sm"><strong>CTR:</strong> ${ctr}</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-foreground mb-2">Timeline</h4>
                        <div class="bg-muted/50 p-4 rounded-lg space-y-2">
                            <p class="text-sm"><strong>Created:</strong> ${ad.createdDate || 'N/A'}</p>
                            <p class="text-sm"><strong>Duration:</strong> ${duration}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
                    openModal('adDetailsModal');
                }

                function openModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeModal(modalId) {
                    const modal = document.getElementById(modalId);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }

                function editSchedule(adData) {
                    let ad;

                    if (typeof adData === 'string') {
                        try {
                            ad = JSON.parse(adData);
                        } catch (e) {
                            console.error('[v0] Failed to parse ad data:', e);
                            return;
                        }
                    } else {
                        ad = adData;
                    }

                    document.getElementById('edit_ad_id').value = ad.id || '';
                    document.getElementById('edit_title').value = ad.title || '';
                    document.getElementById('edit_company').value = ad.company || '';
                    document.getElementById('edit_start_date').value = ad.startDate || '';
                    document.getElementById('edit_end_date').value = ad.endDate || '';
                    document.getElementById('edit_time_slot').value = ad.timeSlot || '00:00-23:59';
                    document.getElementById('edit_placement').value = ad.placement || 'homepage_top';
                    document.getElementById('edit_priority').value = ad.priority || 'medium';

                    const statusBadge = document.getElementById('edit_current_status');
                    statusBadge.textContent = ad.status || 'Unknown';
                    statusBadge.className = 'badge badge-' + (ad.status || 'unknown').toLowerCase();

                    openModal('editAdModal');
                }

                function pauseAd(adData) {
                    let ad;

                    if (typeof adData === 'string') {
                        try {
                            ad = JSON.parse(adData);
                        } catch (e) {
                            console.error('[v0] Failed to parse ad data:', e);
                            return;
                        }
                    } else {
                        ad = adData;
                    }

                    document.getElementById('confirmModalTitle').textContent = 'Pause Advertisement';
                    document.getElementById('confirmModalMessage').textContent = 'Are you sure you want to pause this advertisement?';

                    const iconContainer = document.getElementById('confirmModalIcon');
                    iconContainer.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-warning/10';
                    iconContainer.innerHTML = '<i data-lucide="pause-circle" class="h-6 w-6 text-warning"></i>';

                    const statusInfo = document.getElementById('confirmModalStatus');
                    statusInfo.innerHTML = `
            <p class="text-sm font-medium mb-1">${ad.title || 'Unknown'}</p>
            <p class="text-xs text-muted-foreground mb-2">${ad.company || 'Unknown Company'}</p>
            <div class="flex items-center justify-between text-xs">
                <span>Current Status:</span>
                <span class="badge badge-${(ad.status || 'unknown').toLowerCase()}">${ad.status || 'Unknown'}</span>
            </div>
            <div class="flex items-center justify-between text-xs mt-1">
                <span>Will Change To:</span>
                <span class="badge badge-paused">Paused</span>
            </div>
        `;

                    const actionBtn = document.getElementById('confirmModalAction');
                    actionBtn.textContent = 'Pause Ad';
                    actionBtn.className = 'btn btn-warning';

                    pendingAction = () => {
                        updateAdStatus(ad.id, 'Paused');
                        closeModal('confirmModal');
                    };

                    lucide.createIcons();
                    openModal('confirmModal');
                }

                function activateAd(adData) {
                    let ad;

                    if (typeof adData === 'string') {
                        try {
                            ad = JSON.parse(adData);
                        } catch (e) {
                            console.error('[v0] Failed to parse ad data:', e);
                            return;
                        }
                    } else {
                        ad = adData;
                    }

                    document.getElementById('confirmModalTitle').textContent = 'Activate Advertisement';
                    document.getElementById('confirmModalMessage').textContent = 'Are you sure you want to activate this advertisement?';

                    const iconContainer = document.getElementById('confirmModalIcon');
                    iconContainer.className = 'mx-auto w-12 h-12 rounded-full flex items-center justify-center bg-success/10';
                    iconContainer.innerHTML = '<i data-lucide="play-circle" class="h-6 w-6 text-success"></i>';

                    const statusInfo = document.getElementById('confirmModalStatus');
                    statusInfo.innerHTML = `
            <p class="text-sm font-medium mb-1">${ad.title || 'Unknown'}</p>
            <p class="text-xs text-muted-foreground mb-2">${ad.company || 'Unknown Company'}</p>
            <div class="flex items-center justify-between text-xs">
                <span>Current Status:</span>
                <span class="badge badge-${(ad.status || 'unknown').toLowerCase()}">${ad.status || 'Unknown'}</span>
            </div>
            <div class="flex items-center justify-between text-xs mt-1">
                <span>Will Change To:</span>
                <span class="badge badge-active">Active</span>
            </div>
        `;

                    const actionBtn = document.getElementById('confirmModalAction');
                    actionBtn.textContent = 'Activate Ad';
                    actionBtn.className = 'btn btn-success';

                    pendingAction = () => {
                        updateAdStatus(ad.id, 'Active');
                        closeModal('confirmModal');
                    };

                    lucide.createIcons();
                    openModal('confirmModal');
                }

                function updateAdStatus(adId, newStatus) {
                    const tableRow = document.querySelector(`.ad-row[data-ad-id="${adId}"]`);
                    if (tableRow) {
                        const statusBadge = tableRow.querySelector('.badge');
                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                            statusBadge.className = 'badge badge-' + newStatus.toLowerCase();
                        }
                        tableRow.dataset.status = newStatus;
                    }

                    const card = document.querySelector(`.ad-schedule-card[data-ad-id="${adId}"]`);
                    if (card) {
                        const statusBadge = card.querySelector('.badge');
                        if (statusBadge) {
                            statusBadge.textContent = newStatus;
                            statusBadge.className = 'badge badge-' + newStatus.toLowerCase();
                        }
                        card.dataset.status = newStatus;
                    }

                    showNotification(`Advertisement ${newStatus.toLowerCase()} successfully!`, 'success');

                    lucide.createIcons();
                }

                function showNotification(message, type = 'success') {
                    const notification = document.createElement('div');
                    notification.className = `notification notification-${type}`;
                    notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: var(--${type === 'success' ? 'success' : 'warning'});
            color: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    toggleView('table');
                    sortAds();
                    lucide.createIcons();

                    const confirmBtn = document.getElementById('confirmModalAction');
                    if (confirmBtn) {
                        confirmBtn.addEventListener('click', function() {
                            if (pendingAction) {
                                pendingAction();
                                pendingAction = null;
                            }
                        });
                    }

                    const editForm = document.getElementById('editAdForm');
                    if (editForm) {
                        editForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            closeModal('editAdModal');
                            showNotification('Advertisement schedule updated successfully!', 'success');
                        });
                    }
                });

                document.querySelectorAll('.modal-overlay').forEach(overlay => {
                    overlay.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this.id);
                        }
                    });
                });
            </script>

            <style>
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }

                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }

                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }

                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            </style>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
    <script src="../../assets/javascript/admin-moderator/common.js"></script>

</body>

</html>
