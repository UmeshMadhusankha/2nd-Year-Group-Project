function setupTabSwitching() {
    showTab('payments');
}

function showTab(tabName) {
    // Hide all tab contents
    document.getElementById('income-tab').style.display = 'none';
    document.getElementById('expenses-tab').style.display = 'none';
    
    // Remove active class from all tabs and tab contents
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Show selected tab content
    if (tabName === 'payments') {
        document.getElementById('income-tab').style.display = 'block';
        document.getElementById('income-tab').classList.add('active');
        // Add active class to payments tab button
        document.querySelector(`[data-tab="payments"]`).classList.add('active');
    } else if (tabName === 'expenses') {
        document.getElementById('expenses-tab').style.display = 'block';
        document.getElementById('expenses-tab').classList.add('active');
        // Add active class to expenses tab button
        document.querySelector(`[data-tab="expenses"]`).classList.add('active');
        
        // Load expenses if not already loaded
        if (allExpenses.length === 0) {
            loadExpenseData();
        }
    }
}

// Global variables
let currentPage = 1;
let pageSize = 25;
let currentSort = { field: 'date', direction: 'desc' };
let currentFilters = {
    status: 'all',
    method: 'all',
    project: 'all',
    milestone: 'all',
    amount: 'all',
    search: ''
};
let selectedTransactions = new Set();
let allTransactions = [];
let filteredTransactions = [];

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentData();
    setupEventListeners();
    updateSummaryCharts();
    setupTabSwitching();
});

function loadPaymentData() {
    allTransactions = [
        {
            id: 'PAY-2025-001',
            date: '2025-09-10',
            projectName: 'AC Repair - Colombo Office',
            projectId: 'PRJ-2025-045',
            milestone: 'Final Completion',
            clientName: 'Tech Solutions Ltd',
            amount: 25000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-089',
            description: 'Final milestone payment for AC unit repair and maintenance'
        },
        {
            id: 'PAY-2025-002',
            date: '2025-09-09',
            projectName: 'Plumbing Repair - Kandy Branch',
            projectId: 'PRJ-2025-042',
            milestone: 'Installation Complete',
            clientName: 'Green Valley Hotels',
            amount: 35000,
            method: 'credit-card',
            status: 'completed',
            invoiceNumber: 'INV-2025-087',
            description: 'Second milestone payment for bathroom plumbing renovation'
        },
        {
            id: 'PAY-2025-003',
            date: '2025-09-08',
            projectName: 'Electrical Wiring - Galle Factory',
            projectId: 'PRJ-2025-041',
            milestone: 'Material Delivery',
            clientName: 'Industrial Motors Pvt Ltd',
            amount: 45000,
            method: 'bank-transfer',
            status: 'pending',
            invoiceNumber: 'INV-2025-085',
            description: 'First milestone payment for electrical system upgrade'
        },
        {
            id: 'PAY-2025-004',
            date: '2025-09-07',
            projectName: 'Roof Repair - Negombo Villa',
            projectId: 'PRJ-2025-038',
            milestone: 'Assessment Complete',
            clientName: 'Mr. Perera',
            amount: 18000,
            method: 'cash',
            status: 'completed',
            invoiceNumber: 'INV-2025-083',
            description: 'Initial assessment and planning milestone payment'
        },
        {
            id: 'PAY-2025-005',
            date: '2025-09-06',
            projectName: 'Generator Maintenance - Hospital',
            projectId: 'PRJ-2025-037',
            milestone: 'Maintenance Complete',
            clientName: 'Colombo General Hospital',
            amount: 55000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-081',
            description: 'Complete generator maintenance and testing milestone'
        },
        {
            id: 'PAY-2025-006',
            date: '2025-09-05',
            projectName: 'HVAC Installation - Shopping Mall',
            projectId: 'PRJ-2025-035',
            milestone: 'Phase 1 Complete',
            clientName: 'Metro Mall Pvt Ltd',
            amount: 125000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-079',
            description: 'First phase HVAC installation milestone payment'
        },
        {
            id: 'PAY-2025-007',
            date: '2025-09-04',
            projectName: 'Water Pump Repair - Farm',
            projectId: 'PRJ-2025-034',
            milestone: 'Repair Complete',
            clientName: 'Lanka Agriculture Co',
            amount: 22000,
            method: 'credit-card',
            status: 'completed',
            invoiceNumber: 'INV-2025-077',
            description: 'Water pump repair and replacement milestone'
        },
        {
            id: 'PAY-2025-008',
            date: '2025-09-03',
            projectName: 'Security System - Office Complex',
            projectId: 'PRJ-2025-033',
            milestone: 'Installation Progress',
            clientName: 'Business Park Ltd',
            amount: 38000,
            method: 'bank-transfer',
            status: 'pending',
            invoiceNumber: 'INV-2025-075',
            description: 'Security system installation progress milestone'
        },
        {
            id: 'PAY-2025-009',
            date: '2025-09-02',
            projectName: 'Kitchen Equipment Repair - Restaurant',
            projectId: 'PRJ-2025-031',
            milestone: 'Equipment Testing',
            clientName: 'Golden Spoon Restaurant',
            amount: 28000,
            method: 'credit-card',
            status: 'completed',
            invoiceNumber: 'INV-2025-073',
            description: 'Kitchen equipment repair and testing milestone'
        },
        {
            id: 'PAY-2025-010',
            date: '2025-09-01',
            projectName: 'Solar Panel Maintenance - Factory',
            projectId: 'PRJ-2025-030',
            milestone: 'Cleaning Complete',
            clientName: 'Eco Manufacturing Ltd',
            amount: 42000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-071',
            description: 'Solar panel cleaning and maintenance milestone'
        },
        {
            id: 'PAY-2025-011',
            date: '2025-08-31',
            projectName: 'Elevator Repair - Apartment Complex',
            projectId: 'PRJ-2025-029',
            milestone: 'Safety Inspection',
            clientName: 'Skyline Apartments',
            amount: 65000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-069',
            description: 'Elevator safety inspection and minor repairs milestone'
        },
        {
            id: 'PAY-2025-012',
            date: '2025-08-30',
            projectName: 'Fire Safety System - School',
            projectId: 'PRJ-2025-028',
            milestone: 'System Testing',
            clientName: 'St. Peters College',
            amount: 48000,
            method: 'credit-card',
            status: 'pending',
            invoiceNumber: 'INV-2025-067',
            description: 'Fire safety system testing and certification milestone'
        },
        {
            id: 'PAY-2025-013',
            date: '2025-08-29',
            projectName: 'Internet Infrastructure - Office',
            projectId: 'PRJ-2025-027',
            milestone: 'Network Setup',
            clientName: 'Digital Solutions Inc',
            amount: 32000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-065',
            description: 'Network infrastructure setup milestone payment'
        },
        {
            id: 'PAY-2025-014',
            date: '2025-08-28',
            projectName: 'Washing Machine Repair - Laundry',
            projectId: 'PRJ-2025-026',
            milestone: 'Parts Replacement',
            clientName: 'Clean & Fresh Laundry',
            amount: 15000,
            method: 'cash',
            status: 'completed',
            invoiceNumber: 'INV-2025-063',
            description: 'Industrial washing machine parts replacement milestone'
        },
        {
            id: 'PAY-2025-015',
            date: '2025-08-27',
            projectName: 'Backup Generator - Data Center',
            projectId: 'PRJ-2025-025',
            milestone: 'Installation Complete',
            clientName: 'DataHub Technologies',
            amount: 95000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-061',
            description: 'Backup generator installation and commissioning milestone'
        },
        {
            id: 'PAY-2025-016',
            date: '2025-08-26',
            projectName: 'Lighting System - Warehouse',
            projectId: 'PRJ-2025-024',
            milestone: 'Phase 2 Progress',
            clientName: 'Storage Solutions Ltd',
            amount: 28500,
            method: 'credit-card',
            status: 'pending',
            invoiceNumber: 'INV-2025-059',
            description: 'LED lighting system installation phase 2 milestone'
        },
        {
            id: 'PAY-2025-017',
            date: '2025-08-25',
            projectName: 'Pool Equipment Repair - Hotel',
            projectId: 'PRJ-2025-023',
            milestone: 'Filter Replacement',
            clientName: 'Ocean View Resort',
            amount: 35500,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-057',
            description: 'Swimming pool filtration system repair milestone'
        },
        {
            id: 'PAY-2025-018',
            date: '2025-08-24',
            projectName: 'Refrigeration Unit - Supermarket',
            projectId: 'PRJ-2025-022',
            milestone: 'Temperature Testing',
            clientName: 'Fresh Mart Supermarket',
            amount: 52000,
            method: 'bank-transfer',
            status: 'completed',
            invoiceNumber: 'INV-2025-055',
            description: 'Commercial refrigeration unit repair and testing milestone'
        },
        {
            id: 'PAY-2025-019',
            date: '2025-08-23',
            projectName: 'Sound System - Conference Hall',
            projectId: 'PRJ-2025-021',
            milestone: 'Audio Testing',
            clientName: 'Convention Center',
            amount: 25500,
            method: 'credit-card',
            status: 'completed',
            invoiceNumber: 'INV-2025-053',
            description: 'Audio system installation and testing milestone'
        },
        {
            id: 'PAY-2025-020',
            date: '2025-08-22',
            projectName: 'Ventilation System - Factory',
            projectId: 'PRJ-2025-020',
            milestone: 'Airflow Testing',
            clientName: 'Manufacturing Corp',
            amount: 68000,
            method: 'bank-transfer',
            status: 'pending',
            invoiceNumber: 'INV-2025-051',
            description: 'Industrial ventilation system testing milestone'
        }
    ];
    
    updateSummaryStats();
    applyFilters();
}

