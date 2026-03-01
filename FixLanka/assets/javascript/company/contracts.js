/**
 * FixLanka - New Contract Creation System
 * Complete 13-Section Legal Contract Form
 */

// ============================================================================
// GLOBAL STATE
// ============================================================================

let currentStep = 1;
const totalSteps = 4;
let selectedQuotation = null;
let formData = {};

// Legal clause templates
const legalTemplates = {
    variations: `Any changes to the original scope of work must be:
1. Requested in writing through the FixLanka platform
2. Reviewed and assessed for cost and timeline impact
3. Mutually approved by both parties before implementation
4. Documented with updated pricing and timeline
5. Logged in the system as a variation order

Unapproved changes will not be compensated. Both parties agree to negotiate in good faith for any necessary modifications.`,

    communication: `All official communications regarding this contract must be conducted through the FixLanka platform messaging system to ensure:
- Proper documentation and traceability
- Timestamp records of all communications
- Clear accountability for decisions

In case of disagreements, both parties agree to:
1. First attempt resolution through good-faith negotiation
2. Use the platform's mediation features if direct negotiation fails
3. Maintain professional and respectful communication at all times
4. Document all agreements reached during negotiation`,

    delays: `CLIENT-CAUSED DELAYS:
- Delays in providing site access
- Late payment of milestone amounts
- Delayed approval of designs or materials
- Changes in project requirements
- Failure to provide necessary permissions

CONTRACTOR-CAUSED DELAYS:
- Failure to meet milestone deadlines without valid reason
- Poor resource management
- Non-compliance with quality standards

RESOLUTION:
Both parties agree to communicate delays promptly and work together on reasonable timeline adjustments. Extended delays may result in contract renegotiation or termination as per clause 10.`,

    termination: `This contract may be terminated under the following conditions:

BY CLIENT:
- 7 days written notice if contractor fails to perform work
- Immediate termination for abandonment of work
- Material breach of contract terms

BY CONTRACTOR:
- Non-payment for more than 30 days after due date
- Client prevents access to site repeatedly
- Material breach by client

MUTUAL TERMINATION:
- Both parties may agree to terminate with written consent

Upon termination:
- Payment for work completed to date is required
- Materials purchased become client property upon payment
- All project documents and records are handed over
- Outstanding invoices must be settled within 14 days`,

    forceMajeure: `Neither party shall be liable for failure to perform obligations due to events beyond reasonable control, including:
- Natural disasters (floods, earthquakes, storms)
- Pandemics or epidemics
- Government actions or regulations
- War, terrorism, or civil unrest
- Strikes or labor disputes (external)
- Severe material shortages

The affected party must:
- Notify the other party within 7 days
- Provide reasonable evidence
- Take reasonable steps to minimize impact
- Resume performance as soon as possible

Timeline extensions will be granted for the duration of the force majeure event.`,

    liability: `EXCLUSIONS:
Neither party shall be liable for indirect, incidental, or consequential damages including:
- Loss of profits or business opportunities
- Loss of data or information
- Third-party claims (except as required by law)

MAXIMUM LIABILITY:
Total liability under this contract is limited to the total contract value stated in Section 5.

EXCEPTIONS:
This limitation does not apply to:
- Willful misconduct or gross negligence
- Death or personal injury
- Fraud or fraudulent misrepresentation
- Violations of applicable law

Both parties acknowledge this limitation is reasonable given the nature and value of this project.`,

    governingLaw: `GOVERNING LAW:
This contract is governed by and construed in accordance with the laws of the Democratic Socialist Republic of Sri Lanka.

ENTIRE AGREEMENT:
This document constitutes the entire agreement between the parties and supersedes all prior negotiations, representations, or agreements.

AMENDMENTS:
Any modifications must be made in writing and signed by both parties through the FixLanka platform.

SEVERABILITY:
If any provision is found invalid, the remaining provisions continue in full effect.`
};

// ============================================================================
// INITIALIZATION
// ============================================================================

$(document).ready(function() {
    console.log('Contract form initialized');
    loadAcceptedQuotations();
    initializeEventListeners();
    updateProgressBar();
    
    // Set today's date as default for contract date
    const today = new Date().toISOString().split('T')[0];
    $('#contractDate').val(today);
});

// ============================================================================
// QUOTATION LOADING
// ============================================================================

