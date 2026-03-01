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
        
        // Redirect to edit page or open edit modal
        alert(`Edit functionality for job ${jobId} - Redirect to edit page`);
    };

    window.viewJobDetails = function(jobId) {
        
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
 
  
 / /   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  
 / /   Q U O T A T I O N   M O D A L   F U N C T I O N S  
 / /   = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =  
 w i n d o w . v i e w Q u o t e s   =   a s y n c   f u n c t i o n   ( r e q u e s t I d )   {  
         c o n s t   m o d a l   =   d o c u m e n t . g e t E l e m e n t B y I d ( ' q u o t e s M o d a l ' ) ;  
         c o n s t   l i s t   =   d o c u m e n t . g e t E l e m e n t B y I d ( ' q u o t e s L i s t ' ) ;  
  
         i f   ( ! m o d a l   | |   ! l i s t )   {  
                 c o n s o l e . e r r o r ( ' M o d a l   e l e m e n t s   n o t   f o u n d ' ) ;  
                 r e t u r n ;  
         }  
  
         m o d a l . c l a s s L i s t . a d d ( ' s h o w ' ) ;  
         l i s t . i n n e r H T M L   =   ' < d i v   c l a s s = " t e x t - c e n t e r   p - 4 "   s t y l e = " t e x t - a l i g n :   c e n t e r ;   p a d d i n g :   2 0 p x ; " > < i   c l a s s = " f a s   f a - s p i n n e r   f a - s p i n " > < / i >   L o a d i n g   q u o t a t i o n s . . . < / d i v > ' ;  
  
         t r y   {  
                 c o n s t   r e s p o n s e   =   a w a i t   f e t c h ( ` / 2 n d - Y e a r - G r o u p - P r o j e c t / F i x L a n k a / a p i / q u o t a t i o n s . p h p ? a c t i o n = g e t _ b y _ r e q u e s t & r e q u e s t _ i d = $ { r e q u e s t I d } ` ) ;  
                 c o n s t   d a t a   =   a w a i t   r e s p o n s e . j s o n ( ) ;  
  
                 i f   ( d a t a . s u c c e s s )   {  
                         i f   ( d a t a . d a t a . l e n g t h   = = =   0 )   {  
                                 l i s t . i n n e r H T M L   =   ' < d i v   c l a s s = " t e x t - c e n t e r   p - 4 "   s t y l e = " t e x t - a l i g n :   c e n t e r ;   p a d d i n g :   2 0 p x ;   c o l o r :   # 6 6 6 ; " > N o   q u o t a t i o n s   r e c e i v e d   y e t . < / d i v > ' ;  
                                 r e t u r n ;  
                         }  
  
                         / /   G r o u p   q u o t e s   b y   s t a t u s   o r   j u s t   l i s t   t h e m .    
                         / /   A c c e p t e d   q u o t e s   s h o u l d   b e   h i g h l i g h t e d   o r   m a y b e   w e   s h o u l d n ' t   s h o w   t h e m   i f   j o b   i s   a l r e a d y   a s s i g n e d ?  
                         / /   T h e   b u t t o n   i s   o n l y   s h o w n   f o r   ' p e n d i n g '   j o b s ,   s o   u s u a l l y   n o   a c c e p t e d   q u o t e s   y e t .  
  
                         l i s t . i n n e r H T M L   =   d a t a . d a t a . m a p ( q   = >   `  
                                 < d i v   c l a s s = " q u o t e - c a r d "   s t y l e = " b o r d e r :   1 p x   s o l i d   # e e e ;   p a d d i n g :   1 5 p x ;   m a r g i n - b o t t o m :   1 5 p x ;   b o r d e r - r a d i u s :   8 p x ;   b a c k g r o u n d :   # f f f ;   b o x - s h a d o w :   0   2 p x   4 p x   r g b a ( 0 , 0 , 0 , 0 . 0 5 ) ; " >  
                                         < d i v   s t y l e = " d i s p l a y :   f l e x ;   j u s t i f y - c o n t e n t :   s p a c e - b e t w e e n ;   a l i g n - i t e m s :   f l e x - s t a r t ;   m a r g i n - b o t t o m :   1 0 p x ; " >  
                                                 < d i v >  
                                                         < h 3   s t y l e = " m a r g i n :   0 ;   f o n t - s i z e :   1 . 1 e m ;   c o l o r :   v a r ( - - h e a d i n g - c o l o r ) ; " > $ { q . p r o v i d e r _ n a m e } < / h 3 >  
                                                         < d i v   s t y l e = " f o n t - s i z e :   0 . 8 5 e m ;   c o l o r :   # 8 8 8 ; " > $ { n e w   D a t e ( q . c r e a t e d _ a t ) . t o L o c a l e D a t e S t r i n g ( ) } < / d i v >  
                                                 < / d i v >  
                                                 < s p a n   c l a s s = " p r i c e - t a g "   s t y l e = " f o n t - w e i g h t :   b o l d ;   c o l o r :   v a r ( - - p r i m a r y - c o l o r ) ;   f o n t - s i z e :   1 . 2 e m ; " > L K R   $ { p a r s e I n t ( q . t o t a l _ a m o u n t ) . t o L o c a l e S t r i n g ( ) } < / s p a n >  
                                         < / d i v >  
                                          
                                         < p   s t y l e = " m a r g i n :   1 0 p x   0 ;   c o l o r :   # 5 5 5 ;   l i n e - h e i g h t :   1 . 4 ; " > $ { q . d e s c r i p t i o n   | |   ' N o   d e s c r i p t i o n   p r o v i d e d . ' } < / p >  
                                          
                                         < d i v   s t y l e = " d i s p l a y :   f l e x ;   f l e x - w r a p :   w r a p ;   g a p :   1 5 p x ;   m a r g i n :   1 5 p x   0 ;   f o n t - s i z e :   0 . 9 e m ;   c o l o r :   # 6 6 6 ;   b a c k g r o u n d :   # f 8 f 9 f a ;   p a d d i n g :   1 0 p x ;   b o r d e r - r a d i u s :   4 p x ; " >  
                                                 < s p a n > < i   c l a s s = " f a s   f a - c l o c k "   s t y l e = " c o l o r :   v a r ( - - p r i m a r y - c o l o r ) ; " > < / i >   $ { q . e s t i m a t e d _ d u r a t i o n }   D a y s < / s p a n >  
                                                 < s p a n > < i   c l a s s = " f a s   f a - t o o l s "   s t y l e = " c o l o r :   v a r ( - - p r i m a r y - c o l o r ) ; " > < / i >   M a t :   L K R   $ { p a r s e I n t ( q . m a t e r i a l _ c o s t ) . t o L o c a l e S t r i n g ( ) } < / s p a n >  
                                                 < s p a n > < i   c l a s s = " f a s   f a - u s e r - h a r d - h a t "   s t y l e = " c o l o r :   v a r ( - - p r i m a r y - c o l o r ) ; " > < / i >   L a b :   L K R   $ { p a r s e I n t ( q . l a b o r _ c o s t ) . t o L o c a l e S t r i n g ( ) } < / s p a n >  
                                         < / d i v >  
                                          
                                         < d i v   s t y l e = " m a r g i n - t o p :   1 5 p x ;   t e x t - a l i g n :   r i g h t ;   b o r d e r - t o p :   1 p x   s o l i d   # e e e ;   p a d d i n g - t o p :   1 5 p x ; " >  
                                                 $ { q . s t a t u s   = = =   ' p e n d i n g '   ?   `  
                                                         < b u t t o n   o n c l i c k = " a c c e p t Q u o t e ( $ { q . q u o t a t i o n _ i d } ) "   c l a s s = " a c t i o n - b t n   b t n - p r i m a r y "   s t y l e = " p a d d i n g :   8 p x   2 0 p x ;   b o r d e r - r a d i u s :   2 0 p x ; " >  
                                                                 < i   c l a s s = " f a s   f a - c h e c k " > < / i >   A c c e p t   Q u o t e  
                                                         < / b u t t o n >  
                                                 `   :   `  
                                                         < s p a n   c l a s s = " b a d g e   b a d g e - $ { q . s t a t u s } " > $ { q . s t a t u s . t o U p p e r C a s e ( ) } < / s p a n >  
                                                 ` }  
                                         < / d i v >  
                                 < / d i v >  
                         ` ) . j o i n ( ' ' ) ;  
                 }   e l s e   {  
                         c o n s o l e . e r r o r ( ' A P I   E r r o r : ' ,   d a t a . m e s s a g e ) ;  
                         l i s t . i n n e r H T M L   =   ` < d i v   c l a s s = " a l e r t   a l e r t - e r r o r "   s t y l e = " c o l o r :   r e d ;   p a d d i n g :   2 0 p x ;   t e x t - a l i g n :   c e n t e r ; " > E r r o r :   $ { d a t a . m e s s a g e } < / d i v > ` ;  
                 }  
         }   c a t c h   ( e r r o r )   {  
                 c o n s o l e . e r r o r ( ' E r r o r   f e t c h i n g   q u o t e s : ' ,   e r r o r ) ;  
                 l i s t . i n n e r H T M L   =   ' < d i v   c l a s s = " a l e r t   a l e r t - e r r o r "   s t y l e = " c o l o r :   r e d ;   p a d d i n g :   2 0 p x ;   t e x t - a l i g n :   c e n t e r ; " > F a i l e d   t o   l o a d   q u o t a t i o n s .   P l e a s e   t r y   a g a i n . < / d i v > ' ;  
         }  
 } ;  
  
 w i n d o w . c l o s e Q u o t e s M o d a l   =   f u n c t i o n   ( )   {  
         c o n s t   m o d a l   =   d o c u m e n t . g e t E l e m e n t B y I d ( ' q u o t e s M o d a l ' ) ;  
         i f   ( m o d a l )   m o d a l . c l a s s L i s t . r e m o v e ( ' s h o w ' ) ;  
 } ;  
  
 / /   C l o s e   o n   o u t s i d e   c l i c k  
 d o c u m e n t . a d d E v e n t L i s t e n e r ( ' c l i c k ' ,   f u n c t i o n   ( e )   {  
         c o n s t   m o d a l   =   d o c u m e n t . g e t E l e m e n t B y I d ( ' q u o t e s M o d a l ' ) ;  
         i f   ( m o d a l   & &   e . t a r g e t   = = =   m o d a l )   {  
                 c l o s e Q u o t e s M o d a l ( ) ;  
         }  
 } ) ;  
  
 w i n d o w . a c c e p t Q u o t e   =   a s y n c   f u n c t i o n   ( q u o t a t i o n I d )   {  
         i f   ( ! c o n f i r m ( ' A r e   y o u   s u r e   y o u   w a n t   t o   a c c e p t   t h i s   q u o t a t i o n ?   T h i s   w i l l   c r e a t e   a   b i n d i n g   c o n t r a c t   a n d   y o u   w i l l   b e   r e d i r e c t e d   t o   t h e   c o n t r a c t s   p a g e . ' ) )   {  
                 r e t u r n ;  
         }  
  
         / /   S h o w   l o a d i n g   s t a t e   o n   b u t t o n  
         c o n s t   b t n   =   d o c u m e n t . q u e r y S e l e c t o r ( ` b u t t o n [ o n c l i c k = " a c c e p t Q u o t e ( $ { q u o t a t i o n I d } ) " ] ` ) ;  
         c o n s t   o r i g i n a l T e x t   =   b t n   ?   b t n . i n n e r H T M L   :   ' ' ;  
         i f   ( b t n )   {  
                 b t n . i n n e r H T M L   =   ' < i   c l a s s = " f a s   f a - s p i n n e r   f a - s p i n " > < / i >   P r o c e s s i n g . . . ' ;  
                 b t n . d i s a b l e d   =   t r u e ;  
         }  
  
         t r y   {  
                 c o n s t   r e s p o n s e   =   a w a i t   f e t c h ( ' / 2 n d - Y e a r - G r o u p - P r o j e c t / F i x L a n k a / a p i / q u o t a t i o n s . p h p ? a c t i o n = a c c e p t ' ,   {  
                         m e t h o d :   ' P O S T ' ,  
                         h e a d e r s :   {  
                                 ' C o n t e n t - T y p e ' :   ' a p p l i c a t i o n / j s o n '  
                         } ,  
                         b o d y :   J S O N . s t r i n g i f y ( {   q u o t a t i o n _ i d :   q u o t a t i o n I d   } )  
                 } ) ;  
                 c o n s t   d a t a   =   a w a i t   r e s p o n s e . j s o n ( ) ;  
  
                 i f   ( d a t a . s u c c e s s )   {  
                         / /   S h o w   s u c c e s s   m e s s a g e  
                         c o n s t   l i s t   =   d o c u m e n t . g e t E l e m e n t B y I d ( ' q u o t e s L i s t ' ) ;  
                         i f   ( l i s t )   {  
                                 l i s t . i n n e r H T M L   =   `  
                                         < d i v   s t y l e = " t e x t - a l i g n :   c e n t e r ;   p a d d i n g :   4 0 p x ; " >  
                                                 < i   c l a s s = " f a s   f a - c h e c k - c i r c l e "   s t y l e = " f o n t - s i z e :   3 e m ;   c o l o r :   v a r ( - - s u c c e s s - c o l o r ) ;   m a r g i n - b o t t o m :   2 0 p x ; " > < / i >  
                                                 < h 3 > Q u o t a t i o n   A c c e p t e d ! < / h 3 >  
                                                 < p > C o n t r a c t   c r e a t e d   s u c c e s s f u l l y .   R e d i r e c t i n g . . . < / p >  
                                         < / d i v >  
                                 ` ;  
                         }  
  
                         s e t T i m e o u t ( ( )   = >   {  
                                 w i n d o w . l o c a t i o n . h r e f   =   ' / 2 n d - Y e a r - G r o u p - P r o j e c t / F i x L a n k a / m y - c o n t r a c t s ' ;  
                         } ,   1 5 0 0 ) ;  
                 }   e l s e   {  
                         a l e r t ( ' F a i l e d   t o   a c c e p t   q u o t a t i o n :   '   +   d a t a . m e s s a g e ) ;  
                         i f   ( b t n )   {  
                                 b t n . i n n e r H T M L   =   o r i g i n a l T e x t ;  
                                 b t n . d i s a b l e d   =   f a l s e ;  
                         }  
                 }  
         }   c a t c h   ( e r r o r )   {  
                 c o n s o l e . e r r o r ( ' E r r o r   a c c e p t i n g   q u o t e : ' ,   e r r o r ) ;  
                 a l e r t ( ' A n   e r r o r   o c c u r r e d .   P l e a s e   t r y   a g a i n . ' ) ;  
                 i f   ( b t n )   {  
                         b t n . i n n e r H T M L   =   o r i g i n a l T e x t ;  
                         b t n . d i s a b l e d   =   f a l s e ;  
                 }  
         }  
 } ;  
 