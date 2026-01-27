# 💰 ISSUE #2: Payment Structure & Timeline Options

---

## 📋 Problem Statement

### **Current Limitation:**

The system currently has **two dropdowns** that don't align properly:

1. **Pricing Type Dropdown:** Includes `Fixed Price`, `Hourly Rate`, `Time & Material`, `Negotiable`
2. **Payment Terms Dropdown:** Only includes milestone-based options

**User's Exact Concern:**  
> "There is no option for that time and material thing. But at the top pricing section included them. How this handle in here?"

### **The Mismatch Problem:**

```
Company selects "Hourly Rate" pricing
   ↓
But payment terms only show:
   - Advance payment
   - Milestone-based payment
   - Payment on completion
   
Where are the options for:
   - Weekly invoicing?
   - Monthly billing?
   - Time & material calculations?
```

### **Real-World Scenario:**
```
Project: Office renovation (uncertain scope)
Company wants: Hourly rate billing ($50/hour)
Current system: Forces milestone-based payment
Problem: Can't submit weekly/monthly invoices
Result: Company avoids these project types
```

---

## ✅ Solution Design

### **Core Concept: Dynamic Payment Terms**

Payment terms dropdown **changes based on selected pricing type**:

```
┌─────────────────────────────────────────────────┐
│  Pricing Type Selection                         │
│  └─> Triggers payment terms update             │
│       └─> Shows only relevant options           │
└─────────────────────────────────────────────────┘
```

### **4 Pricing Models Explained**

---

## 💵 MODEL 1: Fixed Price

**Description:** Total project cost is fixed at quotation time. Customer knows exact final cost.

**When to Use:**
- Well-defined scope (painting, tiling, simple repairs)
- Predictable materials and labor
- Customer wants price certainty

**Payment Terms Options:**

| Option | Description | Timeline |
|--------|-------------|----------|
| **100% Advance** | Pay full amount before work starts | Day 1: 100% |
| **50/50 Split** | Half upfront, half on completion | Day 1: 50%<br>Final: 50% |
| **30/70 Split** | Small upfront, majority on completion | Day 1: 30%<br>Final: 70% |
| **Milestone-Based** | Payments tied to project phases | Phase 1: X%<br>Phase 2: Y%<br>Phase 3: Z% |
| **On Completion** | Pay everything when project finishes | Final: 100% |

**Visual Timeline:**

```
OPTION 1: 100% Advance
├─ Day 1: Pay $10,000 (100%)
└─ Work completes: $0

OPTION 2: 50/50 Split
├─ Day 1: Pay $5,000 (50%)
└─ Completion: Pay $5,000 (50%)

OPTION 3: 30/70 Split
├─ Day 1: Pay $3,000 (30%)
└─ Completion: Pay $7,000 (70%)

OPTION 4: Milestone-Based
├─ Milestone 1 (Foundation): Pay $3,000 (30%)
├─ Milestone 2 (Construction): Pay $5,000 (50%)
└─ Milestone 3 (Finishing): Pay $2,000 (20%)

OPTION 5: On Completion
└─ Final day: Pay $10,000 (100%)
```

**Database Structure:**

```sql
-- For fixed price projects
pricing_type = 'fixed_price'
payment_terms = 'advance_100' | 'split_50_50' | 'split_30_70' | 
                'milestone' | 'on_completion'
budget = 10000.00  -- Fixed amount
```

**Contract Creation Flow:**

```
1. Customer accepts quotation
2. System auto-creates contract with:
   - Total amount: $10,000
   - Payment schedule based on selected terms
3. For milestone-based:
   - Company adds milestone breakdown
   - Customer approves breakdown
4. Contract activates
5. Payments released per schedule
```

---

## ⏱️ MODEL 2: Hourly Rate

**Description:** Company charges per hour worked. Customer pays for actual time spent.

**When to Use:**
- Uncertain scope (troubleshooting, repairs)
- Ongoing maintenance contracts
- Consulting or advisory services

**Payment Terms Options:**

| Option | Description | Billing Cycle |
|--------|-------------|---------------|
| **Weekly Invoicing** | Invoice submitted every week | Every 7 days |
| **Bi-weekly Invoicing** | Invoice submitted every 2 weeks | Every 14 days |
| **Monthly Invoicing** | Invoice submitted monthly | Every 30 days |

**How It Works:**

```
Step 1: Set hourly rate in quotation ($50/hour)
Step 2: Track hours worked each day
Step 3: Submit invoice at end of period
Step 4: Customer reviews timesheet
Step 5: Customer approves & pays invoice
```

**Visual Timeline:**

```
WEEKLY INVOICING EXAMPLE:

Week 1: Jan 1-7
├─ Monday: 6 hours
├─ Tuesday: 8 hours
├─ Wednesday: 7 hours
├─ Thursday: 8 hours
├─ Friday: 5 hours
└─ Total: 34 hours × $50 = $1,700
    └─> Submit Invoice #001: $1,700

Week 2: Jan 8-14
├─ Monday: 7 hours
├─ Tuesday: 8 hours
├─ Wednesday: 6 hours
├─ Thursday: 8 hours
├─ Friday: 7 hours
└─ Total: 36 hours × $50 = $1,800
    └─> Submit Invoice #002: $1,800

TOTAL PROJECT COST: Sum of all invoices
```

**Database Structure:**

