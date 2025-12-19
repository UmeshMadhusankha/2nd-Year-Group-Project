// Fix Lanka Landing Page JavaScript
// ===================================

// API Configuration
const API_BASE = '/2nd-Year-Group-Project/FixLanka';

// Sample provider data for demonstration (fallback)
const providerData = [
    {
        id: 1,
        name: "Kamal Silva",
        title: "Master Electrician",
        rating: 4.9,
        reviews: 156,
        distance: "0.8 km away",
        description: "Certified electrician with 15+ years of experience. Specializes in residential and commercial electrical work.",
        avatar: "KS"
    },
    {
        id: 2,
        name: "Nimal Perera",
        title: "Plumbing Expert",
        rating: 4.8,
        reviews: 243,
        distance: "1.2 km away",
        description: "Licensed plumber offering 24/7 emergency services. Expert in pipe repairs and bathroom installations.",
        avatar: "NP"
    },
    {
        id: 3,
        name: "Saman Fernando",
        title: "HVAC Technician",
        rating: 4.7,
        reviews: 89,
        distance: "2.1 km away",
        description: "Air conditioning and heating specialist. Quick diagnostics and reliable repair services.",
        avatar: "SF"
    },
    {
        id: 4,
        name: "Amara Jayasinghe",
        title: "House Cleaning Pro",
        rating: 5.0,
        reviews: 178,
        distance: "0.5 km away",
        description: "Professional cleaning service with eco-friendly products. Trusted by 200+ families.",
        avatar: "AJ"
    },
    {
        id: 5,
        name: "Chaminda Rathnayake",
        title: "Carpenter & Handyman",
        rating: 4.6,
        reviews: 134,
        distance: "1.8 km away",
        description: "Skilled carpenter specializing in custom furniture and home repairs. Quality craftsmanship guaranteed.",
        avatar: "CR"
    },
    {
        id: 6,
        name: "Lakshmi Wijeratne",
        title: "Interior Painter",
        rating: 4.9,
        reviews: 267,
        distance: "1.5 km away",
        description: "Professional painter with attention to detail. Transforms spaces with quality finishes.",
        avatar: "LW"
    },
    {
        id: 7,
        name: "Roshan Mendis",
        title: "Appliance Repair",
        rating: 4.5,
        reviews: 98,
        distance: "2.3 km away",
        description: "Expert in washing machine, refrigerator, and microwave repairs. Same-day service available.",
        avatar: "RM"
    },
    {
        id: 8,
        name: "Priya Gunasekara",
        title: "Garden Maintenance",
        rating: 4.8,
        reviews: 156,
        distance: "1.1 km away",
        description: "Professional gardener offering lawn care, pruning, and landscape design services.",
        avatar: "PG"
    },
    {
        id: 9,
        name: "Dinesh Amarasinghe",
        title: "Tile Installation Expert",
        rating: 4.7,
        reviews: 112,
        distance: "1.9 km away",
        description: "Specialist in ceramic, marble, and porcelain tile installation. Precise workmanship.",
        avatar: "DA"
    },
    {
        id: 10,
        name: "Kumari Abeysekera",
        title: "Home Security Specialist",
        rating: 4.9,
        reviews: 87,
        distance: "2.5 km away",
        description: "Security system installation and maintenance. Keeping your home safe and secure.",
        avatar: "KA"
    },
    {
        id: 11,
        name: "Janaka Rodrigo",
        title: "Pest Control Expert",
        rating: 4.6,
        reviews: 145,
        distance: "1.7 km away",
        description: "Eco-friendly pest control solutions. Effective treatment for all types of pest problems.",
        avatar: "JR"
    },
    {
        id: 12,
        name: "Sanduni Perera",
        title: "Window Cleaning Pro",
        rating: 4.8,
        reviews: 203,
        distance: "0.9 km away",
        description: "Professional window cleaning for residential and commercial properties. Streak-free results.",
        avatar: "SP"
    }
];

// DOM Elements
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
const searchForm = document.getElementById('searchForm');
const providersGrid = document.getElementById('providersGrid');
const loadingIndicator = document.getElementById('loadingIndicator');
const scrollTrigger = document.getElementById('scrollTrigger');
const profileAvatar = document.getElementById('profileAvatar');
const profileDropdown = document.getElementById('profileDropdown');

