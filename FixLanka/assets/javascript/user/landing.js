// Fix Lanka Landing Page JavaScript
// ===================================

// App + API endpoints
const APP_BASE = '/2nd-Year-Group-Project/FixLanka';
const API_ENDPOINTS = Object.freeze({
    providerSearch: `${APP_BASE}/api/user/loadLandingProviders.php`,
    companies: `${APP_BASE}/api/companies.php`,
    listedJobs: `${APP_BASE}/api/user/listed-job-requests.php`
});

/*
|-------------------------------------------------------------------------------
| Landing Providers Data Flow (Quick Reference)
|-------------------------------------------------------------------------------
| 1) API is called from: loadProviders()
| 2) Request URL: API_ENDPOINTS.providerSearch (/api/user/loadLandingProviders.php)
| 3) Raw API response is stored in: lastLandingProvidersResponse
| 4) Providers array (result.data) is stored in: lastLandingProvidersBatch
| 5) Rendering happens via:
|    - renderProviderCard(provider)  -> repairers grid
|    - renderCompanyCard(company)    -> companies grid
*/

const LANDING_PROVIDER_BLUEPRINTS = Object.freeze({
    repairer: {
        source: 'Repairer table',
        identity: ['repairer_id', 'provider_type'],
        primaryDisplay: ['full_name', 'category_name'],
        ratingAndStats: ['ratings', 'completedJobsCount'],
        media: ['profilePicture'],
        location: ['districts', 'address'],
        contact: ['phoneNumber', 'email'],
        detailText: ['about', 'availability']
    },
    company: {
        source: 'Company table',
        identity: ['company_id', 'provider_type'],
        primaryDisplay: ['name', 'business_type'],
        ratingAndStats: ['ratings', 'date_of_joined'],
        location: ['districts', 'address'],
        contact: ['contact_no', 'email', 'website'],
        detailText: ['description']
    }
});

// Latest payload storage (before populating grids)
let lastLandingProvidersResponse = null;
let lastLandingProvidersBatch = [];

// DOM Elements
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
const searchForm = document.getElementById('searchForm');
const repairersGrid = document.getElementById('repairersGrid');
const companiesGrid = document.getElementById('companiesGrid');
const loadingIndicator = document.getElementById('loadingIndicator');
const scrollTrigger = document.getElementById('scrollTrigger');
const profileAvatar = document.getElementById('profileAvatar');
const profileDropdown = document.getElementById('profileDropdown');
const providerTabs = document.querySelectorAll('.provider-tab');
const listedJobRequestModal = document.getElementById('listedJobRequestModal');
const listedJobRequestCloseBtn = document.getElementById('listedJobRequestCloseBtn');
const listedJobRequestCancelBtn = document.getElementById('listedJobRequestCancelBtn');
const listedJobRequestSubmitBtn = document.getElementById('listedJobRequestSubmitBtn');
const listedJobRequestList = document.getElementById('listedJobRequestList');
const listedJobRequestError = document.getElementById('listedJobRequestError');
const listedJobRequestProviderTypeLabel = document.getElementById('listedJobRequestProviderTypeLabel');

// Active grid pointer (used by loader + no-results helpers)
let providersGrid = repairersGrid;

// State variables
let currentPage = 0;
const itemsPerPage = 6;
let isLoading = false;
let allProvidersLoaded = false;
let currentProviderType = 'repairers';
const landingRepairersById = new Map();
let selectedListedJobRequestId = null;
let listedJobRequestContext = {
    providerId: null,
    providerType: null
};

function cacheLandingRepairers(providers) {
    if (!Array.isArray(providers)) return;

    providers.forEach((provider) => {
        const type = normalizeProviderType(provider?.provider_type || currentProviderType);
        if (type !== 'individual') return;

        const repairerId = Number(provider?.repairer_id);
        if (!Number.isFinite(repairerId) || repairerId <= 0) return;

        landingRepairersById.set(repairerId, provider);
    });
}

function getLandingRepairerById(repairerId) {
    const numericId = Number(repairerId);
    if (!Number.isFinite(numericId) || numericId <= 0) return null;
    return landingRepairersById.get(numericId) || null;
}

window.getLandingRepairerById = getLandingRepairerById;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeSearchForm();
    initializeLazyLoading();
    initializeProfileDropdown();
    initializeProviderTabs();
    initializeListedJobRequestModal();
    loadInitialProviders();
});

