// ===== Configuration =====
const REPAIRER_ID = window.CURRENT_REPAIRER_ID || 0;
const BASE_API = '/2nd-Year-Group-Project/FixLanka/api';

// Initialize welcome page functionality
document.addEventListener('DOMContentLoaded', function () {
    initializeWelcomePage();
});

/**
 * Initialize all welcome page functionality
 */
function initializeWelcomePage() {
    updateGreeting();
    loadDashboardStats();
    loadRecentActivity();
    initializeAvailabilityToggle();
    initializeQuickActions();
    initializeAvailabilitySync();
}

/**
 * Fetch real stats from the backend and populate stat cards
 */
async function loadDashboardStats() {
    if (!REPAIRER_ID) return;

    try {
        // Fetch job stats
        const [jobRes, quoteRes] = await Promise.all([
            fetch(`${BASE_API}/repairer-jobs.php?action=stats&repairer_id=${REPAIRER_ID}`),
            fetch(`${BASE_API}/repairer-quotes.php?repairer_id=${REPAIRER_ID}`)
        ]);

        const jobData = await jobRes.json();
        const quoteData = await quoteRes.json();

        if (jobData.success) {
            const activeEl = document.getElementById('welcomeActiveJobs');
            if (activeEl) activeEl.textContent = jobData.active || 0;

            // Total earnings from paid jobs – we'll compute from jobs list if needed
            const earningsEl = document.getElementById('welcomeTotalEarnings');
            if (earningsEl) {
                earningsEl.textContent = 'LKR 0';
            }
        }

        if (quoteData.success) {
            const pendingQuotes = (quoteData.data || []).filter(q => q.status === 'pending').length;
            const pendingEl = document.getElementById('welcomePendingQuotes');
            if (pendingEl) pendingEl.textContent = pendingQuotes;
        }
    } catch (err) {
        console.error('Failed to load dashboard stats:', err);
    }
}

/**
 * Load recent activity (recent jobs) from the backend
 */
async function loadRecentActivity() {
    if (!REPAIRER_ID) {
        renderEmptyActivity();
        return;
    }

    try {
        const res = await fetch(`${BASE_API}/repairer-jobs.php?action=list&repairer_id=${REPAIRER_ID}`);
        const data = await res.json();

        const activityList = document.getElementById('activityList');
        if (!activityList) return;

        if (!data.success || !data.jobs || data.jobs.length === 0) {
            renderEmptyActivity();
            return;
        }

        // Show the 5 most recent jobs as activity
        const recent = data.jobs.slice(0, 5);
        activityList.innerHTML = recent.map(job => {
            const statusColors = {
                active: '#0abab5', in_progress: '#f59e0b', completed: '#10b981',
                cancelled: '#ef4444', paid: '#10b981'
            };
            const uiStatus = job.ui_status || 'active';
            const color = statusColors[uiStatus] || '#0abab5';
            const firstName = job.customer_first_name || '';
            const lastName = job.customer_last_name || '';
            const customerName = (firstName + ' ' + lastName).trim() || 'Customer';
            return `
                <div class="activity-item">
                    <div class="activity-icon" style="background:${color}22;color:${color}">
                        <i class="fas fa-hammer"></i>
                    </div>
                    <div class="activity-content">
                        <p class="activity-text"><strong>${escapeHtml(job.job_title || 'Job')}</strong> – ${escapeHtml(customerName)}</p>
                        <span class="activity-time">${formatActivityDate(job.dateSubmitted)}</span>
                    </div>
                    <span class="activity-badge" style="background:${color}22;color:${color}">${uiStatus}</span>
                </div>`;
        }).join('');
    } catch (err) {
        console.error('Failed to load recent activity:', err);
        renderEmptyActivity();
    }
}

function renderEmptyActivity() {
    const activityList = document.getElementById('activityList');
    if (activityList) {
        activityList.innerHTML = `
            <div style="text-align:center;padding:40px;color:var(--text-secondary)">
                <i class="fas fa-inbox fa-2x"></i>
                <p style="margin-top:12px">No recent activity yet.</p>
            </div>`;
    }
}

function formatActivityDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


/**
 * Update greeting based on time of day
 */
