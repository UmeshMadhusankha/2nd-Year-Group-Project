# 🔧 ISSUE #1: Dynamic Budget Management System

---

## 📋 Problem Statement

### **Current Limitation:**
When a company provides a quotation, the budget is treated as a **fixed, final amount**. However, in real-world construction and repair scenarios:

- **Hidden damage** may be discovered during work (e.g., termite damage behind walls)
- **Scope changes** may be necessary (e.g., outdated plumbing needs replacement)
- **Material price fluctuations** may occur during project execution
- **Unforeseen complications** may arise (e.g., structural issues)

**User's Exact Concern:**  
> "Budget is just our estimation. We can face some issues when we going through the work. That budget is not enough like that. So we need to change the budget. How we handle that?"

### **Real-World Scenario:**
```
Initial Quote: $5,000 for bathroom renovation
During Work: Discovered mold behind tiles requiring additional treatment
Extra Cost: $1,200 for mold remediation + tile replacement
Problem: No system to request budget increase or get customer approval
Result: Project stalls, disputes arise, customer trust erodes
```

---

## ✅ Solution Design

### **1. Budget Type System**

Add **two budget types** to quotations:

| Budget Type | Description | Use Case |
|-------------|-------------|----------|
| **Fixed** | Budget cannot change. Customer pays exact quoted amount | Simple, predictable projects (painting, cleaning) |
| **Flexible** | Budget can vary within a specified range | Complex projects (renovations, repairs with unknowns) |

### **2. Variance Range (For Flexible Budgets)**

Allow company to set **allowed variance percentage**:

```
Options:
- ±5%  (Small variations, predictable projects)
- ±10% (Moderate variations, standard repairs)
- ±15% (Large variations, old properties)
- Custom (Enter percentage manually, e.g., ±20%, ±25%)
```

**Example:**
```
Base Budget: $10,000
Variance: ±10%
Allowed Range: $9,000 - $11,000

If cost goes to $10,500 → NO approval needed (within range)
If cost goes to $11,500 → Customer approval REQUIRED
```

### **3. Budget Change Request Workflow**

```
┌─────────────────────────────────────────────────────────────────┐
│                   BUDGET CHANGE REQUEST FLOW                     │
└─────────────────────────────────────────────────────────────────┘

Step 1: Company Discovers Issue
   ↓
   - Takes photos of unexpected problem
   - Documents reason for budget increase
   - Calculates additional cost needed

Step 2: Company Submits Change Request
   ↓
   - Opens "Request Budget Change" form in project
   - Enters new budget amount
   - Provides detailed reason
   - Uploads evidence (photos of damage, videos)
   - Submits request

Step 3: Customer Receives Notification
   ↓
   - Email: "Budget Change Request for [Project Name]"
   - SMS: "Your contractor requested $X budget increase"
   - In-app notification badge

Step 4: Customer Reviews Request
   ↓
   - Views original budget vs requested budget
   - Sees evidence photos/videos
   - Reads detailed explanation
   - Can ask questions via in-app chat

Step 5: Customer Decision
   ↓
   ┌────────────────┬────────────────┐
   │   APPROVE      │    DECLINE     │
   └────────────────┴────────────────┘
        ↓                    ↓
   Budget Updated      Request Rejected
   Work Continues      Company Notified
   Payment Adjusted    Negotiate via Chat
                       OR Cancel Project
```

---

## 🗄️ Database Schema Implementation

### **A. Update `companyquotation` Table**

```sql
ALTER TABLE companyquotation 
ADD COLUMN budget_type ENUM('fixed', 'flexible') NOT NULL DEFAULT 'fixed'
COMMENT 'Whether budget can change during project',

ADD COLUMN variance_range INT NULL DEFAULT NULL
COMMENT 'Allowed variance percentage for flexible budgets (e.g., 10 for ±10%)',

ADD COLUMN current_budget DECIMAL(10,2) NULL DEFAULT NULL
COMMENT 'Current approved budget if changed from original';
```

**Example Data:**
```
quotation_id: 42
budget: 10000.00
budget_type: 'flexible'
variance_range: 10
current_budget: 11200.00  (updated after approval)
```

### **B. Create `budget_change_request` Table**

