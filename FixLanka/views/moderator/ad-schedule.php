<?php
/**
 * Ad Scheduling Dashboard View
 * Proper MVC Architecture - View Layer (Display Only)
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include dependencies
require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';

// Initialize Controller
require_once __DIR__ . '/../../controllers/AdScheduleController.php';

global $pdo;
$controller = new AdScheduleController($pdo);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handlePostRequest();
    // Redirect to prevent form resubmission
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

// Get data from controller
$viewData = $controller->getViewData();
$messages = $controller->getMessages();

// Extract variables
extract($viewData);
$message = $messages['message'];
$messageType = $messages['type'];

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-ad-schedule';
$pageTitle = 'Ad Scheduling';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php renderMeta($pageTitle, 'Manage advertisements', $basePath); ?>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/ad-schedule.css">
            <?php renderPageHeader($basePath, 'Ad Scheduling', 'Manage advertisement timing and placement'); ?>

            <main style="margin-top: 5rem;" class="ad-schedule-content">
                <div class="space-y-6">
                    <div class="page-header-with-action">
                        <div>
                            <h2 class="text-3xl font-bold">Ad Scheduling Dashboard</h2>
                            <p class="text-muted-foreground">Manage advertisement timing and placement</p>
                        </div>
                        <button onclick="openModal('scheduleModal')" class="btn btn-primary">
                            <i data-lucide="calendar-plus" class="mr-2 h-4 w-4"></i>
                            Schedule Ad
                        </button>
                    </div>

                    <!-- IMPROVED ERROR/SUCCESS MESSAGE DISPLAY -->
                    <?php if ($message): ?>
                        <div style="
                            padding: 1rem 1.5rem;
                            border-radius: 8px;
                            margin-bottom: 1.5rem;
                            border: 2px solid <?php echo $messageType === 'success' ? '#10b981' : '#ef4444'; ?>;
                            background-color: <?php echo $messageType === 'success' ? '#d1fae5' : '#fee2e2'; ?>;
                            color: <?php echo $messageType === 'success' ? '#065f46' : '#991b1b'; ?>;
                            font-weight: 500;
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                        ">
                            <?php if ($messageType === 'success'): ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            <?php else: ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            <?php endif; ?>
                            <span style="flex: 1;"><?php echo htmlspecialchars($message); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-4 grid-cols-4">
                        <?php
                        renderCard('Total', $stats['total'] ?? 0, 'All ads', 'calendar', 'blue');
                        renderCard('Active', $stats['active'] ?? 0, 'Running', 'play-circle', 'green');
                        renderCard('Today', $stats['starting_today'] ?? 0, 'New', 'clock', 'yellow');
                        renderCard('Slots', $availableSlots ?? 0, 'Available', 'calendar-days', 'purple');
                        ?>
                    </div>

                    <div class="bg-card rounded-lg border p-6">
                        <h3 class="text-lg font-medium mb-4">Filters</h3>
                        <form method="GET" action="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" id="filterForm">
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Sort</label>
                                    <select name="sort" class="form-select" onchange="this.form.submit()">
                                        <option value="newest" <?php echo ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                        <option value="oldest" <?php echo ($filters['sort'] ?? '') === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Placement</label>
                                    <select name="placement" class="form-select" onchange="this.form.submit()">
                                        <option value="all">All</option>
                                        <option value="banner" <?php echo ($filters['placement'] ?? '') === 'banner' ? 'selected' : ''; ?>>Banner</option>
                                        <option value="featured" <?php echo ($filters['placement'] ?? '') === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                        <option value="sponsored" <?php echo ($filters['placement'] ?? '') === 'sponsored' ? 'selected' : ''; ?>>Sponsored</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Search</label>
                                    <input type="text" name="search" class="form-input" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" placeholder="Search...">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="btn btn-sm btn-primary">Apply Filters</button>
                                    <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="btn btn-sm btn-outline ml-2">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="schedule-grid">
                        <div class="scheduled-ads-table">
                            <div class="p-6">
                                <h3 class="text-lg font-medium">Scheduled Advertisements</h3>
                                <p class="text-sm text-muted-foreground">Total: <?php echo count($scheduledAds ?? []); ?> ads</p>
                            </div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox"></th>
                                        <th>DETAILS</th>
                                        <th>SCHEDULE</th>
                                        <th>PERFORMANCE</th>
                                        <th>BUDGET</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($scheduledAds) && count($scheduledAds) > 0): ?>
                                        <?php foreach ($scheduledAds as $ad): ?>
                                            <tr>
                                                <td><input type="checkbox"></td>
                                                <td>
                                                    <div class="font-medium"><?php echo htmlspecialchars($ad['title'] ?? 'Untitled'); ?></div>
                                                    <div class="text-sm text-muted-foreground"><?php echo htmlspecialchars($ad['company_name'] ?? 'Unknown'); ?></div>
                                                    <span class="badge badge-<?php echo strtolower($ad['placement'] ?? 'banner'); ?>"><?php echo ucfirst($ad['placement'] ?? 'banner'); ?></span>
                                                </td>
                                                <td>
                                                    <div class="text-sm">
                                                        <div><strong>Start:</strong> <?php echo date('M d, Y', strtotime($ad['start_date'])); ?></div>
                                                        <div><strong>End:</strong> <?php echo date('M d, Y', strtotime($ad['end_date'])); ?></div>
                                                        <?php if ($ad['start_time']): ?>
                                                            <div class="text-xs text-muted-foreground">
                                                                <?php echo date('h:i A', strtotime($ad['start_time'])); ?> - <?php echo date('h:i A', strtotime($ad['end_time'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="badge badge-<?php echo strtolower($ad['status'] ?? 'scheduled'); ?>">
                                                        <?php echo ucfirst($ad['status'] ?? 'scheduled'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-sm text-muted-foreground">No data</div>
                                                </td>
                                                <td>
                                                    <div class="font-medium">LKR <?php echo number_format($ad['budget'] ?? 0, 2); ?></div>
                                                </td>
                                                <td>
                                                    <div class="flex gap-2">
                                                        <button onclick='editSchedule(<?php echo json_encode($ad); ?>)' class="btn btn-sm btn-primary" title="Edit">
                                                            <i data-lucide="edit-2" class="h-4 w-4"></i>
                                                        </button>
                                                        <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                                            <input type="hidden" name="action" value="delete_schedule">
                                                            <input type="hidden" name="schedule_id" value="<?php echo $ad['schedule_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-8">
                                                <p class="text-muted-foreground">No advertisements found</p>
                                                <p class="text-sm text-muted-foreground mt-2">Click "Schedule Ad" to create your first advertisement</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="sidebar-cards">
                            <div class="calendar-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium">Calendar</h3>
                                    <div class="mt-4">
                                        <div class="flex justify-between items-center mb-4">
                                            <?php
                                            $prevMonth = ($currentMonth == 1) ? 12 : $currentMonth - 1;
                                            $prevYear = ($currentMonth == 1) ? $currentYear - 1 : $currentYear;
                                            $nextMonth = ($currentMonth == 12) ? 1 : $currentMonth + 1;
                                            $nextYear = ($currentMonth == 12) ? $currentYear + 1 : $currentYear;
                                            
                                            $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
                                            ?>
                                            <a href="<?php echo $baseUrl . '?month=' . $prevMonth . '&year=' . $prevYear; ?>" class="btn btn-sm btn-secondary">
                                                <i data-lucide="chevron-left"></i>
                                            </a>
                                            <h4 class="font-medium">
                                                <?php 
                                                $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                                echo $monthNames[$currentMonth] . ' ' . $currentYear; 
                                                ?>
                                            </h4>
                                            <a href="<?php echo $baseUrl . '?month=' . $nextMonth . '&year=' . $nextYear; ?>" class="btn btn-sm btn-secondary">
                                                <i data-lucide="chevron-right"></i>
                                            </a>
                                        </div>
                                        <div class="calendar-grid">
                                            <?php
                                            $dayNames = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                                            foreach ($dayNames as $d) {
                                                echo '<div class="text-xs text-center font-bold text-muted-foreground">' . $d . '</div>';
                                            }
                                            
                                            $firstDayOfMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
                                            $daysInMonth = date('t', $firstDayOfMonth);
                                            $dayOfWeek = date('w', $firstDayOfMonth);
                                            
                                            for ($i = 0; $i < $dayOfWeek; $i++) {
                                                echo '<div class="calendar-day"></div>';
                                            }
                                            
                                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                                $events = $calendarEvents[$dateStr] ?? [];
                                                $hasEvents = count($events) > 0;
                                                
                                                echo '<div class="calendar-day' . ($hasEvents ? ' has-event' : '') . '">';
                                                echo '<div class="text-sm">' . $day . '</div>';
                                                if ($hasEvents) {
                                                    echo '<div class="mt-1 flex gap-1 justify-center flex-wrap">';
                                                    foreach ($events as $event) {
                                                        $color = $event['placement'] === 'banner' ? 'bg-green-500' : ($event['placement'] === 'featured' ? 'bg-blue-500' : 'bg-purple-500');
                                                        echo '<div class="w-2 h-2 rounded-full ' . $color . '" title="' . htmlspecialchars($event['title']) . '"></div>';
                                                    }
                                                    echo '</div>';
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <div class="mt-4 flex gap-3 text-xs">
                                            <div class="flex items-center gap-1">
                                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                                <span>Banner</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                                <span>Featured</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                                <span>Sponsored</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="analytics-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium mb-4">Placement Analytics</h3>
                                    <?php if (isset($analytics) && is_array($analytics) && count($analytics) > 0): ?>
                                        <?php foreach (['banner', 'featured', 'sponsored'] as $type): ?>
                                            <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-lg shadow-sm">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-3 h-3 rounded-full <?php echo $type === 'banner' ? 'bg-green-500' : ($type === 'featured' ? 'bg-blue-500' : 'bg-purple-500'); ?>"></div>
                                                        <h4 class="font-semibold text-gray-800"><?php echo ucfirst($type); ?></h4>
                                                    </div>
                                                    <span class="text-2xl font-bold text-blue-600">
                                                        <?php echo isset($analytics[$type]) ? $analytics[$type]['active'] : 0; ?>
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-600 mt-1">
                                                    <span class="font-medium">Active schedules</span>
                                                </div>
                                                <div class="mt-3 w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full" 
                                                         style="width: <?php echo min(100, (isset($analytics[$type]) ? $analytics[$type]['active'] : 0) * 10); ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-8">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">No active schedules yet</p>
                                            <p class="text-xs text-gray-400 mt-1">Schedule ads to see analytics</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Create Modal -->
            <div id="scheduleModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Schedule Advertisement</h3>
                            <button type="button" class="modal-close" onclick="closeModal('scheduleModal')">
                                <i data-lucide="x"></i>
                            </button>
                        </div>
                        <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                            <input type="hidden" name="action" value="create_schedule">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="form-label">Select Advertisement <span class="text-red-500">*</span></label>
                                    <select name="ad_id" required class="form-select">
                                        <option value="">Choose an advertisement...</option>
                                        <?php if (isset($availableAds) && count($availableAds) > 0): ?>
                                            <?php foreach ($availableAds as $ad): ?>
                                                <option value="<?php echo $ad['ad_id']; ?>">
                                                    #<?php echo $ad['ad_id']; ?> - <?php echo htmlspecialchars($ad['title']); ?> 
                                                    (<?php echo ucfirst($ad['type']); ?>) - LKR <?php echo number_format($ad['budget'], 2); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No approved/active ads available</option>
                                        <?php endif; ?>
                                    </select>
                                    <small class="text-gray-500">Only approved and active advertisements can be scheduled</small>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Start Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="start_date" required class="form-input" 
                                               value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Date <span class="text-red-500">*</span></label>
                                        <input type="date" name="end_date" required class="form-input" 
                                               value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" 
                                               min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Start Time (Optional)</label>
                                        <input type="time" name="start_time" class="form-input" value="00:00">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Time (Optional)</label>
                                        <input type="time" name="end_time" class="form-input" value="23:59">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('scheduleModal')" class="btn btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="calendar-plus" class="mr-2"></i>
                                    Create Schedule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div id="editAdModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Edit Schedule</h3>
                            <button type="button" class="modal-close" onclick="closeModal('editAdModal')">
                                <i data-lucide="x"></i>
                            </button>
                        </div>
                        <form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                            <input type="hidden" name="action" value="update_schedule">
                            <input type="hidden" id="edit_schedule_id" name="schedule_id">
                            <div class="modal-body">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" id="edit_start_date" name="start_date" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Date</label>
                                        <input type="date" id="edit_end_date" name="end_date" class="form-input">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label class="form-label">Start Time</label>
                                        <input type="time" id="edit_start_time" name="start_time" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">End Time</label>
                                        <input type="time" id="edit_end_time" name="end_time" class="form-input">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="closeModal('editAdModal')" class="btn btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <i data-lucide="save" class="mr-2"></i>
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                lucide.createIcons();

                function openModal(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeModal(id) {
                    const modal = document.getElementById(id);
                    if (modal) {
                        modal.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                }

                function editSchedule(ad) {
                    if (!ad || !ad.schedule_id) return;
                    
                    document.getElementById('edit_schedule_id').value = ad.schedule_id || '';
                    document.getElementById('edit_start_date').value = ad.start_date || '';
                    document.getElementById('edit_end_date').value = ad.end_date || '';
                    document.getElementById('edit_start_time').value = ad.start_time || '';
                    document.getElementById('edit_end_time').value = ad.end_time || '';
                    
                    openModal('editAdModal');
                }

                document.querySelectorAll('.modal-overlay').forEach(modal => {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            closeModal(this.id);
                        }
                    });
                });

                document.addEventListener('DOMContentLoaded', function() {
                    lucide.createIcons();
                });
            </script>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>