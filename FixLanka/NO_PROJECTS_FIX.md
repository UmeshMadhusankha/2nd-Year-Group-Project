# Fix: No Projects Appearing in Contract Creation

## 🐛 Problem
- User added projects to the Project table directly
- New Contract modal shows "No Projects Available"
- Expected to see project cards for selection

## 🔍 Root Cause
The contract creation feature looks for **accepted CompanyQuotations**, not direct Project entries. The workflow is:

1. Customer creates JobRequest
2. Company submits CompanyQuotation
3. Customer accepts quotation → status = 'accepted'
4. **Now** the quotation appears in "New Contract" modal
5. Company creates contract from accepted quotation

## ✅ Solution

### Fixed 3 Issues:

#### 1. **Wrong Table Structure**
- **Before:** Query looked for columns that don't exist
  - `price_quoted` (doesn't exist)
  - `proposed_start_date` (doesn't exist)
  - `u.fname`, `u.lname` (doesn't exist)
  
- **After:** Using correct column names
  - `total_amount` (actual column)
  - `start_date`, `completion_date` (actual columns)
  - `u.username` (actual column)

#### 2. **Wrong Foreign Key**
- **Before:** `CompanyQuotation.company_id` (doesn't exist)
- **After:** `CompanyQuotation.user_id` (correct - companies are users!)

#### 3. **Missing Data**
- Need to insert data into **CompanyQuotation** table, not just Project table
- Projects alone don't appear in the selection

## 📝 Files Fixed

### 1. `controllers/ContractController.php`
**Method:** `getAcceptedProjects()`

**Changes:**
- Updated SQL query to use correct column names
- Fixed joins and WHERE clause
- Added error logging
- Fixed data formatting to handle actual column structure

### 2. Created SQL Scripts

**`INSERT_QUOTATIONS_STEP_BY_STEP.sql`** - Step-by-step guide with:
- Query to find your company user_id
- Clean test data
- Insert customers, job requests, and quotations
- Verification query

## 🚀 How to Fix (Step-by-Step)

### Step 1: Find Your Company User ID
Open phpMyAdmin and run:
```sql
SELECT user_id, username, email 
FROM User 
WHERE user_role = 'company';
```
Copy your `user_id` number.

### Step 2: Run the SQL Script
1. Open `INSERT_QUOTATIONS_STEP_BY_STEP.sql`
2. Find lines 39 and 40 (CompanyQuotation INSERT)
3. Replace `user_id = 1` with your actual user_id
4. Run the entire script in phpMyAdmin

### Step 3: Test
1. Go to Contracts page
2. Click "New Contract" button
3. **You should now see 2 project cards!**
   - Office Renovation - LKR 295,000
   - Kitchen Plumbing - LKR 18,500

## 📊 Database Structure (Correct Understanding)

```
User (with user_role='company')
    ↓ user_id
CompanyQuotation (status='accepted')
    ↓ quotation_id, request_id
JobRequest
    ↓ customer_id
User (customer)
```

**Key Points:**
- Companies are stored in User table with `user_role = 'company'`
- CompanyQuotation.user_id = company's user_id
- Contract creation pulls from accepted CompanyQuotations
- NOT from Project table directly

## 🔄 Correct Workflow

1. **Customer** creates JobRequest
2. **Company** views available job requests
3. **Company** submits CompanyQuotation
4. **Customer** reviews and accepts quotation
5. **Quotation status** changes to 'accepted'
6. **Company** sees it in "New Contract" modal ✅
7. **Company** selects and creates contract

## ✅ What's Fixed Now

- ✅ Query uses correct table structure
- ✅ Query uses correct column names
- ✅ Query properly joins User (customers)
- ✅ Data formatting handles actual columns
- ✅ Error logging added for debugging
- ✅ SQL script matches database schema

## 📋 CompanyQuotation Columns (Reference)

| Column | Type | Description |
|--------|------|-------------|
| quotation_id | INT | Primary key |
| request_id | INT | FK to JobRequest |
| user_id | INT | FK to User (company) |
| title | VARCHAR(250) | Project title |
| description | VARCHAR(500) | Quote description |
| labor_cost | DECIMAL | Labor charges |
| material_cost | DECIMAL | Material costs |
| transport_cost | DECIMAL | Transport fees |
| other_charges | DECIMAL | Other expenses |
| total_amount | DECIMAL | **Total quote price** |
| start_date | DATE | **Proposed start** |
| completion_date | DATE | **Proposed end** |
| estimated_duration | INT | Days needed |
| payment_terms | VARCHAR | Payment schedule |
| warranty_period | VARCHAR | Warranty duration |
| status | ENUM | pending/accepted/rejected |

## 🎯 Next Steps

1. Run the SQL script with your company's user_id
2. Test the New Contract feature
3. Select a project card
4. Verify auto-fill works correctly
5. Complete the contract creation

---

**Status:** ✅ FIXED  
**Date:** December 10, 2025  
**Issue:** Wrong table structure and missing quotation data  
**Resolution:** Fixed SQL query + provided correct data insertion script