```sql
-- Quotation
pricing_type = 'hourly_rate'
payment_terms = 'weekly' | 'biweekly' | 'monthly'
hourly_rate = 50.00
estimated_hours = 200  -- Optional estimate

-- Invoice table
CREATE TABLE project_invoices (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,  -- INV-001, INV-002
    
    -- Period
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Hours & Cost
    hours_worked DECIMAL(5,2) NOT NULL,
    hourly_rate DECIMAL(10,2) NOT NULL,
    labor_cost DECIMAL(10,2) NOT NULL,  -- hours × rate
    
    -- Materials (if any)
    material_cost DECIMAL(10,2) DEFAULT 0,
    
    -- Total
    total_amount DECIMAL(10,2) NOT NULL,  -- labor + materials
    
    -- Status
    status ENUM('draft', 'submitted', 'approved', 'paid', 'disputed') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    
    -- Attachments
    timesheet_file VARCHAR(255) NULL,  -- Path to timesheet document
    
    FOREIGN KEY (project_id) REFERENCES project(project_id)
);

-- Daily timesheet entries
CREATE TABLE invoice_timesheets (
    timesheet_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    work_date DATE NOT NULL,
    hours_worked DECIMAL(4,2) NOT NULL,
    tasks_completed TEXT NOT NULL,
    evidence_photos JSON NULL,  -- Optional work progress photos
    
    FOREIGN KEY (invoice_id) REFERENCES project_invoices(invoice_id)
);
```

**Invoice Submission UI (Company Side):**

```html
<!-- Invoice Creation Form -->
<div class="invoice-form">
    <h3>Create New Invoice</h3>
    
    <!-- Period Selection -->
    <div class="form-group">
        <label>Billing Period</label>
        <input type="date" name="period_start" required>
        <span>to</span>
        <input type="date" name="period_end" required>
    </div>
    
    <!-- Timesheet Entries -->
    <div class="timesheet-section">
        <h4>Daily Time Entries</h4>
        
        <table class="timesheet-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Tasks Completed</th>
                    <th>Evidence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="timesheet-entries">
                <!-- Day 1 -->
                <tr>
                    <td><input type="date" name="work_date[]" value="2026-01-15"></td>
                    <td><input type="number" step="0.5" name="hours[]" value="8"></td>
                    <td>
                        <textarea name="tasks[]" rows="2">Installed new plumbing fixtures in bathroom</textarea>
                    </td>
                    <td>
                        <input type="file" name="evidence[]" accept="image/*" multiple>
                    </td>
                    <td><button type="button" class="btn-remove">×</button></td>
                </tr>
                
                <!-- Day 2 -->
                <tr>
                    <td><input type="date" name="work_date[]" value="2026-01-16"></td>
                    <td><input type="number" step="0.5" name="hours[]" value="7"></td>
                    <td>
                        <textarea name="tasks[]" rows="2">Connected pipes, tested water pressure</textarea>
                    </td>
                    <td>
                        <input type="file" name="evidence[]" accept="image/*" multiple>
                    </td>
                    <td><button type="button" class="btn-remove">×</button></td>
                </tr>
            </tbody>
        </table>
        
        <button type="button" id="add-day-btn" class="btn btn-secondary">
            + Add Another Day
        </button>
    </div>
    
    <!-- Cost Summary -->
    <div class="invoice-summary">
        <table>
            <tr>
                <td>Total Hours Worked:</td>
                <td><strong id="total-hours">15.0</strong> hours</td>
            </tr>
            <tr>
                <td>Hourly Rate:</td>
                <td>$<span id="hourly-rate">50.00</span>/hour</td>
            </tr>
            <tr>
                <td>Labor Cost:</td>
                <td>$<strong id="labor-cost">750.00</strong></td>
            </tr>
            <tr>
                <td>Materials (optional):</td>
                <td>$<input type="number" step="0.01" id="material-cost" value="0"></td>
            </tr>
            <tr class="total-row">
                <td>Total Invoice Amount:</td>
                <td><strong>$<span id="total-amount">750.00</span></strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Timesheet Document Upload -->
    <div class="form-group">
        <label>Upload Timesheet Document (PDF/Excel)</label>
        <input type="file" name="timesheet_document" accept=".pdf,.xlsx,.xls">
        <small>Optional: Upload detailed timesheet in PDF or Excel format</small>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn btn-secondary">Save as Draft</button>
        <button type="submit" class="btn btn-primary">Submit Invoice</button>
    </div>
</div>
```

**Invoice Approval UI (Customer Side):**

```html
<!-- Customer Invoice Review -->
<div class="invoice-review">
    <div class="invoice-header">
        <h3>Invoice #001</h3>
        <span class="status-badge pending">Pending Approval</span>
    </div>
    
    <div class="invoice-details">
        <p><strong>Project:</strong> Bathroom Renovation</p>
        <p><strong>Company:</strong> ABC Plumbing Services</p>
        <p><strong>Period:</strong> Jan 15 - Jan 21, 2026</p>
        <p><strong>Submitted:</strong> Jan 22, 2026 at 10:30 AM</p>
    </div>
    
    <!-- Daily Breakdown -->
    <div class="timesheet-review">
        <h4>Work Log</h4>
        
        <div class="day-entry">
            <div class="day-header">
                <strong>Monday, Jan 15</strong>
                <span class="hours">8.0 hours</span>
            </div>
            <p class="tasks">Installed new plumbing fixtures in bathroom</p>
            <div class="evidence-photos">
                <img src="/uploads/evidence/day1_1.jpg" alt="Work evidence">
                <img src="/uploads/evidence/day1_2.jpg" alt="Work evidence">
            </div>
        </div>
        
        <div class="day-entry">
            <div class="day-header">
                <strong>Tuesday, Jan 16</strong>
                <span class="hours">7.0 hours</span>
            </div>
            <p class="tasks">Connected pipes, tested water pressure</p>
            <div class="evidence-photos">
                <img src="/uploads/evidence/day2_1.jpg" alt="Work evidence">
            </div>
        </div>
        
        <!-- More days... -->
    </div>
    
    <!-- Cost Breakdown -->
    <div class="invoice-calculation">
        <table>
            <tr>
                <td>Total Hours:</td>
                <td>15.0 hours</td>
            </tr>
            <tr>
                <td>Hourly Rate:</td>
                <td>$50.00/hour</td>
            </tr>
            <tr>
                <td>Labor Cost:</td>
                <td>$750.00</td>
            </tr>
            <tr>
                <td>Materials:</td>
                <td>$0.00</td>
            </tr>
            <tr class="total">
                <td><strong>Total Due:</strong></td>
                <td><strong>$750.00</strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Customer Actions -->
    <div class="invoice-actions">
        <button class="btn btn-secondary" onclick="downloadInvoice()">
            📄 Download Invoice
        </button>
        <button class="btn btn-warning" onclick="disputeInvoice()">
            ⚠️ Dispute Invoice
        </button>
        <button class="btn btn-success" onclick="approveInvoice()">
            ✅ Approve & Pay
        </button>
    </div>
</div>
```

