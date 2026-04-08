/**
 * Customer Contracts Page
 * Fetches and displays contracts for the logged-in user
 */
(function () {
    'use strict';

    const API = '/2nd-Year-Group-Project/FixLanka/api/contracts.php';
    let allContracts = [];
    let currentFilter = '';
    let currentSearch = '';

    let currentContractMilestones = [];
    let currentReviewMilestoneId = null;

    document.addEventListener('DOMContentLoaded', init);

    function init() {
        loadContracts();
        bindFilters();
        bindModalClose();

        // Bind Proof Modal Close
        const proofOverlay = document.getElementById('proofReviewOverlay');
        if (proofOverlay) {
            proofOverlay.addEventListener('click', (e) => {
                if (e.target === proofOverlay) closeProofModal();
            });
        }
    }


    // =========================================
    // LOAD CONTRACTS
    // =========================================
    async function loadContracts() {
        const list = document.getElementById('contractsList');
        if (!list) return;

        list.innerHTML = `
            <div class="contracts-loading">
                <div class="spinner"></div>
                <p>Loading your contracts...</p>
            </div>`;

        try {
            const res = await fetch(`${API}?action=list`);
            const json = await res.json();

            if (!json.success) {
                list.innerHTML = `<div class="contracts-empty"><i class="fas fa-exclamation-circle"></i><h3>Error</h3><p>${json.message}</p></div>`;
                return;
            }

            allContracts = json.data || [];
            updateStats();
            renderList();
        } catch (err) {
            console.error('Load error:', err);
            list.innerHTML = `<div class="contracts-empty"><i class="fas fa-exclamation-circle"></i><h3>Connection Error</h3><p>Could not load contracts. Please try again.</p></div>`;
        }
    }

    // =========================================
    // UPDATE STATS
    // =========================================
    function updateStats() {
        const total = allContracts.length;
        const active = allContracts.filter(c => ['active', 'in_progress'].includes(c.status)).length;
        const pending = allContracts.filter(c => ['draft', 'pending_signature', 'pending'].includes(c.status)).length;
        const completed = allContracts.filter(c => c.status === 'completed').length;

        setText('statTotal', total);
        setText('statActive', active);
        setText('statPending', pending);
        setText('statCompleted', completed);
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }

    // =========================================
    // RENDER LIST
    // =========================================
    function renderList() {
        const list = document.getElementById('contractsList');
        if (!list) return;

        let filtered = allContracts;

        // Apply status filter
        if (currentFilter) {
            if (currentFilter === 'active') {
                filtered = filtered.filter(c => ['active', 'in_progress'].includes(c.status));
            } else if (currentFilter === 'pending') {
                filtered = filtered.filter(c => ['draft', 'pending_signature'].includes(c.status));
            } else {
                filtered = filtered.filter(c => c.status === currentFilter);
            }
        }

        // Apply search
        if (currentSearch) {
            const q = currentSearch.toLowerCase();
            filtered = filtered.filter(c =>
                (c.project_title || '').toLowerCase().includes(q) ||
                (c.contract_number || '').toLowerCase().includes(q) ||
                (c.company_name || '').toLowerCase().includes(q)
            );
        }

        if (filtered.length === 0) {
            list.innerHTML = `
                <div class="contracts-empty">
                    <i class="fas fa-file-contract"></i>
                    <h3>No contracts found</h3>
                    <p>${currentFilter || currentSearch ? 'Try changing your filters.' : 'You don\'t have any contracts yet.'}</p>
                </div>`;
            return;
        }

        list.innerHTML = filtered.map(c => renderCard(c)).join('');

        // Bind click
        list.querySelectorAll('.contract-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                openDetail(id);
            });
        });
    }

    function renderCard(c) {
        const statusLabel = formatStatus(c.status);
        const icon = getStatusIcon(c.status);
        const statusClass = (c.status || 'draft').replace(/ /g, '_');
        const amount = formatCurrency(c.total_budget);
        const date = c.contract_date ? formatDate(c.contract_date) : '—';

        return `
        <div class="contract-card" data-id="${c.contract_id}">
            <div class="cc-icon ${statusClass}">
                <i class="fas ${icon}"></i>
            </div>
            <div class="cc-body">
                <h3 class="cc-title">${esc(c.project_title || 'Untitled Contract')}</h3>
                <p class="cc-company"><i class="fas fa-building"></i> ${esc(c.company_name || '—')}</p>
                <div class="cc-meta">
                    <span><i class="fas fa-hashtag"></i> ${esc(c.contract_number || '—')}</span>
                    <span><i class="fas fa-calendar"></i> ${date}</span>
                    <span><i class="fas fa-map-marker-alt"></i> ${esc(c.project_location || '—')}</span>
                    ${c.total_milestones > 0 ? `<span><i class="fas fa-flag"></i> ${c.completed_milestones}/${c.total_milestones} milestones</span>` : ''}
                </div>
            </div>
            <div class="cc-right">
                <span class="cc-amount">${amount}</span>
                <span class="cc-status ${statusClass}">${statusLabel}</span>
            </div>
        </div>`;
    }

    // =========================================
    // OPEN DETAIL
    // =========================================
    async function openDetail(contractId) {
        const overlay = document.getElementById('contractDetailOverlay');
        const body = document.getElementById('contractDetailBody');
        if (!overlay || !body) return;

        overlay.classList.add('show');
        body.innerHTML = `<div class="contracts-loading"><div class="spinner"></div><p>Loading contract details...</p></div>`;

        try {
            const res = await fetch(`${API}?action=get&id=${contractId}`);
            const json = await res.json();

            if (!json.success) {
                body.innerHTML = `<p style="color:var(--danger);padding:20px;">Error: ${json.message}</p>`;
                return;
            }

            renderDetail(json.data);
        } catch (err) {
            console.error('Detail error:', err);
            body.innerHTML = `<p style="color:var(--danger);padding:20px;">Could not load contract details.</p>`;
        }
    }

    function renderDetail(c) {
        const body = document.getElementById('contractDetailBody');
        const headerText = document.getElementById('cdHeaderTitle');
        const headerSub = document.getElementById('cdHeaderSub');
        const footer = document.getElementById('cdFooter');

        if (headerText) headerText.textContent = c.project_title || 'Contract Details';
        if (headerSub) headerSub.textContent = c.contract_number || '';

        // Parse terms
        let terms = {};
        if (c.terms_parsed) terms = c.terms_parsed;
        else if (c.terms_conditions) {
            try { terms = JSON.parse(c.terms_conditions); } catch (e) { }
        }

        const progress = parseInt(c.progress_percentage) || 0;
        const paymentLabel = formatPaymentMethod(c.payment_method);
        const milestones = c.milestones || [];
        const isMilestoneBased = c.payment_method === 'milestone_based';

        body.innerHTML = `
            <!-- Progress -->
            <div class="cd-progress-wrapper">
                <div class="cd-progress-top">
                    <span>Project Progress</span>
                    <strong>${progress}%</strong>
                </div>
                <div class="cd-progress-track">
                    <div class="cd-progress-fill" style="width:${progress}%"></div>
                </div>
            </div>

            <!-- Section 1: Parties -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-users"></i> Parties</div>
                <div class="cd-info-grid">
                    <div class="cd-info-item">
                        <span class="cd-info-label">Company</span>
                        <span class="cd-info-value">${esc(c.company_name || '—')}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Registration No.</span>
                        <span class="cd-info-value">${esc(c.company_registration || '—')}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Company Contact</span>
                        <span class="cd-info-value">${esc(c.company_email || '—')}${c.company_phone ? ' · ' + esc(c.company_phone) : ''}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Customer</span>
                        <span class="cd-info-value">${esc((c.customer_fname || '') + ' ' + (c.customer_lname || ''))}</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Project Overview -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-building"></i> Project Overview</div>
                <div class="cd-info-grid">
                    <div class="cd-info-item">
                        <span class="cd-info-label">Project Title</span>
                        <span class="cd-info-value">${esc(c.project_title || '—')}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Reference</span>
                        <span class="cd-info-value">${esc(c.project_reference || '—')}</span>
                    </div>
                    <div class="cd-info-item full">
                        <span class="cd-info-label">Location</span>
                        <span class="cd-info-value">${esc(c.project_location || '—')}</span>
                    </div>
                    <div class="cd-info-item full">
                        <span class="cd-info-label">Description</span>
                        <span class="cd-info-value">${esc(c.project_description || '—')}</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Scope of Work -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-tasks"></i> Scope of Work</div>
                <div class="cd-info-grid">
                    <div class="cd-info-item full">
                        <span class="cd-info-label">Description</span>
                        <span class="cd-info-value">${esc(c.scope_description || '—')}</span>
                    </div>
                    <div class="cd-info-item full">
                        <span class="cd-info-label">Inclusions</span>
                        <span class="cd-info-value">${esc(c.scope_inclusions || '—')}</span>
                    </div>
                    <div class="cd-info-item full">
                        <span class="cd-info-label">Exclusions</span>
                        <span class="cd-info-value">${esc(c.scope_exclusions || '—')}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Standards</span>
                        <span class="cd-info-value">${esc(c.scope_standards || '—')}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Materials Responsibility</span>
                        <span class="cd-info-value">${capitalize(c.materials_responsibility || 'company')}</span>
                    </div>
                </div>
            </div>

            <!-- Section 4: Financial -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-money-bill-wave"></i> Payments</div>
                <div class="cd-info-grid">
                    <div class="cd-info-item">
                        <span class="cd-info-label">Contract Value</span>
                        <span class="cd-info-value highlight">${formatCurrency(c.total_budget)}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Payment Method</span>
                        <span class="cd-info-value">${paymentLabel}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Amount Paid</span>
                        <span class="cd-info-value">${formatCurrency(c.amount_paid)}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">Amount Pending</span>
                        <span class="cd-info-value">${formatCurrency(c.amount_pending)}</span>
                    </div>
                </div>
            </div>

            <!-- Section 5: Timeline & Milestones -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-calendar-alt"></i> Timeline & Milestones</div>
                <div class="cd-info-grid" style="margin-bottom:16px;">
                    <div class="cd-info-item">
                        <span class="cd-info-label">Start Date</span>
                        <span class="cd-info-value">${formatDate(c.start_date)}</span>
                    </div>
                    <div class="cd-info-item">
                        <span class="cd-info-label">End Date</span>
                        <span class="cd-info-value">${formatDate(c.end_date)}</span>
                    </div>
                </div>
                ${milestones.length > 0 ? renderMilestonesTable(milestones, isMilestoneBased) : '<p style="color:var(--text-medium);font-size:0.85rem;">No milestones defined.</p>'}
            </div>

            <!-- Section 6: Clauses & Terms -->
            <div class="cd-section">
                <div class="cd-section-header"><i class="fas fa-gavel"></i> Clauses & Terms</div>
                <div class="cd-terms-content">
                    ${renderTerms(c, terms)}
                </div>
            </div>
        `;

        // Footer action buttons
        if (footer) {
            const isPending = ['draft', 'pending_signature'].includes(c.status);

            if (isPending && c.sent_to_customer) {
                footer.innerHTML = `
                    <button class="cd-btn danger" onclick="respondContract(${c.contract_id}, 'rejected')"><i class="fas fa-times"></i> Decline</button>
                    <button class="cd-btn primary" onclick="respondContract(${c.contract_id}, 'accepted')"><i class="fas fa-check"></i> Accept Contract</button>
                `;
            } else if (c.undo_available) {
                // Calculate time remaining
                const deadline = new Date(c.undo_deadline);
                const now = new Date();
                const diff = Math.max(0, Math.floor((deadline - now) / 1000 / 60)); // minutes
                const hours = Math.floor(diff / 60);
                const mins = diff % 60;

                footer.innerHTML = `
                    <div style="flex: 1; display: flex; align-items: center; font-size: 0.85em; color: var(--text-medium);">
                        <i class="fas fa-stopwatch" style="margin-right: 6px; color: var(--warning);"></i> 
                        Undo available: ${hours}h ${mins}m remaining
                    </div>
                    <button class="cd-btn danger" onclick="undoContract(${c.contract_id})" title="Cancel contract within 24 hours of acceptance"><i class="fas fa-undo"></i> Undo Contract</button>
                    <button class="cd-btn secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>
                 `;
            } else {
                footer.innerHTML = `<button class="cd-btn secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>`;
            }
        }
    }

    function renderMilestonesTable(milestones, isMilestoneBased) {
        currentContractMilestones = milestones; // Store globally

        let html = `<table class="cd-milestones-table">
            <thead><tr>
                <th>#</th>
                <th>Milestone</th>
                <th>Due Date</th>
                ${isMilestoneBased ? '<th>Amount</th>' : ''}
                <th>Status</th>
                <th>Action</th>
            </tr></thead><tbody>`;

        milestones.forEach(ms => {
            const statusClass = (ms.status || 'pending').replace(/ /g, '_');

            let actionBtn = '—';
            if (ms.status === 'submitted') {
                actionBtn = `<button class="cd-btn secondary small" onclick="openProofModal(${ms.milestone_id})" style="padding: 4px 10px; font-size: 0.8rem;"><i class="fas fa-eye"></i> Review</button>`;
            } else if (ms.status === 'approved') {
                actionBtn = '<span class="text-success"><i class="fas fa-check"></i> Paid</span>';
            }

            html += `<tr>
                <td>${ms.milestone_number}</td>
                <td><strong>${esc(ms.title)}</strong>${ms.description ? '<br><small style="color:var(--text-medium)">' + esc(ms.description) + '</small>' : ''}</td>
                <td>${formatDate(ms.due_date)}</td>
                ${isMilestoneBased ? `<td>${formatCurrency(ms.amount)}</td>` : ''}
                <td><span class="ms-status-badge ${statusClass}">${formatStatus(ms.status)}</span></td>
                <td>${actionBtn}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        return html;
    }

    function renderTerms(c, terms) {
        let parts = [];

        // Late payment
        const lp = c.late_payment_penalty || (terms.delays && terms.delays.late_payment_penalty) || '';
        if (lp) parts.push(`<p><strong>Late Payment:</strong> ${esc(lp)}</p>`);

        // Pause work
        const pw = c.pause_work_clause ?? (terms.delays && terms.delays.pause_work_clause);
        if (pw) parts.push(`<p><strong>Pause Work Clause:</strong> Company may pause work if payment is overdue beyond 14 days.</p>`);

        // Time extension
        const te = c.time_extension_clause ?? (terms.delays && terms.delays.time_extension_clause);
        if (te) parts.push(`<p><strong>Time Extension:</strong> Deadline extensions may apply for force majeure events.</p>`);

        // Variation
        if (c.variation_clause) parts.push(`<p><strong>Variation Clause:</strong> Changes to scope must be agreed in writing with cost adjustments.</p>`);

        // Communication
        const comm = c.communication_channel || (terms.communication && terms.communication.channel) || '';
        if (comm) parts.push(`<p><strong>Communication:</strong> ${capitalize(comm)}</p>`);

        // Dispute
        const disp = c.dispute_resolution || (terms.communication && terms.communication.dispute_resolution) || '';
        if (disp) parts.push(`<p><strong>Dispute Resolution:</strong> ${esc(disp)}</p>`);

        // Warranty
        const warranty = terms.warranty_period || '';
        if (warranty) parts.push(`<p><strong>Warranty:</strong> ${esc(warranty)}</p>`);

        // Additional
        const additional = terms.additional_terms || '';
        if (additional) parts.push(`<p><strong>Additional Terms:</strong> ${esc(additional)}</p>`);

        return parts.length > 0 ? parts.join('') : '<p>No additional terms specified.</p>';
    }

    // =========================================
    // FILTERS
    // =========================================
    function bindFilters() {
        const statusSel = document.getElementById('custStatusFilter');
        const searchInput = document.getElementById('custSearchInput');

        if (statusSel) {
            statusSel.addEventListener('change', () => {
                currentFilter = statusSel.value;
                renderList();
            });
        }

        if (searchInput) {
            let timer;
            searchInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    currentSearch = searchInput.value.trim();
                    renderList();
                }, 300);
            });
        }
    }

    // =========================================
    // MODAL CLOSE
    // =========================================
    function bindModalClose() {
        const overlay = document.getElementById('contractDetailOverlay');
        const closeBtn = document.getElementById('cdCloseBtn');

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDetail);
        }

        if (overlay) {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) closeDetail();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDetail();
        });
    }

    function closeDetail() {
        const overlay = document.getElementById('contractDetailOverlay');
        if (overlay) overlay.classList.remove('show');
    }

// =========================================
// RESPOND TO CONTRACT
// =========================================
window.respondContract = async function (contractId, response) {
    const label = response === 'accepted' ? 'accept' : 'decline';
    if (response === 'accepted') {
        const msg =
            "Electronic Signature Confirmation\n\n" +
            "By clicking OK, you confirm you have read and agree to the contract terms, and you electronically sign this contract in FixLanka.";
        if (!confirm(msg)) return;
    } else {
        if (!confirm(`Are you sure you want to ${label} this contract?`)) return;
    }

    try {
        const res = await fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'respond',
                contract_id: contractId,
                response: response,
                esign_consent: response === 'accepted'
            })
        });
        const json = await res.json();

        if (json.success) {
            alert(`Contract ${label}ed successfully!`);
            closeDetail();
            loadContracts();
        } else {
            alert('Error: ' + json.message);
        }
    } catch (err) {
        console.error('Respond error:', err);
        alert('Could not process your response. Please try again.');
    }
};
// =========================================
// UNDO CONTRACT
// =========================================
window.undoContract = async function (contractId) {
    const reason = prompt("Please provide a reason for cancelling this contract:");
    if (reason === null) return; // User cancelled prompt

    if (reason.trim() === "") {
        alert("Please provide a reason.");
        return;
    }

    if (!confirm("Are you sure you want to cancel this contract? This action cannot be undone and the job request will be reopened.")) return;

    try {
        // Show loading state
        const btn = document.querySelector(`button[onclick="undoContract(${contractId})"]`);
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
        }

        const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts/undo', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ contract_id: contractId, reason: reason })
        });
        const json = await res.json();

        if (json.success) {
            alert('Contract cancelled successfully.');
            closeContractDetail();
            // Reload to refresh lists
            loadContracts();
            updateStats(); // Refresh stats too
        } else {
            alert('Error: ' + json.message);
            if (btn) {
                btn.innerHTML = '<i class="fas fa-undo"></i> Undo Contract';
                btn.disabled = false;
            }
        }
    } catch (err) {
        console.error('Undo error:', err);
        alert('Could not cancel contract. Please try again.');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-undo"></i> Undo Contract';
            btn.disabled = false;
        }
    }
};

window.closeContractDetail = closeDetail;

// =========================================
// HELPERS
// =========================================
function formatCurrency(val) {
    const num = parseFloat(val) || 0;
    return 'LKR ' + num.toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(d) {
    if (!d) return '—';
    const date = new Date(d);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatStatus(s) {
    if (!s) return 'Draft';
    return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

function getStatusIcon(s) {
    const map = {
        'active': 'fa-check-circle',
        'in_progress': 'fa-spinner',
        'pending_signature': 'fa-pen',
        'draft': 'fa-file-alt',
        'completed': 'fa-trophy',
        'terminated': 'fa-ban',
        'disputed': 'fa-exclamation-triangle'
    };
    return map[s] || 'fa-file-contract';
}

function formatPaymentMethod(m) {
    const map = {
        'full_upfront': 'Full Upfront',
        'milestone_based': 'Milestone-Based',
        '50_50': '50/50 Split',
        '30_70': '30/70 Split',
        'completion': 'On Completion'
    };
    return map[m] || capitalize(m || '');
}

// =========================================
// PROOF REVIEW
// =========================================
window.openProofModal = function (milestoneId) {
    const ms = currentContractMilestones.find(m => m.milestone_id == milestoneId);
    if (!ms) return;

    currentReviewMilestoneId = milestoneId;

    const overlay = document.getElementById('proofReviewOverlay');
    const title = document.getElementById('proofPhaseTitle');
    const body = document.getElementById('proofReviewBody');

    if (title) title.textContent = ms.title;

    // Parse proof files
    let filesHtml = '<p>No files attached.</p>';
    try {
        if (ms.proof_files) {
            const files = JSON.parse(ms.proof_files);
            if (files && files.length > 0) {
                filesHtml = '<ul class="proof-files-list">';
                files.forEach(path => {
                    const name = path.split('/').pop();
                    filesHtml += `<li><a href="/2nd-Year-Group-Project/FixLanka/${path}" target="_blank"><i class="fas fa-file-download"></i> ${esc(name)}</a></li>`;
                });
                filesHtml += '</ul>';
            }
        }
    } catch (e) {
        console.error('JSON Parse error', e);
    }

    body.innerHTML = `
        <div class="proof-section">
            <h4><i class="fas fa-align-left"></i> Description of Work</h4>
            <div class="proof-desc">${esc(ms.proof_of_work || 'No description provided.')}</div>
        </div>
        <div class="proof-section">
            <h4><i class="fas fa-paperclip"></i> Attached Files</h4>
            ${filesHtml}
        </div>
        <div class="proof-section">
            <h4><i class="fas fa-info-circle"></i> Verification Info</h4>
            <p class="text-small">By approving this phase, the funds (${formatCurrency(ms.amount)}) will be released from Escrow to the Company. This action cannot be undone.</p>
        </div>
    `;

    overlay.classList.add('show');
};

window.closeProofModal = function () {
    const overlay = document.getElementById('proofReviewOverlay');
    if (overlay) overlay.classList.remove('show');
    currentReviewMilestoneId = null;
};

window.verifyMilestoneCurrent = async function (action) {
    if (!currentReviewMilestoneId) return;

    const actionText = action === 'approve' ? 'APPROVE and RELEASE PAYMENT' : 'REJECT';
    if (!confirm(`Are you sure you want to ${actionText} for this phase?`)) return;

    let feedback = '';
    if (action === 'reject') {
        feedback = prompt("Please provide a reason for rejection (required):");
        if (!feedback || !feedback.trim()) {
            alert("Rejection reason is required.");
            return;
        }
    }

    try {
        // Show loading
        const body = document.getElementById('proofReviewBody');
        body.innerHTML = '<div class="contracts-loading"><div class="spinner"></div><p>Processing verification...</p></div>';

        const formData = new FormData();
        formData.append('milestone_id', currentReviewMilestoneId);
        formData.append('action', action);
        if (feedback) formData.append('feedback', feedback);

        // Call API with action=verify_phase in URL to route correctly
        const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/projects.php?action=verify_phase', {
            method: 'POST',
            body: formData
        });

        const json = await res.json();

        if (json.success) {
            alert(`Phase ${action}ed successfully.`);
            closeProofModal();
            closeContractDetail(); // Close parent modal too to refresh
            loadContracts(); // Refresh list
        } else {
            alert('Error: ' + json.message);
            // Reload modal content if failed (optional, or just close)
            closeProofModal();
        }

    } catch (err) {
        console.error(err);
        alert('Communication error. Please try again.');
        closeProofModal();
    }
}

function capitalize(s) {
    return s ? s.charAt(0).toUpperCase() + s.slice(1).replace(/_/g, ' ') : '';
}

function esc(s) {
    if (!s) return '';
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
}
})();
