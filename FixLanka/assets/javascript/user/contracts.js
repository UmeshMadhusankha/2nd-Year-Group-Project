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
    let lastOpenedContractDetail = null;


    document.addEventListener('DOMContentLoaded', () => {
        init();
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.card-action-more') && !e.target.closest('.card-action-menu')) {
                document.querySelectorAll('.card-action-menu.active').forEach(m => {
                    m.classList.remove('active');
                    m.classList.remove('is-fixed');
                });
            }
        });

        // Close menus on scroll to prevent misalignment with fixed positioning
        window.addEventListener('scroll', closeAllMenus, true);
        const list = document.getElementById('contractsList');
        if (list) list.addEventListener('scroll', closeAllMenus, true);
    });

    function closeAllMenus() {
        document.querySelectorAll('.card-action-menu.active').forEach(m => {
            m.classList.remove('active');
            m.classList.remove('is-fixed');
        });
    }

    function updateBodyScrollLock() {
        const detailOverlay = document.getElementById('contractDetailOverlay');
        const proofOverlay = document.getElementById('proofReviewOverlay');
        const chatOverlay = document.getElementById('chatModalOverlay');
        const adjustOverlay = document.getElementById('contractAdjustOverlay');

        const anyOpen =
            (!!detailOverlay && detailOverlay.classList.contains('show')) ||
            (!!proofOverlay && proofOverlay.classList.contains('show')) ||
            (!!chatOverlay && chatOverlay.classList.contains('active')) ||
            (!!adjustOverlay && adjustOverlay.classList.contains('show'));

        document.body.classList.toggle('modal-open', anyOpen);
    }

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
        const terminated = allContracts.filter(c => ['terminated', 'disputed'].includes(c.status)).length;

        setText('statTotal', total);
        setText('statActive', active);
        setText('statPending', pending);
        setText('statCompleted', completed);
        setText('statTerminated', terminated);
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
            } else if (currentFilter === 'terminated') {
                filtered = filtered.filter(c => ['terminated', 'disputed'].includes(c.status));
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

        // Bind click for row + action buttons
        list.querySelectorAll('.contract-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.dataset.id;
                openDetail(id);
            });

            card.querySelectorAll('.card-action-btn, .card-menu-item').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const action = btn.dataset.action;
                    const parentCard = btn.closest('.contract-card');
                    const id = parentCard ? parentCard.dataset.id : null;
                    if (!id) return;

                    if (action === 'more') {
                        document.querySelectorAll('.card-action-menu.active').forEach(m => {
                            if (m.id !== `cardMenu-${id}`) {
                                m.classList.remove('active');
                                m.classList.remove('is-fixed');
                            }
                        });

                        const menu = document.getElementById(`cardMenu-${id}`);
                        if (menu) {
                            const isOpening = !menu.classList.contains('active');
                            if (isOpening) {
                                const rect = btn.getBoundingClientRect();
                                menu.classList.add('is-fixed');
                                menu.style.top = (rect.bottom + 5) + 'px';
                                // Align right edge of menu with right edge of button
                                menu.style.left = 'auto';
                                menu.style.right = (window.innerWidth - rect.right) + 'px';
                            } else {
                                menu.classList.remove('is-fixed');
                            }
                            menu.classList.toggle('active');
                        }
                        return;
                    }

                    const current = allContracts.find(x => String(x.contract_id) === String(id)) || {};

                    if (action === 'view') {
                        openDetail(id);
                        return;
                    }
                    if (action === 'chat') {
                        if (typeof window.openContractChat === 'function') {
                            window.openContractChat(
                                Number(id),
                                current.company_name || 'Chat',
                                current.contract_number || ''
                            );
                        }
                        return;
                    }
                    if (action === 'request') {
                        if (typeof window.requestContractAdjustments === 'function') {
                            window.requestContractAdjustments(Number(id));
                        }
                        return;
                    }
                    if (action === 'accept') {
                        if (typeof window.respondContract === 'function') {
                            window.respondContract(Number(id), 'accepted');
                        }
                        return;
                    }
                    if (action === 'decline') {
                        if (typeof window.respondContract === 'function') {
                            window.respondContract(Number(id), 'rejected');
                        }
                        return;
                    }
                    if (action === 'undo') {
                        if (typeof window.undoContract === 'function') {
                            window.undoContract(Number(id));
                        }
                        return;
                    }
                });
            });
        });
    }

    function renderCard(c) {
        const statusClass = normalizeStatusClass(c.status);
        const statusText = formatStatusText(c.status);
        const statusIcon = getStatusIcon(c.status);

        const companyName = c.company_name || '—';
        const initials = getInitials(companyName);

        const formattedValue = formatCurrency((c.value !== undefined && c.value !== null && c.value !== '') ? c.value : c.total_budget);
        const laborPerLabel = resolveQuotationPerLabel(c.labor_unit_label, 'labor');
        const materialPerLabel = resolveQuotationPerLabel(c.material_unit_label, 'material');
        const laborPriceText = formatUnitPrice(c.labor_cost);
        const materialPriceText = formatUnitPrice(c.material_cost);

        const hasLaborOrMaterialPrice = laborPriceText !== null || materialPriceText !== null;
        const valueBlock = hasLaborOrMaterialPrice
            ? `
                <div class="card-unit-price-block" aria-label="Unit price breakdown">
                    ${laborPriceText ? `<div class="card-unit-price-item"><span class="card-unit-price-label">${esc(formatCostLabel('Labor', laborPerLabel))}</span><span class="card-unit-price-value">${laborPriceText}</span></div>` : ''}
                    ${materialPriceText ? `<div class="card-unit-price-item"><span class="card-unit-price-label">${esc(formatCostLabel('Material', materialPerLabel))}</span><span class="card-unit-price-value">${materialPriceText}</span></div>` : ''}
                </div>
            `
            : `<div class="card-value-badge">${formattedValue}</div>`;
        const startDate = c.start_date ? formatDate(c.start_date) : '—';
        const endDate = c.end_date ? formatDate(c.end_date) : '—';
        const paymentLabel = formatPaymentMethod(c.payment_method);

        const progress = getContractProgress(c);
        const daysInfo = getDaysInfo(c.start_date, c.end_date, c.status);

        const isPendingSignature = ['draft', 'pending_signature'].includes(String(c.status || '')) && !!c.sent_to_customer;

        const actions = buildCustomerCardActions({
            contractId: c.contract_id,
            isPendingSignature,
            canUndo: !!c.undo_available,
            unreadCount: parseInt(c.unread_messages || c.unread_count || 0)
        });

        return `
        <div class="contract-card" data-id="${c.contract_id}" data-status="${mapCardStatus(c.status)}">
            <div class="card-top-row">
                <div class="card-type-badge">Contract</div>
                <div class="card-status-badge ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                    <span>${esc(statusText)}</span>
                </div>
            </div>

            <div class="card-title-section">
                <div class="card-title-row">
                    <h3 class="card-title">${esc(c.project_title || 'Untitled Contract')}</h3>
                    <div class="card-status-inline card-status-badge ${statusClass}">
                        <i class="fas ${statusIcon}"></i>
                        <span>${esc(statusText)}</span>
                    </div>
                </div>
                <span class="card-contract-number">${esc(c.contract_number || '—')}</span>
            </div>

            <div class="card-client-row">
                <div class="card-client-avatar">${esc(initials)}</div>
                <div class="card-client-info">
                    <span class="card-client-name">${esc(companyName)}</span>
                    <span class="card-client-email">${esc(c.project_location || '')}</span>
                </div>
                ${valueBlock}
            </div>

            <div class="card-meta-grid">
                <div class="card-meta-item">
                    <i class="fas fa-calendar-plus"></i>
                    <div>
                        <span class="meta-label">Start</span>
                        <span class="meta-value">${startDate}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <span class="meta-label">End</span>
                        <span class="meta-value">${endDate}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <span class="meta-label">Payment</span>
                        <span class="meta-value">${esc(paymentLabel)}</span>
                    </div>
                </div>
                <div class="card-meta-item">
                    <i class="fas fa-flag"></i>
                    <div>
                        <span class="meta-label">Milestones</span>
                        <span class="meta-value">${c.total_milestones > 0 ? `${c.completed_milestones}/${c.total_milestones}` : '—'}</span>
                    </div>
                </div>
            </div>



            <div class="card-actions-row">
                ${actions}
            </div>
        </div>`;
    }

    function normalizeStatusClass(status) {
        return String(status || 'draft').toLowerCase().replace(/_/g, '-');
    }

    function formatStatusText(status) {
        const s = String(status || 'draft');
        const map = {
            draft: 'Draft',
            pending: 'Pending',
            pending_signature: 'Awaiting Signature',
            active: 'Active',
            in_progress: 'In Progress',
            milestone_pending: 'Milestone Pending',
            completed: 'Completed',
            terminated: 'Terminated',
            disputed: 'Disputed'
        };
        return map[s] || formatStatus(s);
    }

    function getInitials(name) {
        const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
        if (parts.length === 0) return '—';
        const letters = parts.slice(0, 2).map(p => p[0]).join('');
        return letters.toUpperCase();
    }

    function getContractProgress(c) {
        const direct = Number(c.progress_percentage);
        if (!Number.isNaN(direct) && direct >= 0) {
            return Math.max(0, Math.min(100, Math.round(direct)));
        }
        const total = Number(c.total_milestones || 0);
        const done = Number(c.completed_milestones || 0);
        if (total > 0) return Math.max(0, Math.min(100, Math.round((done / total) * 100)));
        return 0;
    }

    function getDaysInfo(startDate, endDate, status) {
        if (!endDate) return { text: 'No deadline', icon: 'fa-infinity' };
        const s = String(status || '').toLowerCase();
        if (s === 'completed') return { text: 'Completed', icon: 'fa-check' };
        if (s === 'terminated' || s === 'disputed') return { text: 'Closed', icon: 'fa-ban' };

        const now = new Date();
        const end = new Date(endDate);
        if (Number.isNaN(end.getTime())) return { text: 'No deadline', icon: 'fa-infinity' };

        const diffDays = Math.ceil((end - now) / (1000 * 60 * 60 * 24));
        if (diffDays < 0) return { text: `${Math.abs(diffDays)}d overdue`, icon: 'fa-exclamation-triangle' };
        if (diffDays === 0) return { text: 'Due today', icon: 'fa-bell' };
        if (diffDays <= 7) return { text: `${diffDays}d remaining`, icon: 'fa-clock' };
        return { text: `${diffDays}d remaining`, icon: 'fa-clock' };
    }

    function buildCustomerCardActions({ contractId, isPendingSignature, canUndo, unreadCount }) {
        let html = '';

        const unreadIndicator = unreadCount > 0
            ? '<span class="chat-unread-dot" style="position: absolute; top: -2px; right: -2px; width: 12px; height: 12px; background: #ef4444; border: 2px solid white; border-radius: 50%; z-index: 10;"></span>'
            : '';

        html += `
            <button type="button" class="card-action-btn card-action-chat" data-action="chat" title="Chat" aria-label="Chat" style="position:relative">
                <i class="fas fa-comments"></i>
                ${unreadIndicator}
            </button>
        `;

        html += `
            <button type="button" class="card-action-btn card-action-more" data-action="more" title="More Options">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        `;

        html += `<div class="card-action-menu" id="cardMenu-${contractId}">`;

        html += `<a class="card-menu-item" data-action="request"><i class="fas fa-pen"></i> Request Adjustments</a>`;

        if (isPendingSignature) {
            html += `<a class="card-menu-item" data-action="accept" style="color:var(--success)"><i class="fas fa-check"></i> Accept Contract</a>`;
            html += `<a class="card-menu-item" data-action="decline" style="color:var(--danger)"><i class="fas fa-times"></i> Decline Contract</a>`;
        } else if (canUndo) {
            html += `<a class="card-menu-item" data-action="undo" style="color:var(--warning)"><i class="fas fa-undo"></i> Undo Contract</a>`;
        }
        html += `</div>`;
        return html;
    }

    function mapCardStatus(status) {
        const s = String(status || 'draft');
        if (['draft', 'pending_signature', 'pending'].includes(s)) return 'pending';
        if (['active', 'in_progress'].includes(s)) return 'active';
        if (s === 'completed') return 'completed';
        if (['terminated', 'disputed'].includes(s)) return 'cancelled';
        return 'pending';
    }

    function mapStatusLabel(status) {
        const s = String(status || 'draft');
        if (['draft', 'pending_signature', 'pending'].includes(s)) return 'Pending';
        if (['active', 'in_progress'].includes(s)) return 'Active';
        if (s === 'completed') return 'Completed';
        if (['terminated', 'disputed'].includes(s)) return 'Terminated';
        return formatStatus(s);
    }

    // =========================================
    // OPEN DETAIL
    // =========================================
    async function openDetail(contractId) {
        const overlay = document.getElementById('contractDetailOverlay');
        const body = document.getElementById('contractDetailBody');
        if (!overlay || !body) return;

        overlay.classList.add('show');
        updateBodyScrollLock();
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
        lastOpenedContractDetail = c;
        const body = document.getElementById('contractDetailBody');
        const headerText = document.getElementById('cdHeaderTitle');
        const headerSub = document.getElementById('cdHeaderSub');
        const footer = document.getElementById('cdFooter');

        if (headerText) headerText.textContent = 'Contract Details';
        if (headerSub) {
            const ref = c.contract_number || (c.contract_id ? ('Contract #' + c.contract_id) : '') || '';
            const company = c.company_name || '';
            headerSub.textContent = [ref, company].filter(Boolean).join(' • ');
        }

        // Parse terms
        let terms = {};
        if (c.terms_parsed) terms = c.terms_parsed;
        else if (c.terms_conditions) {
            try { terms = JSON.parse(c.terms_conditions); } catch (e) { }
        }

        const milestones = c.milestones || [];
        const isMilestoneBased = c.payment_method === 'milestone_based';

        const shared = window.ContractPreview;
        if (shared && typeof shared.renderHTML === 'function') {
            body.innerHTML = shared.renderHTML(c, {
                isMilestoneBased,
                paymentLabel: formatPaymentMethod(c.payment_method),
                renderMilestoneAction: (ms) => {
                    const msStatus = String(ms?.status || 'pending');
                    if (msStatus === 'submitted') {
                        return `<button class="action-btn secondary small" onclick="openProofModal(${ms.milestone_id})"><i class="fas fa-eye"></i> Review</button>`;
                    }
                    if (msStatus === 'approved' || msStatus === 'paid') {
                        return '<span class="text-success"><i class="fas fa-check"></i> Paid</span>';
                    }
                    return '—';
                }
            });
        } else {
            // Fallback to legacy renderer
            const progress = parseInt(c.progress_percentage) || 0;
            const paymentLabel = formatPaymentMethod(c.payment_method);
            body.innerHTML = renderLegalContractPreview(c, {
                terms,
                progress,
                paymentLabel,
                milestones,
                isMilestoneBased
            });
        }

        // Footer action buttons
        if (footer) {
            const isPending = ['draft', 'pending_signature'].includes(c.status);

            const chatBtn = `
                <button class="btn-secondary" onclick="openContractChat(${c.contract_id}, '${escAttr(c.company_name || 'Chat')}', '${escAttr(c.contract_number || '')}')">
                    <i class="fas fa-comments"></i> Chat
                </button>
            `;

            const changeReqBtn = `
                <button class="btn-secondary" onclick="requestContractAdjustments(${c.contract_id})">
                    <i class="fas fa-pen"></i> Request Adjustments
                </button>
            `;

            if (isPending && c.sent_to_customer) {
                footer.innerHTML = `
                    ${chatBtn}
                    ${changeReqBtn}
                    <button class="action-btn danger small" onclick="respondContract(${c.contract_id}, 'rejected')"><i class="fas fa-times"></i> Decline</button>
                    <button class="btn-primary" onclick="respondContract(${c.contract_id}, 'accepted')"><i class="fas fa-check"></i> Accept Contract</button>
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
                    ${chatBtn}
                    <button class="action-btn danger small" onclick="undoContract(${c.contract_id})" title="Cancel contract within 24 hours of acceptance"><i class="fas fa-undo"></i> Undo Contract</button>
                    <button class="btn-secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>
                 `;
            } else {
                // Active / completed / terminated etc.
                footer.innerHTML = `
                    ${chatBtn}
                    ${changeReqBtn}
                    <button class="btn-secondary" onclick="closeContractDetail()"><i class="fas fa-times"></i> Close</button>
                `;
            }
        }
    }

    function renderLegalContractPreview(c, ctx) {
        const terms = ctx && ctx.terms ? ctx.terms : {};
        const progress = ctx && typeof ctx.progress !== 'undefined' ? ctx.progress : (parseInt(c.progress_percentage) || 0);
        const paymentLabel = ctx && ctx.paymentLabel ? ctx.paymentLabel : formatPaymentMethod(c.payment_method);
        const milestones = ctx && ctx.milestones ? ctx.milestones : (c.milestones || []);
        const isMilestoneBased = ctx && typeof ctx.isMilestoneBased !== 'undefined' ? ctx.isMilestoneBased : (c.payment_method === 'milestone_based');

        const statusKey = normalizeStatusClass(c.status || 'draft');
        const statusText = formatStatusText(c.status || 'draft');

        const contractDate = c.contract_date || c.created_at || c.sent_at || c.updated_at || '';
        const refText = c.contract_number || c.contract_id || c.id || '—';

        const clientName = ((c.customer_fname || '') + ' ' + (c.customer_lname || '')).trim() || '—';
        const clientDetails = [
            c.customer_email || '',
            c.customer_phone || '',
            c.customer_district || ''
        ].filter(Boolean).join(' | ') || '—';

        const companyName = c.company_name || '—';
        const companyDetails = [
            c.company_registration || '',
            c.company_email || '',
            c.company_phone || ''
        ].filter(Boolean).join(' | ') || '—';

        const projectType = c.project_type || c.project_category || c.service_type || '—';

        const materialsLabels = {
            company: 'All materials supplied by the Contractor',
            client: 'All materials supplied by the Client',
            shared: 'Shared responsibility'
        };

        const budgetTypes = {
            fixed: 'Fixed Price',
            flexible: 'Flexible (±10%)'
        };

        const hasScope = Boolean(c.scope_description || c.scope_inclusions || c.scope_exclusions || c.materials_responsibility);

        const hasUnitMilestones = Array.isArray(milestones) && milestones.some(ms => {
            const ul = (ms && ms.unit_label != null) ? String(ms.unit_label).trim() : '';
            const ur = (ms && ms.unit_rate != null && ms.unit_rate !== '') ? parseFloat(ms.unit_rate) : 0;
            return ul !== '' || (ur > 0);
        });
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
                        <div><strong>Title:</strong> <span>${esc(c.project_title || '—')}</span></div>
                        <div><strong>Reference:</strong> <span>${esc(c.project_reference || '—')}</span></div>
                        <div><strong>Location:</strong> <span>${esc(c.project_location || '—')}</span></div>
                        <div><strong>Type:</strong> <span>${esc(projectType)}</span></div>
                    </div>
                    <p class="preview-paragraph">${esc(c.project_description || '—')}</p>
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
                        ${milestones.length > 0 ? renderPreviewMilestonesTable(milestones, isMilestoneBased) : '<p class="preview-muted">No milestones defined.</p>'}
                    </div>
                </div>

                <div class="preview-section">
                    <h4>5. PRICING, PAYMENTS & DELAYS</h4>
                    <div class="preview-grid cols-3">
                        <div><strong>Contract Value:</strong> <span class="preview-value">${formatCurrency(c.total_budget)}</span></div>
                        <div><strong>Budget Type:</strong> <span>${esc(budgetTypes[c.budget_type] || 'Fixed Price')}</span></div>
                        <div><strong>Payment Method:</strong> <span>${esc(paymentLabel || '—')}</span></div>
                    </div>
                    <div class="preview-grid" style="margin-top:10px;">
                        <div><strong>Amount Paid:</strong> <span>${formatCurrency(c.amount_paid)}</span></div>
                        <div><strong>Remaining:</strong> <span>${formatCurrency(c.amount_pending)}</span></div>
                    </div>

                    <div class="preview-schedule" style="margin-top:12px;">
                        ${renderPaymentSchedulePreview(c, milestones)}
                    </div>

                    ${hasUnitMilestones ? `
                        <p class="preview-paragraph" style="margin-top:10px;">
                            <strong>Unit-priced settlement:</strong> final billed amounts are calculated from submitted actual units and (if applicable) actual unit rates, then verified by you before payment.
                        </p>` : ''}

                    <p style="margin-top:10px;"><strong>Late Payment:</strong> <span>${esc(c.late_payment_penalty || (terms.delays && terms.delays.late_payment_penalty) || 'As per standard terms')}</span></p>
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
                    <p class="preview-paragraph">${esc(c.variation_clause ? 'Any change to scope, pricing, materials, or timeline must be approved in writing by both parties before execution.' : 'Variation control is not enabled for this contract.')}</p>
                </div>

                <div class="preview-section">
                    <h4>7. COMMUNICATION & DISPUTE RESOLUTION</h4>
                    <p><strong>Channel:</strong> <span>${esc(formatCommunicationChannel(c.communication_channel))}</span></p>
                    <p class="preview-paragraph">${esc(c.dispute_resolution || (terms.dispute && terms.dispute.dispute_resolution) || 'Disputes shall be resolved through mediation via the FixLanka platform.')}</p>
                </div>

                <div class="preview-section" style="${c.sent_to_customer ? '' : 'display:none;'}">
                    <h4>8. CUSTOMER RESPONSE</h4>
                    <div class="preview-grid">
                        <div><strong>Sent to Customer:</strong> <span>${esc(c.sent_to_customer ? ('Yes' + (c.sent_at ? ' — ' + formatDate(c.sent_at) : '')) : 'No')}</span></div>
                        <div><strong>Response:</strong> <span>${esc(formatCustomerResponse(c.customer_response || 'pending'))}</span></div>
                    </div>
                </div>
            </div>
        `;
    }

    function renderPreviewMilestonesTable(milestones, isMilestoneBased) {
        currentContractMilestones = milestones || [];

        const hasUnitMilestones = Array.isArray(milestones) && milestones.some(ms => {
            const ul = (ms && ms.unit_label != null) ? String(ms.unit_label).trim() : '';
            const ur = (ms && ms.unit_rate != null && ms.unit_rate !== '') ? parseFloat(ms.unit_rate) : 0;
            return ul !== '' || (ur > 0);
        });

        const computeBilledAmount = (ms) => {
            const agreedRate = parseFloat(ms?.unit_rate || 0);
            const actualRate = parseFloat(ms?.actual_unit_rate || 0);
            const unitRate = (actualRate > 0) ? actualRate : agreedRate;
            const actualQty = parseFloat(ms?.actual_quantity || 0);
            if (unitRate > 0 && actualQty > 0) return unitRate * actualQty;
            const stored = parseFloat(ms?.actual_amount ?? ms?.amount ?? ms?.payment_amount ?? 0);
            return Number.isFinite(stored) ? stored : 0;
        };

        let html = `<table class="preview-milestones-table">
            <thead><tr>
                <th>#</th>
                <th>Milestone</th>
                <th>Due Date</th>
                ${isMilestoneBased ? '<th>Amount</th>' : ''}
                <th>Status</th>
                <th>Action</th>
            </tr></thead><tbody>`;

        (milestones || []).forEach((ms, i) => {
            const msStatus = ms.status || 'pending';
            const statusText = formatStatus(msStatus);
            const statusClass = (msStatus || 'pending').replace(/ /g, '_');

            let actionBtn = '—';
            if (msStatus === 'submitted') {
                actionBtn = `<button class="action-btn secondary small" onclick="openProofModal(${ms.milestone_id})"><i class="fas fa-eye"></i> Review</button>`;
            } else if (msStatus === 'approved' || msStatus === 'paid') {
                actionBtn = '<span class="text-success"><i class="fas fa-check"></i> Paid</span>';
            }

            html += `<tr>
                <td>${i + 1}</td>
                <td>
                    <strong>${esc(ms.title || ms.milestone_name || ('Milestone ' + (i + 1)))}</strong>
                    ${ms.description ? '<br><small style="color:var(--text-muted)">' + esc(ms.description) + '</small>' : ''}
                    ${hasUnitMilestones && (ms.unit_label || ms.unit_rate) ? (() => {
                        const unitLabel = (ms.unit_label || '').toString().trim() || 'units';
                        const agreedRate = parseFloat(ms.unit_rate || 0);
                        const actualRate = parseFloat(ms.actual_unit_rate || 0);
                        const unitRate = (actualRate > 0) ? actualRate : agreedRate;
                        const actualQty = parseFloat(ms.actual_quantity || 0);
                        const billed = computeBilledAmount(ms);
                        const rateStr = (unitRate > 0) ? (formatCurrency(unitRate) + ' / ' + esc(unitLabel)) : '—';

                        const unitLine = `<br><small style="color:var(--text-muted)">Unit: ${esc(unitLabel)} • Rate: ${rateStr}</small>`;
                        const actualLine = (actualQty > 0 && unitRate > 0)
                            ? `<br><small style="color:var(--text-muted)">Submitted: ${actualQty} ${esc(unitLabel)} • Billed: <strong>${formatCurrency(billed)}</strong></small>`
                            : '';
                        return unitLine + actualLine;
                    })() : ''}
                </td>
                <td>${formatDate(ms.due_date)}</td>
                ${isMilestoneBased ? (() => {
                    if (hasUnitMilestones && (ms.unit_label || ms.unit_rate)) {
                        const actualQty = parseFloat(ms?.actual_quantity || 0);
                        return `<td>${actualQty > 0 ? formatCurrency(computeBilledAmount(ms)) : '—'}</td>`;
                    }
                    return `<td>${formatCurrency(ms.amount || ms.payment_amount)}</td>`;
                })() : ''}
                <td><span class="ms-status-badge ${statusClass}">${esc(statusText)}</span></td>
                <td>${actionBtn}</td>
            </tr>`;
        });

        html += '</tbody></table>';
        return html;
    }

    function renderPaymentSchedulePreview(c, milestones) {
        const totalVal = parseFloat(c.total_budget || 0) || 0;
        const method = c.payment_method || 'full_upfront';

        const hasUnitMilestones = Array.isArray(milestones) && milestones.some(ms => {
            const ul = (ms && ms.unit_label != null) ? String(ms.unit_label).trim() : '';
            const ur = (ms && ms.unit_rate != null && ms.unit_rate !== '') ? parseFloat(ms.unit_rate) : 0;
            return ul !== '' || (ur > 0);
        });

        let scheduleHTML = '<h5 class="preview-schedule-title"><i class="fas fa-receipt"></i> Payment Schedule</h5>';

        if (method === 'milestone_based' && milestones && milestones.length > 0) {
            if (hasUnitMilestones) {
                scheduleHTML += '<p class="preview-muted" style="margin-bottom:10px;">Unit-priced milestones are billed from verified actual units (and actual unit rates if provided).</p>';
                scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>Rate (per unit)</th></tr></thead><tbody>';
                milestones.forEach((ms, i) => {
                    const unitLabel = (ms.unit_label || '').toString().trim() || 'units';
                    const agreedRate = parseFloat(ms.unit_rate || 0);
                    const actualRate = parseFloat(ms.actual_unit_rate || 0);
                    const unitRate = (actualRate > 0) ? actualRate : agreedRate;
                    const rateStr = (unitRate > 0) ? (formatCurrency(unitRate) + ' / ' + esc(unitLabel)) : '—';
                    scheduleHTML += `<tr><td>${i + 1}</td><td>${esc(ms.title || ms.milestone_name || ('Milestone ' + (i + 1)))}</td><td>${rateStr}</td></tr>`;
                });
                scheduleHTML += '</tbody></table>';
                return scheduleHTML;
            }

            scheduleHTML += '<table class="preview-milestones-table"><thead><tr><th>#</th><th>Milestone</th><th>%</th><th>Amount</th></tr></thead><tbody>';
            milestones.forEach((ms, i) => {
                const pct = parseFloat(ms.payment_percentage || ms.percentage || 0) || 0;
                const amt = parseFloat(ms.payment_amount || ms.amount || (totalVal * pct / 100)) || 0;
                scheduleHTML += `<tr><td>${i + 1}</td><td>${esc(ms.title || ms.milestone_name || ('Milestone ' + (i + 1)))}</td><td>${pct}%</td><td>${formatCurrency(amt)}</td></tr>`;
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

    function formatCommunicationChannel(channel) {
        return 'FixLanka Platform';
    }

    function formatCustomerResponse(resp) {
        const responseLabels = {
            pending: 'Pending',
            accepted: 'Accepted',
            rejected: 'Rejected',
            negotiating: 'Negotiating'
        };
        return responseLabels[resp] || 'Pending';
    }

    function escAttr(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // Open shared contract chat widget
    window.openContractChat = async function (contractId, companyName, contractNumber) {
        if (typeof ChatWidget === 'undefined') {
            await window.showAlert('Chat is not available on this page.', 'warning');
            return;
        }
        ChatWidget.open(contractId, {
            name: companyName || 'Chat',
            contractNumber: contractNumber || `Contract #${contractId}`
        });
    };

    // Customer requests contract adjustments
    window.requestContractAdjustments = async function (contractId) {
        const contract = await _getContractForAdjustment(contractId);
        if (!contract) {
            await window.showAlert('Could not load contract details.', 'danger', 'Error');
            return;
        }
        _openAdjustModal(contract);
    };

    async function _getContractForAdjustment(contractId) {
        if (lastOpenedContractDetail && String(lastOpenedContractDetail.contract_id) === String(contractId)) {
            return lastOpenedContractDetail;
        }

        try {
            const res = await fetch(`${API}?action=get&id=${contractId}`);
            const json = await res.json();
            if (json && json.success) return json.data;
        } catch (e) {
            // ignore
        }
        return null;
    }

    function _isUnitPricedContract(contract) {
        if (!contract || contract.payment_method !== 'milestone_based') return false;
        const milestones = Array.isArray(contract.milestones) ? contract.milestones : [];
        return milestones.some(m => {
            const unitLabel = m?.unit_label ?? m?.unitLabel ?? null;
            const unitRate = m?.unit_rate ?? m?.unitRate ?? null;
            return (unitLabel != null && String(unitLabel).trim() !== '') || (unitRate != null && unitRate !== '');
        });
    }

    function _getAdjustMode() {
        const overlay = document.getElementById('contractAdjustOverlay');
        return overlay?.dataset?.adjustMode || 'milestone';
    }

    function _ensureAdjustModalDOM() {
        if (document.getElementById('contractAdjustOverlay')) return;

        const html = `
        <div class="ca-overlay" id="contractAdjustOverlay" aria-hidden="true">
            <div class="ca-modal" role="dialog" aria-modal="true" aria-labelledby="caTitle">
                <div class="ca-header">
                    <div>
                        <h3 id="caTitle">Request Contract Adjustments</h3>
                        <div class="ca-sub" id="caSub"></div>
                    </div>
                    <button type="button" class="ca-close" id="caCloseBtn" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="ca-body">
                    <div class="ca-grid">
                        <label class="ca-field">
                            <span>Start date</span>
                            <input type="date" id="caStartDate" />
                        </label>
                        <label class="ca-field">
                            <span>End date</span>
                            <input type="date" id="caEndDate" />
                        </label>
                    </div>

                    <div class="ca-section" id="caMilestonesSection" style="display:none">
                        <div class="ca-section-head">
                            <h4 id="caMilestonesTitle">Milestones</h4>
                            <button type="button" class="btn-secondary" id="caAddMilestoneBtn"><i class="fas fa-plus"></i> Add</button>
                        </div>
                        <div class="ca-muted" id="caMilestonesHint">Edit only what you need.</div>
                        <div class="ca-ms-wrap" id="caMilestonesWrap"></div>
                    </div>

                    <div class="ca-section">
                        <h4>Note to the company (optional)</h4>
                        <textarea id="caNote" class="ca-note" rows="3" placeholder="Explain what you changed and why..."></textarea>
                    </div>
                </div>
                <div class="ca-footer">
                    <button type="button" class="btn-secondary" id="caCancelBtn">Cancel</button>
                    <button type="button" class="btn-primary" id="caSubmitBtn"><i class="fas fa-paper-plane"></i> Send request</button>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);

        const overlay = document.getElementById('contractAdjustOverlay');
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) _closeAdjustModal();
        });

        document.getElementById('caCloseBtn').addEventListener('click', _closeAdjustModal);
        document.getElementById('caCancelBtn').addEventListener('click', _closeAdjustModal);
    }

    function _openAdjustModal(contract) {
        _ensureAdjustModalDOM();
        const overlay = document.getElementById('contractAdjustOverlay');
        if (!overlay) return;

        overlay.dataset.contractId = String(contract.contract_id);
        const sub = document.getElementById('caSub');
        if (sub) sub.textContent = `${contract.contract_number || ('Contract #' + contract.contract_id)} • ${contract.company_name || ''}`;

        const startEl = document.getElementById('caStartDate');
        const endEl = document.getElementById('caEndDate');
        const noteEl = document.getElementById('caNote');

        if (startEl) startEl.value = (contract.start_date || '').slice(0, 10);
        if (endEl) endEl.value = (contract.end_date || '').slice(0, 10);
        if (noteEl) noteEl.value = '';

        const isMilestoneBased = contract.payment_method === 'milestone_based';
        const isUnitPriced = isMilestoneBased && _isUnitPricedContract(contract);
        overlay.dataset.adjustMode = isUnitPriced ? 'unit' : 'milestone';

        const msSection = document.getElementById('caMilestonesSection');
        if (msSection) msSection.style.display = isMilestoneBased ? 'block' : 'none';

        if (isMilestoneBased) {
            const addBtn = document.getElementById('caAddMilestoneBtn');
            const titleEl = document.getElementById('caMilestonesTitle');
            const hintEl = document.getElementById('caMilestonesHint');

            if (isUnitPriced) {
                if (titleEl) titleEl.textContent = 'Unit rates';
                if (hintEl) hintEl.textContent = 'Unit-priced contract: you can propose new unit rates here. Milestone %/amount adjustments are not used for unit-priced billing.';
                if (addBtn) addBtn.style.display = 'none';
                _renderUnitRateEditor(contract.milestones || []);
            } else {
                if (titleEl) titleEl.textContent = 'Milestones';
                if (hintEl) hintEl.textContent = 'Edit only what you need.';
                if (addBtn) {
                    addBtn.style.display = '';
                    addBtn.onclick = () => {
                        _appendMilestoneRow({ title: '', due_date: '', amount: '', description: '' });
                    };
                }
                _renderMilestoneEditor(contract.milestones || []);
            }
        }

        const submitBtn = document.getElementById('caSubmitBtn');
        if (submitBtn) {
            submitBtn.onclick = () => _submitAdjustmentRequest(contract);
        }

        overlay.classList.add('show');
        updateBodyScrollLock();
    }

    function _closeAdjustModal() {
        const overlay = document.getElementById('contractAdjustOverlay');
        if (!overlay) return;
        overlay.classList.remove('show');
        updateBodyScrollLock();
    }

    function _renderMilestoneEditor(milestones) {
        const wrap = document.getElementById('caMilestonesWrap');
        if (!wrap) return;

        wrap.dataset.mode = 'milestone';

        const budgetRaw = document.getElementById('caBudget')?.value;
        const budget = budgetRaw !== '' && budgetRaw != null ? Number(budgetRaw) : null;

        const rows = Array.isArray(milestones) && milestones.length
            ? milestones.map(m => {
                const amt = (m.payment_amount ?? m.amount ?? null);
                const pctExisting = m.payment_percentage ?? m.percentage ?? null;
                const pct = (pctExisting != null && pctExisting !== '')
                    ? Number(pctExisting)
                    : (budget && amt != null ? (Number(amt) / Number(budget)) * 100 : null);
                return {
                    title: m.title || m.milestone_name || '',
                    due_date: (m.due_date || '').slice(0, 10),
                    percentage: (pct != null && Number.isFinite(Number(pct))) ? Number(pct).toFixed(2) : '',
                    amount: (amt != null && amt !== '') ? String(amt) : '',
                    description: m.description || m.milestone_description || ''
                };
            })
            : [{ title: '', due_date: '', percentage: '', amount: '', description: '' }];

        wrap.innerHTML = `
            <table class="ca-ms-table">
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Title</th>
                        <th style="width:140px">Due date</th>
                        <th style="width:120px">%</th>
                        <th style="width:150px">Amount</th>
                        <th style="width:44px"></th>
                    </tr>
                </thead>
                <tbody id="caMsTbody"></tbody>
            </table>
            <div class="ca-ms-help">Click a milestone row to edit its description.</div>
            <div class="ca-ms-totals" id="caMsTotals"></div>
            <div class="ca-ms-desc" id="caMsDescWrap" style="display:none">
                <div class="ca-ms-desc-head">
                    <strong id="caMsDescTitle">Milestone</strong>
                    <button type="button" class="btn-secondary" id="caMsDescClose"><i class="fas fa-times"></i> Done</button>
                </div>
                <textarea id="caMsDesc" rows="4" placeholder="Description / deliverables..."></textarea>
            </div>
        `;

        const tbody = document.getElementById('caMsTbody');
        rows.forEach(r => _appendMilestoneRow(r, tbody));

        const descClose = document.getElementById('caMsDescClose');
        if (descClose) descClose.onclick = () => _closeMilestoneDesc();

        _recalcMilestoneAmounts();
        _updateMilestoneTotalsHint();
    }

    function _renderUnitRateEditor(milestones) {
        const wrap = document.getElementById('caMilestonesWrap');
        if (!wrap) return;

        wrap.dataset.mode = 'unit';
        const unitRows = Array.isArray(milestones) ? milestones.filter(m => {
            const unitLabel = m?.unit_label ?? m?.unitLabel ?? null;
            const unitRate = m?.unit_rate ?? m?.unitRate ?? null;
            return (unitLabel != null && String(unitLabel).trim() !== '') || (unitRate != null && unitRate !== '');
        }) : [];

        const rows = unitRows.map(m => ({
            milestone_id: m.milestone_id ?? null,
            milestone_number: m.milestone_number ?? null,
            title: m.title || m.milestone_name || '',
            unit_label: (m.unit_label ?? m.unitLabel ?? '').toString(),
            unit_rate: (m.unit_rate ?? m.unitRate ?? ''),
            proposed_unit_rate: ''
        }));

        wrap.innerHTML = `
            <table class="ca-ms-table">
                <thead>
                    <tr>
                        <th style="width:28px">#</th>
                        <th>Item</th>
                        <th style="width:180px">Unit</th>
                        <th style="width:160px">Current rate</th>
                        <th style="width:180px">Proposed rate</th>
                    </tr>
                </thead>
                <tbody id="caUnitTbody"></tbody>
            </table>
            <div class="ca-ms-help">Enter proposed unit rates if you want to renegotiate pricing. Explain any changes in the note.</div>
            <div class="ca-ms-totals" id="caMsTotals"></div>
        `;

        const tbody = document.getElementById('caUnitTbody');
        if (!tbody) return;

        rows.forEach((r, idx) => {
            const tr = document.createElement('tr');
            tr.className = 'ca-unit-row';
            tr.dataset.milestoneId = r.milestone_id != null ? String(r.milestone_id) : '';
            tr.dataset.milestoneNumber = r.milestone_number != null ? String(r.milestone_number) : '';
            tr.dataset.title = (r.title || '').trim();
            tr.dataset.unitLabel = (r.unit_label || '').trim();
            tr.dataset.currentUnitRate = (r.unit_rate != null ? String(r.unit_rate) : '');
            tr.innerHTML = `
                <td class="ca-ms-idx">${idx + 1}</td>
                <td>${escAttr(r.title)}</td>
                <td>${escAttr((r.unit_label || '').trim() || '—')}</td>
                <td>Rs. ${escAttr((r.unit_rate != null && r.unit_rate !== '') ? String(r.unit_rate) : '—')}</td>
                <td>
                    <input type="number" min="0" step="0.01" class="ca-unit-proposed-rate" value="" placeholder="Leave blank to keep" />
                </td>
            `;
            tbody.appendChild(tr);

            const proposedEl = tr.querySelector('.ca-unit-proposed-rate');
            if (proposedEl) {
                proposedEl.addEventListener('input', () => {
                    _setInputError(proposedEl, false);
                    _updateMilestoneTotalsHint();
                });
            }
        });

        _updateMilestoneTotalsHint();
    }

    function _appendMilestoneRow(row, tbodyOverride) {
        const tbody = tbodyOverride || document.getElementById('caMsTbody');
        if (!tbody) return;

        const tr = document.createElement('tr');
        tr.className = 'ca-ms-row';
        tr.innerHTML = `
            <td class="ca-ms-idx"></td>
            <td><input type="text" class="ca-ms-title" value="${escAttr(row.title)}" placeholder="Milestone title" /></td>
            <td><input type="date" class="ca-ms-date" value="${escAttr(row.due_date)}" /></td>
            <td><input type="number" min="0" max="100" step="0.01" class="ca-ms-pct" value="${escAttr(row.percentage)}" placeholder="0.00" /></td>
            <td><input type="number" min="0" step="0.01" class="ca-ms-amount" value="${escAttr(row.amount)}" placeholder="0.00" readonly /></td>
            <td><button type="button" class="ca-ms-remove" title="Remove"><i class="fas fa-trash"></i></button></td>
        `;
        tr.dataset.description = row.description || '';

        const pctEl = tr.querySelector('.ca-ms-pct');
        if (pctEl) {
            pctEl.addEventListener('input', () => {
                _recalcMilestoneAmounts();
                _updateMilestoneTotalsHint();
            });
        }

        tr.querySelector('.ca-ms-remove').addEventListener('click', (e) => {
            e.stopPropagation();
            tr.remove();
            _renumberMilestones();
            _closeMilestoneDesc();
            _recalcMilestoneAmounts();
            _updateMilestoneTotalsHint();
        });

        tr.addEventListener('click', (e) => {
            if (e.target && (e.target.tagName === 'INPUT' || e.target.closest('button'))) return;
            _openMilestoneDesc(tr);
        });

        tbody.appendChild(tr);
        _renumberMilestones();
    }

    function _recalcMilestoneAmounts() {
        if (document.getElementById('caMilestonesWrap')?.dataset?.mode === 'unit') return;
        const budgetRaw = document.getElementById('caBudget')?.value;
        const budget = budgetRaw !== '' && budgetRaw != null ? Number(budgetRaw) : null;
        if (!budget || !Number.isFinite(budget) || budget <= 0) return;

        const rows = Array.from(document.querySelectorAll('#caMsTbody .ca-ms-row'));
        rows.forEach(r => {
            const pctRaw = r.querySelector('.ca-ms-pct')?.value;
            const pct = pctRaw !== '' && pctRaw != null ? Number(pctRaw) : null;
            const amountEl = r.querySelector('.ca-ms-amount');
            if (!amountEl) return;
            if (pct != null && Number.isFinite(pct)) {
                const amt = (budget * pct) / 100;
                amountEl.value = Number.isFinite(amt) ? amt.toFixed(2) : '';
            }
        });
    }

    function _updateMilestoneTotalsHint() {
        const el = document.getElementById('caMsTotals');
        if (!el) return;

        const mode = document.getElementById('caMilestonesWrap')?.dataset?.mode;
        if (mode === 'unit') {
            const rows = Array.from(document.querySelectorAll('#caUnitTbody .ca-unit-row'));
            const withProposed = rows.filter(r => {
                const v = r.querySelector('.ca-unit-proposed-rate')?.value;
                return v !== '' && v != null;
            }).length;
            el.innerHTML = `
                <span class="ca-ms-total ok">Unit-priced milestones: ${rows.length} item(s)</span>
                <span class="ca-ms-total ${withProposed > 0 ? 'ok' : ''}">${withProposed} proposed rate change(s)</span>
            `;
            return;
        }

        const rows = Array.from(document.querySelectorAll('#caMsTbody .ca-ms-row'));
        const sumPct = rows.reduce((acc, r) => acc + (Number(r.querySelector('.ca-ms-pct')?.value) || 0), 0);
        const budgetRaw = document.getElementById('caBudget')?.value;
        const budget = budgetRaw !== '' && budgetRaw != null ? Number(budgetRaw) : null;
        const sumAmt = rows.reduce((acc, r) => acc + (Number(r.querySelector('.ca-ms-amount')?.value) || 0), 0);

        const pctOk = Math.abs(sumPct - 100) <= 0.01;
        const amtOk = (budget && Number.isFinite(budget)) ? (Math.abs(sumAmt - budget) <= 0.05) : true;

        el.innerHTML = `
            <span class="ca-ms-total ${pctOk ? 'ok' : 'bad'}">Total: ${sumPct.toFixed(2)}%</span>
            ${budget && Number.isFinite(budget) ? `<span class="ca-ms-total ${amtOk ? 'ok' : 'bad'}">Amounts: Rs. ${sumAmt.toFixed(2)} / Rs. ${budget.toFixed(2)}</span>` : ''}
        `;
    }

    function _renumberMilestones() {
        const rows = Array.from(document.querySelectorAll('#caMsTbody .ca-ms-row'));
        rows.forEach((r, idx) => {
            const cell = r.querySelector('.ca-ms-idx');
            if (cell) cell.textContent = String(idx + 1);
        });
    }

    function _openMilestoneDesc(tr) {
        const wrap = document.getElementById('caMsDescWrap');
        const ta = document.getElementById('caMsDesc');
        const title = document.getElementById('caMsDescTitle');
        if (!wrap || !ta || !title) return;

        // Save any open description first
        const prev = wrap.dataset.activeRowId;
        if (prev) {
            const prevRow = document.querySelector(`#caMsTbody .ca-ms-row[data-row-id="${prev}"]`);
            if (prevRow) prevRow.dataset.description = ta.value;
        }

        if (!tr.dataset.rowId) tr.dataset.rowId = String(Date.now()) + String(Math.random()).slice(2);
        wrap.dataset.activeRowId = tr.dataset.rowId;

        const t = tr.querySelector('.ca-ms-title');
        title.textContent = (t && t.value ? t.value : 'Milestone') + ' — Description';
        ta.value = tr.dataset.description || '';
        wrap.style.display = 'block';
    }

    function _closeMilestoneDesc() {
        const wrap = document.getElementById('caMsDescWrap');
        const ta = document.getElementById('caMsDesc');
        if (!wrap || !ta) return;

        const active = wrap.dataset.activeRowId;
        if (active) {
            const row = document.querySelector(`#caMsTbody .ca-ms-row[data-row-id="${active}"]`);
            if (row) row.dataset.description = ta.value;
        }

        wrap.dataset.activeRowId = '';
        wrap.style.display = 'none';
    }

    function _collectMilestonesFromEditor() {
        _closeMilestoneDesc();
        const rows = Array.from(document.querySelectorAll('#caMsTbody .ca-ms-row'));
        return rows.map(r => {
            const title = (r.querySelector('.ca-ms-title')?.value || '').trim();
            const dueDate = r.querySelector('.ca-ms-date')?.value || '';
            const pctRaw = r.querySelector('.ca-ms-pct')?.value;
            const pct = pctRaw !== '' && pctRaw != null ? Number(pctRaw) : null;
            const amountRaw = r.querySelector('.ca-ms-amount')?.value;
            const amount = amountRaw !== '' && amountRaw != null ? Number(amountRaw) : null;
            return {
                title,
                due_date: dueDate || null,
                amount: Number.isFinite(amount) ? amount : null,
                percentage: Number.isFinite(pct) ? pct : null,
                description: (r.dataset.description || '').trim()
            };
        }).filter(m => m.title);
    }

    function _collectUnitRateProposalsFromEditor() {
        const rows = Array.from(document.querySelectorAll('#caUnitTbody .ca-unit-row'));
        return rows.map(r => {
            const proposedRaw = r.querySelector('.ca-unit-proposed-rate')?.value;
            const proposed = proposedRaw !== '' && proposedRaw != null ? Number(proposedRaw) : null;
            const currentRaw = r.dataset.currentUnitRate;
            const current = currentRaw !== '' && currentRaw != null ? Number(currentRaw) : null;

            return {
                milestone_id: r.dataset.milestoneId ? Number(r.dataset.milestoneId) : null,
                milestone_number: r.dataset.milestoneNumber ? Number(r.dataset.milestoneNumber) : null,
                title: (r.dataset.title || '').trim() || null,
                unit_label: (r.dataset.unitLabel || '').trim() || null,
                current_unit_rate: Number.isFinite(current) ? current : null,
                proposed_unit_rate: Number.isFinite(proposed) ? proposed : null
            };
        }).filter(x => x.proposed_unit_rate != null);
    }

    function _setInputError(el, isError) {
        if (!el) return;
        el.classList.toggle('ca-error', !!isError);
    }

    async function _submitAdjustmentRequest(contract) {
        const submitBtn = document.getElementById('caSubmitBtn');
        if (submitBtn) submitBtn.disabled = true;

        try {
            const startDate = document.getElementById('caStartDate')?.value || null;
            const endDate = document.getElementById('caEndDate')?.value || null;
            let note = (document.getElementById('caNote')?.value || '').trim();

            _setInputError(document.getElementById('caStartDate'), false);
            _setInputError(document.getElementById('caEndDate'), false);

            if (!startDate || !endDate) {
                _setInputError(document.getElementById('caStartDate'), !startDate);
                _setInputError(document.getElementById('caEndDate'), !endDate);
                await window.showAlert('Please provide a valid start date and end date.', 'warning');
                return;
            }

            if (new Date(endDate) < new Date(startDate)) {
                _setInputError(document.getElementById('caStartDate'), true);
                _setInputError(document.getElementById('caEndDate'), true);
                await window.showAlert('End date must be after start date.', 'warning');
                return;
            }

            const proposed = {
                start_date: startDate,
                end_date: endDate
            };

            if (contract.payment_method === 'milestone_based') {
                const isUnitPriced = _isUnitPricedContract(contract);

                if (isUnitPriced) {
                    // Unit-priced contracts cannot submit milestone % changes (backend requires % totals).
                    // Instead, allow proposing unit rate changes as a separate payload.
                    const unitRows = Array.from(document.querySelectorAll('#caUnitTbody .ca-unit-row'));
                    let badRate = false;
                    unitRows.forEach(r => {
                        const el = r.querySelector('.ca-unit-proposed-rate');
                        if (!el) return;
                        _setInputError(el, false);
                        const raw = el.value;
                        if (raw === '' || raw == null) return;
                        const n = Number(raw);
                        if (!Number.isFinite(n) || n < 0) {
                            _setInputError(el, true);
                            badRate = true;
                        }
                    });
                    if (badRate) {
                        await window.showAlert('Please enter valid proposed unit rates (0 or higher), or leave them blank.', 'warning');
                        _updateMilestoneTotalsHint();
                        return;
                    }

                    const proposals = _collectUnitRateProposalsFromEditor();
                    if (proposals.length) proposed.unit_rate_proposals = proposals;

                    if (proposals.length) {
                        const lines = proposals.map(p => {
                            const name = p.title || 'Unit item';
                            const unit = p.unit_label ? ` per ${p.unit_label}` : '';
                            const cur = (p.current_unit_rate != null) ? ` (current: Rs. ${Number(p.current_unit_rate).toFixed(2)})` : '';
                            return `- ${name}: Rs. ${Number(p.proposed_unit_rate).toFixed(2)}${unit}${cur}`;
                        });
                        const summary = `Proposed unit rates:\n${lines.join('\n')}`;
                        note = note ? `${note}\n\n${summary}` : summary;
                    }

                    _updateMilestoneTotalsHint();
                } else {
                    const milestones = _collectMilestonesFromEditor();
                    proposed.milestones = milestones;

                    // Company-side style validations
                    if (milestones.length < 2) {
                        await window.showAlert('Minimum 2 milestones required.', 'warning');
                        return;
                    }
                    if (milestones.length > 10) {
                        await window.showAlert('Maximum 10 milestones allowed.', 'warning');
                        return;
                    }

                    // Field validations per milestone
                    const start = new Date(startDate);
                    const end = new Date(endDate);
                    let sumPct = 0;
                    let hasBad = false;

                    const rows = Array.from(document.querySelectorAll('#caMsTbody .ca-ms-row'));
                    rows.forEach((r) => {
                        _setInputError(r.querySelector('.ca-ms-title'), false);
                        _setInputError(r.querySelector('.ca-ms-date'), false);
                        _setInputError(r.querySelector('.ca-ms-pct'), false);
                    });

                    milestones.forEach((m, idx) => {
                        const rowEl = rows[idx];
                        if (!m.title) {
                            _setInputError(rowEl?.querySelector('.ca-ms-title'), true);
                            hasBad = true;
                        }
                        if (!m.due_date) {
                            _setInputError(rowEl?.querySelector('.ca-ms-date'), true);
                            hasBad = true;
                        } else {
                            const d = new Date(m.due_date);
                            if (d < start || d > end) {
                                _setInputError(rowEl?.querySelector('.ca-ms-date'), true);
                                hasBad = true;
                            }
                        }
                        const pct = Number(m.percentage);
                        if (!Number.isFinite(pct) || pct <= 0 || pct > 100) {
                            _setInputError(rowEl?.querySelector('.ca-ms-pct'), true);
                            hasBad = true;
                        } else {
                            sumPct += pct;
                        }
                    });

                    if (hasBad) {
                        await window.showAlert('Please fix milestone fields (title, due date within range, and valid percentage).', 'warning');
                        _updateMilestoneTotalsHint();
                        return;
                    }

                    if (Math.abs(sumPct - 100) > 0.01) {
                        await window.showAlert(`Milestone percentages must total 100% (currently: ${sumPct.toFixed(2)}%).`, 'warning');
                        _updateMilestoneTotalsHint();
                        return;
                    }

                    // Ensure amounts are synced from percentages + budget
                    _recalcMilestoneAmounts();
                    const sumAmt = milestones.reduce((acc, mm) => acc + (Number(mm.amount) || 0), 0);

                    _updateMilestoneTotalsHint();
                }
            }

            const res = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'request_contract_change',
                    contract_id: contract.contract_id,
                    request_text: note,
                    proposed_changes: proposed
                })
            });
            const json = await res.json();
            if (!json.success) {
                await window.showAlert(json.message || 'Failed to submit change request', 'danger', 'Error');
                return;
            }

            _closeAdjustModal();
            await window.showAlert('Adjustment request sent to the company.', 'success', 'Success');

            // Open chat so the request is visible immediately
            try {
                const current = allContracts.find(x => String(x.contract_id) === String(contract.contract_id));
                openContractChat(
                    contract.contract_id,
                    current ? current.company_name : (contract.company_name || 'Chat'),
                    current ? current.contract_number : (contract.contract_number || '')
                );
            } catch (e) {
                // ignore
            }
        } catch (err) {
            console.error('Change request error:', err);
            await window.showAlert('Network error. Please try again.', 'danger', 'Error');
        } finally {
            if (submitBtn) submitBtn.disabled = false;
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
                actionBtn = `<button class="action-btn secondary small" onclick="openProofModal(${ms.milestone_id})"><i class="fas fa-eye"></i> Review</button>`;
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
        const tabs = document.getElementById('contractStatusTabs');
        const searchInput = document.getElementById('custSearchInput');

        if (tabs) {
            tabs.querySelectorAll('.filter-tab').forEach(btn => {
                btn.addEventListener('click', () => {
                    tabs.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentFilter = btn.dataset.status || '';
                    renderList();
                });
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
        updateBodyScrollLock();
    }

    // =========================================
    // RESPOND TO CONTRACT
    // =========================================
    window.respondContract = async function (contractId, response) {
        if (response !== 'accepted') {
            const label = 'decline';
            const confirmed = await window.showConfirm(
                `Are you sure you want to ${label} this contract?`,
                { title: 'Decline Contract', type: 'danger', confirmText: 'Yes, Decline' }
            );
            if (!confirmed) return;

            try {
                const res = await fetch(API, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'respond',
                        contract_id: contractId,
                        response: response
                    })
                });
                const json = await res.json();
                if (json.success) {
                    showToast(`Contract ${label}ed successfully!`);
                    closeDetail();
                    loadContracts();
                } else {
                    await window.showAlert('Error: ' + (json.message || 'Unknown error'), 'danger', 'Error');
                }
            } catch (err) {
                console.error('Respond error:', err);
                await window.showAlert('Could not process your response.', 'danger', 'Error');
            }
            return;
        }

        // Acceptance flow (NO initial deposit step): go straight to e-sign confirmation.
        triggerSignatureConfirmation(contractId);
    };

    /**
     * Final E-Sign Confirmation Modal
     */
    async function triggerSignatureConfirmation(contractId) {
        const confirmed = await window.showConfirm(
            'By clicking Confirm, you confirm you have read and agree to the contract terms, and you electronically sign this contract in FixLanka.',
            { title: 'Electronic Signature Confirmation', confirmText: 'Confirm & Sign' }
        );
        if (!confirmed) return;

        try {
            const acceptRes = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'respond',
                    contract_id: contractId,
                    response: 'accepted',
                    esign_consent: true
                })
            });
            const acceptJson = await acceptRes.json();
            if (acceptJson.success) {
                showToast('Contract accepted successfully!');
                closeDetail();
                loadContracts();
            } else {
                await window.showAlert('Error: ' + (acceptJson.message || 'Unknown error'), 'danger', 'Error');
            }
        } catch (err) {
            console.error('Accept error:', err);
            await window.showAlert('Could not sign contract. Please try again.', 'danger', 'Error');
        }
    }

    /**
     * Shows a simulated payment modal for the upfront escrow deposit
     */
    function showEscrowPaymentModal(contract, amount) {
        if (typeof window.showAlert === 'function') {
            window.showAlert('Initial deposit is not required. Please continue with signing.', 'info', 'Notice');
        }
        triggerSignatureConfirmation(contract.contract_id);
        return;

        let overlay = document.getElementById('escrowPaymentOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'escrowPaymentOverlay';
            overlay.className = 'payment-modal-overlay';
            document.body.appendChild(overlay);
        }

        // Simple escaping helper
        const esc = (str) => {
            if (!str) return '';
            return str.replace(/[&<>"']/g, m => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            })[m]);
        };

        overlay.innerHTML = `
            <div class="payment-modal">
                <div class="payment-header">
                    <div class="payment-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h2 class="payment-title">Secure Escrow Deposit</h2>
                    <p class="payment-desc">Upfront payment required to start <strong>${esc(contract.project_title)}</strong></p>
                </div>

                <div class="payment-amount-box">
                    <div class="payment-label">Required Initial Deposit</div>
                    <div class="payment-value">LKR ${numericAmount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                </div>

                <div class="payment-info">
                    <p><i class="fas fa-info-circle"></i> This amount will be held securely in FixLanka's Escrow system and only released to the company when milestones are completed and approved by you.</p>
                </div>

                <div class="payment-card-sim">
                    <label>Card Details (Simulated)</label>
                    <input type="text" placeholder="#### #### #### ####" value="4242 4242 4242 4242" readonly>
                    <div class="row">
                        <input type="text" placeholder="MM/YY" value="12/28" readonly>
                        <input type="text" placeholder="CVC" value="311" readonly>
                    </div>
                </div>

                <div class="payment-footer">
                    <button class="btn btn-secondary" onclick="document.getElementById('escrowPaymentOverlay').classList.remove('show')">Cancel</button>
                    <button class="btn btn-primary" id="confirmPaymentBtn">
                        <i class="fas fa-lock"></i> Pay & Proceed
                    </button>
                </div>
            </div>
        `;

        overlay.classList.add('show');

        const confirmBtn = overlay.querySelector('#confirmPaymentBtn');
        confirmBtn.onclick = async () => {
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Artificial delay to simulate processing
            setTimeout(() => {
                overlay.classList.remove('show');
                // Proceed to signature confirmation
                triggerSignatureConfirmation(contract.contract_id, true);
            }, 1000);
        };
    }


    // =========================================
    // UNDO CONTRACT
    // =========================================
    window.undoContract = async function (contractId) {
        const reason = await window.showPrompt(
            'Please provide a reason for cancelling this contract (required):',
            '',
            { title: 'Cancel Contract', placeholder: 'Reason...', confirmText: 'Continue' }
        );
        if (reason === null) return;

        if (!String(reason).trim()) {
            await window.showAlert('Please provide a reason.', 'warning');
            return;
        }

        const confirmed = await window.showConfirm("Are you sure you want to cancel this contract? This action cannot be undone and the job request will be reopened.", { title: 'Cancel Contract', type: 'danger', confirmText: 'Cancel Contract' });
        if (!confirmed) return;

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
                body: JSON.stringify({ contract_id: contractId, reason: String(reason).trim() })
            });
            const json = await res.json();

            if (json.success) {
                await window.showAlert('Contract cancelled successfully.', 'success', 'Success');
                closeContractDetail();
                // Reload to refresh lists
                loadContracts();
                updateStats(); // Refresh stats too
            } else {
                await window.showAlert('Error: ' + json.message, 'danger', 'Error');
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-undo"></i> Undo Contract';
                    btn.disabled = false;
                }
            }
        } catch (err) {
            console.error('Undo error:', err);
            await window.showAlert('Could not cancel contract. Please try again.', 'danger', 'Error');
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

    function formatUnitPrice(value) {
        if (value === null || value === undefined || value === '') {
            return null;
        }

        const numericValue = Number(value);
        if (!Number.isFinite(numericValue)) {
            return null;
        }

        return formatCurrency(numericValue);
    }

    function resolveQuotationPerLabel(rawUnitLabel, type) {
        if (!rawUnitLabel || typeof rawUnitLabel !== 'string') {
            return '';
        }

        const normalized = rawUnitLabel.trim().toLowerCase();
        if (!normalized) {
            return '';
        }

        if (normalized.includes('hour')) {
            return type === 'material' ? '' : 'per hour';
        }

        if (
            normalized.includes('m²') ||
            normalized.includes('m2') ||
            normalized.includes('sqm') ||
            normalized.includes('sqft') ||
            normalized.includes('area')
        ) {
            return 'per area';
        }

        if (normalized.includes('unit')) {
            return 'per no. of units';
        }

        return '';
    }

    function formatCostLabel(baseLabel, perLabel) {
        return perLabel ? `${baseLabel} (${perLabel})` : baseLabel;
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
        } catch (e) { /* no files */ }

        // Unit billing summary
        const unitLabel = ms.unit_label || 'units';
        const agreedRate = parseFloat(ms.unit_rate || 0);
        const actualRate = parseFloat(ms.actual_unit_rate || 0);
        const unitRate = (actualRate > 0) ? actualRate : agreedRate;
        const estQty = parseFloat(ms.estimated_quantity || 0);
        const actualQty = parseFloat(ms.actual_quantity || 0);
        const hasDynamicBill = unitRate > 0 && actualQty > 0;
        const billedAmount = hasDynamicBill ? unitRate * actualQty : parseFloat(ms.actual_amount || ms.amount || 0);
        const estimatedTotal = agreedRate > 0 ? agreedRate * estQty : parseFloat(ms.amount || 0);

        const billingHtml = hasDynamicBill ? `
            <div class="unit-billing-card">
                <h4><i class="fas fa-calculator"></i> Unit Billing Verification</h4>
                <div class="unit-billing-grid">
                    <div class="ub-row">
                        <span class="ub-label">Agreed Rate</span>
                        <span class="ub-value">${formatCurrency(agreedRate)} / ${esc(unitLabel)}</span>
                    </div>
                    ${actualRate > 0 && Math.abs(actualRate - agreedRate) > 0.009 ? `
                    <div class="ub-row">
                        <span class="ub-label">Actual Rate <span class="ub-sub">(material variation)</span></span>
                        <span class="ub-value ub-actual">${formatCurrency(actualRate)} / ${esc(unitLabel)}</span>
                    </div>` : ''}
                    <div class="ub-row">
                        <span class="ub-label">Estimated <span class="ub-sub">(from quotation)</span></span>
                        <span class="ub-value ub-estimate">${estQty > 0 ? estQty + ' ' + esc(unitLabel) : '—'}
                            ${estQty > 0 ? '<span class="ub-sub">≈ ' + formatCurrency(estimatedTotal) + '</span>' : ''}
                        </span>
                    </div>
                    <div class="ub-row">
                        <span class="ub-label">Actual Submitted <span class="ub-sub">(by company)</span></span>
                        <span class="ub-value ub-actual">${actualQty} ${esc(unitLabel)}</span>
                    </div>
                    <div class="ub-divider"></div>
                    <div class="ub-row ub-total">
                        <span class="ub-label"><strong>Amount to Pay</strong></span>
                        <span class="ub-value ub-pay-amount">${formatCurrency(billedAmount)}</span>
                    </div>
                </div>
                <p class="ub-note"><i class="fas fa-info-circle"></i> By approving, you confirm ${actualQty} ${esc(unitLabel)} of work was completed and agree to pay <strong>${formatCurrency(billedAmount)}</strong>.</p>
            </div>` : `
            <div class="unit-billing-card">
                <h4><i class="fas fa-receipt"></i> Payment Summary</h4>
                <div class="unit-billing-grid">
                    <div class="ub-row ub-total">
                        <span class="ub-label"><strong>Amount on Approval</strong></span>
                        <span class="ub-value ub-pay-amount">${formatCurrency(billedAmount)}</span>
                    </div>
                </div>
                <p class="ub-note"><i class="fas fa-info-circle"></i> By approving, you confirm this stage is complete and agree to pay <strong>${formatCurrency(billedAmount)}</strong>.</p>
            </div>`;

        body.innerHTML = `
        <div class="proof-section">
            <h4><i class="fas fa-align-left"></i> Company Notes</h4>
            <div class="proof-desc">${esc(ms.comments || ms.proof_of_work || 'No description provided.')}</div>
        </div>
        ${billingHtml}
        <div class="proof-section">
            <h4><i class="fas fa-paperclip"></i> Attached Files</h4>
            ${filesHtml}
        </div>`;

        overlay.classList.add('show');
        updateBodyScrollLock();
    };

    window.closeProofModal = function () {
        const overlay = document.getElementById('proofReviewOverlay');
        if (overlay) overlay.classList.remove('show');
        currentReviewMilestoneId = null;
        updateBodyScrollLock();
    };

    window.verifyMilestoneCurrent = async function (action) {
        if (!currentReviewMilestoneId) return;

        const ms = currentContractMilestones.find(m => m.milestone_id == currentReviewMilestoneId);
        const agreedRate = parseFloat(ms?.unit_rate || 0);
        const actualRate = parseFloat(ms?.actual_unit_rate || 0);
        const unitRate = (actualRate > 0) ? actualRate : agreedRate;
        const actualQty = parseFloat(ms?.actual_quantity || 0);
        const billedAmt = (unitRate > 0 && actualQty > 0) ? unitRate * actualQty : parseFloat(ms?.actual_amount || ms?.amount || 0);
        const unitLabel = ms?.unit_label || 'units';

        if (action === 'approve') {
            const payStr = billedAmt > 0 ? `\n\nThis will record a payment of ${formatCurrency(billedAmt)}.` : '';
            const confirmed = await window.showConfirm(`Confirm Approval\n\nYou are confirming that ${actualQty > 0 ? actualQty + ' ' + unitLabel + ' of work' : 'this stage'} was completed.${payStr}\n\nThis action cannot be undone.`, { title: 'Confirm Milestone', confirmText: 'Approve & Pay' });
            if (!confirmed) return;
        } else {
            const feedback = await window.showPrompt('Please provide a reason for rejection (required):', '', { title: 'Reject Milestone', placeholder: 'Explain what was missing or incorrect...', confirmText: 'Submit Rejection' });
            if (!feedback || !feedback.trim()) {
                await window.showAlert('Rejection reason is required.', 'warning');
                return;
            }
            // Store for fetch below
            window._rejectFeedback = feedback.trim();
        }

        const body = document.getElementById('proofReviewBody');
        body.innerHTML = '<div class="contracts-loading"><div class="spinner"></div><p>Processing...</p></div>';

        try {
            const apiAction = action === 'approve' ? 'approve_milestone' : 'reject_milestone';
            const payload = { milestone_id: currentReviewMilestoneId };
            if (action === 'reject') payload.reason = window._rejectFeedback;

            const res = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: apiAction, ...payload })
            });
            const json = await res.json();

            if (json.success) {
                let msg = `Stage ${action === 'approve' ? 'approved' : 'rejected'} successfully.`;
                if (json.billed_amount > 0) {
                    msg += `\n\n${formatCurrency(json.billed_amount)} has been recorded as paid.`;
                }
                await window.showAlert(msg, 'success', 'Success');
                closeProofModal();
                loadContracts();
            } else {
                await window.showAlert('Error: ' + (json.message || 'Unknown error'), 'danger', 'Error');
                closeProofModal();
            }
        } catch (err) {
            console.error(err);
            await window.showAlert('Network error. Please try again.', 'danger', 'Error');
            closeProofModal();
        }
    };

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
