# Advertisement Review Module - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Database Setup
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "Import" tab
3. Choose file: `create_database.sql`
4. Click "Go" button
5. Choose file: `sample_ads_data.sql` 
6. Click "Go" button again

### Step 2: Access the Page
Open your browser and go to:
```
http://localhost/2nd-Year-Group-Project/FixLanka/views/moderator/ads.php
```

### Step 3: Test the Features
✅ You should see 10 sample advertisements
✅ Statistics cards should show counts
✅ Try filtering by status or type
✅ Click "Review" to approve/reject ads

---

## 📊 What You'll See

### Statistics Cards (Top of Page)
- **Total Ads:** 10
- **Pending Review:** 4 ads
- **Approved:** 3 ads  
- **Rejected:** 1 ad

### Advertisement Table
| ID | Company | Title | Type | Budget | Status | Action |
|----|---------|-------|------|--------|--------|--------|
| #1 | ABC Construction | Premium Home... | Banner | $50,000 | Pending | Review |
| ... | ... | ... | ... | ... | ... | ... |

---

## 🎯 Key Features to Test

### 1. Filtering
```
Status Filter:
☐ All Status (shows all 10 ads)
☐ Pending (shows 4 ads)
☐ Approved (shows 3 ads)
☐ Rejected (shows 1 ad)
☐ Active (shows 2 ads)

Type Filter:
☐ All Types
☐ Banner (shows ads with type=banner)
☐ Sponsored (shows ads with type=sponsored)
☐ Featured (shows ads with type=featured)

Search:
☐ Type "construction" → filters ads with that keyword
☐ Type "repair" → filters ads with that keyword
```

### 2. Review & Approve
```
1. Click "Review" button on any PENDING ad
2. Modal opens showing ad details
3. Select "Approve Advertisement" from dropdown
4. Click "Submit Review"
5. Page reloads
6. Ad status changes to "Approved"
7. Statistics update automatically
```

### 3. Review & Reject
```
1. Click "Review" button on any PENDING ad
2. Modal opens
3. Select "Reject Advertisement"
4. Rejection reason field appears
5. Type reason (optional in this version)
6. Click "Submit Review"
7. Ad status changes to "Rejected"
```

---

## 📝 Sample Data Overview

### Companies (3 total)
1. **ABC Construction Ltd** - Construction company
2. **XYZ Repairs Co** - Repair services
3. **BuildMax Solutions** - Construction & Renovation

### Repairers (2 total)
1. **John Silva** - Plumber (10 years experience)
2. **Sarah Fernando** - Electrician

### Advertisements (10 total)
| ID | Provider | Title | Type | Budget | Status |
|----|----------|-------|------|--------|--------|
| 1 | ABC Construction | Premium Home Construction | Banner | $50,000 | Pending |
| 2 | ABC Construction | Special Discount on Renovation | Featured | $35,000 | Approved |
| 3 | XYZ Repairs | Emergency Repair Services 24/7 | Sponsored | $25,000 | Pending |
| 4 | BuildMax Solutions | Quality Building Materials | Banner | $45,000 | Rejected |
| 5 | BuildMax Solutions | Spring Sale Home Improvement | Featured | $30,000 | Active |
| 6 | John Silva | Expert Plumbing Services | Sponsored | $15,000 | Approved |
| 7 | Sarah Fernando | Professional Electrical Work | Banner | $20,000 | Pending |
| 8 | ABC Construction | New Year Construction Packages | Featured | $55,000 | Pending |
| 9 | XYZ Repairs | Fast & Reliable Repair Solutions | Sponsored | $28,000 | Approved |
| 10 | BuildMax Solutions | Your Dream Home Partner | Banner | $60,000 | Active |

---

## 🔍 Testing Scenarios

### Scenario 1: Approve a Pending Ad
```
1. Go to ads.php
2. Filter by Status: "Pending" 
3. Click "Review" on ad #1 (ABC Construction)
4. Select "Approve Advertisement"
5. Click "Submit Review"
6. ✅ Success message appears
7. ✅ Ad #1 status is now "Approved"
8. ✅ Pending count decreases by 1
9. ✅ Approved count increases by 1
```

### Scenario 2: Reject a Pending Ad
```
1. Filter by Status: "Pending"
2. Click "Review" on ad #3 (XYZ Repairs)
3. Select "Reject Advertisement"
4. (Optional) Enter rejection reason
5. Click "Submit Review"
6. ✅ Ad #3 status is now "Rejected"
7. ✅ Statistics update
```

### Scenario 3: Search & Filter
```
1. Type "construction" in search box
2. Press Enter or click Filter
3. ✅ Only ads with "construction" in title/company show
4. Select Type: "Banner"
5. ✅ Results further filtered to banner ads only
```

### Scenario 4: Activate an Approved Ad
```
1. Filter by Status: "Approved"
2. Click "Review" on any approved ad
3. Select "Set as Active"
4. Click "Submit Review"
5. ✅ Ad status changes to "Active"
```

---

## 🐛 Troubleshooting

### Problem: Page shows "Connection failed"
**Solution:**
1. Check XAMPP is running
2. Check MySQL service is started
3. Verify database name is "fix_lanka"
4. Check username is "root" with empty password

### Problem: "No advertisements found"
**Solution:**
1. Run `sample_ads_data.sql` again
2. Check data in phpMyAdmin: `SELECT * FROM Advertisement`
3. Verify Company and Repairer tables have data

### Problem: Company name shows "Unknown Provider"
**Solution:**
1. Check Company table has records with matching company_id
2. Check Repairer table has records with matching repairer_id
3. Verify provider_id and provider_type in Advertisement table

### Problem: Filter not working
**Solution:**
1. Check URL has GET parameters: `?status=pending`
2. Check case sensitivity (use lowercase: "pending" not "Pending")
3. Clear browser cache

### Problem: Approve/Reject not saving
**Solution:**
1. Check form method is POST
2. Check ad_id is being passed in hidden field
3. Check database connection is active
4. Look for error messages in browser console

---

## 💡 Tips

1. **Use phpMyAdmin SQL tab** to run custom queries and test:
   ```sql
   SELECT * FROM Advertisement WHERE status = 'pending';
   SELECT COUNT(*) FROM Advertisement;
   UPDATE Advertisement SET status = 'pending' WHERE ad_id = 1;
   ```

2. **Reset data** anytime by re-running `sample_ads_data.sql`

3. **Check session messages** - They appear at the top after actions

4. **View URL parameters** - Filter changes are visible in address bar

5. **Inspect table** - Right-click table → Inspect to see data

---

## 📚 Next Steps

After testing the basic features, you can:

1. ✅ Add user authentication
2. ✅ Implement rejection reason storage
3. ✅ Add pagination for large datasets
4. ✅ Add date range filters
5. ✅ Export ads to CSV/PDF
6. ✅ Add email notifications
7. ✅ Implement ad analytics

See `ADS_MODULE_README.md` for detailed documentation.

---

## ✅ Checklist

Complete this checklist to verify everything works:

- [ ] XAMPP is running
- [ ] Database `fix_lanka` exists
- [ ] Sample data is inserted
- [ ] Page loads without errors
- [ ] Statistics cards show correct counts
- [ ] Table displays 10 sample ads
- [ ] Status filter works
- [ ] Type filter works  
- [ ] Search works
- [ ] Review modal opens
- [ ] Approve action works
- [ ] Reject action works
- [ ] Statistics update after actions
- [ ] Success messages appear

**If all checked ✅ - You're ready to go! 🎉**
