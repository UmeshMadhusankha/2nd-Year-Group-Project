// ================================================
// REPAIRER JOBS & APPLICATIONS MANAGEMENT
// ================================================

const API_BASE = '/2nd-Year-Group-Project/FixLanka/api';
let currentRepairerId = window.CURRENT_REPAIRER_ID || 1; // Injected by PHP via company-jobs.php
let currentRepairerStatus = 'available'; // available, busy

// In-memory cache so drawers can look up data without extra fetches
let cachedJobPostings = [];
let cachedApplications = [];
let cachedContracts = [];
let cachedAssignments = [];
let cachedMessages = [];

// ================================================
// HELPERS
// ================================================

function normalizeApplicationStatus(status) {
    const s = (status || 'pending').toString().toLowerCase();
    if (s === 'approved') return 'accepted';
    if (s === 'declined') return 'rejected';
    return s;
}

async function fetchMyApplicationForPosting(postingId) {
    const params = new URLSearchParams({
        action: 'check',
        repairer_id: currentRepairerId,
        posting_id: postingId
    });

    const res = await fetch(`${API_BASE}/repairer-job-applications.php?${params}`);
    const data = await res.json();
    if (!data.success) throw new Error(data.error || 'Failed to check application status');
    return data;
}

async function updateJobDetailsApplyState(postingId) {
    const applyBtn = document.getElementById('jobApplyBtn');
    const drawer = document.getElementById('jobDetailsDrawer');
    if (!applyBtn || !drawer) return;

    // Default state
    applyBtn.disabled = false;
    applyBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Apply Now';
    drawer.dataset.applied = '0';
    drawer.dataset.appStatus = '';

    try {
        const check = await fetchMyApplicationForPosting(postingId);
        const applied = !!check.applied;
        const statusRaw = check.application?.app_status;
        const status = normalizeApplicationStatus(statusRaw);

        drawer.dataset.applied = applied ? '1' : '0';
        drawer.dataset.appStatus = status || '';

        if (applied) {
            applyBtn.disabled = true;
            const label = status === 'accepted'
                ? 'Accepted'
                : status === 'rejected'
                    ? 'Applied (Rejected)'
                    : 'Applied (Pending)';
            const icon = status === 'accepted'
                ? 'fa-check-circle'
                : status === 'rejected'
                    ? 'fa-times-circle'
                    : 'fa-clock';
            applyBtn.innerHTML = `<i class="fas ${icon}"></i> ${label}`;
        }
    } catch (e) {
        // Non-blocking: keep Apply enabled if check fails
        console.warn('updateJobDetailsApplyState:', e);
    }
}

// ================================================
// INITIALIZATION
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Load initial tab content (Browse Jobs)
    loadJobPostings();

    // Update all tab stats for badges
    updateApplicationStats();
    updateContractStats();

    // Initialize cover letter character counter
    const coverLetterInput = document.getElementById('coverLetter');
    if (coverLetterInput) {
        coverLetterInput.addEventListener('input', updateCoverLetterCount);
    }
});


// ================================================
// JOB POSTINGS FUNCTIONS
// ================================================

