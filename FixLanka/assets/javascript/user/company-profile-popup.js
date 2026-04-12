// Company Profile Popup JavaScript (loads real DB data)

const COMPANY_APP_BASE = '/2nd-Year-Group-Project/FixLanka';
const DEBUG_COMPANY_POPUP = true;
let activeCompanyId = null;

if (DEBUG_COMPANY_POPUP) {
    console.log('[CompanyPopup] Script loaded');
}

/**
 * Open company profile popup
 * @param {number} companyId - The ID of the company
 * @param {object|null} companyData - Optional company data
 */
function openCompanyProfile(companyId, companyData = null) {
    if (DEBUG_COMPANY_POPUP) {
        console.groupCollapsed('[CompanyPopup] openCompanyProfile()');
        console.log('companyId (raw):', companyId);
        console.log('companyId (number):', Number(companyId));
    }

    const modal = document.getElementById('companyProfileModal');

    if (!modal) {
        console.error('Company Profile modal not found');
        if (DEBUG_COMPANY_POPUP) console.groupEnd();
        return;
    }

    // Show modal
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    if (DEBUG_COMPANY_POPUP) {
        console.log('Modal found + show class added:', modal.classList.contains('show'));
        console.groupEnd();
    }

    // Load profile data
    loadCompanyProfile(companyId, companyData);
}

/**
 * Close company profile popup
 */
function closeCompanyProfile() {
    const modal = document.getElementById('companyProfileModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        if (DEBUG_COMPANY_POPUP) {
            console.log('[CompanyPopup] closeCompanyProfile(): modal hidden');
        }
    }
}

/**
 * Load company profile data
 * @param {number} companyId - The ID of the company
 * @param {object|null} companyData - Optional company data
 */
function loadCompanyProfile(companyId, companyData = null) {
    const modal = document.getElementById('companyProfileModal');
    if (!modal) return;

    const numericId = Number(companyId);
    if (DEBUG_COMPANY_POPUP) {
        console.groupCollapsed('[CompanyPopup] loadCompanyProfile()');
        console.log('companyId (raw):', companyId);
        console.log('companyId (number):', numericId);
    }

    if (!Number.isFinite(numericId) || numericId <= 0) {
        console.error('[CompanyPopup] Invalid companyId, aborting:', companyId);
        if (DEBUG_COMPANY_POPUP) console.groupEnd();
        return;
    }

    // Basic loading state
    document.getElementById('profileName').textContent = 'Loading...';
    document.getElementById('profileType').textContent = 'Service Company';
    document.getElementById('profileAbout').textContent = 'Loading profile information...';
    document.getElementById('profilePhone').textContent = 'N/A';
    document.getElementById('profileEmail').textContent = 'N/A';
    document.getElementById('profileWebsite').textContent = 'N/A';
    document.getElementById('profileAddress').textContent = 'N/A';
    document.getElementById('profileDistricts').textContent = 'N/A';
    displayRating(0);

    try {
        // For now, use the data passed or try to fetch from API
        // In a full implementation, you would fetch from an API like:
        // const response = await fetch(`${COMPANY_APP_BASE}/api/companies.php?action=getDetails&id=${numericId}`);
        
        if (!companyData) {
            throw new Error('Company data not available');
        }

        const c = companyData;
        const name = c.name || c.company_name || 'Company';
        const type = c.business_type || c.company_type || 'Service Company';
        const rating = Number(c.ratings ?? c.rating ?? 0);

        const data = {
            id: c.company_id || companyId,
            name,
            type,
            rating: Number.isFinite(rating) ? rating : 0,
            about: c.description || 'No description available.',
            phone: c.contact_no || c.phone || 'N/A',
            email: c.email || 'N/A',
            website: c.website || 'N/A',
            address: c.address || 'N/A',
            districts: c.districts || 'N/A'
        };

        if (DEBUG_COMPANY_POPUP) console.log('Mapped profile data from company data:', data);
        activeCompanyId = data.id;  // Store the active company ID
        displayProfile(data);
        if (DEBUG_COMPANY_POPUP) console.groupEnd();
    } catch (err) {
        console.error('Failed to load company profile:', err);
        document.getElementById('profileName').textContent = 'Failed to load';
        document.getElementById('profileAbout').textContent = 'Could not load profile details. Please try again.';
        if (DEBUG_COMPANY_POPUP) console.groupEnd();
    }
}

// Export functions for inline onclick
window.openCompanyProfile = openCompanyProfile;
window.closeCompanyProfile = closeCompanyProfile;
window.loadCompanyProfile = loadCompanyProfile;

/**
 * Display profile data in the modal
 * @param {object} data - The profile data
 */
function displayProfile(data) {
    // Logo
    const logoEl = document.getElementById('profileLogo');
    if (logoEl) {
        logoEl.textContent = generateAvatarInitials(data.name);
    }

    // Name and Type
    document.getElementById('profileName').textContent = data.name;
    document.getElementById('profileType').textContent = data.type;

    // Rating
    displayRating(data.rating);

    // About
    document.getElementById('profileAbout').textContent = data.about;

    // Contact Information
    document.getElementById('profilePhone').textContent = data.phone;
    document.getElementById('profileEmail').textContent = data.email;
    document.getElementById('profileWebsite').textContent = data.website;
    document.getElementById('profileAddress').textContent = data.address;
    document.getElementById('profileDistricts').textContent = data.districts;
}

/**
 * Generate avatar initials from name
 * @param {string} name - The name to generate initials from
 * @returns {string} Initials (up to 2 characters)
 */
function generateAvatarInitials(name) {
    if (!name) return 'CO';
    const parts = String(name).trim().split(' ').filter(Boolean);
    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    return String(name).substring(0, 2).toUpperCase();
}

/**
 * Display rating stars
 * @param {number} rating - Rating value (0-5)
 */
function displayRating(rating) {
    const starsContainer = document.getElementById('profileStars');
    const ratingText = document.getElementById('profileRatingText');

    if (!starsContainer || !ratingText) return;

    const value = Number.isFinite(rating) ? rating : 0;
    const fullStars = Math.floor(value);
    const hasHalfStar = value % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    let starsHTML = '';
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
    ratingText.textContent = `${value.toFixed(1)}`;
}

/**
 * Send request for a listed job to company
 */
window.sendCompanyListedJobRequest = function sendCompanyListedJobRequest() {
    if (!activeCompanyId) {
        console.error('No active company ID');
        return;
    }
    // Open listed job request modal for this company
    if (typeof window.openListedJobRequestModal === 'function') {
        window.openListedJobRequestModal(activeCompanyId, 'company');
    } else {
        window.showAlert('Listed job request modal not available', 'error');
    }
};

/**
 * Send request for a new job to company
 */
window.requestCompanyNewJobRequest = function requestCompanyNewJobRequest() {
    if (!activeCompanyId) {
        console.error('No active company ID');
        return;
    }
    // Open direct job request modal for this company
    if (typeof window.openDirectJobRequestModal === 'function') {
        window.openDirectJobRequestModal(activeCompanyId, 'company');
    } else {
        window.showAlert('New job request modal not available', 'error');
    }
};

// Close modal on ESC key
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        closeCompanyProfile();
    }
});

// Prevent clicks inside modal content from closing the modal
document.addEventListener('DOMContentLoaded', function () {
    const modalContent = document.querySelector('.company-modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }
});
