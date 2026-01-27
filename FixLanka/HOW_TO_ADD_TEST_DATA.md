# 🧪 How to Add Test Data for Search Testing

## 📋 Quick Steps

### Option 1: Using phpMyAdmin (Easiest) ✅

1. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Login (usually no password for XAMPP)

2. **Select Database**
   - Click on `fix_lanka` database in left sidebar

3. **Open SQL Tab**
   - Click the **SQL** tab at the top

4. **Copy & Paste SQL**
   - Open the file: `test_search_data.sql`
   - Copy ALL the content
   - Paste into the SQL box in phpMyAdmin

5. **IMPORTANT: Update company_id**
   - Look at line 11-15 in the SQL
   - Change `company_id = 2` to YOUR company ID
   - (Check your login session or database to find your company_id)

6. **Run the SQL**
   - Click the **Go** button (bottom right)
   - Wait for "Query executed successfully" message

7. **Done!** 🎉
   - Test data is now in your database

---

### Option 2: Using MySQL Command Line

```bash
# Open PowerShell and navigate to MySQL bin folder
cd C:\xampp\mysql\bin

# Run MySQL
.\mysql.exe -u root fix_lanka

# Copy and paste the SQL commands from test_search_data.sql
# Or import the file:
source C:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\test_search_data.sql

# Exit MySQL
exit
```

---

## 📊 What Data Will Be Added?

### 5 Test Projects:
1. **Kitchen Renovation Project** - Budget: 150,000 LKR
2. **Office Outlet Installation** - Budget: 75,000 LKR  
3. **Bathroom Plumbing Repair** - Budget: 95,000 LKR
4. **Air Conditioning System Repair** - Budget: 120,000 LKR
5. **Cabinet Door Replacement** - Budget: 45,000 LKR (Completed)

### 5 Test Repair Requests:
1. **Kitchen Sink Leaking** - Urgency: High
2. **Outlet Not Working in Bedroom** - Urgency: Medium
3. **Cabinet Door Broken** - Urgency: Low
4. **Air Conditioner Not Cooling** - Urgency: High
5. **Bathroom Pipe Burst** - Urgency: Critical (Emergency!)

---

## 🔍 Test Search Keywords

After adding data, test search with these keywords:

| Search Term | Expected Results |
|-------------|------------------|
| **kitchen** | 2 projects + 2 requests = 4 results |
| **repair** | All 5 projects + 3 requests = 8 results |
| **outlet** | 1 project + 1 request = 2 results |
| **door** | 1 project + 1 request = 2 results |
| **bathroom** | 1 project + 1 request = 2 results |
| **air conditioning** | 1 project + 1 request = 2 results |
| **urgent** | Should find high/critical requests |
| **leak** | 2 requests (sink + pipe) |

---

## ⚠️ IMPORTANT: Update company_id!

**Before running the SQL, you MUST update the company_id!**

### How to Find Your company_id:

**Method 1: Check Session**
1. Open dashboard page
2. Press F12 (Developer Tools)
3. Go to Console tab
4. Type: `document.cookie`
5. Look for your user info

**Method 2: Check Database**
```sql
-- Run this in phpMyAdmin SQL tab:
SELECT company_id, company_name, email 
FROM company 
ORDER BY company_id;

-- Find your company and note the company_id
```

**Method 3: Check Login**
- Log out and log back in
- Check the email you used
- Then in phpMyAdmin:
```sql
SELECT company_id FROM company WHERE email = 'your-email@example.com';
```

### Then Update the SQL:

Find all lines with `company_id = 2` and change `2` to your actual company_id:

```sql
-- Example: If your company_id is 5
-- Change this:
(2, 'Kitchen Renovation Project', ...)

-- To this:
(5, 'Kitchen Renovation Project', ...)
```

---

## ✅ Verify Data Was Added

After running the SQL, verify in phpMyAdmin:

### Check Projects:
```sql
SELECT project_id, title, status 
FROM project 
WHERE company_id = YOUR_COMPANY_ID
ORDER BY created_at DESC;
```