function updateGreeting() {
    const welcomeTitle = document.querySelector('.welcome-title');
    const repairerName = document.querySelector('.repairer-name');

    if (welcomeTitle && repairerName) {
        const hour = new Date().getHours();
        let greeting = 'Welcome back';

        if (hour < 12) {
            greeting = 'Good morning';
        } else if (hour < 17) {
            greeting = 'Good afternoon';
        } else {
            greeting = 'Good evening';
        }

        // Update the greeting while keeping the name
        const name = repairerName.textContent;
        welcomeTitle.innerHTML = `${greeting}, <span class="repairer-name">${name}</span>!`;
    }
}

/**
 * Animate statistics numbers on page load
 */
function startStatsAnimation() {
    const statNumbers = document.querySelectorAll('.stat-number');

    statNumbers.forEach(stat => {
        const finalValue = stat.textContent;

        // Skip if it's not a number (like "LKR 45,200")
        if (finalValue.includes('LKR')) {
            animateCurrency(stat, finalValue);
        } else {
            const number = parseInt(finalValue);
            if (!isNaN(number)) {
                animateNumber(stat, number);
            }
        }
    });
}

/**
 * Animate number counting up
 */
function animateNumber(element, finalNumber) {
    let currentNumber = 0;
    const increment = Math.ceil(finalNumber / 30);

    const counter = setInterval(() => {
        currentNumber += increment;
        if (currentNumber >= finalNumber) {
            currentNumber = finalNumber;
            clearInterval(counter);
        }
        element.textContent = currentNumber;
    }, 50);
}

/**
 * Animate currency counting up
 */
function animateCurrency(element, finalValue) {
    const match = finalValue.match(/LKR ([\d,]+)/);
    if (match) {
        const number = parseInt(match[1].replace(',', ''));
        let currentNumber = 0;
        const increment = Math.ceil(number / 30);

        const counter = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= number) {
                currentNumber = number;
                clearInterval(counter);
            }
            element.textContent = `LKR ${currentNumber.toLocaleString()}`;
        }, 50);
    }
}

/**
 * Update activity times to be relative
 */
function updateActivityTimes() {
    const activityTimes = document.querySelectorAll('.activity-time');

    activityTimes.forEach(timeElement => {
        // This would typically get real timestamps from backend
        // For now, keeping the static text as it's already formatted nicely
    });
}

/**
 * Initialize availability status toggle
 */
function initializeAvailabilityToggle() {
    const availabilityButton = document.querySelector('.btn-primary');
    const statusElement = document.querySelector('.stat-status');
    const statusCard = document.querySelector('.availability-status');

    if (availabilityButton && statusElement) {
        availabilityButton.addEventListener('click', function (e) {
            e.preventDefault();
            toggleAvailabilityStatus(statusElement, statusCard, availabilityButton);
        });
    }
}

/**
 * Toggle availability status
 */
function toggleAvailabilityStatus(statusElement, statusCard, button) {
    const isAvailable = statusElement.classList.contains('available');
    const newStatus = isAvailable ? 'unavailable' : 'available';

    // Update localStorage for cross-page synchronization
    localStorage.setItem('fixlanka_availability_status', newStatus);

    // Update the display
    updateAvailabilityDisplay(newStatus);

    // Add visual feedback
    button.style.transform = 'scale(0.95)';
    setTimeout(() => {
        button.style.transform = '';
    }, 150);

    // Show success message (optional)
    showStatusMessage(isAvailable ? 'Status set to unavailable' : 'Status set to available');

    // Dispatch event for other components
    const availabilityEvent = new CustomEvent('availabilityChanged', {
        detail: {
            status: newStatus,
            timestamp: new Date().toISOString()
        }
    });

    window.dispatchEvent(availabilityEvent);
}

/**
 * Show status message (simple implementation)
 */
function showStatusMessage(message) {
    // Create a simple toast message
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: var(--success-color);
        color: var(--text-white);
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        transform: translateX(100%);
    `;

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);

    // Remove after 3 seconds
    setTimeout(() => {
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

/**
 * Initialize quick actions functionality
 */
function initializeQuickActions() {
    const actionCards = document.querySelectorAll('.action-card');

    actionCards.forEach(card => {
        card.addEventListener('click', function (e) {
            // Add click effect
            this.style.transform = 'translateY(-2px) scale(1.02)';

            setTimeout(() => {
                this.style.transform = '';
            }, 150);

            // Get the href and navigate after animation
            const href = this.getAttribute('href');
            if (href && !href.startsWith('#')) {
                setTimeout(() => {
                    window.location.href = href;
                }, 100);
                e.preventDefault();
            }
        });

        // Add ripple effect on click
        card.addEventListener('mousedown', function (e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('div');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(10, 186, 181, 0.3);
                width: 20px;
                height: 20px;
                left: ${e.clientX - rect.left - 10}px;
                top: ${e.clientY - rect.top - 10}px;
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1;
            `;

            this.style.position = 'relative';
            this.appendChild(ripple);

            setTimeout(() => {
                if (ripple.parentNode) {
                    ripple.parentNode.removeChild(ripple);
                }
            }, 600);
        });
    });
}

