# ⚡ FixLanka Ad Scheduling - Quick Setup (5 Minutes)

## Step 1: Create Database Table (1 minute)
Open phpMyAdmin or MySQL command line and run:

```sql
USE fix_lanka;

CREATE TABLE IF NOT EXISTS ads_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    category VARCHAR(100) NOT NULL,
    placement VARCHAR(100) NOT NULL,
    priority VARCHAR(20) NOT NULL DEFAULT 'Medium',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    daily_time_start TIME NOT NULL DEFAULT '00:00:00',
    daily_time_end TIME NOT NULL DEFAULT '23:59:59',
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    views INT DEFAULT 0,
    clicks INT DEFAULT 0,
    ctr FLOAT DEFAULT 0.0,
    budget DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES Advertisement(ad_id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_category (category),
    INDEX idx_placement (placement),
    INDEX idx_dates (start_date, end_date)
);
```

## Step 2: Insert Test Data (30 seconds)

```sql
-- First, check if you have ads in Advertisement table
SELECT ad_id FROM Advertisement LIMIT 1;

-- If you have ad_id = 1, insert test schedule:
INSERT INTO ads_schedule (ad_id, company_name, title, type, category, placement, priority, start_date, end_date, daily_time_start, daily_time_end, budget)
VALUES 
(1, 'FixItNow Pvt Ltd', 'Professional Plumbing Services', 'Banner', 'Plumbing', 'Homepage Top', 'High', '2024-12-01', '2024-12-31', '09:00:00', '18:00:00', 50000.00),
(1, 'Lanka Builders', 'Construction Services', 'Sponsored', 'Construction', 'Search Results', 'Medium', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), '08:00:00', '20:00:00', 75000.00);

-- If you don't have ad_id = 1, create one first:
INSERT INTO Advertisement (ad_id, provider_id, provider_type, title, type, budget, status) 
VALUES (1, 1, 'company', 'Test Advertisement', 'banner', 10000, 'approved');
```

## Step 3: Verify Files Exist (30 seconds)
Check that these 7 files are present:

1. ✅ `views/moderator/schedule_ad.php`
2. ✅ `views/moderator/fetch_scheduled_ads.php`
3. ✅ `views/moderator/update_schedule.php`
4. ✅ `views/moderator/delete_schedule.php`
5. ✅ `views/moderator/calendar_data.php`
6. ✅ `views/moderator/update_status_auto.php`
7. ✅ `views/moderator/ad-schedule.php` (updated with backend integration)

## Step 4: Test the Dashboard (2 minutes)

### 4.1 Access the Page
Open your browser:
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/ad-schedule.php
```

### 4.2 Check Statistics Cards
You should see:
- **Total Scheduled:** 2 (or your actual count)
- **Currently Active:** Number of active ads
- **Starting Today:** Ads starting today
- **Available Slots:** Calculated automatically

### 4.3 Check Table
- Table should show your test data
- Status badges should be color-coded
- Action buttons should be visible

### 4.4 Test Create Function
1. Click **"Schedule Ad"** button
2. Fill in the form:
   - **Ad ID:** 1 (or any valid ad_id)
   - **Company Name:** Test Company
   - **Title:** Test Advertisement
   - **Type:** Banner
   - **Category:** Plumbing
   - **Start Date:** Today
   - **End Date:** 30 days from now
   - **Start Time:** 09:00
   - **End Time:** 18:00
   - **Placement:** Homepage Top
   - **Priority:** High
   - **Budget:** 50000
3. Click **"Schedule Advertisement"**
4. Should see success message
5. Table should update with new row

### 4.5 Test Filter
1. Select **"Active"** from Status dropdown
2. Table should filter to show only active ads

### 4.6 Test Edit
1. Click **Edit** icon (pencil) on any row
2. Modal should open with populated data
3. Change end date
4. Click **"Save Changes"**
5. Table should update

### 4.7 Test Delete
1. Click **Delete** icon (trash) on any row
2. Confirm deletion
3. Row should disappear

## Step 5: Done! 🎉

Your Ad Scheduling Dashboard is now fully functional with:
- ✅ Real-time data from database
- ✅ Create new schedules
- ✅ Edit existing schedules
- ✅ Delete schedules
- ✅ Filter by status, priority, category, placement
- ✅ Search functionality
- ✅ Sort functionality
- ✅ Auto-updating statuses
- ✅ Live statistics

## 🐛 If Something Goes Wrong

### Problem: Blank page or white screen
**Solution:** 
```bash
# Check PHP error log
tail -f /xampp/xampp/logs/error_log
```

### Problem: "Table doesn't exist"
**Solution:**
```sql
-- Verify table was created
SHOW TABLES LIKE 'ads_schedule';
-- Should return 1 row
```

### Problem: "Cannot connect to database"
**Solution:**
```php
// Check config/databse.php has correct details:
$host = 'localhost';
$dbname = 'fix_lanka';
$username = 'root';
$password = '';  // Your MySQL password
```

### Problem: Table shows "Loading..." forever
**Solution:**
1. Open browser console (F12)
2. Look for errors in Console tab
3. Check Network tab for failed requests
4. Verify path to `fetch_scheduled_ads.php`

## 📝 Quick Reference

### API Endpoints
```javascript
// Load all ads
GET fetch_scheduled_ads.php

// Create schedule
POST schedule_ad.php

// Update schedule  
POST update_schedule.php

// Delete schedule
POST delete_schedule.php

// Calendar data
GET calendar_data.php

// Auto-update statuses
GET update_status_auto.php
```

### Form Field Names
```
ad_id, company_name, title, type, category
start_date, end_date, time_start, time_end
placement, priority, budget
```

---

**Total Setup Time:** ~5 minutes  
**Difficulty Level:** Easy ⭐  
**Status:** Ready to Use ✅
