/**
 * Time & Material Tracking System
 * Track hours, materials, and costs for hourly contracts
 * 
 * @package FixLanka
 * @version 1.0.0
 */

/**
 * Initialize time tracking for hourly contracts
 * @param {string} contractId - Contract ID
 * @param {number} hourlyRate - Hourly rate
 */
async function initializeTimeTracking(contractId, hourlyRate) {
    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'get_time_entries',
                contract_id: contractId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            displayTimeTracking(result.entries, hourlyRate, contractId, result.totals);
        } else {
            throw new Error(result.message || 'Failed to load time entries');
        }
    } catch (error) {
        console.error('Time tracking load error:', error);
        showToast('error', 'Load Failed', error.message);
    }
}

/**
 * Display time tracking interface
 * @param {Array} entries - Array of time entries
 * @param {number} hourlyRate - Hourly rate
 * @param {string} contractId - Contract ID
 * @param {Object} totals - Totals object
 */
function displayTimeTracking(entries, hourlyRate, contractId, totals) {
    const container = document.getElementById('timeTrackingContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="time-tracking-wrapper">
            <div class="time-summary-cards">
                <div class="summary-card hours">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Total Hours</div>
                        <div class="card-value">${totals.total_hours.toFixed(2)}</div>
                    </div>
                </div>

                <div class="summary-card rate">
                    <div class="card-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Hourly Rate</div>
                        <div class="card-value">${formatCurrency(hourlyRate)}</div>
                    </div>
                </div>

                <div class="summary-card cost">
                    <div class="card-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Total Labor Cost</div>
                        <div class="card-value">${formatCurrency(totals.total_cost)}</div>
                    </div>
                </div>

                <div class="summary-card materials">
                    <div class="card-icon">
                        <i class="fas fa-toolbox"></i>
                    </div>
                    <div class="card-content">
                        <div class="card-label">Materials Cost</div>
                        <div class="card-value">${formatCurrency(totals.materials_cost)}</div>
                    </div>
                </div>
            </div>

            <div class="add-entry-section">
                <h4><i class="fas fa-plus-circle"></i> Log Work Time</h4>
                <form id="timeEntryForm" onsubmit="submitTimeEntry(event, '${contractId}', ${hourlyRate})">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="workDate">Date *</label>
                            <input type="date" id="workDate" name="work_date" required max="${new Date().toISOString().split('T')[0]}">
                        </div>
                        <div class="form-group">
                            <label for="hoursWorked">Hours Worked *</label>
                            <input type="number" id="hoursWorked" name="hours" min="0.25" step="0.25" max="24" required>
                        </div>
                        <div class="form-group">
                            <label for="laborCost">Labor Cost</label>
                            <input type="text" id="laborCost" readonly class="cost-display">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="workDescription">Work Description *</label>
                        <textarea id="workDescription" name="description" rows="3" required 
                                  placeholder="Describe the work performed..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="includeMaterials" onchange="toggleMaterialsFields()">
                            Include Materials Used
                        </label>
                    </div>

                    <div id="materialsSection" class="materials-fields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="materialsDescription">Materials Description</label>
                                <textarea id="materialsDescription" name="materials_description" rows="2" 
                                          placeholder="List materials used..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="materialsCost">Materials Cost</label>
                                <input type="number" id="materialsCost" name="materials_cost" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-entry">
                        <i class="fas fa-save"></i> Log Time Entry
                    </button>
                </form>
            </div>

            ${entries && entries.length > 0 ? `
                <div class="entries-history-section">
                    <h4><i class="fas fa-list"></i> Time Entry History</h4>
                    <div class="entries-list">
                        ${entries.map(entry => renderTimeEntryCard(entry)).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `;

    // Setup hours calculator
    document.getElementById('hoursWorked').addEventListener('input', function() {
        const hours = parseFloat(this.value) || 0;
        const cost = hours * hourlyRate;
        document.getElementById('laborCost').value = formatCurrency(cost);
    });
}

/**
 * Toggle materials fields visibility
 */
function toggleMaterialsFields() {
    const section = document.getElementById('materialsSection');
    const checkbox = document.getElementById('includeMaterials');
    section.style.display = checkbox.checked ? 'block' : 'none';
}

/**
 * Render individual time entry card
 * @param {Object} entry - Time entry object
 * @returns {string} HTML string
 */
function renderTimeEntryCard(entry) {
    return `
        <div class="time-entry-card ${entry.status}">
            <div class="entry-header">
                <div class="entry-date">
                    <i class="fas fa-calendar"></i>
                    ${formatDate(entry.work_date)}
                </div>
                <div class="entry-status-badge ${entry.status}">
                    ${getEntryStatusBadge(entry.status)}
                </div>
            </div>

            <div class="entry-time-info">
                <div class="time-badge">
                    <i class="fas fa-clock"></i>
                    <span>${entry.hours} hours</span>
                </div>
                <div class="cost-badge">
                    <i class="fas fa-dollar-sign"></i>
                    <span>${formatCurrency(entry.labor_cost)}</span>
                </div>
                ${entry.materials_cost > 0 ? `
                    <div class="materials-badge">
                        <i class="fas fa-toolbox"></i>
                        <span>${formatCurrency(entry.materials_cost)}</span>
                    </div>
                ` : ''}
            </div>

            <div class="entry-description">
                <strong>Work Performed:</strong><br>
                ${entry.description}
            </div>

            ${entry.materials_description ? `
                <div class="entry-materials">
                    <strong>Materials Used:</strong><br>
                    ${entry.materials_description}
                </div>
            ` : ''}

            <div class="entry-footer">
                <div class="entry-meta">
                    <span class="entry-worker">
                        <i class="fas fa-user"></i> ${entry.logged_by}
                    </span>
                    <span class="entry-time">
                        <i class="fas fa-clock"></i> ${formatDateTime(entry.logged_at)}
                    </span>
                </div>

                ${entry.status === 'pending' && entry.can_approve ? `
                    <div class="entry-actions">
                        <button onclick="approveTimeEntry(${entry.id})" class="btn-entry-action approve">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button onclick="rejectTimeEntry(${entry.id})" class="btn-entry-action reject">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </div>
                ` : ''}

                ${entry.status === 'approved' ? `
                    <div class="approval-note">
                        <i class="fas fa-check-circle"></i>
                        Approved by ${entry.approved_by}
                    </div>
                ` : ''}

                ${entry.status === 'rejected' ? `
                    <div class="rejection-note">
                        <i class="fas fa-times-circle"></i>
                        Rejected: ${entry.rejection_reason || 'No reason provided'}
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

/**
 * Get entry status badge
 * @param {string} status - Entry status
 * @returns {string} Badge HTML
 */
function getEntryStatusBadge(status) {
    const badges = {
        'pending': '<i class="fas fa-clock"></i> Pending',
        'approved': '<i class="fas fa-check-circle"></i> Approved',
        'rejected': '<i class="fas fa-times-circle"></i> Rejected'
    };
    return badges[status] || status;
}

/**
 * Submit new time entry
 * @param {Event} event - Form submit event
 * @param {string} contractId - Contract ID
 * @param {number} hourlyRate - Hourly rate
 */
async function submitTimeEntry(event, contractId, hourlyRate) {
    event.preventDefault();

    const formData = new FormData(event.target);
    formData.append('action', 'submit_time_entry');
    formData.append('contract_id', contractId);
    formData.append('hourly_rate', hourlyRate);

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Logged!', 'Time entry has been logged successfully');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to submit time entry');
        }
    } catch (error) {
        console.error('Submit error:', error);
        showToast('error', 'Submission Failed', error.message);
    }
}

