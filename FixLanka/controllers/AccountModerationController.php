<?php
/**
 * AccountModerationController.php
 * Business logic and validation layer
 */

class AccountModerationController
{
    private $pdo;
    private $model;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        require_once __DIR__ . '/../models/AccountModerationModel.php';
        $this->model = new AccountModerationModel($pdo);
    }

    public function handleRequest()
    {
        // Check admin authentication
        if (!$this->isAdmin()) {
            $_SESSION['error_message'] = 'Unauthorized access. Admin login required.';
            header('Location: /2nd-Year-Group-Project/FixLanka/views/admin/login.php');
            exit();
        }

        // Route to appropriate action
        $action = $_GET['action'] ?? $_POST['action'] ?? 'index';

        switch ($action) {
            case 'index':
                $this->showPage();
                break;
            
            case 'ban':
                $this->handleBan();
                break;
            
            case 'suspend':
                $this->handleSuspend();
                break;
            
            case 'restore':
                $this->handleRestore();
                break;
            
            default:
                $this->showPage();
                break;
        }
    }

    /**
     * Display main page with data
     */
    private function showPage()
    {
        try {
            // Auto-restore expired suspensions
            $this->model->autoRestoreExpiredSuspensions();

            // Get filter parameters from URL
            $search = trim($_GET['search'] ?? '');
            $statusFilter = $_GET['status'] ?? '';
            $roleFilter = $_GET['role'] ?? '';
            $sortFilter = $_GET['sort'] ?? 'newest';
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = max(10, min(100, intval($_GET['limit'] ?? 10)));
            $offset = ($page - 1) * $limit;

            // Get statistics
            $stats = $this->model->getStatistics();

            // Get accounts with filters
            $result = $this->model->getAllAccounts($search, $statusFilter, $roleFilter, $sortFilter, $limit, $offset);
            $accounts = $result['accounts'];
            $totalAccounts = $result['total'];
            $totalPages = ceil($totalAccounts / $limit);

            // Get all accounts for dropdown
            $allAccountsForDropdown = $this->model->getAllAccountsForDropdown();

            // Get flash messages
            $successMessage = $_SESSION['success_message'] ?? '';
            $errorMessage = $_SESSION['error_message'] ?? '';
            unset($_SESSION['success_message'], $_SESSION['error_message']);

            // Include view
            include __DIR__ . '/../views/admin/account-moderation.php';

        } catch (Exception $e) {
            error_log("Controller Error: " . $e->getMessage());
            error_log("Stack Trace: " . $e->getTraceAsString());
            $_SESSION['error_message'] = 'An error occurred: ' . $e->getMessage();
            $this->redirect();
        }
    }

    /**
     * Handle ban account request
     */
    private function handleBan()
    {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method.';
            $this->redirect();
        }

        // Get and validate inputs
        $accountId = trim($_POST['account_id'] ?? '');
        $accountType = trim($_POST['account_type'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        // Validation
        if (empty($accountId) || !is_numeric($accountId)) {
            $_SESSION['error_message'] = 'Invalid account ID.';
            $this->redirect();
        }

        if (!in_array($accountType, ['User', 'Repairer', 'Company'])) {
            $_SESSION['error_message'] = 'Invalid account type.';
            $this->redirect();
        }

        if (empty($reason) || strlen($reason) < 10) {
            $_SESSION['error_message'] = 'Reason must be at least 10 characters long.';
            $this->redirect();
        }

        try {
            $this->pdo->beginTransaction();

            // Get current account status
            $currentAccount = $this->model->getAccountById($accountId, $accountType);
            if (!$currentAccount) {
                throw new Exception("Account not found");
            }

            $statusBefore = $currentAccount['account_status'];
            $adminUsername = $_SESSION['admin_username'] ?? 'admin';

            // Ban the account
            $this->model->banAccount($accountId, $accountType, $reason, $notes, $adminUsername);

            // Log moderation case
            $this->model->logModerationCase(
                $accountId,
                $accountType,
                $adminUsername,
                'BAN',
                $reason,
                $notes,
                null,
                date('Y-m-d H:i:s'),
                null,
                $statusBefore,
                'BANNED',
                1
            );

            // Create admin notification
            $this->model->createAdminNotification(
                $adminUsername,
                'ACCOUNT_BANNED',
                'Account Permanently Banned',
                "Account {$currentAccount['name']} ({$accountType}) has been permanently banned. Reason: {$reason}",
                $accountId,
                $accountType
            );

            $this->pdo->commit();

            $_SESSION['success_message'] = "Account permanently banned successfully.";
            $this->redirect();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Ban Error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to ban account: ' . $e->getMessage();
            $this->redirect();
        }
    }

    /**
     * Handle suspend account request
     */
    private function handleSuspend()
    {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method.';
            $this->redirect();
        }

        // Get and validate inputs
        $accountId = trim($_POST['account_id'] ?? '');
        $accountType = trim($_POST['account_type'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        $duration = trim($_POST['duration'] ?? '');
        $customDays = trim($_POST['custom_days'] ?? '');

        // Validation
        if (empty($accountId) || !is_numeric($accountId)) {
            $_SESSION['error_message'] = 'Invalid account ID.';
            $this->redirect();
        }

        if (!in_array($accountType, ['User', 'Repairer', 'Company'])) {
            $_SESSION['error_message'] = 'Invalid account type.';
            $this->redirect();
        }

        if (empty($reason) || strlen($reason) < 10) {
            $_SESSION['error_message'] = 'Reason must be at least 10 characters long.';
            $this->redirect();
        }

        // Calculate duration
        $durationDays = 0;
        if ($duration === 'custom') {
            if (empty($customDays) || !is_numeric($customDays) || $customDays < 1) {
                $_SESSION['error_message'] = 'Invalid custom duration. Must be at least 1 day.';
                $this->redirect();
            }
            $durationDays = intval($customDays);
        } elseif (in_array($duration, ['1', '3', '7', '14', '30'])) {
            $durationDays = intval($duration);
        } else {
            $_SESSION['error_message'] = 'Invalid duration selected.';
            $this->redirect();
        }

        try {
            $this->pdo->beginTransaction();

            // Get current account status
            $currentAccount = $this->model->getAccountById($accountId, $accountType);
            if (!$currentAccount) {
                throw new Exception("Account not found");
            }

            $statusBefore = $currentAccount['account_status'];
            $adminUsername = $_SESSION['admin_username'] ?? 'admin';
            $startDate = date('Y-m-d H:i:s');
            $endDate = date('Y-m-d H:i:s', strtotime("+$durationDays days"));

            // Suspend the account
            $this->model->suspendAccount($accountId, $accountType, $reason, $notes, $endDate, $adminUsername);

            // Log moderation case
            $this->model->logModerationCase(
                $accountId,
                $accountType,
                $adminUsername,
                'SUSPEND',
                $reason,
                $notes,
                $durationDays,
                $startDate,
                $endDate,
                $statusBefore,
                'SUSPENDED',
                0
            );

            // Create admin notification
            $this->model->createAdminNotification(
                $adminUsername,
                'ACCOUNT_SUSPENDED',
                'Account Suspended',
                "Account {$currentAccount['name']} ({$accountType}) has been suspended for {$durationDays} days until {$endDate}. Reason: {$reason}",
                $accountId,
                $accountType
            );

            $this->pdo->commit();

            $_SESSION['success_message'] = "Account suspended for {$durationDays} days successfully.";
            $this->redirect();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Suspend Error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to suspend account: ' . $e->getMessage();
            $this->redirect();
        }
    }

    /**
     * Handle restore account request
     */
    private function handleRestore()
    {
        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error_message'] = 'Invalid request method.';
            $this->redirect();
        }

        // Get and validate inputs
        $accountId = trim($_POST['account_id'] ?? '');
        $accountType = trim($_POST['account_type'] ?? '');

        // Validation
        if (empty($accountId) || !is_numeric($accountId)) {
            $_SESSION['error_message'] = 'Invalid account ID.';
            $this->redirect();
        }

        if (!in_array($accountType, ['User', 'Repairer', 'Company'])) {
            $_SESSION['error_message'] = 'Invalid account type.';
            $this->redirect();
        }

        // Check if permanently banned
        if ($this->model->isPermanentlyBanned($accountId, $accountType)) {
            $_SESSION['error_message'] = 'Cannot restore permanently banned account. Please contact system administrator.';
            $this->redirect();
        }

        try {
            $this->pdo->beginTransaction();

            // Get current account status
            $currentAccount = $this->model->getAccountById($accountId, $accountType);
            if (!$currentAccount) {
                throw new Exception("Account not found");
            }

            $statusBefore = $currentAccount['account_status'];
            $adminUsername = $_SESSION['admin_username'] ?? 'admin';

            // Restore the account
            $this->model->restoreAccount($accountId, $accountType, $adminUsername);

            // Log moderation case
            $this->model->logModerationCase(
                $accountId,
                $accountType,
                $adminUsername,
                'RESTORE',
                'Account restored by admin',
                'Account has been restored to active status',
                null,
                date('Y-m-d H:i:s'),
                null,
                $statusBefore,
                'ACTIVE',
                0
            );

            // Create admin notification
            $this->model->createAdminNotification(
                $adminUsername,
                'ACCOUNT_RESTORED',
                'Account Restored',
                "Account {$currentAccount['name']} ({$accountType}) has been restored to active status.",
                $accountId,
                $accountType
            );

            $this->pdo->commit();

            $_SESSION['success_message'] = "Account restored successfully.";
            $this->redirect();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Restore Error: " . $e->getMessage());
            $_SESSION['error_message'] = 'Failed to restore account: ' . $e->getMessage();
            $this->redirect();
        }
    }

    /**
     * Check if user is admin
     */
    private function isAdmin()
    {
        return isset($_SESSION['admin_username']) || isset($_SESSION['admin_id']);
    }

    /**
     * Redirect to main page with filters preserved
     */
    private function redirect()
    {
        $params = [];
        
        // Preserve filters
        if (!empty($_GET['search'])) $params['search'] = $_GET['search'];
        if (!empty($_GET['status'])) $params['status'] = $_GET['status'];
        if (!empty($_GET['role'])) $params['role'] = $_GET['role'];
        if (!empty($_GET['sort'])) $params['sort'] = $_GET['sort'];
        if (!empty($_GET['page'])) $params['page'] = $_GET['page'];
        if (!empty($_GET['limit'])) $params['limit'] = $_GET['limit'];
        
        $queryString = !empty($params) ? '&' . http_build_query($params) : '';
        header('Location: /2nd-Year-Group-Project/FixLanka/index.php?page=accountModeration' . $queryString);
        exit();
    }
}
?>