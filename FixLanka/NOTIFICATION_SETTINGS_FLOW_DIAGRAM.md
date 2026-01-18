# Notification Settings - Visual Flow Diagram

## 🔄 Complete Data Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER OPENS PAGE                         │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  settings.php (Frontend)                                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ DOMContentLoaded Event Fires                             │  │
│  │ → fetchSettings()                                        │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              │ GET Request
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  api/settings.php                                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. Check session & auth                                  │  │
│  │ 2. Create CompanyModel instance                          │  │
│  │ 3. Call $model->getSettings($companyId)                  │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  CompanyModel.php                                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ getSettings($companyId)                                  │  │
│  │ SELECT * FROM companysettings WHERE company_id = ?       │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  Database (MySQL)                                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ companysettings table                                    │  │
│  │ ┌────────────────────────────────────────────────────┐   │  │
│  │ │ company_id: 1                                      │   │  │
│  │ │ email_repair_requests: 1                          │   │  │
│  │ │ email_project_updates: 1                          │   │  │
│  │ │ email_payments: 1                                 │   │  │
│  │ │ email_team_activity: 0                            │   │  │
│  │ │ email_messages: 1                                 │   │  │
│  │ │ push_desktop: 1                                   │   │  │
│  │ │ push_mobile: 0                                    │   │  │
│  │ │ quiet_hours_start: 22:00:00                       │   │  │
│  │ │ quiet_hours_end: 08:00:00                         │   │  │
│  │ └────────────────────────────────────────────────────┘   │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              │ Returns Data
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  settings.php (Frontend)                                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ populateSettings(data)                                   │  │
│  │ → Set all toggle checked states                         │  │
│  │ → Set quiet hours time values                           │  │
│  │ → storeOriginalSettings()                               │  │
│  │ → updateSaveButtonState(false) ← Disable save buttons   │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       PAGE READY TO USE                         │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  [✓] New Repair Requests        [OFF]                   │  │
│  │  [✓] Project Updates             [OFF]                   │  │
│  │  [✓] Payment Notifications       [OFF]                   │  │
│  │  [ ] Team Activity               [OFF]                   │  │
│  │  [✓] Customer Messages           [OFF]                   │  │
│  │                                                          │  │
│  │  Save button: [DISABLED - no changes]                   │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 👆 User Makes Changes

```
┌─────────────────────────────────────────────────────────────────┐
│  USER TOGGLES A SWITCH                                          │
│  (e.g., Turn OFF "Project Updates")                             │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ 'change' event
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  Event Listener (initializeChangeDetection)                     │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. Get parent .notification-item                         │  │
│  │ 2. Compare with originalSettings                         │  │
│  │ 3. If different → Add .changed class (yellow highlight)  │  │
│  │ 4. Call checkForChanges()                                │  │
│  │ 5. Call updateSaveButtonState(true)                      │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  VISUAL FEEDBACK                                                │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  [✓] New Repair Requests        [OFF]                   │  │
│  │  ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓  │  │
│  │  ┃ [  ] Project Updates         [OFF] 🟡 CHANGED       ┃  │  │
│  │  ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛  │  │
│  │  [✓] Payment Notifications       [OFF]                   │  │
│  │                                                          │  │
│  │  Save button: [ENABLED - 1 change detected]             │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 💾 User Saves Changes

```
┌─────────────────────────────────────────────────────────────────┐
│  USER CLICKS "SAVE PREFERENCES"                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  saveSettings() Function                                        │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. Show spinner: "🔄 Saving..."                          │  │
│  │ 2. Disable both save buttons                            │  │
│  │ 3. Collect all toggle values                            │  │
│  │ 4. Collect quiet hours values                           │  │
│  │ 5. POST to /api/settings.php                            │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              │ POST {action: "update_notifications", settings: {...}}
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  api/settings.php                                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. Decode JSON input                                     │  │
│  │ 2. Validate action = "update_notifications"              │  │
│  │ 3. Call $model->updateSettings($companyId, $settings)    │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  CompanyModel.php                                               │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ updateSettings($companyId, $data)                        │  │
│  │ 1. Map frontend keys to DB columns                       │  │
│  │    emailProjectUpdates → email_project_updates = 0       │  │
│  │ 2. Build SQL INSERT ... ON DUPLICATE KEY UPDATE          │  │
│  │ 3. Execute with prepared statement                       │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  Database Update                                                │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ UPDATE companysettings SET                               │  │
│  │   email_project_updates = 0,                             │  │
│  │   updated_at = NOW()                                     │  │
│  │ WHERE company_id = 1                                     │  │
│  └──────────────────────────┬───────────────────────────────┘  │
└─────────────────────────────┼────────────────────────────────────┘
                              │
                              │ Success!
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│  Response Chain                                                 │
│  Database → CompanyModel → API → Frontend                       │
│  {success: true, message: "Settings saved successfully"}        │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  saveSettings() Success Handler                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ 1. hasUnsavedChanges = false                             │  │
│  │ 2. Call storeOriginalSettings() → Clear yellow           │  │
│  │ 3. showToast("Settings saved successfully", "success")   │  │
│  │ 4. Re-enable buttons                                     │  │
│  │ 5. Restore original button text                          │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│  VISUAL RESULT                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  [✓] New Repair Requests        [OFF]                   │  │
│  │  [ ] Project Updates             [OFF] ✅ SAVED          │  │
│  │  [✓] Payment Notifications       [OFF]                   │  │
│  │                                                          │  │
│  │  ┌───────────────────────────────────────────────────┐  │  │
│  │  │ ✅ Success! Settings saved successfully           │  │  │
│  │  └───────────────────────────────────────────────────┘  │  │
│  │                                                          │  │
│  │  Save button: [DISABLED - no unsaved changes]           │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 State Diagram

