<?php

function renderModeratorSidebar($currentPath, $basePath)
{
    // Normalize current path (remove trailing slashes)
    $currentPath = trim($currentPath, '/');

    // Menu items for the new UI
    $menuItems = [
        ['title' => 'Dashboard', 'url' => 'dashboard', 'icon' => 'home'],
        ['title' => 'Financial Reports', 'url' => 'finance', 'icon' => 'dollar-sign'],
        ['title' => 'Advertisement Review', 'url' => 'ads', 'icon' => 'monitor'],
        ['title' => 'Static Content', 'url' => 'static-content', 'icon' => 'file-text'],
        ['title' => 'Ad Scheduling', 'url' => 'ad-schedule', 'icon' => 'calendar'],
        ['title' => 'Ad Reports', 'url' => 'ad-reports', 'icon' => 'flag'],
        ['title' => 'Notifications', 'url' => 'notifications', 'icon' => 'bell'],
        ['title' => 'User Management', 'url' => 'account-moderation', 'icon' => 'users']
    ];

    echo '<!-- Sidebar Component -->';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<nav class="sidebar-nav">';
    echo '<ul class="nav-list">';

    foreach ($menuItems as $item) {
        $isActive = ($currentPath === trim($item['url'], '/'));
        $activeClass = $isActive ? 'active' : '';

        echo '<li class="nav-item ' . $activeClass . '">';
        echo '<a href="' . $item['url'] . '.php" class="nav-link" data-tooltip="' . htmlspecialchars($item['title']) . '">';
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
