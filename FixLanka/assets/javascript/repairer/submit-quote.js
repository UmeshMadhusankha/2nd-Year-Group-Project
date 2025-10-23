// ================================================
// SUBMIT QUOTE PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize submit quote page functionality
    initializeQuoteForm();
    initializeConfirmationModal();
    initializeFormValidation();
    
    // Get job ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const jobId = urlParams.get('jobId') || '1'; // Default to job 1 if no ID provided
    
    // Set request_id in hidden field (in real app, this would be the actual request_id from database)
    document.getElementById('request-id').value = jobId;
    
    // Get job data based on ID
    const jobData = getJobDataById(jobId);
    
    // Populate job details
    populateJobDetails(jobData);
    
    // Set default valid until date (7 days from now)
    setDefaultValidUntilDate();
});

// ===== JOB DATA REPOSITORY =====
function getJobDataById(jobId) {
    const jobsDatabase = {
        '1': {
            id: 'JOB-2025-001',
            title: 'Fix Kitchen Faucet Leak',
            customer: 'Sarah Johnson',
            location: 'Colombo 03, Sri Lanka',
            category: 'Plumbing',
            description: 'The kitchen faucet has been leaking for the past week. The leak appears to be coming from the base of the faucet where it connects to the sink. Water is constantly dripping, and the problem seems to be getting worse. I\'ve tried tightening the connections but it hasn\'t helped. The faucet is about 3 years old and was working fine until recently.',
            budget: 'LKR 2,500 - 3,500',
            posted: '2 hours ago',
            urgency: 'Medium'
        },
        '2': {
            id: 'JOB-2025-002',
            title: 'Ceiling Fan Installation',
            customer: 'Ravi Enterprises',
            location: 'Kandy, Central Province',
            category: 'Electrical',
            description: 'Need to install a new ceiling fan in the main office area. The electrical wiring is already in place, just need to mount the fan and connect it properly. The fan is a standard 52-inch model. Need someone experienced with electrical work and proper safety protocols.',
            budget: 'LKR 3,000 - 4,000',
            posted: '4 hours ago',
            urgency: 'Low'
        },
        '3': {
            id: 'JOB-2025-003',
            title: 'AC Unit Servicing',
            customer: 'Michael Fernando',
            location: 'Gampaha, Western Province',
            category: 'HVAC',
            description: 'My air conditioning unit hasn\'t been cooling properly for the past few days. It\'s making strange noises and the air coming out isn\'t very cold. The unit is about 2 years old and hasn\'t been serviced since installation. Need a thorough cleaning and check-up.',
            budget: 'LKR 4,000 - 6,000',
            posted: '6 hours ago',
            urgency: 'High'
        },
        '4': {
            id: 'JOB-2025-004',
            title: 'Washing Machine Repair',
            customer: 'Priya Mendis',
            location: 'Negombo, Western Province',
            category: 'Appliance Repair',
            description: 'My washing machine stopped working yesterday. It fills with water but doesn\'t start the wash cycle. The display shows an error code E3. It\'s a Samsung front-loading machine, about 4 years old. Need urgent repair as I have a lot of laundry pending.',
            budget: 'LKR 3,500 - 5,000',
            posted: '8 hours ago',
            urgency: 'High'
        },
        '5': {
            id: 'JOB-2025-005',
            title: 'Kitchen Cabinet Door Repair',
            customer: 'David Silva',
            location: 'Mount Lavinia, Western Province',
            category: 'Carpentry',
            description: 'One of my kitchen cabinet doors has come off its hinges and needs to be fixed. The hinge seems to be damaged and might need replacement. The cabinet is made of solid wood and matches the rest of the kitchen, so I need someone who can do a neat job.',
            budget: 'LKR 1,500 - 2,500',
            posted: '12 hours ago',
            urgency: 'Low'
        },
        '6': {
            id: 'JOB-2025-006',
            title: 'Bathroom Tile Replacement',
            customer: 'Anjali Perera',
            location: 'Dehiwala, Western Province',
            category: 'General Maintenance',
            description: 'Several tiles in my bathroom have cracked and need replacement. I have matching tiles available. The area that needs work is about 2 square meters near the shower area. Need someone experienced with tile work to ensure waterproofing.',
            budget: 'LKR 5,000 - 7,500',
            posted: '1 day ago',
            urgency: 'Medium'
        }
    };
    
    return jobsDatabase[jobId] || jobsDatabase['1']; // Return requested job or default to job 1
}

