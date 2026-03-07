// ================================================
// EARNINGS PAGE JAVASCRIPT
// ================================================

// Mock earnings data
const earningsData = {
    customer: [
        {
            id: 1,
            jobTitle: 'Kitchen Sink Repair',
            category: 'Plumbing',
            customerName: 'Sarah Fernando',
            location: 'Colombo 07',
            contact: '+94 77 123 4567',
            date: '2025-09-01',
            completedTime: '3:45 PM',
            duration: '2.5 hours',
            serviceFee: 2800,
            platformFee: 280,
            materialsCost: 500,
            totalEarned: 3020,
            paymentMethod: 'Cash',
            transactionId: 'TXN-2025-09-001',
            status: 'paid',
            rating: 5.0,
            description: 'Repaired leaking kitchen sink faucet, replaced worn-out washers, and installed new O-rings. Also cleaned the drain and checked all connections for potential issues.',
            timeline: [
                { event: 'Job Requested', date: '2025-09-01', time: '9:00 AM' },
                { event: 'Job Accepted', date: '2025-09-01', time: '9:15 AM' },
                { event: 'Work Started', date: '2025-09-01', time: '11:00 AM' },
                { event: 'Work Completed', date: '2025-09-01', time: '1:30 PM' },
                { event: 'Payment Received', date: '2025-09-01', time: '3:45 PM' }
            ]
        },
        {
            id: 2,
            jobTitle: 'Ceiling Fan Installation',
            category: 'Electrical',
            customerName: 'Kandy Hardware Store',
            location: 'Kandy',
            contact: '+94 81 234 5678',
            date: '2025-08-30',
            completedTime: '5:20 PM',
            duration: '3 hours',
            serviceFee: 4200,
            platformFee: 420,
            materialsCost: 1200,
            totalEarned: 4980,
            paymentMethod: 'Bank Transfer',
            transactionId: 'TXN-2025-08-030',
            status: 'paid',
            rating: 4.5,
            description: 'Installed new ceiling fan in the main showroom. Included wiring, mounting bracket installation, and testing. Also provided guidance on maintenance.',
            timeline: [
                { event: 'Job Requested', date: '2025-08-29', time: '2:00 PM' },
                { event: 'Job Accepted', date: '2025-08-29', time: '2:30 PM' },
                { event: 'Work Started', date: '2025-08-30', time: '2:00 PM' },
                { event: 'Work Completed', date: '2025-08-30', time: '5:00 PM' },
                { event: 'Payment Received', date: '2025-08-30', time: '5:20 PM' }
            ]
        },
        {
            id: 3,
            jobTitle: 'Air Conditioning Repair',
            category: 'HVAC',
            customerName: 'Priya Wickramasinghe',
            location: 'Nugegoda',
            contact: '+94 77 987 6543',
            date: '2025-08-29',
            completedTime: 'Pending',
            duration: '2 hours',
            serviceFee: 3500,
            platformFee: 350,
            materialsCost: 800,
            totalEarned: 3950,
            paymentMethod: 'Pending',
            transactionId: 'TXN-2025-08-029',
            status: 'pending',
            rating: 0,
            description: 'Diagnosed and repaired AC unit. Replaced faulty compressor relay and recharged refrigerant. Tested all functions before completion.',
            timeline: [
                { event: 'Job Requested', date: '2025-08-28', time: '10:00 AM' },
                { event: 'Job Accepted', date: '2025-08-28', time: '10:30 AM' },
                { event: 'Work Started', date: '2025-08-29', time: '9:00 AM' },
                { event: 'Work Completed', date: '2025-08-29', time: '11:00 AM' },
                { event: 'Awaiting Payment', date: '2025-08-29', time: '11:15 AM' }
            ]
        },
        {
            id: 4,
            jobTitle: 'Washing Machine Repair',
            category: 'Appliance',
            customerName: 'Nimal Perera',
            location: 'Maharagama',
            contact: '+94 71 456 7890',
            date: '2025-08-28',
            completedTime: '4:30 PM',
            duration: '1.5 hours',
            serviceFee: 2500,
            platformFee: 250,
            materialsCost: 600,
            totalEarned: 2850,
            paymentMethod: 'Cash',
            transactionId: 'TXN-2025-08-028',
            status: 'paid',
            rating: 4.8,
            description: 'Fixed washing machine drainage issue. Cleaned filter, replaced drain hose, and checked motor functionality.',
            timeline: [
                { event: 'Job Requested', date: '2025-08-27', time: '3:00 PM' },
                { event: 'Job Accepted', date: '2025-08-27', time: '3:20 PM' },
                { event: 'Work Started', date: '2025-08-28', time: '3:00 PM' },
                { event: 'Work Completed', date: '2025-08-28', time: '4:30 PM' },
                { event: 'Payment Received', date: '2025-08-28', time: '4:30 PM' }
            ]
        },
        {
            id: 5,
            jobTitle: 'Bathroom Plumbing Fix',
            category: 'Plumbing',
            customerName: 'Kamala Silva',
            location: 'Dehiwala',
            contact: '+94 77 234 5678',
            date: '2025-08-27',
            completedTime: 'Pending',
            duration: '1 hour',
            serviceFee: 1800,
            platformFee: 180,
            materialsCost: 300,
            totalEarned: 1920,
            paymentMethod: 'Pending',
            transactionId: 'TXN-2025-08-027',
            status: 'pending',
            rating: 0,
            description: 'Fixed leaking bathroom faucet and toilet flush mechanism. Replaced worn seals and adjusted water flow.',
            timeline: [
                { event: 'Job Requested', date: '2025-08-27', time: '8:00 AM' },
                { event: 'Job Accepted', date: '2025-08-27', time: '8:15 AM' },
                { event: 'Work Started', date: '2025-08-27', time: '10:00 AM' },
                { event: 'Work Completed', date: '2025-08-27', time: '11:00 AM' },
                { event: 'Awaiting Payment', date: '2025-08-27', time: '11:15 AM' }
            ]
        }
    ],
    company: []
};

