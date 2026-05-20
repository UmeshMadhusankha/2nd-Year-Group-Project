// ================================================
// EARNINGS PAGE JAVASCRIPT - Connected to Backend
// ================================================

const EARNINGS_REPAIRER_ID = window.CURRENT_REPAIRER_ID || 0;
const EARNINGS_API = '/2nd-Year-Group-Project/FixLanka/api';
const COMPANY_EARNINGS_API = `${EARNINGS_API}/repairer-company-earnings.php`;

let allJobs = [];
let filteredJobs = [];
let allCompanyAssignments = [];
let filteredCompanyAssignments = [];
let currentSearchTerm = '';

let currentCustomerSort = { sortBy: 'date', direction: 'desc' };
let currentCompanySort = { sortBy: 'date', direction: 'desc' };

document.addEventListener('DOMContentLoaded', function () {
    if (!EARNINGS_REPAIRER_ID) {
        showEarningsError('Session expired. Please log in again.');
        showCompanyEarningsError('Session expired. Please log in again.');
        return;
    }

    initializeFilters();
    initializeCompanyFilters();
    initializeTableSorting();
    initializeSearch();
    
    loadEarningsFromAPI();
    loadCompanyEarningsFromAPI();
});

// ===== LOAD FROM API =====
async function loadEarningsFromAPI() {
    setTableLoading('earningsTableBody', 7);
    setSubtitle('earningsSubtitle', 'Loading...');
    try {
        const res = await fetch(`${EARNINGS_API}/repairer-jobs.php?action=list&repairer_id=${EARNINGS_REPAIRER_ID}`);
        const data = await res.json();

        if (!data.success) {
            showEarningsError(data.message || 'Failed to load earnings');
            return;
        }

        allJobs = (data.jobs || []).filter(j => j.ui_status === 'completed' || j.ui_status === 'paid');
        applyFilters();
        updateSummaryStats(allJobs);
        updateWelcomeTotalEarnings(allJobs);
    } catch (err) {
        console.error('Earnings load error:', err);
        showEarningsError('Failed to connect to the server. Please try again.');
    }
}

async function loadCompanyEarningsFromAPI() {
    setTableLoading('companyEarningsTableBody', 8);
    setSubtitle('companyEarningsSubtitle', 'Loading...');

    try {
        const res = await fetch(`${COMPANY_EARNINGS_API}?action=list&repairer_id=${EARNINGS_REPAIRER_ID}`);
        const data = await res.json();

        if (!data.success) {
            showCompanyEarningsError(data.message || 'Failed to load company earnings');
            return;
        }

        allCompanyAssignments = data.assignments || [];
        applyCompanyFilters();
        updateCompanySummaryStats(allCompanyAssignments);
    } catch (err) {
        console.error('Company earnings load error:', err);
        showCompanyEarningsError('Failed to connect to the server. Please try again.');
    }
}

function setTableLoading(tbodyId, colSpan) {
    const tbody = document.getElementById(tbodyId);
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align:center;padding:24px;color:var(--text-secondary)"><i class="fas fa-spinner fa-spin"></i> Loading earnings...</td></tr>`;
    }
}

function setSubtitle(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

function showEarningsError(message) {
    const tbody = document.getElementById('earningsTableBody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;color:#ef4444"><i class="fas fa-exclamation-circle"></i> ${escapeHtmlEarnings(message)}</td></tr>`;
    }
    setSubtitle('earningsSubtitle', 'Error loading data');
}

function showCompanyEarningsError(message) {
    const tbody = document.getElementById('companyEarningsTableBody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:24px;color:#ef4444"><i class="fas fa-exclamation-circle"></i> ${escapeHtmlEarnings(message)}</td></tr>`;
    }
    setSubtitle('companyEarningsSubtitle', 'Error loading data');
}

function escapeHtmlEarnings(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderEarningsTable(jobs) {
    const tbody = document.getElementById('earningsTableBody');
    if (!tbody) return;

    if (!jobs.length) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-secondary)"><i class="fas fa-inbox fa-2x"></i><p style="margin-top:12px">No earnings found for the selected filters.</p></td></tr>`;
        setSubtitle('earningsSubtitle', '0 payments found');
        return;
    }

    tbody.innerHTML = jobs.map(job => createEarningsRow(job)).join('');
    setSubtitle('earningsSubtitle', `${jobs.length} payment${jobs.length !== 1 ? 's' : ''} found`);
}

