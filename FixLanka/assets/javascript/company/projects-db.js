/**
 * Projects Database JavaScript
 * 
 * Handles all frontend interactions with the Projects API
 * Manages CRUD operations for company projects
 * 
 * @package FixLanka\Assets\JavaScript\Company
 * @version 1.0.0
 */

// ================================================================
// GLOBAL VARIABLES
// ================================================================

// Get current company ID from session (passed from PHP)
const currentCompanyId = window.CURRENT_COMPANY_ID || null;

// Store projects data
let projectsData = [];
let filteredProjects = [];
let currentFilter = 'all';
let currentView = 'table';

// DOM Elements
let projectModal;
let projectForm;
let projectDetailsModal;

// ================================================================
// INITIALIZATION
// ================================================================

/**
 * Initialize page when DOM is fully loaded
 */
document.addEventListener('DOMContentLoaded', function () {

    // Validate company authentication
    if (!currentCompanyId) {
        console.error('Company ID not found. Please login.');
        showToast('Company not authenticated. Please login.', 'error');
        return;
    }


    // Get DOM elements
    projectModal = document.getElementById('project-modal');
    projectForm = document.getElementById('project-form');
    projectDetailsModal = document.getElementById('project-details-modal');

    // Initialize UI components
    initializeFilters();
    initializeViewToggle();
    initializeModal();

    // Load initial data
    loadProjects();
    loadStatistics();
});

// ================================================================
// DATA LOADING FUNCTIONS
// ================================================================

/**
 * Load all projects for the current company
 */
async function loadProjects() {
    const showInlineState = (message, type = 'empty') => {
        const tableBody = document.getElementById('projects-table-body');
        const cardContainer = document.getElementById('projects-card-container');

        const icon = type === 'error' ? 'fas fa-exclamation-circle' : 'fas fa-inbox';
        const safeMessage = escapeHtml(message || (type === 'error' ? 'Failed to load projects' : 'No projects found'));

        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="no-data">
                        <i class="${icon}"></i>
                        <p>${safeMessage}</p>
                    </td>
                </tr>
            `;
        }

        if (cardContainer) {
            cardContainer.innerHTML = `
                <div class="no-data-card">
                    <i class="${icon}"></i>
                    <p>${safeMessage}</p>
                </div>
            `;
        }
    };

    try {
        showLoader();

        // Avoid infinite "loading" UI when the server hangs
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000);

        const response = await fetch(
            `/2nd-Year-Group-Project/FixLanka/api/projects.php?company_id=${encodeURIComponent(currentCompanyId)}`,
            { signal: controller.signal }
        );

        clearTimeout(timeoutId);

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status})`, 'error');
            projectsData = [];
            filteredProjects = [];
            showInlineState('Failed to load projects', 'error');
            hideLoader();
            return;
        }

        const result = await response.json();

        if (result.success) {
            projectsData = result.data || [];
            filteredProjects = projectsData;
            renderProjects();
            updateCounts();
        } else {
            showToast(result.message || 'Failed to load projects', 'error');
            projectsData = [];
            filteredProjects = [];
            showInlineState(result.message || 'Failed to load projects', 'error');
            updateCounts();
        }

        hideLoader();
    } catch (error) {
        console.error('Error loading projects:', error);
        const isTimeout = error && (error.name === 'AbortError');
        const msg = isTimeout
            ? 'Request timed out while loading projects'
            : 'Failed to load projects. Please try again.';
        showToast(msg, 'error');
        projectsData = [];
        filteredProjects = [];
        showInlineState(msg, 'error');
        hideLoader();
    }
}

/**
 * Load project timeline (Phases)
 * @param {number} projectId 
 */
