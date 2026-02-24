/**
 * Freelancers Database Integration
 * Connects the workforce.php frontend to api/freelancers.php
 */

const FREELANCERS_API_URL = '/2nd-Year-Group-Project/FixLanka/api/freelancers.php';
let freelancersList = [];

/**
 * Load all freelancers available to the company
 */
async function loadFreelancers() {
    if (!currentCompanyId) {
        console.error('Company ID not found');
        return;
    }

    try {
        const response = await fetch(`${FREELANCERS_API_URL}?company_id=${currentCompanyId}`);

        if (!response.ok) {
            throw new Error('Failed to fetch freelancers');
        }

        const data = await response.json();

        if (data.success) {
            freelancersList = data.freelancers || [];
            renderFreelancersList(freelancersList);
        } else {
            console.error('API Error:', data.error);
        }

    } catch (error) {
        console.error('Error loading freelancers:', error);
    }
}

/**
 * Render freelancers grid in the UI
 */
function renderFreelancersList(freelancers) {
    const container = document.getElementById('freelancersGrid');
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

        const isHired = freelancer.is_hired == 1;
        const buttonHtml = isHired ?
            `<button class="btn btn-outline" style="width:100%" disabled>Already Hired</button>` :
            `<button class="btn btn-primary" style="width:100%" onclick="openAssignJobDrawer(${freelancer.repairer_id})">Offer Job</button>`;

        const avatarHtml = freelancer.profile_photo ?
            `<img src="/2nd-Year-Group-Project/FixLanka/${freelancer.profile_photo}" alt="${freelancer.first_name}" class="freelancer-avatar">` :
            `<div class="freelancer-avatar-placeholder">${freelancer.first_name.charAt(0)}${freelancer.last_name.charAt(0)}</div>`;

        const card = document.createElement('div');
        card.className = 'freelancer-card';
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
                        <div class="rating-stars" style="color: #ffc107; font-size: 10px;">
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

    // Load jobs dynamically
    const jobSelect = document.getElementById('assignJobSelect');
    if (jobSelect && currentCompanyId) {
        try {
            jobSelect.innerHTML = '<option value="">Loading jobs...</option>';
            const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/job-postings.php?company_id=${currentCompanyId}`);
            const result = await response.json();

            jobSelect.innerHTML = '<option value="">-- Select a job --</option>';
            if (result.success && result.postings) {
                result.postings.forEach(job => {
                    if (job.status !== 'closed' && job.status !== 'filled') {
                        const option = document.createElement('option');
                        option.value = job.posting_id;
                        option.textContent = `${job.title} (${job.category})`;
                        jobSelect.appendChild(option);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading jobs for select:', error);
            jobSelect.innerHTML = '<option value="">Error loading jobs</option>';
        }
    }

    // Open drawer
    const drawer = document.getElementById('assignJobDrawer');
    if (drawer) {
        drawer.style.display = 'block';
        setTimeout(() => drawer.classList.add('active'), 10);
    }
};

window.closeAssignJobDrawer = function () {
    const drawer = document.getElementById('assignJobDrawer');
    if (drawer) {
        drawer.classList.remove('active');
        setTimeout(() => {
            drawer.style.display = 'none';
            document.getElementById('assignJobForm').reset();
            currentAssignFreelancerId = null;
        }, 300);
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
