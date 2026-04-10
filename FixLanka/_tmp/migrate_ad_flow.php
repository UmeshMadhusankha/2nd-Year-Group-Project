<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/plain');

function tableExists(PDO $pdo, string $table): bool {
    $stmt = $pdo->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :t LIMIT 1');
    $stmt->execute([':t' => $table]);
    return (bool)$stmt->fetchColumn();
}

function columnType(PDO $pdo, string $table, string $column): ?string {
    $stmt = $pdo->prepare(
        'SELECT column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :t AND column_name = :c LIMIT 1'
    );
    $stmt->execute([':t' => $table, ':c' => $column]);
    $val = $stmt->fetchColumn();
    return $val === false ? null : (string)$val;
}

function execOk(PDO $pdo, string $sql): void {
    $pdo->exec($sql);
}

function countRows(PDO $pdo, string $table): int {
    $stmt = $pdo->query('SELECT COUNT(*) FROM `' . str_replace('`', '``', $table) . '`');
    return (int)$stmt->fetchColumn();
}

echo "DB: " . $pdo->query('SELECT DATABASE()')->fetchColumn() . "\n";

// 1) Ensure advertisement.status supports the new lifecycle.
if (tableExists($pdo, 'advertisement')) {
    $statusType = columnType($pdo, 'advertisement', 'status') ?? '';
    if (stripos($statusType, "'expired'") === false) {
        echo "Updating advertisement.status ENUM to include 'expired'...\n";
        // Keep existing values and add expired.
        execOk($pdo,
            "ALTER TABLE advertisement MODIFY COLUMN status "
            . "ENUM('pending','approved','rejected','scheduled','active','paused','inactive','suspended','expired') "
            . "NOT NULL DEFAULT 'pending'"
        );
    } else {
        echo "advertisement.status already contains 'expired'.\n";
    }
} else {
    echo "WARNING: advertisement table is missing; skipping ENUM update.\n";
}

// 2) Create adrotationsettings (optional, but needed for moderator rotation settings UI).
if (!tableExists($pdo, 'adrotationsettings')) {
    echo "Creating adrotationsettings table...\n";
    execOk($pdo, "CREATE TABLE adrotationsettings (\n"
        . "  setting_id int(11) NOT NULL AUTO_INCREMENT,\n"
        . "  banner_seconds int(11) NOT NULL DEFAULT 30,\n"
        . "  featured_seconds int(11) NOT NULL DEFAULT 60,\n"
        . "  sponsored_seconds int(11) NOT NULL DEFAULT 90,\n"
        . "  banner_capacity int(11) NOT NULL DEFAULT 5,\n"
        . "  featured_capacity int(11) NOT NULL DEFAULT 3,\n"
        . "  sponsored_capacity int(11) NOT NULL DEFAULT 8,\n"
        . "  updated_by int(11) DEFAULT NULL,\n"
        . "  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),\n"
        . "  PRIMARY KEY (setting_id)\n"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );
} else {
    echo "adrotationsettings already exists.\n";
}

if (tableExists($pdo, 'adrotationsettings')) {
    if (countRows($pdo, 'adrotationsettings') === 0) {
        echo "Seeding default adrotationsettings row...\n";
        execOk($pdo, "INSERT INTO adrotationsettings (banner_seconds, featured_seconds, sponsored_seconds, banner_capacity, featured_capacity, sponsored_capacity)\n"
            . "VALUES (30, 60, 90, 5, 3, 8)"
        );
    }
}

// 3) Create adreport table (required for moderator reports page).
if (!tableExists($pdo, 'adreport')) {
    echo "Creating adreport table...\n";
    execOk($pdo, "CREATE TABLE adreport (\n"
        . "  report_id int(11) NOT NULL AUTO_INCREMENT,\n"
        . "  ad_id int(11) NOT NULL,\n"
        . "  reporter_type enum('user','repairer','company') NOT NULL DEFAULT 'user',\n"
        . "  reporter_id int(11) DEFAULT NULL,\n"
        . "  issue_type enum('inappropriate_content','misleading_information','spam','privacy_violation','copyright_infringement','fraud','other') NOT NULL DEFAULT 'other',\n"
        . "  description text NOT NULL,\n"
        . "  priority enum('low','medium','high','critical') NOT NULL DEFAULT 'low',\n"
        . "  status enum('pending','investigating','resolved','dismissed','escalated') NOT NULL DEFAULT 'pending',\n"
        . "  moderator_notes text DEFAULT NULL,\n"
        . "  evidence varchar(500) DEFAULT NULL,\n"
        . "  handled_by int(11) DEFAULT NULL,\n"
        . "  created_at timestamp NOT NULL DEFAULT current_timestamp(),\n"
        . "  updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),\n"
        . "  PRIMARY KEY (report_id),\n"
        . "  KEY idx_ad (ad_id),\n"
        . "  KEY idx_status (status),\n"
        . "  KEY idx_priority (priority)\n"
        . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    // Best-effort FK add (skip if it fails).
    try {
        execOk($pdo, "ALTER TABLE adreport ADD CONSTRAINT adreport_ibfk_1 FOREIGN KEY (ad_id) REFERENCES advertisement(ad_id) ON DELETE CASCADE");
    } catch (Throwable $e) {
        echo "NOTE: Could not add FK adreport->advertisement (may already exist or table engine mismatch).\n";
    }
} else {
    echo "adreport already exists.\n";
}

// 4) Optional one-time migration from existing ad_reports -> adreport.
if (tableExists($pdo, 'ad_reports') && tableExists($pdo, 'adreport')) {
    $adreportCount = countRows($pdo, 'adreport');
    if ($adreportCount === 0) {
        echo "Migrating data from ad_reports -> adreport (best-effort)...\n";
        $sql = "INSERT INTO adreport (\n"
            . "  ad_id, reporter_type, reporter_id, issue_type, description, priority, status, moderator_notes, evidence, handled_by, created_at, updated_at\n"
            . ")\n"
            . "SELECT\n"
            . "  ar.ad_id,\n"
            . "  (CASE WHEN ar.reporter_type = 'moderator' THEN 'user' ELSE ar.reporter_type END) AS reporter_type,\n"
            . "  ar.reporter_id,\n"
            . "  (CASE\n"
            . "     WHEN ar.report_category = 'copyright_violation' THEN 'copyright_infringement'\n"
            . "     WHEN ar.report_category = 'false_advertising' THEN 'misleading_information'\n"
            . "     WHEN ar.report_category = 'offensive_material' THEN 'inappropriate_content'\n"
            . "     WHEN ar.report_category IN ('broken_link','poor_quality') THEN 'other'\n"
            . "     WHEN ar.report_category IN ('inappropriate_content','misleading_information','spam','other') THEN ar.report_category\n"
            . "     ELSE 'other'\n"
            . "   END) AS issue_type,\n"
            . "  ar.description,\n"
            . "  ar.severity AS priority,\n"
            . "  ar.status,\n"
            . "  ar.resolution_notes AS moderator_notes,\n"
            . "  NULL AS evidence,\n"
            . "  ar.assigned_to AS handled_by,\n"
            . "  ar.created_at,\n"
            . "  ar.updated_at\n"
            . "FROM ad_reports ar";

        try {
            execOk($pdo, $sql);
            echo "Migrated rows: " . countRows($pdo, 'adreport') . "\n";
        } catch (Throwable $e) {
            echo "WARNING: Migration from ad_reports failed: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Skipping migration: adreport already has {$adreportCount} rows.\n";
    }
}

echo "DONE\n";