function generateSampleMilestonePayments(count) {
    const customers = [
        { name: 'Ruwan Bandara', email: 'ruwan.b@email.com', avatar: 'RB' },
        { name: 'Thilani Jayasekara', email: 'thilani.j@email.com', avatar: 'TJ' },
        { name: 'Chamara Rathnayake', email: 'chamara.r@email.com', avatar: 'CR' },
        { name: 'Nimali Gunasekara', email: 'nimali.g@email.com', avatar: 'NG' },
        { name: 'Kasun Wijeratne', email: 'kasun.w@email.com', avatar: 'KW' }
    ];
    
    const projects = [
        { title: 'Kitchen Renovation', description: 'Complete kitchen upgrade with modern appliances' },
        { title: 'Office Electrical Repair', description: 'Electrical system maintenance and repair' },
        { title: 'Bathroom Plumbing Fix', description: 'Complete bathroom plumbing renovation' },
        { title: 'HVAC Installation', description: 'Central air conditioning system installation' },
        { title: 'Appliance Repair Service', description: 'Home appliance repair and maintenance' }
    ];
    
    const milestoneTypes = [
        { type: 'initial', title: 'Project Started', description: 'Initial payment for materials and setup', percentage: 30 },
        { type: 'progress', title: 'Work in Progress', description: 'Milestone payment for completed phase', percentage: 60 },
        { type: 'completion', title: 'Work Completed', description: 'Payment after work completion', percentage: 90 },
        { type: 'final', title: 'Final Payment', description: 'Final payment after quality check', percentage: 100 }
    ];
    
    const methods = ['online-banking', 'card', 'cash', 'wallet'];
    const statuses = ['completed', 'pending', 'failed'];
    const repairers = ['Saman Kumara', 'Ajith Perera', 'Gayan Silva', 'Roshan Fernando'];
    
    const transactions = [];
    
    for (let i = 0; i < count; i++) {
        const customer = customers[Math.floor(Math.random() * customers.length)];
        const project = projects[Math.floor(Math.random() * projects.length)];
        const milestone = milestoneTypes[Math.floor(Math.random() * milestoneTypes.length)];
        const method = methods[Math.floor(Math.random() * methods.length)];
        const status = statuses[Math.floor(Math.random() * statuses.length)];
        const repairer = repairers[Math.floor(Math.random() * repairers.length)];
        
        // Generate random date within last 30 days
        const date = new Date();
        date.setDate(date.getDate() - Math.floor(Math.random() * 30));
        
        // Generate due date (5-15 days from payment date)
        const dueDate = new Date(date);
        dueDate.setDate(dueDate.getDate() + Math.floor(Math.random() * 10) + 5);
        
        transactions.push({
            id: `INV-2024-${(1239 + i).toString().padStart(6, '0')}`,
            date: date.toISOString(),
            customer: customer,
            project: {
                id: `PRJ-${(6 + i).toString().padStart(3, '0')}`,
                title: project.title,
                description: project.description
            },
            milestone: milestone,
            amount: Math.floor(Math.random() * 80000) + 15000,
            method: method,
            status: status,
            repairer: repairer,
            dueDate: dueDate.toISOString().split('T')[0]
        });
    }
    
    return transactions;
}

function setupEventListeners() {
    // Period selector
    document.getElementById('period-select').addEventListener('change', updatePeriod);
    
    // Search functionality
    document.getElementById('search-filter').addEventListener('input', debounce(searchTransactions, 300));
    
    // Page size change
    document.getElementById('page-size').addEventListener('change', changePageSize);
}