function loadAcceptedQuotations() {
    $.ajax({
        url: '../../api/contracts.php',
        method: 'GET',
        data: { action: 'getAcceptedQuotations' },
        dataType: 'json',
        success: function(response) {
            console.log('Quotations loaded:', response);
            
            if (response.success && response.data && response.data.length > 0) {
                populateQuotationDropdown(response.data);
            } else {
                $('#quotationSelect').html('<option value="">No accepted quotations available</option>');
                showNotification('No accepted quotations found. Please get a quotation accepted first.', 'warning');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading quotations:', error);
            $('#quotationSelect').html('<option value="">Error loading quotations</option>');
            showNotification('Failed to load quotations. Please try again.', 'error');
        }
    });
}

function populateQuotationDropdown(quotations) {
    let options = '<option value="">-- Select a quotation --</option>';
    
    quotations.forEach(function(quotation) {
        const clientName = quotation.customer_fname && quotation.customer_lname 
            ? `${quotation.customer_fname} ${quotation.customer_lname}`
            : 'Unknown Client';
        
        const amount = quotation.price ? parseFloat(quotation.price).toFixed(2) : '0.00';
        const title = quotation.title || 'Untitled Project';
        
        options += `<option value="${quotation.quotation_id}" 
                            data-quotation='${JSON.stringify(quotation)}'>
                        ${title} - ${clientName} (Rs. ${amount})
                    </option>`;
    });
    
    $('#quotationSelect').html(options);
}

// ============================================================================
// EVENT LISTENERS
// ============================================================================

function initializeEventListeners() {
    // Quotation selection
    $('#quotationSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            const quotationData = selectedOption.data('quotation');
            handleQuotationSelection(quotationData);
        } else {
            $('#quotationDetails').hide();
        }
    });
    
    // Payment method change
    $('#paymentMethod').on('change', function() {
        const method = $(this).val();
        if (method && method !== '') {
            generateMilestoneTable(method);
            $('#milestoneTableContainer').show();
        } else {
            $('#milestoneTableContainer').hide();
        }
    });
    
    // Contract value change - update cost breakdown
    $('#contractValue').on('change', function() {
        updateCostBreakdown();
    });
    
    // Form submission
    $('#contractForm').on('submit', function(e) {
        e.preventDefault();
        submitContract();
    });
    
    // Date validation
    $('#startDate').on('change', function() {
        const startDate = new Date($(this).val());
        const minCompletionDate = new Date(startDate);
        minCompletionDate.setDate(minCompletionDate.getDate() + 1);
        $('#completionDate').attr('min', minCompletionDate.toISOString().split('T')[0]);
    });
}

// ============================================================================
// QUOTATION SELECTION HANDLER
// ============================================================================

function handleQuotationSelection(quotation) {
    console.log('Selected quotation:', quotation);
    selectedQuotation = quotation;
    
    // Show selection details
    $('#selectedProjectTitle').text(quotation.title || 'N/A');
    $('#selectedClientName').text(
        quotation.customer_fname && quotation.customer_lname
            ? `${quotation.customer_fname} ${quotation.customer_lname}`
            : 'Unknown'
    );
    $('#selectedAmount').text(quotation.price ? parseFloat(quotation.price).toFixed(2) : '0.00');
    $('#quotationDetails').slideDown();
    
    // Auto-fill form fields
    autoFillFromQuotation(quotation);
}

function autoFillFromQuotation(quotation) {
    // Section 1: Client Information
    const clientName = quotation.customer_fname && quotation.customer_lname
        ? `${quotation.customer_fname} ${quotation.customer_lname}`
        : '';
    $('#clientName').val(clientName);
    $('#clientEmail').val(quotation.customer_email || '');
    $('#clientContact').val(quotation.customer_phone || '');
    $('#clientNIC').val(quotation.customer_nic || '');
    $('#clientAddress').val(quotation.customer_address || '');
    
    // Section 2: Project Information
    $('#projectTitle').val(quotation.title || '');
    $('#projectReferenceID').val(quotation.quotation_id || '');
    $('#projectType').val(quotation.project_type || '');
    $('#projectLocation').val(quotation.location || '');
    $('#projectDescription').val(quotation.description || '');
    
    // Section 3: Scope of Work
    $('#scopeDescription').val(quotation.description || '');
    $('#quotationReference').val(`Quotation #${quotation.quotation_id}`);
    
    // Section 5: Contract Value
    const contractValue = quotation.price ? parseFloat(quotation.price) : 0;
    $('#contractValue').val(contractValue.toFixed(2));
    
    // Update cost breakdown
    updateCostBreakdown();
    
    // Set default dates
    const today = new Date();
    $('#startDate').val(today.toISOString().split('T')[0]);
    
    // Set completion date based on duration (default 30 days if not specified)
    const durationDays = quotation.estimated_duration || 30;
    const completionDate = new Date(today);
    completionDate.setDate(completionDate.getDate() + parseInt(durationDays));
    $('#completionDate').val(completionDate.toISOString().split('T')[0]);
    
    console.log('Form auto-filled successfully');
}

