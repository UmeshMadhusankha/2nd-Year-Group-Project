
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const messageTextarea = document.getElementById('message');
    const charCounter = document.querySelector('.char-counter');
    const successMessage = document.getElementById('successMessage');
    
    // Character counter for message textarea
    if (messageTextarea && charCounter) {
        messageTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const maxLength = 500;
            
            charCounter.textContent = `${currentLength} / ${maxLength} characters`;
            
            // Change color if approaching limit
            if (currentLength > maxLength * 0.9) {
                charCounter.style.color = 'var(--danger-color)';
            } else if (currentLength > maxLength * 0.7) {
                charCounter.style.color = 'var(--warning-color)';
            } else {
                charCounter.style.color = 'var(--text-muted)';
            }
            
            // Limit character count
            if (currentLength > maxLength) {
                this.value = this.value.substring(0, maxLength);
                charCounter.textContent = `${maxLength} / ${maxLength} characters`;
            }
        });
    }
    
    // Handle form submission (visual only - no backend)
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data (for visual purposes only)
            const formData = new FormData(contactForm);
            const name = formData.get('name');
            const email = formData.get('email');
            const subject = formData.get('subject');
            const message = formData.get('message');
            
            // Validate form
            if (!name || !email || !subject || !message) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Show success message
            successMessage.classList.add('show');
            
            // Reset form
            contactForm.reset();
            charCounter.textContent = '0 / 500 characters';
            
            // Hide success message after 5 seconds
            setTimeout(function() {
                successMessage.classList.remove('show');
            }, 5000);
            
            // Scroll to success message
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }
    
    // Email validation
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailPattern.test(this.value)) {
                this.style.borderColor = 'var(--danger-color)';
            } else {
                this.style.borderColor = 'var(--border-color)';
            }
        });
    }
});
