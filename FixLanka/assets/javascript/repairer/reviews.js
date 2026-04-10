// ================================================
// REVIEWS PAGE JAVASCRIPT - Connected to Backend
// ================================================

const REVIEWS_REPAIRER_ID = window.CURRENT_REPAIRER_ID || 0;
const REVIEWS_API = '/2nd-Year-Group-Project/FixLanka/api';

// Raw data from API
let allReviews = [];
let currentFilters = { rating: 'all', response: 'all', sort: 'newest' };

document.addEventListener('DOMContentLoaded', function () {
    initializeFilters();
    initializeModal();
    if (REVIEWS_REPAIRER_ID) {
        loadReviewsFromAPI();
    } else {
        showReviewsError('Session expired. Please log in again.');
    }
});

// ===== LOAD FROM API =====
async function loadReviewsFromAPI() {
    const list = document.getElementById('reviewsList');
    if (list) {
        list.innerHTML = `<div style="text-align:center;padding:40px;color:var(--text-secondary)"><i class="fas fa-spinner fa-spin fa-2x"></i><p style="margin-top:12px">Loading reviews...</p></div>`;
    }

    try {
        const res = await fetch(`${REVIEWS_API}/repairer-reviews.php?repairer_id=${REVIEWS_REPAIRER_ID}&limit=100`);
        const data = await res.json();

        if (!data.success) {
            showReviewsError(data.error || 'Failed to load reviews');
            return;
        }

        allReviews = data.reviews || [];
        populateStats(data.stats);
        renderReviews(allReviews);
    } catch (err) {
        console.error('Reviews load error:', err);
        showReviewsError('Failed to connect to the server. Please try again.');
    }
}

function populateStats(stats) {
    if (!stats) return;

    const avg = stats.average_rating || 0;
    const total = stats.total_reviews || 0;
    const positiveRate = stats.positive_rate || 0;

    // Header overview
    setText('overallRatingNumber', avg.toFixed(1));
    setText('reviewCountLabel', `${total} review${total !== 1 ? 's' : ''}`);

    // Star display
    const starsEl = document.getElementById('overallRatingStars');
    if (starsEl) starsEl.innerHTML = renderStars(avg);

    // Stat cards
    setText('totalReviewsCount', total);
    setText('positiveReviewsRate', `${positiveRate}%`);
    setText('responseRate', '—');       // Not tracked yet
    setText('avgResponseTime', '—');    // Not tracked yet
    setText('reviewsSubtitle', `${total} review${total !== 1 ? 's' : ''} total`);
}

function renderStars(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            html += '<i class="fas fa-star" style="color:#f59e0b"></i>';
        } else if (i - 0.5 <= rating) {
            html += '<i class="fas fa-star-half-alt" style="color:#f59e0b"></i>';
        } else {
            html += '<i class="far fa-star" style="color:#f59e0b"></i>';
        }
    }
    return html;
}

function renderReviews(reviews) {
    const list = document.getElementById('reviewsList');
    if (!list) return;

    if (!reviews.length) {
        list.innerHTML = `<div style="text-align:center;padding:40px;color:var(--text-secondary)"><i class="fas fa-star fa-2x"></i><p style="margin-top:12px">No reviews yet. Complete some jobs to receive your first review!</p></div>`;
        setText('reviewsSubtitle', '0 reviews');
        return;
    }

    list.innerHTML = reviews.map(r => createReviewCard(r)).join('');
}

function createReviewCard(review) {
    const rating = parseFloat(review.rating) || 0;
    const author = review.author || 'Anonymous';
    const text = review.text || review.comments || '';
    const date = review.date ? new Date(review.date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—';

    return `
        <div class="review-item" data-rating="${rating}">
            <div class="review-header">
                <div class="reviewer-info">
                    <div class="reviewer-avatar">${escapeHtmlReviews(author.charAt(0).toUpperCase())}</div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">${escapeHtmlReviews(author)}</h4>
                        <span class="review-date">${date}</span>
                    </div>
                </div>
                <div class="review-rating">
                    ${renderStars(rating)}
                    <span class="rating-value">${rating.toFixed(1)}</span>
                </div>
            </div>
            <div class="review-body">
                <p class="review-text">${escapeHtmlReviews(text)}</p>
            </div>
        </div>`;
}

function showReviewsError(message) {
    const list = document.getElementById('reviewsList');
    if (list) {
        list.innerHTML = `<div style="text-align:center;padding:40px;color:#ef4444"><i class="fas fa-exclamation-circle fa-2x"></i><p style="margin-top:12px">${escapeHtmlReviews(message)}</p></div>`;
    }
    setText('reviewsSubtitle', 'Error loading data');
}

function escapeHtmlReviews(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}


// ===== FILTER FUNCTIONALITY =====
function initializeFilters() {
    const ratingFilter = document.getElementById('rating-filter');
    const sortFilter = document.getElementById('sort-filter');
    const clearBtn = document.querySelector('.btn-outline');

    if (ratingFilter) {
        ratingFilter.addEventListener('change', applyFilters);
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', applyFilters);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', clearFilters);
    }
}

