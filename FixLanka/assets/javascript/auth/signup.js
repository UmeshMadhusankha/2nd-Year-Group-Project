// Role-based form switching and validation
document.addEventListener('DOMContentLoaded', function() {
    const roleButtons = document.querySelectorAll('.role-btn');
    const forms = document.querySelectorAll('.signup-form');
    
    // Role button click handlers
    roleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const role = this.getAttribute('data-role');
            
            // Update active button
            roleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update visible form
            forms.forEach(form => {
                if (form.getAttribute('data-role') === role) {
                    form.classList.add('active');
                } else {
                    form.classList.remove('active');
                }
            });
        });
    });
    
    // Form submission validation
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const role = this.getAttribute('data-role');
            
            // Validate password match
            const password = this.querySelector('[name="password"]').value;
            const confirmPassword = this.querySelector('[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
            
            // Validate districts for repairer and company
            if (role === 'repairer' || role === 'company') {
                const districtCheckboxes = this.querySelectorAll('input[name="districts[]"]:checked');
                if (districtCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one service district');
                    return false;
                }
            }
            
            // Validate business type for company
            if (role === 'company') {
                const businessTypeCheckboxes = this.querySelectorAll('input[name="business_type[]"]:checked');
                if (businessTypeCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one business type');
                    return false;
                }
            }
            
            // Validate file upload for repairer (if provided)
            if (role === 'repairer') {
                const fileInput = this.querySelector('input[name="profile_picture"]');
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    
                    if (file.size > maxSize) {
                        e.preventDefault();
                        alert('Profile picture must be less than 5MB');
                        return false;
                    }
                    
                    if (!allowedTypes.includes(file.type)) {
                        e.preventDefault();
                        alert('Only JPG, PNG, GIF, and WEBP images are allowed');
                        return false;
                    }
                }
            }
            
            return true;
        });
    });
    
    // Real-time password match indicator for all forms
    const allPasswordInputs = document.querySelectorAll('input[type="password"][name="password"]');
    allPasswordInputs.forEach(passwordInput => {
        const form = passwordInput.closest('form');
        const confirmPasswordInput = form.querySelector('input[name="confirm_password"]');
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.style.borderColor = '#ff4444';
                } else {
                    this.style.borderColor = '#44ff44';
                }
            });
        }
    });
    
    // Password strength indicator
    const passwordInputs = document.querySelectorAll('input[type="password"][name="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            const password = this.value;
            const hint = this.nextElementSibling;
            
            if (!hint || !hint.classList.contains('password-hint')) return;
            
            if (password.length === 0) {
                hint.textContent = 'At least 6 characters';
                hint.style.color = '#666';
            } else if (password.length < 6) {
                hint.textContent = 'Too short';
                hint.style.color = '#e74c3c';
            } else if (password.length < 8) {
                hint.textContent = 'Good';
                hint.style.color = '#f39c12';
            } else {
                hint.textContent = 'Strong';
                hint.style.color = '#27ae60';
            }
        });
    });
});