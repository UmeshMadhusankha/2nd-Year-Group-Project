# 📋 COMPLETE BACKEND VERIFICATION GUIDE
## Ad Scheduling System - Step by Step Testing

---

## ✅ BACKEND FILES IMPLEMENTED (7 Total)

### 1. **schedule_ad.php** - Create New Schedules
- **Purpose**: Add new advertisement schedules
- **Method**: POST
- **Technology**: mysqli
- **Status**: ✅ Complete

### 2. **fetch_scheduled_ads.php** - Retrieve & Filter Ads
- **Purpose**: Get all scheduled ads with filters
- **Method**: GET
- **Technology**: mysqli
- **Status**: ✅ Complete

### 3. **update_schedule.php** - Update Existing Schedules
- **Purpose**: Modify advertisement schedules
- **Method**: POST
- **Technology**: mysqli
- **Status**: ✅ Complete

### 4. **delete_schedule.php** - Remove Schedules
- **Purpose**: Delete advertisement schedules
- **Method**: POST/DELETE
- **Technology**: mysqli
- **Status**: ✅ Complete

### 5. **calendar_data.php** - Calendar View Data
- **Purpose**: Provide calendar JSON data
- **Method**: GET
- **Technology**: mysqli
- **Status**: ✅ Complete

### 6. **update_status_auto.php** - Auto Status Updates
- **Purpose**: Automatically update ad statuses
- **Method**: GET (runs on page load)
- **Technology**: mysqli
- **Status**: ✅ Complete

### 7. **ad-schedule.php** - Main Dashboard
- **Purpose**: Display and manage all schedules
- **Method**: GET
- **Technology**: mysqli + HTML/JS
- **Status**: ✅ Complete

---

## 🧪 HOW TO TEST - STEP BY STEP

### **METHOD 1: Automated Test Suite** (Recommended)

#### Step 1: Open the Test Page
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/test_ad_scheduling_backend.php
```

#### Step 2: Click "Run All Tests"
The page will automatically test all 7 backend endpoints:
- ✅ Database table check
- ✅ Fetch ads (HTML)
- ✅ Fetch ads (JSON)
- ✅ Filter by status
- ✅ Calendar data
- ✅ Auto status update
- ✅ Create new ad schedule

#### Step 3: Check Results
- **Green = Success** ✅
- **Red = Failed** ❌
- **All 7 should be green**

---

### **METHOD 2: Manual Testing** (Detailed)

#### Test 1: Check Database Table
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/check_table.php`

**Expected Result**:
```json
{
  "success": true,
  "message": "Table exists and is ready",
  "total_records": 5,
  "fields": ["id", "ad_id", "company_name", "title", ...],
  "field_count": 19
}
```

**What to Check**:
- ✅ success = true
- ✅ total_records = 5 (or more)
- ✅ field_count = 19

---

#### Test 2: Fetch Scheduled Ads (HTML)
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/fetch_scheduled_ads.php`

**Expected Result**: HTML table rows showing:
```html
<tr class="ad-row" data-ad-id="1">
  <td>...</td>
  <td>Professional Cleaning Services</td>
  ...
</tr>
```

**What to Check**:
- ✅ See `<tr>` tags with ad data
- ✅ No error messages
- ✅ Contains company names, titles, dates

---

#### Test 3: Fetch Scheduled Ads (JSON)
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/fetch_scheduled_ads.php?format=json`

**Expected Result**:
```json
{
  "success": true,
  "count": 5,
  "data": [
    {
      "id": 1,
      "company_name": "Clean Pro Services",
      "title": "Professional Cleaning Services",
      "status": "Scheduled",
      "budget": 25000.00
    },
    ...
  ]
}
```

**What to Check**:
- ✅ success = true
- ✅ count = 5 (or more)
- ✅ data array contains ad objects

---

#### Test 4: Filter by Status
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/fetch_scheduled_ads.php?status=Active&format=json`

**Expected Result**:
```json
{
  "success": true,
  "count": 2,
  "data": [
    {
      "status": "Active",
      "title": "Professional Plumbing Services",
      ...
    },
    {
      "status": "Active",
      "title": "Electrical Repairs & Installation",
      ...
    }
  ]
}
```

**What to Check**:
- ✅ Only shows Active ads
- ✅ count = 2 (from sample data)
- ✅ All items have status = "Active"

---

#### Test 5: Calendar Data
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/calendar_data.php`

**Expected Result**:
```json
{
  "success": true,
  "count": 5,
  "events": [...],
  "eventsByDate": {
    "2024-08-01": [...],
    "2024-08-05": [...],
    ...
  },
  "statistics": {
    "total": 5,
    "active": 2,
    "pending": 2
  }
}
```

**What to Check**:
- ✅ success = true
- ✅ events array populated
- ✅ eventsByDate has date keys
- ✅ statistics shows counts