**Expected:** Should see 5 new projects

### Check Requests:
```sql
SELECT request_id, title, status 
FROM jobrequest 
WHERE company_id = YOUR_COMPANY_ID
ORDER BY created_at DESC;
```

**Expected:** Should see 5 new requests

---

## 🧪 Testing the Search

1. **Refresh Dashboard**
   - Go to your company dashboard
   - Make sure page loads without errors

2. **Open Search**
   - Click the search box (usually top right)
   - Should see search input field

3. **Test Search Terms**
   - Type: **kitchen**
   - Press Enter
   - **Expected:** See "Kitchen Renovation Project" and "Kitchen Sink Leaking"

4. **Try Different Searches**
   - Search: **repair**
   - Search: **outlet**  
   - Search: **urgent**
   - Each should return relevant results

5. **Check Console**
   - Press F12
   - Go to Console tab
   - Should see NO red errors
   - Should see: `✅ Response: {"success":true,"results":[...]}`

---

## 🎯 Expected Search Results

### Search: "kitchen"
```json
{
  "success": true,
  "results": [
    {
      "type": "project",
      "title": "Kitchen Renovation Project",
      "subtitle": "Project #123",
      "url": "projects.php?id=123",
      "icon": "fa-project-diagram"
    },
    {
      "type": "request",
      "title": "Kitchen Sink Leaking",
      "subtitle": "Request #456",
      "url": "repair-requests.php?id=456",
      "icon": "fa-tools"
    }
  ]
}
```

### Search: "repair"
Should return 8+ results (all projects and most requests)

---

## 🧹 Clean Up Test Data (Optional)

If you want to remove test data later:

```sql
-- Delete test projects
DELETE FROM project 
WHERE company_id = YOUR_COMPANY_ID 
AND title IN (
    'Kitchen Renovation Project',
    'Office Outlet Installation',
    'Bathroom Plumbing Repair',
    'Air Conditioning System Repair',
    'Cabinet Door Replacement'
);

-- Delete test requests
DELETE FROM jobrequest 
WHERE company_id = YOUR_COMPANY_ID 
AND title IN (
    'Kitchen Sink Leaking',
    'Outlet Not Working in Bedroom',
    'Cabinet Door Broken',
    'Air Conditioner Not Cooling',
    'Bathroom Pipe Burst'
);
```

---

## 🐛 Troubleshooting

### Problem: "Duplicate entry" error
**Solution:** Data already exists. Either:
- Change project titles to be unique
- Delete existing test data first
- Skip duplicate entries

### Problem: "Unknown column 'company_id'"
**Solution:** Your table structure is different
- Check your actual table structure
- Adjust the INSERT statements to match

### Problem: "Foreign key constraint fails"
**Solution:** 
- Make sure your company_id exists in company table
- Make sure user_id = 1 exists in user table
- Make sure category_id values (1,2,3,4) exist in category table

### Problem: No results when searching
**Solution:**
- Check if data was actually inserted: `SELECT * FROM project WHERE company_id = YOUR_ID`
- Check if you're logged in as correct company
- Check console for errors
- Verify search API fix was applied

---

## 📞 Need Help?

If you get stuck:
1. Take a screenshot of the error
2. Check phpMyAdmin to see if data exists
3. Check browser console for errors
4. Let me know and I'll help debug!

---

## ✅ Success Checklist

- [ ] Opened phpMyAdmin
- [ ] Selected fix_lanka database
- [ ] Updated company_id in SQL file to my actual ID
- [ ] Ran the INSERT statements
- [ ] Saw "Query executed successfully" message
- [ ] Verified data in project table
- [ ] Verified data in jobrequest table
- [ ] Refreshed dashboard
- [ ] Tested search with "kitchen"
- [ ] Saw search results appear
- [ ] No errors in console
- [ ] Search is working! 🎉

---

**Ready to test?** Follow Option 1 (phpMyAdmin) - it's the easiest! 👍
