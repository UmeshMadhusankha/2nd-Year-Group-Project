/**
 * Milestone Plan Review JavaScript (Customer View)
 * Phase 4 - Feature 1: Customer approval interface
 */

let contractData = {};
let milestones = [];
const API_BASE = '/2nd-Year-Group-Project/FixLanka/api/milestones.php';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    loadMilestonePlan();
});

/**
 * Load milestone plan from API
 */
function loadMilestonePlan() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');

    if (!contractId) {
        showToast('Error', 'No contract ID provided', 'error');
        return;
    }

    // Fetch contract data
    Promise.all([
        fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get_details&contract_id=${contractId}`).then(r => r.json()),
        fetch(`${API_BASE}?action=get_plan&contract_id=${contractId}`).then(r => r.json())
    ])
        .then(([contractResponse, milestonesResponse]) => {
            if (contractResponse.success && milestonesResponse.success) {
                contractData = contractResponse.contract;
                milestones = milestonesResponse.milestones;

                displayContractInfo();
                displayPaymentBreakdown();
                displayMilestones();
            } else {
                showToast('Error', 'Failed to load milestone plan', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading data:', error);
            showToast('Error', 'Failed to load data', 'error');
        });
}

/**
 * Display contract information
 */
function displayContractInfo() {
    document.getElementById('contractId').textContent = contractData.contract_id || 'N/A';
    document.getElementById('totalBudget').textContent = formatCurrency(contractData.total_budget || 0);
    document.getElementById('totalMilestones').textContent = milestones.length;

    if (contractData.start_date && contractData.end_date) {
        const start = new Date(contractData.start_date);
        const end = new Date(contractData.end_date);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        document.getElementById('duration').textContent = `${days} days`;
    }
}

/**
 * Display payment breakdown
 */
function displayPaymentBreakdown() {
    const container = document.getElementById('paymentBreakdown');
    container.innerHTML = '';

    milestones.forEach((milestone, index) => {
        const item = document.createElement('div');
        item.className = 'breakdown-item';
        item.innerHTML = `
            <div class="breakdown-label">Milestone ${index + 1}</div>
            <div class="breakdown-value">${milestone.percentage}%</div>
            <div class="breakdown-label">${formatCurrency(milestone.amount)}</div>
        `;
        container.appendChild(item);
    });
}

/**
 * Display milestones in timeline view
 */
function displayMilestones() {
    displayTimelineView();
    displayGridView();
}

/**
 * Display timeline view
 */
function displayTimelineView() {
    const container = document.getElementById('timelineView');
    container.innerHTML = '';

    milestones.forEach((milestone, index) => {
        const item = document.createElement('div');
        item.className = 'timeline-item';

        // Parse deliverables
        let deliverables = [];
        if (milestone.deliverables) {
            try {
                deliverables = typeof milestone.deliverables === 'string'
                    ? JSON.parse(milestone.deliverables)
                    : milestone.deliverables;
            } catch (e) {
                deliverables = [];
            }
        }

        // Find dependency name
        let dependencyText = '';
        if (milestone.depends_on_milestone) {
            const depMilestone = milestones.find(m => m.milestone_id == milestone.depends_on_milestone);
            if (depMilestone) {
                dependencyText = `⚠️ Depends on: ${depMilestone.milestone_name}`;
            }
        }

        item.innerHTML = `
            <div class="timeline-marker">${index + 1}</div>
            <div class="timeline-content">
                <div class="milestone-header">
                    <div class="milestone-title">
                        <h3>${milestone.milestone_name}</h3>
                        <div class="milestone-subtitle">Milestone ${index + 1} of ${milestones.length}</div>
                    </div>
                    <div class="milestone-payment">
                        <div class="payment-percentage">${milestone.percentage}%</div>
                        <div class="payment-amount">${formatCurrency(milestone.amount)}</div>
                    </div>
                </div>
                
                ${milestone.milestone_description ? `
                    <div class="milestone-description">${escapeHtml(milestone.milestone_description)}</div>
                ` : ''}
                
                ${milestone.planned_start_date || milestone.planned_end_date ? `
                    <div class="milestone-dates">
                        ${milestone.planned_start_date ? `
                            <div class="date-item">
                                <span>📅 Start:</span>
                                <span>${formatDate(milestone.planned_start_date)}</span>
                            </div>
                        ` : ''}
                        ${milestone.planned_end_date ? `
                            <div class="date-item">
                                <span>🎯 Due:</span>
                                <span>${formatDate(milestone.planned_end_date)}</span>
                            </div>
                        ` : ''}
                        ${milestone.planned_start_date && milestone.planned_end_date ? `
                            <div class="date-item">
                                <span>⏱️ Duration:</span>
                                <span>${calculateDuration(milestone.planned_start_date, milestone.planned_end_date)} days</span>
                            </div>
                        ` : ''}
                    </div>
                ` : ''}
                
                ${deliverables && deliverables.length > 0 && deliverables[0] ? `
                    <div class="deliverables-list">
                        <h4>📦 Expected Deliverables:</h4>
                        <ul>
                            ${deliverables.map(d => `<li>${escapeHtml(d)}</li>`).join('')}
                        </ul>
                    </div>
                ` : ''}
                
                ${dependencyText ? `
                    <div class="dependency-note">
                        <span>⚠️</span>
                        <span>${dependencyText}</span>
                    </div>
                ` : ''}
            </div>
        `;

        container.appendChild(item);
    });
}

/**
 * Display grid view
 */
function displayGridView() {
    const container = document.getElementById('gridView');
    container.innerHTML = '';

    milestones.forEach((milestone, index) => {
        const card = document.createElement('div');
        card.className = 'milestone-card';

        let deliverables = [];
        if (milestone.deliverables) {
            try {
                deliverables = typeof milestone.deliverables === 'string'
                    ? JSON.parse(milestone.deliverables)
                    : milestone.deliverables;
            } catch (e) {
                deliverables = [];
            }
        }

        card.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Milestone ${index + 1}</h3>
                <span class="payment-percentage">${milestone.percentage}%</span>
            </div>
            <h4 style="margin: 10px 0; color: #2c3e50;">${milestone.milestone_name}</h4>
            <div class="payment-amount" style="margin-bottom: 15px;">${formatCurrency(milestone.amount)}</div>
            ${milestone.milestone_description ? `
                <p style="color: #666; font-size: 14px; line-height: 1.6;">${escapeHtml(milestone.milestone_description)}</p>
            ` : ''}
            ${deliverables && deliverables.length > 0 && deliverables[0] ? `
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                    <strong style="font-size: 13px; color: #7f8c8d;">Deliverables:</strong>
                    <ul style="margin: 10px 0; padding-left: 20px; font-size: 13px;">
                        ${deliverables.map(d => `<li>${escapeHtml(d)}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        `;

        container.appendChild(card);
    });
}

/**
 * Switch between timeline and grid view
 */
function switchView(view) {
    const timelineView = document.getElementById('timelineView');
    const gridView = document.getElementById('gridView');
    const buttons = document.querySelectorAll('.view-toggle button');

    buttons.forEach(btn => btn.classList.remove('active'));

    if (view === 'timeline') {
        timelineView.classList.add('active');
        gridView.classList.remove('active');
        buttons[0].classList.add('active');
    } else {
        timelineView.classList.remove('active');
        gridView.classList.add('active');
        buttons[1].classList.add('active');
    }
}

/**
 * Show approve confirmation modal
 */
function showApproveModal() {
    document.getElementById('approveModal').classList.add('active');
}

/**
 * Show request changes modal
 */
function showChangesModal() {
    document.getElementById('changesModal').classList.add('active');
}

/**
 * Close modal
 */
function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

/**
 * Approve milestone plan
 */
async function approvePlan() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    const feedback = document.getElementById('feedbackText').value;

    const confirmed = await window.showConfirm('Are you sure you want to approve this milestone plan?', {
        title: 'Approve Milestone Plan',
        confirmText: 'Yes, Approve',
        type: 'success'
    });

    if (!confirmed) {
        return;
    }

    showToast('Processing...', 'Approving milestone plan', 'info');

    const formData = new FormData();
    formData.append('action', 'approve_plan');
    formData.append('contract_id', contractId);
    if (feedback) {
        formData.append('feedback', feedback);
    }

    fetch(API_BASE, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success!', 'Milestone plan approved successfully', 'success');
                closeModal('approveModal');

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/2nd-Year-Group-Project/FixLanka/views/user/contracts.php';
                }, 2000);
            } else {
                showToast('Error', data.message || 'Failed to approve plan', 'error');
            }
        })
        .catch(error => {
            console.error('Approval error:', error);
            showToast('Error', 'Network error. Please try again.', 'error');
        });
}

