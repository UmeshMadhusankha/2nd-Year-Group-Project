<?php
// Page configuration
$currentPage = 'profile';
$pageTitle = 'My Profile';
$pageSubtitle = 'Manage your personal information and settings';
$searchPlaceholder = 'Search requests, repairers, projects...';

// Get user ID from session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

// Load service categories for the category dropdown (DB-driven)
$serviceCategories = [];
try {
    require_once __DIR__ . '/../../../config/database.php';
    if (isset($pdo)) {
        $stmt = $pdo->query('SELECT category_id, name FROM category ORDER BY name');
        $serviceCategories = $stmt->fetchAll();
    }
} catch (Exception $e) {
    // Non-fatal: dropdown will still render the placeholder.
    error_log('Failed to load service categories for repairer profile: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/profile.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content-wrapper">
            <div class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header">
                        <div class="page-header-content">
                            <div class="page-header-text">
                                <h2 class="page-title">My Profile</h2>
                                <p class="page-description">Update your personal information and preferences</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn btn-primary" id="edit-profile-btn">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit Profile</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Profile Content -->
                    <section class="profile-section">
                        <div class="profile-container">
                            <!-- Left Column - Profile Photo & Quick Info -->
                            <div class="profile-left-column">
                                <div class="profile-photo-section">
                                    <div class="profile-photo-container">
                                        <img src="/2nd-Year-Group-Project/FixLanka/assets/images/user.png" alt="Profile Photo" class="profile-photo" id="profile-photo">
                                        <div class="profile-photo-overlay">
                                            <button class="photo-upload-btn" id="photo-upload-btn">
                                                <i class="fas fa-camera"></i>
                                                <span>Change Photo</span>
                                            </button>
                                        </div>
                                        <input type="file" id="photo-upload-input" accept="image/*" style="display: none;">
                                    </div>
                                    <div class="profile-photo-info">
                                        <h3 class="profile-name" id="profileDisplayName">—</h3>
                                        <p class="profile-role">Repair Specialist</p>
                                        <div class="profile-rating">
                                            <div class="stars" id="profileRatingStars">
                                            </div>
                                            <span class="rating-text" id="profileRatingText">—</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Info Cards -->
                                <div class="quick-info-section">
                                    <h4 class="quick-info-title">Quick Info</h4>
                                    <div class="quick-info-cards">
                                        <div class="info-card">
                                            <i class="fas fa-calendar-check"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Member Since</span>
                                                <span class="info-card-value" id="profileMemberSince">—</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-tools"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Jobs Completed</span>
                                                <span class="info-card-value" id="profileJobsCompleted">—</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-medal"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Success Rate</span>
                                                <span class="info-card-value" id="profileSuccessRate">—</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-clock"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Response Time</span>
                                                <span class="info-card-value" id="profileResponseTime">—</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Form Fields -->
                            <div class="profile-right-column">
                                <form class="profile-form" id="profile-form">
                                    <div class="form-section">
                                        <h4 class="form-section-title">Personal Information</h4>
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label for="full-name" class="form-label">Full Name</label>
                                                <input type="text" id="full-name" name="full-name" class="form-input" value="" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" id="email" name="email" class="form-input" value="" readonly required pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" title="Please enter a valid email address">
                                            </div>
                                            <div class="form-group">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" id="phone" name="phone" class="form-input" value="" readonly required pattern="[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}" title="Please enter a valid phone number (e.g., 0771234567 or +94771234567)">
                                            </div>
                                            <div class="form-group">
                                                <label for="service-category" class="form-label">Service Category</label>
                                                <select id="service-category" name="service-category" class="form-select" disabled>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($serviceCategories as $cat): ?>
                                                        <option value="<?php echo htmlspecialchars((string)$cat['category_id']); ?>">
                                                            <?php echo htmlspecialchars((string)$cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="service-category-name" class="form-label">Service Category (Current)</label>
                                                <input type="text" id="service-category-name" class="form-input" value="" readonly>
                                            </div>

                                            <div class="form-group form-group-full">
                                                <label class="form-label">Skills (Tags)</label>
                                                <div class="skills-tag-input">
                                                    <div class="skill-tags" id="skillsTags"></div>
                                                    <input type="text" id="skillsInput" class="form-input" placeholder="Type a skill and press Enter" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group form-group-full">
                                                <label class="form-label">Working Districts</label>
                                                <div class="checkbox-grid" id="working-districts">
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Ampara" disabled>
                                                        <span>Ampara</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Anuradhapura" disabled>
                                                        <span>Anuradhapura</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Badulla" disabled>
                                                        <span>Badulla</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Batticaloa" disabled>
                                                        <span>Batticaloa</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Colombo" checked disabled>
                                                        <span>Colombo</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Galle" disabled>
                                                        <span>Galle</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Gampaha" checked disabled>
                                                        <span>Gampaha</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Hambantota" disabled>
                                                        <span>Hambantota</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Jaffna" disabled>
                                                        <span>Jaffna</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Kalutara" checked disabled>
                                                        <span>Kalutara</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Kandy" disabled>
                                                        <span>Kandy</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Kegalle" disabled>
                                                        <span>Kegalle</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Kilinochchi" disabled>
                                                        <span>Kilinochchi</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Kurunegala" disabled>
                                                        <span>Kurunegala</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Mannar" disabled>
                                                        <span>Mannar</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Matale" disabled>
                                                        <span>Matale</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Matara" disabled>
                                                        <span>Matara</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Monaragala" disabled>
                                                        <span>Monaragala</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Mullaitivu" disabled>
                                                        <span>Mullaitivu</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Nuwara Eliya" disabled>
                                                        <span>Nuwara Eliya</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Polonnaruwa" disabled>
                                                        <span>Polonnaruwa</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Puttalam" disabled>
                                                        <span>Puttalam</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Ratnapura" disabled>
                                                        <span>Ratnapura</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Trincomalee" disabled>
                                                        <span>Trincomalee</span>
                                                    </label>
                                                    <label class="checkbox-item">
                                                        <input type="checkbox" name="districts[]" value="Vavuniya" disabled>
                                                        <span>Vavuniya</span>
                                                    </label>
                                                </div>
                                                <small class="form-hint">Select all districts where you provide services</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="availability" class="form-label">Current Availability</label>
                                                <select id="availability" name="availability" class="form-select" disabled>
                                                    <option value="available">Available</option>
                                                    <option value="unavailable">Unavailable</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section">
                                        <h4 class="form-section-title">Working Hours</h4>
                                        <div class="working-hours-container">
                                            <div class="working-hours-grid">
                                                <div class="working-hours-row">
                                                    <span class="day-label">Monday - Friday</span>
                                                    <div class="time-inputs">
                                                        <input type="time" id="weekday-start" name="weekday-start" class="form-input time-input" value="" readonly>
                                                        <span class="time-separator">to</span>
                                                        <input type="time" id="weekday-end" name="weekday-end" class="form-input time-input" value="" readonly>
                                                    </div>
                                                </div>
                                                <div class="working-hours-row">
                                                    <span class="day-label">Saturday</span>
                                                    <div class="time-inputs">
                                                        <input type="time" id="saturday-start" name="saturday-start" class="form-input time-input" value="" readonly>
                                                        <span class="time-separator">to</span>
                                                        <input type="time" id="saturday-end" name="saturday-end" class="form-input time-input" value="" readonly>
                                                    </div>
                                                </div>
                                                <div class="working-hours-row">
                                                    <span class="day-label">Sunday</span>
                                                    <div class="time-inputs">
                                                        <label class="checkbox-wrapper">
                                                            <input type="checkbox" id="sunday-closed" name="sunday-closed" disabled>
                                                            <span class="checkbox-label">Closed</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="form-actions">
                                        <div class="form-actions-left">
                                            <button type="button" class="btn btn-outline" id="change-password-btn">
                                                <i class="fas fa-key"></i>
                                                <span>Change Password</span>
                                            </button>
                                        </div>
                                        <div class="form-actions-right">
                                            <button type="button" class="btn btn-secondary" id="cancel-changes-btn" style="display: none;">
                                                <span>Cancel</span>
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="save-changes-btn" style="display: none;">
                                                <i class="fas fa-save"></i>
                                                <span>Save Changes</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Change Password Modal -->
    <div class="modal-overlay" id="change-password-modal" style="display: none;">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Change Password</h3>
                <button class="modal-close" id="close-password-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form class="password-form" id="password-form">
                    <div class="form-group">
                        <label for="current-password" class="form-label">Current Password</label>
                        <input type="password" id="current-password" name="current-password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="new-password" class="form-label">New Password</label>
                        <input type="password" id="new-password" name="new-password" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="form-input" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" id="cancel-password-change">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pass user ID to JavaScript
        window.currentUserId = <?php echo json_encode((int)$userId); ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/profile.js"></script>
</body>
</html>

