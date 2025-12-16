<?php
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
    
    public function index() {
        $contents = $this->model->getAllContent();
        $stats = $this->model->getStatistics();
        
        $data = [
            'contents' => $contents,
            'stats' => $stats,
            'message' => isset($_SESSION['message']) ? $_SESSION['message'] : '',
            'message_type' => isset($_SESSION['message_type']) ? $_SESSION['message_type'] : ''
        ];
        
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        $this->loadView('moderator/static-content', $data);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content');
            exit;
        }
        
        $id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $body = isset($_POST['body']) ? trim($_POST['body']) : '';
        $status = isset($_POST['status']) ? $_POST['status'] : 'Draft';
        
        if ($id <= 0 || empty($title) || empty($body)) {
            $_SESSION['message'] = 'Invalid data provided';
            $_SESSION['message_type'] = 'error';
            header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content');
            exit;
        }
        
        $validStatuses = ['Draft', 'Published'];
        if (!in_array($status, $validStatuses)) {
            $status = 'Draft';
        }
        
        $success = $this->model->updateContent($id, $title, $description, $body, $status);
        
        if ($success) {
            $_SESSION['message'] = 'Content updated successfully';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update content';
            $_SESSION['message_type'] = 'error';
        }
        
        header('Location: /2nd-Year-Group-Project/FixLanka/moderator-static-content');
        exit;
    }
    
    private function loadView($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/' . $view . '.php';
    }
}

$controller = new StaticContentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $controller->update();
} else {
    $controller->index();
}
?>