/**
 * Escrow Account Management System
 * Display escrow status, balances, and fund releases
 * 
 * @package FixLanka
 * @version 1.0.0
 */

/**
 * Initialize escrow display for a contract
 * @param {string} contractId - Contract ID
 */
async function initializeEscrowDisplay(contractId) {
    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_escrow_status',
                contract_id: contractId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            displayEscrowStatus(result.escrow);
        } else {
            throw new Error(result.message || 'Failed to load escrow status');
        }
    } catch (error) {
        console.error('Escrow load error:', error);
        showToast('error', 'Load Failed', error.message);
    }
}

/**
 * Display escrow account status and balances
 * @param {Object} escrow - Escrow data object
 */
function displayEscrowStatus(escrow) {
    const container = document.getElementById('escrowStatusContainer');
    if (!container) return;

    // Calculate percentages
    const totalFunds = parseFloat(escrow.total_deposited || 0);
    const releasedFunds = parseFloat(escrow.total_released || 0);
    const heldFunds = totalFunds - releasedFunds;
    const releasePercentage = totalFunds > 0 ? (releasedFunds / totalFunds * 100).toFixed(1) : 0;

    container.innerHTML = `
        <div class="escrow-card">
            <div class="escrow-header">
                <div class="escrow-icon ${escrow.status}">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="escrow-info">
                    <h3>Escrow Account</h3>
                    <div class="escrow-status-badge ${escrow.status}">
                        ${getEscrowStatusText(escrow.status)}
                    </div>
                </div>
            </div>

            <div class="escrow-balances">
                <div class="balance-item total">
                    <div class="balance-label">
                        <i class="fas fa-dollar-sign"></i>
                        Total Deposited
                    </div>
                    <div class="balance-amount">${formatCurrency(totalFunds)}</div>
                </div>

                <div class="balance-divider"></div>

                <div class="balance-item released">
                    <div class="balance-label">
                        <i class="fas fa-check-circle"></i>
                        Released to Company
                    </div>
                    <div class="balance-amount">${formatCurrency(releasedFunds)}</div>
                    <div class="balance-percentage">${releasePercentage}%</div>
                </div>

                <div class="balance-item held">
                    <div class="balance-label">
                        <i class="fas fa-lock"></i>
                        Held in Escrow
                    </div>
                    <div class="balance-amount">${formatCurrency(heldFunds)}</div>
                </div>
            </div>

            <div class="escrow-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${releasePercentage}%"></div>
                </div>
                <div class="progress-labels">
                    <span>Released: ${releasePercentage}%</span>
                    <span>Remaining: ${(100 - releasePercentage).toFixed(1)}%</span>
                </div>
            </div>

            ${escrow.status === 'active' ? renderPendingReleases(escrow.pending_releases) : ''}
            ${renderReleaseHistory(escrow.release_history)}
        </div>
    `;
}

/**
 * Get human-readable escrow status text
 * @param {string} status - Escrow status code
 * @returns {string} Status text
 */
function getEscrowStatusText(status) {
    const statusMap = {
        'active': 'Active',
        'completed': 'Completed',
        'refunded': 'Refunded',
        'disputed': 'Under Dispute',
        'pending': 'Pending Setup'
    };
    return statusMap[status] || status;
}

/**
 * Render pending release requests
 * @param {Array} pendingReleases - Array of pending releases
 * @returns {string} HTML string
 */
function renderPendingReleases(pendingReleases) {
    if (!pendingReleases || pendingReleases.length === 0) return '';

    return `
        <div class="pending-releases-section">
            <h4><i class="fas fa-clock"></i> Pending Releases</h4>
            ${pendingReleases.map(release => `
                <div class="release-request-card">
                    <div class="release-info">
                        <div class="release-milestone">
                            <i class="fas fa-flag-checkered"></i>
                            ${release.milestone_name || 'Milestone ' + release.milestone_number}
                        </div>
                        <div class="release-amount">${formatCurrency(release.amount)}</div>
                    </div>
                    <div class="release-status">
                        <span class="status-badge pending">
                            <i class="fas fa-hourglass-half"></i>
                            Awaiting Approval
                        </span>
                    </div>
                    ${release.can_approve ? `
                        <div class="release-actions">
                            <button onclick="approveRelease(${release.id})" class="btn-release approve">
                                <i class="fas fa-check"></i> Approve Release
                            </button>
                            <button onclick="denyRelease(${release.id})" class="btn-release deny">
                                <i class="fas fa-times"></i> Deny
                            </button>
                        </div>
                    ` : ''}
                </div>
            `).join('')}
        </div>
    `;
}

/**
 * Render escrow release history
 * @param {Array} history - Array of past releases
 * @returns {string} HTML string
 */
function renderReleaseHistory(history) {
    if (!history || history.length === 0) return '';

    return `
        <div class="release-history-section">
            <h4><i class="fas fa-history"></i> Release History</h4>
            <div class="history-list">
                ${history.map(entry => `
                    <div class="history-entry">
                        <div class="history-icon ${entry.status}">
                            <i class="fas fa-${entry.status === 'released' ? 'check' : 'times'}"></i>
                        </div>
                        <div class="history-details">
                            <div class="history-milestone">${entry.milestone_name}</div>
                            <div class="history-date">${formatDate(entry.date)}</div>
                        </div>
                        <div class="history-amount ${entry.status}">
                            ${formatCurrency(entry.amount)}
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
}

/**
 * Approve escrow release request
 * @param {number} releaseId - Release request ID
 */
async function approveRelease(releaseId) {
    if (!confirm('Are you sure you want to approve this fund release? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'approve_escrow_release',
                release_id: releaseId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Approved!', 'Funds have been released to the company');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to approve release');
        }
    } catch (error) {
        console.error('Approve error:', error);
        showToast('error', 'Approval Failed', error.message);
    }
}

/**
 * Deny escrow release request
 * @param {number} releaseId - Release request ID
 */
async function denyRelease(releaseId) {
    const reason = prompt('Please provide a reason for denying this release:');
    if (!reason || reason.trim() === '') return;

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'deny_escrow_release',
                release_id: releaseId,
                reason: reason
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Denied', 'Release request has been denied');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to deny release');
        }
    } catch (error) {
        console.error('Deny error:', error);
        showToast('error', 'Denial Failed', error.message);
    }
}

/**
 * Format currency
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toLocaleString('en-LK', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

/**
 * Format date
 * @param {string} dateString - Date string to format
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Show toast notification
 * @param {string} type - Toast type (success/error)
 * @param {string} title - Toast title
 * @param {string} message - Toast message
 */
function showToast(type, title, message) {
    const toast = document.createElement('div');
    toast.className = `escrow-toast ${type} show`;
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
        initializeEscrowDisplay,
        approveRelease,
        denyRelease
    };
}