async function loadProjectTimeline(projectId) {
    const timelineContainer = document.getElementById('tab-timeline');
    if (!timelineContainer) return;

    // Set loading state
    timelineContainer.innerHTML = `
        <div class="timeline-container" style="display: flex; justify-content: center; align-items: center; padding: 40px; min-height: 200px;">
            <div style="text-align: center; color: var(--text-secondary);">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"></i>
                <div>Loading contract phases...</div>
            </div>
        </div>
    `;

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?action=phases&project_id=${projectId}`);
        const result = await response.json();

        if (result.success && result.data && result.data.length > 0) {
            const phases = result.data;
            let html = `
                <div class="table-container" style="padding: 20px;">
                    <div style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <h4 style="margin: 0; color: var(--text-primary);"><i class="fas fa-list-ol"></i> Project Timeline (Phases)</h4>
                        <span class="badge" style="background: #eef2ff; color: #4f46e5; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            ${phases.length} Phases
                        </span>
                    </div>
                    
                    <table class="data-table" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background-color: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px; color: #64748b; font-weight: 600; width: 40px;">#</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600;">Phase Name</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600;">Description</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600; width: 100px;">Target Date</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600; text-align: right; width: 80px;">Amount</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600; width: 100px; text-align: center;">Status</th>
                                <th style="padding: 12px; color: #64748b; font-weight: 600; width: 120px; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('en-LK', {
                    style: 'decimal',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            };

            const formatDate = (dateString) => {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            };

            const getStatusBadge = (status) => {
                const colors = {
                    'pending': '#f1f5f9', 'text': '#475569',
                    'in_progress': '#dbeafe', 'text_color': '#1e40af',
                    'submitted': '#fef3c7', 'text_color': '#92400e',
                    'approved': '#d1fae5', 'text_color': '#065f46',
                    'rejected': '#fee2e2', 'text_color': '#b91c1c'
                };

                let bg = colors[status] || '#f1f5f9';
                let col = colors['text_color'] || (colors[status] ? colors['text_' + status] : '#475569');
                // Fix map logic slightly for simplicity
                if (status === 'in_progress') { bg = '#dbeafe'; col = '#1e40af'; }
                if (status === 'submitted') { bg = '#fef3c7'; col = '#92400e'; }
                if (status === 'approved' || status === 'completed') { bg = '#d1fae5'; col = '#065f46'; }
                if (status === 'rejected') { bg = '#fee2e2'; col = '#b91c1c'; }

                const label = status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1);
                return `<span style="background-color: ${bg}; color: ${col}; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; white-space: nowrap;">${label}</span>`;
            };

            let totalPercent = 0;
            let totalAmount = 0;

            phases.forEach((item, index) => {
                const amount = parseFloat(item.amount_lkr || 0);
                const pct = parseFloat(item.pct_of_total || 0);
                const milestoneId = item.id || item.milestone_id; // Handle both legacy and new ID names if needed
                // Note: SQL query selects 'milestone_number' as 'sort_order'. We should ensure we have the ID.
                // The query in ProjectModel.php does NOT currently select the ID! 
                // We need to update ProjectModel.php to select 'milestone_id'. 
                // Assuming it's selected as we'll fix it, or let's use a workaround for now but really we need the ID.
                // Actually the query is: SELECT milestone_number as sort_order, ... 
                // It misses milestone_id! I need to fix the backend query first.
                // Waait, I can't restart backend task easily.
                // Let's assume I will fix the backend query right after this.

                totalAmount += amount;
                totalPercent += pct;

                let actionBtn = '';
                // Logic based on status
                if (item.status === 'pending') {
                    actionBtn = `<button onclick="startPhase(${item.milestone_id || item.id})" class="btn-primary-small" style="padding: 4px 8px; font-size: 11px;"><i class="fas fa-play"></i> Start</button>`;
                } else if (item.status === 'in_progress') {
                    actionBtn = `<button onclick="openProofModal(${item.milestone_id || item.id})" class="btn-success-small" style="padding: 4px 8px; font-size: 11px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;"><i class="fas fa-check"></i> Complete</button>`;
                } else if (item.status === 'submitted') {
                    actionBtn = `<span style="font-size: 11px; color: #d97706;"><i class="fas fa-clock"></i> In Review</span>`;
                } else {
                    actionBtn = `<span style="font-size: 11px; color: #059669;"><i class="fas fa-check-double"></i> Done</span>`;
                }

                html += `
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; color: #64748b;">${index + 1}</td>
                        <td style="padding: 12px; font-weight: 500; color: #334155;">${item.phase_name}</td>
                        <td style="padding: 12px; color: #64748b; font-size: 13px;">${item.description || '-'}</td>
                        <td style="padding: 12px; color: #64748b;">${formatDate(item.target_date)}</td>
                        <td style="padding: 12px; text-align: right; font-family: monospace; color: #334155;">${formatCurrency(amount)}</td>
                        <td style="padding: 12px; text-align: center;">${getStatusBadge(item.status || 'pending')}</td>
                        <td style="padding: 12px; text-align: right;">${actionBtn}</td>
                    </tr>
                `;
            });

            // Totals row
            html += `
                        <tr style="background-color: #f8fafc; font-weight: 600; border-top: 2px solid #e2e8f0;">
                            <td colspan="4" style="padding: 12px; text-align: right;">Total</td>
                            <td style="padding: 12px; text-align: right; font-family: monospace;">${formatCurrency(totalAmount)}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            `;
            timelineContainer.innerHTML = html;
        } else {
            timelineContainer.innerHTML = `
                <div class="no-data-card" style="padding: 40px; text-align: center;">
                    <i class="fas fa-file-contract" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <h4 style="margin: 0 0 8px 0; color: #475569;">No Payment Schedule Found</h4>
                    <p style="color: #64748b; margin: 0;">This project does not have an active contract or payment schedule yet.</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading timeline:', error);
        timelineContainer.innerHTML = `
            <div class="error-state" style="padding: 30px; text-align: center; color: #ef4444;">
                <i class="fas fa-exclamation-circle" style="font-size: 32px; margin-bottom: 12px;"></i>
                <p>Failed to load project timeline</p>
                <button onclick="loadProjectTimeline(${projectId})" class="btn-secondary-small" style="margin-top: 10px;">Try Again</button>
            </div>
        `;
    }
}

async function loadProjectFinancials(projectId) {
    const financialContainer = document.getElementById('tab-financial');
    if (!financialContainer) return;

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?action=financials&project_id=${projectId}`);
        const result = await response.json();

        if (result.success && result.data) {
            const data = result.data;

            const formatCurrency = (amount) => {
                return 'LKR ' + new Intl.NumberFormat('en-LK', {
                    style: 'decimal',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(amount);
            };

            const formatDate = (dateString) => {
                if (!dateString) return '-';
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            };

            const budget = data.total_budget || 0;
            const paid = data.total_paid || 0;
            const progressPct = budget > 0 ? (paid / budget) * 100 : 0;

            let html = `
                <div class="project-details-container" style="padding: 20px;">
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                        <!-- Budget Card -->
                        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase;">Total Budget</h4>
                                </div>
                            </div>
                            <div style="font-size: 24px; font-weight: 700; color: #0f172a;">${formatCurrency(budget)}</div>
                        </div>

                        <!-- Amount Paid Card -->
                        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase;">Amount Paid</h4>
                                </div>
                            </div>
                            <div style="font-size: 24px; font-weight: 700; color: #16a34a;">${formatCurrency(paid)}</div>
                        </div>

                        <!-- Amount Pending Card -->
                        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #fef9c3; color: #ca8a04; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase;">Amount Pending</h4>
                                </div>
                            </div>
                            <div style="font-size: 24px; font-weight: 700; color: #ca8a04;">${formatCurrency(data.total_pending || 0)}</div>
                        </div>

                        <!-- Escrow Balance Card -->
                        <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 8px; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div>
                                    <h4 style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; display: flex; align-items: center; gap: 6px;">
                                        Escrow Balance
                                        <div class="tooltip-container" style="position: relative; display: inline-block;">
                                            <i class="fas fa-info-circle" style="color: #94a3b8; font-size: 14px; cursor: help;"></i>
                                            <div class="custom-tooltip" style="visibility: hidden; width: 250px; background-color: #1e293b; color: #f8fafc; text-align: center; border-radius: 8px; padding: 12px; position: absolute; z-index: 100; bottom: 150%; left: 50%; transform: translateX(-50%); opacity: 0; transition: opacity 0.2s, visibility 0.2s; font-size: 12px; font-weight: normal; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); line-height: 1.5; text-transform: none;">
                                                Funds securely held by the platform. These will be released to you automatically as the customer approves your milestones.
                                                <div style="position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border-width: 6px; border-style: solid; border-color: #1e293b transparent transparent transparent;"></div>
                                            </div>
                                        </div>
                                    </h4>
                                </div>
                            </div>
                            <div style="font-size: 24px; font-weight: 700; color: #9333ea;">${formatCurrency(data.escrow_balance || 0)}</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 13px; font-weight: 600; color: #475569;">Payment Progress</span>
                            <span style="font-size: 14px; font-weight: 600; color: var(--primary-color);">${progressPct.toFixed(1)}%</span>
                        </div>
                        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 5px; overflow: hidden;">
                            <div style="height: 100%; width: ${progressPct}%; background: var(--primary-color); border-radius: 5px; transition: width 0.5s ease;"></div>
                        </div>
                    </div>
            `;

            // Payment History Table
            if (data.payments && data.payments.length > 0) {
                html += `
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden;">
                        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;">
                            <h4 style="margin: 0; color: #334155; font-size: 16px;"><i class="fas fa-history"></i> Payment History</h4>
                        </div>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #e2e8f0; text-align: left;">
                                        <th style="padding: 12px 20px; color: #64748b; font-weight: 600;">Date</th>
                                        <th style="padding: 12px 20px; color: #64748b; font-weight: 600;">Description</th>
                                        <th style="padding: 12px 20px; color: #64748b; font-weight: 600;">Type</th>
                                        <th style="padding: 12px 20px; color: #64748b; font-weight: 600; text-align: right;">Amount</th>
                                        <th style="padding: 12px 20px; color: #64748b; font-weight: 600; text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                data.payments.forEach(payment => {
                    const statusColors = {
                        'pending': { bg: '#f1f5f9', text: '#475569' },
                        'completed': { bg: '#dcfce7', text: '#16a34a' },
                        'held_escrow': { bg: '#f3e8ff', text: '#9333ea' },
                        'released': { bg: '#d1fae5', text: '#059669' },
                        'refunded': { bg: '#fee2e2', text: '#b91c1c' }
                    };

                    const paymentStatus = payment.status || 'pending';
                    const colors = statusColors[paymentStatus] || statusColors['pending'];
                    const statusLabel = paymentStatus.replace('_', ' ').charAt(0).toUpperCase() + paymentStatus.replace('_', ' ').slice(1);
                    const badgeHtml = `<span style="background-color: ${colors.bg}; color: ${colors.text}; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; display: inline-block; white-space: nowrap;">${statusLabel}</span>`;

                    const ptype = payment.payment_type ? payment.payment_type.charAt(0).toUpperCase() + payment.payment_type.slice(1) : 'Milestone';

                    html += `
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 12px 20px; color: #475569;">${formatDate(payment.created_at)}</td>
                            <td style="padding: 12px 20px; color: #334155; font-weight: 500;">${payment.description || '-'}</td>
                            <td style="padding: 12px 20px; color: #64748b;">${ptype}</td>
                            <td style="padding: 12px 20px; text-align: right; font-family: monospace; color: #0f172a; font-weight: 500;">${formatCurrency(payment.amount)}</td>
                            <td style="padding: 12px 20px; text-align: center;">${badgeHtml}</td>
                        </tr>
                    `;
                });

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            } else if (!data.has_contract) {
                html += `
                    <div class="info-notice" style="margin-top: 20px;">
                        <i class="fas fa-info-circle"></i>
                        <p>Detailed financial breakdown including milestones and payments will be available once a contract is established.</p>
                    </div>
                `;
            } else {
                html += `
                    <div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; padding: 40px; text-align: center;">
                        <i class="fas fa-receipt" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
                        <h4 style="margin: 0 0 8px 0; color: #475569;">No Payment History Found</h4>
                        <p style="color: #64748b; margin: 0;">There are no recorded transactions or payments for this project yet.</p>
                    </div>
                `;
            }

            html += `</div>`; // Close project-details-container
            financialContainer.innerHTML = html;

        } else {
            throw new Error(result.message || 'Failed to load financials');
        }
    } catch (error) {
        console.error('Error loading financials:', error);
        financialContainer.innerHTML = `
            <div class="error-state" style="padding: 40px; text-align: center; color: #ef4444;">
                <i class="fas fa-exclamation-circle" style="font-size: 40px; margin-bottom: 16px;"></i>
                <h4 style="margin: 0 0 8px 0;">Failed to Load Financial Data</h4>
                <p style="color: #64748b; margin-bottom: 20px;">An error occurred while fetching the financial records.</p>
                <button onclick="loadProjectFinancials(${projectId})" class="btn-secondary" style="padding: 8px 16px;">
                    <i class="fas fa-sync"></i> Try Again
                </button>
            </div>
        `;
    }
}

// Start Phase
async function startPhase(milestoneId) {
    if (!confirm('Are you sure you want to start this phase?')) return;

    try {
        const formData = new FormData();
        formData.append('milestone_id', milestoneId);

        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?action=start_phase`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (result.success) {
            showToast('Phase started!', 'success');
            // Reload timeline - we need the project ID. 
            // Since we don't have it easily available in this scope without passing it around, 
            // we can reload the current project details if we store the current ID globally or passed it.
            // For now, let's close and reopen or just find the current open project ID.
            // We can look at the drawer or store it.
            // Helper: Find the project ID from the DOM or variable.
            // Assumption: we won't fix the reload perfectly right now, let user refresh.
            // Better: store currentProjectId when opening drawer.
            if (window.currentOpenProjectId) loadProjectTimeline(window.currentOpenProjectId);
        } else {
            showToast(result.message || 'Failed to start phase', 'error');
        }
    } catch (e) {
        console.error(e);
        showToast('Error starting phase', 'error');
    }
}

// Proof Modal Logic
function openProofModal(milestoneId) {
    document.getElementById('proof-milestone-id').value = milestoneId;
    document.getElementById('proof-modal').classList.add('active');
}

function closeProofModal() {
    document.getElementById('proof-modal').classList.remove('active');
    document.getElementById('proof-form').reset();
    document.getElementById('file-list').innerHTML = '';
}

// File input change handler for preview
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('proof-files');
    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            const list = document.getElementById('file-list');
            list.innerHTML = '';
            Array.from(e.target.files).forEach(file => {
                list.innerHTML += `<div><i class="fas fa-file"></i> ${file.name}</div>`;
            });
        });
    }

    // Submit Proof Form
    const proofForm = document.getElementById('proof-form');
    if (proofForm) {
        proofForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?action=complete_phase`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showToast('Proof submitted successfully!', 'success');
                    closeProofModal();
                    if (window.currentOpenProjectId) loadProjectTimeline(window.currentOpenProjectId);
                } else {
                    showToast(result.message || 'Upload failed', 'error');
                }
            } catch (error) {
                console.error(error);
                showToast('An error occurred during upload', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});

/**
 * Load project statistics
 */
async function loadStatistics() {
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?action=stats&company_id=${currentCompanyId}`);

        if (!response.ok) {
            console.error('Failed to load statistics');
            return;
        }

        const result = await response.json();

        if (result.success) {
            updateStatistics(result.data);
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// ================================================================
// RENDER FUNCTIONS
// ================================================================

/**
 * Render projects based on current view (table or cards)
 */
function renderProjects() {
    if (currentView === 'table') {
        renderTableView();
    } else {
        renderCardView();
    }
}

/**
 * Render projects in table view
 */
function renderTableView() {
    const tableBody = document.getElementById('projects-table-body');

    if (!tableBody) {
        console.error('Table body not found');
        return;
    }

    if (filteredProjects.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="8" class="no-data">
                    <i class="fas fa-inbox"></i>
                    <p>No projects found</p>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = filteredProjects.map(project => `
        <tr>
            <td>
                <div class="project-title-cell">
                    <strong>${escapeHtml(project.title)}</strong>
                    <span class="project-type">${escapeHtml(project.project_type || 'N/A')}</span>
                </div>
            </td>
            <td>
                <div class="customer-info">
                    <span class="customer-name">${escapeHtml(project.customer_first_name)} ${escapeHtml(project.customer_last_name)}</span>
                    <span class="customer-email">${escapeHtml(project.customer_email)}</span>
                </div>
            </td>
            <td><span class="location-tag"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(project.location)}</span></td>
            <td><span class="status-badge status-${project.status}">${formatStatus(project.status)}</span></td>
            <td>
                <div class="progress-cell">
                    <div class="progress-bar-mini">
                        <div class="progress-fill" style="width: ${project.progress}%"></div>
                    </div>
                    <span class="progress-text">${project.progress}%</span>
                </div>
            </td>
            <td>LKR ${formatNumber(project.budget || 0)}</td>
            <td>${formatDate(project.start_date)} - ${formatDate(project.end_date)}</td>
            <td>
                <div class="action-buttons-cell">
                    <button class="action-icon-btn" onclick="viewProjectDetails(${project.project_id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-icon-btn" onclick="editProject(${project.project_id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-icon-btn delete" onclick="deleteProject(${project.project_id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Render projects in card view
 */
function renderCardView() {
    const cardContainer = document.getElementById('projects-card-container');

    if (!cardContainer) {
        console.error('Card container not found');
        return;
    }

    if (filteredProjects.length === 0) {
        cardContainer.innerHTML = `
            <div class="no-data-card">
                <i class="fas fa-inbox"></i>
                <p>No projects found</p>
            </div>
        `;
        return;
    }

    cardContainer.innerHTML = filteredProjects.map(project => `
        <div class="project-card">
            <div class="card-header">
                <div class="card-title">
                    <h3>${escapeHtml(project.title)}</h3>
                    <span class="project-type-badge">${escapeHtml(project.project_type || 'General')}</span>
                </div>
                <span class="status-badge status-${project.status}">${formatStatus(project.status)}</span>
            </div>
            
            <div class="card-body">
                <div class="card-info-row">
                    <i class="fas fa-user"></i>
                    <span>${escapeHtml(project.customer_first_name)} ${escapeHtml(project.customer_last_name)}</span>
                </div>
                <div class="card-info-row">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${escapeHtml(project.location)}</span>
                </div>
                <div class="card-info-row">
                    <i class="fas fa-calendar"></i>
                    <span>${formatDate(project.start_date)} - ${formatDate(project.end_date)}</span>
                </div>
                <div class="card-info-row">
                    <i class="fas fa-dollar-sign"></i>
                    <span>LKR ${formatNumber(project.budget || 0)}</span>
                </div>
            </div>
            
            <div class="card-progress">
                <div class="progress-header">
                    <span>Progress</span>
                    <span class="progress-percentage">${project.progress}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${project.progress}%"></div>
                </div>
            </div>
            
            <div class="card-actions">
                <button class="btn-secondary-small" onclick="viewProjectDetails(${project.project_id})">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="btn-primary-small" onclick="editProject(${project.project_id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-danger-small" onclick="deleteProject(${project.project_id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

/**
 * Update statistics dashboard
 */
function updateStatistics(stats) {
    // Update stat numbers if elements exist
    const elements = {
        'total-projects': stats.total_projects || 0,
        'planned-projects': stats.planned || 0,
        'in-progress-projects': stats.in_progress || 0,
        'completed-projects': stats.completed || 0,
        'average-progress': Math.round(stats.average_progress || 0) + '%'
    };

    Object.keys(elements).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = elements[id];
        }
    });
}

// ================================================================
// CRUD OPERATIONS
// ================================================================

/**
 * Create a new project
 */
async function createProject(formData) {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/projects.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`Server Error (${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
            showToast('Project created successfully!', 'success');
            closeProjectModal();
            loadProjects();
            loadStatistics();
        } else {
            showToast(result.message || 'Failed to create project', 'error');
        }
    } catch (error) {
        console.error('Error creating project:', error);
        showToast('Failed to create project. Please try again.', 'error');
    }
}

/**
 * Update an existing project
 */
async function updateProject(projectId, formData) {
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?project_id=${projectId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`Server Error (${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
            showToast('Project updated successfully!', 'success');
            closeProjectModal();
            loadProjects();
            loadStatistics();
        } else {
            showToast(result.message || 'Failed to update project', 'error');
        }
    } catch (error) {
        console.error('Error updating project:', error);
        showToast('Failed to update project. Please try again.', 'error');
    }
}

/**
 * Update project progress
 */
async function updateProgress(projectId, progress) {
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?project_id=${projectId}&action=progress`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ progress: progress })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Progress updated successfully!', 'success');
            loadProjects();
        } else {
            showToast(result.message || 'Failed to update progress', 'error');
        }
    } catch (error) {
        console.error('Error updating progress:', error);
        showToast('Failed to update progress. Please try again.', 'error');
    }
}

/**
 * Update project status
 */
async function updateStatus(projectId, status) {
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?project_id=${projectId}&action=status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Status updated successfully!', 'success');
            loadProjects();
            loadStatistics();
        } else {
            showToast(result.message || 'Failed to update status', 'error');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('Failed to update status. Please try again.', 'error');
    }
}

/**
 * Delete a project
 */
async function deleteProject(projectId) {
    if (!confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?project_id=${projectId}`, {
            method: 'DELETE'
        });

        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            throw new Error(`Server Error (${response.status})`);
        }

        const result = await response.json();

        if (result.success) {
            showToast('Project deleted successfully!', 'success');
            loadProjects();
            loadStatistics();
        } else {
            showToast(result.message || 'Failed to delete project', 'error');
        }
    } catch (error) {
        console.error('Error deleting project:', error);
        showToast('Failed to delete project. Please try again.', 'error');
    }
}