async function loadJobPostings() {
    const jobsGrid = document.getElementById('jobsGrid');
    if (!jobsGrid) return;

    jobsGrid.innerHTML = `
        <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
            <i class="fas fa-spinner fa-spin" style="font-size:32px; margin-bottom:12px;"></i>
            <p>Loading job postings...</p>
        </div>`;

    try {
        const params = new URLSearchParams({ action: 'browse' });
        const res  = await fetch(`${API_BASE}/job-postings.php?${params}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load jobs');

        cachedJobPostings = data.postings || [];
        jobsGrid.innerHTML = '';

        if (cachedJobPostings.length === 0) {
            jobsGrid.innerHTML = `
                <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
                    <i class="fas fa-briefcase" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                    <h3>No Job Postings Available</h3>
                    <p>There are no open job postings right now. Check back later!</p>
                </div>`;
        } else {
            cachedJobPostings.forEach(job => jobsGrid.appendChild(createJobCard(job)));
        }

        const jobCountEl = document.getElementById('jobCount');
        if (jobCountEl) jobCountEl.textContent = `${cachedJobPostings.length} job${cachedJobPostings.length !== 1 ? 's' : ''} available`;

    } catch (err) {
        console.error('loadJobPostings error:', err);
        jobsGrid.innerHTML = `
            <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
                <i class="fas fa-exclamation-circle" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                <h3>Failed to Load Jobs</h3>
                <p>${err.message}</p>
                <button class="btn btn-primary" onclick="loadJobPostings()" style="margin-top:16px;">
                    <i class="fas fa-sync-alt"></i> Try Again
                </button>
            </div>`;
    }
}

function getCompanyInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
}

function getCompanyInitials(name) {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').substring(0, 2).toUpperCase();
}

function createJobCard(job) {
    const card = document.createElement('div');
    card.className = 'job-card';
    card.dataset.category = (job.category || '').toLowerCase();
    card.onclick = () => viewJobDetails(job.posting_id);

    const priorityRaw   = job.priority_level ?? job.priorityLevel;
    const priority      = ((priorityRaw ?? 'medium') + '').toLowerCase();
    const priorityLabel = priority.charAt(0).toUpperCase() + priority.slice(1);
    const badgeClass    = priority === 'urgent' ? 'urgent' : 'active';
    const avatar        = getCompanyInitials(job.company_name);
    const budget        = `LKR ${Number(job.min_budget).toLocaleString()} - ${Number(job.max_budget).toLocaleString()}/hr`;
    const appCount      = job.application_count || 0;

    card.innerHTML = `
        <div class="job-card-header">
            <div style="display:flex; align-items:center; gap:12px; flex:1;">
                <div class="company-avatar">${avatar}</div>
                <div class="job-card-title">
                    <h3>${job.title}</h3>
                    <p class="company-name">${job.company_name || ''}</p>
                </div>
            </div>
            <span class="job-badge ${badgeClass}">${priorityLabel}</span>
        </div>
        <div class="job-card-info">
            <div class="job-info-item"><i class="fas fa-briefcase"></i><span>${job.employment_type || ''}</span></div>
            <div class="job-info-item"><i class="fas fa-map-marker-alt"></i><span>${job.location || ''}</span></div>
            <div class="job-info-item"><i class="fas fa-calendar"></i><span>Posted ${getRelativeTime(job.posted_date)}</span></div>
            <div class="job-info-item"><i class="fas fa-users"></i><span>${appCount} applicant${appCount !== 1 ? 's' : ''}</span></div>
        </div>
        <div class="job-card-footer">
            <span class="job-budget">${budget}</span>
            <button class="btn-apply" onclick="event.stopPropagation(); viewJobDetails(${job.posting_id})">
                <i class="fas fa-arrow-right"></i> View Details
            </button>
        </div>
    `;
    return card;
}

async function viewJobDetails(postingId) {
    let job = cachedJobPostings.find(j => j.posting_id == postingId);

    if (!job) {
        try {
            const res  = await fetch(`${API_BASE}/job-postings.php?action=get&posting_id=${postingId}`);
            const data = await res.json();
            if (!data.success) throw new Error(data.error);
            job = data.posting;
        } catch (err) {
            showNotification('Failed to load job details', 'error');
            return;
        }
    }

    const budget = `LKR ${Number(job.min_budget).toLocaleString()} - ${Number(job.max_budget).toLocaleString()}/hr`;

    document.getElementById('jobCompanyAvatar').textContent    = getCompanyInitials(job.company_name);
    document.getElementById('jobDetailTitle').textContent      = job.title || '';
    document.getElementById('jobCompanyName').textContent      = job.company_name || '';
    document.getElementById('jobPostedDate').textContent       = `Posted ${getRelativeTime(job.posted_date)}`;
    document.getElementById('jobApplicationCount').textContent = `${job.application_count || 0} applicants`;
    document.getElementById('jobLocation').textContent         = job.location || '';
    document.getElementById('jobCategory').textContent         = (job.category || '').toUpperCase();
    document.getElementById('jobEmploymentType').textContent   = job.employment_type || '';
    document.getElementById('jobBudget').textContent           = budget;
    const priorityRaw = job.priority_level ?? job.priorityLevel;
    const deadlineRaw = job.application_deadline ?? job.applicationDeadline;

    document.getElementById('jobExperience').textContent       = formatMinExperience(job.min_experience);
    document.getElementById('jobPriority').textContent         = formatPriorityLevel(priorityRaw);
    document.getElementById('jobDeadline').textContent         = isValidDateString(deadlineRaw) ? formatDate(deadlineRaw) : 'No deadline';
    document.getElementById('jobDescription').textContent      = job.description || '';
    document.getElementById('jobLocationRequirements').textContent = (job.location_requirements ?? job.locationRequirements) || 'None specified';

    const statusBadge = document.getElementById('jobStatusBadge');
    const status = job.status || 'open';
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    statusBadge.className   = `job-badge ${status}`;

    const skillsContainer = document.getElementById('jobSkillsTags');
    skillsContainer.innerHTML = '';
    const skillsRaw = job.required_skills ?? job.requiredSkills;
    const skills = skillsRaw ? skillsRaw.split(',').map(s => s.trim()).filter(Boolean) : [];
    skills.forEach(skill => {
        const tag = document.createElement('span');
        tag.className   = 'skill-tag';
        tag.textContent = skill;
        skillsContainer.appendChild(tag);
    });

    document.getElementById('jobDetailsDrawer').dataset.jobId = postingId;
    document.getElementById('jobDetailsDrawer').classList.add('active');

    // Disable Apply button if already applied
    updateJobDetailsApplyState(postingId);
}

function closeJobDetailsDrawer() {
    document.getElementById('jobDetailsDrawer').classList.remove('active');
}

function openApplicationForm() {
    const postingId = document.getElementById('jobDetailsDrawer').dataset.jobId;
    const job = cachedJobPostings.find(j => j.posting_id == postingId);
    if (!job) return;

    // Prevent re-apply UX: if already applied, do not open the form
    const drawer = document.getElementById('jobDetailsDrawer');
    if (drawer && drawer.dataset.applied === '1') {
        const st = normalizeApplicationStatus(drawer.dataset.appStatus);
        const msg = st === 'accepted'
            ? 'This job has already accepted your application.'
            : st === 'rejected'
                ? 'You already applied for this job (rejected).'
                : 'You already applied for this job.';
        showNotification(msg, 'info');
        return;
    }

    const budget = `LKR ${Number(job.min_budget).toLocaleString()} - ${Number(job.max_budget).toLocaleString()}/hr`;
    document.getElementById('applyingJobTitle').textContent    = job.title;
    document.getElementById('applyingCompanyName').textContent = job.company_name || '';

    document.getElementById('applicationFormDrawer').dataset.jobId = postingId;
    closeJobDetailsDrawer();
    document.getElementById('applicationFormDrawer').classList.add('active');
}

function closeApplicationFormDrawer() {
    document.getElementById('applicationFormDrawer').classList.remove('active');
    document.getElementById('jobApplicationForm').reset();
}

async function submitApplication() {
    const postingId   = document.getElementById('applicationFormDrawer').dataset.jobId;
    const proposedRate = document.getElementById('proposedRate').value;
    const availability = document.getElementById('availability').value;
    const coverLetter  = document.getElementById('coverLetter').value;

    if (!proposedRate || !availability || !coverLetter) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    try {
        const res  = await fetch(`${API_BASE}/repairer-job-applications.php?action=submit`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                repairer_id:   currentRepairerId,
                posting_id:    parseInt(postingId),
                proposed_rate: parseFloat(proposedRate),
                availability:  availability,
                cover_letter:  coverLetter
            })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error);

        showNotification('Application submitted successfully!', 'success');
        closeApplicationFormDrawer();
        setTimeout(() => {
            loadApplications();
            switchMainTab('applications');
        }, 1200);
    } catch (err) {
        showNotification(err.message || 'Failed to submit application', 'error');
    }
}

function updateCoverLetterCount() {
    const coverLetter = document.getElementById('coverLetter');
    const counter = document.getElementById('coverLetterCount');
    if (coverLetter && counter) {
        counter.textContent = coverLetter.value.length;
    }
}

function filterJobsByCategory(category) {
    // Filter cards using the data-category attribute set during rendering
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach(card => {
        if (category === 'all') {
            card.style.display = 'block';
        } else {
            const cardCategory = (card.dataset.category || '').toLowerCase();
            card.style.display = cardCategory === category.toLowerCase() ? 'block' : 'none';
        }
    });
}

function searchJobs() {
    const searchTerm = document.getElementById('jobSearchInput').value.toLowerCase();
    const jobCards = document.querySelectorAll('.job-card');

    jobCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? 'block' : 'none';
    });
}

function refreshJobPostings() {
    showNotification('Refreshing job postings...', 'info');
    loadJobPostings();
}

// ================================================
// APPLICATIONS FUNCTIONS
// ================================================

async function loadApplications() {
    const applicationsList = document.getElementById('applicationsList');
    if (!applicationsList) return;

    applicationsList.innerHTML = `
        <div style="text-align:center; padding:40px 20px; color:var(--text-secondary);">
            <i class="fas fa-spinner fa-spin" style="font-size:32px;"></i>
            <p>Loading applications...</p>
        </div>`;

    try {
        const res  = await fetch(`${API_BASE}/repairer-job-applications.php?action=my-list&repairer_id=${currentRepairerId}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load applications');

        cachedApplications = data.applications || [];
        applicationsList.innerHTML = '';

        if (cachedApplications.length === 0) {
            applicationsList.innerHTML = `
                <div style="text-align:center; padding:60px 20px; color:var(--text-secondary);">
                    <i class="fas fa-inbox" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                    <h3>No Applications Yet</h3>
                    <p>You haven't submitted any job applications. Browse available jobs to get started!</p>
                    <button class="btn btn-primary" onclick="switchMainTab('browse')" style="margin-top:16px;">
                        <i class="fas fa-search"></i> Browse Jobs
                    </button>
                </div>`;
        } else {
            cachedApplications.forEach(app => applicationsList.appendChild(createApplicationCard(app)));
        }

        const appCountEl = document.getElementById('appCount');
        if (appCountEl) appCountEl.textContent = `${cachedApplications.length} application${cachedApplications.length !== 1 ? 's' : ''} submitted`;

        updateApplicationStats();

    } catch (err) {
        console.error('loadApplications error:', err);
        applicationsList.innerHTML = `
            <div style="text-align:center; padding:60px 20px; color:var(--text-secondary);">
                <i class="fas fa-exclamation-circle" style="font-size:64px; opacity:0.3;"></i>
                <h3>Failed to Load Applications</h3>
                <p>${err.message}</p>
            </div>`;
    }
}

function createApplicationCard(app) {
    const card = document.createElement('div');
    card.className = 'application-card';
    card.dataset.appId = app.app_id;
    card.onclick = () => viewApplicationDetails(app.app_id);

    const status    = normalizeApplicationStatus(app.app_status);
    const iconClass = status === 'accepted' ? 'accepted' : status === 'rejected' ? 'rejected' : 'pending';
    const icon      = status === 'accepted' ? 'fa-check-circle' : status === 'rejected' ? 'fa-times-circle' : 'fa-clock';
    const budget    = `LKR ${Number(app.min_budget).toLocaleString()} - ${Number(app.max_budget).toLocaleString()}/hr`;

    card.innerHTML = `
        <div class="application-icon ${iconClass}">
            <i class="fas ${icon}"></i>
        </div>
        <div class="application-content">
            <h4>${app.title || ''}</h4>
            <p class="application-company">${app.company_name || ''}</p>
            <div class="application-meta">
                <span><i class="fas fa-calendar"></i> Applied ${getRelativeTime(app.date_applied)}</span>
                <span><i class="fas fa-money-bill-wave"></i> ${budget}</span>
                <span><i class="fas fa-tag"></i> ${(app.category || '').toUpperCase()}</span>
            </div>
        </div>
        <span class="status-badge ${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>
    `;
    
    return card;
}

function viewApplicationDetails(appId) {
    const app = cachedApplications.find(a => a.app_id == appId);
    if (!app) return;

    const status = normalizeApplicationStatus(app.app_status);
    const statusBanner  = document.getElementById('appStatusBanner');
    const statusIcon    = document.getElementById('appStatusIcon');
    const statusTitle   = document.getElementById('appStatusTitle');
    const statusMessage = document.getElementById('appStatusMessage');


    statusBanner.className = 'application-status-banner';
    if (status === 'accepted') {
        statusBanner.classList.add('accepted');
        statusIcon.innerHTML   = '<i class="fas fa-check-circle"></i>';
        statusTitle.textContent   = 'Application Accepted!';
        statusMessage.textContent = 'Congratulations! The company has accepted your application.';
    } else if (status === 'rejected') {
        statusBanner.classList.add('rejected');
        statusIcon.innerHTML   = '<i class="fas fa-times-circle"></i>';
        statusTitle.textContent   = 'Application Declined';
        statusMessage.textContent = 'Unfortunately, the company has declined your application.';
    } else {
        statusIcon.innerHTML   = '<i class="fas fa-clock"></i>';
        statusTitle.textContent   = 'Application Under Review';
        statusMessage.textContent = 'Your application is being reviewed by the company.';
    }

    const budget = `LKR ${Number(app.min_budget).toLocaleString()} - ${Number(app.max_budget).toLocaleString()}/hr`;
    document.getElementById('appJobTitle').textContent    = app.title || '';
    document.getElementById('appCompanyName').textContent = app.company_name || '';
    document.getElementById('appJobCategory').textContent = (app.category || '').toUpperCase();
    document.getElementById('appJobBudget').textContent   = budget;
    document.getElementById('appAppliedDate').textContent = formatDate(app.date_applied);
    document.getElementById('appProposedRate').textContent = budget;
    document.getElementById('appAvailability').textContent = '';
    document.getElementById('appCoverLetter').textContent  = '';

    const statusBadge = document.getElementById('appStatusBadge');
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    statusBadge.className   = `status-badge ${status}`;

    // Build simple timeline
    const timeline = document.getElementById('appTimeline');
    timeline.innerHTML = '';
    const items = [{ event: 'Application Submitted', date: formatDate(app.date_applied) }];
    if (status !== 'pending') items.push({ event: status === 'accepted' ? 'Application Accepted' : 'Application Declined', date: '' });
    items.forEach(item => {
        const ti = document.createElement('div');
        ti.className = 'timeline-item';
        ti.innerHTML = `<div class="timeline-marker"></div><div class="timeline-content"><h5>${item.event}</h5><p>${item.date}</p></div>`;
        timeline.appendChild(ti);
    });

    const withdrawBtn = document.getElementById('withdrawBtn');
    withdrawBtn.style.display = status === 'pending' ? 'inline-flex' : 'none';
    withdrawBtn.onclick = () => withdrawApplication(app.app_id);

    document.getElementById('applicationDetailsDrawer').classList.add('active');
}

function closeApplicationDetailsDrawer() {
    document.getElementById('applicationDetailsDrawer').classList.remove('active');
}

async function withdrawApplication(appId) {
    if (!confirm('Are you sure you want to withdraw this application?')) return;

    try {
        const res  = await fetch(`${API_BASE}/repairer-job-applications.php?action=withdraw`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ app_id: appId, repairer_id: currentRepairerId })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error);

        showNotification('Application withdrawn successfully', 'success');
        closeApplicationDetailsDrawer();
        setTimeout(() => { loadApplications(); }, 500);
    } catch (err) {
        showNotification(err.message || 'Failed to withdraw application', 'error');
    }
}

