# Contract Creation from Accepted Projects - Implementation Guide

## 🎯 Feature Overview

This feature transforms the contract creation process by allowing companies to select from their **accepted quotations** instead of manually entering all project and client details. This ensures data consistency and speeds up contract creation.

## 🔄 Workflow Changes

### Before (Old Workflow)
1. Click "New Contract"
2. Step 1: Manually enter client information
3. Step 2: Manually enter project details
4. Step 3: Enter financial terms
5. Step 4: Review and submit

**Problems:**
- Prone to data entry errors
- Time-consuming duplicate data entry
- Risk of inconsistency between quotation and contract
- No connection to existing accepted quotations

### After (New Workflow)
1. Click "New Contract"
2. **Step 1: Select from accepted projects** ✨ NEW
3. Step 2: Review & edit auto-filled project/client details
4. Step 3: Set financial terms & timeline
5. Step 4: Review and submit

**Benefits:**
✅ Data consistency between quotation and contract
✅ Faster contract creation (data auto-fills)
✅ No duplicate data entry
✅ Clear connection between quotation and contract
✅ Only shows projects ready for contract

## 📊 Technical Implementation

### 1. Backend API Endpoint

#### New Controller Method
**File:** `controllers/ContractController.php`
**Method:** `getAcceptedProjects()`

**Purpose:** Fetch all accepted quotations that haven't been converted to contracts yet

**SQL Query Logic:**
```sql
SELECT quotations and related data
FROM CompanyQuotation q
INNER JOIN JobRequest jr ON q.request_id = jr.request_id
INNER JOIN User u ON jr.user_id = u.user_id
WHERE q.user_id = [company_id]
  AND q.status = 'accepted'
  AND NOT EXISTS (
      SELECT 1 FROM Project p 
      WHERE p.company_id = q.user_id 
      AND p.customer_id = jr.user_id
      AND p.title = jr.title
  )
```

**Data Returned:**
- Quotation ID and Request ID
- Project title, description, type, location
- Quoted price and proposed dates
- Client name, email, phone
- Category and urgency level

#### API Route
**File:** `api/contracts.php`
**Endpoint:** `GET /api/contracts.php?action=getAcceptedProjects`

**Response Format:**
```json
{
  "success": true,
  "data": [
    {
      "quotation_id": 1,
      "request_id": 5,
      "project_title": "Office HVAC Repair",
      "project_description": "Complete HVAC system maintenance",
      "quoted_price": "150000.00",
      "proposed_start_date": "2025-01-15",
      "proposed_end_date": "2025-01-30",
      "client_name": "John Doe",
      "client_email": "john@example.com",
      "client_phone": "+94771234567",
      "location": "123 Main St, Colombo",
      "project_type": "Maintenance"
    }
  ],
  "count": 1
}
```

### 2. Frontend UI Changes

#### Step 1: Project Selection (NEW)

**File:** `views/company/contracts.php`
**Lines:** 638-656

**States:**
1. **Loading State** - Shows spinner while fetching projects
2. **Empty State** - Shows friendly message when no projects available
3. **Projects List** - Grid of selectable project cards

**Project Card Components:**
- Header: Title, description, type badge
- Details: Location, start date, end date, urgency
- Footer: Client name, quoted price
- Selection indicator: Checkmark when selected

#### Step 2: Auto-filled Information

**Fields Auto-populated:**

**Client Information (Read-only):**
- Client Name
- Email Address
- Phone Number

**Project Details (Editable):**
- Project Title
- Project Type
- Location
- Description

**Hidden Fields:**
- `selectedQuotationId` - Links contract to quotation
- `selectedRequestId` - Links contract to original job request

#### Step 3: Financial & Timeline

**Pre-filled Fields:**
- Contract Value (from quoted price)
- Start Date (from proposed start)
- End Date (from proposed end)

**New Fields:**
- Payment Terms dropdown
- Contract Type selection
- Advance payment amount

### 3. JavaScript Implementation

#### New Functions

**File:** `assets/javascript/company/contracts-enhanced.js`

**loadAcceptedProjects()** - Lines ~890-930
- Fetches accepted projects from API
- Handles loading/empty/error states
- Renders project cards dynamically

**createProjectCard(project)** - Lines ~932-1005
- Creates DOM element for each project
- Formats currency and dates
- Adds click handler for selection
- Stores project data in card

**selectProject(cardElement, projectData)** - Lines ~1007-1020
- Removes selection from other cards
- Adds selected class to clicked card
- Calls auto-fill function

**autoFillProjectData(project)** - Lines ~1022-1045
- Populates all form fields with project data
- Sets hidden quotation/request IDs
- Fills client, project, and financial fields

**validateFormStep(step)** - Lines ~1108-1136 (Modified)
- Added Step 1 validation
- Ensures project is selected before proceeding
- Shows error if no project selected

#### Helper Functions

**formatCurrency(amount)** - Format numbers as currency
**formatDate(dateString)** - Format dates to readable format
**escapeHtml(text)** - Sanitize text for XSS prevention

### 4. CSS Styling

**File:** `assets/css/company/contracts.css`
**Lines:** 3895-4104

**Key Classes:**

`.project-selection-container` - Main container
`.loading-projects` - Loading state with spinner
`.no-projects` - Empty state styling
`.projects-list` - Grid layout for project cards
`.project-card` - Individual project card
`.project-card.selected` - Selected state with checkmark
`.project-card-header` - Title and type badge
`.project-card-details` - Grid of project info
`.project-card-footer` - Client and price info
`.readonly-field` - Read-only input styling
`.info-section` - Section separators in Step 2
`.step-description` - Highlighted instructions

