<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\index.php
require_once __DIR__ . '/config/session.php';

$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/2nd-Year-Group-Project/FixLanka', '', $request);
$request = strtok($request, '?'); // Remove query string

switch ($request) {
    case '/':
    case '/home':
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
        require_once __DIR__ . '/views/user/post_job.php';
        break;
    
    case '/create-job':
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->create();
        break;
    
    case '/job-history':
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->index();
        break;
    
    case '/update-job':
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->update();
        break;
    
    case '/delete-job':
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->delete();
        break;
    
    case '/payment':
        require_once __DIR__ . '/views/user/payment.php';
        break;

    case '/my-contracts':
        require_once __DIR__ . '/views/user/contracts.php';
        break;
    
    case '/provider':
        require_once __DIR__ . '/views/user/provider.php';
        break;
    
    case '/profile':
        require_once __DIR__ . '/views/user/profile.php';
        break;
    
    case '/chat':
        require_once __DIR__ . '/views/user/chat.php';
        break;

    case '/admin':
    case '/admin-dashboard':
        require_once __DIR__ . '/views/admin/dashboard.php';
        break;
    
    case '/admin-ads':
        require_once __DIR__ . '/views/admin/ads.php';
        break;
    
    case '/admin-alerts':
        require_once __DIR__ . '/views/admin/alerts.php';
        break;
    
    case '/admin-analytics':
        require_once __DIR__ . '/views/admin/analytics.php';
        break;
    
    case '/admin-finance':
        require_once __DIR__ . '/views/admin/finance.php';
        break;
    
    case '/admin-issues':
        require_once __DIR__ . '/views/admin/issues.php';
        break;
    
    case '/admin-users':
        require_once __DIR__ . '/views/admin/users.php';
        break;
    
    case '/admin-moderators':
        require_once __DIR__ . '/views/admin/moderators.php';
        break;
    
    case '/moderator-dashboard':
        require_once __DIR__ . '/views/moderator/dashboard.php';
        break;
    
    case '/moderator-finance':
        require_once __DIR__ . '/views/moderator/finance.php';
        break;
    
    case '/moderator-ads':
        require_once __DIR__ . '/views/moderator/ads.php';
        break;
    
    case '/moderator-static-content':
        require_once __DIR__ . '/views/moderator/static-content.php';
        break;
    
    case '/moderator-ad-schedule':
        require_once __DIR__ . '/views/moderator/ad-schedule.php';
        break;
    
    case '/moderator-ad-reports':
        require_once __DIR__ . '/views/moderator/ad-reports.php';
        break;
    
    case '/moderator-notifications':
        require_once __DIR__ . '/views/moderator/notifications.php';
        break;
    
    case '/moderator-notifications-all':
        require_once __DIR__ . '/views/moderator/notifications-all.php';
        break;
    
    case '/moderator-account-moderation':
        require_once __DIR__ . '/views/moderator/account-moderation.php';
        break;
    
    case '/settings':
        require_once __DIR__ . '/views/user/settings.php';
        break;
    
    case '/help-center':
        require_once __DIR__ . '/views/user/help-center.php';
        break;
    
    case '/repairer-dashboard':
    case '/repairer-welcome':
        require_once __DIR__ . '/views/repairer/pages/welcome.php';
        break;
    
    case '/repairer-available-jobs':
        require_once __DIR__ . '/views/repairer/pages/available-jobs.php';
        break;
    
    case '/repairer-my-jobs':
        require_once __DIR__ . '/views/repairer/pages/my-jobs.php';
        break;
    
    case '/repairer-company-jobs':
        require_once __DIR__ . '/views/repairer/pages/company-jobs.php';
        break;
    
    case '/repairer-earnings':
        require_once __DIR__ . '/views/repairer/pages/earnings.php';
        break;
    
    case '/repairer-reviews':
        require_once __DIR__ . '/views/repairer/pages/reviews.php';
        break;
    
    case '/repairer-profile':
        require_once __DIR__ . '/views/repairer/pages/profile.php';
        break;
    
    case '/repairer-subscription':
        require_once __DIR__ . '/views/repairer/pages/subscription.php';
        break;
    
    case '/repairer-support':
        require_once __DIR__ . '/views/repairer/pages/support.php';
        break;
    
    case '/repairer-settings':
        require_once __DIR__ . '/views/repairer/pages/settings.php';
        break;
    
    case '/repairer-upgrade':
        require_once __DIR__ . '/views/repairer/pages/upgrade.php';
        break;
    
    case '/repairer-submit-quote':
        require_once __DIR__ . '/views/repairer/pages/submit-quote.php';
        break;
    
    case '/company-dashboard':
        require_once __DIR__ . '/views/company/dashboard.php';
        break;
    
    case '/company-repair-requests':
        require_once __DIR__ . '/views/company/repair-requests.php';
        break;
    
    case '/company-contracts':
        require_once __DIR__ . '/views/company/contracts.php';
        break;
    
    case '/company-projects':
        require_once __DIR__ . '/views/company/projects.php';
        break;
    
    case '/company-workforce':
        require_once __DIR__ . '/views/company/workforce.php';
        break;
    
    case '/company-payments':
        require_once __DIR__ . '/views/company/payments.php';
        break;
    
    case '/company-reviews':
        require_once __DIR__ . '/views/company/reviews.php';
        break;
    
    case '/company-support':
        require_once __DIR__ . '/views/company/support.php';
        break;
    
    case '/company-profile':
        require_once __DIR__ . '/views/company/profile.php';
        break;
    
    case '/company-settings':
        require_once __DIR__ . '/views/company/settings.php';
        break;
    
    case '/company-advertisements':
        require_once __DIR__ . '/views/company/advertisements.php';
        break;
    
    case '/company-feedback':
        require_once __DIR__ . '/views/company/feedback.php';
        break;

    // API Routes for Quotations
    case '/company-submit-quotation':
        require_once __DIR__ . '/controllers/CompanyQuotationController.php';
        $controller = new CompanyQuotationController();
        $controller->create();
        break;

    case '/company-get-quotations':
        require_once __DIR__ . '/controllers/CompanyQuotationController.php';
        $controller = new CompanyQuotationController();
        $controller->getMyQuotations();
        break;

    case '/api/job-requests/open':
        require_once __DIR__ . '/controllers/JobRequestController.php';
        $controller = new JobRequestController();
        $controller->getOpenRequests();
        break;

    case '/api/contracts/undo':
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