// State variables
let currentPage = 0;
const itemsPerPage = 6;
let isLoading = false;
let allProvidersLoaded = false;
let currentProviderType = 'all'; // Track selected provider type

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileMenu();
    initializeSearchForm();
    initializeLazyLoading();
    initializeProfileDropdown();
    loadInitialProviders();
});

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

// Search Form Functionality
function initializeSearchForm() {
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Apply filters (will reload providers)
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
    await loadProviders(true);
    
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
async function loadProviders(isFiltered = false) {
    if (isLoading) return;
    
    isLoading = true;
    showLoading();
    
    try {
        // Get filter values from form
        const service = document.getElementById('serviceSelect')?.value || '';
        const rating = document.getElementById('ratingSelect')?.value || '';
        const district = document.getElementById('districtSelect')?.value || '';
        
        // Build query parameters
        const params = new URLSearchParams({
            limit: itemsPerPage,
            offset: currentPage * itemsPerPage
        });
        
        // Add filters only if they have values
        if (service) params.append('category', service);
        if (rating) params.append('rating', rating);
        if (district) params.append('location', district);
        
        // Add provider type filter
        if (currentProviderType && currentProviderType !== 'all') {
            params.append('provider_type', currentProviderType);
        }
        
        // Determine which endpoint to use
        const endpoint = (service || rating || district || currentProviderType !== 'all') ? 'get-providers' : 'get-featured-providers';
        
        const apiUrl = `${API_BASE}/${endpoint}?${params.toString()}`;
        console.log('========================================');
        console.log('FETCHING FROM API');
        console.log('URL:', apiUrl);
        console.log('Parameters:', {
            service,
            rating,
            district,
            provider_type: currentProviderType,
            limit: itemsPerPage,
            offset: currentPage * itemsPerPage
        });
        console.log('========================================');
        
        // Fetch data from API
        const response = await fetch(apiUrl);
        
        if (!response.ok) {
            throw new Error('Failed to fetch providers');
        }
        
        const result = await response.json();
        
        console.log('========================================');
        console.log('API RESPONSE RECEIVED');
        console.log('========================================');
        console.log('Full API Response:', result);
        console.log('Success:', result.success);
        console.log('Provider Type Filter Applied:', currentProviderType);
        console.log('Total Providers Received:', result.data ? result.data.length : 0);
        
        if (result.success && result.data) {
            const providers = result.data;
            
            // Detailed provider type analysis
            const companies = providers.filter(p => p.provider_type === 'company');
            const individuals = providers.filter(p => p.provider_type === 'individual');
            
            console.log('----------------------------------------');
            console.log('PROVIDER BREAKDOWN:');
            console.log('Companies found:', companies.length);
            console.log('Individuals found:', individuals.length);
            console.log('----------------------------------------');
            
            if (companies.length > 0) {
                console.log('Company Details:');
                companies.forEach((c, idx) => {
                    console.log(`  ${idx + 1}. ${c.full_name || c.name} (ID: ${c.company_id}, Rating: ${c.ratings})`);
                });
            }
            
            if (individuals.length > 0) {
                console.log('Individual Details:');
                individuals.forEach((i, idx) => {
                    console.log(`  ${idx + 1}. ${i.full_name || i.name} (ID: ${i.repairer_id}, Rating: ${i.ratings})`);
                });
            }
            
            console.log('========================================');
            
            // Check if no results
            if (providers.length === 0 && currentPage === 0) {
                console.log('No providers found for current filters');
                showNoResults();
                allProvidersLoaded = true;
                scrollTrigger.style.display = 'none';
            } else {
                // Hide no results message if it was showing
                hideNoResults();
                
                // Render providers with staggered animation
                providers.forEach((provider, index) => {
                    setTimeout(() => {
                        renderProviderCard(provider);
                    }, index * 100);
                });
                
                // Update pagination state
                currentPage++;
                
                // Check if more providers are available
                if (result.pagination) {
                    allProvidersLoaded = !result.pagination.hasMore;
                } else if (providers.length < itemsPerPage) {
                    allProvidersLoaded = true;
                }
                
                if (allProvidersLoaded) {
                    scrollTrigger.style.display = 'none';
                }
            }
        } else {
            console.error('No providers found or invalid response');
            // Show no results if first page
            if (currentPage === 0) {
                showNoResults();
            }
        }
        
    } catch (error) {
        console.error('Error loading providers:', error);
        // Fallback to sample data
        loadSampleProviders();
    } finally {
        isLoading = false;
        hideLoading();
    }
}

// Fallback function to load sample providers
function loadSampleProviders() {
    const dataSource = window.currentFilteredData || providerData;
    const startIndex = currentPage * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, dataSource.length);
    const currentBatch = dataSource.slice(startIndex, endIndex);
    
    currentBatch.forEach((provider, index) => {
        setTimeout(() => {
            renderProviderCard(provider);
        }, index * 100);
    });
    
    currentPage++;
    
    if (endIndex >= dataSource.length) {
        allProvidersLoaded = true;
        scrollTrigger.style.display = 'none';
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
    // Debug logging for each provider
    console.log('=== Rendering Provider Card ===');
    console.log('Provider Type:', provider.provider_type);
    console.log('Provider Name:', provider.full_name || provider.name);
    console.log('Provider ID:', provider.repairer_id || provider.company_id);
    console.log('Full Provider Data:', provider);
    
    const card = document.createElement('div');
    card.className = 'provider-card';
    card.style.animationDelay = '0s'; // Reset animation delay
    
    // Generate avatar initials or use profile picture
    const avatar = provider.profilePicture 
        ? `<img src="${API_BASE}/${provider.profilePicture}" alt="${provider.full_name || provider.name}" class="avatar-img">`
        : generateAvatarInitials(provider.full_name || provider.name);
    
    // Determine provider type and display info
    const providerType = provider.provider_type || 'individual';
    const providerName = provider.full_name || provider.name || 'Unknown';
    const providerTitle = provider.category_name || (providerType === 'company' ? 'Service Company' : 'Service Provider');
    const rating = provider.ratings || 0;
    const completedJobs = provider.completedJobsCount || 0;
    const about = provider.about || provider.address || 'Professional service provider';
    const availability = provider.availability || 'available';
    const serviceAreas = provider.districts || provider.address || 'Available in your area';
    
    // Provider ID and type for viewing details
    const providerId = providerType === 'individual' ? provider.repairer_id : provider.company_id;
    
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
            <span class="rating-text">${rating.toFixed(1)} ${completedJobs > 0 ? `(${completedJobs} jobs)` : ''}</span>
        </div>
        
        <div class="provider-distance">
            <i class="fas fa-map-marker-alt"></i>
            ${escapeHtml(serviceAreas.substring(0, 50))}${serviceAreas.length > 50 ? '...' : ''}
        </div>
        
        <p class="provider-description">
            ${escapeHtml(about.substring(0, 120))}${about.length > 120 ? '...' : ''}
        </p>
        
        <div class="provider-actions">
            <button class="view-profile-btn" onclick="viewProviderProfile(${providerId}, '${providerType}')">
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

// View Provider Profile Function
async function viewProviderProfile(providerId, providerType) {
    try {
        const response = await fetch(`${API_BASE}/get-provider-details?id=${providerId}&type=${providerType}`);
        const result = await response.json();
        
        if (result.success && result.data) {
            const provider = result.data;
            // Navigate to provider details page or show modal
            window.location.href = `${API_BASE}/provider?id=${providerId}&type=${providerType}`;
        } else {
            alert('Unable to load provider details. Please try again.');
        }
    } catch (error) {
        console.error('Error fetching provider details:', error);
        alert('Failed to load provider details');
    }
}

// View Profile Function (placeholder - for fallback sample data)
function viewProfile(providerId) {
    const provider = providerData.find(p => p.id === providerId);
    if (provider) {
        alert(`Viewing profile for ${provider.name}\n\nThis would normally navigate to a detailed profile page.`);
        console.log('View profile for provider:', provider);
    }
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
    loadProviders(true);
}

// Console log for debugging
console.log('Fix Lanka Landing Page JavaScript loaded successfully');
console.log('Total providers available:', providerData.length);
