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

    const API = '/2nd-Year-Group-Project/FixLanka/api/chat.php';
    const POLL_INTERVAL = 5000; // 5 seconds

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
            if (e.target === _overlay) close();
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

    // ── Public: Open chat ──
    function open(contractId, opts = {}) {
        _ensureDOM();
        _contractId = contractId;
        _lastMessageId = 0;
        _userRole = null;

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

        // Load messages
        _loadMessages(true);

        // Start polling
        _startPolling();
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

            if (msg.message_type === 'system') {
                div.className = 'chat-msg system';
                div.textContent = msg.message;
            } else {
                const isMine = msg.sender_type === _userRole;
                div.className = `chat-msg ${isMine ? 'sent' : 'received'}`;
                div.innerHTML = `
                    <div class="chat-msg-text">${_escapeHtml(msg.message)}</div>
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
    return { open, close };
})();