// Period management
function updatePeriod() {
    const period = document.getElementById('period-select').value;
    const periodBadge = document.getElementById('current-period');
    
    if (period === 'custom') {
        showDateRangeModal();
        return;
    }
    
    const periodLabels = {
        'today': 'Today',
        'week': 'This Week',
        'month': 'This Month',
        'quarter': 'This Quarter',
        'year': 'This Year'
    };
    
    periodBadge.textContent = periodLabels[period];
    
    // Filter transactions based on period
    filterByPeriod(period);
    updateSummaryStats();
    applyFilters();
}

function filterByPeriod(period) {
    const now = new Date();
    let startDate = new Date();
    
    switch(period) {
        case 'today':
            startDate.setHours(0, 0, 0, 0);
            break;
        case 'week':
            startDate.setDate(now.getDate() - now.getDay());
            startDate.setHours(0, 0, 0, 0);
            break;
        case 'month':
            startDate.setDate(1);
            startDate.setHours(0, 0, 0, 0);
            break;
        case 'quarter':
            const quarter = Math.floor(now.getMonth() / 3);
            startDate.setMonth(quarter * 3, 1);
            startDate.setHours(0, 0, 0, 0);
            break;
        case 'year':
            startDate.setMonth(0, 1);
            startDate.setHours(0, 0, 0, 0);
            break;
    }
    
    allTransactions = allTransactions.filter(transaction => {
        const transactionDate = new Date(transaction.date);
        return transactionDate >= startDate && transactionDate <= now;
    });
}

// Summary statistics
function updateSummaryStats() {
    if (allTransactions.length === 0) {
        // Show zero values when no data
        document.getElementById('total-revenue').textContent = 'LKR 0';
        document.getElementById('total-expenses').textContent = 'LKR 0';
        document.getElementById('net-profit').textContent = 'LKR 0';
        document.getElementById('pending-payments').textContent = 'LKR 0';
        document.getElementById('total-transactions').textContent = '0';
        document.getElementById('avg-payment').textContent = 'LKR 0';
        return;
    }

    const completed = allTransactions.filter(t => t.status === 'completed');
    const pending = allTransactions.filter(t => t.status === 'pending');
    
    const totalRevenue = completed.reduce((sum, t) => sum + t.amount, 0);
    const totalExpenses = Math.floor(totalRevenue * 0.618); // Assuming 61.8% expenses
    const netProfit = totalRevenue - totalExpenses;
    const pendingAmount = pending.reduce((sum, t) => sum + t.amount, 0);
    const avgPayment = completed.length > 0 ? Math.floor(totalRevenue / completed.length) : 0;
    
    document.getElementById('total-revenue').textContent = `LKR ${totalRevenue.toLocaleString()}`;
    document.getElementById('total-expenses').textContent = `LKR ${totalExpenses.toLocaleString()}`;
    document.getElementById('net-profit').textContent = `LKR ${netProfit.toLocaleString()}`;
    document.getElementById('pending-payments').textContent = `LKR ${pendingAmount.toLocaleString()}`;
    document.getElementById('total-transactions').textContent = allTransactions.length.toString();
    document.getElementById('avg-payment').textContent = `LKR ${avgPayment.toLocaleString()}`;
}

// Filter functions
function applyFilters() {
    filteredTransactions = allTransactions.filter(transaction => {
        // Status filter
        if (currentFilters.status !== 'all' && transaction.status !== currentFilters.status) {
            return false;
        }
        
        // Method filter
        if (currentFilters.method !== 'all' && transaction.method !== currentFilters.method) {
            return false;
        }
        
        // Amount filter
        if (currentFilters.amount !== 'all') {
            const amount = transaction.amount;
            switch(currentFilters.amount) {
                case '0-1000':
                    if (amount > 1000) return false;
                    break;
                case '1000-5000':
                    if (amount <= 1000 || amount > 5000) return false;
                    break;
                case '5000-10000':
                    if (amount <= 5000 || amount > 10000) return false;
                    break;
                case '10000+':
                    if (amount <= 10000) return false;
                    break;
            }
        }
        
        // Service filter
        if (currentFilters.service !== 'all' && transaction.service !== currentFilters.service) {
            return false;
        }
        
        // Search filter
        if (currentFilters.search) {
            const searchTerm = currentFilters.search.toLowerCase();
            const searchableText = `${transaction.id} ${transaction.clientName} ${transaction.projectName} ${transaction.milestone} ${transaction.invoiceNumber}`.toLowerCase();
            if (!searchableText.includes(searchTerm)) {
                return false;
            }
        }
        
        return true;
    });
    
    sortTransactions();
    
    // Reset to first page
    currentPage = 1;
    
    // Update display
    displayTransactions();
    updatePagination();
    updateResultsCount();
}

function resetFilters() {
    currentFilters = {
        status: 'all',
        method: 'all',
        amount: 'all',
        service: 'all',
        search: ''
    };
    
    // Reset form elements
    document.getElementById('status-filter').value = 'all';
    document.getElementById('method-filter').value = 'all';
    document.getElementById('amount-filter').value = 'all';
    document.getElementById('service-filter').value = 'all';
    document.getElementById('search-filter').value = '';
    
    applyFilters();
}

function searchTransactions() {
    currentFilters.search = document.getElementById('search-filter').value;
    applyFilters();
}

// Filter event handlers
document.addEventListener('DOMContentLoaded', function() {
    // Status filter
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            currentFilters.status = this.value;
            applyFilters();
        });
    }

    // Project filter
    const projectFilter = document.getElementById('project-filter');
    if (projectFilter) {
        projectFilter.addEventListener('change', function() {
            currentFilters.project = this.value;
            applyFilters();
        });
    }

    // Milestone filter
    const milestoneFilter = document.getElementById('milestone-filter');
    if (milestoneFilter) {
        milestoneFilter.addEventListener('change', function() {
            currentFilters.milestone = this.value;
            applyFilters();
        });
    }

    // Amount filter
    const amountFilter = document.getElementById('amount-filter');
    if (amountFilter) {
        amountFilter.addEventListener('change', function() {
            currentFilters.amount = this.value;
            applyFilters();
        });
    }
});

// Sorting
function sortTable(field) {
    if (currentSort.field === field) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.field = field;
        currentSort.direction = 'desc';
    }
    
    // Update sort indicators
    updateSortIndicators();
    
    sortTransactions();
    displayTransactions();
}