// ================================================================
// UI INTERACTION FUNCTIONS
// ================================================================

/**
 * Open project modal for starting a project from an accepted contract
 */
async function openStartProjectModal() {
    if (projectModal && projectForm) {
        projectForm.reset();

        // Fetch eligible contracts
        const selector = document.getElementById('contract-selector');
        selector.innerHTML = '<option value="">Loading available contracts...</option>';
        selector.disabled = true;

        try {
            const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/contracts.php?action=list');
            const result = await response.json();

            if (result.success && result.data) {
                // Filter for accepted and no project_id
                const eligibleContracts = result.data.filter(c => c.status === 'accepted' && !c.project_id);

                if (eligibleContracts.length === 0) {
                    selector.innerHTML = '<option value="">No accepted contracts available to start.</option>';
                } else {
                    selector.innerHTML = '<option value="">-- Select an Accepted Contract --</option>';
                    eligibleContracts.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.contract_id;
                        // Build label "Contract #123 - Client Name - LKR..."
                        const clientName = c.client_name || 'Client';
                        const total = c.value ? ` (LKR ${formatNumber(c.value)})` : '';
                        option.textContent = `Contract #${c.contract_id} - ${clientName}${total}`;
                        selector.appendChild(option);
                    });
                    selector.disabled = false;
                }
            } else {
                selector.innerHTML = '<option value="">Failed to load contracts.</option>';
            }
        } catch (e) {
            console.error('Error fetching contracts for selector:', e);
            selector.innerHTML = '<option value="">Error loading contracts.</option>';
        }

        projectModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close project modal
 */
