<?php
// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
requireRole('company');
$userData = getUserData();
?>
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/payments-export-modal.css">
</head>

<body>
    <div class="app-container">
        <!-- Include Sidebar -->
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Include Topbar -->
            <!-- Include Topbar -->
            <?php include 'topbar.php'; ?>

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

    <!-- Export Payments Modal -->
    <div class="export-modal-overlay" id="exportModal">
        <div class="export-modal-container">
            <div class="export-modal-header">
                <h2><i class="fas fa-download"></i> Export Payments</h2>
                <button class="export-modal-close" onclick="closeExportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="export-modal-body">
                <div class="export-options-container">
                    <!-- Filter By Type Section (Radio - single selection) -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-filter"></i> Filter by Type</h4>
                        <div class="export-radio-group">
                            <label class="export-radio-option">
                                <input type="radio" name="paymentType" value="all" checked>
                                <span class="radio-indicator"></span>
                                <span class="option-text">All Payments</span>
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="paymentType" value="income">
                                <span class="radio-indicator"></span>
                                <span class="option-text">Income Only</span>
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="paymentType" value="expense">
                                <span class="radio-indicator"></span>
                                <span class="option-text">Expenses Only</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filter By Status Section -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-flag"></i> Filter by Status</h4>
                        <div class="export-checkbox-group">
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportCompleted" checked>
                                <span class="checkbox-indicator"><i class="fas fa-check"></i></span>
                                <span class="option-text">Completed</span>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportPending" checked>
                                <span class="checkbox-indicator"><i class="fas fa-check"></i></span>
                                <span class="option-text">Pending</span>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportFailed">
                                <span class="checkbox-indicator"><i class="fas fa-check"></i></span>
                                <span class="option-text">Failed</span>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range Section -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-calendar-alt"></i> Date Range</h4>
                        <div class="export-date-range">
                            <div class="export-date-presets">
                                <button class="export-date-preset-btn" onclick="setDatePreset(7)" type="button">Last 7 days</button>
                                <button class="export-date-preset-btn" onclick="setDatePreset(30)" type="button">Last 30 days</button>
                                <button class="export-date-preset-btn" onclick="setDatePreset(90)" type="button">Last 3 months</button>
                                <button class="export-date-preset-btn" onclick="setDatePreset(365)" type="button">Last year</button>
                            </div>
                            <div class="export-date-inputs">
                                <div class="export-date-field">
                                    <label for="exportStartDate">From</label>
                                    <input type="date" id="exportStartDate">
                                </div>
                                <div class="export-date-field">
                                    <label for="exportEndDate">To</label>
                                    <input type="date" id="exportEndDate">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Format Section -->
                    <div class="export-option-group">
                        <h4><i class="fas fa-file-alt"></i> Export Format</h4>
                        <div class="export-format-group">
                            <label class="export-format-option">
                                <input type="radio" name="exportFormat" value="csv" checked>
                                <div class="export-format-icon csv">
                                    <i class="fas fa-file-csv"></i>
                                </div>
                                <span class="export-format-name">CSV</span>
                                <span class="export-format-desc">Universal format</span>
                            </label>
                            <label class="export-format-option">
                                <input type="radio" name="exportFormat" value="excel">
                                <div class="export-format-icon excel">
                                    <i class="fas fa-file-excel"></i>
                                </div>
                                <span class="export-format-name">Excel</span>
                                <span class="export-format-desc">Spreadsheet</span>
                            </label>
                            <label class="export-format-option">
                                <input type="radio" name="exportFormat" value="pdf">
                                <div class="export-format-icon pdf">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <span class="export-format-name">PDF</span>
                                <span class="export-format-desc">Print ready</span>
                            </label>
                        </div>
                    </div>

                    <!-- Export Summary -->
                    <div class="export-summary-box" id="exportSummaryBox">
                        <h4><i class="fas fa-chart-pie"></i> Export Preview</h4>
                        <div class="export-summary-stats">
                            <div class="export-summary-stat">
                                <span class="stat-value" id="exportTotalRecords">--</span>
                                <span class="stat-label">Records</span>
                            </div>
                            <div class="export-summary-stat">
                                <span class="stat-value" id="exportTotalAmount">--</span>
                                <span class="stat-label">Total</span>
                            </div>
                            <div class="export-summary-stat">
                                <span class="stat-value" id="exportDateRange">--</span>
                                <span class="stat-label">Days</span>
                            </div>
                        </div>
                        <div class="export-summary-loading">
                            <i class="fas fa-spinner"></i>
                            <span>Calculating...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="export-modal-footer">
                <button class="export-btn export-btn-cancel" onclick="closeExportModal()" type="button">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="export-btn export-btn-preview" onclick="previewPaymentsExport()" type="button">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button class="export-btn export-btn-download" onclick="downloadPaymentsExport()" type="button">
                    <i class="fas fa-download"></i> Download
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
                const sidebarContainer = document.getElementById('sidebar-container');
                if (sidebarContainer) {
                    sidebarContainer.innerHTML = data;
                    // Add active class
                    setTimeout(() => {
                        const paymentsLink = document.querySelector('#sidebar-container a[href*="payments"]');
                        if (paymentsLink) paymentsLink.parentElement.classList.add('active');
                    }, 100);
                }
            });

        fetch('/2nd-Year-Group-Project/FixLanka/views/company/topbar.php?page=payments')
            .then(response => response.text())
            .then(data => {
                const topbarContainer = document.getElementById('topbar-container');
                if (topbarContainer) {
                    topbarContainer.innerHTML = data;
                }
            });

        // Main Payment Logic
        document.addEventListener('DOMContentLoaded', () => {
            fetchPaymentsData();
            
            // Period change listener
            document.getElementById('period-select').addEventListener('change', updatePeriod);
        });

        let currentData = { income: [], expenses: [], projects: [] };

        async function fetchPaymentsData() {
            const period = document.getElementById('period-select').value;
            // Handle custom range if implemented in UI, for now just pass period
            const url = `/2nd-Year-Group-Project/FixLanka/api/payments.php?period=${period}`;
            
            try {
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.success) {
                    currentData = result.data;
                    updateDashboard(result.data.summary);
                    populateIncomeTable(result.data.income);
                    populateExpenseTable(result.data.expenses);
                    populateProjectDropdowns(result.data.projects);
                }
            } catch (error) {
                console.error('Error fetching payments:', error);
            }
        }

        function updateDashboard(summary) {
            document.getElementById('total-income-display').textContent = formatCurrency(summary.total_income);
            document.getElementById('total-expenses-display').textContent = formatCurrency(summary.total_expenses);
            document.getElementById('net-profit-display').textContent = formatCurrency(summary.net_profit);
            document.getElementById('pending-payments-display').textContent = formatCurrency(summary.pending_payments || 0);
            
            document.getElementById('total-transactions-display').textContent = summary.total_transactions;
            document.getElementById('avg-payment-display').textContent = formatCurrency(summary.avg_payment);
            document.getElementById('profit-margin-display').textContent = summary.profit_margin + '%';
            document.getElementById('expense-ratio-display').textContent = summary.expense_ratio + '%';
        }

        function populateIncomeTable(income) {
            const tbody = document.getElementById('income-table-body');
            tbody.innerHTML = '';
            
            if (income.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">No income records found</td></tr>';
                return;
            }

            income.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.date}</td>
                    <td>${item.project_name}</td>
                    <td>${item.client_name}</td>
                    <td>${formatCurrency(item.amount)}</td>
                    <td><span class="status-badge ${item.status}">${item.status}</span></td>
                `;
                tbody.appendChild(tr);
            });
        }

        function populateExpenseTable(expenses) {
            const tbody = document.getElementById('expense-table-body');
            tbody.innerHTML = '';

            if (expenses.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">No expense records found</td></tr>';
                return;
            }

            expenses.forEach(item => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${item.date}</td>
                    <td>${item.project_name}</td>
                    <td>${item.category}</td>
                    <td>${item.description}</td>
                    <td>${formatCurrency(item.amount)}</td>
                    <td>
                        <button class="btn-icon" onclick="deleteExpense('${item.id}')"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }
        
        function populateProjectDropdowns(projects) {
            const selects = ['income-project-filter', 'expense-project-filter', 'expense-project', 'edit-expense-project'];
            selects.forEach(id => {
               const select = document.getElementById(id);
               if (!select) return;
               
               // Keep first option (All/Select)
               const firstOption = select.options[0];
               select.innerHTML = '';
               select.appendChild(firstOption);
               
               projects.forEach(p => {
                   const opt = document.createElement('option');
                   opt.value = p.project_id;
                   opt.textContent = p.title;
                   select.appendChild(opt);
               });
            });
        }

        function formatCurrency(amount) {
            return 'LKR ' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updatePeriod() {
            fetchPaymentsData();
        }
        
        // Modal Logic
        function openExpenseModal() {
            document.getElementById('expense-modal').style.display = 'block';
             // Set default date to today
            document.getElementById('expense-date').valueAsDate = new Date();
        }

        function closeExpenseModal() {
            document.getElementById('expense-modal').style.display = 'none';
        }
        
        async function saveExpense() {
            const data = {
                project_id: document.getElementById('expense-project').value,
                category: document.getElementById('expense-category').value,
                amount: document.getElementById('expense-amount').value,
                date: document.getElementById('expense-date').value,
                description: document.getElementById('expense-description').value
            };
            
            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/payments.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                
                if (result.success) {
                    alert('Expense saved successfully');
                    closeExpenseModal();
                    fetchPaymentsData(); // Refresh
                } else {
                    alert(result.message || 'Failed to save expense');
                }
            } catch (e) {
                console.error(e);
                alert('Error processing request');
            }
        }
        
        function deleteExpense(id) {
            if(!confirm("Are you sure you want to delete this expense?")) return;
            
             fetch('/2nd-Year-Group-Project/FixLanka/api/payments.php', {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ expense_id: id })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                         alert('Expense deleted');
                         fetchPaymentsData();
                    } else {
                        alert('Failed to delete');
                    }
                });
        }
        
        // --- Filter Logic (Client-side for now as API handles basic filters) ---
        function applyIncomeFilters() {
             // Basic implementation: Refetch or client-filter.
             // Given the list size, client-side filter on 'currentData.income' is fast
             const status = document.getElementById('income-status-filter').value;
             const project = document.getElementById('income-project-filter').value;
             // ... amount filter logic ...
             
             let filtered = currentData.income.filter(item => {
                 if (status !== 'all' && item.status !== status) return false;
                 if (project !== 'all' && item.project_id != project) return false; // Note: project_id might be int/string
                 return true;
             });
             
             populateIncomeTable(filtered);
        }
        
         function applyExpenseFilters() {
             const category = document.getElementById('expense-category-filter').value;
             const project = document.getElementById('expense-project-filter').value;
             
             let filtered = currentData.expenses.filter(item => {
                 if (category !== 'all' && item.category !== category) return false;
                 if (project !== 'all' && item.project_id != project) return false;
                 return true;
             });
             
             populateExpenseTable(filtered);
        }
    </script>
</body>

</html>



