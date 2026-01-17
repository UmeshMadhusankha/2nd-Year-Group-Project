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
                                        <input type="number" id="labor-quantity" class="form-input" 
                                            placeholder="Enter quantity" min="0" step="0.01">
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
    </script>
</body>

</html>






