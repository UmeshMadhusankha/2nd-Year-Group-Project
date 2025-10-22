// ================================================
// EARNINGS PAGE JAVASCRIPT
// ================================================

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
    
    console.log(`Applied filters: Period=${periodFilter}, Status=${statusFilter}, Visible=${visibleCount}`);
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
    
    console.log(`Sorted by: ${sortBy} (${direction})`);
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
    console.log(`Viewing details for earning ID: ${earningId}`);
    
    showNotification('Opening earning details...', 'info');
    
    // Simulate modal or page navigation
    setTimeout(() => {
        alert(`Earning Details for ID: ${earningId}\n\nThis would show:\n- Complete job information\n- Payment breakdown\n- Transaction details\n- Customer information\n- Invoice copy\n- Timeline of events`);
    }, 500);
}

function downloadInvoice(earningId) {
    console.log(`Downloading invoice for earning ID: ${earningId}`);
    
    showNotification('Generating invoice...', 'info');
    
    // Simulate invoice generation and download
    setTimeout(() => {
        showNotification('Invoice downloaded successfully!', 'success');
        console.log(`Invoice for earning ${earningId} would be downloaded as PDF`);
    }, 1500);
}

function sendReminder(earningId) {
    console.log(`Sending payment reminder for earning ID: ${earningId}`);
    
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
    console.log(`Checking payment status for earning ID: ${earningId}`);
    
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
    console.log('Loading more earnings...');
    
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
    console.log(`Updated summary: Total=${totalEarnings}, Monthly=${monthlyEarnings}, Pending=${pendingAmount}`);
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
