// Settings page functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize all features when page loads
    initializeTabs();
    initializePasswordStrength();
    initializePasswordToggles();
    initializeFileUploads();
    initializeToggles();
    initializeSaveButtons();
    initializeSessionManagement();
    initializePaymentMethods();
    
});

/**
 * Tab Navigation System
 * Allows users to switch between different settings sections
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the tab name from the data attribute
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
            
            // Save the active tab to localStorage so it persists on page reload
            localStorage.setItem('activeSettingsTab', targetTab);
        });
    });
    
    // Restore previously active tab from localStorage
    const savedTab = localStorage.getItem('activeSettingsTab');
    if (savedTab) {
        const savedButton = document.querySelector(`[data-tab="${savedTab}"]`);
        const savedContent = document.getElementById(savedTab);
        
        if (savedButton && savedContent) {
            // Clear all active states
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Activate saved tab
            savedButton.classList.add('active');
            savedContent.classList.add('active');
        }
    } else {
        // If no saved tab, make sure the first tab is active
        if (tabButtons.length > 0 && tabContents.length > 0) {
            tabButtons[0].classList.add('active');
            tabContents[0].classList.add('active');
        }
    }
}

/**
 * Password Strength Indicator
 * Provides real-time feedback on password strength as user types
 */
function initializePasswordStrength() {
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const strengthContainer = newPasswordInput ? newPasswordInput.closest('.form-group').querySelector('.password-strength') : null;
    const requirementsList = document.querySelectorAll('.password-requirements li');
    
    if (!newPasswordInput || !strengthContainer) return;
    
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        
        // Check each requirement
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Update visual indicators for each requirement
        requirementsList.forEach((item, index) => {
            const reqKey = Object.keys(requirements)[index];
            if (requirements[reqKey]) {
                item.classList.add('valid');
                item.querySelector('i').className = 'fas fa-check-circle';
            } else {
                item.classList.remove('valid');
                item.querySelector('i').className = 'fas fa-circle';
            }
        });
        
        // Calculate overall strength
        const metRequirements = Object.values(requirements).filter(Boolean).length;
        const strengthText = strengthContainer.querySelector('.strength-text');
        
        // Remove existing strength classes
        strengthContainer.classList.remove('weak', 'medium', 'strong');
        
        // Apply new strength class based on how many requirements are met
        if (metRequirements <= 2) {
            strengthContainer.classList.add('weak');
            strengthText.textContent = 'Weak password';
        } else if (metRequirements <= 4) {
            strengthContainer.classList.add('medium');
            strengthText.textContent = 'Medium strength password';
        } else {
            strengthContainer.classList.add('strong');
            strengthText.textContent = 'Strong password';
        }
    });
    
    // Validate that passwords match when user types in confirm field
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const newPassword = newPasswordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                this.style.borderColor = 'var(--danger-color)';
            } else {
                this.style.borderColor = '';
            }
        });
    }
}

/**
 * Password Visibility Toggle
 * Allows users to show/hide password text
 */
function initializePasswordToggles() {
    const passwordToggles = document.querySelectorAll('.toggle-password');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            // Toggle between password and text input type
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

/**
 * File Upload Handlers
 * Handles logo and profile picture uploads with preview
 */
function initializeFileUploads() {
    // Company logo upload
    const logoInput = document.getElementById('companyLogo');
    const logoImage = document.getElementById('logoImage');
    const logoUploadBtn = document.getElementById('uploadLogoBtn');
    
    if (logoUploadBtn) {
        logoUploadBtn.addEventListener('click', function() {
            logoInput.click();
        });
    }
    
    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showToast('Error', 'Please select an image file', 'error');
                    return;
                }
                
                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showToast('Error', 'File size must be less than 5MB', 'error');
                    return;
                }
                
                // Create preview of the uploaded image
                const reader = new FileReader();
                reader.onload = function(e) {
                    logoImage.src = e.target.result;
                    showToast('Success', 'Logo uploaded successfully', 'success');
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Profile picture upload
    const profileInput = document.getElementById('profilePicture');
    const profileImage = document.getElementById('profileImage');
    const profileUploadBtn = document.getElementById('uploadProfileBtn');
    
    if (profileUploadBtn) {
        profileUploadBtn.addEventListener('click', function() {
            profileInput.click();
        });
    }
    
    if (profileInput) {
        profileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    showToast('Error', 'Please select an image file', 'error');
                    return;
                }
                
                // Validate file size (max 2MB for profile pictures)
                if (file.size > 2 * 1024 * 1024) {
                    showToast('Error', 'File size must be less than 2MB', 'error');
                    return;
                }
                
                // Create preview of the uploaded image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                    showToast('Success', 'Profile picture updated successfully', 'success');
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Toggle Switch Management
 * Handles all toggle switches for notifications and preferences
 */
function initializeToggles() {
    const toggles = document.querySelectorAll('.switch input[type="checkbox"]');
    
    toggles.forEach(toggle => {
        // Load saved state from localStorage
        const toggleId = toggle.id;
        const savedState = localStorage.getItem(`toggle_${toggleId}`);
        
        if (savedState !== null) {
            toggle.checked = savedState === 'true';
        }
        
        // Save state when toggle changes
        toggle.addEventListener('change', function() {
            localStorage.setItem(`toggle_${toggleId}`, this.checked);
            
            // Show feedback to user
            const label = this.closest('.notification-item, .preference-item')?.querySelector('h3')?.textContent;
            const status = this.checked ? 'enabled' : 'disabled';
            showToast('Settings Updated', `${label} ${status}`, 'success');
        });
    });
}

/**
 * Save Button Handlers
 * Handles form submissions and data saving
 */
function initializeSaveButtons() {
    // Save all button in header
    const saveAllBtn = document.querySelector('.btn-save-all');
    if (saveAllBtn) {
        saveAllBtn.addEventListener('click', function() {
            saveAllSettings();
        });
    }
    
    // Individual section save buttons
    const saveBtns = document.querySelectorAll('.btn-primary');
    saveBtns.forEach(btn => {
        // Skip the save-all button and 2FA enable button
        if (btn.classList.contains('btn-save-all') || btn.classList.contains('btn-enable')) {
            return;
        }
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.tab-content');
            if (form) {
                saveTabSettings(form);
            }
        });
    });
    
    // Enable 2FA button
    const enable2FABtn = document.querySelector('.btn-enable');
    if (enable2FABtn) {
        enable2FABtn.addEventListener('click', function() {
            enable2FA();
        });
    }
    
    // Delete account button
    const deleteAccountBtn = document.querySelector('.btn-danger-outline');
    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', function() {
            deleteAccount();
        });
    }
}