/**
 * Request changes to milestone plan
 */
function requestChanges() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    const feedback = document.getElementById('changesReason').value;

    if (!feedback || feedback.trim() === '') {
        showToast('Error', 'Please provide feedback for the requested changes', 'error');
        return;
    }

    showToast('Processing...', 'Submitting change request', 'info');

    const formData = new FormData();
    formData.append('action', 'request_changes');
    formData.append('contract_id', contractId);
    formData.append('feedback', feedback);

    fetch(API_BASE, {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success!', 'Change request submitted successfully', 'success');
                closeModal('changesModal');

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/2nd-Year-Group-Project/FixLanka/views/user/contracts.php';
                }, 2000);
            } else {
                showToast('Error', data.message || 'Failed to submit request', 'error');
            }
        })
        .catch(error => {
            console.error('Request error:', error);
            showToast('Error', 'Network error. Please try again.', 'error');
        });
}

/**
 * Calculate duration between dates
 */
function calculateDuration(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
    return days;
}

/**
 * Format currency
 */
function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

/**
 * Show toast notification
 */
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');

    titleEl.textContent = title;
    messageEl.textContent = message;

    if (type === 'success') {
        icon.textContent = '✓';
        toast.className = 'toast success show';
    } else if (type === 'error') {
        icon.textContent = '✗';
        toast.className = 'toast error show';
    } else {
        icon.textContent = 'ℹ';
        toast.className = 'toast info show';
    }

    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
}
