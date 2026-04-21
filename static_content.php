
Task 1: 
Add Company Motto, follow these step-by-step instructions. I've broken it down by file so you can handle one part at a time.

Step 1: Database Update
First, you need a place to store the motto. Run this SQL command in your MySQL console (e.g., via phpMyAdmin or the mysql CLI):

sql
ALTER TABLE `company` ADD COLUMN `motto` VARCHAR(255) DEFAULT NULL;
Step 2: Backend Model Update
Open 

CompanyModel.php
 and add 'motto' to the $allowedFields array inside the 

updateProfile
 function (around line 83).

php
// Inside updateProfile method
$allowedFields = [
    'name', 'business_type', 'contact_no', 'description', 
    'website', 'city', 'province', 'postal_code', 
    'facebook', 'instagram', 'linkedin', 'twitter',
    'alternate_phone', 'whatsapp',
    'skills', 'registration_no', 'tax_id', 'established_year',
    'motto' // <--- Add this line
];
Step 3: Frontend HTML Update
Open 

profile.php
 and find the "Company Details" section (around line 101). Add a new form group for the motto:

html
<div class="form-group">
    <label for="companyMotto">Company Motto</label>
    <input type="text" id="companyMotto" placeholder="Enter your company motto (e.g., Quality First)">
</div>
Step 4: Frontend JavaScript (Loading Data)
In the same file (

profile.php
), scroll down to the fetchCompanyProfile() function (around line 1455). Add the line to populate the input:

javascript
// Inside fetchCompanyProfile, after result.data is retrieved
document.getElementById('companyMotto').value = data.motto || '';
Step 5: Frontend JavaScript (Saving Data)
Still in 

profile.php
, find the saveCompanyProfile() function (around line 1620). Add the motto to the data object:

javascript
// Inside saveCompanyProfile data object
const data = {
    action: 'update_profile',
    name: document.getElementById('companyName').value,
    motto: document.getElementById('companyMotto').value, // <--- Add this line
    // ... rest of the fields
};
Step 6: Display on Dashboard
Finally, display it on your 

dashboard.php
. Find the header area and add a line to echo the motto:

php
<!-- Somewhere near the welcome message or company name -->
<p class="company-motto">"<?php echo htmlspecialchars($profile['motto'] ?? ''); ?>"</p>
Tip: After you finish these steps, try saving a motto in your profile and then check if it appears on your dashboard! Let me know when you're ready for Task 2 or if you get stuck.



Task 2:
 Advanced Registration Number Validation
This task will help you practice Frontend Validation and Regular Expressions (Regex).

The Goal: Ensure the "Business Registration Number" follows the format: BR- + 5 digits + 1 Capital Letter (e.g., BR-12345A).

1. Add an Error Message placeholder
In 

profile.php
, find your registrationNumber input and add a small tag below it to show the error:

html
<div class="form-group">
    <label for="registrationNumber">Business Registration Number</label>
    <input type="text" id="registrationNumber" placeholder="e.g., BR-12345A">
    <!-- Add this line below -->
    <span id="regError" style="color: red; font-size: 12px; display: none;">Invalid format! Expected BR-12345A</span>
</div>
2. Add the Validation Logic (JavaScript)
Scroll down to your <script> section in 

profile.php
. We need a function to check the format. Add this function:

javascript
function validateRegistration() {
    const regInput = document.getElementById('registrationNumber');
    const errorSpan = document.getElementById('regError');
    const saveBtn = document.getElementById('saveCompanyBtn');
    
    // Regex: ^ (start), BR-, \d{5} (5 digits), [A-Z] (one capital letter), $ (end)
    const regex = /^BR-\d{5}[A-Z]$/;
    
    if (regInput.value === "" || regex.test(regInput.value)) {
        errorSpan.style.display = 'none';
        regInput.style.borderColor = ''; // Reset border
        saveBtn.disabled = false;
        return true;
    } else {
        errorSpan.style.display = 'block';
        regInput.style.borderColor = 'red';
        saveBtn.disabled = true; // Disable save button if invalid
        return false;
    }
}
3. Trigger the validation
Inside your document.addEventListener('DOMContentLoaded', ...) block, add an event listener so it validates as you type:

javascript
document.getElementById('registrationNumber').addEventListener('input', validateRegistration);
4. Final Guard in Save Function
In your saveCompanyProfile() function, add a check at the very beginning to prevent saving if the validation fails:

javascript
async function saveCompanyProfile() {
    if (!validateRegistration()) {
        showAlert('Please fix the registration number format.', 'warning');
        return;
    }
    // ... the rest of your save code ...
}
Try it out: Go to your profile, type something like 123, and see if the red error appears and the "Save" button gets disabled!





