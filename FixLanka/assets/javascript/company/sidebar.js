// Sidebar Component JavaScript

// Sidebar functionality
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (!sidebar || !sidebarToggle) {
        console.error('Sidebar elements not found');
        return;
    }
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
        // Store collapsed state in localStorage
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Restore collapsed state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
    }
    
    // Auto-collapse on mobile
    if (window.innerWidth <= 768) {
        sidebar.classList.add('collapsed');
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
        } else if (window.innerWidth > 768 && !isCollapsed) {
            sidebar.classList.remove('collapsed');
        }
    });
    
    // Handle navigation clicks
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item's parent
            this.closest('.nav-item').classList.add('active');
            
            // Update page title based on clicked link
            const linkText = this.querySelector('span').textContent;
            updatePageTitle(linkText);
            
            // Store active page in localStorage
            localStorage.setItem('activePage', linkText);
        });
    });
    
    // Restore active page from localStorage
    const activePage = localStorage.getItem('activePage');
    if (activePage) {
        restoreActivePage(activePage);
    }
}

// Update page title in header
function updatePageTitle(title) {
    const pageTitle = document.querySelector('.page-title');
    if (pageTitle) {
        pageTitle.textContent = title;
        
        // Update subtitle based on page
        const pageSubtitle = document.querySelector('.page-subtitle');
        if (pageSubtitle) {
            pageSubtitle.textContent = getPageSubtitle(title);
        }
    }
}

// Get appropriate subtitle for each page
function getPageSubtitle(page) {
    const subtitles = {
        'Dashboard': 'Welcome back, let\'s see what\'s happening today',
        'Projects': 'Manage and track your ongoing projects',
        'Requests': 'Handle customer repair requests efficiently',
        'Workforce': 'Manage your team and assignments',
        'Payments': 'Track payments and financial transactions',
        'Contracts': 'Manage contracts and agreements',
        'Support': 'Handle customer support and issues',
        'Settings': 'Configure your application preferences'
    };
    
    return subtitles[page] || 'Manage your business operations';
}

// Restore active page state
function restoreActivePage(pageName) {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const linkText = link.querySelector('span').textContent;
        if (linkText === pageName) {
            // Remove active from all
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active to current
            link.closest('.nav-item').classList.add('active');
            updatePageTitle(pageName);
        }
    });
}

// Mobile sidebar toggle for touch devices
function setupMobileSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar = sidebar.contains(e.target);
            const isToggleButton = e.target.closest('#sidebarToggle');
            
            if (!isClickInsideSidebar && !isToggleButton && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });
    
    // Add touch gesture support for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipeGesture();
    });
    
    function handleSwipeGesture() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;
        
        if (window.innerWidth <= 768) {
            // Swipe right to open sidebar
            if (swipeDistance > swipeThreshold && touchStartX < 50) {
                sidebar.classList.add('open');
            }
            // Swipe left to close sidebar
            else if (swipeDistance < -swipeThreshold && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    }
}

// Initialize sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    setupMobileSidebarToggle();
});

// Export functions for global access
if (typeof window !== 'undefined') {
    window.initializeSidebar = initializeSidebar;
    window.updatePageTitle = updatePageTitle;
}