function sortTransactions() {
    filteredTransactions.sort((a, b) => {
        let aValue, bValue;
        
        switch(currentSort.field) {
            case 'date':
                aValue = new Date(a.date);
                bValue = new Date(b.date);
                break;
            case 'invoice':
                aValue = a.id;
                bValue = b.id;
                break;
            case 'project':
                aValue = a.project.title;
                bValue = b.project.title;
                break;
            case 'milestone':
                aValue = a.milestone.type;
                bValue = b.milestone.type;
                break;
            case 'customer':
                aValue = a.customer.name;
                bValue = b.customer.name;
                break;
            case 'amount':
                aValue = a.amount;
                bValue = b.amount;
                break;
            case 'status':
                aValue = a.status;
                bValue = b.status;
                break;
            default:
                return 0;
        }
        
        if (aValue < bValue) return currentSort.direction === 'asc' ? -1 : 1;
        if (aValue > bValue) return currentSort.direction === 'asc' ? 1 : -1;
        return 0;
    });
}

function updateSortIndicators() {
    // Reset all sort indicators
    document.querySelectorAll('.payments-table th.sortable i').forEach(icon => {
        icon.className = 'fas fa-sort';
    });
    
    // Update current sort indicator
    const currentHeader = document.querySelector(`[onclick="sortTable('${currentSort.field}')"] i`);
    if (currentHeader) {
        currentHeader.className = `fas fa-sort-${currentSort.direction === 'asc' ? 'up' : 'down'}`;
    }
}

// Display functions
function displayTransactions() {
    const tbody = document.getElementById('payments-tbody');
    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = startIndex + pageSize;
    const pageTransactions = filteredTransactions.slice(startIndex, endIndex);
    
    tbody.innerHTML = '';
    
    pageTransactions.forEach(transaction => {
        const row = createTransactionRow(transaction);
        tbody.appendChild(row);
    });
    
    // Clear selections
    selectedTransactions.clear();
    document.getElementById('select-all').checked = false;
}

function createTransactionRow(transaction) {
    const row = document.createElement('tr');
    row.className = 'payment-row';
    row.onclick = () => openInvoiceModal(transaction.id);
    
    const statusClass = getStatusClass(transaction.status);
    const milestoneClass = getMilestoneClass(transaction.milestone.type);
    
    row.innerHTML = `
        <td onclick="event.stopPropagation()">
            <input type="checkbox" class="transaction-checkbox" value="${transaction.id}" 
                   onchange="toggleTransactionSelection('${transaction.id}')" />
        </td>
        <td>
            <div class="date-info">
                <div class="payment-date">${formatDate(transaction.date)}</div>
                <div class="payment-time">${formatTime(transaction.date)}</div>
            </div>
        </td>
        <td>
            <span class="invoice-id">${transaction.id}</span>
        </td>
        <td>
            <div class="project-info">
                <div class="project-title">${transaction.project.title}</div>
                <div class="project-id">${transaction.project.id}</div>
            </div>
        </td>
        <td>
            <div class="milestone-info">
                <span class="milestone-badge ${milestoneClass}">${transaction.milestone.type}</span>
                <div class="milestone-description">${transaction.milestone.title}</div>
                <div class="milestone-progress">${transaction.milestone.percentage}% Complete</div>
            </div>
        </td>
        <td>
            <div class="customer-info">
                <div class="customer-avatar">${transaction.customer.avatar}</div>
                <div class="customer-details">
                    <div class="customer-name">${transaction.customer.name}</div>
                    <div class="customer-email">${transaction.customer.email}</div>
                </div>
            </div>
        </td>
        <td>
            <div class="amount-info">
                <span class="amount">LKR ${transaction.amount.toLocaleString()}</span>
                <div class="due-date">Due: ${formatDate(transaction.dueDate)}</div>
            </div>
        </td>
        <td>
            <span class="status-badge ${statusClass}">${transaction.status}</span>
        </td>
        <td onclick="event.stopPropagation()">
            <div class="action-buttons-table">
                <button class="action-btn-sm view" onclick="openInvoiceModal('${transaction.id}')" 
                        title="View Invoice">
                    <i class="fas fa-file-invoice"></i>
                </button>
                <button class="action-btn-sm download" onclick="downloadInvoice('${transaction.id}')" 
                        title="Download Invoice">
                    <i class="fas fa-download"></i>
                </button>
                <button class="action-btn-sm print" onclick="printInvoice('${transaction.id}')" 
                        title="Print Invoice">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </td>
    `;
    
    return row;
}

// Helper function for milestone classes
function getMilestoneClass(type) {
    const classes = {
        'initial': 'initial',
        'progress': 'progress',
        'completion': 'completion',
        'final': 'final'
    };
    return classes[type] || 'initial';
}

// Helper functions
function getStatusClass(status) {
    const classes = {
        'completed': 'completed',
        'pending': 'pending',
        'failed': 'failed',
        'refunded': 'refunded'
    };
    return classes[status] || 'completed';
}

function getMethodIcon(method) {
    const icons = {
        'online-banking': 'fas fa-university',
        'card': 'fas fa-credit-card',
        'cash': 'fas fa-money-bill',
        'wallet': 'fas fa-wallet'
    };
    return icons[method] || 'fas fa-credit-card';
}

function getMethodName(method) {
    const names = {
        'online-banking': 'Online Banking',
        'card': 'Credit/Debit Card',
        'cash': 'Cash',
        'wallet': 'Digital Wallet'
    };
    return names[method] || method;
}

function getServiceColor(service) {
    const colors = {
        'electrical': '#3498db',
        'plumbing': '#e74c3c',
        'hvac': '#9b59b6',
        'appliance': '#27ae60',
        'carpentry': '#f39c12'
    };
    return colors[service] || '#3498db';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredTransactions.length / pageSize);
    const paginationNumbers = document.getElementById('pagination-numbers');
    
    // Update pagination info
    const startItem = Math.min((currentPage - 1) * pageSize + 1, filteredTransactions.length);
    const endItem = Math.min(currentPage * pageSize, filteredTransactions.length);
    document.getElementById('pagination-info').textContent = 
        `Showing ${startItem}-${endItem} of ${filteredTransactions.length} results`;
    
    // Update button states
    document.getElementById('first-btn').classList.toggle('disabled', currentPage === 1);
    document.getElementById('prev-btn').classList.toggle('disabled', currentPage === 1);
    document.getElementById('next-btn').classList.toggle('disabled', currentPage === totalPages);
    document.getElementById('last-btn').classList.toggle('disabled', currentPage === totalPages);
    
    // Generate page numbers
    paginationNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-btn ${i === currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.onclick = () => changePage(i);
            paginationNumbers.appendChild(pageBtn);
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            const dots = document.createElement('span');
            dots.textContent = '...';
            dots.style.padding = '8px';
            dots.style.color = 'var(--text-secondary)';
            paginationNumbers.appendChild(dots);
        }
    }
}

