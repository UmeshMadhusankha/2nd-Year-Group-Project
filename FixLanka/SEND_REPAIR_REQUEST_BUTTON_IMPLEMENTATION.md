# Send Repair Request Button Implementation

## Overview
Added "Send Repair Request" buttons to both repairer and company profile view pages on the landing page.

## Changes Made

### 1. Repairer Profile Popup (`repairer-profile-popup.php`)
**Location:** Action buttons section

**Added:**
- Primary button "Send Repair Request" with tools icon
- Positioned as the first (most prominent) action button
- Existing buttons reorganized:
  - Send Message → Secondary button
  - Request Quote → Outline button
  - View Full Profile → Outline button

**Button HTML:**
```html
<button class="btn-primary" onclick="sendRepairRequest('repairer')">
    <i class="fas fa-tools"></i>
    Send Repair Request
</button>
```

### 2. Company Modal (`landing.js`)
**Location:** Company profile modal - after services section

**Added:**
- New action buttons section (`company-modal-actions`)
- Three action buttons:
  1. **Send Repair Request** (Primary) - tools icon
  2. **Contact Company** (Secondary) - comment icon
  3. **Request Quote** (Outline) - invoice icon

**Button HTML:**
```javascript
<div class="company-modal-actions">
    <button class="btn-primary" onclick="sendRepairRequest('company', ${companyId})">
        <i class="fas fa-tools"></i>
        Send Repair Request
    </button>
    <button class="btn-secondary" onclick="contactCompany(${companyId})">
        <i class="fas fa-comment"></i>
        Contact Company
    </button>
    <button class="btn-outline" onclick="requestCompanyQuote(${companyId})">
        <i class="fas fa-file-invoice"></i>
        Request Quote
    </button>
</div>
```

### 3. JavaScript Functions (`landing.js`)
**Added placeholder functions (no functionality yet):**

#### `sendRepairRequest(type, providerId)`
- Handles repair request for both repairers and companies
- Shows alert indicating feature is coming soon
- Logs request details to console
- Parameters:
  - `type`: 'repairer' or 'company'
  - `providerId`: Optional ID of the provider

#### `contactCompany(companyId)`
- Placeholder for company contact functionality
- Shows alert with coming soon message

#### `requestCompanyQuote(companyId)`
- Placeholder for company quote request functionality
- Shows alert with coming soon message

### 4. CSS Styling (`landing.css`)
**Added styles for company modal actions:**

```css
.company-modal-actions {
  display: flex;
  gap: var(--spacing-md);
  padding: var(--spacing-xl) 0 var(--spacing-md) 0;
  border-top: 1px solid var(--border-color);
  margin-top: var(--spacing-xl);
  flex-wrap: wrap;
}
```

**Button styles:**
- `.btn-primary` - Gradient background (primary to secondary color)
- `.btn-secondary` - Light background with border
- `.btn-outline` - Transparent with primary border
- All buttons have hover effects (translateY, box-shadow)
- Responsive flex layout (wraps on smaller screens)

## Button Appearance

### Repairer Profile Popup
```
┌─────────────────────────────────────────┐
│ [🔧 Send Repair Request]  (Primary)     │
│ [💬 Send Message]         (Secondary)   │
│ [📄 Request Quote]        (Outline)     │
│ [🔗 View Full Profile]    (Outline)     │
└─────────────────────────────────────────┘
```

### Company Profile Modal
```
┌─────────────────────────────────────────┐
│ Services Section...                     │
├─────────────────────────────────────────┤
│ [🔧 Send Repair Request] (Primary)      │
│ [💬 Contact Company]     (Secondary)    │
│ [📄 Request Quote]       (Outline)      │
└─────────────────────────────────────────┘
```

## Files Modified

1. ✅ `views/user/repairer-profile-popup.php` - Added button to action section
2. ✅ `assets/javascript/user/landing.js` - Added button to company modal + placeholder functions
3. ✅ `assets/css/user/landing.css` - Added CSS for company modal actions

## Current Status

### ✅ Completed
- Button UI implemented in both profile views
- Proper styling and hover effects
- Responsive layout
- Console logging for debugging
- Alert messages for user feedback

### ⏳ Pending (Future Implementation)
- Actual repair request functionality
- Integration with job posting system
- Database operations
- Form validation
- User authentication checks
- Request submission workflow
- Success/error handling
- Email notifications

## Usage

**For Repairers:**
1. User clicks on a repairer card
2. Profile popup opens
3. "Send Repair Request" button is visible as primary action
4. Clicking shows "coming soon" alert

**For Companies:**
1. User clicks "View Company Details" on a company card
2. Company modal opens
3. Action buttons appear at the bottom after services section
4. "Send Repair Request" is the primary (most prominent) button
5. Clicking shows "coming soon" alert with type and ID

## Technical Details

### Button Classes
- `btn-primary` - Main action button (gradient background)
- `btn-secondary` - Secondary action (solid background)
- `btn-outline` - Tertiary action (border only)

### Icons Used
- `fa-tools` - Repair request
- `fa-comment` - Contact/message
- `fa-file-invoice` - Quote request
- `fa-external-link-alt` - View profile

### Responsive Behavior
- Buttons use flex layout with wrap
- Min-width: 150px ensures readability
- On mobile, buttons stack vertically
- Gap spacing maintained across screen sizes

## Testing Recommendations

1. ✅ Click "Send Repair Request" on repairer profile
2. ✅ Click "Send Repair Request" on company modal
3. ✅ Verify alert message appears
4. ✅ Check console logs for correct parameters
5. ✅ Test button hover states
6. ✅ Test responsive behavior on mobile
7. ✅ Verify button styling matches design system

## Future TODOs

```javascript
// TODO: Implement repair request functionality
function sendRepairRequest(type, providerId) {
    // 1. Check if user is logged in
    // 2. Redirect to post-job page with pre-filled provider info
    // 3. Or open inline request form modal
    // 4. Collect repair request details
    // 5. Submit to backend API
    // 6. Show success message
    // 7. Optionally navigate to job history
}
```

## Date Implemented
October 24, 2025

## Developer Notes
- Button functionality intentionally left as placeholder
- Easy to implement actual functionality later
- Design follows existing button patterns
- No breaking changes to existing code
- Backward compatible
