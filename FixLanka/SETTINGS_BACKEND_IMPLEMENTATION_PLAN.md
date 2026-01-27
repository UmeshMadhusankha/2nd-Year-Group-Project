# Settings Page Backend Integration - Implementation Plan

## 📋 Overview
This document outlines the complete implementation plan to connect the settings page toggles to the backend, ensuring all notification preferences are properly saved and retrieved from the database.

---

## ✅ Current Status Analysis

### What's Already Working:
1. ✅ **Database Schema** - `companysettings` table exists with all required columns
2. ✅ **API Endpoint** - `/api/settings.php` handles GET and POST requests
3. ✅ **Model Methods** - `CompanyModel.php` has:
   - `getSettings()` - Fetches settings from DB
   - `updateSettings()` - Saves settings to DB
   - `getLoginHistory()` - Fetches activity logs
   - `getBillingHistory()` - Fetches billing records
4. ✅ **Frontend JS** - Basic fetch/save functions exist
5. ✅ **Toast Function** - Now properly declared (just fixed)

### What Needs Implementation:
1. ❌ **Quiet Hours** - Not being saved/loaded
2. ❌ **Session Management** - Active sessions feature incomplete
3. ❌ **Payment Methods** - Not connected to backend
4. ❌ **Security Features** - Password change, 2FA missing
5. ❌ **Real-time Feedback** - Better UX during save operations
6. ❌ **Validation** - Input validation on both frontend and backend

---

## 🎯 Implementation Phases

### **PHASE 1: Core Toggle Functionality** ⭐ (Priority 1)

#### 1.1 Verify Database Connection
**File:** `config/database.php`
**Action:** Ensure connection is established

```php
// Verify this file exists and returns working PDO instance
$pdo = Database::getInstance()->getConnection();
```

#### 1.2 Test Current Toggle System
**File:** `views/company/settings.php`
**Steps:**
1. Open browser console (F12)
2. Toggle any notification switch
3. Click "Save Preferences"
4. Check for:
   - Network request to `/api/settings.php`
   - Response JSON showing `success: true`
   - No console errors

**Expected Behavior:**
```javascript
// Request payload should look like:
{
  "action": "update_notifications",
  "settings": {
    "emailRepairRequests": true,
    "emailProjectUpdates": true,
    "emailPayments": true,
    "emailTeamActivity": false,
    "emailMessages": true,
    "pushDesktop": true,
    "pushMobile": false
  }
}
```

#### 1.3 Add Loading States
**File:** `views/company/settings.php`
**Line:** Around 568-600 (saveSettings function)

**Changes Needed:**
```javascript
async function saveSettings() {
    // ADD: Disable save button and show loading
    const saveBtn = document.getElementById('saveNotificationsBtn');
    const saveAllBtn = document.getElementById('saveAllBtn');
    const originalText = saveBtn.innerHTML;
    
    saveBtn.disabled = true;
    saveAllBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    const settings = {
        emailRepairRequests: document.getElementById('emailRepairRequests').checked,
        emailProjectUpdates: document.getElementById('emailProjectUpdates').checked,
        emailPayments: document.getElementById('emailPayments').checked,
        emailTeamActivity: document.getElementById('emailTeamActivity').checked,
        emailMessages: document.getElementById('emailMessages').checked,
        pushDesktop: document.getElementById('pushDesktop').checked,
        pushMobile: document.getElementById('pushMobile').checked
    };

    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update_notifications',
                settings: settings
            })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('Settings saved successfully', 'success');
        } else {
            showToast('Failed to save settings: ' + (result.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error saving settings:', error);
        showToast('Network error occurred', 'error');
    } finally {
        // ADD: Re-enable buttons
        saveBtn.disabled = false;
        saveAllBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    }
}
```

#### 1.4 Add Change Detection
**Purpose:** Only enable save button when changes are made

