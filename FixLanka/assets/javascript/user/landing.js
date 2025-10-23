// Fix Lanka Landing Page JavaScript
// ===================================

// Sample provider data for demonstration
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
        name: "Ranjith Kumar",
        title: "Carpentry Specialist",
        rating: 4.6,
        reviews: 127,
        distance: "3.5 km away",
        description: "Expert carpenter specializing in custom furniture, kitchen cabinets, and home renovations. Quality craftsmanship guaranteed.",
        avatar: "RK"
    },
    {
        id: 5,
        name: "Pradeep Bandara",
        title: "Painting Professional",
        rating: 4.5,
        reviews: 93,
        distance: "1.8 km away",
        description: "Professional painting contractor for interior and exterior projects. High-quality finishes with premium paints.",
        avatar: "PB"
    },
    {
        id: 6,
        name: "Amara Jayasinghe",
        title: "House Cleaning Pro",
        rating: 5.0,
        reviews: 178,
        distance: "0.5 km away",
        description: "Professional cleaning service with eco-friendly products. Trusted by 200+ families.",
        avatar: "AJ"
    },
    {
        id: 7,
        name: "Lakshmi Wijeratne",
        title: "Interior Painter",
        rating: 4.9,
        reviews: 267,
        distance: "1.5 km away",
        description: "Professional painter with attention to detail. Transforms spaces with quality finishes.",
        avatar: "LW"
    },
    {
        id: 8,
        name: "Roshan Mendis",
        title: "Appliance Repair",
        rating: 4.5,
        reviews: 98,
        distance: "2.3 km away",
        description: "Expert in washing machine, refrigerator, and microwave repairs. Same-day service available.",
        avatar: "RM"
    },
    {
        id: 9,
        name: "Priya Gunasekara",
        title: "Garden Maintenance",
        rating: 4.8,
        reviews: 156,
        distance: "1.1 km away",
        description: "Professional gardener offering lawn care, pruning, and landscape design services.",
        avatar: "PG"
    },
    {
        id: 10,
        name: "Dinesh Amarasinghe",
        title: "Tile Installation Expert",
        rating: 4.7,
        reviews: 112,
        distance: "1.9 km away",
        description: "Specialist in ceramic, marble, and porcelain tile installation. Precise workmanship.",
        avatar: "DA"
    },
    {
        id: 11,
        name: "Kumari Abeysekera",
        title: "Home Security Specialist",
        rating: 4.9,
        reviews: 87,
        distance: "2.5 km away",
        description: "Security system installation and maintenance. Keeping your home safe and secure.",
        avatar: "KA"
    },
    {
        id: 12,
        name: "Janaka Rodrigo",
        title: "Pest Control Expert",
        rating: 4.6,
        reviews: 145,
        distance: "1.7 km away",
        description: "Eco-friendly pest control solutions. Effective treatment for all types of pest problems.",
        avatar: "JR"
    },
    {
        id: 13,
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
    
    // Use filtered data if available, otherwise use original data
    const dataSource = window.currentFilteredData || providerData;
    
    // Calculate start and end indices
    const startIndex = currentPage * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, dataSource.length);
    
    // Get current batch of providers
    const currentBatch = dataSource.slice(startIndex, endIndex);
    
    // Simulate network delay
    setTimeout(() => {
        // Render providers
        currentBatch.forEach((provider, index) => {
            setTimeout(() => {
                renderProviderCard(provider);
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
    
    if (providersGrid) {
        providersGrid.appendChild(card);
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
        alert(`Viewing profile for ${provider.name}\n\nDetailed profile coming soon!`);
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
