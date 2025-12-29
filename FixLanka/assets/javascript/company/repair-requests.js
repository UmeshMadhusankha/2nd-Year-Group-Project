const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
const viewButtons = document.querySelectorAll('.view-btn');
const quotationModal = document.getElementById('quotation-modal');
const quotationForm = document.getElementById('quotation-form');

// Store quotations in localStorage (will be replaced with database later)
let quotations = JSON.parse(localStorage.getItem('quotations')) || [];
let editingQuotationId = null;

// Load mock data for testing
function loadMockQuotations() {
    const mockQuotations = [
        {
            quotation_id: "QUOT-1729670400000",
            request_id: "REQ-2025-001",
            title: "Kitchen Cabinet Repair",
            description: "Repair and refinish kitchen cabinets including hardware replacement, surface restoration, and protective coating application.",
            labor_cost: 25000.00,
            material_cost: 18000.00,
            transport_cost: 2500.00,
            other_cost: 1500.00,
            total_price: 47000.00,
            estimated_start_date: "2025-10-28",
            estimated_completion_date: "2025-11-05",
            estimated_duration: 8,
            payment_terms: "50% upfront, 50% on completion",
            warranty_period: "6 Months",
            terms_conditions: "All materials included. Work hours: 8 AM - 5 PM. Customer to provide workspace access.",
            validity_period: 30,
            status: "pending",
            submitted_at: "2025-10-23T08:30:00.000Z",
            updated_at: "2025-10-23T08:30:00.000Z"
        },
        {
            quotation_id: "QUOT-1729670500000",
            request_id: "REQ-2025-002",
            title: "Bathroom Plumbing Repair",
            description: "Complete plumbing repair including pipe replacement, fixture installation, and leak fixing for bathroom.",
            labor_cost: 35000.00,
            material_cost: 42000.00,
            transport_cost: 3000.00,
            other_cost: 5000.00,
            total_price: 85000.00,
            estimated_start_date: "2025-10-25",
            estimated_completion_date: "2025-10-30",
            estimated_duration: 5,
            payment_terms: "40% upfront, 30% mid-project, 30% on completion",
            warranty_period: "1 Year",
            terms_conditions: "High-quality PVC pipes and brass fittings. Water supply will be temporarily disconnected during work.",
            validity_period: 15,
            status: "pending",
            submitted_at: "2025-10-23T09:15:00.000Z",
            updated_at: "2025-10-23T09:15:00.000Z"
        },
        {
            quotation_id: "QUOT-1729670600000",
            request_id: "REQ-2025-003",
            title: "Roof Leak Repair",
            description: "Comprehensive roof leak detection and repair with waterproofing treatment for tiled roof section.",
            labor_cost: 45000.00,
            material_cost: 38000.00,
            transport_cost: 4000.00,
            other_cost: 3000.00,
            total_price: 90000.00,
            estimated_start_date: "2025-11-01",
            estimated_completion_date: "2025-11-12",
            estimated_duration: 11,
            payment_terms: "50% upfront, 50% on completion",
            warranty_period: "2 Years",
            terms_conditions: "Weather-dependent schedule. Includes 5-year warranty on waterproofing. Customer to clear roof access.",
            validity_period: 45,
            status: "accepted",
            submitted_at: "2025-10-22T10:00:00.000Z",
            updated_at: "2025-10-23T07:45:00.000Z",
            accepted_at: "2025-10-23T07:45:00.000Z"
        },
        {
            quotation_id: "QUOT-1729670700000",
            request_id: "REQ-2025-004",
            title: "Electrical Wiring Upgrade",
            description: "Upgrade old electrical wiring system with modern cables, circuit breakers, and safety switches for entire house.",
            labor_cost: 65000.00,
            material_cost: 85000.00,
            transport_cost: 5000.00,
            other_cost: 10000.00,
            total_price: 165000.00,
            estimated_start_date: "2025-11-05",
            estimated_completion_date: "2025-11-20",
            estimated_duration: 15,
            payment_terms: "30% upfront, 40% mid-project, 30% on completion",
            warranty_period: "3 Years",
            terms_conditions: "Certified electricians only. Includes electrical safety certificate. Power outages during installation required.",
            validity_period: 60,
            status: "accepted",
            submitted_at: "2025-10-21T14:20:00.000Z",
            updated_at: "2025-10-22T11:30:00.000Z",
            accepted_at: "2025-10-22T11:30:00.000Z"
        },
        {
            quotation_id: "QUOT-1729670800000",
            request_id: "REQ-2025-005",
            title: "Living Room Floor Tiling",
            description: "Remove old flooring and install premium ceramic tiles in living room area with grouting and sealing.",
            labor_cost: 38000.00,
            material_cost: 72000.00,
            transport_cost: 3500.00,
            other_cost: 4500.00,
            total_price: 118000.00,
            estimated_start_date: "2025-10-26",
            estimated_completion_date: "2025-11-03",
            estimated_duration: 8,
            payment_terms: "50% upfront, 50% on completion",
            warranty_period: "1 Year",
            terms_conditions: "Customer to select tile design from our catalog. Room to be emptied before work begins. 48-hour curing time after completion.",
            validity_period: 30,
            status: "pending",
            submitted_at: "2025-10-23T11:00:00.000Z",
            updated_at: "2025-10-23T11:00:00.000Z"
        },
        {
            quotation_id: "QUOT-1729670900000",
            request_id: "REQ-2025-006",
            title: "Window Frame Replacement",
            description: "Replace 6 old wooden window frames with modern aluminum frames including glass, mesh screens, and installation.",
            labor_cost: 42000.00,
            material_cost: 96000.00,
            transport_cost: 6000.00,
            other_cost: 8000.00,
            total_price: 152000.00,
            estimated_start_date: "2025-11-10",
            estimated_completion_date: "2025-11-22",
            estimated_duration: 12,
            payment_terms: "40% upfront, 60% on completion",
            warranty_period: "5 Years",
            terms_conditions: "Powder-coated aluminum frames with double-glazed glass. Measurements to be verified on-site. Old frames disposal included.",
            validity_period: 45,
            status: "accepted",
            submitted_at: "2025-10-20T13:45:00.000Z",
            updated_at: "2025-10-21T09:20:00.000Z",
            accepted_at: "2025-10-21T09:20:00.000Z"
        }
    ];
    
    // Only load if no quotations exist
    if (quotations.length === 0) {
        quotations = mockQuotations;
        localStorage.setItem('quotations', JSON.stringify(quotations));
        
        
        
        
        
        ');
        
        ');
        
        ');
        
        )');
    } else {
        
        " to restore default mock data', 'color: #888; font-style: italic;');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadMockQuotations(); // Load mock data first
    initializeTabs();
    initializeFilters();
    initializeSearch();
    initializeViewControls();
    initializeModals();
    setActiveNavigation();
    loadQuotationsToLogs();
});

function setActiveNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const linkText = link.querySelector('span')?.textContent;
        const href = link.getAttribute('href');
        
        if (linkText === 'Repair Requests' || href === 'repair-requests.php') {
            link.closest('.nav-item').classList.add('active');
        }
    });
    
    localStorage.setItem('activePage', 'Repair Requests');
}
function initializeTabs() {
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(tabName).classList.add('active');
    
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}
function initializeFilters() {
    const filterSelects = document.querySelectorAll('.filter-select');
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
}

function applyFilters() {
    const serviceFilter = document.getElementById('service-filter')?.value || '';
    const priorityFilter = document.getElementById('priority-filter')?.value || '';
    const locationFilter = document.getElementById('location-filter')?.value || '';
    
    const requestCards = document.querySelectorAll('.request-card');
    
    requestCards.forEach(card => {
        let showCard = true;
        
        if (serviceFilter && !cardMatchesService(card, serviceFilter)) showCard = false;
        if (priorityFilter && !cardMatchesPriority(card, priorityFilter)) showCard = false;
        if (locationFilter && !cardMatchesLocation(card, locationFilter)) showCard = false;
        
        if (showCard) {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
        } else {
            card.style.opacity = '0';
            setTimeout(() => card.style.display = 'none', 300);
        }
    });
}

function cardMatchesService(card, service) {
    const title = card.querySelector('.request-title').textContent.toLowerCase();
    return title.includes(service.toLowerCase());
}

function cardMatchesPriority(card, priority) {
    const priorityBadge = card.querySelector('.priority-badge');
    return priorityBadge && priorityBadge.classList.contains(priority);
}

function cardMatchesLocation(card, location) {
    const locationText = card.querySelector('.detail-value').textContent.toLowerCase();
    return locationText.includes(location.toLowerCase());
}

function initializeSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            performSearch(this.value.toLowerCase());
        });
    });
}

function performSearch(searchTerm) {
    const requestCards = document.querySelectorAll('.request-card');
    const tableRows = document.querySelectorAll('.requests-table tbody tr');
    
    requestCards.forEach(card => {
        const title = card.querySelector('.request-title').textContent.toLowerCase();
        const customer = card.querySelector('.customer-details h4').textContent.toLowerCase();
        const description = card.querySelector('.request-description p').textContent.toLowerCase();
        
        const matches = title.includes(searchTerm) || customer.includes(searchTerm) || description.includes(searchTerm);
        
        if (matches || searchTerm === '') {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
        } else {
            card.style.opacity = '0';
            setTimeout(() => card.style.display = 'none', 300);
        }
    });
    
    tableRows.forEach(row => {
        const title = row.querySelector('h5').textContent.toLowerCase();
        const customer = row.querySelector('.table-customer-info h5').textContent.toLowerCase();
        const matches = title.includes(searchTerm) || customer.includes(searchTerm);
        row.style.display = matches || searchTerm === '' ? 'table-row' : 'none';
    });
}
function initializeViewControls() {
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const viewType = this.dataset.view;
            switchView(viewType);
        });
    });
}

