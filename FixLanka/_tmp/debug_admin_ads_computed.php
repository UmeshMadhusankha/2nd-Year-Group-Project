<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../models/AdminAdvertisementModel.php';

header('Content-Type: text/plain');

$model = new AdminAdvertisementModel($pdo);
$ads = $model->getAdvertisements([], 200);

echo "ad_id\tstored\tcomputed\ttitle\n";
foreach ($ads as $a) {
    echo ($a['ad_id'] ?? '') . "\t" . ($a['status'] ?? '') . "\t" . ($a['computed_status'] ?? '') . "\t" . ($a['title'] ?? '') . "\n";
}

echo "\nFiltered by status=approved (should include scheduled/active/etc)\n";
$ads2 = $model->getAdvertisements(['status' => 'approved'], 200);
echo "ad_id\tstored\tcomputed\ttitle\n";
foreach ($ads2 as $a) {
    echo ($a['ad_id'] ?? '') . "\t" . ($a['status'] ?? '') . "\t" . ($a['computed_status'] ?? '') . "\t" . ($a['title'] ?? '') . "\n";
}
