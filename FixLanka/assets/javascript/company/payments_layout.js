/* ====================================
   Side-by-Side Layout JavaScript
   ==================================== */

// Global variables
let allIncomePayments = [];
let allExpenses = [];
let filteredIncomePayments = [];
let filteredExpenses = [];
let currentIncomeFilters = {
    status: 'all',
    project: 'all',
    amount: 'all',
    search: ''
};
let currentExpenseFilters = {
    project: 'all',
    category: 'all',
    date: 'all',
    search: ''
};
let editingExpenseId = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadIncomeData();
    loadExpenseData();
    setupEventListeners();
    updateSummaryStatistics();
});

// Load income payment data
function loadIncomeData() {
    allIncomePayments = [
        {
            id: 'PAY-2025-001',
            date: '2025-09-10',
            projectName: 'AC Repair - Colombo Office',
            projectId: 'PRJ-2025-045',
            clientName: 'Tech Solutions Ltd',
            amount: 25000,
            status: 'completed',
            invoice: {
                number: 'INV-2025-001',
                issueDate: '2025-09-08',
                dueDate: '2025-09-22',
                clientAddress: '123 Business District, Colombo 03',
                clientPhone: '+94 11 234 5678',
                clientEmail: 'accounts@techsolutions.lk',
                services: [
                    { description: 'AC Unit Repair', quantity: 1, rate: 15000, amount: 15000 },
                    { description: 'Replacement Parts', quantity: 1, rate: 8000, amount: 8000 },
                    { description: 'Labor Charges', quantity: 4, rate: 500, amount: 2000 }
                ],
                subtotal: 25000,
                tax: 0,
                total: 25000
            }
        },
        {
            id: 'PAY-2025-002',
            date: '2025-09-09',
            projectName: 'Plumbing Repair - Kandy Branch',
            projectId: 'PRJ-2025-042',
            clientName: 'Green Valley Hotels',
            amount: 35000,
            status: 'completed',
            invoice: {
                number: 'INV-2025-002',
                issueDate: '2025-09-07',
                dueDate: '2025-09-21',
                clientAddress: 'Green Valley Road, Kandy',
                clientPhone: '+94 81 234 5678',
                clientEmail: 'maintenance@greenvalley.lk',
                services: [
                    { description: 'Pipe Replacement', quantity: 1, rate: 20000, amount: 20000 },
                    { description: 'Fittings and Joints', quantity: 1, rate: 10000, amount: 10000 },
                    { description: 'Labor Charges', quantity: 10, rate: 500, amount: 5000 }
                ],
                subtotal: 35000,
                tax: 0,
                total: 35000
            }
        },
        {
            id: 'PAY-2025-003',
            date: '2025-09-08',
            projectName: 'Electrical Wiring - Galle Factory',
            projectId: 'PRJ-2025-041',
            clientName: 'Industrial Motors Pvt Ltd',
            amount: 45000,
            status: 'pending',
            invoice: {
                number: 'INV-2025-003',
                issueDate: '2025-09-06',
                dueDate: '2025-09-20',
                clientAddress: 'Industrial Zone, Galle',
                clientPhone: '+94 91 234 5678',
                clientEmail: 'procurement@industrialmotors.lk',
                services: [
                    { description: 'Electrical Wiring Installation', quantity: 1, rate: 30000, amount: 30000 },
                    { description: 'Electrical Components', quantity: 1, rate: 12000, amount: 12000 },
                    { description: 'Testing and Certification', quantity: 1, rate: 3000, amount: 3000 }
                ],
                subtotal: 45000,
                tax: 0,
                total: 45000
            }
        },
        {
            id: 'PAY-2025-004',
            date: '2025-09-07',
            projectName: 'Roof Repair - Negombo Villa',
            projectId: 'PRJ-2025-038',
            clientName: 'Mr. Perera',
            amount: 18000,
            status: 'completed',
            invoice: {
                number: 'INV-2025-004',
                issueDate: '2025-09-05',
                dueDate: '2025-09-19',
                clientAddress: 'Villa Lane, Negombo',
                clientPhone: '+94 31 234 5678',
                clientEmail: 'perera.villa@gmail.com',
                services: [
                    { description: 'Roof Tile Replacement', quantity: 1, rate: 12000, amount: 12000 },
                    { description: 'Waterproofing', quantity: 1, rate: 4000, amount: 4000 },
                    { description: 'Labor Charges', quantity: 4, rate: 500, amount: 2000 }
                ],
                subtotal: 18000,
                tax: 0,
                total: 18000
            }
        },
        {
            id: 'PAY-2025-005',
            date: '2025-09-06',
            projectName: 'Generator Maintenance - Hospital',
            projectId: 'PRJ-2025-037',
            clientName: 'Colombo General Hospital',
            amount: 55000,
            status: 'completed',
            invoice: {
                number: 'INV-2025-005',
                issueDate: '2025-09-04',
                dueDate: '2025-09-18',
                clientAddress: 'Regent Street, Colombo 08',
                clientPhone: '+94 11 269 1111',
                clientEmail: 'maintenance@cgh.health.lk',
                services: [
                    { description: 'Generator Overhaul', quantity: 1, rate: 35000, amount: 35000 },
                    { description: 'Replacement Parts', quantity: 1, rate: 15000, amount: 15000 },
                    { description: 'Testing and Calibration', quantity: 1, rate: 5000, amount: 5000 }
                ],
                subtotal: 55000,
                tax: 0,
                total: 55000
            }
        },
        {
            id: 'PAY-2025-006',
            date: '2025-09-05',
            projectName: 'HVAC Installation - Shopping Mall',
            projectId: 'PRJ-2025-035',
            clientName: 'Metro Mall Pvt Ltd',
            amount: 125000,
            status: 'completed',
            invoice: {
                number: 'INV-2025-006',
                issueDate: '2025-09-03',
                dueDate: '2025-09-17',
                clientAddress: 'Metro Complex, Colombo 04',
                clientPhone: '+94 11 567 8900',
                clientEmail: 'operations@metromall.lk',
                services: [
                    { description: 'HVAC System Installation', quantity: 1, rate: 80000, amount: 80000 },
                    { description: 'Ductwork and Fittings', quantity: 1, rate: 25000, amount: 25000 },
                    { description: 'System Testing and Commissioning', quantity: 1, rate: 20000, amount: 20000 }
                ],
                subtotal: 125000,
                tax: 0,
                total: 125000
            }
        }
    ];
    
    populateIncomeProjectFilters();
    applyIncomeFilters();
    updateSummaryStatistics();
}

