/**
 * Company Employees Database Integration
 * 
 * Handles all frontend operations for company employee management
 * Connects to API endpoints for CRUD operations
 * 
 * @version 1.0.0
 */

// Get company ID from global scope
var currentCompanyId = window.CURRENT_COMPANY_ID || window.CURRENT_USER_ID || window.currentCompanyId || null;

// API endpoint
const API_URL = '/2nd-Year-Group-Project/FixLanka/api/company-employees.php';

/**
 * Load all employees and statistics
 */
async function loadEmployeesData() {
    if (!currentCompanyId) {
        console.error('Company ID not found');
        showToast('Error: Company ID not found', 'error');
        return;
    }

    try {
        // Load statistics
        await loadEmployeeStatistics();

        // Load employees list (if needed)
        // await loadEmployeesList();

    } catch (error) {
        console.error('Error loading employees data:', error);
        showToast('Failed to load employee data', 'error');
    }
}

/**
 * Load employee statistics from database
 */
async function loadEmployeeStatistics() {
    try {
        const response = await fetch(`${API_URL}?action=stats&company_id=${currentCompanyId}`);

        if (!response.ok) {
            throw new Error('Failed to fetch statistics');
        }

        const data = await response.json();
        updateStatisticsUI(data);

    } catch (error) {
        console.error('Error loading statistics:', error);
        showToast('Failed to load statistics', 'error');
    }
}

/**
 * Update UI with statistics from database
 */
function updateStatisticsUI(data) {
    const { specialties, totals } = data;

    // Update main stats in preview card
    updateElement('.staff-card .stat-number', totals.available_employees !== undefined ? totals.available_employees : (totals.active_employees || 0));
    updateElement('.staff-card .stat-label', totals.available_employees !== undefined ? 'Available Staff' : 'Active Staff');

    updateElement('.staff-card .sub-stat:nth-child(1) .sub-number', totals.total_employees || 0);
    updateElement('.staff-card .sub-stat:nth-child(1) .sub-label', 'Total Staff');

    updateElement('.staff-card .sub-stat:nth-child(2) .sub-number', parseFloat(totals.avg_rating || 0).toFixed(1));
    updateElement('.staff-card .sub-stat:nth-child(2) .sub-label', 'Avg Rating');

    // Update specialty counts in preview card
    updateSpecialtyPreview(specialties);

    // Update category cards in detailed view
    updateCategoryCards(specialties);
}

/**
 * Update specialty preview in preview card
 */
function updateSpecialtyPreview(specialties) {
    const container = document.querySelector('.staff-card .specialty-items');
    if (!container) return;

    container.innerHTML = '';

    if (!specialties || specialties.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No employees yet</p>';
        return;
    }

    specialties.forEach(spec => {
        const item = document.createElement('div');
        item.className = 'specialty-item';
        const displayCount = spec.available_count !== undefined ? spec.available_count : (spec.active_count || 0);
        item.innerHTML = `
            <span class="specialty-name">${spec.specialty}</span>
            <span class="specialty-count">${displayCount} Available / ${spec.total_count || 0} Total</span>
        `;
        container.appendChild(item);
    });
}

/**
 * Update category cards in detailed section
 */
function updateCategoryCards(specialties) {
    const container = document.querySelector('.employee-categories');
    if (!container) return;

    container.innerHTML = '';

    if (!specialties || specialties.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-users" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                <p style="color: #999; font-size: 16px;">No employees found. Click "Add Staff" to get started.</p>
            </div>
        `;
        return;
    }

    // Icon mapping
    const iconMap = {
        'Plumbing': 'fa-wrench',
        'Electrical': 'fa-bolt',
        'Carpentry': 'fa-hammer',
        'HVAC': 'fa-fan',
        'HVAC Technician': 'fa-fan',
        'Painting': 'fa-paint-roller',
        'Painter': 'fa-paint-roller',
        'Masonry': 'fa-hard-hat',
        'Welding': 'fa-fire',
        'General': 'fa-tools'
    };

    // Color mapping
    const colorMap = {
        'Plumbing': 'plumbing',
        'Electrical': 'electrical',
        'Carpentry': 'carpentry',
        'HVAC': 'hvac',
        'HVAC Technician': 'hvac',
        'Painting': 'painting',
        'Painter': 'painting',
        'Masonry': 'masonry',
        'Welding': 'welding',
        'General': 'general'
    };

    specialties.forEach(spec => {
        const icon = iconMap[spec.specialty] || 'fa-tools';
        const colorClass = colorMap[spec.specialty] || 'general';

        const totalCount = Number(spec.total_count || 0);
        const availableCount = spec.available_count !== undefined ? Number(spec.available_count) : Number(spec.active_count || 0);
        const countText = `${availableCount.toLocaleString()} Available / ${totalCount.toLocaleString()} Total`;

        const card = document.createElement('div');
        card.className = 'category-card category-card--summary';
        card.innerHTML = `
            <div class="category-icon ${colorClass}">
                <i class="fas ${icon}"></i>
            </div>
            <div class="category-info">
                <h3>${spec.specialty}</h3>
                <div class="category-meta">
                    <span class="repairer-count">${countText}</span>
                </div>
            </div>
        `;
        container.appendChild(card);
    });
}

/**
 * Helper function to update element text content
 */
function updateElement(selector, value) {
    const element = document.querySelector(selector);
    if (element) {
        element.textContent = value;
    }
}

/**
 * Create new employee
 */
async function createEmployee(employeeData) {
    try {
        employeeData.company_id = currentCompanyId;

        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(employeeData)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Employee added successfully', 'success');
            await loadEmployeesData(); // Reload data
            return true;
        } else {
            showToast(result.message || 'Failed to add employee', 'error');
            return false;
        }

    } catch (error) {
        console.error('Error creating employee:', error);
        showToast('Failed to add employee', 'error');
        return false;
    }
}

/**
 * Bulk add employees
 */
async function bulkAddEmployees(employees) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                bulk: true,
                company_id: currentCompanyId,
                employees: employees
            })
        });

        const result = await response.json();

        if (result.success) {
            showToast(result.message, 'success');
            await loadEmployeesData();
            return true;
            const raw = await response.text();
            let result;
            try {
                result = raw ? JSON.parse(raw) : null;
            } catch (e) {
                throw new Error('Server returned non-JSON response for bulk add');
            }

            if (!response.ok) {
                throw new Error((result && (result.message || result.error)) || 'Bulk add failed');
            }
            showToast(result.message || 'Failed to add employees', 'error');
            return false;
        }

    } catch (error) {
        console.error('Error bulk adding employees:', error);
        showToast('Failed to add employees', 'error');
        return false;
    }
}

/**
 * Update employee
 */
async function updateEmployee(employeeId, employeeData) {
    try {
        const response = await fetch(`${API_URL}?employee_id=${employeeId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(employeeData)
        });

        const result = await response.json();

        if (result.success) {
            showToast('Employee updated successfully', 'success');
            await loadEmployeesData();
            return true;
        } else {
            showToast(result.message || 'Failed to update employee', 'error');
            return false;
        }

    } catch (error) {
        console.error('Error updating employee:', error);
        showToast('Failed to update employee', 'error');
        return false;
    }
}

