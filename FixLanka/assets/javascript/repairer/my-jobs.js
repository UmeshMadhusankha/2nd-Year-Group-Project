// ================================================
// MY JOBS PAGE JAVASCRIPT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    initializeMyJobsPage();
});

function initializeMyJobsPage() {
    initializeFilterTabs();
    initializeStatusSelects();
    initializeSortFilter();
    updateJobCounts();
}

// ===== FILTER TABS FUNCTIONALITY ===== 
function initializeFilterTabs() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const jobsList = document.querySelector('.jobs-list');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active tab
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Apply filter
            applyJobFilter(filter);
            
            // Update job counts display
            updateFilteredJobCount(filter);
        });
    });
}

function applyJobFilter(filter) {
    const jobItems = document.querySelectorAll('.job-item');
    const jobsList = document.querySelector('.jobs-list');
    
    // Remove existing filter classes
    jobsList.classList.remove('filter-active', 'filter-completed');
    
    jobItems.forEach(item => {
        if (filter === 'all') {
            item.style.display = 'flex';
        } else if (filter === 'active') {
            jobsList.classList.add('filter-active');
            if (['active', 'in-progress', 'on-hold', 'scheduled'].includes(item.getAttribute('data-status'))) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else if (filter === 'paid') {
            jobsList.classList.add('filter-paid');
            if (['complete', 'paid', 'completed'].includes(item.getAttribute('data-status'))) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else if (filter === 'cancelled') {
            jobsList.classList.add('filter-cancelled');
            if (item.getAttribute('data-status') === 'cancelled') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        }
    });
}

function updateFilteredJobCount(filter) {
    const sectionSubtitle = document.querySelector('.section-subtitle');
    const activeJobs = document.querySelectorAll('.job-item[data-status="active"], .job-item[data-status="in-progress"], .job-item[data-status="on-hold"], .job-item[data-status="scheduled"]').length;
    const completedJobs = document.querySelectorAll('.job-item[data-status="complete"], .job-item[data-status="paid"], .job-item[data-status="completed"]').length;
    const cancelledJobs = document.querySelectorAll('.job-item[data-status="cancelled"]').length;
    const totalJobs = activeJobs + completedJobs + cancelledJobs;
    
    let count = 0;
    let label = '';
    
    switch(filter) {
        case 'all':
            count = totalJobs;
            label = 'jobs found';
            break;
        case 'active':
            count = activeJobs;
            label = 'active jobs';
            break;
        case 'paid':
            count = completedJobs;
            label = 'paid jobs';
            break;
        case 'cancelled':
            count = cancelledJobs;
            label = 'cancelled jobs';
            break;
    }
    
    sectionSubtitle.textContent = `${count} ${label}`;
}

function updateJobCounts() {
    const activeJobs = document.querySelectorAll('.job-item[data-status="active"], .job-item[data-status="in-progress"], .job-item[data-status="on-hold"], .job-item[data-status="scheduled"]').length;
    const completedJobs = document.querySelectorAll('.job-item[data-status="complete"], .job-item[data-status="paid"], .job-item[data-status="completed"]').length;
    const cancelledJobs = document.querySelectorAll('.job-item[data-status="cancelled"]').length;
    const totalJobs = activeJobs + completedJobs + cancelledJobs;
    
    // Update header stats
    const headerStats = document.querySelectorAll('.header-stat-number');
    if (headerStats.length >= 2) {
        headerStats[0].textContent = activeJobs;
        headerStats[1].textContent = totalJobs;
    }
    
    // Update tab counts
    const tabCounts = document.querySelectorAll('.tab-count');
    if (tabCounts.length >= 4) {
        tabCounts[0].textContent = totalJobs; // All jobs
        tabCounts[1].textContent = activeJobs; // Active jobs
        tabCounts[2].textContent = completedJobs; // Paid jobs
        tabCounts[3].textContent = cancelledJobs; // Cancelled jobs
    }
}

// ===== STATUS SELECT FUNCTIONALITY =====
function initializeStatusSelects() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const jobItem = this.closest('.job-item');
            const jobId = jobItem.querySelector('.job-title').textContent;
            
            console.log(`Status changed for "${jobId}" to: ${this.value}`);
            
            // Here you would typically send an API request to update the status
            // For now, we'll just show a confirmation
            if (this.value === 'complete') {
                if (confirm('Mark this job as complete? Customer will be notified.')) {
                    markJobAsComplete(jobItem);
                } else {
                    // Reset to previous value
                    this.value = 'in-progress';
                }
            } else if (this.value === 'paid') {
                if (confirm('Mark this job as paid? This will finalize the job.')) {
                    markJobAsPaid(jobItem);
                } else {
                    // Reset to previous value
                    this.value = this.previousValue || 'complete';
                }
            }
        });
        
        // Store previous value for reset functionality
        select.addEventListener('focus', function() {
            this.previousValue = this.value;
        });
    });
}

