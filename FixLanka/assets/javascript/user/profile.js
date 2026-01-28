// ================================================
// PROFILE PAGE - USER PROFILE MANAGEMENT
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    const USER_QUOTES_API = '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php';
    const DEBUG_QUOTES = true;
    
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
        setupEventListeners();
        loadSidebarState();

        try {
            renderProfileData();
        } catch (e) {
            console.error('[Profile] renderProfileData failed:', e);
        }

        try {
            animateCounters();
            animateProgressBars();
        } catch (e) {
            console.warn('[Profile] animations failed:', e);
        }

        loadQuotesPreview();
    }

    // ================================================
    // RENDER PROFILE DATA
    // ================================================
    function setText(selector, value) {
        const el = document.querySelector(selector);
        if (el) el.textContent = value;
    }

    function renderProfileData() {
        // User info card
        setText('.user-full-name', userData.fullName);
        setText('.user-username', `@${userData.username}`);
        setText('.account-status', userData.accountStatus);
        setText('.member-since', `Member since ${userData.memberSince}`);
        
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
        const completionEl = document.getElementById('completionPercentage');
        if (completionEl) completionEl.textContent = String(userData.completionPercentage);

        const progressFill = document.querySelector('.progress-fill');
        if (progressFill) progressFill.style.width = `${userData.completionPercentage}%`;

        // Stats
        const statEls = document.querySelectorAll('.stat-number');
        if (statEls.length >= 4) {
            statEls[0].textContent = userData.stats.totalJobs;
            statEls[1].textContent = userData.stats.activeJobs;
            statEls[2].textContent = userData.stats.completedJobs;
            statEls[3].textContent = userData.stats.pendingPayments;
        }

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
        setText('.average-rating', String(userData.rating.average));
        setText('.total-reviews', `${userData.rating.total} reviews`);

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

        // Quotes preview is loaded from DB via loadQuotesPreview()

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
            'quotes': '/2nd-Year-Group-Project/FixLanka/views/user/quotes_received.php',
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
    // QUOTES (DB)
    // ================================================
    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatMoney(value) {
        const n = Number(value);
        if (!Number.isFinite(n)) return 'LKR 0';
        return `LKR ${n.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    }

    async function fetchJson(url, options) {
        if (DEBUG_QUOTES) {
            console.log('[Quotes] Request:', url, options?.method || 'GET');
        }

        const res = await fetch(url, { credentials: 'same-origin', ...(options || {}) });
        const text = await res.text();
        if (DEBUG_QUOTES) {
            console.log('[Quotes] Response status:', res.status);
            console.log('[Quotes] Response body (first 300 chars):', text.slice(0, 300));
        }
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid JSON');
        }
        if (!res.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${res.status})`);
        }
        return data;
    }

    function setQuotesBadge(count) {
        const badge = document.getElementById('quotesBadge');
        if (!badge) return;
        const c = Number.isFinite(count) ? count : 0;
        if (c > 0) {
            badge.textContent = `${c} new`;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    function renderProfileQuoteItem(q) {
        const providerName = q.provider_name || (q.source === 'company' ? 'Company' : 'Repairer');
        const providerTypeLabel = q.source === 'company' ? 'Company' : 'Individual';
        const jobTitle = q.job_title || 'Job';
        const amount = formatMoney(q.amount);
        const canRespond = q.status === 'pending';

        return `
            <div class="quote-item" data-source="${escapeHtml(q.source)}" data-quote-id="${escapeHtml(q.quote_id)}">
                <div class="quote-provider">
                    <img src="${escapeHtml(q.provider_avatar || 'https://via.placeholder.com/40')}" alt="Provider" class="provider-avatar">
                    <div class="provider-info">
                        <span class="provider-name">${escapeHtml(providerName)}</span>
                        <span class="provider-type">${escapeHtml(providerTypeLabel)}</span>
                    </div>
                </div>
                <div class="quote-details">
                    <span class="quote-amount">${escapeHtml(amount)}</span>
                    <span class="quote-job">${escapeHtml(jobTitle)}</span>
                </div>
                <div class="quote-actions">
                    <button class="btn-success-sm" ${canRespond ? '' : 'disabled'} onclick="handleQuoteAction('accepted','${escapeHtml(q.source)}',${escapeHtml(q.quote_id)})">Accept</button>
                    <button class="btn-outline-sm" ${canRespond ? '' : 'disabled'} onclick="handleQuoteAction('rejected','${escapeHtml(q.source)}',${escapeHtml(q.quote_id)})">Decline</button>
                </div>
            </div>
        `;
    }

    async function loadQuotesPreview() {
        const list = document.getElementById('quotesList');
        if (!list) return;
        try {
            const data = await fetchJson(`${USER_QUOTES_API}?action=summary&limit=3`);
            if (DEBUG_QUOTES) {
                console.log('[Quotes] summary payload:', data);
            }
            const quotes = Array.isArray(data.quotes) ? data.quotes : [];
            setQuotesBadge(parseInt(data.pending_count, 10) || 0);

            if (quotes.length === 0) {
                list.innerHTML = `
                    <div class="quote-item">
                        <div class="quote-details">
                            <span class="quote-job">No quotes received yet</span>
                        </div>
                    </div>
                `;
                return;
            }

            list.innerHTML = quotes.map(renderProfileQuoteItem).join('');
        } catch (e) {
            console.error('[Quotes] Failed to load preview:', e);
            list.innerHTML = `
                <div class="quote-item">
                    <div class="quote-details">
                        <span class="quote-job">Failed to load quotes</span>
                    </div>
                </div>
            `;
            setQuotesBadge(0);
        }
    }

    // ================================================
    // QUOTE ACTIONS (Global function for onclick)
    // ================================================
    window.handleQuoteAction = async function(decision, source, quoteId) {
        try {
            await fetchJson(`${USER_QUOTES_API}?action=respond`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ source, quote_id: Number(quoteId), decision })
            });
            await loadQuotesPreview();
        } catch (e) {
            console.error('[Quotes] respond failed:', e);
            showToast('Failed to update quote', 'error');
        }
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
