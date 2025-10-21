<?php
function renderMeta($title = 'Advanced PHP Router', $description = 'A Next.js-inspired PHP routing system', $basePath = '')
{
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
    <meta name="keywords" content="PHP, Router, Next.js, Advanced, Framework">
    <meta name="author" content="Advanced PHP Router">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars($title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($description) ?>">

    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/common/common.css">
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/common/sidebar.css">
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/common/topbar.css">
    <link rel="stylesheet" href="<?= $basePath ?>/public/css/common/modals.css">
    <link rel="icon" href="<?= $basePath ?>/public/favicon.ico" type="image/x-icon">
<?php
}
?>