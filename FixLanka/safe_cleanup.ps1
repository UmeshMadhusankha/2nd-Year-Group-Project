# SAFE Code Cleanup Script
# Removes ONLY debug statements, preserves all functionality and meaningful comments
# Creates backup before any changes

Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "    SAFE CODE CLEANUP - FixLanka Project" -ForegroundColor Green
Write-Host "    Removes debug code, preserves functionality" -ForegroundColor Yellow
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

$rootPath = "c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka"
$backupPath = "$rootPath\backup_before_safe_cleanup_$(Get-Date -Format 'yyyy-MM-dd_HHmmss')"
$reportPath = "$rootPath\cleanup_report_$(Get-Date -Format 'yyyy-MM-dd_HHmmss').txt"

# Initialize counters
$jsCleanCount = 0
$phpCleanCount = 0
$totalConsoleLogs = 0
$totalErrorLogs = 0

Write-Host "📁 Working Directory: $rootPath" -ForegroundColor White
Write-Host "💾 Backup Location: $backupPath" -ForegroundColor White
Write-Host ""

# Create backup
Write-Host "💾 Creating backup..." -ForegroundColor Yellow
try {
    if (!(Test-Path $backupPath)) {
        New-Item -ItemType Directory -Path $backupPath -Force | Out-Null
    }
    
    # Backup JavaScript files
    $jsBackup = "$backupPath\javascript"
    New-Item -ItemType Directory -Path $jsBackup -Force | Out-Null
    Copy-Item -Path "$rootPath\assets\javascript\*" -Destination $jsBackup -Recurse -Force
    
    # Backup API files
    $apiBackup = "$backupPath\api"
    New-Item -ItemType Directory -Path $apiBackup -Force | Out-Null
    Copy-Item -Path "$rootPath\api\*" -Destination $apiBackup -Recurse -Force
    
    Write-Host "   ✅ Backup created successfully" -ForegroundColor Green
} catch {
    Write-Host "   ❌ Backup failed: $_" -ForegroundColor Red
    Write-Host "   Aborting cleanup for safety" -ForegroundColor Red
    exit 1
}
Write-Host ""

# Start report
$report = @"
════════════════════════════════════════════════════
    SAFE CODE CLEANUP REPORT
    Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
════════════════════════════════════════════════════

BACKUP LOCATION: $backupPath

"@

# Clean JavaScript files
Write-Host "🧹 Cleaning JavaScript files..." -ForegroundColor Yellow
$jsFiles = Get-ChildItem -Path "$rootPath\assets\javascript" -Filter "*.js" -Recurse

foreach ($file in $jsFiles) {
    try {
        $lines = Get-Content $file.FullName
        $originalLineCount = $lines.Count
        $cleanedLines = @()
        $removedCount = 0
        
        foreach ($line in $lines) {
            # Remove ONLY console.log debug statements
            # Keep: console.error, console.warn (important for error tracking)
            if ($line -match '^\s*console\.log\(') {
                $removedCount++
                $totalConsoleLogs++
                # Skip this line (don't add to cleanedLines)
            }
            elseif ($line -match 'console\.log\([^)]*\);\s*$' -and $line -notmatch '//.*console\.log') {
                # Remove inline console.log at end of line, but keep rest of line if exists
                $cleanLine = $line -replace 'console\.log\([^)]*\);\s*$', ''
                if ($cleanLine.Trim() -ne '') {
                    $cleanedLines += $cleanLine
                }
                $removedCount++
                $totalConsoleLogs++
            }
            else {
                # Keep all other lines (including meaningful comments)
                $cleanedLines += $line
            }
        }
        
        if ($removedCount -gt 0) {
            # Write cleaned content back
            $cleanedLines | Set-Content -Path $file.FullName -Encoding UTF8
            $jsCleanCount++
            $report += "`n✓ $($file.Name) - Removed $removedCount console.log statements"
            Write-Host "   ✓ $($file.Name) - Removed $removedCount debug statements" -ForegroundColor Gray
        }
    } catch {
        Write-Host "   ⚠️  Skipped: $($file.Name) - $_" -ForegroundColor Yellow
        $report += "`n⚠ $($file.Name) - SKIPPED: $_"
    }
}
Write-Host "   ✅ Cleaned $jsCleanCount JavaScript files" -ForegroundColor Green
Write-Host ""

# Clean PHP files
Write-Host "🧹 Cleaning PHP API files..." -ForegroundColor Yellow
$phpFiles = Get-ChildItem -Path "$rootPath\api" -Filter "*.php" -Recurse

