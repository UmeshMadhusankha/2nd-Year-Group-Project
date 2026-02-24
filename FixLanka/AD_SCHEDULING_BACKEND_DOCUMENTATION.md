# FixLanka Ad Scheduling Dashboard - Complete Backend Implementation

## 📋 Overview
This document provides a complete guide to the newly implemented backend for the FixLanka Ad Scheduling Dashboard.

## 🗄️ Database Setup

### Step 1: Create the ads_schedule Table
Run the SQL file located at:
```
FixLanka/database/ads_schedule_table.sql
```

Execute in phpMyAdmin or MySQL command line:
```sql
USE fix_lanka;
SOURCE /path/to/FixLanka/database/ads_schedule_table.sql;
```

### Table Schema
```sql
ads_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT (FK to Advertisement table),
    company_name VARCHAR(255),
    title VARCHAR(255),
    type VARCHAR(50),  -- Banner, Sponsored, Featured
    category VARCHAR(100),
    placement VARCHAR(100),  -- Homepage Top, Sidebar, etc.
    priority VARCHAR(20),  -- Low, Medium, High
    start_date DATE,
    end_date DATE,
    daily_time_start TIME,
    daily_time_end TIME,
    status VARCHAR(20),  -- Pending, Active, Expiring Soon, Completed
    views INT DEFAULT 0,
    clicks INT DEFAULT 0,
    ctr FLOAT DEFAULT 0.0,
    budget DECIMAL(10,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
)
```

## 📁 Backend Files Created

### 1. **schedule_ad.php** - Create New Scheduled Ad
**Location:** `views/moderator/schedule_ad.php`

**Purpose:** Handles insertion of new scheduled advertisements

**Method:** POST

**Parameters:**
- `ad_id` (required) - Advertisement ID from Advertisement table
- `company_name` (required) - Company name
- `title` (required) - Ad title
- `type` (required) - Banner/Sponsored/Featured
- `category` (required) - Category name
- `placement` (required) - Placement location
- `start_date` (required) - YYYY-MM-DD format
- `end_date` (required) - YYYY-MM-DD format
- `time_start` (required) - HH:MM format
- `time_end` (required) - HH:MM format
- `priority` (required) - Low/Medium/High
- `budget` (required) - Decimal amount

**Response:**
```json
{
    "success": true/false,
    "message": "Success or error message",
    "data": {
        "schedule_id": 123,
        "status": "Active/Pending"
    }
}
```

**Features:**
- ✅ Input validation and sanitization
- ✅ Date and time format validation
- ✅ Budget validation
- ✅ Foreign key check for ad_id
- ✅ Auto-status determination based on dates
- ✅ Prepared statements (SQL injection protection)
- ✅ XSS protection with htmlspecialchars

---

### 2. **fetch_scheduled_ads.php** - List & Filter Ads
**Location:** `views/moderator/fetch_scheduled_ads.php`

**Purpose:** Returns filtered and sorted list of scheduled ads

**Method:** GET

**Query Parameters:**
- `status` - Filter by status (Active, Pending, Expiring Soon, Completed)
- `priority` - Filter by priority (Low, Medium, High)
- `category` - Filter by category
- `placement` - Filter by placement
- `search` - Search in title and company_name
- `sort` - Sort by: newest, oldest, start_date, end_date, priority, budget_high, budget_low
- `format` - html (default) or json

**Response:**
- **HTML Format:** Ready-to-inject table rows with all styling
- **JSON Format:**
```json
{
    "success": true,
    "count": 10,
    "data": [...]
}
```

**Features:**
- ✅ Dynamic filtering (status, priority, category, placement)
- ✅ Full-text search in title and company name
- ✅ Multiple sorting options
- ✅ Status badge color coding
- ✅ Priority badge color coding
- ✅ Performance metrics display
- ✅ Action buttons (View, Edit, Delete)
- ✅ Formatted dates and currency

---

### 3. **update_schedule.php** - Update Existing Schedule
**Location:** `views/moderator/update_schedule.php`

**Purpose:** Updates existing scheduled advertisement details

**Method:** POST

**Parameters:**
- `schedule_id` (required) - ID of schedule to update
- Any of the following (optional):
  - `title`, `company_name`, `type`, `category`, `placement`
  - `start_date`, `end_date`, `time_start`, `time_end`
  - `priority`, `budget`, `status`

**Response:**
```json
{
    "success": true/false,
    "message": "Update status message",
    "data": { /* updated record */ }
}
```

**Features:**
- ✅ Dynamic field updates (only update provided fields)
- ✅ Validation for all inputs
- ✅ Date range validation
- ✅ Returns updated record after successful update

---

### 4. **delete_schedule.php** - Delete Schedule
**Location:** `views/moderator/delete_schedule.php`

**Purpose:** Deletes a scheduled advertisement

**Method:** POST or DELETE

