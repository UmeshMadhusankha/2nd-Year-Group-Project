// ===================================
// CONTRACTS PAGE - COMPLETE IMPLEMENTATION
// ===================================

// Global State Management
let contractsData = [];
let displayedContracts = [];
let currentPage = 1;
const contractsPerPage = 9; // Show 9 contracts per page (3x3 grid)
let currentContract = null;
let currentStep = 1;
let editingContract = null;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    initializeContractsPage();
    loadContractsData();

    // PHASE 2: Initialize quotation selector when New Contract modal opens
    setupNewContractModalListener();

    // Close menus on scroll to prevent misalignment with fixed positioning
    window.addEventListener('scroll', closeAllMenus, true);
    const container = document.getElementById('contractsContainer');
    if (container) container.addEventListener('scroll', closeAllMenus, true);
});

function closeAllMenus() {
    document.querySelectorAll('.card-action-menu.active').forEach(m => {
        m.classList.remove('active');
        m.classList.remove('is-fixed');
    });
}

/**
 * PHASE 2: Setup listener for New Contract button
 */
function setupNewContractModalListener() {
    // Wait a bit for the page to fully load
    setTimeout(() => {
        const newContractBtn = document.getElementById('newContractBtn') ||
            document.querySelector('[onclick*="newContract"]') ||
            document.querySelector('.btn-primary');

        if (newContractBtn) {
            console.log('? Found New Contract button, adding listener');
            newContractBtn.addEventListener('click', function () {
                // Small delay to let modal open
                setTimeout(() => {
                    initializeQuotationSelectorForContractForm();
                }, 300);
            });
        } else {
            console.log('?? New Contract button not found yet, will try on modal open');
        }
    }, 1000);
}

/**
 * PHASE 2: Initialize quotation selector in contract creation form
 */
async function initializeQuotationSelectorForContractForm() {
    // Check if selector exists (modal is open)
    const selector = document.getElementById('quotationSelector');
    if (!selector) {
        console.log('?? Quotation selector not found - modal not open yet');
        return;
    }

    // Check if already populated
    if (selector.options.length > 1) {
        console.log('?? Quotation selector already populated');
        return;
    }

    try {
        console.log('?? Loading accepted quotations for contract creation...');

        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=getAcceptedQuotations');

        if (!response.ok) {
            console.log('? API returned error:', response.status);
            return;
        }

        const result = await response.json();
        console.log('?? Accepted quotations:', result);

        if (result.success && result.data && result.data.length > 0) {
            populateQuotationSelector(result.data);
            updateQuotationBadge(result.data.length);
        } else {
            updateQuotationBadge(0);
        }
    } catch (error) {
        console.error('? Error loading quotations:', error);
        updateQuotationBadge(0, true);
    }
}

/**
 * Populate the quotation selector dropdown
 */
function populateQuotationSelector(quotations) {
    const selector = document.getElementById('quotationSelector');
    if (!selector) return;

    // Clear existing options (except first one)
    selector.innerHTML = '<option value="">-- Select Accepted Quotation or Create Manually --</option>';

    quotations.forEach(q => {
        const option = document.createElement('option');
        option.value = q.quotation_id;
        option.textContent = `${q.title} (${q.customer_fname} ${q.customer_lname})`;
        option.dataset.quotation = JSON.stringify(q);
        selector.appendChild(option);
    });

    // Add event listeners
    selector.addEventListener('change', handleQuotationSelectionChange);
}

/**
 * Update quotation count badge
 */
function updateQuotationBadge(count, isError = false) {
    const badge = document.getElementById('quotationCountBadge');
    if (!badge) return;

    if (isError) {
        badge.textContent = 'Error';
        badge.style.background = '#f44336';
    } else if (count > 0) {
        badge.textContent = `${count} available`;
        badge.style.background = '#4caf50';
    } else {
        badge.textContent = 'None available';
        badge.style.background = '#999';
    }
}

/**
 * Handle quotation selection from dropdown
 */
function handleQuotationSelectionChange(event) {
    const selected = event.target.options[event.target.selectedIndex];

    if (!selected.value) {
        // Hide preview if nothing selected
        const preview = document.getElementById('quotationPreviewCard');
        if (preview) preview.style.display = 'none';
        return;
    }

    try {
        const quotation = JSON.parse(selected.dataset.quotation);
        console.log('? Quotation selected:', quotation);

        // Show preview
        showQuotationPreview(quotation);

        // Auto-fill form fields
        autoFillContractForm(quotation);

    } catch (error) {
        console.error('Error parsing quotation data:', error);
    }
}

/**
 * Show quotation preview card
 */
function showQuotationPreview(q) {
    const preview = document.getElementById('quotationPreviewCard');
    if (!preview) return;

    preview.innerHTML = `
        <h4 style="margin: 0 0 15px 0; color: #2e7d32; display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-check-circle"></i> 
            <span>Selected: ${escapeHtml(q.title)}</span>
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; font-size: 13px; margin-bottom: 15px;">
            <div><strong>Customer:</strong> ${escapeHtml(q.customer_fname)} ${escapeHtml(q.customer_lname)}</div>
            <div><strong>Budget Type:</strong> ${q.budget_type ? q.budget_type.toUpperCase() : 'Fixed'}</div>
            <div><strong>Payment:</strong> ${formatPaymentMethod(q.payment_method)}</div>
            <div><strong>Pricing:</strong> ${q.pricing_type ? (q.pricing_type === 'time_and_material' ? 'Time & Material' : 'Fixed Price') : 'Fixed Price'}</div>
            <div><strong>Duration:</strong> ${q.estimated_duration || 'TBD'} days</div>
        </div>
        <div style="padding: 12px; background: #fff3cd; border-radius: 6px; font-size: 12px; color: #856404;">
            <i class="fas fa-info-circle"></i> <strong>Form will be auto-filled.</strong> 
            Review all fields in the next steps and modify if needed.
        </div>
    `;
    preview.style.display = 'block';
}

/**
 * Auto-fill contract form with quotation data
 */
function autoFillContractForm(q) {
    console.log('?? Auto-filling form with quotation data...');

    // Store quotation ID in hidden field
    setFieldValue('selectedQuotationId', q.quotation_id);
    setFieldValue('selectedRequestId', q.request_id);
    setFieldValue('customerId', q.customer_id);

    // Step 1: Party Information (client and company details)
    const setText = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || '-'; };
    
    setText('partyClientName', `${q.customer_fname} ${q.customer_lname}`);
    setText('partyClientEmail', q.customer_email);
    setText('partyClientAddress', q.customer_address);
    setText('partyClientDistrict', q.customer_district);
    
    setText('partyCompanyName', q.company_name);
    setText('partyCompanyReg', q.company_registration);
    setText('partyCompanyAddress', q.company_address);
    setText('partyCompanyContact', q.company_contact);
    
    // Show the parties section
    const partiesSection = document.getElementById('partiesSection');
    if (partiesSection) partiesSection.style.display = 'block';

    // Step 2: Client & Project Information
    setFieldValue('clientName', `${q.customer_fname} ${q.customer_lname}`);
    setFieldValue('clientEmail', q.customer_email);
    setFieldValue('clientPhone', q.customer_phone || '');
    setFieldValue('projectTitle', q.title);

    const resolvedProjectType =
        q.request_category_name ||
        q.category_name ||
        q.project_type ||
        'Service Request';

    const resolvedProjectLocationParts = [
        q.request_address || q.location,
        q.request_district || q.district
    ].filter(Boolean);
    const resolvedProjectLocation = resolvedProjectLocationParts.join(', ');

    setFieldValue('projectType', resolvedProjectType);
    setFieldValue('projectLocation', resolvedProjectLocation || q.location || q.district || '');
    setFieldValue('projectDescription', q.description || q.request_description || q.request_title || '');

    // Step 3: Financial Terms (Phase 1 Business Logic)
    setFieldValue('contractValue', q.total_amount);
    setFieldValue('budgetType', q.budget_type || 'fixed');
    setFieldValue('budgetMin', q.budget_min || '');
    setFieldValue('budgetMax', q.budget_max || '');
    setFieldValue('paymentMethod', q.payment_method || 'full_upfront');
    setFieldValue('pricingType', q.pricing_type || 'fixed_price');
    setFieldValue('hourlyRate', q.hourly_rate || '');

    // Calculate spending cap for Time & Material
    if (q.pricing_type === 'time_and_material') {
        const cap = parseFloat(q.total_amount) * (q.spending_cap_multiplier || 1.10);
        setFieldValue('spendingCap', cap.toFixed(2));
    }

    // Timeline
    setFieldValue('startDate', q.start_date || '');
    setFieldValue('endDate', q.completion_date || '');
    setFieldValue('estimatedDuration', q.estimated_duration || '');

    // Additional Terms
    setFieldValue('warrantyPeriod', q.warranty_period || '');
    setFieldValue('paymentTermsText', q.payment_terms || '');
    setFieldValue('additionalTerms', q.additional_terms || '');

    // Trigger change events to update UI
    triggerFormCalculations();

    console.log('? Form auto-filled successfully');
}

/**
 * Helper: Set form field value safely
 */
function setFieldValue(fieldId, value) {
    const field = document.getElementById(fieldId) || document.querySelector(`[name="${fieldId}"]`);
    if (field && value !== null && value !== undefined && value !== '') {
        field.value = value;
        field.dispatchEvent(new Event('change', { bubbles: true }));
        field.dispatchEvent(new Event('input', { bubbles: true }));
    }
}

/**
 * Trigger form calculations for budget ranges, spending cap, etc.
 */
function triggerFormCalculations() {
    // Trigger budget type change to show/hide budget range fields
    const budgetTypeField = document.getElementById('budgetType');
    if (budgetTypeField) {
        handleBudgetTypeChange({ target: budgetTypeField });
    }

    // Trigger pricing type change to show/hide hourly rate fields
    const pricingTypeField = document.getElementById('pricingType');
    if (pricingTypeField) {
        handlePricingTypeChange({ target: pricingTypeField });
    }

    // Calculate duration
    calculateDuration();
}

/**
 * Format payment method for display
 */
function formatPaymentMethod(method) {
    const methods = {
        'full_upfront': 'Full Upfront',
        'milestone_based': 'Milestone Based',
        '50_50': '50/50 Split',
        '30_70': '30/70 Split',
        'completion': 'After Completion'
    };
    return methods[method] || method;
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Handle budget type change (show/hide budget range)
 */
function handleBudgetTypeChange(event) {
    const budgetType = event.target.value;
    const minGroup = document.getElementById('budgetMinGroup');
    const maxGroup = document.getElementById('budgetMaxGroup');
    const contractValue = parseFloat(document.getElementById('contractValue')?.value || 0);

    if (budgetType === 'flexible' && contractValue > 0) {
        if (minGroup) minGroup.style.display = 'block';
        if (maxGroup) maxGroup.style.display = 'block';

        // Calculate and set budget range
        setFieldValue('budgetMin', (contractValue * 0.9).toFixed(2));
        setFieldValue('budgetMax', (contractValue * 1.1).toFixed(2));
    } else {
        if (minGroup) minGroup.style.display = 'none';
        if (maxGroup) maxGroup.style.display = 'none';
        setFieldValue('budgetMin', '');
        setFieldValue('budgetMax', '');
    }
}

/**
 * Handle pricing type change (show/hide hourly rate)
 */
function handlePricingTypeChange(event) {
    const pricingType = event.target.value;
    const hourlyGroup = document.getElementById('hourlyRateGroup');
    const capGroup = document.getElementById('spendingCapGroup');

    if (pricingType === 'time_and_material') {
        if (hourlyGroup) hourlyGroup.style.display = 'block';
        if (capGroup) capGroup.style.display = 'block';

        // Calculate spending cap
        const contractValue = parseFloat(document.getElementById('contractValue')?.value || 0);
        if (contractValue > 0) {
            setFieldValue('spendingCap', (contractValue * 1.1).toFixed(2));
        }
    } else {
        if (hourlyGroup) hourlyGroup.style.display = 'none';
        if (capGroup) capGroup.style.display = 'none';
        setFieldValue('hourlyRate', '');
        setFieldValue('spendingCap', '');
    }
}

/**
 * Calculate duration between start and end dates
 */
function calculateDuration() {
    const startDate = document.getElementById('startDate')?.value;
    const endDate = document.getElementById('endDate')?.value;

    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        setFieldValue('estimatedDuration', diffDays);
    }
}

