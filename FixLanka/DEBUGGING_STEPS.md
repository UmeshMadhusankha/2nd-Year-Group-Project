# Notification CRUD Debugging Guide

## 🚨 Problems Identified & Fixed

### **Problem 1: Conflicting JavaScript Files** ✅ FIXED

**Issue:** Both `notifications.js` (old mock data system) and `crud.js` (new CRUD system) were running simultaneously, causing conflicts.

**Solution:** Disabled `notifications.js` in `views/moderator/notifications.php`

```javascript
// OLD - DISABLED:
<script src=".../notifications.js"></script>

// NEW - ACTIVE:
<script src=".../crud.js"></script>
```

---

### **Problem 2: Missing CSS File** ✅ FIXED

**Issue:** `modals.css` doesn't exist, causing 404 error

**Solution:** Removed non-existent CSS link from `notifications.php`

```html
<!-- REMOVED: -->
<link rel="stylesheet" href=".../modals.css" />

<!-- USING: -->
<link rel="stylesheet" href=".../moderators.css" />
```

---

### **Problem 3: API 500 Error** 🔍 NEEDS TESTING

**Issue:** Server-side error when fetching/creating notifications

**Possible Causes:**

1. Database table `Notification` doesn't exist
2. Database connection issue
3. PHP error in Controller/Model

**Solution:** Added comprehensive debugging logs throughout the stack

---

## 🧪 TESTING STEPS

### **Step 1: Verify Database Table Exists**

Run the database test script:

```
http://localhost/2nd-Year-Group-Project/FixLanka/test_notification_db.php
```

**Expected Output:**

```
=== Notification Database Test ===
1. Checking if Notification table exists...
   ✓ Notification table EXISTS
2. Table structure: [shows columns]
3. ✓ Found X notification(s)
=== All Tests Passed! ===
```

**If table doesn't exist:**

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database `fix_lanka`
3. Import `create_database.sql` OR run this SQL:

```sql
CREATE TABLE Notification (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    send_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    recipient_type ENUM('all', 'user', 'repairer', 'company') DEFAULT 'all',
    status ENUM('sent', 'pending', 'failed') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### **Step 2: Check PHP Error Logs**

Open PHP error log:

```
C:\xampp\apache\logs\error.log
```

Clear the log (optional):

1. Stop Apache
2. Delete `error.log` file
3. Start Apache

Now test the page and watch for new errors.

---

### **Step 3: Test CREATE Operation with Console Debugging**

1. Open notifications page: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/notifications.php`

2. **Open Browser Console** (F12 → Console tab)

3. Fill in the form:

   - Recipients: "All Users"
   - Title: "Test Notification"
   - Message: "This is a test message"

4. Click "Send Now"

5. **Check Console Output:**

You should see these logs in order:

```
[CRUD] Initializing notification CRUD module...
[CRUD] API URL: /2nd-Year-Group-Project/FixLanka/api/notifications.php
[CRUD] Setting up notification form submit handler
[CRUD] Form submitted - CREATE operation started
[CRUD] Form data: {title: "Test Notification", message: "This is a test message", recipients: "all", action: "add"}
[CRUD] Fetching API: /2nd-Year-Group-Project/FixLanka/api/notifications.php
[CRUD] API Response status: 200
[CRUD] API Response data: {success: true, message: "Notification sent successfully", data: {...}}
```

6. **Check PHP Error Log:**

You should see:

```
[API] Notification API called
[API] REQUEST_METHOD: POST
[API] Action: add
[CONTROLLER] addNotification called
[MODEL] createNotification - Title: Test Notification, Recipient: all, Status: sent
[MODEL] Notification created successfully with ID: 1
```

---

### **Step 4: Test READ Operation**

After creating a notification:

**Console should show:**

```
[CRUD] Loading notifications from API...
[CRUD] API URL: /2nd-Year-Group-Project/FixLanka/api/notifications.php?action=getRecent&limit=5
[CRUD] Load response status: 200
[CRUD] Load response data: {success: true, data: [{...}]}
[CRUD] Loaded notifications: [{notification_id: 1, title: "Test Notification", ...}]
```

**PHP log should show:**

