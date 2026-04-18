/**
 * Phase 6: Contract Chat Widget
 * Polling-based chat for contract communication.
 *
 * Usage:
 *   ChatWidget.open(contractId, { name, contractNumber });
 *   ChatWidget.close();
 */
const ChatWidget = (() => {
    // ── State ──
    let _contractId = null;
    let _lastMessageId = 0;
    let _pollTimer = null;
    let _userRole = null; // 'customer' | 'company'
    let _isOpen = false;
    let _changeRequestsById = new Map();

    const API = '/2nd-Year-Group-Project/FixLanka/api/chat.php';
    const CONTRACT_API = '/2nd-Year-Group-Project/FixLanka/api/contracts.php';
    const POLL_INTERVAL = 5000; // 5 seconds

    const CR_PREFIX = '__CONTRACT_CHANGE_REQUEST__:';
    const CR_RESP_PREFIX = '__CONTRACT_CHANGE_RESPONSE__:';

    // Change request preview modal DOM
    let _crOverlay = null;
    let _crBody = null;
    let _crTitle = null;
    let _crCloseBtn = null;
    let _crFooter = null;

    // ── DOM references (lazy) ──
    let _overlay, _messagesEl, _inputEl, _sendBtn,
        _headerName, _headerContract, _headerAvatar,
        _emptyState, _inactiveBanner;

    /**
     * Build the chat modal HTML and inject it into the page (once).
     */
    function _ensureDOM() {
        if (document.getElementById('chatModalOverlay')) {
            _bindDOM();
            _ensureChangeRequestPreviewDOM();
            return;
        }

        const html = `
        <div class="chat-modal-overlay" id="chatModalOverlay">
            <div class="chat-container">
                <div class="chat-header">
                    <div class="chat-header-info">
                        <div class="chat-header-avatar" id="chatHeaderAvatar">?</div>
                        <div class="chat-header-details">
                            <div class="chat-header-name" id="chatHeaderName">Chat</div>
                            <div class="chat-header-contract" id="chatHeaderContract"></div>
                        </div>
                    </div>
                    <button class="chat-close-btn" id="chatCloseBtn" title="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="chat-inactive-banner" id="chatInactiveBanner" style="display:none">
                    <i class="fas fa-lock"></i>
                    <h4>Chat Not Active</h4>
                    <p>Chat will become available once the contract is sent and accepted.</p>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <div class="chat-empty-state" id="chatEmptyState">
                        <i class="fas fa-comments"></i>
                        <p>No messages yet.<br>Start the conversation!</p>
                    </div>
                </div>

                <div class="chat-input-area" id="chatInputArea">
                    <textarea class="chat-input" id="chatInput"
                              placeholder="Type a message..." rows="1"></textarea>
                    <button class="chat-send-btn" id="chatSendBtn" title="Send">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);
        _bindDOM();
        _attachEvents();
        _ensureChangeRequestPreviewDOM();
    }

    function _bindDOM() {
        _overlay = document.getElementById('chatModalOverlay');
        _messagesEl = document.getElementById('chatMessages');
        _inputEl = document.getElementById('chatInput');
        _sendBtn = document.getElementById('chatSendBtn');
        _headerName = document.getElementById('chatHeaderName');
        _headerContract = document.getElementById('chatHeaderContract');
        _headerAvatar = document.getElementById('chatHeaderAvatar');
        _emptyState = document.getElementById('chatEmptyState');
        _inactiveBanner = document.getElementById('chatInactiveBanner');
    }

    function _attachEvents() {
        // Close
        document.getElementById('chatCloseBtn').addEventListener('click', close);
        _overlay.addEventListener('click', e => {
            // In inline/embedded mode we don't want backdrop-click to close
            if (e.target === _overlay && _overlay.getAttribute('data-inline') !== '1') close();
        });

        // Send on click
        _sendBtn.addEventListener('click', _sendMessage);

        // Send on Enter (Shift+Enter for newline)
        _inputEl.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                _sendMessage();
            }
        });

        // Auto-resize textarea
        _inputEl.addEventListener('input', () => {
            _inputEl.style.height = 'auto';
            _inputEl.style.height = Math.min(_inputEl.scrollHeight, 100) + 'px';
        });
    }

    function _ensureChangeRequestPreviewDOM() {
        if (document.getElementById('chatCrPreviewOverlay')) {
            _bindChangeRequestPreviewDOM();
            return;
        }

        const html = `
        <div class="chat-cr-preview-overlay" id="chatCrPreviewOverlay" aria-hidden="true">
            <div class="chat-cr-preview" role="dialog" aria-modal="true" aria-labelledby="chatCrPreviewTitle">
                <div class="chat-cr-preview-header">
                    <div>
                        <div class="chat-cr-preview-title" id="chatCrPreviewTitle">Contract changes</div>
                        <div class="chat-cr-preview-sub" id="chatCrPreviewSub"></div>
                    </div>
                    <button type="button" class="chat-cr-preview-close" id="chatCrPreviewClose" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="chat-cr-preview-body" id="chatCrPreviewBody"></div>
                <div class="chat-cr-preview-footer" id="chatCrPreviewFooter">
                    <button type="button" class="chat-cr-btn" id="chatCrPreviewCloseBtn">Close</button>
                </div>
            </div>
        </div>`;

        document.body.insertAdjacentHTML('beforeend', html);
        _bindChangeRequestPreviewDOM();

        _crOverlay.addEventListener('click', (e) => {
            if (e.target === _crOverlay) _closeChangeRequestPreview();
        });
        _crCloseBtn.addEventListener('click', _closeChangeRequestPreview);
        document.getElementById('chatCrPreviewCloseBtn').addEventListener('click', _closeChangeRequestPreview);
    }

    function _bindChangeRequestPreviewDOM() {
        _crOverlay = document.getElementById('chatCrPreviewOverlay');
        _crBody = document.getElementById('chatCrPreviewBody');
        _crTitle = document.getElementById('chatCrPreviewSub');
        _crCloseBtn = document.getElementById('chatCrPreviewClose');
        _crFooter = document.getElementById('chatCrPreviewFooter');
    }

    function _closeChangeRequestPreview() {
        if (!_crOverlay) return;
        _crOverlay.classList.remove('active');
        _crOverlay.setAttribute('aria-hidden', 'true');
        if (_crBody) _crBody.innerHTML = '';
    }

    function _openChangeRequestPreview(changeRequestId) {
        if (!changeRequestId || !_crOverlay || !_crBody) return;
        _crOverlay.classList.add('active');
        _crOverlay.setAttribute('aria-hidden', 'false');
        _crBody.innerHTML = `<div class="chat-cr-preview-loading">Loading…</div>`;

        _loadChangeRequestPreview(changeRequestId);
    }

    async function _loadChangeRequestPreview(changeRequestId) {
        try {
            const res = await fetch(`${CONTRACT_API}?action=get_contract_change_preview&change_request_id=${encodeURIComponent(changeRequestId)}`);
            const json = await res.json();
            if (!json.success) {
                _crBody.innerHTML = `<div class="chat-cr-preview-error">${_escapeHtml(json.message || 'Failed to load preview')}</div>`;
                return;
            }

            const payload = json.data || {};
            const cr = payload.change_request || {};
            const contract = payload.contract || {};
            const proposed = payload.proposed_changes || null;
            const diff = payload.diff || {};

            const contractNumber = contract.contract_number || `Contract #${contract.contract_id || ''}`;
            const companyName = contract.company_name || '';
            if (_crTitle) _crTitle.textContent = `${contractNumber}${companyName ? ' • ' + companyName : ''}`;

            const effective = Object.assign({}, contract);
            if (proposed && typeof proposed === 'object') {
                if (proposed.start_date != null) effective.start_date = proposed.start_date;
                if (proposed.end_date != null) effective.end_date = proposed.end_date;
                if (proposed.payment_method != null) effective.payment_method = proposed.payment_method;
                if (Array.isArray(proposed.milestones)) effective.milestones = proposed.milestones;
            }

            const status = String(cr.status || 'pending');
            _crBody.innerHTML = _renderChangePreview(effective, cr, diff, proposed);

            // Footer actions
            _crFooter.innerHTML = `<button type="button" class="chat-cr-btn" id="chatCrPreviewCloseBtn2">Close</button>`;
            document.getElementById('chatCrPreviewCloseBtn2').addEventListener('click', _closeChangeRequestPreview);

            if (_userRole === 'company' && status === 'pending') {
                const rejectBtn = document.createElement('button');
                rejectBtn.type = 'button';
                rejectBtn.className = 'chat-cr-btn reject';
                rejectBtn.textContent = 'Reject';
                rejectBtn.addEventListener('click', async () => {
                    const note = (typeof window.showPrompt === 'function')
                        ? await window.showPrompt('Reason for rejection (optional):', '', { title: 'Reject Change Request', placeholder: 'Add a short reason (optional)...', confirmText: 'Reject', type: 'warning', icon: 'fas fa-times-circle' })
                        : (prompt('Reason for rejection (optional):') || '');

                    // Cancelled
                    if (note === null) return;

                    rejectBtn.disabled = true;
                    const ok = await _respondToChangeRequest(changeRequestId, 'rejected', String(note || ''));
                    if (ok) _closeChangeRequestPreview();
                });

                const acceptBtn = document.createElement('button');
                acceptBtn.type = 'button';
                acceptBtn.className = 'chat-cr-btn accept';
                acceptBtn.textContent = 'Accept';
                acceptBtn.addEventListener('click', async () => {
                    acceptBtn.disabled = true;
                    await _respondToChangeRequest(changeRequestId, 'accepted');
                    _closeChangeRequestPreview();
                });

                _crFooter.insertBefore(rejectBtn, _crFooter.firstChild);
                _crFooter.insertBefore(acceptBtn, _crFooter.firstChild);
            }
        } catch (e) {
            _crBody.innerHTML = `<div class="chat-cr-preview-error">Network error. Please try again.</div>`;
        }
    }

    function _renderChangePreview(contract, cr, diff, proposedChanges) {
        const fields = (diff && diff.fields) ? diff.fields : {};
        const msDiff = Array.isArray(diff && diff.milestones) ? diff.milestones : [];
        const msChangedByIndex = new Map();
        msDiff.forEach(d => {
            if (d && typeof d.index === 'number') msChangedByIndex.set(d.index, d.changed || {});
        });

        const h = (s) => _escapeHtml(String(s ?? ''));
        const markIf = (key, val) => {
            const v = h(val || '—');
            return fields && fields[key] ? `<span class="cr-diff">${v}</span>` : v;
        };

        const requestText = h((cr && cr.request_text) || '—');
        const status = h((cr && cr.status) || 'pending');

        const start = markIf('start_date', contract.start_date ? String(contract.start_date).slice(0, 10) : '—');
        const end = markIf('end_date', contract.end_date ? String(contract.end_date).slice(0, 10) : '—');

        const milestones = Array.isArray(contract.milestones) ? contract.milestones : [];
        const proposals = (proposedChanges && Array.isArray(proposedChanges.unit_rate_proposals)) ? proposedChanges.unit_rate_proposals : [];
        const hasUnitPricing = milestones.some(m => {
            const ul = m?.unit_label ?? null;
            const ur = m?.unit_rate ?? null;
            return (ul != null && String(ul).trim() !== '') || (ur != null && ur !== '');
        });
        const isUnitNegotiation = proposals.length > 0 || hasUnitPricing;

        let msHtml = '';
        if (isUnitNegotiation) {
            const byId = new Map();
            const byNum = new Map();
            proposals.forEach(p => {
                if (p && p.milestone_id != null) byId.set(String(p.milestone_id), p);
                if (p && p.milestone_number != null) byNum.set(String(p.milestone_number), p);
            });

            const unitMs = milestones.filter(m => {
                const ul = m?.unit_label ?? null;
                const ur = m?.unit_rate ?? null;
                return (ul != null && String(ul).trim() !== '') || (ur != null && ur !== '');
            });

            if (!unitMs.length) {
                msHtml = '<div class="chat-cr-muted">No unit-priced milestones found.</div>';
            } else {
                msHtml += '<table class="chat-cr-ms-table"><thead><tr><th>#</th><th>Item</th><th>Unit</th><th>Current rate</th><th>Proposed rate</th></tr></thead><tbody>';
                unitMs.forEach((m, idx) => {
                    const idKey = m.milestone_id != null ? String(m.milestone_id) : '';
                    const numKey = m.milestone_number != null ? String(m.milestone_number) : '';
                    const p = (idKey && byId.get(idKey)) || (numKey && byNum.get(numKey)) || null;

                    const title = h(m.title || m.milestone_name || '—');
                    const unitLabel = h((m.unit_label || '').trim() || '—');
                    const curRate = (m.unit_rate != null && m.unit_rate !== '') ? `Rs. ${Number(m.unit_rate).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : '—';
                    const propRateRaw = p && p.proposed_unit_rate != null ? Number(p.proposed_unit_rate) : null;
                    const propRate = (propRateRaw != null && Number.isFinite(propRateRaw))
                        ? `<span class="cr-diff">Rs. ${propRateRaw.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`
                        : '—';

                    msHtml += `<tr class="chat-cr-ms-row${p ? ' changed' : ''}"><td>${idx + 1}</td><td>${title}</td><td>${unitLabel}</td><td>${h(curRate)}</td><td>${propRate}</td></tr>`;
                });
                msHtml += '</tbody></table>';
            }
        } else {
            if (milestones.length) {
                msHtml += '<table class="chat-cr-ms-table"><thead><tr><th>#</th><th>Milestone</th><th>Due</th><th>%</th><th>Amount</th></tr></thead><tbody>';
                milestones.forEach((m, idx) => {
                    const changed = msChangedByIndex.get(idx) || {};
                    const rowChanged = !!(changed && Object.keys(changed).length);
                    const title = changed.title ? `<span class="cr-diff">${h(m.title || m.milestone_name || '')}</span>` : h(m.title || m.milestone_name || '—');
                    const due = changed.due_date ? `<span class="cr-diff">${h(m.due_date ? String(m.due_date).slice(0, 10) : '—')}</span>` : h(m.due_date ? String(m.due_date).slice(0, 10) : '—');
                    const amtVal = (m.amount ?? m.payment_amount ?? null);
                    const pctRaw = (m.percentage ?? m.payment_percentage ?? null);
                    const pctNum = (pctRaw != null && pctRaw !== '' && Number.isFinite(Number(pctRaw))) ? Number(pctRaw) : null;
                    const pctText = (pctNum != null && Number.isFinite(pctNum)) ? `${pctNum.toFixed(2)}%` : '—';
                    const pct = changed.percentage ? `<span class="cr-diff">${h(pctText)}</span>` : h(pctText);
                    const amt = changed.amount ? `<span class="cr-diff">${h(amtVal != null ? `Rs. ${Number(amtVal).toLocaleString()}` : '—')}</span>` : h(amtVal != null ? `Rs. ${Number(amtVal).toLocaleString()}` : '—');
                    msHtml += `<tr class="chat-cr-ms-row${rowChanged ? ' changed' : ''}"><td>${idx + 1}</td><td>${title}</td><td>${due}</td><td>${pct}</td><td>${amt}</td></tr>`;
                    if ((changed.description) && (m.description || m.milestone_description)) {
                        msHtml += `<tr class="chat-cr-ms-desc"><td></td><td colspan="4"><span class="cr-diff">${h(m.description || m.milestone_description)}</span></td></tr>`;
                    }
                });
                msHtml += '</tbody></table>';
            } else {
                msHtml = '<div class="chat-cr-muted">No milestones provided.</div>';
            }
        }

        return `
        <div class="chat-cr-doc">
            <div class="chat-cr-doc-top">
                <div>
                    <div class="chat-cr-doc-k">Status</div>
                    <div class="chat-cr-doc-v"><span class="chat-cr-badge ${status}">${status}</span></div>
                </div>
                <div>
                    <div class="chat-cr-doc-k">Customer note</div>
                    <div class="chat-cr-doc-v">${requestText || '—'}</div>
                </div>
            </div>

            <div class="chat-cr-doc-grid">
                <div>
                    <div class="chat-cr-doc-k">Start date</div>
                    <div class="chat-cr-doc-v">${start}</div>
                </div>
                <div>
                    <div class="chat-cr-doc-k">End date</div>
                    <div class="chat-cr-doc-v">${end}</div>
                </div>
            </div>

            <div class="chat-cr-doc-sec">
                <div class="chat-cr-doc-k">${isUnitNegotiation ? 'Unit rates' : 'Milestones'}</div>
                ${msHtml}
            </div>
        </div>`;
    }

    // ── Public: Open chat ──
    function open(contractId, opts = {}) {
        _ensureDOM();

        // Reset any inline mode (callers can re-enable after open if needed)
        if (_overlay) {
            _overlay.classList.remove('inline');
            _overlay.removeAttribute('data-inline');
        }
        _contractId = contractId;
        _lastMessageId = 0;
        _userRole = null;
        _changeRequestsById = new Map();

        // Set header
        const name = opts.name || opts.client_name || 'Chat';
        const contractNum = opts.contractNumber || opts.contract_number || `Contract #${contractId}`;
        _headerName.textContent = name;
        _headerContract.textContent = contractNum;
        _headerAvatar.textContent = name.charAt(0).toUpperCase();

        // Reset UI
        _messagesEl.innerHTML = '';
        _emptyState && (_emptyState.style.display = 'none');
        _inactiveBanner.style.display = 'none';
        document.getElementById('chatInputArea').style.display = 'flex';
        _inputEl.value = '';

        // Show overlay
        _overlay.classList.add('active');
        _isOpen = true;

        // Load change requests (for accurate status rendering), then messages
        _loadChangeRequests().finally(() => {
            _loadMessages(true);
        });

        // Start polling
        _startPolling();
    }

    // Optional: Render chat inside a specific element instead of full-screen modal.
    // This reuses the same DOM/CSS and API, just changes how the overlay is positioned.
    function mountInline(contractId, mountEl, opts = {}) {
        if (!mountEl) return;
        open(contractId, opts);

        const overlay = document.getElementById('chatModalOverlay');
        if (!overlay) return;

        // Move overlay inside provided container
        mountEl.innerHTML = '';
        mountEl.appendChild(overlay);
        overlay.classList.add('inline');
        overlay.setAttribute('data-inline', '1');
        overlay.classList.add('active');
    }

    async function _loadChangeRequests() {
        if (!_contractId) return;
        try {
            const res = await fetch(`${CONTRACT_API}?action=list_contract_changes&contract_id=${_contractId}`);
            const data = await res.json();
            if (!data.success || !Array.isArray(data.data)) return;

            const map = new Map();
            data.data.forEach(r => {
                const id = Number(r.change_request_id);
                if (!Number.isNaN(id)) map.set(id, r);
            });
            _changeRequestsById = map;
        } catch (e) {
            // Non-fatal
        }
    }

    // ── Public: Close chat ──
    function close() {
        if (!_isOpen) return;
        _overlay.classList.remove('active');
        _isOpen = false;
        _stopPolling();
        _contractId = null;
    }

    // ── Load messages from API ──
    async function _loadMessages(initial = false) {
        if (!_contractId) return;

        try {
            const url = `${API}?action=get_messages&contract_id=${_contractId}&since_id=${_lastMessageId}`;
            const res = await fetch(url);
            const data = await res.json();

            if (!data.success) {
                console.warn('Chat load error:', data.message);
                return;
            }

            // Chat not active?
            if (data.chat_active === false) {
                _inactiveBanner.style.display = 'flex';
                _messagesEl.style.display = 'none';
                document.getElementById('chatInputArea').style.display = 'none';
                return;
            }

            _messagesEl.style.display = 'flex';
            _userRole = data.user_role;

            if (data.messages && data.messages.length > 0) {
                _emptyState && (_emptyState.style.display = 'none');
                _renderMessages(data.messages, initial);
                _lastMessageId = data.messages[data.messages.length - 1].chat_id;
            } else if (initial && _messagesEl.children.length === 0) {
                // Show empty state on initial load only
                if (_emptyState) {
                    _messagesEl.appendChild(_emptyState);
                    _emptyState.style.display = 'flex';
                }
            }
        } catch (err) {
            console.error('Chat fetch error:', err);
        }
    }

    // ── Render messages ──
    function _renderMessages(messages, scrollToBottom = true) {
        let lastDate = null;

        messages.forEach(msg => {
            // Date divider
            const msgDate = _parseTS(msg.created_at).toLocaleDateString();
            if (msgDate !== lastDate) {
                lastDate = msgDate;
                const divider = document.createElement('div');
                divider.className = 'chat-date-divider';
                divider.innerHTML = `<span>${_formatDate(msg.created_at)}</span>`;
                _messagesEl.appendChild(divider);
            }

            const div = document.createElement('div');

            const rawText = String(msg.message ?? '');
            const msgType = msg.message_type || msg.attachment_type || 'text';

            // Change request card
            if (rawText.startsWith(CR_PREFIX)) {
                div.className = 'chat-msg system';
                div.appendChild(_buildChangeRequestCard(rawText));
            }
            // Change request response (system notice)
            else if (rawText.startsWith(CR_RESP_PREFIX)) {
                div.className = 'chat-msg system';
                div.textContent = _formatChangeRequestResponse(rawText);
            }
            else if (msgType === 'system') {
                div.className = 'chat-msg system';
                div.textContent = rawText;
            } else {
                const isMine = msg.sender_type === _userRole;
                div.className = `chat-msg ${isMine ? 'sent' : 'received'}`;
                div.innerHTML = `
                    <div class="chat-msg-text">${_escapeHtml(rawText)}</div>
                    <span class="chat-msg-time">${_formatTime(msg.created_at)}</span>
                `;
            }

            div.dataset.chatId = msg.chat_id;
            _messagesEl.appendChild(div);
        });

        if (scrollToBottom) {
            requestAnimationFrame(() => {
                _messagesEl.scrollTop = _messagesEl.scrollHeight;
            });
        }
    }

    function _safeJsonParse(text) {
        try {
            return JSON.parse(text);
        } catch (e) {
            return null;
        }
    }

    function _buildChangeRequestCard(rawText) {
        const payloadRaw = rawText.slice(CR_PREFIX.length);
        const payload = _safeJsonParse(payloadRaw) || {};

        const idCandidate = payload.change_request_id ?? payload.id ?? payload.request_id ?? payload.changeRequestId;
        let changeRequestId = Number(idCandidate);

        // Back-compat: if payload isn't JSON, try extracting the first integer ID
        let legacyRequestText = '';
        if (Number.isNaN(changeRequestId)) {
            const m = String(payloadRaw || '').match(/^(?:\s*#?)(\d+)\s*(?:[|:\-–—]|\s)\s*(.*)$/);
            if (m) {
                changeRequestId = Number(m[1]);
                legacyRequestText = (m[2] || '').trim();
            } else {
                const anyId = String(payloadRaw || '').match(/\b(\d+)\b/);
                if (anyId) changeRequestId = Number(anyId[1]);
            }
        }

        const requestText = String(payload.request_text ?? payload.text ?? legacyRequestText ?? '').trim();

        const row = _changeRequestsById.get(changeRequestId);
        const status = String((row && row.status) || payload.status || 'pending');

        const wrapper = document.createElement('div');
        wrapper.className = 'chat-change-request';

        const header = document.createElement('div');
        header.className = 'chat-change-request-header';
        header.innerHTML = `
            <div class="chat-cr-title">Contract adjustment request</div>
            <span class="chat-cr-badge ${_escapeHtml(status)}">${_escapeHtml(status)}</span>
        `;

        const body = document.createElement('div');
        body.className = 'chat-change-request-body';
        body.textContent = requestText || '—';

        const actions = document.createElement('div');
        actions.className = 'chat-change-request-actions';

        if (!Number.isNaN(changeRequestId) && changeRequestId > 0) {
            const viewBtn = document.createElement('button');
            viewBtn.type = 'button';
            viewBtn.className = 'chat-cr-btn view';
            viewBtn.textContent = 'View changes';
            viewBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                _openChangeRequestPreview(changeRequestId);
            });
            actions.appendChild(viewBtn);

            // Make the card itself clickable too (matches user expectation)
            wrapper.style.cursor = 'pointer';
            wrapper.addEventListener('click', (e) => {
                if (e.target && (e.target.closest('button'))) return;
                _openChangeRequestPreview(changeRequestId);
            });
        }

        if (_userRole === 'company' && status === 'pending' && !Number.isNaN(changeRequestId) && changeRequestId > 0) {
            const acceptBtn = document.createElement('button');
            acceptBtn.type = 'button';
            acceptBtn.className = 'chat-cr-btn accept';
            acceptBtn.textContent = 'Accept';

            const rejectBtn = document.createElement('button');
            rejectBtn.type = 'button';
            rejectBtn.className = 'chat-cr-btn reject';
            rejectBtn.textContent = 'Reject';

            acceptBtn.addEventListener('click', async () => {
                acceptBtn.disabled = true;
                rejectBtn.disabled = true;
                await _respondToChangeRequest(changeRequestId, 'accepted');
            });

            rejectBtn.addEventListener('click', async () => {
                const note = (typeof window.showPrompt === 'function')
                    ? await window.showPrompt('Reason for rejection (optional):', '', { title: 'Reject Change Request', placeholder: 'Add a short reason (optional)...', confirmText: 'Reject', type: 'warning', icon: 'fas fa-times-circle' })
                    : (prompt('Reason for rejection (optional):') || '');

                // Cancelled
                if (note === null) return;

                acceptBtn.disabled = true;
                rejectBtn.disabled = true;
                await _respondToChangeRequest(changeRequestId, 'rejected', String(note || ''));
            });

            actions.appendChild(rejectBtn);
            actions.appendChild(acceptBtn);
        }

        wrapper.appendChild(header);
        wrapper.appendChild(body);
        if (actions.childNodes.length) wrapper.appendChild(actions);

        return wrapper;
    }

    function _formatChangeRequestResponse(rawText) {
        const payloadRaw = rawText.slice(CR_RESP_PREFIX.length);
        const payload = _safeJsonParse(payloadRaw) || {};
        const decision = payload.decision ? String(payload.decision) : 'updated';
        const id = payload.change_request_id ? `#${payload.change_request_id}` : '';
        return `Change request ${id} ${decision}.`;
    }

    async function _respondToChangeRequest(changeRequestId, decision, responseNote = '') {
        try {
            const res = await fetch(CONTRACT_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'respond_contract_change',
                    change_request_id: changeRequestId,
                    decision,
                    response_note: responseNote
                })
            });
            const data = await res.json();
            if (!data.success) {
                if (typeof window.showAlert === 'function') {
                    await window.showAlert(data.message || 'Failed to update change request', 'danger', 'Error');
                } else {
                    alert(data.message || 'Failed to update change request');
                }
                return false;
            }

            // Refresh status cache and messages
            await _loadChangeRequests();
            await _loadMessages(false);
        } catch (e) {
            if (typeof window.showAlert === 'function') {
                await window.showAlert('Network error. Please try again.', 'danger', 'Error');
            } else {
                alert('Network error. Please try again.');
            }
            return false;
        }

        return true;
    }

    // ── Send message ──
    async function _sendMessage() {
        const text = _inputEl.value.trim();
        if (!text || !_contractId) return;

        _sendBtn.disabled = true;
        _inputEl.value = '';
        _inputEl.style.height = 'auto';

        try {
            const res = await fetch(API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'send',
                    contract_id: _contractId,
                    message: text
                })
            });
            const data = await res.json();

            if (!data.success) {
                console.error('Send failed:', data.message);
                _inputEl.value = text; // restore
                if (typeof showNotification === 'function') {
                    showNotification('Failed to send message', 'error');
                }
            } else {
                // Immediately poll to show the new message
                await _loadMessages();
            }
        } catch (err) {
            console.error('Send error:', err);
            _inputEl.value = text;
        } finally {
            _sendBtn.disabled = false;
            _inputEl.focus();
        }
    }

    // ── Polling ──
    function _startPolling() {
        _stopPolling();
        _pollTimer = setInterval(() => {
            if (_isOpen && _contractId) {
                _loadMessages();
            }
        }, POLL_INTERVAL);
    }

    function _stopPolling() {
        if (_pollTimer) {
            clearInterval(_pollTimer);
            _pollTimer = null;
        }
    }

    // ── Helpers ──
    function _escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Parse MySQL timestamp (YYYY-MM-DD HH:MM:SS) into a Date
    function _parseTS(ts) {
        if (!ts) return new Date();
        // Replace space with T for ISO compatibility
        return new Date(ts.replace(' ', 'T'));
    }

    function _formatTime(ts) {
        const d = _parseTS(ts);
        return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    function _formatDate(ts) {
        const d = _parseTS(ts);
        const today = new Date();
        const yesterday = new Date();
        yesterday.setDate(today.getDate() - 1);

        if (d.toDateString() === today.toDateString()) return 'Today';
        if (d.toDateString() === yesterday.toDateString()) return 'Yesterday';
        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // ── Public API ──
    return { open, close, mountInline };
})();