// Add event listeners for budget and pricing type changes
document.addEventListener('DOMContentLoaded', function () {
    const budgetTypeField = document.getElementById('budgetType');
    if (budgetTypeField) {
        budgetTypeField.addEventListener('change', handleBudgetTypeChange);
    }

    const pricingTypeField = document.getElementById('pricingType');
    if (pricingTypeField) {
        pricingTypeField.addEventListener('change', handlePricingTypeChange);
    }

    const startDateField = document.getElementById('startDate');
    const endDateField = document.getElementById('endDate');
    if (startDateField) startDateField.addEventListener('change', calculateDuration);
    if (endDateField) endDateField.addEventListener('change', calculateDuration);

    // Recalculate budget range when contract value changes
    const contractValueField = document.getElementById('contractValue');
    if (contractValueField) {
        contractValueField.addEventListener('input', function () {
            const budgetTypeField = document.getElementById('budgetType');
            if (budgetTypeField && budgetTypeField.value === 'flexible') {
                handleBudgetTypeChange({ target: budgetTypeField });
            }
        });
    }
});

/**
 * REMOVED: Auto-create contracts function
 * Reason: New approach - manual creation with auto-fill
 */
// async function autoCreateContractsFromQuotations() { ... } // REMOVED

function initializeContractsPage() {
    initializeFilters();
    initializeViewSwitcher();
    initializeContractActions();
    initializeModals();
    initializeNewContractForm();
    initializeSendContractModal();
    initializeDeleteModal();
    initializeCancelModal();
    initializeScrollToTop();
    initializeExportModal();
}

// ===================================
// DATA MANAGEMENT
// ===================================

async function loadContractsData() {
    const container = document.getElementById('contractsContainer');
    const loading = document.getElementById('contractsLoading');
    const empty = document.getElementById('contractsEmpty');

    // Show loading state
    if (loading) loading.style.display = 'flex';
    if (empty) empty.style.display = 'none';

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=list');

        // Handle 401 Unauthorized specifically
        if (response.status === 401) {
            if (loading) loading.style.display = 'none';
            container.innerHTML = `
                <div class="contracts-loading" id="contractsLoading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p>Loading contracts...</p>
                </div>
                <div class="contracts-empty" id="contractsEmpty" style="display: none;">
                    <i class="fas fa-file-contract fa-3x"></i>
                    <h3>No Contracts Found</h3>
                    <p>You don't have any contracts yet. Start by creating a new contract.</p>
                </div>
                <div class="contracts-error" style="display: flex; grid-column: 1 / -1; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; background: var(--bg-card); border-radius: var(--border-radius-lg); border: 1px solid rgba(255, 193, 7, 0.3); text-align: center;">
                    <i class="fas fa-lock fa-3x" style="color: #ffc107; opacity: 0.8; margin-bottom: 24px;"></i>
                    <h3 style="color: var(--text-primary); font-size: 1.5rem; margin: 0 0 12px 0;">Authentication Required</h3>
                    <p style="color: var(--text-secondary); font-size: 1rem; margin: 0 0 24px 0; max-width: 400px;">Please log in as a company user to view contracts.</p>
                    <a href="/2nd-Year-Group-Project/FixLanka/views/auth/login.php" class="action-btn primary" style="text-decoration: none; margin-top: 12px;">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            `;
            hideLoadMoreButton();
            hideEndIndicator();
            return;
        }

        // Check if response is ok
        if (!response.ok) {
            throw new Error(`Server error: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();

        // Check if API returned success
        if (!result.success) {
            // This is an API error (like database connection failed)
            throw new Error(result.message || 'Failed to load contracts');
        }

        // Hide loading
        if (loading) loading.style.display = 'none';

        // Store contracts data
        contractsData = result.data || [];
        displayedContracts = [];
        currentPage = 1;

        if (contractsData.length === 0) {
            // Database is empty - this is NOT an error, just no data yet
            if (empty) empty.style.display = 'flex';
            hideLoadMoreButton();
            hideEndIndicator();
        } else {
            // Hide empty state and render contracts
            if (empty) empty.style.display = 'none';
            renderContractsPage();
        }

        // Update stats
        updateContractStats();

    } catch (error) {
        console.error('Error loading contracts:', error);
        if (loading) loading.style.display = 'none';

        // Show error message with try again button
        // This is for real errors like network issues, database connection failures, etc.
        container.innerHTML = `
            <div class="contracts-loading" id="contractsLoading" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Loading contracts...</p>
            </div>
            <div class="contracts-empty" id="contractsEmpty" style="display: none;">
                <i class="fas fa-file-contract fa-3x"></i>
                <h3>No Contracts Found</h3>
                <p>You don't have any contracts yet. Start by creating a new contract.</p>
            </div>
            <div class="contracts-error" style="display: flex; grid-column: 1 / -1; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; background: var(--bg-card); border-radius: var(--border-radius-lg); border: 1px solid rgba(220, 53, 69, 0.2); text-align: center;">
                <i class="fas fa-exclamation-triangle fa-3x" style="color: var(--danger-color); opacity: 0.6; margin-bottom: 24px;"></i>
                <h3 style="color: var(--text-primary); font-size: 1.5rem; margin: 0 0 12px 0;">Connection Error</h3>
                <p style="color: var(--text-secondary); font-size: 1rem; margin: 0 0 24px 0; max-width: 400px;">${escapeHtml(error.message)}</p>
                <button class="action-btn primary" onclick="loadContractsData()" style="margin-top: 12px;">
                    <i class="fas fa-redo"></i> Try Again
                </button>
            </div>
        `;
        hideLoadMoreButton();
        hideEndIndicator();
    }
}

function renderContractsPage() {
    // Calculate which contracts to show
    const startIndex = 0;
    const endIndex = currentPage * contractsPerPage;
    const contractsToShow = contractsData.slice(startIndex, endIndex);

    // Render contracts
    renderContracts(contractsToShow);

    // Show/hide load more button
    if (endIndex >= contractsData.length) {
        hideLoadMoreButton();
        showEndIndicator();
    } else {
        showLoadMoreButton();
        hideEndIndicator();
    }
}

function loadMoreContracts() {
    currentPage++;
    renderContractsPage();
}

function renderContracts(contracts) {
    const container = document.getElementById('contractsContainer');

    // Clear existing cards (keep loading/empty states hidden)
    const loading = document.getElementById('contractsLoading');
    const empty = document.getElementById('contractsEmpty');
    container.innerHTML = '';

    // Re-add loading and empty (hidden)
    if (loading) {
        loading.style.display = 'none';
        container.appendChild(loading);
    }
    if (empty) {
        empty.style.display = 'none';
        container.appendChild(empty);
    }

    // Render each contract card
    contracts.forEach(contract => {
        const cardHTML = createContractCard(contract);
        container.insertAdjacentHTML('beforeend', cardHTML);
    });

    // Re-initialize card actions after rendering
    initializeContractActions();
}

function createContractCard(contract) {
    const statusClass = contract.status.toLowerCase().replace('_', '-');
    const statusIcon = getStatusIcon(contract.status.toLowerCase());
    const statusText = formatStatusText(contract.status);

    // Get initials from client name
    const initials = contract.client_name.split(' ').map(word => word[0]).join('').toUpperCase().substring(0, 2);

    // Format currency
    const formattedValue = formatCurrency(contract.value);
    const laborPerLabel = resolveQuotationPerLabel(contract.labor_unit_label, 'labor');
    const materialPerLabel = resolveQuotationPerLabel(contract.material_unit_label, 'material');
    const laborPriceText = formatUnitPrice(contract.labor_cost);
    const materialPriceText = formatUnitPrice(contract.material_cost);

    const hasLaborOrMaterialPrice = laborPriceText !== null || materialPriceText !== null;
    const valueBlock = hasLaborOrMaterialPrice
        ? `
            <div class="card-unit-price-block" aria-label="Unit price breakdown">
                ${laborPriceText ? `<div class="card-unit-price-item"><span class="card-unit-price-label">${escapeHtml(formatCostLabel('Labor', laborPerLabel))}</span><span class="card-unit-price-value">${laborPriceText}</span></div>` : ''}
                ${materialPriceText ? `<div class="card-unit-price-item"><span class="card-unit-price-label">${escapeHtml(formatCostLabel('Material', materialPerLabel))}</span><span class="card-unit-price-value">${materialPriceText}</span></div>` : ''}
            </div>
        `
        : `<div class="card-value-badge">${formattedValue}</div>`;

    // Format dates
    const startDate = formatDate(contract.start_date);
    const endDate = formatDate(contract.end_date);
    const createdDate = formatDate(contract.contract_date);

    // Calculate days remaining
    const daysInfo = getDaysInfo(contract.start_date, contract.end_date, contract.status);

    // Customer response badge
    const responseBadge = getCustomerResponseBadge(contract);

    // Payment method label
    const paymentLabel = formatPaymentMethod(contract.payment_method);

    // Progress
    const progress = contract.progress || 0;

    // Type badge
    const typeLabel = contract.type ? contract.type.charAt(0).toUpperCase() + contract.type.slice(1) : 'General';

    // Build action buttons based on status
    const actions = buildCardActions(contract);

    return `
        <div class="contract-card" data-status="${statusClass}" data-type="${contract.type || 'general'}" data-contract-id="${contract.contract_id}">
            <div class="card-top-row">
                <div class="card-type-badge">${typeLabel}</div>
                <div class="card-status-badge ${statusClass}">
                    <i class="${statusIcon}"></i>
                    <span>${statusText}</span>
                </div>
            </div>
            
            <div class="card-title-section">
                <div class="card-title-row">
                    <h3 class="card-title">${escapeHtml(contract.title)}</h3>
                    <div class="card-status-inline card-status-badge ${statusClass}">
                        <i class="${statusIcon}"></i>
                        <span>${statusText}</span>
                    </div>
                </div>
                <span class="card-contract-number">${contract.contract_number}</span>
            </div>

            <div class="card-client-row">
                <div class="card-client-avatar">${initials}</div>
                <div class="card-client-info">
                    <span class="card-client-name">${escapeHtml(contract.client_name)}</span>
                    <span class="card-client-email">${escapeHtml(contract.client_email || '')}</span>
                </div>
                ${valueBlock}
            </div>

            <div class="card-meta-grid">
                <div class="card-meta-item">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <span class="meta-label">Start</span>
                        <span class="meta-value">${startDate}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <span class="meta-label">End</span>
                        <span class="meta-value">${endDate}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <span class="meta-label">Payment</span>
                        <span class="meta-value">${paymentLabel}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <span class="meta-label">Location</span>
                        <span class="meta-value">${escapeHtml(contract.location || 'N/A')}</span>
                    </div>
                </div>
            </div>



            <div class="card-actions-row">
                ${actions}
            </div>
        </div>
    `;
}

function formatUnitPrice(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    const numericValue = Number(value);
    if (!Number.isFinite(numericValue)) {
        return null;
    }

    return formatCurrency(numericValue);
}

function resolveQuotationPerLabel(rawUnitLabel, type) {
    if (!rawUnitLabel || typeof rawUnitLabel !== 'string') {
        return '';
    }

    const normalized = rawUnitLabel.trim().toLowerCase();
    if (!normalized) {
        return '';
    }

    if (normalized.includes('hour')) {
        return type === 'material' ? '' : 'per hour';
    }

    if (
        normalized.includes('m²') ||
        normalized.includes('m2') ||
        normalized.includes('sqm') ||
        normalized.includes('sqft') ||
        normalized.includes('area')
    ) {
        return 'per area';
    }

    if (normalized.includes('unit')) {
        return 'per no. of units';
    }

    return '';
}

function formatCostLabel(baseLabel, perLabel) {
    return perLabel ? `${baseLabel} (${perLabel})` : baseLabel;
}

function formatStatusText(status) {
    const map = {
        'draft': 'Draft',
        'sent': 'Sent',
        'active': 'Active',
        'in_progress': 'In Progress',
        'milestone_pending': 'Milestone Pending',
        'completed': 'Completed',
        'terminated': 'Terminated',
        'disputed': 'Disputed'
    };
    return map[status] || status.charAt(0).toUpperCase() + status.slice(1);
}

function getStatusIcon(status) {
    const icons = {
        'draft': 'fas fa-file-alt',
        'sent': 'fas fa-paper-plane',
        'active': 'fas fa-play-circle',
        'in_progress': 'fas fa-hard-hat',
        'milestone_pending': 'fas fa-tasks',
        'completed': 'fas fa-check-circle',
        'terminated': 'fas fa-ban',
        'disputed': 'fas fa-exclamation-triangle'
    };
    return icons[status] || 'fas fa-file-contract';
}

function getDaysInfo(startDate, endDate, status) {
    if (!endDate) return { text: 'No deadline', icon: 'fas fa-infinity' };

    const now = new Date();
    const end = new Date(endDate);
    const start = new Date(startDate);
    const diffDays = Math.ceil((end - now) / (1000 * 60 * 60 * 24));

    if (status === 'completed') {
        return { text: 'Completed', icon: 'fas fa-check' };
    }
    if (status === 'cancelled' || status === 'terminated') {
        return { text: 'Closed', icon: 'fas fa-ban' };
    }
    if (diffDays < 0) {
        return { text: `${Math.abs(diffDays)}d overdue`, icon: 'fas fa-exclamation-triangle' };
    }
    if (diffDays === 0) {
        return { text: 'Due today', icon: 'fas fa-bell' };
    }
    if (diffDays <= 7) {
        return { text: `${diffDays}d remaining`, icon: 'fas fa-clock' };
    }
    return { text: `${diffDays}d remaining`, icon: 'far fa-clock' };
}

function getCustomerResponseBadge(contract) {
    if (!contract.sent_to_customer) return '';

    const responseMap = {
        'accepted': '<span class="card-response-badge accepted"><i class="fas fa-check-circle"></i> Accepted</span>',
        'rejected': '<span class="card-response-badge rejected"><i class="fas fa-times-circle"></i> Rejected</span>',
        'negotiating': '<span class="card-response-badge negotiating"><i class="fas fa-comments"></i> Negotiating</span>',
        'pending': '<span class="card-response-badge pending-response"><i class="fas fa-paper-plane"></i> Sent</span>'
    };

    return responseMap[contract.customer_response] || responseMap['pending'];
}

function formatPaymentMethod(method) {
    const map = {
        'full_upfront': 'Full Upfront',
        'milestone_based': 'Milestone',
        '50_50': '50/50 Split',
        '30_70': '30/70 Split',
        'completion': 'On Completion'
    };
    return map[method] || 'Standard';
}

function buildCardActions(contract) {
    let html = '';
    const id = contract.contract_id;
    const status = contract.status || 'draft';
    const isSent = contract.sent_to_customer == 1;
    const isAcceptedByCustomer =
        contract?.terms_accepted === true ||
        contract?.terms_accepted === 1 ||
        String(contract?.customer_response || '') === 'accepted' ||
        String(status) === 'accepted';

    // 1) Primary action button (Send, Chat, Start Project, or View Project)
    if (!isSent) {
        html += `<button class="card-action-btn card-action-send send-contract-btn" data-contract-id="${id}" title="Send to Customer">
            <i class="fas fa-paper-plane"></i>
        </button>`;
    } else if (isAcceptedByCustomer) {
        if (!contract.project_id) {
            // Contract accepted, project not started
            html += `<button class="card-action-btn" onclick="handleStartProjectFromContract(${id})" style="background: var(--primary-color); border: none; color: white; width: auto; padding: 0 16px; border-radius: 6px; font-weight: 600;" title="Start Project">
                <i class="fas fa-rocket"></i> Start Project
            </button>`;
        } else {
            // Project already started
            html += `<button class="card-action-btn" onclick="window.location.href='projects.php'" style="background: var(--success); border: none; color: white; width: auto; padding: 0 16px; border-radius: 6px; font-weight: 600;" title="View Project">
                <i class="fas fa-eye"></i> View Project
            </button>`;
        }
    } else if (contract.chat_active == 1) {
        // Unread indicator dot
        const unreadCount = parseInt(contract.unread_count || contract.unread_messages || 0);
        const unreadIndicator = unreadCount > 0
            ? '<span class="chat-unread-dot" style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #ef4444; border: 2px solid white; border-radius: 50%; z-index: 10;"></span>'
            : '';

        html += `<button class="card-action-btn card-action-chat chat-contract-btn" data-contract-id="${id}" title="Chat with Customer" style="position:relative">
            <i class="fas fa-comments"></i>
            ${unreadIndicator}
        </button>`;
    }

    // 2) Edit  visible button (only for draft/sent)
    if (['draft', 'sent'].includes(status)) {
        html += `<button class="card-action-btn edit-contract-btn" data-contract-id="${id}" title="Edit Contract">
            <i class="fas fa-edit"></i>
        </button>`;
    }

    // 3) More menu (?)  contains all other actions
    html += `<button class="card-action-btn card-action-more more-menu-btn" data-contract-id="${id}" title="More Options">
        <i class="fas fa-ellipsis-v"></i>
    </button>`;

    // Dropdown menu items
    html += `<div class="card-action-menu" id="cardMenu-${id}">`;
    html += `<a class="card-menu-item view-contract-btn" data-contract-id="${id}"><i class="fas fa-eye"></i> View Details</a>`;
    html += `<a class="card-menu-item download-contract-btn" data-contract-id="${id}"><i class="fas fa-download"></i> Download PDF</a>`;
    if (['active', 'sent'].includes(status)) {
        html += `<a class="card-menu-item terminate-contract-btn" data-contract-id="${id}"><i class="fas fa-times-circle"></i> Cancel Contract</a>`;
    }
    if (status === 'draft') {
        html += `<a class="card-menu-item delete-contract-btn" data-contract-id="${id}"><i class="fas fa-trash-alt"></i> Delete</a>`;
    }
    html += `</div>`;

    return html;
}

function toggleCardMenu(event, contractId, btn = null) {
    event.stopPropagation();
    event.preventDefault();

    // Close all other menus first
    document.querySelectorAll('.card-action-menu.active').forEach(menu => {
        if (menu.id !== `cardMenu-${contractId}`) {
            menu.classList.remove('active');
            menu.classList.remove('is-fixed');
        }
    });

    const menu = document.getElementById(`cardMenu-${contractId}`);
    if (menu) {
        const isOpening = !menu.classList.contains('active');
        const triggerBtn = btn || (event.target ? event.target.closest('.more-menu-btn') : null);

        if (isOpening && triggerBtn) {
            const rect = triggerBtn.getBoundingClientRect();
            menu.classList.add('is-fixed');
            menu.style.top = (rect.bottom + 5) + 'px';
            // Align right edge of menu with right edge of button
            menu.style.left = 'auto';
            menu.style.right = (window.innerWidth - rect.right) + 'px';
        } else {
            menu.classList.remove('is-fixed');
        }
        menu.classList.toggle('active');
    }
}

// Close card menus when clicking anywhere outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.card-action-more') && !e.target.closest('.more-menu-btn') && !e.target.closest('.card-action-menu')) {
        document.querySelectorAll('.card-action-menu.active').forEach(m => {
            m.classList.remove('active');
            m.classList.remove('is-fixed');
        });
    }
});

function handleTerminateContract(contractId) {
    openCancelModal(contractId);
}

// ===================================
// CANCEL CONTRACT MODAL
// ===================================

function initializeCancelModal() {
    const modal = document.getElementById('cancelModal');
    const closeBtn = document.getElementById('cancelModalClose');
    const cancelBtn = document.getElementById('cancelCancelBtn');
    const confirmBtn = document.getElementById('cancelConfirmBtn');

    if (closeBtn) closeBtn.addEventListener('click', closeCancelModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeCancelModal);
    if (confirmBtn) confirmBtn.addEventListener('click', confirmCancelContract);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeCancelModal();
        });
    }
}

let contractToCancel = null;

async function openCancelModal(contractId) {
    contractToCancel = contractId;

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`);
        const result = await response.json();

        if (result.success && result.data) {
            const info = result.data?.client?.name
                ? `${result.data.client.name} - ${result.data.title || 'Contract'}`
                : (result.data.title || 'Contract');
            const infoElement = document.getElementById('cancelContractInfo');
            if (infoElement) infoElement.textContent = info;
        }
    } catch (error) {
        console.error('Error loading contract for cancellation:', error);
    }

    const modal = document.getElementById('cancelModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeCancelModal() {
    const modal = document.getElementById('cancelModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        contractToCancel = null;
    }
}

async function confirmCancelContract() {
    if (!contractToCancel) return;

    const confirmBtn = document.getElementById('cancelConfirmBtn');
    const originalText = confirmBtn ? confirmBtn.innerHTML : null;
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
    }

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=cancel_contract', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contract_id: contractToCancel })
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Failed to cancel contract');
        }

        showNotification('Contract cancelled. You can now send a new contract for this project.', 'success');
        closeCancelModal();
        loadContractsData();
    } catch (error) {
        console.error('Cancel contract failed:', error);
        showNotification(error.message || 'Failed to cancel contract', 'error');
    } finally {
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        }
    }
}

