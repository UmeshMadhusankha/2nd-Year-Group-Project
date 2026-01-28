// Repairer Profile Popup JavaScript (loads real DB data)

const DEFAULT_APP_BASE = '/2nd-Year-Group-Project/FixLanka';
const REPAIRERS_API_URL = `${DEFAULT_APP_BASE}/api/repairers.php`;
const DEBUG_REPAIRER_POPUP = true;

if (DEBUG_REPAIRER_POPUP) {
    console.log('[RepairerPopup] Script loaded', { REPAIRERS_API_URL });
}

/**
 * Open repairer profile popup
 * @param {number} repairerId - The ID of the repairer
 */
function openRepairerProfile(repairerId) {
    if (DEBUG_REPAIRER_POPUP) {
        console.groupCollapsed('[RepairerPopup] openRepairerProfile()');
        console.log('repairerId (raw):', repairerId);
        console.log('repairerId (number):', Number(repairerId));
    }

    const modal = document.getElementById('repairerProfileModal');
    
    if (!modal) {
        console.error('Profile modal not found');
        if (DEBUG_REPAIRER_POPUP) console.groupEnd();
        return;
    }

    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    if (DEBUG_REPAIRER_POPUP) {
        console.log('Modal found + show class added:', modal.classList.contains('show'));
        console.groupEnd();
    }

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
        if (DEBUG_REPAIRER_POPUP) {
            console.log('[RepairerPopup] closeRepairerProfile(): modal hidden');
        }
    }
}

/**
 * Load repairer profile data
 * @param {number} repairerId - The ID of the repairer
 */
function loadRepairerProfile(repairerId) {
    const modal = document.getElementById('repairerProfileModal');
    if (!modal) return;

    const numericId = Number(repairerId);
    if (DEBUG_REPAIRER_POPUP) {
        console.groupCollapsed('[RepairerPopup] loadRepairerProfile()');
        console.log('repairerId (raw):', repairerId);
        console.log('repairerId (number):', numericId);
    }

    if (!Number.isFinite(numericId) || numericId <= 0) {
        console.error('[RepairerPopup] Invalid repairerId, aborting:', repairerId);
        if (DEBUG_REPAIRER_POPUP) console.groupEnd();
        return;
    }

    // Basic loading state
    document.getElementById('profileName').textContent = 'Loading...';
    document.getElementById('profileCategory').textContent = 'Service Professional';
    document.getElementById('profileAbout').textContent = 'Loading profile information...';
    document.getElementById('profilePhone').textContent = 'N/A';
    document.getElementById('profileEmail').textContent = 'N/A';
    document.getElementById('profileDistricts').textContent = 'N/A';
    document.getElementById('completedJobs').textContent = '0';
    displayReviews([]);

    const url = `${REPAIRERS_API_URL}?action=getDetails&id=${encodeURIComponent(numericId)}`;
    if (DEBUG_REPAIRER_POPUP) console.log('Fetching:', url);

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
        .then(res => {
            if (DEBUG_REPAIRER_POPUP) console.log('Response:', { status: res.status, ok: res.ok });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return res.json();
        })
        .then(result => {
            if (DEBUG_REPAIRER_POPUP) console.log('JSON:', result);
            if (!result || !result.success || !result.data) {
                throw new Error('Invalid provider response');
            }

            const p = result.data;
            const name = p.full_name || [p.f_name, p.l_name].filter(Boolean).join(' ') || p.name || 'Repairer';
            const category = p.category_name || 'Service Professional';
            const rating = Number(p.ratings ?? 0);
            const reviewCount = Number(p.reviewCount ?? 0);

            const imageUrl = resolveProfileImageUrl(p.profilePicture, name);

            const data = {
                id: p.repairer_id || repairerId,
                name,
                category,
                rating: Number.isFinite(rating) ? rating : 0,
                reviewCount: Number.isFinite(reviewCount) ? reviewCount : 0,
                completedJobs: Number(p.completedJobsCount ?? 0) || 0,
                distance: 'N/A',
                about: p.about || 'No description available.',
                phone: p.phoneNumber || 'N/A',
                email: p.email || 'N/A',
                districts: p.districts || 'N/A',
                availability: p.availability || 'available',
                image: imageUrl,
                reviews: Array.isArray(p.reviews) ? p.reviews : []
            };

            if (DEBUG_REPAIRER_POPUP) console.log('Mapped profile data:', data);
            displayProfile(data);
            if (DEBUG_REPAIRER_POPUP) console.groupEnd();
        })
        .catch(err => {
            console.error('Failed to load repairer profile:', err);
            document.getElementById('profileName').textContent = 'Failed to load';
            document.getElementById('profileAbout').textContent = 'Could not load profile details. Please try again.';
            displayReviews([]);
            if (DEBUG_REPAIRER_POPUP) console.groupEnd();
        });
}

// Export functions for inline onclick + other scripts
window.openRepairerProfile = openRepairerProfile;
window.closeRepairerProfile = closeRepairerProfile;
window.loadRepairerProfile = loadRepairerProfile;

function resolveProfileImageUrl(profilePicture, name) {
    const value = (profilePicture || '').toString().trim();
    if (value) {
        if (value.startsWith('http://') || value.startsWith('https://')) return value;
        if (value.startsWith('/')) return value;
        return `${DEFAULT_APP_BASE}/${value}`;
    }

    const encoded = encodeURIComponent((name || 'Repairer').replace(/\s+/g, '+'));
    return `https://ui-avatars.com/api/?name=${encoded}&size=200&background=17a2b8&color=fff&bold=true`;
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
