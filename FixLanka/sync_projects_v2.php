<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/models/ProjectModel.php';

$projectModel = new Project($pdo);

// Fetch all projects (including completed ones this time to sync their quotes/requests)
$stmt = $pdo->query("SELECT project_id, title, status FROM Project WHERE status NOT IN ('cancelled')");
$projects = $stmt->fetchAll();

echo "Starting project sync (with quote/request status)...\n";

foreach ($projects as $proj) {
    $pid = $proj['project_id'];
    $title = $proj['title'];
    echo "Processing Project #$pid ($title)... ";
    
    // Recalculating progress will trigger updateStatus side effects if status is or becomes 'completed'
    $result = $projectModel->recalculateProjectProgress($pid);
    
    if ($result['success']) {
        echo "Progress: {$result['progress']}% " . ($result['is_completed'] ? "[COMPLETED]" : "") . "\n";
    } else {
        echo "Error: {$result['message']}\n";
    }
}

echo "Sync finished.\n";
