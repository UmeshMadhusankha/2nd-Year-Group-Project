<?php
/**
 * AdScheduleController.php
 * ✅ 100% CRASH-PROOF CONTROLLER
 * ✅ Business logic and validations only
 * Version: 3.0.1 - FIXED CALENDAR DATA
 */

// ✅ CRASH PROTECTION
ini_set('memory_limit', '256M');
set_time_limit(30);

class AdScheduleController
{
    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/AdScheduleModel.php';
        $this->model = new AdScheduleModel($this->pdo);

        if (!$this->model->tableExists()) {
            $this->model->createTableIfNotExists();
        }

        $this->model->autoUpdateStatuses();
    }

    public function handlePostRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'create_schedule':
                    return $this->createSchedule();
                case 'update_schedule':
                    return $this->updateSchedule();
                case 'delete_schedule':
                    return $this->deleteSchedule();
                default:
                    $_SESSION['error_message'] = '❌ Invalid action';
                    return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            error_log("Controller Error: " . $e->getMessage());
            return false;
        }
    }

    private function createSchedule()
    {
        if (empty($_POST['ad_id'])) {
            $_SESSION['error_message'] = '❌ Please select an advertisement';
            return false;
        }

        $ad_id = intval($_POST['ad_id']);
        $adDetails = $this->model->getAdvertisementById($ad_id);
        
        if (!$adDetails) {
            $_SESSION['error_message'] = '❌ Advertisement not found';
            return false;
        }

        if (strtolower($adDetails['status']) !== 'approved') {
            $_SESSION['error_message'] = '❌ Only APPROVED advertisements can be scheduled';
            return false;
        }

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $_SESSION['error_message'] = '❌ Start date and end date are required';
            return false;
        }

        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $today = date('Y-m-d');

        if ($start_date < $today) {
            $_SESSION['error_message'] = '❌ Start date cannot be in the past';
            return false;
        }

        if (strtotime($end_date) < strtotime($start_date)) {
            $_SESSION['error_message'] = '❌ End date must be after start date';
            return false;
        }

        $data = [
            'ad_id' => $ad_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_time' => !empty($_POST['start_time']) ? $_POST['start_time'] : '00:00:00',
            'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : '23:59:59'
        ];

        try {
            if ($this->model->createSchedule($data)) {
                $_SESSION['success_message'] = '✅ Advertisement scheduled successfully!';
                return true;
            } else {
                $_SESSION['error_message'] = '❌ Failed to create schedule';
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            return false;
        }
    }

    private function updateSchedule()
    {
        if (empty($_POST['schedule_id'])) {
            $_SESSION['error_message'] = '❌ Schedule ID is required';
            return false;
        }

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $_SESSION['error_message'] = '❌ Start date and end date are required';
            return false;
        }

        $today = date('Y-m-d');
        if ($_POST['start_date'] < $today) {
            $_SESSION['error_message'] = '❌ Start date cannot be in the past';
            return false;
        }

        if (strtotime($_POST['end_date']) < strtotime($_POST['start_date'])) {
            $_SESSION['error_message'] = '❌ End date must be after start date';
            return false;
        }

        $schedule_id = intval($_POST['schedule_id']);
        $data = [
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'start_time' => $_POST['start_time'] ?? '00:00:00',
            'end_time' => $_POST['end_time'] ?? '23:59:59'
        ];

        try {
            if ($this->model->updateSchedule($schedule_id, $data)) {
                $_SESSION['success_message'] = '✅ Schedule updated successfully!';
                return true;
            } else {
                $_SESSION['error_message'] = '❌ Failed to update schedule';
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            return false;
        }
    }

    private function deleteSchedule()
    {
        if (empty($_POST['schedule_id'])) {
            $_SESSION['error_message'] = '❌ Schedule ID is required';
            return false;
        }

        $schedule_id = intval($_POST['schedule_id']);

        try {
            if ($this->model->deleteSchedule($schedule_id)) {
                $_SESSION['success_message'] = '✅ Schedule deleted successfully!';
                return true;
            } else {
                $_SESSION['error_message'] = '❌ Failed to delete schedule';
                return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            return false;
        }
    }

    public function getViewData()
    {
        $filters = [
            'placement' => $_GET['placement'] ?? 'all',
            'status' => $_GET['status'] ?? 'all',
            'search' => $_GET['search'] ?? ''
        ];

        $stats = $this->model->getStatistics();
        $scheduledAds = $this->model->getScheduledAds($filters);
        $availableAds = $this->model->getApprovedAdvertisements();
        $analyticsArray = $this->model->getPlacementAnalytics();
        
        $analytics = [];
        foreach ($analyticsArray as $item) {
            $placement = strtolower($item['placement']);
            $analytics[$placement] = $item;
        }
        
        $currentMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
        $currentYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
        
        if ($currentMonth < 1 || $currentMonth > 12) {
            $currentMonth = date('n');
        }
        
        // ✅ FIXED: Proper calendar event data structure
        $calendarEventsArray = $this->model->getCalendarEvents($currentMonth, $currentYear);
        $calendarEvents = [];
        foreach ($calendarEventsArray as $event) {
            $date = $event['event_date'];
            $calendarEvents[$date] = [
                'count' => $event['event_count'],
                'titles' => isset($event['event_titles']) ? explode('||', $event['event_titles']) : []
            ];
        }
        
        $startingToday = $this->model->getStartingTodayCount();
        $availableSlots = max(0, 24 - ($stats['active_schedules'] ?? 0));

        return [
            'stats' => $stats,
            'scheduledAds' => $scheduledAds,
            'availableAds' => $availableAds,
            'analytics' => $analytics,
            'placementData' => $analyticsArray,
            'calendarEvents' => $calendarEvents,
            'availableSlots' => $availableSlots,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'filters' => $filters,
            'startingToday' => $startingToday
        ];
    }

    public function getMessages()
    {
        $success = $_SESSION['success_message'] ?? '';
        $error = $_SESSION['error_message'] ?? '';
        
        unset($_SESSION['success_message'], $_SESSION['error_message']);
        
        return [
            'message' => $success ?: $error,
            'type' => $success ? 'success' : ($error ? 'error' : '')
        ];
    }
}