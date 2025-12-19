# Quotation Management System - Complete Guide

## Overview
The Quotation Management System allows companies to submit, track, edit, and manage quotations for repair requests. Quotations are currently stored in `localStorage` for testing purposes and will be migrated to the database in the next phase.

---

## Features Implemented

### ✅ 1. Enhanced Quotation Form
**Location:** `views/company/repair-requests.php` (Lines 659-850)

**Fields:**
- Request ID (auto-filled, hidden)
- Quotation Title (auto-filled from request)
- Service Description (detailed breakdown)
- **Cost Breakdown:**
  - Labor Cost (required)
  - Material Cost (required)
  - Transport/Delivery Cost (optional)
  - Other Costs (optional)
  - **Total Price** (auto-calculated)
- **Timeline:**
  - Estimated Start Date (with validation)
  - Estimated Completion Date (with validation)
  - Duration in Days (auto-calculated)
- **Terms:**
  - Payment Terms (dropdown)
  - Warranty Period (dropdown)
  - Additional Terms & Conditions (textarea)
  - Quotation Validity Period (days)
- Agreement Checkbox (required)

**Validation:**
- All required fields must be filled
- Dates cannot be in the past
- Completion date must be after start date
- Total price must be greater than zero
- Agreement must be checked

---

### ✅ 2. Real-Time Cost Calculation
**Location:** `assets/javascript/company/repair-requests.js` (Lines 250-275)

**Functionality:**
```javascript
Total Price = Labor Cost + Material Cost + Transport Cost + Other Cost
```

**Features:**
- Automatic calculation on field change
- Real-time updates
- Formatted currency display (LKR)
- Visual cost summary section

---

### ✅ 3. Date Validation & Auto-Duration
**Location:** `assets/javascript/company/repair-requests.js` (Lines 285-320)

**Features:**
- Prevents past dates
- Sets minimum completion date based on start date
- Auto-calculates duration in days
- Validates date logic

---

### ✅ 4. Quotation Submission & Storage
**Location:** `assets/javascript/company/repair-requests.js` (Lines 329-420)

**Data Structure:**
```javascript
{
    quotation_id: "QUOT-1234567890",
    request_id: "REQ-123",
    title: "Kitchen Renovation",
    description: "Complete kitchen remodeling...",
    labor_cost: 50000.00,
    material_cost: 75000.00,
    transport_cost: 5000.00,
    other_cost: 2000.00,
    total_price: 132000.00,
    estimated_start_date: "2025-02-01",
    estimated_completion_date: "2025-02-15",
    estimated_duration: 14,
    payment_terms: "50% upfront, 50% on completion",
    warranty_period: "1 Year",
    terms_conditions: "Additional terms...",
    validity_period: 30,
    status: "pending", // or "accepted"
    submitted_at: "2025-01-15T10:30:00.000Z",
    updated_at: "2025-01-15T10:30:00.000Z"
}
```

**Storage:**
- Uses `localStorage` with key: `quotations`
- Array of quotation objects
- Persists across page reloads

---

### ✅ 5. Logs Section Display
**Location:** `views/company/repair-requests.php` (Lines 565-610)

**Sections:**
1. **Pending Quotations** - Editable quotations awaiting customer response
2. **Accepted Quotations** - Read-only quotations accepted by customers

**Features:**
- Dynamic count badges
- Empty state messages
- Separated by status
- Visual distinction between pending/accepted

---

### ✅ 6. Edit Quotation Functionality
**Location:** `assets/javascript/company/repair-requests.js` (Lines 513-580)

**Workflow:**
1. Click "Edit" button on pending quotation
2. Modal opens with pre-filled data
3. Modal title changes to "Edit Quotation"
4. Edit badge displayed
5. Make changes
6. Submit updates existing quotation
7. Logs refresh automatically

**Restrictions:**
- Only pending quotations can be edited
- Accepted quotations show view-only mode

---

### ✅ 7. Delete Quotation Functionality
**Location:** `assets/javascript/company/repair-requests.js` (Lines 582-610)

