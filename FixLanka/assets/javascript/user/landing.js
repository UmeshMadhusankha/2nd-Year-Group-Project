// Fix Lanka Landing Page JavaScript
// ===================================
// All provider data is now fetched from the database via API endpoints

// TODO: Replace mock data with API calls
// Placeholder arrays for future API integration
const providerData = [];
const companyData = [];

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

// State variables
let currentPage = 0;
const itemsPerPage = 6;
let isLoading = false;
let allProvidersLoaded = false;
let currentProviderType = 'repairers';

// Initialize the application
document.addEventListener('DOMContentLoaded', function () {
    initializeMobileMenu();
    initializeSearchForm();
    initializeLazyLoading();
    initializeProfileDropdown();
    initializeProviderTabs();
    loadInitialProviders();
});

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
            const district = document.getElementById('districtSelect').value;
            const rating = document.getElementById('ratingSelect').value;

            // Simulate search functionality

            // Show loading state
            showSearchLoading();

            // Simulate API call delay
            setTimeout(() => {
                hideSearchLoading();
                filterProviders({ service, district, rating });
            }, 1000);
        });
    }

    // Clear button handler
    const clearBtn = document.getElementById('clearBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('serviceSelect').value = '';
            document.getElementById('districtSelect').value = '';
            document.getElementById('ratingSelect').value = '';
            
            // Reset providers display
            currentPage = 0;
            allProvidersLoaded = false;
            
            if (currentProviderType === 'repairers' && repairersGrid) {
                repairersGrid.innerHTML = '';
            } else if (companiesGrid) {
                companiesGrid.innerHTML = '';
            }
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
    // TODO: Implement API-based filtering
    let filteredData = window.currentFilteredData || providerData || [];

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
    // TODO: Fetch from API endpoints instead of mock data
    const dataSource = currentProviderType === 'repairers'
        ? (window.currentFilteredData || providerData || [])
        : (companyData || []);

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
    // TODO: Fetch company data from API instead of mock data
    const company = companyData && companyData.find(c => c.id === companyId);
    if (!company) {
        window.showAlert('Company data is loading or not available. Please try opening from the company profile popup instead.', 'warning');
        return;
    }

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
    // Open repairer profile popup with the given ID
    // All data is fetched from the database via the repairer-profile-popup.js
    openRepairerProfile(providerId);
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

    window.showAlert(`Send Repair Request feature will be implemented soon!\n\nProvider Type: ${type}\nProvider ID: ${providerId || 'Current profile'}`, 'info', 'Coming Soon');
    // TODO: Implement repair request functionality
    // This will redirect to post-job page or open a request form
}

// Contact Company Function (Placeholder)
function contactCompany(companyId) {

    window.showAlert('Contact Company feature will be implemented soon!', 'info', 'Coming Soon');
    // TODO: Implement contact company functionality
}

// Request Company Quote Function (Placeholder)
function requestCompanyQuote(companyId) {

    window.showAlert('Request Company Quote feature will be implemented soon!', 'info', 'Coming Soon');
    // TODO: Implement company quote request functionality
}

// Console log for debugging