function switchView(viewType) {
    viewButtons.forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-view="${viewType}"]`).classList.add('active');
    
    const requestsGrid = document.querySelector('.requests-grid');
    
    if (viewType === 'grid') {
        requestsGrid.style.display = 'grid';
        requestsGrid.classList.remove('list-view');
    } else {
        requestsGrid.style.display = 'block';
        requestsGrid.classList.add('list-view');
    }
}

function initializeModals() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeAllModals();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

function openQuotationModal(requestId) {
    const modal = document.getElementById('quotation-modal');
    const requestDetails = document.getElementById('quotation-request-details');
    const requestIdInput = document.getElementById('request-id');
    
    // Store the request ID
    requestIdInput.value = requestId;
    
    // Get request data from the card
    const requestCard = document.querySelector(`[onclick*="${requestId}"]`)?.closest('.request-card');
    let requestInfo = {
        id: requestId,
        title: 'Repair Request',
        category: 'General',
        customer: 'Customer',
        district: 'N/A'
    };
    
    if (requestCard) {
        requestInfo.title = requestCard.querySelector('.request-title')?.textContent || requestInfo.title;
        requestInfo.category = requestCard.querySelector('.category-label')?.textContent || requestInfo.category;
        requestInfo.customer = requestCard.querySelector('.customer-details h4')?.textContent || requestInfo.customer;
        requestInfo.district = requestCard.querySelector('.detail-value')?.textContent || requestInfo.district;
    }
    
    // Populate request details
    requestDetails.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h4 style="margin: 0 0 var(--spacing-xs) 0; color: var(--text-primary);">${requestInfo.title}</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                    Request #${requestId} â€¢ ${requestInfo.customer} â€¢ ${requestInfo.category}
                </p>
            </div>
            <span class="badge" style="background: var(--primary-color); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                ${requestInfo.district}
            </span>
        </div>
    `;
    
    // Pre-fill quotation title
    document.getElementById('quotation-title').value = `${requestInfo.category} - ${requestInfo.title}`;
    
    // Set minimum dates to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('estimated-start-date').setAttribute('min', today);
    document.getElementById('estimated-completion-date').setAttribute('min', today);
    
    // Initialize cost calculation listeners
    initializeCostCalculation();
    
    // Initialize date validation
    initializeDateValidation();
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        document.getElementById('quotation-title').focus();
    }, 300);
}

