/**
 * my-jobs.js   Repairer "My Jobs" page
 * All data loaded from api/repairer-jobs.php (no mock data).
 */

// ===== GLOBALS =====
const repairerId = window.CURRENT_REPAIRER_ID || 0;
const BASE_URL   = window.BASE_URL || '/2nd-Year-Group-Project/FixLanka/';

let cachedJobs    = [];   // full list from API
let currentFilter = 'all';

// ===== INIT =====
document.addEventListener('DOMContentLoaded', function () {
    initializeMyJobsPage();
});

function initializeMyJobsPage() {
    initializeFilterTabs();
    initializeSortFilter();
    initializeSearch();
    initializeCalendar();
    loadJobs();
}

// ===== FILTER TABS =====
function initializeFilterTabs() {
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            renderJobs(cachedJobs, currentFilter);
            updateSectionSubtitle(currentFilter);
        });
    });
}

// ===== SORT =====
function initializeSortFilter() {
    const sortFilter = document.getElementById('sort-filter');
    if (sortFilter) {
        sortFilter.addEventListener('change', function () {
            sortJobs(this.value);
        });
    }
}

function sortJobs(sortBy) {
    const list = cachedJobs.slice();
    switch (sortBy) {
        case 'newest':
            list.sort((a, b) => new Date(b.dateSubmitted) - new Date(a.dateSubmitted));
            break;
        case 'oldest':
            list.sort((a, b) => new Date(a.dateSubmitted) - new Date(b.dateSubmitted));
            break;
        case 'status':
            const order = { active: 0, completed: 1, paid: 2, cancelled: 3 };
            list.sort((a, b) => (order[a.ui_status] || 9) - (order[b.ui_status] || 9));
            break;
        case 'amount':
            list.sort((a, b) => parseFloat(b.quoteAmount || 0) - parseFloat(a.quoteAmount || 0));
            break;
    }
    cachedJobs = list;
    renderJobs(cachedJobs, currentFilter);
}

// ===== LOAD JOBS FROM API =====
async function loadJobs() {
    if (!repairerId) {
        showEmptyState('Not logged in. Please refresh the page.');
        return;
    }

    showLoadingState();

    try {
        const res  = await fetch(`${BASE_URL}api/repairer-jobs.php?action=list&repairer_id=${repairerId}`);
        const data = await res.json();

        if (!data.success) {
            showEmptyState('Failed to load jobs: ' + (data.message || 'Unknown error'));
            return;
        }

        cachedJobs = data.jobs || [];
        renderJobs(cachedJobs, currentFilter);
        updateJobCounts();
        renderCalendar();
        renderUpcomingEvents();

    } catch (err) {
        console.error('loadJobs error:', err);
        showEmptyState('Could not connect to the server. Please try again.');
    }
}

