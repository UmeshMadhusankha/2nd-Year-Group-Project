/**
 * Available Jobs Page JavaScript
 * Handles job filtering, application, and pagination
 */

// Global variables
let currentRepairerId = 1; // TODO: Get from session
let availableJobs = [];
let submittedQuotes = [];

document.addEventListener('DOMContentLoaded', function() {
    initializeJobsPage();
    initializeFilters();
    initializeTabs();
    loadAvailableJobs();
    loadSubmittedQuotations();
});

/**
 * Initialize tabs functionality
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            switchTab(tabName);
        });
    });
}

/**
 * Switch between tabs
 */
function switchTab(tabName) {
    // Remove active class from all buttons and contents
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Add active class to selected tab
    const selectedButton = document.querySelector(`[data-tab="${tabName}"]`);
    const selectedContent = document.getElementById(`${tabName}-tab`);
    
    if (selectedButton) selectedButton.classList.add('active');
    if (selectedContent) selectedContent.classList.add('active');
    
    // Load quotations when switching to that tab
    if (tabName === 'submitted-quotes') {
        loadSubmittedQuotations();
    }
}

/**
 * Initialize the jobs page functionality
 */
function initializeJobsPage() {
    console.log('Available Jobs page loaded');
    
    // Add smooth scroll behavior for better UX
    document.documentElement.style.scrollBehavior = 'smooth';
}

/**
 * Load available jobs from API
 */