**Add after fetchSettings():**
```javascript
let originalSettings = {};

function populateSettings(data) {
    // ... existing code ...
    
    // AFTER populating, store original state
    originalSettings = {
        emailRepairRequests: document.getElementById('emailRepairRequests').checked,
        emailProjectUpdates: document.getElementById('emailProjectUpdates').checked,
        emailPayments: document.getElementById('emailPayments').checked,
        emailTeamActivity: document.getElementById('emailTeamActivity').checked,
        emailMessages: document.getElementById('emailMessages').checked,
        pushDesktop: document.getElementById('pushDesktop').checked,
        pushMobile: document.getElementById('pushMobile').checked
    };
    
    // Disable save buttons initially
    document.getElementById('saveNotificationsBtn').disabled = true;
    document.getElementById('saveAllBtn').disabled = true;
}

// Add change listeners to all toggles
function initializeChangeDetection() {
    const toggles = [
        'emailRepairRequests', 'emailProjectUpdates', 'emailPayments',
        'emailTeamActivity', 'emailMessages', 'pushDesktop', 'pushMobile'
    ];
    
    toggles.forEach(id => {
        document.getElementById(id).addEventListener('change', () => {
            const hasChanges = checkForChanges();
            document.getElementById('saveNotificationsBtn').disabled = !hasChanges;
            document.getElementById('saveAllBtn').disabled = !hasChanges;
        });
    });
}

function checkForChanges() {
    return originalSettings.emailRepairRequests !== document.getElementById('emailRepairRequests').checked ||
           originalSettings.emailProjectUpdates !== document.getElementById('emailProjectUpdates').checked ||
           originalSettings.emailPayments !== document.getElementById('emailPayments').checked ||
           originalSettings.emailTeamActivity !== document.getElementById('emailTeamActivity').checked ||
           originalSettings.emailMessages !== document.getElementById('emailMessages').checked ||
           originalSettings.pushDesktop !== document.getElementById('pushDesktop').checked ||
           originalSettings.pushMobile !== document.getElementById('pushMobile').checked;
}

// Call in DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // ... existing code ...
    initializeChangeDetection();
});
```

---

### **PHASE 2: Quiet Hours Feature** ⭐ (Priority 2)

#### 2.1 Update Database Save
**File:** `models/CompanyModel.php`
**Line:** 126-158 (updateSettings function)

**Changes:**
```php
public function updateSettings($companyId, $data) {
    $dbData = [
        'email_repair_requests' => $data['emailRepairRequests'] ? 1 : 0,
        'email_project_updates' => $data['emailProjectUpdates'] ? 1 : 0,
        'email_payments' => $data['emailPayments'] ? 1 : 0,
        'email_team_activity' => $data['emailTeamActivity'] ? 1 : 0,
        'email_messages' => $data['emailMessages'] ? 1 : 0,
        'push_desktop' => $data['pushDesktop'] ? 1 : 0,
        'push_mobile' => $data['pushMobile'] ? 1 : 0,
        'company_id' => $companyId
    ];
    
    // ADD: Quiet hours if provided
    if (isset($data['quietHoursStart'])) {
        $dbData['quiet_hours_start'] = $data['quietHoursStart'];
    }
    if (isset($data['quietHoursEnd'])) {
        $dbData['quiet_hours_end'] = $data['quietHoursEnd'];
    }

    $sql = "INSERT INTO CompanySettings (
        company_id, email_repair_requests, email_project_updates, 
        email_payments, email_team_activity, email_messages, 
        push_desktop, push_mobile, quiet_hours_start, quiet_hours_end
    ) VALUES (
        :company_id, :email_repair_requests, :email_project_updates,
        :email_payments, :email_team_activity, :email_messages,
        :push_desktop, :push_mobile, :quiet_hours_start, :quiet_hours_end
    )
    ON DUPLICATE KEY UPDATE 
        email_repair_requests = VALUES(email_repair_requests),
        email_project_updates = VALUES(email_project_updates),
        email_payments = VALUES(email_payments),
        email_team_activity = VALUES(email_team_activity),
        email_messages = VALUES(email_messages),
        push_desktop = VALUES(push_desktop),
        push_mobile = VALUES(push_mobile),
        quiet_hours_start = VALUES(quiet_hours_start),
        quiet_hours_end = VALUES(quiet_hours_end)";
    
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($dbData);
}
```