/**
 * Update employee status
 */
async function updateEmployeeStatus(employeeId, status) {
    try {
        const response = await fetch(`${API_URL}?employee_id=${employeeId}&action=status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: status })
        });

        const result = await response.json();

        if (result.success) {
            showToast('Employee status updated', 'success');
            await loadEmployeesData();
            return true;
        } else {
            showToast(result.message || 'Failed to update status', 'error');
            return false;
        }

    } catch (error) {
        console.error('Error updating employee status:', error);
        showToast('Failed to update status', 'error');
        return false;
    }
}

/**
 * Delete employee
 */
async function deleteEmployee(employeeId) {
    if (!confirm('Are you sure you want to offboard this employee? They will be removed from active workforce but history will be kept.')) {
        return false;
    }

    try {
        const response = await fetch(`${API_URL}?employee_id=${employeeId}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (result.success) {
            showToast('Employee offboarded successfully', 'success');
            await loadEmployeesData();
            return true;
        } else {
            showToast(result.message || 'Failed to remove employee', 'error');
            return false;
        }

    } catch (error) {
        console.error('Error deleting employee:', error);
        showToast('Failed to remove employee', 'error');
        return false;
    }
}

/**
 * Reduce staff by specialty
 * Gets employees by specialty and deletes the specified quantity
 */
async function reduceStaffBySpecialty(reductionData) {
    try {
        // reductionData format: [{ skillCategory: 'Plumbing', reductionQuantity: 2 }, ...]

        const promises = reductionData.map(async (reduction) => {
            // Get employees by specialty
            const response = await fetch(`${API_URL}?company_id=${currentCompanyId}&specialty=${encodeURIComponent(reduction.skillCategory)}`);

            if (!response.ok) {
                throw new Error(`Failed to fetch ${reduction.skillCategory} employees`);
            }

            const employees = await response.json();

            if (!employees || employees.length === 0) {
                throw new Error(`No ${reduction.skillCategory} employees found`);
            }

            if (employees.length < reduction.reductionQuantity) {
                throw new Error(`Only ${employees.length} ${reduction.skillCategory} employees available, cannot reduce ${reduction.reductionQuantity}`);
            }

            // Delete the first N employees (oldest hired first)
            const employeesToDelete = employees.slice(0, reduction.reductionQuantity);

            const deletePromises = employeesToDelete.map(emp =>
                fetch(`${API_URL}?employee_id=${emp.employee_id}`, {
                    method: 'DELETE'
                }).then(res => res.json())
            );

            return Promise.all(deletePromises);
        });

        await Promise.all(promises);

        const totalReduced = reductionData.reduce((sum, item) => sum + item.reductionQuantity, 0);
        showToast(`Successfully reduced ${totalReduced} staff members`, 'success');

        // Reload data
        await loadEmployeesData();

        return true;

    } catch (error) {
        console.error('Error reducing staff:', error);
        showToast('Failed to reduce staff: ' + error.message, 'error');
        return false;
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} show`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;

    // Add to body
    document.body.appendChild(toast);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Initialize when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        if (currentCompanyId) {
            loadEmployeesData();
        }
    });
} else {
    if (currentCompanyId) {
        loadEmployeesData();
    }
}

// Export functions for global access
window.companyEmployees = {
    load: loadEmployeesData,
    create: createEmployee,
    bulkAdd: bulkAddEmployees,
    update: updateEmployee,
    updateStatus: updateEmployeeStatus,
    delete: deleteEmployee,
    reduceStaff: reduceStaffBySpecialty
};