function showLoadMoreButton() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadMoreContainer = document.querySelector('.load-more-container');
    if (loadMoreContainer) {
        loadMoreContainer.style.display = 'flex';
    }
    if (loadMoreBtn && !loadMoreBtn.hasAttribute('data-listener')) {
        loadMoreBtn.setAttribute('data-listener', 'true');
        loadMoreBtn.addEventListener('click', loadMoreContracts);
    }
}

function hideLoadMoreButton() {
    const loadMoreContainer = document.querySelector('.load-more-container');
    if (loadMoreContainer) {
        loadMoreContainer.style.display = 'none';
    }
}

function showEndIndicator() {
    const endIndicator = document.getElementById('contractsEnd');
    if (endIndicator) {
        endIndicator.style.display = 'block';
    }
}

function hideEndIndicator() {
    const endIndicator = document.getElementById('contractsEnd');
    if (endIndicator) {
        endIndicator.style.display = 'none';
    }
}

function formatCurrency(amount) {
    return `LKR ${parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

async function updateContractStats() {
    try {
        // Calculate stats from local data if available
        if (contractsData && contractsData.length > 0) {
            const active = contractsData.filter(c => c.status === 'active').length;
            const draft = contractsData.filter(c => ['draft', 'sent'].includes(c.status)).length;
            const completed = contractsData.filter(c => c.status === 'completed').length;
            const totalValue = contractsData.reduce((sum, c) => sum + (parseFloat(c.value) || 0), 0);

            const elActive = document.getElementById('statActive');
            const elDraft = document.getElementById('statDraft');
            const elCompleted = document.getElementById('statCompleted');
            const elTotal = document.getElementById('statTotal');

            if (elActive) elActive.textContent = active;
            if (elDraft) elDraft.textContent = draft;
            if (elCompleted) elCompleted.textContent = completed;
            if (elTotal) elTotal.textContent = formatCurrency(totalValue);
            return;
        }

        // Fallback: fetch from API
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=stats');
        const result = await response.json();

        if (result.success && result.data) {
            const stats = result.data;
            const elActive = document.getElementById('statActive');
            const elDraft = document.getElementById('statDraft');
            const elCompleted = document.getElementById('statCompleted');
            const elTotal = document.getElementById('statTotal');

            if (elActive) elActive.textContent = stats.active || 0;
            if (elDraft) elDraft.textContent = (stats.draft || 0) + (stats.pending || 0);
            if (elCompleted) elCompleted.textContent = stats.completed || 0;
            if (elTotal) elTotal.textContent = formatCurrency(stats.total_value || 0);
        }
    } catch (error) {
        console.error('Error updating stats:', error);
    }
}

function extractContractData(card, id) {
    // This function is now deprecated as we load from API
    // Kept for backwards compatibility
    const title = card.querySelector('.card-title')?.textContent || card.querySelector('.contract-info h3')?.textContent || '';
    const contractId = card.querySelector('.card-contract-number')?.textContent || card.querySelector('.contract-id')?.textContent || `CNT-2025-${String(id).padStart(3, '0')}`;
    const clientName = card.querySelector('.card-client-name')?.textContent || card.querySelector('.client-details h4')?.textContent || '';
    const statusEl = card.querySelector('.card-status-badge span') || card.querySelector('.contract-status');
    const status = statusEl ? statusEl.textContent.trim().toLowerCase() : 'active';
    const value = card.querySelector('.card-value-badge')?.textContent || card.querySelector('.contract-value')?.textContent || 'LKR 0';
    const progressElement = card.querySelector('.card-progress-fill') || card.querySelector('.progress-fill');
    const progress = progressElement ? parseInt(progressElement.style.width) || 0 : 0;

    return {
        id,
        contractId,
        title,
        clientName,
        status,
        value,
        progress,
        type: card.getAttribute('data-type') || 'general',
        startDate: '2025-01-15',
        endDate: '2025-03-15',
        description: ''
    };
}


// ===================================
// FILTER SYSTEM
// ===================================

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');

    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (dateFilter) dateFilter.addEventListener('change', applyFilters);
}

function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;

    // Filter the original data
    let filteredData = contractsData;

    if (statusFilter) {
        filteredData = filteredData.filter(contract => contract.status === statusFilter);
    }

    if (typeFilter) {
        filteredData = filteredData.filter(contract => contract.type === typeFilter);
    }

    // For date filter, you can add custom logic here
    // For now, we'll just use the filtered data

    // Reset pagination
    currentPage = 1;

    // Update displayed contracts based on filtered data
    if (filteredData.length === 0) {
        const container = document.getElementById('contractsContainer');
        const empty = document.getElementById('contractsEmpty');
        container.innerHTML = '';
        if (empty) {
            const emptyClone = empty.cloneNode(true);
            emptyClone.style.display = 'flex';
            emptyClone.querySelector('h3').textContent = 'No Matching Contracts';
            emptyClone.querySelector('p').textContent = 'Try adjusting your filters to see more results.';
            container.appendChild(emptyClone);
        }
        hideLoadMoreButton();
        hideEndIndicator();
    } else {
        // Temporarily update contractsData for rendering
        const originalData = contractsData;
        contractsData = filteredData;
        renderContractsPage();
        contractsData = originalData; // Restore original data
    }

    updateResultsCount(filteredData.length);
}

function updateResultsCount(count) {

}

// ===================================
// VIEW SWITCHER
// ===================================

function initializeViewSwitcher() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const contractsContainer = document.getElementById('contractsContainer');

    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const viewType = this.getAttribute('data-view');

            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            if (viewType === 'list') {
                contractsContainer.classList.add('list-view');
            } else {
                contractsContainer.classList.remove('list-view');
            }

            localStorage.setItem('contractsViewPreference', viewType);
        });
    });

    // Load saved preference
    const savedView = localStorage.getItem('contractsViewPreference');
    if (savedView) {
        const targetButton = document.querySelector(`[data-view="${savedView}"]`);
        if (targetButton) targetButton.click();
    }
}

// ===================================
// CONTRACT ACTIONS
// ===================================

let contractActionsInitialized = false;

function initializeContractActions() {
    // Prevent adding duplicate global listeners
    if (contractActionsInitialized) return;
    contractActionsInitialized = true;

    document.addEventListener('click', function (e) {
        // --- View ---
        if (e.target.closest('.view-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.view-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleViewContractById(contractId);
            return;
        }

        // --- Send to Customer ---
        if (e.target.closest('.send-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.send-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleSendContractById(contractId);
            return;
        }

        // --- Chat ---
        if (e.target.closest('.chat-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.chat-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleChatContractById(contractId);
            return;
        }



        // --- Edit ---
        if (e.target.closest('.edit-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.edit-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleEditContractById(contractId);
            return;
        }

        // --- Delete ---
        if (e.target.closest('.delete-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.delete-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleDeleteContractById(contractId);
            // Close any open menu
            document.querySelectorAll('.card-action-menu.active').forEach(m => m.classList.remove('active'));
            return;
        }

        // --- Download ---
        if (e.target.closest('.download-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.download-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleDownloadContractById(contractId);
            document.querySelectorAll('.card-action-menu.active').forEach(m => m.classList.remove('active'));
            return;
        }

        // --- More menu toggle ---
        if (e.target.closest('.more-menu-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.more-menu-btn');
            const contractId = btn.getAttribute('data-contract-id');
            toggleCardMenu(e, contractId, btn);
            return;
        }

        // --- Terminate ---
        if (e.target.closest('.terminate-contract-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const btn = e.target.closest('.terminate-contract-btn');
            const contractId = btn.getAttribute('data-contract-id');
            handleTerminateContract(contractId);
            document.querySelectorAll('.card-action-menu.active').forEach(m => m.classList.remove('active'));
            return;
        }

        // --- Card click (view details) ---
        if (e.target.closest('.contract-card') && !e.target.closest('.card-action-btn') && !e.target.closest('.card-menu-item') && !e.target.closest('.card-action-menu')) {
            const card = e.target.closest('.contract-card');
            const contractId = card.getAttribute('data-contract-id');
            handleViewContractById(contractId);
        }
    });

    // New contract button
    const newContractBtn = document.getElementById('newContractBtn');
    if (newContractBtn) {
        newContractBtn.addEventListener('click', openNewContractModal);
    }
}

/**
 * Open chat widget for a contract
 */
function handleChatContractById(contractId) {
    // Find the contract in the loaded data
    let contract = contractsData.find(c => c.contract_id == contractId);

    if (contract) {
        ChatWidget.open(contractId, {
            name: contract.client_name || contract.customer_name || 'Customer',
            contractNumber: contract.contract_number || ('Contract #' + contractId)
        });
    } else {
        // Fallback: open with minimal info
        ChatWidget.open(contractId, {
            name: 'Customer',
            contractNumber: 'Contract #' + contractId
        });
    }
}
async function handleViewContractById(contractId) {
    try {
        // Always fetch full data from API for the detail view
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Contract not found');
        }

        openContractDetailsModal(result.data);
    } catch (error) {
        console.error('Error viewing contract:', error);
        alert('Failed to load contract details: ' + error.message);
    }
}

async function handleEditContractById(contractId) {
    try {
        // Always fetch full data from API for editing
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Contract not found');
        }

        openEditContractModal(result.data);
    } catch (error) {
        console.error('Error editing contract:', error);
        alert('Failed to load contract for editing: ' + error.message);
    }
}

async function handleDownloadContractById(contractId) {
    try {
        // Open the PDF in a new window for printing
        const url = `/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=downloadPDF&id=${contractId}`;

        // Open in new window
        const printWindow = window.open(url, '_blank', 'width=800,height=600');

        if (!printWindow) {
            throw new Error('Please allow pop-ups to download the contract PDF');
        }

        console.log(`Opening contract ${contractId} for download/print`);

    } catch (error) {
        console.error('Error downloading contract:', error);
        alert('Failed to download contract: ' + error.message);
    }
}

async function handleSendContractById(contractId) {
    try {
        // Find contract in local data
        let contract = contractsData.find(c => c.contract_id == contractId);

        if (!contract) {
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Contract not found');
            contract = result.data;
        }

        // Build a data object for the send modal
        currentContract = {
            id: contract.contract_number || contract.contract_id,
            title: contract.title,
            client: contract.client_name,
            email: contract.client_email || '',
            contractId: contract.contract_id
        };

        openSendContractModal(currentContract);
    } catch (error) {
        console.error('Error preparing send:', error);
        alert('Failed to prepare contract for sending: ' + error.message);
    }
}



// Keep legacy functions for backward compatibility
function handleViewContract(contractCard) {
    const contractId = contractCard.getAttribute('data-contract-id');
    if (contractId) {
        handleViewContractById(contractId);
    } else {
        const contractData = extractDetailedContractData(contractCard);
        openContractDetailsModal(contractData);
    }
}


function extractDetailedContractData(card) {
    const title = card.querySelector('.card-title')?.textContent || card.querySelector('.contract-info h3')?.textContent || '';
    const contractId = card.querySelector('.card-contract-number')?.textContent || card.querySelector('.contract-id')?.textContent || '';
    const clientName = card.querySelector('.card-client-name')?.textContent || card.querySelector('.client-details h4')?.textContent || '';
    const statusElement = card.querySelector('.card-status-badge span') || card.querySelector('.contract-status');
    const status = statusElement ? statusElement.textContent.trim() : 'Active';
    const value = card.querySelector('.card-value-badge')?.textContent || card.querySelector('.contract-value')?.textContent || 'LKR 0';
    const progressElement = card.querySelector('.card-progress-fill') || card.querySelector('.progress-fill');
    const progress = progressElement ? parseInt(progressElement.style.width) || 0 : 0;
    const clientEmail = card.querySelector('.card-client-email')?.textContent || '';

    return {
        id: contractId,
        title,
        client: clientName,
        status: status.toLowerCase(),
        progress,
        value,
        description: '',
        contactPerson: clientName,
        email: clientEmail || 'N/A',
        phone: '+94 77 123 4567',
        type: card.getAttribute('data-type') || 'General',
        location: 'Colombo, Sri Lanka',
        startDate: '2025-01-15',
        endDate: '2025-03-15',
        stage: getProgressStage(progress),
        paidAmount: calculatePaidAmount(value, progress),
        remainingAmount: calculateRemainingAmount(value, progress),
        paymentTerms: '30 Days',
        contractType: 'Fixed Price',
        duration: '60 Days',
        priority: 'High',
        team: 'Team Alpha'
    };
}

function getProgressStage(progress) {
    if (progress < 25) return 'Planning Phase';
    if (progress < 50) return 'Design Phase';
    if (progress < 75) return 'Implementation Phase';
    if (progress < 90) return 'Testing Phase';
    return 'Final Review';
}

function calculatePaidAmount(value, progress) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const paidAmount = Math.floor(numValue * progress / 100);
    return `LKR ${paidAmount.toLocaleString()}`;
}

function calculateRemainingAmount(value, progress) {
    const numValue = parseInt(value.replace(/[^\d]/g, ''));
    const remaining = numValue - Math.floor(numValue * progress / 100);
    return `LKR ${remaining.toLocaleString()}`;
}

function handleEditContract(contractCard) {
    const contractData = extractDetailedContractData(contractCard);
    openEditContractModal(contractData);
}

function handleDownloadContract(contractCard) {
    const title = contractCard.querySelector('.contract-info h3')?.textContent || 'Contract';
    showNotification(`Downloading ${title}...`, 'info');

    // Simulate download
    setTimeout(() => {
        const contractId = contractCard.querySelector('.contract-id')?.textContent || 'CNT-001';
        downloadContractPDF(contractId, title);
        showNotification('Contract downloaded successfully!', 'success');
    }, 1500);
}

function downloadContractPDF(contractId, title) {
    // Create a simple text file as placeholder
    const content = `CONTRACT AGREEMENT\n\n${contractId}\n${title}\n\nFixLanka Services\nwww.fixlanka.com\n\nThis is a placeholder contract document.`;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${contractId.replace(/[^a-zA-Z0-9]/g, '_')}_contract.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function handleSendContract(contractCard) {
    currentContract = extractDetailedContractData(contractCard);
    openSendContractModal(currentContract);
}

function handleGenerateInvoice(contractCard) {
    const title = contractCard.querySelector('.contract-info h3')?.textContent || 'Contract';
    showNotification(`Generating invoice for ${title}...`, 'info');

    setTimeout(() => {
        showNotification('Invoice generated successfully!', 'success');
    }, 1500);
}

// ===================================
// CONTRACT DETAILS MODAL
// ===================================

function initializeModals() {
    const modal = document.getElementById('contractModal');
    const closeBtn = document.getElementById('closeModal');
    const modalClose = document.getElementById('modalClose');
    const modalEdit = document.getElementById('modalEdit');
    const modalDownload = document.getElementById('modalDownload');
    const modalPrint = document.getElementById('modalPrint');

    if (closeBtn) closeBtn.addEventListener('click', closeContractDetailsModal);
    if (modalClose) modalClose.addEventListener('click', closeContractDetailsModal);

    if (modalEdit) {
        modalEdit.addEventListener('click', function () {
            closeContractDetailsModal();
            if (currentContract) {
                openEditContractModal(currentContract);
            }
        });
    }

    if (modalDownload) {
        modalDownload.addEventListener('click', function () {
            if (currentContract) {
                downloadContractPDF(currentContract.id, currentContract.title);
                showNotification('Contract downloaded successfully!', 'success');
            }
        });
    }

    if (modalPrint) {
        modalPrint.addEventListener('click', function () {
            showNotification('Preparing contract for printing...', 'info');
            setTimeout(() => window.print(), 1000);
        });
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeContractDetailsModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeContractDetailsModal();
        }
    });
}

function openContractDetailsModal(contractData) {
    currentContract = contractData;
    const modal = document.getElementById('contractModal');

    if (modal) {
        populateModalContent(contractData);
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeContractDetailsModal() {
    const modal = document.getElementById('contractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function populateModalContent(data) {
    // Prefer shared renderer so company + customer views stay identical
    if (window.ContractPreview && typeof window.ContractPreview.renderHTML === 'function') {
        const container = document.getElementById('viewContractPreview');
        if (container) {
            container.innerHTML = window.ContractPreview.renderHTML(data, {
                isMilestoneBased: data.payment_method === 'milestone_based',
                paymentLabel: (function () {
                    const map = { 'full_upfront': 'Full Upfront', 'milestone_based': 'Milestone-Based', '50_50': '50/50 Split', '30_70': '30/70 Split', 'completion': 'On Completion' };
                    return map[data.payment_method] || 'Standard';
                })(),
                renderMilestoneAction: () => '—'
            });
        }
        return;
    }

    // Helper
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val || ''; };
    const formatDate = (d) => { if (!d) return ''; const dt = new Date(d); return dt.toLocaleDateString('en-LK', { year: 'numeric', month: 'long', day: 'numeric' }); };

    // Header
    set('viewRef', `Contract Reference: ${data.contract_number || data.id || ''}`);
    set('viewDate', formatDate(data.contract_date));

    const statusEl = document.getElementById('viewStatus');
    if (statusEl) {
        const statusLabel = (data.status || 'draft').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        statusEl.textContent = statusLabel;
        statusEl.className = `contract-status-badge ${data.status || 'draft'}`;
    }

    // Section 1: Parties
    const client = data.client || {};
    set('viewClientName', client.name || data.client_name || '');
    set('viewClientDetails', [client.email, client.address, client.district].filter(Boolean).join(' | ') || '');

    const company = data.company || {};
    set('viewCompanyName', company.name || '');
    set('viewCompanyDetails', [company.registration_no, company.address, company.contact].filter(Boolean).join(' | ') || '');

    // Section 2: Project
    set('viewTitle', data.title || data.project_title || '');
    set('viewProjectRef', data.project_reference || '');
    set('viewLocation', data.location || data.project_location || '');
    set('viewType', data.type || '');
    set('viewDescription', data.description || '');

    // Section 3: Scope
    const scopeSection = document.getElementById('viewScopeSection');
    if (scopeSection) {
        const hasScope = data.scope_description || data.scope_inclusions || data.scope_exclusions;
        scopeSection.style.display = hasScope ? 'block' : 'none';
    }
    set('viewScopeDesc', data.scope_description || '');
    set('viewInclusions', data.scope_inclusions || 'As per quotation');
    set('viewExclusions', data.scope_exclusions || 'None specified');

    const matLabels = { 'company': 'All materials supplied by the Contractor', 'client': 'All materials supplied by the Client', 'shared': 'Shared responsibility' };
    set('viewMaterials', matLabels[data.materials_responsibility] || 'As per agreement');

    // Section 4: Timeline
    set('viewStartDate', formatDate(data.start_date));
    set('viewEndDate', formatDate(data.end_date));
    set('viewProgress', (data.progress || 0) + '%');

    // Milestones
    const msContainer = document.getElementById('viewMilestonesContainer');
    if (msContainer && data.milestones && data.milestones.length > 0) {
        const isMilestonePayment = data.payment_method === 'milestone_based';
        let html = '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>Due Date</th><th>Status</th>';
        if (isMilestonePayment) html += '<th>Amount</th>';
        html += '</tr></thead><tbody>';
        data.milestones.forEach((ms, i) => {
            const msStatus = ms.status || 'pending';
            const statusIcon = msStatus === 'completed' ? '?' : msStatus === 'in_progress' ? '??' : '?';
            html += `<tr>
                <td>${i + 1}</td>
                <td>${escapeHtml(ms.title || ms.milestone_name || 'Milestone ' + (i + 1))}</td>
                <td>${formatDate(ms.due_date)}</td>
                <td>${statusIcon} ${msStatus.replace(/_/g, ' ')}</td>`;
            if (isMilestonePayment) {
                html += `<td>LKR ${parseFloat(ms.payment_amount || 0).toLocaleString()}</td>`;
            }
            html += '</tr>';
        });
        html += '</tbody></table>';
        msContainer.innerHTML = html;
    } else if (msContainer) {
        msContainer.innerHTML = '<p style="color:#94a3b8;font-style:italic;">No milestones defined</p>';
    }

    // Section 5: Financial
    const totalVal = parseFloat(data.value || 0);
    set('viewValue', `LKR ${totalVal.toLocaleString()}`);

    const budgetTypes = { 'fixed': 'Fixed Price', 'time_based': 'Time-Based', 'flexible': 'Flexible (±10%)' };
    set('viewBudgetType', budgetTypes[data.budget_type] || 'Fixed Price');

    const payMethodLabels = { 'full_upfront': 'Full Upfront', 'milestone_based': 'Milestone-Based', '50_50': '50/50 Split', '30_70': '30/70 Split', 'completion': 'On Completion' };
    set('viewPaymentMethod', payMethodLabels[data.payment_method] || 'Standard');

    set('viewAmountPaid', `LKR ${parseFloat(data.amount_paid || 0).toLocaleString()}`);
    set('viewAmountPending', `LKR ${parseFloat(data.amount_pending || totalVal).toLocaleString()}`);
    set('viewLatePayment', data.late_payment_penalty || 'As per standard terms');

    // Payment Schedule Breakdown
    const scheduleContainer = document.getElementById('viewPaymentSchedule');
    if (scheduleContainer) {
        const method = data.payment_method || 'full_upfront';
        let scheduleHTML = '<h5 style="margin:0 0 8px;font-size:14px;color:#334155;"><i class="fas fa-receipt" style="margin-right:6px;color:#0abab5;"></i>Payment Schedule</h5>';

        if (method === 'milestone_based' && data.milestones && data.milestones.length > 0) {
            // Milestone-based: show each milestone with payment amount
            scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>%</th><th>Amount (LKR)</th></tr></thead><tbody>';
            data.milestones.forEach((ms, i) => {
                const pct = parseFloat(ms.payment_percentage || ms.percentage || 0);
                const amt = parseFloat(ms.payment_amount || ms.amount || (totalVal * pct / 100));
                scheduleHTML += `<tr><td>${i + 1}</td><td>${escapeHtml(ms.title || ms.milestone_name || 'Milestone ' + (i + 1))}</td><td>${pct}%</td><td>LKR ${amt.toLocaleString()}</td></tr>`;
            });
            scheduleHTML += '</tbody></table>';
        } else {
            // Fixed schedule types
            const schedules = {
                'full_upfront': [{ label: 'Full Payment Upfront', pct: 100 }],
                '50_50': [{ label: 'Upfront Payment', pct: 50 }, { label: 'On Completion', pct: 50 }],
                '30_70': [{ label: 'Advance Payment', pct: 30 }, { label: 'On Completion', pct: 70 }],
                'completion': [{ label: 'Full Payment After Completion', pct: 100 }]
            };
            const items = schedules[method] || [{ label: 'Full Payment', pct: 100 }];
            scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>Payment</th><th>%</th><th>Amount (LKR)</th></tr></thead><tbody>';
            items.forEach(item => {
                const amt = totalVal * item.pct / 100;
                scheduleHTML += `<tr><td>${item.label}</td><td>${item.pct}%</td><td>LKR ${amt.toLocaleString()}</td></tr>`;
            });
            scheduleHTML += '</tbody></table>';
        }
        scheduleContainer.innerHTML = scheduleHTML;
    }

    // Section 6: Variations
    set('viewVariation', data.variation_clause
        ? 'Any change to scope, pricing, materials, or timeline must be approved in writing by both parties before execution.'
        : 'Variation control is not enabled for this contract.');

    // Section 7: Communication
    set('viewCommChannel', 'FixLanka Platform');
    set('viewDisputeRes', data.dispute_resolution || 'Disputes shall be resolved through mediation via the FixLanka platform.');

    // Section 8: Customer Response
    const responseSection = document.getElementById('viewCustomerResponseSection');
    if (responseSection) {
        if (data.sent_to_customer) {
            responseSection.style.display = 'block';
            set('viewSentStatus', `Yes  sent on ${formatDate(data.sent_at)}`);
            const responseLabels = { 'pending': '? Pending', 'accepted': '? Accepted', 'rejected': '? Rejected', 'negotiating': '?? Negotiating' };
            set('viewCustomerResponse', responseLabels[data.customer_response] || '? Pending');
        } else {
            responseSection.style.display = 'none';
        }
    }
}

// ===================================
// NEW/EDIT CONTRACT FORM
// ===================================

function initializeNewContractForm() {
    const modal = document.getElementById('newContractModal');
    const closeBtn = document.getElementById('newContractClose');
    const cancelBtn = document.getElementById('formCancelBtn');
    const nextBtn = document.getElementById('formNextBtn');
    const prevBtn = document.getElementById('formPrevBtn');
    const submitBtn = document.getElementById('formSubmitBtn');

    if (closeBtn) closeBtn.addEventListener('click', closeNewContractModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeNewContractModal);
    if (nextBtn) nextBtn.addEventListener('click', nextFormStep);
    if (prevBtn) prevBtn.addEventListener('click', prevFormStep);
    // if (submitBtn) submitBtn.addEventListener('click', submitContractForm); // DISABLED: Handled by contract-form-enhanced.js

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeNewContractModal();
        });
    }
}

function openNewContractModal() {
    editingContract = null;
    currentStep = 1;
    const modal = document.getElementById('newContractModal');
    document.getElementById('formModalTitle').textContent = 'Create New Contract';
    document.getElementById('formSubmitBtn').innerHTML = '<i class="fas fa-paper-plane"></i> Create & Send to Customer';

    // Clear edit ID
    const editField = document.getElementById('editContractId');
    if (editField) editField.value = '';

    // Reset form
    document.getElementById('contractForm').reset();
    showFormStep(1);

    // PHASE 2: Load accepted quotations for auto-fill
    initializeQuotationSelectorForContractForm();

    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function openEditContractModal(contractData) {
    editingContract = contractData;

    // Use the IIFE-exposed edit function which:
    // - Resets form, sets editContractId, populates all fields
    // - Skips Step 1 (quotation/parties  not editable)
    // - Opens modal at Step 2 (Project Details)
    if (typeof window.openContractForEdit === 'function') {
        window.openContractForEdit(contractData);
    } else {
        // Fallback if IIFE hasn't loaded yet
        console.error('openContractForEdit not available  IIFE may not have loaded');
    }
}

function closeNewContractModal() {
    const modal = document.getElementById('newContractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        editingContract = null;
        currentStep = 1;
        // Clear edit ID
        const editField = document.getElementById('editContractId');
        if (editField) editField.value = '';
    }
}

// Load accepted quotations/projects for contract creation
async function loadAcceptedProjects() {
    const loadingContainer = document.getElementById('loadingProjects');
    const noProjectsContainer = document.getElementById('noProjects');
    const projectsList = document.getElementById('projectsList');

    // Show loading state
    loadingContainer.style.display = 'flex';
    noProjectsContainer.style.display = 'none';
    projectsList.style.display = 'none';
    projectsList.innerHTML = '';

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=getAcceptedProjects');
        const result = await response.json();





        loadingContainer.style.display = 'none';

        if (!result.success || result.data.length === 0) {

            noProjectsContainer.style.display = 'block';
            return;
        }

        // Display projects

        projectsList.style.display = 'grid';
        result.data.forEach(project => {

            const projectCard = createProjectCard(project);
            projectsList.appendChild(projectCard);
        });

    } catch (error) {
        console.error('Error loading accepted projects:', error);
        loadingContainer.style.display = 'none';
        noProjectsContainer.style.display = 'block';
        noProjectsContainer.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <h4>Error Loading Projects</h4>
            <p>Failed to load accepted projects. Please try again.</p>
        `;
    }
}

