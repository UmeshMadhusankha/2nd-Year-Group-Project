/**
 * Freelancers Database Integration
 * Connects the workforce.php frontend to api/freelancers.php
 */

// Workforce "Available Freelancers" should show the company's freelance contractors
// (repairers recruited via job postings), not the company's permanent employees.
const COMPANY_EMPLOYEES_API_URL = '/2nd-Year-Group-Project/FixLanka/api/company-employees.php';
const REPAIRER_APPLICATIONS_API_URL = '/2nd-Year-Group-Project/FixLanka/api/repairer-applications.php';
let freelancersList = [];

// Remember the current filter so it can be re-applied after re-render.
window.__freelancerFilterState = window.__freelancerFilterState || { key: 'status', value: 'all' };

function applyFreelancerFilterToDom(filterKey, filterValue) {
    const key = (filterKey || 'status').toString().toLowerCase();
    const value = (filterValue || 'all').toString().toLowerCase();

    const cards = document.querySelectorAll('.freelancer-list .freelancer-card, #freelancersGrid .freelancer-card');
    let visibleCount = 0;

    cards.forEach(card => {
        let shouldShow = true;

        if (key === 'status') {
            const cardStatus = (card.dataset.status || '').toString().toLowerCase();
            const isAssigned = (card.dataset.assigned || '0') === '1';

            if (value === 'all') {
                shouldShow = true;
            } else if (value === 'assigned') {
                shouldShow = isAssigned || cardStatus === 'assigned';
            } else {
                shouldShow = cardStatus === value;
            }
        }

        card.style.display = shouldShow ? '' : 'none';
        if (shouldShow) visibleCount++;
    });

    // Empty state (only for workforce.php grid)
    const container = document.querySelector('.freelancer-list') || document.getElementById('freelancersGrid');
    if (!container) return;

    const existingEmpty = container.querySelector('.empty-state[data-filter-empty="1"]');
    if (visibleCount === 0) {
        if (!existingEmpty) {
            const empty = document.createElement('div');
            empty.className = 'empty-state';
            empty.dataset.filterEmpty = '1';
            empty.style.gridColumn = '1 / -1';
            empty.style.textAlign = 'center';
            empty.style.padding = '60px 20px';
            empty.innerHTML = `
                <i class="fas fa-filter" style="font-size: 48px; color: #d1d5db; margin-bottom: 15px;"></i>
                <h3 style="color: #6b7280; margin-bottom: 8px;">No freelancers found</h3>
                <p style="color: #9ca3af;">No ${value === 'all' ? '' : value} freelancers at the moment</p>
            `;
            container.appendChild(empty);
        }
    } else if (existingEmpty) {
        existingEmpty.remove();
    }
}

// Called by inline onclick in workforce.php
window.applyFreelancerFilter = function (filterKey, filterValue, buttonEl) {
    const key = (filterKey || 'status').toString().toLowerCase();
    const value = (filterValue || 'all').toString().toLowerCase();
    window.__freelancerFilterState = { key, value };

    const tabs = document.querySelectorAll('.freelancer-filter-tabs .tab-btn');
    if (tabs && tabs.length) {
        tabs.forEach(btn => btn.classList.remove('active'));

        // Prefer the passed element; fallback to a matching data-filter-value button.
        const toActivate = buttonEl || document.querySelector(`.freelancer-filter-tabs .tab-btn[data-filter-value="${CSS.escape(value)}"]`);
        if (toActivate) toActivate.classList.add('active');
    }

    applyFreelancerFilterToDom(key, value);
};

/**
 * Load all freelancers available to the company
 */
async function loadFreelancers() {
    if (!currentCompanyId) {
        console.error('Company ID not found');
        return;
    }

    try {
        // Fetch only freelance contractors for this company
        const response = await fetch(`${COMPANY_EMPLOYEES_API_URL}?company_id=${currentCompanyId}&employment_type=freelance&order_by=created_at&order_dir=DESC`);

        if (!response.ok) {
            throw new Error('Failed to fetch freelancers');
        }

        const data = await response.json();

        // company-employees API returns an array
        const rows = Array.isArray(data) ? data : (data && Array.isArray(data.data) ? data.data : []);
        let normalized = rows.map(normalizeCompanyFreelancer);

        // Fallback: if onboarding rows are missing/misclassified, show accepted recruits
        // directly from approved applications.
        if (!normalized || normalized.length === 0) {
            const approved = await fetchApprovedApplicationsAsFreelancers();
            if (approved.length > 0) {
                normalized = approved;
            }
        }

        // De-duplicate by repairer_id
        const seen = new Set();
        freelancersList = (normalized || []).filter(fr => {
            const key = String(fr.repairer_id || fr.id || '');
            if (!key) return false;
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });

        renderFreelancersList(freelancersList);
        updateFreelancersPreview(freelancersList);

    } catch (error) {
        console.error('Error loading freelancers:', error);
    }
}

