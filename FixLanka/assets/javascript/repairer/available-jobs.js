/**
 * Available Jobs Page JavaScript
 * Fully connected to database via API endpoints.
 * Handles: browsing jobs, filtering, viewing details, submitting/editing/deleting quotes.
 */

// ===== Global State =====
const currentRepairerId = window.CURRENT_REPAIRER_ID || 0;
const API_BASE = '/2nd-Year-Group-Project/FixLanka/api';
let availableJobs = [];
let submittedQuotes = [];
let directJobs = [];
let receivedNegotiations = [];
let acceptedNegotiations = [];

// ===== Initialization =====
document.addEventListener('DOMContentLoaded', function () {
    if (!currentRepairerId) {
        showToast('Session expired. Please log in again.', 'error');
        setTimeout(() => {
            window.location.href = '/2nd-Year-Group-Project/FixLanka/views/auth/login.php';
        }, 1500);
        return;
    }

    initializeTabs();
    initializeFilters();
    loadAvailableJobs();
    loadDirectJobs();
    loadSubmittedQuotations();
    loadReceivedNegotiations();
    loadAcceptedNegotiations();
});

// =========================================================================
//  TABS
// =========================================================================
function initializeTabs() {
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', function () {
            switchTab(this.getAttribute('data-tab'));
        });
    });
}

function switchTab(tabName) {
    document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

    const btn = document.querySelector(`[data-tab="${tabName}"]`);
    const content = document.getElementById(`${tabName}-tab`);
    if (btn) btn.classList.add('active');
    if (content) content.classList.add('active');

    if (tabName === 'submitted-quotes') {
        loadSubmittedQuotations();
    }
    if (tabName === 'direct-jobs') {
        loadDirectJobs();
    }
    if (tabName === 'received-negotiations') {
        loadReceivedNegotiations();
        loadAcceptedNegotiations();
    }
}

