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
    <title>FixLanka - Contracts Management</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/contracts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Contracts</span>
                        </div>
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
                        <div class="quick-stats">
                            <div class="stat-item">
                                <span class="stat-value">22</span>
                                <span class="stat-label">Active Contracts</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">8</span>
                                <span class="stat-label">Pending</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">48</span>
                                <span class="stat-label">Completed</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-value">LKR 3.2M</span>
                                <span class="stat-label">Total Value</span>
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
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
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
                <h2>Contract Details</h2>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content" id="modalContent">
                <!-- This will be populated dynamically with comprehensive contract details -->

                <!-- Default content structure -->
                <div class="contract-modal-header">
                    <div class="contract-modal-title">
                        <h1 id="modalContractTitle">Contract Title</h1>
                        <p class="contract-id" id="modalContractId">CNT-2025-001</p>
                    </div>
                    <div class="contract-status-badge" id="modalContractStatus">
                        Active
                    </div>
                </div>

                <div class="contract-modal-body">
                    <div class="contract-main-info">
                        <!-- Client Information -->
                        <div class="modal-section">
                            <h3><i class="fas fa-user-tie"></i> Client Information</h3>
                            <div class="modal-info-grid">
                                <div class="modal-info-item">
                                    <label>Client Name</label>
                                    <div class="value" id="modalClientName">ABC Corporation</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Contact Person</label>
                                    <div class="value" id="modalContactPerson">John Doe</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Email</label>
                                    <div class="value" id="modalClientEmail">john@abc.com</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Phone</label>
                                    <div class="value" id="modalClientPhone">+94 77 123 4567</div>
                                </div>
                            </div>
                        </div>

                        <!-- Project Details -->
                        <div class="modal-section">
                            <h3><i class="fas fa-project-diagram"></i> Project Details</h3>
                            <div class="contract-description" id="modalDescription">
                                This is a comprehensive project description that will provide detailed information about
                                the scope of work, deliverables, and requirements.
                            </div>
                            <div class="modal-info-grid">
                                <div class="modal-info-item">
                                    <label>Project Type</label>
                                    <div class="value" id="modalProjectType">Renovation</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Location</label>
                                    <div class="value" id="modalLocation">Colombo, Sri Lanka</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Start Date</label>
                                    <div class="value" id="modalStartDate">2025-01-15</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>End Date</label>
                                    <div class="value" id="modalEndDate">2025-03-15</div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Tracking -->
                        <div class="modal-section">
                            <h3><i class="fas fa-chart-line"></i> Progress Tracking</h3>
                            <div class="progress-section">
                                <div class="modal-progress-bar">
                                    <div class="modal-progress-fill" id="modalProgressFill" style="width: 65%"></div>
                                </div>
                                <div class="progress-details">
                                    <span class="progress-text" id="modalProgressText">65% Complete</span>
                                    <span id="modalProgressStage">Design Phase</span>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="modal-section">
                            <h3><i class="fas fa-history"></i> Project Timeline</h3>
                            <div class="contract-timeline" id="modalTimeline">
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Project Started</h4>
                                        <p>Contract signed and project officially commenced</p>
                                        <div class="timeline-date">January 15, 2025</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h4>Planning Phase</h4>
                                        <p>Detailed planning and resource allocation completed</p>
                                        <div class="timeline-date">January 20, 2025</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="modal-section">
                            <h3><i class="fas fa-paperclip"></i> Contract Documents</h3>
                            <div class="contract-attachments">
                                <div class="attachment-list" id="modalAttachments">
                                    <div class="attachment-item">
                                        <div class="attachment-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="attachment-info">
                                            <h5>Main Contract Agreement</h5>
                                            <p>PDF • 2.4 MB • Last modified: Jan 15, 2025</p>
                                        </div>
                                    </div>
                                    <div class="attachment-item">
                                        <div class="attachment-icon">
                                            <i class="fas fa-file-image"></i>
                                        </div>
                                        <div class="attachment-info">
                                            <h5>Project Blueprints</h5>
                                            <p>Images • 15.2 MB • Last modified: Jan 10, 2025</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contract-sidebar-info">
                        <!-- Financial Information -->
                        <div class="modal-section">
                            <h3><i class="fas fa-dollar-sign"></i> Financial Details</h3>
                            <div class="modal-info-grid">
                                <div class="modal-info-item">
                                    <label>Contract Value</label>
                                    <div class="value highlight" id="modalContractValue">LKR 250,000</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Paid Amount</label>
                                    <div class="value" id="modalPaidAmount">LKR 100,000</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Remaining</label>
                                    <div class="value" id="modalRemainingAmount">LKR 150,000</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Payment Terms</label>
                                    <div class="value" id="modalPaymentTerms">30 Days</div>
                                </div>
                            </div>
                        </div>

                        <!-- Key Information -->
                        <div class="modal-section">
                            <h3><i class="fas fa-info-circle"></i> Contract Info</h3>
                            <div class="modal-info-grid">
                                <div class="modal-info-item">
                                    <label>Contract Type</label>
                                    <div class="value" id="modalContractType">Fixed Price</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Duration</label>
                                    <div class="value" id="modalDuration">60 Days</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Priority</label>
                                    <div class="value" id="modalPriority">High</div>
                                </div>
                                <div class="modal-info-item">
                                    <label>Assigned Team</label>
                                    <div class="value" id="modalTeam">Team Alpha</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer-actions">
                    <div class="modal-footer-left">
                        <button class="btn btn-outline" id="modalDownload">
                            <i class="fas fa-download"></i> Download Contract
                        </button>
                        <button class="btn btn-outline" id="modalPrint">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                    <div class="modal-footer-right">
                        <button class="btn btn-secondary" id="modalEdit">
                            <i class="fas fa-edit"></i> Edit Contract
                        </button>
                        <button class="btn btn-primary" id="modalClose">
                            <i class="fas fa-check"></i> Close
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

    <!-- New Contract Modal -->
    <div class="modal-overlay" id="newContractModal">
        <div class="modal-container form-modal">
            <div class="modal-header">
                <h2><i class="fas fa-plus-circle"></i> <span id="formModalTitle">Create New Contract</span></h2>
                <button class="modal-close" id="newContractClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="contractForm" class="contract-form">
                    <!-- Step Indicators -->
                    <div class="form-steps">
                        <div class="form-step-indicator active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Select Project</div>
                        </div>
                        <div class="form-step-indicator" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Project Details</div>
                        </div>
                        <div class="form-step-indicator" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Financial Terms</div>
                        </div>
                        <div class="form-step-indicator" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-label">Review</div>
                        </div>
                    </div>

                    <!-- Step 1: Select Project (NEW) -->
                    <div class="form-step-content active" data-step="1">
                        <h3><i class="fas fa-clipboard-check"></i> Select Accepted Project</h3>
                        <p class="step-description">Choose an accepted quotation to create a contract for</p>
                        
                        <div class="project-selection-container">
                            <div class="loading-projects" id="loadingProjects">
                                <div class="spinner"></div>
                                <p>Loading accepted projects...</p>
                            </div>
                            
                            <div class="no-projects" id="noProjects" style="display: none;">
                                <i class="fas fa-folder-open"></i>
                                <h4>No Projects Available</h4>
                                <p>You don't have any accepted quotations yet that can be converted to contracts.</p>
                            </div>
                            
                            <div class="projects-list" id="projectsList" style="display: none;">
                                <!-- Projects will be loaded dynamically here -->
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Client & Project Information (Auto-filled) -->
                    <div class="form-step-content" data-step="2">
                        <h3><i class="fas fa-info-circle"></i> Project & Client Information</h3>
                        <p class="step-description">Review and edit project details</p>
                        
                        <!-- Hidden fields for IDs -->
                        <input type="hidden" id="selectedQuotationId" name="quotation_id">
                        <input type="hidden" id="selectedRequestId" name="request_id">
                        
                        <div class="info-section">
                            <h4><i class="fas fa-user-tie"></i> Client Information</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="clientName">Client Name <span class="required">*</span></label>
                                    <input type="text" id="clientName" name="clientName" readonly class="readonly-field">
                                </div>
                                <div class="form-group">
                                    <label for="clientEmail">Email Address</label>
                                    <input type="email" id="clientEmail" name="clientEmail" readonly class="readonly-field">
                                </div>
                                <div class="form-group">
                                    <label for="clientPhone">Phone Number</label>
                                    <input type="tel" id="clientPhone" name="clientPhone" readonly class="readonly-field">
                                </div>
                            </div>
                        </div>

                        <div class="info-section">
                            <h4><i class="fas fa-project-diagram"></i> Project Details</h4>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="projectTitle">Project Title <span class="required">*</span></label>
                                    <input type="text" id="projectTitle" name="projectTitle" required>
                                </div>
                                <div class="form-group">
                                    <label for="projectType">Project Type <span class="required">*</span></label>
                                    <input type="text" id="projectType" name="projectType" required>
                                </div>
                                <div class="form-group">
                                    <label for="projectLocation">Location <span class="required">*</span></label>
                                    <input type="text" id="projectLocation" name="projectLocation" required>
                                </div>
                                <div class="form-group full-width">
                                    <label for="projectDescription">Project Description <span class="required">*</span></label>
                                    <textarea id="projectDescription" name="projectDescription" rows="4" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Financial Terms & Timeline -->
                    <div class="form-step-content" data-step="3">
                        <h3><i class="fas fa-dollar-sign"></i> Financial Terms & Timeline</h3>
                        <p class="step-description">Define contract budget and schedule</p>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="contractValue">Contract Value (LKR) <span class="required">*</span></label>
                                <input type="number" id="contractValue" name="contractValue" required placeholder="Pre-filled from quotation" min="0" step="1000">
                            </div>
                            <div class="form-group">
                                <label for="startDate">Start Date <span class="required">*</span></label>
                                <input type="date" id="startDate" name="startDate" required>
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date <span class="required">*</span></label>
                                <input type="date" id="endDate" name="endDate" required>
                            </div>
                            <div class="form-group">
                                <label for="contractType">Contract Type <span class="required">*</span></label>
                                <select id="contractType" name="contractType" required>
                                    <option value="">Select type</option>
                                    <option value="fixed-price">Fixed Price</option>
                                    <option value="time-material">Time & Material</option>
                                    <option value="cost-plus">Cost Plus</option>
                                    <option value="retainer">Retainer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="paymentTerms">Payment Terms <span class="required">*</span></label>
                                <select id="paymentTerms" name="paymentTerms" required>
                                    <option value="">Select terms</option>
                                    <option value="full-upfront">Full Payment Upfront</option>
                                    <option value="50-50">50% Upfront, 50% on Completion</option>
                                    <option value="installments-3">3 Installments</option>
                                    <option value="installments-4">4 Installments</option>
                                    <option value="monthly">Monthly Payments</option>
                                    <option value="milestone">Milestone Based</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="advancePayment">Advance Payment (LKR)</label>
                                <input type="number" id="advancePayment" name="advancePayment" placeholder="50000" min="0" step="1000">
                            </div>
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select id="currency" name="currency">
                                    <option value="LKR" selected>Sri Lankan Rupee (LKR)</option>
                                    <option value="USD">US Dollar (USD)</option>
                                    <option value="EUR">Euro (EUR)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="taxRate">Tax Rate (%)</label>
                                <input type="number" id="taxRate" name="taxRate" placeholder="15" min="0" max="100" step="0.1">
                            </div>
                            <div class="form-group full-width">
                                <label for="paymentNotes">Payment Notes</label>
                                <textarea id="paymentNotes" name="paymentNotes" rows="3" placeholder="Additional payment terms or conditions..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Review -->
                    <div class="form-step-content" data-step="4">
                        <h3><i class="fas fa-check-circle"></i> Review Contract Details</h3>
                        <div class="review-container">
                            <div class="review-section">
                                <h4><i class="fas fa-user-tie"></i> Client Information</h4>
                                <div class="review-grid">
                                    <div class="review-item"><label>Client Name:</label><span id="reviewClientName">-</span></div>
                                    <div class="review-item"><label>Client Type:</label><span id="reviewClientType">-</span></div>
                                    <div class="review-item"><label>Contact Person:</label><span id="reviewContactPerson">-</span></div>
                                    <div class="review-item"><label>Email:</label><span id="reviewClientEmail">-</span></div>
                                    <div class="review-item"><label>Phone:</label><span id="reviewClientPhone">-</span></div>
                                    <div class="review-item"><label>Address:</label><span id="reviewClientAddress">-</span></div>
                                </div>
                            </div>
                            <div class="review-section">
                                <h4><i class="fas fa-project-diagram"></i> Project Details</h4>
                                <div class="review-grid">
                                    <div class="review-item"><label>Project Title:</label><span id="reviewProjectTitle">-</span></div>
                                    <div class="review-item"><label>Project Type:</label><span id="reviewProjectType">-</span></div>
                                    <div class="review-item"><label>Location:</label><span id="reviewProjectLocation">-</span></div>
                                    <div class="review-item"><label>Start Date:</label><span id="reviewStartDate">-</span></div>
                                    <div class="review-item"><label>End Date:</label><span id="reviewEndDate">-</span></div>
                                    <div class="review-item"><label>Priority:</label><span id="reviewPriority">-</span></div>
                                    <div class="review-item full-width"><label>Description:</label><span id="reviewDescription">-</span></div>
                                </div>
                            </div>
                            <div class="review-section">
                                <h4><i class="fas fa-dollar-sign"></i> Financial Terms</h4>
                                <div class="review-grid">
                                    <div class="review-item"><label>Contract Value:</label><span id="reviewContractValue">-</span></div>
                                    <div class="review-item"><label>Contract Type:</label><span id="reviewContractType">-</span></div>
                                    <div class="review-item"><label>Payment Terms:</label><span id="reviewPaymentTerms">-</span></div>
                                    <div class="review-item"><label>Advance Payment:</label><span id="reviewAdvancePayment">-</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
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
                    <i class="fas fa-check"></i> Create Contract
                </button>
            </div>
        </div>
    </div>

    <!-- Send Contract Modal -->
    <div class="modal-overlay" id="sendContractModal">
        <div class="modal-container small-modal">
            <div class="modal-header">
                <h2><i class="fas fa-paper-plane"></i> Send Contract</h2>
                <button class="modal-close" id="sendModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="sendContractForm" class="send-form">
                    <div class="form-group">
                        <label for="sendToEmail">Recipient Email <span class="required">*</span></label>
                        <input type="email" id="sendToEmail" name="sendToEmail" required placeholder="client@example.com">
                    </div>
                    <div class="form-group">
                        <label for="sendCcEmail">CC (Optional)</label>
                        <input type="email" id="sendCcEmail" name="sendCcEmail" placeholder="manager@company.com">
                    </div>
                    <div class="form-group">
                        <label for="sendSubject">Subject <span class="required">*</span></label>
                        <input type="text" id="sendSubject" name="sendSubject" required value="Contract Agreement - FixLanka">
                    </div>
                    <div class="form-group">
                        <label for="sendMessage">Message <span class="required">*</span></label>
                        <textarea id="sendMessage" name="sendMessage" rows="6" required>Dear Client,

