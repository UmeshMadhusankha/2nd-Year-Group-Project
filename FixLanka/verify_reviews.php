<?php
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/config/database.php';
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/models/ProjectModel.php';
require_once 'c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/models/JobCollaborationModel.php';

$projectId = 46;
echo "Verifying sync for Project #$projectId...\n";

$projectModel = new Project($pdo);
$collabModel = new JobCollaborationModel($pdo);

// 1. Check JobRequest Status BEFORE update
$stmt = $pdo->prepare("SELECT job_request_id FROM contract WHERE project_id = :pid ORDER BY contract_id DESC LIMIT 1");
$stmt->execute([':pid' => $projectId]);
$requestId = $stmt->fetchColumn();

if ($requestId) {
    $stmtStatus = $pdo->prepare("SELECT status FROM jobrequest WHERE request_id = :rid");
    $stmtStatus->execute([':rid' => $requestId]);
    $initialStatus = $stmtStatus->fetchColumn();
    echo "Initial JobRequest #$requestId Status: $initialStatus\n";
}

// 2. Trigger updateStatus
$result = $projectModel->updateStatus($projectId, 'completed');
echo "updateStatus result: " . json_encode($result) . "\n";

if ($requestId) {
    // 3. Check JobRequest Status AFTER update
    $stmtStatus->execute([':rid' => $requestId]);
    $afterStatus = $stmtStatus->fetchColumn();
    echo "After update JobRequest #$requestId Status: $afterStatus\n";

    // 4. Check job_collaboration record
    $stmtCollab = $pdo->prepare("SELECT * FROM job_collaboration WHERE request_id = :rid");
    $stmtCollab->execute([':rid' => $requestId]);
    $collab = $stmtCollab->fetch(PDO::FETCH_ASSOC);
    
    if ($collab) {
        echo "Collaboration found! ID: {$collab['collaboration_id']}, Phase: {$collab['current_phase']}\n";
        echo "User ID: {$collab['user_id']}, Provider ID: {$collab['provider_id']}, Provider Role: {$collab['provider_role']}\n";
        echo "User Completed: {$collab['user_completed_at']}, Provider Completed: {$collab['provider_completed_at']}\n";
        echo "User Paid: {$collab['user_payment_confirmed_at']}, Provider Paid: {$collab['provider_payment_confirmed_at']}\n";
        
        if ($collab['current_phase'] === 'review') {
            echo "SUCCESS: Collaboration is in review phase!\n";
        } else {
            // Manually try to sync again with debug info
            echo "Attempting manual sync via model...\n";
            $collabModel->syncStateWithJobRequest($collab['collaboration_id']);
            
            // Re-fetch
            $stmtCollab->execute([':rid' => $requestId]);
            $collabAfter = $stmtCollab->fetch(PDO::FETCH_ASSOC);
            echo "After manual sync Phase: {$collabAfter['current_phase']}\n";
        }
    } else {
        echo "No collaboration record found for Request #$requestId\n";
    }
}