async function fetchApprovedApplicationsAsFreelancers() {
    try {
        const url = `${REPAIRER_APPLICATIONS_API_URL}?action=list&company_id=${currentCompanyId}&status=approved`;
        const resp = await fetch(url);
        if (!resp.ok) return [];
        const data = await resp.json();
        if (!data || !data.success || !Array.isArray(data.applications)) return [];

        return data.applications.map(app => {
            return {
                id: app.repairer_id,
                employee_id: null,
                repairer_id: app.repairer_id,
                first_name: app.first_name || '',
                last_name: app.last_name || '',
                email: app.email || '',
                phone: app.phone || '',
                specialty: app.specialty || 'General',
                experience_years: Number(app.experience_years || 0),
                hourly_rate: Number(app.expected_rate || app.hourly_rate || 0),
                rating: Number(app.rating || 0),
                profile_photo: app.profile_photo || null,
                avatar: null,
                status: 'Available'
            };
        });
    } catch (e) {
        console.warn('Fallback approved-applications fetch failed:', e);
        return [];
    }
}

function normalizeCompanyFreelancer(row) {
    const hourlyRate = Number(row.hourly_rate || row.applicant_hourly_rate || 0);
    const rating = Number(row.rating || 0);
    const statusRaw = (row.status || 'active').toLowerCase();
    const status = statusRaw === 'active' ? 'Available' : 'Unavailable';

    return {
        // workforce-friendly shape
        id: row.employee_id || row.repairer_id,
        employee_id: row.employee_id,
        repairer_id: row.repairer_id,
        first_name: row.first_name || '',
        last_name: row.last_name || '',
        email: row.email || '',
        phone: row.phone || '',
        specialty: row.specialty || 'General',
        experience_years: Number(row.experience_years || 0),
        hourly_rate: hourlyRate,
        rating: rating,
        profile_photo: row.profile_photo || null,
        avatar: row.avatar || null,
        status: status
    };
}

/**
 * Render freelancers grid in the UI
 */
