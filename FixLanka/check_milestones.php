<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/models/ProjectModel.php';

$projectId = 46;
$projectModel = new Project($pdo);
$phases = $projectModel->getContractPhases($projectId);

echo "Project ID: $projectId\n";
echo "Title: I need to fix the table\n";
echo "Milestones:\n";
print_r($phases['data']);
