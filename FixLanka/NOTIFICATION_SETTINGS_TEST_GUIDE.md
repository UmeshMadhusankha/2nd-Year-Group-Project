# Notification Settings - Testing Guide

## ✅ Implementation Complete

### What Was Implemented:

#### **Phase 1: Core Toggle Functionality** ✅
1. ✅ Loading states with spinner during save
2. ✅ Change detection - save button only enabled when changes made
3. ✅ Visual feedback - changed items highlighted in yellow
4. ✅ Unsaved changes warning when leaving page
5. ✅ Cancel button to revert changes
6. ✅ Error handling with detailed error messages
7. ✅ Success/error toast notifications

#### **Phase 2: Quiet Hours Feature** ✅
1. ✅ Backend updated to save/load quiet hours start time
2. ✅ Backend updated to save/load quiet hours end time
3. ✅ Frontend sends quiet hours data to backend
4. ✅ Frontend loads quiet hours data from backend

#### **UI/UX Enhancements** ✅
1. ✅ Save buttons disabled by default
2. ✅ Save buttons enabled only when changes detected
3. ✅ Changed notification items highlighted with yellow background
4. ✅ Animation when item changes
5. ✅ Loading spinner on save buttons
6. ✅ Re-enable buttons after save completes

---

## 🧪 Testing Checklist

### **Test 1: Initial Page Load**
```
✅ Open: http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php
✅ Check: All toggle switches show correct state from database
✅ Check: Quiet hours show correct times
✅ Check: Save buttons are DISABLED (no changes yet)
✅ Check: No console errors in browser DevTools (F12)
```

**Expected Result:**
- Page loads successfully
- All settings reflect database values
- Save buttons are grayed out/disabled

---

### **Test 2: Toggle Switch Changes**
```
1. Toggle any notification switch (e.g., "New Repair Requests")
2. Observe:
   ✅ Item background turns yellow with animation
   ✅ Yellow left border appears
   ✅ Save buttons become ENABLED (blue and clickable)
   ✅ hasUnsavedChanges = true
```

**Expected Result:**
- Visual feedback immediately shows what changed
- Save buttons become active

---

### **Test 3: Multiple Changes**
```
1. Toggle 3-4 different switches
2. Check:
   ✅ All changed items highlighted in yellow
   ✅ Unchanged items remain white/normal
   ✅ Save button still enabled
```

**Expected Result:**
- Multiple items can be changed
- Each maintains its visual state

---

### **Test 4: Revert Changes (Toggle Back)**
```
1. Toggle a switch ON
2. Toggle the same switch back OFF (original state)
3. Check:
   ✅ Yellow highlight REMOVED (returns to normal)
   ✅ If no other changes, save button becomes DISABLED again
```

**Expected Result:**
- Items that match original state lose highlight
- Save button disabled when all back to original

---

### **Test 5: Quiet Hours Changes**
```
1. Change "Quiet Hours Start" time
2. Change "Quiet Hours End" time
3. Check:
   ✅ Save button becomes enabled
```

**Expected Result:**
- Time changes are detected
- Save button activates

---

### **Test 6: Save Settings**
```
1. Make 2-3 changes to toggles
2. Click "Save Preferences" button
3. Observe:
   ✅ Button shows spinner: "🔄 Saving..."
   ✅ Button becomes disabled during save
   ✅ Success toast appears: "Settings saved successfully"
   ✅ Yellow highlights REMOVED from all items
   ✅ Save button becomes DISABLED again
4. Open browser DevTools Network tab
5. Check API request:
   ✅ POST to /api/settings.php
   ✅ Response: {"success": true, "message": "Settings saved successfully"}
```

**Expected Result:**
- Smooth save animation
- Success feedback
- UI resets to "no changes" state

---

### **Test 7: Database Persistence**
```
1. Make changes and save
2. Refresh the page (F5)
3. Check:
   ✅ All toggle switches reflect the saved values
   ✅ Quiet hours show saved times
   ✅ Save buttons are disabled (no unsaved changes)
```

**Expected Result:**
- Settings persist across page reloads
- Database correctly stores values

---

### **Test 8: Cancel Changes**
```
1. Toggle 2-3 switches (yellow highlights appear)
2. Click "Cancel" button
3. If changes exist:
   ✅ Confirm dialog appears: "Discard changes?"
   ✅ Click OK
   ✅ All toggles revert to original state
   ✅ Yellow highlights removed
   ✅ Toast: "Changes discarded"
   ✅ Save button becomes disabled
```

**Expected Result:**
- Changes reverted without saving
- UI resets completely

---

### **Test 9: Leave Page Warning**
```
1. Toggle a switch (make unsaved changes)
2. Try to:
   - Click browser back button, OR
   - Close the tab, OR
   - Navigate to another page
3. Check:
   ✅ Browser warning appears: "You have unsaved changes. Are you sure you want to leave?"
   ✅ Can choose to stay or leave
```

**Expected Result:**
- User warned about data loss
- Can prevent accidental navigation

---

### **Test 10: Save All Button**
```
1. Make changes in Notifications tab
2. Click "Save All Changes" button (top right)
3. Check:
   ✅ Works same as "Save Preferences"
   ✅ Shows spinner and saves
   ✅ Success toast appears
```

**Expected Result:**
- Both save buttons work identically

---

