/**
 * Company Repair Requests Page JavaScript
 * 
 * Handles all functionality for the company repair requests page including:
 * - Loading and displaying job requests
 * - Managing quotation CRUD operations (Create, Read, Update, Delete)
 * - UI interactions (tabs, modals, filters)
 * - Cost calculations
 * - Form validations
 * 
 * @package FixLanka
 * @version 1.0.0
 */

// ================================================================
// GLOBAL VARIABLES & CONSTANTS
// ================================================================

/**
 * Current company user ID (retrieved from PHP session)
 * @type {number|null}
 */
const currentCompanyId = window.CURRENT_USER_ID || null;

/**
 * Array of available job requests
 * @type {Array}
 */
let availableRequests = [];

/**
 * Array of submitted quotations by the company
 * @type {Array}
 */
let submittedQuotations = [];

/**
 * ID of quotation currently being edited (null if creating new)
 * @type {number|null}
 */
let editingQuotationId = null;

/**
 * DOM element references (initialized after DOM load)
 */
let quotationModal;
let quotationForm;
let requestDetailsModal;

// ================================================================
// INITIALIZATION
// ================================================================

/**
 * Initialize page when DOM is fully loaded
 * Sets up event listeners, loads data, and initializes UI components
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing page...');
    
    // Validate user authentication
    if (!currentCompanyId) {
        console.error('User ID not found. Please login.');
        showToast('User not authenticated. Please login.', 'error');
        return;
    }
    
    console.log('Current Company ID:', currentCompanyId);
    
    // Get DOM elements after DOM is ready
    quotationModal = document.getElementById('quotation-modal');
    quotationForm = document.getElementById('quotation-form');
    requestDetailsModal = document.getElementById('request-details-modal');
    
    console.log('Modal elements:', { quotationModal, quotationForm, requestDetailsModal });
    
    initializeTabs();
    initializeViewToggle();
    initializeFilters();
    initializeModal();
    initializeCostCalculator();
    initializeDateValidation();
    
    // Load initial data from API
    console.log('Starting to load data...');
    loadAvailableRequests();
    loadSubmittedQuotations();
});

// ================================================================
// DATA LOADING FUNCTIONS
// ================================================================

/**
 * Load available job requests from the API
 * Fetches all pending job requests that companies can submit quotations for
 * 
 * @async
 * @returns {Promise<void>}
 */
