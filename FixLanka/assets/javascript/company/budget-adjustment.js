/**
 * Budget Adjustment System
 * Handle budget change requests for flexible contracts
 * 
 * @package FixLanka
 * @version 1.0.0
 */

/**
 * Initialize budget adjustment UI for a contract
 * @param {string} contractId - Contract ID
 * @param {string} budgetType - Budget type (fixed/flexible)
 */
async function initializeBudgetAdjustment(contractId, budgetType) {
    if (budgetType !== 'flexible') {
        return; // Only flexible contracts can adjust budget
    }

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_budget_adjustments',
                contract_id: contractId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            displayBudgetAdjustments(result.adjustments, result.current_budget, contractId);
        } else {
            throw new Error(result.message || 'Failed to load budget adjustments');
        }
    } catch (error) {
        console.error('Budget load error:', error);
        showToast('error', 'Load Failed', error.message);
    }
}

/**
 * Display budget adjustments and request form
 * @param {Array} adjustments - Array of adjustment records
 * @param {number} currentBudget - Current budget amount
 * @param {string} contractId - Contract ID
 */
function displayBudgetAdjustments(adjustments, currentBudget, contractId) {
    const container = document.getElementById('budgetAdjustmentContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="budget-adjustment-wrapper">
            <div class="current-budget-card">
                <div class="budget-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="budget-info">
                    <div class="budget-label">Current Contract Budget</div>
                    <div class="budget-amount">${formatCurrency(currentBudget)}</div>
                </div>
            </div>

            <div class="budget-request-section">
                <h4><i class="fas fa-edit"></i> Request Budget Adjustment</h4>
                <form id="budgetAdjustmentForm" onsubmit="submitBudgetAdjustment(event, '${contractId}')">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="newBudget">New Budget Amount *</label>
                            <input type="number" id="newBudget" name="new_budget" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="budgetChange">Change Amount</label>
                            <input type="text" id="budgetChange" readonly class="change-display">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="adjustmentReason">Reason for Adjustment *</label>
                        <textarea id="adjustmentReason" name="reason" rows="4" required 
                                  placeholder="Explain why the budget needs to be adjusted..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="supportingDocs">Supporting Documents (Optional)</label>
                        <input type="file" id="supportingDocs" name="documents[]" multiple 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small>Upload receipts, quotes, or other supporting documentation</small>
                    </div>

                    <button type="submit" class="btn-submit-adjustment">
                        <i class="fas fa-paper-plane"></i> Submit Adjustment Request
                    </button>
                </form>
            </div>

            ${adjustments && adjustments.length > 0 ? `
                <div class="adjustment-history-section">
                    <h4><i class="fas fa-history"></i> Adjustment History</h4>
                    <div class="adjustments-list">
                        ${adjustments.map(adj => renderAdjustmentCard(adj)).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `;

    // Setup budget change calculator
    document.getElementById('newBudget').addEventListener('input', function() {
        const newBudget = parseFloat(this.value) || 0;
        const change = newBudget - currentBudget;
        const changeDisplay = document.getElementById('budgetChange');
        
        if (change > 0) {
            changeDisplay.value = `+${formatCurrency(change)} (Increase)`;
            changeDisplay.style.color = '#28a745';
        } else if (change < 0) {
            changeDisplay.value = `${formatCurrency(change)} (Decrease)`;
            changeDisplay.style.color = '#dc3545';
        } else {
            changeDisplay.value = 'No Change';
            changeDisplay.style.color = '#6c757d';
        }
    });
}

/**
 * Render individual adjustment card
 * @param {Object} adjustment - Adjustment object
 * @returns {string} HTML string
 */
function renderAdjustmentCard(adjustment) {
    const change = parseFloat(adjustment.new_budget) - parseFloat(adjustment.old_budget);
    const isIncrease = change > 0;

    return `
        <div class="adjustment-card ${adjustment.status}">
            <div class="adjustment-header">
                <div class="adjustment-date">${formatDate(adjustment.requested_date)}</div>
                <div class="adjustment-status-badge ${adjustment.status}">
                    ${getAdjustmentStatusBadge(adjustment.status)}
                </div>
            </div>

            <div class="adjustment-amounts">
                <div class="amount-item old">
                    <div class="amount-label">Previous Budget</div>
                    <div class="amount-value">${formatCurrency(adjustment.old_budget)}</div>
                </div>
                
                <div class="amount-arrow ${isIncrease ? 'increase' : 'decrease'}">
                    <i class="fas fa-arrow-${isIncrease ? 'up' : 'down'}"></i>
                </div>

                <div class="amount-item new">
                    <div class="amount-label">Requested Budget</div>
                    <div class="amount-value">${formatCurrency(adjustment.new_budget)}</div>
                </div>

                <div class="amount-change ${isIncrease ? 'increase' : 'decrease'}">
                    ${isIncrease ? '+' : ''}${formatCurrency(change)}
                </div>
            </div>

            <div class="adjustment-reason">
                <strong>Reason:</strong> ${adjustment.reason}
            </div>

            ${adjustment.documents && adjustment.documents.length > 0 ? `
                <div class="adjustment-documents">
                    <strong>Documents:</strong>
                    ${adjustment.documents.map(doc => `
                        <a href="${doc.url}" target="_blank" class="doc-link">
                            <i class="fas fa-file-alt"></i> ${doc.name}
                        </a>
                    `).join('')}
                </div>
            ` : ''}

            ${adjustment.status === 'pending' && adjustment.can_approve ? `
                <div class="adjustment-actions">
                    <button onclick="approveBudgetAdjustment(${adjustment.id})" class="btn-adjust approve">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button onclick="rejectBudgetAdjustment(${adjustment.id})" class="btn-adjust reject">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            ` : ''}

            ${adjustment.status === 'approved' ? `
                <div class="approval-info">
                    <i class="fas fa-check-circle"></i>
                    Approved by ${adjustment.approved_by} on ${formatDate(adjustment.approved_date)}
                </div>
            ` : ''}

            ${adjustment.status === 'rejected' ? `
                <div class="rejection-info">
                    <i class="fas fa-times-circle"></i>
                    Rejected by ${adjustment.rejected_by} on ${formatDate(adjustment.rejected_date)}
                    ${adjustment.rejection_reason ? `<br><strong>Reason:</strong> ${adjustment.rejection_reason}` : ''}
                </div>
            ` : ''}
        </div>
    `;
}

/**
 * Get adjustment status badge
 * @param {string} status - Adjustment status
 * @returns {string} Badge HTML
 */
function getAdjustmentStatusBadge(status) {
    const badges = {
        'pending': '<i class="fas fa-clock"></i> Pending Review',
        'approved': '<i class="fas fa-check-circle"></i> Approved',
        'rejected': '<i class="fas fa-times-circle"></i> Rejected'
    };
    return badges[status] || status;
}

/**
 * Submit budget adjustment request
 * @param {Event} event - Form submit event
 * @param {string} contractId - Contract ID
 */
async function submitBudgetAdjustment(event, contractId) {
    event.preventDefault();

    const formData = new FormData(event.target);
    formData.append('action', 'submit_budget_adjustment');
    formData.append('contract_id', contractId);

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Submitted!', 'Budget adjustment request has been submitted for approval');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to submit adjustment request');
        }
    } catch (error) {
        console.error('Submit error:', error);
        showToast('error', 'Submission Failed', error.message);
    }
}

