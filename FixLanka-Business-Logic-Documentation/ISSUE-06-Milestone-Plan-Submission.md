# 🏗️ **ISSUE #6: Milestone Plan Submission Process (UPDATED)**

---

## 📋 **Problem Statement**

### **Your Concern:**
"After quotation is accepted, contract is auto-created with basic info. But how does company add the detailed milestone plan? What's the process?"

### **User Needs:**
- Understand the two-stage contract process
- Know what company needs to submit
- Understand customer review process
- Clear workflow from basic contract to active project

---

## 🔗 **INTEGRATED WITH OTHER SOLUTIONS:**

This solution works together with:
- **✅ Issue #1:** Dynamic Budget (milestone payments match budget type)
- **✅ Issue #2:** Payment Terms (milestone breakdown matches terms)
- **✅ Issue #3:** 24-hour undo (customer can undo before plan submitted)
- **✅ Issue #4:** In-app chat (opens after plan submitted for discussion)
- **✅ Issue #5:** Contract access (contract visible during all stages)

---

## 🎬 **COMPLETE TWO-STAGE CONTRACT PROCESS**

```
┌────────────────────────────────────────────────────────┐
│ STAGE 1: AUTOMATIC BASIC CONTRACT CREATION            │
│ (Immediate - When Quotation Accepted)                 │
└────────────────────────────────────────────────────────┘

Customer Accepts Quotation
         ↓
System Creates Basic Contract Record
├─ Contract ID: CNT-2026-001 ← Generated
├─ Project Title: Kitchen Sink Repair
├─ Total Budget: LKR 50,000 (Flexible ±10%)
├─ Payment Terms: Milestone-based
├─ Timeline: 20 days (Jan 15 - Feb 4)
├─ Customer: John Silva
├─ Company: ABC Construction
├─ Status: "pending_milestone_plan" ← KEY STATUS
├─ Chat Room: Created but inactive
└─ Undo Window: 24 hours (Issue #3)

System Sends Notifications
├─ Customer: "Contract created! Company will add plan."
├─ Company: "Add milestone plan within 48 hours!"
└─ Email + SMS + In-app notifications sent

Contract is Now Visible (Issue #5)
├─ My Contracts tab (shows "Pending Plan")
├─ From original request (contract link appears)
├─ Notification bell (contract created alert)
├─ Email link (click to view)
└─ Dashboard widgets (pending actions)

         ↓ (48-hour deadline for company)

┌────────────────────────────────────────────────────────┐
│ STAGE 2: COMPANY ADDS DETAILED MILESTONE PLAN         │
│ (Within 48 hours of contract creation)                │
└────────────────────────────────────────────────────────┘

Company Accesses Contract
├─ Sees "ACTION REQUIRED" alert
├─ Deadline countdown: "46 hours remaining"
└─ Clicks "Add Milestone Plan" button

Company Fills Milestone Plan Form
├─ Breaks project into 3-5 milestones
├─ Defines payment amount per milestone
├─ Sets duration for each phase
├─ Lists deliverables (checkbox list)
└─ Adds dependencies between milestones

System Validates Plan
├─ Total payments = Total budget ✓
├─ Total duration ≤ Contract timeline ✓
├─ All required fields filled ✓
└─ Logical milestone sequence ✓

Company Submits Plan
├─ System updates contract status
├─ Status changes: "pending_customer_approval"
├─ Chat room activates (Issue #4) ← Chat now available
└─ Notifications sent to customer

         ↓

┌────────────────────────────────────────────────────────┐
│ STAGE 3: CUSTOMER REVIEWS & DECIDES                   │
│ (After company submits plan)                           │
└────────────────────────────────────────────────────────┘

Customer Receives Notifications
├─ Email: "Milestone plan submitted - Review needed"
├─ SMS: "ABC Construction submitted plan. Login."
├─ In-app: Notification badge appears
└─ Dashboard: "Action required" widget shows

Customer Reviews Plan
├─ Sees full milestone breakdown
├─ Payment schedule visualization
├─ Timeline chart
├─ Deliverables lists
└─ Company notes and attachments

Chat Opens for Discussion (Issue #4)
├─ Customer can ask questions
├─ Negotiate changes
├─ Request clarifications
└─ Discuss any concerns

Customer Makes Decision:

┌─ OPTION A: APPROVE PLAN ──────────────────┐
│ Customer clicks "Approve Plan"            │
│ ↓                                         │
│ Confirmation dialog appears               │
│ ↓                                         │
│ Customer confirms approval                │
│ ↓                                         │
│ System activates contract                 │
│ Status: "active"                          │
│ Project record created                    │
│ Milestones initialized                    │
│ Work can begin                            │
│ Chat remains active                       │
└───────────────────────────────────────────┘

┌─ OPTION B: REQUEST CHANGES ───────────────┐
│ Customer clicks "Request Changes"         │
│ ↓                                         │
│ Change request form appears               │
│ ↓                                         │
│ Customer specifies what to change         │
│ ↓                                         │
│ Chat opens with pre-filled message        │
│ ↓                                         │
│ Company responds and revises plan         │
│ ↓                                         │
│ Company submits revised plan (Version 2)  │
│ ↓                                         │
│ Customer reviews revised plan             │
│ ↓                                         │
│ Approve or request more changes           │
└───────────────────────────────────────────┘

┌─ OPTION C: REJECT PLAN ───────────────────┐
│ Customer clicks "Reject Plan"             │
│ ↓                                         │
│ Rejection reason form appears             │
│ ↓                                         │
│ Customer explains why rejecting           │
│ ↓                                         │
│ Status: "under_negotiation"               │
│ ↓                                         │
│ Chat opens for discussion                 │
│ ↓                                         │
│ Possible outcomes:                        │
│ • Company submits new plan → Review again │
│ • Agreement reached → Contract updates    │
│ • No agreement → Contract cancelled       │
└───────────────────────────────────────────┘

         ↓ (After approval)

┌────────────────────────────────────────────────────────┐
│ STAGE 4: CONTRACT ACTIVATION & WORK BEGINS            │
│ (After customer approves milestone plan)              │
└────────────────────────────────────────────────────────┘

System Activates Contract
├─ Status: "active"
├─ Milestone 1 status: "active" (ready to start)
├─ Milestones 2-4 status: "pending" (waiting for previous)
├─ Project record created (PRJ-2026-001)
├─ Payment schedule locked
├─ Chat remains active
└─ Progress tracking begins

Notifications Sent
├─ Customer: "Contract activated! Work begins [date]."
├─ Company: "Customer approved! You can start work."
└─ Calendar reminders set

Work Can Begin
├─ Company starts Milestone 1
├─ Customer tracks progress
├─ Chat used for updates
├─ Photos shared via chat
└─ Issues discussed in real-time

Milestone Completion Flow
├─ Company marks milestone complete
├─ Customer receives notification
├─ Customer reviews deliverables
├─ Customer approves milestone
├─ Payment released automatically
├─ Next milestone becomes "active"
└─ Process repeats until project done

         ↓ (All milestones complete)

Contract Completed
├─ Status: "completed"
├─ Customer rates company
├─ Final invoice generated
├─ Chat history preserved
└─ Contract archived but accessible
```