#### 2.2 Update Frontend Save
**File:** `views/company/settings.php`
**Add to saveSettings function:**

```javascript
const settings = {
    emailRepairRequests: document.getElementById('emailRepairRequests').checked,
    emailProjectUpdates: document.getElementById('emailProjectUpdates').checked,
    emailPayments: document.getElementById('emailPayments').checked,
    emailTeamActivity: document.getElementById('emailTeamActivity').checked,
    emailMessages: document.getElementById('emailMessages').checked,
    pushDesktop: document.getElementById('pushDesktop').checked,
    pushMobile: document.getElementById('pushMobile').checked,
    // ADD THESE:
    quietHoursStart: document.getElementById('quietHoursStart').value,
    quietHoursEnd: document.getElementById('quietHoursEnd').value
};
```

#### 2.3 Update Frontend Load
**File:** `views/company/settings.php`
**Add to populateSettings function:**

```javascript
function populateSettings(data) {
    if (data.settings) {
        const n = data.settings;
        const isTrue = (val) => val == 1 || val === true;

        setSwitch('emailRepairRequests', isTrue(n.email_repair_requests));
        setSwitch('emailProjectUpdates', isTrue(n.email_project_updates));
        setSwitch('emailPayments', isTrue(n.email_payments));
        setSwitch('emailTeamActivity', isTrue(n.email_team_activity));
        setSwitch('emailMessages', isTrue(n.email_messages));
        setSwitch('pushDesktop', isTrue(n.push_desktop));
        setSwitch('pushMobile', isTrue(n.push_mobile));
        
        // ADD THESE:
        if (n.quiet_hours_start) {
            document.getElementById('quietHoursStart').value = n.quiet_hours_start.substring(0, 5);
        }
        if (n.quiet_hours_end) {
            document.getElementById('quietHoursEnd').value = n.quiet_hours_end.substring(0, 5);
        }
    }
    // ... rest of function
}
```

---

### **PHASE 3: Security Tab - Session Management** ⭐ (Priority 3)

#### 3.1 Create Sessions Table
**File:** Create `database/migrations/add_sessions_table.sql`

```sql
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('company','customer','repairer','admin') NOT NULL,
  `device_type` varchar(100) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`session_id`),
  KEY `idx_user` (`user_id`, `user_role`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

**Run:** Execute this in phpMyAdmin or via command line

#### 3.2 Track Sessions in Session Handler
**File:** `config/session.php`
**Add function:**

```php
function trackUserSession() {
    if (!isset($_SESSION['user_id'])) return;
    
    require_once __DIR__ . '/database.php';
    $pdo = Database::getInstance()->getConnection();
    
    $sessionId = session_id();
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['user_role'];
    
    // Parse user agent
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $deviceType = 'Desktop'; // Simple detection
    if (preg_match('/mobile|android|iphone|ipad/i', $userAgent)) {
        $deviceType = 'Mobile';
    }
    
    $browser = 'Unknown';
    if (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
    elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
    elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
    elseif (preg_match('/Edge/i', $userAgent)) $browser = 'Edge';
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    // Upsert session
    $sql = "INSERT INTO user_sessions (session_id, user_id, user_role, device_type, browser, ip_address, is_current)
            VALUES (?, ?, ?, ?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE 
            last_activity = CURRENT_TIMESTAMP,
            is_current = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sessionId, $userId, $userRole, $deviceType, $browser, $ipAddress]);
}