document.addEventListener('DOMContentLoaded', function() {
    initializeEarningsPage();
});

function initializeEarningsPage() {
    initializeFilters();
    initializeTableSorting();
    initializeSearch();
    updateEarningsSummary();
}

// ===== FILTERS FUNCTIONALITY ===== 
function initializeFilters() {
    const periodFilter = document.getElementById('period-filter');
    const statusFilter = document.getElementById('status-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    periodFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    statusFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    sortFilter.addEventListener('change', function() {
        sortEarningsTable(this.value);
    });
}

function applyFilters() {
    const periodFilter = document.getElementById('period-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    
    const earningsRows = document.querySelectorAll('.earnings-row');
    let visibleCount = 0;
    
    earningsRows.forEach(row => {
        let showRow = true;
        
        // Apply status filter
        if (statusFilter !== 'all') {
            const rowStatus = row.getAttribute('data-status');
            if (rowStatus !== statusFilter) {
                showRow = false;
            }
        }
        
        // Apply period filter (simplified - in real app would use actual dates)
        if (periodFilter !== 'all') {
            // For demo purposes, we'll show/hide based on some logic
            const dateText = row.querySelector('.date-primary').textContent;
            if (periodFilter === 'this-month' && !dateText.includes('Sep')) {
                showRow = false;
            }
        }
        
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update count
    updateFilteredCount(visibleCount, statusFilter, periodFilter);
    
    
}

function updateFilteredCount(count, status, period) {
    const sectionSubtitle = document.querySelector('.section-subtitle');
    let label = 'payments';
    
    if (status !== 'all') {
        label = `${status} payments`;
    }
    
    if (period !== 'all') {
        const periodLabel = document.querySelector(`option[value="${period}"]`).textContent.toLowerCase();
        sectionSubtitle.textContent = `${count} ${label} ${periodLabel}`;
    } else {
        sectionSubtitle.textContent = `${count} ${label} found`;
    }
}

function resetFilters() {
    document.getElementById('period-filter').value = 'this-month';
    document.getElementById('status-filter').value = 'all';
    document.getElementById('sort-filter').value = 'newest';
    
    applyFilters();
    sortEarningsTable('newest');
    
    showNotification('Filters reset successfully!', 'info');
}

// ===== TABLE SORTING FUNCTIONALITY =====
function initializeTableSorting() {
    const sortableHeaders = document.querySelectorAll('.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            const currentDirection = this.getAttribute('data-direction') || 'asc';
            const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            
            // Reset all other headers
            sortableHeaders.forEach(h => {
                h.setAttribute('data-direction', '');
                h.querySelector('i').className = 'fas fa-sort';
            });
            
            // Set current header
            this.setAttribute('data-direction', newDirection);
            this.querySelector('i').className = newDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';
            
            sortEarningsTable(sortBy, newDirection);
        });
    });
}

