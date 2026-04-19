<?php
// Page configuration
$currentPage = 'settings';
$pageTitle = 'Settings';
$pageSubtitle = 'Manage your account preferences and application settings';
$searchPlaceholder = 'Search settings...';
$repairerId = (int)($_SESSION['user_id'] ?? 0);
$repairerName = (string)($_SESSION['user_name'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/settings.css">
</head>
<body>
    <script>
        window.REPAIRER_ID = <?php echo $repairerId; ?>;
    </script>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

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
                                <button class="filter-tab settings-tab active" data-tab="notifications">
                                    <i class="fas fa-bell"></i>
                                    <span>Notifications</span>
                                </button>
                                <button class="filter-tab settings-tab" data-tab="privacy">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>Privacy & Security</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Settings Content -->
                    <div class="settings-content">
                    <!-- Notifications Settings -->
                    <div class="settings-panel active" id="notifications-panel">
                        <div class="settings-header">
                            <h2>Notification Preferences</h2>
                            <p>Choose what notifications you want to receive</p>
                        </div>

                        <div class="settings-section">
                            <h3>Email Notifications</h3>
                            <div class="settings-options">
                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="emailJobRequests">New Job Requests</label>
                                        <p>Get notified when new job requests match your skills</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="emailJobRequests" name="emailJobRequests" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="emailQuoteResponses">Quote Responses</label>
                                        <p>Receive alerts when customers respond to your quotes</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="emailQuoteResponses" name="emailQuoteResponses" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="emailPaymentNotifications">Payment Notifications</label>
                                        <p>Get notified about payment receipts and transactions</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="emailPaymentNotifications" name="emailPaymentNotifications" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="emailReviewsRatings">Reviews and Ratings</label>
                                        <p>Be alerted when you receive new reviews</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="emailReviewsRatings" name="emailReviewsRatings" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="emailWeeklySummary">Weekly Summary</label>
                                        <p>Receive weekly performance and earnings reports</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="emailWeeklySummary" name="emailWeeklySummary">
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
                                        <label for="pushBrowserNotifications">Browser Notifications</label>
                                        <p>Show desktop notifications for important updates</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="pushBrowserNotifications" name="pushBrowserNotifications">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="pushSoundAlerts">Sound Alerts</label>
                                        <p>Play sound when receiving notifications</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="pushSoundAlerts" name="pushSoundAlerts" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="settings-actions">
                            <button class="btn btn-primary" id="saveNotificationsBtn" data-save="notifications">Save Preferences</button>
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
                                        <label for="privacyProfileVisibility">Profile Visibility</label>
                                        <p>Make your profile visible to customers</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="privacyProfileVisibility" name="privacyProfileVisibility" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="privacyShowContact">Show Contact Information</label>
                                        <p>Display your phone number and email publicly</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="privacyShowContact" name="privacyShowContact">
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="privacyLocationSharing">Location Sharing</label>
                                        <p>Share your location for nearby job matching</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="privacyLocationSharing" name="privacyLocationSharing" checked>
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
                                        <label for="securityLoginAlerts">Login Alerts</label>
                                        <p>Get notified of login attempts from new devices</p>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="securityLoginAlerts" name="securityLoginAlerts" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>

                                <div class="setting-option">
                                    <div class="option-info">
                                        <label for="securitySessionTimeout">Session Timeout</label>
                                        <p>Automatically log out after period of inactivity</p>
                                    </div>
                                    <select class="form-select" id="securitySessionTimeout" name="securitySessionTimeout">
                                        <option value="15 minutes">15 minutes</option>
                                        <option value="30 minutes" selected>30 minutes</option>
                                        <option value="1 hour">1 hour</option>
                                        <option value="Never">Never</option>
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
                            <button class="btn btn-primary" id="savePrivacyBtn" data-save="privacy">Save Security Settings</button>
                        </div>
                    </div>
                </div>
                </div>
            </main>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/settings.js"></script>
</body>
</html>

