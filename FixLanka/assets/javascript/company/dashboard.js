// FixLanka Dashboard JavaScript - Enhanced Version with Full Calendar Functionality

// Global calendar state
const calendarState = {
    currentDate: new Date(),
    selectedDate: null,
    events: [],
    systemEvents: [],
    viewMode: 'month'
};

let dashboardData = null;

document.addEventListener('DOMContentLoaded', function () {
    // Initialize all dashboard components
    initializeUI();

    // Initialize tab switching functionality
    initializeTabSwitching();

    // Initialize income chart
    initializeIncomeChart();

    // Initialize sidebar toggle with multiple attempts
    initializeSidebarToggle();

    // Initialize full calendar functionality
    initializeCalendar();

    // Initialize dashboard navigation
    initializeDashboardNavigation();

    // Load real dashboard data from backend
    loadDashboardData();

    // Also try to initialize after a longer delay
    setTimeout(initializeSidebarToggle, 2000);
});

// Initialize dashboard navigation to connect sections with pages
function initializeDashboardNavigation() {
    // KPI Cards Navigation
    initializeKPINavigation();

    // Panel Navigation
    initializePanelNavigation();

    // Action Buttons Navigation
    initializeActionButtonsNavigation();

    // Workforce Navigation (already connected)
    initializeWorkforceNavigation();
}

function initializeKPINavigation() {
    // Make KPI cards clickable to navigate to relevant pages
    const kpiCards = document.querySelectorAll('.kpi-card');

    kpiCards.forEach((card, index) => {
        card.style.cursor = 'pointer';
        card.style.transition = 'all 0.2s ease';

        card.addEventListener('click', function () {
            const cardContent = this.querySelector('.kpi-content h3').textContent;

            switch (cardContent) {
                case 'Active Projects':
                    navigateToPage('projects.php', 'Projects');
                    break;
                case 'Pending Requests':
                    navigateToPage('repair-requests.php', 'Repair Requests');
                    break;
                case 'Total Earnings':
                    // Navigate to payments page for earnings details
                    navigateToPage('payments.php', 'Payments');
                    break;
                case 'Average Rating':
                    // Show rating details modal
                    showRatingModal();
                    break;
            }
        });

        // Add hover effects
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
}

function initializePanelNavigation() {
    // Repair Requests Panel
    const repairRequestsViewAll = document.querySelector('.requests-panel .view-all-btn');
    if (repairRequestsViewAll) {
        repairRequestsViewAll.addEventListener('click', function (e) {
            e.preventDefault();
            navigateToPage('repair-requests.php', 'Repair Requests');
        });
    }

    // Projects Panel
    const projectsNewBtn = document.querySelector('.projects-panel .view-all-btn');
    if (projectsNewBtn) {
        projectsNewBtn.addEventListener('click', function (e) {
            e.preventDefault();
            navigateToPage('projects.php', 'Projects');
        });
    }

    // Contracts Panel
    const contractsViewAll = document.querySelector('.contracts-panel .view-all-btn');
    if (contractsViewAll) {
        contractsViewAll.addEventListener('click', function (e) {
            e.preventDefault();
            navigateToPage('contracts.php', 'Contracts');
        });
    }

    // Payments Panel
    const paymentsViewAll = document.querySelector('.payments-list-section .view-all-btn');
    if (paymentsViewAll) {
        paymentsViewAll.addEventListener('click', function (e) {
            e.preventDefault();
            navigateToPage('payments.php', 'Payments');
        });
    }
}

function initializeActionButtonsNavigation() {
    // Make request cards clickable
    document.addEventListener('click', function (e) {
        // If click is inside the Repair Requests list/table, let its own handlers run
        if (e.target.closest('#requestsList')) return;

        const requestCard = e.target.closest('.request-card');
        if (requestCard && !e.target.closest('.request-actions')) {
            // Navigate to repair requests page with specific request
            navigateToPage('repair-requests.php', 'Repair Requests');
        }

        // Project items clickable
        const projectItem = e.target.closest('.project-item');
        if (projectItem && !e.target.closest('.project-actions')) {
            navigateToPage('projects.php', 'Projects');
        }

        // Contract items clickable
        const contractItem = e.target.closest('.contract-item');
        if (contractItem) {
            navigateToPage('contracts.php', 'Contracts');
        }

        // Payment items clickable
        const paymentItem = e.target.closest('.payment-item');
        if (paymentItem) {
            navigateToPage('payments.php', 'Payments');
        }
    });

    // Action buttons in request cards
    document.addEventListener('click', function (e) {
        // If click is inside the Repair Requests list/table, let its own handlers run
        if (e.target.closest('#requestsList')) return;

        const actionBtn = e.target.closest('.action-btn');
        if (!actionBtn) return;

        e.stopPropagation();

        const buttonText = actionBtn.textContent.trim();
        const requestCard = actionBtn.closest('.request-card');

        switch (buttonText) {
            case 'Accept':
            case 'Decline':
                handleRequestAction(requestCard, buttonText);
                break;
            case 'Assign Worker':
                navigateToPage('workforce.php', 'Workforce');
                break;
            case 'View Details':
                navigateToPage('repair-requests.php', 'Repair Requests');
                break;
            case 'Place Bid':
                showBidModal(requestCard);
                break;
            case 'Start Project':
                navigateToPage('projects.php', 'Projects');
                break;
        }
    });
}

function initializeWorkforceNavigation() {
    // Workforce cards already have navigation - just ensure they work
    const workforceButtons = document.querySelectorAll('.workforce-actions .action-btn');
    workforceButtons.forEach(button => {
        if (!button.onclick) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const category = this.closest('.workforce-item').dataset.category;
                const hash = category ? `#${category}s` : '';
                navigateToPage(`workforce.php${hash}`, 'Workforce');
            });
        }
    });
}

function navigateToPage(url, pageName) {
    // Direct navigation without loading screen
    window.location.href = url;
}

function redirectToRepairRequests(requestId, action) {
    const id = Number(requestId);
    const a = String(action || '').toLowerCase();
    const safeAction = (a === 'quote' || a === 'details') ? a : 'details';
    const url = `/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php?request_id=${encodeURIComponent(id)}&action=${encodeURIComponent(safeAction)}`;
    window.location.href = url;
}

