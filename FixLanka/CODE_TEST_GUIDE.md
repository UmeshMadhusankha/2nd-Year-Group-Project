# ✅ CODE TEST PREPARATION GUIDE
## Quick Reference for Final Presentation Exam

---

## 📍 QUICK NAVIGATION MAP

### **1. CRUD Operations Locations**

#### **CREATE Operations**
- **Advertisements**: `views/company/advertisements.php` (line ~620-680)
- **Contracts**: `views/company/workforce.php` (line ~2600-2700)
- **Projects**: `api/projects.php` (POST method)
- **Job Postings**: `views/company/workforce.php` (job posting section)

#### **READ Operations**
- **Advertisements**: `api/advertisements.php` (main query line ~45-80)
- **Contracts**: `api/contracts.php` (GET requests)
- **Projects**: `api/projects.php` (GET with filters)
- **Workforce**: `api/workforce.php`

#### **UPDATE Operations**
- **Advertisement Status**: `assets/javascript/company/advertisements-filters.js` (line ~200-450)
  - `pauseAdvertisement()` - line ~234
  - `resumeAdvertisement()` - line ~264
  - `editAdvertisement()` - line ~175
- **Contracts**: `api/contracts.php` (PUT method)
- **Projects**: `api/projects.php` (PUT method)

#### **DELETE Operations**
- **Advertisements**: `advertisements-filters.js` → `deleteAdvertisement()` (line ~202)
- **Contracts**: `api/contracts.php` (DELETE method)
- **Projects**: `api/projects.php` (DELETE method)

---

## 🎨 CSS MODIFICATION HOTSPOTS

### **Button Styling** (Most Likely to Be Asked)
**File**: `assets/css/company/advertisements.css`

```css
/* Primary Action Buttons (line ~1850) */
.action-btn-ad.primary {
    background: var(--primary-color);  /* Change this for button color */
    color: white;
    border: none;
}

/* Secondary Buttons (line ~1865) */
.action-btn-ad.secondary {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
}

/* Delete/Danger Buttons (line ~1895) */
.action-btn-ad.delete {
    background: #dc2626;  /* Change this for delete button color */
    color: white;
}
```

### **Color Variables** (Global Changes)
**File**: `assets/css/common/variables.css`

```css
:root {
    --primary-color: #14b8a6;      /* Main brand color (teal) */
    --primary-hover: #0d9488;      /* Hover state */
    --bg-primary: #1e1e2e;         /* Dark background */
    --bg-secondary: #27293d;       /* Card background */
    --text-primary: #ffffff;       /* Main text */
    --text-secondary: #9ca3af;     /* Secondary text */
}
```

### **Card/Modal Styling**
**File**: `assets/css/company/advertisements.css`

```css
/* Advertisement Cards (line ~450) */
.ad-card {
    background: var(--bg-secondary);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Modal Dialogs (line ~1750) */
.ad-modal {
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(10px);
}
```

---

## 💡 COMMON EXAM QUESTIONS & ANSWERS

### **Q1: "Change this button color to blue"**
**Answer & Location**: 
```css
/* File: assets/css/company/advertisements.css (line ~1850) */
.action-btn-ad.primary {
    background: #3b82f6;  /* Changed from teal to blue */
}
```

### **Q2: "Show me how you delete an advertisement"**
**Answer & Location**: 
```javascript
// File: assets/javascript/company/advertisements-filters.js (line ~202)
async deleteAdvertisement(adId) {
    if (!confirm('Delete this advertisement permanently?')) return;
    
    try {
        const response = await fetch(`/api/advertisements.php?id=${adId}`, {
            method: 'DELETE'
        });
        const data = await response.json();
        
        if (data.success) {
            this.showNotification('Advertisement deleted', 'success');
            this.filterAds(); // Refresh list
        }
    } catch (error) {
        this.showNotification('Delete failed', 'error');
    }
}
```

### **Q3: "Explain this database query"**
**Answer & Location**:
```php
// File: api/advertisements.php (line ~45)
$query = "SELECT 
            ad_id as id,
            title,
            status,
            -- This calculates click-through rate as percentage
            CASE 
                WHEN click_count > 0 AND view_count > 0 
                THEN ROUND((click_count / view_count) * 100, 2)
                ELSE 0.00
            END as click_through_rate
          FROM advertisement 
          WHERE provider_id = :company_id";

// Explanation: 
// - Fetches advertisements for a specific company
// - Calculates CTR (clicks ÷ views × 100)
// - Uses CASE to handle division by zero
// - Returns 0 if no views/clicks yet
```

### **Q4: "Add a new field to the advertisement form"**
**Answer & Location**:
```html
<!-- File: views/company/advertisements.php (line ~530) -->
<div class="form-group">
    <label for="newField">New Field</label>
    <input type="text" id="newField" name="newField" required>
</div>
```
Then update JavaScript:
```javascript
// Same file (line ~650)
const formData = {
    title: document.getElementById('adTitle').value,
    newField: document.getElementById('newField').value  // Add this
};
```

