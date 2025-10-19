/**
 * Settings Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeSettingsTabs();
    initializeFormHandlers();
    initializeRangeSlider();
    initializeSaveButtons();
});

/**
 * Initialize Settings Tab Navigation
 */
function initializeSettingsTabs() {
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
 * Initialize Form Handlers
 */
function initializeFormHandlers() {
    // Password validation
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');

    if (confirmPassword) {
        confirmPassword.addEventListener('blur', function() {
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                    confirmPassword.style.borderColor = 'var(--error-color)';
                } else {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.style.borderColor = 'var(--success-color)';
                }
            }
        });
    }

    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                this.style.borderColor = 'var(--error-color)';
            } else {
                this.style.borderColor = 'var(--success-color)';
            }
        });
    }

    // Phone validation
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Remove non-numeric characters except + and space
            this.value = this.value.replace(/[^0-9+\s]/g, '');
        });
    }
}

/**
 * Initialize Range Slider
 */
function initializeRangeSlider() {
    const rangeInput = document.getElementById('radiusRange');
    const rangeValue = document.getElementById('radiusValue');

    if (rangeInput && rangeValue) {
        rangeInput.addEventListener('input', function() {
            rangeValue.textContent = `${this.value} km`;
        });
    }
}

/**
 * Initialize Save Buttons
 */
function initializeSaveButtons() {
    const saveButtons = document.querySelectorAll('.btn-primary');

    saveButtons.forEach(button => {
        if (button.textContent.includes('Save')) {
            button.addEventListener('click', function(e) {
                const panel = this.closest('.settings-panel');
                const panelName = panel.id.replace('-panel', '');
                
                // Show loading state
                const originalText = this.textContent;
                this.textContent = 'Saving...';
                this.disabled = true;

                // Simulate API call
                setTimeout(() => {
                    showNotification('Settings saved successfully!', 'success');
                    this.textContent = originalText;
                    this.disabled = false;

                    // Store settings in localStorage (for demo purposes)
                    saveSettings(panelName, panel);
                }, 1000);
            });
        }
    });

    // Delete account button
    const deleteButton = document.querySelector('.btn-danger');
    if (deleteButton) {
        deleteButton.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
                    showNotification('Account deletion initiated. You will receive a confirmation email.', 'warning');
                }
            }
        });
    }

    // Cancel button
    const cancelButtons = document.querySelectorAll('.btn-secondary');
    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const panel = this.closest('.settings-panel');
            if (panel) {
                loadSettings(panel.id.replace('-panel', ''), panel);
                showNotification('Changes discarded', 'info');
            }
        });
    });
}

/**
 * Save Settings to LocalStorage
 */
function saveSettings(panelName, panel) {
    const settings = {};

    // Get all form inputs
    const inputs = panel.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            settings[input.id || input.name] = input.checked;
        } else if (input.type === 'radio') {
            if (input.checked) {
                settings[input.name] = input.value;
            }
        } else {
            settings[input.id || input.name] = input.value;
        }
    });

    localStorage.setItem(`settings_${panelName}`, JSON.stringify(settings));
    console.log(`Saved ${panelName} settings:`, settings);
}

/**
 * Load Settings from LocalStorage
 */
function loadSettings(panelName, panel) {
    const savedSettings = localStorage.getItem(`settings_${panelName}`);
    
    if (savedSettings) {
        const settings = JSON.parse(savedSettings);
        
        // Restore all form inputs
        Object.keys(settings).forEach(key => {
            const input = panel.querySelector(`#${key}, [name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = settings[key];
                } else if (input.type === 'radio') {
                    if (input.value === settings[key]) {
                        input.checked = true;
                    }
                } else {
                    input.value = settings[key];
                }
            }
        });

        console.log(`Loaded ${panelName} settings:`, settings);
    }
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
 * Add Payment Method
 */
const addPaymentBtn = document.querySelector('.add-payment-btn');
if (addPaymentBtn) {
    addPaymentBtn.addEventListener('click', function() {
        showNotification('Payment method form would open here', 'info');
        // In a real application, this would open a modal with a payment form
    });
}

/**
 * Handle Enable 2FA
 */
const enable2FABtn = document.querySelector('.btn-outline');
if (enable2FABtn && enable2FABtn.textContent.includes('2FA')) {
    enable2FABtn.addEventListener('click', function() {
        showNotification('Two-factor authentication setup would start here', 'info');
        // In a real application, this would start the 2FA setup process
    });
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

console.log('Settings page initialized successfully');