function createEarningsRow(job) {
    const isPaid = job.ui_status === 'paid' || job.payment_status === 'completed';
    const status = isPaid ? 'paid' : 'pending';
    const statusIcon = isPaid ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-clock"></i>';
    const statusLabel = isPaid ? 'Paid' : 'Pending';
    const amount = parseFloat(job.quoteAmount) || 0;
    const monthlyTotal = parseFloat(job.monthly_total) || 0;
    const rawDate = job.paymentDate || job.dateSubmitted || job.job_posted_date || '';
    const date = rawDate ? new Date(rawDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
    const customerFirst = job.customer_first_name || '';
    const customerLast = job.customer_last_name || '';
    const customerName = (customerFirst + ' ' + customerLast).trim() || '—';

    const actions = `<button class="btn btn-sm btn-outline" title="View Details" onclick="viewEarningDetails(${job.quote_id})"><i class="fas fa-eye"></i></button>`;

    return `
        <tr class="earnings-row" data-status="${status}" data-date="${escapeHtmlEarnings(rawDate)}" data-amount="${amount}" data-quote-id="${job.quote_id}">
            <td class="date-cell">
                <div class="date-info">
                    <span class="date-primary">${date}</span>
                </div>
            </td>
            <td class="job-cell">
                <div class="job-info">
                    <span class="job-title">${escapeHtmlEarnings(job.job_title || '—')}</span>
                    <span class="job-category">${escapeHtmlEarnings(job.category_name || '—')}</span>
                </div>
            </td>
            <td class="customer-cell">
                <div class="customer-info">
                    <span class="customer-name">${escapeHtmlEarnings(customerName)}</span>
                    <span class="customer-location">${escapeHtmlEarnings(job.district || '—')}</span>
                </div>
            </td>
            <td class="amount-cell">
                <span class="amount-earned">LKR ${amount.toLocaleString()}</span>
            </td>
            <td class="amount-cell">
                <span class="amount-earned">LKR ${monthlyTotal.toLocaleString()}</span>
            </td>
            <td class="status-cell">
                <span class="payment-status ${status}">${statusIcon} ${statusLabel}</span>
            </td>
            <td class="actions-cell">${actions}</td>
        </tr>`;
}

function renderCompanyEarningsTable(assignments) {
    const tbody = document.getElementById('companyEarningsTableBody');
    if (!tbody) return;

    if (!assignments.length) {
        tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-secondary)"><i class="fas fa-inbox fa-2x"></i><p style="margin-top:12px">No company earnings found for the selected filters.</p></td></tr>`;
        setSubtitle('companyEarningsSubtitle', '0 payments found');
        return;
    }

    tbody.innerHTML = assignments.map(item => createCompanyEarningsRow(item)).join('');
    setSubtitle('companyEarningsSubtitle', `${assignments.length} payment${assignments.length !== 1 ? 's' : ''} found`);
}

