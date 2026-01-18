# Notification Settings - Quick Reference

## 🚀 Quick Start

### Open the Settings Page:
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php
```

### Test in 30 Seconds:
1. Open page
2. Press F12 (open DevTools)
3. Toggle any switch
4. Look for yellow highlight ✅
5. Click "Save Preferences"
6. Look for "Saving..." spinner ✅
7. Look for success toast ✅
8. Refresh page (F5)
9. Verify toggle kept new state ✅

---

## 📁 Files Modified

| File | Location | Changes |
|------|----------|---------|
| **settings.php** | `views/company/` | Frontend JavaScript (9 new functions) |
| **CompanyModel.php** | `models/` | updateSettings() method enhanced |
| **settings.css** | `assets/css/company/` | Added .changed class styles |

---

## 🎯 Key Functions

### JavaScript (settings.php)

| Function | Purpose |
|----------|---------|
| `storeOriginalSettings()` | Save current state for comparison |
| `checkForChanges()` | Detect if any settings changed |
| `updateSaveButtonState(bool)` | Enable/disable save buttons |
| `initializeChangeDetection()` | Setup event listeners on all toggles |
| `cancelChanges()` | Revert to saved state |
| `saveSettings()` | POST changes to backend |
| `populateSettings(data)` | Load settings from API |
| `fetchSettings()` | GET settings from API |
| `showToast(msg, type)` | Show success/error message |

### PHP (CompanyModel.php)

| Method | Purpose |
|--------|---------|
| `getSettings($companyId)` | Fetch settings from database |
| `updateSettings($companyId, $data)` | Save settings to database |
| `getLoginHistory($userId)` | Fetch activity log (Security tab) |
| `getBillingHistory($companyId)` | Fetch invoices (Billing tab) |

---

## 🗄️ Database

### Table: `companysettings`

```sql
-- One row per company
SELECT * FROM companysettings WHERE company_id = 1;

-- Check if settings exist for a company
SELECT COUNT(*) FROM companysettings WHERE company_id = 1;

-- View all companies with settings
SELECT c.company_name, cs.* 
FROM company c 
LEFT JOIN companysettings cs ON c.company_id = cs.company_id;

-- Manually insert settings for testing
INSERT INTO companysettings (
    company_id, email_repair_requests, email_project_updates,
    email_payments, email_team_activity, email_messages,
    push_desktop, push_mobile, quiet_hours_start, quiet_hours_end
) VALUES (
    1, 1, 1, 1, 0, 1, 1, 0, '22:00:00', '08:00:00'
)
ON DUPLICATE KEY UPDATE
    email_repair_requests = 1,
    email_project_updates = 1;

-- Delete settings (will reload defaults)
DELETE FROM companysettings WHERE company_id = 1;
```

---

## 🧪 Browser Console Commands

Open DevTools (F12) → Console tab

```javascript
// Check current settings
console.log(originalSettings);

// Check for changes
console.log(checkForChanges());

// Check unsaved changes flag
console.log(hasUnsavedChanges);

// Get specific toggle state
console.log(document.getElementById('emailRepairRequests').checked);

// Manually trigger save (testing)
saveSettings();

// Manually fetch settings (reload from server)
fetchSettings();

// Check if save buttons are disabled
console.log(document.getElementById('saveNotificationsBtn').disabled);

// Force enable save button (testing)
document.getElementById('saveNotificationsBtn').disabled = false;
document.getElementById('saveAllBtn').disabled = false;

// Force add yellow highlight to test CSS
document.querySelectorAll('.notification-item')[0].classList.add('changed');

// Remove all yellow highlights
document.querySelectorAll('.notification-item.changed').forEach(i => i.classList.remove('changed'));

