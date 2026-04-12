// ================================================
// PAYMENT PAGE - SECURE PAYMENT PROCESSING
// ================================================

document.addEventListener('DOMContentLoaded', function () {

    // ================================================
    // DOM ELEMENTS
    // ================================================
    const paymentForm = document.getElementById('paymentForm');
    const cancelBtn = document.getElementById('cancelBtn');
    const payBtn = document.getElementById('payBtn');

    // Form inputs
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const stateInput = document.getElementById('state');
    const zipCodeInput = document.getElementById('zipCode');
    const countrySelect = document.getElementById('country');
    const cardNumberInput = document.getElementById('cardNumber');
    const expiryDateInput = document.getElementById('expiryDate');
    const cvvInput = document.getElementById('cvv');
    const saveCardCheckbox = document.getElementById('saveCard');

    // Card detection
    const cardIcon = document.getElementById('cardIcon');
    const cardType = document.getElementById('cardType');

    // Promo code
    const promoCodeInput = document.getElementById('promoCode');
    const applyPromoBtn = document.getElementById('applyPromoBtn');
    const promoMessage = document.getElementById('promoMessage');

    // Price elements
    const subtotalElement = document.getElementById('subtotal');
    const serviceFeeElement = document.getElementById('serviceFee');
    const discountRow = document.getElementById('discountRow');
    const discountElement = document.getElementById('discount');
    const totalElement = document.getElementById('total');

    // Modals
    const successModal = document.getElementById('successModal');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const viewOrderBtn = document.getElementById('viewOrderBtn');
    const backToDashboardBtn = document.getElementById('backToDashboardBtn');
    const transactionIdElement = document.getElementById('transactionId');
    const amountPaidElement = document.getElementById('amountPaid');

    // ================================================
    // PAYMENT DATA
    // ================================================
    const paymentData = {
        subtotal: 5000,
        serviceFee: 500,
        discount: 0,
        total: 5500,
        appliedPromo: null
    };

    // Valid promo codes
    const promoCodes = {
        'FIRST10': { type: 'percentage', value: 10, description: '10% off your first payment' },
        'SAVE500': { type: 'fixed', value: 500, description: 'LKR 500 off' },
        'FIXLANKA': { type: 'percentage', value: 15, description: '15% discount' }
    };

    // ================================================
    // CARD TYPE DETECTION
    // ================================================
    const cardPatterns = {
        visa: /^4/,
        mastercard: /^5[1-5]/,
        amex: /^3[47]/,
        discover: /^6(?:011|5)/
    };

    const cardIcons = {
        visa: '<i class="fab fa-cc-visa" style="color: #1A1F71;"></i>',
        mastercard: '<i class="fab fa-cc-mastercard" style="color: #EB001B;"></i>',
        amex: '<i class="fab fa-cc-amex" style="color: #006FCF;"></i>',
        discover: '<i class="fab fa-cc-discover" style="color: #FF6000;"></i>',
        default: '<i class="fas fa-credit-card" style="color: #64748b;"></i>'
    };

    // ================================================
    // INITIALIZE
    // ================================================
    function init() {
        setupEventListeners();
        loadSavedData();
        updatePriceSummary();
    }

    // ================================================
    // EVENT LISTENERS
    // ================================================
    function setupEventListeners() {
        // Form submission
        paymentForm.addEventListener('submit', handlePayment);

        // Cancel button
        cancelBtn.addEventListener('click', handleCancel);

        // Card number formatting and detection
        cardNumberInput.addEventListener('input', function (e) {
            formatCardNumber(e);
            detectCardType(e.target.value);
        });

        // Expiry date formatting
        expiryDateInput.addEventListener('input', formatExpiryDate);

        // CVV validation
        cvvInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });

        // ZIP code validation
        zipCodeInput.addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\d\s-]/g, '');
        });

        // Promo code
        applyPromoBtn.addEventListener('click', applyPromoCode);
        promoCodeInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyPromoCode();
            }
        });

        // Real-time validation
        fullNameInput.addEventListener('blur', validateFullName);
        emailInput.addEventListener('blur', validateEmail);
        cardNumberInput.addEventListener('blur', validateCardNumber);
        expiryDateInput.addEventListener('blur', validateExpiryDate);
        cvvInput.addEventListener('blur', validateCVV);

        // Success modal actions
        viewOrderBtn.addEventListener('click', function () {
            window.location.href = 'job-history.html';
        });

        backToDashboardBtn.addEventListener('click', function () {
            window.location.href = 'dashboard.html';
        });

        // Auto-save form data (for convenience, not sensitive data)
        const formInputs = [fullNameInput, emailInput, addressInput, cityInput, stateInput, zipCodeInput];
        formInputs.forEach(input => {
            input.addEventListener('change', saveFormData);
        });
    }

    // ================================================
    // CARD NUMBER FORMATTING
    // ================================================
    function formatCardNumber(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    }

    // ================================================
    // CARD TYPE DETECTION
    // ================================================
    function detectCardType(cardNumber) {
        const cleanNumber = cardNumber.replace(/\s/g, '');
        let detectedType = 'default';

        for (const [type, pattern] of Object.entries(cardPatterns)) {
            if (pattern.test(cleanNumber)) {
                detectedType = type;
                break;
            }
        }

        cardType.innerHTML = cardIcons[detectedType];

        // Update CVV max length based on card type
        cvvInput.maxLength = detectedType === 'amex' ? 4 : 3;
    }

    // ================================================
    // EXPIRY DATE FORMATTING
    // ================================================
    function formatExpiryDate(e) {
        let value = e.target.value.replace(/\D/g, '');

        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }

        e.target.value = value;
    }

    // ================================================
    // VALIDATION FUNCTIONS
    // ================================================
    function validateFullName() {
        const value = fullNameInput.value.trim();
        const isValid = value.length >= 3 && /^[a-zA-Z\s]+$/.test(value);

        setValidationState(fullNameInput, isValid, 'Please enter a valid full name');
        return isValid;
    }

    function validateEmail() {
        const value = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(value);

        setValidationState(emailInput, isValid, 'Please enter a valid email address');
        return isValid;
    }

    function validateCardNumber() {
        const value = cardNumberInput.value.replace(/\s/g, '');
        const isValid = value.length >= 13 && value.length <= 19 && /^\d+$/.test(value);

        setValidationState(cardNumberInput, isValid, 'Please enter a valid card number');
        return isValid;
    }

    function validateExpiryDate() {
        const value = expiryDateInput.value;
        const [month, year] = value.split('/');

        if (!month || !year) {
            setValidationState(expiryDateInput, false, 'Please enter expiry date (MM/YY)');
            return false;
        }

        const currentDate = new Date();
        const currentYear = currentDate.getFullYear() % 100;
        const currentMonth = currentDate.getMonth() + 1;

        const expMonth = parseInt(month);
        const expYear = parseInt(year);

        const isValid = expMonth >= 1 && expMonth <= 12 &&
            (expYear > currentYear || (expYear === currentYear && expMonth >= currentMonth));

        setValidationState(expiryDateInput, isValid, 'Card has expired or invalid date');
        return isValid;
    }

    function validateCVV() {
        const value = cvvInput.value;
        const isValid = value.length >= 3 && value.length <= 4 && /^\d+$/.test(value);

        setValidationState(cvvInput, isValid, 'Please enter a valid CVV');
        return isValid;
    }

    function setValidationState(input, isValid, message) {
        const inputWrapper = input.closest('.input-wrapper') || input.parentElement;
        const existingError = inputWrapper.querySelector('.error-message');

        if (existingError) {
            existingError.remove();
        }

        if (!isValid && input.value.trim() !== '') {
            input.style.borderColor = 'var(--danger-color)';

            const errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.style.cssText = `
                color: var(--danger-color);
                font-size: var(--font-size-sm);
                margin-top: var(--spacing-xs);
                display: flex;
                align-items: center;
                gap: var(--spacing-xs);
            `;
            errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

            inputWrapper.appendChild(errorElement);
        } else {
            input.style.borderColor = isValid ? 'var(--success-color)' : '';
        }
    }

    // ================================================
    // PROMO CODE HANDLING
    // ================================================
    function applyPromoCode() {
        const code = promoCodeInput.value.trim().toUpperCase();

        if (!code) {
            showPromoMessage('Please enter a promo code', 'error');
            return;
        }

        if (promoCodes[code]) {
            const promo = promoCodes[code];
            paymentData.appliedPromo = code;

            if (promo.type === 'percentage') {
                paymentData.discount = Math.round(paymentData.subtotal * promo.value / 100);
            } else {
                paymentData.discount = promo.value;
            }

            updatePriceSummary();
            showPromoMessage(`${promo.description} applied!`, 'success');

            applyPromoBtn.disabled = true;
            applyPromoBtn.textContent = 'Applied';
            promoCodeInput.disabled = true;
        } else {
            showPromoMessage('Invalid promo code', 'error');
        }
    }

    function showPromoMessage(message, type) {
        promoMessage.textContent = message;
        promoMessage.className = `promo-message ${type}`;
        promoMessage.style.display = 'block';

        if (type === 'error') {
            setTimeout(() => {
                promoMessage.style.display = 'none';
            }, 3000);
        }
    }

    // ================================================
    // PRICE SUMMARY UPDATE
    // ================================================
    function updatePriceSummary() {
        paymentData.total = paymentData.subtotal + paymentData.serviceFee - paymentData.discount;

        subtotalElement.textContent = `LKR ${paymentData.subtotal.toLocaleString()}`;
        serviceFeeElement.textContent = `LKR ${paymentData.serviceFee.toLocaleString()}`;
        totalElement.textContent = `LKR ${paymentData.total.toLocaleString()}`;

        if (paymentData.discount > 0) {
            discountRow.style.display = 'flex';
            discountElement.textContent = `- LKR ${paymentData.discount.toLocaleString()}`;
        } else {
            discountRow.style.display = 'none';
        }
    }

    // ================================================
    // PAYMENT SUBMISSION
    // ================================================
    function handlePayment(e) {
        e.preventDefault();

        // Validate all fields
        const isFullNameValid = validateFullName();
        const isEmailValid = validateEmail();
        const isCardNumberValid = validateCardNumber();
        const isExpiryValid = validateExpiryDate();
        const isCVVValid = validateCVV();

        if (!isFullNameValid || !isEmailValid || !isCardNumberValid || !isExpiryValid || !isCVVValid) {
            showNotification('Please fix the errors in the form', 'error');
            return;
        }

        // Collect payment data
        const paymentInfo = {
            fullName: fullNameInput.value.trim(),
            email: emailInput.value.trim(),
            address: {
                street: addressInput.value.trim(),
                city: cityInput.value.trim(),
                state: stateInput.value.trim(),
                zipCode: zipCodeInput.value.trim(),
                country: countrySelect.value
            },
            card: {
                number: cardNumberInput.value.replace(/\s/g, ''),
                expiry: expiryDateInput.value,
                cvv: cvvInput.value,
                saveCard: saveCardCheckbox.checked
            },
            amount: paymentData.total,
            promoCode: paymentData.appliedPromo
        };


        // Show loading overlay
        loadingOverlay.classList.add('show');
        payBtn.disabled = true;

        // Simulate payment processing
        setTimeout(() => {
            processPayment(paymentInfo);
        }, 3000);
    }

    // ================================================
    // PROCESS PAYMENT
    // ================================================
    function processPayment(paymentInfo) {
        // Simulate successful payment
        const transactionId = 'TXN' + Date.now();

        // Hide loading
        loadingOverlay.classList.remove('show');

        // Update success modal
        transactionIdElement.textContent = transactionId;
        amountPaidElement.textContent = `LKR ${paymentData.total.toLocaleString()}`;

        // Show success modal
        successModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Clear sensitive data
        clearFormData();

        // Store transaction in localStorage (for demo purposes)
        storeTransaction({
            id: transactionId,
            amount: paymentData.total,
            date: new Date().toISOString(),
            status: 'completed',
            paymentInfo: {
                name: paymentInfo.fullName,
                email: paymentInfo.email
            }
        });
    }

    // ================================================
    // CANCEL PAYMENT
    // ================================================
    async function handleCancel() {
        const confirmed = await window.showConfirm('Are you sure you want to cancel this payment?', { title: 'Cancel Payment', confirmText: 'Yes, Cancel', type: 'warning' });
        if (confirmed) {
            window.location.href = 'job-history.html';
        }
    }

    // ================================================
    // DATA PERSISTENCE
    // ================================================
    function saveFormData() {
        const formData = {
            fullName: fullNameInput.value,
            email: emailInput.value,
            address: addressInput.value,
            city: cityInput.value,
            state: stateInput.value,
            zipCode: zipCodeInput.value,
            country: countrySelect.value
        };

        localStorage.setItem('fixlanka_payment_form', JSON.stringify(formData));
    }

    function loadSavedData() {
        const savedData = localStorage.getItem('fixlanka_payment_form');

        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                fullNameInput.value = data.fullName || '';
                emailInput.value = data.email || '';
                addressInput.value = data.address || '';
                cityInput.value = data.city || '';
                stateInput.value = data.state || '';
                zipCodeInput.value = data.zipCode || '';
                countrySelect.value = data.country || 'LK';
            } catch (error) {
                console.error('Error loading saved data:', error);
            }
        }
    }

    function clearFormData() {
        // Clear sensitive card data
        cardNumberInput.value = '';
        expiryDateInput.value = '';
        cvvInput.value = '';
        saveCardCheckbox.checked = false;
    }

    function storeTransaction(transaction) {
        const transactions = JSON.parse(localStorage.getItem('fixlanka_transactions') || '[]');
        transactions.unshift(transaction);
        localStorage.setItem('fixlanka_transactions', JSON.stringify(transactions));
    }

    // ================================================
    // NOTIFICATION
    // ================================================
    function showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'payment-toast ' + type;
        toast.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: ${type === 'error' ? 'var(--danger-color)' : 'var(--success-color)'};
            color: white;
            padding: var(--spacing-md) var(--spacing-lg);
            border-radius: var(--border-radius-sm);
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
        `;

        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <span style="margin-left: 10px;">${message}</span>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add animation styles
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // ================================================
    // KEYBOARD SHORTCUTS
    // ================================================
    document.addEventListener('keydown', function (e) {
        // ESC to cancel
        if (e.key === 'Escape' && !successModal.classList.contains('show')) {
            handleCancel();
        }

        // Ctrl/Cmd + Enter to submit
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            paymentForm.dispatchEvent(new Event('submit'));
        }
    });

    // ================================================
    // SECURITY FEATURES
    // ================================================

    // Prevent copy/paste on sensitive fields
    [cardNumberInput, cvvInput].forEach(input => {
        input.addEventListener('copy', e => e.preventDefault());
        input.addEventListener('paste', e => e.preventDefault());
        input.addEventListener('cut', e => e.preventDefault());
    });

    // Clear form on page unload (security)
    window.addEventListener('beforeunload', function () {
        cardNumberInput.value = '';
        cvvInput.value = '';
    });

    // ================================================
    // INITIALIZE APP
    // ================================================
    init();
});