// Call this after successful login
trackUserSession();
```

#### 3.3 Add Session Methods to CompanyModel
**File:** `models/CompanyModel.php`
**Add these methods:**

```php
public function getActiveSessions($userId) {
    $sql = "SELECT session_id, device_type, browser, ip_address, location, 
                   last_activity, is_current, created_at
            FROM user_sessions 
            WHERE user_id = ? AND user_role = 'company'
            ORDER BY last_activity DESC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function revokeSession($userId, $sessionId) {
    // Can't revoke current session via this method
    $currentSessionId = session_id();
    if ($sessionId === $currentSessionId) {
        return false;
    }
    
    $sql = "DELETE FROM user_sessions 
            WHERE session_id = ? AND user_id = ? AND user_role = 'company'";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$sessionId, $userId]);
}

public function revokeAllOtherSessions($userId) {
    $currentSessionId = session_id();
    $sql = "DELETE FROM user_sessions 
            WHERE user_id = ? AND user_role = 'company' AND session_id != ?";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([$userId, $currentSessionId]);
}
```

#### 3.4 Add Session API Endpoints
**File:** `api/settings.php`
**Add these actions in POST handler:**

```php
if ($action === 'get_sessions') {
    $sessions = $companyModel->getActiveSessions($companyId);
    echo json_encode(['success' => true, 'data' => $sessions]);
}
elseif ($action === 'revoke_session') {
    $sessionId = $input['session_id'] ?? '';
    $success = $companyModel->revokeSession($companyId, $sessionId);
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'Session revoked' : 'Failed to revoke session'
    ]);
}
elseif ($action === 'revoke_all_sessions') {
    $success = $companyModel->revokeAllOtherSessions($companyId);
    echo json_encode([
        'success' => $success,
        'message' => $success ? 'All other sessions revoked' : 'Failed to revoke sessions'
    ]);
}
```

#### 3.5 Update Frontend - Load Sessions
**File:** `views/company/settings.php`
**Modify fetchSettings to also fetch sessions:**

```javascript
async function fetchSettings() {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php');
        const result = await response.json();

        if (result.success) {
            populateSettings(result.data);
            // ADD: Fetch sessions separately or include in same call
            fetchActiveSessions();
        } else {
            console.error('Failed to load settings:', result.message);
        }
    } catch (error) {
        console.error('Error loading settings:', error);
    }
}

async function fetchActiveSessions() {
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'get_sessions' })
        });
        const result = await response.json();

        if (result.success) {
            populateSessions(result.data);
        }
    } catch (error) {
        console.error('Error loading sessions:', error);
    }
}

function populateSessions(sessions) {
    const container = document.querySelector('.settings-section'); // Find the right container
    const currentSessionId = '<?php echo session_id(); ?>'; // Get from PHP
    
    let html = '<h2 class="section-title">Active Sessions</h2>';
    
    sessions.forEach(session => {
        const isCurrent = session.is_current == 1 || session.session_id === currentSessionId;
        const timeAgo = getTimeAgo(session.last_activity);
        
        html += `
        <div class="session-item ${isCurrent ? 'current' : ''}">
            <div class="session-icon">
                <i class="fas fa-${session.device_type === 'Mobile' ? 'mobile-alt' : 'laptop'}"></i>
            </div>
            <div class="session-info">
                <h3>${session.device_type} - ${session.browser}</h3>
                <p>${session.location || session.ip_address}${isCurrent ? ' • Current session' : ''}</p>
                <small>Last active: ${timeAgo}</small>
            </div>
            ${isCurrent ? 
                '<span class="session-badge">Current</span>' : 
                `<button class="btn-revoke" onclick="revokeSession('${session.session_id}')">Revoke</button>`
            }
        </div>`;
    });
    
    html += `
        <button class="action-btn danger" onclick="revokeAllSessions()">
            <i class="fas fa-times-circle"></i> Revoke All Other Sessions
        </button>`;
    
    // Update the container (adjust selector as needed)
    const sessionsContainer = document.querySelector('#security-tab .settings-section');
    if (sessionsContainer) {
        sessionsContainer.innerHTML = html;
    }
}

