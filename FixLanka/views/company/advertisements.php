<?php
/**
 * Advertisement Management Page
 * Displays and manages company advertisements
 */

// Start session and check authentication
require_once '../../config/session.php';

// Require company role
requireRole(['company']);

// Get user data
$userData = getUserData();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advertisement Management - FixLanka Company Dashboard</title>
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/variables.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/progress-bars.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/buttons.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/sidebar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/dashboard.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/advertisements.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/company/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <input type="checkbox" id="sidebar-toggle">

    <div class="dashboard-container">
        <!-- Sidebar Component -->
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header Component -->
            <?php include 'topbar.php'; ?>



            <!-- KPI Cards Section -->
            <section class="kpi-section">
                <!-- Page Header -->
                <header class="page-header">
                    <div class="header-content">
                        <div class="header-main">
                            <div class="title-section">
                                <h1><i class="fas fa-bullhorn"></i> Advertisement Management</h1>
                                <p class="subtitle">Manage your company employees and freelance contractors</p>
                                <nav class="breadcrumbs">
                                    <a href="/2nd-Year-Group-Project/FixLanka/company-dashboard"><i class="fas fa-home"></i> Dashboard</a>
                                    <span class="separator">/</span>
                                    <span class="current">Advertisements</span>
                                </nav>
                            </div>
                            <div class="header-actions">
                                <div class="action-buttons">
                                    <button class="action-btn primary" id="createAdBtn">
                                        <i class="fas fa-plus"></i> Create New Advertisement
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Filters & Controls -->
                <div class="controls-section">
                    <!-- Single Row: All Filters -->
                    <div class="filters-row">
                        <!-- Status Filter -->
                        <select id="statusFilter" class="filter-dropdown">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="approved">Approved</option>
                            <option value="pending">Pending</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="paused">Paused</option>
                            <option value="expired">Expired</option>
                        </select>

                        <!-- Type Filter -->
                        <select id="typeFilter" class="filter-dropdown">
                            <option value="all">All Types</option>
                            <option value="banner">Banner</option>
                            <option value="featured">Featured</option>
                            <option value="sponsored">Sponsored</option>
                        </select>

                        <!-- Date Filter -->
                        <select id="dateFilter" class="filter-dropdown">
                            <option value="all">All Dates</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="quarter">Last 3 Months</option>
                            <option value="year">This Year</option>
                        </select>

                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <button class="view-btn" id="listViewBtn" title="List View">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="view-btn active" id="gridViewBtn" title="Grid View">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                <div class="results-summary" style="display: none;">
                    <div class="results-info">
                        <span class="results-text">
                            Showing <strong id="resultCount">0</strong> of <strong id="totalCount">0</strong> advertisements
                        </span>
                    </div>
                </div>

                <!-- Advertisements List -->
                <div class="ads-section">
                    <div class="ads-container">
                        <!-- Advertisements will be loaded dynamically from API via JavaScript -->
                        <div class="loading-state">
                            <div class="spinner"></div>
                            <p>Loading advertisements...</p>
                        </div>
                    </div>

                    <!-- Empty State (shown when no results) -->
                    <div class="empty-state" id="emptyState" style="display: none;">
                        <div class="empty-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3>No Advertisements Found</h3>
                        <p>You haven't created any advertisements yet. Create your first advertisement to get started.</p>
                        <button class="action-btn primary" onclick="document.getElementById('createAdBtn').click()">
                            <i class="fas fa-plus"></i> Create Advertisement
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>
        </main>
    </div>


    <!-- Create/Edit Advertisement Modal -->
    <div class="modal-overlay" id="adModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 id="modalTitle"><i class="fas fa-plus-circle"></i> Create New Advertisement</h2>
                <button class="modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-content">
                <form id="adForm">
                    <!-- Step 1: Ad Type Selection -->
                    <div class="form-step active" id="step1">
                        <h3 class="step-title">Step 1: Select Advertisement Type</h3>
                        <div class="ad-type-selection">
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="banner" checked>
                                <div class="type-card-content">
                                    <i class="fas fa-image"></i>
                                    <h4>Banner Ad</h4>
                                    <p>Static image advertisement (JPG, PNG, GIF)</p>
                                    <span class="recommended">Recommended</span>
                                </div>
                            </label>
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="featured">
                                <div class="type-card-content">
                                    <i class="fas fa-star"></i>
                                    <h4>Featured Ad</h4>
                                    <p>Higher visibility placement for your promotion</p>
                                </div>
                            </label>
                            <label class="ad-type-card">
                                <input type="radio" name="adType" value="sponsored">
                                <div class="type-card-content">
                                    <i class="fas fa-bullhorn"></i>
                                    <h4>Sponsored Ad</h4>
                                    <p>Sponsored listing to reach more customers</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Upload Media -->
                    <div class="form-step" id="step2">
                        <h3 class="step-title">Step 2: Upload Media</h3>

                        <div class="upload-section">
                            <div class="upload-area" id="uploadArea">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h4>Drag & Drop your files here</h4>
                                <p>or click to browse</p>
                                <input type="file" id="mediaFile" accept="image/*" hidden>
                                <div class="upload-specs">
                                    <p id="uploadSpecsText"><strong>Banner:</strong> Recommended size 1200x628px, Max 5MB</p>
                                </div>
                            </div>
                            <div class="upload-preview" id="uploadPreview" style="display: none;">
                                <img id="previewImage" src="" alt="Preview">
                                <video id="previewVideo" controls style="display: none;"></video>
                                <button type="button" class="btn-remove" id="removeMedia">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="adTitle">Advertisement Title *</label>
                            <input type="text" id="adTitle" placeholder="e.g., Summer Special - 20% Off All Repairs" required>
                        </div>

                        <div class="form-group">
                            <label for="adDescription">Description *</label>
                            <textarea id="adDescription" rows="4" placeholder="Provide a compelling description of your advertisement..." required></textarea>
                            <small>Describe what makes this offer special and why customers should choose your services</small>
                        </div>
                    </div>

                    <!-- Step 3: Target & Details -->
                    <div class="form-step" id="step3">
                        <h3 class="step-title">Step 3: Advertisement Details</h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="adCategory">Service Category *</label>
                                <select id="adCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="plumbing">Plumbing Services</option>
                                    <option value="electrical">Electrical Services</option>
                                    <option value="carpentry">Carpentry</option>
                                    <option value="painting">Painting</option>
                                    <option value="hvac">HVAC Services</option>
                                    <option value="roofing">Roofing</option>
                                    <option value="general">General Repairs</option>
                                    <option value="all">All Services</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="linkUrl">Call-to-Action Link (Optional)</label>
                            <input type="url" id="linkUrl" placeholder="https://yourwebsite.com/offer">
                            <small>When users click your ad button, they’ll open this link.</small>
                        </div>

                        <div class="form-group">
                            <label for="linkText">Call-to-Action Button Text</label>
                            <input type="text" id="linkText" placeholder="e.g., Learn More, Get Quote, Book Now">
                            <small>Optional. If you provide a link but leave this empty, we’ll show “Learn More”.</small>
                        </div>
                    </div>

                    <!-- Step 4: Schedule -->
                    <div class="form-step" id="step4">
                        <h3 class="step-title">Step 4: Schedule Your Advertisement</h3>

                        <div class="schedule-dates" id="scheduleDates">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="startDate">Start Date *</label>
                                    <input type="date" id="startDate" required>
                                </div>
                                <div class="form-group">
                                    <label for="endDate">End Date *</label>
                                    <input type="date" id="endDate" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <small><strong>Campaign duration:</strong> <span id="scheduleDurationText">—</span></small>
                                <small><strong>Allowed duration for selected plan:</strong> <span id="minDurationText">—</span></small>
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Ad Review Process:</strong>
                                <p>Your advertisement is reviewed within 24–48 hours. Please choose a start date at least 2 days from today to allow review time.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Budget & Payment -->
                    <div class="form-step" id="step5">
                        <h3 class="step-title">Step 5: Budget & Payment</h3>

                        <div class="pricing-info">
                            <div class="price-card">
                                <div class="price-header">
                                    <i class="fas fa-tag"></i>
                                    <h4>Advertisement Pricing</h4>
                                </div>
                                <div class="price-breakdown">
                                    <div class="price-item">
                                        <span>Base Rate (per day)</span>
                                        <span class="price" id="baseRateDisplay">LKR 500</span>
                                    </div>
                                    <div class="price-item">
                                        <span>Priority Placement</span>
                                        <span class="price" id="priorityRateDisplay">+ LKR 200/day</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="priorityPlacement">
                                Priority Placement (+LKR 200/day)
                            </label>
                            <small id="priorityHelpText">Your ad will appear at the top of the page for maximum visibility</small>
                        </div>

                        <div class="cost-summary">
                            <div class="summary-row">
                                <span>Campaign Duration:</span>
                                <span id="summaryDuration">30 days</span>
                            </div>
                            <div class="summary-row">
                                <span>Base Cost:</span>
                                <span id="summaryBase">LKR 15,000</span>
                            </div>
                            <div class="summary-row" id="videoCostRow" style="display: none;">
                                <span>Video Premium:</span>
                                <span id="summaryVideo">LKR 9,000</span>
                            </div>
                            <div class="summary-row" id="priorityCostRow" style="display: none;">
                                <span>Priority Placement:</span>
                                <span id="summaryPriority">LKR 6,000</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total">
                                <span>Total Cost:</span>
                                <span id="summaryTotal">LKR 15,000</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" required>
                                <option value="">Loading your saved payment methods...</option>
                            </select>
                            <small>This list comes from your company profile payment methods.</small>
                            <div id="paymentMethodShortcut" style="display:none; margin-top: 10px;">
                                <a class="btn-secondary" id="paymentMethodShortcutLink" href="/2nd-Year-Group-Project/FixLanka/views/company/settings.php?tab=billing">
                                    <i class="fas fa-credit-card"></i> Add Payment Method
                                </a>
                                <small style="display:block; margin-top: 6px;">Opens Settings → Billing → Payment Methods.</small>
                            </div>
                        </div>

                        <div class="terms-checkbox">
                            <label>
                                <input type="checkbox" id="termsAgree" required>
                                I agree to the <a href="#" target="_blank">Advertising Terms & Conditions</a>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="modal-footer">
                        <div class="step-indicator">
                            <span class="step-dot active"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                            <span class="step-dot"></span>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-secondary" id="prevStep" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" class="btn-secondary" id="saveDraft">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="button" class="btn-primary" id="nextStep">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn-primary" id="submitAd" style="display: none;">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- link the sidebar.js -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/sidebar.js"></script>

    <script>
        // Modal functionality
        const createAdBtn = document.getElementById('createAdBtn');
        const adModal = document.getElementById('adModal');
        const closeModal = document.getElementById('closeModal');
        const adFormEl = document.getElementById('adForm');
        const modalTitleEl = document.getElementById('modalTitle');

        if (createAdBtn && adModal && closeModal) {
            createAdBtn.addEventListener('click', () => {
                if (adFormEl) {
                    delete adFormEl.dataset.editingAdId;
                    delete adFormEl.dataset.originalStartDate;
                    delete adFormEl.dataset.originalEndDate;
                    adFormEl.reset();
                }
                if (modalTitleEl) {
                    modalTitleEl.innerHTML = '<i class="fas fa-plus-circle"></i> Create New Advertisement';
                }
                const submitAdBtn = document.getElementById('submitAd');
                if (submitAdBtn) {
                    submitAdBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit for Approval';
                }
                if (typeof removeMedia !== 'undefined' && removeMedia) {
                    removeMedia.click();
                }
                currentStep = 1;
                showStep(currentStep);

                if (typeof initWizardDefaults === 'function') {
                    initWizardDefaults();
                }

                adModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });

            closeModal.addEventListener('click', () => {
                adModal.classList.remove('active');
                document.body.style.overflow = 'auto';
            });

            adModal.addEventListener('click', (e) => {
                if (e.target === adModal) {
                    adModal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        }

        // Multi-step form navigation
        let currentStep = 1;
        const totalSteps = 5;
        const formSteps = document.querySelectorAll('.form-step');
        const stepDots = document.querySelectorAll('.step-dot');
        const prevBtn = document.getElementById('prevStep');
        const nextBtn = document.getElementById('nextStep');
        const submitBtn = document.getElementById('submitAd');

        function showStep(step) {
            formSteps.forEach((s, i) => {
                s.classList.toggle('active', i === step - 1);
            });

            stepDots.forEach((dot, i) => {
                dot.classList.toggle('active', i < step);
            });

            prevBtn.style.display = step > 1 ? 'block' : 'none';
            nextBtn.style.display = step < totalSteps ? 'block' : 'none';
            submitBtn.style.display = step === totalSteps ? 'block' : 'none';
        }

        function clearAllFieldErrors() {
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
                if (el.getAttribute && el.getAttribute('aria-invalid') === 'true') {
                    el.removeAttribute('aria-invalid');
                }
            });
            document.querySelectorAll('.field-error').forEach(el => el.remove());
        }

        function getErrorContainer(element) {
            if (!element) return null;
            return element.closest?.('.form-group') || element.closest?.('.upload-section') || element.closest?.('.schedule-dates') || element.parentElement;
        }

        function setFieldError(element, message) {
            const container = getErrorContainer(element);
            if (!container) return;

            if (element && element.classList) {
                element.classList.add('is-invalid');
                if (element.setAttribute) {
                    element.setAttribute('aria-invalid', 'true');
                }
            }

            const existing = container.querySelector('.field-error');
            const msgEl = existing || document.createElement('div');
            msgEl.className = 'field-error';
            msgEl.setAttribute('role', 'alert');
            msgEl.textContent = message;
            if (!existing) {
                container.appendChild(msgEl);
            }
        }

        function getStepIndexFromElement(element) {
            const stepEl = element?.closest?.('.form-step');
            if (!stepEl || !stepEl.id) return null;
            const m = String(stepEl.id).match(/^step(\d+)$/);
            if (!m) return null;
            const n = parseInt(m[1], 10);
            return Number.isFinite(n) ? n : null;
        }

        function focusFirstError() {
            const first = document.querySelector('.is-invalid');
            if (!first) return null;

            const step = getStepIndexFromElement(first);
            if (step && step !== currentStep) {
                currentStep = step;
                showStep(currentStep);
            }

            first.scrollIntoView({ behavior: 'smooth', block: 'center' });

            try {
                if (typeof first.focus === 'function') {
                    first.focus({ preventScroll: true });
                } else {
                    first.setAttribute('tabindex', '-1');
                    first.focus({ preventScroll: true });
                }
            } catch (e) {
                // non-focusable element
            }

            return first;
        }

        function isValidHttpUrl(url) {
            if (!url) return false;
            try {
                const u = new URL(url);
                return u.protocol === 'http:' || u.protocol === 'https:';
            } catch {
                return false;
            }
        }

        function validateStep(step) {
            let ok = true;
            const editingAdId = parseInt((adFormEl && adFormEl.dataset.editingAdId) ? adFormEl.dataset.editingAdId : '', 10);
            const isEditing = Number.isFinite(editingAdId) && editingAdId > 0;
            const adType = document.querySelector('input[name="adType"]:checked')?.value || '';

            if (!adType || !['banner', 'featured', 'sponsored'].includes(adType)) {
                ok = false;
            }

            // Step 1 -> Step 2
            if (step === 1) {
                return ok;
            }

            // Step 2 -> Step 3 (media + basic info)
            if (step === 2) {
                const titleEl = document.getElementById('adTitle');
                const descEl = document.getElementById('adDescription');
                const title = (titleEl?.value ?? '').trim();
                const description = (descEl?.value ?? '').trim();

                if (!title) {
                    ok = false;
                    setFieldError(titleEl, 'Advertisement title is required.');
                } else if (title.length < 5) {
                    ok = false;
                    setFieldError(titleEl, 'Please enter a clearer title (at least 5 characters).');
                }

                if (!description) {
                    ok = false;
                    setFieldError(descEl, 'Description is required.');
                } else if (description.length < 20) {
                    ok = false;
                    setFieldError(descEl, 'Please enter a more detailed description (at least 20 characters).');
                }

                const file = (mediaFile && mediaFile.files && mediaFile.files[0]) ? mediaFile.files[0] : selectedMediaFile;
                const hasExistingPreview = !!(previewImage && previewImage.src);

                if (!file && !(isEditing && hasExistingPreview)) {
                    ok = false;
                    setFieldError(uploadArea, 'Please upload an image for your advertisement.');
                }

                if (file) {
                    if (!file.type || !file.type.startsWith('image/')) {
                        ok = false;
                        setFieldError(uploadArea, 'Please upload a valid image file (JPG, PNG, GIF).');
                    }
                    const maxBytes = 5 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        ok = false;
                        setFieldError(uploadArea, 'Image file is too large. Maximum allowed size is 5MB.');
                    }
                }
                return ok;
            }

            // Step 3 -> Step 4 (category + CTA)
            if (step === 3) {
                const categoryEl = document.getElementById('adCategory');
                const category = (categoryEl?.value ?? '').trim();
                if (!category) {
                    ok = false;
                    setFieldError(categoryEl, 'Please select a service category.');
                }

                const linkUrlEl = document.getElementById('linkUrl');
                const linkTextEl = document.getElementById('linkText');
                const linkUrl = (linkUrlEl?.value ?? '').trim();
                let linkText = (linkTextEl?.value ?? '').trim();

                if (adType === 'sponsored' && !linkUrl) {
                    ok = false;
                    setFieldError(linkUrlEl, 'Sponsored Ads require a Call-to-Action link (to drive leads).');
                }

                if (linkText && !linkUrl) {
                    ok = false;
                    setFieldError(linkUrlEl, 'Please add a Call-to-Action link or clear the button text.');
                }

                if (linkUrl) {
                    if (!isValidHttpUrl(linkUrl)) {
                        ok = false;
                        setFieldError(linkUrlEl, 'Please enter a valid Call-to-Action link (must start with http:// or https://).');
                    }
                    if (!linkText) {
                        linkText = 'Learn More';
                        if (linkTextEl) {
                            linkTextEl.value = linkText;
                        }
                    }
                }
                return ok;
            }

            // Step 4 -> Step 5 (schedule business rules)
            if (step === 4) {
                const startEl = document.getElementById('startDate');
                const endEl = document.getElementById('endDate');
                const startDate = (startEl?.value ?? '').trim();
                const endDate = (endEl?.value ?? '').trim();

                if (!startDate) {
                    ok = false;
                    setFieldError(startEl, 'Start date is required.');
                }
                if (!endDate) {
                    ok = false;
                    setFieldError(endEl, 'End date is required.');
                }

                if (!startDate || !endDate) {
                    return false;
                }

                const originalStart = (adFormEl && adFormEl.dataset.originalStartDate) ? adFormEl.dataset.originalStartDate : '';
                const originalEnd = (adFormEl && adFormEl.dataset.originalEndDate) ? adFormEl.dataset.originalEndDate : '';
                const datesUnchanged = isEditing && originalStart && originalEnd && originalStart === startDate && originalEnd === endDate;

                const minStart = ymd(addDays(new Date(), REVIEW_LEAD_DAYS));
                if (!isEditing && startDate < minStart) {
                    ok = false;
                    setFieldError(startEl, `Please choose a start date at least ${REVIEW_LEAD_DAYS} days from today (for review time).`);
                }
                if (isEditing && startDate < minStart && originalStart && originalStart !== startDate) {
                    ok = false;
                    setFieldError(startEl, `Start date must be at least ${REVIEW_LEAD_DAYS} days from today (for review time).`);
                }

                const duration = calculateDurationDays(startDate, endDate);
                if (!duration) {
                    ok = false;
                    setFieldError(endEl, 'End date must be the same as or after the start date.');
                }

                const minDays = (minDurationByType[adType] ?? 3);
                if (duration < minDays && !datesUnchanged) {
                    ok = false;
                    setFieldError(endEl, `The selected plan requires a minimum duration of ${minDays} days.`);
                }

                const maxDays = (maxDurationByType[adType] ?? 30);
                if (duration > maxDays && !datesUnchanged) {
                    ok = false;
                    setFieldError(endEl, `The selected plan allows a maximum duration of ${maxDays} days.`);
                }

                return ok;
            }
        }

        nextBtn.addEventListener('click', () => {
            if (currentStep < totalSteps) {
                clearAllFieldErrors();
                const ok = validateStep(currentStep);
                if (!ok) {
                    focusFirstError();
                    return;
                }

                currentStep++;
                showStep(currentStep);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // File upload
        const uploadArea = document.getElementById('uploadArea');
        const mediaFile = document.getElementById('mediaFile');
        const uploadPreview = document.getElementById('uploadPreview');
        const previewImage = document.getElementById('previewImage');
        const previewVideo = document.getElementById('previewVideo');
        const removeMedia = document.getElementById('removeMedia');

        let selectedMediaFile = null;

        function setFileInput(file) {
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                mediaFile.files = dt.files;
            } catch (e) {
                // If DataTransfer is not supported, drag-drop will still preview but may not submit.
            }
        }

        uploadArea.addEventListener('click', () => {
            mediaFile.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = 'var(--primary-color)';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#ddd';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#ddd';
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                setFileInput(files[0]);
                handleFileUpload(files[0]);
            }
        });

        mediaFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                selectedMediaFile = e.target.files[0];
                handleFileUpload(e.target.files[0]);
            }
        });

        function handleFileUpload(file) {
            selectedMediaFile = file;
            const reader = new FileReader();

            reader.onload = (e) => {
                uploadArea.style.display = 'none';
                uploadPreview.style.display = 'block';

                if (file.type.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewVideo.style.display = 'none';
                } else {
                    // Only images are supported for ads currently
                    previewImage.style.display = 'none';
                    previewVideo.style.display = 'none';
                }
            };

            reader.readAsDataURL(file);
        }

        removeMedia.addEventListener('click', () => {
            selectedMediaFile = null;
            mediaFile.value = '';
            uploadArea.style.display = 'flex';
            uploadPreview.style.display = 'none';
            previewImage.src = '';
            previewVideo.src = '';
        });

        // Type-specific journey hints
        function updateTypeJourneyHints() {
            const adType = document.querySelector('input[name="adType"]:checked')?.value || 'banner';
            const specs = document.getElementById('uploadSpecsText');
            if (!specs) return;

            if (adType === 'featured') {
                specs.innerHTML = '<strong>Featured:</strong> Recommended size 1200x628px, Max 5MB (high-visibility placement)';
            } else if (adType === 'sponsored') {
                specs.innerHTML = '<strong>Sponsored:</strong> Recommended size 1200x628px, Max 5MB (promoted listing style)';
            } else {
                specs.innerHTML = '<strong>Banner:</strong> Recommended size 1200x628px, Max 5MB';
            }
        }

        // Scheduling rules: always schedule, with a 2-day lead time
        const REVIEW_LEAD_DAYS = 2;
        const startDateEl = document.getElementById('startDate');
        const endDateEl = document.getElementById('endDate');
        const scheduleDurationTextEl = document.getElementById('scheduleDurationText');
        const minDurationTextEl = document.getElementById('minDurationText');

        const minDurationByType = {
            banner: 3,
            featured: 7,
            sponsored: 14
        };

        const maxDurationByType = {
            banner: 30,
            featured: 60,
            sponsored: 90
        };

        function updateDurationRulesText() {
            if (!minDurationTextEl) return;
            const adType = document.querySelector('input[name="adType"]:checked')?.value || 'banner';
            const minDays = (minDurationByType[adType] ?? 3);
            const maxDays = (maxDurationByType[adType] ?? 30);
            minDurationTextEl.textContent = `${minDays}–${maxDays} days`;
        }

        function ymd(d) {
            const pad = (n) => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        function addDays(date, days) {
            const d = new Date(date);
            d.setDate(d.getDate() + days);
            return d;
        }

        function calculateDurationDays(startYmd, endYmd) {
            if (!startYmd || !endYmd) return null;
            const start = new Date(startYmd + 'T00:00:00');
            const end = new Date(endYmd + 'T00:00:00');
            if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) return null;
            const diffMs = end.getTime() - start.getTime();
            if (diffMs < 0) return null;
            const diffDays = Math.floor(diffMs / (24 * 60 * 60 * 1000));
            return diffDays + 1; // inclusive
        }

        function setDefaultScheduleDatesIfEmpty() {
            if (!startDateEl || !endDateEl) return;

            const minStart = ymd(addDays(new Date(), REVIEW_LEAD_DAYS));
            startDateEl.min = minStart;

            if (!startDateEl.value) {
                startDateEl.value = minStart;
            }

            endDateEl.min = startDateEl.value;
            if (!endDateEl.value) {
                endDateEl.value = ymd(addDays(new Date(startDateEl.value + 'T00:00:00'), 6));
            }
        }

        function updateScheduleDurationText() {
            if (!scheduleDurationTextEl) return;
            const days = calculateDurationDays(startDateEl?.value || '', endDateEl?.value || '');
            scheduleDurationTextEl.textContent = days ? `${days} day(s)` : '—';
        }

        if (startDateEl && endDateEl) {
            startDateEl.addEventListener('change', () => {
                endDateEl.min = startDateEl.value;
                updateScheduleDurationText();
                updateCostSummary();
            });
            endDateEl.addEventListener('change', () => {
                updateScheduleDurationText();
                updateCostSummary();
            });
        }

        // Cost calculation
        function updateCostSummary() {
            const adType = document.querySelector('input[name="adType"]:checked')?.value || 'banner';
            const priority = document.getElementById('priorityPlacement').checked;
            const durationFromDates = calculateDurationDays(startDateEl?.value || '', endDateEl?.value || '');
            const duration = durationFromDates || 30;

            const typeRates = {
                banner: 500,
                featured: 800,
                sponsored: 1000
            };

            const baseRate = typeRates[adType] ?? 500;
            const priorityDaily = adType === 'sponsored' ? 0 : 200;
            const priorityRate = (priorityDaily > 0 && priority) ? priorityDaily : 0;

            let baseCost = baseRate * duration;
            let priorityCost = priorityRate * duration;
            let total = baseCost + priorityCost;

            const baseRateDisplay = document.getElementById('baseRateDisplay');
            if (baseRateDisplay) {
                baseRateDisplay.textContent = 'LKR ' + baseRate.toLocaleString();
            }

            const priorityRateDisplay = document.getElementById('priorityRateDisplay');
            if (priorityRateDisplay) {
                priorityRateDisplay.textContent = priorityDaily > 0
                    ? ('+ LKR ' + priorityDaily.toLocaleString() + '/day')
                    : 'Included';
            }

            document.getElementById('summaryDuration').textContent = duration + ' days';
            document.getElementById('summaryBase').textContent = 'LKR ' + baseCost.toLocaleString();
            document.getElementById('summaryPriority').textContent = 'LKR ' + priorityCost.toLocaleString();
            document.getElementById('summaryTotal').textContent = 'LKR ' + total.toLocaleString();

            document.getElementById('videoCostRow').style.display = 'none';
            document.getElementById('priorityCostRow').style.display = priorityCost > 0 ? 'flex' : 'none';
        }

        function updatePriorityControls() {
            const adType = document.querySelector('input[name="adType"]:checked')?.value || 'banner';
            const priorityEl = document.getElementById('priorityPlacement');
            const priorityHelp = document.getElementById('priorityHelpText');

            if (!priorityEl) return;

            if (adType === 'sponsored') {
                priorityEl.checked = true;
                priorityEl.disabled = true;
                if (priorityHelp) {
                    priorityHelp.textContent = 'Priority Placement is included for Sponsored ads.';
                }
            } else {
                priorityEl.disabled = false;
                if (priorityHelp) {
                    priorityHelp.textContent = 'Your ad will appear at the top of the page for maximum visibility';
                }
            }
        }

        document.querySelectorAll('input[name="adType"]').forEach(radio => {
            radio.addEventListener('change', () => {
                updateTypeJourneyHints();
                updatePriorityControls();
                updateDurationRulesText();
                updateCostSummary();
            });
        });

        document.getElementById('priorityPlacement').addEventListener('change', updateCostSummary);

        async function loadCompanyPaymentMethods() {
            const select = document.getElementById('paymentMethod');
            const shortcut = document.getElementById('paymentMethodShortcut');
            if (!select) return;

            const setShortcutVisible = (visible) => {
                if (!shortcut) return;
                shortcut.style.display = visible ? 'block' : 'none';
            };

            setShortcutVisible(false);

            select.disabled = true;
            select.innerHTML = '<option value="">Loading your saved payment methods...</option>';

            try {
                const resp = await fetch('../../api/settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_billing_data' })
                });

                const data = await resp.json();
                const methods = data?.data?.payment_methods;
                const list = Array.isArray(methods) ? methods : [];

                if (!resp.ok || !data?.success) {
                    throw new Error(data?.message || data?.error || 'Failed to load payment methods');
                }

                if (list.length === 0) {
                    select.innerHTML = '<option value="">No saved payment methods found</option>';
                    select.disabled = true;
                    setShortcutVisible(true);
                    return;
                }

                select.innerHTML = '<option value="">Select a saved payment method</option>';
                for (const m of list) {
                    const id = m.payment_method_id || m.id;
                    const cardType = (m.card_type || 'Card').toString();
                    const last4 = (m.last_four_digits || '****').toString();
                    const isPrimary = String(m.is_primary) === '1' || m.is_primary === 1;
                    const opt = document.createElement('option');
                    opt.value = String(id);
                    opt.textContent = `${cardType} •••• ${last4}${isPrimary ? ' (Primary)' : ''}`;
                    select.appendChild(opt);
                }

                select.disabled = false;
                setShortcutVisible(false);
            } catch (e) {
                console.error(e);
                select.innerHTML = '<option value="">Unable to load payment methods</option>';
                select.disabled = true;
                setShortcutVisible(true);
            }
        }

        function initWizardDefaults() {
            updateTypeJourneyHints();
            updatePriorityControls();
            setDefaultScheduleDatesIfEmpty();
            updateScheduleDurationText();
            updateDurationRulesText();
            updateCostSummary();
            loadCompanyPaymentMethods();
        }

        // Form submission
        document.getElementById('adForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitButton = document.getElementById('submitAd');
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            try {
                clearAllFieldErrors();

                const editingAdId = parseInt((adFormEl && adFormEl.dataset.editingAdId) ? adFormEl.dataset.editingAdId : '', 10);
                const isEditing = Number.isFinite(editingAdId) && editingAdId > 0;

                // Validate all steps (show inline warnings; jump to first invalid step)
                let ok = true;
                ok = validateStep(1) && ok;
                ok = validateStep(2) && ok;
                ok = validateStep(3) && ok;
                ok = validateStep(4) && ok;

                const paymentMethodEl = document.getElementById('paymentMethod');
                const paymentMethod = paymentMethodEl ? paymentMethodEl.value : '';
                if (!paymentMethod) {
                    ok = false;
                    setFieldError(paymentMethodEl, paymentMethodEl?.disabled
                        ? 'No saved payment methods found. Please add one in Settings.'
                        : 'Please select a saved payment method from your company profile.');
                }

                const termsAgree = document.getElementById('termsAgree');
                if (!isEditing && termsAgree && !termsAgree.checked) {
                    ok = false;
                    setFieldError(termsAgree, 'You must agree to the Advertising Terms & Conditions.');
                }

                if (!ok) {
                    focusFirstError();
                    return;
                }

                const adType = document.querySelector('input[name="adType"]:checked').value;
                const allowedTypes = ['banner', 'featured', 'sponsored'];
                if (!allowedTypes.includes(adType)) {
                    throw new Error('Invalid advertisement type selected.');
                }

                const title = document.getElementById('adTitle').value.trim();
                const description = document.getElementById('adDescription').value.trim();
                const category = document.getElementById('adCategory').value;

                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                const duration = calculateDurationDays(startDate, endDate);
                if (!duration) {
                    throw new Error('End date must be the same as or after the start date.');
                }

                const originalStart = (adFormEl && adFormEl.dataset.originalStartDate) ? adFormEl.dataset.originalStartDate : '';
                const originalEnd = (adFormEl && adFormEl.dataset.originalEndDate) ? adFormEl.dataset.originalEndDate : '';
                const datesUnchanged = isEditing && originalStart && originalEnd && originalStart === startDate && originalEnd === endDate;

                const minDays = (minDurationByType[adType] ?? 3);
                if (duration < minDays && !datesUnchanged) {
                    throw new Error(`The selected plan requires a minimum duration of ${minDays} days.`);
                }

                const maxDays = (maxDurationByType[adType] ?? 30);
                if (duration > maxDays && !datesUnchanged) {
                    throw new Error(`The selected plan allows a maximum duration of ${maxDays} days.`);
                }

                const linkUrl = document.getElementById('linkUrl').value.trim();
                let linkText = document.getElementById('linkText').value.trim();
                if (linkUrl && !linkText) {
                    linkText = 'Learn More';
                }

                const fd = new FormData();
                fd.append('type', adType);
                fd.append('title', title);
                fd.append('description', description);
                fd.append('category', category);
                fd.append('target_url', linkUrl);
                fd.append('link_text', linkText);
                fd.append('schedule_type', 'scheduled');
                if (startDate) fd.append('start_date', startDate);
                if (endDate) fd.append('end_date', endDate);
                fd.append('duration', String(duration));
                if (document.getElementById('priorityPlacement').checked) {
                    fd.append('priority_placement', '1');
                }
                fd.append('payment_method', paymentMethod);

                if (isEditing) {
                    fd.append('action', 'update');
                    fd.append('ad_id', String(editingAdId));
                }

                const mediaToUpload = (mediaFile.files && mediaFile.files[0]) ? mediaFile.files[0] : selectedMediaFile;
                if (mediaToUpload) {
                    fd.append('media_file', mediaToUpload);
                }

                const response = await fetch('../../api/advertisements.php', {
                    method: 'POST',
                    body: fd
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Failed to submit advertisement');
                }

                alert(isEditing
                    ? 'Advertisement updated successfully! It will remain pending until reviewed.'
                    : 'Advertisement submitted successfully! It will be reviewed by our moderation team within 24-48 hours.'
                );

                adModal.classList.remove('active');
                document.body.style.overflow = 'auto';
                currentStep = 1;
                showStep(currentStep);
                document.getElementById('adForm').reset();
                removeMedia.click();

                if (adFormEl) {
                    delete adFormEl.dataset.editingAdId;
                }
                if (modalTitleEl) {
                    modalTitleEl.innerHTML = '<i class="fas fa-plus-circle"></i> Create New Advertisement';
                }
                const submitAdBtnAfter = document.getElementById('submitAd');
                if (submitAdBtnAfter) {
                    submitAdBtnAfter.innerHTML = '<i class="fas fa-paper-plane"></i> Submit for Approval';
                }

                if (window.advertisementFilters && typeof window.advertisementFilters.loadAdvertisements === 'function') {
                    window.advertisementFilters.loadAdvertisements();
                }
            } catch (err) {
                console.error(err);
                alert(err.message || 'Failed to submit advertisement');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });

        // Allow other scripts to navigate wizard steps when editing.
        window.__adWizardSetStep = (step) => {
            const s = parseInt(step, 10);
            if (!Number.isFinite(s) || s < 1 || s > totalSteps) return;
            currentStep = s;
            showStep(currentStep);
        };

        // Save as draft
        document.getElementById('saveDraft').addEventListener('click', () => {
            alert('Advertisement saved as draft successfully!');
        });

        // Highlight active sidebar link
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof initWizardDefaults === 'function') {
                initWizardDefaults();
            }

            const navLinks = document.querySelectorAll('.sidebar .nav-link');

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                const navItem = link.parentElement;

                // Remove active class from all items
                navItem.classList.remove('active');

                // Add active class to current page (check for advertisements.php in href)
                if (href && href.includes('/views/company/advertisements.php')) {
                    navItem.classList.add('active');
                }
            });
        });
    </script>

    <!-- Advertisement Management JavaScript -->
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/company/advertisements-filters.js"></script>
</body>

</html>


