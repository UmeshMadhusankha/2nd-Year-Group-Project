<?php
/**
 * AdScheduleController.php
 * Fixed version with proper analytics and calendar data structure
 */

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
                    $_SESSION['error_message'] = 'Invalid action';
                    return false;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            return false;
        }
    }

    private function createSchedule()
    {
        if (empty($_POST['ad_id'])) {
            $_SESSION['error_message'] = 'Please select an advertisement';
            return false;
        }

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $_SESSION['error_message'] = 'Start date and end date are required';
            return false;
        }

        if (strtotime($_POST['end_date']) < strtotime($_POST['start_date'])) {
            $_SESSION['error_message'] = 'End date must be after start date';
            return false;
        }

        $data = [
            'ad_id' => intval($_POST['ad_id']),
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'start_time' => !empty($_POST['start_time']) ? $_POST['start_time'] : null,
            'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : null
        ];

        try {
            if ($this->model->createSchedule($data)) {
                $_SESSION['success_message'] = 'Advertisement scheduled successfully!';
                return true;
            } else {
                $_SESSION['error_message'] = 'Failed to schedule advertisement';
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
            $_SESSION['error_message'] = 'Schedule ID is required';
            return false;
        }

        if (empty($_POST['start_date']) || empty($_POST['end_date'])) {
            $_SESSION['error_message'] = 'Start date and end date are required';
            return false;
        }

        if (strtotime($_POST['end_date']) < strtotime($_POST['start_date'])) {
            $_SESSION['error_message'] = 'End date must be after start date';
            return false;
        }

        $schedule_id = intval($_POST['schedule_id']);
        $data = [
            'start_date' => $_POST['start_date'],
            'end_date' => $_POST['end_date'],
            'start_time' => $_POST['start_time'] ?? null,
            'end_time' => $_POST['end_time'] ?? null
        ];

        try {
            if ($this->model->updateSchedule($schedule_id, $data)) {
                $_SESSION['success_message'] = 'Schedule updated successfully!';
                return true;
            } else {
                $_SESSION['error_message'] = 'Failed to update schedule';
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
            $_SESSION['error_message'] = 'Schedule ID is required';
            return false;
        }

        $schedule_id = intval($_POST['schedule_id']);

        if ($this->model->deleteSchedule($schedule_id)) {
            $_SESSION['success_message'] = 'Schedule deleted successfully!';
            return true;
        } else {
            $_SESSION['error_message'] = 'Failed to delete schedule';
            return false;
        }
    }

    public function getViewData()
    {
        $filters = [
            'placement' => $_GET['placement'] ?? 'all',
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
            'search' => $_GET['search'] ?? '',
            'sort' => $_GET['sort'] ?? 'newest',
            'status' => $_GET['status'] ?? '',
            'priority' => $_GET['priority'] ?? ''
        ];

        $stats = $this->model->getStatistics();
        $scheduledAds = $this->model->getScheduledAds($filters);
        $availableAds = $this->model->getAdvertisements(['type' => 'all']);
        $analyticsArray = $this->model->getPlacementAnalytics();
        
        // FIXED: Convert analytics array to associative array by placement
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
        
        // FIXED: Get calendar events and organize by date
        $calendarEventsArray = $this->model->getCalendarEvents($currentMonth, $currentYear);
        $calendarEvents = [];
        foreach ($calendarEventsArray as $event) {
            $date = $event['start'];
            if (!isset($calendarEvents[$date])) {
                $calendarEvents[$date] = [];
            }
            $calendarEvents[$date][] = $event;
        }
        
        $availableSlots = max(0, 24 - ($stats['active_schedules'] ?? 0));

        return [
            'stats' => [
                'total' => $stats['total_schedules'],
                'active' => $stats['active_schedules'],
                'starting_today' => 0
            ],
            'scheduledAds' => $scheduledAds,
            'availableAds' => $availableAds,
            'analytics' => $analytics,
            'placementData' => $analyticsArray,
            'calendarEvents' => $calendarEvents,
            'availableSlots' => $availableSlots,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'filters' => $filters
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