**Workflow:**
1. Click "Delete" button on pending quotation
2. Confirmation dialog appears
3. Confirm deletion
4. Quotation removed from array
5. localStorage updated
6. Logs refresh
7. Success notification shown

**Restrictions:**
- Only pending quotations can be deleted
- Accepted quotations cannot be deleted

---

### ✅ 8. View Quotation Details
**Location:** `assets/javascript/company/repair-requests.js` (Lines 612-651)

**Display:**
- Complete quotation details
- Cost breakdown
- Timeline information
- Terms and conditions
- Status and timestamps

**Current Implementation:**
- Shows alert dialog with formatted details
- Can be upgraded to modal in future

---

### ✅ 9. Simulate Customer Acceptance (Demo)
**Location:** `assets/javascript/company/repair-requests.js` (Lines 653-663)

**Purpose:** Testing accepted quotation state

**Usage:**
```javascript
// Open browser console (F12)
simulateAcceptQuotation('QUOT-1234567890')
```

**Effect:**
- Changes quotation status to "accepted"
- Adds acceptance timestamp
- Moves quotation to "Accepted Quotations" section
- Removes edit/delete buttons
- Shows read-only view

---

## User Flow

### Submitting a Quotation
1. Navigate to **Public Requests** or **Direct Requests** tab
2. Find a repair request
3. Click **"Submit Quotation"** button
4. Modal opens with request details pre-filled
5. Fill in cost breakdown, timeline, and terms
6. Review auto-calculated total
7. Check agreement checkbox
8. Click **"Submit Quotation"**
9. Quotation saved to localStorage
10. Automatically switched to **Logs** tab
11. Quotation appears in **Pending Quotations** section

### Editing a Pending Quotation
1. Go to **Logs** tab
2. Find quotation in **Pending Quotations**
3. Click **Edit** button (pencil icon)
4. Modal opens with existing data
5. Make changes
6. Click **"Submit Quotation"** (updates existing)
7. Quotation updated in logs

### Deleting a Quotation
1. Go to **Logs** tab
2. Find quotation in **Pending Quotations**
3. Click **Delete** button (trash icon)
4. Confirm deletion
5. Quotation removed from logs

### Viewing Quotation Details
1. Go to **Logs** tab
2. Click **View** button (eye icon)
3. Details shown in alert dialog

---

## Technical Implementation

### Files Modified

#### 1. `views/company/repair-requests.php`
**Changes:**
- Enhanced quotation modal form (Lines 659-850)
- Changed "Location" labels to "District" (Lines 165-195)
- Restructured logs section for quotations (Lines 565-610)
- Added demo info box (Lines 553-567)

#### 2. `assets/javascript/company/repair-requests.js`
**Changes:**
- Added quotations array initialization (Lines 1-10)
- Enhanced `openQuotationModal()` (Lines 177-240)
- Added `initializeCostCalculation()` (Lines 250-260)
- Added `calculateTotalCost()` (Lines 262-275)
- Added date validation functions (Lines 285-320)
- Modified `submitQuotation()` (Lines 329-420)
- Added `loadQuotationsToLogs()` (Lines 422-510)
- Added `editQuotation()` (Lines 513-580)
- Added `deleteQuotation()` (Lines 582-610)
- Added `viewQuotationDetails()` (Lines 612-651)
- Added `simulateAcceptQuotation()` (Lines 653-663)

#### 3. `assets/css/company/repair-requests.css`
**Changes:**
- Added quotation item styles (Lines 1730-1830)
- Added action button styles
- Added badge styles
- Added empty state styles
- Added count badge styles

#### 4. `models/JobRequestModel.php`
**Changes:**
- Added `getAllPublicRequests()` method (Lines 107-165)
- Added `getDirectRequestsForCompany()` method (Lines 167-235)

---

## Data Flow Diagram

