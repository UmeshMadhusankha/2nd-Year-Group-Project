<?php

function renderAdminSidebar($currentPath, $basePath)
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

    $menuItems = [
        ['title' => 'Dashboard', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-dashboard', 'icon' => 'fa-home'],
        ['title' => 'Moderator Management', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-moderators', 'icon' => 'fa-users'],
        ['title' => 'Account Moderation', 'url' => '/2nd-Year-Group-Project/FixLanka/views/admin/account-moderation.php', 'icon' => 'fa-shield'],
        ['title' => 'Send Alerts', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-alerts', 'icon' => 'fa-triangle-exclamation'],
        ['title' => 'Issues & Reports', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-issues', 'icon' => 'fa-message'],
        ['title' => 'Analytics', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-analytics', 'icon' => 'fa-chart-bar'],
        ['title' => 'Advertisement Review', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-ads', 'icon' => 'fa-desktop'],
        ['title' => 'Financial Overview', 'url' => '/2nd-Year-Group-Project/FixLanka/admin-finance', 'icon' => 'fa-dollar-sign']
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

        echo '<li class="nav-item ' . $activeClass . '">';
        echo '<a href="' . htmlspecialchars($item['url']) . '" class="nav-link" data-tooltip="' . htmlspecialchars($item['title']) . '">';
        echo '<i class="fa-solid ' . htmlspecialchars($item['icon']) . '"></i>';
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