/**
 * Save all settings across all tabs
 */
function saveAllSettings() {
    // Collect all form data from all tabs
    const allInputs = document.querySelectorAll('.settings-content input, .settings-content select, .settings-content textarea');
    const formData = new FormData();
    
    allInputs.forEach(input => {
        if (input.type === 'checkbox') {
            formData.append(input.name || input.id, input.checked);
        } else if (input.type !== 'file') {
            formData.append(input.name || input.id, input.value);
        }
    });
    
    // Simulate API call (replace with actual API call)
    setTimeout(() => {
        showToast('Success', 'All settings saved successfully', 'success');
    }, 500);
    
    // In a real application, you would send this data to the server:
    // fetch('/api/settings/save-all', {
    //     method: 'POST',
    //     body: formData
    // })
    // .then(response => response.json())
    // .then(data => {
    //     showToast('Success', 'All settings saved successfully', 'success');
    // })
    // .catch(error => {
    //     showToast('Error', 'Failed to save settings', 'error');
    // });
}

/**
 * Save settings for a specific tab
 */
function saveTabSettings(tabContent) {
    const tabId = tabContent.id;
    const inputs = tabContent.querySelectorAll('input, select, textarea');
    const formData = new FormData();
    
    // Collect form data
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            formData.append(input.name || input.id, input.checked);
        } else if (input.type !== 'file') {
            formData.append(input.name || input.id, input.value);
        }
    });
    
    // Validate password fields if in account settings
    if (tabId === 'account-tab') {
        const currentPassword = document.getElementById('currentPassword')?.value;
        const newPassword = document.getElementById('newPassword')?.value;
        const confirmPassword = document.getElementById('confirmPassword')?.value;
        
        if (newPassword && newPassword !== confirmPassword) {
            showToast('Error', 'New passwords do not match', 'error');
            return;
        }
        
        if (newPassword && !currentPassword) {
            showToast('Error', 'Please enter your current password', 'error');
            return;
        }
    }
    
    // Simulate API call
    setTimeout(() => {
        const tabName = tabContent.querySelector('.section-title')?.textContent || 'Settings';
        showToast('Success', `${tabName} saved successfully`, 'success');
        
        // Clear password fields after successful save
        if (tabId === 'account-tab') {
            const passwordInputs = tabContent.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => input.value = '');
        }
    }, 500);
}

/**
 * Session Management
 * Handles active session revocation
 */
function initializeSessionManagement() {
    const revokeButtons = document.querySelectorAll('.btn-revoke');
    
    revokeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const sessionItem = this.closest('.session-item');
            const deviceName = sessionItem.querySelector('h3').textContent;
            
            // Show confirmation dialog
            if (confirm(`Are you sure you want to revoke access for "${deviceName}"?`)) {
                // Simulate API call to revoke session
                setTimeout(() => {
                    sessionItem.style.transition = 'all 0.3s ease';
                    sessionItem.style.opacity = '0';
                    sessionItem.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        sessionItem.remove();
                        showToast('Success', 'Session revoked successfully', 'success');
                    }, 300);
                }, 500);
            }
        });
    });
}

/**
 * Payment Method Management
 * Handles setting primary payment method and adding new cards
 */
