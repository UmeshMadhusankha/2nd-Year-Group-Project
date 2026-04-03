<?php
require __DIR__ . '/../config/database.php';

header('Content-Type: text/plain');

echo "Latest advertisements with schedule counts\n";
echo "ad_id\tstatus\tschedules\tstart..end\ttitle\n";

$sql = "SELECT a.ad_id,a.title,a.status,a.start_date,a.end_date,COUNT(s.schedule_id) AS schedules
        FROM advertisement a
        LEFT JOIN adschedule s ON s.ad_id=a.ad_id
        GROUP BY a.ad_id
        ORDER BY a.ad_id DESC
        LIMIT 20";

$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['ad_id'] . "\t" . $r['status'] . "\t" . $r['schedules'] . "\t" . $r['start_date'] . ".." . $r['end_date'] . "\t" . $r['title'] . "\n";
}

echo "\nLatest schedules with linked ad status\n";
echo "schedule_id\tad_id\tad_status\tschedule_range\ttime\ttitle\n";

$sql2 = "SELECT s.schedule_id,s.ad_id,a.status AS ad_status,a.title,s.start_date,s.end_date,
                COALESCE(s.start_time,'00:00:00') AS st, COALESCE(s.end_time,'23:59:59') AS et
         FROM adschedule s
         INNER JOIN advertisement a ON a.ad_id=s.ad_id
         ORDER BY s.schedule_id DESC
         LIMIT 20";

$rows2 = $pdo->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows2 as $r) {
    echo $r['schedule_id'] . "\t" . $r['ad_id'] . "\t" . $r['ad_status'] . "\t" . $r['start_date'] . ".." . $r['end_date'] . "\t" . $r['st'] . "-" . $r['et'] . "\t" . $r['title'] . "\n";
}

echo "\nAds that have schedules but are still stored as APPROVED\n";
echo "ad_id\tstatus\tschedules\ttitle\n";

$sql3 = "SELECT a.ad_id,a.title,a.status,COUNT(s.schedule_id) AS schedules
         FROM advertisement a
         INNER JOIN adschedule s ON s.ad_id=a.ad_id
         GROUP BY a.ad_id
         HAVING LOWER(a.status)='approved'
         ORDER BY a.ad_id DESC";

$rows3 = $pdo->query($sql3)->fetchAll(PDO::FETCH_ASSOC);
if (!$rows3) {
    echo "(none)\n";
} else {
    foreach ($rows3 as $r) {
        echo $r['ad_id'] . "\t" . $r['status'] . "\t" . $r['schedules'] . "\t" . $r['title'] . "\n";
    }
}
