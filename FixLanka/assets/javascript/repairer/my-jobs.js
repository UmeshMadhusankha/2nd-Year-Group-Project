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
    jobsList.classList.remove('filter-active', 'filter-completed', 'filter-paid', 'filter-cancelled');
    
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
        } else if (filter === 'completed') {
            jobsList.classList.add('filter-completed');
            if (item.getAttribute('data-status') === 'completed') {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        } else if (filter === 'paid') {
            jobsList.classList.add('filter-paid');
            if (['paid'].includes(item.getAttribute('data-status'))) {
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
    const completedJobs = document.querySelectorAll('.job-item[data-status="completed"]').length;
    const paidJobs = document.querySelectorAll('.job-item[data-status="paid"]').length;
    const cancelledJobs = document.querySelectorAll('.job-item[data-status="cancelled"]').length;
    const totalJobs = activeJobs + completedJobs + paidJobs + cancelledJobs;
    
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
        case 'completed':
            count = completedJobs;
            label = 'completed jobs awaiting payment';
            break;
        case 'paid':
            count = paidJobs;
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
    const completedJobs = document.querySelectorAll('.job-item[data-status="completed"]').length;
    const paidJobs = document.querySelectorAll('.job-item[data-status="paid"]').length;
    const cancelledJobs = document.querySelectorAll('.job-item[data-status="cancelled"]').length;
    const totalJobs = activeJobs + completedJobs + paidJobs + cancelledJobs;
    
    // Update header stats
    const headerStats = document.querySelectorAll('.header-stat-number');
    if (headerStats.length >= 3) {
        headerStats[0].textContent = activeJobs;
        headerStats[1].textContent = completedJobs; // Awaiting Payment
        headerStats[2].textContent = totalJobs;
    }
    
    // Update tab counts
    const tabCounts = document.querySelectorAll('.tab-count');
    if (tabCounts.length >= 5) {
        tabCounts[0].textContent = totalJobs; // All jobs
        tabCounts[1].textContent = activeJobs; // Active jobs
        tabCounts[2].textContent = completedJobs; // Completed jobs
        tabCounts[3].textContent = paidJobs; // Paid jobs
        tabCounts[4].textContent = cancelledJobs; // Cancelled jobs
    }
}

// ===== STATUS SELECT FUNCTIONALITY =====
function initializeStatusSelects() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const jobItem = this.closest('.job-item');
            const jobId = jobItem.querySelector('.job-title').textContent;
            
            
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
        
    } else if (reason !== null) {
        // User clicked OK but didn't provide a reason
        alert('Please provide a reason for cancellation.');
    }
    // If reason is null, user clicked Cancel, so do nothing
}

// ===== MARK JOB AS COMPLETED FUNCTIONALITY =====
function markJobAsCompleted(button, jobId) {
    const jobItem = button.closest('.job-item');
    const jobTitle = jobItem.querySelector('.job-title').textContent;
    
    // Show confirmation dialog
    if (confirm(`Mark "${jobTitle}" as completed?\n\nThe customer will be notified and requested to make payment.`)) {
        // Update the job item to completed state
        jobItem.setAttribute('data-status', 'completed');
        
        // Update the status badge
        const statusBadge = jobItem.querySelector('.job-status-badge');
        statusBadge.className = 'job-status-badge completed';
        statusBadge.innerHTML = '<i class="fas fa-clipboard-check"></i>Completed';
        
        // Replace actions with completion info
        const jobActions = jobItem.querySelector('.job-actions');
        jobActions.innerHTML = `
            <div class="completion-info">
                <span class="completed-label">
                    <i class="fas fa-hourglass-half"></i>
                    Awaiting Payment
                </span>
                <span class="completion-note">Customer has been notified to proceed with payment</span>
            </div>
            <button class="btn btn-primary" onclick="viewJobDetails(${jobId})">
                <i class="fas fa-eye"></i>
                View Details
            </button>
        `;
        
        // Update the date to show completion
        const jobDate = jobItem.querySelector('.job-date span');
        jobDate.textContent = 'Completed just now';
        
        // Update counts
        updateJobCounts();
        
        // Get current active filter
        const activeFilter = document.querySelector('.filter-tab.active');
        if (activeFilter) {
            const filter = activeFilter.getAttribute('data-filter');
            updateFilteredJobCount(filter);
            
            // If we're on 'active' filter, hide the completed job
            if (filter === 'active') {
                jobItem.style.display = 'none';
            }
        }
        
        // Show success message
        showNotification('Job marked as completed! Customer notified to make payment.', 'success');
        
        // Log the completion (in production, this would be an API call)
        
    }
}

