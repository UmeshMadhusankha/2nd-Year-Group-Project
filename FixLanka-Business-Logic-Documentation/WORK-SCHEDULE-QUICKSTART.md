# 🚀 QUICK START: Work Schedule Implementation

## 📋 30-Second Overview

**What:** Add work schedule transparency to quotations  
**Why:** Supervisor requirement - prevent customer confusion  
**Where:** Phase 1 of IMPLEMENTATION-PLAN.md  
**Time:** 4-6 hours total

---

## ⚡ Quick Implementation (Copy-Paste Ready)

### **Step 1: Database (2 minutes)**

```bash
cd c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka
```

Create file: `database/migrations/phase1_work_schedule.sql`

```sql
USE fix_lanka;

ALTER TABLE `companyquotation`
ADD COLUMN `work_schedule_type` ENUM('all_days', 'weekdays_only', 'weekends_included', 'custom') 
    NOT NULL DEFAULT 'weekdays_only' AFTER `estimated_duration`,
ADD COLUMN `working_days_per_week` TINYINT(1) NULL AFTER `work_schedule_type`,
ADD COLUMN `daily_work_hours` DECIMAL(4,2) NULL AFTER `working_days_per_week`,
ADD COLUMN `work_start_time` TIME NULL AFTER `daily_work_hours`,
ADD COLUMN `work_end_time` TIME NULL AFTER `work_start_time`,
ADD COLUMN `custom_schedule_details` TEXT NULL AFTER `work_end_time`,
ADD COLUMN `total_work_hours` DECIMAL(10,2) NULL AFTER `custom_schedule_details`,
ADD COLUMN `overtime_available` BOOLEAN DEFAULT FALSE AFTER `total_work_hours`,
ADD COLUMN `overtime_rate` DECIMAL(10,2) NULL AFTER `overtime_available`;

UPDATE `companyquotation` 
SET work_schedule_type = 'weekdays_only', working_days_per_week = 5, 
    daily_work_hours = 8.00, work_start_time = '08:00:00', 
    work_end_time = '17:00:00'
WHERE work_schedule_type IS NULL;
```

Run:
```bash
C:\xampp\mysql\bin\mysql.exe -u root fix_lanka < database/migrations/phase1_work_schedule.sql
```

---

### **Step 2: Backend Validation (5 minutes)**

Add to `models/CompanyQuotationModel.php`:

```php
public function validateWorkSchedule($data) {
    $errors = [];
    
    if (isset($data['working_days_per_week'])) {
        $days = (int)$data['working_days_per_week'];
        if ($days < 1 || $days > 7) {
            $errors[] = 'Working days must be between 1 and 7';
        }
    }
    
    if (isset($data['daily_work_hours'])) {
        $hours = (float)$data['daily_work_hours'];
        if ($hours <= 0 || $hours > 24) {
            $errors[] = 'Daily hours must be between 0 and 24';
        }
    }
    
    if (isset($data['work_start_time']) && isset($data['work_end_time'])) {
        if (strtotime($data['work_start_time']) >= strtotime($data['work_end_time'])) {
            $errors[] = 'End time must be after start time';
        }
    }
    
    return empty($errors) ? true : $errors;
}
```

---

### **Step 3: API Update (5 minutes)**

In `api/company-quotes.php`, add to `handlePost()`:

```php
// After existing quotation data
$workScheduleData = [
    'work_schedule_type' => $_POST['work_schedule_type'] ?? 'weekdays_only',
    'working_days_per_week' => (int)($_POST['working_days_per_week'] ?? 5),
    'daily_work_hours' => (float)($_POST['daily_work_hours'] ?? 8.00),
    'work_start_time' => $_POST['work_start_time'] ?? '08:00:00',
    'work_end_time' => $_POST['work_end_time'] ?? '17:00:00',
    'custom_schedule_details' => $_POST['custom_schedule_details'] ?? null,
    'total_work_hours' => (float)($_POST['total_work_hours'] ?? null),
    'overtime_available' => isset($_POST['overtime_available']),
    'overtime_rate' => (float)($_POST['overtime_rate'] ?? null)
];

// Validate
$validation = $quotationModel->validateWorkSchedule($workScheduleData);
if ($validation !== true) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $validation]);
    return;
}

// Merge and save
$quotationData = array_merge($existingData, $workScheduleData);
```

---

### **Step 4: Frontend Form (15 minutes)**

Add to quotation form (after existing fields):