/**
 * Approve time entry
 * @param {number} entryId - Entry ID
 */
async function approveTimeEntry(entryId) {
    if (!confirm('Are you sure you want to approve this time entry?')) {
        return;
    }

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'approve_time_entry',
                entry_id: entryId
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Approved!', 'Time entry has been approved');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to approve entry');
        }
    } catch (error) {
        console.error('Approve error:', error);
        showToast('error', 'Approval Failed', error.message);
    }
}

/**
 * Reject time entry
 * @param {number} entryId - Entry ID
 */
async function rejectTimeEntry(entryId) {
    const reason = prompt('Please provide a reason for rejecting this entry:');
    if (!reason || reason.trim() === '') return;

    try {
        const response = await fetch('api/contracts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'reject_time_entry',
                entry_id: entryId,
                reason: reason
            })
        });

        const result = await response.json();
        
        if (result.success) {
            showToast('success', 'Rejected', 'Time entry has been rejected');
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(result.message || 'Failed to reject entry');
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
 * Format date and time
 * @param {string} dateString - Date string
 * @returns {string} Formatted date/time
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
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
    toast.className = `time-toast ${type} show`;
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
        initializeTimeTracking,
        submitTimeEntry,
        approveTimeEntry,
        rejectTimeEntry
    };
}