function initializePaymentMethods() {
    // Set primary payment method
    const setPrimaryButtons = document.querySelectorAll('.btn-set-primary');
    setPrimaryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active state from all cards
            document.querySelectorAll('.payment-method-card').forEach(card => {
                card.classList.remove('active');
                card.querySelector('.badge-primary')?.remove();
            });
            
            // Set this card as primary
            const card = this.closest('.payment-method-card');
            card.classList.add('active');
            
            // Add primary badge
            const badge = document.createElement('span');
            badge.className = 'badge-primary';
            badge.textContent = 'Primary';
            card.querySelector('.card-info').appendChild(badge);
            
            // Hide this button
            this.style.display = 'none';
            
            showToast('Success', 'Primary payment method updated', 'success');
        });
    });
    
    // Remove payment method
    const removeButtons = document.querySelectorAll('.btn-icon');
    removeButtons.forEach(btn => {
        if (btn.querySelector('.fa-trash')) {
            btn.addEventListener('click', function() {
                const card = this.closest('.payment-method-card');
                const cardNumber = card.querySelector('.card-info p').textContent;
                
                if (confirm(`Remove payment method ${cardNumber}?`)) {
                    card.style.transition = 'all 0.3s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        card.remove();
                        showToast('Success', 'Payment method removed', 'success');
                    }, 300);
                }
            });
        }
    });
    
    // Add new payment method
    const addCardBtn = document.querySelector('.btn-add-card');
    if (addCardBtn) {
        addCardBtn.addEventListener('click', function() {
            // In a real application, this would open a modal with a payment form
            showToast('Info', 'Payment form would open here', 'success');
        });
    }
}

/**
 * Enable Two-Factor Authentication
 */
function enable2FA() {
    // In a real application, this would open a modal with QR code and setup instructions
    const confirmed = confirm('Enable Two-Factor Authentication?\n\nYou will need to scan a QR code with your authenticator app.');
    
    if (confirmed) {
        // Simulate API call
        setTimeout(() => {
            // Update the security card to show enabled state
            const twoFACard = document.querySelector('.security-card');
            const statusBadge = twoFACard.querySelector('.status-badge');
            const enableBtn = twoFACard.querySelector('.btn-enable');
            
            if (statusBadge && enableBtn) {
                statusBadge.classList.remove('disabled');
                statusBadge.classList.add('enabled');
                statusBadge.textContent = 'Enabled';
                
                // Change button to disable
                enableBtn.textContent = 'Disable';
                enableBtn.classList.add('btn-danger');
                
                showToast('Success', 'Two-Factor Authentication enabled', 'success');
            }
        }, 500);
    }
}

/**
 * Delete Account
 */
function deleteAccount() {
    const confirmed = confirm('Are you sure you want to delete your account?\n\nThis action cannot be undone and all your data will be permanently deleted.');
    
    if (confirmed) {
        const doubleConfirm = confirm('This is your last chance. Are you absolutely sure?');
        
        if (doubleConfirm) {
            // In a real application, this would call an API to delete the account
            showToast('Info', 'Account deletion would be processed here', 'success');
            
            // Typically, you would redirect to a confirmation page or logout
            // setTimeout(() => {
            //     window.location.href = '/logout';
            // }, 2000);
        }
    }
}

/**
 * Toast Notification System
 * Shows temporary notifications to the user
 */
function showToast(title, message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    const toastIcon = toast.querySelector('.toast-icon');
    const toastTitle = toast.querySelector('.toast-content h4');
    const toastMessage = toast.querySelector('.toast-content p');
    const closeBtn = toast.querySelector('.toast-close');
    
    // Update content
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Update icon based on type
    if (type === 'success') {
        toastIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        toastIcon.className = 'toast-icon success';
    } else if (type === 'error') {
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
        toastIcon.className = 'toast-icon error';
    }
    
    // Show toast with animation
    toast.classList.add('show');
    
    // Auto-hide after 3 seconds
    const autoHideTimeout = setTimeout(() => {
        hideToast();
    }, 3000);
    
    // Close button handler
    closeBtn.onclick = function() {
        clearTimeout(autoHideTimeout);
        hideToast();
    };
}

/**
 * Hide toast notification
 */
function hideToast() {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.classList.remove('show');
    }
}

/**
 * Form Validation
 * Validates all required fields before submission
 */
function validateForm(form) {
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = 'var(--danger-color)';
            
            // Reset border color when user starts typing
            input.addEventListener('input', function() {
                this.style.borderColor = '';
            }, { once: true });
        }
    });
    
    if (!isValid) {
        showToast('Error', 'Please fill in all required fields', 'error');
    }
    
    return isValid;
}

/**
 * Auto-save functionality (optional)
 * Automatically saves changes as user types (with debounce)
 */
let autoSaveTimeout;
function enableAutoSave() {
    const inputs = document.querySelectorAll('.settings-content input, .settings-content select, .settings-content textarea');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Clear existing timeout
            clearTimeout(autoSaveTimeout);
            
            // Set new timeout - saves 2 seconds after user stops typing
            autoSaveTimeout = setTimeout(() => {
                const value = this.value;
                const fieldName = this.name || this.id;
                
                // Save to localStorage for demo purposes
                localStorage.setItem(`settings_${fieldName}`, value);
                
                // In a real application, you would send this to the server
                
            }, 2000);
        });
    });
}

// Uncomment to enable auto-save feature
// enableAutoSave();