function createCompanyEarningsRow(assignment) {
    const status = assignment.ui_status === 'paid' ? 'paid' : 'pending';
    const statusIcon = status === 'paid' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-clock"></i>';
    const statusLabel = status === 'paid' ? 'Paid' : 'Pending';
    const amount = parseFloat(assignment.amount) || 0;
    const rawDate = assignment.assigned_date || assignment.end_date || '';
    const date = rawDate ? new Date(rawDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—';
    const assignmentTitle = assignment.project_title || assignment.role || '—';
    const assignmentCategory = assignment.role || 'Company Project';
    const companyName = assignment.company_name || '—';
    const location = assignment.project_location || '—';
    const hoursValue = parseFloat(assignment.hours_worked);
    const hoursWorked = Number.isFinite(hoursValue) ? hoursValue : 0;
    const rateValue = parseFloat(assignment.hourly_rate);
    const hourlyRate = Number.isFinite(rateValue) ? rateValue : 0;

    const actions = `<button class="btn btn-sm btn-outline" title="View Details" onclick="viewCompanyEarningDetails(${assignment.assignment_id})"><i class="fas fa-eye"></i></button>`;

    return `
        <tr class="company-earnings-row" data-status="${status}" data-date="${escapeHtmlEarnings(rawDate)}" data-amount="${amount}" data-assignment-id="${assignment.assignment_id}">
            <td class="date-cell">
                <div class="date-info">
                    <span class="date-primary">${date}</span>
                </div>
            </td>
            <td class="job-cell">
                <div class="job-info">
                    <span class="job-title">${escapeHtmlEarnings(assignmentTitle)}</span>
                    <span class="job-category">${escapeHtmlEarnings(assignmentCategory)}</span>
                </div>
            </td>
            <td class="customer-cell">
                <div class="customer-info">
                    <span class="customer-name">${escapeHtmlEarnings(companyName)}</span>
                    <span class="customer-location">${escapeHtmlEarnings(location)}</span>
                </div>
            </td>
            <td class="hours-cell">
                <span class="hours-worked">${formatHoursWorked(hoursWorked)}</span>
            </td>
            <td class="rate-cell">
                <span class="hourly-rate">${formatHourlyRate(hourlyRate)}</span>
            </td>
            <td class="amount-cell">
                <span class="amount-earned">LKR ${amount.toLocaleString()}</span>
            </td>
            <td class="status-cell">
                <span class="payment-status ${status}">${statusIcon} ${statusLabel}</span>
            </td>
            <td class="actions-cell">${actions}</td>
        </tr>`;
}

function updateSummaryStats(jobs) {
    const now = new Date();
    const thisMonth = now.getMonth();
    const thisYear = now.getFullYear();

    let totalEarnings = 0, monthlyEarnings = 0, pendingAmount = 0;

    jobs.forEach(job => {
        const amount = parseFloat(job.quoteAmount) || 0;
        const isPaid = job.ui_status === 'paid' || job.payment_status === 'completed';
        const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date;
        const d = dateValue ? new Date(dateValue) : null;
        if (isPaid) {
            totalEarnings += amount;
            if (d && d.getMonth() === thisMonth && d.getFullYear() === thisYear) {
                monthlyEarnings += amount;
            }
        } else {
            pendingAmount += amount;
        }
    });

    const avgValue = jobs.length > 0 ? (totalEarnings / Math.max(jobs.filter(j => j.ui_status === 'paid').length, 1)) : 0;

    setText('totalEarningsStat', `LKR ${totalEarnings.toLocaleString()}`);
    setText('monthEarningsStat', `LKR ${monthlyEarnings.toLocaleString()}`);
    setText('pendingEarningsStat', `LKR ${pendingAmount.toLocaleString()}`);
    setText('avgJobValueStat', `LKR ${Math.round(avgValue).toLocaleString()}`);
}

function updateWelcomeTotalEarnings(jobs) {
    const paidTotal = jobs
        .filter(j => j.ui_status === 'paid' || j.payment_status === 'completed')
        .reduce((sum, j) => sum + (parseFloat(j.quoteAmount) || 0), 0);
    const el = document.getElementById('welcomeTotalEarnings');
    if (el) el.textContent = `LKR ${paidTotal.toLocaleString()}`;
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}


// ===== FILTERS FUNCTIONALITY ===== 
function initializeFilters() {
    const periodFilter = document.getElementById('period-filter');
    const statusFilter = document.getElementById('status-filter');
    const sortFilter = document.getElementById('sort-filter');

    periodFilter.addEventListener('change', function () {
        applyFilters();
    });

    statusFilter.addEventListener('change', function () {
        applyFilters();
    });

    sortFilter.addEventListener('change', function () {
        currentCustomerSort = { sortBy: this.value, direction: 'desc' };
        sortCustomerEarnings(currentCustomerSort.sortBy, currentCustomerSort.direction);
    });
}

function applyFilters() {
    const periodFilter = document.getElementById('period-filter').value;
    const statusFilter = document.getElementById('status-filter').value;
    filteredJobs = allJobs.filter(job => {
        const isPaid = job.ui_status === 'paid' || job.payment_status === 'completed';
        if (statusFilter === 'paid' && !isPaid) return false;
        if (statusFilter === 'pending' && isPaid) return false;

        const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date;
        if (!isWithinPeriod(dateValue, periodFilter)) return false;

        if (currentSearchTerm) {
            if (!matchesSearch(job, currentSearchTerm)) return false;
        }

        return true;
    });

    sortCustomerEarnings(currentCustomerSort.sortBy, currentCustomerSort.direction, false);
    applyMonthlyTotals(filteredJobs);
    renderEarningsTable(filteredJobs);
    updateFilteredCount(filteredJobs.length, statusFilter, periodFilter);
    updateMonthlyTotal(filteredJobs);
}

function applyMonthlyTotals(jobs) {
    const totalsByMonth = new Map();

    jobs.forEach(job => {
        const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date || '';
        if (!dateValue) return;
        const dateKey = new Date(dateValue).toISOString().slice(0, 7);
        const amount = parseFloat(job.quoteAmount) || 0;
        totalsByMonth.set(dateKey, (totalsByMonth.get(dateKey) || 0) + amount);
    });

    jobs.forEach(job => {
        const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date || '';
        const dateKey = dateValue ? new Date(dateValue).toISOString().slice(0, 7) : '';
        job.monthly_total = dateKey ? (totalsByMonth.get(dateKey) || 0) : 0;
    });
}

function updateMonthlyTotal(jobs) {
    const now = new Date();
    const monthKey = now.toISOString().slice(0, 7);
    let total = 0;

    jobs.forEach(job => {
        const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date || '';
        if (!dateValue) return;
        const dateKey = new Date(dateValue).toISOString().slice(0, 7);
        if (dateKey !== monthKey) return;
        total += parseFloat(job.quoteAmount) || 0;
    });

    const label = `Monthly Total: LKR ${total.toLocaleString()}`;
    setText('monthlyTotalStat', label);
}

function updateFilteredCount(count, status, period) {
    const sectionSubtitle = document.getElementById('earningsSubtitle');
    let label = 'payments';

    if (status !== 'all') {
        label = `${status} payments`;
    }

    if (currentSearchTerm) {
        sectionSubtitle.textContent = `${count} ${label} matching "${currentSearchTerm}"`;
        return;
    }

    if (period !== 'all') {
        const periodLabel = document.querySelector(`option[value="${period}"]`).textContent.toLowerCase();
        sectionSubtitle.textContent = `${count} ${label} ${periodLabel}`;
        return;
    }

    sectionSubtitle.textContent = `${count} ${label} found`;
}

function resetFilters() {
    document.getElementById('period-filter').value = 'this-month';
    document.getElementById('status-filter').value = 'all';
    document.getElementById('sort-filter').value = 'newest';

    currentCustomerSort = { sortBy: 'newest', direction: 'desc' };
    currentSearchTerm = '';
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) searchInput.value = '';
    applyFilters();

    showNotification('Filters reset successfully!', 'info');
}

function initializeCompanyFilters() {
    const periodFilter = document.getElementById('company-period-filter');
    const statusFilter = document.getElementById('company-status-filter');

    if (!periodFilter || !statusFilter) return;

    periodFilter.addEventListener('change', function () {
        applyCompanyFilters();
    });

    statusFilter.addEventListener('change', function () {
        applyCompanyFilters();
    });
}

function applyCompanyFilters() {
    const periodFilter = document.getElementById('company-period-filter').value;
    const statusFilter = document.getElementById('company-status-filter').value;

    filteredCompanyAssignments = allCompanyAssignments.filter(item => {
        const isPaid = item.ui_status === 'paid';
        if (statusFilter === 'paid' && !isPaid) return false;
        if (statusFilter === 'pending' && isPaid) return false;

        const dateValue = item.assigned_date || item.end_date;
        if (!isWithinPeriod(dateValue, periodFilter)) return false;

        return true;
    });

    sortCompanyEarnings(currentCompanySort.sortBy, currentCompanySort.direction, false);
    renderCompanyEarningsTable(filteredCompanyAssignments);
}

