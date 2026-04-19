/**
 * Settings Page JavaScript
 */

const SETTINGS_API = '/2nd-Year-Group-Project/FixLanka/api/repairer-settings.php';

document.addEventListener('DOMContentLoaded', function () {
    initializeSettingsTabs();
    initializeSaveButtons();
    loadSettingsData();
});

/**
 * Initialize Settings Tab Navigation
 */
function initializeSettingsTabs() {
    const tabs = document.querySelectorAll('.settings-tab');
    const panels = document.querySelectorAll('.settings-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');

            // Remove active class from all tabs and panels
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));

            // Add active class to clicked tab and corresponding panel
            this.classList.add('active');
            const targetPanel = document.getElementById(`${targetTab}-panel`);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }

            // Store active tab in localStorage
            localStorage.setItem('activeSettingsTab', targetTab);
        });
    });

    // Restore last active tab
    const lastActiveTab = localStorage.getItem('activeSettingsTab');
    if (lastActiveTab) {
        const tab = document.querySelector(`[data-tab="${lastActiveTab}"]`);
        if (tab) {
            tab.click();
        }
    }
}

/**
 * Initialize Save Buttons
 */
function initializeSaveButtons() {
    const saveNotificationsBtn = document.getElementById('saveNotificationsBtn');
    const savePrivacyBtn = document.getElementById('savePrivacyBtn');

    if (saveNotificationsBtn) {
        saveNotificationsBtn.addEventListener('click', async function () {
            await handleSettingsSave(this, 'notifications');
        });
    }

    if (savePrivacyBtn) {
        savePrivacyBtn.addEventListener('click', async function () {
            await handleSettingsSave(this, 'privacy');
        });
    }

    // Delete account button
    const deleteButton = document.querySelector('.btn-danger');
    if (deleteButton) {
        deleteButton.addEventListener('click', async function () {
            const confirmed = await window.showConfirm('Are you sure you want to delete your account? This action cannot be undone.', {
                title: 'Delete Account',
                confirmText: 'Delete Account',
                type: 'danger'
            });

            if (confirmed) {
                const doubleConfirmed = await window.showConfirm('This will permanently delete all your data. Are you absolutely sure?', {
                    title: 'Final Confirmation',
                    confirmText: 'Yes, Permanently Delete',
                    type: 'danger'
                });

                if (doubleConfirmed) {
                    showNotification('Account deletion initiated. You will receive a confirmation email.', 'warning');
                }
            }
        });
    }

    // Cancel button
    const cancelButtons = document.querySelectorAll('.btn-secondary');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function () {
            showNotification('Changes discarded', 'info');
        });
    });
}

async function loadSettingsData() {
    await loadSettingsDataFromApi();
}

async function loadSettingsDataFromApi() {
    try {
        const response = await fetch(SETTINGS_API, { method: 'GET' });
        const result = await response.json();
        if (!result.success) {
            showNotification(result.message || 'Failed to load settings', 'error');
            return;
        }

        const settings = result.data || {};
        setToggle('emailJobRequests', isTrue(settings.email_job_requests));
        setToggle('emailQuoteResponses', isTrue(settings.email_quote_responses));
        setToggle('emailPaymentNotifications', isTrue(settings.email_payment_notifications));
        setToggle('emailReviewsRatings', isTrue(settings.email_reviews_ratings));
        setToggle('emailWeeklySummary', isTrue(settings.email_weekly_summary));
        setToggle('pushBrowserNotifications', isTrue(settings.push_browser_notifications));
        setToggle('pushSoundAlerts', isTrue(settings.push_sound_alerts));

        setToggle('privacyProfileVisibility', isTrue(settings.privacy_profile_visibility));
        setToggle('privacyShowContact', isTrue(settings.privacy_show_contact));
        setToggle('privacyLocationSharing', isTrue(settings.privacy_location_sharing));

        setToggle('securityLoginAlerts', isTrue(settings.security_login_alerts));
        setInputValue('securitySessionTimeout', settings.security_session_timeout || '30 minutes');
    } catch (error) {
        console.error('Error loading settings:', error);
        showNotification('Failed to load settings', 'error');
    }
}


async function handleSettingsSave(button, panelType) {
    setButtonLoading(button, true);

    try {
        const settingsPayload = panelType === 'notifications'
            ? collectNotificationSettings()
            : collectPrivacySettings();

        const response = await fetch(SETTINGS_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update_settings', settings: settingsPayload })
        });

        const result = await response.json();
        if (!result.success) {
            showNotification(result.message || 'Failed to save settings', 'error');
            return;
        }

        showNotification('Settings saved successfully!', 'success');
    } catch (error) {
        console.error('Error saving settings:', error);
        showNotification('Failed to save settings', 'error');
    } finally {
        setButtonLoading(button, false);
    }
}

function collectNotificationSettings() {
    return {
        emailJobRequests: getToggleValue('emailJobRequests'),
        emailQuoteResponses: getToggleValue('emailQuoteResponses'),
        emailPaymentNotifications: getToggleValue('emailPaymentNotifications'),
        emailReviewsRatings: getToggleValue('emailReviewsRatings'),
        emailWeeklySummary: getToggleValue('emailWeeklySummary'),
        pushBrowserNotifications: getToggleValue('pushBrowserNotifications'),
        pushSoundAlerts: getToggleValue('pushSoundAlerts')
    };
}

function collectPrivacySettings() {
    return {
        privacyProfileVisibility: getToggleValue('privacyProfileVisibility'),
        privacyShowContact: getToggleValue('privacyShowContact'),
        privacyLocationSharing: getToggleValue('privacyLocationSharing'),
        securityLoginAlerts: getToggleValue('securityLoginAlerts'),
        securitySessionTimeout: getInputValue('securitySessionTimeout')
    };
}

function setButtonLoading(button, isLoading) {
    if (!button) return;
    if (isLoading) {
        button.dataset.originalText = button.textContent;
        button.textContent = 'Saving...';
        button.disabled = true;
    } else {
        button.textContent = button.dataset.originalText || 'Save';
        button.disabled = false;
    }
}

function setInputValue(id, value) {
    const input = document.getElementById(id);
    if (input) {
        input.value = value ?? '';
    }
}

function getInputValue(id) {
    const input = document.getElementById(id);
    return input ? String(input.value).trim() : '';
}

function setToggle(id, value) {
    const input = document.getElementById(id);
    if (input) {
        input.checked = Boolean(value);
    }
}

function getToggleValue(id) {
    const input = document.getElementById(id);
    return input ? Boolean(input.checked) : false;
}

function isTrue(value) {
    return value === true || value === 1 || value === '1' || value === 'true';
}

/**
 * Show Notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
    `;

    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: calc(var(--header-height) + 20px);
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 500;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;

    document.body.appendChild(notification);

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

/**
 * Get Notification Icon
 */
function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

/**
 * Get Notification Color
 */
function getNotificationColor(type) {
    const colors = {
        success: '#22c55e',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6'
    };
    return colors[type] || '#3b82f6';
}

/**
 * Add CSS animations
 */
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .notification-content i {
        font-size: 20px;
    }
`;
document.head.appendChild(style);


