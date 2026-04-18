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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/repairer-pages.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/earnings.css">
</head>
<body>
    <!-- Sidebar Toggle Checkbox -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle-input">
    
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Include Topbar -->
        <?php require_once __DIR__ . '/../common/topbar.php'; ?>

        <!-- Include Sidebar -->
        <?php require_once __DIR__ . '/../common/sidebar.php'; ?>

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
                                <button class="btn btn-primary" onclick="exportEarningsReport()">
                                    <i class="fas fa-download"></i>
                                    Export Report
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Earnings Type Tabs -->
                    <div class="tabs-container">
                        <div class="tabs">
                            <button class="tab-btn active" data-tab="customer" onclick="switchEarningsTab('customer')">
                                <i class="fas fa-users"></i> Customer Repairs
                            </button>
                            <button class="tab-btn" data-tab="company" onclick="switchEarningsTab('company')">
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
                                    <h3 class="summary-number" id="totalEarningsStat">—</h3>
                                    <p class="summary-label">Total Earnings</p>
                                </div>
                            </div>

                            <div class="summary-card month-earnings">
                                <div class="summary-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number" id="monthEarningsStat">—</h3>
                                    <p class="summary-label">Earnings This Month</p>
                                </div>
                            </div>

                            <div class="summary-card pending-payments">
                                <div class="summary-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number" id="pendingEarningsStat">—</h3>
                                    <p class="summary-label">Pending Payments</p>
                                </div>
                            </div>

                            <div class="summary-card avg-job-value">
                                <div class="summary-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div class="summary-content">
                                    <h3 class="summary-number" id="avgJobValueStat">—</h3>
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
                            <span class="section-subtitle" id="earningsSubtitle">Loading...</span>
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
                                <tbody id="earningsTableBody">
                                    <tr>
                                        <td colspan="6" style="text-align:center;padding:24px;color:var(--text-secondary)">
                                            <i class="fas fa-spinner fa-spin"></i> Loading earnings...
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
                                        <h3 class="summary-number" id="companyTotalEarningsStat">—</h3>
                                        <p class="summary-label">Total from Company Jobs</p>
                                    </div>
                                </div>

                                <div class="summary-card month-earnings">
                                    <div class="summary-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number" id="companyMonthEarningsStat">—</h3>
                                        <p class="summary-label">This Month</p>
                                    </div>
                                </div>

                                <div class="summary-card pending-payments">
                                    <div class="summary-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number" id="companyPendingStat">—</h3>
                                        <p class="summary-label">Pending Payments</p>
                                    </div>
                                </div>

                                <div class="summary-card avg-job-value">
                                    <div class="summary-icon">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h3 class="summary-number" id="companyActiveContractsStat">—</h3>
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
                                <span class="section-subtitle" id="companyEarningsSubtitle">Loading...</span>
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
                                    <tbody id="companyEarningsTableBody">
                                        <tr>
                                            <td colspan="8" style="text-align:center;padding:24px;color:var(--text-secondary)">
                                                <i class="fas fa-spinner fa-spin"></i> Loading company earnings...
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

    <!-- Earning Details Drawer -->
    <div class="drawer" id="earningDetailsDrawer">
        <div class="drawer-overlay" onclick="closeEarningDetailsDrawer()"></div>
        <div class="drawer-content large">
            <div class="drawer-header">
                <h3><i class="fas fa-receipt"></i> Earning Details</h3>
                <button class="drawer-close" onclick="closeEarningDetailsDrawer()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="drawer-body">
                <!-- Payment Status Banner -->
                <div class="earning-status-banner" id="earningStatusBanner">
                    <div class="status-icon" id="earningStatusIcon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="status-content">
                        <h4 id="earningStatusTitle">Payment Received</h4>
                        <p id="earningStatusMessage">This payment has been successfully received</p>
                    </div>
                </div>

                <!-- Job Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-briefcase"></i> Job Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Job Title</span>
                            <span class="detail-value" id="earningJobTitle">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Category</span>
                            <span class="detail-value" id="earningCategory">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Date Completed</span>
                            <span class="detail-value" id="earningDate">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Duration</span>
                            <span class="detail-value" id="earningDuration">—</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-user"></i> Customer Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Customer Name</span>
                            <span class="detail-value" id="earningCustomerName">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Location</span>
                            <span class="detail-value" id="earningLocation">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Contact</span>
                            <span class="detail-value" id="earningContact">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Rating Given</span>
                            <span class="detail-value" id="earningRating">—</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Breakdown -->
                <div class="detail-section">
                    <h4><i class="fas fa-money-bill-wave"></i> Payment Breakdown</h4>
                    <div class="payment-breakdown">
                        <div class="breakdown-row">
                            <span class="breakdown-label">Service Fee</span>
                            <span class="breakdown-value" id="earningServiceFee">—</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Platform Fee (10%)</span>
                            <span class="breakdown-value text-danger" id="earningPlatformFee">—</span>
                        </div>
                        <div class="breakdown-row">
                            <span class="breakdown-label">Materials Cost</span>
                            <span class="breakdown-value" id="earningMaterialsCost">—</span>
                        </div>
                        <div class="breakdown-row total">
                            <span class="breakdown-label"><strong>Total Earned</strong></span>
                            <span class="breakdown-value" id="earningTotalEarned">—</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="detail-section">
                    <h4><i class="fas fa-credit-card"></i> Payment Information</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value" id="earningPaymentMethod">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Transaction ID</span>
                            <span class="detail-value" id="earningTransactionId">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Date</span>
                            <span class="detail-value" id="earningPaymentDate">—</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                <span class="status-badge" id="earningPaymentStatus">—</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="detail-section">
                    <h4><i class="fas fa-align-left"></i> Job Description</h4>
                    <p id="earningDescription" style="color: var(--text-secondary); line-height: 1.6; background: var(--bg-secondary); padding: 16px; border-radius: 8px;">—</p>
                </div>

                <!-- Timeline -->
                <div class="detail-section">
                    <h4><i class="fas fa-history"></i> Timeline</h4>
                    <div id="earningTimeline" class="timeline">
                        <!-- Timeline items will be loaded here -->
                    </div>
                </div>
            </div>
            <div class="drawer-footer">
                <button class="btn btn-secondary" onclick="closeEarningDetailsDrawer()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button class="btn btn-outline" onclick="downloadInvoiceFromDrawer()">
                    <i class="fas fa-download"></i> Download Invoice
                </button>
                <button class="btn btn-primary" id="drawerReminderBtn" style="display: none;" onclick="sendReminderFromDrawer()">
                    <i class="fas fa-bell"></i> Send Reminder
                </button>
            </div>
        </div>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script>
        window.CURRENT_REPAIRER_ID = <?php echo isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0; ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/earnings.js"></script>
</body>
</html>