function renderFreelancersList(freelancers) {
    // workforce.php uses `.freelancer-list`; other pages may use `#freelancersGrid`
    const container = document.querySelector('.freelancer-list') || document.getElementById('freelancersGrid');
    if (!container) return;

    container.innerHTML = '';

    if (!freelancers || freelancers.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-users-slash" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                <p style="color: #999; font-size: 16px;">No freelancers available at the moment.</p>
            </div>
        `;
        return;
    }

    freelancers.forEach(freelancer => {
        // Build skills HTML
        let skillsHtml = `<span class="skill-tag">${freelancer.specialty || 'General'}</span>`;

        // Build rating HTML
        let ratingHtml = '';
        const rating = parseFloat(freelancer.rating) || 0;
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                ratingHtml += '<i class="fas fa-star text-warning"></i>';
            } else if (i - 0.5 <= rating) {
                ratingHtml += '<i class="fas fa-star-half-alt text-warning"></i>';
            } else {
                ratingHtml += '<i class="far fa-star text-warning"></i>';
            }
        }

        const buttonHtml = `<button class="btn btn-primary" style="width:100%" onclick="openAssignJobDrawer(${freelancer.repairer_id})">Assign Job</button>`;

        const avatarHtml = freelancer.profile_photo ?
            `<img src="/2nd-Year-Group-Project/FixLanka/${freelancer.profile_photo}" alt="${freelancer.first_name}" class="freelancer-avatar">` :
            `<div class="freelancer-avatar-placeholder">${freelancer.first_name.charAt(0)}${freelancer.last_name.charAt(0)}</div>`;

        const card = document.createElement('div');
        card.className = 'freelancer-card';
        card.dataset.status = (freelancer.status || '').toString().toLowerCase();
        // Placeholder until we wire assignments into this list.
        card.dataset.assigned = '0';
        card.innerHTML = `
            <div class="freelancer-header">
                ${avatarHtml}
                <div class="freelancer-info">
                    <h3 class="freelancer-name">${freelancer.first_name} ${freelancer.last_name}</h3>
                    <div class="freelancer-title">${freelancer.specialty || 'Repairer'} • ${freelancer.experience_years} yrs exp</div>
                </div>
            </div>
            <div class="freelancer-stats">
                <div class="stat">
                    <div class="stat-value">Rs. ${parseFloat(freelancer.hourly_rate || 0).toLocaleString()}/hr</div>
                    <div class="stat-label">Rate</div>
                </div>
                <div class="stat">
                    <div class="stat-value">98%</div>
                    <div class="stat-label">Success</div>
                </div>
                <div class="stat">
                    <div class="stat-value">${freelancer.rating}</div>
                    <div class="stat-label">
                        <div class="rating-stars">
                            ${ratingHtml}
                        </div>
                    </div>
                </div>
            </div>
            <div class="freelancer-skills">
                ${skillsHtml}
            </div>
            <div class="freelancer-actions">
                <button class="btn btn-outline" onclick="viewFreelancerProfile(${freelancer.repairer_id})">View Profile</button>
                ${buttonHtml}
            </div>
        `;
        container.appendChild(card);
    });

    // Re-apply the selected tab filter after re-render.
    const state = window.__freelancerFilterState || { key: 'status', value: 'all' };
    applyFreelancerFilterToDom(state.key, state.value);
}

function updateFreelancersPreview(freelancers) {
    // Dashboard preview card elements exist only on workforce.php
    const availableEl = document.getElementById('freelancersAvailable');
    const activeEl = document.getElementById('freelancersActive');
    const freeEl = document.getElementById('freelancersFree');
    const topEl = document.getElementById('topFreelancersPreview');

    if (availableEl) availableEl.textContent = String(freelancers.length || 0);
    if (activeEl) activeEl.textContent = String(freelancers.length || 0);
    if (freeEl) freeEl.textContent = String(freelancers.length || 0);

    if (topEl) {
        topEl.innerHTML = '';
        if (!freelancers || freelancers.length === 0) {
            topEl.innerHTML = '<p style="color:#718096; font-size:12px; padding: 12px 0;">No freelancers available yet</p>';
            return;
        }

        const top = [...freelancers]
            .sort((a, b) => (Number(b.rating) || 0) - (Number(a.rating) || 0))
            .slice(0, 3);

        topEl.innerHTML = top.map(fr => {
            const initials = (fr.first_name?.charAt(0) || '') + (fr.last_name?.charAt(0) || '');
            return `
                <div class="freelancer-item" style="display:flex; align-items:center; gap:10px; padding: 10px 0; border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <div class="application-avatar" style="width:32px; height:32px; border-radius:999px; display:flex; align-items:center; justify-content:center; background: rgba(0,0,0,0.06); font-weight: 600;">
                        ${initials.toUpperCase()}
                    </div>
                    <div style="display:flex; flex-direction:column; min-width:0;">
                        <span style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${fr.first_name} ${fr.last_name}</span>
                        <span style="font-size:12px; color:#718096;">${fr.specialty || 'General'} • ${Number(fr.rating || 0).toFixed(1)}★</span>
                    </div>
                </div>
            `;
        }).join('');
    }
}

/**
 * Filter freelancers list
 */
function filterFreelancers() {
    const searchInput = document.querySelector('.freelancers-section .search-bar input');
    const skillSelect = document.querySelector('.freelancers-section select:nth-of-type(1)');

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const skillValue = skillSelect ? skillSelect.value : '';

    // Simple frontend filtering
    const filtered = freelancersList.filter(f => {
        const matchesSearch = !searchTerm ||
            `${f.first_name} ${f.last_name}`.toLowerCase().includes(searchTerm) ||
            f.specialty.toLowerCase().includes(searchTerm);

        const matchesSkill = !skillValue || skillValue === 'all' || f.specialty === skillValue;

        return matchesSearch && matchesSkill;
    });

    renderFreelancersList(filtered);
}

// Attach event listeners when module loads
document.addEventListener('DOMContentLoaded', () => {
    // Search input listener
    const searchInput = document.querySelector('.freelancers-section .search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', filterFreelancers);
    }

    // Filter selects listeners
    const selects = document.querySelectorAll('.freelancers-section select');
    selects.forEach(select => {
        select.addEventListener('change', filterFreelancers);
    });
});

// Export functions for global access
window.freelancersDb = {
    load: loadFreelancers,
    freelancers: () => freelancersList
};

// Global assignment functionality
let currentAssignFreelancerId = null;

window.openAssignJobDrawer = async function (freelancerId) {
    const freelancer = freelancersList.find(f => f.repairer_id == freelancerId);
    if (!freelancer) return;

    currentAssignFreelancerId = freelancerId;

    // Set UI details
    document.getElementById('assignPersonName').textContent = `${freelancer.first_name} ${freelancer.last_name}`;
    document.getElementById('assignPersonSpecialty').textContent = freelancer.specialty || 'General';
    document.getElementById('assignPersonAvatar').textContent = `${freelancer.first_name.charAt(0)}${freelancer.last_name.charAt(0)}`;

    // Fill in default hourly rate from freelancer's profile
    const rateInput = document.getElementById('assignHourlyRate');
    if (rateInput) {
        rateInput.value = parseFloat(freelancer.hourly_rate) || 0;
    }

    if (typeof updateAssignmentCost === 'function') {
        updateAssignmentCost();
    }

    // Load projects dynamically
    const jobSelect = document.getElementById('assignJobSelect');
    if (jobSelect && currentCompanyId) {
        try {
            jobSelect.innerHTML = '<option value="">Loading projects...</option>';
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/projects.php?company_id=${currentCompanyId}`);
            const result = await response.json();

            jobSelect.innerHTML = '<option value="">-- Select a project --</option>';
            if (result.success && Array.isArray(result.data)) {
                result.data.forEach(project => {
                    const option = document.createElement('option');
                    option.value = project.project_id;
                    option.textContent = `${project.title} (${project.status || '—'})`;
                    jobSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading projects for select:', error);
            jobSelect.innerHTML = '<option value="">Error loading projects</option>';
        }
    }

    // Open drawer
    const drawer = document.getElementById('assignJobDrawer');
    if (drawer) {
        drawer.classList.add('active');
    }
};

window.closeAssignJobDrawer = function () {
    const drawer = document.getElementById('assignJobDrawer');
    if (drawer) {
        drawer.classList.remove('active');
        setTimeout(() => {
            const form = document.getElementById('assignJobForm');
            if (form) form.reset();
            currentAssignFreelancerId = null;
        }, 150);
    }
};

window.handleJobAssignment = async function (event) {
    event.preventDefault();

    if (!currentCompanyId) {
        alert('Company ID not configured.');
        return false;
    }
    if (!currentAssignFreelancerId) {
        alert('No freelancer selected.');
        return false;
    }

    const projectId = document.getElementById('assignJobSelect').value;
    const startDate = document.getElementById('assignStartDate').value;
    const deadlineDate = document.getElementById('assignDeadline').value;
    const pricingModel = document.querySelector('input[name="pricingModel"]:checked').value;

    let rateOrPrice = 0;
    let estimatedHours = null;

    if (pricingModel === 'hourly') {
        rateOrPrice = document.getElementById('assignHourlyRate').value;
        estimatedHours = document.getElementById('assignEstimatedHours').value;
    } else {
        rateOrPrice = document.getElementById('assignFixedPrice').value;
    }

    const notes = document.getElementById('assignNotes').value;

    const formData = new FormData();
    formData.append('action', 'assign_job');
    formData.append('repairer_id', currentAssignFreelancerId);
    formData.append('project_id', projectId);
    formData.append('start_date', startDate);
    formData.append('deadline_date', deadlineDate);
    formData.append('pricing_model', pricingModel);
    formData.append('rate_or_price', rateOrPrice);
    if (estimatedHours) {
        formData.append('estimated_hours', estimatedHours);
    }
    formData.append('notes', notes);

    try {
        const btn = event.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        btn.disabled = true;

        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/freelancer-assignments.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        btn.innerHTML = originalText;
        btn.disabled = false;

        if (result.success) {
            alert('Job Offer successfully sent to freelancer!');
            window.closeAssignJobDrawer();
        } else {
            alert('Error: ' + result.error);
        }

    } catch (error) {
        console.error('Error assigning job:', error);
        alert('There was an error sending the job offer.');
    }

    return false;
};