// =========================================================================
//  RECEIVED NEGOTIATIONS (READ/UPDATE)
// =========================================================================
async function loadReceivedNegotiations() {
    const container = document.getElementById('received-negotiations-container');
    const countBadge = document.getElementById('received-negotiations-badge');
    const countText = document.getElementById('received-negotiations-count');

    if (!container) return;

    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading received negotiations...</p>
        </div>`;

    try {
        const response = await fetch(`${API_BASE}/repairer-negotiations.php?action=list&limit=100`, {
            credentials: 'same-origin'
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Failed to load negotiations');
        }

        receivedNegotiations = Array.isArray(result.items) ? result.items : [];
        renderReceivedNegotiations(receivedNegotiations);

        const count = receivedNegotiations.length;
        if (countBadge) countBadge.textContent = count;
        if (countText) countText.textContent = `${count} negotiation${count !== 1 ? 's' : ''} waiting`;
    } catch (err) {
        console.error('Error loading received negotiations:', err);
        showContainerError('received-negotiations-container', `Failed to load negotiations. ${err.message || ''}`.trim());
        if (countBadge) countBadge.textContent = '!';
        if (countText) countText.textContent = 'Error loading negotiations';
    }
}

async function loadAcceptedNegotiations() {
    const container = document.getElementById('accepted-negotiations-container');
    const countText = document.getElementById('accepted-negotiations-count');

    if (!container) return;

    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading accepted negotiations...</p>
        </div>`;

    try {
        const response = await fetch(`${API_BASE}/repairer-negotiations.php?action=list&limit=100&status=accepted`, {
            credentials: 'same-origin'
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const result = await response.json();
        if (!result.success) {
            throw new Error(result.message || 'Failed to load history');
        }

        acceptedNegotiations = Array.isArray(result.items) ? result.items : [];
        renderAcceptedNegotiations(acceptedNegotiations);

        if (countText) {
            const count = acceptedNegotiations.length;
            countText.textContent = `${count} negotiation${count !== 1 ? 's' : ''} in history`;
        }
    } catch (err) {
        console.error('Error loading accepted negotiations:', err);
        showContainerError('accepted-negotiations-container', `Failed to load accepted negotiations. ${err.message || ''}`.trim());
        if (countText) countText.textContent = 'Error loading history';
    }
}

function renderAcceptedNegotiations(items) {
    const container = document.getElementById('accepted-negotiations-container');
    if (!container) return;

    if (!Array.isArray(items) || !items.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-clock-rotate-left"></i>
                <h3>No accepted negotiations yet</h3>
                <p>Accepted negotiations will show here as a read-only history.</p>
            </div>`;
        return;
    }

    container.innerHTML = items.map((item) => createNegotiationCard({
        ...item,
        status: 'accepted'
    })).join('');
}

function renderReceivedNegotiations(items) {
    const container = document.getElementById('received-negotiations-container');
    if (!container) return;

    if (!Array.isArray(items) || !items.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-comments-dollar"></i>
                <h3>No pending negotiations</h3>
                <p>User price negotiations sent to you will appear here.</p>
            </div>`;
        return;
    }

    container.innerHTML = items.map(createNegotiationCard).join('');
}

function createNegotiationCard(item) {
    const senderName = item.sender_name || 'User';
    const jobTitle = item.effective_job_title || item.job_title || `Request #${item.request_id}`;
    const jobDescription = item.effective_job_description || item.job_description || '';
    const previousPrice = Number(item.listed_price || 0);
    const proposedPrice = Number(item.proposed_price || 0);
    const counterPrice = Number(item.latest_counter_price || 0);
    const currentQuoteAmount = Number(item.current_quote_amount || 0);
    const canAct = String(item.status || '').toLowerCase() === 'pending' || String(item.status || '').toLowerCase() === 'countered';

    return `
        <div class="quote-card status-pending negotiation-card" data-negotiation-id="${Number(item.negotiation_id)}">
            <div class="quote-header">
                <div class="quote-job-info">
                    <h4 class="quote-job-title">${escapeHtml(jobTitle)}</h4>
                    <p class="quote-job-meta">
                        <i class="fas fa-user"></i> From ${escapeHtml(senderName)}
                        <span class="separator">&bull;</span>
                        <i class="fas fa-hashtag"></i> Negotiation #${Number(item.negotiation_id)}
                        <span class="separator">&bull;</span>
                        <i class="fas fa-file-invoice"></i> Quote #${Number(item.quote_id)}
                    </p>
                </div>
                <div class="quote-status-badge status-pending">
                    <i class="fas fa-comments-dollar"></i>
                    ${escapeHtml(String(item.status || 'pending').toUpperCase())}
                </div>
            </div>

            <div class="quote-body">
                <div class="quote-details-grid">
                    <div class="quote-detail-item">
                        <label>Previous Price</label>
                        <span>LKR ${previousPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>User Proposed</label>
                        <span class="quote-amount">LKR ${proposedPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Current Quote Amount</label>
                        <span>LKR ${currentQuoteAmount.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Our Counter</label>
                        <span>${counterPrice > 0 ? `LKR ${counterPrice.toLocaleString('en-US', { minimumFractionDigits: 2 })}` : escapeHtml(String(item.request_type || 'regular'))}</span>
                    </div>
                </div>

                ${item.message ? `
                <div class="quote-message">
                    <label><i class="fas fa-comment"></i> User Message</label>
                    <p>${escapeHtml(item.message)}</p>
                </div>` : ''}

                ${jobDescription ? `
                <div class="quote-message negotiation-job-description">
                    <label><i class="fas fa-file-lines"></i> Job Description</label>
                    <p>${escapeHtml(jobDescription)}</p>
                </div>` : ''}

                <div class="quote-meta">
                    <span><i class="fas fa-clock"></i> Sent ${formatDate(item.created_at)}</span>
                </div>
            </div>

            <div class="quote-actions">
                ${canAct ? `
                    <button class="btn btn-primary btn-sm" onclick="acceptNegotiation(${Number(item.negotiation_id)})">
                        <i class="fas fa-check"></i> Accept
                    </button>
                    <button class="btn btn-secondary btn-sm" onclick="openCounterNegotiationModal(${Number(item.negotiation_id)})">
                        <i class="fas fa-scale-balanced"></i> Counter
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="rejectNegotiation(${Number(item.negotiation_id)})">
                        <i class="fas fa-xmark"></i> Reject
                    </button>
                ` : `
                    <span class="quote-readonly-label"><i class="fas fa-lock"></i> Already processed</span>
                `}
            </div>
        </div>`;
}

async function acceptNegotiation(negotiationId) {
    if (!Number.isFinite(Number(negotiationId)) || Number(negotiationId) <= 0) {
        showToast('Invalid negotiation selected.', 'error');
        return;
    }

    if (!confirm('Accept this user negotiation proposal?')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/repairer-negotiations.php?action=accept`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ negotiation_id: Number(negotiationId) })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to accept negotiation');
        }

        showToast('Negotiation accepted successfully.', 'success');
        await loadReceivedNegotiations();
    } catch (err) {
        console.error('Error accepting negotiation:', err);
        showToast(err.message || 'Failed to accept negotiation.', 'error');
    }
}

async function rejectNegotiation(negotiationId) {
    if (!Number.isFinite(Number(negotiationId)) || Number(negotiationId) <= 0) {
        showToast('Invalid negotiation selected.', 'error');
        return;
    }

    if (!confirm('This negotiation will be deleted. Continue?')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/repairer-negotiations.php?action=reject`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ negotiation_id: Number(negotiationId) })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to reject negotiation');
        }

        showToast('Negotiation rejected successfully.', 'success');
        await loadReceivedNegotiations();
    } catch (err) {
        console.error('Error rejecting negotiation:', err);
        showToast(err.message || 'Failed to reject negotiation.', 'error');
    }
}

function openCounterNegotiationModal(negotiationId) {
    const modal = document.getElementById('counterNegotiationModal');
    const item = receivedNegotiations.find((entry) => Number(entry.negotiation_id) === Number(negotiationId));
    if (!modal || !item) {
        showToast('Negotiation not found.', 'error');
        return;
    }

    const previousPriceInput = document.getElementById('counterPreviousPrice');
    const proposedByUserInput = document.getElementById('counterProposedByUser');
    const negotiationIdInput = document.getElementById('counterNegotiationId');
    const counterPriceInput = document.getElementById('counterPriceInput');
    const counterMessageInput = document.getElementById('counterMessageInput');

    if (previousPriceInput) previousPriceInput.value = `LKR ${Number(item.listed_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    if (proposedByUserInput) proposedByUserInput.value = `LKR ${Number(item.proposed_price || 0).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
    if (negotiationIdInput) negotiationIdInput.value = String(negotiationId);
    if (counterPriceInput) counterPriceInput.value = Number(item.proposed_price || 0) > 0 ? String(Number(item.proposed_price)) : '';
    if (counterMessageInput) counterMessageInput.value = '';

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeCounterNegotiationModal() {
    const modal = document.getElementById('counterNegotiationModal');
    if (!modal) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

async function submitCounterNegotiation(event) {
    event.preventDefault();

    const negotiationId = Number(document.getElementById('counterNegotiationId')?.value || 0);
    const counterPrice = Number(document.getElementById('counterPriceInput')?.value || 0);
    const message = document.getElementById('counterMessageInput')?.value?.trim() || '';
    const submitBtn = document.getElementById('counterSubmitBtn');

    if (!Number.isFinite(negotiationId) || negotiationId <= 0) {
        showToast('Invalid negotiation selected.', 'error');
        return;
    }
    if (!Number.isFinite(counterPrice) || counterPrice <= 0) {
        showToast('Enter a valid counter amount.', 'warning');
        return;
    }

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    }

    try {
        const response = await fetch(`${API_BASE}/repairer-negotiations.php?action=counter`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({
                negotiation_id: negotiationId,
                counter_price: counterPrice,
                message: message || null,
            })
        });

        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Failed to send counter proposal');
        }

        showToast('Counter proposal sent successfully.', 'success');
        closeCounterNegotiationModal();
        await loadReceivedNegotiations();
    } catch (err) {
        console.error('Error sending counter proposal:', err);
        showToast(err.message || 'Failed to send counter proposal.', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Counter';
        }
    }
}

