# 🔄 **COMPLETE DETAILED FLOW - ALL 6 SOLUTIONS INTEGRATED**

## 📖 **Purpose of This Document**

This document provides a **step-by-step detailed flow** of the complete FixLanka quotation-to-project-completion process, showing how all 6 business logic solutions work together at every stage.

**No code included** - Pure business logic and user experience flow.

---

## 🗺️ **VISUAL FLOW MAP - COMPLETE INFORMATION ARCHITECTURE**

```
═══════════════════════════════════════════════════════════════════════════════
                           FIXLANKA PLATFORM FLOW
                    Complete User Journey with All Payment Methods
═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: REQUEST & QUOTATION                                                │
│ Duration: 1-7 days | Actors: Customer + Companies                           │
└─────────────────────────────────────────────────────────────────────────────┘
                                      │
                    ┌─────────────────┴─────────────────┐
                    │                                   │
              [CUSTOMER]                          [COMPANIES]
                    │                                   │
       ┌────────────┴────────────┐                     │
       │ Post Service Request    │                     │
       │ • Category selection    │                     │
       │ • Description           │                     │
       │ • Location              │                     │
       │ • Timeline              │                     │
       │ • Budget estimate       │                     │
       │ • Photos (optional)     │                     │
       └────────────┬────────────┘                     │
                    │                                   │
                    └──────────[REQUEST POSTED]─────────┤
                                                        │
                                        ┌───────────────┴───────────────┐
                                        │ View Available Requests       │
                                        │ • Browse by category          │
                                        │ • Filter by location          │
                                        │ • Check requirements          │
                                        └───────────────┬───────────────┘
                                                        │
                                        ┌───────────────┴───────────────┐
                                        │ Prepare Quotation             │
                                        │ • Budget amount               │
                                        │ • Budget type (Issue #1) ────┐
                                        │   ├─ Flexible (±10%)         │
                                        │   └─ Fixed                   │
                                        │ • Payment terms (Issue #2) ──┤
                                        │   ├─ Milestone-based         │
                                        │   ├─ 50-50 Upfront+Final     │
                                        │   ├─ 30-70 Upfront+Final     │
                                        │   ├─ After Completion (100%) │
                                        │   └─ Time & Material         │
                                        │ • Timeline                   │
                                        │ • Work description           │
                                        └───────────────┬───────────────┘
                                                        │
                    ┌──────────[QUOTATIONS SUBMITTED]───┘
                    │
       ┌────────────┴────────────┐
       │ Review Quotations       │
       │ • Compare side-by-side  │
       │ • Check budget types    │
       │ • Check payment terms   │
       │ • View company ratings  │
       │ • Ask questions         │
       └────────────┬────────────┘
                    │
                    ▼

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: QUOTATION ACCEPTANCE (Issue #3)                                    │
│ Duration: 5 minutes | Actor: Customer                                       │
└─────────────────────────────────────────────────────────────────────────────┘
                    │
       ┌────────────┴────────────┐
       │ Select Quotation        │
       │ Click "Accept"          │
       └────────────┬────────────┘
                    │
       ┌────────────┴────────────┐
       │ Confirmation Dialog     │
       │ • Review details        │
       │ • 24hr undo explained   │
       │ ☑ Checkbox required     │
       └────────────┬────────────┘
                    │
       ┌────────────┴────────────┐
       │ CONFIRM ACCEPTANCE      │
       └────────────┬────────────┘
                    │
                    ├─────────────────────────────────────────────────────────┐
                    │                                                         │
                    ▼                                                         │
       ┌────────────────────────┐                                            │
       │ System Actions:        │                                            │
       │ 1. Create contract     │                                            │
       │ 2. Start 24hr undo ⏰  │                                            │
       │ 3. Create chat (Issue#4)│                                           │
       │ 4. Send notifications  │                                            │
       └────────────┬───────────┘                                            │
                    │                                                         │
                    ▼                                                         │
                                                                              │
═══════════════════════════════════════════════════════════════════════════════
                                                                              │
┌─────────────────────────────────────────────────────────────────────────────┤
│ PHASE 3: CONTRACT CREATION & VISIBILITY (Issues #1, #2, #5)                 │
│ Duration: Instant | System automatic                                        │
└─────────────────────────────────────────────────────────────────────────────┘
                    │                                                         │
       ┌────────────┴────────────┐                                            │
       │ Contract Created        │                                            │
       │ • ID: CNT-2026-XXX     │                                            │
       │ • Budget (Issue #1)    │                                            │
       │ • Payment (Issue #2)   │                                            │
       │ • Timeline             │                                            │
       └────────────┬────────────┘                                            │
                    │                                                         │
       ┌────────────┴────────────────────────────┐                           │
       │ Visible in 5 Locations (Issue #5):      │                           │
       │ 1. My Contracts Tab                     │                           │
       │ 2. Original Request Page                │                           │
       │ 3. Notification Bell                    │                           │
       │ 4. Email Links                          │                           │
       │ 5. Dashboard Widget                     │                           │
       └────────────┬────────────────────────────┘                           │
                    │                                                         │
                    ├─── 24hr Undo Available (Issue #3) ──────────────────────┤
                    │                                                         │
                    ▼                                                         │
                                                                              │
      ═══ FLOW SPLITS BASED ON PAYMENT METHOD (Issue #2) ═══                 │
                    │                                                         │
      ┌─────────────┼─────────────┬─────────────┬─────────────┐             │
      │             │             │             │             │             │
      ▼             ▼             ▼             ▼             ▼             │
                                                                              │
[MILESTONE]   [50-50 UP+F]  [30-70 UP+F]  [100% FINAL]  [TIME & MAT]       │
                                                                              │
═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 🔀 FLOW BRANCH 1: MILESTONE-BASED PAYMENT                                   │
│ (Most Complex - Detailed Project Control)                                   │
└─────────────────────────────────────────────────────────────────────────────┘
      │
      ├─ PHASE 4: Milestone Plan Submission (Issue #6)
      │  │ Duration: 48 hours | Actor: Company
      │  │
      │  ├─ Company receives "Action Required"
      │  ├─ Company has 48hr deadline
      │  ├─ Company fills milestone planning form:
      │  │  • 3-5 milestones recommended
      │  │  • Payment per milestone
      │  │  • Duration per milestone
      │  │  • Deliverables per milestone
      │  │  • Dependencies
      │  ├─ System validates:
      │  │  • Budget matches (Issue #1)
      │  │  • Timeline fits
      │  │  • Payment terms match (Issue #2)
      │  ├─ Company submits plan
      │  └─ Chat activates (Issue #4) 💬
      │
      ├─ PHASE 5: Customer Reviews Plan (Issues #4, #6)
      │  │ Duration: 24-72 hours | Actor: Customer
      │  │
      │  ├─ Customer receives notification
      │  ├─ Customer views milestone plan
      │  ├─ Chat available for discussion 💬
      │  ├─ Customer decides:
      │  │  ├─[A] APPROVE ✅
      │  │  ├─[B] REQUEST CHANGES 📝
      │  │  └─[C] REJECT ❌
      │  └─ If approved → Continue
      │
      ├─ PHASE 6: Contract Activation (Issue #6)
      │  │ Duration: Instant | System automatic
      │  │
      │  ├─ Contract status: "Active" ✅
      │  ├─ Project record created
      │  ├─ Milestone 1 becomes active
      │  ├─ Notifications sent
      │  └─ Work can begin
      │
      ├─ PHASE 7: Project Execution (All Issues)
      │  │ Duration: Project timeline (e.g., 20 days)
      │  │
      │  ├─ For each milestone:
      │  │  │
      │  │  ├─ Company works on milestone
      │  │  ├─ Daily updates via chat 💬 (Issue #4)
      │  │  ├─ Company marks milestone complete
      │  │  ├─ Customer reviews deliverables
      │  │  ├─ Customer approves milestone
      │  │  ├─ Payment released automatically 💰
      │  │  │  └─ (From escrow after approval)
      │  │  └─ Next milestone activates
      │  │
      │  ├─ Budget adjustments possible (Issue #1)
      │  │  └─ If flexible budget: ±10% adjustments
      │  │
      │  ├─ Progress visible in 5 locations (Issue #5)
      │  │
      │  └─ All 4 milestones completed → Phase 8
      │
      └─ PHASE 8: Project Completion
         │ Duration: 1-2 days
         │
         ├─ Final milestone approved
         ├─ Final payment released 💰
         ├─ Customer rates company ⭐
         ├─ Project archived
         ├─ Documents available
         └─ Warranty tracking (if applicable)

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 🔀 FLOW BRANCH 2: UPFRONT + FINAL PAYMENT (50-50 or 30-70)                  │
│ (Simplified - Two Payment Points with Escrow Protection)                    │
└─────────────────────────────────────────────────────────────────────────────┘
      │
      ├─ PHASE 4-50: Upfront Payment Required
      │  │ Duration: Immediate | Actor: Customer
      │  │
      │  ├─ Customer sees payment request
      │  ├─ 🔒 Escrow protection explained
      │  ├─ Payment methods shown
      │  ├─ Customer pays upfront:
      │  │  • 50-50: LKR 25,000 (50%)
      │  │  • 30-70: LKR 15,000 (30%)
      │  ├─ Payment goes to ESCROW 🔒
      │  │  └─ NOT to company yet!
      │  ├─ Chat available immediately 💬 (Issue #4)
      │  └─ Contract status: "Pending Work Start"
      │
      ├─ PHASE 5-50: Work Start Verification (Escrow Release)
      │  │ Duration: Start date | Actors: Company + Customer
      │  │
      │  ├─ Scheduled start date arrives
      │  ├─ Company arrives on-site
      │  ├─ Company submits "Work Started" proof:
      │  │  • On-site photo 📷
      │  │  • Start date confirmation
      │  │  • Location verification
      │  │  • Timestamp verification
      │  ├─ Customer receives verification request
      │  ├─ Customer has 24 hours to verify:
      │  │  ├─[A] CONFIRM WORK STARTED ✅
      │  │  │   └─ Upfront payment released from escrow 💰
      │  │  ├─[B] REPORT ISSUE ❌
      │  │  │   └─ Payment stays in escrow, refund processed
      │  │  └─[C] NO RESPONSE
      │  │      └─ Auto-release after 24 hours
      │  └─ Contract status: "Active - Work in Progress"
      │
      ├─ PHASE 6-50: Work Execution (Simplified)
      │  │ Duration: Project timeline (e.g., 20 days)
      │  │
      │  ├─ Company works on project
      │  ├─ Progress updates via chat 💬 (Issue #4)
      │  ├─ Photos shared regularly 📷
      │  ├─ Customer monitors progress
      │  ├─ No milestone approvals needed
      │  ├─ Budget adjustments possible (Issue #1)
      │  │  └─ If flexible: ±10% via chat
      │  └─ Company completes work → Phase 7
      │
      └─ PHASE 7-50: Final Payment & Completion
         │ Duration: 1-2 days
         │
         ├─ Company marks work complete
         ├─ Customer receives notification
         ├─ Customer reviews completed work
         ├─ Customer inspects quality
         ├─ Customer decides:
         │  ├─[A] APPROVE & PAY ✅
         │  │   • Final payment (50% or 70%)
         │  │   • Goes to escrow 🔒
         │  │   • Released after approval
         │  ├─[B] REQUEST FIXES 🔧
         │  │   └─ Payment on hold until fixed
         │  └─[C] DISCUSS VIA CHAT 💬
         ├─ Customer makes final payment
         ├─ Final payment held in escrow briefly
         ├─ Customer approves quality
         ├─ Final payment released 💰
         ├─ Customer rates company ⭐
         └─ Project complete

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 🔀 FLOW BRANCH 3: AFTER COMPLETION PAYMENT (100% FINAL)                     │
│ (Simplest - Zero Upfront Risk with 7-Day Guarantee)                         │
└─────────────────────────────────────────────────────────────────────────────┘
      │
      ├─ PHASE 4-100: Contract Activation (No Payment)
      │  │ Duration: Instant
      │  │
      │  ├─ NO upfront payment required ✅
      │  ├─ Contract immediately active
      │  ├─ Chat available immediately 💬 (Issue #4)
      │  ├─ 7-day guarantee explained 🔒
      │  └─ Work scheduled to start
      │
      ├─ PHASE 5-100: Work Execution (Full Trust)
      │  │ Duration: Project timeline (e.g., 20 days)
      │  │
      │  ├─ Company works entirely on trust
      │  ├─ Progress updates via chat 💬
      │  ├─ Customer monitors progress
      │  ├─ No payments during work
      │  ├─ Budget adjustments possible (Issue #1)
      │  └─ Company completes work → Phase 6
      │
      └─ PHASE 6-100: Payment with 7-Day Guarantee
         │ Duration: 7 days + inspection
         │
         ├─ Company marks work complete
         ├─ Customer receives notification
         ├─ Customer inspects work
         ├─ Customer makes FULL payment (100%)
         │  └─ Goes to ESCROW 🔒 (NOT to company)
         │
         ├─ 🔒 7-DAY QUALITY GUARANTEE PERIOD 🔒
         │  │ Payment held in escrow for 7 days
         │  │
         │  ├─ Customer can:
         │  │  • Inspect thoroughly
         │  │  • Test functionality
         │  │  • Request fixes (free) 🔧
         │  │  • Verify deliverables
         │  │  • Check documentation
         │  │
         │  ├─ Customer decides within 7 days:
         │  │  ├─[A] APPROVE ✅
         │  │  │   └─ Payment released from escrow 💰
         │  │  ├─[B] REQUEST FIXES 🔧
         │  │  │   ├─ Payment stays in escrow
         │  │  │   ├─ Company fixes (no charge)
         │  │  │   └─ 7-day period extended
         │  │  ├─[C] RAISE DISPUTE ⚠️
         │  │  │   ├─ FixLanka mediates
         │  │  │   └─ Partial/full refund possible
         │  │  └─[D] NO ACTION
         │  │      └─ Auto-release after 7 days
         │  │
         │  └─ Maximum customer protection!
         │
         ├─ Payment released from escrow 💰
         ├─ Customer rates company ⭐
         └─ Project complete

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 🔀 FLOW BRANCH 4: TIME & MATERIAL PAYMENT                                   │
│ (Flexible - Hourly/Daily Billing with Time Tracking)                        │
└─────────────────────────────────────────────────────────────────────────────┘
      │
      ├─ PHASE 4-TM: Contract Setup
      │  │ Duration: Instant
      │  │
      │  ├─ NO upfront payment
      │  ├─ Hourly/daily rate defined
      │  ├─ Spending cap set (Issue #1)
      │  │  └─ Example: LKR 60,000 max (120% of estimate)
      │  ├─ Time tracking enabled ⏱️
      │  ├─ Chat available immediately 💬
      │  └─ Contract immediately active
      │
      ├─ PHASE 5-TM: Work with Time Tracking
      │  │ Duration: Variable (until project complete)
      │  │
      │  ├─ Company clocks in/out daily ⏱️
      │  ├─ Time logs visible to customer in real-time
      │  │  • Date and hours
      │  │  • Tasks performed
      │  │  • On-site photos 📷
      │  │  • Location verification
      │  ├─ Material purchases tracked
      │  │  • Receipts required 📄
      │  │  • 10% markup shown
      │  ├─ Customer monitors daily:
      │  │  • Hours worked today
      │  │  • Total hours this week
      │  │  • Running cost total
      │  │  • Spending vs cap
      │  ├─ Chat for questions 💬
      │  └─ Weekly invoicing → Phase 6
      │
      ├─ PHASE 6-TM: Weekly Invoicing
      │  │ Duration: Every Friday
      │  │
      │  ├─ Company generates weekly invoice:
      │  │  • Total hours × rate
      │  │  • Materials + markup
      │  │  • Detailed time log attached
      │  │  • All receipts attached
      │  ├─ Customer receives invoice
      │  ├─ Customer reviews:
      │  │  • Verify hours accurate
      │  │  • Check material costs
      │  │  • Review receipts
      │  ├─ Customer decides:
      │  │  ├─[A] APPROVE & PAY ✅
      │  │  │   └─ Payment within 3 days
      │  │  ├─[B] DISPUTE HOURS/COSTS ⚠️
      │  │  │   └─ Discuss via chat or mediation
      │  │  └─[C] REQUEST CLARIFICATION 💬
      │  ├─ Spending cap monitoring:
      │  │  └─ If approaching cap: Company must pause
      │  └─ Next week continues...
      │
      └─ PHASE 7-TM: Project Completion
         │ Duration: 1-2 days
         │
         ├─ Company completes work
         ├─ Final invoice generated
         ├─ Customer reviews final invoice
         ├─ Customer pays final invoice
         ├─ Customer rates company ⭐
         └─ Project complete

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 🔄 CROSS-CUTTING FEATURES (Available in ALL Flow Branches)                  │
└─────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Issue #3: 24-Hour Undo Window                                            │
├──────────────────────────────────────────────────────────────────────────┤
│ • Active for first 24 hours after acceptance                             │
│ • Countdown timer visible everywhere                                     │
│ • Can undo at any time before deadline                                   │
│ • Full refund if payment made                                            │
│ • Contract cancelled, request reopened                                   │
│ • 1-hour reminder before deadline                                        │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Issue #4: In-App Messaging (Chat)                                        │
├──────────────────────────────────────────────────────────────────────────┤
│ Activation:                                                               │
│ • Milestone-based: After plan submitted                                  │
│ • Other methods: Immediately after acceptance                            │
│                                                                           │
│ Features:                                                                 │
│ • Text messaging                                                          │
│ • Photo sharing 📷                                                        │
│ • File attachments 📎                                                     │
│ • Location pins 📍                                                        │
│ • Emoji reactions 😊                                                      │
│ • Message history preserved                                               │
│ • No phone numbers shared (privacy)                                      │
│                                                                           │
│ Access Points (5):                                                        │
│ 1. Contract page - main chat button                                      │
│ 2. Notification bell - chat notifications                                │
│ 3. Dashboard widget - quick chat access                                  │
│ 4. Email links - "Reply via Chat" button                                 │
│ 5. Mobile app - dedicated chat tab                                       │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Issue #5: Contract Visibility (5 Locations)                              │
├──────────────────────────────────────────────────────────────────────────┤
│ Contract accessible from:                                                 │
│                                                                           │
│ 1. My Contracts Tab                                                      │
│    • Main location                                                        │
│    • Full list view                                                       │
│    • Filter and search                                                    │
│                                                                           │
│ 2. Original Request Page                                                 │
│    • Journey view                                                         │
│    • See how request became contract                                     │
│                                                                           │
│ 3. Notification Bell                                                     │
│    • Quick access from alerts                                            │
│    • Direct links in notifications                                       │
│                                                                           │
│ 4. Email Links                                                           │
│    • Every email has contract link                                       │
│    • Deep links to specific sections                                     │
│                                                                           │
│ 5. Dashboard Widgets                                                     │
│    • At-a-glance view                                                     │
│    • Quick actions available                                             │
│                                                                           │
│ All locations show:                                                       │
│ • Real-time contract status                                              │
│ • Live progress updates                                                   │
│ • Payment status                                                          │
│ • Chat availability 💬                                                    │
│ • Countdown timers (undo, deadlines)                                     │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Issue #1: Budget Flexibility                                             │
├──────────────────────────────────────────────────────────────────────────┤
│ Flexible Budget (±10%):                                                   │
│ • Chosen during quotation                                                │
│ • Allows cost adjustments                                                │
│ • Customer approval required                                             │
│ • Useful for material price changes                                      │
│ • Example: LKR 50,000 → LKR 45k-55k range                               │
│                                                                           │
│ Fixed Budget:                                                             │
│ • No adjustments allowed                                                 │
│ • Total cost locked                                                       │
│ • Company absorbs any overruns                                           │
│ • Customer protected from surprises                                      │
│                                                                           │
│ Time & Material Exception:                                               │
│ • No fixed budget                                                        │
│ • Spending cap instead (e.g., 120% of estimate)                         │
│ • Variable final cost                                                     │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ Issue #2: Payment Terms (5 Methods)                                      │
├──────────────────────────────────────────────────────────────────────────┤
│ Comparison:                                                               │
│                                                                           │
│ 1. Milestone-Based        → Most control, most complex                   │
│ 2. 50-50 Upfront+Final    → Balanced, escrow protected                  │
│ 3. 30-70 Upfront+Final    → Lower risk, escrow protected                │
│ 4. After Completion       → Zero risk, 7-day guarantee                   │
│ 5. Time & Material        → Flexible scope, weekly billing               │
│                                                                           │
│ All methods (except T&M) have escrow protection 🔒                       │
└──────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────────────────┐
│ 🔒 Escrow Protection System                                              │
├──────────────────────────────────────────────────────────────────────────┤
│ Upfront Payments:                                                         │
│ • Go to FixLanka escrow (not company)                                    │
│ • Held until work starts                                                 │
│ • Company submits start proof with photo 📷                              │
│ • Customer verifies (24hr window)                                        │
│ • Released after verification                                            │
│                                                                           │
│ Milestone/Final Payments:                                                │
│ • Also held in escrow                                                    │
│ • Held until customer approves quality                                   │
│ • Customer inspects work first                                           │
│ • Approve when satisfied                                                 │
│ • Payment released to company                                            │
│                                                                           │
│ 100% Final Special Protection:                                           │
│ • 7-day quality guarantee period                                         │
│ • Extended inspection time                                               │
│ • Free fixes if issues found                                             │
│ • Escrow extended until resolved                                         │
│                                                                           │
│ Benefits:                                                                 │
│ ✅ Customer: Protected from scams                                        │
│ ✅ Company: Guaranteed payment when work done                            │
│ ✅ Platform: Trust and safety for all                                    │
└──────────────────────────────────────────────────────────────────────────┘

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ 📊 DECISION POINTS SUMMARY                                                  │
└─────────────────────────────────────────────────────────────────────────────┘

CUSTOMER DECISIONS:
├─ Phase 1: Choose which quotation to accept
├─ Phase 2: Confirm acceptance (with 24hr undo option)
├─ Phase 3: Choose to undo within 24 hours (optional)
│
├─ MILESTONE-BASED PATH:
│  ├─ Phase 5: Approve/Change/Reject milestone plan
│  ├─ Phase 7: Approve each milestone (4× decisions)
│  └─ Phase 8: Rate company
│
├─ UPFRONT+FINAL PATH:
│  ├─ Phase 4: Make upfront payment
│  ├─ Phase 5: Verify work started (24hr window)
│  ├─ Phase 7: Approve completed work & pay final
│  └─ Phase 7: Rate company
│
├─ AFTER COMPLETION PATH:
│  ├─ Phase 6: Make payment (goes to escrow)
│  ├─ Phase 6: Inspect for 7 days
│  ├─ Phase 6: Approve payment release
│  └─ Phase 6: Rate company
│
└─ TIME & MATERIAL PATH:
   ├─ Phase 6: Approve/dispute weekly invoices
   ├─ Phase 7: Approve final invoice
   └─ Phase 7: Rate company

COMPANY DECISIONS:
├─ Phase 1: Whether to send quotation
├─ Phase 1: Choose payment method to offer
│
├─ MILESTONE-BASED PATH:
│  ├─ Phase 4: Design milestone plan (3-5 milestones)
│  ├─ Phase 5: Accept/negotiate customer changes
│  └─ Phase 7: Mark each milestone complete (4× actions)
│
└─ ALL OTHER PATHS:
   ├─ Phase 5: Submit work-started proof
   └─ Phase 6/7: Mark work complete

═══════════════════════════════════════════════════════════════════════════════

┌─────────────────────────────────────────────────────────────────────────────┐
│ ⏱️ TIMELINE COMPARISON                                                      │
└─────────────────────────────────────────────────────────────────────────────┘

MILESTONE-BASED:
Day -7 to 0:  Request & Quotation (Phase 1-2)
Day 0:        Acceptance & Contract Creation (Phase 3)
Day 0-2:      Company submits plan (Phase 4)
Day 2-3:      Customer reviews plan (Phase 5)
Day 3:        Contract activation (Phase 6)
Day 4-20:     Work execution with 4 approval points (Phase 7)
Day 20-21:    Final rating & completion (Phase 8)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total: ~28 days (request to completion)

UPFRONT + FINAL (50-50 or 30-70):
Day -7 to 0:  Request & Quotation (Phase 1-2)
Day 0:        Acceptance, payment, escrow hold (Phase 3-4)
Day 4:        Work starts, escrow released (Phase 5)
Day 4-20:     Work execution (Phase 6)
Day 20-21:    Final payment, approval, rating (Phase 7)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total: ~28 days (request to completion)

AFTER COMPLETION (100%):
Day -7 to 0:  Request & Quotation (Phase 1-2)
Day 0:        Acceptance, immediate activation (Phase 3-4)
Day 4:        Work starts (Phase 5)
Day 4-20:     Work execution (Phase 5)
Day 20:       Payment to escrow (Phase 6)
Day 20-27:    7-day quality guarantee (Phase 6)
Day 27:       Release payment, rating (Phase 6)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total: ~34 days (request to completion)

TIME & MATERIAL:
Day -7 to 0:  Request & Quotation (Phase 1-2)
Day 0:        Acceptance, immediate activation (Phase 3-4)
Day 4-?:      Work with daily time tracking (Phase 5)
Every Friday: Weekly invoice & payment (Phase 6)
Day ?:        Final invoice, rating (Phase 7)
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total: Variable (depends on project scope)

═══════════════════════════════════════════════════════════════════════════════

END OF VISUAL FLOW MAP
═══════════════════════════════════════════════════════════════════════════════
```

---

## 🎯 **The Complete Journey Overview**

```
PHASE 1: Request & Quotation
├─ Customer posts service request
├─ Companies view and send quotations
└─ Quotations include budget type and payment terms

PHASE 2: Quotation Acceptance (Issue #3)
├─ Customer reviews quotation
├─ Customer confirms acceptance
├─ 24-hour undo window begins
└─ Success notification with all details

PHASE 3: Basic Contract Auto-Creation (Issues #1, #2, #5)
├─ System creates contract record automatically
├─ Contract includes budget and payment info
├─ Contract becomes visible in 5 locations
├─ Status: "Pending Milestone Plan"
└─ Chat room created but inactive

PHASE 4: Milestone Plan Submission (Issue #6)
├─ Company receives "Action Required" alert
├─ Company has 48 hours to submit plan
├─ Company fills milestone planning form
├─ System validates plan against budget/timeline
└─ Company submits plan

PHASE 5: Chat Opens & Customer Reviews (Issues #4, #6)
├─ Chat automatically becomes active
├─ Customer receives notification to review
├─ Customer can ask questions via chat
├─ Customer makes decision (approve/change/reject)
└─ Negotiation possible via chat

PHASE 6: Contract Activation (Issue #6)
├─ Customer approves milestone plan
├─ Contract status changes to "Active"
├─ Project record created
├─ First milestone becomes active
└─ Work can officially begin

PHASE 7: Project Execution (All Issues)
├─ Company works on milestones
├─ Updates shared via chat
├─ Milestones completed and approved
├─ Payments released automatically
└─ Budget adjustments if needed

PHASE 8: Project Completion
├─ All milestones completed
├─ Final payment released
├─ Customer rates company
└─ Contract archived
```