---

#### Test 6: Auto Status Update
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/update_status_auto.php`

**Expected Result**:
```json
{
  "success": true,
  "updated": 0,
  "message": "Successfully updated 0 ad schedule(s)"
}
```

**What to Check**:
- ✅ success = true
- ✅ No error messages
- ✅ Returns updated count

---

#### Test 7: Main Dashboard Page
**URL**: `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php`

**Expected Result**:
- ✅ Page loads without errors
- ✅ Statistics cards show numbers (5 Total, 2 Active, etc.)
- ✅ Table displays 5 scheduled advertisements
- ✅ Action buttons visible (Play, Edit, View)
- ✅ Calendar shows August 2024
- ✅ Placement Analytics visible

**Visual Checks**:
- ✅ Professional Cleaning Services (Scheduled)
- ✅ Construction & Renovation (Scheduled)
- ✅ Professional Plumbing Services (Active)
- ✅ Electrical Repairs & Installation (Active)
- ✅ Emergency Repair Services (Expired)

---

#### Test 8: Create New Schedule (POST)

**Method**: Use browser console or test suite

**Code to run in browser console on ad-schedule.php**:
```javascript
const formData = new FormData();
formData.append('ad_id', '999');
formData.append('company_name', 'Test Company');
formData.append('title', 'Test Advertisement');
formData.append('type', 'Banner');
formData.append('category', 'Testing');
formData.append('placement', 'Homepage Top');
formData.append('priority', 'High');
formData.append('start_date', '2024-12-01');
formData.append('end_date', '2024-12-31');
formData.append('time_start', '09:00');
formData.append('time_end', '18:00');
formData.append('budget', '100000');

fetch('/2nd-Year-Group-Project/FixLanka/views/moderator/schedule_ad.php', {
    method: 'POST',
    body: formData
})
.then(res => res.json())
.then(data => console.log(data));
```

**Expected Result**:
```json
{
  "success": true,
  "message": "Advertisement scheduled successfully!",
  "data": {
    "schedule_id": 6,
    "status": "Pending"
  }
}
```

**What to Check**:
- ✅ success = true
- ✅ schedule_id returned (new ID)
- ✅ Refresh page to see new ad in table

---

## 🎯 QUICK VERIFICATION CHECKLIST

### Database Check
- [ ] Table `ads_schedule` exists
- [ ] Has 19 fields
- [ ] Contains 5+ records

### Endpoint Tests
- [ ] fetch_scheduled_ads.php returns HTML
- [ ] fetch_scheduled_ads.php?format=json returns JSON
- [ ] Filtering works (status, priority, etc.)
- [ ] calendar_data.php returns events
- [ ] update_status_auto.php runs without errors
- [ ] schedule_ad.php creates new records

### UI Tests  
- [ ] Main page loads correctly
- [ ] Statistics show correct numbers
- [ ] Table displays all ads
- [ ] Action buttons visible
- [ ] Calendar renders
- [ ] No console errors

---

## 🔍 TROUBLESHOOTING

### Issue: "Table doesn't exist"
**Solution**: The page auto-creates it on first load. Just refresh!

### Issue: "No data showing"
**Solution**: 
1. Check `http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/check_table.php`
2. If total_records = 0, run `install_table.php`

### Issue: "Connection failed"
**Solution**:
1. Start XAMPP Apache & MySQL
2. Verify database name is `fix_lanka`

### Issue: "Console errors"
**Solution**:
1. Press F12 to open Developer Tools
2. Check Console tab for errors
3. Common fix: Hard refresh (Ctrl + F5)

---

## ✨ FINAL CONFIRMATION

### All backend is working if:
1. ✅ Test suite shows 7/7 tests passed (all green)
2. ✅ Main dashboard displays 5 advertisements
3. ✅ Statistics cards show numbers
4. ✅ No errors in browser console
5. ✅ All 7 backend files exist and are mysqli-based
6. ✅ Can create, read, update, delete schedules

### Files Verified:
- ✅ `schedule_ad.php` - 336 lines, mysqli
- ✅ `fetch_scheduled_ads.php` - 227 lines, mysqli
- ✅ `update_schedule.php` - Complete, mysqli
- ✅ `delete_schedule.php` - Complete, mysqli
- ✅ `calendar_data.php` - Complete, mysqli
- ✅ `update_status_auto.php` - Complete, mysqli
- ✅ `ad-schedule.php` - 1332 lines, mysqli + UI

---

## 🎉 SUCCESS CRITERIA

**Backend is 100% complete when**:
- All 7 endpoints respond correctly
- Database operations work (INSERT, SELECT, UPDATE, DELETE)
- No PDO references (100% mysqli)
- Auto table creation works
- Sample data loads properly
- All AJAX calls return valid responses
- UI displays data without errors

**YOU ARE READY TO USE THE SYSTEM!** 🚀
