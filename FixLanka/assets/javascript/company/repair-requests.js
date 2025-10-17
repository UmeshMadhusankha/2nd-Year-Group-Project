// ====================================
// FixLanka Repair Requests JavaScript
// ====================================

// DOM Elements
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
const viewButtons = document.querySelectorAll('.view-btn');
const quotationModal = document.getElementById('quotation-modal');
const quotationForm = document.getElementById('quotation-form');

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeFilters();
    initializeSearch();
    initializeViewControls();
    initializeModals();
    setActiveNavigation();
});

// Set active navigation item for this page
function setActiveNavigation() {
    // Remove active class from all navigation items
    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Find and activate the repair requests navigation item
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        const linkText = link.querySelector('span')?.textContent;
        const href = link.getAttribute('href');
        
        if (linkText === 'Repair Requests' || href === 'repair-requests.html') {
            link.closest('.nav-item').classList.add('active');
        }
    });
    
    // Store active page in localStorage for persistence
    localStorage.setItem('activePage', 'Repair Requests');
}

// ===============================================
// TAB FUNCTIONALITY
// ===============================================
function initializeTabs() {
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            switchTab(tabName);
        });
    });
}

function switchTab(tabName) {
    // Remove active class from all tabs and contents
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));
    
    // Add active class to selected tab and content
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    document.getElementById(tabName).classList.add('active');
    
    // Update URL without page reload (optional)
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// ===============================================
// FILTER FUNCTIONALITY
// ===============================================
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
        
        // Apply service filter
        if (serviceFilter && !cardMatchesService(card, serviceFilter)) {
            showCard = false;
        }
        
        // Apply priority filter
        if (priorityFilter && !cardMatchesPriority(card, priorityFilter)) {
            showCard = false;
        }
        
        // Apply location filter
        if (locationFilter && !cardMatchesLocation(card, locationFilter)) {
            showCard = false;
        }
        
        // Show/hide card with animation
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

// ===============================================
// SEARCH FUNCTIONALITY
// ===============================================
function initializeSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            performSearch(searchTerm);
        });
    });
}

function performSearch(searchTerm) {
    const requestCards = document.querySelectorAll('.request-card');
    const tableRows = document.querySelectorAll('.requests-table tbody tr');
    
    // Search in cards (Public Requests)
    requestCards.forEach(card => {
        const title = card.querySelector('.request-title').textContent.toLowerCase();
        const customer = card.querySelector('.customer-details h4').textContent.toLowerCase();
        const description = card.querySelector('.request-description p').textContent.toLowerCase();
        
        const matches = title.includes(searchTerm) || 
                       customer.includes(searchTerm) || 
                       description.includes(searchTerm);
        
        if (matches || searchTerm === '') {
            card.style.display = 'block';
            setTimeout(() => card.style.opacity = '1', 10);
        } else {
            card.style.opacity = '0';
            setTimeout(() => card.style.display = 'none', 300);
        }
    });
    
    // Search in table rows (Direct Requests)
    tableRows.forEach(row => {
        const title = row.querySelector('h5').textContent.toLowerCase();
        const customer = row.querySelector('.table-customer-info h5').textContent.toLowerCase();
        
        const matches = title.includes(searchTerm) || customer.includes(searchTerm);
        
        row.style.display = matches || searchTerm === '' ? 'table-row' : 'none';
    });
}

// ===============================================
// VIEW CONTROLS
// ===============================================
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