---

## 📊 **STAGE 1: AUTOMATIC BASIC CONTRACT CREATION**

### **What System Creates Automatically:**

```sql
-- When customer accepts quotation, system creates:

INSERT INTO contracts (
    contract_number,
    quotation_id,
    request_id,
    customer_id,
    company_id,
    project_title,
    project_description,
    
    -- Budget (Issue #1)
    total_budget,
    budget_type,                  -- 'flexible' or 'fixed'
    budget_flexibility_percent,   -- 10 for flexible, 0 for fixed
    budget_min,                   -- 45,000 (if flexible)
    budget_max,                   -- 55,000 (if flexible)
    
    -- Payment Terms (Issue #2)
    payment_terms,               -- 'milestone', 'upfront_final', 'after_completion', 'time_material'
    pricing_type,                -- 'fixed_price', 'time_material', 'unit_price'
    
    -- Timeline
    estimated_start_date,
    estimated_end_date,
    estimated_duration_days,
    
    -- Status & Tracking
    status,                      -- 'pending_milestone_plan' ← KEY
    created_at,
    undo_deadline,               -- NOW() + 24 HOURS (Issue #3)
    milestone_plan_deadline      -- NOW() + 48 HOURS
    
) VALUES (
    'CNT-2026-001',              -- Generated contract number
    42,                          -- quotation_id
    15,                          -- request_id
    105,                         -- customer_id (John Silva)
    8,                           -- company_id (ABC Construction)
    'Kitchen Sink Repair',
    'Repair and replace kitchen sink...',
    
    50000.00,                    -- total_budget
    'flexible',                  -- budget_type
    10.00,                       -- ±10% flexibility
    45000.00,                    -- budget_min (50k - 10%)
    55000.00,                    -- budget_max (50k + 10%)
    
    'milestone',                 -- payment_terms
    'fixed_price',               -- pricing_type
    
    '2026-01-15',                -- estimated_start_date
    '2026-02-04',                -- estimated_end_date
    20,                          -- estimated_duration_days
    
    'pending_milestone_plan',    -- status ← Contract created but not active
    NOW(),                       -- created_at
    DATE_ADD(NOW(), INTERVAL 24 HOUR),   -- undo_deadline
    DATE_ADD(NOW(), INTERVAL 48 HOUR)    -- milestone_plan_deadline
);

-- Create chat room (inactive until plan submitted) - Issue #4
INSERT INTO chat_rooms (
    contract_id,
    customer_id,
    company_id,
    status,                      -- 'inactive' ← Chat not available yet
    created_at
) VALUES (
    'CNT-2026-001',
    105,
    8,
    'inactive',                  -- Will change to 'active' after plan submitted
    NOW()
);
```

### **What Customer Sees (Stage 1):**

```
┌──────────────────────────────────────────────────────────┐
│  Contract #CNT-2026-001                                  │
│  Kitchen Sink Repair                                     │
│  Status: ⏳ Pending Milestone Plan                       │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ⚠️ CONTRACT NOT YET ACTIVE                              │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  📋 WAITING FOR MILESTONE PLAN                     │ │
│  │                                                    │ │
│  │  ABC Construction is preparing:                    │ │
│  │  • Detailed work breakdown into milestones        │ │
│  │  • Payment schedule per milestone                  │ │
│  │  • Timeline for each phase                         │ │
│  │  • Deliverables description                        │ │
│  │  • Dependencies between phases                     │ │
│  │                                                    │ │
│  │  📅 Deadline: Jan 27, 2026 at 2:30 PM             │ │
│  │  ⏰ Expected: Within 24-48 hours                   │ │
│  │                                                    │ │
│  │  You'll be notified when plan is submitted:       │ │
│  │  ✉️ Email notification                             │ │
│  │  📱 SMS alert                                      │ │
│  │  🔔 In-app notification                            │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  📊 CONTRACT DETAILS:                                    │
│                                                          │
│  🏢 Company: ABC Construction (Pvt) Ltd                 │
│     Rating: ⭐⭐⭐⭐⭐ 4.8/5.0                            │
│     Verified ✅ | License: #ABC-12345                   │
│                                                          │
│  💰 Budget: LKR 50,000                                  │
│     Type: Flexible (±10%)                               │
│     Range: LKR 45,000 - 55,000                          │
│     [View Budget Details] (Issue #1)                     │
│                                                          │
│  💳 Payment Terms: Milestone-based                       │
│     Pricing: Fixed Price                                │
│     [View Payment Options] (Issue #2)                    │
│                                                          │
│  📅 Timeline: 20 days                                   │
│     Start: Jan 15, 2026                                 │
│     End: Feb 4, 2026                                    │
│                                                          │
│  💬 CHAT: Not Available Yet                              │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  💬 Chat will open after milestone plan submitted │ │
│  │                                                    │ │
│  │  Why chat opens later:                             │ │
│  │  • Prevents spam during plan preparation          │ │
│  │  • Opens when discussion needed (plan review)     │ │
│  │  • Remains active throughout project              │ │
│  │                                                    │ │
│  │  Features when chat opens:                         │ │
│  │  ✓ Text messaging                                 │ │
│  │  ✓ File attachments                                │ │
│  │  ✓ Photo sharing                                  │ │
│  │  ✓ Location pins                                  │ │
│  │  ✓ No phone numbers shared (privacy)             │ │
│  │                                                    │ │
│  │  Need urgent contact now?                          │ │
│  │  [📧 Email Company] [📞 Request Callback]         │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ⏰ 24-HOUR UNDO WINDOW (Issue #3)                      │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  🔄 You can still undo this acceptance             │ │
│  │  Time remaining: 22:15:30                          │ │
│  │  Deadline: Jan 26, 2026 at 2:30 PM               │ │
│  │  [⏪ UNDO ACCEPTANCE]                              │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **What Company Sees (Stage 1):**

```
┌──────────────────────────────────────────────────────────┐
│  Contract #CNT-2026-001                                  │
│  Kitchen Sink Repair - John Silva                        │
│  Status: ⚠️ ACTION REQUIRED                              │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  🎉 QUOTATION ACCEPTED!                                  │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  ⚠️ IMMEDIATE ACTION REQUIRED                      │ │
│  │                                                    │ │
│  │  You must add a detailed milestone plan            │ │
│  │  within 48 hours to activate this contract.       │ │
│  │                                                    │ │
│  │  📅 Deadline: Jan 27, 2026 at 2:30 PM             │ │
│  │  ⏰ Remaining: 47 hours 15 minutes                 │ │
│  │                                                    │ │
│  │  [➕ ADD MILESTONE PLAN NOW]  ←────────────────── │ │
│  │         ↑ PRIMARY ACTION BUTTON                   │ │
│  │                                                    │ │
│  │  Why this deadline:                                │ │
│  │  • Customer is waiting to start project           │ │
│  │  • Contract not active until plan submitted       │ │
│  │  • Customer may cancel if delayed too long        │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  📊 CONTRACT DETAILS:                                    │
│                                                          │
│  👤 Customer: John Silva                                │
│     Location: Colombo 07                                │
│     Phone: Available after plan approved                │
│                                                          │
│  🏗️ Project: Kitchen Sink Repair                        │
│     Description: [Full description...]                  │
│                                                          │
│  💰 Budget: LKR 50,000                                  │
│     Type: Flexible (±10%)                               │
│     Your quote: LKR 50,000                              │
│     Allowed range: LKR 45,000 - 55,000                  │
│                                                          │
│  💳 Payment Terms: Milestone-based                       │
│     You agreed to: Payment per milestone completion     │
│                                                          │
│  📅 Timeline: 20 days (Jan 15 - Feb 4)                 │
│     You must create milestones within this timeframe    │
│                                                          │
│  📋 WHAT TO INCLUDE IN MILESTONE PLAN:                   │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  ✓ Work Breakdown (3-5 milestones recommended)    │ │
│  │    Break project into logical phases              │ │
│  │                                                    │ │
│  │  ✓ Payment Amount per Milestone                    │ │
│  │    Total must equal LKR 50,000                    │ │
│  │    (or within flexible range)                      │ │
│  │                                                    │ │
│  │  ✓ Duration for Each Phase                         │ │
│  │    Total must fit in 20-day timeline              │ │
│  │                                                    │ │
│  │  ✓ Deliverables and Tasks                          │ │
│  │    Clear, measurable outcomes                      │ │
│  │                                                    │ │
│  │  ✓ Dependencies Between Milestones                 │ │
│  │    Which phases depend on others                   │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  💡 TIPS FOR CREATING MILESTONE PLAN:                    │
│                                                          │
│  • Keep milestones measurable and verifiable           │
│  • Allow customer inspection points                     │
│  • Plan for contingency time                            │
│  • Be specific about deliverables                       │
│  • Use templates for common project types              │
│  • Review similar past projects                         │
│                                                          │
│  [📋 View Milestone Templates]                          │
│  [👁️ See Example Plans]                                 │
│  [❓ Milestone Planning Guide]                          │
│                                                          │
│  💬 CHAT: Opens After Plan Submitted                     │
│  Customer will be able to chat with you after you      │
│  submit the milestone plan. Use chat to discuss        │
│  any questions or changes they request.                 │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 🛠️ **STAGE 2: COMPANY ADDS MILESTONE PLAN**

