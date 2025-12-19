# Company Repair Requests - Backend CRUD Implementation

## Overview
Complete backend implementation for the Company Repair Requests page with full CRUD operations for CompanyQuotation and JobRequest integration.

## Files Created/Modified

### 1. **Backend Model**
**File:** `models/CompanyQuotationModel.php`

**Features:**
- Full CRUD operations for CompanyQuotation table
- Methods:
  - `create($data)` - Create new quotation
  - `getAll($filters)` - Get all quotations with optional filtering
  - `getById($quotation_id)` - Get single quotation by ID
  - `update($quotation_id, $data)` - Update pending quotation
  - `delete($quotation_id)` - Delete pending quotation
  - `hasQuotationForRequest($request_id)` - Check if quotation exists
  - `updateStatus($quotation_id, $status)` - Update quotation status

**Database Integration:**
- Joins with JobRequest, User, and Category tables
- Returns complete quotation details with customer and job information
- Validation for pending-only updates/deletes

### 2. **API Endpoint**
**File:** `api/company-quotes.php`

**Endpoints:**
- **GET** `/api/company-quotes.php`
  - Query params: `quotation_id`, `request_id`, `user_id`, `status`
  - Returns quotation list with filters

- **POST** `/api/company-quotes.php`
  - Creates new quotation
  - Validates required fields and business rules
  - Prevents duplicate quotations for same request

- **PUT** `/api/company-quotes.php`
  - Updates existing quotation
  - Only allows updates to pending quotations
  - Full field validation

- **DELETE** `/api/company-quotes.php?quotation_id=X`
  - Deletes quotation
  - Only allows deletion of pending quotations
  - Authorization check

**Features:**
- JSON request/response format
- Comprehensive error handling
- Debug logging for troubleshooting
- CORS headers for cross-origin requests

### 3. **Frontend JavaScript**
**File:** `assets/javascript/company/repair-requests-db.js`

**Key Functions:**

**Data Loading:**
- `loadAvailableRequests()` - Fetch job requests from API
- `loadSubmittedQuotations()` - Fetch company quotations from API

**Rendering:**
- `renderAvailableRequests()` - Display job request cards
- `renderSubmittedQuotations()` - Display quotations in logs tab
- `createRequestCard(request)` - Generate HTML for job card
- `createQuotationLogItem(quotation)` - Generate HTML for quotation item

**CRUD Operations:**
- `openQuotationModal(requestId)` - Open modal for new quotation
- `editQuotation(quotationId)` - Load and edit existing quotation
- `submitQuotation()` - Submit new or update existing quotation
- `deleteQuotation(quotationId)` - Delete pending quotation

**UI Helpers:**
- Tab switching
- View toggle (grid/list)
- Cost calculator with real-time total
- Date validation
- Toast notifications
- Modal management

### 4. **View Updates**
**File:** `views/company/repair-requests.php`

**Changes:**
- Removed all hardcoded mock data (3 job request cards)
- Added loading state for job requests
- Updated script reference to `repair-requests-db.js`
- Made header stats dynamic with IDs
- Kept modal structure intact for quotation submission

## Database Schema

### CompanyQuotation Table
```sql
CREATE TABLE CompanyQuotation (
    quotation_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(250) NOT NULL,
    description VARCHAR(500),
    labor_cost DECIMAL(10,2) NOT NULL,
    material_cost DECIMAL(10,2) NOT NULL,
    transport_cost DECIMAL(10,2) DEFAULT 0.00,
    other_charges DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    start_date DATE NOT NULL,
    completion_date DATE NOT NULL,
    estimated_duration INT NOT NULL,
    payment_terms VARCHAR(100),
    warranty_period VARCHAR(50),
    additional_terms TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'successful') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
);
```

## API Usage Examples