// ===== RENDER JOBS =====
function renderJobs(jobs, filter) {
    const container = document.getElementById('jobsList');
    if (!container) return;

    const filtered = filter === 'all'
        ? jobs
        : jobs.filter(j => j.ui_status === filter);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="empty-state" style="text-align:center;padding:3rem;">
                <i class="fas fa-briefcase" style="font-size:3rem;color:#ccc;margin-bottom:1rem;"></i>
                <p style="color:#888;">No ${filter === 'all' ? '' : filter + ' '}jobs found.</p>
            </div>`;
        return;
    }

    container.innerHTML = filtered.map(job => createJobCard(job)).join('');
    updateSectionSubtitle(filter);
}

function createJobCard(job) {
    const uiStatus = job.ui_status || 'active';
    const jobStatus = job.job_status || 'accepted';
    const badgeMap = {
        active:    { icon: 'play-circle',     label: 'Active' },
        completed: { icon: 'clipboard-check', label: 'Completed' },
        paid:      { icon: 'credit-card',     label: 'Paid' },
        cancelled: { icon: 'times-circle',    label: 'Cancelled' },
    };
    const badge = badgeMap[uiStatus] || badgeMap.active;

    const customerName = `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.trim() || 'Unknown Customer';
    const location     = [job.district, job.address].filter(Boolean).join(', ') || '';
    const dateLabel    = formatDateRelative(job.dateSubmitted);
    const amount       = job.quoteAmount ? `LKR ${parseFloat(job.quoteAmount).toLocaleString()}` : '';

    // Sub-status indicator for active jobs
    let subStatusHtml = '';
    if (uiStatus === 'active') {
        if (jobStatus === 'accepted') {
            subStatusHtml = `<div class="job-sub-status accepted"><i class="fas fa-handshake"></i> Accepted &mdash; Ready to Start</div>`;
        } else if (jobStatus === 'in_progress') {
            subStatusHtml = `<div class="job-sub-status in-progress"><i class="fas fa-tools"></i> Work In Progress</div>`;
        }
    }

    let actionsHtml = '';
    if (uiStatus === 'active') {
        if (jobStatus === 'accepted') {
            // Freshly accepted — repairer can start work
            actionsHtml = `
                <button class="btn btn-primary" onclick="startWork(${job.request_id})">
                    <i class="fas fa-play"></i> Start Work
                </button>
                <button class="btn btn-secondary" onclick="viewJobDetails(${job.quote_id})">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelJob(${job.request_id})">
                    <i class="fas fa-times"></i> Cancel
                </button>`;
        } else {
            // Work in progress — repairer can mark complete
            actionsHtml = `
                <button class="btn btn-success" onclick="markComplete(${job.request_id})">
                    <i class="fas fa-check"></i> Mark Complete
                </button>
                <button class="btn btn-secondary" onclick="viewJobDetails(${job.quote_id})">
                    <i class="fas fa-eye"></i> View Details
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelJob(${job.request_id})">
                    <i class="fas fa-times"></i> Cancel
                </button>`;
        }
    } else if (uiStatus === 'completed') {
        actionsHtml = `
            <div class="completion-info">
                <span class="completed-label"><i class="fas fa-hourglass-half"></i> Awaiting Payment</span>
            </div>
            <button class="btn btn-secondary" onclick="viewJobDetails(${job.quote_id})">
                <i class="fas fa-eye"></i> View Details
            </button>`;
    } else if (uiStatus === 'paid') {
        actionsHtml = `
            <div class="completion-info">
                <span class="completed-label paid-label"><i class="fas fa-check-circle"></i> Completed &amp; Paid</span>
            </div>
            <button class="btn btn-secondary" onclick="viewJobDetails(${job.quote_id})">
                <i class="fas fa-eye"></i> View Details
            </button>`;
    } else {
        // Cancelled
        actionsHtml = `
            <div class="cancellation-info">
                <span class="cancelled-label"><i class="fas fa-times-circle"></i> Cancelled</span>
            </div>
            <button class="btn btn-secondary" onclick="viewJobDetails(${job.quote_id})">
                <i class="fas fa-eye"></i> View Details
            </button>`;
    }

    return `
        <div class="job-item ${uiStatus}-job" data-status="${uiStatus}" data-quote-id="${job.quote_id}">
            <div class="job-info">
                <div class="job-header">
                    <h3 class="job-title">${escapeHtml(job.job_title || '')}</h3>
                    <div class="job-status-badge ${uiStatus}">
                        <i class="fas fa-${badge.icon}"></i>${badge.label}
                    </div>
                </div>
                ${subStatusHtml}
                <div class="job-details">
                    <div class="job-customer"><i class="fas fa-user"></i><span>${escapeHtml(customerName)}</span></div>
                    <div class="job-location"><i class="fas fa-map-marker-alt"></i><span>${escapeHtml(location)}</span></div>
                    <div class="job-date"><i class="fas fa-calendar"></i><span>${dateLabel}</span></div>
                    <div class="job-amount"><i class="fas fa-money-bill"></i><span class="amount">${amount}</span></div>
                </div>
            </div>
            <div class="job-actions">${actionsHtml}</div>
        </div>`;
}

