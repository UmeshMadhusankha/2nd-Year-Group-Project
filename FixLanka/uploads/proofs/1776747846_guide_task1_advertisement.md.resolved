# Guide: Implementing Target Audience for Advertisements

Follow these steps to implement the "Target Audience" feature. This covers database updates, form changes, and UI display.

## 1. Database Update
First, we need to change the `target_audience` column to an `ENUM` type with predefined values.

**SQL Command:**
Run this in your MySQL console (XAMPP phpMyAdmin):
```sql
ALTER TABLE advertisement 
MODIFY COLUMN target_audience ENUM('homeowners', 'renters', 'business_owners', 'all') 
DEFAULT 'all';
```

## 2. Frontend: Update the Creation Modal
Open [views/company/advertisements.php](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/views/company/advertisements.php) and find **Step 3: Target & Details** (around line 240).

**Add this HTML:**
After the "Service Category" group, add the "Target Audience" dropdown:
```html
<div class="form-group">
    <label for="adTargetAudience">Target Audience *</label>
    <select id="adTargetAudience" required>
        <option value="all">All Audiences</option>
        <option value="homeowners">Homeowners</option>
        <option value="renters">Renters</option>
        <option value="business_owners">Business Owners</option>
    </select>
</div>
```

## 3. Frontend: Collect Data for Submission
In the same file ([advertisements.php](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/api/advertisements.php)), find the form submit event listener (look for `document.getElementById('adForm').addEventListener('submit', ...)`).

**Update the JS:**
1. Get the value from the new field:
   ```javascript
   const targetAudience = document.getElementById('adTargetAudience').value;
   ```
2. Append it to the `FormData` object (`fd`):
   ```javascript
   fd.append('target_audience', targetAudience);
   ```

## 4. Frontend: Display on Cards
Open [assets/javascript/company/advertisements-filters.js](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements-filters.js) and find the [renderAdCard(ad)](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements-filters.js#297-343) method.

**Update the template:**
Add a new meta-item to display the audience:
```javascript
<div class="meta-item">
    <i class="fas fa-users"></i>
    <span>Target: ${ad.target_audience || 'All'}</span>
</div>
```

## 5. Frontend: Pre-fill when Editing
In [advertisements-filters.js](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements-filters.js), find the [editAdvertisement(adId)](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements-filters.js#1220-1224) method.

**Add pre-fill logic:**
Locate where other fields like `adTitle` are set and add:
```javascript
const targetAudienceEl = document.getElementById('adTargetAudience');
if (targetAudienceEl) targetAudienceEl.value = ad.target_audience || 'all';
```

## 6. Backend API (Verification)
Check [api/advertisements.php](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/api/advertisements.php). The [handleCreateAdvertisement](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/api/advertisements.php#387-636) and [handleUpdateAdvertisement](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/api/advertisements.php#637-889) functions already capture `$_POST['target_audience']` or have a placeholder.

Ensure this line uses the value from `$_POST`:
```php
$targetAudience = trim((string)($_POST['target_audience'] ?? 'all'));
```

---
**Tip:** After making these changes, clear your browser cache or force-reload (Ctrl+F5) to see the JavaScript updates!
