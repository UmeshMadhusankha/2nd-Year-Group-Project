<?php
require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/JobRequestModel.php';

class JobRequestController {
    private $jobRequestModel;
    
    public function __construct() {
        global $pdo;
        $this->jobRequestModel = new JobRequest($pdo);
    }
    
    /**
     * CREATE - Handle job request creation
     */
    public function create() {
        if (!isLoggedIn()) {
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/post-job');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        
        // Handle photo upload
        $photoPath = null;
        if (isset($_FILES['photos']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/job_photos/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                $fileName = uniqid('job_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['photos']['tmp_name'], $targetPath)) {
                    $photoPath = 'uploads/job_photos/' . $fileName;
                }
            }
        }
        
        // Process provider type checkboxes
        $providerType = 'individual'; // default
        if (isset($_POST['provider_type']) && is_array($_POST['provider_type'])) {
            $selectedTypes = $_POST['provider_type'];
            if (count($selectedTypes) === 2) {
                $providerType = 'both';
            } else {
                $providerType = $selectedTypes[0];
            }
        }

        $data = [
            'user_id' => $userId,
            'category_id' => $_POST['category_id'] ?? null,
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'district' => trim($_POST['district'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'service_provider_type' => $providerType,
            'urgency' => $_POST['urgency'] ?? 'medium',
            'finish_date' => $_POST['finish_date'] ?? null,
            'photos' => $photoPath
        ];
        
        // Validate required fields
        if (empty($data['category_id']) || empty($data['title']) || empty($data['description']) || 
            empty($data['district']) || empty($data['address']) || empty($data['finish_date'])) {
            $_SESSION['error'] = 'All required fields must be filled';
            header('Location: /2nd-Year-Group-Project/FixLanka/post-job');
            exit;
        }
        
        // Validate at least one provider type is selected
        if (empty($providerType)) {
            $_SESSION['error'] = 'Please select at least one service provider type';
            header('Location: /2nd-Year-Group-Project/FixLanka/post-job');
            exit;
        }
        
        $requestId = $this->jobRequestModel->create($data);
        
        if ($requestId) {
            $_SESSION['success'] = 'Job request created successfully!';
            header('Location: /2nd-Year-Group-Project/FixLanka/job-history');
        } else {
            $_SESSION['error'] = 'Failed to create job request';
            header('Location: /2nd-Year-Group-Project/FixLanka/post-job');
        }
        exit;
    }
    
    /**
     * READ - Show all job requests
     */
    public function index() {
        if (!isLoggedIn()) {
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $jobRequests = $this->jobRequestModel->getAllByUser($userId);
        
        require_once __DIR__ . '/../views/user/job_history.php';
    }
    
    /**
     * UPDATE - Handle job request update
     */
    public function update() {
        if (!isLoggedIn()) {
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/job-history');
            exit;
        }
        
        $requestId = $_POST['request_id'] ?? 0;
        $userId = $_SESSION['user_id'];
        
        // Handle photo upload for update
        $photoPath = null;
        if (isset($_FILES['photos']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../uploads/job_photos/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExtension = pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                $fileName = uniqid('job_') . '.' . $fileExtension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['photos']['tmp_name'], $targetPath)) {
                    $photoPath = 'uploads/job_photos/' . $fileName;
                }
            }
        }
        
        $data = [
            'user_id' => $userId,
            'category_id' => $_POST['category_id'] ?? 1,
            'description' => trim($_POST['description'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'service_provider_type' => $_POST['service_provider_type'] ?? 'individual',
            'urgency' => $_POST['urgency'] ?? 'medium',
            'photos' => $photoPath
        ];
        
        if ($this->jobRequestModel->update($requestId, $data)) {
            $_SESSION['success'] = 'Job request updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update job request';
        }
        
        header('Location: /2nd-Year-Group-Project/FixLanka/job-history');
        exit;
    }
    
    /**
     * DELETE - Handle job request deletion
     */
    public function delete() {
        if (!isLoggedIn()) {
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/job-history');
            exit;
        }
        
        $requestId = $_POST['request_id'] ?? 0;
        $userId = $_SESSION['user_id'];
        
        if ($this->jobRequestModel->delete($requestId, $userId)) {
            $_SESSION['success'] = 'Job request deleted successfully!';
        } else {
            $_SESSION['error'] = 'Failed to delete job request';
        }
        
        header('Location: /2nd-Year-Group-Project/FixLanka/job-history');
        exit;
    }
}
?>