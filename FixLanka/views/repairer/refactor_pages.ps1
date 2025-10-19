# PowerShell script to refactor remaining pages
$pages = @(
    @{file='reviews.php'; page='reviews'; title='Customer Reviews'; subtitle='Manage your customer feedback and ratings'; search='Search reviews, customers, ratings...'},
    @{file='profile.php'; page='profile'; title='My Profile'; subtitle='Manage your personal information and settings'; search='Search...'},
    @{file='support.php'; page='support'; title='Support'; subtitle='Get help and contact our support team'; search='Search support articles...'},
    @{file='upgrade.php'; page='upgrade'; title='Upgrade to Pro'; subtitle='Unlock premium features and benefits'; search='Search features...'},
    @{file='welcome.php'; page='welcome'; title='Welcome to FixLanka'; subtitle='Your trusted repair network dashboard'; search='Search...'},
    @{file='submit-quote.php'; page='submit-quote'; title='Submit Quote'; subtitle='Submit your repair quote'; search='Search...'}
)

foreach ($p in $pages) {
    $file = "pages\$($p.file)"
    Write-Host "Processing $file..."
    
    $phpHeader = @"
<?php
// Page configuration
`$currentPage = '$($p.page)';
`$pageTitle = '$($p.title)';
`$pageSubtitle = '$($p.subtitle)';
`$searchPlaceholder = '$($p.search)';
?>
"@
    
    $content = Get-Content $file -Raw
    
    # Find the start of the duplicated header (after <body>)
    $pattern = '(?s)(<!DOCTYPE html>.*?<body>\s+<!-- Sidebar Toggle Checkbox -->.*?<div class="dashboard-container">\s+<!-- Include Topbar -->)\s+<header class="header">.*?</header>\s+<!-- Include Sidebar -->\s+<aside class="sidebar".*?</aside>'
    
    if ($content -match $pattern) {
        $beforeHeader = $matches[1]
        
        $replacement = @"
$phpHeader
$beforeHeader
        <?php include '../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php include '../common/sidebar.php'; ?>
"@
        
        $content = $content -replace $pattern, $replacement
        $content | Set-Content $file -NoNewline
        Write-Host "Updated $file successfully"
    } else {
        Write-Host "Pattern not found in $file" -ForegroundColor Yellow
    }
}

Write-Host "`nAll files processed!" -ForegroundColor Green