function filterApplications(status, clickedEl) {
    // Update active filter chip
    document.querySelectorAll('#applicationsTab .filter-chip').forEach(chip => {
        chip.classList.remove('active');
    });
    if (clickedEl) clickedEl.classList.add('active');

    // Filter application cards
    const appCards = document.querySelectorAll('.application-card');
    appCards.forEach(card => {
        if (status === 'all') {
            card.style.display = 'flex';
        } else {
            const appStatus = card.querySelector('.status-badge')?.textContent?.toLowerCase() || '';
            card.style.display = appStatus === status ? 'flex' : 'none';
        }
    });
}

function searchApplications() {
    const searchTerm = document.getElementById('applicationSearchInput').value.toLowerCase();
    const appCards = document.querySelectorAll('.application-card');

    appCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchTerm) ? 'flex' : 'none';
    });
}

function updateApplicationStats() {
    const pending  = cachedApplications.filter(a => (a.app_status || '').toLowerCase() === 'pending').length;
    const accepted = cachedApplications.filter(a => (a.app_status || '').toLowerCase() === 'accepted').length;
    const rejected = cachedApplications.filter(a => (a.app_status || '').toLowerCase() === 'rejected').length;
    const total    = cachedApplications.length;

    const el = id => document.getElementById(id);
    if (el('pendingCount'))  el('pendingCount').textContent  = pending;
    if (el('acceptedCount')) el('acceptedCount').textContent = accepted;
    if (el('rejectedCount')) el('rejectedCount').textContent = rejected;
    if (el('totalCount'))    el('totalCount').textContent    = total;

    const badge = document.getElementById('applicationsBadge');
    if (badge) {
        badge.textContent     = total;
        badge.style.display   = total > 0 ? 'inline-block' : 'none';
    }
}