### **The Milestone Planning Form:**

```
┌──────────────────────────────────────────────────────────┐
│  Add Milestone Plan                                      │
│  Contract #CNT-2026-001 - Kitchen Sink Repair            │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  📊 CONTRACT CONSTRAINTS:                                │
│                                                          │
│  Total Budget: LKR 50,000 (Flexible ±10%)               │
│  Allowed Range: LKR 45,000 - 55,000                      │
│  Timeline: 20 days (Jan 15 - Feb 4)                     │
│  Payment Terms: Milestone-based                          │
│                                                          │
│  ⚠️ Validation Rules:                                    │
│  • Sum of milestone payments must be within budget range│
│  • Total milestone duration must ≤ 20 days              │
│  • All required fields must be filled                    │
│  • At least 1 deliverable per milestone                 │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📋 MILESTONE 1                                          │
│                                                          │
│  Milestone Name: *                                       │
│  [Site Preparation & Material Procurement___________]    │
│                                                          │
│  Description: * (Min 50 characters)                      │
│  ┌────────────────────────────────────────────────────┐ │
│  │ • Clear work area and protect surroundings         │ │
│  │ • Remove old sink and fixtures                     │ │
│  │ • Inspect plumbing and electrical systems          │ │
│  │ • Procure all required materials                   │ │
│  │ • Prepare installation area                        │ │
│  │ • Verify material quality                          │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  Duration: * [5] days                                   │
│  Start Date: [Jan 15, 2026] (Auto-filled from contract)│
│  End Date: [Jan 19, 2026] (Auto-calculated)            │
│                                                          │
│  Payment Amount: * [LKR] [12,500]                       │
│  Percentage: 25% of total budget                         │
│  ℹ️ Within allowed range ✓                              │
│                                                          │
│  Payment Trigger: *                                      │
│  ( ) At start of milestone                              │
│  (•) On completion of milestone ← Selected              │
│  ( ) After customer approval of deliverables            │
│                                                          │
│  Deliverables: * (Add at least 1)                       │
│  ☑ Work area prepared and protected                     │
│  ☑ Old sink and fixtures removed                        │
│  ☑ Plumbing and electrical inspected                    │
│  ☑ All materials procured and verified                  │
│  ☑ Installation area ready                              │
│  [+ Add Deliverable]                                    │
│                                                          │
│  Dependencies:                                           │
│  ( ) None - Can start immediately                       │
│  (•) None (first milestone)                             │
│                                                          │
│  Photos/Documents: (Optional)                            │
│  [📎 Attach Files] (Material list, schedule, etc.)     │
│                                                          │
│  [➕ Add Another Milestone]  [❌ Remove This Milestone]  │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📋 MILESTONE 2                                          │
│                                                          │
│  Milestone Name: *                                       │
│  [Main Installation Work_________________________]       │
│                                                          │
│  Description: *                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ • Install new plumbing pipes and connections       │ │
│  │ • Mount sink and countertop properly               │ │
│  │ • Connect water supply lines                        │ │
│  │ • Install drainage system                           │ │
│  │ • Install faucet and fixtures                      │ │
│  │ • Test water pressure and flow                      │ │
│  │ • Check for leaks thoroughly                        │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  Duration: * [10] days                                  │
│  Start Date: [Jan 20, 2026] (Auto-calculated)          │
│  End Date: [Jan 29, 2026]                              │
│                                                          │
│  Payment Amount: * [LKR] [25,000]                       │
│  Percentage: 50% of total budget                         │
│                                                          │
│  Payment Trigger: *                                      │
│  (•) On completion of milestone                         │
│                                                          │
│  Deliverables: *                                         │
│  ☑ All plumbing installed and tested                    │
│  ☑ Sink properly mounted and secured                    │
│  ☑ No leaks detected                                    │
│  ☑ Water pressure satisfactory                          │
│  ☑ Drainage functioning properly                        │
│  [+ Add Deliverable]                                    │
│                                                          │
│  Dependencies:                                           │
│  (•) Depends on: Milestone 1 (Site Preparation)         │
│  ⚠️ Cannot start until M1 is completed                  │
│                                                          │
│  [➕ Add Another Milestone]  [❌ Remove This Milestone]  │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📋 MILESTONE 3                                          │
│                                                          │
│  Milestone Name: *                                       │
│  [Finishing & Quality Check__________________]          │
│                                                          │
│  Description: *                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ • Seal all joints with caulking                    │ │
│  │ • Install drain covers and accessories             │ │
│  │ • Clean work area thoroughly                        │ │
│  │ • Final quality inspection                          │ │
│  │ • Customer walkthrough and approval                │ │
│  │ • Address any minor adjustments                     │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  Duration: * [3] days                                   │
│  Start Date: [Jan 30, 2026]                            │
│  End Date: [Feb 1, 2026]                               │
│                                                          │
│  Payment Amount: * [LKR] [10,000]                       │
│  Percentage: 20% of total budget                         │
│                                                          │
│  Deliverables: *                                         │
│  ☑ All finishing work completed                         │
│  ☑ Area cleaned and restored                            │
│  ☑ Quality standards met                                │
│  ☑ Customer walkthrough completed                       │
│  ☑ Minor adjustments addressed                          │
│                                                          │
│  Dependencies:                                           │
│  (•) Depends on: Milestone 2 (Main Installation)        │
│                                                          │
│  [➕ Add Another Milestone]  [❌ Remove This Milestone]  │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📋 MILESTONE 4                                          │
│                                                          │
│  Milestone Name: *                                       │
│  [Final Inspection & Handover________________]          │
│                                                          │
│  Description: *                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ • Complete system functionality test               │ │
│  │ • Provide warranty documentation                   │ │
│  │ • Train customer on maintenance                    │ │
│  │ • Address any final concerns                       │ │
│  │ • Official project sign-off                        │ │
│  │ • Provide maintenance guidelines                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  Duration: * [2] days                                   │
│  Start Date: [Feb 2, 2026]                             │
│  End Date: [Feb 4, 2026]                               │
│                                                          │
│  Payment Amount: * [LKR] [2,500]                        │
│  Percentage: 5% of total budget                          │
│                                                          │
│  Deliverables: *                                         │
│  ☑ All work verified and approved                       │
│  ☑ Warranty documents provided                          │
│  ☑ Customer trained on usage/maintenance                │
│  ☑ Project documentation complete                       │
│  ☑ Official sign-off obtained                           │
│                                                          │
│  Dependencies:                                           │
│  (•) Depends on: Milestone 3 (Finishing)                │
│                                                          │
│  [❌ Remove This Milestone]                             │
│                                                          │
│  ══════════════════════════════════════════════════════  │
│                                                          │
│  📊 MILESTONE PLAN SUMMARY                               │
│                                                          │
│  Total Milestones: 4                                    │
│  Total Duration: 20 days ✓ (Fits timeline)             │
│  Total Payments: LKR 50,000 ✓ (Matches budget)         │
│                                                          │
│  ┌─ VALIDATION RESULTS ────────────────────────────┐   │
│  │ ✅ Payment amounts sum to total budget           │   │
│  │ ✅ Timeline fits within contract duration        │   │
│  │ ✅ All milestones have descriptions              │   │
│  │ ✅ Deliverables defined for each phase           │   │
│  │ ✅ Dependencies properly set                     │   │
│  │ ✅ No scheduling conflicts                       │   │
│  └──────────────────────────────────────────────────┘   │
│                                                          │
│  Payment Breakdown:                                      │
│  ┌──────────────────────────────────────────────────┐   │
│  │                                                  │   │
│  │  M1: 25% │ M2: 50%    │ M3: 20% │ M4: 5%        │   │
│  │  12,500  │ 25,000     │ 10,000  │ 2,500         │   │
│  │                                                  │   │
│  └──────────────────────────────────────────────────┘   │
│                                                          │
│  Timeline Visualization:                                 │
│  Jan 15──────┤ M1 ├──────Jan 20────────┤ M2 ├──────    │
│  │           5d          │             10d               │
│  Jan 30──┤M3├──Feb 2┤M4├──Feb 4                         │
│          3d       2d                                     │
│                                                          │
│  Additional Notes (Optional):                            │
│  ┌────────────────────────────────────────────────────┐ │
│  │ • Weather-dependent work may cause slight delays   │ │
│  │ • Customer should ensure area is accessible daily  │ │
│  │ • All materials covered by 2-year warranty         │ │
│  │ • Emergency contact: Available after approval      │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  Attachments (Optional):                                 │
│  📎 Work_Schedule.pdf (Uploaded)                        │
│  📎 Material_List.xlsx (Uploaded)                       │
│  [📎 Add More Files]                                    │
│                                                          │
│  ══════════════════════════════════════════════════════  │
│                                                          │
│  ACTIONS:                                                │
│                                                          │
│  [💾 Save as Draft]  [👁️ Preview]  [✅ Submit for Approval]
│                                            ↑ PRIMARY     │
│                                                          │
│  ℹ️ Saving as draft: You can return to edit later       │
│  ℹ️ Preview: See how customer will view your plan       │
│  ℹ️ Submit: Sends plan to customer for review           │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **Validation Logic:**

```javascript
// System validates before allowing submission:

