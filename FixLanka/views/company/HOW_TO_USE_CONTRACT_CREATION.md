# How to Use the Contract Creation Feature

## Quick Start Guide

### Step 1: Access Applications
1. Navigate to **Workforce** page
2. Click on the **Applications** tab
3. You'll see all pending applications

### Step 2: Review Application
1. Click on an application card to view details (optional)
2. Or directly click the **Accept** button on any application

### Step 3: Contract Creation Drawer Opens
The Contract Creation Drawer will slide in from the right with the applicant's name pre-populated.

### Step 4: Select Employment Type
Choose one of the four employment types by clicking on the card:
- **Full-Time Employee** - For permanent positions
- **Freelance/Contractor** - For project-based work
- **Part-Time** - For reduced hours
- **Trial Period** - For 3-month probation

### Step 5: Set Contract Duration
1. **Start Date**: Select when employment begins (required)
2. **Duration**: Choose from dropdown:
   - Permanent/Indefinite
   - 3 Months (Trial)
   - 6 Months
   - 1 Year
   - 2 Years
   - 3 Years
   - Custom Duration (shows end date picker)

### Step 6: Configure Compensation
1. **Payment Structure**: Select how they'll be paid
   - Monthly Salary
   - Hourly Rate
   - Project-Based
   - Commission-Based
2. **Amount**: Enter the payment amount (required)
3. **Payment Frequency**: When they get paid
   - Monthly
   - Bi-weekly
   - Weekly
   - Per Project

### Step 7: Set Working Hours
1. **Hours Per Week**: e.g., 40
2. **Work Schedule**: 
   - Regular (Mon-Fri, 9-5)
   - Flexible Hours
   - Shift-Based
   - On-Call Basis
3. **Overtime Policy**:
   - No Overtime
   - Paid at 1.5x Rate
   - Paid at 2x Rate
   - Compensatory Time Off

### Step 8: Select Benefits
Check all applicable benefits:
- ☑ Health Insurance
- ☑ Paid Annual Leave
- ☑ Sick Leave
- ☑ Transport Allowance
- ☑ Mobile Allowance
- ☑ Tools & Equipment
- ☑ Training & Development
- ☑ Performance Bonus

Add any additional benefits in the text area below.

### Step 9: Set Leave Entitlements
Enter the number of days per year for:
- **Annual Leave**: e.g., 14 days
- **Sick Leave**: e.g., 7 days
- **Casual Leave**: e.g., 7 days

### Step 10: Configure Termination Policies
1. **Notice Period (Employee)**: How much notice employee must give
2. **Notice Period (Company)**: How much notice you must give
3. **Severance Pay**: Compensation if terminated
   - No Severance Pay
   - As Per Labor Law
   - 1 Month Salary
   - 2 Months Salary
   - Custom Agreement

### Step 11: Add Terms & Conditions
1. **Special Clauses**: Enter any specific terms (optional)
2. Check applicable standard clauses:
   - ☑ Include Confidentiality Agreement
   - ☑ Intellectual Property Rights Assignment
   - ☑ Non-Compete Clause (12 months)

### Step 12: Review Summary
The **Contract Summary** section shows a real-time preview:
- Employee name and position
- Contract type and duration
- Compensation details
- Selected benefits

### Step 13: Finalize Contract
Choose one of three actions:

1. **Cancel** - Close without saving
   - Discards all changes
   - Returns to workforce page

2. **Save as Draft** - Save for later
   - Saves incomplete contract
   - Can resume later
   - Shows success notification

3. **Generate & Send Contract** - Complete hiring
   - Validates required fields (Start Date, Amount)
   - Creates contract
   - Sends to applicant's email
   - Moves applicant to Employees
   - Shows success notification
   - Closes drawer automatically

## Tips & Best Practices

### Required vs Optional Fields
- **Required**: Start Date, Salary Amount
- **Recommended**: All fields for complete contracts
- **Optional**: Additional benefits, special clauses

### Employment Type Selection
- **Full-Time**: Use for permanent staff, include all benefits
- **Freelance**: Set project-based payment, flexible hours
- **Part-Time**: Reduce hours/week, pro-rate benefits
- **Trial**: Set 3-month duration, evaluate before permanent

### Compensation Guidelines
- **Monthly Salary**: Fixed amount per month (e.g., 75,000 LKR)
- **Hourly Rate**: Per hour rate (e.g., 500 LKR/hour)
- **Project-Based**: Fixed amount per completed project
- **Commission**: Percentage of sales/revenue

### Leave Entitlements (Sri Lanka Standards)
- Annual Leave: 14 days minimum (private sector)
- Sick Leave: 7 days typical
- Casual Leave: 7 days typical
- Adjust based on employment type and company policy

### Notice Periods
- **Entry Level**: 1 week to 2 weeks
- **Mid Level**: 1 month
- **Senior Level**: 2-3 months
- **Trial Period**: No notice or 1 week

### Common Contract Configurations

#### Example 1: Full-Time Technician
- Type: Full-Time Employee
- Duration: Permanent
- Salary: 75,000 LKR/month
- Hours: 40/week, Regular schedule
- Benefits: Health Insurance, Paid Leave, Sick Leave, Transport, Tools
- Annual Leave: 14 days
- Sick Leave: 7 days
- Notice: 1 month (both parties)

#### Example 2: Freelance Contractor
- Type: Freelance/Contractor
- Duration: 6 months
- Payment: 1,500 LKR/hour
- Hours: Flexible
- Benefits: Tools & Equipment
- Leave: Not applicable
- Notice: 2 weeks

#### Example 3: Trial Period
- Type: Trial Period
- Duration: 3 months
- Salary: 60,000 LKR/month
- Hours: 40/week
- Benefits: Basic (Transport, Tools)
- Annual Leave: Pro-rated
- Notice: 1 week

## Troubleshooting

### Drawer Won't Open
- Ensure you clicked "Accept" button
- Check browser console for errors
- Refresh the page and try again

### Can't Submit Form
- Check Start Date is filled
- Check Salary Amount is filled
- Ensure valid values (no negative numbers)

### Summary Not Updating
- All inputs trigger real-time updates
- If stuck, close and reopen drawer
- Check browser console for JavaScript errors

### Mobile Display Issues
- Drawer should be full-width on mobile
- Scroll to see all sections
- All fields should be single-column

## Keyboard Shortcuts
- **Tab**: Navigate between fields
- **Enter**: Submit form (when focused on submit button)
- **Esc**: Close drawer (coming soon)

## Next Steps After Contract Creation
1. Contract is automatically sent to applicant's email
2. Applicant reviews and signs contract
3. Signed contract returned via email or portal
4. Employee added to active workforce
5. Contract stored in database for records

## Support
For issues or questions about the contract creation feature:
- Check console for error messages
- Review CONTRACT_CREATION_IMPLEMENTATION.md for technical details
- Contact development team for backend integration issues
