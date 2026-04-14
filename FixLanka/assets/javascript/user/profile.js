// ================================================
// PROFILE PAGE - LIGHTWEIGHT FRONTEND LOGIC
// ================================================

document.addEventListener('DOMContentLoaded', function() {
    const APP_BASE = '/2nd-Year-Group-Project/FixLanka';
    const UPDATE_USER_API = `${APP_BASE}/api/user/updateUser.php`;
    const backendData = window.profilePageData || {};

    const profileState = {
        firstName: backendData.firstName || '',
        lastName: backendData.lastName || '',
        fullName: backendData.fullName || 'User',
        email: backendData.email || '',
        address: backendData.address || '',
        district: backendData.district || '',
        location: backendData.location || 'Sri Lanka',
        avatar: backendData.avatar || '',
        jobStats: {
            total: Number(backendData.jobStats?.total ?? 0),
            active: Number(backendData.jobStats?.active ?? 0),
            completed: Number(backendData.jobStats?.completed ?? 0),
            pending: Number(backendData.jobStats?.pending ?? 0)
        }
    };

    const editProfileBtn = document.getElementById('editProfileBtn');
    const manageProfileBtn = document.getElementById('manageProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const editProfileForm = document.getElementById('editProfileForm');

    const profileAvatarImg = document.getElementById('profileAvatar');
    const avatarOverlay = document.getElementById('avatarOverlay');
    const avatarInput = document.getElementById('avatarInput');

    const viewQuotesBtn = document.getElementById('viewQuotesBtn');
    const postJobBtn = document.getElementById('postJobBtn');
    const paymentHistoryBtn = document.getElementById('paymentHistoryBtn');
    const helpCenterBtn = document.getElementById('helpCenterBtn');
    const viewAllJobsBtn = document.getElementById('viewAllJobsBtn');
    const viewAllSetupBtn = document.getElementById('viewAllSetupBtn');

    const completionPercentageEl = document.getElementById('completionPercentage');
    const completionTasksEl = document.getElementById('completionTasks');
    const setupLevelTextEl = document.getElementById('setupLevelText');
    const setupHintsListEl = document.getElementById('setupHintsList');
    const progressCircle = document.getElementById('progressCircle');

    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    function init() {
        bindEvents();
        renderProfile();
        animateCounters();
    }

    function bindEvents() {
        editProfileBtn?.addEventListener('click', openEditModal);
        manageProfileBtn?.addEventListener('click', openEditModal);
        closeEditModal?.addEventListener('click', closeModal);
        cancelEditBtn?.addEventListener('click', closeModal);

        editProfileModal?.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && editProfileModal?.classList.contains('show')) {
                closeModal();
            }
        });

        editProfileForm?.addEventListener('submit', handleProfileUpdate);

        avatarOverlay?.addEventListener('click', function() {
            avatarInput?.click();
        });

        avatarInput?.addEventListener('change', handleAvatarUpload);

        viewQuotesBtn?.addEventListener('click', function() {
            window.location.href = `${APP_BASE}/job-history?view=quotes`;
        });

        postJobBtn?.addEventListener('click', function() {
            window.location.href = `${APP_BASE}/post-job`;
        });

        paymentHistoryBtn?.addEventListener('click', function() {
            window.location.href = `${APP_BASE}/job-history`;
        });

        helpCenterBtn?.addEventListener('click', function() {
            window.location.href = `${APP_BASE}/help-center`;
        });

        viewAllJobsBtn?.addEventListener('click', function() {
            window.location.href = `${APP_BASE}/job-history`;
        });

        viewAllSetupBtn?.addEventListener('click', function() {
            renderSetupGame();
            showToast('Setup score refreshed', 'info');
        });
    }

    function openEditModal() {
        editProfileModal?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        editProfileModal?.classList.remove('show');
        document.body.style.overflow = '';
    }

    function renderProfile() {
        setText('.user-full-name', profileState.fullName || 'User');

        const contactItems = document.querySelectorAll('.contact-item span');
        if (contactItems.length >= 2) {
            contactItems[0].textContent = profileState.email || 'N/A';
            contactItems[1].textContent = profileState.location || 'N/A';
        }

        if (profileAvatarImg) {
            profileAvatarImg.src = resolveAvatarUrl(profileState.avatar, profileState.fullName);
        }

        const statEls = document.querySelectorAll('.stat-number');
        if (statEls.length >= 4) {
            statEls[0].textContent = String(profileState.jobStats.total || 0);
            statEls[1].textContent = String(profileState.jobStats.active || 0);
            statEls[2].textContent = String(profileState.jobStats.completed || 0);
            statEls[3].textContent = String(profileState.jobStats.pending || 0);
        }

        prefillEditForm();
        renderSetupGame();
    }

    function prefillEditForm() {
        const firstNameInput = document.getElementById('editFirstName');
        const lastNameInput = document.getElementById('editLastName');
        const emailInput = document.getElementById('editEmail');
        const addressInput = document.getElementById('editAddress');
        const districtInput = document.getElementById('editDistrict');

        if (firstNameInput) firstNameInput.value = profileState.firstName || '';
        if (lastNameInput) lastNameInput.value = profileState.lastName || '';
        if (emailInput) emailInput.value = profileState.email || '';
        if (addressInput) addressInput.value = profileState.address || '';
        if (districtInput) districtInput.value = profileState.district || '';
    }

    function getSetupRules() {
        const hasName = String(profileState.fullName || '').trim().length >= 3;
        const hasValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(profileState.email || '').trim());
        const hasLocation = String(profileState.location || '').trim().length >= 3;
        const hasProfilePhoto = String(profileState.avatar || '').trim() !== '';

        return [
            { label: 'Name added', ok: hasName },
            { label: 'Valid email added', ok: hasValidEmail },
            { label: 'Location added', ok: hasLocation },
            { label: 'Profile photo added', ok: hasProfilePhoto }
        ];
    }

    function renderSetupGame() {
        const rules = getSetupRules();
        const total = rules.length;
        const completed = rules.filter(rule => rule.ok).length;
        const percent = Math.round((completed / total) * 100);

        if (completionPercentageEl) {
            completionPercentageEl.textContent = String(percent);
        }

        if (completionTasksEl) {
            completionTasksEl.innerHTML = rules.map(rule => `
                <div class="task-item ${rule.ok ? 'completed' : 'pending'}">
                    <i class="${rule.ok ? 'fas fa-check-circle' : 'far fa-circle'}"></i>
                    <span>${escapeHtml(rule.label)}</span>
                </div>
            `).join('');
        }

        if (setupLevelTextEl) {
            setupLevelTextEl.textContent = getLevelText(percent);
        }

        if (setupHintsListEl) {
            const pendingRules = rules.filter(rule => !rule.ok);
            if (pendingRules.length === 0) {
                setupHintsListEl.innerHTML = `
                    <div class="activity-item">
                        <div class="activity-icon review">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="activity-details">
                            <p class="activity-text">Profile setup completed. Great work!</p>
                            <span class="activity-time">100% ready</span>
                        </div>
                    </div>
                `;
            } else {
                setupHintsListEl.innerHTML = pendingRules.slice(0, 3).map(rule => `
                    <div class="activity-item">
                        <div class="activity-icon payment">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <div class="activity-details">
                            <p class="activity-text">Next quest: ${escapeHtml(rule.label)}</p>
                            <span class="activity-time">Setup game</span>
                        </div>
                    </div>
                `).join('');
            }
        }

        updateProgressCircle(percent);
    }

    function updateProgressCircle(percent) {
        if (!progressCircle) return;

        const radius = 52;
        const circumference = 2 * Math.PI * radius;
        const offset = circumference - (percent / 100) * circumference;

        progressCircle.style.strokeDasharray = `${circumference} ${circumference}`;
        progressCircle.style.strokeDashoffset = `${offset}`;
    }

    function getLevelText(percent) {
        if (percent >= 90) return 'Level: Profile Master';
        if (percent >= 70) return 'Level: Trusted Member';
        if (percent >= 40) return 'Level: Getting There';
        return 'Level: Starter';
    }

    async function handleProfileUpdate(e) {
        e.preventDefault();

        const firstName = String(document.getElementById('editFirstName')?.value || '').trim();
        const lastName = String(document.getElementById('editLastName')?.value || '').trim();
        const email = String(document.getElementById('editEmail')?.value || '').trim();
        const address = String(document.getElementById('editAddress')?.value || '').trim();
        const district = String(document.getElementById('editDistrict')?.value || '').trim();

        if (!firstName || !lastName || !email || !address) {
            showToast('First name, last name, email and address are required', 'error');
            return;
        }

        const submitBtn = editProfileForm?.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn?.innerHTML || '';
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        }

        try {
            const result = await fetchJson(UPDATE_USER_API, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ firstName, lastName, email, address, district })
            });

            const updated = result?.user || {};
            const mergedFullName = `${String(updated.firstName || firstName).trim()} ${String(updated.lastName || lastName).trim()}`.trim();
            profileState.firstName = String(updated.firstName || firstName);
            profileState.lastName = String(updated.lastName || lastName);
            profileState.fullName = String(updated.fullName || mergedFullName);
            profileState.email = String(updated.email || email);
            profileState.address = String(updated.address || address);
            profileState.district = String(updated.district || district);
            profileState.location = [profileState.address, profileState.district].filter(Boolean).join(', ') || 'Sri Lanka';
            profileState.avatar = String(updated.avatar || profileState.avatar || '');

            renderProfile();
            closeModal();
            showToast('Profile updated successfully', 'success');
        } catch (err) {
            showToast(err.message || 'Failed to update profile', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
    }

    function handleAvatarUpload(e) {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            showToast('Please select an image file', 'error');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showToast('Image size should be less than 5MB', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(evt) {
            profileState.avatar = String(evt.target?.result || '');
            renderProfile();
            showToast('Profile image updated', 'success');
        };
        reader.readAsDataURL(file);
    }

    function resolveAvatarUrl(value, fullName) {
        const trimmed = String(value || '').trim();
        if (trimmed) {
            if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) return trimmed;
            if (trimmed.startsWith('/')) return trimmed;
            return `${APP_BASE}/${trimmed}`;
        }

        const encoded = encodeURIComponent((fullName || 'User').replace(/\s+/g, '+'));
        return `https://ui-avatars.com/api/?name=${encoded}&size=240&background=0ABAB5&color=fff&bold=true`;
    }

    function setText(selector, value) {
        const el = document.querySelector(selector);
        if (el) el.textContent = value;
    }

    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent || '0', 10);
            if (!Number.isFinite(target) || target <= 0) return;

            let current = 0;
            const increment = Math.max(1, Math.ceil(target / 30));
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = String(target);
                    clearInterval(timer);
                } else {
                    counter.textContent = String(current);
                }
            }, 20);
        });
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    async function fetchJson(url, options) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            ...(options || {})
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid server response');
        }

        if (!response.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${response.status})`);
        }

        return data;
    }

    function showToast(message, type = 'success') {
        if (!toast || !toastMessage) return;

        toastMessage.textContent = message;
        toast.className = `toast ${type}`;
        toast.classList.add('show');

        const toastIcon = toast.querySelector('.toast-icon');
        if (toastIcon) {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };
            toastIcon.className = `fas ${icons[type] || icons.success} toast-icon`;
        }

        setTimeout(() => {
            toast.classList.remove('show');
        }, 2600);
    }

    init();
});