// =========================================================================
//  FILTERS
// =========================================================================
function initializeFilters() {
    const applyBtn = document.querySelector('.btn-filter.btn-primary');
    const resetBtn = document.querySelector('.btn-filter.btn-secondary');
    if (applyBtn) applyBtn.addEventListener('click', applyFilters);
    if (resetBtn) resetBtn.addEventListener('click', resetFilters);
}

function applyFilters() {
    const filters = {
        category: document.getElementById('category-filter')?.value || '',
        district: document.getElementById('location-filter')?.value || '',
        sort: document.getElementById('sort-filter')?.value || 'newest'
    };
    loadAvailableJobs(filters);
}

function resetFilters() {
    const cat = document.getElementById('category-filter');
    const loc = document.getElementById('location-filter');
    const sort = document.getElementById('sort-filter');
    if (cat) cat.value = '';
    if (loc) loc.value = '';
    if (sort) sort.value = 'newest';
    loadAvailableJobs();
}

// =========================================================================
//  LOAD AVAILABLE JOBS (READ)
// =========================================================================
async function loadAvailableJobs(filters = {}) {
    const container = document.getElementById('jobs-grid-container');
    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading available jobs...</p>
        </div>`;

    try {
        const params = new URLSearchParams();
        if (filters.category) params.append('category', filters.category);
        if (filters.district) params.append('district', filters.district);
        if (filters.sort) params.append('sort', filters.sort);
        params.append('service_provider_type', 'individual');

        const response = await fetch(`${API_BASE}/job-requests.php?${params.toString()}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const result = await response.json();


        if (result.success) {
            availableJobs = result.data || [];
            renderJobs(availableJobs);
            updateJobCounts(result.count || availableJobs.length);
        } else {
            showContainerError('jobs-grid-container', 'Failed to load jobs: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        console.error('Error loading jobs:', err);
        showContainerError('jobs-grid-container', 'Failed to load jobs. ' + err.message);
    }
}

// =========================================================================
//  LOAD DIRECT JOBS (READ)
// =========================================================================
function loadDirectJobs() {
    const container = document.getElementById('direct-jobs-grid-container');
    if (!container) return;

    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading direct jobs...</p>
        </div>`;

    const payload = window.DIRECT_JOBS || {};
    const directSource = Array.isArray(payload.direct) ? payload.direct : [];
    const listedSource = Array.isArray(payload.listed) ? payload.listed : [];

    const normalized = [];
    directSource.forEach(job => normalized.push(normalizeDirectJob(job, 'direct')));
    listedSource.forEach(job => normalized.push(normalizeDirectJob(job, 'listed')));

    const deduped = new Map();
    normalized.forEach(job => {
        if (!job || !job.request_id) return;
        const key = String(job.request_id);
        const existing = deduped.get(key);
        if (!existing || existing.source !== 'direct') {
            deduped.set(key, job);
        }
    });

    directJobs = Array.from(deduped.values()).sort((a, b) => {
        const aTime = new Date(a.dateCreated || a.date_created || 0).getTime();
        const bTime = new Date(b.dateCreated || b.date_created || 0).getTime();
        return bTime - aTime;
    });

    renderDirectJobs(directJobs);
    updateDirectJobCounts(directJobs.length);
}

function renderJobs(jobs) {
    const container = document.getElementById('jobs-grid-container');
    if (!jobs.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No jobs available</h3>
                <p>There are no job requests matching your criteria at the moment.</p>
            </div>`;
        return;
    }
    container.innerHTML = jobs.map(createJobCard).join('');
}