### Create Quotation
```javascript
POST /api/company-quotes.php
Content-Type: application/json

{
    "request_id": 1,
    "user_id": 1,
    "title": "AC Repair Service",
    "description": "Complete AC repair and maintenance",
    "labor_cost": 25000.00,
    "material_cost": 15000.00,
    "transport_cost": 2000.00,
    "other_charges": 1000.00,
    "total_amount": 43000.00,
    "start_date": "2025-11-01",
    "completion_date": "2025-11-05",
    "estimated_duration": 5,
    "payment_terms": "50_50",
    "warranty_period": "6_months",
    "additional_terms": "All materials included"
}
```

### Update Quotation
```javascript
PUT /api/company-quotes.php
Content-Type: application/json

{
    "quotation_id": 1,
    "title": "AC Repair Service - Updated",
    "labor_cost": 30000.00,
    "material_cost": 18000.00,
    "transport_cost": 2500.00,
    "other_charges": 1500.00,
    "total_amount": 52000.00,
    ...
}
```

### Delete Quotation
```javascript
DELETE /api/company-quotes.php?quotation_id=1
```

### Get Quotations
```javascript
// Get all quotations
GET /api/company-quotes.php

// Get by quotation ID
GET /api/company-quotes.php?quotation_id=1

// Get by request ID
GET /api/company-quotes.php?request_id=5

// Get by status
GET /api/company-quotes.php?status=pending
```

## Features Implemented

### ✅ CREATE
- Submit new quotation for job request
- Automatic cost calculation
- Validation for all required fields
- Duplicate prevention (one quotation per request)
- Date validation (start/completion dates)

### ✅ READ
- Load all available job requests from database
- Display job request cards with full details
- Load submitted quotations (pending, accepted)
- Filter quotations by status
- View detailed quotation information

### ✅ UPDATE
- Edit pending quotations only
- Pre-populate form with existing data
- Real-time cost recalculation
- Validation before update
- Prevent editing of accepted/rejected quotations

### ✅ DELETE
- Delete pending quotations only
- Confirmation dialog before deletion
- Prevent deletion of accepted/rejected quotations
- Automatic UI refresh after deletion

## Security Features

1. **SQL Injection Prevention**
   - All queries use prepared statements with PDO
   - Parameter binding for all user inputs

2. **XSS Prevention**
   - HTML escaping in `escapeHtml()` function
   - Content Security Policy headers

3. **Business Logic Validation**
   - Only pending quotations can be edited/deleted
   - Duplicate quotation prevention
   - Cost validation (must be > 0)
   - Date validation (future dates only)

4. **Authorization**
   - User ID validation (TODO: implement session)
   - Quotation ownership verification

## TODO Items

1. **Session Management**
   - Replace hardcoded `currentCompanyId = 1` with actual session
   - Implement proper authentication check

2. **Direct Requests Tab**
   - Implement direct request handling
   - Accept/Reject functionality

3. **Filtering**
   - Implement category filter
   - Implement location filter
   - Implement priority filter
   - Implement search functionality

4. **File Attachments**
   - Display job request photos
   - Upload quotation attachments

5. **Notifications**
   - Notify customer when quotation submitted
   - Notify company when quotation accepted/rejected

## Testing

### Test Create Quotation
1. Navigate to Repair Requests page
2. Click "Submit Quotation" on any job request
3. Fill in all required fields
4. Click submit
5. Verify quotation appears in "Pending Quotations" section

### Test Update Quotation
1. Go to "Request Logs" tab
2. Click "Edit" on pending quotation
3. Modify fields
4. Click submit
5. Verify changes are saved

### Test Delete Quotation
1. Go to "Request Logs" tab
2. Click "Delete" on pending quotation
3. Confirm deletion
4. Verify quotation is removed

## Error Handling

All operations include:
- Try-catch blocks for API calls
- User-friendly error messages
- Console logging for debugging
- Toast notifications for user feedback
- Server-side validation
- Client-side validation

## Browser Console Debugging

Enable debug logging:
```javascript
console.log('Creating quotation:', formData);
console.log('Response:', result);
```

Check PHP error logs:
```bash
tail -f C:\xampp\php\logs\php_error_log
```

## Dependencies

- PHP 7.4+
- MySQL/MariaDB
- PDO extension
- Existing database with JobRequest and User tables
- Font Awesome icons
- Modern browser with ES6 support
