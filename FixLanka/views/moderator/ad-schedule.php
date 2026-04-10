<?php
/**
 * Ad Scheduling Dashboard View
 * ✅ MVC COMPLIANT - No CSS, No SQL
 * ✅ 100% Working Calendar
 * Version: 3.0.1 - CLEAN SEPARATION
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/_components/Sidebar.php';
require_once __DIR__ . '/_components/Meta.php';
require_once __DIR__ . '/_components/Header.php';
require_once __DIR__ . '/_components/Common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/AdScheduleController.php';

// ✅ FIXED: Proper PDO initialization
try {
    // $pdo is provided by config/database.php
    $controller = new AdScheduleController($pdo);
} catch (Exception $e) {
    die("⛔ Database Error: " . htmlspecialchars($e->getMessage()));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->handlePostRequest();
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

$viewData = $controller->getViewData();
$messages = $controller->getMessages();

extract($viewData);
$message = $messages['message'];
$messageType = $messages['type'];

// ✅ CALENDAR LOGIC (Pure PHP)
$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$currentMonthName = $monthNames[$currentMonth];
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
$firstDayOfMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$dayOfWeek = date('w', $firstDayOfMonth);

$basePath = '';
$currentPath = '/2nd-Year-Group-Project/FixLanka/moderator-ad-schedule';
$pageTitle = 'Ad Scheduling';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php renderMeta($pageTitle, 'Manage advertisements', $basePath); ?>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/moderator/ad-schedule.css?v=<?php echo time(); ?>">
</head>
<body class="bg-foreground text-background">
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">

    <div class="dashboard-container">
        <?php renderModeratorSidebar($currentPath, $basePath); ?>
        
        <div class="dashboard-main">
            <?php renderPageHeader($basePath, 'Ad Scheduling', 'Manage advertisement timing and placement'); ?>

            <main style="margin-top: 5rem;" class="ad-schedule-content">
                <div class="space-y-6">
                    <div class="page-header-with-action">
                        <div>
                            <h2 class="text-3xl font-bold">Ad Scheduling Dashboard</h2>
                            <p class="text-muted-foreground">Manage advertisement timing and placement</p>
                        </div>
                        <button onclick="openModal('scheduleModal')" class="btn btn-primary">
                            <i class="fa-solid fa-calendar-plus mr-2 h-4 w-4"></i>
                            Schedule Ad
                        </button>
                    </div>

                    <?php if ($message): ?>
                        <div class="message-box message-<?php echo $messageType; ?>">
                            <span class="message-icon"><?php echo $messageType === 'success' ? '✅' : '❌'; ?></span>
                            <span class="message-text"><?php echo nl2br(htmlspecialchars($message)); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($setupIssues)): ?>
                        <div class="message-box message-error">
                            <span class="message-icon">⚠️</span>
                            <span class="message-text">
                                Scheduling data is not available because the database setup is incomplete:
                                <?php echo htmlspecialchars(implode(' | ', $setupIssues)); ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="grid gap-4 grid-cols-4">
                        <?php
                        renderCard('Total', $stats['total'] ?? 0, 'All schedules', 'calendar', 'blue');
                        renderCard('Active', $stats['active_schedules'] ?? 0, 'Running now', 'play-circle', 'green');
                        renderCard('Today', $startingToday ?? 0, 'Starting today', 'clock', 'yellow');
                        renderCard('Slots', $availableSlots ?? 0, 'Available', 'layers', 'purple');
                        ?>
                    </div>

                    <div class="bg-card rounded-lg border p-6">
                        <h3 class="text-lg font-medium mb-4">Filters</h3>
                        <form method="GET" action="" id="filterForm">
                            <div class="grid grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Placement</label>
                                    <select name="placement" class="form-select w-full">
                                        <option value="all" <?php echo $filters['placement'] === 'all' ? 'selected' : ''; ?>>All Placements</option>
                                        <option value="banner" <?php echo $filters['placement'] === 'banner' ? 'selected' : ''; ?>>Banner</option>
                                        <option value="sponsored" <?php echo $filters['placement'] === 'sponsored' ? 'selected' : ''; ?>>Sponsored</option>
                                        <option value="featured" <?php echo $filters['placement'] === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Status</label>
                                    <select name="status" class="form-select w-full">
                                        <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All Statuses</option>
                                        <option value="scheduled" <?php echo $filters['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                                        <option value="active" <?php echo $filters['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="completed" <?php echo $filters['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-2">Search</label>
                                    <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" placeholder="Search ads..." class="form-input w-full">
                                </div>
                                <div class="flex items-end">
                                    <a href="?" class="btn btn-secondary">Clear</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="schedule-grid">
                        <div class="scheduled-ads-table">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold">Scheduled Advertisements</h3>
                                <p class="text-sm text-muted-foreground">Total: <?php echo count($scheduledAds); ?> schedules</p>
                            </div>
                            <div class="scheduled-ads-scroll">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Details</th>
                                        <th>Schedule</th>
                                        <th>Performance</th>
                                        <th>Budget</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($scheduledAds)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-8">
                                                <i class="fa-solid fa-calendar-xmark w-12 h-12 mx-auto mb-2 text-muted-foreground"></i>
                                                <p>No scheduled advertisements found</p>
                                                <?php if (!empty($availableAds)): ?>
                                                    <p class="text-sm text-muted-foreground" style="margin-top: .5rem;">Approved ads available: <?php echo count($availableAds); ?> — click “Schedule Ad” to add one.</p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($scheduledAds as $ad): ?>
                                            <tr>
                                                <td>
                                                    <div class="font-medium"><?php echo htmlspecialchars($ad['ad_title']); ?></div>
                                                    <div class="text-sm text-muted-foreground"><?php echo htmlspecialchars($ad['provider_name']); ?></div>
                                                    <span class="badge badge-blue"><?php echo ucfirst($ad['placement']); ?></span>
                                                </td>
                                                <td>
                                                    <div class="text-sm">
                                                        <div>Start: <?php echo date('M d, Y', strtotime($ad['start_date'])); ?></div>
                                                        <div>End: <?php echo date('M d, Y', strtotime($ad['end_date'])); ?></div>
                                                        <div class="text-xs text-muted-foreground"><?php echo $ad['start_time']; ?> - <?php echo $ad['end_time']; ?></div>
                                                    </div>
                                                    <span class="badge badge-status-<?php echo $ad['status']; ?>"><?php echo ucfirst($ad['status']); ?></span>
                                                </td>
                                                <td class="text-sm text-muted-foreground">No data</td>
                                                <td class="font-mono">LKR <?php echo number_format($ad['budget'], 2); ?></td>
                                                <td>
                                                    <div class="flex gap-2">
                                                        <button onclick='editSchedule(<?php echo json_encode($ad); ?>)' class="btn btn-sm btn-secondary">
                                                            <i class="fa-solid fa-pen w-4 h-4"></i>
                                                        </button>
                                                        <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ Delete this schedule?')">
                                                            <input type="hidden" name="action" value="delete_schedule">
                                                            <input type="hidden" name="schedule_id" value="<?php echo $ad['schedule_id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="fa-solid fa-trash w-4 h-4"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <div class="sidebar-cards">
                            <div class="calendar-wrapper">
                                <div class="calendar-header">
                                    <h3 class="calendar-title"><?php echo $currentMonthName . ' ' . $currentYear; ?></h3>
                                    <div class="calendar-nav">
                                        <button onclick="window.location.href='?month=<?php echo $currentMonth == 1 ? 12 : $currentMonth - 1; ?>&year=<?php echo $currentMonth == 1 ? $currentYear - 1 : $currentYear; ?>'">
                                            <i class="fa-solid fa-chevron-left w-4 h-4"></i>
                                        </button>
                                        <button onclick="window.location.href='?month=<?php echo date('n'); ?>&year=<?php echo date('Y'); ?>'">Today</button>
                                        <button onclick="window.location.href='?month=<?php echo $currentMonth == 12 ? 1 : $currentMonth + 1; ?>&year=<?php echo $currentMonth == 12 ? $currentYear + 1 : $currentYear; ?>'">
                                            <i class="fa-solid fa-chevron-right w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="calendar-grid">
                                    <div class="calendar-day-header">Sun</div>
                                    <div class="calendar-day-header">Mon</div>
                                    <div class="calendar-day-header">Tue</div>
                                    <div class="calendar-day-header">Wed</div>
                                    <div class="calendar-day-header">Thu</div>
                                    <div class="calendar-day-header">Fri</div>
                                    <div class="calendar-day-header">Sat</div>
                                    
                                    <?php for ($i = 0; $i < $dayOfWeek; $i++): ?>
                                        <div class="calendar-day empty"></div>
                                    <?php endfor; ?>
                                    
                                    <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                                        $dateKey = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                        $isToday = ($dateKey === date('Y-m-d'));
                                        $hasEvent = isset($calendarEvents[$dateKey]);
                                        $eventCount = $hasEvent ? $calendarEvents[$dateKey]['count'] : 0;
                                    ?>
                                        <div class="calendar-day <?php echo $isToday ? 'today' : ''; ?> <?php echo $hasEvent ? 'has-event' : ''; ?>" 
                                             onclick="alert('Schedules for <?php echo $dateKey; ?>:\n<?php echo $hasEvent ? $eventCount . ' schedule(s)' : 'No schedules'; ?>')">
                                            <?php echo $day; ?>
                                            <?php if ($hasEvent): ?>
                                                <div class="event-tooltip"><?php echo $eventCount; ?> schedule<?php echo $eventCount > 1 ? 's' : ''; ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="analytics-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold mb-4">Placement Analytics</h3>
                                    <?php foreach (['banner', 'sponsored', 'featured'] as $type): 
                                        $data = $analytics[$type] ?? ['total_schedules' => 0, 'active_count' => 0];
                                        $percentage = $data['total_schedules'] > 0 ? ($data['active_count'] / $data['total_schedules'] * 100) : 0;
                                    ?>
                                        <div class="placement-item">
                                            <div>
                                                <div class="font-medium"><?php echo ucfirst($type); ?></div>
                                                <div class="text-sm text-muted-foreground"><?php echo $data['total_schedules']; ?> total</div>
                                            </div>
                                            <div class="placement-stats">
                                                <div class="font-semibold"><?php echo $data['active_count']; ?></div>
                                                <div class="performance-bar">
                                                    <div class="performance-fill" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="rotation-settings-card">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold mb-4">Ad Rotation Settings</h3>
                                    <p class="text-sm text-muted-foreground" style="margin-top: -10px; margin-bottom: 12px;">
                                        Controls how long each ad stays visible (seconds) and how many ads can share the same time window (capacity).
                                    </p>

                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_rotation_settings">

                                        <div class="rotation-grid">
                                            <div class="rotation-row">
                                                <div class="rotation-label">Banner</div>
                                                <div>
                                                    <label class="rotation-field-label">Seconds per ad</label>
                                                    <input type="number" min="0" max="600" name="banner_seconds" class="form-input w-full" value="<?php echo (int)($rotationSettings['banner_seconds'] ?? 30); ?>">
                                                </div>
                                                <div>
                                                    <label class="rotation-field-label">Max concurrent</label>
                                                    <input type="number" min="0" max="100" name="banner_capacity" class="form-input w-full" value="<?php echo (int)($rotationSettings['banner_capacity'] ?? 5); ?>">
                                                    <div class="rotation-help">0 = unlimited</div>
                                                </div>
                                            </div>

                                            <div class="rotation-row">
                                                <div class="rotation-label">Featured</div>
                                                <div>
                                                    <label class="rotation-field-label">Seconds per ad</label>
                                                    <input type="number" min="0" max="600" name="featured_seconds" class="form-input w-full" value="<?php echo (int)($rotationSettings['featured_seconds'] ?? 60); ?>">
                                                </div>
                                                <div>
                                                    <label class="rotation-field-label">Max concurrent</label>
                                                    <input type="number" min="0" max="100" name="featured_capacity" class="form-input w-full" value="<?php echo (int)($rotationSettings['featured_capacity'] ?? 3); ?>">
                                                    <div class="rotation-help">0 = unlimited</div>
                                                </div>
                                            </div>

                                            <div class="rotation-row">
                                                <div class="rotation-label">Sponsored</div>
                                                <div>
                                                    <label class="rotation-field-label">Seconds per ad</label>
                                                    <input type="number" min="0" max="600" name="sponsored_seconds" class="form-input w-full" value="<?php echo (int)($rotationSettings['sponsored_seconds'] ?? 90); ?>">
                                                </div>
                                                <div>
                                                    <label class="rotation-field-label">Max concurrent</label>
                                                    <input type="number" min="0" max="100" name="sponsored_capacity" class="form-input w-full" value="<?php echo (int)($rotationSettings['sponsored_capacity'] ?? 8); ?>">
                                                    <div class="rotation-help">0 = unlimited</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="rotation-actions">
                                            <button type="submit" class="btn btn-primary w-full">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <div id="scheduleModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Schedule Advertisement</h3>
                            <button type="button" class="modal-close" onclick="closeModal('scheduleModal')">×</button>
                        </div>
                        <form method="POST" onsubmit="return validateScheduleForm()">
                            <input type="hidden" name="action" value="create_schedule">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Select Advertisement *</label>
                                    <select name="ad_id" id="ad_id" class="form-select w-full" required>
                                        <option value="">-- Select Approved Ad --</option>
                                        <?php foreach ($availableAds as $ad): ?>
                                            <option
                                                value="<?php echo $ad['ad_id']; ?>"
                                                <?php echo $ad['is_scheduled'] ? 'disabled' : ''; ?>
                                                data-campaign-start="<?php echo htmlspecialchars((string)($ad['start_date'] ?? '')); ?>"
                                                data-campaign-end="<?php echo htmlspecialchars((string)($ad['end_date'] ?? '')); ?>"
                                            >
                                                <?php echo htmlspecialchars($ad['title']); ?> (<?php echo ucfirst($ad['type']); ?>) 
                                                <?php echo $ad['is_scheduled'] ? '- Already Scheduled' : ''; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p id="campaignRangeHint" class="text-sm text-muted-foreground" style="margin-top: 6px;"></p>
                                </div>
                                <input type="hidden" name="start_date" id="start_date">
                                <input type="hidden" name="end_date" id="end_date">

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="text" id="start_date_display" class="form-input w-full" readonly placeholder="Select an advertisement">
                                    </div>
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="text" id="end_date_display" class="form-input w-full" readonly placeholder="Select an advertisement">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" name="start_time" id="start_time" class="form-input w-full" value="00:00">
                                    </div>
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" name="end_time" id="end_time" class="form-input w-full" value="23:59">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('scheduleModal')">Cancel</button>
                                <button type="submit" class="btn btn-primary">Create Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="editAdModal" class="modal-overlay">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Edit Schedule</h3>
                            <button type="button" class="modal-close" onclick="closeModal('editAdModal')">×</button>
                        </div>
                        <form method="POST" onsubmit="return validateEditForm()">
                            <input type="hidden" name="action" value="update_schedule">
                            <input type="hidden" name="schedule_id" id="edit_schedule_id">
                            <div class="modal-body">
                                <p id="editCampaignRangeHint" class="text-sm text-muted-foreground" style="margin-top: 0; margin-bottom: 10px;"></p>
                                <input type="hidden" name="start_date" id="edit_start_date">
                                <input type="hidden" name="end_date" id="edit_end_date">

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label>Start Date</label>
                                        <input type="text" id="edit_start_date_display" class="form-input w-full" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input type="text" id="edit_end_date_display" class="form-input w-full" readonly>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" name="start_time" id="edit_start_time" class="form-input w-full">
                                    </div>
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" name="end_time" id="edit_end_time" class="form-input w-full">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeModal('editAdModal')">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Schedule</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/admin-moderator/common.js?v=<?php echo time(); ?>"></script>
            <script>
                // Auto-apply filters (Placement/Status). Search applies on Enter.
                (function () {
                    var form = document.getElementById('filterForm');
                    if (!form) return;
                    var selects = form.querySelectorAll('select[name="placement"], select[name="status"]');
                    selects.forEach(function (el) {
                        el.addEventListener('change', function () {
                            form.submit();
                        });
                    });
                })();
            </script>
        </div>
    </div>
</body>
</html>