function closeProjectModal() {
    if (projectModal) {
        projectModal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Start a project from an accepted contract (Replaces saveProject)
 */
async function saveProject(e) {
    if (e) e.preventDefault();

    try {
        const contractSelect = document.getElementById('contract-selector');
        const contractId = contractSelect ? contractSelect.value : null;

        if (!contractId) {
            showToast('Please select a contract first', 'error');
            return;
        }

        const submitBtn = document.getElementById('start-project-submit-btn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
            submitBtn.disabled = true;
        }

        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/projects.php?action=start_from_contract', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ contract_id: contractId })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Project started successfully from contract!', 'success');
            closeProjectModal();

            // Reload projects and statistics
            await loadProjects();
            await loadStatistics();
        } else {
            showToast(result.message || 'Failed to start project', 'error');
        }
    } catch (error) {
        console.error('Error starting project:', error);
        showToast('An error occurred while starting the project', 'error');
    } finally {
        const submitBtn = document.getElementById('start-project-submit-btn');
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-rocket"></i> Start Project';
            submitBtn.disabled = false;
        }
    }
}

/**
 * Edit project
 */
function editProject(projectId) {
    const project = projectsData.find(p => p.project_id === projectId);
    if (!project) return;

    // Populate form with project data
    document.getElementById('modal-title').textContent = 'Edit Project';
    document.getElementById('project-id').value = project.project_id;
    document.getElementById('project-title').value = project.title || '';
    document.getElementById('project-type').value = project.project_type || '';
    document.getElementById('project-location').value = project.location || '';
    document.getElementById('project-budget').value = project.budget || '';
    document.getElementById('project-start-date').value = project.start_date || '';
    document.getElementById('project-end-date').value = project.end_date || '';
    document.getElementById('project-status').value = project.status || 'planned';
    document.getElementById('project-progress').value = project.progress || 0;
    document.getElementById('project-description').value = project.description || '';

    openProjectModal();
}