service category sort

Task 1:
Add Company Motto, follow these step-by-step instructions. I've broken it down by file so you can handle one part at a time.

Step 1: Database Update
First, you need a place to store the motto. Run this SQL command in your MySQL console (e.g., via phpMyAdmin or the mysql CLI):

sql
ALTER TABLE `company` ADD COLUMN `motto` VARCHAR(255) DEFAULT NULL;
Step 2: Backend Model Update
Open

CompanyModel.php
 and add 'motto' to the $allowedFields array inside the

updateProfile
 function (around line 83).

php
// Inside updateProfile method
$allowedFields = [
    'name', 'business_type', 'contact_no', 'description',
    'website', 'city', 'province', 'postal_code',
    'facebook', 'instagram', 'linkedin', 'twitter',
    'alternate_phone', 'whatsapp',
    'skills', 'registration_no', 'tax_id', 'established_year',
    'motto' // <--- Add this line
];
Step 3: Frontend HTML Update
Open

profile.php
 and find the "Company Details" section (around line 101). Add a new form group for the motto:

html
<div class="form-group">
    <label for="companyMotto">Company Motto</label>
    <input type="text" id="companyMotto" placeholder="Enter your company motto (e.g., Quality First)">
</div>
Step 4: Frontend JavaScript (Loading Data)
In the same file (

profile.php
), scroll down to the fetchCompanyProfile() function (around line 1455). Add the line to populate the input:

javascript
// Inside fetchCompanyProfile, after result.data is retrieved
document.getElementById('companyMotto').value = data.motto || '';
Step 5: Frontend JavaScript (Saving Data)
Still in

profile.php
, find the saveCompanyProfile() function (around line 1620). Add the motto to the data object:

javascript
// Inside saveCompanyProfile data object
const data = {
    action: 'update_profile',
    name: document.getElementById('companyName').value,
    motto: document.getElementById('companyMotto').value, // <--- Add this line
    // ... rest of the fields
};
Step 6: Display on Dashboard
Finally, display it on your

dashboard.php
. Find the header area and add a line to echo the motto:

php
<!-- Somewhere near the welcome message or company name -->
<p class="company-motto">"<?php echo htmlspecialchars($profile['motto'] ?? ''); ?>"</p>
Tip: After you finish these steps, try saving a motto in your profile and then check if it appears on your dashboard! Let me know when you're ready for Task 2 or if you get stuck.



Task 2:
Advanced Registration Number Validation
This task will help you practice Frontend Validation and Regular Expressions (Regex).

The Goal: Ensure the "Business Registration Number" follows the format: BR- + 5 digits + 1 Capital Letter (e.g., BR-12345A).

1. Add an Error Message placeholder
In

profile.php
, find your registrationNumber input and add a small tag below it to show the error:

html
<div class="form-group">
    <label for="registrationNumber">Business Registration Number</label>
    <input type="text" id="registrationNumber" placeholder="e.g., BR-12345A">
    <!-- Add this line below -->
    <span id="regError" style="color: red; font-size: 12px; display: none;">Invalid format! Expected BR-12345A</span>
</div>
2. Add the Validation Logic (JavaScript)
Scroll down to your <script> section in

profile.php
. We need a function to check the format. Add this function:

javascript
function validateRegistration() {
    const regInput = document.getElementById('registrationNumber');
    const errorSpan = document.getElementById('regError');
    const saveBtn = document.getElementById('saveCompanyBtn');

    // Regex: ^ (start), BR-, \d{5} (5 digits), [A-Z] (one capital letter), $ (end)
    const regex = /^BR-\d{5}[A-Z]$/;

    if (regInput.value === "" || regex.test(regInput.value)) {
        errorSpan.style.display = 'none';
        regInput.style.borderColor = ''; // Reset border
        saveBtn.disabled = false;
        return true;
    } else {
        errorSpan.style.display = 'block';
        regInput.style.borderColor = 'red';
        saveBtn.disabled = true; // Disable save button if invalid
        return false;
    }
}
3. Trigger the validation
Inside your document.addEventListener('DOMContentLoaded', ...) block, add an event listener so it validates as you type:

javascript
document.getElementById('registrationNumber').addEventListener('input', validateRegistration);
4. Final Guard in Save Function
In your saveCompanyProfile() function, add a check at the very beginning to prevent saving if the validation fails:

javascript
async function saveCompanyProfile() {
    if (!validateRegistration()) {
        showAlert('Please fix the registration number format.', 'warning');
        return;
    }
    // ... the rest of your save code ...
}
Try it out: Go to your profile, type something like 123, and see if the red error appears and the "Save" button gets disabled!

