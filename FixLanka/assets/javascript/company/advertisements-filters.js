/**
 * Advertisement Filtering System
 * Handles all filtering, sorting, and search functionality
 */

class AdvertisementFilters {
    constructor() {
        this.advertisements = [];
        this.filteredAds = [];
        this.currentFilters = {
            status: 'all',
            type: 'all',
            dateRange: 'all',
            sort: 'newest'
        };

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupReportModal();
        this.loadAdvertisements();
    }

    setupEventListeners() {
        // Status filter dropdown
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentFilters.status = e.target.value;
                this.applyFilters();
            });
        }

        // Type filter dropdown
        const typeFilter = document.getElementById('typeFilter');
        if (typeFilter) {
            typeFilter.addEventListener('change', (e) => {
                this.currentFilters.type = e.target.value;
                this.applyFilters();
            });
        }

        // Date filter dropdown
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', (e) => {
                this.currentFilters.dateRange = e.target.value;
                this.applyFilters();
            });
        }

        // View toggle buttons
        const listViewBtn = document.getElementById('listViewBtn');
        const gridViewBtn = document.getElementById('gridViewBtn');

        if (listViewBtn && gridViewBtn) {
            listViewBtn.addEventListener('click', () => {
                listViewBtn.classList.add('active');
                gridViewBtn.classList.remove('active');
                // Add list view logic here
            });

            gridViewBtn.addEventListener('click', () => {
                gridViewBtn.classList.add('active');
                listViewBtn.classList.remove('active');
                // Add grid view logic here
            });
        }
    }

    async loadAdvertisements() {
        try {
            const response = await fetch('../../api/advertisements.php', { cache: 'no-store' });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                // Ensure data.data is an array to prevent iteration errors
                this.advertisements = Array.isArray(data.data) ? data.data : [];
                this.updateStatusCounts(data.counts || {});
                this.applyFilters();
            } else {
                const msg = data.error || data.message || 'Failed to load advertisements';
                console.error('Failed to load advertisements:', msg);
                // Ensure advertisements is an empty array on API errors
                this.advertisements = [];
                this.showError(msg);
            }
        } catch (error) {
            console.error('Error loading advertisements:', error);
            // Ensure advertisements is an empty array on exceptions
            this.advertisements = [];
            this.showError('Error loading advertisements: ' + error.message);
        }
    }

    handleStatusFilter(status) {
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        this.currentFilters.status = status;
        this.applyFilters();
    }

    handleSearchInput(value) {
        this.currentFilters.search = value.toLowerCase();

        // Show/hide clear button
        const clearBtn = document.getElementById('clearSearch');
        if (clearBtn) {
            clearBtn.style.display = value ? 'flex' : 'none';
        }

        this.updateFilterDisplay();

        // Real-time search (debounced)
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.applyFilters();
        }, 300);
    }

    handleDateRangeChange(range) {
        this.currentFilters.dateRange = range;
        const customDateRange = document.getElementById('customDateRange');

        if (range === 'custom') {
            customDateRange.style.display = 'flex';
        } else {
            customDateRange.style.display = 'none';
            this.currentFilters.startDate = null;
            this.currentFilters.endDate = null;
        }

        this.updateFilterDisplay();
    }

    applyFilters() {
        let filtered = [...this.advertisements];

        // Status filter
        if (this.currentFilters.status !== 'all') {
            filtered = filtered.filter(ad =>
                ad.computed_status === this.currentFilters.status
            );
        }

        // Type filter
        if (this.currentFilters.type !== 'all') {
            filtered = filtered.filter(ad => ad.type === this.currentFilters.type);
        }

        // Date range filter
        if (this.currentFilters.dateRange !== 'all') {
            filtered = this.filterByDateRange(filtered);
        }

        // Sort
        filtered = this.sortAdvertisements(filtered);

        this.filteredAds = filtered;
        this.renderAdvertisements();
        this.updateResultsSummary();
    }

    filterByDateRange(ads) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

        const getCreatedDate = (ad) => {
            const value = ad.created_at || ad.submission_date || ad.submissionDate || null;
            const d = value ? new Date(value) : new Date(NaN);
            return d;
        };

        switch (this.currentFilters.dateRange) {
            case 'today':
                return ads.filter(ad => {
                    const created = getCreatedDate(ad);
                    return created >= today;
                });

            case 'week':
                const weekAgo = new Date(today);
                weekAgo.setDate(weekAgo.getDate() - 7);
                return ads.filter(ad => {
                    const created = getCreatedDate(ad);
                    return created >= weekAgo;
                });

            case 'month':
                const monthAgo = new Date(today);
                monthAgo.setMonth(monthAgo.getMonth() - 1);
                return ads.filter(ad => {
                    const created = getCreatedDate(ad);
                    return created >= monthAgo;
                });

            case 'quarter':
                const quarterAgo = new Date(today);
                quarterAgo.setMonth(quarterAgo.getMonth() - 3);
                return ads.filter(ad => {
                    const created = getCreatedDate(ad);
                    return created >= quarterAgo;
                });

            case 'year':
                const yearStart = new Date(now.getFullYear(), 0, 1);
                return ads.filter(ad => {
                    const created = getCreatedDate(ad);
                    return created >= yearStart;
                });

            case 'custom':
                if (this.currentFilters.startDate && this.currentFilters.endDate) {
                    const start = new Date(this.currentFilters.startDate);
                    const end = new Date(this.currentFilters.endDate);
                    return ads.filter(ad => {
                        const created = getCreatedDate(ad);
                        return created >= start && created <= end;
                    });
                }
                return ads;

            default:
                return ads;
        }
    }

    sortAdvertisements(ads) {
        const sorted = [...ads];

        switch (this.currentFilters.sort) {
            case 'newest':
                return sorted.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            case 'oldest':
                return sorted.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

            case 'title-asc':
                return sorted.sort((a, b) => a.title.localeCompare(b.title));

            case 'title-desc':
                return sorted.sort((a, b) => b.title.localeCompare(a.title));

            default:
                return sorted;
        }
    }

    renderAdvertisements() {
        const container = document.querySelector('.ads-container');
        if (!container) return;

        // Hide loading state if it exists
        const loadingState = container.querySelector('.loading-state');
        if (loadingState) {
            loadingState.style.display = 'none';
        }

        if (this.filteredAds.length === 0) {
            // Check if this is initial load with no data or filtered results
            const hasFilters = this.currentFilters.status !== 'all' ||
                this.currentFilters.type !== 'all' ||
                this.currentFilters.dateRange !== 'all';

            const heading = hasFilters ? 'No Advertisements Match Your Filters' : 'No Advertisements Found';
            const message = hasFilters
                ? 'No advertisements match your current filters. Try adjusting your filters or create a new advertisement.'
                : 'You haven\'t created any advertisements yet. Create your first advertisement to get started.';

            container.innerHTML = `
                <div class="empty-state-card">
                    <div class="empty-icon-circle">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3 class="empty-heading">${heading}</h3>
                    <p class="empty-message">${message}</p>
                    ${hasFilters ? "" : "<button class=\"btn-create-ad\" onclick=\"document.getElementById('createAdBtn').click()\"><i class=\"fas fa-plus\"></i> Create Advertisement</button>"}
                </div>
            `;
            return;
        }

        container.innerHTML = this.filteredAds.map(ad => this.renderAdCard(ad)).join('');
    }

    renderAdCard(ad) {
        const statusClass = ad.computed_status || ad.status;
        const statusIcon = this.getStatusIcon(statusClass);
        const typeIcon = this.getTypeIcon(ad.type);

        return `
            <div class="ad-card" data-ad-id="${ad.id}">
                <div class="ad-media">
                    ${this.renderMediaPreview(ad)}
                    <span class="media-type">
                        <i class="${typeIcon}"></i> ${ad.type}
                    </span>
                </div>
                <div class="ad-content">
                    <div class="ad-header">
                        <h3 class="ad-title">${ad.title}</h3>
                        <span class="status-badge ${statusClass}">
                            <i class="${statusIcon}"></i> ${statusClass}
                        </span>
                    </div>
                    <p class="ad-description">${ad.description || 'No description provided'}</p>
                    <div class="ad-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-day"></i>
                            <span>Start: ${this.formatDate(ad.start_date)}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>End: ${this.formatDate(ad.end_date)}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-eye"></i>
                            <span>${(ad.impressions || 0).toLocaleString()} views</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-mouse-pointer"></i>
                            <span>${(ad.clicks || 0).toLocaleString()} clicks</span>
                        </div>
                    </div>
                </div>
                <div class="ad-actions">
                    ${this.renderActionButtons(ad, statusClass)}
                </div>
            </div>
        `;
    }

    renderActionButtons(ad, statusClass) {
        let buttons = [];

        // View Details button (always available)
        buttons.push(`
            <button class="action-btn-ad view" onclick="advertisementFilters.viewAdDetails(${ad.id})" title="View details">
                <i class="fas fa-eye"></i> View
            </button>
        `);

        // Add Report Issue button (available for all ads)
        buttons.push(`
            <button class="action-btn-ad warning" onclick="advertisementFilters.reportIssue(${ad.id})" title="Report issue with this ad">
                <i class="fas fa-flag"></i> Report
            </button>
        `);

        // Status-specific action buttons
        switch (statusClass) {
            case 'active':
                // Active ads: Pause, Analytics, Edit, Delete
                buttons.push(`
                    <button class="action-btn-ad pause" onclick="advertisementFilters.pauseAdvertisement(${ad.id})" title="Pause advertisement">
                        <i class="fas fa-pause"></i> Pause
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad success" onclick="advertisementFilters.viewAnalytics(${ad.id})" title="View analytics">
                        <i class="fas fa-chart-line"></i> Analytics
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad edit" onclick="advertisementFilters.editAdvertisement(${ad.id})" title="Edit advertisement">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad delete" onclick="advertisementFilters.deleteAdvertisement(${ad.id})" title="Delete advertisement">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                `);
                break;

            case 'paused':
                // Paused ads: Resume, Analytics, Edit, Delete
                buttons.push(`
                    <button class="action-btn-ad success" onclick="advertisementFilters.resumeAdvertisement(${ad.id})" title="Resume advertisement">
                        <i class="fas fa-play"></i> Resume
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad primary" onclick="advertisementFilters.viewAnalytics(${ad.id})" title="View analytics">
                        <i class="fas fa-chart-line"></i> Analytics
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad edit" onclick="advertisementFilters.editAdvertisement(${ad.id})" title="Edit advertisement">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad delete" onclick="advertisementFilters.deleteAdvertisement(${ad.id})" title="Delete advertisement">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                `);
                break;

            case 'pending':
                // Pending ads: Edit, Cancel (while waiting for approval)
                buttons.push(`
                    <button class="action-btn-ad edit" onclick="advertisementFilters.editAdvertisement(${ad.id})" title="Edit before approval">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad warning" onclick="advertisementFilters.cancelAdvertisement(${ad.id})" title="Cancel submission">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                `);
                break;

            case 'scheduled':
                // Scheduled ads: Start Now, Edit, Cancel
                buttons.push(`
                    <button class="action-btn-ad success" onclick="advertisementFilters.startAdvertisement(${ad.id})" title="Start immediately">
                        <i class="fas fa-play"></i> Start Now
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad edit" onclick="advertisementFilters.editAdvertisement(${ad.id})" title="Edit scheduled advertisement">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad warning" onclick="advertisementFilters.cancelAdvertisement(${ad.id})" title="Cancel scheduled advertisement">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                `);
                break;

            case 'expired':
                // Expired ads: View Report, Renew, Duplicate, Archive
                buttons.push(`
                    <button class="action-btn-ad primary" onclick="advertisementFilters.viewReport(${ad.id})" title="View final report">
                        <i class="fas fa-chart-bar"></i> Report
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad success" onclick="advertisementFilters.renewAdvertisement(${ad.id})" title="Renew advertisement">
                        <i class="fas fa-redo"></i> Renew
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad secondary" onclick="advertisementFilters.duplicateAdvertisement(${ad.id})" title="Create a copy">
                        <i class="fas fa-copy"></i> Duplicate
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad warning" onclick="advertisementFilters.archiveAdvertisement(${ad.id})" title="Archive advertisement">
                        <i class="fas fa-archive"></i> Archive
                    </button>
                `);
                break;

            default:
                // Default: Edit and Delete
                buttons.push(`
                    <button class="action-btn-ad edit" onclick="advertisementFilters.editAdvertisement(${ad.id})" title="Edit advertisement">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                `);
                buttons.push(`
                    <button class="action-btn-ad delete" onclick="advertisementFilters.deleteAdvertisement(${ad.id})" title="Delete advertisement">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                `);
        }

        return buttons.join('');
    }

    renderMediaPreview(ad) {
        const imageUrl = ad.image_url || ad.imageUrl || ad.media_url || '';
        if (imageUrl) {
            const safeSrc = this.escapeHtml(imageUrl);
            const safeAlt = this.escapeHtml(ad.title || 'Advertisement image');
            return `<img src="${safeSrc}" alt="${safeAlt}" loading="lazy">`;
        }

        // Fallback placeholder banner when no image is uploaded
        return `
            <div class="ad-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="banner-content">
                    <h2>${ad.title}</h2>
                </div>
            </div>
        `;
    }

    escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    getStatusIcon(status) {
        const icons = {
            active: 'fas fa-circle',
            pending: 'fas fa-clock',
            scheduled: 'fas fa-calendar-alt',
            paused: 'fas fa-pause-circle',
            expired: 'fas fa-calendar-times'
        };
        return icons[status] || 'fas fa-circle';
    }

    getTypeIcon(type) {
        const icons = {
            banner: 'fas fa-image',
            featured: 'fas fa-star',
            sponsored: 'fas fa-bullhorn',
            video: 'fas fa-video',
            carousel: 'fas fa-images'
        };
        return icons[type] || 'fas fa-ad';
    }

    formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    updateStatusCounts(counts) {
        Object.keys(counts).forEach(status => {
            const element = document.getElementById(`count-${status}`);
            if (element) {
                element.textContent = counts[status];
            }
        });
    }

    updateResultsSummary() {
        const resultCount = document.getElementById('resultCount');
        const totalCount = document.getElementById('totalCount');
        const resultsSummary = document.querySelector('.results-summary');

        if (resultCount) resultCount.textContent = this.filteredAds.length;
        if (totalCount) totalCount.textContent = this.advertisements.length;

        // Show/hide results summary
        if (resultsSummary && this.filteredAds.length > 0) {
            resultsSummary.style.display = 'block';
        } else if (resultsSummary) {
            resultsSummary.style.display = 'none';
        }
    }

    resetFilters() {
        this.currentFilters = {
            status: 'all',
            type: 'all',
            dateRange: 'all',
            sort: 'newest'
        };

        // Reset UI
        const statusFilter = document.getElementById('statusFilter');
        const typeFilter = document.getElementById('typeFilter');
        const dateFilter = document.getElementById('dateFilter');

        if (statusFilter) statusFilter.value = 'all';
        if (typeFilter) typeFilter.value = 'all';
        if (dateFilter) dateFilter.value = 'all';

        this.applyFilters();
    }

    showError(message) {
        const container = document.querySelector('.ads-container');
        if (container) {
            // Hide loading state if it exists
            const loadingState = container.querySelector('.loading-state');
            if (loadingState) {
                loadingState.style.display = 'none';
            }

            container.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Unable to Load Advertisements</h3>
                    <p>${message || 'An error occurred while loading your advertisements. Please try again.'}</p>
                    <button class="action-btn primary" onclick="advertisementFilters.loadAdvertisements()">
                        <i class="fas fa-redo"></i> Try Again
                    </button>
                </div>
            `;
        }
    }

    // ============================================
    // ACTION HANDLERS
    // ============================================

    viewAdDetails(adId) {
        const ad = this.advertisements.find(a => a.id === adId);
        if (!ad) {
            alert('Advertisement not found');
            return;
        }

        // Create and show details modal
        const modalHtml = `
            <div class="modal-overlay active" id="viewAdModal">
                <div class="modal-container" style="max-width: 800px;">
                    <div class="modal-header">
                        <h2><i class="fas fa-info-circle"></i> Advertisement Details</h2>
                        <button class="modal-close" onclick="document.getElementById('viewAdModal').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <div class="ad-detail-section">
                            <h3>${ad.title}</h3>
                            <span class="status-badge ${ad.computed_status || ad.status}">
                                <i class="${this.getStatusIcon(ad.computed_status || ad.status)}"></i> 
                                ${ad.computed_status || ad.status}
                            </span>
                        </div>
                        <div class="ad-detail-section">
                            <p><strong>Description:</strong></p>
                            <p>${ad.description || 'No description provided'}</p>
                        </div>
                        <div class="ad-detail-section">
                            <p><strong>Category:</strong> ${ad.category || 'N/A'}</p>
                            <p><strong>Type:</strong> ${ad.type || 'N/A'}</p>
                            <p><strong>Budget:</strong> Rs. ${(ad.budget || 0).toLocaleString()}</p>
                        </div>
                        <div class="ad-detail-section">
                            <p><strong>Start Date:</strong> ${this.formatDate(ad.start_date)}</p>
                            <p><strong>End Date:</strong> ${this.formatDate(ad.end_date)}</p>
                        </div>
                        <div class="ad-detail-section">
                            <h4>Performance Metrics</h4>
                            <p><strong>Views:</strong> ${(ad.impressions || 0).toLocaleString()}</p>
                            <p><strong>Clicks:</strong> ${(ad.clicks || 0).toLocaleString()}</p>
                            <p><strong>Click-Through Rate:</strong> ${ad.click_through_rate || 0}%</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary" onclick="document.getElementById('viewAdModal').remove()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    async editAdvertisement(adId) {
        const ad = this.advertisements.find(a => a.id === adId);
        if (!ad) {
            alert('Advertisement not found');
            return;
        }

        const statusClass = ad.computed_status || ad.status;
        if (statusClass !== 'pending') {
            alert('Only pending advertisements can be edited.');
            return;
        }

        // Open the existing create modal and populate with data
        const adModal = document.getElementById('adModal');
        const adForm = document.getElementById('adForm');
        if (adModal) {
            if (adForm) {
                adForm.dataset.editingAdId = String(adId);
                adForm.dataset.originalStartDate = ad.start_date ? String(ad.start_date).slice(0, 10) : '';
                adForm.dataset.originalEndDate = ad.end_date ? String(ad.end_date).slice(0, 10) : '';
            }

            const modalTitle = document.getElementById('modalTitle');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-pen"></i> Edit Advertisement';
            }

            const submitBtn = document.getElementById('submitAd');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Advertisement';
            }

            // Prefill key fields
            const typeRadio = document.querySelector(`input[name="adType"][value="${ad.type}"]`);
            if (typeRadio) typeRadio.checked = true;

            const titleEl = document.getElementById('adTitle');
            const descEl = document.getElementById('adDescription');
            if (titleEl) titleEl.value = ad.title || '';
            if (descEl) descEl.value = ad.description || '';

            // Prefill schedule dates (required)
            const startDateEl = document.getElementById('startDate');
            const endDateEl = document.getElementById('endDate');
            if (startDateEl && ad.start_date) startDateEl.value = String(ad.start_date).slice(0, 10);
            if (endDateEl && ad.end_date) endDateEl.value = String(ad.end_date).slice(0, 10);

            // Show existing image preview if available
            const uploadArea = document.getElementById('uploadArea');
            const uploadPreview = document.getElementById('uploadPreview');
            const previewImage = document.getElementById('previewImage');
            const previewVideo = document.getElementById('previewVideo');

            if (ad.image_url && uploadArea && uploadPreview && previewImage) {
                uploadArea.style.display = 'none';
                uploadPreview.style.display = 'block';
                previewImage.src = ad.image_url;
                previewImage.style.display = 'block';
                if (previewVideo) previewVideo.style.display = 'none';
            }

            adModal.classList.add('active');
            document.body.style.overflow = 'hidden';

            if (typeof window.initWizardDefaults === 'function') {
                window.initWizardDefaults();
            } else if (typeof initWizardDefaults === 'function') {
                initWizardDefaults();
            }

            // Jump wizard to the media/details step to make updating the image easy
            if (typeof window.__adWizardSetStep === 'function') {
                window.__adWizardSetStep(2);
            }
        }
    }

    async pauseAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Are you sure you want to pause this advertisement?', {
                title: 'Pause Advertisement',
                confirmText: 'Pause',
                cancelText: 'Cancel',
                type: 'warning',
                icon: 'fas fa-pause'
            })
            : confirm('Are you sure you want to pause this advertisement?');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'pause',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement paused successfully!');
                this.loadAdvertisements(); // Reload data
            } else {
                alert('Failed to pause advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error pausing advertisement:', error);
            alert('Error pausing advertisement. Please try again.');
        }
    }

    async resumeAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Resume this advertisement?', {
                title: 'Resume Advertisement',
                confirmText: 'Resume',
                cancelText: 'Cancel',
                type: 'question',
                icon: 'fas fa-play'
            })
            : confirm('Resume this advertisement?');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'resume',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement resumed successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to resume advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error resuming advertisement:', error);
            alert('Error resuming advertisement. Please try again.');
        }
    }

    async startAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Start this advertisement immediately?', {
                title: 'Start Advertisement',
                confirmText: 'Start',
                cancelText: 'Cancel',
                type: 'question',
                icon: 'fas fa-bolt'
            })
            : confirm('Start this advertisement immediately?');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'start',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement started successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to start advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error starting advertisement:', error);
            alert('Error starting advertisement. Please try again.');
        }
    }

    async cancelAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Are you sure you want to cancel this advertisement?', {
                title: 'Cancel Advertisement',
                confirmText: 'Cancel Ad',
                cancelText: 'Keep',
                type: 'warning',
                icon: 'fas fa-ban'
            })
            : confirm('Are you sure you want to cancel this advertisement?');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'cancel',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement cancelled successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to cancel advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error cancelling advertisement:', error);
            alert('Error cancelling advertisement. Please try again.');
        }
    }

    async deleteAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Are you sure you want to delete this advertisement? This action cannot be undone.', {
                title: 'Delete Advertisement',
                confirmText: 'Delete',
                cancelText: 'Cancel',
                type: 'danger',
                icon: 'fas fa-trash'
            })
            : confirm('Are you sure you want to delete this advertisement? This action cannot be undone.');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement deleted successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to delete advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error deleting advertisement:', error);
            alert('Error deleting advertisement. Please try again.');
        }
    }

    viewAnalytics(adId) {
        const ad = this.advertisements.find(a => a.id === adId);
        if (!ad) {
            alert('Advertisement not found');
            return;
        }

        // Show analytics modal
        const ctr = ad.click_through_rate ||
            (ad.clicks > 0 && ad.impressions > 0 ? ((ad.clicks / ad.impressions) * 100).toFixed(2) : 0);

        const modalHtml = `
            <div class="modal-overlay active" id="analyticsModal">
                <div class="modal-container" style="max-width: 900px;">
                    <div class="modal-header">
                        <h2><i class="fas fa-chart-line"></i> Advertisement Analytics</h2>
                        <button class="modal-close" onclick="document.getElementById('analyticsModal').remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-content">
                        <h3>${ad.title}</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1.5rem 0;">
                            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                                <i class="fas fa-eye" style="font-size: 2rem; color: var(--primary-color);"></i>
                                <h4 style="margin: 0.5rem 0;">${(ad.impressions || 0).toLocaleString()}</h4>
                                <p style="color: var(--text-secondary); margin: 0;">Total Views</p>
                            </div>
                            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                                <i class="fas fa-mouse-pointer" style="font-size: 2rem; color: var(--success-color);"></i>
                                <h4 style="margin: 0.5rem 0;">${(ad.clicks || 0).toLocaleString()}</h4>
                                <p style="color: var(--text-secondary); margin: 0;">Total Clicks</p>
                            </div>
                            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                                <i class="fas fa-chart-line" style="font-size: 2rem; color: var(--warning-color);"></i>
                                <h4 style="margin: 0.5rem 0;">${ctr}%</h4>
                                <p style="color: var(--text-secondary); margin: 0;">Click-Through Rate</p>
                            </div>
                            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--border-radius); text-align: center;">
                                <i class="fas fa-dollar-sign" style="font-size: 2rem; color: var(--info-color);"></i>
                                <h4 style="margin: 0.5rem 0;">Rs. ${(ad.budget_spent || 0).toLocaleString()}</h4>
                                <p style="color: var(--text-secondary); margin: 0;">Budget Spent</p>
                            </div>
                        </div>
                        <div style="margin-top: 2rem;">
                            <h4>Campaign Details</h4>
                            <p><strong>Duration:</strong> ${this.formatDate(ad.start_date)} - ${this.formatDate(ad.end_date)}</p>
                            <p><strong>Total Budget:</strong> Rs. ${(ad.budget || 0).toLocaleString()}</p>
                            <p><strong>Status:</strong> <span class="status-badge ${ad.computed_status}">${ad.computed_status}</span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-secondary" onclick="document.getElementById('analyticsModal').remove()">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    viewReport(adId) {
        // Similar to analytics but for expired ads with final report
        this.viewAnalytics(adId);
    }

    async renewAdvertisement(adId) {
        const ad = this.advertisements.find(a => a.id === adId);
        if (!ad) {
            alert('Advertisement not found');
            return;
        }

        const confirmedRenew = window.systemConfirm
            ? await window.systemConfirm(`Renew advertisement "${ad.title}"?\n\nThis will create a new campaign with the same details.`, {
                title: 'Renew Advertisement',
                confirmText: 'Renew',
                cancelText: 'Cancel',
                type: 'question',
                icon: 'fas fa-sync'
            })
            : confirm(`Renew advertisement "${ad.title}"?\n\nThis will create a new campaign with the same details.`);

        if (!confirmedRenew) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'renew',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement renewed successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to renew advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error renewing advertisement:', error);
            alert('Error renewing advertisement. Please try again.');
        }
    }

    async duplicateAdvertisement(adId) {
        const ad = this.advertisements.find(a => a.id === adId);
        if (!ad) {
            alert('Advertisement not found');
            return;
        }

        const confirmedDup = window.systemConfirm
            ? await window.systemConfirm(`Create a copy of "${ad.title}"?`, {
                title: 'Duplicate Advertisement',
                confirmText: 'Duplicate',
                cancelText: 'Cancel',
                type: 'question',
                icon: 'fas fa-copy'
            })
            : confirm(`Create a copy of "${ad.title}"?`);

        if (!confirmedDup) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'duplicate',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement duplicated successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to duplicate advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error duplicating advertisement:', error);
            alert('Error duplicating advertisement. Please try again.');
        }
    }

    async archiveAdvertisement(adId) {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Archive this advertisement? It will be moved to archived items.', {
                title: 'Archive Advertisement',
                confirmText: 'Archive',
                cancelText: 'Cancel',
                type: 'warning',
                icon: 'fas fa-archive'
            })
            : confirm('Archive this advertisement? It will be moved to archived items.');

        if (!confirmed) return;

        try {
            const response = await fetch('../../api/advertisements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'archive',
                    ad_id: adId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert('Advertisement archived successfully!');
                this.loadAdvertisements();
            } else {
                alert('Failed to archive advertisement: ' + (data.error || data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error archiving advertisement:', error);
            alert('Error archiving advertisement. Please try again.');
        }
    }

    // ============================================
    // REPORTING LOGIC
    // ============================================

    setupReportModal() {
        const modal = document.getElementById('reportAdModal');
        const form = document.getElementById('reportAdForm');
        const closeBtn = document.getElementById('closeReportModal');
        const cancelBtn = document.getElementById('cancelReport');

        if (!modal || !form) return;

        const closeModal = () => {
            modal.classList.remove('active');
            form.reset();
        };

        if (closeBtn) closeBtn.onclick = closeModal;
        if (cancelBtn) cancelBtn.onclick = closeModal;

        window.onclick = (event) => {
            if (event.target === modal) closeModal();
        };

        form.onsubmit = async (e) => {
            e.preventDefault();
            await this.submitAdReport();
        };
    }

    reportIssue(adId) {
        const modal = document.getElementById('reportAdModal');
        const adIdInput = document.getElementById('reportAdId');

        if (modal && adIdInput) {
            adIdInput.value = adId;
            modal.classList.add('active');
        }
    }

    async submitAdReport() {
        const form = document.getElementById('reportAdForm');
        const btn = document.getElementById('submitReportBtn');
        const modal = document.getElementById('reportAdModal');

        if (!form || !btn) return;

        const originalBtnHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        try {
            const formData = {
                ad_id: form.ad_id.value,
                issue_type: form.issue_type.value,
                description: form.description.value
            };

            const response = await fetch('../../api/ad-report-submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            if (result.success) {
                if (window.showAlert) {
                    await window.showAlert(result.message || 'Report submitted successfully.', 'success', 'Report Submitted');
                } else {
                    alert(result.message || 'Report submitted successfully. Our moderators will review it shortly.');
                }
                this.closeReportModal();
            } else {
                throw new Error(result.error || 'Failed to submit report');
            }
        } catch (error) {
            console.error('Error submitting report:', error);
            if (window.showAlert) {
                window.showAlert(error.message || 'An error occurred. Please try again.', 'danger', 'Submission Error');
            } else {
                alert(error.message || 'An error occurred. Please try again.');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalBtnHtml;
        }
    }
}

// Initialize on page load
window.advertisementFilters = null;
document.addEventListener('DOMContentLoaded', () => {
    window.advertisementFilters = new AdvertisementFilters();
});

// Global functions for ad actions
function viewAdDetails(adId) {

    // TODO: Implement view details modal
}

function editAdvertisement(adId) {

    // TODO: Implement edit functionality
}

function pauseAdvertisement(adId) {
    const run = async () => {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Are you sure you want to pause this advertisement?', {
                title: 'Pause Advertisement',
                confirmText: 'Pause',
                cancelText: 'Cancel',
                type: 'warning',
                icon: 'fas fa-pause'
            })
            : confirm('Are you sure you want to pause this advertisement?');

        if (!confirmed) return;

        // TODO: Implement pause functionality via API
    };
    run();
}

function deleteAdvertisement(adId) {
    const run = async () => {
        const confirmed = window.systemConfirm
            ? await window.systemConfirm('Are you sure you want to delete this advertisement? This action cannot be undone.', {
                title: 'Delete Advertisement',
                confirmText: 'Delete',
                cancelText: 'Cancel',
                type: 'danger',
                icon: 'fas fa-trash'
            })
            : confirm('Are you sure you want to delete this advertisement? This action cannot be undone.');

        if (!confirmed) return;

        // TODO: Implement delete functionality via API
    };
    run();
}


