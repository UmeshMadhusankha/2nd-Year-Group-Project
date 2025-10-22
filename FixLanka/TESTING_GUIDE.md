# Multi-Role Registration System - Testing Guide

## Implementation Summary

Successfully implemented a multi-role registration system for FixLanka with support for three user types:

1. **User** - Regular customers posting jobs
2. **Repairer** - Service providers accepting jobs
3. **Company** - Business entities managing teams

---

## What Was Implemented

### ✅ Frontend (UI & UX)

**1. views/auth/signup.php**

- Added role selection buttons (User, Repairer, Company)
- Created three distinct forms with proper field validation
- User form: f_name, l_name, email, password, address
- Repairer form: f_name, l_name, email, phoneNumber, password, category_id, districts (multi-select), about, profilePicture (file upload)
- Company form: name, business_type (multi-select), registration_no, tax_id, address, email, website, contact_no, districts (multi-select), password, description
- Each form has hidden input `user_type` to identify registration type

**2. assets/css/auth/signup.css**

- Added `.role-buttons` styling with flex layout
- Styled `.role-btn` with hover effects and `.active` state
- Form visibility control: `.signup-form { display: none }` / `.signup-form.active { display: block }`
- Checkbox group styling for districts and business types
- Responsive design for mobile devices

**3. assets/javascript/auth/signup.js**

- Role button click handlers to switch between forms
- Form validation before submission:
  - Password match validation
  - Password length check (minimum 6 characters)
  - Districts validation for repairer/company
  - Business type validation for company
  - File upload validation (type, size) for repairer
- Real-time password match indicator
- Password strength indicator

---

### ✅ Backend (Controllers & Logic)

**4. controllers/AuthController.php**

**Main Changes:**

- Refactored `register()` method to route based on `user_type`
- Extracted existing User registration logic into `registerUser()` method
- Added `registerRepairer()` method
- Added `registerCompany()` method
- Added `handleFileUpload()` helper method

**registerUser():**

- Validates: f_name, l_name, email, password, confirm_password, address
- Checks email uniqueness in User table
- Hashes password with PASSWORD_DEFAULT
- Inserts into User table
- Sets session variables: user_id, user_name, user_email, user_role='user'
- Redirects to: `/2nd-Year-Group-Project/FixLanka/` (home)

**registerRepairer():**

- Validates: f_name, l_name, email, phoneNumber, password, category_id, districts[], about
- Checks email uniqueness in Repairer table
- Handles profile picture upload (optional, max 5MB, JPG/PNG/GIF/WEBP)
- Converts districts array to CSV: `implode(',', $districts)`
- Hashes password
- Inserts into Repairer table with availability='available'
- Sets session with user_role='repairer'
- Redirects to: `/2nd-Year-Group-Project/FixLanka/repairer-dashboard`

**registerCompany():**

- Validates: name, business_type[], registration_no, tax_id, address, email, contact_no, districts[], password, description
- Checks email uniqueness AND registration_no uniqueness in Company table
- Converts business_type and districts arrays to CSV
- Hashes password
- Inserts into Company table
- Sets session with user_role='company'
- Redirects to: `/2nd-Year-Group-Project/FixLanka/company-dashboard`

**handleFileUpload($file, $userType):**

- Validates file type: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp']
- Validates file size: Max 5MB
- Creates directory: `assets/uploads/profiles/{userType}/` (with 0777 permissions)
- Generates unique filename: `uniqid() . '_' . time() . '.extension`
- Moves uploaded file
- Returns relative path for database storage

---

### ✅ Routing & Mock Pages

**5. index.php**

- Added route: `/repairer-dashboard` → views/repairer/dashboard.php
- Added route: `/company-dashboard` → views/company/dashboard.php

**6. views/repairer/dashboard.php**

- Mock dashboard with "Coming Soon" content
- Displays success message from session
- Lists planned features: Job Management, Profile, Earnings, Reviews, Support
- "Back to Home" button

**7. views/company/dashboard.php**

