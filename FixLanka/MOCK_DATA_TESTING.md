# Mock Data Testing Guide

## 🎯 Overview
The quotation system now includes **6 pre-loaded mock quotations** for easy testing without manual data entry.

---

## 📊 Mock Data Breakdown

### Pending Quotations (3)
1. **Kitchen Cabinet Repair** - `QUOT-1729670400000`
   - Total: LKR 47,000
   - Duration: 8 days
   - Status: Pending

2. **Bathroom Plumbing Repair** - `QUOT-1729670500000`
   - Total: LKR 85,000
   - Duration: 5 days
   - Status: Pending

3. **Living Room Floor Tiling** - `QUOT-1729670800000`
   - Total: LKR 118,000
   - Duration: 8 days
   - Status: Pending

### Accepted Quotations (3)
1. **Roof Leak Repair** - `QUOT-1729670600000`
   - Total: LKR 90,000
   - Duration: 11 days
   - Status: Accepted

2. **Electrical Wiring Upgrade** - `QUOT-1729670700000`
   - Total: LKR 165,000
   - Duration: 15 days
   - Status: Accepted

3. **Window Frame Replacement** - `QUOT-1729670900000`
   - Total: LKR 152,000
   - Duration: 12 days
   - Status: Accepted

---

## 🧪 Testing Workflow

### 1. Initial Load
1. Open the repair requests page
2. Navigate to **Logs** tab
3. You should see:
   - **3 pending quotations** with Edit/Delete buttons
   - **3 accepted quotations** (read-only)
   - Count badges showing correct numbers

### 2. Test View Functionality
```
✅ Click "View" button (eye icon) on any quotation
✅ Verify complete details displayed in alert
✅ Check all cost breakdowns are shown
✅ Confirm timeline information is correct
```

### 3. Test Edit Functionality
```
✅ Click "Edit" button on a PENDING quotation
✅ Modal should open with pre-filled data
✅ Modal title shows "Edit Quotation"
✅ Edit badge appears
✅ Change labor cost to 30,000
✅ Verify total auto-calculates
✅ Click "Submit Quotation"
✅ Verify changes reflected in logs
✅ Try editing an ACCEPTED quotation → Should show error
```

### 4. Test Delete Functionality
```
✅ Click "Delete" button on a PENDING quotation
✅ Confirmation dialog appears
✅ Click "OK" to confirm
✅ Quotation removed from list
✅ Count badge decrements
✅ Try deleting an ACCEPTED quotation → Should show error
```

### 5. Test Acceptance Simulation
```
✅ Open browser console (F12)
✅ Type: simulateAcceptQuotation('QUOT-1729670400000')
✅ Press Enter
✅ Quotation moves from Pending to Accepted section
✅ Edit/Delete buttons removed
✅ Green checkmark icon displayed
✅ Try to edit → Should show error message
```

### 6. Test Clear All Data
```
✅ Click "Clear All Data" button in demo info box
✅ Confirmation dialog appears
✅ Click "OK"
✅ All quotations cleared
✅ Both sections show empty state
✅ Count badges show 0
```

### 7. Test Reload Mock Data
```
✅ Open browser console (F12)
✅ Type: reloadMockData()
✅ Press Enter
✅ 6 mock quotations reloaded
✅ 3 appear in Pending section
✅ 3 appear in Accepted section
✅ Count badges updated correctly
```

### 8. Test Submit New Quotation
```
✅ Go to "Public Requests" or "Direct Requests" tab
✅ Click "Submit Quotation" on any request
✅ Fill in the form with test data
✅ Submit
✅ Switch to "Logs" tab automatically
✅ New quotation appears in Pending section
✅ Count badge increments
✅ Can edit/delete the new quotation
```

---

## 🖥️ Browser Console Commands

### View All Quotations
```javascript
quotations
// or
JSON.parse(localStorage.getItem('quotations'))
```

### View Specific Quotation
```javascript
quotations.find(q => q.quotation_id === 'QUOT-1729670400000')
```

### Accept a Pending Quotation
```javascript
simulateAcceptQuotation('QUOT-1729670400000')
```

### Clear All Data
```javascript
clearAllQuotations()
```

### Reload Mock Data
```javascript
reloadMockData()
```

### View Pending Quotations Only
```javascript
quotations.filter(q => q.status === 'pending')
```

### View Accepted Quotations Only
```javascript
quotations.filter(q => q.status === 'accepted')
```

### Count Quotations
```javascript
console.log('Pending:', quotations.filter(q => q.status === 'pending').length);
console.log('Accepted:', quotations.filter(q => q.status === 'accepted').length);
console.log('Total:', quotations.length);
```

### Add Custom Test Quotation
```javascript
quotations.push({
    quotation_id: "QUOT-" + Date.now(),
    request_id: "REQ-TEST-001",
    title: "Custom Test Repair",
    description: "Testing custom quotation",
    labor_cost: 10000,
    material_cost: 15000,
    transport_cost: 2000,
    other_cost: 500,
    total_price: 27500,
    estimated_start_date: "2025-11-01",
    estimated_completion_date: "2025-11-10",
    estimated_duration: 9,
    payment_terms: "50% upfront, 50% on completion",
    warranty_period: "1 Year",
    terms_conditions: "Test terms",
    validity_period: 30,
    status: "pending",
    submitted_at: new Date().toISOString(),
    updated_at: new Date().toISOString()
});
localStorage.setItem('quotations', JSON.stringify(quotations));
loadQuotationsToLogs();
```

---

## ✅ Expected Behaviors

