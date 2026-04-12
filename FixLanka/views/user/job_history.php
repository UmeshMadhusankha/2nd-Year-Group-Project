<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\job_history.php

// This file is loaded by JobRequestController->index()
// $jobRequests variable is already set by the controller

if (!isset($jobRequests)) {
    $jobRequests = [];
}

if (!isset($directJobRequests)) {
    $directJobRequests = [];
}

$allJobRequests = [];

foreach ($jobRequests as $job) {
    $job['request_type'] = 'regular';
    $job['posted_date'] = $job['dateCreated'] ?? ($job['date_created'] ?? null);
    $allJobRequests[] = $job;
}

foreach ($directJobRequests as $job) {
    $job['request_type'] = 'direct';
    $job['service_provider_type'] = $job['service_provider_type'] ?? ($job['provider_type'] ?? 'individual');
    $job['posted_date'] = $job['date_created'] ?? ($job['dateCreated'] ?? null);
    $allJobRequests[] = $job;
}

usort($allJobRequests, function ($a, $b) {
    $dateA = strtotime($a['posted_date'] ?? '1970-01-01 00:00:00');
    $dateB = strtotime($b['posted_date'] ?? '1970-01-01 00:00:00');
    return $dateB <=> $dateA;
});

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Count jobs by status
$pendingCount = 0;
$inProgressCount = 0;
$completedCount = 0;
$cancelledCount = 0;

