// Fix Lanka Provider Profile Page JavaScript
// =============================================

// DOM Elements
const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
const profileAvatar = document.getElementById('profileAvatar');
const profileDropdown = document.getElementById('profileDropdown');
const messageBtn = document.getElementById('messageBtn');
const toggleReviewsBtn = document.getElementById('toggleReviewsBtn');
const reviewsContainer = document.getElementById('reviewsContainer');

// State variables
let reviewsExpanded = false;

// Initialize the application
document.addEventListener('DOMContentLoaded', function () {
    initializeMobileMenu();
    initializeProfileDropdown();
    initializeMessageButton();
    initializeReviewsToggle();
    initializeStickyHeader();
});

// Mobile Menu Functionality (reuse from landing page)
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

// Profile Dropdown Functionality (reuse from landing page)
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

        // Close dropdown when clicking on dropdown links
        const dropdownLinks = profileDropdown.querySelectorAll('.dropdown-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                // Handle logout separately
                if (this.classList.contains('logout')) {
                    e.preventDefault();
                    handleLogout();
                } else {
                    // For other links, you can add navigation logic here
                }

                // Close dropdown
                profileDropdown.classList.remove('active');
            });
        });

        // Close dropdown on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
            }
        });
    }
}

// Handle Logout
function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {

        alert('You have been logged out successfully!');
        // Optionally redirect to login page
        // window.location.href = 'index.html';
    }
}

// Message Button Functionality
function initializeMessageButton() {
    if (messageBtn) {
        messageBtn.addEventListener('click', function () {
            // Get provider name from the header
            const providerName = document.querySelector('.provider-name').textContent;

            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connecting...';
            this.disabled = true;

            // Simulate connecting delay
            setTimeout(() => {
                // Reset button
                this.innerHTML = originalText;
                this.disabled = false;

                // Show messaging interface placeholder
                showMessageDialog(providerName);
            }, 1500);
        });
    }
}

// Show Message Dialog (placeholder)
function showMessageDialog(providerName) {
    alert(`Opening message conversation with ${providerName}...\n\nThis would normally open a messaging interface where you can:\n&bull; Send direct messages\n&bull; Share photos\n&bull; Discuss project details\n&bull; Schedule appointments`);

    // In a real application, this would open a modal or redirect to a messaging page

}

// Reviews Toggle Functionality
function initializeReviewsToggle() {
    if (toggleReviewsBtn && reviewsContainer) {
        toggleReviewsBtn.addEventListener('click', function () {
            const hiddenReviews = reviewsContainer.querySelectorAll('.hidden-review');
            const toggleText = this.querySelector('.toggle-text');
            const toggleIcon = this.querySelector('.toggle-icon');

            if (!reviewsExpanded) {
                // Show more reviews
                hiddenReviews.forEach((review, index) => {
                    setTimeout(() => {
                        review.classList.remove('hidden-review');
                        review.classList.add('show');
                    }, index * 100); // Stagger animation
                });

                toggleText.textContent = 'Show Less Reviews';
                this.classList.add('expanded');
                reviewsExpanded = true;

            } else {
                // Hide additional reviews
                hiddenReviews.forEach(review => {
                    review.classList.add('hidden-review');
                    review.classList.remove('show');
                });

                toggleText.textContent = 'Show More Reviews';
                this.classList.remove('expanded');
                reviewsExpanded = false;

                // Scroll back to reviews section
                document.querySelector('.reviews-section').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }
}

// Sticky Header Enhancement
function initializeStickyHeader() {
    const providerHeader = document.getElementById('providerHeader');
    const navbar = document.querySelector('.navbar');

    if (providerHeader && navbar) {
        let lastScrollY = window.scrollY;

        window.addEventListener('scroll', function () {
            const currentScrollY = window.scrollY;
            const navbarHeight = navbar.offsetHeight;

            // Add shadow when scrolled
            if (currentScrollY > navbarHeight) {
                providerHeader.style.boxShadow = 'var(--shadow-lg)';
                providerHeader.style.borderBottom = '1px solid var(--border-color)';
            } else {
                providerHeader.style.boxShadow = 'var(--shadow-sm)';
                providerHeader.style.borderBottom = '1px solid var(--border-light)';
            }

            lastScrollY = currentScrollY;
        });
    }
}

// Handle service tag clicks
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('service-tag')) {
        const serviceName = e.target.textContent;

        // Add a temporary highlight effect
        e.target.style.transform = 'scale(1.05)';
        setTimeout(() => {
            e.target.style.transform = '';
        }, 150);

        // In a real application, this could:
        // - Show service details modal
        // - Navigate to booking page for that service
        // - Show pricing information
        alert(`Learn more about ${serviceName} service?\n\nThis would show detailed information about the service including:\n&bull; Pricing\n&bull; What's included\n&bull; Estimated duration\n&bull; Book now option`);
    }
});

// Contact information click handlers
document.addEventListener('click', function (e) {
    const contactItem = e.target.closest('.contact-item');
    if (contactItem) {
        const contactIcon = contactItem.querySelector('.contact-icon');
        const contactValue = contactItem.querySelector('.contact-value');

        if (contactIcon && contactValue) {
            const iconClass = contactIcon.className;
            const value = contactValue.textContent;

            if (iconClass.includes('fa-phone')) {
                // Handle phone click
                if (confirm(`Call ${value}?`)) {
                    window.open(`tel:${value.replace(/\s/g, '')}`);
                }
            } else if (iconClass.includes('fa-envelope')) {
                // Handle email click
                if (confirm(`Send email to ${value}?`)) {
                    window.open(`mailto:${value}`);
                }
            } else if (iconClass.includes('fa-map-marker-alt')) {
                // Handle location click

                alert(`Opening map for ${value}...\n\nThis would show the service area on a map.`);
            }
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

// Smooth scrolling for any anchor links
document.addEventListener('click', function (e) {
    const target = e.target;

    // Handle navigation links with hash
    if (target.matches('a[href^="#"]') && target.getAttribute('href') !== '#') {
        e.preventDefault();
        const targetId = target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            const navbar = document.querySelector('.navbar');
            const providerHeader = document.querySelector('.provider-header');
            const offset = (navbar?.offsetHeight || 0) + (providerHeader?.offsetHeight || 0);

            const targetPosition = targetElement.offsetTop - offset;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    }
});

// Add scroll-based navbar styling
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

// Accessibility: Focus management
document.addEventListener('keydown', function (e) {
    // Handle escape key for closing dropdowns and menus
    if (e.key === 'Escape') {
        // Close mobile menu
        if (mobileMenu && mobileMenu.classList.contains('active')) {
            mobileMenu.classList.remove('active');
            mobileMenuToggle.focus();
        }

        // Close profile dropdown
        if (profileDropdown && profileDropdown.classList.contains('active')) {
            profileDropdown.classList.remove('active');
            profileAvatar.focus();
        }
    }

    // Handle Enter key for buttons
    if (e.key === 'Enter' && e.target.matches('.service-tag')) {
        e.target.click();
    }
});

// Console log for debugging

// Animation observer for elements coming into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe section cards for animation
document.addEventListener('DOMContentLoaded', function () {
    const sectionCards = document.querySelectorAll('.section-card');
    sectionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        card.style.transitionDelay = `${index * 0.1}s`;
        observer.observe(card);
    });
});
