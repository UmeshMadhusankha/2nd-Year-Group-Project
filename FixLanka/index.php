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
    
    case '/provider':
        require_once __DIR__ . '/views/user/provider.php';
        break;
    
    case '/profile':
        require_once __DIR__ . '/views/user/profile.php';
        break;
    
    case '/chat':
        require_once __DIR__ . '/views/user/chat.php';
        break;
    
    case '/settings':
        require_once __DIR__ . '/views/user/settings.php';
        break;
    
    case '/help-center':
        require_once __DIR__ . '/views/user/help-center.php';
        break;
    
    case '/repairer-dashboard':
        require_once __DIR__ . '/views/repairer/dashboard.php';
        break;
    
    case '/company-dashboard':
        require_once __DIR__ . '/views/company/dashboard.php';
        break;
    
    default:
        http_response_code(404);
        echo "404 - Page Not Found";
        break;
}
?>