```
┌─────────────────────┐
│  Repair Request     │
│  (Public/Direct)    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ Submit Quotation    │
│ Button Clicked      │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ openQuotationModal()│
│ - Extract request   │
│ - Pre-fill form     │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ User Fills Form     │
│ - Costs auto-calc   │
│ - Dates validated   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ submitQuotation()   │
│ - Validate form     │
│ - Generate ID       │
│ - Save to storage   │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│ loadQuotationsToLogs│
│ - Separate by status│
│ - Render UI         │
└──────────┬──────────┘
           │
     ┌─────┴─────┐
     │           │
     ▼           ▼
┌─────────┐ ┌─────────┐
│ Pending │ │Accepted │
│  (Edit/ │ │ (View   │
│ Delete) │ │  Only)  │
└─────────┘ └─────────┘
```

---

## Database Integration (Future)

### Quotations Table Schema
```sql
CREATE TABLE quotations (
    quotation_id VARCHAR(20) PRIMARY KEY,
    request_id VARCHAR(20) NOT NULL,
    company_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    labor_cost DECIMAL(10,2) NOT NULL,
    material_cost DECIMAL(10,2) NOT NULL,
    transport_cost DECIMAL(10,2) DEFAULT 0.00,
    other_cost DECIMAL(10,2) DEFAULT 0.00,
    total_price DECIMAL(10,2) NOT NULL,
    estimated_start_date DATE NOT NULL,
    estimated_completion_date DATE NOT NULL,
    estimated_duration INT NOT NULL,
    payment_terms VARCHAR(100),
    warranty_period VARCHAR(50),
    terms_conditions TEXT,
    validity_period INT DEFAULT 30,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    accepted_at TIMESTAMP NULL,
    FOREIGN KEY (request_id) REFERENCES job_requests(request_id),
    FOREIGN KEY (company_id) REFERENCES companies(company_id)
);
```

### API Endpoints (To Be Created)

#### Submit Quotation
```
POST /api/quotations/submit
Content-Type: application/json

{
    "request_id": "REQ-123",
    "title": "Kitchen Renovation",
    "description": "...",
    "labor_cost": 50000,
    "material_cost": 75000,
    ...
}

Response:
{
    "success": true,
    "quotation_id": "QUOT-1234567890",
    "message": "Quotation submitted successfully"
}
```

#### Update Quotation
```
PUT /api/quotations/update/{quotation_id}
Content-Type: application/json

{
    "labor_cost": 55000,
    "material_cost": 80000,
    ...
}

Response:
{
    "success": true,
    "message": "Quotation updated successfully"
}
```

#### Delete Quotation
```
DELETE /api/quotations/delete/{quotation_id}

Response:
{
    "success": true,
    "message": "Quotation deleted successfully"
}
```

#### Get Company Quotations
```
GET /api/quotations/company/{company_id}?status=pending

Response:
{
    "success": true,
    "quotations": [...]
}
```

---

## Testing Guide

### 1. Submit a New Quotation
- [ ] Navigate to Public Requests tab
- [ ] Click "Submit Quotation" on any request
- [ ] Fill in all required fields
- [ ] Verify real-time total calculation
- [ ] Check date validation (try past dates)
- [ ] Submit the form
- [ ] Verify it appears in Logs > Pending Quotations

### 2. Edit a Pending Quotation
- [ ] Go to Logs tab
- [ ] Click Edit button on a pending quotation
- [ ] Verify form pre-fills correctly
- [ ] Make changes to costs
- [ ] Verify total recalculates
- [ ] Submit the update
- [ ] Verify changes reflected in logs

### 3. Delete a Quotation
- [ ] Go to Logs tab
- [ ] Click Delete button on a pending quotation
- [ ] Confirm deletion
- [ ] Verify quotation removed from list
- [ ] Verify count badge decrements

### 4. Simulate Acceptance
- [ ] Open browser console (F12)
- [ ] Get a quotation ID from pending list
- [ ] Run: `simulateAcceptQuotation('QUOT-ID')`
- [ ] Verify quotation moves to Accepted section
- [ ] Verify edit/delete buttons removed
- [ ] Try to edit/delete - should show error

### 5. LocalStorage Persistence
- [ ] Submit a quotation
- [ ] Refresh the page
- [ ] Verify quotation still appears in logs
- [ ] Check console: `localStorage.getItem('quotations')`

