# FixLanka Design System Documentation

## Overview
This document describes the unified design system used across the FixLanka application. All components follow consistent patterns defined in the common CSS files.

---

## File Structure

```
assets/css/common/
├── variables.css       # Design tokens (colors, spacing, typography)
├── buttons.css         # Unified button components
└── DESIGN_SYSTEM.md   # This documentation
```

---

## Design Tokens (variables.css)

### Colors
- **Primary**: `--primary-color: #0abab5` (Teal brand color)
- **Primary Dark**: `--primary-dark: #08908c`
- **Secondary**: `--secondary-color: #0a2e33`
- **Accent**: `--accent-color: #2a515c`

### Status Colors
- **Success**: `--success-color: #10b981` (Green)
- **Warning**: `--warning-color: #f59e0b` (Orange)
- **Danger**: `--danger-color: #ef4444` (Red)
- **Info**: `--info-color: #3b82f6` (Blue)

### Spacing System
```css
--spacing-xs: 4px
--spacing-sm: 8px
--spacing-md: 16px
--spacing-lg: 24px
--spacing-xl: 32px
--spacing-2xl: 48px
```

### Typography Scale
```css
--font-size-xs: 10px
--font-size-sm: 12px
--font-size-base: 14px
--font-size-md: 16px
--font-size-lg: 18px
--font-size-xl: 22px
--font-size-2xl: 26px
--font-size-3xl: 30px
```

### Border Radius
```css
--border-radius: 12px (default)
--border-radius-sm: 8px
--border-radius-lg: 16px
--border-radius-xl: 20px
```

### Shadows
```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05)
--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1)
--shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1)
--shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15)
```

---

## Button Components (buttons.css)

### 1. Large Action Buttons

**Class**: `.action-btn`

**Purpose**: Primary CTAs in headers and forms

**Variants**:
- `.action-btn.primary` - Main actions (teal background)
- `.action-btn.secondary` - Secondary actions (outlined)
- `.action-btn.outline` - Tertiary actions (transparent)
- `.action-btn.danger` - Destructive actions (red)
- `.action-btn.success` - Confirmation actions (green)

**Sizes**:
- `.action-btn.small` - Compact version
- `.action-btn` (default) - Standard size
- `.action-btn.large` - Prominent version

**Example**:
```html
<button class="action-btn primary">
  <i class="fas fa-plus"></i>
  New Project
</button>
```

---

### 2. View Toggle Buttons

**Class**: `.view-toggle`

**Purpose**: Switch between different view modes (e.g., table vs cards)

**Container**: `.view-toggle-group`

**States**:
- Default: Inactive state
- `.active` - Currently selected view

**Example**:
```html
<div class="view-toggle-group">
  <button class="view-toggle active" data-view="table">
    <i class="fas fa-table"></i> Table
  </button>
  <button class="view-toggle" data-view="cards">
    <i class="fas fa-th-large"></i> Cards
  </button>
</div>
```

**Features**:
- Subtle hover effect with background color change
- Active state with primary color background
- Smooth transitions
- Icon + text layout

---

### 3. Small Action Buttons (Icon Buttons)

**Class**: `.action-btn-sm`

**Purpose**: Compact icon-only buttons in tables and cards

**Variants**:
- `.action-btn-sm.primary` - View/preview actions
- `.action-btn-sm.secondary` - Edit actions
- `.action-btn-sm.info` - Additional info/chat actions

**Container**: `.action-btn-group` (for grouping multiple buttons)

**Example**:
```html
<div class="action-btn-group">
  <button class="action-btn-sm primary" title="View">
    <i class="fas fa-eye"></i>
  </button>
  <button class="action-btn-sm secondary" title="Edit">
    <i class="fas fa-edit"></i>
  </button>
  <button class="action-btn-sm info" title="Chat">
    <i class="fas fa-comments"></i>
  </button>
</div>
```

**Features**:
- 36px × 36px fixed size
- Icon-only (no text)
- Hover effect with lift animation
- Color-coded by action type

---

### 4. Modal Buttons

**Classes**: `.btn-cancel`, `.btn-save`, `.btn-primary`, `.btn-secondary`

**Purpose**: Actions in modal dialogs and forms

**Container**: `.modal-actions`

**Types**:
- `.btn-cancel` - Cancel/close action (outlined)
- `.btn-save` / `.btn-primary` - Save/confirm action (teal)
- `.btn-secondary` - Alternative action (outlined)

**Example**:
```html
<div class="modal-actions">
  <button class="btn-cancel">
    <i class="fas fa-times"></i>
    Cancel
  </button>
  <button class="btn-save">
    <i class="fas fa-check"></i>
    Save Changes
  </button>
</div>
```

