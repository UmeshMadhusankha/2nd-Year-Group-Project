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
    $normalizedStatus = strtolower(trim((string)($job['status'] ?? '')));
    switch ($normalizedStatus) {
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
    <link rel="stylesheet" href="/2nd-Year-Group-Project/FixLanka/assets/css/user/direct-job-request-popup.css">
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
                        $statusValue = strtolower(trim((string)($job['status'] ?? '')));
                        $statusClass = $statusValue !== '' ? $statusValue : 'unknown';
                        $isDirectRequest = ($job['request_type'] ?? 'regular') === 'direct';
                        $isPendingRegular = $statusValue === 'pending' && !$isDirectRequest;
                        $isPendingDirect = $statusValue === 'pending' && $isDirectRequest;
                        $statusLabel = ucfirst(str_replace('_', ' ', $statusClass));
                        $providerType = str_replace(',', ', ', (string)($job['service_provider_type'] ?? 'individual'));
                        $providerType = ucwords(str_replace('_', ' ', $providerType));
                        $postedDate = $job['posted_date'] ?? null;
                        $title = $job['title'] ?? ($job['category_name'] ?? 'Job Request');
                        $providerName = trim((string)($job['provider_name'] ?? ''));
                        $providerTypeKey = strtolower((string)($job['service_provider_type'] ?? 'individual'));
                        if ($providerTypeKey === 'company') {
                            $providerTypeLabel = 'company';
                        } else {
                            $providerTypeLabel = 'repairer';
                        }
                        ?>
                            <div class="job-card <?php echo $isDirectRequest ? 'direct-job-card' : ''; ?>"
                                data-status="<?php echo htmlspecialchars($statusClass); ?>"
                                data-request-type="<?php echo htmlspecialchars($job['request_type']); ?>"
                                data-request-id="<?php echo (int)$job['request_id']; ?>"
                                data-provider-id="<?php echo (int)($job['provider_id'] ?? 0); ?>"
                                data-provider-type="<?php echo htmlspecialchars($job['provider_type'] ?? ($job['service_provider_type'] ?? '')); ?>"
                                data-category-id="<?php echo (int)($job['category_id'] ?? 0); ?>">
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

                            <?php if ($isDirectRequest): ?>
                                <p class="direct-job-note" style="color: red; font-weight: 600; font-size: var(--font-size-lg);">
                                    This is a direct job request made to <?php echo htmlspecialchars($providerName !== '' ? $providerName : 'the selected provider'); ?>.
                                </p>
                            <?php endif; ?>

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
                                <button type="button"
                                        class="action-btn btn-view-quotes"
                                        onclick="openJobQuotesModal(<?php echo (int)$job['request_id']; ?>, '<?php echo $isDirectRequest ? 'direct' : 'regular'; ?>')">
                                    <i class="fas fa-file-invoice-dollar"></i> View Received Quotations
                                </button>

                                <?php if ($isPendingRegular): ?>
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
                                <?php elseif ($isPendingDirect): ?>
                                    <button type="button" class="action-btn btn-edit" onclick="openDirectEditModal(<?php echo (int)$job['request_id']; ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="action-btn btn-delete" onclick="deleteDirectRequest(<?php echo (int)$job['request_id']; ?>)">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
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

    <div id="jobQuotesModal" class="modal-overlay">
        <div class="modal-container quotes-request-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Received Quotations</h2>
                <button class="modal-close" type="button" onclick="closeJobQuotesModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content">
                <div class="quotes-request-list" id="jobQuotesList">
                    <div class="quote-empty-state">Loading quotes...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="quoteDetailsModal" class="modal-overlay">
        <div class="modal-container quote-details-modal-container">
            <div class="modal-header">
                <h2 class="modal-title">Quotation Details</h2>
                <button class="modal-close" type="button" onclick="closeQuoteDetailsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-content" id="quoteDetailsContent">
                <div class="quote-empty-state">Select a quote to view details.</div>
            </div>
            <div class="modal-actions quote-details-actions">
                <button type="button" class="action-btn btn-secondary" onclick="closeQuoteDetailsModal()">
                    <i class="fas fa-times"></i> Close
                </button>
                <button type="button" class="action-btn btn-reject-sm" id="quoteRejectBtn">
                    <i class="fas fa-xmark"></i> Reject
                </button>
                <a href="#" class="action-btn btn-negotiate-sm" id="quoteNegotiateBtn">
                    <i class="fas fa-message"></i> Negotiate
                </a>
                <button type="button" class="action-btn btn-success-sm" id="quoteAcceptBtn">
                    <i class="fas fa-check"></i> Accept
                </button>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/direct-job-request-popup.php'; ?>

    <script>
    const regularJobs = <?php echo json_encode($jobRequests); ?>;
    const directJobs = <?php echo json_encode($directJobRequests); ?>;
    const jobs = regularJobs;
    const directJobsById = new Map((Array.isArray(directJobs) ? directJobs : []).map((job) => [Number(job.request_id), job]));

    const USER_QUOTES_API = '/2nd-Year-Group-Project/FixLanka/api/user-quotes.php';
    const DIRECT_JOB_API = '/2nd-Year-Group-Project/FixLanka/api/user/direct-job-requests.php';

    const jobsPostedBtn = document.getElementById('jobsPostedBtn');
    const quotesReceivedBtn = document.getElementById('quotesReceivedBtn');
    const jobsFilterTabs = document.getElementById('jobsFilterTabs');
    const jobsContainerEl = document.querySelector('.jobs-container');
    const quotesReceivedSection = document.getElementById('quotesReceivedSection');
    const quotesReceivedList = document.getElementById('quotesReceivedList');
    const quotesReceivedPill = document.getElementById('quotesReceivedPill');
    const quotesFilterTabs = document.querySelectorAll('.quotes-filter-tab');
    const jobQuotesModal = document.getElementById('jobQuotesModal');
    const jobQuotesList = document.getElementById('jobQuotesList');
    const jobQuotesSubtitle = document.getElementById('jobQuotesSubtitle');
    const quoteDetailsModal = document.getElementById('quoteDetailsModal');
    const quoteDetailsContent = document.getElementById('quoteDetailsContent');
    const quoteAcceptBtn = document.getElementById('quoteAcceptBtn');
    const quoteRejectBtn = document.getElementById('quoteRejectBtn');
    const quoteNegotiateBtn = document.getElementById('quoteNegotiateBtn');
    const JOB_HISTORY_VIEW_KEY = 'jobHistory.activeView';

    const directJobRequestModal = document.getElementById('directJobRequestModal');
    const directJobRequestCloseBtn = document.getElementById('directJobRequestCloseBtn');
    const directJobRequestCancelBtn = document.getElementById('directJobRequestCancelBtn');
    const directJobRequestForm = document.getElementById('directJobRequestForm');
    const directJobRequestError = document.getElementById('directJobRequestError');
    const directJobRequestSuccess = document.getElementById('directJobRequestSuccess');
    const directJobRequestProviderLabel = document.getElementById('directJobRequestProviderLabel');
    const directJobProviderId = document.getElementById('directJobProviderId');
    const directJobProviderType = document.getElementById('directJobProviderType');
    const directJobCategory = document.getElementById('directJobCategory');
    const directJobCategoryDisplay = document.getElementById('directJobCategoryDisplay');
    const directJobCategoryList = document.getElementById('directJobCategoryList');
    const directJobAddress = document.getElementById('directJobAddress');
    const directJobDistrict = document.getElementById('directJobDistrict');
    const directJobFinishDate = document.getElementById('directJobFinishDate');
    const directJobPhotos = document.getElementById('directJobPhotos');
    const directJobPhotoPreview = document.getElementById('directJobPhotoPreview');
    const directJobRequestSubmitBtn = document.getElementById('directJobRequestSubmitBtn');

    let currentQuoteStatusFilter = 'pending';
    let currentRequestQuotesContext = { requestId: null, requestType: null };
    let currentlyOpenedQuote = null;
    let directEditRequestId = null;

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

    function formatDateOnly(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatDateTime(value) {
        if (!value) return 'N/A';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return 'N/A';
        return d.toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
    }

    function readableStatus(status) {
        return String(status || 'pending').replaceAll('_', ' ');
    }

    function normalizeProviderType(providerType) {
        const normalized = String(providerType || '').toLowerCase().trim();
        return normalized === 'company' ? 'company' : 'individual';
    }

    async function fetchJson(url, options) {
        const response = await fetch(url, { credentials: 'same-origin', ...(options || {}) });
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (error) {
            console.error('[QuotesAPI] Invalid JSON response', {
                url,
                status: response.status,
                preview: String(text || '').slice(0, 500)
            });
            throw new Error('Invalid JSON response');
        }

        if (!response.ok || result?.success === false) {
            console.error('[QuotesAPI] Request failed', {
                url,
                status: response.status,
                result
            });
            throw new Error(result?.message || `Request failed (${response.status})`);
        }

        return result;
    }

    function updateEditFileName(input) {
        const fileDisplay = document.getElementById('edit-file-name-display');
        if (!fileDisplay) return;
        if (input.files && input.files[0]) {
            fileDisplay.innerHTML = `<div class="file-preview-item">${escapeHtml(input.files[0].name)}</div>`;
        } else {
            fileDisplay.innerHTML = '';
        }
    }

    function setQuotesPill(count) {
        if (!quotesReceivedPill) return;
        if (count > 0) {
            quotesReceivedPill.textContent = String(count);
            quotesReceivedPill.style.display = 'inline-flex';
        } else {
            quotesReceivedPill.style.display = 'none';
        }
    }

    function formatStatusLabel(status) {
        return readableStatus(status).replace(/\b\w/g, (char) => char.toUpperCase());
    }

    function showActionToast(message) {
        let toast = document.getElementById('jobHistoryActionToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'jobHistoryActionToast';
            toast.className = 'job-history-toast';
            document.body.appendChild(toast);
        }

        toast.textContent = String(message || 'Action completed successfully.');
        toast.classList.add('show');

        window.clearTimeout(window.__jobHistoryToastTimer);
        window.__jobHistoryToastTimer = window.setTimeout(() => {
            toast.classList.remove('show');
        }, 2200);
    }

    function refreshJobTabCounts() {
        const cards = Array.from(document.querySelectorAll('.job-card'));
        const counts = {
            all: cards.length,
            pending: 0,
            in_progress: 0,
            completed: 0,
            cancelled: 0,
        };

        cards.forEach((card) => {
            const status = String(card.dataset.status || '').toLowerCase();
            if (Object.prototype.hasOwnProperty.call(counts, status)) {
                counts[status] += 1;
            }
        });

        document.querySelectorAll('.filter-tab').forEach((tab) => {
            const status = String(tab.dataset.status || 'all');
            const countNode = tab.querySelector('.tab-count');
            if (!countNode || !Object.prototype.hasOwnProperty.call(counts, status)) return;
            countNode.textContent = String(counts[status]);
        });
    }

    function applyActiveJobFilter() {
        const activeTab = document.querySelector('.filter-tab.active');
        const activeStatus = activeTab ? String(activeTab.dataset.status || 'all') : 'all';

        document.querySelectorAll('.job-card').forEach((card) => {
            card.style.display = activeStatus === 'all' || card.dataset.status === activeStatus ? 'block' : 'none';
        });
    }

    function updateJobCardStatus(requestId, requestType, nextStatus) {
        const numericRequestId = Number(requestId);
        const normalizedRequestType = String(requestType || 'regular').toLowerCase() === 'direct' ? 'direct' : 'regular';
        const normalizedStatus = String(nextStatus || '').toLowerCase().trim();
        if (!Number.isFinite(numericRequestId) || numericRequestId <= 0 || normalizedStatus === '') return;

        const card = document.querySelector(`.job-card[data-request-id="${numericRequestId}"][data-request-type="${normalizedRequestType}"]`);
        if (!card) return;

        card.dataset.status = normalizedStatus;

        const statusBadge = card.querySelector('.badge-status');
        if (statusBadge) {
            Array.from(statusBadge.classList).forEach((className) => {
                if (className.startsWith('status-')) {
                    statusBadge.classList.remove(className);
                }
            });
            statusBadge.classList.add(`status-${normalizedStatus}`);
            statusBadge.textContent = formatStatusLabel(normalizedStatus);
        }

        if (normalizedStatus !== 'pending') {
            const actionsContainer = card.querySelector('.job-actions');
            const viewQuotesBtn = actionsContainer ? actionsContainer.querySelector('.btn-view-quotes') : null;
            if (actionsContainer && viewQuotesBtn) {
                const viewQuotesHtml = viewQuotesBtn.outerHTML;
                const readOnlyHtml = normalizedRequestType === 'direct'
                    ? '<span class="read-only-badge direct-read-only-badge"><i class="fas fa-lock"></i> In Progress</span>'
                    : '<span class="read-only-badge"><i class="fas fa-lock"></i> Read Only</span>';
                actionsContainer.innerHTML = `${viewQuotesHtml}${readOnlyHtml}`;
            }
        }

        refreshJobTabCounts();
        applyActiveJobFilter();
    }

    function quoteDetailsHtml(quote) {
        const companyFields = quote.source === 'company'
            ? `
                <div class="quote-meta-item"><strong>Labor:</strong> ${escapeHtml(formatMoney(quote.labor_cost))}</div>
                <div class="quote-meta-item"><strong>Materials:</strong> ${escapeHtml(formatMoney(quote.material_cost))}</div>
                <div class="quote-meta-item"><strong>Transport:</strong> ${escapeHtml(formatMoney(quote.transport_cost))}</div>
                <div class="quote-meta-item"><strong>Other Charges:</strong> ${escapeHtml(formatMoney(quote.other_charges))}</div>
                <div class="quote-meta-item"><strong>Start Date:</strong> ${escapeHtml(formatDateOnly(quote.company_start_date))}</div>
                <div class="quote-meta-item"><strong>Completion Date:</strong> ${escapeHtml(formatDateOnly(quote.company_completion_date))}</div>
                <div class="quote-meta-item"><strong>Payment Method:</strong> ${escapeHtml(quote.payment_method || 'N/A')}</div>
            `
            : `
                <div class="quote-meta-item"><strong>Estimated Days:</strong> ${escapeHtml(quote.estimated_days ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Warranty:</strong> ${escapeHtml(quote.warranty_period ?? 'N/A')}</div>
                <div class="quote-meta-item"><strong>Valid Until:</strong> ${escapeHtml(formatDateOnly(quote.valid_until))}</div>
                <div class="quote-meta-item"><strong>Materials Included:</strong> ${quote.materials_included == 1 ? 'Yes' : 'No'}</div>
            `;

        return `
            <div class="quote-details-head">
                <h3 class="quote-job-title">${escapeHtml(quote.job_title || 'Quotation')}</h3>
                <span class="quote-status-pill quote-status-${escapeHtml(String(quote.status || 'pending').toLowerCase())}">${escapeHtml(readableStatus(quote.status))}</span>
            </div>
            <div class="quote-provider">
                <img src="${escapeHtml(quote.provider_avatar || 'https://via.placeholder.com/48')}" alt="Provider" class="provider-avatar">
                <div class="provider-info">
                    <span class="provider-name">${escapeHtml(quote.provider_name || (quote.source === 'company' ? 'Company' : 'Repairer'))}</span>
                    <span class="provider-type">${escapeHtml(quote.provider_type || (quote.source === 'company' ? 'Company' : 'Individual'))}</span>
                </div>
                <div class="quote-price">${escapeHtml(formatMoney(quote.amount))}</div>
            </div>
            <div class="quote-meta-grid">
                <div class="quote-meta-item"><strong>Request Type:</strong> ${escapeHtml(quote.request_type || 'regular')}</div>
                <div class="quote-meta-item"><strong>Category:</strong> ${escapeHtml(quote.category_name || 'N/A')}</div>
                <div class="quote-meta-item"><strong>Quote Sent:</strong> ${escapeHtml(formatDateTime(quote.created_at))}</div>
                <div class="quote-meta-item"><strong>Job Posted:</strong> ${escapeHtml(formatDateTime(quote.job_posted_at))}</div>
                ${companyFields}
            </div>
            <p class="quote-message">${escapeHtml(quote.quote_message || quote.message || 'No additional notes.')}</p>
        `;
    }

    function renderQuoteCard(quote, variant) {
        const compact = variant === 'request';
        const quotePayload = JSON.stringify({ source: quote.source, quote_id: quote.quote_id, request_type: quote.request_type });
        const extraInfo = compact
            ? `<p class="quote-job-meta">${escapeHtml(quote.provider_type || quote.source)} | ${escapeHtml(formatMoney(quote.amount))}</p>`
            : `<p class="quote-job-meta">${escapeHtml(quote.category_name || 'N/A')} | Sent ${escapeHtml(formatDateTime(quote.created_at))}</p>`;

        return `
            <article class="quote-item quote-item-${compact ? 'compact' : 'full'}" role="button" tabindex="0" onclick='openQuoteDetails(${quotePayload})' onkeydown='if(event.key === "Enter" || event.key === " "){event.preventDefault();openQuoteDetails(${quotePayload});}'>
                <div class="quote-card-top">
                    <div>
                        <h3 class="quote-job-title">${escapeHtml(quote.provider_name || (quote.source === 'company' ? 'Company' : 'Repairer'))}</h3>
                        ${extraInfo}
                    </div>
                    <span class="quote-status-pill quote-status-${escapeHtml(String(quote.status || 'pending').toLowerCase())}">${escapeHtml(readableStatus(quote.status))}</span>
                </div>
                <div class="quote-meta-grid ${compact ? 'quote-meta-grid-compact' : ''}">
                    <div class="quote-meta-item"><strong>Service Provider Type:</strong> ${escapeHtml(quote.provider_type || 'N/A')}</div>
                    <div class="quote-meta-item"><strong>Total:</strong> ${escapeHtml(formatMoney(quote.amount))}</div>
                    <div class="quote-meta-item"><strong>Request ID:</strong> #${escapeHtml(quote.request_id)}</div>
                    <div class="quote-meta-item"><strong>Quote ID:</strong> #${escapeHtml(quote.quote_id)}</div>
                </div>
                <div class="quote-actions quote-actions-inline">
                    <button type="button" class="action-btn btn-view-quotes" onclick='event.stopPropagation();openQuoteDetails(${quotePayload})'>
                        <i class="fas fa-eye"></i> View Full Details
                    </button>
                </div>
            </article>
        `;
    }

    async function loadQuotesForRequest(requestId, requestType) {
        const normalizedRequestType = String(requestType || '').toLowerCase() === 'direct' ? 'direct' : 'regular';
        const url = `${USER_QUOTES_API}?action=list&limit=50&offset=0&request_id=${encodeURIComponent(String(requestId))}&request_type=${encodeURIComponent(normalizedRequestType)}`;
        const result = await fetchJson(url);
        return Array.isArray(result.quotes) ? result.quotes : [];
    }

    async function loadQuotesReceived() {
        if (!quotesReceivedList) return;
        quotesReceivedList.innerHTML = '<div class="quote-empty-state">Loading quotes...</div>';

        try {
            const result = await fetchJson(`${USER_QUOTES_API}?action=list&limit=100&offset=0&status=${encodeURIComponent(currentQuoteStatusFilter)}`);
            const quotes = Array.isArray(result.quotes) ? result.quotes : [];
            setQuotesPill(parseInt(result.pending_count, 10) || 0);

            if (!quotes.length) {
                quotesReceivedList.innerHTML = '<div class="quote-empty-state">No quotes received.</div>';
                return;
            }

            quotesReceivedList.innerHTML = quotes.map((quote) => renderQuoteCard(quote, 'section')).join('');
            window.__lastQuotes = quotes;
        } catch (error) {
            console.error('[QuotesReceived] Failed to load list', {
                statusFilter: currentQuoteStatusFilter,
                error: error && error.message ? error.message : error
            });
            quotesReceivedList.innerHTML = '<div class="quote-empty-state">Failed to load quotes.</div>';
            setQuotesPill(0);
        }
    }

    async function openJobQuotesModal(requestId, requestType) {
        if (!jobQuotesModal || !jobQuotesList) return;

        const normalizedRequestType = String(requestType || '').toLowerCase() === 'direct' ? 'direct' : 'regular';
        currentRequestQuotesContext = { requestId: Number(requestId), requestType: normalizedRequestType };
        jobQuotesList.innerHTML = '<div class="quote-empty-state">Loading quotes...</div>';

        jobQuotesModal.classList.add('show');
        document.body.style.overflow = 'hidden';

        try {
            const quotes = await loadQuotesForRequest(requestId, normalizedRequestType);
            if (!quotes.length) {
                jobQuotesList.innerHTML = '<div class="quote-empty-state">No quotes received</div>';
                return;
            }

            jobQuotesList.innerHTML = quotes.map((quote) => renderQuoteCard(quote, 'request')).join('');
            window.__lastRequestQuotes = quotes;
        } catch (error) {
            console.error('[JobQuotesModal] Failed to load request quotes', {
                requestId,
                requestType: normalizedRequestType,
                error: error && error.message ? error.message : error
            });
            const message = error && error.message ? error.message : 'Failed to load request quotations.';
            jobQuotesList.innerHTML = `<div class="quote-empty-state">${escapeHtml(message)}</div>`;
        }
    }

    function closeJobQuotesModal() {
        if (!jobQuotesModal) return;
        jobQuotesModal.classList.remove('show');
        document.body.style.overflow = '';
    }

    function findQuote(payload) {
        const allQuotes = [
            ...(Array.isArray(window.__lastQuotes) ? window.__lastQuotes : []),
            ...(Array.isArray(window.__lastRequestQuotes) ? window.__lastRequestQuotes : [])
        ];

        return allQuotes.find((quote) =>
            String(quote.source) === String(payload.source) &&
            Number(quote.quote_id) === Number(payload.quote_id) &&
            String(quote.request_type || 'regular') === String(payload.request_type || 'regular')
        ) || null;
    }

    function setQuoteActionButtons(quote) {
        if (!quoteAcceptBtn || !quoteRejectBtn || !quoteNegotiateBtn) return;

        const canRespond = String(quote.status || '').toLowerCase() === 'pending';
        quoteAcceptBtn.disabled = !canRespond;
        quoteRejectBtn.disabled = !canRespond;

        const providerId = Number(quote.provider_id);
        const canNegotiate = Number.isFinite(providerId) && providerId > 0;
        if (canNegotiate) {
            quoteNegotiateBtn.classList.remove('is-disabled');
            quoteNegotiateBtn.href = `/2nd-Year-Group-Project/FixLanka/chat?source=${encodeURIComponent(quote.source)}&provider_id=${encodeURIComponent(String(providerId))}&request_id=${encodeURIComponent(String(quote.request_id))}&quote_id=${encodeURIComponent(String(quote.quote_id))}`;
            quoteNegotiateBtn.onclick = null;
        } else {
            quoteNegotiateBtn.classList.add('is-disabled');
            quoteNegotiateBtn.href = '#';
            quoteNegotiateBtn.onclick = function() { return false; };
        }
    }

    function openQuoteDetails(payload) {
        const quote = findQuote(payload);
        if (!quote || !quoteDetailsModal || !quoteDetailsContent) {
            alert('Unable to open quote details.');
            return;
        }

        currentlyOpenedQuote = quote;
        quoteDetailsContent.innerHTML = quoteDetailsHtml(quote);
        setQuoteActionButtons(quote);

        quoteDetailsModal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeQuoteDetailsModal() {
        if (!quoteDetailsModal) return;
        quoteDetailsModal.classList.remove('show');
        document.body.style.overflow = '';
        currentlyOpenedQuote = null;
    }

    async function handleQuoteAction(decision, source, quoteId, requestType) {
        const actionLabel = decision === 'accepted' ? 'accept' : 'reject';
        if (!confirm(`Are you sure you want to ${actionLabel} this quotation?`)) {
            return;
        }

        await fetchJson(`${USER_QUOTES_API}?action=respond`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ source, quote_id: Number(quoteId), decision, request_type: requestType || null })
        });

        if (decision === 'accepted' && currentlyOpenedQuote) {
            updateJobCardStatus(currentlyOpenedQuote.request_id, currentlyOpenedQuote.request_type, 'in_progress');
        }

        showActionToast(decision === 'accepted' ? 'Quotation accepted successfully.' : 'Quotation rejected successfully.');

        if (currentRequestQuotesContext.requestId) {
            await openJobQuotesModal(currentRequestQuotesContext.requestId, currentRequestQuotesContext.requestType);
        }
        await loadQuotesReceived();
        closeQuoteDetailsModal();
    }

    function switchMainView(view) {
        const showJobs = view === 'jobs';
        jobsPostedBtn.classList.toggle('active', showJobs);
        quotesReceivedBtn.classList.toggle('active', !showJobs);

        if (jobsFilterTabs) jobsFilterTabs.style.display = showJobs ? 'flex' : 'none';
        if (jobsContainerEl) jobsContainerEl.style.display = showJobs ? '' : 'none';
        if (quotesReceivedSection) quotesReceivedSection.style.display = showJobs ? 'none' : 'block';

        try {
            sessionStorage.setItem(JOB_HISTORY_VIEW_KEY, showJobs ? 'jobs' : 'quotes');
        } catch (e) {
            // Ignore storage failures.
        }

        const url = new URL(window.location.href);
        if (showJobs) {
            url.searchParams.delete('view');
        } else {
            url.searchParams.set('view', 'quotes');
            loadQuotesReceived();
        }
        window.history.replaceState({}, '', url.toString());
    }

    function renderDirectCategoryOptions(categories, selectedCategoryId) {
        if (!directJobCategory || !directJobCategoryDisplay || !directJobCategoryList) return;

        const valid = Array.isArray(categories) ? categories : [];
        if (!valid.length) {
            directJobCategory.value = String(selectedCategoryId || '');
            directJobCategoryDisplay.value = '';
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
            return;
        }

        const selected = valid.find((item) => Number(item.category_id) === Number(selectedCategoryId)) || valid[0];
        directJobCategory.value = String(selected.category_id);
        directJobCategoryDisplay.value = String(selected.name || '');

        if (valid.length <= 1) {
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
            return;
        }

        directJobCategoryList.style.display = 'grid';
        directJobCategoryList.innerHTML = valid.map((item) => {
            const checked = Number(item.category_id) === Number(selected.category_id) ? 'checked' : '';
            return `
                <label class="direct-job-category-option">
                    <input type="radio" name="directJobCategoryChoice" value="${Number(item.category_id)}" ${checked}>
                    <span>${escapeHtml(item.name || '')}</span>
                </label>
            `;
        }).join('');

        const radios = directJobCategoryList.querySelectorAll('input[name="directJobCategoryChoice"]');
        radios.forEach((radio) => {
            radio.addEventListener('change', function() {
                if (!this.checked) return;
                const selectedItem = valid.find((item) => Number(item.category_id) === Number(this.value));
                if (!selectedItem) return;
                directJobCategory.value = String(selectedItem.category_id);
                directJobCategoryDisplay.value = String(selectedItem.name || '');
            });
        });
    }

    async function openDirectEditModal(requestId) {
        const numericId = Number(requestId);
        if (!Number.isFinite(numericId) || numericId <= 0 || !directJobRequestModal || !directJobRequestForm) return;

        try {
            const directResult = await fetchJson(`${DIRECT_JOB_API}?action=get&request_id=${encodeURIComponent(String(numericId))}`);
            const directRequest = directResult.data || directResult;

            const providerId = Number(directRequest.provider_id || 0);
            const providerType = normalizeProviderType(directRequest.provider_type || 'individual');
            const categoriesResult = await fetchJson(`${DIRECT_JOB_API}?action=provider-categories&provider_id=${encodeURIComponent(String(providerId))}&provider_type=${encodeURIComponent(providerType)}`);
            const providerCategories = categoriesResult.data || [];

            directEditRequestId = numericId;
            directJobRequestForm.reset();

            directJobProviderId.value = String(providerId);
            directJobProviderType.value = providerType;
            directJobRequestProviderLabel.textContent = providerType === 'company' ? 'this company' : 'this repairer';
            document.getElementById('directJobTitle').value = String(directRequest.title || '');
            document.getElementById('directJobDescription').value = String(directRequest.description || '');
            directJobDistrict.value = String(directRequest.district || '');
            directJobAddress.value = String(directRequest.address || '');
            directJobFinishDate.value = String(directRequest.finish_date || '');

            renderDirectCategoryOptions(providerCategories, Number(directRequest.category_id || 0));

            if (directJobPhotoPreview) {
                directJobPhotoPreview.textContent = directRequest.photos ? `Current: ${String(directRequest.photos).split('/').pop()}` : '';
            }

            if (directJobRequestError) {
                directJobRequestError.style.display = 'none';
                directJobRequestError.textContent = '';
            }
            if (directJobRequestSuccess) {
                directJobRequestSuccess.style.display = 'none';
                directJobRequestSuccess.textContent = '';
            }
            if (directJobRequestSubmitBtn) {
                directJobRequestSubmitBtn.textContent = 'Update Request';
                directJobRequestSubmitBtn.disabled = false;
            }

            directJobRequestModal.classList.add('show');
            directJobRequestModal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        } catch (error) {
            alert(error.message || 'Failed to load direct request for editing.');
        }
    }

    async function deleteDirectRequest(requestId) {
        const numericId = Number(requestId);
        if (!Number.isFinite(numericId) || numericId <= 0) return;
        if (!confirm('Are you sure you want to delete this direct job request?')) return;

        try {
            const formData = new FormData();
            formData.set('request_id', String(numericId));
            await fetchJson(`${DIRECT_JOB_API}?action=delete`, {
                method: 'POST',
                body: formData
            });
            window.location.reload();
        } catch (error) {
            alert(error.message || 'Failed to delete direct request.');
        }
    }

    function closeDirectJobRequestModal() {
        if (!directJobRequestModal || !directJobRequestForm) return;
        directJobRequestModal.classList.remove('show');
        directJobRequestModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        directJobRequestForm.reset();
        directEditRequestId = null;
        if (directJobCategoryList) {
            directJobCategoryList.innerHTML = '';
            directJobCategoryList.style.display = 'none';
        }
        if (directJobPhotoPreview) {
            directJobPhotoPreview.textContent = '';
        }
        if (directJobRequestSubmitBtn) {
            directJobRequestSubmitBtn.textContent = 'Request';
            directJobRequestSubmitBtn.disabled = false;
        }
        if (directJobRequestError) {
            directJobRequestError.style.display = 'none';
            directJobRequestError.textContent = '';
        }
        if (directJobRequestSuccess) {
            directJobRequestSuccess.style.display = 'none';
            directJobRequestSuccess.textContent = '';
        }
    }

    async function submitDirectJobEdit(event) {
        event.preventDefault();
        if (!directEditRequestId) return;

        const formData = new FormData(directJobRequestForm);
        formData.set('request_id', String(directEditRequestId));
        formData.set('provider_id', String(directJobProviderId.value || ''));
        formData.set('provider_type', String(directJobProviderType.value || 'individual'));
        formData.set('category_id', String(directJobCategory.value || ''));

        if (directJobRequestSubmitBtn) {
            directJobRequestSubmitBtn.disabled = true;
            directJobRequestSubmitBtn.textContent = 'Updating...';
        }

        try {
            const result = await fetchJson(`${DIRECT_JOB_API}?action=update`, {
                method: 'POST',
                body: formData
            });

            if (directJobRequestSuccess) {
                directJobRequestSuccess.style.display = 'block';
                directJobRequestSuccess.textContent = result.message || 'Direct request updated successfully.';
            }
            setTimeout(() => window.location.reload(), 400);
        } catch (error) {
            if (directJobRequestError) {
                directJobRequestError.style.display = 'block';
                directJobRequestError.textContent = error.message || 'Failed to update direct request.';
            }
            if (directJobRequestSubmitBtn) {
                directJobRequestSubmitBtn.disabled = false;
                directJobRequestSubmitBtn.textContent = 'Update Request';
            }
        }
    }

    function openEditModal(requestId) {
        const job = jobs.find((item) => Number(item.request_id) === Number(requestId));
        if (!job) return;

        document.getElementById('edit_request_id').value = job.request_id;
        document.getElementById('edit_title').value = job.title || '';
        document.getElementById('edit_category_id').value = job.category_id;
        document.getElementById('edit_description').value = job.description || '';
        document.getElementById('edit_district').value = job.district || '';
        document.getElementById('edit_address').value = job.address || '';
        document.getElementById('edit_urgency').value = job.urgency || 'medium';
        document.getElementById('edit_finish_date').value = job.finish_date || '';

        const providerType = String(job.service_provider_type || '');
        document.getElementById('edit_provider_individual').checked = providerType.includes('individual');
        document.getElementById('edit_provider_company').checked = providerType.includes('company');
        document.getElementById('edit-file-name-display').innerHTML = '';
        document.getElementById('editModal').classList.add('show');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('show');
    }

    window.openEditModal = openEditModal;
    window.closeEditModal = closeEditModal;
    window.openJobQuotesModal = openJobQuotesModal;
    window.closeJobQuotesModal = closeJobQuotesModal;
    window.openQuoteDetails = openQuoteDetails;
    window.closeQuoteDetailsModal = closeQuoteDetailsModal;
    window.openDirectEditModal = openDirectEditModal;
    window.deleteDirectRequest = deleteDirectRequest;

    document.addEventListener('DOMContentLoaded', function() {
        const finishDateInput = document.getElementById('edit_finish_date');
        if (finishDateInput) {
            finishDateInput.setAttribute('min', new Date().toISOString().split('T')[0]);
        }

        document.querySelectorAll('.filter-tab').forEach((tab) => {
            tab.addEventListener('click', function() {
                const status = this.dataset.status;
                document.querySelectorAll('.filter-tab').forEach((t) => t.classList.remove('active'));
                this.classList.add('active');
                document.querySelectorAll('.job-card').forEach((card) => {
                    card.style.display = status === 'all' || card.dataset.status === status ? 'block' : 'none';
                });
            });
        });

        const providerCheckboxes = document.querySelectorAll('#editForm input[name="provider_type[]"]');
        const providerError = document.getElementById('edit-provider-error');
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(event) {
                const checked = Array.from(providerCheckboxes).some((checkbox) => checkbox.checked);
                if (!checked) {
                    event.preventDefault();
                    providerError.textContent = 'Please select at least one service provider type';
                } else {
                    providerError.textContent = '';
                }
            });
        }

        if (jobsPostedBtn && quotesReceivedBtn) {
            jobsPostedBtn.addEventListener('click', function() { switchMainView('jobs'); });
            quotesReceivedBtn.addEventListener('click', function() { switchMainView('quotes'); });
        }

        if (quotesFilterTabs && quotesFilterTabs.length) {
            quotesFilterTabs.forEach((tab) => {
                tab.addEventListener('click', function() {
                    const next = this.dataset.quoteStatus;
                    if (!next || next === currentQuoteStatusFilter) return;
                    currentQuoteStatusFilter = next;
                    quotesFilterTabs.forEach((item) => item.classList.remove('active'));
                    this.classList.add('active');
                    if (quotesReceivedSection && quotesReceivedSection.style.display !== 'none') {
                        loadQuotesReceived();
                    }
                });
            });
        }

        if (jobQuotesModal) {
            jobQuotesModal.addEventListener('click', function(event) {
                if (event.target === jobQuotesModal) {
                    closeJobQuotesModal();
                }
            });
        }

        if (quoteDetailsModal) {
            quoteDetailsModal.addEventListener('click', function(event) {
                if (event.target === quoteDetailsModal) {
                    closeQuoteDetailsModal();
                }
            });
        }

        document.getElementById('editModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeEditModal();
            }
        });

        if (directJobRequestCloseBtn) {
            directJobRequestCloseBtn.addEventListener('click', closeDirectJobRequestModal);
        }
        if (directJobRequestCancelBtn) {
            directJobRequestCancelBtn.addEventListener('click', closeDirectJobRequestModal);
        }
        if (directJobRequestModal) {
            directJobRequestModal.addEventListener('click', function(event) {
                if (event.target === directJobRequestModal) {
                    closeDirectJobRequestModal();
                }
            });
        }
        if (directJobPhotos && directJobPhotoPreview) {
            directJobPhotos.addEventListener('change', function() {
                const file = directJobPhotos.files && directJobPhotos.files[0];
                if (file) {
                    directJobPhotoPreview.textContent = `Selected: ${file.name}`;
                }
            });
        }
        if (directJobRequestForm) {
            directJobRequestForm.addEventListener('submit', submitDirectJobEdit);
        }

        if (quoteAcceptBtn) {
            quoteAcceptBtn.addEventListener('click', function() {
                if (!currentlyOpenedQuote) return;
                handleQuoteAction('accepted', currentlyOpenedQuote.source, currentlyOpenedQuote.quote_id, currentlyOpenedQuote.request_type);
            });
        }
        if (quoteRejectBtn) {
            quoteRejectBtn.addEventListener('click', function() {
                if (!currentlyOpenedQuote) return;
                handleQuoteAction('rejected', currentlyOpenedQuote.source, currentlyOpenedQuote.quote_id, currentlyOpenedQuote.request_type);
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
        switchMainView(initialView === 'quotes' ? 'quotes' : 'jobs');

        setTimeout(function() {
            document.querySelectorAll('.alert').forEach((alertElement) => {
                alertElement.style.opacity = '0';
                setTimeout(() => alertElement.remove(), 300);
            });
        }, 5000);
    });
    </script>

    <script src="/2nd-Year-Group-Project/FixLanka/assets/javascript/user/job-history.js"></script>
</body>
</html>