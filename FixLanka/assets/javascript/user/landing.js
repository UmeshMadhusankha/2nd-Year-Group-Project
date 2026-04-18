// Fix Lanka Landing Page JavaScript
// ===================================

// Sample provider data for demonstration (Individual Repairers)
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

// Sample company data for demonstration
const companyData = [
    {
        id: 1,
        name: "Lanka Build Solutions",
        type: "Construction & Renovation",
        rating: 4.9,
        reviews: 342,
        location: "Colombo 5",
        employees: 45,
        projects: 280,
        yearsFounded: "Est. 2010",
        services: ["Construction", "Renovation", "Interior Design", "Electrical"],
        description: "Leading construction company with over 13 years of experience in residential and commercial projects. Committed to quality and timely delivery.",
        logo: "LBS"
    },
    {
        id: 2,
        name: "HomeFix Services Ltd",
        type: "Multi-Service Company",
        rating: 4.8,
        reviews: 567,
        location: "Nugegoda",
        employees: 82,
        projects: 850,
        yearsFounded: "Est. 2008",
        services: ["Plumbing", "Electrical", "HVAC", "Carpentry", "Painting"],
        description: "One-stop solution for all your home repair and maintenance needs. Professional team available 24/7 for emergency services.",
        logo: "HF"
    },
    {
        id: 3,
        name: "CleanPro Lanka",
        type: "Cleaning Services",
        rating: 4.9,
        reviews: 789,
        location: "Kandy",
        employees: 120,
        projects: 1200,
        yearsFounded: "Est. 2012",
        services: ["House Cleaning", "Office Cleaning", "Deep Cleaning", "Pest Control"],
        description: "Premier cleaning service provider with eco-friendly solutions. Trusted by over 500 corporate clients and 5000+ residential customers.",
        logo: "CP"
    },
    {
        id: 4,
        name: "TechElectric Solutions",
        type: "Electrical Services",
        rating: 4.7,
        reviews: 423,
        location: "Dehiwala",
        employees: 35,
        projects: 650,
        yearsFounded: "Est. 2015",
        services: ["Electrical Installation", "Wiring", "Solar Panels", "Smart Home"],
        description: "Specialized in modern electrical solutions including smart home automation and solar energy systems. Certified technicians.",
        logo: "TE"
    },
    {
        id: 5,
        name: "AquaFlow Plumbing Co",
        type: "Plumbing & Water Solutions",
        rating: 4.8,
        reviews: 312,
        location: "Moratuwa",
        employees: 28,
        projects: 520,
        yearsFounded: "Est. 2013",
        services: ["Plumbing", "Water Tank Installation", "Drainage", "Bathroom Fitting"],
        description: "Expert plumbing services with 24/7 emergency response. Specialists in water management and modern bathroom installations.",
        logo: "AF"
    },
    {
        id: 6,
        name: "CoolAir HVAC Systems",
        type: "Air Conditioning Services",
        rating: 4.9,
        reviews: 456,
        location: "Colombo 7",
        employees: 40,
        projects: 720,
        yearsFounded: "Est. 2011",
        services: ["AC Installation", "AC Repair", "Maintenance", "Ventilation"],
        description: "Leading HVAC company providing installation, repair, and maintenance services. Authorized dealers for major AC brands.",
        logo: "CA"
    },
    {
        id: 7,
        name: "WoodCraft Interiors",
        type: "Carpentry & Furniture",
        rating: 4.7,
        reviews: 234,
        location: "Maharagama",
        employees: 32,
        projects: 380,
        yearsFounded: "Est. 2014",
        services: ["Custom Furniture", "Kitchen Cabinets", "Wardrobes", "Doors & Windows"],
        description: "Premium carpentry services with custom designs. Expert craftsmen creating beautiful and functional wooden solutions.",
        logo: "WC"
    },
    {
        id: 8,
        name: "PaintPro Lanka",
        type: "Painting & Decorating",
        rating: 4.8,
        reviews: 398,
        location: "Galle",
        employees: 55,
        projects: 890,
        yearsFounded: "Est. 2009",
        services: ["Interior Painting", "Exterior Painting", "Wall Texturing", "Waterproofing"],
        description: "Professional painting company using premium quality paints. Experts in color consultation and decorative finishes.",
        logo: "PP"
    },
    {
        id: 9,
        name: "SecureHome Systems",
        type: "Security & Automation",
        rating: 4.9,
        reviews: 287,
        location: "Colombo 3",
        employees: 38,
        projects: 420,
        yearsFounded: "Est. 2016",
        services: ["CCTV Installation", "Alarm Systems", "Access Control", "Home Automation"],
        description: "Advanced security solutions with smart home integration. Protecting homes and businesses with cutting-edge technology.",
        logo: "SH"
    },
    {
        id: 10,
        name: "GreenScape Gardens",
        type: "Landscaping & Gardening",
        rating: 4.6,
        reviews: 178,
        location: "Kotte",
        employees: 25,
        projects: 310,
        yearsFounded: "Est. 2017",
        services: ["Landscape Design", "Garden Maintenance", "Irrigation", "Tree Services"],
        description: "Professional landscaping and garden maintenance services. Creating and maintaining beautiful outdoor spaces.",
        logo: "GS"
    }
];

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


