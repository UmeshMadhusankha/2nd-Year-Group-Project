<?php
// filepath: c:\xampp\htdocs\2nd-Year-Group-Project\FixLanka\views\user\job_history.php

// This file is loaded by JobRequestController->index()
// $jobRequests variable is already set by the controller

if (!isset($jobRequests)) {
    $jobRequests = [];
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);

// Count jobs by status
$pendingCount = 0;
$inProgressCount = 0;
$completedCount = 0;
$cancelledCount = 0;

foreach ($jobRequests as $job) {
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/common.css">
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/common/modals.css">
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
                <div class="filter-tabs">
                    <button class="filter-tab active" data-status="all">
                        All Jobs <span class="tab-count"><?php echo count($jobRequests); ?></span>
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
                <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="btn-primary">
                    <i class="fas fa-plus"></i> Post New Job
                </a>
            </div>

            <!-- Jobs Container -->
            <div class="jobs-container">
                <?php if (empty($jobRequests)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <h2 class="empty-title">No Job Requests Yet</h2>
                        <p class="empty-description">You haven't posted any job requests. Start by posting your first job!</p>
                        <a href="/2nd-Year-Group-Project/FixLanka/post-job" class="post-job-btn">
                            <i class="fas fa-plus"></i> Post Your First Job
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($jobRequests as $job): ?>
                        <?php
                        $statusClass = strtolower($job['status']);
                        $isPending = $job['status'] === 'pending';
                        $statusLabel = ucfirst(str_replace('_', ' ', $job['status']));
                        ?>
                        <div class="job-card" data-status="<?php echo $job['status']; ?>">
                            <div class="job-card-header">
                                <div>
                                    <h3 class="job-title"><?php echo htmlspecialchars($job['category_name'] ?? 'Job Request'); ?></h3>
                                    <p class="job-date">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Posted on <?php echo date('F j, Y \a\t g:i A', strtotime($job['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="job-badges">
                                    <span class="job-badge badge-status status-<?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                    <span class="job-badge badge-urgency urgency-<?php echo $job['urgency']; ?>">
                                        <i class="fas fa-bolt"></i> <?php echo ucfirst($job['urgency']); ?>
                                    </span>
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
                                        <span class="detail-value"><?php echo ucfirst($job['service_provider_type']); ?></span>
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
                                    <form id="deleteForm_<?php echo $job['request_id']; ?>" action="/2nd-Year-Group-Project/FixLanka/delete-job" method="POST" style="display:inline;">
                                        <input type="hidden" name="request_id" value="<?php echo $job['request_id']; ?>">
                                        <button type="button" class="action-btn btn-delete" 
                                                onclick="handleDeleteJob(<?php echo $job['request_id']; ?>)">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                    
                                    <button onclick="viewQuotes(<?php echo $job['request_id']; ?>)" class="action-btn btn-view-quotes" style="background-color: #17a2b8; color: white;">
                                        <i class="fas fa-file-invoice-dollar"></i> View Quotes
                                    </button>
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

    <!-- Quotes List Modal -->
    <div id="quotesModal" class="modal-overlay">
        <div class="modal-container" style="max-width:680px;width:90%;">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-file-invoice-dollar" style="color:#0abab5;margin-right:8px"></i>Received Quotations</h2>
                <button class="modal-close" onclick="closeQuotesModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-content" style="padding:0;">
                <div id="quotesList" style="max-height:70vh;overflow-y:auto;padding:16px;"></div>
            </div>
        </div>
    </div>

    <!-- Quote Detail Modal (2nd level) -->
    <div id="quoteDetailModal" class="modal-overlay" style="z-index:1200;">
        <div class="modal-container" style="max-width:600px;width:90%;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0abab5,#059090);">
                <h2 class="modal-title" style="color:#fff;"><i class="fas fa-receipt" style="margin-right:8px"></i>Quote Details</h2>
                <button class="modal-close" onclick="closeQuoteDetail()" style="color:#fff;"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-content">
                <div id="quoteDetailBody"></div>
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
    // ─── View Quotes ────────────────────────────────────────────────────────
    // Cache for quotes per requestId so we can open detail without re-fetching
    const _quotesCache = {};

    function viewQuotes(requestId) {
        const modal = document.getElementById('quotesModal');
        const list  = document.getElementById('quotesList');
        if (!modal || !list) return;

        list.innerHTML = '<div style="text-align:center;padding:30px;color:#6b7280"><i class="fas fa-spinner fa-spin fa-2x"></i><p style="margin-top:12px">Loading quotations...</p></div>';
        modal.classList.add('show');

        fetch(`/2nd-Year-Group-Project/FixLanka/api/user-quotes.php?action=list&request_id=${requestId}&limit=50`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    list.innerHTML = `<div style="text-align:center;padding:30px;color:#ef4444"><i class="fas fa-exclamation-circle"></i> ${escapeQH(data.message || 'Failed to load quotations.')}</div>`;
                    return;
                }
                const quotes = data.quotes || [];
                _quotesCache[requestId] = quotes;

                if (!quotes.length) {
                    list.innerHTML = '<div style="text-align:center;padding:40px;color:#6b7280"><i class="fas fa-file-invoice-dollar fa-2x"></i><p style="margin-top:14px">No quotations received yet for this job.</p></div>';
                    return;
                }

                list.innerHTML = `
                    <p style="margin:0 0 12px;color:#6b7280;font-size:13px">${quotes.length} quotation${quotes.length !== 1 ? 's' : ''} received — click any row to view full details</p>
                    ${quotes.map((q, i) => renderQuoteListRow(q, i, requestId)).join('')}`;
            })
            .catch(() => {
                list.innerHTML = '<div style="text-align:center;padding:30px;color:#ef4444"><i class="fas fa-exclamation-circle"></i> Network error. Please try again.</div>';
            });
    }

    // Compact list row — clicking opens detail popup
    function renderQuoteListRow(q, index, requestId) {
        const colors  = { pending:'#f59e0b', accepted:'#22c55e', rejected:'#ef4444' };
        const color   = colors[q.status] || '#6b7280';
        const label   = q.status ? q.status.charAt(0).toUpperCase() + q.status.slice(1) : '—';
        const amount  = parseFloat(q.amount || 0).toLocaleString();
        const rating  = parseFloat(q.provider_rating || 0).toFixed(1);
        const initial = (q.provider_name || 'R')[0].toUpperCase();
        const days    = q.estimated_days ? `${q.estimated_days}d` : '—';

        return `
        <div onclick="openQuoteDetail(${requestId}, ${index})"
             style="display:flex;align-items:center;gap:14px;padding:14px 16px;margin-bottom:10px;border:1px solid #e5e7eb;
                    border-radius:12px;background:#fff;cursor:pointer;transition:box-shadow .15s,border-color .15s;
                    box-shadow:0 1px 3px rgba(0,0,0,.05)"
             onmouseover="this.style.borderColor='#0abab5';this.style.boxShadow='0 4px 12px rgba(10,186,181,.15)'"
             onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 1px 3px rgba(0,0,0,.05)'">

            <!-- Avatar -->
            <div style="flex-shrink:0;width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#0abab5,#059090);
                        display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:17px">
                ${initial}
            </div>

            <!-- Name + type + rating -->
            <div style="flex:1;min-width:0">
                <div style="font-weight:700;font-size:15px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeQH(q.provider_name || 'Unknown')}</div>
                <div style="font-size:12px;color:#6b7280;margin-top:2px">${escapeQH(q.provider_type || 'Individual')} &nbsp;·&nbsp; <i class="fas fa-clock"></i> ${days} &nbsp;·&nbsp; <i class="fas fa-star" style="color:#f59e0b"></i> ${rating}</div>
            </div>

            <!-- Amount + status -->
            <div style="text-align:right;flex-shrink:0">
                <div style="font-size:17px;font-weight:800;color:#0abab5">LKR ${amount}${q.labor_unit_label || q.material_unit_label ? ' <span style="font-size:11px;font-weight:normal">(per unit)</span>' : ''}</div>
                <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;
                             background:${color}18;color:${color};border:1px solid ${color}">${label}</span>
            </div>

            <!-- Chevron -->
            <div style="flex-shrink:0;color:#9ca3af;font-size:13px"><i class="fas fa-chevron-right"></i></div>
        </div>`;
    }

    // Detail popup
    function openQuoteDetail(requestId, index) {
        const q = (_quotesCache[requestId] || [])[index];
        if (!q) return;
        const body = document.getElementById('quoteDetailBody');
        if (!body) return;

        const colors  = { pending:'#f59e0b', accepted:'#22c55e', rejected:'#ef4444' };
        const color   = colors[q.status] || '#6b7280';
        const label   = q.status ? q.status.charAt(0).toUpperCase() + q.status.slice(1) : '—';
        const amount  = parseFloat(q.amount || 0).toLocaleString();
        const rating  = parseFloat(q.provider_rating || 0).toFixed(1);
        const days    = q.estimated_days ? `${q.estimated_days} day${q.estimated_days != 1 ? 's' : ''}` : '—';
        const warranty= q.warranty_period ? `${q.warranty_period} months` : 'None';
        const mats    = q.materials_included == 1 ? '<span style="color:#22c55e"><i class="fas fa-check-circle"></i> Included</span>' : '<span style="color:#ef4444"><i class="fas fa-times-circle"></i> Not Included</span>';
        const validUntil = q.valid_until ? new Date(q.valid_until).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'}) : '—';
        const submittedOn = q.created_at ? new Date(q.created_at).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'}) : '—';
        const initial = (q.provider_name || 'R')[0].toUpperCase();
        const isPending = q.status === 'pending';

        // Detail grid construction
        let detailCells = [
            detailCell('fas fa-tools','Materials','', mats),
            detailCell('fas fa-shield-alt','Valid For (Warranty)', warranty),
            detailCell('fas fa-calendar-alt','Submitted On', submittedOn),
            detailCell('fas fa-tag','Job Title', escapeQH(q.job_title || '—'))
        ];

        // Add company-specific detailed fields if available
        if (q.source === 'company') {
            const formatCurr = (val) => val ? 'LKR ' + parseFloat(val).toLocaleString() : '—';
            
            detailCells.push(
                detailCell('fas fa-user-hard-hat', q.labor_unit_label ? `Labor Cost (${q.labor_unit_label})` : 'Labor Cost', formatCurr(q.labor_cost)),
                detailCell('fas fa-box-open', q.material_unit_label ? `Material Cost (${q.material_unit_label})` : 'Material Cost', formatCurr(q.material_cost)),
                detailCell('fas fa-truck', 'Transport Cost', formatCurr(q.transport_cost)),
                detailCell('fas fa-plus-circle', 'Other Charges', formatCurr(q.other_charges)),
                detailCell('fas fa-money-check-alt', 'Pricing Type', escapeQH(q.pricing_type ? q.pricing_type.replace(/_/g, ' ') : '—').replace(/\b\w/g, l => l.toUpperCase())),
                detailCell('fas fa-wallet', 'Payment Method', escapeQH(q.payment_method ? q.payment_method.replace(/_/g, ' ') : '—').replace(/\b\w/g, l => l.toUpperCase())),
                detailCell('fas fa-file-contract', 'Payment Terms', escapeQH(q.payment_terms || '—')),
                detailCell('fas fa-calendar-week', 'Work Schedule', escapeQH(q.work_schedule_type ? q.work_schedule_type.replace(/_/g, ' ') : '—').replace(/\b\w/g, l => l.toUpperCase()))
            );
        }

        const detailGridHtml = detailCells.join('');

        body.innerHTML = `
        <!-- Provider header -->
        <div style="display:flex;align-items:center;gap:14px;padding:20px;background:#f9fafb;border-bottom:1px solid #e5e7eb">
            <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#0abab5,#059090);
                        display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;font-weight:700;flex-shrink:0">${initial}</div>
            <div style="flex:1">
                <div style="font-size:18px;font-weight:700;color:#111827">${escapeQH(q.provider_name || 'Unknown Repairer')}</div>
                <div style="font-size:13px;color:#6b7280;margin-top:2px">
                    <i class="fas fa-user-tag"></i> ${escapeQH(q.provider_type || 'Individual')}
                    &nbsp;&nbsp;<i class="fas fa-star" style="color:#f59e0b"></i> ${rating} rating
                </div>
            </div>
            <div style="text-align:right">
                <div style="font-size:24px;font-weight:800;color:#0abab5">LKR ${amount}${q.labor_unit_label || q.material_unit_label ? ' <span style="font-size:14px;font-weight:normal">(per unit)</span>' : ''}</div>
                <span style="font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;
                             background:${color}18;color:${color};border:1px solid ${color}">${label}</span>
            </div>
        </div>

        <!-- Detail grid -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">
            ${detailGridHtml}
        </div>

        <!-- Message -->
        ${q.message ? '<div style="margin:0;padding:16px 20px;border-top:1px solid #e5e7eb"><div style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:8px">Message from Provider</div><div style="background:#f0fdfc;border-left:3px solid #0abab5;border-radius:0 8px 8px 0;padding:12px 14px;font-size:14px;color:#374151;line-height:1.6"><i class=\'fas fa-comment-dots\' style=\'color:#0abab5;margin-right:6px\'></i>' + escapeQH(q.message) + '</div></div>' : ''}

        <!-- Actions -->
        <div style="padding:16px 20px;border-top:1px solid #e5e7eb;display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeQuoteDetail()"
                    style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;border-radius:8px;padding:9px 18px;cursor:pointer;font-weight:600">
                <i class="fas fa-arrow-left"></i> Back to List
            </button>
            ${isPending ? '<button onclick="acceptQuote(\'' + q.source + '\', ' + q.quote_id + ', ' + requestId + ')" style="background:#22c55e;color:#fff;border:none;border-radius:8px;padding:9px 20px;cursor:pointer;font-weight:700;"><i class=\'fas fa-check\'></i> Accept This Quote</button>' : ''}
        </div>`;

        document.getElementById('quoteDetailModal').classList.add('show');
    }

    function detailCell(icon, label, value, rawHtml) {
        return `<div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;border-right:1px solid #f3f4f6">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#9ca3af;margin-bottom:4px"><i class="${icon}" style="margin-right:4px"></i>${label}</div>
            <div style="font-size:15px;font-weight:600;color:#111827">${rawHtml || escapeQH(value)}</div>
        </div>`;
    }

    function escapeQH(str) {
        if (!str) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    async function acceptQuote(source, quoteId, requestId) {
        const confirmed = await window.showConfirm(
            'Accept this quote? All other quotes for this job will be rejected and the job will be assigned to this provider.',
            { title: 'Accept Quotation', confirmText: 'Accept Quote' }
        );
        
        if (!confirmed) return;

        try {
            const res = await fetch('/2nd-Year-Group-Project/FixLanka/api/user-quotes.php?action=respond', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ source, quote_id: quoteId, decision: 'accepted' })
            });
            const data = await res.json();

            if (data.success) {
                closeQuoteDetail();
                closeQuotesModal();
                await window.showAlert('Quote accepted! The job has been assigned to the provider.', 'success', 'Success');
                location.reload();
            } else {
                await window.showAlert('Error: ' + (data.message || 'Failed to accept quote.'), 'danger', 'Error');
            }
        } catch (err) {
            await window.showAlert('Network error. Please try again.', 'danger', 'Error');
        }
    }

    async function handleDeleteJob(requestId) {
        const confirmed = await window.showConfirm(
            'Are you sure you want to delete this job request? This action cannot be undone.',
            { title: 'Delete Job Request', confirmText: 'Delete', type: 'danger' }
        );
        if (confirmed) {
            document.getElementById('deleteForm_' + requestId).submit();
        }
    }

    function closeQuoteDetail() {
        document.getElementById('quoteDetailModal').classList.remove('show');
    }

    function closeQuotesModal() {
        closeQuoteDetail();
        document.getElementById('quotesModal').classList.remove('show');
    }

    document.getElementById('quotesModal').addEventListener('click', function(e) {
        if (e.target === this) closeQuotesModal();
    });
    document.getElementById('quoteDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeQuoteDetail();
    });

    </script>


    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/common/common.js"></script>
    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/job-history.js"></script>
</body>
</html>