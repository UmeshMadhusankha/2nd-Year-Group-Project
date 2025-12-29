// ================================================
// FORM VALIDATION & INTERACTIVITY
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    // Get form elements
    const form = document.getElementById('postJobForm');
    const submitBtn = document.getElementById('submitBtn');
    const fileUpload = document.getElementById('fileUpload');
    const filePreview = document.getElementById('filePreview');

    // Required fields
    const requiredFields = {
        jobTitle: document.getElementById('jobTitle'),
        category: document.getElementById('category'),
        description: document.getElementById('description'),
        location: document.getElementById('location')
    };

    // Error message elements
    const errorElements = {
        jobTitle: document.getElementById('jobTitleError'),
        category: document.getElementById('categoryError'),
        description: document.getElementById('descriptionError'),
        location: document.getElementById('locationError')
    };

    // ================================================
    // VALIDATION FUNCTIONS
    // ================================================

    function validateField(fieldName) {
        const field = requiredFields[fieldName];
        const errorElement = errorElements[fieldName];
        const value = field.value.trim();

        if (!value) {
            errorElement.textContent = 'This field is required';
            field.parentElement.classList.add('error');
            return false;
        } else {
            errorElement.textContent = '';
            field.parentElement.classList.remove('error');
            return true;
        }
    }

    function validateAllFields() {
        let isValid = true;
        
        for (const fieldName in requiredFields) {
            if (!validateField(fieldName)) {
                isValid = false;
            }
        }

        return isValid;
    }

    function updateSubmitButton() {
        const isValid = validateAllFields();
        submitBtn.disabled = !isValid;
    }

    // ================================================
    // EVENT LISTENERS FOR REAL-TIME VALIDATION
    // ================================================

    for (const fieldName in requiredFields) {
        const field = requiredFields[fieldName];
        
        // Validate on blur
        field.addEventListener('blur', function() {
            validateField(fieldName);
            updateSubmitButton();
        });

        // Update button state on input
        field.addEventListener('input', function() {
            updateSubmitButton();
        });

        // For select, also validate on change
        if (field.tagName === 'SELECT') {
            field.addEventListener('change', function() {
                validateField(fieldName);
                updateSubmitButton();
            });
        }
    }

    // ================================================
    // FILE UPLOAD PREVIEW
    // ================================================

    fileUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        
        if (files.length === 0) {
            filePreview.innerHTML = '';
            return;
        }

        filePreview.innerHTML = '<strong>Selected files:</strong>';
        
        files.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.className = 'file-preview-item';
            
            const fileName = file.name;
            const fileSize = (file.size / 1024).toFixed(2) + ' KB';
            
            fileItem.textContent = `${fileName} (${fileSize})`;
            filePreview.appendChild(fileItem);
        });
    });

    // ================================================
    // SET MINIMUM DATE FOR DATE PICKER
    // ================================================

    const preferredDateInput = document.getElementById('preferredDate');
    const today = new Date().toISOString().split('T')[0];
    preferredDateInput.setAttribute('min', today);

    // ================================================
    // FORM SUBMISSION
    // ================================================

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Final validation check
        if (!validateAllFields()) {
            alert('Please fill in all required fields');
            return;
        }

        // Collect form data
        const formData = {
            jobTitle: document.getElementById('jobTitle').value.trim(),
            category: document.getElementById('category').value,
            description: document.getElementById('description').value.trim(),
            budget: document.getElementById('budget').value,
            preferredDate: document.getElementById('preferredDate').value,
            location: document.getElementById('location').value.trim(),
            showToCompanies: document.getElementById('showToCompanies').checked,
            showToRepairers: document.getElementById('showToRepairers').checked,
            urgent: document.getElementById('urgent').checked,
            jobType: document.querySelector('input[name="jobType"]:checked').value,
            files: Array.from(fileUpload.files).map(f => f.name)
        };

        // Log to console
        console.log('Job Posted Successfully!', formData);

        // Show success message
        alert('✅ Job posted successfully!\n\nYour job has been submitted and will be visible to repairers shortly.');

        // Optional: Reset form
        // form.reset();
        // filePreview.innerHTML = '';
        // submitBtn.disabled = true;

        // Redirect to dashboard (uncomment when ready)
        // window.location.href = 'dashboard.php';
    });

    // ================================================
    // INITIAL VALIDATION CHECK
    // ================================================

    updateSubmitButton();
});