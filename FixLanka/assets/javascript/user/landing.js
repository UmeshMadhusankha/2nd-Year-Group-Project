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
            
            const service = document.getElementById('serviceSelect').value;
            const rating = document.getElementById('ratingSelect').value;
            const location = document.getElementById('locationInput').value;
            
            // Simulate search functionality
            console.log('Search submitted:', { service, rating, location });
            
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

// Filter Providers - now uses real API
async function filterProviders(filters) {
    // Clear current grid and reset pagination
    providersGrid.innerHTML = '';
    currentPage = 0;
    allProvidersLoaded = false;
    scrollTrigger.style.display = 'block';
    
    // Load filtered results from API
    await loadProviders(true);
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
        // Get filter values
        const service = document.getElementById('serviceSelect')?.value || '';
        const rating = document.getElementById('ratingSelect')?.value || '';
        const location = document.getElementById('locationInput')?.value || '';
        
        // Build query parameters
        const params = new URLSearchParams({
            limit: itemsPerPage,
            offset: currentPage * itemsPerPage
        });
        
        if (service) params.append('category', service);
        if (rating) params.append('rating', rating);
        if (location) params.append('location', location);
        
        // Fetch data from API
        const response = await fetch(`${API_BASE}/get-featured-providers?${params.toString()}`);
        
        if (!response.ok) {
            throw new Error('Failed to fetch providers');
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            const providers = result.data;
            
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
        } else {
            console.error('No providers found or invalid response');
            // Fallback to sample data if API fails
            loadSampleProviders();
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

// Render Provider Card
function renderProviderCard(provider) {
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

// Form Validation Enhancement
function validateSearchForm() {
    const location = document.getElementById('locationInput').value.trim();
    
    if (location.length < 3) {
        alert('Please enter a valid location (at least 3 characters)');
        return false;
    }
    
    return true;
}

// Add form validation to search form
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        if (!validateSearchForm()) {
            e.preventDefault();
            return false;
        }
    });
}

// Console log for debugging
console.log('Fix Lanka Landing Page JavaScript loaded successfully');
console.log('Total providers available:', providerData.length);
