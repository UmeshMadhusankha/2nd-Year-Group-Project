// Repairer Profile Popup JavaScript with Mock Data

// Mock data for repairers
const mockRepairers = {
    1: {
        id: 1,
        name: "Kamal Silva",
        category: "Master Electrician",
        rating: 4.9,
        reviewCount: 156,
        completedJobs: 234,
        distance: "0.8 km away",
        about: "Certified electrician with 15+ years of experience. Specializes in residential and commercial electrical work, including installations, repairs, and maintenance. Licensed and insured professional committed to delivering high-quality electrical solutions.",
        phone: "+94 77 123 4567",
        email: "kamal.silva@fixlanka.lk",
        districts: "Colombo, Gampaha, Kalutara",
        availability: "available",
        image: "https://ui-avatars.com/api/?name=Kamal+Silva&size=200&background=17a2b8&color=fff&bold=true",
        reviews: [
            {
                author: "Priya Fernando",
                rating: 5,
                text: "Excellent work! Very professional and completed the job on time. Highly recommended for any electrical work.",
                date: "2025-10-15"
            },
            {
                author: "Rajesh Kumar",
                rating: 5,
                text: "Kamal was very knowledgeable and fixed our electrical issues quickly. Great service!",
                date: "2025-10-10"
            },
            {
                author: "Anura Perera",
                rating: 4,
                text: "Good service, fair pricing. Would hire again for future electrical needs.",
                date: "2025-10-05"
            }
        ]
    },
    2: {
        id: 2,
        name: "Nimal Perera",
        category: "Plumbing Expert",
        rating: 4.8,
        reviewCount: 243,
        completedJobs: 312,
        distance: "1.2 km away",
        about: "Licensed plumber offering 24/7 emergency services. Expert in pipe repairs, bathroom installations, water heater services, and drainage solutions. 18 years of experience serving residential and commercial clients.",
        phone: "+94 71 234 5678",
        email: "nimal.perera@fixlanka.lk",
        districts: "Colombo, Dehiwala, Moratuwa",
        availability: "busy",
        image: "https://ui-avatars.com/api/?name=Nimal+Perera&size=200&background=28a745&color=fff&bold=true",
        reviews: [
            {
                author: "Sanduni Dias",
                rating: 5,
                text: "Fixed our leaking pipes promptly. Very reliable and professional service.",
                date: "2025-10-18"
            },
            {
                author: "Chaminda Silva",
                rating: 5,
                text: "Nimal is the best plumber in Colombo! Quick response and excellent work quality.",
                date: "2025-10-12"
            },
            {
                author: "Malini Gunasekara",
                rating: 4,
                text: "Great plumber, solved our drainage issues efficiently.",
                date: "2025-10-08"
            }
        ]
    },
    3: {
        id: 3,
        name: "Saman Fernando",
        category: "HVAC Technician",
        rating: 4.7,
        reviewCount: 89,
        completedJobs: 145,
        distance: "2.1 km away",
        about: "Air conditioning and heating specialist. Quick diagnostics and reliable repair services for all AC brands. Certified technician with expertise in installation, maintenance, and emergency repairs.",
        phone: "+94 76 345 6789",
        email: "saman.fernando@fixlanka.lk",
        districts: "Colombo, Nugegoda, Maharagama",
        availability: "available",
        image: "https://ui-avatars.com/api/?name=Saman+Fernando&size=200&background=ffc107&color=000&bold=true",
        reviews: [
            {
                author: "Ruwan Jayasinghe",
                rating: 5,
                text: "Fixed our AC unit in no time. Very knowledgeable about cooling systems.",
                date: "2025-10-16"
            },
            {
                author: "Lakshmi Rathnayake",
                rating: 4,
                text: "Good service and fair pricing. AC is working perfectly now.",
                date: "2025-10-11"
            },
            {
                author: "Dinesh Wijeratne",
                rating: 5,
                text: "Highly recommend! Saman is very professional and efficient.",
                date: "2025-10-07"
            }
        ]
    },
    4: {
        id: 4,
        name: "Ranjith Kumar",
        category: "Carpentry Specialist",
        rating: 4.6,
        reviewCount: 127,
        completedJobs: 198,
        distance: "3.5 km away",
        about: "Expert carpenter specializing in custom furniture, kitchen cabinets, and home renovations. 12 years of experience creating beautiful and functional woodwork. Quality craftsmanship guaranteed.",
        phone: "+94 75 456 7890",
        email: "ranjith.kumar@fixlanka.lk",
        districts: "Colombo, Kotte, Battaramulla",
        availability: "available",
        image: "https://ui-avatars.com/api/?name=Ranjith+Kumar&size=200&background=dc3545&color=fff&bold=true",
        reviews: [
            {
                author: "Tharaka Mendis",
                rating: 5,
                text: "Built amazing custom shelves for our home. Excellent craftsmanship!",
                date: "2025-10-14"
            },
            {
                author: "Nishantha Silva",
                rating: 4,
                text: "Good work on our kitchen cabinets. Professional and clean.",
                date: "2025-10-09"
            }
        ]
    },
    5: {
        id: 5,
        name: "Pradeep Bandara",
        category: "Painting Professional",
        rating: 4.5,
        reviewCount: 93,
        completedJobs: 167,
        distance: "1.8 km away",
        about: "Professional painting contractor for interior and exterior projects. Specializes in residential and commercial painting with attention to detail. High-quality finishes with premium paints.",
        phone: "+94 77 567 8901",
        email: "pradeep.bandara@fixlanka.lk",
        districts: "Colombo, Rajagiriya, Pannipitiya",
        availability: "unavailable",
        image: "https://ui-avatars.com/api/?name=Pradeep+Bandara&size=200&background=6c757d&color=fff&bold=true",
        reviews: [
            {
                author: "Gayan Perera",
                rating: 5,
                text: "Perfect paint job! Our house looks brand new.",
                date: "2025-10-13"
            },
            {
                author: "Amila Rajapaksa",
                rating: 4,
                text: "Good quality painting work. Very neat and professional.",
                date: "2025-10-06"
            }
        ]
    }
};