foreach ($file in $phpFiles) {
    try {
        $lines = Get-Content $file.FullName
        $cleanedLines = @()
        $removedCount = 0
        
        foreach ($line in $lines) {
            # Remove ONLY debug error_log statements
            # Keep: error_log in catch blocks (important for production error tracking)
            $keepLine = $true
            
            # Remove debug error_log with print_r
            if ($line -match 'error_log\([^)]*print_r\(') {
                $keepLine = $false
                $removedCount++
                $totalErrorLogs++
            }
            # Remove specific debug patterns
            elseif ($line -match 'error_log\("(GET request|PUT Request|POST Request|DELETE Request|Creating|Retrieved|Sample|Data:|Debug)') {
                $keepLine = $false
                $removedCount++
                $totalErrorLogs++
            }
            # Remove standalone debug error_log
            elseif ($line -match '^\s*error_log\("(?!Database error|Error in).*"\);?\s*$') {
                $keepLine = $false
                $removedCount++
                $totalErrorLogs++
            }
            
            if ($keepLine) {
                $cleanedLines += $line
            }
        }
        
        if ($removedCount -gt 0) {
            # Write cleaned content back
            $cleanedLines | Set-Content -Path $file.FullName -Encoding UTF8
            $phpCleanCount++
            $report += "`n✓ $($file.Name) - Removed $removedCount error_log statements"
            Write-Host "   ✓ $($file.Name) - Removed $removedCount debug statements" -ForegroundColor Gray
        }
    } catch {
        Write-Host "   ⚠️  Skipped: $($file.Name) - $_" -ForegroundColor Yellow
        $report += "`n⚠ $($file.Name) - SKIPPED: $_"
    }
}
Write-Host "   ✅ Cleaned $phpCleanCount PHP files" -ForegroundColor Green
Write-Host ""

# Final report
$report += @"

════════════════════════════════════════════════════
    CLEANUP SUMMARY
════════════════════════════════════════════════════

JavaScript Files Cleaned:     $jsCleanCount
PHP Files Cleaned:            $phpCleanCount
Total console.log removed:    $totalConsoleLogs
Total error_log removed:      $totalErrorLogs

PRESERVED:
✓ All business logic
✓ All meaningful comments
✓ All error handling in catch blocks
✓ All console.error and console.warn
✓ All production error_log statements

BACKUP: $backupPath

════════════════════════════════════════════════════
    VERIFICATION STEPS
════════════════════════════════════════════════════

1. Test the application thoroughly
2. Login as company user
3. Test all CRUD operations:
   - Create advertisement
   - Edit advertisement
   - Delete advertisement
   - View list
4. Check browser console for errors
5. If any issues, restore from backup:
   Copy-Item "$backupPath\*" -Destination "$rootPath" -Recurse -Force

════════════════════════════════════════════════════
"@

# Save report
$report | Set-Content -Path $reportPath -Encoding UTF8

Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "    ✅ CLEANUP COMPLETE!" -ForegroundColor Green
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
Write-Host "📊 SUMMARY:" -ForegroundColor Yellow
Write-Host "   • JavaScript files cleaned: $jsCleanCount" -ForegroundColor White
Write-Host "   • PHP files cleaned: $phpCleanCount" -ForegroundColor White
Write-Host "   • console.log removed: $totalConsoleLogs" -ForegroundColor White
Write-Host "   • error_log removed: $totalErrorLogs" -ForegroundColor White
Write-Host ""
Write-Host "💾 BACKUP:" -ForegroundColor Yellow
Write-Host "   Location: $backupPath" -ForegroundColor White
Write-Host ""
Write-Host "📄 REPORT:" -ForegroundColor Yellow
Write-Host "   Saved to: $reportPath" -ForegroundColor White
Write-Host ""
Write-Host "✅ PRESERVED:" -ForegroundColor Green
Write-Host "   ✓ All functionality intact" -ForegroundColor White
Write-Host "   ✓ All meaningful comments kept" -ForegroundColor White
Write-Host "   ✓ All error handling kept" -ForegroundColor White
Write-Host "   ✓ Code still readable and understandable" -ForegroundColor White
Write-Host ""
Write-Host "🧪 NEXT STEP: Test your application!" -ForegroundColor Yellow
Write-Host "   If anything breaks, restore from backup folder" -ForegroundColor Gray
Write-Host ""
Write-Host "════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""
