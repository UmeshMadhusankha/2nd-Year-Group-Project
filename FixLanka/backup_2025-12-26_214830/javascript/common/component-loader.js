function loadComponent(containerId, componentFile) {
    fetch(componentFile)
        .then(response => response.text())
        .then(data => {
            document.getElementById(containerId).innerHTML = data;
            if (componentFile.includes('sidebar') && typeof initializeSidebar === 'function') {
                initializeSidebar();
            }
            if (componentFile.includes('topbar') && typeof initializeTopbar === 'function') {
                initializeTopbar();
            }
        })
        .catch(error => console.error('Error loading component:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    // Load sidebar and topbar PHP components
    if (document.getElementById('sidebar-container')) {
        loadComponent('sidebar-container', 'sidebar.php');
    }
    if (document.getElementById('topbar-container')) {
        loadComponent('topbar-container', 'topbar.php');
    }
    // Legacy support for header-container
    if (document.getElementById('header-container')) {
        loadComponent('header-container', 'topbar.php');
    }
});
