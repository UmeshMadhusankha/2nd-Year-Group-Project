// FixLanka Dashboard JavaScript - UI Only Version

document.addEventListener('DOMContentLoaded', function() {
    // Basic UI initialization only - no interactive functions
    initializeStaticUI();
    
    // Initialize tab switching functionality
    initializeTabSwitching();
    
    // Initialize income chart
    initializeIncomeChart();
    
    // Initialize sidebar toggle with multiple attempts
    initializeSidebarToggle();
    
    // Also try to initialize after a longer delay
    setTimeout(initializeSidebarToggle, 2000);
});

// Initialize static UI elements
function initializeStaticUI() {
    // Set current month display
    const currentMonthElement = document.getElementById('currentMonth');
    if (currentMonthElement) {
        const currentDate = new Date();
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        currentMonthElement.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
    }
    
    // Generate basic calendar dates (current month only)
    generateStaticCalendar();
    
    // Load static request data
    loadStaticRequests();
}

// Generate static calendar without interactions
function generateStaticCalendar() {
    const calendarDates = document.getElementById('calendarDates');
    if (!calendarDates) return;
    
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const currentYear = currentDate.getFullYear();
    const today = currentDate.getDate();
    
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    let datesHTML = '';
    
    // Previous month's trailing dates
    const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate();
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = prevMonthDays - i;
        datesHTML += `<div class="calendar-date other-month">${day}</div>`;
    }
    
    // Current month dates
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today;
        
        // Sample events for display only
        const hasEvent = [25, 27, 30].includes(day);
        
        let classes = 'calendar-date';
        if (isToday) classes += ' today';
        if (hasEvent) classes += ' has-event';
        
        datesHTML += `<div class="${classes}">${day}</div>`;
    }
    
    // Next month's leading dates
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);
    for (let day = 1; day <= remainingCells; day++) {
        datesHTML += `<div class="calendar-date other-month">${day}</div>`;
    }
    
    calendarDates.innerHTML = datesHTML;
}

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
