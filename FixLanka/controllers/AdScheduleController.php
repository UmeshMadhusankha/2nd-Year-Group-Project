<?php
/**
 * AdScheduleController.php
 * Moderator scheduling controller (MVC)
 */

require_once __DIR__ . '/../models/AdScheduleModel.php';

class AdScheduleController
{
    private AdScheduleModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new AdScheduleModel($pdo);
    }

    public function handlePostRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = $_POST['action'] ?? '';

        try {
            switch ($action) {
                case 'create_schedule':
                    $this->createSchedule();
                    break;
                case 'update_schedule':
                    $this->updateSchedule();
                    break;
                case 'delete_schedule':
                    $this->deleteSchedule();
                    break;
				case 'update_rotation_settings':
					$this->updateRotationSettings();
					break;
                default:
                    $this->setMessage('Invalid action.', 'error');
            }
        } catch (Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }
    }

    public function getViewData(): array
    {
        $currentMonth = isset($_GET['month']) ? max(1, min(12, (int)$_GET['month'])) : (int)date('n');
        $currentYear = isset($_GET['year']) ? max(1970, (int)$_GET['year']) : (int)date('Y');

        $filters = [
            'placement' => $_GET['placement'] ?? 'all',
            'status' => $_GET['status'] ?? 'all',
            'search' => trim($_GET['search'] ?? ''),
        ];

        $scheduledAds = $this->model->getSchedules($filters);
        $availableAds = $this->model->getAvailableAds();

        $setupIssues = $this->model->getSetupIssues();

        $calendarEvents = $this->buildCalendarEvents($scheduledAds, $currentYear, $currentMonth);

        $stats = $this->model->getStats();
        $startingToday = $this->model->countStartingToday();

        $availableSlots = 0;
        if (is_array($availableAds)) {
            $availableSlots = (int)count(array_filter($availableAds, function ($ad) {
                return empty($ad['is_scheduled']);
            }));
        }

        $analytics = $this->model->getPlacementAnalytics();
		$rotationSettings = $this->model->getRotationSettings();

        return [
            'setupIssues' => $setupIssues,
            'stats' => $stats,
            'startingToday' => $startingToday,
            'availableSlots' => $availableSlots,
            'filters' => $filters,
            'scheduledAds' => $scheduledAds,
            'availableAds' => $availableAds,
            'calendarEvents' => $calendarEvents,
            'analytics' => $analytics,
			'rotationSettings' => $rotationSettings,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ];
    }

    public function getMessages(): array
    {
        $message = $_SESSION['ad_schedule_message'] ?? '';
        $type = $_SESSION['ad_schedule_message_type'] ?? '';
        unset($_SESSION['ad_schedule_message'], $_SESSION['ad_schedule_message_type']);
        return ['message' => $message, 'type' => $type];
    }

    private function createSchedule(): void
    {
        $adId = (int)($_POST['ad_id'] ?? 0);
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');

        // Moderator cannot edit date range. Model will enforce campaign start/end.
        if ($adId <= 0) {
            throw new InvalidArgumentException('Please select an advertisement.');
        }

		if ($startTime !== '' && $endTime !== '' && $startTime > $endTime) {
			throw new InvalidArgumentException('Start time must be before end time.');
		}

        $startTime = ($startTime === '') ? null : $startTime;
        $endTime = ($endTime === '') ? null : $endTime;

        $this->model->createSchedule($adId, '', '', $startTime, $endTime);
        $this->setMessage('Schedule created successfully.', 'success');
    }

    private function updateSchedule(): void
    {
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');

        // Moderator cannot edit date range. Model will enforce campaign start/end.
        if ($scheduleId <= 0) {
            throw new InvalidArgumentException('Schedule ID is required.');
        }

		if ($startTime !== '' && $endTime !== '' && $startTime > $endTime) {
			throw new InvalidArgumentException('Start time must be before end time.');
		}

        $startTime = ($startTime === '') ? null : $startTime;
        $endTime = ($endTime === '') ? null : $endTime;

        $this->model->updateSchedule($scheduleId, '', '', $startTime, $endTime);
        $this->setMessage('Schedule updated successfully.', 'success');
    }

    private function updateRotationSettings(): void
    {
        $settings = [
            'banner_seconds' => (int)($_POST['banner_seconds'] ?? 0),
            'featured_seconds' => (int)($_POST['featured_seconds'] ?? 0),
            'sponsored_seconds' => (int)($_POST['sponsored_seconds'] ?? 0),
            'banner_capacity' => (int)($_POST['banner_capacity'] ?? 0),
            'featured_capacity' => (int)($_POST['featured_capacity'] ?? 0),
            'sponsored_capacity' => (int)($_POST['sponsored_capacity'] ?? 0),
        ];

        $updatedBy = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
        $this->model->updateRotationSettings($settings, $updatedBy);
        $this->setMessage('Rotation settings updated.', 'success');
    }

    private function deleteSchedule(): void
    {
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        if ($scheduleId <= 0) {
            throw new InvalidArgumentException('Schedule ID is required.');
        }

        $this->model->deleteSchedule($scheduleId);
        $this->setMessage('Schedule deleted successfully.', 'success');
    }

    private function setMessage(string $message, string $type): void
    {
        $_SESSION['ad_schedule_message'] = $message;
        $_SESSION['ad_schedule_message_type'] = $type;
    }

    private function buildCalendarEvents(array $scheduledAds, int $year, int $month): array
    {
        $firstOfMonth = sprintf('%04d-%02d-01', $year, $month);
        $lastOfMonth = date('Y-m-t', strtotime($firstOfMonth));

        $events = [];

        foreach ($scheduledAds as $schedule) {
            $start = $schedule['start_date'] ?? null;
            $end = $schedule['end_date'] ?? null;
            if (!$start || !$end) {
                continue;
            }

            $rangeStart = max($start, $firstOfMonth);
            $rangeEnd = min($end, $lastOfMonth);

            $cursor = strtotime($rangeStart);
            $endTs = strtotime($rangeEnd);
            if ($cursor === false || $endTs === false) {
                continue;
            }

            while ($cursor <= $endTs) {
                $dateKey = date('Y-m-d', $cursor);
                if (!isset($events[$dateKey])) {
                    $events[$dateKey] = ['count' => 0];
                }
                $events[$dateKey]['count']++;
                $cursor = strtotime('+1 day', $cursor);
            }
        }

        return $events;
    }
}