```
┌─────────────────┐
│  INITIAL STATE  │  → Page loads
│  • No changes   │
│  • Save disabled│
└────────┬────────┘
         │
         │ User toggles switch
         ▼
┌─────────────────┐
│  CHANGED STATE  │
│  • Yellow HL    │
│  • Save enabled │
└────┬───────┬────┘
     │       │
     │       │ User clicks Save
     │       ▼
     │    ┌─────────────────┐
     │    │  SAVING STATE   │
     │    │  • Spinner      │
     │    │  • Disabled     │
     │    └────────┬────────┘
     │             │
     │             │ API Success
     │             ▼
     │    ┌─────────────────┐
     │    │  SAVED STATE    │──┐
     │    │  • Toast shows  │  │
     │    │  • HL cleared   │  │
     │    └────────┬────────┘  │
     │             │            │
     │             └────────────┘
     │             Back to INITIAL
     │
     │ User clicks Cancel
     ▼
┌─────────────────┐
│  CANCELLED      │
│  • Confirm?     │
│  → Reload data  │
└────────┬────────┘
         │
         │ Back to INITIAL
         ▼
```

---

## 🎨 Component Interaction Map

```
┌─────────────────────────────────────────────────────────────┐
│                     SETTINGS PAGE UI                        │
│                                                             │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Topbar     │  │  Sidebar     │  │  Main Panel  │      │
│  └─────────────┘  └──────────────┘  └──────┬───────┘      │
│                                             │              │
│       ┌─────────────────────────────────────┘              │
│       │                                                    │
│  ┌────▼──────────────────────────────────────────────┐    │
│  │           NOTIFICATIONS TAB (Active)              │    │
│  ├───────────────────────────────────────────────────┤    │
│  │  ┌────────────────────────────────────────────┐   │    │
│  │  │  Email Notifications Section             │   │    │
│  │  │  ┌────────────────────────────────┐      │   │    │
│  │  │  │ [✓] New Repair Requests  [▓▓▓] │◄─────┼───┼────┐
│  │  │  ├────────────────────────────────┤      │   │    │
│  │  │  │ [✓] Project Updates     [▓▓▓] │◄─────┼───┼────┤
│  │  │  ├────────────────────────────────┤      │   │    │
│  │  │  │ [✓] Payment Notify      [▓▓▓] │      │   │    │
│  │  │  └────────────────────────────────┘      │   │    │
│  │  └────────────────────────────────────────┘   │    │
│  │                                                │    │
│  │  ┌────────────────────────────────────────────┐   │    │
│  │  │  Push Notifications Section              │   │    │
│  │  │  ┌────────────────────────────────┐      │   │    │
│  │  │  │ [✓] Desktop Push        [▓▓▓] │      │   │    │
│  │  │  ├────────────────────────────────┤      │   │    │
│  │  │  │ [ ] Mobile Push         [▓▓▓] │      │   │    │
│  │  │  └────────────────────────────────┘      │   │    │
│  │  └────────────────────────────────────────┘   │    │
│  │                                                │    │
│  │  ┌────────────────────────────────────────────┐   │    │
│  │  │  Quiet Hours Section                     │   │    │
│  │  │  From: [22:00] To: [08:00]               │   │    │
│  │  └────────────────────────────────────────┘   │    │
│  │                                                │    │
│  │  ┌──────────────┐  ┌──────────────┐          │    │
│  │  │   Cancel     │  │ Save Changes │          │    │
│  │  └──────────────┘  └──────────────┘          │    │
│  └───────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
         │                                │
         │                                │
         │ Event Listeners                │ Event Listeners
         │ (change detection)             │ (save/cancel)
         ▼                                ▼
┌─────────────────────┐        ┌──────────────────────┐
│  JavaScript Engine  │◄──────►│   State Manager      │
│  • Event handlers   │        │  • originalSettings  │
│  • DOM manipulation │        │  • hasUnsavedChanges │
└──────────┬──────────┘        └───────────┬──────────┘
           │                               │
           │                               │
           │                               │
           ▼                               ▼
┌─────────────────────────────────────────────────────────┐
│                    API Layer                            │
│  fetch('/api/settings.php')                             │
│  • GET: Load settings                                   │
│  • POST: Save settings                                  │
└────────────────────────┬────────────────────────────────┘
                         │
                         │ HTTP
                         ▼
┌─────────────────────────────────────────────────────────┐
│                  Backend (PHP)                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │ settings.php │→ │CompanyModel  │→ │   Database   │  │
│  │  (API)       │  │   (Model)    │  │   (MySQL)    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

## 📊 Data Structure

### Frontend (JavaScript):
```javascript
originalSettings = {
    emailRepairRequests: true,      // boolean
    emailProjectUpdates: true,      // boolean
    emailPayments: true,            // boolean
    emailTeamActivity: false,       // boolean
    emailMessages: true,            // boolean
    pushDesktop: true,              // boolean
    pushMobile: false,              // boolean
    quietHoursStart: "22:00",       // string (HH:MM)
    quietHoursEnd: "08:00"          // string (HH:MM)
}

