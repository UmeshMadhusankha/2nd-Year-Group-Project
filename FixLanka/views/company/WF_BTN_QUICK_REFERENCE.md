# WF-BTN Quick Reference Guide

## 🎨 Button Variants Cheat Sheet

```html
<!-- View Button (Blue) -->
<button class="wf-btn wf-btn-view" onclick="viewDetails()">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>

<!-- Assign Button (Purple) -->
<button class="wf-btn wf-btn-assign" onclick="assignJob()">
    <i class="fas fa-plus-circle"></i>
    <span>Assign</span>
</button>

<!-- Edit Button (Orange) -->
<button class="wf-btn wf-btn-edit" onclick="editItem()">
    <i class="fas fa-edit"></i>
    <span>Edit</span>
</button>

<!-- Success Button (Green) -->
<button class="wf-btn wf-btn-success" onclick="acceptAction()">
    <i class="fas fa-check-circle"></i>
    <span>Accept</span>
</button>

<!-- Danger Button (Red) -->
<button class="wf-btn wf-btn-danger" onclick="deleteAction()">
    <i class="fas fa-times-circle"></i>
    <span>Decline</span>
</button>

<!-- Secondary Button (Gray) -->
<button class="wf-btn wf-btn-secondary" onclick="secondaryAction()">
    <i class="fas fa-sync"></i>
    <span>Renew</span>
</button>

<!-- Warning Button (Yellow) -->
<button class="wf-btn wf-btn-warning" onclick="warningAction()">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Warning</span>
</button>

<!-- Info Button (Cyan) -->
<button class="wf-btn wf-btn-info" onclick="infoAction()">
    <i class="fas fa-info-circle"></i>
    <span>Info</span>
</button>
```

## 📏 Button Sizes

```html
<!-- Small -->
<button class="wf-btn wf-btn-sm wf-btn-view">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>

<!-- Default (no size class needed) -->
<button class="wf-btn wf-btn-view">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>

<!-- Large -->
<button class="wf-btn wf-btn-lg wf-btn-view">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>
```

## 🎭 Button States

```html
<!-- Disabled -->
<button class="wf-btn wf-btn-view disabled">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>

<!-- Loading (add via JS) -->
<button class="wf-btn wf-btn-view loading">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>
```

## 📦 Button Groups

```html
<div class="action-group">
    <button class="wf-btn wf-btn-view">
        <i class="fas fa-eye"></i>
        <span>View</span>
    </button>
    <button class="wf-btn wf-btn-edit">
        <i class="fas fa-edit"></i>
        <span>Edit</span>
    </button>
    <button class="wf-btn wf-btn-danger">
        <i class="fas fa-trash"></i>
        <span>Delete</span>
    </button>
</div>
```

## 🏷️ Status Badges

```html
<!-- Pending -->
<span class="status-badge pending-badge">
    <i class="fas fa-clock"></i> Pending
</span>

<!-- Active/Available -->
<span class="status-badge available">
    <i class="fas fa-check"></i> Available
</span>

<!-- Busy/Assigned -->
<span class="status-badge busy">
    <i class="fas fa-briefcase"></i> Busy
</span>

<!-- Inactive -->
<span class="status-badge inactive">
    <i class="fas fa-ban"></i> Inactive
</span>

<!-- Rejected -->
<span class="status-badge rejected">
    <i class="fas fa-times"></i> Rejected
</span>
```

## 🎨 Color Reference