function refreshApplications() {
    showNotification('Refreshing applications...', 'info');
    loadApplications();
    updateApplicationStats();
}

// ================================================
// CONTRACTS & ASSIGNMENTS FUNCTIONS
// ================================================

async function loadContracts() {
    const contractsGrid = document.getElementById('contractsGrid');
    if (!contractsGrid) return;

    contractsGrid.innerHTML = `
        <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
            <i class="fas fa-spinner fa-spin" style="font-size:32px; margin-bottom:12px;"></i>
            <p>Loading contracts...</p>
        </div>`;

    try {
        const res  = await fetch(`${API_BASE}/repairer-job-applications.php?action=my-list&repairer_id=${currentRepairerId}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load contracts');

        cachedContracts = (data.applications || []).filter(a => (a.app_status || '').toLowerCase() === 'accepted');
        contractsGrid.innerHTML = '';

        if (cachedContracts.length === 0) {
            contractsGrid.innerHTML = `
                <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
                    <i class="fas fa-handshake" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                    <h3>No Active Contracts</h3>
                    <p>Contracts will appear here once a company accepts your application.</p>
                </div>`;
        } else {
            cachedContracts.forEach(contract => contractsGrid.appendChild(createContractCard(contract)));
        }

        const contractCountEl = document.getElementById('contractCount');
        if (contractCountEl) contractCountEl.textContent = `${cachedContracts.length} active contract${cachedContracts.length !== 1 ? 's' : ''}`;

        updateContractStats();
    } catch (err) {
        console.error('loadContracts error:', err);
        contractsGrid.innerHTML = `
            <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--text-secondary);">
                <i class="fas fa-exclamation-circle" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                <h3>Failed to Load Contracts</h3>
                <p>${err.message}</p>
                <button class="btn btn-primary" onclick="loadContracts()" style="margin-top:16px;">
                    <i class="fas fa-sync-alt"></i> Try Again
                </button>
            </div>`;
    }
}

function closeContractDetailsDrawer() {
    document.getElementById('contractDetailsDrawer').classList.remove('active');
}

function createContractCard(contract) {
    const card = document.createElement('div');
    card.className = 'contract-card';
    card.onclick = () => viewContractDetails(contract.app_id);

    const budget = `LKR ${Number(contract.min_budget).toLocaleString()} - ${Number(contract.max_budget).toLocaleString()}/hr`;
    const avatar = getCompanyInitials(contract.company_name);
    const dateAccepted = formatDate(contract.date_applied);
    const empType = (contract.employment_type || '').split('-').map(w => w ? w[0].toUpperCase() + w.slice(1) : '').join(' ');

    card.innerHTML = `
        <div class="contract-header">
            <div class="company-avatar">${avatar}</div>
            <div>
                <h4>${contract.title || ''}</h4>
                <p class="contract-role">${contract.company_name || ''} &middot; ${(contract.category || '').toUpperCase()}</p>
            </div>
        </div>
        <div class="contract-stats">
            <div class="contract-stat">
                <span class="contract-stat-value">${empType || 'Contract'}</span>
                <span class="contract-stat-label">Type</span>
            </div>
            <div class="contract-stat">
                <span class="contract-stat-value">${contract.location || 'Remote'}</span>
                <span class="contract-stat-label">Location</span>
            </div>
            <div class="contract-stat">
                <span class="contract-stat-value">${dateAccepted}</span>
                <span class="contract-stat-label">Accepted</span>
            </div>
            <div class="contract-stat">
                <span class="contract-stat-value" style="font-size:var(--font-size-sm);">${budget}</span>
                <span class="contract-stat-label">Budget Range</span>
            </div>
        </div>
        <div style="margin-top:var(--spacing-md); padding-top:var(--spacing-md); border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <span class="status-badge accepted">Active Contract</span>
            <span style="font-size:var(--font-size-sm); color:var(--text-secondary);">
                <i class="fas fa-map-marker-alt"></i> ${contract.location || 'Remote'}
            </span>
        </div>
    `;
    return card;
}

function viewContractDetails(contractId) {
    const contract = cachedContracts.find(c => c.app_id == contractId);
    if (!contract) return;

    const el = id => document.getElementById(id);
    const avatar = el('contractCompanyAvatar');
    if (avatar) avatar.textContent = getCompanyInitials(contract.company_name);

    if (el('contractCompanyName')) el('contractCompanyName').textContent = contract.company_name || '';
    if (el('contractRole'))        el('contractRole').textContent        = contract.title || '';
    if (el('contractType'))        el('contractType').textContent        = contract.employment_type || '—';
    if (el('contractStartDate'))   el('contractStartDate').textContent   = formatDate(contract.date_applied);
    if (el('contractRate'))        el('contractRate').textContent        = `LKR ${Number(contract.min_budget).toLocaleString()} - ${Number(contract.max_budget).toLocaleString()}/hr`;
    if (el('totalAssignments'))    el('totalAssignments').textContent    = '—';
    if (el('completedAssignments')) el('completedAssignments').textContent = '—';
    if (el('totalEarnings'))       el('totalEarnings').textContent       = '—';
    if (el('companyEmail'))        el('companyEmail').textContent        = '—';
    if (el('companyPhone'))        el('companyPhone').textContent        = '—';

    const contractStatus = el('contractStatus');
    if (contractStatus) {
        contractStatus.textContent = 'Active';
        contractStatus.className   = 'status-badge accepted';
    }

    const assignmentsContainer = el('contractAssignments');
    if (assignmentsContainer) {
        assignmentsContainer.innerHTML = `
            <div style="text-align:center; padding:20px; color:var(--text-secondary);">
                <i class="fas fa-clipboard-list" style="font-size:32px; opacity:0.3; margin-bottom:8px;"></i>
                <p>Work assignments from this contract will appear here.</p>
            </div>`;
    }

    document.getElementById('contractDetailsDrawer').classList.add('active');
}

async function loadAssignments() {
    const assignmentsList = document.getElementById('assignmentsList');
    if (!assignmentsList) return;

    assignmentsList.innerHTML = `
        <div style="text-align:center; padding:40px 20px; color:var(--text-secondary);">
            <i class="fas fa-spinner fa-spin" style="font-size:32px;"></i>
            <p>Loading assignments...</p>
        </div>`;

    try {
        const res  = await fetch(`${API_BASE}/freelancer-assignments.php?action=list_for_repairer&repairer_id=${currentRepairerId}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to load assignments');

        cachedAssignments = data.offers || [];
        assignmentsList.innerHTML = '';

        if (cachedAssignments.length === 0) {
            assignmentsList.innerHTML = `
                <div style="text-align:center; padding:60px 20px; color:var(--text-secondary);">
                    <i class="fas fa-clipboard-list" style="font-size:64px; opacity:0.3; margin-bottom:16px;"></i>
                    <h3>No Job Offers</h3>
                    <p>When a company assigns you to a project, the offer will appear here.</p>
                </div>`;
        } else {
            cachedAssignments.forEach(a => assignmentsList.appendChild(createAssignmentCard(a)));
        }

        const assignmentCountEl = document.getElementById('assignmentCount');
        if (assignmentCountEl) assignmentCountEl.textContent = `${cachedAssignments.length} assignment${cachedAssignments.length !== 1 ? 's' : ''}`;

        updateContractStats();
    } catch (err) {
        console.error('loadAssignments error:', err);
        assignmentsList.innerHTML = `
            <div style="text-align:center; padding:60px 20px; color:var(--text-secondary);">
                <i class="fas fa-exclamation-circle" style="font-size:64px; opacity:0.3;"></i>
                <h3>Failed to Load Assignments</h3>
                <p>${err.message}</p>
            </div>`;
    }
}

