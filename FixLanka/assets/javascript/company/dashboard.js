// FixLanka Dashboard JavaScript - Enhanced Version with Full Calendar Functionality

// Global calendar state
const calendarState = {
    currentDate: new Date(),
    selectedDate: null,
    events: [],
    viewMode: 'month'
};

document.addEventListener('DOMContentLoaded', function() {
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
        
        card.addEventListener('click', function() {
            const cardContent = this.querySelector('.kpi-content h3').textContent;
            
            switch(cardContent) {
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
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
}

function initializePanelNavigation() {
    // Repair Requests Panel
    const repairRequestsViewAll = document.querySelector('.requests-panel .view-all-btn');
    if (repairRequestsViewAll) {
        repairRequestsViewAll.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPage('repair-requests.php', 'Repair Requests');
        });
    }
    
    // Projects Panel
    const projectsNewBtn = document.querySelector('.projects-panel .view-all-btn');
    if (projectsNewBtn) {
        projectsNewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPage('projects.php', 'Projects');
        });
    }
    
    // Contracts Panel
    const contractsViewAll = document.querySelector('.contracts-panel .view-all-btn');
    if (contractsViewAll) {
        contractsViewAll.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPage('contracts.php', 'Contracts');
        });
    }
    
    // Payments Panel
    const paymentsViewAll = document.querySelector('.payments-list-section .view-all-btn');
    if (paymentsViewAll) {
        paymentsViewAll.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToPage('payments.php', 'Payments');
        });
    }
}