function sortEarningsTable(sortBy, direction = 'desc') {
    const tbody = document.querySelector('.earnings-table tbody');
    const rows = Array.from(tbody.querySelectorAll('.earnings-row'));
    
    rows.sort((a, b) => {
        let aValue, bValue;
        
        switch(sortBy) {
            case 'date':
                aValue = getDateValue(a);
                bValue = getDateValue(b);
                break;
            case 'job':
                aValue = a.querySelector('.job-title').textContent.toLowerCase();
                bValue = b.querySelector('.job-title').textContent.toLowerCase();
                break;
            case 'customer':
                aValue = a.querySelector('.customer-name').textContent.toLowerCase();
                bValue = b.querySelector('.customer-name').textContent.toLowerCase();
                break;
            case 'amount':
                aValue = getAmountValue(a);
                bValue = getAmountValue(b);
                break;
            case 'status':
                aValue = a.querySelector('.payment-status').textContent.toLowerCase();
                bValue = b.querySelector('.payment-status').textContent.toLowerCase();
                break;
            case 'newest':
                aValue = getDateValue(a);
                bValue = getDateValue(b);
                direction = 'desc';
                break;
            case 'oldest':
                aValue = getDateValue(a);
                bValue = getDateValue(b);
                direction = 'asc';
                break;
            case 'amount-high':
                aValue = getAmountValue(a);
                bValue = getAmountValue(b);
                direction = 'desc';
                break;
            case 'amount-low':
                aValue = getAmountValue(a);
                bValue = getAmountValue(b);
                direction = 'asc';
                break;
            default:
                return 0;
        }
        
        if (direction === 'asc') {
            return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
        } else {
            return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
        }
    });
    
    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
    
    `);
}

function getDateValue(row) {
    const dateText = row.querySelector('.date-primary').textContent;
    // Simple date parsing - in real app would use proper date parsing
    if (dateText.includes('Sep 1')) return new Date('2025-09-01');
    if (dateText.includes('Aug 30')) return new Date('2025-08-30');
    if (dateText.includes('Aug 29')) return new Date('2025-08-29');
    if (dateText.includes('Aug 28')) return new Date('2025-08-28');
    if (dateText.includes('Aug 27')) return new Date('2025-08-27');
    if (dateText.includes('Aug 25')) return new Date('2025-08-25');
    return new Date();
}

function getAmountValue(row) {
    const amountText = row.querySelector('.amount-earned').textContent;
    // Extract number from "LKR 2,500" format
    return parseInt(amountText.replace(/[^\d]/g, '')) || 0;
}

// ===== SEARCH FUNCTIONALITY =====
function initializeSearch() {
    const searchInput = document.querySelector('.search-box input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            searchEarnings(searchTerm);
        });
    }
}

function searchEarnings(searchTerm) {
    const earningsRows = document.querySelectorAll('.earnings-row');
    let visibleCount = 0;
    
    earningsRows.forEach(row => {
        const jobTitle = row.querySelector('.job-title').textContent.toLowerCase();
        const customerName = row.querySelector('.customer-name').textContent.toLowerCase();
        const customerLocation = row.querySelector('.customer-location').textContent.toLowerCase();
        const category = row.querySelector('.job-category').textContent.toLowerCase();
        
        const matches = jobTitle.includes(searchTerm) || 
                       customerName.includes(searchTerm) || 
                       customerLocation.includes(searchTerm) ||
                       category.includes(searchTerm);
        
        if (matches || searchTerm === '') {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update count
    const sectionSubtitle = document.querySelector('.section-subtitle');
    if (searchTerm) {
        sectionSubtitle.textContent = `${visibleCount} payments found for "${searchTerm}"`;
    } else {
        sectionSubtitle.textContent = '18 payments this month';
    }
}

// ===== EARNINGS ACTIONS =====
function viewEarningDetails(earningId) {
    
    // Find the earning data
    const earning = earningsData.customer.find(e => e.id === earningId);
    if (!earning) {
        showNotification('Earning details not found', 'error');
        return;
    }
    
    // Update status banner
    const statusBanner = document.getElementById('earningStatusBanner');
    const statusIcon = document.getElementById('earningStatusIcon');
    const statusTitle = document.getElementById('earningStatusTitle');
    const statusMessage = document.getElementById('earningStatusMessage');
    
    statusBanner.className = 'earning-status-banner';
    if (earning.status === 'paid') {
        statusBanner.classList.add('paid');
        statusIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        statusTitle.textContent = 'Payment Received';
        statusMessage.textContent = 'This payment has been successfully received';
    } else {
        statusBanner.classList.add('pending');
        statusIcon.innerHTML = '<i class="fas fa-clock"></i>';
        statusTitle.textContent = 'Payment Pending';
        statusMessage.textContent = 'Awaiting payment from customer';
    }
    
    // Populate job information
    document.getElementById('earningJobTitle').textContent = earning.jobTitle;
    document.getElementById('earningCategory').textContent = earning.category;
    document.getElementById('earningDate').textContent = formatDateDisplay(earning.date);
    document.getElementById('earningDuration').textContent = earning.duration;
    
    // Populate customer information
    document.getElementById('earningCustomerName').textContent = earning.customerName;
    document.getElementById('earningLocation').textContent = earning.location;
    document.getElementById('earningContact').textContent = earning.contact;
    
    // Populate rating
    const ratingEl = document.getElementById('earningRating');
    if (earning.rating > 0) {
        const stars = Array(5).fill(0).map((_, i) => 
            `<i class="fas fa-star" style="color: ${i < earning.rating ? '#f39c12' : '#ddd'};"></i>`
        ).join('');
        ratingEl.innerHTML = `${stars} <span style="margin-left: 8px;">${earning.rating.toFixed(1)}</span>`;
    } else {
        ratingEl.innerHTML = '<span style="color: var(--text-secondary);">No rating yet</span>';
    }
    
    // Populate payment breakdown
    document.getElementById('earningServiceFee').textContent = `LKR ${earning.serviceFee.toLocaleString()}`;
    document.getElementById('earningPlatformFee').textContent = `- LKR ${earning.platformFee.toLocaleString()}`;
    document.getElementById('earningMaterialsCost').textContent = `LKR ${earning.materialsCost.toLocaleString()}`;
    document.getElementById('earningTotalEarned').innerHTML = `<strong>LKR ${earning.totalEarned.toLocaleString()}</strong>`;
    
    // Populate payment information
    document.getElementById('earningPaymentMethod').textContent = earning.paymentMethod;
    document.getElementById('earningTransactionId').textContent = earning.transactionId;
    document.getElementById('earningPaymentDate').textContent = earning.status === 'paid' 
        ? `${formatDateDisplay(earning.date)} - ${earning.completedTime}`
        : 'Pending';
    
    const paymentStatusBadge = document.getElementById('earningPaymentStatus');
    paymentStatusBadge.textContent = earning.status.charAt(0).toUpperCase() + earning.status.slice(1);
    paymentStatusBadge.className = `status-badge ${earning.status}`;
    
    // Populate description
    document.getElementById('earningDescription').textContent = earning.description;
    
    // Populate timeline
    const timelineEl = document.getElementById('earningTimeline');
    timelineEl.innerHTML = '';
    earning.timeline.forEach(item => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <h5>${item.event}</h5>
                <p>${item.date} at ${item.time}</p>
            </div>
        `;
        timelineEl.appendChild(timelineItem);
    });
    
    // Show/hide reminder button
    const reminderBtn = document.getElementById('drawerReminderBtn');
    reminderBtn.style.display = earning.status === 'pending' ? 'inline-flex' : 'none';
    reminderBtn.onclick = () => sendReminder(earning.id);
    
    // Store earning ID for invoice download
    document.getElementById('earningDetailsDrawer').dataset.earningId = earning.id;
    
    // Open drawer
    document.getElementById('earningDetailsDrawer').classList.add('active');
}

