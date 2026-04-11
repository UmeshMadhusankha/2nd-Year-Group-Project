/* ================================================
   MY PROFILE PAGE JAVASCRIPT
   Full CRUD: Load, Edit, Save, Change Password, 
   Upload Photo, and Topbar sync
   ================================================ */

document.addEventListener('DOMContentLoaded', function () {
    // ── Base API path ──
    const API_BASE = '/2nd-Year-Group-Project/FixLanka/api/repairers.php';

    // ── DOM references ──
    const editProfileBtn   = document.getElementById('edit-profile-btn');
    const saveChangesBtn   = document.getElementById('save-changes-btn');
    const cancelChangesBtn = document.getElementById('cancel-changes-btn');
    const changePasswordBtn = document.getElementById('change-password-btn');
    const photoUploadBtn   = document.getElementById('photo-upload-btn');
    const photoUploadInput = document.getElementById('photo-upload-input');
    const profilePhoto     = document.getElementById('profile-photo');

    // Modal
    const changePasswordModal = document.getElementById('change-password-modal');
    const closePasswordModal  = document.getElementById('close-password-modal');
    const cancelPasswordChange = document.getElementById('cancel-password-change');
    const passwordForm        = document.getElementById('password-form');

    // Profile dropdown
    const profileMenu     = document.querySelector('.profile-menu');

    // Form
    const profileForm = document.getElementById('profile-form');
    const formInputs  = document.querySelectorAll('.form-input:not(.time-input)');
    const timeInputs  = document.querySelectorAll('.form-input.time-input');
    const formSelects = document.querySelectorAll('.form-select');
    const districtCheckboxes = document.querySelectorAll('input[name="districts[]"]');
    const sundayClosedCheckbox = document.getElementById('sunday-closed');

    // ── State ──
    let isEditing = false;
    let originalFormData = {};
    let currentProfileData = null;

    // Skills tags
    const skillsInput = document.getElementById('skillsInput');
    const skillsTagsContainer = document.getElementById('skillsTags');
    let skillsTags = [];

    // ── Init ──
    init();

    function init() {
        loadProfileData();
        addEventListeners();
    }
    function syncCategoryNameFromSelect() {
        var catSelect = document.getElementById('service-category');
        var catNameInput = document.getElementById('service-category-name');
        if (!catNameInput) return;

        if (currentProfileData && (currentProfileData.category_name || '').toString().trim() && (!isEditing)) {
            catNameInput.value = (currentProfileData.category_name || '').toString().trim();
            return;
        }

        if (catSelect && catSelect.selectedOptions && catSelect.selectedOptions.length) {
            var optText = (catSelect.selectedOptions[0].textContent || '').trim();
            catNameInput.value = (catSelect.value ? optText : '—');
        } else {
            catNameInput.value = '—';
        }
    }

    /* ──────────────────────────────────────────────
       READ – Load profile from server
    ────────────────────────────────────────────── */
    function loadProfileData() {
        if (!window.currentUserId) {
            console.error('No user ID available');
            return;
        }

        fetch(API_BASE + '?action=getDetails&id=' + window.currentUserId)
            .then(function (r) { return r.json(); })
            .then(function (result) {
                if (result.success && result.data) {
                    currentProfileData = result.data;
                    populateProfileData(result.data);
                    storeOriginalFormData();
                } else {
                    showNotification('Failed to load profile data.', 'error');
                }
            })
            .catch(function () { showNotification('Network error loading profile.', 'error'); });
    }

    function populateProfileData(data) {
        // ── Left column ──
        var profileName = document.getElementById('profileDisplayName');
        if (profileName) profileName.textContent = data.full_name || (data.f_name + ' ' + data.l_name);

        // Profile photo (page + topbar)
        if (data.profilePicture) {
            var photoUrl = '/2nd-Year-Group-Project/FixLanka/' + data.profilePicture;
            if (profilePhoto) profilePhoto.src = photoUrl;
            syncTopbarPhoto(photoUrl);
        }

        // Rating stars
        renderRatingStars(data.ratings);

        // Rating text
        var ratingText = document.getElementById('profileRatingText');
        if (ratingText) {
            var avg = data.ratings !== undefined ? parseFloat(data.ratings).toFixed(1) : '0.0';
            var cnt = data.reviewCount !== undefined ? data.reviewCount : 0;
            ratingText.textContent = avg + ' (' + cnt + ' reviews)';
        }

        // Quick info
        var memberSince = document.getElementById('profileMemberSince');
        if (memberSince && data.dateJoined) {
            memberSince.textContent = new Date(data.dateJoined).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        }
        var jobsCompleted = document.getElementById('profileJobsCompleted');
        if (jobsCompleted) jobsCompleted.textContent = data.completedJobsCount || 0;

        var successRate = document.getElementById('profileSuccessRate');
        if (successRate) {
            var pct = data.successRatePct;
            var num = (pct === null || pct === undefined) ? NaN : Number(pct);
            successRate.textContent = Number.isFinite(num) ? (Math.round(num) + '%') : '—';
        }

        var responseTime = document.getElementById('profileResponseTime');
        if (responseTime) responseTime.textContent = '—';

        // ── Right column form fields ──
        setVal('full-name', data.full_name || (data.f_name + ' ' + data.l_name));
        setVal('email', data.email || '');
        setVal('phone', data.phoneNumber || '');

        var catSelect = document.getElementById('service-category');
        if (catSelect && data.category_id) catSelect.value = data.category_id;

        var catNameInput = document.getElementById('service-category-name');
        if (catNameInput) {
            var nameFromApi = (data.category_name || '').toString().trim();
            if (nameFromApi) {
                catNameInput.value = nameFromApi;
            } else if (catSelect && catSelect.selectedOptions && catSelect.selectedOptions.length) {
                var optText = (catSelect.selectedOptions[0].textContent || '').trim();
                catNameInput.value = (catSelect.value ? optText : '—');
            } else {
                catNameInput.value = '—';
            }
        }

        // Districts
        if (data.districts) {
            var list = data.districts.split(',').map(function (d) { return d.trim(); });
            districtCheckboxes.forEach(function (cb) { cb.checked = list.includes(cb.value); });
        } else {
            districtCheckboxes.forEach(function (cb) { cb.checked = false; });
        }

        var availSelect = document.getElementById('availability');
        if (availSelect && data.availability) availSelect.value = data.availability;

        // Topbar dropdown name + email
        syncTopbarName(data.full_name || (data.f_name + ' ' + data.l_name));
        syncTopbarEmail(data.email || '');

        // Skills
        setSkillsFromData(data.skills);
        renderSkillsTags();
    }

    function setSkillsFromData(skillsValue) {
        if (Array.isArray(skillsValue)) {
            skillsTags = normalizeTags(skillsValue);
            return;
        }
        const raw = (skillsValue || '').toString();
        skillsTags = normalizeTags(raw.split(',').map(s => s.trim()).filter(Boolean));
    }

    function normalizeTags(tags) {
        const seen = new Set();
        const out = [];
        (tags || []).forEach(t => {
            const cleaned = (t || '').toString().trim().replace(/\s+/g, ' ');
            if (!cleaned) return;
            const key = cleaned.toLowerCase();
            if (seen.has(key)) return;
            seen.add(key);
            out.push(cleaned);
        });
        return out;
    }

    function renderSkillsTags() {
        if (!skillsTagsContainer) return;

        const html = skillsTags.map((tag, idx) => {
            const removeBtn = isEditing
                ? `<button type="button" class="skill-tag-remove" data-idx="${idx}" aria-label="Remove ${escapeHtml(tag)}">×</button>`
                : '';
            return `<span class="skill-tag">${escapeHtml(tag)}${removeBtn}</span>`;
        }).join('');

        skillsTagsContainer.innerHTML = html;
    }

    function escapeHtml(str) {
        return (str || '').toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // Keep category name field in sync with dropdown
    (function wireCategorySelectSync() {
        var catSelect = document.getElementById('service-category');
        if (!catSelect) return;
        catSelect.addEventListener('change', function () {
            syncCategoryNameFromSelect();
        });
    })();

    function renderRatingStars(rating) {
        var container = document.getElementById('profileRatingStars');
        if (!container) return;
        var r = parseFloat(rating) || 0;
        var html = '';
        for (var i = 1; i <= 5; i++) {
            if (i <= Math.floor(r)) html += '<i class="fas fa-star"></i>';
            else if (i - r < 1) html += '<i class="fas fa-star-half-alt"></i>';
            else html += '<i class="far fa-star"></i>';
        }
        container.innerHTML = html;
    }

    /* ──────────────────────────────────────────────
       EDIT MODE – Toggle, Enter, Exit
    ────────────────────────────────────────────── */
    function storeOriginalFormData() {
        originalFormData = {};
        formInputs.forEach(function (inp) { originalFormData[inp.id] = inp.value; });
        timeInputs.forEach(function (inp) { originalFormData[inp.id] = inp.value; });
        formSelects.forEach(function (sel) { originalFormData[sel.id] = sel.value; });
        districtCheckboxes.forEach(function (cb) { originalFormData['district_' + cb.value] = cb.checked; });
        if (sundayClosedCheckbox) originalFormData['sunday-closed'] = sundayClosedCheckbox.checked;
    }

    function restoreOriginalFormData() {
        formInputs.forEach(function (inp) { if (originalFormData[inp.id] !== undefined) inp.value = originalFormData[inp.id]; });
        timeInputs.forEach(function (inp) { if (originalFormData[inp.id] !== undefined) inp.value = originalFormData[inp.id]; });
        formSelects.forEach(function (sel) { if (originalFormData[sel.id] !== undefined) sel.value = originalFormData[sel.id]; });
        districtCheckboxes.forEach(function (cb) { if (originalFormData['district_' + cb.value] !== undefined) cb.checked = originalFormData['district_' + cb.value]; });
        if (sundayClosedCheckbox && originalFormData['sunday-closed'] !== undefined) sundayClosedCheckbox.checked = originalFormData['sunday-closed'];
        clearAllFieldErrors();
    }

    function toggleEditMode() {
        if (isEditing) {
            exitEditMode();
        } else {
            enterEditMode();
        }
    }

    function enterEditMode() {
        isEditing = true;
        formInputs.forEach(function (inp) { inp.removeAttribute('readonly'); inp.classList.add('editable'); });
        timeInputs.forEach(function (inp) { inp.removeAttribute('readonly'); inp.classList.add('editable'); });
        formSelects.forEach(function (sel) { sel.removeAttribute('disabled'); sel.classList.add('editable'); });
        districtCheckboxes.forEach(function (cb) { cb.removeAttribute('disabled'); });
        if (sundayClosedCheckbox) sundayClosedCheckbox.removeAttribute('disabled');

        syncCategoryNameFromSelect();

        if (skillsInput) {
            skillsInput.removeAttribute('readonly');
            skillsInput.classList.add('editable');
        }
        renderSkillsTags();

        editProfileBtn.style.display = 'none';
        saveChangesBtn.style.display = 'inline-flex';
        cancelChangesBtn.style.display = 'inline-flex';
        showNotification('Edit mode enabled. Make your changes and click Save.', 'info');
    }

    function exitEditMode() {
        isEditing = false;
        formInputs.forEach(function (inp) { inp.setAttribute('readonly', 'readonly'); inp.classList.remove('editable'); });
        timeInputs.forEach(function (inp) { inp.setAttribute('readonly', 'readonly'); inp.classList.remove('editable'); });
        formSelects.forEach(function (sel) { sel.setAttribute('disabled', 'disabled'); sel.classList.remove('editable'); });
        districtCheckboxes.forEach(function (cb) { cb.setAttribute('disabled', 'disabled'); });
        if (sundayClosedCheckbox) sundayClosedCheckbox.setAttribute('disabled', 'disabled');

        syncCategoryNameFromSelect();

        if (skillsInput) {
            skillsInput.setAttribute('readonly', 'readonly');
            skillsInput.classList.remove('editable');
            skillsInput.value = '';
        }
        renderSkillsTags();

        editProfileBtn.style.display = 'inline-flex';
        saveChangesBtn.style.display = 'none';
        cancelChangesBtn.style.display = 'none';
        clearAllFieldErrors();
    }

    /* ──────────────────────────────────────────────
       UPDATE – Save profile changes to server
    ────────────────────────────────────────────── */
    function handleSaveChanges(e) {
        e.preventDefault();
        if (!validateForm()) return;

        // Collect selected districts
        var selectedDistricts = [];
        districtCheckboxes.forEach(function (cb) { if (cb.checked) selectedDistricts.push(cb.value); });

        var payload = {
            id: window.currentUserId,
            full_name: getVal('full-name'),
            email: getVal('email'),
            phone: getVal('phone'),
            category_id: document.getElementById('service-category').value || null,
            districts: selectedDistricts.join(', '),
            availability: document.getElementById('availability').value,
            about: currentProfileData ? (currentProfileData.about || '') : '',
            skills: skillsTags.join(', ')
        };

        // Loading state
        saveChangesBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Saving...</span>';
        saveChangesBtn.disabled = true;

        fetch(API_BASE + '?action=updateProfile', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(function (r) { return r.json(); })
        .then(function (result) {
            saveChangesBtn.innerHTML = '<i class="fas fa-save"></i><span>Save Changes</span>';
            saveChangesBtn.disabled = false;

            if (result.success) {
                currentProfileData = result.data || currentProfileData;
                populateProfileData(currentProfileData);
                storeOriginalFormData();
                exitEditMode();
                showNotification('Profile updated successfully!', 'success');
            } else {
                showNotification(result.message || 'Failed to update profile.', 'error');
            }
        })
        .catch(function () {
            saveChangesBtn.innerHTML = '<i class="fas fa-save"></i><span>Save Changes</span>';
            saveChangesBtn.disabled = false;
            showNotification('Network error. Please try again.', 'error');
        });
    }

    function handleCancelChanges(e) {
        e.preventDefault();
        restoreOriginalFormData();
        if (currentProfileData) {
            setSkillsFromData(currentProfileData.skills);
            renderSkillsTags();
        }
        exitEditMode();
        showNotification('Changes cancelled.', 'info');
    }

    function handleSkillsKeydown(e) {
        if (!isEditing) return;
        if (e.key !== 'Enter') return;
        e.preventDefault();

        const val = (skillsInput.value || '').trim();
        if (!val) return;

        skillsTags = normalizeTags(skillsTags.concat([val]));
        skillsInput.value = '';
        renderSkillsTags();
    }

    function handleSkillsTagClick(e) {
        const btn = e.target.closest('.skill-tag-remove');
        if (!btn) return;
        if (!isEditing) return;
        const idx = parseInt(btn.getAttribute('data-idx'), 10);
        if (Number.isNaN(idx)) return;
        skillsTags.splice(idx, 1);
        renderSkillsTags();
    }

    /* ──────────────────────────────────────────────
       CHANGE PASSWORD
    ────────────────────────────────────────────── */
    function showChangePasswordModal() {
        changePasswordModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(function () { document.getElementById('current-password').focus(); }, 100);
    }

    function hideChangePasswordModal() {
        changePasswordModal.style.display = 'none';
        document.body.style.overflow = '';
        passwordForm.reset();
        clearPasswordErrors();
    }

    function handlePasswordChange(e) {
        e.preventDefault();

        var currentPwd = document.getElementById('current-password').value;
        var newPwd     = document.getElementById('new-password').value;
        var confirmPwd = document.getElementById('confirm-password').value;

        if (!validatePasswordChange(currentPwd, newPwd, confirmPwd)) return;

        var submitBtn = passwordForm.querySelector('button[type="submit"]');
        var origHTML  = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;

        fetch(API_BASE + '?action=changePassword', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: window.currentUserId,
                current_password: currentPwd,
                new_password: newPwd
            })
        })
        .then(function (r) { return r.json(); })
        .then(function (result) {
            submitBtn.innerHTML = origHTML;
            submitBtn.disabled = false;

            if (result.success) {
                hideChangePasswordModal();
                showNotification('Password changed successfully!', 'success');
            } else {
                showNotification(result.message || 'Failed to change password.', 'error');
                if (result.message && result.message.toLowerCase().indexOf('current password') !== -1) {
                    showPasswordError('current-password', result.message);
                }
            }
        })
        .catch(function () {
            submitBtn.innerHTML = origHTML;
            submitBtn.disabled = false;
            showNotification('Network error. Please try again.', 'error');
        });
    }

    function validatePasswordChange(current, newPwd, confirm) {
        clearPasswordErrors();
        var valid = true;

        if (!current) { showPasswordError('current-password', 'Current password is required'); valid = false; }
        if (!newPwd) { showPasswordError('new-password', 'New password is required'); valid = false; }
        else if (newPwd.length < 8) { showPasswordError('new-password', 'Password must be at least 8 characters'); valid = false; }

        if (!confirm) { showPasswordError('confirm-password', 'Please confirm your new password'); valid = false; }
        else if (newPwd !== confirm) { showPasswordError('confirm-password', 'Passwords do not match'); valid = false; }

        return valid;
    }

    function showPasswordError(fieldId, msg) {
        var field = document.getElementById(fieldId);
        field.classList.add('error');
        var el = document.createElement('span');
        el.className = 'field-error';
        el.textContent = msg;
        field.parentNode.appendChild(el);
    }

    function clearPasswordErrors() {
        passwordForm.querySelectorAll('.form-input').forEach(function (f) {
            f.classList.remove('error');
            var err = f.parentNode.querySelector('.field-error');
            if (err) err.remove();
        });
    }

    /* ──────────────────────────────────────────────
       PHOTO UPLOAD
    ────────────────────────────────────────────── */
    function triggerPhotoUpload() {
        photoUploadInput.click();
    }

    function handlePhotoUpload(e) {
        var file = e.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            showNotification('Please select a valid image file.', 'error');
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showNotification('File size must be less than 5 MB.', 'error');
            return;
        }

        // Show instant preview while uploading
        var reader = new FileReader();
        reader.onload = function (ev) {
            profilePhoto.src = ev.target.result;
            syncTopbarPhoto(ev.target.result);
        };
        reader.readAsDataURL(file);

        // Upload to server
        var formData = new FormData();
        formData.append('photo', file);
        formData.append('id', window.currentUserId);

        showNotification('Uploading photo...', 'info');

        fetch(API_BASE + '?action=uploadPhoto', {
            method: 'POST',
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (result) {
            if (result.success && result.data) {
                var url = result.data.url;
                profilePhoto.src = url;
                syncTopbarPhoto(url);
                if (currentProfileData) currentProfileData.profilePicture = result.data.profilePicture;
                showNotification('Profile photo updated successfully!', 'success');
            } else {
                // Revert preview on failure
                if (currentProfileData && currentProfileData.profilePicture) {
                    var oldUrl = '/2nd-Year-Group-Project/FixLanka/' + currentProfileData.profilePicture;
                    profilePhoto.src = oldUrl;
                    syncTopbarPhoto(oldUrl);
                }
                showNotification(result.message || 'Failed to upload photo.', 'error');
            }
        })
        .catch(function () {
            showNotification('Network error uploading photo.', 'error');
        });

        // Reset input so the same file can be re-selected
        photoUploadInput.value = '';
    }

    /* ──────────────────────────────────────────────
       TOPBAR SYNC – update profile info in header
    ────────────────────────────────────────────── */
    function syncTopbarPhoto(url) {
        var topbarAvatar = document.querySelector('.profile-menu .profile-avatar');
        if (topbarAvatar) topbarAvatar.src = url;
    }

    function syncTopbarName(name) {
        var el = document.querySelector('.profile-dropdown-name');
        if (el) el.textContent = name;
    }

    function syncTopbarEmail(email) {
        var el = document.querySelector('.profile-dropdown-email');
        if (el) el.textContent = email;
    }

    /* ──────────────────────────────────────────────
       FORM VALIDATION
    ────────────────────────────────────────────── */
    function validateForm() {
        var valid = true;
        clearAllFieldErrors();

        var fullName = document.getElementById('full-name');
        var email = document.getElementById('email');
        var phone = document.getElementById('phone');

        if (!fullName.value.trim()) { showFieldError(fullName, 'Full name is required'); valid = false; }
        if (!email.value.trim()) { showFieldError(email, 'Email is required'); valid = false; }
        else if (!isValidEmail(email.value)) { showFieldError(email, 'Please enter a valid email address'); valid = false; }
        if (!phone.value.trim()) { showFieldError(phone, 'Phone number is required'); valid = false; }
        else if (!isValidPhone(phone.value)) { showFieldError(phone, 'Please enter a valid phone number (e.g., 0771234567)'); valid = false; }

        return valid;
    }

    function isValidEmail(v) {
        return /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v);
    }

    function isValidPhone(v) {
        var digits = v.replace(/\D/g, '');
        if (digits.length === 10 && digits.charAt(0) === '0') return true;
        if ((digits.length === 11 || digits.length === 12) && digits.indexOf('94') === 0) return true;
        return /^[\+]?[(]?[0-9]{1,4}[)]?[-\s.]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,9}$/.test(v) && digits.length >= 10;
    }

    function showFieldError(field, msg) {
        clearFieldError(field);
        field.classList.add('error');
        var el = document.createElement('span');
        el.className = 'field-error';
        el.textContent = msg;
        field.parentNode.appendChild(el);
    }

    function clearFieldError(field) {
        field.classList.remove('error');
        var err = field.parentNode.querySelector('.field-error');
        if (err) err.remove();
    }

    function clearAllFieldErrors() {
        document.querySelectorAll('.field-error').forEach(function (e) { e.remove(); });
        document.querySelectorAll('.form-input.error, .form-select.error').forEach(function (f) { f.classList.remove('error'); });
    }

    /* ──────────────────────────────────────────────
       EVENT LISTENERS
    ────────────────────────────────────────────── */
    function addEventListeners() {
        // Edit / Save / Cancel
        editProfileBtn.addEventListener('click', toggleEditMode);
        saveChangesBtn.addEventListener('click', handleSaveChanges);
        cancelChangesBtn.addEventListener('click', handleCancelChanges);

        if (skillsInput) {
            skillsInput.addEventListener('keydown', handleSkillsKeydown);
        }
        if (skillsTagsContainer) {
            skillsTagsContainer.addEventListener('click', handleSkillsTagClick);
        }
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (isEditing) handleSaveChanges(e);
        });

        // Password modal
        changePasswordBtn.addEventListener('click', showChangePasswordModal);
        closePasswordModal.addEventListener('click', hideChangePasswordModal);
        cancelPasswordChange.addEventListener('click', hideChangePasswordModal);
        passwordForm.addEventListener('submit', handlePasswordChange);

        // Photo upload
        photoUploadBtn.addEventListener('click', triggerPhotoUpload);
        photoUploadInput.addEventListener('change', handlePhotoUpload);

        // Modal overlay click to close
        changePasswordModal.addEventListener('click', function (e) {
            if (e.target === changePasswordModal) hideChangePasswordModal();
        });

        // Real-time validation on blur
        var emailField = document.getElementById('email');
        var phoneField = document.getElementById('phone');
        if (emailField) {
            emailField.addEventListener('blur', function () {
                if (isEditing && this.value && !isValidEmail(this.value)) {
                    showFieldError(this, 'Please enter a valid email address');
                }
            });
            emailField.addEventListener('input', function () {
                if (this.classList.contains('error')) clearFieldError(this);
            });
        }
        if (phoneField) {
            phoneField.addEventListener('blur', function () {
                if (isEditing && this.value && !isValidPhone(this.value)) {
                    showFieldError(this, 'Please enter a valid phone number');
                }
            });
            phoneField.addEventListener('input', function () {
                if (this.classList.contains('error')) clearFieldError(this);
            });
        }

        // Profile dropdown toggle
        if (profileMenu) {
            profileMenu.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('active');
            });
        }
        document.addEventListener('click', function (e) {
            if (profileMenu && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });

        // Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideChangePasswordModal();
                if (profileMenu) profileMenu.classList.remove('active');
            }
        });
    }

    /* ──────────────────────────────────────────────
       NOTIFICATIONS
    ────────────────────────────────────────────── */
    function showNotification(message, type) {
        type = type || 'info';
        // Remove any existing notification to prevent stacking
        var existing = document.querySelector('.notification.show');
        if (existing) existing.remove();

        var icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
        var n = document.createElement('div');
        n.className = 'notification notification-' + type;
        n.innerHTML =
            '<div class="notification-content">' +
                '<i class="fas fa-' + (icons[type] || 'info-circle') + '"></i>' +
                '<span>' + message + '</span>' +
            '</div>' +
            '<button class="notification-close"><i class="fas fa-times"></i></button>';
        document.body.appendChild(n);
        n.querySelector('.notification-close').addEventListener('click', function () { removeNotification(n); });
        setTimeout(function () { n.classList.add('show'); }, 50);
        setTimeout(function () { if (n.parentNode) removeNotification(n); }, 5000);
    }

    function removeNotification(el) {
        el.classList.remove('show');
        setTimeout(function () { if (el.parentNode) el.remove(); }, 300);
    }

    /* ── Helpers ── */
    function setVal(id, v) { var el = document.getElementById(id); if (el) el.value = v; }
    function getVal(id) { var el = document.getElementById(id); return el ? el.value.trim() : ''; }
});

