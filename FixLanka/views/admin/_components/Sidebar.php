<?php

function renderAdminSidebar($currentPath, $basePath)
{
    // Normalize current path (remove trailing slashes)
    $currentPath = trim($currentPath, '/');

    $menuItems = [
        ['title' => 'Dashboard', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-dashboard', 'icon' => 'home'],
        ['title' => 'Moderator Management', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-moderators', 'icon' => 'users'],
        ['title' => 'Send Alerts', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-alerts', 'icon' => 'alert-triangle'],
        ['title' => 'Issues & Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-issues', 'icon' => 'message-square'],
        ['title' => 'Analytics', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-analytics', 'icon' => 'bar-chart-3'],
        ['title' => 'Advertisement Review', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-ads', 'icon' => 'monitor'],
        ['title' => 'Financial Overview', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-finance', 'icon' => 'dollar-sign']
    ];

    echo '<!-- Sidebar Component -->';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<nav class="sidebar-nav">';
    echo '<ul class="nav-list">';

    foreach ($menuItems as $item) {
        $isActive = ($currentPath === trim($item['url'], '/'));
        $activeClass = $isActive ? 'active' : '';

        echo '<li class="nav-item ' . $activeClass . '">';
        echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link" data-tooltip="' . htmlspecialchars($item['title']) . '">';
        echo '<i data-lucide="' . htmlspecialchars($item['icon']) . '" class=""></i>';
        echo '<span>' . htmlspecialchars($item['title']) . '</span>';
        echo '</a>';
        echo '</li>';
    }

    echo '</ul>';
    echo '</nav>';

    // Footer section
    echo '<footer class="sidebar-footer">';
    echo '<p>© 2025 FixLanka<br>';
    echo '<a href="#terms">Terms</a> | ';
    echo '<a href="#privacy">Privacy</a> | ';
    echo '<a href="#help">Help</a>';
    echo '</p>';
    echo '</footer>';

    echo '</aside>';
}