function changePage(page) {
    if (typeof page === 'string') {
        const totalPages = Math.ceil(filteredTransactions.length / pageSize);
        switch(page) {
            case 'first':
                currentPage = 1;
                break;
            case 'prev':
                currentPage = Math.max(1, currentPage - 1);
                break;
            case 'next':
                currentPage = Math.min(totalPages, currentPage + 1);
                break;
            case 'last':
                currentPage = totalPages;
                break;
        }
    } else {
        currentPage = page;
    }
    
    displayTransactions();
    updatePagination();
}

function changePageSize() {
    pageSize = parseInt(document.getElementById('page-size').value);
    currentPage = 1;
    displayTransactions();
    updatePagination();
}

function updateResultsCount() {
    document.getElementById('results-count').textContent = 
        `Showing 1-${Math.min(pageSize, filteredTransactions.length)} of ${filteredTransactions.length} transactions`;
}

// Selection management
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            selectedTransactions.add(checkbox.value);
        } else {
            selectedTransactions.delete(checkbox.value);
        }
    });
}

function toggleTransactionSelection(transactionId) {
    if (selectedTransactions.has(transactionId)) {
        selectedTransactions.delete(transactionId);
    } else {
        selectedTransactions.add(transactionId);
    }
    
    // Update select all checkbox
    const totalCheckboxes = document.querySelectorAll('.transaction-checkbox').length;
    const selectAllCheckbox = document.getElementById('select-all');
    selectAllCheckbox.checked = selectedTransactions.size === totalCheckboxes;
    selectAllCheckbox.indeterminate = selectedTransactions.size > 0 && selectedTransactions.size < totalCheckboxes;
}