// ===== JOB DETAILS POPULATION =====
function populateJobDetails(jobData) {
    document.getElementById('job-title').textContent = jobData.title;
    document.getElementById('customer-name').textContent = jobData.customer;
    document.getElementById('job-location').textContent = jobData.location;
    document.getElementById('job-category').textContent = jobData.category;
    document.getElementById('job-budget').textContent = jobData.budget;
    
    // Set urgency with proper styling
    const urgencyElement = document.getElementById('job-urgency');
    urgencyElement.textContent = jobData.urgency;
    urgencyElement.setAttribute('data-urgency', jobData.urgency.toLowerCase());
    
    document.getElementById('job-description-text').textContent = jobData.description;
    
    // Update confirmation modal job title
    document.getElementById('confirm-job').textContent = jobData.title;
    
    // Update page title and header
    document.title = `Submit Quote - ${jobData.title} - FixLanka`;
    const pageTitle = document.querySelector('.page-info .page-title');
    if (pageTitle) {
        pageTitle.textContent = `Submit Quote - ${jobData.title}`;
    }
}

// ===== QUOTE FORM HANDLING =====
function initializeQuoteForm() {
    const form = document.getElementById('quote-form');
    const backBtn = document.getElementById('back-to-jobs-btn');
    const cancelBtn = document.getElementById('cancel-quote-btn');
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const submitBtn = document.getElementById('submit-quote-btn');

    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleQuoteSubmission();
        });
    }

    // Handle back button - use absolute path
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            if (hasUnsavedChanges()) {
                if (confirm('You have unsaved changes. Are you sure you want to go back?')) {
                    window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
                }
            } else {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
            }
        });
    }

    // Handle cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All entered information will be lost.')) {
                clearForm();
            }
        });
    }

    // Handle save draft button
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            saveDraft();
        });
    }

    // Auto-save functionality
    setupAutoSave();
}

function handleQuoteSubmission() {
    const form = document.getElementById('quote-form');
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const warrantyPeriod = document.getElementById('warranty-period').value;
    const validUntil = document.getElementById('valid-until').value;
    const materialsIncluded = document.getElementById('materials-included').checked;
    const message = document.getElementById('quote-message').value;
    const termsAgreed = document.getElementById('terms-agreement').checked;

    // Validate required fields
    if (!quoteAmount || !estimatedDays || !validUntil || !message || !termsAgreed) {
        showNotification('Please fill in all required fields and agree to terms.', 'error');
        return;
    }

    // Validate quote amount
    if (parseFloat(quoteAmount) <= 0) {
        showNotification('Quote amount must be greater than zero.', 'error');
        return;
    }

    // Validate estimated days
    if (parseInt(estimatedDays) <= 0) {
        showNotification('Estimated days must be at least 1 day.', 'error');
        return;
    }

    // Validate message length
    if (message.trim().length < 10) {
        showNotification('Quote details must be at least 10 characters long.', 'error');
        return;
    }

    // Validate valid until date is in the future
    const validUntilDate = new Date(validUntil);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (validUntilDate < today) {
        showNotification('Quote validity date must be in the future.', 'error');
        return;
    }

    // Update confirmation modal
    updateConfirmationModal(quoteAmount, estimatedDays, warrantyPeriod, validUntil, materialsIncluded);
    
    // Show confirmation modal
    showConfirmationModal();
}

// ===== BREAKDOWN CALCULATOR ===== 
// (Removed - not needed for database schema)

// ===== CONFIRMATION MODAL =====
function initializeConfirmationModal() {
    const modalOverlay = document.getElementById('confirmation-modal-overlay');
    const closeBtn = document.getElementById('close-confirmation-modal');
    const cancelBtn = document.getElementById('cancel-confirmation');
    const confirmBtn = document.getElementById('confirm-submission');

    // Handle close buttons
    if (closeBtn) {
        closeBtn.addEventListener('click', hideConfirmationModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideConfirmationModal);
    }

    // Handle click outside modal
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                hideConfirmationModal();
            }
        });
    }

    // Handle confirmation
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmQuoteSubmission);
    }
}

