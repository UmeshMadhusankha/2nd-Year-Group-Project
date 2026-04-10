// Advertisement Management JavaScript
// Handles filtering, sorting, search, and all ad actions

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all event listeners and functionality
    initializeAdvertisements();
});

// Global state
let currentFilter = 'all';
let currentSort = 'newest';
let currentCategory = 'all';
let currentView = 'grid';

function initializeAdvertisements() {
    // Search functionality
    const searchInput = document.getElementById('adSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    
    if (searchInput) {
        searchInput.addEventListener('input', handleSearch);
    }
    
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', clearSearch);
    }
    
    // Sort functionality
    const sortSelect = document.getElementById('sortBy');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            sortAds(currentSort);
        });
    }
    
    // Category filter
    const categorySelect = document.getElementById('categoryFilter');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            currentCategory = this.value;
            filterAdsByCategory(currentCategory);
        });
    }
    
    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.dataset.status;
            switchFilterTab(status);
        });
    });
    
    // View toggle
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            toggleView(view);
        });
    });
    
    // Initialize counts
    updateFilterCounts();
    updateResultCount();
}

// Search functionality
function handleSearch(e) {
    const query = e.target.value.toLowerCase().trim();
    const clearBtn = document.getElementById('clearSearch');
    
    // Show/hide clear button
    if (clearBtn) {
        clearBtn.style.display = query ? 'flex' : 'none';
    }
    
    filterAdsBySearch(query);
}

function clearSearch() {
    const searchInput = document.getElementById('adSearch');
    const clearBtn = document.getElementById('clearSearch');
    
    if (searchInput) {
        searchInput.value = '';
    }
    if (clearBtn) {
        clearBtn.style.display = 'none';
    }
    
    filterAdsBySearch('');
}

function filterAdsBySearch(query) {
    const cards = document.querySelectorAll('.ad-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        // Get card text content
        const title = card.querySelector('.ad-title')?.textContent.toLowerCase() || '';
        const category = card.querySelector('.ad-category')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.ad-description')?.textContent.toLowerCase() || '';
        
        // Check if matches search
        const matches = title.includes(query) || 
                       category.includes(query) || 
                       description.includes(query);
        
        // Check current filter and category
        const status = card.dataset.status;
        const cardCategory = card.dataset.category;
        
        const statusMatch = currentFilter === 'all' || status === currentFilter;
        const categoryMatch = currentCategory === 'all' || cardCategory === currentCategory;
        
        // Show/hide card
        if (matches && statusMatch && categoryMatch) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultCount(visibleCount);
    toggleEmptyState(visibleCount);
}

// Filter by status tabs
function switchFilterTab(status) {
    currentFilter = status;
    
    // Update active tab
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => {
        if (tab.dataset.status === status) {
            tab.classList.add('active');
        } else {
            tab.classList.remove('active');
        }
    });
    
    // Apply filters
    applyAllFilters();
}

function filterAdsByCategory(category) {
    currentCategory = category;
    applyAllFilters();
}