// Load expense data
function loadExpenseData() {
    allExpenses = [
        {
            id: 'EXP-2025-001',
            date: '2025-09-09',
            projectId: 'PRJ-2025-045',
            projectName: 'AC Repair - Colombo Office',
            category: 'materials',
            description: 'AC refrigerant gas and filters',
            amount: 8500
        },
        {
            id: 'EXP-2025-002',
            date: '2025-09-08',
            projectId: 'PRJ-2025-042',
            projectName: 'Plumbing Repair - Kandy Branch',
            category: 'materials',
            description: 'PVC pipes and fittings',
            amount: 12000
        },
        {
            id: 'EXP-2025-003',
            date: '2025-09-07',
            projectId: 'PRJ-2025-041',
            projectName: 'Electrical Wiring - Galle Factory',
            category: 'materials',
            description: 'Electrical cables and switches',
            amount: 18500
        },
        {
            id: 'EXP-2025-004',
            date: '2025-09-06',
            projectId: 'PRJ-2025-037',
            projectName: 'Generator Maintenance - Hospital',
            category: 'labor',
            description: 'Technician labor costs',
            amount: 15000
        },
        {
            id: 'EXP-2025-005',
            date: '2025-09-05',
            projectId: 'PRJ-2025-035',
            projectName: 'HVAC Installation - Shopping Mall',
            category: 'equipment',
            description: 'HVAC installation tools rental',
            amount: 25000
        }
    ];
    
    populateExpenseProjectFilters();
    applyExpenseFilters();
    updateSummaryStatistics();
}

// Setup event listeners
function setupEventListeners() {
    // Set default date to today for expense form
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expense-date').value = today;
}