function showConfirmationModal() {
    const modal = document.getElementById('confirmation-modal-overlay');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function hideConfirmationModal() {
    const modal = document.getElementById('confirmation-modal-overlay');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function updateConfirmationModal(quoteAmount, estimatedDays, warrantyPeriod, validUntil, materialsIncluded) {
    const confirmAmount = document.getElementById('confirm-amount');
    const confirmDays = document.getElementById('confirm-days');
    const confirmWarranty = document.getElementById('confirm-warranty');
    const confirmValidUntil = document.getElementById('confirm-valid-until');
    const confirmMaterials = document.getElementById('confirm-materials');

    if (confirmAmount) {
        confirmAmount.textContent = `Rs. ${parseFloat(quoteAmount).toFixed(2)}`;
    }

    if (confirmDays) {
        const days = parseInt(estimatedDays);
        confirmDays.textContent = `${days} day${days !== 1 ? 's' : ''}`;
    }

    if (confirmWarranty) {
        const months = parseInt(warrantyPeriod);
        if (months === 0) {
            confirmWarranty.textContent = 'No warranty';
        } else if (months === 12) {
            confirmWarranty.textContent = '1 year';
        } else if (months === 24) {
            confirmWarranty.textContent = '2 years';
        } else {
            confirmWarranty.textContent = `${months} month${months !== 1 ? 's' : ''}`;
        }
    }

    if (confirmValidUntil) {
        const date = new Date(validUntil);
        confirmValidUntil.textContent = date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    if (confirmMaterials) {
        confirmMaterials.textContent = materialsIncluded ? 'Included' : 'Not Included';
    }
}

// Helper function to set default valid until date (7 days from now)
function setDefaultValidUntilDate() {
    const validUntilInput = document.getElementById('valid-until');
    if (validUntilInput && !validUntilInput.value) {
        const today = new Date();
        const defaultDate = new Date(today.setDate(today.getDate() + 7));
        const formattedDate = defaultDate.toISOString().split('T')[0];
        validUntilInput.value = formattedDate;
        
        // Set minimum date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        validUntilInput.min = tomorrow.toISOString().split('T')[0];
    }
}

function confirmQuoteSubmission() {
    const confirmBtn = document.getElementById('confirm-submission');
    
    // Show loading state
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    confirmBtn.disabled = true;

    // Get form data
    const requestId = document.getElementById('request-id').value;
    const repairerId = document.getElementById('repairer-id').value;
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const warrantyPeriod = document.getElementById('warranty-period').value;
    const validUntil = document.getElementById('valid-until').value;
    const materialsIncluded = document.getElementById('materials-included').checked;
    const message = document.getElementById('quote-message').value;

    // Prepare data for API
    const quoteData = {
        request_id: parseInt(requestId),
        repairer_id: parseInt(repairerId),
        quoteAmount: parseFloat(quoteAmount),
        estimatedDays: parseInt(estimatedDays),
        warrantyPeriod: parseInt(warrantyPeriod),
        validUntil: validUntil,
        materialsIncluded: materialsIncluded,
        message: message,
        status: 'pending'
    };

    // Submit to API
    fetch('/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(quoteData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide modal
            hideConfirmationModal();
            
            // Show success message
            showNotification('Quote submitted successfully! The customer will be notified.', 'success');
            
            // Log the submitted data (for debugging in dummy mode)
            console.log('Quote submitted:', data.data);
            
            // Clear form
            clearForm();
            
            // Reset button
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm & Submit';
            confirmBtn.disabled = false;
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/my-jobs.php';
            }, 2000);
        } else {
            throw new Error(data.error || 'Failed to submit quote');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to submit quote: ' + error.message, 'error');
        
        // Reset button
        confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm & Submit';
        confirmBtn.disabled = false;
        
        // Hide modal
        hideConfirmationModal();
    });
}