// Create project card element
function createProjectCard(project) {
    const card = document.createElement('div');
    card.className = 'project-card';
    card.dataset.projectId = project.project_id;

    card.innerHTML = `
        <div class="project-card-header">
            <div class="project-card-title">
                <h4>${escapeHtml(project.project_title)}</h4>
                <p>${escapeHtml(project.project_description || 'No description provided')}</p>
            </div>
            <span class="project-card-type">${escapeHtml(project.project_type)}</span>
        </div>
        
        <div class="project-card-details">
            <div class="project-detail-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(project.location)}</span>
            </div>
            <div class="project-detail-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Start: ${formatDate(project.proposed_start_date)}</span>
            </div>
            <div class="project-detail-item">
                <i class="fas fa-calendar-check"></i>
                <span>End: ${formatDate(project.proposed_end_date)}</span>
            </div>
            <div class="project-detail-item">
                <i class="fas fa-exclamation-circle"></i>
                <span>Priority: ${escapeHtml(project.urgency || 'Normal')}</span>
            </div>
        </div>
        
        <div class="project-card-footer">
            <div class="project-client-info">
                <i class="fas fa-user"></i>
                <span>${escapeHtml(project.client_name)}</span>
            </div>
            <div class="project-quoted-price">
                LKR ${formatCurrency(project.quoted_price)}
            </div>
        </div>
    `;

    // Store full project data in card
    card.dataset.projectData = JSON.stringify(project);

    // Add click handler
    card.addEventListener('click', () => selectProject(card, project));

    return card;
}