// View controls
function setTableView(view) {
    document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[data-view="${view}"]`).classList.add('active');
    
    const table = document.getElementById('payments-table');
    table.className = `payments-table ${view}-view`;
}

// Export functions
function exportReport() {
    showNotification('Exporting full payment report...', 'info');
    // Implementation for full report export
    setTimeout(() => {
        showNotification('Payment report exported successfully!', 'success');
    }, 2000);
}

function toggleExportMenu() {
    const dropdown = document.getElementById('export-dropdown');
    dropdown.classList.toggle('show');
}

function exportAs(format) {
    const dropdown = document.getElementById('export-dropdown');
    dropdown.classList.remove('show');
    
    showNotification(`Exporting as ${format.toUpperCase()}...`, 'info');
    
    // Implementation for different export formats
    setTimeout(() => {
        showNotification(`Export completed! Downloaded as ${format.toUpperCase()}`, 'success');
    }, 2000);
}

// Modal functions
function showDateRangeModal() {
    document.getElementById('date-range-modal').classList.add('show');
}

function closeDateRangeModal() {
    document.getElementById('date-range-modal').classList.remove('show');
    // Reset period selector to month if custom was cancelled
    document.getElementById('period-select').value = 'month';
}

function applyCustomDateRange() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    
    if (!startDate || !endDate) {
        showNotification('Please select both start and end dates', 'error');
        return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
        showNotification('Start date cannot be after end date', 'error');
        return;
    }
    
    // Apply custom date filter
    const start = new Date(startDate);
    const end = new Date(endDate);
    end.setHours(23, 59, 59, 999); // Include full end day
    
    allTransactions = allTransactions.filter(transaction => {
        const transactionDate = new Date(transaction.date);
        return transactionDate >= start && transactionDate <= end;
    });
    
    // Update period badge
    document.getElementById('current-period').textContent = 
        `${startDate} to ${endDate}`;
    
    closeDateRangeModal();
    updateSummaryStats();
    applyFilters();
}

// Action handlers
function openInvoiceModal(invoiceId) {
    const transaction = allTransactions.find(t => t.id === invoiceId);
    if (!transaction) return;
    
    // Create invoice modal dynamically
    const modal = createInvoiceModal(transaction);
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => modal.classList.add('show'), 100);
}

function createInvoiceModal(transaction) {
    const modal = document.createElement('div');
    modal.className = 'invoice-modal-overlay';
    modal.onclick = (e) => {
        if (e.target === modal) closeInvoiceModal();
    };
    
    const statusClass = getStatusClass(transaction.status);
    const milestoneClass = getMilestoneClass(transaction.milestone.type);
    
    modal.innerHTML = `
        <div class="invoice-modal-content">
            <div class="invoice-header">
                <div class="invoice-title">
                    <h2>Invoice ${transaction.id}</h2>
                    <span class="status-badge ${statusClass}">${transaction.status}</span>
                </div>
                <button class="modal-close" onclick="closeInvoiceModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="invoice-body">
                <div class="invoice-section">
                    <h3>Project Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Project:</label>
                            <span>${transaction.project.title}</span>
                        </div>
                        <div class="info-item">
                            <label>Project ID:</label>
                            <span>${transaction.project.id}</span>
                        </div>
                        <div class="info-item">
                            <label>Description:</label>
                            <span>${transaction.project.description}</span>
                        </div>
                    </div>
                </div>
                
                <div class="invoice-section">
                    <h3>Milestone Details</h3>
                    <div class="milestone-card">
                        <div class="milestone-header">
                            <span class="milestone-badge ${milestoneClass}">${transaction.milestone.type}</span>
                            <span class="milestone-percentage">${transaction.milestone.percentage}% Complete</span>
                        </div>
                        <h4>${transaction.milestone.title}</h4>
                        <p>${transaction.milestone.description}</p>
                    </div>
                </div>
                
                <div class="invoice-section">
                    <h3>Customer Information</h3>
                    <div class="customer-details-full">
                        <div class="customer-avatar-large">${transaction.customer.avatar}</div>
                        <div class="customer-info-full">
                            <h4>${transaction.customer.name}</h4>
                            <p>${transaction.customer.email}</p>
                        </div>
                    </div>
                </div>
                
                <div class="invoice-section">
                    <h3>Payment Details</h3>
                    <div class="payment-details">
                        <div class="payment-amount">
                            <label>Amount:</label>
                            <span class="amount-large">LKR ${transaction.amount.toLocaleString()}</span>
                        </div>
                        <div class="payment-dates">
                            <div class="date-item">
                                <label>Payment Date:</label>
                                <span>${formatDate(transaction.date)} ${formatTime(transaction.date)}</span>
                            </div>
                            <div class="date-item">
                                <label>Due Date:</label>
                                <span>${formatDate(transaction.dueDate)}</span>
                            </div>
                        </div>
                        <div class="payment-method-full">
                            <label>Payment Method:</label>
                            <span>${getMethodName(transaction.method)}</span>
                        </div>
                        <div class="repairer-info">
                            <label>Assigned Repairer:</label>
                            <span>${transaction.repairer}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="invoice-footer">
                <button class="btn secondary" onclick="closeInvoiceModal()">Close</button>
                <button class="btn primary" onclick="downloadInvoice('${transaction.id}')">
                    <i class="fas fa-download"></i> Download PDF
                </button>
                <button class="btn secondary" onclick="printInvoice('${transaction.id}')">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    `;
    
    return modal;
}

function closeInvoiceModal() {
    const modal = document.querySelector('.invoice-modal-overlay');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            if (modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 300);
    }
}

function downloadInvoice(invoiceId) {
    showNotification(`Downloading invoice ${invoiceId}...`, 'info');
    // Implementation for downloading invoice
    setTimeout(() => {
        showNotification(`Invoice ${invoiceId} downloaded successfully!`, 'success');
    }, 2000);
}

function printInvoice(invoiceId) {
    showNotification(`Preparing invoice ${invoiceId} for printing...`, 'info');
    // Implementation for printing invoice
    setTimeout(() => {
        window.print();
    }, 1000);
}

function viewTransaction(transactionId) {
    openInvoiceModal(transactionId);
}

function downloadReceipt(transactionId) {
    downloadInvoice(transactionId);
}

function refreshData() {
    showNotification('Refreshing payment data...', 'info');
    
    // Simulate data refresh
    setTimeout(() => {
        loadPaymentData();
        updateSummaryStats();
        showNotification('Payment data refreshed successfully!', 'success');
    }, 1500);
}

// Chart updates
function updateSummaryCharts() {
    // Simple revenue trend chart using canvas
    const canvas = document.getElementById('revenueCanvas');
    const ctx = canvas.getContext('2d');
    
    // Sample data for revenue trend
    const data = [2400, 2800, 3200, 2900, 3500, 4200, 3800];
    const labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Draw simple line chart
    const padding = 40;
    const chartWidth = canvas.width - 2 * padding;
    const chartHeight = canvas.height - 2 * padding;
    
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const valueRange = maxValue - minValue;
    
    // Draw grid lines
    ctx.strokeStyle = '#e0e0e0';
    ctx.lineWidth = 1;
    
    for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding, y);
        ctx.lineTo(canvas.width - padding, y);
        ctx.stroke();
    }
    
    // Draw data line
    ctx.strokeStyle = '#3498db';
    ctx.lineWidth = 3;
    ctx.beginPath();
    
    data.forEach((value, index) => {
        const x = padding + (chartWidth / (data.length - 1)) * index;
        const y = padding + chartHeight - ((value - minValue) / valueRange) * chartHeight;
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw data points
    ctx.fillStyle = '#3498db';
    data.forEach((value, index) => {
        const x = padding + (chartWidth / (data.length - 1)) * index;
        const y = padding + chartHeight - ((value - minValue) / valueRange) * chartHeight;
        
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, Math.PI * 2);
        ctx.fill();
    });
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
        <span>${message}</span>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Remove notification
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const exportDropdown = document.getElementById('export-dropdown');
    const exportToggle = document.querySelector('.dropdown-toggle');
    
    if (!exportToggle.contains(event.target)) {
        exportDropdown.classList.remove('show');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    updateSummaryStats();
    loadExpenseData();
    setupExpenseEventListeners();
});

// Expense global variables
let allExpenses = [];
let filteredExpenses = [];
let currentExpensePage = 1;
let expensePageSize = 10;
let currentExpenseSort = { field: 'date', direction: 'desc' };
let currentExpenseFilters = {
    project: 'all',
    category: 'all',
    date: 'all',
    search: ''
};
let editingExpenseId = null;

// Load expense data
function loadExpenseData() {
    // Sample expense data related to the payment projects
    allExpenses = [
        {
            id: 'EXP-2025-001',
            date: '2025-09-09',
            projectId: 'PRJ-2025-045',
            projectName: 'AC Repair - Colombo Office',
            category: 'materials',
            description: 'AC refrigerant gas and filters for office AC unit repair',
            amount: 8500,
            receipt: null
        },
        {
            id: 'EXP-2025-002',
            date: '2025-09-08',
            projectId: 'PRJ-2025-042',
            projectName: 'Plumbing Repair - Kandy Branch',
            category: 'materials',
            description: 'PVC pipes, fittings, and waterproofing materials for bathroom renovation',
            amount: 12000,
            receipt: null
        },
        {
            id: 'EXP-2025-003',
            date: '2025-09-07',
            projectId: 'PRJ-2025-041',
            projectName: 'Electrical Wiring - Galle Factory',
            category: 'materials',
            description: 'Electrical cables, switches, and circuit breakers for factory wiring',
            amount: 18500,
            receipt: null
        },
        {
            id: 'EXP-2025-004',
            date: '2025-09-06',
            projectId: 'PRJ-2025-037',
            projectName: 'Generator Maintenance - Hospital',
            category: 'labor',
            description: 'Technician labor costs for generator maintenance work',
            amount: 15000,
            receipt: null
        },
        {
            id: 'EXP-2025-005',
            date: '2025-09-05',
            projectId: 'PRJ-2025-035',
            projectName: 'HVAC Installation - Shopping Mall',
            category: 'equipment',
            description: 'Specialized HVAC installation tools and equipment rental',
            amount: 25000,
            receipt: null
        },
        {
            id: 'EXP-2025-006',
            date: '2025-09-04',
            projectId: 'PRJ-2025-034',
            projectName: 'Water Pump Repair - Farm',
            category: 'transport',
            description: 'Transportation costs for equipment and materials to remote farm location',
            amount: 3500,
            receipt: null
        },
        {
            id: 'EXP-2025-007',
            date: '2025-09-03',
            projectId: 'PRJ-2025-033',
            projectName: 'Security System - Office Complex',
            category: 'materials',
            description: 'Security cameras, sensors, and control panel components',
            amount: 22000,
            receipt: null
        },
        {
            id: 'EXP-2025-008',
            date: '2025-09-02',
            projectId: 'PRJ-2025-031',
            projectName: 'Kitchen Equipment Repair - Restaurant',
            category: 'materials',
            description: 'Replacement parts for commercial kitchen equipment',
            amount: 9500,
            receipt: null
        },
        {
            id: 'EXP-2025-009',
            date: '2025-09-01',
            projectId: 'PRJ-2025-030',
            projectName: 'Solar Panel Maintenance - Factory',
            category: 'equipment',
            description: 'Professional cleaning equipment and safety gear for solar panel maintenance',
            amount: 7500,
            receipt: null
        },
        {
            id: 'EXP-2025-010',
            date: '2025-08-31',
            projectId: 'PRJ-2025-029',
            projectName: 'Elevator Repair - Apartment Complex',
            category: 'permits',
            description: 'Safety inspection permits and certification fees for elevator repair',
            amount: 5000,
            receipt: null
        },
        {
            id: 'EXP-2025-011',
            date: '2025-08-30',
            projectId: 'PRJ-2025-028',
            projectName: 'Fire Safety System - School',
            category: 'materials',
            description: 'Fire extinguishers, smoke detectors, and alarm system components',
            amount: 16000,
            receipt: null
        },
        {
            id: 'EXP-2025-012',
            date: '2025-08-29',
            projectId: 'PRJ-2025-027',
            projectName: 'Internet Infrastructure - Office',
            category: 'equipment',
            description: 'Network switches, routers, and cabling equipment',
            amount: 14500,
            receipt: null
        }
    ];
    
    updateExpenseSummaryStats();
    applyExpenseFilters();
    populateExpenseProjectFilters();
}

// Setup expense event listeners
function setupExpenseEventListeners() {
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expense-date').value = today;
}

// Update expense summary statistics
function updateExpenseSummaryStats() {
    const totalExpenses = allExpenses.reduce((sum, expense) => sum + expense.amount, 0);
    
    // Calculate this month's expenses
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();
    const monthlyExpenses = allExpenses.filter(expense => {
        const expenseDate = new Date(expense.date);
        return expenseDate.getMonth() === currentMonth && expenseDate.getFullYear() === currentYear;
    }).reduce((sum, expense) => sum + expense.amount, 0);
    
    // Count active projects (projects with expenses)
    const activeProjects = new Set(allExpenses.map(expense => expense.projectId)).size;
    
    // Calculate average expense
    const avgExpense = allExpenses.length > 0 ? Math.floor(totalExpenses / allExpenses.length) : 0;
    
    document.getElementById('total-expenses-amount').textContent = `LKR ${totalExpenses.toLocaleString()}`;
    document.getElementById('monthly-expenses').textContent = `LKR ${monthlyExpenses.toLocaleString()}`;
    document.getElementById('active-projects-expenses').textContent = activeProjects.toString();
    document.getElementById('avg-expense').textContent = `LKR ${avgExpense.toLocaleString()}`;
}

// Populate project filter dropdown
function populateExpenseProjectFilters() {
    const projectFilter = document.getElementById('expense-project-filter');
    const projects = [...new Set(allExpenses.map(expense => ({ 
        id: expense.projectId, 
        name: expense.projectName 
    })))];
    
    // Clear existing options except "All Projects"
    projectFilter.innerHTML = '<option value="all">All Projects</option>';
    
    projects.forEach(project => {
        const option = document.createElement('option');
        option.value = project.id;
        option.textContent = project.name;
        projectFilter.appendChild(option);
    });
}

// Apply expense filters
function applyExpenseFilters() {
    filteredExpenses = allExpenses.filter(expense => {
        // Project filter
        if (currentExpenseFilters.project !== 'all' && expense.projectId !== currentExpenseFilters.project) {
            return false;
        }
        
        // Category filter
        if (currentExpenseFilters.category !== 'all' && expense.category !== currentExpenseFilters.category) {
            return false;
        }
        
        // Date filter
        if (currentExpenseFilters.date !== 'all') {
            const expenseDate = new Date(expense.date);
            const now = new Date();
            
            switch(currentExpenseFilters.date) {
                case 'today':
                    if (expenseDate.toDateString() !== now.toDateString()) return false;
                    break;
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (expenseDate < weekAgo) return false;
                    break;
                case 'month':
                    if (expenseDate.getMonth() !== now.getMonth() || expenseDate.getFullYear() !== now.getFullYear()) return false;
                    break;
                case 'quarter':
                    const quarterAgo = new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000);
                    if (expenseDate < quarterAgo) return false;
                    break;
            }
        }
        
        // Search filter
        if (currentExpenseFilters.search) {
            const searchTerm = currentExpenseFilters.search.toLowerCase();
            const searchableText = `${expense.id} ${expense.projectName} ${expense.category} ${expense.description}`.toLowerCase();
            if (!searchableText.includes(searchTerm)) {
                return false;
            }
        }
        
        return true;
    });
    
    sortExpenseData();
    
    // Reset to first page
    currentExpensePage = 1;
    
    // Update display
    displayExpenses();
    updateExpensePagination();
    updateExpenseResultsCount();
}

// Update filter handlers
document.getElementById('expense-project-filter').addEventListener('change', function() {
    currentExpenseFilters.project = this.value;
    applyExpenseFilters();
});

document.getElementById('expense-category-filter').addEventListener('change', function() {
    currentExpenseFilters.category = this.value;
    applyExpenseFilters();
});

document.getElementById('expense-date-filter').addEventListener('change', function() {
    currentExpenseFilters.date = this.value;
    applyExpenseFilters();
});

document.getElementById('expense-search').addEventListener('input', function() {
    currentExpenseFilters.search = this.value;
    applyExpenseFilters();
});

// Sort expenses
function sortExpenses(field) {
    if (currentExpenseSort.field === field) {
        currentExpenseSort.direction = currentExpenseSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentExpenseSort.field = field;
        currentExpenseSort.direction = 'desc';
    }
    
    applyExpenseFilters();
}

function sortExpenseData() {
    filteredExpenses.sort((a, b) => {
        let aVal = a[currentExpenseSort.field];
        let bVal = b[currentExpenseSort.field];
        
        if (currentExpenseSort.field === 'amount') {
            aVal = parseFloat(aVal);
            bVal = parseFloat(bVal);
        } else if (currentExpenseSort.field === 'date') {
            aVal = new Date(aVal);
            bVal = new Date(bVal);
        } else {
            aVal = aVal ? aVal.toString().toLowerCase() : '';
            bVal = bVal ? bVal.toString().toLowerCase() : '';
        }
        
        if (aVal < bVal) {
            return currentExpenseSort.direction === 'asc' ? -1 : 1;
        } else if (aVal > bVal) {
            return currentExpenseSort.direction === 'asc' ? 1 : -1;
        }
        return 0;
    });
}

// Display expenses
function displayExpenses() {
    const tableBody = document.getElementById('expense-table-body');
    const startIndex = (currentExpensePage - 1) * expensePageSize;
    const endIndex = startIndex + expensePageSize;
    const pageExpenses = filteredExpenses.slice(startIndex, endIndex);
    
    if (pageExpenses.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <div>No expenses found</div>
                    <small>Try adjusting your filters or add some expenses</small>
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = pageExpenses.map(expense => `
        <tr onclick="editExpense('${expense.id}')">
            <td>${formatDate(expense.date)}</td>
            <td>
                <span class="expense-project-badge">${expense.projectName}</span>
            </td>
            <td>
                <span class="expense-category-badge ${expense.category}">${expense.category}</span>
            </td>
            <td>${expense.description}</td>
            <td class="expense-amount">-LKR ${expense.amount.toLocaleString()}</td>
            <td>
                <div class="expense-actions">
                    <button class="btn secondary" onclick="event.stopPropagation(); editExpense('${expense.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn danger" onclick="event.stopPropagation(); deleteExpense('${expense.id}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Pagination functions
function updateExpensePagination() {
    const totalPages = Math.ceil(filteredExpenses.length / expensePageSize);
    const pageNumbers = document.getElementById('expense-page-numbers');
    
    pageNumbers.innerHTML = '';
    
    for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = `page-btn ${i === currentExpensePage ? 'active' : ''}`;
        pageBtn.textContent = i;
        pageBtn.onclick = () => goToExpensePage(i);
        pageNumbers.appendChild(pageBtn);
    }
    
    // Update navigation buttons
    const prevBtn = document.querySelector('button[onclick="previousExpensePage()"]');
    const nextBtn = document.querySelector('button[onclick="nextExpensePage()"]');
    
    if (prevBtn) prevBtn.disabled = currentExpensePage === 1;
    if (nextBtn) nextBtn.disabled = currentExpensePage === totalPages;
}

