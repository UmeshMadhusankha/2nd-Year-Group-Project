<?php
// Page configuration
$currentPage = 'available-jobs'; // submit-quote is accessed from available-jobs page
$pageTitle = 'Submit Quote';
$pageSubtitle = 'Provide your quote for the requested job';
$searchPlaceholder = 'Search requests, repairers, projects...';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Quote - FixLanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/common/global.css">
    <link rel="stylesheet" href="../../../assets/css/common/variables.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/repairer/submit-quote.css">
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
        <main class="main-content-wrapper">
            <div class="main-content">
                <div class="content-wrapper">
                    <!-- Page Header -->
                    <section class="page-header">
                        <div class="page-header-content">
                            <div class="page-header-text">
                                <h2 class="page-title">Submit Quote</h2>
                                <p class="page-description">Provide your quote for the requested repair job</p>
                            </div>
                            <div class="page-header-actions">
                                <button class="btn btn-secondary" id="back-to-jobs-btn">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to Jobs</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Job Details Section -->
                    <section class="job-details-section">
                        <div class="job-details-header">
                            <h3 class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Job Details
                            </h3>
                        </div>
                        
                        <div class="job-details-content">
                            <div class="job-info-grid">
                                <div class="job-info-item">
                                    <div class="job-info-label">Job Title</div>
                                    <div class="job-info-value" id="job-title">Fix Kitchen Faucet Leak</div>
                                </div>
                                <div class="job-info-item">
                                    <div class="job-info-label">Customer</div>
                                    <div class="job-info-value" id="customer-name">Sarah Johnson</div>
                                </div>
                                <div class="job-info-item">
                                    <div class="job-info-label">Location</div>
                                    <div class="job-info-value" id="job-location">Colombo 03, Sri Lanka</div>
                                </div>
                                <div class="job-info-item">
                                    <div class="job-info-label">Category</div>
                                    <div class="job-info-value" id="job-category">Plumbing</div>
                                </div>
                                <div class="job-info-item">
                                    <div class="job-info-label">Budget Range</div>
                                    <div class="job-info-value" id="job-budget">LKR 2,500 - 3,500</div>
                                </div>
                                <div class="job-info-item">
                                    <div class="job-info-label">Urgency</div>
                                    <div class="job-info-value" id="job-urgency">Medium</div>
                                </div>
                            </div>
                            
                            <div class="job-description">
                                <div class="job-info-label">Description</div>
                                <div class="job-info-value" id="job-description-text">
                                    The kitchen faucet has been leaking for the past week. The leak appears to be coming from the base of the faucet where it connects to the sink. Water is constantly dripping, and the problem seems to be getting worse. I've tried tightening the connections but it hasn't helped. The faucet is about 3 years old and was working fine until recently.
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Quote Form Section -->
                    <section class="quote-form-section">
                        <div class="quote-form-header">
                            <h3 class="section-title">
                                <i class="fas fa-file-invoice-dollar"></i>
                                Your Quote
                            </h3>
                            <p class="section-description">Please provide detailed information for your quote</p>
                        </div>
                        
                        <form class="quote-form" id="quote-form">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="quote-price" class="form-label">Price (LKR) *</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Rs.</span>
                                        <input type="number" id="quote-price" name="price" class="form-input" 
                                               placeholder="0.00" min="0" step="0.01" required>
                                    </div>
                                    <small class="form-help">Include all materials and labor costs</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="completion-time" class="form-label">Estimated Completion Time *</label>
                                    <div class="time-input-group">
                                        <input type="number" id="completion-days" name="completion-days" class="form-input time-input" 
                                               placeholder="0" min="0" max="365">
                                        <span class="time-label">Days</span>
                                        <input type="number" id="completion-hours" name="completion-hours" class="form-input time-input" 
                                               placeholder="0" min="0" max="23">
                                        <span class="time-label">Hours</span>
                                    </div>
                                    <small class="form-help">Estimated time to complete the job</small>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="quote-notes" class="form-label">Notes *</label>
                                <textarea id="quote-notes" name="notes" class="form-textarea" rows="6" 
                                          placeholder="Describe your approach, materials needed, any special considerations..." required></textarea>
                                <small class="form-help">Provide details about how you'll approach this job</small>
                            </div>
                            
                            <!-- Quote Breakdown Section -->
                            <div class="quote-breakdown">
                                <h4 class="breakdown-title">
                                    <i class="fas fa-calculator"></i>
                                    Quote Breakdown (Optional)
                                </h4>
                                
                                <div class="breakdown-items" id="breakdown-items">
                                    <div class="breakdown-item">
                                        <div class="breakdown-input-group">
                                            <input type="text" class="breakdown-description" placeholder="Item description (e.g., New faucet cartridge)">
                                            <input type="number" class="breakdown-cost" placeholder="Cost" min="0" step="0.01">
                                            <button type="button" class="btn-remove-item" title="Remove item">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-secondary btn-sm" id="add-breakdown-item">
                                    <i class="fas fa-plus"></i>
                                    Add Item
                                </button>
                                
                                <div class="breakdown-total">
                                    <div class="total-row">
                                        <span class="total-label">Breakdown Total:</span>
                                        <span class="total-amount" id="breakdown-total">Rs. 0.00</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="terms-section">
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="terms-agreement" required>
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text">
                                            I agree to the <a href="#terms" class="link">terms and conditions</a> and confirm that this quote is valid for 7 days
                                        </span>
                                    </label>
                                </div>
                                
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="warranty-offered">
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text">
                                            I offer a warranty/guarantee for this work
                                        </span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="button" class="btn btn-secondary" id="cancel-quote-btn">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </button>
                                <button type="button" class="btn btn-outline" id="save-draft-btn">
                                    <i class="fas fa-save"></i>
                                    <span>Save Draft</span>
                                </button>
                                <button type="submit" class="btn btn-primary" id="submit-quote-btn">
                                    <i class="fas fa-paper-plane"></i>
                                    <span>Submit Quote</span>
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal-overlay" id="confirmation-modal-overlay">
        <div class="confirmation-modal" id="confirmation-modal">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Quote Submission</h3>
                <button class="modal-close" id="close-confirmation-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="confirmation-content">
                    <div class="confirmation-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    
                    <div class="confirmation-details">
                        <h4>Quote Summary</h4>
                        <div class="summary-item">
                            <span class="summary-label">Total Price:</span>
                            <span class="summary-value" id="confirm-price">Rs. 0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Completion Time:</span>
                            <span class="summary-value" id="confirm-time">0 days, 0 hours</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Job:</span>
                            <span class="summary-value" id="confirm-job">Fix Kitchen Faucet Leak</span>
                        </div>
                    </div>
                    
                    <div class="confirmation-message">
                        <p>Are you sure you want to submit this quote? Once submitted, the customer will be notified and you'll be committed to this pricing.</p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn btn-secondary" id="cancel-confirmation">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button class="btn btn-primary" id="confirm-submission">
                    <i class="fas fa-check"></i>
                    Confirm & Submit
                </button>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="../../../assets/javascript/repairer/common/common.js"></script>
    <script src="../../../assets/javascript/repairer/submit-quote.js"></script>
</body>
</html>