// Handle project selection
function selectProject(cardElement, projectData) {
    // Remove selected class from all cards
    document.querySelectorAll('.project-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Add selected class to clicked card
    cardElement.classList.add('selected');

    // Auto-fill form with project data
    autoFillProjectData(projectData);
}

// Auto-fill form with selected project data
function autoFillProjectData(project) {
    // Store project ID
    document.getElementById('selectedQuotationId').value = project.project_id || '';
    document.getElementById('selectedRequestId').value = project.project_id || '';

    // Client Information (Step 2)
    document.getElementById('clientName').value = project.client_name;
    document.getElementById('clientEmail').value = project.client_email || '';
    document.getElementById('clientPhone').value = project.client_phone || '';

    // Project Details (Step 2)
    document.getElementById('projectTitle').value = project.project_title;
    document.getElementById('projectType').value = project.project_type;
    document.getElementById('projectLocation').value = project.location;
    document.getElementById('projectDescription').value = project.project_description || '';

    // Financial & Timeline (Step 3)
    document.getElementById('contractValue').value = project.quoted_price;
    document.getElementById('startDate').value = project.proposed_start_date || '';
    document.getElementById('endDate').value = project.proposed_end_date || '';


}

// Helper function to format currency
function formatCurrency(amount) {
    return parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'Not set';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function nextFormStep() {
    if (validateFormStep(currentStep)) {
        if (currentStep === 3) {
            // Before going to review, populate review data
            populateReviewStep();
        }
        currentStep++;
        showFormStep(currentStep);
    }
}

function prevFormStep() {
    currentStep--;
    showFormStep(currentStep);
}

function showFormStep(step) {
    // Update indicators
    document.querySelectorAll('.form-step-indicator').forEach((indicator, index) => {
        if (index + 1 < step) {
            indicator.classList.add('completed');
            indicator.classList.remove('active');
        } else if (index + 1 === step) {
            indicator.classList.add('active');
            indicator.classList.remove('completed');
        } else {
            indicator.classList.remove('active', 'completed');
        }
    });

    // Update content
    document.querySelectorAll('.form-step-content').forEach((content, index) => {
        if (index + 1 === step) {
            content.classList.add('active');
        } else {
            content.classList.remove('active');
        }
    });

    // Update buttons
    const prevBtn = document.getElementById('formPrevBtn');
    const nextBtn = document.getElementById('formNextBtn');
    const submitBtn = document.getElementById('formSubmitBtn');

    if (step === 1) {
        prevBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'inline-flex';
    }

    if (step === 4) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'inline-flex';
    } else {
        nextBtn.style.display = 'inline-flex';
        submitBtn.style.display = 'none';
    }
}

function validateFormStep(step) {
    // Step 1: Validate quotation selection
    if (step === 1) {
        const quotationSelector = document.getElementById('quotationSelector');
        if (!quotationSelector || !quotationSelector.value) {
            alert('Please select an accepted quotation to create a contract');
            return false;
        }
        return true;
    }

    // Other steps: Validate required fields
    const stepContent = document.querySelector(`.form-step-content[data-step="${step}"]`);
    const requiredFields = stepContent.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });

    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }

    return isValid;
}

