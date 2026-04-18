/**
 * Milestone Workflow System
 * Complete milestone management for companies and customers
 * 
 * @package FixLanka
 * @version 1.0.0
 */

// ============================================
// MILESTONE SUBMISSION (Company Side)
// ============================================

/**
 * Submit milestone for customer approval
 * @param {number} milestoneId - Milestone ID
 * @param {string} milestoneTitle - Milestone title for confirmation
 */
async function submitMilestone(milestoneId, milestoneTitle) {
    const confirmed = confirm(
        `Submit Milestone for Approval\n\n` +
        `"${milestoneTitle}"\n\n` +
        `This will:\n` +
        `✓ Notify the customer\n` +
        `✓ Request approval\n` +
        `✓ Require customer verification\n\n` +
        `Make sure all work is complete before submitting.\n\n` +
        `Continue?`
    );
    
    if (!confirmed) return;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'submit_milestone',
                milestone_id: milestoneId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMilestoneSuccess('Milestone Submitted!', 'Customer will be notified to review and approve.');
            setTimeout(() => location.reload(), 2000);
        } else {
            showMilestoneError('Submission Failed', result.message || 'Unable to submit milestone.');
        }
    } catch (error) {
        console.error('Submit milestone error:', error);
        showMilestoneError('Network Error', 'Unable to connect to server.');
    }
}

/**
 * Mark milestone work as started
 * @param {number} milestoneId - Milestone ID
 */
async function markWorkStarted(milestoneId) {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_work_started',
                milestone_id: milestoneId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMilestoneSuccess('Work Started', 'Milestone marked as in progress.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showMilestoneError('Error', result.message || 'Failed to update status.');
        }
    } catch (error) {
        console.error('Mark work started error:', error);
        showMilestoneError('Network Error', 'Unable to connect to server.');
    }
}

/**
 * Mark milestone work as completed
 * @param {number} milestoneId - Milestone ID
 */
async function markWorkCompleted(milestoneId) {
    const confirmed = confirm(
        'Mark this milestone as completed?\n\n' +
        'This indicates all work for this milestone is finished.\n' +
        'You can then submit it for customer approval.'
    );
    
    if (!confirmed) return;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'mark_work_completed',
                milestone_id: milestoneId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMilestoneSuccess('Work Completed', 'Ready to submit for customer approval.');
            setTimeout(() => location.reload(), 1500);
        } else {
            showMilestoneError('Error', result.message || 'Failed to update status.');
        }
    } catch (error) {
        console.error('Mark work completed error:', error);
        showMilestoneError('Network Error', 'Unable to connect to server.');
    }
}

// ============================================
// MILESTONE APPROVAL (Customer Side)
// ============================================

/**
 * Approve milestone
 * @param {number} milestoneId - Milestone ID
 * @param {string} milestoneTitle - Milestone title
 */
async function approveMilestone(milestoneId, milestoneTitle) {
    const confirmed = confirm(
        `Approve Milestone\n\n` +
        `"${milestoneTitle}"\n\n` +
        `By approving, you confirm:\n` +
        `✓ Work meets expectations\n` +
        `✓ Quality is acceptable\n` +
        `✓ Payment will be released\n\n` +
        `Continue with approval?`
    );
    
    if (!confirmed) return;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'approve_milestone',
                milestone_id: milestoneId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMilestoneSuccess('Milestone Approved!', 'Payment will be processed shortly.');
            setTimeout(() => location.reload(), 2000);
        } else {
            showMilestoneError('Approval Failed', result.message || 'Unable to approve milestone.');
        }
    } catch (error) {
        console.error('Approve milestone error:', error);
        showMilestoneError('Network Error', 'Unable to connect to server.');
    }
}

/**
 * Reject milestone with reason
 * @param {number} milestoneId - Milestone ID
 * @param {string} milestoneTitle - Milestone title
 */
function rejectMilestone(milestoneId, milestoneTitle) {
    // Show rejection modal
    showRejectionModal(milestoneId, milestoneTitle);
}

/**
 * Show rejection reason modal
 * @param {number} milestoneId - Milestone ID
 * @param {string} milestoneTitle - Milestone title
 */