function createAssignmentCard(assignment) {
    const card = document.createElement('div');
    const status = (assignment.status || 'offered').toLowerCase();
    card.className = 'assignment-card';
    card.onclick   = () => viewAssignmentDetails(assignment.assignment_id);

    const statusClassMap = {
        offered: 'pending',
        accepted: 'accepted',
        declined: 'rejected',
        cancelled: 'rejected',
        in_progress: 'in-progress',
        completed: 'completed'
    };
    const statusLabelMap = {
        offered: 'Offered',
        accepted: 'Accepted',
        declined: 'Declined',
        cancelled: 'Cancelled',
        in_progress: 'In Progress',
        completed: 'Completed'
    };

    const statusClass = statusClassMap[status] || 'pending';
    const statusLabel = statusLabelMap[status] || (status.charAt(0).toUpperCase() + status.slice(1));

    const title = assignment.project_title || 'Project Assignment';
    const companyName = assignment.company_name || 'Company';
    const dueDate = assignment.deadline_date ? `Deadline ${formatDate(assignment.deadline_date)}` : '';
    const rate = assignment.rate_or_price !== undefined && assignment.rate_or_price !== null ? Number(assignment.rate_or_price) : 0;
    const pricingModel = (assignment.pricing_model || 'hourly').toLowerCase();
    const pricingLabel = pricingModel === 'fixed' ? `LKR ${rate.toLocaleString()}` : `LKR ${rate.toLocaleString()}/hr`;

    card.innerHTML = `
        <div class="assignment-header">
            <div class="assignment-info">
                <h4>${title}</h4>
                <p class="assignment-company"><i class="fas fa-building"></i> ${companyName}</p>
            </div>
            <span class="status-badge ${statusClass}">${statusLabel}</span>
        </div>
        <div class="assignment-details">
            <div class="assignment-detail">
                <i class="fas fa-map-marker-alt"></i>
                <span>${assignment.project_location || '—'}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-calendar"></i>
                <span>${dueDate || '—'}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-play"></i>
                <span>${assignment.start_date ? `Start ${formatDate(assignment.start_date)}` : '—'}</span>
            </div>
            <div class="assignment-detail">
                <i class="fas fa-coins"></i>
                <span>${pricingLabel}</span>
            </div>
        </div>
    `;
    return card;
}