function populateFormWithContract(data) {
    const setVal = (id, val) => { const el = document.getElementById(id); if (el && val !== null && val !== undefined) el.value = val; };
    const setCheck = (id, val) => { const el = document.getElementById(id); if (el) el.checked = !!val; };
    const setRadio = (name, val) => { const el = document.querySelector(`input[name="${name}"][value="${val}"]`); if (el) el.checked = true; };

    // Store contract_id for update
    setVal('editContractId', data.contract_id);

    // Step 1: Quotation selection  hide it, show "Editing existing contract" info
    const client = data.client || {};
    setVal('selectedQuotationId', data.quotation_id || '');
    setVal('selectedRequestId', data.job_request_id || '');
    setVal('customerId', client.id || data.customer_id || '');

    // Step 1 party info (auto-filled from quotation, now from contract data)
    const partyClientName = document.getElementById('partyClientName');
    if (partyClientName) partyClientName.textContent = client.name || '';
    const partyClientEmail = document.getElementById('partyClientEmail');
    if (partyClientEmail) partyClientEmail.textContent = client.email || '';
    const partyClientAddress = document.getElementById('partyClientAddress');
    if (partyClientAddress) partyClientAddress.textContent = client.address || '';
    const partyClientDistrict = document.getElementById('partyClientDistrict');
    if (partyClientDistrict) partyClientDistrict.textContent = client.district || '';

    const company = data.company || {};
    const partyCompanyName = document.getElementById('partyCompanyName');
    if (partyCompanyName) partyCompanyName.textContent = company.name || '';
    const partyCompanyReg = document.getElementById('partyCompanyReg');
    if (partyCompanyReg) partyCompanyReg.textContent = company.registration_no || '';
    const partyCompanyAddress = document.getElementById('partyCompanyAddress');
    if (partyCompanyAddress) partyCompanyAddress.textContent = company.address || '';
    const partyCompanyContact = document.getElementById('partyCompanyContact');
    if (partyCompanyContact) partyCompanyContact.textContent = company.contact || '';

    // Show parties section
    const partiesSection = document.getElementById('partiesSection');
    if (partiesSection) partiesSection.style.display = 'block';

    // Step 2: Project Details
    setVal('projectTitle', data.title || data.project_title || '');
    setVal('projectReference', data.project_reference || data.contract_number || '');
    setVal('projectLocation', data.location || data.project_location || '');
    setVal('projectType', data.type || '');
    setVal('projectDescription', data.description || '');

    // Step 3: Scope of Work
    setVal('scopeDescription', data.scope_description || '');
    setVal('scopeInclusions', data.scope_inclusions || '');
    setVal('scopeExclusions', data.scope_exclusions || '');
    setVal('scopeStandards', data.scope_standards || '');
    setRadio('materials_responsibility', data.materials_responsibility || 'company');

    // Step 4: Milestones
    if (data.milestones && data.milestones.length > 0) {
        const msBody = document.getElementById('milestonesBody');
        if (msBody) {
            msBody.innerHTML = '';
            data.milestones.forEach((ms, i) => {
                const row = document.createElement('tr');
                row.className = 'milestone-row';
                row.innerHTML = `
                    <td>${i + 1}</td>
                    <td><input type="text" name="ms_name[]" value="${escapeHtml(ms.title || ms.milestone_name || '')}" class="form-input" placeholder="Milestone name"></td>
                    <td><input type="text" name="ms_desc[]" value="${escapeHtml(ms.description || '')}" class="form-input" placeholder="Description"></td>
                    <td><input type="date" name="ms_date[]" value="${ms.due_date || ''}" class="form-input"></td>
                    <td class="ms-payment-col"><input type="number" name="ms_pct[]" class="form-input ms-pct-input" value="${ms.payment_percentage || ms.percentage || 0}" min="0" max="100" step="1"></td>
                    <td class="ms-payment-col"><input type="number" name="ms_amount[]" class="form-input ms-amount-input" value="${ms.payment_amount || ms.amount || 0}" min="0" step="0.01" readonly></td>
                    <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                `;
                msBody.appendChild(row);
            });
        }
    }

    // Step 5: Payment & Financial
    setVal('contractValue', data.value || data.total_budget || '');
    setVal('budgetType', data.budget_type || 'fixed');
    setVal('budgetMin', data.budget_min || '');
    setVal('budgetMax', data.budget_max || '');
    setVal('taxInclusive', data.tax_inclusive);
    setVal('paymentMethod', data.payment_method || 'milestone_based');
    setVal('pricingType', data.pricing_type || 'fixed_price');
    setVal('hourlyRate', data.hourly_rate || '');
    setVal('spendingCap', data.spending_cap || '');

    // Step 6: Timeline (dates already set in step 2)
    setVal('startDate', data.start_date || '');
    setVal('endDate', data.end_date || '');

    // Calculate duration
    if (data.start_date && data.end_date) {
        const start = new Date(data.start_date);
        const end = new Date(data.end_date);
        const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        setVal('estimatedDuration', diff);
    }

    // Delays
    setVal('latePaymentPenalty', data.late_payment_penalty || '');
    setCheck('pauseWorkClause', data.pause_work_clause);
    setCheck('timeExtensionClause', data.time_extension_clause);

    // Step 7: Terms & Clauses
    setCheck('variationClause', data.variation_clause);
    setVal('communicationChannel', 'system');
    setVal('disputeResolution', data.dispute_resolution || '');
    setVal('additionalTerms', data.terms_conditions || '');

    // Trigger change events to update UI (payment columns, budget fields, etc.)
    const paymentMethodEl = document.getElementById('paymentMethod');
    if (paymentMethodEl) paymentMethodEl.dispatchEvent(new Event('change'));

    const budgetTypeEl = document.getElementById('budgetType');
    if (budgetTypeEl) budgetTypeEl.dispatchEvent(new Event('change'));
}

function populateReviewStep() {
    // Helper function to safely get element value
    const getValue = (id, defaultValue = '-') => {
        const element = document.getElementById(id);
        return element && element.value ? element.value : defaultValue;
    };

    // Client Info
    const clientNameEl = document.getElementById('reviewClientName');
    if (clientNameEl) clientNameEl.textContent = getValue('clientName');

    const clientTypeEl = document.getElementById('reviewClientType');
    if (clientTypeEl) clientTypeEl.textContent = getValue('clientType', 'N/A');

    const contactPersonEl = document.getElementById('reviewContactPerson');
    if (contactPersonEl) contactPersonEl.textContent = getValue('contactPerson', getValue('clientName'));

    const clientEmailEl = document.getElementById('reviewClientEmail');
    if (clientEmailEl) clientEmailEl.textContent = getValue('clientEmail');

    const clientPhoneEl = document.getElementById('reviewClientPhone');
    if (clientPhoneEl) clientPhoneEl.textContent = getValue('clientPhone', 'N/A');

    const clientAddressEl = document.getElementById('reviewClientAddress');
    if (clientAddressEl) clientAddressEl.textContent = getValue('clientAddress', 'N/A');

    // Project Details
    const projectTitleEl = document.getElementById('reviewProjectTitle');
    if (projectTitleEl) projectTitleEl.textContent = getValue('projectTitle');

    const projectTypeEl = document.getElementById('reviewProjectType');
    if (projectTypeEl) projectTypeEl.textContent = getValue('projectType');

    const projectLocationEl = document.getElementById('reviewProjectLocation');
    if (projectLocationEl) projectLocationEl.textContent = getValue('projectLocation');

    const startDateEl = document.getElementById('reviewStartDate');
    if (startDateEl) startDateEl.textContent = getValue('startDate');

    const endDateEl = document.getElementById('reviewEndDate');
    if (endDateEl) endDateEl.textContent = getValue('endDate');

    const priorityEl = document.getElementById('reviewPriority');
    if (priorityEl) priorityEl.textContent = getValue('priority', 'N/A');

    const descriptionEl = document.getElementById('reviewDescription');
    if (descriptionEl) descriptionEl.textContent = getValue('projectDescription');

    // Financial
    const valueEl = document.getElementById('reviewContractValue');
    if (valueEl) {
        const value = getValue('contractValue', '0');
        valueEl.textContent = value !== '-' && value !== '0' ? `LKR ${parseInt(value).toLocaleString()}` : 'N/A';
    }

    const contractTypeEl = document.getElementById('reviewContractType');
    if (contractTypeEl) contractTypeEl.textContent = getValue('contractType');

    const paymentTermsEl = document.getElementById('reviewPaymentTerms');
    if (paymentTermsEl) paymentTermsEl.textContent = getValue('paymentTerms');

    const advancePaymentEl = document.getElementById('reviewAdvancePayment');
    if (advancePaymentEl) {
        const advance = getValue('advancePayment', '0');
        advancePaymentEl.textContent = advance !== '-' && advance !== '0' ? `LKR ${parseInt(advance).toLocaleString()}` : 'N/A';
    }
}