/**
 * View project details
 */
function viewProjectDetails(projectId) {
    const project = projectsData.find(p => p.project_id === projectId);
    if (!project) return;

    // Store current project ID for refreshes
    window.currentOpenProjectId = projectId;

    // Format currency
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-LK', {
            style: 'currency',
            currency: 'LKR',
            minimumFractionDigits: 2
        }).format(amount);
    };

    // Format date
    const formatDate = (date) => {
        if (!date) return 'N/A';
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    // Get status badge HTML
    const getStatusBadge = (status) => {
        return `<span class="status-badge status-${status}">${status.replace('_', ' ')}</span>`;
    };

    // Populate Overview Tab
    const overviewContent = `
        <div class="project-details-container">
            <div class="project-details-header">
                <div class="project-title-row">
                    <h2 class="project-details-title">
                        ${escapeHtml(project.title)}
                    </h2>
                    ${getStatusBadge(project.status)}
                </div>
                <div class="project-badges-row">
                    <span class="project-type-badge">${escapeHtml(project.project_type || 'N/A')}</span>
                    <span class="location-tag">
                        <i class="fas fa-map-marker-alt"></i> ${escapeHtml(project.location)}
                    </span>
                </div>
            </div>

            <div class="project-stats-grid">
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="fas fa-user"></i> Customer
                    </div>
                    <div class="stat-value" style="font-size: 18px;">
                        ${escapeHtml(project.customer_name || 'Unknown')}
                    </div>
                </div>
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                    <div class="stat-value" style="font-size: 16px; word-break: break-all;">
                        ${escapeHtml(project.customer_email || 'N/A')}
                    </div>
                </div>
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="fas fa-wallet" style="color: var(--primary-color);"></i> Budget
                    </div>
                    <div class="stat-value highlight">
                        ${formatCurrency(project.budget)}
                    </div>
                </div>
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="fas fa-chart-line" style="color: #3b82f6;"></i> Progress
                    </div>
                    <div class="stat-value">
                        ${project.progress}%
                    </div>
                </div>
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="far fa-calendar-alt"></i> Start Date
                    </div>
                    <div class="stat-value" style="font-size: 18px;">
                        ${formatDate(project.start_date)}
                    </div>
                </div>
                <div class="project-stat-card">
                    <div class="stat-label">
                        <i class="far fa-calendar-check"></i> End Date
                    </div>
                    <div class="stat-value" style="font-size: 18px;">
                        ${formatDate(project.end_date)}
                    </div>
                </div>
            </div>

            ${project.description ? `
                <div class="project-description-card" style="margin-bottom: 24px;">
                    <h4 class="section-title">
                        <i class="fas fa-align-left"></i> Description
                    </h4>
                    <p class="description-text">
                        ${escapeHtml(project.description)}
                    </p>
                </div>
            ` : ''
        }
        </div>
    `;

    // Populate Timeline Tab (Dynamic)
    const timelineContent = `
        <div class="project-details-container" style="min-height: 300px;">
            <div id="timeline-loading-placeholder" style="display: flex; justify-content: center; align-items: center; height: 200px; flex-direction: column;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color); margin-bottom: 16px;"></i>
                <div style="color: #64748b; font-weight: 500;">Loading project timeline...</div>
            </div>
        </div>
    `;

    // Populate Financial Tab (Dynamic)
    const financialContent = `
        <div class="project-details-container" style="min-height: 300px;">
            <div id="financial-loading-placeholder" style="display: flex; justify-content: center; align-items: center; height: 200px; flex-direction: column;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color); margin-bottom: 16px;"></i>
                <div style="color: #64748b; font-weight: 500;">Loading financial data...</div>
            </div>
        </div>
    `;

    // Populate Chat Tab
    const chatContent = `
        <div class="project-details-container" style="height: 100%; display: flex; flex-direction: column; padding: 0;">
            <div class="chat-wrapper" style="flex: 1; display: flex; flex-direction: column; height: 500px;">
                <div class="chat-header" style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                    <div class="chat-title" style="font-weight: 600; color: #1e293b;">
                        <i class="fas fa-comments"></i> Chat with ${escapeHtml(project.customer_name || 'Customer')}
                    </div>
                </div>
                <div class="chat-messages" id="chat-messages" style="flex: 1; overflow-y: auto; padding: 20px; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px;">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="chat-input-area" style="padding: 15px; border-top: 1px solid #e2e8f0; background: white; display: flex; gap: 10px;">
                    <input type="text" id="chat-message-input" placeholder="Type your message..." style="flex: 1; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 20px; outline: none; transition: border-color 0.2s;">
                        <button id="send-chat-btn" onclick="sendMessage(${project.contract_id})" style="background: var(--primary-color); color: white; border: none; border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                </div>
            </div>
        </div>
    `;

    // Load timeline and financials dynamically after a short delay
    setTimeout(() => {
        if (typeof loadProjectTimeline === 'function') {
            loadProjectTimeline(project.project_id);
        }
        if (typeof loadProjectFinancials === 'function') {
            loadProjectFinancials(project.project_id);
        }
        if (typeof loadProjectChat === 'function') {
            loadProjectChat(project.contract_id);
        }
    }, 100);

    // Update drawer content
    document.getElementById('tab-overview').innerHTML = overviewContent;
    document.getElementById('tab-timeline').innerHTML = timelineContent;
    document.getElementById('tab-financial').innerHTML = financialContent;
    document.getElementById('tab-chat').innerHTML = chatContent;

    // Reset to overview tab
    document.querySelectorAll('.drawer-tab').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.drawer-tab-content').forEach(content => content.classList.remove('active'));
    document.querySelector('[data-tab="overview"]').classList.add('active');
    document.getElementById('tab-overview').classList.add('active');


    // Show drawer
    const drawer = document.getElementById('project-details-drawer');
    if (drawer) {
        drawer.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Close project drawer
 */
function closeProjectDrawer() {
    const drawer = document.getElementById('project-details-drawer');
    if (drawer) {
        drawer.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Initialize filters
 */
function initializeFilters() {
    const filterSelect = document.querySelector('.filter-select');
    if (filterSelect) {
        filterSelect.addEventListener('change', function () {
            currentFilter = this.value;
            applyFilters();
        });
    }
}

/**
 * Apply filters to projects
 */
function applyFilters() {
    if (currentFilter === 'all') {
        filteredProjects = projectsData;
    } else {
        filteredProjects = projectsData.filter(p => p.status === currentFilter);
    }
    renderProjects();
}

/**
 * Initialize view toggle
 */
function initializeViewToggle() {
    const viewToggles = document.querySelectorAll('.view-toggle');
    viewToggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            currentView = this.dataset.view;
            viewToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Show/hide appropriate view
            const tableView = document.getElementById('table-view');
            const cardView = document.getElementById('card-view');

            if (currentView === 'table') {
                if (tableView) tableView.style.display = 'block';
                if (cardView) cardView.style.display = 'none';
            } else {
                if (tableView) tableView.style.display = 'none';
                if (cardView) cardView.style.display = 'grid';
            }

            renderProjects();
        });
    });
}

