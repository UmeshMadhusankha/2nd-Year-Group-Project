<!-- NEW CONTRACT MODAL - UX Optimized Multi-Step Design -->
<div class="modal-overlay" id="newContractModal">
    <div class="modal-container modern-modal">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2><i class="fas fa-file-contract"></i> Create New Contract</h2>
            <button class="modal-close" id="newContractClose">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Progress Steps -->
        <div class="modal-progress">
            <div class="progress-step active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Select Project</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Project Details</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Financial Terms</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="modal-content">
            <form id="contractForm" method="POST" action="../../api/contracts/create.php">
                
                <!-- STEP 1: SELECT QUOTATION -->
                <div class="form-step active" data-step="1">
                    <div class="step-content">
                        <div class="step-header">
                            <i class="fas fa-file-invoice icon-large"></i>
                            <h3>Select Accepted Quotation</h3>
                            <p>Choose an accepted quotation to create a contract, or skip to create manually</p>
                        </div>

                        <!-- Info Message -->
                        <div class="info-message">
                            <i class="fas fa-info-circle"></i>
                            <span>Contracts can only be created from accepted quotations.</span>
                        </div>

                        <!-- Quotation Selector -->
                        <div class="quotation-selector">
                            <div class="selector-header">
                                <div class="header-left">
                                    <i class="fas fa-clipboard-check"></i>
                                    <div>
                                        <h4>Select Accepted Quotation</h4>
                                        <p>Choose a quotation to auto-fill the contract form</p>
                                    </div>
                                </div>
                                <span class="badge-count" id="quotationCount">1 available</span>
                            </div>

                            <select id="quotationSelector" name="quotation_id" class="form-select" required>
                                <option value="">-- Select Accepted Quotation or Create Manually --</option>
                                <!-- Options populated by JavaScript -->
                            </select>

                            <!-- Selected Quotation Preview -->
                            <div id="quotationPreview" class="quotation-preview" style="display: none;">
                                <div class="preview-header">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="selectedTitle">Selected: -</span>
                                </div>
                                <div class="preview-details">
                                    <div class="detail-item">
                                        <span class="label">Customer:</span>
                                        <span class="value" id="customerName">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Amount:</span>
                                        <span class="value" id="quotationAmount">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Budget Type:</span>
                                        <span class="value" id="budgetType">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Payment:</span>
                                        <span class="value" id="paymentInfo">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Pricing:</span>
                                        <span class="value" id="pricingType">-</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label">Duration:</span>
                                        <span class="value" id="duration">-</span>
                                    </div>
                                </div>
                                <div class="preview-notice">
                                    <i class="fas fa-magic"></i>
                                    <span>Form will be auto-filled. Review all fields in the next steps and modify if needed.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step Actions -->
                    <div class="step-actions">
                        <button type="button" class="btn-cancel" onclick="closeContractModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="button" class="btn-next" onclick="nextContractStep(2)">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: PROJECT DETAILS -->
                <div class="form-step" data-step="2">
                    <div class="step-content">
                        <div class="step-header">
                            <i class="fas fa-project-diagram icon-large"></i>
                            <h3>Project Details</h3>
                            <p>Review and confirm project information</p>
                        </div>

                        <!-- Auto-fill Notice -->
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span><strong>Auto-filled from quotation:</strong> Review and edit if needed</span>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Project Name <span class="required">*</span></label>
                                <input type="text" id="projectName" name="project_name" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label>Project ID</label>
                                <input type="text" class="form-input" value="Auto-generated" disabled>
                            </div>

                            <div class="form-group full-width">
                                <label>Customer Name</label>
                                <input type="text" id="customerNameInput" class="form-input" readonly>
                            </div>

                            <div class="form-group full-width">
                                <label>Site Address <span class="required">*</span></label>
                                <textarea id="siteAddress" name="site_address" class="form-textarea" rows="2" required></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Project Description <span class="required">*</span></label>
                                <textarea id="projectDescription" name="project_description" class="form-textarea" rows="4" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Start Date <span class="required">*</span></label>
                                <input type="date" id="startDate" name="start_date" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label>Completion Date <span class="required">*</span></label>
                                <input type="date" id="completionDate" name="completion_date" class="form-input" required>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-back" onclick="prevContractStep(1)">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn-next" onclick="nextContractStep(3)">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: FINANCIAL TERMS -->
                <div class="form-step" data-step="3">
                    <div class="step-content">
                        <div class="step-header">
                            <i class="fas fa-money-bill-wave icon-large"></i>
                            <h3>Financial Terms</h3>
                            <p>Define payment terms and contract value</p>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label>Total Contract Value (LKR) <span class="required">*</span></label>
                                <input type="number" id="contractValue" name="contract_value" class="form-input" min="0" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label>Pricing Type <span class="required">*</span></label>
                                <select id="pricingTypeSelect" name="pricing_type" class="form-select" required>
                                    <option value="fixed">Fixed Price</option>
                                    <option value="time_material">Time & Material</option>
                                    <option value="unit_price">Unit Price</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Advance Payment (%) <span class="required">*</span></label>
                                <input type="number" id="advancePayment" name="advance_payment" class="form-input" min="0" max="100" step="5" required>
                            </div>

                            <div class="form-group">
                                <label>Payment Method <span class="required">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="cash">Cash</option>
                                </select>
                            </div>

                            <div class="form-group full-width">
                                <label>Payment Terms <span class="required">*</span></label>
                                <textarea name="payment_terms" class="form-textarea" rows="3" placeholder="e.g., Payment due within 7 days of invoice..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Warranty Period</label>
                                <input type="text" name="warranty_period" class="form-input" placeholder="e.g., 12 months">
                            </div>

                            <div class="form-group">
                                <label>Delay Penalty</label>
                                <input type="text" name="delay_penalty" class="form-input" placeholder="e.g., LKR 5,000/day">
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="payment-summary" id="paymentSummary" style="display: none;">
                            <h4><i class="fas fa-calculator"></i> Payment Breakdown</h4>
                            <div class="summary-row">
                                <span>Advance Payment:</span>
                                <span id="advanceAmount">LKR 0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Remaining Amount:</span>
                                <span id="remainingAmount">LKR 0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="totalAmount">LKR 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-back" onclick="prevContractStep(2)">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="button" class="btn-next" onclick="nextContractStep(4)">
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 4: REVIEW -->
                <div class="form-step" data-step="4">
                    <div class="step-content">
                        <div class="step-header">
                            <i class="fas fa-clipboard-check icon-large"></i>
                            <h3>Review & Submit</h3>
                            <p>Review all contract details before creating</p>
                        </div>

                        <!-- Review Summary -->
                        <div id="reviewSummary">
                            <!-- Populated by JavaScript -->
                        </div>

                        <!-- Confirmation -->
                        <div class="confirmation-box">
                            <label class="checkbox-label">
                                <input type="checkbox" id="confirmAccurate" required>
                                <span>I confirm that all information provided is accurate and complete</span>
                            </label>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button type="button" class="btn-back" onclick="prevContractStep(3)">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <div class="action-group">
                            <button type="button" class="btn-secondary" onclick="saveDraftContract()">
                                <i class="fas fa-save"></i> Save Draft
                            </button>
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-check"></i> Create Contract
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
/* Modern Modal Styles */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-overlay.active {
    display: flex;
}

