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
    <title>Support - FixLanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/support.css">
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

            <div class="support-container">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-life-ring"></i> Support Center</h1>
                                <p class="subtitle">Get help and manage support tickets</p>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn secondary" id="exportTicketsBtn">
                                        <i class="fas fa-download"></i>
                                        Export
                                    </button>
                                    <button class="action-btn primary" id="newTicketBtn">
                                        <i class="fas fa-plus"></i>
                                        New Ticket
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="breadcrumbs">
                            <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                            <span class="separator">/</span>
                            <span class="current">Support</span>
                        </div>

                    </div>
                </header>

                <!-- Support Controls (Filters) -->
                <div class="support-controls">
                    <div class="filter-section">
                        <div class="filter-select-wrapper">
                            <select id="categoryFilter" class="filter-select">
                                <option value="">All Categories</option>
                                <option value="payment">Payment Issues</option>
                                <option value="technical">Technical Issues</option>
                                <option value="account">Account Issues</option>
                                <option value="feature">Feature Requests</option>
                                <option value="billing">Billing Inquiry</option>
                                <option value="other">Other</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                        <div class="filter-select-wrapper">
                            <select id="priorityFilter" class="filter-select">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                            <i class="fa-solid fa-chevron-down filter-select-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Support Dashboard Grid -->
                <section class="support-dashboard">
                    <!-- Recent Tickets -->
                    <div class="recent-tickets-panel">
                        <div class="tickets-list" id="recentTicketsList">
                            <div class="loading-state" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                <i class="fas fa-circle-notch fa-spin"></i> Loading tickets...
                            </div>
                        </div>
                    </div>




                </section>

                <!-- New Ticket Modal -->
                <div class="ticket-modal" id="ticketModal">
                    <div class="modal-overlay"></div>
                    <div class="modal-container">
                        <div class="modal-header">
                            <div class="modal-title">
                                <i class="fas fa-plus-circle"></i>
                                <h2>Create New Support Ticket</h2>
                            </div>
                            <button class="modal-close" id="closeModal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form class="ticket-form" id="ticketForm">
                            <div class="form-progress">
                                <div class="progress-steps">
                                    <div class="step active" data-step="1">
                                        <div class="step-number">1</div>
                                        <span class="step-label">Basic Info</span>
                                    </div>
                                    <div class="step" data-step="2">
                                        <div class="step-number">2</div>
                                        <span class="step-label">Details</span>
                                    </div>
                                    <div class="step" data-step="3">
                                        <div class="step-number">3</div>
                                        <span class="step-label">Review</span>
                                    </div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 33.33%;"></div>
                                </div>
                            </div>

                            <!-- Step 1: Basic Information -->
                            <div class="form-step active" data-step="1">
                                <div class="form-section">
                                    <h3>Basic Information</h3>
                                    <p class="section-description">Let's start with the basics about your support request</p>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ticketTitle" class="required">
                                                Ticket Title
                                            </label>
                                            <input 
                                                type="text" 
                                                id="ticketTitle" 
                                                name="title" 
                                                placeholder="Brief description of your issue"
                                                required
                                                maxlength="100"
                                            >
                                            <div class="char-counter">
                                                <span class="current">0</span>/<span class="max">100</span>
                                            </div>
                                            <div class="field-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group half">
                                            <label for="ticketCategory" class="required">
                                                Category
                                            </label>
                                            <select id="ticketCategory" name="category" required>
                                                <option value="">Select a category</option>
                                                <option value="payment">Payment Issues</option>
                                                <option value="technical">Technical Issues</option>
                                                <option value="account">Account Issues</option>
                                                <option value="feature">Feature Request</option>
                                                <option value="billing">Billing Inquiry</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <div class="field-error"></div>
                                        </div>

                                        <div class="form-group half">
                                            <label for="ticketPriority" class="required">
                                                Priority Level
                                            </label>
                                            <select id="ticketPriority" name="priority" required>
                                                <option value="">Select priority</option>
                                                <option value="low">Low - General inquiry</option>
                                                <option value="medium">Medium - Standard issue</option>
                                                <option value="high">High - Business impacting</option>
                                                <option value="urgent">Urgent - System down</option>
                                            </select>
                                            <div class="field-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ticketProject">
                                                Related Project (Optional)
                                            </label>
                                            <select id="ticketProject" name="project">
                                                <option value="">Select a project if applicable</option>
                                                <option value="pr-001">Air Conditioner Repair - Colombo (#PR-001)</option>
                                                <option value="pr-002">Kitchen Renovation - Kandy (#PR-002)</option>
                                                <option value="pr-003">Plumbing Service - Galle (#PR-003)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Detailed Information -->
                            <div class="form-step" data-step="2">
                                <div class="form-section">
                                    <h3>Detailed Information</h3>
                                    <p class="section-description">Provide more details to help us understand and resolve your issue</p>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ticketDescription" class="required">
                                                Description
                                            </label>
                                            <textarea 
                                                id="ticketDescription" 
                                                name="description" 
                                                placeholder="Please describe your issue in detail. Include any error messages, steps you've tried, and what you expected to happen..."
                                                required
                                                rows="6"
                                                maxlength="1000"
                                            ></textarea>
                                            <div class="char-counter">
                                                <span class="current">0</span>/<span class="max">1000</span>
                                            </div>
                                            <div class="field-error"></div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ticketSteps">
                                                What Happened (Optional)
                                            </label>
                                            <textarea 
                                                id="ticketSteps" 
                                                name="steps" 
                                                placeholder="If applicable, list the steps to reproduce the issue:&#10;1. First step&#10;2. Second step&#10;3. What happened"
                                                rows="4"
                                                maxlength="500"
                                            ></textarea>
                                            <div class="char-counter">
                                                <span class="current">0</span>/<span class="max">500</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ticketAttachments">
                                                Attachments (Optional)
                                            </label>
                                            <div class="file-upload-area" id="fileUploadArea">
                                                <input 
                                                    type="file" 
                                                    id="ticketAttachments" 
                                                    name="attachments" 
                                                    multiple 
                                                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt"
                                                    style="display: none;"
                                                >
                                                <div class="upload-placeholder">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p>Drag & drop files here or <span class="upload-link">browse</span></p>
                                                    <small>Supported: Images, PDF, Documents (Max 5MB each)</small>
                                                </div>
                                                <div class="uploaded-files"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group half">
                                            <label for="ticketUrgency">
                                                How urgent is this?
                                            </label>
                                            <div class="radio-group">
                                                <label class="radio-option">
                                                    <input type="radio" name="urgency" value="can-wait">
                                                    <span class="radio-custom"></span>
                                                    <div class="radio-content">
                                                        <strong>Can wait</strong>
                                                        <small>Response within 48 hours</small>
                                                    </div>
                                                </label>
                                                <label class="radio-option">
                                                    <input type="radio" name="urgency" value="soon" checked>
                                                    <span class="radio-custom"></span>
                                                    <div class="radio-content">
                                                        <strong>Need help soon</strong>
                                                        <small>Response within 24 hours</small>
                                                    </div>
                                                </label>
                                                <label class="radio-option">
                                                    <input type="radio" name="urgency" value="asap">
                                                    <span class="radio-custom"></span>
                                                    <div class="radio-content">
                                                        <strong>ASAP</strong>
                                                        <small>Response within 4 hours</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group half">
                                            <label>
                                                Who is affected?
                                            </label>
                                            <div class="checkbox-group">
                                                <label class="checkbox-option">
                                                    <input type="checkbox" name="affected[]" value="just-me" checked>
                                                    <span class="checkbox-custom"></span>
                                                    Just me
                                                </label>
                                                <label class="checkbox-option">
                                                    <input type="checkbox" name="affected[]" value="my-team">
                                                    <span class="checkbox-custom"></span>
                                                    My team
                                                </label>
                                                <label class="checkbox-option">
                                                    <input type="checkbox" name="affected[]" value="customers">
                                                    <span class="checkbox-custom"></span>
                                                    Our customers
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Review & Submit -->
                            <div class="form-step" data-step="3">
                                <div class="form-section">
                                    <h3>Review Your Ticket</h3>
                                    <p class="section-description">Please review your information before submitting</p>
                                    
                                    <div class="review-section">
                                        <div class="review-card">
                                            <div class="review-header">
                                                <h4>Basic Information</h4>
                                                <button type="button" class="edit-step" data-step="1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                            <div class="review-content" id="reviewBasic">
                                                <!-- Content will be populated by JavaScript -->
                                            </div>
                                        </div>

                                        <div class="review-card">
                                            <div class="review-header">
                                                <h4>Details</h4>
                                                <button type="button" class="edit-step" data-step="2">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                            <div class="review-content" id="reviewDetails">
                                                <!-- Content will be populated by JavaScript -->
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="checkbox-option agreement">
                                                <input type="checkbox" name="agreement" id="agreement" required>
                                                <span class="checkbox-custom"></span>
                                                <span class="agreement-text">
                                                    I confirm that the information provided is accurate and I agree to the 
                                                    <a href="#" target="_blank">Terms of Service</a> and 
                                                    <a href="#" target="_blank">Privacy Policy</a>
                                                </span>
                                            </label>
                                            <div class="field-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Navigation -->
                            <div class="form-navigation">
                                <button type="button" class="nav-btn secondary" id="prevBtn" style="display: none;">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                                <div class="nav-spacer"></div>
                                <button type="button" class="nav-btn primary" id="nextBtn">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                                <button type="submit" class="nav-btn primary submit-btn" id="submitBtn" style="display: none;">
                                    <i class="fas fa-paper-plane"></i> Submit Ticket
                                </button>
                            </div>
                        </form>

                        <!-- Success State -->
                        <div class="success-state" id="successState" style="display: none;">
                            <div class="success-content">
                                <div class="success-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h3>Ticket Created Successfully!</h3>
                                <p>Your support ticket has been created and assigned ID: <strong id="ticketId">#SP-004</strong></p>
                                <p>Our team will review your request and respond according to the priority level you selected.</p>
                                
                                <div class="success-actions">
                                    <button type="button" class="action-btn primary" id="viewTicketBtn">
                                        <i class="fas fa-eye"></i> View Ticket
                                    </button>
                                    <button type="button" class="action-btn secondary" id="createAnotherBtn">
                                        <i class="fas fa-plus"></i> Create Another
                                    </button>
                                    <button type="button" class="action-btn secondary" id="closeSuccessBtn">
                                        <i class="fas fa-times"></i> Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- End support-container -->
        </main>
    </div>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/support.js"></script>
    <script src="dashboard.js"></script>
    <script>
        // Any specific page init
    </script>
</body>
</html>