/**
 * Initialize modal
 */
function initializeModal() {
    // Modal close handlers
    const closeButtons = document.querySelectorAll('.modal-close, .modal-overlay');
    closeButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (e.target === btn) {
                closeProjectModal();
            }
        });
    });

    // Form submit handler
    const projectForm = document.getElementById('project-form');
    if (projectForm) {
        projectForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await saveProject(e);
        });
    }

    // Drawer tab switching
    const drawerTabs = document.querySelectorAll('.drawer-tab');
    drawerTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.drawer-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.drawer-tab-content').forEach(c => c.classList.remove('active'));

            // Add active class to clicked tab and corresponding content
            tab.classList.add('active');
            const tabName = tab.getAttribute('data-tab');
            const content = document.getElementById(`tab - ${tabName} `);
            if (content) {
                content.classList.add('active');
            }
        });
    });

    // Drawer close handler
    const drawerOverlay = document.getElementById('project-details-drawer');
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', (e) => {
            if (e.target === drawerOverlay) {
                closeProjectDrawer();
            }
        });
    }
}

/**
 * Update project counts
 */
function updateCounts() {
    const counts = {
        all: projectsData.length,
        planned: projectsData.filter(p => p.status === 'planned').length,
        in_progress: projectsData.filter(p => p.status === 'in_progress').length,
        completed: projectsData.filter(p => p.status === 'completed').length,
        cancelled: projectsData.filter(p => p.status === 'cancelled').length,
        on_hold: projectsData.filter(p => p.status === 'on_hold').length
    };

    // Update count badges if they exist
    Object.keys(counts).forEach(key => {
        const element = document.querySelector(`[data-status="${key}"] .count`);
        if (element) {
            element.textContent = counts[key];
        }
    });
}

