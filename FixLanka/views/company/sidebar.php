<?php
// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item <?php echo ($current_page == 'dashboard.php' || $current_page == 'index.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php" class="nav-link" data-tooltip="Dashboard">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'repair-requests.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php" class="nav-link" data-tooltip="Repair Requests">
                    <i class="fas fa-tools"></i>
                    <span>Repair Requests</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'projects.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/projects.php" class="nav-link" data-tooltip="Projects">
                    <i class="fas fa-tasks"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'workforce.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/workforce.php" class="nav-link" data-tooltip="Workforce">
                    <i class="fas fa-users-cog"></i>
                    <span>Workforce</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'payments.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/payments.php" class="nav-link" data-tooltip="Payments">
                    <i class="fas fa-wallet"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'contracts.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/contracts.php" class="nav-link" data-tooltip="Contracts">
                    <i class="fas fa-handshake"></i>
                    <span>Contracts</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'advertisements.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/advertisements.php" class="nav-link" data-tooltip="Advertisements">
                    <i class="fas fa-bullhorn"></i>
                    <span>Advertisements</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/reviews.php" class="nav-link" data-tooltip="Reviews">
                    <i class="fas fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'support.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/support.php" class="nav-link" data-tooltip="Support">
                    <i class="fas fa-life-ring"></i>
                    <span>Support</span>
                </a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <a href="/2nd-Year-Group-Project/FixLanka/views/company/settings.php" class="nav-link" data-tooltip="Settings">
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
            <a href="help.php" target="_blank">Help</a>
        </p>
    </footer>
</aside>

<script>
    // Handle exclusive navigation selection - works for all nav items including Dashboard
    function initializeNavigation() {
        // Wait a bit to ensure DOM is fully loaded
        setTimeout(() => {
            const navItems = document.querySelectorAll('.sidebar .nav-item');
            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            console.log('Initializing navigation, found', navLinks.length, 'nav links'); // Debug log

            navLinks.forEach(function (link, index) {
                // Remove any existing event listeners to prevent duplicates
                link.removeEventListener('click', handleNavClick);
                // Add the click event listener
                link.addEventListener('click', handleNavClick);
                console.log('Added click listener to link', index); // Debug log
            });
        }, 200);
    }

    function handleNavClick(e) {
        console.log('Nav link clicked:', this.textContent.trim()); // Debug log

        // Check if it's a link to another page
        const href = this.getAttribute('href');
        if (href && href.startsWith('/2nd-Year-Group-Project/FixLanka/views/company/')) {
            // Extract page name and update topbar if function exists
            const pageName = href.replace('/2nd-Year-Group-Project/FixLanka/views/company/', '').replace('.php', '');
            if (typeof window.updatePageHeader === 'function') {
                console.log('Updating page header to:', pageName);
                window.updatePageHeader(pageName);
            }
            // Allow normal navigation to the page
            return;
        }

        // For other links (hash links), prevent default and handle as before
        e.preventDefault();

        // Remove active class from all nav items
        const allNavItems = document.querySelectorAll('.sidebar .nav-item');
        allNavItems.forEach(function (item) {
            item.classList.remove('active');
        });

        // Add active class to the clicked nav item
        this.parentElement.classList.add('active');

        console.log('Active class added to:', this.textContent.trim()); // Debug log
    }

    // Multiple initialization attempts to ensure it works
    document.addEventListener('DOMContentLoaded', initializeNavigation);

    // Also initialize when this script runs (for dynamic loading)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeNavigation);
    } else {
        initializeNavigation();
    }

    // Initialize after a delay as well (fallback)
    setTimeout(initializeNavigation, 500);
</script>