**Parameters:**
- `schedule_id` (required)

**Response:**
```json
{
    "success": true/false,
    "message": "Deletion status",
    "data": {
        "deleted_id": 123,
        "title": "Ad Title",
        "company": "Company Name"
    }
}
```

**Features:**
- ✅ Existence check before deletion
- ✅ Returns deleted record info
- ✅ Cascade deletion (automatically removes from calendar)

---

### 5. **calendar_data.php** - Calendar Widget Data
**Location:** `views/moderator/calendar_data.php`

**Purpose:** Returns scheduled ads formatted for calendar display

**Method:** GET

**Query Parameters:**
- `start` - Start date filter (YYYY-MM-DD)
- `end` - End date filter (YYYY-MM-DD)
- `month` - Month filter (1-12)
- `year` - Year filter (YYYY)

**Response:**
```json
{
    "success": true,
    "count": 15,
    "events": [
        {
            "id": 1,
            "title": "Ad Title",
            "company": "Company Name",
            "start": "2024-08-01",
            "end": "2024-08-31",
            "status": "Active",
            "color": "#22c55e",
            "priorityColor": "#ef4444",
            "duration": 31
        }
    ],
    "eventsByDate": {
        "2024-08-01": [...],
        "2024-08-02": [...]
    },
    "statistics": {
        "total": 20,
        "active": 5,
        "pending": 8,
        "expiring": 2,
        "completed": 5,
        "startingToday": 1
    }
}
```

**Features:**
- ✅ Date range filtering
- ✅ Color-coded events by status
- ✅ Events grouped by date
- ✅ Real-time statistics
- ✅ Duration calculation

---

### 6. **update_status_auto.php** - Auto Status Updates
**Location:** `views/moderator/update_status_auto.php`

**Purpose:** Automatically updates ad statuses based on current date

**Usage:**
1. Auto-runs on page load (included in ad-schedule.php)
2. Can be called via AJAX: `GET update_status_auto.php`
3. Can be set as cron job for automatic updates

**Status Update Rules:**
```
today < start_date          → Pending
start_date ≤ today ≤ end_date → Active
end_date - today ≤ 2 days   → Expiring Soon
today > end_date            → Completed
```

**Response:**
```json
{
    "success": true,
    "updated": 5,
    "message": "Successfully updated 5 ad schedule(s)"
}
```

**Features:**
- ✅ Transaction-safe (rollback on error)
- ✅ Batch updates for efficiency
- ✅ Can run silently when included
- ✅ Returns update count

---

## 🎨 Frontend Integration (ad-schedule.php)

### Key Changes Made:

1. **Database Connection**
   - Added `require_once 'config/databse.php'`
   - Auto-runs status update script on page load

2. **Real Statistics**
   ```php
   // Replaced mock data with real queries
   $stats = $pdo->query("SELECT COUNT(*), SUM(CASE...) FROM ads_schedule");
   ```

3. **AJAX Data Loading**
   ```javascript
   function loadScheduledAds() {
       fetch('fetch_scheduled_ads.php?' + params)
           .then(response => response.text())
           .then(html => tableBody.innerHTML = html);
   }
   ```

4. **Form Submission Handlers**
   ```javascript
   document.getElementById('scheduleAdForm').addEventListener('submit', (e) => {
       e.preventDefault();
       submitSchedule(new FormData(e.target));
   });
   ```

5. **Filter Integration**
   - All filter dropdowns call `loadScheduledAds()`
   - Search bar triggers AJAX reload
   - Sort dropdown reloads with sort parameter

---

## 🚀 Quick Start Guide

### Step 1: Setup Database
```bash
# Login to MySQL
mysql -u root -p

# Run the table creation script
mysql> USE fix_lanka;
mysql> SOURCE /xampp/htdocs/2nd-Year-Group-Project/FixLanka/database/ads_schedule_table.sql;
```

### Step 2: Verify Files
Ensure these files exist:
- ✅ `views/moderator/schedule_ad.php`
- ✅ `views/moderator/fetch_scheduled_ads.php`
- ✅ `views/moderator/update_schedule.php`
- ✅ `views/moderator/delete_schedule.php`
- ✅ `views/moderator/calendar_data.php`
- ✅ `views/moderator/update_status_auto.php`
- ✅ `views/moderator/ad-schedule.php` (updated)

### Step 3: Test the System

1. **Access the Dashboard**
   ```
   http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php
   ```

2. **Create a Test Schedule**
   - Click "Schedule Ad" button
   - Fill in all required fields
   - Click "Schedule Advertisement"
   - Should see success message and table updates

3. **Test Filtering**
   - Use Status dropdown → select "Active"
   - Use Priority dropdown → select "High"
   - Use Search box → type company name
   - All should filter the table dynamically

4. **Test Editing**
   - Click Edit icon on any row
   - Modify dates or budget
   - Save changes
   - Verify table updates

