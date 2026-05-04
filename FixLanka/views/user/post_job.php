<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\post_job.php
require_once __DIR__ . '/../../config/session.php';

if (!isLoggedIn()) {
    header('Location: /2nd-Year-Group-Project/FixLanka/login');
    exit;
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job - Fix Lanka</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/post_job.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <main class="main-content">
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <h1>Post a New Job</h1>
                    <p>Fill in the details below to post your repair job</p>
                    <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                        <i class="fas fa-home"></i> Home
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="/2nd-Year-Group-Project/FixLanka/create-job" method="POST" enctype="multipart/form-data" id="jobPostForm">
                    <div class="form-group">
                        <label for="title">Job Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="e.g., Kitchen Sink Repair, AC Installation">
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Loading categories...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" rows="5" required placeholder="Describe your job in detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="district">District <span class="required">*</span></label>
                        <select id="district" name="district" required>
                            <option value="">Select your district</option>
                            <option value="Colombo">Colombo</option>
                            <option value="Gampaha">Gampaha</option>
                            <option value="Kalutara">Kalutara</option>
                            <option value="Kandy">Kandy</option>
                            <option value="Matale">Matale</option>
                            <option value="Nuwara Eliya">Nuwara Eliya</option>
                            <option value="Galle">Galle</option>
                            <option value="Matara">Matara</option>
                            <option value="Hambantota">Hambantota</option>
                            <option value="Jaffna">Jaffna</option>
                            <option value="Kilinochchi">Kilinochchi</option>
                            <option value="Mannar">Mannar</option>
                            <option value="Vavuniya">Vavuniya</option>
                            <option value="Mullaitivu">Mullaitivu</option>
                            <option value="Batticaloa">Batticaloa</option>
                            <option value="Ampara">Ampara</option>
                            <option value="Trincomalee">Trincomalee</option>
                            <option value="Kurunegala">Kurunegala</option>
                            <option value="Puttalam">Puttalam</option>
                            <option value="Anuradhapura">Anuradhapura</option>
                            <option value="Polonnaruwa">Polonnaruwa</option>
                            <option value="Badulla">Badulla</option>
                            <option value="Monaragala">Monaragala</option>
                            <option value="Ratnapura">Ratnapura</option>
                            <option value="Kegalle">Kegalle</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="address">Address <span class="required">*</span></label>
                        <input type="text" id="address" name="address" required placeholder="Enter your full address (street, area)">
                        <label class="address-toggle-row">
                            <input type="checkbox" id="useHomeAddress">
                            <span>Use home address</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label>Service Provider Type <span class="required">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="provider_individual" name="provider_type[]" value="individual">
                                <label for="provider_individual" class="checkbox-label">
                                    <i class="fas fa-user"></i>
                                    Individual Repairer
                                </label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="provider_company" name="provider_type[]" value="company">
                                <label for="provider_company" class="checkbox-label">
                                    <i class="fas fa-building"></i>
                                    Company
                                </label>
                            </div>
                        </div>
                        <small class="hint-text">Select at least one service provider type</small>
                        <span class="error-message" id="provider-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="urgency">Urgency Level <span class="required">*</span></label>
                        <select id="urgency" name="urgency" required>
                            <option value="medium" selected>Medium</option>
                            <option value="urgent">Urgent <span class="pro-text">PRO</span></option>
                        </select>
                        <div class="urgency-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Urgent priority is a PRO feature - Your job will be highlighted to service providers</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="finish_date">Expected Finish Date <span class="required">*</span></label>
                        <input type="date" id="finish_date" name="finish_date" required>
                        <small class="hint-text">When do you need this work completed?</small>
                    </div>

                    <div class="form-group">
                        <label for="photos">Photos (Optional)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="photos" name="photos" accept="image/*" onchange="updateFileName(this)">
                            <label for="photos" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <span class="upload-text">Choose File</span>
                            </label>
                        </div>
                        <small class="hint-text">Upload a photo related to your job request</small>
                        <div id="file-name-display" class="file-preview"></div>
                    </div>

                    <div class="form-group">
                        <label for="job_area">Job Area </label>
                        <select id="job_area" name="job_area">
                            <option value="area1" selected>Area 1</option>
                            <option value="area2">Area 2</option>
                            <option value="area3">Area 3</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn">Post Job</button>
                </form>
            </div>
        </div>
    </main>

    <script>
    function updateFileName(input) {
        const fileDisplay = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            fileDisplay.innerHTML = `<div class="file-preview-item">${fileName}</div>`;
        } else {
            fileDisplay.innerHTML = '';
        }
    }

    // Set minimum date to today
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_id');
        const addressInput = document.getElementById('address');
        const districtSelect = document.getElementById('district');
        const useHomeAddress = document.getElementById('useHomeAddress');
        const finishDateInput = document.getElementById('finish_date');
        const today = new Date().toISOString().split('T')[0];
        finishDateInput.setAttribute('min', today);
        let homeAddressValue = '';
        let homeDistrictValue = '';
        let manualAddressValue = '';
        let manualDistrictValue = '';

        async function loadHomeAddress() {
            if (!addressInput) return;

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/user/loadCurrentUserAddress.php', {
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();
                if (!response.ok || !result || !result.success) {
                    throw new Error(result?.message || 'Failed to load address');
                }

                homeAddressValue = String(result.data?.address || '').trim();
                homeDistrictValue = String(result.data?.district || '').trim();
                if (useHomeAddress && useHomeAddress.checked) {
                    if (homeAddressValue) {
                        addressInput.value = homeAddressValue;
                    }
                    if (districtSelect && homeDistrictValue) {
                        districtSelect.value = homeDistrictValue;
                    }
                }
            } catch (error) {
                homeAddressValue = '';
                homeDistrictValue = '';
            }
        }

        if (addressInput && useHomeAddress) {
            addressInput.addEventListener('input', function() {
                if (!useHomeAddress.checked) {
                    manualAddressValue = addressInput.value;
                }
            });

            if (districtSelect) {
                districtSelect.addEventListener('change', function() {
                    if (!useHomeAddress.checked) {
                        manualDistrictValue = districtSelect.value;
                    }
                });
            }

            useHomeAddress.addEventListener('change', function() {
                if (this.checked) {
                    manualAddressValue = addressInput.value;
                    if (districtSelect) {
                        manualDistrictValue = districtSelect.value;
                    }
                    addressInput.value = homeAddressValue || addressInput.value;
                    if (districtSelect && homeDistrictValue) {
                        districtSelect.value = homeDistrictValue;
                    }
                    addressInput.readOnly = true;
                    addressInput.classList.add('is-readonly');
                } else {
                    addressInput.readOnly = false;
                    addressInput.classList.remove('is-readonly');
                    addressInput.value = manualAddressValue;
                    if (districtSelect) {
                        districtSelect.value = manualDistrictValue;
                    }
                }
            });
        }

        async function loadCategories() {
            if (!categorySelect) return;

            categorySelect.innerHTML = '<option value="">Loading categories...</option>';

            try {
                const response = await fetch('/2nd-Year-Group-Project/FixLanka/api/user/loadCategories.php', {
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();
                if (!response.ok || !result || !result.success || !Array.isArray(result.data)) {
                    throw new Error('Failed to load categories');
                }

                const options = ['<option value="">Select a category</option>'];
                result.data.forEach((category) => {
                    const id = Number(category.category_id || 0);
                    const name = String(category.name || '').trim();
                    if (!Number.isFinite(id) || id <= 0 || !name) return;

                    const escapedName = name
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');

                    options.push(`<option value="${id}">${escapedName}</option>`);
                });

                categorySelect.innerHTML = options.join('');
            } catch (error) {
                categorySelect.innerHTML = '<option value="" disabled>Categories unavailable</option>';
            }
        }

        loadCategories();
        loadHomeAddress();

        // Validate provider type checkboxes
        const form = document.getElementById('jobPostForm');
        const providerCheckboxes = document.querySelectorAll('input[name="provider_type[]"]');
        const providerError = document.getElementById('provider-error');

        form.addEventListener('submit', function(e) {
            const isChecked = Array.from(providerCheckboxes).some(checkbox => checkbox.checked);
            
            if (!isChecked) {
                e.preventDefault();
                providerError.textContent = 'Please select at least one service provider type';
                providerCheckboxes[0].closest('.form-group').classList.add('error');
                return false;
            }
            
            providerError.textContent = '';
            providerCheckboxes[0].closest('.form-group').classList.remove('error');
        });

        // Clear error on checkbox change
        providerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const isChecked = Array.from(providerCheckboxes).some(cb => cb.checked);
                if (isChecked) {
                    providerError.textContent = '';
                    providerCheckboxes[0].closest('.form-group').classList.remove('error');
                }
            });
        });
    });
    </script>
</body>
</html>