function validateMilestonePlan(plan, contract) {
    let errors = [];
    
    // 1. Budget Validation (Issue #1)
    let totalPayments = plan.milestones.reduce(
        (sum, m) => sum + m.payment_amount, 0
    );
    
    if (contract.budget_type === 'flexible') {
        if (totalPayments < contract.budget_min || 
            totalPayments > contract.budget_max) {
            errors.push(
                `Total payments (${totalPayments}) must be within ` +
                `${contract.budget_min} - ${contract.budget_max}`
            );
        }
    } else { // fixed budget
        if (totalPayments !== contract.total_budget) {
            errors.push(
                `Total payments (${totalPayments}) must equal ` +
                `budget (${contract.total_budget})`
            );
        }
    }
    
    // 2. Timeline Validation
    let totalDays = plan.milestones.reduce(
        (sum, m) => sum + m.duration_days, 0
    );
    
    if (totalDays > contract.estimated_duration_days) {
        errors.push(
            `Total duration (${totalDays} days) exceeds ` +
            `contract timeline (${contract.estimated_duration_days} days)`
        );
    }
    
    // 3. Required Fields
    plan.milestones.forEach((milestone, index) => {
        if (!milestone.name || milestone.name.trim() === '') {
            errors.push(`Milestone ${index + 1}: Name required`);
        }
        
        if (!milestone.description || milestone.description.length < 50) {
            errors.push(
                `Milestone ${index + 1}: Description too short (min 50 chars)`
            );
        }
        
        if (!milestone.payment_amount || milestone.payment_amount <= 0) {
            errors.push(`Milestone ${index + 1}: Payment amount required`);
        }
        
        if (!milestone.duration_days || milestone.duration_days <= 0) {
            errors.push(`Milestone ${index + 1}: Duration required`);
        }
        
        if (!milestone.deliverables || milestone.deliverables.length === 0) {
            errors.push(
                `Milestone ${index + 1}: At least 1 deliverable required`
            );
        }
    });
    
    // 4. Logical Sequence
    // Check if dependent milestones come after their dependencies
    plan.milestones.forEach((milestone, index) => {
        if (milestone.depends_on) {
            let dependencyIndex = plan.milestones.findIndex(
                m => m.id === milestone.depends_on
            );
            if (dependencyIndex >= index) {
                errors.push(
                    `Milestone ${index + 1}: Dependency must come before`
                );
            }
        }
    });
    
    return {
        valid: errors.length === 0,
        errors: errors
    };
}
```

---

## 📋 **STAGE 3: CUSTOMER REVIEWS MILESTONE PLAN**

### **After Company Submits, Customer Sees:**

```
┌──────────────────────────────────────────────────────────┐
│  Contract #CNT-2026-001                                  │
│  Kitchen Sink Repair                                     │
│  Status: 📋 PENDING YOUR REVIEW                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  🆕 MILESTONE PLAN SUBMITTED - ACTION REQUIRED           │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  ABC Construction submitted a detailed             │ │
│  │  milestone plan for your review and approval.      │ │
│  │                                                    │ │
│  │  📅 Submitted: Jan 25, 2026 at 4:30 PM            │ │
│  │  📊 4 Milestones | LKR 50,000 | 20 Days           │ │
│  │                                                    │ │
│  │  💬 CHAT NOW AVAILABLE (Issue #4)                  │ │
│  │  You can now discuss the plan with the company    │ │
│  │  [Open Chat →]                                     │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  [📋 Milestone Plan] [💬 Chat] [📄 Files]               │
│         ↑ ACTIVE TAB                                     │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📊 MILESTONE PLAN SUMMARY                               │
│                                                          │
│  Total Budget: LKR 50,000                               │
│  Total Duration: 20 days (Jan 15 - Feb 4)              │
│  Payment Method: Milestone-based (4 milestones)          │
│  All Payments: On milestone completion                   │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📋 MILESTONE BREAKDOWN                                  │
│                                                          │
│  ┌─ MILESTONE 1 ─────────────────────────────────────┐  │
│  │ Site Preparation & Material Procurement           │  │
│  │                                                    │  │
│  │ ⏰ Duration: 5 days (Jan 15 - Jan 19)             │  │
│  │ 💰 Payment: LKR 12,500 (25%) - On completion      │  │
│  │                                                    │  │
│  │ 📝 Description:                                    │  │
│  │ • Clear work area and protect surroundings        │  │
│  │ • Remove old sink and fixtures                    │  │
│  │ • Inspect plumbing and electrical systems         │  │
│  │ • Procure all required materials                  │  │
│  │ • Prepare installation area                       │  │
│  │ • Verify material quality                         │  │
│  │                                                    │  │
│  │ ✅ Deliverables:                                   │  │
│  │ ☑ Work area prepared and protected                │  │
│  │ ☑ Old sink and fixtures removed                   │  │
│  │ ☑ Plumbing and electrical inspected               │  │
│  │ ☑ All materials procured and verified             │  │
│  │ ☑ Installation area ready                         │  │
│  │                                                    │  │
│  │ 🔗 Dependencies: None (starts immediately)         │  │
│  │                                                    │  │
│  │ [💬 Ask About This Milestone]  ←─────────────────│  │
│  │       Opens chat with pre-filled question         │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌─ MILESTONE 2 ─────────────────────────────────────┐  │
│  │ Main Installation Work                             │  │
│  │                                                    │  │
│  │ ⏰ Duration: 10 days (Jan 20 - Jan 29)            │  │
│  │ 💰 Payment: LKR 25,000 (50%) - On completion      │  │
│  │                                                    │  │
│  │ 📝 Description:                                    │  │
│  │ • Install new plumbing pipes and connections      │  │
│  │ • Mount sink and countertop properly              │  │
│  │ • Connect water supply lines                       │  │
│  │ • Install drainage system                          │  │
│  │ • Install faucet and fixtures                     │  │
│  │ • Test water pressure and flow                     │  │
│  │ • Check for leaks thoroughly                       │  │
│  │                                                    │  │
│  │ ✅ Deliverables:                                   │  │
│  │ ☑ All plumbing installed and tested               │  │
│  │ ☑ Sink properly mounted and secured               │  │
│  │ ☑ No leaks detected                               │  │
│  │ ☑ Water pressure satisfactory                     │  │
│  │ ☑ Drainage functioning properly                   │  │
│  │                                                    │  │
│  │ 🔗 Dependencies: Milestone 1 must be completed    │  │
│  │                                                    │  │
│  │ [💬 Ask About This Milestone]                     │  │
│  │ [👁️ Expand for Full Details]                      │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌─ MILESTONE 3 ─────────────────────────────────────┐  │
│  │ Finishing & Quality Check                          │  │
│  │ ⏰ 3 days | 💰 LKR 10,000 (20%)                   │  │
│  │ [👁️ Expand...]                                     │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ┌─ MILESTONE 4 ─────────────────────────────────────┐  │
│  │ Final Inspection & Handover                        │  │
│  │ ⏰ 2 days | 💰 LKR 2,500 (5%)                     │  │
│  │ [👁️ Expand...]                                     │  │
│  └────────────────────────────────────────────────────┘  │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📊 TIMELINE VISUALIZATION                               │
│                                                          │
│  Jan 15────────┤ M1 ├────────Jan 20──────────┤ M2 ├────│
│  │             5d              │              10d        │
│  └────────────────────────────────────────────────────   │
│  Jan 30──┤M3├──Feb 2┤M4├──Feb 4                         │
│          3d       2d                                     │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  💰 PAYMENT SCHEDULE                                     │
│                                                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │                                                  │   │
│  │  M1: 25% │ M2: 50%    │ M3: 20% │ M4: 5%        │   │
│  │  12,500  │ 25,000     │ 10,000  │ 2,500         │   │
│  │                                                  │   │
│  │  Trigger: All payments released on completion    │   │
│  └──────────────────────────────────────────────────┘   │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📝 COMPANY NOTES                                        │
│                                                          │
│  "• Weather-dependent work may cause slight delays      │
│   • Customer should ensure area is accessible daily     │
│   • All materials covered by 2-year warranty            │
│   • Emergency contact: Available after approval"        │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📎 ATTACHMENTS                                          │
│                                                          │
│  📄 Work_Schedule.pdf (245 KB)                          │
│  📄 Material_List.xlsx (128 KB)                         │
│  [Download All]                                          │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  💬 QUESTIONS OR CONCERNS?                               │
│                                                          │
│  You can now chat with ABC Construction about           │
│  this plan. Chat opened when plan was submitted.        │
│                                                          │
│  [💬 OPEN CHAT NOW]  ←─────────────────────────────── │
│        ↑ Chat feature from Issue #4                     │
│                                                          │
│  Chat Features Available:                                │
│  ✓ Text messaging                                       │
│  ✓ Ask questions about specific milestones             │
│  ✓ Request clarifications                               │
│  ✓ Negotiate changes                                    │
│  ✓ Share concerns                                       │
│  ✓ File sharing                                         │
│  ✓ All communication documented                         │
│  ✓ No phone numbers shared (privacy)                   │
│                                                          │
│  ══════════════════════════════════════════════════════  │
│                                                          │
│  ⚠️ YOUR DECISION REQUIRED                               │
│                                                          │
│  Please review the milestone plan carefully and         │
│  choose one of the following options:                   │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  [✅ APPROVE PLAN - START WORK]  ←─────────────── │ │
│  │        ↑ PRIMARY GREEN BUTTON                      │ │
│  │                                                    │ │
│  │  What happens if you approve:                      │ │
│  │  ✓ Contract becomes fully active                   │ │
│  │  ✓ Payment schedule locked                         │ │
│  │  ✓ Company can start work on Jan 15               │ │
│  │  ✓ Milestone 1 becomes active                      │ │
│  │  ✓ Progress tracking begins                        │ │
│  │  ✓ Chat remains active for updates                 │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  [📝 REQUEST CHANGES]  ←────────────────────────── │ │
│  │        ↑ ORANGE BUTTON                             │ │
│  │                                                    │ │
│  │  What happens if you request changes:              │ │
│  │  • Form opens to specify changes needed           │ │
│  │  • Chat opens with your change request            │ │
│  │  • Company reviews and responds                    │ │
│  │  • Company submits revised plan (Version 2)       │ │
│  │  • You review revised plan                         │ │
│  │  • Process repeats until approved                  │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  [❌ REJECT PLAN]  ←────────────────────────────── │ │
│  │        ↑ RED BUTTON                                │ │
│  │                                                    │ │
│  │  What happens if you reject:                       │ │
│  │  • Form opens to explain why rejecting            │ │
│  │  • Status changes to "under_negotiation"          │ │
│  │  • Chat opens for discussion                       │ │
│  │  • Options:                                        │ │
│  │    - Company submits new plan                      │ │
│  │    - Agreement reached via chat                    │ │
│  │    - Cancel contract if no agreement               │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ⏰ Note: If undo window still active (within 24hrs    │
│  of acceptance), you can still undo the entire         │
│  acceptance if you prefer.                              │
│                                                          │
│  [View 24-hr Undo Status]                              │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## ✅ **OPTION A: APPROVE PLAN**

### **When Customer Clicks "Approve Plan":**

```
┌──────────────────────────────────────────────────────────┐
│  ✅ APPROVE MILESTONE PLAN?                              │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  You are about to approve this milestone plan:           │
│                                                          │
│  📊 Plan Summary:                                        │
│  • 4 milestones                                          │
│  • 20 days timeline (Jan 15 - Feb 4)                    │
│  • LKR 50,000 total budget                              │
│  • Milestone-based payments                              │
│                                                          │
│  ⚠️ THIS WILL:                                           │
│                                                          │
│  ✓ Activate the contract                                │
│  ✓ Lock payment schedule                                │
│  ✓ Allow company to start work on Jan 15               │
│  ✓ Begin milestone tracking                             │
│  ✓ Release payments as milestones complete             │
│                                                          │
│  ⚠️ ONCE APPROVED:                                       │
│  • Payment schedule cannot be changed                    │
│  • Major changes require renegotiation                   │
│  • Cancellation may incur fees                          │
│  • Budget changes need approval process (Issue #1)      │
│                                                          │
│  💬 CHAT REMINDER:                                       │
│  Chat will remain active for project updates,           │
│  questions, and coordination.                            │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  ☐ I have carefully reviewed this milestone plan  │ │
│  │     and agree to approve it.                       │ │
│  │         ↑ MUST CHECK TO PROCEED                    │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  [Cancel]  [✅ CONFIRM APPROVAL]                        │
│                    ↑ Only active when checked           │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **After Approval, System Does:**

```javascript
// Backend processing:

async function activateContract(contractId) {
    // 1. Update contract status
    await database.execute(`
        UPDATE contracts 
        SET status = 'active',
            milestone_plan_approved_at = NOW(),
            approved_by_customer = TRUE
        WHERE contract_id = ?
    `, [contractId]);
    
    // 2. Create project record
    await database.execute(`
        INSERT INTO projects (
            contract_id,
            title,
            customer_id,
            company_id,
            budget,
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, 'active', NOW())
    `, [/* contract data */]);
    
    // 3. Create milestone records from plan
    for (let milestone of milestones) {
        await database.execute(`
            INSERT INTO milestones (
                contract_id,
                milestone_number,
                name,
                description,
                payment_amount,
                duration_days,
                start_date,
                end_date,
                status,  -- 'active' for M1, 'pending' for others
                deliverables_json,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        `, [/* milestone data */]);
    }
    
    // 4. Keep chat active (already opened after plan submission)
    await database.execute(`
        UPDATE chat_rooms 
        SET status = 'active_project'
        WHERE contract_id = ?
    `, [contractId]);
    
    // 5. Send notifications
    await sendNotification({
        to: customerId,
        type: 'contract_activated',
        message: 'Milestone plan approved! Work begins Jan 15.'
    });
    
    await sendNotification({
        to: companyId,
        type: 'plan_approved',
        message: 'Customer approved plan. You can start work!'
    });
    
    // 6. Create audit log
    await logAction({
        action: 'milestone_plan_approved',
        contract: contractId,
        user: customerId,
        timestamp: Date.now()
    });
}
```

### **Success Message:**

```
┌──────────────────────────────────────────────────────────┐
│  ✅ MILESTONE PLAN APPROVED!                             │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  🎉 Contract Activated Successfully!                     │
│                                                          │
│  📋 Contract: #CNT-2026-001                              │
│  🏗️ Project: #PRJ-2026-001 (Created)                    │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📅 WORK SCHEDULE:                                       │
│                                                          │
│  Start Date: Jan 15, 2026                               │
│  End Date: Feb 4, 2026                                  │
│  Duration: 20 days                                      │
│                                                          │
│  Next Milestone: Milestone 1 (Active)                   │
│  Site Preparation & Material Procurement                │
│  Due: Jan 19, 2026                                      │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📊 WHAT HAPPENS NEXT:                                   │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │  1️⃣ COMPANY BEGINS WORK (Jan 15)                   │ │
│  │     ABC Construction will start Milestone 1        │ │
│  │                                                    │ │
│  │  2️⃣ YOU RECEIVE PROGRESS UPDATES                   │ │
│  │     • 💬 Via chat                                   │ │
│  │     • 📧 Email notifications                       │ │
│  │     • 📸 Photos from site                           │ │
│  │                                                    │ │
│  │  3️⃣ APPROVE MILESTONES AS COMPLETED                │ │
│  │     Company marks milestone done →                 │ │
│  │     You review deliverables →                      │ │
│  │     You approve milestone →                        │ │
│  │     Payment released automatically                 │ │
│  │                                                    │ │
│  │  4️⃣ TRACK PROGRESS                                 │ │
│  │     Dashboard shows completion %                   │ │
│  │     Timeline updated in real-time                  │ │
│  │                                                    │ │
│  │  5️⃣ PROJECT COMPLETES                              │ │
│  │     Final inspection → Rate company →              │ │
│  │     Download invoice → Contract archived           │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  💬 CHAT IS ACTIVE                                       │
│                                                          │
│  You can now communicate with ABC Construction           │
│  throughout the project:                                 │
│                                                          │
│  • Daily updates                                        │
│  • Share photos                                         │
│  • Discuss issues                                       │
│  • Coordinate schedules                                 │
│  • Answer questions                                     │
│                                                          │
│  [💬 OPEN CHAT NOW]                                     │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  📧 NOTIFICATIONS SENT:                                  │
│                                                          │
│  ✓ Email: john.silva@email.com                         │
│  ✓ SMS: +94 77 *** **45                                │
│  ✓ Company: ABC Construction notified                   │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  [📋 VIEW PROJECT DASHBOARD]  [💬 CHAT]  [Close]       │
│        ↑ PRIMARY ACTION                                  │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 📝 **OPTION B: REQUEST CHANGES**

### **Change Request Form:**

```
┌──────────────────────────────────────────────────────────┐
│  📝 REQUEST CHANGES TO MILESTONE PLAN                    │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Which milestone(s) need changes?                        │
│  (Select all that apply)                                 │
│                                                          │
│  ☐ Milestone 1: Site Preparation                        │
│  ☐ Milestone 2: Main Installation                       │
│  ☐ Milestone 3: Finishing                               │
│  ☐ Milestone 4: Final Inspection                        │
│  ☑ Overall timeline/budget                               │
│  ☑ Payment schedule                                      │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  What changes do you want?                               │
│  (Be as specific as possible)                            │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  │ For Timeline:                                      │ │
│  │ Can we extend by 3 days? I need the kitchen       │ │
│  │ functional by Feb 7 instead of Feb 4 due to an    │ │
│  │ event we're hosting.                               │ │
│  │                                                    │ │
│  │ For Payment Schedule:                              │ │
│  │ Can we make the first payment smaller (10%)       │ │
│  │ and increase the final payment? I'd prefer        │ │
│  │ paying more after seeing the finished work.       │ │
│  │                                                    │ │
│  │ Suggested breakdown:                               │ │
│  │ M1: 10% (LKR 5,000)                               │ │
│  │ M2: 50% (LKR 25,000)                              │ │
│  │ M3: 25% (LKR 12,500)                              │ │
│  │ M4: 15% (LKR 7,500)                               │ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  Urgency Level:                                          │
│  ( ) Major changes - Need thorough discussion           │
│  (•) Minor adjustments - Company can probably accommodate│
│  ( ) Just questions - No major changes needed           │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  Preferred Discussion Method:                            │
│  (•) Chat (fastest - real-time discussion)              │
│  ( ) Phone call (schedule callback)                     │
│  ( ) Email (detailed written response)                  │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  ⚠️ WHAT HAPPENS NEXT:                                   │
│                                                          │
│  1. Your change request will be sent to the company     │
│  2. Chat will open automatically with your message      │
│  3. Company will respond to discuss feasibility         │
│  4. Company will submit revised plan (Version 2)        │
│  5. You'll receive notification to review revised plan  │
│  6. You can approve or request more changes             │
│                                                          │
│  💬 Chat allows real-time negotiation and discussion    │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  [Cancel]  [📤 SEND CHANGE REQUEST]                     │
│                    ↑ PRIMARY BUTTON                      │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **After Sending Change Request:**

```
┌──────────────────────────────────────────────────────────┐
│  ✅ CHANGE REQUEST SENT                                  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  Your change request has been sent to ABC Construction.  │
│                                                          │
│  📧 Company notified: Jan 25, 4:45 PM                   │
│  💬 Chat opened for discussion                           │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  NEXT STEPS:                                             │
│                                                          │
│  1. Company will review your requested changes          │
│  2. Company will respond via chat                        │
│  3. You can discuss and negotiate                        │
│  4. Company will submit revised plan                     │
│  5. You'll be notified when ready for review            │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  [💬 OPEN CHAT TO DISCUSS]  [View Original Plan]  [Close]│
│        ↑ PRIMARY ACTION                                  │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **Chat Opens Automatically with Pre-filled Message:**

```
┌──────────────────────────────────────────────────────────┐
│  💬 Chat with ABC Construction                           │
│  Contract #CNT-2026-001                                  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  [System Message] Jan 25, 4:45 PM                        │
│  Customer requested changes to milestone plan.           │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  [Your Message] Jan 25, 4:45 PM (Auto-sent)             │
│  Hi, I reviewed the milestone plan. Overall it looks    │
│  good, but I'd like to request a few changes:           │
│                                                          │
│  1. TIMELINE EXTENSION:                                  │
│  Can we extend the timeline by 3 days? I need the       │
│  kitchen functional by Feb 7 instead of Feb 4 due       │
│  to an event we're hosting.                              │
│                                                          │
│  2. PAYMENT SCHEDULE ADJUSTMENT:                         │
│  Can we make the first payment smaller and increase     │
│  the final payment? I'd prefer paying more after        │
│  seeing the finished work.                               │
│                                                          │
│  Suggested breakdown:                                    │
│  • M1: 10% (LKR 5,000)                                  │
│  • M2: 50% (LKR 25,000)                                 │
│  • M3: 25% (LKR 12,500)                                 │
│  • M4: 15% (LKR 7,500)                                  │
│                                                          │
│  Let me know if these changes are feasible!             │
│                                                    ✓✓    │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  [ABC Construction is typing...]                         │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  Type your message...                                    │
│  ┌────────────────────────────────────────────────────┐ │
│  │                                                    │ │
│  └────────────────────────────────────────────────────┘ │
│  [📎][📷][📍][😊][Send]                                │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

### **Company Response Example:**

```
[ABC Construction] Jan 25, 4:52 PM
Hi John! Thanks for your feedback. Let me address
your requests:

1. TIMELINE EXTENSION: ✅
Yes, we can extend to Feb 7. That gives us 23 days
total, which is perfect. I'll update the plan.

2. PAYMENT SCHEDULE: ⚠️ Partially
I can adjust, but 10% for M1 is very low considering
material procurement costs. How about:
• M1: 15% (LKR 7,500) - covers materials
• M2: 50% (LKR 25,000) - main work
• M3: 20% (LKR 10,000) - finishing
• M4: 15% (LKR 7,500) - final payment

This gives you more payment at the end while covering
our upfront material costs. Would this work?

[ABC Construction] Jan 25, 4:53 PM
I'll submit a revised plan once you approve these
adjustments. 👍

[Your Message] Jan 25, 5:00 PM
That sounds fair! Yes, please go ahead with those
adjustments. Looking forward to the revised plan.

[ABC Construction] Jan 25, 5:02 PM
Perfect! I'll prepare the revised plan (Version 2)
and submit it shortly. You'll get a notification
when it's ready for review.

[System Message] Jan 25, 5:15 PM
ABC Construction submitted revised milestone plan
(Version 2).
[📋 REVIEW REVISED PLAN →]
```

---

## 📊 **MILESTONE PLAN VERSION HISTORY**

### **Version Tracking System:**

```
┌──────────────────────────────────────────────────────────┐
│  📋 MILESTONE PLAN VERSION HISTORY                       │
│  Contract #CNT-2026-001                                  │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ✅ VERSION 2 (Current - Approved)                       │
│  ┌────────────────────────────────────────────────────┐ │
│  │ Submitted: Jan 25, 2026 at 5:15 PM                │ │
│  │ Status: ✅ Approved by customer                    │ │
│  │ Changes from v1:                                   │ │
│  │ • Timeline extended to 23 days (Feb 7)            │ │
│  │ • Payment schedule adjusted                        │ │
│  │   - M1: 15% (was 25%)                             │ │
│  │   - M2: 50% (same)                                │ │
│  │   - M3: 20% (same)                                │ │
│  │   - M4: 15% (was 5%)                              │ │
│  │                                                    │ │
│  │ [View This Version]                                │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ⏸️ VERSION 1 (Superseded)                               │
│  ┌────────────────────────────────────────────────────┐ │
│  │ Submitted: Jan 25, 2026 at 4:30 PM                │ │
│  │ Status: Customer requested changes                 │ │
│  │ Customer feedback:                                 │ │
│  │ "Can we extend timeline and adjust payments?"     │ │
│  │                                                    │ │
│  │ [View This Version]                                │ │
│  │ [📊 Compare with Version 2]                        │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  ──────────────────────────────────────────────────────  │
│                                                          │
│  COMPARISON TOOL:                                        │
│  [Compare v1 vs v2] [Show All Changes] [Download Report]│
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 🎯 **COMPLETE WORKFLOW SUMMARY**

```
╔══════════════════════════════════════════════════════╗
║ ISSUE #6: TWO-STAGE CONTRACT PROCESS - COMPLETE FLOW║
╚══════════════════════════════════════════════════════╝

┌─ STAGE 1: AUTO-CREATION (Immediate) ─────────────────┐
│ Customer accepts quotation                           │
│ ↓                                                    │
│ System creates basic contract                        │
│ ├─ Contract ID: CNT-2026-001                        │
│ ├─ Basic info from quotation                        │
│ ├─ Status: "pending_milestone_plan"                 │
│ ├─ Chat room: Created but inactive                  │
│ └─ Undo window: 24 hours (Issue #3)                 │
│ ↓                                                    │
│ Notifications sent to both parties                   │
│ Contract accessible from 5 locations (Issue #5)     │
└──────────────────────────────────────────────────────┘
         ↓ (Within 48 hours)
┌─ STAGE 2: MILESTONE PLANNING (Company) ──────────────┐
│ Company sees "ACTION REQUIRED" alert                 │
│ ↓                                                    │
│ Company fills milestone plan form                    │
│ ├─ 3-5 milestones                                   │
│ ├─ Payment per milestone                            │
│ ├─ Duration per phase                               │
│ ├─ Deliverables list                                │
│ └─ Dependencies                                      │
│ ↓                                                    │
│ System validates plan                                │
│ ├─ Budget matches (Issue #1)                        │
│ ├─ Timeline fits                                    │
│ └─ All required fields filled                       │
│ ↓                                                    │
│ Company submits plan                                 │
│ ├─ Status: "pending_customer_approval"              │
│ └─ Chat opens (Issue #4) ← NOW ACTIVE               │
└──────────────────────────────────────────────────────┘
         ↓ (Customer notified)
┌─ STAGE 3: CUSTOMER REVIEW & DECISION ────────────────┐
│ Customer receives notifications                      │
│ ↓                                                    │
│ Customer reviews milestone plan                      │
│ ├─ Full breakdown visible                           │
│ ├─ Payment schedule shown                           │
│ ├─ Timeline visualization                           │
│ └─ Chat available for questions                     │
│ ↓                                                    │
│ Customer chooses:                                    │
│ ├─ OPTION A: Approve → Contract activates           │
│ ├─ OPTION B: Request changes → Negotiation          │
│ └─ OPTION C: Reject → Discussion/cancellation       │
└──────────────────────────────────────────────────────┘
         ↓ (If approved)
┌─ STAGE 4: CONTRACT ACTIVATION ───────────────────────┐
│ System activates contract                            │
│ ├─ Status: "active"                                 │
│ ├─ Project record created                           │
│ ├─ Milestones initialized                           │
│ ├─ M1 status: "active"                              │
│ ├─ M2-M4 status: "pending"                          │
│ └─ Chat remains active                              │
│ ↓                                                    │
│ Notifications sent to both parties                   │
│ ↓                                                    │
│ Work can officially begin                            │
│ Progress tracking starts                             │
│ Milestone completion cycle begins                    │
└──────────────────────────────────────────────────────┘

╔══════════════════════════════════════════════════════╗
║ INTEGRATED WITH ALL OTHER SOLUTIONS:                ║
║ ✅ Issue #1: Budget flexibility in payments         ║
║ ✅ Issue #2: Payment terms determine milestone flow ║
║ ✅ Issue #3: 24-hour undo available during Stage 1  ║
║ ✅ Issue #4: Chat opens in Stage 2, stays active    ║
║ ✅ Issue #5: Contract visible from 5 access points  ║
╚══════════════════════════════════════════════════════╝
```

---

## ✅ **BENEFITS OF TWO-STAGE PROCESS**

### **1. Quick Response to Customer (Issue #5)**
- Contract created immediately
- Customer gets contract ID right away
- Visible from 5 access points instantly
- Feels progress happening

### **2. Proper Planning Time for Company**
- 48 hours to create detailed breakdown
- No rushed, poor-quality planning
- Can review similar projects
- Use templates for common work

### **3. Customer Control & Transparency**
- Must approve before work starts
- Sees exactly what they're paying for
- Can negotiate if needed
- All changes documented

### **4. Integrated Chat System (Issue #4)**
- Opens when plan submitted (when discussion needed)
- Available throughout review process
- Stays active during entire project
- Real-time negotiation possible

### **5. Budget Flexibility Integration (Issue #1)**
- Flexible budgets: Payments can be within ±10% range
- Fixed budgets: Payments must equal exact amount
- System validates automatically

### **6. Payment Terms Integration (Issue #2)**
- Milestone-based: Payments per milestone completion
- Upfront+Final: Different milestone structure
- After Completion: Single final milestone
- Time & Material: Hourly tracking per milestone

### **7. Undo Window Protection (Issue #3)**
- Customer can still undo during plan preparation
- 24-hour window provides safety net
- Prevents commitment regret

### **8. Version Control**
- All plan versions saved
- Can compare versions
- Audit trail for disputes
- Transparency in negotiations

### **9. Fair Deadlines**
- Company: 48 hours to submit plan
- Customer: Reasonable time to review
- Automatic reminders prevent delays

### **10. Prevents Misunderstandings**
- Everything documented upfront
- Clear deliverables defined
- No surprises during project
- Reference point for disputes

---

**This comprehensive two-stage process ensures proper planning, transparency, and agreement before work begins, while integrating seamlessly with all other system features!** 🏗️✅
