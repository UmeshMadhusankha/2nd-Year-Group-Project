document.addEventListener('DOMContentLoaded', () => {
    const API = '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php';
    const DEBUG_QUOTES = true;

    const quotesList = document.getElementById('quotesList');
    const pendingBadge = document.getElementById('quotesPendingBadge');
    const statusFilter = document.getElementById('quoteStatusFilter');
    const loadMoreBtn = document.getElementById('loadMoreQuotesBtn');

    let offset = 0;
    const limit = 10;
    let isLoading = false;

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatMoney(value) {
        const n = Number(value);
        if (!Number.isFinite(n)) return 'LKR 0';
        return `LKR ${n.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    }

    function setPending(count) {
        const c = Number.isFinite(count) ? count : 0;
        if (!pendingBadge) return;
        if (c > 0) {
            pendingBadge.textContent = `${c} new`;
            pendingBadge.style.display = 'inline-block';
        } else {
            pendingBadge.style.display = 'none';
        }
    }

    async function fetchJson(url, options) {
        if (DEBUG_QUOTES) {
            console.log('[QuotesReceived] Request:', url, options?.method || 'GET');
        }

        const res = await fetch(url, { credentials: 'same-origin', ...(options || {}) });
        const text = await res.text();
        if (DEBUG_QUOTES) {
            console.log('[QuotesReceived] Response status:', res.status);
            console.log('[QuotesReceived] Response body (first 300 chars):', text.slice(0, 300));
        }
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid JSON');
        }
        if (!res.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${res.status})`);
        }
        return data;
    }

    function renderQuoteItem(q) {
        const providerName = q.provider_name || (q.source === 'company' ? 'Company' : 'Repairer');
        const providerTypeLabel = q.source === 'company' ? 'Company' : 'Individual';
        const jobTitle = q.job_title || 'Job';
        const amount = formatMoney(q.amount);

        const canRespond = q.status === 'pending';
        const acceptDisabled = canRespond ? '' : 'disabled';
        const declineDisabled = canRespond ? '' : 'disabled';

        return `
            <div class="quote-item" data-source="${escapeHtml(q.source)}" data-quote-id="${escapeHtml(q.quote_id)}">
                <div class="quote-provider">
                    <img src="${escapeHtml(q.provider_avatar || 'https://via.placeholder.com/40')}" alt="Provider" class="provider-avatar">
                    <div class="provider-info">
                        <span class="provider-name">${escapeHtml(providerName)}</span>
                        <span class="provider-type">${escapeHtml(providerTypeLabel)}</span>
                    </div>
                </div>
                <div class="quote-details">
                    <span class="quote-amount">${escapeHtml(amount)}</span>
                    <span class="quote-job">${escapeHtml(jobTitle)}</span>
                </div>
                <div class="quote-actions">
                    <button class="btn-success-sm" data-action="accepted" ${acceptDisabled}>Accept</button>
                    <button class="btn-outline-sm" data-action="rejected" ${declineDisabled}>Decline</button>
                </div>
            </div>
        `;
    }

    function setLoadingState() {
        if (!quotesList) return;
        quotesList.innerHTML = `
            <div class="quote-item">
                <div class="quote-details">
                    <span class="quote-job">Loading...</span>
                </div>
            </div>
        `;
    }

    function setEmptyState() {
        if (!quotesList) return;
        quotesList.innerHTML = `
            <div class="quote-item">
                <div class="quote-details">
                    <span class="quote-job">No quotes found</span>
                </div>
            </div>
        `;
    }

    async function loadQuotes(reset = false) {
        if (isLoading) return;
        isLoading = true;

        if (reset) {
            offset = 0;
            setLoadingState();
        }

        const status = statusFilter?.value || '';
        const url = `${API}?action=list&limit=${limit}&offset=${offset}${status ? `&status=${encodeURIComponent(status)}` : ''}`;

        try {
            const data = await fetchJson(url);
            if (DEBUG_QUOTES) {
                console.log('[QuotesReceived] list payload:', data);
            }
            setPending(parseInt(data.pending_count, 10) || 0);

            const quotes = Array.isArray(data.quotes) ? data.quotes : [];
            if (reset) {
                if (quotes.length === 0) {
                    setEmptyState();
                } else {
                    quotesList.innerHTML = quotes.map(renderQuoteItem).join('');
                }
            } else {
                if (quotes.length > 0) {
                    quotesList.insertAdjacentHTML('beforeend', quotes.map(renderQuoteItem).join(''));
                }
            }

            offset += quotes.length;

            if (loadMoreBtn) {
                loadMoreBtn.style.display = quotes.length === limit ? 'inline-flex' : 'none';
            }
        } catch (e) {
            console.error('[QuotesReceived] Failed to load quotes:', e);
            if (reset) {
                quotesList.innerHTML = `
                    <div class="quote-item">
                        <div class="quote-details">
                            <span class="quote-job">Failed to load quotes</span>
                        </div>
                    </div>
                `;
            }
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
        } finally {
            isLoading = false;
        }
    }

    async function respondToQuote(source, quoteId, decision, buttonEl) {
        try {
            if (buttonEl) buttonEl.disabled = true;
            await fetchJson(`${API}?action=respond`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ source, quote_id: Number(quoteId), decision })
            });

            // refresh list (simple + safe)
            await loadQuotes(true);
        } catch (e) {
            if (buttonEl) buttonEl.disabled = false;
            alert(e.message || 'Failed to update quote');
        }
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', () => loadQuotes(true));
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => loadQuotes(false));
    }

    if (quotesList) {
        quotesList.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;

            const item = e.target.closest('.quote-item');
            if (!item) return;

            const source = item.getAttribute('data-source');
            const quoteId = item.getAttribute('data-quote-id');
            const action = btn.getAttribute('data-action');

            if (!source || !quoteId || !action) return;

            respondToQuote(source, quoteId, action, btn);
        });
    }

    loadQuotes(true);
});
