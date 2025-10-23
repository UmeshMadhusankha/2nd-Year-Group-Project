// ================================================
// EDIT QUOTE PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Get quote ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const quoteId = urlParams.get('quoteId');
    
    if (!quoteId) {
        showNotification('Invalid quote ID', 'error');
        setTimeout(() => {
            window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
        }, 2000);
        return;
    }
    
    // Load quote data
    loadQuoteData(quoteId);
    
    // Initialize form handlers
    initializeEditForm();
    initializeConfirmationModal();
});

/**
 * Load existing quote data
 */
function loadQuoteData(quoteId) {
    const loadingState = document.getElementById('loading-state');
    const jobDetailsSection = document.getElementById('job-details-section');
    const quoteFormSection = document.getElementById('quote-form-section');
    
    // Fetch quote data from API
    fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?quote_id=${quoteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                const quote = data.data[0];
                
                // Check if quote can be edited
                if (quote.status !== 'pending') {
                    showNotification('This quote cannot be edited. Only pending quotes can be modified.', 'error');
                    setTimeout(() => {
                        window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
                    }, 2000);
                    return;
                }
                
                // Populate form with quote data
                populateForm(quote);
                
                // Hide loading, show form
                loadingState.style.display = 'none';
                jobDetailsSection.style.display = 'block';
                quoteFormSection.style.display = 'block';
            } else {
                throw new Error('Quote not found');
            }
        })
        .catch(error => {
            console.error('Error loading quote:', error);
            showNotification('Failed to load quote data: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
            }, 2000);
        });
}

/**
 * Populate form with existing quote data
 */
function populateForm(quote) {
    console.log('Populating form with quote data:', quote);
    
    // Set hidden fields
    document.getElementById('quote-id').value = quote.quote_id;
    document.getElementById('request-id').value = quote.request_id;
    document.getElementById('repairer-id').value = quote.repairer_id;
    
    // Debug - verify hidden fields were set
    console.log('Hidden fields set:');
    console.log('  quote-id:', document.getElementById('quote-id').value);
    console.log('  request-id:', document.getElementById('request-id').value);
    console.log('  repairer-id:', document.getElementById('repairer-id').value);
    
    // Set form fields
    document.getElementById('quote-amount').value = quote.quoteAmount;
    document.getElementById('estimated-days').value = quote.estimatedDays;
    document.getElementById('warranty-period').value = quote.warrantyPeriod || 0;
    document.getElementById('valid-until').value = quote.validUntil;
    document.getElementById('materials-included').checked = quote.materialsIncluded;
    document.getElementById('quote-message').value = quote.message || '';
    
    // Populate job details (in real app, fetch from JobRequest table)
    document.getElementById('job-title').textContent = `Job Request #${quote.request_id}`;
    document.getElementById('customer-name').textContent = 'Loading...';
    document.getElementById('job-location').textContent = 'Loading...';
    document.getElementById('job-category').textContent = 'Loading...';
    
    // Set minimum date for valid until (tomorrow)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('valid-until').min = tomorrow.toISOString().split('T')[0];
}

/**
 * Initialize edit form handlers
 */
function initializeEditForm() {
    const form = document.getElementById('quote-form');
    const backBtn = document.getElementById('back-to-jobs-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const updateBtn = document.getElementById('update-quote-btn');
    
    // Handle form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleQuoteUpdate();
        });
    }
    
    // Handle back button
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            if (confirm('Discard changes and go back?')) {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
            }
        });
    }
    
    // Handle cancel button
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
            }
        });
    }
}

/**
 * Handle quote update submission
 */
function handleQuoteUpdate() {
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const warrantyPeriod = document.getElementById('warranty-period').value;
    const validUntil = document.getElementById('valid-until').value;
    const materialsIncluded = document.getElementById('materials-included').checked;
    const message = document.getElementById('quote-message').value;
    
    // Validate required fields
    if (!quoteAmount || !estimatedDays || !validUntil || !message) {
        showNotification('Please fill in all required fields.', 'error');
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
    
    // Validate valid until date
    const validUntilDate = new Date(validUntil);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (validUntilDate < today) {
        showNotification('Quote validity date must be in the future.', 'error');
        return;
    }
    
    // Update confirmation modal
    updateConfirmationModal(quoteAmount, estimatedDays, warrantyPeriod, validUntil);
    
    // Show confirmation modal
    showConfirmationModal();
}

/**
 * Initialize confirmation modal
 */
function initializeConfirmationModal() {
    const modalOverlay = document.getElementById('confirmation-modal-overlay');
    const closeBtn = document.getElementById('close-confirmation-modal');
    const cancelBtn = document.getElementById('cancel-confirmation');
    const confirmBtn = document.getElementById('confirm-update');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', hideConfirmationModal);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideConfirmationModal);
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                hideConfirmationModal();
            }
        });
    }
    
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmQuoteUpdate);
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