function applyFilters() {
    const ratingFilter = document.getElementById('rating-filter').value;
    const sortBy = document.getElementById('sort-filter').value;
    const reviewItems = document.querySelectorAll('.review-item');

    // Convert NodeList to Array for sorting
    const reviewsArray = Array.from(reviewItems);

    // Filter by rating
    reviewsArray.forEach(item => {
        const rating = parseInt(item.dataset.rating);
        if (ratingFilter === 'all' || rating >= parseInt(ratingFilter)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    // Sort reviews
    const visibleReviews = reviewsArray.filter(item => item.style.display !== 'none');

    visibleReviews.sort((a, b) => {
        switch (sortBy) {
            case 'newest':
                return new Date(b.dataset.date) - new Date(a.dataset.date);
            case 'oldest':
                return new Date(a.dataset.date) - new Date(b.dataset.date);
            case 'highest':
                return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
            case 'lowest':
                return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
            default:
                return 0;
        }
    });

    // Reorder DOM elements
    const reviewsList = document.querySelector('.reviews-list');
    if (reviewsList) {
        visibleReviews.forEach(review => {
            reviewsList.appendChild(review);
        });
    }

    // Update visible count
    updateVisibleCount(visibleReviews.length);
}

function clearFilters() {
    document.getElementById('rating-filter').value = 'all';
    document.getElementById('sort-filter').value = 'newest';
    applyFilters();
    showNotification('Filters cleared successfully', 'success');
}

function updateVisibleCount(count) {
    const subtitle = document.querySelector('.section-subtitle');
    if (subtitle) {
        subtitle.textContent = `Showing ${count} review${count !== 1 ? 's' : ''}`;
    }
}

// ===== MODAL FUNCTIONALITY =====
function initializeModal() {
    const modal = document.getElementById('response-modal');
    const modalOverlay = document.querySelector('.modal-overlay');
    const closeBtn = document.querySelector('.modal-close');
    const cancelBtn = document.querySelector('.btn-secondary');
    const sendBtn = document.querySelector('.btn-primary');
    const textarea = document.getElementById('response-text');

    // Close modal handlers
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', function (e) {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });
    }

    // ESC key to close modal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('show')) {
            closeModal();
        }
    });

    // Character counter
    if (textarea) {
        textarea.addEventListener('input', updateCharacterCount);
    }

    // Send response handler
    if (sendBtn) {
        sendBtn.addEventListener('click', sendResponse);
    }
}

function openModal(reviewId, customerName, jobTitle) {
    const modal = document.querySelector('.modal-overlay');
    const customerInfo = document.querySelector('.customer-info');
    const responseText = document.getElementById('response-text');

    if (modal) {
        // Update customer info
        if (customerInfo) {
            customerInfo.innerHTML = `
                <strong>Responding to:</strong> ${customerName}<br>
                <strong>Job:</strong> ${jobTitle}
            `;
        }

        // Clear previous response
        if (responseText) {
            responseText.value = '';
            updateCharacterCount();
        }

        // Store review ID for later use
        modal.dataset.reviewId = reviewId;

        // Show modal
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus on textarea
        setTimeout(() => {
            if (responseText) {
                responseText.focus();
            }
        }, 300);
    }
}

function closeModal() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

function updateCharacterCount() {
    const textarea = document.getElementById('response-text');
    const counter = document.querySelector('.character-count');

    if (textarea && counter) {
        const remaining = 500 - textarea.value.length;
        counter.textContent = `${remaining} characters remaining`;

        if (remaining < 50) {
            counter.style.color = '#ef4444';
        } else if (remaining < 100) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = 'var(--text-secondary)';
        }
    }
}

