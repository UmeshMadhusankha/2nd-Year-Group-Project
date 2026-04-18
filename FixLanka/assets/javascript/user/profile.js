// ================================================
// PROFILE PAGE - USER PROFILE MANAGEMENT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ================================================
    // SAMPLE USER DATA
    // ================================================
    const userData = {
        fullName: "John Doe",
        username: "johndoe",
        email: "john.doe@example.com",
        phone: "+94 77 123 4567",
        location: "Colombo 07, Sri Lanka",
        bio: "Looking for reliable service providers for home repairs and maintenance.",
        avatar: "https://via.placeholder.com/150",
        accountStatus: "Verified",
        memberSince: "January 2024",
        completionPercentage: 85,
        stats: {
            totalJobs: 12,
            activeJobs: 3,
            completedJobs: 8,
            pendingPayments: 1
        },
        rating: {
            average: 4.5,
            total: 24,
            breakdown: {
                5: 15,
                4: 6,
                3: 2,
                2: 1,
                1: 0
            }
        },
        recentActivity: [
            {
                icon: "fa-briefcase",
                action: "Posted a new job",
                description: "Kitchen Sink Repair",
                time: "2 hours ago"
            },
            {
                icon: "fa-handshake",
                action: "Agreement sent",
                description: "Electrical Wiring Installation",
                time: "1 day ago"
            },
            {
                icon: "fa-credit-card",
                action: "Milestone payment made",
                description: "Office Deep Cleaning - Milestone 1/3",
                time: "2 days ago"
            },
            {
                icon: "fa-star",
                action: "Review received",
                description: "5 stars from Kasun Silva",
                time: "3 days ago"
            }
        ],
        quotes: [
            {
                id: 1,
                jobTitle: "Kitchen Sink Repair",
                provider: {
                    name: "Kasun Silva",
                    type: "Individual",
                    rating: 4.8,
                    avatar: "https://via.placeholder.com/50"
                },
                amount: 5000,
                status: "pending"
            },
            {
                id: 2,
                jobTitle: "Electrical Wiring Installation",
                provider: {
                    name: "Quick Fix Solutions",
                    type: "Company",
                    rating: 4.6,
                    avatar: "https://via.placeholder.com/50"
                },
                amount: 15000,
                status: "pending"
            },
            {
                id: 3,
                jobTitle: "AC Unit Maintenance",
                provider: {
                    name: "Nimal Perera",
                    type: "Individual",
                    rating: 4.9,
                    avatar: "https://via.placeholder.com/50"
                },
                amount: 3500,
                status: "pending"
            }
        ]
    };

    // ================================================
    // DOM ELEMENTS
    // ================================================
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    
    // Profile elements
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editProfileForm = document.getElementById('editProfileForm');
    const profileAvatarImg = document.getElementById('profileAvatar');
    const avatarOverlay = document.getElementById('avatarOverlay');
    const avatarInput = document.getElementById('avatarInput');
    
    // Action buttons
    const manageProfileBtn = document.getElementById('manageProfileBtn');
    const viewQuotesBtn = document.getElementById('viewQuotesBtn');
    const manageReviewsBtn = document.getElementById('manageReviewsBtn');
    const paymentHistoryBtn = document.getElementById('paymentHistoryBtn');
    const viewAllJobsBtn = document.getElementById('viewAllJobsBtn');
    const viewAllActivityBtn = document.getElementById('viewAllActivityBtn');
    const viewAllReviewsBtn = document.getElementById('viewAllReviewsBtn');
    const viewAllQuotesBtn = document.getElementById('viewAllQuotesBtn');
    
    // Toast
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    // ================================================
    // INITIALIZE
    // ================================================
    function init() {
        renderProfileData();
        setupEventListeners();
        loadSidebarState();
        animateCounters();
        animateProgressBars();
    }

    // ================================================
    // RENDER PROFILE DATA
    // ================================================
    function renderProfileData() {
        // User info card
        document.querySelector('.user-full-name').textContent = userData.fullName;
        document.querySelector('.user-username').textContent = `@${userData.username}`;
        document.querySelector('.account-status').textContent = userData.accountStatus;
        document.querySelector('.member-since').textContent = `Member since ${userData.memberSince}`;
        
        if (profileAvatarImg) {
            profileAvatarImg.src = userData.avatar;
        }
        
        // Update all avatar images
        document.querySelectorAll('.user-avatar, .avatar-image').forEach(img => {
            img.src = userData.avatar;
        });

        // Contact info
        const contactItems = document.querySelectorAll('.contact-item span');
        if (contactItems.length >= 3) {
            contactItems[0].textContent = userData.email;
            contactItems[1].textContent = userData.phone;
            contactItems[2].textContent = userData.location;
        }

        // Profile completion
        document.querySelector('.completion-percentage').textContent = `${userData.completionPercentage}%`;
        document.querySelector('.progress-fill').style.width = `${userData.completionPercentage}%`;

        // Stats
        document.querySelector('.stat-number[data-stat="total"]').textContent = userData.stats.totalJobs;
        document.querySelector('.stat-number[data-stat="active"]').textContent = userData.stats.activeJobs;
        document.querySelector('.stat-number[data-stat="completed"]').textContent = userData.stats.completedJobs;
        document.querySelector('.stat-number[data-stat="pending"]').textContent = userData.stats.pendingPayments;

        // Recent activity
        const activityList = document.querySelector('.activity-list');
        if (activityList) {
            activityList.innerHTML = userData.recentActivity.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas ${activity.icon}"></i>
                    </div>
                    <div class="activity-details">
                        <h4 class="activity-action">${activity.action}</h4>
                        <p class="activity-description">${activity.description}</p>
                        <span class="activity-time">${activity.time}</span>
                    </div>
                </div>
            `).join('');
        }

        // Reviews summary
        document.querySelector('.average-rating').textContent = userData.rating.average;
        document.querySelector('.total-reviews').textContent = `${userData.rating.total} reviews`;

        // Rating breakdown
        const ratingBreakdown = document.querySelector('.rating-breakdown');
        if (ratingBreakdown) {
            ratingBreakdown.innerHTML = Object.entries(userData.rating.breakdown)
                .reverse()
                .map(([stars, count]) => {
                    const percentage = (count / userData.rating.total * 100).toFixed(0);
                    return `
                        <div class="rating-row">
                            <span class="rating-label">${stars} <i class="fas fa-star"></i></span>
                            <div class="rating-bar">
                                <div class="bar-fill" style="width: ${percentage}%"></div>
                            </div>
                            <span class="rating-count">${count}</span>
                        </div>
                    `;
                }).join('');
        }

        // Quotes
        const quotesList = document.querySelector('.quotes-list');
        if (quotesList) {
            quotesList.innerHTML = userData.quotes.slice(0, 3).map(quote => `
                <div class="quote-item" data-quote-id="${quote.id}">
                    <div class="quote-header">
                        <div class="provider-info">
                            <img src="${quote.provider.avatar}" alt="${quote.provider.name}" class="provider-avatar">
                            <div>
                                <h4 class="provider-name">${quote.provider.name}</h4>
                                <p class="provider-type">${quote.provider.type}</p>
                                <div class="provider-rating">
                                    <i class="fas fa-star"></i>
                                    <span>${quote.provider.rating}</span>
                                </div>
                            </div>
                        </div>
                        <div class="quote-amount">
                            <span class="amount-label">Quote</span>
                            <span class="amount-value">LKR ${quote.amount.toLocaleString()}</span>
                        </div>
                    </div>
                    <p class="quote-job-title">${quote.jobTitle}</p>
                    <div class="quote-actions">
                        <button class="btn-success" onclick="handleQuoteAction('accept', ${quote.id})">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button class="btn-outline" onclick="handleQuoteAction('decline', ${quote.id})">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Pre-fill edit form
        if (editProfileForm) {
            document.getElementById('editFullName').value = userData.fullName;
            document.getElementById('editUsername').value = userData.username;
            document.getElementById('editEmail').value = userData.email;
            document.getElementById('editPhone').value = userData.phone;
            document.getElementById('editLocation').value = userData.location;
            document.getElementById('editBio').value = userData.bio;
        }
    }

    // ================================================
    // EVENT LISTENERS
    // ================================================
    function setupEventListeners() {
        // Sidebar toggle
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        // Profile dropdown
        if (profileToggle) {
            profileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown?.classList.toggle('show');
            });
        }

        // Mobile menu
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                mobileMenu?.classList.toggle('show');
            });
        }

        // Edit profile modal
        if (editProfileBtn) {
            editProfileBtn.addEventListener('click', openEditModal);
        }
        if (closeEditModal) {
            closeEditModal.addEventListener('click', closeModal);
        }
        if (cancelEditBtn) {
            cancelEditBtn.addEventListener('click', closeModal);
        }
        if (editProfileForm) {
            editProfileForm.addEventListener('submit', handleProfileUpdate);
        }

        // Avatar upload
        if (avatarOverlay) {
            avatarOverlay.addEventListener('click', () => avatarInput?.click());
        }
        if (avatarInput) {
            avatarInput.addEventListener('change', handleAvatarUpload);
        }

        // Action buttons
        const actionButtons = {
            manageProfileBtn: openEditModal,
            viewQuotesBtn: () => navigateToPage('quotes'),
            manageReviewsBtn: () => navigateToPage('reviews'),
            paymentHistoryBtn: () => navigateToPage('payments'),
            viewAllJobsBtn: () => navigateToPage('jobs'),
            viewAllActivityBtn: () => navigateToPage('activity'),
            viewAllReviewsBtn: () => navigateToPage('reviews'),
            viewAllQuotesBtn: () => navigateToPage('quotes')
        };

        Object.entries(actionButtons).forEach(([id, handler]) => {
            const btn = document.getElementById(id);
            if (btn) btn.addEventListener('click', handler);
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (profileDropdown && !profileToggle?.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('show');
            }
            if (mobileMenu && !mobileMenuToggle?.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.remove('show');
                mobileMenuToggle?.classList.remove('active');
            }
        });

        // Modal overlay click
        if (editProfileModal) {
            editProfileModal.addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editProfileModal?.classList.contains('show')) {
                closeModal();
            }
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                openEditModal();
            }
        });

        // Window resize
        window.addEventListener('resize', handleResize);
    }

    // ================================================
    // SIDEBAR FUNCTIONS
    // ================================================
    function toggleSidebar() {
        sidebar?.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar?.classList.contains('collapsed'));
    }

    function loadSidebarState() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed && sidebar) {
            sidebar.classList.add('collapsed');
        }
    }

    // ================================================
    // MODAL FUNCTIONS
    // ================================================
    function openEditModal() {
        editProfileModal?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        editProfileModal?.classList.remove('show');
        document.body.style.overflow = '';
    }

    // ================================================
    // PROFILE UPDATE
    // ================================================
    function handleProfileUpdate(e) {
        e.preventDefault();
        
        const updatedData = {
            fullName: document.getElementById('editFullName').value,
            username: document.getElementById('editUsername').value,
            email: document.getElementById('editEmail').value,
            phone: document.getElementById('editPhone').value,
            location: document.getElementById('editLocation').value,
            bio: document.getElementById('editBio').value
        };

        // Update userData object
        Object.assign(userData, updatedData);

        // Show loading state
        const submitBtn = editProfileForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            renderProfileData();
            closeModal();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showToast('Profile updated successfully!', 'success');
        }, 1500);
    }

    // ================================================
    // AVATAR UPLOAD
    // ================================================
    function handleAvatarUpload(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            // Check file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Image size should be less than 5MB', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                userData.avatar = e.target.result;
                
                // Update all avatar images
                document.querySelectorAll('.user-avatar, .avatar-image, #profileAvatar').forEach(img => {
                    img.src = e.target.result;
                });
                
                showToast('Profile picture updated successfully!', 'success');
            };
            reader.readAsDataURL(file);
        } else {
            showToast('Please select a valid image file', 'error');
        }
    }

    // ================================================
    // NAVIGATION
    // ================================================
    function navigateToPage(page) {
        const routes = {
            'quotes': 'quotes.html',
            'reviews': 'reviews.html',
            'payments': 'payments.html',
            'jobs': 'job-history.html',
            'activity': 'activity.html'
        };

        const url = routes[page];
        if (url) {
            showToast(`Loading ${page}...`, 'info');
            setTimeout(() => {
                window.location.href = url;
            }, 500);
        }
    }

    // ================================================
    // QUOTE ACTIONS (Global function for onclick)
    // ================================================
    window.handleQuoteAction = function(action, quoteId) {
        const quoteItem = document.querySelector(`[data-quote-id="${quoteId}"]`);
        const quote = userData.quotes.find(q => q.id === quoteId);
        
        if (!quoteItem || !quote) return;

        // Animate
        quoteItem.style.transform = 'scale(0.98)';
        quoteItem.style.opacity = '0.7';

        setTimeout(() => {
            quote.status = action === 'accept' ? 'accepted' : 'declined';
            
            if (action === 'accept') {
                quoteItem.style.background = 'rgba(16, 185, 129, 0.1)';
                quoteItem.style.border = '2px solid var(--success-color)';
                showToast(`Quote from ${quote.provider.name} accepted!`, 'success');
            } else {
                quoteItem.style.background = 'rgba(239, 68, 68, 0.1)';
                quoteItem.style.border = '2px solid var(--danger-color)';
                showToast(`Quote from ${quote.provider.name} declined.`, 'warning');
            }

            quoteItem.style.transform = 'scale(1)';
            quoteItem.style.opacity = '1';

            // Disable buttons
            const buttons = quoteItem.querySelectorAll('button');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            });
        }, 300);
    };

    // ================================================
    // ANIMATIONS
    // ================================================
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = Math.ceil(target / 50);
            const duration = 1000;
            const stepTime = duration / 50;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = current;
                }
            }, stepTime);
        });
    }

    function animateProgressBars() {
        setTimeout(() => {
            const progressBars = document.querySelectorAll('.progress-fill, .bar-fill');
            
            progressBars.forEach(bar => {
                const targetWidth = bar.style.width;
                bar.style.width = '0%';
                bar.style.transition = 'width 1s ease-out';
                
                setTimeout(() => {
                    bar.style.width = targetWidth;
                }, 100);
            });
        }, 500);
    }

    // ================================================
    // RESPONSIVE HANDLING
    // ================================================
    function handleResize() {
        // Close mobile menu on desktop
        if (window.innerWidth > 768) {
            mobileMenu?.classList.remove('show');
            mobileMenuToggle?.classList.remove('active');
        }
        
        // Auto-collapse sidebar on tablet
        if (window.innerWidth <= 1024 && sidebar && !sidebar.classList.contains('collapsed')) {
            sidebar.classList.add('collapsed');
        }
    }

    // ================================================
    // TOAST NOTIFICATION
    // ================================================
    function showToast(message, type = 'success') {
        if (!toast || !toastMessage) return;

        toastMessage.textContent = message;
        toast.className = `toast ${type}`;
        toast.classList.add('show');

        // Change icon based on type
        const toastIcon = toast.querySelector('.toast-icon');
        if (toastIcon) {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            toastIcon.className = `fas ${icons[type] || icons.success} toast-icon`;
        }

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // ================================================
    // INITIALIZE APP
    // ================================================
    init();
});
