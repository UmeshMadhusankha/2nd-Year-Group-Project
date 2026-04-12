// ================================================
// JOB HISTORY PAGE - FILTERING, SEARCH, AND REVIEW
// ================================================

// 1. DECLARE VARIABLES AT THE TOP (Global Scope)
// This ensures global handlers (for inline onclick) can access these references.
let filterTabs;
let searchInput;
let searchClear;
let jobsContainer;
let emptyState;
let loadMoreBtn;
let loadMoreContainer;
let reviewModal;
let modalClose;
let cancelReview;
let reviewForm;
let starRating;
let stars = [];
let ratingText;
let reviewText;
let submitReview;
let reviewCharCounter;
let providerInfo;
let toast;
let toastMessage;
let profileAvatar;
let profileDropdown;
let mobileMenuToggle;
let mobileMenu;

// ================================================
// STATE
// ================================================
let currentFilter = 'all';
let currentSearchTerm = '';
let selectedRating = 0;
let currentJobForReview = null;
let displayedJobsCount = 0;

// ================================================
// INITIALIZE
// ================================================
function init() {
    if (!Array.isArray(window.allJobs)) {
        window.allJobs = [];
    }
    if (!Array.isArray(window.additionalJobs)) {
        window.additionalJobs = [];
    }

    displayedJobsCount = window.allJobs.length;

    renderJobs();
    updateCounts();
    setupEventListeners();
    checkForCompletionNotification();
}

// ================================================
// RENDER JOBS
// ================================================
function renderJobs() {
    if (!jobsContainer || !emptyState || !loadMoreContainer) return;

    const filteredJobs = getFilteredJobs();

    if (filteredJobs.length === 0) {
        jobsContainer.style.display = 'none';
        loadMoreContainer.style.display = 'none';
        emptyState.style.display = 'block';
    } else {
        jobsContainer.style.display = 'grid';
        emptyState.style.display = 'none';

        jobsContainer.innerHTML = filteredJobs.map((job) => createJobCard(job)).join('');

        // Show/hide load more button
        if (displayedJobsCount >= window.allJobs.length + window.additionalJobs.length) {
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
    const statusClass = String(job.status || '').toLowerCase();
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
    let filtered = window.allJobs;

    // Filter by status
    if (currentFilter !== 'all') {
        filtered = filtered.filter((job) => job.status === currentFilter);
    }

    // Filter by search term
    if (currentSearchTerm) {
        const lowered = currentSearchTerm.toLowerCase();
        filtered = filtered.filter(
            (job) =>
                String(job.title || '').toLowerCase().includes(lowered) ||
                String(job.description || '').toLowerCase().includes(lowered)
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
    const allCount = document.getElementById('allCount');
    const openCount = document.getElementById('openCount');
    const assignedCount = document.getElementById('assignedCount');
    const completedCount = document.getElementById('completedCount');

    if (allCount) allCount.textContent = window.allJobs.length;
    if (openCount) openCount.textContent = window.allJobs.filter((j) => j.status === 'open').length;
    if (assignedCount) assignedCount.textContent = window.allJobs.filter((j) => j.status === 'assigned').length;
    if (completedCount) completedCount.textContent = window.allJobs.filter((j) => j.status === 'completed').length;
}

// ================================================
// EVENT LISTENERS
// ================================================
function setupEventListeners() {
    // Filter tabs
    if (filterTabs && filterTabs.length) {
        filterTabs.forEach((tab) => {
            tab.addEventListener('click', function() {
                filterTabs.forEach((t) => t.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                renderJobs();
            });
        });
    }

    // Search
    if (searchInput && searchClear) {
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
    }

    // Load more
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            this.classList.add('loading');
            this.innerHTML = '<i class="fas fa-spinner"></i> Loading...';

            setTimeout(() => {
                window.allJobs = [...window.allJobs, ...window.additionalJobs];
                displayedJobsCount = window.allJobs.length;
                renderJobs();
                updateCounts();
                this.classList.remove('loading');
                this.innerHTML = '<i class="fas fa-plus"></i> Load More Jobs';
            }, 1000);
        });
    }

    // Modal close
    if (modalClose) modalClose.addEventListener('click', closeReviewModal);
    if (cancelReview) cancelReview.addEventListener('click', closeReviewModal);

    if (reviewModal) {
        reviewModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeReviewModal();
            }
        });
    }

    // Star rating
    if (stars && stars.length) {
        stars.forEach((star) => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating, 10);
                updateStarRating();
                validateReviewForm();
            });

            star.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    this.click();
                }
            });
        });
    }

    // Review textarea
    if (reviewText && reviewCharCounter) {
        reviewText.addEventListener('input', function() {
            const length = this.value.length;
            reviewCharCounter.textContent = `${length}/500 characters`;
            validateReviewForm();
        });
    }

    // Review form submit
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitReviewHandler();
        });
    }

    // Profile dropdown
    if (profileAvatar && profileDropdown) {
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
    }

    // Mobile menu
    if (mobileMenuToggle && mobileMenu) {
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
    const job = window.allJobs.find((j) => j.id === jobId);
    if (!job || !job.provider || !reviewModal || !providerInfo || !reviewText || !reviewCharCounter) return;

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
    if (reviewModal) reviewModal.classList.remove('show');
    document.body.style.overflow = '';
    currentJobForReview = null;
}

