// ================================================
// SUBMIT QUOTE PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize submit quote page functionality
    initializeQuoteForm();
    initializeBreakdownCalculator();
    initializeConfirmationModal();
    initializeFormValidation();
    
    // Get job ID from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const jobId = urlParams.get('jobId') || '1'; // Default to job 1 if no ID provided
    
    // Get job data based on ID
    const jobData = getJobDataById(jobId);
    
    // Populate job details
    populateJobDetails(jobData);
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

    // Handle back button
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            if (hasUnsavedChanges()) {
                if (confirm('You have unsaved changes. Are you sure you want to go back?')) {
                    window.location.href = 'available-jobs.php';
                }
            } else {
                window.location.href = 'available-jobs.php';
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
    const price = document.getElementById('quote-price').value;
    const days = document.getElementById('completion-days').value || '0';
    const hours = document.getElementById('completion-hours').value || '0';
    const notes = document.getElementById('quote-notes').value;
    const termsAgreed = document.getElementById('terms-agreement').checked;

    // Validate required fields
    if (!price || !notes || !termsAgreed) {
        showNotification('Please fill in all required fields and agree to terms.', 'error');
        return;
    }

    if (!days && !hours) {
        showNotification('Please provide an estimated completion time.', 'error');
        return;
    }

    // Update confirmation modal
    updateConfirmationModal(price, days, hours);
    
    // Show confirmation modal
    showConfirmationModal();
}

// ===== BREAKDOWN CALCULATOR =====
function initializeBreakdownCalculator() {
    const addItemBtn = document.getElementById('add-breakdown-item');
    const breakdownItems = document.getElementById('breakdown-items');

    if (addItemBtn) {
        addItemBtn.addEventListener('click', addBreakdownItem);
    }

    // Initialize existing item listeners
    updateBreakdownListeners();
    updateBreakdownTotal();
}

function addBreakdownItem() {
    const breakdownItems = document.getElementById('breakdown-items');
    const newItem = document.createElement('div');
    newItem.className = 'breakdown-item';
    
    newItem.innerHTML = `
        <div class="breakdown-input-group">
            <input type="text" class="breakdown-description" placeholder="Item description (e.g., New faucet cartridge)">
            <input type="number" class="breakdown-cost" placeholder="Cost" min="0" step="0.01">
            <button type="button" class="btn-remove-item" title="Remove item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    breakdownItems.appendChild(newItem);
    updateBreakdownListeners();
}

function updateBreakdownListeners() {
    const removeButtons = document.querySelectorAll('.btn-remove-item');
    const costInputs = document.querySelectorAll('.breakdown-cost');

    // Remove item listeners
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.breakdown-item').remove();
            updateBreakdownTotal();
        });
    });

    // Cost change listeners
    costInputs.forEach(input => {
        input.addEventListener('input', updateBreakdownTotal);
    });
}

function updateBreakdownTotal() {
    const costInputs = document.querySelectorAll('.breakdown-cost');
    let total = 0;

    costInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });

    document.getElementById('breakdown-total').textContent = `Rs. ${total.toFixed(2)}`;
}

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

function updateConfirmationModal(price, days, hours) {
    const confirmPrice = document.getElementById('confirm-price');
    const confirmTime = document.getElementById('confirm-time');

    if (confirmPrice) {
        confirmPrice.textContent = `Rs. ${parseFloat(price).toFixed(2)}`;
    }

    if (confirmTime) {
        const timeText = formatTime(parseInt(days), parseInt(hours));
        confirmTime.textContent = timeText;
    }
}

function formatTime(days, hours) {
    const parts = [];
    if (days > 0) parts.push(`${days} day${days !== 1 ? 's' : ''}`);
    if (hours > 0) parts.push(`${hours} hour${hours !== 1 ? 's' : ''}`);
    return parts.length > 0 ? parts.join(', ') : '0 hours';
}

function confirmQuoteSubmission() {
    const confirmBtn = document.getElementById('confirm-submission');
    
    // Show loading state
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    confirmBtn.disabled = true;

    // Simulate API call
    setTimeout(() => {
        // Hide modal
        hideConfirmationModal();
        
        // Show success message
        showNotification('Quote submitted successfully! The customer will be notified.', 'success');
        
        // Clear form
        clearForm();
        
        // Reset button
        confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm & Submit';
        confirmBtn.disabled = false;
        
        // Redirect after delay
        setTimeout(() => {
            window.location.href = 'my-jobs.php';
        }, 2000);
        
    }, 2000); // Simulate 2 second delay
}

// ===== FORM VALIDATION =====
function initializeFormValidation() {
    const priceInput = document.getElementById('quote-price');
    const notesInput = document.getElementById('quote-notes');
    const termsCheckbox = document.getElementById('terms-agreement');

    // Real-time validation
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            validatePrice(this);
        });
    }

    if (notesInput) {
        notesInput.addEventListener('input', function() {
            validateNotes(this);
        });
    }

    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            validateTerms(this);
        });
    }
}

function validatePrice(input) {
    const value = parseFloat(input.value);
    const isValid = value > 0;
    
    toggleFieldValidation(input, isValid);
    return isValid;
}

function validateNotes(input) {
    const isValid = input.value.trim().length >= 10;
    
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
    const price = document.getElementById('quote-price').value;
    const notes = document.getElementById('quote-notes').value;
    const days = document.getElementById('completion-days').value;
    const hours = document.getElementById('completion-hours').value;

    return price || notes || days || hours;
}

function clearForm() {
    const form = document.getElementById('quote-form');
    if (form) {
        form.reset();
        
        // Clear breakdown items except the first one
        const breakdownItems = document.getElementById('breakdown-items');
        const items = breakdownItems.querySelectorAll('.breakdown-item');
        for (let i = 1; i < items.length; i++) {
            items[i].remove();
        }
        
        // Clear first item
        const firstItem = items[0];
        if (firstItem) {
            firstItem.querySelector('.breakdown-description').value = '';
            firstItem.querySelector('.breakdown-cost').value = '';
        }
        
        updateBreakdownTotal();
    }
}

function saveDraft() {
    const price = document.getElementById('quote-price').value;
    const days = document.getElementById('completion-days').value;
    const hours = document.getElementById('completion-hours').value;
    const notes = document.getElementById('quote-notes').value;

    if (!price && !notes && !days && !hours) {
        showNotification('Nothing to save.', 'info');
        return;
    }

    // Simulate saving to local storage or API
    const draftData = {
        price,
        days,
        hours,
        notes,
        timestamp: new Date().toISOString()
    };

    localStorage.setItem('quote-draft', JSON.stringify(draftData));
    showNotification('Draft saved successfully!', 'success');
}

function setupAutoSave() {
    let autoSaveTimeout;
    
    const inputs = [
        document.getElementById('quote-price'),
        document.getElementById('completion-days'),
        document.getElementById('completion-hours'),
        document.getElementById('quote-notes')
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