async function loadAvailableJobs(filters = {}) {
    const container = document.getElementById('jobs-grid-container');
    
    // Show loading state
    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading available jobs...</p>
        </div>
    `;
    
    try {
        // Build query parameters
        const params = new URLSearchParams();
        if (filters.category) params.append('category', filters.category);
        if (filters.district) params.append('district', filters.district);
        if (filters.sort) params.append('sort', filters.sort);
        params.append('service_provider_type', 'individual'); // Only show jobs for individual repairers
        
        const apiUrl = `/2nd-Year-Group-Project/FixLanka/api/job-requests.php?${params.toString()}`;
        console.log('Fetching jobs from:', apiUrl);
        
        const response = await fetch(apiUrl);
        console.log('Response status:', response.status);
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('API Response:', result);
        
        if (result.success) {
            availableJobs = result.data;
            renderJobs(availableJobs);
            updateJobCounts(result.count);
        } else {
            console.error('API returned error:', result.error);
            showError('Failed to load jobs: ' + (result.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error loading jobs:', error);
        showError('Failed to load jobs. Error: ' + error.message);
    }
}

/**
 * Render jobs to the grid
 */
function renderJobs(jobs) {
    const container = document.getElementById('jobs-grid-container');
    
    if (jobs.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No jobs available</h3>
                <p>There are no job requests matching your criteria at the moment.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = jobs.map(job => createJobCard(job)).join('');
}

/**
 * Create HTML for a job card
 */
function createJobCard(job) {
    const categoryClass = getCategoryClass(job.category_name);
    const urgencyClass = job.urgency === 'urgent' ? 'high' : 'low';
    const urgencyIcon = job.urgency === 'urgent' ? 'fa-exclamation-circle' : 'fa-info-circle';
    const urgencyText = job.urgency === 'urgent' ? 'High Priority' : 'Low Priority';
    
    return `
        <div class="job-card" data-job-id="${job.request_id}">
            <div class="job-header">
                <div class="job-category-badge ${categoryClass}">
                    <i class="${getCategoryIcon(job.category_name)}"></i>
                    ${job.category_name}
                </div>
                <div class="job-posted">
                    <i class="fas fa-clock"></i>
                    ${job.posted_ago}
                </div>
            </div>
            
            <div class="job-content">
                <h3 class="job-title">${escapeHtml(job.title)}</h3>
                <div class="job-customer">
                    <i class="fas fa-user"></i>
                    <span>${escapeHtml(job.customer_name)}</span>
                </div>
                <div class="job-address">
                    <i class="fas fa-location-dot"></i>
                    <span>${escapeHtml(job.address)}</span>
                </div>
                <div class="job-urgency ${urgencyClass}">
                    <i class="fas ${urgencyIcon}"></i>
                    <span>${urgencyText}</span>
                </div>
                <div class="job-date">
                    <i class="fas fa-calendar"></i>
                    <span>${formatDate(job.finish_date)}</span>
                </div>
            </div>

            <div class="job-actions">
                <button class="btn btn-secondary job-btn" onclick="viewJobDetails(${job.request_id})">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                <button class="btn btn-primary job-btn" onclick="submitQuote(${job.request_id})">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Submit Quote
                </button>
            </div>
        </div>
    `;
}

/**
 * Update job counts in header and tabs
 */
function updateJobCounts(totalCount) {
    // Update header stats
    const newJobsCount = availableJobs.filter(job => {
        const hoursAgo = (new Date() - new Date(job.dateCreated)) / (1000 * 60 * 60);
        return hoursAgo < 24;
    }).length;
    
    document.getElementById('new-jobs-count').textContent = newJobsCount;
    document.getElementById('total-jobs-count').textContent = totalCount;
    document.getElementById('available-jobs-badge').textContent = totalCount;
    
    // Update section subtitle
    document.getElementById('jobs-count').textContent = `${totalCount} job${totalCount !== 1 ? 's' : ''} available`;
}

/**
 * Initialize filter functionality
 */
function initializeFilters() {
    const applyFiltersBtn = document.querySelector('.btn-filter.btn-primary');
    const resetFiltersBtn = document.querySelector('.btn-filter.btn-secondary');
    
    // Apply filters button
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
    
    // Reset filters button
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', resetFilters);
    }
}

/**
 * Apply filters to job listings
 */
function applyFilters() {
    const categoryFilter = document.getElementById('category-filter');
    const locationFilter = document.getElementById('location-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    const filters = {
        category: categoryFilter ? categoryFilter.value : '',
        district: locationFilter ? locationFilter.value : '',
        sort: sortFilter ? sortFilter.value : 'newest'
    };
    
    console.log('Applying filters:', filters);
    loadAvailableJobs(filters);
}

/**
 * Reset all filters
 */
function resetFilters() {
    const categoryFilter = document.getElementById('category-filter');
    const locationFilter = document.getElementById('location-filter');
    const sortFilter = document.getElementById('sort-filter');
    
    if (categoryFilter) categoryFilter.value = '';
    if (locationFilter) locationFilter.value = '';
    if (sortFilter) sortFilter.value = 'newest';
    
    console.log('Filters reset');
    loadAvailableJobs();
}

/**
 * View job details
 */
function viewJobDetails(jobId) {
    console.log(`Viewing details for job ID: ${jobId}`);
    openJobDetailsDrawer(jobId);
}

/**
 * Open job details drawer
 */
async function openJobDetailsDrawer(jobId) {
    const job = availableJobs.find(j => j.request_id == jobId);
    if (!job) return;
    
    const drawer = document.getElementById('jobDetailsDrawer');
    
    // Populate drawer with job details
    const categoryClass = getCategoryClass(job.category_name);
    const urgencyClass = job.urgency === 'urgent' ? 'high' : 'low';
    
    document.getElementById('detailCategory').innerHTML = `
        <i class="${getCategoryIcon(job.category_name)}"></i>
        <span>${job.category_name}</span>
    `;
    document.getElementById('detailCategory').className = `job-detail-category ${categoryClass}`;
    
    document.getElementById('detailUrgency').innerHTML = `
        <i class="fas ${job.urgency === 'urgent' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
        <span>${job.urgency === 'urgent' ? 'High Priority' : 'Low Priority'}</span>
    `;
    document.getElementById('detailUrgency').className = `job-detail-urgency ${urgencyClass}`;
    
    document.getElementById('detailTitle').textContent = job.title;
    document.getElementById('detailCustomerName').textContent = job.customer_name;
    document.getElementById('detailPosted').textContent = job.posted_ago;
    document.getElementById('detailAddress').textContent = job.address;
    document.getElementById('detailSchedule').textContent = formatDate(job.finish_date);
    document.getElementById('detailDescription').textContent = job.description;
    
    // Handle attachments
    const attachmentsContainer = document.getElementById('detailAttachments');
    if (job.photos && job.photos.length > 0) {
        attachmentsContainer.innerHTML = job.photos.map(photo => `
            <div class="attachment-item">
                <i class="fas fa-image"></i>
                <span>${photo}</span>
            </div>
        `).join('');
    } else {
        attachmentsContainer.innerHTML = '<p class="text-muted">No attachments</p>';
    }
    
    // Store current job ID for submit quote button
    drawer.dataset.currentJobId = jobId;
    
    // Show drawer
    drawer.classList.add('open');
}

/**
 * Close job details drawer
 */
function closeJobDetails() {
    const drawer = document.getElementById('jobDetailsDrawer');
    drawer.classList.remove('open');
}

/**
 * Submit quote from details drawer
 */
function submitQuoteFromDetails() {
    const drawer = document.getElementById('jobDetailsDrawer');
    const jobId = drawer.dataset.currentJobId;
    if (jobId) {
        submitQuote(jobId);
    }
}

/**
 * Submit quote for a job
 */
function submitQuote(jobId) {
    console.log(`Submitting quote for job ID: ${jobId}`);
    
    // Navigate to submit quote page with job ID using absolute path
    window.location.href = `/2nd-Year-Group-Project/FixLanka/views/repairer/pages/submit-quote.php?jobId=${jobId}`;
}

/**
 * Load submitted quotations
 */
async function loadSubmittedQuotations() {
    const container = document.getElementById('submitted-quotes-container');
    const countBadge = document.getElementById('quotes-count-badge');
    const countText = document.getElementById('quotes-count');
    
    // Show loading state
    container.innerHTML = `
        <div class="loading-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading your quotations...</p>
        </div>
    `;
    
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?repairer_id=${currentRepairerId}`);
        const result = await response.json();
        
        if (result.success) {
            submittedQuotes = result.data;
            renderQuotations(submittedQuotes);
            
            // Update counts
            countBadge.textContent = result.count;
            countText.textContent = `${result.count} quotation${result.count !== 1 ? 's' : ''} submitted`;
        } else {
            showError('Failed to load quotations: ' + result.error);
        }
    } catch (error) {
        console.error('Error loading quotations:', error);
        showError('Failed to load quotations. Please try again later.');
    }
}

