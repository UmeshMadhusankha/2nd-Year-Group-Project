# Task 2: Deadline Logic Guide

This guide explains how to implement complex deadline validation for the Job Posting form in [views/company/workforce.php](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/views/company/workforce.php).

## Objective
*   Prevent the deadline from being more than **3 months** in the future.
*   If the Priority Level is **"Urgent"**, ensure the deadline is within the next **7 days**.
*   Disable the **"Publish Job Posting"** button if these conditions are not met.

---

## Step 1: Add the Validation Function
Find the `<script>` section in [views/company/workforce.php](file:///c:/xampp/htdocs/2nd-Year-Group-Project/FixLanka/views/company/workforce.php) (around line 2750) and add this new function to handle the deadline logic:

```javascript
function validateDeadlineLogic() {
    const deadlineInput = document.getElementById('applicationDeadline');
    const prioritySelect = document.getElementById('priorityLevel');
    const publishBtn = document.getElementById('publishBtn');
    
    if (!deadlineInput || !prioritySelect || !publishBtn) return true;

    const deadlineVal = deadlineInput.value;
    if (!deadlineVal) {
        publishBtn.disabled = false;
        return true;
    }

    const deadlineDate = new Date(deadlineVal);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // 1. Check if deadline is in the past
    if (deadlineDate < today) {
        publishBtn.disabled = true;
        return false;
    }

    // 2. Cannot be more than 3 months in the future
    const threeMonthsFromNow = new Date();
    threeMonthsFromNow.setMonth(today.getMonth() + 3);
    
    if (deadlineDate > threeMonthsFromNow) {
        publishBtn.disabled = true;
        return false;
    }

    // 3. Urgent priority -> MUST be within 7 days
    if (prioritySelect.value === 'urgent') {
        const sevenDaysFromNow = new Date();
        sevenDaysFromNow.setDate(today.getDate() + 7);
        
        if (deadlineDate > sevenDaysFromNow) {
            publishBtn.disabled = true;
            return false;
        }
    }

    // If all checks pass
    publishBtn.disabled = false;
    return true;
}
```

---

## Step 2: Hook into Form Events
To make the validation real-time, you should call this function whenever the deadline or priority changes. Add these event listeners after your function:

```javascript
document.addEventListener('DOMContentLoaded', function() {
    const deadlineInput = document.getElementById('applicationDeadline');
    const prioritySelect = document.getElementById('priorityLevel');

    if (deadlineInput) {
        deadlineInput.addEventListener('change', validateDeadlineLogic);
    }
    if (prioritySelect) {
        prioritySelect.addEventListener('change', validateDeadlineLogic);
    }
});
```

---

## Step 3: Update existing Step Validation
Modify the existing `validateCurrentStep()` function in your script to also check the deadline logic before allowing the user to proceed to the review step.

```javascript
function validateCurrentStep() {
    // ... existing required field checks ...

    // Add this for Step 2
    if (currentStep === 2) {
        if (!validateDeadlineLogic()) {
            showNotification('Deadline does not meet requirements for the selected priority.', 'error');
            return false;
        }
    }

    return isValid;
}
```

---

## Verification
1.  Open the **Create Job Posting** modal.
2.  Go to **Step 2 (Details)**.
3.  Set Priority to **Urgent**.
4.  Pick a date **10 days** from now -> The "Next" button should allow you through but the logic should ideally stop you or the final button should be disabled.
5.  Wait, if the user is on Step 2, they click "Next" to go to Step 3. The "Publish" button is on Step 3.
6.  **Tip**: It's better to show an error message immediately when they pick an invalid date!
