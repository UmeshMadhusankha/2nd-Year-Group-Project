<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../models/UserQuotesModel.php';

$model = new UserQuotesModel($pdo);

$testUserIds = [9998, 9996, 1, 2];
$statuses = ['pending','accepted','rejected','completed', null];

foreach ($testUserIds as $uid) {
    echo "\n=== USER {$uid} ===\n";
    foreach ($statuses as $st) {
        try {
            $rows = $model->getUserQuotes($uid, 200, 0, $st);
            $label = $st === null ? 'all' : $st;
            echo str_pad($label, 10) . ': ' . count($rows) . "\n";
            if (count($rows) > 0 && ($st === 'pending' || $st === null)) {
                $sample = array_slice($rows, 0, 5);
                foreach ($sample as $r) {
                    echo '  - src=' . ($r['source'] ?? '') . ' q=' . ($r['quote_id'] ?? '') . ' req=' . ($r['request_id'] ?? '') . ' status=' . ($r['status'] ?? '') . ' has_contract=' . ($r['has_contract'] ?? '') . "\n";
                }
            }
        } catch (Throwable $e) {
            $label = $st === null ? 'all' : $st;
            echo str_pad($label, 10) . ': ERROR => ' . $e->getMessage() . "\n";
        }
    }
}
