<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

class AuthController {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    private function establishAuthenticatedSession($id, $name, $email, $role) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);

        unset(
            $_SESSION['user_id'],
            $_SESSION['user_name'],
            $_SESSION['user_email'],
            $_SESSION['user_role'],
            $_SESSION['company_id'],
            $_SESSION['repairer_id'],
            $_SESSION['admin_id'],
            $_SESSION['moderator_id']
        );

        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = $role;

        if ($role === 'company') {
            $_SESSION['company_id'] = (int)$id;
        } elseif ($role === 'repairer') {
            $_SESSION['repairer_id'] = (int)$id;
        } elseif ($role === 'admin') {
            $_SESSION['admin_id'] = $id;
        } elseif ($role === 'moderator') {
            $_SESSION['moderator_id'] = (int)$id;
        }
    }

    private function columnExists($table, $column) {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1'
            );
            $stmt->execute([$table, $column]);
            return (bool) $stmt->fetchColumn();
        } catch (Exception $e) {
            return false;
        }
    }

    private function normalizeCategoryName($name) {
        $name = trim((string)$name);
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }

    private function findOrCreateCategoryIdByName($rawName) {
        $name = $this->normalizeCategoryName($rawName);
        if ($name === '') {
            throw new InvalidArgumentException('Category name is required');
        }

        // DB column is VARCHAR(100)
        if (function_exists('mb_strlen') && mb_strlen($name) > 100) {
            $name = mb_substr($name, 0, 100);
        } elseif (strlen($name) > 100) {
            $name = substr($name, 0, 100);
        }

        $stmt = $this->pdo->prepare('SELECT category_id FROM category WHERE LOWER(name) = LOWER(?) LIMIT 1');
        $stmt->execute([$name]);
        $row = $stmt->fetch();
        if ($row && isset($row['category_id'])) {
            return (int)$row['category_id'];
        }

        try {
            $insert = $this->pdo->prepare('INSERT INTO category (name) VALUES (?)');
            $insert->execute([$name]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            // If another request inserted the same category concurrently, re-fetch.
            $stmt = $this->pdo->prepare('SELECT category_id FROM category WHERE LOWER(name) = LOWER(?) LIMIT 1');
            $stmt->execute([$name]);
            $row = $stmt->fetch();
            if ($row && isset($row['category_id'])) {
                return (int)$row['category_id'];
            }
            throw $e;
        }
    }
    
    public function showLoginPage() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function showForgotPasswordPage() {
        require_once __DIR__ . '/../views/auth/forgot-password.php';
    }

    /**
     * Forgot password (temporary): resets password using only email + new password.
     * No OTP / token verification is performed.
     */
    public function resetPasswordWithoutVerification() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showForgotPasswordPage();
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';

        if (empty($email) || empty($newPassword)) {
            $_SESSION['error'] = 'Email and new password are required';
            $_SESSION['prefill_email'] = $email;
            header('Location: /2nd-Year-Group-Project/FixLanka/forgot-password');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            $_SESSION['prefill_email'] = $email;
            header('Location: /2nd-Year-Group-Project/FixLanka/forgot-password');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters long';
            $_SESSION['prefill_email'] = $email;
            header('Location: /2nd-Year-Group-Project/FixLanka/forgot-password');
            exit;
        }

        try {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $updated = false;

            // NOTE: we intentionally update the first matching account.
            $updated = $updated || $this->updatePasswordByEmail('user', $email, $newHash);
            $updated = $updated || $this->updatePasswordByEmail('Admin', $email, $newHash);
            $updated = $updated || $this->updatePasswordByEmail('Moderator', $email, $newHash);
            $updated = $updated || $this->updatePasswordByEmail('company', $email, $newHash);
            $updated = $updated || $this->updatePasswordByEmail('repairer', $email, $newHash);

            if (!$updated) {
                $_SESSION['error'] = 'No account found with that email';
                $_SESSION['prefill_email'] = $email;
                header('Location: /2nd-Year-Group-Project/FixLanka/forgot-password');
                exit;
            }

            $_SESSION['success'] = 'Password updated successfully. Please login.';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Failed to update password. Please try again.';
            $_SESSION['prefill_email'] = $email;
            error_log('Forgot password error: ' . $e->getMessage());
            header('Location: /2nd-Year-Group-Project/FixLanka/forgot-password');
            exit;
        }
    }

    private function updatePasswordByEmail($table, $email, $hash) {
        // Table is whitelisted by our own calls above.
        $sql = "UPDATE {$table} SET password = ? WHERE email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$hash, $email]);
        return $stmt->rowCount() > 0;
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
                $this->establishAuthenticatedSession(
                    (int)$user['user_id'],
                    $user['f_name'] . ' ' . $user['l_name'],
                    (string)$user['email'],
                    'user'
                );

                if (class_exists('AuditLogger')) {
                    AuditLogger::log(
                        $this->pdo,
                        'auth.login_success',
                        ['role' => 'user'],
                        'user',
                        (string)$user['user_id'],
                        200,
                        (int)$user['user_id'],
                        'user'
                    );
                }
                
                header('Location: /2nd-Year-Group-Project/FixLanka/');
                exit;
            }
            
            // Try to find user in Admin table
            $stmt = $this->pdo->prepare("SELECT username, email, password FROM Admin WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                $this->establishAuthenticatedSession(
                    (string)$admin['username'],
                    (string)$admin['username'],
                    (string)$admin['email'],
                    'admin'
                );

                if (class_exists('AuditLogger')) {
                    AuditLogger::log(
                        $this->pdo,
                        'auth.login_success',
                        ['role' => 'admin'],
                        'admin',
                        (string)$admin['username'],
                        200,
                        null,
                        'admin'
                    );
                }
                
                header('Location: /2nd-Year-Group-Project/FixLanka/admin-dashboard');
                exit;
            }
            
            // Try to find user in Moderator table
            $stmt = $this->pdo->prepare("SELECT moderator_id, username, email, password FROM Moderator WHERE email = ?");
            $stmt->execute([$email]);
            $moderator = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($moderator && password_verify($password, $moderator['password'])) {
                $this->establishAuthenticatedSession(
                    (int)$moderator['moderator_id'],
                    (string)$moderator['username'],
                    (string)$moderator['email'],
                    'moderator'
                );

                if (class_exists('AuditLogger')) {
                    AuditLogger::log(
                        $this->pdo,
                        'auth.login_success',
                        ['role' => 'moderator'],
                        'moderator',
                        (string)$moderator['moderator_id'],
                        200,
                        (int)$moderator['moderator_id'],
                        'moderator'
                    );
                }
                
                header('Location: /2nd-Year-Group-Project/FixLanka/moderator-dashboard');
                exit;
            }
            
            // Try to find user in Company table
            $stmt = $this->pdo->prepare("SELECT company_id, name, email, password FROM company WHERE email = ?");
            $stmt->execute([$email]);
            $company = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($company && password_verify($password, $company['password'])) {
                $this->establishAuthenticatedSession(
                    (int)$company['company_id'],
                    (string)$company['name'],
                    (string)$company['email'],
                    'company'
                );

                if (class_exists('AuditLogger')) {
                    AuditLogger::log(
                        $this->pdo,
                        'auth.login_success',
                        ['role' => 'company'],
                        'company',
                        (string)$company['company_id'],
                        200,
                        (int)$company['company_id'],
                        'company'
                    );
                }
                
                header('Location: /2nd-Year-Group-Project/FixLanka/company-dashboard');
                exit;
            }
            
            // Try to find user in Repairer table
            $stmt = $this->pdo->prepare("SELECT repairer_id, f_name, l_name, email, password FROM repairer WHERE email = ?");
            $stmt->execute([$email]);
            $repairer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($repairer && password_verify($password, $repairer['password'])) {
                $this->establishAuthenticatedSession(
                    (int)$repairer['repairer_id'],
                    $repairer['f_name'] . ' ' . $repairer['l_name'],
                    (string)$repairer['email'],
                    'repairer'
                );

                if (class_exists('AuditLogger')) {
                    AuditLogger::log(
                        $this->pdo,
                        'auth.login_success',
                        ['role' => 'repairer'],
                        'repairer',
                        (string)$repairer['repairer_id'],
                        200,
                        (int)$repairer['repairer_id'],
                        'repairer'
                    );
                }
                
                header('Location: /2nd-Year-Group-Project/FixLanka/repairer-welcome');
                exit;
            }
            
            // If no match found in any table
            if (class_exists('AuditLogger')) {
                AuditLogger::log(
                    $this->pdo,
                    'auth.login_failed',
                    ['email_hash' => hash('sha256', strtolower($email))],
                    null,
                    null,
                    401,
                    null,
                    null
                );
            }
            $_SESSION['error'] = 'Invalid email or password';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
            
        } catch (PDOException $e) {
            if (class_exists('AuditLogger')) {
                AuditLogger::log(
                    $this->pdo,
                    'auth.login_error',
                    ['email_hash' => hash('sha256', strtolower($email))],
                    null,
                    null,
                    500,
                    null,
                    null
                );
            }
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
            $this->establishAuthenticatedSession(
                (int)$userId,
                $f_name . ' ' . $l_name,
                (string)$email,
                'user'
            );
            $_SESSION['success'] = 'Account created successfully!';

            if (class_exists('AuditLogger')) {
                AuditLogger::log(
                    $this->pdo,
                    'auth.register',
                    ['role' => 'user'],
                    'user',
                    (string)$userId,
                    201,
                    (int)$userId,
                    'user'
                );
            }
            
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
        $category_custom = trim($_POST['category_custom'] ?? '');
        $districts = $_POST['districts'] ?? [];
        $about = trim($_POST['about'] ?? '');
        $experienceInitialYears = intval($_POST['experience_initial_years'] ?? 0);
        
        // Validation
        if (empty($f_name) || empty($l_name) || empty($email) || empty($password) || 
            empty($phoneNumber) || empty($about)) {
            $_SESSION['error'] = 'All required fields must be filled';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }

        // Resolve category: existing selection or custom name ("Other")
        if ($category_id === 'other' || ($category_id === '' && $category_custom !== '')) {
            if ($category_custom === '') {
                $_SESSION['error'] = 'Please enter your service category';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
        } else {
            if ($category_id === '' || !ctype_digit((string)$category_id) || (int)$category_id <= 0) {
                $_SESSION['error'] = 'Please select a valid service category';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
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

        if ($experienceInitialYears < 0 || $experienceInitialYears > 50) {
            $_SESSION['error'] = 'Please enter a valid initial experience (0 to 50 years)';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        try {
            // Determine final category_id
            if ($category_id === 'other' || ($category_id === '' && $category_custom !== '')) {
                $category_id = $this->findOrCreateCategoryIdByName($category_custom);
            } else {
                $category_id = (int)$category_id;
                $catCheck = $this->pdo->prepare('SELECT category_id FROM category WHERE category_id = ?');
                $catCheck->execute([$category_id]);
                if (!$catCheck->fetch()) {
                    $_SESSION['error'] = 'Please select a valid service category';
                    header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                    exit;
                }
            }

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
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $profilePicture = $this->handleFileUpload($_FILES['profile_picture'], 'repairers');
                if ($profilePicture === false) {
                    $_SESSION['error'] = 'Failed to upload profile picture';
                    header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                    exit;
                }
            }

            // Convert districts array to comma-separated string for storage in repairer.districts column
            $districtsText = is_array($districts) ? implode(',', $districts) : '';
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into repairer table with column fallbacks (older DBs may use camelCase)
            $profileCol = $this->columnExists('repairer', 'profile_picture') ? 'profile_picture' : ($this->columnExists('repairer', 'profilePicture') ? 'profilePicture' : null);
            $experienceYearsCol = $this->columnExists('repairer', 'experience_years') ? 'experience_years' : ($this->columnExists('repairer', 'experienceYears') ? 'experienceYears' : null);
            $experienceInitialCol = $this->columnExists('repairer', 'experience_initial_years') ? 'experience_initial_years' : ($this->columnExists('repairer', 'experienceInitialYears') ? 'experienceInitialYears' : null);

            $columns = ['f_name', 'l_name', 'email', 'password', 'phoneNumber', 'about', 'category_id', 'districts', 'availability'];
            $values = [$f_name, $l_name, $email, $hashedPassword, $phoneNumber, $about, $category_id, $districtsText, 'available'];

            if ($profileCol) {
                $columns[] = $profileCol;
                $values[] = $profilePicture;
            }
            if ($experienceInitialCol) {
                $columns[] = $experienceInitialCol;
                $values[] = $experienceInitialYears;
            }
            if ($experienceYearsCol) {
                $columns[] = $experienceYearsCol;
                $values[] = $experienceInitialYears;
            }

            $placeholders = implode(',', array_fill(0, count($columns), '?'));
            $sql = 'INSERT INTO repairer (' . implode(',', $columns) . ') VALUES (' . $placeholders . ')';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            
            $repairerId = $this->pdo->lastInsertId();

            $this->establishAuthenticatedSession(
                (int)$repairerId,
                $f_name . ' ' . $l_name,
                (string)$email,
                'repairer'
            );
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
        $business_type_other = trim($_POST['business_type_other'] ?? '');
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

        // If "Other" is selected, require and store the typed business type.
        if (in_array('Other', $business_type, true)) {
            if ($business_type_other === '') {
                $_SESSION['error'] = 'Please enter your business type';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            $business_type = array_values(array_filter($business_type, function ($t) {
                return $t !== 'Other';
            }));
            $business_type[] = $business_type_other;
        }
        
        if (empty($districts) || !is_array($districts)) {
            $_SESSION['error'] = 'Please select at least one service district';
            header('Location: /2nd-Year-Group-Project/FixLanka/signup');
            exit;
        }
        
        try {
            // Check email uniqueness
            $stmt = $this->pdo->prepare("SELECT company_id FROM Company WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email already registered';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            // Check registration number uniqueness
            $stmt = $this->pdo->prepare("SELECT company_id FROM Company WHERE registration_no = ?");
            $stmt->execute([$registration_no]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Registration number already exists';
                header('Location: /2nd-Year-Group-Project/FixLanka/signup');
                exit;
            }
            
            // Convert arrays to CSV (Business type only)
            $businessTypeCSV = implode(',', $business_type);
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // 1. Insert into Location table
            $primaryDistrict = !empty($districts) ? $districts[0] : null;
            $stmt = $this->pdo->prepare("INSERT INTO location (address, district) VALUES (?, ?)");
            $stmt->execute([$address, $primaryDistrict]);
            $locationId = $this->pdo->lastInsertId();

            // 2. Insert into Company table
            $stmt = $this->pdo->prepare("
                INSERT INTO Company (name, business_type, registration_no, tax_id, location_id, email, website, contact_no, password, description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $businessTypeCSV, $registration_no, $tax_id, $locationId, $email, $website, $contact_no, $hashedPassword, $description]);
            
            $companyId = $this->pdo->lastInsertId();

            // 3. Insert service areas
            if (!empty($districts)) {
                $areaStmt = $this->pdo->prepare("INSERT INTO service_area (owner_id, owner_type, district) VALUES (?, 'company', ?)");
                foreach ($districts as $district) {
                    $areaStmt->execute([$companyId, $district]);
                }
            }
            $this->establishAuthenticatedSession(
                (int)$companyId,
                (string)$name,
                (string)$email,
                'company'
            );
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

        if (class_exists('AuditLogger')) {
            AuditLogger::log(
                $this->pdo,
                'auth.logout',
                ['role' => $_SESSION['user_role'] ?? null],
                null,
                null,
                200,
                $_SESSION['user_id'] ?? null,
                $_SESSION['user_role'] ?? null
            );
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