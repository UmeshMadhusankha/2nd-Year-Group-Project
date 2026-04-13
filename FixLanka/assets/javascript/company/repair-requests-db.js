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
// NOTE: Use var to avoid redeclaration errors when multiple company scripts are loaded together.
var currentCompanyId = window.CURRENT_USER_ID || window.CURRENT_COMPANY_ID || window.currentCompanyId || null;

/**
 * Optional widget config (used when embedding Repair Requests UI inside other pages, e.g. dashboard)
 * @type {object|null}
 */
const repairRequestsWidgetConfig = window.REPAIR_REQUESTS_WIDGET_CONFIG || null;

/**
 * True when this script runs in embedded widget mode
 * @type {boolean}
 */
const isWidgetMode = Boolean(repairRequestsWidgetConfig && repairRequestsWidgetConfig.enabled);

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
 * Company default settings for auto-filling quotations
 * @type {Object|null}
 */
let companyDefaults = null;

/**
 * ID of quotation currently being edited (null if creating new)
 * @type {number|null}
 */
let editingQuotationId = null;

/**
 * Prevents duplicate work schedule event bindings
 * @type {boolean}
 */
let workScheduleInitialized = false;

/**
 * DOM element references (initialized after DOM load)
 */
let quotationModal;
let quotationForm;
let requestDetailsModal;

// Cache for direct job requests (directjobrequest table)
let directRequestsCache = [];

/**
 * Pending action requested via URL params (repair-requests.php?request_id=...&action=quote|details)
 * @type {{requestId:number, action:'quote'|'details'|null}|null}
 */
let pendingUrlAction = null;

// ================================================================
// INITIALIZATION
// ================================================================

/**
 * Initialize page when DOM is fully loaded
 * Sets up event listeners, loads data, and initializes UI components
 */
document.addEventListener('DOMContentLoaded', function () {

    // Validate user authentication
    if (!currentCompanyId) {
        console.error('User ID not found. Please login.');
        showToast('User not authenticated. Please login.', 'error');
        return;
    }


    // Get DOM elements after DOM is ready
    quotationModal = document.getElementById('quotation-modal');
    quotationForm = document.getElementById('quotation-form');
    requestDetailsModal = document.getElementById('request-details-modal');

    // Capture URL-driven actions (only used on full page)
    if (!isWidgetMode) {
        const params = new URLSearchParams(window.location.search || '');
        const requestId = Number(params.get('request_id') || params.get('requestId') || 0);
        const actionRaw = String(params.get('action') || '').toLowerCase();
        const action = (actionRaw === 'quote' || actionRaw === 'details') ? actionRaw : null;
        if (requestId > 0 && action) {
            pendingUrlAction = { requestId, action };
        }
    }


    // Full-page-only UI (tabs / filters / view toggles)
    if (!isWidgetMode) {
        initializeTabs();
        initializeViewToggle();
        initializeFilters();
    }

    // Shared UI (modals + pricing + date validation)
    if (quotationModal && quotationForm && requestDetailsModal) {
        initializeModal();
        initializeCostCalculator();
        initializeDateValidation();
    }

    // Load company defaults for auto-filling
    loadCompanyDefaults();

    // Load initial data from API
    const shouldLoadRequests = !isWidgetMode || (repairRequestsWidgetConfig.loadRequests !== false);
    if (shouldLoadRequests) {
        loadAvailableRequests();
    }

    const shouldLoadQuotations = !isWidgetMode || (repairRequestsWidgetConfig.loadQuotations !== false);
    if (shouldLoadQuotations) {
        loadSubmittedQuotations();
    }
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
            availableRequests = (result.data || []).map(r => ({
                ...r,
                created_at: r.created_at || r.dateCreated || null
            }));

            if (isWidgetMode) {
                const limit = Number(repairRequestsWidgetConfig.limit || 0);
                availableRequests = [...availableRequests].sort((a, b) => {
                    const dateDiff = (new Date(b.created_at || 0)) - (new Date(a.created_at || 0));
                    if (dateDiff !== 0) return dateDiff;
                    return Number(b.request_id || 0) - Number(a.request_id || 0);
                });
                if (limit > 0) {
                    availableRequests = availableRequests.slice(0, limit);
                }
            }

            renderAvailableRequests();

            // If the page was opened with a request action, apply it after data load
            if (!isWidgetMode) {
                await applyPendingUrlAction();
            }

            const shouldShowCounts = !isWidgetMode || (repairRequestsWidgetConfig.showCounts !== false);
            if (shouldShowCounts) {
                updateRequestCounts();
            }
        } else {
            showToast(result.message || result.error || 'Failed to load job requests', 'error');
        }
    } catch (error) {
        console.error('Error loading job requests:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

async function applyPendingUrlAction() {
    if (!pendingUrlAction) return;
    const { requestId, action } = pendingUrlAction;
    pendingUrlAction = null;

    // Ensure data exists (fallback by id) then open requested modal
    if (action === 'quote') {
        await openQuotationModal(requestId);
    } else {
        await viewRequestDetails(requestId);
    }

    // Clean URL so refresh doesn't reopen
    try {
        const url = new URL(window.location.href);
        url.searchParams.delete('request_id');
        url.searchParams.delete('requestId');
        url.searchParams.delete('action');
        window.history.replaceState({}, document.title, url.toString());
    } catch {
        // ignore
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

        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?user_id=${currentCompanyId}`);

        // Check for HTTP errors
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status}): ${errorText.substring(0, 200)}`, 'error');
            return;
        }

        const result = await response.json();



        if (result.success) {
            submittedQuotations = result.data || [];

            renderSubmittedQuotations();
            updateQuotationCounts();
            // Successful Contracts section should show completed projects (not contracts)
            loadCompletedProjectsForLogs();
        } else {
            showToast(result.message || result.error || 'Failed to load quotations', 'error');
        }
    } catch (error) {
        console.error('Error loading quotations:', error);
        showToast(`Error: ${error.message}`, 'error');
    }
}

/**
 * Load company default settings for auto-filling quotations
 * Fetches default payment terms, warranty, terms template, etc.
 * 
 * @async
 * @returns {Promise<void>}
 */