### **Test 11: Error Handling**
```
Simulate network error:
1. Open DevTools Network tab
2. Set "Throttling" to "Offline"
3. Toggle a switch and click Save
4. Check:
   ✅ Error toast appears: "Network error occurred"
   ✅ Save button re-enables (not stuck)
   ✅ Can try again

Alternative: Stop XAMPP Apache temporarily
```

**Expected Result:**
- Graceful error handling
- User can retry

---

### **Test 12: Verify Database**
```
1. Make and save changes
2. Open phpMyAdmin
3. Navigate to: fixlanka -> companysettings table
4. Check row for your company_id:
   ✅ email_repair_requests = 1 or 0 (matches toggle)
   ✅ email_project_updates = 1 or 0
   ✅ email_payments = 1 or 0
   ✅ email_team_activity = 1 or 0
   ✅ email_messages = 1 or 0
   ✅ push_desktop = 1 or 0
   ✅ push_mobile = 1 or 0
   ✅ quiet_hours_start = "HH:MM:SS" (e.g., "22:00:00")
   ✅ quiet_hours_end = "HH:MM:SS" (e.g., "08:00:00")
   ✅ updated_at = current timestamp
```

**Expected Result:**
- All values correctly stored in database
- Timestamp shows recent update

---

## 🐛 Troubleshooting

### Issue: Save button always disabled
**Cause:** Change detection not working
**Fix:**
1. Open browser console (F12)
2. Type: `checkForChanges()`
3. Should return `true` when changes made
4. Check `originalSettings` object exists

### Issue: Settings don't save
**Debug:**
1. Open DevTools Network tab
2. Make a change and click Save
3. Look for POST request to `/api/settings.php`
4. Check Response tab for error message
5. Check Console for JavaScript errors

**Common Causes:**
- Session expired (logout and login again)
- Database connection issue
- Wrong `company_id` in session

### Issue: Yellow highlights don't appear
**Fix:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Hard refresh (Ctrl+F5)
3. Check CSS file loaded: `settings.css`

### Issue: Settings don't load on page load
**Debug:**
1. Open Console
2. Check for: `Failed to load settings: [message]`
3. Verify API endpoint returns data:
   ```
   Open in new tab: http://localhost/2nd-Year-Group-Project/FixLanka/api/settings.php
   Should see JSON with settings data
   ```

---

## 📊 Console Commands for Testing

Open browser console (F12) and try:

```javascript
// Check current settings state
console.log(originalSettings);

// Check if changes detected
console.log(checkForChanges());

// Check unsaved changes flag
console.log(hasUnsavedChanges);

// Manually trigger save (for testing)
saveSettings();

// Check specific toggle state
console.log(document.getElementById('emailRepairRequests').checked);
```

---

## ✨ Features Summary

### **Visual Feedback:**
- 🟡 Yellow highlight on changed items
- 🔵 Blue enabled save buttons when changes exist
- ⚪ Gray disabled save buttons when no changes
- 🔄 Spinner animation during save
- ✅ Green success toast
- ❌ Red error toast

### **Smart Behavior:**
- Only saves when changes detected
- Warns before leaving with unsaved changes
- Reverts cleanly on cancel
- Handles errors gracefully
- Persists to database correctly

### **User Experience:**
- Immediate visual feedback
- Clear save state indication
- No accidental data loss
- Smooth animations
- Intuitive toggle system

---

## 🎯 Next Steps (Optional Enhancements)

If everything works perfectly, consider:

1. **Session Management** (Phase 3)
   - View active login sessions
   - Revoke individual sessions
   - "Revoke all other sessions" button

2. **Security Tab Enhancements**
   - Change password feature
   - Two-factor authentication
   - Security questions

3. **Billing Tab Backend**
   - Real payment method management
   - Invoice download functionality
   - Subscription plan changes

4. **Advanced Notifications**
   - Email digest frequency
   - Notification sound settings
   - In-app notification preferences

---

## 📝 Test Results Template

Copy this and fill in as you test:

```
=== NOTIFICATION SETTINGS TEST RESULTS ===
Date: [DATE]
Tester: [NAME]

[ ] Test 1: Initial Page Load - PASS/FAIL
[ ] Test 2: Toggle Changes - PASS/FAIL
[ ] Test 3: Multiple Changes - PASS/FAIL
[ ] Test 4: Revert Changes - PASS/FAIL
[ ] Test 5: Quiet Hours - PASS/FAIL
[ ] Test 6: Save Settings - PASS/FAIL
[ ] Test 7: Database Persistence - PASS/FAIL
[ ] Test 8: Cancel Changes - PASS/FAIL
[ ] Test 9: Leave Warning - PASS/FAIL
[ ] Test 10: Save All Button - PASS/FAIL
[ ] Test 11: Error Handling - PASS/FAIL
[ ] Test 12: Database Verify - PASS/FAIL

Notes:
- 
- 

Overall Status: READY FOR PRODUCTION / NEEDS FIXES
```

---

## 🚀 Quick Start

```powershell
# 1. Start XAMPP
# 2. Open page
start http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php

# 3. Open DevTools
# Press F12

# 4. Test toggles
# Click switches and watch for yellow highlights

# 5. Save and verify
# Click save, check for success toast

# 6. Refresh page
# Verify settings persist
```

---

**Implementation Date:** January 18, 2026
**Status:** ✅ Complete and Ready for Testing
**Estimated Test Time:** 15-20 minutes