// Show test toast
showToast('Test message', 'success');
showToast('Error test', 'error');
```

---

## 🔍 Debugging

### Check API Response

1. Open DevTools (F12)
2. Go to Network tab
3. Click a toggle and save
4. Look for `settings.php` request
5. Click it → Preview tab
6. Should see:
   ```json
   {
       "success": true,
       "message": "Settings saved successfully"
   }
   ```

### Check Console for Errors

Common errors:
```
❌ "Failed to fetch" → Backend not running (start XAMPP)
❌ "Unauthorized" → Not logged in (check session)
❌ "Invalid action" → Wrong API request format
❌ "Cannot read property 'checked'" → Element not found
```

### Check Network Timing

In Network tab, look at:
- **Status:** Should be `200 OK`
- **Type:** Should be `xhr` or `fetch`
- **Time:** Should be < 500ms
- **Size:** Should be small (few KB)

### Check PHP Errors

Location: `C:\xampp\apache\logs\error.log`

Common PHP errors:
```
❌ PDO exception → Database connection failed
❌ Undefined index → Missing array key
❌ Call to undefined method → Model method missing
```

---

## 🎨 CSS Classes

| Class | Effect |
|-------|--------|
| `.notification-item` | Base style (white background) |
| `.notification-item:hover` | Hover effect (slight transform) |
| `.notification-item.changed` | Changed state (yellow highlight) |
| `.switch` | Toggle switch container |
| `.switch input:checked + .slider` | Toggle ON state (blue) |
| `.btn-primary` | Save button style |
| `.btn-secondary` | Cancel button style |
| `.toast` | Notification popup |
| `.toast.success` | Success toast (green) |
| `.toast.error` | Error toast (red) |

---

## 🔧 Configuration

### Toggle IDs (Frontend):
```javascript
'emailRepairRequests'   → New Repair Requests
'emailProjectUpdates'   → Project Updates
'emailPayments'         → Payment Notifications
'emailTeamActivity'     → Team Activity
'emailMessages'         → Customer Messages
'pushDesktop'           → Desktop Notifications
'pushMobile'            → Mobile Push Notifications
'quietHoursStart'       → Quiet hours start time
'quietHoursEnd'         → Quiet hours end time
```

### Database Columns:
```sql
email_repair_requests   → TINYINT (0 or 1)
email_project_updates   → TINYINT (0 or 1)
email_payments          → TINYINT (0 or 1)
email_team_activity     → TINYINT (0 or 1)
email_messages          → TINYINT (0 or 1)
push_desktop            → TINYINT (0 or 1)
push_mobile             → TINYINT (0 or 1)
quiet_hours_start       → TIME (HH:MM:SS)
quiet_hours_end         → TIME (HH:MM:SS)
```

### API Endpoints:
```
GET  /api/settings.php              → Fetch all settings
POST /api/settings.php              → Save settings
     action: "update_notifications"
     settings: { ... }
```

---

## 📊 Status Indicators

### Visual Feedback:

| State | Save Button | Item Background | Border |
|-------|-------------|-----------------|--------|
| **No changes** | Disabled (gray) | White | Light gray |
| **Has changes** | Enabled (blue) | Yellow | Yellow (4px left) |
| **Saving** | Disabled + Spinner | Yellow | Yellow |
| **Saved** | Disabled (gray) | White | Light gray |
| **Error** | Enabled (blue) | Yellow | Yellow |

### Toast Messages:

| Type | Color | Icon | Message |
|------|-------|------|---------|
| **Success** | Green | ✓ | "Settings saved successfully" |
| **Error** | Red | ✕ | "Failed to save settings" |
| **Network Error** | Red | ✕ | "Network error occurred" |
| **Discard** | Green | ✓ | "Changes discarded" |

---

## ⚡ Keyboard Shortcuts

| Key | Action |
|-----|--------|
| **F5** | Refresh page |
| **Ctrl+F5** | Hard refresh (clear cache) |
| **F12** | Open DevTools |
| **Ctrl+Shift+C** | Inspect element |
| **Ctrl+Shift+J** | Open Console |
| **Ctrl+R** | Reload page |
| **Esc** | Close dialogs |

Future enhancement ideas:
- `Ctrl+S` → Save settings
- `Ctrl+Z` → Undo changes
- `Enter` → Save (when focused)

---

## 🎯 Testing Scenarios

### Scenario 1: New User (No Settings)
```
1. Company just registered
2. First time opening settings
3. Expected: Default values loaded
   - Most toggles ON (1)
   - Team Activity OFF (0)
   - Quiet hours: 22:00 to 08:00
