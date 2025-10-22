<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../assets/css/company/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/company/topbar.css">
    <link rel="stylesheet" href="../../assets/css/company/settings.css">
    <!-- <link rel="stylesheet" href="../../assets/css/company/dashboard.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Include Sidebar -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Topbar -->
            <div id="topbar-container"></div>

            <!-- Settings Header -->
            <div class="settings-header">
                <div class="header-left">
                    <h1><i class="fas fa-cog"></i> Settings</h1>
                    <p class="subtitle">Configure system preferences, notifications, and security settings</p>
                </div>
                <div class="header-right">
                    <button class="btn-save-all" id="saveAllBtn">
                        <i class="fas fa-save"></i> Save All Changes
                    </button>
                </div>
            </div>
            <!-- Settings Content -->
            <div class="settings-container">
                <!-- Settings Navigation Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="notifications-tab">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </button>
                    <button class="tab-btn" data-tab="security-tab">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security</span>
                    </button>
                    <button class="tab-btn" data-tab="preferences-tab">
                        <i class="fas fa-sliders-h"></i>
                        <span>Preferences</span>
                    </button>
                    <button class="tab-btn" data-tab="billing-tab">
                        <i class="fas fa-credit-card"></i>
                        <span>Subscription</span>
                    </button>
                </div>

                <!-- Settings Content Panels -->
                <div class="settings-content">

                    <!-- Notifications Tab -->
                    <div class="tab-content active" id="notifications-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Email Notifications</h2>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>New Repair Requests</h3>
                                    <p>Get notified when customers submit new repair requests</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailRepairRequests" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Project Updates</h3>
                                    <p>Receive updates when project status changes</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailProjectUpdates" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Payment Notifications</h3>
                                    <p>Get alerts for received payments and pending invoices</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailPayments" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Team Activity</h3>
                                    <p>Updates about your workforce assignments and availability</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailTeamActivity">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Customer Messages</h3>
                                    <p>Receive notifications for new customer messages</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailMessages" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Push Notifications</h2>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Desktop Notifications</h3>
                                    <p>Show desktop notifications for important updates</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="pushDesktop" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Mobile Push Notifications</h3>
                                    <p>Receive push notifications on your mobile device</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="pushMobile">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Notification Frequency</h2>

                            <div class="form-group">
                                <label for="notificationFrequency">Email Digest</label>
                                <select id="notificationFrequency">
                                    <option value="realtime">Real-time (as they happen)</option>
                                    <option value="hourly">Hourly digest</option>
                                    <option value="daily">Daily digest</option>
                                    <option value="weekly">Weekly digest</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="quietHoursStart">Quiet Hours</label>
                                <div class="time-range">
                                    <input type="time" id="quietHoursStart" value="22:00">
                                    <span>to</span>
                                    <input type="time" id="quietHoursEnd" value="08:00">
                                </div>
                                <small>No notifications will be sent during these hours</small>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelNotificationsBtn">Cancel</button>
                            <button class="btn-primary" id="saveNotificationsBtn">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-content" id="security-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Two-Factor Authentication</h2>

                            <div class="security-card">
                                <div class="security-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="security-content">
                                    <h3>Two-Factor Authentication (2FA)</h3>
                                    <p>Add an extra layer of security to your account by requiring both your password
                                        and an authentication code</p>
                                    <div class="security-status">
                                        <span class="status-badge disabled">Disabled</span>
                                        <button class="btn-enable" id="enable2FABtn">Enable 2FA</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Active Sessions</h2>

                            <div class="session-item current">
                                <div class="session-icon">
                                    <i class="fas fa-laptop"></i>
                                </div>
                                <div class="session-info">
                                    <h3>Windows PC - Chrome</h3>
                                    <p>Colombo, Sri Lanka • Current session</p>
                                    <small>Last active: Just now</small>
                                </div>
                                <span class="session-badge">Current</span>
                            </div>

                            <div class="session-item">
                                <div class="session-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="session-info">
                                    <h3>Android - Mobile App</h3>
                                    <p>Colombo, Sri Lanka</p>
                                    <small>Last active: 2 hours ago</small>
                                </div>
                                <button class="btn-revoke">Revoke</button>
                            </div>

                            <div class="session-item">
                                <div class="session-icon">
                                    <i class="fas fa-tablet-alt"></i>
                                </div>
                                <div class="session-info">
                                    <h3>iPad - Safari</h3>
                                    <p>Kandy, Sri Lanka</p>
                                    <small>Last active: Yesterday</small>
                                </div>
                                <button class="btn-revoke">Revoke</button>
                            </div>
                            <!-- button class action-btn.danger -->

                            <button class="action-btn danger" id="revokeAllBtn">
                                <i class="fas fa-times-circle"></i> Revoke All Other Sessions
                            </button>

                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Login History</h2>

                            <div class="login-history">
                                <div class="history-item">
                                    <div class="history-icon success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="history-info">
                                        <h4>Successful login</h4>
                                        <p>Windows PC - Chrome • Colombo, Sri Lanka</p>
                                        <small>Today at 9:30 AM</small>
                                    </div>
                                </div>

                                <div class="history-item">
                                    <div class="history-icon success">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="history-info">
                                        <h4>Successful login</h4>
                                        <p>Android - Mobile App • Colombo, Sri Lanka</p>
                                        <small>Yesterday at 6:45 PM</small>
                                    </div>
                                </div>

                                <div class="history-item">
                                    <div class="history-icon failed">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <div class="history-info">
                                        <h4>Failed login attempt</h4>
                                        <p>Unknown device • Galle, Sri Lanka</p>
                                        <small>2 days ago at 11:20 PM</small>
                                    </div>
                                </div>
                            </div>

                            <button class="btn-secondary" id="viewFullHistoryBtn">
                                View Full History
                            </button>
                        </div>
                    </div>

                    <!-- Preferences Tab -->
                    <div class="tab-content" id="preferences-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Display Preferences</h2>

                            <div class="form-group">
                                <label for="language">Language</label>
                                <select id="language">
                                    <option value="en">English</option>
                                    <option value="si">Sinhala (සිංහල)</option>
                                    <option value="ta">Tamil (தமிழ்)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                <select id="timezone">
                                    <option value="Asia/Colombo">Asia/Colombo (GMT+5:30)</option>
                                    <option value="Asia/Kolkata">Asia/Kolkata (GMT+5:30)</option>
                                    <option value="UTC">UTC (GMT+0:00)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dateFormat">Date Format</label>
                                <select id="dateFormat">
                                    <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                    <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select id="currency">
                                    <option value="LKR">LKR - Sri Lankan Rupee</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="GBP">GBP - British Pound</option>
                                </select>
                            </div>

                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3>Dark Mode</h3>
                                    <p>Switch to dark theme for better visibility in low light</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="darkModeToggle">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Dashboard Preferences</h2>

                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3>Compact View</h3>
                                    <p>Display more information in less space</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="compactView">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3>Show Request IDs</h3>
                                    <p>Display request IDs on cards and lists</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="showRequestIDs" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="preference-item">
                                <div class="preference-info">
                                    <h3>Auto-refresh Data</h3>
                                    <p>Automatically refresh dashboard data every 5 minutes</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="autoRefresh" checked>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label for="defaultView">Default Dashboard View</label>
                                <select id="defaultView">
                                    <option value="overview">Overview</option>
                                    <option value="requests">Repair Requests</option>
                                    <option value="projects">Projects</option>
                                    <option value="calendar">Calendar</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button class="btn-secondary" id="cancelPreferencesBtn">Cancel</button>
                            <button class="btn-primary" id="savePreferencesBtn">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                        </div>
                    </div>

                    <!-- Billing Tab -->
                    <div class="tab-content" id="billing-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Subscription Plan</h2>

                            <div class="current-plan-card">
                                <div class="plan-header">
                                    <div class="plan-info">
                                        <h3>Professional Plan</h3>
                                        <p>Perfect for growing repair businesses</p>
                                    </div>
                                    <div class="plan-price">
                                        <span class="price">LKR 5,000</span>
                                        <span class="period">/month</span>
                                    </div>
                                </div>
                                <div class="plan-features">
                                    <div class="feature-item">
                                        <i class="fas fa-check"></i>
                                        <span>Unlimited repair requests</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-check"></i>
                                        <span>Up to 10 team members</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-check"></i>
                                        <span>Priority customer support</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-check"></i>
                                        <span>Advanced analytics</span>
                                    </div>
                                </div>
                                <div class="plan-actions">
                                    <button class="btn-secondary">Change Plan</button>
                                    <button class="action-btn danger">Cancel Subscription</button>
                                </div>
                            </div>

                            <div class="billing-info">
                                <div class="info-item">
                                    <span class="label">Billing Period:</span>
                                    <span class="value">Monthly</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Next Billing Date:</span>
                                    <span class="value">November 15, 2025</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Payment Method:</span>
                                    <span class="value">•••• 4242 (Visa)</span>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Payment Methods</h2>

                            <div class="payment-method-card active">
                                <div class="card-icon">
                                    <i class="fab fa-cc-visa"></i>
                                </div>
                                <div class="card-info">
                                    <h4>Visa ending in 4242</h4>
                                    <p>Expires 12/2026</p>
                                </div>
                                <div class="card-badge">
                                    <span class="badge-primary">Primary</span>
                                </div>
                                <button class="btn-icon" title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="payment-method-card">
                                <div class="card-icon">
                                    <i class="fab fa-cc-mastercard"></i>
                                </div>
                                <div class="card-info">
                                    <h4>Mastercard ending in 8888</h4>
                                    <p>Expires 09/2027</p>
                                </div>
                                <button class="btn-set-primary">Set as Primary</button>
                                <button class="btn-icon" title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <button class="btn-add-card" id="addCardBtn">
                                <i class="fas fa-plus"></i> Add Payment Method
                            </button>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Billing History</h2>

                            <div class="billing-history">
                                <table class="history-table">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>INV-2025-10-001</td>
                                            <td>Oct 15, 2025</td>
                                            <td>LKR 5,000</td>
                                            <td><span class="status-badge paid">Paid</span></td>
                                            <td>
                                                <button class="btn-icon" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INV-2025-09-001</td>
                                            <td>Sep 15, 2025</td>
                                            <td>LKR 5,000</td>
                                            <td><span class="status-badge paid">Paid</span></td>
                                            <td>
                                                <button class="btn-icon" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>INV-2025-08-001</td>
                                            <td>Aug 15, 2025</td>
                                            <td>LKR 5,000</td>
                                            <td><span class="status-badge paid">Paid</span></td>
                                            <td>
                                                <button class="btn-icon" title="Download">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button class="btn-icon" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <button class="btn-secondary" id="viewAllInvoicesBtn">
                                View All Invoices
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <div class="toast-icon success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-content">
            <h4>Success!</h4>
            <p>Your settings have been saved successfully.</p>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Load Components and Scripts -->
    <script src="../../assets/javascript/common/component-loader.js"></script>
    <script src="../../assets/javascript/company/sidebar.js"></script>
    <script src="../../assets/javascript/company/settings.js"></script>

    <!-- Set active sidebar item for Settings page -->
    <script>
        // Wait for sidebar to load
        setTimeout(() => {
            // Set Settings as active page in localStorage
            localStorage.setItem('activePage', 'Settings');

            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Find and activate the Settings nav item
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                const linkText = link.querySelector('span')?.textContent;
                if (linkText === 'Settings') {
                    link.closest('.nav-item').classList.add('active');
                }
            });
        }, 300);
    </script>
</body>

</html>