### Pending Quotations
- ✅ Show 3 action buttons: View, Edit, Delete
- ✅ Can be edited with pre-filled data
- ✅ Can be deleted with confirmation
- ✅ Can be accepted via console command
- ✅ Show pending status badge
- ✅ Blue/purple icon color

### Accepted Quotations
- ✅ Show only 1 button: View
- ✅ Cannot be edited (error message shown)
- ✅ Cannot be deleted (error message shown)
- ✅ Show green checkmark icon
- ✅ Display "Accepted by customer" text
- ✅ Green status indicator

### Empty States
- ✅ Show when no quotations exist
- ✅ Display helpful message
- ✅ Show inbox icon
- ✅ Provide guidance on next steps

### Demo Info Box
- ✅ Shows count of mock quotations loaded
- ✅ Clear All Data button works
- ✅ Console commands listed
- ✅ Data storage info displayed
- ✅ Gradient purple background

---

## 🐛 Troubleshooting

### Mock Data Not Loading
**Symptom:** No quotations appear on first load

**Solution:**
```javascript
// Open console and run:
reloadMockData()
```

### Data Persists After Clear
**Symptom:** Data still shows after clearing

**Solution:**
```javascript
// Force clear localStorage
localStorage.removeItem('quotations');
location.reload();
```

### Console Commands Not Working
**Symptom:** Function not defined errors

**Solution:**
1. Ensure page is fully loaded
2. Check JavaScript console for errors
3. Refresh the page
4. Check if repair-requests.js is loaded

### Edit Modal Not Pre-filling
**Symptom:** Edit modal opens but fields are empty

**Solution:**
1. Check if quotation ID is correct
2. Verify quotation exists in array
3. Check console for errors
4. Refresh page and try again

### Quotations Lost on Refresh
**Symptom:** Quotations disappear after page reload

**Solution:**
- This should NOT happen - data is in localStorage
- Check if browser has localStorage enabled
- Check browser console for storage errors
- Try incognito mode

---

## 📝 Test Checklist

### Basic Functionality
- [ ] Page loads without errors
- [ ] Mock data auto-loads (6 quotations)
- [ ] Logs tab shows correct count badges
- [ ] Pending section shows 3 quotations
- [ ] Accepted section shows 3 quotations
- [ ] Empty states hidden when data present

### View Functionality
- [ ] View button appears on all quotations
- [ ] Clicking view shows complete details
- [ ] Details are correctly formatted
- [ ] All costs displayed properly

### Edit Functionality (Pending Only)
- [ ] Edit button only on pending quotations
- [ ] Edit opens modal with pre-filled data
- [ ] Modal title changes to "Edit Quotation"
- [ ] Edit badge displayed
- [ ] Cost auto-calculation works
- [ ] Date validation works
- [ ] Submit updates existing quotation
- [ ] Changes reflected immediately
- [ ] Accepted quotations show error on edit attempt

### Delete Functionality (Pending Only)
- [ ] Delete button only on pending quotations
- [ ] Confirmation dialog appears
- [ ] Cancel preserves quotation
- [ ] Confirm removes quotation
- [ ] Count badge decrements
- [ ] Empty state shows when last removed
- [ ] Accepted quotations show error on delete attempt

### Acceptance Simulation
- [ ] Console command accepts quotation
- [ ] Quotation moves to accepted section
- [ ] Edit/Delete buttons removed
- [ ] Green icon displayed
- [ ] Counts updated correctly
- [ ] Cannot edit after acceptance

### Clear All Data
- [ ] Button visible in demo box
- [ ] Confirmation dialog appears
- [ ] All quotations cleared
- [ ] Empty states displayed
- [ ] Count badges reset to 0

### Reload Mock Data
- [ ] Console command works
- [ ] 6 quotations restored
- [ ] Correct split (3 pending, 3 accepted)
- [ ] Counts updated
- [ ] Success notification shown

### Persistence
- [ ] Data survives page refresh
- [ ] Data survives tab close/reopen
- [ ] Data accessible across sessions
- [ ] LocalStorage updated correctly

---

## 🎓 Learning Resources

### Understanding the Code
1. **Mock Data Location:** `assets/javascript/company/repair-requests.js` (Lines 11-143)
2. **Load Function:** `loadMockQuotations()` (Lines 11-156)
3. **Display Function:** `loadQuotationsToLogs()` (Lines 562-650)
4. **Edit Function:** `editQuotation()` (Lines 653-720)
5. **Delete Function:** `deleteQuotation()` (Lines 722-740)
6. **Accept Function:** `simulateAcceptQuotation()` (Lines 793-803)

### Key Concepts
- **localStorage** - Browser storage for data persistence
- **JSON parsing** - Converting stored strings to objects
- **Array filtering** - Separating pending vs accepted
- **Status management** - Controlling edit/delete availability
- **Modal pre-filling** - Populating forms with existing data

---

## 🚀 Next Steps

After testing with mock data:
1. ✅ Verify all functionality works correctly
2. ✅ Test edge cases and error scenarios
3. ✅ Ensure UI/UX is smooth
4. 🔄 Prepare for database integration
5. 🔄 Create backend API endpoints
6. 🔄 Replace localStorage with API calls
7. 🔄 Add real-time notifications
8. 🔄 Implement file attachments

---

## 📞 Support

If you encounter issues:
1. Check browser console for errors (F12)
2. Verify localStorage is enabled
3. Try clearing cache and reloading
4. Use `reloadMockData()` to reset
5. Check this guide for troubleshooting steps

---

**Last Updated:** October 23, 2025  
**Mock Data Version:** 1.0  
**Total Mock Quotations:** 6 (3 pending, 3 accepted)