function markJobAsCompleted_old(jobItem) {
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

// Mock job data
const jobsData = {
    1: {
        title: 'Kitchen Sink Repair',
        status: 'Active',
        statusClass: 'active',
        amount: 'LKR 2,800',
        customerName: 'Sarah Fernando',
        customerPhone: '+94 77 123 4567',
        customerEmail: 'sarah.fernando@email.com',
        location: 'Colombo 07, Western Province',
        started: 'October 21, 2025',
        completion: 'October 24, 2025',
        jobId: '#JOB-2025-001',
        category: 'Plumbing',
        description: 'The kitchen sink is leaking from the pipe underneath. Water is dripping constantly and needs immediate repair. The customer mentioned that the issue started 3 days ago and has been getting worse. Please bring necessary tools and replacement parts if needed.',
        serviceCharge: 'LKR 2,500',
        platformFee: 'LKR 375',
        tax: 'LKR 125',
        totalAmount: 'LKR 2,800',
        earnings: 'LKR 2,125',
        timeline: [
            { title: 'Job Accepted', date: 'October 21, 2025 - 10:30 AM', status: 'completed' },
            { title: 'Work Started', date: 'October 21, 2025 - 2:00 PM', status: 'completed' },
            { title: 'In Progress', date: 'Current Status', status: 'active' },
            { title: 'Pending Completion', date: 'Est. October 24, 2025', status: 'pending' }
        ],
        primaryAction: 'Mark as Complete'
    },
    2: {
        title: 'Ceiling Fan Installation',
        status: 'Active',
        statusClass: 'active',
        amount: 'LKR 4,200',
        customerName: 'Kandy Hardware Store',
        customerPhone: '+94 81 234 5678',
        customerEmail: 'contact@kandyhardware.lk',
        location: 'Kandy, Central Province',
        started: 'October 22, 2025',
        completion: 'October 25, 2025',
        jobId: '#JOB-2025-002',
        category: 'Electrical',
        description: 'Installation of 3 ceiling fans in the showroom. The fans have been purchased and are ready for installation. Wiring is already in place. Need to install fans securely and test all functions including speed controls.',
        serviceCharge: 'LKR 3,800',
        platformFee: 'LKR 570',
        tax: 'LKR 190',
        totalAmount: 'LKR 4,200',
        earnings: 'LKR 3,230',
        timeline: [
            { title: 'Job Accepted', date: 'October 22, 2025 - 9:00 AM', status: 'completed' },
            { title: 'Work Started', date: 'October 22, 2025 - 11:30 AM', status: 'completed' },
            { title: 'In Progress', date: 'Current Status', status: 'active' },
            { title: 'Pending Completion', date: 'Est. October 25, 2025', status: 'pending' }
        ],
        primaryAction: 'Mark as Complete'
    },
    3: {
        title: 'Air Conditioning Repair',
        status: 'Active',
        statusClass: 'active',
        amount: 'LKR 3,500',
        customerName: 'Priya Wickramasinghe',
        customerPhone: '+94 71 987 6543',
        customerEmail: 'priya.w@email.com',
        location: 'Nugegoda, Western Province',
        started: 'October 23, 2025',
        completion: 'October 26, 2025',
        jobId: '#JOB-2025-003',
        category: 'Air Conditioning',
        description: 'AC unit not cooling properly. Customer reports that the AC runs but only blows warm air. Needs diagnostic check and repair. Unit is approximately 3 years old, regular brand.',
        serviceCharge: 'LKR 3,100',
        platformFee: 'LKR 465',
        tax: 'LKR 155',
        totalAmount: 'LKR 3,500',
        earnings: 'LKR 2,635',
        timeline: [
            { title: 'Job Accepted', date: 'October 23, 2025 - 8:15 AM', status: 'completed' },
            { title: 'Work Started', date: 'October 23, 2025 - 10:00 AM', status: 'completed' },
            { title: 'In Progress', date: 'Current Status', status: 'active' },
            { title: 'Pending Completion', date: 'Est. October 26, 2025', status: 'pending' }
        ],
        primaryAction: 'Mark as Complete'
    },
    4: {
        title: 'Washing Machine Repair',
        status: 'Paid',
        statusClass: 'paid',
        amount: 'LKR 2,500',
        customerName: 'Nimal Perera',
        customerPhone: '+94 77 555 1234',
        customerEmail: 'nimal.p@email.com',
        location: 'Maharagama, Western Province',
        started: 'October 17, 2025',
        completion: 'October 20, 2025',
        jobId: '#JOB-2025-004',
        category: 'Appliance Repair',
        description: 'Washing machine making loud noise during spin cycle and not draining water properly. Fixed the drainage pump and replaced worn bearings. Machine now operates smoothly.',
        serviceCharge: 'LKR 2,200',
        platformFee: 'LKR 330',
        tax: 'LKR 110',
        totalAmount: 'LKR 2,500',
        earnings: 'LKR 1,870',
        timeline: [
            { title: 'Job Accepted', date: 'October 17, 2025 - 9:30 AM', status: 'completed' },
            { title: 'Work Started', date: 'October 17, 2025 - 1:00 PM', status: 'completed' },
            { title: 'Work Completed', date: 'October 20, 2025 - 3:30 PM', status: 'completed' },
            { title: 'Payment Received', date: 'October 20, 2025 - 4:00 PM', status: 'completed' }
        ],
        primaryAction: 'Download Invoice'
    },
    5: {
        title: 'Bathroom Plumbing Fix',
        status: 'Paid',
        statusClass: 'paid',
        amount: 'LKR 1,800',
        customerName: 'Kamala Silva',
        customerPhone: '+94 71 444 3333',
        customerEmail: 'kamala.silva@email.com',
        location: 'Dehiwala, Western Province',
        started: 'October 14, 2025',
        completion: 'October 16, 2025',
        jobId: '#JOB-2025-005',
        category: 'Plumbing',
        description: 'Leaky bathroom faucet and slow draining sink. Replaced faulty washers and cleared pipe blockage. All fixtures now working properly.',
        serviceCharge: 'LKR 1,600',
        platformFee: 'LKR 240',
        tax: 'LKR 80',
        totalAmount: 'LKR 1,800',
        earnings: 'LKR 1,360',
        timeline: [
            { title: 'Job Accepted', date: 'October 14, 2025 - 10:00 AM', status: 'completed' },
            { title: 'Work Started', date: 'October 14, 2025 - 2:30 PM', status: 'completed' },
            { title: 'Work Completed', date: 'October 16, 2025 - 11:00 AM', status: 'completed' },
            { title: 'Payment Received', date: 'October 16, 2025 - 11:30 AM', status: 'completed' }
        ],
        primaryAction: 'Download Invoice'
    },
    6: {
        title: 'Electrical Wiring Repair',
        status: 'Cancelled',
        statusClass: 'cancelled',
        amount: 'LKR 3,200',
        customerName: 'Rajith Kumar',
        customerPhone: '+94 77 666 7777',
        customerEmail: 'rajith.k@email.com',
        location: 'Moratuwa, Western Province',
        started: 'N/A',
        completion: 'N/A',
        jobId: '#JOB-2025-006',
        category: 'Electrical',
        description: 'Faulty wiring in bedroom causing power outages. Job was cancelled by customer before work could begin. Reason: Customer requested another repairer.',
        serviceCharge: 'LKR 3,000',
        platformFee: 'LKR 450',
        tax: 'LKR 150',
        totalAmount: 'LKR 3,200',
        earnings: 'LKR 0',
        timeline: [
            { title: 'Job Accepted', date: 'October 19, 2025 - 9:00 AM', status: 'completed' },
            { title: 'Job Cancelled', date: 'October 21, 2025 - 10:30 AM', status: 'cancelled' }
        ],
        primaryAction: 'Close'
    }
};

function viewJobDetails(jobId) {
    
    // Get job data
    const job = jobsData[jobId];
    
    if (!job) {
        showNotification('Job details not found', 'error');
        return;
    }
    
    // Update modal content
    document.getElementById('modal-job-title').textContent = job.title;
    document.getElementById('modal-job-status').innerHTML = `<i class="fas fa-${job.statusClass === 'active' ? 'tools' : job.statusClass === 'paid' ? 'check-circle' : 'times-circle'}"></i> ${job.status}`;
    document.getElementById('modal-job-status').className = `job-detail-status ${job.statusClass}`;
    document.getElementById('modal-job-amount').textContent = job.amount;
    
    document.getElementById('modal-customer-name').textContent = job.customerName;
    document.getElementById('modal-customer-phone').textContent = job.customerPhone;
    document.getElementById('modal-customer-email').textContent = job.customerEmail;
    document.getElementById('modal-job-location').textContent = job.location;
    
    document.getElementById('modal-job-started').textContent = job.started;
    document.getElementById('modal-job-completion').textContent = job.completion;
    document.getElementById('modal-job-id').textContent = job.jobId;
    document.getElementById('modal-job-category').textContent = job.category;
    
    document.getElementById('modal-job-description').textContent = job.description;
    
    document.getElementById('modal-service-charge').textContent = job.serviceCharge;
    document.getElementById('modal-platform-fee').textContent = job.platformFee;
    document.getElementById('modal-tax').textContent = job.tax;
    document.getElementById('modal-total-amount').textContent = job.totalAmount;
    document.getElementById('modal-earnings').textContent = job.earnings;
    
    // Update timeline
    const timelineContainer = document.getElementById('modal-timeline');
    timelineContainer.innerHTML = job.timeline.map(item => `
        <div class="timeline-item ${item.status}">
            <div class="timeline-icon">
                <i class="fas fa-${item.status === 'completed' ? 'check' : item.status === 'active' ? 'tools' : item.status === 'cancelled' ? 'times' : 'clock'}"></i>
            </div>
            <div class="timeline-content">
                <div class="timeline-title">${item.title}</div>
                <div class="timeline-date">${item.date}</div>
            </div>
        </div>
    `).join('');
    
    // Update primary action button
    const primaryActionBtn = document.getElementById('modal-primary-action');
    primaryActionBtn.textContent = job.primaryAction;
    primaryActionBtn.onclick = () => {
        if (job.primaryAction === 'Mark as Complete') {
            showNotification('Job marked as complete!', 'success');
            closeJobDetailsModal();
        } else if (job.primaryAction === 'Download Invoice') {
            downloadInvoice(jobId);
        } else {
            closeJobDetailsModal();
        }
    };
    
    // Show modal
    const modal = document.getElementById('jobDetailsModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeJobDetailsModal() {
    const modal = document.getElementById('jobDetailsModal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// Close modal on overlay click
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('jobDetailsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeJobDetailsModal();
            }
        });
    }
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('jobDetailsModal');
            if (modal && modal.classList.contains('show')) {
                closeJobDetailsModal();
            }
        }
    });
});

