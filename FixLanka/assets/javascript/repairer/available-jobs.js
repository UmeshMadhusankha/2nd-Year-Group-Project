/**
 * Available Jobs Page JavaScript
 * Handles job filtering, application, and pagination
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeJobsPage();
    initializeFilters();
    initializeInfiniteScroll();
});

/**
 * Initialize the jobs page functionality
 */
function initializeJobsPage() {
    console.log('Available Jobs page loaded');
    
    // Add smooth scroll behavior for better UX
    document.documentElement.style.scrollBehavior = 'smooth';
    
    // Initialize job card animations
    animateJobCards();
}

/**
 * Initialize filter functionality
 */
function initializeFilters() {
    const categoryFilter = document.getElementById('category-filter');
    const locationFilter = document.getElementById('location-filter');
    const sortFilter = document.getElementById('sort-filter');
    const applyFiltersBtn = document.querySelector('.btn-filter.btn-primary');
    const resetFiltersBtn = document.querySelector('.btn-filter.btn-secondary');
    
    // Add event listeners for filter changes
    if (categoryFilter) {
        categoryFilter.addEventListener('change', handleFilterChange);
    }
    
    if (locationFilter) {
        locationFilter.addEventListener('change', handleFilterChange);
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', handleFilterChange);
    }
    
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
 * Handle filter changes
 */
function handleFilterChange(event) {
    console.log(`Filter changed: ${event.target.id} = ${event.target.value}`);
    // In a real application, this would trigger immediate filtering
    // For now, we'll just log the change
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
        location: locationFilter ? locationFilter.value : '',
        sort: sortFilter ? sortFilter.value : 'newest'
    };
    
    console.log('Applying filters:', filters);
    
    // Show loading state
    showFilteringProgress();
    
    // Simulate API call
    setTimeout(() => {
        filterJobs(filters);
        hideFilteringProgress();
        showToast('Filters applied successfully!', 'success');
    }, 1000);
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
    showToast('Filters reset successfully!', 'info');
    
    // Reload all jobs
    setTimeout(() => {
        location.reload();
    }, 500);
}

/**
 * Filter jobs based on criteria
 */
function filterJobs(filters) {
    const jobCards = document.querySelectorAll('.job-card');
    let visibleCount = 0;
    
    jobCards.forEach(card => {
        let shouldShow = true;
        
        // Category filter
        if (filters.category) {
            const categoryBadge = card.querySelector('.job-category-badge');
            if (categoryBadge && !categoryBadge.classList.contains(filters.category)) {
                shouldShow = false;
            }
        }
        
        // Location filter (simplified - would normally check job location data)
        if (filters.location) {
            const locationText = card.querySelector('.job-location span');
            if (locationText && !locationText.textContent.toLowerCase().includes(filters.location.toLowerCase())) {
                shouldShow = false;
            }
        }
        
        // Show/hide card with animation
        if (shouldShow) {
            card.style.display = 'flex';
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, visibleCount * 100);
            visibleCount++;
        } else {
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
    
    // Update results count
    updateResultsCount(visibleCount);
}

/**
 * Update the results count display
 */
function updateResultsCount(count) {
    const subtitle = document.querySelector('.section-subtitle');
    if (subtitle) {
        subtitle.textContent = `${count} jobs match your criteria`;
    }
}

/**
 * Show filtering progress
 */
function showFilteringProgress() {
    const applyBtn = document.querySelector('.btn-filter.btn-primary');
    if (applyBtn) {
        applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Filtering...';
        applyBtn.disabled = true;
    }
}

/**
 * Hide filtering progress
 */
function hideFilteringProgress() {
    const applyBtn = document.querySelector('.btn-filter.btn-primary');
    if (applyBtn) {
        applyBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
        applyBtn.disabled = false;
    }
}

/**
 * View job details
 */
function viewJobDetails(jobId) {
    console.log(`Viewing details for job ID: ${jobId}`);
    
    // In a real application, this would open a modal or navigate to details page
    showToast(`Opening details for Job #${jobId}`, 'info');
    
    // Simulate navigation to job details
    setTimeout(() => {
        // window.location.href = `job-details.html?id=${jobId}`;
        console.log(`Would navigate to job-details.html?id=${jobId}`);
    }, 1000);
}

/**
 * Apply for a job
 */
function applyForJob(jobId) {
    console.log(`Applying for job ID: ${jobId}`);
    
    // Show confirmation dialog
    if (confirm('Are you sure you want to apply for this job?')) {
        // Show loading state
        const button = event.target.closest('.btn-primary');
        const originalContent = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-check"></i> Applied';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            button.style.background = 'var(--success-color)';
            
            showToast(`Successfully applied for Job #${jobId}!`, 'success');
            
            // Disable further applications
            setTimeout(() => {
                button.disabled = true;
            }, 2000);
        }, 1500);
    }
}

/**
 * Submit quote for a job
 */
function submitQuote(jobId) {
    console.log(`Submitting quote for job ID: ${jobId}`);
    
    // Navigate to submit quote page with job ID
    window.location.href = `submit-quote.php?jobId=${jobId}`;
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
    const jobTitle = document.getElementById('detailTitle').textContent;
    showToast(`Opening quote form for: ${jobTitle}`, 'success');
    // Here you would typically open a quote submission form
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

// Export functions for global use
window.viewJobDetails = viewJobDetails;
window.closeJobDetails = closeJobDetails;
window.submitQuote = submitQuote;
window.submitQuoteFromDetails = submitQuoteFromDetails;
window.applyForJob = applyForJob;
window.searchJobs = searchJobs;
window.refreshJobs = refreshJobs;