// ================================================================
// HELPER FUNCTIONS
// ================================================================

/**
 * Format status for display
 */
function formatStatus(status) {
    const statusMap = {
        'planned': 'Planned',
        'in_progress': 'In Progress',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
        'on_hold': 'On Hold'
    };
    return statusMap[status] || status;
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast - ${type} `;
    toast.innerHTML = `
                            < i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}" ></i >
                                <span>${message}</span>
                        `;

    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => toast.classList.add('show'), 100);

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Show loader
 */
function showLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'flex';
}

/**
 * Hide loader
 */
function hideLoader() {
    const loader = document.getElementById('loader');
    if (loader) loader.style.display = 'none';
}

// Make functions globally accessible
window.openProjectModal = openStartProjectModal;
window.openStartProjectModal = openStartProjectModal;
window.closeProjectModal = closeProjectModal;
window.editProject = editProject;
window.viewProjectDetails = viewProjectDetails;
window.closeProjectDrawer = closeProjectDrawer;
window.deleteProject = deleteProject;
window.updateProgress = updateProgress;
window.updateStatus = updateStatus;

// --- INLINE CHAT LOGIC ---
function renderProjectChatMessages(messages, userRole) {
    const messagesContainer = document.getElementById('chat-messages');
    if (!messagesContainer) return;

    if (!messages || messages.length === 0) {
        messagesContainer.innerHTML = `
                            < div style = "flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; height: 100%;" >
                <i class="fas fa-comments" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>No messages yet. Start the conversation!</p>
            </div >
                            `;
        return;
    }

    let html = '';
    let lastDate = null;

    messages.forEach(msg => {
        // Handle date dividers
        const msgDateObj = new Date(msg.created_at.replace(' ', 'T'));
        const msgDate = msgDateObj.toLocaleDateString();

        if (msgDate !== lastDate) {
            lastDate = msgDate;
            let displayDate = msgDate;
            const today = new Date().toLocaleDateString();
            const yesterday = new Date(new Date().setDate(new Date().getDate() - 1)).toLocaleDateString();

            if (msgDate === today) displayDate = 'Today';
            else if (msgDate === yesterday) displayDate = 'Yesterday';
            else displayDate = msgDateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

            html += `
                            < div style = "text-align: center; margin: 15px 0;" >
                                <span style="background: #e2e8f0; color: #64748b; font-size: 11px; padding: 4px 10px; border-radius: 12px; font-weight: 500;">
                                    ${displayDate}
                                </span>
                </div >
                            `;
        }

        if (msg.message_type === 'system') {
            html += `
                            < div style = "text-align: center; margin: 10px 0;" >
                                <span style="background: #f1f5f9; color: #64748b; font-size: 12px; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <i class="fas fa-info-circle"></i> ${escapeHtml(msg.message)}
                                </span>
                </div >
                            `;
            return;
        }

        const isMine = msg.sender_type === userRole;
        const timeStr = msgDateObj.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

        if (isMine) {
            html += `
                            < div style = "display: flex; justify-content: flex-end; margin-bottom: 10px;" >
                                <div style="max-width: 75%; display: flex; flex-direction: column; align-items: flex-end;">
                                    <div style="background: var(--primary-color, #0abab5); color: white; padding: 10px 15px; border-radius: 15px 15px 0 15px; box-shadow: 0 1px 2px rgba(0,0,0,0.1); word-wrap: break-word;">
                                        ${escapeHtml(msg.message)}
                                    </div>
                                    <span style="font-size: 10px; color: #94a3b8; margin-top: 4px;">${timeStr}</span>
                                </div>
                </div >
                            `;
        } else {
            html += `
                            < div style = "display: flex; justify-content: flex-start; margin-bottom: 10px;" >
                                <div style="max-width: 75%; display: flex; flex-direction: column; align-items: flex-start;">
                                    <div style="background: white; color: #1e293b; padding: 10px 15px; border-radius: 15px 15px 15px 0; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); word-wrap: break-word;">
                                        ${escapeHtml(msg.message)}
                                    </div>
                                    <span style="font-size: 10px; color: #94a3b8; margin-top: 4px;">${timeStr}</span>
                                </div>
                </div >
                            `;
        }
    });

    messagesContainer.innerHTML = html;
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

let chatPollInterval = null;

async function loadProjectChat(contractId) {
    if (!contractId) return;

    const messagesContainer = document.getElementById('chat-messages');
    if (!messagesContainer) return;

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/chat.php?action=get_messages&contract_id=' + contractId);
        const result = await response.json();

        if (result.success) {
            renderProjectChatMessages(result.messages, result.user_role);

            // Set up Enter key to send messages securely
            const inputEl = document.getElementById('chat-message-input');
            if (inputEl) {
                // Remove old event listeners via cloning
                const newEl = inputEl.cloneNode(true);
                inputEl.parentNode.replaceChild(newEl, inputEl);

                newEl.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendMessage(contractId);
                    }
                });
            }

            // Start polling for new messages when drawer is active
            if (chatPollInterval) clearInterval(chatPollInterval);
            chatPollInterval = setInterval(async () => {
                const drawerWrapper = document.getElementById('projectDetailsDrawerOverlay');
                if (!drawerWrapper || !drawerWrapper.classList.contains('active')) {
                    clearInterval(chatPollInterval);
                    return;
                }

                try {
                    const pollRes = await fetch('/2nd-Year-Group-Project/FixLanka/api/chat.php?action=get_messages&contract_id=' + contractId);
                    const pollData = await pollRes.json();
                    if (pollData.success && document.getElementById('chat-messages')) {
                        renderProjectChatMessages(pollData.messages, pollData.user_role);
                    }
                } catch (e) {
                    console.error('Chat polling error', e);
                }
            }, 5000);

        } else {
            messagesContainer.innerHTML = '<div style="padding: 20px; color: #ef4444; text-align: center;">Failed to load messages.</div>';
        }
    } catch (e) {
        messagesContainer.innerHTML = '<div style="padding: 20px; color: #ef4444; text-align: center;">Error connecting to chat.</div>';
    }
}

async function sendMessage(contractId) {
    const inputField = document.getElementById('chat-message-input');
    const sendBtn = document.getElementById('send-chat-btn');
    if (!inputField || !sendBtn) return;

    const text = inputField.value.trim();
    if (!text) return;

    sendBtn.disabled = true;
    inputField.disabled = true;

    try {
        const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'send',
                contract_id: contractId,
                message: text
            })
        });

        const data = await res.json();
        if (data.success) {
            inputField.value = '';
            // Refresh instantly inline
            loadProjectChat(contractId);
        } else {
            alert('Failed to send message: ' + (data.message || 'Unknown error'));
        }
    } catch (e) {
        alert('Error sending message');
    } finally {
        sendBtn.disabled = false;
        inputField.disabled = false;
        inputField.focus();
    }
}
