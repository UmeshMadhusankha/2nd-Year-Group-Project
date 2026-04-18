<?php

function renderModeratorSidebar($currentPath, $basePath)
{
    $normalizeToPageKey = function ($pathOrUrl) {
        if (!is_string($pathOrUrl) || $pathOrUrl === '') {
            return '';
        }

        $path = parse_url($pathOrUrl, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = $pathOrUrl;
        }

        $path = trim($path, '/');
        if ($path === '') {
            return '';
        }

        $lastSegment = basename($path);
        $lastSegment = preg_replace('/\.php$/i', '', $lastSegment);
        return strtolower($lastSegment);
    };

    $stripRolePrefix = function ($pageKey) {
        if (!is_string($pageKey) || $pageKey === '') {
            return '';
        }
        $pageKey = strtolower($pageKey);
        if (str_starts_with($pageKey, 'admin-')) {
            return substr($pageKey, strlen('admin-'));
        }
        if (str_starts_with($pageKey, 'moderator-')) {
            return substr($pageKey, strlen('moderator-'));
        }
        return $pageKey;
    };

    $currentKey = $normalizeToPageKey($currentPath);
    if ($currentKey === '') {
        $currentKey = $normalizeToPageKey($_SERVER['REQUEST_URI'] ?? '');
    }

    // Icon mapping: Lucide → Font Awesome
    $iconMap = [
        'home' => 'fa-home',
        'dollar-sign' => 'fa-dollar-sign',
        'monitor' => 'fa-desktop',
        'file-text' => 'fa-file-lines',
        'calendar' => 'fa-calendar',
        'flag' => 'fa-flag',
        'bell' => 'fa-bell',
        'users' => 'fa-users'
    ];

    // Menu items for the new UI
    $menuItems = [
        ['title' => 'Dashboard', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-dashboard', 'icon' => 'home'],
        ['title' => 'Financial Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-finance', 'icon' => 'dollar-sign'],
        ['title' => 'Advertisement Review', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ads', 'icon' => 'monitor'],
        ['title' => 'Static Content', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-static-content', 'icon' => 'file-text'],
        ['title' => 'Ad Scheduling', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ad-schedule', 'icon' => 'calendar'],
        ['title' => 'Ad Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-ad-reports', 'icon' => 'flag'],
        ['title' => 'Notifications', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-notifications', 'icon' => 'bell'],
        ['title' => 'Support Tickets', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-support-tickets', 'icon' => 'file-text'],
      //  ['title' => 'User Management', 'url' => '/2nd-Year-Group-Project/FixLanka/moderator-account-moderation', 'icon' => 'users']
    ];

    echo '<!-- Sidebar Component -->';
    echo '<aside class="sidebar" id="sidebar">';
    echo '<nav class="sidebar-nav">';
    echo '<ul class="nav-list">';

    foreach ($menuItems as $item) {
        $itemKey = $normalizeToPageKey($item['url']);
        $isActive = ($currentKey !== '' && (
            $currentKey === $itemKey ||
            $stripRolePrefix($currentKey) === $itemKey ||
            $currentKey === $stripRolePrefix($itemKey) ||
            $stripRolePrefix($currentKey) === $stripRolePrefix($itemKey)
        ));
        $activeClass = $isActive ? 'active' : '';
        
        // Get Font Awesome icon class
        $faIcon = $iconMap[$item['icon']] ?? 'fa-circle';

        echo '<li class="nav-item ' . $activeClass . '">';
        echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link" data-tooltip="' . htmlspecialchars($item['title']) . '">';
        echo '<i class="fa-solid ' . $faIcon . '"></i>';
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