/**
 * Approve budget adjustment
 * @param {number} adjustmentId - Adjustment ID
 */
async function approveBudgetAdjustment(adjustmentId) {
    if (!confirm('Are you sure you want to approve this budget adjustment? The contract budget will be updated.')) {
        return;
    }

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'approve_budget_adjustment',
                adjustment_id: adjustmentId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Approved!', 'Budget adjustment has been approved and applied');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to approve adjustment');
        }
    } catch (error) {
        console.error('Approve error:', error);
        showToast('error', 'Approval Failed', error.message);
    }
}

/**
 * Reject budget adjustment
 * @param {number} adjustmentId - Adjustment ID
 */
async function rejectBudgetAdjustment(adjustmentId) {
    const reason = prompt('Please provide a reason for rejecting this adjustment:');
    if (!reason || reason.trim() === '') return;

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reject_budget_adjustment',
                adjustment_id: adjustmentId,
                reason: reason
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Rejected', 'Budget adjustment request has been rejected');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to reject adjustment');
        }
    } catch (error) {
        console.error('Reject error:', error);
        showToast('error', 'Rejection Failed', error.message);
    }
}

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Show toast notification
 * @param {string} type - Toast type
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 */
function showToast(type, title, message) {
    const toast = document.createElement('div');
    toast.className = `budget-toast ${type} show`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        </div>
        <div class="toast-content">
            <h4>${title}</h4>
            <p>${message}</p>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// Export functions for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initializeBudgetAdjustment,
        submitBudgetAdjustment,
        approveBudgetAdjustment,
        rejectBudgetAdjustment
    };
}
