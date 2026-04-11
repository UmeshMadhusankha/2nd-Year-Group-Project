<?php
/**
 * Quick schema comparison: create_database.sql vs live DB (information_schema)
 * - Focus: table existence + column existence + column type mismatch (esp. ENUM)
 * - Ignores: adreport table (as requested)
 */

require_once __DIR__ . '/../config/database.php';

$schemaFile = realpath(__DIR__ . '/../create_database.sql');
if (!$schemaFile || !is_file($schemaFile)) {
    fwrite(STDERR, "Schema file not found: FixLanka/create_database.sql\n");
    exit(1);
}

$sqlText = file_get_contents($schemaFile);
if ($sqlText === false) {
    fwrite(STDERR, "Failed to read schema file.\n");
    exit(1);
}

$ignoreTables = ['adreport'];

function normalizeSqlType(string $t): string {
    $t = strtolower(trim($t));
    $t = preg_replace('/\s+/', '', $t);
    return $t;
}

function extractExpectedTablesAndColumns(string $sqlText, array $ignoreTables): array {
    $expected = [];

    // CREATE TABLE ... (`col` type ...)
    $createRe = '/create\s+table\s+(?:if\s+not\s+exists\s+)?`?([a-z0-9_]+)`?\s*\((.*?)\)\s*engine=/is';
    if (preg_match_all($createRe, $sqlText, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $table = strtolower($match[1]);
            if (in_array($table, $ignoreTables, true)) {
                continue;
            }

            $body = $match[2];
            if (!isset($expected[$table])) {
                $expected[$table] = ['columns' => []];
            }

            $lines = preg_split('/\r?\n/', $body);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] !== '`') {
                    continue;
                }

                // `col` type ...,
                if (!preg_match('/^`([^`]+)`\s+([^\s,]+)(?:\s|,|$)/i', $line, $cm)) {
                    continue;
                }

                $col = strtolower($cm[1]);
                $type = $cm[2];
                $expected[$table]['columns'][$col] = normalizeSqlType($type);
            }
        }
    }

    // ALTER TABLE ... ADD COLUMN `col` TYPE ...
    $alterRe = '/alter\s+table\s+`?([a-z0-9_]+)`?\s+(.*?);/is';
    if (preg_match_all($alterRe, $sqlText, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $table = strtolower($match[1]);
            if (in_array($table, $ignoreTables, true)) {
                continue;
            }

            $alterBody = $match[2];
            if (!isset($expected[$table])) {
                $expected[$table] = ['columns' => []];
            }

            if (preg_match_all('/add\s+column\s+`?([a-z0-9_]+)`?\s+([^\s,]+)(?:\s|,|$)/i', $alterBody, $am, PREG_SET_ORDER)) {
                foreach ($am as $addMatch) {
                    $col = strtolower($addMatch[1]);
                    $type = $addMatch[2];
                    $expected[$table]['columns'][$col] = normalizeSqlType($type);
                }
            }
        }
    }

    ksort($expected);
    foreach ($expected as $t => $info) {
        ksort($expected[$t]['columns']);
    }

    return $expected;
}

function fetchActualTables(PDO $pdo, string $dbName): array {
    $stmt = $pdo->prepare(
        'SELECT table_name FROM information_schema.tables WHERE table_schema = :db'
    );
    $stmt->execute([':db' => $dbName]);
    $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $out = [];
    foreach ($rows as $t) {
        $out[] = strtolower((string)$t);
    }
    sort($out);
    return $out;
}

function fetchActualColumns(PDO $pdo, string $dbName, string $table): array {
    $stmt = $pdo->prepare(
        'SELECT column_name, column_type FROM information_schema.columns WHERE table_schema = :db AND table_name = :t'
    );
    $stmt->execute([':db' => $dbName, ':t' => $table]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
        $out[strtolower($r['column_name'])] = normalizeSqlType((string)$r['column_type']);
    }
    ksort($out);
    return $out;
}

$expected = extractExpectedTablesAndColumns($sqlText, $ignoreTables);

// Determine current DB name
$dbName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
if ($dbName === '') {
    fwrite(STDERR, "No database selected on PDO connection.\n");
    exit(1);
}

$actualTables = fetchActualTables($pdo, $dbName);
$expectedTables = array_keys($expected);

$missingTables = array_values(array_diff($expectedTables, $actualTables));
$extraTables = array_values(array_diff($actualTables, $expectedTables));

echo "DB: {$dbName}\n";
echo "Schema file: {$schemaFile}\n";
echo "Ignored tables: " . implode(', ', $ignoreTables) . "\n\n";

if ($missingTables) {
    echo "MISSING TABLES (in DB, but declared in SQL):\n";
    foreach ($missingTables as $t) {
        echo "  - {$t}\n";
    }
    echo "\n";
}

if ($extraTables) {
    echo "EXTRA TABLES (in DB, not declared in SQL):\n";
    foreach ($extraTables as $t) {
        echo "  - {$t}\n";
    }
    echo "\n";
}

$tablesToCheck = array_values(array_intersect($expectedTables, $actualTables));
$anyColumnIssues = false;

foreach ($tablesToCheck as $table) {
    $expectedCols = $expected[$table]['columns'] ?? [];
    $actualCols = fetchActualColumns($pdo, $dbName, $table);

    $missingCols = array_values(array_diff(array_keys($expectedCols), array_keys($actualCols)));
    $extraCols = array_values(array_diff(array_keys($actualCols), array_keys($expectedCols)));

    $typeMismatches = [];
    foreach ($expectedCols as $col => $expType) {
        if (!isset($actualCols[$col])) {
            continue;
        }
        $actType = $actualCols[$col];
        if ($expType !== $actType) {
            $typeMismatches[] = [$col, $expType, $actType];
        }
    }

    if ($missingCols || $extraCols || $typeMismatches) {
        $anyColumnIssues = true;
        echo "TABLE: {$table}\n";

        if ($missingCols) {
            echo "  Missing columns:\n";
            foreach ($missingCols as $c) {
                echo "    - {$c} (expected {$expectedCols[$c]})\n";
            }
        }

        if ($extraCols) {
            echo "  Extra columns:\n";
            foreach ($extraCols as $c) {
                echo "    - {$c} (actual {$actualCols[$c]})\n";
            }
        }

        if ($typeMismatches) {
            echo "  Type mismatches:\n";
            foreach ($typeMismatches as [$c, $e, $a]) {
                echo "    - {$c}: expected {$e} | actual {$a}\n";
            }
        }

        echo "\n";
    }
}

if (!$missingTables && !$extraTables && !$anyColumnIssues) {
    echo "OK: Live DB schema matches create_database.sql (excluding ignored tables).\n";
    exit(0);
}

exit(2);