/**
 * Render quotations to container
 */
function renderQuotations(quotes) {
    const container = document.getElementById('submitted-quotes-container');
    
    if (quotes.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-invoice"></i>
                <h3>No quotations submitted</h3>
                <p>You haven't submitted any quotations yet. Browse available jobs and submit your quotes!</p>
            </div>
        `;
        return;
    }
    
    // Group quotes by status
    const pending = quotes.filter(q => q.status === 'pending');
    const accepted = quotes.filter(q => q.status === 'accepted');
    const rejected = quotes.filter(q => q.status === 'rejected');
    const expired = quotes.filter(q => q.status === 'expired');
    
    let html = '';
    
    if (pending.length > 0) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-clock"></i> Pending Quotations</h3>`;
        html += pending.map(q => createQuoteCard(q)).join('');
    }
    
    if (accepted.length > 0) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-check-circle"></i> Accepted Quotations</h3>`;
        html += accepted.map(q => createQuoteCard(q)).join('');
    }
    
    if (rejected.length > 0) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-times-circle"></i> Rejected Quotations</h3>`;
        html += rejected.map(q => createQuoteCard(q)).join('');
    }
    
    if (expired.length > 0) {
        html += `<h3 class="quotes-section-title"><i class="fas fa-hourglass-end"></i> Expired Quotations</h3>`;
        html += expired.map(q => createQuoteCard(q)).join('');
    }
    
    container.innerHTML = html;
}

/**
 * Create HTML for a quotation card
 */