**Contract Creation Flow:**

```
1. Customer accepts hourly rate quotation
2. System creates contract with:
   - Pricing type: Hourly Rate
   - Rate: $50/hour
   - Payment schedule: Weekly invoicing
   - Estimated hours: 200 (optional)
   - Estimated budget: $10,000 (optional, for customer planning)
3. Contract activates, project starts
4. Each week:
   a. Company tracks daily hours
   b. Company submits invoice with timesheet
   c. Customer reviews invoice
   d. Customer approves & pays
5. Project completes when work is done (not milestone-based)
```

---

## 🔧 MODEL 3: Time & Material

**Description:** Company charges for **actual labor hours + actual materials used**. Most transparent model.

**When to Use:**
- Unpredictable scope (major renovations, restorations)
- Materials cost varies significantly
- Customer wants full cost breakdown

**Formula:**
```
Invoice Amount = (Hours Worked × Labor Rate) + (Material Cost + Markup)
```

**Payment Terms Options:**

| Option | Description | Billing Cycle |
|--------|-------------|---------------|
| **Weekly Invoicing** | Invoice submitted every week | Every 7 days |
| **Bi-weekly Invoicing** | Invoice every 2 weeks | Every 14 days |
| **Monthly Invoicing** | Invoice submitted monthly | Every 30 days |

**Example Calculation:**

```
Week 1 Invoice:

LABOR:
  40 hours × $60/hour = $2,400

MATERIALS:
  Material Cost: $1,500
  Markup (15%): $225
  Material Total: $1,725

TOTAL INVOICE: $2,400 + $1,725 = $4,125
```

**Visual Timeline:**

```
TIME & MATERIAL PROJECT EXAMPLE:

Week 1:
├─ Labor: 40 hours × $60 = $2,400
├─ Materials: $1,500 + 15% markup = $1,725
└─ Invoice #001: $4,125

Week 2:
├─ Labor: 35 hours × $60 = $2,100
├─ Materials: $800 + 15% markup = $920
└─ Invoice #002: $3,020

Week 3:
├─ Labor: 42 hours × $60 = $2,520
├─ Materials: $1,200 + 15% markup = $1,380
└─ Invoice #003: $3,900

TOTAL PROJECT COST: $4,125 + $3,020 + $3,900 = $11,045
```

**Database Structure:**

```sql
-- Quotation
pricing_type = 'time_material'
payment_terms = 'weekly' | 'biweekly' | 'monthly'
labor_rate = 60.00  -- Per hour labor charge
material_markup_percentage = 15  -- Markup on materials (10-20% typical)
estimated_hours = 150  -- Optional
estimated_material_cost = 5000  -- Optional

-- Invoice includes both labor and materials
-- (Use same project_invoices table, but populate both fields)
INSERT INTO project_invoices (
    labor_cost,      -- hours × labor_rate
    material_cost    -- actual materials + markup
)
```

**Invoice Submission UI (Company Side):**

```html
<!-- Time & Material Invoice -->
<div class="tm-invoice-form">
    <h3>Time & Material Invoice</h3>
    
    <!-- Labor Section -->
    <div class="section labor-section">
        <h4>Labor Hours</h4>
        
        <table class="timesheet-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Hours</th>
                    <th>Rate</th>
                    <th>Cost</th>
                    <th>Tasks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jan 15</td>
                    <td>8.0</td>
                    <td>$60.00</td>
                    <td>$480.00</td>
                    <td>Demolition work</td>
                </tr>
                <tr>
                    <td>Jan 16</td>
                    <td>7.5</td>
                    <td>$60.00</td>
                    <td>$450.00</td>
                    <td>Framing new walls</td>
                </tr>
                <!-- More rows -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total Labor</th>
                    <th colspan="2">$2,400.00</th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <!-- Materials Section -->
    <div class="section materials-section">
        <h4>Materials Used</h4>
        
        <table class="materials-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Cost</th>
                    <th>Subtotal</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody id="material-entries">
                <tr>
                    <td><input type="text" name="item[]" value="2x4 Lumber"></td>
                    <td><input type="number" name="qty[]" value="50"></td>
                    <td><input type="number" step="0.01" name="unit_cost[]" value="3.50"></td>
                    <td class="calculated">$175.00</td>
                    <td><input type="file" name="receipt[]" accept="image/*,.pdf"></td>
                </tr>
                <tr>
                    <td><input type="text" name="item[]" value="Drywall Sheets"></td>
                    <td><input type="number" name="qty[]" value="20"></td>
                    <td><input type="number" step="0.01" name="unit_cost[]" value="12.00"></td>
                    <td class="calculated">$240.00</td>
                    <td><input type="file" name="receipt[]" accept="image/*,.pdf"></td>
                </tr>
                <!-- More rows -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Material Subtotal</th>
                    <th colspan="2">$1,500.00</th>
                </tr>
                <tr>
                    <th colspan="3">Markup (15%)</th>
                    <th colspan="2">$225.00</th>
                </tr>
                <tr class="total">
                    <th colspan="3">Total Materials</th>
                    <th colspan="2">$1,725.00</th>
                </tr>
            </tfoot>
        </table>
        
        <button type="button" id="add-material-btn">+ Add Material Item</button>
    </div>
    
    <!-- Grand Total -->
    <div class="invoice-grand-total">
        <table>
            <tr>
                <td>Total Labor:</td>
                <td>$2,400.00</td>
            </tr>
            <tr>
                <td>Total Materials:</td>
                <td>$1,725.00</td>
            </tr>
            <tr class="grand-total">
                <td><strong>Invoice Total:</strong></td>
                <td><strong>$4,125.00</strong></td>
            </tr>
        </table>
    </div>
    
    <div class="form-actions">
        <button type="button" class="btn btn-secondary">Save as Draft</button>
        <button type="submit" class="btn btn-primary">Submit Invoice</button>
    </div>
</div>
```

