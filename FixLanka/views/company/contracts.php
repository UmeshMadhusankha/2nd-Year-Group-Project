<?php
/**
 * Company Contracts Page
 * 
 * This page allows companies to:
 * - View all their contracts
 * - Manage contract details
 * - Track contract status and milestones
 * - Filter and search contracts
 * 
 * Authentication: Requires logged-in company user
 * 
 * @package FixLanka\Views\Company
 * @version 1.0.0
 */

// Start session and verify authentication
require_once __DIR__ . '/../../config/session.php';
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
    <title>FixLanka - Contracts Management</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/contracts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/chat.css">
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>

            <!-- Contracts Page Content -->
            <section class="contracts-page">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-handshake"></i> Contracts Management</h1>
                                <p class="subtitle">Manage all your service contracts and agreements</p>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn secondary" id="exportBtn">
                                        <i class="fas fa-download"></i>
                                        Export
                                    </button>
                                    <button class="action-btn primary" id="newContractBtn">
                                        <i class="fas fa-plus"></i>
                                        New Contract
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Contracts</span>
                        </div>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-value" id="statActive">0</span>
                                <span class="stat-label">Active</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="statDraft">0</span>
                                <span class="stat-label">Draft</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value" id="statCompleted">0</span>
                                <span class="stat-label">Completed</span>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Filters and Search -->
                <div class="contracts-controls">
                    <div class="filter-section">
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="pending_signature">Awaiting Signature</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="expired">Expired</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="repair">Repair</option>
                                <option value="installation">Installation</option>
                                <option value="renovation">Renovation</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                        <div class="filter-select-wrapper">
                            <select class="filter-select" id="dateFilter">
                                <option value="">All Dates</option>
                                <option value="this-week">This Week</option>
                                <option value="this-month">This Month</option>
                                <option value="last-month">Last Month</option>
                                <option value="this-year">This Year</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                    </div>

                    <div class="view-options">
                        <button class="view-btn active" data-view="grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Contracts List/Grid -->
                <div class="contracts-container" id="contractsContainer">
                    <!-- Loading State -->
                    <div class="contracts-loading" id="contractsLoading">
                        <div class="loading-spinner"></div>
                        <p>Loading contracts...</p>
                    </div>

                    <!-- Empty State -->
                    <div class="contracts-empty" id="contractsEmpty" style="display: none;">
                        <i class="fas fa-file-contract fa-3x"></i>
                        <h3>No Contracts Found</h3>
                        <p>You don't have any contracts yet. Start by creating a new contract.</p>
                    </div>

                    <!-- Contracts will be dynamically loaded here by JavaScript -->
                </div>

                <!-- Load More / Infinite Scroll -->
                <div class="load-more-container">
                    <button class="load-more-btn" id="loadMoreBtn">
                        <i class="fas fa-plus"></i>
                        Load More Contracts
                    </button>
                </div>

                <!-- End of Contracts Indicator -->
                <div class="contracts-end" id="contractsEnd" style="display: none;">
                    <i class="fas fa-check-circle"></i>
                    You've reached the end of all contracts
                </div>
            </section>
        </main>
    </div>

    <!-- Scroll to Top Button -->
    <button class="scroll-to-top" id="scrollToTop">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- Contract Details Modal -->
    <div class="modal-overlay" id="contractModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2><i class="fas fa-file-contract"></i> Contract Details</h2>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content" id="modalContent">
                <div class="contract-preview" id="viewContractPreview">
                    <!-- Legal Document Header -->
                    <div class="preview-header">
                        <h2>CONSTRUCTION SERVICE AGREEMENT</h2>
                        <p class="preview-ref" id="viewRef">Contract Reference: -</p>
                        <p class="preview-date">Date: <span id="viewDate">-</span></p>
                        <div class="contract-status-badge" id="viewStatus">Draft</div>
                    </div>

                    <!-- Section 1: Parties -->
                    <div class="preview-section">
                        <h4>1. PARTIES TO THE CONTRACT</h4>
                        <div class="preview-parties">
                            <div>
                                <strong>First Party (Client):</strong>
                                <span id="viewClientName">-</span><br>
                                <small id="viewClientDetails">-</small>
                            </div>
                            <div>
                                <strong>Second Party (Contractor):</strong>
                                <span id="viewCompanyName">-</span><br>
                                <small id="viewCompanyDetails">-</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Project -->
                    <div class="preview-section">
                        <h4>2. PROJECT OVERVIEW</h4>
                        <div class="preview-grid">
                            <div><strong>Title:</strong> <span id="viewTitle">-</span></div>
                            <div><strong>Reference:</strong> <span id="viewProjectRef">-</span></div>
                            <div><strong>Location:</strong> <span id="viewLocation">-</span></div>
                            <div><strong>Type:</strong> <span id="viewType">-</span></div>
                        </div>
                        <p id="viewDescription" style="margin-top:8px;color:#4a5568;">-</p>
                    </div>

                    <!-- Section 3: Scope -->
                    <div class="preview-section" id="viewScopeSection">
                        <h4>3. SCOPE OF WORK</h4>
                        <p id="viewScopeDesc">-</p>
                        <div class="preview-grid" style="margin-top:10px;">
                            <div>
                                <strong>Inclusions:</strong>
                                <pre id="viewInclusions" class="preview-pre">-</pre>
                            </div>
                            <div>
                                <strong>Exclusions:</strong>
                                <pre id="viewExclusions" class="preview-pre">-</pre>
                            </div>
                        </div>
                        <p><strong>Materials:</strong> <span id="viewMaterials">-</span></p>
                    </div>

                    <!-- Section 4: Timeline & Milestones -->
                    <div class="preview-section">
                        <h4>4. PROJECT DURATION & MILESTONES</h4>
                        <div class="preview-grid cols-3">
                            <div><strong>Start:</strong> <span id="viewStartDate">-</span></div>
                            <div><strong>Completion:</strong> <span id="viewEndDate">-</span></div>
                            <div><strong>Progress:</strong> <span id="viewProgress">0%</span></div>
                        </div>
                        <div id="viewMilestonesContainer" style="margin-top:10px;"></div>
                    </div>

                    <!-- Section 5: Financial -->
                    <div class="preview-section">
                        <h4>5. PRICING, PAYMENTS & DELAYS</h4>
                        <div class="preview-grid cols-3">
                            <div><strong>Contract Value:</strong> <span id="viewValue" class="preview-value">-</span></div>
                            <div><strong>Budget Type:</strong> <span id="viewBudgetType">-</span></div>
                            <div><strong>Payment Method:</strong> <span id="viewPaymentMethod">-</span></div>
                        </div>
                        <div class="preview-grid" style="margin-top:10px;">
                            <div><strong>Amount Paid:</strong> <span id="viewAmountPaid">LKR 0</span></div>
                            <div><strong>Remaining:</strong> <span id="viewAmountPending">-</span></div>
                        </div>
                        <!-- Payment Schedule Breakdown -->
                        <div id="viewPaymentSchedule" style="margin-top:12px; border-top: 1px solid #e2e8f0; padding-top: 10px;"></div>
                        <p style="margin-top:8px;"><strong>Late Payment:</strong> <span id="viewLatePayment">As per standard terms</span></p>
                    </div>

                    <!-- Section 5.1: Budget Flexibility (Phase 3) -->
                    <div class="preview-section" id="budgetFlexibilitySection" style="display:none;">
                        <h4>5.1 BUDGET FLEXIBILITY</h4>
                        <div class="budget-info-box">
                            <div class="budget-type-display">
                                <span class="badge" id="budgetTypeBadge"></span>
                                <div id="budgetRangeInfo" style="display:none; margin-top: 10px;">
                                    <p><strong>Allowed Budget Range:</strong></p>
                                    <div class="preview-grid cols-2" style="margin-top: 5px;">
                                        <div>Minimum: <span id="viewBudgetMin" class="preview-value">-</span></div>
                                        <div>Maximum: <span id="viewBudgetMax" class="preview-value">-</span></div>
                                    </div>
                                    <p style="margin-top: 8px; font-size: 13px; color: #666;">
                                        <em>Final cost may vary within +/-10% range to accommodate material price changes or necessary adjustments. All changes require customer approval.</em>
                                    </p>
                                </div>
                                <div id="budgetFixedInfo" style="display:none; margin-top: 10px;">
                                    <p style="font-size: 13px; color: #666;">
                                        <em>Total cost is locked. No adjustments allowed without new quotation.</em>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Budget Adjustment Container -->
                        <div id="budgetAdjustmentContainer" style="margin-top: 15px;"></div>
                    </div>

                    <!-- Section 6: Variations -->
                    <div class="preview-section">
                        <h4>6. VARIATIONS & CHANGES</h4>
                        <p id="viewVariation">-</p>
                    </div>

                    <!-- Section 7: Communication & Disputes -->
                    <div class="preview-section">
                        <h4>7. COMMUNICATION & DISPUTE RESOLUTION</h4>
                        <p><strong>Channel:</strong> <span id="viewCommChannel">-</span></p>
                        <p id="viewDisputeRes">-</p>
                    </div>

                    <!-- Customer Response Status -->
                    <div class="preview-section" id="viewCustomerResponseSection" style="display:none;">
                        <h4>8. CUSTOMER RESPONSE</h4>
                        <div class="preview-grid">
                            <div><strong>Sent to Customer:</strong> <span id="viewSentStatus">-</span></div>
                            <div><strong>Response:</strong> <span id="viewCustomerResponse">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-actions">
                    <div class="modal-footer-left">
                        <button class="btn btn-outline" id="modalDownload">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </div>
                    <div class="modal-footer-right">
                        <button class="btn btn-primary" id="modalClose">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal-overlay" id="exportModal">
        <div class="modal-container export-modal">
            <div class="modal-header">
                <h2><i class="fas fa-download"></i> Export Contracts</h2>
                <button class="modal-close" id="exportModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="export-options">
                    <!-- Filter By Status Section -->
                    <div class="export-section">
                        <h3><i class="fas fa-filter"></i> Filter by Status</h3>
                        <div class="filter-grid">
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportAll" checked>
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-list export-icon all"></i>
                                    <div class="option-text">
                                        <span class="option-title">All Contracts</span>
                                        <span class="option-desc">Export every contract</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportActive">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-play-circle export-icon active"></i>
                                    <div class="option-text">
                                        <span class="option-title">Active Contracts</span>
                                        <span class="option-desc">Currently running projects</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportPending">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-clock export-icon pending"></i>
                                    <div class="option-text">
                                        <span class="option-title">Pending Contracts</span>
                                        <span class="option-desc">Awaiting approval or start</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportCompleted">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-check-circle export-icon completed"></i>
                                    <div class="option-text">
                                        <span class="option-title">Completed Contracts</span>
                                        <span class="option-desc">Successfully finished projects</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportCancelled">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-times-circle export-icon cancelled"></i>
                                    <div class="option-text">
                                        <span class="option-title">Cancelled Contracts</span>
                                        <span class="option-desc">Terminated or void contracts</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportExpired">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-hourglass-end export-icon expired"></i>
                                    <div class="option-text">
                                        <span class="option-title">Expired Contracts</span>
                                        <span class="option-desc">Past deadline contracts</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Special Categories Section -->
                    <div class="export-section">
                        <h3><i class="fas fa-exclamation-triangle"></i> Special Categories</h3>
                        <div class="special-filter-grid">
                            <label class="export-checkbox-option highlighted">
                                <input type="checkbox" id="exportWithIssues">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-exclamation-circle export-icon issues"></i>
                                    <div class="option-text">
                                        <span class="option-title">Contracts with Issues</span>
                                        <span class="option-desc">Requiring documents, signatures, or actions</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportHighValue">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-dollar-sign export-icon high-value"></i>
                                    <div class="option-text">
                                        <span class="option-title">High-Value Contracts</span>
                                        <span class="option-desc">Above average contract values</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-checkbox-option">
                                <input type="checkbox" id="exportRecentUpdates">
                                <span class="checkbox-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-sync-alt export-icon recent"></i>
                                    <div class="option-text">
                                        <span class="option-title">Recently Updated</span>
                                        <span class="option-desc">Modified in the last 30 days</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Date Range Section -->
                    <div class="export-section">
                        <h3><i class="fas fa-calendar-alt"></i> Date Range</h3>
                        <div class="date-range-options">
                            <div class="date-input-group">
                                <label for="exportStartDate">From:</label>
                                <input type="date" id="exportStartDate" class="date-input">
                            </div>
                            <div class="date-input-group">
                                <label for="exportEndDate">To:</label>
                                <input type="date" id="exportEndDate" class="date-input">
                            </div>
                        </div>
                        <div class="date-presets">
                            <button class="preset-btn" data-preset="7">Last 7 days</button>
                            <button class="preset-btn" data-preset="30">Last 30 days</button>
                            <button class="preset-btn" data-preset="90">Last 3 months</button>
                            <button class="preset-btn" data-preset="365">Last year</button>
                        </div>
                    </div>
                    <!-- Export Format Section -->
                    <div class="export-section">
                        <h3><i class="fas fa-file-alt"></i> Export Format</h3>
                        <div class="format-options">
                            <label class="export-radio-option">
                                <input type="radio" name="exportFormat" value="excel" checked>
                                <span class="radio-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-file-excel export-icon excel"></i>
                                    <div class="option-text">
                                        <span class="option-title">Excel (.xlsx)</span>
                                        <span class="option-desc">Best for data analysis and spreadsheets</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportFormat" value="pdf">
                                <span class="radio-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-file-pdf export-icon pdf"></i>
                                    <div class="option-text">
                                        <span class="option-title">PDF Document</span>
                                        <span class="option-desc">Professional reports and printing</span>
                                    </div>
                                </div>
                            </label>
                            <label class="export-radio-option">
                                <input type="radio" name="exportFormat" value="csv">
                                <span class="radio-custom"></span>
                                <div class="option-content">
                                    <i class="fas fa-file-csv export-icon csv"></i>
                                    <div class="option-text">
                                        <span class="option-title">CSV File</span>
                                        <span class="option-desc">Universal compatibility</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Export Summary -->
                    <div class="export-summary">
                        <div class="summary-content">
                            <i class="fas fa-info-circle"></i>
                            <span id="exportSummaryText">Ready to export all contracts</span>
                        </div>
                        <div class="estimated-count">
                            <span id="estimatedCount">Estimated: 0 contracts</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-outline" id="exportCancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-secondary" id="exportPreview">
                    <i class="fas fa-eye"></i> Preview
                </button>
                <button class="btn btn-primary" id="exportDownload">
                    <i class="fas fa-download"></i> Export Now
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Contract Creation Modal (8-Section Legal Form) -->
    <div class="modal-overlay" id="newContractModal">
        <div class="modal-container form-modal enhanced-contract-modal">
            <div class="modal-header">
                <h2><i class="fas fa-file-contract"></i> <span id="formModalTitle">Create Legal Contract</span></h2>
                <div class="modal-header-right">
                    <span class="draft-status" id="draftStatus" style="display:none;">
                        <i class="fas fa-save"></i> <span id="draftStatusText">Saved</span>
                    </span>
                    <button class="modal-close" id="newContractClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Precondition Error Banner (hidden by default) -->
            <div class="precondition-error" id="preconditionError" style="display:none;">
                <div class="precondition-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="precondition-msg" id="preconditionMsg">Cannot create contract.</div>
            </div>

            <div class="modal-content">
                <form id="contractForm" class="contract-form" autocomplete="off">
                    <input type="hidden" id="editContractId" name="editContractId" value="">
                    <!-- Progress Bar -->
                    <div class="form-progress-bar">
                        <div class="progress-track">
                            <div class="progress-fill" id="formProgressFill"></div>
                        </div>
                        <div class="progress-steps">
                            <div class="progress-step active" data-step="1">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Quotation</div>
                            </div>
                            <div class="progress-step" data-step="2">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Parties</div>
                            </div>
                            <div class="progress-step" data-step="3">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Project</div>
                            </div>
                            <div class="progress-step" data-step="4">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Scope</div>
                            </div>
                            <div class="progress-step" data-step="5">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Payments</div>
                            </div>
                            <div class="progress-step" data-step="6">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Timeline</div>
                            </div>
                            <div class="progress-step" data-step="7">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Clauses</div>
                            </div>
                            <div class="progress-step" data-step="8">
                                <div class="progress-dot"></div>
                                <div class="progress-label">Review</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden IDs -->
                    <input type="hidden" id="selectedQuotationId" name="quotation_id">
                    <input type="hidden" id="selectedRequestId" name="request_id">
                    <input type="hidden" id="customerId" name="customer_id">

                    <!-- =============================== -->
                    <!-- STEP 1: Select Accepted Quotation -->
                    <!-- =============================== -->
                    <div class="form-step-content active" data-step="1">
                        <div class="step-header">
                            <h3><i class="fas fa-clipboard-check"></i> Select Accepted Quotation</h3>
                            <p class="step-description">Choose an accepted quotation to auto-fill the contract. All fields will be populated automatically.</p>
                        </div>

                        <div class="quotation-selector-wrapper">
                            <div class="qs-header">
                                <i class="fas fa-clipboard-check qs-icon"></i>
                                <div>
                                    <h4>Available Quotations</h4>
                                    <p>Only accepted quotations without existing contracts are shown</p>
                                </div>
                                <span id="quotationCountBadge" class="qs-badge">Loading...</span>
                            </div>

                            <div class="form-group">
                                <select id="quotationSelector" class="form-control" required>
                                    <option value="">-- Select an Accepted Quotation --</option>
                                </select>
                            </div>

                            <div id="quotationPreviewCard" class="qs-preview" style="display: none;"></div>

                            <div class="qs-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Contracts can only be created from accepted quotations. The form will auto-fill with quotation data.</span>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- STEP 2: Parties to the Contract -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="2">
                        <div class="step-header">
                            <h3><i class="fas fa-users"></i> Section 1 - Parties to the Contract</h3>
                            <p class="step-description">This section defines who is legally bound. All data is auto-filled and <strong>read-only</strong> to prevent legal errors.</p>
                        </div>

                        <div class="parties-grid">
                            <!-- Client Card -->
                            <div class="party-card">
                                <div class="party-card-header client">
                                    <i class="fas fa-user-tie"></i>
                                    <h4>Client (First Party)</h4>
                                </div>
                                <div class="party-card-body">
                                    <div class="party-field">
                                        <label>Full Name</label>
                                        <div class="party-value" id="partyClientName">-</div>
                                        <input type="hidden" id="clientName" name="client_name">
                                    </div>
                                    <div class="party-field">
                                        <label>Address</label>
                                        <div class="party-value" id="partyClientAddress">-</div>
                                    </div>
                                    <div class="party-field">
                                        <label>Email</label>
                                        <div class="party-value" id="partyClientEmail">-</div>
                                        <input type="hidden" id="clientEmail" name="client_email">
                                    </div>
                                    <div class="party-field">
                                        <label>District</label>
                                        <div class="party-value" id="partyClientDistrict">-</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Card -->
                            <div class="party-card">
                                <div class="party-card-header company">
                                    <i class="fas fa-building"></i>
                                    <h4>Company (Second Party)</h4>
                                </div>
                                <div class="party-card-body">
                                    <div class="party-field">
                                        <label>Company Name</label>
                                        <div class="party-value" id="partyCompanyName">-</div>
                                    </div>
                                    <div class="party-field">
                                        <label>Business Registration No.</label>
                                        <div class="party-value" id="partyCompanyReg">-</div>
                                    </div>
                                    <div class="party-field">
                                        <label>Registered Address</label>
                                        <div class="party-value" id="partyCompanyAddress">-</div>
                                    </div>
                                    <div class="party-field">
                                        <label>Contact</label>
                                        <div class="party-value" id="partyCompanyContact">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="qs-info" style="margin-top:15px;">
                            <i class="fas fa-lock"></i>
                            <span>Party details are locked to prevent legal inconsistency. To change, update the respective profiles.</span>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- STEP 3: Project Overview -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="3">
                        <div class="step-header">
                            <h3><i class="fas fa-project-diagram"></i> Section 2 - Project Overview</h3>
                            <p class="step-description">Clearly identify which project this contract applies to. Auto-filled from the quotation; editable while in draft.</p>
                        </div>

                        <div class="legal-section">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="projectTitle">Project Title <span class="required">*</span></label>
                                    <input type="text" id="projectTitle" name="project_title" required placeholder="e.g., Roof Repair - Colombo 7">
                                </div>
                                <div class="form-group">
                                    <label for="projectReference">Project Reference ID</label>
                                    <input type="text" id="projectReference" name="project_reference" readonly class="readonly-field" placeholder="Auto-generated">
                                </div>
                                <div class="form-group">
                                    <label for="projectLocation">Project Location <span class="required">*</span></label>
                                    <input type="text" id="projectLocation" name="project_location" required placeholder="Full address of the project site">
                                </div>
                                <div class="form-group">
                                    <label for="projectType">Project Type</label>
                                    <input type="text" id="projectType" name="project_type" placeholder="e.g., Renovation, Repair, Construction">
                                </div>
                                <div class="form-group full-width">
                                    <label for="projectDescription">Project Description <span class="required">*</span></label>
                                    <textarea id="projectDescription" name="project_description" rows="4" required placeholder="Brief description of the project scope and objectives..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- STEP 4: Scope of Work -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="4">
                        <div class="step-header">
                            <h3><i class="fas fa-tasks"></i> Section 3 - Scope of Work</h3>
                            <p class="step-description">This is the <strong>most critical section</strong>. Define exactly what work is included and excluded.</p>
                        </div>

                        <div class="legal-section critical-section">
                            <div class="form-group full-width">
                                <label for="scopeDescription">Detailed Work Description <span class="required">*</span></label>
                                <textarea id="scopeDescription" name="scope_description" rows="5" required placeholder="Describe all work to be performed in detail..."></textarea>
                                <small class="field-hint">Be as specific as possible. This is the primary reference for what the contractor must deliver.</small>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="scopeInclusions">Inclusions</label>
                                    <textarea id="scopeInclusions" name="scope_inclusions" rows="4" placeholder="List what IS included:&#10;&bull; All labour costs&#10;&bull; Standard materials&#10;&bull; Site cleanup"></textarea>
                                    <small class="field-hint">What the contract price covers</small>
                                </div>
                                <div class="form-group">
                                    <label for="scopeExclusions">Exclusions</label>
                                    <textarea id="scopeExclusions" name="scope_exclusions" rows="4" placeholder="List what is NOT included:&#10;&bull; Permits & licenses&#10;&bull; Structural changes&#10;&bull; Furniture removal"></textarea>
                                    <small class="field-hint">What is explicitly NOT covered</small>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label for="scopeStandards">Standards & Specifications</label>
                                <textarea id="scopeStandards" name="scope_standards" rows="3" placeholder="Any building codes, quality standards, or specifications that apply..."></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Materials Responsibility <span class="required">*</span></label>
                                <div class="radio-group">
                                    <label class="radio-option">
                                        <input type="radio" name="materials_responsibility" value="company" checked>
                                        <span class="radio-custom"></span>
                                        <div>
                                            <strong>Company supplies all materials</strong>
                                            <small>All materials sourced and provided by the contractor</small>
                                        </div>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="materials_responsibility" value="client">
                                        <span class="radio-custom"></span>
                                        <div>
                                            <strong>Client supplies all materials</strong>
                                            <small>Client procures and provides all materials on site</small>
                                        </div>
                                    </label>
                                    <label class="radio-option">
                                        <input type="radio" name="materials_responsibility" value="shared">
                                        <span class="radio-custom"></span>
                                        <div>
                                            <strong>Shared responsibility</strong>
                                            <small>Materials are split between both parties as agreed</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- STEP 5: Timeline & Milestones -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="5">
                        <div class="step-header">
                            <h3><i class="fas fa-file-invoice-dollar"></i> Section 4 - Payment Terms</h3>
                            <p class="step-description">Define the financial terms of the contract including pricing structure, payment schedule, and consequences for delays.</p>
                        </div>

                        <div class="legal-section">
                            <!-- Pricing -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-tags"></i> Contract Pricing</h4>
                                
                                <div class="form-grid">
                                    <input type="hidden" id="contractValue" name="total_budget">
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="budgetType">Contract Type <span class="required">*</span></label>
                                        <select id="budgetType" name="budget_type" required>
                                            <option value="fixed">Fixed Price</option>
                                            <option value="flexible">Flexible (Materials)</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="taxInclusive">Tax Inclusion</label>
                                        <select id="taxInclusive" name="tax_inclusive">
                                            <option value="1">All taxes included</option>
                                            <option value="0">Taxes additional</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-grid" id="budgetFlexRow" style="display:none;">
                                    <div class="form-group">
                                        <label for="budgetFlexPercent">Material Price Flexibility (%)</label>
                                        <input type="number" id="budgetFlexPercent" name="budget_flexibility_percentage" min="0" max="100" step="0.5" value="10">
                                        <small>Applies to materials cost only (increase/decrease)</small>
                                    </div>
                                </div>
                                <div class="form-grid" id="budgetRangeRow" style="display:none;">
                                    <div class="form-group">
                                        <label for="budgetMin">Minimum (LKR)</label>
                                        <input type="number" id="budgetMin" name="budget_min" readonly class="readonly-field">
                                    </div>
                                    <div class="form-group">
                                        <label for="budgetMax">Maximum (LKR)</label>
                                        <input type="number" id="budgetMax" name="budget_max" readonly class="readonly-field">
                                    </div>
                                </div>

                                <!-- Cost Breakdown (read-only from quotation) -->
                                <div class="cost-breakdown" id="costBreakdown" style="display:none;">
                                    <h5>Cost Breakdown (from Quotation)</h5>
                                    <div class="breakdown-grid">
                                        <div class="breakdown-item"><span id="bdLabourLabel">Labour</span><span id="bdLabour">-</span></div>
                                        <div class="breakdown-item"><span id="bdMaterialsLabel">Materials</span><span id="bdMaterials">-</span></div>
                                        <div class="breakdown-item"><span id="bdTransportLabel">Transport</span><span id="bdTransport">-</span></div>
                                        <div class="breakdown-item"><span id="bdOtherLabel">Other</span><span id="bdOther">-</span></div>
                                        <div class="breakdown-item total"><span>Total Per Unit</span><span id="bdTotal">-</span></div>
                                    </div>
                                </div>

                                <div class="qs-info" id="unitMeasurementInfo" style="display:none;">
                                    <i class="fas fa-ruler-combined"></i>
                                    <span id="unitMeasurementText">Unit measurement: -</span>
                                </div>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-credit-card"></i> Payment Schedule</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="paymentMethod">Payment Method <span class="required">*</span></label>
                                        <select id="paymentMethod" name="payment_method" required>
                                            <option value="full_upfront">Full Payment Upfront (100%)</option>
                                            <option value="milestone_based">Milestone-Based Payments</option>
                                            <option value="50_50">50% Upfront + 50% on Completion</option>
                                            <option value="30_70">30% Upfront + 70% on Completion</option>
                                            <option value="completion">100% After Completion</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pricingType">Pricing Structure</label>
                                        <select id="pricingType" name="pricing_type">
                                            <option value="fixed_price">Fixed Price</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-grid" id="hourlyRateRow" style="display:none;">
                                    <div class="form-group">
                                        <label for="hourlyRate">Hourly Rate (LKR)</label>
                                        <input type="number" id="hourlyRate" name="hourly_rate" min="0" step="100" placeholder="e.g., 5000">
                                    </div>
                                    <div class="form-group">
                                        <label for="spendingCap">Spending Cap (LKR)</label>
                                        <input type="number" id="spendingCap" name="spending_cap" readonly class="readonly-field" placeholder="Auto: 110% of budget">
                                    </div>
                                </div>


                            </div>

                            <!-- Delay Handling -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-clock"></i> Delay & Late Payment Handling</h4>
                                <div class="form-group full-width">
                                    <label for="latePaymentPenalty">Late Payment Consequences</label>
                                    <textarea id="latePaymentPenalty" name="late_payment_penalty" rows="2" placeholder="e.g., Interest of 2% per month on overdue payments after a 7-day grace period.">Interest of 2% per month on overdue payments after a 7-day grace period.</textarea>
                                </div>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="checkbox-label">
                                            <input type="checkbox" id="pauseWorkClause" name="pause_work_clause" value="1" checked>
                                            <span>Company may pause work if payment is overdue by 14+ days</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="checkbox-label">
                                            <input type="checkbox" id="timeExtensionClause" name="time_extension_clause" value="1" checked>
                                            <span>Automatic time extension for client-caused delays</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->

                    <!-- STEP 6: Pricing, Payments & Delays -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="6">
                        <div class="step-header">
                            <h3><i class="fas fa-calendar-alt"></i> Section 5 - Project Duration & Milestones</h3>
                            <p class="step-description">Set the project timeline and define key milestones for tracking contract progress.</p>
                        </div>

                        <div class="legal-section">
                            <div class="form-grid cols-3">
                                <div class="form-group">
                                    <label for="startDate">Start Date <span class="required">*</span></label>
                                    <input type="date" id="startDate" name="start_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="endDate">Expected Completion <span class="required">*</span></label>
                                    <input type="date" id="endDate" name="end_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="estimatedDuration">Working Days</label>
                                    <input type="number" id="estimatedDuration" name="estimated_duration" min="1" readonly class="readonly-field" placeholder="Auto">
                                </div>
                            </div>

                            <div class="milestones-section">
                                <div class="milestones-header">
                                    <div style="display: flex; align-items: center;">
                                        <h4 style="margin: 0;">
                                            <i class="fas fa-flag-checkered"></i> Project Timeline
                                        </h4>
                                        <div class="tooltip-container" style="position: relative; display: inline-block; margin-left: 8px;">
                                            <i class="fas fa-info-circle ms-info-icon" style="font-size: 0.8em; color: #6c757d; cursor: help;"></i>
                                            <div class="custom-tooltip" style="visibility: hidden; width: 250px; background-color: #1e293b; color: #f8fafc; text-align: center; border-radius: 8px; padding: 12px; position: absolute; z-index: 100; bottom: 150%; left: 50%; transform: translateX(-50%); opacity: 0; transition: opacity 0.2s, visibility 0.2s; font-size: 12px; font-weight: normal; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); line-height: 1.5; text-transform: none;">
                                                Define key checkpoints (phases) for the project. Use only phase name, description, and target date to track progress.
                                                <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border-width: 6px; border-style: solid; border-color: #1e293b transparent transparent transparent;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-sm btn-add" id="addMilestoneBtn">
                                        <i class="fas fa-plus"></i> Add Phase
                                    </button>
                                </div>

                                <div class="milestones-table-wrapper">
                                    <table class="milestones-table" id="milestonesTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Phase Name</th>
                                                <th>Description</th>
                                                <th>Target Date</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="milestonesBody">
                                            <tr class="milestone-row">
                                                <td>1</td>
                                                <td><input type="text" name="ms_name[]" placeholder="Project Start" value="Project Commencement"></td>
                                                <td><input type="text" name="ms_desc[]" placeholder="Description" value="Site preparation and initial setup"></td>
                                                <td><input type="date" name="ms_date[]"></td>
                                                <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                                            </tr>
                                            <tr class="milestone-row">
                                                <td>2</td>
                                                <td><input type="text" name="ms_name[]" placeholder="Midpoint" value="Mid-Project Review"></td>
                                                <td><input type="text" name="ms_desc[]" placeholder="Description" value="Progress inspection and quality check"></td>
                                                <td><input type="date" name="ms_date[]"></td>
                                                <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                                            </tr>
                                            <tr class="milestone-row">
                                                <td>3</td>
                                                <td><input type="text" name="ms_name[]" placeholder="Completion" value="Project Handover"></td>
                                                <td><input type="text" name="ms_desc[]" placeholder="Description" value="Final inspection, cleanup, and handover"></td>
                                                <td><input type="date" name="ms_date[]"></td>
                                                <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->

                    <!-- STEP 7: Variations, Communication, Additional Terms -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="7">
                        <div class="step-header">
                            <h3><i class="fas fa-gavel"></i> Sections 6 & 7 - Variations, Communication & Additional Terms</h3>
                            <p class="step-description">Legal clauses for change control, dispute resolution, and any additional terms.</p>
                        </div>

                        <div class="legal-section">
                            <!-- Variation Clause -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-exchange-alt"></i> Variations & Changes (Section 6)</h4>
                                <div class="clause-card">
                                    <div class="clause-toggle">
                                        <label class="checkbox-label">
                                            <input type="checkbox" id="variationClause" name="variation_clause" value="1" checked>
                                            <span><strong>Enable Variation Control</strong></span>
                                        </label>
                                    </div>
                                    <div class="clause-body" id="variationClauseBody">
                                        <p class="clause-text">
                                            Any change to the scope of work, pricing, materials, or timeline requires written approval from <strong>both parties</strong> before execution.
                                            Changes will be processed as Variation Requests within the FixLanka platform and must be signed off before work proceeds.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Communication -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-comments"></i> Communication & Dispute Resolution (Section 7)</h4>
                                <div class="form-group">
                                    <label for="communicationChannel">Official Communication Channel</label>
                                    <select id="communicationChannel" name="communication_channel">
                                        <option value="system">FixLanka Platform Messaging (Recommended)</option>
                                    </select>
                                    <small class="field-hint">All contract-related communication must use FixLanka chat (email is not supported).</small>
                                </div>
                                <div class="form-group full-width">
                                    <label for="disputeResolution">Dispute Resolution Process</label>
                                    <textarea id="disputeResolution" name="dispute_resolution" rows="3" placeholder="Default: Negotiation -> Mediation via FixLanka -> External arbitration">Both parties agree to attempt resolution through negotiation via the FixLanka platform before seeking external mediation or arbitration. A message log of all communications will be maintained as part of the contract record.</textarea>
                                </div>
                            </div>

                            <!-- Additional Terms -->
                            <div class="subsection">
                                <h4 class="subsection-title"><i class="fas fa-file-alt"></i> Additional Terms & Warranty</h4>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="warrantyPeriod">Warranty Period</label>
                                        <input type="text" id="warrantyPeriod" name="warranty_period" placeholder="e.g., 6 months after completion">
                                    </div>
                                    <div class="form-group">
                                        <label for="paymentTermsText">Payment Terms Notes</label>
                                        <input type="text" id="paymentTermsText" name="payment_terms" placeholder="e.g., Net 30 days">
                                    </div>
                                </div>
                                <div class="form-group full-width">
                                    <label for="additionalTerms">Additional Terms & Conditions</label>
                                    <textarea id="additionalTerms" name="additional_terms" rows="4" placeholder="Any additional clauses, conditions, or agreements..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- =============================== -->
                    <!-- STEP 8: Review & Finalize -->
                    <!-- =============================== -->
                    <div class="form-step-content" data-step="8">
                        <div class="step-header">
                            <h3><i class="fas fa-check-double"></i> Section 8 - Review & Finalize</h3>
                            <p class="step-description">Review the complete contract before sending to customer. This preview mirrors the final legal document.</p>
                        </div>

                        <div class="contract-preview" id="contractPreview">
                            <!-- Legal Document Header -->
                            <div class="preview-header">
                                <h2>CONSTRUCTION SERVICE AGREEMENT</h2>
                                <p class="preview-ref" id="previewRef">Contract Reference: -</p>
                                <p class="preview-date">Date: <span id="previewDate"></span></p>
                            </div>

                            <!-- Section 1: Parties -->
                            <div class="preview-section">
                                <h4>1. PARTIES TO THE CONTRACT</h4>
                                <div class="preview-parties">
                                    <div>
                                        <strong>First Party (Client):</strong>
                                        <span id="previewClientName">-</span><br>
                                        <small id="previewClientDetails">-</small>
                                    </div>
                                    <div>
                                        <strong>Second Party (Contractor):</strong>
                                        <span id="previewCompanyName">-</span><br>
                                        <small id="previewCompanyDetails">-</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Project -->
                            <div class="preview-section">
                                <h4>2. PROJECT OVERVIEW</h4>
                                <div class="preview-grid">
                                    <div><strong>Title:</strong> <span id="previewProjectTitle">-</span></div>
                                    <div><strong>Reference:</strong> <span id="previewProjectRef">-</span></div>
                                    <div><strong>Location:</strong> <span id="previewProjectLocation">-</span></div>
                                    <div><strong>Type:</strong> <span id="previewProjectType">-</span></div>
                                </div>
                                <p id="previewProjectDesc" style="margin-top:8px;color:#4a5568;">-</p>
                            </div>

                            <!-- Section 3: Scope -->
                            <div class="preview-section">
                                <h4>3. SCOPE OF WORK</h4>
                                <p id="previewScopeDesc">-</p>
                                <div class="preview-grid" style="margin-top:10px;">
                                    <div>
                                        <strong>Inclusions:</strong>
                                        <pre id="previewInclusions" class="preview-pre">-</pre>
                                    </div>
                                    <div>
                                        <strong>Exclusions:</strong>
                                        <pre id="previewExclusions" class="preview-pre">-</pre>
                                    </div>
                                </div>
                                <p><strong>Materials:</strong> <span id="previewMaterials">-</span></p>
                            </div>

                            <!-- Section 4: Timeline -->
                            <div class="preview-section">
                                <h4>4. PROJECT DURATION & MILESTONES</h4>
                                <div class="preview-grid cols-3">
                                    <div><strong>Start:</strong> <span id="previewStartDate">-</span></div>
                                    <div><strong>Completion:</strong> <span id="previewEndDate">-</span></div>
                                    <div><strong>Duration:</strong> <span id="previewDuration">-</span> days</div>
                                </div>
                                <table class="preview-milestones-table" id="previewMilestonesTable" style="margin-top:10px;">
                                    <thead><tr><th>#</th><th>Milestone</th><th>Date</th></tr></thead>
                                    <tbody id="previewMilestonesBody"></tbody>
                                </table>
                            </div>

                            <!-- Section 5: Financial -->
                            <div class="preview-section">
                                <h4>5. PRICING, PAYMENTS & DELAYS</h4>
                                <div class="preview-grid cols-3">
                                    <div><strong>Quotation Total:</strong> <span id="previewValue">-</span></div>
                                    <div><strong>Type:</strong> <span id="previewBudgetType">-</span></div>
                                    <div><strong>Payment:</strong> <span id="previewPaymentMethod">-</span></div>
                                </div>
                                <div id="previewPaymentSchedule" style="margin-top:10px;"></div>
                                <p style="margin-top:8px;"><strong>Late Payment:</strong> <span id="previewLatePayment">-</span></p>
                            </div>

                            <!-- Section 6 & 7: Clauses -->
                            <div class="preview-section">
                                <h4>6. VARIATIONS & CHANGES</h4>
                                <p id="previewVariation">-</p>
                            </div>
                            <div class="preview-section">
                                <h4>7. COMMUNICATION & DISPUTE RESOLUTION</h4>
                                <p><strong>Channel:</strong> <span id="previewCommChannel">-</span></p>
                                <p id="previewDisputeRes">-</p>
                            </div>

                            <!-- Additional -->
                            <div class="preview-section" id="previewAdditionalSection" style="display:none;">
                                <h4>ADDITIONAL TERMS</h4>
                                <p id="previewAdditionalTerms">-</p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer with Navigation -->
            <div class="modal-footer">
                <div class="footer-left">
                    <button type="button" class="btn btn-outline" id="formSaveDraftBtn">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                </div>
                <div class="footer-right">
                    <button type="button" class="btn btn-outline" id="formPrevBtn" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn btn-outline" id="formCancelBtn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="formNextBtn">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="formSubmitBtn" style="display: none;">
                        <i class="fas fa-paper-plane"></i> Create & Send to Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Contract Confirmation Modal -->
    <div class="modal-overlay" id="sendContractModal">
        <div class="modal-container confirmation-modal">
            <div class="modal-header">
                <h2><i class="fas fa-paper-plane"></i> Send Contract</h2>
                <button class="modal-close" id="sendModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="send-confirm-content" style="text-align:center; padding: 20px 10px;">
                    <div style="font-size: 48px; color: var(--primary-color, #0abab5); margin-bottom: 15px;">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <p style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">
                        Send this contract to the customer?
                    </p>
                    <p id="sendContractTitle" style="font-size: 14px; color: #666; margin-bottom: 5px;">-</p>
                    <p id="sendContractClient" style="font-size: 14px; color: #666; margin-bottom: 20px;">-</p>
                    <div style="background: #f0fdfa; border: 1px solid #99f6e4; border-radius: 8px; padding: 12px 16px; text-align: left; font-size: 13px; color: #555;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color, #0abab5); margin-right: 6px;"></i>
                        The customer will be able to <strong>view</strong>, <strong>accept</strong>, or <strong>decline</strong> this contract from their dashboard.
                    </div>
                </div>
                <input type="hidden" id="sendContractId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="sendCancelBtn">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="sendSubmitBtn">
                    <i class="fas fa-paper-plane"></i> Send to Customer
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container confirmation-modal">
            <div class="modal-header warning">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <button class="modal-close" id="deleteModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <p class="warning-text">Are you sure you want to delete this contract?</p>
                <p class="contract-info-text" id="deleteContractInfo">Contract Title</p>
                <p class="warning-note"><strong>Warning:</strong> This action cannot be undone. All contract data, documents, and history will be permanently deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="deleteCancelBtn">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="deleteConfirmBtn">
                    <i class="fas fa-trash"></i> Delete Contract
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Contract Confirmation Modal -->
    <div class="modal-overlay" id="cancelModal">
        <div class="modal-container confirmation-modal">
            <div class="modal-header warning">
                <h2><i class="fas fa-times-circle"></i> Cancel Contract</h2>
                <button class="modal-close" id="cancelModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <p class="warning-text">Are you sure you want to cancel this contract?</p>
                <p class="contract-info-text" id="cancelContractInfo">Contract</p>
                <p class="warning-note"><strong>Note:</strong> This will remove the contract and allow a new contract to be sent for the same project.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="cancelCancelBtn">
                    <i class="fas fa-times"></i> Keep Contract
                </button>
                <button type="button" class="btn btn-danger" id="cancelConfirmBtn">
                    <i class="fas fa-trash"></i> Cancel Contract
                </button>
            </div>
        </div>
    </div>



    <!-- Rejection Reason Modal (Customer View Simulation) -->
    <div class="modal-overlay" id="rejectionModal">
        <div class="modal-container small-modal">
            <div class="modal-header warning">
                <h2><i class="fas fa-times-circle"></i> Reject Contract</h2>
                <button class="modal-close" id="rejectionModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <p class="modal-description">Please provide a reason for rejecting this contract. This will help us understand your concerns and make necessary adjustments.</p>
                
                <form id="rejectionForm">
                    <div class="form-group">
                        <label>Primary Reason: <span class="required">*</span></label>
                        <div class="rejection-reasons">
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="budget" required>
                                <span class="reason-text">
                                    <i class="fas fa-dollar-sign"></i>
                                    Budget too high
                                </span>
                            </label>
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="timeline" required>
                                <span class="reason-text">
                                    <i class="fas fa-clock"></i>
                                    Timeline doesn't work
                                </span>
                            </label>
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="scope" required>
                                <span class="reason-text">
                                    <i class="fas fa-project-diagram"></i>
                                    Project scope mismatch
                                </span>
                            </label>
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="terms" required>
                                <span class="reason-text">
                                    <i class="fas fa-file-contract"></i>
                                    Payment terms unclear
                                </span>
                            </label>
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="quality" required>
                                <span class="reason-text">
                                    <i class="fas fa-star"></i>
                                    Quality concerns
                                </span>
                            </label>
                            <label class="reason-option">
                                <input type="radio" name="rejectionReason" value="other" required>
                                <span class="reason-text">
                                    <i class="fas fa-question-circle"></i>
                                    Other reason
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rejectionDetails">Detailed Explanation: <span class="required">*</span></label>
                        <textarea 
                            id="rejectionDetails" 
                            name="rejectionDetails" 
                            rows="4" 
                            required
                            placeholder="Please explain your concerns in detail. This will help us revise the contract to better meet your needs..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="openNegotiation" name="openNegotiation" checked>
                            <span>I'm willing to negotiate and discuss revisions</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="rejectionCancel">
                    <i class="fas fa-arrow-left"></i> Go Back
                </button>
                <button type="button" class="btn btn-danger" id="rejectionSubmit">
                    <i class="fas fa-times-circle"></i> Submit Rejection
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================
         MILESTONE PLAN BUILDER MODAL
         ======================================== -->
    <div id="milestonePlanModal" class="modal" style="display: none;">
        <div class="modal-content milestone-modal-large">
            <button class="modal-close" onclick="closeMilestonePlanModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-header">
                <h2><i class="fas fa-clipboard-list"></i> Create Milestone Plan</h2>
                <p>Define the payment milestones for this contract. Percentages must total exactly 100%.</p>
            </div>
            
            <div class="modal-body">
                <!-- Contract Info Summary -->
                <div class="milestone-contract-summary">
                    <div class="summary-item">
                        <span class="summary-label">Contract ID:</span>
                        <span class="summary-value" id="msPlanContractId">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Total Budget:</span>
                        <span class="summary-value" id="msPlanBudget">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Duration:</span>
                        <span class="summary-value" id="msPlanDuration">-</span>
                    </div>
                </div>
                
                <!-- Percentage Tracker -->
                <div class="percentage-tracker">
                    <div class="percentage-header">
                        <span class="percentage-label">Total Percentage:</span>
                        <span class="percentage-value" id="totalPercentage">0.00%</span>
                    </div>
                    <div class="percentage-bar">
                        <div class="percentage-fill" id="percentageFill" style="width: 0%"></div>
                    </div>
                    <div class="percentage-warning" id="percentageWarning"></div>
                </div>
                
                <!-- Milestones Container -->
                <div id="milestonesContainer" class="milestones-container">
                    <!-- Milestones will be added dynamically -->
                </div>
                
                <button type="button" class="btn-add-milestone" onclick="addMilestone()">
                    <i class="fas fa-plus"></i> Add Milestone
                </button>
                
                <!-- Action Buttons -->
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="closeMilestonePlanModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button class="btn btn-info" onclick="previewMilestonePlan()">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button class="btn btn-success" id="submitPlanBtn" onclick="submitMilestonePlan()" disabled>
                        <i class="fas fa-check"></i> Submit Plan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/chat.js?v=6.8"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/shared/contract-preview.js?v=1.3"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/contracts-enhanced.js?v=7.0"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/contract-form-enhanced.js?v=7.3"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/shared/budget-adjustment.js?v=6.5"></script>
    <script>
        // Store current contract for budget adjustment
        let currentContractData = null;
        
        // Override or extend the existing showContractModal function
        const originalShowContractModal = window.showContractDetails || function() {};
        
        window.showContractDetails = function(contractId) {
            window.currentContractId = contractId; 
            if (typeof originalShowContractModal === 'function') originalShowContractModal(contractId);
            
            // Chat loading removed (Phase 6 0%)
            
            if(typeof loadBudgetInformation === 'function') loadBudgetInformation(contractId);
        };

        // Function to load and display budget information
        async function loadBudgetInformation(contractId) {
            try {
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get&id=${contractId}`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    currentContractData = result.data;
                    displayBudgetFlexibility(result.data);
                    
                    // Check if milestone plan is needed
                    checkMilestonePlanStatus(result.data);
                    
                    // Initialize budget adjustment system if flexible
                    if (result.data.budget_type === 'flexible') {
                        setTimeout(() => {
                            initializeBudgetAdjustment(contractId, 'flexible');
                        }, 500);
                    }
                }
            } catch (error) {
                console.error('Error loading budget information:', error);
            }
        }
        
        // Check milestone plan status and show appropriate UI
        function checkMilestonePlanStatus(contract) {
            const milestonePlanAction = document.getElementById('milestonePlanAction');
            const milestonePlanStatus = document.getElementById('milestonePlanStatus');
            
            // Hide both by default
            milestonePlanAction.style.display = 'none';
            milestonePlanStatus.style.display = 'none';
            
            // Check if payment method is milestone
            if (contract.payment_method === 'milestone' || contract.payment_method === 'Milestone-based (Phased)') {
                // Check if plan is already submitted
                if (contract.milestone_plan_submitted) {
                    milestonePlanStatus.style.display = 'block';
                } else {
                    milestonePlanAction.style.display = 'block';
                }
            }
        }
        
        // Function to display budget flexibility section
        function displayBudgetFlexibility(contract) {
            const section = document.getElementById('budgetFlexibilitySection');
            const budgetTypeBadge = document.getElementById('budgetTypeBadge');
            const budgetRangeInfo = document.getElementById('budgetRangeInfo');
            const budgetFixedInfo = document.getElementById('budgetFixedInfo');
            const budgetMinSpan = document.getElementById('viewBudgetMin');
            const budgetMaxSpan = document.getElementById('viewBudgetMax');
            
            if (!section) return;
            
            // Show section
            section.style.display = 'block';
            
            // Format currency
            const formatCurrency = (amount) => {
                return 'LKR ' + new Intl.NumberFormat('en-LK', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            };
            
            if (contract.budget_type === 'flexible') {
                // Flexible budget
                const budgetValue = parseFloat(contract.value || contract.total_budget || 0);
                const minBudget = contract.budget_min || null;
                const maxBudget = contract.budget_max || null;
                let overallPct = null;
                if (budgetValue > 0 && maxBudget !== null && maxBudget !== undefined && maxBudget !== '') {
                    const maxNum = parseFloat(maxBudget);
                    if (!isNaN(maxNum) && maxNum >= budgetValue) {
                        overallPct = ((maxNum - budgetValue) / budgetValue) * 100;
                    }
                }
                const pctText = overallPct !== null ? ` (±${overallPct.toFixed(1)}%)` : '';

                budgetTypeBadge.innerHTML = `Flexible Budget${pctText}`;
                budgetTypeBadge.className = 'badge badge-flexible';
                budgetTypeBadge.style.background = '#3498db';
                budgetTypeBadge.style.color = 'white';
                
                // Calculate range if not provided
                const resolvedMin = minBudget !== null && minBudget !== undefined && minBudget !== '' ? parseFloat(minBudget) : (budgetValue > 0 ? budgetValue : 0);
                const resolvedMax = maxBudget !== null && maxBudget !== undefined && maxBudget !== '' ? parseFloat(maxBudget) : (budgetValue > 0 ? budgetValue : 0);
                
                // Display range
                budgetRangeInfo.style.display = 'block';
                budgetFixedInfo.style.display = 'none';
                budgetMinSpan.textContent = formatCurrency(resolvedMin);
                budgetMaxSpan.textContent = formatCurrency(resolvedMax);
                
            } else {
                // Fixed budget
                budgetTypeBadge.innerHTML = 'Fixed Budget';
                budgetTypeBadge.className = 'badge badge-fixed';
                budgetTypeBadge.style.background = '#95a5a6';
                budgetTypeBadge.style.color = 'white';
                
                budgetRangeInfo.style.display = 'none';
                budgetFixedInfo.style.display = 'block';
            }
        }
    </script>
</body>

</html>