function viewAssignmentDetails(requestId) {
    const assignment = cachedAssignments.find(a => a.assignment_id == requestId);
    if (!assignment) return;

    const el = id => document.getElementById(id);
    const companyName = assignment.company_name || 'Company';
    const title = assignment.project_title || 'Project Assignment';
    const pricingModel = (assignment.pricing_model || 'hourly').toLowerCase();
    const rate = assignment.rate_or_price !== undefined && assignment.rate_or_price !== null ? Number(assignment.rate_or_price) : 0;

    if (el('assignmentTitle'))       el('assignmentTitle').textContent       = title;
    if (el('assignmentCompany'))     el('assignmentCompany').textContent     = companyName;
    if (el('assignmentDate'))        el('assignmentDate').textContent        = assignment.start_date ? formatDate(assignment.start_date) : '—';
    if (el('assignmentTime'))        el('assignmentTime').textContent        = '—';
    if (el('assignmentLocation'))    el('assignmentLocation').textContent    = assignment.project_location || '—';
    if (el('estimatedHours'))        el('estimatedHours').textContent        = assignment.estimated_hours ? `${assignment.estimated_hours} hr` : '—';
    if (el('assignmentPriority'))    el('assignmentPriority').textContent    = pricingModel === 'fixed' ? 'Fixed Price' : 'Hourly';

    const descParts = [];
    if (pricingModel === 'fixed') {
        descParts.push(`Price: LKR ${rate.toLocaleString()}`);
    } else {
        descParts.push(`Rate: LKR ${rate.toLocaleString()}/hr`);
    }
    if (assignment.deadline_date) descParts.push(`Deadline: ${formatDate(assignment.deadline_date)}`);
    if (assignment.notes) descParts.push(`Notes: ${assignment.notes}`);
    if (el('assignmentDescription')) el('assignmentDescription').textContent = descParts.join(' • ');

    const status = (assignment.status || 'offered').toLowerCase();
    const statusBadge = el('assignmentStatus');
    if (statusBadge) {
        const statusClassMap = {
            offered: 'pending',
            accepted: 'accepted',
            declined: 'rejected',
            cancelled: 'rejected',
            in_progress: 'in-progress',
            completed: 'completed'
        };
        const statusLabelMap = {
            offered: 'Offered',
            accepted: 'Accepted',
            declined: 'Declined',
            cancelled: 'Cancelled',
            in_progress: 'In Progress',
            completed: 'Completed'
        };
        statusBadge.textContent = statusLabelMap[status] || status;
        statusBadge.className   = `status-badge ${statusClassMap[status] || 'pending'}`;
    }

    // Hide customer-job progress controls for offers
    const updateSection = document.getElementById('assignmentUpdateSection');
    const notesSection  = document.getElementById('assignmentNotesSection');
    if (updateSection) updateSection.style.display = 'none';
    if (notesSection)  notesSection.style.display  = 'none';

    const acceptBtn  = document.getElementById('assignmentAcceptBtn');
    const declineBtn = document.getElementById('assignmentDeclineBtn');
    const showOfferActions = status === 'offered';
    if (acceptBtn)  acceptBtn.style.display  = showOfferActions ? 'inline-flex' : 'none';
    if (declineBtn) declineBtn.style.display = showOfferActions ? 'inline-flex' : 'none';

    const drawer = document.getElementById('assignmentDetailsDrawer');
    drawer.dataset.assignmentId = assignment.assignment_id;
    drawer.classList.add('active');
}

async function acceptCurrentOffer() {
    const drawer = document.getElementById('assignmentDetailsDrawer');
    const assignmentId = parseInt(drawer?.dataset?.assignmentId || 0);
    if (!assignmentId) return;

    try {
        const res = await fetch(`${API_BASE}/freelancer-assignments.php?action=accept`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ assignment_id: assignmentId, repairer_id: currentRepairerId })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to accept offer');

        showNotification('Offer accepted', 'success');
        closeAssignmentDetailsDrawer();
        loadAssignments();
    } catch (err) {
        showNotification(err.message || 'Failed to accept offer', 'error');
    }
}

async function declineCurrentOffer() {
    const drawer = document.getElementById('assignmentDetailsDrawer');
    const assignmentId = parseInt(drawer?.dataset?.assignmentId || 0);
    if (!assignmentId) return;

    if (!confirm('Decline this offer?')) return;

    try {
        const res = await fetch(`${API_BASE}/freelancer-assignments.php?action=decline`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ assignment_id: assignmentId, repairer_id: currentRepairerId })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Failed to decline offer');

        showNotification('Offer declined', 'success');
        closeAssignmentDetailsDrawer();
        loadAssignments();
    } catch (err) {
        showNotification(err.message || 'Failed to decline offer', 'error');
    }
}

function closeAssignmentDetailsDrawer() {
    document.getElementById('assignmentDetailsDrawer').classList.remove('active');
}

function updateAssignmentStatus() {
    const status = document.getElementById('jobStatus').value;

    // Status will be saved when user clicks "Save Update"
}

