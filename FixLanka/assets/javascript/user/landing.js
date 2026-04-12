// Fix Lanka Landing Page JavaScript
// ===================================

// App + API endpoints
const APP_BASE = '/2nd-Year-Group-Project/FixLanka';
const API_ENDPOINTS = Object.freeze({
    providerSearch: `${APP_BASE}/api/user/loadLandingProviders.php`,
    companies: `${APP_BASE}/api/companies.php`,
    listedJobs: `${APP_BASE}/api/user/listed-job-requests.php`,
    directRequestQuotes: `${APP_BASE}/api/user/direct-request-quotes.php`,
    directJobRequests: `${APP_BASE}/api/user/direct-job-requests.php`
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
const directJobRequestModal = document.getElementById('directJobRequestModal');
const directJobRequestCloseBtn = document.getElementById('directJobRequestCloseBtn');
const directJobRequestCancelBtn = document.getElementById('directJobRequestCancelBtn');
const directJobRequestForm = document.getElementById('directJobRequestForm');
const directJobRequestError = document.getElementById('directJobRequestError');
const directJobRequestSuccess = document.getElementById('directJobRequestSuccess');
const directJobRequestProviderLabel = document.getElementById('directJobRequestProviderLabel');
const directJobProviderId = document.getElementById('directJobProviderId');
const directJobProviderType = document.getElementById('directJobProviderType');
const directJobFinishDate = document.getElementById('directJobFinishDate');
const directJobPhotos = document.getElementById('directJobPhotos');
const directJobPhotoPreview = document.getElementById('directJobPhotoPreview');
const directJobRequestSubmitBtn = document.getElementById('directJobRequestSubmitBtn');

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
let directJobRequestContext = {
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
document.addEventListener('DOMContentLoaded', function () {
    initializeMobileMenu();
    initializeSearchForm();
    initializeLazyLoading();
    initializeProfileDropdown();
    initializeProviderTabs();
    initializeListedJobRequestModal();
    initializeDirectJobRequestModal();
    loadInitialProviders();
});

function initializeDirectJobRequestModal() {
    if (!directJobRequestModal || !directJobRequestForm) return;

    if (directJobRequestCloseBtn) {
        directJobRequestCloseBtn.addEventListener('click', closeDirectJobRequestModal);
    }

    if (directJobRequestCancelBtn) {
        directJobRequestCancelBtn.addEventListener('click', closeDirectJobRequestModal);
    }

    directJobRequestModal.addEventListener('click', (event) => {
        if (event.target === directJobRequestModal) {
            closeDirectJobRequestModal();
        }
    });

    directJobRequestForm.addEventListener('submit', submitDirectJobRequest);

    if (directJobFinishDate) {
        const today = new Date().toISOString().split('T')[0];
        directJobFinishDate.setAttribute('min', today);
    }

    if (directJobPhotos && directJobPhotoPreview) {
        directJobPhotos.addEventListener('change', () => {
            const file = directJobPhotos.files && directJobPhotos.files[0] ? directJobPhotos.files[0] : null;
            directJobPhotoPreview.textContent = file ? `Selected: ${file.name}` : '';
        });
    }
}

function closeDirectJobRequestModal() {
    if (!directJobRequestModal || !directJobRequestForm) return;

    directJobRequestModal.classList.remove('show');
    directJobRequestModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';

    directJobRequestForm.reset();
    directJobRequestContext = { providerId: null, providerType: null };

    if (directJobProviderId) directJobProviderId.value = '';
    if (directJobProviderType) directJobProviderType.value = '';
    if (directJobPhotoPreview) directJobPhotoPreview.textContent = '';

    if (directJobRequestError) {
        directJobRequestError.style.display = 'none';
        directJobRequestError.textContent = '';
    }
    if (directJobRequestSuccess) {
        directJobRequestSuccess.style.display = 'none';
        directJobRequestSuccess.textContent = '';
    }

    if (directJobRequestSubmitBtn) {
        directJobRequestSubmitBtn.disabled = false;
        directJobRequestSubmitBtn.textContent = 'Request';
    }
}

function openDirectJobRequestModal(providerType, providerId) {
    if (!directJobRequestModal || !directJobRequestForm) {
        alert('New job request popup is not available right now.');
        return;
    }

    const normalizedType = normalizeProviderType(providerType) === 'company' ? 'company' : 'individual';
    const numericProviderId = Number(providerId);

    if (!Number.isFinite(numericProviderId) || numericProviderId <= 0) {
        alert('Invalid provider selection. Please reopen the profile and try again.');
        return;
    }

    // Close profile popups first as requested.
    if (normalizedType === 'company' && typeof window.closeCompanyModal === 'function') {
        window.closeCompanyModal();
    }
    if (normalizedType === 'individual' && typeof window.closeRepairerProfile === 'function') {
        window.closeRepairerProfile();
    }

    directJobRequestContext = {
        providerId: numericProviderId,
        providerType: normalizedType
    };

    directJobRequestForm.reset();
    if (directJobProviderId) directJobProviderId.value = String(numericProviderId);
    if (directJobProviderType) directJobProviderType.value = normalizedType;
    if (directJobRequestProviderLabel) {
        directJobRequestProviderLabel.textContent = normalizedType === 'company' ? 'this company' : 'this repairer';
    }

    if (directJobFinishDate) {
        const today = new Date().toISOString().split('T')[0];
        directJobFinishDate.setAttribute('min', today);
    }

    if (directJobPhotoPreview) directJobPhotoPreview.textContent = '';

    if (directJobRequestError) {
        directJobRequestError.style.display = 'none';
        directJobRequestError.textContent = '';
    }
    if (directJobRequestSuccess) {
        directJobRequestSuccess.style.display = 'none';
        directJobRequestSuccess.textContent = '';
    }

    if (directJobRequestSubmitBtn) {
        directJobRequestSubmitBtn.disabled = false;
        directJobRequestSubmitBtn.textContent = 'Request';
    }

    directJobRequestModal.classList.add('show');
    directJobRequestModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

async function submitDirectJobRequest(event) {
    event.preventDefault();

    if (!directJobRequestForm) return;
    if (!directJobRequestContext.providerId || !directJobRequestContext.providerType) return;

    const formData = new FormData(directJobRequestForm);
    formData.set('provider_id', String(directJobRequestContext.providerId));
    formData.set('provider_type', directJobRequestContext.providerType);

    if (directJobRequestError) {
        directJobRequestError.style.display = 'none';
        directJobRequestError.textContent = '';
    }
    if (directJobRequestSuccess) {
        directJobRequestSuccess.style.display = 'none';
        directJobRequestSuccess.textContent = '';
    }

    if (directJobRequestSubmitBtn) {
        directJobRequestSubmitBtn.disabled = true;
        directJobRequestSubmitBtn.textContent = 'Requesting...';
    }

    try {
        const response = await fetch(API_ENDPOINTS.directJobRequests, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();
        if (!response.ok || !result?.success) {
            throw new Error(result?.message || `Failed to create direct job request (${response.status})`);
        }

        if (directJobRequestSuccess) {
            directJobRequestSuccess.textContent = result.message || 'Direct job request created successfully.';
            directJobRequestSuccess.style.display = 'block';
        }

        setTimeout(() => {
            closeDirectJobRequestModal();
        }, 700);
    } catch (error) {
        console.error('Failed to submit direct job request:', error);
        if (directJobRequestError) {
            directJobRequestError.textContent = error.message || 'Failed to create direct request. Please try again.';
            directJobRequestError.style.display = 'block';
        }
    } finally {
        if (directJobRequestSubmitBtn) {
            directJobRequestSubmitBtn.disabled = false;
            directJobRequestSubmitBtn.textContent = 'Request';
        }
    }
}

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

async function submitListedJobRequest() {
    if (!Number.isFinite(selectedListedJobRequestId) || selectedListedJobRequestId <= 0) return;
    if (!listedJobRequestContext.providerType || !listedJobRequestContext.providerId) return;

    if (listedJobRequestSubmitBtn) {
        listedJobRequestSubmitBtn.disabled = true;
        listedJobRequestSubmitBtn.textContent = 'Requesting...';
    }

    if (listedJobRequestError) {
        listedJobRequestError.style.display = 'none';
        listedJobRequestError.textContent = '';
    }

    try {
        const payload = {
            provider_type: listedJobRequestContext.providerType,
            request_id: selectedListedJobRequestId,
            provider_id: listedJobRequestContext.providerId
        };

        const response = await fetch(API_ENDPOINTS.directRequestQuotes, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();
        if (!response.ok || !result?.success) {
            throw new Error(result?.message || `Failed to submit request (${response.status})`);
        }

        alert(result.message || 'Request sent successfully.');
        closeListedJobRequestModal();
    } catch (error) {
        console.error('Failed to submit listed-job request:', error);
        if (listedJobRequestError) {
            listedJobRequestError.textContent = error.message || 'Failed to send request. Please try again.';
            listedJobRequestError.style.display = 'block';
        }
    } finally {
        if (listedJobRequestSubmitBtn) {
            listedJobRequestSubmitBtn.textContent = 'Request';
            listedJobRequestSubmitBtn.disabled = !Number.isFinite(selectedListedJobRequestId) || selectedListedJobRequestId <= 0;
        }
    }
}

// Mobile Menu Functionality
function initializeMobileMenu() {
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function () {
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
            link.addEventListener('click', function () {
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
        profileAvatar.addEventListener('click', function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (!profileAvatar.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }
        });
    }
}

// Provider Tabs Functionality
function initializeProviderTabs() {
    providerTabs.forEach(tab => {
        tab.addEventListener('click', function () {
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
            } else {
                companiesGrid.classList.add('active');
                currentProviderType = 'companies';
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
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const service = document.getElementById('serviceSelect').value;
            const rating = document.getElementById('ratingSelect').value;
            const location = document.getElementById('locationInput').value;

            // Simulate search functionality

            // Show loading state
            showSearchLoading();

            // Simulate API call delay
            setTimeout(() => {
                hideSearchLoading();
                filterProviders({ service, rating, location });
            }, 1000);
        });
    }
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
        searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';
        searchBtn.disabled = false;
    }
}

// Filter Providers (simplified for demo)
function filterProviders(filters) {
    let filteredData = [...providerData];

    // Filter by service (simplified matching)
    if (filters.service) {
        filteredData = filteredData.filter(provider =>
            provider.title.toLowerCase().includes(filters.service.toLowerCase()) ||
            provider.description.toLowerCase().includes(filters.service.toLowerCase())
        );
    }

    // Filter by rating
    if (filters.rating) {
        const minRating = parseFloat(filters.rating);
        filteredData = filteredData.filter(provider => provider.rating >= minRating);
    }

    // Clear current grid and reset pagination
    providersGrid.innerHTML = '';
    currentPage = 0;
    allProvidersLoaded = false;

    // Update provider data temporarily for this search
    window.currentFilteredData = filteredData;

    // Load filtered results
    loadProviders(true);
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
function loadProviders(isFiltered = false) {
    if (isLoading) return;

    isLoading = true;
    showLoading();

    // Select data source based on current provider type
    const dataSource = currentProviderType === 'repairers'
        ? (window.currentFilteredData || providerData)
        : companyData;

    // Calculate start and end indices
    const startIndex = currentPage * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, dataSource.length);

    // Get current batch of providers
    const currentBatch = dataSource.slice(startIndex, endIndex);

    // Simulate network delay
    setTimeout(() => {
        // Render providers based on type
        currentBatch.forEach((provider, index) => {
            setTimeout(() => {
                if (currentProviderType === 'repairers') {
                    renderProviderCard(provider);
                } else {
                    renderCompanyCard(provider);
                }
            }, index * 100); // Stagger animation
        });

        // Update pagination state
        currentPage++;
        isLoading = false;
        hideLoading();

        // Check if all providers are loaded
        if (endIndex >= dataSource.length) {
            allProvidersLoaded = true;
            scrollTrigger.style.display = 'none';
        }

    }, 800); // Simulate loading delay
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

// Render Provider Card
function renderProviderCard(provider) {
    const card = document.createElement('div');
    card.className = 'provider-card';
    card.style.animationDelay = '0s'; // Reset animation delay

    card.innerHTML = `
        <div class="provider-header">
            <div class="provider-avatar">
                ${provider.avatar}
            </div>
            <div class="provider-info">
                <h3 class="provider-name">${provider.name}</h3>
                <p class="provider-title">${provider.title}</p>
            </div>
        </div>
        
        <div class="provider-rating">
            <div class="stars">
                ${generateStars(provider.rating)}
            </div>
            <span class="rating-text">${provider.rating} (${provider.reviews} reviews)</span>
        </div>
        
        <div class="provider-distance">
            <i class="fas fa-map-marker-alt"></i>
            ${provider.distance}
        </div>
        
        <p class="provider-description">
            ${provider.description}
        </p>
        
        <div class="provider-actions">
            <button class="view-profile-btn" onclick="viewProfile(${provider.id})">
                <i class="fas fa-user"></i>
                View Profile
            </button>
        </div>
    `;

    if (repairersGrid) {
        repairersGrid.appendChild(card);
    }
}

// Render Company Card
function renderCompanyCard(company) {
    const card = document.createElement('div');
    card.className = 'company-card';
    card.style.animationDelay = '0s';

    const servicesHTML = company.services.slice(0, 4).map(service =>
        `<span class="service-tag">${service}</span>`
    ).join('');

    card.innerHTML = `
        <div class="company-header">
            <div class="company-logo">
                ${company.logo}
            </div>
            <div class="company-info">
                <h3 class="company-name">${company.name}</h3>
                <span class="company-type">${company.type}</span>
            </div>
        </div>
        
        <div class="company-stats">
            <div class="company-stat">
                <span class="stat-value">${company.employees}+</span>
                <span class="stat-label">Employees</span>
            </div>
            <div class="company-stat">
                <span class="stat-value">${company.projects}+</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="company-stat">
                <span class="stat-value">${company.yearsFounded.split(' ')[1]}</span>
                <span class="stat-label">Founded</span>
            </div>
        </div>
        
        <div class="company-rating">
            <div class="stars">
                ${generateStars(company.rating)}
            </div>
            <span class="rating-text">${company.rating} (${company.reviews} reviews)</span>
        </div>
        
        <div class="company-location">
            <i class="fas fa-map-marker-alt"></i>
            ${company.location}
        </div>
        
        <div class="company-services">
            <p class="services-label">Services Offered:</p>
            <div class="services-tags">
                ${servicesHTML}
                ${company.services.length > 4 ? `<span class="service-tag">+${company.services.length - 4} more</span>` : ''}
            </div>
        </div>
        
        <p class="company-description">
            ${company.description}
        </p>
        
        <div class="company-actions">
            <button class="view-company-btn" onclick="viewCompanyDetails(${company.id})">
                <i class="fas fa-building"></i>
                View Company Details
            </button>
        </div>
    `;

    if (companiesGrid) {
        companiesGrid.appendChild(card);
    }
}

// View Company Details
function viewCompanyDetails(companyId) {
    const company = companyData.find(c => c.id === companyId);
    if (!company) return;

    // Create modal HTML
    const modalHTML = `
        <div class="company-modal-overlay" id="companyModal">
            <div class="company-modal-container">
                <div class="company-modal-header">
                    <div class="company-modal-logo">
                        ${company.logo}
                    </div>
                    <div class="company-modal-title-section">
                        <h2 class="company-modal-title">${company.name}</h2>
                        <p class="company-modal-type">${company.type}</p>
                    </div>
                    <button class="company-modal-close" onclick="closeCompanyModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="company-modal-content">
                    <div class="company-modal-rating">
                        <div class="stars">
                            ${generateStars(company.rating)}
                        </div>
                        <span class="rating-text">${company.rating} / 5.0 (${company.reviews} reviews)</span>
                    </div>
                    
                    <div class="company-modal-info-grid">
                        <div class="company-modal-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <span class="info-label">Location</span>
                                <span class="info-value">${company.location}</span>
                            </div>
                        </div>
                        
                        <div class="company-modal-info-item">
                            <i class="fas fa-users"></i>
                            <div>
                                <span class="info-label">Team Size</span>
                                <span class="info-value">${company.employees}+ Employees</span>
                            </div>
                        </div>
                        
                        <div class="company-modal-info-item">
                            <i class="fas fa-briefcase"></i>
                            <div>
                                <span class="info-label">Projects Completed</span>
                                <span class="info-value">${company.projects}+</span>
                            </div>
                        </div>
                        
                        <div class="company-modal-info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <span class="info-label">Established</span>
                                <span class="info-value">${company.yearsFounded}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="company-modal-section">
                        <h3 class="modal-section-title">
                            <i class="fas fa-info-circle"></i>
                            About Company
                        </h3>
                        <p class="company-modal-description">${company.description}</p>
                    </div>
                    
                    <div class="company-modal-section">
                        <h3 class="modal-section-title">
                            <i class="fas fa-tools"></i>
                            Services Offered
                        </h3>
                        <div class="company-modal-services">
                            ${company.services.map(service =>
        `<span class="modal-service-tag">
                                    <i class="fas fa-check-circle"></i>
                                    ${service}
                                </span>`
    ).join('')}
                        </div>
                    </div>
                    
                    <div class="company-modal-actions">
                        <button class="btn-primary" onclick="sendRepairRequest('company', ${companyId})">
                            <i class="fas fa-tools"></i>
                            Send Repair Request
                        </button>
                        <button class="btn-secondary" onclick="contactCompany(${companyId})">
                            <i class="fas fa-comment"></i>
                            Contact Company
                        </button>
                        <button class="btn-outline" onclick="requestCompanyQuote(${companyId})">
                            <i class="fas fa-file-invoice"></i>
                            Request Quote
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Show modal with animation
    setTimeout(() => {
        document.getElementById('companyModal').classList.add('show');
    }, 10);

    // Close on outside click
    document.getElementById('companyModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeCompanyModal();
        }
    });
}

// Close Company Modal
function closeCompanyModal() {
    const modal = document.getElementById('companyModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.remove();
        }, 300);
    }
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

// View Profile Function
function viewProfile(providerId) {
    const provider = providerData.find(p => p.id === providerId);
    if (!provider) return;

    // Only show popup for repairers with detailed profiles (IDs 1-5)
    // ID 1: Kamal Silva (Electrician)
    // ID 2: Nimal Perera (Plumber)
    // ID 3: Saman Fernando (HVAC)
    // ID 4: Ranjith Kumar (Carpenter) - mapped from Chaminda
    // ID 5: Pradeep Bandara (Painter) - mapped from Lakshmi

    if (providerId >= 1 && providerId <= 5) {
        // Open the detailed profile popup
        openRepairerProfile(providerId);
    } else {
        // For other providers, show placeholder message
        window.showAlert(`Viewing profile for ${provider.name}\n\nDetailed profile coming soon!`, 'info', 'Profile Preview');

    }
}

// Smooth Scrolling for Navigation Links
document.addEventListener('click', function (e) {
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
window.addEventListener('resize', function () {
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
window.addEventListener('scroll', function () {
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

// Form Validation Enhancement
function validateSearchForm() {
    const location = document.getElementById('locationInput').value.trim();

    if (location.length < 3) {
        window.showAlert('Please enter a valid location (at least 3 characters)', 'warning');
        return false;
    }

    return true;
}

// Add form validation to search form
if (searchForm) {
    searchForm.addEventListener('submit', function (e) {
        if (!validateSearchForm()) {
            e.preventDefault();
            return false;
        }
    });
}

// Send Repair Request Function (Placeholder - No functionality yet)
function sendRepairRequest(type, providerId) {
    openListedJobRequestModal(type, providerId);
}

// Contact Company Function (Placeholder)
function contactCompany(companyId) {

    window.showAlert('Contact Company feature will be implemented soon!', 'info', 'Coming Soon');
    // TODO: Implement contact company functionality
}

// Request Company Quote Function (Placeholder)
function requestCompanyQuote(companyId) {
    openDirectJobRequestModal('company', companyId);
}

// Request Repairer Quote / New Job Function (Placeholder)
function requestRepairerQuote(repairerId) {
    openDirectJobRequestModal('individual', repairerId);
}

// Console log for debugging


