<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Fix Lanka (Test)</title>
    <link rel="stylesheet" href="../../assets/css/auth/signup.css">
    <style>
        /* Inline test to verify CSS is the issue */
        body { background: red !important; }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>Create Account</h1>
                <p>Join Fix Lanka today</p>
            </div>
            
            <form action="#" method="POST" class="signup-form" id="signupForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="f_name">First Name *</label>
                        <input type="text" id="f_name" name="f_name" required placeholder="Enter first name">
                    </div>
                    
                    <div class="form-group">
                        <label for="l_name">Last Name *</label>
                        <input type="text" id="l_name" name="l_name" required placeholder="Enter last name">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address (optional)">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password" minlength="6">
                    <small class="password-hint">At least 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
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
                Already have an account? <a href="#">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>
