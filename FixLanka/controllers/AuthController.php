<?php
require_once __DIR__ . '/../config/database.php';
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
            // Try to find user in User table
            $stmt = $this->pdo->prepare("SELECT user_id, f_name, l_name, email, password FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['f_name'] . ' ' . $user['l_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = 'user';
                
                header('Location: /2nd-Year-Group-Project/FixLanka/');
                exit;
            }
            
            // Try to find user in Admin table (by email or username)
            $stmt = $this->pdo->prepare("SELECT username, email, password FROM Admin WHERE email = ? OR username = ?");
            $stmt->execute([$email, $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['username']; // Using username as ID for admin
                $_SESSION['user_name'] = $admin['username'];
                $_SESSION['user_email'] = $admin['email'];
                $_SESSION['user_role'] = 'admin';
                
                header('Location: /2nd-Year-Group-Project/FixLanka/admin-dashboard');
                exit;
            }
            
            // Try to find user in Moderator table
            $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, password FROM Moderator WHERE email = ?");
            $stmt->execute([$email]);
            $moderator = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($moderator && password_verify($password, $moderator['password'])) {
                $_SESSION['user_id'] = $moderator['moderator_id'];
                $_SESSION['user_name'] = $moderator['username'];
                $_SESSION['user_email'] = $moderator['email'];
                $_SESSION['user_role'] = 'moderator';
                
                header('Location: /2nd-Year-Group-Project/FixLanka/moderator-dashboard');
                exit;
            }
            
            // Try to find user in Company table
            $stmt = $this->pdo->prepare("SELECT company_id, name, email, password FROM company WHERE email = ?");
            $stmt->execute([$email]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($company && password_verify($password, $company['password'])) {
                $_SESSION['user_id'] = $company['company_id'];
                $_SESSION['user_name'] = $company['name'];
                $_SESSION['user_email'] = $company['email'];
                $_SESSION['user_role'] = 'company';
                
                header('Location: /2nd-Year-Group-Project/FixLanka/company-dashboard');
                exit;
            }
            
            // Try to find user in Repairer table
            $stmt = $this->pdo->prepare("SELECT repairer_id, f_name, l_name, email, password FROM repairer WHERE email = ?");
            $stmt->execute([$email]);
            $repairer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($repairer && password_verify($password, $repairer['password'])) {
                $_SESSION['user_id'] = $repairer['repairer_id'];
                $_SESSION['user_name'] = $repairer['f_name'] . ' ' . $repairer['l_name'];
                $_SESSION['user_email'] = $repairer['email'];
                $_SESSION['user_role'] = 'repairer';
                
                header('Location: /2nd-Year-Group-Project/FixLanka/repairer-welcome');
                exit;
            }
            
            // If no match found in any table
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Login failed. Please try again.';
            error_log("Login error: " . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        // Detect user type and route to appropriate registration method
        $userType = $_POST['user_type'] ?? 'user';
        
        switch ($userType) {
            case 'repairer':
                $this->registerRepairer();
                break;
            case 'company':
                $this->registerCompany();
                break;
            case 'user':
            default:
                $this->registerUser();
                break;
        }
    }
    
    private function registerUser() {
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
            $stmt = $this->pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already registered';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO user (f_name, l_name, email, password, address) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$f_name, $l_name, $email, $hashedPassword, $address]);
            
            $userId = $this->pdo->lastInsertId();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $f_name . ' ' . $l_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'user';
            $_SESSION['success'] = 'Account created successfully!';
            
            header('Location: /2nd-Year-Group-Project/FixLanka/');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            error_log("Registration error: " . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
    }
    
    private function registerRepairer() {
        $f_name = trim($_POST['f_name'] ?? '');
        $l_name = trim($_POST['l_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phoneNumber = trim($_POST['phoneNumber'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $category_id = $_POST['category_id'] ?? '';
        $districts = $_POST['districts'] ?? [];
        $about = trim($_POST['about'] ?? '');
        
        // Validation
        if (empty($f_name) || empty($l_name) || empty($email) || empty($password) || 
            empty($phoneNumber) || empty($category_id) || empty($about)) {
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
        
        if (empty($districts) || !is_array($districts)) {
            $_SESSION['error'] = 'Please select at least one service district';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        try {
            // Check email uniqueness
            $stmt = $this->pdo->prepare("SELECT repairer_id FROM repairer WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already registered';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            // Handle file upload
            $profilePicture = null;
            if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
                $profilePicture = $this->handleFileUpload($_FILES['profilePicture'], 'repairers');
                if ($profilePicture === false) {
                    $_SESSION['error'] = 'Failed to upload profile picture';
                    header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                    exit;
                }
            }
            
            // Convert districts array to CSV
            $districtsCSV = implode(',', $districts);
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into repairer table
            $stmt = $this->pdo->prepare("
                INSERT INTO repairer (f_name, l_name, email, password, phoneNumber, about, profilePicture, districts, category_id, availability) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')
            ");
            $stmt->execute([$f_name, $l_name, $email, $hashedPassword, $phoneNumber, $about, $profilePicture, $districtsCSV, $category_id]);
            
            $repairerId = $this->pdo->lastInsertId();
            $_SESSION['user_id'] = $repairerId;
            $_SESSION['user_name'] = $f_name . ' ' . $l_name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'repairer';
            $_SESSION['success'] = 'Repairer account created successfully!';
            
            header('Location: /2nd-Year-Group-Project/FixLanka/repairer-welcome');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            error_log("Repairer registration error: " . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
    }
    
    private function registerCompany() {
        $name = trim($_POST['name'] ?? '');
        $business_type = $_POST['business_type'] ?? [];
        $registration_no = trim($_POST['registration_no'] ?? '');
        $tax_id = trim($_POST['tax_id'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $contact_no = trim($_POST['contact_no'] ?? '');
        $districts = $_POST['districts'] ?? [];
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $description = trim($_POST['description'] ?? '');
        
        // Validation
        if (empty($name) || empty($registration_no) || empty($address) || empty($email) || 
            empty($password) || empty($contact_no) || empty($description)) {
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
        
        if (empty($business_type) || !is_array($business_type)) {
            $_SESSION['error'] = 'Please select at least one business type';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        if (empty($districts) || !is_array($districts)) {
            $_SESSION['error'] = 'Please select at least one service district';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        try {
            // Check email uniqueness
            $stmt = $this->pdo->prepare("SELECT company_id FROM company WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already registered';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            // Check registration number uniqueness
            $stmt = $this->pdo->prepare("SELECT company_id FROM company WHERE registration_no = ?");
            $stmt->execute([$registration_no]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Registration number already exists';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            // Convert arrays to CSV
            $businessTypeCSV = implode(',', $business_type);
            $districtsCSV = implode(',', $districts);
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into company table
            $stmt = $this->pdo->prepare("
                INSERT INTO company (name, business_type, registration_no, tax_id, address, email, website, contact_no, districts, password, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $businessTypeCSV, $registration_no, $tax_id, $address, $email, $website, $contact_no, $districtsCSV, $hashedPassword, $description]);
            
            $companyId = $this->pdo->lastInsertId();
            $_SESSION['user_id'] = $companyId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'company';
            $_SESSION['success'] = 'Company account created successfully!';
            
            header('Location: /2nd-Year-Group-Project/FixLanka/company-dashboard');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Registration failed. Please try again.';
            error_log("Company registration error: " . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
    }
    
    private function handleFileUpload($file, $userType) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        // Validate file size (5MB max)
        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../assets/uploads/profiles/' . $userType . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Return relative path for database storage
            return '/2nd-Year-Group-Project/FixLanka/assets/uploads/profiles/' . $userType . '/' . $filename;
        }
        
        return false;
    }
    
    public function logout() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to landing page
        header('Location: /2nd-Year-Group-Project/FixLanka/');
        exit;
    }
}
?>