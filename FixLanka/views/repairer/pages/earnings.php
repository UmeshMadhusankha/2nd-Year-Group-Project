<?php
// Page configuration
$currentPage = 'earnings';
$pageTitle = 'Earnings';
$pageSubtitle = 'Track your income and payment history';
$searchPlaceholder = 'Search earnings, jobs, dates...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Earnings - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/common/global.css">
    <link rel="stylesheet" href="../../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../../assets/css/common/topbar.css">
    <link rel="stylesheet" href="../../../assets/css/common/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/earnings.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php include '../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php include '../common/sidebar.php'; ?>

        <!-- Main Content -->

        <!-- Main Content -->
        <div class="main-content-wrapper">
            <main class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header-section">
                        <div class="page-header">
                            <div class="page-header-text">
                                <h1 class="page-header-title">Earnings</h1>
                                <p class="page-header-subtitle">Track your income from customer repairs and company contracts</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn btn-primary">
                                    <i class="fas fa-download"></i>
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Earnings Type Tabs -->
                    <div class="tabs-container">
                        <div class="tabs">
                            <button class="tab-btn active" onclick="switchEarningsTab('customer')">
                                <i class="fas fa-users"></i> Customer Repairs
                            </button>
                            <button class="tab-btn" onclick="switchEarningsTab('company')">
                                <i class="fas fa-building"></i> Company Jobs
                            </button>
                        </div>
                    </div>

                    <!-- TAB 1: Customer Repairs Earnings -->
                    <div class="tab-content active" id="customerEarningsTab">

                    <!-- Summary Section -->
                    <section class="summary-section">
                        <div class="summary-cards">
                            <div class="summary-card total-earnings">
                                <div class="summary-icon">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number">LKR 285,450</h3>
                                    <p class="summary-label">Total Earnings</p>
                                </div>
                            </div>

                            <div class="summary-card month-earnings">
                                <div class="summary-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number">LKR 45,200</h3>
                                    <p class="summary-label">Earnings This Month</p>
                                </div>
                            </div>

                            <div class="summary-card pending-payments">
                                <div class="summary-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number">LKR 8,500</h3>
                                    <p class="summary-label">Pending Payments</p>
                                </div>
                            </div>

                            <div class="summary-card avg-job-value">
                                <div class="summary-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number">LKR 2,850</h3>
                                    <p class="summary-label">Average Job Value</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Filters Section -->
                    <section class="filters-section">
                        <div class="filters-container">
                            <div class="filter-group">
                                <label for="period-filter" class="filter-label">Time Period</label>
                                <select id="period-filter" class="filter-select">
                                    <option value="all">All Time</option>
                                    <option value="this-month" selected>This Month</option>
                                    <option value="last-month">Last Month</option>
                                    <option value="last-3-months">Last 3 Months</option>
                                    <option value="this-year">This Year</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="status-filter" class="filter-label">Payment Status</label>
                                <select id="status-filter" class="filter-select">
                                    <option value="all">All Payments</option>
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>

                            <div class="filter-group">
                                <label for="sort-filter" class="filter-label">Sort By</label>
                                <select id="sort-filter" class="filter-select">
                                    <option value="newest">Newest First</option>
                                    <option value="oldest">Oldest First</option>
                                    <option value="amount-high">Highest Amount</option>
                                    <option value="amount-low">Lowest Amount</option>
                                </select>
                            </div>

                            <div class="filter-actions">
                                <button class="btn btn-outline" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i>
                                    Reset
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Earnings Table Section -->
                    <section class="earnings-section">
                        <div class="section-header">
                            <h2 class="section-title">Earnings History</h2>
                            <span class="section-subtitle">5 payments this month</span>
                        </div>

                        <div class="earnings-table-container">
                            <table class="earnings-table">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-sort="date">
                                            <span>Date</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="job">
                                            <span>Job Title</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="customer">
                                            <span>Customer</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="amount">
                                            <span>Amount Earned</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th class="sortable" data-sort="status">
                                            <span>Payment Status</span>
                                            <i class="fas fa-sort"></i>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="earnings-row" data-status="paid">
                                        <td class="date-cell">
                                            <div class="date-info">
                                                <span class="date-primary">Sep 1, 2025</span>
                                                <span class="date-secondary">2 hours ago</span>
                                            </div>
                                        </td>
                                        <td class="job-cell">
                                            <div class="job-info">
                                                <span class="job-title">Kitchen Sink Repair</span>
                                                <span class="job-category">Plumbing</span>
                                            </div>
                                        </td>
                                        <td class="customer-cell">
                                            <div class="customer-info">
                                                <span class="customer-name">Sarah Fernando</span>
                                                <span class="customer-location">Colombo 07</span>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="amount-earned">LKR 2,800</span>
                                        </td>
                                        <td class="status-cell">
                                            <span class="payment-status paid">
                                                <i class="fas fa-check-circle"></i>
                                                Paid
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline" onclick="downloadInvoice(1)">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr class="earnings-row" data-status="paid">
                                        <td class="date-cell">
                                            <div class="date-info">
                                                <span class="date-primary">Aug 30, 2025</span>
                                                <span class="date-secondary">2 days ago</span>
                                            </div>
                                        </td>
                                        <td class="job-cell">
                                            <div class="job-info">
                                                <span class="job-title">Ceiling Fan Installation</span>
                                                <span class="job-category">Electrical</span>
                                            </div>
                                        </td>
                                        <td class="customer-cell">
                                            <div class="customer-info">
                                                <span class="customer-name">Kandy Hardware Store</span>
                                                <span class="customer-location">Kandy</span>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="amount-earned">LKR 4,200</span>
                                        </td>
                                        <td class="status-cell">
                                            <span class="payment-status paid">
                                                <i class="fas fa-check-circle"></i>
                                                Paid
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline" onclick="downloadInvoice(2)">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr class="earnings-row" data-status="pending">
                                        <td class="date-cell">
                                            <div class="date-info">
                                                <span class="date-primary">Aug 29, 2025</span>
                                                <span class="date-secondary">3 days ago</span>
                                            </div>
                                        </td>
                                        <td class="job-cell">
                                            <div class="job-info">
                                                <span class="job-title">Air Conditioning Repair</span>
                                                <span class="job-category">HVAC</span>
                                            </div>
                                        </td>
                                        <td class="customer-cell">
                                            <div class="customer-info">
                                                <span class="customer-name">Priya Wickramasinghe</span>
                                                <span class="customer-location">Nugegoda</span>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="amount-earned">LKR 3,500</span>
                                        </td>
                                        <td class="status-cell">
                                            <span class="payment-status pending">
                                                <i class="fas fa-clock"></i>
                                                Pending
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails(3)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="sendReminder(3)">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr class="earnings-row" data-status="paid">
                                        <td class="date-cell">
                                            <div class="date-info">
                                                <span class="date-primary">Aug 28, 2025</span>
                                                <span class="date-secondary">4 days ago</span>
                                            </div>
                                        </td>
                                        <td class="job-cell">
                                            <div class="job-info">
                                                <span class="job-title">Washing Machine Repair</span>
                                                <span class="job-category">Appliance</span>
                                            </div>
                                        </td>
                                        <td class="customer-cell">
                                            <div class="customer-info">
                                                <span class="customer-name">Nimal Perera</span>
                                                <span class="customer-location">Maharagama</span>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="amount-earned">LKR 2,500</span>
                                        </td>
                                        <td class="status-cell">
                                            <span class="payment-status paid">
                                                <i class="fas fa-check-circle"></i>
                                                Paid
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails(4)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline" onclick="downloadInvoice(4)">
                                                <i class="fas fa-download"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <tr class="earnings-row" data-status="pending">
                                        <td class="date-cell">
                                            <div class="date-info">
                                                <span class="date-primary">Aug 27, 2025</span>
                                                <span class="date-secondary">5 days ago</span>
                                            </div>
                                        </td>
                                        <td class="job-cell">
                                            <div class="job-info">
                                                <span class="job-title">Bathroom Plumbing Fix</span>
                                                <span class="job-category">Plumbing</span>
                                            </div>
                                        </td>
                                        <td class="customer-cell">
                                            <div class="customer-info">
                                                <span class="customer-name">Kamala Silva</span>
                                                <span class="customer-location">Dehiwala</span>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <span class="amount-earned">LKR 1,800</span>
                                        </td>
                                        <td class="status-cell">
                                            <span class="payment-status pending">
                                                <i class="fas fa-clock"></i>
                                                Pending
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button class="btn btn-sm btn-outline" onclick="viewEarningDetails(5)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="sendReminder(5)">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    </div>

                    <!-- TAB 2: Company Jobs Earnings -->
                    <div class="tab-content" id="companyEarningsTab">
                        <!-- Summary Section -->
                        <section class="summary-section">
                            <div class="summary-cards">
                                <div class="summary-card total-earnings">
                                    <div class="summary-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number">LKR 67,500</h3>
                                        <p class="summary-label">Total from Company Jobs</p>
                                    </div>
                                </div>

                                <div class="summary-card month-earnings">
                                    <div class="summary-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number">LKR 24,000</h3>
                                        <p class="summary-label">This Month</p>
                                    </div>
                                </div>

                                <div class="summary-card pending-payments">
                                    <div class="summary-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number">LKR 11,200</h3>
                                        <p class="summary-label">Pending Payments</p>
                                    </div>
                                </div>

                                <div class="summary-card avg-job-value">
                                    <div class="summary-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number">2</h3>
                                        <p class="summary-label">Active Contracts</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Filters Section -->
                        <section class="filters-section">
                            <div class="filters-container">
                                <div class="filter-group">
                                    <label for="company-period-filter" class="filter-label">Time Period</label>
                                    <select id="company-period-filter" class="filter-select">
                                        <option value="all">All Time</option>
                                        <option value="this-month" selected>This Month</option>
                                        <option value="last-month">Last Month</option>
                                        <option value="last-3-months">Last 3 Months</option>
                                        <option value="this-year">This Year</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <label for="company-contract-filter" class="filter-label">Contract</label>
                                    <select id="company-contract-filter" class="filter-select">
                                        <option value="all">All Contracts</option>
                                        <option value="techcorp">TechCorp Solutions</option>
                                        <option value="buildpro">BuildPro Lanka</option>
                                    </select>
                                </div>

                                <div class="filter-group">
                                    <label for="company-status-filter" class="filter-label">Payment Status</label>
                                    <select id="company-status-filter" class="filter-select">
                                        <option value="all">All Payments</option>
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>

                                <div class="filter-actions">
                                    <button class="btn btn-outline" onclick="resetCompanyFilters()">
                                        <i class="fas fa-undo"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- Company Jobs Earnings Table Section -->
                        <section class="earnings-section">
                            <div class="section-header">
                                <h2 class="section-title">Company Job Payments</h2>
                                <span class="section-subtitle">3 assignments this month</span>
                            </div>

                            <div class="earnings-table-container">
                                <table class="earnings-table">
                                    <thead>
                                        <tr>
                                            <th class="sortable" data-sort="date">
                                                <span>Date</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="assignment">
                                                <span>Assignment</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="company">
                                                <span>Company</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="hours">
                                                <span>Hours Worked</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="rate">
                                                <span>Hourly Rate</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="amount">
                                                <span>Total Amount</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th class="sortable" data-sort="status">
                                                <span>Status</span>
                                                <i class="fas fa-sort"></i>
                                            </th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="earnings-row" data-status="paid">
                                            <td class="date-cell">
                                                <div class="date-info">
                                                    <span class="date-primary">Oct 22, 2025</span>
                                                    <span class="date-secondary">Today</span>
                                                </div>
                                            </td>
                                            <td class="job-cell">
                                                <div class="job-info">
                                                    <span class="job-title">HVAC System Maintenance</span>
                                                    <span class="job-category">HVAC</span>
                                                </div>
                                            </td>
                                            <td class="customer-cell">
                                                <div class="customer-info">
                                                    <span class="customer-name">TechCorp Solutions</span>
                                                    <span class="customer-location">Colombo 07</span>
                                                </div>
                                            </td>
                                            <td class="hours-cell">
                                                <span class="hours-worked">7.5 hours</span>
                                            </td>
                                            <td class="rate-cell">
                                                <span class="hourly-rate">LKR 2,800/hr</span>
                                            </td>
                                            <td class="amount-cell">
                                                <span class="amount-earned">LKR 21,000</span>
                                            </td>
                                            <td class="status-cell">
                                                <span class="payment-status paid">
                                                    <i class="fas fa-check-circle"></i>
                                                    Paid
                                                </span>
                                            </td>
                                            <td class="actions-cell">
                                                <button class="btn btn-sm btn-outline" onclick="viewCompanyEarningDetails(1)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline" onclick="downloadCompanyInvoice(1)">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <tr class="earnings-row" data-status="pending">
                                            <td class="date-cell">
                                                <div class="date-info">
                                                    <span class="date-primary">Oct 23, 2025</span>
                                                    <span class="date-secondary">Tomorrow</span>
                                                </div>
                                            </td>
                                            <td class="job-cell">
                                                <div class="job-info">
                                                    <span class="job-title">Emergency AC Repair</span>
                                                    <span class="job-category">HVAC</span>
                                                </div>
                                            </td>
                                            <td class="customer-cell">
                                                <div class="customer-info">
                                                    <span class="customer-name">TechCorp Solutions</span>
                                                    <span class="customer-location">Colombo 03</span>
                                                </div>
                                            </td>
                                            <td class="hours-cell">
                                                <span class="hours-worked">4.0 hours</span>
                                            </td>
                                            <td class="rate-cell">
                                                <span class="hourly-rate">LKR 2,800/hr</span>
                                            </td>
                                            <td class="amount-cell">
                                                <span class="amount-earned">LKR 11,200</span>
                                            </td>
                                            <td class="status-cell">
                                                <span class="payment-status pending">
                                                    <i class="fas fa-clock"></i>
                                                    Pending
                                                </span>
                                            </td>
                                            <td class="actions-cell">
                                                <button class="btn btn-sm btn-outline" onclick="viewCompanyEarningDetails(2)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary" onclick="sendCompanyReminder(2)">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <tr class="earnings-row" data-status="paid">
                                            <td class="date-cell">
                                                <div class="date-info">
                                                    <span class="date-primary">Oct 24, 2025</span>
                                                    <span class="date-secondary">In 2 days</span>
                                                </div>
                                            </td>
                                            <td class="job-cell">
                                                <div class="job-info">
                                                    <span class="job-title">Electrical Panel Installation</span>
                                                    <span class="job-category">Electrical</span>
                                                </div>
                                            </td>
                                            <td class="customer-cell">
                                                <div class="customer-info">
                                                    <span class="customer-name">BuildPro Lanka</span>
                                                    <span class="customer-location">Kandy</span>
                                                </div>
                                            </td>
                                            <td class="hours-cell">
                                                <span class="hours-worked">8.0 hours</span>
                                            </td>
                                            <td class="rate-cell">
                                                <span class="hourly-rate">LKR 2,500/hr</span>
                                            </td>
                                            <td class="amount-cell">
                                                <span class="amount-earned">LKR 20,000</span>
                                            </td>
                                            <td class="status-cell">
                                                <span class="payment-status paid">
                                                    <i class="fas fa-check-circle"></i>
                                                    Paid
                                                </span>
                                            </td>
                                            <td class="actions-cell">
                                                <button class="btn btn-sm btn-outline" onclick="viewCompanyEarningDetails(3)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline" onclick="downloadCompanyInvoice(3)">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../../../assets/javascript/common/common.js"></script>
    <script src="../../../assets/javascript/repairer/earnings.js"></script>
    <script>
        // Tab switching function
        function switchEarningsTab(tab) {
            // Update tab buttons
            document.querySelectorAll('.tabs-container .tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tab + 'EarningsTab').classList.add('active');
        }

        function resetCompanyFilters() {
            document.getElementById('company-period-filter').value = 'this-month';
            document.getElementById('company-contract-filter').value = 'all';
            document.getElementById('company-status-filter').value = 'all';
        }

        function viewCompanyEarningDetails(id) {
            console.log('Viewing company earning details:', id);
            // Implementation for viewing company earning details
        }

        function downloadCompanyInvoice(id) {
            console.log('Downloading company invoice:', id);
            // Implementation for downloading company invoice
        }

        function sendCompanyReminder(id) {
            console.log('Sending payment reminder to company:', id);
            // Implementation for sending reminder
        }
    </script>
</body>
</html>
