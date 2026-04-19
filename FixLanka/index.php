<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\index.php
require_once __DIR__ . '/config/session.php';

$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/2nd-Year-Group-Project/FixLanka', '', $request);
$request = strtok($request, '?'); // Remove query string

switch ($request) {
    case '/':
    case '/home':
        if (isLoggedIn() && !hasRole('user')) {
            redirectToRoleHome();
        }
        require_once __DIR__ . '/views/user/landing.php';
        break;
    
    case '/login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLoginPage();
        }
        break;

    case '/forgot-password':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->resetPasswordWithoutVerification();
        } else {
            $controller->showForgotPasswordPage();
        }
        break;
    
    case '/signup':
        require_once __DIR__ . '/views/auth/signup.php';
        break;
    
    case '/register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;
    
    case '/logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case '/post-job':
        requireRole('user');
        require_once __DIR__ . '/views/user/post_job.php';
        break;
    
    case '/create-job':
        requireRole('user');
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->create();
        break;
    
    case '/job-history':
        requireRole('user');
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->index();
        break;
    
    case '/update-job':
        requireRole('user');
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->update();
        break;
    
    case '/delete-job':
        requireRole('user');
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->delete();
        break;
    
    case '/payment':
        requireRole('user');
        require_once __DIR__ . '/views/user/payment.php';
        break;

    case '/my-contracts':
        requireRole('user');
        require_once __DIR__ . '/views/user/contracts.php';
        break;
    
    case '/provider':
        requireRole('user');
        require_once __DIR__ . '/views/user/provider.php';
        break;
    
    case '/profile':
        requireRole('user');
        require_once __DIR__ . '/views/user/profile.php';
        break;
    
    case '/chat':
        requireRole('user');
        require_once __DIR__ . '/views/user/chat.php';
        break;

    case '/admin':
    case '/admin-dashboard':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    
    case '/admin-ads':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/ads.php';
        break;
    
    case '/admin-alerts':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/alerts.php';
        break;

    case '/admin-alerts-action':
        requireRole('admin');
        require_once __DIR__ . '/controllers/AdminAlertController.php';
        $controller = new AdminAlertController();
        $controller->handleRequest();
        break;
    
    case '/admin-analytics':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/analytics.php';
        break;
    
    case '/admin-finance':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/finance.php';
        break;
    
    case '/admin-issues':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/issues.php';
        break;

    case '/admin-support-tickets':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/support-tickets.php';
        break;
    
    case '/admin-users':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/users.php';
        break;
    
    case '/admin-moderators':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/moderators.php';
        break;

    case '/admin-moderators-action':
        requireRole('admin');
        require_once __DIR__ . '/controllers/ModeratorController.php';
        $controller = new ModeratorController();
        $controller->handleRequest();
        break;

    case '/admin-audit-logs':
        requireRole('admin');
        require_once __DIR__ . '/views/admin/audit-logs.php';
        break;
    
    case '/moderator-dashboard':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/dashboard.php';
        break;
    
    case '/moderator-finance':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/finance.php';
        break;
    
    case '/moderator-ads':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/ads.php';
        break;
    
    case '/moderator-static-content':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/static-content.php';
        break;
    
    case '/moderator-ad-schedule':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/ad-schedule.php';
        break;
    
    case '/moderator-ad-reports':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/ad-reports.php';
        break;
    
    case '/moderator-notifications':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/notifications.php';
        break;

    case '/moderator-support-tickets':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/support-tickets.php';
        break;
    
    case '/moderator-notifications-all':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/notifications-all.php';
        break;
    
    case '/moderator-account-moderation':
        requireRole('moderator');
        require_once __DIR__ . '/views/moderator/account-moderation.php';
        break;
    
    case '/settings':
        requireRole('user');
        require_once __DIR__ . '/views/user/settings.php';
        break;
    
    case '/help-center':
        if (isLoggedIn() && !hasRole('user')) {
            redirectToRoleHome();
        }
        require_once __DIR__ . '/views/user/help-center.php';
        break;
    
    case '/repairer-dashboard':
    case '/repairer-welcome':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/welcome.php';
        break;
    
    case '/repairer-available-jobs':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/available-jobs.php';
        break;
    
    case '/repairer-my-jobs':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/my-jobs.php';
        break;
    
    case '/repairer-company-jobs':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/company-jobs.php';
        break;
    
    case '/repairer-earnings':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/earnings.php';
        break;
    
    case '/repairer-reviews':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/reviews.php';
        break;
    
    case '/repairer-profile':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/profile.php';
        break;
    
    case '/repairer-subscription':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/subscription.php';
        break;
    
    case '/repairer-support':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/support.php';
        break;

    case '/repairer-notifications':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/notifications.php';
        break;
    
    case '/repairer-settings':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/settings.php';
        break;
    
    case '/repairer-upgrade':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/upgrade.php';
        break;
    
    case '/repairer-submit-quote':
        requireRole('repairer');
        require_once __DIR__ . '/views/repairer/pages/submit-quote.php';
        break;
    
    case '/company-dashboard':
        requireRole('company');
        require_once __DIR__ . '/views/company/dashboard.php';
        break;
    
    case '/company-repair-requests':
        requireRole('company');
        require_once __DIR__ . '/views/company/repair-requests.php';
        break;
    
    case '/company-contracts':
        requireRole('company');
        require_once __DIR__ . '/views/company/contracts.php';
        break;
    
    case '/company-projects':
        requireRole('company');
        require_once __DIR__ . '/views/company/projects.php';
        break;
    
    case '/company-workforce':
        requireRole('company');
        require_once __DIR__ . '/views/company/workforce.php';
        break;
    
    case '/company-payments':
        requireRole('company');
        require_once __DIR__ . '/views/company/payments.php';
        break;
    
    case '/company-reviews':
        requireRole('company');
        require_once __DIR__ . '/views/company/reviews.php';
        break;
    
    case '/company-support':
        requireRole('company');
        require_once __DIR__ . '/views/company/support.php';
        break;
    
    case '/company-profile':
        requireRole('company');
        require_once __DIR__ . '/views/company/profile.php';
        break;
    
    case '/company-settings':
        requireRole('company');
        require_once __DIR__ . '/views/company/settings.php';
        break;
    
    case '/company-advertisements':
        requireRole('company');
        require_once __DIR__ . '/views/company/advertisements.php';
        break;
    
    case '/company-feedback':
        requireRole('company');
        require_once __DIR__ . '/views/company/feedback.php';
        break;

    // API Routes for Quotations
    case '/company-submit-quotation':
        requireRole('company');
        require_once __DIR__ . '/controllers/CompanyQuotationController.php';
        $controller = new CompanyQuotationController();
        $controller->create();
        break;

    case '/company-get-quotations':
        requireRole('company');
        require_once __DIR__ . '/controllers/CompanyQuotationController.php';
        $controller = new CompanyQuotationController();
        $controller->getMyQuotations();
        break;

    case '/api/job-requests/open':
        requireRole(['company', 'repairer', 'admin']);
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->getOpenRequests();
        break;

    case '/api/contracts/undo':
        requireRole(['admin', 'company']);
        require_once __DIR__ . '/controllers/ContractController.php';
        $controller = new ContractController();
        $controller->undoContract();
        break;
    
    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}
?>