// ===== FORM VALIDATION =====
function initializeFormValidation() {
    const amountInput = document.getElementById('quote-amount');
    const daysInput = document.getElementById('estimated-days');
    const messageInput = document.getElementById('quote-message');
    const validUntilInput = document.getElementById('valid-until');
    const termsCheckbox = document.getElementById('terms-agreement');

    // Real-time validation
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            validateAmount(this);
        });
    }

    if (daysInput) {
        daysInput.addEventListener('input', function() {
            validateDays(this);
        });
    }

    if (messageInput) {
        messageInput.addEventListener('input', function() {
            validateMessage(this);
        });
    }

    if (validUntilInput) {
        validUntilInput.addEventListener('change', function() {
            validateValidUntil(this);
        });
    }

    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            validateTerms(this);
        });
    }
}

function validateAmount(input) {
    const value = parseFloat(input.value);
    const isValid = value > 0;
    
    toggleFieldValidation(input, isValid);
    return isValid;
}

function validateDays(input) {
    const value = parseInt(input.value);
    const isValid = value > 0 && value <= 365;
    
    toggleFieldValidation(input, isValid);
    return isValid;
}

function validateMessage(input) {
    const isValid = input.value.trim().length >= 10;
    
    toggleFieldValidation(input, isValid);
    return isValid;
}

function validateValidUntil(input) {
    const selectedDate = new Date(input.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const isValid = selectedDate > today;
    
    toggleFieldValidation(input, isValid);
    return isValid;
}

function validateTerms(input) {
    const isValid = input.checked;
    
    toggleFieldValidation(input.closest('.checkbox-label'), isValid);
    return isValid;
}

function toggleFieldValidation(element, isValid) {
    if (isValid) {
        element.classList.remove('invalid');
        element.classList.add('valid');
    } else {
        element.classList.remove('valid');
        element.classList.add('invalid');
    }
}

// ===== UTILITY FUNCTIONS =====
function hasUnsavedChanges() {
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const message = document.getElementById('quote-message').value;

    return quoteAmount || estimatedDays || message;
}

function clearForm() {
    const form = document.getElementById('quote-form');
    if (form) {
        form.reset();
        // Reset to default valid until date
        setDefaultValidUntilDate();
    }
}

function saveDraft() {
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const warrantyPeriod = document.getElementById('warranty-period').value;
    const validUntil = document.getElementById('valid-until').value;
    const materialsIncluded = document.getElementById('materials-included').checked;
    const message = document.getElementById('quote-message').value;
    const requestId = document.getElementById('request-id').value;

    if (!quoteAmount && !estimatedDays && !message) {
        showNotification('Nothing to save.', 'info');
        return;
    }

    // Simulate saving to local storage
    const draftData = {
        request_id: requestId,
        quoteAmount,
        estimatedDays,
        warrantyPeriod,
        validUntil,
        materialsIncluded,
        message,
        timestamp: new Date().toISOString()
    };

    localStorage.setItem('quote-draft-' + requestId, JSON.stringify(draftData));
    showNotification('Draft saved successfully!', 'success');
}

function setupAutoSave() {
    let autoSaveTimeout;
    
    const inputs = [
        document.getElementById('quote-amount'),
        document.getElementById('estimated-days'),
        document.getElementById('warranty-period'),
        document.getElementById('valid-until'),
        document.getElementById('materials-included'),
        document.getElementById('quote-message')
    ];

    inputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    if (hasUnsavedChanges()) {
                        saveDraft();
                    }
                }, 30000); // Auto-save after 30 seconds of inactivity
            });
        }
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        max-width: 400px;
        animation: slideIn 0.3s ease;
    `;

    // Add to document
    document.body.appendChild(notification);

    // Handle close button
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.remove();
    });

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS animations and validation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .notification-close:hover {
        opacity: 1;
    }
    
    .form-input.valid,
    .form-textarea.valid {
        border-color: var(--success-color, #4caf50);
    }
    
    .form-input.invalid,
    .form-textarea.invalid {
        border-color: var(--error-color, #f44336);
    }
    
    .checkbox-label.invalid .checkbox-custom {
        border-color: var(--error-color, #f44336);
    }
    
    .checkbox-label.valid .checkbox-custom {
        border-color: var(--success-color, #4caf50);
    }
`;
document.head.appendChild(style);