function initializeActionButtonsNavigation() {
    // Make request cards clickable
    document.addEventListener('click', function(e) {
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
    document.addEventListener('click', function(e) {
        const actionBtn = e.target.closest('.action-btn');
        if (!actionBtn) return;
        
        e.stopPropagation();
        
        const buttonText = actionBtn.textContent.trim();
        const requestCard = actionBtn.closest('.request-card');
        
        switch(buttonText) {
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
            button.addEventListener('click', function(e) {
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

function showEarningsModal() {
    const modal = createModal('earningsModal');
    
    // Calculate sample earnings data
    const monthlyEarnings = [
        { month: 'Jan', amount: 380000, projects: 12 },
        { month: 'Feb', amount: 420000, projects: 15 },
        { month: 'Mar', amount: 390000, projects: 11 },
        { month: 'Apr', amount: 450000, projects: 16 },
        { month: 'May', amount: 480000, projects: 18 },
        { month: 'Jun', amount: 520000, projects: 20 }
    ];
    
    const totalEarnings = monthlyEarnings.reduce((sum, month) => sum + month.amount, 0);
    const avgMonthly = totalEarnings / monthlyEarnings.length;
    const totalProjects = monthlyEarnings.reduce((sum, month) => sum + month.projects, 0);
    
    modal.innerHTML = `
        <div class="modal-content earnings-modal">
            <div class="modal-header">
                <h3><i class="fas fa-chart-line"></i> Earnings Overview</h3>
                <button class="modal-close" onclick="closeModal('earningsModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="earnings-summary">
                    <div class="summary-card">
                        <h4>Total Earnings (6 months)</h4>
                        <p class="amount">LKR ${formatCurrency(totalEarnings)}</p>
                    </div>
                    <div class="summary-card">
                        <h4>Average Monthly</h4>
                        <p class="amount">LKR ${formatCurrency(avgMonthly)}</p>
                    </div>
                    <div class="summary-card">
                        <h4>Total Projects</h4>
                        <p class="amount">${totalProjects}</p>
                    </div>
                </div>
                
                <div class="earnings-chart">
                    <h4>Monthly Earnings Trend</h4>
                    <div class="chart-container">
                        ${monthlyEarnings.map(month => `
                            <div class="chart-column">
                                <div class="bar" style="height: ${(month.amount / 520000) * 100}%"></div>
                                <span class="month-label">${month.month}</span>
                                <span class="amount-label">LKR ${formatCurrency(month.amount / 1000)}K</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="earnings-breakdown">
                    <h4>Earnings Breakdown</h4>
                    <div class="breakdown-item">
                        <span>Project Payments</span>
                        <span>LKR ${formatCurrency(totalEarnings * 0.7)}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Service Fees</span>
                        <span>LKR ${formatCurrency(totalEarnings * 0.2)}</span>
                    </div>
                    <div class="breakdown-item">
                        <span>Consultations</span>
                        <span>LKR ${formatCurrency(totalEarnings * 0.1)}</span>
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
    
    const ratingData = [
        { category: 'Plumbing', rating: 4.9, reviews: 45, trend: '+0.2' },
        { category: 'Electrical', rating: 4.8, reviews: 38, trend: '+0.1' },
        { category: 'Carpentry', rating: 4.7, reviews: 32, trend: '+0.3' },
        { category: 'Painting', rating: 4.6, reviews: 28, trend: '+0.1' }
    ];
    
    const avgRating = ratingData.reduce((sum, cat) => sum + cat.rating, 0) / ratingData.length;
    const totalReviews = ratingData.reduce((sum, cat) => sum + cat.reviews, 0);
    
    modal.innerHTML = `
        <div class="modal-content rating-modal">
            <div class="modal-header">
                <h3><i class="fas fa-star"></i> Customer Ratings</h3>
                <button class="modal-close" onclick="closeModal('ratingModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="rating-overview">
                    <div class="overall-rating">
                        <div class="rating-number">${avgRating.toFixed(1)}</div>
                        <div class="rating-stars">
                            ${generateStars(avgRating)}
                        </div>
                        <div class="rating-text">Based on ${totalReviews} reviews</div>
                    </div>
                </div>
                
                <div class="category-ratings">
                    <h4>Ratings by Category</h4>
                    ${ratingData.map(cat => `
                        <div class="category-rating">
                            <div class="category-info">
                                <span class="category-name">${cat.category}</span>
                                <span class="category-trend ${cat.trend.startsWith('+') ? 'positive' : 'negative'}">
                                    ${cat.trend}
                                </span>
                            </div>
                            <div class="rating-display">
                                <span class="rating-number">${cat.rating}</span>
                                <div class="rating-stars">${generateStars(cat.rating)}</div>
                                <span class="review-count">(${cat.reviews})</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
                
                <div class="recent-reviews">
                    <h4>Recent Reviews</h4>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">Sarah Johnson</span>
                            <div class="review-rating">${generateStars(5)}</div>
                        </div>
                        <p>"Excellent plumbing work! Fixed our kitchen sink perfectly."</p>
                        <span class="review-date">2 days ago</span>
                    </div>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="reviewer-name">Mike Wilson</span>
                            <div class="review-rating">${generateStars(4)}</div>
                        </div>
                        <p>"Good carpentry service, professional team."</p>
                        <span class="review-date">5 days ago</span>
                    </div>
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
    
    return '★'.repeat(fullStars) + 
           (hasHalfStar ? '☆' : '') + 
           '☆'.repeat(emptyStars);
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
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
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
    loadStaticRequests();
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
    
    // Load sample events
    loadSampleEvents();
}

function loadSampleEvents() {
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    calendarState.events = [
        {
            id: 'evt-1',
            title: 'Team Meeting',
            date: new Date(currentYear, currentMonth, 27),
            time: '10:00 AM',
            type: 'meeting',
            description: 'Weekly team coordination meeting',
            participants: ['John Smith', 'Sarah Johnson', 'Mike Wilson']
        },
        {
            id: 'evt-2',
            title: 'Project Deadline',
            date: new Date(currentYear, currentMonth, 30),
            time: '5:00 PM',
            type: 'deadline',
            description: 'ABC Corporation office renovation completion',
            priority: 'high'
        },
        {
            id: 'evt-3',
            title: 'Client Consultation',
            date: new Date(currentYear, currentMonth, 25),
            time: '2:00 PM',
            type: 'consultation',
            description: 'Initial consultation for kitchen renovation',
            client: 'Emma Davis'
        },
        {
            id: 'evt-4',
            title: 'Equipment Maintenance',
            date: new Date(currentYear, currentMonth, 28),
            time: '9:00 AM',
            type: 'maintenance',
            description: 'Monthly equipment maintenance check',
            location: 'Workshop'
        },
        {
            id: 'evt-5',
            title: 'Training Session',
            date: new Date(currentYear, currentMonth, 29),
            time: '11:00 AM',
            type: 'training',
            description: 'Safety protocols training for new team members',
            duration: '2 hours'
        }
    ];
    
    saveCalendarEvents();
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
            loadSampleEvents();
        }
    } else {
        loadSampleEvents();
    }
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
    document.addEventListener('click', function(e) {
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
    
    const eventsHTML = events.map(event => `
        <div class="event-item ${event.type}">
            <div class="event-header">
                <h4>${event.title}</h4>
                <span class="event-time">${event.time}</span>
            </div>
            <p class="event-description">${event.description}</p>
            ${event.client ? `<div class="event-meta">Client: ${event.client}</div>` : ''}
            ${event.location ? `<div class="event-meta">Location: ${event.location}</div>` : ''}
            ${event.participants ? `<div class="event-meta">Participants: ${event.participants.join(', ')}</div>` : ''}
            <div class="event-actions">
                <button onclick="editEvent('${event.id}')" class="action-btn secondary">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deleteEvent('${event.id}')" class="action-btn danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
    
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
                transition: opacity 0.3s ease;
            }
            .modal-overlay.active {
                opacity: 1;
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
    }
}

function deleteEvent(eventId) {
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
    const upcomingEvents = calendarState.events
        .filter(event => event.date >= today)
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
    document.addEventListener('keydown', function(e) {
        if (e.target.closest('.modal-overlay')) return; // Don't interfere with modals
        
        const selectedDate = calendarState.selectedDate || new Date();
        
        switch(e.key) {
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
    return calendarState.events.filter(event => 
        event.date.toDateString() === date.toDateString()
    );
}

function formatDateString(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ✅ FIX: Same local-safe version for input fields
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
    
    // Load Direct Requests by default
    loadRequestsByType('direct');
}

// Initialize tab switching functionality
function initializeTabSwitching() {
    const tabButtons = document.querySelectorAll('.tab-button');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
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
function loadRequestsByType(type) {
    const requestsList = document.getElementById('requestsList');
    if (!requestsList) return;
    
    let requestsData;
    
    if (type === 'direct') {
        // Direct Requests - Customer-to-company direct requests
        requestsData = [
            {
                customer: 'John Smith',
                avatar: 'https://via.placeholder.com/48x48/0abab5/ffffff?text=JS',
                category: 'Plumbing',
                title: 'Kitchen Sink Repair',
                description: 'Leaking kitchen sink needs immediate attention',
                date: 'Aug 25',
                status: 'pending',
                type: 'direct'
            },
            {
                customer: 'Sarah Johnson',
                avatar: 'https://via.placeholder.com/48x48/f59e0b/ffffff?text=SJ',
                category: 'Electrical',
                title: 'Outlet Installation',
                description: 'Need 3 new outlets installed in living room',
                date: 'Aug 24',
                status: 'accepted',
                type: 'direct'
            },
            {
                customer: 'Mike Wilson',
                avatar: 'https://via.placeholder.com/48x48/10b981/ffffff?text=MW',
                category: 'Carpentry',
                title: 'Cabinet Door Fix',
                description: 'Kitchen cabinet door is loose and needs repair',
                date: 'Aug 23',
                status: 'completed',
                type: 'direct'
            },
            {
                customer: 'Emma Davis',
                avatar: 'https://via.placeholder.com/48x48/ef4444/ffffff?text=ED',
                category: 'Roofing',
                title: 'Roof Leak Repair',
                description: 'Urgent roof leak in bedroom ceiling',
                date: 'Aug 22',
                status: 'pending',
                type: 'direct'
            }
        ];
    } else {
        // Public Requests - Open market requests from the platform
        requestsData = [
            {
                customer: 'ABC Corporation',
                avatar: 'https://via.placeholder.com/48x48/3b82f6/ffffff?text=ABC',
                category: 'Construction',
                title: 'Office Building Renovation',
                description: 'Complete renovation of 3rd floor office space',
                date: 'Aug 26',
                status: 'bidding',
                type: 'public',
                bidAmount: 'LKR 500K',
                deadline: 'Sep 15'
            },
            {
                customer: 'City Council',
                avatar: 'https://via.placeholder.com/48x48/8b5cf6/ffffff?text=CC',
                category: 'Public Works',
                title: 'Park Maintenance',
                description: 'Monthly maintenance for Central Park facilities',
                date: 'Aug 25',
                status: 'bidding',
                type: 'public',
                bidAmount: 'LKR 150K',
                deadline: 'Sep 10'
            },
            {
                customer: 'Green Valley Resort',
                avatar: 'https://via.placeholder.com/48x48/059669/ffffff?text=GV',
                category: 'Hospitality',
                title: 'Pool Area Repair',
                description: 'Swimming pool deck and filtration system repair',
                date: 'Aug 24',
                status: 'awarded',
                type: 'public',
                bidAmount: 'LKR 300K',
                deadline: 'Aug 30'
            },
            {
                customer: 'Tech Startup Hub',
                avatar: 'https://via.placeholder.com/48x48/f97316/ffffff?text=TS',
                category: 'Commercial',
                title: 'HVAC System Installation',
                description: 'Install modern HVAC system in new office space',
                date: 'Aug 23',
                status: 'bidding',
                type: 'public',
                bidAmount: 'LKR 750K',
                deadline: 'Sep 20'
            }
        ];
    }
    
    const requestsHTML = requestsData.map(request => `
        <div class="request-card ${type}-request" data-status="${request.status}">
            <div class="request-info">
                <img src="${request.avatar}" alt="${request.customer}" class="customer-avatar">
                <div class="request-details">
                    <h4>
                        <span class="title-text">${request.title}</span>
                        <span class="request-status ${request.status}">${capitalizeFirst(request.status)}</span>
                    </h4>
                    <div class="request-meta">
                        <span class="request-category">${request.category}</span>
                        <span>${request.customer}</span>
                        <span>${request.date}</span>
                        ${request.deadline ? `<span class="deadline">Due: ${request.deadline}</span>` : ''}
                    </div>
                    <p class="request-description">${request.description}</p>
                    ${request.bidAmount ? `<div class="bid-amount">Budget: ${request.bidAmount}</div>` : ''}
                </div>
            </div>
            <div class="request-actions">
                ${getStaticActionButtons(request.status, type)}
            </div>
        </div>
    `).join('');
    
    // Add fade animation
    requestsList.style.opacity = '0';
    setTimeout(() => {
        requestsList.innerHTML = requestsHTML;
        requestsList.style.opacity = '1';
    }, 150);
}

// Generate static action buttons (no functionality)
function getStaticActionButtons(status, type = 'direct') {
    if (type === 'direct') {
        // Direct request actions
        switch(status) {
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
        switch(status) {
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

// Initialize sidebar toggle functionality
function initializeSidebarToggle() {
    // Wait a bit for components to load
    setTimeout(() => {
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        if (sidebarToggle) {
            console.log('Sidebar toggle button found');
            
            sidebarToggle.addEventListener('click', function() {
                console.log('Toggle button clicked');
                
                const sidebar = document.querySelector('.sidebar');
                const body = document.body;
                
                if (sidebar) {
                    console.log('Sidebar found, toggling...');
                    
                    // Toggle sidebar collapsed class
                    sidebar.classList.toggle('collapsed');
                    
                    // Toggle body class for content responsiveness  
                    body.classList.toggle('sidebar-collapsed');
                    
                    const isCollapsed = sidebar.classList.contains('collapsed');
                    const bodyHasClass = body.classList.contains('sidebar-collapsed');
                    
                    console.log('Sidebar collapsed:', isCollapsed);
                    console.log('Body has sidebar-collapsed class:', bodyHasClass);
                    console.log('Main content should adjust margin to:', isCollapsed ? '70px' : '280px');
                } else {
                    console.error('Sidebar element not found');
                }
            });
        } else {
            console.error('Sidebar toggle button not found');
            // Try again in a bit
            setTimeout(initializeSidebarToggle, 1000);
        }
    }, 500); // Wait for components to load
}

// Note: Sidebar toggle functionality restored
// This version provides static UI display with responsive sidebar

// Global function to manually test sidebar toggle
window.testSidebarToggle = function() {
    const sidebar = document.querySelector('.sidebar');
    const body = document.body;
    
    if (sidebar) {
        sidebar.classList.toggle('collapsed');
        body.classList.toggle('sidebar-collapsed');
        
        const isCollapsed = sidebar.classList.contains('collapsed');
        const bodyHasClass = body.classList.contains('sidebar-collapsed');
        
        console.log('Manual toggle results:');
        console.log('- Sidebar collapsed:', isCollapsed);
        console.log('- Body has sidebar-collapsed class:', bodyHasClass);
        console.log('- Expected main content margin:', isCollapsed ? '70px' : '280px');
        
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
    const period = document.querySelector('.period-selector').value;
    const chartBars = document.querySelectorAll('.chart-bar');
    const chartLabels = document.querySelector('.chart-labels');
    const summaryValues = document.querySelectorAll('.summary-value');
    
    let data, labels, summary;
    
    switch(period) {
        case 'Last 7 Days':
            data = [45, 62, 38, 75, 52, 68, 41];
            labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            summary = ['LKR 381K', 'LKR 54K', '+18%'];
            break;
        case 'Last 30 Days':
            data = [120, 95, 160, 85, 140, 110, 130];
            labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7'];
            summary = ['LKR 840K', 'LKR 28K', '+12%'];
            break;
        case 'Last 3 Months':
            data = [450, 380, 520, 340, 460, 390, 480];
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
            summary = ['LKR 3.02M', 'LKR 101K', '+25%'];
            break;
        default:
            return;
    }
    
    // Update chart bars
    const maxValue = Math.max(...data);
    chartBars.forEach((bar, index) => {
        if (data[index] !== undefined) {
            const height = (data[index] / maxValue) * 100;
            bar.style.height = height + '%';
            bar.setAttribute('data-value', data[index] + 'K');
        }
    });
    
    // Update labels
    const labelElements = chartLabels.querySelectorAll('.chart-label');
    labelElements.forEach((label, index) => {
        if (labels[index]) {
            label.textContent = labels[index];
        }
    });
    
    // Update summary values
    summaryValues.forEach((value, index) => {
        if (summary[index]) {
            value.textContent = summary[index];
        }
    });
    
    // Re-animate bars
    animateChartBars();
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