async function loadCompanyDefaults() {
    try {

        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-defaults.php?user_id=${currentCompanyId}`);

        if (!response.ok) {
            console.warn('Could not load company defaults, will use system defaults');
            return;
        }

        const result = await response.json();

        if (result.success && result.data) {
            companyDefaults = result.data;

        }
    } catch (error) {
        console.warn('Error loading company defaults:', error);
        // Continue without defaults - not critical
    }
}

// ================================================
// RENDER FUNCTIONS
// ================================================

/**
 * Render available job requests
 */
function renderAvailableRequests() {
    const containerSelector = (isWidgetMode && repairRequestsWidgetConfig && repairRequestsWidgetConfig.containerSelector)
        ? repairRequestsWidgetConfig.containerSelector
        : '.requests-grid';
    const container = document.querySelector(containerSelector);

    if (!container) {
        // In widget mode, the host page may intentionally not provide a render container.
        if (isWidgetMode) return;
        console.warn('Requests container not found:', containerSelector);
        return;
    }

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
    const initials = getInitialsFromFullName(request.customer_name || '');
    const datePosted = formatTimeAgo(request.created_at);
    const hasAttachments = request.photos && request.photos.length > 0;

    // Calculate days until expiry
    const deadline = new Date(request.finish_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    deadline.setHours(0, 0, 0, 0);
    const daysUntilExpiry = Math.ceil((deadline - today) / (1000 * 60 * 60 * 24));

    // Determine if expiring soon (within 3 days)
    const isExpiringSoon = daysUntilExpiry > 0 && daysUntilExpiry <= 3;
    const expiryWarning = isExpiringSoon
        ? `<div class="expiry-warning">
               <i class="fas fa-exclamation-triangle"></i>
               Expires in ${daysUntilExpiry} day${daysUntilExpiry !== 1 ? 's' : ''}!
           </div>`
        : '';

    return `
        <article class="request-card ${isExpiringSoon ? 'expiring-soon' : ''}">
            ${expiryWarning}
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
                    <h4>${escapeHtml(request.customer_name || 'Unknown Customer')}</h4>
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


    if (!pendingList) {
        console.error('CRITICAL: pending-quotations-list element not found!');
        return;
    }

    const pending = submittedQuotations.filter(q => q.status === 'pending');
    const isContractSent = (q) => {
        const sent = q?.contract_sent_to_customer ?? q?.sent_to_customer ?? 0;
        return Number(sent) === 1;
    };

    // Accepted section should also include items where a contract was already sent
    const accepted = submittedQuotations.filter(q => q.status === 'accepted' || isContractSent(q));
    const rejected = submittedQuotations.filter(q => q.status === 'rejected');
    // NOTE: Successful Contracts UI is now driven by completed projects, not quotations.
    const draft = []; // Draft quotations would need separate handling


    if (pending.length > 0) {

    }

    // Update counts
    if (pendingCount) pendingCount.textContent = pending.length;
    if (acceptedCount) acceptedCount.textContent = accepted.length;
    if (rejectedCount) rejectedCount.textContent = rejected.length;
    if (successfulCount) successfulCount.textContent = '0';
    if (draftCount) draftCount.textContent = draft.length;

    // Render pending quotations
    if (pendingList) {

        if (pending.length === 0) {

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

            const quotationsHTML = pending.map(q => createQuotationLogItem(q)).join('');

            pendingList.innerHTML = quotationsHTML;

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

    // Successful contracts list is rendered by loadCompletedProjectsForLogs()
    if (successfulList) {
        successfulList.innerHTML = `
            <div class="quotations-empty-state">
                <div class="empty-state-icon successful">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <h3 class="empty-state-title">Loading Completed Projects...</h3>
                <p class="empty-state-text">Please wait while we fetch your completed projects.</p>
            </div>
        `;
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
 * Load completed projects and render them in the "Successful Contracts" section.
 * This section is intended to show completed projects (not contract cards).
 */
async function loadCompletedProjectsForLogs() {
    const successfulList = document.getElementById('successful-contracts-list');
    const successfulCount = document.getElementById('successful-contracts-count');

    if (!successfulList) return;

    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000);

        const response = await fetch(
            `/2nd-Year-Group-Project/FixLanka/api/projects.php?company_id=${encodeURIComponent(currentCompanyId)}&status=completed`,
            { signal: controller.signal }
        );

        clearTimeout(timeoutId);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Completed projects API error:', errorText);
            successfulList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon rejected">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3 class="empty-state-title">Failed to load completed projects</h3>
                    <p class="empty-state-text">Please try again later.</p>
                </div>
            `;
            if (successfulCount) successfulCount.textContent = '0';
            return;
        }

        const result = await response.json();
        const projects = (result && result.success && Array.isArray(result.data)) ? result.data : [];

        if (successfulCount) successfulCount.textContent = String(projects.length);

        if (projects.length === 0) {
            successfulList.innerHTML = `
                <div class="quotations-empty-state">
                    <div class="empty-state-icon successful">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3 class="empty-state-title">No Completed Projects</h3>
                    <p class="empty-state-text">Completed projects with positive outcomes will be displayed here.</p>
                </div>
            `;
            return;
        }

        successfulList.innerHTML = projects.map(p => createCompletedProjectLogItem(p)).join('');
    } catch (error) {
        console.error('Error loading completed projects:', error);
        successfulList.innerHTML = `
            <div class="quotations-empty-state">
                <div class="empty-state-icon rejected">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3 class="empty-state-title">Failed to load completed projects</h3>
                <p class="empty-state-text">Please try again later.</p>
            </div>
        `;
        if (successfulCount) successfulCount.textContent = '0';
    }
}

function createCompletedProjectLogItem(project) {
    const title = project.title || 'Completed Project';
    const projectId = project.project_id || project.id || '';
    const customerName = `${project.customer_first_name || ''} ${project.customer_last_name || ''}`.trim() || 'Customer';

    // Show an approximate month label (prefer end_date, else start_date)
    const dateSource = project.end_date || project.start_date || null;
    let formattedDate = '-';
    if (dateSource) {
        const d = new Date(dateSource);
        formattedDate = d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    }

    const amountValue = (project.final_cost != null ? project.final_cost : project.budget);
    const amount = (amountValue != null && amountValue !== '')
        ? parseFloat(amountValue).toFixed(2)
        : null;

    return `
        <div class="quotation-card successful">
            <div class="quotation-card-main">
                <div class="quotation-card-left">
                    <div class="quotation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="quotation-info-section">
                        <div class="quotation-header-top">
                            <h4 class="quotation-title">${escapeHtml(title)}</h4>
                            <span class="status-badge success"><i class="fas fa-check"></i> Completed</span>
                        </div>
                        <div class="quotation-meta">
                            <span class="meta-item">
                                <i class="fas fa-hashtag"></i>
                                Project ${escapeHtml(String(projectId))}
                            </span>
                            <span class="meta-separator">&bull;</span>
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                ${escapeHtml(customerName)}
                            </span>
                            <span class="meta-separator">&bull;</span>
                            <span class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                ${formattedDate}
                            </span>
                        </div>
                        ${project.labor_cost || project.material_cost || project.transport_cost ? `
                        <div class="quotation-cost-breakdown">
                            ${project.labor_cost ? `
                            <div class="cost-line">
                                <span class="cost-label">Labor${formatUnitSuffix(project.labor_unit_label)}:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(project.labor_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                            ${project.material_cost ? `
                            <div class="cost-line">
                                <span class="cost-label">Material${formatUnitSuffix(project.material_unit_label)}:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(project.material_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                            ${project.transport_cost && parseFloat(project.transport_cost) > 0 ? `
                            <div class="cost-line">
                                <span class="cost-label">Transport:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(project.transport_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div class="quotation-card-right">
                    <div class="quotation-actions-vertical">
                        <a href="/2nd-Year-Group-Project/FixLanka/views/company/projects.php" class="action-btn small success">
                            <i class="fas fa-project-diagram"></i>
                            View Projects
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
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

    const contractSent = Number(quotation?.contract_sent_to_customer ?? quotation?.sent_to_customer ?? 0) === 1;

    if (isSuccessful) {
        iconClass = 'fas fa-check-circle';
        statusBadge = '<span class="status-badge success"><i class="fas fa-check"></i> Completed</span>';
        cardClass += ' successful';
    } else if (isRejected) {
        iconClass = 'fas fa-times-circle';
        statusBadge = '<span class="status-badge danger"><i class="fas fa-times"></i> Rejected</span>';
        cardClass += ' rejected';
    } else if (isAccepted) {
        iconClass = contractSent ? 'fas fa-paper-plane' : 'fas fa-file-check';
        statusBadge = contractSent
            ? '<span class="status-badge info"><i class="fas fa-paper-plane"></i> Contract Sent</span>'
            : '<span class="status-badge info"><i class="fas fa-handshake"></i> Accepted</span>';
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
                            <span class="meta-separator">&bull;</span>
                            <span class="meta-item">
                                <i class="fas fa-user"></i>
                                ${escapeHtml(quotation.customer_fname + ' ' + quotation.customer_lname)}
                            </span>
                            <span class="meta-separator">&bull;</span>
                            <span class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                ${formattedDate}
                            </span>
                        </div>
                        <div class="quotation-cost-breakdown">
                            ${quotation.labor_cost ? `
                            <div class="cost-line">
                                <span class="cost-label">Labor${formatUnitSuffix(quotation.labor_unit_label)}:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(quotation.labor_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                            ${quotation.material_cost ? `
                            <div class="cost-line">
                                <span class="cost-label">Material${formatUnitSuffix(quotation.material_unit_label)}:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(quotation.material_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                            ${quotation.transport_cost && parseFloat(quotation.transport_cost) > 0 ? `
                            <div class="cost-line">
                                <span class="cost-label">Transport:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(quotation.transport_cost).toFixed(2))}</span>
                            </div>
                            ` : ''}
                            ${quotation.other_charges && parseFloat(quotation.other_charges) > 0 ? `
                            <div class="cost-line">
                                <span class="cost-label">Other Charges:</span>
                                <span class="cost-value">LKR ${formatNumber(parseFloat(quotation.other_charges).toFixed(2))}</span>
                            </div>
                            ` : ''}
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
async function openQuotationModal(requestId) {
    editingQuotationId = null;
    quotationForm.reset();

    // Find the requested job request (fallback to fetch-by-id when not preloaded)
    await ensureRequestLoaded(requestId);

    const request = availableRequests.find(r => Number(r.request_id) === Number(requestId));
    if (!request) {
        showToast('Request not found', 'error');
        return;
    }

    // ============================================
    // CHECK IF REQUEST IS EXPIRED
    // ============================================
    const requestDeadline = new Date(request.finish_date);
    const currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); // Reset time for date-only comparison
    requestDeadline.setHours(0, 0, 0, 0);

    if (requestDeadline < currentDate) {
        // Request has expired - cannot submit quotation
        showToast('Cannot submit quotation - This request has expired', 'error', 5000);

        // Show detailed error modal
        const daysExpired = Math.ceil((currentDate - requestDeadline) / (1000 * 60 * 60 * 24));
        await window.showAlert(
            `Request Expired\n\n` +
            `This service request expired ${daysExpired} day(s) ago.\n` +
            `Deadline was: ${formatDate(request.finish_date)}\n\n` +
            `You cannot submit quotations for expired requests.`
        );
        return; // Block quotation submission
    }

    // Set the hidden request ID field
    document.getElementById('request-id').value = requestId;

    // Display request details in the modal
    const detailsContainer = document.getElementById('quotation-request-details');
    detailsContainer.innerHTML = `
        <h4 style="margin: 0 0 0.5rem 0;">${escapeHtml(request.title)}</h4>
        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
            <i class="fas fa-map-marker-alt"></i> ${escapeHtml(request.district)} &bull; 
            <i class="fas fa-calendar"></i> Needed by ${formatDate(request.finish_date)}
        </p>
    `;

    // ============================================
    // AUTO-FILL FIELDS
    // ============================================

    // 1. Auto-fill Quotation Title from request title
    if (request.title) {
        document.getElementById('quotation-title').value = escapeHtml(request.title);
    }

    // 2. Auto-fill Service Description with request description as template
    if (request.description) {
        const serviceDesc = document.getElementById('service-description');
        serviceDesc.value = `Based on your request:\n\n${escapeHtml(request.description)}\n\nWe will provide the following services:\n`;
        // Set cursor at the end for easy editing
        setTimeout(() => serviceDesc.setSelectionRange(serviceDesc.value.length, serviceDesc.value.length), 100);
    }

    // 3. Auto-fill Estimated Start Date (today + lead time or + 3 days default)
    const today = new Date();
    const leadTimeDays = companyDefaults?.standard_lead_time_days || 3;
    const startDate = new Date(today);
    startDate.setDate(startDate.getDate() + leadTimeDays);
    document.getElementById('estimated-start-date').value = startDate.toISOString().split('T')[0];

    // 4. Auto-fill Estimated Completion Date with intelligent logic
    let completionDate;

    if (request.finish_date) {
        // Customer has a preferred deadline
        const customerDeadline = new Date(request.finish_date);
        const minCompletionDate = new Date(startDate);
        minCompletionDate.setDate(minCompletionDate.getDate() + 1); // At least 1 day after start

        // If customer deadline is AFTER our start date, use it as a target
        if (customerDeadline > startDate) {
            completionDate = customerDeadline;
        } else {
            // Customer deadline is in the past or before start date
            // Use a reasonable default: start date + 7 days
            completionDate = new Date(startDate);
            completionDate.setDate(completionDate.getDate() + 7);
        }
    } else {
        // No customer deadline, use default: start date + 7 days
        completionDate = new Date(startDate);
        completionDate.setDate(completionDate.getDate() + 7);
    }

    document.getElementById('estimated-completion-date').value = completionDate.toISOString().split('T')[0];

    // 5. Auto-calculate daily hours and duration (respects work schedule type)
    updateDailyWorkHours();
    autoCalculateDuration();

    // 6. Auto-fill Payment Method (default to empty for user selection)
    if (companyDefaults?.default_payment_terms) {
        const paymentTermsMap = {
            'milestone': 'milestone',
            'on_completion': 'completion',
            'time_material': 'time_material'
        };
        const mappedMethod = paymentTermsMap[companyDefaults.default_payment_terms] || '';
        document.getElementById('payment-method').value = mappedMethod;
    } else {
        document.getElementById('payment-method').value = ''; // Default to placeholder
    }
    // Trigger payment method info update
    updatePaymentMethodInfo();

    // 7. Auto-fill Warranty Period from company defaults
    if (companyDefaults?.default_warranty) {
        document.getElementById('warranty-period').value = companyDefaults.default_warranty;
    } else {
        document.getElementById('warranty-period').value = '6_months'; // Default fallback
    }

    // 8. Auto-fill Quotation Validity from company defaults
    if (companyDefaults?.quotation_validity_days) {
        document.getElementById('validity-period').value = companyDefaults.quotation_validity_days;
    } else {
        document.getElementById('validity-period').value = '30'; // Default 30 days
    }

    // 9. Auto-fill Terms & Conditions from company template
    if (companyDefaults?.standard_terms_template) {
        document.getElementById('terms-conditions').value = companyDefaults.standard_terms_template;
    }

    // 10. Set minimum dates
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

    // Initialize work schedule features after modal is shown
    setTimeout(() => {
        initializeWorkSchedule();
    }, 100);

    // Show auto-fill notification
    showToast('Form auto-filled with available data. Please review and adjust as needed.', 'info', 3000);
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

            // Map payment_method (if available) or derive from payment_terms
            if (quotation.payment_method) {
                document.getElementById('payment-method').value = quotation.payment_method;
            } else if (quotation.payment_terms) {
                // Try to map old payment_terms to new payment_method
                const oldTerms = quotation.payment_terms.toLowerCase();
                if (oldTerms.includes('100%') || oldTerms.includes('advance')) {
                    document.getElementById('payment-method').value = 'upfront_final';
                } else if (oldTerms.includes('50')) {
                    document.getElementById('payment-method').value = '50-50';
                } else if (oldTerms.includes('30')) {
                    document.getElementById('payment-method').value = '30-70';
                } else if (oldTerms.includes('hourly') || oldTerms.includes('time')) {
                    document.getElementById('payment-method').value = 'time_material';
                } else {
                    document.getElementById('payment-method').value = 'milestone';
                }
            }
            updatePaymentMethodInfo();

            // Populate budget_type if available
            if (quotation.budget_type) {
                document.querySelector(`input[name="budget_type"][value="${quotation.budget_type}"]`).checked = true;
                updateBudgetDisplay();
            }

            // Populate hourly_rate if available
            if (quotation.hourly_rate) {
                document.getElementById('hourly-rate').value = quotation.hourly_rate;
            }

            document.getElementById('warranty-period').value = quotation.warranty_period || '';
            document.getElementById('terms-conditions').value = quotation.additional_terms || '';

            // Calculate total
            calculateTotal();

            // Populate request details
            const detailsContainer = document.getElementById('quotation-request-details');
            detailsContainer.innerHTML = `
                <h4 style="margin: 0 0 0.5rem 0;">${escapeHtml(quotation.job_title)}</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                    <i class="fas fa-map-marker-alt"></i> ${escapeHtml(quotation.district)} &bull; 
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

    // Validate all required form fields
    if (!quotationForm.checkValidity()) {
        quotationForm.reportValidity();
        return;
    }

    // ===== COLLECT BUSINESS LOGIC FIELDS (NEW) =====
    const budget_type = document.querySelector('input[name="budget_type"]:checked')?.value || 'fixed';
    const payment_method = document.getElementById('payment-method')?.value || 'milestone';
    const hourly_rate = document.getElementById('hourly-rate')?.value || null;
    const spending_cap_multiplier = document.getElementById('spending-cap')?.value || 1.5;

    // Auto-determine pricing_type based on labor pricing method
    let pricing_type = 'fixed_price';
    let labor_unit_label = null;
    const laborMethod = document.querySelector('input[name="labor_pricing_method"]:checked')?.value;

    if (laborMethod === 'hourly') {
        pricing_type = 'time_based';
        labor_unit_label = 'per hour';
    } else if (laborMethod === 'per_sqm') {
        pricing_type = 'hybrid';
        labor_unit_label = 'per m²';
    } else if (laborMethod === 'per_unit') {
        pricing_type = 'hybrid';
        labor_unit_label = 'per unit';
    }

    // Determine material unit label
    let material_unit_label = null;
    if (document.getElementById('vendor-supplies-materials')?.checked) {
        const materialMethod = document.querySelector('input[name="material_pricing_method"]:checked')?.value;
        if (materialMethod === 'per_sqm') {
            material_unit_label = 'per m²';
        } else if (materialMethod === 'per_unit') {
            material_unit_label = 'per unit';
        }
    }

    if (!labor_unit_label) {
        const laborLabelText = document.getElementById('labor-unit-label')?.textContent?.trim() || '';
        labor_unit_label = laborLabelText.replace(/[()]/g, '').trim() || null;
    }

    if (!material_unit_label) {
        const materialLabelText = document.getElementById('material-unit-label')?.textContent?.trim() || '';
        material_unit_label = materialLabelText.replace(/[()]/g, '').trim() || null;
    }

    // Override if payment method is time_material
    if (payment_method === 'time_material') {
        pricing_type = 'time_based';
    }

    // ===== AUTO-GENERATE payment_terms FROM payment_method =====
    // Map payment_method to payment_terms for backwards compatibility
    let payment_terms = '';
    switch (payment_method) {
        case 'milestone':
            payment_terms = 'Milestone-based Payment - Payment released at project milestones';
            break;
        case '50-50':
            payment_terms = '50% Advance, 50% on Completion';
            break;
        case '30-70':
            payment_terms = '30% Advance, 70% on Completion';
            break;
        case 'upfront_final':
            payment_terms = '100% Advance Payment';
            break;
        case 'time_material':
            payment_terms = `Time & Material - Hourly Rate: LKR ${hourly_rate || 0}`;
            break;
        default:
            payment_terms = 'As per payment method selected';
    }
    // ===== END BUSINESS LOGIC FIELDS =====

    // Additional validation: Check if hourly rate is required for Time & Material
    if (payment_method === 'time_material' && (!hourly_rate || parseFloat(hourly_rate) <= 0)) {
        showToast('Hourly rate is required when using Time & Material payment method', 'error');
        document.getElementById('hourly-rate')?.focus();
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
        payment_terms: payment_terms, // Auto-generated from payment_method
        warranty_period: document.getElementById('warranty-period').value,
        additional_terms: document.getElementById('terms-conditions').value,
        // ===== NEW BUSINESS LOGIC FIELDS =====
        budget_type: budget_type,
        payment_method: payment_method,
        hourly_rate: hourly_rate,
        spending_cap_multiplier: spending_cap_multiplier,
        pricing_type: pricing_type,
        labor_unit_label: labor_unit_label,
        material_unit_label: material_unit_label,

        // Also include work schedule fields
        work_schedule_type: document.querySelector('input[name="work_schedule_type"]:checked')?.value || 'weekdays_only',
        working_days_per_week: parseInt(document.getElementById('working-days-per-week')?.value) || 5,
        daily_work_hours: parseFloat(document.getElementById('daily-work-hours')?.value) || 8.00,
        work_start_time: document.getElementById('work-start-time')?.value || '08:00:00',
        work_end_time: document.getElementById('work-end-time')?.value || '17:00:00'
    };


    try {
        let response;

        if (editingQuotationId) {
            // UPDATE existing quotation using ENHANCED endpoint
            formData.quotation_id = editingQuotationId;

            response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?action=update_enhanced', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        } else {
            // CREATE new quotation using ENHANCED endpoint

            response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?action=create_enhanced', {
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

        if (result.success) {
            showToast(editingQuotationId ? 'Quotation updated successfully!' : 'Quotation submitted successfully!', 'success');

            // Short undo window for newly submitted quotation
            if (!editingQuotationId && typeof window.showUndoToast === 'function') {
                const quotationId = result.data?.quotation_id || result.data?.id;
                const seconds = (result.undo && result.undo.undo_seconds) ? Number(result.undo.undo_seconds) : 30;
                if (quotationId) {
                    window.showUndoToast('Quotation submitted. Undo available', async () => {
                        const undoRes = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?action=undo_submit', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ quotation_id: quotationId })
                        });
                        const undoJson = await undoRes.json();
                        if (undoJson.success) {
                            showToast('Quotation undone', 'info');
                            await loadSubmittedQuotations();
                            await loadAvailableRequests();
                        } else {
                            showToast(undoJson.error || undoJson.message || 'Undo failed', 'error');
                        }
                    }, seconds);
                }
            }
            closeQuotationModal();


            // Reload quotations and wait for it to complete
            await loadSubmittedQuotations();
            await loadAvailableRequests();


            // Switch to logs tab to show the new quotation
            if (!editingQuotationId) {
                // Use setTimeout to ensure DOM is updated
                setTimeout(() => {
                    const logsTab = document.querySelector('[data-tab="logs"]');
                    if (logsTab) {

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

    // Confirm deletion with user
    const confirmed = await window.showConfirm('Are you sure you want to delete this quotation? This action cannot be undone.', {
        title: 'Delete Quotation',
        confirmText: 'Delete',
        type: 'danger'
    });

    if (!confirmed) {
        return;
    }

    try {
        const deleteUrl = `/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?quotation_id=${quotationId}`;

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
async function viewRequestDetails(requestId, type = 'job') {
    await ensureRequestLoaded(requestId, type);

    const request = availableRequests.find(r => Number(r.request_id) === Number(requestId));
    if (!request) {
        showToast('Request not found', 'error');
        return;
    }

    const detailsContainer = document.getElementById('request-details-content');
    detailsContainer.innerHTML = `
        <div style="padding: var(--spacing-md);">
            <h3>${escapeHtml(request.title)}</h3>
            <p><strong>Category:</strong> ${escapeHtml(request.category_name || 'General')}</p>
            <p><strong>District:</strong> ${escapeHtml(request.district || '-')}</p>
            <p><strong>Address:</strong> ${escapeHtml(request.address || '-')}</p>
            <p><strong>Deadline:</strong> ${formatDate(request.finish_date)}</p>
            <p><strong>Urgency:</strong> ${escapeHtml(request.urgency || '-')}</p>
            <p><strong>Description:</strong></p>
            <p>${escapeHtml(request.description)}</p>
        </div>
    `;

    // Set button action
    setRequestDetailsQuoteButtonVisible(true);
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

/**
 * Ensure a request is available in `availableRequests` for modal rendering.
 * On full page, requests are usually preloaded. On dashboard, we may only have the request_id.
 *
 * @param {number} requestId
 * @returns {void|Promise<void>}
 */
async function ensureRequestLoaded(requestId, type = 'job') {
    const id = Number(requestId);
    if (!id) return false;
    if (availableRequests.some(r => Number(r.request_id) === id)) return true;

    const normalizedType = String(type || 'job').toLowerCase();

    // Fetch by id from API and cache it
    try {
        let response;

        if (normalizedType === 'direct') {
            response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-direct-requests.php?request_id=${encodeURIComponent(id)}&limit=1`);
        } else {
            response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-requests.php?request_id=${encodeURIComponent(id)}`);
        }

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Server Error (${response.status}): ${errorText.substring(0, 200)}`);
        }

        const result = await response.json();
        const row = (result && result.success && Array.isArray(result.data) && result.data.length > 0)
            ? result.data[0]
            : null;

        if (!row) return false;

        // Normalize created_at + photos field
        row.created_at = row.created_at || row.dateCreated || null;
        if (row.photos && typeof row.photos === 'string') {
            row.photos = row.photos.split(',');
        }

        availableRequests.push(row);
        return true;
    } catch (error) {
        console.error('Error fetching request by id:', error);
        return false;
    }
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

        });
    });
}

/**
 * Initialize modal
 */
function initializeModal() {
    const modalClose = document.querySelector('#quotation-modal .modal-close');
    const cancelBtn = document.querySelector('#quotation-modal .action-btn.secondary');
    // REMOVED: submitBtn event listener - using onclick in HTML to avoid double submission

    if (modalClose) {
        modalClose.addEventListener('click', closeQuotationModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeQuotationModal);
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

    const subtotalEl = document.getElementById('subtotal-amount');
    if (subtotalEl) subtotalEl.textContent = `LKR ${formatNumber(subtotal.toFixed(2))}`;

    const totalEl = document.getElementById('total-amount');
    if (totalEl) totalEl.textContent = `LKR ${formatNumber(total.toFixed(2))}`;

    const totalPriceInput = document.getElementById('total-price');
    if (totalPriceInput) totalPriceInput.value = total.toFixed(2);
}

/**
 * Initialize date validation
 */
function initializeDateValidation() {
    // Automated duration calculation from date range is now disabled as per requirements
    // We keep the fields available in case they are needed for legacy reasons but no longer auto-calculate
}

/**
 * Auto-calculate duration between start and completion dates
 */
function autoCalculateDuration() {
    // Automated duration calculation is now disabled.
    // Companies now provide unit-based rates instead of auto-calculated fixed prices.
    return;
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
 * Get initials from full name string
 */
function getInitialsFromFullName(fullName) {
    if (!fullName) return '';
    const parts = fullName.trim().split(/\s+/);
    const first = parts[0] ? parts[0].charAt(0).toUpperCase() : '';
    const last = parts.length > 1 ? parts[parts.length - 1].charAt(0).toUpperCase() : '';
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

function formatUnitSuffix(unitLabel) {
    const raw = String(unitLabel || '').trim();
    if (!raw) return '';

    // Strip outer parentheses if present.
    let normalized = raw.replace(/^\((.*)\)$/, '$1').trim();

    // Guard against accidental double-prefix like "per per hour".
    normalized = normalized.replace(/^per\s+per\s+/i, 'per ');

    if (/^per\s+/i.test(normalized)) {
        normalized = normalized.replace(/^per\s+/i, 'per ').trim();
        return ` (${normalized})`;
    }
    return ` (per ${normalized})`;
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
window.viewDirectRequestDetails = viewDirectRequestDetails;
window.closeRequestDetailsModal = closeRequestDetailsModal;

// ================================================================
// DIRECT REQUESTS FUNCTIONALITY
// ================================================================

/**
 * Load and render direct requests (future feature)
 * Currently shows empty state as direct requests are not yet implemented
 */
async function loadDirectRequests() {
    const emptyState = document.getElementById('direct-requests-empty');
    const table = document.getElementById('direct-requests-table');

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/company-direct-requests.php?limit=100');

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Direct requests API error:', errorText);
            // Fail closed: show empty state
            if (emptyState) emptyState.style.display = 'block';
            if (table) table.style.display = 'none';
            updateDirectRequestsCount(0);
            return;
        }

        const result = await response.json();
        if (!result || !result.success) {
            if (emptyState) emptyState.style.display = 'block';
            if (table) table.style.display = 'none';
            updateDirectRequestsCount(0);
            return;
        }

        const rows = Array.isArray(result.data) ? result.data : [];

        const directRequests = rows.map(r => ({
            ...r,
            created_at: r.created_at || r.dateCreated || null,
            status: r.status || 'pending',
            customer_name: r.customer_name || `${r.customer_fname || ''} ${r.customer_lname || ''}`.trim()
        }));

        renderDirectRequests(directRequests);
    } catch (error) {
        console.error('Error loading direct requests:', error);
        if (emptyState) emptyState.style.display = 'block';
        if (table) table.style.display = 'none';
        updateDirectRequestsCount(0);
    }
}

/**
 * Update direct requests count in header and tab
 * @param {number} count - Number of direct requests
 */
function updateDirectRequestsCount(count) {
    const headerCount = document.getElementById('direct-requests-count');
    const tabCount = document.getElementById('direct-requests-tab-count');

    if (headerCount) {
        headerCount.textContent = count;
    }
    if (tabCount) {
        tabCount.textContent = count;
    }
}

/**
 * Render direct requests in table (future implementation)
 * @param {Array} requests - Array of direct request objects
 */
function renderDirectRequests(requests) {
    const tbody = document.getElementById('direct-requests-tbody');
    const emptyState = document.getElementById('direct-requests-empty');
    const table = document.getElementById('direct-requests-table');

    if (!tbody) return;

    if (!requests || requests.length === 0) {
        // Show empty state
        emptyState.style.display = 'block';
        table.style.display = 'none';
        updateDirectRequestsCount(0);
        return;
    }

    // Hide empty state, show table
    emptyState.style.display = 'none';
    table.style.display = 'table';

    // Update counts
    updateDirectRequestsCount(requests.length);

    // Cache for modal rendering
    directRequestsCache = Array.isArray(requests) ? requests : [];

    // Render rows
    tbody.innerHTML = requests.map(request => createDirectRequestRow(request)).join('');
}

/**
 * Create HTML for a single direct request table row
 * @param {Object} request - Direct request object
 * @returns {string} HTML string
 */
function createDirectRequestRow(request) {
    const customerName = request.customer_name
        || `${request.customer_fname || ''} ${request.customer_lname || ''}`.trim()
        || request.customer
        || 'Customer';
    const initials = getInitialsFromFullName(customerName);

    const rawStatus = String(request.status || 'pending').toLowerCase();
    const statusClass = rawStatus === 'accepted' ? 'accepted' :
        rawStatus === 'rejected' ? 'rejected' :
            rawStatus === 'completed' ? 'accepted' : 'pending';
    const statusIcon = rawStatus === 'accepted' ? 'check' :
        rawStatus === 'rejected' ? 'times' :
            rawStatus === 'completed' ? 'check-double' : 'clock';

    // Check if expired
    const deadline = request.finish_date ? new Date(request.finish_date) : null;
    const today = new Date();
    const isExpired = deadline ? (deadline < today) : false;

    return `
        <tr data-request-id="${request.request_id}">
            <td>
                <div>
                    <h5>${escapeHtml(request.title)}</h5>
                    <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                        #REQ-${request.request_id} &bull; ${escapeHtml(request.category_name || request.category || 'General')}
                    </p>
                </div>
            </td>
            <td>
                <div class="table-customer">
                    <div class="table-customer-avatar">${initials}</div>
                    <div class="table-customer-info">
                        <h5>${escapeHtml(customerName)}</h5>
                        <p>${escapeHtml(request.customer_email || request.email || 'No email')}</p>
                    </div>
                </div>
            </td>
            <td>${formatDate(request.created_at)}</td>
            <td>
                ${deadline
            ? (isExpired
                ? `<span style="color: var(--danger-color); font-weight: 600;">
                         <i class="fas fa-exclamation-triangle"></i> Expired
                       </span>`
                : formatDate(request.finish_date))
            : '-'
        }
            </td>
            <td>
                <span class="status-badge ${statusClass}">
                    <i class="fas fa-${statusIcon}"></i>
                    ${rawStatus.charAt(0).toUpperCase() + rawStatus.slice(1)}
                </span>
            </td>
            <td>
                <div class="table-actions">
                    ${rawStatus === 'pending' && !isExpired ? `
                        <button class="table-action-btn view" onclick="viewDirectRequestDetails(${request.request_id})">
                            <i class="fas fa-eye"></i>
                            <span>View</span>
                        </button>
                        <button class="table-action-btn accept" onclick="acceptDirectRequest(${request.request_id})">
                            <i class="fas fa-check"></i>
                            <span>Accept</span>
                        </button>
                        <button class="table-action-btn reject" onclick="rejectDirectRequest(${request.request_id})">
                            <i class="fas fa-times"></i>
                            <span>Decline</span>
                        </button>
                    ` : rawStatus === 'accepted' ? `
                        <button class="table-action-btn view" onclick="viewDirectRequestDetails(${request.request_id})">
                            <i class="fas fa-file-contract"></i>
                            <span>View Contract</span>
                        </button>
                    ` : isExpired ? `
                        <button class="table-action-btn view" onclick="viewDirectRequestDetails(${request.request_id})">
                            <i class="fas fa-eye"></i>
                            <span>View</span>
                        </button>
                        <button class="table-action-btn view" onclick="contactCustomer(${request.user_id})">
                            <i class="fas fa-phone"></i>
                            <span>Contact</span>
                        </button>
                    ` : `
                        <button class="table-action-btn view" onclick="viewDirectRequestDetails(${request.request_id})">
                            <i class="fas fa-eye"></i>
                            <span>View</span>
                        </button>
                    `}
                </div>
            </td>
        </tr>
    `;
}

function setRequestDetailsQuoteButtonVisible(isVisible) {
    const btn = document.getElementById('submit-quote-from-details');
    if (!btn) return;
    btn.style.display = isVisible ? '' : 'none';
    if (!isVisible) {
        btn.onclick = null;
    }
}

/**
 * View direct-request details (directjobrequest)
 */
async function viewDirectRequestDetails(requestId) {
    const id = Number(requestId);
    if (!id) {
        showToast('Request not found', 'error');
        return;
    }

    const request = (Array.isArray(directRequestsCache) ? directRequestsCache : []).find(r => Number(r.request_id) === id);
    if (!request) {
        showToast('Request not found', 'error');
        return;
    }

    const detailsContainer = document.getElementById('request-details-content');
    if (!detailsContainer) {
        showToast('Request details popup unavailable', 'error');
        return;
    }

    const customerName = request.customer_name
        || `${request.customer_fname || ''} ${request.customer_lname || ''}`.trim()
        || 'Customer';

    detailsContainer.innerHTML = `
        <div style="padding: var(--spacing-md);">
            <h3>${escapeHtml(request.title)}</h3>
            <p><strong>Customer:</strong> ${escapeHtml(customerName)}</p>
            <p><strong>Category:</strong> ${escapeHtml(request.category_name || request.category || 'General')}</p>
            <p><strong>District:</strong> ${escapeHtml(request.district || '')}</p>
            <p><strong>Address:</strong> ${escapeHtml(request.address || '')}</p>
            <p><strong>Deadline:</strong> ${request.finish_date ? formatDate(request.finish_date) : '-'}</p>
            <p><strong>Description:</strong></p>
            <p>${escapeHtml(request.description || '')}</p>
        </div>
    `;

    // Direct requests should not show the "Submit Quote" CTA
    setRequestDetailsQuoteButtonVisible(false);

    requestDetailsModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Accept a direct request
 * @param {number} requestId - ID of the request to accept
 */
async function acceptDirectRequest(requestId) {
    const confirmed = await window.showConfirm('Accept this direct request? This will create a contract with the customer.', {
        title: 'Accept Request',
        confirmText: 'Accept'
    });

    if (!confirmed) {
        return;
    }

    try {
        // Future: API call to accept direct request
        // For now, show placeholder message
        showToast('Direct requests feature coming soon! This will create a contract.', 'info', 5000);

        // Future implementation:
        // const response = await fetch('/api/direct-requests.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ action: 'accept', request_id: requestId })
        // });
        // if (response.ok) {
        //     showToast('Request accepted successfully!', 'success');
        //     loadDirectRequests(); // Reload
        // }
    } catch (error) {
        console.error('Error accepting request:', error);
        showToast('Error accepting request: ' + error.message, 'error');
    }
}

/**
 * Reject a direct request
 * @param {number} requestId - ID of the request to reject
 */
async function rejectDirectRequest(requestId) {
    const reason = await window.showPrompt('Please provide a reason for declining this request:', {
        title: 'Decline Request',
        confirmText: 'Decline',
        placeholder: 'Reason for declining...'
    });

    if (!reason || reason.trim() === '') {
        showToast('Decline cancelled - reason is required', 'info');
        return;
    }

    try {
        // Future: API call to reject direct request
        showToast('Direct requests feature coming soon! Reason: ' + reason, 'info', 5000);

        // Future implementation:
        // const response = await fetch('/api/direct-requests.php', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify({ 
        //         action: 'reject', 
        //         request_id: requestId,
        //         reason: reason 
        //     })
        // });
        // if (response.ok) {
        //     showToast('Request declined', 'success');
        //     loadDirectRequests(); // Reload
        // }
    } catch (error) {
        console.error('Error rejecting request:', error);
        showToast('Error declining request: ' + error.message, 'error');
    }
}

/**
 * Contact customer about expired direct request
 * @param {number} customerId - ID of the customer to contact
 */
function contactCustomer(customerId) {
    // Future: Open messaging system or show customer contact details
    showToast('Customer contact feature coming soon!', 'info', 3000);
}

// Make direct request functions globally accessible
window.acceptDirectRequest = acceptDirectRequest;
window.rejectDirectRequest = rejectDirectRequest;
window.contactCustomer = contactCustomer;

// Initialize direct requests when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadDirectRequests);
} else {
    loadDirectRequests();
}

// ============================================================
// ⭐ NEW: Work Schedule Management Functions
// ============================================================

/**
 * Initialize work schedule features
 * Sets up event listeners and default values for work schedule fields
 */
function initializeWorkSchedule() {
    if (workScheduleInitialized) {
        return;
    }
    workScheduleInitialized = true;

    console.log('🔧 Initializing work schedule features...');

    const startTimeField = document.getElementById('work-start-time');
    const endTimeField = document.getElementById('work-end-time');

    // Auto-update working days based on schedule type
    const scheduleTypeSelect = document.getElementById('work-schedule-type');
    if (scheduleTypeSelect) {
        scheduleTypeSelect.addEventListener('change', function () {
            const scheduleType = this.value;
            const workingDaysInput = document.getElementById('working-days-per-week');
            const customScheduleRow = document.getElementById('custom-schedule-row');
            const customScheduleField = document.getElementById('custom-schedule-details');

            switch (scheduleType) {
                case 'weekdays_only':
                    workingDaysInput.value = 5;
                    customScheduleRow.style.display = 'none';
                    customScheduleField.required = false;
                    break;
                case 'weekends_included':
                    workingDaysInput.value = 6;
                    customScheduleRow.style.display = 'none';
                    customScheduleField.required = false;
                    break;
                case 'all_days':
                    workingDaysInput.value = 7;
                    customScheduleRow.style.display = 'none';
                    customScheduleField.required = false;
                    break;
                case 'custom':
                    customScheduleRow.style.display = 'block';
                    customScheduleField.required = true;
                    break;
            }

            // Recalculate total hours and update preview
            autoCalculateDuration();
            calculateTotalWorkHours();
            updateSchedulePreview();
        });
    }

    // Show/hide overtime rate field
    const overtimeCheckbox = document.getElementById('overtime-available');
    if (overtimeCheckbox) {
        overtimeCheckbox.addEventListener('change', function () {
            const overtimeRateRow = document.getElementById('overtime-rate-row');
            const overtimeRateField = document.getElementById('overtime-rate');

            if (this.checked) {
                overtimeRateRow.style.display = 'block';
                overtimeRateField.required = true;
            } else {
                overtimeRateRow.style.display = 'none';
                overtimeRateField.required = false;
                overtimeRateField.value = '';
            }

            updateSchedulePreview();
        });
    }

    // Auto-calculate daily hours when time fields change
    if (startTimeField) {
        startTimeField.addEventListener('change', () => {
            updateDailyWorkHours();
            calculateTotalWorkHours();
            updateSchedulePreview();
        });
        startTimeField.addEventListener('input', () => {
            updateDailyWorkHours();
            calculateTotalWorkHours();
        });
    }
    if (endTimeField) {
        endTimeField.addEventListener('change', () => {
            updateDailyWorkHours();
            calculateTotalWorkHours();
            updateSchedulePreview();
        });
        endTimeField.addEventListener('input', () => {
            updateDailyWorkHours();
            calculateTotalWorkHours();
        });
    }

    // Auto-calculate total work hours when duration changes (duration is auto-calculated)
    const durationField = document.getElementById('estimated-duration');
    if (durationField) {
        durationField.addEventListener('input', calculateTotalWorkHours);
        durationField.addEventListener('change', calculateTotalWorkHours);
    }

    // Update preview when any schedule field changes
    const fieldsForPreview = [
        'work-schedule-type', 'working-days-per-week', 'daily-work-hours',
        'work-start-time', 'work-end-time', 'overtime-available', 'overtime-rate',
        'estimated-start-date', 'estimated-completion-date', 'estimated-duration'
    ];
    fieldsForPreview.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', updateSchedulePreview);
            field.addEventListener('input', updateSchedulePreview);
        }
    });

    // Initial calculation and preview
    updateDailyWorkHours();
    autoCalculateDuration();
    calculateTotalWorkHours();
    updateSchedulePreview();

    console.log('✅ Work schedule features initialized');
}

function parseTimeToMinutes(timeString) {
    if (!timeString || typeof timeString !== 'string') return null;
    const [hoursStr, minutesStr] = timeString.split(':');
    const hours = Number(hoursStr);
    const minutes = Number(minutesStr);
    if (!Number.isFinite(hours) || !Number.isFinite(minutes)) return null;
    if (hours < 0 || hours > 23 || minutes < 0 || minutes > 59) return null;
    return hours * 60 + minutes;
}

function updateDailyWorkHours() {
    const startTime = document.getElementById('work-start-time')?.value;
    const endTime = document.getElementById('work-end-time')?.value;
    const dailyHoursField = document.getElementById('daily-work-hours');

    if (!dailyHoursField) return;
    if (!startTime || !endTime) {
        dailyHoursField.value = '';
        dailyHoursField.classList.remove('error');
        return;
    }

    const startMinutes = parseTimeToMinutes(startTime);
    const endMinutes = parseTimeToMinutes(endTime);

    if (startMinutes === null || endMinutes === null) {
        dailyHoursField.value = '';
        dailyHoursField.classList.add('error');
        return;
    }

    const diffMinutes = endMinutes - startMinutes;
    if (diffMinutes <= 0) {
        dailyHoursField.value = '';
        dailyHoursField.classList.add('error');
        showToast('End time must be after start time!', 'error', 3000);
        return;
    }

    dailyHoursField.classList.remove('error');
    dailyHoursField.value = (diffMinutes / 60).toFixed(2);
}

function isWorkingDayForScheduleType(dayOfWeek, scheduleType) {
    // dayOfWeek: 0=Sunday, 1=Monday, ..., 6=Saturday
    switch (scheduleType) {
        case 'weekdays_only':
            return dayOfWeek >= 1 && dayOfWeek <= 5;
        case 'weekends_included':
            return dayOfWeek >= 1 && dayOfWeek <= 6;
        case 'all_days':
        case 'custom':
        default:
            return true;
    }
}

function calculateWorkingDaysBetweenDates(startDate, endDate, scheduleType) {
    // Counts working days in [startDate, endDate] (inclusive)
    // i.e., start=2026-04-03, end=2026-04-06 => 4 days (if all are working days)
    if (!(startDate instanceof Date) || !(endDate instanceof Date)) return 0;
    if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) return 0;

    const startUtc = new Date(Date.UTC(startDate.getFullYear(), startDate.getMonth(), startDate.getDate()));
    const endUtc = new Date(Date.UTC(endDate.getFullYear(), endDate.getMonth(), endDate.getDate()));
    if (endUtc < startUtc) return 0;

    // Custom schedule: approximate working days using the provided working-days-per-week value
    if (scheduleType === 'custom') {
        const workingDaysPerWeek = parseFloat(document.getElementById('working-days-per-week')?.value) || 5;
        const totalCalendarDays = Math.floor((endUtc - startUtc) / (1000 * 60 * 60 * 24)) + 1;
        const fullWeeks = Math.floor(totalCalendarDays / 7);
        const remainderDays = totalCalendarDays % 7;
        const estimatedWorkingDays = (fullWeeks * workingDaysPerWeek) + Math.min(remainderDays, workingDaysPerWeek);
        return Math.max(0, Math.round(estimatedWorkingDays));
    }

    let count = 0;
    const current = new Date(startUtc);
    while (current <= endUtc) {
        const dow = current.getUTCDay();
        if (isWorkingDayForScheduleType(dow, scheduleType)) {
            count++;
        }
        current.setUTCDate(current.getUTCDate() + 1);
    }
    return count;
}

/**
 * Calculate total work hours based on duration, working days, and daily hours
 * Also auto-populates the hourly labor "Number of Hours" field
 */
function calculateTotalWorkHours() {
    const estimatedDuration = parseFloat(document.getElementById('estimated-duration')?.value) || 0;
    const dailyWorkHours = parseFloat(document.getElementById('daily-work-hours')?.value) || 0;

    const totalHoursField = document.getElementById('total-work-hours');

    // Auto-population of labor quantity is now disabled.
    // Labor is now manually entered as a unit rate.

    if (estimatedDuration > 0 && dailyWorkHours > 0 && totalHoursField) {
        const totalHours = (estimatedDuration * dailyWorkHours).toFixed(2);
        totalHoursField.value = totalHours;
    } else if (totalHoursField) {
        totalHoursField.value = '';
    }
}

/**
 * Update the schedule preview display
 */
function updateSchedulePreview() {
    const scheduleType = document.getElementById('work-schedule-type')?.value;
    const workingDays = document.getElementById('working-days-per-week')?.value;
    const dailyHours = document.getElementById('daily-work-hours')?.value;
    const startTime = document.getElementById('work-start-time')?.value;
    const endTime = document.getElementById('work-end-time')?.value;
    const totalHours = document.getElementById('total-work-hours')?.value;
    const overtimeAvailable = document.getElementById('overtime-available')?.checked;
    const overtimeRate = document.getElementById('overtime-rate')?.value;

    const previewContent = document.getElementById('schedule-preview-content');

    if (!previewContent) return;

    // If no data yet, show placeholder
    if (!scheduleType || !workingDays || !dailyHours) {
        previewContent.innerHTML = `
            <p class="text-muted">
                <i class="fas fa-arrow-up"></i> Fill in the fields above to see your work schedule preview
            </p>
        `;
        return;
    }

    // Schedule type names
    const scheduleNames = {
        'weekdays_only': 'Weekdays Only (Monday - Friday)',
        'weekends_included': 'Weekends Included (Monday - Saturday)',
        'all_days': 'All 7 Days per Week',
        'custom': 'Custom Schedule'
    };

    // Build preview HTML
    let html = `<div class="schedule-summary">`;

    // Schedule Type
    html += `
        <div class="schedule-item">
            <i class="fas fa-calendar-week"></i>
            <div>
                <strong>Schedule:</strong> ${scheduleNames[scheduleType] || scheduleType}
            </div>
        </div>
    `;

    // Working Days
    html += `
        <div class="schedule-item">
            <i class="fas fa-business-time"></i>
            <div>
                <strong>Working Days:</strong> ${workingDays} days per week
            </div>
        </div>
    `;

    // Daily Hours
    html += `
        <div class="schedule-item">
            <i class="fas fa-clock"></i>
            <div>
                <strong>Daily Hours:</strong> ${parseFloat(dailyHours).toFixed(1)} hours/day
            </div>
        </div>
    `;

    // Work Time
    if (startTime && endTime) {
        html += `
            <div class="schedule-item">
                <i class="fas fa-stopwatch"></i>
                <div>
                    <strong>Work Time:</strong> ${formatTime(startTime)} - ${formatTime(endTime)}
                </div>
            </div>
        `;
    }

    // Total Hours
    if (totalHours) {
        html += `
            <div class="schedule-item">
                <i class="fas fa-calculator"></i>
                <div>
                    <strong>Total Project Hours:</strong> <span class="highlight">${parseFloat(totalHours).toFixed(2)} hours</span>
                </div>
            </div>
        `;
    }

    // Overtime
    if (overtimeAvailable) {
        const rateText = overtimeRate ? `LKR ${parseFloat(overtimeRate).toLocaleString('en-US', { minimumFractionDigits: 2 })}` : '(not set)';
        html += `
            <div class="schedule-item overtime-info">
                <i class="fas fa-plus-circle"></i>
                <div>
                    <strong>Overtime:</strong> Available at ${rateText}/hour
                </div>
            </div>
        `;
    }

    html += `</div>`;

    previewContent.innerHTML = html;
}

/**
 * Format time for display (e.g., 08:00 -> 8:00 AM)
 * @param {string} timeString - Time in HH:MM format
 * @returns {string} Formatted time string
 */
function formatTime(timeString) {
    if (!timeString) return '';

    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);

    return `${displayHour}:${minutes} ${ampm}`;
}

// ============================================================
// Initialize Work Schedule on Page Load
// ============================================================

// Add work schedule initialization to existing DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    // Wait a bit to ensure form is fully loaded
    setTimeout(() => {
        initializeWorkSchedule();
    }, 500);
});

console.log('📅 Work schedule module loaded');