function resetCompanyFilters() {
    document.getElementById('company-period-filter').value = 'this-month';
    document.getElementById('company-status-filter').value = 'all';
    currentCompanySort = { sortBy: 'date', direction: 'desc' };
    applyCompanyFilters();
    showNotification('Filters reset successfully!', 'info');
}

// ===== TABLE SORTING FUNCTIONALITY =====
function initializeTableSorting() {
    const customerHeaders = document.querySelectorAll('#customerEarningsTab .sortable');
    const companyHeaders = document.querySelectorAll('#companyEarningsTab .sortable');

    customerHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const sortBy = this.getAttribute('data-sort');
            const newDirection = toggleSortIndicator(customerHeaders, this);
            currentCustomerSort = { sortBy, direction: newDirection };
            sortCustomerEarnings(sortBy, newDirection);
        });
    });

    companyHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const sortBy = this.getAttribute('data-sort');
            const newDirection = toggleSortIndicator(companyHeaders, this);
            currentCompanySort = { sortBy, direction: newDirection };
            sortCompanyEarnings(sortBy, newDirection);
        });
    });
}

function sortCustomerEarnings(sortBy, direction = 'desc', rerender = true) {
    const sortKey = sortBy || 'date';
    let resolvedDirection = direction;

    if (sortKey === 'newest') resolvedDirection = 'desc';
    if (sortKey === 'oldest') resolvedDirection = 'asc';
    if (sortKey === 'amount-high') resolvedDirection = 'desc';
    if (sortKey === 'amount-low') resolvedDirection = 'asc';
    if (sortKey === 'monthly-total-high') resolvedDirection = 'desc';
    if (sortKey === 'monthly-total-low') resolvedDirection = 'asc';

    filteredJobs.sort((a, b) => {
        const aValue = getCustomerSortValue(a, sortKey);
        const bValue = getCustomerSortValue(b, sortKey);

        if (resolvedDirection === 'asc') {
            return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
        }
        return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
    });

    if (rerender) {
        renderEarningsTable(filteredJobs);
    }
}

function sortCompanyEarnings(sortBy, direction = 'desc', rerender = true) {
    const sortKey = sortBy || 'date';

    filteredCompanyAssignments.sort((a, b) => {
        const aValue = getCompanySortValue(a, sortKey);
        const bValue = getCompanySortValue(b, sortKey);

        if (direction === 'asc') {
            return aValue > bValue ? 1 : aValue < bValue ? -1 : 0;
        }
        return aValue < bValue ? 1 : aValue > bValue ? -1 : 0;
    });

    if (rerender) {
        renderCompanyEarningsTable(filteredCompanyAssignments);
    }
}

function toggleSortIndicator(headers, activeHeader) {
    const currentDirection = activeHeader.getAttribute('data-direction') || 'asc';
    const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';

    headers.forEach(h => {
        h.setAttribute('data-direction', '');
        const icon = h.querySelector('i');
        if (icon) icon.className = 'fas fa-sort';
    });

    activeHeader.setAttribute('data-direction', newDirection);
    const activeIcon = activeHeader.querySelector('i');
    if (activeIcon) activeIcon.className = newDirection === 'asc' ? 'fas fa-sort-up' : 'fas fa-sort-down';

    return newDirection;
}

// ===== SEARCH FUNCTIONALITY =====
function initializeSearch() {
    const searchInput = document.querySelector('.search-box input');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearchTerm = this.value.toLowerCase().trim();
            applyFilters();
        });
    }
}

