<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\config\session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('getValidRoles')) {
    function getValidRoles() {
        return ['user', 'admin', 'moderator', 'company', 'repairer'];
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false;
        }

        $role = $_SESSION['user_role'] ?? null;
        if (!is_string($role) || $role === '') {
            return false;
        }

        return in_array($role, getValidRoles(), true);
    }
}

if (!function_exists('hasRole')) {
    function hasRole($roles) {
        if (!isLoggedIn()) {
            return false;
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $currentRole = $_SESSION['user_role'] ?? null;
        return in_array($currentRole, $roles, true);
    }
}

if (!function_exists('getUserData')) {
    function getUserData() {
        if (isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? 'User',
                'email' => $_SESSION['user_email'] ?? '',
                'role' => $_SESSION['user_role'] ?? 'user'
            ];
        }
        return null;
    }
}

if (!function_exists('requireRole')) {
    function requireRole($allowedRoles) {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Please login to access this page';
            header('Location: /2nd-Year-Group-Project/FixLanka/login');
            exit;
        }
        
        $currentRole = $_SESSION['user_role'] ?? 'user';
        
        // Convert single role to array for consistent checking
        if (!is_array($allowedRoles)) {
            $allowedRoles = [$allowedRoles];
        }
        
        if (!in_array($currentRole, $allowedRoles)) {
            $_SESSION['error'] = 'You do not have permission to access this page';
            header('Location: ' . getRoleHomePath($currentRole));
            exit;
        }
    }
}

if (!function_exists('getRoleHomePath')) {
    function getRoleHomePath($role = null) {
        $resolvedRole = is_string($role) && $role !== '' ? $role : ($_SESSION['user_role'] ?? null);

        switch ($resolvedRole) {
            case 'admin':
                return '/2nd-Year-Group-Project/FixLanka/admin-dashboard';
            case 'moderator':
                return '/2nd-Year-Group-Project/FixLanka/moderator-dashboard';
            case 'company':
                return '/2nd-Year-Group-Project/FixLanka/company-dashboard';
            case 'repairer':
                return '/2nd-Year-Group-Project/FixLanka/repairer-welcome';
            case 'user':
            default:
                return '/2nd-Year-Group-Project/FixLanka/';
        }
    }
}

if (!function_exists('redirectToRoleHome')) {
    function redirectToRoleHome($role = null) {
        header('Location: ' . getRoleHomePath($role));
        exit;
    }
}

if (!function_exists('getUserRole')) {
    function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
}

/**
 * Track user session in database for security monitoring
 */
if (!function_exists('trackUserSession')) {
    function trackUserSession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            return false;
        }
        
        try {
            // Create database connection
            $pdo = new PDO(
                "mysql:host=localhost;dbname=fix_lanka;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $sessionId = session_id();
            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['user_role'];
            
            // Parse user agent to get device and browser info
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
            
            // Simple device detection
            $deviceType = 'Desktop';
            if (preg_match('/mobile|android|iphone|ipod/i', $userAgent)) {
                $deviceType = 'Mobile';
            } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
                $deviceType = 'Tablet';
            }
            
            // Simple browser detection
            $browser = 'Unknown';
            if (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edg/i', $userAgent)) {
                $browser = 'Chrome';
            } elseif (preg_match('/Firefox/i', $userAgent)) {
                $browser = 'Firefox';
            } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
                $browser = 'Safari';
            } elseif (preg_match('/Edg/i', $userAgent)) {
                $browser = 'Edge';
            } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
                $browser = 'Internet Explorer';
            } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
                $browser = 'Opera';
            }
            
            // Simple OS detection
            $os = 'Unknown';
            if (preg_match('/Windows NT 10/i', $userAgent)) {
                $os = 'Windows 10/11';
            } elseif (preg_match('/Windows NT/i', $userAgent)) {
                $os = 'Windows';
            } elseif (preg_match('/Mac OS X/i', $userAgent)) {
                $os = 'macOS';
            } elseif (preg_match('/Linux/i', $userAgent)) {
                $os = 'Linux';
            } elseif (preg_match('/Android/i', $userAgent)) {
                $os = 'Android';
            } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
                $os = 'iOS';
            }
            
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            
            // Upsert session record
            $sql = "INSERT INTO user_sessions 
                    (session_id, user_id, user_role, device_type, browser, os, ip_address, user_agent, is_current)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)
                    ON DUPLICATE KEY UPDATE 
                        last_activity = CURRENT_TIMESTAMP,
                        is_current = 1,
                        device_type = VALUES(device_type),
                        browser = VALUES(browser),
                        os = VALUES(os),
                        ip_address = VALUES(ip_address)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $sessionId, 
                $userId, 
                $userRole, 
                $deviceType, 
                $browser, 
                $os, 
                $ipAddress, 
                $userAgent
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Session tracking error: " . $e->getMessage());
            return false;
        }
    }
}

/**
 * Clean up old inactive sessions (call this periodically)
 */
if (!function_exists('cleanupOldSessions')) {
    function cleanupOldSessions($daysOld = 30) {
        try {
            // Create database connection
            $pdo = new PDO(
                "mysql:host=localhost;dbname=fix_lanka;charset=utf8mb4",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $sql = "DELETE FROM user_sessions 
                    WHERE last_activity < DATE_SUB(NOW(), INTERVAL ? DAY)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$daysOld]);
            return true;
        } catch (PDOException $e) {
            error_log("Session cleanup error: " . $e->getMessage());
            return false;
        }
    }
}

// Track session on every page load
if (isLoggedIn()) {
    trackUserSession();
}
?>