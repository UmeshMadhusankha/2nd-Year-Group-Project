<?php
require_once __DIR__ . '/../../config/session.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .settings-container {
            max-width: 1000px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }

        .settings-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }

        .settings-header p {
            color: #666;
            font-size: 16px;
        }

        .settings-tabs {
            display: flex;
            gap: 10px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .settings-tab {
            padding: 12px 24px;
            background: #f8f9fa;
            border: none;
            border-radius: 8px;
            color: #666;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .settings-tab:hover {
            background: #e9ecef;
        }

        .settings-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .settings-panel {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .settings-panel.active {
            display: block;
        }

        .settings-section {
            margin-bottom: 40px;
        }

        .settings-section:last-child {
            margin-bottom: 0;
        }

        .settings-section h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .setting-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .option-info label {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .option-info p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .toggle-slider {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .toggle-switch input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .settings-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #666;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .danger-zone {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 20px;
            background: #fff5f5;
        }

        .danger-zone h3 {
            color: #dc3545;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .error-message {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .settings-container {
                margin-top: 80px;
            }

            .settings-tabs {
                flex-wrap: wrap;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .settings-actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="settings-container">
        <div class="settings-header">
            <h1>Settings</h1>
            <p>Manage your account preferences and settings</p>
        </div>

        <div class="settings-tabs">
            <button class="settings-tab active" data-tab="account">
                <i class="fas fa-user-circle"></i>
                <span>Account</span>
            </button>
            <button class="settings-tab" data-tab="notifications">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
            </button>
            <button class="settings-tab" data-tab="privacy">
                <i class="fas fa-shield-alt"></i>
                <span>Privacy & Security</span>
            </button>
        </div>

        <!-- Account Settings Panel -->
        <div class="settings-panel active" id="account-panel">
            <div class="settings-section">
                <h3>Profile Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" class="form-input" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" class="form-input" placeholder="Enter last name">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-input" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-input" placeholder="+94 77 123 4567">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" class="form-input" placeholder="Enter your address"></textarea>
                </div>
            </div>

            <div class="settings-section">
                <h3>Change Password</h3>
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

            <div class="settings-actions">
                <button class="btn btn-secondary">Cancel</button>
                <button class="btn btn-primary">Save Changes</button>
            </div>
        </div>

        <!-- Notifications Settings Panel -->
        <div class="settings-panel" id="notifications-panel">
            <div class="settings-section">
                <h3>Email Notifications</h3>
                <div class="setting-option">
                    <div class="option-info">
                        <label>Job Updates</label>
                        <p>Get notified about your job requests and updates</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-option">
                    <div class="option-info">
                        <label>Quote Responses</label>
                        <p>Receive alerts when repairers respond to your requests</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-option">
                    <div class="option-info">
                        <label>Payment Notifications</label>
                        <p>Get notified about payment confirmations</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-option">
                    <div class="option-info">
                        <label>Promotional Emails</label>
                        <p>Receive news and offers from FixLanka</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="settings-section">
                <h3>Push Notifications</h3>
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

            <div class="settings-actions">
                <button class="btn btn-primary">Save Preferences</button>
            </div>
        </div>

        <!-- Privacy & Security Settings Panel -->
        <div class="settings-panel" id="privacy-panel">
            <div class="settings-section">
                <h3>Privacy Settings</h3>
                <div class="setting-option">
                    <div class="option-info">
                        <label>Profile Visibility</label>
                        <p>Make your profile visible to service providers</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-option">
                    <div class="option-info">
                        <label>Show Contact Information</label>
                        <p>Display your phone number to matched repairers</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="setting-option">
                    <div class="option-info">
                        <label>Location Sharing</label>
                        <p>Share your location for better service matching</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            <div class="settings-section">
                <h3>Security Settings</h3>
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
            </div>

            <div class="settings-section danger-zone">
                <h3>Danger Zone</h3>
                <div class="setting-option" style="background: transparent;">
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
    </div>

    <script>
        // Tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.settings-tab');
            const panels = document.querySelectorAll('.settings-panel');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all tabs and panels
                    tabs.forEach(t => t.classList.remove('active'));
                    panels.forEach(p => p.classList.remove('active'));

                    // Add active class to clicked tab and corresponding panel
                    this.classList.add('active');
                    document.getElementById(targetTab + '-panel').classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