function closeQuotationModal() {
    const modal = document.getElementById('quotation-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('quotation-form').reset();
    
    // Reset calculated values
    document.getElementById('subtotal-amount').textContent = 'LKR 0.00';
    document.getElementById('total-amount').textContent = 'LKR 0.00';
    document.getElementById('total-price').value = '0';
}

function initializeCostCalculation() {
    const laborCost = document.getElementById('labor-cost');
    const materialCost = document.getElementById('material-cost');
    const transportCost = document.getElementById('transport-cost');
    const otherCost = document.getElementById('other-cost');
    
    // Add event listeners for real-time calculation
    [laborCost, materialCost, transportCost, otherCost].forEach(input => {
        input.addEventListener('input', calculateTotalCost);
    });
}

function calculateTotalCost() {
    const labor = parseFloat(document.getElementById('labor-cost').value) || 0;
    const material = parseFloat(document.getElementById('material-cost').value) || 0;
    const transport = parseFloat(document.getElementById('transport-cost').value) || 0;
    const other = parseFloat(document.getElementById('other-cost').value) || 0;
    
    const subtotal = labor + material + transport + other;
    const total = subtotal; // Can add tax calculation here if needed
    
    // Update display
    document.getElementById('subtotal-amount').textContent = `LKR ${formatCurrency(subtotal)}`;
    document.getElementById('total-amount').textContent = `LKR ${formatCurrency(total)}`;
    document.getElementById('total-price').value = total.toFixed(2);
}

function formatCurrency(amount) {
    return amount.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function initializeDateValidation() {
    const startDate = document.getElementById('estimated-start-date');
    const endDate = document.getElementById('estimated-completion-date');
    const duration = document.getElementById('estimated-duration');
    
    // Update end date based on start date and duration
    startDate.addEventListener('change', function() {
        endDate.setAttribute('min', this.value);
        calculateDuration();
    });
    
    endDate.addEventListener('change', calculateDuration);
    duration.addEventListener('input', updateEndDate);
}

function calculateDuration() {
    const startDate = document.getElementById('estimated-start-date').value;
    const endDate = document.getElementById('estimated-completion-date').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        document.getElementById('estimated-duration').value = diffDays;
    }
}

function updateEndDate() {
    const startDate = document.getElementById('estimated-start-date').value;
    const duration = parseInt(document.getElementById('estimated-duration').value);
    
    if (startDate && duration > 0) {
        const start = new Date(startDate);
        start.setDate(start.getDate() + duration);
        document.getElementById('estimated-completion-date').value = start.toISOString().split('T')[0];
    }
}

function submitQuotation() {
    const form = document.getElementById('quotation-form');
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Check if agreement is checked
    if (!document.getElementById('agreement').checked) {
        showNotification('Please confirm the agreement checkbox', 'error');
        return;
    }
    
    // Collect form data
    const formData = new FormData(form);
    const quotationData = {
        quotation_id: editingQuotationId || `QUOT-${Date.now()}`, // Use existing ID or generate new
        request_id: formData.get('request_id'),
        title: formData.get('title'),
        description: formData.get('description'),
        labor_cost: parseFloat(formData.get('labor_cost')),
        material_cost: parseFloat(formData.get('material_cost')),
        transport_cost: parseFloat(formData.get('transport_cost')) || 0,
        other_cost: parseFloat(formData.get('other_cost')) || 0,
        total_price: parseFloat(formData.get('total_price')),
        estimated_start_date: formData.get('estimated_start_date'),
        estimated_completion_date: formData.get('estimated_completion_date'),
        estimated_duration: parseInt(formData.get('estimated_duration')),
        payment_terms: formData.get('payment_terms'),
        warranty_period: formData.get('warranty_period'),
        terms_conditions: formData.get('terms_conditions') || '',
        validity_period: parseInt(formData.get('validity_period')),
        status: 'pending',
        submitted_at: editingQuotationId ? quotations.find(q => q.quotation_id === editingQuotationId)?.submitted_at : new Date().toISOString(),
        updated_at: new Date().toISOString()
    };
    
    // Validate minimum total
    if (quotationData.total_price <= 0) {
        showNotification('Total amount must be greater than zero', 'error');
        return;
    }
    
    // Show loading state
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (editingQuotationId ? 'Updating...' : 'Submitting...');
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        
        // Save to localStorage (will be replaced with database later)
        if (editingQuotationId) {
            // Update existing quotation
            const index = quotations.findIndex(q => q.quotation_id === editingQuotationId);
            if (index !== -1) {
                quotations[index] = quotationData;
            }
            showNotification('Quotation updated successfully!', 'success');
        } else {
            // Add new quotation
            quotations.push(quotationData);
            showNotification('Quotation submitted successfully!', 'success');
        }
        
        // Save to localStorage
        localStorage.setItem('quotations', JSON.stringify(quotations));
        
        // Reload quotations in logs tab
        loadQuotationsToLogs();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Reset editing state
        editingQuotationId = null;
        
        closeQuotationModal();
    }, 1500);
}

