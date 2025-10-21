function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (!sidebar || !sidebarToggle) return;
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) sidebar.classList.add('collapsed');
    
    if (window.innerWidth <= 768) sidebar.classList.add('collapsed');
    
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
        } else if (window.innerWidth > 768 && !isCollapsed) {
            sidebar.classList.remove('collapsed');
        }
    });
    
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const isPageLink = href.endsWith('.html') || href === 'repair-requests.html' || href === 'dashboard.html' || href === 'projects.html' || href === 'workforce.html' || href === 'payments.html' || href === 'contracts.html' || href === 'support.html' || href === 'settings.html';
            
            if (isPageLink) {
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                this.closest('.nav-item').classList.add('active');
                
                const linkText = this.querySelector('span').textContent;
                localStorage.setItem('activePage', linkText);
                
                return;
            }
            
            e.preventDefault();
            
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            this.closest('.nav-item').classList.add('active');
            
            const linkText = this.querySelector('span').textContent;
            updatePageTitle(linkText);
            
            localStorage.setItem('activePage', linkText);
        });
    });
    
    const activePage = localStorage.getItem('activePage');
    if (activePage) restoreActivePage(activePage);
}

function updatePageTitle(title) {
    const pageTitle = document.querySelector('.page-title');
    if (pageTitle) {
        pageTitle.textContent = title;
        
        const pageSubtitle = document.querySelector('.page-subtitle');
        if (pageSubtitle) {
            pageSubtitle.textContent = getPageSubtitle(title);
        }
    }
}

function getPageSubtitle(page) {
    const subtitles = {
        'Dashboard': 'Welcome back, let\'s see what\'s happening today',
        'Projects': 'Manage and track your ongoing projects',
        'Repair Requests': 'Manage customer repair requests - public opportunities and direct requests',
        'Requests': 'Handle customer repair requests efficiently',
        'Workforce': 'Manage your team and assignments',
        'Payments': 'Track payments and financial transactions',
        'Contracts': 'Manage contracts and agreements',
        'Support': 'Handle customer support and issues',
        'Settings': 'Configure your application preferences'
    };
    
    return subtitles[page] || 'Manage your business operations';
}

function restoreActivePage(pageName) {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const linkText = link.querySelector('span').textContent;
        if (linkText === pageName) {
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            
            link.closest('.nav-item').classList.add('active');
            updatePageTitle(pageName);
        }
    });
}

function setupMobileSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            const isClickInsideSidebar = sidebar.contains(e.target);
            const isToggleButton = e.target.closest('#sidebarToggle');
            
            if (!isClickInsideSidebar && !isToggleButton && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });
    
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
            if (swipeDistance > swipeThreshold && touchStartX < 50) {
                sidebar.classList.add('open');
            }
            else if (swipeDistance < -swipeThreshold && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initializeSidebar();
    setupMobileSidebarToggle();
});

if (typeof window !== 'undefined') {
    window.initializeSidebar = initializeSidebar;
    window.updatePageTitle = updatePageTitle;
}