function downloadInvoice(jobId) {
    
    showNotification('Generating invoice...', 'info');
    
    // Simulate invoice generation and download
    setTimeout(() => {
        showNotification('Invoice downloaded successfully!', 'success');
        
        // In a real application, this would trigger an actual file download
        
    }, 1500);
}

function loadMoreJobs() {
    
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

// ===== CALENDAR FUNCTIONALITY =====
let currentDate = new Date();
let currentMonth = currentDate.getMonth();
let currentYear = currentDate.getFullYear();

// Sample events data - dates with scheduled jobs
const jobEvents = [
    { date: '2025-10-25', title: 'Client Consultation', time: '2:00 PM', type: 'consultation' },
    { date: '2025-10-27', title: 'Team Meeting', time: '10:00 AM', type: 'meeting' },
    { date: '2025-10-28', title: 'Equipment Maintenance', time: '9:00 AM', type: 'maintenance' },
    { date: '2025-10-29', title: 'Kitchen Sink Repair', time: '11:00 AM', type: 'job' },
    { date: '2025-10-30', title: 'Ceiling Fan Installation', time: '2:00 PM', type: 'job' }
];

// Initialize calendar
function initializeCalendar() {
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar();
        });
        
        nextBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar();
        });
        
        renderCalendar();
        renderUpcomingEvents();
    }
}