- Mock dashboard with "Coming Soon" content
- Displays success message from session
- Lists planned features: Team Management, Job Assignments, Analytics, Revenue Reports, Client Management, Marketing
- "Back to Home" button

---

## Database Schema Reference

### User Table

```sql
user_id (PK), f_name, l_name, email (UNIQUE), password, profilePicture, address, district, created_at, updated_at
```

### Repairer Table

```sql
repairer_id (PK), f_name, l_name, email (UNIQUE), password, phoneNumber, about, profilePicture,
ratings, completedJobsCount, districts (TEXT - CSV), availability ENUM('available','busy','unavailable'),
dateJoined, category_id (FK)
```

### Company Table

```sql
company_id (PK), name, business_type (VARCHAR 255 - CSV), registration_no (UNIQUE), tax_id,
address, email (UNIQUE), website, contact_no, districts (TEXT - CSV), password, description,
rating, date_of_joined
```

---

## Testing Checklist

### Prerequisites

1. ✅ Start XAMPP Control Panel
2. ✅ Start Apache server
3. ✅ Start MySQL server
4. ✅ Verify database `fix_lanka` exists with all tables

### Test 1: User Registration

**URL:** http://localhost/2nd-Year-Group-Project/FixLanka/signup

**Steps:**

1. Verify page loads with three role buttons (User is active by default)
2. Verify User form is visible, others are hidden
3. Fill in User form:
   - First Name: John
   - Last Name: Doe
   - Email: john.doe@example.com
   - Address: 123 Main St, Colombo
   - Password: password123
   - Confirm Password: password123
   - Check "I agree to Terms & Conditions"
4. Click "Create User Account"
5. **Expected Result:**
   - Redirects to home page (/)
   - Session success message displayed
   - User logged in
   - Check database: User table has new entry with hashed password

**Validation Tests:**

- Empty fields → "All required fields must be filled"
- Invalid email → "Invalid email format"
- Password < 6 chars → "Password must be at least 6 characters long"
- Password mismatch → "Passwords do not match"
- Duplicate email → "Email already registered"

---

### Test 2: Repairer Registration

**Steps:**

1. Go to /signup
2. Click "Repairer" button
3. Verify Repairer form appears, others hidden
4. Fill in form:
   - First Name: Mike
   - Last Name: Smith
   - Email: mike.smith@example.com
   - Phone: 0771234567
   - Password: password123
   - Confirm Password: password123
   - Category: Plumbing (or any other)
   - Districts: Check Colombo, Gampaha, Kandy
   - About: "10 years experience in plumbing services"
   - Profile Picture: Upload a JPG/PNG (optional, < 5MB)
   - Check terms
5. Click "Create Repairer Account"
6. **Expected Result:**
   - Redirects to /repairer-dashboard
   - Mock dashboard displays "Welcome, Repairer!"
   - Success message shown
   - Check database: Repairer table has new entry
   - districts field = "Colombo,Gampaha,Kandy"
   - availability = "available"
   - If file uploaded: profilePicture path saved, file exists in assets/uploads/profiles/repairers/

**Validation Tests:**

- No districts selected → "Please select at least one service district"
- File > 5MB → "Failed to upload profile picture"
- Invalid file type (PDF, DOCX) → "Failed to upload profile picture"

---

### Test 3: Company Registration

**Steps:**

1. Go to /signup
2. Click "Company" button
3. Verify Company form appears
4. Fill in form:
   - Company Name: ABC Services Ltd
   - Business Type: Check Plumbing, Electrical, HVAC
   - Registration No: REG123456
   - Tax ID: TAX789012 (optional)
   - Email: info@abcservices.com
   - Website: https://abcservices.com (optional)
   - Address: 456 Business Ave, Colombo 3
   - Contact Number: 0112345678
   - Districts: Check Colombo, Gampaha, Kalutara, Kandy
   - Password: password123
   - Confirm Password: password123
   - Description: "Full-service home repair company with certified technicians"
   - Check terms
