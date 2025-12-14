# Workforce Page - Testing Checklist

## 🧪 Manual Testing Guide

### Pre-Testing Setup
- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] Logged in as a Company user
- [ ] Browser console open (F12) to check for errors

---

## ✅ Test 1: Page Load
**URL:** `http://localhost/2nd-Year-Group-Project/FixLanka/views/company/workforce.php`

- [ ] Page loads without errors
- [ ] No JavaScript errors in console
- [ ] Dashboard view shows all 3 cards (Employees, Freelancers, Applications)
- [ ] Sidebar highlights "Workforce" as active
- [ ] Top navigation bar displays correctly

**Expected Results:**
- Company Employees card shows employee count
- Freelancers card shows "0 Available" and "No freelancers available yet"
- Applications card shows application count

---

## ✅ Test 2: Company Employees Section

### 2.1 View All Staff
- [ ] Click "View All Staff" button on Company Employees card
- [ ] Page transitions to employees detail view
- [ ] Employee categories load (Electrician, Plumber, Carpenter, etc.)
- [ ] Each category shows correct count

**Check Console:** Should see successful API call to `company-employees.php`

### 2.2 View Employees by Category
- [ ] Click "View All" on any employee category
- [ ] Modal/drawer opens (or section expands)
- [ ] Employee list displays for that category
- [ ] Employee cards show:
  - [ ] Profile photo or auto-generated avatar (initials)
  - [ ] Name (no "NULL" text)
  - [ ] Email (no "NULL" text)
  - [ ] Phone (no "NULL" text)
  - [ ] Specialty
  - [ ] Hourly rate
  - [ ] Rating

### 2.3 Add/Reduce Staff
- [ ] "+/- Add or Reduce Staff" button works
- [ ] Can increase employee count
- [ ] Can decrease employee count
- [ ] Changes save to database
- [ ] UI updates after changes

### 2.4 Back to Dashboard
- [ ] Click "← Back to Dashboard" button
- [ ] Returns to dashboard view
- [ ] All cards visible again

---

## ✅ Test 3: Freelancers Section

### 3.1 View Freelancers
- [ ] Click "View Freelancers" button
- [ ] Section expands/transitions
- [ ] Shows "No Freelancers Available" empty state
- [ ] Message is centered and styled properly

### 3.2 Freelancer Actions (Should Show Alerts)
If there are any freelancer action buttons visible:
- [ ] Clicking them shows alert: "This feature requires API integration"
- [ ] No JavaScript errors occur

---

## ✅ Test 4: Job Postings Section

### 4.1 View Job Postings
- [ ] Click "View Postings" button on Job Postings card
- [ ] Job postings section opens
- [ ] Job postings load from database
- [ ] Each job posting shows:
  - [ ] Job title
  - [ ] Specialty
  - [ ] Date posted
  - [ ] Status (Active/Closed)
  - [ ] Number of applications

### 4.2 Create Job Posting
- [ ] Click "Create Job Posting" button (top right)
- [ ] Multi-step form opens
- [ ] Step 1: Basic Information
  - [ ] Can enter job title
  - [ ] Can select specialty
  - [ ] Can set hourly rate
  - [ ] "Next" button works
- [ ] Step 2: Job Details
  - [ ] Can enter description
  - [ ] Can set requirements
  - [ ] "Next" button works
- [ ] Step 3: Review
  - [ ] Preview shows all entered data
  - [ ] "Submit" button works
- [ ] Job posting saves to database
- [ ] List updates with new posting

### 4.3 Filter Job Postings
- [ ] Click "All Postings" filter
- [ ] Click "Active" filter - shows only active
- [ ] Click "Closed" filter - shows only closed
- [ ] Click "Draft" filter - shows only drafts

### 4.4 View Applications for Job
- [ ] Click "View Applications" on a job posting
- [ ] Application list opens
- [ ] Shows all applicants for that job
- [ ] Each application shows applicant details

---

## ✅ Test 5: Applications Section

### 5.1 View Applications
- [ ] Click "View Applications" on Applications card
- [ ] Applications section opens
- [ ] All applications load from database
- [ ] Each application shows:
  - [ ] Applicant name
  - [ ] Specialty
  - [ ] Status (Pending/Approved/Rejected)
  - [ ] Date applied