function renderCalendar() {
    const calendarDays = document.getElementById('calendar-days');
    const monthYearDisplay = document.getElementById('current-month-year');
    
    if (!calendarDays || !monthYearDisplay) return;
    
    // Clear previous days
    calendarDays.innerHTML = '';
    
    // Update month/year display
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    monthYearDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    // Get today's date for comparison
    const today = new Date();
    const todayDate = today.getDate();
    const todayMonth = today.getMonth();
    const todayYear = today.getFullYear();
    
    // Add previous month's days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const dayElement = createDayElement(day, 'prev-month');
        calendarDays.appendChild(dayElement);
    }
    
    // Add current month's days
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = createDayElement(day, 'current-month');
        
        // Check if it's today
        if (day === todayDate && currentMonth === todayMonth && currentYear === todayYear) {
            dayElement.classList.add('today');
        }
        
        // Check if day has events
        const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const hasEvent = jobEvents.some(event => event.date === dateString);
        if (hasEvent) {
            dayElement.classList.add('has-event');
        }
        
        calendarDays.appendChild(dayElement);
    }
    
    // Add next month's days to fill grid
    const totalCells = calendarDays.children.length;
    const remainingCells = 42 - totalCells; // 6 rows * 7 days
    for (let day = 1; day <= remainingCells; day++) {
        const dayElement = createDayElement(day, 'next-month');
        calendarDays.appendChild(dayElement);
    }
}