// ===== STATUS UPDATE ACTIONS =====
async function apiUpdateStatus(requestId, newStatus) {
    try {
        const res  = await fetch(`${BASE_URL}api/repairer-jobs.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'update-status', request_id: requestId, status: newStatus, repairer_id: repairerId })
        });
        const data = await res.json();
        if (data.success) {
            showNotification('Job status updated!', 'success');
            await loadJobs();
        } else {
            showNotification(data.message || 'Failed to update status', 'error');
        }
    } catch (err) {
        showNotification('Network error while updating status', 'error');
    }
}

async function startWork(requestId) {
    if (!confirm('Start working on this job? The customer will be notified.')) return;
    await apiUpdateStatus(requestId, 'in_progress');
}

async function markComplete(requestId) {
    if (!confirm('Mark this job as complete? The customer will be notified.')) return;
    await apiUpdateStatus(requestId, 'completed');
}

async function cancelJob(requestId) {
    if (!confirm('Are you sure you want to cancel this job? This action cannot be undone.')) return;
    await apiUpdateStatus(requestId, 'cancelled');
}

// ===== VIEW JOB DETAILS MODAL =====
function viewJobDetails(quoteId) {
    const job = cachedJobs.find(j => parseInt(j.quote_id) === parseInt(quoteId));
    if (!job) {
        showNotification('Job details not found', 'error');
        return;
    }

    const uiStatus = job.ui_status || 'active';
    const iconMap  = { active: 'tools', completed: 'clipboard-check', paid: 'check-circle', cancelled: 'times-circle' };
    const labelMap = { active: 'Active', completed: 'Completed', paid: 'Paid', cancelled: 'Cancelled' };

    const customerName = `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.trim() || 'Unknown';
    const amount       = job.quoteAmount ? parseFloat(job.quoteAmount) : 0;
    const platformFee  = (amount * 0.15).toFixed(2);
    const tax          = (amount * 0.05).toFixed(2);
    const earnings     = (amount - parseFloat(platformFee) - parseFloat(tax)).toFixed(2);

    setText('modal-job-title',    job.job_title || '');
    const statusEl = document.getElementById('modal-job-status');
    if (statusEl) {
        statusEl.innerHTML = `<i class="fas fa-${iconMap[uiStatus]}"></i> ${labelMap[uiStatus]}`;
        statusEl.className = `job-detail-status ${uiStatus}`;
    }
    setText('modal-job-amount',      `LKR ${amount.toLocaleString()}`);
    setText('modal-customer-name',   customerName);
    setText('modal-customer-phone',  '');
    setText('modal-customer-email',  job.customer_email || '');
    setText('modal-job-location',    [job.district, job.address].filter(Boolean).join(', ') || '');
    setText('modal-job-started',     job.dateSubmitted ? new Date(job.dateSubmitted).toLocaleDateString() : '');
    setText('modal-job-completion',  job.finish_date || '');
    setText('modal-job-id',          `#JOB-${String(job.request_id).padStart(7, '0')}`);
    setText('modal-job-category',    job.category_name || '');
    setText('modal-job-description', job.job_description || '');
    setText('modal-service-charge',  `LKR ${amount.toLocaleString()}`);
    setText('modal-platform-fee',    `LKR ${parseFloat(platformFee).toLocaleString()}`);
    setText('modal-tax',             `LKR ${parseFloat(tax).toLocaleString()}`);
    setText('modal-total-amount',    `LKR ${amount.toLocaleString()}`);
    setText('modal-earnings',        `LKR ${parseFloat(earnings).toLocaleString()}`);

    const timelineEl = document.getElementById('modal-timeline');
    if (timelineEl) {
        timelineEl.innerHTML = buildTimeline(job).map(s => `
            <div class="timeline-item ${s.status}">
                <div class="timeline-icon"><i class="fas fa-${s.icon}"></i></div>
                <div class="timeline-content">
                    <div class="timeline-title">${s.title}</div>
                    <div class="timeline-date">${s.date}</div>
                </div>
            </div>`).join('');
    }

    const primaryBtn = document.getElementById('modal-primary-action');
    if (primaryBtn) {
        const jobStatus = job.job_status || 'accepted';
        if (uiStatus === 'active' && jobStatus === 'accepted') {
            primaryBtn.innerHTML = '<i class="fas fa-play"></i> Start Work';
            primaryBtn.className = 'btn btn-primary';
            primaryBtn.style.display = '';
            primaryBtn.onclick = async () => {
                if (confirm('Start working on this job? The customer will be notified.')) {
                    await apiUpdateStatus(job.request_id, 'in_progress');
                    closeJobDetailsModal();
                }
            };
        } else if (uiStatus === 'active') {
            primaryBtn.innerHTML = '<i class="fas fa-check"></i> Mark as Complete';
            primaryBtn.className = 'btn btn-success';
            primaryBtn.style.display = '';
            primaryBtn.onclick = async () => {
                if (confirm('Mark this job as complete? The customer will be notified.')) {
                    await apiUpdateStatus(job.request_id, 'completed');
                    closeJobDetailsModal();
                }
            };
        } else {
            primaryBtn.style.display = 'none';
        }
    }

    const modal = document.getElementById('jobDetailsModal');
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function buildTimeline(job) {
    const steps = [];
    steps.push({ title: 'Quote Submitted', date: formatDate(job.dateSubmitted), status: 'completed', icon: 'check' });
    steps.push({ title: 'Quote Accepted',  date: formatDate(job.dateSubmitted), status: 'completed', icon: 'check' });

    const js = job.job_status;
    if (js === 'in_progress' || js === 'completed' || js === 'cancelled') {
        steps.push({ title: 'Work Started', date: '', status: 'completed', icon: 'tools' });
    } else {
        steps.push({ title: 'Work Started', date: 'Pending', status: 'pending', icon: 'clock' });
    }

    if (js === 'completed') {
        steps.push({ title: 'Work Completed', date: '', status: 'completed', icon: 'clipboard-check' });
        if (job.payment_status === 'completed') {
            steps.push({ title: 'Payment Received', date: formatDate(job.paymentDate), status: 'completed', icon: 'credit-card' });
        } else {
            steps.push({ title: 'Awaiting Payment', date: 'Pending', status: 'active', icon: 'hourglass-half' });
        }
    } else if (js === 'cancelled') {
        steps.push({ title: 'Job Cancelled', date: '', status: 'cancelled', icon: 'times-circle' });
    } else {
        steps.push({ title: 'Pending Completion', date: job.finish_date || '', status: 'pending', icon: 'clock' });
    }

    return steps;
}

function closeJobDetailsModal() {
    const modal = document.getElementById('jobDetailsModal');
    if (modal) modal.classList.remove('show');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('jobDetailsModal');
    if (modal) {
        modal.addEventListener('click', e => { if (e.target === modal) closeJobDetailsModal(); });
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeJobDetailsModal();
    });
});

// ===== JOB COUNTS / HEADER STATS =====
async function updateJobCounts() {
    if (!repairerId) return;
    try {
        const res  = await fetch(`${BASE_URL}api/repairer-jobs.php?action=stats&repairer_id=${repairerId}`);
        const data = await res.json();
        if (!data.success) return;

        // Header stats
        setText('headerActiveJobs', data.active);
        setText('headerAwaitingPayment', data.completed);
        setText('headerTotalJobs', data.total);

        // Tab counts
        setText('tabCountAll', data.total);
        setText('tabCountActive', data.active);
        setText('tabCountCompleted', data.completed);
        setText('tabCountPaid', data.paid);
        setText('tabCountCancelled', data.cancelled);
    } catch (err) {
        console.warn('updateJobCounts error:', err);
    }
}

// ===== SECTION SUBTITLE =====
function updateSectionSubtitle(filter) {
    const subtitle = document.querySelector('.section-subtitle');
    if (!subtitle) return;
    const filtered = filter === 'all' ? cachedJobs : cachedJobs.filter(j => j.ui_status === filter);
    const labelMap = {
        all:       'total jobs',
        active:    'active jobs',
        completed: 'completed jobs awaiting payment',
        paid:      'paid jobs',
        cancelled: 'cancelled jobs',
    };
    subtitle.textContent = `${filtered.length} ${labelMap[filter] || 'jobs'}`;
}

// ===== SEARCH =====
function initializeSearch() {
    const input = document.querySelector('.search-box input');
    if (input) {
        input.addEventListener('input', function () {
            const term = this.value.toLowerCase().trim();
            if (term === '') {
                renderJobs(cachedJobs, currentFilter);
                return;
            }
            const filtered = cachedJobs.filter(j => {
                const name = `${j.customer_first_name || ''} ${j.customer_last_name || ''}`.toLowerCase();
                return (j.job_title || '').toLowerCase().includes(term) ||
                       name.includes(term) ||
                       (j.district || '').toLowerCase().includes(term) ||
                       (j.address  || '').toLowerCase().includes(term);
            });
            renderJobs(filtered, currentFilter);
        });
    }
}

// ===== LOADING / EMPTY STATE =====
function showLoadingState() {
    const c = document.getElementById('jobsList');
    if (c) c.innerHTML = `
        <div class="loading-state" style="text-align:center;padding:3rem;">
            <i class="fas fa-spinner fa-spin" style="font-size:2rem;color:#0891b2;"></i>
            <p style="margin-top:1rem;color:#666;">Loading jobs</p>
        </div>`;
}

function showEmptyState(message) {
    const c = document.getElementById('jobsList');
    if (c) c.innerHTML = `
        <div class="empty-state" style="text-align:center;padding:3rem;">
            <i class="fas fa-briefcase" style="font-size:3rem;color:#ccc;margin-bottom:1rem;"></i>
            <p style="color:#888;">${escapeHtml(message)}</p>
        </div>`;
}

// ===== CALENDAR =====
let currentDate  = new Date();
let currentMonth = currentDate.getMonth();
let currentYear  = currentDate.getFullYear();

function initializeCalendar() {
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            renderCalendar();
        });
        nextBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            renderCalendar();
        });
        renderCalendar();
        renderUpcomingEvents();
    }
}