function createJobCard(job) {
    const catClass = getCategoryClass(job.category_name);
    const urgClass = job.urgency === 'urgent' ? 'high' : 'low';
    const urgIcon = job.urgency === 'urgent' ? 'fa-exclamation-circle' : 'fa-info-circle';
    const urgText = job.urgency === 'urgent' ? 'High Priority' : 'Normal Priority';
    const alreadyQuoted = submittedQuotes.some(q => q.request_id == job.request_id);

    return `
        <div class="job-card${alreadyQuoted ? ' already-quoted' : ''}" data-job-id="${job.request_id}">
            ${alreadyQuoted ? `<div class="quoted-ribbon"><i class="fas fa-check-circle"></i> Quote Submitted</div>` : ''}
            <div class="job-header">
                <div class="job-category-badge ${catClass}">
                    <i class="${getCategoryIcon(job.category_name)}"></i>
                    ${escapeHtml(job.category_name || 'General')}
                </div>
                <div class="job-posted">
                    <i class="fas fa-clock"></i>
                    ${escapeHtml(job.posted_ago)}
                </div>
            </div>
            <div class="job-content">
                <h3 class="job-title">${escapeHtml(job.title)}</h3>
                <div class="job-customer">
                    <i class="fas fa-user"></i>
                    <span>${escapeHtml(job.customer_name)}</span>
                </div>
                <div class="job-address">
                    <i class="fas fa-location-dot"></i>
                    <span>${escapeHtml(job.address || job.district || 'N/A')}</span>
                </div>
                <div class="job-urgency ${urgClass}">
                    <i class="fas ${urgIcon}"></i>
                    <span>${urgText}</span>
                </div>
                <div class="job-date">
                    <i class="fas fa-calendar"></i>
                    <span>Finish by: ${formatDate(job.finish_date)}</span>
                </div>
            </div>
            <div class="job-actions">
                <button class="btn btn-secondary job-btn" onclick="viewJobDetails(${job.request_id}, 'available')">
                    <i class="fas fa-eye"></i> View Details
                </button>
                ${alreadyQuoted ? `
                <button class="btn btn-disabled job-btn" disabled>
                    <i class="fas fa-check"></i> Already Quoted
                </button>` : `
                <button class="btn btn-primary job-btn" onclick="openQuoteModal(${job.request_id}, 'available')">
                    <i class="fas fa-file-invoice-dollar"></i> Submit Quote
                </button>`}
            </div>
        </div>`;
}