```sql
CREATE TABLE budget_change_request (
    change_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    quotation_id INT NOT NULL,
    
    -- Budget details
    original_budget DECIMAL(10,2) NOT NULL,
    current_budget DECIMAL(10,2) NOT NULL,
    requested_budget DECIMAL(10,2) NOT NULL,
    increase_amount DECIMAL(10,2) GENERATED ALWAYS AS (requested_budget - current_budget) STORED,
    
    -- Request details
    reason TEXT NOT NULL COMMENT 'Why budget increase is needed',
    evidence_files JSON NULL COMMENT 'Array of photo/video file paths',
    detailed_breakdown TEXT NULL COMMENT 'Cost breakdown of additional work',
    
    -- Status tracking
    status ENUM('pending', 'approved', 'declined', 'cancelled') NOT NULL DEFAULT 'pending',
    requested_by INT NOT NULL COMMENT 'Company user ID who submitted request',
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    -- Response tracking
    responded_by INT NULL COMMENT 'Customer user ID who approved/declined',
    responded_at TIMESTAMP NULL,
    customer_comments TEXT NULL COMMENT 'Customer feedback on request',
    
    -- Audit trail
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    
    FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE,
    FOREIGN KEY (quotation_id) REFERENCES companyquotation(quotation_id) ON DELETE CASCADE,
    FOREIGN KEY (requested_by) REFERENCES users(user_id),
    FOREIGN KEY (responded_by) REFERENCES users(user_id),
    
    INDEX idx_project_status (project_id, status),
    INDEX idx_requested_at (requested_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Example Record:**
```json
{
  "change_id": 15,
  "project_id": 203,
  "quotation_id": 42,
  "original_budget": 10000.00,
  "current_budget": 10000.00,
  "requested_budget": 11500.00,
  "increase_amount": 1500.00,
  "reason": "Discovered severe termite damage in wall studs requiring complete replacement. Original quote assumed studs were intact.",
  "evidence_files": [
    "/uploads/projects/203/evidence/termite_damage_1.jpg",
    "/uploads/projects/203/evidence/termite_damage_2.jpg",
    "/uploads/projects/203/evidence/wall_interior.jpg"
  ],
  "detailed_breakdown": "- Replace 12 wall studs: $600\n- Termite treatment: $400\n- Additional drywall: $300\n- Extra labor (2 days): $200",
  "status": "pending",
  "requested_by": 78,
  "requested_at": "2026-01-20 14:30:00"
}
```

---

## 🎨 User Interface Implementation

### **A. Quotation Form (Company Side)**

**Location:** `views/company/create-quotation.php`

```html
<!-- Budget Section -->
<div class="form-group">
    <label>Estimated Budget <span class="required">*</span></label>
    <input type="number" step="0.01" name="budget" id="budget" required>
</div>

<!-- NEW: Budget Type Selection -->
<div class="form-group">
    <label>Budget Type</label>
    <div class="radio-group">
        <label class="radio-inline">
            <input type="radio" name="budget_type" value="fixed" checked>
            Fixed Price (No changes allowed)
        </label>
        <label class="radio-inline">
            <input type="radio" name="budget_type" value="flexible">
            Flexible Budget (Changes allowed with approval)
        </label>
    </div>
    <small class="form-text">
        💡 Choose "Flexible" if unexpected issues might arise during work
    </small>
</div>

<!-- NEW: Variance Range (shows only if Flexible selected) -->
<div class="form-group" id="variance-group" style="display: none;">
    <label>Allowed Variance Range</label>
    <select name="variance_range" id="variance_range">
        <option value="">Select range...</option>
        <option value="5">±5% (Predictable projects)</option>
        <option value="10">±10% (Standard repairs)</option>
        <option value="15">±15% (Complex renovations)</option>
        <option value="custom">Custom percentage...</option>
    </select>
    
    <!-- Shows if custom selected -->
    <input type="number" min="1" max="50" id="custom_variance" 
           name="custom_variance" placeholder="Enter percentage" 
           style="display: none; margin-top: 10px;">
    
    <!-- Budget Range Preview -->
    <div class="budget-range-preview" id="range-preview" style="margin-top: 10px; display: none;">
        <small>
            💰 Budget Range: 
            <span id="min-budget">$0</span> - <span id="max-budget">$0</span>
        </small>
    </div>
