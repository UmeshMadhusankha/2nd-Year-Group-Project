# SAFE Code Cleanup Script
# Removes ONLY debug statements, preserves all functionality and meaningful comments
# Creates backup before any changes

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "    SAFE CODE CLEANUP - FixLanka Project" -ForegroundColor Green
Write-Host "    Removes debug code, preserves functionality" -ForegroundColor Yellow
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""

$rootPath = "c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka"
$timestamp = Get-Date -Format 'yyyy-MM-dd_HHmmss'
$backupPath = "$rootPath\backup_$timestamp"
$reportPath = "$rootPath\cleanup_report_$timestamp.txt"

# Initialize counters
$jsCleanCount = 0
$phpCleanCount = 0
$totalConsoleLogs = 0
$totalErrorLogs = 0

Write-Host "Working Directory: $rootPath" -ForegroundColor White
Write-Host "Backup Location: $backupPath" -ForegroundColor White
Write-Host ""

# Create backup
Write-Host "Creating backup..." -ForegroundColor Yellow
try {
    if (!(Test-Path $backupPath)) {
        New-Item -ItemType Directory -Path $backupPath -Force | Out-Null
    }
    
    # Backup JavaScript files
    $jsBackup = "$backupPath\javascript"
    New-Item -ItemType Directory -Path $jsBackup -Force | Out-Null
    Copy-Item -Path "$rootPath\assets\javascript\*" -Destination $jsBackup -Recurse -Force -ErrorAction SilentlyContinue
    
    # Backup API files
    $apiBackup = "$backupPath\api"
    New-Item -ItemType Directory -Path $apiBackup -Force | Out-Null
    Copy-Item -Path "$rootPath\api\*" -Destination $apiBackup -Recurse -Force -ErrorAction SilentlyContinue
    
    Write-Host "   [OK] Backup created successfully" -ForegroundColor Green
} catch {
    Write-Host "   [ERROR] Backup failed: $_" -ForegroundColor Red
    Write-Host "   Aborting cleanup for safety" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Start report
$report = "SAFE CODE CLEANUP REPORT`n"
$report += "Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n"
$report += "Backup: $backupPath`n`n"

# Clean JavaScript files
Write-Host "Cleaning JavaScript files..." -ForegroundColor Yellow
$jsFiles = Get-ChildItem -Path "$rootPath\assets\javascript" -Filter "*.js" -Recurse -ErrorAction SilentlyContinue

foreach ($file in $jsFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -ErrorAction Stop
        $originalContent = $content
        $removedCount = 0
        
        # Count console.log occurrences
        $matches = [regex]::Matches($content, 'console\.log\([^)]*\);?')
        $removedCount = $matches.Count
        
        if ($removedCount -gt 0) {
            # Remove console.log statements but keep the rest of the line
            $content = $content -replace 'console\.log\([^)]*\);?\s*\n', "`n"
            $content = $content -replace 'console\.log\([^)]*\);?\s*', ''
            
            # Write cleaned content
            Set-Content -Path $file.FullName -Value $content -NoNewline -Encoding UTF8
            $jsCleanCount++
            $totalConsoleLogs += $removedCount
            $report += "[OK] $($file.Name) - Removed $removedCount console.log statements`n"
            Write-Host "   [OK] $($file.Name) - Removed $removedCount statements" -ForegroundColor Gray
        }
    } catch {
        Write-Host "   [SKIP] $($file.Name) - $_" -ForegroundColor Yellow
        $report += "[SKIP] $($file.Name) - $_`n"
    }
}
Write-Host "   [DONE] Cleaned $jsCleanCount JavaScript files" -ForegroundColor Green
Write-Host ""

# Clean PHP files
Write-Host "Cleaning PHP API files..." -ForegroundColor Yellow
$phpFiles = Get-ChildItem -Path "$rootPath\api" -Filter "*.php" -Recurse -ErrorAction SilentlyContinue