```html
<div class="form-section">
    <h4>📅 Work Schedule</h4>
    
    <select name="work_schedule_type" id="work-schedule-type" required>
        <option value="weekdays_only" selected>Weekdays Only (Mon-Fri)</option>
        <option value="weekends_included">Weekends Included (Mon-Sat)</option>
        <option value="all_days">All Days (7 days/week)</option>
        <option value="custom">Custom Schedule</option>
    </select>
    
    <input type="number" name="working_days_per_week" id="working-days" 
           value="5" min="1" max="7" required>
    
    <input type="number" name="daily_work_hours" id="daily-hours" 
           value="8.00" step="0.5" required>
    
    <input type="time" name="work_start_time" value="08:00" required>
    <input type="time" name="work_end_time" value="17:00" required>
    
    <input type="number" name="total_work_hours" id="total-hours" readonly>
    
    <label>
        <input type="checkbox" name="overtime_available" id="overtime-check">
        Overtime Available
    </label>
    
    <input type="number" name="overtime_rate" id="overtime-rate" 
           step="0.01" style="display:none;">
    
    <textarea name="custom_schedule_details" id="custom-schedule" 
              style="display:none;"></textarea>
</div>
```

---

### **Step 5: JavaScript (15 minutes)**

Add to `repair-requests.js`:

```javascript
document.getElementById('work-schedule-type').addEventListener('change', function() {
    const type = this.value;
    const days = document.getElementById('working-days');
    const customField = document.getElementById('custom-schedule');
    
    if (type === 'weekdays_only') days.value = 5;
    else if (type === 'weekends_included') days.value = 6;
    else if (type === 'all_days') days.value = 7;
    
    customField.style.display = type === 'custom' ? 'block' : 'none';
    calculateTotalHours();
});

document.getElementById('overtime-check').addEventListener('change', function() {
    document.getElementById('overtime-rate').style.display = 
        this.checked ? 'block' : 'none';
});

function calculateTotalHours() {
    const duration = parseFloat(document.getElementById('estimated-duration').value) || 0;
    const daysPerWeek = parseFloat(document.getElementById('working-days').value) || 5;
    const hoursPerDay = parseFloat(document.getElementById('daily-hours').value) || 8;
    
    const weeks = Math.ceil(duration / 7);
    const totalHours = (weeks * daysPerWeek * hoursPerDay).toFixed(2);
    
    document.getElementById('total-hours').value = totalHours;
}

['estimated-duration', 'working-days', 'daily-hours'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateTotalHours);
});
```

---

## ✅ Testing (10 minutes)

### **Test 1: Standard Schedule**
```
Schedule Type: Weekdays Only
Days: 5
Hours: 8.00
Duration: 10 days
Expected Total Hours: 80.00 ✓
```

### **Test 2: Validation**
```
Try Days: 8 → Should show error ✓
Try Hours: 25 → Should show error ✓
Try End before Start → Should show error ✓
```

### **Test 3: Save & Display**
```
Submit quotation → Check database ✓
View quotation → See schedule info ✓
```

---

## 🎯 Success Checklist

- [ ] 9 columns added to database
- [ ] Validation working correctly
- [ ] Form fields appear
- [ ] Auto-calculation works
- [ ] Overtime field toggles
- [ ] Custom schedule shows/hides
- [ ] Data saves to database
- [ ] Schedule displays in view
- [ ] All 4 test scenarios pass

---

## 🆘 Quick Troubleshooting

**Problem:** Columns not added  
**Fix:** Check MySQL user has ALTER permission

**Problem:** JavaScript not working  
**Fix:** Check browser console for errors

**Problem:** Validation always fails  
**Fix:** Check field names match exactly

**Problem:** Total hours = 0  
**Fix:** Ensure duration field exists and has value

---

## 📚 Full Documentation

**Detailed Guide:** `WORK-SCHEDULE-FEATURE.md`  
**Implementation Plan:** `IMPLEMENTATION-PLAN.md` (Phase 1, Section 1.4)  
**Example Code:** All included in both documents

---

## 🎓 Key Concepts

**Calendar Days vs Work Days:**
- 10 calendar days with 5-day work week = 10÷7 = ~2 weeks = 10 work days
- 10 work days × 8 hours = 80 total work hours

**Schedule Types:**
- `weekdays_only` = Monday-Friday (5 days)
- `weekends_included` = Monday-Saturday (6 days)
- `all_days` = Every day (7 days)
- `custom` = User-defined pattern

**Overtime:**
- Typically 1.5x regular hourly rate
- Optional field, only shown if checkbox enabled
- Should be validated against regular hourly rate if exists

---

**Time Required:** 45-60 minutes for basic implementation  
**Status:** ✅ Ready to implement  
**Next:** Follow Step 1 above to start
