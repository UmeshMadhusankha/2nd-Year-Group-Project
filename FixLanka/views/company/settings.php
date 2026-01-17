<?php
// Start session and verify authentication
require_once '../../config/session.php';
requireRole('company');
$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/settings.css">
    <!-- <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="app-container">
        <!-- Include Sidebar -->
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Topbar -->
            <!-- Include Topbar -->
            <?php include 'topbar.php'; ?>

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

                    Security Tab
                    <div class="tab-content" id="security-tab">

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

                        <!-- Change Password Section -->
                        <div class="settings-section">
                            <h2 class="section-title">Change Password</h2>
                            <form id="changePasswordForm" autocomplete="off" onsubmit="event.preventDefault(); changePassword();">
                                <!-- Robust Browser Autofill Trick: Inputs must be 'visible' but hidden from view -->
                                <div style="position: absolute; left: -9999px; top: -9999px;">
                                    <input type="text" name="fake_username" tabindex="-1">
                                    <input type="password" name="fake_password" tabindex="-1">
                                </div>

                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" id="currentPassword" name="current_password_input" class="form-control" 
                                           autocomplete="new-password" readonly 
                                           onfocus="this.removeAttribute('readonly');" required>
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <input type="password" id="newPassword" name="new_password_input" class="form-control" 
                                           autocomplete="new-password" readonly 
                                           onfocus="this.removeAttribute('readonly');" required minlength="8">
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <input type="password" id="confirmPassword" name="confirm_password_input" class="form-control" 
                                           autocomplete="new-password" readonly 
                                           onfocus="this.removeAttribute('readonly');" required minlength="8">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-key"></i> Update Password
                                    </button>
                                </div>
                            </form>
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <!-- <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/settings.js"></script> -->

    <!-- Load Sidebar and Topbar Components -->
    <script>
        // Settings Tab Logic
        document.addEventListener('DOMContentLoaded', () => {
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Remove active from all
                    tabBtns.forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Add active to current
                    btn.classList.add('active');
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Force clear password fields to prevent stubborn autofill
            setTimeout(() => {
                const pwFields = ['currentPassword', 'newPassword', 'confirmPassword'];
                pwFields.forEach(id => {
                    const el = document.getElementById(id);
                    if(el) {
                        el.value = ''; 
                        el.setAttribute('readonly', 'readonly'); // Re-apply readonly just in case
                    }
                });
            }, 500); // Slight delay to override browser fill
        });

        async function changePassword() {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                showToast('New passwords do not match', 'error');
                return;
            }

            try {
                 const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-profile.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'change_password',
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message, 'success');
                    document.getElementById('changePasswordForm').reset();
                } else {
                    showToast(result.message || 'Failed to change password', 'error');
                }
            } catch (error) {
                 console.error('Error changing password:', error);
                 showToast('An error occurred. Please try again.', 'error');
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = toast.querySelector('.toast-icon');
            const title = toast.querySelector('h4');
            const text = toast.querySelector('p');
            
            toast.style.display = 'flex';
            text.textContent = message;
            
            if (type === 'success') {
                icon.className = 'toast-icon success';
                icon.innerHTML = '<i class="fas fa-check-circle"></i>';
                title.textContent = 'Success!';
            } else {
                icon.className = 'toast-icon error';
                icon.innerHTML = '<i class="fas fa-times-circle"></i>';
                title.textContent = 'Error';
                // Add basic error style if not present in CSS
                icon.style.backgroundColor = '#ffebee';
                icon.style.color = '#c62828';
            }
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
            
        // Close toast button
        document.querySelector('.toast-close').addEventListener('click', () => {
            document.getElementById('toast').style.display = 'none';
        });
    </script>

    <!-- Set active sidebar item for Settings page -->
    <script>
        // Set Settings as active page in localStorage
        localStorage.setItem('activePage', 'Settings');
    </script>
</body>

</html>



