# 📅 Work Schedule Specification Feature

**Date:** January 28, 2026  
**Status:** 📋 PLANNED - Ready for Implementation  
**Phase:** Phase 1 - Database Enhancement  
**Priority:** HIGH (Supervisor Requirement)

---

## 🎯 Problem Statement

**Supervisor Feedback:**
> "When we send a quotation, we need to specify clearly: Are we working all days or only weekdays? What are the specific hours per day? This creates transparency and prevents misunderstandings between company and customer."

**Current Issue:**
- Quotations only show `estimated_duration` (number of days)
- No clarity on working days vs calendar days
- No specification of daily work hours
- No mention of work start/end times
- Customers may assume 24/7 work when company means weekdays only

**Impact:**
- ❌ Customer confusion about timeline
- ❌ Unrealistic expectations
- ❌ Potential disputes
- ❌ Unprofessional appearance

---

## ✅ Solution: Work Schedule Fields

### **9 New Database Columns**

| Column Name | Type | Purpose | Example |
|-------------|------|---------|---------|
| `work_schedule_type` | ENUM | Quick schedule indicator | `'weekdays_only'`, `'all_days'`, `'custom'` |
| `working_days_per_week` | TINYINT(1) | Number of working days | `5`, `6`, `7` |
| `daily_work_hours` | DECIMAL(4,2) | Hours worked per day | `8.00`, `6.50`, `10.00` |
| `work_start_time` | TIME | Daily start time | `'08:00:00'`, `'09:00:00'` |
| `work_end_time` | TIME | Daily end time | `'17:00:00'`, `'18:00:00'` |
| `custom_schedule_details` | TEXT | Free-text schedule notes | "Mon-Thu: 8hrs, Fri: 6hrs..." |
| `total_work_hours` | DECIMAL(10,2) | Total project hours | `120.00` (15 days × 8 hrs) |
| `overtime_available` | BOOLEAN | Overtime possible? | `TRUE`/`FALSE` |
| `overtime_rate` | DECIMAL(10,2) | Cost per overtime hour | `1500.00` LKR/hr |

---

## 🏗️ Implementation Structure

### **Files to Create/Modify:**

#### 1. Database Migration
- **File:** `database/migrations/phase1_work_schedule.sql`
- **Action:** Add 9 columns to `companyquotation` table
- **Run:** `mysql -u root -p fix_lanka < phase1_work_schedule.sql`

#### 2. Backend Model
- **File:** `models/CompanyQuotationModel.php`
- **Updates:**
  - Add work schedule fields to `create()` method
  - Add `calculateTotalWorkHours()` helper method
  - Add `validateWorkSchedule()` validation method

#### 3. API Endpoint
- **File:** `api/company-quotes.php`
- **Updates:**
  - Add work schedule data extraction in `handlePost()`
  - Add validation before quotation creation
  - Return work schedule in quotation details

#### 4. Frontend Form
- **File:** `views/company/repair-requests.php`
- **Add:** Complete work schedule section with:
  - Schedule type dropdown
  - Working days input
  - Daily hours input
  - Start/end time pickers
  - Overtime checkbox and rate
  - Custom schedule textarea
  - Live preview box

#### 5. Frontend JavaScript
- **File:** `assets/javascript/company/repair-requests.js`
- **Add Functions:**
  - `initializeWorkSchedule()` - Setup event listeners
  - `calculateTotalWorkHours()` - Auto-calculation
  - `updateSchedulePreview()` - Real-time preview
  - `formatTime()` - Time formatting helper

#### 6. CSS Styling
- **File:** `assets/css/company/quotations.css`
- **Add:** Styling for work schedule section and preview box

#### 7. Display Template
- **File:** Quotation view template
- **Add:** Work schedule information display with visual timeline

---

## 📋 Implementation Steps

### **Step 1: Database (15 minutes)**
```bash
cd c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka
C:\xampp\mysql\bin\mysql.exe -u root fix_lanka < database/migrations/phase1_work_schedule.sql
```