hasUnsavedChanges = false;          // boolean
```

### API Request:
```json
POST /api/settings.php
{
    "action": "update_notifications",
    "settings": {
        "emailRepairRequests": true,
        "emailProjectUpdates": false,
        "emailPayments": true,
        "emailTeamActivity": false,
        "emailMessages": true,
        "pushDesktop": true,
        "pushMobile": false,
        "quietHoursStart": "22:00",
        "quietHoursEnd": "08:00"
    }
}
```

### API Response:
```json
{
    "success": true,
    "message": "Settings saved successfully"
}
```

### Database (MySQL):
```sql
companysettings
├── setting_id: 1
├── company_id: 1
├── email_repair_requests: 1       (TINYINT)
├── email_project_updates: 0       (TINYINT)
├── email_payments: 1              (TINYINT)
├── email_team_activity: 0         (TINYINT)
├── email_messages: 1              (TINYINT)
├── push_desktop: 1                (TINYINT)
├── push_mobile: 0                 (TINYINT)
├── quiet_hours_start: 22:00:00    (TIME)
├── quiet_hours_end: 08:00:00      (TIME)
└── updated_at: 2026-01-18 14:30:00 (TIMESTAMP)
```

---

## 🎬 Animation Timeline

```
User Toggles Switch
    ↓
    0ms   → 'change' event fires
    ↓
   10ms   → JavaScript detects change
    ↓
   20ms   → Add .changed class to item
    ↓
   30ms   → CSS animation starts
    |        ┌─────────────────────────────┐
    |        │ @keyframes highlightChange  │
    |        │ 0%:   scale(1)              │
   280ms →  │ 250ms: scale(1.02)          │
    |        │ 500ms: scale(1)             │
    |        └─────────────────────────────┘
   530ms   → Animation complete
    ↓
   540ms   → Item now steady with yellow background
    ↓
           → Save button enabled
           → User sees feedback complete
```

---

**Diagrams Created:** January 18, 2026
**Purpose:** Visual understanding of notification settings system
**Use:** Reference for development and debugging
