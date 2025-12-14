<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reports - FixLanka</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/payments.css">
</head>

<body>
    <div class="app-container">
        <!-- Include Sidebar -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Topbar -->
            <div id="topbar-container"></div>

            <!-- Payments Content -->
            <div class="payments-container">
                <!-- Header Section -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1>
                                    <i class="fas fa-chart-line"></i>
                                    Payment Reports
                                </h1>
                                <p class="subtitle">
                                    Income payments and expense tracking for your repair services
                                </p>
                                <nav class="breadcrumbs">
                                    <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php">
                                        <i class="fas fa-home"></i>
                                        Dashboard
                                    </a>
                                    <span class="separator">/</span>
                                    <span class="current">Payment Reports</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn secondary" onclick="exportReport()">
                                        <i class="fas fa-download"></i>
                                        Export Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Financial Summary Section -->
                <section class="summary-section">
                    <div class="summary-header">
                        <h2>
                            <i class="fas fa-analytics"></i>
                            Financial Overview
                        </h2>
                        <p>Real-time insights into your payment and expense performance</p>
                    </div>

                    <div class="summary-grid">
                        <!-- Total Revenue Card -->
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-card-title">
                                    <span class="summary-card-icon">
                                        <i class="fas fa-arrow-up"></i>
                                    </span>
                                    Total Income
                                </div>
                                <div class="summary-card-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    12.5%
                                </div>
                            </div>
                            <div class="summary-card-value" id="total-income-display">LKR 0</div>
                            <p class="summary-card-subtitle">Total payments received this period</p>
                            <div class="summary-progress">
                                <div class="summary-progress-bar" style="width: 85%"></div>
                            </div>
                        </div>

                        <!-- Total Expenses Card -->
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-card-title">
                                    <span class="summary-card-icon">
                                        <i class="fas fa-arrow-down"></i>
                                    </span>
                                    Total Expenses
                                </div>
                                <div class="summary-card-trend trend-down">
                                    <i class="fas fa-arrow-down"></i>
                                    3.2%
                                </div>
                            </div>
                            <div class="summary-card-value" id="total-expenses-display">LKR 0</div>
                            <p class="summary-card-subtitle">Total expenses for this period</p>
                            <div class="summary-progress">
                                <div class="summary-progress-bar" style="width: 65%"></div>
                            </div>
                        </div>

                        <!-- Net Profit Card -->
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-card-title">
                                    <span class="summary-card-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </span>
                                    Net Profit
                                </div>
                                <div class="summary-card-trend trend-up">
                                    <i class="fas fa-arrow-up"></i>
                                    18.7%
                                </div>
                            </div>
                            <div class="summary-card-value" id="net-profit-display">LKR 0</div>
                            <p class="summary-card-subtitle">Income minus expenses</p>
                            <div class="summary-progress">
                                <div class="summary-progress-bar" style="width: 92%"></div>
                            </div>
                        </div>

                        <!-- Pending Payments Card -->
                        <div class="summary-card">
                            <div class="summary-card-header">
                                <div class="summary-card-title">
                                    <span class="summary-card-icon">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    Pending Payments
                                </div>
                                <div class="summary-card-trend">
                                    <i class="fas fa-minus"></i>
                                    2 items
                                </div>
                            </div>
                            <div class="summary-card-value" id="pending-payments-display">LKR 0</div>
                            <p class="summary-card-subtitle">Awaiting payment confirmation</p>
                            <div class="summary-progress">
                                <div class="summary-progress-bar" style="width: 35%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="summary-details">
                        <div class="summary-detail-item">
                            <div class="summary-detail-value" id="total-transactions-display">0</div>
                            <div class="summary-detail-label">Total Transactions</div>
                        </div>
                        <div class="summary-detail-item">
                            <div class="summary-detail-value" id="avg-payment-display">LKR 0</div>
                            <div class="summary-detail-label">Avg Payment</div>
                        </div>
                        <div class="summary-detail-item">
                            <div class="summary-detail-value" id="profit-margin-display">0%</div>
                            <div class="summary-detail-label">Profit Margin</div>
                        </div>
                        <div class="summary-detail-item">
                            <div class="summary-detail-value" id="expense-ratio-display">0%</div>
                            <div class="summary-detail-label">Expense Ratio</div>
                        </div>
                    </div>
                </section>

                <!-- Main Layout Grid -->
                <div class="layout-grid">
                    <!-- Income Section (Left Side) -->
                    <div class="income-section">
                        <div class="section-header">
                            <i class="fas fa-arrow-up"></i>
                            Income Payments
                        </div>
                        <div class="section-content">
                            <!-- Income Filters -->
                            <div class="filter-section">
                                <h4>
                                    <i class="fas fa-filter"></i>
                                    Income Filters
                                </h4>
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="income-status-filter">Status</label>
                                        <select id="income-status-filter" onchange="applyIncomeFilters()">
                                            <option value="all">All Statuses</option>
                                            <option value="completed">Completed</option>
                                            <option value="pending">Pending</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="income-project-filter">Project</label>
                                        <select id="income-project-filter" onchange="applyIncomeFilters()">
                                            <option value="all">All Projects</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="income-amount-filter">Amount Range</label>
                                        <select id="income-amount-filter" onchange="applyIncomeFilters()">
                                            <option value="all">All Amounts</option>
                                            <option value="0-5000">LKR 0 - 5,000</option>
                                            <option value="5000-15000">LKR 5,000 - 15,000</option>
                                            <option value="15000-30000">LKR 15,000 - 30,000</option>
                                            <option value="30000+">LKR 30,000+</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="period-select">Time period</label>
                                        <select id="period-select" onchange="updatePeriod()">
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month" selected>This Month</option>
                                            <option value="quarter">This Quarter</option>
                                            <option value="year">This Year</option>
                                            <option value="custom">Custom Range</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button class="btn-reset" onclick="resetIncomeFilters()">Reset Filters</button>
                                </div>
                            </div>

                            <!-- Income Table -->
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th onclick="sortIncomeTable('date')">Date</th>
                                            <th onclick="sortIncomeTable('project')">Project</th>
                                            <th onclick="sortIncomeTable('client')">Client</th>
                                            <th onclick="sortIncomeTable('amount')">Amount</th>
                                            <th onclick="sortIncomeTable('status')">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="income-table-body">
                                        <!-- Income rows will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Section (Right Side) -->
                    <div class="expense-section">
                        <div class="section-header">
                            <i class="fas fa-arrow-down"></i>
                            Expenses
                        </div>
                        <div class="section-content">
                            <!-- Expense Filters -->
                            <div class="filter-section">
                                <h4>
                                    <i class="fas fa-filter"></i>
                                    Expense Filters
                                </h4>
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="expense-project-filter">Project</label>
                                        <select id="expense-project-filter" onchange="applyExpenseFilters()">
                                            <option value="all">All Projects</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="expense-category-filter">Category</label>
                                        <select id="expense-category-filter" onchange="applyExpenseFilters()">
                                            <option value="all">All Categories</option>
                                            <option value="materials">Materials</option>
                                            <option value="labor">Labor</option>
                                            <option value="transport">Transport</option>
                                            <option value="equipment">Equipment</option>
                                            <option value="permits">Permits</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="expense-date-filter">Date Range</label>
                                        <select id="expense-date-filter" onchange="applyExpenseFilters()">
                                            <option value="all">All Time</option>
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label for="expense-amount-filter">Amount Range</label>
                                        <select id="expense-amount-filter" onchange="applyExpenseFilters()">
                                            <option value="all">All Amounts</option>
                                            <option value="0-5000">LKR 0 - 5,000</option>
                                            <option value="5000-15000">LKR 5,000 - 15,000</option>
                                            <option value="15000-30000">LKR 15,000 - 30,000</option>
                                            <option value="30000+">LKR 30,000+</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-actions">
                                    <button class="btn-add" onclick="openExpenseModal()">
                                        <i class="fas fa-plus"></i>
                                        Add Expense
                                    </button>
                                    <button class="btn-reset" onclick="resetExpenseFilters()">Reset Filters</button>
                                </div>
                            </div>

                            <!-- Expense Table -->
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th onclick="sortExpenseTable('date')">Date</th>
                                            <th onclick="sortExpenseTable('project')">Project</th>
                                            <th onclick="sortExpenseTable('category')">Category</th>
                                            <th onclick="sortExpenseTable('description')">Description</th>
                                            <th onclick="sortExpenseTable('amount')">Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="expense-table-body">
                                        <!-- Expense rows will be populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Expense Modal -->
    <div id="expense-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-receipt"></i>
                    Add New Expense
                </h3>
                <button class="close-btn" onclick="closeExpenseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="expense-form">
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="expense-project">Project *</label>
                            <select id="expense-project" required>
                                <option value="">Select Project</option>
                                <option value="PRJ-2025-045">AC Repair - Colombo Office</option>
                                <option value="PRJ-2025-042">Plumbing Repair - Kandy Branch</option>
                                <option value="PRJ-2025-041">Electrical Wiring - Galle Factory</option>
                                <option value="PRJ-2025-038">Roof Repair - Negombo Villa</option>
                                <option value="PRJ-2025-037">Generator Maintenance - Hospital</option>
                                <option value="PRJ-2025-035">HVAC Installation - Shopping Mall</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="expense-category">Category *</label>
                            <select id="expense-category" required>
                                <option value="">Select Category</option>
                                <option value="materials">Materials</option>
                                <option value="labor">Labor</option>
                                <option value="transport">Transport</option>
                                <option value="equipment">Equipment</option>
                                <option value="permits">Permits & Licenses</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="expense-amount">Amount (LKR) *</label>
                            <input type="number" id="expense-amount" placeholder="0.00" min="0" step="0.01" required>
                        </div>
                        <div class="input-group">
                            <label for="expense-date">Date *</label>
                            <input type="date" id="expense-date" required>
                        </div>
                    </div>
                    <div class="input-group full-width">
                        <label for="expense-description">Description/Reason *</label>
                        <textarea id="expense-description" placeholder="Describe the expense reason and details..."
                            rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn secondary" onclick="closeExpenseModal()">Cancel</button>
                <button class="btn primary" onclick="saveExpense()">
                    <i class="fas fa-save"></i>
                    Save Expense
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div id="edit-expense-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-edit"></i>
                    Edit Expense
                </h3>
                <button class="close-btn" onclick="closeEditExpenseModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-expense-form">
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="edit-expense-project">Project *</label>
                            <select id="edit-expense-project" required>
                                <option value="">Select Project</option>
                                <option value="PRJ-2025-045">AC Repair - Colombo Office</option>
                                <option value="PRJ-2025-042">Plumbing Repair - Kandy Branch</option>
                                <option value="PRJ-2025-041">Electrical Wiring - Galle Factory</option>
                                <option value="PRJ-2025-038">Roof Repair - Negombo Villa</option>
                                <option value="PRJ-2025-037">Generator Maintenance - Hospital</option>
                                <option value="PRJ-2025-035">HVAC Installation - Shopping Mall</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="edit-expense-category">Category *</label>
                            <select id="edit-expense-category" required>
                                <option value="">Select Category</option>
                                <option value="materials">Materials</option>
                                <option value="labor">Labor</option>
                                <option value="transport">Transport</option>
                                <option value="equipment">Equipment</option>
                                <option value="permits">Permits & Licenses</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="edit-expense-amount">Amount (LKR) *</label>
                            <input type="number" id="edit-expense-amount" placeholder="0.00" min="0" step="0.01"
                                required>
                        </div>
                        <div class="input-group">
                            <label for="edit-expense-date">Date *</label>
                            <input type="date" id="edit-expense-date" required>
                        </div>
                    </div>
                    <div class="input-group full-width">
                        <label for="edit-expense-description">Description/Reason *</label>
                        <textarea id="edit-expense-description" placeholder="Describe the expense reason and details..."
                            rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn secondary" onclick="closeEditExpenseModal()">Cancel</button>
                <button class="btn primary" onclick="updateExpense()">
                    <i class="fas fa-save"></i>
                    Update Expense
                </button>
            </div>
        </div>
    </div>

    <!-- Invoice Viewer Modal -->
    <div id="invoice-modal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3>
                    <i class="fas fa-file-invoice"></i>
                    Invoice Details
                </h3>
                <button class="close-btn" onclick="closeInvoiceModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="invoice-content">
                    <!-- Invoice content will be dynamically generated -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn secondary" onclick="closeInvoiceModal()">Close</button>
                <button class="btn primary" onclick="printInvoice()">
                    <i class="fas fa-print"></i>
                    Print Invoice
                </button>
                <button class="btn primary" onclick="downloadInvoice()">
                    <i class="fas fa-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Load sidebar and topbar
        fetch('/2nd-Year-Group-Project/FixLanka/views/company/sidebar.php')
            .then(response => response.text())
            .then(data => {
                // Set active state immediately in the HTML before inserting
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;

                // Remove any existing active classes
                const allNavItems = tempDiv.querySelectorAll('.nav-item');
                allNavItems.forEach(item => item.classList.remove('active'));

                // Set payments as active immediately
                const paymentsLink = tempDiv.querySelector('a[href="/2nd-Year-Group-Project/FixLanka/views/company/payments.php"]');
                if (paymentsLink) {
                    paymentsLink.parentElement.classList.add('active');
                }

                // Insert the modified HTML
                document.getElementById('sidebar-container').innerHTML = tempDiv.innerHTML;
            });

        fetch('/2nd-Year-Group-Project/FixLanka/views/company/topbar.php?page=payments')
            .then(response => response.text())
            .then(data => {
                document.getElementById('topbar-container').innerHTML = data;
                
                // Initialize topbar after loading
                if (typeof initializeTopbar === 'function') {
                    setTimeout(initializeTopbar, 100);
                }
                if (typeof initProfileDropdown === 'function') {
                    setTimeout(initProfileDropdown, 200);
                }
            });
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/payments_layout.js"></script>
</body>

</html>