function createDayElement(day, monthClass) {
    const dayElement = document.createElement('div');
    dayElement.className = `calendar-day ${monthClass}`;
    dayElement.textContent = day;
    return dayElement;
}

function renderUpcomingEvents() {
    const eventsList = document.getElementById('upcoming-events-list');
    
    if (!eventsList) return;
    
    // Clear previous events
    eventsList.innerHTML = '';
    
    // Filter and sort upcoming events
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const upcomingEvents = jobEvents
        .filter(event => {
            const eventDate = new Date(event.date);
            return eventDate >= today;
        })
        .sort((a, b) => new Date(a.date) - new Date(b.date))
        .slice(0, 5); // Show only next 5 events
    
    if (upcomingEvents.length === 0) {
        eventsList.innerHTML = '<div class="no-events-message">No upcoming events</div>';
        return;
    }
    
    upcomingEvents.forEach(event => {
        const eventItem = document.createElement('div');
        eventItem.className = 'event-item';
        
        // Format date
        const eventDate = new Date(event.date);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                           'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const dateStr = `${monthNames[eventDate.getMonth()]} ${eventDate.getDate()}`;
        
        eventItem.innerHTML = `
            <div class="event-date-badge">${dateStr}</div>
            <div class="event-details">
                <div class="event-title">${event.title}</div>
                <div class="event-time">
                    <i class="fas fa-clock"></i>
                    ${event.time}
                </div>
            </div>
        `;
        
        eventsList.appendChild(eventItem);
    });
}

// Initialize calendar when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCalendar);
} else {
    initializeCalendar();
}