---

## 📋 **DETAILED FLOW - EVERY STEP EXPLAINED**

---

# **PHASE 1: REQUEST & QUOTATION**

## **Step 1.1: Customer Posts Service Request**

### **What Happens:**
Customer fills out a service request form describing their needs.

### **Information Customer Provides:**
- Service category (Plumbing, Electrical, Construction, etc.)
- Detailed description of work needed
- Location (address or area)
- Preferred timeline
- Budget range (optional estimate)
- Photos of the issue (optional)
- Urgency level (normal, urgent, emergency)

### **What System Does:**
- Creates service request record in database
- Assigns unique request ID (e.g., REQ-2026-1234)
- Sets status to "Open for Quotes"
- Notifies relevant companies in the area
- Shows request in public marketplace (if customer chose public)

### **Notifications Sent:**
- **To Customer:** Email confirmation with request ID
- **To Companies:** In-app notification about new request in their area

---

## **Step 1.2: Companies View Request**

### **What Happens:**
Companies browse available requests matching their expertise.

### **What Companies See:**
- Request description and requirements
- Customer location (area, not exact address yet)
- Preferred timeline
- Budget range (if provided)
- Number of quotes already submitted
- Photos (if uploaded)

### **What Companies Can Do:**
- View full request details
- Save to favorites for later
- Send quotation
- Skip and move to next request

---

## **Step 1.3: Company Prepares Quotation**

### **What Happens:**
Company decides to quote and fills quotation form.

### **Information Company Provides:**