.modern-modal {
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.modal-header {
    padding: 24px 32px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    border-radius: 16px 16px 0 0;
}

.modal-header h2 {
    font-size: 22px;
    font-weight: 700;
    color: white;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Progress Steps */
.modal-progress {
    padding: 24px 32px;
    display: flex;
    align-items: center;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #9ca3af;
    transition: all 0.3s;
    margin-bottom: 8px;
}

.progress-step.active .step-circle {
    background: #06b6d4;
    border-color: #06b6d4;
    color: white;
    transform: scale(1.1);
}

.progress-step.completed .step-circle {
    background: #10b981;
    border-color: #10b981;
    color: white;
}

.step-label {
    font-size: 12px;
    color: #6b7280;
    text-align: center;
    font-weight: 500;
}

.progress-step.active .step-label {
    color: #06b6d4;
    font-weight: 600;
}

.progress-line {
    flex: 1;
    height: 2px;
    background: #d1d5db;
    margin: 0 8px 24px 8px;
}

/* Modal Content */
.modal-content {
    flex: 1;
    overflow-y: auto;
    padding: 32px;
}

/* Form Steps */
.form-step {
    display: none;
}

.form-step.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.step-content {
    min-height: 400px;
}

.step-header {
    text-align: center;
    margin-bottom: 32px;
}

.icon-large {
    font-size: 48px;
    color: #06b6d4;
    margin-bottom: 16px;
}

.step-header h3 {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 8px 0;
}

.step-header p {
    color: #6b7280;
    font-size: 15px;
    margin: 0;
}

/* Info Message */
.info-message {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #1e40af;
}

/* Quotation Selector */
.quotation-selector {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}

.selector-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.header-left {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.header-left i {
    font-size: 24px;
    color: #2e7d32;
}

.header-left h4 {
    margin: 0 0 4px 0;
    color: #2e7d32;
    font-size: 16px;
    font-weight: 600;
}

.header-left p {
    margin: 0;
    color: #558b2f;
    font-size: 13px;
}

.badge-count {
    background: #4caf50;
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* Form Elements */
.form-select, .form-input, .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.form-select:focus, .form-input:focus, .form-textarea:focus {
    outline: none;
    border-color: #06b6d4;
    box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
}

.quotation-selector .form-select {
    border-color: #81c784;
    background: white;
}

/* Quotation Preview */
.quotation-preview {
    margin-top: 20px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #81c784;
}

.preview-header {
    background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: white;
    font-weight: 600;
}

.preview-details {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.detail-item .label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 500;
}

.detail-item .value {
    font-size: 14px;
    color: #111827;
    font-weight: 600;
}

.preview-notice {
    background: #fff3e0;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #e65100;
    font-size: 13px;
    border-top: 1px solid #ffe0b2;
}

/* Alerts */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
}

.alert-success {
    background: #d1fae5;
    border-left: 4px solid #10b981;
    color: #065f46;
}

/* Form Grid */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.required {
    color: #ef4444;
}

/* Payment Summary */
.payment-summary {
    background: #f0fdf4;
    border: 2px solid #86efac;
    border-radius: 12px;
    padding: 20px;
    margin-top: 24px;
}

.payment-summary h4 {
    font-size: 16px;
    font-weight: 600;
    color: #166534;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 15px;
    color: #166534;
}

.summary-row.total {
    border-top: 2px solid #86efac;
    margin-top: 8px;
    padding-top: 12px;
    font-weight: 700;
    font-size: 18px;
    color: #14532d;
}

/* Review Summary */
#reviewSummary {
    display: grid;
    gap: 20px;
}

.review-section {
    background: #f9fafb;
    border-radius: 12px;
    padding: 20px;
}

.review-section h4 {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
}

.review-item {
    display: flex;
    padding: 8px 0;
    font-size: 14px;
}

.review-item .label {
    font-weight: 500;
    color: #6b7280;
    min-width: 150px;
}

.review-item .value {
    color: #111827;
    flex: 1;
}

/* Confirmation Box */
.confirmation-box {
    background: #eff6ff;
    border: 2px solid #3b82f6;
    border-radius: 12px;
    padding: 20px;
    margin-top: 24px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #1e40af;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

/* Step Actions */
.step-actions {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.action-group {
    display: flex;
    gap: 12px;
}

.btn-cancel, .btn-back, .btn-next, .btn-secondary, .btn-submit {
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    border: none;
}

.btn-cancel {
    background: white;
    border: 2px solid #d1d5db;
    color: #6b7280;
}

.btn-cancel:hover {
    background: #f9fafb;
}

.btn-back {
    background: white;
    border: 2px solid #d1d5db;
    color: #374151;
}

.btn-back:hover {
    background: #f9fafb;
}

.btn-next {
    background: #06b6d4;
    color: white;
    border: none;
}

.btn-next:hover {
    background: #0891b2;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-submit {
    background: #10b981;
    color: white;
}

.btn-submit:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .modern-modal {
        width: 95%;
        max-height: 95vh;
    }

    .modal-progress {
        padding: 16px;
    }

    .step-circle {
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .step-label {
        font-size: 10px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .preview-details {
        grid-template-columns: 1fr;
    }

    .step-actions {
        flex-direction: column;
    }

    .action-group {
        width: 100%;
    }

    .btn-cancel, .btn-back, .btn-next, .btn-secondary, .btn-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Modal Control
function openContractModal() {
    document.getElementById('newContractModal').classList.add('active');
    loadAcceptedQuotations();
}

function closeContractModal() {
    document.getElementById('newContractModal').classList.remove('active');
    document.getElementById('contractForm').reset();
    goToStep(1);
}

// Step Navigation
let currentContractStep = 1;

function nextContractStep(step) {
    if (validateContractStep(currentContractStep)) {
        // Update step indicators
        document.querySelector(`.progress-step[data-step="${currentContractStep}"]`).classList.remove('active');
        document.querySelector(`.progress-step[data-step="${currentContractStep}"]`).classList.add('completed');
        document.querySelector(`.progress-step[data-step="${step}"]`).classList.add('active');

        // Update form steps
        document.querySelector(`.form-step[data-step="${currentContractStep}"]`).classList.remove('active');
        document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

        currentContractStep = step;

        // Populate review on last step
        if (step === 4) {
            populateContractReview();
        }
    }
}

function prevContractStep(step) {
    // Update step indicators
    document.querySelector(`.progress-step[data-step="${currentContractStep}"]`).classList.remove('active');
    document.querySelector(`.progress-step[data-step="${step}"]`).classList.remove('completed');
    document.querySelector(`.progress-step[data-step="${step}"]`).classList.add('active');

    // Update form steps
    document.querySelector(`.form-step[data-step="${currentContractStep}"]`).classList.remove('active');
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');

    currentContractStep = step;
}

function goToStep(step) {
    // Reset all
    document.querySelectorAll('.progress-step').forEach(s => {
        s.classList.remove('active', 'completed');
    });
    document.querySelectorAll('.form-step').forEach(s => {
        s.classList.remove('active');
    });

    // Activate target
    document.querySelector(`.progress-step[data-step="${step}"]`).classList.add('active');
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    currentContractStep = step;
}

// Validation
function validateContractStep(step) {
    const currentStep = document.querySelector(`.form-step[data-step="${step}"]`);
    const required = currentStep.querySelectorAll('[required]');
    let valid = true;

    required.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            valid = false;

            field.addEventListener('input', function() {
                this.style.borderColor = '#d1d5db';
            }, { once: true });
        }
    });

    if (!valid) {
        alert('Please fill in all required fields.');
    }

    return valid;
}

// Load Quotations
function loadAcceptedQuotations() {
    // This will be replaced with actual API call
    const mockQuotations = [
        {
            id: 1,
            title: 'Kitchen Sink Pipe Leak Repair',
            customer: 'Rajitha Silva',
            amount: 15000,
            budgetType: 'FLEXIBLE',
            pricing: 'Fixed Price',
            duration: '5 days'
        }
    ];

    const selector = document.getElementById('quotationSelector');
    document.getElementById('quotationCount').textContent = `${mockQuotations.length} available`;

    mockQuotations.forEach(q => {
        const option = document.createElement('option');
        option.value = q.id;
        option.textContent = `${q.title} - ${q.customer} (LKR ${q.amount.toLocaleString()})`;
        option.dataset.quotation = JSON.stringify(q);
        selector.appendChild(option);
    });
}

// Quotation Selection Handler
document.getElementById('quotationSelector').addEventListener('change', function() {
    const preview = document.getElementById('quotationPreview');
    
    if (this.value) {
        const data = JSON.parse(this.options[this.selectedIndex].dataset.quotation);
        
        // Show preview
        document.getElementById('selectedTitle').textContent = `Selected: ${data.title}`;
        document.getElementById('customerName').textContent = data.customer;
        document.getElementById('quotationAmount').textContent = `LKR ${data.amount.toLocaleString()}`;
        document.getElementById('budgetType').textContent = data.budgetType;
        document.getElementById('pricingType').textContent = data.pricing;
        document.getElementById('duration').textContent = data.duration;
        
        preview.style.display = 'block';
        
        // Auto-fill form fields
        document.getElementById('projectName').value = data.title;
        document.getElementById('customerNameInput').value = data.customer;
        document.getElementById('contractValue').value = data.amount;
    } else {
        preview.style.display = 'none';
    }
});

// Payment Calculator
document.getElementById('contractValue').addEventListener('input', calculatePaymentBreakdown);
document.getElementById('advancePayment').addEventListener('input', calculatePaymentBreakdown);

function calculatePaymentBreakdown() {
    const value = parseFloat(document.getElementById('contractValue').value) || 0;
    const advance = parseFloat(document.getElementById('advancePayment').value) || 0;

    if (value > 0 && advance > 0) {
        const advanceAmount = (value * advance) / 100;
        const remaining = value - advanceAmount;

        document.getElementById('advanceAmount').textContent = `LKR ${advanceAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('remainingAmount').textContent = `LKR ${remaining.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('totalAmount').textContent = `LKR ${value.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('paymentSummary').style.display = 'block';
    } else {
        document.getElementById('paymentSummary').style.display = 'none';
    }
}

// Review Population
function populateContractReview() {
    const sections = [
        {
            title: 'Project Information',
            items: [
                { label: 'Project Name', value: document.getElementById('projectName').value },
                { label: 'Customer', value: document.getElementById('customerNameInput').value },
                { label: 'Start Date', value: document.getElementById('startDate').value },
                { label: 'Completion Date', value: document.getElementById('completionDate').value }
            ]
        },
        {
            title: 'Financial Terms',
            items: [
                { label: 'Contract Value', value: 'LKR ' + (parseFloat(document.getElementById('contractValue').value) || 0).toLocaleString() },
                { label: 'Advance Payment', value: (document.getElementById('advancePayment').value || '0') + '%' },
                { label: 'Pricing Type', value: document.getElementById('pricingTypeSelect').options[document.getElementById('pricingTypeSelect').selectedIndex].text }
            ]
        }
    ];

    let html = '';
    sections.forEach(section => {
        html += `<div class="review-section">
            <h4>${section.title}</h4>`;
        section.items.forEach(item => {
            html += `<div class="review-item">
                <div class="label">${item.label}:</div>
                <div class="value">${item.value || 'Not specified'}</div>
            </div>`;
        });
        html += `</div>`;
    });

    document.getElementById('reviewSummary').innerHTML = html;
}

// Form Submission
document.getElementById('contractForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!document.getElementById('confirmAccurate').checked) {
        alert('Please confirm that all information is accurate.');
        return;
    }

    // Show loading
    alert('Creating contract...');
    
    // Submit form
    this.submit();
});

// Save Draft
function saveDraftContract() {
    alert('Contract saved as draft!');
    closeContractModal();
}

// Event Listener for New Contract Button
document.getElementById('newContractBtn').addEventListener('click', openContractModal);
document.getElementById('newContractClose').addEventListener('click', closeContractModal);

// Close on overlay click
document.getElementById('newContractModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeContractModal();
    }
});
</script>