/* ── Injected CSS for notifications & form states ── */
(function () {
    var css = '' +
        '.notification{position:fixed;top:20px;right:20px;background:var(--bg-card,#fff);border-radius:var(--border-radius,8px);box-shadow:0 4px 20px rgba(0,0,0,.15);border-left:4px solid var(--info-color,#3b82f6);padding:16px;display:flex;align-items:center;gap:12px;min-width:300px;max-width:500px;transform:translateX(120%);transition:transform .3s ease;z-index:10001}' +
        '.notification.show{transform:translateX(0)}' +
        '.notification-success{border-left-color:var(--success-color,#22c55e)}' +
        '.notification-error{border-left-color:var(--danger-color,#ef4444)}' +
        '.notification-warning{border-left-color:var(--warning-color,#f59e0b)}' +
        '.notification-content{display:flex;align-items:center;gap:8px;flex:1}' +
        '.notification-content i{font-size:1.15rem}' +
        '.notification-success .notification-content i{color:var(--success-color,#22c55e)}' +
        '.notification-error .notification-content i{color:var(--danger-color,#ef4444)}' +
        '.notification-warning .notification-content i{color:var(--warning-color,#f59e0b)}' +
        '.notification-info .notification-content i{color:var(--info-color,#3b82f6)}' +
        '.notification-close{background:none;border:none;color:var(--text-muted,#888);cursor:pointer;padding:4px;border-radius:4px;transition:background .2s}' +
        '.notification-close:hover{background:var(--bg-secondary,#f1f1f1);color:var(--text-primary,#333)}' +
        '.form-input.error,.form-select.error{border-color:var(--danger-color,#ef4444)!important;box-shadow:0 0 0 3px rgba(239,68,68,.1)}' +
        '.field-error{color:var(--danger-color,#ef4444);font-size:.75rem;margin-top:4px;display:block}' +
        '.form-input.editable,.form-select.editable{border-color:var(--primary-color,#3b82f6);background:var(--bg-primary,#fff)}';
    var s = document.createElement('style');
    s.textContent = css;
    document.head.appendChild(s);
})();