| Variant | Color | Use Case |
|---------|-------|----------|
| View | Blue (#3498db) | View details, profiles |
| Assign | Purple (#9b59b6) | Assign jobs, tasks |
| Edit | Orange (#f39c12) | Edit, modify |
| Success | Green (#27ae60) | Accept, approve |
| Danger | Red (#e74c3c) | Delete, decline |
| Secondary | Gray (#95a5a6) | Renew, secondary |
| Warning | Yellow (#f1c40f) | Warnings, caution |
| Info | Cyan (#1abc9c) | Information, help |

## ⚡ JavaScript Actions

```javascript
// View employee details
viewEmployeeDetails(employeeId);

// View freelancer details
viewFreelancerDetails(freelancerId);

// View application details
viewApplicationDetails(applicationId);

// Assign job
assignJob(employeeId); // Opens assignment drawer

// Approve application
approveApplication(applicationId); // Opens contract creation

// Reject application
rejectApplication(applicationId); // Confirms and rejects

// Edit employee
editEmployee(employeeId); // Opens edit modal

// Edit freelancer
editFreelancer(freelancerId); // Opens edit modal

// Renew contract
renewContract(freelancerId); // Opens renewal form
```

## 📱 Responsive Behavior

| Screen Size | Layout | Button Width | Gap |
|-------------|--------|--------------|-----|
| Desktop (>992px) | Horizontal | Auto | 10px |
| Tablet (768-992px) | Horizontal | Auto | 8px |
| Mobile (<768px) | Vertical Stack | 100% | 8px |
| Small (<480px) | Vertical Stack | 100% | 8px |

## 🎯 Best Practices

### ✅ DO
- Always include both icon and text
- Use semantic icons (eye for view, edit for edit)
- Group related buttons in `.action-group`
- Use appropriate variant for action type
- Add `title` attribute for tooltips

### ❌ DON'T
- Don't mix button systems (stick to wf-btn)
- Don't use inline styles
- Don't omit icon or text
- Don't create buttons wider than needed
- Don't use wrong color for action type

## 🔧 Common Patterns

### Employee Card Actions
```html
<div class="card-actions">
    <button class="wf-btn wf-btn-view" onclick="viewEmployeeDetails('${id}')">
        <i class="fas fa-user"></i>
        <span>View Profile</span>
    </button>
    <button class="wf-btn wf-btn-assign" onclick="assignJob('${id}')">
        <i class="fas fa-briefcase"></i>
        <span>Assign Job</span>
    </button>
    <button class="wf-btn wf-btn-edit" onclick="editEmployee('${id}')">
        <i class="fas fa-edit"></i>
        <span>Edit</span>
    </button>
</div>
```

### Application Actions
```html
<div class="action-group">
    <button class="wf-btn wf-btn-view" onclick="viewApplicationDetails('${id}')">
        <i class="fas fa-file-alt"></i>
        <span>View</span>
    </button>
    <button class="wf-btn wf-btn-success" onclick="approveApplication('${id}')">
        <i class="fas fa-check-circle"></i>
        <span>Accept</span>
    </button>
    <button class="wf-btn wf-btn-danger" onclick="rejectApplication('${id}')">
        <i class="fas fa-times-circle"></i>
        <span>Decline</span>
    </button>
</div>
```

### With Status Badge
```html
<div class="application-actions">
    <span class="status-badge pending-badge">
        <i class="fas fa-clock"></i> Pending
    </span>
    <div class="action-group">
        <!-- buttons here -->
    </div>
</div>
```

## 🎨 Icon Recommendations

| Action | Icon Class | Alternative |
|--------|-----------|-------------|
| View Profile | fa-user | fa-id-card |
| View Details | fa-eye | fa-search |
| View File | fa-file-alt | fa-folder-open |
| Edit | fa-edit | fa-pen |
| Delete | fa-trash | fa-trash-alt |
| Assign | fa-plus-circle | fa-briefcase |
| Accept | fa-check-circle | fa-thumbs-up |
| Decline | fa-times-circle | fa-thumbs-down |
| Renew | fa-sync | fa-file-contract |
| Save | fa-save | fa-check |
| Cancel | fa-times | fa-ban |

## 🚀 Quick Start

1. **Add button to HTML**
```html
<button class="wf-btn wf-btn-view" onclick="myAction()">
    <i class="fas fa-eye"></i>
    <span>View</span>
</button>
```

2. **Create action handler**
```javascript
function myAction() {
    console.log('Button clicked!');
    showNotification('Action completed', 'success');
}
```

3. **Test responsiveness**
- Desktop: Buttons horizontal
- Mobile: Buttons stack vertically

## 📊 Size Guide

| Size | Padding | Font | Icon | Min Width | Use Case |
|------|---------|------|------|-----------|----------|
| Small | 8×14px | 13px | 14px | 90px | Compact lists |
| Default | 11×20px | 14px | 16px | 110px | Standard use |
| Large | 14×24px | 16px | 18px | 140px | Hero actions |

## 🎯 Loading State Example

```javascript
// Show loading
const button = document.querySelector('.wf-btn-view');
button.classList.add('loading');

// Simulate async action
setTimeout(() => {
    // Remove loading
    button.classList.remove('loading');
    showNotification('Action completed!', 'success');
}, 2000);
```

## 💡 Tips & Tricks

### Tip 1: Dynamic Button States
```javascript
// Disable button
button.classList.add('disabled');

// Enable button
button.classList.remove('disabled');
```

### Tip 2: Conditional Rendering
```javascript
const isAvailable = status === 'available';
const assignButton = isAvailable 
    ? `<button class="wf-btn wf-btn-assign">Assign</button>`
    : '';
```

### Tip 3: Button Groups with Status
```javascript
`
<div class="application-actions">
    <span class="status-badge ${statusClass}">${status}</span>
    <div class="action-group">
        ${buttons}
    </div>
</div>
`
```

---

## 📚 Related Files

- `COMPLETE_BUTTON_REBUILD.md` - Full documentation
- `workforce.php` - Implementation
- `workforce.css` - Button styles

---

**Quick Reference Version**: 1.0  
**Last Updated**: October 22, 2025  
**Status**: ✅ Production Ready