async function loadAvailableRequests() {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-requests.php?status=pending');
        
        // Check for HTTP errors
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }
        
        const result = await response.json();
        
        if (result.success) {
            availableRequests = result.data || [];
            renderAvailableRequests();
            updateRequestCounts();
        } else {
            showToast(result.message || result.error || 'Failed to load job requests', 'error');
        }
    } catch (error) {
        console.error('Error loading job requests:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

/**
 * Load submitted quotations by the current company
 * Fetches all quotations submitted by the logged-in company user
 * 
 * @async
 * @returns {Promise<void>}
 */
async function loadSubmittedQuotations() {
    try {
        console.log('Loading submitted quotations for user_id:', currentCompanyId);
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?user_id=${currentCompanyId}`);
        
        // Check for HTTP errors
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }
        
        const result = await response.json();
        
        console.log('Quotations API response:', result);
        console.log('Number of quotations:', result.data?.length || 0);
        
        if (result.success) {
            submittedQuotations = result.data || [];
            console.log('Submitted quotations loaded:', submittedQuotations);
            renderSubmittedQuotations();
            updateQuotationCounts();
        } else {
            showToast(result.message || result.error || 'Failed to load quotations', 'error');
        }
    } catch (error) {
        console.error('Error loading quotations:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

// ================================================
// RENDER FUNCTIONS
// ================================================

/**
 * Render available job requests
 */
function renderAvailableRequests() {
    const container = document.querySelector('.requests-grid');
    
    if (!availableRequests || availableRequests.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                <i class="fas fa-inbox" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
                <p style="color: var(--text-secondary);">No job requests available at the moment</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = availableRequests.map(request => createRequestCard(request)).join('');
}

/**
 * Create request card HTML
 */
function createRequestCard(request) {
    const urgencyClass = request.urgency === 'urgent' ? 'high' : 'low';
    const urgencyText = request.urgency === 'urgent' ? 'High Priority' : 'Low Priority';
    const initials = getInitials(request.customer_fname, request.customer_lname);
    const datePosted = formatTimeAgo(request.dateCreated);
    const hasAttachments = request.photos && request.photos.length > 0;
    
    return `
        <article class="request-card">
            <div class="card-header">
                <div>
                    <h3 class="request-title">${escapeHtml(request.title)}</h3>
                    <p class="request-id"><span class="category-label">${escapeHtml(request.category_name || 'General')}</span></p>
                </div>
                <span class="priority-badge ${urgencyClass}">${urgencyText}</span>
            </div>

            <div class="customer-info">
                <div class="customer-avatar">${initials}</div>
                <div class="customer-details">
                    <h4>${escapeHtml(request.customer_fname + ' ' + request.customer_lname)}</h4>
                </div>
                <a href="#" class="view-profile-btn" onclick="event.preventDefault();">View Profile</a>
            </div>

            <div class="request-details">
                <div class="detail-row">
                    <i class="fas fa-map-marker-alt detail-icon"></i>
                    <span class="detail-label">District:</span>
                    <span class="detail-value">${escapeHtml(request.district)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-calendar detail-icon"></i>
                    <span class="detail-label">Date Needed:</span>
                    <span class="detail-value">${formatDate(request.finish_date)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-clock detail-icon"></i>
                    <span class="detail-label">Posted:</span>
                    <span class="detail-value">${datePosted}</span>
                </div>
            </div>

            <div class="request-description">
                <p>${escapeHtml(request.description).substring(0, 150)}${request.description.length > 150 ? '...' : ''}</p>
            </div>

            ${hasAttachments ? `
            <div class="attachments-section">
                <div class="attachments-label">
                    <i class="fas fa-paperclip"></i>
                    Attachments
                </div>
            </div>
            ` : ''}

            <div class="card-actions">
                <button class="action-btn primary" onclick="openQuotationModal(${request.request_id})">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Submit Quotation
                </button>
                <button class="action-btn secondary" onclick="viewRequestDetails(${request.request_id})">
                    <i class="fas fa-eye"></i>
                    View
                </button>
            </div>
        </article>
    `;
}

/**
 * Render submitted quotations in logs tab
 */
function renderSubmittedQuotations() {
    console.log('=== Rendering submitted quotations ===');
    console.log('Total quotations to render:', submittedQuotations.length);
    
    const pendingList = document.getElementById('pending-quotations-list');
    const acceptedList = document.getElementById('accepted-quotations-list');
    const rejectedList = document.getElementById('rejected-quotations-list');
    const successfulList = document.getElementById('successful-contracts-list');
    const draftList = document.getElementById('draft-quotations-list');
    
    const pendingCount = document.getElementById('pending-quotations-count');
    const acceptedCount = document.getElementById('accepted-quotations-count');
    const rejectedCount = document.getElementById('rejected-quotations-count');
    const successfulCount = document.getElementById('successful-contracts-count');
    const draftCount = document.getElementById('draft-quotations-count');
    
    console.log('DOM Elements Check:', {
        pendingList: !!pendingList,
        acceptedList: !!acceptedList,
        rejectedList: !!rejectedList,
        successfulList: !!successfulList,
        draftList: !!draftList,
        pendingCount: !!pendingCount,
        acceptedCount: !!acceptedCount
    });
    
    if (!pendingList) {
        console.error('CRITICAL: pending-quotations-list element not found!');
        return;
    }
    
    const pending = submittedQuotations.filter(q => q.status === 'pending');
    const accepted = submittedQuotations.filter(q => q.status === 'accepted');
    const rejected = submittedQuotations.filter(q => q.status === 'rejected');
    const successful = submittedQuotations.filter(q => q.status === 'successful');
    const draft = []; // Draft quotations would need separate handling
    
    console.log('Quotations by status:', {
        pending: pending.length,
        accepted: accepted.length,
        rejected: rejected.length,
        successful: successful.length,
        draft: draft.length
    });
    
    if (pending.length > 0) {
        console.log('Pending quotations data:', pending);
    }
    
    // Update counts
    if (pendingCount) pendingCount.textContent = pending.length;
    if (acceptedCount) acceptedCount.textContent = accepted.length;
    if (rejectedCount) rejectedCount.textContent = rejected.length;
    if (successfulCount) successfulCount.textContent = successful.length;
    if (draftCount) draftCount.textContent = draft.length;
    
    // Render pending quotations
    if (pendingList) {
        console.log('Rendering pending list. Count:', pending.length);
        if (pending.length === 0) {
            console.log('No pending quotations - showing empty state');
            pendingList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <h3 class="empty-state-title">No Pending Quotations</h3>
                    <p class="empty-state-text">Submit quotations for available job requests to see them here.</p>
                </div>
            `;
        } else {
            console.log('Rendering', pending.length, 'pending quotations');
            const quotationsHTML = pending.map(q => createQuotationLogItem(q)).join('');
            console.log('Generated HTML length:', quotationsHTML.length);
            pendingList.innerHTML = quotationsHTML;
            console.log('Pending quotations rendered successfully');
        }
    } else {
        console.error('Cannot render pending quotations - pendingList element not found');
    }
    
    // Render accepted quotations
    if (acceptedList) {
        if (accepted.length === 0) {
            acceptedList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon accepted">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="empty-state-title">No Accepted Quotations</h3>
                    <p class="empty-state-text">Quotations accepted by customers will appear here.</p>
                </div>
            `;
        } else {
            acceptedList.innerHTML = accepted.map(q => createQuotationLogItem(q, true)).join('');
        }
    }
    
    // Render rejected quotations
    if (rejectedList) {
        if (rejected.length === 0) {
            rejectedList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon rejected">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3 class="empty-state-title">No Rejected Quotations</h3>
                    <p class="empty-state-text">Rejected quotations will appear here for your review.</p>
                </div>
            `;
        } else {
            rejectedList.innerHTML = rejected.map(q => createQuotationLogItem(q, false, true)).join('');
        }
    }
    
    // Render successful contracts
    if (successfulList) {
        if (successful.length === 0) {
            successfulList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon successful">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="empty-state-title">No Completed Contracts</h3>
                    <p class="empty-state-text">Successfully completed contracts will be displayed here.</p>
                </div>
            `;
        } else {
            successfulList.innerHTML = successful.map(q => createQuotationLogItem(q, false, false, true)).join('');
        }
    }
    
    // Render draft quotations
    if (draftList) {
        draftList.innerHTML = `
            <div class="quotations-empty-state">
                <div class="empty-state-icon draft">
                    <i class="fas fa-edit"></i>
                </div>
                <h3 class="empty-state-title">No Draft Quotations</h3>
                <p class="empty-state-text">Save quotations as drafts to complete them later.</p>
            </div>
        `;
    }
}

/**
 * Create quotation log item HTML
 */
function createQuotationLogItem(quotation, isAccepted = false, isRejected = false, isSuccessful = false) {
    const date = new Date(quotation.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    const amount = parseFloat(quotation.total_amount).toFixed(2);
    
    // Determine which icon to show and styling
    let iconClass = 'fas fa-file-invoice-dollar';
    let statusBadge = '';
    let cardClass = 'quotation-card';
    
    if (isSuccessful) {
        iconClass = 'fas fa-check-circle';
        statusBadge = '<span class="status-badge success"><i class="fas fa-check"></i> Completed</span>';
        cardClass += ' successful';
    } else if (isRejected) {
        iconClass = 'fas fa-times-circle';
        statusBadge = '<span class="status-badge danger"><i class="fas fa-times"></i> Rejected</span>';
        cardClass += ' rejected';
    } else if (isAccepted) {
        iconClass = 'fas fa-file-check';
        statusBadge = '<span class="status-badge info"><i class="fas fa-handshake"></i> Accepted</span>';
        cardClass += ' accepted';
    } else {
        statusBadge = '<span class="status-badge warning"><i class="fas fa-clock"></i> Pending</span>';
        cardClass += ' pending';
    }
    
    // Show edit/delete buttons only for pending quotations
    const showActions = !isAccepted && !isRejected && !isSuccessful && quotation.status === 'pending';
    
    return `
        <div class="${cardClass}">
            <div class="quotation-card-main">
                <div class="quotation-card-left">
                    <div class="quotation-icon">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="quotation-info-section">
                        <div class="quotation-header-top">
                            <h4 class="quotation-title">${escapeHtml(quotation.title)}</h4>
                            ${statusBadge}
                        </div>
                        <div class="quotation-meta">
                            <span class="meta-item">
                                <i class="fas fa-hashtag"></i>
                                ${isSuccessful ? 'Contract' : 'Request'} ${quotation.request_id}
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                ${escapeHtml(quotation.customer_fname + ' ' + quotation.customer_lname)}
                            </span>
                            <span class="meta-separator">•</span>
                            <span class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                ${formattedDate}
                            </span>
                        </div>
                        <div class="quotation-amount-inline">
                            <span class="amount-label">Total Amount:</span>
                            <span class="amount-value">LKR ${formatNumber(amount)}</span>
                        </div>
                        ${isRejected && quotation.rejection_reason ? `
                        <div class="quotation-rejection-reason-inline">
                            <i class="fas fa-info-circle"></i>
                            <span><strong>Reason:</strong> ${escapeHtml(quotation.rejection_reason)}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                <div class="quotation-card-right">
                    ${showActions ? `
                    <div class="quotation-actions-vertical">
                        <button class="action-btn small primary" onclick="editQuotation(${quotation.quotation_id})">
                            <i class="fas fa-edit"></i>
                            Edit
                        </button>
                        <button class="action-btn small danger" onclick="deleteQuotation(${quotation.quotation_id})">
                            <i class="fas fa-trash"></i>
                            Delete
                        </button>
                    </div>
                    ` : ''}
                    
                    ${isSuccessful ? `
                    <div class="quotation-actions-vertical">
                        <a href="/2nd-Year-Group-Project/FixLanka/views/company/contracts.php?id=${quotation.quotation_id}" class="action-btn small success">
                            <i class="fas fa-file-contract"></i>
                            View Contract
                        </a>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
}

// ================================================================
// QUOTATION CRUD OPERATIONS
// ================================================================

/**
 * Open quotation modal for creating a new quotation
 * 
 * Displays the quotation form modal pre-populated with job request details.
 * Resets form to empty state for new quotation creation.
 * 
 * @param {number} requestId - The ID of the job request to create a quotation for
 * @returns {void}
 */
function openQuotationModal(requestId) {
    editingQuotationId = null;
    quotationForm.reset();
    
    // Find the requested job request
    const request = availableRequests.find(r => r.request_id === requestId);
    if (!request) {
        showToast('Request not found', 'error');
        return;
    }
    
    // Set the hidden request ID field
    document.getElementById('request-id').value = requestId;
    
    // Display request details in the modal
    const detailsContainer = document.getElementById('quotation-request-details');
    detailsContainer.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0;">${escapeHtml(request.title)}</h4>
        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
            <i class="fas fa-map-marker-alt"></i> ${escapeHtml(request.district)} • 
            <i class="fas fa-calendar"></i> Needed by ${formatDate(request.finish_date)}
        </p>
    `;
    
    // Set minimum dates
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const minDate = tomorrow.toISOString().split('T')[0];
    document.getElementById('estimated-start-date').min = minDate;
    document.getElementById('estimated-completion-date').min = minDate;
    
    // Update modal title
    document.querySelector('#quotation-modal .modal-title').innerHTML = `
        <i class="fas fa-file-invoice-dollar"></i>
        Submit Quotation
    `;
    
    // Show the modal
    quotationModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Load and edit an existing quotation
 * 
 * Fetches quotation data from API and populates the form for editing.
 * Only pending quotations can be edited.
 * 
 * @async
 * @param {number} quotationId - The ID of the quotation to edit
 * @returns {Promise<void>}
 */
async function editQuotation(quotationId) {
    console.log('Editing quotation:', quotationId);
    
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?quotation_id=${quotationId}`);
        
        // Check for HTTP errors
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }
        
        const result = await response.json();
        
        if (result.success && result.data && result.data.length > 0) {
            const quotation = result.data[0];
            
            // Verify quotation is editable (only pending status)
            if (quotation.status !== 'pending') {
                showToast('Only pending quotations can be edited', 'error');
                return;
            }
            
            editingQuotationId = quotationId;
            
            // Populate form
            document.getElementById('request-id').value = quotation.request_id;
            document.getElementById('quotation-title').value = quotation.title;
            document.getElementById('service-description').value = quotation.description || '';
            document.getElementById('labor-cost').value = quotation.labor_cost;
            document.getElementById('material-cost').value = quotation.material_cost;
            document.getElementById('transport-cost').value = quotation.transport_cost || 0;
            document.getElementById('other-cost').value = quotation.other_charges || 0;
            document.getElementById('estimated-start-date').value = quotation.start_date;
            document.getElementById('estimated-completion-date').value = quotation.completion_date;
            document.getElementById('estimated-duration').value = quotation.estimated_duration;
            document.getElementById('payment-terms').value = quotation.payment_terms || '';
            document.getElementById('warranty-period').value = quotation.warranty_period || '';
            document.getElementById('terms-conditions').value = quotation.additional_terms || '';
            
            // Calculate total
            calculateTotal();
            
            // Populate request details
            const detailsContainer = document.getElementById('quotation-request-details');
            detailsContainer.innerHTML = `
                <h4 style="margin: 0 0 0.5rem 0;">${escapeHtml(quotation.job_title)}</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                    <i class="fas fa-map-marker-alt"></i> ${escapeHtml(quotation.district)} • 
                    Request #${quotation.request_id}
                </p>
            `;
            
            // Update modal title
            document.querySelector('#quotation-modal .modal-title').innerHTML = `
                <i class="fas fa-edit"></i>
                Edit Quotation
            `;
            
            // Show modal
            quotationModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            showToast(result.message || result.error || 'Quotation not found', 'error');
        }
    } catch (error) {
        console.error('Error loading quotation:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

/**
 * Submit or update a quotation
 * 
 * Handles both creating new quotations and updating existing ones.
 * Determines action based on editingQuotationId (null = create, number = update).
 * Validates form data and sends appropriate HTTP request (POST or PUT).
 * 
 * @async
 * @returns {Promise<void>}
 */
async function submitQuotation() {
    console.log('Submit quotation called, editing ID:', editingQuotationId);
    
    // Validate all required form fields
    if (!quotationForm.checkValidity()) {
        quotationForm.reportValidity();
        return;
    }
    
    // Collect form data into object
    const formData = {
        request_id: parseInt(document.getElementById('request-id').value),
        user_id: currentCompanyId,
        title: document.getElementById('quotation-title').value,
        description: document.getElementById('service-description').value,
        labor_cost: parseFloat(document.getElementById('labor-cost').value),
        material_cost: parseFloat(document.getElementById('material-cost').value),
        transport_cost: parseFloat(document.getElementById('transport-cost').value) || 0,
        other_charges: parseFloat(document.getElementById('other-cost').value) || 0,
        total_amount: parseFloat(document.getElementById('total-price').value),
        start_date: document.getElementById('estimated-start-date').value,
        completion_date: document.getElementById('estimated-completion-date').value,
        estimated_duration: parseInt(document.getElementById('estimated-duration').value),
        payment_terms: document.getElementById('payment-terms').value,
        warranty_period: document.getElementById('warranty-period').value,
        additional_terms: document.getElementById('terms-conditions').value
    };
    
    console.log('Form data:', formData);
    
    try {
        let response;
        
        if (editingQuotationId) {
            // UPDATE existing quotation
            formData.quotation_id = editingQuotationId;
            console.log('Updating quotation with data:', formData);
            
            response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-quotes.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        } else {
            // CREATE new quotation
            console.log('Creating new quotation with data:', formData);
            
            response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-quotes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        }
        
        // Check if response is ok
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }
        
        const result = await response.json();
        console.log('Response:', result);
        
        if (result.success) {
            showToast(editingQuotationId ? 'Quotation updated successfully!' : 'Quotation submitted successfully!', 'success');
            closeQuotationModal();
            
            console.log('Quotation saved successfully. Reloading data...');
            
            // Reload quotations and wait for it to complete
            await loadSubmittedQuotations();
            await loadAvailableRequests();
            
            console.log('Data reloaded. Switching to logs tab...');
            
            // Switch to logs tab to show the new quotation
            if (!editingQuotationId) {
                // Use setTimeout to ensure DOM is updated
                setTimeout(() => {
                    const logsTab = document.querySelector('[data-tab="logs"]');
                    if (logsTab) {
                        console.log('Clicking logs tab');
                        logsTab.click();
                    } else {
                        console.error('Logs tab not found!');
                    }
                }, 100);
            }
        } else {
            showToast(result.message || result.error || 'Unknown error', 'error');
        }
    } catch (error) {
        console.error('Error saving quotation:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

/**
 * Delete a quotation
 * 
 * Sends DELETE request to API to remove a quotation.
 * Only pending quotations can be deleted. Asks for user confirmation first.
 * 
 * @async
 * @param {number} quotationId - The ID of the quotation to delete
 * @returns {Promise<void>}
 */
async function deleteQuotation(quotationId) {
    console.log('Delete quotation called:', quotationId);
    
    // Confirm deletion with user
    if (!confirm('Are you sure you want to delete this quotation? This action cannot be undone.')) {
        return;
    }
    
    try {
        const deleteUrl = `/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?quotation_id=${quotationId}`;
        console.log('DELETE URL:', deleteUrl);
        
        const response = await fetch(deleteUrl, {
            method: 'DELETE'
        });
        
        // Check for HTTP errors
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }
        
        const result = await response.json();
        console.log('Delete response:', result);
        
        if (result.success) {
            showToast('Quotation deleted successfully!', 'success');
            loadSubmittedQuotations();
        } else {
            showToast(result.message || result.error || 'Failed to delete quotation', 'error');
        }
    } catch (error) {
        console.error('Error deleting quotation:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

/**
 * Close quotation modal
 */
function closeQuotationModal() {
    quotationModal.classList.remove('active');
    document.body.style.overflow = '';
    quotationForm.reset();
    editingQuotationId = null;
}

/**
 * View request details
 */
function viewRequestDetails(requestId) {
    const request = availableRequests.find(r => r.request_id === requestId);
    if (!request) {
        showToast('Request not found', 'error');
        return;
    }
    
    const detailsContainer = document.getElementById('request-details-content');
    detailsContainer.innerHTML = `
        <div style="padding: var(--spacing-md);">
            <h3>${escapeHtml(request.title)}</h3>
            <p><strong>Category:</strong> ${escapeHtml(request.category_name || 'General')}</p>
            <p><strong>District:</strong> ${escapeHtml(request.district)}</p>
            <p><strong>Address:</strong> ${escapeHtml(request.address)}</p>
            <p><strong>Deadline:</strong> ${formatDate(request.finish_date)}</p>
            <p><strong>Urgency:</strong> ${request.urgency}</p>
            <p><strong>Description:</strong></p>
            <p>${escapeHtml(request.description)}</p>
        </div>
    `;
    
    // Set button action
    document.getElementById('submit-quote-from-details').onclick = () => {
        closeRequestDetailsModal();
        openQuotationModal(requestId);
    };
    
    requestDetailsModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Close request details modal
 */
function closeRequestDetailsModal() {
    requestDetailsModal.classList.remove('active');
    document.body.style.overflow = '';
}

// ================================================================
// HELPER FUNCTIONS & UI UTILITIES
// ================================================================

/**
 * Initialize tab switching functionality
 * Sets up click handlers for tab buttons to switch between different content sections
 * 
 * @returns {void}
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const targetTab = button.dataset.tab;
            
            // Remove active class from all tabs and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            button.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
}

/**
 * Initialize view toggle
 */
function initializeViewToggle() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const requestsGrid = document.querySelector('.requests-grid');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', () => {
            viewButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const view = button.dataset.view;
            if (view === 'list') {
                requestsGrid.classList.add('list-view');
            } else {
                requestsGrid.classList.remove('list-view');
            }
        });
    });
}

/**
 * Initialize filters
 */
function initializeFilters() {
    const filters = document.querySelectorAll('.filter-select');
    
    filters.forEach(filter => {
        filter.addEventListener('change', () => {
            // TODO: Implement filtering logic
            console.log('Filter changed:', filter.id, filter.value);
        });
    });
}

/**
 * Initialize modal
 */
function initializeModal() {
    const modalClose = document.querySelector('#quotation-modal .modal-close');
    const cancelBtn = document.querySelector('#quotation-modal .action-btn.secondary');
    const submitBtn = document.querySelector('#quotation-modal .action-btn.primary');
    
    if (modalClose) {
        modalClose.addEventListener('click', closeQuotationModal);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeQuotationModal);
    }
    
    if (submitBtn) {
        submitBtn.addEventListener('click', submitQuotation);
    }
    
    // Close on overlay click
    quotationModal.addEventListener('click', (e) => {
        if (e.target === quotationModal) {
            closeQuotationModal();
        }
    });
    
    // Request details modal
    const detailsModalClose = document.querySelector('#request-details-modal .modal-close');
    const detailsCloseBtn = document.querySelector('#request-details-modal .action-btn.secondary');
    
    if (detailsModalClose) {
        detailsModalClose.addEventListener('click', closeRequestDetailsModal);
    }
    
    if (detailsCloseBtn) {
        detailsCloseBtn.addEventListener('click', closeRequestDetailsModal);
    }
    
    requestDetailsModal.addEventListener('click', (e) => {
        if (e.target === requestDetailsModal) {
            closeRequestDetailsModal();
        }
    });
}

/**
 * Initialize cost calculator
 */
function initializeCostCalculator() {
    const laborCost = document.getElementById('labor-cost');
    const materialCost = document.getElementById('material-cost');
    const transportCost = document.getElementById('transport-cost');
    const otherCost = document.getElementById('other-cost');
    
    [laborCost, materialCost, transportCost, otherCost].forEach(input => {
        if (input) {
            input.addEventListener('input', calculateTotal);
        }
    });
}

/**
 * Calculate total cost
 */
function calculateTotal() {
    const labor = parseFloat(document.getElementById('labor-cost').value) || 0;
    const material = parseFloat(document.getElementById('material-cost').value) || 0;
    const transport = parseFloat(document.getElementById('transport-cost').value) || 0;
    const other = parseFloat(document.getElementById('other-cost').value) || 0;
    
    const subtotal = labor + material + transport + other;
    const total = subtotal;
    
    document.getElementById('subtotal-amount').textContent = `LKR ${formatNumber(subtotal.toFixed(2))}`;
    document.getElementById('total-amount').textContent = `LKR ${formatNumber(total.toFixed(2))}`;
    document.getElementById('total-price').value = total.toFixed(2);
}

/**
 * Initialize date validation
 */
function initializeDateValidation() {
    const startDate = document.getElementById('estimated-start-date');
    const completionDate = document.getElementById('estimated-completion-date');
    
    if (startDate) {
        startDate.addEventListener('change', () => {
            if (completionDate) {
                completionDate.min = startDate.value;
            }
        });
    }
}

/**
 * Update request counts
 */
function updateRequestCounts() {
    const publicCount = document.querySelector('[data-tab="public-requests"] .tab-count');
    const headerPublicCount = document.getElementById('public-requests-count');
    const headerPendingCount = document.getElementById('pending-response-count');
    
    if (publicCount) {
        publicCount.textContent = availableRequests.length;
    }
    if (headerPublicCount) {
        headerPublicCount.textContent = availableRequests.length;
    }
    if (headerPendingCount) {
        const pendingQuotes = submittedQuotations.filter(q => q.status === 'pending').length;
        headerPendingCount.textContent = pendingQuotes;
    }
}

/**
 * Update quotation counts
 */
function updateQuotationCounts() {
    const logsCount = document.querySelector('[data-tab="logs"] .tab-count');
    const headerMonthCount = document.getElementById('this-month-count');
    
    if (logsCount) {
        logsCount.textContent = submittedQuotations.length;
    }
    
    // Count quotations from this month
    if (headerMonthCount) {
        const now = new Date();
        const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
        const thisMonthQuotes = submittedQuotations.filter(q => {
            const quoteDate = new Date(q.created_at);
            return quoteDate >= firstDayOfMonth;
        });
        headerMonthCount.textContent = thisMonthQuotes.length;
    }
}

/**
 * Get initials from name
 */
function getInitials(firstName, lastName) {
    const first = firstName ? firstName.charAt(0).toUpperCase() : '';
    const last = lastName ? lastName.charAt(0).toUpperCase() : '';
    return first + last;
}

/**
 * Format date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

/**
 * Format time ago
 */
function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    const intervals = {
        year: 31536000,
        month: 2592000,
        week: 604800,
        day: 86400,
        hour: 3600,
        minute: 60
    };
    
    for (const [unit, secondsInUnit] of Object.entries(intervals)) {
        const interval = Math.floor(seconds / secondsInUnit);
        if (interval >= 1) {
            return `${interval} ${unit}${interval > 1 ? 's' : ''} ago`;
        }
    }
    
    return 'Just now';
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${type === 'success' ? 'var(--success-color)' : type === 'error' ? 'var(--danger-color)' : 'var(--primary-color)'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-lg);
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span style="margin-left: 0.5rem;">${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Make functions globally accessible
window.openQuotationModal = openQuotationModal;
window.closeQuotationModal = closeQuotationModal;
window.submitQuotation = submitQuotation;
window.editQuotation = editQuotation;
window.deleteQuotation = deleteQuotation;
window.viewRequestDetails = viewRequestDetails;
window.closeRequestDetailsModal = closeRequestDetailsModal;