### **Q5: "Make this card rounded with shadow"**
**Answer & Location**:
```css
/* File: assets/css/company/advertisements.css */
.ad-card {
    border-radius: 16px;           /* Increase roundness */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);  /* Add shadow */
}
```

---

## 🔧 HELPER FUNCTIONS YOU CREATED

### **File**: `api/helpers.php` (YOU MUST EXPLAIN THIS)

```php
/**
 * Get company by user ID - extracts complex lookup logic
 * Used in multiple APIs to avoid code duplication
 */
function getCompanyByUserId($pdo, $userId) {
    // Try direct match first
    $stmt = $pdo->prepare("SELECT company_id, email, name 
                           FROM company 
                           WHERE company_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($company) return $company;
    
    // Fallback: match by email through user table
    // ... (explain the two-step lookup process)
}

/**
 * Standardize JSON responses across all APIs
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
```

**Why you created this**: 
- Reduces code duplication (DRY principle)
- Makes debugging easier (one place to fix)
- Standardizes API responses
- Shows understanding of refactoring

---

## 📊 DATABASE STRUCTURE (Be Ready to Explain)

### **Key Tables**:
```sql
-- Advertisement Table
CREATE TABLE advertisement (
    ad_id INT PRIMARY KEY AUTO_INCREMENT,
    provider_id INT,              -- Company ID
    provider_type VARCHAR(50),    -- 'company' or 'repairer'
    title VARCHAR(255),
    description TEXT,
    status ENUM('pending', 'active', 'paused', 'rejected'),
    view_count INT DEFAULT 0,     -- Impressions
    click_count INT DEFAULT 0,    -- Clicks
    start_date DATE,
    end_date DATE
);

-- Company Table
CREATE TABLE company (
    company_id INT PRIMARY KEY,
    email VARCHAR(255),
    name VARCHAR(255),
    -- ... other fields
);
```

---

## ⚡ QUICK TIPS FOR EXAM

### **1. Finding Code Fast**
- **Ctrl+P**: Quick file open
- **Ctrl+F**: Find in file
- **Ctrl+Shift+F**: Find in all files
- Remember line numbers from this guide!

### **2. Common Mistakes to Avoid**
- ❌ Don't forget semicolons in JavaScript
- ❌ Don't mix single/double quotes
- ❌ Don't forget `var(--variable-name)` for CSS variables
- ❌ Don't forget `:` in PHP prepared statements

### **3. Show You Understand**
- **Explain WHAT**: "This fetches advertisements"
- **Explain WHY**: "We check status to filter by active/pending"
- **Explain HOW**: "Using a CASE statement for dynamic status"

### **4. If You Forget Something**
- "Let me check my helper functions file"
- "I need to review the database query structure"
- "Let me verify the CSS variable name"
- **Show process, not just knowledge**

---

## 🎯 MOST LIKELY EXAM SCENARIOS

### **Scenario 1: CSS Change** (80% probability)
"Change the primary button color from teal to purple"

**Answer**:
```css
/* assets/css/common/variables.css */
:root {
    --primary-color: #9333ea;  /* Purple */
}
```

### **Scenario 2: CRUD Operation** (90% probability)
"Show me the delete function for advertisements"

**Answer**: Point to `advertisements-filters.js` line ~202, explain:
1. Confirm dialog prevents accidental deletion
2. Fetch with DELETE method
3. Error handling with try-catch
4. UI refresh after success

### **Scenario 3: Database Query** (70% probability)
"Explain this query and what it does"

**Answer**: Be ready to explain:
- JOIN operations
- WHERE clauses for filtering
- CASE statements for computed fields
- PDO prepared statements for security

### **Scenario 4: Add New Feature** (60% probability)
"Add a 'Featured' checkbox to the advertisement form"

**Answer**: Show 3 steps:
1. Add HTML input
2. Update JavaScript to capture value
3. Update database table/API (if time)

---

## 📝 KEY POINTS TO EMPHASIZE

1. **Helper Functions**: "I extracted common code to avoid duplication"
2. **Comments**: "I added comments to explain complex logic"
3. **Error Handling**: "I use try-catch to gracefully handle errors"
4. **Security**: "I use prepared statements to prevent SQL injection"
5. **User Experience**: "I add confirmation dialogs for destructive actions"

---

## 🚀 FINAL CHECKLIST

Before exam, make sure you can:
- [ ] Navigate to any CRUD operation in under 10 seconds
- [ ] Explain the helper functions you created
- [ ] Change any button color instantly
- [ ] Add a new form field with JavaScript handler
- [ ] Explain status-based button rendering
- [ ] Describe the database query logic
- [ ] Show error handling implementation

---

## 💪 YOU'VE GOT THIS!

**Remember**: You built this system. You understand:
- Dynamic action buttons based on status
- Helper functions for code reusability
- Clean code without debug statements
- Professional error handling
- Responsive CSS with variables

**Be confident**. Explain your thought process. Show you understand WHY, not just WHAT.

Good luck! 🎉