// Load quotations to logs section
function loadQuotationsToLogs() {
    const pendingList = document.getElementById('pending-quotations-list');
    const acceptedList = document.getElementById('accepted-quotations-list');
    const pendingCount = document.getElementById('pending-quotations-count');
    const acceptedCount = document.getElementById('accepted-quotations-count');
    
    if (!pendingList || !acceptedList) return;
    
    // Filter quotations by status
    const pendingQuotations = quotations.filter(q => q.status === 'pending');
    const acceptedQuotations = quotations.filter(q => q.status === 'accepted');
    
    // Update counts
    pendingCount.textContent = pendingQuotations.length;
    acceptedCount.textContent = acceptedQuotations.length;
    
    // Display pending quotations
    if (pendingQuotations.length === 0) {
        pendingList.innerHTML = `
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                <p>No pending quotations. Submit a quotation to see it here.</p>
            </div>
        `;
    } else {
        pendingList.innerHTML = pendingQuotations.map(q => `
            <div class="log-item quotation-item" data-quotation-id="${q.quotation_id}">
                <div class="log-item-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="log-item-content">
                    <h4 class="log-item-title">${q.title}</h4>
                    <p class="log-item-meta">
                        Quotation #${q.quotation_id.split('-')[1]} â€¢ ${q.request_id} â€¢ LKR ${formatCurrency(q.total_price)}
                    </p>
                    <p class="log-item-meta" style="margin-top: 4px; font-size: 0.85em;">
                        <i class="fas fa-calendar"></i> ${new Date(q.estimated_start_date).toLocaleDateString()} - ${new Date(q.estimated_completion_date).toLocaleDateString()}
                        <span style="margin-left: 12px;"><i class="fas fa-clock"></i> ${q.estimated_duration} days</span>
                    </p>
                </div>
                <div class="log-item-actions">
                    <button class="log-action-btn view" onclick="viewQuotationDetails('${q.quotation_id}')" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="log-action-btn edit" onclick="editQuotation('${q.quotation_id}')" title="Edit Quotation">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="log-action-btn delete" onclick="deleteQuotation('${q.quotation_id}')" title="Delete Quotation">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    // Display accepted quotations
    if (acceptedQuotations.length === 0) {
        acceptedList.innerHTML = `
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                <p>No accepted quotations yet.</p>
            </div>
        `;
    } else {
        acceptedList.innerHTML = acceptedQuotations.map(q => `
            <div class="log-item quotation-item accepted" data-quotation-id="${q.quotation_id}">
                <div class="log-item-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="log-item-content">
                    <h4 class="log-item-title">${q.title}</h4>
                    <p class="log-item-meta">
                        Quotation #${q.quotation_id.split('-')[1]} â€¢ ${q.request_id} â€¢ LKR ${formatCurrency(q.total_price)}
                    </p>
                    <p class="log-item-meta" style="margin-top: 4px; font-size: 0.85em; color: var(--success-color);">
                        <i class="fas fa-check"></i> Accepted by customer
                    </p>
                </div>
                <div class="log-item-actions">
                    <button class="log-action-btn view" onclick="viewQuotationDetails('${q.quotation_id}')" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
}

// Edit quotation function
function editQuotation(quotationId) {
    const quotation = quotations.find(q => q.quotation_id === quotationId);
    
    if (!quotation) {
        showNotification('Quotation not found', 'error');
        return;
    }
    
    if (quotation.status === 'accepted') {
        showNotification('Cannot edit accepted quotations', 'error');
        return;
    }
    
    // Set editing mode
    editingQuotationId = quotationId;
    
    // Open modal with pre-filled data
    const modal = document.getElementById('quotation-modal');
    const requestDetails = document.getElementById('quotation-request-details');
    
    // Update modal title
    document.querySelector('#quotation-modal .modal-title').innerHTML = `
        <i class="fas fa-edit"></i>
        Edit Quotation
    `;
    
    // Populate request details
    requestDetails.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h4 style="margin: 0 0 var(--spacing-xs) 0; color: var(--text-primary);">${quotation.title}</h4>
                <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                    Quotation #${quotation.quotation_id.split('-')[1]} â€¢ ${quotation.request_id}
                </p>
            </div>
            <span class="badge" style="background: var(--warning-color); color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                Editing
            </span>
        </div>
    `;
    
    // Pre-fill form fields
    document.getElementById('request-id').value = quotation.request_id;
    document.getElementById('quotation-title').value = quotation.title;
    document.getElementById('service-description').value = quotation.description;
    document.getElementById('labor-cost').value = quotation.labor_cost;
    document.getElementById('material-cost').value = quotation.material_cost;
    document.getElementById('transport-cost').value = quotation.transport_cost;
    document.getElementById('other-cost').value = quotation.other_cost;
    document.getElementById('estimated-start-date').value = quotation.estimated_start_date;
    document.getElementById('estimated-completion-date').value = quotation.estimated_completion_date;
    document.getElementById('estimated-duration').value = quotation.estimated_duration;
    document.getElementById('payment-terms').value = quotation.payment_terms;
    document.getElementById('warranty-period').value = quotation.warranty_period;
    document.getElementById('terms-conditions').value = quotation.terms_conditions;
    document.getElementById('validity-period').value = quotation.validity_period;
    document.getElementById('agreement').checked = true;
    
    // Trigger cost calculation
    calculateTotalCost();
    
    // Initialize listeners
    initializeCostCalculation();
    initializeDateValidation();
    
    // Open modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Delete quotation function
function deleteQuotation(quotationId) {
    const quotation = quotations.find(q => q.quotation_id === quotationId);
    
    if (!quotation) {
        showNotification('Quotation not found', 'error');
        return;
    }
    
    if (quotation.status === 'accepted') {
        showNotification('Cannot delete accepted quotations', 'error');
        return;
    }
    
    if (confirm(`Are you sure you want to delete this quotation?\n\n"${quotation.title}"\n\nThis action cannot be undone.`)) {
        // Remove from array
        quotations = quotations.filter(q => q.quotation_id !== quotationId);
        
        // Save to localStorage
        localStorage.setItem('quotations', JSON.stringify(quotations));
        
        // Reload logs
        loadQuotationsToLogs();
        
        showNotification('Quotation deleted successfully', 'success');
    }
}

// View quotation details
function viewQuotationDetails(quotationId) {
    const quotation = quotations.find(q => q.quotation_id === quotationId);
    
    if (!quotation) {
        showNotification('Quotation not found', 'error');
        return;
    }
    
    // You can create a view modal or show details in the existing modal
    // For now, log to console and show an alert
    
    const details = `
Quotation Details:
â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Title: ${quotation.title}
Request ID: ${quotation.request_id}
Status: ${quotation.status.toUpperCase()}

Cost Breakdown:
â€¢ Labor: LKR ${formatCurrency(quotation.labor_cost)}
â€¢ Material: LKR ${formatCurrency(quotation.material_cost)}
â€¢ Transport: LKR ${formatCurrency(quotation.transport_cost)}
â€¢ Other: LKR ${formatCurrency(quotation.other_cost)}
â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Total: LKR ${formatCurrency(quotation.total_price)}

Timeline:
â€¢ Start Date: ${new Date(quotation.estimated_start_date).toLocaleDateString()}
â€¢ Completion: ${new Date(quotation.estimated_completion_date).toLocaleDateString()}
â€¢ Duration: ${quotation.estimated_duration} days

Terms:
â€¢ Payment: ${quotation.payment_terms}
â€¢ Warranty: ${quotation.warranty_period}
â€¢ Valid for: ${quotation.validity_period} days

Submitted: ${new Date(quotation.submitted_at).toLocaleString()}
    `;
    
    alert(details);
}

// Demo function to simulate customer accepting a quotation
function simulateAcceptQuotation(quotationId) {
    const quotation = quotations.find(q => q.quotation_id === quotationId);
    
    if (quotation) {
        quotation.status = 'accepted';
        quotation.accepted_at = new Date().toISOString();
        localStorage.setItem('quotations', JSON.stringify(quotations));
        loadQuotationsToLogs();
        showNotification('Quotation accepted (simulated)', 'success');
    }
}

// Clear all quotations from localStorage
function clearAllQuotations() {
    if (confirm('âš ï¸ This will delete ALL quotations (including mock data).\n\nAre you sure you want to continue?\n\nYou can reload the page to restore mock data.')) {
        localStorage.removeItem('quotations');
        quotations = [];
        loadQuotationsToLogs();
        showNotification('All quotations cleared! Refresh page to reload mock data.', 'success');
        
    }
}

// Reload mock data (can be called from console)
function reloadMockData() {
    localStorage.removeItem('quotations');
    quotations = [];
    loadMockQuotations();
    loadQuotationsToLogs();
    showNotification('Mock data reloaded successfully!', 'success');
    
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        modal.classList.remove('active');
    });
    document.body.style.overflow = 'auto';
}

// Request Details Modal Functions
function viewRequestDetails(requestId) {
    const modal = document.getElementById('request-details-modal');
    const content = document.getElementById('request-details-content');
    
    // Get request data (in a real app, this would come from an API)
    const requestData = getRequestData(requestId);
    
    // Populate modal content
    content.innerHTML = `
        <div class="request-details-modal-content">
            <div class="detail-header">
                <div class="detail-header-left">
                    <h3>${requestData.title}</h3>
                    <p class="request-id-text"><span class="category-label">${requestData.category}</span></p>
                </div>
                <span class="priority-badge ${requestData.priority.toLowerCase()}">${requestData.priority} Priority</span>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-user"></i> Customer Information</h4>
                <div class="customer-info-grid">
                    <div class="info-item">
                        <label>Name:</label>
                        <span>${requestData.customer.name}</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-map-marker-alt"></i> Location & Schedule</h4>
                <div class="customer-info-grid">
                    <div class="info-item">
                        <label>Full Address:</label>
                        <span>${requestData.fullAddress}</span>
                    </div>
                    <div class="info-item">
                        <label>Date Needed:</label>
                        <span>${requestData.dateNeeded}</span>
                    </div>
                    <div class="info-item">
                        <label>Posted:</label>
                        <span>${requestData.posted}</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-file-alt"></i> Description</h4>
                <p class="description-text">${requestData.description}</p>
            </div>
            
            ${requestData.attachments && requestData.attachments.length > 0 ? `
                <div class="detail-section">
                    <h4><i class="fas fa-paperclip"></i> Attachments (${requestData.attachments.length})</h4>
                    <div class="attachments-grid">
                        ${requestData.attachments.map(attachment => `
                            <div class="attachment-card">
                                <i class="fas ${getAttachmentIcon(attachment)}"></i>
                                <span>${attachment}</span>
                                <button class="download-btn" onclick="downloadAttachment('${attachment}')">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            ${requestData.additionalInfo ? `
                <div class="detail-section">
                    <h4><i class="fas fa-info-circle"></i> Additional Information</h4>
                    <p class="description-text">${requestData.additionalInfo}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    // Set up the quotation button
    const quoteBtn = document.getElementById('submit-quote-from-details');
    quoteBtn.onclick = function() {
        closeRequestDetailsModal();
        setTimeout(() => openQuotationModal(requestId), 300);
    };
    
    // Show modal
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeRequestDetailsModal() {
    const modal = document.getElementById('request-details-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function getRequestData(requestId) {
    // Mock data - in a real application, this would fetch from an API
    const requestsData = {
        'REQ-2025-001': {
            title: 'Air Conditioner Repair',
            category: 'HVAC',
            priority: 'High',
            customer: {
                name: 'John Doe'
            },
            location: 'Colombo 07',
            fullAddress: '123, Flower Road, Colombo 07, Western Province',
            dateNeeded: 'September 15, 2025',
            posted: '2 hours ago',
            description: 'AC unit not cooling properly. Making strange noises and consuming more electricity than usual. Urgent repair needed as weather is getting hotter.',
            attachments: ['ac-problem.jpg', 'warranty.pdf'],
            additionalInfo: 'Customer is available for inspection between 9 AM - 5 PM on weekdays.'
        },
        'REQ-2025-002': {
            title: 'Electrical Wiring - Office',
            category: 'Electrical',
            priority: 'Low',
            customer: {
                name: 'ABC Pvt Ltd'
            },
            location: 'Nugegoda',
            fullAddress: '456, Stanley Thilakarathne Mawatha, Nugegoda, Western Province',
            dateNeeded: 'September 20, 2025',
            posted: '5 hours ago',
            description: 'Complete rewiring of office building for safety compliance. Need certified electrician with commercial experience. Project timeline flexible.',
            attachments: [],
            additionalInfo: 'Building inspection report available upon request. Work must be completed during weekends to avoid business disruption.'
        },
        'REQ-2025-003': {
            title: 'Plumbing Emergency',
            category: 'Plumbing',
            priority: 'High',
            customer: {
                name: 'Sarah Miller'
            },
            location: 'Kandy',
            fullAddress: '78, Peradeniya Road, Kandy, Central Province',
            dateNeeded: 'ASAP',
            posted: '30 minutes ago',
            description: 'Burst pipe in main bathroom causing water damage. Need emergency plumber immediately. Water supply currently shut off.',
            attachments: ['water-damage.jpg', 'pipe-burst.jpg', 'damage-video.mp4'],
            additionalInfo: 'Emergency situation. Customer is at home and available immediately. Insurance claim will be filed.'
        },
        'REQ-2025-004': {
            title: 'AC Repair - Colombo',
            category: 'HVAC',
            priority: 'High',
            customer: {
                name: 'Robert Johnson'
            },
            location: 'Colombo 05',
            fullAddress: '234, Havelock Road, Colombo 05, Western Province',
            dateNeeded: 'September 12, 2025',
            posted: '1 day ago',
            description: 'Emergency AC repair service needed. Unit completely stopped working during heatwave.',
            attachments: ['ac-unit.jpg'],
            additionalInfo: 'This is a direct request. Customer specifically requested your company based on previous work.'
        },
        'REQ-2025-005': {
            title: 'Plumbing Fix - Kandy',
            category: 'Plumbing',
            priority: 'Low',
            customer: {
                name: 'Jane Smith'
            },
            location: 'Kandy',
            fullAddress: '89, Dalada Veediya, Kandy, Central Province',
            dateNeeded: 'September 18, 2025',
            posted: '2 days ago',
            description: 'Bathroom renovation plumbing work. Need to install new fixtures and update piping.',
            attachments: ['bathroom-layout.pdf'],
            additionalInfo: 'Customer is planning a complete bathroom renovation and needs plumbing expertise.'
        },
        'REQ-2025-006': {
            title: 'Electrical Installation',
            category: 'Electrical',
            priority: 'Low',
            customer: {
                name: 'Mike Brown'
            },
            location: 'Galle',
            fullAddress: '567, Galle Road, Galle, Southern Province',
            dateNeeded: 'September 25, 2025',
            posted: '3 days ago',
            description: 'New construction electrical installation work. Complete wiring for a new commercial building.',
            attachments: ['building-plan.pdf', 'electrical-diagram.pdf'],
            additionalInfo: 'Contract already accepted. This is for viewing contract details.'
        }
    };
    
    return requestsData[requestId] || {
        title: 'Request Not Found',
        category: 'Other',
        priority: 'Low',
        customer: { name: 'Unknown' },
        location: 'Unknown',
        fullAddress: 'Address not available',
        dateNeeded: 'Unknown',
        posted: 'Unknown',
        description: 'No details available for this request.',
        attachments: []
    };
}

function getAttachmentIcon(filename) {
    const ext = filename.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'xls': 'fa-file-excel',
        'xlsx': 'fa-file-excel',
        'jpg': 'fa-image',
        'jpeg': 'fa-image',
        'png': 'fa-image',
        'gif': 'fa-image',
        'mp4': 'fa-video',
        'avi': 'fa-video',
        'mov': 'fa-video',
        'zip': 'fa-file-archive',
        'rar': 'fa-file-archive'
    };
    return iconMap[ext] || 'fa-file';
}

function downloadAttachment(filename) {
    showNotification(`Downloading ${filename}...`, 'info');
    // In a real application, this would trigger an actual download
    setTimeout(() => {
        showNotification(`${filename} downloaded successfully`, 'success');
    }, 1000);
}

function submitQuotation() {
    const form = document.getElementById('quotation-form');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const quotationData = {
        price: document.getElementById('quote-price').value,
        currency: document.getElementById('quote-currency').value,
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        materialsSupply: document.querySelector('input[name="materials"]:checked').value,
        additionalNotes: document.getElementById('additional-notes').value
    };
    
    showNotification('Quotation submitted successfully!', 'success');
    closeQuotationModal();
}
function acceptDirectRequest(requestId) {
    if (confirm(`Are you sure you want to accept request ${requestId}?`)) {
        showNotification('Processing request...', 'info');
        
        setTimeout(() => {
            showNotification('Request accepted! Redirecting to contract creation...', 'success');
            setTimeout(() => {
                window.location.href = `contract-creation.html?request=${requestId}`;
            }, 1500);
        }, 1000);
    }
}

function rejectDirectRequest(requestId) {
    const reason = prompt('Please provide a reason for rejecting this request:');
    
    if (reason && reason.trim() !== '') {
        showNotification('Processing rejection...', 'info');
        
        setTimeout(() => {
            showNotification('Request rejected successfully', 'success');
            
            const row = document.querySelector(`[onclick*="${requestId}"]`).closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            statusBadge.className = 'status-badge rejected';
            statusBadge.innerHTML = '<i class="fas fa-times"></i> Rejected';
            
            const actionsCell = row.querySelector('.table-actions');
            actionsCell.innerHTML = `
                <a href="#" class="table-action-btn view">
                    <i class="fas fa-eye"></i>
                    View
                </a>
            `;
        }, 1000);
    }
}
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function getNotificationIcon(type) {
    switch (type) {
        case 'success': return 'check-circle';
        case 'error': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        default: return 'info-circle';
    }
}

function formatCurrency(amount, currency = 'LKR') {
    return new Intl.NumberFormat('en-LK', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('en-LK', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

function validateDateRange() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
        alert('End date must be after start date');
        return false;
    }
    return true;
}

function initializeRealTimeUpdates() {
    setInterval(() => {
        updateRequestCounters();
        
        if (Math.random() < 0.1) {
            showNotification('New repair request received!', 'info');
        }
    }, 30000);
}

function updateRequestCounters() {
    const publicCount = document.querySelector('[data-tab="public-requests"] .tab-count');
    const directCount = document.querySelector('[data-tab="direct-requests"] .tab-count');
    
    if (Math.random() < 0.3) {
        const currentPublic = parseInt(publicCount.textContent);
        publicCount.textContent = currentPublic + 1;
        
        const publicStat = document.querySelector('.stat-card .stat-number');
        if (publicStat) {
            publicStat.textContent = currentPublic + 1;
        }
    }
}

document.addEventListener('DOMContentLoaded', initializeRealTimeUpdates);
function handleUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    if (activeTab) {
        switchTab(activeTab);
    }
}

document.addEventListener('DOMContentLoaded', handleUrlParameters);
function exportRequests(format = 'csv') {
    const data = collectRequestsData();
    
    switch (format) {
        case 'csv':
            exportAsCSV(data);
            break;
        case 'pdf':
            exportAsPDF(data);
            break;
        case 'excel':
            exportAsExcel(data);
            break;
    }
}

function collectRequestsData() {
    const requests = [];
    const cards = document.querySelectorAll('.request-card:not([style*="display: none"])');
    
    cards.forEach(card => {
        requests.push({
            title: card.querySelector('.request-title').textContent,
            customer: card.querySelector('.customer-details h4').textContent,
            priority: card.querySelector('.priority-badge').textContent,
            location: card.querySelector('.detail-value').textContent,
            date: card.querySelectorAll('.detail-value')[1].textContent
        });
    });
    
    return requests;
}

function exportAsCSV(data) {
    const csv = [
        ['Title', 'Customer', 'Priority', 'Location', 'Date'],
        ...data.map(item => [item.title, item.customer, item.priority, item.location, item.date])
    ].map(row => row.join(',')).join('\\n');
    
    downloadFile(csv, 'repair-requests.csv', 'text/csv');
}

function downloadFile(content, filename, mimeType) {
    const blob = new Blob([content], { type: mimeType });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    link.click();
    URL.revokeObjectURL(url);
}

document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-input').focus();
    }
    
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        const firstRequestCard = document.querySelector('.request-card');
        if (firstRequestCard) {
            const requestId = firstRequestCard.querySelector('.request-id').textContent.match(/#(.+)/)[1];
            openQuotationModal(requestId);
        }
    }
    
    if (e.key >= '1' && e.key <= '3' && e.altKey) {
        e.preventDefault();
        const tabIndex = parseInt(e.key) - 1;
        const tabs = ['public-requests', 'direct-requests', 'logs'];
        if (tabs[tabIndex]) {
            switchTab(tabs[tabIndex]);
        }
    }
});
function printCurrentView() {
    window.print();
}

const printStyles = `
    @media print {
        .sidebar, .topbar, .tab-nav, .requests-controls, .modal-overlay {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .request-card {
            break-inside: avoid;
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = printStyles;
document.head.appendChild(styleSheet);