// ===== EARNINGS ACTIONS =====
function viewEarningDetails(earningId) {
    const job = allJobs.find(item => Number(item.quote_id) === Number(earningId));
    if (!job) {
        showNotification('Earning details not found', 'error');
        return;
    }

    const isPaid = job.ui_status === 'paid' || job.payment_status === 'completed';
    const paidAmount = parseFloat(job.payment_amount) || parseFloat(job.quoteAmount) || 0;
    const platformFee = paidAmount > 0 ? Math.round(paidAmount * 0.1) : 0;
    const totalEarned = paidAmount > 0 ? paidAmount - platformFee : 0;

    const customerName = `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.trim() || '—';
    const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date;

    setDrawerStatus(isPaid);
    setText('earningJobTitle', job.job_title || '—');
    setText('earningCategory', job.category_name || '—');
    setText('earningDate', dateValue ? formatDateDisplay(dateValue) : '—');
    setText('earningDuration', job.estimatedDays ? `${job.estimatedDays} day(s)` : '—');

    setText('earningCustomerName', customerName);
    setText('earningLocation', job.district || '—');
    setText('earningContact', job.customer_email || '—');
    setText('earningRating', '—');

    setText('earningServiceFee', paidAmount ? `LKR ${paidAmount.toLocaleString()}` : '—');
    setText('earningPlatformFee', paidAmount ? `- LKR ${platformFee.toLocaleString()}` : '—');
    setText('earningMaterialsCost', formatMaterialsIncluded(job.materialsIncluded));
    setText('earningTotalEarned', paidAmount ? `LKR ${totalEarned.toLocaleString()}` : '—');

    setText('earningPaymentMethod', formatPaymentTypeLabel(job.paymentType));
    setText('earningTransactionId', job.payment_id ? `PAY-${job.payment_id}` : '—');
    setText('earningPaymentDate', isPaid && job.paymentDate ? formatDateDisplay(job.paymentDate) : 'Pending');

    const paymentStatusBadge = document.getElementById('earningPaymentStatus');
    paymentStatusBadge.textContent = isPaid ? 'Paid' : 'Pending';
    paymentStatusBadge.className = `status-badge ${isPaid ? 'paid' : 'pending'}`;

    setText('earningDescription', job.job_description || '—');
    setTimeline(buildCustomerTimeline(job));

    const reminderBtn = document.getElementById('drawerReminderBtn');
    reminderBtn.style.display = isPaid ? 'none' : 'inline-flex';
    reminderBtn.onclick = sendReminderFromDrawer;

    const drawer = document.getElementById('earningDetailsDrawer');
    drawer.dataset.earningId = earningId;
    drawer.dataset.earningType = 'customer';
    drawer.dataset.requestId = job.request_id;
    drawer.dataset.assignmentId = '';
    drawer.classList.add('active');
}

function closeEarningDetailsDrawer() {
    document.getElementById('earningDetailsDrawer').classList.remove('active');
}

function downloadInvoiceFromDrawer() {
    const drawer = document.getElementById('earningDetailsDrawer');
    const type = drawer.dataset.earningType;
    const requestId = drawer.dataset.requestId;
    const assignmentId = drawer.dataset.assignmentId;

    if (type === 'company') {
        downloadInvoice({ type: 'company', assignmentId: parseInt(assignmentId, 10) });
        return;
    }

    downloadInvoice({ type: 'customer', requestId: parseInt(requestId, 10) });
}

function sendReminderFromDrawer() {
    const drawer = document.getElementById('earningDetailsDrawer');
    const type = drawer.dataset.earningType;
    const requestId = drawer.dataset.requestId;
    const assignmentId = drawer.dataset.assignmentId;

    if (type === 'company') {
        sendReminder({ type: 'company', assignmentId: parseInt(assignmentId, 10) });
        return;
    }

    sendReminder({ type: 'customer', requestId: parseInt(requestId, 10) });
}

function formatDateDisplay(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function downloadInvoice(earningId) {
    if (!earningId || !earningId.type) {
        showNotification('Invoice details missing.', 'error');
        return;
    }

    if (earningId.type === 'customer' && !Number.isFinite(earningId.requestId)) {
        showNotification('Invoice details missing.', 'error');
        return;
    }

    if (earningId.type === 'company' && !Number.isFinite(earningId.assignmentId)) {
        showNotification('Invoice details missing.', 'error');
        return;
    }

    const endpoint = earningId.type === 'company'
        ? `${COMPANY_EARNINGS_API}?action=invoice&assignment_id=${earningId.assignmentId}`
        : `${EARNINGS_API}/repairer-jobs.php?action=invoice&request_id=${earningId.requestId}`;

    showNotification('Generating invoice...', 'info');

    fetch(endpoint)
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.invoice) {
                showNotification(data.message || 'Failed to generate invoice.', 'error');
                return;
            }

            const html = buildInvoiceHtml(data.invoice);
            const fileName = `${data.invoice.invoice_number || 'invoice'}.html`;
            downloadHtmlFile(html, fileName);
            showNotification('Invoice downloaded.', 'success');
        })
        .catch(() => {
            showNotification('Failed to generate invoice.', 'error');
        });
}

function sendReminder(earningId) {
    if (!earningId || !earningId.type) {
        showNotification('Reminder details missing.', 'error');
        return;
    }

    if (earningId.type === 'customer' && !Number.isFinite(earningId.requestId)) {
        showNotification('Reminder details missing.', 'error');
        return;
    }

    if (earningId.type === 'company' && !Number.isFinite(earningId.assignmentId)) {
        showNotification('Reminder details missing.', 'error');
        return;
    }

    const isCompany = earningId.type === 'company';
    const endpoint = isCompany
        ? `${COMPANY_EARNINGS_API}`
        : `${EARNINGS_API}/repairer-jobs.php`;

    const payload = isCompany
        ? { action: 'send-reminder', assignment_id: earningId.assignmentId }
        : { action: 'send-reminder', request_id: earningId.requestId };

    const confirmMessage = isCompany
        ? 'Send payment reminder to company?'
        : 'Send payment reminder to customer?';

    if (!confirm(confirmMessage)) {
        return;
    }

    showNotification('Sending payment reminder...', 'info');

    fetch(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                showNotification(data.message || 'Failed to send reminder.', 'error');
                return;
            }

            showNotification('Payment reminder sent successfully!', 'success');
        })
        .catch(() => {
            showNotification('Failed to send reminder.', 'error');
        });
}

function buildInvoiceHtml(invoice) {
    const issuedDate = invoice.issued_date ? formatDateDisplay(invoice.issued_date) : '—';
    const status = invoice.status === 'paid' ? 'Paid' : 'Pending';
    const amount = typeof invoice.amount === 'number' ? invoice.amount : parseFloat(invoice.amount || 0);
    const platformFee = typeof invoice.platform_fee === 'number'
        ? invoice.platform_fee
        : parseFloat(invoice.platform_fee || 0);
    const total = typeof invoice.total === 'number' ? invoice.total : parseFloat(invoice.total || 0);

    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>${escapeHtmlEarnings(invoice.invoice_number || 'Invoice')}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 32px; color: #111827; }
        h1 { margin: 0 0 8px; font-size: 24px; }
        .meta { margin-bottom: 24px; color: #4b5563; }
        .section { margin-bottom: 24px; }
        .label { font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background: #f9fafb; }
        .total { font-weight: 700; }
    </style>
</head>
<body>
    <h1>Invoice</h1>
    <div class="meta">Invoice #${escapeHtmlEarnings(invoice.invoice_number || '—')} • ${issuedDate} • ${status}</div>

    <div class="section">
        <div><span class="label">Billed To:</span> ${escapeHtmlEarnings(invoice.customer_name || '—')}</div>
        <div><span class="label">Email:</span> ${escapeHtmlEarnings(invoice.customer_email || '—')}</div>
        <div><span class="label">Location:</span> ${escapeHtmlEarnings(invoice.address || invoice.district || '—')}</div>
    </div>

    <div class="section">
        <div><span class="label">Job:</span> ${escapeHtmlEarnings(invoice.job_title || '—')}</div>
        <div>${escapeHtmlEarnings(invoice.job_description || '')}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Service Amount</td>
                <td>${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            </tr>
            <tr>
                <td>Platform Fee</td>
                <td>${platformFee.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            </tr>
            <tr class="total">
                <td>Total Earned</td>
                <td>${total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>`;
}