function updateExpenseResultsCount() {
    const startIndex = (currentExpensePage - 1) * expensePageSize + 1;
    const endIndex = Math.min(currentExpensePage * expensePageSize, filteredExpenses.length);
    
    document.getElementById('expense-results-count').textContent = 
        `Showing ${startIndex}-${endIndex} of ${filteredExpenses.length} expenses`;
}

function goToExpensePage(page) {
    currentExpensePage = page;
    displayExpenses();
    updateExpensePagination();
    updateExpenseResultsCount();
}

function previousExpensePage() {
    if (currentExpensePage > 1) {
        goToExpensePage(currentExpensePage - 1);
    }
}

function nextExpensePage() {
    const totalPages = Math.ceil(filteredExpenses.length / expensePageSize);
    if (currentExpensePage < totalPages) {
        goToExpensePage(currentExpensePage + 1);
    }
}

// Modal functions
function openExpenseModal() {
    document.getElementById('expense-modal').style.display = 'flex';
    // Set default date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expense-date').value = today;
}

function closeExpenseModal() {
    document.getElementById('expense-modal').style.display = 'none';
    document.getElementById('expense-form').reset();
}

function saveExpense() {
    const project = document.getElementById('expense-project').value;
    const category = document.getElementById('expense-category').value;
    const amount = parseFloat(document.getElementById('expense-amount').value);
    const date = document.getElementById('expense-date').value;
    const description = document.getElementById('expense-description').value;
    
    if (!project || !category || !amount || !date || !description) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Get project name from the dropdown
    const projectSelect = document.getElementById('expense-project');
    const projectName = projectSelect.options[projectSelect.selectedIndex].text;
    
    const newExpense = {
        id: `EXP-2025-${String(allExpenses.length + 1).padStart(3, '0')}`,
        date: date,
        projectId: project,
        projectName: projectName,
        category: category,
        description: description,
        amount: amount,
        receipt: null
    };
    
    allExpenses.unshift(newExpense);
    updateExpenseSummaryStats();
    applyExpenseFilters();
    populateExpenseProjectFilters();
    closeExpenseModal();
    
    // Show success message
    alert('Expense added successfully!');
}