</div>
```

**JavaScript Logic:**

```javascript
// Show/hide variance section based on budget type
document.querySelectorAll('input[name="budget_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const varianceGroup = document.getElementById('variance-group');
        if (this.value === 'flexible') {
            varianceGroup.style.display = 'block';
        } else {
            varianceGroup.style.display = 'none';
        }
    });
});

// Show custom input if custom variance selected
document.getElementById('variance_range').addEventListener('change', function() {
    const customInput = document.getElementById('custom_variance');
    if (this.value === 'custom') {
        customInput.style.display = 'block';
        customInput.required = true;
    } else {
        customInput.style.display = 'none';
        customInput.required = false;
        calculateBudgetRange();
    }
});

// Calculate and display budget range
function calculateBudgetRange() {
    const budget = parseFloat(document.getElementById('budget').value);
    const varianceSelect = document.getElementById('variance_range');
    const customVariance = document.getElementById('custom_variance');
    
    let variance = 0;
    
    if (varianceSelect.value === 'custom') {
        variance = parseFloat(customVariance.value) || 0;
    } else {
        variance = parseFloat(varianceSelect.value) || 0;
    }
    
    if (budget > 0 && variance > 0) {
        const minBudget = budget * (1 - variance / 100);
        const maxBudget = budget * (1 + variance / 100);
        
        document.getElementById('min-budget').textContent = '$' + minBudget.toFixed(2);
        document.getElementById('max-budget').textContent = '$' + maxBudget.toFixed(2);
        document.getElementById('range-preview').style.display = 'block';
    }
}

// Trigger calculation on budget or variance change
document.getElementById('budget').addEventListener('input', calculateBudgetRange);
document.getElementById('variance_range').addEventListener('change', calculateBudgetRange);
document.getElementById('custom_variance').addEventListener('input', calculateBudgetRange);
```

---

### **B. Budget Change Request Form (Company Side)**

**Location:** `views/company/projects.php` (inside project drawer)

```html
<!-- Budget Change Request Button (shows only for flexible budgets) -->
<div class="financial-actions" id="budget-actions">
    <button class="btn btn-warning" id="request-budget-change-btn">
        📝 Request Budget Change
    </button>
</div>

