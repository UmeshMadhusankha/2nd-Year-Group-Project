/**
 * ================================================
 * REGISTRATION PAGE JAVASCRIPT
 * ================================================
 * Handles role selection, form switching, and validation
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== ROLE SELECTION =====
    const roleButtons = document.querySelectorAll('.role-btn');
    const registerForms = document.querySelectorAll('.register-form');

    roleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedRole = this.getAttribute('data-role');
            
            // Update active role button
            roleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding form
            registerForms.forEach(form => {
                if (form.getAttribute('data-form') === selectedRole) {
                    form.classList.add('active');
                } else {
                    form.classList.remove('active');
                }
            });
        });
    });

    // ===== FILE UPLOAD HANDLING =====
    const fileInputs = document.querySelectorAll('.form-input-file');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name;
            const label = this.nextElementSibling.querySelector('span');
            
            if (fileName) {
                label.textContent = fileName;
            } else {
                label.textContent = 'Choose file or drag here';
            }
        });
    });

    // ===== FORM VALIDATION & SUBMISSION =====
    const forms = document.querySelectorAll('.register-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const formType = this.getAttribute('data-form');
            
            // Add user_type to formData
            formData.append('user_type', formType);
            
            // Validate password match
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }
            
            // Validate password strength
            if (password.length < 8) {
                showNotification('Password must be at least 8 characters long!', 'error');
                return;
            }
            
            // Form-specific validation
            if (formType === 'repairer') {
                const pricing = formData.get('pricing');
                if (pricing <= 0) {
                    showNotification('Please enter a valid pricing amount!', 'error');
                    return;
                }
            }
            
            if (formType === 'company') {
                const regNumber = formData.get('registration_number');
                if (regNumber.trim() === '') {
                    showNotification('Please enter a valid registration number!', 'error');
                    return;
                }
            }
            
            // Show loading state
            const submitButton = this.querySelector('.btn-primary');
            const originalText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
            
            // Submit form via AJAX
            fetch('../../controllers/RegistrationController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    
                    // Reset form
                    form.reset();
                    
                    // Redirect based on user type after 2 seconds
                    setTimeout(() => {
                        switch(formType) {
                            case 'user':
                                window.location.href = '../user/dashboard.php';
                                break;
                            case 'repairer':
                                window.location.href = '../repairer/pages/welcome.php';
                                break;
                            case 'company':
                                window.location.href = '../company/dashboard.php';
                                break;
                            default:
                                window.location.href = 'login.php';
                        }
                    }, 2000);
                } else {
                    showNotification(data.message || 'Registration failed. Please try again.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
                showNotification('An error occurred. Please try again later.', 'error');
            });
        });
    });

    // ===== NOTIFICATION SYSTEM =====
    function showNotification(message, type = 'info') {
        // Remove existing notification if any
        const existingNotification = document.querySelector('.notification');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        // Set icon based on type
        let icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        if (type === 'error') icon = 'fa-exclamation-circle';
        if (type === 'warning') icon = 'fa-exclamation-triangle';
        
        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => notification.classList.add('show'), 10);
        
        // Close button functionality
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        });
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // ===== REAL-TIME VALIDATION =====
    const emailInputs = document.querySelectorAll('input[type="email"]');
    
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.style.borderColor = 'var(--danger-color)';
                showValidationError(this, 'Please enter a valid email address');
            } else {
                this.style.borderColor = '';
                removeValidationError(this);
            }
        });
    });
    
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    
    phoneInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && this.value.length < 10) {
                this.style.borderColor = 'var(--danger-color)';
                showValidationError(this, 'Please enter a valid phone number');
            } else {
                this.style.borderColor = '';
                removeValidationError(this);
            }
        });
    });

    // ===== VALIDATION ERROR HELPERS =====
    function showValidationError(input, message) {
        removeValidationError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        input.parentElement.appendChild(errorDiv);
    }

    function removeValidationError(input) {
        const existingError = input.parentElement.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }
    }

    // ===== PASSWORD STRENGTH INDICATOR =====
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    
    passwordInputs.forEach(input => {
        if (input.name === 'password') {
            input.addEventListener('input', function() {
                const strength = calculatePasswordStrength(this.value);
                showPasswordStrength(this, strength);
            });
        }
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 6) strength++;
        if (password.length >= 10) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z\d]/.test(password)) strength++;
        
        return strength;
    }

    function showPasswordStrength(input, strength) {
        let existingIndicator = input.parentElement.querySelector('.password-strength');
        
        if (!existingIndicator) {
            existingIndicator = document.createElement('div');
            existingIndicator.className = 'password-strength';
            input.parentElement.appendChild(existingIndicator);
        }
        
        let strengthText = '';
        let strengthClass = '';
        
        if (strength === 0 || input.value.length === 0) {
            existingIndicator.innerHTML = '';
            return;
        } else if (strength <= 2) {
            strengthText = 'Weak';
            strengthClass = 'weak';
        } else if (strength <= 3) {
            strengthText = 'Medium';
            strengthClass = 'medium';
        } else {
            strengthText = 'Strong';
            strengthClass = 'strong';
        }
        
        existingIndicator.innerHTML = `
            <div class="strength-bar ${strengthClass}">
                <div class="strength-fill"></div>
            </div>
            <span class="strength-text">${strengthText}</span>
        `;
    }
});

// ===== DYNAMIC STYLES FOR NOTIFICATIONS AND VALIDATION =====
const style = document.createElement('style');
style.textContent = `
    /* Notification Styles */
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 16px 20px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 300px;
        max-width: 500px;
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification i:first-child {
        font-size: 20px;
    }

    .notification-success {
        border-left: 4px solid var(--success-color);
    }

    .notification-success i:first-child {
        color: var(--success-color);
    }

    .notification-error {
        border-left: 4px solid var(--danger-color);
    }

    .notification-error i:first-child {
        color: var(--danger-color);
    }

    .notification-warning {
        border-left: 4px solid var(--warning-color);
    }

    .notification-warning i:first-child {
        color: var(--warning-color);
    }

    .notification-info {
        border-left: 4px solid var(--info-color);
    }

    .notification-info i:first-child {
        color: var(--info-color);
    }

    .notification span {
        flex: 1;
        color: var(--text-primary);
        font-size: 14px;
    }

    .notification-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
        font-size: 16px;
        transition: color 0.2s;
    }

    .notification-close:hover {
        color: var(--text-primary);
    }

    /* Validation Error Styles */
    .validation-error {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--danger-color);
        font-size: 12px;
        margin-top: 4px;
    }

    .validation-error i {
        font-size: 12px;
    }

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .strength-bar {
        flex: 1;
        height: 6px;
        background: var(--border-color);
        border-radius: 3px;
        overflow: hidden;
    }

    .strength-fill {
        height: 100%;
        width: 0%;
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    .strength-bar.weak .strength-fill {
        width: 33%;
        background: var(--danger-color);
    }

    .strength-bar.medium .strength-fill {
        width: 66%;
        background: var(--warning-color);
    }

    .strength-bar.strong .strength-fill {
        width: 100%;
        background: var(--success-color);
    }

    .strength-text {
        font-size: 12px;
        font-weight: 500;
        min-width: 60px;
    }

    .strength-bar.weak + .strength-text {
        color: var(--danger-color);
    }

    .strength-bar.medium + .strength-text {
        color: var(--warning-color);
    }

    .strength-bar.strong + .strength-text {
        color: var(--success-color);
    }
`;
document.head.appendChild(style);