**Customer Review Highlights:**

```html
<!-- Material Receipts Section -->
<div class="receipt-gallery">
    <h4>Material Purchase Receipts</h4>
    <div class="receipts">
        <div class="receipt-card">
            <img src="/uploads/receipts/lumber_receipt.jpg" alt="Lumber receipt">
            <p>2x4 Lumber - $175.00</p>
        </div>
        <div class="receipt-card">
            <img src="/uploads/receipts/drywall_receipt.jpg" alt="Drywall receipt">
            <p>Drywall Sheets - $240.00</p>
        </div>
    </div>
</div>

<!-- Markup Explanation -->
<div class="markup-info">
    <p>
        💡 <strong>Material Markup:</strong> A 15% markup is applied to materials to cover 
        procurement, transportation, storage, and handling costs.
    </p>
</div>
```

**Contract Creation Flow:**

```
1. Customer accepts time & material quotation
2. System creates contract with:
   - Pricing type: Time & Material
   - Labor rate: $60/hour
   - Material markup: 15%
   - Payment schedule: Weekly invoicing
   - Estimated budget: $15,000 (optional, for planning)
3. Contract activates, project starts
4. Each billing period:
   a. Company tracks hours + materials
   b. Company uploads material receipts
   c. Company submits invoice with full breakdown
   d. Customer reviews labor log + material receipts
   e. Customer approves & pays
5. Project cost = sum of all invoices (actual cost)
```

---

## 🤝 MODEL 4: Negotiable

**Description:** Custom terms discussed between customer and company. No standard options.

**When to Use:**
- Unique project requirements
- Long-term contracts with special terms
- Complex payment structures
- Trade/barter arrangements
- Phased projects with variable pricing

**Payment Terms Options:**

| Option | Description |
|--------|-------------|
| **To Be Discussed** | Open negotiation via in-app chat |
| **Custom Terms** | Mutually agreed written terms |

**How It Works:**

```
Step 1: Company selects "Negotiable" pricing type
Step 2: System opens in-app chat between company and customer
Step 3: Both parties discuss:
   - Project scope
   - Pricing structure
   - Payment schedule
   - Special conditions
Step 4: Company submits custom quotation with agreed terms
Step 5: Customer reviews and accepts
Step 6: Contract created with custom terms
```

**Visual Flow:**

```
NEGOTIABLE PRICING FLOW:

Company: "This project needs custom pricing"
   ↓
Selects: Pricing Type = Negotiable
   ↓
System: Opens chat + marks quotation as "In Negotiation"
   ↓
Chat Discussion:
   Company: "I can do $8,000 with 40/30/30 payment split"
   Customer: "What about $7,500 with 50/50?"
   Company: "I can do $7,800 with 50% upfront, 25% mid, 25% final"
   Customer: "Agreed!"
   ↓
Company: Updates quotation with final agreed terms
   ↓
Customer: Reviews and accepts
   ↓
Contract: Created with custom terms
```

**Database Structure:**

```sql
-- Quotation
pricing_type = 'negotiable'
payment_terms = 'custom'
budget = NULL  -- TBD during negotiation
custom_terms TEXT  -- Free text field for agreed terms

-- Example custom_terms content:
/*
Payment Structure:
- 50% ($3,900) upfront before work begins
- 25% ($1,950) upon completion of foundation work
- 25% ($1,950) upon final completion

Special Terms:
- Materials purchased by customer directly
- Work to be completed within 6 weeks
- Weekend work available at +20% hourly rate
- Customer provides lunch for workers
*/
```

**Quotation Form UI:**

```html
<!-- Negotiable Pricing Section -->
<div class="negotiable-section" id="negotiable-section" style="display: none;">
    <div class="info-box warning">
        <h4>📝 Negotiable Pricing Selected</h4>
        <p>
            You've selected custom pricing. Please use the chat system to discuss 
            terms with the customer. Once agreed, enter the final terms below.
        </p>
        <button class="btn btn-primary" onclick="openCustomerChat()">
            💬 Open Chat with Customer
        </button>
    </div>
    
    <div class="form-group">
        <label>Final Agreed Budget (after negotiation)</label>
        <input type="number" step="0.01" name="negotiated_budget" 
               placeholder="Enter amount agreed in chat">
    </div>
    
    <div class="form-group">
        <label>Custom Payment Terms</label>
        <textarea name="custom_terms" rows="8" 
                  placeholder="Enter the payment structure and special terms agreed with customer...

Example:
- 50% ($5,000) upfront
- 25% ($2,500) upon milestone 1 completion
- 25% ($2,500) upon final completion

Special conditions:
- Materials provided by customer
- Work schedule: Mon-Fri only
- etc."></textarea>
        <small>Be specific about payment amounts, dates, and any special conditions</small>
    </div>
    
    <div class="negotiation-status">
        <p><strong>Negotiation Status:</strong> <span class="status-badge">In Progress</span></p>
        <p><strong>Messages Exchanged:</strong> 12</p>
        <p><strong>Last Message:</strong> 2 hours ago</p>
    </div>
</div>
```

**Customer Side (Viewing Negotiable Quote):**