function updateConfirmationModal(quoteAmount, estimatedDays, warrantyPeriod, validUntil) {
    document.getElementById('confirm-amount').textContent = `Rs. ${parseFloat(quoteAmount).toFixed(2)}`;
    
    const days = parseInt(estimatedDays);
    document.getElementById('confirm-days').textContent = `${days} day${days !== 1 ? 's' : ''}`;
    
    const months = parseInt(warrantyPeriod);
    let warrantyText = 'No warranty';
    if (months === 12) warrantyText = '1 year';
    else if (months === 24) warrantyText = '2 years';
    else if (months > 0) warrantyText = `${months} month${months !== 1 ? 's' : ''}`;
    document.getElementById('confirm-warranty').textContent = warrantyText;
    
    const date = new Date(validUntil);
    document.getElementById('confirm-valid-until').textContent = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Confirm and submit quote update
 */
function confirmQuoteUpdate() {
    const confirmBtn = document.getElementById('confirm-update');
    
    // Show loading state
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    confirmBtn.disabled = true;
    
    // Get form data
    const quoteId = document.getElementById('quote-id').value;
    const repairerId = document.getElementById('repairer-id').value;
    const quoteAmount = document.getElementById('quote-amount').value;
    const estimatedDays = document.getElementById('estimated-days').value;
    const warrantyPeriod = document.getElementById('warranty-period').value;
    const validUntil = document.getElementById('valid-until').value;
    const materialsIncluded = document.getElementById('materials-included').checked;
    const message = document.getElementById('quote-message').value;
    
    // Debug logging - check what we're getting from form
    console.log('Form values BEFORE parsing:');
    console.log('  quoteId (raw):', quoteId, 'type:', typeof quoteId);
    console.log('  repairerId (raw):', repairerId, 'type:', typeof repairerId);
    
    // Prepare update data
    const updateData = {
        quote_id: parseInt(quoteId),
        repairer_id: parseInt(repairerId), // Required for authorization
        quoteAmount: parseFloat(quoteAmount),
        estimatedDays: parseInt(estimatedDays),
        warrantyPeriod: parseInt(warrantyPeriod),
        validUntil: validUntil,
        materialsIncluded: materialsIncluded,
        message: message
    };
    
    // Debug logging - check what we're sending
    console.log('Update data AFTER parsing:');
    console.log('  quote_id:', updateData.quote_id, 'type:', typeof updateData.quote_id, 'isNaN:', isNaN(updateData.quote_id));
    console.log('  repairer_id:', updateData.repairer_id, 'type:', typeof updateData.repairer_id, 'isNaN:', isNaN(updateData.repairer_id));
    console.log('Full updateData object:', JSON.stringify(updateData, null, 2));
    
    // Submit update to API
    fetch('/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(updateData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            hideConfirmationModal();
            showNotification('Quote updated successfully!', 'success');
            
            console.log('Quote updated:', data.data);
            
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Update';
            confirmBtn.disabled = false;
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/available-jobs.php';
            }, 2000);
        } else {
            throw new Error(data.error || 'Failed to update quote');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to update quote: ' + error.message, 'error');
        
        confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Update';
        confirmBtn.disabled = false;
        
        hideConfirmationModal();
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 80px;
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
    
    document.body.appendChild(notification);
    
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => notification.remove());
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Add CSS animation
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
    
    .loading-state-container {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--bg-primary);
        border-radius: var(--border-radius);
        margin: 2rem 0;
    }
    
    .loading-state-container i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .loading-state-container p {
        color: var(--text-secondary);
        font-size: 1rem;
    }
`;
document.head.appendChild(style);
