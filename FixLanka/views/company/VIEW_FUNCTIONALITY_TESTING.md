# View Functionality Testing Guide

## Quick Test Checklist

### 1. Freelancer View Button Tests

#### Test Location
- Navigate to: `Available Freelancers` section in Workforce page

#### Actions to Test
1. **Click View Button**
   - ✅ Drawer opens with smooth animation
   - ✅ Freelancer name displays in header
   - ✅ Avatar emoji shows correctly

2. **Personal Tab** (Default Active)
   - ✅ Full name populated
   - ✅ Email address shown
   - ✅ Phone number displayed
   - ✅ Address appears

3. **Professional Tab**
   - ✅ Click tab to switch
   - ✅ Specialty shown
   - ✅ Experience displayed (X years)
   - ✅ Rating shows (out of 5.0)
   - ✅ Hourly rate formatted (LKR X,XXX/hour)
   - ✅ Availability status correct

4. **Work History Tab**
   - ✅ Click tab to switch
   - ✅ Active Jobs count displayed
   - ✅ Completed Jobs count shown
   - ✅ Total Earnings calculated correctly
   - ✅ Stat cards have proper icons and colors

5. **Contract Tab**
   - ✅ Click tab to switch
   - ✅ Contract date shown (or N/A)
   - ✅ Contract duration calculated
   - ✅ Info box displays

6. **Action Buttons**
   - ✅ Close button works
   - ✅ Edit button functional
   - ✅ Assign button shows only for Available status
   - ✅ Assign button hidden for On Contract status

7. **Close Drawer**
   - ✅ Click close button (X)
   - ✅ Click "Close" button at bottom
   - ✅ Click outside drawer to close
   - ✅ Drawer closes with animation

---

### 2. Application View Button Tests

#### Test Location
- Navigate to: `Pending Applications` section in Workforce page

#### Actions to Test
1. **Click View Button**
   - ✅ Drawer opens with smooth animation
   - ✅ Application details display

2. **Personal Info Tab** (Default Active)
   - ✅ Full name populated
   - ✅ Email address shown
   - ✅ Phone number displayed
   - ✅ Application date formatted

3. **Professional Tab**
   - ✅ Click tab to switch
   - ✅ Specialty shown
   - ✅ Experience displayed
   - ✅ Expected rate shown
   - ✅ Availability status

4. **Additional Tab**
   - ✅ Click tab to switch
   - ✅ Cover letter displayed (or generated)
   - ✅ Skills show as tags
   - ✅ Skills have proper styling

5. **Action Buttons**
   - ✅ Close button works
   - ✅ Decline button functional
   - ✅ Accept button opens contract drawer

6. **Close Drawer**
   - ✅ Close button (X) works
   - ✅ Bottom close button works
   - ✅ Click outside to close

---

### 3. Responsive Design Tests

#### Desktop (1920px+)
- ✅ Drawer slides from right
- ✅ Width: 600px
- ✅ All tabs visible in one row
- ✅ Stats cards in 3 columns

#### Tablet (768px - 1024px)
- ✅ Drawer width appropriate
- ✅ Tabs scrollable if needed
- ✅ Stats cards in 2 columns

#### Mobile (< 768px)
- ✅ Drawer full width
- ✅ Tabs stack vertically
- ✅ Stats cards in 1 column
- ✅ Buttons stack vertically
- ✅ Header card stacks (avatar above info)

---

### 4. Data Validation Tests

#### Freelancer Data
Test with various data scenarios:
- ✅ Freelancer with contract date
- ✅ Freelancer without contract (N/A)
- ✅ Available status freelancer (assign button shows)
- ✅ On Contract status freelancer (assign button hidden)
- ✅ Different experience levels
- ✅ Various rating values

#### Application Data
Test with various data scenarios:
- ✅ Application with cover letter
- ✅ Application without cover letter (auto-generated)
- ✅ Multiple skills (3-5 skills)
- ✅ Single skill
- ✅ Different specialties

---

### 5. Animation & Performance Tests

#### Animations
- ✅ Drawer slide-in smooth
- ✅ Drawer slide-out smooth
- ✅ Tab switch animation (fade)
- ✅ Button hover effects
- ✅ Card hover effects (stats cards)

#### Performance
- ✅ No lag when opening drawer
- ✅ Smooth scrolling in drawer content
- ✅ Fast tab switching
- ✅ No console errors

---

### 6. Edge Case Tests

#### Empty/Missing Data
- ✅ Phone: "Not provided" when missing
- ✅ Address: "Not specified" when missing
- ✅ Contract Date: "N/A" when missing
- ✅ Skills: Empty state handled

#### Long Text
- ✅ Long names don't break layout
- ✅ Long addresses wrap properly
- ✅ Long cover letters scroll
- ✅ Many skills wrap to multiple lines

#### Special Characters
- ✅ Names with accents
- ✅ Emails with special chars
- ✅ Addresses with special formatting

---

### 7. Browser Compatibility Tests

Test in multiple browsers:
- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile Safari (iOS)
- ✅ Chrome Mobile (Android)

---

### 8. Accessibility Tests

#### Keyboard Navigation
- ✅ Tab key cycles through buttons
- ✅ Enter/Space activates buttons
- ✅ ESC closes drawer (if implemented)

#### Screen Readers
- ✅ Button labels readable
- ✅ Field labels announced
- ✅ Status badges announced

---

## Expected Behavior Summary

### Freelancer View Flow
```
Click View Button
  → Drawer slides in from right
  → Personal tab active by default
  → All fields populated with data
  → Assign button visible if Available
  → User can switch tabs
  → User can close drawer
```

### Application View Flow
```
Click View Button
  → Drawer slides in from right
  → Personal Info tab active by default
  → All fields populated
  → Cover letter shown or generated
  → Skills displayed as tags
  → User can switch tabs
  → Accept button leads to contract creation
```

---

## Bug Reporting Template

If you find any issues:

```
**Issue Title:** [Brief description]

**Steps to Reproduce:**
1. 
2. 
3. 

**Expected Behavior:**
[What should happen]

**Actual Behavior:**
[What actually happens]

**Browser/Device:**
[Chrome 120 / Windows 11]

**Screenshot/Video:**
[If applicable]
```

---

## Performance Metrics

### Expected Load Times
- Drawer open: < 300ms
- Tab switch: < 100ms
- Data population: < 50ms

### Memory Usage
- No memory leaks after multiple drawer opens/closes
- Proper cleanup on drawer close

---

## Known Limitations

1. **Static Data**: Currently uses hardcoded arrays
2. **No Image Upload**: Avatars are emoji-based
3. **Manual Refresh**: Data doesn't auto-refresh
4. **Limited History**: Only basic statistics shown

---

## Success Criteria

✅ All tests pass without errors
✅ No console warnings or errors
✅ Smooth animations on all devices
✅ Data displays correctly
✅ Responsive design works perfectly
✅ Buttons function as expected
✅ Drawers close properly

---

## Testing Complete Sign-off

**Tested By:** _______________  
**Date:** _______________  
**Build Version:** v1.0  
**Status:** ☐ Pass  ☐ Fail  ☐ Needs Review  

**Notes:**
_________________________
_________________________
_________________________