function getTimeAgo(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const diffMs = now - then;
    const diffMins = Math.floor(diffMs / 60000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins} minutes ago`;
    
    const diffHours = Math.floor(diffMins / 60);
    if (diffHours < 24) return `${diffHours} hours ago`;
    
    const diffDays = Math.floor(diffHours / 24);
    if (diffDays === 1) return 'Yesterday';
    return `${diffDays} days ago`;
}

async function revokeSession(sessionId) {
    if (!confirm('Are you sure you want to revoke this session?')) return;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'revoke_session',
                session_id: sessionId
            })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('Session revoked successfully', 'success');
            fetchActiveSessions(); // Refresh list
        } else {
            showToast('Failed to revoke session', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error occurred', 'error');
    }
}

async function revokeAllSessions() {
    if (!confirm('Are you sure you want to revoke all other sessions? You will remain logged in on this device.')) return;
    
    try {
        const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'revoke_all_sessions' })
        });
        const result = await response.json();
        
        if (result.success) {
            showToast('All other sessions revoked', 'success');
            fetchActiveSessions(); // Refresh list
        } else {
            showToast('Failed to revoke sessions', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Network error occurred', 'error');
    }
}
```

---

### **PHASE 4: Enhanced User Experience** ⭐ (Priority 4)

#### 4.1 Add Cancel Button Functionality
**File:** `views/company/settings.php`

```javascript
document.getElementById('cancelNotificationsBtn').addEventListener('click', () => {
    // Reload settings from server
    fetchSettings();
    showToast('Changes discarded', 'success');
});
```

#### 4.2 Add Unsaved Changes Warning
```javascript
let hasUnsavedChanges = false;

// Track changes
function checkForChanges() {
    const currentHasChanges = 
        originalSettings.emailRepairRequests !== document.getElementById('emailRepairRequests').checked ||
        originalSettings.emailProjectUpdates !== document.getElementById('emailProjectUpdates').checked ||
        // ... all other comparisons
        
    hasUnsavedChanges = currentHasChanges;
    return currentHasChanges;
}

// Warn on page leave
window.addEventListener('beforeunload', (e) => {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});

// Reset flag after save
async function saveSettings() {
    // ... existing save code ...
    if (result.success) {
        hasUnsavedChanges = false;
        showToast('Settings saved successfully', 'success');
    }
}
```

#### 4.3 Add Visual Feedback for Toggle Changes
**File:** `assets/css/company/settings.css`

```css
.notification-item.changed {
    background-color: #fff3cd;
    border-left: 3px solid #ffc107;
    transition: all 0.3s ease;
}

.notification-item {
    transition: all 0.3s ease;
}
```

**File:** `views/company/settings.php`

```javascript
function initializeChangeDetection() {
    const toggles = [
        'emailRepairRequests', 'emailProjectUpdates', 'emailPayments',
        'emailTeamActivity', 'emailMessages', 'pushDesktop', 'pushMobile'
    ];
    
    toggles.forEach(id => {
        document.getElementById(id).addEventListener('change', (e) => {
            // Highlight changed item
            const item = e.target.closest('.notification-item');
            if (item) {
                item.classList.add('changed');
            }
            
            const hasChanges = checkForChanges();
            document.getElementById('saveNotificationsBtn').disabled = !hasChanges;
            document.getElementById('saveAllBtn').disabled = !hasChanges;
        });
    });
}
```

---

## 🧪 Testing Checklist

### Phase 1 Testing:
- [ ] Toggle each notification switch
- [ ] Click Save - verify success toast appears
- [ ] Refresh page - verify toggles maintain saved state
- [ ] Check database - verify `companysettings` table has correct values
- [ ] Test with no existing settings (new company)
- [ ] Test "Cancel" button reverts changes
- [ ] Verify save button is disabled when no changes

### Phase 2 Testing:
- [ ] Change quiet hours start time
- [ ] Change quiet hours end time
- [ ] Save and verify in database
- [ ] Refresh and verify times are loaded correctly

### Phase 3 Testing:
- [ ] View active sessions list
- [ ] Open site in different browser - verify new session appears
- [ ] Click "Revoke" on non-current session - verify it disappears
- [ ] Click "Revoke All Other Sessions" - verify only current remains
- [ ] Verify login history shows activity

### Phase 4 Testing:
- [ ] Make changes and try to navigate away - verify warning appears
- [ ] Make changes, save, then navigate - no warning should appear
- [ ] Visual feedback shows on toggle change
- [ ] Loading spinner appears during save

---

## 🐛 Common Issues & Solutions

### Issue 1: Settings Not Saving
**Symptoms:** Toast shows success but data doesn't persist
**Debug:**
```javascript
// Add to saveSettings():
console.log('Sending data:', settings);
console.log('Response:', result);
```
**Check:**
- Database credentials correct
- `companysettings` table exists
- `company_id` matches logged-in user

### Issue 2: Toggles Don't Load on Page Load
**Symptoms:** All toggles reset to default
**Debug:**
```javascript
// Add to populateSettings():
console.log('Received data:', data);
```
**Check:**
- API returns correct data structure
- Field names match (snake_case vs camelCase)
- Session contains valid `user_id`

### Issue 3: Sessions Not Tracking
**Symptoms:** No sessions appear in list
**Check:**
- `user_sessions` table created
- `trackUserSession()` is called after login
- Session ID is valid

---

## 📊 Database Schema Reference

```sql
-- companysettings table
CREATE TABLE `companysettings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email_repair_requests` tinyint(1) DEFAULT 1,
  `email_project_updates` tinyint(1) DEFAULT 1,
  `email_payments` tinyint(1) DEFAULT 1,
  `email_team_activity` tinyint(1) DEFAULT 0,
  `email_messages` tinyint(1) DEFAULT 1,
  `push_desktop` tinyint(1) DEFAULT 1,
  `push_mobile` tinyint(1) DEFAULT 0,
  `quiet_hours_start` time DEFAULT '22:00:00',
  `quiet_hours_end` time DEFAULT '08:00:00',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `unique_company` (`company_id`)
);
```

---

## 🎯 Priority Order for Implementation

1. **START HERE:** Phase 1 - Core Toggle Functionality (1-2 hours)
2. **NEXT:** Phase 2 - Quiet Hours Feature (30 minutes)
3. **THEN:** Phase 1.3 & 1.4 - Loading States & Change Detection (1 hour)
4. **OPTIONAL:** Phase 3 - Session Management (2-3 hours)
5. **POLISH:** Phase 4 - Enhanced UX (1 hour)

---

## 📝 Quick Start Commands

```powershell
# 1. Backup database first
mysqldump -u root fixlanka > backup_before_settings_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql

# 2. Open the settings page in browser
start http://localhost/2nd-Year-Group-Project/FixLanka/views/company/settings.php

# 3. Open browser console (F12) and monitor network tab
# Look for requests to: /api/settings.php
```

---

## 🚀 Next Steps

1. Read through this entire document
2. Start with Phase 1, section 1.2 - Test current system
3. Fix any issues found during testing
4. Proceed through phases in order
5. Test after each phase
6. Move to next phase only when current phase works

---

## 💡 Tips

- Always test in browser console first
- Check Network tab for API responses
- Use `console.log()` liberally
- Commit after each working phase
- Keep database backups
- Test with multiple companies/users

---

**Last Updated:** January 18, 2026
**Status:** Ready for Implementation
**Estimated Time:** 4-6 hours for full implementation