**Verify:**
```sql
DESCRIBE companyquotation;
-- Should see 9 new columns
```

---

### **Step 2: Backend Model (30 minutes)**

Add to `CompanyQuotationModel.php`:
```php
// 1. Update create() method - add work schedule parameters
// 2. Add calculateTotalWorkHours() method
// 3. Add validateWorkSchedule() method
```

**Test:**
```php
$validation = $model->validateWorkSchedule([
    'working_days_per_week' => 5,
    'daily_work_hours' => 8.00,
    'work_start_time' => '08:00:00',
    'work_end_time' => '17:00:00'
]);
// Should return true
```

---

### **Step 3: Frontend Form (45 minutes)**

Add HTML section after existing quotation form fields:
- Schedule type selector
- Working days/hours inputs
- Time pickers
- Overtime fields
- Custom schedule textarea
- Preview box

**Test:**
- Open quotation form
- See new "Work Schedule Specifications" section
- Fields should have default values (weekdays, 5 days, 8 hours)

---

### **Step 4: Frontend JavaScript (45 minutes)**

Add functionality:
- Auto-update working days based on schedule type
- Show/hide custom schedule field
- Show/hide overtime rate field
- Auto-calculate total work hours
- Update preview in real-time

**Test:**
- Change schedule type → working days auto-update
- Change duration/hours → total hours recalculate
- Enable overtime → rate field appears
- Preview updates instantly

---

### **Step 5: API Integration (20 minutes)**

Update `api/company-quotes.php`:
- Extract work schedule POST data
- Validate before saving
- Include in quotation retrieval

**Test:**
```javascript
// Submit quotation with work schedule
fetch('/api/company-quotes.php', {
    method: 'POST',
    body: formDataWithSchedule
});
// Check database - work schedule fields saved
```

---

### **Step 6: Display Template (30 minutes)**

Add work schedule display to quotation view:
- Info grid with schedule details
- Visual timeline bar (optional)
- Overtime badge if enabled

**Test:**
- View submitted quotation
- See work schedule information displayed
- Timeline bar shows work period correctly

---

## 🧪 Testing Scenarios

### **Scenario 1: Standard Weekday Schedule**
```
Input:
- Schedule Type: Weekdays Only
- Working Days: 5 days/week
- Daily Hours: 8 hours/day
- Work Time: 8:00 AM - 5:00 PM
- Estimated Duration: 10 days
- Overtime: No

Expected Output:
- Total Work Hours: 80.00 (10 days ÷ 7 × 5 days × 8 hrs = 80)
- Display: "Weekdays Only (Mon-Fri), 5 days/week, 8 hours/day, 8:00 AM - 5:00 PM"
```

---

### **Scenario 2: Weekend Work Included**
```
Input:
- Schedule Type: Weekends Included
- Working Days: 6 days/week
- Daily Hours: 8 hours/day
- Work Time: 8:00 AM - 5:00 PM
- Estimated Duration: 14 days
- Overtime: Yes at LKR 1,800/hour

Expected Output:
- Total Work Hours: 96.00 (14 days ÷ 7 × 6 days × 8 hrs)
- Display: "Weekends Included (Mon-Sat), 6 days/week, 8 hours/day, Overtime available at LKR 1,800/hr"
```

---

### **Scenario 3: Urgent 24/7 Project**
```
Input:
- Schedule Type: All Days
- Working Days: 7 days/week
- Daily Hours: 12 hours/day
- Work Time: 6:00 AM - 6:00 PM
- Estimated Duration: 7 days
- Overtime: Yes at LKR 2,500/hour
- Custom Details: "Rotating shifts. Team works 6am-6pm and 6pm-6am shifts."

Expected Output:
- Total Work Hours: 84.00 (7 days × 12 hrs)
- Display: Shows custom schedule details prominently
```

---

