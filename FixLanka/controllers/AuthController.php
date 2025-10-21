<?php
require_once __DIR__ . '/../config/databse.php';
require_once __DIR__ . '/../config/session.php';

class AuthController {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function showLoginPage() {
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginPage();
            return;
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT user_id, f_name, l_name, email, password FROM User WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['f_name'] . ' ' . $user['l_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = 'user';
                
                // Redirect to landing page (home) instead of dashboard
                header('Location: /2nd-Year-Group-Project/FixLanka/');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid email or password';
                header('Location: /2nd-Year-Group-Project/FixLanka/login');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Login failed. Please try again.';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        $f_name = trim($_POST['f_name'] ?? '');
        $l_name = trim($_POST['l_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($f_name) || empty($l_name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'All required fields must be filled';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters long';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT user_id FROM User WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already registered';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO User (f_name, l_name, email, password, address) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$f_name, $l_name, $email, $hashedPassword, $address]);
            
            $userId = $this->pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $f_name . ' ' . $l_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            $_SESSION['success'] = 'Account created successfully!';
            
            // Redirect to landing page (home) instead of dashboard
            header('Location: /2nd-Year-Group-Project/FixLanka/');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            error_log("Registration error: " . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
    }
    
    public function logout() {
        session_destroy();
        header('Location: /2nd-Year-Group-Project/FixLanka/');
        exit;
    }
}
?>