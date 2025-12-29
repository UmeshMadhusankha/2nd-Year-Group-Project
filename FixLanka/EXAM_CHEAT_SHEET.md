# 🚀 EXAM DAY CHEAT SHEET
## Ultra-Quick Reference (Print This!)

---

## ⚡ INSTANT NAVIGATION

### CRUD Operations
| Operation | File | Line | Function |
|-----------|------|------|----------|
| **Delete Ad** | `advertisements-filters.js` | 202 | `deleteAdvertisement()` |
| **Edit Ad** | `advertisements-filters.js` | 175 | `editAdvertisement()` |
| **Pause Ad** | `advertisements-filters.js` | 234 | `pauseAdvertisement()` |
| **Resume Ad** | `advertisements-filters.js` | 264 | `resumeAdvertisement()` |
| **Get Ads** | `api/advertisements.php` | 45 | Main query |

### CSS Hotspots
| Element | File | Line | Property |
|---------|------|------|----------|
| **Button Color** | `advertisements.css` | 1850 | `background` |
| **Brand Color** | `variables.css` | 12 | `--primary-color` |
| **Card Style** | `advertisements.css` | 450 | `.ad-card` |
| **Delete Btn** | `advertisements.css` | 1895 | `.delete` |

---

## 🎯 TOP 5 EXAM QUESTIONS

### 1. "Change button to blue"
```css
.action-btn-ad.primary { background: #3b82f6; }
```

### 2. "Show delete function"
```javascript
async deleteAdvertisement(adId) {
    if (!confirm('Delete?')) return;
    await fetch(`/api/advertisements.php?id=${adId}`, { method: 'DELETE' });
}
```

### 3. "Explain this query"
```php
// Fetches ads for company, calculates CTR
SELECT ad_id, title,
  CASE WHEN clicks > 0 THEN (clicks/views)*100 ELSE 0 END as ctr
FROM advertisement WHERE provider_id = :company_id
```

### 4. "Add new field"
```html
<input type="text" id="newField">
```
```javascript
formData.newField = document.getElementById('newField').value;
```

### 5. "Explain helper function"
```php
// Extracts company lookup - avoids duplicate code in 5+ APIs
function getCompanyByUserId($pdo, $userId) {
    // Try direct match, fallback to email lookup
}
```

---

## 💡 KEY TALKING POINTS

**When explaining your code, say:**
- "I extracted this to a helper to avoid duplication"
- "I use prepared statements for SQL injection prevention"
- "I add confirmation dialogs for destructive actions"
- "I standardized error handling across all APIs"
- "I use CSS variables for easy theme changes"

---

## 📁 FILES YOU CREATED/MODIFIED

**Created:**
- ✅ `api/helpers.php` - 7 utility functions
- ✅ `CODE_TEST_GUIDE.md` - Full exam guide

**Optimized:**
- ✅ `api/advertisements.php` - Refactored with helpers
- ✅ `advertisements-filters.js` - 12 action methods

---

## ⏱️ 10-SECOND RULES

If asked to:
- **Change color** → Variables.css or element selector
- **Show CRUD** → advertisements-filters.js
- **Explain query** → api/advertisements.php line 45
- **Add field** → HTML input + JavaScript formData

---

## 🔑 CONFIDENCE BOOSTERS

You built:
- ✅ 12 action handlers with status logic
- ✅ Helper functions (shows senior-level thinking)
- ✅ Clean code without debug statements
- ✅ Professional error handling
- ✅ Responsive, modern UI

**You know this code. Trust yourself!**

---

## 📞 EMERGENCY LOOKUP

Forgot something? Say:
- "Let me check my helper functions"
- "I need to verify the variable name"
- "Let me look at my documentation"

**Showing process is better than guessing!**

---

## ✅ PRE-EXAM CHECKLIST

- [ ] Ran `cleanup_debug_code.ps1`
- [ ] Read `CODE_TEST_GUIDE.md`
- [ ] Can find delete function in 5 seconds
- [ ] Can change button color in 5 seconds
- [ ] Can explain helper functions
- [ ] Can explain status-based buttons

---

## 🎓 FINAL WISDOM

**Remember:**
- Quality > Speed (explain while you work)
- Process > Memory (show how you find things)
- Understanding > Memorization (explain WHY)

**You've got this! 🚀**