function submitContractForm(e) {
    // If the enhanced form logic is active, prevent this old handler from running
    if (document.body.classList.contains('enhanced-form-active') || window.enhancedFormActive) {
        if (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
        return;
    }

    const form = document.getElementById('contractForm');
    const formData = new FormData(form);

    // Convert FormData to object
    const contractData = {};
    formData.forEach((value, key) => {
        contractData[key] = value;
    });

    // This flow creates a contract from an accepted quotation.
    // Do not send a fake project_id (it will fail FK constraints). The backend will create a project.
    contractData.quotation_id = document.getElementById('selectedQuotationId').value;
    delete contractData.project_id;

    const submitBtn = document.getElementById('formSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    // Determine if creating or updating
    const isEdit = editingContract !== null;
    const url = isEdit
        ? `/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=update&id=${editingContract.contract_id}`
        : '/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=create';

    const method = isEdit ? 'PUT' : 'POST';

    // Call actual API
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(contractData)
    })
        .then(response => response.json())
        .then(result => {

            if (result.success) {
                showNotification(
                    isEdit ? 'Contract updated successfully!' : 'Contract created successfully!',
                    'success'
                );
                closeNewContractModal();
                // Reload contracts from database
                loadContractsData();
            } else {
                showNotification(result.message || 'Failed to save contract', 'error');
            }
        })
        .catch(error => {
            console.error('Error saving contract:', error);
            showNotification('Failed to save contract. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
}

function addNewContractCard(data) {
    const container = document.getElementById('contractsContainer');
    const cardHTML = createContractCardHTML(data);
    container.insertAdjacentHTML('afterbegin', cardHTML);

    // Animate the new card
    const newCard = container.querySelector('.contract-card');
    newCard.style.opacity = '0';
    newCard.style.transform = 'scale(0.9)';
    setTimeout(() => {
        newCard.style.transition = 'all 0.3s ease';
        newCard.style.opacity = '1';
        newCard.style.transform = 'scale(1)';
    }, 100);
}

function createContractCardHTML(data) {
    const contractNumber = document.querySelectorAll('.contract-card').length + 1;
    const status = 'pending';

    return `
        <div class="contract-card" data-status="${status}" data-type="${data.projectType}">
            <div class="contract-header">
                <div class="contract-info">
                    <h3>${data.projectTitle}</h3>
                    <p class="contract-id">Contract #CNT-2025-${String(contractNumber).padStart(3, '0')}</p>
                </div>
                <div class="contract-status ${status}">
                    <i class="fas fa-clock"></i>
                    Pending
                </div>
            </div>
            <div class="client-info">
                <div class="client-avatar">${data.clientName.substring(0, 2).toUpperCase()}</div>
                <div class="client-details">
                    <h4>${data.clientName}</h4>
                    <p>${data.clientType} Client</p>
                    <span class="contract-value">LKR ${parseInt(data.contractValue).toLocaleString()}</span>
                </div>
            </div>
            <div class="contract-details">
                <div class="detail-row">
                    <i class="fas fa-calendar-alt detail-icon"></i>
                    <span class="detail-label">Start Date:</span>
                    <span class="detail-value">${formatDate(data.startDate)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-calendar-check detail-icon"></i>
                    <span class="detail-label">End Date:</span>
                    <span class="detail-value">${formatDate(data.endDate)}</span>
                </div>
                <div class="detail-row">
                    <i class="fas fa-chart-line detail-icon"></i>
                    <span class="detail-label">Progress:</span>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%"></div>
                        </div>
                        <span class="progress-text">0%</span>
                    </div>
                </div>
            </div>
            <div class="contract-description">
                <p>${data.projectDescription}</p>
            </div>
            <div class="card-actions">
                <button class="action-btn primary" title="View Details">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                <button class="action-btn secondary" title="Edit Contract">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="action-btn secondary" title="Send for Signature">
                    <i class="fas fa-paper-plane"></i>
                    Send
                </button>
            </div>
        </div>
    `;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ===================================
// SEND CONTRACT MODAL
// ===================================

function initializeSendContractModal() {
    const modal = document.getElementById('sendContractModal');
    const closeBtn = document.getElementById('sendModalClose');
    const cancelBtn = document.getElementById('sendCancelBtn');
    const submitBtn = document.getElementById('sendSubmitBtn');

    if (closeBtn) closeBtn.addEventListener('click', closeSendContractModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeSendContractModal);
    if (submitBtn) submitBtn.addEventListener('click', submitSendContract);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeSendContractModal();
        });
    }
}

function openSendContractModal(contractData) {
    const modal = document.getElementById('sendContractModal');

    // Fill confirmation details
    const titleEl = document.getElementById('sendContractTitle');
    const clientEl = document.getElementById('sendContractClient');
    const idEl = document.getElementById('sendContractId');

    if (titleEl) titleEl.textContent = contractData.title || contractData.id || '';
    if (clientEl) clientEl.textContent = contractData.client ? `Customer: ${contractData.client}` : '';
    if (idEl) idEl.value = contractData.contractId || '';

    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeSendContractModal() {
    const modal = document.getElementById('sendContractModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

async function submitSendContract() {
    const contractId = document.getElementById('sendContractId')?.value;

    if (!contractId) {
        showNotification('No contract selected', 'error');
        return;
    }

    const submitBtn = document.getElementById('sendSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=sendToCustomer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contract_id: contractId })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('Contract sent to customer successfully!', 'success');
            closeSendContractModal();

            // Update the local data so the card reflects sent + new status
            const contract = contractsData.find(c => c.contract_id == contractId);
            if (contract) {
                contract.sent_to_customer = 1;
                contract.sent_at = new Date().toISOString();
                contract.status = 'sent';
            }

            // Re-render the entire card (status badge + action buttons)
            const card = document.querySelector(`.contract-card[data-contract-id="${contractId}"]`);
            if (card && contract) {
                // Update all status badges on the card
                card.querySelectorAll('.card-status-badge').forEach(badge => {
                    badge.className = 'card-status-badge sent';
                    badge.innerHTML = '<i class="fas fa-paper-plane"></i> <span>Sent</span>';
                });
                // Update the data-status attribute
                card.setAttribute('data-status', 'sent');
                // Update action buttons (Send ? Chat)
                const actionsRow = card.querySelector('.card-actions-row');
                if (actionsRow) {
                    actionsRow.innerHTML = buildCardActions(contract);
                }
            }
        } else {
            showNotification(result.message || 'Failed to send contract', 'error');
        }
    } catch (error) {
        console.error('Error sending contract:', error);
        showNotification('Failed to send contract. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
}

// ===================================
// DELETE CONTRACT MODAL
// ===================================

function initializeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const closeBtn = document.getElementById('deleteModalClose');
    const cancelBtn = document.getElementById('deleteCancelBtn');
    const confirmBtn = document.getElementById('deleteConfirmBtn');

    if (closeBtn) closeBtn.addEventListener('click', closeDeleteModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeDeleteModal);
    if (confirmBtn) confirmBtn.addEventListener('click', confirmDeleteContract);

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeDeleteModal();
        });
    }
}

let contractToDelete = null;

async function handleDeleteContractById(contractId) {
    try {
        // Fetch contract details to show in confirmation
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Failed to fetch contract details');
        }

        openDeleteModal(contractId, result.data);
    } catch (error) {
        console.error('Error loading contract for deletion:', error);
        showNotification('Failed to load contract details', 'error');
    }
}

function openDeleteModal(contractId, contractData) {
    contractToDelete = contractId;
    const title = contractData?.client_name
        ? `${contractData.client_name} - ${contractData.project_name}`
        : 'Contract';

    const modal = document.getElementById('deleteModal');
    const infoElement = document.getElementById('deleteContractInfo');

    if (infoElement) {
        infoElement.textContent = title;
    }

    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        contractToDelete = null;
    }
}

async function confirmDeleteContract() {
    if (!contractToDelete) return;

    const confirmBtn = document.getElementById('deleteConfirmBtn');
    const originalText = confirmBtn.innerHTML;
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=delete&id=${contractToDelete}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (response.status === 401) {
            showNotification('Session expired. Please login again.', 'error');
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/auth/login.php';
            }, 2000);
            return;
        }

        if (!result.success) {
            throw new Error(result.message || 'Failed to delete contract');
        }

        showNotification('Contract deleted successfully!', 'success');
        closeDeleteModal();

        // Reload contracts from database
        loadContractsData();

    } catch (error) {
        console.error('Error deleting contract:', error);
        showNotification(error.message || 'Failed to delete contract. Please try again.', 'error');
    } finally {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    }
}

// ===================================
// EXPORT MODAL (Enhanced)
// ===================================

function initializeExportModal() {
    const exportBtn = document.getElementById('exportBtn');
    const exportModal = document.getElementById('exportModal');
    const exportModalClose = document.getElementById('exportModalClose');
    const exportCancel = document.getElementById('exportCancel');
    const exportDownload = document.getElementById('exportDownload');
    const exportPreview = document.getElementById('exportPreview');

    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            exportModal.classList.add('active');
            updateExportSummary();
        });
    }

    if (exportModalClose) {
        exportModalClose.addEventListener('click', () => {
            exportModal.classList.remove('active');
        });
    }

    if (exportCancel) {
        exportCancel.addEventListener('click', () => {
            exportModal.classList.remove('active');
        });
    }

    if (exportModal) {
        exportModal.addEventListener('click', (e) => {
            if (e.target === exportModal) {
                exportModal.classList.remove('active');
            }
        });
    }

    initializeExportFilters();
    initializeDatePresets();
    if (exportPreview) exportPreview.addEventListener('click', showExportPreview);
    if (exportDownload) exportDownload.addEventListener('click', performExport);
}

function initializeExportFilters() {
    const allCheckbox = document.getElementById('exportAll');
    const statusCheckboxes = document.querySelectorAll('#exportActive, #exportPending, #exportCompleted, #exportCancelled, #exportExpired');
    const specialCheckboxes = document.querySelectorAll('#exportWithIssues, #exportHighValue, #exportRecentUpdates');

    if (allCheckbox) {
        allCheckbox.addEventListener('change', function () {
            if (this.checked) {
                [...statusCheckboxes, ...specialCheckboxes].forEach(cb => cb.checked = false);
            }
            updateExportSummary();
        });
    }

    [...statusCheckboxes, ...specialCheckboxes].forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (this.checked && allCheckbox) allCheckbox.checked = false;
            updateExportSummary();
        });
    });

    const startDate = document.getElementById('exportStartDate');
    const endDate = document.getElementById('exportEndDate');

    if (startDate) startDate.addEventListener('change', updateExportSummary);
    if (endDate) endDate.addEventListener('change', updateExportSummary);
}

function initializeDatePresets() {
    const presetButtons = document.querySelectorAll('.preset-btn');
    const startDateInput = document.getElementById('exportStartDate');
    const endDateInput = document.getElementById('exportEndDate');

    presetButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            presetButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const days = parseInt(this.dataset.preset);
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - days);

            if (startDateInput) startDateInput.value = startDate.toISOString().split('T')[0];
            if (endDateInput) endDateInput.value = endDate.toISOString().split('T')[0];

            updateExportSummary();
        });
    });
}

function updateExportSummary() {
    const selectedFilters = getSelectedFilters();
    const estimatedCount = calculateEstimatedCount(selectedFilters);

    const summaryText = document.getElementById('exportSummaryText');
    const countElement = document.getElementById('estimatedCount');

    if (summaryText && countElement) {
        if (selectedFilters.all) {
            summaryText.textContent = 'Ready to export all contracts';
        } else if (selectedFilters.statuses.length > 0 || selectedFilters.special.length > 0) {
            const filterNames = [...selectedFilters.statuses, ...selectedFilters.special];
            summaryText.textContent = `Ready to export: ${filterNames.join(', ')}`;
        } else {
            summaryText.textContent = 'Select filters to export specific contracts';
        }

        countElement.textContent = `Estimated: ${estimatedCount} contracts`;
    }
}

function getSelectedFilters() {
    const filters = {
        all: document.getElementById('exportAll')?.checked || false,
        statuses: [],
        special: [],
        dateRange: {
            start: document.getElementById('exportStartDate')?.value || null,
            end: document.getElementById('exportEndDate')?.value || null
        },
        format: document.querySelector('input[name="exportFormat"]:checked')?.value || 'excel'
    };

    const statusMap = {
        'exportActive': 'Active',
        'exportPending': 'Pending',
        'exportCompleted': 'Completed',
        'exportCancelled': 'Cancelled',
        'exportExpired': 'Expired'
    };

    Object.keys(statusMap).forEach(id => {
        if (document.getElementById(id)?.checked) {
            filters.statuses.push(statusMap[id]);
        }
    });

    const specialMap = {
        'exportWithIssues': 'With Issues',
        'exportHighValue': 'High Value',
        'exportRecentUpdates': 'Recently Updated'
    };

    Object.keys(specialMap).forEach(id => {
        if (document.getElementById(id)?.checked) {
            filters.special.push(specialMap[id]);
        }
    });

    return filters;
}

function calculateEstimatedCount(filters) {
    const allContracts = document.querySelectorAll('.contract-card:not([style*="display: none"])');
    if (filters.all) return allContracts.length;

    let count = 0;
    allContracts.forEach(card => {
        const cardStatus = card.querySelector('.contract-status')?.textContent.trim();
        if (filters.statuses.length > 0 && filters.statuses.some(s => cardStatus?.includes(s))) {
            count++;
        }
    });

    return count || allContracts.length;
}

