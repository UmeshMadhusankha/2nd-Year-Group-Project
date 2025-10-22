<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'welcome') ? 'active' : ''; ?>">
                <a href="welcome.php" class="nav-link" data-tooltip="Welcome">
                    <i class="fas fa-home"></i>
                    <span>Welcome</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'available-jobs') ? 'active' : ''; ?>">
                <a href="available-jobs.php" class="nav-link" data-tooltip="Available Jobs">
                    <i class="fas fa-briefcase"></i>
                    <span>Available Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'my-jobs') ? 'active' : ''; ?>">
                <a href="my-jobs.php" class="nav-link" data-tooltip="My Jobs">
                    <i class="fas fa-clipboard-list"></i>
                    <span>My Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'company-jobs') ? 'active' : ''; ?>">
                <a href="company-jobs.php" class="nav-link" data-tooltip="Company Jobs">
                    <i class="fas fa-building"></i>
                    <span>Company Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'earnings') ? 'active' : ''; ?>">
                <a href="earnings.php" class="nav-link" data-tooltip="Earnings">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'reviews') ? 'active' : ''; ?>">
                <a href="reviews.php" class="nav-link" data-tooltip="Reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">
                <a href="profile.php" class="nav-link" data-tooltip="My Profile">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'subscription') ? 'active' : ''; ?>">
                <a href="subscription.php" class="nav-link" data-tooltip="Subscription">
                    <i class="fas fa-crown"></i>
                    <span>Subscription</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'support') ? 'active' : ''; ?>">
                <a href="support.php" class="nav-link" data-tooltip="Support">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'settings') ? 'active' : ''; ?>">
                <a href="settings.php" class="nav-link" data-tooltip="Settings">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Footer in Sidebar -->
    <footer class="sidebar-footer">
        <p>© 2025 FixLanka<br>
           <a href="#terms">Terms</a> | 
           <a href="#privacy">Privacy</a> | 
           <a href="#help">Help</a>
        </p>
    </footer>
</aside>