**1. Budget Information (Issue #1 Integration):**
- Total estimated budget amount
- Choose budget type:
  - **OPTION A: Flexible Budget**
    - Set flexibility percentage (5%, 10%, 15%)
    - System calculates min/max range automatically
    - Example: LKR 50,000 ±10% = Range: 45,000 - 55,000
  - **OPTION B: Fixed Budget**
    - Exact amount, no flexibility
    - No adjustments allowed later
    - Example: LKR 50,000 (exactly)

**2. Payment Terms (Issue #2 Integration):**
- Choose payment method:
  - **OPTION A: Milestone-based**
    - Payments released per milestone completion
    - Detailed breakdown comes later in milestone plan
  - **OPTION B: Upfront + Final**
    - Percentage upfront (typically 30-50%)
    - Remainder on completion
  - **OPTION C: After Completion**
    - Full payment after project done
    - Usually for small, quick jobs
  - **OPTION D: Time & Material**
    - Hourly or daily rate
    - Final cost depends on actual time spent

**3. Pricing Type:**
- Fixed price (total amount known)
- Time & Material (rate × time)
- Unit price (price per unit of work)

**4. Timeline:**
- Estimated start date
- Estimated completion date
- Total duration in days

**5. Description:**
- Detailed work scope
- Materials included/excluded
- Warranty information
- Special conditions or notes

**6. Attachments (Optional):**
- Work samples
- Material specifications
- Company certifications
- Previous project photos

### **What System Does When Quotation Submitted:**
- Validates all required fields filled
- Checks budget and payment terms make sense
- Assigns quotation ID (e.g., QUO-2026-5678)
- Sets status to "Pending Customer Review"
- Notifies customer about new quotation

### **Notifications Sent:**
- **To Customer:** Email + SMS + In-app notification
- **To Company:** Confirmation that quotation sent

---

## **Step 1.4: Customer Reviews Quotations**

### **What Happens:**
Customer receives notifications and reviews all quotations received.

### **What Customer Sees for Each Quotation:**

**Company Information:**
- Company name and logo
- Rating (stars out of 5)
- Number of completed projects
- Verification status (verified/unverified)
- License number (if applicable)
- Reviews from other customers

**Budget Details:**
- Total budget amount
- Budget type clearly labeled:
  - "Flexible (±10%)" with range shown
  - "Fixed (No adjustments)"
- Visual indicator (flexible = green, fixed = orange)

**Payment Terms:**
- Payment method name (Milestone, Upfront+Final, etc.)
- Brief explanation of how payment works
- Example payment schedule (if milestone-based)

**Timeline:**
- Start date
- End date
- Duration (e.g., "20 days")
- Comparison with other quotes (faster/slower)

**Work Description:**
- Detailed scope of work
- What's included
- What's excluded
- Materials covered
- Warranty terms

**Attachments:**
- View company's work samples
- Check certifications
- See material specifications

### **Actions Customer Can Take:**
- View full quotation details
- Compare with other quotations side-by-side
- Ask questions (message company)
- Save to favorites
- Accept quotation
- Reject quotation
- Request clarification

### **Comparison Features:**
- Side-by-side comparison table
- Sort by price (low to high, high to low)
- Sort by rating
- Sort by timeline (fastest to slowest)
- Filter by budget type (flexible only, fixed only)
- Filter by payment terms

---

# **PHASE 2: QUOTATION ACCEPTANCE** (Issue #3)

## **Step 2.1: Customer Initiates Acceptance**

### **What Happens:**
Customer finds a quotation they like and clicks "Accept Quotation" button.

### **What Customer Sees - Confirmation Dialog:**

**Dialog Header:**
- "Accept This Quotation?"
- Warning icon indicating this is important decision

**Quotation Summary Display:**
- Company name and logo
- Total budget with type clearly marked
- Payment terms method
- Timeline (start - end dates)
- Project title

**Important Reminders:**
- "Review all details carefully before accepting"
- "You'll have 24 hours to undo this acceptance if you change your mind"
- "Contract will be created immediately"
- "Company will add detailed milestone plan within 48 hours"

**What Happens After Acceptance - Explained:**
1. Contract created automatically with basic information
2. You can undo acceptance within 24 hours (countdown timer starts)
3. Company will submit detailed milestone plan within 48 hours
4. You'll review and approve the milestone plan
5. Chat will open after company submits plan
6. Work begins after you approve the plan

**Confirmation Checkbox:**
- ☐ "I have carefully reviewed this quotation and agree to accept it"
- This checkbox MUST be checked before proceeding

**Buttons:**
- [Cancel] - Gray button, returns to quotation view
- [CONFIRM ACCEPTANCE] - Green button, only active when checkbox checked

---

## **Step 2.2: Customer Confirms Acceptance**

### **What Happens:**
Customer checks the confirmation checkbox and clicks "CONFIRM ACCEPTANCE" button.

### **What System Does Immediately (in order):**

**1. Update Quotation Status:**
- Changes quotation status from "Pending" to "Accepted"
- Records acceptance timestamp
- Records which user accepted

**2. Update Other Quotations:**
- All other quotations for same request change to "Rejected"
- System logs reason: "Customer accepted different quotation"

**3. Update Request Status:**
- Request status changes from "Open" to "Contract Created"
- Request no longer appears in public marketplace

**4. Create Basic Contract Record:**
- Generates unique contract ID (CNT-2026-001)
- Copies all information from quotation:
  - Budget amount and type (flexible/fixed)
  - Budget min/max if flexible
  - Payment terms and pricing type
  - Timeline (start date, end date, duration)
  - Project title and description
  - Customer information
  - Company information
- Sets contract status: "pending_milestone_plan"
- Records contract creation timestamp
- Sets undo deadline: Current time + 24 hours
- Sets milestone plan deadline: Current time + 48 hours

**5. Create Chat Room (Inactive):**
- Creates chat room record
- Links to contract
- Links customer and company
- Sets status: "inactive" (won't open until milestone plan submitted)

**6. Create Notification Records:**
- Customer notification: "Quotation accepted! Contract created."
- Company notification: "Customer accepted! Add milestone plan."

**7. Send Email to Customer:**
- Subject: "Quotation Accepted - Contract #CNT-2026-001 Created"
- Contract details summary
- Link to view contract
- 24-hour undo information
- What happens next explanation

**8. Send Email to Company:**
- Subject: "Customer Accepted Your Quotation! Action Required"
- Contract details
- Deadline to submit milestone plan (48 hours)
- Link to add milestone plan
- Instructions

**9. Send SMS Notifications:**
- Customer: "Quotation accepted! Contract CNT-2026-001 created. Check email."
- Company: "Customer accepted! Add plan within 48hrs. Check email."

**10. Create Audit Log Entries:**
- Log quotation acceptance action
- Log contract creation
- Log all status changes
- Timestamp everything

---

## **Step 2.3: Success Dialog Displayed**

### **What Happens:**
After system processing (takes 2-3 seconds), success dialog appears.

### **What Customer Sees - Success Dialog:**

**Header:**
- ✅ Big green checkmark icon
- "Quotation Accepted Successfully!"
- "Contract Created: #CNT-2026-001"

**Three-Stage Process Explained:**

**STAGE 1: Contract Created ✅ (Complete)**
- Basic contract created with quotation details
- Status: Pending Milestone Plan
- Visible in your account now

**STAGE 2: Milestone Plan (In Progress)**
- ABC Construction will add detailed plan within 48 hours
- Plan will break project into phases (milestones)
- You'll receive notification when submitted
- Expected: Within 24-48 hours

**STAGE 3: Review & Activation (Pending)**
- You'll review the milestone plan
- Chat will open for questions/discussion
- You can approve, request changes, or reject
- Work begins after you approve

**Contract Details Box:**

**Budget Information:**
- Total: LKR 50,000
- Type: Flexible (±10%)
- Range: LKR 45,000 - 55,000
- [Learn about flexible budgets →] (Issue #1 info link)

**Payment Terms:**
- Method: Milestone-based
- Details: Payment released per milestone completion
- Breakdown: Will be defined in milestone plan
- [Learn about payment terms →] (Issue #2 info link)

**Timeline:**
- Start: January 15, 2026
- End: February 4, 2026
- Duration: 20 days

**24-Hour Undo Window (Issue #3):**
- ⏰ Countdown timer: 23:59:45 remaining
- Deadline: January 26, 2026 at 2:30 PM
- [⏪ UNDO ACCEPTANCE] - Red button
- "You can reverse this decision within 24 hours"
- "After deadline, changes require company agreement"

**Chat Status (Issue #4):**
- 💬 Currently: Not Available
- Opens: After milestone plan submitted
- Why: Prevents spam during plan preparation
- Features when opened:
  - Text messaging
  - Photo sharing
  - File attachments
  - Location sharing
  - No phone numbers shared (privacy)

**Where to Find Your Contract (Issue #5):**

Contract #CNT-2026-001 is now visible in 5 locations:

1. **My Contracts Tab**
   - Main location for all your contracts
   - Shows status, progress, timers
   - [Go to My Contracts →]

2. **Original Request Page**
   - View from your request: REQ-2026-1234
   - See journey: Request → Quotation → Contract
   - [View Request →]

3. **Notification Bell**
   - Notification about contract creation
   - Click notification to view contract
   - [Open Notifications →]

4. **Email Link**
   - Check your email inbox
   - Click link in confirmation email
   - Opens contract directly

5. **Dashboard Widget**
   - Your dashboard now shows this contract
   - "Pending Actions" section
   - [Go to Dashboard →]

**What Happens Next:**

**Within 48 Hours:**
- ABC Construction will submit milestone plan
- You'll receive email, SMS, and in-app notification
- Chat will open automatically
- You'll be able to review the detailed plan

**Your Actions:**
- Monitor 24-hour undo countdown
- Check email for milestone plan notification
- Review contract details in your account
- Wait for milestone plan submission

**Need Help?**
- [View Contract Details]
- [FAQ About Contracts]
- [Contact Support]

**Buttons:**
- [📋 VIEW CONTRACT NOW] - Primary green button
- [🏠 GO TO DASHBOARD] - Secondary blue button
- [Close] - Small gray button

---

## **Step 2.4: Notifications Delivered**

### **What Happens:**
All stakeholders receive notifications through multiple channels.

### **Customer Receives:**

**Email Notification:**
- **Subject:** "✅ Quotation Accepted - Contract #CNT-2026-001 Created"
- **From:** FixLanka Platform <noreply@fixlanka.lk>
- **Content:**
  - Congratulations message
  - Contract ID prominently displayed
  - Company name and details
  - Budget and payment terms summary
  - Timeline summary
  - 24-hour undo window notice with deadline
  - What happens next (3 stages explained)
  - Links to view contract (all 5 access points)
  - Chat information (opens after plan)
  - Support contact information
- **Visual Design:**
  - Professional header with FixLanka logo
  - Green success banner
  - Contract details in formatted table
  - Clear section headings
  - Mobile-responsive design

**SMS Notification:**
- "✅ Quotation accepted! Contract CNT-2026-001 created. ABC Construction will add plan within 48hrs. 24hr undo available. View: [short-link]"
- Short, concise, with action link

**In-App Notification:**
- Notification badge appears on bell icon
- When clicked, shows:
  - "Quotation Accepted Successfully"
  - Contract #CNT-2026-001
  - Company name
  - "Click to view contract"
  - Timestamp

**Push Notification (if enabled):**
- Mobile/Desktop notification
- "Contract Created: CNT-2026-001"
- "ABC Construction accepted. Add milestone plan within 48hrs."
- Clicking opens contract

### **Company Receives:**

**Email Notification:**
- **Subject:** "🎉 Customer Accepted Your Quotation! Action Required - Contract #CNT-2026-001"
- **From:** FixLanka Platform <noreply@fixlanka.lk>
- **Content:**
  - Congratulations message
  - Contract ID
  - Customer name (John Silva)
  - ⚠️ ACTION REQUIRED banner
  - Deadline: Submit milestone plan within 48 hours
  - Countdown timer
  - Contract details summary
  - What to include in milestone plan:
    - Break project into 3-5 milestones
    - Set payment per milestone
    - Define deliverables
    - Set duration per phase
  - Link to milestone planning form
  - Tips for creating good milestone plans
  - Template links
  - Support contact
- **Call-to-Action:** Big button "ADD MILESTONE PLAN NOW"

**SMS Notification:**
- "🎉 Customer accepted quotation! Contract CNT-2026-001. Add milestone plan within 48hrs. Login: [short-link]"

**In-App Notification:**
- "⚠️ ACTION REQUIRED"
- "Customer Accepted Your Quotation!"
- Contract #CNT-2026-001
- "Submit milestone plan: 47hrs 30mins remaining"
- Red badge indicator

**Dashboard Alert:**
- Prominent banner on company dashboard
- "Pending Action: Add Milestone Plan"
- Contract details
- Countdown timer
- Direct link to planning form

---

# **PHASE 3: BASIC CONTRACT AUTO-CREATION** (Issues #1, #2, #5)

## **Step 3.1: Contract Becomes Visible in All 5 Locations**

### **What Happens:**
Contract record is now accessible from 5 different places in the platform.

---

### **ACCESS POINT 1: My Contracts Tab**

**Where:** Main navigation → My Contracts

**What Customer Sees:**

**Page Layout:**
- Page title: "My Contracts"
- Filter bar at top
- Search box
- Sort options
- List of contract cards

**Filter Options:**
- All Contracts
- Pending Plan (shows CNT-2026-001)
- Pending Approval
- Active
- Completed
- Cancelled

**Search Box:**
- "Search by contract ID, company name, or project..."
- Searches in real-time

**Sort Options:**
- Newest first (default)
- Oldest first
- Budget (high to low)
- Budget (low to high)
- Deadline approaching

**Contract Card Display:**

```
┌────────────────────────────────────────────┐
│ Contract #CNT-2026-001                     │
│ Kitchen Sink Repair                        │
│ Status: ⏳ Pending Milestone Plan          │
├────────────────────────────────────────────┤
│                                            │
│ 🏢 ABC Construction (Pvt) Ltd             │
│ ⭐⭐⭐⭐⭐ 4.8/5.0 | ✅ Verified           │
│                                            │
│ 💰 Budget: LKR 50,000 (Flexible ±10%)    │
│ 💳 Payment: Milestone-based                │
│ 📅 Timeline: 20 days (Jan 15 - Feb 4)    │
│                                            │
│ ⚠️ WAITING FOR MILESTONE PLAN              │
│ Company deadline: 46 hours remaining       │
│                                            │
│ ⏰ 24-HOUR UNDO AVAILABLE                  │
│ Time remaining: 22:15:30                   │
│ [⏪ UNDO ACCEPTANCE]                       │
│                                            │
│ 💬 Chat: Opens after plan submitted        │
│                                            │
│ [VIEW DETAILS] [EMAIL COMPANY]             │
│                                            │
└────────────────────────────────────────────┘
```

**Card Features:**
- Color-coded status bar (orange = pending plan)
- Countdown timers visible (undo + plan deadline)
- Quick action buttons
- Progress indicator (0% - not started)
- Visual badges (verified, flexible budget, etc.)

**Clicking "VIEW DETAILS":**
Opens full contract details page (see Access Point details below)

---

### **ACCESS POINT 2: From Original Request**

**Where:** My Requests → View Request REQ-2026-1234

**What Customer Sees:**

**Request Page Updates:**
- Status banner: "✅ Contract Created"
- Timeline visualization showing progress:
  - Request Posted ✅
  - Quotations Received ✅
  - Quotation Accepted ✅
  - Contract Created ✅ ← Current stage
  - Milestone Plan Submitted ⏳
  - Plan Approved ⏳
  - Project Active ⏳
  - Project Completed ⏳

**Journey Section:**

**Request Details:** (original request info)
- Title, description, photos
- Date posted
- Location

↓ Led to

**Quotations Received:** (5 quotations)
- Quotation #1 (Rejected)
- Quotation #2 (Rejected)
- Quotation #3 ✅ ACCEPTED → Contract #CNT-2026-001
- Quotation #4 (Rejected)
- Quotation #5 (Rejected)

↓ Created

**Contract Information:**
```
┌────────────────────────────────────────────┐
│ 📄 CONTRACT CREATED                        │
│                                            │
│ Contract #CNT-2026-001                     │
│ Status: Pending Milestone Plan             │
│                                            │
│ This quotation from ABC Construction       │
│ was accepted and converted to a contract.  │
│                                            │
│ Budget: LKR 50,000 (Flexible ±10%)        │
│ Payment: Milestone-based                   │
│ Timeline: 20 days                          │
│                                            │
│ ⏰ 24-hour undo: 22:15:30 remaining        │
│                                            │
│ [VIEW CONTRACT DETAILS →]                  │
│ [TRACK CONTRACT STATUS]                    │
│                                            │
└────────────────────────────────────────────┘
```

**Benefits of This View:**
- Customer can see complete journey
- Understand how they got to contract
- Compare with rejected quotations
- Review original request to verify scope

---

### **ACCESS POINT 3: Notification Bell**

**Where:** Top navigation bar → Bell icon (has red badge)

**What Customer Sees When Clicking Bell:**

**Notification Dropdown Panel:**

```
┌────────────────────────────────────────────┐
│ 🔔 Notifications (3 new)                  │
├────────────────────────────────────────────┤
│                                            │
│ 📄 Contract #CNT-2026-001 Created         │
│ Kitchen Sink Repair                        │
│ 5 minutes ago                              │
│ [View Contract →]                          │
│                                            │
├────────────────────────────────────────────┤
│                                            │
│ ✅ Quotation Accepted                      │
│ ABC Construction - Kitchen Sink Repair     │
│ 5 minutes ago                              │
│ [View Details →]                           │
│                                            │
├────────────────────────────────────────────┤
│                                            │
│ 📩 New Quotation Received                  │
│ From: XYZ Plumbing                         │
│ For: Kitchen Sink Repair                   │
│ 2 hours ago                                │
│ [View Quotation →]                         │
│                                            │
├────────────────────────────────────────────┤
│                                            │
│ [View All Notifications]                   │
│ [Mark All as Read]                         │
│                                            │
└────────────────────────────────────────────┘
```

**Notification Details:**
- Most recent at top
- Unread = bold text + dot indicator
- Timestamp (relative: "5 minutes ago")
- Icon per notification type
- Direct action links
- Badge count on bell icon

**Clicking Contract Notification:**
Opens contract details page directly

**Future Notifications Here:**
- Milestone plan submitted
- Chat messages received
- Milestone completed
- Payment released
- Undo deadline approaching (23hr reminder)
- Plan deadline approaching (company)

---

### **ACCESS POINT 4: Email Links**

**Where:** Customer's email inbox

**Email Content (Detailed):**

```
From: FixLanka Platform <noreply@fixlanka.lk>
To: john.silva@email.com
Subject: ✅ Quotation Accepted - Contract #CNT-2026-001 Created

──────────────────────────────────────────────

[FixLanka Logo]

Congratulations, John! 🎉

Your quotation has been accepted and converted to 
a contract.

──────────────────────────────────────────────

CONTRACT DETAILS

Contract ID: #CNT-2026-001
Project: Kitchen Sink Repair
Company: ABC Construction (Pvt) Ltd
Status: Pending Milestone Plan

──────────────────────────────────────────────

BUDGET & PAYMENT

Total Budget: LKR 50,000
Budget Type: Flexible (±10%)
Allowed Range: LKR 45,000 - 55,000

Payment Terms: Milestone-based
Details: Payment will be released as each 
         milestone is completed and approved.

──────────────────────────────────────────────

TIMELINE

Start Date: January 15, 2026
End Date: February 4, 2026
Duration: 20 days

──────────────────────────────────────────────

⚠️ 24-HOUR UNDO WINDOW

You have 24 hours to undo this acceptance if
you change your mind.

Deadline: January 26, 2026 at 2:30 PM
Time Remaining: Approximately 23 hours

[⏪ UNDO ACCEPTANCE NOW]
        ↑ Direct link to undo

After this deadline, any changes will require
agreement with the company.

──────────────────────────────────────────────

WHAT HAPPENS NEXT?

STAGE 1: Contract Created ✅
Your contract has been created with basic
information from the quotation.

STAGE 2: Milestone Plan (In Progress)
ABC Construction will submit a detailed 
milestone plan within 48 hours. This plan will:
• Break project into phases (milestones)
• Define payment per milestone
• List deliverables for each phase
• Set duration for each phase

You'll receive notification when submitted.

STAGE 3: Review & Activation (Pending)
Once the plan is submitted:
• Chat will open for discussion
• You can review the detailed plan
• Ask questions via chat
• Approve, request changes, or reject
• Work begins after you approve

──────────────────────────────────────────────

💬 CHAT SYSTEM

Chat is currently inactive. It will automatically
open after ABC Construction submits the milestone
plan. This prevents spam while they prepare.

Chat Features (when active):
✓ Text messaging
✓ Photo sharing  
✓ File attachments
✓ Location pins
✓ No phone numbers shared (privacy)

──────────────────────────────────────────────

ACCESS YOUR CONTRACT

View and manage your contract from any of these
locations:

1. My Contracts Tab
   [Go to My Contracts →]

2. Dashboard Widget
   [Open Dashboard →]

3. Original Request Page
   [View Request REQ-2026-1234 →]

4. This Email (click below)
   [VIEW CONTRACT DETAILS →]
           ↑ Primary CTA button

──────────────────────────────────────────────

NEED HELP?

• FAQ about contracts
• How flexible budgets work
• Understanding payment terms
• Contact support: support@fixlanka.lk

──────────────────────────────────────────────

Best regards,
The FixLanka Team

──────────────────────────────────────────────

This is an automated message. Please do not reply
to this email. For support, contact us at
support@fixlanka.lk

© 2026 FixLanka Platform. All rights reserved.
```

**Email Features:**
- Clean, professional design
- All key information visible without scrolling
- Multiple CTAs for different actions
- Works on mobile email clients
- Direct links to contract
- Undo link prominently placed
- Educational content (what happens next)

---

### **ACCESS POINT 5: Dashboard Widgets**

**Where:** Main dashboard / Home page after login

**What Customer Sees on Dashboard:**

**Dashboard Layout:**

```
┌─────────────────────────────────────────────┐
│ Welcome back, John Silva! 👋                │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ ⚠️ ACTION REQUIRED (1)                      │
├─────────────────────────────────────────────┤
│                                             │
│ 📋 Review Milestone Plan                    │
│ Contract #CNT-2026-001                      │
│ Waiting for ABC Construction to submit plan │
│ Expected: Within 24-48 hours                │
│                                             │
│ ⏰ 24-hour undo expires in: 22:15:30        │
│ [⏪ UNDO ACCEPTANCE]                        │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 📊 ACTIVE CONTRACTS (1)                     │
├─────────────────────────────────────────────┤
│                                             │
│ Contract #CNT-2026-001                      │
│ Kitchen Sink Repair                         │
│ Status: ⏳ Pending Milestone Plan           │
│ Company: ABC Construction                   │
│ Budget: LKR 50,000 (Flexible ±10%)         │
│                                             │
│ Progress: ████░░░░░░ 0%                     │
│                                             │
│ Next: Waiting for company to add plan       │
│ Deadline: 46 hours remaining                │
│                                             │
│ [VIEW CONTRACT] [EMAIL COMPANY]             │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 📈 RECENT ACTIVITY                          │
├─────────────────────────────────────────────┤
│                                             │
│ • Contract CNT-2026-001 created            │
│   5 minutes ago                             │
│                                             │
│ • Quotation from ABC Construction accepted │
│   5 minutes ago                             │
│                                             │
│ • New quotation received from XYZ Plumbing │
│   2 hours ago                               │
│                                             │
│ [View All Activity →]                       │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ 💰 PAYMENT SUMMARY                          │
├─────────────────────────────────────────────┤
│                                             │
│ Pending Payments: LKR 50,000                │
│ (Will be paid after milestones complete)    │
│                                             │
│ Paid This Month: LKR 0                      │
│ Total Spent: LKR 0                          │
│                                             │
└─────────────────────────────────────────────┘
```

**Widget Features:**
- Real-time updates
- Color-coded statuses
- Countdown timers
- Quick actions
- At-a-glance information
- Progress tracking
- Prioritized by urgency

---

## **Step 3.2: Contract Details Page (Comprehensive View)**

**What Happens When Customer Clicks "View Contract Details":**

Opens full contract details page showing all information and current status.

**Full Contract Details Page:**

```
┌──────────────────────────────────────────────┐
│ [← Back to My Contracts]                     │
└──────────────────────────────────────────────┘

CONTRACT #CNT-2026-001
Kitchen Sink Repair

[Pending Plan] [Active] [Completed] [Cancelled]
    ↑ Orange      Gray     Gray        Gray
   Current status tabs

═══════════════════════════════════════════════

⚠️ CONTRACT NOT YET ACTIVE

Current Status: Pending Milestone Plan

┌─────────────────────────────────────────────┐
│ 📋 WAITING FOR MILESTONE PLAN               │
│                                             │
│ ABC Construction is preparing a detailed    │
│ milestone plan that will include:           │
│                                             │
│ ✓ Work breakdown into milestones           │
│ ✓ Payment schedule per milestone           │
│ ✓ Timeline for each phase                  │
│ ✓ Deliverables description                 │
│ ✓ Dependencies between phases              │
│                                             │
│ 📅 Company Deadline: Jan 27, 2026 at 2:30 PM│
│ ⏰ Time Remaining: 46 hours 15 minutes      │
│                                             │
│ What happens when submitted:                │
│ • You'll receive email, SMS, and in-app     │
│   notification                              │
│ • Chat will open automatically              │
│ • You can review and approve/reject plan    │
│ • Work begins after you approve             │
│                                             │
└─────────────────────────────────────────────┘

═══════════════════════════════════════════════

📊 CONTRACT INFORMATION

🏢 COMPANY DETAILS
   Company: ABC Construction (Pvt) Ltd
   Rating: ⭐⭐⭐⭐⭐ 4.8/5.0 (256 reviews)
   Status: ✅ Verified
   License: #ABC-12345
   Contact: Available after plan approval
   
   [View Company Profile] [See Reviews]

👤 CUSTOMER DETAILS
   Name: John Silva
   Location: Colombo 07
   Contact: Email verified ✅

📋 PROJECT DETAILS
   Title: Kitchen Sink Repair
   Category: Plumbing
   
   Description:
   "Repair and replace kitchen sink with new 
   fixtures. Old sink is leaking and needs 
   complete replacement. Includes new faucet 
   and drain system installation."
   
   Original Request: REQ-2026-1234
   [View Original Request →]

═══════════════════════════════════════════════

💰 BUDGET INFORMATION (Issue #1)

Total Budget: LKR 50,000

Budget Type: 🟢 Flexible (±10%)

What this means:
• Final cost can vary within range
• Allows for material price changes
• Adjustments need customer approval
• Protects both parties from surprises

Allowed Range:
• Minimum: LKR 45,000 (-10%)
• Maximum: LKR 55,000 (+10%)

Budget flexibility helps accommodate:
✓ Material price variations
✓ Additional necessary repairs
✓ Unforeseen complications
✓ Quality upgrades if desired

If costs change:
• Company can request adjustment via chat
• You review and approve/reject
• Must stay within ±10% range
• Changes documented in contract

[Learn More About Flexible Budgets →]

═══════════════════════════════════════════════

💳 PAYMENT INFORMATION (Issue #2)

Payment Terms: Milestone-based

How it works:
• Project divided into milestones
• Payment released per milestone completion
• Company completes milestone → You review → 
  You approve → Payment released automatically
• Ensures work quality before paying

Pricing Type: Fixed Price
• Total cost is fixed (within flexible range)
• Not charged by hour or day
• Price covers entire project scope
• Includes labor and materials

Payment Schedule:
Will be defined in milestone plan (pending)

Example milestone payment breakdown:
• Milestone 1: Material procurement (25%)
• Milestone 2: Main installation (50%)
• Milestone 3: Finishing work (15%)
• Milestone 4: Final inspection (10%)

Benefits of milestone payments:
✓ Pay as work progresses
✓ Verify quality before each payment
✓ No large upfront payment risk
✓ Automatic payment release
✓ Transparent payment tracking

[Learn More About Payment Terms →]

═══════════════════════════════════════════════

📅 TIMELINE

Start Date: January 15, 2026
End Date: February 4, 2026
Total Duration: 20 days

Timeline breakdown:
Will be defined in milestone plan with 
duration per milestone.

Visual Timeline:
[Jan 15]────────[Today: Jan 25]────────[Feb 4]
  Start          Day 10                  End
  
Project Status: Not started (pending plan)

═══════════════════════════════════════════════

💬 CHAT STATUS (Issue #4)

Status: 💤 Chat Currently Inactive

Why chat isn't available yet:
• Prevents spam during plan preparation
• Opens when meaningful discussion needed
• Activates after milestone plan submitted
• Remains active throughout project

Chat will open when:
✅ ABC Construction submits milestone plan
✅ You receive notification to review plan
✅ Discussion becomes necessary

Features when chat opens:
✓ Real-time text messaging
✓ Photo sharing (site photos, work progress)
✓ File attachments (documents, invoices)
✓ Location pins (for site location)
✓ Emoji reactions
✓ Message history preserved
✓ Notifications for new messages

Privacy protection:
• No phone numbers shared
• All communication on platform
• Message history documented
• Secure and encrypted
• Can report abuse

Need to contact company now?
[📧 Send Email] [📞 Request Callback]

[Learn More About Chat System →]

═══════════════════════════════════════════════

⏰ 24-HOUR UNDO WINDOW (Issue #3)

You can still undo this acceptance!

Time Remaining: 22:15:30
     ↓ Live countdown timer
Deadline: January 26, 2026 at 2:30 PM

Why 24 hours?
• Protects against rushed decisions
• Allows time to reconsider
• No penalty for changing mind
• Better than being locked in

What happens if you undo:
✓ Contract will be cancelled
✓ Quotation becomes available again
✓ Other companies can quote again
✓ No charges or penalties
✓ Request reopened for quotes
✓ You can accept different quotation

What happens if you don't undo:
• Undo option expires after deadline
• Contract continues as normal
• Changes need company agreement
• Still can negotiate via chat

┌─────────────────────────────────────────────┐
│                                             │
│         [⏪ UNDO ACCEPTANCE]                │
│              ↑ Red button                   │
│                                             │
│ "I want to cancel this contract and         │
│  reopen the request for new quotations"     │
│                                             │
└─────────────────────────────────────────────┘

Undo deadline reminder:
We'll send you a reminder 1 hour before 
the deadline (Jan 26 at 1:30 PM)

[Learn More About Undo Feature →]

═══════════════════════════════════════════════

📍 WHERE TO ACCESS THIS CONTRACT (Issue #5)

Contract #CNT-2026-001 is accessible from
5 different locations:

1. My Contracts Tab
   • Primary location for all contracts
   • List view with filters and search
   • Shows all statuses and progress
   [Go to My Contracts →]

2. Original Request Page
   • View complete journey
   • Request → Quotations → Contract
   • Compare with other quotes
   [View Request REQ-2026-1234 →]

3. Notification Bell
   • Notification about contract creation
   • Updates about plan submission
   • Chat message notifications
   [Open Notifications →]

4. Email Links
   • Confirmation email sent
   • Click link to view contract
   • Mobile-friendly access
   [Check Your Email]

5. Dashboard Widgets
   • Quick view on home page
   • Pending actions highlighted
   • Progress at a glance
   [Go to Dashboard →]

All locations show same contract information
with real-time updates.

═══════════════════════════════════════════════

📄 CONTRACT DOCUMENTS

Status: Basic contract created
Detailed documents pending milestone plan approval

Available Now:
• Quotation PDF
• Contract summary

Available After Plan Approval:
• Complete contract agreement
• Milestone plan document
• Payment schedule
• Terms and conditions

[Download Quotation PDF]
[Email Documents to Me]

═══════════════════════════════════════════════

🔔 NOTIFICATIONS & REMINDERS

You will be notified about:
✅ Milestone plan submitted (email + SMS + app)
✅ Undo deadline approaching (1 hour before)
✅ Company deadline approaching (company only)
✅ Chat messages received
✅ Milestone completions
✅ Payment releases
✅ Project milestones
✅ Any contract updates

Notification Preferences:
Email: ✅ Enabled
SMS: ✅ Enabled  
Push: ✅ Enabled
[Change Preferences →]

═══════════════════════════════════════════════

📊 NEXT STEPS

What's happening now:
1. ABC Construction is preparing milestone plan
2. Deadline: 46 hours remaining
3. You'll be notified when submitted

What you should do:
• Monitor undo countdown if reconsidering
• Check email for milestone plan notification
• Review this contract information
• Wait for company to submit plan

After plan submitted:
• Chat will open automatically
• You can discuss plan with company
• Review detailed milestone breakdown
• Approve, request changes, or reject
• Work begins after you approve

═══════════════════════════════════════════════

ACTIONS

[💬 Email Company]
[📄 Download Contract Summary]
[🔔 Set Reminder]
[❓ Get Help]
[⏪ UNDO ACCEPTANCE] ← Red button
[📋 Track Status Updates]

═══════════════════════════════════════════════

Need help? [Contact Support] [View FAQ] [Live Chat]
```

**This comprehensive view provides:**
- Complete transparency
- All integration points visible
- Educational content
- Clear next steps
- Multiple action options
- Real-time status updates
- Easy navigation

---

# **PHASE 3B: ALTERNATIVE PAYMENT METHODS** (Issue #2)

## **Important Note About Phase 4-8 Flow Variations**

**The flow shown in Phases 4-8 above assumes MILESTONE-BASED payment terms.**

However, there are **3 OTHER payment methods** available (Issue #2), and each has a **DIFFERENT contract flow**:

1. **Milestone-based** (Detailed in Phases 4-8 above)
2. **Upfront + Final Payment** (50-50 or 30-70 split)
3. **After Completion** (100% at end)
4. **Time & Material** (Hourly/daily billing)

Let's now see how Phases 4-8 differ for each of these other payment methods:

---

## **SCENARIO 2: UPFRONT + FINAL PAYMENT (50-50 or 30-70)**

### **What's Different:**
- **NO milestone plan required**
- Contract activates immediately after quotation acceptance
- Two simple payment stages only
- Much simpler process than milestone-based
- Chat opens immediately after acceptance
- Work begins on scheduled start date
- **🔒 ESCROW PROTECTION:** Upfront payments held by FixLanka until work starts

---

### **🔒 ESCROW SYSTEM FOR UPFRONT PAYMENTS**

**How It Protects Customers:**

When customers make upfront payments (50-50, 30-70, or any upfront amount), the money is **NOT sent directly to the company**. Instead:

1. **Payment goes to FixLanka Escrow Account** (secure holding)
2. **FixLanka holds the money** until company confirms work has started
3. **Company must submit "Work Started" confirmation** with:
   - Start date confirmation
   - On-site photo (proof of presence)
   - Initial work update
4. **Customer receives notification** about work start
5. **Customer has 24 hours** to verify work actually started
6. **After customer confirmation OR 24hr auto-verify**, payment released to company

**Benefits:**
- ✅ Customer protected from companies taking money and not showing up
- ✅ Company motivated to start on time (money held until they do)
- ✅ FixLanka acts as trusted middleman
- ✅ Automatic verification if customer doesn't respond (24hrs)
- ✅ Dispute resolution available if issues arise

---

### **Phase 3 → 4: Contract Creation with 50-50 Payment**

**After customer accepts quotation with "50% Upfront + 50% Final" payment terms:**

### **What System Does (Different from Milestone-based):**

**1. Create Contract with Payment Schedule:**
- Creates contract record
- Status: "pending_upfront_payment" (NOT pending plan)
- Payment schedule auto-generated:
  - Payment 1: 50% (upfront) - Due immediately, **held in escrow**
  - Payment 2: 50% (final) - Due at completion
- No milestone plan needed
- Chat activates immediately
- Escrow protection enabled

**2. Customer Sees Payment Request with Escrow Protection Notice:**

```
┌──────────────────────────────────────────────┐
│ ✅ QUOTATION ACCEPTED - CONTRACT CREATED     │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ 💰 UPFRONT PAYMENT REQUIRED                  │
│                                              │
│ Payment Terms: 50% Upfront + 50% Final       │
│ Total Budget: LKR 50,000                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 YOUR PAYMENT IS PROTECTED BY ESCROW       │
│                                              │
│ ⚠️ IMPORTANT: Your upfront payment will NOT  │
│ go directly to the company!                  │
│                                              │
│ How FixLanka Escrow Protects You:            │
│                                              │
│ ✓ Step 1: You pay LKR 25,000 to FixLanka    │
│   → Money held securely in escrow account    │
│                                              │
│ ✓ Step 2: Company MUST start work on time   │
│   → Company submits "Work Started" proof     │
│   → On-site photo required                   │
│   → Start date must match contract           │
│                                              │
│ ✓ Step 3: You verify work actually started  │
│   → FixLanka notifies you                    │
│   → You have 24 hours to verify              │
│   → Or auto-verified after 24 hours          │
│                                              │
│ ✓ Step 4: Payment released to company        │
│   → Only after work confirmed started        │
│   → Company can now purchase materials       │
│   → You're protected from fraud              │
│                                              │
│ 🛡️ What if company doesn't start work?      │
│ • Your money stays in escrow                 │
│ • Full refund processed automatically        │
│ • No penalty or fees charged                 │
│ • Contract can be cancelled                  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⚠️ PAYMENT 1 DUE NOW:                        │
│ Amount: LKR 25,000 (50%)                     │
│ Held by: FixLanka (Escrow Protection)        │
│ Released: After company starts work          │
│ Status: ⏳ Pending your payment              │
│                                              │
│ Why upfront payment?                         │
│ • Allows company to purchase materials       │
│ • Secures your booking                       │
│ • Standard practice for this payment term    │
│                                              │
│ Payment Methods Available:                   │
│ • Bank Transfer                              │
│ • Credit/Debit Card                          │
│ • Mobile Payment (Dialog/Mobitel)            │
│ • FixLanka Wallet                            │
│                                              │
│ [💳 PAY LKR 25,000 NOW (ESCROW PROTECTED)]   │
│ [📄 View Full Escrow Terms]                  │
│ [❓ How Does Escrow Work?]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT 2 (FINAL):                           │
│ Amount: LKR 25,000 (50%)                     │
│ Due: After work completion                   │
│ Status: ⏰ Not yet due                       │
│                                              │
│ Final payment will be requested after        │
│ company completes all work and you           │
│ verify quality.                              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ TIMELINE:                                    │
│ Work Scheduled Start: January 15, 2026       │
│ Expected Completion: February 4, 2026        │
│ Duration: 20 days                            │
│                                              │
│ ⚠️ Company must start work on scheduled      │
│ date or your upfront payment will be         │
│ refunded automatically.                      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⏰ 24-HOUR UNDO: 23:45:12 remaining          │
│ [⏪ UNDO ACCEPTANCE]                         │
│                                              │
│ Note: If you undo, any payment made will     │
│ be refunded immediately from escrow.         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 CHAT NOW AVAILABLE                        │
│ [💬 CHAT WITH ABC CONSTRUCTION]              │
│                                              │
│ Chat is active! Discuss payment details,     │
│ work schedule, or any questions.             │
│                                              │
└──────────────────────────────────────────────┘
```

### **Step 4-50-50: Customer Makes Upfront Payment**

**Customer clicks "Pay Now" and completes payment.**

**Payment Options Screen:**

```
┌──────────────────────────────────────────────┐
│ MAKE PAYMENT                                 │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ Payment Amount: LKR 25,000                   │
│ Payment Type: Upfront (50%)                  │
│ Recipient: ABC Construction (Pvt) Ltd        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ SELECT PAYMENT METHOD:                       │
│                                              │
│ (•) Credit/Debit Card                        │
│ ( ) Bank Transfer                            │
│ ( ) Mobile Payment                           │
│ ( ) FixLanka Wallet                          │
│                                              │
│ ──────────────────────────────────────────   │
│                                              │
│ CARD DETAILS:                                │
│                                              │
│ Card Number:                                 │
│ ┌──────────────────────────────────────┐    │
│ │ 4532 **** **** 1234                  │    │
│ └──────────────────────────────────────┘    │
│                                              │
│ Expiry:        CVV:                          │
│ ┌────────┐    ┌─────┐                       │
│ │ 12/28  │    │ ***  │                      │
│ └────────┘    └─────┘                       │
│                                              │
│ Cardholder Name:                             │
│ ┌──────────────────────────────────────┐    │
│ │ John Silva                            │    │
│ └──────────────────────────────────────┘    │
│                                              │
│ ──────────────────────────────────────────   │
│                                              │
│ PAYMENT SUMMARY:                             │
│                                              │
│ Subtotal: LKR 25,000.00                      │
│ Processing Fee: LKR 500.00 (2%)              │
│ ─────────────────────────                    │
│ Total: LKR 25,500.00                         │
│                                              │
│ ──────────────────────────────────────────   │
│                                              │
│ ☑ I authorize this payment                   │
│                                              │
│ [Cancel] [💳 PAY LKR 25,500]                 │
│                                              │
└──────────────────────────────────────────────┘
```

**After Payment Success:**

```
┌──────────────────────────────────────────────┐
│ ✅ PAYMENT SUCCESSFUL!                       │
├──────────────────────────────────────────────┤
│                                              │
│ Payment of LKR 25,500 processed successfully │
│                                              │
│ Transaction ID: TXN-2026-789456              │
│ Date: January 25, 2026 at 2:45 PM            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 PAYMENT HELD IN ESCROW                    │
│                                              │
│ Your payment is SECURE! Here's what happens  │
│ next:                                        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT DETAILS:                             │
│                                              │
│ Contract: #CNT-2026-002                      │
│ Project: Kitchen Sink Repair                 │
│ Company: ABC Construction                    │
│                                              │
│ Amount Paid: LKR 25,000                      │
│ Processing Fee: LKR 500                      │
│ Total Charged: LKR 25,500                    │
│                                              │
│ Payment Type: Upfront (50%)                  │
│ Method: Credit Card (****1234)               │
│ Status: 🔒 HELD IN ESCROW                    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🛡️ ESCROW PROTECTION ACTIVE                 │
│                                              │
│ Your LKR 25,000 is now held securely by      │
│ FixLanka and will NOT be released to the     │
│ company until:                               │
│                                              │
│ ✓ Company confirms work started              │
│ ✓ Company submits on-site photo proof        │
│ ✓ Start date matches schedule (Jan 15)       │
│ ✓ You verify work started (or 24hr passes)   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ CONTRACT STATUS: ⏳ PENDING WORK START       │
│                                              │
│ Contract is ready, waiting for company to    │
│ start work on the scheduled date.            │
│                                              │
│ Scheduled Start: January 15, 2026            │
│ Days Until Start: 11 days                    │
│ Expected Completion: February 4, 2026        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT HAPPENS ON JANUARY 15:                  │
│                                              │
│ 1️⃣ Company Arrives on Site                  │
│    • Company must arrive as scheduled        │
│    • Cannot delay without your agreement     │
│                                              │
│ 2️⃣ Company Submits "Work Started" Proof     │
│    • Takes on-site photo                     │
│    • Confirms start date                     │
│    • Submits via FixLanka app                │
│    • Time-stamped and location-verified      │
│                                              │
│ 3️⃣ You Receive Verification Request         │
│    • Email + SMS + In-app notification       │
│    • View company's start proof              │
│    • Confirm work actually started           │
│    • You have 24 hours to respond            │
│                                              │
│ 4️⃣ Payment Released (After Confirmation)    │
│    • If you confirm: Immediate release       │
│    • If no response: Auto-release after 24hr │
│    • Company receives payment                │
│    • Work continues normally                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🚨 WHAT IF COMPANY DOESN'T SHOW UP?          │
│                                              │
│ You're fully protected! If company:          │
│ • Doesn't start on scheduled date            │
│ • Doesn't submit work-started proof          │
│ • You report they didn't show up             │
│                                              │
│ Then:                                        │
│ ✓ Your LKR 25,000 stays in escrow            │
│ ✓ Full refund processed (within 24hrs)       │
│ ✓ No penalties or fees                       │
│ ✓ Contract automatically cancelled           │
│ ✓ You can accept another quotation           │
│                                              │
│ [📄 View Full Escrow Protection Terms]       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ REMAINING PAYMENT:                           │
│                                              │
│ Final Payment: LKR 25,000 (50%)              │
│ Due: After work completion                   │
│ Status: Not yet due                          │
│ Protection: Also held in escrow until        │
│             you approve completed work       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ NOTIFICATIONS:                               │
│                                              │
│ You'll be notified about:                    │
│ ✓ Company work-start submission (Jan 15)     │
│ ✓ Verification request                       │
│ ✓ Payment release confirmation               │
│ ✓ Daily progress updates via chat            │
│ ✓ Any delays or changes                      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ DOCUMENTS:                                   │
│                                              │
│ 📄 Payment Receipt (download)                │
│ 📄 Escrow Agreement (download)               │
│ 📄 Contract Agreement (download)             │
│ 📄 Payment Schedule (download)               │
│                                              │
│ [📥 Download All Documents]                  │
│ [📧 Email Documents to Me]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ TRACK YOUR PAYMENT:                          │
│                                              │
│ [🔒 VIEW ESCROW STATUS]                      │
│ [📊 VIEW CONTRACT]                           │
│ [💬 CHAT WITH COMPANY]                       │
│ [❓ ESCROW FAQs]                             │
│ [📞 CONTACT SUPPORT]                         │
│ [Close]                                      │
│                                              │
└──────────────────────────────────────────────┘
```

### **NEW STEP: Company Receives Payment Hold Notice**

**Company sees different message - payment is held:**

```
┌──────────────────────────────────────────────┐
│ 📧 Customer Made Upfront Payment             │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ Good news! John Silva has paid the upfront   │
│ payment of LKR 25,000.                       │
│                                              │
│ ⚠️ IMPORTANT: Payment is held in escrow      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT STATUS: 🔒 HELD IN ESCROW            │
│                                              │
│ Amount: LKR 25,000 (50% upfront)             │
│ Status: Secured in FixLanka escrow account   │
│ Release Condition: Work start confirmation   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ TO RECEIVE THIS PAYMENT, YOU MUST:           │
│                                              │
│ 1️⃣ Start work on scheduled date: Jan 15     │
│                                              │
│ 2️⃣ Submit "Work Started" confirmation:      │
│    • Arrive on-site as scheduled             │
│    • Take clear on-site photo                │
│    • Submit via FixLanka app                 │
│    • Include start date confirmation         │
│                                              │
│ 3️⃣ Wait for customer verification:          │
│    • Customer has 24 hours to verify         │
│    • Or auto-verified after 24 hours         │
│                                              │
│ 4️⃣ Payment released to your account!        │
│    • Automatically after verification        │
│    • Usually within 1-2 hours                │
│    • You'll receive notification             │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⚠️ IMPORTANT REMINDERS:                      │
│                                              │
│ • Start on time: Jan 15, 2026                │
│ • Cannot delay without customer agreement    │
│ • Must submit photo proof on start day       │
│ • Payment released only after work starts    │
│                                              │
│ If you cannot start on scheduled date:       │
│ • Contact customer IMMEDIATELY via chat      │
│ • Negotiate new start date                   │
│ • Get customer's written agreement           │
│ • Update contract start date                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHY ESCROW SYSTEM?                           │
│                                              │
│ Escrow protects both parties:               │
│ ✓ Customer: Money safe until work starts     │
│ ✓ Company: Payment guaranteed when you start │
│ ✓ Both: Trust and transparency               │
│                                              │
│ This builds customer confidence and helps    │
│ you get more bookings!                       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ CONTRACT DETAILS:                            │
│                                              │
│ Project: Kitchen Sink Repair                 │
│ Customer: John Silva                         │
│ Start Date: January 15, 2026                 │
│ Duration: 20 days                            │
│ Total Budget: LKR 50,000                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📅 SET START DAY REMINDER]                  │
│ [💬 CHAT WITH CUSTOMER]                      │
│ [📄 VIEW CONTRACT]                           │
│ [❓ ESCROW FAQs]                             │
│                                              │
└──────────────────────────────────────────────┘
```

### **NEW STEP: Work Start Day - Company Submits Proof (January 15)**

**Company arrives on-site and submits work-started confirmation:**

```
┌──────────────────────────────────────────────┐
│ 📸 SUBMIT WORK STARTED CONFIRMATION          │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ You're starting work today! Submit proof to  │
│ release your upfront payment from escrow.    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ESCROW PAYMENT PENDING RELEASE:              │
│                                              │
│ Amount: LKR 25,000 (50% upfront)             │
│ Status: 🔒 Held in escrow                    │
│ Will Release: After you submit proof         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WORK START CONFIRMATION FORM:                │
│                                              │
│ Start Date: * (Required)                     │
│ ┌──────────────────────────────────────┐    │
│ │ January 15, 2026        [Today ✓]    │    │
│ └──────────────────────────────────────┘    │
│                                              │
│ Start Time:                                  │
│ ┌──────────────────────────────────────┐    │
│ │ 09:00 AM                             │    │
│ └──────────────────────────────────────┘    │
│                                              │
│ On-Site Photo: * (Required)                  │
│ Take a clear photo showing:                  │
│ • You or your team on-site                   │
│ • Work area/location                         │
│ • Today's work beginning                     │
│                                              │
│ 📷 [TAKE PHOTO NOW]                          │
│ 📷 [UPLOAD FROM GALLERY]                     │
│                                              │
│ Photo Preview:                               │
│ [Photo: Team at kitchen sink location]       │
│ ✓ Photo captured                             │
│ ✓ Location verified (Colombo 07)             │
│ ✓ Timestamp: Jan 15, 2026 09:15 AM           │
│                                              │
│ Work Description (Optional):                 │
│ ┌──────────────────────────────────────┐    │
│ │ Arrived on site. Beginning with site │    │
│ │ preparation and old sink removal. All │    │
│ │ tools and initial materials on-site.  │    │
│ └──────────────────────────────────────┘    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ☑ I confirm work has started today as        │
│   scheduled                                  │
│                                              │
│ ☑ I understand this triggers escrow payment  │
│   release after customer verification        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [Cancel] [✅ SUBMIT WORK START CONFIRMATION] │
│                                              │
└──────────────────────────────────────────────┘
```

**After company submits:**

```
┌──────────────────────────────────────────────┐
│ ✅ WORK START CONFIRMATION SUBMITTED!        │
├──────────────────────────────────────────────┤
│                                              │
│ Your work-started proof has been sent to     │
│ customer John Silva for verification.        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ SUBMITTED PROOF:                             │
│                                              │
│ 📅 Start Date: January 15, 2026              │
│ 🕐 Start Time: 09:15 AM                      │
│ 📷 Photo: Submitted ✓                        │
│ 📍 Location: Verified (Colombo 07) ✓         │
│ 🕐 Timestamp: Verified ✓                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT RELEASE STATUS:                      │
│                                              │
│ Amount: LKR 25,000                           │
│ Status: ⏳ Awaiting customer verification    │
│                                              │
│ NEXT STEPS:                                  │
│                                              │
│ 1. Customer receives verification request    │
│ 2. Customer has 24 hours to verify           │
│ 3. Payment released after verification       │
│    (or auto-released after 24 hours)         │
│                                              │
│ Expected Payment: Today or tomorrow          │
│                                              │
│ You'll receive notification when payment     │
│ is released to your account.                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 Customer has been notified via:           │
│ • Email notification                         │
│ • SMS alert                                  │
│ • In-app notification                        │
│ • Chat message                               │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [💬 SEND UPDATE TO CUSTOMER]                 │
│ [📊 VIEW CONTRACT]                           │
│ [🔒 CHECK ESCROW STATUS]                     │
│ [Close]                                      │
│                                              │
└──────────────────────────────────────────────┘
```

### **NEW STEP: Customer Receives Verification Request**

**Customer receives notification:**

```
┌──────────────────────────────────────────────┐
│ ✅ COMPANY STARTED WORK - VERIFY NOW         │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ ABC Construction has submitted proof that    │
│ work has started as scheduled!               │
│                                              │
│ ⚠️ ACTION REQUIRED: Verify work started      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ COMPANY'S WORK START PROOF:                  │
│                                              │
│ 📅 Start Date: January 15, 2026 ✓            │
│ 🕐 Time: 09:15 AM                            │
│ 📍 Location: Colombo 07 (Your address) ✓     │
│ 📷 On-Site Photo: Submitted ✓                │
│                                              │
│ [VIEW FULL-SIZE PHOTO]                       │
│                                              │
│ Photo Shows:                                 │
│ 📷 [Company team at your kitchen sink]       │
│    Timestamp: Jan 15, 2026 - 09:15 AM        │
│    Location: Verified match ✓                │
│                                              │
│ Company's Note:                              │
│ "Arrived on site. Beginning with site        │
│ preparation and old sink removal. All tools  │
│ and initial materials on-site."              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 ESCROW PAYMENT PENDING YOUR VERIFICATION: │
│                                              │
│ Amount: LKR 25,000 (50% upfront)             │
│ Status: Held in escrow                       │
│ Awaiting: Your verification                  │
│                                              │
│ Your escrow payment will be released to      │
│ company after you verify that work has       │
│ actually started.                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PLEASE VERIFY:                               │
│                                              │
│ Did ABC Construction actually start work     │
│ at your location today?                      │
│                                              │
│ OPTION 1: YES, WORK STARTED ✅               │
│ Select this if:                              │
│ • Company arrived as scheduled               │
│ • Work has begun                             │
│ • Photo matches your location                │
│ • You're satisfied they started              │
│                                              │
│ Result: Payment released immediately to      │
│         company from escrow                  │
│                                              │
│ [✅ CONFIRM WORK STARTED]                    │
│         ↑ Green button                       │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ OPTION 2: NO, DIDN'T START / ISSUE ❌        │
│ Select this if:                              │
│ • Company didn't show up                     │
│ • Work hasn't started                        │
│ • Photo doesn't match                        │
│ • Any concerns or issues                     │
│                                              │
│ Result: Payment stays in escrow, dispute     │
│         process starts, you get refund       │
│                                              │
│ [❌ REPORT ISSUE]                            │
│         ↑ Red button                         │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ OPTION 3: NEED MORE TIME 🕐                  │
│ Not sure yet? Take more time to verify.      │
│                                              │
│ You have 24 hours total to verify.           │
│ Time Remaining: 23 hours 45 minutes          │
│                                              │
│ If no response after 24 hours:               │
│ → Payment auto-released (work assumed OK)    │
│                                              │
│ [⏰ VERIFY LATER] [💬 ASK COMPANY QUESTION]  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ℹ️ VERIFICATION TIPS:                        │
│                                              │
│ • Check if company team is at your location  │
│ • Look at the submitted photo carefully      │
│ • Verify photo timestamp is today            │
│ • If uncertain, chat with company            │
│ • Report issues immediately if problems      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [💬 CHAT WITH COMPANY]                       │
│ [📞 CALL SUPPORT]                            │
│ [❓ VERIFICATION FAQs]                       │
│                                              │
└──────────────────────────────────────────────┘
```

### **Customer Confirms Work Started:**

```
┌──────────────────────────────────────────────┐
│ ✅ WORK START CONFIRMED!                     │
├──────────────────────────────────────────────┤
│                                              │
│ Thank you for confirming that ABC            │
│ Construction has started work as scheduled!  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ESCROW PAYMENT RELEASED:                     │
│                                              │
│ Amount: LKR 25,000                           │
│ Released To: ABC Construction                │
│ Released At: January 15, 2026 - 10:30 AM     │
│ Transaction ID: REL-2026-789456              │
│                                              │
│ Your upfront payment has been released from  │
│ escrow to the company. They can now purchase │
│ materials and continue work.                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PROJECT STATUS: ✅ WORK IN PROGRESS          │
│                                              │
│ Start Date: January 15, 2026 ✓               │
│ Expected Completion: February 4, 2026        │
│ Days Remaining: 20 days                      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT STATUS:                              │
│                                              │
│ ✅ Upfront (50%): LKR 25,000 - Paid & Released│
│ ⏳ Final (50%): LKR 25,000 - Due at completion│
│                                              │
│ Total Paid: LKR 25,000 (50%)                 │
│ Remaining: LKR 25,000 (50%)                  │
│                                              │
│ Final payment will be held in escrow after   │
│ work completion until you approve quality.   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT HAPPENS NEXT:                           │
│                                              │
│ 1. Company continues work (Jan 15 - Feb 4)   │
│ 2. Receive progress updates via chat         │
│ 3. Company completes work                    │
│ 4. You make final payment (held in escrow)   │
│ 5. You verify completed work                 │
│ 6. Final payment released after approval     │
│ 7. Rate company and close project            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📄 RECEIPT AVAILABLE:                        │
│                                              │
│ [📥 Download Escrow Release Receipt]         │
│ [📧 Email Receipt to Me]                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📊 VIEW CONTRACT]                           │
│ [💬 CHAT WITH COMPANY]                       │
│ [🔒 VIEW PAYMENT HISTORY]                    │
│ [Close]                                      │
│                                              │
└──────────────────────────────────────────────┘
```

### **Phase 5-50-50: Work Execution (with Escrow Protection)**

**No milestone plan, no milestone approvals. Just simple work flow:**

**Week 1-3: Company Works**
- Company begins work on start date
- Provides daily/weekly updates via chat
- Shares progress photos
- Customer monitors progress
- No payment releases during work (already paid 50%)

**Example Chat During Work:**

```
[ABC Construction] Jan 15, 9:00 AM
Good morning John! Starting work today.
Materials purchased with your upfront payment.
Thank you! 🙂

[You] Jan 15, 9:15 AM
Great! Keep me updated.

[ABC Construction] Jan 18, 3:00 PM
Progress update: Sink installation complete.
Working on plumbing connections.
📷 [Photo: New sink installed]

[You] Jan 18, 3:30 PM
Looks great! On schedule?

[ABC Construction] Jan 18, 3:45 PM
Yes! On track to finish by Feb 4 as planned. ✓
```

### **Phase 6-50-50: Work Completion & Final Payment**

**Company completes all work:**

```
[ABC Construction] Feb 4, 4:00 PM
John, work is complete! Ready for your
inspection. All items finished as agreed.
📷 [6 photos of completed work]

[System Message]
═══════════════════════════════════════════════
Company has marked work as COMPLETE
Contract #CNT-2026-002

Please verify the work quality and make
final payment if satisfied.

[INSPECT & PAY FINAL PAYMENT →]
═══════════════════════════════════════════════
```

**Customer Reviews Completed Work:**

```
┌──────────────────────────────────────────────┐
│ WORK COMPLETION VERIFICATION                 │
│ Contract #CNT-2026-002                       │
├──────────────────────────────────────────────┤
│                                              │
│ ABC Construction has marked all work as      │
│ complete and is requesting final payment.    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ COMPLETED WORK:                              │
│                                              │
│ Project: Kitchen Sink Repair                 │
│ Started: January 15, 2026                    │
│ Completed: February 4, 2026 ✓ On schedule    │
│ Duration: 20 days (as planned)               │
│                                              │
│ Original Scope:                              │
│ • Remove old sink and fixtures               │
│ • Install new sink and plumbing              │
│ • Connect water supply and drainage          │
│ • Test and verify functionality              │
│ • Clean up work area                         │
│                                              │
│ Company's Completion Notes:                  │
│ "All work completed successfully. New sink   │
│ installed with modern fixtures. Plumbing     │
│ tested - no leaks detected. 2-year warranty  │
│ provided. Maintenance guide included."       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PHOTO EVIDENCE: (6 photos)                   │
│                                              │
│ 📷 Completed sink installation                │
│ 📷 Faucet and fixtures                        │
│ 📷 Under-sink plumbing                        │
│ 📷 Drainage system                            │
│ 📷 Clean work area                            │
│ 📷 Overall view                               │
│                                              │
│ [View All Photos]                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ DOCUMENTS PROVIDED:                          │
│                                              │
│ 📄 Warranty Certificate (2 years)            │
│ 📄 Maintenance Guidelines                    │
│ 📄 Material Specifications                   │
│                                              │
│ [Download Documents]                         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💰 FINAL PAYMENT DUE:                        │
│                                              │
│ Total Budget: LKR 50,000                     │
│ Already Paid: LKR 25,000 (50% upfront)       │
│ Final Payment: LKR 25,000 (50%)              │
│                                              │
│ ⚠️ ACTION REQUIRED:                          │
│                                              │
│ Please verify the work quality, then:        │
│                                              │
│ OPTION 1: ACCEPT & PAY                       │
│ If work is satisfactory, make final          │
│ payment to complete project.                 │
│                                              │
│ [✅ VERIFY WORK & PAY LKR 25,000]            │
│           ↑ Green button                     │
│                                              │
│ OPTION 2: REQUEST FIXES                      │
│ If you notice issues or incomplete work,     │
│ request company to fix before payment.       │
│                                              │
│ [🔧 REQUEST FIXES] (Orange button)           │
│                                              │
│ OPTION 3: DISCUSS VIA CHAT                   │
│ Ask questions or clarify concerns before     │
│ making decision.                             │
│                                              │
│ [💬 OPEN CHAT] (Blue button)                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ Note: After final payment, you'll rate       │
│ the company and project will be complete.    │
│                                              │
└──────────────────────────────────────────────┘
```

**Customer Verifies & Pays Final Payment:**

```
┌──────────────────────────────────────────────┐
│ FINAL PAYMENT CONFIRMATION                   │
├──────────────────────────────────────────────┤
│                                              │
│ You are about to make the final payment      │
│ for this project.                            │
│                                              │
│ Amount: LKR 25,000 (50%)                     │
│                                              │
│ ⚠️ BEFORE PAYING, CONFIRM:                   │
│                                              │
│ ☐ I have inspected the completed work        │
│ ☐ All work is completed satisfactorily       │
│ ☐ I have received all documents              │
│ ☐ I am satisfied with the quality            │
│                                              │
│ After payment, project will be marked as     │
│ complete. You can still contact company      │
│ for warranty claims.                         │
│                                              │
│ [Cancel] [💳 PAY LKR 25,000 NOW]             │
│                                              │
└──────────────────────────────────────────────┘
```

**After Final Payment Success:**

```
┌──────────────────────────────────────────────┐
│ 🎉 PROJECT COMPLETED SUCCESSFULLY!           │
├──────────────────────────────────────────────┤
│                                              │
│ Final payment of LKR 25,000 has been         │
│ processed successfully!                      │
│                                              │
│ Transaction ID: TXN-2026-789789              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT SUMMARY:                             │
│                                              │
│ Upfront Payment: LKR 25,000 (Jan 25)         │
│ Final Payment: LKR 25,000 (Feb 4)            │
│ ─────────────────────────                    │
│ Total Paid: LKR 50,000 ✅                    │
│                                              │
│ Project: COMPLETE                            │
│ Status: ✅ PAID IN FULL                      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📋 Rate your experience with                 │
│    ABC Construction:                         │
│                                              │
│ [⭐ RATE & REVIEW NOW]                       │
│                                              │
└──────────────────────────────────────────────┘
```

### **Key Differences - 50-50 Payment:**

**Compared to Milestone-Based:**
- ✅ **Simpler:** No milestone plan needed
- ✅ **Faster:** Contract activates immediately after upfront payment
- ✅ **Less admin:** Only 2 payments vs. 4+ milestone payments
- ✅ **Chat available:** Opens immediately (not after plan)
- ⚠️ **Upfront risk:** Customer pays 50% before work starts
- ⚠️ **Less control:** Can't withhold payment during work
- ⚠️ **Single verification:** Only verify once at end

**Best For:**
- Smaller projects
- Trusted companies
- Simple work scope
- Fixed deliverables
- Time-sensitive projects

---

## **SCENARIO 3: UPFRONT + FINAL PAYMENT (30-70)**

### **What's Different from 50-50:**
- Same process as 50-50
- Only difference: payment split ratio
- 30% upfront, 70% final
- Less upfront risk for customer
- More final payment for verification
- **🔒 ESCROW PROTECTION:** Same escrow system applies

### **Payment Schedule for 30-70:**

**For LKR 50,000 budget:**

```
PAYMENT SCHEDULE: 30% Upfront + 70% Final
🔒 ESCROW PROTECTED

Payment 1 (Upfront): LKR 15,000 (30%)
• Due: Immediately after acceptance
• Purpose: Material procurement, booking
• Held in: FixLanka Escrow Account
• Released: After company starts work & verified

Payment 2 (Final): LKR 35,000 (70%)
• Due: After work completion
• Purpose: Balance payment
• Held in: FixLanka Escrow Account
• Released: After customer verifies quality
```

**Customer View:**

```
┌──────────────────────────────────────────────┐
│ 💰 PAYMENT REQUIRED                          │
│                                              │
│ Payment Terms: 30% Upfront + 70% Final       │
│ Total Budget: LKR 50,000                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 ESCROW PROTECTION ACTIVE                  │
│                                              │
│ Both payments protected by FixLanka escrow:  │
│ • Upfront: Held until work starts            │
│ • Final: Held until you approve quality      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT 1 (UPFRONT): LKR 15,000 (30%)        │
│ Status: ⏳ Due now                           │
│ Protection: Held in escrow until work starts │
│                                              │
│ [💳 PAY LKR 15,000 NOW (ESCROW PROTECTED)]   │
│                                              │
│ PAYMENT 2 (FINAL): LKR 35,000 (70%)          │
│ Status: Due after completion                 │
│ Protection: Held in escrow until you approve │
│                                              │
│ ──────────────────────────────────────────   │
│                                              │
│ Why 30-70 split?                             │
│ • Lower upfront risk for you (only 30%)      │
│ • Majority paid after seeing results (70%)   │
│ • Still allows company to buy materials      │
│ • Good balance of security                   │
│ • Full escrow protection both payments       │
│                                              │
│ [📄 View Full Escrow Terms]                  │
│ [❓ How Escrow Protects Me]                  │
│                                              │
└──────────────────────────────────────────────┘
```

**All other steps identical to 50-50 scenario above, including:**
- Escrow payment holding
- Work start verification
- Photo proof requirement
- 24-hour verification window
- Payment release after confirmation

---

## **SCENARIO 4: AFTER COMPLETION (100% FINAL)**

### **What's Different:**
- **NO upfront payment**
- Contract activates immediately after acceptance
- Company works entirely on trust
- Customer pays only after verifying completed work
- Zero financial risk for customer
- High risk for company
- **🔒 ESCROW PROTECTION:** Final payment held until customer approves

---

### **Phase 3 → 4: Contract with After Completion Payment**

**After customer accepts quotation with "After Completion" payment:**

```
┌──────────────────────────────────────────────┐
│ ✅ QUOTATION ACCEPTED - CONTRACT CREATED     │
│ Contract #CNT-2026-003                       │
├──────────────────────────────────────────────┤
│                                              │
│ 💰 PAYMENT TERMS: AFTER COMPLETION           │
│                                              │
│ Total Budget: LKR 50,000                     │
│                                              │
│ Payment Schedule:                            │
│ • Upfront Payment: LKR 0 (0%)                │
│ • Final Payment: LKR 50,000 (100%)           │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ✅ NO UPFRONT PAYMENT REQUIRED!              │
│                                              │
│ You don't need to pay anything now.          │
│ Work will begin on schedule, and you'll      │
│ only pay after:                              │
│                                              │
│ ✓ All work is completed                      │
│ ✓ You inspect and verify quality             │
│ ✓ You approve the work                       │
│ ✓ You're satisfied with results              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 ESCROW PROTECTION FOR FINAL PAYMENT       │
│                                              │
│ When you make final payment (after work      │
│ completion), it will be:                     │
│                                              │
│ • Held in FixLanka escrow account            │
│ • NOT immediately sent to company            │
│ • You verify work quality first              │
│ • Released only after your approval          │
│ • 7-day quality guarantee period             │
│                                              │
│ This protects you even with After Completion │
│ payment - ensuring quality before company    │
│ receives money.                              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ CONTRACT STATUS: ✅ ACTIVE                   │
│                                              │
│ Your contract is already active!             │
│ No payment needed to activate.               │
│                                              │
│ Work Starts: January 15, 2026                │
│ Expected Completion: February 4, 2026        │
│ Duration: 20 days                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT DUE: After February 4, 2026          │
│                                              │
│ You'll be notified when company completes    │
│ work. You can then:                          │
│ • Inspect the completed work                 │
│ • Verify all deliverables                    │
│ • Request fixes if needed (free)             │
│ • Make payment (held in escrow)              │
│ • Final approve within 7 days                │
│ • Payment released after approval            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 CHAT AVAILABLE NOW                        │
│ [💬 CHAT WITH ABC CONSTRUCTION]              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⏰ 24-HOUR UNDO: 23:45:12 remaining          │
│ [⏪ UNDO ACCEPTANCE]                         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT HAPPENS NEXT:                           │
│                                              │
│ 1. Company starts work (Jan 15)              │
│ 2. Receive progress updates via chat         │
│ 3. Company completes work (Feb 4)            │
│ 4. You inspect and verify quality            │
│ 5. You make payment (held in escrow)         │
│ 6. 7-day quality guarantee period            │
│ 7. You give final approval                   │
│ 8. Payment released to company               │
│ 9. Rate company and close project            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📊 VIEW CONTRACT] [💬 OPEN CHAT]            │
│ [🔒 LEARN ABOUT ESCROW PROTECTION] [Close]   │
│                                              │
└──────────────────────────────────────────────┘
```

### **Phase 5-100: Work Execution**

**Identical to 50-50 scenario:**
- Company works and provides updates via chat
- Customer monitors progress
- Photos and documents shared
- No payments during work

### **Phase 6-100: Completion & Full Payment**

**Company marks work complete:**

```
┌──────────────────────────────────────────────┐
│ WORK COMPLETION VERIFICATION                 │
│ Contract #CNT-2026-003                       │
├──────────────────────────────────────────────┤
│                                              │
│ ABC Construction has completed all work      │
│ and is requesting payment.                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💰 FULL PAYMENT DUE (ESCROW PROTECTED):      │
│                                              │
│ Total Budget: LKR 50,000                     │
│ Paid So Far: LKR 0                           │
│ Amount Due: LKR 50,000 (100%)                │
│                                              │
│ Payment Terms: After Completion              │
│                                              │
│ 🔒 ESCROW PROTECTION:                        │
│ Your payment will be held in escrow for      │
│ 7 days while you verify quality. Company     │
│ receives payment only after your approval.   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ COMPLETED WORK:                              │
│ [Work details and photos]                    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⚠️ HOW PAYMENT WORKS:                        │
│                                              │
│ Step 1: You pay LKR 50,000 now               │
│ → Goes to FixLanka escrow (NOT company)      │
│                                              │
│ Step 2: You have 7 days to verify quality    │
│ → Inspect thoroughly                         │
│ → Test everything                            │
│ → Request fixes if needed (free)             │
│                                              │
│ Step 3: You give final approval              │
│ → If satisfied: Approve payment release      │
│ → Payment goes to company                    │
│                                              │
│ Step 4: Project complete!                    │
│ → Rate company                               │
│ → Download documents                         │
│                                              │
│ 🛡️ What if issues found?                    │
│ • Money stays in escrow                      │
│ • Company must fix issues (free)             │
│ • 7-day period extended until fixed          │
│ • Only approve when fully satisfied          │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ☐ All work completed as agreed               │
│ ☐ Quality meets expectations                 │
│ ☐ No issues or defects                       │
│ ☐ All deliverables received                  │
│ ☐ Documentation provided                     │
│ ☐ Work area cleaned                          │
│                                              │
│ Take your time to inspect carefully!         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [✅ PAY LKR 50,000 (ESCROW PROTECTED)]       │
│ [🔧 REQUEST FIXES FIRST]                     │
│ [💬 DISCUSS VIA CHAT]                        │
│ [🔒 LEARN ABOUT 7-DAY PROTECTION]            │
│                                              │
└──────────────────────────────────────────────┘
```

**After Payment (Goes to Escrow):**

```
┌──────────────────────────────────────────────┐
│ ✅ PAYMENT RECEIVED - HELD IN ESCROW         │
├──────────────────────────────────────────────┤
│                                              │
│ Payment of LKR 50,000 processed successfully │
│ and held securely in escrow!                 │
│                                              │
│ Transaction ID: TXN-2026-789789              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔒 7-DAY QUALITY GUARANTEE PERIOD            │
│                                              │
│ Your payment is held in escrow and will      │
│ NOT be released to company until you give    │
│ final approval.                              │
│                                              │
│ Escrow Period: 7 days                        │
│ Ends: February 11, 2026                      │
│ Time Remaining: 6 days 23 hours              │
│                                              │
│ During this time:                            │
│ ✓ Thoroughly inspect all work                │
│ ✓ Test functionality completely              │
│ ✓ Check quality and finish                   │
│ ✓ Request fixes if needed (free)             │
│ ✓ Verify all deliverables                    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT STATUS:                              │
│                                              │
│ Amount: LKR 50,000                           │
│ Status: 🔒 Held in Escrow                    │
│ Released To: ABC Construction (pending)      │
│ Release Condition: Your final approval       │
│                                              │
│ Company will receive payment only after      │
│ you approve quality.                         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ YOUR ACTIONS DURING 7-DAY PERIOD:            │
│                                              │
│ OPTION 1: Approve & Release Payment          │
│ If everything is perfect, you can approve    │
│ immediately (no need to wait 7 days).        │
│                                              │
│ [✅ APPROVE & RELEASE PAYMENT NOW]           │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ OPTION 2: Request Fixes                      │
│ If you notice issues, request fixes:         │
│ • Company fixes at no additional cost        │
│ • Escrow period extended until fixed         │
│ • Payment stays held                         │
│                                              │
│ [🔧 REQUEST FIXES]                           │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ OPTION 3: Raise Dispute                      │
│ If serious issues and company not           │
│ cooperating:                                 │
│ • Start dispute process                      │
│ • FixLanka mediates                          │
│ • Partial/full refund possible               │
│                                              │
│ [⚠️ RAISE DISPUTE]                           │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ OPTION 4: Auto-Release After 7 Days          │
│ If you don't take action within 7 days:     │
│ • Payment auto-released to company           │
│ • Assumes you're satisfied                   │
│ • Reminder sent 1 day before                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ INSPECTION CHECKLIST:                        │
│                                              │
│ ☐ Visual inspection complete                 │
│ ☐ Functionality tested                       │
│ ☐ Quality meets expectations                 │
│ ☐ All deliverables received                  │
│ ☐ No defects found                           │
│ ☐ Clean and professional finish              │
│ ☐ Documentation provided                     │
│ ☐ Warranty certificate received              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [� VIEW DETAILED INSPECTION GUIDE]          │
│ [💬 CHAT WITH COMPANY]                       │
│ [🔒 VIEW ESCROW DETAILS]                     │
│ [📞 CALL SUPPORT]                            │
│                                              │
└──────────────────────────────────────────────┘
```

**When Customer Approves (Within 7 Days):**

```
┌──────────────────────────────────────────────┐
│ 🎉 PAYMENT SUCCESSFUL - PROJECT COMPLETE!    │
├──────────────────────────────────────────────┤
│                                              │
│ Full payment of LKR 50,000 processed!        │
│                                              │
│ Payment Breakdown:                           │
│ • Upfront: LKR 0 (0%)                        │
│ • Final: LKR 50,000 (100%) ✅                │
│ • Total: LKR 50,000 ✅ PAID IN FULL          │
│                                              │
│ Project Status: COMPLETED                    │
│                                              │
│ [⭐ RATE & REVIEW NOW]                       │
│                                              │
└──────────────────────────────────────────────┘
```

### **Key Differences - After Completion Payment:**

**Compared to Milestone-Based:**
- ✅ **Simplest:** No plan, no upfront payment
- ✅ **Zero risk:** Customer pays nothing until satisfied
- ✅ **One verification:** Single payment at end
- ✅ **Fast activation:** Contract active immediately
- ⚠️ **Company risk:** Company works on trust
- ⚠️ **Less common:** Few companies offer this

**Best For:**
- Established, trusted companies
- Small, quick projects
- Customers with budget constraints
- Emergency/urgent repairs

---

## **SCENARIO 5: TIME & MATERIAL PAYMENT**

### **What's Different:**
- Payment based on actual time worked
- NOT fixed price - variable cost
- Hourly or daily rates
- Weekly/monthly billing
- Requires detailed time tracking
- No upfront total budget

---

### **Phase 3 → 4: Contract with Time & Material Payment**

**After customer accepts quotation with "Time & Material" payment:**

```
┌──────────────────────────────────────────────┐
│ ✅ QUOTATION ACCEPTED - CONTRACT CREATED     │
│ Contract #CNT-2026-004                       │
├──────────────────────────────────────────────┤
│                                              │
│ 💰 PAYMENT TERMS: TIME & MATERIAL            │
│                                              │
│ Pricing Type: Time & Material (Hourly)       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ RATE STRUCTURE:                              │
│                                              │
│ Labor Rate: LKR 2,500/hour                   │
│ • Skilled plumber                            │
│ • Includes tools and expertise               │
│                                              │
│ Material Cost: Actual cost + 10% markup      │
│ • Receipts provided for all materials        │
│ • 10% markup covers procurement              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ESTIMATED BUDGET: LKR 50,000                 │
│                                              │
│ ⚠️ This is an ESTIMATE only!                 │
│                                              │
│ Actual cost depends on:                      │
│ • Actual hours worked                        │
│ • Material costs (market prices)             │
│ • Any additional work discovered             │
│ • Complexity of issues found                 │
│                                              │
│ Estimated Hours: 20 hours                    │
│ Estimated Materials: LKR 15,000              │
│                                              │
│ Final cost may be higher or lower than       │
│ estimate based on actual work required.      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ BILLING SCHEDULE: Weekly                     │
│                                              │
│ You'll receive invoices every Friday for     │
│ work completed that week.                    │
│                                              │
│ Each invoice includes:                       │
│ • Detailed time log (date, hours, tasks)     │
│ • Material receipts and costs                │
│ • Photos of work completed                   │
│ • Running total spent                        │
│                                              │
│ Payment due: Within 3 days of invoice        │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ SPENDING CAP: LKR 60,000                     │
│                                              │
│ To protect you, we've set a spending cap     │
│ at LKR 60,000 (120% of estimate).            │
│                                              │
│ Company MUST stop work and get your          │
│ approval if costs approach this cap.         │
│                                              │
│ You can adjust cap anytime via chat.         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ TIME TRACKING: ✅ ENABLED                    │
│                                              │
│ Company will use FixLanka time tracking:     │
│ • Clock in/out for work sessions             │
│ • Photo verification at clock-in             │
│ • Location tracking (site verification)      │
│ • Task notes for each session                │
│                                              │
│ You can view live time logs anytime!         │
│ [📊 VIEW TIME LOGS]                          │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ CONTRACT STATUS: ✅ ACTIVE                   │
│                                              │
│ Work Starts: January 15, 2026                │
│ Time Tracking Begins: Jan 15 at 8:00 AM      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 CHAT AVAILABLE NOW                        │
│ [💬 CHAT WITH ABC CONSTRUCTION]              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT HAPPENS NEXT:                           │
│                                              │
│ Week 1:                                      │
│ • Company works and tracks time daily        │
│ • You monitor time logs in real-time         │
│ • Receive updates via chat                   │
│ • Friday: First invoice issued               │
│                                              │
│ Week 2-3:                                    │
│ • Same process continues                     │
│ • Weekly invoices                            │
│ • Pay within 3 days of each invoice          │
│                                              │
│ Completion:                                  │
│ • Company completes work                     │
│ • Final invoice issued                       │
│ • You verify and pay                         │
│ • Rate and close project                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📊 VIEW CONTRACT] [⏱️ VIEW TIME LOGS]       │
│ [💬 OPEN CHAT] [⚙️ ADJUST CAP] [Close]      │
│                                              │
└──────────────────────────────────────────────┘
```

### **Phase 5-T&M: Daily Time Tracking**

**Real-time time tracking visible to customer:**

```
┌──────────────────────────────────────────────┐
│ ⏱️ TIME TRACKING - CONTRACT #CNT-2026-004   │
│ ABC Construction - Kitchen Sink Repair       │
├──────────────────────────────────────────────┤
│                                              │
│ CURRENT STATUS: 🟢 WORKING NOW               │
│                                              │
│ Clocked In: Today at 9:00 AM                 │
│ Duration: 2 hours 15 minutes                 │
│ Location: ✅ Verified (On-site)              │
│                                              │
│ Today's Tasks:                               │
│ "Installing new sink basin and fixtures"     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ THIS WEEK (Jan 15-19):                       │
│                                              │
│ Monday, Jan 15:                              │
│ 9:00 AM - 12:30 PM (3.5 hours)               │
│ Tasks: Site prep, old sink removal           │
│ 📷 Clock-in photo | 📍 Location verified     │
│                                              │
│ 1:30 PM - 5:00 PM (3.5 hours)                │
│ Tasks: Plumbing inspection, material list    │
│ 📷 Clock-in photo | 📍 Location verified     │
│                                              │
│ Total Monday: 7 hours                        │
│ Cost: LKR 17,500 (7 × LKR 2,500)             │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ Tuesday, Jan 16:                             │
│ 8:30 AM - 12:00 PM (3.5 hours)               │
│ Tasks: Material procurement                  │
│ 📷 Clock-in photo | 📍 Location verified     │
│                                              │
│ 2:00 PM - 6:00 PM (4 hours)                  │
│ Tasks: New plumbing installation             │
│ 📷 Clock-in photo | 📍 Location verified     │
│                                              │
│ Total Tuesday: 7.5 hours                     │
│ Cost: LKR 18,750                             │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ Wednesday, Jan 17: (IN PROGRESS)             │
│ 9:00 AM - Now (2.25 hours so far)            │
│ Tasks: Installing new sink basin             │
│ 📷 Clock-in photo | 📍 Location verified     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WEEK SUMMARY SO FAR:                         │
│                                              │
│ Total Hours: 16.75 hours                     │
│ Labor Cost: LKR 41,875                       │
│                                              │
│ Materials Purchased:                         │
│ • Sink basin: LKR 8,500                      │
│ • Faucet set: LKR 3,200                      │
│ • Pipes & fittings: LKR 2,800                │
│ • Sealant & supplies: LKR 1,200              │
│ Subtotal: LKR 15,700                         │
│ Markup (10%): LKR 1,570                      │
│ Material Total: LKR 17,270                   │
│                                              │
│ 📎 All receipts attached and verified ✓      │
│                                              │
│ ─────────────────────────────────────────    │
│                                              │
│ RUNNING TOTAL THIS WEEK:                     │
│ Labor: LKR 41,875                            │
│ Materials: LKR 17,270                        │
│ ═══════════════════                          │
│ Total: LKR 59,145                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ BUDGET STATUS:                               │
│                                              │
│ Estimated Budget: LKR 50,000                 │
│ Spent This Week: LKR 59,145                  │
│ Status: ⚠️ Over estimate by LKR 9,145        │
│                                              │
│ Spending Cap: LKR 60,000                     │
│ Remaining: LKR 855                           │
│ Status: ⚠️ APPROACHING CAP!                  │
│                                              │
│ ⚠️ ALERT: Company must pause work and        │
│ request cap increase if more time needed.    │
│                                              │
│ [💬 DISCUSS WITH COMPANY]                    │
│ [⚙️ INCREASE SPENDING CAP]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ INVOICE COMING:                              │
│                                              │
│ Next invoice date: Friday, Jan 19            │
│ Will include: Jan 15-19 work (5 days)        │
│ Est. amount: ~LKR 60,000                     │
│ Payment due: Within 3 days (Jan 22)          │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📥 DOWNLOAD TIME REPORT]                    │
│ [📧 EMAIL REPORT TO ME]                      │
│ [💬 CHAT WITH COMPANY]                       │
│ [⚙️ CONTRACT SETTINGS]                       │
│                                              │
└──────────────────────────────────────────────┘
```

### **Phase 6-T&M: Weekly Invoice & Payment**

**Friday evening - Company issues invoice:**

```
┌──────────────────────────────────────────────┐
│ 📄 INVOICE RECEIVED                          │
│ Contract #CNT-2026-004                       │
├──────────────────────────────────────────────┤
│                                              │
│ INVOICE #INV-2026-001                        │
│ From: ABC Construction (Pvt) Ltd             │
│ Date: Friday, January 19, 2026               │
│                                              │
│ Billing Period: Jan 15-19, 2026 (Week 1)     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ LABOR CHARGES:                               │
│                                              │
│ Monday, Jan 15: 7.0 hours × LKR 2,500        │
│ = LKR 17,500                                 │
│                                              │
│ Tuesday, Jan 16: 7.5 hours × LKR 2,500       │
│ = LKR 18,750                                 │
│                                              │
│ Wednesday, Jan 17: 8.0 hours × LKR 2,500     │
│ = LKR 20,000                                 │
│                                              │
│ Thursday, Jan 18: 6.5 hours × LKR 2,500      │
│ = LKR 16,250                                 │
│                                              │
│ Friday, Jan 19: 5.0 hours × LKR 2,500        │
│ = LKR 12,500                                 │
│                                              │
│ Total Hours: 34.0 hours                      │
│ Total Labor: LKR 85,000                      │
│                                              │
│ [📊 View Detailed Time Logs]                 │
│                                              │
│ ──────────────────────────────────────────   │
│                                              │
│ MATERIAL CHARGES:                            │
│                                              │
│ Sink basin (Jan 16)                          │
│ Cost: LKR 8,500 + 10% = LKR 9,350            │
│ 📎 Receipt attached                          │
│                                              │
│ Faucet set (Jan 16)                          │
│ Cost: LKR 3,200 + 10% = LKR 3,520            │
│ 📎 Receipt attached                          │
│                                              │
│ Pipes & fittings (Jan 17)                    │
│ Cost: LKR 2,800 + 10% = LKR 3,080            │
│ 📎 Receipt attached                          │
│                                              │
│ Sealant & supplies (Jan 18)                  │
│ Cost: LKR 1,200 + 10% = LKR 1,320            │
│ 📎 Receipt attached                          │
│                                              │
│ Total Materials: LKR 17,270                  │
│                                              │
│ [📥 Download All Receipts]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ INVOICE SUMMARY:                             │
│                                              │
│ Labor (34 hrs): LKR 85,000                   │
│ Materials: LKR 17,270                        │
│ ─────────────────────────                    │
│ Subtotal: LKR 102,270                        │
│                                              │
│ ⚠️ WARNING: OVER BUDGET!                     │
│                                              │
│ Original Estimate: LKR 50,000                │
│ Actual Cost Week 1: LKR 102,270              │
│ Over Budget: LKR 52,270 (+104%)              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ EXPLANATION FROM COMPANY:                    │
│                                              │
│ "During removal, we discovered significant   │
│ corrosion in main pipes requiring complete   │
│ replacement (not visible in initial          │
│ inspection). Additional 15 hours spent on    │
│ unexpected pipe replacement. New pipes and   │
│ fittings added to materials. This was        │
│ necessary to prevent future leaks and        │
│ ensure safety. Photos and details shared     │
│ via chat on Jan 16."                         │
│                                              │
│ [💬 View Chat Discussion]                    │
│ [📷 View Problem Photos (8 photos)]          │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ ⚠️ PAYMENT DUE: January 22, 2026             │
│ Amount: LKR 102,270                          │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ YOUR OPTIONS:                                │
│                                              │
│ [✅ APPROVE & PAY INVOICE]                   │
│ If work is legitimate and necessary          │
│                                              │
│ [❓ DISPUTE INVOICE]                         │
│ If you question hours or charges             │
│                                              │
│ [💬 DISCUSS WITH COMPANY]                    │
│ Ask questions before deciding                │
│                                              │
│ [📞 REQUEST SUPPORT]                         │
│ Get FixLanka mediation help                  │
│                                              │
└──────────────────────────────────────────────┘
```

### **Key Differences - Time & Material:**

**Compared to All Fixed-Price Methods:**
- ⚠️ **Variable cost:** Final price unknown until completion
- ⚠️ **Can exceed estimate:** No fixed budget limit (with cap)
- ✅ **Transparent:** See exactly where money goes
- ✅ **Fair pricing:** Only pay for actual work done
- ✅ **Flexible scope:** Can add/remove work easily
- 📊 **More admin:** Weekly invoicing and tracking
- 🔍 **Requires monitoring:** Must watch hours and costs

**Best For:**
- Complex projects with unknowns
- Repair work (hidden issues likely)
- Projects needing flexibility
- Customers who trust company
- When scope can't be defined upfront

---

## **COMPARISON SUMMARY: ALL 4 PAYMENT METHODS**

```
═══════════════════════════════════════════════
PAYMENT METHOD COMPARISON (WITH ESCROW PROTECTION)
═══════════════════════════════════════════════

METHOD 1: MILESTONE-BASED
├─ Complexity: ⭐⭐⭐⭐⭐ (Most complex)
├─ Customer Control: ⭐⭐⭐⭐⭐ (Most control)
├─ Upfront Payment: 0-25% typical
├─ Escrow Protection: ✅ Each milestone payment held until approved
├─ Milestone Plan: ✅ Required
├─ Chat Opens: After plan submitted
├─ Payment Points: 3-5 milestones
├─ Verification Points: At each milestone
├─ Budget Type: Fixed (within flex range)
├─ Timeline: Detailed per milestone
├─ Best For: Large projects, complex work
└─ Escrow: Milestone payment → Escrow → Work done → Approve → Release

METHOD 2: UPFRONT + FINAL (50-50)
├─ Complexity: ⭐⭐⭐ (Medium)
├─ Customer Control: ⭐⭐⭐ (Medium)
├─ Upfront Payment: 50%
├─ Escrow Protection: ✅ Upfront held until work starts, final until complete
├─ Milestone Plan: ❌ Not required
├─ Chat Opens: Immediately
├─ Payment Points: 2 (upfront + final)
├─ Verification Points: Work start + completion
├─ Budget Type: Fixed
├─ Timeline: Single start/end date
├─ Best For: Medium projects, trusted companies
└─ Escrow: Upfront → Escrow → Work starts (verified) → Release
          Final → Escrow → Work complete (verified) → Release

METHOD 3: UPFRONT + FINAL (30-70)
├─ Complexity: ⭐⭐⭐ (Medium)
├─ Customer Control: ⭐⭐⭐⭐ (High)
├─ Upfront Payment: 30%
├─ Escrow Protection: ✅ Both payments held (30% + 70%)
├─ Milestone Plan: ❌ Not required
├─ Chat Opens: Immediately
├─ Payment Points: 2 (upfront + final)
├─ Verification Points: Work start + completion
├─ Budget Type: Fixed
├─ Timeline: Single start/end date
├─ Best For: Medium projects, less upfront risk
└─ Escrow: 30% → Escrow → Work starts (verified) → Release
          70% → Escrow → Work complete (verified) → Release

METHOD 4: AFTER COMPLETION (100%)
├─ Complexity: ⭐ (Simplest)
├─ Customer Control: ⭐⭐⭐⭐⭐ (Most control)
├─ Upfront Payment: 0%
├─ Escrow Protection: ✅ Full payment held with 7-day guarantee
├─ Milestone Plan: ❌ Not required
├─ Chat Opens: Immediately
├─ Payment Points: 1 (final only)
├─ Verification Points: At completion + 7-day guarantee
├─ Budget Type: Fixed
├─ Timeline: Single start/end date
├─ Best For: Small projects, maximum trust, established companies
└─ Escrow: 100% → Escrow → 7-day inspection → Approve → Release

METHOD 5: TIME & MATERIAL
├─ Complexity: ⭐⭐⭐⭐ (High admin)
├─ Customer Control: ⭐⭐⭐ (Medium)
├─ Upfront Payment: 0% (weekly billing)
├─ Escrow Protection: ⚠️ Limited (weekly invoices, not full escrow)
├─ Milestone Plan: ❌ Not required
├─ Chat Opens: Immediately
├─ Payment Points: Weekly invoices
├─ Verification Points: Weekly + final
├─ Budget Type: Variable (estimate only)
├─ Timeline: Flexible, hour-based
├─ Best For: Complex, unknown scope projects
└─ Escrow: Weekly invoices paid directly after approval

═══════════════════════════════════════════════

🔒 ESCROW PROTECTION SUMMARY:

MILESTONE-BASED:
• Every milestone payment held in escrow
• Released only after customer approves that milestone
• Maximum granular control
• Example: M1 payment held → M1 complete → Customer approves → Released

50-50 UPFRONT + FINAL:
• Upfront (50%) held until work starts
• Company submits start proof with photo
• Customer verifies (24hr window)
• Released after verification
• Final (50%) held until work complete
• Customer inspects and approves
• Released after approval

30-70 UPFRONT + FINAL:
• Same as 50-50 but different split
• Upfront (30%) held until work starts
• Final (70%) held until complete
• Lower upfront risk for customer

AFTER COMPLETION (100%):
• Zero upfront payment
• Full 100% paid after work complete
• Payment held in escrow for 7 DAYS
• Customer has 7 days to inspect thoroughly
• Request fixes during this period (free)
• Approve when satisfied
• Released after approval or auto-release after 7 days
• Maximum customer protection

TIME & MATERIAL:
• No traditional escrow (weekly billing)
• Weekly invoices reviewed and approved
• Customer approves before payment
• Can dispute any invoice
• Spending cap provides budget protection

═══════════════════════════════════════════════

WHY ESCROW MATTERS:

FOR CUSTOMERS:
✅ Money protected until work actually done
✅ Can't be scammed by companies taking money and disappearing
✅ Verification required before payment release
✅ Dispute resolution if issues arise
✅ Full or partial refunds possible
✅ Peace of mind and trust

FOR COMPANIES:
✅ Payment guaranteed when work done properly
✅ No payment disputes after release
✅ Builds customer confidence = more bookings
✅ Professional reputation enhanced
✅ FixLanka mediates any disputes fairly

FOR PLATFORM:
✅ Trust and safety for all parties
✅ Reduced fraud and scams
✅ Higher customer satisfaction
✅ More completed projects
✅ Better dispute resolution

═══════════════════════════════════════════════

ESCROW FEES:

• No extra fees for escrow protection
• Included in standard platform fees
• FixLanka absorbs escrow costs
• Transaction fees only (2-3% on payments)
• Worth it for peace of mind

═══════════════════════════════════════════════

DECISION FLOWCHART (WITH ESCROW):

Is project scope clearly defined?
│
├─ YES: Choose fixed-price method
│   │
│   ├─ Large/complex project?
│   │   └─ YES → MILESTONE-BASED
│   │       (Maximum control, escrow per milestone)
│   │
│   ├─ Medium project, need control?
│   │   └─ YES → 30-70 UPFRONT+FINAL
│   │       (Lower upfront, both payments in escrow)
│   │
│   ├─ Medium project, trust company?
│   │   └─ YES → 50-50 UPFRONT+FINAL
│   │       (Balanced split, both payments in escrow)
│   │
│   └─ Small project, established company?
│       └─ YES → AFTER COMPLETION
│           (Zero risk, 7-day escrow guarantee)
│
└─ NO: Scope unclear or likely to change
    └─ TIME & MATERIAL (with spending cap)
        (Weekly billing, no traditional escrow)

═══════════════════════════════════════════════

KEY TAKEAWAY:

ALL payment methods (except T&M) now have ESCROW
PROTECTION, making FixLanka the safest platform for
both customers and companies!

Customers: Never pay without seeing work done first!
Companies: Get paid guaranteed when work is done right!

═══════════════════════════════════════════════
```

---

# **PHASE 4: MILESTONE PLAN SUBMISSION** (Issue #6)
*This phase only applies to MILESTONE-BASED payment method*

## **Step 4.1: Company Receives Action Required Alert**

### **What Happens:**
Company logs into their account and sees prominent alerts about milestone plan requirement.

### **Company Dashboard View:**

```
┌──────────────────────────────────────────────┐
│ Welcome back, ABC Construction! 👷           │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│ ⚠️ URGENT ACTION REQUIRED (1)                │
├──────────────────────────────────────────────┤
│                                              │
│ 📋 ADD MILESTONE PLAN - DEADLINE APPROACHING │
│                                              │
│ Contract #CNT-2026-001                       │
│ Kitchen Sink Repair - John Silva             │
│                                              │
│ ⏰ DEADLINE: 46 hours 15 minutes remaining   │
│    Jan 27, 2026 at 2:30 PM                  │
│                                              │
│ Customer is waiting for you to submit a     │
│ detailed milestone plan. Contract will not   │
│ activate until plan is submitted and        │
│ approved.                                    │
│                                              │
│ What to include:                             │
│ • Break project into 3-5 milestones         │
│ • Set payment amount per milestone          │
│ • Define duration for each phase            │
│ • List deliverables                         │
│ • Add any dependencies                      │
│                                              │
│ [➕ ADD MILESTONE PLAN NOW] ← Big green btn │
│                                              │
└──────────────────────────────────────────────┘
```

### **Email Reminder Company Received:**

```
Subject: ⚠️ Action Required: Add Milestone Plan
        Contract #CNT-2026-001

Dear ABC Construction,

Customer John Silva accepted your quotation!
Your contract has been created, but ACTION IS
REQUIRED to activate it.

CONTRACT: #CNT-2026-001
PROJECT: Kitchen Sink Repair
CUSTOMER: John Silva
BUDGET: LKR 50,000 (Flexible ±10%)

⚠️ YOU MUST SUBMIT A MILESTONE PLAN WITHIN 48 HOURS

Deadline: January 27, 2026 at 2:30 PM
Time Remaining: 46 hours

Why this matters:
• Contract won't activate without plan
• Customer may cancel if delayed
• Chat won't open until plan submitted
• Your reputation affects future business

What to submit:
✓ Break project into logical phases (3-5 milestones)
✓ Set payment amount per milestone
✓ Ensure total = LKR 50,000 (or within ±10%)
✓ Define deliverables per milestone
✓ Set duration per phase (total ≤ 20 days)
✓ Add dependencies if any

[ADD MILESTONE PLAN NOW →]

Need help?
• View milestone planning guide
• See example plans
• Use milestone templates
• Contact support

Best regards,
FixLanka Team
```

---

## **Step 4.2: Company Accesses Contract**

### **What Happens:**
Company clicks "Add Milestone Plan" from any location.

### **What Company Sees - Contract Overview:**

```
CONTRACT #CNT-2026-001
Kitchen Sink Repair

Status: ⚠️ ACTION REQUIRED - Add Milestone Plan

═══════════════════════════════════════════════

⚠️ SUBMIT MILESTONE PLAN REQUIRED

Deadline: Jan 27, 2026 at 2:30 PM
Time Remaining: 46 hours 15 minutes

[➕ ADD MILESTONE PLAN NOW] ← Primary action

═══════════════════════════════════════════════

PROJECT INFORMATION

Customer: John Silva
Location: Colombo 07
Phone: Will be available after plan approved

Project Description:
"Repair and replace kitchen sink with new 
fixtures. Old sink is leaking and needs 
complete replacement. Includes new faucet 
and drain system installation."

Original Request: REQ-2026-1234
Photos: 3 photos attached
[View Customer Request Details →]

═══════════════════════════════════════════════

BUDGET CONSTRAINTS (Issue #1)

Total Budget: LKR 50,000
Budget Type: Flexible (±10%)

⚠️ IMPORTANT: Your milestone payments must
total within this range:

Minimum Allowed: LKR 45,000
Maximum Allowed: LKR 55,000
Your Quotation: LKR 50,000

You can adjust total within ±10% range if
material costs change, but milestone plan
must be within range when submitted.

═══════════════════════════════════════════════

PAYMENT TERMS (Issue #2)

Payment Method: Milestone-based
Pricing Type: Fixed Price

What this means:
• You must break project into milestones
• Payment released per milestone completion
• Customer approves each milestone
• Payment automatic after approval
• Total must match budget (±10%)

Typical milestone breakdown:
• 3-5 milestones recommended
• Clear deliverables per milestone
• Measurable completion criteria
• Reasonable payment distribution

═══════════════════════════════════════════════

TIMELINE CONSTRAINTS

Start Date: January 15, 2026
End Date: February 4, 2026
Maximum Duration: 20 days

⚠️ Your milestones must fit within 20 days

You can have shorter duration but not longer.
Plan contingency time for delays.

═══════════════════════════════════════════════

WHAT TO INCLUDE IN MILESTONE PLAN

Required for Each Milestone:
✓ Milestone name/title
✓ Description (minimum 50 characters)
✓ Payment amount
✓ Duration in days
✓ Start and end dates
✓ At least 1 deliverable
✓ Payment trigger (start/completion/approval)
✓ Dependencies (if any)

Optional:
• Photos or diagrams
• Material list
• Special notes
• Attachments

═══════════════════════════════════════════════

TIPS FOR GOOD MILESTONE PLANS

✓ Be specific and measurable
✓ Allow customer inspection points
✓ Break work into logical phases
✓ Front-load material procurement
✓ Include buffer time for delays
✓ List clear deliverables
✓ Define completion criteria

Common mistakes to avoid:
✗ Too many milestones (confusing)
✗ Vague descriptions
✗ Unrealistic timelines
✗ Uneven payment distribution
✗ Missing deliverables

═══════════════════════════════════════════════

HELPFUL RESOURCES

[📋 View Milestone Templates]
[👁️ See Example Plans]
[📚 Milestone Planning Guide]
[❓ FAQ]

Similar past projects:
• Kitchen Sink Installation (3 milestones)
• Bathroom Plumbing Repair (4 milestones)
• Kitchen Renovation (5 milestones)

[View Similar Projects →]

═══════════════════════════════════════════════

[➕ START ADDING MILESTONE PLAN]
[💾 Load Template]
[📞 Contact Support]
```

---

## **Step 4.3: Company Fills Milestone Planning Form**

### **What Happens:**
Company clicks "Add Milestone Plan" and opens comprehensive planning form.

### **Milestone Planning Form:**

```
═══════════════════════════════════════════════
ADD MILESTONE PLAN
Contract #CNT-2026-001 - Kitchen Sink Repair
═══════════════════════════════════════════════

CONTRACT CONSTRAINTS REMINDER:

Budget: LKR 50,000 (Flexible ±10%)
Range: LKR 45,000 - 55,000
Timeline: Maximum 20 days
Payment: Milestone-based

⚠️ All milestone payments must total within
   budget range. All milestone durations must
   total ≤ 20 days.

[💾 Save as Draft] [👁️ Load Template]

═══════════════════════════════════════════════

MILESTONE 1

Milestone Name: * (Required)
┌─────────────────────────────────────────────┐
│ Site Preparation & Material Procurement     │
└─────────────────────────────────────────────┘

Description: * (Minimum 50 characters)
┌─────────────────────────────────────────────┐
│ This milestone covers initial site          │
│ preparation and all material procurement.   │
│                                             │
│ Work includes:                              │
│ • Clear and protect work area               │
│ • Remove old sink and fixtures safely       │
│ • Inspect existing plumbing system          │
│ • Check for any structural issues           │
│ • Procure all required materials            │
│ • Verify material quality and quantity      │
│ • Prepare installation area                 │
│ • Ensure workspace is ready                 │
│                                             │
│ Character count: 287 ✓                      │
└─────────────────────────────────────────────┘

Duration: * (Required)
┌───┐ days
│ 5 │
└───┘

Start Date: * (Auto-calculated from contract)
January 15, 2026

End Date: (Auto-calculated from duration)
January 19, 2026

Payment Amount: * (Required)
LKR ┌───────────┐
    │ 12,500.00 │
    └───────────┘

Percentage of Total: 25% ✓
Status: ✅ Within budget range

Payment Trigger: * (Required)
( ) At start of milestone
(•) On completion of milestone ← Selected
( ) After customer approval of deliverables

Deliverables: * (At least 1 required)
┌─────────────────────────────────────────────┐
│ ☑ Work area cleared and protected          │
│ ☑ Old sink and fixtures removed             │
│ ☑ Plumbing system inspected and documented │
│ ☑ All materials procured and verified      │
│ ☑ Installation area prepared and ready     │
└─────────────────────────────────────────────┘
[+ Add Another Deliverable]

Dependencies:
(•) None - Can start immediately
( ) Depends on another milestone

Attachments: (Optional)
📎 Material_List.pdf (125 KB) [X Remove]
[📎 Add More Files]

Photos/Diagrams: (Optional)
[📷 Upload Photos]

Notes for Customer: (Optional)
┌─────────────────────────────────────────────┐
│ Weather-dependent work. Slight delays       │
│ possible if heavy rain during this phase.   │
└─────────────────────────────────────────────┘

[➕ Add Another Milestone] [❌ Remove This Milestone]

═══════════════════════════════════════════════

MILESTONE 2

Milestone Name: *
┌─────────────────────────────────────────────┐
│ Main Installation Work                      │
└─────────────────────────────────────────────┘

Description: *
┌─────────────────────────────────────────────┐
│ This is the main installation phase where   │
│ the actual sink replacement and plumbing    │
│ work will be completed.                     │
│                                             │
│ Work includes:                              │
│ • Install new plumbing pipes                │
│ • Connect water supply lines properly       │
│ • Install drainage system                   │
│ • Mount new sink and countertop             │
│ • Install faucet and fixtures               │
│ • Connect all plumbing                      │
│ • Test water pressure and flow              │
│ • Check for any leaks thoroughly            │
│ • Ensure proper drainage                    │
│ • Initial functional testing                │
│                                             │
│ Character count: 398 ✓                      │
└─────────────────────────────────────────────┘

Duration: *
┌────┐ days
│ 10 │
└────┘

Start Date: (Auto-calculated)
January 20, 2026
(Starts after Milestone 1)

End Date: (Auto-calculated)
January 29, 2026

Payment Amount: *
LKR ┌───────────┐
    │ 25,000.00 │
    └───────────┘

Percentage: 50% ✓
Status: ✅ Within budget range

Payment Trigger: *
(•) On completion of milestone

Deliverables: *
┌─────────────────────────────────────────────┐
│ ☑ All new plumbing installed and tested    │
│ ☑ Sink properly mounted and secured        │
│ ☑ Water supply lines connected             │
│ ☑ Drainage system installed                │
│ ☑ No leaks detected                        │
│ ☑ Water pressure satisfactory              │
│ ☑ All fixtures installed and working       │
└─────────────────────────────────────────────┘
[+ Add Another Deliverable]

Dependencies:
( ) None
(•) Depends on: Milestone 1 (Site Preparation)
    ↑ Cannot start until M1 is completed

[➕ Add Another Milestone] [❌ Remove This Milestone]

═══════════════════════════════════════════════

MILESTONE 3

Milestone Name: *
┌─────────────────────────────────────────────┐
│ Finishing & Quality Check                   │
└─────────────────────────────────────────────┘

Description: *
┌─────────────────────────────────────────────┐
│ Final finishing work and comprehensive      │
│ quality inspection to ensure everything     │
│ meets quality standards.                    │
│                                             │
│ Work includes:                              │
│ • Seal all joints with caulking             │
│ • Install drain covers and accessories      │
│ • Clean work area thoroughly                │
│ • Remove all debris and waste               │
│ • Polish sink and fixtures                  │
│ • Final quality inspection                  │
│ • Test all functions comprehensively        │
│ • Customer walkthrough and approval         │
│ • Address any minor adjustments             │
│                                             │
│ Character count: 356 ✓                      │
└─────────────────────────────────────────────┘

Duration: *
┌───┐ days
│ 3 │
└───┘

Start Date:
January 30, 2026

End Date:
February 1, 2026

Payment Amount: *
LKR ┌───────────┐
    │ 10,000.00 │
    └───────────┘

Percentage: 20% ✓
Status: ✅ Within budget range

Payment Trigger: *
(•) On completion of milestone

Deliverables: *
┌─────────────────────────────────────────────┐
│ ☑ All finishing work completed              │
│ ☑ Area cleaned and debris removed           │
│ ☑ Quality standards met and verified        │
│ ☑ Customer walkthrough completed            │
│ ☑ Minor adjustments addressed               │
│ ☑ All functions tested and working          │
└─────────────────────────────────────────────┘

Dependencies:
(•) Depends on: Milestone 2 (Main Installation)

[➕ Add Another Milestone] [❌ Remove This Milestone]

═══════════════════════════════════════════════

MILESTONE 4

Milestone Name: *
┌─────────────────────────────────────────────┐
│ Final Inspection & Handover                 │
└─────────────────────────────────────────────┘

Description: *
┌─────────────────────────────────────────────┐
│ Final project inspection, documentation,    │
│ and official handover to customer.          │
│                                             │
│ Work includes:                              │
│ • Complete system functionality test        │
│ • Provide all warranty documentation        │
│ • Train customer on usage and maintenance   │
│ • Provide maintenance guidelines            │
│ • Address any final concerns                │
│ • Official project sign-off                 │
│ • Hand over all documentation               │
│                                             │
│ Character count: 298 ✓                      │
└─────────────────────────────────────────────┘

Duration: *
┌───┐ days
│ 2 │
└───┘

Start Date:
February 2, 2026

End Date:
February 4, 2026 ✓ (Matches contract end date)

Payment Amount: *
LKR ┌──────────┐
    │ 2,500.00 │
    └──────────┘

Percentage: 5% ✓
Status: ✅ Within budget range

Payment Trigger: *
(•) After customer approval of deliverables

Deliverables: *
┌─────────────────────────────────────────────┐
│ ☑ All work verified and approved            │
│ ☑ Warranty documents provided               │
│ ☑ Customer trained on usage/maintenance     │
│ ☑ Maintenance guidelines provided           │
│ ☑ Project documentation complete            │
│ ☑ Official sign-off obtained                │
└─────────────────────────────────────────────┘

Dependencies:
(•) Depends on: Milestone 3 (Finishing)

[❌ Remove This Milestone]

═══════════════════════════════════════════════

MILESTONE PLAN SUMMARY

Total Milestones: 4
Total Duration: 20 days ✅ (Fits timeline)
Total Payments: LKR 50,000 ✅ (Matches budget)

┌─────────────────────────────────────────────┐
│ VALIDATION RESULTS                          │
├─────────────────────────────────────────────┤
│ ✅ Payment amounts sum to total budget      │
│ ✅ Timeline fits within contract duration   │
│ ✅ All milestones have descriptions         │
│ ✅ Deliverables defined for each phase      │
│ ✅ Dependencies properly set                │
│ ✅ No scheduling conflicts                  │
│ ✅ All required fields filled               │
└─────────────────────────────────────────────┘

Payment Breakdown Visualization:
┌───────────────────────────────────────────┐
│ M1: 25%  M2: 50%    M3: 20%    M4: 5%    │
│ 12,500   25,000     10,000     2,500     │
└───────────────────────────────────────────┘

Timeline Visualization:
Jan 15─────┤M1├─────Jan 20────────┤M2├─────
           5d                    10d
Jan 30──┤M3├──Feb 2┤M4├──Feb 4
        3d       2d

Additional Notes (Optional):
┌─────────────────────────────────────────────┐
│ • Weather-dependent work may cause slight   │
│   delays in outdoor portions                │
│ • Customer should ensure site access daily  │
│   between 8 AM - 5 PM                       │
│ • All materials covered by 2-year warranty  │
│ • Emergency contact will be provided after  │
│   plan approval                             │
└─────────────────────────────────────────────┘

Overall Attachments:
📎 Work_Schedule.pdf (245 KB)
📎 Material_List.xlsx (128 KB)
[📎 Add More Files]

═══════════════════════════════════════════════

SUBMISSION OPTIONS

[💾 Save as Draft]
• Save progress and return later
• Draft not visible to customer
• Can edit before submitting

[👁️ Preview Plan]
• See how customer will view your plan
• Check formatting and clarity
• Verify all information

[✅ SUBMIT FOR CUSTOMER APPROVAL]
• Sends plan to customer for review
• Customer receives notifications
• Chat opens automatically
• Plan becomes visible to customer
• Cannot edit after submission
  (unless customer requests changes)

⚠️ Before submitting, verify:
☑ All information is accurate
☑ Descriptions are clear
☑ Timeline is realistic
☑ Payments total correctly
☑ Deliverables are specific

═══════════════════════════════════════════════
```

---

## **Step 4.4: System Validates Milestone Plan**

### **What Happens:**
When company clicks "Submit", system performs comprehensive validation.

### **Validation Checks Performed:**

**1. Budget Validation (Issue #1):**
- Check: Sum of all milestone payments
- Expected: Must equal contract budget OR be within flexible range
- This case: LKR 50,000 (exact match) ✅
- Flexible range: LKR 45,000 - 55,000
- Result: PASS

**2. Timeline Validation:**
- Check: Sum of all milestone durations
- Expected: Must be ≤ contract duration (20 days)
- This case: 5 + 10 + 3 + 2 = 20 days ✅
- Result: PASS

**3. Date Sequence Validation:**
- Check: End date matches or is before contract end date
- Contract end: February 4, 2026
- Plan end: February 4, 2026 ✅
- Result: PASS

**4. Required Fields Validation:**
For each milestone, check:
- Name: ✅ All filled
- Description: ✅ All ≥ 50 characters
- Payment amount: ✅ All filled and > 0
- Duration: ✅ All filled and > 0
- Deliverables: ✅ All have at least 1
- Result: PASS

**5. Dependency Logic Validation:**
- Check: Dependent milestones come after dependencies
- M2 depends on M1: M1 is before M2 ✅
- M3 depends on M2: M2 is before M3 ✅
- M4 depends on M3: M3 is before M4 ✅
- Result: PASS

**6. Payment Term Compliance (Issue #2):**
- Contract payment term: Milestone-based ✅
- Plan structure: 4 milestones with payments ✅
- Result: PASS

**All Validations Pass:**
System proceeds to submit plan.

**If Validation Fails:**
- Error messages shown in red
- Specific issues highlighted
- Form remains editable
- Cannot submit until fixed
- Errors listed at top of form

---

## **Step 4.5: Company Submits Milestone Plan**

### **What Happens:**
After validation passes, system processes submission.

### **System Actions (in order):**

**1. Save Milestone Plan:**
- Creates milestone plan record
- Links to contract
- Stores all milestone details
- Records submission timestamp
- Sets plan version to 1
- Status: "submitted"

**2. Update Contract Status:**
- Changes from: "pending_milestone_plan"
- Changes to: "pending_customer_approval"
- Records plan submission time
- Calculates review deadline (customer has reasonable time)

**3. Activate Chat Room (Issue #4):**
- Changes chat status from: "inactive"
- Changes to: "active"
- Chat becomes available to both parties
- System logs chat activation
- Chat ready for use

**4. Create Milestone Records:**
- Creates 4 separate milestone records
- One for each milestone in plan
- Status for all: "pending_approval"
- Links each to contract
- Stores all details per milestone

**5. Generate Notifications:**
- Customer notification: "Milestone plan submitted - Review needed"
- Company notification: "Plan submitted successfully"
- Create notification records in database

**6. Send Email to Customer:**
- Plan submission notification
- Link to review plan
- Explanation of what to do
- Chat availability notice

**7. Send SMS to Customer:**
- Brief alert about plan submission
- Short link to review

**8. Send In-App Notifications:**
- Notification badge appears
- Push notification if enabled
- Dashboard widget updates

**9. Send Email to Company:**
- Confirmation of submission
- Copy of submitted plan
- What happens next
- Expected response timeframe

**10. Create Audit Log:**
- Log plan submission action
- Record all status changes
- Timestamp everything
- Link to user who submitted

### **Confirmation Shown to Company:**

```
┌──────────────────────────────────────────────┐
│ ✅ MILESTONE PLAN SUBMITTED SUCCESSFULLY!    │
├──────────────────────────────────────────────┤
│                                              │
│ Your milestone plan for Contract             │
│ #CNT-2026-001 has been submitted to          │
│ customer John Silva for review.              │
│                                              │
│ 📋 Plan Details:                             │
│ • 4 Milestones                               │
│ • 20 days total duration                     │
│ • LKR 50,000 total budget                    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 CHAT NOW AVAILABLE!                       │
│                                              │
│ Chat has been activated with John Silva.     │
│ You can now communicate directly about       │
│ this project.                                │
│                                              │
│ [💬 OPEN CHAT NOW]                           │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT HAPPENS NEXT:                           │
│                                              │
│ 1. Customer Receives Notification            │
│    • Email, SMS, and in-app alert sent       │
│    • Customer will review your plan          │
│                                              │
│ 2. Customer Reviews Plan                     │
│    • Reviews milestone breakdown             │
│    • Can ask questions via chat              │
│    • Makes decision                          │
│                                              │
│ 3. Three Possible Outcomes:                  │
│                                              │
│    OPTION A: Customer Approves ✅            │
│    → Contract activates immediately          │
│    → Work can begin on start date            │
│    → You receive approval notification       │
│    → Milestone 1 becomes active              │
│                                              │
│    OPTION B: Customer Requests Changes 📝    │
│    → Customer specifies what to change       │
│    → Discuss via chat                        │
│    → Submit revised plan (Version 2)         │
│    → Customer reviews revised plan           │
│                                              │
│    OPTION C: Customer Rejects Plan ❌        │
│    → Discuss concerns via chat               │
│    → Negotiate new plan                      │
│    → Or cancel contract if no agreement      │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ CUSTOMER REVIEW TIMELINE:                    │
│                                              │
│ Expected: Within 24-48 hours                 │
│ You'll be notified when customer decides     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT YOU CAN DO NOW:                         │
│                                              │
│ • Monitor chat for customer questions        │
│ • Prepare to start work on Jan 15           │
│ • Review submitted plan                      │
│ • Wait for customer approval                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ NOTIFICATIONS:                               │
│                                              │
│ You will be notified via:                    │
│ ✅ Email                                     │
│ ✅ SMS                                       │
│ ✅ In-app notification                       │
│ ✅ Chat messages                             │
│                                              │
│ When customer:                               │
│ • Approves plan                              │
│ • Requests changes                           │
│ • Rejects plan                               │
│ • Sends chat message                         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📋 VIEW SUBMITTED PLAN]                     │
│ [💬 OPEN CHAT]                               │
│ [📄 DOWNLOAD PLAN PDF]                       │
│ [🏠 GO TO DASHBOARD]                         │
│                                              │
└──────────────────────────────────────────────┘
```

---

# **PHASE 5: CHAT OPENS & CUSTOMER REVIEWS** (Issues #4, #6)

## **Step 5.1: Customer Receives Plan Submission Notifications**

### **What Happens:**
Customer is notified through multiple channels about milestone plan submission.

### **Email Notification to Customer:**

```
Subject: 📋 Milestone Plan Submitted - Review Required
         Contract #CNT-2026-001

Dear John Silva,

Great news! ABC Construction has submitted a
detailed milestone plan for your project.

CONTRACT: #CNT-2026-001
PROJECT: Kitchen Sink Repair
STATUS: Ready for Your Review

═══════════════════════════════════════════════

MILESTONE PLAN SUBMITTED

ABC Construction has broken down your project
into 4 milestones with clear deliverables and
payment schedule:

Milestone 1: Site Preparation (5 days, 25%)
Milestone 2: Main Installation (10 days, 50%)
Milestone 3: Finishing Work (3 days, 20%)
Milestone 4: Final Inspection (2 days, 5%)

Total: 20 days, LKR 50,000

═══════════════════════════════════════════════

💬 CHAT IS NOW AVAILABLE!

You can now communicate directly with ABC
Construction to ask questions about the plan,
discuss concerns, or negotiate changes.

Chat Features:
✓ Real-time messaging
✓ Photo sharing
✓ File attachments
✓ No phone numbers shared (privacy)

[OPEN CHAT NOW →]

═══════════════════════════════════════════════

ACTION REQUIRED: REVIEW & DECIDE

Please review the milestone plan carefully and
choose one of these options:

1. APPROVE PLAN ✅
   Work begins immediately after approval

2. REQUEST CHANGES 📝
   Specify what you want changed

3. REJECT PLAN ❌
   Discuss concerns with company

═══════════════════════════════════════════════

[VIEW MILESTONE PLAN →] ← Primary button
[Open Chat]
[Contact Support]

Best regards,
FixLanka Team
```

### **SMS Notification:**
```
📋 Milestone plan submitted for Contract
CNT-2026-001. Review needed. Chat now active.
View: [short-link]
```

### **In-App Notification:**
```
Notification Badge: 1 new

Milestone Plan Submitted
Contract #CNT-2026-001
ABC Construction submitted detailed plan
💬 Chat now available
[Review Plan →]
2 minutes ago
```

### **Push Notification:**
```
📋 Milestone Plan Ready for Review
ABC Construction - Kitchen Sink Repair
Tap to review and chat
```

---

## **Step 5.2: Customer Accesses Contract to Review Plan**

### **What Happens:**
Customer clicks any notification or accesses contract from one of 5 locations (Issue #5).

### **Contract Page - Updated View:**

```
CONTRACT #CNT-2026-001
Kitchen Sink Repair

Status: 📋 PENDING YOUR REVIEW

[Contract Details] [Milestone Plan] [Chat] [Files]
                        ↑ Active tab

═══════════════════════════════════════════════

🆕 MILESTONE PLAN SUBMITTED - ACTION REQUIRED

ABC Construction submitted a detailed milestone
plan for your review and approval.

Submitted: January 25, 2026 at 4:30 PM
(15 minutes ago)

┌─────────────────────────────────────────────┐
│                                             │
│ 💬 CHAT NOW AVAILABLE!                      │
│                                             │
│ You can now discuss this plan directly with │
│ ABC Construction using our chat system.     │
│                                             │
│ Ask questions, request clarifications, or   │
│ negotiate changes.                          │
│                                             │
│ [💬 OPEN CHAT NOW] ← Blue button            │
│                                             │
└─────────────────────────────────────────────┘

═══════════════════════════════════════════════

MILESTONE PLAN SUMMARY

Total Milestones: 4
Total Duration: 20 days (Jan 15 - Feb 4)
Total Budget: LKR 50,000
Payment Method: Milestone-based (4 payments)

═══════════════════════════════════════════════

MILESTONE 1: SITE PREPARATION & MATERIAL PROCUREMENT

Duration: 5 days (Jan 15 - Jan 19)
Payment: LKR 12,500 (25%)
Payment Trigger: On milestone completion

Description:
This milestone covers initial site preparation
and all material procurement.

Work includes:
• Clear and protect work area
• Remove old sink and fixtures safely
• Inspect existing plumbing system
• Check for any structural issues
• Procure all required materials
• Verify material quality and quantity
• Prepare installation area
• Ensure workspace is ready

Deliverables:
☑ Work area cleared and protected
☑ Old sink and fixtures removed
☑ Plumbing system inspected and documented
☑ All materials procured and verified
☑ Installation area prepared and ready

Dependencies: None (Starts immediately)

Company Notes:
"Weather-dependent work. Slight delays possible
if heavy rain during this phase."

Attachments:
📎 Material_List.pdf (125 KB) [Download]

[💬 Ask About This Milestone →]

─────────────────────────────────────────────

MILESTONE 2: MAIN INSTALLATION WORK

Duration: 10 days (Jan 20 - Jan 29)
Payment: LKR 25,000 (50%)
Payment Trigger: On milestone completion

Description:
This is the main installation phase where the
actual sink replacement and plumbing work will
be completed.

Work includes:
• Install new plumbing pipes
• Connect water supply lines properly
• Install drainage system
• Mount new sink and countertop
• Install faucet and fixtures
• Connect all plumbing
• Test water pressure and flow
• Check for any leaks thoroughly
• Ensure proper drainage
• Initial functional testing

Deliverables:
☑ All new plumbing installed and tested
☑ Sink properly mounted and secured
☑ Water supply lines connected
☑ Drainage system installed
☑ No leaks detected
☑ Water pressure satisfactory
☑ All fixtures installed and working

Dependencies: Milestone 1 must be completed first

[💬 Ask About This Milestone →]
[👁️ Expand for Full Details]

─────────────────────────────────────────────

MILESTONE 3: FINISHING & QUALITY CHECK

Duration: 3 days (Jan 30 - Feb 1)
Payment: LKR 10,000 (20%)
[👁️ Expand...]

─────────────────────────────────────────────

MILESTONE 4: FINAL INSPECTION & HANDOVER

Duration: 2 days (Feb 2 - Feb 4)
Payment: LKR 2,500 (5%)
[👁️ Expand...]

═══════════════════════════════════════════════

TIMELINE VISUALIZATION

Jan 15────────┤ M1 ├────────Jan 20──────────┤ M2 ├────
              5 days                       10 days

Jan 30──┤M3├──Feb 2┤M4├──Feb 4
        3d       2d

Progress tracking will begin after you approve.

═══════════════════════════════════════════════

PAYMENT SCHEDULE

┌─────────────────────────────────────────────┐
│                                             │
│  M1: 25%  │  M2: 50%  │  M3: 20%  │ M4: 5% │
│  12,500   │  25,000   │  10,000   │ 2,500  │
│                                             │
│  All payments released on milestone         │
│  completion and your approval.              │
│                                             │
└─────────────────────────────────────────────┘

Total Budget: LKR 50,000 (Flexible ±10%)
Your budget flexibility allows adjustments if
needed during project execution.

═══════════════════════════════════════════════

COMPANY'S ADDITIONAL NOTES

"• Weather-dependent work may cause slight
   delays in outdoor portions
 • Customer should ensure site access daily
   between 8 AM - 5 PM
 • All materials covered by 2-year warranty
 • Emergency contact will be provided after
   plan approval"

═══════════════════════════════════════════════

ATTACHMENTS

📄 Work_Schedule.pdf (245 KB) [Download]
📄 Material_List.xlsx (128 KB) [Download]
[Download All Attachments]

═══════════════════════════════════════════════

💬 QUESTIONS OR CONCERNS?

Chat is now active! You can discuss this plan
directly with ABC Construction.

Chat Features Available:
✓ Text messaging
✓ Ask questions about specific milestones
✓ Request clarifications
✓ Negotiate changes
✓ Share concerns
✓ File sharing
✓ All communication documented
✓ No phone numbers shared (privacy)

[💬 OPEN CHAT NOW] ← Primary blue button

═══════════════════════════════════════════════

⚠️ YOUR DECISION REQUIRED

Please review the milestone plan carefully and
choose one of the following options:

┌─────────────────────────────────────────────┐
│                                             │
│ [✅ APPROVE PLAN - START WORK]              │
│          ↑ Big green button                 │
│                                             │
│ What happens if you approve:                │
│ ✓ Contract becomes fully active             │
│ ✓ Payment schedule locked                   │
│ ✓ Company can start work on Jan 15         │
│ ✓ Milestone 1 becomes active                │
│ ✓ Progress tracking begins                  │
│ ✓ Chat remains active for updates           │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│                                             │
│ [📝 REQUEST CHANGES]                        │
│          ↑ Orange button                    │
│                                             │
│ What happens if you request changes:        │
│ • Form opens to specify changes needed      │
│ • Chat opens with your change request       │
│ • Company reviews and responds              │
│ • Company submits revised plan (Version 2)  │
│ • You review revised plan                   │
│ • Process repeats until approved            │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│                                             │
│ [❌ REJECT PLAN]                            │
│          ↑ Red button                       │
│                                             │
│ What happens if you reject:                 │
│ • Form opens to explain why rejecting       │
│ • Status changes to "under_negotiation"     │
│ • Chat opens for discussion                 │
│ • Options:                                  │
│   - Company submits new plan                │
│   - Agreement reached via chat              │
│   - Cancel contract if no agreement         │
│                                             │
└─────────────────────────────────────────────┘

═══════════════════════════════════════════════

⏰ UNDO WINDOW STATUS (Issue #3)

24-hour undo window: ⏰ 21 hours 45 minutes remaining
Deadline: Jan 26, 2026 at 2:30 PM

You can still undo the entire quotation
acceptance if you prefer.

[View Undo Status →]

═══════════════════════════════════════════════
```

---

## **Step 5.3: Chat System Opens (Issue #4)**

### **What Happens:**
Customer or company can now access chat from multiple locations.

### **Chat Access Points (5 locations, matching Issue #5):**

**1. From Contract Page:**
[💬 OPEN CHAT] button visible throughout

**2. From My Contracts Tab:**
Contract card shows "💬 Chat Active" badge

**3. From Notification:**
Chat message notifications link to chat

**4. From Email:**
"Open Chat" links in emails

**5. From Dashboard:**
Dashboard widget shows chat status

### **Chat Interface:**

```
┌──────────────────────────────────────────────┐
│ 💬 Chat with ABC Construction                │
│ Contract #CNT-2026-001                       │
│ Online now ● Last seen 2 minutes ago        │
├──────────────────────────────────────────────┤
│                                              │
│ [System Message] Today at 4:30 PM           │
│ ═══════════════════════════════════════════  │
│ Chat activated. You can now communicate     │
│ about Contract #CNT-2026-001.                │
│ ═══════════════════════════════════════════  │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ [System Message] Today at 4:30 PM           │
│ ═══════════════════════════════════════════  │
│ ABC Construction submitted milestone plan.   │
│ [📋 View Plan →]                             │
│ ═══════════════════════════════════════════  │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ [You can start chatting]                    │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ Type your message...                         │
│ ┌────────────────────────────────────────┐  │
│ │                                        │  │
│ └────────────────────────────────────────┘  │
│ [📎][📷][📍][😊][Send]                     │
│   ↑   ↑   ↑   ↑                            │
│ Attach Photo Location Emoji                 │
│                                              │
│ [View Contract] [View Plan] [Help]          │
│                                              │
└──────────────────────────────────────────────┘
```

### **Chat Features Available:**

**Text Messaging:**
- Real-time message delivery
- Read receipts (✓✓ = delivered, ✓✓ blue = read)
- Typing indicators
- Message history preserved
- Search messages

**File Attachments:**
- Documents (PDF, DOC, XLS, etc.)
- Maximum 10 MB per file
- Multiple files per message
- Preview before sending
- Download and view

**Photo Sharing:**
- Camera or gallery
- Multiple photos at once
- Compression for faster sending
- Full resolution available
- Captions supported

**Location Sharing:**
- Share exact location
- Pin on map
- Address included
- Useful for site location
- Privacy: Only shared when you choose

**Emoji & Reactions:**
- Full emoji keyboard
- React to messages
- Quick responses
- Professional tone maintained

**Quick Actions:**
- View contract
- View milestone plan
- View request
- Add reminder
- Call support

**Privacy & Security:**
- No phone numbers displayed
- No personal contact info shared
- All chats logged for disputes
- Can report abuse
- Encrypted messages

---

## **Step 5.4: Customer Discusses Plan via Chat (Optional)**

### **What Happens:**
Customer can ask questions or discuss concerns before making decision.

### **Example Chat Conversation:**

```
[Customer - John] Today at 4:35 PM
Hi, I reviewed the milestone plan. Overall it
looks good! I have a few questions though.

[ABC Construction] Today at 4:36 PM
Hello John! Happy to answer any questions about
the plan. What would you like to know?

[Customer - John] Today at 4:37 PM
For Milestone 1, it says "weather-dependent".
What happens if it rains heavily? Will the
timeline extend?

[ABC Construction] Today at 4:38 PM
Good question! We've built in some buffer time.
Light rain won't affect us as the work is mostly
indoors. Heavy rain might delay by 1-2 days max,
but we'll make it up in later phases to stay on
schedule.

[Customer - John] Today at 4:40 PM
Okay, that makes sense. Also, I noticed the first
payment is 25%. That seems high for just
preparation. Can we adjust the payment schedule?

[ABC Construction] Today at 4:42 PM
I understand your concern. The 25% covers
material procurement, which is significant upfront
cost for us. However, I can adjust slightly.
How about:

M1: 15% (LKR 7,500)
M2: 50% (LKR 25,000)
M3: 20% (LKR 10,000)
M4: 15% (LKR 7,500)

This way, you pay less upfront and more at the
end. Would this work better for you?

[Customer - John] Today at 4:45 PM
Yes! That sounds much better. Can you update
the plan with these new payment percentages?

[ABC Construction] Today at 4:46 PM
Absolutely! I'll submit a revised plan (Version 2)
with the adjusted payment schedule. You'll
receive a notification when it's ready for review.
Give me about 15 minutes.

[Customer - John] Today at 4:47 PM
Perfect! Thank you for being flexible. 👍

[ABC Construction] Today at 4:48 PM
My pleasure! Customer satisfaction is our
priority. I'll get that revision submitted shortly.

[System Message] Today at 5:02 PM
═══════════════════════════════════════════════
ABC Construction submitted revised milestone
plan (Version 2).
[📋 View Revised Plan →]
═══════════════════════════════════════════════
```

### **Benefits of Chat Discussion:**
- Real-time negotiation
- Faster than email
- All changes documented
- Builds trust
- Resolves concerns quickly
- Transparent communication
- Both parties can reference later

---

## **Step 5.5: Customer Makes Decision**

### **What Happens:**
After reviewing plan (and optionally discussing), customer chooses one of 3 options.

---

### **OPTION A: APPROVE PLAN**

**What Customer Sees:**

```
┌──────────────────────────────────────────────┐
│ ✅ APPROVE MILESTONE PLAN?                   │
├──────────────────────────────────────────────┤
│                                              │
│ You are about to approve this milestone plan:│
│                                              │
│ Contract: #CNT-2026-001                      │
│ Company: ABC Construction                    │
│ Milestones: 4                                │
│ Timeline: 20 days (Jan 15 - Feb 4)          │
│ Total Budget: LKR 50,000                     │
│                                              │
│ ⚠️ THIS WILL:                                │
│                                              │
│ ✓ Activate the contract                     │
│ ✓ Lock payment schedule                     │
│ ✓ Allow company to start work on Jan 15     │
│ ✓ Begin milestone tracking                  │
│ ✓ Release payments as milestones complete   │
│                                              │
│ ⚠️ ONCE APPROVED:                            │
│                                              │
│ • Payment schedule cannot be changed         │
│ • Major changes require renegotiation        │
│ • Cancellation may incur fees                │
│ • Budget changes need approval (Issue #1)    │
│                                              │
│ 💬 CHAT REMINDER:                            │
│                                              │
│ Chat will remain active for project updates, │
│ questions, and coordination throughout the   │
│ entire project.                              │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │                                        │  │
│ │ ☐ I have carefully reviewed this       │  │
│ │   milestone plan and agree to approve  │  │
│ │   it.                                  │  │
│ │      ↑ MUST CHECK TO PROCEED           │  │
│ │                                        │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ [Cancel] [✅ CONFIRM APPROVAL]               │
│                 ↑ Only active when checked   │
│                                              │
└──────────────────────────────────────────────┘
```

**After Customer Clicks "Confirm Approval":**

System processes approval (details in Phase 6 below).

---

### **OPTION B: REQUEST CHANGES**

**What Customer Sees:**

```
┌──────────────────────────────────────────────┐
│ 📝 REQUEST CHANGES TO MILESTONE PLAN         │
├──────────────────────────────────────────────┤
│                                              │
│ What do you want changed in the plan?       │
│                                              │
│ Which milestone(s) need changes?             │
│ (Select all that apply)                      │
│                                              │
│ ☐ Milestone 1: Site Preparation             │
│ ☑ Milestone 2: Main Installation            │
│ ☐ Milestone 3: Finishing                    │
│ ☐ Milestone 4: Final Inspection             │
│ ☑ Overall timeline/budget                    │
│ ☑ Payment schedule                           │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Describe the changes you want:              │
│ (Be as specific as possible)                 │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │                                        │  │
│ │ For Milestone 2:                       │  │
│ │ The duration seems too long. Can we    │  │
│ │ reduce it from 10 days to 8 days?      │  │
│ │                                        │  │
│ │ For Payment Schedule:                  │  │
│ │ The first payment (25%) is too high.   │  │
│ │ Can we make it 15% instead? And        │  │
│ │ increase the final payment to 15%?     │  │
│ │                                        │  │
│ │ Suggested breakdown:                   │  │
│ │ M1: 15% (LKR 7,500)                   │  │
│ │ M2: 50% (LKR 25,000)                  │  │
│ │ M3: 20% (LKR 10,000)                  │  │
│ │ M4: 15% (LKR 7,500)                   │  │
│ │                                        │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Urgency Level:                               │
│ ( ) Major changes - Need discussion          │
│ (•) Minor adjustments - Probably okay        │
│ ( ) Just questions - No major changes        │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ ⚠️ WHAT HAPPENS NEXT:                        │
│                                              │
│ 1. Your change request sent to company       │
│ 2. Chat opens with your message              │
│ 3. Company discusses feasibility             │
│ 4. Company submits revised plan (Version 2)  │
│ 5. You review revised plan                   │
│ 6. You can approve or request more changes   │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ [Cancel] [📤 SEND CHANGE REQUEST]            │
│                     ↑ PRIMARY BUTTON         │
│                                              │
└──────────────────────────────────────────────┘
```

**After Sending Change Request:**

- System records change request
- Company receives notification
- Chat opens automatically with customer's message
- Status remains "pending_customer_approval"
- Waiting for company to submit revised plan

---

### **OPTION C: REJECT PLAN**

**What Customer Sees:**

```
┌──────────────────────────────────────────────┐
│ ❌ REJECT MILESTONE PLAN?                    │
├──────────────────────────────────────────────┤
│                                              │
│ ⚠️ This is a serious action. Please only    │
│ reject if you have significant concerns      │
│ that cannot be resolved through changes.     │
│                                              │
│ Why are you rejecting this plan?             │
│ (Required - helps company understand)        │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │                                        │  │
│ │ The timeline is unrealistic and the    │  │
│ │ payment schedule doesn't match what    │  │
│ │ we discussed. I prefer a different     │  │
│ │ approach to breaking down the work.    │  │
│ │                                        │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ What would you prefer?                       │
│ ( ) Discuss with company via chat            │
│ ( ) Company submits completely new plan      │
│ (•) Consider canceling contract              │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ ⚠️ WHAT HAPPENS NEXT:                        │
│                                              │
│ • Status changes to "under_negotiation"      │
│ • Chat opens for discussion                  │
│ • Company receives your feedback             │
│ • Options:                                   │
│   - Company submits new plan                 │
│   - Agreement reached via negotiation        │
│   - Cancel contract if no agreement          │
│                                              │
│ You still have your 24-hour undo window      │
│ available if you want to completely cancel   │
│ the acceptance.                              │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ [Cancel] [❌ CONFIRM REJECTION]              │
│                     ↑ RED BUTTON             │
│                                              │
└──────────────────────────────────────────────┘
```

**After Rejection:**

- System records rejection with reason
- Contract status: "under_negotiation"
- Company receives notification with feedback
- Chat opens for discussion
- Both parties work to find solution
- Or contract cancelled if no agreement

---

# **PHASE 6: CONTRACT ACTIVATION** (Issue #6)

## **Step 6.1: Customer Approves Milestone Plan**

### **What Happens:**
Assuming customer clicked "Approve Plan" and confirmed.

### **System Actions (in order):**

**1. Update Contract Status:**
- Changes from: "pending_customer_approval"
- Changes to: "active"
- Records approval timestamp
- Records approval by customer
- Locks milestone plan (no edits)

**2. Update Milestone Plan Status:**
- Changes plan status from "submitted" to "approved"
- Locks plan for editing
- Becomes official contract document

**3. Update Milestone Statuses:**
- Milestone 1: Changes to "active" (ready to start)
- Milestones 2-4: Remain "pending" (waiting for previous)

**4. Create Project Record:**
- Generates project ID (PRJ-2026-001)
- Links to contract
- Status: "active"
- Start date: January 15, 2026
- Expected end: February 4, 2026
- Budget: LKR 50,000
- Current milestone: Milestone 1

**5. Lock Payment Schedule:**
- Payment schedule fixed
- Cannot be changed without renegotiation
- Payment triggers defined per milestone

**6. Keep Chat Active:**
- Chat status remains "active"
- Now used for project updates
- Both parties can communicate throughout

**7. Generate Notifications:**
- Customer notification: "Plan approved! Contract active."
- Company notification: "Customer approved! You can start work."

**8. Send Emails:**
- Customer: Contract activation confirmation
- Company: Approval notice with next steps

**9. Send SMS:**
- Brief alerts to both parties

**10. Update Dashboard Widgets:**
- Contract status updates everywhere
- Progress tracking begins
- Milestone 1 shows as active

**11. Generate Contract Documents:**
- Official contract PDF
- Milestone plan document
- Payment schedule
- Terms and conditions

**12. Create Calendar Reminders:**
- Milestone start dates
- Milestone end dates
- Payment due dates
- Project completion date

**13. Initialize Progress Tracking:**
- Progress bar at 0%
- Milestone completion tracking
- Timeline visualization
- Budget tracking

**14. Create Audit Log:**
- Log approval action
- Record all status changes
- Timestamp everything

---

## **Step 6.2: Success Message Displayed**

### **What Customer Sees:**

```
┌──────────────────────────────────────────────┐
│ ✅ MILESTONE PLAN APPROVED!                  │
│ CONTRACT ACTIVATED SUCCESSFULLY!             │
├──────────────────────────────────────────────┤
│                                              │
│ 🎉 Your project is now active and ready     │
│    to begin!                                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📋 CONTRACT: #CNT-2026-001                   │
│ 🏗️ PROJECT: #PRJ-2026-001 (Created)         │
│ ✅ STATUS: ACTIVE                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📅 WORK SCHEDULE                             │
│                                              │
│ Start Date: January 15, 2026 (5 days from now)│
│ End Date: February 4, 2026                   │
│ Duration: 20 days                            │
│                                              │
│ Next Milestone: Milestone 1 (Active)         │
│ Site Preparation & Material Procurement      │
│ Expected Completion: January 19, 2026        │
│ Payment: LKR 12,500 (25%)                    │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📊 WHAT HAPPENS NEXT                         │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │                                        │  │
│ │ 1️⃣ COMPANY BEGINS WORK (Jan 15)        │  │
│ │    ABC Construction will start         │  │
│ │    Milestone 1 on the scheduled date   │  │
│ │                                        │  │
│ │ 2️⃣ YOU RECEIVE PROGRESS UPDATES        │  │
│ │    • Via chat messaging                │  │
│ │    • Email notifications               │  │
│ │    • Photos from work site             │  │
│ │    • Status updates in your account    │  │
│ │                                        │  │
│ │ 3️⃣ APPROVE MILESTONES AS COMPLETED     │  │
│ │    Company marks milestone done →      │  │
│ │    You review deliverables →           │  │
│ │    You approve milestone →             │  │
│ │    Payment released automatically      │  │
│ │                                        │  │
│ │ 4️⃣ TRACK PROGRESS                      │  │
│ │    Dashboard shows completion %        │  │
│ │    Timeline updated in real-time       │  │
│ │    Budget tracking active              │  │
│ │                                        │  │
│ │ 5️⃣ PROJECT COMPLETES                   │  │
│ │    Final inspection → Rate company →   │  │
│ │    Download invoice → Archive contract │  │
│ │                                        │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 💬 CHAT IS ACTIVE                            │
│                                              │
│ You can now communicate with ABC             │
│ Construction throughout the project:         │
│                                              │
│ • Daily progress updates                     │
│ • Share photos                               │
│ • Discuss issues or changes                  │
│ • Coordinate schedules                       │
│ • Answer questions                           │
│ • Track milestones                           │
│                                              │
│ [💬 OPEN CHAT NOW]                           │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📧 NOTIFICATIONS SENT                        │
│                                              │
│ ✓ Email confirmation sent to your inbox      │
│ ✓ SMS alert sent to your phone               │
│ ✓ Company notified to begin preparation      │
│ ✓ Calendar reminders set                     │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 📄 DOCUMENTS AVAILABLE                       │
│                                              │
│ New documents generated:                     │
│ • Official Contract Agreement (PDF)          │
│ • Approved Milestone Plan (PDF)              │
│ • Payment Schedule (PDF)                     │
│ • Terms & Conditions (PDF)                   │
│                                              │
│ [📥 DOWNLOAD ALL DOCUMENTS]                  │
│ [📧 EMAIL DOCUMENTS TO ME]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ 🔔 YOU WILL BE NOTIFIED ABOUT                │
│                                              │
│ • Work start (Jan 15)                        │
│ • Milestone completions                      │
│ • Payment releases                           │
│ • Chat messages from company                 │
│ • Any schedule changes                       │
│ • Budget adjustments (if needed)             │
│ • Project completion                         │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📋 VIEW PROJECT DASHBOARD]                  │
│ [💬 OPEN CHAT]                               │
│ [📄 VIEW CONTRACT]                           │
│ [🏠 GO TO HOME]                              │
│                                              │
└──────────────────────────────────────────────┘
```

---

## **Step 6.3: Contract Now Shows "Active" Status**

### **What Both Parties See:**

**Contract page header changes:**
```
CONTRACT #CNT-2026-001
Kitchen Sink Repair

Status: ✅ ACTIVE | PROJECT #PRJ-2026-001

[Contract Details] [Milestones] [Progress] [Chat] [Files]

Progress: ████░░░░░░ 0% (0 of 4 milestones complete)
Timeline: Day 0 of 20 | Start: Jan 15 | End: Feb 4

Current Milestone: Milestone 1 (Active)
Next Action: Wait for company to begin work

[💬 Chat] [📊 Track Progress] [📄 Documents]
```

**Updated features now available:**
- Progress tracking
- Milestone approval workflow
- Payment tracking
- Document downloads
- Enhanced chat for project updates
- Timeline visualization
- Budget monitoring

---

# **PHASE 7: PROJECT EXECUTION** (All Issues Integrated)

## **Step 7.1: Work Begins - Milestone 1 Becomes Active**

### **What Happens:**
On January 15, 2026 (contract start date), company begins Milestone 1.

### **Company View:**

```
PROJECT #PRJ-2026-001
Contract #CNT-2026-001 - Kitchen Sink Repair

Status: ✅ ACTIVE - WORK IN PROGRESS

═══════════════════════════════════════════════

⚡ CURRENT MILESTONE: MILESTONE 1

Site Preparation & Material Procurement
Status: 🟢 ACTIVE - In Progress
Duration: 5 days (Jan 15 - Jan 19)
Days Elapsed: 0 of 5
Payment: LKR 12,500 (25% - on completion)

Today's Date: January 15, 2026 (Day 1)

┌─────────────────────────────────────────────┐
│ YOUR TASKS FOR TODAY:                       │
├─────────────────────────────────────────────┤
│ ☐ Clear and protect work area               │
│ ☐ Remove old sink and fixtures              │
│ ☐ Inspect plumbing system                   │
│ ☐ Begin material procurement                │
└─────────────────────────────────────────────┘

DELIVERABLES TO COMPLETE:
☐ Work area cleared and protected
☐ Old sink and fixtures removed
☐ Plumbing system inspected and documented
☐ All materials procured and verified
☐ Installation area prepared and ready

Progress: 0% complete

[💬 Update Customer via Chat]
[📸 Share Progress Photos]
[✅ Mark Milestone Complete] (disabled until work done)

═══════════════════════════════════════════════

UPCOMING MILESTONES:

Milestone 2: Main Installation (Pending)
Starts: Jan 20 | Ends: Jan 29 | Payment: LKR 25,000

Milestone 3: Finishing (Pending)
Starts: Jan 30 | Ends: Feb 1 | Payment: LKR 10,000

Milestone 4: Final Inspection (Pending)
Starts: Feb 2 | Ends: Feb 4 | Payment: LKR 2,500

═══════════════════════════════════════════════
```

### **Customer View:**

```
PROJECT #PRJ-2026-001
Contract #CNT-2026-001 - Kitchen Sink Repair

Status: ✅ ACTIVE - Work Started Today!

═══════════════════════════════════════════════

🎬 WORK STARTED: January 15, 2026

ABC Construction has begun working on your
project today!

Current Milestone: Milestone 1
Site Preparation & Material Procurement
Expected Completion: January 19, 2026 (4 days)

Progress: ████░░░░░░░░░░ 0% overall
         (Milestone 1 in progress)

═══════════════════════════════════════════════

WHAT'S HAPPENING NOW:

✓ Company is on-site today
✓ Beginning site preparation
✓ Removing old sink and fixtures
✓ Inspecting plumbing system
✓ Procuring required materials

You'll receive updates via chat as work progresses.

[💬 OPEN CHAT] - Ask questions or get updates

═══════════════════════════════════════════════

TIMELINE:

[Day 1]────────────────────────────────[Day 20]
   ↑ Today                               Feb 4
Jan 15

Current Phase: M1 (Day 1 of 5)
Next Phase: M2 starts Jan 20
Completion: Feb 4 (20 days from start)

On Schedule ✓

═══════════════════════════════════════════════

PAYMENT STATUS:

Milestone 1: LKR 12,500 - Pending completion
Milestone 2: LKR 25,000 - Not started
Milestone 3: LKR 10,000 - Not started
Milestone 4: LKR 2,500 - Not started

Total Paid: LKR 0 of 50,000 (0%)
Payments are released after you approve each
completed milestone.

═══════════════════════════════════════════════
```

---

## **Step 7.2: Daily Progress Updates via Chat**

### **What Happens:**
Company sends updates throughout work day via chat.

### **Example Daily Chat Updates:**

```
┌──────────────────────────────────────────────┐
│ 💬 Chat with ABC Construction                │
│ Project #PRJ-2026-001                        │
├──────────────────────────────────────────────┤
│                                              │
│ [ABC Construction] Jan 15, 9:30 AM          │
│ Good morning John! We've arrived on-site    │
│ and starting work now. Weather is clear,    │
│ perfect conditions. 👍                       │
│                                    ✓✓ Read  │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ [ABC Construction] Jan 15, 11:00 AM         │
│ Update: Old sink and fixtures removed       │
│ successfully. No issues found.               │
│                                              │
│ 📷 [Photo: Removed old sink]                │
│ 📷 [Photo: Work area]                       │
│                                    ✓✓ Read  │
│                                              │
│ [You - John] Jan 15, 11:15 AM               │
│ Great! Looks good. Any issues with the      │
│ plumbing pipes?                              │
│                                    ✓✓ Read  │
│                                              │
│ [ABC Construction] Jan 15, 11:20 AM         │
│ Inspected the plumbing. Everything in good  │
│ condition. No corrosion or leaks in existing│
│ pipes. This is good news - no additional    │
│ repairs needed! Should save some costs. 💰  │
│                                    ✓✓ Read  │
│                                              │
│ [You - John] Jan 15, 11:25 AM               │
│ Excellent! Keep me updated. 👍               │
│                                    ✓✓ Read  │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ [ABC Construction] Jan 15, 2:30 PM          │
│ Afternoon update: Work area protected and   │
│ prepared. Materials ordered from supplier.  │
│ Arriving tomorrow morning. On schedule! ✓   │
│                                              │
│ 📷 [Photo: Protected work area]             │
│                                    ✓✓       │
│                                              │
│ ─────────────────────────────────────────── │
│                                              │
│ [ABC Construction] Jan 15, 5:00 PM          │
│ Day 1 complete! Summary:                    │
│ ✓ Old sink removed                          │
│ ✓ Plumbing inspected (all good!)            │
│ ✓ Work area prepared and protected          │
│ ✓ Materials ordered (arrive tomorrow)       │
│                                              │
│ Tomorrow: Material verification and         │
│ preparation for installation phase.         │
│                                              │
│ See you tomorrow! 🙂                         │
│                                    ✓✓ Read  │
│                                              │
│ [You - John] Jan 15, 5:15 PM                │
│ Thanks for the detailed updates! Looks      │
│ like great progress on day 1. 👏            │
│                                    ✓✓ Read  │
│                                              │
└──────────────────────────────────────────────┘
```

### **Benefits of Chat During Work:**
- Real-time updates
- Photo evidence of progress
- Immediate question answering
- Builds trust and transparency
- Documents everything
- Customer feels involved
- Issues resolved quickly

---

## **Step 7.3: Milestone 1 Completion**

### **What Happens:**
After 5 days of work, company completes Milestone 1.

### **Company Marks Milestone Complete:**

```
MILESTONE 1 COMPLETION FORM

Milestone: Site Preparation & Material Procurement
Status: Ready to mark as complete

CHECKLIST VERIFICATION:

Required Deliverables:
☑ Work area cleared and protected
☑ Old sink and fixtures removed
☑ Plumbing system inspected and documented
☑ All materials procured and verified
☑ Installation area prepared and ready

All deliverables completed? ☑ Yes

─────────────────────────────────────────────

Completion Details:

Completion Date: January 19, 2026 ✓ (On schedule)
Actual Duration: 5 days ✓ (As planned)

Work Summary:
┌─────────────────────────────────────────────┐
│ All preparation work completed successfully.│
│ Old sink removed without issues. Plumbing   │
│ system inspected - in excellent condition,  │
│ no additional repairs needed. All materials │
│ procured and verified for quality. Site is  │
│ ready for main installation work.           │
└─────────────────────────────────────────────┘

Photos/Evidence:
📷 [8 photos attached]
• Before: Old sink (2 photos)
• During: Removal process (3 photos)
• After: Prepared area (3 photos)

Documents:
📎 Material_Inspection_Report.pdf
📎 Plumbing_Inspection_Photos.zip

─────────────────────────────────────────────

Customer Notification:

Message to customer:
┌─────────────────────────────────────────────┐
│ Hi John! Milestone 1 is complete and ready  │
│ for your review. All preparation work done  │
│ as planned. Site is ready for installation  │
│ phase. Please review the deliverables and   │
│ approve when ready. Photos attached! 👍      │
└─────────────────────────────────────────────┘

─────────────────────────────────────────────

⚠️ ONCE YOU MARK COMPLETE:

• Customer receives notification
• Customer reviews deliverables
• Customer approves or requests fixes
• Payment released after customer approval
• Milestone 2 becomes active automatically

[Cancel] [✅ MARK MILESTONE 1 COMPLETE]
```

### **After Company Marks Complete:**

**System Actions:**
1. Updates Milestone 1 status to "completed_pending_approval"
2. Sends notifications to customer (email, SMS, in-app)
3. Opens approval workflow for customer
4. Chat message sent automatically
5. Payment prepared (awaiting approval)

---

## **Step 7.4: Customer Reviews and Approves Milestone**

### **Customer Receives Notification:**

```
📧 Email Subject: Milestone 1 Completed - Review Required
Contract #CNT-2026-001

Dear John,

ABC Construction has completed Milestone 1 and
submitted it for your review and approval.

MILESTONE 1: Site Preparation & Material Procurement
Completed: January 19, 2026 (On schedule ✓)
Payment: LKR 12,500 (25%)

Deliverables Completed:
✓ Work area cleared and protected
✓ Old sink and fixtures removed
✓ Plumbing system inspected and documented
✓ All materials procured and verified
✓ Installation area prepared and ready

Photos and documentation attached for review.

ACTION REQUIRED: Please review and approve

[REVIEW MILESTONE NOW →]

After approval, payment of LKR 12,500 will be
released automatically, and Milestone 2 will
begin.

Best regards,
FixLanka Team
```

### **Customer Reviews Milestone:**

```
MILESTONE 1 REVIEW & APPROVAL

Site Preparation & Material Procurement
Completed: January 19, 2026
Payment: LKR 12,500 (25%) - Pending your approval

═══════════════════════════════════════════════

DELIVERABLES REVIEW:

☑ Work area cleared and protected
   Status: ✅ Completed

☑ Old sink and fixtures removed
   Status: ✅ Completed

☑ Plumbing system inspected and documented
   Status: ✅ Completed
   📎 Inspection Report attached

☑ All materials procured and verified
   Status: ✅ Completed
   📎 Material list attached

☑ Installation area prepared and ready
   Status: ✅ Completed

All Required Deliverables: ✅ COMPLETED

═══════════════════════════════════════════════

PHOTO EVIDENCE: (8 photos)

📷 Before Work:
   [Photo 1: Old sink condition]
   [Photo 2: Work area before]

📷 During Work:
   [Photo 3: Removal process 1]
   [Photo 4: Removal process 2]
   [Photo 5: Inspection process]

📷 After Completion:
   [Photo 6: Cleared area]
   [Photo 7: Prepared installation space]
   [Photo 8: Procured materials]

[View All Photos in Gallery]
[Download Photos]

═══════════════════════════════════════════════

COMPANY'S COMPLETION NOTES:

"All preparation work completed successfully.
Old sink removed without issues. Plumbing system
inspected - in excellent condition, no additional
repairs needed. All materials procured and verified
for quality. Site is ready for main installation
work."

═══════════════════════════════════════════════

DOCUMENTS ATTACHED:

📄 Material_Inspection_Report.pdf (245 KB)
📄 Plumbing_Inspection_Photos.zip (2.3 MB)

[Download All Documents]

═══════════════════════════════════════════════

TIMELINE STATUS:

Scheduled Completion: January 19, 2026
Actual Completion: January 19, 2026
Status: ✅ ON SCHEDULE

Next Milestone Starts: January 20, 2026 (Tomorrow)

═══════════════════════════════════════════════

PAYMENT DETAILS:

Amount: LKR 12,500 (25% of total budget)
Payment Will Be Released: After your approval
Payment Method: Automatic (from escrow/account)

Remaining Budget:
Total: LKR 50,000
Paid: LKR 0
Pending: LKR 50,000
After This Payment: LKR 37,500 remaining

═══════════════════════════════════════════════

💬 QUESTIONS OR CONCERNS?

If you have any questions or concerns about
this milestone, you can discuss with ABC
Construction via chat before approving.

[💬 ASK QUESTIONS VIA CHAT]

═══════════════════════════════════════════════

YOUR ACTION REQUIRED:

Please review all deliverables, photos, and
documentation above, then choose an action:

┌─────────────────────────────────────────────┐
│                                             │
│ [✅ APPROVE MILESTONE]                      │
│          ↑ Big green button                 │
│                                             │
│ What happens when you approve:              │
│ ✓ Payment of LKR 12,500 released           │
│   automatically to company                  │
│ ✓ Milestone 1 marked as fully complete     │
│ ✓ Milestone 2 becomes active immediately    │
│ ✓ Company can begin next phase tomorrow     │
│ ✓ Progress updates to 25% complete          │
│                                             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│                                             │
│ [🔧 REQUEST FIXES]                          │
│          ↑ Orange button                    │
│                                             │
│ If you notice any issues or incomplete      │
│ work, request company to fix before         │
│ approving.                                  │
│                                             │
│ What happens:                               │
│ • Specify what needs fixing                 │
│ • Company receives notification             │
│ • Company addresses issues                  │
│ • Resubmits for approval                    │
│ • Payment on hold until fixed               │
│                                             │
└─────────────────────────────────────────────┘

═══════════════════════════════════════════════
```

### **Customer Approves Milestone:**

**Approval Confirmation Dialog:**
```
┌──────────────────────────────────────────────┐
│ ✅ APPROVE MILESTONE 1?                      │
├──────────────────────────────────────────────┤
│                                              │
│ You are about to approve Milestone 1:        │
│                                              │
│ Site Preparation & Material Procurement      │
│ Payment: LKR 12,500                          │
│                                              │
│ ⚠️ THIS WILL:                                │
│                                              │
│ ✓ Release payment of LKR 12,500 to company  │
│ ✓ Mark Milestone 1 as complete              │
│ ✓ Activate Milestone 2 (Main Installation)  │
│ ✓ Update project progress to 25%            │
│                                              │
│ Approval cannot be reversed after payment    │
│ is released. Please confirm all work is      │
│ satisfactory.                                │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │ ☐ I have reviewed all deliverables and  │  │
│ │   approve this milestone as complete.   │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ [Cancel] [✅ CONFIRM APPROVAL]               │
│                                              │
└──────────────────────────────────────────────┘
```

---

## **Step 7.5: Payment Released & Milestone 2 Begins**

### **What Happens After Customer Approves:**

**System Actions (in order):**

1. **Process Payment:**
   - Transfer LKR 12,500 from escrow/customer account
   - Send to company account
   - Generate payment receipt
   - Record transaction

2. **Update Milestone 1:**
   - Status: "completed_approved"
   - Approval timestamp
   - Payment released timestamp

3. **Update Milestone 2:**
   - Status changes to: "active"
   - Start date: January 20, 2026
   - Company can begin work

4. **Update Project Progress:**
   - Progress: 25% complete (1 of 4 milestones)
   - Timeline updated
   - Budget tracking updated

5. **Send Notifications:**
   - Customer: "Milestone approved! Payment released."
   - Company: "Payment received! Start Milestone 2."

6. **Generate Documents:**
   - Payment receipt (PDF)
   - Milestone completion certificate
   - Updated project status report

7. **Update Everywhere (Issue #5):**
   - My Contracts tab
   - Dashboard widgets
   - Email notifications
   - SMS alerts
   - In-app notifications

### **Success Message:**

```
┌──────────────────────────────────────────────┐
│ ✅ MILESTONE 1 APPROVED!                     │
│ PAYMENT RELEASED SUCCESSFULLY                │
├──────────────────────────────────────────────┤
│                                              │
│ Payment of LKR 12,500 has been released to  │
│ ABC Construction.                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ MILESTONE 1: ✅ COMPLETE                     │
│ Site Preparation & Material Procurement      │
│ Completed: January 19, 2026                  │
│ Payment: LKR 12,500 ✓ Released               │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PROJECT PROGRESS UPDATED:                    │
│                                              │
│ ████████░░░░░░░░░░░░ 25% Complete            │
│ (1 of 4 milestones approved)                 │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ MILESTONE 2 NOW ACTIVE: 🟢                   │
│ Main Installation Work                       │
│ Starts: January 20, 2026 (Tomorrow)          │
│ Duration: 10 days                            │
│ Payment: LKR 25,000 (50%)                    │
│                                              │
│ ABC Construction will begin the main         │
│ installation work tomorrow.                  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PAYMENT SUMMARY:                             │
│                                              │
│ Milestone 1: LKR 12,500 ✅ Paid              │
│ Milestone 2: LKR 25,000 ⏳ Pending           │
│ Milestone 3: LKR 10,000 ⏳ Pending           │
│ Milestone 4: LKR 2,500 ⏳ Pending            │
│                                              │
│ Total Paid: LKR 12,500 (25%)                 │
│ Remaining: LKR 37,500 (75%)                  │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ DOCUMENTS:                                   │
│                                              │
│ 📄 Payment Receipt (download)                │
│ 📄 Milestone 1 Completion Certificate        │
│ 📄 Updated Project Status Report             │
│                                              │
│ [📥 Download All]                            │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📊 VIEW PROJECT PROGRESS]                   │
│ [💬 OPEN CHAT]                               │
│ [Close]                                      │
│                                              │
└──────────────────────────────────────────────┘
```

---

## **Step 7.6: Milestones 2, 3, 4 Follow Same Process**

### **Process Repeats:**

**For Each Remaining Milestone:**

1. **Milestone Becomes Active:**
   - Status changes to "active"
   - Company begins work
   - Timeline starts

2. **Daily Progress Updates:**
   - Company sends chat updates
   - Photos shared
   - Questions answered
   - Issues discussed

3. **Milestone Completion:**
   - Company marks complete
   - Submits deliverables
   - Customer notified

4. **Customer Review:**
   - Customer reviews work
   - Checks deliverables
   - Views photos/docs
   - Asks questions if needed

5. **Customer Approval:**
   - Approves milestone
   - Payment released automatically
   - Next milestone becomes active
   - Progress updated

### **Progress Tracking Throughout:**

```
OVERALL PROJECT PROGRESS

Timeline: Day 15 of 20 (75%)
Completion: 75% (3 of 4 milestones complete)

████████████████████░░░░ 75%

MILESTONE STATUS:

✅ Milestone 1: Complete (Paid: LKR 12,500)
✅ Milestone 2: Complete (Paid: LKR 25,000)
✅ Milestone 3: Complete (Paid: LKR 10,000)
🟢 Milestone 4: Active (Pending: LKR 2,500)

Budget Status:
Paid: LKR 47,500 (95%)
Remaining: LKR 2,500 (5%)

Timeline Status: On Schedule ✓
Expected Completion: February 4, 2026 (4 days)
```

---

## **Step 7.7: Budget Adjustments (If Needed - Issue #1)**

### **Scenario: Company Needs Budget Adjustment**

**What Happens If Company Discovers Extra Work:**

**Company Initiates Budget Change Request:**

```
BUDGET ADJUSTMENT REQUEST
Project #PRJ-2026-001

Current Budget: LKR 50,000 (Flexible ±10%)
Allowed Range: LKR 45,000 - 55,000

During Milestone 2, we discovered additional
issues that require extra work and materials.

Issue Discovered:
"Water supply pipes have corrosion and need
replacement. Not visible during initial
inspection."

Additional Work Required:
• Replace corroded supply pipes
• Additional materials needed
• Extra 2 days of work

Additional Cost: LKR 3,000

New Total Budget: LKR 53,000
Status: ✅ Within flexible range (LKR 45k-55k)

Photos/Evidence:
📷 [Photo: Corroded pipes]
📷 [Photo: Damage extent]

[Submit Budget Change Request to Customer]
```

**Customer Receives Request via Chat:**

```
[ABC Construction] Jan 25, 10:00 AM
John, we discovered an issue during installation
that needs your approval.

[System Message]
═══════════════════════════════════════════════
Budget Adjustment Request
Current: LKR 50,000 → Proposed: LKR 53,000

Additional Work: Corroded pipe replacement
Additional Cost: LKR 3,000
Status: Within your flexible budget range ✓

📷 Photos attached
[REVIEW REQUEST →]
═══════════════════════════════════════════════

[ABC Construction] Jan 25, 10:02 AM
The water supply pipes have corrosion (see
photos). They need replacement for long-term
reliability. The cost increase of LKR 3,000
is within your flexible budget range.

Would you like us to proceed?
```

**Customer Reviews and Approves:**

```
BUDGET ADJUSTMENT REVIEW

Requested By: ABC Construction
Date: January 25, 2026
Current Budget: LKR 50,000
Proposed Budget: LKR 53,000
Increase: LKR 3,000 (+6%)

BUDGET TYPE: Flexible (±10%) ✅
Status: ✅ WITHIN ALLOWED RANGE

Min Allowed: LKR 45,000
Max Allowed: LKR 55,000
Proposed: LKR 53,000 ✓ Valid

Reason for Increase:
"Water supply pipes have corrosion and need
replacement. Not visible during initial
inspection. Required for long-term reliability."

Evidence:
📷 [Photos of corroded pipes]

Additional Work:
• Replace corroded supply pipes
• Additional materials
• Extra 2 days

Impact on Timeline: +2 days (new end: Feb 6)

[✅ APPROVE ADJUSTMENT] [❌ REJECT] [💬 DISCUSS]
```

**Benefits of Flexible Budget (Issue #1):**
- Handles unexpected issues
- No contract renegotiation needed
- Work continues smoothly
- Customer protects (can't exceed range)
- Company protected (can cover extra costs)
- Both parties happy

---

# **PHASE 8: PROJECT COMPLETION**

## **Step 8.1: Final Milestone (Milestone 4) Completed**

### **What Happens:**
Company completes all work and submits final milestone.

### **Company Marks Milestone 4 Complete:**

```
MILESTONE 4 COMPLETION - FINAL MILESTONE

Final Inspection & Handover
Status: Ready to mark as complete

ALL PROJECT DELIVERABLES COMPLETED:

Milestone 1: ✅ Complete & Approved
Milestone 2: ✅ Complete & Approved
Milestone 3: ✅ Complete & Approved
Milestone 4: Ready for final approval

Final Milestone Deliverables:
☑ All work verified and approved
☑ Warranty documents provided
☑ Customer trained on usage/maintenance
☑ Maintenance guidelines provided
☑ Project documentation complete
☑ Official sign-off obtained

Final Inspection Completed: February 4, 2026

Work Summary:
┌─────────────────────────────────────────────┐
│ Project completed successfully! All          │
│ milestones finished on schedule. Kitchen     │
│ sink fully installed and tested. Customer    │
│ trained on usage. 2-year warranty provided.  │
│ All documentation handed over. Ready for     │
│ final approval and project closure.          │
└─────────────────────────────────────────────┘

Final Photos:
📷 [12 photos of completed work]

Documents Provided to Customer:
📄 Warranty Certificate (2 years)
📄 Maintenance Guidelines
📄 User Manual
📄 Material Specifications
📄 Project Completion Report

[✅ MARK PROJECT COMPLETE & SUBMIT FOR FINAL APPROVAL]
```

---

## **Step 8.2: Customer Reviews Final Work**

### **Customer Receives Completion Notification:**

```
📧 Email: 🎉 Project Complete! Final Review Required
Contract #CNT-2026-001 | Project #PRJ-2026-001

Dear John,

Congratulations! ABC Construction has completed
your Kitchen Sink Repair project!

PROJECT COMPLETION SUMMARY:

Started: January 15, 2026
Completed: February 4, 2026 ✓ On Schedule
Duration: 20 days (as planned)

All 4 Milestones Completed:
✅ Milestone 1: Site Preparation
✅ Milestone 2: Main Installation
✅ Milestone 3: Finishing Work
✅ Milestone 4: Final Inspection

Budget Status:
Total Paid: LKR 47,500 (95%)
Final Payment: LKR 2,500 (5%)
Total Budget: LKR 50,000 ✓ On Budget

FINAL ACTION REQUIRED:
Please review the completed work, approve the
final milestone, and rate ABC Construction.

[REVIEW FINAL WORK & COMPLETE PROJECT →]

After approval:
• Final payment released (LKR 2,500)
• Rate and review company
• Download completion documents
• Project archived

Thank you for using FixLanka!

Best regards,
FixLanka Team
```

### **Final Approval Page:**

```
PROJECT COMPLETION - FINAL APPROVAL
Contract #CNT-2026-001 | Project #PRJ-2026-001

Kitchen Sink Repair - COMPLETED! 🎉

═══════════════════════════════════════════════

PROJECT SUMMARY

Company: ABC Construction (Pvt) Ltd
Started: January 15, 2026
Completed: February 4, 2026
Duration: 20 days ✓ On Schedule
Budget: LKR 50,000 ✓ On Budget

All Milestones Completed Successfully! ✅

═══════════════════════════════════════════════

FINAL MILESTONE REVIEW

Milestone 4: Final Inspection & Handover

Deliverables:
☑ All work verified and approved
☑ Warranty documents provided (2 years)
☑ Customer trained on usage/maintenance
☑ Maintenance guidelines provided
☑ Project documentation complete
☑ Official sign-off obtained

Final Photos: (12 photos)
📷 [Before & After comparison]
📷 [Completed installation - 8 angles]
📷 [Quality details - 3 photos]

[View All Photos]

Documents Received:
📄 Warranty Certificate (2 years)
📄 Maintenance Guidelines
📄 User Manual
📄 Material Specifications
📄 Project Completion Report

[Download All Documents]

═══════════════════════════════════════════════

FINAL PAYMENT

Amount: LKR 2,500 (5%)

This is the final payment for Milestone 4.

Total Project Cost Summary:
M1: LKR 12,500 ✅ Paid
M2: LKR 25,000 ✅ Paid
M3: LKR 10,000 ✅ Paid
M4: LKR 2,500 ⏳ Pending approval

Total: LKR 50,000

After approval, final payment will be released
and project will be marked as complete.

═══════════════════════════════════════════════

APPROVE FINAL MILESTONE

┌─────────────────────────────────────────────┐
│ ☐ I confirm all work is completed          │
│   satisfactorily                            │
│                                             │
│ ☐ I have received all documents             │
│                                             │
│ ☐ I approve final payment release           │
└─────────────────────────────────────────────┘

[✅ APPROVE FINAL MILESTONE & RELEASE PAYMENT]
          ↑ Active when all checked

═══════════════════════════════════════════════
```

---

## **Step 8.3: Customer Rates Company**

### **What Happens After Final Approval:**

**Rating & Review Form Appears:**

```
┌──────────────────────────────────────────────┐
│ ⭐ RATE YOUR EXPERIENCE                      │
│ ABC Construction (Pvt) Ltd                   │
├──────────────────────────────────────────────┤
│                                              │
│ Overall Rating: * (Required)                 │
│                                              │
│ ☆☆☆☆☆  →  ★★★★★                            │
│ ↑ Click to rate                              │
│                                              │
│ Selected: ★★★★★ (5 stars)                   │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Detailed Ratings:                            │
│                                              │
│ Quality of Work:        ★★★★★ (5/5)         │
│ Communication:          ★★★★★ (5/5)         │
│ Timeliness:             ★★★★★ (5/5)         │
│ Professionalism:        ★★★★★ (5/5)         │
│ Value for Money:        ★★★★☆ (4/5)         │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Write a Review: (Optional but recommended)   │
│                                              │
│ ┌────────────────────────────────────────┐  │
│ │ ABC Construction did an excellent job   │  │
│ │ with our kitchen sink repair. They were │  │
│ │ professional, punctual, and kept us     │  │
│ │ updated daily via chat. The work was    │  │
│ │ completed on time and within budget.    │  │
│ │ Very satisfied with the quality of      │  │
│ │ work. Highly recommend! 👍               │  │
│ │                                        │  │
│ │ Character count: 287                   │  │
│ └────────────────────────────────────────┘  │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Add Photos: (Optional)                       │
│ Share photos of completed work               │
│                                              │
│ 📷 [3 photos added]                          │
│ [+ Add More Photos]                          │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ Would you hire this company again?           │
│ (•) Yes, definitely                          │
│ ( ) Maybe                                    │
│ ( ) No                                       │
│                                              │
│ Would you recommend to others?               │
│ (•) Yes, highly recommend                    │
│ ( ) Yes, with reservations                   │
│ ( ) No                                       │
│                                              │
│ ──────────────────────────────────────────── │
│                                              │
│ [Skip] [✅ SUBMIT RATING & COMPLETE PROJECT] │
│                      ↑ Primary button        │
│                                              │
└──────────────────────────────────────────────┘
```

---

## **Step 8.4: Project Completion & Archive**

### **What Happens After Rating Submitted:**

**System Actions:**

1. **Release Final Payment:**
   - Transfer LKR 2,500 to company
   - Generate final receipt
   - Total paid: LKR 50,000

2. **Update All Statuses:**
   - Contract: "completed"
   - Project: "completed"
   - All milestones: "completed_approved"

3. **Save Rating & Review:**
   - Add to company profile
   - Update company average rating
   - Make review public (if customer allowed)

4. **Generate Final Documents:**
   - Project completion certificate
   - Final invoice
   - Payment history
   - Review certificate for company

5. **Archive Project:**
   - Move to "Completed Projects"
   - Preserve all data
   - Keep chat history
   - Maintain documents
   - Still accessible

6. **Send Notifications:**
   - Customer: "Project completed! Thank you!"
   - Company: "Payment received! Customer rated you 5★"

7. **Update Company Profile:**
   - Increment completed projects count
   - Update average rating
   - Add review to profile

### **Project Completion Success Page:**

```
┌──────────────────────────────────────────────┐
│ 🎉 PROJECT COMPLETED SUCCESSFULLY!           │
├──────────────────────────────────────────────┤
│                                              │
│ Thank you for using FixLanka!                │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ PROJECT DETAILS                              │
│                                              │
│ Contract: #CNT-2026-001                      │
│ Project: #PRJ-2026-001                       │
│ Status: ✅ COMPLETED                         │
│                                              │
│ Company: ABC Construction (Pvt) Ltd          │
│ Project: Kitchen Sink Repair                 │
│                                              │
│ Started: January 15, 2026                    │
│ Completed: February 4, 2026                  │
│ Duration: 20 days ✓ On Schedule              │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ FINANCIAL SUMMARY                            │
│                                              │
│ Total Budget: LKR 50,000                     │
│ Total Paid: LKR 50,000 ✅ Complete           │
│                                              │
│ Payment Breakdown:                           │
│ • Milestone 1: LKR 12,500 ✅                 │
│ • Milestone 2: LKR 25,000 ✅                 │
│ • Milestone 3: LKR 10,000 ✅                 │
│ • Milestone 4: LKR 2,500 ✅                  │
│                                              │
│ Budget Status: ✅ On Budget                  │
│ No overruns or adjustments                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ YOUR RATING                                  │
│                                              │
│ You rated ABC Construction:                  │
│ ★★★★★ 5.0/5.0                               │
│                                              │
│ Your review:                                 │
│ "ABC Construction did an excellent job..."   │
│                                              │
│ Thank you for your feedback! Your review     │
│ helps other customers make informed          │
│ decisions.                                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ DOCUMENTS AVAILABLE                          │
│                                              │
│ All project documents saved and available:   │
│                                              │
│ 📄 Final Invoice (PDF)                       │
│ 📄 Payment History (PDF)                     │
│ 📄 Project Completion Certificate (PDF)      │
│ 📄 All Milestone Documents (ZIP)             │
│ 📄 Warranty Certificate (2 years)            │
│ 📄 Maintenance Guidelines (PDF)              │
│ 📄 Chat History (PDF)                        │
│ 📄 Photo Gallery (ZIP, 45 photos)            │
│                                              │
│ [📥 DOWNLOAD ALL DOCUMENTS]                  │
│ [📧 EMAIL DOCUMENTS TO ME]                   │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WARRANTY & SUPPORT                           │
│                                              │
│ Your 2-year warranty is active:              │
│                                              │
│ Warranty Period: Feb 4, 2026 - Feb 4, 2028  │
│ Coverage: Parts and labor for sink/plumbing │
│ Provider: ABC Construction (Pvt) Ltd         │
│                                              │
│ For warranty claims or support:              │
│ [💬 Contact Company via Chat]                │
│ [📧 Email Company]                           │
│ [📞 Call Company] (number now visible)       │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ WHAT'S NEXT?                                 │
│                                              │
│ ✓ Project archived in your account           │
│ ✓ All documents accessible anytime           │
│ ✓ Chat history preserved                     │
│ ✓ Warranty tracking active                   │
│ ✓ You can view this project anytime          │
│                                              │
│ Need another service?                        │
│ [➕ POST NEW SERVICE REQUEST]                │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ THANK YOU FOR CHOOSING FIXLANKA!             │
│                                              │
│ We hope your experience was excellent.       │
│ We'd love to help with your future projects! │
│                                              │
│ ══════════════════════════════════════════   │
│                                              │
│ [📋 VIEW COMPLETED PROJECT]                  │
│ [🏠 GO TO DASHBOARD]                         │
│ [➕ NEW SERVICE REQUEST]                     │
│ [👁️ BROWSE SERVICES]                         │
│                                              │
└──────────────────────────────────────────────┘
```

---

## **Step 8.5: Completed Project View (Archived)**

### **What Customer Can Access After Completion:**

```
COMPLETED PROJECT
Contract #CNT-2026-001 | Project #PRJ-2026-001

Kitchen Sink Repair
Status: ✅ COMPLETED

Company: ABC Construction (Pvt) Ltd
Completed: February 4, 2026
Duration: 20 days
Budget: LKR 50,000

Progress: ████████████████████████ 100%

[Contract Details] [Timeline] [Payments] [Chat History] [Documents]

═══════════════════════════════════════════════

PROJECT TIMELINE

Jan 15 ─┤M1├─ Jan 19 ─┤M2├─ Jan 29 ─┤M3├─ Feb 1 ─┤M4├─ Feb 4

All milestones completed on schedule ✓

Milestone 1: ✅ 5 days - Completed Jan 19
Milestone 2: ✅ 10 days - Completed Jan 29
Milestone 3: ✅ 3 days - Completed Feb 1
Milestone 4: ✅ 2 days - Completed Feb 4

═══════════════════════════════════════════════

PAYMENT HISTORY

Date         Milestone  Amount      Status
Jan 19, 2026 M1        LKR 12,500  ✅ Paid
Jan 29, 2026 M2        LKR 25,000  ✅ Paid
Feb 1, 2026  M3        LKR 10,000  ✅ Paid
Feb 4, 2026  M4        LKR 2,500   ✅ Paid

Total Paid: LKR 50,000 ✅

[Download Payment History]

═══════════════════════════════════════════════

DOCUMENTS (All Preserved)

Contract Documents:
📄 Original Contract Agreement
📄 Approved Milestone Plan
📄 Payment Schedule

Milestone Documents:
📄 M1 Completion Report
📄 M2 Completion Report
📄 M3 Completion Report
📄 M4 Completion Report

Final Documents:
📄 Project Completion Certificate
📄 Final Invoice
📄 Warranty Certificate (2 years)
📄 Maintenance Guidelines

[Download All Documents]

═══════════════════════════════════════════════

PHOTO GALLERY (45 photos preserved)

Before: 8 photos
During: 28 photos
After: 9 photos

[View Photo Gallery]

═══════════════════════════════════════════════

CHAT HISTORY PRESERVED

All conversations saved and accessible.

Messages: 127 messages over 20 days
Photos: 45 photos shared
Files: 12 files shared

[View Full Chat History]
[Download Chat History PDF]

═══════════════════════════════════════════════

YOUR RATING & REVIEW

You rated: ★★★★★ 5.0/5.0
Date: February 4, 2026

Your review:
"ABC Construction did an excellent job with our
kitchen sink repair. They were professional,
punctual, and kept us updated daily via chat.
The work was completed on time and within
budget. Very satisfied with the quality of work.
Highly recommend! 👍"

[Edit Review] [Add More Photos]

═══════════════════════════════════════════════

WARRANTY INFORMATION

Status: ✅ ACTIVE
Period: Feb 4, 2026 - Feb 4, 2028 (2 years)
Coverage: Parts and labor for sink/plumbing

For warranty claims:
[Contact ABC Construction]

═══════════════════════════════════════════════

ACTIONS

[📄 Download Project Report]
[💬 Contact Company]
[📧 Email All Documents]
[🔄 Request Similar Service]
[❓ Get Support]

═══════════════════════════════════════════════
```

---

## 🎯 **COMPLETE JOURNEY SUMMARY**

```
═══════════════════════════════════════════════
COMPLETE FLOW: REQUEST TO COMPLETION
═══════════════════════════════════════════════

PHASE 1: REQUEST & QUOTATION (Days -10 to -1)
• Customer posts service request
• Companies send quotations
• Quotations include budget type & payment terms
• Customer compares and selects

PHASE 2: ACCEPTANCE (Day 0)
• Customer accepts quotation
• 24-hour undo window begins
• Basic contract auto-created

PHASE 3: CONTRACT CREATION (Day 0)
• Contract visible in 5 locations
• Budget and payment terms locked in
• Status: Pending Milestone Plan

PHASE 4: MILESTONE PLANNING (Days 0-2)
• Company has 48 hours to submit plan
• Company creates detailed milestone breakdown
• System validates plan

PHASE 5: CUSTOMER REVIEW (Days 2-3)
• Chat opens automatically
• Customer reviews milestone plan
• Discusses via chat if needed
• Approves, requests changes, or rejects

PHASE 6: ACTIVATION (Day 3)
• Customer approves plan
• Contract activates
• Project record created
• Milestone 1 becomes active

PHASE 7: EXECUTION (Days 4-20)
• Work progresses through milestones
• Daily updates via chat
• Customer approves each milestone
• Payments released automatically
• Budget adjustments if needed (flexible)

PHASE 8: COMPLETION (Day 20)
• Final milestone completed
• Customer reviews and approves
• Final payment released
• Customer rates company
• Project archived
• All documents preserved

═══════════════════════════════════════════════

ALL 6 SOLUTIONS INTEGRATED THROUGHOUT:

✅ Issue #1: Budget flexibility used throughout
✅ Issue #2: Payment terms drive milestone structure
✅ Issue #3: 24-hour undo protects customer
✅ Issue #4: Chat active from plan submission
✅ Issue #5: Contract accessible from 5 locations
✅ Issue #6: Two-stage process ensures proper planning

═══════════════════════════════════════════════

TOTAL DURATION: ~30 days (Request to Completion)
CUSTOMER TOUCHPOINTS: 8 major decisions
NOTIFICATIONS: Email, SMS, In-app throughout
DOCUMENTS GENERATED: 15+ documents
CHAT MESSAGES: Typically 100-200 messages
PHOTOS SHARED: Typically 30-50 photos
PAYMENT TRANSACTIONS: 4 automatic releases

═══════════════════════════════════════════════
```

---

## ✅ **END OF COMPLETE DETAILED FLOW**

**This document covered:**
- ✅ All 8 phases in complete detail
- ✅ Every step explained without code
- ✅ All 6 solutions integrated at each stage
- ✅ User interfaces shown in detail
- ✅ System actions explained
- ✅ Notifications and communications
- ✅ Success and error flows
- ✅ Complete user journey from start to finish

**Total pages equivalent:** 150+ pages of detailed business logic documentation

**Ready for:** Development, design, testing, and stakeholder review