function markJobAsComplete(jobItem) {
    // Update the job item to complete state
    jobItem.setAttribute('data-status', 'complete');
    
    // Update the status badge
    const statusBadge = jobItem.querySelector('.job-status-badge');
    statusBadge.className = 'job-status-badge complete';
    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i>Complete';
    
    // Update the date to show completion
    const jobDate = jobItem.querySelector('.job-date span');
    jobDate.textContent = 'Completed just now';
    
    // Update the status select options
    const statusSelect = jobItem.querySelector('.status-select');
    statusSelect.innerHTML = `
        <option value="complete" selected>Complete</option>
        <option value="paid">Paid</option>
    `;
    
    // Update counts
    updateJobCounts();
    
    // Show success message
    showNotification('Job marked as complete! Awaiting payment.', 'success');
}

function markJobAsPaid(jobItem) {
    // Update the job item to paid state
    jobItem.setAttribute('data-status', 'paid');
    
    // Update the status badge
    const statusBadge = jobItem.querySelector('.job-status-badge');
    statusBadge.className = 'job-status-badge paid';
    statusBadge.innerHTML = '<i class="fas fa-credit-card"></i>Paid';
    
    // Replace actions with completion info
    const jobActions = jobItem.querySelector('.job-actions');
    jobActions.innerHTML = `
        <div class="completion-info">
            <span class="completed-label">
                <i class="fas fa-check"></i>
                Completed & Paid
            </span>
        </div>
        <button class="btn btn-secondary" onclick="viewJobDetails('${Date.now()}')">
            <i class="fas fa-eye"></i>
            View Details
        </button>
        <button class="btn btn-outline" onclick="downloadInvoice('${Date.now()}')">
            <i class="fas fa-download"></i>
            Invoice
        </button>
    `;
    
    // Update the date to show payment completion
    const jobDate = jobItem.querySelector('.job-date span');
    jobDate.textContent = 'Paid just now';
    
    // Update counts
    updateJobCounts();
    
    // Show success message
    showNotification('Job payment received! Job completed successfully.', 'success');
}

