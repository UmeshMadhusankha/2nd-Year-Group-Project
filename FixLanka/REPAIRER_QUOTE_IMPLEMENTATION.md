# Repairer Quote Submission Implementation Summary

## Overview
Implemented a complete quote submission system for repairer actors that matches the database schema and uses dummy data for testing before database integration.

## Database Schema Alignment
The system is designed to work with the `RepairerQuote` table:

```sql
CREATE TABLE RepairerQuote (
    quote_id INT PRIMARY KEY AUTO_INCREMENT,
    request_id INT NOT NULL,
    repairer_id INT NOT NULL,
    quoteAmount DECIMAL(10,2) NOT NULL,
    message TEXT,
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    dateSubmitted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES JobRequest(request_id) ON DELETE CASCADE,
    FOREIGN KEY (repairer_id) REFERENCES Repairer(repairer_id) ON DELETE CASCADE
);
```

## Changes Made

### 1. Fixed Navigation (available-jobs.js)
**File:** `assets/javascript/repairer/available-jobs.js`

- ✅ Updated `submitQuote()` function to use absolute path
- ✅ Updated `submitQuoteFromDetails()` function to use absolute path
- **Before:** `window.location.href = 'submit-quote.php?jobId=${jobId}'`
- **After:** `window.location.href = '/2nd-Year-Group-Project/FixLanka/views/repairer/pages/submit-quote.php?jobId=${jobId}'`

### 2. Updated Form (submit-quote.php)
**File:** `views/repairer/pages/submit-quote.php`

Simplified the form to match database fields:

**Form Fields:**
- `request_id` (hidden) - Links to JobRequest
- `repairer_id` (hidden) - Currently set to dummy value "1"
- `quoteAmount` (number input) - The quote price in LKR
- `message` (textarea) - Detailed quote description
- Terms agreement checkbox

**Removed Fields:**
- ❌ Completion time (days/hours)
- ❌ Quote breakdown calculator
- ❌ Warranty checkbox

**Confirmation Modal:**
- Updated to show only: Quote Amount, Job Title, and Status

### 3. Created API Endpoint (repairer-quotes.php)
**File:** `api/repairer-quotes.php`

A complete RESTful API for managing repairer quotes:

**Features:**
- ✅ **GET** - Retrieve quotes (with filters for repairer_id, request_id, status, quote_id)
- ✅ **POST** - Create new quote (currently in dummy mode)
- ✅ **PUT** - Update existing quote (dummy mode)
- ✅ **DELETE** - Delete quote (dummy mode)

**Dummy Data Mode:**
- Currently returns mock responses without database interaction
- Logs all operations to PHP error log
- Ready to switch to real database (commented code included)

**API Response Example:**
```json
{
    "success": true,
    "message": "Quote submitted successfully (dummy mode)",
    "data": {
        "quote_id": 1234,
        "request_id": 1,
        "repairer_id": 1,
        "quoteAmount": 3500.00,
        "message": "I can fix the kitchen faucet...",
        "status": "pending",
        "dateSubmitted": "2025-10-23 14:30:00"
    }
}
```

### 4. Updated JavaScript (submit-quote.js)
**File:** `assets/javascript/repairer/submit-quote.js`

**Key Changes:**
- ✅ Removed breakdown calculator functionality
- ✅ Updated form validation for `quoteAmount` and `message`
- ✅ Simplified confirmation modal update
- ✅ Integrated with API endpoint via fetch
- ✅ Fixed back button to use absolute path
- ✅ Updated auto-save to work with new fields
- ✅ Added proper error handling

**Form Validation:**
- Quote amount must be greater than 0
- Message must be at least 10 characters
- Terms must be agreed to

**Submission Flow:**
1. User fills form → validates → clicks Submit
2. Confirmation modal shows → user confirms
3. Data sent to API via POST request
4. Success notification shown
5. Redirects to my-jobs page after 2 seconds

## How to Use

### Current Setup (Dummy Mode)
1. Navigate to available jobs page
2. Click "Submit Quote" on any job
3. Fill in:
   - **Quote Amount:** Enter price in LKR
   - **Quote Details:** Describe your approach (min 10 chars)
   - Check terms agreement
4. Click "Submit Quote"
5. Confirm in modal
6. Quote is "submitted" (logged, not saved to DB)

### Testing
- Check browser console for submitted data
- Check PHP error logs for API calls
- LocalStorage used for draft saving

### Switching to Real Database
When ready to connect to the real database:

1. **Ensure database table exists:**
   ```bash
   # Run the create_database.sql or alter_database.sql
   ```

2. **In `api/repairer-quotes.php`:**
   - Uncomment the database code in POST, PUT, DELETE handlers
   - Comment out the dummy response code
   - Test with actual repairer_id from session

3. **Update repairer_id:**
   - In `submit-quote.php`, change hidden field:
   ```php
   <input type="hidden" id="repairer-id" name="repairer_id" 
          value="<?php echo $_SESSION['repairer_id']; ?>">
   ```

4. **Update request_id mapping:**
   - Currently uses jobId from URL as request_id
   - Ensure jobId corresponds to actual JobRequest.request_id

## File Structure
```
FixLanka/
├── api/
│   └── repairer-quotes.php          ← NEW: Quote API endpoint
├── assets/
│   └── javascript/
│       └── repairer/
│           ├── available-jobs.js     ← UPDATED: Fixed navigation
│           └── submit-quote.js       ← UPDATED: New schema integration
└── views/
    └── repairer/
        └── pages/
            └── submit-quote.php      ← UPDATED: Simplified form
```

## API Testing with Postman/cURL

### Submit a Quote (POST)
```bash
curl -X POST http://localhost/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php \
-H "Content-Type: application/json" \
-d '{
  "request_id": 1,
  "repairer_id": 1,
  "quoteAmount": 3500.00,
  "message": "I can complete this job efficiently with quality materials."
}'
```

### Get Quotes (GET)
```bash
# Get all quotes
curl http://localhost/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php

# Get quotes by repairer
curl http://localhost/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?repairer_id=1

# Get quotes by status
curl http://localhost/2nd-Year-Group-Project/FixLanka/api/repairer-quotes.php?status=pending
```

## Next Steps

1. **Test the dummy implementation:**
   - Navigate through the flow
   - Check console logs
   - Verify validation works

2. **Create database tables:**
   - Run SQL scripts to create RepairerQuote table

3. **Enable database mode:**
   - Uncomment database code in API
   - Test with real data

4. **Add session management:**
   - Get repairer_id from session
   - Add authentication checks

5. **Enhance features:**
   - Add file attachments support
   - Add quote expiry handling
   - Add notification system integration

## Notes

- ✅ All paths are now absolute (no relative path issues)
- ✅ Form matches database schema exactly
- ✅ API is RESTful and well-documented
- ✅ Dummy mode allows testing without database
- ✅ Easy to switch to real database
- ✅ Proper error handling implemented
- ✅ Validation on both client and server side (when enabled)

## Troubleshooting

**Issue:** Navigation doesn't work
- **Solution:** Ensure absolute paths start with `/2nd-Year-Group-Project/FixLanka/`

**Issue:** Form doesn't submit
- **Solution:** Check browser console for JavaScript errors
- Verify API endpoint is accessible

**Issue:** API returns 500 error
- **Solution:** Check PHP error logs
- Ensure database.php is properly configured (when not in dummy mode)

**Issue:** Quote amount not saving
- **Solution:** Currently in dummy mode - data is logged, not saved
- Check console and PHP logs for confirmation