### **Scenario 4: Part-Time/Custom Schedule**
```
Input:
- Schedule Type: Custom
- Working Days: 3 days/week
- Daily Hours: 4 hours/day
- Work Time: 2:00 PM - 6:00 PM
- Estimated Duration: 21 days
- Custom Details: "Monday, Wednesday, Friday only. Afternoon shifts."

Expected Output:
- Total Work Hours: 36.00 (21 days ÷ 7 × 3 days × 4 hrs)
- Display: Shows custom schedule details, highlights part-time nature
```

---

## 📊 Business Benefits

### **For Companies:**
✅ Professional quotations with clear terms  
✅ Reduced customer confusion  
✅ Better expectation management  
✅ Fewer disputes about timelines  
✅ Ability to specify custom schedules  
✅ Transparency builds trust  

### **For Customers:**
✅ Clear understanding of work schedule  
✅ Can plan around company's work times  
✅ Knows exactly when workers will be present  
✅ Understands why duration is X days (calendar vs work days)  
✅ Sees overtime availability upfront  
✅ Makes informed acceptance decisions  

### **For Platform:**
✅ Comprehensive quotation system  
✅ Reduced support tickets  
✅ Professional image  
✅ Competitive advantage  
✅ Industry best practice  

---

## 🔍 Validation Rules

### **Working Days Per Week**
- **Range:** 1-7
- **Error:** "Working days must be between 1 and 7"

### **Daily Work Hours**
- **Range:** 0.5-24.0
- **Error:** "Daily hours must be between 0.5 and 24"

### **Work Times**
- **Rule:** `work_end_time` > `work_start_time`
- **Error:** "Work end time must be after start time"
- **Exception:** Overnight shifts (future enhancement)

### **Overtime Rate**
- **Rule:** Required if `overtime_available` = TRUE
- **Rule:** Must be > 0
- **Error:** "Overtime rate is required when overtime is available"
- **Recommendation:** Should be ≥ 1.5x regular hourly rate

### **Custom Schedule Details**
- **Rule:** Required if `work_schedule_type` = 'custom'
- **Min Length:** 20 characters
- **Error:** "Please provide detailed custom schedule information"

### **Total Work Hours**
- **Auto-calculated:** Cannot be manually edited
- **Formula:** `ceil(estimated_duration / 7) * working_days_per_week * daily_work_hours`

---

## 🎨 UI/UX Design

### **Form Section Layout**
```
┌─────────────────────────────────────────────────────┐
│  📅 Work Schedule Specifications                    │
│  Specify your working days and hours for transparency│
│                                                       │
│  [Schedule Type ▼]        [Working Days: 5  ]       │
│                                                       │
│  [Daily Hours: 8.00]  [Start: 08:00]  [End: 17:00]  │
│                                                       │
│  [Total Work Hours: 80.00 (auto-calculated)]        │
│                                                       │
│  ☐ Overtime Available  [Rate: _____ LKR/hr]         │
│                                                       │
│  ┌─ Schedule Preview ─────────────────────────┐     │
│  │ 📊 Schedule: Weekdays Only (Mon-Fri)       │     │
│  │ 💼 Working Days: 5 days per week           │     │
│  │ ⏰ Daily Hours: 8.00 hours/day             │     │
│  │ 🕐 Work Time: 8:00 AM - 5:00 PM            │     │
│  │ 🧮 Total Project Hours: 80.00 hours        │     │
│  └─────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────┘
```

### **Display View Layout**
```
┌─────────────────────────────────────────────────────┐
│  📅 Work Schedule                                   │
│                                                       │
│  Schedule Type:    [Weekdays Only (Mon-Fri)]        │
│  Working Days:     5 days per week                   │
│  Daily Hours:      8.00 hours/day                    │
│  Work Time:        8:00 AM - 5:00 PM                 │
│  Total Hours:      80.00 hours                       │
│  Overtime:         ✓ Available at LKR 1,800/hour    │
│                                                       │
│  ├── Typical Work Day ───────────────────────────┤  │
│  │12AM  6AM  [████████████]  6PM  12AM│          │  │
│  └──────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────┘
```

