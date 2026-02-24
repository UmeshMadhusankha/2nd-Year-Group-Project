/**
 * Milestone Plan Builder JavaScript
 * Phase 4 - Task 1.16: Dynamic milestone management
 * 
 * Features:
 * - Add/remove milestones dynamically
 * - Real-time percentage calculation
 * - Form validation
 * - Preview mode
 * - Auto-save draft
 */

// Global variables
let milestones = [];
let milestoneCounter = 0;
let contractData = {};
const API_BASE = '/2nd-Year-Group-Project/FixLanka/api/milestones.php';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadContractData();
    loadDraft();
    
    // Add first milestone automatically
    if (milestones.length === 0) {
        addMilestone();
    }
});

/**
 * Load contract data from URL parameter
 */
function loadContractData() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    
    if (!contractId) {
        showToast('Error', 'No contract ID provided', 'error');
        return;
    }
    
    // Fetch contract data from API
    fetch(`/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=get_details&contract_id=${contractId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                contractData = data.contract;
                displayContractInfo();
            } else {
                showToast('Error', data.message || 'Failed to load contract', 'error');
            }
        })
        .catch(error => {
            console.error('Error loading contract:', error);
            showToast('Error', 'Failed to load contract data', 'error');
        });
}

/**
 * Display contract information in header
 */
function displayContractInfo() {
    document.getElementById('contractId').textContent = contractData.contract_id || 'N/A';
    document.getElementById('totalBudget').textContent = formatCurrency(contractData.total_budget || 0);
    document.getElementById('startDate').textContent = formatDate(contractData.start_date) || 'N/A';
    document.getElementById('endDate').textContent = formatDate(contractData.end_date) || 'N/A';
}

/**
 * Add new milestone to the form (Task 1.11)
 */
function addMilestone() {
    milestoneCounter++;
    
    const milestone = {
        id: milestoneCounter,
        name: '',
        description: '',
        percentage: 0,
        planned_start_date: '',
        planned_end_date: '',
        deliverables: [''],
        depends_on: null
    };
    
    milestones.push(milestone);
    renderMilestones();
    calculateTotalPercentage();
}

/**
 * Remove milestone from the form
 */
function removeMilestone(id) {
    if (milestones.length <= 1) {
        showToast('Warning', 'You must have at least 1 milestone', 'error');
        return;
    }
    
    milestones = milestones.filter(m => m.id !== id);
    renderMilestones();
    calculateTotalPercentage();
}

/**
 * Render all milestones (Task 1.11)
 */
function renderMilestones() {
    const container = document.getElementById('milestonesList');
    container.innerHTML = '';
    
    milestones.forEach((milestone, index) => {
        const card = createMilestoneCard(milestone, index + 1);
        container.appendChild(card);
    });
}

/**
 * Create milestone card HTML
 */
function createMilestoneCard(milestone, number) {
    const card = document.createElement('div');
    card.className = 'milestone-card';
    card.innerHTML = `
        <div class="milestone-header">
            <span class="milestone-number">Milestone ${number}</span>
            ${milestones.length > 1 ? `<button class="remove-milestone-btn" onclick="removeMilestone(${milestone.id})">🗑️ Remove</button>` : ''}
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="name_${milestone.id}">Milestone Name *</label>
                <input type="text" id="name_${milestone.id}" value="${milestone.name}" 
                       onchange="updateMilestone(${milestone.id}, 'name', this.value)"
                       placeholder="e.g., Foundation & Structure" required>
            </div>
            <div class="form-group">
                <label for="percentage_${milestone.id}">Payment Percentage * </label>
                <input type="number" id="percentage_${milestone.id}" value="${milestone.percentage}" 
                       onchange="updateMilestone(${milestone.id}, 'percentage', parseFloat(this.value)); calculateTotalPercentage();"
                       min="0" max="100" step="0.01" placeholder="e.g., 25" required>
                <small>Percentage of total budget (Rs. ${formatCurrency(contractData.total_budget || 0)})</small>
            </div>
        </div>
        
        <div class="form-group">
            <label for="description_${milestone.id}">Description</label>
            <textarea id="description_${milestone.id}" 
                      onchange="updateMilestone(${milestone.id}, 'description', this.value)"
                      rows="3" placeholder="Describe what will be accomplished in this milestone">${milestone.description}</textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="start_date_${milestone.id}">Planned Start Date</label>
                <input type="date" id="start_date_${milestone.id}" value="${milestone.planned_start_date}" 
                       onchange="updateMilestone(${milestone.id}, 'planned_start_date', this.value)"
                       min="${contractData.start_date || ''}">
            </div>
            <div class="form-group">
                <label for="end_date_${milestone.id}">Planned End Date</label>
                <input type="date" id="end_date_${milestone.id}" value="${milestone.planned_end_date}" 
                       onchange="updateMilestone(${milestone.id}, 'planned_end_date', this.value)"
                       max="${contractData.end_date || ''}">
            </div>
        </div>
        
        <div class="form-group">
            <label for="depends_on_${milestone.id}">Depends On Milestone</label>
            <select id="depends_on_${milestone.id}" 
                    onchange="updateMilestone(${milestone.id}, 'depends_on', this.value)">
                <option value="">None - Independent</option>
                ${milestones.filter(m => m.id !== milestone.id).map((m, i) => 
                    `<option value="${m.id}" ${milestone.depends_on == m.id ? 'selected' : ''}>
                        Milestone ${i + 1}: ${m.name || 'Untitled'}
                    </option>`
                ).join('')}
            </select>
            <small>Select if this milestone depends on completion of another</small>
        </div>
        
        <div class="deliverables-section">
            <label><strong>Deliverables</strong></label>
            <div id="deliverables_${milestone.id}">
                ${milestone.deliverables.map((d, i) => `
                    <div class="deliverable-item">
                        <input type="text" value="${d}" 
                               onchange="updateDeliverable(${milestone.id}, ${i}, this.value)"
                               placeholder="e.g., Foundation plans approved">
                        ${milestone.deliverables.length > 1 ? 
                            `<button class="remove-deliverable-btn" onclick="removeDeliverable(${milestone.id}, ${i})">×</button>` : 
                            ''}
                    </div>
                `).join('')}
            </div>
            <button class="add-deliverable-btn" onclick="addDeliverable(${milestone.id})">+ Add Deliverable</button>
        </div>
    `;
    
    return card;
}

/**
 * Update milestone data
 */
function updateMilestone(id, field, value) {
    const milestone = milestones.find(m => m.id === id);
    if (milestone) {
        milestone[field] = value;
        saveDraft(); // Auto-save
    }
}

/**
 * Add deliverable to milestone
 */
function addDeliverable(milestoneId) {
    const milestone = milestones.find(m => m.id === milestoneId);
    if (milestone) {
        milestone.deliverables.push('');
        renderMilestones();
    }
}

/**
 * Remove deliverable from milestone
 */
function removeDeliverable(milestoneId, index) {
    const milestone = milestones.find(m => m.id === milestoneId);
    if (milestone && milestone.deliverables.length > 1) {
        milestone.deliverables.splice(index, 1);
        renderMilestones();
    }
}

/**
 * Update deliverable text
 */
function updateDeliverable(milestoneId, index, value) {
    const milestone = milestones.find(m => m.id === milestoneId);
    if (milestone) {
        milestone.deliverables[index] = value;
        saveDraft();
    }
}

/**
 * Calculate total percentage (Task 1.12)
 */
function calculateTotalPercentage() {
    let total = 0;
    milestones.forEach(m => {
        const percentage = parseFloat(m.percentage) || 0;
        total += percentage;
    });
    
    const warningEl = document.getElementById('percentageWarning');
    const totalEl = document.getElementById('totalPercentage');
    const messageEl = document.getElementById('percentageMessage');
    const submitBtn = document.getElementById('submitBtn');
    
    totalEl.textContent = total.toFixed(2) + '%';
    
    if (Math.abs(total - 100) < 0.01) {
        // Valid - exactly 100%
        warningEl.className = 'percentage-warning valid';
        messageEl.textContent = '✓ Perfect! Percentages total 100%. You can submit the plan.';
        submitBtn.disabled = false;
    } else if (total < 100) {
        // Under 100%
        warningEl.className = 'percentage-warning invalid';
        messageEl.textContent = `Need ${(100 - total).toFixed(2)}% more to reach 100%`;
        submitBtn.disabled = true;
    } else {
        // Over 100%
        warningEl.className = 'percentage-warning invalid';
        messageEl.textContent = `Reduce by ${(total - 100).toFixed(2)}% to reach 100%`;
        submitBtn.disabled = true;
    }
    
    return total;
}

/**
 * Validate form before submission (Task 1.18)
 */
function validateForm() {
    const errors = [];
    
    // Check minimum milestones
    if (milestones.length < 2) {
        errors.push('Minimum 2 milestones required');
    }
    
    // Check percentage total
    const total = calculateTotalPercentage();
    if (Math.abs(total - 100) > 0.01) {
        errors.push('Percentages must total exactly 100%');
    }
    
    // Check each milestone has required fields
    milestones.forEach((m, i) => {
        if (!m.name || m.name.trim() === '') {
            errors.push(`Milestone ${i + 1}: Name is required`);
        }
        if (!m.percentage || m.percentage <= 0) {
            errors.push(`Milestone ${i + 1}: Percentage must be greater than 0`);
        }
    });
    
    if (errors.length > 0) {
        showToast('Validation Error', errors.join('\n'), 'error');
        return false;
    }
    
    return true;
}

/**
 * Show preview mode (Task 1.19)
 */
function showPreview() {
    if (!validateForm()) {
        return;
    }
    
    // Hide form, show preview
    document.getElementById('formView').style.display = 'none';
    document.getElementById('previewView').classList.add('active');
    
    // Render timeline
    renderTimeline();
}

/**
 * Hide preview mode
 */
function hidePreview() {
    document.getElementById('formView').style.display = 'block';
    document.getElementById('previewView').classList.remove('active');
}

/**
 * Render timeline preview
 */
function renderTimeline() {
    const container = document.getElementById('timelinePreview');
    container.innerHTML = '';
    
    milestones.forEach((milestone, index) => {
        const amount = (milestone.percentage / 100) * (contractData.total_budget || 0);
        
        const item = document.createElement('div');
        item.className = 'timeline-item';
        item.innerHTML = `
            <div class="timeline-marker">${index + 1}</div>
            <div class="timeline-content">
                <h4>${milestone.name || 'Untitled Milestone'}</h4>
                <span class="percentage">${milestone.percentage}% - ${formatCurrency(amount)}</span>
                ${milestone.description ? `<p>${milestone.description}</p>` : ''}
                ${milestone.planned_start_date || milestone.planned_end_date ? `
                    <p><strong>Timeline:</strong> ${formatDate(milestone.planned_start_date) || 'TBD'} to ${formatDate(milestone.planned_end_date) || 'TBD'}</p>
                ` : ''}
                ${milestone.deliverables && milestone.deliverables.filter(d => d).length > 0 ? `
                    <p><strong>Deliverables:</strong></p>
                    <ul>
                        ${milestone.deliverables.filter(d => d).map(d => `<li>${d}</li>`).join('')}
                    </ul>
                ` : ''}
            </div>
        `;
        container.appendChild(item);
    });
}

/**
 * Submit plan to API (Task 1.7)
 */
function submitPlan() {
    if (!validateForm()) {
        return;
    }
    
    if (!confirm('Submit this milestone plan to the customer for approval?')) {
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    
    // Prepare data
    const planData = {
        contract_id: contractId,
        milestones: milestones.map(m => ({
            name: m.name,
            description: m.description,
            percentage: m.percentage,
            planned_start_date: m.planned_start_date,
            planned_end_date: m.planned_end_date,
            deliverables: m.deliverables.filter(d => d.trim() !== ''),
            depends_on: m.depends_on
        }))
    };
    
    // Show loading
    showToast('Submitting...', 'Please wait while we submit your plan', 'info');
    
    // Submit to API
    fetch(API_BASE + '?action=submit_plan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(planData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success!', 'Milestone plan submitted successfully', 'success');
            clearDraft();
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = '/2nd-Year-Group-Project/FixLanka/views/company/contracts.php';
            }, 2000);
        } else {
            showToast('Error', data.message || 'Failed to submit plan', 'error');
        }
    })
    .catch(error => {
        console.error('Submit error:', error);
        showToast('Error', 'Network error. Please try again.', 'error');
    });
}

/**
 * Save draft to localStorage (Task 1.17)
 */
function saveDraft() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    
    if (contractId) {
        const draftKey = `milestone_plan_draft_${contractId}`;
        localStorage.setItem(draftKey, JSON.stringify({
            milestones: milestones,
            counter: milestoneCounter,
            savedAt: new Date().toISOString()
        }));
    }
}

/**
 * Load draft from localStorage
 */
function loadDraft() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    
    if (contractId) {
        const draftKey = `milestone_plan_draft_${contractId}`;
        const draft = localStorage.getItem(draftKey);
        
        if (draft) {
            const data = JSON.parse(draft);
            milestones = data.milestones || [];
            milestoneCounter = data.counter || 0;
            
            if (milestones.length > 0) {
                renderMilestones();
                calculateTotalPercentage();
                showToast('Info', 'Draft loaded from previous session', 'info');
            }
        }
    }
}

/**
 * Clear draft from localStorage
 */
function clearDraft() {
    const urlParams = new URLSearchParams(window.location.search);
    const contractId = urlParams.get('contract_id');
    
    if (contractId) {
        const draftKey = `milestone_plan_draft_${contractId}`;
        localStorage.removeItem(draftKey);
    }
}

/**
 * Show toast notification
 */
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const icon = document.getElementById('toastIcon');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');
    
    // Set content
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    // Set icon
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
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 5000);
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