function sendResponse() {
    const modal = document.querySelector('.modal-overlay');
    const responseText = document.getElementById('response-text');
    const sendBtn = document.querySelector('.btn-primary');

    if (!responseText || !responseText.value.trim()) {
        showNotification('Please enter a response before sending', 'error');
        return;
    }

    if (responseText.value.length > 500) {
        showNotification('Response exceeds 500 character limit', 'error');
        return;
    }

    // Show loading state
    if (sendBtn) {
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        sendBtn.disabled = true;
    }

    // Simulate API call
    setTimeout(() => {
        const reviewId = modal.dataset.reviewId;
        addResponseToReview(reviewId, responseText.value.trim());

        closeModal();
        showNotification('Response sent successfully!', 'success');

        // Reset button
        if (sendBtn) {
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Send Response';
            sendBtn.disabled = false;
        }
    }, 1500);
}

function addResponseToReview(reviewId, responseText) {
    const reviewItem = document.querySelector(`[data-review-id="${reviewId}"]`);
    if (!reviewItem) return;

    // Check if response already exists
    const existingResponse = reviewItem.querySelector('.review-response');
    if (existingResponse) {
        existingResponse.remove();
    }

    // Create response element
    const responseElement = document.createElement('div');
    responseElement.className = 'review-response';
    responseElement.innerHTML = `
        <div class="response-header">
            <i class="fas fa-reply"></i>
            Your Response
        </div>
        <p class="response-text">${responseText}</p>
        <div class="response-date">${new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })}</div>
    `;

    // Insert response before review actions
    const reviewActions = reviewItem.querySelector('.review-actions');
    if (reviewActions) {
        reviewActions.parentNode.insertBefore(responseElement, reviewActions);
    }

    // Update respond button
    const respondBtn = reviewItem.querySelector('.respond-btn');
    if (respondBtn) {
        respondBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Response';
        respondBtn.classList.remove('btn-primary');
        respondBtn.classList.add('btn-secondary');
    }

    // Update stats
    updateStats();
}

// ===== RESPONSE MANAGEMENT =====
function initializeResponses() {
    const respondBtns = document.querySelectorAll('.respond-btn');

    respondBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const reviewItem = this.closest('.review-item');
            const reviewId = reviewItem.dataset.reviewId;
            const customerName = reviewItem.querySelector('.reviewer-name').textContent;
            const jobTitle = reviewItem.querySelector('.review-job').textContent;

            openModal(reviewId, customerName, jobTitle);
        });
    });
}

// ===== ACTION HANDLERS =====
function initializeActions() {
    // Load more button
    const loadMoreBtn = document.querySelector('.load-more-btn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreReviews);
    }
}

// Function to open respond modal
function openRespondModal(reviewId, customerName) {
    const modal = document.getElementById('respondModal');
    const customerNameSpan = document.getElementById('modalCustomerName');
    const responseText = document.getElementById('responseText');
    const charCount = document.getElementById('charCount');
    const modalTitle = modal ? modal.querySelector('.modal-title') : null;

    if (modal) {
        // Reset modal title
        if (modalTitle) {
            modalTitle.textContent = 'Respond to Review';
        }

        // Update customer info
        if (customerNameSpan) {
            customerNameSpan.textContent = customerName;
        }

        // Clear previous response
        if (responseText) {
            responseText.value = '';
            if (charCount) {
                charCount.textContent = '0';
            }
        }

        // Store review ID for later use
        modal.dataset.reviewId = reviewId;

        // Show modal with class
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus on textarea
        setTimeout(() => {
            if (responseText) {
                responseText.focus();
            }
        }, 100);
    }
}