function calendarEventsFromJobs() {
    return cachedJobs
        .filter(j => j.finish_date && j.ui_status !== 'cancelled')
        .map(j => ({ date: j.finish_date, title: j.job_title, type: 'job' }));
}

function renderCalendar() {
    const calendarDays     = document.getElementById('calendar-days');
    const monthYearDisplay = document.getElementById('current-month-year');
    if (!calendarDays || !monthYearDisplay) return;

    calendarDays.innerHTML = '';

    const monthNames = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];
    monthYearDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;

    const firstDay    = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrev  = new Date(currentYear, currentMonth, 0).getDate();
    const today       = new Date();
    const jobEvents   = calendarEventsFromJobs();

    for (let i = firstDay - 1; i >= 0; i--) {
        calendarDays.appendChild(createDayElement(daysInPrev - i, 'prev-month'));
    }
    for (let day = 1; day <= daysInMonth; day++) {
        const el = createDayElement(day, 'current-month');
        if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
            el.classList.add('today');
        }
        const ds = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        if (jobEvents.some(e => e.date === ds)) el.classList.add('has-event');
        calendarDays.appendChild(el);
    }
    const remaining = 42 - calendarDays.children.length;
    for (let d = 1; d <= remaining; d++) {
        calendarDays.appendChild(createDayElement(d, 'next-month'));
    }
}