function initializeListedJobRequestModal() {
    if (!listedJobRequestModal) return;

    if (listedJobRequestCloseBtn) {
        listedJobRequestCloseBtn.addEventListener('click', closeListedJobRequestModal);
    }

    if (listedJobRequestCancelBtn) {
        listedJobRequestCancelBtn.addEventListener('click', closeListedJobRequestModal);
    }

    if (listedJobRequestSubmitBtn) {
        listedJobRequestSubmitBtn.addEventListener('click', submitListedJobRequest);
    }

    if (listedJobRequestList) {
        listedJobRequestList.addEventListener('change', (event) => {
            const target = event.target;
            if (!target || target.name !== 'listedJobRequestId') return;

            selectedListedJobRequestId = Number(target.value);
            if (listedJobRequestSubmitBtn) {
                listedJobRequestSubmitBtn.disabled = !Number.isFinite(selectedListedJobRequestId) || selectedListedJobRequestId <= 0;
            }
        });
    }

    listedJobRequestModal.addEventListener('click', (event) => {
        if (event.target === listedJobRequestModal) {
            closeListedJobRequestModal();
        }
    });
}

function closeListedJobRequestModal() {
    if (!listedJobRequestModal) return;

    listedJobRequestModal.classList.remove('show');
    listedJobRequestModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    selectedListedJobRequestId = null;
    listedJobRequestContext = { providerId: null, providerType: null };

    if (listedJobRequestSubmitBtn) {
        listedJobRequestSubmitBtn.disabled = true;
    }

    if (listedJobRequestError) {
        listedJobRequestError.style.display = 'none';
        listedJobRequestError.textContent = '';
    }

    if (listedJobRequestList) {
        listedJobRequestList.innerHTML = '<p class="listed-job-placeholder">Loading your pending jobs...</p>';
    }
}

async function openListedJobRequestModal(providerType, providerId) {
    if (!listedJobRequestModal || !listedJobRequestList) {
        alert('Listed-job request popup is not available right now.');
        return;
    }

    const normalizedType = normalizeProviderType(providerType) === 'company' ? 'company' : 'individual';
    const numericProviderId = Number(providerId);

    listedJobRequestContext = {
        providerId: Number.isFinite(numericProviderId) && numericProviderId > 0 ? numericProviderId : null,
        providerType: normalizedType
    };

    selectedListedJobRequestId = null;
    if (listedJobRequestSubmitBtn) listedJobRequestSubmitBtn.disabled = true;

    if (listedJobRequestProviderTypeLabel) {
        listedJobRequestProviderTypeLabel.textContent = normalizedType === 'company' ? 'this company' : 'this repairer';
    }

    if (listedJobRequestError) {
        listedJobRequestError.style.display = 'none';
        listedJobRequestError.textContent = '';
    }

    listedJobRequestList.innerHTML = '<p class="listed-job-placeholder">Loading your pending jobs...</p>';

    listedJobRequestModal.classList.add('show');
    listedJobRequestModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    try {
        const params = new URLSearchParams();
        params.set('provider_type', normalizedType);

        const response = await fetch(`${API_ENDPOINTS.listedJobs}?${params.toString()}`, {
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();
        if (!response.ok || !result?.success) {
            throw new Error(result?.message || `Failed to load pending jobs (${response.status})`);
        }

        renderListedJobRequestOptions(Array.isArray(result.data) ? result.data : []);
    } catch (error) {
        console.error('Failed to load listed job requests:', error);
        if (listedJobRequestError) {
            listedJobRequestError.textContent = error.message || 'Failed to load your listed jobs.';
            listedJobRequestError.style.display = 'block';
        }
        listedJobRequestList.innerHTML = '<p class="listed-job-placeholder">Could not load jobs. Please try again.</p>';
    }
}

function renderListedJobRequestOptions(jobs) {
    if (!listedJobRequestList) return;

    if (!jobs.length) {
        listedJobRequestList.innerHTML = '<p class="listed-job-placeholder">No pending listed jobs match this provider type.</p>';
        return;
    }

    const html = jobs.map((job) => {
        const requestId = Number(job.request_id);
        const title = escapeHtml(String(job.title || `Job #${requestId}`));
        const category = escapeHtml(String(job.category_name || 'Uncategorized'));
        const district = escapeHtml(String(job.district || 'N/A'));
        const finishDate = escapeHtml(String(job.finish_date || 'N/A'));

        return `
            <div class="listed-job-item">
                <label>
                    <input type="radio" name="listedJobRequestId" value="${requestId}">
                    <span>
                        <p class="listed-job-item-title">${title}</p>
                        <p class="listed-job-item-meta">Category: ${category} | District: ${district} | Finish by: ${finishDate}</p>
                    </span>
                </label>
            </div>
        `;
    }).join('');

    listedJobRequestList.innerHTML = html;
}

function submitListedJobRequest() {
    if (!Number.isFinite(selectedListedJobRequestId) || selectedListedJobRequestId <= 0) return;
    if (!listedJobRequestContext.providerType || !listedJobRequestContext.providerId) return;

    alert(
        `Listed-job request submission will be implemented next.\n\n` +
        `Job Request ID: ${selectedListedJobRequestId}\n` +
        `Provider Type: ${listedJobRequestContext.providerType}\n` +
        `Provider ID: ${listedJobRequestContext.providerId}`
    );

    closeListedJobRequestModal();
}

// Mobile Menu Functionality
function initializeMobileMenu() {
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            
            // Animate hamburger menu
            const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
            hamburgers.forEach((line, index) => {
                if (mobileMenu.classList.contains('active')) {
                    if (index === 0) line.style.transform = 'rotate(45deg) translate(5px, 5px)';
                    if (index === 1) line.style.opacity = '0';
                    if (index === 2) line.style.transform = 'rotate(-45deg) translate(7px, -6px)';
                } else {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                }
            });
        });

        // Close mobile menu when clicking on links
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                // Reset hamburger animation
                const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
                hamburgers.forEach(line => {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                });
            });
        });
    }
}