// ===== CANCEL JOB FUNCTIONALITY =====
function cancelJob(button, jobId) {
    const jobItem = button.closest('.job-item');
    const jobTitle = jobItem.querySelector('.job-title').textContent;
    
    // Show confirmation dialog with reason
    const reason = prompt(`Are you sure you want to cancel the job "${jobTitle}"?\n\nPlease provide a reason for cancellation:`);
    
    if (reason && reason.trim() !== '') {
        // Update the job item to cancelled state
        jobItem.setAttribute('data-status', 'cancelled');
        
        // Update the status badge
        const statusBadge = jobItem.querySelector('.job-status-badge');
        statusBadge.className = 'job-status-badge cancelled';
        statusBadge.innerHTML = '<i class="fas fa-times-circle"></i>Cancelled';
        
        // Update the date to show cancellation
        const jobDate = jobItem.querySelector('.job-date span');
        jobDate.textContent = 'Cancelled just now';
        
        // Replace actions with cancellation info
        const jobActions = jobItem.querySelector('.job-actions');
        jobActions.innerHTML = `
            <div class="cancellation-info">
                <span class="cancelled-label">
                    <i class="fas fa-times-circle"></i>
                    Job Cancelled
                </span>
                <span class="cancel-reason">Reason: ${reason}</span>
            </div>
            <button class="btn btn-secondary" onclick="viewJobDetails(${jobId})">
                <i class="fas fa-eye"></i>
                View Details
            </button>
        `;
        
        // Update counts
        updateJobCounts();
        
        // Get current active filter
        const activeFilter = document.querySelector('.filter-tab.active');
        if (activeFilter) {
            const filter = activeFilter.getAttribute('data-filter');
            updateFilteredJobCount(filter);
            
            // If we're on 'active' filter, hide the cancelled job
            if (filter === 'active') {
                jobItem.style.display = 'none';
            }
        }
        
        // Show success message
        showNotification('Job cancelled successfully. Customer will be notified.', 'warning');
        
        // Log the cancellation (in production, this would be an API call)
        console.log(`Job ${jobId} cancelled. Reason: ${reason}`);
    } else if (reason !== null) {
        // User clicked OK but didn't provide a reason
        alert('Please provide a reason for cancellation.');
    }
    // If reason is null, user clicked Cancel, so do nothing
}

function markJobAsCompleted(jobItem) {
    // Update the job item to completed state
    jobItem.setAttribute('data-status', 'completed');
    
    // Update the status badge
    const statusBadge = jobItem.querySelector('.job-status-badge');
    statusBadge.className = 'job-status-badge completed';
    statusBadge.innerHTML = '<i class="fas fa-check-circle"></i>Completed';
    
    // Replace actions with completion info
    const jobActions = jobItem.querySelector('.job-actions');
    jobActions.innerHTML = `
        <div class="completion-info">
            <span class="completed-label">
                <i class="fas fa-check"></i>
                Completed & Paid
            </span>
        </div>
        <button class="btn btn-secondary" onclick="viewJobDetails('${Date.now()}')">
            <i class="fas fa-eye"></i>
            View Details
        </button>
        <button class="btn btn-outline" onclick="downloadInvoice('${Date.now()}')">
            <i class="fas fa-download"></i>
            Invoice
        </button>
    `;
    
    // Update the date to show completion
    const jobDate = jobItem.querySelector('.job-date span');
    jobDate.textContent = 'Completed just now';
    
    // Update counts
    updateJobCounts();
    
    // Show success message
    showNotification('Job marked as completed successfully!', 'success');
}

// ===== SORT FUNCTIONALITY =====
function initializeSortFilter() {
    const sortFilter = document.getElementById('sort-filter');
    
    sortFilter.addEventListener('change', function() {
        const sortBy = this.value;
        sortJobs(sortBy);
    });
}

function sortJobs(sortBy) {
    const jobsList = document.querySelector('.jobs-list');
    const jobItems = Array.from(jobsList.querySelectorAll('.job-item'));
    
    jobItems.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                // Sort by newest first (reverse chronological)
                return getJobDate(b) - getJobDate(a);
            case 'oldest':
                // Sort by oldest first (chronological)
                return getJobDate(a) - getJobDate(b);
            case 'status':
                // Sort by status (active first, then completed)
                const statusA = a.getAttribute('data-status');
                const statusB = b.getAttribute('data-status');
                if (statusA === statusB) return 0;
                return statusA === 'active' ? -1 : 1;
            case 'amount':
                // Sort by amount (highest first)
                return getJobAmount(b) - getJobAmount(a);
            default:
                return 0;
        }
    });
    
    // Re-append sorted items
    jobItems.forEach(item => jobsList.appendChild(item));
    
    console.log(`Jobs sorted by: ${sortBy}`);
}