---

## 📚 Documentation Updates

### **Files to Update:**
1. ✅ **IMPLEMENTATION-PLAN.md** - Updated Phase 1, Section 1.1 & 1.4
2. ⬜ **API-DOCUMENTATION.md** - Add work schedule fields to quotation endpoints
3. ⬜ **USER-GUIDE.md** - Add section on work schedule specifications
4. ⬜ **COMPANY-MANUAL.md** - How to fill work schedule section
5. ⬜ **FAQ.md** - Common questions about work schedules

### **Code Comments:**
- Add PHPDoc comments to new methods
- Add inline comments explaining calculations
- Document validation rules
- Add examples in comments

---

## 🚀 Future Enhancements (Optional)

### **Phase 2 Additions:**
- **Holiday Calendar:** Exclude public holidays from work days
- **Break Time Tracking:** Specify lunch/tea breaks
- **Multiple Shifts:** Support for shift work (day/night)
- **Weather Dependency:** Flag for weather-dependent work
- **Site Availability:** Customer's site access hours

### **Advanced Features:**
- **Calendar Integration:** Visual calendar showing work days
- **Conflict Detection:** Alert if dates conflict with holidays
- **Automatic Adjustments:** Auto-adjust duration based on holidays
- **Weather Forecast:** Integration for outdoor work
- **Resource Planning:** Link to worker availability

---

## 📞 Support & Questions

**Implementation Questions:**
- Contact development team lead
- Reference: IMPLEMENTATION-PLAN.md Section 1.4

**Business Logic Questions:**
- Contact project supervisor
- Reference: This document

**Testing Issues:**
- Follow testing checklist in Section 1.5
- Document any edge cases found

---

## ✅ Implementation Checklist

### **Before Starting:**
- [ ] Read this document completely
- [ ] Review IMPLEMENTATION-PLAN.md Phase 1
- [ ] Backup current database
- [ ] Backup current code files

### **Database:**
- [ ] Run migration SQL
- [ ] Verify 9 columns added
- [ ] Check default values set
- [ ] Test with sample data

### **Backend:**
- [ ] Update CompanyQuotationModel.php
- [ ] Add validation method
- [ ] Add calculation method
- [ ] Update API endpoint
- [ ] Test with Postman/cURL

### **Frontend:**
- [ ] Add form fields to quotation form
- [ ] Add JavaScript functions
- [ ] Add CSS styling
- [ ] Test in browser (Chrome, Firefox, Edge)
- [ ] Test mobile responsiveness

### **Display:**
- [ ] Update quotation view template
- [ ] Add work schedule display section
- [ ] Add visual timeline (optional)
- [ ] Test with different scenarios

### **Testing:**
- [ ] Test standard weekday schedule
- [ ] Test weekend work included
- [ ] Test 24/7 schedule
- [ ] Test custom/part-time schedule
- [ ] Test overtime functionality
- [ ] Test validation (invalid inputs)
- [ ] Test total hours calculation

### **Documentation:**
- [ ] Update API documentation
- [ ] Update user guide
- [ ] Add code comments
- [ ] Update FAQ

### **Deployment:**
- [ ] Test on staging environment
- [ ] Get supervisor approval
- [ ] Deploy to production
- [ ] Monitor for issues
- [ ] Gather user feedback

---

## 📈 Success Metrics

**After Implementation, measure:**
- Reduction in customer questions about work schedule
- Reduction in disputes about timeline expectations
- Increase in quotation acceptance rate
- Improvement in customer satisfaction scores
- Decrease in contract amendments due to schedule confusion

**Target:**
- 50% reduction in schedule-related support tickets
- 90%+ of quotations include detailed work schedule
- Positive feedback from supervisors and customers

---

**Document Version:** 1.0  
**Last Updated:** January 28, 2026  
**Status:** 📋 Ready for Implementation  
**Estimated Implementation Time:** 3-4 hours  
**Estimated Testing Time:** 1-2 hours  
**Total Time:** 4-6 hours
