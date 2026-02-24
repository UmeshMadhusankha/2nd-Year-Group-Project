// ================================================
// JOB HISTORY PAGE - FILTERING, SEARCH, AND REVIEW
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================================
    // SAMPLE JOB DATA
    // ================================================
    let allJobs = [
        {
            id: 1,
            title: "Fix Kitchen Sink Leak",
            category: "Plumbing",
            status: "completed",
            description: "Need urgent repair for a leaking kitchen sink. Water is dripping from the pipe connection under the sink.",
            budget: "5000",
            applicants: 8,
            postedDate: "2025-01-10",
            provider: {
                name: "Kasun Silva",
                avatar: "https://via.placeholder.com/50"
            },
            paymentDone: true,
            reviewed: false
        },
        {
            id: 2,
            title: "Electrical Wiring Installation",
            category: "Electrical",
            status: "assigned",
            description: "Need to install new electrical wiring for the living room. Includes outlets and light fixtures.",
            budget: "15000",
            applicants: 5,
            postedDate: "2025-01-12",
            provider: {
                name: "Nimal Perera",
                avatar: "https://via.placeholder.com/50"
            },
            paymentDone: false,
            reviewed: false
        },
        {
            id: 3,
            title: "Deep Cleaning Service",
            category: "Cleaning",
            status: "open",
            description: "Looking for professional deep cleaning service for a 3-bedroom house. All rooms, kitchen, and bathrooms.",
            budget: "8000",
            applicants: 12,
            postedDate: "2025-01-15",
            provider: null,
            paymentDone: false,
            reviewed: false
        },
        {
            id: 4,
            title: "AC Unit Maintenance",
            category: "HVAC",
            status: "completed",
            description: "Regular maintenance required for split AC unit. Cleaning and gas refilling if needed.",
            budget: "3500",
            applicants: 6,
            postedDate: "2025-01-05",
            provider: {
                name: "Amal Fernando",
                avatar: "https://via.placeholder.com/50"
            },
            paymentDone: true,
            reviewed: true
        },
        {
            id: 5,
            title: "Bathroom Tile Replacement",
            category: "Plumbing",
            status: "open",
            description: "Need to replace cracked tiles in the bathroom. Approximately 20 tiles need replacement.",
            budget: "12000",
            applicants: 4,
            postedDate: "2025-01-16",
            provider: null,
            paymentDone: false,
            reviewed: false
        },
        {
            id: 6,
            title: "Ceiling Fan Installation",
            category: "Electrical",
            status: "completed",
            description: "Install two ceiling fans in bedrooms. Fans will be provided, need installation service only.",
            budget: "2500",
            applicants: 7,
            postedDate: "2025-01-08",
            provider: {
                name: "Sunil Jayawardena",
                avatar: "https://via.placeholder.com/50"
            },
            paymentDone: true,
            reviewed: false
        }
    ];

    // Additional jobs for "Load More" functionality
    const additionalJobs = [
        {
            id: 7,
            title: "Refrigerator Repair",
            category: "HVAC",
            status: "open",
            description: "Refrigerator not cooling properly. Need immediate repair.",
            budget: "4000",
            applicants: 3,
            postedDate: "2025-01-17",
            provider: null,
            paymentDone: false,
            reviewed: false
        },
        {
            id: 8,
            title: "Garden Landscaping",
            category: "Other",
            status: "assigned",
            description: "Complete garden makeover with new plants and landscaping design.",
            budget: "25000",
            applicants: 5,
            postedDate: "2025-01-14",
            provider: {
                name: "Chaminda Rathnayake",
                avatar: "https://via.placeholder.com/50"
            },
            paymentDone: false,
            reviewed: false
        }
    ];

    // ================================================
    // DOM ELEMENTS
    // ================================================
    const filterTabs = document.querySelectorAll('.filter-tab');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    const jobsContainer = document.getElementById('jobsContainer');
    const emptyState = document.getElementById('emptyState');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const loadMoreContainer = document.getElementById('loadMoreContainer');
    
    // Modal elements
    const reviewModal = document.getElementById('reviewModal');
    const modalClose = document.getElementById('modalClose');
    const cancelReview = document.getElementById('cancelReview');
    const reviewForm = document.getElementById('reviewForm');
    const starRating = document.getElementById('starRating');
    const stars = starRating.querySelectorAll('.star');
    const ratingText = document.getElementById('ratingText');
    const reviewText = document.getElementById('reviewText');
    const submitReview = document.getElementById('submitReview');
    const reviewCharCounter = document.getElementById('reviewCharCounter');
    const providerInfo = document.getElementById('providerInfo');
    
    // Toast
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    // Profile dropdown
    const profileAvatar = document.getElementById('profileAvatar');
    const profileDropdown = document.getElementById('profileDropdown');
    
    // Mobile menu
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    // ================================================
    // STATE
    // ================================================
    let currentFilter = 'all';
    let currentSearchTerm = '';
    let selectedRating = 0;
    let currentJobForReview = null;
    let displayedJobsCount = allJobs.length;

    // ================================================
    // INITIALIZE
    // ================================================
    function init() {
        renderJobs();
        updateCounts();
        setupEventListeners();
        checkForCompletionNotification();
    }

    // ================================================
    // RENDER JOBS
    // ================================================
    function renderJobs() {
        const filteredJobs = getFilteredJobs();
        
        if (filteredJobs.length === 0) {
            jobsContainer.style.display = 'none';
            loadMoreContainer.style.display = 'none';
            emptyState.style.display = 'block';
        } else {
            jobsContainer.style.display = 'grid';
            emptyState.style.display = 'none';
            
            jobsContainer.innerHTML = filteredJobs.map(job => createJobCard(job)).join('');
            
            // Show/hide load more button
            if (displayedJobsCount >= allJobs.length + additionalJobs.length) {
                loadMoreContainer.style.display = 'none';
            } else {
                loadMoreContainer.style.display = 'block';
            }
        }
    }

    // ================================================
    // CREATE JOB CARD
    // ================================================
    function createJobCard(job) {
        const statusClass = job.status.toLowerCase();
        const canReview = job.status === 'completed' && job.paymentDone && !job.reviewed;
        
        return `
            <div class="job-card" data-job-id="${job.id}" data-status="${job.status}">
                <div class="job-card-header">
                    <div>
                        <h3 class="job-title">${job.title}</h3>
                        <p class="job-date">Posted on ${formatDate(job.postedDate)}</p>
                    </div>
                    <div class="job-badges">
                        <span class="job-badge badge-category">${job.category}</span>
                        <span class="job-badge badge-status ${statusClass}">${capitalizeFirst(job.status)}</span>
                    </div>
                </div>
                
                <p class="job-description">${job.description}</p>
                
                <div class="job-details">
                    <div class="job-budget">
                        <span class="budget-amount">LKR ${formatNumber(job.budget)}</span>
                        <span class="budget-label">Budget</span>
                    </div>
                    <div class="job-applicants">
                        <span class="applicants-count">${job.applicants}</span>
                        <span class="applicants-label">${job.status === 'open' ? 'Applicants' : 'Total Applied'}</span>
                    </div>
                </div>
                
                <div class="job-actions">
                    <button class="action-btn btn-icon" onclick="editJob(${job.id})" title="Edit Job">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn btn-secondary" onclick="viewJobDetails(${job.id})">
                        <i class="fas fa-eye"></i>
                        View Details
                    </button>
                    ${canReview ? `
                        <button class="action-btn btn-review" onclick="openReviewModal(${job.id})">
                            <i class="fas fa-star"></i>
                            Leave Review
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }

    // ================================================
    // FILTER JOBS
    // ================================================
    function getFilteredJobs() {
        let filtered = allJobs;
        
        // Filter by status
        if (currentFilter !== 'all') {
            filtered = filtered.filter(job => job.status === currentFilter);
        }
        
        // Filter by search term
        if (currentSearchTerm) {
            filtered = filtered.filter(job => 
                job.title.toLowerCase().includes(currentSearchTerm.toLowerCase()) ||
                job.description.toLowerCase().includes(currentSearchTerm.toLowerCase())
            );
        }
        
        // Sort by date (newest first)
        filtered.sort((a, b) => new Date(b.postedDate) - new Date(a.postedDate));
        
        return filtered;
    }

    // ================================================
    // UPDATE COUNTS
    // ================================================
    function updateCounts() {
        document.getElementById('allCount').textContent = allJobs.length;
        document.getElementById('openCount').textContent = allJobs.filter(j => j.status === 'open').length;
        document.getElementById('assignedCount').textContent = allJobs.filter(j => j.status === 'assigned').length;
        document.getElementById('completedCount').textContent = allJobs.filter(j => j.status === 'completed').length;
    }

    // ================================================
    // EVENT LISTENERS
    // ================================================
    function setupEventListeners() {
        // Filter tabs
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderJobs();
            });
        });

        // Search
        searchInput.addEventListener('input', function() {
            currentSearchTerm = this.value;
            searchClear.style.display = this.value ? 'block' : 'none';
            renderJobs();
        });

        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            currentSearchTerm = '';
            this.style.display = 'none';
            renderJobs();
        });

        // Load more
        loadMoreBtn.addEventListener('click', function() {
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner"></i> Loading...';
            
            setTimeout(() => {
                allJobs = [...allJobs, ...additionalJobs];
                displayedJobsCount = allJobs.length;
                renderJobs();
                updateCounts();
                this.classList.remove('loading');
                this.innerHTML = '<i class="fas fa-plus"></i> Load More Jobs';
            }, 1000);
        });

        // Modal close
        modalClose.addEventListener('click', closeReviewModal);
        cancelReview.addEventListener('click', closeReviewModal);
        
        reviewModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });

        // Star rating
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                updateStarRating();
                validateReviewForm();
            });

            star.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    this.click();
                }
            });
        });

        // Review textarea
        reviewText.addEventListener('input', function() {
            const length = this.value.length;
            reviewCharCounter.textContent = `${length}/500 characters`;
            validateReviewForm();
        });

        // Review form submit
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitReviewHandler();
        });

        // Profile dropdown
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });

        // Mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileMenu.classList.toggle('show');
            });
        }
    }

    // ================================================
    // REVIEW MODAL FUNCTIONS
    // ================================================
    window.openReviewModal = function(jobId) {
        const job = allJobs.find(j => j.id === jobId);
        if (!job || !job.provider) return;

        currentJobForReview = job;
        selectedRating = 0;
        reviewText.value = '';
        reviewCharCounter.textContent = '0/500 characters';
        
        // Populate provider info
        providerInfo.innerHTML = `
            <div class="provider-avatar">
                <img src="${job.provider.avatar}" alt="${job.provider.name}">
            </div>
            <div class="provider-details">
                <h4>${job.provider.name}</h4>
                <p>Service Provider for "${job.title}"</p>
            </div>
        `;
        
        updateStarRating();
        reviewModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    function closeReviewModal() {
        reviewModal.classList.remove('show');
        document.body.style.overflow = '';
        currentJobForReview = null;
    }

    function updateStarRating() {
        stars.forEach((star, index) => {
            const rating = index + 1;
            if (rating <= selectedRating) {
                star.classList.add('active');
                star.querySelector('i').className = 'fas fa-star';
                star.setAttribute('aria-checked', 'true');
            } else {
                star.classList.remove('active');
                star.querySelector('i').className = 'far fa-star';
                star.setAttribute('aria-checked', 'false');
            }
        });

        if (selectedRating === 0) {
            ratingText.textContent = 'Click to rate';
        } else {
            const ratingLabels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
            ratingText.textContent = ratingLabels[selectedRating - 1];
        }
    }

    function validateReviewForm() {
        const isValid = selectedRating > 0 && reviewText.value.trim().length > 0;
        submitReview.disabled = !isValid;
    }

    function submitReviewHandler() {
        if (!currentJobForReview) return;

        const reviewData = {
            jobId: currentJobForReview.id,
            rating: selectedRating,
            comment: reviewText.value.trim(),
            provider: currentJobForReview.provider.name
        };

        console.log('Review submitted:', reviewData);

        // Update job as reviewed
        const jobIndex = allJobs.findIndex(j => j.id === currentJobForReview.id);
        if (jobIndex !== -1) {
            allJobs[jobIndex].reviewed = true;
        }

        closeReviewModal();
        renderJobs();
        showToast('Thank you! Your review has been submitted.');
    }

    // ================================================
    // GLOBAL FUNCTIONS (for onclick handlers)
    // ================================================
    window.editJob = function(jobId) {
        console.log('Edit job:', jobId);
        // Redirect to edit page or open edit modal
        alert(`Edit functionality for job ${jobId} - Redirect to edit page`);
    };

    window.viewJobDetails = function(jobId) {
        console.log('View job details:', jobId);
        // Redirect to job details page
        alert(`View details for job ${jobId} - Redirect to details page`);
    };

    // ================================================
    // TOAST NOTIFICATION
    // ================================================
    function showToast(message) {
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // ================================================
    // CHECK FOR COMPLETION NOTIFICATION
    // ================================================
    function checkForCompletionNotification() {
        // Check if redirected from job completion
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('completed') === 'true') {
            setTimeout(() => {
                showToast('Please leave a review for your completed job.');
            }, 500);
        }
    }

    // ================================================
    // UTILITY FUNCTIONS
    // ================================================
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    function formatNumber(num) {
        return parseInt(num).toLocaleString();
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ================================================
    // INITIALIZE APP
    // ================================================
    init();
});

// ================================================
// MOBILE MENU ANIMATIONS
// ================================================
const style = document.createElement('style');
style.textContent = `
    .mobile-menu-toggle.active .hamburger:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }
    
    .mobile-menu-toggle.active .hamburger:nth-child(2) {
        opacity: 0;
    }
    
    .mobile-menu-toggle.active .hamburger:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }
`;
document.head.appendChild(style);