// ===============================================
// MODAL FUNCTIONALITY
// ===============================================
function initializeModals() {
    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            closeAllModals();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

function openQuotationModal(requestId) {
    const modal = document.getElementById('quotation-modal');
    const requestDetails = document.getElementById('quotation-request-details');
    
    // Populate request details (you can fetch this from the server or parse from DOM)
    requestDetails.innerHTML = `
        <h4>Request #${requestId}</h4>
        <p>Please review the request details above and provide your quotation.</p>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Focus on first input
    setTimeout(() => {
        document.getElementById('quote-price').focus();
    }, 300);
}

function closeQuotationModal() {
    const modal = document.getElementById('quotation-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('quotation-form').reset();
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        modal.classList.remove('active');
    });
    document.body.style.overflow = 'auto';
}

// ===============================================
// QUOTATION SUBMISSION
// ===============================================
function submitQuotation() {
    const form = document.getElementById('quotation-form');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Collect form data
    const quotationData = {
        price: document.getElementById('quote-price').value,
        currency: document.getElementById('quote-currency').value,
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        materialsSupply: document.querySelector('input[name="materials"]:checked').value,
        additionalNotes: document.getElementById('additional-notes').value
    };
    
    // Show loading state
    const submitBtn = document.querySelector('.modal-footer .action-btn.primary');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
    
    // Simulate API call (replace with actual implementation)
    setTimeout(() => {
        // Success
        showNotification('Quotation submitted successfully!', 'success');
        closeQuotationModal();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Optionally refresh the page or update the UI
        // location.reload();
    }, 2000);
}

// ===============================================
// DIRECT REQUEST ACTIONS
// ===============================================
function acceptDirectRequest(requestId) {
    if (confirm(`Are you sure you want to accept request ${requestId}?`)) {
        // Show loading state
        showNotification('Processing request...', 'info');
        
        // Simulate API call
        setTimeout(() => {
            showNotification('Request accepted! Redirecting to contract creation...', 'success');
            
            // Redirect to contract creation page
            setTimeout(() => {
                window.location.href = `contract-creation.html?request=${requestId}`;
            }, 1500);
        }, 1000);
    }
}

function rejectDirectRequest(requestId) {
    const reason = prompt('Please provide a reason for rejecting this request:');
    
    if (reason && reason.trim() !== '') {
        // Show loading state
        showNotification('Processing rejection...', 'info');
        
        // Simulate API call
        setTimeout(() => {
            showNotification('Request rejected successfully', 'success');
            
            // Update UI to reflect rejection
            const row = document.querySelector(`[onclick*="${requestId}"]`).closest('tr');
            const statusBadge = row.querySelector('.status-badge');
            statusBadge.className = 'status-badge rejected';
            statusBadge.innerHTML = '<i class="fas fa-times"></i> Rejected';
            
            // Update actions
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

// ===============================================
// NOTIFICATION SYSTEM
// ===============================================
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove after 5 seconds
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

// ===============================================
// UTILITY FUNCTIONS
// ===============================================
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

// ===============================================
// REAL-TIME UPDATES (WebSocket simulation)
// ===============================================
function initializeRealTimeUpdates() {
    // Simulate real-time updates every 30 seconds
    setInterval(() => {
        // Update counters
        updateRequestCounters();
        
        // Show new request notification (randomly)
        if (Math.random() < 0.1) { // 10% chance
            showNotification('New repair request received!', 'info');
        }
    }, 30000);
}

function updateRequestCounters() {
    // This would typically fetch updated counts from the server
    const publicCount = document.querySelector('[data-tab="public-requests"] .tab-count');
    const directCount = document.querySelector('[data-tab="direct-requests"] .tab-count');
    
    // Simulate count updates (replace with actual API calls)
    if (Math.random() < 0.3) {
        const currentPublic = parseInt(publicCount.textContent);
        publicCount.textContent = currentPublic + 1;
        
        // Update header stats
        const publicStat = document.querySelector('.stat-card .stat-number');
        if (publicStat) {
            publicStat.textContent = currentPublic + 1;
        }
    }
}

// Initialize real-time updates
document.addEventListener('DOMContentLoaded', initializeRealTimeUpdates);

// ===============================================
// URL PARAMETER HANDLING
// ===============================================
function handleUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');
    
    if (activeTab) {
        switchTab(activeTab);
    }
}

// Handle URL parameters on load
document.addEventListener('DOMContentLoaded', handleUrlParameters);

// ===============================================
// EXPORT FUNCTIONALITY
// ===============================================
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
    // Collect data from current view
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

// ===============================================
// KEYBOARD SHORTCUTS
// ===============================================
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.querySelector('.search-input').focus();
    }
    
    // Ctrl/Cmd + N for new quotation
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        const firstRequestCard = document.querySelector('.request-card');
        if (firstRequestCard) {
            const requestId = firstRequestCard.querySelector('.request-id').textContent.match(/#(.+)/)[1];
            openQuotationModal(requestId);
        }
    }
    
    // Tab navigation with numbers
    if (e.key >= '1' && e.key <= '3' && e.altKey) {
        e.preventDefault();
        const tabIndex = parseInt(e.key) - 1;
        const tabs = ['public-requests', 'direct-requests', 'logs'];
        if (tabs[tabIndex]) {
            switchTab(tabs[tabIndex]);
        }
    }
});

// ===============================================
// PRINT FUNCTIONALITY
// ===============================================
function printCurrentView() {
    window.print();
}

// Add print styles dynamically
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
