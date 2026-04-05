<?php
require_once __DIR__ . '/../../config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/dashboard');
    exit;
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
$prefillEmail = $_SESSION['prefill_email'] ?? '';

unset($_SESSION['error'], $_SESSION['success'], $_SESSION['prefill_email']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/auth/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Reset Password</h1>
                <p>Set a new password for your Fix Lanka account</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="/2nd-Year-Group-Project/FixLanka/forgot-password" method="POST" class="login-form" autocomplete="off">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($prefillEmail); ?>" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required placeholder="Enter your new password" minlength="6" autocomplete="new-password">
                </div>

                <button type="submit" class="action-btn primary large login-submit-btn">Update Password</button>
            </form>

            <div class="signup-link">
                Back to <a href="/2nd-Year-Group-Project/FixLanka/login">Login</a>
            </div>
        </div>
    </div>
</body>
</html>