function getJobDate(jobItem) {
    // This is a simplified date extraction
    // In a real application, you'd have actual date data
    const dateText = jobItem.querySelector('.job-date span').textContent;
    if (dateText.includes('today')) return new Date();
    if (dateText.includes('yesterday')) return new Date(Date.now() - 86400000);
    if (dateText.includes('2 days ago')) return new Date(Date.now() - 172800000);
    if (dateText.includes('3 days ago')) return new Date(Date.now() - 259200000);
    if (dateText.includes('1 week ago')) return new Date(Date.now() - 604800000);
    return new Date(Date.now() - Math.random() * 2629746000); // Random date within last month
}

function getJobAmount(jobItem) {
    const amountText = jobItem.querySelector('.amount').textContent;
    // Extract number from "LKR 2,500" format
    return parseInt(amountText.replace(/[^\d]/g, '')) || 0;
}

// ===== JOB ACTIONS =====
function updateJobStatus(jobId) {
    console.log(`Updating status for job ID: ${jobId}`);
    
    // Get the job item
    const jobItem = document.querySelector(`[data-job-id="${jobId}"]`) || 
                    document.querySelectorAll('.job-item')[jobId - 1];
    
    if (!jobItem) {
        showNotification('Job not found!', 'error');
        return;
    }
    
    const statusSelect = jobItem.querySelector('.status-select');
    const newStatus = statusSelect.value;
    
    // Simulate API call
    showNotification('Updating job status...', 'info');
    
    setTimeout(() => {
        if (newStatus === 'completed') {
            markJobAsCompleted(jobItem);
        } else {
            showNotification(`Job status updated to: ${newStatus}`, 'success');
        }
    }, 1000);
}

function viewJobDetails(jobId) {
    console.log(`Viewing details for job ID: ${jobId}`);
    
    // In a real application, this would open a modal or navigate to a details page
    showNotification('Opening job details...', 'info');
    
    // Simulate navigation
    setTimeout(() => {
        alert(`Job Details for ID: ${jobId}\n\nThis would open a detailed view of the job with:\n- Full job description\n- Customer contact information\n- Progress timeline\n- Messages and updates\n- Payment information`);
    }, 500);
}

function downloadInvoice(jobId) {
    console.log(`Downloading invoice for job ID: ${jobId}`);
    
    showNotification('Generating invoice...', 'info');
    
    // Simulate invoice generation and download
    setTimeout(() => {
        showNotification('Invoice downloaded successfully!', 'success');
        
        // In a real application, this would trigger an actual file download
        console.log(`Invoice for job ${jobId} would be downloaded as PDF`);
    }, 1500);
}

function loadMoreJobs() {
    console.log('Loading more jobs...');
    
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const originalText = loadMoreBtn.innerHTML;
    
    // Show loading state
    loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    loadMoreBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Reset button
        loadMoreBtn.innerHTML = originalText;
        loadMoreBtn.disabled = false;
        
        // Add some demo jobs
        addDemoJobs();
        
        showNotification('3 more jobs loaded!', 'success');
    }, 2000);
}

function addDemoJobs() {
    const jobsList = document.querySelector('.jobs-list');
    const loadMoreSection = document.querySelector('.load-more-section');
    
    const demoJobs = [
        {
            title: 'Water Heater Repair',
            customer: 'Ananda Wickramasinghe',
            location: 'Kottawa, Western Province',
            date: 'Completed 2 weeks ago',
            amount: 'LKR 3,200',
            status: 'completed'
        },
        {
            title: 'Door Lock Installation',
            customer: 'Sunil Fernando',
            location: 'Panadura, Western Province', 
            date: 'Started 4 hours ago',
            amount: 'LKR 1,200',
            status: 'active'
        },
        {
            title: 'Garden Light Setup',
            customer: 'Mala Perera',
            location: 'Moratuwa, Western Province',
            date: 'Completed 10 days ago',
            amount: 'LKR 2,800',
            status: 'completed'
        }
    ];
    
    demoJobs.forEach((job, index) => {
        const jobElement = createJobElement(job, Date.now() + index);
        jobsList.insertBefore(jobElement, loadMoreSection);
    });
    
    // Update counts
    updateJobCounts();
}