// ============================================================================
// MILESTONE TABLE GENERATION
// ============================================================================

function generateMilestoneTable(paymentMethod) {
    const contractValue = parseFloat($('#contractValue').val()) || 0;
    const startDate = $('#startDate').val();
    const completionDate = $('#completionDate').val();
    
    if (contractValue === 0) {
        showNotification('Please ensure contract value is set', 'warning');
        return;
    }
    
    let milestones = [];
    
    switch(paymentMethod) {
        case 'full_upfront':
            milestones = [
                { name: 'Full Payment', description: 'Complete payment before work starts', percentage: 100, daysOffset: 0 }
            ];
            break;
            
        case '50_50':
            milestones = [
                { name: 'Advance Payment', description: '50% payment before work starts', percentage: 50, daysOffset: 0 },
                { name: 'Final Payment', description: '50% payment on completion', percentage: 50, daysOffset: null }
            ];
            break;
            
        case '30_70':
            milestones = [
                { name: 'Advance Payment', description: '30% payment before work starts', percentage: 30, daysOffset: 0 },
                { name: 'Final Payment', description: '70% payment on completion', percentage: 70, daysOffset: null }
            ];
            break;
            
        case 'milestone_based':
            const projectDuration = calculateDaysBetween(startDate, completionDate);
            const interval = Math.floor(projectDuration / 4);
            
            milestones = [
                { name: 'Advance Payment', description: 'Initial payment', percentage: 20, daysOffset: 0 },
                { name: 'Phase 1 Complete', description: '25% project completion', percentage: 20, daysOffset: interval },
                { name: 'Phase 2 Complete', description: '50% project completion', percentage: 20, daysOffset: interval * 2 },
                { name: 'Phase 3 Complete', description: '75% project completion', percentage: 20, daysOffset: interval * 3 },
                { name: 'Final Payment', description: 'Project completion', percentage: 20, daysOffset: null }
            ];
            break;
            
        case 'completion':
            milestones = [
                { name: 'Full Payment', description: 'Complete payment on project completion', percentage: 100, daysOffset: null }
            ];
            break;
    }
    
    // Generate table rows
    let tableHTML = '';
    let totalPercentage = 0;
    let totalAmount = 0;
    
    milestones.forEach((milestone, index) => {
        const amount = (contractValue * milestone.percentage / 100).toFixed(2);
        totalPercentage += milestone.percentage;
        totalAmount += parseFloat(amount);
        
        // Calculate due date
        let dueDate = '';
        if (milestone.daysOffset === 0) {
            dueDate = startDate;
        } else if (milestone.daysOffset === null) {
            dueDate = completionDate;
        } else {
            const date = new Date(startDate);
            date.setDate(date.getDate() + milestone.daysOffset);
            dueDate = date.toISOString().split('T')[0];
        }
        
        tableHTML += `
            <tr>
                <td><input type="text" name="milestone_name[]" value="${milestone.name}" required></td>
                <td><input type="text" name="milestone_description[]" value="${milestone.description}" required></td>
                <td><input type="number" name="milestone_percentage[]" value="${milestone.percentage}" min="0" max="100" step="0.01" class="milestone-percentage" required></td>
                <td><input type="number" name="milestone_amount[]" value="${amount}" min="0" step="0.01" class="milestone-amount" readonly></td>
                <td><input type="date" name="milestone_date[]" value="${dueDate}" required></td>
            </tr>
        `;
    });
    
    $('#milestoneTableBody').html(tableHTML);
    $('#totalPercentage').text(totalPercentage.toFixed(1));
    $('#totalAmount').text(totalAmount.toFixed(2));
    
    // Add event listener for percentage changes
    $('.milestone-percentage').on('input', function() {
        updateMilestoneAmounts();
    });
}

