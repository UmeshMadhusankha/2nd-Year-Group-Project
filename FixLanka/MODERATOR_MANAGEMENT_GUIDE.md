# Moderator Management System - Complete Workflow

## 📁 Files Created

### 1. **Controller** (`controllers/ModeratorController.php`)

- Handles all database operations (CRUD)
- Methods:
  - `getAllModerators()` - Fetch all moderators from database
  - `addModerator()` - Insert new moderator with password hashing
  - `updateModerator()` - Update moderator details (optional password change)
  - `deleteModerator()` - Remove moderator from database
- Includes validation and error handling
- Returns JSON responses

### 2. **API Endpoint** (`api/moderators.php`)

- Acts as a router for AJAX requests
- Accepts `action` parameter (getAll, add, update, delete)
- Calls appropriate controller methods
- Returns JSON responses to frontend

### 3. **View** (`views/admin/moderators.php`)

- User interface for moderator management
- Uses AJAX to communicate with API
- No direct database operations in view
- Real-time updates without page refresh

### 4. **CSS** (`assets/css/admin/moderators.css`)

- Styling for table, modals, buttons, and forms

## 🔄 Complete Workflow

### **Loading Moderators (Page Load)**

```
Browser → moderators.php (View)
   ↓
JavaScript: loadModeratorsFromAPI()
   ↓
AJAX GET → api/moderators.php?action=getAll
   ↓
ModeratorController::getAllModerators()
   ↓
SELECT query to database
   ↓
JSON response → Browser
   ↓
Render table with data
```

### **Adding a Moderator**

```
User clicks "Add Moderator" button
   ↓
Modal opens with form
   ↓
User fills: username, email, password, section
   ↓
User clicks "Save Moderator"
   ↓
AJAX POST → api/moderators.php (action=add)
   ↓
ModeratorController::addModerator()
   ↓
Validation checks (required fields, email format, duplicates)
   ↓
Password hashing: password_hash($password, PASSWORD_DEFAULT)
   ↓
INSERT query to database
   ↓
JSON response → Browser
   ↓
Success message shown
   ↓
Reload moderators list
   ↓
Modal closes
```

### **Editing a Moderator**

```
User clicks edit icon on table row
   ↓
Modal opens with pre-filled data
   ↓
User updates: email, password (optional), section
   ↓
User clicks "Save Moderator"
   ↓
AJAX POST → api/moderators.php (action=update)
   ↓
ModeratorController::updateModerator()
   ↓
Validation checks
   ↓
If password provided: hash and update
If password empty: update only email and section
   ↓
UPDATE query to database
   ↓
JSON response → Browser
   ↓
Success message shown
   ↓
Reload moderators list
   ↓
Modal closes
```

### **Deleting a Moderator**

```
User clicks delete icon on table row
   ↓
Confirmation modal opens
   ↓
User clicks "Delete"
   ↓
AJAX POST → api/moderators.php (action=delete)
   ↓
ModeratorController::deleteModerator()
   ↓
DELETE query to database
   ↓
JSON response → Browser
   ↓
Success message shown
   ↓
Reload moderators list
   ↓
Modal closes
```

## 🔐 Security Features

1. **Password Hashing**: All passwords hashed with bcrypt (PASSWORD_DEFAULT)
2. **Prepared Statements**: All SQL queries use prepared statements (prevents SQL injection)
3. **XSS Prevention**: HTML escaping in JavaScript (escapeHtml function)
4. **Validation**: Server-side validation for all inputs
5. **Duplicate Checks**: Username and email uniqueness enforced

## 🎯 Testing Steps

### Test 1: Load Page

1. Navigate to: `/2nd-Year-Group-Project/FixLanka/admin-moderators`
2. ✅ Page should load without errors
3. ✅ Table should display existing moderators from database
4. ✅ If no moderators: "No moderators found" message

### Test 2: Add Moderator

1. Click "Add Moderator" button
2. ✅ Modal should open
3. Fill form:
   - Username: `test_mod`
   - Email: `test@example.com`
   - Password: `test123456`
   - Section: `Advertisement Review`
4. Click "Save Moderator"
5. ✅ Success message appears
6. ✅ New moderator appears in table
7. ✅ Password is hashed in database (check phpMyAdmin)

### Test 3: Edit Moderator

1. Click pencil icon on any moderator row
2. ✅ Modal opens with pre-filled data
3. ✅ Username field is disabled (read-only)
4. Change email to: `updated@example.com`
5. Leave password empty
6. Click "Save Moderator"
7. ✅ Success message appears
8. ✅ Email updated in table
9. ✅ Password unchanged in database

### Test 4: Change Password

1. Click edit on moderator
2. Enter new password: `newpassword123`
3. Click "Save Moderator"
4. ✅ Success message appears
5. ✅ Password updated and hashed in database

### Test 5: Delete Moderator

1. Click trash icon on any moderator row
2. ✅ Confirmation modal opens
3. Click "Delete"
4. ✅ Success message appears
5. ✅ Moderator removed from table
6. ✅ Moderator deleted from database

### Test 6: Search & Filter

1. Type in search box
2. ✅ Table filters in real-time
3. Select section from dropdown
4. ✅ Table filters by section

### Test 7: Validation

1. Try adding moderator with empty fields
2. ✅ Error: "All fields are required"
3. Try password less than 6 characters
4. ✅ Error: "Password must be at least 6 characters"
5. Try duplicate username
6. ✅ Error: "Username already exists"
7. Try duplicate email
8. ✅ Error: "Email already exists"

## 🐛 Troubleshooting

### Issue: "Failed to load moderators"

**Solution**: Check browser console for errors. Verify:

- API file exists at `/api/moderators.php`
- Database connection working
- `fix_lanka` database exists
- `Moderator` table exists

### Issue: Modal doesn't open

**Solution**: Check browser console. Verify:

- Lucide icons loaded
- No JavaScript errors
- CSS file loaded correctly

### Issue: Password not hashing

**Solution**: Check:

- PHP version >= 5.5 (password_hash function)
- Controller properly included in API
- POST data reaching controller

### Issue: "Call to undefined function password_hash"

**Solution**: Update PHP to version 5.5 or higher

## 📊 Database Schema Reference

```sql
CREATE TABLE Moderator (
    moderator_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    assigned_section VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔗 URL Structure

- **Page**: `/2nd-Year-Group-Project/FixLanka/admin-moderators`
- **API**: `/2nd-Year-Group-Project/FixLanka/api/moderators.php`
- **Actions**: `?action=getAll`, `action=add`, `action=update`, `action=delete`

## ✅ Success Indicators

1. ✅ Page loads without PHP errors
2. ✅ Moderators from database display in table
3. ✅ Add button opens modal
4. ✅ Form submissions work without page refresh
5. ✅ Success/error messages appear
6. ✅ Table updates in real-time
7. ✅ Passwords are hashed (not plaintext in database)
8. ✅ Search and filter work correctly
9. ✅ Edit preserves password when field is empty
10. ✅ Delete confirmation works

---

**Created for**: FixLanka Admin Dashboard  
**Date**: October 23, 2025  
**Architecture**: MVC Pattern with AJAX
