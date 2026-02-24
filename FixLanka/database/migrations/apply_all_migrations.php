<?php
/**
 * Apply All Phase Migrations
 * 
 * Safely runs all ALTER TABLE and CREATE TABLE statements.
 * Skips any that already exist (handles duplicate column/table errors gracefully).
 * 
 * Run via browser: http://localhost/2nd-Year-Group-Project/FixLanka/database/migrations/apply_all_migrations.php
 */

require_once __DIR__ . '/../../config/database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>FixLanka — Apply All Phase Migrations</h1>";
echo "<pre>";

$sqlFile = __DIR__ . '/phase_all_complete.sql';
if (!file_exists($sqlFile)) {
    die("ERROR: Migration file not found: $sqlFile");
}

$sql = file_get_contents($sqlFile);

// Split into individual statements (by semicolons not inside quotes)
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

$applied = 0;
$skipped = 0;
$errors = 0;
$total = count($statements);

foreach ($statements as $i => $stmt) {
    // Skip comments-only blocks
    $cleaned = trim(preg_replace('/--[^\n]*/', '', $stmt));
    if (empty($cleaned)) {
        $total--;
        continue;
    }
    
    // Extract a short label for display
    if (preg_match('/CREATE TABLE.*?`(\w+)`/i', $stmt, $m)) {
        $label = "CREATE TABLE `{$m[1]}`";
    } elseif (preg_match('/ALTER TABLE\s+`(\w+)`\s+ADD\s+(COLUMN|INDEX|CONSTRAINT|KEY)\s+`?(\w+)`?/i', $stmt, $m)) {
        $label = "ALTER `{$m[1]}` ADD {$m[2]} `{$m[3]}`";
    } elseif (preg_match('/ALTER TABLE\s+`(\w+)`/i', $stmt, $m)) {
        $label = "ALTER `{$m[1]}`";
    } else {
        $label = substr($cleaned, 0, 60) . '...';
    }

    try {
        $pdo->exec($stmt);
        echo "<span style='color:green'>[APPLIED]</span> $label\n";
        $applied++;
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Check if it's a "duplicate" error (column/table/index already exists)
        if (strpos($msg, 'Duplicate column name') !== false ||
            strpos($msg, 'Duplicate key name') !== false ||
            strpos($msg, 'already exists') !== false ||
            strpos($msg, '1060') !== false || // MySQL duplicate column
            strpos($msg, '1061') !== false || // MySQL duplicate key
            strpos($msg, '1050') !== false) { // MySQL table already exists
            echo "<span style='color:orange'>[SKIPPED]</span> $label (already exists)\n";
            $skipped++;
        } else {
            echo "<span style='color:red'>[ERROR]</span> $label\n";
            echo "    → $msg\n";
            $errors++;
        }
    }
}

echo "\n";
echo "====================================\n";
echo "Total statements: $total\n";
echo "<span style='color:green'>Applied: $applied</span>\n";
echo "<span style='color:orange'>Skipped (already exist): $skipped</span>\n";
if ($errors > 0) {
    echo "<span style='color:red'>Errors: $errors</span>\n";
} else {
    echo "Errors: 0\n";
}
echo "====================================\n";

if ($errors === 0) {
    echo "\n<span style='color:green; font-weight:bold'>✅ All migrations applied successfully!</span>\n";
} else {
    echo "\n<span style='color:red; font-weight:bold'>⚠️ Some migrations had errors. Review above.</span>\n";
}

echo "</pre>";