function downloadHtmlFile(html, fileName) {
    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}

function viewCompanyEarningDetails(assignmentId) {
    const assignment = allCompanyAssignments.find(item => Number(item.assignment_id) === Number(assignmentId));
    if (!assignment) {
        showNotification('Company earning details not found', 'error');
        return;
    }

    const isPaid = assignment.ui_status === 'paid';
    const amount = parseFloat(assignment.amount) || 0;
    const dateValue = assignment.assigned_date || assignment.end_date;

    setDrawerStatus(isPaid);
    setText('earningJobTitle', assignment.project_title || assignment.role || '—');
    setText('earningCategory', assignment.role || 'Company Project');
    setText('earningDate', dateValue ? formatDateDisplay(dateValue) : '—');
    setText('earningDuration', assignment.project_status || '—');

    setText('earningCustomerName', assignment.company_name || '—');
    setText('earningLocation', assignment.project_location || '—');
    setText('earningContact', '—');
    setText('earningRating', '—');

    setText('earningServiceFee', amount ? `LKR ${amount.toLocaleString()}` : '—');
    setText('earningPlatformFee', '—');
    setText('earningMaterialsCost', formatMaterialsResponsibility(assignment.materials_responsibility));
    setText('earningTotalEarned', amount ? `LKR ${amount.toLocaleString()}` : '—');

    setText('earningPaymentMethod', formatContractPaymentMethod(assignment.payment_method));
    setText('earningTransactionId', assignment.assignment_id ? `ASSIGN-${assignment.assignment_id}` : '—');
    setText('earningPaymentDate', isPaid ? (dateValue ? formatDateDisplay(dateValue) : '—') : 'Pending');

    const paymentStatusBadge = document.getElementById('earningPaymentStatus');
    paymentStatusBadge.textContent = isPaid ? 'Paid' : 'Pending';
    paymentStatusBadge.className = `status-badge ${isPaid ? 'paid' : 'pending'}`;

    setText('earningDescription', assignment.project_description || '—');
    setTimeline(buildCompanyTimeline(assignment));

    const reminderBtn = document.getElementById('drawerReminderBtn');
    reminderBtn.style.display = isPaid ? 'none' : 'inline-flex';
    reminderBtn.onclick = sendReminderFromDrawer;

    const drawer = document.getElementById('earningDetailsDrawer');
    drawer.dataset.earningId = assignmentId;
    drawer.dataset.earningType = 'company';
    drawer.dataset.assignmentId = assignmentId;
    drawer.dataset.requestId = '';
    drawer.classList.add('active');
}

function updateCompanySummaryStats(assignments) {
    const now = new Date();
    const thisMonth = now.getMonth();
    const thisYear = now.getFullYear();

    let totalEarnings = 0;
    let monthlyEarnings = 0;
    let pendingAmount = 0;
    let activeContracts = 0;

    assignments.forEach(item => {
        const amount = parseFloat(item.amount) || 0;
        const isPaid = item.ui_status === 'paid';
        const dateValue = item.assigned_date || item.end_date;
        const dateObj = dateValue ? new Date(dateValue) : null;

        if (isPaid) {
            totalEarnings += amount;
            if (dateObj && dateObj.getMonth() === thisMonth && dateObj.getFullYear() === thisYear) {
                monthlyEarnings += amount;
            }
        } else {
            pendingAmount += amount;
        }

        if (item.status === 'active') {
            activeContracts += 1;
        }
    });

    setText('companyTotalEarningsStat', `LKR ${totalEarnings.toLocaleString()}`);
    setText('companyMonthEarningsStat', `LKR ${monthlyEarnings.toLocaleString()}`);
    setText('companyPendingStat', `LKR ${pendingAmount.toLocaleString()}`);
    setText('companyActiveContractsStat', `${activeContracts}`);
}

function switchEarningsTab(tab) {
    document.querySelectorAll('.tabs-container .tab-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-tab') === tab);
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    const target = document.getElementById(`${tab}EarningsTab`);
    if (target) target.classList.add('active');
}

function exportEarningsReport() {
    const activeTab = document.querySelector('.tabs-container .tab-btn.active');
    const tab = activeTab ? activeTab.getAttribute('data-tab') : 'customer';
    const isCompany = tab === 'company';
    const items = isCompany ? filteredCompanyAssignments : filteredJobs;

    if (!items || items.length === 0) {
        showNotification('No earnings to export for the current filters.', 'warning');
        return;
    }

    const html = isCompany
        ? buildCompanyReportHtml(items)
        : buildCustomerReportHtml(items);

    openPrintWindow(html, isCompany ? 'Company Earnings Report' : 'Customer Earnings Report');
}

