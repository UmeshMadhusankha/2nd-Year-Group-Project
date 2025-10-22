const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');
const viewButtons = document.querySelectorAll('.view-btn');
const quotationModal = document.getElementById('quotation-modal');
const quotationForm = document.getElementById('quotation-form');

document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeFilters();
    initializeSearch();
    initializeViewControls();
    initializeModals();
    setActiveNavigation();
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
    
    requestDetails.innerHTML = `
        <h4>Request #${requestId}</h4>
        <p>Please review the request details above and provide your quotation.</p>
    `;
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    setTimeout(() => {
        document.getElementById('quote-price').focus();
    }, 300);
}

function closeQuotationModal() {
    const modal = document.getElementById('quotation-modal');
    modal.classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('quotation-form').reset();
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
                    <p class="request-id-text">${requestId}</p>
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
                    <div class="info-item">
                        <label>Type:</label>
                        <span>${requestData.customer.type}</span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span>${requestData.customer.email || 'Not provided'}</span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span>${requestData.customer.phone || 'Not provided'}</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-section">
                <h4><i class="fas fa-map-marker-alt"></i> Location & Schedule</h4>
                <div class="customer-info-grid">
                    <div class="info-item">
                        <label>Location:</label>
                        <span>${requestData.location}</span>
                    </div>
                    <div class="info-item">
                        <label>Date Needed:</label>
                        <span>${requestData.dateNeeded}</span>
                    </div>
                    <div class="info-item">
                        <label>Posted:</label>
                        <span>${requestData.posted}</span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-text">${requestData.status || 'Open'}</span>
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
            priority: 'High',
            customer: {
                name: 'John Doe',
                type: 'Residential Customer',
                email: 'john.doe@email.com',
                phone: '+94 77 123 4567'
            },
            location: 'Colombo 07',
            dateNeeded: 'September 15, 2025',
            posted: '2 hours ago',
            status: 'Open',
            description: 'AC unit not cooling properly. Making strange noises and consuming more electricity than usual. Urgent repair needed as weather is getting hotter.',
            attachments: ['ac-problem.jpg', 'warranty.pdf'],
            additionalInfo: 'Customer is available for inspection between 9 AM - 5 PM on weekdays.'
        },
        'REQ-2025-002': {
            title: 'Electrical Wiring - Office',
            priority: 'Medium',
            customer: {
                name: 'ABC Pvt Ltd',
                type: 'Commercial Customer',
                email: 'contact@abcpvtltd.com',
                phone: '+94 11 234 5678'
            },
            location: 'Nugegoda',
            dateNeeded: 'September 20, 2025',
            posted: '5 hours ago',
            status: 'Open',
            description: 'Complete rewiring of office building for safety compliance. Need certified electrician with commercial experience. Project timeline flexible.',
            attachments: [],
            additionalInfo: 'Building inspection report available upon request. Work must be completed during weekends to avoid business disruption.'
        },
        'REQ-2025-003': {
            title: 'Plumbing Emergency',
            priority: 'High',
            customer: {
                name: 'Sarah Miller',
                type: 'Residential Customer',
                email: 'sarah.miller@email.com',
                phone: '+94 76 987 6543'
            },
            location: 'Kandy',
            dateNeeded: 'ASAP',
            posted: '30 minutes ago',
            status: 'Urgent',
            description: 'Burst pipe in main bathroom causing water damage. Need emergency plumber immediately. Water supply currently shut off.',
            attachments: ['water-damage.jpg', 'pipe-burst.jpg', 'damage-video.mp4'],
            additionalInfo: 'Emergency situation. Customer is at home and available immediately. Insurance claim will be filed.'
        },
        'REQ-2025-004': {
            title: 'AC Repair - Colombo',
            priority: 'High',
            customer: {
                name: 'Robert Johnson',
                type: 'Residential Customer',
                email: 'robert@email.com',
                phone: '+94 77 456 7890'
            },
            location: 'Colombo 05',
            dateNeeded: 'September 12, 2025',
            posted: '1 day ago',
            status: 'Pending',
            description: 'Emergency AC repair service needed. Unit completely stopped working during heatwave.',
            attachments: ['ac-unit.jpg'],
            additionalInfo: 'This is a direct request. Customer specifically requested your company based on previous work.'
        },
        'REQ-2025-005': {
            title: 'Plumbing Fix - Kandy',
            priority: 'Medium',
            customer: {
                name: 'Jane Smith',
                type: 'Residential Customer',
                email: 'jane.smith@email.com',
                phone: '+94 81 234 5678'
            },
            location: 'Kandy',
            dateNeeded: 'September 18, 2025',
            posted: '2 days ago',
            status: 'Pending',
            description: 'Bathroom renovation plumbing work. Need to install new fixtures and update piping.',
            attachments: ['bathroom-layout.pdf'],
            additionalInfo: 'Customer is planning a complete bathroom renovation and needs plumbing expertise.'
        },
        'REQ-2025-006': {
            title: 'Electrical Installation',
            priority: 'Low',
            customer: {
                name: 'Mike Brown',
                type: 'Commercial Customer',
                email: 'mike.brown@email.com',
                phone: '+94 11 345 6789'
            },
            location: 'Galle',
            dateNeeded: 'September 25, 2025',
            posted: '3 days ago',
            status: 'Accepted',
            description: 'New construction electrical installation work. Complete wiring for a new commercial building.',
            attachments: ['building-plan.pdf', 'electrical-diagram.pdf'],
            additionalInfo: 'Contract already accepted. This is for viewing contract details.'
        }
    };
    
    return requestsData[requestId] || {
        title: 'Request Not Found',
        priority: 'Low',
        customer: { name: 'Unknown', type: 'Unknown' },
        location: 'Unknown',
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