// Profile Dropdown Functionality
function initializeProfileDropdown() {
    if (profileAvatar && profileDropdown) {
        // Toggle dropdown when clicking profile avatar
        profileAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileAvatar.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }
        });
    }
}

// Provider Tabs Functionality
function initializeProviderTabs() {
    providerTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const type = this.dataset.type;
            
            // Update active tab
            providerTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding grid
            document.querySelectorAll('.providers-grid').forEach(grid => {
                grid.classList.remove('active');
            });
            
            if (type === 'repairers') {
                repairersGrid.classList.add('active');
                currentProviderType = 'repairers';
                providersGrid = repairersGrid;
            } else {
                companiesGrid.classList.add('active');
                currentProviderType = 'companies';
                providersGrid = companiesGrid;
            }
            
            // Reset and reload data
            currentPage = 0;
            allProvidersLoaded = false;
            
            const targetGrid = type === 'repairers' ? repairersGrid : companiesGrid;
            targetGrid.innerHTML = '';
            
            loadInitialProviders();
        });
    });
}

// Search Form Functionality
function initializeSearchForm() {
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Apply filters via API
            filterProviders();
            updateActiveFilters();
        });
        
        // Also trigger filter on dropdown change
        const filterInputs = ['serviceSelect', 'ratingSelect', 'districtSelect'];
        filterInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('change', function() {
                    filterProviders();
                    updateActiveFilters();
                });
            }
        });
        
        // Clear filters button
        const clearBtn = document.getElementById('clearFiltersBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                clearAllFilters();
            });
        }
    }
}

// Update active filters display
function updateActiveFilters() {
    const activeFiltersDiv = document.getElementById('activeFilters');
    const filterTagsDiv = document.getElementById('filterTags');
    
    if (!activeFiltersDiv || !filterTagsDiv) return;
    
    const service = document.getElementById('serviceSelect');
    const rating = document.getElementById('ratingSelect');
    const district = document.getElementById('districtSelect');
    
    let hasFilters = false;
    filterTagsDiv.innerHTML = '';
    
    // Add service tag
    if (service && service.value) {
        hasFilters = true;
        const tag = createFilterTag('Service', service.options[service.selectedIndex].text, () => {
            service.value = '';
            filterProviders();
            updateActiveFilters();
        });
        filterTagsDiv.appendChild(tag);
    }
    
    // Add district tag
    if (district && district.value) {
        hasFilters = true;
        const tag = createFilterTag('District', district.value, () => {
            district.value = '';
            filterProviders();
            updateActiveFilters();
        });
        filterTagsDiv.appendChild(tag);
    }
    
    // Add rating tag
    if (rating && rating.value) {
        hasFilters = true;
        const tag = createFilterTag('Rating', rating.options[rating.selectedIndex].text, () => {
            rating.value = '';
            filterProviders();
            updateActiveFilters();
        });
        filterTagsDiv.appendChild(tag);
    }
    
    activeFiltersDiv.style.display = hasFilters ? 'flex' : 'none';
}

