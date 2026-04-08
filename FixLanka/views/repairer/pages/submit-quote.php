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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/global.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/topbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/common/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/repairer/submit-quote.css">
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
                            <!-- Hidden fields for request_id and repairer_id -->
                            <input type="hidden" id="request-id" name="request_id" value="">
                            <input type="hidden" id="repairer-id" name="repairer_id" value="1">
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="quote-amount" class="form-label">Quote Amount (LKR) *</label>
                                    <div class="input-group">
                                        <span class="input-prefix">Rs.</span>
                                        <input type="number" id="quote-amount" name="quoteAmount" class="form-input" 
                                               placeholder="0.00" min="0" step="0.01" required>
                                    </div>
                                    <small class="form-help">Total price for the job</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="estimated-days" class="form-label">Estimated Duration (Days) *</label>
                                    <input type="number" id="estimated-days" name="estimatedDays" class="form-input" 
                                           placeholder="e.g., 3" min="1" max="365" required>
                                    <small class="form-help">How many days to complete this job</small>
                                </div>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="warranty-period" class="form-label">Warranty Period (Months)</label>
                                    <select id="warranty-period" name="warrantyPeriod" class="form-input">
                                        <option value="0">No Warranty</option>
                                        <option value="1">1 Month</option>
                                        <option value="3">3 Months</option>
                                        <option value="6" selected>6 Months</option>
                                        <option value="12">1 Year</option>
                                        <option value="24">2 Years</option>
                                    </select>
                                    <small class="form-help">Warranty/guarantee period for your work</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="valid-until" class="form-label">Quote Valid Until *</label>
                                    <input type="date" id="valid-until" name="validUntil" class="form-input" required>
                                    <small class="form-help">Quote expiry date (typically 7-14 days)</small>
                                </div>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="materials-included" name="materialsIncluded" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">
                                        Materials cost included in quote
                                    </span>
                                </label>
                                <small class="form-help">Check if the quote amount includes all materials needed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="quote-message" class="form-label">Quote Details *</label>
                                <textarea id="quote-message" name="message" class="form-textarea" rows="6" 
                                          placeholder="Describe your approach, materials to be used, work schedule, and any special considerations..." required></textarea>
                                <small class="form-help">Provide detailed information about how you'll approach this job</small>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="terms-section">
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" id="terms-agreement" required>
                                        <span class="checkbox-custom"></span>
                                        <span class="checkbox-text">
                                            I agree to the <a href="#terms" class="link">terms and conditions</a> and confirm the accuracy of this quote
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
                            <span class="summary-label">Job:</span>
                            <span class="summary-value" id="confirm-job">Fix Kitchen Faucet Leak</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Quote Amount:</span>
                            <span class="summary-value" id="confirm-amount">Rs. 0.00</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Estimated Duration:</span>
                            <span class="summary-value" id="confirm-days">0 days</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Warranty:</span>
                            <span class="summary-value" id="confirm-warranty">6 months</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Valid Until:</span>
                            <span class="summary-value" id="confirm-valid-until">-</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Materials:</span>
                            <span class="summary-value" id="confirm-materials">Included</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Status:</span>
                            <span class="summary-value">Pending</span>
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
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/repairer/submit-quote.js"></script>
</body>
</html>

