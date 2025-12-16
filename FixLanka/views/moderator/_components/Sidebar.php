<?php

function renderModeratorSidebar($currentPath, $basePath)
{
    // Normalize current path (remove trailing slashes)
    $currentPath = trim($currentPath, '/');

    // Menu items for the new UI
    $menuItems = [
        ['title' => 'Dashboard', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-dashboard', 'icon' => 'home'],
        ['title' => 'Financial Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-finance', 'icon' => 'dollar-sign'],
        ['title' => 'Advertisement Review', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ads', 'icon' => 'monitor'],
        ['title' => 'Static Content', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-static-content', 'icon' => 'file-text'],
        ['title' => 'Ad Scheduling', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ad-schedule', 'icon' => 'calendar'],
        ['title' => 'Ad Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ad-reports', 'icon' => 'flag'],
        ['title' => 'Notifications', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-notifications', 'icon' => 'bell'],
      //  ['title' => 'User Management', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-account-moderation', 'icon' => 'users']
    ];

    echo '<!-- Sidebar Component -->';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<nav class="sidebar-nav">';
    echo '<ul class="nav-list">';

    foreach ($menuItems as $item) {
        $isActive = (trim($currentPath, '/') === trim($item['url'], '/'));
        echo '<script>console.log("Current Path: ' . addslashes($currentPath) . ' | Item URL: ' . addslashes($item['url']) . ' | Is Active: ' . ($isActive ? 'true' : 'false') . '");</script>';
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