Please find attached the contract agreement for your review. Kindly review the terms and conditions and provide your signature if everything is in order.

If you have any questions or concerns, please don't hesitate to contact us.

Best regards,
FixLanka Team</textarea>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="sendCopy" name="sendCopy" checked>
                            <span>Send a copy to myself</span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="requestSignature" name="requestSignature" checked>
                            <span>Request digital signature</span>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="sendCancelBtn">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="sendSubmitBtn">
                    <i class="fas fa-paper-plane"></i> Send Contract
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

    <!-- Contract Negotiation/Chat Modal -->
    <div class="modal-overlay" id="negotiationModal">
        <div class="modal-container chat-modal">
            <div class="modal-header">
                <div class="chat-header-info">
                    <h2><i class="fas fa-comments"></i> Contract Negotiation</h2>
                    <p class="chat-contract-title" id="chatContractTitle">Contract Title</p>
                    <div class="contract-status-badge rejected" id="chatContractStatus">
                        <i class="fas fa-times-circle"></i> Rejected by Customer
                    </div>
                </div>
                <button class="modal-close" id="negotiationModalClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="chat-container">
                <!-- Rejection Reason Banner (if rejected) -->
                <div class="rejection-banner" id="rejectionBanner" style="display: none;">
                    <div class="rejection-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="rejection-details">
                        <h4>Customer Rejected Contract</h4>
                        <p class="rejection-reason" id="rejectionReason">Reason: Budget concerns and timeline too tight</p>
                        <p class="rejection-date" id="rejectionDate">Rejected on: October 20, 2025 at 2:45 PM</p>
                        <button class="rejection-cta-btn" id="editContractFromBanner">
                            <i class="fas fa-edit"></i>
                            Edit Contract Now
                        </button>
                    </div>
                </div>

                <!-- Quick Actions Bar -->
                <div class="chat-quick-actions">
                    <button class="quick-action-btn primary-action" id="reviseContractBtn" title="Open contract editor to make changes">
                        <i class="fas fa-edit"></i>
                        <span>Edit Contract</span>
                    </button>
                    <button class="quick-action-btn" id="viewOriginalBtn" title="View full contract details">
                        <i class="fas fa-file-alt"></i>
                        <span>View Original</span>
                    </button>
                    <button class="quick-action-btn" id="sendRevisedBtn" title="Send updated contract to customer">
                        <i class="fas fa-paper-plane"></i>
                        <span>Send Revised</span>
                    </button>
                    <button class="quick-action-btn danger" id="withdrawContractBtn" title="Cancel this contract permanently">
                        <i class="fas fa-ban"></i>
                        <span>Withdraw</span>
                    </button>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages" id="chatMessages">
                    <!-- System Message -->
                    <div class="chat-message system-message">
                        <div class="message-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="message-content">
                            <p><strong>Contract Sent</strong></p>
                            <p>You sent this contract to the customer for review.</p>
                            <span class="message-time">October 18, 2025 at 10:30 AM</span>
                        </div>
                    </div>

                    <!-- Customer Message -->
                    <div class="chat-message customer-message">
                        <div class="message-avatar">JD</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">John Doe</span>
                                <span class="message-role">Customer</span>
                            </div>
                            <p>Thank you for sending the contract. I've reviewed it carefully, but I have some concerns about the timeline and budget.</p>
                            <span class="message-time">October 19, 2025 at 3:15 PM</span>
                        </div>
                    </div>

                    <!-- Company Response -->
                    <div class="chat-message company-message">
                        <div class="message-avatar company">FL</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">FixLanka Team</span>
                                <span class="message-role">Company</span>
                            </div>
                            <p>We understand your concerns. What specific aspects would you like us to adjust?</p>
                            <span class="message-time">October 19, 2025 at 4:00 PM</span>
                        </div>
                    </div>

                    <!-- System Message - Rejection -->
                    <div class="chat-message system-message rejection-message">
                        <div class="message-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="message-content">
                            <p><strong>Contract Rejected</strong></p>
                            <p>The customer has declined the current contract terms.</p>
                            <p class="rejection-details"><strong>Reason:</strong> Budget concerns and timeline too tight</p>
                            <span class="message-time">October 20, 2025 at 2:45 PM</span>
                        </div>
                    </div>

                    <!-- Customer Explanation -->
                    <div class="chat-message customer-message">
                        <div class="message-avatar">JD</div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender">John Doe</span>
                                <span class="message-role">Customer</span>
                            </div>
                            <p>The proposed budget of LKR 250,000 is above our limit. We can go up to LKR 180,000. Also, can we extend the timeline from 60 days to 90 days?</p>
                            <span class="message-time">October 20, 2025 at 2:50 PM</span>
                        </div>
                    </div>

                    <!-- Typing Indicator (hidden by default) -->
                    <div class="typing-indicator" id="typingIndicator" style="display: none;">
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                        <span class="typing-text">Customer is typing...</span>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="chat-input-container">
                    <div class="chat-input-wrapper">
                        <textarea 
                            id="chatInput" 
                            class="chat-input" 
                            placeholder="Type your message to the customer..."
                            rows="1"
                        ></textarea>
                        <div class="chat-input-actions">
                            <button class="input-action-btn" id="attachFileBtn" title="Attach File">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button class="input-action-btn" id="sendMessageBtn" title="Send Message">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <div class="message-tips">
                        <i class="fas fa-lightbulb"></i>
                        <span>Tip: Be professional and address customer concerns clearly. Use "Revise Contract" to update terms.</span>
                    </div>
                </div>
            </div>

            <div class="modal-footer chat-footer">
                <div class="footer-left">
                    <span class="chat-status">
                        <i class="fas fa-circle online"></i>
                        Customer active 5 minutes ago
                    </span>
                </div>
                <div class="footer-right">
                    <button type="button" class="btn btn-outline" id="closeNegotiationBtn">
                        <i class="fas fa-times"></i> Close Chat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Status Update Modal -->
    <div class="modal-overlay" id="statusUpdateModal">
        <div class="modal-container small-modal">
            <div class="modal-header">
                <h2><i class="fas fa-sync-alt"></i> Update Contract Status</h2>
                <button class="modal-close" id="statusUpdateClose">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="statusUpdateForm">
                    <div class="status-current">
                        <label>Current Status:</label>
                        <div class="status-badge" id="currentStatusBadge">Rejected</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="newStatus">Change Status To: <span class="required">*</span></label>
                        <select id="newStatus" name="newStatus" required>
                            <option value="">Select new status</option>
                            <option value="draft">Draft</option>
                            <option value="sent">Sent to Customer</option>
                            <option value="under-review">Under Review</option>
                            <option value="negotiating">Negotiating</option>
                            <option value="accepted">Accepted by Customer</option>
                            <option value="active">Active (Work Started)</option>
                            <option value="rejected">Rejected</option>
                            <option value="withdrawn">Withdrawn</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="statusNotes">Notes (Optional):</label>
                        <textarea id="statusNotes" name="statusNotes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>

                    <div class="status-info-box">
                        <i class="fas fa-info-circle"></i>
                        <p><strong>Status Workflow:</strong></p>
                        <ul>
                            <li><strong>Draft</strong> → Contract being prepared</li>
                            <li><strong>Sent</strong> → Awaiting customer response</li>
                            <li><strong>Under Review</strong> → Customer reviewing</li>
                            <li><strong>Rejected</strong> → Customer declined</li>
                            <li><strong>Negotiating</strong> → Changes being discussed</li>
                            <li><strong>Accepted</strong> → Customer agreed</li>
                            <li><strong>Active</strong> → Work in progress</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" id="statusUpdateCancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="statusUpdateSubmit">
                    <i class="fas fa-check"></i> Update Status
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

    <!-- Scripts -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/contracts-enhanced.js"></script>
    <script>
        // Additional scripts if needed
    </script>
</body>

</html>



