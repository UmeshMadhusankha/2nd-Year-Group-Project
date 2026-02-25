/**
 * Applications Database Integration
 * Connects the workforce.php frontend to api/repairer-applications.php
 */

const APPLICATIONS_API_URL = '/2nd-Year-Group-Project/FixLanka/api/repairer-applications.php';

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
            renderApplicationsTable(data.applications || []);
        } else {
            console.error('API Error:', data.error);
        }

    } catch (error) {
        console.error('Error loading applications:', error);
    }
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
        const d = new Date(app.applied_date);
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
        tr.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                        ${app.first_name.charAt(0)}${app.last_name.charAt(0)}
                    </div>
                    <div>
                        <div style="font-weight: 500;">${app.first_name} ${app.last_name}</div>
                        <div style="font-size: 12px; color: #666;">${app.email}</div>
                    </div>
                </div>
            </td>
            <td>
                <div style="font-weight: 500;">${app.job_title}</div>
            </td>
            <td>${formattedDate}</td>
            <td>${app.experience_years} years</td>
            <td>${statusBadge}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 5px;">
                    <i class="fas fa-star" style="color: #ffc107; font-size: 12px;"></i>
                    <span>${parseFloat(app.rating).toFixed(1)}</span>
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn view-btn" title="View Application" onclick="viewApplication(${app.application_id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${app.status === 'pending' || app.status === 'reviewed' ? `
                    <button class="action-btn approve-btn" title="Approve & Hire" onclick="approveApplication(${app.application_id}, event)">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="action-btn reject-btn" title="Reject" onclick="rejectApplication(${app.application_id}, event)">
                        <i class="fas fa-times"></i>
                    </button>
                    ` : ''}
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

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
            alert('Application approved! They have been added to your employees.');
            loadApplications();

            // Reload employees and freelancers table as well
            if (window.companyEmployees) window.companyEmployees.load();
            if (window.freelancersDb) window.freelancersDb.load();
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