// Create filter tag element
function createFilterTag(label, value, onRemove) {
    const tag = document.createElement('span');
    tag.className = 'filter-tag';
    tag.innerHTML = `
        <span class="filter-tag-label">${label}:</span>
        <span class="filter-tag-value">${value}</span>
        <button class="filter-tag-remove" aria-label="Remove filter">×</button>
    `;
    
    const removeBtn = tag.querySelector('.filter-tag-remove');
    removeBtn.addEventListener('click', onRemove);
    
    return tag;
}

// Clear all filters
function clearAllFilters() {
    const service = document.getElementById('serviceSelect');
    const rating = document.getElementById('ratingSelect');
    const district = document.getElementById('districtSelect');
    
    if (service) service.value = '';
    if (rating) rating.value = '';
    if (district) district.value = '';
    
    filterProviders();
    updateActiveFilters();
}

// Search Loading States
function showSearchLoading() {
    const searchBtn = document.querySelector('.search-btn');
    if (searchBtn) {
        searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
        searchBtn.disabled = true;
    }
}

function hideSearchLoading() {
    const searchBtn = document.querySelector('.search-btn');
    if (searchBtn) {
        searchBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filters';
        searchBtn.disabled = false;
    }
}

// Filter Providers - now uses real API
async function filterProviders() {
    // Clear current grid and reset pagination
    if (providersGrid) {
        providersGrid.innerHTML = '';
    }
    currentPage = 0;
    allProvidersLoaded = false;
    if (scrollTrigger) {
        scrollTrigger.style.display = 'block';
    }
    
    // Show loading state
    showSearchLoading();
    
    // Load filtered results from API
    await loadProviders();
    
    // Hide loading state
    hideSearchLoading();
}

// Lazy Loading Functionality
function initializeLazyLoading() {
    // Create Intersection Observer for lazy loading
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !isLoading && !allProvidersLoaded) {
                loadProviders();
            }
        });
    }, {
        rootMargin: '100px' // Start loading when 100px away from trigger
    });

    // Observe the scroll trigger element
    if (scrollTrigger) {
        observer.observe(scrollTrigger);
    }
}

// Load Initial Providers
function loadInitialProviders() {
    loadProviders();
}

