# Project Not Showing in Contract Modal - FIX

## Problem
When clicking "New Contract", the modal shows "No Projects Available" even though there's a project in the database.

## Root Cause
The SQL query in `ContractController.php` was using wrong column names:
- `u.username` - **DOESN'T EXIST** in User table
- `u.phoneNumber` - **DOESN'T EXIST** in User table

**Error from logs:**
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'u.username' in 'field list'
```

## Solution Applied

### File: `controllers/ContractController.php`

**Changed SQL query from:**
```php
SELECT 
    ...
    u.username as customer_name,
    u.email as customer_email,
    u.phoneNumber as customer_phone
FROM Project p
INNER JOIN User u ON p.customer_id = u.user_id
```

**To:**
```php
SELECT 
    ...
    u.f_name,
    u.l_name,
    u.email as customer_email
FROM Project p
INNER JOIN User u ON p.customer_id = u.user_id
```

**Changed data formatting from:**
```php
'client_name' => $project['customer_name'],
'client_phone' => $project['customer_phone'] ?? ''
```

**To:**
```php
'client_name' => trim(($project['f_name'] ?? '') . ' ' . ($project['l_name'] ?? '')),
'client_phone' => '' // Phone not in User table
```

### Additional Debugging Added

Added error logging to help diagnose issues:
```php
error_log("[ContractController] Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
error_log("[ContractController] Session role: " . ($_SESSION['user_role'] ?? 'NOT SET'));
error_log("[ContractController] Company ID from session: " . ($companyId ?? 'NULL'));
error_log("[ContractController] SQL executed with company_id: " . $companyId);
error_log("[ContractController] Found " . count($projects) . " projects");
```

## How to Test

### Option 1: Use the Test Script
1. Open: `http://localhost/2nd-Year-Group-Project/FixLanka/test_projects_query.php`
2. Should show your project(s)

### Option 2: Test in Actual Modal
1. Open: `http://localhost/2nd-Year-Group-Project/FixLanka/views/company/contracts.php`
2. Click "New Contract" button
3. Projects should now appear in Step 1
4. Check browser console (F12) for detailed logs

## Expected Behavior

**Before Fix:**
- ❌ Modal shows "No Projects Available"
- ❌ Console shows SQL error
- ❌ Apache logs show "Unknown column 'u.username'"

**After Fix:**
- ✅ Modal shows project cards
- ✅ Project title: "xxdcfvgbhn"
- ✅ Client name: "umesh yapa" (combined from f_name + l_name)
- ✅ Client email: "umesh@gmail.com"
- ✅ Budget, dates, and description display correctly

## Database Schema Reference

**User Table Columns:**
- ✅ user_id
- ✅ f_name
- ✅ l_name
- ✅ email
- ✅ password
- ✅ profilePicture
- ✅ address
- ✅ district
- ❌ username (DOESN'T EXIST)
- ❌ phoneNumber (DOESN'T EXIST)

**Project Table Columns:**
- ✅ project_id
- ✅ title
- ✅ description
- ✅ project_type
- ✅ location
- ✅ budget
- ✅ start_date
- ✅ end_date
- ✅ status
- ✅ company_id
- ✅ customer_id

## Query Logic

The query finds projects where:
1. ✅ `company_id` matches logged-in company user
2. ✅ Status is 'planned' or 'active'
3. ✅ No contract exists yet for this project
4. ✅ Ordered by start_date DESC (newest first)

## Verification Checklist

- [x] Fixed SQL column names to match actual database
- [x] Updated data formatting to use f_name + l_name
- [x] Added comprehensive error logging
- [x] Created test script for debugging
- [x] Verified User table structure
- [x] Verified Project table structure

## Files Modified

1. **controllers/ContractController.php**
   - Lines 390-395: Fixed SQL SELECT columns
   - Lines 379-382: Added session debugging logs
   - Lines 415-420: Fixed client_name and client_phone formatting

## Next Steps

1. **Refresh your browser** (Ctrl + Shift + R to clear cache)
2. **Open the contracts page**
3. **Click "New Contract"**
4. **Projects should now appear!**

If still not working:
- Check Apache error logs: `C:\xampp\apache\logs\error.log`
- Check browser console for JavaScript errors
- Run test script: `test_projects_query.php`
- Verify you're logged in as user_id 2 with role 'company'

---

**Status:** ✅ FIXED  
**Date:** December 14, 2024  
**Issue:** SQL query using non-existent columns  
**Solution:** Updated to use correct column names from database schema
