<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - FixLanka</title>
  <link rel="stylesheet" href="../../assets/css/common/variables.css" />
  <link rel="stylesheet" href="../../assets/css/common/global.css" />
  <link rel="stylesheet" href="../../assets/css/company/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-container">

  <!-- Login Card -->
  <div class="login-card">
    <div class="login-form">
      
      <!-- Logo Section -->
      <div class="logo-section">
        <img class="logo" src="fixlanka.png" alt="FixLanka Logo" />
        <h1 class="welcome-title">Welcome Back</h1>
        <p class="welcome-subtitle">Sign in to find trusted repairers</p>
      </div>

      <!-- Login Form -->
      <form class="form" id="loginForm">
        
        <!-- Email Field -->
        <div class="input-group">
          <label class="input-label" for="email">Email</label>
          <div class="input-container">
            <i class="fas fa-envelope input-icon"></i>
            <input 
              class="form-input" 
              placeholder="your@email.com" 
              type="email" 
              id="email" 
              required 
            />
          </div>
        </div>

        <!-- Password Field -->
        <div class="input-group">
          <div class="label-row">
            <label class="input-label" for="password">Password</label>
            <a href="#" class="forgot-link">Forgot Password?</a>
          </div>
          <div class="input-container">
            <i class="fas fa-lock input-icon"></i>
            <input 
              class="form-input" 
              placeholder="Enter password" 
              type="password" 
              id="password" 
              autocomplete="current-password"
              required 
            />
            <button type="button" class="password-toggle" id="passwordToggle">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>

        <!-- Remember Me -->
        <div class="checkbox-group">
          <input type="checkbox" id="remember" class="checkbox-input" />
          <label for="remember" class="checkbox-label">Remember me</label>
        </div>

        <!-- Login Button -->
        <button type="submit" class="login-btn">
          Sign In
        </button>

      </form>

      <!-- Register Link -->
      <div class="register-section">
        <p class="register-text">
          Don't have an account? 
          <a href="#" class="register-link">Sign up</a>
        </p>
      </div>

      <!-- Divider -->
      <div class="divider">
        <span class="divider-text">or continue with</span>
      </div>

      <!-- Social Login -->
      <div class="social-login">
        <button type="button" class="social-btn">
          <i class="fab fa-google"></i>
          Google
        </button>
        <button type="button" class="social-btn">
          <i class="fab fa-facebook-f"></i>
          Facebook
        </button>
      </div>

    </div>
  </div>

  <!-- JavaScript for interactions -->
  <script>
    // Password toggle functionality
    document.getElementById('passwordToggle').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
      } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
      }
    });

    // Simple form validation feedback
    document.querySelectorAll('.form-input').forEach(input => {
      input.addEventListener('blur', function() {
        const container = this.closest('.input-container');
        
        if (this.value && this.checkValidity()) {
          container.classList.add('valid');
        } else {
          container.classList.remove('valid');
        }
      });

      input.addEventListener('input', function() {
        const container = this.closest('.input-container');
        container.classList.remove('valid');
      });
    });
  </script>

</body>
</html>
