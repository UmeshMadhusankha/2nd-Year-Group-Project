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

                        <div class="settings-section" id="sessions-container">
                            <h2 class="section-title">
                                Active Sessions 
                                <span class="session-count-badge" id="sessionCountBadge">Loading...</span>
                            </h2>
                            
                            <!-- Sessions will be loaded here dynamically -->
                            <div id="sessions-list">
                                <div class="loading-spinner">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading sessions...</p>
                                </div>
                            </div>

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

                    <!-- Billing Tab -->
                    <div class="tab-content" id="billing-tab">
                        <div class="settings-section">
                            <h2 class="section-title">Subscription Plan</h2>

                            <div class="current-plan-card">
                                <div class="plan-header">
                                    <div class="plan-info">
                                        <h3>Loading...</h3>
                                        <p>Fetching subscription details...</p>
                                    </div>
                                    <div class="plan-price">
                                        <span class="price">LKR 0</span>
                                        <span class="period">/month</span>
                                    </div>
                                </div>
                                <div class="plan-features">
                                    <!-- Features will be populated dynamically -->
                                </div>
                                <div class="plan-actions">
                                    <button class="btn-secondary">Change Plan</button>
                                    <button class="action-btn danger">Cancel Subscription</button>
                                </div>
                            </div>

                            <div class="billing-info">
                                <div class="info-item">
                                    <span class="label">Billing Period:</span>
                                    <span class="value">Loading...</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Next Billing Date:</span>
                                    <span class="value">Loading...</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Payment Method:</span>
                                    <span class="value">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Payment Methods</h2>

                            <!-- Payment methods will be populated dynamically by JavaScript -->
                            <div id="payment-methods-container">
                                <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                                    <i class="fas fa-spinner fa-spin"></i> Loading payment methods...
                                </p>
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
                                        <!-- Billing history will be populated dynamically by JavaScript -->
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                                                <i class="fas fa-spinner fa-spin"></i> Loading billing history...
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

    <!-- Add Payment Method Modal -->
    <div class="modal" id="addPaymentModal">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-credit-card"></i> Add Payment Method</h3>
                <button class="modal-close" onclick="closeAddCardModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addPaymentForm">
                <div class="form-group">
                    <label for="cardType">Card Type <span class="required">*</span></label>
                    <select id="cardType" name="card_type" required>
                        <option value="">Select card type</option>
                        <option value="visa">Visa</option>
                        <option value="mastercard">Mastercard</option>
                        <option value="amex">American Express</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cardNumber">Card Number <span class="required">*</span></label>
                    <input type="text" id="cardNumber" name="card_number" 
                           placeholder="1234 5678 9012 3456" 
                           maxlength="19" required>
                    <small class="form-hint">Only the last 4 digits will be stored</small>
                </div>

                <div class="form-group">
                    <label for="cardHolder">Cardholder Name <span class="required">*</span></label>
                    <input type="text" id="cardHolder" name="card_holder_name" 
                           placeholder="John Doe" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="expiryMonth">Expiry Month <span class="required">*</span></label>
                        <select id="expiryMonth" name="expiry_month" required>
                            <option value="">MM</option>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expiryYear">Expiry Year <span class="required">*</span></label>
                        <select id="expiryYear" name="expiry_year" required>
                            <option value="">YYYY</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV <span class="required">*</span></label>
                    <input type="text" id="cvv" name="cvv" 
                           placeholder="123" maxlength="4" required>
                    <small class="form-hint">3 or 4 digits on the back of your card</small>
                </div>

                <div class="form-group">
                    <label for="billingAddress">Billing Address <span class="required">*</span></label>
                    <textarea id="billingAddress" name="billing_address" 
                              rows="3" placeholder="Enter your billing address" required></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <label>
                        <input type="checkbox" id="makePrimary" name="make_primary">
                        <span>Set as primary payment method</span>
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeAddCardModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">
                        <i class="fas fa-plus"></i> Add Card
                    </button>
                </div>
            </form>
        </div>
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
    <!-- Load Sidebar and Topbar Components -->
    <script>
        // Store original settings for change detection
        let originalSettings = {};
        let hasUnsavedChanges = false;

        document.addEventListener('DOMContentLoaded', () => {
            // Tab Logic
            const tabBtns = document.querySelectorAll('.tab-btn');
            const setActiveTab = (tabId) => {
                if (!tabId) return;
                tabBtns.forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                const btn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
                const content = document.getElementById(tabId);
                if (btn) btn.classList.add('active');
                if (content) content.classList.add('active');
            };

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    btn.classList.add('active');
                    const tabId = btn.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Optional deep-link: settings.php?tab=billing or settings.php#billing-tab
            try {
                const params = new URLSearchParams(window.location.search);
                const tabParam = (params.get('tab') || '').trim();
                const hash = (window.location.hash || '').replace('#', '').trim();
                let requested = '';
                if (tabParam) {
                    requested = tabParam.endsWith('-tab') ? tabParam : (tabParam + '-tab');
                    if (tabParam === 'billing') requested = 'billing-tab';
                    if (tabParam === 'security') requested = 'security-tab';
                    if (tabParam === 'notifications') requested = 'notifications-tab';
                } else if (hash) {
                    requested = hash;
                    if (hash === 'billing') requested = 'billing-tab';
                }
                if (requested) {
                    setActiveTab(requested);
                }
            } catch (e) {
                // Ignore URL parsing issues
            }

            // Fetch Settings Data
            fetchSettings();
            
            // Save Button Listener
            document.getElementById('saveNotificationsBtn').addEventListener('click', saveSettings);
            
            // Allow top save button to do the same
            document.getElementById('saveAllBtn').addEventListener('click', saveSettings);
            
            // Cancel Button Listener
            document.getElementById('cancelNotificationsBtn').addEventListener('click', cancelChanges);
            
            // Initialize change detection
            initializeChangeDetection();
        });

        async function fetchSettings() {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php');
                const result = await response.json();

                if (result.success) {
                    populateSettings(result.data);
                } else {
                    console.error('Failed to load settings:', result.message);
                }
            } catch (error) {
                console.error('Error loading settings:', error);
            }
        }

        function populateSettings(data) {
            // 1. Populate Toggles (Notifications)
            // Note: DB uses snake_case keys (email_repair_requests) vs camelCase in previous mock
            if (data.settings) {
                const n = data.settings;
                // Helper to check if value is true/1 vs false/0
                const isTrue = (val) => val == 1 || val === true;

                setSwitch('emailRepairRequests', isTrue(n.email_repair_requests));
                setSwitch('emailProjectUpdates', isTrue(n.email_project_updates));
                setSwitch('emailPayments', isTrue(n.email_payments));
                setSwitch('emailTeamActivity', isTrue(n.email_team_activity));
                setSwitch('emailMessages', isTrue(n.email_messages));
                setSwitch('pushDesktop', isTrue(n.push_desktop));
                setSwitch('pushMobile', isTrue(n.push_mobile));
                
                // Populate Quiet Hours
                if (n.quiet_hours_start) {
                    document.getElementById('quietHoursStart').value = n.quiet_hours_start.substring(0, 5);
                }
                if (n.quiet_hours_end) {
                    document.getElementById('quietHoursEnd').value = n.quiet_hours_end.substring(0, 5);
                }
                
                // Store original settings for change detection
                storeOriginalSettings();
            }

            // 2. Populate Billing History
            if (data.billing && Array.isArray(data.billing)) {
                const tbody = document.querySelector('.billing-history tbody');
                if(tbody) {
                    if (data.billing.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No billing history found.</td></tr>';
                    } else {
                        tbody.innerHTML = data.billing.map(invoice => `
                            <tr>
                                <td>${invoice.invoice_id}</td>
                                <td>${new Date(invoice.date).toLocaleDateString()}</td>
                                <td>${invoice.amount}</td>
                                <td><span class="status-badge ${invoice.status}">${invoice.status}</span></td>
                                <td>
                                    <button class="btn-icon" title="Download"><i class="fas fa-download"></i></button>
                                    <button class="btn-icon" title="View"><i class="fas fa-eye"></i></button>
                                </td>
                            </tr>
                        `).join('');
                    }
                }
            }

            // 3. Populate Login History
            if (data.history && Array.isArray(data.history)) {
                const historyContainer = document.querySelector('.login-history');
                if(historyContainer) {
                    if (data.history.length === 0) {
                        historyContainer.innerHTML = '<p class="text-muted">No recent activity found.</p>';
                    } else {
                        historyContainer.innerHTML = data.history.map(log => {
                            const isSuccess = log.action_type && !log.action_type.includes('fail');
                            return `
                            <div class="history-item">
                                <div class="history-icon ${isSuccess ? 'success' : 'failed'}">
                                    <i class="fas ${isSuccess ? 'fa-check' : 'fa-times'}"></i>
                                </div>
                                <div class="history-info">
                                    <h4>${log.action_type || 'Activity'}</h4>
                                    <p>${log.ip_address || 'Unknown IP'} • ${log.description || 'System Action'}</p>
                                    <small>${new Date(log.timestamp).toLocaleString()}</small>
                                </div>
                            </div>
                            `;
                        }).join('');
                    }
                }
            }
        }

        function setSwitch(id, value) {
            const el = document.getElementById(id);
            if (el) el.checked = value;
        }

        function storeOriginalSettings() {
            originalSettings = {
                emailRepairRequests: document.getElementById('emailRepairRequests').checked,
                emailProjectUpdates: document.getElementById('emailProjectUpdates').checked,
                emailPayments: document.getElementById('emailPayments').checked,
                emailTeamActivity: document.getElementById('emailTeamActivity').checked,
                emailMessages: document.getElementById('emailMessages').checked,
                pushDesktop: document.getElementById('pushDesktop').checked,
                pushMobile: document.getElementById('pushMobile').checked,
                quietHoursStart: document.getElementById('quietHoursStart').value,
                quietHoursEnd: document.getElementById('quietHoursEnd').value
            };
            
            // Clear all changed highlights
            document.querySelectorAll('.notification-item.changed').forEach(item => {
                item.classList.remove('changed');
            });
            
            // Disable save buttons initially
            updateSaveButtonState(false);
            hasUnsavedChanges = false;
        }

        function checkForChanges() {
            const hasChanges = 
                originalSettings.emailRepairRequests !== document.getElementById('emailRepairRequests').checked ||
                originalSettings.emailProjectUpdates !== document.getElementById('emailProjectUpdates').checked ||
                originalSettings.emailPayments !== document.getElementById('emailPayments').checked ||
                originalSettings.emailTeamActivity !== document.getElementById('emailTeamActivity').checked ||
                originalSettings.emailMessages !== document.getElementById('emailMessages').checked ||
                originalSettings.pushDesktop !== document.getElementById('pushDesktop').checked ||
                originalSettings.pushMobile !== document.getElementById('pushMobile').checked ||
                originalSettings.quietHoursStart !== document.getElementById('quietHoursStart').value ||
                originalSettings.quietHoursEnd !== document.getElementById('quietHoursEnd').value;
            
            hasUnsavedChanges = hasChanges;
            return hasChanges;
        }

        function updateSaveButtonState(enabled) {
            const saveBtn = document.getElementById('saveNotificationsBtn');
            const saveAllBtn = document.getElementById('saveAllBtn');
            if (saveBtn) saveBtn.disabled = !enabled;
            if (saveAllBtn) saveAllBtn.disabled = !enabled;
        }

        function initializeChangeDetection() {
            const toggleIds = [
                'emailRepairRequests', 'emailProjectUpdates', 'emailPayments',
                'emailTeamActivity', 'emailMessages', 'pushDesktop', 'pushMobile'
            ];
            
            // Add change listeners to all toggles
            toggleIds.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', (e) => {
                        // Highlight changed item
                        const item = e.target.closest('.notification-item');
                        if (item) {
                            const originalValue = originalSettings[id];
                            const currentValue = e.target.checked;
                            if (originalValue !== currentValue) {
                                item.classList.add('changed');
                            } else {
                                item.classList.remove('changed');
                            }
                        }
                        
                        const hasChanges = checkForChanges();
                        updateSaveButtonState(hasChanges);
                    });
                }
            });
            
            // Add change listeners to time inputs
            const quietHoursStart = document.getElementById('quietHoursStart');
            const quietHoursEnd = document.getElementById('quietHoursEnd');
            if (quietHoursStart) {
                quietHoursStart.addEventListener('change', () => {
                    const hasChanges = checkForChanges();
                    updateSaveButtonState(hasChanges);
                });
            }
            if (quietHoursEnd) {
                quietHoursEnd.addEventListener('change', () => {
                    const hasChanges = checkForChanges();
                    updateSaveButtonState(hasChanges);
                });
            }
        }

        function cancelChanges() {
            if (hasUnsavedChanges) {
                if (confirm('Are you sure you want to discard your changes?')) {
                    fetchSettings();
                    showToast('Changes discarded', 'success');
                }
            } else {
                showToast('No changes to discard', 'success');
            }
        }

        // Warn user about unsaved changes when leaving page
        window.addEventListener('beforeunload', (e) => {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        async function saveSettings() {
            const saveBtn = document.getElementById('saveNotificationsBtn');
            const saveAllBtn = document.getElementById('saveAllBtn');
            const originalText = saveBtn.innerHTML;
            
            // Disable buttons and show loading
            saveBtn.disabled = true;
            saveAllBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            
            const settings = {
                emailRepairRequests: document.getElementById('emailRepairRequests').checked,
                emailProjectUpdates: document.getElementById('emailProjectUpdates').checked,
                emailPayments: document.getElementById('emailPayments').checked,
                emailTeamActivity: document.getElementById('emailTeamActivity').checked,
                emailMessages: document.getElementById('emailMessages').checked,
                pushDesktop: document.getElementById('pushDesktop').checked,
                pushMobile: document.getElementById('pushMobile').checked,
                quietHoursStart: document.getElementById('quietHoursStart').value,
                quietHoursEnd: document.getElementById('quietHoursEnd').value
            };

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'update_notifications',
                        settings: settings
                    })
                });
                const result = await response.json();
                
                if (result.success) {
                    hasUnsavedChanges = false;
                    storeOriginalSettings();
                    showToast('Settings saved successfully', 'success');
                } else {
                    showToast('Failed to save settings: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error saving settings:', error);
                showToast('Network error occurred', 'error');
            } finally {
                // Re-enable buttons and restore text
                saveBtn.disabled = false;
                saveAllBtn.disabled = false;
                saveBtn.innerHTML = originalText;
                saveAllBtn.innerHTML = '<i class="fas fa-save"></i> Save All Changes';
                
                // Update button state based on changes
                if (!hasUnsavedChanges) {
                    updateSaveButtonState(false);
                }
            }
        }

        function showToast(message, type) {
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

        // ==================== SESSION MANAGEMENT ====================
        
        async function fetchActiveSessions() {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_sessions' })
                });
                const result = await response.json();

                if (result.success) {
                    displaySessions(result.data, result.current_session_id, result.count);
                } else {
                    document.getElementById('sessions-list').innerHTML = 
                        '<p class="error-message">Failed to load sessions</p>';
                }
            } catch (error) {
                console.error('Error loading sessions:', error);
                document.getElementById('sessions-list').innerHTML = 
                    '<p class="error-message">Network error while loading sessions</p>';
            }
        }

        function displaySessions(sessions, currentSessionId, totalCount) {
            const container = document.getElementById('sessions-list');
            const countBadge = document.getElementById('sessionCountBadge');
            
            // Update count badge
            countBadge.textContent = totalCount + (totalCount === 1 ? ' session' : ' sessions');
            countBadge.style.backgroundColor = '#0ABAB5';
            countBadge.style.color = 'white';
            countBadge.style.padding = '4px 12px';
            countBadge.style.borderRadius = '12px';
            countBadge.style.fontSize = '13px';
            countBadge.style.fontWeight = '600';
            countBadge.style.marginLeft = '10px';
            
            if (!sessions || sessions.length === 0) {
                container.innerHTML = '<p class="no-data">No active sessions found</p>';
                return;
            }
            
            let html = '';
            
            sessions.forEach(session => {
                const isCurrent = session.session_id === currentSessionId || session.is_current == 1;
                const timeAgo = getTimeAgo(session.last_activity);
                const deviceIcon = getDeviceIcon(session.device_type);
                
                html += `
                <div class="session-item ${isCurrent ? 'current' : ''}">
                    <div class="session-icon">
                        <i class="${deviceIcon}"></i>
                    </div>
                    <div class="session-info">
                        <h3>${session.os} - ${session.browser}</h3>
                        <p>${session.ip_address || 'Unknown IP'}${isCurrent ? ' • Current session' : ''}</p>
                        <small>Last active: ${timeAgo}</small>
                    </div>
                    ${isCurrent ? 
                        '<span class="session-badge">Current</span>' : 
                        `<button class="btn-revoke" onclick="revokeSession('${session.session_id}')">Revoke</button>`
                    }
                </div>`;
            });
            
            // Add "Revoke All" button if more than 1 session
            if (sessions.length > 1) {
                html += `
                <button class="action-btn danger" onclick="revokeAllSessions()" style="margin-top: 20px;">
                    <i class="fas fa-times-circle"></i> Revoke All Other Sessions
                </button>`;
            }
            
            container.innerHTML = html;
        }

        function getDeviceIcon(deviceType) {
            switch(deviceType) {
                case 'Mobile':
                    return 'fas fa-mobile-alt';
                case 'Tablet':
                    return 'fas fa-tablet-alt';
                case 'Desktop':
                default:
                    return 'fas fa-laptop';
            }
        }

        function getTimeAgo(timestamp) {
            const now = new Date();
            const then = new Date(timestamp);
            const diffMs = now - then;
            const diffMins = Math.floor(diffMs / 60000);
            
            if (diffMins < 1) return 'Just now';
            if (diffMins < 60) return `${diffMins} minute${diffMins > 1 ? 's' : ''} ago`;
            
            const diffHours = Math.floor(diffMins / 60);
            if (diffHours < 24) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            
            const diffDays = Math.floor(diffHours / 24);
            if (diffDays === 1) return 'Yesterday';
            if (diffDays < 7) return `${diffDays} days ago`;
            if (diffDays < 30) return `${Math.floor(diffDays / 7)} week${diffDays >= 14 ? 's' : ''} ago`;
            return `${Math.floor(diffDays / 30)} month${diffDays >= 60 ? 's' : ''} ago`;
        }

        async function revokeSession(sessionId) {
            if (!confirm('Are you sure you want to revoke this session? The user will be logged out immediately.')) {
                return;
            }
            
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'revoke_session',
                        session_id: sessionId
                    })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast('Session revoked successfully', 'success');
                    fetchActiveSessions(); // Refresh list
                } else {
                    showToast('Failed to revoke session: ' + (result.message || 'Unknown error'), 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            }
        }

        async function revokeAllSessions() {
            if (!confirm('Are you sure you want to revoke all other sessions? All other users will be logged out immediately. You will remain logged in on this device.')) {
                return;
            }
            
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'revoke_all_sessions' })
                });
                const result = await response.json();
                
                if (result.success) {
                    showToast(`All other sessions revoked (${result.remaining_sessions} session remaining)`, 'success');
                    fetchActiveSessions(); // Refresh list
                } else {
                    showToast('Failed to revoke sessions', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Network error occurred', 'error');
            }
        }

        // Load sessions when Security tab is clicked
        document.addEventListener('DOMContentLoaded', () => {
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const tabId = btn.getAttribute('data-tab');
                    if (tabId === 'security-tab') {
                        // Load sessions when security tab is opened
                        fetchActiveSessions();
                    }
                });
            });
        });
            
        // Close toast button
        document.querySelector('.toast-close').addEventListener('click', () => {
            document.getElementById('toast').style.display = 'none';
        });
    </script>

    <!-- ==================== BILLING TAB JAVASCRIPT ==================== -->
    <script>
        // Plan configurations
        const PLANS = {
            free: {
                name: 'Free',
                price: 0,
                features: ['5 requests/month', '1 team member', 'Email support', 'Basic analytics']
            },
            basic: {
                name: 'Basic',
                price: 2500,
                features: ['50 requests/month', '3 team members', 'Chat support', 'Standard analytics']
            },
            professional: {
                name: 'Professional',
                price: 5000,
                features: ['Unlimited requests', '10 team members', 'Priority support', 'Advanced analytics']
            },
            enterprise: {
                name: 'Enterprise',
                price: 10000,
                features: ['Everything + Custom features', 'Unlimited team', '24/7 support', 'API access']
            }
        };

        // Load billing data when billing tab is clicked
        document.querySelector('[data-tab="billing-tab"]').addEventListener('click', function() {
            if (!this.dataset.loaded) {
                loadBillingData();
                this.dataset.loaded = 'true';
            }
        });

        // If opened via deep-link to billing, load immediately
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const params = new URLSearchParams(window.location.search);
                const tabParam = (params.get('tab') || '').trim();
                const hash = (window.location.hash || '').replace('#', '').trim();
                const wantsBilling = tabParam === 'billing' || tabParam === 'billing-tab' || hash === 'billing' || hash === 'billing-tab';
                if (!wantsBilling) return;

                const billingBtn = document.querySelector('[data-tab="billing-tab"]');
                if (billingBtn && !billingBtn.dataset.loaded) {
                    loadBillingData();
                    billingBtn.dataset.loaded = 'true';
                }
            } catch (e) {
                // Ignore URL parsing issues
            }
        });

        // Load all billing data from backend
        async function loadBillingData() {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_billing_data' })
                });

                const result = await response.json();
                if (result.success) {
                    populateBillingData(result.data);
                } else {
                    showToast(result.message || 'Failed to load billing data', 'error');
                }
            } catch (error) {
                console.error('Error loading billing data:', error);
                showToast('Error loading billing data', 'error');
            }
        }

        // Populate UI with billing data
        function populateBillingData(data) {
            // Update subscription plan
            if (data.subscription) {
                const sub = data.subscription;
                const plan = PLANS[sub.plan_name] || PLANS.free;
                
                const planInfoH3 = document.querySelector('.plan-info h3');
                const planInfoP = document.querySelector('.plan-info p');
                const planPrice = document.querySelector('.plan-price .price');
                const planPeriod = document.querySelector('.plan-price .period');
                
                if (planInfoH3) planInfoH3.textContent = plan.name + ' Plan';
                if (planInfoP) planInfoP.textContent = 'Perfect for ' + (sub.plan_name === 'enterprise' ? 'large' : sub.plan_name === 'professional' ? 'growing' : 'small') + ' businesses';
                if (planPrice) planPrice.textContent = 'LKR ' + parseFloat(sub.plan_price).toLocaleString();
                if (planPeriod) planPeriod.textContent = '/' + sub.billing_period;
                
                // Update plan features
                const featuresContainer = document.querySelector('.plan-features');
                if (featuresContainer) {
                    featuresContainer.innerHTML = plan.features.map(feature => `
                        <div class="feature-item">
                            <i class="fas fa-check"></i>
                            <span>${feature}</span>
                        </div>
                    `).join('');
                }
                
                // Update billing info
                const billingPeriodValue = document.querySelector('.billing-info .info-item:nth-child(1) .value');
                const nextBillingValue = document.querySelector('.billing-info .info-item:nth-child(2) .value');
                
                if (billingPeriodValue) {
                    billingPeriodValue.textContent = sub.billing_period.charAt(0).toUpperCase() + sub.billing_period.slice(1);
                }
                if (nextBillingValue) {
                    nextBillingValue.textContent = formatDate(sub.next_billing_date);
                }
            }

            // Update payment methods
            if (data.payment_methods) {
                displayPaymentMethods(data.payment_methods);
            } else {
                displayPaymentMethods([]);
            }

            // Update billing history
            if (data.billing_history) {
                displayBillingHistory(data.billing_history);
            } else {
                displayBillingHistory([]);
            }
        }

        // Display payment methods
        function displayPaymentMethods(methods) {
            const container = document.getElementById('payment-methods-container');
            const addButton = document.getElementById('addCardBtn');
            
            if (!methods || methods.length === 0) {
                container.innerHTML = `
                    <p style="text-align: center; color: var(--text-secondary); padding: 20px;">
                        <i class="fas fa-credit-card"></i><br>
                        No payment methods added yet
                    </p>
                `;
                return;
            }
            
            // Clear loading message and display cards
            container.innerHTML = '';
            
            methods.forEach(method => {
                const cardHtml = `
                    <div class="payment-method-card ${method.is_primary ? 'active' : ''}" data-id="${method.payment_method_id}">
                        <div class="card-icon">
                            <i class="fab fa-cc-${method.card_type}"></i>
                        </div>
                        <div class="card-info">
                            <h4>${method.card_type.charAt(0).toUpperCase() + method.card_type.slice(1)} ending in ${method.last_four_digits}</h4>
                            <p>Expires ${method.expiry_month}/${method.expiry_year}</p>
                        </div>
                        ${method.is_primary ? 
                            '<div class="card-badge"><span class="badge-primary">Primary</span></div>' : 
                            `<button class="btn-set-primary" onclick="setPrimaryPayment(${method.payment_method_id})">Set as Primary</button>`
                        }
                        <button class="btn-icon" title="Remove" onclick="removePaymentMethod(${method.payment_method_id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', cardHtml);
            });
            
            // Update primary payment in billing info
            const primary = methods.find(m => m.is_primary);
            if (primary) {
                const paymentMethodValue = document.querySelector('.billing-info .info-item:nth-child(3) .value');
                if (paymentMethodValue) {
                    paymentMethodValue.textContent = 
                        `•••• ${primary.last_four_digits} (${primary.card_type.charAt(0).toUpperCase() + primary.card_type.slice(1)})`;
                }
            }
        }

        // Display billing history
        function displayBillingHistory(history) {
            const tbody = document.querySelector('.history-table tbody');
            
            if (!history || history.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--text-secondary);">
                            <i class="fas fa-receipt"></i><br>
                            No billing history available
                        </td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = history.map(invoice => `
                <tr>
                    <td>${invoice.invoice_id}</td>
                    <td>${formatDate(invoice.date)}</td>
                    <td>LKR ${parseFloat(invoice.amount).toLocaleString()}</td>
                    <td><span class="status-badge ${invoice.status}">${invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1)}</span></td>
                    <td>
                        <button class="btn-icon" title="Download" onclick="downloadInvoice('${invoice.invoice_id}')">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-icon" title="View" onclick="viewInvoice('${invoice.invoice_id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Set primary payment method
        async function setPrimaryPayment(paymentMethodId) {
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'set_primary_payment',
                        payment_method_id: paymentMethodId
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showToast('Primary payment method updated', 'success');
                    loadBillingData(); // Reload to update UI
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error setting primary payment:', error);
                showToast('Failed to update primary payment method', 'error');
            }
        }

        // Remove payment method
        async function removePaymentMethod(paymentMethodId) {
            if (!confirm('Are you sure you want to remove this payment method?')) {
                return;
            }

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'remove_payment_method',
                        payment_method_id: paymentMethodId
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showToast('Payment method removed', 'success');
                    loadBillingData(); // Reload to update UI
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error removing payment method:', error);
                showToast('Failed to remove payment method', 'error');
            }
        }

        // Download invoice
        function downloadInvoice(invoiceId) {
            // In real implementation, this would trigger PDF download
            showToast('Invoice download feature coming soon', 'info');
            console.log('Download invoice:', invoiceId);
        }

        // View invoice
        function viewInvoice(invoiceId) {
            // In real implementation, this would open invoice modal
            showToast('Invoice view feature coming soon', 'info');
            console.log('View invoice:', invoiceId);
        }

        // Format date helper
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        // Change Plan button handler
        document.querySelector('.btn-secondary').addEventListener('click', function() {
            showToast('Plan change feature coming soon', 'info');
            // TODO: Open modal to select new plan
        });

        // Cancel Subscription button handler
        document.querySelector('.action-btn.danger').addEventListener('click', async function() {
            if (!confirm('Are you sure you want to cancel your subscription? You will lose access to premium features.')) {
                return;
            }

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'cancel_subscription' })
                });

                const result = await response.json();
                if (result.success) {
                    showToast('Subscription cancelled successfully', 'success');
                    loadBillingData(); // Reload to update UI
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Error cancelling subscription:', error);
                showToast('Failed to cancel subscription', 'error');
            }
        });

        // Add Payment Method Modal Functions
        function openAddCardModal() {
            const modal = document.getElementById('addPaymentModal');
            const form = document.getElementById('addPaymentForm');
            
            // Populate year dropdown with next 15 years
            const yearSelect = document.getElementById('expiryYear');
            const currentYear = new Date().getFullYear();
            yearSelect.innerHTML = '<option value="">YYYY</option>';
            for (let i = 0; i < 15; i++) {
                const year = currentYear + i;
                yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
            }
            
            // Reset form
            form.reset();
            
            // Show modal
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeAddCardModal() {
            const modal = document.getElementById('addPaymentModal');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close modal when clicking backdrop
        document.getElementById('addPaymentModal').addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('modal-backdrop')) {
                closeAddCardModal();
            }
        });

        // Format card number with spaces
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Only allow numbers in card number and CVV
        document.getElementById('cardNumber').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
                e.preventDefault();
            }
        });

        document.getElementById('cvv').addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace') {
                e.preventDefault();
            }
        });

        // Handle form submission
        document.getElementById('addPaymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitPaymentBtn');
            const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
            const cardType = document.getElementById('cardType').value;
            const cardHolder = document.getElementById('cardHolder').value.trim();
            const expiryMonth = document.getElementById('expiryMonth').value;
            const expiryYear = document.getElementById('expiryYear').value;
            const cvv = document.getElementById('cvv').value;
            const billingAddress = document.getElementById('billingAddress').value.trim();
            const makePrimary = document.getElementById('makePrimary').checked;

            // Validation
            if (!cardType) {
                showToast('Please select a card type', 'error');
                return;
            }

            if (cardNumber.length < 13 || cardNumber.length > 19) {
                showToast('Please enter a valid card number', 'error');
                return;
            }

            if (!cardHolder) {
                showToast('Please enter cardholder name', 'error');
                return;
            }

            if (!expiryMonth || !expiryYear) {
                showToast('Please select expiry date', 'error');
                return;
            }

            // Check if card is expired
            const currentDate = new Date();
            const expiryDate = new Date(parseInt(expiryYear), parseInt(expiryMonth) - 1);
            if (expiryDate < currentDate) {
                showToast('Card has expired', 'error');
                return;
            }

            if (cvv.length < 3 || cvv.length > 4) {
                showToast('Please enter a valid CVV', 'error');
                return;
            }

            if (!billingAddress) {
                showToast('Please enter billing address', 'error');
                return;
            }

            // Extract last 4 digits
            const lastFourDigits = cardNumber.slice(-4);

            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;

            try {
                const response = await fetch('../../api/settings.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'add_payment_method',
                        payment_method: {
                            card_type: cardType,
                            last_four_digits: lastFourDigits,
                            card_holder_name: cardHolder,
                            expiry_month: expiryMonth,
                            expiry_year: expiryYear,
                            billing_address: billingAddress,
                            is_primary: makePrimary ? 1 : 0
                        }
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Payment method added successfully', 'success');
                    closeAddCardModal();
                    // Reload billing data to show new card
                    await loadBillingData();
                } else {
                    showToast(data.message || 'Failed to add payment method', 'error');
                }
            } catch (error) {
                console.error('Error adding payment method:', error);
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
            }
        });

        // Add Payment Method button handler
        document.getElementById('addCardBtn').addEventListener('click', openAddCardModal);
    </script>

    <!-- Set active sidebar item for Settings page -->
    <script>
        // Set Settings as active page in localStorage
        localStorage.setItem('activePage', 'Settings');
    </script>
</body>

</html>






