<?php
/**
 * Repairer topbar wrapper -> shared topbar
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../shared/topbar.php';

$repairerId = (int)($_SESSION['user_id'] ?? 0);
$userName = (string)($_SESSION['user_name'] ?? 'Repairer');
$userEmail = (string)($_SESSION['user_email'] ?? '');

$avatarUrl = '/2nd-Year-Group-Project/FixLanka/assets/images/user.png';
if ($repairerId > 0) {
    try {
        $avatarStmt = $pdo->prepare('SELECT profilePicture FROM repairer WHERE repairer_id = ? LIMIT 1');
        $avatarStmt->execute([$repairerId]);
        $avatarRow = $avatarStmt->fetch(PDO::FETCH_ASSOC);
        if ($avatarRow && !empty($avatarRow['profilePicture'])) {
            $avatarUrl = '/2nd-Year-Group-Project/FixLanka/' . ltrim((string)$avatarRow['profilePicture'], '/');
        }
    } catch (Throwable $e) {
        // keep default avatar
    }
}

$resolvedTitle = isset($pageTitle) && $pageTitle !== '' ? (string)$pageTitle : 'Dashboard';
$resolvedSubtitle = isset($pageSubtitle) && $pageSubtitle !== ''
    ? (string)$pageSubtitle
    : (isset($searchPlaceholder) && $searchPlaceholder !== '' ? (string)$searchPlaceholder : 'Welcome to FixLanka');
$resolvedSearchPlaceholder = isset($searchPlaceholder) && $searchPlaceholder !== ''
    ? (string)$searchPlaceholder
    : 'Search jobs, clients, locations...';

renderSharedTopbar([
    'role' => 'repairer',
    'userId' => $repairerId,
    'userName' => $userName,
    'userEmail' => $userEmail,
    'avatarUrl' => $avatarUrl,
    'pageTitle' => $resolvedTitle,
    'pageSlogan' => $resolvedSubtitle,
    'searchPlaceholder' => $resolvedSearchPlaceholder,
    'searchCategories' => [
        ['value' => 'all', 'label' => 'All'],
        ['value' => 'jobs', 'label' => 'Jobs'],
        ['value' => 'companies', 'label' => 'Companies'],
        ['value' => 'earnings', 'label' => 'Earnings'],
        ['value' => 'reviews', 'label' => 'Reviews']
    ],
    'notificationsPageUrl' => '/2nd-Year-Group-Project/FixLanka/repairer-notifications',
    'profileLinks' => [
        ['href' => '/2nd-Year-Group-Project/FixLanka/repairer-profile', 'label' => 'My Profile', 'icon' => 'fas fa-user'],
        ['href' => '/2nd-Year-Group-Project/FixLanka/repairer-settings', 'label' => 'Settings', 'icon' => 'fas fa-cog'],
        ['href' => '/2nd-Year-Group-Project/FixLanka/repairer-upgrade', 'label' => 'Upgrade', 'icon' => 'fas fa-crown']
    ],
    'quickSearchLinks' => [
        ['title' => 'Welcome', 'subtitle' => 'Repairer dashboard', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-welcome', 'icon' => 'fa-home'],
        ['title' => 'Available Jobs', 'subtitle' => 'Open opportunities', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-available-jobs', 'icon' => 'fa-briefcase'],
        ['title' => 'My Jobs', 'subtitle' => 'Current and completed tasks', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-my-jobs', 'icon' => 'fa-hammer'],
        ['title' => 'Company Jobs', 'subtitle' => 'Contracted company work', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-company-jobs', 'icon' => 'fa-building'],
        ['title' => 'Earnings', 'subtitle' => 'Income history', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-earnings', 'icon' => 'fa-wallet'],
        ['title' => 'Reviews', 'subtitle' => 'Performance feedback', 'url' => '/2nd-Year-Group-Project/FixLanka/repairer-reviews', 'icon' => 'fa-star']
    ]
]);
