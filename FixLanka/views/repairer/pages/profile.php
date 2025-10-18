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
        <header class="header">
            <div class="header-left">
                <label for="sidebar-toggle" class="sidebar-toggle">
                    <i class="fas fa-bars"></i>
                </label>
                <div class="logo">
                    <img src="../common/fixlanka.png" alt="FixLanka" class="logo-image">
                </div>
                <div class="page-info">
                    <h1 class="page-title">My Profile</h1>
                    <p class="page-subtitle">Manage your personal information and settings</p>
                </div>
            </div>
            <div class="header-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search requests, repairers, projects...">
                </div>
                <div class="notification-bell">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="profile-menu">
                    <img src="../common/user.png" alt="Admin" class="profile-avatar">
                    <div class="profile-dropdown">
                        <div class="profile-dropdown-header">
                            <h4 class="profile-dropdown-name">John Doe</h4>
                            <p class="profile-dropdown-email">john.doe@fixlanka.com</p>
                        </div>
                        <ul class="profile-dropdown-menu">
                            <li class="profile-dropdown-item">
                                <a href="profile.php" class="profile-dropdown-link" data-action="profile">
                                    <i class="fas fa-user"></i>
                                    <span>My Profile</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="settings.php" class="profile-dropdown-link" data-action="settings">
                                    <i class="fas fa-cog"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="upgrade.php" class="profile-dropdown-link" data-action="upgrade">
                                    <i class="fas fa-crown"></i>
                                    <span>Upgrade</span>
                                </a>
                            </li>
                            <div class="profile-dropdown-divider"></div>
                            <li class="profile-dropdown-item">
                                <a href="support.php" class="profile-dropdown-link" data-action="support">
                                    <i class="fas fa-life-ring"></i>
                                    <span>Support</span>
                                </a>
                            </li>
                            <li class="profile-dropdown-item">
                                <a href="#" class="profile-dropdown-link logout" data-action="logout">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="welcome.php" class="nav-link">
                            <i class="fas fa-home"></i>
                            <span>Welcome</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="available-jobs.php" class="nav-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Available Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="my-jobs.php" class="nav-link">
                            <i class="fas fa-tasks"></i>
                            <span>My Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="earnings.php" class="nav-link">
                            <i class="fas fa-wallet"></i>
                            <span>Earnings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="reviews.php" class="nav-link">
                            <i class="fas fa-star"></i>
                            <span>Reviews</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="profile.php" class="nav-link">
                            <i class="fas fa-user"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="support.php" class="nav-link">
                            <i class="fas fa-life-ring"></i>
                            <span>Support</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="upgrade.php" class="nav-link">
                            <i class="fas fa-crown"></i>
                            <span>Upgrade</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#settings" class="nav-link">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Footer in Sidebar -->
            <footer class="sidebar-footer">
                <p>© 2025 FixLanka<br>
                   <a href="#terms">Terms</a> | 
                   <a href="#privacy">Privacy</a> | 
                   <a href="#help">Help</a>
                </p>
            </footer>
        </aside>

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