// ===== UTILITY FUNCTIONS =====
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification - ${type} `;
    notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border - radius: 8px;
    color: white;
    font - weight: 500;
    z - index: 10000;
    max - width: 300px;
    box - shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateX(100 %);
    transition: transform 0.3s ease;
    `;

    // Set background color based on type
    switch (type) {
        case 'success':
            notification.style.background = '#10b981';
            break;
        case 'error':
            notification.style.background = '#ef4444';
            break;
        case 'warning':
            notification.style.background = '#f59e0b';
            break;
        default:
            notification.style.background = '#3b82f6';
    }

    notification.textContent = message;
    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ===== HELPERS =====
function formatPaymentTypeLabel(paymentType) {
    const map = {
        credit_card: 'Credit card',
        debit_card: 'Debit card',
        cash: 'Cash',
        bank_transfer: 'Bank transfer'
    };

    if (!paymentType) return '—';
    return map[paymentType] || paymentType.replace(/_/g, ' ');
}

function formatContractPaymentMethod(method) {
    const map = {
        full_upfront: 'Full upfront',
        milestone_based: 'Milestone based',
        '50_50': '50/50 split',
        '30_70': '30/70 split',
        completion: 'Completion',
        time_and_material: 'Time and material'
    };

    if (!method) return '—';
    return map[method] || method.replace(/_/g, ' ');
}

function formatMaterialsIncluded(value) {
    if (value === null || value === undefined) return '—';
    return Number(value) === 1 ? 'Included' : 'Not included';
}

function formatMaterialsResponsibility(value) {
    const map = {
        company: 'Company provided',
        client: 'Client provided',
        shared: 'Shared'
    };

    if (!value) return '—';
    return map[value] || value;
}

function formatHoursWorked(hours) {
    if (!hours || hours <= 0) return '—';
    return `${hours.toFixed(2)} h`;
}

function formatHourlyRate(rate) {
    if (!rate || rate <= 0) return '—';
    return `LKR ${rate.toLocaleString()}`;
}

function isWithinPeriod(dateValue, period) {
    if (!dateValue) return false;
    if (period === 'all') return true;

    const date = new Date(dateValue);
    if (Number.isNaN(date.getTime())) return false;

    const now = new Date();
    const thisYear = now.getFullYear();
    const thisMonth = now.getMonth();

    switch (period) {
        case 'this-month':
            return date.getFullYear() === thisYear && date.getMonth() === thisMonth;
        case 'last-month': {
            const lastMonth = new Date(thisYear, thisMonth - 1, 1);
            return date.getFullYear() === lastMonth.getFullYear() && date.getMonth() === lastMonth.getMonth();
        }
        case 'last-3-months': {
            const start = new Date(thisYear, thisMonth - 2, 1);
            return date >= start && date <= now;
        }
        case 'this-year':
            return date.getFullYear() === thisYear;
        default:
            return true;
    }
}

