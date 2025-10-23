# Company Repair Requests - Testing Guide

## Quick Start

### 1. Database Setup
Make sure your database has the required tables. Run this SQL if needed:

```sql
-- The CompanyQuotation table should already exist from create_database.sql
-- If not, check the table structure in COMPANY_QUOTATION_CRUD.md
```

### 2. Test the API
Open in your browser:
```
http://localhost/2nd-Year-Group-Project/FixLanka/test_company_quotation_api.html
```

This test page allows you to:
- ✅ View all available job requests
- ✅ Create new quotations
- ✅ View all quotations (pending, accepted, etc.)
- ✅ Update existing quotations
- ✅ Delete pending quotations

### 3. Test the Full Interface
Navigate to the repair requests page:
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php
```

## Testing Checklist

### ✅ Test CREATE Operation

1. **Submit a New Quotation:**
   - Go to "Public Requests" tab
   - Click "Submit Quotation" on any job request
   - Fill in all required fields:
     - Quotation Title
     - Service Description
     - Labor Cost
     - Material Cost
     - Transport Cost (optional)
     - Other Charges (optional)
     - Start Date
     - Completion Date
     - Duration
     - Payment Terms
     - Warranty Period
   - Click "Submit Quotation"
   - **Expected Result:** 
     - Success message appears
     - Quotation appears in "Request Logs" → "Pending Quotations"
     - Header stats update

2. **Validation Tests:**
   - Try submitting without required fields → Should show error
   - Try submitting with negative costs → Should show error
   - Try submitting with completion date before start date → Should show error
   - Try submitting duplicate quotation for same request → Should show error

### ✅ Test READ Operation

1. **View Job Requests:**
   - Go to "Public Requests" tab
   - **Expected Result:** 
     - All pending job requests from database display
     - Each card shows: title, category, customer info, district, dates
     - Loading spinner shows while fetching

2. **View Submitted Quotations:**
   - Go to "Request Logs" tab
   - **Expected Result:**
     - Pending quotations appear in "Pending Quotations" section
     - Accepted quotations appear in "Accepted Quotations" section
     - Counts update correctly

3. **Filter Quotations:**
   - Use the test API page
   - Test filtering by:
     - `status=pending`
     - `status=accepted`
     - `quotation_id=X`
     - `request_id=X`

### ✅ Test UPDATE Operation

1. **Edit Pending Quotation:**
   - Go to "Request Logs" tab
   - Find a pending quotation
   - Click "Edit" button
   - **Expected Result:**
     - Modal opens with form pre-filled
     - Modal title shows "Edit Quotation"
     - All fields contain existing data

2. **Update Values:**
   - Change labor cost
   - Change duration
   - Update description
   - Click "Submit Quotation"
   - **Expected Result:**
     - Success message appears
     - Changes are saved
     - Quotation in logs shows updated values

3. **Validation Tests:**
   - Try editing accepted quotation → Should show error
   - Try editing rejected quotation → Should show error
   - Only pending quotations should be editable

### ✅ Test DELETE Operation

1. **Delete Pending Quotation:**
   - Go to "Request Logs" tab
   - Find a pending quotation
   - Click "Delete" button
   - Confirm deletion
   - **Expected Result:**
     - Confirmation dialog appears
     - Quotation is removed from list
     - Success message shows
     - Counts update

2. **Validation Tests:**
   - Try deleting accepted quotation → Should fail
   - Try deleting non-existent quotation → Should show error

### ✅ Test UI Features

1. **Tab Switching:**
   - Switch between "Public Requests", "Direct Requests", "Request Logs"
   - **Expected:** Active tab highlights, content changes

2. **View Toggle:**
   - Click grid/list view buttons
   - **Expected:** Layout changes

3. **Modal Operations:**
   - Click outside modal → Should close
   - Click X button → Should close
   - Click Cancel → Should close
   - ESC key → Should close

4. **Cost Calculator:**
   - Change labor cost
   - Change material cost
   - **Expected:** Total updates automatically

5. **Date Validation:**
   - Select start date
   - **Expected:** Completion date minimum updates

## API Testing with cURL

### Get All Quotations
```bash
curl "http://localhost/2nd-Year-Group-Project/FixLanka/api/company-quotes.php"
```

### Get Pending Quotations
```bash
curl "http://localhost/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?status=pending"
```

### Create Quotation
```bash
curl -X POST "http://localhost/2nd-Year-Group-Project/FixLanka/api/company-quotes.php" \
  -H "Content-Type: application/json" \
  -d '{
    "request_id": 1,
    "user_id": 1,
    "title": "Test Quotation",
    "description": "Test description",
    "labor_cost": 25000,
    "material_cost": 18000,
    "transport_cost": 2500,
    "other_charges": 1500,
    "total_amount": 47000,
    "start_date": "2025-11-01",
    "completion_date": "2025-11-05",
    "estimated_duration": 5,
    "payment_terms": "50_50",
    "warranty_period": "6_months"
  }'
