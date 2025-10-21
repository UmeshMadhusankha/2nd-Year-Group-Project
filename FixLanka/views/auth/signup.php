<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\auth\signup.php
require_once __DIR__ . '/../../config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/dashboard');
    exit;
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
    <title>Sign Up - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/auth/signup.css">
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>Create Account</h1>
                <p>Join Fix Lanka today</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form action="/2nd-Year-Group-Project/FixLanka/register" method="POST" class="signup-form" id="signupForm" autocomplete="off">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f_name">First Name *</label>
                        <input type="text" id="f_name" name="f_name" required placeholder="Enter first name" autocomplete="off" value="<?php echo htmlspecialchars($_POST['f_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="l_name">Last Name *</label>
                        <input type="text" id="l_name" name="l_name" required placeholder="Enter last name" autocomplete="off" value="<?php echo htmlspecialchars($_POST['l_name'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="off" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address (optional)" autocomplete="off" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password" minlength="6" autocomplete="new-password">
                    <small class="password-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password" autocomplete="new-password">
                </div>
                
                <div class="terms-checkbox">
                    <label class="checkbox-label">
                        <input type="checkbox" name="terms" required>
                        I agree to the <a href="/terms" target="_blank">Terms & Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="signup-submit-btn">Create Account</button>
            </form>
            
            <div class="login-link">
                Already have an account? <a href="/2nd-Year-Group-Project/FixLanka/login">Login here</a>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/auth/signup.js"></script>
</body>
</html>