async function saveProgressUpdate() {
    const requestId = document.getElementById('assignmentDetailsDrawer').dataset.requestId;
    const status    = document.getElementById('jobStatus')?.value;

    if (!requestId) {
        showNotification('No assignment selected', 'error');
        return;
    }

    const statusMap = { 'assigned': 'in_progress', 'in-progress': 'in_progress', 'completed': 'completed' };
    const apiStatus = statusMap[status] || 'in_progress';

    try {
        const res  = await fetch(`${API_BASE}/repairer-jobs.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action:      'update-status',
                request_id:  parseInt(requestId),
                status:      apiStatus,
                repairer_id: currentRepairerId
            })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || data.message);

        showNotification('Progress saved successfully!', 'success');
        closeAssignmentDetailsDrawer();
        loadAssignments();
    } catch (err) {
        showNotification(err.message || 'Failed to save progress', 'error');
    }
}

async function markAssignmentComplete() {
    const requestId = document.getElementById('assignmentDetailsDrawer').dataset.requestId;

    if (!requestId) {
        showNotification('No assignment selected', 'error');
        return;
    }

    if (!confirm('Mark this assignment as complete?')) return;

    try {
        const res  = await fetch(`${API_BASE}/repairer-jobs.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action:      'update-status',
                request_id:  parseInt(requestId),
                status:      'completed',
                repairer_id: currentRepairerId
            })
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || data.message);

        showNotification('Assignment marked as complete!', 'success');
        closeAssignmentDetailsDrawer();
        loadAssignments();
        updateContractStats();
    } catch (err) {
        showNotification(err.message || 'Failed to mark assignment complete', 'error');
    }
}

function loadMessages() {
    const conversationsList = document.getElementById('conversationsList');
    if (!conversationsList) return;
    conversationsList.innerHTML = `
        <div style="text-align:center; padding:40px 20px; color:var(--text-secondary);">
            <i class="fas fa-comments" style="font-size:48px; opacity:0.3; margin-bottom:12px;"></i>
            <p>No messages yet. Messages from companies will appear here.</p>
        </div>`;
}

function createConversationCard(message) {
    const card = document.createElement('div');
    card.className = 'conversation-card';
    return card;
}

let activeMessageId = null;

function openChatView(messageId) {
    // Chat will be implemented when message data is available from the server
}

function loadChatMessages(message) {
    const chatMessagesArea = document.getElementById('chatMessagesArea');
    if (chatMessagesArea) chatMessagesArea.innerHTML = '<div class="no-messages">No messages yet</div>';
}

function closeChatView() {
    document.querySelector('.chat-empty-state').style.display = 'flex';
    document.getElementById('chatActive').style.display = 'none';
    activeMessageId = null;

    // Remove active state from all conversations
    document.querySelectorAll('.conversation-card').forEach(card => {
        card.classList.remove('active');
    });
}

function sendChatMessage() {
    showNotification('Messaging not yet connected to server', 'info');
}

function handleChatKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendChatMessage();
    }
}

// Legacy functions for drawer (kept for compatibility)
function viewMessageThread(messageId) {
    openChatView(messageId);
}

function closeMessageThreadDrawer() {
    closeChatView();
}

function sendMessage() {
    sendChatMessage();
}

function handleMessageKeyPress(event) {
    handleChatKeyPress(event);
}

function sendMessageToCompany() {
    closeContractDetailsDrawer();
    
    // Switch to messages tab and open thread
    switchTab('messages');
    setTimeout(() => {
        viewMessageThread(message.id);
    }, 300);
}

function updateContractStats() {
    const el = id => document.getElementById(id);

    // Contract stats from cachedContracts (accepted applications to company job postings)
    const activeContracts   = cachedContracts.length;
    const completedJobCount = 0;
    const totalEarned       = 0;
    const pendingEarned     = 0;

    if (el('activeContractsCount'))  el('activeContractsCount').textContent  = activeContracts;
    if (el('totalContractEarnings')) el('totalContractEarnings').textContent = `LKR ${totalEarned.toLocaleString()}`;
    if (el('pendingPayments'))       el('pendingPayments').textContent       = `LKR ${pendingEarned.toLocaleString()}`;
    if (el('completedJobsCount'))    el('completedJobsCount').textContent    = completedJobCount;

    // Offer stats from cachedAssignments (freelancer_assignments)
    const offeredCount   = cachedAssignments.filter(a => (a.status || '').toLowerCase() === 'offered').length;
    const acceptedCount  = cachedAssignments.filter(a => ['accepted', 'in_progress'].includes((a.status || '').toLowerCase())).length;
    const completedCount = cachedAssignments.filter(a => (a.status || '').toLowerCase() === 'completed').length;
    const totalHours     = cachedAssignments
        .filter(a => ['accepted', 'in_progress'].includes((a.status || '').toLowerCase()))
        .reduce((sum, a) => sum + (parseFloat(a.estimated_hours) || 0), 0);

    if (el('activeAssignmentsCount')) el('activeAssignmentsCount').textContent = acceptedCount;
    if (el('pendingAssignments'))     el('pendingAssignments').textContent     = offeredCount;
    if (el('completedAssignments'))   el('completedAssignments').textContent   = completedCount;
    if (el('totalHours'))             el('totalHours').textContent             = `${totalHours}h`;

    if (el('unreadMessagesCount')) el('unreadMessagesCount').textContent = 0;
    if (el('totalThreads'))        el('totalThreads').textContent        = 0;
    if (el('sentMessages'))        el('sentMessages').textContent        = 0;

    // Tab badges
    const contractsBadge = el('contractsBadge');
    if (contractsBadge) {
        contractsBadge.textContent   = activeContracts;
        contractsBadge.style.display = activeContracts > 0 ? 'inline-block' : 'none';
    }

    const assignmentsBadge = el('assignmentsBadge');
    if (assignmentsBadge) {
        assignmentsBadge.textContent   = offeredCount;
        assignmentsBadge.style.display = offeredCount > 0 ? 'inline-block' : 'none';
    }

    const messagesBadge = el('messagesBadge');
    if (messagesBadge) { messagesBadge.textContent = 0; messagesBadge.style.display = 'none'; }
}