function renderDirectJobs(jobs) {
    const container = document.getElementById('direct-jobs-grid-container');
    if (!container) return;

    if (!jobs.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-location-arrow"></i>
                <h3>No direct jobs yet</h3>
                <p>Direct requests sent specifically to you will appear here.</p>
            </div>`;
        return;
    }

    container.innerHTML = jobs.map(createDirectJobCard).join('');
}

function createDirectJobCard(job) {
    const catClass = getCategoryClass(job.category_name);
    const alreadyQuoted = submittedQuotes.some(q => q.request_id == job.request_id);
    const requestLabel = job.source === 'direct' ? 'Direct Request' : 'Listed Job Request';
    const requestIcon = job.source === 'direct' ? 'fa-location-arrow' : 'fa-share-square';

    return `
        <div class="job-card${alreadyQuoted ? ' already-quoted' : ''}" data-job-id="${job.request_id}">
            ${alreadyQuoted ? `<div class="quoted-ribbon"><i class="fas fa-check-circle"></i> Quote Submitted</div>` : ''}
            <div class="job-header">
                <div class="job-category-badge ${catClass}">
                    <i class="${getCategoryIcon(job.category_name)}"></i>
                    ${escapeHtml(job.category_name || 'General')}
                </div>
                <div class="job-posted">
                    <i class="fas fa-clock"></i>
                    ${escapeHtml(job.posted_ago || 'Recently')}
                </div>
            </div>
            <div class="job-content">
                <h3 class="job-title">${escapeHtml(job.title)}</h3>
                <div class="job-customer">
                    <i class="fas fa-user"></i>
                    <span>${escapeHtml(job.customer_name || 'Customer')}</span>
                </div>
                <div class="job-address">
                    <i class="fas fa-location-dot"></i>
                    <span>${escapeHtml(job.address || job.district || 'N/A')}</span>
                </div>
                <div class="job-type">
                    <i class="fas ${requestIcon}"></i>
                    <span>${requestLabel}</span>
                </div>
                <div class="job-date">
                    <i class="fas fa-calendar"></i>
                    <span>Finish by: ${formatDate(job.finish_date)}</span>
                </div>
            </div>
            <div class="job-actions">
                <button class="btn btn-secondary job-btn" onclick="viewJobDetails(${job.request_id}, 'direct')">
                    <i class="fas fa-eye"></i> View Details
                </button>
                ${alreadyQuoted ? `
                <button class="btn btn-disabled job-btn" disabled>
                    <i class="fas fa-check"></i> Already Quoted
                </button>` : `
                <button class="btn btn-primary job-btn" onclick="openQuoteModal(${job.request_id}, 'direct')">
                    <i class="fas fa-file-invoice-dollar"></i> Submit Quote
                </button>`}
            </div>
        </div>`;
}

function updateJobCounts(total) {
    const newCount = availableJobs.filter(j => {
        const hrs = (Date.now() - new Date(j.dateCreated).getTime()) / 3600000;
        return hrs < 24;
    }).length;

    setText('new-jobs-count', newCount);
    setText('total-jobs-count', total);
    setText('available-jobs-badge', total);
    setText('jobs-count', `${total} job${total !== 1 ? 's' : ''} available`);
}

// =========================================================================
//  VIEW JOB DETAILS (DRAWER)
// =========================================================================
function viewJobDetails(jobId, source = 'available') {
    openJobDetailsDrawer(jobId, source);
}

function openJobDetailsDrawer(jobId, source = 'available') {
    const jobList = source === 'direct' ? directJobs : availableJobs;
    const job = jobList.find(j => j.request_id == jobId);
    if (!job) {
        showToast('Job not found', 'error');
        return;
    }

    const drawer = document.getElementById('jobDetailsDrawer');
    const catClass = getCategoryClass(job.category_name);

    // Category badge
    const catEl = document.getElementById('detailCategory');
    catEl.innerHTML = `<i class="${getCategoryIcon(job.category_name)}"></i><span>${escapeHtml(job.category_name || 'General')}</span>`;
    catEl.className = `job-detail-category ${catClass}`;

    // Urgency badge
    const urgEl = document.getElementById('detailUrgency');
    const isUrgent = job.urgency === 'urgent';
    const urgencyLabel = source === 'direct'
        ? (job.source === 'direct' ? 'Direct Request' : 'Listed Request')
        : (isUrgent ? 'High Priority' : 'Normal Priority');
    const urgencyIcon = source === 'direct'
        ? (job.source === 'direct' ? 'fa-location-arrow' : 'fa-share-square')
        : (isUrgent ? 'fa-exclamation-circle' : 'fa-info-circle');
    const urgencyClass = source === 'direct' ? 'normal' : (isUrgent ? 'high' : 'low');
    urgEl.innerHTML = `<i class="fas ${urgencyIcon}"></i><span>${urgencyLabel}</span>`;
    urgEl.className = `job-detail-urgency ${urgencyClass}`;

    // Text fields
    setText('detailTitle', job.title);
    setText('detailCustomerName', job.customer_name);
    setText('detailPosted', job.posted_ago);
    setText('detailDistrict', job.district || 'N/A');
    setText('detailAddress', job.address || 'N/A');
    setText('detailSchedule', formatDate(job.finish_date));
    setText('detailDescription', job.description || 'No description provided.');

    // Provider type
    const providerType = job.service_provider_type || 'individual';
    const providerLabel = providerType === 'both' ? 'Individual / Company' : providerType.charAt(0).toUpperCase() + providerType.slice(1);
    setText('detailProviderType', providerLabel);

    // Attachments
    const attachContainer = document.getElementById('detailAttachments');
    const photos = Array.isArray(job.photos)
        ? job.photos
        : typeof job.photos === 'string' && job.photos !== ''
            ? job.photos.split(',')
            : [];
    if (photos.length > 0 && photos[0] !== '') {
        attachContainer.innerHTML = photos.map(photo => `
            <div class="attachment-item">
                <i class="fas fa-image"></i>
                <span>${escapeHtml(photo.trim())}</span>
            </div>`).join('');
    } else {
        attachContainer.innerHTML = '<p class="text-muted">No attachments</p>';
    }

    // Store job ID for submit button
    drawer.dataset.currentJobId = jobId;
    drawer.dataset.currentJobSource = source;

    // Update submit button: check if already quoted
    const submitBtn = document.getElementById('drawerSubmitQuoteBtn');
    const alreadyQuoted = submittedQuotes.some(q => q.request_id == jobId);
    if (alreadyQuoted) {
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Already Quoted';
        submitBtn.disabled = true;
        submitBtn.classList.add('btn-disabled');
    } else {
        submitBtn.innerHTML = '<i class="fas fa-file-invoice-dollar"></i> Submit Quote';
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-disabled');
    }

    // Show drawer
    drawer.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeJobDetails() {
    const drawer = document.getElementById('jobDetailsDrawer');
    drawer.classList.remove('active');
    document.body.style.overflow = '';
}

function submitQuoteFromDetails() {
    const drawer = document.getElementById('jobDetailsDrawer');
    const jobId = drawer.dataset.currentJobId;
    const source = drawer.dataset.currentJobSource || 'available';
    if (jobId) {
        closeJobDetails();
        openQuoteModal(parseInt(jobId), source);
    }
}

// =========================================================================
//  SUBMIT QUOTE (CREATE)
// =========================================================================
function openQuoteModal(jobId, source = 'available') {
    const jobList = source === 'direct' ? directJobs : availableJobs;
    const job = jobList.find(j => j.request_id == jobId);
    if (!job) {
        showToast('Job not found', 'error');
        return;
    }

    // Check if already quoted
    const alreadyQuoted = submittedQuotes.some(q => q.request_id == jobId);
    if (alreadyQuoted) {
        showToast('You have already submitted a quote for this job.', 'warning');
        return;
    }

    // Populate form
    document.getElementById('quoteJobId').value = jobId;
    document.getElementById('quoteJobTitle').textContent = `Job: ${job.title}`;
    document.getElementById('quoteForm').reset();
    document.getElementById('quoteJobId').value = jobId; // Re-set after reset

    // Set default valid until (14 days from now)
    const defaultValid = new Date();
    defaultValid.setDate(defaultValid.getDate() + 14);
    document.getElementById('validUntil').value = defaultValid.toISOString().split('T')[0];

    // Set min date for validUntil to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('validUntil').min = tomorrow.toISOString().split('T')[0];

    // Show modal
    document.getElementById('quoteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeQuoteModal() {
    document.getElementById('quoteModal').style.display = 'none';
    document.body.style.overflow = '';
}

async function handleQuoteSubmit(event) {
    event.preventDefault();

    const submitBtn = document.getElementById('submitQuoteBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    const formData = {
        request_id: parseInt(document.getElementById('quoteJobId').value),
        repairer_id: currentRepairerId,
        quoteAmount: parseFloat(document.getElementById('quoteAmount').value),
        estimatedDays: parseInt(document.getElementById('estimatedDays').value),
        warrantyPeriod: parseInt(document.getElementById('warrantyPeriod').value) || 0,
        validUntil: document.getElementById('validUntil').value,
        materialsIncluded: document.querySelector('input[name="materialsIncluded"]:checked')?.value === '1' ? 1 : 0,
        message: document.getElementById('quoteMessage').value.trim()
    };

    try {
        const response = await fetch(`${API_BASE}/repairer-quotes.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Quote submitted successfully!', 'success');
            closeQuoteModal();
            await loadSubmittedQuotations(); // Refresh quotes list
            renderJobs(availableJobs);       // Re-render job cards to reflect quoted state
        } else {
            showToast('Failed: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (err) {
        console.error('Error submitting quote:', err);
        showToast('Failed to submit quote. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Quote';
    }
}

// =========================================================================
//  LOAD SUBMITTED QUOTATIONS (READ)
// =========================================================================
async function loadSubmittedQuotations() {
    const container = document.getElementById('submitted-quotes-container');
    const countBadge = document.getElementById('quotes-count-badge');
    const countText = document.getElementById('quotes-count');

    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading your quotations...</p>
        </div>`;

    try {
        const response = await fetch(`${API_BASE}/repairer-quotes.php?repairer_id=${currentRepairerId}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const result = await response.json();


        if (result.success) {
            submittedQuotes = result.data || [];
            renderQuotations(submittedQuotes);
            renderDirectJobs(directJobs);
            const count = submittedQuotes.length;
            if (countBadge) countBadge.textContent = count;
            if (countText) countText.textContent = `${count} quotation${count !== 1 ? 's' : ''} submitted`;
        } else {
            showContainerError('submitted-quotes-container', 'Failed to load quotations: ' + (result.error || 'Unknown error'));
        }
    } catch (err) {
        console.error('Error loading quotations:', err);
        showContainerError('submitted-quotes-container', 'Failed to load quotations. Please try again.');
        if (countBadge) countBadge.textContent = '!';
        if (countText) countText.textContent = 'Error loading quotations';
    }
}

function renderQuotations(quotes) {
    const container = document.getElementById('submitted-quotes-container');

    if (!quotes.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-invoice"></i>
                <h3>No quotations submitted</h3>
                <p>You haven't submitted any quotations yet. Browse available jobs and submit your quotes!</p>
            </div>`;
        return;
    }

    // Group by status
    const groups = {
        pending: quotes.filter(q => q.status === 'pending'),
        accepted: quotes.filter(q => q.status === 'accepted'),
        rejected: quotes.filter(q => q.status === 'rejected'),
        expired: quotes.filter(q => q.status === 'expired')
    };

    let html = '';
    if (groups.pending.length) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-clock"></i> Pending Quotations (${groups.pending.length})</h3>`;
        html += groups.pending.map(createQuoteCard).join('');
    }
    if (groups.accepted.length) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-check-circle"></i> Accepted Quotations (${groups.accepted.length})</h3>`;
        html += groups.accepted.map(createQuoteCard).join('');
    }
    if (groups.rejected.length) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-times-circle"></i> Rejected Quotations (${groups.rejected.length})</h3>`;
        html += groups.rejected.map(createQuoteCard).join('');
    }
    if (groups.expired.length) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-hourglass-end"></i> Expired Quotations (${groups.expired.length})</h3>`;
        html += groups.expired.map(createQuoteCard).join('');
    }


    container.innerHTML = html;
}

