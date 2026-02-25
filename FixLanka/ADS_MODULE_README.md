# Advertisement Review Module - Backend Documentation

## Overview
This module provides a complete backend implementation for the Advertisement Review system in FixLanka. It uses **plain PHP with mysqli** (no frameworks) for easy understanding and maintenance.

---

## Features Implemented

### ✅ Backend Functionality

1. **Database Connection**
   - Uses mysqli for database operations
   - Connects to `fix_lanka` database
   - Connection details: localhost, root, no password (default XAMPP)

2. **Advertisement Listing**
   - Fetches all advertisements from database
   - Joins with Company and Repairer tables to get provider names
   - Displays in a responsive table

3. **Filtering System**
   - Filter by Status: All, Pending, Approved, Rejected, Active
   - Filter by Type: All, Banner, Sponsored, Featured
   - Search by title or company name
   - Filters work via GET parameters

4. **Statistics Dashboard**
   - Total Advertisements count
   - Pending Reviews count
   - Approved Ads count
   - Rejected Ads count

5. **Moderation Actions**
   - Approve advertisements (sets status to 'approved')
   - Reject advertisements (sets status to 'rejected')
   - Activate advertisements (sets status to 'active')
   - Actions handled via POST requests
   - Automatic page reload after action

6. **Security Features**
   - Prepared statements for SQL queries
   - SQL injection prevention
   - XSS protection with htmlspecialchars()
   - Session-based success/error messages

---

## Database Setup

### Step 1: Create the Database
Run the main database creation script:
```bash
# In phpMyAdmin or MySQL command line
source c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/create_database.sql
```

### Step 2: Add Sample Data (Optional for Testing)
```bash
# In phpMyAdmin or MySQL command line
source c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/sample_ads_data.sql
```

This will insert:
- 3 sample companies
- 2 sample repairers  
- 10 sample advertisements with various statuses
- 5 ad schedules

---

## File Structure

```
FixLanka/
├── views/
│   └── moderator/
│       └── ads.php              # Main file with complete backend
├── config/
│   └── databse.php              # Database config (PDO - not used here)
├── create_database.sql          # Database schema
├── sample_ads_data.sql          # Sample data for testing
└── ADS_MODULE_README.md         # This file
```

---

## How It Works

### 1. **Page Load Flow**

```php
// 1. Connect to database (mysqli)
$conn = new mysqli($host, $username, $password, $dbname);

// 2. Handle POST actions (approve/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update advertisement status
    // Redirect to prevent form resubmission
}

// 3. Get filter parameters from URL
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';

// 4. Count statistics
$total_ads = COUNT(*) FROM Advertisement
$pending_ads = COUNT(*) WHERE status = 'pending'
// ... etc

// 5. Fetch filtered advertisements
SELECT a.*, Company.name as company_name
FROM Advertisement a
LEFT JOIN Company ON ...
WHERE status = ? AND type = ?
ORDER BY submission_date DESC

// 6. Display results in HTML table
```

### 2. **Filter Form (GET Method)**

```html
<form method="GET" action="ads.php">
    <input type="text" name="search" />
    <select name="status">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <!-- etc -->
    </select>
    <button type="submit">Filter</button>
</form>
```

**URL Examples:**
- All ads: `ads.php`
- Pending only: `ads.php?status=pending`
- Banner ads: `ads.php?type=banner`
- Search: `ads.php?search=construction`
- Combined: `ads.php?status=pending&type=banner&search=home`

### 3. **Review/Action Form (POST Method)**

```html
<form method="POST" action="ads.php">
    <input type="hidden" name="ad_id" value="123" />
    <select name="action">
        <option value="approve">Approve</option>
        <option value="reject">Reject</option>
        <option value="activate">Activate</option>
    </select>
    <button type="submit">Submit</button>
</form>
```

**Backend Processing:**
```php
if ($_POST['action'] === 'approve') {
    $new_status = 'approved';
} elseif ($_POST['action'] === 'reject') {
    $new_status = 'rejected';
}

$stmt = $conn->prepare("UPDATE Advertisement SET status = ? WHERE ad_id = ?");
$stmt->bind_param("si", $new_status, $ad_id);
$stmt->execute();

header("Location: ads.php"); // Reload page
```

---

## Code Structure in ads.php