// Load Providers with Pagination
async function loadProviders() {
    if (isLoading || allProvidersLoaded) return;

    isLoading = true;
    showLoading();

    try {
        const service = document.getElementById('serviceSelect')?.value || '';
        const rating = document.getElementById('ratingSelect')?.value || '';
        const district = document.getElementById('districtSelect')?.value || '';

        const providerType = currentProviderType === 'companies' ? 'company' : 'individual';
        const hasFilters = Boolean(service || rating || district);

        const params = new URLSearchParams();
        params.set('mode', hasFilters ? 'search' : 'featured');
        params.set('provider_type', providerType);
        params.set('limit', String(itemsPerPage));
        params.set('offset', String(currentPage * itemsPerPage));

        if (service) params.set('category', service);
        if (rating) params.set('rating', rating);
        if (district) params.set('location', district);

        const apiUrl = `${API_ENDPOINTS.providerSearch}?${params.toString()}`;
        const response = await fetch(apiUrl, {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch providers (${response.status})`);
        }

        const result = await response.json();
        const providers = (result && result.success && Array.isArray(result.data)) ? result.data : [];

        // Store API payload before rendering (useful for filter/debug decisions)
        lastLandingProvidersResponse = result;
        lastLandingProvidersBatch = providers;
        cacheLandingRepairers(providers);

        if (providers.length === 0 && currentPage === 0) {
            showNoResults();
            allProvidersLoaded = true;
            if (scrollTrigger) scrollTrigger.style.display = 'none';
            return;
        }

        hideNoResults();

        providers.forEach((provider, index) => {
            setTimeout(() => {
                if (providerType === 'company') {
                    renderCompanyCard(provider);
                } else {
                    renderProviderCard(provider);
                }
            }, index * 80);
        });

        currentPage++;

        if (result.pagination) {
            allProvidersLoaded = !result.pagination.hasMore;
        } else if (providers.length < itemsPerPage) {
            allProvidersLoaded = true;
        }

        if (allProvidersLoaded && scrollTrigger) {
            scrollTrigger.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading providers:', error);
        if (currentPage === 0) {
            showNoResults();
        }
        allProvidersLoaded = true;
        if (scrollTrigger) {
            scrollTrigger.style.display = 'none';
        }
    } finally {
        isLoading = false;
        hideLoading();
    }
}

// Show Loading Indicator
function showLoading() {
    if (loadingIndicator) {
        loadingIndicator.classList.add('show');
    }
}

// Hide Loading Indicator
function hideLoading() {
    if (loadingIndicator) {
        loadingIndicator.classList.remove('show');
    }
}

// Show No Results Message
function showNoResults() {
    if (!providersGrid) return;
    
    const noResultsDiv = document.createElement('div');
    noResultsDiv.id = 'noResultsMessage';
    noResultsDiv.className = 'no-results';
    noResultsDiv.innerHTML = `
        <div class="no-results-icon">
            <i class="fas fa-search"></i>
        </div>
        <h3>No Providers Found</h3>
        <p>We couldn't find any providers matching your criteria.</p>
        <p>Try adjusting your filters or clearing them to see all providers.</p>
        <button class="btn-primary" onclick="clearAllFilters()">Clear All Filters</button>
    `;
    
    providersGrid.appendChild(noResultsDiv);
}

// Hide No Results Message
function hideNoResults() {
    const noResultsMsg = document.getElementById('noResultsMessage');
    if (noResultsMsg) {
        noResultsMsg.remove();
    }
}

// Render Provider Card
function renderProviderCard(provider) {
    const card = document.createElement('div');
    card.className = 'provider-card';
    card.style.animationDelay = '0s'; // Reset animation delay

    const pictureUrl = resolveAssetUrl(provider.profilePicture);
    
    // Generate avatar initials or use profile picture
    const avatar = pictureUrl
        ? `<img src="${pictureUrl}" alt="${provider.full_name || provider.name}" class="avatar-img">`
        : generateAvatarInitials(provider.full_name || provider.name);
    
    // Determine provider type and display info
    const providerType = normalizeProviderType(provider.provider_type || 'individual');
    const providerName = provider.full_name || provider.name || 'Unknown';
    const providerTitle = provider.category_name || (providerType === 'company' ? 'Service Company' : 'Service Provider');
    const rating = Number(provider.ratings ?? provider.rating ?? 0);
    const completedJobs = Number(provider.completedJobsCount ?? 0);
    const about = provider.about || provider.address || 'Professional service provider';
    const availability = provider.availability || 'available';
    const serviceAreas = String(provider.districts || provider.address || 'Available in your area');
    
    // Provider ID and type for viewing details
    const providerId = providerType === 'individual' ? provider.repairer_id : provider.company_id;
    const safeProviderId = Number(providerId);
    if (DEBUG_PROFILE_FLOW) {
        console.log('[ProfileFlow] renderProviderCard()', {
            providerTypeRaw: provider.provider_type,
            providerType,
            providerId,
            safeProviderId,
            providerName
        });
    }
    
    card.innerHTML = `
        <div class="provider-header">
            <div class="provider-avatar">
                ${avatar}
            </div>
            <div class="provider-info">
                <h3 class="provider-name">${escapeHtml(providerName)}</h3>
                <p class="provider-title">${escapeHtml(providerTitle)}</p>
                ${providerType === 'company' ? '<span class="provider-badge company-badge">Company</span>' : ''}
                ${availability === 'available' ? '<span class="provider-badge available-badge">Available</span>' : ''}
            </div>
        </div>
        
        <div class="provider-rating">
            <div class="stars">
                ${generateStars(rating)}
            </div>
            <span class="rating-text">${(Number.isFinite(rating) ? rating : 0).toFixed(1)} ${completedJobs > 0 ? `(${completedJobs} jobs)` : ''}</span>
        </div>
        
        <div class="provider-distance">
            <i class="fas fa-map-marker-alt"></i>
            ${escapeHtml(serviceAreas.substring(0, 50))}${serviceAreas.length > 50 ? '...' : ''}
        </div>
        
        <p class="provider-description">
            ${escapeHtml(about.substring(0, 120))}${about.length > 120 ? '...' : ''}
        </p>
        
        <div class="provider-actions">
            <button class="view-profile-btn" data-provider-id="${escapeHtml(String(safeProviderId || ''))}" data-provider-type="${escapeHtml(providerType)}" onclick="viewProviderProfile(${safeProviderId || 'null'}, '${providerType}')">
                <i class="fas fa-user"></i>
                View Profile
            </button>
            ${providerType === 'individual' && provider.phoneNumber ? 
                `<a href="tel:${provider.phoneNumber}" class="contact-btn">
                    <i class="fas fa-phone"></i>
                </a>` : ''}
        </div>
    `;
    
    if (providersGrid) {
        providersGrid.appendChild(card);
    }
}

// Render Company Card
function renderCompanyCard(company) {
    // Render API company cards
    const card = document.createElement('div');
    card.className = 'company-card';
    card.style.animationDelay = '0s';

    const companyName = company.name || 'Company';
    const companyType = company.business_type || 'Service Company';
    const rating = Number(company.ratings ?? company.rating ?? 0);
    const location = company.districts || company.address || '';
    const description = company.description || 'Professional service company';
    const founded = company.date_of_joined ? String(company.date_of_joined).slice(0, 4) : '-';

    card.innerHTML = `
        <div class="company-header">
            <div class="company-logo">${generateAvatarInitials(companyName)}</div>
            <div class="company-info">
                <h3 class="company-name">${escapeHtml(companyName)}</h3>
                <span class="company-type">${escapeHtml(companyType)}</span>
            </div>
        </div>

        <div class="company-stats">
            <div class="company-stat">
                <span class="stat-value">${rating.toFixed(1)}</span>
                <span class="stat-label">Rating</span>
            </div>
            <div class="company-stat">
                <span class="stat-value">${escapeHtml(String((company.districts || '').split(',').filter(Boolean).length || 1))}</span>
                <span class="stat-label">Areas</span>
            </div>
            <div class="company-stat">
                <span class="stat-value">${escapeHtml(founded)}</span>
                <span class="stat-label">Joined</span>
            </div>
        </div>

        <div class="company-rating">
            <div class="stars">${generateStars(rating)}</div>
            <span class="rating-text">${rating.toFixed(1)}</span>
        </div>

        <div class="company-location">
            <i class="fas fa-map-marker-alt"></i>
            ${escapeHtml(String(location).substring(0, 60))}${String(location).length > 60 ? '...' : ''}
        </div>

        <p class="company-description">${escapeHtml(String(description).substring(0, 140))}${String(description).length > 140 ? '...' : ''}</p>

        <div class="company-actions">
            <button class="view-company-btn" data-provider-id="${escapeHtml(String(company.company_id ?? ''))}" data-provider-type="company" onclick="viewProviderProfile(${Number(company.company_id) || 'null'}, 'company')">
                <i class="fas fa-building"></i>
                View Company Details
            </button>
        </div>
    `;

    if (providersGrid) {
        providersGrid.appendChild(card);
    }
}

function resolveAssetUrl(path) {
    if (!path) return '';
    const trimmed = String(path).trim();
    if (!trimmed) return '';
    if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) return trimmed;
    if (trimmed.startsWith('/')) return trimmed;
    return `${APP_BASE}/${trimmed}`;
}

const DEBUG_PROFILE_FLOW = true;

function normalizeProviderType(type) {
    const value = String(type || '').trim().toLowerCase();
    if (value === 'individual' || value === 'repairer' || value === 'repairers') return 'individual';
    if (value === 'company' || value === 'companies') return 'company';
    return value || 'individual';
}

// Unified handler used by cards; repairer popup is mock-based for now
function viewProviderProfile(providerId, providerType) {
    const normalizedType = normalizeProviderType(providerType);
    const numericId = Number(providerId);

    if (DEBUG_PROFILE_FLOW) {
        console.groupCollapsed('[ProfileFlow] viewProviderProfile()');
        console.log('providerId (raw):', providerId);
        console.log('providerType (raw):', providerType);
        console.log('providerType (normalized):', normalizedType);
        console.log('providerId (number):', numericId);
        console.log('openRepairerProfile available:', typeof openRepairerProfile);
        console.groupEnd();
    }

    if (!Number.isFinite(numericId) || numericId <= 0) {
        console.error('[ProfileFlow] Invalid providerId, aborting:', providerId, providerType);
        return;
    }

    if (normalizedType === 'individual') {
        if (typeof openRepairerProfile === 'function') {
            const repairer = getLandingRepairerById(numericId);
            openRepairerProfile(numericId, repairer);
        } else {
            console.error('[ProfileFlow] openRepairerProfile() is not available. Check script load order for repairer-profile-popup.js');
            alert('Repairer profile popup is not available right now.');
        }
        return;
    }

    openCompanyProfile(numericId);
}

// Make sure inline onclick can always find it
window.viewProviderProfile = viewProviderProfile;

// Defensive: handle clicks even if inline handlers break
document.addEventListener('click', (e) => {
    const btn = e.target?.closest?.('.view-profile-btn, .view-company-btn');
    if (!btn) return;

    // Prefer data-* if present
    const dataId = btn.getAttribute('data-provider-id');
    const dataType = btn.getAttribute('data-provider-type');

    if (DEBUG_PROFILE_FLOW) {
        console.log('[ProfileFlow] Click detected on view button', { dataId, dataType, className: btn.className });
    }

    if (dataId) {
        e.preventDefault();
        viewProviderProfile(dataId, dataType || (btn.classList.contains('view-company-btn') ? 'company' : 'individual'));
    }
});

// Generate avatar initials from name
function generateAvatarInitials(name) {
    if (!name) return 'SP';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Generate Star Rating HTML
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let starsHTML = '';
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<i class="fas fa-star star"></i>';
    }
    
    // Half star
    if (hasHalfStar) {
        starsHTML += '<i class="fas fa-star-half-alt star"></i>';
    }
    
    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<i class="far fa-star star empty"></i>';
    }
    
    return starsHTML;
}

// Smooth Scrolling for Navigation Links
document.addEventListener('click', function(e) {
    const target = e.target;
    
    // Handle navigation links with hash
    if (target.matches('a[href^="#"]')) {
        e.preventDefault();
        const targetId = target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

// Handle Window Resize
window.addEventListener('resize', function() {
    // Close mobile menu on resize to larger screen
    if (window.innerWidth > 768) {
        const mobileMenu = document.querySelector('.mobile-menu');
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            
            // Reset hamburger animation
            if (mobileMenuToggle) {
                const hamburgers = mobileMenuToggle.querySelectorAll('.hamburger');
                hamburgers.forEach(line => {
                    line.style.transform = 'none';
                    line.style.opacity = '1';
                });
            }
        }
    }
    
    // Close profile dropdown on resize
    if (profileDropdown && profileDropdown.classList.contains('active')) {
        profileDropdown.classList.remove('active');
    }
});

// Add scroll-based navbar styling (optional enhancement)
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.backdropFilter = 'blur(10px)';
        } else {
            navbar.style.background = 'var(--bg-card)';
            navbar.style.backdropFilter = 'none';
        }
    }
});

// Provider Type Filter Function
function filterByProviderType(type, buttonElement) {
    // Update current provider type
    currentProviderType = type;
    
    // Update button active states
    const allButtons = document.querySelectorAll('.provider-type-btn');
    allButtons.forEach(btn => btn.classList.remove('active'));
    buttonElement.classList.add('active');
    
    // Clear current providers and reset pagination
    if (providersGrid) {
        providersGrid.innerHTML = '';
    }
    currentPage = 0;
    allProvidersLoaded = false;
    if (scrollTrigger) {
        scrollTrigger.style.display = 'block';
    }
    
    // Reload providers with new filter
    loadProviders();
}

// Send Repair Request Function (Placeholder - No functionality yet)
function sendRepairRequest(type, providerId) {
    openListedJobRequestModal(type, providerId);
}

// Request Company Quote Function (Placeholder)
function requestCompanyQuote(companyId) {
    
    alert('Request Company Quote feature will be implemented soon!');
    // TODO: Implement company quote request functionality
}

// Request Repairer Quote / New Job Function (Placeholder)
function requestRepairerQuote(repairerId) {
    alert(`Request new job feature for this repairer will be implemented soon!\n\nRepairer ID: ${repairerId || 'Unknown'}`);
    // TODO: Implement new job request flow for a specific repairer
}

// Console log for debugging


