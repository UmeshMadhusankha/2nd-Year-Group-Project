/* ====================================
   Payments Page - Backend Connected
   ==================================== */

// Global variables
let allIncomePayments = [];
let allExpenses = [];
let allProjects = [];
let filteredIncomePayments = [];
let filteredExpenses = [];
let currentIncomeFilters = {
    status: 'all',
    project: 'all',
    amount: 'all'
};
let currentExpenseFilters = {
    project: 'all',
    category: 'all',
    date: 'all'
};
let editingExpenseId = null;

// API base URL
const API_BASE = '/2nd-Year-Group-Project/FixLanka/api';

// Initialize page
document.addEventListener('DOMContentLoaded', function () {
    loadPaymentData();
    setupEventListeners();
});

// Load all payment data from API
async function loadPaymentData() {
    try {
        showLoading();

        const period = document.getElementById('period-select')?.value || 'month';
        const response = await fetch(`${API_BASE}/payments.php?period=${period}`);
        const result = await response.json();

        if (result.success) {
            allIncomePayments = result.data.income || [];
            allExpenses = result.data.expenses || [];
            allProjects = result.data.projects || [];

            // Update summary cards
            updateSummaryCards(result.data.summary);

            // Populate filter dropdowns
            populateProjectFilters();

            // Apply filters and display data
            applyIncomeFilters();
            applyExpenseFilters();
        } else {
            console.error('Failed to load payment data:', result.message);
            showEmptyState();
        }
    } catch (error) {
        console.error('Error loading payment data:', error);
        showEmptyState();
    } finally {
        hideLoading();
    }
}

// Show loading state
function showLoading() {
    const incomeBody = document.getElementById('income-table-body');
    const expenseBody = document.getElementById('expense-table-body');

    const loadingHTML = `
        <tr>
            <td colspan="6" style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #0abad5;"></i>
                <div style="margin-top: 12px;">Loading...</div>
            </td>
        </tr>
    `;

    if (incomeBody) incomeBody.innerHTML = loadingHTML;
    if (expenseBody) expenseBody.innerHTML = loadingHTML;
}

function hideLoading() {
    // Loading hidden when tables are populated
}

function showEmptyState() {
    allIncomePayments = [];
    allExpenses = [];
    updateSummaryCards({
        total_income: 0,
        total_expenses: 0,
        net_profit: 0,
        pending_payments: 0,
        total_transactions: 0,
        avg_payment: 0,
        profit_margin: 0,
        expense_ratio: 0
    });
    applyIncomeFilters();
    applyExpenseFilters();
}

// Update summary cards with API data
function updateSummaryCards(summary) {
    document.getElementById('total-income-display').textContent = `LKR ${(summary.total_income || 0).toLocaleString()}`;
    document.getElementById('total-expenses-display').textContent = `LKR ${(summary.total_expenses || 0).toLocaleString()}`;
    document.getElementById('net-profit-display').textContent = `LKR ${(summary.net_profit || 0).toLocaleString()}`;
    document.getElementById('pending-payments-display').textContent = `LKR ${(summary.pending_payments || 0).toLocaleString()}`;
    document.getElementById('total-transactions-display').textContent = (summary.total_transactions || 0).toString();
    document.getElementById('avg-payment-display').textContent = `LKR ${(summary.avg_payment || 0).toLocaleString()}`;
    document.getElementById('profit-margin-display').textContent = `${summary.profit_margin || 0}%`;
    document.getElementById('expense-ratio-display').textContent = `${summary.expense_ratio || 0}%`;

    // Color net profit based on value
    const netProfitElement = document.getElementById('net-profit-display');
    if ((summary.net_profit || 0) < 0) {
        netProfitElement.style.color = '#ef4444';
    } else {
        netProfitElement.style.color = '#10b981';
    }

    // Update pending count in trend
    const pendingTrend = document.querySelector('.summary-card:nth-child(4) .summary-card-trend');
    if (pendingTrend) {
        pendingTrend.innerHTML = `<i class="fas fa-minus"></i> ${summary.pending_count || 0} items`;
    }
}