```html
<div class="negotiable-quote-view">
    <div class="alert alert-info">
        <h4>Custom Pricing Discussion</h4>
        <p>
            This company has proposed custom pricing for your project. 
            Please review their initial proposal and discuss details via chat.
        </p>
    </div>
    
    <div class="proposed-terms">
        <h4>Company's Initial Proposal</h4>
        <div class="terms-content">
            <p><strong>Estimated Budget:</strong> $8,000 - $10,000</p>
            <p><strong>Proposed Payment Structure:</strong></p>
            <ul>
                <li>40% upfront ($3,200 - $4,000)</li>
                <li>30% at midpoint ($2,400 - $3,000)</li>
                <li>30% upon completion ($2,400 - $3,000)</li>
            </ul>
            <p><strong>Special Conditions:</strong></p>
            <ul>
                <li>Project timeline: 8-10 weeks</li>
                <li>Materials sourced by company (included in price)</li>
                <li>Weekend work available at premium rate</li>
            </ul>
        </div>
    </div>
    
    <div class="negotiation-actions">
        <button class="btn btn-primary" onclick="openNegotiationChat()">
            💬 Discuss Terms
        </button>
        <button class="btn btn-secondary">
            ❌ Decline & Request New Quote
        </button>
    </div>
</div>
```

**Contract Creation Flow:**

```
1. Customer accepts negotiable quotation (after chat agreement)
2. System creates contract with:
   - Pricing type: Negotiable
   - Payment terms: Custom
   - Budget: As agreed
   - Custom terms text: Full copy of agreed terms
3. Both parties digitally sign contract
4. Contract activates
5. Payments follow custom schedule defined in terms
```

---

## 🔄 Dynamic Dropdown Implementation

### **JavaScript Logic:**

```javascript
// Get dropdown elements
const pricingTypeSelect = document.getElementById('pricing_type');
const paymentTermsSelect = document.getElementById('payment_terms');
const paymentTermsGroup = document.getElementById('payment-terms-group');

// Define payment options for each pricing type
const paymentOptions = {
    'fixed_price': [
        { value: 'advance_100', text: '100% Advance Payment' },
        { value: 'split_50_50', text: '50/50 Split (50% upfront, 50% completion)' },
        { value: 'split_30_70', text: '30/70 Split (30% upfront, 70% completion)' },
        { value: 'milestone', text: 'Milestone-Based Payments' },
        { value: 'on_completion', text: 'Payment on Completion (100% final)' }
    ],
    'hourly_rate': [
        { value: 'weekly', text: 'Weekly Invoicing' },
        { value: 'biweekly', text: 'Bi-weekly Invoicing (Every 2 weeks)' },
        { value: 'monthly', text: 'Monthly Invoicing' }
    ],
    'time_material': [
        { value: 'weekly', text: 'Weekly Invoicing (Labor + Materials)' },
        { value: 'biweekly', text: 'Bi-weekly Invoicing (Labor + Materials)' },
        { value: 'monthly', text: 'Monthly Invoicing (Labor + Materials)' }
    ],
    'negotiable': [
        { value: 'custom', text: 'Custom Terms (To Be Discussed)' }
    ]
};

// Update payment terms when pricing type changes
pricingTypeSelect.addEventListener('change', function() {
    const selectedType = this.value;
    
    // Clear existing options
    paymentTermsSelect.innerHTML = '<option value="">Select payment terms...</option>';
    
    // Get options for selected pricing type
    const options = paymentOptions[selectedType] || [];
    
    // Populate dropdown
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.value;
        optionElement.textContent = option.text;
        paymentTermsSelect.appendChild(optionElement);
    });
    
    // Show/hide additional fields based on pricing type
    updateAdditionalFields(selectedType);
    
    // Show payment terms dropdown (was hidden initially)
    paymentTermsGroup.style.display = 'block';
});

// Show/hide additional input fields
function updateAdditionalFields(pricingType) {
    // Hide all additional sections first
    document.getElementById('hourly-rate-section').style.display = 'none';
    document.getElementById('material-markup-section').style.display = 'none';
    document.getElementById('negotiable-section').style.display = 'none';
    
    // Show relevant sections
    switch(pricingType) {
        case 'hourly_rate':
            document.getElementById('hourly-rate-section').style.display = 'block';
            break;
            
        case 'time_material':
            document.getElementById('hourly-rate-section').style.display = 'block';
            document.getElementById('material-markup-section').style.display = 'block';
            break;
            
        case 'negotiable':
            document.getElementById('negotiable-section').style.display = 'block';
            break;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // If editing existing quotation, trigger change event
    if (pricingTypeSelect.value) {
        pricingTypeSelect.dispatchEvent(new Event('change'));
    }
});
```

### **HTML Structure:**

```html
<form id="quotation-form">
    <!-- Pricing Type Selection -->
    <div class="form-group">
        <label>Pricing Type <span class="required">*</span></label>
        <select name="pricing_type" id="pricing_type" required>
            <option value="">Select pricing model...</option>
            <option value="fixed_price">Fixed Price (Set amount for entire project)</option>
            <option value="hourly_rate">Hourly Rate (Pay per hour worked)</option>
            <option value="time_material">Time & Material (Labor + Materials)</option>
            <option value="negotiable">Negotiable (Custom terms)</option>
        </select>
        <small class="form-text">
            💡 Choose the pricing model that best fits this project
        </small>
    </div>
    
    <!-- Payment Terms (dynamic) -->
    <div class="form-group" id="payment-terms-group" style="display: none;">
        <label>Payment Terms <span class="required">*</span></label>
        <select name="payment_terms" id="payment_terms" required>
            <option value="">Select payment terms...</option>
            <!-- Options populated dynamically by JavaScript -->
        </select>
    </div>
    
    <!-- Fixed Price: Budget Input -->
    <div class="form-group" id="budget-section">
        <label>Project Budget <span class="required">*</span></label>
        <input type="number" step="0.01" name="budget" id="budget" required>
    </div>
    
    <!-- Hourly Rate: Additional Fields -->
    <div id="hourly-rate-section" style="display: none;">
        <div class="form-group">
            <label>Hourly Rate <span class="required">*</span></label>
            <input type="number" step="0.01" name="hourly_rate" placeholder="e.g., 50.00">
            <small>Amount charged per hour of work</small>
        </div>
        
        <div class="form-group">
            <label>Estimated Hours (Optional)</label>
            <input type="number" name="estimated_hours" placeholder="e.g., 200">
            <small>Rough estimate for customer planning (not binding)</small>
        </div>
    </div>
    
    <!-- Time & Material: Additional Fields -->
    <div id="material-markup-section" style="display: none;">
        <div class="form-group">
            <label>Material Markup Percentage <span class="required">*</span></label>
            <select name="material_markup_percentage">
                <option value="10">10% (Standard)</option>
                <option value="15" selected>15% (Recommended)</option>
                <option value="20">20% (Premium)</option>
                <option value="custom">Custom...</option>
            </select>
            <small>Markup applied to material costs to cover procurement and handling</small>
        </div>
        
        <div class="form-group">
            <label>Estimated Material Cost (Optional)</label>
            <input type="number" step="0.01" name="estimated_material_cost" placeholder="e.g., 5000.00">
            <small>Rough estimate of material costs (not binding)</small>
        </div>
    </div>
    
    <!-- Negotiable: Custom Section -->
    <div id="negotiable-section" style="display: none;">
        <!-- (HTML shown in Model 4 section above) -->
    </div>
    
    <!-- Submit Button -->
    <div class="form-actions">
        <button type="button" class="btn btn-secondary">Save as Draft</button>
        <button type="submit" class="btn btn-primary">Submit Quotation</button>
    </div>
</form>
```