function closeEarningDetailsDrawer() {
    document.getElementById('earningDetailsDrawer').classList.remove('active');
}

function downloadInvoiceFromDrawer() {
    const earningId = document.getElementById('earningDetailsDrawer').dataset.earningId;
    downloadInvoice(parseInt(earningId));
}

function sendReminderFromDrawer() {
    const earningId = document.getElementById('earningDetailsDrawer').dataset.earningId;
    sendReminder(parseInt(earningId));
}

function formatDateDisplay(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
}

function downloadInvoice(earningId) {
    
    showNotification('Generating invoice...', 'info');
    
    // Simulate invoice generation and download
    setTimeout(() => {
        showNotification('Invoice downloaded successfully!', 'success');
        
    }, 1500);
}

function sendReminder(earningId) {
    
    if (confirm('Send payment reminder to customer? This will notify them about the pending payment.')) {
        showNotification('Sending payment reminder...', 'info');
        
        setTimeout(() => {
            showNotification('Payment reminder sent successfully!', 'success');
            
            // Update the UI to show reminder was sent
            const row = document.querySelector(`[onclick="sendReminder(${earningId})"]`).closest('tr');
            const dateSecondary = row.querySelector('.date-secondary');
            dateSecondary.textContent = 'Reminder sent today';
        }, 1000);
    }
}

