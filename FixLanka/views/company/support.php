<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - FixLanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
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
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <div id="header-container"></div>

            <div class="support-container">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-life-ring"></i> Support Center</h1>
                                <p class="subtitle">Get help and manage support tickets</p>
                                <nav class="breadcrumbs">
                                    <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                                    <span class="separator">/</span>
                                    <span class="current">Support</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn primary" id="newTicketBtn">
                                        <i class="fas fa-plus"></i> New Ticket
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Support Dashboard Grid -->
                <section class="support-dashboard">
                    <!-- Recent Tickets -->
                    <div class="recent-tickets-panel">
                        <div class="panel-header">
                            <h2>Recent Support Tickets</h2>
                            <button class="view-all-btn">View All Tickets</button>
                        </div>
                        <div class="tickets-list">
                            <div class="ticket-item high-priority">
                                <div class="ticket-id">#SP-001</div>
                                <div class="ticket-details">
                                    <h4>Payment Gateway Error</h4>
                                    <p>Customer unable to complete payment process</p>
                                    <span class="ticket-meta">John Doe • 2 hours ago</span>
                                </div>
                                <div class="ticket-priority high">High</div>
                                <div class="ticket-status open">Open</div>
                            </div>

                            <div class="ticket-item medium-priority">
                                <div class="ticket-id">#SP-002</div>
                                <div class="ticket-details">
                                    <h4>Profile Update Issue</h4>
                                    <p>Unable to update contact information</p>
                                    <span class="ticket-meta">Sarah Wilson • 4 hours ago</span>
                                </div>
                                <div class="ticket-priority medium">Medium</div>
                                <div class="ticket-status in-progress">In Progress</div>
                            </div>

                            <div class="ticket-item low-priority">
                                <div class="ticket-id">#SP-003</div>
                                <div class="ticket-details">
                                    <h4>Feature Request</h4>
                                    <p>Request for dark mode theme</p>
                                    <span class="ticket-meta">Mike Johnson • 1 day ago</span>
                                </div>
                                <div class="ticket-priority low">Low</div>
                                <div class="ticket-status pending">Pending</div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Categories -->
                    <div class="support-categories-panel">
                        <h2>Support Categories</h2>
                        <div class="categories-grid">
                            <div class="category-card">
                                <i class="fas fa-credit-card"></i>
                                <h3>Payment Issues</h3>
                                <p>Billing and payment problems</p>
                                <span class="ticket-count">5 tickets</span>
                            </div>
                            <div class="category-card">
                                <i class="fas fa-bug"></i>
                                <h3>Technical Issues</h3>
                                <p>System bugs and errors</p>
                                <span class="ticket-count">8 tickets</span>
                            </div>
                            <div class="category-card">
                                <i class="fas fa-user"></i>
                                <h3>Account Issues</h3>
                                <p>Profile and account problems</p>
                                <span class="ticket-count">3 tickets</span>
                            </div>
                            <div class="category-card">
                                <i class="fas fa-lightbulb"></i>
                                <h3>Feature Requests</h3>
                                <p>New feature suggestions</p>
                                <span class="ticket-count">7 tickets</span>
                            </div>
                        </div>
                    </div>

                    <!-- Knowledge Base -->
                    <div class="knowledge-base-panel">
                        <h2>Knowledge Base</h2>
                        <div class="kb-items">
                            <div class="kb-item">
                                <i class="fas fa-file-alt"></i>
                                <div class="kb-content">
                                    <h4>How to reset your password</h4>
                                    <p>Step-by-step guide for password recovery</p>
                                </div>
                                <span class="kb-views">1.2k views</span>
                            </div>
                            <div class="kb-item">
                                <i class="fas fa-video"></i>
                                <div class="kb-content">
                                    <h4>Getting started tutorial</h4>
                                    <p>Complete video walkthrough for new users</p>
                                </div>
                                <span class="kb-views">856 views</span>
                            </div>
                            <div class="kb-item">
                                <i class="fas fa-book"></i>
                                <div class="kb-content">
                                    <h4>User manual</h4>
                                    <p>Comprehensive guide to all features</p>
                                </div>
                                <span class="kb-views">2.1k views</span>
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
                                                Steps to Reproduce (Optional)
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

    <script src="dashboard.js"></script>
    <script>
        // Load components when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            Promise.all([
                loadComponent('sidebar-container', '/2nd-Year-Group-Project/FixLanka/views/company/sidebar.php'),
                loadComponent('header-container', '/2nd-Year-Group-Project/FixLanka/views/company/topbar.php')
            ]).then(() => {
                initializeTicketForm();
            });
        });

        // Function to load HTML components
        function loadComponent(containerId, componentFile) {
            return fetch(componentFile)
                .then(response => response.text())
                .then(html => {
                    if (containerId === 'sidebar-container') {
                        // Set active state immediately in the HTML before inserting
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        
                        // Remove any existing active classes
                        const allNavItems = tempDiv.querySelectorAll('.nav-item');
                        allNavItems.forEach(item => item.classList.remove('active'));
                        
                        // Set support as active immediately
                        const supportLink = tempDiv.querySelector('a[href="/2nd-Year-Group-Project/FixLanka/company-support"]');
                        if (supportLink) {
                            supportLink.parentElement.classList.add('active');
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

        // New Ticket Form Functionality
        function initializeTicketForm() {
            const modal = document.getElementById('ticketModal');
            const newTicketBtn = document.getElementById('newTicketBtn');
            const closeModal = document.getElementById('closeModal');
            const overlay = modal.querySelector('.modal-overlay');
            const form = document.getElementById('ticketForm');
            const steps = document.querySelectorAll('.form-step');
            const progressSteps = document.querySelectorAll('.step');
            const progressFill = document.querySelector('.progress-fill');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const successState = document.getElementById('successState');

            let currentStep = 1;
            const totalSteps = 3;

            // Modal controls
            newTicketBtn.addEventListener('click', () => openModal());
            closeModal.addEventListener('click', () => closeModalFunc());
            overlay.addEventListener('click', () => closeModalFunc());

            // Form navigation
            nextBtn.addEventListener('click', () => nextStep());
            prevBtn.addEventListener('click', () => prevStep());
            submitBtn.addEventListener('click', (e) => submitForm(e));

            // Edit step buttons
            document.querySelectorAll('.edit-step').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const step = parseInt(e.target.closest('.edit-step').dataset.step);
                    goToStep(step);
                });
            });

            // Character counters
            initializeCharCounters();

            // File upload
            initializeFileUpload();

            // Form validation
            initializeValidation();

            // Success state actions
            document.getElementById('createAnotherBtn').addEventListener('click', () => {
                resetForm();
                successState.style.display = 'none';
                form.style.display = 'block';
                goToStep(1);
            });

            document.getElementById('closeSuccessBtn').addEventListener('click', () => closeModalFunc());
            document.getElementById('viewTicketBtn').addEventListener('click', () => {
                // Simulate viewing ticket
                alert('Redirecting to ticket view...');
                closeModalFunc();
            });

            function openModal() {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                resetForm();
            }

            function closeModalFunc() {
                modal.classList.remove('active');
                document.body.style.overflow = '';
                setTimeout(() => {
                    resetForm();
                }, 300);
            }

            function nextStep() {
                if (validateCurrentStep()) {
                    if (currentStep < totalSteps) {
                        currentStep++;
                        updateStep();
                    }
                }
            }

            function prevStep() {
                if (currentStep > 1) {
                    currentStep--;
                    updateStep();
                }
            }

            function goToStep(step) {
                currentStep = step;
                updateStep();
            }

            function updateStep() {
                // Update form steps
                steps.forEach((step, index) => {
                    step.classList.toggle('active', index + 1 === currentStep);
                });

                // Update progress steps
                progressSteps.forEach((step, index) => {
                    const stepNum = index + 1;
                    step.classList.toggle('active', stepNum === currentStep);
                    step.classList.toggle('completed', stepNum < currentStep);
                });

                // Update progress bar
                const progressPercent = (currentStep / totalSteps) * 100;
                progressFill.style.width = progressPercent + '%';

                // Update navigation buttons
                prevBtn.style.display = currentStep > 1 ? 'flex' : 'none';
                nextBtn.style.display = currentStep < totalSteps ? 'flex' : 'none';
                submitBtn.style.display = currentStep === totalSteps ? 'flex' : 'none';

                // Update review content if on step 3
                if (currentStep === 3) {
                    updateReviewContent();
                }
            }

            function validateCurrentStep() {
                const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
                const requiredFields = currentStepElement.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });

                return isValid;
            }

            function validateField(field) {
                const value = field.value.trim();
                const fieldGroup = field.closest('.form-group');
                const errorElement = fieldGroup.querySelector('.field-error');
                let isValid = true;
                let errorMessage = '';

                // Check if required field is empty
                if (field.hasAttribute('required') && !value) {
                    isValid = false;
                    errorMessage = 'This field is required';
                }

                // Field-specific validation
                switch (field.id) {
                    case 'ticketTitle':
                        if (value && value.length < 10) {
                            isValid = false;
                            errorMessage = 'Title must be at least 10 characters long';
                        }
                        break;
                    case 'ticketDescription':
                        if (value && value.length < 20) {
                            isValid = false;
                            errorMessage = 'Description must be at least 20 characters long';
                        }
                        break;
                    case 'agreement':
                        if (!field.checked) {
                            isValid = false;
                            errorMessage = 'You must agree to the terms and conditions';
                        }
                        break;
                }

                // Update field appearance
                field.classList.toggle('error', !isValid);
                
                // Show/hide error message
                if (errorMessage) {
                    errorElement.textContent = errorMessage;
                    errorElement.classList.add('show');
                } else {
                    errorElement.classList.remove('show');
                }

                return isValid;
            }

            function initializeCharCounters() {
                const fieldsWithCounters = ['ticketTitle', 'ticketDescription', 'ticketSteps'];
                
                fieldsWithCounters.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    const counter = field.closest('.form-group').querySelector('.char-counter');
                    const currentSpan = counter.querySelector('.current');
                    const max = parseInt(counter.querySelector('.max').textContent);

                    field.addEventListener('input', () => {
                        const current = field.value.length;
                        currentSpan.textContent = current;
                        
                        // Update counter color based on usage
                        counter.classList.remove('warning', 'danger');
                        if (current > max * 0.9) {
                            counter.classList.add('danger');
                        } else if (current > max * 0.7) {
                            counter.classList.add('warning');
                        }
                    });
                });
            }

            function initializeFileUpload() {
                const uploadArea = document.getElementById('fileUploadArea');
                const fileInput = document.getElementById('ticketAttachments');
                const uploadedFiles = uploadArea.querySelector('.uploaded-files');
                let files = [];

                // Click to browse
                uploadArea.addEventListener('click', (e) => {
                    if (e.target.classList.contains('upload-link') || e.target.closest('.upload-placeholder')) {
                        fileInput.click();
                    }
                });

                // File input change
                fileInput.addEventListener('change', (e) => {
                    handleFiles(Array.from(e.target.files));
                });

                // Drag and drop
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', (e) => {
                    e.preventDefault();
                    if (!uploadArea.contains(e.relatedTarget)) {
                        uploadArea.classList.remove('dragover');
                    }
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    handleFiles(Array.from(e.dataTransfer.files));
                });

                function handleFiles(newFiles) {
                    newFiles.forEach(file => {
                        if (file.size > 5 * 1024 * 1024) { // 5MB limit
                            alert(`${file.name} is too large. Maximum file size is 5MB.`);
                            return;
                        }

                        if (!isValidFileType(file)) {
                            alert(`${file.name} is not a supported file type.`);
                            return;
                        }

                        files.push(file);
                        addFileToDisplay(file);
                    });
                }

                function isValidFileType(file) {
                    const allowedTypes = [
                        'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                        'application/pdf', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain'
                    ];
                    return allowedTypes.includes(file.type);
                }

                function addFileToDisplay(file) {
                    const fileElement = document.createElement('div');
                    fileElement.className = 'uploaded-file';
                    fileElement.innerHTML = `
                        <i class="fas fa-file-alt file-icon"></i>
                        <div class="file-info">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                        <button type="button" class="remove-file" data-filename="${file.name}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;

                    fileElement.querySelector('.remove-file').addEventListener('click', () => {
                        files = files.filter(f => f.name !== file.name);
                        fileElement.remove();
                    });

                    uploadedFiles.appendChild(fileElement);
                }

                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }
            }

            function initializeValidation() {
                // Real-time validation for required fields
                form.querySelectorAll('[required]').forEach(field => {
                    field.addEventListener('blur', () => validateField(field));
                    field.addEventListener('input', () => {
                        if (field.classList.contains('error')) {
                            validateField(field);
                        }
                    });
                });
            }

            function updateReviewContent() {
                const formData = new FormData(form);
                
                // Basic information review
                const basicReview = document.getElementById('reviewBasic');
                basicReview.innerHTML = `
                    <div class="review-item">
                        <span class="review-label">Title:</span>
                        <span class="review-value">${formData.get('title') || 'Not specified'}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Category:</span>
                        <span class="review-value">${getCategoryLabel(formData.get('category'))}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Priority:</span>
                        <span class="review-value">
                            <span class="priority-badge ${formData.get('priority')}">${getPriorityLabel(formData.get('priority'))}</span>
                        </span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Project:</span>
                        <span class="review-value">${getProjectLabel(formData.get('project'))}</span>
                    </div>
                `;

                // Details review
                const detailsReview = document.getElementById('reviewDetails');
                const urgency = formData.get('urgency');
                const affected = formData.getAll('affected[]');
                
                detailsReview.innerHTML = `
                    <div class="review-item">
                        <span class="review-label">Description:</span>
                        <span class="review-value">${truncateText(formData.get('description') || 'Not provided', 100)}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Urgency:</span>
                        <span class="review-value">${getUrgencyLabel(urgency)}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Affected:</span>
                        <span class="review-value">${affected.map(a => getAffectedLabel(a)).join(', ')}</span>
                    </div>
                    <div class="review-item">
                        <span class="review-label">Attachments:</span>
                        <span class="review-value">${document.querySelectorAll('.uploaded-file').length} file(s)</span>
                    </div>
                `;
            }

            function getCategoryLabel(value) {
                const labels = {
                    'payment': 'Payment Issues',
                    'technical': 'Technical Issues',
                    'account': 'Account Issues',
                    'feature': 'Feature Request',
                    'billing': 'Billing Inquiry',
                    'other': 'Other'
                };
                return labels[value] || 'Not specified';
            }

            function getPriorityLabel(value) {
                const labels = {
                    'low': 'Low',
                    'medium': 'Medium',
                    'high': 'High',
                    'urgent': 'Urgent'
                };
                return labels[value] || 'Not specified';
            }

            function getProjectLabel(value) {
                const labels = {
                    'pr-001': 'Air Conditioner Repair - Colombo (#PR-001)',
                    'pr-002': 'Kitchen Renovation - Kandy (#PR-002)',
                    'pr-003': 'Plumbing Service - Galle (#PR-003)'
                };
                return labels[value] || 'None selected';
            }

            function getUrgencyLabel(value) {
                const labels = {
                    'can-wait': 'Can wait (48h response)',
                    'soon': 'Need help soon (24h response)',
                    'asap': 'ASAP (4h response)'
                };
                return labels[value] || 'Not specified';
            }

            function getAffectedLabel(value) {
                const labels = {
                    'just-me': 'Just me',
                    'my-team': 'My team',
                    'customers': 'Our customers'
                };
                return labels[value] || value;
            }

            function truncateText(text, maxLength) {
                if (text.length <= maxLength) return text;
                return text.substring(0, maxLength) + '...';
            }

            function submitForm(e) {
                e.preventDefault();
                
                if (!validateCurrentStep()) {
                    return;
                }

                // Hide form and show success state
                form.style.display = 'none';
                successState.style.display = 'block';
                
                // Generate ticket ID
                const ticketId = '#SP-' + String(Math.floor(Math.random() * 1000) + 100).padStart(3, '0');
                document.getElementById('ticketId').textContent = ticketId;
            }

            function resetForm() {
                form.reset();
                currentStep = 1;
                updateStep();
                
                // Clear uploaded files
                document.querySelector('.uploaded-files').innerHTML = '';
                
                // Clear error states
                form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
                form.querySelectorAll('.field-error.show').forEach(el => el.classList.remove('show'));
                
                // Reset character counters
                form.querySelectorAll('.char-counter .current').forEach(el => el.textContent = '0');
                
                // Hide success state and show form
                successState.style.display = 'none';
                form.style.display = 'block';
            }

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (modal.classList.contains('active')) {
                    if (e.key === 'Escape') {
                        closeModalFunc();
                    }
                }
            });
        }
    </script>
</body>

</html>



