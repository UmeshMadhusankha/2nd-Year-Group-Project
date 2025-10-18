// ================================================
// UPGRADE PAGE JAVASCRIPT
// ================================================

// Global variables
let selectedPlan = null;
let isAnnualBilling = false;

// Plan data
const plans = {
    pro: {
        name: 'Pro Plan',
        monthlyPrice: 2500,
        annualPrice: 2000,
        icon: 'fas fa-star',
        features: [
            'Unlimited job applications',
            'Priority in search results',
            'Advanced analytics dashboard',
            'Customer contact information',
            'Pro badge on profile',
            '24/7 priority support',
            'Reduced platform fees (12%)'
        ]
    },
    business: {
        name: 'Business Plan',
        monthlyPrice: 4500,
        annualPrice: 3600,
        icon: 'fas fa-building',
        features: [
            'Everything in Pro Plan',
            'Team member accounts (up to 5)',
            'Advanced scheduling tools',
            'Custom business profile',
            'Invoice generation & tracking',
            'Dedicated account manager',
            'Lowest platform fees (8%)'
        ]
    }
};

// DOM elements
const billingToggle = document.getElementById('billing-toggle');
const upgradeModal = document.getElementById('upgrade-modal-overlay');
const closeModalBtn = document.getElementById('close-upgrade-modal');
const cancelUpgradeBtn = document.getElementById('cancel-upgrade');
const confirmUpgradeBtn = document.getElementById('confirm-upgrade');

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
    attachEventListeners();
});

function initializePage() {
    console.log('Upgrade page initialized');
    
    // Set initial billing display
    updatePricingDisplay();
    
    // Initialize FAQ items
    initializeFAQ();
}

function attachEventListeners() {
    // Billing toggle
    if (billingToggle) {
        billingToggle.addEventListener('change', function() {
            isAnnualBilling = this.checked;
            updatePricingDisplay();
        });
    }
    
    // Modal event listeners
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeUpgradeModal);
    }
    
    if (cancelUpgradeBtn) {
        cancelUpgradeBtn.addEventListener('click', closeUpgradeModal);
    }
    
    if (confirmUpgradeBtn) {
        confirmUpgradeBtn.addEventListener('click', processUpgrade);
    }
    
    // Close modal on overlay click
    if (upgradeModal) {
        upgradeModal.addEventListener('click', function(e) {
            if (e.target === upgradeModal) {
                closeUpgradeModal();
            }
        });
    }
    
    // ESC key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && upgradeModal.classList.contains('active')) {
            closeUpgradeModal();
        }
    });
}

function updatePricingDisplay() {
    // This is handled by CSS, but we can add JavaScript enhancements here
    console.log('Billing mode:', isAnnualBilling ? 'Annual' : 'Monthly');
}

function selectPlan(planType) {
    selectedPlan = planType;
    const plan = plans[planType];
    
    if (!plan) {
        console.error('Invalid plan type:', planType);
        return;
    }
    
    // Update modal content
    updateModalContent(plan);
    
    // Show modal
    showUpgradeModal();
}

function updateModalContent(plan) {
    // Update plan name
    const modalPlanName = document.getElementById('modal-plan-name');
    const summaryPlan = document.getElementById('summary-plan');
    if (modalPlanName) modalPlanName.textContent = plan.name;
    if (summaryPlan) summaryPlan.textContent = plan.name;
    
    // Update plan price
    const price = isAnnualBilling ? plan.annualPrice : plan.monthlyPrice;
    const modalPlanPrice = document.getElementById('modal-plan-price');
    const summaryTotal = document.getElementById('summary-total');
    if (modalPlanPrice) modalPlanPrice.textContent = `LKR ${price.toLocaleString()}/month`;
    if (summaryTotal) summaryTotal.textContent = `LKR ${price.toLocaleString()}`;
    
    // Update billing type
    const summaryBilling = document.getElementById('summary-billing');
    if (summaryBilling) summaryBilling.textContent = isAnnualBilling ? 'Annual' : 'Monthly';
    
    // Update plan icon
    const modalIcon = document.querySelector('.modal-body .plan-icon i');
    if (modalIcon) {
        modalIcon.className = plan.icon;
    }
    
    // Update benefits list
    const modalBenefits = document.getElementById('modal-benefits');
    if (modalBenefits) {
        modalBenefits.innerHTML = plan.features.map(feature => 
            `<li><i class="fas fa-check"></i> ${feature}</li>`
        ).join('');
    }
}

function showUpgradeModal() {
    if (upgradeModal) {
        upgradeModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeUpgradeModal() {
    if (upgradeModal) {
        upgradeModal.classList.remove('active');
        document.body.style.overflow = '';
        selectedPlan = null;
    }
}

function processUpgrade() {
    if (!selectedPlan) {
        console.error('No plan selected');
        return;
    }
    
    const plan = plans[selectedPlan];
    const price = isAnnualBilling ? plan.annualPrice : plan.monthlyPrice;
    
    // Show loading state
    confirmUpgradeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    confirmUpgradeBtn.disabled = true;
    
    // Simulate payment process
    setTimeout(() => {
        // Reset button
        confirmUpgradeBtn.innerHTML = '<i class="fas fa-credit-card"></i> Proceed to Payment';
        confirmUpgradeBtn.disabled = false;
        
        // Close modal
        closeUpgradeModal();
        
        // Show success message
        showSuccessMessage(plan.name);
        
        // In a real application, you would redirect to payment processor
        console.log('Redirecting to payment for:', plan.name, 'Price:', price);
    }, 2000);
}

function showSuccessMessage(planName) {
    // Create success notification
    const notification = document.createElement('div');
    notification.className = 'upgrade-notification success';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <div class="notification-text">
                <h4>Upgrade Initiated!</h4>
                <p>You will be redirected to payment for ${planName}</p>
            </div>
        </div>
    `;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success-color);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 1001;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// FAQ Functions
function initializeFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            toggleFaq(this);
        });
    });
}

function toggleFaq(element) {
    const faqItem = element.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
        faqItem.classList.add('active');
    }
}

// Utility Functions
function formatPrice(price) {
    return `LKR ${price.toLocaleString()}`;
}

function trackUpgradeEvent(planType, billingType) {
    // Analytics tracking would go here
    console.log('Upgrade event:', { planType, billingType });
}

// Export functions for global access
window.selectPlan = selectPlan;
window.toggleFaq = toggleFaq;
