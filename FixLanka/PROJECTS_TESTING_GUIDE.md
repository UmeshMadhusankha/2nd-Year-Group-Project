# Projects Page - Quick Testing Guide

## 🧪 How to Test All Functions

### **1. Test Create New Project**

**Steps:**
1. Click the "**Create New Project**" button (top right of page)
2. Modal should open with empty form
3. Fill in all required fields:
   - Title: "Test Kitchen Renovation"
   - Type: Select "Renovation"
   - Location: "Colombo 7"
   - Budget: 250000
   - Start Date: Pick today's date
   - End Date: Pick a date 1 month from now
   - Status: "Planned"
   - Progress: 0
   - Description: "Complete kitchen remodeling project"
4. Click "**Save Project**" button
5. Watch for loader animation
6. Success toast should appear: "Project created successfully"
7. Modal should close automatically
8. **NEW PROJECT should appear in the table/cards**
9. Statistics counters should update (+1 to "Planned")

**Database Verification:**
```sql
SELECT * FROM project WHERE title = 'Test Kitchen Renovation' ORDER BY created_at DESC LIMIT 1;
```

**Expected:**
- New row in database
- `company_id = 2` (your company)
- `is_active = 1`
- All fields populated as entered

---

### **2. Test Edit Project**

**Steps:**
1. Find the project you just created in the list
2. Click the **✏️ edit icon** (second icon in Actions column)
3. Modal should open with **all fields pre-filled**
4. Modal title should say "**Edit Project**"
5. Change some fields:
   - Budget: 300000 (increase)
   - Status: "In Progress"
   - Progress: 25
6. Click "**Save Project**"
7. Success toast: "Project updated successfully"
8. Modal closes
9. **Changes should reflect immediately** in the table
10. Status badge should change color
11. Progress percentage should show 25%

**Database Verification:**
```sql
SELECT * FROM project WHERE title = 'Test Kitchen Renovation';
-- Check: budget = 300000, status = 'in_progress', progress = 25
```

---

### **3. Test View Project Details**

**Steps:**
1. Find any project in the list
2. Click the **👁️ view icon** (first icon in Actions column)
3. **Drawer should slide in from the right**
4. Should show 4 tabs at the top:
   - Overview (active by default)
   - Customer
   - Timeline
   - Financial
5. **Overview Tab** should show:
   - Project title with type badge
   - Status badge (colored)
   - Location with icon
   - 4 info cards: Budget, Progress, Start Date, End Date
   - Description text (if exists)
6. Click "**Customer**" tab
   - Should show customer name with avatar
   - Email address with icon
   - Info message about contact details
7. Click "**Timeline**" tab
   - Should show visual timeline with dots
   - Three dates: Created, Start, End
   - Color-coded indicators
8. Click "**Financial**" tab
   - Should show budget in large text
   - Formatted as "LKR 250,000.00"
   - Info message about milestones
9. Click **X button** or click **outside drawer** to close
10. Drawer should slide out

**What to Check:**
- Currency formatted correctly (commas, LKR symbol)
- Dates formatted as "Dec 26, 2024" format
- Status badges have correct colors
- Tab switching is smooth (no page reload)

---

### **4. Test Delete Project**

**Steps:**
1. Find the test project you created
2. Click the **🗑️ delete icon** (third icon in Actions column)
3. **Confirmation dialog** should appear:
   - "Are you sure you want to delete this project?"
4. Click "**Cancel**" first (test cancellation)
   - Nothing should happen
   - Project still in list
5. Click delete icon again
6. This time click "**OK**"
7. Success toast: "Project deleted successfully"
8. **Project should disappear from list**
9. Statistics counter should update (-1 from its status)

**Database Verification:**
```sql
SELECT * FROM project WHERE title = 'Test Kitchen Renovation';
-- Should show: is_active = 0 (soft deleted, not actually removed)
```

---

### **5. Test Status Update (Quick Action)**

**Steps:**
1. Find any project with status "Planned"
2. Look in the **Status column** (has dropdown)
3. Click the dropdown
4. Select "**In Progress**"
5. Status badge should change color immediately:
   - Was: Light orange background
   - Now: Light teal background
6. Success toast should appear
7. Statistics counters should update:
   - "Planned" count decreases by 1
   - "In Progress" count increases by 1

**Database Verification:**
```sql
SELECT status FROM project WHERE project_id = X;
-- Should show: 'in_progress'
```

---

## 🔍 Browser Developer Tools Checks

### **Network Tab (F12 → Network):**

**When Creating Project:**
```
Request URL: http://localhost/2nd-Year-Group-Project/FixLanka/api/projects.php
Request Method: POST
Status Code: 200 OK
Content-Type: application/json

Request Payload:
{
  "title": "Test Kitchen Renovation",
  "project_type": "Renovation",
  "location": "Colombo 7",
  "budget": 250000,
  ...
}

Response:
{
  "success": true,
  "data": {
    "project_id": 15
  },
  "message": "Project created successfully"
}
```

**When Editing Project:**
```
Request URL: .../api/projects.php?project_id=15
Request Method: PUT
Status Code: 200 OK

Response:
{
  "success": true,
  "message": "Project updated successfully"
}
```