function createQuoteCard(quote) {
    const statusClass = getQuoteStatusClass(quote.status);
    const statusIcon = getQuoteStatusIcon(quote.status);
    const canEdit = quote.status === 'pending';

    // Warranty text
    let warrantyText = 'No warranty';
    const wp = parseInt(quote.warrantyPeriod) || 0;
    if (wp === 12) warrantyText = '1 year';
    else if (wp === 24) warrantyText = '2 years';
    else if (wp > 0) warrantyText = `${wp} month${wp !== 1 ? 's' : ''}`;

    return `
        <div class="quote-card ${statusClass}" data-quote-id="${quote.quote_id}">
            <div class="quote-header">
                <div class="quote-job-info">
                    <h4 class="quote-job-title">${escapeHtml(quote.job_title || 'Job Request #' + quote.request_id)}</h4>
                    <p class="quote-job-meta">
                        <i class="fas fa-hashtag"></i> Quote #${quote.quote_id}
                        <span class="separator">&bull;</span>
                        <i class="fas fa-tag"></i> ${escapeHtml(quote.category_name || 'N/A')}
                        <span class="separator">&bull;</span>
                        <i class="fas fa-map-marker-alt"></i> ${escapeHtml(quote.district || 'N/A')}
                    </p>
                </div>
                <div class="quote-status-badge ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                    ${quote.status.charAt(0).toUpperCase() + quote.status.slice(1)}
                </div>
            </div>
            <div class="quote-body">
                <div class="quote-details-grid">
                    <div class="quote-detail-item">
                        <label>Quote Amount</label>
                        <span class="quote-amount">LKR ${parseFloat(quote.quoteAmount).toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Estimated Days</label>
                        <span>${quote.estimatedDays || 'N/A'} day${(parseInt(quote.estimatedDays) || 0) !== 1 ? 's' : ''}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Warranty</label>
                        <span>${warrantyText}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Valid Until</label>
                        <span>${formatDate(quote.validUntil)}</span>
                    </div>
                </div>
                ${quote.message ? `
                    <div class="quote-message">
                        <label><i class="fas fa-comment"></i> Your Message</label>
                        <p>${escapeHtml(quote.message)}</p>
                    </div>` : ''}
                <div class="quote-meta">
                    <span><i class="fas fa-clock"></i> Submitted ${formatDate(quote.dateSubmitted)}</span>
                    <span><i class="fas fa-box"></i> Materials: ${parseInt(quote.materialsIncluded) ? 'Included' : 'Not Included'}</span>
                </div>
            </div>
            <div class="quote-actions">
                ${canEdit ? `
                    <button class="btn btn-secondary btn-sm" onclick="openEditQuoteModal(${quote.quote_id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteQuote(${quote.quote_id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                ` : `
                    <span class="quote-readonly-label"><i class="fas fa-lock"></i> ${quote.status === 'accepted' ? 'Accepted' : quote.status === 'rejected' ? 'Rejected' : 'Expired'} - Read only</span>
                `}
            </div>
        </div>`;
}

