# Company Pages - Absolute Path Navigation Implementation

## Overview
All navigation paths in Company actor UI have been converted to use **absolute file paths** instead of relative or routed paths.

## Path Format Used

### ❌ OLD (Relative/Routed Paths)
```html
<a href="/company-dashboard">Dashboard</a>
<a href="/company-workforce">Workforce</a>
```

### ✅ NEW (Absolute Paths)
```html
<a href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php">Dashboard</a>
<a href="/2nd-Year-Group-Project/FixLanka/views/company/workforce.php">Workforce</a>
```

## Files Updated

### 1. dashboard.php ✅
**Main Content Navigation:**
- "View All" link → Repair Requests: `/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php`
- "View All" link → Contracts: `/2nd-Year-Group-Project/FixLanka/views/company/contracts.php`
- "View All" link → Payments: `/2nd-Year-Group-Project/FixLanka/views/company/payments.php`
- "View All" link → Reviews: `/2nd-Year-Group-Project/FixLanka/views/company/reviews.php`
- "View All" link → Support: `/2nd-Year-Group-Project/FixLanka/views/company/support.php`

**Workforce Category Cards:**
- Carpenters onclick → `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php#carpenters`
- Electricians onclick → `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php#electricians`
- Plumbers onclick → `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php#plumbers`
- Painters onclick → `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php#painters`

**JavaScript Functions:**
- `navigateToRepairRequests()` → `/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php`
- `navigateToContracts()` → `/2nd-Year-Group-Project/FixLanka/views/company/contracts.php`

**JavaScript Selector:**
- Dashboard active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php"]`

### 2. workforce.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Workforce active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/workforce.php"]`

### 3. support.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Support active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/support.php"]`

### 4. repair-requests.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Repair Requests active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php"]`

### 5. projects.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Projects active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/projects.php"]`

### 6. payments.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Payments active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/payments.php"]`

### 7. contracts.php ✅
**Breadcrumb:**
- Dashboard link: `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`

**JavaScript Selector:**
- Contracts active state: `a[href="/2nd-Year-Group-Project/FixLanka/views/company/contracts.php"]`

### 8. sidebar.php ✅
**All Navigation Links:**
- Dashboard → `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php`
- Repair Requests → `/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php`
- Projects → `/2nd-Year-Group-Project/FixLanka/views/company/projects.php`
- Workforce → `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php`
- Payments → `/2nd-Year-Group-Project/FixLanka/views/company/payments.php`
- Contracts → `/2nd-Year-Group-Project/FixLanka/views/company/contracts.php`
- Advertisements → `/2nd-Year-Group-Project/FixLanka/views/company/advertisements.php`
- Support → `/2nd-Year-Group-Project/FixLanka/views/company/support.php`
- Settings → `/2nd-Year-Group-Project/FixLanka/views/company/settings.php`

**JavaScript Path Detection:**
- Updated to detect paths starting with: `/2nd-Year-Group-Project/FixLanka/views/company/`
- Extracts page name by removing path prefix and `.php` extension

### 9. topbar.php ✅
**Profile Dropdown Links:**
- My Profile → `/2nd-Year-Group-Project/FixLanka/views/company/profile.php`
- Settings → `/2nd-Year-Group-Project/FixLanka/views/company/settings.php`
- Help & Support → `/2nd-Year-Group-Project/FixLanka/views/company/support.php`

## Complete Path Mapping

| Page | Absolute Path |
|------|---------------|
| Dashboard | `/2nd-Year-Group-Project/FixLanka/views/company/dashboard.php` |
| Repair Requests | `/2nd-Year-Group-Project/FixLanka/views/company/repair-requests.php` |
| Contracts | `/2nd-Year-Group-Project/FixLanka/views/company/contracts.php` |
| Projects | `/2nd-Year-Group-Project/FixLanka/views/company/projects.php` |
| Workforce | `/2nd-Year-Group-Project/FixLanka/views/company/workforce.php` |
| Payments | `/2nd-Year-Group-Project/FixLanka/views/company/payments.php` |
| Reviews | `/2nd-Year-Group-Project/FixLanka/views/company/reviews.php` |
| Support | `/2nd-Year-Group-Project/FixLanka/views/company/support.php` |
| Profile | `/2nd-Year-Group-Project/FixLanka/views/company/profile.php` |
| Settings | `/2nd-Year-Group-Project/FixLanka/views/company/settings.php` |
| Advertisements | `/2nd-Year-Group-Project/FixLanka/views/company/advertisements.php` |
| Feedback | `/2nd-Year-Group-Project/FixLanka/views/company/feedback.php` |

## Total Changes

- **9 files** updated
- **7 main content pages** (dashboard, workforce, support, repair-requests, projects, payments, contracts)
- **2 shared components** (sidebar, topbar)
- **40+ navigation links** converted to absolute paths
- **10+ JavaScript selectors** updated

## Navigation Types Updated

1. **HTML Anchor Links** - All `<a href="">` tags
2. **Breadcrumb Navigation** - Dashboard links on every page
3. **Button onclick Handlers** - JavaScript navigation functions
4. **View All Buttons** - Links from dashboard panels to detail pages
5. **Workforce Category Cards** - onclick navigation with hash anchors
6. **Modal Navigation Functions** - JavaScript window.location assignments
7. **JavaScript Selectors** - querySelector for active state highlighting
8. **Sidebar Navigation Menu** - All menu items
9. **Topbar Dropdown Menu** - Profile menu links

## Benefits of Absolute Paths

1. ✅ **Direct File Access** - No dependency on routing logic
2. ✅ **Explicit Paths** - Clear understanding of file locations
3. ✅ **No Router Conflicts** - Bypasses index.php routing
4. ✅ **Easier Debugging** - Can see exact file being referenced
5. ✅ **Consistent Behavior** - Works regardless of router configuration

## Testing Checklist

- ✅ Navigate from dashboard to each section using "View All" buttons
- ✅ Navigate from each page back to dashboard using breadcrumbs
- ✅ Click on workforce category cards from dashboard
- ✅ Verify sidebar links work on all pages
- ✅ Test topbar profile dropdown links
- ✅ Verify sidebar active state highlights correctly
- ✅ Test modal navigation (Start New Project)
- ✅ Confirm all links open correct pages without 404 errors
- ✅ Test navigation with hash anchors (workforce categories)

## Important Notes

### Asset Paths (Unchanged)
CSS and JavaScript assets still use absolute paths as they were:
```html
<link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
<script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/dashboard.js"></script>
```

### Logout Path (Unchanged)
The logout functionality still uses the routed path:
```html
<a href="/2nd-Year-Group-Project/FixLanka/logout">Logout</a>
```
This is correct as it goes through the authentication controller.

## Date Implemented
October 24, 2025

## Developer: GitHub Copilot
All paths have been systematically converted from relative/routed format to absolute file paths for direct access to company view files.