```
[API] Action: getRecent
[CONTROLLER] getRecentNotifications called
[CONTROLLER] Fetching 5 notifications from model
[MODEL] getRecentNotifications called with limit: 5
[MODEL] Fetched 1 notifications from database
[CONTROLLER] Fetched 1 notifications
```

**Page should display:**

- Notification card with your test message
- Edit and Delete buttons visible
- Recipient badge showing "all"
- Status badge showing "sent"
- Timestamp

---

### **Step 5: Test UPDATE Operation**

1. Click "Edit" button on a notification
2. Modal should open with pre-filled data
3. Change title to "Updated Notification"
4. Click "Update Notification"

**Console:**

```
[CRUD] Edit notification clicked: ID 1
[CRUD] Update form submitted
[CRUD] API Response: {success: true, message: "Notification updated successfully"}
```

**PHP log:**

```
[API] Action: update
[CONTROLLER] UPDATE Notification - ID: 1, Title: Updated Notification
[MODEL] updateNotification called
```

---

### **Step 6: Test DELETE Operation**

1. Click "Delete" button
2. Confirmation modal appears
3. Click "Delete"

**Console:**

```
[CRUD] Delete notification: ID 1
[CRUD] API Response: {success: true, message: "Notification deleted successfully"}
```

**PHP log:**

```
[API] Action: delete
[CONTROLLER] DELETE Notification - ID: 1
[MODEL] deleteNotification called
```

---

## 🐛 COMMON ERRORS & SOLUTIONS

### **Error: "Failed to load resource: 500 Internal Server Error"**

**Cause:** PHP error in backend

**Fix:**

1. Check `C:\xampp\apache\logs\error.log`
2. Look for PHP errors (syntax, undefined functions, etc.)
3. Common issues:
   - Database table doesn't exist → Run SQL to create table
   - Database connection failed → Check XAMPP MySQL is running
   - Typo in file paths → Check `require_once` paths

---

### **Error: "Cannot read properties of null"**

**Cause:** JavaScript trying to access element that doesn't exist

**Fix:**

1. Check console for which element is null
2. Verify element ID exists in HTML
3. Ensure scripts load after DOM elements

---

### **Error: "Notification not found" or empty list**

**Cause:** Database table empty or query failing

**Fix:**

1. Run database test script
2. Check if notifications exist: `SELECT * FROM Notification;`
3. Verify table structure matches model queries

---

### **Error: Form submits but data disappears**

**Cause:** Old JavaScript (`notifications.js`) still intercepting form

**Fix:**

1. Verify `notifications.js` is commented out in view file
2. Clear browser cache (Ctrl+F5)
3. Check Network tab for actual API call

---

## 📊 VERIFICATION QUERIES

Run these in phpMyAdmin to verify operations:

```sql
-- Check all notifications
SELECT * FROM Notification ORDER BY send_date DESC;

-- Count by status
SELECT status, COUNT(*) as count FROM Notification GROUP BY status;

-- Count by recipient
SELECT recipient_type, COUNT(*) as count FROM Notification GROUP BY recipient_type;

-- Recent notifications (what API returns)
SELECT notification_id, title, message, send_date, recipient_type, status
FROM Notification
ORDER BY send_date DESC
LIMIT 5;

-- Check specific notification
SELECT * FROM Notification WHERE notification_id = 1;
```

---

## ✅ SUCCESS CRITERIA

After all tests, you should have:

- ✅ No 404 errors in browser console
- ✅ No 500 errors from API
- ✅ Console shows detailed debug logs
- ✅ PHP error log shows operation flow
- ✅ CREATE: New notification appears in database
- ✅ READ: Notifications display on page with edit/delete buttons
- ✅ UPDATE: Changes persist in database
- ✅ DELETE: Notification removed from database
- ✅ Success messages appear after each operation

---

## 📞 NEXT STEPS IF STILL NOT WORKING

1. **Share Console Output:** Copy entire console log and share
2. **Share PHP Error Log:** Copy relevant errors from `error.log`
3. **Share Database Test Result:** Output from `test_notification_db.php`
4. **Share Network Tab:** Show failed API requests (Status, Response)

With these logs, I can pinpoint the exact issue!
