/**
 * Applications Database Integration
 * Connects the workforce.php frontend to api/repairer-applications.php
 */

const APPLICATIONS_API_URL = '/2nd-Year-Group-Project/FixLanka/api/repairer-applications.php';

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function formatDate(dateString) {
    const d = new Date(dateString);
    if (Number.isNaN(d.getTime())) return '-';
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function normalizeApplication(app) {
    const expectedRate = app.expected_rate ?? app.expectedRate ?? app.hourly_rate ?? 0;
    return {
        id: app.application_id,
        jobTitle: app.job_title ?? '-',
        firstName: app.first_name ?? '',
        lastName: app.last_name ?? '',
        email: app.email ?? '-',
        phone: app.phone ?? '-',
        specialty: app.specialty ?? 'General',
        experience: Number(app.experience_years ?? app.experience ?? 0) || 0,
        expectedRate: Number(expectedRate) || 0,
        coverLetter: app.cover_letter ?? app.coverLetter ?? '',
        status: app.status ?? 'pending',
        appliedDate: app.applied_date ?? app.appliedDate ?? null,
        rating: Number(app.rating ?? 0) || 0,
        skills: app.skills ?? ''
    };
}

function normalizeSkills(skills) {
    if (Array.isArray(skills)) {
        return skills
            .map(s => String(s || '').trim())
            .filter(Boolean);
    }
    if (typeof skills === 'string') {
        return skills
            .split(',')
            .map(s => s.trim())
            .filter(Boolean);
    }
    return [];
}

function renderSkillTags(skills) {
    const items = normalizeSkills(skills);
    if (!items.length) {
        return '<span class="skill-tag">No specific skills listed</span>';
    }
    return items.map(s => `<span class="skill-tag">${escapeHtml(s)}</span>`).join('');
}

function renderWorkHistory(workHistory) {
    const container = document.getElementById('modalWorkHistory');
    if (!container) return;

    if (!Array.isArray(workHistory) || workHistory.length === 0) {
        container.innerHTML = '<p style="margin: 0; color: var(--text-secondary);">No completed work history available.</p>';
        return;
    }

    container.innerHTML = workHistory.map(item => {
        const title = escapeHtml(item?.title || 'Untitled');
        const category = escapeHtml(item?.category || '');
        const completedAt = item?.completed_at ? formatDate(item.completed_at) : '-';
        const source = escapeHtml(item?.source || '');
        const summary = escapeHtml(item?.summary || '');
        const hours = item?.hours_worked != null ? ` • ${escapeHtml(item.hours_worked)} hrs` : '';
        const rating = item?.rating != null ? ` • ⭐ ${escapeHtml(item.rating)}` : '';

        return `
            <div class="detail-item full-width" style="margin-bottom: 12px;">
                <div style="font-weight: 600; margin-bottom: 4px;">${title}</div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 6px;">
                    ${category ? `${category} • ` : ''}${escapeHtml(completedAt)}${hours}${rating}${source ? ` • ${source}` : ''}
                </div>
                ${summary ? `<div style="font-size: 13px; line-height: 1.4;">${summary}</div>` : ''}
            </div>
        `;
    }).join('');
}

/**
 * Load all applications for a company
 */
async function loadApplications() {
    if (!currentCompanyId) {
        console.error('Company ID not found');
        return;
    }

    try {
        const response = await fetch(`${APPLICATIONS_API_URL}?action=list&company_id=${currentCompanyId}`);

        if (!response.ok) {
            throw new Error('Failed to fetch applications');
        }

        const data = await response.json();

        if (data.success) {
            const applications = (data.applications || []).map(normalizeApplication);
            window.__applicationsCache = applications;
            renderApplications(applications);
        } else {
            console.error('API Error:', data.error);
        }

    } catch (error) {
        console.error('Error loading applications:', error);
    }
}

function renderApplications(applications) {
    const listContainer = document.querySelector('.applications-list');
    const tableBody = document.getElementById('applicationsTableBody');

    if (listContainer) {
        renderApplicationsCards(listContainer, applications);
        return;
    }

    if (tableBody) {
        renderApplicationsTable(applications);
    }
}

function renderApplicationsCards(container, applications) {
    container.innerHTML = '';

    if (!applications || applications.length === 0) {
        container.innerHTML = `
            <div class="applications-empty">
                <i class="fas fa-inbox"></i>
                <h3>No Applications</h3>
                <p>No applications pending review</p>
            </div>
        `;
        return;
    }

    applications.forEach(app => {
        const card = document.createElement('div');
        card.className = 'application-card';
        card.style.cursor = 'pointer';
        // Map DB statuses to the tab filters used in workforce.php
        card.dataset.status = app.status === 'pending' ? 'new' : app.status;

        const initials = `${(app.firstName || '').charAt(0)}${(app.lastName || '').charAt(0)}`.toUpperCase();

        const statusConfig = {
            pending: { cls: 'pending', icon: 'fas fa-clock', label: 'Pending' },
            reviewed: { cls: 'reviewed', icon: 'fas fa-eye', label: 'Reviewed' },
            approved: { cls: 'hired', icon: 'fas fa-check-circle', label: 'Approved' },
            rejected: { cls: 'rejected', icon: 'fas fa-times-circle', label: 'Rejected' }
        };
        const status = statusConfig[app.status] || statusConfig.pending;

        card.innerHTML = `
            <div class="application-header">
                <div class="applicant-profile">
                    <div class="applicant-avatar">${escapeHtml(initials)}</div>
                    <div class="applicant-info">
                        <h4>${escapeHtml(app.firstName)} ${escapeHtml(app.lastName)}</h4>
                        <span class="applicant-specialty">
                            <i class="fas fa-wrench"></i> ${escapeHtml(app.specialty)}
                        </span>
                        <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">${escapeHtml(app.jobTitle)}</div>
                    </div>
                </div>
                <div class="application-badges">
                    <span class="status-badge ${status.cls}">
                        <i class="${status.icon}"></i> ${status.label}
                    </span>
                    <span class="time-badge">
                        <i class="fas fa-clock"></i> ${escapeHtml(formatDate(app.appliedDate))}
                    </span>
                </div>
            </div>

            <div class="application-details-grid">
                <div class="detail-item">
                    <label>
                        <span class="detail-icon experience"><i class="fas fa-briefcase"></i></span>
                        Experience
                    </label>
                    <span>${escapeHtml(app.experience)} years</span>
                </div>

                <div class="detail-item">
                    <label>
                        <span class="detail-icon rate"><i class="fas fa-money-bill-wave"></i></span>
                        Expected Rate
                    </label>
                    <span>LKR ${Number(app.expectedRate || 0).toLocaleString()}/hr</span>
                </div>

                <div class="detail-item">
                    <label>
                        <span class="detail-icon email"><i class="fas fa-envelope"></i></span>
                        Email
                    </label>
                    <span>${escapeHtml(app.email)}</span>
                </div>

                <div class="detail-item">
                    <label>
                        <span class="detail-icon phone"><i class="fas fa-phone"></i></span>
                        Phone
                    </label>
                    <span>${escapeHtml(app.phone)}</span>
                </div>
            </div>

            ${app.coverLetter ? `
                <div class="application-cover-letter">
                    <h6><i class="fas fa-file-alt"></i> Cover Letter</h6>
                    <p>${escapeHtml(String(app.coverLetter).substring(0, 150))}${String(app.coverLetter).length > 150 ? '...' : ''}</p>
                </div>
            ` : ''}

            <div class="application-actions">
                ${(app.status === 'pending' || app.status === 'reviewed') ? `
                    <button class="action-btn-sm success" onclick="approveApplication(${app.id}, event)" title="Accept Application">
                        <i class="fas fa-check-circle"></i> Accept
                    </button>
                    <button class="action-btn-sm danger" onclick="rejectApplication(${app.id}, event)" title="Decline Application">
                        <i class="fas fa-times-circle"></i> Decline
                    </button>
                ` : ''}
            </div>
        `;

        card.addEventListener('click', () => window.viewApplicationDetails(app.id));

        container.appendChild(card);
    });
}

/**
 * Render applications table
 */
function renderApplicationsTable(applications) {
    const tbody = document.getElementById('applicationsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!applications || applications.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px;">
                    <i class="fas fa-inbox" style="font-size: 40px; color: #ddd; margin-bottom: 20px;"></i>
                    <p style="color: #666;">No applications pending review</p>
                </td>
            </tr>
        `;
        return;
    }

    applications.forEach(app => {
        const d = new Date(app.appliedDate || app.applied_date);
        const formattedDate = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

        let statusBadge = '';
        switch (app.status) {
            case 'pending': statusBadge = '<span class="status-badge pending">Pending</span>'; break;
            case 'reviewed': statusBadge = '<span class="status-badge review">Reviewed</span>'; break;
            case 'approved': statusBadge = '<span class="status-badge hired">Approved</span>'; break;
            case 'rejected': statusBadge = '<span class="status-badge rejected">Rejected</span>'; break;
            default: statusBadge = `<span class="status-badge">${app.status}</span>`;
        }

        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.addEventListener('click', () => window.viewApplicationDetails(app.id));
        tr.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                        ${escapeHtml(app.firstName.charAt(0))}${escapeHtml(app.lastName.charAt(0))}
                    </div>
                    <div>
                        <div style="font-weight: 500;">${escapeHtml(app.firstName)} ${escapeHtml(app.lastName)}</div>
                        <div style="font-size: 12px; color: #666;">${escapeHtml(app.email)}</div>
                    </div>
                </div>
            </td>
            <td>
                <div style="font-weight: 500;">${escapeHtml(app.jobTitle)}</div>
            </td>
            <td>${formattedDate}</td>
            <td>${escapeHtml(app.experience)} years</td>
            <td>${statusBadge}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-star" style="color: #ffc107; font-size: 12px;"></i>
                    <span>${parseFloat(app.rating || 0).toFixed(1)}</span>
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    ${app.status === 'pending' || app.status === 'reviewed' ? `
                    <button class="action-btn approve-btn" title="Approve & Hire" onclick="approveApplication(${app.id}, event)">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn reject-btn" title="Reject" onclick="rejectApplication(${app.id}, event)">
                        <i class="fas fa-times"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Robust drawer implementation (does not rely on workforce.php mock arrays)
window.viewApplicationDetails = async function (applicationId) {
    try {
        const response = await fetch(`${APPLICATIONS_API_URL}?action=details&application_id=${encodeURIComponent(applicationId)}`);
        const data = await response.json();
        if (!data.success || !data.application) {
            alert(data.error || 'Failed to load application details');
            return;
        }

        const app = normalizeApplication(data.application);
        const drawer = document.getElementById('applicationDetailsDrawer');
        if (!drawer) return;
        drawer.dataset.applicationId = String(app.id);

        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        };

        setText('modalFullName', `${app.firstName} ${app.lastName}`.trim() || '-');
        setText('modalEmail', app.email || '-');
        setText('modalPhone', app.phone || '-');
        setText('modalApplicationDate', formatDate(app.appliedDate));
        setText('modalSpecialty', app.specialty || 'General');
        setText('modalExperience', `${app.experience} years`);
        setText('modalExpectedRate', `LKR ${Number(app.expectedRate || 0).toLocaleString()}/hr`);
        setText('modalCoverLetter', app.coverLetter || 'No cover letter provided.');

        const skillsContainer = document.getElementById('modalSkills');
        if (skillsContainer) {
            skillsContainer.innerHTML = renderSkillTags(app.skills);
        }

        renderWorkHistory(data.work_history);

        drawer.classList.add('active');
    } catch (e) {
        console.error('Error loading application details:', e);
        alert('Failed to load application details');
    }
};

window.approveApplicationFromDrawer = function () {
    const drawer = document.getElementById('applicationDetailsDrawer');
    const applicationId = drawer?.dataset?.applicationId;
    if (!applicationId) return;
    approveApplication(Number(applicationId));
    drawer.classList.remove('active');
};

window.rejectApplicationFromDrawer = function () {
    const drawer = document.getElementById('applicationDetailsDrawer');
    const applicationId = drawer?.dataset?.applicationId;
    if (!applicationId) return;
    rejectApplication(Number(applicationId));
    drawer.classList.remove('active');
};

/**
 * Approve application
 */
async function approveApplication(applicationId, event) {
    if (event) event.stopPropagation();

    if (!confirm('Are you sure you want to approve this application and quickly onboard them?')) {
        return;
    }

    try {
        const response = await fetch(`${APPLICATIONS_API_URL}?action=approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ application_id: applicationId })
        });

        const result = await response.json();
        if (result.success) {
            alert('Application approved! Redirecting to Available Freelancers.');
            loadApplications();

            // Refresh lists
            if (window.companyEmployees) window.companyEmployees.load();
            if (window.freelancersDb) window.freelancersDb.load();

            // After accepting, take the user to Available Freelancers (not Company Employees)
            if (typeof window.expandSection === 'function') {
                window.expandSection('freelancers');
            }
        } else {
            alert('Error: ' + result.error);
        }

    } catch (error) {
        console.error('Error approving application:', error);
    }
}

/**
 * Reject application
 */
async function rejectApplication(applicationId, event) {
    if (event) event.stopPropagation();

    const reason = prompt('Please provide a reason for rejection (optional):');
    if (reason === null) return; // cancelled

    try {
        const response = await fetch(`${APPLICATIONS_API_URL}?action=reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ application_id: applicationId, reason: reason })
        });

        const result = await response.json();
        if (result.success) {
            alert('Application rejected.');
            loadApplications();
        } else {
            alert('Error: ' + result.error);
        }

    } catch (error) {
        console.error('Error rejecting application:', error);
    }
}

// Ensure load triggers
document.addEventListener('DOMContentLoaded', () => {
    // Initial load
});

// Expose on window
window.applicationsDb = {
    load: loadApplications,
    approve: approveApplication,
    reject: rejectApplication
};