**When Deleting Project:**
```
Request URL: .../api/projects.php?project_id=15
Request Method: DELETE
Status Code: 200 OK

Response:
{
  "success": true,
  "message": "Project deleted successfully"
}
```

### **Console Tab (F12 → Console):**

Should see logs from projects-db.js:
```
Loading projects for company ID: 2
Projects loaded: 5
Statistics loaded: {planned: 2, in_progress: 1, completed: 1, ...}
```

**Should NOT see:**
- Red error messages
- "Uncaught TypeError"
- "Cannot read property of null"
- 404 errors

---

## 🐛 Troubleshooting

### **Problem: Modal doesn't open**
**Check:**
- Console for errors
- Element exists: `document.getElementById('project-modal')`
- CSS file loaded properly
- No JavaScript errors before clicking

**Fix:** Hard refresh (Ctrl+F5)

---

### **Problem: Form submits but nothing happens**
**Check:**
- Network tab for API request
- Console for error messages
- Response from API (should be JSON with success=true)

**Common causes:**
- Database connection issue
- API path incorrect
- Session expired (company_id not found)

**Fix:** Check `api/projects.php` line 1-20 for errors

---

### **Problem: Drawer doesn't show data**
**Check:**
- Console: `projectsData` array has data
- Project exists in array with correct project_id
- Tab content IDs match: `tab-overview`, `tab-customer`, etc.

**Fix:** Reload page to refresh projectsData

---

### **Problem: Database not updating**
**Check:**
- MySQL server running (XAMPP Control Panel)
- Database name correct (`fix_lanka`)
- PDO connection works
- `is_active = 1` in queries

**Verify:**
```sql
-- Check connection
SELECT 1;

-- Check table exists
SHOW TABLES LIKE 'project';

-- Check recent projects
SELECT * FROM project WHERE company_id = 2 ORDER BY created_at DESC LIMIT 5;
```

---

### **Problem: Toast notifications don't appear**
**Check:**
- `showToast()` function exists in projects-db.js
- CSS for `.toast` class exists
- JavaScript not blocked by browser

**Quick test in console:**
```javascript
showToast('Test message', 'success');
```

---

## ✅ Success Indicators

### **Everything is working if you see:**

1. ✅ Modal opens smoothly with backdrop blur
2. ✅ Form fields accept input
3. ✅ Validation works (try submitting empty form)
4. ✅ Loader animation shows during save
5. ✅ Toast notifications appear with correct colors
6. ✅ Modal closes automatically after save
7. ✅ Table/cards update without page refresh
8. ✅ Statistics counters update correctly
9. ✅ Drawer slides in smoothly
10. ✅ All 4 tabs show different content
11. ✅ Currency formatted as "LKR 250,000.00"
12. ✅ Dates formatted as "Dec 26, 2024"
13. ✅ Status badges have correct colors
14. ✅ Edit form pre-fills with existing data
15. ✅ Delete removes project from list
16. ✅ Database records match UI changes

### **Check Database Changes:**

```sql
-- Count active projects
SELECT COUNT(*) FROM project WHERE company_id = 2 AND is_active = 1;
-- Should match total shown in UI

-- Check status distribution
SELECT status, COUNT(*) 
FROM project 
WHERE company_id = 2 AND is_active = 1 
GROUP BY status;
-- Should match statistics cards in UI

-- Check last created project
SELECT * FROM project 
WHERE company_id = 2 
ORDER BY created_at DESC 
LIMIT 1;
-- Should be the last project you created

-- Check last updated project
SELECT * FROM project 
WHERE company_id = 2 
ORDER BY updated_at DESC 
LIMIT 1;
-- Should be the last project you edited
```

---

## 🎯 Quick Test Scenario (5 minutes)

**Complete flow to verify everything works:**

1. **Create** a project named "Quick Test Project"
   - Fill all fields
   - Save
   - ✅ Should appear in list

2. **View** the project details
   - Click view icon
   - Check all 4 tabs
   - ✅ All data should display correctly

3. **Edit** the project
   - Click edit icon
   - Change budget to 500000
   - Change status to "In Progress"
   - Save
   - ✅ Changes should reflect immediately

4. **Update status** via dropdown
   - Change to "Completed"
   - ✅ Badge should turn green

5. **Delete** the project
   - Click delete icon
   - Confirm
   - ✅ Should disappear from list

**If all 5 steps work → Backend integration is PERFECT! ✨**

---

## 📞 Support

If you encounter issues:

1. Check browser console (F12) for error messages
2. Check Network tab for failed API requests
3. Check database for record changes
4. Clear browser cache (Ctrl+Shift+Delete)
5. Restart XAMPP if needed
6. Check PHP error logs: `xampp/apache/logs/error.log`

**Common fixes:**
- Hard refresh: `Ctrl+F5`
- Clear cache: `Ctrl+Shift+R`
- Restart Apache: XAMPP Control Panel → Stop → Start
- Check MySQL: XAMPP Control Panel → MySQL should be running (green)