<!-- Budget Change Request Modal -->
<div class="modal" id="budget-change-modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Request Budget Change</h3>
            <span class="close">&times;</span>
        </div>
        
        <div class="modal-body">
            <form id="budget-change-form" enctype="multipart/form-data">
                <!-- Current Budget Info -->
                <div class="info-box">
                    <p><strong>Original Budget:</strong> $<span id="original-budget-display">10,000.00</span></p>
                    <p><strong>Current Budget:</strong> $<span id="current-budget-display">10,000.00</span></p>
                    <p><strong>Allowed Range:</strong> $<span id="allowed-min">9,000.00</span> - $<span id="allowed-max">11,000.00</span></p>
                </div>
                
                <!-- New Budget Amount -->
                <div class="form-group">
                    <label>Requested New Budget <span class="required">*</span></label>
                    <input type="number" step="0.01" name="requested_budget" 
                           id="requested-budget" required>
                    <div class="budget-diff" id="budget-diff-display"></div>
                </div>
                
                <!-- Reason for Change -->
                <div class="form-group">
                    <label>Reason for Budget Increase <span class="required">*</span></label>
                    <textarea name="reason" rows="5" required 
                              placeholder="Explain what unexpected issue was discovered and why additional budget is needed..."></textarea>
                </div>
                
                <!-- Detailed Cost Breakdown -->
                <div class="form-group">
                    <label>Cost Breakdown</label>
                    <textarea name="detailed_breakdown" rows="4"
                              placeholder="- Additional materials: $XXX&#10;- Extra labor hours: $XXX&#10;- Equipment rental: $XXX"></textarea>
                </div>
                
                <!-- Evidence Upload -->
                <div class="form-group">
                    <label>Upload Evidence (Photos/Videos) <span class="required">*</span></label>
                    <input type="file" name="evidence_files[]" multiple 
                           accept="image/*,video/*" id="evidence-upload">
                    <small>Upload photos or videos showing the issue that requires budget increase</small>
                    
                    <!-- Preview -->
                    <div class="file-preview" id="evidence-preview"></div>
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" id="cancel-change-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
```

**JavaScript for Change Request:**

```javascript
// Open budget change modal
document.getElementById('request-budget-change-btn').addEventListener('click', function() {
    const projectId = this.dataset.projectId;
    
    // Load current budget data
    fetch(`/api/projects.php?id=${projectId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('original-budget-display').textContent = data.original_budget;
            document.getElementById('current-budget-display').textContent = data.current_budget;
            document.getElementById('allowed-min').textContent = data.min_budget;
            document.getElementById('allowed-max').textContent = data.max_budget;
            
            document.getElementById('budget-change-modal').style.display = 'block';
        });
});

// Calculate budget difference
document.getElementById('requested-budget').addEventListener('input', function() {
    const current = parseFloat(document.getElementById('current-budget-display').textContent);
    const requested = parseFloat(this.value);
    const difference = requested - current;
    
    const diffDisplay = document.getElementById('budget-diff-display');
    if (difference > 0) {
        diffDisplay.innerHTML = `<span class="increase">+$${difference.toFixed(2)} increase</span>`;
        diffDisplay.style.color = '#ff6b6b';
    } else if (difference < 0) {
        diffDisplay.innerHTML = `<span class="decrease">-$${Math.abs(difference).toFixed(2)} decrease</span>`;
        diffDisplay.style.color = '#51cf66';
    } else {
        diffDisplay.innerHTML = '';
    }
});

// Preview uploaded files
document.getElementById('evidence-upload').addEventListener('change', function() {
    const preview = document.getElementById('evidence-preview');
    preview.innerHTML = '';
    
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'preview-item';
            
            if (file.type.startsWith('image/')) {
                div.innerHTML = `<img src="${e.target.result}" alt="${file.name}">`;
            } else if (file.type.startsWith('video/')) {
                div.innerHTML = `<video controls><source src="${e.target.result}"></video>`;
            }
            
            div.innerHTML += `<span>${file.name}</span>`;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// Submit budget change request
document.getElementById('budget-change-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('project_id', currentProjectId);
    formData.append('quotation_id', currentQuotationId);
    
    fetch('/api/budget-change-request.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Budget change request submitted successfully', 'success');
            document.getElementById('budget-change-modal').style.display = 'none';
            loadProjectDetails(currentProjectId); // Refresh project data
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    });
});
```

---

### **C. Budget Change Approval Interface (Customer Side)**

**Location:** `views/customer/dashboard.php` (Notifications section)

```html
<!-- Notification Card -->
<div class="notification-card budget-change-request">
    <div class="notification-icon">
        <i class="fas fa-exclamation-circle"></i>
    </div>
    
    <div class="notification-content">
        <h4>Budget Change Request</h4>
        <p><strong>Project:</strong> Bathroom Renovation</p>
        <p><strong>Company:</strong> ABC Construction Ltd.</p>
        <p class="budget-info">
            <span class="original">Current: $10,000.00</span>
            <span class="arrow">→</span>
            <span class="requested">Requested: $11,500.00</span>
            <span class="increase">(+$1,500.00)</span>
        </p>
        <p class="reason-preview">Reason: Discovered severe termite damage...</p>
    </div>
    
    <div class="notification-actions">
        <button class="btn btn-sm btn-primary" onclick="viewBudgetChangeRequest(15)">
            View Details
        </button>
    </div>
</div>

<!-- Budget Change Request Modal (Customer View) -->
<div class="modal" id="budget-change-detail-modal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3>Budget Change Request</h3>
            <span class="close">&times;</span>
        </div>
        
        <div class="modal-body">
            <!-- Budget Comparison -->
            <div class="budget-comparison">
                <div class="budget-item original">
                    <label>Current Budget</label>
                    <h2>$10,000.00</h2>
                </div>
                <div class="arrow">→</div>
                <div class="budget-item requested">
                    <label>Requested Budget</label>
                    <h2>$11,500.00</h2>
                </div>
                <div class="budget-item difference">
                    <label>Increase</label>
                    <h2 class="increase-amount">+$1,500.00</h2>
                    <small>(+15%)</small>
                </div>
            </div>
            
            <!-- Reason -->
            <div class="section">
                <h4>Reason for Change</h4>
                <p class="reason-text">
                    Discovered severe termite damage in wall studs requiring complete replacement. 
                    Original quote assumed studs were intact, but inspection revealed extensive damage 
                    that must be addressed before continuing with renovation.
                </p>
            </div>
            
            <!-- Cost Breakdown -->
            <div class="section">
                <h4>Cost Breakdown</h4>
                <ul class="cost-list">
                    <li>Replace 12 wall studs: <strong>$600</strong></li>
                    <li>Termite treatment: <strong>$400</strong></li>
                    <li>Additional drywall: <strong>$300</strong></li>
                    <li>Extra labor (2 days): <strong>$200</strong></li>
                </ul>
                <div class="total">Total Additional Cost: <strong>$1,500.00</strong></div>
            </div>
            
            <!-- Evidence Photos -->
            <div class="section">
                <h4>Evidence Photos</h4>
                <div class="evidence-gallery">
                    <img src="/uploads/projects/203/evidence/termite_damage_1.jpg" alt="Termite damage">
                    <img src="/uploads/projects/203/evidence/termite_damage_2.jpg" alt="Wall damage">
                    <img src="/uploads/projects/203/evidence/wall_interior.jpg" alt="Wall interior">
                </div>
            </div>
            
            <!-- Customer Feedback Section -->
            <div class="section">
                <h4>Your Response</h4>
                <textarea id="customer-comments" rows="3" 
                          placeholder="Add comments or questions (optional)..."></textarea>
            </div>
            
            <!-- Actions -->
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="openProjectChat()">
                    💬 Discuss via Chat
                </button>
                <button class="btn btn-danger" onclick="declineBudgetChange()">
                    ❌ Decline Request
                </button>
                <button class="btn btn-success" onclick="approveBudgetChange()">
                    ✅ Approve Change
                </button>
            </div>
        </div>
    </div>
</div>
```

**JavaScript for Approval:**

```javascript
function viewBudgetChangeRequest(changeId) {
    // Load full request details
    fetch(`/api/budget-change-request.php?id=${changeId}`)
        .then(res => res.json())
        .then(data => {
            // Populate modal with data
            // ... (populate fields as shown in HTML)
            
            document.getElementById('budget-change-detail-modal').style.display = 'block';
        });
}

function approveBudgetChange() {
    const changeId = currentChangeRequestId;
    const comments = document.getElementById('customer-comments').value;
    
    // Confirmation dialog
    if (!confirm('Are you sure you want to approve this budget change? The new budget will be $11,500.00')) {
        return;
    }
    
    fetch('/api/budget-change-request.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            change_id: changeId,
            action: 'approve',
            customer_comments: comments
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Budget change approved successfully', 'success');
            document.getElementById('budget-change-detail-modal').style.display = 'none';
            location.reload(); // Refresh page to update project budget
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    });
}

function declineBudgetChange() {
    const changeId = currentChangeRequestId;
    const comments = document.getElementById('customer-comments').value;
    
    if (!comments.trim()) {
        alert('Please provide a reason for declining this request');
        return;
    }
    
    fetch('/api/budget-change-request.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            change_id: changeId,
            action: 'decline',
            customer_comments: comments
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Budget change declined', 'info');
            document.getElementById('budget-change-detail-modal').style.display = 'none';
            location.reload();
        } else {
            showNotification('Error: ' + data.message, 'error');
        }
    });
}
```

---

## 🔌 Backend API Implementation

### **File:** `api/budget-change-request.php`

```php
<?php
require_once '../includes/session.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// POST: Submit new budget change request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $project_id = $_POST['project_id'] ?? null;
    $quotation_id = $_POST['quotation_id'] ?? null;
    $requested_budget = $_POST['requested_budget'] ?? null;
    $reason = $_POST['reason'] ?? null;
    $detailed_breakdown = $_POST['detailed_breakdown'] ?? null;
    
    // Validate inputs
    if (!$project_id || !$quotation_id || !$requested_budget || !$reason) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    // Verify user is authorized (company that owns the project)
    $check_sql = "SELECT p.company_id, q.budget, q.current_budget, q.budget_type, q.variance_range
                  FROM project p
                  JOIN companyquotation q ON p.quotation_id = q.quotation_id
                  WHERE p.project_id = ? AND p.company_id = ?";
    
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ii', $project_id, $_SESSION['company_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or project not found']);
        exit;
    }
    
    $project_data = $result->fetch_assoc();
    $original_budget = $project_data['budget'];
    $current_budget = $project_data['current_budget'] ?? $project_data['budget'];
    $budget_type = $project_data['budget_type'];
    
    // Check if budget type is flexible
    if ($budget_type === 'fixed') {
        echo json_encode(['success' => false, 'message' => 'This project has a fixed budget that cannot be changed']);
        exit;
    }
    
    // Handle file uploads
    $uploaded_files = [];
    if (!empty($_FILES['evidence_files']['name'][0])) {
        $upload_dir = '../uploads/projects/' . $project_id . '/evidence/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['evidence_files']['tmp_name'] as $key => $tmp_name) {
            $file_name = basename($_FILES['evidence_files']['name'][$key]);
            $file_path = $upload_dir . uniqid() . '_' . $file_name;
            
            if (move_uploaded_file($tmp_name, $file_path)) {
                $uploaded_files[] = $file_path;
            }
        }
    }
    
    // Insert budget change request
    $insert_sql = "INSERT INTO budget_change_request 
                   (project_id, quotation_id, original_budget, current_budget, 
                    requested_budget, reason, evidence_files, detailed_breakdown, 
                    requested_by, ip_address, user_agent)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_sql);
    $evidence_json = json_encode($uploaded_files);
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    $stmt->bind_param('iidddsssiss', 
        $project_id, 
        $quotation_id, 
        $original_budget, 
        $current_budget, 
        $requested_budget, 
        $reason, 
        $evidence_json, 
        $detailed_breakdown, 
        $_SESSION['user_id'],
        $ip,
        $user_agent
    );
    
    if ($stmt->execute()) {
        $change_id = $conn->insert_id;
        
        // Send notification to customer
        $customer_sql = "SELECT u.user_id, u.email, u.phone 
                        FROM project p 
                        JOIN users u ON p.user_id = u.user_id 
                        WHERE p.project_id = ?";
        $stmt2 = $conn->prepare($customer_sql);
        $stmt2->bind_param('i', $project_id);
        $stmt2->execute();
        $customer = $stmt2->get_result()->fetch_assoc();
        
        // Send email notification
        $to = $customer['email'];
        $subject = "Budget Change Request for Your Project";
        $message = "A budget change has been requested for your project.\n\n";
        $message .= "Requested Amount: $" . number_format($requested_budget, 2) . "\n";
        $message .= "Increase: $" . number_format($requested_budget - $current_budget, 2) . "\n\n";
        $message .= "Please log in to review and approve/decline this request.";
        
        mail($to, $subject, $message);
        
        // Send SMS notification (if SMS service configured)
        // sendSMS($customer['phone'], "Budget change request submitted for your project. Please review.");
        
        echo json_encode([
            'success' => true, 
            'message' => 'Budget change request submitted successfully',
            'change_id' => $change_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}

// GET: Retrieve budget change request details
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $change_id = $_GET['id'] ?? null;
    
    if (!$change_id) {
        echo json_encode(['success' => false, 'message' => 'Missing change ID']);
        exit;
    }
    
    $sql = "SELECT bcr.*, 
                   p.project_name, 
                   c.company_name,
                   u_req.full_name as requested_by_name,
                   u_res.full_name as responded_by_name
            FROM budget_change_request bcr
            JOIN project p ON bcr.project_id = p.project_id
            JOIN company c ON p.company_id = c.company_id
            LEFT JOIN users u_req ON bcr.requested_by = u_req.user_id
            LEFT JOIN users u_res ON bcr.responded_by = u_res.user_id
            WHERE bcr.change_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $change_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $row['evidence_files'] = json_decode($row['evidence_files'], true);
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
    }
}

// PUT: Approve or decline budget change request
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $change_id = $input['change_id'] ?? null;
    $action = $input['action'] ?? null; // 'approve' or 'decline'
    $customer_comments = $input['customer_comments'] ?? null;
    
    if (!$change_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    // Verify user is authorized (customer who owns the project)
    $check_sql = "SELECT bcr.project_id, bcr.quotation_id, bcr.requested_budget, p.user_id
                  FROM budget_change_request bcr
                  JOIN project p ON bcr.project_id = p.project_id
                  WHERE bcr.change_id = ?";
    
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('i', $change_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Request not found']);
        exit;
    }
    
    $request_data = $result->fetch_assoc();
    
    if ($request_data['user_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    // Update request status
    $status = ($action === 'approve') ? 'approved' : 'declined';
    
    $update_sql = "UPDATE budget_change_request 
                   SET status = ?, 
                       responded_by = ?, 
                       responded_at = NOW(), 
                       customer_comments = ?
                   WHERE change_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('sisi', $status, $_SESSION['user_id'], $customer_comments, $change_id);
    
    if ($stmt->execute()) {
        
        // If approved, update the quotation's current budget
        if ($action === 'approve') {
            $update_budget_sql = "UPDATE companyquotation 
                                 SET current_budget = ? 
                                 WHERE quotation_id = ?";
            $stmt2 = $conn->prepare($update_budget_sql);
            $stmt2->bind_param('di', $request_data['requested_budget'], $request_data['quotation_id']);
            $stmt2->execute();
        }
        
        // Notify company of decision
        // ... (send email/SMS to company)
        
        echo json_encode([
            'success' => true, 
            'message' => 'Budget change request ' . $status,
            'new_budget' => ($action === 'approve') ? $request_data['requested_budget'] : null
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
```

---

## 📊 Budget History Display

**Location:** `views/company/projects.php` (Financial Tab)

```html
<div class="budget-history-section">
    <h4>Budget History</h4>
    
    <div class="timeline">
        <!-- Initial Budget -->
        <div class="timeline-item">
            <div class="timeline-marker original"></div>
            <div class="timeline-content">
                <h5>Initial Budget</h5>
                <p class="amount">$10,000.00</p>
                <p class="date">January 10, 2026</p>
                <p class="note">Budget set in quotation</p>
            </div>
        </div>
        
        <!-- Budget Change Request 1 -->
        <div class="timeline-item">
            <div class="timeline-marker approved"></div>
            <div class="timeline-content">
                <h5>Budget Change Approved</h5>
                <p class="amount increase">$11,200.00 <span>(+$1,200)</span></p>
                <p class="date">January 18, 2026</p>
                <p class="note">Reason: Additional plumbing work required</p>
                <button class="btn-link" onclick="viewChangeDetails(15)">View Details</button>
            </div>
        </div>
        
        <!-- Budget Change Request 2 -->
        <div class="timeline-item">
            <div class="timeline-marker declined"></div>
            <div class="timeline-content">
                <h5>Budget Change Declined</h5>
                <p class="amount">$12,500.00 <span>(+$1,300)</span></p>
                <p class="date">January 22, 2026</p>
                <p class="note">Customer declined: Out of budget range</p>
                <button class="btn-link" onclick="viewChangeDetails(18)">View Details</button>
            </div>
        </div>
        
        <!-- Current Budget -->
        <div class="timeline-item current">
            <div class="timeline-marker current"></div>
            <div class="timeline-content">
                <h5>Current Budget</h5>
                <p class="amount current">$11,200.00</p>
                <p class="date">Active</p>
            </div>
        </div>
    </div>
</div>
```

---

## ✅ Testing Scenarios

### **Test Case 1: Fixed Budget (No Changes)**
```
1. Company creates quotation with budget_type = 'fixed'
2. Customer accepts quotation
3. Project starts
4. Company tries to click "Request Budget Change" button
   EXPECTED: Button is disabled/hidden with message "This project has a fixed budget"
```

### **Test Case 2: Flexible Budget - Change Within Range**
```
Budget: $10,000
Variance: ±10% ($9,000 - $11,000)
Requested: $10,500

EXPECTED:
- Notification sent to customer
- Customer can approve/decline
- If approved, budget updates to $10,500
- Timeline shows change
```

### **Test Case 3: Flexible Budget - Change Exceeds Range**
```
Budget: $10,000
Variance: ±10% ($9,000 - $11,000)
Requested: $12,000 (exceeds max by $1,000)

EXPECTED:
- Warning shown: "Requested amount exceeds allowed variance"
- Requires customer approval even though outside range
- Customer sees clear warning in approval dialog
```

### **Test Case 4: Multiple Budget Changes**
```
1. Original: $10,000
2. Change 1: $10,500 (approved)
3. Change 2: $11,200 (approved)
4. Change 3: $12,000 (declined)

EXPECTED:
- Timeline shows all 4 entries
- Current budget is $11,200
- Variance calculated from CURRENT budget, not original
```

### **Test Case 5: File Upload**
```
1. Company uploads 3 photos (2 JPG, 1 PNG)
2. Customer views request
   EXPECTED: All 3 images display in gallery
3. Customer clicks image
   EXPECTED: Full-size lightbox opens
```

### **Test Case 6: Declined Request**
```
1. Customer declines change
2. Company receives notification
3. Company clicks "View Declined Request"
   EXPECTED: Shows customer's reason for declining
4. Company can open chat to negotiate
```

---

## 🚨 Edge Cases & Validation

### **1. Budget Decrease**
```php
// In budget-change-request.php
if ($requested_budget < $current_budget) {
    // Allow decrease (cost saving for customer)
    // No approval needed - auto-approve
    $status = 'approved';
    // Update budget immediately
}
```

### **2. Multiple Pending Requests**
```php
// Check for existing pending requests
$check_pending = "SELECT COUNT(*) as pending_count 
                  FROM budget_change_request 
                  WHERE project_id = ? AND status = 'pending'";

if ($pending_count > 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'You already have a pending budget change request. Please wait for customer response.'
    ]);
    exit;
}
```

### **3. Project Status Check**
```php
// Only allow changes if project is active
$project_status_sql = "SELECT status FROM project WHERE project_id = ?";
// ... execute
if ($project_status !== 'in_progress') {
    echo json_encode([
        'success' => false, 
        'message' => 'Budget changes can only be requested for active projects'
    ]);
    exit;
}
```

### **4. Variance Calculation**
```javascript
// Variance should be calculated from CURRENT budget, not original
function calculateVarianceRange(currentBudget, variancePercent) {
    const minBudget = currentBudget * (1 - variancePercent / 100);
    const maxBudget = currentBudget * (1 + variancePercent / 100);
    return { min: minBudget, max: maxBudget };
}
```

---

## 📈 Success Metrics

After implementation, track:

1. **Adoption Rate:** % of companies choosing flexible budgets
2. **Change Request Frequency:** Average requests per project
3. **Approval Rate:** % of requests approved by customers
4. **Dispute Reduction:** Compare disputes before/after feature
5. **Average Change Amount:** How much budgets increase on average
6. **Time to Resolution:** How quickly requests are approved/declined

---

## 🎯 Implementation Checklist

- [ ] Database: Add columns to `companyquotation` table
- [ ] Database: Create `budget_change_request` table
- [ ] Frontend: Update quotation form with budget type selector
- [ ] Frontend: Create budget change request modal (company)
- [ ] Frontend: Create budget approval interface (customer)
- [ ] Frontend: Add budget history timeline
- [ ] Backend: Create `budget-change-request.php` API
- [ ] Backend: Add file upload handling
- [ ] Notifications: Email notification on new request
- [ ] Notifications: SMS notification (optional)
- [ ] Notifications: Email notification on approval/decline
- [ ] Testing: Test all 6 scenarios above
- [ ] Testing: Test file uploads
- [ ] Testing: Test edge cases
- [ ] Documentation: Update user guide
- [ ] Training: Create video tutorial for companies

---

## 📝 Summary

This dynamic budget management system solves the critical problem of budget inflexibility in construction projects. By allowing companies to request budget changes with proper documentation and customer approval, it:

✅ **Prevents project stalls** due to unexpected costs  
✅ **Builds trust** through transparent communication  
✅ **Protects customers** with approval requirements and variance limits  
✅ **Maintains flexibility** while controlling scope creep  
✅ **Creates audit trail** of all budget decisions

The two-budget-type approach (fixed vs flexible) gives both companies and customers control over their projects while accommodating the reality of construction work.

---

**Status:** 📝 Ready for Implementation  
**Priority:** 🔴 HIGH  
**Complexity:** 🟡 MEDIUM  
**Impact:** 🟢 HIGH
