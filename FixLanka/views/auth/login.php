<?php
require_once __DIR__ . '/../../config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToRoleHome();
}

// Get error message if exists
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/auth/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Login to your Fix Lanka account</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="/2nd-Year-Group-Project/FixLanka/login" method="POST" class="login-form" id="loginForm" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="off">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password" autocomplete="new-password">
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>
                    <a href="/2nd-Year-Group-Project/FixLanka/forgot-password" class="forgot-link">Forgot Password?</a>
                </div>
                
                <button type="submit" class="action-btn primary large login-submit-btn">Login</button>
            </form>
            
            <div class="signup-link">
                Don't have an account? <a href="/2nd-Year-Group-Project/FixLanka/signup">Sign up here</a>
            </div>
        </div>
    </div>
</body>
</html>
