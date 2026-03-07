<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'welcome') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-welcome" class="nav-link" data-tooltip="Welcome">
                    <i class="fas fa-home"></i>
                    <span>Welcome</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'available-jobs') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-available-jobs" class="nav-link" data-tooltip="Available Jobs">
                    <i class="fas fa-briefcase"></i>
                    <span>Available Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'my-jobs') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-my-jobs" class="nav-link" data-tooltip="My Jobs">
                    <i class="fas fa-clipboard-list"></i>
                    <span>My Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'company-jobs') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-company-jobs" class="nav-link" data-tooltip="Company Jobs">
                    <i class="fas fa-building"></i>
                    <span>Company Jobs</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'earnings') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-earnings" class="nav-link" data-tooltip="Earnings">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'reviews') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-reviews" class="nav-link" data-tooltip="Reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'profile') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-profile" class="nav-link" data-tooltip="My Profile">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'subscription') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-subscription" class="nav-link" data-tooltip="Subscription">
                    <i class="fas fa-crown"></i>
                    <span>Subscription</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'support') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-support" class="nav-link" data-tooltip="Support">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
            </li>
            <li class="nav-item <?php echo (isset($currentPage) && $currentPage == 'settings') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/repairer-settings" class="nav-link" data-tooltip="Settings">
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
