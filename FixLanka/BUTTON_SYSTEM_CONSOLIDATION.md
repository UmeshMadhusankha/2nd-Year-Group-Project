# Button System Consolidation

## Overview
All button styles have been consolidated into `assets/css/common/buttons.css` for consistency across the entire application.

## Complete Button Types in System

### 1. **`.action-btn`** - Large Action Buttons (Header CTAs)
**Usage**: Primary actions in headers, navigation bars
**Variants**: `.primary`, `.secondary`, `.outline`, `.danger`, `.success`
**Sizes**: `.small`, `.large`
**Features**:
- Shimmer effect on hover
- 44px minimum height
- Icon support (left/right)
- Responsive sizing

**Example**:
```html
<button class="action-btn primary">
    <i class="fas fa-plus"></i> Add New
</button>
```

### 2. **`.action-btn-sm`** - Small Icon Buttons (Table Actions)
**Usage**: Action buttons in tables, cards (view, edit, delete)
**Variants**: `.primary`, `.secondary`, `.info`, `.success`, `.danger`
**Features**:
- 36x36px fixed size
- Icon-only display
- Hover transform effect
- Color variants for different actions

**Example**:
```html
<button class="action-btn-sm info" title="View">
    <i class="fas fa-eye"></i>
</button>
```

### 3. **`.tab-btn`** - Tab Navigation Buttons
**Usage**: Filter tabs, navigation tabs, switching between views
**Features**:
- Active state with gradient
- Badge support for counters
- Flex layout with icons
- Hover highlight effect

**Example**:
```html
<button class="tab-btn active">
    <i class="fas fa-list"></i> All Items
    <span class="badge">5</span>
</button>
```

### 4. **`.filter-btn`** - Filter Control Buttons
**Usage**: Filtering controls, search filters
**Features**:
- Active state styling
- Icon + text layout
- Shadow effects
- Transform on hover

**Example**:
```html
<button class="filter-btn active">
    <i class="fas fa-filter"></i>
    <span>Active</span>
</button>
```

### 5. **`.action-button`** - Full-Width Card Actions
**Usage**: Actions at the bottom of cards (view details, select, etc.)
**Variants**: `.primary`, `.secondary`
**Features**:
- Full width
- Border-top separator
- Centered content
- Hover background change

**Example**:
```html
<button class="action-button primary">
    <i class="fas fa-arrow-right"></i> View Details
</button>
```

### 6. **`.view-toggle`** - View Mode Toggle Buttons
**Usage**: Switch between card/list views, different display modes
**Features**:
- Group container (`.view-toggle-group`)
- Active state
- Icon + text display
- Compact height (32px)

**Example**:
```html
<div class="view-toggle-group">
    <button class="view-toggle active">
        <i class="fas fa-th"></i> Grid
    </button>
    <button class="view-toggle">
        <i class="fas fa-list"></i> List
    </button>
</div>
```

### 7. **`.btn-save` / `.btn-primary`** - Modal Save Buttons
**Usage**: Primary action in modals, forms
**Features**:
- 40px height
- Primary color styling
- Transform on hover
- Used in modal actions

**Example**:
```html
<div class="modal-actions">
    <button class="btn-cancel">Cancel</button>
    <button class="btn-save">Save Changes</button>
</div>
```

### 8. **`.btn-cancel`** - Modal Cancel Buttons
**Usage**: Cancel/close actions in modals
**Features**:
- Transparent background
- Border styling
- Hover state

### 9. **`.btn-secondary`** - Secondary Modal Buttons
**Usage**: Alternative actions in modals
**Features**:
- Similar to cancel but with primary color text
- Used for non-destructive alternatives

### 10. **`.submit-btn`** - Form Submit Buttons
**Usage**: Form submission buttons
**Features**:
- Full width
- Gradient background
- Shadow effect
- Optimized for forms

**Example**:
```html
<button class="submit-btn">
    <i class="fas fa-paper-plane"></i> Submit
</button>
```

### 11. **`.reset-btn`** - Form Reset Buttons
**Usage**: Form reset/clear buttons
**Features**:
- Transparent background
- Border outline
- Secondary styling

**Example**:
```html
<button class="reset-btn">
    <i class="fas fa-redo"></i> Reset
</button>
```