// =========================================================================
//  EDIT QUOTE (UPDATE)
// =========================================================================
function openEditQuoteModal(quoteId) {
    const quote = submittedQuotes.find(q => q.quote_id == quoteId);
    if (!quote) {
        showToast('Quote not found', 'error');
        return;
    }

    if (quote.status !== 'pending') {
        showToast('Only pending quotes can be edited.', 'warning');
        return;
    }

    // Populate form
    document.getElementById('editQuoteId').value = quote.quote_id;
    document.getElementById('editQuoteJobTitle').textContent = `Job: ${quote.job_title || 'Job Request #' + quote.request_id}`;
    document.getElementById('editQuoteAmount').value = parseFloat(quote.quoteAmount);
    document.getElementById('editEstimatedDays').value = quote.estimatedDays;
    document.getElementById('editWarrantyPeriod').value = quote.warrantyPeriod || 0;
    document.getElementById('editValidUntil').value = quote.validUntil;
    document.getElementById('editQuoteMessage').value = quote.message || '';

    // Set materials radio
    const matVal = parseInt(quote.materialsIncluded) ? '1' : '0';
    const matRadio = document.querySelector(`input[name="editMaterialsIncluded"][value="${matVal}"]`);
    if (matRadio) matRadio.checked = true;

    // Set min date for validUntil to tomorrow
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('editValidUntil').min = tomorrow.toISOString().split('T')[0];

    // Show modal
    document.getElementById('editQuoteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditQuoteModal() {
    document.getElementById('editQuoteModal').style.display = 'none';
    document.body.style.overflow = '';
}

async function handleQuoteUpdate(event) {
    event.preventDefault();

    const updateBtn = document.getElementById('updateQuoteBtn');
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';

    const formData = {
        quote_id: parseInt(document.getElementById('editQuoteId').value),
        repairer_id: currentRepairerId,
        quoteAmount: parseFloat(document.getElementById('editQuoteAmount').value),
        estimatedDays: parseInt(document.getElementById('editEstimatedDays').value),
        warrantyPeriod: parseInt(document.getElementById('editWarrantyPeriod').value) || 0,
        validUntil: document.getElementById('editValidUntil').value,
        materialsIncluded: document.querySelector('input[name="editMaterialsIncluded"]:checked')?.value === '1' ? 1 : 0,
        message: document.getElementById('editQuoteMessage').value.trim()
    };

    try {
        const response = await fetch(`${API_BASE}/repairer-quotes.php`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Quote updated successfully!', 'success');
            closeEditQuoteModal();
            loadSubmittedQuotations();
        } else {
            showToast('Failed: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (err) {
        console.error('Error updating quote:', err);
        showToast('Failed to update quote. Please try again.', 'error');
    } finally {
        updateBtn.disabled = false;
        updateBtn.innerHTML = '<i class="fas fa-save"></i> Update Quote';
    }
}

// =========================================================================
//  DELETE QUOTE (DELETE)
// =========================================================================
async function deleteQuote(quoteId) {
    if (!confirm('Are you sure you want to delete this quotation? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(
            `${API_BASE}/repairer-quotes.php?quote_id=${quoteId}&repairer_id=${currentRepairerId}`,
            { method: 'DELETE' }
        );

        const result = await response.json();


        if (result.success) {
            showToast('Quotation deleted successfully!', 'success');
            await loadSubmittedQuotations();
            renderJobs(availableJobs); // Re-render to restore Submit Quote button
        } else {
            showToast('Failed to delete: ' + (result.error || 'Unknown error'), 'error');
        }
    } catch (err) {
        console.error('Error deleting quotation:', err);
        showToast('Failed to delete quotation. Please try again.', 'error');
    }
}

// =========================================================================
//  UTILITY FUNCTIONS
// =========================================================================
function getCategoryClass(name) {
    if (!name) return 'general';
    const n = name.toLowerCase();
    if (n.includes('plumb')) return 'plumbing';
    if (n.includes('electric')) return 'electrical';
    if (n.includes('appliance')) return 'appliance';
    if (n.includes('hvac') || n.includes('air')) return 'hvac';
    if (n.includes('carpent') || n.includes('wood')) return 'carpentry';
    if (n.includes('paint')) return 'painting';
    if (n.includes('roof')) return 'roofing';
    if (n.includes('clean')) return 'cleaning';
    if (n.includes('landscap') || n.includes('garden')) return 'landscaping';
    if (n.includes('pest')) return 'pest-control';
    if (n.includes('secur')) return 'security';
    if (n.includes('floor')) return 'flooring';
    if (n.includes('mason')) return 'masonry';
    if (n.includes('weld')) return 'welding';
    if (n.includes('glass') || n.includes('mirror')) return 'glass';
    if (n.includes('tile')) return 'tile';
    if (n.includes('drywall')) return 'drywall';
    if (n.includes('insul')) return 'insulation';
    if (n.includes('window')) return 'window';
    if (n.includes('interior') || n.includes('design')) return 'interior';
    return 'general';
}

function getCategoryIcon(name) {
    if (!name) return 'fas fa-tools';
    const n = name.toLowerCase();
    if (n.includes('plumb')) return 'fas fa-wrench';
    if (n.includes('electric')) return 'fas fa-bolt';
    if (n.includes('appliance')) return 'fas fa-tv';
    if (n.includes('hvac') || n.includes('air')) return 'fas fa-snowflake';
    if (n.includes('carpent') || n.includes('wood')) return 'fas fa-hammer';
    if (n.includes('paint')) return 'fas fa-paint-brush';
    if (n.includes('roof')) return 'fas fa-home';
    if (n.includes('clean')) return 'fas fa-broom';
    if (n.includes('landscap') || n.includes('garden')) return 'fas fa-leaf';
    if (n.includes('pest')) return 'fas fa-bug';
    if (n.includes('secur')) return 'fas fa-shield-alt';
    if (n.includes('floor')) return 'fas fa-th-large';
    if (n.includes('mason')) return 'fas fa-cubes';
    if (n.includes('weld')) return 'fas fa-fire';
    if (n.includes('glass') || n.includes('mirror')) return 'fas fa-border-all';
    if (n.includes('tile')) return 'fas fa-th';
    if (n.includes('window')) return 'fas fa-window-maximize';
    if (n.includes('interior') || n.includes('design')) return 'fas fa-couch';
    return 'fas fa-tools';
}

function getQuoteStatusClass(status) {
    return {
        pending: 'status-pending',
        accepted: 'status-accepted',
        rejected: 'status-rejected',
        expired: 'status-expired'
    }[status] || 'status-pending';
}

function getQuoteStatusIcon(status) {
    return {
        pending: 'fa-clock',
        accepted: 'fa-check-circle',
        rejected: 'fa-times-circle',
        expired: 'fa-hourglass-end'
    }[status] || 'fa-question-circle';
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return 'N/A';
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function getPostedAgo(dateStr) {
    if (!dateStr) return 'Recently';
    const time = new Date(dateStr).getTime();
    if (isNaN(time)) return 'Recently';
    const diffHours = (Date.now() - time) / 3600000;
    if (diffHours < 1) return 'Just now';
    if (diffHours < 24) return `${Math.floor(diffHours)} hour${Math.floor(diffHours) !== 1 ? 's' : ''} ago`;
    const days = Math.floor(diffHours / 24);
    return `${days} day${days !== 1 ? 's' : ''} ago`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function showContainerError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error</h3>
                <p>${escapeHtml(message)}</p>
                <button class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Retry
                </button>
            </div>`;
    }
}

function normalizeDirectJob(job, source) {
    if (!job) return null;
    const dateCreated = job.dateCreated || job.date_created || null;
    const photosRaw = job.photos || '';
    return {
        request_id: job.request_id,
        title: job.title || 'Job Request',
        description: job.description || '',
        status: job.status || 'pending',
        district: job.district || '',
        address: job.address || '',
        finish_date: job.finish_date || null,
        dateCreated: dateCreated,
        photos: Array.isArray(photosRaw) ? photosRaw : typeof photosRaw === 'string' && photosRaw !== '' ? photosRaw.split(',') : [],
        category_name: job.category_name || 'General',
        customer_name: `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.trim(),
        service_provider_type: 'individual',
        urgency: 'normal',
        posted_ago: getPostedAgo(dateCreated),
        source: source
    };
}

function updateDirectJobCounts(total) {
    setText('direct-jobs-badge', total);
    setText('direct-jobs-count', `${total} job${total !== 1 ? 's' : ''} available`);
}

function showToast(message, type = 'info') {
    // Remove any existing toasts
    document.querySelectorAll('.app-toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `app-toast toast-${type}`;

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0abab5'
    };

    toast.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i><span>${escapeHtml(message)}</span>`;
    toast.style.cssText = `
        position: fixed; top: 80px; right: 20px;
        background: ${colors[type] || colors.info}; color: white;
        padding: 14px 20px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 100000; display: flex; align-items: center; gap: 10px;
        font-size: 14px; font-weight: 500; max-width: 400px;
        transform: translateX(110%); transition: transform 0.3s ease;
    `;

    document.body.appendChild(toast);
    requestAnimationFrame(() => { toast.style.transform = 'translateX(0)'; });

    setTimeout(() => {
        toast.style.transform = 'translateX(110%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// =========================================================================
//  GLOBAL EXPORTS
// =========================================================================
window.viewJobDetails = viewJobDetails;
window.closeJobDetails = closeJobDetails;
window.submitQuoteFromDetails = submitQuoteFromDetails;
window.openQuoteModal = openQuoteModal;
window.closeQuoteModal = closeQuoteModal;
window.handleQuoteSubmit = handleQuoteSubmit;
window.openEditQuoteModal = openEditQuoteModal;
window.closeEditQuoteModal = closeEditQuoteModal;
window.handleQuoteUpdate = handleQuoteUpdate;
window.deleteQuote = deleteQuote;
window.loadSubmittedQuotations = loadSubmittedQuotations;
window.acceptNegotiation = acceptNegotiation;
window.rejectNegotiation = rejectNegotiation;
window.openCounterNegotiationModal = openCounterNegotiationModal;
window.closeCounterNegotiationModal = closeCounterNegotiationModal;
window.submitCounterNegotiation = submitCounterNegotiation;