// Update summary statistics
function updateSummaryStatistics() {
    // Calculate totals
    const totalIncome = allIncomePayments
        .filter(payment => payment.status === 'completed')
        .reduce((sum, payment) => sum + payment.amount, 0);
    
    const totalExpenses = allExpenses.reduce((sum, expense) => sum + expense.amount, 0);
    
    const netProfit = totalIncome - totalExpenses;
    
    const pendingPayments = allIncomePayments
        .filter(payment => payment.status === 'pending')
        .reduce((sum, payment) => sum + payment.amount, 0);
    
    const totalTransactions = allIncomePayments.length + allExpenses.length;
    
    const avgPayment = allIncomePayments.length > 0 ? 
        Math.round(totalIncome / allIncomePayments.filter(p => p.status === 'completed').length) : 0;
    
    const profitMargin = totalIncome > 0 ? Math.round((netProfit / totalIncome) * 100) : 0;
    const expenseRatio = totalIncome > 0 ? Math.round((totalExpenses / totalIncome) * 100) : 0;
    
    // Update display
    document.getElementById('total-income-display').textContent = `LKR ${totalIncome.toLocaleString()}`;
    document.getElementById('total-expenses-display').textContent = `LKR ${totalExpenses.toLocaleString()}`;
    document.getElementById('net-profit-display').textContent = `LKR ${netProfit.toLocaleString()}`;
    document.getElementById('pending-payments-display').textContent = `LKR ${pendingPayments.toLocaleString()}`;
    document.getElementById('total-transactions-display').textContent = totalTransactions.toString();
    document.getElementById('avg-payment-display').textContent = `LKR ${avgPayment.toLocaleString()}`;
    document.getElementById('profit-margin-display').textContent = `${profitMargin}%`;
    document.getElementById('expense-ratio-display').textContent = `${expenseRatio}%`;
    
    // Update profit display color based on value
    const netProfitElement = document.getElementById('net-profit-display');
    if (netProfit < 0) {
        netProfitElement.style.color = '#ef4444';
    } else {
        netProfitElement.style.color = '#10b981';
    }
}

// Income filter functions
function populateIncomeProjectFilters() {
    const projectFilter = document.getElementById('income-project-filter');
    const projects = [...new Set(allIncomePayments.map(payment => ({ 
        id: payment.projectId, 
        name: payment.projectName 
    })))];
    
    projectFilter.innerHTML = '<option value="all">All Projects</option>';
    projects.forEach(project => {
        const option = document.createElement('option');
        option.value = project.id;
        option.textContent = project.name;
        projectFilter.appendChild(option);
    });
}

function applyIncomeFilters() {
    filteredIncomePayments = allIncomePayments.filter(payment => {
        // Status filter
        if (currentIncomeFilters.status !== 'all' && payment.status !== currentIncomeFilters.status) {
            return false;
        }
        
        // Project filter
        if (currentIncomeFilters.project !== 'all' && payment.projectId !== currentIncomeFilters.project) {
            return false;
        }
        
        // Amount filter
        if (currentIncomeFilters.amount !== 'all') {
            const amount = payment.amount;
            switch(currentIncomeFilters.amount) {
                case '0-5000':
                    if (amount > 5000) return false;
                    break;
                case '5000-15000':
                    if (amount <= 5000 || amount > 15000) return false;
                    break;
                case '15000-30000':
                    if (amount <= 15000 || amount > 30000) return false;
                    break;
                case '30000+':
                    if (amount <= 30000) return false;
                    break;
            }
        }
        
        // Search filter
        if (currentIncomeFilters.search) {
            const searchTerm = currentIncomeFilters.search.toLowerCase();
            const searchableText = `${payment.id} ${payment.projectName} ${payment.clientName}`.toLowerCase();
            if (!searchableText.includes(searchTerm)) {
                return false;
            }
        }
        
        return true;
    });
    
    displayIncomePayments();
}

function displayIncomePayments() {
    const tableBody = document.getElementById('income-table-body');
    
    if (filteredIncomePayments.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-credit-card" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <div>No income payments found</div>
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = filteredIncomePayments.map(payment => `
        <tr onclick="viewInvoice('${payment.id}')" style="cursor: pointer;" title="Click to view invoice">
            <td>${formatDate(payment.date)}</td>
            <td>${payment.projectName}</td>
            <td>${payment.clientName}</td>
            <td class="amount-positive">+LKR ${payment.amount.toLocaleString()}</td>
            <td><span class="status-badge status-${payment.status}">${payment.status}</span></td>
        </tr>
    `).join('');
}

// Expense filter functions
function populateExpenseProjectFilters() {
    const projectFilter = document.getElementById('expense-project-filter');
    const projects = [...new Set(allExpenses.map(expense => ({ 
        id: expense.projectId, 
        name: expense.projectName 
    })))];
    
    projectFilter.innerHTML = '<option value="all">All Projects</option>';
    projects.forEach(project => {
        const option = document.createElement('option');
        option.value = project.id;
        option.textContent = project.name;
        projectFilter.appendChild(option);
    });
}

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
    
    displayExpenses();
}

