# Session Management - Quick Test Guide

## 🚀 Quick Test (5 Minutes)

### Test 1: View Sessions
```
1. Open: http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php
2. Click "Security" tab
3. Wait for sessions to load
4. Expected Result:
   ✅ See your current session
   ✅ Shows device type, browser, OS
   ✅ Shows "Current" badge
   ✅ Shows session count badge
```

### Test 2: Create Multiple Sessions
```
1. Keep Chrome open (logged in)
2. Open Firefox
3. Go to: http://localhost/2nd-Year-Group-Project/FixLanka/
4. Login with same account
5. Back to Chrome → Settings → Security tab
6. Expected Result:
   ✅ See 2 sessions
   ✅ Chrome = Current
   ✅ Firefox = has Revoke button
```

### Test 3: Revoke a Session
```
1. In Chrome, click "Revoke" on Firefox session
2. Confirm dialog
3. Expected Result:
   ✅ Success toast appears
   ✅ Firefox session disappears
   ✅ Only 1 session remains
4. Go to Firefox, try to navigate
5. Expected Result:
   ✅ Redirected to login
```

---

## 🐛 Debug Commands

### Check Database Table:
```sql
-- In phpMyAdmin or MySQL CLI:
USE fix_lanka;

-- Show table structure
DESCRIBE user_sessions;

-- View all sessions
SELECT * FROM user_sessions ORDER BY last_activity DESC;

-- Count sessions per user
SELECT user_id, user_role, COUNT(*) as count 
FROM user_sessions 
GROUP BY user_id, user_role;

-- View your sessions (replace 1 with your company_id)
SELECT * FROM user_sessions 
WHERE user_id = 1 AND user_role = 'company'
ORDER BY last_activity DESC;
```

### Browser Console Commands:
```javascript
// Open DevTools (F12) → Console

// Check if functions exist
typeof fetchActiveSessions

// Manually fetch sessions
fetchActiveSessions()

// View session data in console
fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({action: 'get_sessions'})
})
.then(r => r.json())
.then(d => console.log(d))
```

---

## 📊 Verification Checklist

### Database:
- [ ] Table `user_sessions` exists
- [ ] Has indexes on user_id and last_activity
- [ ] Has at least 1 row after login

### API Endpoints:
- [ ] POST get_sessions returns session data
- [ ] POST revoke_session removes session
- [ ] POST revoke_all_sessions removes all but current

### Frontend:
- [ ] Security tab loads without errors
- [ ] Sessions display correctly
- [ ] Revoke buttons work
- [ ] Success toasts appear
- [ ] Device icons show correctly

### Functionality:
- [ ] Current session marked with badge
- [ ] Can't revoke current session
- [ ] Time ago formatting works
- [ ] Session count accurate
- [ ] Revoked sessions logout users

---

## 🎯 Common Test Scenarios

### Scenario: Fresh Login
```
Result: 1 session created
Device: Detected correctly
Browser: Detected correctly
Status: is_current = 1
```

### Scenario: Multiple Browsers
```
Chrome: Session 1 (current)
Firefox: Session 2 (can revoke)
Edge: Session 3 (can revoke)
Total: 3 sessions shown
```

### Scenario: After Revoke
```
Before: 3 sessions
Action: Revoke Firefox session
After: 2 sessions (Chrome, Edge)
Firefox: Redirected to login on next action
```

### Scenario: Revoke All
```
Before: 5 sessions
Action: Click "Revoke All Other Sessions"
After: 1 session (current only)
Others: All redirected to login
```

---

## 📱 Device Detection Examples

### Desktop Browsers:
```
Chrome → 💻 Windows 10/11 - Chrome
Firefox → 💻 Windows 10/11 - Firefox
Safari → 💻 macOS - Safari
Edge → 💻 Windows 10/11 - Edge
```

### Mobile Browsers:
```
Android Chrome → 📱 Android - Chrome
iPhone Safari → 📱 iOS - Safari
Samsung Internet → 📱 Android - Samsung Internet
```

### Tablets:
```
iPad → 📱 iOS - Safari
Android Tablet → 📱 Android - Chrome
```

---

## ⚡ Quick Fixes

### Problem: Sessions not loading
**Quick Fix:**
```javascript
// Open Console (F12)
// Check for errors
// Then manually call:
fetchActiveSessions()
```

### Problem: Wrong device detected
**Quick Fix:**
```sql
-- Update manually in database
UPDATE user_sessions 
SET device_type = 'Mobile', browser = 'Chrome' 
WHERE session_id = 'your_session_id';
```

### Problem: Too many old sessions
**Quick Fix:**
```sql
-- Delete sessions older than 7 days
DELETE FROM user_sessions 
WHERE last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## 🎬 Testing Script

Copy and execute this test sequence:

```
=== SESSION MANAGEMENT TEST ===

Step 1: Initial State
[ ] Login to company dashboard
[ ] Go to Settings → Security tab
[ ] Verify: 1 session visible (current)
[ ] Verify: "Current" badge present
[ ] Verify: No "Revoke" button on current

Step 2: Create Second Session
[ ] Open Firefox/Edge (different browser)
[ ] Login with same account
[ ] Back to original browser
[ ] Refresh Security tab
[ ] Verify: 2 sessions visible
[ ] Verify: Second session has "Revoke" button

Step 3: Test Revoke Individual
[ ] Click "Revoke" on second session
[ ] Confirm dialog
[ ] Verify: Success toast shown
[ ] Verify: Second session removed
[ ] Verify: Only 1 session remains
[ ] Check second browser
[ ] Verify: Redirected to login

Step 4: Create Multiple Sessions
[ ] Open 3 different browsers
[ ] Login to all with same account
[ ] Refresh Security tab
[ ] Verify: 3 sessions visible
[ ] Verify: Count badge shows "3 sessions"

Step 5: Test Revoke All
[ ] Click "Revoke All Other Sessions"
[ ] Confirm dialog
[ ] Verify: Success toast shows "1 session remaining"
[ ] Verify: Only current session visible
[ ] Check other browsers
[ ] Verify: All redirected to login

Step 6: Database Verification
[ ] Open phpMyAdmin
[ ] Navigate to fix_lanka → user_sessions
[ ] Verify: Only 1 row with your user_id
[ ] Verify: is_current = 1
[ ] Verify: last_activity is recent

=== ALL TESTS PASSED ===
Status: ✅ Session Management Working!
```

---

## 🚀 Production Deployment

Before going live:

1. **Test all scenarios** above
2. **Set up session cleanup:**
   ```php
   // Add to a cron job (daily)
   cleanupOldSessions(30); // Remove sessions older than 30 days
   ```

3. **Monitor session table size:**
   ```sql
   SELECT COUNT(*) FROM user_sessions;
   ```

4. **Add indexes if slow** (already done):
   ```sql
   CREATE INDEX idx_user ON user_sessions(user_id, user_role);
   CREATE INDEX idx_last_activity ON user_sessions(last_activity);
   ```

5. **Consider adding:**
   - Email alerts on new login
   - Push notifications for suspicious activity
   - Session history (keep deleted sessions for audit)

---

**Last Updated:** January 18, 2026
**Status:** Ready for Testing! 🎉