/**
 * Add CSS animation for ripple effect
 */
function addRippleAnimation() {
    if (!document.getElementById('ripple-animation')) {
        const style = document.createElement('style');
        style.id = 'ripple-animation';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
}

// Add ripple animation on page load
document.addEventListener('DOMContentLoaded', addRippleAnimation);

/**
 * Add hover effects to stat cards
 */
document.addEventListener('DOMContentLoaded', function () {
    const statCards = document.querySelectorAll('.stat-card');

    statCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-4px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});

/**
 * Initialize availability synchronization with other pages
 */
function initializeAvailabilitySync() {
    // Load saved availability status on page load
    loadSavedAvailabilityStatus();

    // Listen for availability changes from other pages
    window.addEventListener('availabilityChanged', function (e) {
        const newStatus = e.detail.status;
        updateAvailabilityDisplay(newStatus);
    });

    // Listen for storage changes (when other tabs update availability)
    window.addEventListener('storage', function (e) {
        if (e.key === 'fixlanka_availability_status') {
            updateAvailabilityDisplay(e.newValue);
        }
    });
}

/**
 * Load saved availability status from localStorage
 */
function loadSavedAvailabilityStatus() {
    const savedStatus = localStorage.getItem('fixlanka_availability_status');
    if (savedStatus) {
        updateAvailabilityDisplay(savedStatus);
    }
}

/**
 * Update availability display based on status
 */
function updateAvailabilityDisplay(status) {
    const statusElement = document.querySelector('.stat-status');
    const statusCard = document.querySelector('.availability-status');
    const button = document.querySelector('.btn-primary');

    if (!statusElement || !statusCard || !button) return;

    if (status === 'available') {
        // Set to available
        statusElement.textContent = 'Available';
        statusElement.classList.remove('unavailable');
        statusElement.classList.add('available');
        statusElement.style.color = 'var(--success-color)';

        // Update status description
        const statusChange = statusCard.querySelector('.stat-change');
        if (statusChange) {
            statusChange.textContent = 'Ready for new jobs';
        }

        // Update button text
        button.innerHTML = '<i class="fas fa-pause"></i> Set Unavailable';

        // Update icon
        const icon = statusCard.querySelector('.stat-icon i');
        if (icon) {
            icon.className = 'fas fa-check-circle';
        }

        // Update icon background
        const iconBg = statusCard.querySelector('.stat-icon');
        if (iconBg) {
            iconBg.style.background = 'rgba(16, 185, 129, 0.1)';
            iconBg.style.color = 'var(--success-color)';
        }
    } else {
        // Set to unavailable
        statusElement.textContent = 'Unavailable';
        statusElement.classList.remove('available');
        statusElement.classList.add('unavailable');
        statusElement.style.color = 'var(--danger-color)';

        // Update status description
        const statusChange = statusCard.querySelector('.stat-change');
        if (statusChange) {
            statusChange.textContent = 'Not accepting new jobs';
        }

        // Update button text
        button.innerHTML = '<i class="fas fa-play"></i> Set Available';

        // Update icon
        const icon = statusCard.querySelector('.stat-icon i');
        if (icon) {
            icon.className = 'fas fa-pause-circle';
        }

        // Update icon background
        const iconBg = statusCard.querySelector('.stat-icon');
        if (iconBg) {
            iconBg.style.background = 'rgba(239, 68, 68, 0.1)';
            iconBg.style.color = 'var(--danger-color)';
        }
    }
}

/**
 * Handle activity item clicks for quick actions
 */
document.addEventListener('DOMContentLoaded', function () {
    const activityItems = document.querySelectorAll('.activity-item');

    activityItems.forEach(item => {
        item.addEventListener('click', function () {
            // Add click effect
            this.style.transform = 'translateX(8px) scale(1.02)';

            setTimeout(() => {
                this.style.transform = '';
            }, 200);

            // Here you could add navigation to detailed view
            // For example: window.location.href = 'job-details.html?id=123';
        });
    });
});
