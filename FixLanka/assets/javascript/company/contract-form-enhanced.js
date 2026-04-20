// ===================================
// ENHANCED CONTRACT CREATION FORM
// 8-Section Legal Contract Form Logic
// ===================================

(function () {
    'use strict';

    const TOTAL_STEPS = 8;
    const API_BASE = '/2nd-Year-Group-Project/FixLanka/api/contracts';
    const QUOTATION_API = '/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=getAcceptedQuotations';
    const ENHANCED_API = `${API_BASE}/create_enhanced.php`;

    let currentStep = 1;
    let selectedQuotation = null;
    let quotationFullData = null; // Full data from enhanced API
    let unitPricingMode = { active: false, laborUnitLabel: '', materialUnitLabel: '' };
    let autoSaveTimer = null;
    let formDirty = false;

    async function readJsonOrNull(resp) {
        const text = await resp.text();
        if (!text) return null;
        try {
            return JSON.parse(text);
        } catch (e) {
            return null;
        }
    }

    // ===================================
    // INITIALIZATION
    // ===================================
    document.addEventListener('DOMContentLoaded', function () {
        initEnhancedContractForm();
    });

    function initEnhancedContractForm() {
        // -------------------------------------------------------
        // CRITICAL: Remove OLD event listeners from contracts-enhanced.js
        // by cloning elements (removes all listeners) and re-attaching fresh ones.
        // This prevents the old 4-step nav from conflicting with our 8-step nav.
        // -------------------------------------------------------
        const buttonIds = ['formNextBtn', 'formPrevBtn', 'formCancelBtn', 'formSubmitBtn', 'newContractClose', 'newContractBtn'];
        buttonIds.forEach(id => {
            const oldEl = document.getElementById(id);
            if (oldEl) {
                const newEl = oldEl.cloneNode(true);
                oldEl.parentNode.replaceChild(newEl, oldEl);
            }
        });

        // Navigation buttons (now clean, no old listeners)
        const nextBtn = document.getElementById('formNextBtn');
        const prevBtn = document.getElementById('formPrevBtn');
        const cancelBtn = document.getElementById('formCancelBtn');
        const submitBtn = document.getElementById('formSubmitBtn');
        const saveDraftBtn = document.getElementById('formSaveDraftBtn');
        const closeBtn = document.getElementById('newContractClose');

        if (nextBtn) nextBtn.addEventListener('click', goNext);
        if (prevBtn) prevBtn.addEventListener('click', goPrev);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (submitBtn) submitBtn.addEventListener('click', submitContract);
        if (saveDraftBtn) saveDraftBtn.addEventListener('click', saveDraft);

        // Prevent default form submission (e.g. Enter key)
        const form = document.getElementById('contractForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                return false;
            });
        }

        // Quotation selector
        const selector = document.getElementById('quotationSelector');
        if (selector) selector.addEventListener('change', onQuotationSelected);

        // Budget type change
        const budgetType = document.getElementById('budgetType');
        if (budgetType) budgetType.addEventListener('change', onBudgetTypeChange);
        // Flexible percent change (materials-based)
        const budgetFlexPercent = document.getElementById('budgetFlexPercent');
        if (budgetFlexPercent) {
            budgetFlexPercent.addEventListener('input', onBudgetTypeChange);
            budgetFlexPercent.addEventListener('change', onBudgetTypeChange);
        }

        // Pricing type change
        const pricingType = document.getElementById('pricingType');
        if (pricingType) pricingType.addEventListener('change', onPricingTypeChange);

        // Contract value change
        const contractValue = document.getElementById('contractValue');
        if (contractValue) contractValue.addEventListener('input', onContractValueChange);

        // Payment method change
        const paymentMethod = document.getElementById('paymentMethod');
        if (paymentMethod) paymentMethod.addEventListener('change', onPaymentMethodChange);

        // Date change listeners
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        if (startDate) startDate.addEventListener('change', onDateChange);
        if (endDate) endDate.addEventListener('change', onDateChange);

        // Apply bounds immediately for any prefilled values
        onDateChange();

        // Milestone buttons
        const addMsBtn = document.getElementById('addMilestoneBtn');
        if (addMsBtn) addMsBtn.addEventListener('click', addMilestoneRow);

        // Sidebar "Edit milestones" link in Payment step


        // Delegation for remove milestone buttons and milestone field changes
        document.addEventListener('click', async function (e) {
            const btn = e.target.closest('.btn-remove-ms');
            if (btn && document.getElementById('milestonesBody')?.contains(btn)) {
                // In unit-priced mode: protect the required unit-billing rows (Labour/Materials)
                if (unitPricingMode.active) {
                    const reqCount = ((unitPricingMode.laborUnitLabel || '').trim() ? 1 : 0) + ((unitPricingMode.materialUnitLabel || '').trim() ? 1 : 0);
                    const row = btn.closest('tr');
                    const rowsArr = Array.from(document.querySelectorAll('#milestonesBody .milestone-row'));
                    const idx = rowsArr.indexOf(row);
                    if (reqCount > 0 && idx > -1 && idx < reqCount) {
                        showNotification('error', 'Action Denied', 'The Labour/Materials unit-billing phases cannot be removed in unit-priced mode.');
                        return;
                    }
                }

                // Feature: Prevent deletion of last remaining phase
                const rows = document.querySelectorAll('#milestonesBody .milestone-row');
                if (rows.length <= 1) {
                    showNotification('error', 'Action Denied', 'At least one phase is required. You cannot delete the last remaining phase.');
                    return;
                }

                // Feature: Two-step confirmation
                const confirmed = window.systemConfirm
                    ? await window.systemConfirm('Are you sure you want to delete this phase? This action cannot be undone.', {
                        title: 'Delete Phase',
                        confirmText: 'Delete',
                        cancelText: 'Cancel',
                        type: 'danger',
                        icon: 'fas fa-trash'
                    })
                    : confirm('Are you sure you want to delete this phase? This action cannot be undone.');

                if (confirmed) {
                    btn.closest('tr').remove();
                    if (typeof renumberMilestones === 'function') renumberMilestones();
                    if (typeof generatePaymentPreview === 'function') generatePaymentPreview();
                }
            }
        });

        document.addEventListener('input', function (e) {
            const msBody = document.getElementById('milestonesBody');
            if (!msBody || !msBody.contains(e.target)) return;

            if (e.target.tagName === 'INPUT') {
                if (typeof generatePaymentPreview === 'function') generatePaymentPreview();
            }
        });

        // "Edit milestones" link in payment section (Step 5) → validate & go to Step 6 (Milestones)
        const goToMsLink = document.getElementById('goToMilestonesLink');
        if (goToMsLink) {
            goToMsLink.addEventListener('click', function (e) {
                e.preventDefault();
                goNext();
            });
        }

        // Variation clause toggle
        const varClause = document.getElementById('variationClause');
        if (varClause) {
            varClause.addEventListener('change', function () {
                const body = document.getElementById('variationClauseBody');
                if (body) body.style.display = this.checked ? 'block' : 'none';
            });
        }

        // New Contract modal open handler (freshly cloned, no old listener)
        const newContractBtn = document.getElementById('newContractBtn');
        if (newContractBtn) {
            newContractBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                openModal();
            });
        }

        // Auto-save on any input change
        const contractForm = document.getElementById('contractForm');
        if (contractForm) {
            contractForm.addEventListener('input', function () {
                formDirty = true;
                debouncedAutoSave();
            });
            contractForm.addEventListener('change', function () {
                formDirty = true;
            });
        }
    }

    // ===================================
    // MODAL MANAGEMENT
    // ===================================
    function openModal() {
        const modal = document.getElementById('newContractModal');
        if (modal) {
            modal.classList.add('active');
            document.body.classList.add('enhanced-form-active'); // Signal to legacy scripts
            window.enhancedFormActive = true;
            resetForm();
            loadQuotations();
        }
    }

    async function closeModal() {
        if (formDirty) {
            const confirmed = window.systemConfirm
                ? await window.systemConfirm('You have unsaved changes. Are you sure you want to close?', {
                    title: 'Discard Changes?',
                    confirmText: 'Close',
                    cancelText: 'Keep Editing',
                    type: 'warning',
                    icon: 'fas fa-exclamation-triangle'
                })
                : confirm('You have unsaved changes. Are you sure you want to close?');

            if (!confirmed) return;
        }
        const modal = document.getElementById('newContractModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.classList.remove('enhanced-form-active');
            window.enhancedFormActive = false;
        }
        resetForm();
    }

    function resetForm() {
        currentStep = 1;
        selectedQuotation = null;
        quotationFullData = null;
        unitPricingMode = { active: false, laborUnitLabel: '', materialUnitLabel: '' };
        formDirty = false;

        const form = document.getElementById('contractForm');
        if (form) form.reset();

        // Reset step indicators
        updateStepIndicators();
        showStep(1);

        // Reset previews
        const preview = document.getElementById('quotationPreviewCard');
        if (preview) preview.style.display = 'none';

        const precondErr = document.getElementById('preconditionError');
        if (precondErr) precondErr.style.display = 'none';

        // Reset default milestone rows
        resetMilestones();

        // Unlock any unit-pricing restrictions
        applyUnitPricingLocks();
    }

    // ===================================
    // STEP NAVIGATION
    // ===================================
    function showStep(step) {
        // Hide all
        document.querySelectorAll('.form-step-content').forEach(el => el.classList.remove('active'));
        // Show target
        const target = document.querySelector(`.form-step-content[data-step="${step}"]`);
        if (target) target.classList.add('active');

        // Update buttons
        const prevBtn = document.getElementById('formPrevBtn');
        const nextBtn = document.getElementById('formNextBtn');
        const submitBtn = document.getElementById('formSubmitBtn');

        const isEditMode = !!document.getElementById('editContractId')?.value;
        const firstStep = isEditMode ? 2 : 1;

        if (prevBtn) prevBtn.style.display = step > firstStep ? 'inline-flex' : 'none';
        if (nextBtn) nextBtn.style.display = step < TOTAL_STEPS ? 'inline-flex' : 'none';
        if (submitBtn) submitBtn.style.display = step === TOTAL_STEPS ? 'inline-flex' : 'none';

        // Scroll modal content to top
        const modalContent = document.querySelector('#newContractModal .modal-content');
        if (modalContent) modalContent.scrollTop = 0;

        updateStepIndicators();
    }

    function updateStepIndicators() {
        document.querySelectorAll('.progress-step').forEach(el => {
            const step = parseInt(el.dataset.step);
            el.classList.remove('active', 'completed');
            if (step === currentStep) el.classList.add('active');
            else if (step < currentStep) el.classList.add('completed');
        });
        // Update progress bar fill
        const fill = document.getElementById('formProgressFill');
        if (fill) {
            const pct = ((currentStep - 1) / (TOTAL_STEPS - 1)) * 100;
            fill.style.width = pct + '%';
        }
    }

    function goNext() {
        if (!validateCurrentStep()) return;

        if (currentStep < TOTAL_STEPS) {
            currentStep++;
            showStep(currentStep);

            // Populate review on last step
            if (currentStep === TOTAL_STEPS) {
                populateReview();
            }
        }
    }

    function goPrev() {
        const isEditMode = document.getElementById('editContractId')?.value;
        const minStep = isEditMode ? 2 : 1;
        if (currentStep > minStep) {
            currentStep--;
            showStep(currentStep);
        }
    }

    // ===================================
    // VALIDATION
    // ===================================
    function validateCurrentStep() {
        const step = document.querySelector(`.form-step-content[data-step="${currentStep}"]`);
        if (!step) return true;

        // Clear previous errors
        step.querySelectorAll('.field-error').forEach(el => el.remove());
        step.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

        let valid = true;

        switch (currentStep) {
            case 1: // Quotation or Contract ID validation
                const editContractId = document.getElementById('editContractId')?.value;
                if (editContractId) {
                    // EDIT MODE: Validate contract ID is present (quotation already linked)
                    // No further Step 1 validation needed - contract is identified by ID
                } else {
                    // NEW CONTRACT MODE: Must select a quotation
                    const qSel = document.getElementById('quotationSelector');
                    if (!qSel || !qSel.value) {
                        showFieldError(qSel, 'Please select an accepted quotation');
                        valid = false;
                    }
                }
                break;

            case 3: // Project overview
                valid = validateRequired(step, ['projectTitle', 'projectLocation', 'projectDescription']);
                break;

            case 4: // Scope
                valid = validateRequired(step, ['scopeDescription']);
                break;

            case 5: // Payments (Swapped from 6)
                // Total budget is pulled from the quotation and stored in a hidden field.
                // Validate only the user-selected payment configuration.
                valid = validateRequired(step, ['budgetType', 'paymentMethod']);
                break;

            case 6: // Timeline & Milestones (Swapped from 5)
                valid = validateRequired(step, ['startDate', 'endDate']);
                if (valid) {
                    const start = new Date(document.getElementById('startDate').value);
                    const end = new Date(document.getElementById('endDate').value);
                    if (end <= start) {
                        showFieldError(document.getElementById('endDate'), 'End date must be after start date');
                        valid = false;
                    }

                    if (valid) {
                        const rows = document.querySelectorAll('#milestonesBody .milestone-row');
                        rows.forEach((row, index) => {
                            const name = row.querySelector('input[name="ms_name[]"]');
                            const desc = row.querySelector('input[name="ms_desc[]"]');
                            const date = row.querySelector('input[name="ms_date[]"]');

                            if (!name?.value?.trim()) {
                                showFieldError(name, `Phase ${index + 1}: name is required`);
                                valid = false;
                            }
                            if (!desc?.value?.trim()) {
                                showFieldError(desc, `Phase ${index + 1}: description is required`);
                                valid = false;
                            }
                            if (!date?.value) {
                                showFieldError(date, `Phase ${index + 1}: target date is required`);
                                valid = false;
                            }
                        });
                    }
                }
                break;
        }

        return valid;
    }

    function validateRequired(container, fieldIds) {
        let valid = true;
        fieldIds.forEach(id => {
            const field = document.getElementById(id);
            if (field && (!field.value || field.value.trim() === '')) {
                showFieldError(field, 'This field is required');
                valid = false;
            }
        });
        return valid;
    }

    function showFieldError(field, message) {
        if (!field) return;
        field.classList.add('input-error');
        const err = document.createElement('div');
        err.className = 'field-error';
        err.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        field.parentNode.appendChild(err);
    }

    // ===================================
    // QUOTATION LOADING
    // ===================================
    async function loadQuotations() {
        const selector = document.getElementById('quotationSelector');
        const badge = document.getElementById('quotationCountBadge');

        if (!selector) return;

        try {
            const response = await fetch(QUOTATION_API);
            if (!response.ok) throw new Error('Failed to load quotations');

            const result = await response.json();

            // Clear and populate
            selector.innerHTML = '<option value="">-- Select an Accepted Quotation --</option>';

            if (result.success && result.data && result.data.length > 0) {
                result.data.forEach(q => {
                    const opt = document.createElement('option');
                    opt.value = q.quotation_id;
                    opt.textContent = `${q.title} (${q.customer_fname} ${q.customer_lname})`;
                    opt.dataset.quotation = JSON.stringify(q);
                    selector.appendChild(opt);
                });
                if (badge) {
                    badge.textContent = `${result.data.length} available`;
                    badge.style.background = '#4caf50';
                }
            } else {
                if (badge) {
                    badge.textContent = 'None available';
                    badge.style.background = '#999';
                }
            }
        } catch (error) {
            console.error('Error loading quotations:', error);
            if (badge) {
                badge.textContent = 'Error';
                badge.style.background = '#f44336';
            }
        }
    }

    // ===================================
    // QUOTATION SELECTION & AUTO-FILL
    // ===================================
    async function onQuotationSelected(event) {
        const selected = event.target.options[event.target.selectedIndex];
        const preview = document.getElementById('quotationPreviewCard');

        if (!selected.value) {
            selectedQuotation = null;
            if (preview) preview.style.display = 'none';

            const bd = document.getElementById('costBreakdown');
            if (bd) bd.style.display = 'none';
            const unitInfo = document.getElementById('unitMeasurementInfo');
            if (unitInfo) unitInfo.style.display = 'none';

            unitPricingMode = { active: false, laborUnitLabel: '', materialUnitLabel: '' };
            applyUnitPricingLocks();
            return;
        }

        try {
            selectedQuotation = JSON.parse(selected.dataset.quotation);
        } catch (e) {
            return;
        }

        // Show preview card
        if (preview) {
            preview.innerHTML = `
                <div class="qs-preview-header">
                    <i class="fas fa-check-circle"></i>
                    <strong>${esc(selectedQuotation.title)}</strong>
                </div>
                <div class="qs-preview-grid">
                    <div><span>Customer</span><strong>${esc(selectedQuotation.customer_fname)} ${esc(selectedQuotation.customer_lname)}</strong></div>
                    <div><span>Duration</span><strong>${selectedQuotation.estimated_duration || 'TBD'} days</strong></div>
                    <div><span>Payment</span><strong>${formatPaymentMethodLabel(selectedQuotation.payment_method)}</strong></div>
                </div>
                <div class="qs-preview-note">
                    <i class="fas fa-magic"></i> All 8 sections will be auto-filled. You can review and edit in subsequent steps.
                </div>
            `;
            preview.style.display = 'block';
        }

        // Load full data from enhanced API for complete auto-fill
        try {
            const qs = new URLSearchParams({
                action: 'getQuotationData',
                quotation_id: String(selectedQuotation.quotation_id)
            });
            const resp = await fetch(`${ENHANCED_API}?${qs.toString()}`);
            if (resp.ok) {
                const result = await readJsonOrNull(resp);
                if (result && result.success) {
                    quotationFullData = result.data;
                    updateUnitBasedLabels(result.data);
                    autoFillAllSections(result.data);
                    applyUnitBasedMilestoneMode(); // Hide/show payment columns based on pricing type
                } else {
                    // Fallback: use basic quotation data
                    autoFillFromBasicData(selectedQuotation);
                }
            } else {
                autoFillFromBasicData(selectedQuotation);
            }
        } catch (e) {
            console.warn('Enhanced API not available, using basic data', e);
            autoFillFromBasicData(selectedQuotation);
        }
    }

    function autoFillAllSections(d) {
        // Hidden IDs
        setVal('selectedQuotationId', d.quotation_id);
        setVal('selectedRequestId', d.request_id);
        setVal('customerId', d.customer_id);

        // Step 2: Parties (read-only display)
        setText('partyClientName', `${d.customer_fname} ${d.customer_lname}`);
        setText('partyClientAddress', d.customer_address || '-');
        setText('partyClientEmail', d.customer_email || '-');
        setText('partyClientDistrict', d.customer_district || '-');
        setVal('clientName', `${d.customer_fname} ${d.customer_lname}`);
        setVal('clientEmail', d.customer_email || '');

        setText('partyCompanyName', d.company_name || '-');
        setText('partyCompanyReg', d.company_registration || '-');
        setText('partyCompanyAddress', d.company_address || '-');
        setText('partyCompanyContact', `${d.company_phone || ''} / ${d.company_email || ''}`);

        // Step 3: Project Overview
        setVal('projectTitle', d.title || d.request_title || '');
        setVal('projectReference', d.project_reference || '');
        const locParts = [d.request_address, d.request_district].filter(Boolean);
        setVal('projectLocation', locParts.join(', '));
        setVal('projectType', d.request_category_name || d.project_type || '');
        setVal('projectDescription', d.description || d.request_description || '');

        // Step 4: Scope
        setVal('scopeDescription', d.description || d.request_description || '');

        // Step 6: Timeline & Milestones
        setVal('startDate', d.start_date || '');
        setVal('endDate', d.completion_date || '');
        onDateChange();

        // Update milestone dates
        setMilestoneDates(d.start_date, d.completion_date);

        // Step 5: Financial (Payments)
        setVal('contractValue', d.total_amount || '');
        setVal('budgetType', d.budget_type || 'fixed');
        setVal('budgetFlexPercent', (d.budget_type === 'flexible') ? '10' : '');
        onBudgetTypeChange();

        // Payment method: quotation table has this column but it's NEVER populated
        // by the quotation form. So d.payment_method is always NULL.
        // Try to detect from payment_terms free text, otherwise default to 50_50.
        let detectedMethod = '50_50'; // Safe default
        if (d.payment_method) {
            // If the quotation actually has a value (future-proof), use it
            detectedMethod = d.payment_method;
        } else if (d.payment_terms) {
            // Parse the free-text payment_terms for hints
            const pt = d.payment_terms.toLowerCase();
            if (pt.includes('milestone')) {
                detectedMethod = 'milestone_based';
            } else if (pt.includes('full') && pt.includes('upfront')) {
                detectedMethod = 'full_upfront';
            } else if (pt.includes('completion') && !pt.includes('advance') && !pt.includes('%')) {
                detectedMethod = 'completion';
            } else if (pt.includes('30%') || pt.includes('30 %') || pt.includes('30/70')) {
                detectedMethod = '30_70';
            }
            // else keep 50_50 default
        }
        setVal('paymentMethod', detectedMethod);

        setVal('pricingType', d.pricing_type || 'fixed_price');
        setVal('hourlyRate', d.hourly_rate || '');
        onPricingTypeChange();
        setVal('warrantyPeriod', d.warranty_period || '');
        setVal('paymentTermsText', d.payment_terms || '');
        setVal('additionalTerms', d.additional_terms || '');

        // Cost breakdown
        showCostBreakdown(d);

        // Sync milestone payment columns + payment preview
        onPaymentMethodChange();
        // Apply unit-based columns visibility after payment method has been set
        applyUnitBasedMilestoneMode();
    }

    function autoFillFromBasicData(q) {
        setVal('selectedQuotationId', q.quotation_id);
        setVal('selectedRequestId', q.request_id);
        setVal('customerId', q.customer_id);

        // If unit labels are included in the basic payload, reflect them.
        updateUnitBasedLabels(q);

        // Parties (basic)
        setText('partyClientName', `${q.customer_fname} ${q.customer_lname}`);
        setText('partyClientAddress', q.customer_address || '-');
        setText('partyClientEmail', q.customer_email || '-');
        setText('partyClientDistrict', q.customer_district || '-');
        setVal('clientName', `${q.customer_fname} ${q.customer_lname}`);
        setVal('clientEmail', q.customer_email || '');

        // Company Details
        setText('partyCompanyName', q.company_name || '-');
        setText('partyCompanyReg', q.company_registration || '-');
        setText('partyCompanyAddress', q.company_address || '-');
        setText('partyCompanyContact', `${q.company_contact || ''} / ${q.company_email || ''}`);


        // Project
        setVal('projectTitle', q.title || '');
        const basicLocParts = [q.request_address || q.location, q.request_district || q.district].filter(Boolean);
        setVal('projectLocation', basicLocParts.join(', '));
        setVal('projectType', q.request_category_name || q.project_type || q.category_name || '');
        setVal('projectDescription', q.description || '');
        setVal('scopeDescription', q.description || '');

        // Timeline
        setVal('startDate', q.start_date || '');
        setVal('endDate', q.completion_date || '');
        onDateChange();
        setMilestoneDates(q.start_date, q.completion_date);

        // Financial
        setVal('contractValue', q.total_amount || '');
        setVal('budgetType', q.budget_type || 'fixed');
        setVal('budgetFlexPercent', (q.budget_type === 'flexible') ? '10' : '');
        onBudgetTypeChange();

        // Payment method: NOT stored in quotation (always NULL)
        // Default to 50_50, let company choose in Step 5
        let detectedMethod = '50_50';
        if (q.payment_method) {
            detectedMethod = q.payment_method;
        } else if (q.payment_terms) {
            const pt = q.payment_terms.toLowerCase();
            if (pt.includes('milestone')) detectedMethod = 'milestone_based';
            else if (pt.includes('full') && pt.includes('upfront')) detectedMethod = 'full_upfront';
            else if (pt.includes('completion') && !pt.includes('advance')) detectedMethod = 'completion';
            else if (pt.includes('30%') || pt.includes('30/70')) detectedMethod = '30_70';
        }
        setVal('paymentMethod', detectedMethod);

        setVal('pricingType', q.pricing_type || 'fixed_price');
        onPricingTypeChange();
        setVal('warrantyPeriod', q.warranty_period || '');
        setVal('paymentTermsText', q.payment_terms || '');
        setVal('additionalTerms', q.additional_terms || '');

        showCostBreakdown(q);
        onPaymentMethodChange();
    }

    // ===================================
    // FINANCIAL LOGIC
    // ===================================
    function onBudgetTypeChange() {
        const type = document.getElementById('budgetType')?.value;
        const row = document.getElementById('budgetRangeRow');
        const flexRow = document.getElementById('budgetFlexRow');
        const val = parseFloat(document.getElementById('contractValue')?.value || 0);

        const pctEl = document.getElementById('budgetFlexPercent');
        let flexPct = parseFloat(pctEl?.value || '');
        if (!isFinite(flexPct) || flexPct < 0) flexPct = 10;
        if (pctEl && (pctEl.value === '' || !isFinite(parseFloat(pctEl.value)))) {
            pctEl.value = String(flexPct);
        }

        const materialCost = parseFloat(
            (quotationFullData && quotationFullData.material_cost) ??
            (selectedQuotation && selectedQuotation.material_cost) ??
            0
        );
        const baseForFlex = materialCost > 0 ? materialCost : val;

        if (type === 'flexible' && val > 0) {
            const delta = baseForFlex * (flexPct / 100);
            const minV = Math.max(0, val - delta);
            const maxV = val + delta;

            setVal('budgetMin', minV.toFixed(0));
            setVal('budgetMax', maxV.toFixed(0));
            if (row) row.style.display = 'grid';
            if (flexRow) flexRow.style.display = 'grid';
        } else {
            if (row) row.style.display = 'none';
            if (flexRow) flexRow.style.display = 'none';
        }
    }

    function onPricingTypeChange() {
        const type = document.getElementById('pricingType')?.value;
        const row = document.getElementById('hourlyRateRow');
        const val = parseFloat(document.getElementById('contractValue')?.value || 0);

        if (type === 'time_and_material') {
            if (row) row.style.display = 'grid';
            if (val > 0) setVal('spendingCap', (val * 1.1).toFixed(0));
        } else {
            if (row) row.style.display = 'none';
        }
    }

    function onContractValueChange() {
        onBudgetTypeChange();
        onPricingTypeChange();
        generatePaymentPreview();
    }

    function showCostBreakdown(d) {
        const bd = document.getElementById('costBreakdown');
        if (!bd) return;

        const unitSuffix = (unitLabel) => {
            const v = String(unitLabel || '').trim();
            return v ? ` (${v})` : '';
        };

        // Always show units next to the corresponding cost line labels (if stored in quotation)
        const labourLabelEl = document.getElementById('bdLabourLabel');
        if (labourLabelEl) labourLabelEl.textContent = `Labour${unitSuffix(d.labor_unit_label)}`;
        const materialLabelEl = document.getElementById('bdMaterialsLabel');
        if (materialLabelEl) materialLabelEl.textContent = `Materials${unitSuffix(d.material_unit_label)}`;

        if (d.labor_cost || d.material_cost) {
            bd.style.display = 'block';
            setText('bdLabour', `LKR ${parseFloat(d.labor_cost || 0).toLocaleString()}`);
            setText('bdMaterials', `LKR ${parseFloat(d.material_cost || 0).toLocaleString()}`);
            setText('bdTransport', `LKR ${parseFloat(d.transport_cost || 0).toLocaleString()}`);
            setText('bdOther', `LKR ${parseFloat(d.other_charges || 0).toLocaleString()}`);
            setText('bdTotal', `LKR ${parseFloat(d.total_amount || 0).toLocaleString()}`);
        } else {
            bd.style.display = 'none';
        }
    }

    // ===================================
    // PAYMENT METHOD CHANGE HANDLER
    // ===================================
    function onPaymentMethodChange() {
        if (unitPricingMode.active) {
            setVal('paymentMethod', 'milestone_based');
            const paymentMethodEl = document.getElementById('paymentMethod');
            if (paymentMethodEl) paymentMethodEl.value = 'milestone_based';
        }

        const method = document.getElementById('paymentMethod')?.value;
        const isMilestone = method === 'milestone_based';

        // Toggle info banners (keep these specific to method for clarity)
        const trackingInfo = document.getElementById('mpliTracking');
        const paymentInfo = document.getElementById('mpliPayment');
        if (trackingInfo) trackingInfo.style.display = isMilestone ? 'none' : 'flex';
        if (paymentInfo) paymentInfo.style.display = isMilestone ? 'flex' : 'none';

        // Toggle milestone payment notice in Step 5
        const notice = document.getElementById('milestonePaymentNotice');
        if (notice) notice.style.display = isMilestone ? 'flex' : 'none';

        // Rebuild phase rows to match the selected payment method
        resetMilestones();

        // Regenerate preview after milestone template reset
        generatePaymentPreview();
    }


    function autoDistributePayment(method) {
        // Row count is already correct after resetMilestones().
        // Just ensure percentages match for fixed-split methods in case
        // the user switched methods without triggering a full reset.
        const rows = document.querySelectorAll('#milestonesBody .milestone-row');
        if (rows.length === 0) return;

        const pcts = {
            'full_upfront': [100],
            'completion': [100],
            '50_50': [50, 50],
            budget_flexibility_percentage: getVal('budgetFlexPercent'),
            '30_70': [30, 70]
        };

        if (pcts[method]) {
            rows.forEach((row, i) => {
                const input = row.querySelector('.ms-pct-input');
                if (input) input.value = pcts[method][i] ?? 0;
            });
        }
        // milestone_based: keep whatever the user has entered
    }

    function toggleMilestonePaymentColumns(show) {
        // Payment columns were removed from this milestone table.
    }

    function applyUnitBasedMilestoneMode() {
        // Payment columns were removed from this milestone table.
    }

    function recalcMilestoneAmountFromPct(pctInput) {
        const total = parseFloat(getVal('contractValue') || 0);
        const pct = parseFloat(pctInput.value || 0);
        const row = pctInput.closest('.milestone-row');
        const amountInput = row?.querySelector('.ms-amount-input');
        if (amountInput && total > 0) {
            const isUnitBased = quotationFullData?.labor_unit_label || quotationFullData?.material_unit_label;
            // If unit-based, don't divide the rate by percentage. The rate stays the same.
            if (isUnitBased) {
                amountInput.value = total.toFixed(0);
            } else {
                amountInput.value = (total * pct / 100).toFixed(0);
            }
        }
    }

    function recalcMilestoneAmountsAll() {
        const total = parseFloat(getVal('contractValue') || 0);
        const isUnitBased = quotationFullData?.labor_unit_label || quotationFullData?.material_unit_label;
        document.querySelectorAll('#milestonesBody .milestone-row').forEach(row => {
            const pctInput = row.querySelector('.ms-pct-input');
            const amountInput = row.querySelector('.ms-amount-input');
            if (pctInput && amountInput && total > 0) {
                const pct = parseFloat(pctInput.value || 0);
                if (isUnitBased) {
                    amountInput.value = total.toFixed(0);
                } else {
                    amountInput.value = (total * pct / 100).toFixed(0);
                }
            }
        });
    }

    function recalcMilestoneTotals() {
        const isUnitBased = quotationFullData?.labor_unit_label || quotationFullData?.material_unit_label;
        const warning = document.getElementById('msTotalWarning');

        // For unit-based, no financial validation - hide warning, skip everything
        if (isUnitBased) {
            if (warning) warning.style.display = 'none';
            return;
        }

        let totalPct = 0;
        let totalAmount = 0;
        document.querySelectorAll('#milestonesBody .milestone-row').forEach(row => {
            totalPct += parseFloat(row.querySelector('.ms-pct-input')?.value || 0);
            totalAmount += parseFloat(row.querySelector('.ms-amount-input')?.value || 0);
        });

        setText('msTotalPct', totalPct.toFixed(0));
        const totalAmountEl = document.getElementById('msTotalAmount');
        if (totalAmountEl) totalAmountEl.textContent = totalAmount.toLocaleString();

        // Validation warning
        const warningText = document.getElementById('msTotalWarningText');
        const contractValue = parseFloat(getVal('contractValue') || 0);

        if (warning && warningText) {
            if (Math.abs(totalPct - 100) > 0.5) {
                warning.style.display = 'flex';
                warningText.textContent = `Phase percentages total ${totalPct.toFixed(0)}% - they must equal 100%.`;
                warning.className = 'milestone-total-warning error';
            } else if (contractValue > 0 && Math.abs(totalAmount - contractValue) > 1) {
                warning.style.display = 'flex';
                warningText.textContent = `Phase amounts (LKR ${totalAmount.toLocaleString()}) don't match contract value (LKR ${contractValue.toLocaleString()}).`;
                warning.className = 'milestone-total-warning error';
            } else if (totalPct > 0) {
                warning.style.display = 'flex';
                warningText.textContent = `Phases total 100% - LKR ${totalAmount.toLocaleString()}`;
                warning.className = 'milestone-total-warning success';
            } else {
                warning.style.display = 'none';
            }
        }
    }

    function generatePaymentPreview() {
        const method = document.getElementById('paymentMethod')?.value;
        const total = parseFloat(document.getElementById('contractValue')?.value || 0);
        const container = document.getElementById('paymentScheduleBody');

        if (!container || total <= 0) return;

        // If milestone-based, read from actual milestone rows
        if (method === 'milestone_based') {
            const rows = document.querySelectorAll('#milestonesBody .milestone-row');
            let html = '<div class="payment-schedule-items">';

            rows.forEach((row, i) => {
                const name = row.querySelector('input[name="ms_name[]"]')?.value || `Milestone ${i + 1}`;
                const date = row.querySelector('input[name="ms_date[]"]')?.value || '';

                const dateStr = date ? ` - ${formatDateDisplay(date)}` : '';
                html += `
                    <div class="ps-item">
                        <div class="ps-info">
                            <span class="ps-label"><i class="fas fa-flag"></i> ${esc(name)}${dateStr}</span>
                            <span class="ps-amount">Amount will be set later</span>
                        </div>
                    </div>
                `;
            });

            html += '</div>';

            container.innerHTML = html;
            return;
        }

        // For non-milestone methods, use fixed schedules
        const schedules = {
            'full_upfront': [{ label: 'Full Payment at Start', pct: 100 }],
            '50_50': [
                { label: 'Upfront Payment (50%)', pct: 50 },
                { label: 'On Completion (50%)', pct: 50 }
            ],
            '30_70': [
                { label: 'Advance Payment (30%)', pct: 30 },
                { label: 'On Completion (70%)', pct: 70 }
            ],
            'completion': [{ label: 'After Completion (100%)', pct: 100 }]
        };

        const items = schedules[method] || [{ label: 'Full Payment', pct: 100 }];
        let html = '<div class="payment-schedule-items">';

        items.forEach(item => {
            const amount = (total * item.pct / 100);
            html += `
                <div class="ps-item">
                    <div class="ps-bar" style="width: ${item.pct}%"></div>
                    <div class="ps-info">
                        <span class="ps-label">${item.label}</span>
                        <span class="ps-amount">LKR ${amount.toLocaleString(undefined, { minimumFractionDigits: 2 })}</span>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
    }

    // ===================================
    // TIMELINE & MILESTONES
    // ===================================
    function enforceDateBounds() {
        const startEl = document.getElementById('startDate');
        const endEl = document.getElementById('endDate');
        if (!startEl || !endEl) return;

        const start = startEl.value;
        const end = endEl.value;

        if (start) {
            endEl.min = start;
        } else {
            endEl.removeAttribute('min');
        }

        if (end) {
            startEl.max = end;
        } else {
            startEl.removeAttribute('max');
        }

        if (start && end) {
            const s = new Date(start);
            const e = new Date(end);
            if (e < s) {
                endEl.value = start;
            }
        }
    }

    function onDateChange() {
        const start = document.getElementById('startDate')?.value;
        const end = document.getElementById('endDate')?.value;

        enforceDateBounds();

        if (start && end) {
            const s = new Date(start);
            const e = new Date(end);
            const diff = Math.ceil((e - s) / (1000 * 60 * 60 * 24));
            if (diff > 0) {
                setVal('estimatedDuration', diff);
            }
        }
    }

    function setMilestoneDates(startStr, endStr) {
        if (!startStr || !endStr) return;
        const dates = document.querySelectorAll('#milestonesBody input[name="ms_date[]"]');
        const start = new Date(startStr);
        const end = new Date(endStr);
        const total = dates.length;

        dates.forEach((input, i) => {
            if (total === 1) {
                input.value = startStr;
            } else {
                const ratio = i / (total - 1);
                const date = new Date(start.getTime() + ratio * (end.getTime() - start.getTime()));
                input.value = date.toISOString().split('T')[0];
            }
        });
    }

    function addMilestoneRow() {
        const tbody = document.getElementById('milestonesBody');
        if (!tbody) return;

        const rowCount = tbody.querySelectorAll('.milestone-row').length + 1;
        const tr = document.createElement('tr');
        tr.className = 'milestone-row';
        tr.innerHTML = `
            <td>${rowCount}</td>
            <td><input type="text" name="ms_name[]" placeholder="Phase name"></td>
            <td><input type="text" name="ms_desc[]" placeholder="Description"></td>
            <td><input type="date" name="ms_date[]"></td>
            <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
        `;
        tbody.appendChild(tr);
    }

    function renumberMilestones() {
        const rows = document.querySelectorAll('#milestonesBody .milestone-row');
        rows.forEach((row, i) => {
            row.querySelector('td:first-child').textContent = i + 1;
        });
    }

    function resetMilestones() {
        const tbody = document.getElementById('milestonesBody');
        if (!tbody) return;

        const method = document.getElementById('paymentMethod')?.value || 'milestone_based';

        if (unitPricingMode.active) {
            const laborUnitLabel = (unitPricingMode.laborUnitLabel || '').trim();
            const materialUnitLabel = (unitPricingMode.materialUnitLabel || '').trim();

            const unitRows = [];
            if (laborUnitLabel) {
                unitRows.push({
                    name: `Labour (${laborUnitLabel})`,
                    desc: 'Submit actual labour units after completion for customer verification.'
                });
            }
            if (materialUnitLabel) {
                unitRows.push({
                    name: `Materials (${materialUnitLabel})`,
                    desc: 'Submit actual material units (and actual rate if changed) for customer verification.'
                });
            }

            if (unitRows.length > 0) {
                const end = document.getElementById('endDate')?.value || '';
                const existing = Array.from(tbody.querySelectorAll('.milestone-row'));

                // If the table is empty, seed it with required unit rows
                if (existing.length === 0) {
                    tbody.innerHTML = unitRows.map((r, i) => `
                        <tr class="milestone-row">
                            <td>${i + 1}</td>
                            <td><input type="text" name="ms_name[]" value="${esc(r.name)}"></td>
                            <td><input type="text" name="ms_desc[]" value="${esc(r.desc)}"></td>
                            <td><input type="date" name="ms_date[]" value="${end}"></td>
                            <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                        </tr>
                    `).join('');
                } else {
                    // Ensure required rows exist at the top, but do not overwrite user edits.
                    for (let i = 0; i < unitRows.length; i++) {
                        let row = tbody.querySelectorAll('.milestone-row')[i];
                        if (!row) {
                            const tr = document.createElement('tr');
                            tr.className = 'milestone-row';
                            tr.innerHTML = `
                                <td>${i + 1}</td>
                                <td><input type="text" name="ms_name[]" value="${esc(unitRows[i].name)}"></td>
                                <td><input type="text" name="ms_desc[]" value="${esc(unitRows[i].desc)}"></td>
                                <td><input type="date" name="ms_date[]" value="${end}"></td>
                                <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
                            `;
                            tbody.appendChild(tr);
                            row = tr;
                        }

                        const nameInput = row.querySelector('input[name="ms_name[]"]');
                        const descInput = row.querySelector('input[name="ms_desc[]"]');
                        const dateInput = row.querySelector('input[name="ms_date[]"]');
                        if (nameInput && !nameInput.value.trim()) nameInput.value = unitRows[i].name;
                        if (descInput && !descInput.value.trim()) descInput.value = unitRows[i].desc;
                        if (dateInput && !dateInput.value) dateInput.value = end;
                    }
                }

                // Renumber after any adjustments
                if (typeof renumberMilestones === 'function') renumberMilestones();
                return;
            }
        }

        // Define templates keyed by payment method
        // Each entry: array of { name, desc }
        const templates = {
            'full_upfront': [
                { name: 'Full Upfront Payment', desc: 'Full project payment collected before work begins' }
            ],
            'completion': [
                { name: 'Payment on Completion', desc: 'Full payment collected after all work is completed and accepted' }
            ],
            '50_50': [
                { name: 'Advance Payment (50%)', desc: 'First instalment paid at project commencement' },
                { name: 'Final Payment (50%)', desc: 'Second instalment paid upon project completion' }
            ],
            '30_70': [
                { name: 'Advance Payment (30%)', desc: 'Initial payment collected at project start' },
                { name: 'Final Payment (70%)', desc: 'Remaining balance collected on project completion' }
            ],
            // milestone_based: 3-row editable template
            'milestone_based': [
                { name: 'Project Commencement', desc: 'Site preparation and initial setup' },
                { name: 'Mid-Project Review', desc: 'Progress inspection and quality check' },
                { name: 'Project Handover', desc: 'Final inspection, cleanup, and handover' }
            ]
        };

        const rows = templates[method] || templates['milestone_based'];

        tbody.innerHTML = rows.map((r, i) => `
            <tr class="milestone-row">
                <td>${i + 1}</td>
                <td><input type="text" name="ms_name[]" placeholder="Phase name" value="${r.name}"></td>
                <td><input type="text" name="ms_desc[]" placeholder="Description" value="${r.desc}"></td>
                <td><input type="date" name="ms_date[]"></td>
                <td><button type="button" class="btn-icon btn-remove-ms" title="Remove"><i class="fas fa-trash-alt"></i></button></td>
            </tr>
        `).join('');

        // Pre-fill dates using the project's start and end dates
        const start = document.getElementById('startDate')?.value;
        const end = document.getElementById('endDate')?.value;
        if (start && end) {
            setMilestoneDates(start, end);
        }
    }

    // ===================================
    // REVIEW / PREVIEW
    // ===================================
    function buildSharedPreviewContractObject() {
        const today = new Date();
        const ref = getVal('projectReference') || `CTR-${today.getFullYear()}-XXXX`;

        const paymentMethod = getVal('paymentMethod');
        const totalVal = parseFloat(getVal('contractValue') || 0);

        const q = quotationFullData || selectedQuotation || {};
        // NOTE: unitPricingMode labels can be empty-string while async quotation load is in-flight.
        // Use the first non-empty label so preview matches post-create rendering.
        const laborUnitLabel = String(unitPricingMode?.laborUnitLabel || q?.labor_unit_label || q?.labour_unit_label || '').trim();
        const materialUnitLabel = String(unitPricingMode?.materialUnitLabel || q?.material_unit_label || q?.material_unit || '').trim();
        const laborCost = q?.labor_cost ?? q?.labour_cost ?? null;
        const materialCost = q?.material_cost ?? null;
        const transportCost = q?.transport_cost ?? null;
        const otherCharges = q?.other_charges ?? null;

        const budgetTypeKey = getVal('budgetType') || 'fixed';
        const flexPct = parseFloat(getVal('budgetFlexPercent') || 10);
        const isFlexible = budgetTypeKey === 'flexible' && isFinite(flexPct) && flexPct > 0;

        const matVal = document.querySelector('input[name="materials_responsibility"]:checked')?.value || 'company';

        const milestones = [];
        document.querySelectorAll('#milestonesBody .milestone-row').forEach((row, i) => {
            const title = row.querySelector('input[name="ms_name[]"]')?.value || `Milestone ${i + 1}`;
            const description = row.querySelector('input[name="ms_desc[]"]')?.value || '';
            const due_date = row.querySelector('input[name="ms_date[]"]')?.value || '';

            const ms = {
                title,
                description,
                due_date,
                status: 'pending'
            };

            milestones.push(ms);
        });

        const contractObj = {
            status: 'draft',
            contract_number: ref,
            contract_date: today.toISOString().slice(0, 10),

            // Parties
            client: {
                name: getVal('clientName') || getText('partyClientName') || '—',
                email: getText('partyClientEmail') || '',
                address: getText('partyClientAddress') || '',
                district: ''
            },
            company: {
                name: getText('partyCompanyName') || '—',
                registration_no: getText('partyCompanyReg') || '',
                address: getText('partyCompanyAddress') || '',
                contact: getText('partyCompanyPhone') || ''
            },

            // Project
            project_title: getVal('projectTitle') || '',
            project_reference: getVal('projectReference') || '',
            project_location: getVal('projectLocation') || '',
            project_type: getVal('projectType') || '',
            project_description: getVal('projectDescription') || '',

            // Scope
            scope_description: getVal('scopeDescription') || '',
            scope_inclusions: getVal('scopeInclusions') || 'As per quotation',
            scope_exclusions: getVal('scopeExclusions') || 'None specified',
            materials_responsibility: matVal,

            // Duration
            start_date: getVal('startDate') || '',
            end_date: getVal('endDate') || '',
            progress_percentage: 0,

            // Financial
            total_budget: isFinite(totalVal) ? totalVal : 0,
            value: isFinite(totalVal) ? totalVal : 0,
            budget_type: budgetTypeKey,
            budget_min: isFlexible ? (totalVal * (1 - flexPct / 100)) : null,
            budget_max: isFlexible ? (totalVal * (1 + flexPct / 100)) : null,
            payment_method: paymentMethod,
            amount_paid: 0,
            amount_pending: isFinite(totalVal) ? totalVal : 0,
            labor_unit_label: laborUnitLabel || null,
            material_unit_label: materialUnitLabel || null,
            labor_cost: laborCost,
            material_cost: materialCost,
            transport_cost: transportCost,
            other_charges: otherCharges,
            late_payment_penalty: getVal('latePaymentPenalty') || 'As per standard terms',

            // Clauses
            variation_clause: !!document.getElementById('variationClause')?.checked,
            dispute_resolution: getVal('disputeResolution') || 'Disputes shall be resolved through mediation via the FixLanka platform.',

            // Misc
            sent_to_customer: false,
            milestones
        };

        // Keep legacy top-level fields too (shared renderer supports both)
        contractObj.company_name = contractObj.company.name;
        contractObj.company_registration = contractObj.company.registration_no;
        contractObj.company_phone = contractObj.company.contact;
        contractObj.customer_email = contractObj.client.email;

        return contractObj;
    }

    function renderReviewViaSharedRenderer() {
        if (!window.ContractPreview || typeof window.ContractPreview.renderHTML !== 'function') return false;

        const host = document.getElementById('contractPreview');
        if (!host) return false;

        // Avoid nested .contract-preview wrappers
        host.classList.remove('contract-preview');

        const c = buildSharedPreviewContractObject();
        const method = c.payment_method;
        host.innerHTML = window.ContractPreview.renderHTML(c, {
            isMilestoneBased: method === 'milestone_based',
            paymentLabel: formatPaymentMethodLabel(method),
            renderMilestoneAction: () => '—'
        });
        return true;
    }

    function populateReview() {
        // Prefer shared renderer so “preview before create” === “preview after create”
        if (renderReviewViaSharedRenderer()) return;

        const today = new Date();
        setText('previewDate', today.toLocaleDateString('en-LK', { year: 'numeric', month: 'long', day: 'numeric' }));

        // Reference
        const ref = getVal('projectReference') || `CTR-${today.getFullYear()}-XXXX`;
        setText('previewRef', `Contract Reference: ${ref}`);

        // Parties
        setText('previewClientName', getVal('clientName') || getText('partyClientName'));
        setText('previewClientDetails', `${getText('partyClientAddress')} | ${getText('partyClientEmail')}`);
        setText('previewCompanyName', getText('partyCompanyName'));
        setText('previewCompanyDetails', `${getText('partyCompanyReg')} | ${getText('partyCompanyAddress')}`);

        // Project
        setText('previewProjectTitle', getVal('projectTitle'));
        setText('previewProjectRef', getVal('projectReference'));
        setText('previewProjectLocation', getVal('projectLocation'));
        setText('previewProjectType', getVal('projectType'));
        setText('previewProjectDesc', getVal('projectDescription'));

        // Scope
        setText('previewScopeDesc', getVal('scopeDescription'));
        setText('previewInclusions', getVal('scopeInclusions') || 'As per quotation');
        setText('previewExclusions', getVal('scopeExclusions') || 'None specified');

        const matVal = document.querySelector('input[name="materials_responsibility"]:checked')?.value || 'company';
        const matLabels = {
            'company': 'All materials supplied by the Contractor',
            'client': 'All materials supplied by the Client',
            'shared': 'Shared responsibility between both parties'
        };
        setText('previewMaterials', matLabels[matVal]);

        // Timeline
        setText('previewStartDate', formatDateDisplay(getVal('startDate')));
        setText('previewEndDate', formatDateDisplay(getVal('endDate')));
        setText('previewDuration', getVal('estimatedDuration'));

        // Milestones table
        const msBody = document.getElementById('previewMilestonesBody');
        if (msBody) {
            msBody.innerHTML = '';
            document.querySelectorAll('#milestonesBody .milestone-row').forEach((row, i) => {
                const name = row.querySelector('input[name="ms_name[]"]')?.value || '';
                const date = row.querySelector('input[name="ms_date[]"]')?.value || '';
                if (name) {
                    msBody.innerHTML += `<tr><td>${i + 1}</td><td>${esc(name)}</td><td>${formatDateDisplay(date)}</td></tr>`;
                }
            });
        }

        // Financial
        const totalVal = parseFloat(getVal('contractValue') || 0);
        const isUnitBased = quotationFullData?.labor_unit_label || quotationFullData?.material_unit_label;
        const totalSuffix = isUnitBased ? ' (per unit)' : '';
        setText('previewValue', `LKR ${totalVal.toLocaleString()}${totalSuffix}`);
        const budgetTypeKey = getVal('budgetType') || 'fixed';
        const pct = parseFloat(getVal('budgetFlexPercent') || 10);
        const budgetLabel = budgetTypeKey === 'flexible'
            ? `Flexible (Materials ±${isFinite(pct) ? pct : 10}%)`
            : 'Fixed Price';
        setText('previewBudgetType', budgetLabel);
        setText('previewPaymentMethod', formatPaymentMethodLabel(getVal('paymentMethod')));

        // Payment schedule in preview
        generatePreviewPaymentSchedule(totalVal, getVal('paymentMethod'));

        setText('previewLatePayment', getVal('latePaymentPenalty') || 'As per standard terms');

        // Variation
        const varChecked = document.getElementById('variationClause')?.checked;
        setText('previewVariation', varChecked
            ? 'Any change to scope, pricing, materials, or timeline must be approved in writing by both parties before execution. Variations will be managed through the FixLanka platform.'
            : 'Variation control is not enabled for this contract.'
        );

        // Communication
        setText('previewCommChannel', 'FixLanka Platform');
        setText('previewDisputeRes', getVal('disputeResolution') || 'As per standard terms');

        // Additional
        const additional = getVal('additionalTerms');
        const addSection = document.getElementById('previewAdditionalSection');
        if (additional && addSection) {
            addSection.style.display = 'block';
            setText('previewAdditionalTerms', additional);
        } else if (addSection) {
            addSection.style.display = 'none';
        }
    }

    function generatePreviewPaymentSchedule(total, method) {
        const container = document.getElementById('previewPaymentSchedule');
        if (!container || total <= 0) return;

        // If milestone-based, read from actual milestone rows
        if (method === 'milestone_based') {
            let html = '<table class="preview-milestones-table"><thead><tr><th>Milestone</th><th>%</th><th>Amount (LKR)</th></tr></thead><tbody>';
            document.querySelectorAll('#milestonesBody .milestone-row').forEach((row, i) => {
                const name = row.querySelector('input[name="ms_name[]"]')?.value || `Milestone ${i + 1}`;
                const pct = parseFloat(row.querySelector('.ms-pct-input')?.value || 0);
                const amount = parseFloat(row.querySelector('.ms-amount-input')?.value || 0);
                html += `<tr><td>${esc(name)}</td><td>${pct}%</td><td>${amount.toLocaleString()}</td></tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
            return;
        }

        const schedules = {
            'full_upfront': [{ l: 'Full Upfront', p: 100 }],
            '50_50': [{ l: 'Upfront (50%)', p: 50 }, { l: 'Completion (50%)', p: 50 }],
            '30_70': [{ l: 'Advance (30%)', p: 30 }, { l: 'Completion (70%)', p: 70 }],
            'completion': [{ l: 'After Completion', p: 100 }]
        };

        const items = schedules[method] || [{ l: 'Full Payment', p: 100 }];
        let html = '<table class="preview-milestones-table"><thead><tr><th>Payment</th><th>%</th><th>Amount (LKR)</th></tr></thead><tbody>';
        items.forEach(item => {
            html += `<tr><td>${item.l}</td><td>${item.p}%</td><td>${(total * item.p / 100).toLocaleString()}</td></tr>`;
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    }

    let isSubmitting = false;

    // ===================================
    // FORM SUBMISSION
    // ===================================
    async function submitContract(e) {
        if (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }

        if (isSubmitting) return;

        isSubmitting = true;
        const submitBtn = document.getElementById('formSubmitBtn');
        if (submitBtn) submitBtn.disabled = true;

        // Final validation (skips Step 1 in edit mode)
        if (!validateAllSteps()) {
            isSubmitting = false;
            if (submitBtn) submitBtn.disabled = false;
            return;
        }

        try {
            const payload = collectFormData();
            const editId = getVal('editContractId');
            const isEdit = editId && editId.length > 0;

            // Safety check: in edit mode, contract ID must exist
            if (isEdit && !editId) {
                showNotification('error', 'Error', 'Contract ID is missing. Cannot save changes.');
                return;
            }

            let url, method;

            if (isEdit) {
                // UPDATE existing contract using its database ID
                url = `/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=update&id=${editId}`;
                payload.contract_id = editId;
                method = 'POST';
            } else {
                // CREATE new contract from quotation
                url = `/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=create`;
                payload.send_to_customer = true;
                method = 'POST';
            }

            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (result.success) {
                formDirty = false;

                // Close modal immediately
                const modal = document.getElementById('newContractModal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.classList.remove('enhanced-form-active');
                    window.enhancedFormActive = false;
                    document.body.style.overflow = '';
                }

                // Show success notification
                if (isEdit) {
                    showNotification('success', 'Contract updated successfully!', 'Your changes have been saved.');
                } else {
                    showNotification(
                        'success',
                        `Contract ${result.data?.contract_number || ''} created & sent!`,
                        'The contract has been sent to the customer for review.'
                    );
                }

                // Reset form and reload data AFTER modal is hidden
                setTimeout(() => {
                    resetForm();

                    // Clear edit state
                    const editField = document.getElementById('editContractId');
                    if (editField) editField.value = '';

                    // Reload the contracts list
                    if (typeof loadContractsData === 'function') {
                        loadContractsData();
                    }
                }, 400);

            } else {
                showNotification('error', 'Failed to save contract', result.message || 'Unknown error');
            }

        } catch (error) {
            console.error('Submit error:', error);
            showNotification('error', 'Error', 'Failed to connect to the server. Please try again.');
        } finally {
            isSubmitting = false;
            const submitBtn = document.getElementById('formSubmitBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        }
    }

    function collectFormData() {
        const data = {
            quotation_id: getVal('selectedQuotationId'),
            request_id: getVal('selectedRequestId'),
            job_request_id: getVal('selectedRequestId'),
            customer_id: getVal('customerId'),
            contract_date: new Date().toISOString().split('T')[0],

            // Project
            project_title: getVal('projectTitle'),
            project_reference: getVal('projectReference'),
            project_location: getVal('projectLocation'),
            project_type: getVal('projectType'),
            project_description: getVal('projectDescription'),

            // Scope
            scope_description: getVal('scopeDescription'),
            scope_inclusions: getVal('scopeInclusions'),
            scope_exclusions: getVal('scopeExclusions'),
            scope_standards: getVal('scopeStandards'),
            materials_responsibility: document.querySelector('input[name="materials_responsibility"]:checked')?.value || 'company',

            // Timeline
            start_date: getVal('startDate'),
            end_date: getVal('endDate'),
            estimated_duration: getVal('estimatedDuration'),

            // Financial
            total_budget: getVal('contractValue'),
            budget_type: getVal('budgetType'),
            budget_min: getVal('budgetMin'),
            budget_max: getVal('budgetMax'),
            tax_inclusive: getVal('taxInclusive'),
            payment_method: getVal('paymentMethod'),
            pricing_type: getVal('pricingType'),
            hourly_rate: getVal('hourlyRate'),
            spending_cap: getVal('spendingCap'),

            // Delays
            late_payment_penalty: getVal('latePaymentPenalty'),
            pause_work_clause: document.getElementById('pauseWorkClause')?.checked ? 1 : 0,
            time_extension_clause: document.getElementById('timeExtensionClause')?.checked ? 1 : 0,

            // Clauses
            variation_clause: document.getElementById('variationClause')?.checked ? 1 : 0,
            communication_channel: 'system',
            dispute_resolution: getVal('disputeResolution'),

            // Additional
            warranty_period: getVal('warrantyPeriod'),
            payment_terms: getVal('paymentTermsText'),
            additional_terms: getVal('additionalTerms'),

            // Milestones
            milestones: collectMilestones()
        };

        return data;
    }

    function collectMilestones() {
        const milestones = [];
        const method = getVal('paymentMethod');
        const rows = document.querySelectorAll('#milestonesBody .milestone-row');

        rows.forEach((row, i) => {
            const name = row.querySelector('input[name="ms_name[]"]')?.value;
            const desc = row.querySelector('input[name="ms_desc[]"]')?.value;
            const date = row.querySelector('input[name="ms_date[]"]')?.value;

            if (name && desc && date) {
                milestones.push({
                    title: name,
                    description: desc,
                    due_date: date,
                    amount: '0.00',
                    percentage: 0
                });
            }
        });

        return milestones;
    }

    function validateAllSteps() {
        const origStep = currentStep;
        let allValid = true;

        // In edit mode, skip Step 1 (quotation selection) - quotation is already assigned
        const isEditMode = !!document.getElementById('editContractId')?.value;
        const startStep = isEditMode ? 2 : 1;

        for (let s = startStep; s <= TOTAL_STEPS - 1; s++) {
            currentStep = s;
            if (!validateCurrentStep()) {
                showStep(s);
                allValid = false;
                break;
            }
        }

        if (allValid) {
            currentStep = origStep;
        }

        return allValid;
    }

    // ===================================
    // DRAFT MANAGEMENT
    // ===================================
    function debouncedAutoSave() {
        if (autoSaveTimer) clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            saveDraft(true);
        }, 5000);
    }

    async function saveDraft(silent) {
        const quotationId = getVal('selectedQuotationId');
        if (!quotationId) return;

        const statusEl = document.getElementById('draftStatus');
        const statusText = document.getElementById('draftStatusText');

        try {
            if (statusEl) statusEl.style.display = 'inline-flex';
            if (statusText) statusText.textContent = 'Saving...';

            const payload = {
                quotation_id: quotationId,
                current_step: currentStep,
                form_data: collectFormData()
            };

            const resp = await fetch(`${ENHANCED_API}?action=saveDraft`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await readJsonOrNull(resp);
            if (!resp.ok || !result) {
                throw new Error(`Draft save failed (${resp.status})`);
            }

            if (result.success) {
                if (statusText) statusText.textContent = 'Draft saved';
                if (!silent) showNotification('success', 'Draft Saved', 'Your progress has been saved.');
            } else {
                throw new Error(result.message || 'Draft save failed');
            }

        } catch (e) {
            if (statusText) statusText.textContent = 'Save failed';
            console.error('Draft save error:', e);
            if (!silent) showNotification('error', 'Save Failed', 'Could not save draft. Please try again.');
        }

        // Hide status after 3s
        setTimeout(() => {
            if (statusEl) statusEl.style.display = 'none';
        }, 3000);
    }

    // ===================================
    // UTILITY FUNCTIONS
    // ===================================
    function setVal(id, value) {
        const el = document.getElementById(id);
        if (el && value !== null && value !== undefined) {
            el.value = value;
        }
    }

    function getVal(id) {
        return document.getElementById(id)?.value || '';
    }

    function setText(id, text) {
        const el = document.getElementById(id);
        if (el) el.textContent = text || '-';
    }

    function getText(id) {
        return document.getElementById(id)?.textContent || '';
    }

    function esc(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    function formatPaymentMethodLabel(method) {
        const labels = {
            'full_upfront': 'Full Payment Upfront',
            'milestone_based': 'Milestone-Based',
            '50_50': '50/50 Split',
            '30_70': '30/70 Split',
            'completion': '100% After Completion'
        };
        return labels[method] || method || '-';
    }

    function formatDateDisplay(dateStr) {
        if (!dateStr) return '-';
        try {
            return new Date(dateStr).toLocaleDateString('en-LK', { year: 'numeric', month: 'short', day: 'numeric' });
        } catch {
            return dateStr;
        }
    }

    function showNotification(type, title, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `ecf-notification ecf-notification-${type}`;
        notification.innerHTML = `
            <div class="ecf-notification-icon">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            </div>
            <div class="ecf-notification-content">
                <strong>${esc(title)}</strong>
                <p>${esc(message)}</p>
            </div>
            <button class="ecf-notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        document.body.appendChild(notification);

        // Auto-remove after 5s
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // ===================================
    // EXPOSE EDIT MODE TO EXTERNAL CODE
    // ===================================
    window.openContractForEdit = function (contractData) {
        const modal = document.getElementById('newContractModal');
        if (!modal) return;

        // Reset form first
        const form = document.getElementById('contractForm');
        if (form) form.reset();

        const unitInfo = document.getElementById('unitMeasurementInfo');
        if (unitInfo) unitInfo.style.display = 'none';

        // Set edit contract ID after reset
        const editField = document.getElementById('editContractId');
        if (editField) editField.value = contractData.contract_id;

        // Populate form with existing contract data (global function from contracts-enhanced.js)
        if (typeof populateFormWithContract === 'function') {
            populateFormWithContract(contractData);
        }

        // Update title & button
        const titleEl = document.getElementById('formModalTitle');
        if (titleEl) titleEl.textContent = 'Edit Contract';
        const submitBtn = document.getElementById('formSubmitBtn');
        if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';

        // Skip to step 2 (project details) - Step 1 is quotation selection, not editable
        currentStep = 2;
        showStep(2);
        updateStepIndicators();

        // Mark step 1 as completed in progress bar
        document.querySelectorAll('.progress-step').forEach(el => {
            const step = parseInt(el.dataset.step);
            if (step === 1) el.classList.add('completed');
        });

        // Open modal
        modal.classList.add('active');
        document.body.classList.add('enhanced-form-active');
        window.enhancedFormActive = true;
        document.body.style.overflow = 'hidden';
        formDirty = false;
    };

    function updateUnitBasedLabels(d) {
        const laborUnitLabel = (d.labor_unit_label || '').trim();
        const materialUnitLabel = (d.material_unit_label || '').trim();
        const isUnitBased = Boolean(laborUnitLabel || materialUnitLabel);

        unitPricingMode = {
            active: isUnitBased,
            laborUnitLabel,
            materialUnitLabel
        };

        // Build a human-readable unit measurement summary.
        let measurementText = 'Lump sum (per job)';
        if (isUnitBased) {
            const parts = [];
            if (laborUnitLabel) parts.push(`Labor: ${laborUnitLabel}`);
            if (materialUnitLabel) parts.push(`Materials: ${materialUnitLabel}`);
            measurementText = parts.join(' / ') || 'Per unit';
        }

        // Show unit measurement info (from quotation)
        const info = document.getElementById('unitMeasurementInfo');
        const infoText = document.getElementById('unitMeasurementText');
        if (info && infoText) {
            infoText.textContent = `Unit measurement: ${measurementText}`;
            info.style.display = 'flex';
        }

        const bdTotalLabel = document.querySelector('.breakdown-item.total span:first-child');
        if (bdTotalLabel) {
            bdTotalLabel.textContent = 'Total';
        }

        // Preview in Step 8
        const previewValueCell = document.getElementById('previewValue')?.parentElement;
        if (previewValueCell) {
            const unitSpanId = 'previewValueUnitSuffix';
            let unitSpan = document.getElementById(unitSpanId);
            if (!unitSpan) {
                unitSpan = document.createElement('span');
                unitSpan.id = unitSpanId;
                previewValueCell.appendChild(unitSpan);
            }
            unitSpan.textContent = isUnitBased ? ` (${measurementText})` : ' (per job)';
        }

        applyUnitPricingLocks();
    }

    function applyUnitPricingLocks() {
        const paymentMethodEl = document.getElementById('paymentMethod');
        const pricingTypeEl = document.getElementById('pricingType');
        const hourlyRow = document.getElementById('hourlyRateRow');
        const addMsBtn = document.getElementById('addMilestoneBtn');

        if (!unitPricingMode.active) {
            if (paymentMethodEl) {
                paymentMethodEl.disabled = false;
                Array.from(paymentMethodEl.options || []).forEach(opt => {
                    opt.disabled = false;
                    opt.hidden = false;
                });
            }
            if (pricingTypeEl) {
                pricingTypeEl.disabled = false;
            }
            if (hourlyRow) {
                hourlyRow.style.display = 'none';
            }
            if (addMsBtn) {
                addMsBtn.disabled = false;
            }

            document.querySelectorAll('#milestonesBody .btn-remove-ms').forEach(btn => {
                btn.style.display = '';
            });
            return;
        }

        // Unit-priced mode: milestone-based only.
        if (paymentMethodEl) {
            paymentMethodEl.value = 'milestone_based';
            Array.from(paymentMethodEl.options || []).forEach(opt => {
                const keep = opt.value === 'milestone_based';
                opt.disabled = !keep;
                opt.hidden = !keep;
            });
            paymentMethodEl.disabled = true;
        }
        setVal('paymentMethod', 'milestone_based');

        // Unit-priced mode: fixed pricing type (no hourly/time-based pricing).
        if (pricingTypeEl) {
            pricingTypeEl.value = 'fixed_price';
            pricingTypeEl.disabled = true;
        }
        setVal('pricingType', 'fixed_price');
        setVal('hourlyRate', '');
        setVal('spendingCap', '');
        if (hourlyRow) hourlyRow.style.display = 'none';

        // Lock milestones to unit billing phases
        if (addMsBtn) addMsBtn.disabled = false;
        resetMilestones();

        // Hide remove button only for the required unit-billing rows
        const reqCount = ((unitPricingMode.laborUnitLabel || '').trim() ? 1 : 0) + ((unitPricingMode.materialUnitLabel || '').trim() ? 1 : 0);
        const rows = Array.from(document.querySelectorAll('#milestonesBody .milestone-row'));
        rows.forEach((row, idx) => {
            const rm = row.querySelector('.btn-remove-ms');
            if (!rm) return;
            rm.style.display = (idx < reqCount) ? 'none' : '';
        });

        generatePaymentPreview();
    }

    function esc(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
})();
