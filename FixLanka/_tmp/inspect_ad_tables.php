<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain');

$tables = [
    'advertisement',
    'adschedule',
    'ad_schedules',
    'adrotationsettings',
    'adreport',
    'ad_reports',
];

function cols(PDO $pdo, string $table): array {
    $stmt = $pdo->prepare(
        "SELECT column_name, column_type, is_nullable, column_default, extra \n"
        . "FROM information_schema.columns \n"
        . "WHERE table_schema = DATABASE() AND table_name = :t \n"
        . "ORDER BY ordinal_position"
    );
    $stmt->execute([':t' => $table]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

foreach ($tables as $t) {
    echo "\n=== {$t} ===\n";

    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t'
    );
    $stmt->execute([':t' => $t]);
    $exists = (int)$stmt->fetchColumn() === 1;

    if (!$exists) {
        echo "(missing)\n";
        continue;
    }

    foreach (cols($pdo, $t) as $c) {
        $default = $c['column_default'];
        if ($default === null) {
            $default = 'NULL';
        }
        echo sprintf(
            "- %-24s %-40s nullable=%-3s default=%-12s extra=%s\n",
            $c['column_name'],
            $c['column_type'],
            $c['is_nullable'],
            $default,
            $c['extra']
        );
    }
}
