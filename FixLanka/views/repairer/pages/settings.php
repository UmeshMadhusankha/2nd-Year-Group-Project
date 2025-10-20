<?php
// Page configuration
$currentPage = 'settings';
$pageTitle = 'Settings';
$pageSubtitle = 'Manage your account preferences and application settings';
$searchPlaceholder = 'Search settings...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../common/global.css">
    <link rel="stylesheet" href="../common/variables.css">
    <link rel="stylesheet" href="../common/topbar.css">
    <link rel="stylesheet" href="../common/sidebar.css">
    <link rel="stylesheet" href="settings.css">
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
        <div class="main-content-wrapper">
            <main class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header-section">
                        <div class="page-header">
                            <div class="page-header-text">
                                <h1 class="page-header-title">Settings</h1>
                                <p class="page-header-subtitle">Manage your account preferences and application settings</p>
                            </div>
                        </div>
                    </section>

                    <!-- Settings Navigation Tabs -->
                    <section class="filter-tabs-section">
                        <div class="filter-tabs-container">
                            <div class="filter-tabs settings-tabs">
                                <button class="filter-tab settings-tab active" data-tab="account">
                                    <i class="fas fa-user-circle"></i>
                                    <span>Account</span>
                                </button>
                                <button class="filter-tab settings-tab" data-tab="notifications">
                                    <i class="fas fa-bell"></i>
                                    <span>Notifications</span>
                                </button>
                                <button class="filter-tab settings-tab" data-tab="privacy">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Privacy & Security</span>
                                </button>
                                <button class="filter-tab settings-tab" data-tab="preferences">
                                    <i class="fas fa-sliders-h"></i>
                                    <span>Preferences</span>
                                </button>
                                <button class="filter-tab settings-tab" data-tab="billing">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Billing</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Settings Content -->
                    <div class="settings-content">
                    <!-- Account Settings -->
                    <div class="settings-panel active" id="account-panel">
                        <div class="settings-header">
                            <h2>Account Settings</h2>
                            <p>Manage your account information and profile details</p>
                        </div>

                        <div class="settings-section">
                            <h3>Profile Information</h3>
                            <div class="settings-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="fullName">Full Name</label>
                                        <input type="text" id="fullName" value="John Doe" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input type="text" id="username" value="johndoe" class="form-input">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" id="email" value="john.doe@fixlanka.com" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" id="phone" value="+94 77 123 4567" class="form-input">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="bio">Bio</label>
                                    <textarea id="bio" rows="4" class="form-input">Professional repairer with 10+ years of experience in electrical and plumbing services.</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Change Password</h3>
                            <div class="settings-form">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" id="currentPassword" class="form-input" placeholder="Enter current password">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="newPassword">New Password</label>
                                        <input type="password" id="newPassword" class="form-input" placeholder="Enter new password">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirmPassword">Confirm Password</label>
                                        <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm new password">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-secondary">Cancel</button>
                            <button class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>

                    <!-- Notifications Settings -->
                    <div class="settings-panel" id="notifications-panel">
                        <div class="settings-header">
                            <h2>Notification Preferences</h2>
                            <p>Choose what notifications you want to receive</p>
                        </div>

                        <div class="settings-section">
                            <h3>Email Notifications</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>New Job Requests</label>
                                        <p>Get notified when new job requests match your skills</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Quote Responses</label>
                                        <p>Receive alerts when customers respond to your quotes</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Payment Notifications</label>
                                        <p>Get notified about payment receipts and transactions</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Reviews and Ratings</label>
                                        <p>Be alerted when you receive new reviews</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Weekly Summary</label>
                                        <p>Receive weekly performance and earnings reports</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Push Notifications</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Browser Notifications</label>
                                        <p>Show desktop notifications for important updates</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Sound Alerts</label>
                                        <p>Play sound when receiving notifications</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-primary">Save Preferences</button>
                        </div>
                    </div>

                    <!-- Privacy & Security Settings -->
                    <div class="settings-panel" id="privacy-panel">
                        <div class="settings-header">
                            <h2>Privacy & Security</h2>
                            <p>Control your privacy and account security settings</p>
                        </div>

                        <div class="settings-section">
                            <h3>Privacy Settings</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Profile Visibility</label>
                                        <p>Make your profile visible to customers</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Show Contact Information</label>
                                        <p>Display your phone number and email publicly</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Location Sharing</label>
                                        <p>Share your location for nearby job matching</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Security Settings</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Two-Factor Authentication</label>
                                        <p>Add an extra layer of security to your account</p>
                                    </div>
                                    <button class="btn btn-outline">Enable 2FA</button>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Login Alerts</label>
                                        <p>Get notified of login attempts from new devices</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Session Timeout</label>
                                        <p>Automatically log out after period of inactivity</p>
                                    </div>
                                    <select class="form-select">
                                        <option>15 minutes</option>
                                        <option selected>30 minutes</option>
                                        <option>1 hour</option>
                                        <option>Never</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section danger-zone">
                            <h3>Danger Zone</h3>
                            <div class="setting-option">
                                <div class="option-info">
                                    <label>Delete Account</label>
                                    <p>Permanently delete your account and all associated data</p>
                                </div>
                                <button class="btn btn-danger">Delete Account</button>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-primary">Save Security Settings</button>
                        </div>
                    </div>

                    <!-- Preferences Settings -->
                    <div class="settings-panel" id="preferences-panel">
                        <div class="settings-header">
                            <h2>Preferences</h2>
                            <p>Customize your experience</p>
                        </div>

                        <div class="settings-section">
                            <h3>Appearance</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Theme</label>
                                        <p>Choose your preferred color theme</p>
                                    </div>
                                    <select class="form-select">
                                        <option selected>Light</option>
                                        <option>Dark</option>
                                        <option>Auto</option>
                                    </select>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Language</label>
                                        <p>Select your preferred language</p>
                                    </div>
                                    <select class="form-select">
                                        <option selected>English</option>
                                        <option>Sinhala</option>
                                        <option>Tamil</option>
                                    </select>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Date Format</label>
                                        <p>Choose how dates are displayed</p>
                                    </div>
                                    <select class="form-select">
                                        <option>MM/DD/YYYY</option>
                                        <option selected>DD/MM/YYYY</option>
                                        <option>YYYY-MM-DD</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Job Preferences</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Service Radius</label>
                                        <p>Maximum distance for job matching</p>
                                    </div>
                                    <div class="range-input">
                                        <input type="range" min="5" max="50" value="20" id="radiusRange">
                                        <span id="radiusValue">20 km</span>
                                    </div>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Availability Status</label>
                                        <p>Let customers know you're available</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label>Auto-Accept Simple Jobs</label>
                                        <p>Automatically accept jobs under LKR 5,000</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-primary">Save Preferences</button>
                        </div>
                    </div>

                    <!-- Billing Settings -->
                    <div class="settings-panel" id="billing-panel">
                        <div class="settings-header">
                            <h2>Billing & Payments</h2>
                            <p>Manage your payment methods and billing information</p>
                        </div>

                        <div class="settings-section">
                            <h3>Payment Methods</h3>
                            <div class="payment-methods">
                                <div class="payment-card">
                                    <div class="card-icon">
                                        <i class="fab fa-cc-visa"></i>
                                    </div>
                                    <div class="card-info">
                                        <p class="card-type">Visa ending in 4242</p>
                                        <p class="card-expiry">Expires 12/2025</p>
                                    </div>
                                    <div class="card-actions">
                                        <span class="badge badge-primary">Default</span>
                                        <button class="btn-icon"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>

                                <div class="payment-card">
                                    <div class="card-icon">
                                        <i class="fab fa-cc-mastercard"></i>
                                    </div>
                                    <div class="card-info">
                                        <p class="card-type">Mastercard ending in 8888</p>
                                        <p class="card-expiry">Expires 06/2026</p>
                                    </div>
                                    <div class="card-actions">
                                        <button class="btn btn-sm btn-outline">Set as Default</button>
                                        <button class="btn-icon"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>

                                <button class="btn btn-outline add-payment-btn">
                                    <i class="fas fa-plus"></i>
                                    Add New Payment Method
                                </button>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Bank Account</h3>
                            <div class="settings-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="bankName">Bank Name</label>
                                        <input type="text" id="bankName" value="Commercial Bank" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label for="accountNumber">Account Number</label>
                                        <input type="text" id="accountNumber" value="************1234" class="form-input">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="accountName">Account Holder Name</label>
                                        <input type="text" id="accountName" value="John Doe" class="form-input">
                                    </div>
                                    <div class="form-group">
                                        <label for="branchCode">Branch Code</label>
                                        <input type="text" id="branchCode" value="001" class="form-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h3>Billing History</h3>
                            <div class="billing-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Oct 15, 2025</td>
                                            <td>Monthly Subscription</td>
                                            <td>LKR 2,500</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                            <td><button class="btn-link">Download</button></td>
                                        </tr>
                                        <tr>
                                            <td>Sep 15, 2025</td>
                                            <td>Monthly Subscription</td>
                                            <td>LKR 2,500</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                            <td><button class="btn-link">Download</button></td>
                                        </tr>
                                        <tr>
                                            <td>Aug 15, 2025</td>
                                            <td>Monthly Subscription</td>
                                            <td>LKR 2,500</td>
                                            <td><span class="badge badge-success">Paid</span></td>
                                            <td><button class="btn-link">Download</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-primary">Update Billing Info</button>
                        </div>
                    </div>
                </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../common/common.js"></script>
    <script src="settings.js"></script>
</body>
</html>