### 6. Form Validation
- [ ] Try submitting without filling required fields
- [ ] Try submitting without checking agreement
- [ ] Try entering past dates
- [ ] Try completion date before start date
- [ ] Verify error messages appear

---

## Browser Console Commands

### View All Quotations
```javascript
console.log(JSON.parse(localStorage.getItem('quotations')));
```

### Clear All Quotations
```javascript
localStorage.removeItem('quotations');
location.reload();
```

### Add Sample Quotation
```javascript
let quotations = JSON.parse(localStorage.getItem('quotations')) || [];
quotations.push({
    quotation_id: "QUOT-" + Date.now(),
    request_id: "REQ-001",
    title: "Sample Renovation",
    description: "Test quotation",
    labor_cost: 50000,
    material_cost: 75000,
    transport_cost: 5000,
    other_cost: 0,
    total_price: 130000,
    estimated_start_date: "2025-02-01",
    estimated_completion_date: "2025-02-15",
    estimated_duration: 14,
    payment_terms: "50% upfront, 50% on completion",
    warranty_period: "1 Year",
    terms_conditions: "",
    validity_period: 30,
    status: "pending",
    submitted_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
});
localStorage.setItem('quotations', JSON.stringify(quotations));
location.reload();
```

### Accept a Quotation
```javascript
simulateAcceptQuotation('QUOT-1234567890');
```

---

## Known Limitations (Current Version)

1. **No Database Integration**
   - Data stored in localStorage only
   - Not shared across browsers/devices
   - Lost if browser cache cleared

2. **No Backend Validation**
   - All validation is client-side only
   - Can be bypassed by advanced users

3. **No Email Notifications**
   - Customers not notified of quotations
   - Companies not notified of acceptance

4. **No File Attachments**
   - Cannot attach blueprints or images to quotations

5. **Basic View Details**
   - Uses alert dialog instead of modal
   - No print/export functionality

---

## Next Steps (Database Integration)

### Phase 1: Backend Setup
- [ ] Create `quotations` table in database
- [ ] Create `QuotationModel.php` in `models/`
- [ ] Create `QuotationController.php` in `controllers/`
- [ ] Set up API endpoints in `api/quotations.php`

### Phase 2: Frontend Migration
- [ ] Replace localStorage calls with API calls
- [ ] Add loading states during API requests
- [ ] Add error handling for network failures
- [ ] Migrate existing data from localStorage to database

### Phase 3: Enhanced Features
- [ ] Add file upload for quotation attachments
- [ ] Create detailed view modal
- [ ] Add print quotation functionality
- [ ] Add email notifications
- [ ] Add quotation comparison feature
- [ ] Add quotation templates

### Phase 4: Customer Portal
- [ ] Customer can view quotations
- [ ] Customer can accept/reject quotations
- [ ] Customer can request revisions
- [ ] Customer can message company

---

## Troubleshooting

### Quotations Not Appearing in Logs
1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify localStorage: `localStorage.getItem('quotations')`
4. Check if `loadQuotationsToLogs()` is called
5. Refresh the page

### Total Not Calculating
1. Check if cost fields have valid numbers
2. Open console and look for errors in `calculateTotalCost()`
3. Verify event listeners are attached

### Cannot Edit/Delete Accepted Quotation
This is by design! Accepted quotations are locked to prevent changes after customer acceptance.

**Workaround for testing:**
```javascript
// In browser console
let quotations = JSON.parse(localStorage.getItem('quotations'));
quotations.find(q => q.quotation_id === 'QUOT-ID').status = 'pending';
localStorage.setItem('quotations', JSON.stringify(quotations));
location.reload();
```

### Form Validation Errors
- Ensure all required fields are filled
- Check that dates are not in the past
- Verify completion date is after start date
- Ensure agreement checkbox is checked
- Check that total price > 0

---

## Support

For issues or questions:
1. Check this guide first
2. Check browser console for errors (F12)
3. Review the code comments in the files
4. Test with sample data using console commands
5. Contact the development team

---

**Last Updated:** January 2025  
**Version:** 1.0 (LocalStorage Implementation)  
**Status:** ✅ UI Complete - Database Integration Pending