---

## 🗄️ Complete Database Schema

### **Update `companyquotation` Table:**

```sql
ALTER TABLE companyquotation
-- Pricing model
ADD COLUMN pricing_type ENUM('fixed_price', 'hourly_rate', 'time_material', 'negotiable') 
    NOT NULL DEFAULT 'fixed_price'
    COMMENT 'Type of pricing structure for this quotation',

-- Payment schedule
ADD COLUMN payment_terms ENUM(
    -- Fixed price options
    'advance_100', 'split_50_50', 'split_30_70', 'milestone', 'on_completion',
    -- Hourly/Time & Material options
    'weekly', 'biweekly', 'monthly',
    -- Negotiable option
    'custom'
) NULL COMMENT 'Payment schedule/terms',

-- Hourly rate fields
ADD COLUMN hourly_rate DECIMAL(10,2) NULL 
    COMMENT 'Hourly labor rate (for hourly_rate and time_material types)',

ADD COLUMN estimated_hours INT NULL 
    COMMENT 'Estimated total hours (optional, for planning)',

-- Time & Material fields
ADD COLUMN labor_rate DECIMAL(10,2) NULL 
    COMMENT 'Labor rate per hour for time & material projects',

ADD COLUMN material_markup_percentage INT NULL DEFAULT 15
    COMMENT 'Markup percentage applied to materials (10-20% typical)',

ADD COLUMN estimated_material_cost DECIMAL(10,2) NULL
    COMMENT 'Estimated material costs (optional, for planning)',

-- Negotiable fields
ADD COLUMN custom_terms TEXT NULL
    COMMENT 'Custom payment terms agreed through negotiation',

-- Estimated budget (for all types)
ADD COLUMN estimated_budget DECIMAL(10,2) NULL
    COMMENT 'Estimated total project cost (may differ from final cost for time-based)';
```

### **Create `project_invoices` Table:**

