
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
                    Quotation #${quotation.quotation_id.split('-')[1]} &bull; ${quotation.request_id}
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
--------------------------------
Title: ${quotation.title}
Request ID: ${quotation.request_id}
Status: ${quotation.status.toUpperCase()}

Cost Breakdown:
&bull; Labor: LKR ${formatCurrency(quotation.labor_cost)}
&bull; Material: LKR ${formatCurrency(quotation.material_cost)}
&bull; Transport: LKR ${formatCurrency(quotation.transport_cost)}
&bull; Other: LKR ${formatCurrency(quotation.other_cost)}
--------------------------------
Total: LKR ${formatCurrency(quotation.total_price)}

Timeline:
&bull; Start Date: ${new Date(quotation.estimated_start_date).toLocaleDateString()}
&bull; Completion: ${new Date(quotation.estimated_completion_date).toLocaleDateString()}
&bull; Duration: ${quotation.estimated_duration} days

Terms:
&bull; Payment: ${quotation.payment_terms}
&bull; Warranty: ${quotation.warranty_period}
&bull; Valid for: ${quotation.validity_period} days

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
    if (confirm('This will delete ALL quotations (including mock data).\n\nAre you sure you want to continue?\n\nYou can reload the page to restore mock data.')) {
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
    quoteBtn.onclick = function () {
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

document.addEventListener('keydown', function (e) {
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
