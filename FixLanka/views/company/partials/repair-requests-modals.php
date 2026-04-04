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
                                class="form-input" min="0" max="24" step="0.5" value="8.00" required readonly
                                style="background-color: #f8f9fa; cursor: not-allowed;">
                            <small class="form-hint">Auto-calculated from Start Time and End Time</small>
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
                                Auto-calculated: Estimated Duration (Days) × Daily Work Hours
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
                            class="form-input" placeholder="e.g., 5" min="1" max="365" required readonly
                            style="background-color: #f8f9fa; cursor: not-allowed;">
                        <small class="form-hint">Auto-calculated from the date range and Work Schedule Type (excludes weekends for Weekdays Only)</small>
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