function updateMilestoneAmounts() {
    const contractValue = parseFloat($('#contractValue').val()) || 0;
    let totalPercentage = 0;
    let totalAmount = 0;
    
    $('.milestone-percentage').each(function() {
        const percentage = parseFloat($(this).val()) || 0;
        const amount = (contractValue * percentage / 100).toFixed(2);
        
        $(this).closest('tr').find('.milestone-amount').val(amount);
        
        totalPercentage += percentage;
        totalAmount += parseFloat(amount);
    });
    
    $('#totalPercentage').text(totalPercentage.toFixed(1));
    $('#totalAmount').text(totalAmount.toFixed(2));
    
    // Validate total percentage
    if (totalPercentage !== 100) {
        $('#totalPercentage').css('color', '#e53e3e');
    } else {
        $('#totalPercentage').css('color', '#38a169');
    }
}

function calculateDaysBetween(date1, date2) {
    const d1 = new Date(date1);
    const d2 = new Date(date2);
    const diffTime = Math.abs(d2 - d1);
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
}

// ============================================================================
// COST BREAKDOWN
// ============================================================================

function updateCostBreakdown() {
    const contractValue = parseFloat($('#contractValue').val()) || 0;
    
    // Simple breakdown (can be customized based on actual data)
    const materials = contractValue * 0.45;
    const labor = contractValue * 0.35;
    const equipment = contractValue * 0.10;
    const other = contractValue * 0.05;
    const tax = contractValue * 0.05;
    
    $('#materialsCost').text(materials.toFixed(2));
    $('#laborCost').text(labor.toFixed(2));
    $('#equipmentCost').text(equipment.toFixed(2));
    $('#otherCosts').text(other.toFixed(2));
    $('#taxAmount').text(tax.toFixed(2));
    $('#totalValue').text(contractValue.toFixed(2));
}

// ============================================================================
// TEMPLATE INSERTION
// ============================================================================

function insertTemplate(templateName, fieldId) {
    if (legalTemplates[templateName]) {
        $(`#${fieldId}`).val(legalTemplates[templateName]);
        showNotification('Template inserted successfully', 'success');
    }
}

// ============================================================================
// STEP NAVIGATION
// ============================================================================