function showEarningsModal() {
    const modal = createModal('earningsModal');

    if (!dashboardData || !Array.isArray(dashboardData.earningsByMonth)) {
        showNotification('Dashboard data not loaded yet', 'warning');
        return;
    }

    const monthlyEarnings = dashboardData.earningsByMonth;
    const totalEarnings = monthlyEarnings.reduce((sum, month) => sum + Number(month.amount || 0), 0);
    const avgMonthly = monthlyEarnings.length > 0 ? (totalEarnings / monthlyEarnings.length) : 0;

    modal.innerHTML = `
        <div class="modal-content earnings-modal">
            <div class="modal-header">
                <h3><i class="fas fa-chart-line"></i> Earnings Overview</h3>
                <button class="modal-close" onclick="closeModal('earningsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="earnings-summary">
                    <div class="summary-card">
                        <h4>Total Earnings</h4>
                        <p class="amount">LKR ${formatCurrency(totalEarnings)}</p>
                    </div>
                    <div class="summary-card">
                        <h4>Average Monthly</h4>
                        <p class="amount">LKR ${formatCurrency(avgMonthly)}</p>
                    </div>
                </div>
                
                <div class="earnings-chart">
                    <h4>Monthly Earnings Trend</h4>
                    <div class="chart-container">
                        ${monthlyEarnings.map(month => `
                            <div class="chart-column">
                                <div class="bar" style="height: ${getEarningsBarHeight(monthlyEarnings, month.amount)}%"></div>
                                <span class="month-label">${month.month}</span>
                                <span class="amount-label">LKR ${formatCurrency((Number(month.amount || 0)) / 1000)}K</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('earningsModal')" class="action-btn secondary">Close</button>
                <button onclick="navigateToPage('repair-requests.php', 'Financial Reports')" class="action-btn primary">
                    View Detailed Reports
                </button>
            </div>
        </div>
    `;

    addEarningsModalStyles();
    showModal('earningsModal');
}

function showRatingModal() {
    const modal = createModal('ratingModal');

    if (!dashboardData) {
        showNotification('Dashboard data not loaded yet', 'warning');
        return;
    }

    const feedbackStats = dashboardData.feedbackStats || { total_reviews: 0, average_rating: 0 };
    const overallRating = (typeof dashboardData.kpis?.average_rating === 'number')
        ? dashboardData.kpis.average_rating
        : (feedbackStats.average_rating || 0);

    const workforce = Array.isArray(dashboardData.workforce) ? dashboardData.workforce : [];
    const ratingData = workforce
        .filter(w => Number(w.avg_rating || 0) > 0)
        .slice(0, 6)
        .map(w => ({
            category: w.specialty,
            rating: Number(w.avg_rating || 0),
            count: Number(w.total || 0)
        }));

    modal.innerHTML = `
        <div class="modal-content rating-modal">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> Customer Ratings</h3>
                <button class="modal-close" onclick="closeModal('ratingModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="rating-overview">
                    <div class="overall-rating">
                        <div class="rating-number">${Number(overallRating || 0).toFixed(1)}</div>
                        <div class="rating-stars">
                            ${generateStars(overallRating || 0)}
                        </div>
                        <div class="rating-text">Based on ${Number(feedbackStats.total_reviews || 0)} reviews</div>
                    </div>
                </div>
                
                <div class="category-ratings">
                    <h4>Ratings by Category</h4>
                    ${ratingData.length > 0 ? ratingData.map(cat => `
                        <div class="category-rating">
                            <div class="category-info">
                                <span class="category-name">${escapeHtml(cat.category)}</span>
                            </div>
                            <div class="rating-display">
                                <span class="rating-number">${cat.rating.toFixed(1)}</span>
                                <div class="rating-stars">${generateStars(cat.rating)}</div>
                                <span class="review-count">(${cat.count})</span>
                            </div>
                        </div>
                    `).join('') : `<div class="review-item"><p>No category ratings yet.</p></div>`}
                </div>
                
                <div class="recent-reviews">
                    <h4>Recent Reviews</h4>
                    ${Array.isArray(dashboardData.feedback) && dashboardData.feedback.length > 0 ? dashboardData.feedback.map(r => `
                        <div class="review-item">
                            <div class="review-header">
                                <span class="reviewer-name">${escapeHtml(r.customer || 'Customer')}</span>
                                <div class="review-rating">${generateStars(Number(r.rating || 0))}</div>
                            </div>
                            <p>${escapeHtml(r.comments || '')}</p>
                            <span class="review-date">${escapeHtml(r.date || '')}</span>
                        </div>
                    `).join('') : `<div class="review-item"><p>No reviews yet.</p></div>`}
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('ratingModal')" class="action-btn secondary">Close</button>
                <button onclick="navigateToPage('support.php', 'Customer Feedback')" class="action-btn primary">
                    View All Reviews
                </button>
            </div>
        </div>
    `;

    addRatingModalStyles();
    showModal('ratingModal');
}



function showBidModal(requestCard) {
    const title = requestCard.querySelector('.title-text').textContent;
    const client = requestCard.querySelector('.request-meta span:nth-child(2)').textContent;

    const modal = createModal('bidModal');
    modal.innerHTML = `
        <div class="modal-content bid-modal">
            <div class="modal-header">
                <h3><i class="fas fa-gavel"></i> Place Bid</h3>
                <button class="modal-close" onclick="closeModal('bidModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="project-info">
                    <h4>${title}</h4>
                    <p>Client: ${client}</p>
                </div>
                
                <form class="bid-form">
                    <div class="form-group">
                        <label>Bid Amount (LKR)</label>
                        <input type="number" placeholder="Enter your bid amount" min="0" step="1000">
                    </div>
                    <div class="form-group">
                        <label>Project Duration</label>
                        <select>
                            <option>1-2 weeks</option>
                            <option>3-4 weeks</option>
                            <option>1-2 months</option>
                            <option>3+ months</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Proposal Description</label>
                        <textarea placeholder="Describe your approach and why you're the best choice"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal('bidModal')" class="action-btn secondary">Cancel</button>
                <button onclick="submitBid()" class="action-btn primary">Submit Bid</button>
            </div>
        </div>
    `;

    showModal('bidModal');
}

function handleRequestAction(requestCard, action) {
    const title = requestCard.querySelector('.title-text').textContent;
    const status = action.toLowerCase() === 'accept' ? 'accepted' : 'declined';

    // Update the request status in the UI
    const statusElement = requestCard.querySelector('.request-status');
    statusElement.textContent = capitalizeFirst(status);
    statusElement.className = `request-status ${status}`;

    // Update action buttons
    const actionsContainer = requestCard.querySelector('.request-actions');
    if (status === 'accepted') {
        actionsContainer.innerHTML = '<button class="action-btn assign">Assign Worker</button>';
    } else {
        actionsContainer.innerHTML = '<button class="action-btn view">View Details</button>';
    }

    showNotification(`Request "${title}" has been ${status}`, 'success');
}

function submitBid() {
    showNotification('Bid submitted successfully!', 'success');
    closeModal('bidModal');
}

// Utility Functions
function createModal(id) {
    let modal = document.getElementById(id);
    if (!modal) {
        modal = document.createElement('div');
        modal.id = id;
        modal.className = 'modal-overlay';
        document.body.appendChild(modal);
    }
    return modal;
}

function showModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Add base modal styles if not present
        if (!document.getElementById('baseModalStyles')) {
            addBaseModalStyles();
        }
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-LK').format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);

    return 'â˜…'.repeat(fullStars) +
        (hasHalfStar ? 'â˜†' : '') +
        'â˜†'.repeat(emptyStars);
}

// Add required styles
function addBaseModalStyles() {
    const styles = document.createElement('style');
    styles.id = 'baseModalStyles';
    styles.textContent = `
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h3 {
            margin: 0;
            color: #0abab5;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .action-btn.primary {
            background: #0abab5;
            color: white;
        }
        .action-btn.secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .action-btn:hover {
            transform: translateY(-1px);
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 4px;
            font-weight: 500;
            color: #374151;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }
    `;
    document.head.appendChild(styles);
}

function addEarningsModalStyles() {
    if (document.getElementById('earningsModalStyles')) return;

    const styles = document.createElement('style');
    styles.id = 'earningsModalStyles';
    styles.textContent = `
        .earnings-modal {
            max-width: 800px;
        }
        .earnings-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .summary-card {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
        }
        .summary-card h4 {
            margin: 0 0 8px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .summary-card .amount {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #0abab5;
        }
        .chart-container {
            display: flex;
            align-items: end;
            gap: 16px;
            height: 200px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .chart-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }
        .bar {
            background: #0abab5;
            width: 20px;
            border-radius: 2px 2px 0 0;
            transition: height 0.6s ease;
        }
        .month-label {
            margin-top: 8px;
            font-size: 12px;
            color: #6b7280;
        }
        .amount-label {
            font-size: 11px;
            color: #0abab5;
            font-weight: 500;
        }
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
    `;
    document.head.appendChild(styles);
}

function addRatingModalStyles() {
    if (document.getElementById('ratingModalStyles')) return;

    const styles = document.createElement('style');
    styles.id = 'ratingModalStyles';
    styles.textContent = `
        .rating-overview {
            text-align: center;
            margin-bottom: 24px;
            padding: 24px;
            background: #f9fafb;
            border-radius: 8px;
        }
        .overall-rating .rating-number {
            font-size: 48px;
            font-weight: bold;
            color: #0abab5;
        }
        .rating-stars {
            font-size: 24px;
            color: #fbbf24;
            margin: 8px 0;
        }
        .category-rating {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .category-trend.positive {
            color: #10b981;
        }
        .category-trend.negative {
            color: #ef4444;
        }
        .rating-display {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .review-item {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            margin: 12px 0;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .review-date {
            font-size: 12px;
            color: #6b7280;
        }
    `;
    document.head.appendChild(styles);
}



// Global functions for modal management
window.closeModal = closeModal;
window.navigateToPage = navigateToPage;
window.submitBid = submitBid;

// Initialize UI elements
function initializeUI() {
    // Load calendar and requests
    loadCalendarEvents();
    updateCalendarDisplay();
    updateUpcomingEvents();
}

// ===== FULL CALENDAR FUNCTIONALITY =====

function initializeCalendar() {
    // Initialize calendar navigation
    const prevButton = document.getElementById('prevMonth');
    const nextButton = document.getElementById('nextMonth');

    if (prevButton) {
        prevButton.addEventListener('click', () => {
            calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() - 1);
            updateCalendarDisplay();
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', () => {
            calendarState.currentDate.setMonth(calendarState.currentDate.getMonth() + 1);
            updateCalendarDisplay();
        });
    }

    // Initialize date clicking
    initializeDateClicking();

    // Initialize keyboard navigation
    initializeKeyboardNavigation();

    // Calendar events are loaded from localStorage via initializeUI()
    updateUpcomingEvents();
}

function loadSampleEvents() {
    // Legacy shim: keep behavior non-destructive
    loadCalendarEvents();
    updateCalendarDisplay();
    updateUpcomingEvents();
}

function loadCalendarEvents() {
    const saved = localStorage.getItem('fixlanka_calendar_events');
    if (saved) {
        try {
            const events = JSON.parse(saved);
            calendarState.events = events.map(event => {
                // Parse date string to avoid timezone issues
                let eventDate;
                if (typeof event.date === 'string') {
                    const parts = event.date.split('T')[0].split('-');
                    const year = parseInt(parts[0], 10);
                    const month = parseInt(parts[1], 10) - 1;
                    const day = parseInt(parts[2], 10);
                    eventDate = new Date(year, month, day);
                } else {
                    eventDate = new Date(event.date);
                }

                return {
                    ...event,
                    date: eventDate
                };
            });
        } catch (e) {
            console.warn('Failed to load calendar events:', e);
            calendarState.events = [];
        }
    } else {
        calendarState.events = [];
    }
}

function setSystemCalendarEvents(systemEvents) {
    if (!Array.isArray(systemEvents)) {
        calendarState.systemEvents = [];
        return;
    }

    calendarState.systemEvents = systemEvents.map(event => {
        let eventDate;
        if (typeof event.date === 'string') {
            const parts = event.date.split('T')[0].split('-');
            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            eventDate = new Date(year, month, day);
        } else {
            eventDate = new Date(event.date);
        }

        return {
            ...event,
            is_system: true,
            date: eventDate,
            time: event.time || 'All day',
            type: event.type || 'system'
        };
    });
}

function getAllCalendarEvents() {
    return [...calendarState.systemEvents, ...calendarState.events];
}

function saveCalendarEvents() {
    try {
        localStorage.setItem('fixlanka_calendar_events', JSON.stringify(calendarState.events));
    } catch (e) {
        console.warn('Failed to save calendar events:', e);
    }
}

function updateCalendarDisplay() {
    updateMonthHeader();
    generateInteractiveCalendar();
    updateUpcomingEvents();
}

function updateMonthHeader() {
    const currentMonthElement = document.getElementById('currentMonth');
    if (currentMonthElement) {
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        const month = monthNames[calendarState.currentDate.getMonth()];
        const year = calendarState.currentDate.getFullYear();
        currentMonthElement.textContent = `${month} ${year}`;
    }
}

function generateInteractiveCalendar() {
    const calendarDates = document.getElementById('calendarDates');
    if (!calendarDates) return;

    const currentMonth = calendarState.currentDate.getMonth();
    const currentYear = calendarState.currentDate.getFullYear();
    const today = new Date();
    const todayDate = today.getDate();
    const todayMonth = today.getMonth();
    const todayYear = today.getFullYear();

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();

    let datesHTML = '';

    // Previous month's trailing dates
    const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
    const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
    const prevMonthDays = new Date(prevYear, prevMonth + 1, 0).getDate();

    for (let i = firstDay - 1; i >= 0; i--) {
        const day = prevMonthDays - i;
        const date = new Date(prevYear, prevMonth, day);
        const events = getEventsForDate(date);

        datesHTML += `
            <div class="calendar-date other-month" 
                 data-date="${formatDateString(date)}"
                 onclick="selectDate('${formatDateString(date)}')">
                ${day}
                ${events.length > 0 ? '<div class="event-indicator"></div>' : ''}
            </div>`;
    }

    // Current month dates
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(currentYear, currentMonth, day);
        const events = getEventsForDate(date);
        const isToday = day === todayDate && currentMonth === todayMonth && currentYear === todayYear;
        const isSelected = calendarState.selectedDate &&
            formatDateString(date) === formatDateString(calendarState.selectedDate);

        let classes = 'calendar-date';
        if (isToday) classes += ' today';
        if (isSelected) classes += ' selected';
        if (events.length > 0) classes += ' has-event';

        datesHTML += `
            <div class="${classes}" 
                 data-date="${formatDateString(date)}"
                 onclick="selectDate('${formatDateString(date)}')"
                 title="${events.length > 0 ? events.map(e => e.title).join(', ') : ''}">
                ${day}
                ${events.length > 0 ? `<div class="event-indicator">${events.length}</div>` : ''}
            </div>`;
    }

    // Next month's leading dates
    const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
    const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);

    for (let day = 1; day <= remainingCells; day++) {
        const date = new Date(nextYear, nextMonth, day);
        const events = getEventsForDate(date);

        datesHTML += `
            <div class="calendar-date other-month" 
                 data-date="${formatDateString(date)}"
                 onclick="selectDate('${formatDateString(date)}')">
                ${day}
                ${events.length > 0 ? '<div class="event-indicator"></div>' : ''}
            </div>`;
    }

    calendarDates.innerHTML = datesHTML;
}

function initializeDateClicking() {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('calendar-date')) {
            const dateString = e.target.dataset.date;
            if (dateString) {
                selectDate(dateString);
            }
        }
    });
}

function selectDate(dateString) {
    // Parse date string properly to avoid timezone issues
    // Split the date string (YYYY-MM-DD) and create date in local timezone
    const parts = dateString.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1; // Month is 0-indexed
    const day = parseInt(parts[2], 10);

    calendarState.selectedDate = new Date(year, month, day);

    // Update calendar display
    updateCalendarDisplay();

    // Show events for selected date
    showEventsForDate(calendarState.selectedDate);
}

function showEventsForDate(date) {
    const events = getEventsForDate(date);
    const dateStr = formatDisplayDate(date);

    if (events.length === 0) {
        showDateModal(dateStr, 'No events scheduled for this date.', []);
        return;
    }

    showDateModal(dateStr, `${events.length} event(s) scheduled:`, events);
}

function showDateModal(dateStr, message, events) {
    // Remove existing modal
    const existingModal = document.getElementById('dateModal');
    if (existingModal) {
        existingModal.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'dateModal';
    modal.className = 'modal-overlay active';

    const eventsHTML = events.map(event => {
        const canEdit = !event.is_system;
        const actionsHTML = canEdit ? `
            <div class="event-actions">
                <button onclick="editEvent('${event.id}')" class="action-btn secondary">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deleteEvent('${event.id}')" class="action-btn danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        ` : '';

        return `
        <div class="event-item ${event.type}">
            <div class="event-header">
                <h4>${event.title}</h4>
                <span class="event-time">${event.time}</span>
            </div>
            <p class="event-description">${event.description}</p>
            ${event.client ? `<div class="event-meta">Client: ${event.client}</div>` : ''}
            ${event.location ? `<div class="event-meta">Location: ${event.location}</div>` : ''}
            ${event.participants ? `<div class="event-meta">Participants: ${event.participants.join(', ')}</div>` : ''}
            ${actionsHTML}
        </div>
    `;
    }).join('');

    modal.innerHTML = `
        <div class="modal-content date-modal">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-day"></i> ${dateStr}</h3>
                <button class="modal-close" onclick="closeDateModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>${message}</p>
                ${eventsHTML}
            </div>
            <div class="modal-footer">
                <button onclick="closeDateModal()" class="action-btn secondary">Close</button>
                <button onclick="addEventToDate('${formatDateString(calendarState.selectedDate)}')" class="action-btn primary">
                    <i class="fas fa-plus"></i> Add Event
                </button>
            </div>
        </div>
    `;

    // Add styles
    if (!document.getElementById('calendarModalStyles')) {
        const styles = document.createElement('style');
        styles.id = 'calendarModalStyles';
        styles.textContent = `
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }
            .modal-overlay.active {
                opacity: 1;
                pointer-events: auto;
            }
            .modal-content {
                background: white;
                border-radius: 12px;
                max-width: 700px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }
            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .modal-header h3 {
                margin: 0;
                color: #0abab5;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .modal-close {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #6b7280;
                padding: 4px;
            }
            .modal-body {
                padding: 24px;
            }
            .modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                gap: 12px;
                justify-content: flex-end;
            }
            .event-item {
                background: #f9fafb;
                border-radius: 8px;
                padding: 20px;
                margin: 12px 0;
                border-left: 4px solid #0abab5;
                display: grid;
                grid-template-columns: 1fr auto;
                gap: 16px;
            }
            .event-item.meeting {
                border-left-color: #3b82f6;
            }
            .event-item.deadline {
                border-left-color: #ef4444;
            }
            .event-item.consultation {
                border-left-color: #f59e0b;
            }
            .event-item.maintenance {
                border-left-color: #8b5cf6;
            }
            .event-item.training {
                border-left-color: #10b981;
            }
            .event-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 8px;
                gap: 16px;
                grid-column: 1 / -1;
            }
            .event-header h4 {
                margin: 0;
                color: #111827;
                font-size: 16px;
                flex: 1;
            }
            .event-time {
                color: #6b7280;
                font-size: 14px;
                font-weight: 500;
                white-space: nowrap;
            }
            .event-description {
                color: #4b5563;
                margin: 8px 0;
                font-size: 14px;
                line-height: 1.5;
                grid-column: 1 / -1;
            }
            .event-meta {
                color: #6b7280;
                font-size: 13px;
                margin: 4px 0;
                grid-column: 1 / -1;
            }
            .event-actions {
                margin-top: 12px;
                display: flex;
                gap: 8px;
                grid-column: 1 / -1;
                justify-content: flex-end;
            }
            .action-btn {
                padding: 6px 12px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 12px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 4px;
                transition: all 0.2s ease;
            }
            .action-btn.primary {
                background: #0abab5;
                color: white;
            }
            .action-btn.secondary {
                background: #f3f4f6;
                color: #374151;
            }
            .action-btn.danger {
                background: #fee2e2;
                color: #dc2626;
            }
            .action-btn:hover {
                transform: translateY(-1px);
            }
            
            @media (max-width: 768px) {
                .modal-content {
                    max-width: 95%;
                    width: 95%;
                }
                .event-item {
                    padding: 16px;
                }
                .event-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 8px;
                }
                .event-time {
                    white-space: normal;
                }
                .event-actions {
                    flex-direction: column;
                }
                .action-btn {
                    width: 100%;
                    justify-content: center;
                }
            }
            
            .calendar-date {
                cursor: pointer;
                transition: all 0.2s ease;
                position: relative;
            }
            .calendar-date:hover {
                background: #f0f9ff;
                transform: scale(1.05);
            }
            .calendar-date.selected {
                background: #0abab5 !important;
                color: white;
            }
            .event-indicator {
                position: absolute;
                bottom: 2px;
                right: 2px;
                background: #ef4444;
                color: white;
                border-radius: 50%;
                width: 16px;
                height: 16px;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
            .calendar-date.other-month .event-indicator {
                background: #9ca3af;
            }
        `;
        document.head.appendChild(styles);
    }

    // Click outside to close
    modal.addEventListener('mousedown', (e) => {
        if (e.target === modal) {
            closeDateModal();
        }
    });

    // Escape to close (installed once)
    if (!window.__fixlankaDashboardModalEsc) {
        window.__fixlankaDashboardModalEsc = true;
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            if (document.getElementById('eventFormModal')) {
                closeEventForm();
                return;
            }
            if (document.getElementById('dateModal')) {
                closeDateModal();
            }
        });
    }

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';
}

function closeDateModal() {
    const modal = document.getElementById('dateModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

function addEventToDate(dateString) {
    closeDateModal();
    // Parse date string properly to avoid timezone issues
    const parts = dateString.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const day = parseInt(parts[2], 10);
    showEventForm(new Date(year, month, day));
}

function showEventForm(date = null, event = null) {
    const isEdit = event !== null;
    const targetDate = date || new Date();

    // Remove existing modal
    const existingModal = document.getElementById('eventFormModal');
    if (existingModal) {
        existingModal.remove();
    }

    const modal = document.createElement('div');
    modal.id = 'eventFormModal';
    modal.className = 'modal-overlay active';

    modal.innerHTML = `
        <div class="modal-content event-form-modal">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-plus"></i> ${isEdit ? 'Edit Event' : 'Add New Event'}</h3>
                <button class="modal-close" onclick="closeEventForm()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm" class="event-form">
                    <input type="hidden" id="eventId" value="${event?.id || ''}">
                    
                    <div class="form-group">
                        <label for="eventTitle">Event Title *</label>
                        <input type="text" id="eventTitle" value="${event?.title || ''}" required placeholder="Enter event title">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="eventDate">Date *</label>
                            <input type="date" id="eventDate" value="${formatDateForInput(targetDate)}" required>
                        </div>
                        <div class="form-group">
                            <label for="eventTime">Time</label>
                            <input type="time" id="eventTime" value="${parseTimeForInput(event?.time)}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventType">Event Type</label>
                        <select id="eventType">
                            <option value="meeting" ${event?.type === 'meeting' ? 'selected' : ''}>Meeting</option>
                            <option value="deadline" ${event?.type === 'deadline' ? 'selected' : ''}>Deadline</option>
                            <option value="consultation" ${event?.type === 'consultation' ? 'selected' : ''}>Consultation</option>
                            <option value="maintenance" ${event?.type === 'maintenance' ? 'selected' : ''}>Maintenance</option>
                            <option value="training" ${event?.type === 'training' ? 'selected' : ''}>Training</option>
                            <option value="other" ${event?.type === 'other' ? 'selected' : ''}>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventDescription">Description</label>
                        <textarea id="eventDescription" placeholder="Enter event description">${event?.description || ''}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventLocation">Location</label>
                        <input type="text" id="eventLocation" value="${event?.location || ''}" placeholder="Enter location">
                    </div>
                    
                    <div class="form-group">
                        <label for="eventParticipants">Participants (comma-separated)</label>
                        <input type="text" id="eventParticipants" value="${event?.participants?.join(', ') || ''}" placeholder="Enter participant names">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button onclick="closeEventForm()" class="action-btn secondary">Cancel</button>
                <button onclick="saveEvent(${isEdit})" class="action-btn primary">
                    ${isEdit ? 'Update Event' : 'Create Event'}
                </button>
            </div>
        </div>
    `;

    // Add form styles
    if (!document.getElementById('eventFormStyles')) {
        const styles = document.createElement('style');
        styles.id = 'eventFormStyles';
        styles.textContent = `
            .event-form-modal {
                max-width: 600px;
            }
            .event-form {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            .form-group {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .form-group label {
                font-weight: 500;
                color: #374151;
                font-size: 14px;
            }
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: 8px 12px;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                font-size: 14px;
                transition: border-color 0.2s ease;
            }
            .form-group input:focus,
            .form-group select:focus,
            .form-group textarea:focus {
                outline: none;
                border-color: #0abab5;
                box-shadow: 0 0 0 3px rgba(10, 186, 181, 0.1);
            }
            .form-group textarea {
                resize: vertical;
                min-height: 80px;
            }
        `;
        document.head.appendChild(styles);
    }

    document.body.appendChild(modal);
    document.body.style.overflow = 'hidden';

    // Click outside to close
    modal.addEventListener('mousedown', (e) => {
        if (e.target === modal) {
            closeEventForm();
        }
    });
}

function closeEventForm() {
    const modal = document.getElementById('eventFormModal');
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

function saveEvent(isEdit) {
    const form = document.getElementById('eventForm');
    const formData = new FormData(form);

    // Parse date string to avoid timezone issues
    const dateStr = document.getElementById('eventDate').value;
    const parts = dateStr.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const day = parseInt(parts[2], 10);

    const eventData = {
        id: isEdit ? document.getElementById('eventId').value : generateEventId(),
        title: document.getElementById('eventTitle').value,
        date: new Date(year, month, day),
        time: document.getElementById('eventTime').value || '12:00',
        type: document.getElementById('eventType').value,
        description: document.getElementById('eventDescription').value,
        location: document.getElementById('eventLocation').value,
        participants: document.getElementById('eventParticipants').value
            .split(',').map(p => p.trim()).filter(p => p)
    };

    // Validate required fields
    if (!eventData.title) {
        showNotification('Event title is required', 'error');
        return;
    }

    if (isEdit) {
        // Update existing event
        const index = calendarState.events.findIndex(e => e.id === eventData.id);
        if (index !== -1) {
            calendarState.events[index] = eventData;
        }
    } else {
        // Add new event
        calendarState.events.push(eventData);
    }

    saveCalendarEvents();
    updateCalendarDisplay();
    closeEventForm();

    showNotification(
        isEdit ? 'Event updated successfully' : 'Event created successfully',
        'success'
    );
}

function editEvent(eventId) {
    const event = calendarState.events.find(e => e.id === eventId);
    if (event) {
        closeDateModal();
        showEventForm(event.date, event);
        return;
    }

    const systemEvent = calendarState.systemEvents.find(e => e.id === eventId);
    if (systemEvent) {
        showNotification('System events cannot be edited', 'info');
    }
}

function deleteEvent(eventId) {
    const systemEvent = calendarState.systemEvents.find(e => e.id === eventId);
    if (systemEvent) {
        showNotification('System events cannot be deleted', 'info');
        return;
    }

    if (confirm('Are you sure you want to delete this event?')) {
        calendarState.events = calendarState.events.filter(e => e.id !== eventId);
        saveCalendarEvents();
        updateCalendarDisplay();
        closeDateModal();
        showNotification('Event deleted successfully', 'success');
    }
}

function updateUpcomingEvents() {
    const upcomingContainer = document.querySelector('.upcoming-events');
    if (!upcomingContainer) return;

    const today = new Date();
    const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const upcomingEvents = getAllCalendarEvents()
        .filter(event => event.date >= todayStart)
        .sort((a, b) => a.date - b.date)
        .slice(0, 3);

    const headerHTML = '<h4><i class="fas fa-clock"></i> Upcoming Events</h4>';

    if (upcomingEvents.length === 0) {
        upcomingContainer.innerHTML = headerHTML + '<p class="no-events">No upcoming events</p>';
        return;
    }

    const eventsHTML = upcomingEvents.map(event => `
        <div class="event-item ${event.type}" onclick="selectDate('${formatDateString(event.date)}')">
            <div class="event-date">${formatShortDate(event.date)}</div>
            <div class="event-title">${event.title}</div>
            <div class="event-time">${event.time}</div>
        </div>
    `).join('');

    upcomingContainer.innerHTML = headerHTML + eventsHTML;

    // Add styles for upcoming events
    if (!document.getElementById('upcomingEventsStyles')) {
        const styles = document.createElement('style');
        styles.id = 'upcomingEventsStyles';
        styles.textContent = `
            .upcoming-events .event-item {
                cursor: pointer;
                transition: all 0.2s ease;
                border-radius: 6px;
                padding: 12px;
                margin: 8px 0;
                background: #f9fafb;
                border-left: 3px solid #0abab5;
            }
            .upcoming-events .event-item:hover {
                background: #f0f9ff;
                transform: translateX(4px);
            }
            .upcoming-events .event-item.meeting {
                border-left-color: #3b82f6;
            }
            .upcoming-events .event-item.deadline {
                border-left-color: #ef4444;
            }
            .upcoming-events .event-item.consultation {
                border-left-color: #f59e0b;
            }
            .upcoming-events .event-item.maintenance {
                border-left-color: #8b5cf6;
            }
            .upcoming-events .event-item.training {
                border-left-color: #10b981;
            }
            .upcoming-events .event-time {
                color: #6b7280;
                font-size: 12px;
                margin-top: 4px;
            }
            .no-events {
                color: #6b7280;
                font-style: italic;
                text-align: center;
                padding: 16px;
            }
        `;
        document.head.appendChild(styles);
    }
}

function initializeKeyboardNavigation() {
    document.addEventListener('keydown', function (e) {
        if (e.target.closest('.modal-overlay')) return; // Don't interfere with modals

        const selectedDate = calendarState.selectedDate || new Date();

        switch (e.key) {
            case 'ArrowLeft':
                e.preventDefault();
                navigateDate(selectedDate, -1);
                break;
            case 'ArrowRight':
                e.preventDefault();
                navigateDate(selectedDate, 1);
                break;
            case 'ArrowUp':
                e.preventDefault();
                navigateDate(selectedDate, -7);
                break;
            case 'ArrowDown':
                e.preventDefault();
                navigateDate(selectedDate, 7);
                break;
            case 'Enter':
                if (calendarState.selectedDate) {
                    e.preventDefault();
                    showEventsForDate(calendarState.selectedDate);
                }
                break;
            case 'n':
                if (e.ctrlKey) {
                    e.preventDefault();
                    showEventForm(calendarState.selectedDate || new Date());
                }
                break;
        }
    });
}

function navigateDate(currentDate, days) {
    const newDate = new Date(currentDate);
    newDate.setDate(newDate.getDate() + days);

    // Update current month if necessary
    if (newDate.getMonth() !== calendarState.currentDate.getMonth() ||
        newDate.getFullYear() !== calendarState.currentDate.getFullYear()) {
        calendarState.currentDate = new Date(newDate);
    }

    selectDate(formatDateString(newDate));
}

// Utility Functions
function getEventsForDate(date) {
    return getAllCalendarEvents().filter(event =>
        event.date.toDateString() === date.toDateString()
    );
}

function formatDateString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// âœ… FIX: Same local-safe version for input fields
function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}


function formatDisplayDate(date) {
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatShortDate(date) {
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric'
    });
}

function parseTimeForInput(timeString) {
    if (!timeString) return '';

    // Convert "10:00 AM" to "10:00"
    const match = timeString.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (match) {
        let hours = parseInt(match[1]);
        const minutes = match[2];
        const ampm = match[3].toUpperCase();

        if (ampm === 'PM' && hours !== 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;

        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    return timeString;
}

function generateEventId() {
    return 'evt-' + Date.now().toString(36) + Math.random().toString(36).substr(2);
}

function showNotification(message, type = 'info') {
    // Remove existing notification
    const existingNotification = document.getElementById('calendar-notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.id = 'calendar-notification';
    notification.className = `notification ${type}`;

    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };

    notification.innerHTML = `
        <i class="${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 400px;
    `;

    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function getNotificationColor(type) {
    const colors = {
        success: '#10b981',
        error: '#ef4444',
        warning: '#f59e0b',
        info: '#0abab5'
    };
    return colors[type] || colors.info;
}

// Global functions for external access
window.selectDate = selectDate;
window.closeDateModal = closeDateModal;
window.closeEventForm = closeEventForm;
window.addEventToDate = addEventToDate;
window.saveEvent = saveEvent;
window.editEvent = editEvent;
window.deleteEvent = deleteEvent;

// Load static requests without interactions
function loadStaticRequests() {
    const requestsList = document.getElementById('requestsList');
    if (!requestsList) return;

    if (!dashboardData || !dashboardData.requests) {
        requestsList.innerHTML = '<div class="empty-state">Loading requests...</div>';
        return;
    }

    const directCount = Array.isArray(dashboardData.requests.direct) ? dashboardData.requests.direct.length : 0;
    const publicCount = Array.isArray(dashboardData.requests.public) ? dashboardData.requests.public.length : 0;

    const tabButtons = document.querySelectorAll('.requests-panel .tab-button');
    const activeBtn = document.querySelector('.requests-panel .tab-button.active');
    const activeType = activeBtn?.getAttribute('data-tab');

    let initialType = 'public';

    if (activeType === 'public' && publicCount > 0) {
        initialType = 'public';
    } else if (activeType === 'direct' && directCount > 0) {
        initialType = 'direct';
    } else if (activeType === 'direct' && directCount > 0) {
        initialType = 'direct';
    } else if (publicCount > 0) {
        initialType = 'public';
    } else {
        initialType = 'direct';
    }

    // Keep UI in sync with what we render
    tabButtons.forEach(btn => btn.classList.remove('active'));
    const btnToActivate = document.querySelector(`.requests-panel .tab-button[data-tab="${initialType}"]`);
    if (btnToActivate) btnToActivate.classList.add('active');

    loadRequestsByType(initialType);
}

// Initialize tab switching functionality
function initializeTabSwitching() {
    const tabButtons = document.querySelectorAll('.requests-panel .tab-button');

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            const tabType = this.getAttribute('data-tab');

            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked button
            this.classList.add('active');

            // Load requests for the selected tab
            loadRequestsByType(tabType);
        });
    });
}

// Load requests by type (direct or public)
async function loadRequestsByType(type) {
    const requestsList = document.getElementById('requestsList');
    if (!requestsList) return;

    if (!dashboardData || !dashboardData.requests) {
        requestsList.innerHTML = '<div class="empty-state">Unable to load requests.</div>';
        return;
    }

    // For Public tab: use same API as the Repair Requests page to keep behavior consistent.
    if (type === 'public') {
        try {
            requestsList.innerHTML = '<div class="loading-state" style="text-align:center;padding:2rem;">Loading requests...</div>';
            const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/job-requests.php?status=pending');
            const json = await res.json();
            const rows = (json && json.success && Array.isArray(json.data)) ? json.data : [];

            // Sort newest-first using whatever timestamp field exists
            const sorted = [...rows].sort((a, b) => {
                const ad = new Date(a.created_at || a.dateCreated || 0);
                const bd = new Date(b.created_at || b.dateCreated || 0);
                const diff = bd - ad;
                if (diff !== 0) return diff;
                return Number(b.request_id || 0) - Number(a.request_id || 0);
            });

            const limited = sorted.slice(0, 5);
            const mapped = limited.map(r => ({
                request_id: Number(r.request_id),
                title: r.title,
                category: r.category_name || r.category || 'General',
                customer: r.customer_name || '',
                date: r.posted_ago || (r.dateCreated ? formatDateShort(r.dateCreated) : ''),
                deadline: r.finish_date ? formatDateShort(r.finish_date) : null,
                status: 'bidding'
            }));

            if (mapped.length === 0) {
                requestsList.innerHTML = '<div class="empty-state">No requests found.</div>';
                return;
            }

            renderRequestsTable(mapped, 'public');
        } catch (e) {
            console.error(e);
            requestsList.innerHTML = '<div class="empty-state">Unable to load requests.</div>';
        }
        return;
    }

    const requestsData = dashboardData.requests[type] || [];

    if (!Array.isArray(requestsData) || requestsData.length === 0) {
        requestsList.innerHTML = '<div class="empty-state">No requests found.</div>';
        return;
    }

    renderRequestsTable(requestsData, type);

    // Add fade animation
    requestsList.style.opacity = '0';
    setTimeout(() => {
        // renderRequestsTable already replaced contents
        requestsList.style.opacity = '1';
    }, 150);
}

function renderRequestsTable(requestsData, type) {
    const requestsList = document.getElementById('requestsList');
    if (!requestsList) return;

    const isPublic = type === 'public';
    const tableHead = isPublic ? `
        <thead>
            <tr>
                <th>Request Details</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
        </thead>
    ` : `
        <thead>
            <tr>
                <th>Request Details</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
    `;

    const rowsHtml = (Array.isArray(requestsData) ? requestsData : []).map(request => {
        const initials = getInitialsFromName(request.customer || '');
        const status = String(request.status || 'pending').toLowerCase();
        const statusClass = status === 'accepted' ? 'accepted' : (status === 'completed' ? 'accepted' : 'pending');
        const statusIcon = status === 'accepted' ? 'check' : (status === 'completed' ? 'check-circle' : 'clock');
        const deadline = request.deadline ? escapeHtml(request.deadline) : '—';

        const publicActions = `
            <div class="table-actions">
                <button class="action-btn small primary" type="button" onclick="redirectToRepairRequests(${Number(request.request_id)}, 'quote')">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Submit Quotation
                </button>
                <button class="action-btn small secondary" type="button" onclick="redirectToRepairRequests(${Number(request.request_id)}, 'details')">
                    <i class="fas fa-eye"></i>
                    View
                </button>
            </div>
        `;

        const directActions = `
            <div class="table-actions">
                <button class="action-btn small secondary" type="button" onclick="redirectToRepairRequests(${Number(request.request_id)}, 'details')">
                    <i class="fas fa-eye"></i>
                    View
                </button>
            </div>
        `;

        return `
            <tr data-request-id="${Number(request.request_id)}">
                <td>
                    <div>
                        <h5>${escapeHtml(request.title || '')}</h5>
                        <p style="margin: 0; color: var(--text-secondary); font-size: var(--font-size-sm);">
                            #REQ-${Number(request.request_id)} &bull; ${escapeHtml(request.category || 'General')}
                        </p>
                    </div>
                </td>
                <td>
                    <div class="table-customer">
                        <div class="table-customer-avatar">${escapeHtml(initials)}</div>
                        <div class="table-customer-info">
                            <h5>${escapeHtml(request.customer || '')}</h5>
                            <p></p>
                        </div>
                    </div>
                </td>
                <td>${escapeHtml(request.date || '')}</td>
                <td>${deadline}</td>
                ${isPublic ? '' : `
                <td>
                    <span class="status-badge ${statusClass}">
                        <i class="fas fa-${statusIcon}"></i>
                        ${capitalizeFirst(status)}
                    </span>
                </td>
                `}
                <td>${isPublic ? publicActions : directActions}</td>
            </tr>
        `;
    }).join('');

    requestsList.innerHTML = `
        <table class="requests-table">
            ${tableHead}
            <tbody>
                ${rowsHtml}
            </tbody>
        </table>
    `;
}

async function loadDashboardData(chartPeriodOverride) {
    const periodText = document.querySelector('.period-selector')?.value || 'Last 7 Days';
    const mappedPeriod = chartPeriodOverride || (periodText === 'Last 30 Days' ? '30d' : (periodText === 'Last 3 Months' ? '3m' : '7d'));

    try {
        const res = await fetch(`/2nd-Year-Group-Project/FixLanka/api/company-dashboard.php?chart_period=${encodeURIComponent(mappedPeriod)}`);
        const json = await res.json();

        if (!json || json.success !== true) {
            throw new Error(json?.message || 'Failed to load dashboard');
        }

        dashboardData = json.data;

        // Merge read-only system events (projects/milestones) into calendar
        setSystemCalendarEvents(dashboardData?.calendar?.system_events || []);
        updateCalendarDisplay();

        renderKPIs(dashboardData.kpis);
        renderProjects(dashboardData.projects);
        renderContracts(dashboardData.contracts);
        renderPayments(dashboardData.payments);
        renderIncomeChart(dashboardData.incomeChart);
        renderWorkforce(dashboardData.workforce);
        renderFeedback(dashboardData.feedback);
        renderSupportTickets(dashboardData.supportTickets);

        loadStaticRequests();
    } catch (e) {
        console.error(e);
        showNotification('Failed to load dashboard data', 'error');
    }
}

function renderKPIs(kpis) {
    if (!kpis) return;

    const activeEl = document.getElementById('kpiActiveProjects');
    const pendingEl = document.getElementById('kpiPendingRequests');
    const earningsEl = document.getElementById('kpiTotalEarnings');
    const ratingEl = document.getElementById('kpiAverageRating');

    if (activeEl) activeEl.textContent = Number(kpis.active_projects ?? 0);
    if (pendingEl) pendingEl.textContent = Number(kpis.pending_requests ?? 0);
    if (earningsEl) earningsEl.textContent = `LKR ${formatCurrency(Number(kpis.total_earnings ?? 0))}`;
    if (ratingEl) ratingEl.textContent = Number(kpis.average_rating ?? 0).toFixed(1);
}

function renderProjects(projects) {
    const container = document.getElementById('dashboardProjectsList');
    if (!container) return;

    if (!Array.isArray(projects) || projects.length === 0) {
        container.innerHTML = '<div class="empty-state">No projects found.</div>';
        return;
    }

    container.innerHTML = projects.map(p => {
        const mapped = mapProjectStatus(p.status);
        const progress = clampNumber(p.progress, 0, 100);
        const dateText = p.end_date ? `Due: ${formatDateShort(p.end_date)}` : (p.start_date ? `Starts: ${formatDateShort(p.start_date)}` : '');

        return `
            <div class="project-item">
                <div class="project-info">
                    <h4>${escapeHtml(p.title || '')}</h4>
                    <p><strong>Client:</strong> ${escapeHtml(p.client || '')}</p>
                    ${dateText ? `<span class="project-date">${escapeHtml(dateText)}</span>` : ''}
                </div>
                <div class="project-details">
                    <div class="project-status ${mapped.className}">${escapeHtml(mapped.label)}</div>
                    <div class="project-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ${progress}%;"></div>
                        </div>
                        <span class="progress-text">${progress}%</span>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function renderContracts(contracts) {
    const container = document.getElementById('dashboardContractsList');
    if (!container) return;

    if (!Array.isArray(contracts) || contracts.length === 0) {
        container.innerHTML = '<div class="empty-state">No contracts found.</div>';
        return;
    }

    container.innerHTML = contracts.map(c => {
        const mapped = mapContractStatus(c.status);
        const startText = c.start_date ? `Started: ${formatDateShort(c.start_date)}` : '';

        return `
            <div class="contract-item">
                <div class="contract-info">
                    <h4>${escapeHtml(c.title || '')}</h4>
                    <p>${escapeHtml(c.customer || '')}</p>
                    ${startText ? `<span class="contract-date">${escapeHtml(startText)}</span>` : ''}
                </div>
                <div class="contract-details">
                    <div class="contract-status ${mapped.className}">${escapeHtml(mapped.label)}</div>
                    <div class="contract-value">LKR ${formatCurrency(Number(c.total_budget || 0) / 1000)}K</div>
                </div>
            </div>
        `;
    }).join('');
}

function renderPayments(payments) {
    const container = document.getElementById('dashboardPaymentsList');
    if (!container) return;

    if (!Array.isArray(payments) || payments.length === 0) {
        container.innerHTML = '<div class="empty-state">No payments found.</div>';
        return;
    }

    container.innerHTML = payments.map(p => {
        const amountClass = p.status === 'completed' ? 'positive' : (p.status === 'failed' || p.status === 'refunded' ? 'negative' : '');
        const sign = p.status === 'completed' ? '+' : '';

        return `
            <div class="payment-item">
                <div class="payment-info">
                    <h4>${escapeHtml(p.title || 'Payment')}</h4>
                    <p>${escapeHtml(p.subtitle || '')}</p>
                </div>
                <div class="payment-amount ${amountClass}">${sign}LKR ${formatCurrency(Number(p.amount || 0) / 1000)}K</div>
                <div class="payment-date">${escapeHtml(p.date || '')}</div>
            </div>
        `;
    }).join('');
}

function renderIncomeChart(incomeChart) {
    const barsContainer = document.getElementById('incomeChart');
    const labelsContainer = document.getElementById('incomeChartLabels');
    if (!barsContainer || !labelsContainer || !incomeChart) return;

    const labels = Array.isArray(incomeChart.labels) ? incomeChart.labels : [];
    const values = Array.isArray(incomeChart.values) ? incomeChart.values : [];
    const maxValue = Math.max(0, ...values.map(v => Number(v || 0)));

    barsContainer.innerHTML = values.map(v => {
        const val = Number(v || 0);
        const height = maxValue > 0 ? (val / maxValue) * 100 : 0;
        return `<div class="chart-bar" data-value="${formatCurrency(val / 1000)}K" style="height: ${height}%;"></div>`;
    }).join('');

    labelsContainer.innerHTML = labels.map(l => `<div class="chart-label">${escapeHtml(l)}</div>`).join('');

    const totalEl = document.getElementById('incomeSummaryTotal');
    const avgEl = document.getElementById('incomeSummaryAvg');
    const growthEl = document.getElementById('incomeSummaryGrowth');

    const summary = incomeChart.summary || {};
    if (totalEl) totalEl.textContent = `LKR ${formatCurrency(Number(summary.total || 0) / 1000)}K`;
    if (avgEl) avgEl.textContent = `LKR ${formatCurrency(Number(summary.avg || 0) / 1000)}K`;

    if (growthEl) {
        if (summary.growth_pct === null || summary.growth_pct === undefined) {
            growthEl.textContent = '—';
        } else {
            const pct = Number(summary.growth_pct || 0);
            const sign = pct >= 0 ? '+' : '';
            growthEl.textContent = `${sign}${pct.toFixed(0)}%`;
        }
    }

    animateChartBars();
}

function renderWorkforce(workforce) {
    if (!Array.isArray(workforce)) workforce = [];

    const grid = document.getElementById('workforceGrid');
    if (!grid) return;

    const categoryFilter = document.getElementById('workforceCategoryFilter');
    if (categoryFilter) {
        categoryFilter.innerHTML = '<option value="">All Categories</option>';
    }

    if (workforce.length === 0) {
        grid.innerHTML = '<div class="empty-state">No workforce categories found.</div>';
        return;
    }

    // Stable sort by specialty name
    const sorted = [...workforce].sort((a, b) => String(a.specialty || '').localeCompare(String(b.specialty || ''), undefined, { sensitivity: 'base' }));

    const cards = sorted.map(row => {
        const specialtyName = String(row.specialty || '');
        const key = workforceSlug(specialtyName);
        const total = Number(row.total || 0);
        const active = Number(row.active || 0);
        const ratio = total > 0 ? Math.round((active / total) * 100) : 0;
        const avgRating = Number(row.avg_rating || 0);

        const badge = ratio >= 60
            ? { cls: 'available', text: 'Available' }
            : (ratio >= 30 ? { cls: 'limited', text: 'Limited' } : { cls: 'offline', text: 'Offline' });

        const icon = getWorkforceIconClass(specialtyName);
        const href = `/2nd-Year-Group-Project/FixLanka/company-workforce#${encodeURIComponent(key)}`;

        if (categoryFilter && key) {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = specialtyName;
            categoryFilter.appendChild(opt);
        }

        return `
            <div class="workforce-item" data-category="${escapeHtml(key)}">
                <div class="workforce-header">
                    <div class="workforce-icon">
                        <i class="fas ${escapeHtml(icon)}"></i>
                    </div>
                    <div class="workforce-title">
                        <h4>${escapeHtml(specialtyName)}</h4>
                        <div class="workforce-availability">
                            <span class="available-count">${active}</span>
                            <span class="total-count">/ ${total} Total</span>
                        </div>
                    </div>
                </div>

                <div class="workforce-progress">
                    <div class="progress-label">
                        <span>Availability Ratio</span>
                        <span class="progress-percentage">${ratio}%</span>
                    </div>
                    <div class="progress-bar" role="progressbar" aria-valuenow="${ratio}" aria-valuemin="0" aria-valuemax="100" aria-label="${escapeHtml(specialtyName)} availability ratio">
                        <div class="progress-fill" style="width: ${ratio}%"></div>
                    </div>
                </div>

                <div class="workforce-details">
                    <div class="detail-item">
                        <i class="fas fa-star" aria-hidden="true"></i>
                        <span>${avgRating.toFixed(1)} Rating</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <span>Verified</span>
                    </div>
                </div>

                <div class="availability-badge ${badge.cls}">${badge.text}</div>

                <div class="workforce-actions" role="group" aria-label="${escapeHtml(specialtyName)} workforce actions">
                    <button class="action-btn primary" type="button" onclick="window.location.href='${href}'">
                        <i class="fas fa-eye icon-left" aria-hidden="true"></i>
                        <span class="btn-text">View All</span>
                    </button>
                </div>
            </div>
        `;
    }).join('');

    grid.innerHTML = cards;
}

function renderFeedback(feedback) {
    const container = document.getElementById('dashboardFeedbackList');
    if (!container) return;

    if (!Array.isArray(feedback) || feedback.length === 0) {
        container.innerHTML = '<div class="empty-state">No feedback yet.</div>';
        return;
    }

    container.innerHTML = feedback.map(f => {
        const rating = clampNumber(Number(f.rating || 0), 0, 5);
        return `
            <div class="feedback-item">
                <div class="feedback-rating">${renderStarsIcons(rating)}</div>
                <p>${escapeHtml(f.comments || '')}</p>
                <span class="feedback-customer">- ${escapeHtml(f.customer || '')}</span>
            </div>
        `;
    }).join('');
}

function renderSupportTickets(tickets) {
    const container = document.getElementById('dashboardSupportTickets');
    if (!container) return;

    if (!Array.isArray(tickets) || tickets.length === 0) {
        container.innerHTML = '<div class="empty-state">No support tickets.</div>';
        return;
    }

    container.innerHTML = tickets.map(t => {
        const priority = (t.priority || 'medium').toLowerCase();
        const mappedPriority = priority === 'urgent' ? 'high' : (priority === 'low' ? 'low' : (priority === 'high' ? 'high' : 'medium'));
        return `
            <div class="ticket-item ${mappedPriority}">
                <div class="ticket-priority">${escapeHtml(priority)}</div>
                <div class="ticket-info">
                    <h4>${escapeHtml(t.title || '')}</h4>
                    <p>${escapeHtml(t.ticket_number || '')}</p>
                </div>
                <div class="ticket-status">${escapeHtml(t.status || '')}</div>
            </div>
        `;
    }).join('');
}

function mapProjectStatus(status) {
    switch ((status || '').toLowerCase()) {
        case 'in_progress':
            return { className: 'in-progress', label: 'In Progress' };
        case 'completed':
            return { className: 'completed', label: 'Completed' };
        case 'on_hold':
            return { className: 'delayed', label: 'On Hold' };
        case 'planned':
        default:
            return { className: 'pending', label: 'Pending' };
    }
}

function mapContractStatus(status) {
    const s = (status || '').toLowerCase();
    if (s === 'active' || s === 'in_progress') return { className: 'active', label: 'Active' };
    if (s === 'completed') return { className: 'completed', label: 'Completed' };
    if (s === 'pending_signature' || s === 'milestone_pending' || s === 'draft') return { className: 'pending', label: 'Pending' };
    return { className: 'pending', label: capitalizeFirst(s || 'pending') };
}

function clampNumber(n, min, max) {
    n = Number(n);
    if (Number.isNaN(n)) return min;
    return Math.min(max, Math.max(min, n));
}

function formatDateShort(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (Number.isNaN(d.getTime())) return String(dateStr);
    return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function workforceSlug(value) {
    return String(value || '')
        .trim()
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9\-]/g, '')
        .replace(/\-+/g, '-')
        .replace(/^\-+|\-+$/g, '')
        || 'category';
}

function getWorkforceIconClass(specialty) {
    const s = String(specialty || '').toLowerCase();
    if (s.includes('carp')) return 'fa-hammer';
    if (s.includes('elect')) return 'fa-bolt';
    if (s.includes('plumb')) return 'fa-wrench';
    if (s.includes('paint')) return 'fa-paint-brush';
    if (s.includes('mason') || s.includes('brick')) return 'fa-trowel';
    if (s.includes('tile')) return 'fa-border-all';
    if (s.includes('ac') || s.includes('hvac')) return 'fa-fan';
    return 'fa-user-cog';
}

function getEarningsBarHeight(series, amount) {
    if (!Array.isArray(series) || series.length === 0) return 0;
    const max = Math.max(0, ...series.map(s => Number(s.amount || 0)));
    const val = Number(amount || 0);
    return max > 0 ? (val / max) * 100 : 0;
}

function renderStarsIcons(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        html += i <= rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
    }
    return html;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

// Generate static action buttons (no functionality)
function getStaticActionButtons(status, type = 'direct') {
    if (type === 'direct') {
        // Direct request actions
        switch (status) {
            case 'pending':
                return `
                    <button class="action-btn accept">Accept</button>
                    <button class="action-btn decline">Decline</button>
                `;
            case 'accepted':
                return `
                    <button class="action-btn assign">Assign Worker</button>
                `;
            case 'completed':
                return `
                    <button class="action-btn view">View Details</button>
                `;
            default:
                return '';
        }
    } else {
        // Public request actions
        switch (status) {
            case 'bidding':
                return `
                    <button class="action-btn bid">Place Bid</button>
                    <button class="action-btn view">View Details</button>
                `;
            case 'awarded':
                return `
                    <button class="action-btn start">Start Project</button>
                `;
            case 'completed':
                return `
                    <button class="action-btn view">View Details</button>
                `;
            default:
                return '';
        }
    }
}

// Utility function
function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Initialize sidebar toggle functionality
 * Uses the checkbox (#sidebar-toggle) and label system for sidebar collapse/expand
 */
function initializeSidebarToggle() {
    // Wait for components to load
    setTimeout(() => {
        // Use the label with class .sidebar-toggle (which controls the checkbox)
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarCheckbox = document.getElementById('sidebar-toggle');

        if (sidebarToggle && sidebarCheckbox) {

            // Listen for checkbox changes
            sidebarCheckbox.addEventListener('change', function () {

                const sidebar = document.querySelector('.sidebar');
                const body = document.body;

                if (sidebar) {
                    // Toggle sidebar collapsed class based on checkbox state
                    if (this.checked) {
                        sidebar.classList.add('collapsed');
                        body.classList.add('sidebar-collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                        body.classList.remove('sidebar-collapsed');
                    }

                    const isCollapsed = sidebar.classList.contains('collapsed');

                }
            });
        } else {
            console.warn('Sidebar toggle elements not found, retrying...');
            // Try again after a delay
            setTimeout(initializeSidebarToggle, 1000);
        }
    }, 500); // Wait for components to load
}

// Note: Sidebar toggle functionality restored
// This version provides static UI display with responsive sidebar

// Global function to manually test sidebar toggle
window.testSidebarToggle = function () {
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;

    if (sidebar) {
        sidebar.classList.toggle('collapsed');
        body.classList.toggle('sidebar-collapsed');

        const isCollapsed = sidebar.classList.contains('collapsed');
        const bodyHasClass = body.classList.contains('sidebar-collapsed');





        return true;
    } else {
        console.error('Sidebar not found for manual toggle');
        return false;
    }
};

// Initialize Income Chart
function initializeIncomeChart() {
    const periodSelector = document.querySelector('.period-selector');
    if (periodSelector) {
        periodSelector.addEventListener('change', updateIncomeChart);
    }

    // Add animation on load
    animateChartBars();
}

// Update income chart based on period selection
function updateIncomeChart() {
    const periodText = document.querySelector('.period-selector')?.value || 'Last 7 Days';
    const period = periodText === 'Last 30 Days' ? '30d' : (periodText === 'Last 3 Months' ? '3m' : '7d');
    loadDashboardData(period);
}

// Animate chart bars on load
function animateChartBars() {
    const chartBars = document.querySelectorAll('.chart-bar');
    chartBars.forEach((bar, index) => {
        const originalHeight = bar.style.height;
        bar.style.height = '0%';
        bar.style.transition = 'height 0.6s ease-out';

        setTimeout(() => {
            bar.style.height = originalHeight;
        }, index * 100 + 300);
    });
}

function getInitialsFromName(fullName) {
    const parts = String(fullName || '').trim().split(/\s+/).filter(Boolean);
    if (parts.length === 0) return 'U';
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
}