**Hover Effects:**
- Border color change
- Box shadow
- Slight lift animation

**Selection Indicator:**
- Green checkmark in circle
- Background tint
- Enhanced shadow

## 📋 Step-by-Step User Flow

### Step 1: Select Project
1. User clicks "New Contract" button
2. Modal opens showing loading spinner
3. System fetches accepted quotations from API
4. Project cards appear in a scrollable grid
5. User clicks on desired project card
6. Card highlights with checkmark ✓
7. Project data is stored and auto-fills form
8. User clicks "Next" to proceed

**Validation:** Must select a project to proceed

### Step 2: Review Project Details
1. Client information appears (read-only)
   - Name, email, phone from customer profile
2. Project details appear (editable)
   - Title, type, location, description
3. User can edit project details if needed
4. User clicks "Next" to proceed

**Validation:** All required fields must have values

### Step 3: Financial & Timeline
1. Contract value pre-filled from quotation
2. Start/end dates pre-filled from proposal
3. User selects payment terms
4. User can adjust dates and values
5. User clicks "Next" to proceed

**Validation:** All required financial fields must be filled

### Step 4: Review & Submit
1. All entered information displayed
2. User reviews all details
3. User clicks "Create Contract"
4. Contract created and linked to quotation

## 🗄️ Database Relationships

```
CompanyQuotation (status='accepted')
    ↓ quotation_id, request_id
JobRequest
    ↓ user_id (customer)
User (customer details)
    
When contract created:
    ↓
Contract (links to Project)
    ↓
Project (created from quotation data)
```

**Key Foreign Keys:**
- Contract.project_id → Project.project_id
- Project.company_id → Company.company_id
- Project.customer_id → User.user_id

## 🔍 Data Flow Diagram

```
1. Company submits quotation → CompanyQuotation (status='pending')
2. Customer accepts quotation → CompanyQuotation (status='accepted')
3. Company opens New Contract modal
4. API: getAcceptedProjects()
   ↓
5. Frontend: Display project cards
   ↓
6. Company selects project
   ↓
7. Auto-fill form with:
   - Client data (from User table via JobRequest)
   - Project data (from JobRequest)
   - Financial data (from CompanyQuotation)
   ↓
8. Company reviews/edits and submits
   ↓
9. Contract created (links to Project)
```

## ⚠️ Edge Cases Handled

1. **No Accepted Quotations**
   - Shows friendly "No Projects Available" message
   - Explains why (no accepted quotations yet)

2. **API Error**
   - Shows error message with try again option
   - Logs error to console for debugging

3. **Missing Data**
   - Falls back to empty strings for optional fields
   - Uses "Not set" for missing dates

4. **Already Has Contract**
   - SQL query excludes quotations that have contracts
   - Prevents duplicate contracts for same project

5. **Loading Timeout**
   - Standard fetch timeout handling
   - User can close modal and retry

## 🧪 Testing Checklist

- [ ] Open New Contract modal - Step 1 shows project selection
- [ ] Verify loading spinner appears initially
- [ ] Confirm accepted quotations load correctly
- [ ] Click on project card - should highlight with checkmark
- [ ] Click different project - previous selection should clear
- [ ] Click "Next" without selecting - should show error
- [ ] Select project and click "Next" - should proceed to Step 2
- [ ] Verify client fields are read-only in Step 2
- [ ] Verify project fields are editable in Step 2
- [ ] Verify Step 3 has pre-filled contract value and dates
- [ ] Complete all steps and create contract
- [ ] Verify contract is created in database
- [ ] Verify project no longer appears in selection list

## 🚀 Future Enhancements

### Possible Improvements:
1. **Search & Filter**
   - Add search bar for project titles
   - Filter by project type, date range, or client
   - Sort by price, date, or urgency

2. **Batch Contract Creation**
   - Select multiple projects
   - Create contracts in bulk

3. **Project Preview**
   - View full quotation details before selection
   - Show attached photos/documents
   - Display conversation history

4. **Smart Defaults**
   - Auto-calculate end date based on project type
   - Suggest payment terms based on project value
   - Pre-fill milestones based on project type

5. **Status Indicators**
   - Show how many days since quotation accepted
   - Highlight urgent projects
   - Mark projects near requested start date

## 📚 Related Files

### Backend
- `controllers/ContractController.php` (Lines 373-467)
- `api/contracts.php` (Lines 79-82)
- `models/ContractModel.php` (Existing)

### Frontend
- `views/company/contracts.php` (Lines 638-735)
- `assets/javascript/company/contracts-enhanced.js` (Lines 840-1045, 1108-1136)
- `assets/css/company/contracts.css` (Lines 3895-4104)

### Database
- `CompanyQuotation` table
- `JobRequest` table
- `User` table
- `Contract` table
- `Project` table

## 📝 Notes

- All read-only fields have `readonly` attribute and special styling
- Project selection is mandatory in Step 1
- Quotation ID and Request ID are stored in hidden fields
- Currency formatting uses en-US locale for consistency
- Date formatting shows Month/Day/Year format
- All user input is HTML-escaped to prevent XSS attacks

---

**Feature Status:** ✅ IMPLEMENTED  
**Version:** 1.0.0  
**Date:** December 2025  
**Implementation Time:** ~2 hours
