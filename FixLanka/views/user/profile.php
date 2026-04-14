<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\profile.php
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/JobRequestModel.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$userData = getUserData();
$dbUser = null;

try {
    global $pdo;
    $stmt = $pdo->prepare('SELECT user_id, f_name, l_name, email, profilePicture, address, district, created_at FROM User WHERE user_id = ? LIMIT 1');
    $stmt->execute([(int)$userData['id']]);
    $dbUser = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (Throwable $e) {
    $dbUser = null;
}

$jobRequests = [];
$totalJobs = 0;
$activeJobs = 0;
$completedJobs = 0;
$pendingJobs = 0;

try {
    global $pdo;
    $jobRequestModel = new JobRequest($pdo);
    $jobRequests = $jobRequestModel->getAllByUser((int)$userData['id']);

    $totalJobs = count($jobRequests);
    foreach ($jobRequests as $job) {
        if (($job['status'] ?? '') === 'completed') {
            $completedJobs++;
        }
        if (($job['status'] ?? '') === 'pending') {
            $pendingJobs++;
        }
        if (in_array(($job['status'] ?? ''), ['in_progress', 'accepted'], true)) {
            $activeJobs++;
        }
    }
} catch (Throwable $e) {
    $jobRequests = [];
}

$displayName = trim((string)(($dbUser['f_name'] ?? '') . ' ' . ($dbUser['l_name'] ?? '')));
if ($displayName === '') {
    $displayName = trim((string)($userData['name'] ?? 'User'));
}

$displayEmail = trim((string)($dbUser['email'] ?? ($userData['email'] ?? '')));
$firstName = trim((string)($dbUser['f_name'] ?? ''));
$lastName = trim((string)($dbUser['l_name'] ?? ''));
$rawAddress = trim((string)($dbUser['address'] ?? ''));
$rawDistrict = trim((string)($dbUser['district'] ?? ''));
$displayLocation = trim($rawAddress !== '' && $rawDistrict !== '' ? ($rawAddress . ', ' . $rawDistrict) : ($rawAddress ?: ($rawDistrict ?: 'Sri Lanka')));
$joinedText = !empty($dbUser['created_at']) ? date('F Y', strtotime($dbUser['created_at'])) : 'N/A';
$profilePicture = trim((string)($dbUser['profilePicture'] ?? ''));

$initial = strtoupper(substr($displayName ?: 'U', 0, 1));
$isEmailValid = filter_var($displayEmail, FILTER_VALIDATE_EMAIL) ? true : false;
$hasLocation = $displayLocation !== '';

$profilePayload = [
    'fullName' => $displayName,
    'firstName' => $firstName,
    'lastName' => $lastName,
    'email' => $displayEmail,
    'address' => $rawAddress,
    'district' => $rawDistrict,
    'location' => $displayLocation,
    'avatar' => $profilePicture,
    'jobStats' => [
        'total' => $totalJobs,
        'active' => $activeJobs,
        'completed' => $completedJobs,
        'pending' => $pendingJobs,
    ],
    'rules' => [
        'hasName' => $displayName !== '',
        'hasValidEmail' => $isEmailValid,
        'hasLocation' => $hasLocation,
        'hasProfilePhoto' => $profilePicture !== '',
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/profile.css">
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-title-section">
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-subtitle">Manage your account information and preferences</p>
                </div>
                <div class="page-actions">
                    <button class="btn-primary" id="editProfileBtn">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Profile Grid -->
            <div class="profile-grid">
                <!-- User Info Card -->
                <div class="profile-card user-info-card">
                    <div class="card-content">
                        <div class="profile-avatar-section">
                            <div class="profile-avatar-container">
                                <img src="https://via.placeholder.com/120" alt="Profile" class="profile-avatar" id="profileAvatar">
                                <div class="avatar-overlay" id="avatarOverlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Change Photo</span>
                                </div>
                            </div>
                            <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                            <div class="profile-status">
                                <span class="status-badge verified">
                                    <i class="fas fa-check-circle"></i>
                                    Verified
                                </span>
                            </div>
                        </div>
                        
                        <div class="user-details">
                            <h2 class="user-full-name"><?php echo htmlspecialchars($displayName ?: 'User'); ?></h2>
                            
                            <div class="contact-info">
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?php echo htmlspecialchars($displayEmail ?: 'N/A'); ?></span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($displayLocation ?: 'N/A'); ?></span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Joined <?php echo htmlspecialchars($joinedText); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Completion Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks"></i>
                            Account Completion
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="completion-stats">
                            <div class="completion-circle">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring-circle-bg" cx="60" cy="60" r="52"></circle>
                                    <circle class="progress-ring-circle" cx="60" cy="60" r="52" id="progressCircle"></circle>
                                </svg>
                                <div class="completion-percentage">
                                    <span class="percentage-value" id="completionPercentage">0</span>
                                    <span class="percentage-symbol">%</span>
                                </div>
                            </div>
                            
                            <div class="completion-tasks" id="completionTasks">
                                <div class="task-item pending">
                                    <i class="far fa-circle"></i>
                                    <span>Checking profile setup...</span>
                                </div>
                            </div>
                            <p class="page-subtitle" id="setupLevelText" style="margin-top:12px;"></p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-content">
                        <div class="action-buttons">
                            <button class="action-btn" id="manageProfileBtn">
                                <div class="action-icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Manage Profile</span>
                                    <span class="action-subtitle">Update information</span>
                                </div>
                            </button>
                            
                            <button class="action-btn" id="viewQuotesBtn">
                                <div class="action-icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">View Quotes</span>
                                    <span class="action-subtitle">Go to quotes received</span>
                                </div>
                            </button>
                            
                            <button class="action-btn" id="postJobBtn">
                                <div class="action-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Post New Job</span>
                                    <span class="action-subtitle">Create a request</span>
                                </div>
                            </button>
                            
                            <button class="action-btn" id="paymentHistoryBtn">
                                <div class="action-icon">
                                    <i class="fas fa-list-check"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Job History</span>
                                    <span class="action-subtitle">View posted jobs</span>
                                </div>
                            </button>

                            <button class="action-btn" id="helpCenterBtn">
                                <div class="action-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="action-text">
                                    <span class="action-title">Help Center</span>
                                    <span class="action-subtitle">Get support</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Job Overview Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-briefcase"></i>
                            Job Overview
                        </h3>
                        <button class="view-all-btn" id="viewAllJobsBtn">View All</button>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon total">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number"><?php echo (int)$totalJobs; ?></span>
                                    <span class="stat-label">Total Jobs</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon active">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number"><?php echo (int)$activeJobs; ?></span>
                                    <span class="stat-label">Active Jobs</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon completed">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number"><?php echo (int)$completedJobs; ?></span>
                                    <span class="stat-label">Completed</span>
                                </div>
                            </div>
                            
                            <div class="stat-item">
                                <div class="stat-icon pending">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div class="stat-details">
                                    <span class="stat-number"><?php echo (int)$pendingJobs; ?></span>
                                    <span class="stat-label">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Setup Tips Card -->
                <div class="profile-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gamepad"></i>
                            Profile Setup Game
                        </h3>
                        <button class="view-all-btn" id="viewAllSetupBtn">Refresh</button>
                    </div>
                    <div class="card-content">
                        <div class="activity-list" id="setupHintsList">
                            <div class="activity-item">
                                <div class="activity-icon job-posted">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="activity-details">
                                    <p class="activity-text">Complete your profile fields to level up your setup score.</p>
                                    <span class="activity-time">Tip</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Edit Profile Modal -->
    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="modal-title">Edit Profile</h3>
                <button class="modal-close" id="closeEditModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="editProfileForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editFirstName">First Name <span class="required">*</span></label>
                            <input type="text" id="editFirstName" class="form-input" value="<?php echo htmlspecialchars($firstName ?: ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="editLastName">Last Name <span class="required">*</span></label>
                            <input type="text" id="editLastName" class="form-input" value="<?php echo htmlspecialchars($lastName ?: ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="editEmail">Email <span class="required">*</span></label>
                            <input type="email" id="editEmail" class="form-input" value="<?php echo htmlspecialchars($displayEmail ?: ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editAddress">Address <span class="required">*</span></label>
                        <input type="text" id="editAddress" class="form-input" value="<?php echo htmlspecialchars($rawAddress ?: ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="editDistrict">District</label>
                        <select id="editDistrict" class="form-input">
                            <option value="" <?php echo $rawDistrict === '' ? 'selected' : ''; ?>>Not selected</option>
                            <option value="Colombo" <?php echo $rawDistrict === 'Colombo' ? 'selected' : ''; ?>>Colombo</option>
                            <option value="Gampaha" <?php echo $rawDistrict === 'Gampaha' ? 'selected' : ''; ?>>Gampaha</option>
                            <option value="Kalutara" <?php echo $rawDistrict === 'Kalutara' ? 'selected' : ''; ?>>Kalutara</option>
                            <option value="Kandy" <?php echo $rawDistrict === 'Kandy' ? 'selected' : ''; ?>>Kandy</option>
                            <option value="Matale" <?php echo $rawDistrict === 'Matale' ? 'selected' : ''; ?>>Matale</option>
                            <option value="Nuwara Eliya" <?php echo $rawDistrict === 'Nuwara Eliya' ? 'selected' : ''; ?>>Nuwara Eliya</option>
                            <option value="Galle" <?php echo $rawDistrict === 'Galle' ? 'selected' : ''; ?>>Galle</option>
                            <option value="Matara" <?php echo $rawDistrict === 'Matara' ? 'selected' : ''; ?>>Matara</option>
                            <option value="Hambantota" <?php echo $rawDistrict === 'Hambantota' ? 'selected' : ''; ?>>Hambantota</option>
                            <option value="Jaffna" <?php echo $rawDistrict === 'Jaffna' ? 'selected' : ''; ?>>Jaffna</option>
                            <option value="Kilinochchi" <?php echo $rawDistrict === 'Kilinochchi' ? 'selected' : ''; ?>>Kilinochchi</option>
                            <option value="Mannar" <?php echo $rawDistrict === 'Mannar' ? 'selected' : ''; ?>>Mannar</option>
                            <option value="Vavuniya" <?php echo $rawDistrict === 'Vavuniya' ? 'selected' : ''; ?>>Vavuniya</option>
                            <option value="Mullaitivu" <?php echo $rawDistrict === 'Mullaitivu' ? 'selected' : ''; ?>>Mullaitivu</option>
                            <option value="Batticaloa" <?php echo $rawDistrict === 'Batticaloa' ? 'selected' : ''; ?>>Batticaloa</option>
                            <option value="Ampara" <?php echo $rawDistrict === 'Ampara' ? 'selected' : ''; ?>>Ampara</option>
                            <option value="Trincomalee" <?php echo $rawDistrict === 'Trincomalee' ? 'selected' : ''; ?>>Trincomalee</option>
                            <option value="Kurunegala" <?php echo $rawDistrict === 'Kurunegala' ? 'selected' : ''; ?>>Kurunegala</option>
                            <option value="Puttalam" <?php echo $rawDistrict === 'Puttalam' ? 'selected' : ''; ?>>Puttalam</option>
                            <option value="Anuradhapura" <?php echo $rawDistrict === 'Anuradhapura' ? 'selected' : ''; ?>>Anuradhapura</option>
                            <option value="Polonnaruwa" <?php echo $rawDistrict === 'Polonnaruwa' ? 'selected' : ''; ?>>Polonnaruwa</option>
                            <option value="Badulla" <?php echo $rawDistrict === 'Badulla' ? 'selected' : ''; ?>>Badulla</option>
                            <option value="Monaragala" <?php echo $rawDistrict === 'Monaragala' ? 'selected' : ''; ?>>Monaragala</option>
                            <option value="Ratnapura" <?php echo $rawDistrict === 'Ratnapura' ? 'selected' : ''; ?>>Ratnapura</option>
                            <option value="Kegalle" <?php echo $rawDistrict === 'Kegalle' ? 'selected' : ''; ?>>Kegalle</option>
                        </select>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" id="cancelEditBtn">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-content">
            <i class="fas fa-check-circle toast-icon"></i>
            <span class="toast-message" id="toastMessage">Success!</span>
        </div>
    </div>

    <script>
        window.profilePageData = <?php echo json_encode($profilePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/profile.js"></script>
</body>
</html>
