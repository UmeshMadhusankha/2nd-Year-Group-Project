<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - FixLanka Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../assets/css/common/buttons.css">
    <link rel="stylesheet" href="../../assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="../../assets/css/company/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/company/topbar.css">
    <link rel="stylesheet" href="../../assets/css/company/projects.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../../assets/javascript/company/projects.js"></script>
</head>

<body>
    <!-- Hidden checkbox for CSS toggle -->
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <div id="header-container"></div>

            <div class="projects-container">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-project-diagram"></i> Projects</h1>
                                <p class="subtitle">Manage and track all your projects from start to completion</p>
                                <nav class="breadcrumbs">
                                    <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                                    <span class="separator">/</span>
                                    <span class="current">Projects</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="quick-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">24</span>
                                        <span class="stat-label">Active Projects</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">12</span>
                                        <span class="stat-label">Completed</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">3</span>
                                        <span class="stat-label">Delayed</span>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <button class="action-btn secondary" id="exportBtn" onclick="openExportModal()">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                    <button class="action-btn primary" id="startProjectBtn" onclick="showProjectStartOptions()">
                                        <i class="fas fa-plus"></i> Start a New Project
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Filters & Controls -->
                <section class="controls-section">
                    <div class="filters-bar">
                        <div class="filter-group">
                            <select class="filter-select">
                                <option value="all">All Status</option>
                                <option value="requests">Requests</option>
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                                <option value="delayed">Delayed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>

                            <button class="date-range-btn" id="dateRangeBtn">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="date-range-text">Select Date Range</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                        </div>
                        <div class="view-controls">
                            <button class="view-toggle active" data-view="table" title="Table View">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="view-toggle" data-view="cards" title="Card View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Date Range Picker Modal -->
                <div class="date-range-modal" id="dateRangeModal">
                    <div class="date-range-overlay"></div>
                    <div class="date-range-popup">
                        <div class="date-range-header">
                            <h3><i class="fas fa-calendar-alt"></i> Select Date Range</h3>
                            <button class="close-date-modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="date-range-content">
                            <!-- Quick Selection Buttons -->
                            <div class="quick-select-section">
                                <h4>Quick Select</h4>
                                <div class="quick-select-buttons">
                                    <button class="quick-select-btn" data-range="today">
                                        <i class="fas fa-calendar-day"></i>
                                        <span>Today</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="yesterday">
                                        <i class="fas fa-calendar-minus"></i>
                                        <span>Yesterday</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="this-week">
                                        <i class="fas fa-calendar-week"></i>
                                        <span>This Week</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="last-week">
                                        <i class="fas fa-calendar"></i>
                                        <span>Last Week</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="this-month">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>This Month</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="last-month">
                                        <i class="fas fa-calendar-minus"></i>
                                        <span>Last Month</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="this-quarter">
                                        <i class="fas fa-calendar-plus"></i>
                                        <span>This Quarter</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="this-year">
                                        <i class="fas fa-calendar"></i>
                                        <span>This Year</span>
                                    </button>
                                    <button class="quick-select-btn" data-range="last-30-days">
                                        <i class="fas fa-calendar-days"></i>
                                        <span>Last 30 Days</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Custom Date Selection -->
                            <div class="custom-date-section">
                                <h4>Custom Range</h4>
                                <div class="date-inputs">
                                    <div class="date-input-group">
                                        <label>Start Date</label>
                                        <input type="date" class="custom-date-input" id="startDate">
                                    </div>
                                    <div class="date-separator">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="date-input-group">
                                        <label>End Date</label>
                                        <input type="date" class="custom-date-input" id="endDate">
                                    </div>
                                </div>
                            </div>

                            <!-- Selected Range Display -->
                            <div class="selected-range-display">
                                <div class="range-preview">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="range-text">No date range selected</span>
                                </div>
                            </div>
                        </div>

                        <div class="date-range-actions">
                            <button class="date-action-btn cancel" id="cancelDateRange">Cancel</button>
                            <button class="date-action-btn clear" id="clearDateRange">Clear</button>
                            <button class="date-action-btn apply" id="applyDateRange">Apply Range</button>
                        </div>
                    </div>
                </div>

                <!-- Project List Section -->
                <section class="project-list-section">
                    <!-- Table View -->
                    <div class="table-view active">
                        <div class="table-container">
                            <table class="projects-table">
                                <thead>
                                    <tr>
                                        <th>Project Title</th>
                                        <th>Customer</th>
                                        <th>Timeline</th>
                                        <th>Contract Status</th>
                                        <th>Progress</th>
                                        <th>Budget</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="project-row" data-project="1">
                                        <td>
                                            <div class="project-title">
                                                <h4>Air Conditioner Repair - Colombo</h4>
                                                <span class="project-id">#PR-2024-001</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <div class="customer-avatar">JP</div>
                                                <div>
                                                    <div class="customer-name">John Perera</div>
                                                    <div class="customer-contact">+94 77 123 4567</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="timeline">
                                                <div class="start-date">Started: Aug 15, 2025</div>
                                                <div class="deadline">Due: Sep 10, 2025</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge active">Active</span>
                                        </td>
                                        <td>
                                            <div class="progress-container">
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: 75%;"></div>
                                                </div>
                                                <span class="progress-text">75%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="budget-info">
                                                <div class="total-budget">LKR 150,000</div>
                                                <div class="spent-budget">Spent: LKR 112,500</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge ongoing">Ongoing</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn-sm primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn-sm secondary" title="Update">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- need to link this directly to the chat section -->
                                                <button class="action-btn-sm info" title="Chat" onclick="event.stopPropagation(); openDrawerWithTab('1', 'communication')">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="project-row" data-project="2">
                                        <td>
                                            <div class="project-title">
                                                <h4>Kitchen Renovation - Kandy</h4>
                                                <span class="project-id">#PR-2024-002</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="customer-info">
                                                <div class="customer-avatar">SS</div>
                                                <div>
                                                    <div class="customer-name">Sarah Silva</div>
                                                    <div class="customer-contact">+94 76 987 6543</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="timeline">
                                                <div class="start-date">Started: Aug 20, 2025</div>
                                                <div class="deadline">Due: Sep 15, 2025</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge active">Active</span>
                                        </td>
                                        <td>
                                            <div class="progress-container">
                                                <div class="progress-bar">
                                                    <div class="progress-fill" style="width: 30%;"></div>
                                                </div>
                                                <span class="progress-text">30%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="budget-info">
                                                <div class="total-budget">LKR 250,000</div>
                                                <div class="spent-budget">Spent: LKR 75,000</div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge delayed">Delayed</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn-sm primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn-sm secondary" title="Update">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn-sm info" title="Chat" onclick="event.stopPropagation(); openDrawerWithTab('2', 'communication')">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Card View -->
                    <div class="card-view">
                        <div class="projects-grid">
                            <div class="project-card" data-project="1">
                                <div class="card-header">
                                    <h4>Air Conditioner Repair - Colombo</h4>
                                    <span class="status-badge ongoing">Ongoing</span>
                                </div>
                                <div class="card-content">
                                    <div class="customer-section">
                                        <div class="customer-avatar">JP</div>
                                        <div class="customer-details">
                                            <div class="customer-name">John Perera</div>
                                            <div class="customer-contact">+94 77 123 4567</div>
                                        </div>
                                    </div>
                                    <div class="progress-section">
                                        <div class="progress-label">Progress: 75%</div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 75%;"></div>
                                        </div>
                                    </div>
                                    <div class="workforce-section">
                                        <div class="workforce-label">Assigned Team:</div>
                                        <div class="workforce-avatars">
                                            <div class="worker-avatar verified" title="Kamal Perera - Electrician">
                                                <span>KP</span>
                                                <i class="fas fa-check-circle verify-badge"></i>
                                            </div>
                                            <div class="worker-avatar verified" title="Nimal Silva - Technician">
                                                <span>NS</span>
                                                <i class="fas fa-check-circle verify-badge"></i>
                                            </div>
                                            <div class="worker-avatar" title="Sunil Fernando - Helper">
                                                <span>SF</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="payment-section">
                                        <div class="next-payment">
                                            <span class="payment-label">Next Payment Due:</span>
                                            <span class="payment-amount">LKR 37,500</span>
                                            <span class="payment-date">Sep 5, 2025</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="action-btn-sm primary">View</button>
                                    <button class="action-btn-sm secondary">Update</button>
                                    <button class="action-btn-sm info" onclick="event.stopPropagation(); openDrawerWithTab('1', 'communication')">Chat</button>
                                </div>
                            </div>

                            <div class="project-card" data-project="2">
                                <div class="card-header">
                                    <h4>Kitchen Renovation - Kandy</h4>
                                    <span class="status-badge delayed">Delayed</span>
                                </div>
                                <div class="card-content">
                                    <div class="customer-section">
                                        <div class="customer-avatar">SS</div>
                                        <div class="customer-details">
                                            <div class="customer-name">Sarah Silva</div>
                                            <div class="customer-contact">+94 76 987 6543</div>
                                        </div>
                                    </div>
                                    <div class="progress-section">
                                        <div class="progress-label">Progress: 30%</div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 30%;"></div>
                                        </div>
                                    </div>
                                    <div class="workforce-section">
                                        <div class="workforce-label">Assigned Team:</div>
                                        <div class="workforce-avatars">
                                            <div class="worker-avatar verified" title="Ruwan Kumara - Carpenter">
                                                <span>RK</span>
                                                <i class="fas fa-check-circle verify-badge"></i>
                                            </div>
                                            <div class="worker-avatar verified" title="Priya Mendis - Designer">
                                                <span>PM</span>
                                                <i class="fas fa-check-circle verify-badge"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="payment-section">
                                        <div class="next-payment">
                                            <span class="payment-label">Next Payment Due:</span>
                                            <span class="payment-amount">LKR 87,500</span>
                                            <span class="payment-date">Sep 15, 2025</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button class="action-btn-sm primary">View</button>
                                    <button class="action-btn-sm secondary">Update</button>
                                    <button class="action-btn-sm info" onclick="event.stopPropagation(); openDrawerWithTab('2', 'communication')">Chat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Project Detail Drawer -->
            <div class="drawer-overlay" id="projectDrawer">
                <div class="drawer-panel">
                    <div class="drawer-header">
                        <h3>Air Conditioner Repair - Colombo</h3>
                        <button class="close-drawer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="drawer-content">
                        <div class="drawer-tabs">
                            <button class="drawer-tab active" data-tab="contract">Contract Details</button>
                            <button class="drawer-tab" data-tab="milestones">Milestones</button>
                            <button class="drawer-tab" data-tab="workforce">Workforce</button>
                            <button class="drawer-tab" data-tab="files">Files</button>
                            <button class="drawer-tab" data-tab="communication">Communication</button>
                            <button class="drawer-tab" data-tab="payments">Payments</button>
                        </div>

                        <!-- Contract Details Tab -->
                        <div class="drawer-tab-content active" id="contract">
                            <div class="contract-details-modern">
                                <!-- Contract Header -->
                                <div class="contract-header">
                                    <div class="contract-header-left">
                                        <div class="contract-id">
                                            <i class="fas fa-file-contract"></i>
                                            <span class="contract-number">#CT-2025-015</span>
                                        </div>
                                        <div class="contract-status">
                                            <span class="status-badge active">Active</span>
                                        </div>
                                    </div>
                                    <div class="contract-header-right">
                                        <!-- button need to link to full contract -->
                                        <button class="contract-action-btn secondary" onclick="navigateToContracts()">
                                            <i class="fas fa-external-link-alt"></i>
                                            View Full Contract
                                        </button>
                                        <button class="contract-action-btn primary">
                                            <i class="fas fa-download"></i>
                                            Download PDF
                                        </button>
                                    </div>
                                </div>

                                <!-- Key Information Grid -->
                                <div class="contract-info-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-info-circle"></i> Key Information</h4>
                                    </div>
                                    <div class="contract-info-grid">
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-dollar-sign"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>Contract Value</label>
                                                <span class="value">LKR 150,000</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>Payment Terms</label>
                                                <span class="value">3 Installments</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-calendar-check"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>Start Date</label>
                                                <span class="value">August 15, 2025</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-calendar-times"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>End Date</label>
                                                <span class="value">September 10, 2025</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>Duration</label>
                                                <span class="value">26 Days</span>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <div class="info-icon">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                            <div class="info-content">
                                                <label>Payment Status</label>
                                                <div class="payment-progress">
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: 45%;"></div>
                                                    </div>
                                                    <span class="progress-text">45% Paid (LKR 67,500)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parties Involved -->
                                <div class="contract-parties-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-users"></i> Parties Involved</h4>
                                    </div>
                                    <div class="parties-grid">
                                        <div class="party-card">
                                            <div class="party-type">
                                                <i class="fas fa-user"></i>
                                                <span>Customer</span>
                                            </div>
                                            <div class="party-details">
                                                <div class="party-name">John Perera</div>
                                                <div class="party-contact">
                                                    <div class="contact-item">
                                                        <i class="fas fa-phone"></i>
                                                        <span>+94 77 123 4567</span>
                                                    </div>
                                                    <div class="contact-item">
                                                        <i class="fas fa-envelope"></i>
                                                        <span>john.perera@email.com</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="party-card">
                                            <div class="party-type">
                                                <i class="fas fa-user-tie"></i>
                                                <span>Project Manager</span>
                                            </div>
                                            <div class="party-details">
                                                <div class="party-name">Samantha Fernando</div>
                                                <div class="party-contact">
                                                    <div class="contact-item">
                                                        <i class="fas fa-phone"></i>
                                                        <span>+94 71 456 7890</span>
                                                    </div>
                                                    <div class="contact-item">
                                                        <i class="fas fa-envelope"></i>
                                                        <span>samantha@fixlanka.com</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Scope & Deliverables -->
                                <div class="contract-scope-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-tasks"></i> Scope & Deliverables</h4>
                                    </div>
                                    <div class="scope-content">
                                        <div class="project-description">
                                            <h5>Project Description</h5>
                                            <p>Complete air conditioning system repair and maintenance including
                                                diagnostic assessment, component replacement, and system optimization
                                                for residential property in Colombo.</p>
                                        </div>
                                        <div class="deliverables-list">
                                            <h5>Key Deliverables</h5>
                                            <ul class="deliverables">
                                                <li>
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Complete diagnostic assessment and damage evaluation</span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Replacement of faulty components and parts</span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-clock"></i>
                                                    <span>System testing and performance optimization</span>
                                                </li>
                                                <li>
                                                    <i class="fas fa-clock"></i>
                                                    <span>6-month warranty and maintenance documentation</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes & Attachments -->
                                <div class="contract-notes-section">
                                    <div class="section-header">
                                        <h4><i class="fas fa-sticky-note"></i> Notes & Attachments</h4>
                                    </div>
                                    <div class="notes-content">
                                        <div class="special-notes">
                                            <h5>Special Notes</h5>
                                            <div class="note-item">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <span>Customer requires work to be completed before September 10th due
                                                    to family event.</span>
                                            </div>
                                            <div class="note-item">
                                                <i class="fas fa-info-circle"></i>
                                                <span>Property access available Monday to Friday, 9 AM - 5 PM.</span>
                                            </div>
                                        </div>
                                        <div class="attachments-list">
                                            <h5>Contract Attachments</h5>
                                            <div class="attachment-items">
                                                <div class="attachment-item">
                                                    <i class="fas fa-file-pdf"></i>
                                                    <div class="attachment-info">
                                                        <span class="filename">Signed_Contract_CT-2025-015.pdf</span>
                                                        <span class="filesize">2.4 MB</span>
                                                    </div>
                                                    <button class="download-attachment">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                                <div class="attachment-item">
                                                    <i class="fas fa-file-image"></i>
                                                    <div class="attachment-info">
                                                        <span class="filename">Property_Photos.zip</span>
                                                        <span class="filesize">5.8 MB</span>
                                                    </div>
                                                    <button class="download-attachment">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                                <div class="attachment-item">
                                                    <i class="fas fa-file-alt"></i>
                                                    <div class="attachment-info">
                                                        <span class="filename">Insurance_Documentation.pdf</span>
                                                        <span class="filesize">1.2 MB</span>
                                                    </div>
                                                    <button class="download-attachment">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Milestones Tab -->
                        <div class="drawer-tab-content" id="milestones">
                            <div class="milestones-section">
                                <h4>Milestone Timeline</h4>
                                <div class="milestone-timeline">
                                    <div class="milestone-item completed">
                                        <div class="milestone-status">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="milestone-content">
                                            <h5>Initial Assessment</h5>
                                            <p>Site inspection and damage evaluation</p>
                                            <div class="milestone-meta">
                                                <span class="milestone-amount">LKR 37,500</span>
                                                <span class="milestone-date">Completed Aug 16</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="milestone-item active">
                                        <div class="milestone-status">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="milestone-content">
                                            <h5>Parts Procurement & Repair</h5>
                                            <p>Order parts and perform repairs</p>
                                            <div class="milestone-meta">
                                                <span class="milestone-amount">LKR 75,000</span>
                                                <span class="milestone-date">Due Sep 5</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="milestone-item pending">
                                        <div class="milestone-status">
                                            <i class="fas fa-circle"></i>
                                        </div>
                                        <div class="milestone-content">
                                            <h5>Final Testing & Handover</h5>
                                            <p>System testing and customer handover</p>
                                            <div class="milestone-meta">
                                                <span class="milestone-amount">LKR 37,500</span>
                                                <span class="milestone-date">Due Sep 10</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Workforce Tab -->
                        <div class="drawer-tab-content" id="workforce">
                            <div class="workforce-section">
                                <div class="workforce-header">
                                    <h4>Assigned Team</h4>
                                    <button class="assign-btn"><i class="fas fa-plus"></i> Assign Repairer</button>
                                </div>

                                <div class="staff-section">
                                    <h5>Permanent Staff</h5>
                                    <div class="staff-counts">
                                        <div class="staff-count-item">
                                            <span class="role">Electricians</span>
                                            <span class="count">2 assigned</span>
                                        </div>
                                        <div class="staff-count-item">
                                            <span class="role">Technicians</span>
                                            <span class="count">1 assigned</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="freelancer-section">
                                    <h5>Freelance Repairers</h5>
                                    <div class="freelancer-list">
                                        <div class="freelancer-item">
                                            <div class="freelancer-avatar verified">
                                                <span>KP</span>
                                                <i class="fas fa-check-circle verify-badge"></i>
                                            </div>
                                            <div class="freelancer-info">
                                                <div class="freelancer-name">Kamal Perera</div>
                                                <div class="freelancer-role">Senior Electrician</div>
                                                <div class="freelancer-rating">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <span>5.0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Files Tab -->
                        <div class="drawer-tab-content" id="files">
                            <div class="files-section">
                                <div class="files-header">
                                    <h4>Project Files</h4>
                                    <button class="upload-btn"><i class="fas fa-upload"></i> Upload File</button>
                                </div>
                                <div class="files-list">
                                    <div class="file-item">
                                        <i class="fas fa-file-pdf file-icon"></i>
                                        <div class="file-info">
                                            <div class="file-name">Initial_Assessment_Report.pdf</div>
                                            <div class="file-meta">2.3 MB • Uploaded Aug 16, 2025</div>
                                        </div>
                                        <button class="download-btn"><i class="fas fa-download"></i></button>
                                    </div>
                                    <div class="file-item">
                                        <i class="fas fa-image file-icon"></i>
                                        <div class="file-info">
                                            <div class="file-name">damage_photos.zip</div>
                                            <div class="file-meta">8.7 MB • Uploaded Aug 15, 2025</div>
                                        </div>
                                        <button class="download-btn"><i class="fas fa-download"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Communication Tab -->
                        <div class="drawer-tab-content" id="communication">
                            <div class="communication-section">
                                <div class="chat-messages">
                                    <div class="message customer">
                                        <div class="message-avatar">JP</div>
                                        <div class="message-content">
                                            <div class="message-header">
                                                <span class="sender">John Perera</span>
                                                <span class="time">Today, 2:30 PM</span>
                                            </div>
                                            <div class="message-text">When will the repair work start?</div>
                                        </div>
                                    </div>
                                    <div class="message company">
                                        <div class="message-avatar">FL</div>
                                        <div class="message-content">
                                            <div class="message-header">
                                                <span class="sender">FixLanka Team</span>
                                                <span class="time">Today, 2:45 PM</span>
                                            </div>
                                            <div class="message-text">We'll start the repair work tomorrow morning at 9
                                                AM. Our technician Kamal will be there.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="message-input">
                                    <input type="text" placeholder="Type your message...">
                                    <button class="send-btn"><i class="fas fa-paper-plane"></i></button>
                                </div>
                            </div>
                        </div>

                        <!-- Payments Tab -->
                        <div class="drawer-tab-content" id="payments">
                            <div class="payments-section">
                                <h4>Payment Status</h4>
                                <div class="payment-timeline">
                                    <div class="payment-item completed">
                                        <div class="payment-status">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="payment-content">
                                            <h5>Initial Payment</h5>
                                            <div class="payment-amount">LKR 37,500</div>
                                            <div class="payment-date">Paid Aug 16, 2025</div>
                                        </div>
                                    </div>
                                    <div class="payment-item pending">
                                        <div class="payment-status">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="payment-content">
                                            <h5>Progress Payment</h5>
                                            <div class="payment-amount">LKR 75,000</div>
                                            <div class="payment-date">Due Sep 5, 2025</div>
                                        </div>
                                    </div>
                                    <div class="payment-item upcoming">
                                        <div class="payment-status">
                                            <i class="fas fa-circle"></i>
                                        </div>
                                        <div class="payment-content">
                                            <h5>Final Payment</h5>
                                            <div class="payment-amount">LKR 37,500</div>
                                            <div class="payment-date">Due Sep 10, 2025</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issues Section (shown when Issues tab is active) -->
            <section class="issues-section" style="display: none;">
                <div class="section-header">
                    <h3>Project Issues</h3>
                    <button class="action-btn primary"><i class="fas fa-plus"></i> Raise New Issue</button>
                </div>
                <div class="issues-list">
                    <div class="issue-item">
                        <div class="issue-status open">Open</div>
                        <div class="issue-content">
                            <h4>Delayed Parts Delivery</h4>
                            <p>Air conditioning parts delivery is delayed by 3 days due to supplier issues.</p>
                            <div class="issue-meta">
                                <span class="assigned-to">Assigned to: Company Team</span>
                                <span class="issue-date">Raised: Aug 28, 2025</span>
                            </div>
                        </div>
                    </div>
                    <div class="issue-item">
                        <div class="issue-status resolved">Resolved</div>
                        <div class="issue-content">
                            <h4>Site Access Permission</h4>
                            <p>Customer availability for site access was resolved with alternate timing.</p>
                            <div class="issue-meta">
                                <span class="assigned-to">Assigned to: Project Manager</span>
                                <span class="issue-date">Resolved: Aug 25, 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Feedback Section (shown when appropriate) -->
            <section class="feedback-section" style="display: none;">
                <div class="section-header">
                    <h3>Customer Feedback</h3>
                    <div class="overall-rating">
                        <span class="rating-label">Overall Rating:</span>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-value">4.8/5</span>
                    </div>
                </div>

                <div class="feedback-breakdown">
                    <div class="category-rating">
                        <span class="category">Quality:</span>
                        <div class="category-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span>5.0</span>
                    </div>
                    <div class="category-rating">
                        <span class="category">Timeliness:</span>
                        <div class="category-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <span>4.0</span>
                    </div>
                    <div class="category-rating">
                        <span class="category">Communication:</span>
                        <div class="category-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <span>5.0</span>
                    </div>
                </div>

                <div class="feedback-reviews">
                    <div class="review-item">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">JP</div>
                                <div>
                                    <div class="reviewer-name">John Perera</div>
                                    <div class="review-project">Air Conditioner Repair</div>
                                </div>
                            </div>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div class="review-content">
                            <p>Excellent service! The team was professional and completed the work on time. Very
                                satisfied with the quality.</p>
                        </div>
                        <div class="review-actions">
                            <button class="reply-btn">Reply</button>
                            <span class="review-date">Aug 20, 2025</span>
                        </div>
                    </div>
                </div>
            </section>

    </div> <!-- End projects-container -->
    </main> <!-- End main-content -->
    </div> <!-- End dashboard-container -->

    <!-- Project Start Options Modal -->
    <div class="project-start-modal" id="projectStartModal">
        <div class="modal-overlay-blur" onclick="closeProjectStartModal()"></div>
        <div class="project-start-content">
            <button class="modal-close-btn" onclick="closeProjectStartModal()">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="modal-header-section">
                <div class="modal-icon-wrapper">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2>Start a New Project</h2>
                <p class="modal-subtitle">Choose how you'd like to begin your new project</p>
            </div>

            <div class="project-options-grid">
                <!-- Option 1: Browse Repair Requests -->
                <div class="project-option-card" onclick="navigateToRepairRequests()">
                    <div class="option-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Browse Repair Requests</h3>
                    <p>View and accept incoming repair requests from customers</p>
                    <div class="option-steps">
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Review customer requests</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Accept suitable requests</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Create contract</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Start project</span>
                        </div>
                    </div>
                    <button class="option-action-btn primary">
                        <i class="fas fa-arrow-right"></i>
                        Go to Repair Requests
                    </button>
                </div>

                <!-- Option 2: From Signed Contract -->
                <div class="project-option-card" onclick="navigateToContracts()">
                    <div class="option-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3>From Signed Contract</h3>
                    <p>Convert an already signed contract into an active project</p>
                    <div class="option-steps">
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>View signed contracts</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Verify agreement terms</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Initialize project</span>
                        </div>
                        <div class="step-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Begin work</span>
                        </div>
                    </div>
                    <button class="option-action-btn secondary">
                        <i class="fas fa-arrow-right"></i>
                        Go to Contracts
                    </button>
                </div>
            </div>

            <div class="modal-footer-note">
                <i class="fas fa-info-circle"></i>
                <p><strong>Note:</strong> All projects must originate from either a customer repair request or a signed contract to ensure proper documentation and workflow.</p>
            </div>
        </div>
    </div>


    <script>
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            loadComponent('sidebar-container', 'sidebar.php');
            loadComponent('header-container', 'topbar.php');
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    document.getElementById(containerId).innerHTML = html;

                    if (containerId === 'sidebar-container') {
                        const allNavItems = document.querySelectorAll('.sidebar .nav-item');
                        allNavItems.forEach(item => item.classList.remove('active'));

                        const projectsLink = document.querySelector('.sidebar a[href="projects.php"]');
                        if (projectsLink) {
                            projectsLink.parentElement.classList.add('active');
                        }
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
                    console.error(`Error loading ${componentFile}:`, error);
                });
        }

        // Project Start Modal Functions
        function showProjectStartOptions() {
            const modal = document.getElementById('projectStartModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeProjectStartModal() {
            const modal = document.getElementById('projectStartModal');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function navigateToRepairRequests() {
            // Show a brief loading/transition message
            showNavigationMessage('Redirecting to Repair Requests...', 'info');
            setTimeout(() => {
                window.location.href = 'repair-requests.php';
            }, 500);
        }

        function navigateToContracts() {
            // Show a brief loading/transition message
            showNavigationMessage('Redirecting to Contracts...', 'info');
            setTimeout(() => {
                window.location.href = 'contracts.php';
            }, 500);
        }

        function showNavigationMessage(message, type) {
            // Create a temporary notification
            const notification = document.createElement('div');
            notification.className = `navigation-notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'info' ? 'info-circle' : 'check-circle'}"></i>
                <span>${message}</span>
            `;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                padding: 16px 24px;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(10, 186, 181, 0.3);
                z-index: 10001;
                display: flex;
                align-items: center;
                gap: 12px;
                font-weight: 600;
                animation: slideInRight 0.3s ease;
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProjectStartModal();
            }
        });
    </script>

    <!-- Export Modal -->
    <div class="modal-overlay" id="exportModal" style="display: none;">
        <div class="export-modal">
            <div class="modal-header">
                <h3><i class="fas fa-file-export"></i> Export Projects</h3>
                <button class="close-modal" onclick="closeExportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="export-options">
                    <div class="form-group">
                        <label>Export Format</label>
                        <div class="format-options">
                            <label class="format-option">
                                <input type="radio" name="exportFormat" value="csv" checked>
                                <div class="format-card">
                                    <i class="fas fa-file-csv"></i>
                                    <span>CSV</span>
                                    <small>Comma-separated values</small>
                                </div>
                            </label>
                            <label class="format-option">
                                <input type="radio" name="exportFormat" value="excel">
                                <div class="format-card">
                                    <i class="fas fa-file-excel"></i>
                                    <span>Excel</span>
                                    <small>Microsoft Excel format</small>
                                </div>
                            </label>
                            <label class="format-option">
                                <input type="radio" name="exportFormat" value="pdf">
                                <div class="format-card">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PDF</span>
                                    <small>Portable Document Format</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Data to Export</label>
                        <div class="data-options">
                            <label class="checkbox-option">
                                <input type="radio" name="dataRange" value="all" checked>
                                <span>All Projects</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="radio" name="dataRange" value="visible">
                                <span>Visible/Filtered Projects Only</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Columns to Include</label>
                        <div class="columns-grid">
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="title" checked>
                                <span>Project Title</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="customer" checked>
                                <span>Customer</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="timeline" checked>
                                <span>Timeline</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="contract" checked>
                                <span>Contract Status</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="progress" checked>
                                <span>Progress</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="budget" checked>
                                <span>Budget</span>
                            </label>
                            <label class="checkbox-option">
                                <input type="checkbox" name="column" value="status" checked>
                                <span>Status</span>
                            </label>
                        </div>
                    </div>

                    <div class="export-info">
                        <i class="fas fa-info-circle"></i>
                        <span>The export will include data based on your selected filters and options.</span>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeExportModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn-primary" onclick="executeExport()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

</body>

</html>
