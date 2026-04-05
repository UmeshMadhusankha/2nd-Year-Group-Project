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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <section class="settings-page">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-cog"></i> Settings</h1>
                                <p class="subtitle">Manage notifications and account security</p>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="btn-save-all" id="saveAllBtn" type="button">
                                        <i class="fas fa-save"></i> Save All Changes
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Settings</span>
                        </div>
                    </div>
                </header>

                <!-- Settings Content -->
                <div class="settings-container">
                <!-- Settings Navigation Tabs -->
                <div class="settings-tabs">
                    <button class="tab-btn active" data-tab="notifications-tab" type="button">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </button>
                    <button class="tab-btn" data-tab="security-tab" type="button">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security</span>
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
                                    <h3>Repair Requests</h3>
                                    <p>Get notified when you receive new repair requests</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailRepairRequests">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Project Updates</h3>
                                    <p>Updates about your ongoing projects and milestones</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailProjectUpdates">
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="notification-item">
                                <div class="notification-info">
                                    <h3>Payments</h3>
                                    <p>Notifications for payment updates and invoices</p>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" id="emailPayments">
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
                                    <input type="checkbox" id="emailMessages">
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
                                    <input type="checkbox" id="pushDesktop">
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
                            <button class="btn-secondary" id="cancelNotificationsBtn" type="button">Cancel</button>
                            <button class="btn-primary" id="saveNotificationsBtn" type="button">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                        </div>
                    </div>

                    <!-- Security Tab -->
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

                            <button class="btn-secondary" id="viewFullHistoryBtn" type="button">View Full History</button>
                        </div>

                        <div class="settings-section">
                            <h2 class="section-title">Change Password</h2>
                            <p class="section-description">Ensure your account stays secure by using a strong password</p>

                            <div class="form-group">
                                <label for="currentPassword">Current Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="currentPassword" placeholder="Enter current password" required>
                                    <button class="toggle-password" type="button" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="newPassword">New Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="newPassword" placeholder="Enter new password" required>
                                    <button class="toggle-password" type="button" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <span class="strength-text">Password strength: <span id="strengthLevel">-</span></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword">Confirm New Password <span class="required">*</span></label>
                                <div class="password-input">
                                    <input type="password" id="confirmPassword" placeholder="Confirm new password" required>
                                    <button class="toggle-password" type="button" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="password-requirements">
                                <h4>Password Requirements:</h4>
                                <ul>
                                    <li id="req-length"><i class="fas fa-times-circle"></i> At least 8 characters</li>
                                    <li id="req-uppercase"><i class="fas fa-times-circle"></i> One uppercase letter</li>
                                    <li id="req-lowercase"><i class="fas fa-times-circle"></i> One lowercase letter</li>
                                    <li id="req-number"><i class="fas fa-times-circle"></i> One number</li>
                                    <li id="req-special"><i class="fas fa-times-circle"></i> One special character</li>
                                </ul>
                            </div>

                            <div class="form-actions">
                                <button class="btn-secondary" type="button" id="cancelPasswordBtn">Cancel</button>
                                <button class="btn-primary" type="button" id="savePasswordBtn">
                                    <i class="fas fa-lock"></i> Change Password
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
                </div>
            </section>
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
        <button class="toast-close" type="button">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Scripts -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>

    <script>
        // Store original settings for change detection
        (() => {
            const SETTINGS_API = '/2nd-Year-Group-Project/FixLanka/api/settings.php';
            const PROFILE_API = '/2nd-Year-Group-Project/FixLanka/api/company-profile.php';

            const qs = (sel, root = document) => root.querySelector(sel);
            const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

            let originalSettings = null;
            let hasUnsavedChanges = false;

            function showToast(message, type = 'success') {
                const toast = qs('#toast');
                if (!toast) return;

                const icon = qs('.toast-icon', toast);
                const title = qs('h4', toast);
                const text = qs('p', toast);

                toast.style.display = 'flex';
                if (text) text.textContent = message;

                const isSuccess = type === 'success';
                if (icon) {
                    icon.className = `toast-icon ${isSuccess ? 'success' : 'error'}`;
                    icon.innerHTML = isSuccess
                        ? '<i class="fas fa-check-circle"></i>'
                        : '<i class="fas fa-times-circle"></i>';
                }
                if (title) title.textContent = isSuccess ? 'Success!' : 'Error';

                window.clearTimeout(showToast._t);
                showToast._t = window.setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            }

            function setActiveTab(tabId) {
                if (!tabId) return;
                qsa('.tab-btn').forEach(b => b.classList.remove('active'));
                qsa('.tab-content').forEach(c => c.classList.remove('active'));

                const btn = qs(`.tab-btn[data-tab="${tabId}"]`);
                const content = qs(`#${tabId}`);
                if (btn) btn.classList.add('active');
                if (content) content.classList.add('active');
            }

            function getRequestedTab() {
                try {
                    const params = new URLSearchParams(window.location.search);
                    const tabParam = (params.get('tab') || '').trim();
                    const hash = (window.location.hash || '').replace('#', '').trim();

                    if (tabParam) {
                        if (tabParam === 'security') return 'security-tab';
                        if (tabParam === 'notifications') return 'notifications-tab';
                        return tabParam.endsWith('-tab') ? tabParam : `${tabParam}-tab`;
                    }

                    if (hash) {
                        return hash.endsWith('-tab') ? hash : `${hash}-tab`;
                    }
                } catch (e) {
                    // ignore
                }
                return '';
            }

            function isTrue(val) {
                return val === true || val === 1 || val === '1' || val === 'true';
            }

            function setSwitch(id, value) {
                const el = qs(`#${id}`);
                if (el) el.checked = Boolean(value);
            }

            function getCurrentSettings() {
                return {
                    emailRepairRequests: Boolean(qs('#emailRepairRequests')?.checked),
                    emailProjectUpdates: Boolean(qs('#emailProjectUpdates')?.checked),
                    emailPayments: Boolean(qs('#emailPayments')?.checked),
                    emailTeamActivity: Boolean(qs('#emailTeamActivity')?.checked),
                    emailMessages: Boolean(qs('#emailMessages')?.checked),
                    pushDesktop: Boolean(qs('#pushDesktop')?.checked),
                    pushMobile: Boolean(qs('#pushMobile')?.checked),
                    quietHoursStart: qs('#quietHoursStart')?.value || '',
                    quietHoursEnd: qs('#quietHoursEnd')?.value || '',
                };
            }

            function updateSaveButtonState(enabled) {
                const saveBtn = qs('#saveNotificationsBtn');
                const saveAllBtn = qs('#saveAllBtn');
                if (saveBtn) saveBtn.disabled = !enabled;
                if (saveAllBtn) saveAllBtn.disabled = !enabled;
            }

            function storeOriginalSettings() {
                originalSettings = getCurrentSettings();
                hasUnsavedChanges = false;
                updateSaveButtonState(false);
                qsa('.notification-item.changed').forEach(item => item.classList.remove('changed'));
            }

            function checkForChanges() {
                if (!originalSettings) return false;
                const current = getCurrentSettings();
                const hasChanges = Object.keys(originalSettings).some(k => originalSettings[k] !== current[k]);
                hasUnsavedChanges = hasChanges;
                return hasChanges;
            }

            async function fetchSettings() {
                try {
                    const response = await fetch(SETTINGS_API);
                    const result = await response.json();
                    if (!result.success) return;

                    const n = result.data?.settings || {};
                    setSwitch('emailRepairRequests', isTrue(n.email_repair_requests));
                    setSwitch('emailProjectUpdates', isTrue(n.email_project_updates));
                    setSwitch('emailPayments', isTrue(n.email_payments));
                    setSwitch('emailTeamActivity', isTrue(n.email_team_activity));
                    setSwitch('emailMessages', isTrue(n.email_messages));
                    setSwitch('pushDesktop', isTrue(n.push_desktop));
                    setSwitch('pushMobile', isTrue(n.push_mobile));

                    if (n.quiet_hours_start && qs('#quietHoursStart')) {
                        qs('#quietHoursStart').value = String(n.quiet_hours_start).substring(0, 5);
                    }
                    if (n.quiet_hours_end && qs('#quietHoursEnd')) {
                        qs('#quietHoursEnd').value = String(n.quiet_hours_end).substring(0, 5);
                    }

                    storeOriginalSettings();
                } catch (error) {
                    console.error('Error loading settings:', error);
                }
            }

            async function saveSettings() {
                const saveBtn = qs('#saveNotificationsBtn');
                const saveAllBtn = qs('#saveAllBtn');
                const originalSaveHtml = saveBtn ? saveBtn.innerHTML : '';
                const originalSaveAllHtml = saveAllBtn ? saveAllBtn.innerHTML : '';

                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                }
                if (saveAllBtn) {
                    saveAllBtn.disabled = true;
                    saveAllBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                }

                try {
                    const current = getCurrentSettings();
                    const response = await fetch(SETTINGS_API, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'update_notifications',
                            settings: current,
                        }),
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast(result.message || 'Settings saved successfully', 'success');
                        storeOriginalSettings();
                    } else {
                        showToast(result.message || 'Failed to save settings', 'error');
                    }
                } catch (error) {
                    console.error('Error saving settings:', error);
                    showToast('Network error occurred', 'error');
                } finally {
                    if (saveBtn) saveBtn.innerHTML = originalSaveHtml;
                    if (saveAllBtn) saveAllBtn.innerHTML = originalSaveAllHtml || '<i class="fas fa-save"></i> Save All Changes';
                    updateSaveButtonState(checkForChanges());
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

            function initializeChangeDetection() {
                const ids = [
                    'emailRepairRequests',
                    'emailProjectUpdates',
                    'emailPayments',
                    'emailTeamActivity',
                    'emailMessages',
                    'pushDesktop',
                    'pushMobile',
                ];

                ids.forEach(id => {
                    const el = qs(`#${id}`);
                    if (!el) return;
                    el.addEventListener('change', (e) => {
                        const item = e.target.closest('.notification-item');
                        if (item && originalSettings) {
                            item.classList.toggle('changed', originalSettings[id] !== e.target.checked);
                        }
                        updateSaveButtonState(checkForChanges());
                    });
                });

                ['quietHoursStart', 'quietHoursEnd'].forEach(id => {
                    const el = qs(`#${id}`);
                    if (!el) return;
                    el.addEventListener('change', () => {
                        updateSaveButtonState(checkForChanges());
                    });
                });
            }

            // ==================== SESSIONS ====================
            function escapeHtml(str) {
                return String(str || '').replace(/[&<>"]/g, (c) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                }[c]));
            }

            function getTimeAgo(dateString) {
                const d = new Date(dateString);
                if (Number.isNaN(d.getTime())) return 'Unknown';
                const diff = Date.now() - d.getTime();
                const mins = Math.floor(diff / 60000);
                if (mins < 1) return 'Just now';
                if (mins < 60) return `${mins} min ago`;
                const hours = Math.floor(mins / 60);
                if (hours < 24) return `${hours} hour${hours === 1 ? '' : 's'} ago`;
                const days = Math.floor(hours / 24);
                return `${days} day${days === 1 ? '' : 's'} ago`;
            }

            function getDeviceIcon(deviceType) {
                const t = String(deviceType || '').toLowerCase();
                if (t.includes('mobile') || t.includes('android') || t.includes('iphone')) return 'fas fa-mobile-alt';
                if (t.includes('tablet')) return 'fas fa-tablet-alt';
                return 'fas fa-desktop';
            }

            async function fetchActiveSessions() {
                const container = qs('#sessions-list');
                if (!container) return;

                try {
                    const response = await fetch(SETTINGS_API, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'get_sessions' }),
                    });
                    const result = await response.json();
                    if (!result.success) {
                        container.innerHTML = '<p class="error-message">Failed to load sessions</p>';
                        return;
                    }

                    displaySessions(result.data || [], result.current_session_id, result.count);
                } catch (error) {
                    console.error('Error loading sessions:', error);
                    container.innerHTML = '<p class="error-message">Network error while loading sessions</p>';
                }
            }

            function displaySessions(sessions, currentSessionId, totalCount) {
                const container = qs('#sessions-list');
                const countBadge = qs('#sessionCountBadge');
                if (!container) return;

                if (countBadge) {
                    const c = Number(totalCount || sessions.length || 0);
                    countBadge.textContent = `${c} ${c === 1 ? 'session' : 'sessions'}`;
                }

                if (!sessions || sessions.length === 0) {
                    container.innerHTML = '<p class="no-data">No active sessions found</p>';
                    return;
                }

                const itemsHtml = sessions.map(s => {
                    const isCurrent = s.session_id === currentSessionId || s.is_current == 1;
                    const icon = getDeviceIcon(s.device_type);
                    const timeAgo = getTimeAgo(s.last_activity);
                    const os = escapeHtml(s.os || 'Unknown OS');
                    const browser = escapeHtml(s.browser || 'Unknown browser');
                    const ip = escapeHtml(s.ip_address || 'Unknown IP');

                    return `
                        <div class="session-item ${isCurrent ? 'current' : ''}">
                            <div class="session-icon">
                                <i class="${icon}"></i>
                            </div>
                            <div class="session-info">
                                <h3>${os} - ${browser}</h3>
                                <p>${ip}${isCurrent ? ' • Current session' : ''}</p>
                                <small>Last active: ${timeAgo}</small>
                            </div>
                            ${isCurrent
                                ? '<span class="session-badge">Current</span>'
                                : `<button class="btn-revoke" type="button" data-session-id="${escapeHtml(s.session_id)}">Revoke</button>`
                            }
                        </div>
                    `;
                }).join('');

                const revokeAllHtml = sessions.length > 1
                    ? '<button class="btn-secondary" type="button" id="revokeAllSessionsBtn">Revoke All Other Sessions</button>'
                    : '';

                container.innerHTML = `${itemsHtml}${revokeAllHtml}`;

                qsa('.btn-revoke', container).forEach(btn => {
                    btn.addEventListener('click', () => revokeSession(btn.dataset.sessionId));
                });

                const revokeAllBtn = qs('#revokeAllSessionsBtn');
                if (revokeAllBtn) {
                    revokeAllBtn.addEventListener('click', revokeAllSessions);
                }
            }

            async function revokeSession(sessionId) {
                if (!sessionId) return;
                if (!confirm('Revoke this session? The user will be logged out on that device.')) return;

                try {
                    const response = await fetch(SETTINGS_API, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'revoke_session', session_id: sessionId }),
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast(result.message || 'Session revoked successfully', 'success');
                        fetchActiveSessions();
                    } else {
                        showToast(result.message || 'Failed to revoke session', 'error');
                    }
                } catch (error) {
                    console.error('Error revoking session:', error);
                    showToast('Network error occurred', 'error');
                }
            }

            async function revokeAllSessions() {
                if (!confirm('Are you sure you want to revoke all other sessions? You will remain logged in on this device.')) {
                    return;
                }

                try {
                    const response = await fetch(SETTINGS_API, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'revoke_all_sessions' }),
                    });
                    const result = await response.json();
                    if (result.success) {
                        showToast(result.message || 'All other sessions revoked successfully', 'success');
                        fetchActiveSessions();
                    } else {
                        showToast(result.message || 'Failed to revoke sessions', 'error');
                    }
                } catch (error) {
                    console.error('Error revoking sessions:', error);
                    showToast('Network error occurred', 'error');
                }
            }

            // ==================== PASSWORD CHANGE ====================
            function initPasswordChange() {
                const securityTab = qs('#security-tab');
                if (!securityTab) return;

                const currentPasswordEl = qs('#currentPassword', securityTab);
                const newPasswordEl = qs('#newPassword', securityTab);
                const confirmPasswordEl = qs('#confirmPassword', securityTab);
                const cancelBtn = qs('#cancelPasswordBtn', securityTab);
                const saveBtn = qs('#savePasswordBtn', securityTab);

                if (!currentPasswordEl || !newPasswordEl || !confirmPasswordEl || !cancelBtn || !saveBtn) return;

                const strengthFill = qs('#passwordStrength .strength-fill', securityTab);
                const strengthLevelEl = qs('#strengthLevel', securityTab);

                const requirementEls = {
                    length: qs('#req-length', securityTab),
                    uppercase: qs('#req-uppercase', securityTab),
                    lowercase: qs('#req-lowercase', securityTab),
                    number: qs('#req-number', securityTab),
                    special: qs('#req-special', securityTab),
                };

                const setRequirement = (el, met) => {
                    if (!el) return;
                    el.classList.toggle('met', met);
                    const icon = qs('i', el);
                    if (icon) icon.className = met ? 'fas fa-check-circle' : 'fas fa-times-circle';
                };

                const evaluatePassword = (password) => ({
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[^A-Za-z0-9]/.test(password),
                });

                const updateStrengthUI = () => {
                    const password = newPasswordEl.value || '';
                    const rules = evaluatePassword(password);

                    setRequirement(requirementEls.length, rules.length);
                    setRequirement(requirementEls.uppercase, rules.uppercase);
                    setRequirement(requirementEls.lowercase, rules.lowercase);
                    setRequirement(requirementEls.number, rules.number);
                    setRequirement(requirementEls.special, rules.special);

                    const metCount = Object.values(rules).filter(Boolean).length;
                    let strengthClass = '';
                    let strengthText = '-';
                    let width = '0%';

                    if (password.length === 0) {
                        strengthClass = '';
                        strengthText = '-';
                        width = '0%';
                    } else if (metCount <= 2) {
                        strengthClass = 'weak';
                        strengthText = 'Weak';
                        width = '33%';
                    } else if (metCount === 3 || metCount === 4) {
                        strengthClass = 'medium';
                        strengthText = 'Medium';
                        width = '66%';
                    } else {
                        strengthClass = 'strong';
                        strengthText = 'Strong';
                        width = '100%';
                    }

                    if (strengthFill) {
                        strengthFill.classList.remove('weak', 'medium', 'strong');
                        if (strengthClass) strengthFill.classList.add(strengthClass);
                        strengthFill.style.width = width;
                    }
                    if (strengthLevelEl) strengthLevelEl.textContent = strengthText;
                };

                const resetPasswordForm = () => {
                    currentPasswordEl.value = '';
                    newPasswordEl.value = '';
                    confirmPasswordEl.value = '';
                    qsa('.password-input input', securityTab).forEach(input => { input.type = 'password'; });
                    qsa('.toggle-password i', securityTab).forEach(icon => { icon.className = 'fas fa-eye'; });
                    updateStrengthUI();
                };

                newPasswordEl.addEventListener('input', updateStrengthUI);
                updateStrengthUI();

                qsa('.toggle-password', securityTab).forEach(btn => {
                    btn.addEventListener('click', () => {
                        const wrapper = btn.closest('.password-input');
                        const input = wrapper ? qs('input', wrapper) : null;
                        const icon = qs('i', btn);
                        if (!input || !icon) return;
                        const isHidden = input.type === 'password';
                        input.type = isHidden ? 'text' : 'password';
                        icon.className = isHidden ? 'fas fa-eye-slash' : 'fas fa-eye';
                    });
                });

                cancelBtn.addEventListener('click', () => {
                    resetPasswordForm();
                    showToast('Password change cancelled', 'success');
                });

                saveBtn.addEventListener('click', async () => {
                    const currentPassword = (currentPasswordEl.value || '').trim();
                    const newPassword = newPasswordEl.value || '';
                    const confirmPassword = confirmPasswordEl.value || '';

                    if (!currentPassword || !newPassword || !confirmPassword) {
                        showToast('Please fill in all password fields', 'error');
                        return;
                    }
                    if (newPassword !== confirmPassword) {
                        showToast('New passwords do not match', 'error');
                        return;
                    }

                    const rules = evaluatePassword(newPassword);
                    if (!Object.values(rules).every(Boolean)) {
                        showToast('Please meet all password requirements', 'error');
                        return;
                    }

                    const originalHtml = saveBtn.innerHTML;
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';

                    try {
                        const response = await fetch(PROFILE_API, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                action: 'change_password',
                                current_password: currentPassword,
                                new_password: newPassword,
                            }),
                        });
                        const result = await response.json();
                        if (result && result.success) {
                            showToast(result.message || 'Password updated successfully', 'success');
                            resetPasswordForm();
                        } else {
                            showToast((result && result.message) ? result.message : 'Failed to update password', 'error');
                        }
                    } catch (error) {
                        console.error('Error changing password:', error);
                        showToast('Network error occurred', 'error');
                    } finally {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = originalHtml;
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Tab logic
                qsa('.tab-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const tabId = btn.getAttribute('data-tab');
                        setActiveTab(tabId);
                        if (tabId === 'security-tab') {
                            fetchActiveSessions();
                        }
                    });
                });

                const requested = getRequestedTab();
                if (requested) {
                    setActiveTab(requested);
                    if (requested === 'security-tab') {
                        fetchActiveSessions();
                    }
                }

                // Toast close button
                const toastCloseBtn = qs('.toast-close');
                if (toastCloseBtn) {
                    toastCloseBtn.addEventListener('click', () => {
                        const toast = qs('#toast');
                        if (toast) toast.style.display = 'none';
                    });
                }

                // Notifications wiring
                qs('#saveNotificationsBtn')?.addEventListener('click', saveSettings);
                qs('#saveAllBtn')?.addEventListener('click', saveSettings);
                qs('#cancelNotificationsBtn')?.addEventListener('click', cancelChanges);
                initializeChangeDetection();
                fetchSettings();

                // Change password wiring
                initPasswordChange();

                // Warn on unsaved changes
                window.addEventListener('beforeunload', (e) => {
                    if (hasUnsavedChanges) {
                        e.preventDefault();
                        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        return e.returnValue;
                    }
                });
            });
        })();
    </script>

    <!-- Set active sidebar item for Settings page -->
    <script>
        // Set Settings as active page in localStorage
        localStorage.setItem('activePage', 'Settings');
    </script>
</body>

</html>






