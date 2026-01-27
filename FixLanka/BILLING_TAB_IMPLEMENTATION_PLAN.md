# Billing/Subscription Tab - Complete Implementation Plan

## Overview
Complete backend integration for the Billing/Subscription tab with real data from the database, including subscription plans, payment methods, and billing history.

---

## Phase 1: Database Structure ✅
**Tables Required:**
1. `billinghistory` - Already exists (stores invoice history)
2. `company` - Already has company data
3. Need to add: `company_subscriptions` table
4. Need to add: `payment_methods` table

---

## Phase 2: Backend Implementation

### 2.1 New Database Tables

**Table: company_subscriptions**
```sql
CREATE TABLE company_subscriptions (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    plan_name ENUM('free', 'basic', 'professional', 'enterprise') DEFAULT 'free',
    plan_price DECIMAL(10,2) NOT NULL,
    billing_period ENUM('monthly', 'yearly') DEFAULT 'monthly',
    status ENUM('active', 'cancelled', 'expired', 'trial') DEFAULT 'trial',
    start_date DATE NOT NULL,
    end_date DATE NULL,
    next_billing_date DATE NULL,
    auto_renew TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE
);
```

**Table: payment_methods**
```sql
CREATE TABLE payment_methods (
    payment_method_id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    card_type ENUM('visa', 'mastercard', 'amex', 'discover') NOT NULL,
    last_four_digits CHAR(4) NOT NULL,
    card_holder_name VARCHAR(100) NOT NULL,
    expiry_month CHAR(2) NOT NULL,
    expiry_year CHAR(4) NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES company(company_id) ON DELETE CASCADE
);
```

### 2.2 CompanyModel Methods to Add

```php
// Get subscription details
public function getSubscription($companyId)

// Get payment methods
public function getPaymentMethods($companyId)

// Add payment method
public function addPaymentMethod($companyId, $data)

// Remove payment method
public function removePaymentMethod($paymentMethodId, $companyId)

// Set primary payment method
public function setPrimaryPaymentMethod($paymentMethodId, $companyId)

// Update subscription plan
public function updateSubscriptionPlan($companyId, $planName, $billingPeriod)

// Cancel subscription
public function cancelSubscription($companyId)
```

### 2.3 API Endpoints to Add

**GET Requests:**
- Fetch subscription data
- Fetch payment methods
- Fetch billing history

**POST Actions:**
- `add_payment_method` - Add new card
- `remove_payment_method` - Remove card
- `set_primary_payment` - Set primary card
- `change_plan` - Update subscription plan
- `cancel_subscription` - Cancel subscription
- `download_invoice` - Download PDF invoice

---

## Phase 3: Frontend JavaScript

### 3.1 On Page Load
```javascript
async function loadBillingData() {
    // Fetch subscription, payment methods, and billing history
    // Populate UI with real data
}
```

### 3.2 Subscription Management
```javascript
function changePlan() - Show modal to select new plan
function cancelSubscription() - Show confirmation, call API
```

### 3.3 Payment Methods
```javascript
function addPaymentMethod() - Show modal with card form
function removePaymentMethod(id) - Confirmation + API call
function setPrimaryPaymentMethod(id) - Update primary card
```

### 3.4 Billing History
```javascript
function downloadInvoice(invoiceId) - Download PDF
function viewInvoice(invoiceId) - View in modal/new tab
function loadAllInvoices() - Paginated history
```

---

## Phase 4: UI Enhancements

### 4.1 Modals Needed
1. **Change Plan Modal** - Select new plan, see pricing comparison
2. **Add Payment Method Modal** - Card details form with validation
3. **Cancel Subscription Modal** - Confirmation with feedback form
4. **View Invoice Modal** - Display invoice details

### 4.2 Plan Features
```javascript
const PLANS = {
    free: { price: 0, features: ['5 requests/month', '1 team member', 'Email support'] },
    basic: { price: 2500, features: ['50 requests/month', '3 team members', 'Chat support'] },
    professional: { price: 5000, features: ['Unlimited requests', '10 team members', 'Priority support', 'Analytics'] },
    enterprise: { price: 10000, features: ['Everything + Custom features', 'Unlimited team', '24/7 support', 'API access'] }
};
```

---

## Implementation Steps

1. ✅ Create database tables (company_subscriptions, payment_methods)
2. ⏳ Add methods to CompanyModel.php
3. ⏳ Update api/settings.php with new endpoints
4. ⏳ Add JavaScript functions to settings.php
5. ⏳ Create modal HTML for plan changes and payment methods
6. ⏳ Add CSS styling for new modals
7. ⏳ Test all functionality end-to-end

---

## Testing Checklist

- [ ] Load billing data on page load
- [ ] Display current subscription plan correctly
- [ ] Show payment methods (primary badge works)
- [ ] Add new payment method
- [ ] Remove payment method
- [ ] Set primary payment method
- [ ] Change subscription plan
- [ ] Cancel subscription
- [ ] View billing history
- [ ] Download invoice
- [ ] Verify real-time updates (no page refresh needed)

---

## Security Considerations

1. **Card Data**: Never store full card numbers (only last 4 digits + expiry)
2. **PCI Compliance**: Use payment gateway (Stripe/PayPal) for processing
3. **Validation**: Server-side validation for all payment operations
4. **Authorization**: Verify company_id matches session user_id
5. **Encryption**: Use HTTPS for all payment-related requests

---

## Next Steps

Start with Phase 1: Create the database tables, then move to backend implementation.