foreach ($allJobRequests as $job) {
    switch ($job['status']) {
        case 'pending':
            $pendingCount++;
            break;
        case 'in_progress':
            $inProgressCount++;
            break;
        case 'completed':
            $completedCount++;
            break;
        case 'cancelled':
            $cancelledCount++;
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job History - Fix Lanka</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/navbar.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/job-history.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <main class="main-content">
        <div class="page-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">My Job Requests</h1>
                    <p class="page-subtitle">Track and manage all your job requests ordered by date</p>
                </div>
                <a href="/2nd-Year-Group-Project/FixLanka/views/user/landing.php" class="btn-home">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Filter Tabs -->
            <div class="filter-controls">
                <div class="history-controls-left">
                    <div class="history-view-toggle">
                        <button class="history-view-btn active" id="jobsPostedBtn" type="button">Jobs Posted</button>
                        <button class="history-view-btn" id="quotesReceivedBtn" type="button">
                            Quotes Received
                            <span class="quotes-pill" id="quotesReceivedPill" style="display:none;">0</span>
                        </button>
                    </div>

                    <div class="filter-tabs" id="jobsFilterTabs">
                        <button class="filter-tab active" data-status="all">
                            All Jobs <span class="tab-count"><?php echo count($allJobRequests); ?></span>
                        </button>
                        <button class="filter-tab" data-status="pending">
                            Pending <span class="tab-count"><?php echo $pendingCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="in_progress">
                            In Progress <span class="tab-count"><?php echo $inProgressCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="completed">
                            Completed <span class="tab-count"><?php echo $completedCount; ?></span>
                        </button>
                        <button class="filter-tab" data-status="cancelled">
                            Cancelled <span class="tab-count"><?php echo $cancelledCount; ?></span>
                        </button>
                    </div>
                </div>
                <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="btn-primary">
                    <i class="fas fa-plus"></i> Post New Job
                </a>
            </div>

            <!-- Jobs Container -->
            <div class="jobs-container">
                <?php if (empty($allJobRequests)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h2 class="empty-title">No Job Requests Yet</h2>
                        <p class="empty-description">You haven't posted any standard or direct job requests yet. Start by posting your first job!</p>
                        <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="post-job-btn">
                            <i class="fas fa-plus"></i> Post Your First Job
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($allJobRequests as $job): ?>
                        <?php
                        $statusClass = strtolower($job['status']);
                        $isDirectRequest = ($job['request_type'] ?? 'regular') === 'direct';
                        $isPending = $job['status'] === 'pending' && !$isDirectRequest;
                        $statusLabel = ucfirst(str_replace('_', ' ', $job['status']));
                        $providerType = str_replace(',', ', ', (string)($job['service_provider_type'] ?? 'individual'));
                        $providerType = ucwords(str_replace('_', ' ', $providerType));
                        $postedDate = $job['posted_date'] ?? null;
                        $title = $job['title'] ?? ($job['category_name'] ?? 'Job Request');
                        ?>
                        <div class="job-card <?php echo $isDirectRequest ? 'direct-job-card' : ''; ?>" data-status="<?php echo $job['status']; ?>" data-request-type="<?php echo $job['request_type']; ?>">
                            <div class="job-card-header">
                                <div>
                                    <h3 class="job-title"><?php echo htmlspecialchars($title); ?></h3>
                                    <p class="job-date">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Posted on <?php echo $postedDate ? date('F j, Y \a\t g:i A', strtotime($postedDate)) : 'N/A'; ?>
                                    </p>
                                </div>
                                <div class="job-badges">
                                    <?php if ($isDirectRequest): ?>
                                        <span class="job-badge badge-direct-request">
                                            <i class="fas fa-location-arrow"></i> Direct Request
                                        </span>
                                        <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                    <?php else: ?>
                                        <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                        <span class="job-badge badge-urgency urgency-<?php echo $job['urgency']; ?>">
                                            <i class="fas fa-bolt"></i> <?php echo ucfirst($job['urgency']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>

                            <div class="job-details-grid">
                                <div class="job-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="detail-label">Address</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($job['address']); ?></span>
                                    </div>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-user-tie"></i>
                                    <div>
                                        <span class="detail-label">Provider Type</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($providerType); ?></span>
                                    </div>
                                </div>
                                <div class="job-detail">
                                    <i class="fas fa-tag"></i>
                                    <div>
                                        <span class="detail-label">Category</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($job['category_name']); ?></span>
                                    </div>
                                </div>
                                <?php if ($job['photos']): ?>
                                <div class="job-detail">
                                    <i class="fas fa-image"></i>
                                    <div>
                                        <span class="detail-label">Photo</span>
                                        <a href="/2nd-Year-Group-Project/FixLanka/<?php echo htmlspecialchars($job['photos']); ?>" 
                                           target="_blank" class="detail-value">View Photo</a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="job-actions">
                                <?php if ($isPending): ?>
                                    <!-- Edit and Delete buttons only for pending jobs -->
                                    <button onclick="openEditModal(<?php echo $job['request_id']; ?>)" class="action-btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="/2nd-Year-Group-Project/FixLanka/delete-job" method="POST" style="display:inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this job request?');">
                                        <input type="hidden" name="request_id" value="<?php echo $job['request_id']; ?>">
                                        <button type="submit" class="action-btn btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                <?php elseif ($isDirectRequest): ?>
                                    <span class="read-only-badge direct-read-only-badge">
                                        <i class="fas fa-paper-plane"></i> Direct job request submitted
                                    </span>
                                <?php else: ?>
                                    <!-- Read-only indicator for non-pending jobs -->
                                    <span class="read-only-badge">
                                        <i class="fas fa-lock"></i> Read Only
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="quotes-received-section" id="quotesReceivedSection" style="display: none;">
                <div class="quotes-received-header">
                    <h2 class="quotes-received-title">Received Quotes For Your Jobs</h2>
                    <p class="quotes-received-subtitle">Quotes from individual repairers and companies for jobs you published.</p>
                </div>
                <div class="quotes-filter-tabs" id="quotesFilterTabs">
                    <button class="quotes-filter-tab active" type="button" data-quote-status="pending">Pending</button>
                    <button class="quotes-filter-tab" type="button" data-quote-status="accepted">Accepted</button>
                    <button class="quotes-filter-tab" type="button" data-quote-status="rejected">Rejected</button>
                </div>
                <div class="quotes-received-list" id="quotesReceivedList">
                    <div class="quote-empty-state">Loading quotes...</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Edit Job Request</h2>
                <button class="modal-close" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <form id="editForm" action="/2nd-Year-Group-Project/FixLanka/update-job" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_request_id" name="request_id">
                    
                    <div class="form-group">
                        <label for="edit_title">Job Title <span class="required">*</span></label>
                        <input type="text" id="edit_title" name="title" required placeholder="e.g., Kitchen Sink Repair, AC Installation">
                    </div>

                    <div class="form-group">
                        <label for="edit_category_id">Category <span class="required">*</span></label>
                        <select id="edit_category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <option value="1">Plumbing</option>
                            <option value="2">Electrical</option>
                            <option value="3">HVAC</option>
                            <option value="4">Cleaning</option>
                            <option value="5">Carpentry</option>
                            <option value="6">Painting</option>
                            <option value="7">Appliance Repair</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description">Description <span class="required">*</span></label>
                        <textarea id="edit_description" name="description" rows="5" required placeholder="Describe your job in detail..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_district">District <span class="required">*</span></label>
                        <select id="edit_district" name="district" required>
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
                        <label for="edit_address">Address <span class="required">*</span></label>
                        <input type="text" id="edit_address" name="address" required placeholder="Enter your full address (street, area)">
                    </div>

                    <div class="form-group">
                        <label>Service Provider Type <span class="required">*</span></label>
                        <div class="checkbox-group">
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_provider_individual" name="provider_type[]" value="individual">
                                <label for="edit_provider_individual" class="checkbox-label">
                                    <i class="fas fa-user"></i>
                                    Individual Repairer
                                </label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" id="edit_provider_company" name="provider_type[]" value="company">
                                <label for="edit_provider_company" class="checkbox-label">
                                    <i class="fas fa-building"></i>
                                    Company
                                </label>
                            </div>
                        </div>
                        <small class="hint-text">Select at least one service provider type</small>
                        <span class="error-message" id="edit-provider-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_urgency">Urgency Level <span class="required">*</span></label>
                        <select id="edit_urgency" name="urgency" required>
                            <option value="medium">Medium</option>
                            <option value="urgent">Urgent <span class="pro-badge">PRO</span></option>
                        </select>
                        <div class="urgency-info">
                            <i class="fas fa-info-circle"></i>
                            <span>Urgent priority is a PRO feature - Your job will be highlighted to service providers</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_finish_date">Expected Finish Date <span class="required">*</span></label>
                        <input type="date" id="edit_finish_date" name="finish_date" required>
                        <small class="hint-text">When do you need this work completed?</small>
                    </div>

                    <div class="form-group">
                        <label for="edit_photos">Photos (Optional)</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="edit_photos" name="photos" accept="image/*" onchange="updateEditFileName(this)">
                            <label for="edit_photos" class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                <span class="upload-text">Choose File</span>
                            </label>
                        </div>
                        <small class="hint-text">Upload a new photo to replace the existing one</small>
                        <div id="edit-file-name-display" class="file-preview"></div>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="action-btn btn-secondary" onclick="closeEditModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="action-btn btn-primary">
                            <i class="fas fa-save"></i> Update Job
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    const jobs = <?php echo json_encode($jobRequests); ?>;
    
    // File upload preview function
    function updateEditFileName(input) {
        const fileDisplay = document.getElementById('edit-file-name-display');
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            fileDisplay.innerHTML = `<div class="file-preview-item">${fileName}</div>`;
        } else {
            fileDisplay.innerHTML = '';
        }
    }

    // Set minimum date to today for finish date
    document.addEventListener('DOMContentLoaded', function() {
        const finishDateInput = document.getElementById('edit_finish_date');
        if (finishDateInput) {
            const today = new Date().toISOString().split('T')[0];
            finishDateInput.setAttribute('min', today);
        }

        // Validate provider type checkboxes on form submit
        const editForm = document.getElementById('editForm');
        const providerCheckboxes = document.querySelectorAll('#editForm input[name="provider_type[]"]');
        const providerError = document.getElementById('edit-provider-error');

        editForm.addEventListener('submit', function(e) {
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
    
    // Filter jobs by status
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            const status = this.dataset.status;
            
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Filter job cards
            document.querySelectorAll('.job-card').forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    const USER_QUOTES_API = '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php';
    const jobsPostedBtn = document.getElementById('jobsPostedBtn');
    const quotesReceivedBtn = document.getElementById('quotesReceivedBtn');
    const jobsFilterTabs = document.getElementById('jobsFilterTabs');
    const jobsContainerEl = document.querySelector('.jobs-container');
    const quotesReceivedSection = document.getElementById('quotesReceivedSection');
    const quotesReceivedList = document.getElementById('quotesReceivedList');
    const quotesReceivedPill = document.getElementById('quotesReceivedPill');
    const quotesFilterTabs = document.querySelectorAll('.quotes-filter-tab');
    const JOB_HISTORY_VIEW_KEY = 'jobHistory.activeView';
    let currentQuoteStatusFilter = 'pending';

    function persistMainView(view) {
        try {
            sessionStorage.setItem(JOB_HISTORY_VIEW_KEY, view);
        } catch (e) {
            // Ignore storage failures (private mode/quota/security settings)
        }

        const url = new URL(window.location.href);
        if (view === 'quotes') {
            url.searchParams.set('view', 'quotes');
        } else {
            url.searchParams.delete('view');
        }
        window.history.replaceState({}, '', url.toString());
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function formatMoney(value) {
        const n = Number(value);
        if (!Number.isFinite(n)) return 'LKR 0';
        return `LKR ${n.toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
    }

    function formatDateTime(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    function formatDateOnly(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleDateString(undefined, {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function readableStatus(status) {
        return String(status || 'pending').replaceAll('_', ' ');
    }

    async function fetchQuotesJson(url, options) {
        const res = await fetch(url, { credentials: 'same-origin', ...(options || {}) });
        const text = await res.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (e) {
            throw new Error('Invalid JSON');
        }

        if (!res.ok || data?.success === false) {
            throw new Error(data?.message || `Request failed (${res.status})`);
        }

        return data;
    }

    function setQuotesPill(count) {
        if (!quotesReceivedPill) return;
        const c = Number.isFinite(count) ? count : 0;
        if (c > 0) {
            quotesReceivedPill.textContent = String(c);
            quotesReceivedPill.style.display = 'inline-flex';
        } else {
            quotesReceivedPill.style.display = 'none';
        }
    }

    function renderQuoteCard(q) {
        const providerName = q.provider_name || (q.source === 'company' ? 'Company' : 'Repairer');
        const providerTypeLabel = q.provider_type || (q.source === 'company' ? 'Company' : 'Individual');
        const jobTitle = q.job_title || 'Job';
        const categoryName = q.category_name || 'General';
        const quoteAmount = formatMoney(q.amount);
        const canRespond = q.status === 'pending';
        const quoteStatus = readableStatus(q.status);

        const repairerExtras = q.source === 'repairer'
            ? `
                <div class="quote-meta-item"><strong>Estimated Days:</strong> ${escapeHtml(q.estimated_days ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Warranty (days):</strong> ${escapeHtml(q.warranty_period ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Valid Until:</strong> ${escapeHtml(formatDateOnly(q.valid_until))}</div>
                <div class="quote-meta-item"><strong>Materials Included:</strong> ${q.materials_included == 1 ? 'Yes' : 'No'}</div>
            `
            : '';

        const companyExtras = q.source === 'company'
            ? `
                <div class="quote-meta-item"><strong>Labor:</strong> ${escapeHtml(formatMoney(q.labor_cost))}</div>
                <div class="quote-meta-item"><strong>Materials:</strong> ${escapeHtml(formatMoney(q.material_cost))}</div>
                <div class="quote-meta-item"><strong>Transport:</strong> ${escapeHtml(formatMoney(q.transport_cost))}</div>
                <div class="quote-meta-item"><strong>Other:</strong> ${escapeHtml(formatMoney(q.other_charges))}</div>
                <div class="quote-meta-item"><strong>Start Date:</strong> ${escapeHtml(formatDateOnly(q.company_start_date))}</div>
                <div class="quote-meta-item"><strong>Completion Date:</strong> ${escapeHtml(formatDateOnly(q.company_completion_date))}</div>
            `
            : '';

        const messageBlock = q.quote_message
            ? `<p class="quote-message">${escapeHtml(q.quote_message)}</p>`
            : '';

        const providerId = Number(q.provider_id);
        const canNegotiate = Number.isFinite(providerId) && providerId > 0;
        const negotiateUrl = `/2nd-Year-Group-Project/FixLanka/chat?source=${encodeURIComponent(q.source || '')}&provider_id=${encodeURIComponent(String(providerId || ''))}&request_id=${encodeURIComponent(String(q.request_id || ''))}&quote_id=${encodeURIComponent(String(q.quote_id || ''))}`;

        return `
            <article class="quote-item" data-source="${escapeHtml(q.source)}" data-quote-id="${escapeHtml(q.quote_id)}">
                <div class="quote-card-top">
                    <div>
                        <h3 class="quote-job-title">${escapeHtml(jobTitle)}</h3>
                        <p class="quote-job-meta">Posted: ${escapeHtml(formatDateTime(q.job_posted_at))} | Category: ${escapeHtml(categoryName)}</p>
                    </div>
                    <span class="quote-status-pill quote-status-${escapeHtml(String(q.status || 'pending').toLowerCase())}">${escapeHtml(quoteStatus)}</span>
                </div>

                <div class="quote-provider">
                    <img src="${escapeHtml(q.provider_avatar || 'https://via.placeholder.com/48')}" alt="Provider" class="provider-avatar">
                    <div class="provider-info">
                        <span class="provider-name">${escapeHtml(providerName)}</span>
                        <span class="provider-type">${escapeHtml(providerTypeLabel)}</span>
                    </div>
                    <div class="quote-price">${escapeHtml(quoteAmount)}</div>
                </div>

                <div class="quote-meta-grid">
                    <div class="quote-meta-item"><strong>Quote Sent:</strong> ${escapeHtml(formatDateTime(q.created_at))}</div>
                    <div class="quote-meta-item"><strong>Job Status:</strong> ${escapeHtml(readableStatus(q.job_status))}</div>
                    <div class="quote-meta-item"><strong>Requested Provider Type:</strong> ${escapeHtml(q.job_provider_preference || 'N/A')}</div>
                    ${repairerExtras}
                    ${companyExtras}
                </div>

                ${messageBlock}

                <div class="quote-actions">
                    <button class="btn-success-sm" ${canRespond ? '' : 'disabled'} onclick="handleQuoteAction('accepted','${escapeHtml(q.source)}',${escapeHtml(q.quote_id)})">Accept</button>
                    <button class="btn-outline-sm" ${canRespond ? '' : 'disabled'} onclick="handleQuoteAction('rejected','${escapeHtml(q.source)}',${escapeHtml(q.quote_id)})">Reject</button>
                    <a class="btn-negotiate-sm ${canNegotiate ? '' : 'is-disabled'}" ${canNegotiate ? `href="${negotiateUrl}"` : 'href="#" aria-disabled="true" onclick="return false;"'}>
                        <i class="fas fa-message"></i> Negotiate
                    </a>
                </div>
            </article>
        `;
    }

    async function loadQuotesReceived() {
        if (!quotesReceivedList) return;

        quotesReceivedList.innerHTML = `
            <div class="quote-empty-state">Loading quotes...</div>
        `;

        try {
            const data = await fetchQuotesJson(`${USER_QUOTES_API}?action=list&limit=50&offset=0&status=${encodeURIComponent(currentQuoteStatusFilter)}`);
            const quotes = Array.isArray(data.quotes) ? data.quotes : [];
            setQuotesPill(parseInt(data.pending_count, 10) || 0);

            if (quotes.length === 0) {
                quotesReceivedList.innerHTML = `
                    <div class="quote-empty-state">No ${escapeHtml(currentQuoteStatusFilter)} quotes found for your jobs</div>
                `;
                return;
            }

            quotesReceivedList.innerHTML = quotes.map(renderQuoteCard).join('');
        } catch (e) {
            quotesReceivedList.innerHTML = `
                <div class="quote-empty-state">Failed to load quotes</div>
            `;
            setQuotesPill(0);
        }
    }

    function switchMainView(view) {
        const showJobs = view === 'jobs';

        jobsPostedBtn.classList.toggle('active', showJobs);
        quotesReceivedBtn.classList.toggle('active', !showJobs);

        if (jobsFilterTabs) jobsFilterTabs.style.display = showJobs ? 'flex' : 'none';
        if (jobsContainerEl) jobsContainerEl.style.display = showJobs ? '' : 'none';
        if (quotesReceivedSection) quotesReceivedSection.style.display = showJobs ? 'none' : 'block';

        if (!showJobs) {
            loadQuotesReceived();
        }

        persistMainView(showJobs ? 'jobs' : 'quotes');
    }

    if (jobsPostedBtn && quotesReceivedBtn) {
        jobsPostedBtn.addEventListener('click', function() {
            switchMainView('jobs');
        });

        quotesReceivedBtn.addEventListener('click', function() {
            switchMainView('quotes');
        });
    }

    const initialViewFromUrl = new URLSearchParams(window.location.search).get('view');
    let initialView = initialViewFromUrl;

    if (!initialView) {
        try {
            initialView = sessionStorage.getItem(JOB_HISTORY_VIEW_KEY) || 'jobs';
        } catch (e) {
            initialView = 'jobs';
        }
    }

    if (initialView === 'quotes') {
        switchMainView('quotes');
    } else {
        switchMainView('jobs');
    }

    if (quotesFilterTabs && quotesFilterTabs.length) {
        quotesFilterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const nextStatus = this.dataset.quoteStatus;
                if (!nextStatus || nextStatus === currentQuoteStatusFilter) return;

                currentQuoteStatusFilter = nextStatus;
                quotesFilterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                if (quotesReceivedSection && quotesReceivedSection.style.display !== 'none') {
                    loadQuotesReceived();
                }
            });
        });
    }

    window.handleQuoteAction = async function(decision, source, quoteId) {
        try {
            if (decision === 'rejected') {
                const confirmed = window.confirm('Are you sure you want to reject this quote?');
                if (!confirmed) {
                    return;
                }
            }

            await fetchQuotesJson(`${USER_QUOTES_API}?action=respond`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ source, quote_id: Number(quoteId), decision })
            });
            await loadQuotesReceived();
        } catch (e) {
            alert('Failed to update quote');
        }
    };
    
    // Open edit modal with populated data
    function openEditModal(requestId) {
        const job = jobs.find(j => j.request_id == requestId);
        if (job) {
            // Populate basic fields
            document.getElementById('edit_request_id').value = job.request_id;
            document.getElementById('edit_title').value = job.title || '';
            document.getElementById('edit_category_id').value = job.category_id;
            document.getElementById('edit_description').value = job.description;
            document.getElementById('edit_district').value = job.district || '';
            document.getElementById('edit_address').value = job.address || '';
            document.getElementById('edit_urgency').value = job.urgency;
            document.getElementById('edit_finish_date').value = job.finish_date || '';
            
            // Handle service provider type checkboxes
            const providerType = job.service_provider_type || '';
            document.getElementById('edit_provider_individual').checked = providerType.includes('individual');
            document.getElementById('edit_provider_company').checked = providerType.includes('company');
            
            // Clear file preview
            document.getElementById('edit-file-name-display').innerHTML = '';
            
            // Show modal
            document.getElementById('editModal').classList.add('show');
        }
    }
    
    // Close edit modal
    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }
    
    // Close modal on outside click
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/job-history.js"></script>
</body>
</html>