function createQuoteCard(quote) {
    const statusClass = getQuoteStatusClass(quote.status);
    const statusIcon = getQuoteStatusIcon(quote.status);
    const canEdit = quote.status === 'pending';
    
    return `
        <div class="quote-card ${statusClass}" data-quote-id="${quote.quote_id}">
            <div class="quote-header">
                <div class="quote-job-info">
                    <h4 class="quote-job-title">${escapeHtml(quote.job_title || 'Job Request')}</h4>
                    <p class="quote-job-meta">
                        <i class="fas fa-calendar"></i> ${formatDate(quote.job_posted_date)}
                        <span class="separator">•</span>
                        <i class="fas fa-map-marker-alt"></i> ${escapeHtml(quote.district || 'N/A')}
                    </p>
                </div>
                <div class="quote-status-badge ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                    ${quote.status.charAt(0).toUpperCase() + quote.status.slice(1)}
                </div>
            </div>
            
            <div class="quote-body">
                <div class="quote-details-grid">
                    <div class="quote-detail-item">
                        <label>Quote Amount</label>
                        <span class="quote-amount">LKR ${parseFloat(quote.quoteAmount).toLocaleString()}</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Estimated Days</label>
                        <span>${quote.estimatedDays || 'N/A'} days</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Warranty Period</label>
                        <span>${quote.warrantyPeriod || 0} months</span>
                    </div>
                    <div class="quote-detail-item">
                        <label>Valid Until</label>
                        <span>${formatDate(quote.validUntil)}</span>
                    </div>
                </div>
                
                ${quote.message ? `
                    <div class="quote-message">
                        <label><i class="fas fa-comment"></i> Your Message</label>
                        <p>${escapeHtml(quote.message)}</p>
                    </div>
                ` : ''}
                
                <div class="quote-meta">
                    <span><i class="fas fa-clock"></i> Submitted ${formatDate(quote.dateSubmitted)}</span>
                </div>
            </div>
            
            ${canEdit ? `
                <div class="quote-actions">
                    <button class="btn btn-secondary btn-sm" onclick="editQuote(${quote.quote_id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteQuote(${quote.quote_id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            ` : ''}
        </div>
    `;
}

/**
 * Edit a quotation
 */
function editQuote(quoteId) {
    console.log(`Editing quote ID: ${quoteId}`);
    window.location.href = `/2nd-Year-Group-Project/FixLanka/views/repairer/pages/edit-quote.php?quoteId=${quoteId}`;
}

/**
 * Delete a quotation
 */
async function deleteQuote(quoteId) {
    if (!confirm('Are you sure you want to delete this quotation? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?quote_id=${quoteId}&repairer_id=${currentRepairerId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Quotation deleted successfully!', 'success');
            loadSubmittedQuotations(); // Reload quotations
        } else {
            showError('Failed to delete quotation: ' + result.error);
        }
    } catch (error) {
        console.error('Error deleting quotation:', error);
        showError('Failed to delete quotation. Please try again later.');
    }
}

// Utility Functions

function getCategoryClass(categoryName) {
    if (!categoryName) return 'general';
    const name = categoryName.toLowerCase();
    if (name.includes('plumb')) return 'plumbing';
    if (name.includes('electric')) return 'electrical';
    if (name.includes('appliance')) return 'appliance';
    if (name.includes('hvac') || name.includes('air')) return 'hvac';
    if (name.includes('carpent') || name.includes('wood')) return 'carpentry';
    if (name.includes('paint')) return 'painting';
    return 'general';
}

function getCategoryIcon(categoryName) {
    if (!categoryName) return 'fas fa-tools';
    const name = categoryName.toLowerCase();
    if (name.includes('plumb')) return 'fas fa-wrench';
    if (name.includes('electric')) return 'fas fa-bolt';
    if (name.includes('appliance')) return 'fas fa-tv';
    if (name.includes('hvac') || name.includes('air')) return 'fas fa-snowflake';
    if (name.includes('carpent') || name.includes('wood')) return 'fas fa-hammer';
    if (name.includes('paint')) return 'fas fa-paint-brush';
    return 'fas fa-tools';
}

function getQuoteStatusClass(status) {
    const classes = {
        'pending': 'status-pending',
        'accepted': 'status-accepted',
        'rejected': 'status-rejected',
        'expired': 'status-expired'
    };
    return classes[status] || 'status-pending';
}

function getQuoteStatusIcon(status) {
    const icons = {
        'pending': 'fa-clock',
        'accepted': 'fa-check-circle',
        'rejected': 'fa-times-circle',
        'expired': 'fa-hourglass-end'
    };
    return icons[status] || 'fa-question-circle';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    const container = document.getElementById('jobs-grid-container') || document.getElementById('submitted-quotes-container');
    if (container) {
        container.innerHTML = `
            <div class="error-state">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error</h3>
                <p>${escapeHtml(message)}</p>
                <button class="btn btn-primary" onclick="location.reload()">Retry</button>
            </div>
        `;
    }
}

function showToast(message, type = 'info') {
    // Simple toast notification (can be enhanced with a toast library)
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Style toast
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    `;
    
    // Add to document
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Initialize infinite scroll
 */
function initializeInfiniteScroll() {
    let isLoading = false;
    let hasMoreJobs = true;
    let currentPage = 1;
    const loadingIndicator = document.getElementById('loading-indicator');
    const endResults = document.getElementById('end-results');
    
    // Add scroll event listener
    window.addEventListener('scroll', function() {
        if (isLoading || !hasMoreJobs) return;
        
        // Check if user scrolled near bottom
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        // Load more when user is 200px from bottom
        if (scrollTop + windowHeight >= documentHeight - 200) {
            loadMoreJobs();
        }
    });
}

/**
 * Load more jobs via infinite scroll
 */
function loadMoreJobs() {
    if (isLoading || !hasMoreJobs) return;
    
    isLoading = true;
    currentPage++;
    
    // Show loading indicator
    const loadingIndicator = document.getElementById('loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.style.display = 'flex';
    }
    
    console.log(`Loading page ${currentPage}...`);
    
    // Simulate API call delay
    setTimeout(() => {
        const newJobs = generateJobCards(6); // Generate 6 more job cards
        appendJobsToGrid(newJobs);
        
        // Hide loading indicator
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
        
        isLoading = false;
        
        // Simulate reaching end after 5 pages (30 total jobs)
        if (currentPage >= 5) {
            hasMoreJobs = false;
            showEndOfResults();
        }
        
        // Update results count
        const totalJobs = document.querySelectorAll('.job-card').length;
        updateResultsCount(totalJobs);
        
    }, 1500); // Simulate network delay
}

/**
 * Generate new job cards dynamically
 */
function generateJobCards(count) {
    const jobTemplates = [
        {
            category: 'plumbing',
            icon: 'fas fa-wrench',
            title: 'Bathroom Pipe Repair',
            customer: 'Anjali Perera',
            customerType: 'user',
            location: 'Dehiwala, Western Province',
            schedule: 'Sept 8, 1:00 PM - 3:00 PM',
            budget: 'LKR 1,800 - 2,400',
            posted: '3 hours ago'
        },
        {
            category: 'electrical',
            icon: 'fas fa-bolt',
            title: 'Socket Installation',
            customer: 'Tech Solutions Ltd',
            customerType: 'building',
            location: 'Maharagama, Western Province',
            schedule: 'Sept 9, 10:00 AM - 12:00 PM',
            budget: 'LKR 1,200 - 1,800',
            posted: '5 hours ago'
        },
        {
            category: 'appliance',
            icon: 'fas fa-tv',
            title: 'Refrigerator Repair',
            customer: 'Sunil Fernando',
            customerType: 'user',
            location: 'Panadura, Western Province',
            schedule: 'Sept 10, 2:00 PM - 4:00 PM',
            budget: 'LKR 2,500 - 3,200',
            posted: '1 day ago'
        },
        {
            category: 'hvac',
            icon: 'fas fa-snowflake',
            title: 'AC Maintenance',
            customer: 'Green Hotel',
            customerType: 'building',
            location: 'Negombo, Western Province',
            schedule: 'Sept 11, 8:00 AM - 11:00 AM',
            budget: 'LKR 4,500 - 6,000',
            posted: '1 day ago'
        },
        {
            category: 'carpentry',
            icon: 'fas fa-hammer',
            title: 'Door Frame Repair',
            customer: 'Malini Silva',
            customerType: 'user',
            location: 'Kelaniya, Western Province',
            schedule: 'Sept 12, 9:00 AM - 12:00 PM',
            budget: 'LKR 2,800 - 3,500',
            posted: '2 days ago'
        },
        {
            category: 'painting',
            icon: 'fas fa-paint-brush',
            title: 'Exterior Wall Painting',
            customer: 'Rainbow Apartments',
            customerType: 'building',
            location: 'Moratuwa, Western Province',
            schedule: 'Sept 13-15, 8:00 AM - 5:00 PM',
            budget: 'LKR 15,000 - 20,000',
            posted: '3 days ago'
        }
    ];
    
    const jobs = [];
    for (let i = 0; i < count; i++) {
        const template = jobTemplates[i % jobTemplates.length];
        const jobId = Date.now() + i; // Generate unique ID
        
        jobs.push({
            ...template,
            id: jobId,
            title: `${template.title} #${jobId.toString().slice(-3)}` // Add unique suffix
        });
    }
    
    return jobs;
}

/**
 * Append new job cards to the grid
 */
function appendJobsToGrid(jobs) {
    const jobsGrid = document.querySelector('.jobs-grid');
    if (!jobsGrid) return;
    
    jobs.forEach((job, index) => {
        const jobCard = createJobCardElement(job);
        
        // Add animation delay
        jobCard.style.opacity = '0';
        jobCard.style.transform = 'translateY(20px)';
        
        jobsGrid.appendChild(jobCard);
        
        // Animate in
        setTimeout(() => {
            jobCard.style.transition = 'all 0.5s ease';
            jobCard.style.opacity = '1';
            jobCard.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Create a job card element
 */
function createJobCardElement(job) {
    const jobCard = document.createElement('div');
    jobCard.className = 'job-card';
    
    jobCard.innerHTML = `
        <div class="job-header">
            <div class="job-category-badge ${job.category}">
                <i class="${job.icon}"></i>
                ${job.category.charAt(0).toUpperCase() + job.category.slice(1)}
            </div>
            <div class="job-posted">
                <i class="fas fa-clock"></i>
                ${job.posted}
            </div>
        </div>
        
        <div class="job-content">
            <h3 class="job-title">${job.title}</h3>
            <div class="job-customer">
                <i class="fas fa-${job.customerType}"></i>
                <span>${job.customer}</span>
            </div>
            <div class="job-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>${job.location}</span>
            </div>
            <div class="job-schedule">
                <i class="fas fa-calendar"></i>
                <span>${job.schedule}</span>
            </div>
            <div class="job-budget">
                <i class="fas fa-money-bill"></i>
                <span class="budget-amount">${job.budget}</span>
            </div>
        </div>

        <div class="job-actions">
            <button class="btn btn-secondary job-btn" onclick="viewJobDetails(${job.id})">
                <i class="fas fa-eye"></i>
                View Details
            </button>
            <button class="btn btn-primary job-btn" onclick="applyForJob(${job.id})">
                <i class="fas fa-paper-plane"></i>
                Apply Now
            </button>
        </div>
    `;
    
    return jobCard;
}

/**
 * Show end of results message
 */
function showEndOfResults() {
    const endResults = document.getElementById('end-results');
    if (endResults) {
        endResults.style.display = 'flex';
    }
}

/**
 * Refresh jobs (reload page)
 */
function refreshJobs() {
    showToast('Refreshing job listings...', 'info');
    setTimeout(() => {
        location.reload();
    }, 1000);
}

/**
 * Animate job cards on page load
 */
function animateJobCards() {
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Show toast message (using the common function)
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.textContent = message;
    
    const colors = {
        success: 'var(--success-color)',
        warning: 'var(--warning-color)',
        error: 'var(--danger-color)',
        info: 'var(--info-color)'
    };
    
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || colors.success};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        z-index: 10000;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

/**
 * Search functionality for jobs
 */
function searchJobs(query) {
    const jobCards = document.querySelectorAll('.job-card');
    const searchQuery = query.toLowerCase();
    let visibleCount = 0;
    
    jobCards.forEach(card => {
        const title = card.querySelector('.job-title').textContent.toLowerCase();
        const customer = card.querySelector('.job-customer span').textContent.toLowerCase();
        const location = card.querySelector('.job-location span').textContent.toLowerCase();
        const category = card.querySelector('.job-category-badge').textContent.toLowerCase();
        
        const matches = title.includes(searchQuery) || 
                       customer.includes(searchQuery) || 
                       location.includes(searchQuery) || 
                       category.includes(searchQuery);
        
        if (matches) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultsCount(visibleCount);
}

/**
 * Mock job data for detailed view
 */
const jobDetailsData = {
    1: {
        title: "Kitchen Sink Repair",
        category: "Plumbing",
        priority: "high",
        customerName: "Sarah Fernando",
        posted: "2 hours ago",
        address: "No. 45, Galle Road, Colombo 07, Western Province",
        schedule: "Tomorrow, 2:00 PM - 4:00 PM",
        description: "The kitchen sink is leaking from the pipe connection underneath. Water is dripping constantly and has created a puddle. The sink was installed about 5 years ago. Need urgent repair to prevent water damage to the cabinet.",
        additionalInfo: [
            "Customer will provide necessary materials",
            "Parking available on premises",
            "Customer prefers afternoon appointments"
        ],
        attachments: [
            { name: "sink-leak.jpg", icon: "fa-image" },
            { name: "pipe-close-up.jpg", icon: "fa-image" }
        ]
    },
    2: {
        title: "Ceiling Fan Installation",
        category: "Electrical",
        priority: "low",
        customerName: "ABC Trading Company",
        posted: "4 hours ago",
        address: "123, Peradeniya Road, Kandy, Central Province",
        schedule: "Sept 3, 9:00 AM - 12:00 PM",
        description: "Need to install a new ceiling fan in the office conference room. Fan and all mounting hardware provided. Requires proper wiring and balancing. Must ensure fan is properly secured as it's a large room with high ceilings.",
        additionalInfo: [
            "All materials provided by company",
            "Access available during business hours",
            "Prior electrical work certificate required"
        ],
        attachments: []
    },
    3: {
        title: "Washing Machine Repair",
        category: "Appliance",
        priority: "high",
        customerName: "Nimal Perera",
        posted: "6 hours ago",
        address: "78, High Level Road, Nugegoda, Western Province",
        schedule: "Sept 4, 3:00 PM - 5:00 PM",
        description: "Washing machine not spinning properly and making loud noise during wash cycle. Machine is 3 years old, Samsung model. Need diagnostic and repair as soon as possible.",
        additionalInfo: [
            "Machine still under extended warranty",
            "Original purchase receipt available",
            "Home occupied during working hours"
        ],
        attachments: [
            { name: "machine-issue.mp4", icon: "fa-video" }
        ]
    },
    4: {
        title: "Air Conditioner Service",
        category: "HVAC",
        priority: "low",
        customerName: "Kamala Silva",
        posted: "1 day ago",
        address: "56, Yakkala Road, Gampaha, Western Province",
        schedule: "Sept 5, 10:00 AM - 1:00 PM",
        description: "Annual servicing required for 2 split AC units. Units need cleaning, gas refill check, and general maintenance. Both units are Daikin brand, installed 2 years ago.",
        additionalInfo: [
            "Regular customer, annual service",
            "Both units easily accessible",
            "Payment on completion"
        ],
        attachments: [
            { name: "ac-units.jpg", icon: "fa-image" }
        ]
    },
    5: {
        title: "Cabinet Door Repair",
        category: "Carpentry",
        priority: "low",
        customerName: "Rajesh Kumar",
        posted: "1 day ago",
        address: "34, Beach Road, Mount Lavinia, Western Province",
        schedule: "Sept 6, 8:00 AM - 11:00 AM",
        description: "Kitchen cabinet door hinge is broken and needs replacement. Door is hanging at an angle. Need experienced carpenter to fix or replace hinge and ensure door closes properly.",
        additionalInfo: [
            "Customer can provide hinge if needed",
            "Other cabinets may need inspection",
            "Morning time slot preferred"
        ],
        attachments: [
            { name: "broken-hinge.jpg", icon: "fa-image" },
            { name: "cabinet-door.jpg", icon: "fa-image" }
        ]
    },
    6: {
        title: "Room Wall Painting",
        category: "Painting",
        priority: "high",
        customerName: "Priya Wickramasinghe",
        posted: "2 days ago",
        address: "89, Colombo Road, Kurunegala, North Western Province",
        schedule: "Sept 7-8, 9:00 AM - 5:00 PM",
        description: "Need to paint one bedroom (12x12 ft). Walls need preparation, one coat of primer and two coats of paint. Color to be selected. Professional finish required as it's for rental property.",
        additionalInfo: [
            "Paint to be purchased by painter",
            "Room is empty, furniture removed",
            "Budget discussed before work"
        ],
        attachments: [
            { name: "room-photos.jpg", icon: "fa-image" },
            { name: "wall-condition.jpg", icon: "fa-image" }
        ]
    }
};

/**
 * Open job details drawer
 */
function viewJobDetails(jobId) {
    const drawer = document.getElementById('jobDetailsDrawer');
    const jobData = jobDetailsData[jobId];
    
    if (!jobData) {
        showToast('Job details not available', 'error');
        return;
    }
    
    // Populate drawer with job data
    document.getElementById('detailCategory').innerHTML = `
        <i class="fas fa-${getCategoryIcon(jobData.category)}"></i>
        <span>${jobData.category}</span>
    `;
    
    const urgencyElement = document.getElementById('detailUrgency');
    urgencyElement.className = `job-detail-urgency ${jobData.priority}`;
    urgencyElement.innerHTML = `
        <i class="fas fa-${jobData.priority === 'high' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${jobData.priority === 'high' ? 'High Priority' : 'Low Priority'}</span>
    `;
    
    document.getElementById('detailTitle').textContent = jobData.title;
    document.getElementById('detailCustomerName').textContent = jobData.customerName;
    document.getElementById('detailPosted').textContent = jobData.posted;
    document.getElementById('detailAddress').textContent = jobData.address;
    document.getElementById('detailSchedule').textContent = jobData.schedule;
    document.getElementById('detailDescription').textContent = jobData.description;
    
    // Populate additional info
    const infoContainer = document.querySelector('.detail-list');
    infoContainer.innerHTML = jobData.additionalInfo.map(info => `
        <div class="detail-list-item">
            <i class="fas fa-check-circle"></i>
            <span>${info}</span>
        </div>
    `).join('');
    
    // Populate attachments
    const attachmentsContainer = document.getElementById('detailAttachments');
    if (jobData.attachments && jobData.attachments.length > 0) {
        attachmentsContainer.innerHTML = jobData.attachments.map(attachment => `
            <div class="attachment-item">
                <i class="fas ${attachment.icon}"></i>
                <span>${attachment.name}</span>
            </div>
        `).join('');
    } else {
        attachmentsContainer.innerHTML = '<p style="color: var(--text-secondary);">No attachments available</p>';
    }
    
    // Show drawer
    drawer.classList.add('active');
    document.body.style.overflow = 'hidden';
}

/**
 * Close job details drawer
 */
function closeJobDetails() {
    const drawer = document.getElementById('jobDetailsDrawer');
    drawer.classList.remove('active');
    document.body.style.overflow = 'auto';
}

/**
 * Submit quote from details drawer
 */
function submitQuoteFromDetails() {
    closeJobDetails();
    const jobId = document.getElementById('jobDetailsDrawer').dataset.jobId || '1';
    // Navigate to submit quote page with absolute path
    window.location.href = `/2nd-Year-Group-Project/FixLanka/views/repairer/pages/submit-quote.php?jobId=${jobId}`;
}

/**
 * Get category icon
 */
function getCategoryIcon(category) {
    const icons = {
        'Plumbing': 'wrench',
        'Electrical': 'bolt',
        'Appliance': 'tv',
        'HVAC': 'snowflake',
        'Carpentry': 'hammer',
        'Painting': 'paint-brush'
    };
    return icons[category] || 'tools';
}

// ===== SUBMITTED QUOTATIONS MANAGEMENT =====

/**
 * Load submitted quotations for the current repairer
 */
function loadSubmittedQuotations() {
    const container = document.getElementById('submitted-quotes-container');
    const quotesCount = document.getElementById('quotes-count');
    const quotesCountBadge = document.getElementById('quotes-count-badge');
    
    // Get repairer ID (in real app, this would come from session)
    const repairerId = 1; // Dummy repairer ID
    
    // Fetch quotations from API
    fetch(`/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?repairer_id=${repairerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data && data.data.length > 0) {
                displayQuotations(data.data, container);
                quotesCount.textContent = `${data.data.length} quotation${data.data.length !== 1 ? 's' : ''} submitted`;
                if (quotesCountBadge) {
                    quotesCountBadge.textContent = data.data.length;
                }
            } else {
                displayEmptyState(container);
                quotesCount.textContent = 'No quotations yet';
                if (quotesCountBadge) {
                    quotesCountBadge.textContent = '0';
                }
            }
        })
        .catch(error => {
            console.error('Error loading quotations:', error);
            displayErrorState(container);
            quotesCount.textContent = 'Error loading quotations';
            if (quotesCountBadge) {
                quotesCountBadge.textContent = '!';
            }
        });
}

/**
 * Display quotations in the container
 */
function displayQuotations(quotations, container) {
    container.innerHTML = quotations.map(quote => createQuoteCard(quote)).join('');
}

/**
 * Create a quotation card HTML
 */
function createQuoteCard(quote) {
    const statusClass = quote.status.toLowerCase();
    const canEdit = quote.status === 'pending';
    
    // Format dates
    const submittedDate = new Date(quote.dateSubmitted);
    const validUntilDate = new Date(quote.validUntil);
    const formattedSubmitted = submittedDate.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
    const formattedValidUntil = validUntilDate.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
    
    // Format warranty
    let warrantyText = 'No warranty';
    if (quote.warrantyPeriod > 0) {
        if (quote.warrantyPeriod === 12) {
            warrantyText = '1 year';
        } else if (quote.warrantyPeriod === 24) {
            warrantyText = '2 years';
        } else {
            warrantyText = `${quote.warrantyPeriod} month${quote.warrantyPeriod !== 1 ? 's' : ''}`;
        }
    }
    
    return `
        <div class="quote-card" data-quote-id="${quote.quote_id}">
            <!-- Quote Header -->
            <div class="quote-header">
                <div class="quote-job-info">
                    <h3 class="quote-job-title">Job Request #${quote.request_id}</h3>
                    <div class="quote-request-id">
                        <i class="fas fa-hashtag"></i>
                        Quote ID: ${quote.quote_id}
                    </div>
                </div>
                <span class="quote-status ${statusClass}">${quote.status}</span>
            </div>
            
            <!-- Quote Details -->
            <div class="quote-details">
                <div class="quote-detail-item">
                    <span class="quote-detail-label">Quote Amount</span>
                    <span class="quote-detail-value amount">
                        <i class="fas fa-rupee-sign"></i>
                        Rs. ${parseFloat(quote.quoteAmount).toFixed(2)}
                    </span>
                </div>
                
                <div class="quote-detail-item">
                    <span class="quote-detail-label">Estimated Duration</span>
                    <span class="quote-detail-value">
                        <i class="fas fa-clock"></i>
                        ${quote.estimatedDays} day${quote.estimatedDays !== 1 ? 's' : ''}
                    </span>
                </div>
                
                <div class="quote-detail-item">
                    <span class="quote-detail-label">Warranty Period</span>
                    <span class="quote-detail-value">
                        <i class="fas fa-shield-alt"></i>
                        ${warrantyText}
                    </span>
                </div>
                
                <div class="quote-detail-item">
                    <span class="quote-detail-label">Valid Until</span>
                    <span class="quote-detail-value">
                        <i class="fas fa-calendar-check"></i>
                        ${formattedValidUntil}
                    </span>
                </div>
            </div>
            
            <!-- Quote Message -->
            ${quote.message ? `
            <div class="quote-message">
                <span class="quote-message-label">Quote Details</span>
                <p class="quote-message-text">${quote.message}</p>
            </div>
            ` : ''}
            
            <!-- Quote Metadata -->
            <div class="quote-metadata">
                <div class="quote-metadata-item">
                    <i class="fas fa-calendar"></i>
                    Submitted: ${formattedSubmitted}
                </div>
                <div class="quote-metadata-item">
                    <i class="fas fa-box"></i>
                    Materials: ${quote.materialsIncluded ? 'Included' : 'Not Included'}
                </div>
            </div>
            
            <!-- Quote Actions -->
            <div class="quote-actions">
                <button class="btn btn-view" onclick="viewQuoteDetails(${quote.quote_id})">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                <button class="btn btn-edit" ${!canEdit ? 'disabled' : ''} 
                        onclick="editQuote(${quote.quote_id})" 
                        ${!canEdit ? `title="Can only edit pending quotations"` : ''}>
                    <i class="fas fa-edit"></i>
                    ${canEdit ? 'Edit' : 'Cannot Edit'}
                </button>
                ${canEdit ? `
                <button class="btn btn-delete" onclick="deleteQuote(${quote.quote_id})">
                    <i class="fas fa-trash"></i>
                    Delete
                </button>
                ` : ''}
            </div>
        </div>
    `;
}

/**
 * Display empty state when no quotations exist
 */
function displayEmptyState(container) {
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-file-invoice"></i>
            <p>You haven't submitted any quotations yet.</p>
            <p style="font-size: 0.875rem; margin-top: 0.5rem;">
                Browse available jobs below and submit your first quote!
            </p>
        </div>
    `;
}

/**
 * Display error state when loading fails
 */
function displayErrorState(container) {
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-circle" style="color: var(--error-color, #dc3545);"></i>
            <p>Failed to load quotations.</p>
            <button class="btn btn-primary" onclick="loadSubmittedQuotations()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Retry
            </button>
        </div>
    `;
}

/**
 * View quotation details
 */
function viewQuoteDetails(quoteId) {
    console.log('Viewing quote details:', quoteId);
    // Navigate to quote details page or open modal
    showToast(`Opening details for quote #${quoteId}`, 'info');
}

/**
 * Edit quotation (only for pending status)
 */
function editQuote(quoteId) {
    console.log('Editing quote:', quoteId);
    // Navigate to edit quote page with quote data pre-filled
    window.location.href = `/2nd-Year-Group-Project/FixLanka/views/repairer/pages/edit-quote.php?quoteId=${quoteId}`;
}

/**
 * Delete quotation
 */
function deleteQuote(quoteId) {
    console.log('Delete quote called with quoteId:', quoteId);
    console.log('currentRepairerId:', currentRepairerId);
    
    if (!confirm('Are you sure you want to delete this quotation? This action cannot be undone.')) {
        return;
    }
    
    // Show loading toast
    showToast('Deleting quotation...', 'info');
    
    // Construct delete URL with both quote_id and repairer_id
    const deleteUrl = `/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?quote_id=${quoteId}&repairer_id=${currentRepairerId}`;
    console.log('DELETE URL:', deleteUrl);
    
    // Delete via API
    fetch(deleteUrl, {
        method: 'DELETE'
    })
    .then(response => {
        console.log('DELETE Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('DELETE Response data:', data);
        
        if (data.success) {
            showToast('Quotation deleted successfully!', 'success');
            // Reload quotations
            loadSubmittedQuotations();
        } else {
            showToast('Failed to delete quotation: ' + (data.error || 'Unknown error'), 'error');
            console.error('Delete failed:', data);
        }
    })
    .catch(error => {
        console.error('Error deleting quotation:', error);
        showToast('Failed to delete quotation', 'error');
    });
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
    
    // Style toast
    toast.style.cssText = `
        position: fixed;
        top: 80px;
        right: 20px;
        background: ${type === 'success' ? '#4caf50' : type === 'error' ? '#f44336' : '#2196f3'};
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.3s ease;
    `;
    
    // Add to document
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export functions for global use
window.viewJobDetails = viewJobDetails;
window.closeJobDetails = closeJobDetails;
window.submitQuote = submitQuote;
window.submitQuoteFromDetails = submitQuoteFromDetails;
window.applyForJob = applyForJob;
window.searchJobs = searchJobs;
window.refreshJobs = refreshJobs;
window.loadSubmittedQuotations = loadSubmittedQuotations;
window.viewQuoteDetails = viewQuoteDetails;
window.editQuote = editQuote;
window.deleteQuote = deleteQuote;