function switchTab(tabName) {
    // Update tab buttons within the current section
    const parentTabs = event.target.closest('.tabs');
    parentTabs.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Update tab content within the current section
    const parentContent = parentTabs.closest('.tab-content');
    if (parentContent) {
        parentContent.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        parentContent.querySelector(`#${tabName}Tab`).classList.add('active');
    }
}

function switchMainTab(tabName, evt) {
    // Support both inline onclick and programmatic calls (e.g. after submit)
    const buttons = Array.from(document.querySelectorAll('.tabs-container > .tabs > .tab-btn'));
    buttons.forEach(btn => btn.classList.remove('active'));

    const e = evt || (typeof window !== 'undefined' ? window.event : undefined);
    let activeBtn = e && (e.currentTarget || e.target) ? (e.currentTarget || e.target) : null;

    if (!activeBtn) {
        const tn = String(tabName || '').toLowerCase();
        activeBtn = buttons.find(b => {
            const onclick = (b.getAttribute('onclick') || '').toLowerCase();
            const text = (b.textContent || '').trim().toLowerCase();
            return onclick.includes(`switchmaintab('${tn}'`) || onclick.includes(`switchmaintab(\"${tn}\"`) || text.includes(tn);
        }) || null;
    }

    if (activeBtn && activeBtn.classList) {
        activeBtn.classList.add('active');
    }

    // Update main tab content
    document.querySelectorAll('.content-wrapper > .tab-content').forEach(content => {
        content.classList.remove('active');
    });

    const targetTab = document.getElementById(`${tabName}Tab`);
    if (targetTab) {
        targetTab.classList.add('active');
    }

    // Load content for the active tab
    if (tabName === 'browse') {
        loadJobPostings();
    } else if (tabName === 'applications') {
        loadApplications();
    } else if (tabName === 'contracts') {
        loadContracts();
    } else if (tabName === 'assignments') {
        loadAssignments();
    } else if (tabName === 'messages') {
        loadMessages();
    }
}

function refreshCurrentTab() {
    const activeTab = document.querySelector('.tabs-container > .tabs > .tab-btn.active');
    if (!activeTab) return;

    const tabText = activeTab.textContent.trim().toLowerCase();

    if (tabText.includes('browse')) {
        showNotification('Refreshing job postings...', 'info');
        loadJobPostings();
    } else if (tabText.includes('applications')) {
        showNotification('Refreshing applications...', 'info');
        loadApplications();
    } else if (tabText.includes('contracts')) {
        showNotification('Refreshing contracts...', 'info');
        loadContracts();
    } else if (tabText.includes('assignments')) {
        showNotification('Refreshing assignments...', 'info');
        loadAssignments();
    } else if (tabText.includes('messages')) {
        showNotification('Refreshing messages...', 'info');
        loadMessages();
    }
}

function refreshContracts() {
    showNotification('Refreshing contracts...', 'info');
    loadContracts();
    updateContractStats();
}

function toggleAvailabilityStatus() {
    currentRepairerStatus = currentRepairerStatus === 'available' ? 'busy' : 'available';

    const statusBtn  = document.getElementById('statusToggleBtn');
    const statusText = document.getElementById('statusText');
    const statusIcon = statusBtn ? statusBtn.querySelector('i') : null;

    if (currentRepairerStatus === 'available') {
        statusText.textContent = 'Available';
        if (statusIcon) {
            statusIcon.classList.remove('busy-dot');
            statusIcon.classList.add('available-dot');
        }
        showNotification('Status updated to Available', 'success');
    } else {
        statusText.textContent = 'Busy';
        if (statusIcon) {
            statusIcon.classList.remove('available-dot');
            statusIcon.classList.add('busy-dot');
        }
        showNotification('Status updated to Busy', 'info');
    }

    // TODO: persist status change to server
}

// ================================================
// UTILITY FUNCTIONS
// ================================================

function normalizeDateValue(value) {
    if (value === null || value === undefined) return null;
    const str = String(value).trim();
    if (!str) return null;
    if (str === '0000-00-00' || str === '0000-00-00 00:00:00') return null;

    // Handle MySQL DATETIME/TIMESTAMP strings: "YYYY-MM-DD HH:MM:SS" (optionally with fractional seconds)
    if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}(?:\.\d+)?$/.test(str)) {
        return str.replace(' ', 'T');
    }

    return str;
}

function isValidDateString(value) {
    const normalized = normalizeDateValue(value);
    if (!normalized) return false;
    const date = new Date(normalized);
    return !Number.isNaN(date.getTime());
}

function formatPriorityLevel(value) {
    const normalized = (value || 'medium').toString().trim().toLowerCase();
    const labels = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
        urgent: 'Urgent'
    };
    return labels[normalized] || 'Medium';
}

function formatMinExperience(value) {
    if (value === null || value === undefined || value === '') return 'Not specified';

    const legacyMap = {
        entry: 'Entry Level (0-1 years)',
        junior: 'Junior (1-3 years)',
        mid: 'Mid Level (3-5 years)',
        senior: 'Senior (5-10 years)',
        expert: 'Expert (10+ years)'
    };

    const raw = value.toString().trim();
    if (legacyMap[raw]) return legacyMap[raw];

    const years = Number(raw);
    if (!Number.isFinite(years)) return raw;
    if (years <= 0) return 'No experience required';
    return `${years}+ year${years === 1 ? '' : 's'}`;
}

function getRelativeTime(dateString) {
    const normalized = normalizeDateValue(dateString) || dateString;
    const date = new Date(normalized);
    if (Number.isNaN(date.getTime())) return '';
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'today';
    if (diffDays === 1) return 'yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.ceil(diffDays / 7)} weeks ago`;
    return date.toLocaleDateString();
}

function formatDate(dateString) {
    const normalized = normalizeDateValue(dateString);
    const date = new Date(normalized || dateString);
    return date.toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icon = type === 'success' ? 'check-circle' :
        type === 'error' ? 'exclamation-circle' :
            type === 'warning' ? 'exclamation-triangle' : 'info-circle';

    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Animations for notifications are defined in jobs.css