5. Click "Create Company Account"
6. **Expected Result:**
   - Redirects to /company-dashboard
   - Mock dashboard displays "Welcome, Company!"
   - Success message shown
   - Check database: Company table has new entry
   - business_type = "Plumbing,Electrical,HVAC"
   - districts = "Colombo,Gampaha,Kalutara,Kandy"

**Validation Tests:**

- No business type selected → "Please select at least one business type"
- No districts selected → "Please select at least one service district"
- Duplicate registration_no → "Registration number already exists"
- Duplicate email → "Email already registered"

---

### Test 4: Form Switching & UI

**Steps:**

1. Go to /signup
2. Click "User" button → Verify User form shows, others hidden, User button has active class
3. Click "Repairer" button → Verify Repairer form shows, others hidden, Repairer button has active class
4. Click "Company" button → Verify Company form shows, others hidden, Company button has active class
5. Fill partial data in one form, switch to another → Verify data doesn't carry over
6. Test responsive design (resize browser to mobile width) → Verify buttons stack vertically

---

### Test 5: JavaScript Validation

**Steps:**

1. Open browser console (F12)
2. Go to /signup
3. Fill User form with mismatched passwords → Submit
   - **Expected:** Alert "Passwords do not match!", form not submitted
4. Fill Repairer form, don't select districts → Submit
   - **Expected:** Alert "Please select at least one service district"
5. Fill Company form, don't select business types → Submit
   - **Expected:** Alert "Please select at least one business type"
6. Upload file > 5MB in Repairer form → Submit
   - **Expected:** Alert "Profile picture must be less than 5MB"
7. Upload PDF in Repairer form → Submit
   - **Expected:** Alert "Only JPG, PNG, GIF, and WEBP images are allowed"
8. Check console for JavaScript errors → Should be none

---

### Test 6: Password Strength Indicator

**Steps:**

1. Go to /signup
2. Click in any password field
3. Type 1 character → Verify hint shows "Too short" in red
4. Type 6 characters → Verify hint shows "Good" in orange
5. Type 8+ characters → Verify hint shows "Strong" in green
6. In confirm password field, type mismatched password → Border turns red
7. Match the password → Border turns green

---

### Test 7: Session & Redirect Flow

**Steps:**

1. Register as User → Check session variables:
   - user_id, user_name, user_email, user_role='user'
   - Redirected to: /
2. Logout, register as Repairer → Check session:
   - user_id, user_name, user_email, user_role='repairer'
   - Redirected to: /repairer-dashboard
3. Logout, register as Company → Check session:
   - user_id, user_name, user_email, user_role='company'
   - Redirected to: /company-dashboard

---

### Test 8: File Upload Directory Structure

**Steps:**