function updateStarRating() {
    if (!stars || !stars.length || !ratingText) return;

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
    if (!submitReview || !reviewText) return;
    const isValid = selectedRating > 0 && reviewText.value.trim().length > 0;
    submitReview.disabled = !isValid;
}

function submitReviewHandler() {
    if (!currentJobForReview) return;

    const reviewData = {
        jobId: currentJobForReview.id,
        rating: selectedRating,
        comment: reviewText ? reviewText.value.trim() : '',
        provider: currentJobForReview.provider.name
    };

    // Update job as reviewed (local state only for now)
    const jobIndex = window.allJobs.findIndex((j) => j.id === currentJobForReview.id);
    if (jobIndex !== -1) {
        window.allJobs[jobIndex].reviewed = true;
    }

    closeReviewModal();
    renderJobs();
    showToast('Thank you! Your review has been submitted.');
}

// ================================================
// GLOBAL FUNCTIONS (for onclick handlers)
// ================================================
window.editJob = function(jobId) {
    alert(`Edit functionality for job ${jobId} - Redirect to edit page`);
};

window.viewJobDetails = function(jobId) {
    alert(`View details for job ${jobId} - Redirect to details page`);
};

// ================================================
// TOAST NOTIFICATION
// ================================================
function showToast(message) {
    if (!toast || !toastMessage) return;

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
    return parseInt(num, 10).toLocaleString();
}

function capitalizeFirst(str) {
    return String(str || '').charAt(0).toUpperCase() + String(str || '').slice(1);
}

// ================================================
// DOM LOADED: ASSIGN VALUES + START APP
// ================================================
document.addEventListener('DOMContentLoaded', function() {
    // 2. ASSIGN VALUES (Inside the loader)
    filterTabs = document.querySelectorAll('.filter-tab');
    searchInput = document.getElementById('searchInput');
    searchClear = document.getElementById('searchClear');
    jobsContainer = document.getElementById('jobsContainer');
    emptyState = document.getElementById('emptyState');
    loadMoreBtn = document.getElementById('loadMoreBtn');
    loadMoreContainer = document.getElementById('loadMoreContainer');

    // Modal elements
    reviewModal = document.getElementById('reviewModal');
    modalClose = document.getElementById('modalClose');
    cancelReview = document.getElementById('cancelReview');
    reviewForm = document.getElementById('reviewForm');
    starRating = document.getElementById('starRating');

    // Review UI is optional on some pages; only bind stars when present.
    stars = starRating ? starRating.querySelectorAll('.star') : [];

    ratingText = document.getElementById('ratingText');
    reviewText = document.getElementById('reviewText');
    submitReview = document.getElementById('submitReview');
    reviewCharCounter = document.getElementById('reviewCharCounter');
    providerInfo = document.getElementById('providerInfo');

    // Toast & Navigation
    toast = document.getElementById('toast');
    toastMessage = document.getElementById('toastMessage');
    profileAvatar = document.getElementById('profileAvatar');
    profileDropdown = document.getElementById('profileDropdown');
    mobileMenuToggle = document.getElementById('mobileMenuToggle');
    mobileMenu = document.getElementById('mobileMenu');

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