/**
 * Open repairer profile popup
 * @param {number} repairerId - The ID of the repairer
 */
function openRepairerProfile(repairerId) {
    const modal = document.getElementById('repairerProfileModal');
    
    if (!modal) {
        console.error('Profile modal not found');
        return;
    }

    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    // Load profile data
    loadRepairerProfile(repairerId);
}

/**
 * Close repairer profile popup
 */
function closeRepairerProfile() {
    const modal = document.getElementById('repairerProfileModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

/**
 * Load repairer profile data
 * @param {number} repairerId - The ID of the repairer
 */
function loadRepairerProfile(repairerId) {
    const data = mockRepairers[repairerId];
    
    if (!data) {
        console.error('Repairer not found');
        return;
    }

    displayProfile(data);
}

/**
 * Display profile data in the modal
 * @param {object} data - The profile data
 */
function displayProfile(data) {
    // Profile Image
    const profileImage = document.getElementById('profileImage');
    profileImage.src = data.image;
    profileImage.alt = data.name;

    // Name and Category
    document.getElementById('profileName').textContent = data.name;
    document.getElementById('profileCategory').textContent = data.category;

    // Rating
    displayRating(data.rating, data.reviewCount);

    // Stats
    document.getElementById('completedJobs').textContent = data.completedJobs;
    document.getElementById('profileDistance').textContent = data.distance;

    // About
    document.getElementById('profileAbout').textContent = data.about;

    // Contact Information
    document.getElementById('profilePhone').textContent = data.phone;
    document.getElementById('profileEmail').textContent = data.email;
    document.getElementById('profileDistricts').textContent = data.districts;

    // Availability
    displayAvailability(data.availability);

    // Reviews
    displayReviews(data.reviews);
}

/**
 * Display rating stars
 * @param {number} rating - Rating value (0-5)
 * @param {number} count - Number of reviews
 */
function displayRating(rating, count) {
    const starsContainer = document.getElementById('profileStars');
    const ratingText = document.getElementById('profileRatingText');

    // Generate stars HTML
    let starsHTML = '';
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    // Full stars
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<i class="fas fa-star"></i>';
    }

    // Half star
    if (hasHalfStar) {
        starsHTML += '<i class="fas fa-star-half-alt"></i>';
    }

    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<i class="far fa-star"></i>';
    }

    starsContainer.innerHTML = starsHTML;
    ratingText.textContent = `${rating.toFixed(1)} (${count} reviews)`;
}

/**
 * Display availability status
 * @param {string} status - Availability status (available, busy, unavailable)
 */
function displayAvailability(status) {
    const badge = document.getElementById('availabilityBadge');
    const text = document.getElementById('availabilityText');

    // Remove all status classes
    badge.classList.remove('busy', 'unavailable');

    // Add appropriate class and text
    switch (status.toLowerCase()) {
        case 'busy':
            badge.classList.add('busy');
            text.textContent = 'Busy';
            break;
        case 'unavailable':
            badge.classList.add('unavailable');
            text.textContent = 'Unavailable';
            break;
        default:
            text.textContent = 'Available';
    }
}

/**
 * Display reviews list
 * @param {array} reviews - Array of review objects
 */
function displayReviews(reviews) {
    const container = document.getElementById('reviewsList');

    if (!reviews || reviews.length === 0) {
        container.innerHTML = `
            <div class="no-reviews">
                <i class="fas fa-comment-slash"></i>
                <p>No reviews yet</p>
            </div>
        `;
        return;
    }

    // Display up to 3 recent reviews
    const recentReviews = reviews.slice(0, 3);
    const reviewsHTML = recentReviews.map(review => {
        const stars = generateStarsHTML(review.rating);
        const date = formatDate(review.date);

        return `
            <div class="review-item">
                <div class="review-header">
                    <span class="review-author">${escapeHtml(review.author)}</span>
                    <div class="review-rating">${stars}</div>
                </div>
                <p class="review-text">${escapeHtml(review.text)}</p>
                <span class="review-date">${date}</span>
            </div>
        `;
    }).join('');

    container.innerHTML = reviewsHTML;
}

/**
 * Generate stars HTML for reviews
 * @param {number} rating - Rating value (1-5)
 * @returns {string} HTML string of stars
 */
function generateStarsHTML(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            html += '<i class="fas fa-star"></i>';
        } else {
            html += '<i class="far fa-star"></i>';
        }
    }
    return html;
}

/**
 * Format date string
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Contact repairer - Opens chat
 */
function contactRepairer() {
    alert('Opening chat... (This will redirect to chat page in the actual application)');
}

/**
 * Request quote from repairer
 */
function requestQuote() {
    alert('Opening quote request form... (This will redirect to post job page in the actual application)');
}

/**
 * View full profile page
 */
function viewFullProfile() {
    alert('Opening full profile page... (This will redirect to provider page in the actual application)');
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRepairerProfile();
    }
});

// Prevent clicks inside modal content from closing the modal
document.addEventListener('DOMContentLoaded', function() {
    const modalContent = document.querySelector('.repairer-modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});