function showRejectionModal(milestoneId, milestoneTitle) {
    const modal = document.createElement('div');
    modal.className = 'milestone-modal-overlay';
    modal.innerHTML = `
        <div class="milestone-modal rejection-modal">
            <div class="milestone-modal-header">
                <h3><i class="fas fa-times-circle"></i> Reject Milestone</h3>
                <button class="close-modal" onclick="this.closest('.milestone-modal-overlay').remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="milestone-modal-body">
                <p class="milestone-title">"${milestoneTitle}"</p>
                <p class="rejection-info">Please provide a reason for rejection. This helps the company understand what needs to be improved.</p>
                <textarea 
                    id="rejectionReason" 
                    class="rejection-textarea" 
                    placeholder="Explain what issues need to be addressed..."
                    rows="5"
                    required
                    maxlength="1000"
                ></textarea>
                <div class="character-count">
                    <span id="charCount">0</span> / 1000 characters
                </div>
            </div>
            <div class="milestone-modal-footer">
                <button class="btn-cancel" onclick="this.closest('.milestone-modal-overlay').remove()">
                    Cancel
                </button>
                <button class="btn-reject-confirm" onclick="confirmMilestoneRejection(${milestoneId})">
                    <i class="fas fa-times-circle"></i> Reject Milestone
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Auto-focus textarea
    const textarea = modal.querySelector('#rejectionReason');
    textarea.focus();
    
    // Character counter
    textarea.addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length;
    });
}

/**
 * Confirm milestone rejection with reason
 * @param {number} milestoneId - Milestone ID
 */
async function confirmMilestoneRejection(milestoneId) {
    const reason = document.getElementById('rejectionReason').value.trim();
    
    if (!reason) {
        alert('Please provide a reason for rejection.');
        return;
    }
    
    if (reason.length < 10) {
        alert('Please provide a more detailed reason (at least 10 characters).');
        return;
    }
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reject_milestone',
                milestone_id: milestoneId,
                reason: reason
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.querySelector('.milestone-modal-overlay').remove();
            showMilestoneSuccess('Milestone Rejected', 'Company has been notified to make improvements.');
            setTimeout(() => location.reload(), 2000);
        } else {
            showMilestoneError('Rejection Failed', result.message || 'Unable to reject milestone.');
        }
    } catch (error) {
        console.error('Reject milestone error:', error);
        showMilestoneError('Network Error', 'Unable to connect to server.');
    }
}

// ============================================
// MILESTONE UI HELPERS
// ============================================

/**
 * Get milestone status badge HTML
 * @param {Object} milestone - Milestone object
 * @returns {string} - HTML string
 */
function getMilestoneStatusBadge(milestone) {
    if (milestone.customer_verified) {
        return '<span class="milestone-badge completed"><i class="fas fa-check-double"></i> Verified</span>';
    }
    if (milestone.customer_approved) {
        return '<span class="milestone-badge approved"><i class="fas fa-check-circle"></i> Approved</span>';
    }
    if (milestone.submitted_by_company) {
        return '<span class="milestone-badge pending"><i class="fas fa-hourglass-half"></i> Awaiting Approval</span>';
    }
    if (milestone.work_completed) {
        return '<span class="milestone-badge completed-work"><i class="fas fa-check"></i> Work Completed</span>';
    }
    if (milestone.work_started) {
        return '<span class="milestone-badge in-progress"><i class="fas fa-play"></i> In Progress</span>';
    }
    return '<span class="milestone-badge not-started"><i class="fas fa-clock"></i> Not Started</span>';
}

/**
 * Get milestone action buttons (company view)
 * @param {Object} milestone - Milestone object
 * @returns {string} - HTML string
 */
function getMilestoneCompanyActions(milestone) {
    let html = '<div class="milestone-actions">';
    
    if (!milestone.work_started) {
        html += `
            <button class="btn-milestone-action start" onclick="markWorkStarted(${milestone.milestone_id})">
                <i class="fas fa-play"></i> Start Work
            </button>
        `;
    }
    
    if (milestone.work_started && !milestone.work_completed) {
        html += `
            <button class="btn-milestone-action complete" onclick="markWorkCompleted(${milestone.milestone_id})">
                <i class="fas fa-check"></i> Mark Completed
            </button>
        `;
    }
    
    if (milestone.work_completed && !milestone.submitted_by_company) {
        html += `
            <button class="btn-milestone-action submit" onclick="submitMilestone(${milestone.milestone_id}, '${milestone.description}')">
                <i class="fas fa-paper-plane"></i> Submit for Approval
            </button>
        `;
    }
    
    if (milestone.submitted_by_company && !milestone.customer_approved) {
        html += `
            <span class="milestone-waiting">
                <i class="fas fa-hourglass-half"></i> Waiting for customer approval...
            </span>
        `;
    }
    
    html += '</div>';
    return html;
}

/**
 * Get milestone action buttons (customer view)
 * @param {Object} milestone - Milestone object
 * @returns {string} - HTML string
 */
function getMilestoneCustomerActions(milestone) {
    if (milestone.submitted_by_company && !milestone.customer_approved) {
        return `
            <div class="milestone-approval-actions">
                <button class="btn-milestone-approve" onclick="approveMilestone(${milestone.milestone_id}, '${milestone.description}')">
                    <i class="fas fa-check-circle"></i> Approve Milestone
                </button>
                <button class="btn-milestone-reject" onclick="rejectMilestone(${milestone.milestone_id}, '${milestone.description}')">
                    <i class="fas fa-times-circle"></i> Reject & Request Changes
                </button>
            </div>
        `;
    }
    return '';
}

// ============================================
// SUCCESS/ERROR MODALS
// ============================================

/**
 * Show success message
 * @param {string} title - Success title
 * @param {string} message - Success message
 */
function showMilestoneSuccess(title, message) {
    const modal = document.createElement('div');
    modal.className = 'milestone-toast success';
    modal.innerHTML = `
        <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(modal);
    
    setTimeout(() => {
        modal.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }, 3000);
}

/**
 * Show error message
 * @param {string} title - Error title
 * @param {string} message - Error message
 */
function showMilestoneError(title, message) {
    const modal = document.createElement('div');
    modal.className = 'milestone-toast error';
    modal.innerHTML = `
        <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
    `;
    document.body.appendChild(modal);
    
    setTimeout(() => {
        modal.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }, 3000);
}

// Export functions
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        submitMilestone,
        markWorkStarted,
        markWorkCompleted,
        approveMilestone,
        rejectMilestone,
        getMilestoneStatusBadge,
        getMilestoneCompanyActions,
        getMilestoneCustomerActions
    };
}