### 5.2 Filter Applications
- [ ] "All" filter shows all applications
- [ ] "Pending" filter shows only pending
- [ ] "Approved" filter shows only approved
- [ ] "Rejected" filter shows only rejected

### 5.3 View Application Details
- [ ] Click on an application
- [ ] Details drawer/modal opens
- [ ] Shows full applicant information
- [ ] Shows resume/portfolio if available

### 5.4 Approve Application
- [ ] Click "Approve" button on pending application
- [ ] Confirmation appears
- [ ] Contract creation form opens (optional)
- [ ] Application status updates to "Approved"
- [ ] Database updates

### 5.5 Reject Application
- [ ] Click "Reject" button on pending application
- [ ] Confirmation appears
- [ ] Application status updates to "Rejected"
- [ ] Database updates

---

## ✅ Test 6: Search & Filter

### 6.1 Search Functionality
- [ ] Search box visible at top
- [ ] Type employee name
- [ ] Results filter in real-time
- [ ] Clear search works

### 6.2 Status Filters
- [ ] Status filter buttons work
- [ ] "All" shows everything
- [ ] Specific filters show correct items

---

## ✅ Test 7: UI/UX Elements

### 7.1 Buttons
Check all buttons follow the standard system:
- [ ] Primary buttons (teal/green color)
- [ ] Secondary buttons (gray/outlined)
- [ ] Danger buttons (red - for delete/reject)
- [ ] All buttons have hover effects
- [ ] All buttons have icons where appropriate

### 7.2 Responsive Design
- [ ] Resize browser window to mobile size
- [ ] Cards stack vertically
- [ ] Buttons remain accessible
- [ ] Text remains readable
- [ ] No horizontal scrolling

### 7.3 Empty States
- [ ] Empty states show appropriate messages
- [ ] Empty states have icons
- [ ] Messages are clear and helpful

### 7.4 Loading States
- [ ] Loading indicators show while fetching data
- [ ] Skeleton screens or spinners appear
- [ ] Content appears after loading

---

## ✅ Test 8: Error Handling

### 8.1 Network Errors
Simulate by stopping Apache briefly:
- [ ] Graceful error messages appear
- [ ] No blank screens
- [ ] User can retry

### 8.2 Invalid Data
- [ ] Form validation works
- [ ] Required fields can't be empty
- [ ] Invalid formats rejected
- [ ] Error messages are clear

---

## ✅ Test 9: Browser Console Check

Open browser console (F12) and verify:
- [ ] No JavaScript errors (red text)
- [ ] No undefined variable errors
- [ ] Only expected TODO warnings appear
- [ ] API calls succeed (green 200 status)

---

## ✅ Test 10: Database Verification

After testing, check MySQL database:
- [ ] New job postings saved correctly
- [ ] Application status updates saved
- [ ] Employee changes reflected
- [ ] No orphaned records
- [ ] Data integrity maintained

---

## 🐛 If You Find Issues:

### For Each Bug:
1. **Document it:**
   - What you did (steps to reproduce)
   - What you expected
   - What actually happened
   - Console errors (if any)

2. **Report it:**
   - Note the line number (if JS error)
   - Note the function name
   - Screenshot if visual issue

3. **Priority:**
   - 🔴 Critical: Page crashes, data loss
   - 🟡 Medium: Feature doesn't work, but no crash
   - 🟢 Low: Visual glitch, minor UX issue

---

## ✅ Success Criteria

The workforce page passes testing if:
- ✅ All core features work (employees, jobs, applications)
- ✅ No critical JavaScript errors
- ✅ Data saves and loads correctly
- ✅ UI is responsive and looks professional
- ✅ Empty states display properly (especially freelancers)
- ✅ Error handling works gracefully

---

## 📊 Testing Results Template

```
Date: _____________
Tester: _____________
Browser: _____________
Device: _____________

PASSED: ___/10 sections
FAILED: ___/10 sections

Critical Issues: ___
Medium Issues: ___
Low Issues: ___

Notes:
_________________________________
_________________________________
_________________________________

Overall Status: [ ] PASS  [ ] FAIL  [ ] NEEDS WORK
```

---

## 🎯 After Testing

Once all tests pass:
- [ ] Document any workarounds for known limitations
- [ ] Update user documentation
- [ ] Mark page as production-ready
- [ ] Move to next feature/page