// Function to close respond modal
function closeRespondModal() {
    const modal = document.getElementById('respondModal');
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Function to send response
function sendResponse() {
    const responseText = document.getElementById('responseText');

    if (!responseText || !responseText.value.trim()) {
        showNotification('Please enter a response before sending', 'error');
        return;
    }

    if (responseText.value.length > 500) {
        showNotification('Response exceeds 500 character limit', 'error');
        return;
    }

    // Simulate sending response
    showNotification('Response sent successfully!', 'success');
    closeRespondModal();

    // Update stats
    updateStats();
}

// Function to edit existing response
function editResponse(reviewId) {


    const reviewItems = document.querySelectorAll('.review-item');

    let reviewItem = null;
    let customerName = '';
    let existingResponseText = '';

    // Find the review item by matching the index (reviewId 1 = index 0, reviewId 2 = index 1, etc.)
    reviewItems.forEach((item, index) => {

        if (index + 1 === reviewId) {
            reviewItem = item;
            const nameElement = item.querySelector('.reviewer-name');
            customerName = nameElement ? nameElement.textContent.trim() : '';

            const responseElement = item.querySelector('.response-text');

            if (responseElement) {
                existingResponseText = responseElement.textContent.trim();

                // Remove quotes if present and clean up whitespace
                existingResponseText = existingResponseText
                    .replace(/^["']|["']$/g, '')
                    .replace(/\s+/g, ' ')
                    .trim();

            }
        }
    });




    if (!reviewItem) {
        console.error('Review item not found for ID:', reviewId);
        showNotification('Could not find review to edit', 'error');
        return;
    }

    // Open modal with existing response
    const modal = document.getElementById('respondModal');
    const customerNameSpan = document.getElementById('modalCustomerName');
    const responseText = document.getElementById('responseText');
    const charCount = document.getElementById('charCount');
    const modalTitle = modal ? modal.querySelector('.modal-title') : null;

    if (modal) {

        // Update modal title
        if (modalTitle) {
            modalTitle.textContent = 'Edit Response';
        }

        // Update customer info
        if (customerNameSpan) {
            customerNameSpan.textContent = customerName;
        }

        // Set existing response
        if (responseText) {
            responseText.value = existingResponseText;
            if (charCount) {
                charCount.textContent = existingResponseText.length.toString();
            }
        }

        // Store review ID for later use
        modal.dataset.reviewId = reviewId;

        // Show modal with class
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus on textarea
        setTimeout(() => {
            if (responseText) {
                responseText.focus();
                // Move cursor to end
                responseText.setSelectionRange(responseText.value.length, responseText.value.length);
            }
        }, 100);
    } else {
        console.error('Modal not found!');
        showNotification('Could not open edit modal', 'error');
    }
}

// Character counter for response textarea
document.addEventListener('DOMContentLoaded', function () {
    const responseText = document.getElementById('responseText');
    const charCount = document.getElementById('charCount');

    if (responseText && charCount) {
        responseText.addEventListener('input', function () {
            charCount.textContent = this.value.length;

            // Change color based on character count
            if (this.value.length > 450) {
                charCount.style.color = '#ef4444';
            } else if (this.value.length > 400) {
                charCount.style.color = '#f59e0b';
            } else {
                charCount.style.color = 'inherit';
            }
        });
    }

    // Close modal on overlay click
    const modal = document.getElementById('respondModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeRespondModal();
            }
        });
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('respondModal');
            if (modal && modal.classList.contains('show')) {
                closeRespondModal();
            }
        }
    });
});

// Function to reset filters
function resetFilters() {
    document.getElementById('rating-filter').value = 'all';
    document.getElementById('response-filter').value = 'all';
    document.getElementById('sort-filter').value = 'newest';
    applyFilters();
    showNotification('Filters reset successfully', 'success');
}

// ===== DATA LOADING =====
function loadReviews() {
    // This would typically fetch from an API
    // For now, we'll work with the existing HTML structure
    const reviewItems = document.querySelectorAll('.review-item');

    // Add data attributes for filtering/sorting
    reviewItems.forEach((item, index) => {
        const stars = item.querySelectorAll('.star-rating .filled').length;
        const dateText = item.querySelector('.review-date').textContent;

        item.dataset.reviewId = `review-${index + 1}`;
        item.dataset.rating = stars;
        item.dataset.date = convertDateToISO(dateText);
    });

    updateVisibleCount(reviewItems.length);
}

function loadMoreReviews() {
    const loadMoreBtn = document.querySelector('.load-more-btn');

    if (loadMoreBtn) {
        loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        loadMoreBtn.disabled = true;
    }

    // Simulate API call
    setTimeout(() => {
        // Add new review items (simulation)
        addNewReviews();

        if (loadMoreBtn) {
            loadMoreBtn.innerHTML = '<i class="fas fa-plus"></i> Load More Reviews';
            loadMoreBtn.disabled = false;
        }

        showNotification('More reviews loaded!', 'success');
    }, 2000);
}

function addNewReviews() {
    const reviewsList = document.querySelector('.reviews-list');
    if (!reviewsList) return;

    // Sample new reviews data
    const newReviews = [
        {
            name: 'David Wilson',
            job: 'Plumbing Repair',
            rating: 4,
            date: '2024-01-10',
            text: 'Good service overall. The plumber arrived on time and fixed the issue. Would recommend for basic plumbing needs.'
        },
        {
            name: 'Lisa Brown',
            job: 'House Cleaning',
            rating: 5,
            date: '2024-01-08',
            text: 'Exceptional cleaning service! My house has never looked better. The team was professional and thorough.'
        }
    ];

    newReviews.forEach((review, index) => {
        const reviewElement = createReviewElement(review, Date.now() + index);
        reviewsList.appendChild(reviewElement);
    });

    // Reapply current filters
    applyFilters();
}

