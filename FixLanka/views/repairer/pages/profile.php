<?php
// Page configuration
$currentPage = 'profile';
$pageTitle = 'My Profile';
$pageSubtitle = 'Manage your personal information and settings';
$searchPlaceholder = 'Search requests, repairers, projects...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../common/global.css">
    <link rel="stylesheet" href="../common/variables.css">
    <link rel="stylesheet" href="../common/topbar.css">
    <link rel="stylesheet" href="../common/sidebar.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php include '../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php include '../common/sidebar.php'; ?>

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
                                        <img src="../common/user.png" alt="Profile Photo" class="profile-photo" id="profile-photo">
                                        <div class="profile-photo-overlay">
                                            <button class="photo-upload-btn" id="photo-upload-btn">
                                                <i class="fas fa-camera"></i>
                                                <span>Change Photo</span>
                                            </button>
                                        </div>
                                        <input type="file" id="photo-upload-input" accept="image/*" style="display: none;">
                                    </div>
                                    <div class="profile-photo-info">
                                        <h3 class="profile-name">John Doe</h3>
                                        <p class="profile-role">Repair Specialist</p>
                                        <div class="profile-rating">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <span class="rating-text">4.8 (127 reviews)</span>
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
                                                <span class="info-card-value">January 2023</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-tools"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Jobs Completed</span>
                                                <span class="info-card-value">127</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-medal"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Success Rate</span>
                                                <span class="info-card-value">98%</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <i class="fas fa-clock"></i>
                                            <div class="info-card-content">
                                                <span class="info-card-label">Response Time</span>
                                                <span class="info-card-value">< 2 hours</span>
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
                                                <input type="text" id="full-name" name="full-name" class="form-input" value="John Doe" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" id="email" name="email" class="form-input" value="john.doe@fixlanka.com" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" id="phone" name="phone" class="form-input" value="+1 (555) 123-4567" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label for="service-category" class="form-label">Service Category</label>
                                                <select id="service-category" name="service-category" class="form-select" disabled>
                                                    <option value="electronics">Electronics Repair</option>
                                                    <option value="appliances">Home Appliances</option>
                                                    <option value="automotive">Automotive</option>
                                                    <option value="plumbing">Plumbing</option>
                                                    <option value="electrical">Electrical</option>
                                                    <option value="carpentry">Carpentry</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="district" class="form-label">Working District</label>
                                                <select id="district" name="district" class="form-select" disabled>
                                                    <option value="">Select District</option>
                                                    <option value="colombo">Colombo</option>
                                                    <option value="gampaha">Gampaha</option>
                                                    <option value="kalutara">Kalutara</option>
                                                    <option value="kandy">Kandy</option>
                                                    <option value="matale">Matale</option>
                                                    <option value="nuwara-eliya">Nuwara Eliya</option>
                                                    <option value="galle">Galle</option>
                                                    <option value="matara">Matara</option>
                                                    <option value="hambantota">Hambantota</option>
                                                    <option value="jaffna">Jaffna</option>
                                                    <option value="kilinochchi">Kilinochchi</option>
                                                    <option value="mannar">Mannar</option>
                                                    <option value="vavuniya">Vavuniya</option>
                                                    <option value="mullaitivu">Mullaitivu</option>
                                                    <option value="batticaloa">Batticaloa</option>
                                                    <option value="ampara">Ampara</option>
                                                    <option value="trincomalee">Trincomalee</option>
                                                    <option value="kurunegala">Kurunegala</option>
                                                    <option value="puttalam">Puttalam</option>
                                                    <option value="anuradhapura">Anuradhapura</option>
                                                    <option value="polonnaruwa">Polonnaruwa</option>
                                                    <option value="badulla">Badulla</option>
                                                    <option value="moneragala">Moneragala</option>
                                                    <option value="ratnapura">Ratnapura</option>
                                                    <option value="kegalle">Kegalle</option>
                                                </select>
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
                                                        <input type="time" id="weekday-start" name="weekday-start" class="form-input time-input" value="09:00" readonly>
                                                        <span class="time-separator">to</span>
                                                        <input type="time" id="weekday-end" name="weekday-end" class="form-input time-input" value="18:00" readonly>
                                                    </div>
                                                </div>
                                                <div class="working-hours-row">
                                                    <span class="day-label">Saturday</span>
                                                    <div class="time-inputs">
                                                        <input type="time" id="saturday-start" name="saturday-start" class="form-input time-input" value="10:00" readonly>
                                                        <span class="time-separator">to</span>
                                                        <input type="time" id="saturday-end" name="saturday-end" class="form-input time-input" value="16:00" readonly>
                                                    </div>
                                                </div>
                                                <div class="working-hours-row">
                                                    <span class="day-label">Sunday</span>
                                                    <div class="time-inputs">
                                                        <label class="checkbox-wrapper">
                                                            <input type="checkbox" id="sunday-closed" name="sunday-closed" checked disabled>
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

    <script src="profile.js"></script>
</body>
</html>