```sql
CREATE TABLE project_invoices (
    invoice_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    
    -- Invoice identification
    invoice_number VARCHAR(50) NOT NULL UNIQUE,  -- INV-001, INV-002, etc.
    
    -- Billing period
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    billing_cycle ENUM('weekly', 'biweekly', 'monthly') NOT NULL,
    
    -- Labor costs
    hours_worked DECIMAL(6,2) NOT NULL DEFAULT 0,
    hourly_rate DECIMAL(10,2) NOT NULL,
    labor_cost DECIMAL(10,2) GENERATED ALWAYS AS (hours_worked * hourly_rate) STORED,
    
    -- Material costs (for time & material projects)
    material_subtotal DECIMAL(10,2) DEFAULT 0 
        COMMENT 'Raw material cost before markup',
    material_markup_percentage INT DEFAULT 0,
    material_markup_amount DECIMAL(10,2) GENERATED ALWAYS AS 
        (material_subtotal * material_markup_percentage / 100) STORED,
    material_cost DECIMAL(10,2) GENERATED ALWAYS AS 
        (material_subtotal + (material_subtotal * material_markup_percentage / 100)) STORED,
    
    -- Total
    total_amount DECIMAL(10,2) GENERATED ALWAYS AS 
        (labor_cost + material_cost) STORED,
    
    -- Status workflow
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'paid', 'disputed', 'rejected') 
        NOT NULL DEFAULT 'draft',
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    submitted_at TIMESTAMP NULL,
    reviewed_at TIMESTAMP NULL,
    approved_at TIMESTAMP NULL,
    paid_at TIMESTAMP NULL,
    
    -- Attachments
    timesheet_file VARCHAR(255) NULL 
        COMMENT 'Path to uploaded timesheet document',
    
    -- Notes
    company_notes TEXT NULL 
        COMMENT 'Internal notes from company',
    customer_feedback TEXT NULL 
        COMMENT 'Customer comments on invoice',
    
    -- Relationships
    FOREIGN KEY (project_id) REFERENCES project(project_id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_project_status (project_id, status),
    INDEX idx_submitted_date (submitted_at),
    INDEX idx_invoice_number (invoice_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Create `invoice_timesheets` Table:**

```sql
CREATE TABLE invoice_timesheets (
    timesheet_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    
    -- Daily entry
    work_date DATE NOT NULL,
    hours_worked DECIMAL(4,2) NOT NULL,
    
    -- Work description
    tasks_completed TEXT NOT NULL 
        COMMENT 'Description of work done on this day',
    
    -- Evidence
    evidence_photos JSON NULL 
        COMMENT 'Array of photo file paths showing work progress',
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Relationships
    FOREIGN KEY (invoice_id) REFERENCES project_invoices(invoice_id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_invoice_date (invoice_id, work_date),
    
    -- Ensure one entry per day per invoice
    UNIQUE KEY unique_invoice_date (invoice_id, work_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Create `invoice_materials` Table:**

```sql
CREATE TABLE invoice_materials (
    material_id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    
    -- Material details
    item_name VARCHAR(200) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_of_measure VARCHAR(50) NULL 
        COMMENT 'e.g., pieces, meters, kilograms',
    unit_cost DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
    
    -- Receipt proof
    receipt_file VARCHAR(255) NULL 
        COMMENT 'Path to receipt/invoice image',
    
    -- Supplier info (optional)
    supplier_name VARCHAR(200) NULL,
    purchase_date DATE NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Relationships
    FOREIGN KEY (invoice_id) REFERENCES project_invoices(invoice_id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_invoice (invoice_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔌 Backend API Implementation

### **File:** `api/invoices.php`

```php
<?php
require_once '../includes/session.php';
require_once '../config/database.php';

header('Content-Type: application/json');

// POST: Create new invoice
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $project_id = $_POST['project_id'] ?? null;
    $period_start = $_POST['period_start'] ?? null;
    $period_end = $_POST['period_end'] ?? null;
    $billing_cycle = $_POST['billing_cycle'] ?? null;
    
    // Validate
    if (!$project_id || !$period_start || !$period_end) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    // Verify company owns project
    $check_sql = "SELECT company_id, pricing_type FROM project p
                  JOIN companyquotation q ON p.quotation_id = q.quotation_id
                  WHERE p.project_id = ? AND p.company_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param('ii', $project_id, $_SESSION['company_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $project_data = $result->fetch_assoc();
    $pricing_type = $project_data['pricing_type'];
    
    // Verify pricing type allows invoicing
    if (!in_array($pricing_type, ['hourly_rate', 'time_material'])) {
        echo json_encode(['success' => false, 'message' => 'This project type does not use invoicing']);
        exit;
    }
    
    // Generate invoice number
    $invoice_count_sql = "SELECT COUNT(*) as count FROM project_invoices WHERE project_id = ?";
    $stmt2 = $conn->prepare($invoice_count_sql);
    $stmt2->bind_param('i', $project_id);
    $stmt2->execute();
    $count = $stmt2->get_result()->fetch_assoc()['count'];
    $invoice_number = sprintf('INV-%04d', $count + 1);
    
    // Get hourly rate from quotation
    $rate_sql = "SELECT hourly_rate, material_markup_percentage 
                 FROM companyquotation q
                 JOIN project p ON q.quotation_id = p.quotation_id
                 WHERE p.project_id = ?";
    $stmt3 = $conn->prepare($rate_sql);
    $stmt3->bind_param('i', $project_id);
    $stmt3->execute();
    $rate_data = $stmt3->get_result()->fetch_assoc();
    $hourly_rate = $rate_data['hourly_rate'];
    $material_markup = $rate_data['material_markup_percentage'] ?? 0;
    
    // Calculate totals from submitted timesheet entries
    $total_hours = 0;
    if (isset($_POST['work_date']) && is_array($_POST['work_date'])) {
        foreach ($_POST['hours'] as $hours) {
            $total_hours += floatval($hours);
        }
    }
    
    // Calculate material total (if time & material)
    $material_subtotal = 0;
    if ($pricing_type === 'time_material' && isset($_POST['material_items'])) {
        $material_items = json_decode($_POST['material_items'], true);
        foreach ($material_items as $item) {
            $material_subtotal += floatval($item['quantity']) * floatval($item['unit_cost']);
        }
    }
    
    // Handle timesheet document upload
    $timesheet_file = null;
    if (!empty($_FILES['timesheet_document']['name'])) {
        $upload_dir = '../uploads/projects/' . $project_id . '/invoices/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = $invoice_number . '_' . basename($_FILES['timesheet_document']['name']);
        $timesheet_file = $upload_dir . $file_name;
        move_uploaded_file($_FILES['timesheet_document']['tmp_name'], $timesheet_file);
    }
    
    // Insert invoice
    $insert_sql = "INSERT INTO project_invoices 
                   (project_id, invoice_number, period_start, period_end, 
                    billing_cycle, hours_worked, hourly_rate, 
                    material_subtotal, material_markup_percentage, 
                    timesheet_file, company_notes, status)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft')";
    
    $stmt4 = $conn->prepare($insert_sql);
    $company_notes = $_POST['company_notes'] ?? null;
    
    $stmt4->bind_param('issssddidss',
        $project_id,
        $invoice_number,
        $period_start,
        $period_end,
        $billing_cycle,
        $total_hours,
        $hourly_rate,
        $material_subtotal,
        $material_markup,
        $timesheet_file,
        $company_notes
    );
    
    if ($stmt4->execute()) {
        $invoice_id = $conn->insert_id;
        
        // Insert timesheet entries
        if (isset($_POST['work_date']) && is_array($_POST['work_date'])) {
            $timesheet_sql = "INSERT INTO invoice_timesheets 
                             (invoice_id, work_date, hours_worked, tasks_completed, evidence_photos)
                             VALUES (?, ?, ?, ?, ?)";
            $stmt5 = $conn->prepare($timesheet_sql);
            
            foreach ($_POST['work_date'] as $index => $work_date) {
                $hours = $_POST['hours'][$index];
                $tasks = $_POST['tasks'][$index];
                $evidence = null; // Handle file uploads separately
                
                $stmt5->bind_param('isdss', $invoice_id, $work_date, $hours, $tasks, $evidence);
                $stmt5->execute();
            }
        }
        
        // Insert material items (if time & material)
        if ($pricing_type === 'time_material' && isset($_POST['material_items'])) {
            $material_sql = "INSERT INTO invoice_materials
                            (invoice_id, item_name, quantity, unit_of_measure, unit_cost, receipt_file)
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt6 = $conn->prepare($material_sql);
            
            $material_items = json_decode($_POST['material_items'], true);
            foreach ($material_items as $item) {
                $stmt6->bind_param('isdsds',
                    $invoice_id,
                    $item['item_name'],
                    $item['quantity'],
                    $item['unit_of_measure'],
                    $item['unit_cost'],
                    $item['receipt_file']
                );
                $stmt6->execute();
            }
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice created successfully',
            'invoice_id' => $invoice_id,
            'invoice_number' => $invoice_number
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}

// GET: Retrieve invoice details
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $invoice_id = $_GET['id'] ?? null;
    
    if (!$invoice_id) {
        echo json_encode(['success' => false, 'message' => 'Missing invoice ID']);
        exit;
    }
    
    // Get invoice with project and company details
    $sql = "SELECT i.*, 
                   p.project_name, 
                   c.company_name,
                   q.pricing_type
            FROM project_invoices i
            JOIN project p ON i.project_id = p.project_id
            JOIN company c ON p.company_id = c.company_id
            JOIN companyquotation q ON p.quotation_id = q.quotation_id
            WHERE i.invoice_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        
        // Get timesheet entries
        $timesheet_sql = "SELECT * FROM invoice_timesheets 
                         WHERE invoice_id = ? 
                         ORDER BY work_date";
        $stmt2 = $conn->prepare($timesheet_sql);
        $stmt2->bind_param('i', $invoice_id);
        $stmt2->execute();
        $row['timesheet_entries'] = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get material items (if applicable)
        $material_sql = "SELECT * FROM invoice_materials WHERE invoice_id = ?";
        $stmt3 = $conn->prepare($material_sql);
        $stmt3->bind_param('i', $invoice_id);
        $stmt3->execute();
        $row['material_items'] = $stmt3->get_result()->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invoice not found']);
    }
}

// PUT: Update invoice status (approve/reject)
elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $invoice_id = $input['invoice_id'] ?? null;
    $action = $input['action'] ?? null; // 'submit', 'approve', 'reject', 'dispute'
    $feedback = $input['customer_feedback'] ?? null;
    
    if (!$invoice_id || !$action) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }
    
    $status_map = [
        'submit' => 'submitted',
        'approve' => 'approved',
        'reject' => 'rejected',
        'dispute' => 'disputed',
        'pay' => 'paid'
    ];
    
    $new_status = $status_map[$action] ?? null;
    if (!$new_status) {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
    
    // Update status
    $update_sql = "UPDATE project_invoices 
                   SET status = ?, 
                       customer_feedback = ?,
                       " . ($action === 'submit' ? "submitted_at = NOW()" : "") . "
                       " . ($action === 'approve' ? "approved_at = NOW()" : "") . "
                       " . ($action === 'pay' ? "paid_at = NOW()" : "") . "
                   WHERE invoice_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssi', $new_status, $feedback, $invoice_id);
    
    if ($stmt->execute()) {
        // Send notifications...
        
        echo json_encode([
            'success' => true,
            'message' => 'Invoice ' . $action . 'ed successfully',
            'new_status' => $new_status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
```

---

## ✅ Testing Scenarios

### **Test Case 1: Fixed Price - Milestone Payment**
```
1. Company creates quotation:
   - Pricing Type: Fixed Price
   - Payment Terms: Milestone-Based
   - Budget: $10,000
2. Customer accepts quotation
3. Contract created with 3 milestones:
   - Milestone 1: $3,000
   - Milestone 2: $5,000
   - Milestone 3: $2,000
4. Verify payment schedule shows correctly
```

### **Test Case 2: Hourly Rate - Weekly Invoicing**
```
1. Company creates quotation:
   - Pricing Type: Hourly Rate
   - Payment Terms: Weekly Invoicing
   - Hourly Rate: $50
2. Customer accepts
3. Week 1: Company works 40 hours
4. Company submits invoice #001 for $2,000
5. Customer reviews timesheet, approves
6. Payment processed
7. Repeat for subsequent weeks
```

### **Test Case 3: Time & Material - Monthly Invoicing**
```
1. Company creates quotation:
   - Pricing Type: Time & Material
   - Payment Terms: Monthly Invoicing
   - Labor Rate: $60/hour
   - Material Markup: 15%
2. Month 1 invoice:
   - Labor: 120 hours × $60 = $7,200
   - Materials: $3,000 + 15% = $3,450
   - Total: $10,650
3. Customer views material receipts
4. Customer approves invoice
```

### **Test Case 4: Negotiable - Custom Terms**
```
1. Company selects "Negotiable" pricing
2. Chat opens automatically
3. Company: "I propose $8,000 with 40/60 split"
4. Customer: "How about $7,500 50/50?"
5. Agreement reached at $7,800 with 50/50
6. Company enters custom terms in quotation
7. Customer accepts
8. Contract created with custom schedule
```

### **Test Case 5: Pricing Type Change**
```
1. Company starts creating quotation
2. Selects "Fixed Price"
   - Payment dropdown shows: Advance, Milestone, etc.
3. Changes to "Hourly Rate"
   - Payment dropdown updates to: Weekly, Biweekly, Monthly
4. Changes to "Time & Material"
   - Additional fields appear: Labor Rate, Material Markup
5. Verify dropdowns always match pricing type
```

---

## 🎯 Success Metrics

1. **Adoption Rate:** % of each pricing type used
2. **Invoice Approval Time:** Average days to approve invoice
3. **Dispute Rate:** % of invoices disputed
4. **Customer Satisfaction:** Rating on payment process
5. **Company Preference:** Which pricing type companies prefer

---

## 📝 Summary

This comprehensive payment system solves the mismatch between pricing types and payment terms by:

✅ **4 distinct pricing models** covering all project types  
✅ **Dynamic dropdowns** that show only relevant options  
✅ **Transparent invoicing** with timesheet and receipt tracking  
✅ **Flexible negotiation** for custom requirements  
✅ **Clear workflow** for each payment model  

Each pricing type has its own contract creation flow, ensuring the system handles everything from simple fixed-price jobs to complex time-and-material projects.

---

**Status:** 📝 Ready for Implementation  
**Priority:** 🔴 HIGH  
**Complexity:** 🟡 MEDIUM-HIGH  
**Impact:** 🟢 HIGH