```

### Update Quotation
```bash
curl -X PUT "http://localhost/2nd-Year-Group-Project/FixLanka/api/company-quotes.php" \
  -H "Content-Type: application/json" \
  -d '{
    "quotation_id": 1,
    "title": "Updated Quotation",
    "labor_cost": 30000,
    "material_cost": 20000,
    "transport_cost": 3000,
    "other_charges": 2000,
    "total_amount": 55000,
    "start_date": "2025-11-01",
    "completion_date": "2025-11-06",
    "estimated_duration": 6,
    "payment_terms": "50_50",
    "warranty_period": "1_year"
  }'
```

### Delete Quotation
```bash
curl -X DELETE "http://localhost/2nd-Year-Group-Project/FixLanka/api/company-quotes.php?quotation_id=1"
```

## Browser Console Testing

### Check for Errors
1. Open Developer Tools (F12)
2. Go to Console tab
3. Look for:
   - ✅ Green success messages
   - ❌ Red error messages
   - ℹ️ Debug logs

### Network Tab
1. Open Network tab
2. Filter by "Fetch/XHR"
3. Click on API requests
4. Check:
   - Request headers
   - Request payload
   - Response status (200, 201, 400, 500)
   - Response data

## Common Issues & Solutions

### Issue: "Failed to load jobs"
**Solution:** 
- Check if database.php exists and has correct credentials
- Verify JobRequest table has data
- Check browser console for errors

### Issue: "Failed to create quotation"
**Solution:**
- Verify all required fields are filled
- Check if request_id exists in JobRequest table
- Check if user_id exists in User table
- Verify no duplicate quotation exists

### Issue: "Failed to update quotation"
**Solution:**
- Ensure quotation is in 'pending' status
- Verify quotation_id exists
- Check all required fields are present

### Issue: "Failed to delete quotation"
**Solution:**
- Ensure quotation is in 'pending' status
- Verify quotation_id exists
- Check browser console for actual error

### Issue: Modal doesn't close
**Solution:**
- Check for JavaScript errors in console
- Refresh page and try again

## Database Verification

### Check if quotations were created:
```sql
SELECT * FROM CompanyQuotation ORDER BY created_at DESC;
```

### Check quotation with job details:
```sql
SELECT 
    cq.*,
    jr.title as job_title,
    u.f_name, u.l_name
FROM CompanyQuotation cq
INNER JOIN JobRequest jr ON cq.request_id = jr.request_id
INNER JOIN User u ON cq.user_id = u.user_id
ORDER BY cq.created_at DESC;
```

### Check pending quotations:
```sql
SELECT * FROM CompanyQuotation WHERE status = 'pending';
```

## Performance Testing

### Load Testing
1. Create 10 quotations
2. Check page load time
3. Check API response time
4. Verify UI remains responsive

### Stress Testing
1. Rapidly create/update/delete quotations
2. Check for race conditions
3. Verify data consistency

## Security Testing

### SQL Injection
✅ All queries use prepared statements with PDO

### XSS Prevention
✅ All output uses `escapeHtml()` function

### Authorization
⚠️ TODO: Implement session-based user verification

## Next Steps

After testing, consider:
1. Implement session management
2. Add file upload for quotation attachments
3. Add email notifications
4. Implement quotation versioning
5. Add quotation templates
6. Implement quotation approval workflow

## Support

If you encounter issues:
1. Check PHP error log: `C:\xampp\php\logs\php_error_log`
2. Check MySQL error log: `C:\xampp\mysql\data\*.err`
3. Check browser console for JavaScript errors
4. Verify database credentials in `config/database.php`
