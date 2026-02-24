<?php
/**
 * StaticContentController.php
 * ENTRY POINT - Handles routing, business logic, and loads view
 * Pure MVC: Controller → Model → View
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/StaticContentModel.php';

class StaticContentController {
    private $model;
    private $db;
    
    public function __construct() {
        $this->db = new mysqli("localhost", "root", "", "fix_lanka");
        
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
        
        $this->db->set_charset("utf8");
        $this->model = new StaticContentModel($this->db);
    }
    
    /**
     * Main routing method
     */
    public function handleRequest() {
        // Handle GET actions (Publish/Unpublish)
        if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
            $this->toggleStatus();
            return;
        }
        
        // Handle POST actions (Update)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'update') {
                $this->updateContent();
                return;
            }
        }
        
        // Default: Display the view
        $this->index();
    }
    
    /**
     * Display view with data
     */
    private function index() {
        // Fetch data from model
        $contents = $this->model->getAllContent();
        $stats = $this->model->getStatistics();
        
        // Get messages
        $message = $_SESSION['message'] ?? '';
        $message_type = $_SESSION['message_type'] ?? '';
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        // Load view template
        require_once __DIR__ . '/../views/moderator/static-content-view.php';
    }
    
    /**
     * Update content
     */
    private function updateContent() {
        $id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $body = isset($_POST['body']) ? trim($_POST['body']) : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'Draft';
        
        if ($id <= 0 || empty($title) || empty($body)) {
            $_SESSION['message'] = 'Invalid data provided';
            $_SESSION['message_type'] = 'error';
            $this->redirect();
            return;
        }
        
        $validStatuses = ['Draft', 'Published'];
        if (!in_array($status, $validStatuses)) {
            $status = 'Draft';
        }
        
        $success = $this->model->updateContent($id, $title, $description, $body, $status);
        
        $_SESSION['message'] = $success ? 'Content updated successfully!' : 'Failed to update content';
        $_SESSION['message_type'] = $success ? 'success' : 'error';
        
        $this->redirect();
    }
    
    /**
     * Toggle content status (Publish/Unpublish)
     */
    private function toggleStatus() {
        if (!isset($_GET['id'])) {
            $_SESSION['message'] = 'Invalid request';
            $_SESSION['message_type'] = 'error';
            $this->redirect();
            return;
        }
        
        $id = intval($_GET['id']);
        $content = $this->model->getContentById($id);
        
        if (!$content) {
            $_SESSION['message'] = 'Content not found';
            $_SESSION['message_type'] = 'error';
            $this->redirect();
            return;
        }
        
        if ($content['status'] === 'Published') {
            $success = $this->model->unpublishContent($id);
            $_SESSION['message'] = $success ? 'Content unpublished successfully!' : 'Failed to unpublish content';
        } else {
            $success = $this->model->publishContent($id);
            $_SESSION['message'] = $success ? 'Content published successfully!' : 'Failed to publish content';
        }
        
        $_SESSION['message_type'] = $success ? 'success' : 'error';
        $this->redirect();
    }
    
    /**
     * Redirect back to controller
     */
    private function redirect() {
        header('Location: /2nd-Year-Group-Project/FixLanka/controllers/StaticContentController.php');
        exit;
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }
}

// ============================================
// EXECUTE CONTROLLER (Entry Point)
// ============================================
$controller = new StaticContentController();
$controller->handleRequest();
?>