```php
<?php
// ============================================
// SECTION 1: DATABASE CONNECTION
// ============================================
$conn = new mysqli($host, $username, $password, $dbname);

// ============================================
// SECTION 2: HANDLE POST ACTIONS
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Approve/Reject/Activate logic
}

// ============================================
// SECTION 3: GET FILTER PARAMETERS
// ============================================
$status_filter = $_GET['status'] ?? '';
$type_filter = $_GET['type'] ?? '';

// ============================================
// SECTION 4: COUNT STATISTICS
// ============================================
$total_sql = "SELECT COUNT(*) as total FROM Advertisement";
$total_result = $conn->query($total_sql);
$total_ads = $total_result->fetch_assoc()['total'];
// ... repeat for pending, approved, rejected

// ============================================
// SECTION 5: FETCH ADVERTISEMENTS
// ============================================
$sql = "SELECT a.*, 
        CASE WHEN a.provider_type = 'company' THEN c.name 
             ELSE CONCAT(r.f_name, ' ', r.l_name) 
        END as company_name
        FROM Advertisement a
        LEFT JOIN Company c ON ...
        LEFT JOIN Repairer r ON ...
        WHERE 1=1";

// Add filters dynamically
if ($status_filter) {
    $sql .= " AND a.status = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// ============================================
// SECTION 6: HTML OUTPUT
// ============================================
?>
<!DOCTYPE html>
<html>
<!-- Display statistics cards -->
<!-- Display filtered table -->
<!-- Display review modal -->
</html>

<?php
// ============================================
// SECTION 7: CLOSE CONNECTION
// ============================================
$conn->close();
?>
```

---

## Testing the Module

### 1. **Initial Setup**
```bash
# Start XAMPP
# Open phpMyAdmin
# Run create_database.sql
# Run sample_ads_data.sql
```

### 2. **Access the Page**
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php
```

### 3. **Test Filtering**
- Select "Pending" from status dropdown → Should show only pending ads
- Select "Banner" from type dropdown → Should show only banner ads
- Type "construction" in search box → Should filter by search term

### 4. **Test Actions**
- Click "Review" button on any ad
- Select "Approve Advertisement"
- Click "Submit Review"
- Page should reload and status should be updated

### 5. **Verify Statistics**
- Numbers in cards should match database counts
- Statistics update after approve/reject actions

---

## Common Issues & Solutions

### Issue 1: "Connection failed"
**Solution:** Check XAMPP is running, database exists, credentials are correct

### Issue 2: "No advertisements found"
**Solution:** Run `sample_ads_data.sql` to insert test data

### Issue 3: "Company name shows as 'Unknown Provider'"
**Solution:** Make sure Company/Repairer records exist with matching IDs

### Issue 4: Filters not working
**Solution:** Check URL has correct GET parameters, case sensitivity matters

### Issue 5: Actions not saving
**Solution:** Check form method="POST", check ad_id is being passed

---

## Extending the Module

### Add New Filter
```php
// 1. Get parameter
$new_filter = $_GET['new_param'] ?? '';

// 2. Add to SQL query
if ($new_filter) {
    $sql .= " AND a.new_field = ?";
    $params[] = $new_filter;
    $types .= "s";
}

// 3. Add to HTML form
<select name="new_param">
    <option value="">All</option>
    <option value="value1">Value 1</option>
</select>
```

### Add New Action
```php
// 1. Add to POST handler
elseif ($action === 'new_action') {
    $new_status = 'new_status_value';
}

// 2. Add to form
<option value="new_action">New Action</option>
```

### Add Pagination
```php
// 1. Get page number
$page = $_GET['page'] ?? 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// 2. Add to SQL
$sql .= " LIMIT ? OFFSET ?";
$stmt->bind_param($types . "ii", ...$params, $limit, $offset);
```

---

## Security Best Practices

✅ **Implemented:**
- Prepared statements (prevents SQL injection)
- htmlspecialchars() for output (prevents XSS)
- Session messages (secure feedback)
- POST for state-changing actions
- GET for filtering/reading

⚠️ **To Add (Future):**
- User authentication check
- CSRF token protection
- Role-based access control
- Input validation
- Rate limiting

---

## Summary

This module provides a **beginner-friendly, framework-free** backend for advertisement moderation. All code is:
- ✅ Easy to read and understand
- ✅ Well-commented
- ✅ Uses basic PHP + mysqli
- ✅ No complex abstractions
- ✅ Production-ready structure
- ✅ Secure against common attacks

For questions or issues, refer to the code comments in `ads.php`.
