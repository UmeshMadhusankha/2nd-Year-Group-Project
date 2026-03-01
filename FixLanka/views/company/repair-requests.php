<?php
/**
 * Company Repair Requests Page
 * 
 * This page allows companies to:
 * - View available job requests from customers
 * - Submit quotations for job requests
 * - Manage submitted quotations (view, edit, delete)
 * - Track quotation status (pending, accepted, rejected, successful)
 * 
 * Authentication: Requires logged-in company user
 * 
 * @package FixLanka\Views\Company
 * @version 1.0.0
 */

// Start session and verify authentication
require_once '../../config/session.php';
requireRole('company');

// Retrieve logged-in user data from session
$userData = getUserData();
$userId = $userData['id'] ?? null;

// Ensure user is authenticated
if (!$userId) {
    die('Error: User not authenticated');
}
?>
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
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
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <!-- Header Container -->
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <!-- Repair Requests Container -->
            <div class="repair-requests-container">

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
                                <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
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
                        <span class="tab-count" id="direct-requests-tab-count">0</span>
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
                <!-- Direct Requests Table -->
                <div class="requests-table-container" id="direct-requests-container">
                    <!-- Empty state - shown when no direct requests -->
                    <div class="empty-state-table" id="direct-requests-empty">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h3 class="empty-state-title">No Direct Requests Yet</h3>
                        <p class="empty-state-description">
                            Direct requests from customers who specifically choose your company will appear here.
                            <br>
                            These are high-value opportunities because the customer already knows your work!
                        </p>
                        <div class="empty-state-tips">
                            <h4><i class="fas fa-lightbulb"></i> How to get direct requests:</h4>
                            <ul>
                                <li>Provide excellent service to build your reputation</li>
                                <li>Encourage satisfied customers to request you again</li>
                                <li>Complete your company profile to stand out</li>
                                <li>Respond quickly to public requests to build trust</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Table - shown when direct requests exist -->
                    <table class="requests-table" id="direct-requests-table" style="display: none;">
                        <thead>
                            <tr>
                                <th>Request Details</th>
                                <th>Customer</th>
                                <th>Date Received</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="direct-requests-tbody">
                            <!-- Direct requests will be dynamically loaded here -->
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

            </div><!-- /.repair-requests-container -->
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
                            <small class="form-hint">Number of calendar days to complete the project</small>
                        </div>
                    </div>

                    <!-- ============================================================ -->
                    <!-- ⭐ NEW: Work Schedule Specifications (Supervisor Requirement) -->
                    <!-- ============================================================ -->
                    <div class="form-section work-schedule-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-calendar-alt"></i>
                            Work Schedule Specifications
                        </h3>
                        <p class="section-description">
                            <i class="fas fa-info-circle"></i>
                            Specify your working days and hours for transparency and clear customer expectations
                        </p>

                        <div class="form-row">
                            <!-- Schedule Type -->
                            <div class="form-group col-md-6">
                                <label class="form-label" for="work-schedule-type">
                                    Work Schedule Type <span class="required">*</span>
                                    <i class="fas fa-question-circle tooltip-icon" title="How many days per week will you work on this project?"></i>
                                </label>
                                <select id="work-schedule-type" name="work_schedule_type" class="form-input" required>
                                    <option value="">-- Select Schedule --</option>
                                    <option value="weekdays_only" selected>Weekdays Only (Monday - Friday)</option>
                                    <option value="weekends_included">Weekends Included (Monday - Saturday)</option>
                                    <option value="all_days">All Days (7 days per week)</option>
                                    <option value="custom">Custom Schedule</option>
                                </select>
                            </div>

                            <!-- Working Days Per Week -->
                            <div class="form-group col-md-6">
                                <label class="form-label" for="working-days-per-week">
                                    Working Days Per Week <span class="required">*</span>
                                </label>
                                <input type="number" id="working-days-per-week" name="working_days_per_week" 
                                    class="form-input" min="1" max="7" value="5" required>
                                <small class="form-hint">Number of days you'll work (1-7)</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <!-- Daily Work Hours -->
                            <div class="form-group col-md-4">
                                <label class="form-label" for="daily-work-hours">
                                    Daily Work Hours <span class="required">*</span>
                                </label>
                                <input type="number" id="daily-work-hours" name="daily_work_hours" 
                                    class="form-input" min="0.5" max="24" step="0.5" value="8.00" required>
                                <small class="form-hint">Hours per working day</small>
                            </div>

                            <!-- Work Start Time -->
                            <div class="form-group col-md-4">
                                <label class="form-label" for="work-start-time">
                                    Start Time <span class="required">*</span>
                                </label>
                                <input type="time" id="work-start-time" name="work_start_time" 
                                    class="form-input" value="08:00" required>
                                <small class="form-hint">Daily work begins at</small>
                            </div>

                            <!-- Work End Time -->
                            <div class="form-group col-md-4">
                                <label class="form-label" for="work-end-time">
                                    End Time <span class="required">*</span>
                                </label>
                                <input type="time" id="work-end-time" name="work_end_time" 
                                    class="form-input" value="17:00" required>
                                <small class="form-hint">Daily work ends at</small>
                            </div>
                        </div>

                        <!-- Total Work Hours (Auto-calculated) -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="total-work-hours">
                                    Total Estimated Work Hours
                                    <i class="fas fa-calculator tooltip-icon" title="Auto-calculated based on duration, working days, and daily hours"></i>
                                </label>
                                <input type="number" id="total-work-hours" name="total_work_hours" 
                                    class="form-input" step="0.5" readonly 
                                    style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small class="form-hint">
                                    <i class="fas fa-info-circle"></i>
                                    Auto-calculated: (Duration ÷ 7 weeks) × Working Days/Week × Hours/Day
                                </small>
                            </div>

                            <!-- Overtime Available -->
                            <div class="form-group col-md-6">
                                <label class="form-label d-block">Overtime Work Available?</label>
                                <div class="custom-switch-wrapper">
                                    <label class="custom-switch">
                                        <input type="checkbox" id="overtime-available" name="overtime_available" value="1">
                                        <span class="switch-slider"></span>
                                        <span class="switch-label">Yes, overtime is available</span>
                                    </label>
                                </div>
                                <small class="form-hint">Can you work extra hours if needed?</small>
                            </div>
                        </div>

                        <!-- Overtime Rate (Conditional) -->
                        <div class="form-row" id="overtime-rate-row" style="display: none;">
                            <div class="form-group col-md-6">
                                <label class="form-label" for="overtime-rate">
                                    Overtime Hourly Rate (LKR) <span class="required">*</span>
                                </label>
                                <input type="number" id="overtime-rate" name="overtime_rate" 
                                    class="form-input" min="0" step="0.01" placeholder="e.g., 1500.00">
                                <small class="form-hint">
                                    <i class="fas fa-lightbulb"></i>
                                    Typically 1.5x your regular hourly rate
                                </small>
                            </div>
                        </div>

                        <!-- Custom Schedule Details (Conditional) -->
                        <div class="form-row" id="custom-schedule-row" style="display: none;">
                            <div class="form-group col-12">
                                <label class="form-label" for="custom-schedule-details">
                                    Custom Schedule Details <span class="required">*</span>
                                </label>
                                <textarea id="custom-schedule-details" name="custom_schedule_details" 
                                    class="form-input" rows="3" 
                                    placeholder="Example: Monday-Thursday 8am-5pm, Friday 8am-3pm. Lunch break: 12pm-1pm. No work on public holidays."></textarea>
                                <small class="form-hint">Provide specific details about your custom work schedule</small>
                            </div>
                        </div>

                        <!-- Work Schedule Preview Box -->
                        <div class="schedule-preview-box">
                            <h5 class="preview-title">
                                <i class="fas fa-eye"></i> Schedule Preview
                            </h5>
                            <div id="schedule-preview-content" class="preview-content">
                                <p class="text-muted">
                                    <i class="fas fa-arrow-up"></i> Fill in the fields above to see your work schedule preview
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-calculator"></i>
                            Pricing & Cost Breakdown
                        </h3>

                        <!-- Labor Cost Subsection -->
                        <div class="pricing-subsection">
                            <h4 class="subsection-title">
                                <i class="fas fa-user-hard-hat"></i> Labor Costs
                            </h4>
                            
                            <!-- Labor Pricing Method -->
                            <div class="form-group">
                                <label class="form-label">Labor Pricing Method <span class="required">*</span></label>
                                <div class="radio-group-grid">
                                    <label class="radio-card">
                                        <input type="radio" name="labor_pricing_method" value="fixed" checked>
                                        <span class="radio-card-content">
                                            <i class="fas fa-hand-holding-usd"></i>
                                            <strong>Fixed Price</strong>
                                        </span>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="labor_pricing_method" value="hourly">
                                        <span class="radio-card-content">
                                            <i class="fas fa-clock"></i>
                                            <strong>Per Hour</strong>
                                        </span>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="labor_pricing_method" value="per_sqm">
                                        <span class="radio-card-content">
                                            <i class="fas fa-ruler-combined"></i>
                                            <strong>Per m²</strong>
                                        </span>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="labor_pricing_method" value="per_unit">
                                        <span class="radio-card-content">
                                            <i class="fas fa-boxes"></i>
                                            <strong>Per Unit</strong>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Unit-Based Labor Pricing -->
                            <div id="labor-unit-pricing" class="unit-pricing-section" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">
                                            Unit Price (LKR) <span class="required">*</span>
                                            <span class="unit-label" id="labor-unit-label"></span>
                                        </label>
                                        <input type="number" id="labor-unit-price" class="form-input" 
                                            placeholder="Enter price per unit" min="0" step="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">
                                            <span id="labor-quantity-label">Quantity</span> <span class="required">*</span>
                                        </label>
                                        <input type="number" id="labor-quantity" class="form-input form-input-calculated" 
                                            placeholder="Auto-filled from Work Schedule" min="0" step="0.01" readonly>
                                    </div>
                                </div>

                                <!-- Labor Cost Breakdown -->
                                <div class="cost-breakdown-small">
                                    <div class="breakdown-row">
                                        <span><span id="labor-breakdown-qty-label">Quantity</span>:</span>
                                        <span id="labor-breakdown-qty">0</span>
                                    </div>
                                    <div class="breakdown-row">
                                        <span>Unit Price:</span>
                                        <span id="labor-breakdown-unit">LKR 0.00</span>
                                    </div>
                                    <div class="breakdown-row highlight">
                                        <span>= Labor Cost:</span>
                                        <span id="labor-breakdown-total">LKR 0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Labor Cost (Read-only when calculated) -->
                            <div class="form-group">
                                <label class="form-label" for="labor-cost">
                                    Total Labor Cost (LKR) <span class="required">*</span>
                                </label>
                                <input type="number" id="labor-cost" name="labor_cost" class="form-input form-input-calculated" 
                                    placeholder="0.00" min="0" step="0.01" required>
                            </div>
                        </div>

                        <!-- Material Cost Subsection -->
                        <div class="pricing-subsection">
                            <h4 class="subsection-title">
                                <i class="fas fa-boxes"></i> Material Costs
                            </h4>

                            <!-- Material Supply Checkbox -->
                            <div class="form-group">
                                <label class="checkbox-option material-supply-toggle">
                                    <input type="checkbox" id="vendor-supplies-materials" name="vendor_supplies_materials" value="1">
                                    <span class="checkbox-label">
                                        <strong>I will supply the materials for this job</strong>
                                        <small class="checkbox-hint">Check this if you're providing all materials. Leave unchecked if customer supplies materials.</small>
                                    </span>
                                </label>
                            </div>

                            <!-- Material Pricing Details (Shown when checkbox is checked) -->
                            <div id="material-pricing-section" style="display: none;">
                                
                                <!-- Material Pricing Method -->
                                <div class="form-group">
                                    <label class="form-label">Material Pricing Method <span class="required">*</span></label>
                                    <div class="radio-group-grid">
                                        <label class="radio-card">
                                            <input type="radio" name="material_pricing_method" value="fixed" checked>
                                            <span class="radio-card-content">
                                                <i class="fas fa-hand-holding-usd"></i>
                                                <strong>Fixed Price</strong>
                                            </span>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="material_pricing_method" value="per_sqm">
                                            <span class="radio-card-content">
                                                <i class="fas fa-ruler-combined"></i>
                                                <strong>Per m²</strong>
                                            </span>
                                        </label>
                                        <label class="radio-card">
                                            <input type="radio" name="material_pricing_method" value="per_unit">
                                            <span class="radio-card-content">
                                                <i class="fas fa-boxes"></i>
                                                <strong>Per Unit</strong>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Unit-Based Material Pricing -->
                                <div id="material-unit-pricing" class="unit-pricing-section" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label">
                                                Unit Price (LKR) <span class="required">*</span>
                                                <span class="unit-label" id="material-unit-label"></span>
                                            </label>
                                            <input type="number" id="material-unit-price" class="form-input" 
                                                placeholder="Enter price per unit" min="0" step="0.01">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">
                                                <span id="material-quantity-label">Quantity</span> <span class="required">*</span>
                                            </label>
                                            <input type="number" id="material-quantity" class="form-input" 
                                                placeholder="Enter quantity" min="0" step="0.01">
                                        </div>
                                    </div>

                                    <!-- Material Cost Breakdown -->
                                    <div class="cost-breakdown-small">
                                        <div class="breakdown-row">
                                            <span><span id="material-breakdown-qty-label">Quantity</span>:</span>
                                            <span id="material-breakdown-qty">0</span>
                                        </div>
                                        <div class="breakdown-row">
                                            <span>Unit Price:</span>
                                            <span id="material-breakdown-unit">LKR 0.00</span>
                                        </div>
                                        <div class="breakdown-row highlight">
                                            <span>= Material Cost:</span>
                                            <span id="material-breakdown-total">LKR 0.00</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Material Cost -->
                                <div class="form-group">
                                    <label class="form-label" for="material-cost">
                                        Total Material Cost (LKR) <span class="required">*</span>
                                    </label>
                                    <input type="number" id="material-cost" name="material_cost" class="form-input form-input-calculated" 
                                        placeholder="0.00" min="0" step="0.01" value="0">
                                </div>
                            </div>

                            <!-- Message when materials not supplied by vendor -->
                            <div id="material-not-supplied-message" class="info-message">
                                <i class="fas fa-info-circle"></i>
                                <span>Materials will be supplied by the customer. No material cost included in this quotation.</span>
                            </div>
                        </div>

                        <!-- Other Costs -->
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="transport-cost">
                                    <i class="fas fa-truck"></i> Transport Cost (LKR)
                                </label>
                                <input type="number" id="transport-cost" name="transport_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" value="0">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="other-cost">
                                    <i class="fas fa-receipt"></i> Other Charges (LKR)
                                </label>
                                <input type="number" id="other-cost" name="other_cost" class="form-input" 
                                    placeholder="0.00" min="0" step="0.01" value="0">
                            </div>
                        </div>

                        <!-- Final Cost Summary -->
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

                    <!-- ============================================================ -->
                    <!-- BUSINESS LOGIC: Payment Method Section -->
                    <!-- ============================================================ -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-credit-card"></i>
                            Payment Method
                        </h3>

                        <div class="form-group">
                            <label class="form-label">Select Payment Structure <span class="required">*</span></label>
                            <select id="payment-method" name="payment_method" class="form-select" required onchange="updatePaymentMethodInfo()">
                                <option value="">Choose payment method</option>
                                <option value="milestone">Milestone-Based Payment</option>
                                <option value="50-50">50% Upfront, 50% on Completion</option>
                                <option value="30-70">30% Upfront, 70% on Completion</option>
                                <option value="upfront_final">100% Upfront Payment</option>
                                <option value="time_material">Time & Material (Hourly Rate)</option>
                            </select>
                        </div>

                        <!-- Payment Method Information Box -->
                        <div id="payment-method-info" style="display: none;">
                            <!-- Info will be populated by JavaScript -->
                        </div>

                        <!-- Hourly Rate Field (shown only for Time & Material) -->
                        <div id="hourly-rate-section" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Hourly Rate (LKR) <span class="required">*</span></label>
                                <input type="number" id="hourly-rate" name="hourly_rate" class="form-input" 
                                       placeholder="Enter your hourly rate" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Spending Cap (optional for Time & Material) -->
                        <div id="spending-cap-section" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Maximum Spending Cap Multiplier (Optional)</label>
                                <input type="number" id="spending-cap" name="spending_cap_multiplier" class="form-input" 
                                       value="1.5" min="1" max="2" step="0.1"
                                       placeholder="Enter multiplier (e.g., 1.5 = 150% of estimate)">
                                <small class="form-hint">
                                    Default: 1.5x (Project will stop if costs exceed estimate × multiplier)
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Conditions Section -->
                    <div class="form-section">
                        <h3 class="form-section-title">
                            <i class="fas fa-file-contract"></i>
                            Terms & Conditions
                        </h3>

                        <!-- Payment Terms field REMOVED - now linked to Payment Method section above -->

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
    <script>
        // Pass PHP session data to JavaScript
        window.CURRENT_USER_ID = <?php echo json_encode($userId); ?>;
    </script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/sidebar.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/repair-requests-db.js"></script>

    <script>
        // Additional scripts if needed


        // ========================================
        // ENHANCED PRICING CALCULATION SYSTEM
        // ========================================

        class QuotationPricingCalculator {
            constructor() {
                this.initializeEventListeners();
            }

            initializeEventListeners() {
                // Labor pricing method listeners
                document.querySelectorAll('input[name="labor_pricing_method"]').forEach(radio => {
                    radio.addEventListener('change', (e) => this.handleLaborMethodChange(e.target.value));
                });

                // Labor calculation inputs
                const laborUnitPrice = document.getElementById('labor-unit-price');
                const laborQuantity = document.getElementById('labor-quantity');
                const laborCost = document.getElementById('labor-cost');

                if (laborUnitPrice) laborUnitPrice.addEventListener('input', () => this.calculateLaborCost());
                if (laborQuantity) laborQuantity.addEventListener('input', () => this.calculateLaborCost());
                if (laborCost) laborCost.addEventListener('input', () => this.updateTotal());

                // Material supply checkbox
                const materialSupplyCheckbox = document.getElementById('vendor-supplies-materials');
                if (materialSupplyCheckbox) {
                    materialSupplyCheckbox.addEventListener('change', (e) => this.toggleMaterialPricing(e.target.checked));
                }

                // Material pricing method listeners
                document.querySelectorAll('input[name="material_pricing_method"]').forEach(radio => {
                    radio.addEventListener('change', (e) => this.handleMaterialMethodChange(e.target.value));
                });

                // Material calculation inputs
                const materialUnitPrice = document.getElementById('material-unit-price');
                const materialQuantity = document.getElementById('material-quantity');
                const materialCost = document.getElementById('material-cost');

                if (materialUnitPrice) materialUnitPrice.addEventListener('input', () => this.calculateMaterialCost());
                if (materialQuantity) materialQuantity.addEventListener('input', () => this.calculateMaterialCost());
                if (materialCost) materialCost.addEventListener('input', () => this.updateTotal());

                // Other costs
                const transportCost = document.getElementById('transport-cost');
                const otherCost = document.getElementById('other-cost');

                if (transportCost) transportCost.addEventListener('input', () => this.updateTotal());
                if (otherCost) otherCost.addEventListener('input', () => this.updateTotal());
            }

            // ===== LABOR CALCULATION METHODS =====

            handleLaborMethodChange(method) {
                const unitPricingSection = document.getElementById('labor-unit-pricing');
                const laborCostInput = document.getElementById('labor-cost');
                const unitLabel = document.getElementById('labor-unit-label');
                const qtyLabel = document.getElementById('labor-quantity-label');
                const qtyBreakdownLabel = document.getElementById('labor-breakdown-qty-label');

                if (method === 'fixed') {
                    // Fixed pricing - hide unit pricing, allow manual input
                    if (unitPricingSection) unitPricingSection.style.display = 'none';
                    if (laborCostInput) {
                        laborCostInput.removeAttribute('readonly');
                        laborCostInput.classList.remove('form-input-calculated');
                    }
                } else {
                    // Unit-based pricing - show unit pricing, make total readonly
                    if (unitPricingSection) unitPricingSection.style.display = 'block';
                    if (laborCostInput) {
                        laborCostInput.setAttribute('readonly', true);
                        laborCostInput.classList.add('form-input-calculated');
                    }

                    // Update labels based on method
                    const labelConfig = {
                        'hourly': {
                            unitLabel: '(per hour)',
                            qtyLabel: 'Number of Hours',
                            placeholder: 'e.g., 8',
                            breakdownLabel: 'Hours'
                        },
                        'per_sqm': {
                            unitLabel: '(per m²)',
                            qtyLabel: 'Area (Square Meters)',
                            placeholder: 'e.g., 50',
                            breakdownLabel: 'Area (m²)'
                        },
                        'per_unit': {
                            unitLabel: '(per unit)',
                            qtyLabel: 'Number of Units',
                            placeholder: 'e.g., 10',
                            breakdownLabel: 'Units'
                        }
                    };

                    const config = labelConfig[method];
                    if (config) {
                        if (unitLabel) unitLabel.textContent = config.unitLabel;
                        if (qtyLabel) qtyLabel.textContent = config.qtyLabel;
                        if (qtyBreakdownLabel) qtyBreakdownLabel.textContent = config.breakdownLabel;
                        
                        const qtyInput = document.getElementById('labor-quantity');
                        if (qtyInput) qtyInput.placeholder = config.placeholder;
                    }
                }

                this.calculateLaborCost();
            }

            calculateLaborCost() {
                const method = document.querySelector('input[name="labor_pricing_method"]:checked')?.value;
                const laborCostInput = document.getElementById('labor-cost');

                if (!method || !laborCostInput) return;

                if (method === 'fixed') {
                    // For fixed pricing, just update the total
                    this.updateTotal();
                    return;
                }

                // Unit-based calculation
                const unitPrice = parseFloat(document.getElementById('labor-unit-price')?.value) || 0;
                const quantity = parseFloat(document.getElementById('labor-quantity')?.value) || 0;
                const totalCost = unitPrice * quantity;

                // Update labor cost input
                laborCostInput.value = totalCost.toFixed(2);

                // Update breakdown display
                const breakdownUnit = document.getElementById('labor-breakdown-unit');
                const breakdownQty = document.getElementById('labor-breakdown-qty');
                const breakdownTotal = document.getElementById('labor-breakdown-total');

                if (breakdownUnit) breakdownUnit.textContent = `LKR ${unitPrice.toFixed(2)}`;
                if (breakdownQty) breakdownQty.textContent = quantity.toFixed(2);
                if (breakdownTotal) breakdownTotal.textContent = `LKR ${totalCost.toFixed(2)}`;

                this.updateTotal();
            }

            // ===== MATERIAL CALCULATION METHODS =====

            toggleMaterialPricing(isSupplied) {
                const pricingSection = document.getElementById('material-pricing-section');
                const notSuppliedMessage = document.getElementById('material-not-supplied-message');
                const materialCostInput = document.getElementById('material-cost');

                if (isSupplied) {
                    if (pricingSection) pricingSection.style.display = 'block';
                    if (notSuppliedMessage) notSuppliedMessage.style.display = 'none';
                    if (materialCostInput) materialCostInput.required = true;
                } else {
                    if (pricingSection) pricingSection.style.display = 'none';
                    if (notSuppliedMessage) notSuppliedMessage.style.display = 'flex';
                    if (materialCostInput) {
                        materialCostInput.value = '0.00';
                        materialCostInput.required = false;
                    }
                    this.updateTotal();
                }
            }

            handleMaterialMethodChange(method) {
                const unitPricingSection = document.getElementById('material-unit-pricing');
                const materialCostInput = document.getElementById('material-cost');
                const unitLabel = document.getElementById('material-unit-label');
                const qtyLabel = document.getElementById('material-quantity-label');
                const qtyBreakdownLabel = document.getElementById('material-breakdown-qty-label');

                if (method === 'fixed') {
                    // Fixed pricing
                    if (unitPricingSection) unitPricingSection.style.display = 'none';
                    if (materialCostInput) {
                        materialCostInput.removeAttribute('readonly');
                        materialCostInput.classList.remove('form-input-calculated');
                    }
                } else {
                    // Unit-based pricing
                    if (unitPricingSection) unitPricingSection.style.display = 'block';
                    if (materialCostInput) {
                        materialCostInput.setAttribute('readonly', true);
                        materialCostInput.classList.add('form-input-calculated');
                    }

                    // Update labels
                    const labelConfig = {
                        'per_sqm': {
                            unitLabel: '(per m²)',
                            qtyLabel: 'Area (Square Meters)',
                            placeholder: 'e.g., 50',
                            breakdownLabel: 'Area (m²)'
                        },
                        'per_unit': {
                            unitLabel: '(per unit)',
                            qtyLabel: 'Number of Units',
                            placeholder: 'e.g., 10',
                            breakdownLabel: 'Units'
                        }
                    };

                    const config = labelConfig[method];
                    if (config) {
                        if (unitLabel) unitLabel.textContent = config.unitLabel;
                        if (qtyLabel) qtyLabel.textContent = config.qtyLabel;
                        if (qtyBreakdownLabel) qtyBreakdownLabel.textContent = config.breakdownLabel;
                        
                        const qtyInput = document.getElementById('material-quantity');
                        if (qtyInput) qtyInput.placeholder = config.placeholder;
                    }
                }

                this.calculateMaterialCost();
            }

            calculateMaterialCost() {
                const method = document.querySelector('input[name="material_pricing_method"]:checked')?.value;
                const materialCostInput = document.getElementById('material-cost');

                if (!method || !materialCostInput) return;

                if (method === 'fixed') {
                    this.updateTotal();
                    return;
                }

                // Unit-based calculation
                const unitPrice = parseFloat(document.getElementById('material-unit-price')?.value) || 0;
                const quantity = parseFloat(document.getElementById('material-quantity')?.value) || 0;
                const totalCost = unitPrice * quantity;

                // Update material cost input
                materialCostInput.value = totalCost.toFixed(2);

                // Update breakdown display
                const breakdownUnit = document.getElementById('material-breakdown-unit');
                const breakdownQty = document.getElementById('material-breakdown-qty');
                const breakdownTotal = document.getElementById('material-breakdown-total');

                if (breakdownUnit) breakdownUnit.textContent = `LKR ${unitPrice.toFixed(2)}`;
                if (breakdownQty) breakdownQty.textContent = quantity.toFixed(2);
                if (breakdownTotal) breakdownTotal.textContent = `LKR ${totalCost.toFixed(2)}`;

                this.updateTotal();
            }

            // ===== TOTAL CALCULATION =====

            updateTotal() {
                const laborCost = parseFloat(document.getElementById('labor-cost')?.value) || 0;
                const materialCost = parseFloat(document.getElementById('material-cost')?.value) || 0;
                const transportCost = parseFloat(document.getElementById('transport-cost')?.value) || 0;
                const otherCost = parseFloat(document.getElementById('other-cost')?.value) || 0;

                const subtotal = laborCost + materialCost + transportCost + otherCost;
                const total = subtotal;

                // Update display
                const subtotalDisplay = document.getElementById('subtotal-amount');
                const totalDisplay = document.getElementById('total-amount');
                const totalInput = document.getElementById('total-price');

                if (subtotalDisplay) subtotalDisplay.textContent = `LKR ${subtotal.toFixed(2)}`;
                if (totalDisplay) totalDisplay.textContent = `LKR ${total.toFixed(2)}`;
                if (totalInput) totalInput.value = total.toFixed(2);
            }
        }

        // Initialize pricing calculator when modal is opened
        let pricingCalculator;
        document.addEventListener('DOMContentLoaded', function() {
            pricingCalculator = new QuotationPricingCalculator();
        });

        // ========================================================================
        // BUSINESS LOGIC: Budget Flexibility Functions
        // ========================================================================

        /**
         * Update budget range display when budget type or total changes
         */
        function updateBudgetDisplay() {
            const budgetType = document.querySelector('input[name="budget_type"]:checked')?.value;
            const totalAmount = parseFloat(document.getElementById('total-amount')?.value) || 0;
            const rangeDisplay = document.getElementById('budget-range-display');
            const rangeText = document.getElementById('budget-range-text');

            if (budgetType === 'flexible' && totalAmount > 0) {
                const minBudget = (totalAmount * 0.90).toFixed(2);
                const maxBudget = (totalAmount * 1.10).toFixed(2);
                
                if (rangeText) {
                    rangeText.textContent = `LKR ${parseFloat(minBudget).toLocaleString()} - LKR ${parseFloat(maxBudget).toLocaleString()}`;
                }
                if (rangeDisplay) {
                    rangeDisplay.style.display = 'block';
                }
            } else {
                if (rangeDisplay) {
                    rangeDisplay.style.display = 'none';
                }
            }
        }

        // Update budget display when total changes
        const totalAmountField = document.getElementById('total-amount');
        if (totalAmountField) {
            // Use MutationObserver to watch for value changes
            const observer = new MutationObserver(updateBudgetDisplay);
            observer.observe(totalAmountField, { attributes: true, attributeFilter: ['value'] });
            
            // Also listen to input events
            totalAmountField.addEventListener('input', updateBudgetDisplay);
        }

        // ========================================================================
        // BUSINESS LOGIC: Payment Method Functions
        // ========================================================================

        /**
         * Update payment method information box
         */
        function updatePaymentMethodInfo() {
            const paymentMethod = document.getElementById('payment-method')?.value;
            const infoBox = document.getElementById('payment-method-info');
            const hourlyRateSection = document.getElementById('hourly-rate-section');
            const spendingCapSection = document.getElementById('spending-cap-section');

            if (!paymentMethod || !infoBox) return;

            const paymentInfo = {
                'milestone': {
                    icon: 'fas fa-tasks',
                    title: 'Milestone-Based Payment',
                    description: 'Payment released in stages as project milestones are completed. Provides security for both parties.',
                    color: '#2196F3'
                },
                '50-50': {
                    icon: 'fas fa-balance-scale',
                    title: '50-50 Split Payment',
                    description: '50% paid upfront to start the project, remaining 50% paid upon successful completion.',
                    color: '#4CAF50'
                },
                '30-70': {
                    icon: 'fas fa-percentage',
                    title: '30-70 Split Payment',
                    description: '30% paid upfront, 70% paid upon completion. Lower initial commitment.',
                    color: '#FF9800'
                },
                'upfront_final': {
                    icon: 'fas fa-dollar-sign',
                    title: '100% Upfront Payment',
                    description: 'Full payment made before work begins. Usually for trusted relationships or small projects.',
                    color: '#9C27B0'
                },
                'time_material': {
                    icon: 'fas fa-clock',
                    title: 'Time & Material',
                    description: 'Pay based on actual hours worked and materials used. Hourly rate applies.',
                    color: '#F44336'
                }
            };

            const info = paymentInfo[paymentMethod];
            if (info) {
                infoBox.innerHTML = `
                    <div class="info-box" style="border-left-color: ${info.color}">
                        <i class="${info.icon}" style="color: ${info.color}"></i>
                        <div>
                            <strong>${info.title}</strong>
                            <p>${info.description}</p>
                        </div>
                    </div>
                `;
                infoBox.style.display = 'block';
            } else {
                infoBox.style.display = 'none';
            }

            // Show/hide hourly rate and spending cap sections
            const hourlyRateInput = document.getElementById('hourly-rate');
            if (paymentMethod === 'time_material') {
                if (hourlyRateSection) hourlyRateSection.style.display = 'block';
                if (spendingCapSection) spendingCapSection.style.display = 'block';
                // Make hourly rate required when Time & Material is selected
                if (hourlyRateInput) hourlyRateInput.required = true;
            } else {
                if (hourlyRateSection) hourlyRateSection.style.display = 'none';
                if (spendingCapSection) spendingCapSection.style.display = 'none';
                // Remove required attribute when not Time & Material
                if (hourlyRateInput) hourlyRateInput.required = false;
            }
        }
    </script>

    <!-- ============================================================ -->
    <!-- BUSINESS LOGIC: Additional Styles -->
    <!-- ============================================================ -->
    <style>
        .info-box {
            display: flex;
            gap: 12px;
            padding: 15px;
            background: #e8f4f8;
            border-left: 4px solid #2196F3;
            border-radius: 6px;
            margin-top: 10px;
        }

        .info-box i {
            color: #2196F3;
            font-size: 1.5rem;
            margin-top: 2px;
        }

        .info-box div {
            flex: 1;
        }

        .info-box strong {
            display: block;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .info-box p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .radio-group-inline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .radio-card small {
            display: block;
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
    </style>
</body>

</html>






