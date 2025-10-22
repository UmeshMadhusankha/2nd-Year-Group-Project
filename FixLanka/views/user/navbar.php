<?php
// Include session helper if not already included
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../../config/session.php';
}
$isLoggedIn = isLoggedIn();
$userData = getUserData();
?>

<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-left">
            <div class="logo">
                <a href="/2nd-Year-Group-Project/FixLanka/" class="logo-link">
                    <span class="logo-text">Fix Lanka</span>
                </a>
            </div>
        </div>
        
        <div class="navbar-center">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/services.php" class="nav-link">Services</a>
                </li>
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/how-it-works.php" class="nav-link">How it works</a>
                </li>
                <li class="nav-item">
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/support.php" class="nav-link">Support</a>
                </li>
            </ul>
        </div>
        
        <div class="navbar-right">
            <?php if ($isLoggedIn): ?>
                <!-- Logged In User Section -->
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <div class="profile-dropdown-container">
                    <div class="profile-avatar" id="profileAvatar">
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($userData['name'], 0, 1)); ?>
                        </div>
                    </div>
                    
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="dropdown-header">
                            <p class="user-name"><?php echo htmlspecialchars($userData['name']); ?></p>
                            <p class="user-email"><?php echo htmlspecialchars($userData['email']); ?></p>
                        </div>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/profile" class="dropdown-link">
                                    <i class="fas fa-user"></i>
                                    My Profile
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/bookings" class="dropdown-link">
                                    <i class="fas fa-calendar-check"></i>
                                    My Bookings
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/messages" class="dropdown-link">
                                    <i class="fas fa-envelope"></i>
                                    Messages
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/settings" class="dropdown-link">
                                    <i class="fas fa-cog"></i>
                                    Settings
                                </a>
                            </li>
                            <li class="dropdown-item">
                                <a href="/2nd-Year-Group-Project/FixLanka/help" class="dropdown-link">
                                    <i class="fas fa-question-circle"></i>
                                    Help Center
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li class="dropdown-item">
                                <form action="/2nd-Year-Group-Project/FixLanka/logout" method="POST" id="logoutForm" style="margin: 0;">
                                    <button type="submit" class="dropdown-link logout" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: inherit; font-family: inherit; padding: 12px 20px;">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Guest User Section - Login Button -->
                <a href="/2nd-Year-Group-Project/FixLanka/login" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
            <?php endif; ?>
        </div>
        
        <!-- Mobile menu toggle -->
        <div class="mobile-menu-toggle" id="mobileMenuToggle">
            <span class="hamburger"></span>
            <span class="hamburger"></span>
            <span class="hamburger"></span>
        </div>
    </div>
    
    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobileMenu">
        <ul class="mobile-nav-menu">
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/services.php" class="mobile-nav-link">Services</a></li>
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/how-it-works.php" class="mobile-nav-link">How it works</a></li>
            <li><a href="/2nd-Year-Group-Project/FixLanka/views/user/support.php" class="mobile-nav-link">Support</a></li>
            
            <?php if ($isLoggedIn): ?>
                <li><a href="/2nd-Year-Group-Project/FixLanka/profile" class="mobile-nav-link">My Profile</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/bookings" class="mobile-nav-link">My Bookings</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/messages" class="mobile-nav-link">Messages</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/settings" class="mobile-nav-link">Settings</a></li>
                <li><a href="/2nd-Year-Group-Project/FixLanka/help" class="mobile-nav-link">Help Center</a></li>
                <li>
                    <form action="/2nd-Year-Group-Project/FixLanka/logout" method="POST" style="margin: 0;">
                        <button type="submit" class="mobile-nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; font-size: inherit; font-family: inherit;">Logout</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/2nd-Year-Group-Project/FixLanka/login" class="mobile-nav-link mobile-login-btn">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<script>
// Profile dropdown toggle
document.addEventListener('DOMContentLoaded', function() {
    const profileAvatar = document.getElementById('profileAvatar');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profileAvatar && profileDropdown) {
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
    }
    
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('show');
        });
    }
});
</script>