1. Register Repairer with profile picture
2. Check directory exists: `c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\assets\uploads\profiles\repairers\`
3. Verify file saved with unique name: `uniqid_timestamp.ext`
4. Verify database has path: `/2nd-Year-Group-Project/FixLanka/assets/uploads/profiles/repairers/{filename}`
5. Access file via browser: http://localhost/2nd-Year-Group-Project/FixLanka/assets/uploads/profiles/repairers/{filename}
6. Verify image displays correctly

---

### Test 9: Database Integrity

**Steps:**

1. Register all three types
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Select database `fix_lanka`
4. Check User table:
   - Verify password is hashed (60 characters, starts with $2y$)
   - Verify created_at timestamp
5. Check Repairer table:
   - Verify districts is CSV string
   - Verify availability = 'available'
   - Verify category_id matches selected dropdown
   - Verify profilePicture path (or NULL if not uploaded)
6. Check Company table:
   - Verify business_type is CSV string
   - Verify districts is CSV string
   - Verify registration_no is unique
   - Verify date_of_joined timestamp

---

### Test 10: Error Handling

**Steps:**

1. Stop MySQL in XAMPP
2. Try to register → Verify graceful error: "Registration failed. Please try again."
3. Start MySQL
4. Go to AuthController.php, temporarily break SQL query
5. Try to register → Verify error logged and user-friendly message shown
6. Check XAMPP logs: `c:\xampp\apache\logs\error.log`
7. Restore AuthController.php

---

## Known Limitations & Future Enhancements

### Current Implementation:

- ✅ Basic registration for all three user types
- ✅ Session management
- ✅ File upload for repairers
- ✅ Multi-select districts and business types
- ✅ Client-side and server-side validation
- ✅ Mock dashboard pages

### Not Yet Implemented:

- ❌ Email verification
- ❌ Password reset functionality
- ❌ Profile editing after registration
- ❌ Image cropping/resizing
- ❌ Company logo upload
- ❌ Advanced password strength requirements (special chars, uppercase, etc.)
- ❌ CSRF token protection
- ❌ Rate limiting for registration attempts
- ❌ Real repairer and company dashboards

---

## Troubleshooting

### Issue: CSS not loading

**Solution:** Verify absolute path in signup.php line 22:

```php
<link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/auth/signup.css">
```

### Issue: JavaScript not working

**Solution:** Check browser console (F12) for errors. Verify script path in signup.php line 93:

```php
<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/auth/signup.js"></script>
```

### Issue: File upload fails

**Solution:**

1. Check directory exists: `assets/uploads/profiles/repairers/`
2. Check directory permissions: 0777
3. Check php.ini settings:
   - `upload_max_filesize = 10M`
   - `post_max_size = 10M`
4. Check `$_FILES['profilePicture']['error']` value

### Issue: Form doesn't submit

**Solution:**

1. Check browser console for JavaScript errors
2. Verify form action: `/2nd-Year-Group-Project/FixLanka/register`
3. Check index.php has `/register` route
4. Verify AuthController.php register() method exists

### Issue: Redirects to wrong page

**Solution:**

1. Check session role value: `$_SESSION['user_role']`
2. Verify redirect URLs in AuthController:
   - User: `/2nd-Year-Group-Project/FixLanka/`
   - Repairer: `/2nd-Year-Group-Project/FixLanka/repairer-dashboard`
   - Company: `/2nd-Year-Group-Project/FixLanka/company-dashboard`

### Issue: Districts/business types not saving

**Solution:**

1. Check POST data: `print_r($_POST['districts']);`
2. Verify `implode(',', $districts)` is used before DB insert
3. Check database column type: TEXT (not VARCHAR with small length)

---

## File Modifications Summary

**Modified Files:**

1. `views/auth/signup.php` - Added role buttons and three forms
2. `assets/css/auth/signup.css` - Added role button and form visibility styles
3. `assets/javascript/auth/signup.js` - Completely refactored for multi-role support
4. `controllers/AuthController.php` - Refactored register() + added 3 new methods
5. `index.php` - Added 2 new routes

**Created Files:**

1. `views/repairer/dashboard.php` - Mock dashboard
2. `views/company/dashboard.php` - Mock dashboard
3. `TESTING_GUIDE.md` - This file

**Auto-Created (on first upload):**

- `assets/uploads/profiles/repairers/` - Directory for repairer profile pictures

---

## Next Steps (For Future Development)

1. **Login Enhancement:**

   - Modify login to check all three tables (User, Repairer, Company)
   - Route to appropriate dashboard based on role

2. **Dashboard Development:**

   - Build real repairer dashboard with job listings
   - Build real company dashboard with team management

3. **Security Hardening:**

   - Add CSRF tokens to all forms
   - Implement rate limiting
   - Add email verification
   - Enhance password requirements

4. **Profile Management:**

   - Allow users to edit their profiles
   - Allow repairers to update availability
   - Allow companies to manage team members

5. **Image Processing:**
   - Add image resizing/cropping
   - Generate thumbnails
   - Optimize images for web

---

## Contact & Support

For issues or questions during testing, check:

1. Browser console (F12) for JavaScript errors
2. XAMPP error logs: `c:\xampp\apache\logs\error.log`
3. PHP error_log entries in AuthController.php

Happy Testing! 🎉