**Features**:
- Standard height: 40px
- Consistent padding and spacing
- Clear visual hierarchy (save is prominent)
- Disabled state support

---

## Usage Guidelines

### DO ✅
- Always import `variables.css` first
- Import `buttons.css` after variables but before page-specific CSS
- Use the predefined button classes consistently
- Follow the established color meanings (primary = action, danger = delete, etc.)
- Use appropriate button types for their intended purpose

### DON'T ❌
- Don't duplicate button styles in page-specific CSS files
- Don't create custom button styles without discussing with the team
- Don't mix button types (e.g., don't use `.action-btn` for table actions)
- Don't override common styles in page-specific files
- Don't forget to add the buttons.css import to new pages

---

## Import Order

**Correct order in HTML `<head>`:**

```html
<link rel="stylesheet" href="../../assets/css/common/variables.css">
<link rel="stylesheet" href="../../assets/css/common/buttons.css">
<link rel="stylesheet" href="../../assets/css/company/sidebar.css">
<link rel="stylesheet" href="../../assets/css/company/topbar.css">
<link rel="stylesheet" href="../../assets/css/company/[page-name].css">
```

---

## Button States

All buttons support these states:

### Default
- Normal appearance
- Subtle hover effect

### Hover
- Slight elevation (transform: translateY(-2px))
- Box shadow increase
- Color intensification

### Active/Pressed
- Reduced elevation
- Slightly darker color

### Disabled
- 50% opacity
- Cursor: not-allowed
- No hover effects
- No pointer events

**Apply disabled state:**
```html
<button class="action-btn primary" disabled>Disabled Button</button>
```

---

## Accessibility

All button components follow accessibility best practices:

- Proper semantic HTML (`<button>` elements)
- Sufficient color contrast ratios
- Keyboard navigation support
- Focus indicators
- Screen reader friendly
- Touch-friendly sizing (minimum 36px height)

---

## Responsive Design

Button components automatically adapt to screen sizes:

### Desktop (> 768px)
- Full size and padding
- All features enabled

### Tablet (480px - 768px)
- Slightly reduced padding
- Maintained functionality

### Mobile (< 480px)
- Full-width layout for `.action-btn` in `.header-actions`
- Compact sizing where appropriate
- Touch-optimized spacing

---

## Migration Guide

If you have existing button styles in page-specific CSS:

1. **Check if the button matches a common type**
   - Does it look like an action button, toggle, or icon button?

2. **Replace custom classes with common classes**
   ```html
   <!-- Before -->
   <button class="custom-view-btn active">Table</button>
   
   <!-- After -->
   <button class="view-toggle active">Table</button>
   ```

3. **Remove duplicate CSS**
   - Delete the custom button styles from page-specific CSS
   - Add a comment referencing buttons.css

4. **Test thoroughly**
   - Verify visual appearance
   - Check hover/active states
   - Test on different screen sizes

---

## Common Patterns

### Header Action Buttons
```html
<div class="header-actions">
  <button class="action-btn primary">
    <i class="fas fa-plus"></i>
    New Item
  </button>
  <button class="action-btn outline">
    <i class="fas fa-filter"></i>
    Filter
  </button>
</div>
```

### Table Row Actions
```html
<td class="actions">
  <div class="action-btn-group">
    <button class="action-btn-sm primary" onclick="viewItem(id)">
      <i class="fas fa-eye"></i>
    </button>
    <button class="action-btn-sm secondary" onclick="editItem(id)">
      <i class="fas fa-edit"></i>
    </button>
  </div>
</td>
```

### Modal Footer
```html
<div class="modal-actions">
  <button class="btn-cancel" onclick="closeModal()">
    Cancel
  </button>
  <button class="btn-save" onclick="saveChanges()">
    Save Changes
  </button>
</div>
```

### View Switcher
```html
<div class="view-toggle-group">
  <button class="view-toggle active" onclick="switchView('table')">
    <i class="fas fa-table"></i> Table
  </button>
  <button class="view-toggle" onclick="switchView('cards')">
    <i class="fas fa-th-large"></i> Cards
  </button>
</div>
```

---

## Future Enhancements

Planned additions to the design system:

- [ ] Form input components (separate file)
- [ ] Badge/status components
- [ ] Loading states and spinners
- [ ] Toast/notification styles
- [ ] Card components
- [ ] Table styling standards
- [ ] Modal overlay system

---

## Questions or Issues?

If you need a button style that doesn't exist in the common system:

1. Check if you can use an existing variant
2. Discuss with the team if a new common style is needed
3. If it's truly page-specific, document why it can't use common styles
4. Consider if it should become a new common component

---

**Last Updated**: January 2025  
**Maintained By**: FixLanka Development Team
