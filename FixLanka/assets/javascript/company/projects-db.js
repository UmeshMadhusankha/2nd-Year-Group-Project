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
document.addEventListener('DOMContentLoaded', function() {
    
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
    try {
        showLoader();
        
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?company_id=${currentCompanyId}`);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server error:', errorText);
            showToast(`Server Error (${response.status})`, 'error');
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
        }
        
        hideLoader();
    } catch (error) {
        console.error('Error loading projects:', error);
        showToast('Failed to load projects. Please try again.', 'error');
        hideLoader();
    }
}

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
 * Open project modal for creating new project
 */
function openProjectModal() {
    if (projectModal && projectForm) {
        projectForm.reset();
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
 * Save project (create or update)
 */
async function saveProject() {
    try {
        showLoader();

        // Get form data
        const projectId = document.getElementById('project-id').value;
        const formData = {
            title: document.getElementById('project-title').value.trim(),
            project_type: document.getElementById('project-type').value,
            location: document.getElementById('project-location').value.trim(),
            budget: parseFloat(document.getElementById('project-budget').value),
            start_date: document.getElementById('project-start-date').value,
            end_date: document.getElementById('project-end-date').value,
            status: document.getElementById('project-status').value,
            progress: parseInt(document.getElementById('project-progress').value) || 0,
            description: document.getElementById('project-description').value.trim(),
            company_id: companyId // From global scope
        };

        // Validate dates
        if (new Date(formData.end_date) < new Date(formData.start_date)) {
            showToast('End date cannot be before start date', 'error');
            hideLoader();
            return;
        }

        // Determine if creating or updating
        const isUpdate = projectId && projectId !== '';
        const url = `/2nd-Year-Group-Project/FixLanka/api/projects.php${isUpdate ? '?project_id=' + projectId : ''}`;
        const method = isUpdate ? 'PUT' : 'POST';

        // Add project_id for updates
        if (isUpdate) {
            formData.project_id = parseInt(projectId);
        }

        // Make API request
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            showToast(isUpdate ? 'Project updated successfully' : 'Project created successfully', 'success');
            closeProjectModal();
            
            // Reload projects and statistics
            await loadProjects();
            await loadStatistics();
        } else {
            showToast(result.message || 'Failed to save project', 'error');
        }
    } catch (error) {
        console.error('Error saving project:', error);
        showToast('An error occurred while saving the project', 'error');
    } finally {
        hideLoader();
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
        <div style="padding: 24px;">
            <div style="background: linear-gradient(135deg, rgba(10, 186, 181, 0.1), rgba(10, 186, 181, 0.05)); 
                        border-radius: 12px; padding: 24px; margin-bottom: 24px; border-left: 4px solid var(--primary-color);">
                <h2 style="margin: 0 0 8px 0; color: var(--text-primary); font-size: 24px;">
                    ${escapeHtml(project.title)}
                </h2>
                <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 12px;">
                    <span class="project-type-badge">${escapeHtml(project.project_type || 'N/A')}</span>
                    ${getStatusBadge(project.status)}
                    <span class="location-tag">
                        <i class="fas fa-map-marker-alt"></i> ${escapeHtml(project.location)}
                    </span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Budget</div>
                    <div style="color: var(--primary-color); font-size: 20px; font-weight: 700;">
                        ${formatCurrency(project.budget)}
                    </div>
                </div>
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Progress</div>
                    <div style="color: var(--primary-color); font-size: 20px; font-weight: 700;">
                        ${project.progress}%
                    </div>
                </div>
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">Start Date</div>
                    <div style="color: var(--text-primary); font-size: 16px; font-weight: 600;">
                        ${formatDate(project.start_date)}
                    </div>
                </div>
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <div style="color: var(--text-secondary); font-size: 12px; margin-bottom: 4px;">End Date</div>
                    <div style="color: var(--text-primary); font-size: 16px; font-weight: 600;">
                        ${formatDate(project.end_date)}
                    </div>
                </div>
            </div>

            ${project.description ? `
                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color);">
                    <h4 style="margin: 0 0 12px 0; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-align-left" style="color: var(--primary-color);"></i> Description
                    </h4>
                    <p style="margin: 0; color: var(--text-secondary); line-height: 1.6;">
                        ${escapeHtml(project.description)}
                    </p>
                </div>
            ` : ''}
        </div>
    `;

    // Populate Customer Tab
    const customerContent = `
        <div style="padding: 24px;">
            <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
                                border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: 700;">
                        ${(project.customer_name || 'U').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h3 style="margin: 0 0 4px 0; color: var(--text-primary);">
                            ${escapeHtml(project.customer_name || 'Unknown Customer')}
                        </h3>
                        <p style="margin: 0; color: var(--text-secondary); display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-envelope"></i> ${escapeHtml(project.customer_email || 'N/A')}
                        </p>
                    </div>
                </div>
                
                <div style="padding: 16px; background: rgba(10, 186, 181, 0.05); border-radius: 8px; border-left: 3px solid var(--primary-color);">
                    <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">
                        <i class="fas fa-info-circle" style="color: var(--primary-color);"></i>
                        This is the customer information associated with this project. Contact details can be used for project-related communication.
                    </p>
                </div>
            </div>
        </div>
    `;

    // Populate Timeline Tab
    const timelineContent = `
        <div style="padding: 24px;">
            <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div style="position: relative; padding-left: 32px;">
                    <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 2px; background: var(--border-color);"></div>
                    
                    <div style="position: relative; margin-bottom: 24px;">
                        <div style="position: absolute; left: -37px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary-color); border: 3px solid white; box-shadow: 0 0 0 2px var(--primary-color);"></div>
                        <div style="background: rgba(10, 186, 181, 0.05); padding: 16px; border-radius: 8px;">
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Project Created</div>
                            <div style="color: var(--text-secondary); font-size: 14px;">${formatDate(project.created_at)}</div>
                        </div>
                    </div>

                    <div style="position: relative; margin-bottom: 24px;">
                        <div style="position: absolute; left: -37px; width: 12px; height: 12px; border-radius: 50%; background: var(--success-color); border: 3px solid white; box-shadow: 0 0 0 2px var(--success-color);"></div>
                        <div style="background: rgba(34, 197, 94, 0.05); padding: 16px; border-radius: 8px;">
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Start Date</div>
                            <div style="color: var(--text-secondary); font-size: 14px;">${formatDate(project.start_date)}</div>
                        </div>
                    </div>

                    <div style="position: relative;">
                        <div style="position: absolute; left: -37px; width: 12px; height: 12px; border-radius: 50%; background: var(--warning-color); border: 3px solid white; box-shadow: 0 0 0 2px var(--warning-color);"></div>
                        <div style="background: rgba(245, 158, 11, 0.05); padding: 16px; border-radius: 8px;">
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Expected End Date</div>
                            <div style="color: var(--text-secondary); font-size: 14px;">${formatDate(project.end_date)}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Populate Financial Tab
    const financialContent = `
        <div style="padding: 24px;">
            <div style="background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--border-color); margin-bottom: 16px;">
                <h4 style="margin: 0 0 16px 0; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-dollar-sign" style="color: var(--primary-color);"></i> Budget Overview
                </h4>
                <div style="font-size: 32px; font-weight: 700; color: var(--primary-color); margin-bottom: 8px;">
                    ${formatCurrency(project.budget)}
                </div>
                <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">
                    Total project budget allocated for all expenses and milestones.
                </p>
            </div>

            <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05)); 
                        padding: 16px; border-radius: 8px; border-left: 3px solid var(--warning-color);">
                <p style="margin: 0; color: var(--text-secondary); font-size: 14px;">
                    <i class="fas fa-info-circle" style="color: var(--warning-color);"></i>
                    Detailed financial breakdown including milestones and payments will be available once contracts are established.
                </p>
            </div>
        </div>
    `;

    // Update drawer content
    document.getElementById('tab-overview').innerHTML = overviewContent;
    document.getElementById('tab-customer').innerHTML = customerContent;
    document.getElementById('tab-timeline').innerHTML = timelineContent;
    document.getElementById('tab-financial').innerHTML = financialContent;

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
        filterSelect.addEventListener('change', function() {
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
        toggle.addEventListener('click', function() {
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
            await saveProject();
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
            const content = document.getElementById(`tab-${tabName}`);
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
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
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
window.openProjectModal = openProjectModal;
window.closeProjectModal = closeProjectModal;
window.editProject = editProject;
window.viewProjectDetails = viewProjectDetails;
window.closeProjectDrawer = closeProjectDrawer;
window.deleteProject = deleteProject;
window.updateProgress = updateProgress;
window.updateStatus = updateStatus;
