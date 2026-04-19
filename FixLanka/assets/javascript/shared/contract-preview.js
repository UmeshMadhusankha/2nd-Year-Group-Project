/* Shared company/customer contract preview renderer
   - No backend/DB dependencies
   - Renders a company-style contract document preview with unit-priced settlement details
*/

(function () {
    'use strict';

    function esc(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatCurrency(val) {
        const num = parseFloat(val) || 0;
        return 'LKR ' + num.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function formatDate(d) {
        if (!d) return '—';
        const dt = new Date(d);
        if (Number.isNaN(dt.getTime())) return '—';
        return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function normalizeStatusClass(status) {
        const s = String(status || 'draft').toLowerCase();
        if (['active'].includes(s)) return 'active';
        if (['in_progress'].includes(s)) return 'in_progress';
        if (['pending', 'pending_signature'].includes(s)) return s;
        if (['completed'].includes(s)) return 'completed';
        if (['terminated', 'disputed', 'rejected'].includes(s)) return s;
        if (['draft'].includes(s)) return 'draft';
        return 'draft';
    }

    function formatStatusText(status) {
        const s = String(status || 'draft');
        return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    }

    function formatPaymentMethod(method) {
        const map = {
            full_upfront: 'Full Upfront',
            milestone_based: 'Milestone-Based',
            '50_50': '50/50 Split',
            '30_70': '30/70 Split',
            completion: 'On Completion'
        };
        return map[method] || (method ? String(method) : '—');
    }

    function formatCommunicationChannel() {
        return 'FixLanka Platform';
    }

    function hasUnitPricing(milestones) {
        return Array.isArray(milestones) && milestones.some(ms => {
            const unitLabel = (ms && ms.unit_label != null) ? String(ms.unit_label).trim() : '';
            const unitRate = (ms && ms.unit_rate != null && ms.unit_rate !== '') ? parseFloat(ms.unit_rate) : 0;
            return unitLabel !== '' || (unitRate > 0);
        });
    }

    function toNumber(val) {
        const num = parseFloat(val);
        return Number.isFinite(num) ? num : 0;
    }

    function getUnitPricingSummary(c) {
        const laborUnitLabel = String(c?.labor_unit_label ?? '').trim();
        const materialUnitLabel = String(c?.material_unit_label ?? '').trim();

        const milestones = Array.isArray(c.milestones) ? c.milestones : [];

        // Priority: current negotiated milestone rates, fallback to contract-level costs
        let laborUnitPrice = toNumber(c?.labor_cost);
        let materialUnitPrice = toNumber(c?.material_cost);

        // Find milestones corresponding to Labour/Materials by checking title
        const laborMs = milestones.find(m => {
            const t = (m.title || m.milestone_name || '').toLowerCase();
            return t.includes('labor') || t.includes('completion') || t.includes('service');
        });
        const matMs = milestones.find(m => {
            const t = (m.title || m.milestone_name || '').toLowerCase();
            return t.includes('material');
        });

        if (laborMs && laborMs.unit_rate != null && laborMs.unit_rate !== '') {
            laborUnitPrice = toNumber(laborMs.unit_rate);
        }
        if (matMs && matMs.unit_rate != null && matMs.unit_rate !== '') {
            materialUnitPrice = toNumber(matMs.unit_rate);
        }

        const items = [];
        if (laborUnitLabel || laborUnitPrice > 0) {
            items.push({ category: 'Labour', unitLabel: laborUnitLabel || 'units', unitPrice: laborUnitPrice });
        }
        if (materialUnitLabel || materialUnitPrice > 0) {
            items.push({ category: 'Materials', unitLabel: materialUnitLabel || 'units', unitPrice: materialUnitPrice });
        }

        return {
            isUnitBased: Boolean(laborUnitLabel || materialUnitLabel || items.length > 0),
            items
        };
    }

    function renderUnitPricingTable(summary) {
        const items = summary?.items || [];
        if (items.length === 0) return '<p class="preview-muted">No unit pricing defined.</p>';

        let html = '<h5 class="preview-schedule-title"><i class="fas fa-tags"></i> Unit Pricing</h5>';
        html += '<table class="preview-milestones-table"><thead><tr><th>Category</th><th>Unit Category</th><th>Unit Price</th></tr></thead><tbody>';
        items.forEach(item => {
            html += `<tr><td>${esc(item.category)}</td><td>${esc(item.unitLabel)}</td><td>${formatCurrency(item.unitPrice)}</td></tr>`;
        });
        html += '</tbody></table>';
        return html;
    }

    function computeBilledAmount(ms) {
        const agreedRate = parseFloat(ms?.unit_rate || 0);
        const actualRate = parseFloat(ms?.actual_unit_rate || 0);
        const unitRate = (actualRate > 0) ? actualRate : agreedRate;
        const actualQty = parseFloat(ms?.actual_quantity || 0);
        if (unitRate > 0 && actualQty > 0) return unitRate * actualQty;
        const stored = parseFloat(ms?.actual_amount ?? ms?.amount ?? ms?.payment_amount ?? 0);
        return Number.isFinite(stored) ? stored : 0;
    }

    function renderMilestonesTable(milestones, options) {
        let html = '<table class="preview-milestones-table">';
        html += '<thead><tr>';
        html += '<th>#</th><th>Milestone</th><th>Due Date</th><th>Status</th>';
        html += '</tr></thead><tbody>';

        (milestones || []).forEach((ms, i) => {
            const title = ms?.title || ms?.milestone_name || ('Milestone ' + (i + 1));
            const desc = ms?.description ? `<br><small style="color:var(--text-muted)">${esc(ms.description)}</small>` : '';
            const due = formatDate(ms?.due_date);
            const status = String(ms?.status || 'pending');
            const statusText = status.replace(/_/g, ' ');

            let actionHtml = esc(statusText);
            if (options && typeof options.renderMilestoneAction === 'function') {
                const customHtml = options.renderMilestoneAction(ms);
                if (customHtml) actionHtml = customHtml;
            }

            html += '<tr>';
            html += `<td>${i + 1}</td>`;
            html += `<td><strong>${esc(title)}</strong>${desc}</td>`;
            html += `<td>${due}</td>`;
            html += `<td>${actionHtml}</td>`;
            html += '</tr>';
        });

        html += '</tbody></table>';
        return html;
    }

    function renderPaymentSchedule(c, milestones) {
        const totalVal = parseFloat(c?.total_budget || c?.value || 0) || 0;
        const method = c?.payment_method || 'full_upfront';
        const unitBased = hasUnitPricing(milestones);

        let scheduleHTML = '<h5 class="preview-schedule-title"><i class="fas fa-receipt"></i> Payment Schedule</h5>';

        if ((method === 'milestone_based' || unitBased) && Array.isArray(milestones) && milestones.length > 0) {
            if (unitBased) {
                scheduleHTML += '<p class="preview-muted" style="margin-bottom:10px;">Unit-priced milestones are billed from verified actual units (and actual unit rates if provided).</p>';
                scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>Rate (per unit)</th></tr></thead><tbody>';
                milestones.forEach((ms, i) => {
                    const title = (ms?.title || ms?.milestone_name || '').toLowerCase();
                    let unitLabel = String(ms?.unit_label || '').trim();
                    const agreedRate = parseFloat(ms?.unit_rate || 0);
                    const actualRate = parseFloat(ms?.actual_unit_rate || 0);
                    let unitRate = (actualRate > 0) ? actualRate : agreedRate;

                    // Fallback comparison to contract level if milestone data is empty (consistent with summary section)
                    if (unitLabel === '' || unitRate === 0) {
                        if (title.includes('labor') || title.includes('completion') || title.includes('service')) {
                            if (unitLabel === '') unitLabel = String(c?.labor_unit_label || '').trim();
                            if (unitRate === 0) unitRate = toNumber(c?.labor_cost);
                        } else if (title.includes('material')) {
                            if (unitLabel === '') unitLabel = String(c?.material_unit_label || '').trim();
                            if (unitRate === 0) unitRate = toNumber(c?.material_cost);
                        }
                    }
                    if (unitLabel === '') unitLabel = 'units';

                    const rateStr = (unitRate > 0) ? (formatCurrency(unitRate) + ' / ' + esc(unitLabel)) : '—';
                    scheduleHTML += `<tr><td>${i + 1}</td><td>${esc(ms?.title || ms?.milestone_name || ('Milestone ' + (i + 1)))}</td><td>${rateStr}</td></tr>`;
                });
                scheduleHTML += '</tbody></table>';
                return scheduleHTML;
            }

            scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>%</th><th>Amount</th></tr></thead><tbody>';
            milestones.forEach((ms, i) => {
                const pct = parseFloat(ms?.payment_percentage || ms?.percentage || 0) || 0;
                const amt = parseFloat(ms?.payment_amount || ms?.amount || (totalVal * pct / 100)) || 0;
                scheduleHTML += `<tr><td>${i + 1}</td><td>${esc(ms?.title || ms?.milestone_name || ('Milestone ' + (i + 1)))}</td><td>${pct}%</td><td>${formatCurrency(amt)}</td></tr>`;
            });
            scheduleHTML += '</tbody></table>';
            return scheduleHTML;
        }

        const schedules = {
            full_upfront: [{ label: 'Full Payment Upfront', pct: 100 }],
            '50_50': [{ label: 'Upfront Payment', pct: 50 }, { label: 'On Completion', pct: 50 }],
            '30_70': [{ label: 'Advance Payment', pct: 30 }, { label: 'On Completion', pct: 70 }],
            completion: [{ label: 'Full Payment After Completion', pct: 100 }]
        };

        const items = schedules[method] || [{ label: 'Full Payment', pct: 100 }];
        scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>Payment</th><th>%</th><th>Amount</th></tr></thead><tbody>';
        items.forEach(item => {
            const amt = totalVal * (item.pct || 0) / 100;
            scheduleHTML += `<tr><td>${esc(item.label)}</td><td>${item.pct}%</td><td>${formatCurrency(amt)}</td></tr>`;
        });
        scheduleHTML += '</tbody></table>';
        return scheduleHTML;
    }

    function renderHTML(contract, options) {
        const c = contract || {};
        const opts = options || {};
        const milestones = Array.isArray(c.milestones) ? c.milestones : [];

        const statusKey = normalizeStatusClass(c.status || 'draft');
        const statusText = formatStatusText(c.status || 'draft');

        const contractDate = c.contract_date || c.created_at || c.sent_at || c.updated_at || '';
        const refText = c.contract_number || c.contract_id || c.id || '—';

        const clientName = ((c.customer_fname || '') + ' ' + (c.customer_lname || '')).trim() || (c.client?.name || c.client_name || '—');
        const clientDetails = [
            c.customer_email || c.client?.email || '',
            c.customer_phone || '',
            c.customer_district || c.client?.district || ''
        ].filter(Boolean).join(' | ') || (c.client?.address ? esc(c.client.address) : '—');

        const companyName = c.company_name || c.company?.name || '—';
        const companyDetails = [
            c.company_registration || c.company?.registration_no || '',
            c.company_email || '',
            c.company_phone || c.company?.contact || ''
        ].filter(Boolean).join(' | ') || (c.company?.address ? esc(c.company.address) : '—');

        const projectType = c.project_type || c.project_category || c.service_type || c.type || '—';

        const materialsLabels = {
            company: 'All materials supplied by the Contractor',
            client: 'All materials supplied by the Client',
            shared: 'Shared responsibility'
        };

        const budgetTypes = {
            fixed: 'Fixed Price',
            flexible: 'Flexible (±10%)'
        };

        const progress = parseInt(c.progress_percentage ?? c.progress ?? 0) || 0;
        const paymentLabel = opts.paymentLabel || formatPaymentMethod(c.payment_method);
        const isMilestoneBased = (opts.isMilestoneBased != null) ? !!opts.isMilestoneBased : (c.payment_method === 'milestone_based');

        const unitPricing = getUnitPricingSummary(c);
        const showUnitPricingOnly = unitPricing.isUnitBased;

        const hasScope = Boolean(c.scope_description || c.scope_inclusions || c.scope_exclusions || c.materials_responsibility);

        // Optional signature metadata (for print/download views)
        let signatureMeta = null;
        if (typeof c.customer_signature === 'string' && c.customer_signature.trim()) {
            try {
                const decoded = JSON.parse(c.customer_signature);
                if (decoded && typeof decoded === 'object') signatureMeta = decoded;
            } catch (_) {
                // ignore
            }
        }

        const hasSignatures = Boolean(
            (c.user_signature && String(c.user_signature).trim()) ||
            (c.company_signature && String(c.company_signature).trim()) ||
            c.signed_at || c.created_at ||
            (signatureMeta && signatureMeta.contract_hash)
        );

        const isFlexibleBudget = String(c.budget_type || '') === 'flexible';
        const budgetMin = (c.budget_min != null && c.budget_min !== '') ? parseFloat(c.budget_min) : null;
        const budgetMax = (c.budget_max != null && c.budget_max !== '') ? parseFloat(c.budget_max) : null;
        const showBudgetFlex = isFlexibleBudget && (budgetMin != null || budgetMax != null);

        return `
            <div class="contract-preview">
                <div class="preview-header">
                    <h2>CONSTRUCTION SERVICE AGREEMENT</h2>
                    <p class="preview-ref">Contract Reference: ${esc(refText)}</p>
                    <p class="preview-date">Date: <span>${formatDate(contractDate)}</span></p>
                    <div class="contract-status-badge ${statusKey}">${esc(statusText)}</div>
                </div>

                <div class="preview-section">
                    <h4>1. PARTIES TO THE CONTRACT</h4>
                    <div class="preview-parties">
                        <div>
                            <strong>First Party (Client):</strong>
                            <span>${esc(clientName)}</span><br>
                            <small>${esc(clientDetails)}</small>
                        </div>
                        <div>
                            <strong>Second Party (Contractor):</strong>
                            <span>${esc(companyName)}</span><br>
                            <small>${esc(companyDetails)}</small>
                        </div>
                    </div>
                </div>

                <div class="preview-section">
                    <h4>2. PROJECT OVERVIEW</h4>
                    <div class="preview-grid">
                        <div><strong>Title:</strong> <span>${esc(c.project_title || c.title || '—')}</span></div>
                        <div><strong>Reference:</strong> <span>${esc(c.project_reference || '—')}</span></div>
                        <div><strong>Location:</strong> <span>${esc(c.project_location || c.location || '—')}</span></div>
                        <div><strong>Type:</strong> <span>${esc(projectType)}</span></div>
                    </div>
                    <p class="preview-paragraph">${esc(c.project_description || c.description || '—')}</p>
                </div>

                <div class="preview-section" style="${hasScope ? '' : 'display:none;'}">
                    <h4>3. SCOPE OF WORK</h4>
                    <p class="preview-paragraph">${esc(c.scope_description || '—')}</p>
                    <div class="preview-grid" style="margin-top:10px;">
                        <div>
                            <strong>Inclusions:</strong>
                            <pre class="preview-pre">${esc(c.scope_inclusions || 'As per quotation')}</pre>
                        </div>
                        <div>
                            <strong>Exclusions:</strong>
                            <pre class="preview-pre">${esc(c.scope_exclusions || 'None specified')}</pre>
                        </div>
                    </div>
                    <p style="margin-top:10px;"><strong>Materials:</strong> <span>${esc(materialsLabels[c.materials_responsibility] || 'As per agreement')}</span></p>
                </div>

                <div class="preview-section">
                    <h4>4. PROJECT DURATION & MILESTONES</h4>
                    <div class="preview-grid cols-3">
                        <div><strong>Start:</strong> <span>${formatDate(c.start_date)}</span></div>
                        <div><strong>Completion:</strong> <span>${formatDate(c.end_date)}</span></div>
                        <div><strong>Progress:</strong> <span>${progress}%</span></div>
                    </div>
                    <div style="margin-top:10px;">
                        ${milestones.length > 0
                ? renderMilestonesTable(milestones, { isMilestoneBased, renderMilestoneAction: opts.renderMilestoneAction })
                : '<p class="preview-muted">No milestones defined.</p>'}
                    </div>
                </div>

                <div class="preview-section">
                    <h4>5. PRICING, PAYMENTS & DELAYS</h4>
                    ${showUnitPricingOnly
                ? `
                            <div class="preview-schedule" style="margin-top:12px;">
                                ${renderUnitPricingTable(unitPricing)}
                            </div>
                        `
                : `
                            <div class="preview-grid cols-3">
                                <div><strong>Contract Value:</strong> <span class="preview-value">${formatCurrency(c.total_budget ?? c.value ?? 0)}</span></div>
                                <div><strong>Budget Type:</strong> <span>${esc(budgetTypes[c.budget_type] || 'Fixed Price')}</span></div>
                                <div><strong>Payment Method:</strong> <span>${esc(paymentLabel || '—')}</span></div>
                            </div>
                            <div class="preview-grid" style="margin-top:10px;">
                                <div><strong>Amount Paid:</strong> <span>${formatCurrency(c.amount_paid || 0)}</span></div>
                                <div><strong>Remaining:</strong> <span>${formatCurrency(c.amount_pending ?? (c.total_budget ?? c.value ?? 0))}</span></div>
                            </div>

                            <div class="preview-schedule" style="margin-top:12px;">
                                ${renderPaymentSchedule(c, milestones)}
                            </div>
                        `
            }

                    <p style="margin-top:10px;"><strong>Late Payment:</strong> <span>${esc(c.late_payment_penalty || 'As per standard terms')}</span></p>
                </div>

                <div class="preview-section" style="${showBudgetFlex ? '' : 'display:none;'}">
                    <h4>5.1 BUDGET FLEXIBILITY</h4>
                    <div class="preview-grid" style="margin-top:10px;">
                        <div><strong>Minimum:</strong> <span class="preview-value">${budgetMin != null ? formatCurrency(budgetMin) : '—'}</span></div>
                        <div><strong>Maximum:</strong> <span class="preview-value">${budgetMax != null ? formatCurrency(budgetMax) : '—'}</span></div>
                    </div>
                    <p class="preview-paragraph" style="margin-top:10px;">
                        Final cost may vary within the allowed range to accommodate material price changes or necessary adjustments. All changes require your approval.
                    </p>
                </div>

                <div class="preview-section">
                    <h4>6. VARIATIONS & CHANGES</h4>
                    <p class="preview-paragraph">${esc(c.variation_clause
                ? 'Any change to scope, pricing, materials, or timeline must be approved in writing by both parties before execution.'
                : 'Variation control is not enabled for this contract.')}</p>
                </div>

                <div class="preview-section">
                    <h4>7. COMMUNICATION & DISPUTE RESOLUTION</h4>
                    <p><strong>Channel:</strong> <span>${esc(formatCommunicationChannel())}</span></p>
                    <p class="preview-paragraph">${esc(c.dispute_resolution || 'Disputes shall be resolved through mediation via the FixLanka platform.')}</p>
                </div>

                <div class="preview-section" style="${c.sent_to_customer ? '' : 'display:none;'}">
                    <h4>8. CUSTOMER RESPONSE</h4>
                    <div class="preview-grid">
                        <div><strong>Sent to Customer:</strong> <span>${esc(c.sent_to_customer ? ('Yes' + (c.sent_at ? ' — ' + formatDate(c.sent_at) : '')) : 'No')}</span></div>
                        <div><strong>Response:</strong> <span>${esc(String(c.customer_response || 'pending').replace(/_/g, ' '))}</span></div>
                    </div>
                </div>

                <div class="preview-section" style="${hasSignatures ? '' : 'display:none;'}">
                    <h4>9. ELECTRONIC SIGNATURES</h4>
                    <div class="preview-grid">
                        <div>
                            <strong>Client (Customer)</strong>
                            <span>${esc(clientName)}</span><br>
                            <small>${esc(c.customer_email || c.client?.email || '')}</small>
                        </div>
                        <div>
                            <strong>Contractor (Company)</strong>
                            <span>${esc(companyName)}</span><br>
                            <small>${esc(c.company_email || '')}</small>
                        </div>
                    </div>

                    <div class="preview-grid" style="margin-top:10px;">
                        <div>
                            <strong>Customer Signature</strong>
                            <span>${esc(c.user_signature || 'PENDING')}</span><br>
                            <small>Signed at: ${esc(c.signed_at ? formatDate(c.signed_at) : '—')}</small>
                        </div>
                        <div>
                            <strong>Company Signature</strong>
                            <span>${esc(c.company_signature || 'AUTO_GENERATED')}</span><br>
                            <small>Created at: ${esc(c.created_at ? formatDate(c.created_at) : '—')}</small>
                        </div>
                    </div>

                    ${signatureMeta && signatureMeta.contract_hash
                ? `<p style="margin-top:10px;"><strong>Document Hash (SHA-256):</strong> <span>${esc(signatureMeta.contract_hash)}</span></p>`
                : ''
            }

                    <p class="preview-paragraph" style="margin-top:10px;">By accepting this contract in the FixLanka system, the customer provides an electronic signature indicating agreement to the contract terms.</p>
                </div>
            </div>
        `;
    }

    window.ContractPreview = {
        renderHTML,
        computeBilledAmount,
        hasUnitPricing,
        formatCurrency,
        formatDate,
        esc
    };
})();
