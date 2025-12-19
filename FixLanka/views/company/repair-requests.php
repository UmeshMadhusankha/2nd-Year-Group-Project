<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Requests - FixLanka</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/repair-requests.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Sidebar Toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Container -->
        <div id="sidebar-container"></div>

        <main class="main-content">
            <!-- Header Container -->
            <div id="header-container"></div>

            <!-- Page Header -->
            <header class="page-header">
                <div class="header-content">
                    <div class="header-main">
                        <div class="title-section">
                            <h1>
                                <i class="fas fa-tools"></i>
                                Repair Requests
                            </h1>
                            <p class="subtitle">
                                Manage customer repair requests - view public opportunities and handle direct
                                requests
                            </p>
                            <div class="breadcrumbs">
                                <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                                <span class="separator">/</span>
                                <span class="current">Repair Requests</span>
                            </div>
                        </div>

                        <!-- Header Stats -->
                        <div class="header-stats">
                            <div class="stat-card">
                                <div class="stat-number" id="public-requests-count">0</div>
                                <div class="stat-label">Public Requests</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="direct-requests-count">0</div>
                                <div class="stat-label">Direct Requests</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="pending-response-count">0</div>
                                <div class="stat-label">Pending Response</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-number" id="this-month-count">0</div>
                                <div class="stat-label">This Month</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Tab Navigation -->
            <section class="requests-tabs">
                <nav class="tab-nav">
                    <button class="tab-button active" data-tab="public-requests">
                        <i class="fas fa-globe"></i>
                        Public Requests
                        <span class="tab-count">24</span>
                    </button>
                    <button class="tab-button" data-tab="direct-requests">
                        <i class="fas fa-inbox"></i>
                        Direct Requests
                        <span class="tab-count">8</span>
                    </button>
                    <button class="tab-button" data-tab="logs">
                        <i class="fas fa-history"></i>
                        Request Logs
                        <span class="tab-count">156</span>
                    </button>
                </nav>
            </section>

            <!-- Public Requests Tab -->
            <section id="public-requests" class="tab-content active">
                <!-- Controls -->
                <div class="requests-controls">
                    <div class="controls-row">
                        <div class="filters-group">
                            <div class="filter-item">
                                <label class="filter-label">Service Type</label>
                                <select class="filter-select" id="service-filter">
                                    <option value="">All Services</option>
                                    <option value="plumbing">Plumbing</option>
                                    <option value="electrical">Electrical</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="hvac">HVAC</option>
                                    <option value="painting">Painting</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Priority</label>
                                <select class="filter-select" id="priority-filter">
                                    <option value="">All Priorities</option>
                                    <option value="high">High Priority</option>
                                    <option value="low">Low Priority</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Location</label>
                                <select class="filter-select" id="location-filter">
                                    <option value="">All Locations</option>
                                    <option value="colombo">Colombo</option>
                                    <option value="kandy">Kandy</option>
                                    <option value="galle">Galle</option>
                                    <option value="negombo">Negombo</option>
                                </select>
                            </div>
                        </div>

                        <div class="view-controls">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Requests Grid -->
                <div class="requests-grid">
                    <!-- Job requests will be loaded dynamically from the database -->
                    <div class="loading-state" style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; opacity: 0.5;"></i>
                        <p style="color: var(--text-secondary); margin-top: 1rem;">Loading job requests...</p>
                    </div>
                </div>
            </section>

            <!-- Direct Requests Tab -->
            <section id="direct-requests" class="tab-content">
                <!-- Controls -->
                <div class="requests-controls">
                    <div class="controls-row">
                        <div class="filters-group">
                            <div class="filter-item">
                                <label class="filter-label">Status</label>
                                <select class="filter-select">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label class="filter-label">Date Range</label>
                                <select class="filter-select">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                        </div>

                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" placeholder="Search direct requests...">
                        </div>
                    </div>
                <!-- </div> -->

                <!-- Direct Requests Table -->
                <div class="requests-table-container">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>Request Details</th>
                                <th>Customer</th>
                                <th>Date Received</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>
                                        <h5>AC Repair - Colombo</h5>
                                        <p
                                            style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                                            #REQ-2025-004 • Emergency Service
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-customer">
                                        <div class="table-customer-avatar">RJ</div>
                                        <div class="table-customer-info">
                                            <h5>Robert Johnson</h5>
                                            <p>robert@email.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td>Sept 10, 2025</td>
                                <td>
                                    <span class="status-badge pending">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="table-action-btn view" onclick="viewRequestDetails('REQ-2025-004')">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </button>
                                        <button class="table-action-btn accept"
                                            onclick="acceptDirectRequest('REQ-2025-004')">
                                            <i class="fas fa-check"></i>
                                            Accept
                                        </button>
                                        <button class="table-action-btn reject"
                                            onclick="rejectDirectRequest('REQ-2025-004')">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <h5>Plumbing Fix - Kandy</h5>
                                        <p
                                            style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                                            #REQ-2025-005 • Bathroom Renovation
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-customer">
                                        <div class="table-customer-avatar">JS</div>
                                        <div class="table-customer-info">
                                            <h5>Jane Smith</h5>
                                            <p>jane.smith@email.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td>Sept 12, 2025</td>
                                <td>
                                    <span class="status-badge pending">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button class="table-action-btn view" onclick="viewRequestDetails('REQ-2025-005')">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </button>
                                        <button class="table-action-btn accept"
                                            onclick="acceptDirectRequest('REQ-2025-005')">
                                            <i class="fas fa-check"></i>
                                            Accept
                                        </button>
                                        <button class="table-action-btn reject"
                                            onclick="rejectDirectRequest('REQ-2025-005')">
                                            <i class="fas fa-times"></i>
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <h5>Electrical Installation</h5>
                                        <p
                                            style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                                            #REQ-2025-006 • New Construction
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-customer">
                                        <div class="table-customer-avatar">MB</div>
                                        <div class="table-customer-info">
                                            <h5>Mike Brown</h5>
                                            <p>mike.brown@email.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td>Sept 11, 2025</td>
                                <td>
                                    <span class="status-badge accepted">
                                        <i class="fas fa-check"></i>
                                        Accepted
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/2nd-Year-Group-Project/FixLanka/views/company/contracts.php?id=REQ-2025-006" class="table-action-btn view">
                                            <i class="fas fa-eye"></i>
                                            View Contract
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Logs Tab -->
            <section id="logs" class="tab-content">
                <div class="logs-container">
                    <!-- Successful Requests -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon success">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Successful Contracts</h3>
                                <p>Completed projects with positive outcomes</p>
                            </div>
                            <div class="log-count" id="successful-contracts-count">0</div>
                        </div>
                        <div class="log-items" id="successful-contracts-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No successful contracts yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon pending">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Pending Quotations</h3>
                                <p>Submitted quotations awaiting customer response</p>
                            </div>
                            <div class="log-count" id="pending-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="pending-quotations-list">
                            <!-- Pending quotations will be dynamically added here -->
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No pending quotations. Submit a quotation to see it here.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Accepted Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Accepted Quotations</h3>
                                <p>Quotations accepted by customers</p>
                            </div>
                            <div class="log-count" id="accepted-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="accepted-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No accepted quotations yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Requests -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon rejected">
                                <i class="fas fa-times"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Rejected Quotations</h3>
                                <p>Quotations declined by customers</p>
                            </div>
                            <div class="log-count" id="rejected-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="rejected-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No rejected quotations yet.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Draft Quotations -->
                    <div class="log-section">
                        <div class="log-section-header">
                            <div class="log-section-icon draft">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="log-section-title">
                                <h3>Draft Quotations</h3>
                                <p>Saved quotation drafts for future completion</p>
                            </div>
                            <div class="log-count" id="draft-quotations-count">0</div>
                        </div>
                        <div class="log-items" id="draft-quotations-list">
                            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                <i class="fas fa-inbox" style="font-size: 2rem; opacity: 0.5; margin-bottom: var(--spacing-sm);"></i>
                                <p>No draft quotations. Save a quotation as draft to see it here.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Quotation Modal -->
    <div id="quotation-modal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Submit Quotation
                </h2>
                <button class="modal-close" onclick="closeQuotationModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="quotation-form">
                    <!-- Hidden field to store request ID -->
                    <input type="hidden" id="request-id" name="request_id">
                    
                    <!-- Request Summary -->
                    <div class="form-group">
                        <label class="form-label">Request Summary</label>
                        <div id="quotation-request-details"
                            style="padding: var(--spacing-md); background: var(--bg-secondary); border-radius: var(--border-radius); margin-bottom: var(--spacing-md);">
                            <!-- Request details will be populated here -->
                        </div>
                    </div>

                    <!-- Quotation Details Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-file-invoice"></i>
                            Quotation Details
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="quotation-title">
                                    Quotation Title <span class="required">*</span>
                                </label>
                                <input type="text" id="quotation-title" name="title" class="form-input" 
                                    placeholder="e.g., AC Repair Service Quote" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="service-description">
                                Service Description <span class="required">*</span>
                            </label>
                            <textarea id="service-description" name="description" class="form-textarea" rows="4"
                                placeholder="Describe the services you will provide, work scope, and deliverables..." required></textarea>
                            <small class="form-hint">Be specific about what's included in this quotation</small>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-calculator"></i>
                            Pricing & Cost Breakdown
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="labor-cost">
                                    Labor Cost (LKR) <span class="required">*</span>
                                </label>
                                <input type="number" id="labor-cost" name="labor_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="material-cost">
                                    Material Cost (LKR) <span class="required">*</span>
                                </label>
                                <input type="number" id="material-cost" name="material_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="transport-cost">
                                    Transport Cost (LKR)
                                </label>
                                <input type="number" id="transport-cost" name="transport_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="other-cost">
                                    Other Charges (LKR)
                                </label>
                                <input type="number" id="other-cost" name="other_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" value="0">
                            </div>
                        </div>

                        <div class="cost-summary">
                            <div class="cost-row">
                                <span>Subtotal:</span>
                                <span id="subtotal-amount">LKR 0.00</span>
                            </div>
                            <div class="cost-row total">
                                <span>Total Quotation Amount:</span>
                                <span id="total-amount">LKR 0.00</span>
                            </div>
                        </div>
                        <input type="hidden" id="total-price" name="total_price" value="0">
                    </div>

                    <!-- Timeline Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Project Timeline
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="estimated-start-date">
                                    Estimated Start Date <span class="required">*</span>
                                </label>
                                <input type="date" id="estimated-start-date" name="estimated_start_date" 
                                    class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="estimated-completion-date">
                                    Estimated Completion Date <span class="required">*</span>
                                </label>
                                <input type="date" id="estimated-completion-date" name="estimated_completion_date" 
                                    class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="estimated-duration">
                                Estimated Duration (Days) <span class="required">*</span>
                            </label>
                            <input type="number" id="estimated-duration" name="estimated_duration" 
                                class="form-input" placeholder="e.g., 5" min="1" max="365" required>
                            <small class="form-hint">Number of working days to complete the project</small>
                        </div>
                    </div>

                    <!-- Terms & Conditions Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-file-contract"></i>
                            Terms & Conditions
                        </h3>

                        <div class="form-group">
                            <label class="form-label">
                                Payment Terms <span class="required">*</span>
                            </label>
                            <select id="payment-terms" name="payment_terms" class="form-select" required>
                                <option value="">Select payment terms</option>
                                <option value="full_advance">100% Advance Payment</option>
                                <option value="50_50">50% Advance, 50% on Completion</option>
                                <option value="30_70">30% Advance, 70% on Completion</option>
                                <option value="milestone">Milestone-based Payment</option>
                                <option value="on_completion">Payment on Completion</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Warranty Period <span class="required">*</span>
                            </label>
                            <select id="warranty-period" name="warranty_period" class="form-select" required>
                                <option value="">Select warranty period</option>
                                <option value="no_warranty">No Warranty</option>
                                <option value="1_month">1 Month</option>
                                <option value="3_months">3 Months</option>
                                <option value="6_months">6 Months</option>
                                <option value="1_year">1 Year</option>
                                <option value="2_years">2 Years</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="terms-conditions">
                                Additional Terms & Conditions
                            </label>
                            <textarea id="terms-conditions" name="terms_conditions" class="form-textarea" rows="4"
                                placeholder="Enter any additional terms, conditions, or special requirements..."></textarea>
                        </div>
                    </div>

                    <!-- Validity Section -->
                    <div class="form-group">
                        <label class="form-label" for="validity-period">
                            Quotation Validity Period <span class="required">*</span>
                        </label>
                        <select id="validity-period" name="validity_period" class="form-select" required>
                            <option value="7">Valid for 7 days</option>
                            <option value="14">Valid for 14 days</option>
                            <option value="30" selected>Valid for 30 days</option>
                            <option value="60">Valid for 60 days</option>
                            <option value="90">Valid for 90 days</option>
                        </select>
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="form-group">
                        <div class="checkbox-option">
                            <input type="checkbox" id="agreement" name="agreement" required>
                            <label for="agreement" class="checkbox-label">
                                I confirm that all information provided is accurate and I agree to the terms and conditions
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeQuotationModal()">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="button" class="action-btn primary" onclick="submitQuotation()">
                    <i class="fas fa-paper-plane"></i>
                    Submit Quotation
                </button>
            </div>
        </div>
    </div>

    <!-- Request Details Modal -->
    <div id="request-details-modal" class="modal-overlay">
        <div class="modal-container modal-large">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-info-circle"></i>
                    Request Details
                </h2>
                <button class="modal-close" onclick="closeRequestDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="request-details-content">
                    <!-- Request details will be dynamically populated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="action-btn secondary" onclick="closeRequestDetailsModal()">
                    <i class="fas fa-times"></i>
                    Close
                </button>
                <button type="button" class="action-btn primary" id="submit-quote-from-details">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Submit Quotation
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/repair-requests-db.js"></script>

    <script>
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            loadComponent('sidebar-container', '/2nd-Year-Group-Project/FixLanka/views/company/sidebar.php');
            loadComponent('header-container', '/2nd-Year-Group-Project/FixLanka/views/company/topbar.php');
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    if (containerId === 'sidebar-container') {
                        // Set active state immediately in the HTML before inserting
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;

                        // Remove any existing active classes
                        const allNavItems = tempDiv.querySelectorAll('.nav-item');
                        allNavItems.forEach(item => item.classList.remove('active'));

                        // Set repair requests as active immediately
                        const repairRequestsLink = tempDiv.querySelector('a[href="/2nd-Year-Group-Project/FixLanka/company-repair-requests"]');
                        if (repairRequestsLink) {
                            repairRequestsLink.parentElement.classList.add('active');
                        }

                        // Insert the modified HTML
                        document.getElementById(containerId).innerHTML = tempDiv.innerHTML;
                    } else {
                        document.getElementById(containerId).innerHTML = html;
                    }

                    // Initialize topbar after loading
                    if (containerId === 'header-container') {
                        if (typeof initializeTopbar === 'function') {
                            setTimeout(initializeTopbar, 100);
                        }
                        if (typeof initProfileDropdown === 'function') {
                            setTimeout(initProfileDropdown, 200);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading component:', error);
                });
        }
    </script>
</body>

</html>