function displayExpenses() {
    const tableBody = document.getElementById('expense-table-body');
    
    if (filteredExpenses.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                    <div>No expenses found</div>
                    <small>Try adjusting your filters or add some expenses</small>
                </td>
            </tr>
        `;
        return;
    }
    
    tableBody.innerHTML = filteredExpenses.map(expense => `
        <tr>
            <td>${formatDate(expense.date)}</td>
            <td>${expense.projectName}</td>
            <td><span class="category-badge category-${expense.category}">${expense.category}</span></td>
            <td>${expense.description}</td>
            <td class="amount-negative">-LKR ${expense.amount.toLocaleString()}</td>
            <td>
                <button class="btn-sm" onclick="editExpense('${expense.id}')" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-sm danger" onclick="deleteExpense('${expense.id}')" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

// Filter event handlers
document.getElementById('income-status-filter').addEventListener('change', function() {
    currentIncomeFilters.status = this.value;
    applyIncomeFilters();
});

document.getElementById('income-project-filter').addEventListener('change', function() {
    currentIncomeFilters.project = this.value;
    applyIncomeFilters();
});

document.getElementById('income-amount-filter').addEventListener('change', function() {
    currentIncomeFilters.amount = this.value;
    applyIncomeFilters();
});

document.getElementById('income-search').addEventListener('input', function() {
    currentIncomeFilters.search = this.value;
    applyIncomeFilters();
});

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

// Reset filters
function resetIncomeFilters() {
    currentIncomeFilters = {
        status: 'all',
        project: 'all',
        amount: 'all',
        search: ''
    };
    
    document.getElementById('income-status-filter').value = 'all';
    document.getElementById('income-project-filter').value = 'all';
    document.getElementById('income-amount-filter').value = 'all';
    document.getElementById('income-search').value = '';
    
    applyIncomeFilters();
}

function resetExpenseFilters() {
    currentExpenseFilters = {
        project: 'all',
        category: 'all',
        date: 'all',
        search: ''
    };
    
    document.getElementById('expense-project-filter').value = 'all';
    document.getElementById('expense-category-filter').value = 'all';
    document.getElementById('expense-date-filter').value = 'all';
    document.getElementById('expense-search').value = '';
    
    applyExpenseFilters();
}

// Modal functions
function openExpenseModal() {
    document.getElementById('expense-modal').style.display = 'flex';
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
    
    const projectSelect = document.getElementById('expense-project');
    const projectName = projectSelect.options[projectSelect.selectedIndex].text;
    
    const newExpense = {
        id: `EXP-2025-${String(allExpenses.length + 1).padStart(3, '0')}`,
        date: date,
        projectId: project,
        projectName: projectName,
        category: category,
        description: description,
        amount: amount
    };
    
    allExpenses.unshift(newExpense);
    populateExpenseProjectFilters();
    applyExpenseFilters();
    updateSummaryStatistics();
    closeExpenseModal();
    alert('Expense added successfully!');
}

function editExpense(expenseId) {
    const expense = allExpenses.find(exp => exp.id === expenseId);
    if (!expense) return;
    
    editingExpenseId = expenseId;
    
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
        
        populateExpenseProjectFilters();
        applyExpenseFilters();
        updateSummaryStatistics();
        closeEditExpenseModal();
        alert('Expense updated successfully!');
    }
}

function deleteExpense(expenseId) {
    if (confirm('Are you sure you want to delete this expense?')) {
        const expenseIndex = allExpenses.findIndex(exp => exp.id === expenseId);
        if (expenseIndex !== -1) {
            allExpenses.splice(expenseIndex, 1);
            populateExpenseProjectFilters();
            applyExpenseFilters();
            updateSummaryStatistics();
            alert('Expense deleted successfully!');
        }
    }
}

// Sorting functions (stubs)
function sortIncomeTable(field) {
    
}

function sortExpenseTable(field) {
    
}

// Export functions (stubs)
function exportReport() {
    alert('Exporting report...');
}

function refreshData() {
    loadIncomeData();
    loadExpenseData();
    updateSummaryStatistics();
    alert('Data refreshed!');
}

function updatePeriod() {
    const period = document.getElementById('period-select').value;
    
}

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

// Invoice viewing functions
function viewInvoice(paymentId) {
    const payment = allIncomePayments.find(p => p.id === paymentId);
    if (!payment || !payment.invoice) {
        alert('Invoice data not found');
        return;
    }
    
    const invoice = payment.invoice;
    const invoiceContent = document.getElementById('invoice-content');
    
    invoiceContent.innerHTML = `
        <div class="invoice-wrapper">
            <!-- Invoice Header -->
            <div class="invoice-header-section">
                <div class="company-info">
                    <div class="company-logo">FL</div>
                    <div class="company-details">
                        <h1>FixLanka Solutions</h1>
                        <p>Professional Repair & Maintenance Services</p>
                        <p>123 Main Street, Colombo 01, Sri Lanka</p>
                        <p>Phone: +94 11 123 4567 | Email: info@fixlanka.lk</p>
                        <p>Registration No: PV 12345 | VAT No: 123456789</p>
                    </div>
                </div>
                <div class="invoice-title">
                    <h2>INVOICE</h2>
                    <div class="invoice-number-display">#${invoice.number}</div>
                </div>
            </div>
            
            <!-- Invoice Addresses & Details -->
            <div class="invoice-addresses">
                <div class="address-block">
                    <h4>Bill To:</h4>
                    <div class="address-content">
                        <p class="company-name">${payment.clientName}</p>
                        <p>${invoice.clientAddress}</p>
                        <p>Phone: ${invoice.clientPhone}</p>
                        <p>Email: ${invoice.clientEmail}</p>
                    </div>
                </div>
                
                <div class="address-block">
                    <h4>Project Details:</h4>
                    <div class="address-content">
                        <p><strong>Project:</strong> ${payment.projectName}</p>
                        <p><strong>Project ID:</strong> ${payment.projectId}</p>
                        <p><strong>Payment Status:</strong></p>
                        <span class="payment-status ${payment.status}">
                            <i class="fas fa-${payment.status === 'completed' ? 'check-circle' : 'clock'}"></i>
                            ${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}
                        </span>
                    </div>
                </div>
                
                <div class="invoice-meta">
                    <table class="invoice-meta-table">
                        <tr>
                            <td class="label">Invoice Date:</td>
                            <td class="value">${formatDate(invoice.issueDate)}</td>
                        </tr>
                        <tr>
                            <td class="label">Due Date:</td>
                            <td class="value">${formatDate(invoice.dueDate)}</td>
                        </tr>
                        <tr>
                            <td class="label">Payment Date:</td>
                            <td class="value">${payment.status === 'completed' ? formatDate(payment.date) : 'Pending'}</td>
                        </tr>
                        <tr>
                            <td class="label">Payment Method:</td>
                            <td class="value">${payment.status === 'completed' ? 'Bank Transfer' : 'N/A'}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Services Section -->
            <div class="invoice-services">
                <h4>Description of Services</h4>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Unit Price (LKR)</th>
                            <th class="text-right">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${invoice.services.map(service => `
                            <tr>
                                <td>${service.description}</td>
                                <td class="text-center">${service.quantity}</td>
                                <td class="text-right">${service.rate.toLocaleString()}</td>
                                <td class="text-right">${service.amount.toLocaleString()}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            
            <!-- Invoice Totals -->
            <div class="invoice-totals">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="value">LKR ${invoice.subtotal.toLocaleString()}</td>
                    </tr>
                    <tr>
                        <td class="label">Tax (VAT):</td>
                        <td class="value">LKR ${invoice.tax.toLocaleString()}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">TOTAL AMOUNT:</td>
                        <td class="value">LKR ${invoice.total.toLocaleString()}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Invoice Footer -->
            <div class="invoice-footer">
                <h5>Thank You For Your Business!</h5>
                <p>We appreciate your trust in FixLanka Solutions for your repair and maintenance needs.</p>
                
                <div class="footer-grid">
                    <div class="footer-section">
                        <h6>Payment Terms:</h6>
                        <p>â€¢ Payment is due within 14 days of invoice date</p>
                        <p>â€¢ Late payments may incur additional charges</p>
                        <p>â€¢ Please reference invoice number in payment</p>
                    </div>
                    <div class="footer-section">
                        <h6>Contact Information:</h6>
                        <p>For questions about this invoice:</p>
                        <p>Email: billing@fixlanka.lk</p>
                        <p>Phone: +94 11 123 4567 (Ext. 123)</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('invoice-modal').style.display = 'flex';
}

function closeInvoiceModal() {
    document.getElementById('invoice-modal').style.display = 'none';
}

function printInvoice() {
    window.print();
}

function downloadInvoice() {
    // This would typically integrate with a PDF generation service
    alert('PDF download functionality would be implemented here. This would generate a PDF version of the invoice for download.');
}
