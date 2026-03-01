/* ================================================
   MY PROFILE PAGE JAVASCRIPT
   ================================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const saveChangesBtn = document.getElementById('save-changes-btn');
    const cancelChangesBtn = document.getElementById('cancel-changes-btn');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const photoUploadBtn = document.getElementById('photo-upload-btn');
    const photoUploadInput = document.getElementById('photo-upload-input');
    const profilePhoto = document.getElementById('profile-photo');
    
    // Modal elements
    const changePasswordModal = document.getElementById('change-password-modal');
    const closePasswordModal = document.getElementById('close-password-modal');
    const cancelPasswordChange = document.getElementById('cancel-password-change');
    const passwordForm = document.getElementById('password-form');
    
    // Profile dropdown elements
    const profileMenu = document.querySelector('.profile-menu');
    const profileDropdown = document.querySelector('.profile-dropdown');
    
    // Form elements
    const profileForm = document.getElementById('profile-form');
    const formInputs = document.querySelectorAll('.form-input');
    const formSelects = document.querySelectorAll('.form-select');
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    
    // State management
    let isEditing = false;
    let originalFormData = {};
    
    // Initialize page
    init();
    
    function init() {
        // Store original form data
        storeOriginalFormData();
        
        // Add event listeners
        addEventListeners();
    }
    
    function storeOriginalFormData() {
        originalFormData = {};
        
        formInputs.forEach(input => {
            originalFormData[input.name] = input.value;
        });
        
        formSelects.forEach(select => {
            originalFormData[select.name] = select.value;
        });
        
        checkboxes.forEach(checkbox => {
            originalFormData[checkbox.name] = checkbox.checked;
        });
    }
    
    function addEventListeners() {
        // Edit profile button
        editProfileBtn.addEventListener('click', toggleEditMode);
        
        // Save and cancel buttons
        saveChangesBtn.addEventListener('click', handleSaveChanges);
        cancelChangesBtn.addEventListener('click', handleCancelChanges);
        
        // Change password functionality
        changePasswordBtn.addEventListener('click', showChangePasswordModal);
        closePasswordModal.addEventListener('click', hideChangePasswordModal);
        cancelPasswordChange.addEventListener('click', hideChangePasswordModal);
        passwordForm.addEventListener('submit', handlePasswordChange);
        
        // Photo upload functionality
        photoUploadBtn.addEventListener('click', triggerPhotoUpload);
        photoUploadInput.addEventListener('change', handlePhotoUpload);
        
        // Profile dropdown functionality
        if (profileMenu) {
            profileMenu.addEventListener('click', toggleProfileDropdown);
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', handleOutsideClick);
        
        // Modal overlay click to close
        changePasswordModal.addEventListener('click', function(e) {
            if (e.target === changePasswordModal) {
                hideChangePasswordModal();
            }
        });
        
        // Form submission
        profileForm.addEventListener('submit', handleFormSubmit);
        
        // Real-time validation for email and phone
        const emailField = document.getElementById('email');
        const phoneField = document.getElementById('phone');
        
        if (emailField) {
            emailField.addEventListener('blur', function() {
                if (isEditing && this.value) {
                    if (!isValidEmail(this.value)) {
                        showFieldError(this, 'Please enter a valid email address (e.g., user@example.com)');
                    } else {
                        clearFieldError(this);
                    }
                }
            });
            
            emailField.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearFieldError(this);
                }
            });
        }
        
        if (phoneField) {
            phoneField.addEventListener('blur', function() {
                if (isEditing && this.value) {
                    if (!isValidPhone(this.value)) {
                        showFieldError(this, 'Please enter a valid phone number (e.g., 0771234567 or +94771234567)');
                    } else {
                        clearFieldError(this);
                    }
                }
            });
            
            phoneField.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    clearFieldError(this);
                }
            });
        }
        
        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideChangePasswordModal();
                closeProfileDropdown();
            }
        });
    }
    
    function toggleEditMode() {
        isEditing = !isEditing;
        
        if (isEditing) {
            enterEditMode();
        } else {
            exitEditMode();
        }
    }
    
    function enterEditMode() {
        // Enable form fields
        formInputs.forEach(input => {
            input.removeAttribute('readonly');
            input.classList.add('editable');
        });
        
        formSelects.forEach(select => {
            select.removeAttribute('disabled');
            select.classList.add('editable');
        });
        
        checkboxes.forEach(checkbox => {
            checkbox.removeAttribute('disabled');
        });
        
        // Update button states
        editProfileBtn.style.display = 'none';
        saveChangesBtn.style.display = 'inline-flex';
        cancelChangesBtn.style.display = 'inline-flex';
        
        // Update button text
        editProfileBtn.innerHTML = '<i class="fas fa-times"></i><span>Cancel Edit</span>';
        
        // Show visual feedback
        showNotification('Edit mode enabled. Make your changes and click Save.', 'info');
    }
    
    function exitEditMode() {
        // Disable form fields
        formInputs.forEach(input => {
            input.setAttribute('readonly', 'readonly');
            input.classList.remove('editable');
        });
        
        formSelects.forEach(select => {
            select.setAttribute('disabled', 'disabled');
            select.classList.remove('editable');
        });
        
        checkboxes.forEach(checkbox => {
            checkbox.setAttribute('disabled', 'disabled');
        });
        
        // Update button states
        editProfileBtn.style.display = 'inline-flex';
        saveChangesBtn.style.display = 'none';
        cancelChangesBtn.style.display = 'none';
        
        // Reset button text
        editProfileBtn.innerHTML = '<i class="fas fa-edit"></i><span>Edit Profile</span>';
        
        isEditing = false;
    }
    
    function handleSaveChanges(e) {
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading state
        saveChangesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Saving...</span>';
        saveChangesBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Update original form data
            storeOriginalFormData();
            
            // Update availability status across the application
            updateGlobalAvailabilityStatus();
            
            // Exit edit mode
            exitEditMode();
            
            // Reset button
            saveChangesBtn.innerHTML = '<i class="fas fa-save"></i><span>Save Changes</span>';
            saveChangesBtn.disabled = false;
            
            // Show success message
            showNotification('Profile updated successfully!', 'success');
            
            // Update profile name in the UI
            updateProfileDisplay();
            
        }, 1500);
    }
    
    function handleCancelChanges(e) {
        e.preventDefault();
        
        // Restore original form data
        restoreOriginalFormData();
        
        // Exit edit mode
        exitEditMode();
        
        // Show notification
        showNotification('Changes cancelled.', 'info');
    }
    
    function restoreOriginalFormData() {
        formInputs.forEach(input => {
            if (originalFormData.hasOwnProperty(input.name)) {
                input.value = originalFormData[input.name];
            }
        });
        
        formSelects.forEach(select => {
            if (originalFormData.hasOwnProperty(select.name)) {
                select.value = originalFormData[select.name];
            }
        });
        
        // Set default district value if not already set
        const districtSelect = document.getElementById('district');
        if (districtSelect && !originalFormData['district']) {
            districtSelect.value = 'colombo'; // Default to Colombo
        }
        
        checkboxes.forEach(checkbox => {
            if (originalFormData.hasOwnProperty(checkbox.name)) {
                checkbox.checked = originalFormData[checkbox.name];
            }
        });
    }
    
    function handleFormSubmit(e) {
        e.preventDefault();
        if (isEditing) {
            handleSaveChanges(e);
        }
    }
    
    function validateForm() {
        const requiredFields = document.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                showFieldError(field, 'This field is required');
                isValid = false;
            } else {
                clearFieldError(field);
            }
        });
        
        // Validate email format
        const emailField = document.getElementById('email');
        if (emailField.value && !isValidEmail(emailField.value)) {
            showFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate phone format
        const phoneField = document.getElementById('phone');
        if (phoneField.value && !isValidPhone(phoneField.value)) {
            showFieldError(phoneField, 'Please enter a valid phone number');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showFieldError(field, message) {
        clearFieldError(field);
        
        field.classList.add('error');
        const errorElement = document.createElement('span');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);
    }
    
    function clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentNode.querySelector('.field-error');
        if (errorElement) {
            errorElement.remove();
        }
    }
    
    function isValidEmail(email) {
        // More comprehensive email validation
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        // Sri Lankan phone number validation
        // Accepts: +94xxxxxxxxx, 0xxxxxxxxx, or plain 10 digits
        const cleanPhone = phone.replace(/\D/g, '');
        
        // Sri Lankan mobile numbers: 10 digits starting with 0, or 11-12 digits with country code
        if (cleanPhone.length === 10 && cleanPhone.startsWith('0')) {
            return true;
        }
        if ((cleanPhone.length === 11 || cleanPhone.length === 12) && cleanPhone.startsWith('94')) {
            return true;
        }
        
        // International format
        const phoneRegex = /^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/;
        return phoneRegex.test(phone) && cleanPhone.length >= 10;
    }
    
    function updateProfileDisplay() {
        const fullNameInput = document.getElementById('full-name');
        const profileNameElements = document.querySelectorAll('.profile-name, .profile-dropdown-name');
        
        profileNameElements.forEach(element => {
            element.textContent = fullNameInput.value;
        });
    }
    
    function updateGlobalAvailabilityStatus() {
        const availabilitySelect = document.getElementById('availability');
        if (!availabilitySelect) return;
        
        const selectedValue = availabilitySelect.value;
        
        // Store availability status in localStorage for cross-page communication
        localStorage.setItem('fixlanka_availability_status', selectedValue);
        
        // Dispatch custom event for any listening components
        const availabilityEvent = new CustomEvent('availabilityChanged', {
            detail: {
                status: selectedValue,
                timestamp: new Date().toISOString()
            }
        });
        
        window.dispatchEvent(availabilityEvent);
        
        // Show specific notification based on status
        if (selectedValue === 'available') {
            showNotification('You are now available for new jobs!', 'success');
        } else {
            showNotification('You are now unavailable for new jobs.', 'info');
        }
    }
    function triggerPhotoUpload() {
        photoUploadInput.click();
    }
    
    function handlePhotoUpload(e) {
        const file = e.target.files[0];
        
        if (file) {
            if (!file.type.startsWith('image/')) {
                showNotification('Please select a valid image file.', 'error');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                showNotification('File size must be less than 5MB.', 'error');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePhoto.src = e.target.result;
                showNotification('Profile photo updated successfully!', 'success');
            };
            reader.readAsDataURL(file);
        }
    }
    
    // Change password modal functionality
    function showChangePasswordModal() {
        changePasswordModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Focus on first input
        setTimeout(() => {
            document.getElementById('current-password').focus();
        }, 100);
    }
    
    function hideChangePasswordModal() {
        changePasswordModal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form
        passwordForm.reset();
        clearPasswordErrors();
    }
    
    function handlePasswordChange(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        
        // Validate passwords
        if (!validatePasswordChange(currentPassword, newPassword, confirmPassword)) {
            return;
        }
        
        // Show loading state
        const submitBtn = passwordForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Hide modal
            hideChangePasswordModal();
            
            // Show success message
            showNotification('Password updated successfully!', 'success');
            
        }, 1500);
    }
    
    function validatePasswordChange(currentPassword, newPassword, confirmPassword) {
        clearPasswordErrors();
        let isValid = true;
        
        if (!currentPassword) {
            showPasswordError('current-password', 'Current password is required');
            isValid = false;
        }
        
        if (!newPassword) {
            showPasswordError('new-password', 'New password is required');
            isValid = false;
        } else if (newPassword.length < 8) {
            showPasswordError('new-password', 'Password must be at least 8 characters long');
            isValid = false;
        }
        
        if (!confirmPassword) {
            showPasswordError('confirm-password', 'Please confirm your new password');
            isValid = false;
        } else if (newPassword !== confirmPassword) {
            showPasswordError('confirm-password', 'Passwords do not match');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showPasswordError(fieldId, message) {
        const field = document.getElementById(fieldId);
        field.classList.add('error');
        
        const errorElement = document.createElement('span');
        errorElement.className = 'field-error';
        errorElement.textContent = message;
        field.parentNode.appendChild(errorElement);
    }
    
    function clearPasswordErrors() {
        const passwordFields = passwordForm.querySelectorAll('.form-input');
        passwordFields.forEach(field => {
            field.classList.remove('error');
            const errorElement = field.parentNode.querySelector('.field-error');
            if (errorElement) {
                errorElement.remove();
            }
        });
    }
    
    // Notification system
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Add close functionality
        notification.querySelector('.notification-close').addEventListener('click', () => {
            hideNotification(notification);
        });
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                hideNotification(notification);
            }
        }, 5000);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
    }
    
    function hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
    
    function getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-circle';
            case 'warning': return 'exclamation-triangle';
            default: return 'info-circle';
        }
    }
    
    // Profile dropdown functionality
    function toggleProfileDropdown(e) {
        e.stopPropagation();
        
        if (profileMenu.classList.contains('active')) {
            closeProfileDropdown();
        } else {
            openProfileDropdown();
        }
    }
    
    function openProfileDropdown() {
        // Close any other open dropdowns first
        closeAllDropdowns();
        
        profileMenu.classList.add('active');
        
        // Add event listener to close dropdown when clicking outside
        setTimeout(() => {
            document.addEventListener('click', handleDropdownOutsideClick);
        }, 0);
    }
    
    function closeProfileDropdown() {
        if (profileMenu) {
            profileMenu.classList.remove('active');
        }
        document.removeEventListener('click', handleDropdownOutsideClick);
    }
    
    function closeAllDropdowns() {
        // Close profile dropdown
        closeProfileDropdown();
        
        // Close any other dropdowns if they exist
        const activeDropdowns = document.querySelectorAll('.dropdown.active, .profile-menu.active');
        activeDropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    }
    
    function handleDropdownOutsideClick(e) {
        if (!profileMenu.contains(e.target)) {
            closeProfileDropdown();
        }
    }
    
    function handleOutsideClick(e) {
        // Close dropdowns when clicking outside
        if (profileMenu && !profileMenu.contains(e.target)) {
            closeProfileDropdown();
        }
    }
});

// Add CSS for notifications and error states
const additionalStyles = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--bg-card);
        border-radius: var(--border-radius);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        border-left: 4px solid var(--info-color);
        padding: var(--spacing-md);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
        min-width: 300px;
        max-width: 500px;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        z-index: 1001;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        border-left-color: var(--success-color);
    }
    
    .notification-error {
        border-left-color: var(--danger-color);
    }
    
    .notification-warning {
        border-left-color: var(--warning-color);
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        flex: 1;
    }
    
    .notification-content i {
        font-size: var(--font-size-lg);
    }
    
    .notification-success .notification-content i {
        color: var(--success-color);
    }
    
    .notification-error .notification-content i {
        color: var(--danger-color);
    }
    
    .notification-warning .notification-content i {
        color: var(--warning-color);
    }
    
    .notification-info .notification-content i {
        color: var(--info-color);
    }
    
    .notification-close {
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: var(--spacing-xs);
        border-radius: var(--border-radius);
        transition: var(--transition-fast);
    }
    
    .notification-close:hover {
        background: var(--bg-secondary);
        color: var(--text-primary);
    }
    
    .form-input.error,
    .form-select.error {
        border-color: var(--danger-color);
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    
    .field-error {
        color: var(--danger-color);
        font-size: var(--font-size-xs);
        margin-top: var(--spacing-xs);
        display: block;
    }
    
    .form-input.editable,
    .form-select.editable {
        border-color: var(--primary-color);
        background: var(--bg-primary);
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
