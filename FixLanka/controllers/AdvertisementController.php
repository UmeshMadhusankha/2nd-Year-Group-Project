<?php

require_once __DIR__ . '/../models/AdvertisementModel.php';
require_once __DIR__ . '/../models/AdScheduleModel.php';

class AdvertisementController
{
	private AdvertisementModel $model;
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
		$this->model = new AdvertisementModel($pdo);
	}

	public function checkTable(): bool
	{
		return $this->model->isReady();
	}

	public function handlePostRequest(): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return;
		}

		$action = trim((string)($_POST['action'] ?? ''));
		$adId = (int)($_POST['ad_id'] ?? 0);
		$notes = trim((string)($_POST['notes'] ?? ''));

		if ($adId <= 0) {
			$this->setMessage('Advertisement ID is required.', 'error');
			return;
		}

		$reviewedBy = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

		try {
			switch ($action) {
				case 'approve':
					$ok = $this->model->reviewAdvertisement($adId, 'approved', $reviewedBy, $notes);
					if ($ok) {
						$autoScheduleMessage = $this->attemptAutoSchedule($adId);
						$msg = 'Advertisement approved.';
						if ($autoScheduleMessage) {
							$msg .= ' ' . $autoScheduleMessage;
						}
						$this->setMessage($msg, 'success');
					} else {
						$this->setMessage('Advertisement not found or already reviewed.', 'error');
					}
					break;
				case 'reject':
					if ($notes === '') {
						$this->setMessage('Please add a short reason before rejecting.', 'error');
						break;
					}
					$ok = $this->model->reviewAdvertisement($adId, 'rejected', $reviewedBy, $notes);
					$this->setMessage($ok ? 'Advertisement rejected.' : 'Advertisement not found or already reviewed.', $ok ? 'success' : 'error');
					break;
				default:
					$this->setMessage('Invalid action.', 'error');
					break;
			}
		} catch (Exception $e) {
			$this->setMessage($e->getMessage(), 'error');
		}
	}

	private function attemptAutoSchedule(int $adId): ?string
	{
		// Auto-scheduling is best-effort. Approval should not fail if scheduling cannot be created.
		try {
			$scheduleModel = new AdScheduleModel($this->pdo);
			if (!$scheduleModel->isReady()) {
				return null;
			}

			$ad = $this->model->getAdvertisementById($adId);
			if (!$ad) {
				return null;
			}

			$campaignStart = (string)($ad['start_date'] ?? '');
			$campaignEnd = (string)($ad['end_date'] ?? '');
			if ($campaignStart === '' || $campaignEnd === '') {
				return 'Scheduling skipped (campaign dates missing).';
			}

			$today = date('Y-m-d');
			if ($today > $campaignEnd) {
				return 'Scheduling skipped (campaign already ended).';
			}

			$startDate = max($today, $campaignStart);
			$endDate = $campaignEnd;

			try {
				$scheduleModel->createSchedule($adId, $startDate, $endDate, '00:00', '23:59');
				return 'Auto-scheduled for the campaign period.';
			} catch (Exception $e) {
				$message = $e->getMessage();
				if (stripos($message, 'overlapping schedule') !== false) {
					return 'Already scheduled.';
				}
				return 'Approved, but scheduling needs manual setup: ' . $message;
			}
		} catch (Exception $e) {
			return 'Approved, but auto-scheduling was not available.';
		}
	}

	public function review(int $adId): ?array
	{
		return $this->model->getAdvertisementById($adId);
	}

	public function getViewData(): array
	{
		$filters = [
			'status' => isset($_GET['status']) ? trim((string)$_GET['status']) : '',
			'type' => isset($_GET['type']) ? trim((string)$_GET['type']) : '',
			'search' => isset($_GET['search']) ? trim((string)$_GET['search']) : '',
		];

		return [
			'stats' => $this->model->getStats(),
			'ads' => $this->model->getAdvertisements($filters),
			'filters' => $filters,
		];
	}

	public function getMessages(): array
	{
		$message = $_SESSION['moderator_ads_message'] ?? '';
		$type = $_SESSION['moderator_ads_message_type'] ?? '';
		unset($_SESSION['moderator_ads_message'], $_SESSION['moderator_ads_message_type']);
		return ['message' => $message, 'type' => $type];
	}

	public function getStatusExplanation(string $status): string
	{
		$status = strtolower(trim($status));
		return match ($status) {
			'pending' => 'This advertisement is waiting for moderator review.',
			'approved' => 'This advertisement has been approved and may be scheduled/activated.',
			'rejected' => 'This advertisement was rejected and will not be shown.',
			'scheduled' => 'This advertisement is approved and scheduled to run on a future date.',
			'active' => 'This advertisement is currently active.',
			'paused' => 'This advertisement is paused and not currently shown.',
			'inactive' => 'This advertisement is inactive and not currently shown.',
			'suspended' => 'This advertisement is suspended due to policy or compliance reasons.',
			default => 'No additional details are available for this status.',
		};
	}

	private function setMessage(string $message, string $type): void
	{
		$_SESSION['moderator_ads_message'] = $message;
		$_SESSION['moderator_ads_message_type'] = $type;
	}
}