function checkStatus(earningId) {
    
    showNotification('Checking payment status...', 'info');
    
    // Simulate status check
    setTimeout(() => {
        const statuses = ['processing', 'paid', 'pending'];
        const newStatus = statuses[Math.floor(Math.random() * statuses.length)];
        
        if (newStatus === 'paid') {
            // Update the row to show paid status
            const row = document.querySelector(`[onclick="checkStatus(${earningId})"]`).closest('tr');
            updatePaymentStatus(row, 'paid');
            showNotification('Payment received! Status updated to Paid.', 'success');
        } else {
            showNotification(`Status checked: Still ${newStatus}`, 'info');
        }
    }, 1500);
}

function updatePaymentStatus(row, status) {
    const statusCell = row.querySelector('.payment-status');
    
    statusCell.className = `payment-status ${status}`;
    
    switch(status) {
        case 'paid':
            statusCell.innerHTML = '<i class="fas fa-check-circle"></i>Paid';
            break;
        case 'pending':
            statusCell.innerHTML = '<i class="fas fa-clock"></i>Pending';
            break;
        case 'processing':
            statusCell.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Processing';
            break;
    }
    
    // Update row data attribute
    row.setAttribute('data-status', status);
    
    // Update summary if needed
    updateEarningsSummary();
}

function loadMoreEarnings() {
    
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const originalText = loadMoreBtn.innerHTML;
    
    // Show loading state
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    loadMoreBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        loadMoreBtn.innerHTML = originalText;
        loadMoreBtn.disabled = false;
        
        // Add some demo earnings
        addDemoEarnings();
        
        showNotification('5 more earnings loaded!', 'success');
    }, 2000);
}

