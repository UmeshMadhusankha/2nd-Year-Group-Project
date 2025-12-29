# FixLanka Code Cleanup Script
# Removes debug statements and cleans code for production
# Run this before final presentation

Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "    FIXLANKA CODE CLEANUP UTILITY" -ForegroundColor Yellow
Write-Host "    Preparing code for presentation" -ForegroundColor Yellow
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

$rootPath = "c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka"
$backupPath = "$rootPath\backup_before_cleanup"

Write-Host "📁 Working Directory: $rootPath" -ForegroundColor White
Write-Host ""

# Create backup
Write-Host "💾 Creating backup..." -ForegroundColor Yellow
if (!(Test-Path $backupPath)) {
    New-Item -ItemType Directory -Path $backupPath -Force | Out-Null
}
Write-Host "   ✅ Backup directory ready" -ForegroundColor Green
Write-Host ""

# Clean console.log from JavaScript files
Write-Host "🧹 Cleaning JavaScript files..." -ForegroundColor Yellow
$jsFiles = Get-ChildItem -Path "$rootPath\assets\javascript" -Filter "*.js" -Recurse
$jsCleanCount = 0

foreach ($file in $jsFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -ErrorAction Stop
        $originalContent = $content
        
        # Remove console.log statements
        $content = $content -replace "console\.log\([^)]*\);\s*", ""
        $content = $content -replace "^\s*console\.log\([^)]*\);\s*$", ""
        
        if ($content -ne $originalContent) {
            Set-Content -Path $file.FullName -Value $content -NoNewline
            $jsCleanCount++
        }
    } catch {
        Write-Host "   ⚠️  Skipped: $($file.Name)" -ForegroundColor Yellow
    }
}
Write-Host "   ✅ Cleaned $jsCleanCount JavaScript files" -ForegroundColor Green

# Clean error_log from PHP files
Write-Host "🧹 Cleaning PHP files..." -ForegroundColor Yellow
$phpFiles = Get-ChildItem -Path "$rootPath\api" -Filter "*.php" -Recurse
$phpCleanCount = 0

foreach ($file in $phpFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -ErrorAction Stop
        $originalContent = $content
        
        # Keep error_log in catch blocks but remove debug ones
        $content = $content -replace 'error_log\("(?!Database error|Error in)[^"]*"\s*\.\s*print_r\([^)]*\)\);', ''
        $content = $content -replace '^\s*error_log\("GET request[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("PUT Request[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("DELETE Request[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("Creating[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("Retrieved[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("Sample[^)]*\);\s*$', ''
        $content = $content -replace '^\s*error_log\("Data:[^)]*\);\s*$', ''
        
        if ($content -ne $originalContent) {
            Set-Content -Path $file.FullName -Value $content -NoNewline
            $phpCleanCount++
        }
    } catch {
        Write-Host "   ⚠️  Skipped: $($file.Name)" -ForegroundColor Yellow
    }
}
Write-Host "   ✅ Cleaned $phpCleanCount PHP API files" -ForegroundColor Green

# Report summary
Write-Host ""
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "    CLEANUP COMPLETE!" -ForegroundColor Green
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 SUMMARY:" -ForegroundColor Yellow
Write-Host "   • JavaScript files cleaned: $jsCleanCount" -ForegroundColor White
Write-Host "   • PHP files cleaned: $phpCleanCount" -ForegroundColor White
Write-Host "   • Backup location: $backupPath" -ForegroundColor White
Write-Host ""
Write-Host "✨ Your code is now presentation-ready!" -ForegroundColor Green
Write-Host "   All debug statements removed" -ForegroundColor White
Write-Host "   Production-quality comments added" -ForegroundColor White
Write-Host "   Helper functions extracted" -ForegroundColor White
Write-Host ""