```

### Scenario 2: Existing User
```
1. Company has saved settings
2. Opening settings page
3. Expected: Saved values loaded
4. Make changes and save
5. Expected: Updated values persist
```

### Scenario 3: Multiple Changes
```
1. Toggle 3 switches
2. Change quiet hours
3. Expected: All 4 items show changes
4. Save once
5. Expected: All 4 saved together
```

### Scenario 4: Cancel Changes
```
1. Toggle 2 switches
2. Click Cancel
3. Expected: Confirm dialog
4. Confirm
5. Expected: Reverted to original
```

### Scenario 5: Leave Page
```
1. Toggle a switch
2. Click browser back button
3. Expected: Browser warning
4. Stay on page
5. Save changes
6. Click back again
7. Expected: No warning (no unsaved)
```

---

## 🐛 Common Issues & Quick Fixes

| Issue | Quick Fix |
|-------|-----------|
| Save button always disabled | Check console for errors, reload page |
| Settings don't save | Check XAMPP running, check session |
| Yellow highlight doesn't show | Clear cache (Ctrl+F5) |
| Toast doesn't appear | Check `showToast()` function exists |
| Can't toggle switches | Check event listeners initialized |
| Database not updating | Check `company_id` in session |
| API returns 401 | Re-login (session expired) |
| API returns 500 | Check PHP error log |

---

## 📦 Dependencies

### Frontend:
- ✅ Font Awesome 6.4.0 (icons)
- ✅ Modern browser (ES6+ support)
- ✅ Fetch API
- ✅ CSS Grid/Flexbox

### Backend:
- ✅ PHP 7.4+
- ✅ PDO extension
- ✅ MySQL 5.7+
- ✅ Session support

### Database:
- ✅ `companysettings` table
- ✅ `company` table (foreign key)
- ✅ InnoDB engine

---

## 🎓 Learning Resources

### Understanding the Code:

1. **JavaScript Event Listeners:**
   - `addEventListener('change', callback)`
   - Used for toggle switches and time inputs

2. **Fetch API:**
   - `fetch(url, {method, headers, body})`
   - Used for GET and POST requests

3. **CSS Classes:**
   - `classList.add('changed')`
   - `classList.remove('changed')`

4. **PHP Prepared Statements:**
   - `$stmt = $pdo->prepare($sql)`
   - `$stmt->execute($params)`

5. **SQL Upsert:**
   - `INSERT ... ON DUPLICATE KEY UPDATE`
   - Updates if exists, inserts if not

---

## 📝 Maintenance

### Weekly Checks:
- [ ] Verify settings save correctly
- [ ] Check error logs for issues
- [ ] Test with different browsers
- [ ] Verify database backups

### Monthly Review:
- [ ] Analyze usage patterns
- [ ] Review notification effectiveness
- [ ] Check for new feature requests
- [ ] Update documentation

### When Issues Occur:
1. Check browser console
2. Check network requests
3. Check PHP error log
4. Check database data
5. Test with fresh user
6. Clear browser cache

---

## 🚀 Performance Tips

### Frontend:
- Minimize DOM queries (cache elements)
- Use event delegation where possible
- Debounce rapid toggle changes
- Lazy load non-visible tabs

### Backend:
- Use prepared statements (already done)
- Index frequently queried columns
- Cache settings in session (future)
- Minimize database round trips

### Database:
- Keep `companysettings` table small
- Index `company_id` column
- Regular OPTIMIZE TABLE
- Monitor query performance

---

## 📞 Support Contacts

### For Issues:
1. **Browser Issues:** Check DevTools Console
2. **API Issues:** Check Network tab
3. **Database Issues:** Check phpMyAdmin
4. **PHP Issues:** Check error logs

### Documentation:
- `SETTINGS_BACKEND_IMPLEMENTATION_PLAN.md` - Full implementation guide
- `NOTIFICATION_SETTINGS_TEST_GUIDE.md` - Comprehensive testing
- `NOTIFICATION_SETTINGS_SUMMARY.md` - What was changed
- `NOTIFICATION_SETTINGS_FLOW_DIAGRAM.md` - Visual diagrams

---

**Last Updated:** January 18, 2026
**Version:** 1.0
**Status:** Production Ready ✅
