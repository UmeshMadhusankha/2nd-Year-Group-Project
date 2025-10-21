document.addEventListener('DOMContentLoaded', function() {
    const signupForm = document.getElementById('signupForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    // Password validation
    signupForm.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Passwords do not match!');
            confirmPassword.focus();
            return false;
        }
        
        if (password.value.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            password.focus();
            return false;
        }
    });
    
    // Real-time password match indicator
    confirmPassword.addEventListener('input', function() {
        if (this.value !== password.value) {
            this.style.borderColor = '#ff4444';
        } else {
            this.style.borderColor = '#44ff44';
        }
    });
});