5. **Test Deletion**
   - Click Delete icon
   - Confirm deletion
   - Row should disappear

---

## 🔧 Technical Details

### Security Measures
1. **SQL Injection Protection**
   - All queries use PDO prepared statements
   - Parameter binding for all user inputs

2. **XSS Protection**
   - `htmlspecialchars()` on all output
   - `ENT_QUOTES` flag for comprehensive escaping

3. **Input Validation**
   - Type checking (INT, FLOAT, DATE, TIME)
   - Range validation (budget > 0, dates logical)
   - Enum validation (status, priority, type)

4. **Session Security**
   - Session status check before start
   - Message storage in $_SESSION

### Performance Optimizations
1. **Database Indexes**
   - Indexes on: status, priority, category, placement, dates
   - Foreign key index on ad_id

2. **AJAX Loading**
   - Only loads data when needed
   - Filters applied server-side (not client-side)

3. **Prepared Statements**
   - Reusable query plans
   - Faster execution for repeated queries

---

## 📊 Database Relationships

```
Advertisement (from create_database.sql)
    ↓ (ad_id)
ads_schedule (new table)
    - Stores scheduling information
    - Multiple schedules per ad possible
    - Tracks performance metrics
```

---

## 🎯 Features Implemented

### Core Functionality
- ✅ Create scheduled advertisements
- ✅ View all scheduled ads in table format
- ✅ Filter by status, priority, category, placement
- ✅ Search by title or company name
- ✅ Sort by date, priority, budget
- ✅ Edit existing schedules
- ✅ Delete schedules
- ✅ Auto-update status based on dates

### Dashboard Statistics
- ✅ Total Scheduled Ads (real count)
- ✅ Currently Active (status = 'Active')
- ✅ Starting Today (start_date = today)
- ✅ Available Slots (24 - active ads)

### UI Enhancements
- ✅ Color-coded status badges
- ✅ Priority badges
- ✅ Performance metrics display
- ✅ Action buttons with icons
- ✅ Loading states
- ✅ Success/error notifications

---

## 🐛 Troubleshooting

### Issue: Table shows "Loading..." forever
**Solution:** Check browser console for errors. Verify `fetch_scheduled_ads.php` path is correct.

### Issue: "Database connection failed"
**Solution:** Verify `config/databse.php` has correct credentials.

### Issue: "Advertisement ID does not exist"
**Solution:** Ensure ad_id in form exists in Advertisement table. Check with:
```sql
SELECT ad_id FROM Advertisement LIMIT 5;
```

### Issue: Status not updating automatically
**Solution:** 
1. Check if `update_status_auto.php` is included in ad-schedule.php
2. Manually trigger: Visit `update_status_auto.php` directly
3. Check server date/time settings

---

## 📝 Sample Data

To insert test data:
```sql
-- Ensure you have ads in Advertisement table first
INSERT INTO Advertisement (ad_id, provider_id, provider_type, title, type, budget, status) 
VALUES (1, 1, 'company', 'Test Ad', 'banner', 10000, 'approved');

-- Then create schedule
INSERT INTO ads_schedule (ad_id, company_name, title, type, category, placement, priority, start_date, end_date, daily_time_start, daily_time_end, budget)
VALUES (1, 'Test Company', 'Test Ad', 'Banner', 'Plumbing', 'Homepage Top', 'High', '2024-12-01', '2024-12-31', '00:00:00', '23:59:59', 50000.00);
```

---

## 🔄 Future Enhancements (Optional)

1. **Calendar Integration**
   - Visual calendar widget using calendar_data.php
   - Drag-and-drop rescheduling

2. **Analytics Dashboard**
   - CTR trends over time
   - Placement performance comparison
   - ROI calculations

3. **Automated Reports**
   - Email notifications for expiring ads
   - Weekly performance summaries
   - Budget utilization reports

4. **Bulk Operations**
   - Select multiple ads
   - Bulk status changes
   - Batch scheduling

---

## ✅ Testing Checklist

- [ ] Database table created successfully
- [ ] Can access ad-schedule.php without errors
- [ ] Statistics show real numbers
- [ ] "Schedule Ad" button opens modal
- [ ] Can submit new schedule successfully
- [ ] Table displays scheduled ads
- [ ] Status filter works
- [ ] Priority filter works
- [ ] Category filter works
- [ ] Placement filter works
- [ ] Search box filters results
- [ ] Sort dropdown changes order
- [ ] Edit button opens modal with data
- [ ] Can update schedule
- [ ] Delete button removes schedule
- [ ] Status auto-updates based on dates

---

## 📞 Support

For issues or questions:
1. Check browser console for JavaScript errors
2. Check PHP error log for backend errors
3. Verify database connection
4. Ensure all files are in correct locations

---

**Implementation Date:** November 15, 2025  
**Version:** 1.0.0  
**Status:** ✅ Complete and Ready to Use