## Color Variants

### Primary Actions
- **`.primary`** - Main actions (teal gradient)
- **`.success`** - Positive actions (green)
- **`.danger`** - Destructive actions (red)
- **`.info`** - Information actions (blue)
- **`.secondary`** - Secondary actions (gray)
- **`.outline`** - Outlined style (transparent with border)

## Button States

### All Buttons Support
- `:hover` - Transform/shadow effects
- `:active` - Press state
- `:disabled` or `.disabled` - Disabled state (50% opacity, no interaction)

## Usage Guidelines

### ✅ DO
- Use `.action-btn` for main CTAs in headers
- Use `.action-btn-sm` for inline table/card actions
- Use `.tab-btn` for navigation and filters
- Use `.submit-btn` for form submissions
- Use appropriate color variants for action context
- Always provide tooltips for icon-only buttons

### ❌ DON'T
- Don't create custom button styles in page-specific CSS
- Don't use multiple button classes together (except variants)
- Don't override core button styles
- Don't forget disabled states for unavailable actions

## Files Affected

### Consolidated From:
- `assets/css/company/workforce.css` - `.tab-btn`, `.filter-btn`, `.action-button`
- `assets/css/user/contact-us.css` - `.submit-btn`
- `assets/css/repairer/earnings.css` - `.tab-btn`
- `assets/css/user/dashboard.css` - `.filter-btn`, `.tab-btn`

### Now Centralized In:
- `assets/css/common/buttons.css` - ALL button styles

## Migration Notes

### For Developers
1. Remove duplicate button styles from page-specific CSS files
2. Import `buttons.css` after `variables.css` in all pages
3. Use standard button classes instead of custom styles
4. Refer to this document for button selection

### Breaking Changes
- None - All existing button classes maintained
- Added new consolidated styles for consistency
- Old custom styles should be removed gradually

## Responsive Behavior

### Desktop (>768px)
- Full button sizes and text
- All icons and labels visible

### Tablet (≤768px)
- Slightly reduced padding
- Maintained functionality

### Mobile (≤480px)
- Icon-only for `.tab-btn` (text hidden)
- Full-width for `.filter-btn`
- Stacked layout for button groups

## Examples by Use Case

### Dashboard Header Actions
```html
<div class="header-actions">
    <button class="action-btn primary">
        <i class="fas fa-plus"></i> New Request
    </button>
    <button class="action-btn outline">
        <i class="fas fa-download"></i> Export
    </button>
</div>
```

### Table Row Actions
```html
<div class="action-btn-group">
    <button class="action-btn-sm info" title="View">
        <i class="fas fa-eye"></i>
    </button>
    <button class="action-btn-sm success" title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <button class="action-btn-sm danger" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</div>
```

### Filter Tabs
```html
<div class="freelancer-filter-tabs">
    <button class="tab-btn active">
        <i class="fas fa-list"></i> All
    </button>
    <button class="tab-btn">
        <i class="fas fa-check"></i> Available
    </button>
    <button class="tab-btn">
        <i class="fas fa-clock"></i> Busy
    </button>
</div>
```

### Modal Actions
```html
<div class="modal-actions">
    <button class="btn-cancel" onclick="closeModal()">
        Cancel
    </button>
    <button class="btn-save" onclick="saveData()">
        <i class="fas fa-save"></i> Save Changes
    </button>
</div>
```

### Form Submission
```html
<form>
    <!-- form fields -->
    <button type="submit" class="submit-btn">
        <i class="fas fa-paper-plane"></i> Submit Form
    </button>
    <button type="reset" class="reset-btn">
        <i class="fas fa-redo"></i> Reset
    </button>
</form>
```

## Maintenance

### Adding New Button Types
1. Add to `buttons.css` with proper documentation
2. Follow existing naming conventions
3. Use CSS variables for colors
4. Include hover and disabled states
5. Update this document

### Modifying Existing Buttons
1. Test across all pages using the button
2. Ensure backward compatibility
3. Update documentation if behavior changes
4. Communicate changes to team

---

**Last Updated**: November 2024
**Maintainer**: Development Team
**Status**: ✅ Consolidated and Standardized