function createDayElement(day, monthClass) {
    const el = document.createElement('div');
    el.className = `calendar-day ${monthClass}`;
    el.textContent = day;
    return el;
}

function renderUpcomingEvents() {
    const list = document.getElementById('upcoming-events-list');
    if (!list) return;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const events = calendarEventsFromJobs()
        .filter(e => new Date(e.date) >= today)
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 5);

    if (events.length === 0) {
        list.innerHTML = '<div class="no-events-message">No upcoming events</div>';
        return;
    }

    const mns = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    list.innerHTML = events.map(e => {
        const d  = new Date(e.date);
        const ds = `${mns[d.getMonth()]} ${d.getDate()}`;
        return `
            <div class="event-item">
                <div class="event-date-badge">${ds}</div>
                <div class="event-details">
                    <div class="event-title">${escapeHtml(e.title)}</div>
                    <div class="event-time"><i class="fas fa-briefcase"></i> Finish by</div>
                </div>
            </div>`;
    }).join('');
}

// ===== UTILITY =====
function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    } catch (e) { return dateStr; }
}

function formatDateRelative(dateStr) {
    if (!dateStr) return '';
    const diff = Date.now() - new Date(dateStr).getTime();
    const days = Math.floor(diff / 86400000);
    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    if (days < 7)  return `${days} days ago`;
    if (days < 30) return `${Math.floor(days / 7)} week${days >= 14 ? 's' : ''} ago`;
    return formatDate(dateStr);
}

function showNotification(message, type) {
    type = type || 'info';
    const n = document.createElement('div');
    n.style.cssText = 'position:fixed;top:20px;right:20px;padding:15px 20px;border-radius:8px;' +
        'color:#fff;font-weight:500;z-index:10000;max-width:300px;' +
        'box-shadow:0 4px 12px rgba(0,0,0,.15);transform:translateX(100%);transition:transform .3s ease;';
    const bg = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
    n.style.background = bg[type] || bg.info;
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => { n.style.transform = 'translateX(0)'; }, 100);
    setTimeout(() => {
        n.style.transform = 'translateX(100%)';
        setTimeout(() => { if (n.parentNode) n.parentNode.removeChild(n); }, 300);
    }, 3000);
}