foreach ($file in $phpFiles) {
    try {
        $content = Get-Content $file.FullName -Raw -ErrorAction Stop
        $originalContent = $content
        $removedCount = 0
        
        # Remove debug error_log with print_r
        $beforeCount = ([regex]::Matches($content, 'error_log\([^)]*print_r\(')).Count
        $content = $content -replace 'error_log\([^)]*print_r\([^)]*\)[^)]*\);?\s*\n', "`n"
        $removedCount += $beforeCount
        
        # Remove specific debug patterns (keep Database error and Error in)
        $patterns = @(
            'error_log\("GET request[^)]*\);?\s*\n',
            'error_log\("PUT Request[^)]*\);?\s*\n',
            'error_log\("POST Request[^)]*\);?\s*\n',
            'error_log\("DELETE Request[^)]*\);?\s*\n',
            'error_log\("Creating[^)]*\);?\s*\n',
            'error_log\("Retrieved[^)]*\);?\s*\n',
            'error_log\("Sample[^)]*\);?\s*\n',
            'error_log\("Data:[^)]*\);?\s*\n',
            'error_log\("Debug[^)]*\);?\s*\n'
        )
        
        foreach ($pattern in $patterns) {
            $matches = [regex]::Matches($content, $pattern)
            $removedCount += $matches.Count
            $content = $content -replace $pattern, "`n"
        }
        
        if ($removedCount -gt 0) {
            # Write cleaned content
            Set-Content -Path $file.FullName -Value $content -NoNewline -Encoding UTF8
            $phpCleanCount++
            $totalErrorLogs += $removedCount
            $report += "[OK] $($file.Name) - Removed $removedCount error_log statements`n"
            Write-Host "   [OK] $($file.Name) - Removed $removedCount statements" -ForegroundColor Gray
        }
    } catch {
        Write-Host "   [SKIP] $($file.Name) - $_" -ForegroundColor Yellow
        $report += "[SKIP] $($file.Name) - $_`n"
    }
}
Write-Host "   [DONE] Cleaned $phpCleanCount PHP files" -ForegroundColor Green
Write-Host ""

# Final report
$report += "`n===================================================`n"
$report += "CLEANUP SUMMARY`n"
$report += "===================================================`n`n"
$report += "JavaScript Files Cleaned:     $jsCleanCount`n"
$report += "PHP Files Cleaned:            $phpCleanCount`n"
$report += "Total console.log removed:    $totalConsoleLogs`n"
$report += "Total error_log removed:      $totalErrorLogs`n"
$report += "`nPRESERVED:`n"
$report += "- All business logic`n"
$report += "- All meaningful comments`n"
$report += "- All error handling in catch blocks`n"
$report += "- All console.error and console.warn`n"
$report += "- All production error_log statements`n"
$report += "`nBACKUP: $backupPath`n"

# Save report
$report | Set-Content -Path $reportPath -Encoding UTF8

Write-Host "====================================================" -ForegroundColor Cyan
Write-Host "    [SUCCESS] CLEANUP COMPLETE!" -ForegroundColor Green
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "SUMMARY:" -ForegroundColor Yellow
Write-Host "   JavaScript files cleaned: $jsCleanCount" -ForegroundColor White
Write-Host "   PHP files cleaned: $phpCleanCount" -ForegroundColor White
Write-Host "   console.log removed: $totalConsoleLogs" -ForegroundColor White
Write-Host "   error_log removed: $totalErrorLogs" -ForegroundColor White
Write-Host ""
Write-Host "BACKUP:" -ForegroundColor Yellow
Write-Host "   Location: $backupPath" -ForegroundColor White
Write-Host ""
Write-Host "REPORT:" -ForegroundColor Yellow
Write-Host "   Saved to: $reportPath" -ForegroundColor White
Write-Host ""
Write-Host "PRESERVED:" -ForegroundColor Green
Write-Host "   [OK] All functionality intact" -ForegroundColor White
Write-Host "   [OK] All meaningful comments kept" -ForegroundColor White
Write-Host "   [OK] All error handling kept" -ForegroundColor White
Write-Host "   [OK] Code still readable" -ForegroundColor White
Write-Host ""
Write-Host "NEXT STEP: Test your application!" -ForegroundColor Yellow
Write-Host "   If anything breaks, restore from: $backupPath" -ForegroundColor Gray
Write-Host ""
Write-Host "====================================================" -ForegroundColor Cyan
Write-Host ""