function showExportPreview() {
    const filters = getSelectedFilters();
    showNotification('Opening export preview...', 'info');

    setTimeout(() => {
        showNotification('Preview feature would open in a new window', 'success');
    }, 1000);
}

function performExport() {
    const filters = getSelectedFilters();
    const format = filters.format;
    const exportBtn = document.getElementById('exportDownload');
    const originalText = exportBtn.innerHTML;
    exportBtn.disabled = true;
    exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exporting...';

    setTimeout(() => {
        const timestamp = new Date().toISOString().split('T')[0];
        const filterSuffix = filters.all ? 'all' : 'filtered';
        const filename = `fixlanka-contracts-${filterSuffix}-${timestamp}`;

        if (format === 'excel' || format === 'csv') {
            downloadCSVFile(filename, filters);
        } else if (format === 'pdf') {
            downloadPDFFile(filename, filters);
        }

        exportBtn.disabled = false;
        exportBtn.innerHTML = originalText;
        document.getElementById('exportModal').classList.remove('active');
        showNotification('Export completed successfully!', 'success');
    }, 2000);
}

function downloadCSVFile(filename, filters) {
    const data = generateExportData(filters);
    const csvContent = convertToCSV(data);
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${filename}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function downloadPDFFile(filename, filters) {
    const data = generateExportData(filters);
    const textContent = data.map(row => row.join(' | ')).join('\n');
    const blob = new Blob([textContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `${filename}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

function generateExportData(filters) {
    const headers = ['Contract ID', 'Title', 'Client', 'Status', 'Value', 'Progress', 'Start Date', 'End Date'];
    const rows = [headers];

    const contracts = document.querySelectorAll('.contract-card:not([style*="display: none"])');
    contracts.forEach(card => {
        const data = extractContractData(card, rows.length);
        rows.push([
            data.contractId,
            data.title,
            data.clientName,
            data.status,
            data.value,
            `${data.progress}%`,
            data.startDate,
            data.endDate
        ]);
    });

    return rows;
}

function convertToCSV(data) {
    return data.map(row =>
        row.map(cell => `"${cell}"`).join(',')
    ).join('\n');
}

// ===================================
// SCROLL TO TOP
// ===================================

function initializeScrollToTop() {
    const scrollToTopBtn = document.getElementById('scrollToTop');
    const mainContent = document.querySelector('.main-content');

    if (!scrollToTopBtn || !mainContent) return;

    mainContent.addEventListener('scroll', function () {
        if (mainContent.scrollTop > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });

    scrollToTopBtn.addEventListener('click', function () {
        mainContent.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===================================
// NOTIFICATION SYSTEM
// ===================================

function showNotification(message, type = 'info') {
    let notification = document.getElementById('notification');

    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 24px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        `;
        document.body.appendChild(notification);
    }

    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0abab5'
    };

    notification.style.backgroundColor = colors[type] || colors.info;
    notification.textContent = message;
    notification.style.transform = 'translateX(0)';

    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
    }, 3000);
}



// ============================================
// PHASE 2A: MILESTONE FEATURES
// ============================================

/**
 * Generate milestone preview based on payment method and dates
 */
function generateMilestonePreview() {
    const totalBudget = parseFloat(document.getElementById('contractValue').value) || 0;
    const paymentMethod = document.getElementById('paymentMethod').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!totalBudget || !startDate || !endDate) {
        return [];
    }

    // Calculate duration
    const start = new Date(startDate);
    const end = new Date(endDate);
    const duration = Math.floor((end - start) / (1000 * 60 * 60 * 24));

    let milestones = [];

    switch (paymentMethod) {
        case 'full_upfront':
            milestones = [{
                number: 1,
                title: 'Full Payment',
                description: 'Complete project payment upfront',
                dueDate: startDate,
                amount: totalBudget,
                percentage: 100
            }];
            break;

        case 'milestone_based':
            const midDate = new Date(start.getTime() + (duration / 2) * 24 * 60 * 60 * 1000);
            milestones = [
                {
                    number: 1,
                    title: 'Initial Payment (30%)',
                    description: 'Project initiation and setup',
                    dueDate: startDate,
                    amount: totalBudget * 0.30,
                    percentage: 30
                },
                {
                    number: 2,
                    title: 'Mid-Project Payment (40%)',
                    description: 'Progress payment for ongoing work',
                    dueDate: midDate.toISOString().split('T')[0],
                    amount: totalBudget * 0.40,
                    percentage: 40
                },
                {
                    number: 3,
                    title: 'Final Payment (30%)',
                    description: 'Project completion and handover',
                    dueDate: endDate,
                    amount: totalBudget * 0.30,
                    percentage: 30
                }
            ];
            break;

        case '50_50':
            milestones = [
                {
                    number: 1,
                    title: 'Initial Payment (50%)',
                    description: 'First half payment at project start',
                    dueDate: startDate,
                    amount: totalBudget * 0.50,
                    percentage: 50
                },
                {
                    number: 2,
                    title: 'Final Payment (50%)',
                    description: 'Second half payment upon completion',
                    dueDate: endDate,
                    amount: totalBudget * 0.50,
                    percentage: 50
                }
            ];
            break;

        case '30_70':
            milestones = [
                {
                    number: 1,
                    title: 'Initial Payment (30%)',
                    description: 'Advance payment at project start',
                    dueDate: startDate,
                    amount: totalBudget * 0.30,
                    percentage: 30
                },
                {
                    number: 2,
                    title: 'Final Payment (70%)',
                    description: 'Completion payment upon delivery',
                    dueDate: endDate,
                    amount: totalBudget * 0.70,
                    percentage: 70
                }
            ];
            break;

        case 'completion':
            milestones = [{
                number: 1,
                title: 'Payment on Completion',
                description: 'Full payment after project completion',
                dueDate: endDate,
                amount: totalBudget,
                percentage: 100
            }];
            break;
    }

    return milestones;
}

/**
 * Display milestone preview in the review step
 */
function displayMilestonePreview() {
    const milestones = generateMilestonePreview();

    if (milestones.length === 0) {
        return '<p class="text-muted">No milestones to display</p>';
    }

    let html = `
        <div class="milestone-preview-section" style="margin-top: 20px;">
            <h4 style="color: #00897b; margin-bottom: 15px;">
                <i class="fas fa-flag-checkered"></i> Payment Milestones
            </h4>
            <div class="milestone-timeline">
    `;

    milestones.forEach((milestone, index) => {
        const isLast = index === milestones.length - 1;
        html += `
            <div class="milestone-item" style="
                position: relative;
                padding: 15px 20px;
                margin-bottom: ${isLast ? '0' : '15px'};
                background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
                border-left: 4px solid #00897b;
                border-radius: 8px;
            ">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <span style="
                                display: inline-block;
                                width: 30px;
                                height: 30px;
                                background: #00897b;
                                color: white;
                                border-radius: 50%;
                                text-align: center;
                                line-height: 30px;
                                font-weight: bold;
                                margin-right: 10px;
                            ">${milestone.number}</span>
                            <h5 style="margin: 0; color: #00695c;">${milestone.title}</h5>
                        </div>
                        <p style="margin: 5px 0 5px 40px; color: #555; font-size: 13px;">
                            ${milestone.description}
                        </p>
                        <div style="margin-left: 40px; font-size: 12px; color: #777;">
                            <i class="fas fa-calendar"></i> Due: ${new Date(milestone.dueDate).toLocaleDateString()}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="
                            font-size: 20px;
                            font-weight: bold;
                            color: #00897b;
                        ">LKR ${milestone.amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                        <div style="
                            display: inline-block;
                            padding: 3px 10px;
                            background: #00897b;
                            color: white;
                            border-radius: 12px;
                            font-size: 11px;
                            margin-top: 5px;
                        ">${milestone.percentage}%</div>
                    </div>
                </div>
            </div>
        `;
    });

    html += `
            </div>
            <div style="
                margin-top: 15px;
                padding: 12px;
                background: #fff3e0;
                border-left: 4px solid #ff9800;
                border-radius: 6px;
                font-size: 13px;
                color: #e65100;
            ">
                <i class="fas fa-info-circle"></i> 
                <strong>${milestones.length} milestone${milestones.length > 1 ? 's' : ''}</strong> will be created automatically when you submit this contract.
            </div>
        </div>
    `;

    return html;
}

/**
 * Update the populateReviewStep function to include milestones
 */
const originalPopulateReviewStep = window.populateReviewStep || function () { };

function populateReviewStep() {
    // Call original function if it exists
    if (typeof originalPopulateReviewStep === 'function') {
        originalPopulateReviewStep();
    }

    // Add milestone preview
    const reviewContent = document.querySelector('[data-step="4"] .review-content');
    if (reviewContent) {
        // Check if milestone section already exists
        if (!reviewContent.querySelector('.milestone-preview-section')) {
            const milestoneHTML = displayMilestonePreview();
            reviewContent.insertAdjacentHTML('beforeend', milestoneHTML);
        }
    }
}

/**
 * Update submit function to use new milestone API
 */
function submitContractFormWithMilestones() {
    const form = document.getElementById('contractForm');
    const formData = new FormData(form);

    const submitBtn = document.getElementById('formSubmitBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Contract...';

    // Use new API endpoint
    fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=createContractWithMilestones', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification('Contract created successfully with ' + result.data.milestones.length + ' milestones!', 'success');
                closeNewContractModal();
                loadContractsData();
            } else {
                showNotification(result.message || 'Failed to create contract', 'error');
            }
        })
        .catch(error => {
            console.error('Error creating contract:', error);
            showNotification('Failed to create contract. Please try again.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
}

// Override the original submit function
const originalSubmitContractForm = window.submitContractForm;
window.submitContractForm = function () {
    // Check if we should use milestone version
    const quotationId = document.getElementById('selectedQuotationId')?.value;
    if (quotationId) {
        submitContractFormWithMilestones();
    } else if (typeof originalSubmitContractForm === 'function') {
        originalSubmitContractForm();
    }
};

// Add listeners for milestone preview updates
document.addEventListener('DOMContentLoaded', function () {
    // Listen for changes that affect milestones
    const fields = ['contractValue', 'paymentMethod', 'startDate', 'endDate'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', function () {
                // Update preview if we're on the review step
                if (currentStep === 4) {
                    populateReviewStep();
                }
            });
        }
    });
});

console.log('? Phase 2A: Milestone features loaded');

/**
 * Handle instantly starting a project from an accepted contract card
 */
function handleStartProjectFromContract(contractId) {
    // If our cached list already knows a project exists, redirect.
    try {
        const existing = Array.isArray(window.contractsData)
            ? window.contractsData.find(c => String(c?.contract_id) === String(contractId))
            : (Array.isArray(contractsData) ? contractsData.find(c => String(c?.contract_id) === String(contractId)) : null);
        const existingPid = existing?.project_id;
        if (existingPid) {
            showNotification(`Project already started (Project #${existingPid}). Redirecting...`, 'info');
            setTimeout(() => { window.location.href = 'projects.php'; }, 600);
            return;
        }
    } catch (_) {
        // ignore
    }

    const confirmPromise = (window.showConfirm && typeof window.showConfirm === 'function')
        ? window.showConfirm('Are you ready to start this project? This will create a new tracking instance on your Projects dashboard.', {
            title: 'Start Project',
            confirmText: 'Start',
            type: 'info'
        })
        : Promise.resolve(confirm('Are you ready to start this project? This will create a new tracking instance on your Projects dashboard.'));

    confirmPromise.then(confirmed => {
        if (!confirmed) return;

        const btn = document.querySelector(`.card-action-btn[onclick="handleStartProjectFromContract(${contractId})"]`);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
            btn.disabled = true;
        }

        fetch('/2nd-Year-Group-Project/FixLanka/api/projects.php?action=start_from_contract', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contract_id: contractId })
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Project started successfully! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'projects.php';
                    }, 1000);
                    return;
                }

                const code = String(data.code || '');
                const existingProjectId = data.project_id || data.projectId;
                const msg = String(data.message || 'Failed to start project.');

                if (code === 'already_started' || /already\s+been\s+started/i.test(msg)) {
                    showNotification(
                        existingProjectId
                            ? `Project already started (Project #${existingProjectId}). Redirecting...`
                            : 'Project already started. Redirecting...',
                        'info'
                    );
                    setTimeout(() => {
                        window.location.href = 'projects.php';
                    }, 800);
                    return;
                }

                showNotification(msg, 'error');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-rocket"></i> Start Project';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                showNotification('An error occurred while starting the project.', 'error');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-rocket"></i> Start Project';
                    btn.disabled = false;
                }
            });
    });
}