function editExpense(expenseId) {
    const expense = allExpenses.find(exp => exp.id === expenseId);
    if (!expense) return;
    
    editingExpenseId = expenseId;
    
    // Populate edit form
    document.getElementById('edit-expense-project').value = expense.projectId;
    document.getElementById('edit-expense-category').value = expense.category;
    document.getElementById('edit-expense-amount').value = expense.amount;
    document.getElementById('edit-expense-date').value = expense.date;
    document.getElementById('edit-expense-description').value = expense.description;
    
    document.getElementById('edit-expense-modal').style.display = 'flex';
}

function closeEditExpenseModal() {
    document.getElementById('edit-expense-modal').style.display = 'none';
    document.getElementById('edit-expense-form').reset();
    editingExpenseId = null;
}

function updateExpense() {
    if (!editingExpenseId) return;
    
    const project = document.getElementById('edit-expense-project').value;
    const category = document.getElementById('edit-expense-category').value;
    const amount = parseFloat(document.getElementById('edit-expense-amount').value);
    const date = document.getElementById('edit-expense-date').value;
    const description = document.getElementById('edit-expense-description').value;
    
    if (!project || !category || !amount || !date || !description) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Get project name from the dropdown
    const projectSelect = document.getElementById('edit-expense-project');
    const projectName = projectSelect.options[projectSelect.selectedIndex].text;
    
    const expenseIndex = allExpenses.findIndex(exp => exp.id === editingExpenseId);
    if (expenseIndex !== -1) {
        allExpenses[expenseIndex] = {
            ...allExpenses[expenseIndex],
            projectId: project,
            projectName: projectName,
            category: category,
            amount: amount,
            date: date,
            description: description
        };
        
        updateExpenseSummaryStats();
        applyExpenseFilters();
        populateExpenseProjectFilters();
        closeEditExpenseModal();
        
        alert('Expense updated successfully!');
    }
}

function deleteExpense(expenseId) {
    if (confirm('Are you sure you want to delete this expense?')) {
        const expenseIndex = allExpenses.findIndex(exp => exp.id === expenseId);
        if (expenseIndex !== -1) {
            allExpenses.splice(expenseIndex, 1);
            updateExpenseSummaryStats();
            applyExpenseFilters();
            populateExpenseProjectFilters();
            alert('Expense deleted successfully!');
        }
    }
}

function exportExpenses() {
    // Simple CSV export
    const csvContent = "data:text/csv;charset=utf-8," 
        + "Date,Project,Category,Description,Amount\n"
        + filteredExpenses.map(expense => 
            `${expense.date},${expense.projectName},${expense.category},"${expense.description}",${expense.amount}`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "expenses.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Utility function
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}
