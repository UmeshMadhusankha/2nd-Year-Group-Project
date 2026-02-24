<?php
require_once 'config/database.php';

try {
    $stmt = $pdo->query("DESCRIBE companyquotation");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Columns in companyquotation:\n";
    foreach ($columns as $col) {
        echo "- $col\n";
    }
    
    $required = [
        'work_schedule_type', 'working_days_per_week', 'daily_work_hours',
        'work_start_time', 'work_end_time', 'custom_schedule_details',
        'total_work_hours', 'overtime_available', 'overtime_rate'
    ];
    
    $missing = array_diff($required, $columns);
    
    if (empty($missing)) {
        echo "\nSUCCESS: All required columns are present.\n";
    } else {
        echo "\nFAILURE: Missing columns:\n";
        print_r($missing);
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