function createJobElement(job, jobId) {
    const jobElement = document.createElement('div');
    jobElement.className = `job-item ${job.status}-job`;
    jobElement.setAttribute('data-status', job.status);
    
    const statusBadge = job.status === 'active' 
        ? '<div class="job-status-badge active"><i class="fas fa-play-circle"></i>Active</div>'
        : '<div class="job-status-badge completed"><i class="fas fa-check-circle"></i>Completed</div>';
    
    const actions = job.status === 'active'
        ? `
            <select class="status-select">
                <option value="in-progress" selected>In Progress</option>
                <option value="on-hold">On Hold</option>
                <option value="completed">Mark Complete</option>
            </select>
            <button class="btn btn-secondary" onclick="viewJobDetails('${jobId}')">
                <i class="fas fa-eye"></i>
                View Details
            </button>
            <button class="btn btn-primary update-status-btn" onclick="updateJobStatus('${jobId}')">
                <i class="fas fa-sync"></i>
                Update Status
            </button>
        `
        : `
            <div class="completion-info">
                <span class="completed-label">
                    <i class="fas fa-check"></i>
                    Completed & Paid
                </span>
            </div>
            <button class="btn btn-secondary" onclick="viewJobDetails('${jobId}')">
                <i class="fas fa-eye"></i>
                View Details
            </button>
            <button class="btn btn-outline" onclick="downloadInvoice('${jobId}')">
                <i class="fas fa-download"></i>
                Invoice
            </button>
        `;
    
    jobElement.innerHTML = `
        <div class="job-info">
            <div class="job-header">
                <h3 class="job-title">${job.title}</h3>
                ${statusBadge}
            </div>
            <div class="job-details">
                <div class="job-customer">
                    <i class="fas fa-user"></i>
                    <span>${job.customer}</span>
                </div>
                <div class="job-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${job.location}</span>
                </div>
                <div class="job-date">
                    <i class="fas fa-calendar"></i>
                    <span>${job.date}</span>
                </div>
                <div class="job-amount">
                    <i class="fas fa-money-bill"></i>
                    <span class="amount">${job.amount}</span>
                </div>
            </div>
        </div>
        <div class="job-actions">
            ${actions}
        </div>
    `;
    
    return jobElement;
}

// ===== UTILITY FUNCTIONS =====
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        max-width: 300px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            notification.style.background = '#10b981';
            break;
        case 'error':
            notification.style.background = '#ef4444';
            break;
        case 'warning':
            notification.style.background = '#f59e0b';
            break;
        default:
            notification.style.background = '#3b82f6';
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after delay
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ===== SEARCH FUNCTIONALITY =====
function initializeSearch() {
    const searchInput = document.querySelector('.search-box input');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            searchJobs(searchTerm);
        });
    }
}

function searchJobs(searchTerm) {
    const jobItems = document.querySelectorAll('.job-item');
    
    jobItems.forEach(item => {
        const title = item.querySelector('.job-title').textContent.toLowerCase();
        const customer = item.querySelector('.job-customer span').textContent.toLowerCase();
        const location = item.querySelector('.job-location span').textContent.toLowerCase();
        
        const matches = title.includes(searchTerm) || 
                       customer.includes(searchTerm) || 
                       location.includes(searchTerm);
        
        item.style.display = matches || searchTerm === '' ? 'flex' : 'none';
    });
    
    // Update count based on visible items
    const visibleJobs = document.querySelectorAll('.job-item[style*="flex"], .job-item:not([style*="none"])').length;
    const sectionSubtitle = document.querySelector('.section-subtitle');
    
    if (searchTerm) {
        sectionSubtitle.textContent = `${visibleJobs} jobs found for "${searchTerm}"`;
    } else {
        // Reset to current filter
        const activeTab = document.querySelector('.filter-tab.active');
        if (activeTab) {
            updateFilteredJobCount(activeTab.getAttribute('data-filter'));
        }
    }
}