function matchesSearch(job, term) {
    const jobTitle = (job.job_title || '').toLowerCase();
    const customerName = `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.toLowerCase();
    const location = (job.district || '').toLowerCase();
    const category = (job.category_name || '').toLowerCase();

    return jobTitle.includes(term) || customerName.includes(term) || location.includes(term) || category.includes(term);
}

function getCustomerSortValue(job, sortBy) {
    const amount = parseFloat(job.quoteAmount) || 0;
    const monthlyTotal = parseFloat(job.monthly_total) || 0;
    const status = job.ui_status === 'paid' || job.payment_status === 'completed' ? 'paid' : 'pending';
    const dateValue = job.paymentDate || job.dateSubmitted || job.job_posted_date || '1970-01-01';

    switch (sortBy) {
        case 'date':
        case 'newest':
        case 'oldest':
            return new Date(dateValue).getTime();
        case 'job':
            return (job.job_title || '').toLowerCase();
        case 'customer':
            return (`${job.customer_first_name || ''} ${job.customer_last_name || ''}`).toLowerCase();
        case 'amount':
        case 'amount-high':
        case 'amount-low':
            return amount;
        case 'monthly-total':
        case 'monthly-total-high':
        case 'monthly-total-low':
            return monthlyTotal;
        case 'status':
            return status;
        default:
            return new Date(dateValue).getTime();
    }
}

function getCompanySortValue(item, sortBy) {
    const amount = parseFloat(item.amount) || 0;
    const dateValue = item.assigned_date || item.end_date || '1970-01-01';
    const status = item.ui_status === 'paid' ? 'paid' : 'pending';

    switch (sortBy) {
        case 'date':
            return new Date(dateValue).getTime();
        case 'assignment':
            return (item.project_title || item.role || '').toLowerCase();
        case 'company':
            return (item.company_name || '').toLowerCase();
        case 'hours':
        case 'rate':
            return 0;
        case 'amount':
            return amount;
        case 'status':
            return status;
        default:
            return new Date(dateValue).getTime();
    }
}

function setDrawerStatus(isPaid) {
    const statusBanner = document.getElementById('earningStatusBanner');
    const statusIcon = document.getElementById('earningStatusIcon');
    const statusTitle = document.getElementById('earningStatusTitle');
    const statusMessage = document.getElementById('earningStatusMessage');

    statusBanner.className = 'earning-status-banner';
    if (isPaid) {
        statusBanner.classList.add('paid');
        statusIcon.innerHTML = '<i class="fas fa-check-circle"></i>';
        statusTitle.textContent = 'Payment Received';
        statusMessage.textContent = 'This payment has been successfully received';
    } else {
        statusBanner.classList.add('pending');
        statusIcon.innerHTML = '<i class="fas fa-clock"></i>';
        statusTitle.textContent = 'Payment Pending';
        statusMessage.textContent = 'Awaiting payment confirmation';
    }
}

function setTimeline(items) {
    const timelineEl = document.getElementById('earningTimeline');
    if (!timelineEl) return;

    timelineEl.innerHTML = '';
    if (!items.length) {
        timelineEl.innerHTML = '<div class="timeline-item"><div class="timeline-marker"></div><div class="timeline-content"><h5>No timeline available</h5><p>—</p></div></div>';
        return;
    }

    items.forEach(item => {
        const timelineItem = document.createElement('div');
        timelineItem.className = 'timeline-item';
        timelineItem.innerHTML = `
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <h5>${escapeHtmlEarnings(item.title)}</h5>
                <p>${escapeHtmlEarnings(item.date)}</p>
            </div>
        `;
        timelineEl.appendChild(timelineItem);
    });
}

function buildCustomerTimeline(job) {
    const timeline = [];
    if (job.job_posted_date) {
        timeline.push({ title: 'Job posted', date: formatDateDisplay(job.job_posted_date) });
    }
    if (job.dateSubmitted) {
        timeline.push({ title: 'Quote submitted', date: formatDateDisplay(job.dateSubmitted) });
    }
    if (job.paymentDate) {
        timeline.push({ title: 'Payment completed', date: formatDateDisplay(job.paymentDate) });
    }
    return timeline;
}

function buildCompanyTimeline(assignment) {
    const timeline = [];
    if (assignment.assigned_date) {
        timeline.push({ title: 'Assignment created', date: formatDateDisplay(assignment.assigned_date) });
    }
    if (assignment.end_date) {
        timeline.push({ title: 'Project end date', date: formatDateDisplay(assignment.end_date) });
    }
    return timeline;
}

function buildCustomerReportHtml(items) {
    const rows = items.map(job => {
        const amount = parseFloat(job.quoteAmount) || 0;
        const rawDate = job.paymentDate || job.dateSubmitted || job.job_posted_date || '';
        const date = rawDate ? formatDateDisplay(rawDate) : '';
        const customerName = `${job.customer_first_name || ''} ${job.customer_last_name || ''}`.trim();
        const status = job.ui_status === 'paid' || job.payment_status === 'completed' ? 'Paid' : 'Pending';

        return `
            <tr>
                <td>${escapeHtmlEarnings(date)}</td>
                <td>${escapeHtmlEarnings(job.job_title || '')}</td>
                <td>${escapeHtmlEarnings(customerName || '')}</td>
                <td>${escapeHtmlEarnings(job.district || '')}</td>
                <td>LKR ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>${escapeHtmlEarnings(status)}</td>
            </tr>
        `;
    }).join('');

    return buildReportHtml('Customer Earnings Report', [
        'Date', 'Job Title', 'Customer', 'Location', 'Amount (LKR)', 'Status'
    ], rows);
}

function buildCompanyReportHtml(items) {
    const rows = items.map(item => {
        const amount = parseFloat(item.amount) || 0;
        const rawDate = item.assigned_date || item.end_date || '';
        const date = rawDate ? formatDateDisplay(rawDate) : '';
        const hours = Number.isFinite(parseFloat(item.hours_worked))
            ? parseFloat(item.hours_worked).toFixed(2)
            : '';
        const rate = Number.isFinite(parseFloat(item.hourly_rate))
            ? parseFloat(item.hourly_rate).toFixed(2)
            : '';
        const status = item.ui_status === 'paid' ? 'Paid' : 'Pending';

        return `
            <tr>
                <td>${escapeHtmlEarnings(date)}</td>
                <td>${escapeHtmlEarnings(item.project_title || item.role || '')}</td>
                <td>${escapeHtmlEarnings(item.company_name || '')}</td>
                <td>${escapeHtmlEarnings(item.project_location || '')}</td>
                <td>${escapeHtmlEarnings(hours)}</td>
                <td>${escapeHtmlEarnings(rate)}</td>
                <td>LKR ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td>${escapeHtmlEarnings(status)}</td>
            </tr>
        `;
    }).join('');

    return buildReportHtml('Company Earnings Report', [
        'Date', 'Assignment', 'Company', 'Location', 'Hours Worked', 'Hourly Rate (LKR)', 'Amount (LKR)', 'Status'
    ], rows);
}

function buildReportHtml(title, headers, rowsHtml) {
    const now = new Date().toLocaleString('en-US');
    const headerRow = headers.map(label => `<th>${escapeHtmlEarnings(label)}</th>`).join('');

    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>${escapeHtmlEarnings(title)}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 32px; color: #111827; }
        h1 { margin-bottom: 4px; font-size: 24px; }
        .meta { margin-bottom: 24px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; font-size: 13px; }
        th { background: #f9fafb; }
    </style>
</head>
<body>
    <h1>${escapeHtmlEarnings(title)}</h1>
    <div class="meta">Generated on ${escapeHtmlEarnings(now)}</div>
    <table>
        <thead>
            <tr>${headerRow}</tr>
        </thead>
        <tbody>
            ${rowsHtml}
        </tbody>
    </table>
</body>
</html>`;
}

function openPrintWindow(html, title) {
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
        showNotification('Popup blocked. Please allow popups to export.', 'error');
        return;
    }

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.document.title = title;

    printWindow.focus();
    printWindow.onload = () => {
        printWindow.print();
    };
}