function nextStep() {
    if (!validateCurrentStep()) {
        return;
    }
    
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
        updateProgressBar();
        
        // Generate preview on step 4
        if (currentStep === 4) {
            generatePreview();
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function prevStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateProgressBar();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function showStep(step) {
    $('.form-step').removeClass('active');
    $(`.form-step[data-step="${step}"]`).addClass('active');
    
    $('.step').removeClass('active completed');
    
    for (let i = 1; i <= totalSteps; i++) {
        if (i < step) {
            $(`.step[data-step="${i}"]`).addClass('completed');
        } else if (i === step) {
            $(`.step[data-step="${i}"]`).addClass('active');
        }
    }
}

function updateProgressBar() {
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    $('#progressLine').css('width', progress + '%');
}

// ============================================================================
// VALIDATION
// ============================================================================

function validateCurrentStep() {
    let isValid = true;
    const currentStepElement = $(`.form-step[data-step="${currentStep}"]`);
    
    // Check required fields in current step
    currentStepElement.find('input[required], select[required], textarea[required]').each(function() {
        if (!$(this).val() || $(this).val().trim() === '') {
            isValid = false;
            $(this).css('border-color', '#e53e3e');
            
            // Add error message if not exists
            if (!$(this).next('.error-message').length) {
                $(this).after('<span class="error-message" style="color: #e53e3e; font-size: 12px; margin-top: 4px; display: block;">This field is required</span>');
            }
        } else {
            $(this).css('border-color', '#e2e8f0');
            $(this).next('.error-message').remove();
        }
    });
    
    // Step-specific validation
    if (currentStep === 1) {
        if (!$('#quotationSelect').val()) {
            showNotification('Please select a quotation', 'error');
            isValid = false;
        }
    }
    
    if (currentStep === 3) {
        // Validate milestone table if visible
        if ($('#milestoneTableContainer').is(':visible')) {
            const totalPercentage = parseFloat($('#totalPercentage').text()) || 0;
            if (Math.abs(totalPercentage - 100) > 0.1) {
                showNotification('Milestone percentages must total 100%', 'error');
                isValid = false;
            }
        }
        
        // Validate dates
        const startDate = new Date($('#startDate').val());
        const completionDate = new Date($('#completionDate').val());
        if (completionDate <= startDate) {
            showNotification('Completion date must be after start date', 'error');
            isValid = false;
        }
    }
    
    if (!isValid) {
        showNotification('Please fill in all required fields correctly', 'error');
    }
    
    return isValid;
}

// ============================================================================
// PREVIEW GENERATION
// ============================================================================

function generatePreview() {
    let previewHTML = '';
    
    // Section 1: Parties
    previewHTML += `
        <div class="preview-section">
            <h3>1. Parties to the Contract</h3>
            <div class="preview-field"><strong>Contractor (Company):</strong> <span>${$('#companyName').val()}</span></div>
            <div class="preview-field"><strong>Business Registration:</strong> <span>${$('#businessRegistration').val()}</span></div>
            <div class="preview-field"><strong>Company Address:</strong> <span>${$('#companyAddress').val()}</span></div>
            <div class="preview-field"><strong>Company Contact:</strong> <span>${$('#companyContact').val()}</span></div>
            <div class="preview-field"><strong>Company Email:</strong> <span>${$('#companyEmail').val()}</span></div>
            <div class="preview-field"><strong>Authorized Representative:</strong> <span>${$('#companyRepresentative').val()}</span></div>
            <br>
            <div class="preview-field"><strong>Client Name:</strong> <span>${$('#clientName').val()}</span></div>
            <div class="preview-field"><strong>Client NIC:</strong> <span>${$('#clientNIC').val() || 'N/A'}</span></div>
            <div class="preview-field"><strong>Client Address:</strong> <span>${$('#clientAddress').val()}</span></div>
            <div class="preview-field"><strong>Client Contact:</strong> <span>${$('#clientContact').val()}</span></div>
            <div class="preview-field"><strong>Client Email:</strong> <span>${$('#clientEmail').val()}</span></div>
        </div>
    `;
    
    // Section 2: Project Identification
    previewHTML += `
        <div class="preview-section">
            <h3>2. Project Identification & Overview</h3>
            <div class="preview-field"><strong>Project Title:</strong> <span>${$('#projectTitle').val()}</span></div>
            <div class="preview-field"><strong>Reference ID:</strong> <span>${$('#projectReferenceID').val()}</span></div>
            <div class="preview-field"><strong>Project Type:</strong> <span>${$('#projectType').val()}</span></div>
            <div class="preview-field"><strong>Location:</strong> <span>${$('#projectLocation').val()}</span></div>
            <div class="preview-field"><strong>Description:</strong> <span>${$('#projectDescription').val()}</span></div>
            <div class="preview-field"><strong>Purpose:</strong> <span>${$('#projectPurpose').val()}</span></div>
        </div>
    `;
    
    // Section 3: Scope of Work
    previewHTML += `
        <div class="preview-section">
            <h3>3. Scope of Work</h3>
            <div class="preview-field"><strong>Description:</strong><br><span>${$('#scopeDescription').val().replace(/\n/g, '<br>')}</span></div>
            <div class="preview-field"><strong>Inclusions:</strong><br><span>${$('#scopeInclusions').val().replace(/\n/g, '<br>')}</span></div>
            <div class="preview-field"><strong>Exclusions:</strong><br><span>${$('#scopeExclusions').val().replace(/\n/g, '<br>') || 'None specified'}</span></div>
            <div class="preview-field"><strong>Standards:</strong><br><span>${$('#scopeStandards').val() || 'Standard industry practices'}</span></div>
            <div class="preview-field"><strong>Materials Responsibility:</strong> <span>${$('#materialsResponsibility').val()}</span></div>
        </div>
    `;
    
    // Section 4: Timeline
    previewHTML += `
        <div class="preview-section">
            <h3>4. Contract Duration & Timeline</h3>
            <div class="preview-field"><strong>Start Date:</strong> <span>${formatDate($('#startDate').val())}</span></div>
            <div class="preview-field"><strong>Completion Date:</strong> <span>${formatDate($('#completionDate').val())}</span></div>
            <div class="preview-field"><strong>Working Days:</strong> <span>${$('#workingDays').val()} days per week</span></div>
            <div class="preview-field"><strong>Working Hours:</strong> <span>${$('#workingHours').val() || 'Standard hours'}</span></div>
            <div class="preview-field"><strong>Milestones:</strong><br><span>${$('#projectMilestones').val().replace(/\n/g, '<br>') || 'See payment schedule'}</span></div>
        </div>
    `;
    
    // Section 5: Pricing
    previewHTML += `
        <div class="preview-section">
            <h3>5. Contract Price & Value</h3>
            <div class="preview-field"><strong>Total Contract Amount:</strong> <span>Rs. ${$('#contractValue').val()}</span></div>
            <div class="preview-field"><strong>Currency:</strong> <span>${$('#currency').val()}</span></div>
            <div class="preview-field"><strong>Pricing Model:</strong> <span>${$('#pricingModel').val()}</span></div>
            <div class="preview-field"><strong>Tax Inclusion:</strong> <span>${$('#taxInclusion').val()}</span></div>
        </div>
    `;
    
    // Section 6: Payment
    previewHTML += `
        <div class="preview-section">
            <h3>6. Payment Terms & Schedule</h3>
            <div class="preview-field"><strong>Payment Method:</strong> <span>${$('#paymentMethod option:selected').text()}</span></div>
            <div class="preview-field"><strong>Payment Due:</strong> <span>${$('#paymentDueDays').val()} days after invoice</span></div>
            <div class="preview-field"><strong>Transfer Method:</strong> <span>${$('#bankTransferMethod').val()}</span></div>
    `;
    
    // Add milestone table if exists
    if ($('#milestoneTableContainer').is(':visible')) {
        previewHTML += '<div class="preview-field"><strong>Payment Schedule:</strong></div>';
        previewHTML += '<table class="milestone-table" style="margin-top: 10px; font-size: 13px;">';
        previewHTML += '<thead><tr><th>Milestone</th><th>%</th><th>Amount</th><th>Due Date</th></tr></thead><tbody>';
        
        $('#milestoneTableBody tr').each(function() {
            const name = $(this).find('input[name="milestone_name[]"]').val();
            const percentage = $(this).find('input[name="milestone_percentage[]"]').val();
            const amount = $(this).find('input[name="milestone_amount[]"]').val();
            const date = $(this).find('input[name="milestone_date[]"]').val();
            
            previewHTML += `<tr>
                <td>${name}</td>
                <td>${percentage}%</td>
                <td>Rs. ${amount}</td>
                <td>${formatDate(date)}</td>
            </tr>`;
        });
        
        previewHTML += '</tbody></table>';
    }
    
    previewHTML += '</div>';
    
    // Sections 7-13: Legal Terms (abbreviated in preview)
    previewHTML += `
        <div class="preview-section">
            <h3>7-13. Legal Terms & Conditions</h3>
            <div class="preview-field">• Variations & Changes: ${$('#variationsClause').val().substring(0, 100)}...</div>
            <div class="preview-field">• Communication & Negotiation: Standard platform communication</div>
            <div class="preview-field">• Project Delays & Responsibilities: Defined accountability</div>
            <div class="preview-field">• Termination: Conditions and procedures defined</div>
            <div class="preview-field">• Force Majeure: Protection for uncontrollable events</div>
            <div class="preview-field">• Liability Limitation: Maximum liability capped at contract value</div>
            <div class="preview-field">• Governing Law: Laws of Sri Lanka</div>
            <div class="preview-field"><strong>Contract Date:</strong> <span>${formatDate($('#contractDate').val())}</span></div>
            <div class="preview-field"><strong>Acceptance Method:</strong> <span>${$('#acceptanceMethod').val()}</span></div>
        </div>
    `;
    
    $('#previewContent').html(previewHTML);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

// ============================================================================
// FORM SUBMISSION
// ============================================================================

function submitContract() {
    if (!validateCurrentStep()) {
        return;
    }
    
    // Disable submit button
    const submitBtn = $('#submitBtn');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
    
    // Collect all form data
    const formData = {
        quotation_id: $('#quotationSelect').val(),
        
        // Section 1: Parties
        company_name: $('#companyName').val(),
        business_registration: $('#businessRegistration').val(),
        company_address: $('#companyAddress').val(),
        company_contact: $('#companyContact').val(),
        company_email: $('#companyEmail').val(),
        company_representative: $('#companyRepresentative').val(),
        client_name: $('#clientName').val(),
        client_nic: $('#clientNIC').val(),
        client_address: $('#clientAddress').val(),
        client_contact: $('#clientContact').val(),
        client_email: $('#clientEmail').val(),
        
        // Section 2: Project
        project_title: $('#projectTitle').val(),
        project_reference_id: $('#projectReferenceID').val(),
        project_type: $('#projectType').val(),
        project_location: $('#projectLocation').val(),
        project_description: $('#projectDescription').val(),
        project_purpose: $('#projectPurpose').val(),
        
        // Section 3: Scope
        scope_description: $('#scopeDescription').val(),
        scope_inclusions: $('#scopeInclusions').val(),
        scope_exclusions: $('#scopeExclusions').val(),
        scope_standards: $('#scopeStandards').val(),
        materials_responsibility: $('#materialsResponsibility').val(),
        quotation_reference: $('#quotationReference').val(),
        
        // Section 4: Timeline
        start_date: $('#startDate').val(),
        completion_date: $('#completionDate').val(),
        working_days: $('#workingDays').val(),
        working_hours: $('#workingHours').val(),
        project_milestones: $('#projectMilestones').val(),
        
        // Section 5: Pricing
        contract_value: $('#contractValue').val(),
        currency: $('#currency').val(),
        pricing_model: $('#pricingModel').val(),
        tax_inclusion: $('#taxInclusion').val(),
        
        // Section 6: Payment
        payment_method: $('#paymentMethod').val(),
        payment_due_days: $('#paymentDueDays').val(),
        bank_transfer_method: $('#bankTransferMethod').val(),
        
        // Milestones
        milestones: collectMilestoneData(),
        
        // Sections 7-13: Legal Terms
        variations_clause: $('#variationsClause').val(),
        communication_clause: $('#communicationClause').val(),
        delays_clause: $('#delaysClause').val(),
        termination_clause: $('#terminationClause').val(),
        force_majeure_clause: $('#forceMajeureClause').val(),
        liability_clause: $('#liabilityClause').val(),
        governing_law_clause: $('#governingLawClause').val(),
        acceptance_method: $('#acceptanceMethod').val(),
        contract_date: $('#contractDate').val()
    };
    
    console.log('Submitting contract data:', formData);
    
    // Submit to server
    $.ajax({
        url: '../../api/contracts.php',
        method: 'POST',
        data: {
            action: 'createContract',
            contractData: JSON.stringify(formData)
        },
        dataType: 'json',
        success: function(response) {
            console.log('Contract submission response:', response);
            
            if (response.success) {
                showNotification('Contract created successfully!', 'success');
                setTimeout(function() {
                    window.location.href = 'contracts.php';
                }, 2000);
            } else {
                showNotification(response.message || 'Failed to create contract', 'error');
                submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Contract');
            }
        },
        error: function(xhr, status, error) {
            console.error('Submission error:', error);
            showNotification('Error submitting contract. Please try again.', 'error');
            submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Submit Contract');
        }
    });
}

function collectMilestoneData() {
    const milestones = [];
    
    $('#milestoneTableBody tr').each(function() {
        milestones.push({
            name: $(this).find('input[name="milestone_name[]"]').val(),
            description: $(this).find('input[name="milestone_description[]"]').val(),
            percentage: $(this).find('input[name="milestone_percentage[]"]').val(),
            amount: $(this).find('input[name="milestone_amount[]"]').val(),
            due_date: $(this).find('input[name="milestone_date[]"]').val()
        });
    });
    
    return milestones;
}

// ============================================================================
// NOTIFICATIONS
// ============================================================================

function showNotification(message, type = 'info') {
    // Remove existing notifications
    $('.notification').remove();
    
    const colors = {
        success: '#48bb78',
        error: '#e53e3e',
        warning: '#ed8936',
        info: '#667eea'
    };
    
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    
    const notification = $(`
        <div class="notification" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            color: ${colors[type]};
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            z-index: 10000;
            border-left: 4px solid ${colors[type]};
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 400px;
            animation: slideIn 0.3s ease;
        ">
            <i class="fas fa-${icons[type]}" style="font-size: 20px;"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

// Add CSS animation
$('head').append(`
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
`);

// ============================================================================
// GLOBAL FUNCTION EXPOSURE (for onclick handlers)
// ============================================================================

window.nextStep = nextStep;
window.prevStep = prevStep;
window.insertTemplate = insertTemplate;

console.log('Contract creation system loaded successfully');
