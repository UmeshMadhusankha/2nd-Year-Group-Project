// ================================================
// UPGRADE PAGE JAVASCRIPT
// ================================================

// Global variables
let selectedPlan = null;
let isAnnualBilling = false;
let modalBillingType = 'monthly'; // Separate billing type for modal

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
    
    // Set initial billing display
    updatePricingDisplay();
    
    // Initialize FAQ items
    initializeFAQ();
}

function attachEventListeners() {
    // Billing toggle (main page)
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
    
}

function selectPlan(planType) {
    selectedPlan = planType;
    const plan = plans[planType];
    
    if (!plan) {
        console.error('Invalid plan type:', planType);
        return;
    }
    
    // Reset modal billing to monthly when opening modal
    modalBillingType = 'monthly';
    
    // Update modal content
    updateModalContent(plan);
    
    // Show modal
    showUpgradeModal();
    
    // Attach billing option listeners after a short delay
    setTimeout(() => {
        attachBillingOptionListeners();
    }, 100);
}

function updateModalContent(plan) {
    
    // Update plan name and icon
    const modalPlanName = document.getElementById('modal-plan-name');
    if (modalPlanName) modalPlanName.textContent = plan.name;
    
    const modalIcon = document.querySelector('.plan-icon-large i');
    if (modalIcon) modalIcon.className = plan.icon;
    
    // Calculate prices
    const monthlyPrice = plan.monthlyPrice;
    const annualPrice = plan.annualPrice;
    const selectedPrice = modalBillingType === 'annual' ? annualPrice : monthlyPrice;
    const totalAmount = modalBillingType === 'annual' ? annualPrice * 12 : monthlyPrice;
    
    // Update billing option prices
    const monthlyOptionPrice = document.getElementById('monthly-option-price');
    const annualOptionPrice = document.getElementById('annual-option-price');
    const annualYearlyTotal = document.getElementById('annual-yearly-total');
    
    if (monthlyOptionPrice) monthlyOptionPrice.textContent = `LKR ${monthlyPrice.toLocaleString()}/month`;
    if (annualOptionPrice) annualOptionPrice.textContent = `LKR ${annualPrice.toLocaleString()}/month`;
    if (annualYearlyTotal) annualYearlyTotal.textContent = `Billed LKR ${(annualPrice * 12).toLocaleString()}/year`;
    
    // Update summary section
    const summaryPlanName = document.getElementById('summary-plan-name');
    const summaryBillingCycle = document.getElementById('summary-billing-cycle');
    const summaryPricePerMonth = document.getElementById('summary-price-per-month');
    const summaryTotalAmount = document.getElementById('summary-total-amount');
    
    if (summaryPlanName) summaryPlanName.textContent = plan.name;
    if (summaryBillingCycle) summaryBillingCycle.textContent = modalBillingType === 'annual' ? 'Annual (12 months)' : 'Monthly';
    if (summaryPricePerMonth) summaryPricePerMonth.textContent = `LKR ${selectedPrice.toLocaleString()}`;
    
    // Update total with animation
    if (summaryTotalAmount) {
        summaryTotalAmount.style.animation = 'none';
        setTimeout(() => {
            summaryTotalAmount.textContent = `LKR ${totalAmount.toLocaleString()}`;
            summaryTotalAmount.style.animation = 'priceUpdate 0.4s ease';
        }, 10);
    }
    
    // Update features list
    const modalFeaturesList = document.getElementById('modal-features-list');
    if (modalFeaturesList) {
        modalFeaturesList.innerHTML = plan.features.slice(0, 4).map(feature => `
            <div class="feature-item">
                <i class="fas fa-check"></i>
                <span>${feature}</span>
            </div>
        `).join('');
    }
    
    
}

function attachBillingOptionListeners() {
    const billingOptions = document.querySelectorAll('.billing-option-card');
    
    billingOptions.forEach(option => {
        option.addEventListener('click', function() {
            const billingType = this.getAttribute('data-billing');
            
            switchBillingType(billingType);
        });
    });
}

function switchBillingType(billingType) {
    modalBillingType = billingType;
    
    // Update active state on cards
    const billingOptions = document.querySelectorAll('.billing-option-card');
    billingOptions.forEach(option => {
        if (option.getAttribute('data-billing') === billingType) {
            option.classList.add('active');
            const radio = option.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        } else {
            option.classList.remove('active');
            const radio = option.querySelector('input[type="radio"]');
            if (radio) radio.checked = false;
        }
    });
    
    // Update prices
    if (selectedPlan) {
        const plan = plans[selectedPlan];
        updateModalContent(plan);
    }
}

function showUpgradeModal() {
    if (upgradeModal) {
        upgradeModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Reset billing options to monthly
        const billingOptions = document.querySelectorAll('.billing-option-card');
        billingOptions.forEach(option => {
            if (option.getAttribute('data-billing') === 'monthly') {
                option.classList.add('active');
                const radio = option.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            } else {
                option.classList.remove('active');
                const radio = option.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
            }
        });
    }
}

function closeUpgradeModal() {
    if (upgradeModal) {
        upgradeModal.classList.remove('active');
        document.body.style.overflow = '';
        selectedPlan = null;
        modalBillingType = 'monthly'; // Reset to monthly
    }
}

function processUpgrade() {
    if (!selectedPlan) {
        console.error('No plan selected');
        return;
    }
    
    const plan = plans[selectedPlan];
    const price = modalBillingType === 'annual' ? plan.annualPrice : plan.monthlyPrice;
    const totalPrice = modalBillingType === 'annual' ? price * 12 : price;
    
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
        showSuccessMessage(plan.name, modalBillingType);
        
        // In a real application, you would redirect to payment processor
        
    }, 2000);
}

function showSuccessMessage(planName, billingType) {
    // Create success notification
    const notification = document.createElement('div');
    notification.className = 'upgrade-notification success';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <div class="notification-text">
                <h4>Upgrade Initiated!</h4>
                <p>You will be redirected to payment for ${planName} (${billingType === 'annual' ? 'Annual' : 'Monthly'})</p>
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
    
}

// Export functions for global access
window.selectPlan = selectPlan;
window.toggleFaq = toggleFaq;
