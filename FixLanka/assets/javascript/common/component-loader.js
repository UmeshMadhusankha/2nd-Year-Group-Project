function loadComponent(containerId, componentFile) {
    fetch(componentFile)
        .then(response => response.text())
        .then(data => {
            document.getElementById(containerId).innerHTML = data;
            if (componentFile === 'sidebar.html' && typeof initializeSidebar === 'function') {
                initializeSidebar();
            }
            if (componentFile === 'topbar.html' && typeof initializeTopbar === 'function') {
                initializeTopbar();
            }
        })
        .catch(error => console.error('Error loading component:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    loadComponent('sidebar-container', 'sidebar.html');
    loadComponent('header-container', 'topbar.html');
});