function addDemoEarnings() {
    const tbody = document.querySelector('.earnings-table tbody');
    
    const demoEarnings = [
        {
            date: 'Aug 23, 2025',
            relative: '9 days ago',
            job: 'Water Heater Repair',
            category: 'Plumbing',
            customer: 'Ananda Wickramasinghe',
            location: 'Kottawa',
            amount: 'LKR 3,200',
            status: 'paid'
        },
        {
            date: 'Aug 22, 2025',
            relative: '10 days ago',
            job: 'Door Lock Installation',
            category: 'General',
            customer: 'Sunil Fernando',
            location: 'Panadura',
            amount: 'LKR 1,200',
            status: 'paid'
        },
        {
            date: 'Aug 20, 2025',
            relative: '12 days ago',
            job: 'Garden Light Setup',
            category: 'Electrical',
            customer: 'Mala Perera',
            location: 'Moratuwa',
            amount: 'LKR 2,800',
            status: 'pending'
        },
        {
            date: 'Aug 18, 2025',
            relative: '2 weeks ago',
            job: 'Roof Leak Fix',
            category: 'General',
            customer: 'Buddhika Rathnayake',
            location: 'Gampaha',
            amount: 'LKR 4,500',
            status: 'paid'
        },
        {
            date: 'Aug 15, 2025',
            relative: '2 weeks ago',
            job: 'Kitchen Cabinet Repair',
            category: 'Carpentry',
            customer: 'Dilani Jayasuriya',
            location: 'Kelaniya',
            amount: 'LKR 3,800',
            status: 'paid'
        }
    ];
    
    demoEarnings.forEach((earning, index) => {
        const row = createEarningRow(earning, Date.now() + index);
        tbody.appendChild(row);
    });
    
    // Update count
    const visibleRows = document.querySelectorAll('.earnings-row:not([style*="none"])').length;
    document.querySelector('.section-subtitle').textContent = `${visibleRows} payments this month`;
}

function createEarningRow(earning, earningId) {
    const row = document.createElement('tr');
    row.className = 'earnings-row';
    row.setAttribute('data-status', earning.status);
    
    const statusIcon = earning.status === 'paid' 
        ? '<i class="fas fa-check-circle"></i>'
        : earning.status === 'pending'
        ? '<i class="fas fa-clock"></i>'
        : '<i class="fas fa-spinner fa-spin"></i>';
    
    const actions = earning.status === 'paid'
        ? `
            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails('${earningId}')">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline" onclick="downloadInvoice('${earningId}')">
                <i class="fas fa-download"></i>
            </button>
        `
        : earning.status === 'pending'
        ? `
            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails('${earningId}')">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-primary" onclick="sendReminder('${earningId}')">
                <i class="fas fa-bell"></i>
            </button>
        `
        : `
            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails('${earningId}')">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-secondary" onclick="checkStatus('${earningId}')">
                <i class="fas fa-refresh"></i>
            </button>
        `;
    
    row.innerHTML = `
        <td class="date-cell">
            <div class="date-info">
                <span class="date-primary">${earning.date}</span>
                <span class="date-secondary">${earning.relative}</span>
            </div>
        </td>
        <td class="job-cell">
            <div class="job-info">
                <span class="job-title">${earning.job}</span>
                <span class="job-category">${earning.category}</span>
            </div>
        </td>
        <td class="customer-cell">
            <div class="customer-info">
                <span class="customer-name">${earning.customer}</span>
                <span class="customer-location">${earning.location}</span>
            </div>
        </td>
        <td class="amount-cell">
            <span class="amount-earned">${earning.amount}</span>
        </td>
        <td class="status-cell">
            <span class="payment-status ${earning.status}">
                ${statusIcon}
                ${earning.status.charAt(0).toUpperCase() + earning.status.slice(1)}
            </span>
        </td>
        <td class="actions-cell">
            ${actions}
        </td>
    `;
    
    return row;
}

// ===== SUMMARY UPDATES =====
function updateEarningsSummary() {
    const allRows = document.querySelectorAll('.earnings-row');
    const paidRows = document.querySelectorAll('.earnings-row[data-status="paid"]');
    const pendingRows = document.querySelectorAll('.earnings-row[data-status="pending"]');
    
    let totalEarnings = 0;
    let monthlyEarnings = 0;
    let pendingAmount = 0;
    
    allRows.forEach(row => {
        const amount = getAmountValue(row);
        const status = row.getAttribute('data-status');
        const dateText = row.querySelector('.date-primary').textContent;
        
        if (status === 'paid') {
            totalEarnings += amount;
            if (dateText.includes('Sep') || dateText.includes('Aug')) {
                monthlyEarnings += amount;
            }
        } else if (status === 'pending') {
            pendingAmount += amount;
        }
    });
    
    // Update summary cards (simplified)
    
}

// ===== UTILITY FUNCTIONS =====
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.background = '#10b981';
            break;
        case 'error':
            notification.style.background = '#ef4444';
            break;
        case 'warning':
            notification.style.background = '#f59e0b';
            break;
        default:
            notification.style.background = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