function applyAllFilters() {
    const cards = document.querySelectorAll('.ad-card');
    const searchQuery = document.getElementById('adSearch')?.value.toLowerCase().trim() || '';
    let visibleCount = 0;
    
    cards.forEach(card => {
        const status = card.dataset.status;
        const category = card.dataset.category;
        
        // Check all filters
        const statusMatch = currentFilter === 'all' || status === currentFilter;
        const categoryMatch = currentCategory === 'all' || category === currentCategory;
        
        // Check search if there's a query
        let searchMatch = true;
        if (searchQuery) {
            const title = card.querySelector('.ad-title')?.textContent.toLowerCase() || '';
            const cardCategoryText = card.querySelector('.ad-category')?.textContent.toLowerCase() || '';
            const description = card.querySelector('.ad-description')?.textContent.toLowerCase() || '';
            
            searchMatch = title.includes(searchQuery) || 
                         cardCategoryText.includes(searchQuery) || 
                         description.includes(searchQuery);
        }
        
        // Show/hide card
        if (statusMatch && categoryMatch && searchMatch) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateResultCount(visibleCount);
    toggleEmptyState(visibleCount);
}

// Sort functionality
function sortAds(sortBy) {
    const container = document.querySelector('.ads-container');
    const cards = Array.from(container.querySelectorAll('.ad-card'));
    
    cards.sort((a, b) => {
        switch(sortBy) {
            case 'newest':
                return new Date(b.dataset.created) - new Date(a.dataset.created);
            
            case 'oldest':
                return new Date(a.dataset.created) - new Date(b.dataset.created);
            
            case 'views':
                const viewsA = parseInt(a.querySelector('.stat-value')?.textContent.replace(/,/g, '') || 0);
                const viewsB = parseInt(b.querySelector('.stat-value')?.textContent.replace(/,/g, '') || 0);
                return viewsB - viewsA;
            
            case 'clicks':
                const statsA = a.querySelectorAll('.stat-value');
                const statsB = b.querySelectorAll('.stat-value');
                const clicksA = parseInt(statsA[1]?.textContent.replace(/,/g, '') || 0);
                const clicksB = parseInt(statsB[1]?.textContent.replace(/,/g, '') || 0);
                return clicksB - clicksA;
            
            case 'ending-soon':
                const dateA = new Date(a.dataset.endDate || '9999-12-31');
                const dateB = new Date(b.dataset.endDate || '9999-12-31');
                return dateA - dateB;
            
            default:
                return 0;
        }
    });
    
    // Re-append sorted cards
    cards.forEach(card => container.appendChild(card));
}

// View toggle
function toggleView(view) {
    currentView = view;
    const container = document.querySelector('.ads-container');
    const viewButtons = document.querySelectorAll('.view-btn');
    
    // Update container class
    if (view === 'list') {
        container.classList.add('list-view');
    } else {
        container.classList.remove('list-view');
    }
    
    // Update active button
    viewButtons.forEach(btn => {
        if (btn.dataset.view === view) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Update result count
function updateResultCount(count = null) {
    const resultCount = document.getElementById('resultCount');
    if (!resultCount) return;
    
    if (count === null) {
        const visibleCards = document.querySelectorAll('.ad-card[style=""], .ad-card:not([style*="display: none"])');
        count = visibleCards.length;
    }
    
    resultCount.textContent = count;
}

// Update filter tab counts
function updateFilterCounts() {
    const cards = document.querySelectorAll('.ad-card');
    const counts = {
        all: cards.length,
        active: 0,
        pending: 0,
        scheduled: 0,
        paused: 0,
        expired: 0
    };
    
    cards.forEach(card => {
        const status = card.dataset.status;
        if (counts.hasOwnProperty(status)) {
            counts[status]++;
        }
    });
    
    // Update tab badges
    Object.keys(counts).forEach(status => {
        const tab = document.querySelector(`.filter-tab[data-status="${status}"]`);
        if (tab) {
            const badge = tab.querySelector('.filter-count');
            if (badge) {
                badge.textContent = counts[status];
            }
        }
    });
}

// Toggle empty state
function toggleEmptyState(visibleCount) {
    const emptyState = document.querySelector('.empty-state');
    const adsContainer = document.querySelector('.ads-container');
    
    if (emptyState) {
        if (visibleCount === 0) {
            emptyState.style.display = 'block';
            if (adsContainer) {
                adsContainer.style.display = 'none';
            }
        } else {
            emptyState.style.display = 'none';
            if (adsContainer) {
                adsContainer.style.display = '';
            }
        }
    }
}

// Ad Action Functions
function viewAdDetails(adId) {
    console.log('Viewing details for ad:', adId);
    // TODO: Implement modal or redirect to ad details page
    alert(`View Details - Ad ID: ${adId}\n\nThis will show detailed analytics and information about the advertisement.`);
}

function editAd(adId) {
    console.log('Editing ad:', adId);
    // TODO: Populate form with ad data and show modal
    alert(`Edit Ad - Ad ID: ${adId}\n\nThis will open the ad editor with pre-filled information.`);
}

function pauseAd(adId) {
    if (confirm('Are you sure you want to pause this advertisement? It will stop showing to users immediately.')) {
        console.log('Pausing ad:', adId);
        // TODO: Send AJAX request to pause ad
        alert(`Ad ${adId} has been paused successfully.`);
        // Update UI
        updateAdStatus(adId, 'paused');
    }
}

function resumeAd(adId) {
    if (confirm('Are you sure you want to resume this advertisement? It will start showing to users again.')) {
        console.log('Resuming ad:', adId);
        // TODO: Send AJAX request to resume ad
        alert(`Ad ${adId} has been resumed successfully.`);
        // Update UI
        updateAdStatus(adId, 'active');
    }
}

function deleteAd(adId) {
    if (confirm('Are you sure you want to delete this advertisement? This action cannot be undone.')) {
        console.log('Deleting ad:', adId);
        // TODO: Send AJAX request to delete ad
        alert(`Ad ${adId} has been deleted successfully.`);
        // Remove card from DOM
        const card = document.querySelector(`.ad-card[data-id="${adId}"]`);
        if (card) {
            card.remove();
            updateFilterCounts();
            updateResultCount();
        }
    }
}

function cancelAd(adId) {
    if (confirm('Are you sure you want to cancel this advertisement? If you paid for it, a refund will be processed.')) {
        console.log('Canceling ad:', adId);
        // TODO: Send AJAX request to cancel ad
        alert(`Ad ${adId} has been canceled successfully. Refund will be processed within 3-5 business days.`);
        // Remove or update card
        deleteAd(adId);
    }
}

function startAdNow(adId) {
    if (confirm('Start this advertisement now instead of waiting for the scheduled date?')) {
        console.log('Starting ad now:', adId);
        // TODO: Send AJAX request to start ad immediately
        alert(`Ad ${adId} is now live and showing to users!`);
        // Update UI
        updateAdStatus(adId, 'active');
    }
}

function renewAd(adId) {
    console.log('Renewing ad:', adId);
    // TODO: Open payment modal or redirect to renewal page
    alert(`Renew Ad - Ad ID: ${adId}\n\nThis will open the renewal form where you can extend the advertisement duration.`);
}

function duplicateAd(adId) {
    console.log('Duplicating ad:', adId);
    // TODO: Copy ad data and open create form
    alert(`Duplicate Ad - Ad ID: ${adId}\n\nThis will create a copy of this advertisement with all the same settings.`);
}

function archiveAd(adId) {
    if (confirm('Archive this advertisement? You can view it in your archived ads later.')) {
        console.log('Archiving ad:', adId);
        // TODO: Send AJAX request to archive ad
        alert(`Ad ${adId} has been archived successfully.`);
        // Remove card from DOM
        const card = document.querySelector(`.ad-card[data-id="${adId}"]`);
        if (card) {
            card.remove();
            updateFilterCounts();
            updateResultCount();
        }
    }
}

function viewAdReport(adId) {
    console.log('Viewing report for ad:', adId);
    // TODO: Redirect to detailed analytics page
    alert(`View Report - Ad ID: ${adId}\n\nThis will show detailed performance analytics, charts, and insights.`);
}

// Helper function to update ad status
function updateAdStatus(adId, newStatus) {
    const card = document.querySelector(`.ad-card[data-id="${adId}"]`);
    if (card) {
        // Update data attribute
        card.dataset.status = newStatus;
        
        // Update status badge
        const statusBadge = card.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.className = 'status-badge';
            statusBadge.classList.add(`status-${newStatus}`);
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
        }
        
        // Update action buttons based on new status
        updateActionButtons(card, newStatus);
        
        // Update counts
        updateFilterCounts();
        
        // Re-apply filters
        applyAllFilters();
    }
}

// Update action buttons based on status
function updateActionButtons(card, status) {
    const actionsDiv = card.querySelector('.ad-actions');
    if (!actionsDiv) return;
    
    const adId = card.dataset.id;
    
    let buttonsHTML = '';
    
    switch(status) {
        case 'active':
            buttonsHTML = `
                <button class="action-btn-ad view" onclick="viewAdReport('${adId}')">
                    <i class="fas fa-chart-line"></i> Analytics
                </button>
                <button class="action-btn-ad edit" onclick="editAd('${adId}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn-ad pause" onclick="pauseAd('${adId}')">
                    <i class="fas fa-pause"></i> Pause
                </button>
                <button class="action-btn-ad delete" onclick="deleteAd('${adId}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            `;
            break;
            
        case 'pending':
            buttonsHTML = `
                <button class="action-btn-ad view" onclick="viewAdDetails('${adId}')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="action-btn-ad edit" onclick="editAd('${adId}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn-ad warning" onclick="cancelAd('${adId}')">
                    <i class="fas fa-times"></i> Cancel
                </button>
            `;
            break;
            
        case 'scheduled':
            buttonsHTML = `
                <button class="action-btn-ad view" onclick="viewAdDetails('${adId}')">
                    <i class="fas fa-eye"></i> View
                </button>
                <button class="action-btn-ad edit" onclick="editAd('${adId}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn-ad success" onclick="startAdNow('${adId}')">
                    <i class="fas fa-play"></i> Start Now
                </button>
                <button class="action-btn-ad warning" onclick="cancelAd('${adId}')">
                    <i class="fas fa-times"></i> Cancel
                </button>
            `;
            break;
            
        case 'paused':
            buttonsHTML = `
                <button class="action-btn-ad success" onclick="resumeAd('${adId}')">
                    <i class="fas fa-play"></i> Resume
                </button>
                <button class="action-btn-ad view" onclick="viewAdReport('${adId}')">
                    <i class="fas fa-chart-line"></i> Analytics
                </button>
                <button class="action-btn-ad edit" onclick="editAd('${adId}')">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="action-btn-ad delete" onclick="deleteAd('${adId}')">
                    <i class="fas fa-trash"></i> Delete
                </button>
            `;
            break;
            
        case 'expired':
            buttonsHTML = `
                <button class="action-btn-ad view" onclick="viewAdReport('${adId}')">
                    <i class="fas fa-chart-bar"></i> Report
                </button>
                <button class="action-btn-ad success" onclick="renewAd('${adId}')">
                    <i class="fas fa-redo"></i> Renew
                </button>
                <button class="action-btn-ad edit" onclick="duplicateAd('${adId}')">
                    <i class="fas fa-copy"></i> Duplicate
                </button>
                <button class="action-btn-ad warning" onclick="archiveAd('${adId}')">
                    <i class="fas fa-archive"></i> Archive
                </button>
            `;
            break;
    }
    
    actionsDiv.innerHTML = buttonsHTML;
}