function createReviewElement(review, id) {
    const reviewDiv = document.createElement('div');
    reviewDiv.className = 'review-item';
    reviewDiv.dataset.reviewId = `review-${id}`;
    reviewDiv.dataset.rating = review.rating;
    reviewDiv.dataset.date = review.date;

    const stars = Array.from({ length: 5 }, (_, i) =>
        `<i class="fas fa-star ${i < review.rating ? 'filled' : ''}"></i>`
    ).join('');

    reviewDiv.innerHTML = `
        <div class="review-header">
            <div class="reviewer-info">
                <div class="reviewer-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="reviewer-details">
                    <h4 class="reviewer-name">${review.name}</h4>
                    <span class="review-job">${review.job}</span>
                </div>
            </div>
            <div class="review-meta">
                <div class="star-rating">
                    ${stars}
                    <span class="rating-number">${review.rating}.0</span>
                </div>
                <div class="review-date">${formatDate(review.date)}</div>
            </div>
        </div>
        <div class="review-content">
            <p class="review-text">${review.text}</p>
        </div>
        <div class="review-actions">
            <button class="btn btn-primary respond-btn" onclick="openRespondModal('review-${id}', '${review.name}')">
                <i class="fas fa-reply"></i> Respond
            </button>
        </div>
    `;

    return reviewDiv;
}

// ===== STATISTICS =====
function updateStats() {
    const reviewItems = document.querySelectorAll('.review-item');
    const totalReviews = reviewItems.length;
    const responsedReviews = document.querySelectorAll('.review-response').length;
    const pendingReviews = totalReviews - responsedReviews;

    // Calculate average rating
    let totalRating = 0;
    reviewItems.forEach(item => {
        totalRating += parseInt(item.dataset.rating);
    });
    const avgRating = totalReviews > 0 ? (totalRating / totalReviews).toFixed(1) : 0;

    // Update stat cards
    updateStatCard('total-reviews', totalReviews);
    updateStatCard('pending-responses', pendingReviews);
    updateStatCard('average-rating', avgRating);

    // Update overall rating display
    const ratingNumber = document.querySelector('.rating-number');
    const ratingCount = document.querySelector('.rating-count');

    if (ratingNumber) {
        ratingNumber.textContent = avgRating;
    }

    if (ratingCount) {
        ratingCount.textContent = `Based on ${totalReviews} reviews`;
    }
}

function updateStatCard(type, value) {
    const statCards = document.querySelectorAll('.stat-card');

    statCards.forEach(card => {
        const statNumber = card.querySelector('.stat-number');
        const statLabel = card.querySelector('.stat-label');

        if (type === 'total-reviews' && statLabel && statLabel.textContent.includes('Total Reviews')) {
            statNumber.textContent = value;
        } else if (type === 'pending-responses' && statLabel && statLabel.textContent.includes('Pending Responses')) {
            statNumber.textContent = value;
        } else if (type === 'average-rating' && statLabel && statLabel.textContent.includes('Average Rating')) {
            statNumber.textContent = value;
        }
    });
}

// ===== UTILITY FUNCTIONS =====
function convertDateToISO(dateString) {
    // Convert "January 15, 2024" to "2024-01-15"
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">
                ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : ''}
                ${type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : ''}
                ${type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' : ''}
                ${type === 'info' ? '<i class="fas fa-info-circle"></i>' : ''}
            </span>
            <span class="notification-message">${message}</span>
        </div>
        <button class="notification-close">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add notification styles
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: var(--spacing-md) var(--spacing-lg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            max-width: 400px;
            animation: slideInRight 0.3s ease-out;
        }
        
        .notification-success { border-left: 4px solid #10b981; }
        .notification-error { border-left: 4px solid #ef4444; }
        .notification-warning { border-left: 4px solid #f59e0b; }
        .notification-info { border-left: 4px solid var(--primary-color); }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            flex: 1;
        }
        
        .notification-icon {
            font-size: var(--font-size-lg);
        }
        
        .notification-success .notification-icon { color: #10b981; }
        .notification-error .notification-icon { color: #ef4444; }
        .notification-warning .notification-icon { color: #f59e0b; }
        .notification-info .notification-icon { color: var(--primary-color); }
        
        .notification-message {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: 4px;
            transition: all var(--transition-fast);
        }
        
        .notification-close:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;

    // Add style to document head
    if (!document.head.querySelector('style[data-notification-styles]')) {
        style.setAttribute('data-notification-styles', 'true');
        document.head.appendChild(style);
    }

    // Add notification to document
    document.body.appendChild(notification);

    // Add close functionality
    const closeBtn = notification.querySelector('.notification-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            notification.remove();
        });
    }

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