// Populate project filter dropdowns
function populateProjectFilters() {
    const incomeFilter = document.getElementById('income-project-filter');
    const expenseFilter = document.getElementById('expense-project-filter');
    const expenseProjectSelect = document.getElementById('expense-project');
    const editExpenseProjectSelect = document.getElementById('edit-expense-project');

    const projectOptions = allProjects.map(p =>
        `<option value="${p.project_id}">${p.title}</option>`
    ).join('');

    if (incomeFilter) {
        incomeFilter.innerHTML = '<option value="all">All Projects</option>' + projectOptions;
    }
    if (expenseFilter) {
        expenseFilter.innerHTML = '<option value="all">All Projects</option>' + projectOptions;
    }
    if (expenseProjectSelect) {
        expenseProjectSelect.innerHTML = '<option value="">Select Project (Optional)</option>' + projectOptions;
    }
    if (editExpenseProjectSelect) {
        editExpenseProjectSelect.innerHTML = '<option value="">Select Project (Optional)</option>' + projectOptions;
    }
}

// Setup event listeners
function setupEventListeners() {
    const today = new Date().toISOString().split('T')[0];
    const expenseDate = document.getElementById('expense-date');
    if (expenseDate) expenseDate.value = today;
}

// Income filter functions
function applyIncomeFilters() {
    filteredIncomePayments = allIncomePayments.filter(payment => {
        // Status filter
        if (currentIncomeFilters.status !== 'all' && payment.status !== currentIncomeFilters.status) {
            return false;
        }
        // Project filter
        if (currentIncomeFilters.project !== 'all' && payment.project_id != currentIncomeFilters.project) {
            return false;
        }
        // Amount filter
        if (currentIncomeFilters.amount !== 'all') {
            const amount = payment.amount;
            switch (currentIncomeFilters.amount) {
                case '0-5000': if (amount > 5000) return false; break;
                case '5000-15000': if (amount <= 5000 || amount > 15000) return false; break;
                case '15000-30000': if (amount <= 15000 || amount > 30000) return false; break;
                case '30000+': if (amount <= 30000) return false; break;
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
                    <small>Payments from completed milestones will appear here</small>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = filteredIncomePayments.map(payment => `
        <tr onclick="viewInvoice('${payment.id}')" style="cursor: pointer;" title="Click to view details">
            <td>${formatDate(payment.date)}</td>
            <td>${payment.project_name}</td>
            <td>${payment.client_name}</td>
            <td class="amount-positive">+LKR ${payment.amount.toLocaleString()}</td>
            <td><span class="status-badge status-${payment.status}">${payment.status}</span></td>
        </tr>
    `).join('');
}

// Expense filter functions
function applyExpenseFilters() {
    filteredExpenses = allExpenses.filter(expense => {
        // Project filter
        if (currentExpenseFilters.project !== 'all' && expense.project_id != currentExpenseFilters.project) {
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
            switch (currentExpenseFilters.date) {
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
                    <small>Click "Add Expense" to track your project costs</small>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = filteredExpenses.map(expense => `
        <tr>
            <td>${formatDate(expense.date)}</td>
            <td>${expense.project_name}</td>
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

// Filter change handlers
document.getElementById('income-status-filter')?.addEventListener('change', function () {
    currentIncomeFilters.status = this.value;
    applyIncomeFilters();
});

document.getElementById('income-project-filter')?.addEventListener('change', function () {
    currentIncomeFilters.project = this.value;
    applyIncomeFilters();
});

document.getElementById('income-amount-filter')?.addEventListener('change', function () {
    currentIncomeFilters.amount = this.value;
    applyIncomeFilters();
});

document.getElementById('expense-project-filter')?.addEventListener('change', function () {
    currentExpenseFilters.project = this.value;
    applyExpenseFilters();
});

document.getElementById('expense-category-filter')?.addEventListener('change', function () {
    currentExpenseFilters.category = this.value;
    applyExpenseFilters();
});

document.getElementById('expense-date-filter')?.addEventListener('change', function () {
    currentExpenseFilters.date = this.value;
    applyExpenseFilters();
});

// Reset filters
function resetIncomeFilters() {
    currentIncomeFilters = { status: 'all', project: 'all', amount: 'all' };
    document.getElementById('income-status-filter').value = 'all';
    document.getElementById('income-project-filter').value = 'all';
    document.getElementById('income-amount-filter').value = 'all';
    applyIncomeFilters();
}

function resetExpenseFilters() {
    currentExpenseFilters = { project: 'all', category: 'all', date: 'all' };
    document.getElementById('expense-project-filter').value = 'all';
    document.getElementById('expense-category-filter').value = 'all';
    document.getElementById('expense-date-filter').value = 'all';
    applyExpenseFilters();
}

// Period change
function updatePeriod() {
    loadPaymentData();
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

async function saveExpense() {
    const projectId = document.getElementById('expense-project').value || null;
    const category = document.getElementById('expense-category').value;
    const amount = parseFloat(document.getElementById('expense-amount').value);
    const date = document.getElementById('expense-date').value;
    const description = document.getElementById('expense-description').value;

    if (!category || !amount || !date || !description) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/payments.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                project_id: projectId,
                category: category,
                amount: amount,
                expense_date: date,
                description: description
            })
        });

        const result = await response.json();

        if (result.success) {
            closeExpenseModal();
            loadPaymentData();
            alert('Expense added successfully!');
        } else {
            alert('Failed to add expense: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error saving expense:', error);
        alert('Error saving expense. Please try again.');
    }
}

function editExpense(expenseId) {
    const expense = allExpenses.find(exp => exp.id === expenseId);
    if (!expense) return;

    editingExpenseId = expenseId;

    document.getElementById('edit-expense-project').value = expense.project_id || '';
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

async function updateExpense() {
    if (!editingExpenseId) return;

    const projectId = document.getElementById('edit-expense-project').value || null;
    const category = document.getElementById('edit-expense-category').value;
    const amount = parseFloat(document.getElementById('edit-expense-amount').value);
    const date = document.getElementById('edit-expense-date').value;
    const description = document.getElementById('edit-expense-description').value;

    if (!category || !amount || !date || !description) {
        alert('Please fill in all required fields');
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/payments.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                expense_id: editingExpenseId,
                project_id: projectId,
                category: category,
                amount: amount,
                expense_date: date,
                description: description
            })
        });

        const result = await response.json();

        if (result.success) {
            closeEditExpenseModal();
            loadPaymentData();
            alert('Expense updated successfully!');
        } else {
            alert('Failed to update expense: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error updating expense:', error);
        alert('Error updating expense. Please try again.');
    }
}

async function deleteExpense(expenseId) {
    if (!confirm('Are you sure you want to delete this expense?')) return;

    try {
        const response = await fetch(`${API_BASE}/payments.php`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ expense_id: expenseId })
        });

        const result = await response.json();

        if (result.success) {
            loadPaymentData();
            alert('Expense deleted successfully!');
        } else {
            alert('Failed to delete expense: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error deleting expense:', error);
        alert('Error deleting expense. Please try again.');
    }
}

// Sorting stubs
function sortIncomeTable(field) {
    // TODO: Implement sorting
}

function sortExpenseTable(field) {
    // TODO: Implement sorting
}

// Export/refresh
function exportReport() {
    const period = document.getElementById('period-select')?.value || 'month';
    const periodLabel = getPeriodLabel(period);
    const today = new Date().toISOString().split('T')[0];

    // Build CSV content
    let csvContent = '';

    // Header
    csvContent += 'FIXLANKA PAYMENT REPORT\n';
    csvContent += `Period: ${periodLabel}\n`;
    csvContent += `Generated: ${formatDate(today)}\n`;
    csvContent += '\n';

    // Summary section
    const totalIncome = allIncomePayments
        .filter(p => p.status === 'completed')
        .reduce((sum, p) => sum + p.amount, 0);
    const totalExpenses = allExpenses.reduce((sum, e) => sum + e.amount, 0);
    const netProfit = totalIncome - totalExpenses;
    const pendingPayments = allIncomePayments
        .filter(p => p.status === 'pending')
        .reduce((sum, p) => sum + p.amount, 0);

    csvContent += 'FINANCIAL SUMMARY\n';
    csvContent += `Total Income,LKR ${totalIncome.toLocaleString()}\n`;
    csvContent += `Total Expenses,LKR ${totalExpenses.toLocaleString()}\n`;
    csvContent += `Net Profit,LKR ${netProfit.toLocaleString()}\n`;
    csvContent += `Pending Payments,LKR ${pendingPayments.toLocaleString()}\n`;
    csvContent += '\n';

    // Income section
    csvContent += 'INCOME PAYMENTS\n';
    csvContent += 'Date,Project,Client,Amount (LKR),Status\n';

    if (allIncomePayments.length === 0) {
        csvContent += 'No income payments found\n';
    } else {
        allIncomePayments.forEach(payment => {
            const escapedProject = escapeCSV(payment.project_name);
            const escapedClient = escapeCSV(payment.client_name);
            csvContent += `${formatDate(payment.date)},${escapedProject},${escapedClient},${payment.amount},${payment.status}\n`;
        });
    }

    csvContent += '\n';

    // Expenses section
    csvContent += 'EXPENSES\n';
    csvContent += 'Date,Project,Category,Description,Amount (LKR)\n';

    if (allExpenses.length === 0) {
        csvContent += 'No expenses found\n';
    } else {
        allExpenses.forEach(expense => {
            const escapedProject = escapeCSV(expense.project_name);
            const escapedDesc = escapeCSV(expense.description);
            csvContent += `${formatDate(expense.date)},${escapedProject},${expense.category},${escapedDesc},${expense.amount}\n`;
        });
    }

    // Create and download file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', `FixLanka_Payment_Report_${today}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // Show success message
    alert('Report exported successfully!');
}

// Helper: Escape CSV values
function escapeCSV(value) {
    if (!value) return '';
    const stringValue = String(value);
    if (stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n')) {
        return '"' + stringValue.replace(/"/g, '""') + '"';
    }
    return stringValue;
}

// Helper: Get period label for export
function getPeriodLabel(period) {
    const labels = {
        'today': 'Today',
        'week': 'This Week',
        'month': 'This Month',
        'quarter': 'This Quarter',
        'year': 'This Year',
        'custom': 'Custom Range'
    };
    return labels[period] || 'All Time';
}

function refreshData() {
    loadPaymentData();
}

// Utility
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

// Invoice viewing
function viewInvoice(paymentId) {
    const payment = allIncomePayments.find(p => p.id === paymentId);
    if (!payment) {
        alert('Payment details not found');
        return;
    }

    const invoiceContent = document.getElementById('invoice-content');

    invoiceContent.innerHTML = `
        <div class="invoice-wrapper">
            <div class="invoice-header-section">
                <div class="company-info">
                    <div class="company-logo">FL</div>
                    <div class="company-details">
                        <h1>FixLanka Solutions</h1>
                        <p>Professional Repair & Maintenance Services</p>
                    </div>
                </div>
                <div class="invoice-title">
                    <h2>PAYMENT RECEIPT</h2>
                    <div class="invoice-number-display">#${payment.id}</div>
                </div>
            </div>
            
            <div class="invoice-addresses">
                <div class="address-block">
                    <h4>Client:</h4>
                    <div class="address-content">
                        <p class="company-name">${payment.client_name}</p>
                    </div>
                </div>
                
                <div class="address-block">
                    <h4>Project Details:</h4>
                    <div class="address-content">
                        <p><strong>Project:</strong> ${payment.project_name}</p>
                        <p><strong>Payment Date:</strong> ${formatDate(payment.date)}</p>
                        <p><strong>Status:</strong> 
                            <span class="payment-status ${payment.status}">
                                ${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="invoice-services">
                <h4>Payment Details</h4>
                <table class="services-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Method</th>
                            <th class="text-right">Amount (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>${payment.description || 'Milestone Payment'}</td>
                            <td>${payment.payment_method || 'Bank Transfer'}</td>
                            <td class="text-right">${payment.amount.toLocaleString()}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="invoice-total-section">
                <div class="invoice-total">
                    <span class="label">Total Amount:</span>
                    <span class="value">LKR ${payment.amount.toLocaleString()}</span>
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
    alert('PDF download coming soon!');
}
